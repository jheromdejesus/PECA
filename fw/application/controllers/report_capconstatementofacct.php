<?php

/*
*	modified by: kwinx
*	1. changed the years of employement query to CEILING
*	2. changed report ID
*	3. set middle initials for employee
*	4. set company to company code only
*/
	
class Report_capconstatementofacct extends Asi_controller {	

	var $file_type;		//1 - excel ; 2 - pdf
	var $report_title;
	var $report_type;
	var $report_start_date;
	var $report_end_date;
	var $report_id;
	
	var $employee_id;
	var $start_date;
	var $end_date;
	var $company_code;
	var $start_trans_date;
	var $end_trans_date;
	
	var $capcon_list;	
	var $loan_list;		
	var $comade_list;	
	var $employee_list;
	
	var $image = "images/printable_loan_watermark.gif";
	var $width = 200;
	var $height = 200;
	var $is_soa = false;	
	var $font_size = 9;
	
function testPDF(){
		$pdf = new FPDF('P', 'pt', array(1240, 1754));

		$pdf->AddPage();

		$pdf->Image($this->image, 0, 0, 400, 400);

		$pdf->SetFont('Arial', 'B', 23);

		$name = "sdfsdf  adfasdfasdfasf";

		$pdf->Text(500, 457, $name);

		$pdf->Text(500, 1268, date('jS F Y'), 'asfasfasfd');

		$pdf->Output('mypdf.pdf');  
	}
	
	function Report_capconstatementofacct()
	{
		parent::Asi_controller();
		$this->load->model('company_model');
		$this->load->model('member_model');
		$this->load->model('capitalcontribution_model');
		$this->load->model('mtransaction_model');
		$this->load->model('loan_model');
		$this->load->model('mloan_model');
		$this->load->library('asi_pdf_ext');
		$this->load->library('asi_excel_ext');	
	}

	function memberSOA(){
		/*$_REQUEST['employee_id'] = '01518821';
		$_REQUEST['file_type'] = '2';
		$_REQUEST['report_type'] = '1';*/
		
		if(isset($_REQUEST['start_date']) && isset($_REQUEST['end_date'])){
			$_REQUEST['start_date'] = date('m/d/Y', strtotime($_REQUEST['start_date']));
			$_REQUEST['end_date'] = date('m/d/Y', strtotime($_REQUEST['end_date']));
		}
		else{
			$_REQUEST['start_date'] = date('m/d/Y', strtotime('first day'));
			$_REQUEST['end_date'] = date('m/d/Y', strtotime('last day'));
		}
		
		$this->is_soa = true;
		$this->index();
	}
	
	function index() 
	{
		  /*$_REQUEST = array( 'file_type' => '2'
							,'start_date' => '01/01/2010' 
							,'end_date' => '06/30/2010' 
							,'start_trans_date' => '01/01/2002' 
							,'end_trans_date' => '01/04/2010' 
							,'company_code' => '403'
							,'employee_id' => '01518821'
							,'report_type' => '1'
							);    */
			/* $_REQUEST['start_date'] = '05/01/2010';
			$_REQUEST['end_date'] = '06/01/2010';
			$_REQUEST['file_type'] = '2';
			$_REQUEST['report_type'] = '2';
			$_REQUEST['company_code'] = '910'; */  
			//$_REQUEST['employee_id'] = '01517073';  
		
			$this->report_title = "Statement of Account";
			$this->report_start_date = date("F j, Y",strtotime($_REQUEST['start_date']));
			$this->report_end_date = date("F j, Y",strtotime($_REQUEST['end_date']));
			$this->report_id = "C0001";
			
			$this->file_type = $_REQUEST['file_type'];	
			$this->start_date = date("Ymd", strtotime($_REQUEST['start_date']));	
			$this->end_date = date("Ymd", strtotime($_REQUEST['end_date']));
			$this->report_type = $_REQUEST['report_type'];
			
			if( $this->report_type == '1'){
				$this->employee_id = $_REQUEST['employee_id'];
			}
			else if( $this->report_type == '2'){
				$this->company_code = $_REQUEST['company_code'];
			}
			else if( $this->report_type == '3'){
				$this->start_trans_date = date("Ymd", strtotime($_REQUEST['start_trans_date']));	
				$this->end_trans_date = date("Ymd", strtotime($_REQUEST['end_trans_date']));	 
			}
			
			$result = $this->setTableData();	

			/* if(count($this->employee_list)>5)
				$this->employee_list = array_slice($this->employee_list, 0,20);   */
			 
			if ($result == true) {
				echo("{'success':false,'msg':'No records found.','error_code':'19'}");	
			}
			else {
				if ($this->file_type=='1') $this->printExcel();
				else $this->printPDF(); 
			}	 
	}
	
