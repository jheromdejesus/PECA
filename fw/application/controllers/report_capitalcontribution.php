<?php

/* Location: ./CodeIgniter/application/controllers/report_capitalcontribution.php */
/*
NOTES
Capital Contribution
-Prooflist : displays all new capcon transactions
-Audit Trail: displays processed capcon for the given date
-BMB Audit Trail: displays posted transactions with BMB Penalty for the given range
*/

class Report_capitalcontribution extends Asi_Controller 
{
	var $report_type;	//1 - prooflist; 2 - audit trail; 3 - BMB
	var $file_type;		//1 - excel ; 2 - pdf
	
	var $report_date_from;
	var $report_date_to;
	var $report_date;
	var $report_title;
	var $list;	
	var $row_height = .18; 	

	function Report_capitalcontribution() 
	{
		parent::Asi_Controller();
		$this->load->model('mcaptranheader_model');
		$this->load->model('tcaptranheader_model');
		$this->load->model('mtransaction_model');
		$this->load->model('member_model');
		$this->load->helper('url');
		$this->load->library('constants');
		$this->load->library('asi_model');
		$this->load->library('asi_pdf_ext');
		$this->load->library('asi_excel_ext');
		$this->load->helper('date');	
	}
	
	function index() 
	{		
		/*$_REQUEST['report_type'] = '2';
		$_REQUEST['file_type'] = '2';
		$_REQUEST['report_date'] = '11/30/2010';*/
		
		$this->report_type = $_REQUEST['report_type'];
		$this->file_type = $_REQUEST['file_type'];
	
		//if ($this->report_type=='3') {
		//jdd 07242017 added capcon audit report
		if ($this->report_type=='3' || $this->report_type=='2') {
			$this->report_date_from = date("Ymd", strtotime($_REQUEST['from_date']));
			$this->report_date_to = date("Ymd", strtotime($_REQUEST['to_date']));
		}
		else {
			$this->report_date = date("Ymd", strtotime($_REQUEST['report_date']));
		}
	
		$result = $this->setTableData($this->report_type);	
		
		if ($result == true) {
			echo("{'success':false,'msg':'No records found.','error_code':'19'}");	
		}
		else {
			if ($this->file_type=='1') $this->printExcel();
			else $this->printPDF();
		}		
	}

	/**
	 * @desc To all retrieve Capital Contribution transactions which are not yet processed
	 * @return array
	 */
	function readCapConProoflist()
	{
		$_REQUEST['filter'] = array(
									'tc.transaction_code' => $this->transaction_code
									,'tc.status_flag' => '1'
							  );
		
		$data = $this->tcaptranheader_model->getCapCon(
			array_key_exists('filter',$_REQUEST) ? $_REQUEST['filter'] : null,
			null,
			null,
			array('tc.transaction_no AS transaction_no'
					,'tc.employee_id AS employee_id'											
					,'tc.transaction_date AS transaction_date'						
					,'tc.transaction_amount AS transaction_amount'						
					,'tcc.transaction_code AS charge_code'						
					,'tcc.amount AS amount'						
					,'tc.or_no AS or_no'						
					,'tc.or_date AS or_date'						
					,'tc.remarks AS remarks'
					,'tc.bank_transfer AS bank_transfer')
				,'tc.transaction_no DESC'
		);
		
		return $data;
		
	}
	
	/**
	 * @desc To retrieve all Capital Contribution transactions which are not yet processed
	 * @return array
	 */
	function readCapConCodesProoflist()
	{
		$_REQUEST['filter'] = array(
									'tc.status_flag' => '1'
							  );
		
		$data = $this->tcaptranheader_model->getCapConCodes(
			null,
			null,
			null,
			array('tc.transaction_code AS transaction_code'
				 ,'rt.transaction_description AS transaction_description')
			,'tc.transaction_code ASC'
		);
		
		return $data['list'];
		
	}
	
