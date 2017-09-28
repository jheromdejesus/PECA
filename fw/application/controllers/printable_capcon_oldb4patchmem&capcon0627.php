<?php

/* Location: ./CodeIgniter/application/controllers/printable_capcon.php */

class Printable_capcon extends Asi_Controller 
{
	var $row_height = .18; 	
	
	function Printable_capcon() 
	{
		parent::Asi_Controller();
		$this->load->helper('url');
		$this->load->model('parameter_model');
		$this->load->model('member_model');
		$this->load->model('mloan_model');
		$this->load->model('tcaptranheader_model');
		$this->load->library('constants');
		$this->load->library('asi_model');
		$this->load->library('asi_pdf_ext');
		$this->load->library('asi_excel_ext');
		$this->load->helper('date');	
	}
	
	function index() 
	{			
		$this->transaction_code = $_REQUEST['transaction_code'];
		$this->transaction_date = date("Ymd",strtotime($_REQUEST['transaction_date']));
		$this->employee_id = $_REQUEST['employee_id'];
		$result = $this->setTableData();
		
		if ($this->transaction_code!='WDWL' && $this->transaction_code!='CLSE') 
			echo("{'success':false,'msg':'File cant be generated.','error_code':'152'}");
		else {
			if ($result==true)
				echo("{'success':false,'msg':'No records found.','error_code':'19'}");	
			else
				$this->printPDF();		
		} 
	}
	