	function setTableData()
	{	
		$list = array();
		if ($this->report_type=='1') {
			$list = $this->retrieveEmployeeInfo();
			//$list = $list['list'];
			$this->employee_list = $list;
		}
		else if ($this->report_type == '2') {
			$list = $this->retrieveMembersForCompany();
			//foreach($list as $value)
			$this->employee_list = $list;
		}
		else {
			$list = $this->retrieveMembersByTransactionDate();
			//foreach($list as $value)
			$this->employee_list = $list;//$value['employee_id'];
		}
		
		if (count($list)==0) {
				return true;
		}
	
	}
	
	/**
	 * @desc To retrieve the employee details based on $employee_id.
	 */
	function retrieveEmployeeInfo()
	{	
		/* $this->member_model->setValue('employee_id', $this->employee_id);
		$this->member_model->setValue('status_flag', '1'); */
		$data = $this->member_model->get(array("employee_id" => $this->employee_id), array("
				employee_id 
				,last_name
				,first_name
				,middle_name
				,FLOOR(DATEDIFF(CURDATE(), hire_date)/365.25) AS member_date
				,company_code"
		));
		/* $company = $this->company_model->get(array("company_code"=>$data['list'][0]['company']),
			array('company_code','company_name')); */
		//$data['list'][0]['company'] .= ' - '.$company['list'][0]['company_name'];
		return $data['list'];
	}
	
