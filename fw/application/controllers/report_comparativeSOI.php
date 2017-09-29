<?php

/* Location: ./CodeIgniter/application/controllers/report_comparativeSOI.php 
 * */


class Report_comparativeSOI extends Asi_Controller 
{
	var $file_type;		//1 - excel ; 2 - pdf

	var $report_from;
	var $report_to;
	var $report_title;
	var $list;	
	var $row_height = .18; 	

	function Report_comparativeSOI() 
	{
		parent::Asi_Controller();
		$this->load->helper('url');
		$this->load->model('ledger_model');
		$this->load->library('constants');
		$this->load->library('asi_model');
		$this->load->library('asi_pdf_ext');
		$this->load->library('asi_excel_ext');
		$this->load->helper('date');	
	}
	
	function index() 
	{			
		/* $_REQUEST['file_type'] = '1';
		$_REQUEST['from_date'] = '01/01/2009';
		$_REQUEST['to_date'] = '12/01/2009';   */
		 
		$this->file_type = $_REQUEST['file_type'];
		$this->report_from = date("Ymd", strtotime($_REQUEST['from_date']));
		$this->report_to = date("Ymd", strtotime($_REQUEST['to_date']));
		
		if (substr($this->report_from,0,-4)==substr($this->report_to,0,-4)){	
			$result = $this->setTableData();	
			if ($result == true) {
				echo("{'success':false,'msg':'No records found.','error_code':'19'}");	
			}
			else {
				if ($this->file_type=='1') $this->printExcel();
				else $this->printPDF();
			}
		}
		else {
			echo("{'success':false,'msg':'Year should be the same.','error_code':'152'}");	
		}		
	}

	/**
	 * @desc To retrieve income/expenses based on the specified period.
	 */
	function readComparativeSI($period)
	{
		$data = $this->ledger_model->retrieveComparativeSIE($period,'I');
		
		return $data['list'];
	}
	
	/**
	 * @desc To retrieve income/expenses based on the specified period.
	 */
	function readComparativeSE($period)
	{
		$data = $this->ledger_model->retrieveComparativeSIE($period,'E');
		
		return $data['list'];
	}
	
	function readAccountList($group)
	{
		$data = $this->ledger_model->retrieveAccountListSOI($this->report_from,$this->report_to,$group);
		return $data['list'];
	}
	
