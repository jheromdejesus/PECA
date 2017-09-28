<?php

/* Location: ./CodeIgniter/application/controllers/report_actsummary.php */

class Report_actsummary extends Asi_Controller 
{
	var $file_type;		//1 - excel ; 2 - pdf
	var $date;
	var $account_no;
	var $account_name;
	
	var $report_title;
	var $report_date;
	var $report_id;
	var $report_type; //1 - posted ; 2 - unposted
	
	var $list;	

	function Report_actsummary() 
	{
		parent::Asi_Controller();
		$this->load->model('account_model');
		$this->load->library('asi_pdf_ext');
		$this->load->library('asi_excel_ext');
	}
	
	function index() 
	{	
		  /*$_REQUEST = array( 'file_type' => '2'
							,'report_date' => '10/31/2010'
							//,'report_date' => '03/19/2008'
							,'account_no' => '1010'
							,'report_type' => '1'
							);  */				
		$this->file_type = $_REQUEST['file_type'];
		$this->date =  date('Ymd', strtotime($_REQUEST['report_date']));
		$this->account_no = $_REQUEST['account_no'];
		
		/* $data = $this->account_model->getAccountInfo($this->account_no,array('account_group'));
		$this->account_group = $data['account_group']; */
		
		$this->report_date = date('F j, Y', strtotime($_REQUEST['report_date']));
		$this->report_id = 'F0004';
		$this->report_type = $_REQUEST['report_type'];;
		
		$result = $this->setTableData();	
		$this->list = $this->getData($this->list);
		
		if ($result == true) {
			echo("{'success':false,'msg':'No records found.','error_code':'19'}");	
		}
		else {
			if ($this->file_type=='1') $this->printExcel();
			else $this->printPDF();
		}		
	}

	/**
	 * @descTo retrieve all active companies.
	 * @return array
	 */
	function read()
	{
		$param['ml.accounting_period LIKE'] = substr($this->date, 0, 6).'%';
		$param['ml.account_no'] = $this->account_no;
		$posted = $this->report_type=='1';
		if($posted){
			$data = $this->account_model->getAccountSummary(
				$param,
				array( 'ml.account_no AS account_no' 										
					,'ra.account_name AS account_name'								
					,"DATE_FORMAT(ml.accounting_period,'%m/%d/%Y') AS accounting_period"
					//[start] 20111228 modified by asi 466 on issue#4 google shared doc					
					,'ROUND(ml.beginning_balance,2) AS ledger_amount'
					//[end] 20111228 modified by asi 466 on issue#4 google shared doc
					,'Journal.journal_no AS journal_no'								
					,'Journal.debit_credit AS debit_credit'		
					//[start] 20111228 modified by asi 466 on issue#4 google shared doc
					,'ROUND(Journal.amount,2) AS journal_amount'
					//[end] 20111228 modified by asi 466 on issue#4 google shared doc
					,'Journal.particulars AS particulars'								
					,"DATE_FORMAT(Journal.transaction_date,'%m/%d/%Y') AS transaction_date"),
				'Journal.transaction_date, Journal.journal_no'
				,$this->date
				,$this->account_no
				,$posted);
		}else{
			$data = $this->account_model->getAccountSummary(
				$param,
				array( 'ml.account_no AS account_no' 										
					,'ra.account_name AS account_name'								
					,"DATE_FORMAT(ml.accounting_period,'%m/%d/%Y') AS accounting_period"								
					,'ROUND(ml.beginning_balance,2) AS ledger_amount'								
					,'Journal.journal_no AS journal_no'								
					,'Journal.debit_credit AS debit_credit'								
					,'ROUND(Journal.amount,2) AS journal_amount'								
					,'Journal.particulars AS particulars'								
					,"DATE_FORMAT(Journal.transaction_date,'%m/%d/%Y') AS transaction_date"),
				'Journal.transaction_date, Journal.journal_no'
				,$this->date
				,$this->account_no
				,$posted);
		}
		return $data['list'];
	}
	