	/**
	 * @desc To retrieve employee's beginning balance based on the specified range period.
	 */
	function retrieveBeginningBalance()
	{	
		$param['employee_id'] = $this->employee_id;
		$data = $this->capitalcontribution_model->getBeginningBalanceBetween(
				$param
				,0
				,1
				,array("
					employee_id 
					,accounting_period
					,beginning_balance AS balance")
				,'accounting_period DESC'
				,$this->start_date
				,$this->end_date
				,$this->employee_id
		);
		return $data['list'];
	}
	/**
	 * @desc To retrieve employee's capital contribution transactions
	 */
	function retrieveCapConTransactions()
	{
		$data = $this->mtransaction_model->retrieveCapConTransactions(
				$this->employee_id
				,$this->start_date
				,$this->end_date
		);
		
		$beginning_balance = $this->retrieveBeginningBalance();
		//[START] google doc #15 : Added by ASI466 on 20120130
		$min_date = $this->capitalcontribution_model->getMinTransactionDate($this->start_date,$this->end_date,$this->employee_id);
		//[END] google doc #15: Added by ASI466 on 20120130
		
		
		//[START] google doc #15 : Modified by ASI466 on 20120130
		$balance_before_start = $this->capitalcontribution_model->retrieveBalBeforeStart($this->employee_id,$min_date);
		//[END] google doc #15 : Modified by ASI466 on 20120130
		
		$list = array();
		if(count($beginning_balance)>0){
			$list[] = array("date" => date("m/d/Y",strtotime($beginning_balance[0]['accounting_period']))	
						 ,"transaction_code" => "Beginning Balance"
						 ,"addition" => ""			
						 ,"deduction" => ""			
						 ,"balance" => number_format($beginning_balance[0]['balance'],2,'.',',')			 
						 );
			$balance = $beginning_balance[0]['balance']	+ $balance_before_start;
		}
		foreach ($data['list'] as $value){
			$balance += $value['addition'];
			$balance -= $value['deduction'];
			if($value['transaction_code'] == 'DIVD')
				$div = ' '.number_format($value['dividend_rate'],2,'.',',').'%';
			else
				$div = '';
			$list[] = array("date" => date("m/d/Y",strtotime($value['transaction_date']	))	
				 ,"transaction_code" => $value['transaction_code'].' - '.$value['transaction_description'].$div
				 ,"addition" => ($value['addition']=='0')?'':number_format($value['addition'],2,'.',',')		
				 ,"deduction" => ($value['deduction']=='0')?'':'('.number_format($value['deduction'],2,'.',',').')'			
				 ,"balance" => number_format($balance,2,'.',',')					 
				 );
	
		}
	
		return $list;
	}
	
	/**
	 * @desc To retrieve employee's beginning balance based on the specified range period.
	 */
	function retrieveLoanPaymentTransactions()
	{
		/*
		$param['tl.status_flag'] = '2';
		$param['tl.employee_id'] = $this->employee_id ;
		$param['tlp.status_flag'] = '2';
		$param['tlp.source <>'] = 'B';
		*/
		
		$param = "tl.status_flag = '2'" 
			. " AND tl.employee_id = '". $this->employee_id ."'"
			//. " AND tlp.source <> 'B'" //2-17-2011 removed by joseph to include loans w/ no payment
			. " AND tlp.status_flag = '2'"
			. " AND tt.reference IS NULL";
		$data = $this->Loan_model->retrieveLoanPaymentTransactions(
				$param
				,array(
				'tl.loan_no'
				, "DATE_FORMAT(tl.loan_date,'%m/%d/%Y') AS loan_date"
				, 'tl.term'
				, 'tl.interest_rate'
				, 'tl.principal'
				)
				,$this->start_date
				,$this->end_date
				//,'tl.loan_date'
				,'tlp.payment_date'
		);
		$list= array();
		foreach($data['list'] as $value){
				$list[$value['loan_no']] = array('loan_no' => " ".$value['loan_no']
								,'loan_date' => $value['loan_date']
								,'term' => " ".$value['term']
								,'interest_rate' => number_format($value['interest_rate'],2,'.',',')	
								,'principal' => number_format($value['principal'],2,'.',',')	
								,'count'=> 0
								,'loan_payments' => array()		 
								);
		}
		// $param['tl.status_flag'] = '2';
		// $param['tl.employee_id'] = $this->employee_id ;
		// $param['tlp.status_flag'] = '2';
		// $param['tlp.source <>'] = 'B';
		$param .= " AND tlp.source <> 'B'"; // 2-17-2011 added by joseph to reverse above and not include beginning loan payment of zero.
		$data = $this->loan_model->retrieveLoanPaymentTransactions(
				$param
				,array(
				'tl.loan_no'
				, "DATE_FORMAT(tlp.payment_date,'%m/%d/%Y') AS payment_date"
				, 'tlp.transaction_code'
				, 'tlp.amount'
				, 'tlp.balance'
				, 'tlp.interest_amount'
				)
				,$this->start_date
				,$this->end_date
				//,'tl.loan_date'
				,'tlp.payment_date'
		);
		
		foreach($data['list'] as $value){
			$list[$value['loan_no']]['loan_payments'][] = array("number" => ++$list[$value['loan_no']]['count']	
											 ,"date" => $value['payment_date']
											 ,"code" => $value['transaction_code']		
											 ,"amount" => number_format($value['amount'],2,'.',',')				
											 ,"balance" => number_format($value['balance'],2,'.',',')			
											 ,"interest" => number_format($value['interest_amount'],2,'.',','));	
				
		}
	
		return $list;
	}	
	
	/**
	 * @desc To retrieve employee's guaranteed loans.
	 */
	function retrieveGuaranteedLoans()
	{
		$param['tlg.guarantor_id'] = $this->employee_id ;
		$param['tlg.status_flag'] = '2';
		$param['tl.status_flag'] = '2';
		$data = $this->loan_model->retrieveGuaranteedLoans(
				$param
				,array(
				'tl.loan_no'
				, 'tl.employee_id AS borrowers_id'
				, 'mm.last_name'
				, 'mm.first_name'
				, 'mm.middle_name'
				, 'tl.loan_code'
				, "DATE_FORMAT(tl.loan_date,'%m/%d/%Y') AS loan_date"
				)
				,$this->start_date
				,$this->end_date
		);
		$list = array();
		foreach($data['list'] as $value){
			$list[] = array("loan_no" => " ".$value['loan_no']		
					 ,"borrower_id" => " ".$value['borrowers_id']	
					 ,"borrower_name" => $value['last_name'].', '.$value['first_name'].' '.$value['middle_name']	
					 ,"loan_code" => $value['loan_code']			
					 ,"loan_date" => $value['loan_date']					 
					 );
		}
		  
		return $list;
	}	
	
	/**
	 * @desc To retrieve employees belonging to the specified company code.
	 */
	function retrieveMembersForCompany()
	{
		/* $param['mm.company_code'] = $this->company_code ;
		$param['mm.member_status'] = 'A'; */
		/* $param['tcc.company_code'] = $this->company_code ;
		$param['tcc.member_status'] = 'A';
		$data = $this->capitalcontribution_model->retrieveMembersForCompany(
				$param
				,array('tcc.employee_id')
				,$this->start_date
				,$this->end_date
		); */
		$data = $this->capitalcontribution_model->retrieveMembersForCompany($this->company_code);
		/* echo $data['query'];
		exit();  */
		return $data['list'];
	}
	
	function retrieveMembersByTransactionDate()
	{
		 /* $this->start_date = '20100101';
		$this->end_date = '20100104'; 
		$this->start_trans_date = '20100101';
		$this->end_trans_date = '20100104';  
		
		$param['tt.status_flag'] = '3';
		$data = $this->capitalcontribution_model->retrieveMembersByTransactionDate(
				$param
				,array('tcc.employee_id')
				,$this->start_date
				,$this->end_date
				,$this->start_trans_date
				,$this->end_trans_date
				,'tcc.employee_id'	
		);
		echo $data['query']; */
		
		$data = $this->capitalcontribution_model->retrieveMembersByTransactionDate($this->start_date,$this->end_date,$this->start_trans_date,$this->end_trans_date);
		
		return $data['list'];	
	}
	
	/**
	 * @desc Prints the PDF file
	 */
	function printPDF()
	{
		$objPdf = new Asi_pdf_ext();
		$objPdf->init("portrait", .5);
		if($this->is_soa){
			$objPdf->has_watermark = true;
		}

		//$objPdf->ln = 0.1;
		
		foreach($this->employee_list as $employee_id)
		{
			$this->employee_id = $employee_id['employee_id'];
			$list = $employee_id;
			$objPdf->writeHeaderInfo($this->report_title, $this->report_start_date." to ".$this->report_end_date,  $this->report_id );
			$emp_id = $list['employee_id'];
			$comp = $list['company_code'];
			$name = $list['last_name'].', '.$list['first_name'].' '.substr($list['middle_name'],-(strlen($list['middle_name'])),1).".";
			$mem_date = $list['member_date'];
			
			$this->font_size = 8;
			$this->_setCellPDF($objPdf, 1, "");
			$this->_setCellPDF($objPdf, 5, "Employee ID:");
			$this->_setCellPDF($objPdf, 20, $emp_id);
			$this->_setCellPDF($objPdf, 7, "Company:");
			$this->_setCellPDF($objPdf, 13, $comp);
			$objPdf->Ln(.15);
			$this->_setCellPDF($objPdf, 1, "");
			$this->_setCellPDF($objPdf, 5, "Name:");
			$this->_setCellPDF($objPdf, 20, $name);
			$this->_setCellPDF($objPdf, 7, "Years of Employment:");
			$this->_setCellPDF($objPdf, 13, $mem_date);
			
			$list = $this->retrieveCapConTransactions();
			if(count($list)>0){
				$objPdf->Ln();
				$objPdf->writeSubheaderTitle('CAPITAL CONTRIBUTION',0.5);	
				//$objPdf->Ln(.15);
				$objPdf->writeSubheader(
										array("Date" => 5		//no of cells in actual excel
											 ,"Transaction Code" => 12
											 ,"Addition" => 8			
											 ,"Deduction" => 8			
											 ,"Balance" => 8							 
											 )
										,array("Date" => "C"		
											 ,"Transaction Code" => "L"
											 ,"Addition" => "R"			
											 ,"Deduction" => "R"			
											 ,"Balance" => "R"					 
											 )
										,0.5);	
				$objPdf->writeTableData(
								$list
								,array("date" => 5		//no of cells in actual excel
									 ,"transaction_code" => 12
									 ,"addition" => 8			
									 ,"deduction" => 8			
									 ,"balance" => 8							 
								)
								,array("date" => "C"		
									 ,"transaction_code" => "L"
									 ,"addition" => "R"			
									 ,"deduction" => "R"			
									 ,"balance" => "R"					 
									 ),0.5, 0.15, 58, 8);
				$objPdf->Ln(.15);
			}
			else{
				$objPdf->Ln();
				$objPdf->writeSubheaderTitle('CAPITAL CONTRIBUTION - No Records',0.5);	
				//$objPdf->Ln();
			}
			$list = $this->retrieveLoanPaymentTransactions();
			if(count($list)>0){
				$objPdf->writeSubheaderTitle('LOANS',0.5);
				//$objPdf->Ln();
			}
			else{
				$objPdf->writeSubheaderTitle('LOANS - No Records',0.5);
				//$objPdf->Ln();
			}
			foreach($list as $value){
				$this->_setCellPDF($objPdf, 1, "");
				$this->_setCellPDF($objPdf, 5, "Loan No:");
				$this->_setCellPDF($objPdf, 20, $value['loan_no']);
				$this->_setCellPDF($objPdf, 6, "Date:");
				$this->_setCellPDF($objPdf, 5, $value['loan_date']);
				$objPdf->Ln(.15);
				$this->_setCellPDF($objPdf, 1, "");
				$this->_setCellPDF($objPdf, 5, "Principal:");
				$this->_setCellPDF($objPdf, 20, $value['principal']);
				$this->_setCellPDF($objPdf, 6, "Interest:");
				$this->_setCellPDF($objPdf, 5, $value['interest_rate']);
				$objPdf->Ln(.15);
				$this->_setCellPDF($objPdf, 26, "");
				$this->_setCellPDF($objPdf, 6, "Term in Months:");
				$this->_setCellPDF($objPdf, 6, $value['term']);
				$objPdf->Ln(.15);
				
				if(count($value['loan_payments'])>0){
					$objPdf->writeSubheader(
										array(" " => 2		//no of cells in actual excel
											 ,"Date" => 5
											 ,"Code" => 5			
											 ,"Amount" => 7			
											 ,"Balance" => 11		
											 ,"Interest" => 11							 
											 )
										,array(" " => "C"	//no of cells in actual excel
											 ,"Date" => "C"
											 ,"Code" => "L"			
											 ,"Amount" => "R"			
											 ,"Balance" => "R"		
											 ,"Interest" => "R"
											 )
										,0.5);
				
					$objPdf->writeLoanTableData(
									$value['loan_payments']
									,array("number" => 2		//no of cells in actual excel
										 ,"date" => 5
										 ,"code" => 5			
										 ,"amount" => 7			
										 ,"balance" => 11		
										 ,"interest" => 11							 
										 )
									,array("number" => "C"		
										 ,"date" => "C"
										 ,"code" => "L"			
										 ,"amount" => "R"			
										 ,"balance" => "R"			
										 ,"interest" => "R"				 
										 ),0.5, 0.15);
				}
				$objPdf->Ln(.15);
				//$objPdf->i+=7;
				//log_message('debug', 'xx-xx '.$objPdf->i);
				/*if ($objPdf->i>=90) {
					
					$objPdf->writeFooter();
					$objPdf->pdf_obj->AddPage();
					$objPdf->Header();
					$objPdf->i=0;
				}*/
			}
			$list = $this->retrieveGuaranteedLoans();
			if(count($list)>0){
				$objPdf->writeSubheaderTitle('CO-MADE LOANS',0.5);	
				//$objPdf->Ln();
				$objPdf->writeSubheader(
										array("Loan No." => 5		//no of cells in actual excel
											 ,"Borrower ID" => 6
											 ,"Borrower Name" => 16			
											 ,"Loan Code" => 5			
											 ,"Loan Date" => 9						 
											 )
										,array("Loan No." => "L"	//no of cells in actual excel
											 ,"Borrower ID" => "R"
											 ,"Borrower Name" => "L"		
											 ,"Loan Code" => "L"		
											 ,"Loan Date" => "C"					 
											 ),0.5);
				
				$objPdf->writeTableData(
										$list
										,array("loan_no" => 5		//no of cells in actual excel
											 ,"borrower_id" => 6
											 ,"borrower_name" => 16		
											 ,"loan_code" => 5			
											 ,"loan_date" => 9						 
											 )
										,array("loan_no" => "L"	
											 ,"borrower_id" => "R"
											 ,"borrower_name" => "L"			
											 ,"loan_code" => "L"			
											 ,"loan_date" => "C"							 
										),0.5, 0.15, 58, 7);
				$objPdf->Ln(.15);
			}
			else
				$objPdf->writeSubheaderTitle('CO-MADE LOANS - No Records',0.5);	
			//$objPdf->Ln();	
			$this->_writeOthers($objPdf);
			$objPdf->writeFooter();
			$objPdf->i=0;
		}
		
		$file_name = "";	
		if ($this->report_type=='1'){
			$file_name = $this->employee_id;
		}
		else if ($this->report_type=='2'){
			$file_name = $this->company_code;
		}
		else if ($this->report_type=='3'){
			$file_name = $this->start_trans_date."-".$this->end_trans_date;
		}
		
		$objPdf->Output($file_name . ' - ' . $this->report_title."_".date("YmdHis"));
	}
	
	/**
	 * @desc Displays the report in Excel File
	 */
	function printExcel()
	{
		$row_start = 6;								 		//row after the header		
		$objExcel = new Asi_excel_ext("portrait");
		$objExcel->init(44,5);	
					
		$objExcel->writeHeaderInfo($this->report_title, $this->report_start_date." to ".$this->report_end_date,  $this->report_id );	
		foreach($this->employee_list as $employee_id){
			$this->employee_id = $employee_id['employee_id'];
			$list = $employee_id;
			$this->font_size = 8;
			$this->_setCell($objExcel->php_excel_obj, 'B'.$row_start, 'Employee ID:');
			$this->_setCell($objExcel->php_excel_obj, 'G'.$row_start, $list['employee_id'].' ');
			$this->_setCell($objExcel->php_excel_obj, 'T'.$row_start, 'Company:');
			$this->_setCell($objExcel->php_excel_obj, 'AB'.$row_start++, ' '.$list['company_code']);
			$this->_setCell($objExcel->php_excel_obj, 'B'.$row_start, 'Name:');
			$this->_setCell($objExcel->php_excel_obj, 'G'.$row_start, $list['last_name'].', '.$list['first_name'].' '.$list['middle_name']);
			$this->_setCell($objExcel->php_excel_obj, 'T'.$row_start, 'Years of Employment:');
			$this->_setCell($objExcel->php_excel_obj, 'AB'.$row_start, ' '.$list['member_date'].' ');	
			$row_start+=2;
				
			$list = $this->retrieveCapConTransactions();
			if(count($list)>0){
				$objExcel->writeSubheaderTitle("CAPITAL CONTRIBUTION", null, $row_start, null);
				$row_start+=2;
				
				$objExcel->writeSubheader(
										array("Date" => 5		//no of cells in actual excel
											 ,"Transaction Code" => 11
											 ,"Addition" => 7			
											 ,"Deduction" => 7			
											 ,"Balance" => 7							 
											 )
										,0
										,$row_start
										,12.75
										,array("Date" => "center"		
											 ,"Transaction Code" => "left"
											 ,"Addition" => "right"			
											 ,"Deduction" => "right"			
											 ,"Balance" => "right"					 
											 )
										,true
										,$row_start++);	
				$objExcel->writeTableData(
										$list
										,array("date" => "center"		
											 ,"transaction_code" => "left"
											 ,"addition" => "right"			
											 ,"deduction" => "right"			
											 ,"balance" => "right"					 
											 )
										,array("date" => "s"		
											 ,"transaction_code" => "s"
											 ,"addition" => "#,##0.00"			
											 ,"deduction" => "#,##0.00"			
											 ,"balance" => "#,##0.00"					 
											 )
										,$row_start);
				$row_start+=count($list);
			}
			else{
				$objExcel->writeSubheaderTitle("CAPITAL CONTRIBUTION - No Records", null, $row_start, null);
				$row_start++;
			}
			$list = $this->retrieveLoanPaymentTransactions();
			if(count($list)>0)
				$objExcel->writeSubheaderTitle("LOANS", null, ++$row_start, null);
			else{
				$objExcel->writeSubheaderTitle("LOANS - No Records", null, ++$row_start, null);
				$row_start++;
			}
			foreach($list as $value){
				$row_start+=2;
				$this->_setCell($objExcel->php_excel_obj, 'B'.$row_start, 'Loan No:');
				$this->_setCell($objExcel->php_excel_obj, 'G'.$row_start, $value['loan_no'].' ');
				$this->_setCell($objExcel->php_excel_obj, 'X'.$row_start, 'Date:');
				$this->_setCell($objExcel->php_excel_obj, 'AB'.$row_start++, $value['loan_date']);
				$this->_setCell($objExcel->php_excel_obj, 'B'.$row_start, 'Principal');
				$this->_setCell($objExcel->php_excel_obj, 'G'.$row_start, $value['principal']);
				$this->_setCell($objExcel->php_excel_obj, 'X'.$row_start, 'Interest:');
				$this->_setCell($objExcel->php_excel_obj, 'AB'.$row_start++, $value['interest_rate']." ");
				$this->_setCell($objExcel->php_excel_obj, 'X'.++$row_start, 'Term In Months');
				$this->_setCell($objExcel->php_excel_obj, 'AD'.$row_start++, $value['term'].' ');

				$objExcel->writeSubheader(
								array(" " => 2		//no of cells in actual excel
									 ,"Date" => 4
									 ,"Code" => 5			
									 ,"Amount" => 6			
									 ,"Balance" => 10		
									 ,"Interest" => 10							 
									 )
								,0
								,++$row_start
								,12.75
								,array(" " => "center"		
									 ,"Date" => "center"
									 ,"Code" => "left"			
									 ,"Amount" => "right"			
									 ,"Balance" => "right"			
									 ,"Interest" => "right"				 
									 )
								,true
								,$row_start++);
				$objExcel->writeTableData(
										$value['loan_payments']
										,array("number" => "center"		
											 ,"date" => "center"
											 ,"code" => "left"			
											 ,"amount" => "right"			
											 ,"balance" => "right"			
											 ,"interest" => "right"				 
											 )
										,array("number" => "s"		
											 ,"date" => "s"
											 ,"code" => "s"			
											 ,"amount" => "#,##0.00"			
											 ,"balance" => "#,##0.00"				
											 ,"interest" => "#,##0.00"				 
											 )
										,$row_start);
				$row_start += count($value['loan_payments']);
			}
			$list = $this->retrieveGuaranteedLoans();
			if(count($list) == 0)
				$objExcel->writeSubheaderTitle("CO-MADE LOANS - No Records", null, ++$row_start, null);
			else{
				$objExcel->writeSubheaderTitle("CO-MADE LOANS", null, ++$row_start, null);
				$row_start+=2;
				
				$objExcel->writeSubheader(
								array("Loan No." => 4		//no of cells in actual excel
									 ,"Borrower ID" => 5
									 ,"Borrower Name" => 15			
									 ,"Loan Code" => 5			
									 ,"Loan Date" => 8							 
									 )
								,0
								,$row_start
								,12.75
								,array("Loan No." => "left"		//no of cells in actual excel
									 ,"Borrower ID" => "right"
									 ,"Borrower Name" => "left"			
									 ,"Loan Code" => "left"			
									 ,"Loan Date" => "center"							 
									 )
								,true
								,$row_start++);
				$objExcel->writeTableData(
										$list
										,array("loan_no" => "left"	
											 ,"borrower_id" => "right"
											 ,"borrower_name" => "left"			
											 ,"loan_code" => "left"			
											 ,"loan_date" => "center"							 
										)
										,array("loan_no" => "s"	
											 ,"borrower_id" => "s"
											 ,"borrower_name" => "s"			
											 ,"loan_code" => "s"			
											 ,"loan_date" => "s"							 
										)
										,$row_start);
				$row_start += count($list);
			}
			$row_start+=2;
			$this->_writeLegendExcel($row_start, $objExcel->php_excel_obj);
			$row_start+=6;
			$this->_writeNoticeExcel($row_start, $objExcel->php_excel_obj);
			
			$row_start+=6;
			$objExcel->writeFooter();			
		
		}
		
		$report = "";
		if ($this->report_type=='1'){
			$report = $this->employee_id."-".$this->report_title;
		}
		else if ($this->report_type=='2'){
			$report = $this->company_code."-".$this->report_title;
		}
		else if ($this->report_type=='3'){
			$report = $this->start_trans_date."-".$this->end_trans_date."-".$this->report_title;
		}
		
		$objWriter = new PHPExcel_Writer_Excel5($objExcel->php_excel_obj);
		$filename = str_replace(" ", "_", $report)."_".date("YmdHis").".xls";
		$objExcel->outputExcel($filename);
		$objWriter->save("php://output");	 
	}
	
	function _writeLegendExcel($row_start, $object){
		$arr1 = array("ADJ" => "ADJUSTMENT"	
					,"ERCC" => "APO-ERC PAYMENT CC"	
					,"AQCC" => "ASSET AQC INTEREST"	
					,"BMBC" => "BELOW MINIMUM CAPCON"	
					,"CCSP" => "CAPCON SUSPENSE"
					,"ABMC" => "APO-ABM CC"	
					,"JQMC" => "APO-JQM PAYMENT CC"
					,"BMBN" => "BELOW MIN BALANCE LP"
					,"BFCC" => "BROKER FEES FROM"
					,"eDST" => "ELECTRONIC DOCUMENT STAMP TAX");
		$row = $row_start;
		$col1 = 'D';
		$col2 = 'H';
		$limit = $row_start+5;
		$this->font_size = 6;
		foreach($arr1 as $key=>$value){
			$this->_setCell($object, $col1.$row, $key);
			$this->_setCell($object, $col2.$row, $value);
			$row++;
			if($row == $limit){
				$row = $row_start;
				$col1 = 'V';
				$col2 = 'Z';
			}
		}
	}

	function _writeLegendPDF($object){
		$arr1 = array("ADJ" => "ADJUSTMENT"		
					,"ABMC" => "APO-ABM CC"	
					,"ERCC" => "APO-ERC PAYMENT CC"	
					,"JQMC" => "APO-JQM PAYMENT CC"	
					,"AQCC" => "ASSET AQC INTEREST"
					,"BMBN" => "BELOW MIN BALANCE LP"
					,"BMBC" => "BELOW MINIMUM CAPCON"	
					,"BFCC" => "BROKER FEES FROM"	
					,"CCSP" => "CAPCON SUSPENSE"
					,"eDST" => "ELECTRONIC DOCUMENT STAMP TAX");
		$newLine = true;
		$this->font_size = 6;
		foreach($arr1 as $key=>$value){
			if($newLine){
				$newLine = false;
				$object->Ln();
				$this->_setCellPDF($object, 5, "");
			}
			else
				$newLine = true;
			$this->_setCellPDF($object, 4, $key);
			$this->_setCellPDF($object, 14, $value);
		}
	}

	
	function _writeNoticeExcel($row_start, $object){
		$object->getActiveSheet()->SetCellValue('A'.$row_start, "THIS STATEMENT OF ACCOUNT WILL SERVE AS YOUR CONFIRMATION STATEMENT. REPORT ANY  EXCEPTION TO THIS STATEMENT TO THE AUDITOR WITHIN TEN (10) DAYS FROM RECEIPT, OTHERWISE, ALL ENTRIES  CONTAINED THEREIN ARE CONSIDERED CORRECT.");
		$object->getActiveSheet()->getStyle('A'.$row_start)->applyFromArray(
			array('font'=> array('name'=>'Arial',
								'bold'=>false,
								'italic'=>false,
								'size'=>$this->font_size)
			));
		$object->getActiveSheet()->getStyle('A'.$row_start)->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_RED);
		$object->getActiveSheet()->getStyle('A'.$row_start)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$object->getActiveSheet()->getStyle('A'.$row_start)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$object->getActiveSheet()->getStyle('A'.$row_start)->getAlignment()->setWrapText(true);
		$object->getActiveSheet()->mergeCells('A'.$row_start.':AK'.($row_start+2));
	}
	