	/**
	 * @desc Displays the report in Excel File
	 */
	function printExcel()
	{
		$row_start = 6;
		$count = $row_start;
		$objExcel = new Asi_excel_ext("landscape");
		$objExcel->init(50,5,.5,false);		
		$objExcel->writeHeaderInfo($this->report_title, date("F j, Y",strtotime($this->report_from))." to ".date("F j,Y",strtotime($this->report_to)), "F0008");
		$objExcel->writeSubheader($this->excel_subheader
							   ,0
							   ,$row_start
							   ,12.75
							   ,$this->excel_aligment
							   ,true
							   ,$row_start);
							   
		$objExcel->_setCell('A7','INCOME',true,7);
		
		$row_start = 8;
		$this->quartot_rowstart = $row_start;
		
		$this->list = $this->getExcelData('I');
		
		$form_count = count($this->list)+$row_start;
		$in_rowstart = $row_start;
		$in_rowend	= $form_count-1;
		$quart_row = $in_rowend+1;
		
		$incometot_row = $form_count;
		
		$objExcel->writeTableData($this->list
								,array("account_name" => "left"
									    ,"account_no" => "right"
									    ,"January"=>"right"
										,"February"=>"right"
										,"March"=>"right"
										,"1q"=>"right"
										,"April"=>"right"
										,"May"=>"right"
										,"June"=>"right"
										,"2q"=>"right"
										,"July"=>"right"
										,"August"=>"right"
										,"September"=>"right"
										,"3q"=>"right"
										,"October"=>"right"
										,"November"=>"right"
										,"December"=>"right"
										,"4q"=>"right")
								,array("account_name" => "s"
									    ,"account_no" => "s"
									    ,"January"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"February"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"March"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"1q"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"April"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"May"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"June"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"2q"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"July"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"August"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"September"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"3q"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"October"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"November"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"December"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"4q"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)")		
								,$row_start);
		
		//to fix error in excel there is no income
		if($in_rowstart > $in_rowend){
			$objExcel->writeDoubleTotals(array("account_name" => "GROSS INCOME"
											,"account_no" => ""
											,"January"=>"0"
											,"February"=>"0"
											,"March"=>"0"
											,"1q"=>"0"
											,"April"=>"0"
											,"May"=>"0"
											,"June"=>"0"
											,"2q"=>"0"
											,"July"=>"0"
											,"August"=>"0"
											,"September"=>"0"
											,"3q"=>"0"
											,"October"=>"0"
											,"November"=>"0"
											,"December"=>"0"
											,"4q"=>"0")
									,array("account_name" => "left"
											,"account_no" => "right"
											,"January"=>"right"
											,"February"=>"right"
											,"March"=>"right"
											,"1q"=>"right"
											,"April"=>"right"
											,"May"=>"right"
											,"June"=>"right"
											,"2q"=>"right"
											,"July"=>"right"
											,"August"=>"right"
											,"September"=>"right"
											,"3q"=>"right"
											,"October"=>"right"
											,"November"=>"right"
											,"December"=>"right"
											,"4q"=>"right")
									,array("account_name" => "s"
											,"account_no" => "s"
											,"January"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
											,"February"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
											,"March"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
											,"1q"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
											,"April"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
											,"May"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
											,"June"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
											,"2q"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
											,"July"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
											,"August"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
											,"September"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
											,"3q"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
											,"October"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
											,"November"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
											,"December"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
											,"4q"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)")		
									,'A'
									,'BW'
									,'thick'); 	
		}
		else{		
			$objExcel->writeDoubleTotals(array("account_name" => "GROSS INCOME"
											,"account_no" => ""
											,"January"=>"=SUM(L$in_rowstart:L$in_rowend)"
											,"February"=>"=SUM(P$in_rowstart:P$in_rowend)"
											,"March"=>"=SUM(T$in_rowstart:T$in_rowend)"
											,"1q"=>"=SUM(L$quart_row:T$quart_row)"
											,"April"=>"=SUM(AB$in_rowstart:AB$in_rowend)"
											,"May"=>"=SUM(AF$in_rowstart:AF$in_rowend)"
											,"June"=>"=SUM(AJ$in_rowstart:AJ$in_rowend)"
											,"2q"=>"=SUM(AB$quart_row:AJ$quart_row)"
											,"July"=>"=SUM(AR$in_rowstart:AR$in_rowend)"
											,"August"=>"=SUM(AV$in_rowstart:AV$in_rowend)"
											,"September"=>"=SUM(AZ$in_rowstart:AZ$in_rowend)"
											,"3q"=>"=SUM(AR$quart_row:AZ$quart_row)"
											,"October"=>"=SUM(BH$in_rowstart:BH$in_rowend)"
											,"November"=>"=SUM(BL$in_rowstart:BL$in_rowend)"
											,"December"=>"=SUM(BP$in_rowstart:BP$in_rowend)"
											,"4q"=>"=SUM(BH$quart_row:BP$quart_row)")
									,array("account_name" => "left"
											,"account_no" => "right"
											,"January"=>"right"
											,"February"=>"right"
											,"March"=>"right"
											,"1q"=>"right"
											,"April"=>"right"
											,"May"=>"right"
											,"June"=>"right"
											,"2q"=>"right"
											,"July"=>"right"
											,"August"=>"right"
											,"September"=>"right"
											,"3q"=>"right"
											,"October"=>"right"
											,"November"=>"right"
											,"December"=>"right"
											,"4q"=>"right")
									,array("account_name" => "s"
											,"account_no" => "s"
											,"January"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
											,"February"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
											,"March"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
											,"1q"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
											,"April"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
											,"May"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
											,"June"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
											,"2q"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
											,"July"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
											,"August"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
											,"September"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
											,"3q"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
											,"October"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
											,"November"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
											,"December"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
											,"4q"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)")		
									,'A'
									,'BW'
									,'thick'); 					
		}

		$count = $row_start + count($this->list) + 1;
		$objExcel->_setCell('A'.$count,'Less: OPERATING EXPENSES:',true,7);
		$row_start = $count+1;
		$this->quartot_rowstart = $row_start;
		
		$this->list = $this->getExcelData('E');		
		
		$form_count = count($this->list)+$row_start;
		$in_rowstart = $row_start;
		$in_rowend	= $form_count-1;							
		$quart_row = $in_rowend+1;
		
		$expensetot_row = $form_count;
	
		$objExcel->writeTableData($this->list
								,array("account_name" => "left"
									    ,"account_no" => "right"
									    ,"January"=>"right"
										,"February"=>"right"
										,"March"=>"right"
										,"1q"=>"right"
										,"April"=>"right"
										,"May"=>"right"
										,"June"=>"right"
										,"2q"=>"right"
										,"July"=>"right"
										,"August"=>"right"
										,"September"=>"right"
										,"3q"=>"right"
										,"October"=>"right"
										,"November"=>"right"
										,"December"=>"right"
										,"4q"=>"right")
								,array("account_name" => "s"
									    ,"account_no" => "s"
									    ,"January"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"February"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"March"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"1q"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"April"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"May"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"June"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"2q"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"July"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"August"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"September"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"3q"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"October"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"November"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"December"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"4q"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)")		
								,$row_start);
		
		//to fix error in excel there are no expenses
		if($in_rowstart > $in_rowend){ 
			$objExcel->writeTotals(array("account_name" => "TOTAL OPERATING EXPENSES"
									    ,"account_no" => ""
										,"January"=>"0"
									    ,"February"=>"0"
									    ,"March"=>"0"
										,"1q"=>"0"
										,"April"=>"0"
										,"May"=>"0"
										,"June"=>"0"
										,"2q"=>"0"
										,"July"=>"0"
										,"August"=>"0"
										,"September"=>"0"
										,"3q"=>"0"
										,"October"=>"0"
										,"November"=>"0"
										,"December"=>"0"
										,"4q"=>"0")
								,array("account_name" => "left"
									    ,"account_no" => "right"
									    ,"January"=>"right"
										,"February"=>"right"
										,"March"=>"right"
										,"1q"=>"right"
										,"April"=>"right"
										,"May"=>"right"
										,"June"=>"right"
										,"2q"=>"right"
										,"July"=>"right"
										,"August"=>"right"
										,"September"=>"right"
										,"3q"=>"right"
										,"October"=>"right"
										,"November"=>"right"
										,"December"=>"right"
										,"4q"=>"right")
								,array("account_name" => "s"
									    ,"account_no" => "s"
									    ,"January"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"February"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"March"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"1q"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"April"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"May"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"June"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"2q"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"July"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"August"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"September"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"3q"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"October"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"November"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"December"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"4q"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)")		
								,'A'
								,'BW');
		}
		else{
			$objExcel->writeTotals(array("account_name" => "TOTAL OPERATING EXPENSES"
									    ,"account_no" => ""
										,"January"=>"=SUM(L$in_rowstart:L$in_rowend)"
									    ,"February"=>"=SUM(P$in_rowstart:P$in_rowend)"
									    ,"March"=>"=SUM(T$in_rowstart:T$in_rowend)"
										,"1q"=>"=SUM(L$quart_row:T$quart_row)"
										,"April"=>"=SUM(AB$in_rowstart:AB$in_rowend)"
										,"May"=>"=SUM(AF$in_rowstart:AF$in_rowend)"
										,"June"=>"=SUM(AJ$in_rowstart:AJ$in_rowend)"
										,"2q"=>"=SUM(AB$quart_row:AJ$quart_row)"
										,"July"=>"=SUM(AR$in_rowstart:AR$in_rowend)"
										,"August"=>"=SUM(AV$in_rowstart:AV$in_rowend)"
										,"September"=>"=SUM(AZ$in_rowstart:AZ$in_rowend)"
										,"3q"=>"=SUM(AR$quart_row:AZ$quart_row)"
										,"October"=>"=SUM(BH$in_rowstart:BH$in_rowend)"
										,"November"=>"=SUM(BL$in_rowstart:BL$in_rowend)"
										,"December"=>"=SUM(BP$in_rowstart:BP$in_rowend)"
										,"4q"=>"=SUM(BH$quart_row:BP$quart_row)")
								,array("account_name" => "left"
									    ,"account_no" => "right"
									    ,"January"=>"right"
										,"February"=>"right"
										,"March"=>"right"
										,"1q"=>"right"
										,"April"=>"right"
										,"May"=>"right"
										,"June"=>"right"
										,"2q"=>"right"
										,"July"=>"right"
										,"August"=>"right"
										,"September"=>"right"
										,"3q"=>"right"
										,"October"=>"right"
										,"November"=>"right"
										,"December"=>"right"
										,"4q"=>"right")
								,array("account_name" => "s"
									    ,"account_no" => "s"
									    ,"January"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"February"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"March"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"1q"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"April"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"May"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"June"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"2q"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"July"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"August"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"September"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"3q"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"October"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"November"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"December"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"4q"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)")		
								,'A'
								,'BW');
		}
		
		$objExcel->writeDoubleTotals(array("account_name" => "NET INCOME"
									    ,"account_no" => ""
										,"January"=>"=L$incometot_row - L$expensetot_row"
									    ,"February"=>"=P$incometot_row  - P$expensetot_row"
									    ,"March"=>"=T$incometot_row  - T$expensetot_row"
										,"1q"=>"=X$incometot_row  - X$expensetot_row"
										,"April"=>"=AB$incometot_row  - AB$expensetot_row"
										,"May"=>"=AF$incometot_row  - AF$expensetot_row"
										,"June"=>"=AJ$incometot_row  - AJ$expensetot_row"
										,"2q"=>"=AN$incometot_row  - AN$expensetot_row"
										,"July"=>"=AR$incometot_row  - AR$expensetot_row"
										,"August"=>"=AV$incometot_row  - AV$expensetot_row"
										,"September"=>"=AZ$incometot_row  - AZ$expensetot_row"
										,"3q"=>"=BD$incometot_row  - BD$expensetot_row"
										,"October"=>"=BH$incometot_row  - BH$expensetot_row"
										,"November"=>"=BL$incometot_row  - BL$expensetot_row"
										,"December"=>"=BP$incometot_row  - BP$expensetot_row"
										,"4q"=>"=BT$incometot_row  - BT$expensetot_row")
								,array("account_name" => "left"
									    ,"account_no" => "right"
									    ,"January"=>"right"
										,"February"=>"right"
										,"March"=>"right"
										,"1q"=>"right"
										,"April"=>"right"
										,"May"=>"right"
										,"June"=>"right"
										,"2q"=>"right"
										,"July"=>"right"
										,"August"=>"right"
										,"September"=>"right"
										,"3q"=>"right"
										,"October"=>"right"
										,"November"=>"right"
										,"December"=>"right"
										,"4q"=>"right")
								,array("account_name" => "s"
									    ,"account_no" => "s"
									    ,"January"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"February"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"March"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"1q"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"April"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"May"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"June"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"2q"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"July"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"August"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"September"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"3q"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"October"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"November"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"December"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)"
										,"4q"=>"_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)")		
								,'A'
								,'BW'
								,'thick'); 
		$count = $row_start + count($this->list) + 2;
		$objExcel->_setCell('A'.$count,'KEY NOTES for 2009',true,7); 
		
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
		$objPdf->init("landscape",.5);		
		$objPdf->writeHeaderInfo($this->report_title, date("F j, Y",strtotime($this->report_from))." to ".date("F j,Y",strtotime($this->report_to)),"F0008");
		