	/**
	 * @desc To retrieve Capital Contribution transactions which are not yet processed
	 * @return array
	 */
	function readCapConAuditTrail()
	{
	// jdj	
	/*$_REQUEST['filter'] = array(
									'tc.transaction_date' => $this->report_date
									,'tc.transaction_code' => $this->transaction_code
									,'tc.status_flag' => '2'
							  ); */
		
		$_REQUEST['filter'] = array(
				'tc.transaction_date >=' => $this->report_date_from
				,'tc.transaction_date <=' => $this->report_date_to
				,'tc.transaction_code' => $this->transaction_code
				,'tc.status_flag' => '2'
		);
		
		$data = $this->mcaptranheader_model->getCapCon(
			array_key_exists('filter',$_REQUEST) ? $_REQUEST['filter'] : null,
			null,
			null,
			array('tc.transaction_no AS transaction_no'
					,'tc.employee_id AS employee_id'											
					,'tc.transaction_date AS transaction_date'						
					,'tc.transaction_amount AS transaction_amount'						
					,'tcc.transaction_code AS charge_code'						
					,'tcc.amount AS amount'						
					,'tc.or_no AS or_no'						
					,'tc.or_date AS or_date'						
					,'tc.remarks AS remarks')
				,'tc.transaction_no DESC'
		);
		
		return $data;	
	}
	
	/**
	 * @desc To retrieve Capital Contribution transactions which are not yet processed
	 * @return array
	 */
	function readCapConCodesAuditTrail()
	{
		/*
		$_REQUEST['filter'] = array(
									'tc.transaction_date' => $this->report_date
									,'tc.status_flag' => '2'
							  ); */
		
		$_REQUEST['filter'] = array(
				'tc.transaction_date >=' => $this->report_date_from
				,'tc.transaction_date <=' => $this->report_date_to
				,'tc.status_flag' => '2'
		);
		
		$data = $this->mcaptranheader_model->getCapConCodes(
			array_key_exists('filter',$_REQUEST) ? $_REQUEST['filter'] : null,
			null,
			null,
			array('tc.transaction_code AS transaction_code'
				 ,'rt.transaction_description AS transaction_description')
			,'tc.transaction_code ASC'
		);
	
		return $data['list'];
		
	}
	
	/**
	 * @desc Retrieve Transactions with BMB Penalty, done after posting
	 * @return array
	 */
	function readBMBAuditTrail()
	{
		$_REQUEST['filter'] = array(
									'transaction_date >=' => $this->report_date_from
		 							,'transaction_date <=' => $this->report_date_to
									,'transaction_code LIKE' => 'BMB%' 
									,'status_flag' => '3'
							  );
		
		$data = $this->mtransaction_model->get_list(
			array_key_exists('filter',$_REQUEST) ? $_REQUEST['filter'] : null,
			null,
			null,
			array('transaction_no'
				 ,'transaction_code'
				 ,'employee_id'
				 ,'transaction_amount')
			,'transaction_no DESC'
		);
		
		return $data;	
	}
	
