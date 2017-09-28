<?php

/* Location: ./CodeIgniter/application/controllers/printable_loan.php */

class Printable_loan extends Asi_Controller 
{
	var $row_height = .18; 	
	var $data;
	
	function Printable_loan() 
	{
		parent::Asi_Controller();
		$this->load->helper('url');
		$this->load->model('parameter_model');
		$this->load->model('loancodeheader_model');
		$this->load->model('member_model');
		$this->load->model('tloan_model');
		$this->load->library('constants');
		/* #####NRBTEMP $this->load->library('asi_model'); */
		$this->load->library('asi_pdf_ext');
		$this->load->library('asi_excel_ext');
		$this->load->helper('date');	
	}
	
	function index() 
	{			
		$this->loan_no = $_REQUEST['loan_no'];
		$result = $this->setTableData();
		
		if ($result==true)
			echo("{'success':false,'msg':'No records found.','error_code':'19'}");	
		else
			$this->printPDF();		
	}
	
	/**
	 * @desc Displays the report in PDF File
	 */
	function printPDF()
	{	
		$this->list = $this->getData($this->loan_info['list']);
		
		$this->objPdf = new Asi_pdf_ext();
		$this->objPdf->init("portrait", .2);	
		
		foreach($this->list AS $this->data) 
		{
			$this->objPdf->AliasNbPages();
			$this->objPdf->AddPage();
		
			$this->writeHeader($this->data);
			
			$this->objPdf->SetFont('Arial','',10);
			$this->objPdf->Cell(30*$this->row_height,$this->row_height,"LOC: ".$this->data['loc'],"","","L",false);
			$this->objPdf->Cell(10*$this->row_height,$this->row_height,"DATE: ".$this->data['loan_date'],"","","L",false);	
			$this->objPdf->Ln(.15);
			$this->objPdf->Cell(10*$this->row_height,$this->row_height,"PAYROLL NO. ".$this->data['payroll_no'],"","","L",false);
			$this->objPdf->Cell(30*$this->row_height,$this->row_height,$this->data['employee_name'],"","","L",false);	
			$this->objPdf->Ln(.3);
			$this->objPdf->Cell(20*$this->row_height,$this->row_height,"STATUS OF PRINCIPAL'S CAPITAL CONTRIBUTION","","","L",false);
			$this->objPdf->Cell(20*$this->row_height,$this->row_height,"P ".$this->data['ending_balance'],"","","R",false);	
				
			$this->writeContent($this->data['loan_code'], $this->data);
			$this->writeDetail($this->data);
			
			$this->objPdf->Ln(.5);	
		}
		
		$this->objPdf->Output("Loan_Printable"."_".date("YmdHis"));
	}
	
	/**
	 * @desc Sets the data
	 */
	function setTableData() 
	{	
		$this->accounting_period = substr(date("Ymd", strtotime($this->parameter_model->getParam('ACCPERIOD'))),0,-2);
		$this->loan_info = $this->tloan_model->getLoanPreviewData($this->loan_no, $this->accounting_period);
	}
	
	/**
	 * @desc Gets the data
	 */
	function getData($transaction_list) 
	{
		$list = array();

		foreach($transaction_list AS $this->data) 
		{
			$mem_data = $this->member_model->getMemberInfo($this->data['employee_id'],array('first_name','last_name','middle_name','company_code'));
			$employee_name = $mem_data['last_name'].", ".$mem_data['first_name']." ".$mem_data['middle_name'];
			$loan_info = $this->loancodeheader_model->getLoanInfo($this->data['loan_code'],array('loan_description','unearned_interest'));
			$loan_description = $loan_info['loan_description'];
			$other_fees = $this->data['initial_interest'];
			if($this->data['employee_interest_amortization'] == 0){
				$other_fees = $other_fees + $this->data['employee_interest_total'];
			}
			
			$list[] = array("ending_balance"=>number_format($this->data['ending_balance'],2,'.',',')
						   ,"loan_no"=>$this->data["loan_no"]
						   ,"loan_code"=>$this->data['loan_code']
						   ,"loan_date"=>date("m/d/Y", strtotime($this->data["loan_date"]))
						   ,"principal"=>number_format($this->data['principal'],2,'.',',')
						   ,"interest_rate" => number_format($this->data['effective_interest_rate'],2,'.',',')."%"
						   ,"initial_interest" => number_format($this->data['initial_interest'],2,'.',',')
						   ,"loan_proceeds" => number_format($this->data['loan_proceeds'],2,'.',',')
						   ,"restructure_amount" => number_format($this->data['restructure_amount'],2,'.',',')
						   ,"service_fee" => number_format($this->data['service_fee_amount'],2,'.',',')
						   ,"mri_fip" => number_format($this->data['mri_fip_amount'],2,'.',',')
						   ,"broker_fee" => number_format($this->data['broker_fee_amount'],2,'.',',')
						   ,"government_fee" => number_format($this->data['government_fee_amount'],2,'.',',')
						   ,"capcon_balance" => number_format($this->data['capcon_balance'],2,'.',',')
						   ,"one_third" => number_format($this->data['rloansurety'],2,'.',',')
						   ,"payroll_no" => $this->data['employee_id']
						   ,"EIR" => number_format($this->data['EFFECTIVE_ANNUAL_INTEREST_RATE'],2,'.',',')."%"
					       ,"MIR" => number_format($this->data['effective_monthly_interest_rate'],2,'.',',')."%"
					       ,"employee_name" => $employee_name
						   ,"loc" => $mem_data['company_code']
						   ,"check_no" => $this->data['check_no']
						   ,"conl_amount"=> number_format($this->data['service_fee_amount'] + $this->data['restructure_amount'] + $other_fees, 2, '.', ',')	
						   ,"mnil_amount"=> number_format($this->data['service_fee_amount'] + $other_fees, 2, '.', ',')	
						   ,"spel_amount"=> number_format($this->data['service_fee_amount'] + $other_fees, 2, '.', ',')	
						   ,"spcl_amount"=> number_format(($this->data['service_fee_amount'] + $other_fees), 2, '.', ',')	
						   ,"car2_amount"=> number_format($other_fees , 2, '.', ',')	
						   ,"loan_description" => $loan_description
						   ,"other_fee"=>number_format($this->data['other_fee_amount'], 2, '.', ',')
						   ,"down_payment" => number_format($this->data['down_payment_amount'], 2, '.', ',')
						   ,"unearned_interest"=>number_format($this->data['employee_interest_total'], 2, '.', ',')
						   ,"deducted_advance"=>$loan_info['unearned_interest']
						   ,"total_amount"=> number_format(($this->data['service_fee_amount'] + $other_fees + $this->data['mri_fip_amount'] 
														+ $this->data['broker_fee_amount'] + $this->data['government_fee_amount']
														- $this->data['other_fee_amount'] + $this->data['restructure_amount']
														+ $this->data['down_payment_amount']), 2, '.', ',')	);
		}
	
		return $list;
	}
	