		$objPdf->writeSubheader($this->pdf_subheader1
							   ,$this->pdf_aligment1
							   ,.5);
		
		$objPdf->SetFont('Arial','B',7);
		$objPdf->Cell(23*$this->row_height,$this->row_height,"INCOME","","","L",false);
		$objPdf->Ln();
	
		$this->getExcelData('I');
	
		$objPdf->writeTableData($this->pdf_list1
  								,array("account_name" => 10
									    ,"account_no" => 3
									    ,"January"=>5
									    ,"February"=>5
									    ,"March"=>5
										,"1q"=>5
										,"April"=>5
										,"May"=>5
										,"June"=>5
										,"2q"=>5)
								,array("account_name" => "L"
									    ,"account_no" => "R"
									    ,"January"=>"R"
										,"February"=>"R"
										,"March"=>"R"
										,"1q"=>"R"
										,"April"=>"R"
										,"May"=>"R"
										,"June"=>"R"
										,"2q"=>"R")
								,.5);
		
		$objPdf->writeTotals(array("account_name" => "GROSS INCOME"
									    ,"account_no" => ""
									    ,"January"=>$this->formatBalance($this->tot_jan,'I')
									    ,"February"=>$this->formatBalance($this->tot_feb,'I')
									    ,"March"=>$this->formatBalance($this->tot_mar,'I')
										,"1q"=>$this->formatBalance($this->tot_1q,'I')
										,"April"=>$this->formatBalance($this->tot_apr,'I')
										,"May"=>$this->formatBalance($this->tot_may,'I')
										,"June"=>$this->formatBalance($this->tot_jun,'I')
										,"2q"=>$this->formatBalance($this->tot_2q,'I'))
								,array("account_name" => "L"
									    ,"account_no" => "R"
									    ,"January"=>"R"
										,"February"=>"R"
										,"March"=>"R"
										,"1q"=>"R"
										,"April"=>"R"
										,"May"=>"R"
										,"June"=>"R"
										,"2q"=>"R")
								,array("account_name" => 10
									    ,"account_no" => 3
									    ,"January"=>5
									    ,"February"=>5
									    ,"March"=>5
										,"1q"=>5
										,"April"=>5
										,"May"=>5
										,"June"=>5
										,"2q"=>5)		
								,.5);	
		
		$objPdf->Ln();
		$y = $objPdf->GetY();
		$objPdf->Line(.5,$y, 10.03, $y);
		$objPdf->Ln(.02);
		$y = $objPdf->GetY();
		$objPdf->Line(.5,$y, 10.03, $y);
		$objPdf->Ln(.02);
		$objPdf->SetFont('Arial','',7);
		$objPdf->Cell(23*$this->row_height,$this->row_height,"Less: OPERATING EXPENSES:","","","L",false);
		$objPdf->Ln();
		
		$this->getExcelData('E');
	
		$objPdf->writeTableData($this->pdf_list1
  								,array("account_name" => 10
									    ,"account_no" => 3
									    ,"January"=>5
									    ,"February"=>5
									    ,"March"=>5
										,"1q"=>5
										,"April"=>5
										,"May"=>5
										,"June"=>5
										,"2q"=>5)
								,array("account_name" => "L"
									    ,"account_no" => "R"
									    ,"January"=>"R"
										,"February"=>"R"
										,"March"=>"R"
										,"1q"=>"R"
										,"April"=>"R"
										,"May"=>"R"
										,"June"=>"R"
										,"2q"=>"R")
								,.5);
		
		$objPdf->writeTotals(array("account_name" => "TOTAL OPERATING EXPENSES"
									    ,"account_no" => ""
									    ,"January"=>$this->formatBalance($this->etot_jan,'E')
									    ,"February"=>$this->formatBalance($this->etot_feb,'E')
									    ,"March"=>$this->formatBalance($this->etot_mar,'E')
										,"1q"=>$this->formatBalance($this->etot_1q,'E')
										,"April"=>$this->formatBalance($this->etot_apr,'E')
										,"May"=>$this->formatBalance($this->etot_may,'E')
										,"June"=>$this->formatBalance($this->etot_jun,'E')
										,"2q"=>$this->formatBalance($this->etot_2q,'E'))
								,array("account_name" => "L"
									    ,"account_no" => "R"
									    ,"January"=>"R"
										,"February"=>"R"
										,"March"=>"R"
										,"1q"=>"R"
										,"April"=>"R"
										,"May"=>"R"
										,"June"=>"R"
										,"2q"=>"R")
								,array("account_name" => 10
									    ,"account_no" => 3
									    ,"January"=>5
									    ,"February"=>5
									    ,"March"=>5
										,"1q"=>5
										,"April"=>5
										,"May"=>5
										,"June"=>5
										,"2q"=>5)		
								,.5);	
		$objPdf->Ln();
		$objPdf->writeTotals(array("account_name" => "NET INCOME"
									    ,"account_no" => ""
									    ,"January"=>$this->formatBalance(($this->tot_jan + $this->etot_jan),'I')
									    ,"February"=>$this->formatBalance(($this->tot_feb + $this->etot_feb),'I')
									    ,"March"=>$this->formatBalance(($this->tot_mar + $this->etot_mar),'I')
										,"1q"=>$this->formatBalance(($this->tot_1q + $this->etot_1q),'I')
										,"April"=>$this->formatBalance(($this->tot_apr + $this->etot_apr),'I')
										,"May"=>$this->formatBalance(($this->tot_may + $this->etot_may),'I')
										,"June"=>$this->formatBalance(($this->tot_jun + $this->etot_jun),'I')
										,"2q"=>$this->formatBalance(($this->tot_2q + $this->etot_2q),'I'))
								,array("account_name" => "L"
									    ,"account_no" => "R"
									    ,"January"=>"R"
										,"February"=>"R"
										,"March"=>"R"
										,"1q"=>"R"
										,"April"=>"R"
										,"May"=>"R"
										,"June"=>"R"
										,"2q"=>"R")
								,array("account_name" => 10
									    ,"account_no" => 3
									    ,"January"=>5
									    ,"February"=>5
									    ,"March"=>5
										,"1q"=>5
										,"April"=>5
										,"May"=>5
										,"June"=>5
										,"2q"=>5)		
								,.5);
		