	/**
	 * @desc Displays the report in PDF File
	 */
	function printPDF()
	{	
		$this->list = $this->getData($this->capcon_info['list']);
		
		$objPdf = new Asi_pdf_ext();
		$objPdf->init("portrait", .7);	
		
		foreach($this->list AS $data) 
		{
			$objPdf->AliasNbPages();
			$objPdf->AddPage();
			$objPdf->SetFont('Arial','',9);
			$objPdf->Cell(7*$this->row_height,$this->row_height,"LOC: ".$data['loc'],"","","L",false);
			$objPdf->Cell(18*$this->row_height,$this->row_height,"PAYROLL NO. ".$data['payroll_no'],"","","L",false);	//sample data
			$objPdf->Cell(10*$this->row_height,$this->row_height,"TRANSACTION NO. ".$data['transaction_no'],"","","L",false);	//sample data
			$objPdf->Ln(.5);
			$objPdf->SetFont('Arial','B',9);
			$objPdf->Cell(0,0,"Savings & Loan Association of P&G Phils. Employee, Inc. (PECA)","","","C",false);
			$objPdf->Ln(.15);
			$objPdf->Cell(0,0,"20th Floor, 6750 Ayala Avenue Office Tower, Ayala Avenue, Makati, Metro Manila","","","C",false);
			$objPdf->Ln(.4);
			$objPdf->SetFont('Arial','',9);
			$objPdf->SetX(6);
			$objPdf->Cell(18*$this->row_height,$this->row_height,"Date: ".date("m/d/Y", strtotime($data['transaction_date'])),"","","L",false);
			$objPdf->Ln(.4);
			$objPdf->Cell(0,0,"To THE TREASURER","","","L",false);
			$objPdf->Ln(.2);
			$objPdf->SetX(1);
			$objPdf->Cell(0,0,"I hereby apply for a withdrawal of  ".$data['transaction_amount']."  from my capital contribution.","","","L",false);
			$objPdf->Ln(.4);
			
			$objPdf->Cell(14*$this->row_height,$this->row_height," ","","","C",false);
			$objPdf->Cell(6*$this->row_height,$this->row_height," ","","","L",false);
			$objPdf->Cell(16*$this->row_height,$this->row_height,"_______________________________________","","","C",false);
			
			$objPdf->Ln(.15);
			$objPdf->Cell(14*$this->row_height,$this->row_height," ","","","C",false);
			$objPdf->Cell(6*$this->row_height,$this->row_height," ","","","L",false);
			$objPdf->Cell(16*$this->row_height,$this->row_height,$data['employee_name'],"","","C",false);
			
			$objPdf->Ln(.4);
			$objPdf->Cell(0,0,"STATUS OF DEPOSITOR'S ACCOUNT","","","L",false);
			$objPdf->Ln(.15);
			$objPdf->SetX(1);
			$objPdf->Cell(13*$this->row_height,$this->row_height,"Capital Contribution","","","L",false);
			$objPdf->Cell(22*$this->row_height,$this->row_height,"P ".$data['capital_contribution'],"","","R",false);
			$objPdf->Ln(.25);
			$objPdf->SetX(1);
			$objPdf->Cell(13*$this->row_height,$this->row_height,"Loan Balance / Non - Withdrawable","","","L",false);
			$objPdf->Cell(10*$this->row_height,$this->row_height,$this->balvalue,"","","L",false);
			$objPdf->Cell(12*$this->row_height,$this->row_height,"P ".number_format($this->non_withdrawable,2,'.',','),"","","R",false);
			$objPdf->Ln(.25);
			$objPdf->SetX(1);
			$objPdf->Cell(13*$this->row_height,$this->row_height,"Maximum Allowable Withdrawal","","","L",false);
			$objPdf->Cell(22*$this->row_height,$this->row_height,"P ".$data['max_allowable'],"","","R",false);
			$objPdf->Ln(.25);
			$objPdf->SetX(1);
			$objPdf->Cell(13*$this->row_height,$this->row_height,"Amount Withdrawn","","","L",false);
			$objPdf->Cell(22*$this->row_height,$this->row_height,$data['transaction_amount'],"","","R",false);
			$objPdf->Ln(.25);
			$objPdf->SetX(1);
			$objPdf->Cell(13*$this->row_height,$this->row_height,"Capital Contribution Balance","","","L",false);
			$objPdf->Cell(22*$this->row_height,$this->row_height,"P ".$data['capital_contribution_balance'],"","","R",false);
			
			$objPdf->Ln(.5);
			$objPdf->Cell(20*$this->row_height,$this->row_height,"Received Check No. ".$data['remarks'],"","","L",false);
			$objPdf->Cell(17*$this->row_height,$this->row_height,"I hereby certify that the foregoing","","","L",false);
			$objPdf->Ln(.15);
			$objPdf->Cell(20*$this->row_height,$this->row_height,"in the amount of ".$data['transaction_amount'],"","","L",false);
			$objPdf->Cell(17*$this->row_height,$this->row_height,"computation and statement of account","","","L",false);
			$objPdf->Ln(.15);
			$objPdf->Cell(20*$this->row_height,$this->row_height,"this _______ day of _________, _________","","","L",false);
			$objPdf->Cell(17*$this->row_height,$this->row_height,"is true and correct.","","","L",false);
			
			$objPdf->Ln(.6);
			$objPdf->Cell(14*$this->row_height,$this->row_height,"___________________________________","","","L",false);
			$objPdf->Cell(8*$this->row_height,$this->row_height," ","","","L",false);
			$objPdf->Cell(14*$this->row_height,$this->row_height,"___________________________________","","","L",false);
			
			$objPdf->Ln(.15);
			$objPdf->Cell(14*$this->row_height,$this->row_height,$data['employee_name'],"","","C",false);
			$objPdf->Cell(8*$this->row_height,$this->row_height," ","","","L",false);
			$objPdf->Cell(14*$this->row_height,$this->row_height,"Bookeeper","","","C",false);
			
			$objPdf->Ln(.3);
			$objPdf->Cell(14*$this->row_height,$this->row_height,"I hereby authorize ____________________","","","L",false);
			$objPdf->Cell(8*$this->row_height,$this->row_height," ","","","L",false);
			$objPdf->Cell(14*$this->row_height,$this->row_height,"Checked by : ________________________","","","L",false);
			
			$objPdf->Ln(.15);
			$objPdf->Cell(14*$this->row_height,$this->row_height,"______________ whose signature appears","","","L",false);
			$objPdf->Cell(8*$this->row_height,$this->row_height," ","","","L",false);
			$objPdf->Cell(14*$this->row_height,$this->row_height,"Auditor","","","C",false);
			
			$objPdf->Ln(.15);
			$objPdf->Cell(14*$this->row_height,$this->row_height,"below to make this withdrawal for me.","","","L",false);
			$objPdf->Cell(8*$this->row_height,$this->row_height," ","","","L",false);
			$objPdf->Cell(14*$this->row_height,$this->row_height," ","","","C",false);
			
			$objPdf->Ln(.2);
			$objPdf->Cell(14*$this->row_height,$this->row_height,"___________________________________","","","L",false);
			$objPdf->Cell(8*$this->row_height,$this->row_height," ","","","L",false);
			$objPdf->Cell(14*$this->row_height,$this->row_height,"Verified by : __________________________","","","L",false);
			
			$objPdf->Ln(.15);
			$objPdf->Cell(14*$this->row_height,$this->row_height,"Signature of Representative","","","C",false);
			$objPdf->Cell(8*$this->row_height,$this->row_height," ","","","L",false);
			$objPdf->Cell(14*$this->row_height,$this->row_height,"Treasurer","","","C",false);
			
			$objPdf->Ln(.5);
			$objPdf->Cell(14*$this->row_height,$this->row_height,"___________________________________","","","L",false);
			$objPdf->Cell(8*$this->row_height,$this->row_height," ","","","L",false);
			$objPdf->Cell(14*$this->row_height,$this->row_height,"Approved by : ________________________","","","L",false);
			
			$objPdf->Ln(.15);
			$objPdf->Cell(14*$this->row_height,$this->row_height,"Signature of Depositor","","","C",false);
			$objPdf->Cell(8*$this->row_height,$this->row_height," ","","","L",false);
			$objPdf->Cell(14*$this->row_height,$this->row_height,"President","","","C",false);
		}
		
		$objPdf->Output("Capcon_Printable"."_".date("YmdHis"));
	}
	