	/**
	 * @desc writes the content of the printable
	 * */
	function writeContent($loan_code) 
	{
		if ($loan_code!='CARL' && $loan_code!='CAR2' && $loan_code!='HSPL' && $loan_code!='SPEL')
		{
			$this->objPdf->Ln(.3);
			$this->objPdf->Cell(20*$this->row_height,$this->row_height,"1/3 CAPITAL CONTRIBUTION REQUIRED","","","L",false);
			$this->objPdf->Cell(20*$this->row_height,$this->row_height,"P ".$this->data['capcon_balance'],"","","R",false);	
		}
		
		$this->objPdf->Ln(.3);
		$this->objPdf->Cell(40*$this->row_height,$this->row_height,"COMPUTATION OF NET AMOUNT BORROWED","","","L",false);
		
		if ($loan_code=='CONL--') //note: removed on 10/13/2010
		{
			$this->objPdf->Ln(.3);
			$this->objPdf->SetX(.5);
			$this->objPdf->Cell(15*$this->row_height,$this->row_height,"Amount","","","L",false);
			$this->objPdf->Cell(23.5*$this->row_height,$this->row_height,"P ".$this->data['principal'],"","","R",false);
			
			$this->objPdf->Ln(.2);
			$this->objPdf->SetX(.5);
			$this->objPdf->Cell(10*$this->row_height,$this->row_height,"Less: Effective Interest Rate","","","L",false);
			$this->objPdf->Cell(5*$this->row_height,$this->row_height,$this->data['interest_rate'],"","","R",false);
			$this->objPdf->Cell(8*$this->row_height,$this->row_height,$this->data['initial_interest'],"","","R",false);
			$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,"","","","R",false);
			
			$this->objPdf->Ln(.15);
			$this->objPdf->SetX(.85);
			$this->objPdf->Cell(13*$this->row_height,$this->row_height,"Service Fee","","","L",false);
			$this->objPdf->Cell(8*$this->row_height,$this->row_height,$this->data['service_fee'],"","","R",false);
			$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,"","","","R",false);
			