	function _writeOthers($object){
		//$object->pdf_obj->AddPage();
		//$object->Header();
		$object->pdf_obj->SetFont('Arial','',9);	
		$this->_writeLegendPDF($object);
		$object->Ln();
		$object->Ln();
		$object->pdf_obj->SetFont('Arial','',8);
		$object->pdf_obj->SetTextColor(255,0,0);
		$notice =  "THIS STATEMENT OF ACCOUNT WILL SERVE AS YOUR CONFIRMATION STATEMENT. REPORT ANY  EXCEPTION TO THIS STATEMENT TO THE AUDITOR";
		$notice2 = "                       		WITHIN TEN (10) DAYS FROM RECEIPT, OTHERWISE, ALL ENTRIES CONTAINED THEREIN ARE CONSIDERED CORRECT.";
		//$notice3 = "                                           ALL ENTRIES CONTAINED THEREIN ARE CONSIDERED CORRECT.";
		$this->_setCellPDF($object, 30, $notice);
		$object->Ln();
		$this->_setCellPDF($object, 30, $notice2);
		//$object->Ln();
		//$this->_setCellPDF($object, 30, $notice3);
		//$object->writeFooter();
	}
	
	function _setCell($object, $cell, $value){
		$object->getActiveSheet()->SetCellValue($cell, $value);
		$object->getActiveSheet()->getStyle($cell)->applyFromArray(
			array('font'=> array('name'=>'Arial',
								'bold'=>false,
								'italic'=>false,
								'size'=>$this->font_size)
			));
	}
	function _setCellPDF($object, $col, $value){
		$object->pdf_obj->SetFont('Arial','',$this->font_size);
		$object->pdf_obj->Cell($col*($object->row_height),$object->row_height,$value);
	}
	
}

?>