	/**
	 * @desc Displays the report in Excel File
	 */
	function printExcel()
	{
		$row_start = 6;											 		//row after the header		
		if ($this->report_type == '3') 
		{								
			$objExcel = new Asi_excel_ext("portrait");
			$objExcel->init(38,5,.7,false); 		
			$objExcel->writeHeaderInfo($this->report_title, "BMB Report for ".date("F j, Y",strtotime($this->report_date_from))." to ".date("F j, Y",strtotime($this->report_date_to)), "T00002");
			$objExcel->writeSubheader(array("Transaction No" => 5		//no of cells in actual excel
											,"Transaction Code" => 6
											,"Employee ID" => 5
											,"Employee Name" => 12
											,"Amount" => 5)
									  ,0
									  ,$row_start
									  ,12.75
									  ,array("Transaction No" => "right"		//no of cells in actual excel
											,"Transaction Code" => "left"
											,"Employee ID" => "right"
											,"Employee Name" => "left"
											,"Amount" => "right")
									  ,true
									  ,$row_start);
			$objExcel->writeTableData($this->list
									  ,array("transaction_no" => "right"
													,"transaction_code" => "left"
													,"employee_id" => "right"
													,"employee_name" => "left"
													,"transaction_amount" => "right")
									  ,array("transaction_no" => "s"
													,"transaction_code" => "s"
													,"employee_id" => "s"
													,"employee_name" => "s"
													,"transaction_amount" => "#,##0.00")
									  ,$row_start+1);	
			$objExcel->writeTotals(array("total" => "Total"
													,"transaction_code" => ""
													,"employee_id" => ""
													,"total_transactions" => count($this->transaction_list['list'])." Employee(s)"
													,"total_amount" => number_format($this->transaction_amount, 2, '.',','))
									  ,array("total" => "left"
													,"transaction_code" => "left"
													,"employee_id" => "right"
													,"total_transactions" => "left"
													,"total_amount" => "right")
									  ,array("total" => "s"
													,"transaction_code" => "s"
													,"employee_id" => "s"
													,"total_transactions" => "s"
													,"total_amount" => "#,##0.00")
									  ,'A'
									  ,'AG');							  									  
		}
		else if ($this->report_type == '2') 
		{			
			$objExcel = new Asi_excel_ext();
			$objExcel->init(64,7);
			//jdj 07242017 change date into from and to
			//$objExcel->writeHeaderInfo($this->report_title, "For ".date("F j, Y", strtotime($this->report_date)), "T00001");
			$objExcel->writeHeaderInfo($this->report_title, "For ".date("F j, Y",strtotime($this->report_date_from))." to ".date("F j, Y",strtotime($this->report_date_to)), "T00001");
			
			$i=0;								  
			foreach ($this->transaction_code_list AS $data) 
			{
				$this->transaction_code = $data["transaction_code"];
				$this->transaction_description = $data["transaction_description"];					
				$objExcel->writeSubheaderTitle($this->transaction_description, 0, $row_start);					  				
				
				if ($i==0) {
					$row_start++;
					$objExcel->writeSubheader(array("Transaction No" => 4		//no of cells in actual excel
												,"Employee ID" => 4
												,"Employee Name" => 12
												,"Transaction Date" => 5
												,"Amount" => 4
												,"Charge Code" => 4
												,"Charge Amount" => 5
												,"OR No." => 4
												,"OR Date" => 4
												,"Remarks" => 9)
											   ,0
											  ,$row_start
											  ,12.75
											  ,array("Transaction No" => "right"	
												,"Employee ID" => "right"
												,"Employee Name" => "left"
												,"Transaction Date" => "center"
												,"Amount" => "right"
												,"Charge Code" => "left"
												,"Charge Amount" => "right"
												,"OR No." => "left"
												,"OR Date" => "center"
												,"Remarks" => "left")
											  ,true
											  ,$row_start);	
				}
				
				$this->transaction_list = $this->readCapConAuditTrail($this->report_date, $this->transaction_code);
								  				
				$this->list = $this->getData($this->transaction_list['list']);	
				$count = (count($this->list))+2;	
				$row_start++;
				$objExcel->writeTableData($this->list
  										  ,array("transaction_no" => "right"
											      ,"employee_id" => "right"
												  ,"employee_name" => "left"
												  ,"transaction_date" => "center"
												  ,"transaction_amount" => "right"
												  ,"charge_code" => "left"
												  ,"charge_amount" => "right"
												  ,"or_no" => "right"
												  ,"or_date" => "center"
												  ,"remarks" => "left")
										  ,array("transaction_no" => "s"
												  ,"employee_id" => "s"
												  ,"employee_name" => "s"
												  ,"transaction_date" => "s"
												  ,"transaction_amount" => "#,##0.00"
												  ,"charge_code" => "left"
												  ,"charge_amount" => "#,##0.00"
												  ,"or_no" => "s"
												  ,"or_date" => "s"
												  ,"remarks" => "s")		
										  ,$row_start);	
										  																  	 
				$objExcel->writeTotals(array("total" => "Total"
												,"employee_id" => ""
												,"total_transactions" => count($this->transaction_list['list'])." Transactions"
												,"transaction_date" => ""
												,"total_amount" => number_format($this->transaction_amount, 2, '.',',')
												,"charge_code" => ""
												,"charge_amount" => number_format($this->charge_amount, 2, '.',',')
												,"or_no" => ""
												,"or_date" => ""
												,"remarks" => "")
										,array("total" => "left"
												,"employee_id" => "right"
												,"total_transactions" => "left"
												,"transaction_date" => "center"
												,"total_amount" => "right"
												,"charge_code" => "left"
												,"charge_amount" => "right"
												,"or_no" => "right"
												,"or_date" => "center"
												,"remarks" => "left")
										,array("total" => "s"
												,"employee_id" => "s"
												,"total_transactions" => "s"
												,"transaction_date" => "s"
												,"total_amount" => "#,##0.00"
												,"charge_code" => "s"
												,"charge_amount" => "#,##0.00"
												,"or_no" => "s"
												,"or_date" => "s"
												,"remarks" => "s")
										,'A' 	  					//first column name:used for border
										,'AX');	  				//end column name:used for border
				$row_start = ($row_start+$count);
				$i++;
			}
		}
		else //if ($this->report_type == '1')
		{
			$objExcel = new Asi_excel_ext();
			$objExcel->init(64,7);		
			$objExcel->writeHeaderInfo($this->report_title, "For ".date("F j, Y", strtotime($this->report_date)), "T00001");
			
			$i=0;								  
			foreach ($this->transaction_code_list AS $data) 
			{
				$this->transaction_code = $data["transaction_code"];
				$this->transaction_description = $data["transaction_description"];					
				$objExcel->writeSubheaderTitle($this->transaction_description, 0, $row_start);					  				
				
				if ($i==0) {
					$row_start++;
					$objExcel->writeSubheader(array("Transaction No" => 4		//no of cells in actual excel
												,"Employee ID" => 4
												,"Employee Name" => 12
												,"Transaction Date" => 5
												,"Amount" => 4
												,"Charge Code" => 4
												,"Charge Amount" => 5
												//,"OR No." => 4
												//,"OR Date" => 4
												,"Remarks" => 9)
											   ,0
											  ,$row_start
											  ,12.75
											  ,array("Transaction No" => "right"	
												,"Employee ID" => "right"
												,"Employee Name" => "left"
												,"Transaction Date" => "center"
												,"Amount" => "right"
												,"Charge Code" => "left"
												,"Charge Amount" => "right"
												//,"OR No." => "left"
												//,"OR Date" => "center"
												,"Remarks" => "left")
											  ,true
											  ,$row_start);	
				}
				
				$this->transaction_list = $this->readCapConProoflist();
								  				
				$this->list = $this->getData($this->transaction_list['list']);	
				$count = (count($this->list))+2;	
				$row_start++;
				$objExcel->writeTableData($this->list
  										  ,array("transaction_no" => "right"
											      ,"employee_id" => "right"
												  ,"employee_name" => "left"
												  ,"transaction_date" => "center"
												  ,"transaction_amount" => "right"
												  ,"charge_code" => "left"
												  ,"charge_amount" => "right"
												  //,"or_no" => "right"
												  //,"or_date" => "center"
												  ,"remarks" => "left")
										  ,array("transaction_no" => "s"
												  ,"employee_id" => "s"
												  ,"employee_name" => "s"
												  ,"transaction_date" => "s"
												  ,"transaction_amount" => "#,##0.00"
												  ,"charge_code" => "left"
												  ,"charge_amount" => "#,##0.00"
												  //,"or_no" => "s"
												  //,"or_date" => "s"
												  ,"remarks" => "s")		
										  ,$row_start);	
										  																  	 
				$objExcel->writeTotals(array("total" => "Total"
												,"employee_id" => ""
												,"total_transactions" => count($this->transaction_list['list'])." Transactions"
												,"transaction_date" => ""
												,"total_amount" => number_format($this->transaction_amount, 2, '.',',')
												,"charge_code" => ""
												,"charge_amount" => number_format($this->charge_amount, 2, '.',',')
												//,"or_no" => ""
												//,"or_date" => ""
												,"remarks" => "")
										,array("total" => "left"
												,"employee_id" => "right"
												,"total_transactions" => "left"
												,"transaction_date" => "center"
												,"total_amount" => "right"
												,"charge_code" => "left"
												,"charge_amount" => "right"
												//,"or_no" => "right"
												//,"or_date" => "center"
												,"remarks" => "left")
										,array("total" => "s"
												,"employee_id" => "s"
												,"total_transactions" => "s"
												,"transaction_date" => "s"
												,"total_amount" => "#,##0.00"
												,"charge_code" => "s"
												,"charge_amount" => "#,##0.00"
												//,"or_no" => "s"
												//,"or_date" => "s"
												,"remarks" => "s")
										,'A' 	  					//first column name:used for border
										,'AX');	  				//end column name:used for border
				$row_start = ($row_start+$count);
				if ($this->transaction_code=='WDWL')
				{
					$bank_Y = 0;
					$bank_N = 0;
					$data = $this->tcaptranheader_model->getWithdrawalBankTransfer();
					foreach($data['list'] as $dat){
						if ($dat['bank_transfer']=='N')
							$bank_N = $dat['amount'];
						if ($dat['bank_transfer']=='Y')
							$bank_Y = $dat['amount'];	
					}
				
					$objExcel->_setCell('A'.$row_start,"Total Amount - Bank Transfer:              ".number_format($bank_Y,2,'.',','),true,7);
					$row_start++;
					$objExcel->_setCell('A'.$row_start,"Total Amount - non-Bank Transfer:      ".number_format($bank_N,2,'.',','),true,7);
				}
				$i++;
			}
		}		
		$objExcel->writeFooter();								
		$objWriter = new PHPExcel_Writer_Excel5($objExcel->php_excel_obj);
		$filename = str_replace(" ", "_", $this->report_title)."_".date("YmdHis").".xls";
		$objExcel->outputExcel($filename);
		$objWriter->save("php://output");
	}
	