			$this->objPdf->Ln(.15);
			$this->objPdf->SetX(.85);
			$this->objPdf->Cell(13*$this->row_height,$this->row_height,"Amount Restructured #","","","L",false);
			$this->objPdf->Cell(8*$this->row_height,$this->row_height,$this->data['restructure_amount'],"","","R",false);
			$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,"P ".$this->data['conl_amount'],"","","R",false);
			
			$this->objPdf->Ln(.01);
			$this->objPdf->SetX(.85);
			$this->objPdf->Cell(13*$this->row_height,$this->row_height," ","","","L",false);
			$this->objPdf->Cell(8*$this->row_height,$this->row_height," ","","","R",false);
			$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,"______________________________","","","R",false);
			
			$this->objPdf->Ln(.3);
			$this->objPdf->SetX(.5);
			$this->objPdf->Cell(10*$this->row_height,$this->row_height,"Net Amount Received by Principal","","","L",false);
			$this->objPdf->Cell(5*$this->row_height,$this->row_height,"","","","R",false);
			$this->objPdf->Cell(8*$this->row_height,$this->row_height,"","","","R",false);
			$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,"P ".$this->data['loan_proceeds'],"","","R",false);
			
			$this->objPdf->Ln(.5);
			$this->objPdf->Cell(0,0,"I hereby certify that the foregoing computation is true and correct.","","","L",false);
			$this->objPdf->Ln(2);
		}
		else if ($loan_code=='CARL--') 
		{
			$this->objPdf->Ln(.3);
			$this->objPdf->SetX(.5);
			$this->objPdf->Cell(15*$this->row_height,$this->row_height,"Amount","","","L",false);
			$this->objPdf->Cell(23.5*$this->row_height,$this->row_height,"P ".$this->data['principal'],"","","R",false);
			
			//$this->objPdf->Ln(.2);
			//$this->objPdf->SetX(.5);
			//$this->objPdf->Cell(15*$this->row_height,$this->row_height,"Less : Down Payment","","","L",false);
			//$this->objPdf->Cell(23.5*$this->row_height,$this->row_height,$this->data['down_payment'],"","","R",false);
			$this->objPdf->Ln(.2);
			$this->objPdf->SetX(.4);
			$this->objPdf->Cell(0,0,"__________________________________________________________________________________________","","","L",false);
			
			$this->objPdf->Ln(.1);
			$this->objPdf->SetX(.5);
			$this->objPdf->Cell(15*$this->row_height,$this->row_height,"Net Amount Received by Principal","","","L",false);
			$this->objPdf->Cell(23.5*$this->row_height,$this->row_height,$this->data['loan_proceeds'],"","","R",false);
			
			$this->objPdf->Ln(.2);
			$this->objPdf->SetX(.5);
			$this->objPdf->Cell(15*$this->row_height,$this->row_height,"Effective Interest Rate","","","L",false);
			$this->objPdf->Cell(5*$this->row_height,$this->row_height,$this->data['interest_rate'],"","","L",false);
			
			/*$this->objPdf->Ln(.1);
			$this->objPdf->SetX(.5);
			$this->objPdf->Cell(10*$this->row_height,$this->row_height,"","","","L",false);
			$this->objPdf->Cell(5*$this->row_height,$this->row_height,$this->data['interest_rate'],"","","L",false);
			$this->objPdf->Cell(8*$this->row_height,$this->row_height,"","","","R",false);
			$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,"","","","R",false);*/
			
			$this->objPdf->Ln(.25);
			$this->objPdf->Cell(0,0,"I hereby certify that the foregoing computation is true and correct.","","","L",false);
			$this->objPdf->Ln(.5);
		}
		else if ($loan_code=='HSPL--') //note: removed on 10/13/2010
		{
			$this->objPdf->Ln(.3);
			$this->objPdf->SetX(.5);
			$this->objPdf->Cell(15*$this->row_height,$this->row_height,"Amount","","","L",false);
			$this->objPdf->Cell(23.5*$this->row_height,$this->row_height,"P ".$this->data['principal'],"","","R",false);
			
			$this->objPdf->Ln(.3);
			$this->objPdf->SetX(.5);
			$this->objPdf->Cell(10*$this->row_height,$this->row_height,"Less: Effective Interest Rate","","","L",false);
			$this->objPdf->Cell(5*$this->row_height,$this->row_height,$this->data['interest_rate'],"","","R",false);
			$this->objPdf->Cell(8*$this->row_height,$this->row_height,"P ".$this->data['initial_interest'],"","","R",false);
			$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,"","","","R",false);
			
			$this->objPdf->Ln(.15);
			$this->objPdf->SetX(.85);
			$this->objPdf->Cell(13*$this->row_height,$this->row_height,"Service Fee","","","L",false);
			$this->objPdf->Cell(8*$this->row_height,$this->row_height,"P ".$this->data['service_fee'],"","","R",false);
			$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,"","","","R",false);
				
			$this->objPdf->Ln(.15);
			$this->objPdf->SetX(.85);
			$this->objPdf->Cell(13*$this->row_height,$this->row_height,"MRI, FIP","","","L",false);
			$this->objPdf->Cell(8*$this->row_height,$this->row_height,"P ".$this->data['mri_fip'],"","","R",false);
			$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,"","","","R",false);
				
			$this->objPdf->Ln(.15);
			$this->objPdf->SetX(.85);
			$this->objPdf->Cell(13*$this->row_height,$this->row_height,"Broker's Fee","","","L",false);
			$this->objPdf->Cell(8*$this->row_height,$this->row_height,"P ".$this->data['broker_fee'],"","","R",false);
			$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,"","","","R",false);
				
			$this->objPdf->Ln(.15);
			$this->objPdf->SetX(.85);
			$this->objPdf->Cell(13*$this->row_height,$this->row_height,"Government Fees","","","L",false);
			$this->objPdf->Cell(8*$this->row_height,$this->row_height,"P ".$this->data['government_fee'],"","","R",false);
			$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,"","","","R",false);
				
			$this->objPdf->Ln(.15);
			$this->objPdf->SetX(.85);
			$this->objPdf->Cell(13*$this->row_height,$this->row_height,"From Capital Contribution","","","L",false);
			$this->objPdf->Cell(8*$this->row_height,$this->row_height,"P ".$this->data['other_fee'],"","","R",false);
			$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,"","","","R",false);
			
			$this->objPdf->Ln(.4);
			$this->objPdf->SetX(.5);
			$this->objPdf->Cell(15*$this->row_height,$this->row_height,"Net Amount Received by Principal","","","L",false);
			$this->objPdf->Cell(23.5*$this->row_height,$this->row_height,"P ".$this->data['loan_proceeds'],"","","R",false);
		
			$this->objPdf->Ln(.5);
			$this->objPdf->Cell(0,0,"I hereby certify that the foregoing computation is true and correct.","","","L",false);
			$this->objPdf->Ln(1.5);
		}
		else if ($loan_code=='MNIL--') //note: removed on 10/13/2010
		{
			$this->objPdf->Ln(.3);
			$this->objPdf->SetX(.5);
			$this->objPdf->Cell(15*$this->row_height,$this->row_height,"Amount","","","L",false);
			$this->objPdf->Cell(23.5*$this->row_height,$this->row_height,"P ".$this->data['principal'],"","","R",false);
			
			$this->objPdf->Ln(.3);
			$this->objPdf->SetX(.5);
			$this->objPdf->Cell(10*$this->row_height,$this->row_height,"Less:  Effective Interest Rate","","","L",false);
			$this->objPdf->Cell(5*$this->row_height,$this->row_height,$this->data['interest_rate'],"","","R",false);
			$this->objPdf->Cell(8*$this->row_height,$this->row_height,"P ".$this->data['unearned_interest'],"","","R",false);
			$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,"","","","R",false);
			
			$this->objPdf->Ln(.15);
			$this->objPdf->SetX(.85);
			$this->objPdf->Cell(13*$this->row_height,$this->row_height,"Service Fee","","","L",false);
			$this->objPdf->Cell(8*$this->row_height,$this->row_height,"P ".$this->data['service_fee'],"","","R",false);
			$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,$this->data['mnil_amount'],"","","R",false);
			
			$this->objPdf->Ln(.3);
			$this->objPdf->SetX(.5);
			$this->objPdf->Cell(15*$this->row_height,$this->row_height,"Net Amount Received by Principal","","","L",false);
			$this->objPdf->Cell(23.5*$this->row_height,$this->row_height,$this->data['loan_proceeds'],"","","R",false);
			
			$this->objPdf->Ln(.5);
		}
		else if ($loan_code=='SPEI--') //note: removed on 10/13/2010
		{
			$this->objPdf->Ln(.3);
			$this->objPdf->SetX(.5);
			$this->objPdf->Cell(15*$this->row_height,$this->row_height,"Amount","","","L",false);
			$this->objPdf->Cell(23.5*$this->row_height,$this->row_height,"P ".$this->data['principal'],"","","R",false);
			
			$this->objPdf->Ln(.2);
			$this->objPdf->SetX(.5);
			$this->objPdf->Cell(10*$this->row_height,$this->row_height,"Less:  Effective Interest Rate","","","L",false);
			$this->objPdf->Cell(5*$this->row_height,$this->row_height,$this->data['interest_rate'],"","","R",false);
			$this->objPdf->Cell(8*$this->row_height,$this->row_height,$this->data['unearned_interest'],"","","R",false);
			$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,"","","","R",false);
			
			$this->objPdf->Ln(.15);
			$this->objPdf->SetX(.85);
			$this->objPdf->Cell(13*$this->row_height,$this->row_height,"Service Fee","","","L",false);
			$this->objPdf->Cell(8*$this->row_height,$this->row_height,$this->data['service_fee'],"","","R",false);
			$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,$this->data['spel_amount'],"","","R",false);
			
			$this->objPdf->Ln(.01);
			$this->objPdf->SetX(.85);
			$this->objPdf->Cell(13*$this->row_height,$this->row_height," ","","","L",false);
			$this->objPdf->Cell(8*$this->row_height,$this->row_height," ","","","R",false);
			$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,"______________________________","","","R",false);
			
			$this->objPdf->Ln(.3);
			$this->objPdf->SetX(.5);
			$this->objPdf->Cell(10*$this->row_height,$this->row_height,"Net Amount Received by Principal","","","L",false);
			$this->objPdf->Cell(5*$this->row_height,$this->row_height,"","","","R",false);
			$this->objPdf->Cell(8*$this->row_height,$this->row_height,"","","","R",false);
			$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,"P ".$this->data['loan_proceeds'],"","","R",false);
			
			$this->objPdf->Ln(.5);
			$this->objPdf->Cell(0,0,"I hereby certify that the foregoing computation is true and correct.","","","L",false);
			$this->objPdf->Ln(.4);
		}
		else if ($loan_code=='SPCL--') //note: removed on 10/13/2010
		{
			$this->objPdf->Ln(.3);
			$this->objPdf->SetX(.5);
			$this->objPdf->Cell(15*$this->row_height,$this->row_height,"Amount","","","L",false);
			$this->objPdf->Cell(23.5*$this->row_height,$this->row_height,"P ".$this->data['principal'],"","","R",false);
			
			$this->objPdf->Ln(.2);
			$this->objPdf->SetX(.5);
			$this->objPdf->Cell(10*$this->row_height,$this->row_height,"Less:  Effective Interest Rate","","","L",false);
			$this->objPdf->Cell(5*$this->row_height,$this->row_height,$this->data['interest_rate'],"","","R",false);
			$this->objPdf->Cell(8*$this->row_height,$this->row_height,"P ".$this->data['initial_interest'],"","","R",false);
			$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,"","","","R",false);
			
			$this->objPdf->Ln(.15);
			$this->objPdf->SetX(.85);
			$this->objPdf->Cell(13*$this->row_height,$this->row_height,"Unearned Interest","","","L",false);
			$this->objPdf->Cell(8*$this->row_height,$this->row_height,"P ".$this->data['unearned_interest'],"","","R",false);
			$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,"","","R",false);
			
			$this->objPdf->Ln(.15);
			$this->objPdf->SetX(.85);
			$this->objPdf->Cell(13*$this->row_height,$this->row_height,"Service Fee","","","L",false);
			$this->objPdf->Cell(8*$this->row_height,$this->row_height,"P ".$this->data['service_fee'],"","","R",false);
			$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,$this->data['spcl_amount'],"","","R",false);
			
			$this->objPdf->Ln(.01);
			$this->objPdf->SetX(.85);
			$this->objPdf->Cell(13*$this->row_height,$this->row_height," ","","","L",false);
			$this->objPdf->Cell(8*$this->row_height,$this->row_height," ","","","R",false);
			$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,"______________________________","","","R",false);
			
			$this->objPdf->Ln(.3);
			$this->objPdf->SetX(.5);
			$this->objPdf->Cell(10*$this->row_height,$this->row_height,"Net Amount Received by Principal","","","L",false);
			$this->objPdf->Cell(5*$this->row_height,$this->row_height,"","","","R",false);
			$this->objPdf->Cell(8*$this->row_height,$this->row_height,"","","","R",false);
			$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,$this->data['loan_proceeds'],"","","R",false);
			
			$this->objPdf->Ln(.5);
			$this->objPdf->Cell(0,0,"I hereby certify that the foregoing computation is true and correct.","","","L",false);
			$this->objPdf->Ln(.4);
		}
		else if ($loan_code=='CAR2--') //note: removed on 10/13/2010
		{
			$this->objPdf->Ln(.3);
			$this->objPdf->SetX(.5);
			$this->objPdf->Cell(15*$this->row_height,$this->row_height,"Amount","","","L",false);
			$this->objPdf->Cell(23.5*$this->row_height,$this->row_height,"P ".$this->data['principal'],"","","R",false);
			
			/*$this->objPdf->Ln(.2);
			$this->objPdf->SetX(.5);
			$this->objPdf->Cell(15*$this->row_height,$this->row_height,"Less : Down Payment","","","L",false);
			$this->objPdf->Cell(23.5*$this->row_height,$this->row_height,$this->data['down_payment'],"","","R",false);
			*/
			$this->objPdf->Ln(.15);
			$this->objPdf->SetX(.85);
			$this->objPdf->Cell(5*$this->row_height,$this->row_height,"Initial Interest","","R",false);
			$this->objPdf->Cell(8*$this->row_height,$this->row_height,$this->data['interest_rate'],"","","R",false);
			$this->objPdf->Cell(23.5*$this->row_height,$this->row_height,$this->data['car2_amount'],"","","R",false);
			
			$this->objPdf->Ln(.2);
			$this->objPdf->SetX(.4);
			$this->objPdf->Cell(0,0,"__________________________________________________________________________________________","","","L",false);
			
			$this->objPdf->Ln(.1);
			$this->objPdf->SetX(.5);
			$this->objPdf->Cell(15*$this->row_height,$this->row_height,"Net Amount Received by Principal","","","L",false);
			$this->objPdf->Cell(23.5*$this->row_height,$this->row_height,$this->data['loan_proceeds'],"","","R",false);
			
			$this->objPdf->Ln(.6);
			$this->objPdf->Cell(0,0,"I hereby certify that the foregoing computation is true and correct.","","","L",false);
			$this->objPdf->Ln(.5);
		}
		else 
		{
			
			$this->objPdf->Ln(.3);
			$this->objPdf->SetX(.5);
			$this->objPdf->Ln(.3);
			$this->objPdf->Cell(15*$this->row_height,$this->row_height,"Amount","","","L",false);
			$this->objPdf->Cell(25*$this->row_height,$this->row_height,"P ".$this->data['principal'],"","","R",false);
			$this->objPdf->Ln(.2);
			$this->objPdf->SetX(.5);
			// jdj 07202017 - rename EIR label
			//$this->objPdf->Cell(10*$this->row_height,$this->row_height,"Less: Effective Interest Rate","","","L",false);
			$this->objPdf->Cell(10*$this->row_height,$this->row_height,"Less: Interest for remaining days of the month","","","L",false);
			$this->objPdf->Cell(10*$this->row_height,$this->row_height,$this->data['interest_rate'],"","","R",false);
			if($this->data['initial_interest'] > 0){
				$this->objPdf->Cell(5*$this->row_height,$this->row_height,$this->data['initial_interest'],"","","R",false);
			}
			else{
				$this->objPdf->Cell(5*$this->row_height,$this->row_height,"","","","R",false);
			}
			$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,"","","","R",false);
			
			if ($this->data['deducted_advance']=='Y'){
				$this->objPdf->Ln(.2);
				$this->objPdf->SetX(.85);
				$this->objPdf->Cell(13*$this->row_height,$this->row_height,"Interest Deducted in Advance","","","L",false);	
				$this->objPdf->Cell(10*$this->row_height,$this->row_height,$this->data['unearned_interest'],"","","R",false);
				$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,"","","","R",false);
			}	
			
			if ($this->data['service_fee'] > 0){
				$this->objPdf->Ln(.2);
				$this->objPdf->SetX(.85);
				$this->objPdf->Cell(13*$this->row_height,$this->row_height,"Service Fee","","","L",false);
				$this->objPdf->Cell(10*$this->row_height,$this->row_height,$this->data['service_fee'],"","","R",false);
				$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,"","","","R",false);
			}
			
			if ($this->data['mri_fip'] > 0){
				$this->objPdf->Ln(.2);
				$this->objPdf->SetX(.85);
				$this->objPdf->Cell(13*$this->row_height,$this->row_height,"MRI, FIP","","","L",false);
				$this->objPdf->Cell(10*$this->row_height,$this->row_height,$this->data['mri_fip'],"","","R",false);
				$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,"","","","R",false);
			}
			
			if ($this->data['broker_fee'] > 0){
				$this->objPdf->Ln(.2);
				$this->objPdf->SetX(.85);
				$this->objPdf->Cell(13*$this->row_height,$this->row_height,"Broker's Fee","","","L",false);
				$this->objPdf->Cell(10*$this->row_height,$this->row_height,$this->data['broker_fee'],"","","R",false);
				$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,"","","","R",false);
			}
			
			if ($this->data['government_fee'] > 0){
				$this->objPdf->Ln(.2);
				$this->objPdf->SetX(.85);
				$this->objPdf->Cell(13*$this->row_height,$this->row_height,"Government Fees","","","L",false);
				$this->objPdf->Cell(10*$this->row_height,$this->row_height,$this->data['government_fee'],"","","R",false);
				$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,"","","","R",false);
			}
			
			if ($this->data['other_fee'] > 0){
				$this->objPdf->Ln(.2);
				$this->objPdf->SetX(.85);
				$this->objPdf->Cell(13*$this->row_height,$this->row_height,"From Capital Contribution","","","L",false);
				$this->objPdf->Cell(10*$this->row_height,$this->row_height,$this->data['other_fee'],"","","R",false);
				$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,"","","","R",false);
			}
			
			if ($this->data['restructure_amount'] > 0){
				$this->objPdf->Ln(.2);
				$this->objPdf->SetX(.85);
				//jdj 07202017 rename restructure label
				//$this->objPdf->Cell(13*$this->row_height,$this->row_height,"Restructured Amount","","","L",false);
				$this->objPdf->Cell(13*$this->row_height,$this->row_height,"Previous Loan","","","L",false);
				$this->objPdf->Cell(10*$this->row_height,$this->row_height,$this->data['restructure_amount'],"","","R",false);
			}
			
			$this->objPdf->Ln(.2);
			$this->objPdf->SetX(.85);
			$this->objPdf->Cell(13*$this->row_height,$this->row_height," ","","","L",false);
			$this->objPdf->Cell(8*$this->row_height,$this->row_height," ","","","R",false);
			$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,"P ".$this->data['total_amount'],"","","R",false);
			
			/*$this->objPdf->Ln(.2);
			$this->objPdf->SetX(.85);
			$this->objPdf->Cell(13*$this->row_height,$this->row_height,"Down Payment Amount","","","L",false);
			$this->objPdf->Cell(8*$this->row_height,$this->row_height,$this->data['down_payment'],"","","R",false);
			$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,"P ".$this->data['total_amount'],"","","R",false);
			*/	
			$this->objPdf->Ln(.01);
			$this->objPdf->SetX(.85);
			$this->objPdf->Cell(13*$this->row_height,$this->row_height," ","","","L",false);
			$this->objPdf->Cell(8*$this->row_height,$this->row_height," ","","","R",false);
			$this->objPdf->Cell(15.5*$this->row_height,$this->row_height,"______________________________","","","R",false);
				
			$this->objPdf->Ln(.3);
			$this->objPdf->Cell(20*$this->row_height,$this->row_height,"Net Amount Received by Principal","","","L",false);
			$this->objPdf->Cell(20*$this->row_height,$this->row_height,"P ".$this->data['loan_proceeds'],"","","R",false);	
			
			//jdj 0720217 added EIR and MIR start
			$this->objPdf->Ln(.3);
			$this->objPdf->SetX(.5);
			$this->objPdf->Cell(15*$this->row_height,$this->row_height,"Effective Annual Interest Rate (EIR)","","","L",false);
			$this->objPdf->Cell(5*$this->row_height,$this->row_height,$this->data['EIR'],"","","R",false);
			$this->objPdf->Ln(.2);
			$this->objPdf->SetX(.5);
			$this->objPdf->Cell(16*$this->row_height,$this->row_height,"Effective Monthly Interest Rate (MIR)","","","L",false);
			$this->objPdf->Cell(4*$this->row_height,$this->row_height,$this->data['MIR'],"","","R",false);
			//end
			
			$this->objPdf->Ln(.7);
			$this->objPdf->Cell(0,0,"I hereby certify that the foregoing computation is true and correct.","","","L",false);
			$this->objPdf->Ln(.4);
		}
		
	}
	
	function writeHeader()
	{
		$this->objPdf->SetFont('Arial','',10);
			$this->objPdf->Cell(0,0,"Savings & Loan Association of P&G Phils. Employee, Inc. (PECA)","","","C",false);
			$this->objPdf->Ln(.15);
			// jdj 0721 change office address
			//$this->objPdf->Cell(0,0,"20th Floor, 6750 Ayala Avenue Office Tower, Ayala Avenue, Makati, Metro Manila","","","C",false);
			$this->objPdf->Cell(0,0,"10th Floor Net Park, 5th Avenue, Cresent Park West, Bonifacio Global City, Taguig 1634","","","C",false);
			$this->objPdf->Ln(.5);
			
			$this->objPdf->SetFont('Arial','B',10);
			if ($this->data['loan_code']=='CARL')
				$this->objPdf->Cell(0,0,"CAR LOAN PROMISSORY NOTE","","","C",false);
			else if ($this->data['loan_code']=='CAR2')
				$this->objPdf->Cell(0,0,"CAR LOAN PROMISSORY NOTE","","","C",false);
			else if ($this->data['loan_code']=='CONL')
				$this->objPdf->Cell(0,0,"CONSUMPTION LOAN PROMISSORY NOTE","","","C",false);
			else if ($this->data['loan_code']=='MNIL')
				$this->objPdf->Cell(0,0,"MINI LOAN PROMISSORY NOTE","","","C",false);
			else if ($this->data['loan_code']=='SPEI')
				$this->objPdf->Cell(0,0,"SPECIAL LOAN PROMISSORY NOTE","","","C",false);
			else if ($this->data['loan_code']=='SPCL')
				$this->objPdf->Cell(0,0,"SPOT CASH LOAN PROMISSORY NOTE","","","C",false);
			else if ($this->data['loan_code']=='HSPL')
				$this->objPdf->Cell(0,0,"HOUSING SUPPORT PROGRAM LOAN PROMISSORY NOTE","","","C",false);
			##### NRB EDIT START #####
			else if ($this->data['loan_code']=='LOY3')
				$this->objPdf->Cell(0,0,"LOYALTY LOAN PLUS 3 PROMISSORY NOTE","","","C",false);
			else if ($this->data['loan_code']=='CAR3')
				$this->objPdf->Cell(0,0,"CAR LOAN 3 PROMISSORY NOTE","","","C",false);
			else if ($this->data['loan_code']=='CON3')
				$this->objPdf->Cell(0,0,"CONSUMPTION LOAN 3 PROMISSORY NOTE","","","C",false);
			else if ($this->data['loan_code']=='MNI3')
				$this->objPdf->Cell(0,0,"MINI LOAN 3 PROMISSORY NOTE","","","C",false);
			else if ($this->data['loan_code']=='SPE3')
				$this->objPdf->Cell(0,0,"SPECIAL LOAN 3 PROMISSORY NOTE","","","C",false);
			else if ($this->data['loan_code']=='SPO3')
				$this->objPdf->Cell(0,0,"SPOT CASH LOAN 3 PROMISSORY NOTE","","","C",false);
			else if ($this->data['loan_code']=='HSP3')
				$this->objPdf->Cell(0,0,"HOUSING SUPPORT PROGRAM LOAN 3 PROMISSORY NOTE","","","C",false);
			else if ($this->data['loan_code']=='HS23')
				$this->objPdf->Cell(0,0,"HOUSING SUPPORT PROGRAM LOAN N-SUB 3 PROMISSORY NOTE","","","C",false);
			else if ($this->data['loan_code']=='HS33')
				$this->objPdf->Cell(0,0,"HOUSING SUPPORT PROGRAM LOAN 3 PROMISSORY NOTE","","","C",false);
			##### NRB EDIT END #####
			else 
				$this->objPdf->Cell(0,0,"LOAN PROMISSORY NOTE","","","C",false);

			$this->objPdf->Ln(.2);
	}
	
	function writeDetail()
	{
		if ($this->data['loan_code']!='HSPL') 
		{
			$this->objPdf->Cell(25*$this->row_height,$this->row_height,"","","","L",false);
			$this->objPdf->Cell(15*$this->row_height,$this->row_height,"____________________________________","","","L",false);
			$this->objPdf->Ln(.15);
			$this->objPdf->Cell(25*$this->row_height,$this->row_height,"","","","L",false);
			$this->objPdf->Cell(15*$this->row_height,$this->row_height,"Bookeeper","","","C",false);
			
			$this->objPdf->Ln(.3);
			$this->objPdf->Cell(15*$this->row_height,$this->row_height,$this->data['check_no'],"","","R",false);
			$this->objPdf->Cell(19*$this->row_height,$this->row_height,"P ".$this->data['loan_proceeds'],"","","R",false);
			$this->objPdf->Ln(.1);
			$this->objPdf->Cell(0,0,"Received Check No. ___________________  Date _________________ for __________________ only.","","","L",false);
			
			$this->objPdf->Ln(.3);
			if ($this->data['loan_code']!='CONL' && $this->data['loan_code']!='CARL' 
				&& $this->data['loan_code']!='CAR2' && $this->data['loan_code']!='HSPL'
				&& $this->data['loan_code']!='MNIL' && $this->data['loan_code']!='SPCL'
				&& $this->data['loan_code']!='SPEL') 
			{
				$this->objPdf->Cell(0,0,"Loan # ".$this->data['loan_no'],"","","L",false);
			}
			else {
				if ($this->data['loan_code']=='HSPL') 
					$this->objPdf->Cell(0,0,"HOUSING SUPPORT PROGRAM Loan # ".$this->data['loan_no'],"","","L",false);
				else if ($this->data['loan_code']=='CAR2') 
					$this->objPdf->Cell(0,0,"Car Loan # ".$this->data['loan_no'],"","","L",false);
				else 
					$this->objPdf->Cell(0,0,$this->data['loan_description']." # ".$this->data['loan_no'],"","","L",false);
			}
			
			$this->objPdf->Ln(.5);
			$this->objPdf->Cell(25*$this->row_height,$this->row_height,"","","","L",false);
			$this->objPdf->Cell(15*$this->row_height,$this->row_height,"____________________________________","","","L",false);
			$this->objPdf->Ln(.15);
			$this->objPdf->Cell(25*$this->row_height,$this->row_height,"","","","L",false);
			$this->objPdf->Cell(15*$this->row_height,$this->row_height,"Principal","","","C",false);
			
			$this->objPdf->Ln(.4);
			$this->objPdf->Cell(25*$this->row_height,$this->row_height,"","","","L",false);
			$this->objPdf->Cell(15*$this->row_height,$this->row_height,"____________________________________","","","L",false);
			$this->objPdf->Ln(.15);
			$this->objPdf->Cell(25*$this->row_height,$this->row_height,"","","","L",false);
			$this->objPdf->Cell(15*$this->row_height,$this->row_height,"Date","","","C",false);
		}
		else
		{			
			//for ($i=0; $i<3; $i++) {
				//if ($i>0) 
				//{
					//$this->objPdf->AddPage();
					//$this->writeHeader();
					
					//if ($i==1)
					//{
						$this->objPdf->Ln(.2);
						$this->objPdf->Cell(25*$this->row_height,$this->row_height,"","","","L",false);
						$this->objPdf->Cell(15*$this->row_height,$this->row_height,"____________________________________","","","L",false);
						$this->objPdf->Ln(.15);
						$this->objPdf->Cell(25*$this->row_height,$this->row_height,"","","","L",false);
						$this->objPdf->Cell(15*$this->row_height,$this->row_height,"Bookeeper","","","C",false);
						
						$this->objPdf->Ln(.3);
						$this->objPdf->Cell(15*$this->row_height,$this->row_height,$this->data['check_no'],"","","R",false);
						$this->objPdf->Cell(19*$this->row_height,$this->row_height,"P ".$this->data['loan_proceeds'],"","","R",false);
						$this->objPdf->Ln(.1);
						$this->objPdf->Cell(0,0,"Received Check No. ___________________  Date _________________ for __________________ only.","","","L",false);
						$this->objPdf->Ln(.3);
						$this->objPdf->Cell(0,0,"Loan # ".$this->data['loan_no'],"","","L",false);
						
						//$this->objPdf->Ln(.3);
						//$this->objPdf->Cell(0,0,"HOUSING SUPPORT PROGRAM Loan # ".$this->data['loan_no'],"","","L",false);
					//}
				//}
				$text1 = '       Consistent with the provisions of the plan, it is clear that the following penalties shall be applied in cases of violation
							of the policy (ex. failure to submit the requirements-TCT in your name, or used money for other purposes, ets.) :';
				$text2 = '       1. the amount of loan plus interest will become due and demandable on ______________ (____ day from date of
							release of check.) The amortization schedule will be used as basis in determining this amount.';
							
				$text3 = '       2. a 10-day grace period will be given to settle the amount from the date the demand letter is recieved by me.
							Failure to pay will automatically authorize the company to apply the maximum collectible from all my monthly
							salaries and all other payables (ex. encashment, allowance, bonus etc.) due me while still employed with the
							company. This could mean a ZERO take home pay for me until such time that I have fully settled my obligation.';
							
				$text4	= '      3. that submission of OR after demand letter has been executed to me will no longer be acceptable and that I will
							have to pay in cash my loan plus interest. I will have to submit a copy of the official receipt as proof of purchase.';
					
					$this->objPdf->SetY(7.5);
					$this->objPdf->SetFont('Arial','',10);
					$this->objPdf->Write(.15,$text1);
					$this->objPdf->Ln(.3);
					$this->objPdf->Write(.15,$text2);
					$this->objPdf->Ln(.3);
					$this->objPdf->Write(.15,$text3);
					$this->objPdf->Ln(.3);
					$this->objPdf->Write(.15,$text4);
					
					$this->objPdf->Ln(.4);
					$this->objPdf->Cell(25*$this->row_height,$this->row_height,"","","","L",false);
					$this->objPdf->Cell(15*$this->row_height,$this->row_height,"____________________________________","","","L",false);
					$this->objPdf->Ln(.15);
					$this->objPdf->Cell(25*$this->row_height,$this->row_height,"","","","L",false);
					$this->objPdf->Cell(15*$this->row_height,$this->row_height,"Principal","","","C",false);
					
					$this->objPdf->Ln(.4);
					$this->objPdf->Cell(25*$this->row_height,$this->row_height,"","","","L",false);
					$this->objPdf->Cell(15*$this->row_height,$this->row_height,"____________________________________","","","L",false);
					$this->objPdf->Ln(.15);
					$this->objPdf->Cell(25*$this->row_height,$this->row_height,"","","","L",false);
					$this->objPdf->Cell(15*$this->row_height,$this->row_height,"Date","","","C",false);
			//}
		}	
	}
	
}

?>