	/**
	 * @desc Displays the report in Excel File
	 */
	function printExcel()
	{
		$row_start = 6;								 		//row after the header		
		$objExcel = new Asi_excel_ext("portrait");
		$objExcel->init(44,8,.7,false);		
		$objExcel->writeHeaderInfo($this->report_title, "For the period ".$this->report_date, $this->report_id);
		
		$objExcel->writeSubheader(
								array("Account No. - Account Name:" => 37		//no of cells in actual excel
									 )
								,0
								,$row_start
								,12.75
								,array("Account No. - Account Name:"  => "left"		
									 )
								,true
								,$row_start++);		
		$objExcel->_setCell('H7', $this->account_name);
		$row_start++;
		$objExcel->writeSubheader(
								array("Date" => 5		//no of cells in actual excel
									,"JV No." => 5
									,"Transaction" => 12 
									,"Debit" => 5
									,"Credit" => 5
									,"Balance" => 5
									 )
								,0
								,$row_start
								,12.75
								,array("Date" => "center"		//no of cells in actual excel
									,"JV No." => "right"
									,"Transaction" => "left" 
									,"Debit" => "right"
									,"Credit" => "right"
									,"Balance" => "right"
									 )
								,true
								,$row_start++);	
		$objExcel->writeTableData(
								$this->list
								,array("date" => "center"		//no of cells in actual excel
									,"jv_no" => "right"
									,"transaction" => "left" 
									,"debit" => "right"
									,"credit" => "right"
									,"balance" => "right"
									 )
								,array(
									 "date"=>"s"
									,"jv_no"=>"s"
									,"transaction"=>"s"
									,"debit"=>"#,##0.00"
									,"credit"=>"#,##0.00"
									,"balance" => "#,##0.00"
								)
								,$row_start);	
								
		if($this->debit < 0){
			$debit_total = "(".number_format(abs($this->debit),2,'.',',').")";
		}
		else{
			$debit_total = number_format($this->debit,2,'.',',');
		}
		
		if($this->credit < 0){
			$credit_total = "(".number_format(abs($this->credit),2,'.',',').")";
		}
		else{
			$credit_total = number_format($this->credit,2,'.',',');
		}
			
		$objExcel->writeTotals(array("total" => "TOTAL:"
									,"jv_no" => ""
									,"transaction" => ""
									,"debit" => $debit_total
									,"credit" => $credit_total
									,"balance" => "")
								  ,array("total" => "left"		//no of cells in actual excel
										,"jv_no" => "right"
										,"transaction" => "left" 
										,"debit" => "right"
										,"credit" => "right"
										,"balance" => "right"
									)
								   ,array(
										 "total"=>"s"
										,"jv_no"=>"s"
										,"transaction"=>"s"
										,"debit"=>"#,##0.00"
										,"credit"=>"#,##0.00"
										,"balance" => "#,##0.00"
									)
									,'A' 	  					//first column name:used for border
									,'AK');	  					//e
								
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
		$objPdf = new Asi_pdf_ext();
		$objPdf->init("portrait", .7);	
		$objPdf->subheader_accountsummary_every_page = true;
		$objPdf->account_name = $this->account_name;
		$objPdf->writeHeaderInfo($this->report_title, "For the period ".$this->report_date, $this->report_id);
		$objPdf->writeSubheader(array("Account No. - Account Name:" => 36		//no of cells in actual excel
									 )
								,array("Account No. - Account Name:"  => "L"));	
		$objPdf->_setCell(7, '');
		$objPdf->_setCell(33, $this->account_name);
		$objPdf->Ln();
		$objPdf->writeSubheader(array("Date" => 4		//no of cells in actual excel
									,"JV No." => 4
									,"Transaction" => 13 
									,"Debit" => 5
									,"Credit" => 5
									,"Balance" => 5
									 )
								,array("Date" => "C"		//no of cells in actual excel
									,"JV No." => "R"
									,"Transaction" => "L" 
									,"Debit" => "R"
									,"Credit" => "R"
									,"Balance" => "R"
									 ));	
		$objPdf->writeTableData($this->list
							   ,array("date" => 4		//no of cells in actual excel
									,"jv_no" => 4
									,"transaction" => 13 
									,"debit" => 5
									,"credit" => 5
									,"balance" => 5
									 )
  							   ,array("date" => "C"		//no of cells in actual excel
									,"jv_no" => "R"
									,"transaction" => "L" 
									,"debit" => "R"
									,"credit" => "R"
									,"balance" => "R"
									 )
								,.7
								,null
								,50);	
								
		if ($this->with_total){
			if($this->debit < 0){
				$debit_total = "(".number_format(abs($this->debit),2,'.',',').")";
			}
			else{
				$debit_total = number_format($this->debit,2,'.',',');
			}
			
			if($this->credit < 0){
				$credit_total = "(".number_format(abs($this->credit),2,'.',',').")";
			}
			else{
				$credit_total = number_format($this->credit,2,'.',',');
			}
			
			$objPdf->writeTotals(array("date" => "TOTAL:"		//no of cells in actual excel
									,"jv_no" => ""
									,"transaction" => "" 
									,"debit" => $debit_total
									,"credit" => $credit_total
									,"balance" => "")
								,array("date" => "L"		//no of cells in actual excel
									,"jv_no" => "R"
									,"transaction" => "L" 
									,"debit" => "R"
									,"credit" => "R"
									,"balance" => "R"
									 )
								,array("date" => 4		//no of cells in actual excel
									,"jv_no" => 4
									,"transaction" => 13 
									,"debit" => 5
									,"credit" => 5
									,"balance" => 5
									 ));	
		}							
									 
		$objPdf->Ln(.25);	
		$objPdf->writeFooter();
		$objPdf->Output($this->report_title."_".date("YmdHis"));
	}
	