	/**
	 * @desc Displays the report in PDF File
	 */
	function printPDF()
	{	
		if ($this->report_type=='3') 
		{
			$objPdf = new Asi_pdf_ext();
			$objPdf->init("portrait", .7);				
			$objPdf->writeHeaderInfo($this->report_title, "BMB Report for ".date("F j, Y",strtotime($this->report_date_from))." to ".date("F j, Y",strtotime($this->report_date_to)), "T00002");
			$objPdf->writeSubheader(array("Transaction No" => 5			//no of cells in actual excel
											,"Transaction Code" => 6
											,"Employee ID" => 5
											,"Employee Name" => 12
											,"Amount" => 5)
									,array("Transaction No" => "R"			//no of cells in actual excel
											,"Transaction Code" => "L"
											,"Employee ID" => "R"
											,"Employee Name" => "L"
											,"Amount" => "R"));								
			$count = $objPdf->writeTableData($this->list
									  ,array("transaction_no" => 5
													,"transaction_code" => 6
													,"employee_id" => 5
													,"employee_name" => 12
													,"transaction_amount" => 5)
									  ,array("transaction_no" => "R"
													,"transaction_code" => "L"
													,"employee_id" => "R"
													,"employee_name" => "L"
													,"transaction_amount" => "R")
									 ,.7
									 ,null
									 ,45);	
			$objPdf->writeTotals(array("total" => "Total"
													,"transaction_code" => ""
													,"employee_id" => ""
													,"total_transactions" => count($this->transaction_list['list'])." Employee(s)"
													,"total_amount" => number_format($this->transaction_amount, 2, '.',','))
									  ,array("total" => "left"
													,"transaction_code" => "L"
													,"employee_id" => "R"
													,"total_transactions" => "L"
													,"total_amount" => "R")
									  ,array("total" => 5
													,"transaction_code" => 6
													,"employee_id" => 5
													,"total_transactions" => 12
													,"total_amount" => 5));																						
		}
		else if ($this->report_type=='2')
		{	
			$objPdf = new Asi_pdf_ext();
			$objPdf->init("landscape", .7);	
			//$i = 0;									
			foreach ($this->transaction_code_list AS $data) 
			{
				$objPdf->i = 0;
				//jdj 07242017 change date into from and to
				//$objPdf->writeHeaderInfo($this->report_title, "For ".date("F j, Y", strtotime($this->report_date)), "T00001");
				$objPdf->writeHeaderInfo($this->report_title, "For ".date("F j, Y",strtotime($this->report_date_from))." to ".date("F j, Y",strtotime($this->report_date_to)), "T00001");
				//$objPdf->writeHeaderInfo($this->report_title, "For ".date("F j, Y", strtotime($this->report_date)), "T00001");
				$this->transaction_code = $data["transaction_code"];
				$this->transaction_description = $data["transaction_description"];	
				$objPdf->writeSubheaderTitle($this->transaction_description);
				
				//if ($i==0) {					
					$objPdf->writeSubheader(array("Transaction No" => 5		//no of cells in actual excel
													,"Employee ID" => 4
													,"Employee Name" => 11
													,"Transaction Date" => 5
													,"Amount" => 4
													,"Charge Code" => 4
													,"Charge Amount" => 5
													//,"OR No." => 5
													//,"OR Date" => 5
													,"Remarks" => 9)
										,array("Transaction No" => "R"		//no of cells in actual excel
													,"Employee ID" => "R"
													,"Employee Name" => "L"
													,"Transaction Date" => "C"
													,"Amount" => "R"
													,"Charge Code" => "L"
													,"Charge Amount" => "R"
													//,"OR No." => "R"
													//,"OR Date" => "C"
													,"Remarks" => "L"));	
				//}	
				
				$this->transaction_list = $this->readCapConAuditTrail($this->report_date, $this->transaction_code);
								  				
				$this->list = $this->getData($this->transaction_list['list']);	
				$count = $objPdf->writeTableData($this->list
										  ,array("transaction_no" => 5
												  ,"employee_id" => 4
												  ,"employee_name" => 11
												  ,"transaction_date" => 5
												  ,"transaction_amount" => 4
												  ,"charge_code" => 4
												  ,"charge_amount" => 5
												  //,"or_no" => 5
												  //,"or_date" => 5
												  ,"remarks" => 9)
  										  ,array("transaction_no" => "R"
											      ,"employee_id" => "R"
												  ,"employee_name" => "L"
												  ,"transaction_date" => "C"
												  ,"transaction_amount" => "R"
												  ,"charge_code" => "L"
												  ,"charge_amount" => "R"
												  //,"or_no" => "R"
												  //,"or_date" => "C"
												  ,"remarks" => "L")
										  ,.7
										  ,null);																  	 
				$objPdf->writeTotals(array("total" => "Total"
												,"employee_id" => ""
												,"total_transactions" => count($this->transaction_list['list'])." Transactions"
												,"transaction_date" => ""
												,"total_amount" => number_format($this->transaction_amount, 2, '.',',')
												,"charge_code" => ""
												,"charge_amount" => number_format($this->charge_amount, 2, '.',',')
												//,"or_no" => ""
												//,"or_date" => ""
												,"remarks" => "")
										,array("total" => "L"
												,"employee_id" => "R"
												,"total_transactions" => "L"
												,"transaction_date" => "G"
												,"total_amount" => "R"
												,"charge_code" => "L"
												,"charge_amount" => "R"
												//,"or_no" => "R"
												//,"or_date" => "C"
												,"remarks" => "L")
										,array("total" => 5
												,"employee_id" => 4
												,"total_transactions" => 11
												,"transaction_date" => 5
												,"total_amount" => 4
												,"charge_code" => 4
												,"charge_amount" => 5
												//,"or_no" => 5
												//,"or_date" => 5
												,"remarks" => 9));
				//$objPdf->Ln(.25);	
				//$objPdf->AddPage();
				//$i++;
			}			
		}
		else //if ($this->report_type=='1')
		{
			$objPdf = new Asi_pdf_ext();
			$objPdf->init("landscape", .7);	
			$objPdf->writeHeaderInfo($this->report_title, "For ".date("F j, Y", strtotime($this->report_date)), "T00001");
			
			$i = 0;									
			foreach ($this->transaction_code_list AS $data) 
			{
				$this->transaction_code = $data["transaction_code"];
				$this->transaction_description = $data["transaction_description"];	
				$objPdf->writeSubheaderTitle($this->transaction_description);
				
				if ($i==0) {					
					$objPdf->writeSubheader(array("Transaction No" => 5		//no of cells in actual excel
													,"Employee ID" => 4
													,"Employee Name" => 11
													,"Transaction Date" => 5
													,"Amount" => 4
													,"Charge Code" => 4
													,"Charge Amount" => 5
													//,"OR No." => 5
													//,"OR Date" => 5
													,"Remarks" => 9)
										,array("Transaction No" => "R"		//no of cells in actual excel
													,"Employee ID" => "R"
													,"Employee Name" => "L"
													,"Transaction Date" => "C"
													,"Amount" => "R"
													,"Charge Code" => "L"
													,"Charge Amount" => "R"
													//,"OR No." => "R"
													//,"OR Date" => "C"
													,"Remarks" => "L"));	
				}	
				
				$this->transaction_list = $this->readCapConProoflist();
										  				
				$this->list = $this->getData($this->transaction_list['list']);	
				$count = $objPdf->writeTableData($this->list
										  ,array("transaction_no" => 5
												  ,"employee_id" => 4
												  ,"employee_name" => 11
												  ,"transaction_date" => 5
												  ,"transaction_amount" => 4
												  ,"charge_code" => 4
												  ,"charge_amount" => 5
												  //,"or_no" => 5
												  //,"or_date" => 5
												  ,"remarks" => 9)
  										  ,array("transaction_no" => "R"
											      ,"employee_id" => "R"
												  ,"employee_name" => "L"
												  ,"transaction_date" => "C"
												  ,"transaction_amount" => "R"
												  ,"charge_code" => "L"
												  ,"charge_amount" => "R"
												  //,"or_no" => "R"
												  //,"or_date" => "C"
												  ,"remarks" => "L")
										  ,.7
										  ,null);																  	 
				$objPdf->writeTotals(array("total" => "Total"
												,"employee_id" => ""
												,"total_transactions" => count($this->transaction_list['list'])." Transactions"
												,"transaction_date" => ""
												,"total_amount" => number_format($this->transaction_amount, 2, '.',',')
												,"charge_code" => ""
												,"charge_amount" => number_format($this->charge_amount, 2, '.',',')
												//,"or_no" => ""
												//,"or_date" => ""
												,"remarks" => "")
										,array("total" => "L"
												,"employee_id" => "R"
												,"total_transactions" => "L"
												,"transaction_date" => "G"
												,"total_amount" => "R"
												,"charge_code" => "L"
												,"charge_amount" => "R"
												//,"or_no" => "R"
												//,"or_date" => "C"
												,"remarks" => "L")
										,array("total" => 5
												,"employee_id" => 4
												,"total_transactions" => 11
												,"transaction_date" => 5
												,"total_amount" => 4
												,"charge_code" => 4
												,"charge_amount" => 5
												//,"or_no" => 5
												//,"or_date" => 5
												,"remarks" => 9));
				
				//20100809 add for closing of capcon
				if ($this->transaction_code=='WDWL' || $this->transaction_code=='CLSE')
				{
					$bank_Y = 0;
					$bank_N = 0;
					$data = $this->tcaptranheader_model->getWithdrawalBankTransfer();
					foreach($data['list'] as $dat){
						if ($dat['bank_transfer']=='N')
							$bank_N = $dat['amount'];
						if ($dat['bank_transfer']=='Y')
							$bank_Y = $dat['amount'];	
					}
					$objPdf->Ln(.25);
					$objPdf->SetFont('Arial','B',9);
					$objPdf->Cell(30*$this->row_height,$this->row_height,"Total Amount - Bank Transfer:              ".number_format($bank_Y,2,'.',','),"","","L",false);
					$objPdf->Ln();	
					$objPdf->Cell(30*$this->row_height,$this->row_height,"Total Amount - non-Bank Transfer:      ".number_format($bank_N,2,'.',','),"","","L",false);				
				}
				
				$objPdf->Ln(.25);	
				$i++;
			}
		}
		
		if ($count!=20) { 
			$objPdf->writeFooter();
		}
		//exit();	
		$objPdf->Output($this->report_title."_".date("YmdHis"));	
	}
	