	/**
	 * @desc Sets the data
	 */
	function setTableData() 
	{	
		//$balance = $this->getBalance($this->employee_id);
		$ccminbal = $this->parameter_model->getParam('CCMINBAL');
		
		$result = $this->mloan_model->retrieveLoanSumNeedingOneThird($this->employee_id);
			if(isset($result['list'][0]['principal_balance'])){
				$balvalueRaw = $result['list'][0]['principal_balance'];
				if($balvalueRaw > 0){
					$this->balvalue = number_format($balvalueRaw,2,'.',',')." X .3333";
					$non_withdrawable = round($balvalueRaw/3, 2);
					if($non_withdrawable < $ccminbal){
						$this->non_withdrawable = $ccminbal;
					}
					else{
						$this->non_withdrawable = $non_withdrawable;
					}
				}
				else{
					$this->balvalue = "";
					$this->non_withdrawable = $ccminbal;
				}
			}
			else{
				$this->balvalue = "";
				$this->non_withdrawable = $ccminbal;
			}
		
		
		//commented to try another approach
		/*if ($balance['capcon_balance']< $ccminbal ) {
			$this->non_withdrawable = $ccminbal;
			$this->balvalue = "";
		}
		else {
			$this->non_withdrawable = $balance['capcon_balance'];
			$this->non_withdrawable = round($this->non_withdrawable, 2);
			$result = $this->mloan_model->retrieveLoanSumNeedingOneThird($this->employee_id);
			if(isset($result['list'][0]['principal_balance'])){
				$balvalueRaw = $result['list'][0]['principal_balance'];
				if($balvalueRaw > 0){
					$this->balvalue = number_format($balvalueRaw,2,'.',',')." X .3333";
				}
				else{
					$this->balvalue = "";
				}
			}
			else{
				$this->balvalue = "";
			}
		}*/
				
		$this->accounting_period = substr(date("Ymd", strtotime($this->parameter_model->getParam('ACCPERIOD'))),0,-2);
		$mem_data = $this->member_model->getMemberInfo($this->employee_id,array('first_name','last_name','middle_name','company_code'));
		$this->employee_name = $mem_data['last_name'].", ".$mem_data['first_name']." ".$mem_data['middle_name'];
		$this->loc = $mem_data['company_code'];
		
		$this->capcon_info = $this->tcaptranheader_model->getCapconPrintableInfo($this->transaction_date,$this->accounting_period,$this->employee_id, $this->transaction_code);
		if (count($this->capcon_info['list'])==0) 
			return true;
	}
	
	/**
	 * @desc Gets the data
	 */
	function getData($transaction_list) 
	{
		$list = array();
		
		foreach($transaction_list AS $data) 
		{
			$list[] = array("transaction_no"=>$data["transaction_no"]
						   ,"transaction_date"=>$data["transaction_date"]
						   ,"payroll_no"=>$data["employee_id"]
						   ,"employee_name" => $this->employee_name
						   ,"loc" => $this->loc
						   ,"remarks" => $data['remarks']
						   ,"max_allowable" => number_format(($data["capital_contribution"] - $this->non_withdrawable),2,'.',',')
						   ,"capital_contribution" => number_format($data["capital_contribution"],2,'.',',')
						   ,"capital_contribution_balance" => number_format(($data["capital_contribution"] - $data["transaction_amount"]),2,'.',',')
						   ,"transaction_amount" => "P ".number_format($data["transaction_amount"],2,'.',','));	
		}
		
		return $list;
	}
	
	/**
	 * @desc Gets the loan and capcon balance of an employee
	 * */
	function getBalance($employee_id)
	{
		$data = $this->mloan_model->get_list(array('employee_id' => $employee_id, 'status_flag'=>'2')
												,null
												,null
												,array('COALESCE(SUM(capital_contribution_balance),0) AS capcon_balance'
													  ,'COALESCE(SUM(principal_balance),0) AS loan_balance'));			
	
		return $data['list'][0];
	}
}

?>