		//july to december starts here
		$objPdf->i = 0;
		$objPdf->writeHeaderInfo($this->report_title, date("F j, Y",strtotime($this->report_from))." to ".date("F j,Y",strtotime($this->report_to)),"F0008");
		$objPdf->writeSubheader($this->pdf_subheader2
							   ,$this->pdf_aligment2
							   ,.5);
		
		$objPdf->SetFont('Arial','B',7);
		$objPdf->Cell(23*$this->row_height,$this->row_height,"INCOME","","","L",false);
		$objPdf->Ln();
		
		$this->getExcelData('I');
		
		$objPdf->writeTableData($this->pdf_list2
  								,array("account_name" => 10
									    ,"account_no" => 3
									    ,"July"=>5
									    ,"August"=>5
									    ,"September"=>5
										,"3q"=>5
										,"October"=>5
										,"November"=>5
										,"December"=>5
										,"4q"=>5)
								,array("account_name" => "L"
									    ,"account_no" => "R"
									    ,"July"=>"R"
										,"August"=>"R"
										,"September"=>"R"
										,"3q"=>"R"
										,"October"=>"R"
										,"November"=>"R"
										,"December"=>"R"
										,"4q"=>"R")
								,.5);
		
		$objPdf->writeTotals(array("account_name" => "GROSS INCOME"
									    ,"account_no" => ""
									    ,"July"=>$this->formatBalance($this->tot_jul,'I')
									    ,"August"=>$this->formatBalance($this->tot_aug,'I')
									    ,"September"=>$this->formatBalance($this->tot_sep,'I')
										,"3q"=>$this->formatBalance($this->tot_3q,'I')
										,"October"=>$this->formatBalance($this->tot_oct,'I')
										,"November"=>$this->formatBalance($this->tot_nov,'I')
										,"December"=>$this->formatBalance($this->tot_dec,'I')
										,"4q"=>$this->formatBalance($this->tot_4q,'I'))
								,array("account_name" => "L"
									    ,"account_no" => "R"
									    ,"July"=>"R"
										,"August"=>"R"
										,"September"=>"R"
										,"3q"=>"R"
										,"October"=>"R"
										,"November"=>"R"
										,"December"=>"R"
										,"4q"=>"R")
								,array("account_name" => 10
									    ,"account_no" => 3
									    ,"July"=>5
									    ,"August"=>5
									    ,"September"=>5
										,"3q"=>5
										,"October"=>5
										,"November"=>5
										,"December"=>5
										,"4q"=>5)		
								,.5);	
		
		$objPdf->Ln();
		$y = $objPdf->GetY();
		$objPdf->Line(.5,$y, 10.03, $y);
		$objPdf->Ln(.02);
		$y = $objPdf->GetY();
		$objPdf->Line(.5,$y, 10.03, $y);
		$objPdf->Ln(.02);
		$objPdf->SetFont('Arial','',7);
		$objPdf->Cell(23*$this->row_height,$this->row_height,"Less: OPERATING EXPENSES:","","","L",false);
		$objPdf->Ln();
		
		$this->getExcelData('E');
		
		$objPdf->writeTableData($this->pdf_list2
  								,array("account_name" => 10
									    ,"account_no" => 3
									    ,"July"=>5
									    ,"August"=>5
									    ,"September"=>5
										,"3q"=>5
										,"October"=>5
										,"November"=>5
										,"December"=>5
										,"4q"=>5)
								,array("account_name" => "L"
									    ,"account_no" => "R"
									    ,"July"=>"R"
										,"August"=>"R"
										,"September"=>"R"
										,"3q"=>"R"
										,"October"=>"R"
										,"November"=>"R"
										,"December"=>"R"
										,"4q"=>"R")
								,.5);
		
		$objPdf->writeTotals(array("account_name" => "TOTAL OPERATING EXPENSES"
									    ,"account_no" => ""
									    ,"July"=>$this->formatBalance($this->etot_jul,'E')
									    ,"August"=>$this->formatBalance($this->etot_aug,'E')
									    ,"September"=>$this->formatBalance($this->etot_sep,'E')
										,"3q"=>$this->formatBalance($this->etot_3q,'E')
										,"October"=>$this->formatBalance($this->etot_oct,'E')
										,"November"=>$this->formatBalance($this->etot_nov,'E')
										,"December"=>$this->formatBalance($this->etot_dec,'E')
										,"4q"=>$this->formatBalance($this->etot_4q,'E'))
								,array("account_name" => "L"
									    ,"account_no" => "R"
									    ,"July"=>"R"
										,"August"=>"R"
										,"September"=>"R"
										,"3q"=>"R"
										,"October"=>"R"
										,"November"=>"R"
										,"December"=>"R"
										,"4q"=>"R")
								,array("account_name" => 10
									    ,"account_no" => 3
									    ,"July"=>5
									    ,"August"=>5
									    ,"September"=>5
										,"3q"=>5
										,"October"=>5
										,"November"=>5
										,"December"=>5
										,"4q"=>5)		
								,.5);	
		$objPdf->Ln();
		$objPdf->writeTotals(array("account_name" => "NET INCOME"
									    ,"account_no" => ""
									    ,"July"=>$this->formatBalance(($this->tot_jul + $this->etot_jul),'I')
									    ,"August"=>$this->formatBalance(($this->tot_aug + $this->etot_aug),'I')
									    ,"September"=>$this->formatBalance(($this->tot_sep + $this->etot_sep),'I')
										,"3q"=>$this->formatBalance(($this->tot_3q + $this->etot_3q),'I')
										,"October"=>$this->formatBalance(($this->tot_oct + $this->etot_oct),'I')
										,"November"=>$this->formatBalance(($this->tot_nov + $this->etot_nov),'I')
										,"December"=>$this->formatBalance(($this->tot_dec + $this->etot_dec),'I')
										,"4q"=>$this->formatBalance(($this->tot_4q + $this->etot_4q),'I'))
								,array("account_name" => "L"
									    ,"account_no" => "R"
									    ,"July"=>"R"
										,"August"=>"R"
										,"September"=>"R"
										,"3q"=>"R"
										,"October"=>"R"
										,"November"=>"R"
										,"December"=>"R"
										,"4q"=>"R")
								,array("account_name" => 10
									    ,"account_no" => 3
									    ,"July"=>5
									    ,"August"=>5
									    ,"September"=>5
										,"3q"=>5
										,"October"=>5
										,"November"=>5
										,"December"=>5
										,"4q"=>5)		
								,.5);
								