	/**
	 * @desc Sets the data
	 */
	function setTableData() 
	{	
		if($this->report_type == '1')
			$this->report_title = "Account Summary Posted";
		else
			$this->report_title = "Account Summary Unposted";
		$this->list = $this->read();
		if (count($this->list)==0)
			return true;
	}
	
	/**
	 * @desc Gets the data
	 */
	function getData($list) 
	{
		$new_list=array();
		$first_visit = true;
		$balance = 0;
		$this->debit = 0;
		$this->credit = 0;
		$this->with_total = true;
		
		foreach ($list AS $data) 
		{	
			if($first_visit){
				$first_visit=false;
				$balance = $data['ledger_amount'];
				if ($balance >= 0){
					$debit = $this->processNum($balance, false);
					$credit = '';
					// $this->debit = $balance;
				} else{
					$debit = '';
					$credit = $this->processNum($balance, false);
					// $this->credit = $balance;
				}
				$new_list[] = array(
						 "date"=>$data["transaction_date"]
						,"jv_no"=> ""
						,"transaction"=>"BEGINNING BALANCE"
						,"debit"=> $debit
						,"credit"=> $credit
						,"balance"=>$this->processNum($balance, false)
					  );	
				$this->account_name = " ".$data["account_no"].' - '.$data["account_name"];
				if($data['journal_no']===null){
					$this->with_total = false;
					break;
				}
			}
			$debit = '';
			$credit = '';
			
			$deb = 0;
			$cred = 0;
			
			if($data['debit_credit'] == 'D'){
				$balance+=$data['journal_amount'];
				$debit = $this->processNum($data['journal_amount']);
				$deb = $data['journal_amount'];
			} else{
				$balance-=$data['journal_amount'];
				$credit = $this->processNum($data['journal_amount']);
				$cred = $data['journal_amount'];
			}
			
			$new_list[] = array(
						 "date"=>$data["transaction_date"]
						,"jv_no"=>" ".$data["journal_no"]
						,"transaction"=>$data["particulars"]
						,"debit"=>$debit
						,"credit"=>$credit
						,"balance"=>$this->processNum($balance, false)
					  );
					  
			$this->debit += $deb;
			$this->credit += $cred;
		}	
		
		return $new_list;
	}
	
	function processNum($num, $put_parenthesis = true){
		/* if ($this->account_group=='A' || $this->account_group=='E')
		{
			if($num<0){
				return '('.number_format(abs($num),2,'.',',').')';
			}
			else
				return number_format($num,2,'.',',');
		}
		else if ($this->account_group=='L' || $this->account_group=='C' || $this->account_group=='I')
		{
			if($num>0){
				return '('.number_format(abs($num),2,'.',',').')';
			}
			else
				return number_format(abs($num),2,'.',',');
		} */
		//return number_format(abs($num),2,'.',',');
		
		//put parenthesis on negative nos. if needed
		if($put_parenthesis){
			if($num < 0){
				return "(".number_format(abs($num),2,'.',',').")";
			}
			else{
				return number_format($num,2,'.',',');
			}
		}
		else{
			return number_format(abs($num),2,'.',',');
		}
	}
}

?>