	/**
	 * @desc Sets the data
	 */
	function setTableData($report_type) 
	{	
		if ($report_type=='3') {
			$this->report_title = "Capital Contribution BMB Audit Trail";
			$this->transaction_list = $this->readBMBAuditTrail();
			if (count($this->transaction_list['list'])==0)
				return true;
			$this->list = $this->getData($this->transaction_list['list']);
		}
		else if ($report_type == '2') {
			$this->report_title = "Capital Contribution Audit Trail";
			$this->transaction_code_list = $this->readCapConCodesAuditTrail();	
			if (count($this->transaction_code_list)==0)
				return true;
		}
		else {
			$this->report_title = "Capital Contribution Prooflist";		
			$this->transaction_code_list = $this->readCapConCodesProoflist();
			if (count($this->transaction_code_list)==0)
				return true;	
		}
	}
	
	/**
	 * @desc Gets the data
	 */
	function getData($transaction_list) 
	{
		$this->transaction_amount = 0;
		$this->charge_amount = 0;
		
		$list = array();
		
		foreach ($transaction_list AS $data) 
		{	
			$mem_data = $this->member_model->getMemberInfo($data['employee_id'], array('last_name','first_name', 'middle_name'));
	
			$last_name = $mem_data['last_name'];
			$first_name = $mem_data['first_name'];	
			$middle_name = substr($mem_data['middle_name'],-(strlen($mem_data['middle_name'])),1).".";	
			
			if ($this->report_type=='3')
			{
				$list[] = array("transaction_no"=>" ".$data["transaction_no"]
								 ,"transaction_code"=>$data["transaction_code"]
								 ,"employee_id"=>" ".$data["employee_id"]
								 ,"employee_name"=>$last_name.", ".$first_name." ".$middle_name
								 ,"transaction_amount"=>number_format($data["transaction_amount"],2,'.',','));
			}
			else 
			{	
				$transaction_date = $this->formatDateYYYYMMDDToMMDDYYYY($data['transaction_date']);
				$or_date = $this->formatDateYYYYMMDDToMMDDYYYY($data['or_date']);
				$list[] = array("transaction_no"=>" ".$data["transaction_no"]
							 ,"employee_id"=>" ".$data["employee_id"]
							 ,"employee_name"=>$last_name.", ".$first_name." ".$middle_name
							 ,"transaction_date"=>$transaction_date
							 ,"transaction_amount"=>number_format($data["transaction_amount"],2,'.',',')
							 ,"charge_code"=>$data["charge_code"]
							 ,"charge_amount"=>number_format($data["amount"],2,'.',',')
							 //,"or_no"=>$data["or_no"]
							 //,"or_date"=>$or_date
							 ,"remarks"=>$data["remarks"]);		
				$this->charge_amount = $this->charge_amount + $data["amount"];	 			  	
			}				 
			$this->transaction_amount = $this->transaction_amount + $data["transaction_amount"];			
			
		}	
		return $list;
	}
	
	function formatDateYYYYMMDDToMMDDYYYY($date)
	{
		if ($date=="" || $date==null)
			$date = "";
		else	
			$date = substr($date, 4, 2) ."/".substr($date, 6, 2) ."/".  substr($date, 0, 4);
	
		return $date;
	}
	
}

?>