		$objPdf->Ln();
		$y = $objPdf->GetY();
		$objPdf->Line(.5,$y, 10.03, $y);
		$objPdf->Ln(.02);
		$y = $objPdf->GetY();
		$objPdf->Line(.5,$y, 10.03, $y);
		$objPdf->Ln(.02);
		$objPdf->SetFont('Arial','',7);
		$objPdf->Cell(23*$this->row_height,$this->row_height,"KEY NOTES for 2009","","","L",false);
		
		$objPdf->writeFooter();
		$objPdf->Output($this->report_title."_".date("YmdHis")); 						
	}
	
	/**
	 * @desc Sets the data
	 */
	function setTableData() 
	{	
		$count = 0;
		$this->report_title = "Comparative Income Statement";
		$this->pdf_subheader1 = array("   "=>10
								," "=>3
								,"January"=>5
								,"February"=>5
								,"March"=>5
								,"1Q Total"=>5
								,"April"=>5
								,"May"=>5
								,"June"=>5
								,"2Q Total"=>5);
		
		$this->pdf_subheader2 = array("   "=>10
								," "=>3
								,"July"=>5
								,"August"=>5
								,"September"=>5
								,"3Q Total"=>5
								,"October"=>5
								,"November"=>5
								,"December"=>5
								,"4Q Total"=>5);
								
		$this->excel_subheader = array("   "=>9
								," "=>2
								,"January"=>4
								,"February"=>4
								,"March"=>4
								,"1Q Total"=>4
								,"April"=>4
								,"May"=>4
								,"June"=>4
								,"2Q Total"=>4
								,"July"=>4
								,"August"=>4
								,"September"=>4
								,"3Q Total"=>4
								,"October"=>4
								,"November"=>4
								,"December"=>4
								,"4Q Total"=>4);	
								
		$this->pdf_aligment1 = array("   "=>"R"
								," "=>"R"
								,"January"=>"R"
								,"February"=>"R"
								,"March"=>"R"
								,"1Q Total"=>"R"
								,"April"=>"R"
								,"May"=>"R"
								,"June"=>"R"
								,"2Q Total"=>"R");
		
		$this->pdf_aligment2 = array("   "=>"R"
								," "=>"R"
								,"July"=>"R"
								,"August"=>"R"
								,"September"=>"R"
								,"3Q Total"=>"R"
								,"October"=>"R"
								,"November"=>"R"
								,"December"=>"R"
								,"4Q Total"=>"R");
								
		$this->excel_aligment = array("   "=>"right"
								," "=>"right"
								,"January"=>"right"
								,"February"=>"right"
								,"March"=>"right"
								,"1Q Total"=>"right"
								,"April"=>"right"
								,"May"=>"right"
								,"June"=>"right"
								,"2Q Total"=>"right"
								,"July"=>"right"
								,"August"=>"right"
								,"September"=>"right"
								,"3Q Total"=>"right"
								,"October"=>"right"
								,"November"=>"right"
								,"December"=>"right"
								,"4Q Total"=>"right");						
		
		$year = substr($this->report_from,0,-4);
		$from = substr($this->report_from,-4);
		$to = substr($this->report_to,-4);
	
		$start = $month = strtotime($this->report_from);
		$end = strtotime($this->report_to);
		
		while($month <= $end)
		{
			 $this->accperiod_list[] = date('Ymd',$month);
			 $month = strtotime("+1 month", $month);
		}
		
		$this->i_list = array();
		$this->e_list = array();
		
		$i_account_temp = $this->readAccountList('I');
		$e_account_temp = $this->readAccountList('E');
		$i_account = array();
		$e_account = array();
		
		foreach($i_account_temp AS $data){
			$i_account[$data['account_no']] = $data;
		}
		
		foreach($e_account_temp AS $data){
			$e_account[$data['account_no']] = $data;
		}
		
		foreach($this->accperiod_list AS $period)
		{
			/* if (count($this->readComparativeSI($period))>0){
				$this->i_list[date("F",strtotime($period))] = $this->readComparativeSI($period);
			}
			else $count++; */
			
			/* if (count($this->readComparativeSE($period))>0){
				$this->e_list[date("F",strtotime($period))] = $this->readComparativeSE($period);
			}
			else $count++; */
			
			//income
			$temp_arr = $this->readComparativeSI($period);
			$temp_arr2 = array();
			foreach($temp_arr AS $data){
				$temp_arr2[$data['account_no']] = $data;
			}
			if (count($temp_arr)>0){
				foreach($i_account AS $key=>$data){
					if (array_key_exists($key,$temp_arr2)){
						$this->i_list[date("F",strtotime($period))] = $temp_arr2;
					}
					else {
						$temp_arr2[$key] = array('account_no'=>$key
											,'account_name'=>$data['account_name']
											,'accounting_period'=>$period
											,'comparative_balance'=>0);
						$this->i_list[date("F",strtotime($period))] = $temp_arr2; 
					}
				}
			}
			else $count++;
			
			//expense
			$temp_arr = $this->readComparativeSE($period);
			$temp_arr2 = array();
			foreach($temp_arr AS $data){
				$temp_arr2[$data['account_no']] = $data;
			}
			if (count($temp_arr)>0){
				foreach($e_account AS $key=>$data){
					if (array_key_exists($key,$temp_arr2)){
						$this->e_list[date("F",strtotime($period))] = $temp_arr2;
					}
					else {
						$temp_arr2[$key] = array('account_no'=>$key
											,'account_name'=>$data['account_name']
											,'accounting_period'=>$period
											,'comparative_balance'=>0);
						$this->e_list[date("F",strtotime($period))] = $temp_arr2; 
					}
				}
			}
			else $count++;
		}
		
		if ($count==(count($this->accperiod_list)*2)) 
			return true;
		
	}
	
	/**
	 * @desc Gets the data
	 */
	function getExcelData($type) 
	{
		$transaction_amount = 0;
		$jan_comp = 0;
		$combal = 0;
		$new_list = array();
		$list = array();
		$this->pdf_list1 = array();
		$this->pdf_list2 = array();
		
		$jan_comp = array();
		$feb_comp = array();
		$mar_comp = array();
		$apr_comp = array();
		$may_comp = array();
		$jun_comp = array();
		$jul_comp = array();
		$aug_comp = array();
		$sep_comp = array();
		$oct_comp = array();
		$nov_comp = array();
		$dec_comp = array();

		$unjan_comp = array();
		$unfeb_comp = array();
		$unmar_comp = array();
		$unapr_comp = array();
		$unmay_comp = array();
		$unjun_comp = array();
		$unjul_comp = array();
		$unaug_comp = array();
		$unsep_comp = array();
		$unoct_comp = array();
		$unnov_comp = array();
		$undec_comp = array();
	
		$quart1_form = array();
		$quart2_form = array();
		$quart3_form = array();
		$quart4_form = array();
	
		if ($type=='I')
		{		
			$this->tot_jan = 0;
			$this->tot_feb = 0;
			$this->tot_mar = 0;
			$this->tot_1q = 0;
			$this->tot_apr = 0;
			$this->tot_may = 0;
			$this->tot_jun = 0;
			$this->tot_2q = 0;
			$this->tot_jul = 0;
			$this->tot_aug = 0;
			$this->tot_sep = 0;
			$this->tot_3q = 0;
			$this->tot_oct = 0;
			$this->tot_nov = 0;
			$this->tot_dec = 0;
			$this->tot_4q = 0;
		
			foreach($this->i_list AS $data){
				foreach($data AS $key2=>$data2){
					$jan_comp[$key2] = number_format(0,2,'.',',');
					$feb_comp[$key2] = number_format(0,2,'.',',');
					$mar_comp[$key2] = number_format(0,2,'.',',');
					$apr_comp[$key2] = number_format(0,2,'.',',');
					$may_comp[$key2] = number_format(0,2,'.',',');
					$jun_comp[$key2] = number_format(0,2,'.',',');
					$jul_comp[$key2] = number_format(0,2,'.',',');
					$aug_comp[$key2] = number_format(0,2,'.',',');
					$sep_comp[$key2] = number_format(0,2,'.',',');
					$oct_comp[$key2] = number_format(0,2,'.',',');
					$nov_comp[$key2] = number_format(0,2,'.',',');
					$dec_comp[$key2] = number_format(0,2,'.',',');
					
					$unjan_comp[$key2] = 0;
					$unfeb_comp[$key2] = 0;
					$unmar_comp[$key2] = 0;
					$unapr_comp[$key2] = 0;
					$unmay_comp[$key2] = 0;
					$unjun_comp[$key2] = 0;
					$unjul_comp[$key2] = 0;
					$unaug_comp[$key2] = 0;
					$unsep_comp[$key2] = 0;
					$unoct_comp[$key2] = 0;
					$unnov_comp[$key2] = 0;
					$undec_comp[$key2] = 0;
				}
			}
			
			foreach($this->i_list AS $key=>$data)
			{
				foreach($data AS $key2=>$data2)
				{
					$list[$key2] = array("account_name"=>"    ".$data2["account_name"]
										,"account_no"=>" ".$data2["account_no"]
										,"January"=>0
										,"February"=>0 
										,"March"=>0
										,"1q"=>""
										,"April"=>0
										,"May"=>0
										,"June"=>0
										,"2q"=>""
										,"July"=>0
										,"August"=>0
										,"September"=>0
										,"3q"=>""
										,"October"=>0
										,"November"=>0
										,"December"=>0
										,"4q"=>"");	
					
					if ($key=='January'){
						$jan_comp[$key2] = $this->formatBalance($data2['comparative_balance'],'I');
						$unjan_comp[$key2] = $data2['comparative_balance'];
						$list[$key2]["January"] = $jan_comp[$key2];
					}
					
					if ($key=='February'){
						$feb_comp[$key2] = $this->formatBalance($data2['comparative_balance'],'I');
						$unfeb_comp[$key2] = $data2['comparative_balance'];
						$list[$key2]["February"] = $feb_comp[$key2];
					}
					
					if ($key=='March'){
						$mar_comp[$key2] = $this->formatBalance($data2['comparative_balance'],'I');
						$unmar_comp[$key2] = $data2['comparative_balance'];
						$list[$key2]["March"] = $mar_comp[$key2];
					}
					
					if ($key=='April'){
						$apr_comp[$key2] = $this->formatBalance($data2['comparative_balance'],'I');
						$unapr_comp[$key2] = $data2['comparative_balance'];
						$list[$key2]["April"] = $apr_comp[$key2];
					}
					
					if ($key=='May'){
						$may_comp[$key2] = $this->formatBalance($data2['comparative_balance'],'I');
						$unmay_comp[$key2] = $data2['comparative_balance'];
						$list[$key2]["May"] = $may_comp[$key2];
					}
					
					if ($key=='June'){
						$jun_comp[$key2] = $this->formatBalance($data2['comparative_balance'],'I');
						$unjun_comp[$key2] = $data2['comparative_balance'];
						$list[$key2]["June"] = $jun_comp[$key2];
					}
					
					if ($key=='July'){
						$jul_comp[$key2] = $this->formatBalance($data2['comparative_balance'],'I');
						$unjul_comp[$key2] = $data2['comparative_balance'];
						$list[$key2]["July"] = $jul_comp[$key2];
					}
					
					if ($key=='August'){
						$aug_comp[$key2] = $this->formatBalance($data2['comparative_balance'],'I');
						$unaug_comp[$key2] = $data2['comparative_balance'];
						$list[$key2]["August"] = $aug_comp[$key2];
					}
					
					if ($key=='September'){
						$sep_comp[$key2] = $this->formatBalance($data2['comparative_balance'],'I');
						$unsep_comp[$key2] = $data2['comparative_balance'];
						$list[$key2]["September"] = $sep_comp[$key2];
					} 
					
					if ($key=='October'){
						$oct_comp[$key2] = $this->formatBalance($data2['comparative_balance'],'I');
						$unoct_comp[$key2] = $data2['comparative_balance'];
						$list[$key2]["October"] = $oct_comp[$key2];
					}
					
					if ($key=='November'){
						$nov_comp[$key2] = $this->formatBalance($data2['comparative_balance'],'I');
						$unnov_comp[$key2] = $data2['comparative_balance'];
						$list[$key2]["October"] = $nov_comp[$key2];
					}
					
					if ($key=='December'){
						$dec_comp[$key2] = $this->formatBalance($data2['comparative_balance'],'I');
						$undec_comp[$key2] = $data2['comparative_balance'];
						$list[$key2]["October"] = $dec_comp[$key2];
					}
				}
			}
		
			foreach($list AS $key=>$data){
				
				if ($this->file_type=='2')
				{
					$this->pdf_list1[$key] = array("account_name"=>"    ".$data["account_name"]
									,"account_no"=>" ".$data["account_no"]
									,"January"=>$jan_comp[$key]
									,"February"=>$feb_comp[$key]
									,"March"=>$mar_comp[$key]
									,"1q"=>$this->formatBalance(($unjan_comp[$key] + $unfeb_comp[$key] + $unmar_comp[$key]),'I')
									,"April"=>$apr_comp[$key]
									,"May"=>$may_comp[$key]
									,"June"=>$jun_comp[$key]
									,"2q"=>$this->formatBalance(($unapr_comp[$key] + $unmay_comp[$key] + $unjun_comp[$key]),'I'));
					$this->pdf_list2[$key] = array("account_name"=>"    ".$data["account_name"]
									,"account_no"=>" ".$data["account_no"]
									,"July"=>$jul_comp[$key]
									,"August"=>$aug_comp[$key]
									,"September"=>$sep_comp[$key]
									,"3q"=>$this->formatBalance(($unjul_comp[$key] + $unaug_comp[$key] + $unsep_comp[$key]),'I')
									,"October"=>$oct_comp[$key]
									,"November"=>$nov_comp[$key]
									,"December"=>$dec_comp[$key]
									,"4q"=>$this->formatBalance(($unoct_comp[$key] + $unnov_comp[$key] + $undec_comp[$key]),'I'));				
				}
				else 
				{
					$quart1_form[$key] = "=SUM(L$this->quartot_rowstart:T$this->quartot_rowstart)";	
					$quart2_form[$key] = "=SUM(AB$this->quartot_rowstart:AJ$this->quartot_rowstart)";	
					$quart3_form[$key] = "=SUM(AR$this->quartot_rowstart:AZ$this->quartot_rowstart)";	
					$quart4_form[$key] = "=SUM(BH$this->quartot_rowstart:BP$this->quartot_rowstart)";	
					
					$new_list[$key] = array("account_name"=>"    ".$data["account_name"]
									,"account_no"=>" ".$data["account_no"]
									,"January"=>$jan_comp[$key]
									,"February"=>$feb_comp[$key]
									,"March"=>$mar_comp[$key]
									,"1q"=>$quart1_form[$key]
									,"April"=>$apr_comp[$key]
									,"May"=>$may_comp[$key]
									,"June"=>$jun_comp[$key]
									,"2q"=>$quart2_form[$key]
									,"July"=>$jul_comp[$key]
									,"August"=>$aug_comp[$key]
									,"September"=>$sep_comp[$key]
									,"3q"=>$quart3_form[$key]
									,"October"=>$oct_comp[$key]
									,"November"=>$nov_comp[$key]
									,"December"=>$dec_comp[$key]
									,"4q"=>$quart4_form[$key]);	
					$this->quartot_rowstart++;				
				}
				$this->tot_jan += $unjan_comp[$key];
				$this->tot_feb += $unfeb_comp[$key];
				$this->tot_mar += $unmar_comp[$key];
				$this->tot_1q += ($unjan_comp[$key] + $unfeb_comp[$key] + $unmar_comp[$key]);
				$this->tot_apr += $unapr_comp[$key];
				$this->tot_may += $unmay_comp[$key];
				$this->tot_jun += $unjun_comp[$key];
				$this->tot_2q += ($unapr_comp[$key] + $unmay_comp[$key] + $unjun_comp[$key]);
				$this->tot_jul += $unjul_comp[$key];
				$this->tot_aug += $unaug_comp[$key];
				$this->tot_sep += $unsep_comp[$key];
				$this->tot_3q += ($unjul_comp[$key] + $unaug_comp[$key] + $unsep_comp[$key]);
				$this->tot_oct += $unoct_comp[$key];
				$this->tot_nov += $unnov_comp[$key];
				$this->tot_dec += $undec_comp[$key];
				$this->tot_4q += ($unoct_comp[$key] + $unnov_comp[$key] + $undec_comp[$key]);
			}
			
		}
		
		if ($type=='E')
		{		
			$this->etot_jan = 0;
			$this->etot_feb = 0;
			$this->etot_mar = 0;
			$this->etot_1q = 0;
			$this->etot_apr = 0;
			$this->etot_may = 0;
			$this->etot_jun = 0;
			$this->etot_2q = 0;
			$this->etot_jul = 0;
			$this->etot_aug = 0;
			$this->etot_sep = 0;
			$this->etot_3q = 0;
			$this->etot_oct = 0;
			$this->etot_nov = 0;
			$this->etot_dec = 0;
			$this->etot_4q = 0;
		
			foreach($this->e_list AS $data){
				foreach($data AS $key2=>$data2){
					$jan_comp[$key2] = number_format(0,2,'.',',');
					$feb_comp[$key2] = number_format(0,2,'.',',');
					$mar_comp[$key2] = number_format(0,2,'.',',');
					$apr_comp[$key2] = number_format(0,2,'.',',');
					$may_comp[$key2] = number_format(0,2,'.',',');
					$jun_comp[$key2] = number_format(0,2,'.',',');
					$jul_comp[$key2] = number_format(0,2,'.',',');
					$aug_comp[$key2] = number_format(0,2,'.',',');
					$sep_comp[$key2] = number_format(0,2,'.',',');
					$oct_comp[$key2] = number_format(0,2,'.',',');
					$nov_comp[$key2] = number_format(0,2,'.',',');
					$dec_comp[$key2] = number_format(0,2,'.',',');
					
					$unjan_comp[$key2] = 0;
					$unfeb_comp[$key2] = 0;
					$unmar_comp[$key2] = 0;
					$unapr_comp[$key2] = 0;
					$unmay_comp[$key2] = 0;
					$unjun_comp[$key2] = 0;
					$unjul_comp[$key2] = 0;
					$unaug_comp[$key2] = 0;
					$unsep_comp[$key2] = 0;
					$unoct_comp[$key2] = 0;
					$unnov_comp[$key2] = 0;
					$undec_comp[$key2] = 0;
				}
			}
			
			foreach($this->e_list AS $key=>$data)
			{
				foreach($data AS $key2=>$data2)
				{
					$list[$key2] = array("account_name"=>"    ".$data2["account_name"]
										,"account_no"=>" ".$data2["account_no"]
										,"January"=>0
										,"February"=>0 
										,"March"=>0
										,"1q"=>""
										,"April"=>0
										,"May"=>0
										,"June"=>0
										,"2q"=>""
										,"July"=>0
										,"August"=>0
										,"September"=>0
										,"3q"=>""
										,"October"=>0
										,"November"=>0
										,"December"=>0
										,"4q"=>"");
										
					if ($key=='January'){
						$jan_comp[$key2] = $this->formatBalance($data2['comparative_balance'],'E');
						$unjan_comp[$key2] = $data2['comparative_balance'];
						$list[$key2]["January"] = $jan_comp[$key2];
					}
					
					if ($key=='February'){
						$feb_comp[$key2] = $this->formatBalance($data2['comparative_balance'],'E');
						$unfeb_comp[$key2] = $data2['comparative_balance'];
						$list[$key2]["February"] = $feb_comp[$key2];
					}
					
					if ($key=='March'){
						$mar_comp[$key2] = $this->formatBalance($data2['comparative_balance'],'E');
						$unmar_comp[$key2] = $data2['comparative_balance'];
						$list[$key2]["March"] = $mar_comp[$key2];
					}
					
					if ($key=='April'){
						$apr_comp[$key2] = $this->formatBalance($data2['comparative_balance'],'E');
						$unapr_comp[$key2] = $data2['comparative_balance'];
						$list[$key2]["April"] = $apr_comp[$key2];
					}
					
					if ($key=='May'){
						$may_comp[$key2] = $this->formatBalance($data2['comparative_balance'],'E');
						$unmay_comp[$key2] = $data2['comparative_balance'];
						$list[$key2]["May"] = $may_comp[$key2];
					}
					
					if ($key=='June'){
						$jun_comp[$key2] = $this->formatBalance($data2['comparative_balance'],'E');
						$unjun_comp[$key2] = $data2['comparative_balance'];
						$list[$key2]["June"] = $jun_comp[$key2];
					}
					
					if ($key=='July'){
						$jul_comp[$key2] = $this->formatBalance($data2['comparative_balance'],'E');
						$unjul_comp[$key2] = $data2['comparative_balance'];
						$list[$key2]["July"] = $jul_comp[$key2];
					}
					
					if ($key=='August'){
						$aug_comp[$key2] = $this->formatBalance($data2['comparative_balance'],'E');
						$unaug_comp[$key2] = $data2['comparative_balance'];
						$list[$key2]["August"] = $aug_comp[$key2];
					}
					
					if ($key=='September'){
						$sep_comp[$key2] = $this->formatBalance($data2['comparative_balance'],'E');
						$unsep_comp[$key2] = $data2['comparative_balance'];
						$list[$key2]["September"] = $sep_comp[$key2];
					}
					
					if ($key=='October'){
						$oct_comp[$key2] = $this->formatBalance($data2['comparative_balance'],'E');
						$unoct_comp[$key2] = $data2['comparative_balance'];
						$list[$key2]["October"] = $oct_comp[$key2];
					}
					
					if ($key=='November'){
						$nov_comp[$key2] = $this->formatBalance($data2['comparative_balance'],'E');
						$unnov_comp[$key2] = $data2['comparative_balance'];
						$list[$key2]["October"] = $nov_comp[$key2];
					}
					
					if ($key=='December'){
						$dec_comp[$key2] = $this->formatBalance($data2['comparative_balance'],'E');
						$undec_comp[$key2] = $data2['comparative_balance'];
						$list[$key2]["October"] = $dec_comp[$key2];
					}
				}
			}
			
			foreach($list AS $key=>$data)
			{
				if ($this->file_type=='2')
				{
					$this->pdf_list1[$key] = array("account_name"=>"    ".$data["account_name"]
									,"account_no"=>" ".$data["account_no"]
									,"January"=>$jan_comp[$key]
									,"February"=>$feb_comp[$key]
									,"March"=>$mar_comp[$key]
									,"1q"=>$this->formatBalance(($unjan_comp[$key] + $unfeb_comp[$key] + $unmar_comp[$key]),'E')
									,"April"=>$apr_comp[$key]
									,"May"=>$may_comp[$key]
									,"June"=>$jun_comp[$key]
									,"2q"=>$this->formatBalance(($unapr_comp[$key] + $unmay_comp[$key] + $unjun_comp[$key]),'E'));
					$this->pdf_list2[$key] = array("account_name"=>"    ".$data["account_name"]
									,"account_no"=>" ".$data["account_no"]
									,"July"=>$jul_comp[$key]
									,"August"=>$aug_comp[$key]
									,"September"=>$sep_comp[$key]
									,"3q"=>$this->formatBalance(($unjul_comp[$key] + $unaug_comp[$key] + $unsep_comp[$key]),'E')
									,"October"=>$oct_comp[$key]
									,"November"=>$nov_comp[$key]
									,"December"=>$dec_comp[$key]
									,"4q"=>$this->formatBalance(($unoct_comp[$key] + $unnov_comp[$key] + $undec_comp[$key]),'E'));				
				}
				else
				{
					$quart1_form[$key] = "=SUM(L$this->quartot_rowstart:T$this->quartot_rowstart)";	
					$quart2_form[$key] = "=SUM(AB$this->quartot_rowstart:AJ$this->quartot_rowstart)";	
					$quart3_form[$key] = "=SUM(AR$this->quartot_rowstart:AZ$this->quartot_rowstart)";	
					$quart4_form[$key] = "=SUM(BH$this->quartot_rowstart:BP$this->quartot_rowstart)";	
					
					$new_list[$key] = array("account_name"=>"    ".$data["account_name"]
									,"account_no"=>" ".$data["account_no"]
									,"January"=>$jan_comp[$key]
									,"February"=>$feb_comp[$key]
									,"March"=>$mar_comp[$key]
									,"1q"=>$quart1_form[$key]
									,"April"=>$apr_comp[$key]
									,"May"=>$may_comp[$key]
									,"June"=>$jun_comp[$key]
									,"2q"=>$quart2_form[$key]
									,"July"=>$jul_comp[$key]
									,"August"=>$aug_comp[$key]
									,"September"=>$sep_comp[$key]
									,"3q"=>$quart3_form[$key]
									,"October"=>$oct_comp[$key]
									,"November"=>$nov_comp[$key]
									,"December"=>$dec_comp[$key]
									,"4q"=>$quart4_form[$key]);	
					$this->quartot_rowstart++;
				}
				$this->etot_jan += $unjan_comp[$key];
				$this->etot_feb += $unfeb_comp[$key];
				$this->etot_mar += $unmar_comp[$key];
				$this->etot_1q += ($unjan_comp[$key] + $unfeb_comp[$key] + $unmar_comp[$key]);
				$this->etot_apr += $unapr_comp[$key];
				$this->etot_may += $unmay_comp[$key];
				$this->etot_jun += $unjun_comp[$key];
				$this->etot_2q += ($unapr_comp[$key] + $unmay_comp[$key] + $unjun_comp[$key]);
				$this->etot_jul += $unjul_comp[$key];
				$this->etot_aug += $unaug_comp[$key];
				$this->etot_sep += $unsep_comp[$key];
				$this->etot_3q += ($unjul_comp[$key] + $unaug_comp[$key] + $unsep_comp[$key]);
				$this->etot_oct += $unoct_comp[$key];
				$this->etot_nov += $unnov_comp[$key];
				$this->etot_dec += $undec_comp[$key];
				$this->etot_4q += ($unoct_comp[$key] + $unnov_comp[$key] + $undec_comp[$key]);
			}
			
		} 
		
		return $new_list;
	}
	
	function formatBalance($balance,$type)
	{
		$comp = $balance;
		if ($this->file_type=='2'){
			if ($type == 'A' || $type == 'E'){
				if ($balance<0){
					$this->combal = abs($balance);
					$comp = "(".number_format(abs($balance),2,'.',',').")";
				}
				else{
					$comp = number_format($balance,2,'.',',');
					$this->combal = $balance;
				}
			} else{
				if ($balance>0){
					$this->combal = abs($balance);
					$comp = "(".number_format($balance,2,'.',',').")";
				}
				else{
					$comp = number_format(abs($balance),2,'.',',');
					$this->combal = $balance;
				}
			}
		}
		else {
			if ($type == 'A' || $type == 'E'){
				//do nothing
			} else{
				$comp *= -1;
			}	
		}
		
		return $comp;
	}
	
}

?>