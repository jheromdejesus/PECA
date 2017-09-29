<?php

/* Location: ./CodeIgniter/application/controllers/report_comparativeSOC.php 
 * */


class Report_comparativeSOC extends Asi_Controller 
{
	var $file_type;		//1 - excel ; 2 - pdf

	var $report_from;
	var $report_to;
	var $report_title;
	var $list;	
	var $row_height = .18; 	
	var $num_format = "_(* #,##0.00_);_(* \(#,##0.00\);_(* -??_);_(@_)";

	function Report_comparativeSOC() 
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
		/*$_REQUEST['from_date'] = '11/01/2010';
		$_REQUEST['to_date'] = '12/01/2010';
		$_REQUEST['file_type'] = '2';*/
		
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
	 * @desc To retrieve Assets based on the specified period.
	 */
	function readComparativeSA($period)
	{
		$data = $this->ledger_model->retrieveComparativeSOC($period,'A');
		
		return $data['list'];
	}
	
	/**
	 * @desc To retrieve Liabilities based on the specified period.
	 */
	function readComparativeSL($period)
	{
		$data = $this->ledger_model->retrieveComparativeSOC($period,'L');
		
		return $data['list'];
	}
	
	/**
	 * @desc To retrieve Capital based on the specified period.
	 */
	function readComparativeSC($period)
	{
		$data = $this->ledger_model->retrieveComparativeSOC($period,'C');
		
		return $data['list'];
	}
	
	/**
	 * @desc To retrieve Gross Income
	 */
	function readComparativeSI($period)
	{
		$data = $this->ledger_model->retrieveComparativeSOC($period,'I');
		
		return $data['list'];
	}
	
	/**
	 * @desc To retrieve Total Expenses
	 */
	function readComparativeSE($period)
	{
		$data = $this->ledger_model->retrieveComparativeSOC($period,'E');
		
		return $data['list'];
	}
	
	
	function readAccountList($group)
	{
		if($group=='I' || $group == 'E'){
			$data = $this->ledger_model->retrieveAccountListSOI($this->report_from,$this->report_to,$group);
		}
		else{
			$data = $this->ledger_model->retrieveAccountList($this->report_from,$this->report_to,$group);
		}
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
		$objExcel->writeHeaderInfo($this->report_title, date("F Y",strtotime($this->report_from))." to ".date("F Y",strtotime($this->report_to)), "F0007");
		$objExcel->writeSubheader($this->subheader
							   ,0
							   ,$row_start
							   ,12.75
							   ,$this->excel_aligment
							   ,true
							   ,$row_start);
							   
		$objExcel->_setCell('A7','Assets',true,7);
		
		$row_start = 8;
		$this->list = $this->getData('A');
		
		$objExcel->writeTableData($this->list
								,array("account_no" => "right"
									    ,"account_name" => "left"
									    ,"January"=>"right"
										,"February"=>"right"
										,"March"=>"right"
										// ,"1q"=>"right"
										,"April"=>"right"
										,"May"=>"right"
										,"June"=>"right"
										// ,"2q"=>"right"
										,"July"=>"right"
										,"August"=>"right"
										,"September"=>"right"
										// ,"3q"=>"right"
										,"October"=>"right"
										,"November"=>"right"
										,"December"=>"right"
										)
								,array("account_no" => "s"
									    ,"account_name" => "s"
									    ,"January"=>$this->num_format
										,"February"=>$this->num_format
										,"March"=>$this->num_format
										// ,"1q"=>$this->num_format
										,"April"=>$this->num_format
										,"May"=>$this->num_format
										,"June"=>$this->num_format
										// ,"2q"=>$this->num_format
										,"July"=>$this->num_format
										,"August"=>$this->num_format
										,"September"=>$this->num_format
										// ,"3q"=>$this->num_format
										,"October"=>$this->num_format
										,"November"=>$this->num_format
										,"December"=>$this->num_format
										)		
								,$row_start);
		
		$count = $row_start + count($this->list) + 1;
		$row_detail_start = $row_start;
		$row_detail_end = $count - 2;	
		log_message('debug',"=SUM(N$row_detail_start:N$row_detail_end)");

		$objExcel->writeDoubleTotals(array("account_no" => ""
									    ,"account_name" => "Total Assets"
									    //,"January"=>$this->formatBalance($this->tot_jan)
										,"January"=>"=SUM(N$row_detail_start:N$row_detail_end)"
									    ,"February"=>"=SUM(R$row_detail_start:R$row_detail_end)"
									    ,"March"=>"=SUM(V$row_detail_start:V$row_detail_end)"
										// ,"1q"=>$this->formatBalance($this->tot_1q)
										,"April"=>"=SUM(Z$row_detail_start:Z$row_detail_end)"
										,"May"=>"=SUM(AD$row_detail_start:AD$row_detail_end)"
										,"June"=>"=SUM(AH$row_detail_start:AH$row_detail_end)"
										// ,"2q"=>$this->formatBalance($this->tot_2q)
										,"July"=>"=SUM(AL$row_detail_start:AL$row_detail_end)"
										,"August"=>"=SUM(AP$row_detail_start:AP$row_detail_end)"
										,"September"=>"=SUM(AT$row_detail_start:AT$row_detail_end)"
										// ,"3q"=>$this->formatBalance($this->tot_3q)
										,"October"=>"=SUM(AX$row_detail_start:AX$row_detail_end)"
										,"November"=>"=SUM(BB$row_detail_start:BB$row_detail_end)"
										,"December"=>"=SUM(BF$row_detail_start:BF$row_detail_end)"
										)
								,array("account_no" => "left"
									    ,"account_name" => "center"
									    ,"January"=>"right"
										,"February"=>"right"
										,"March"=>"right"
										// ,"1q"=>"right"
										,"April"=>"right"
										,"May"=>"right"
										,"June"=>"right"
										// ,"2q"=>"right"
										,"July"=>"right"
										,"August"=>"right"
										,"September"=>"right"
										// ,"3q"=>"right"
										,"October"=>"right"
										,"November"=>"right"
										,"December"=>"right"
										)
								,array("account_no" => "s"
									    ,"account_name" => "s"
									    ,"January"=>$this->num_format
										,"February"=>$this->num_format
										,"March"=>$this->num_format
										// ,"1q"=>$this->num_format
										,"April"=>$this->num_format
										,"May"=>$this->num_format
										,"June"=>$this->num_format
										// ,"2q"=>$this->num_format
										,"July"=>$this->num_format
										,"August"=>$this->num_format
										,"September"=>$this->num_format
										// ,"3q"=>$this->num_format
										,"October"=>$this->num_format
										,"November"=>$this->num_format
										,"December"=>$this->num_format
										)
								,'N'
								,'BI');				
		
		$objExcel->_setCell('A'.$count,'Liabilities:',true,7);
		$row_start = $count+1;	
		$this->list = $this->getData('L');		
		
		$objExcel->writeTableData($this->list
								,array("account_no" => "right"
									    ,"account_name" => "left"
									    ,"January"=>"right"
										,"February"=>"right"
										,"March"=>"right"
										// ,"1q"=>"right"
										,"April"=>"right"
										,"May"=>"right"
										,"June"=>"right"
										// ,"2q"=>"right"
										,"July"=>"right"
										,"August"=>"right"
										,"September"=>"right"
										// ,"3q"=>"right"
										,"October"=>"right"
										,"November"=>"right"
										,"December"=>"right"
										)
								,array("account_no" => "s"
									    ,"account_name" => "s"
									    ,"January"=>$this->num_format
										,"February"=>$this->num_format
										,"March"=>$this->num_format
										// ,"1q"=>$this->num_format
										,"April"=>$this->num_format
										,"May"=>$this->num_format
										,"June"=>$this->num_format
										// ,"2q"=>$this->num_format
										,"July"=>$this->num_format
										,"August"=>$this->num_format
										,"September"=>$this->num_format
										// ,"3q"=>$this->num_format
										,"October"=>$this->num_format
										,"November"=>$this->num_format
										,"December"=>$this->num_format
										)
								,$row_start);
		
		$count = $row_start + count($this->list) + 1;
		$row_detail_start = $row_start;
		$row_detail_end = $count - 2;	
		//total liablities loc
		$lia_total_start = $row_detail_end + 1;
		log_message('debug',"=SUM(N$row_detail_start:N$row_detail_end)");
								
		$objExcel->writeTotals(array("account_no" => ""
										,"account_name" => "Total Liabilities"
									     //,"January"=>$this->formatBalance($this->tot_jan)
										,"January"=>"=SUM(N$row_detail_start:N$row_detail_end)"
									    ,"February"=>"=SUM(R$row_detail_start:R$row_detail_end)"
									    ,"March"=>"=SUM(V$row_detail_start:V$row_detail_end)"
										// ,"1q"=>$this->formatBalance($this->tot_1q)
										,"April"=>"=SUM(Z$row_detail_start:Z$row_detail_end)"
										,"May"=>"=SUM(AD$row_detail_start:AD$row_detail_end)"
										,"June"=>"=SUM(AH$row_detail_start:AH$row_detail_end)"
										// ,"2q"=>$this->formatBalance($this->tot_2q)
										,"July"=>"=SUM(AL$row_detail_start:AL$row_detail_end)"
										,"August"=>"=SUM(AP$row_detail_start:AP$row_detail_end)"
										,"September"=>"=SUM(AT$row_detail_start:AT$row_detail_end)"
										// ,"3q"=>$this->formatBalance($this->tot_3q)
										,"October"=>"=SUM(AX$row_detail_start:AX$row_detail_end)"
										,"November"=>"=SUM(BB$row_detail_start:BB$row_detail_end)"
										,"December"=>"=SUM(BF$row_detail_start:BF$row_detail_end)"
										)
								,array("account_no" => "right"
									    ,"account_name" => "left"
									    ,"January"=>"right"
										,"February"=>"right"
										,"March"=>"right"
										// ,"1q"=>"right"
										,"April"=>"right"
										,"May"=>"right"
										,"June"=>"right"
										// ,"2q"=>"right"
										,"July"=>"right"
										,"August"=>"right"
										,"September"=>"right"
										// ,"3q"=>"right"
										,"October"=>"right"
										,"November"=>"right"
										,"December"=>"right"
										)
								,array("account_no" => "s"
									    ,"account_name" => "s"
									    ,"January"=>$this->num_format
										,"February"=>$this->num_format
										,"March"=>$this->num_format
										// ,"1q"=>$this->num_format
										,"April"=>$this->num_format
										,"May"=>$this->num_format
										,"June"=>$this->num_format
										// ,"2q"=>$this->num_format
										,"July"=>$this->num_format
										,"August"=>$this->num_format
										,"September"=>$this->num_format
										// ,"3q"=>$this->num_format
										,"October"=>$this->num_format
										,"November"=>$this->num_format
										,"December"=>$this->num_format
										)
								,'N'
								,'BI');
		
		$objExcel->_setCell('A'.$count,'Capital:',true,7);
		$row_start = $count+1;		
		$this->list = $this->getData('C');		
		
		$objExcel->writeTableData($this->list
								,array("account_no" => "right"
									    ,"account_name" => "left"
									    ,"January"=>"right"
										,"February"=>"right"
										,"March"=>"right"
										// ,"1q"=>"right"
										,"April"=>"right"
										,"May"=>"right"
										,"June"=>"right"
										// ,"2q"=>"right"
										,"July"=>"right"
										,"August"=>"right"
										,"September"=>"right"
										// ,"3q"=>"right"
										,"October"=>"right"
										,"November"=>"right"
										,"December"=>"right"
										)
								,array("account_no" => "s"
									    ,"account_name" => "s"
									    ,"January"=>$this->num_format
										,"February"=>$this->num_format
										,"March"=>$this->num_format
										// ,"1q"=>$this->num_format
										,"April"=>$this->num_format
										,"May"=>$this->num_format
										,"June"=>$this->num_format
										// ,"2q"=>$this->num_format
										,"July"=>$this->num_format
										,"August"=>$this->num_format
										,"September"=>$this->num_format
										// ,"3q"=>$this->num_format
										,"October"=>$this->num_format
										,"November"=>$this->num_format
										,"December"=>$this->num_format
										)
								,$row_start);
								
		$count = $row_start + count($this->list) + 1;
		$row_detail_start = $row_start;
		$row_detail_end = $count - 2;	
		//total capital loc
		$cap_total_start = $row_detail_end + 1;
		log_message('debug',"=SUM(N$row_detail_start:N$row_detail_end)");
								
		$objExcel->writeTotals(array("account_no" => ""
										,"account_name" => "Total Capital"
									    ,"January"=>"=SUM(N$row_detail_start:N$row_detail_end)"
									    ,"February"=>"=SUM(R$row_detail_start:R$row_detail_end)"
									    ,"March"=>"=SUM(V$row_detail_start:V$row_detail_end)"
										// ,"1q"=>$this->formatBalance($this->tot_1q)
										,"April"=>"=SUM(Z$row_detail_start:Z$row_detail_end)"
										,"May"=>"=SUM(AD$row_detail_start:AD$row_detail_end)"
										,"June"=>"=SUM(AH$row_detail_start:AH$row_detail_end)"
										// ,"2q"=>$this->formatBalance($this->tot_2q)
										,"July"=>"=SUM(AL$row_detail_start:AL$row_detail_end)"
										,"August"=>"=SUM(AP$row_detail_start:AP$row_detail_end)"
										,"September"=>"=SUM(AT$row_detail_start:AT$row_detail_end)"
										// ,"3q"=>$this->formatBalance($this->tot_3q)
										,"October"=>"=SUM(AX$row_detail_start:AX$row_detail_end)"
										,"November"=>"=SUM(BB$row_detail_start:BB$row_detail_end)"
										,"December"=>"=SUM(BF$row_detail_start:BF$row_detail_end)"
										)
								,array("account_no" => "right"
									    ,"account_name" => "left"
									    ,"January"=>"right"
										,"February"=>"right"
										,"March"=>"right"
										// ,"1q"=>"right"
										,"April"=>"right"
										,"May"=>"right"
										,"June"=>"right"
										// ,"2q"=>"right"
										,"July"=>"right"
										,"August"=>"right"
										,"September"=>"right"
										// ,"3q"=>"right"
										,"October"=>"right"
										,"November"=>"right"
										,"December"=>"right"
										)
								,array("account_no" => "s"
									    ,"account_name" => "s"
									    ,"January"=>$this->num_format
										,"February"=>$this->num_format
										,"March"=>$this->num_format
										// ,"1q"=>$this->num_format
										,"April"=>$this->num_format
										,"May"=>$this->num_format
										,"June"=>$this->num_format
										// ,"2q"=>$this->num_format
										,"July"=>$this->num_format
										,"August"=>$this->num_format
										,"September"=>$this->num_format
										// ,"3q"=>$this->num_format
										,"October"=>$this->num_format
										,"November"=>$this->num_format
										,"December"=>$this->num_format
										)
								,'N'
								,'BI');
								
		$objExcel->writeDoubleTotals(array("account_no" => " "
									    ,"account_name" => "Total Liabilities and Capital Account"
									   ,"January"=>"=N$lia_total_start + N$cap_total_start"
									    ,"February"=>"=R$lia_total_start + R$cap_total_start"
									    ,"March"=>"=V$lia_total_start + V$cap_total_start"
										// ,"1q"=>$this->formatBalance($this->tot_1q)
										,"April"=>"=Z$lia_total_start + Z$cap_total_start"
										,"May"=>"=AD$lia_total_start + AD$cap_total_start"
										,"June"=>"=AH$lia_total_start + AH$cap_total_start"
										// ,"2q"=>$this->formatBalance($this->tot_2q)
										,"July"=>"=AL$lia_total_start + AL$cap_total_start"
										,"August"=>"=AP$lia_total_start + AP$cap_total_start"
										,"September"=>"=AT$lia_total_start + AT$cap_total_start"
										// ,"3q"=>$this->formatBalance($this->tot_3q)
										,"October"=>"=AX$lia_total_start + AX$cap_total_start"
										,"November"=>"=BB$lia_total_start + BB$cap_total_start"
										,"December"=>"=BF$lia_total_start + BF$cap_total_start"
										)
								,array("account_no" => "right"
									    ,"account_name" => "left"
									    ,"January"=>"right"
										,"February"=>"right"
										,"March"=>"right"
										// ,"1q"=>"right"
										,"April"=>"right"
										,"May"=>"right"
										,"June"=>"right"
										// ,"2q"=>"right"
										,"July"=>"right"
										,"August"=>"right"
										,"September"=>"right"
										// ,"3q"=>"right"
										,"October"=>"right"
										,"November"=>"right"
										,"December"=>"right"
										)
								,array("account_no" => "s"
									    ,"account_name" => "s"
									    ,"January"=>$this->num_format
										,"February"=>$this->num_format
										,"March"=>$this->num_format
										// ,"1q"=>$this->num_format
										,"April"=>$this->num_format
										,"May"=>$this->num_format
										,"June"=>$this->num_format
										// ,"2q"=>$this->num_format
										,"July"=>$this->num_format
										,"August"=>$this->num_format
										,"September"=>$this->num_format
										// ,"3q"=>$this->num_format
										,"October"=>$this->num_format
										,"November"=>$this->num_format
										,"December"=>$this->num_format
										)
								,'N'
								,'BI');
								
		$count = $row_start + count($this->list) + 1;
		$row_start = $count + 1;
		
		$income_total_start = $row_start;		
		$this->getIncome();
		$objExcel->writeTableData(array(array("account_no" => "400"
									    ,"account_name" => "Gross Income"
									    ,"January"=>$this->formatBalance($this->itot_jan)
									    ,"February"=>$this->formatBalance($this->itot_feb)
									    ,"March"=>$this->formatBalance($this->itot_mar)
										// ,"1q"=>$this->formatBalance($this->itot_1q)
										,"April"=>$this->formatBalance($this->itot_apr)
										,"May"=>$this->formatBalance($this->itot_may)
										,"June"=>$this->formatBalance($this->itot_jun)
										// ,"2q"=>$this->formatBalance($this->itot_2q)
										,"July"=>$this->formatBalance($this->itot_jul)
										,"August"=>$this->formatBalance($this->itot_aug)
										,"September"=>$this->formatBalance($this->itot_sep)
										// ,"3q"=>$this->formatBalance($this->itot_3q)
										,"October"=>$this->formatBalance($this->itot_oct)
										,"November"=>$this->formatBalance($this->itot_nov)
										,"December"=>$this->formatBalance($this->itot_dec)
										))
								,array("account_no" => "right"
									    ,"account_name" => "left"
									    ,"January"=>"right"
										,"February"=>"right"
										,"March"=>"right"
										// ,"1q"=>"right"
										,"April"=>"right"
										,"May"=>"right"
										,"June"=>"right"
										// ,"2q"=>"right"
										,"July"=>"right"
										,"August"=>"right"
										,"September"=>"right"
										// ,"3q"=>"right"
										,"October"=>"right"
										,"November"=>"right"
										,"December"=>"right"
										)
								,array("account_no" => "s"
									    ,"account_name" => "s"
									    ,"January"=>$this->num_format
										,"February"=>$this->num_format
										,"March"=>$this->num_format
										// ,"1q"=>$this->num_format
										,"April"=>$this->num_format
										,"May"=>$this->num_format
										,"June"=>$this->num_format
										// ,"2q"=>$this->num_format
										,"July"=>$this->num_format
										,"August"=>$this->num_format
										,"September"=>$this->num_format
										// ,"3q"=>$this->num_format
										,"October"=>$this->num_format
										,"November"=>$this->num_format
										,"December"=>$this->num_format
										)
								,$row_start);
		
		$row_start++;
		$expense_total_start = $row_start;	
		
		$this->getExpense();
		$objExcel->writeTableData(array(array("account_no" => "500"
									    ,"account_name" => "Total Expenses"
									    ,"January"=>$this->formatBalance($this->etot_jan)
									    ,"February"=>$this->formatBalance($this->etot_feb)
									    ,"March"=>$this->formatBalance($this->etot_mar)
										// ,"1q"=>$this->formatBalance($this->etot_1q)
										,"April"=>$this->formatBalance($this->etot_apr)
										,"May"=>$this->formatBalance($this->etot_may)
										,"June"=>$this->formatBalance($this->etot_jun)
										// ,"2q"=>$this->formatBalance($this->etot_2q)
										,"July"=>$this->formatBalance($this->etot_jul)
										,"August"=>$this->formatBalance($this->etot_aug)
										,"September"=>$this->formatBalance($this->etot_sep)
										// ,"3q"=>$this->formatBalance($this->etot_3q)
										,"October"=>$this->formatBalance($this->etot_oct)
										,"November"=>$this->formatBalance($this->etot_nov)
										,"December"=>$this->formatBalance($this->etot_dec)
										))
								,array("account_no" => "right"
									    ,"account_name" => "left"
									    ,"January"=>"right"
										,"February"=>"right"
										,"March"=>"right"
										// ,"1q"=>"right"
										,"April"=>"right"
										,"May"=>"right"
										,"June"=>"right"
										// ,"2q"=>"right"
										,"July"=>"right"
										,"August"=>"right"
										,"September"=>"right"
										// ,"3q"=>"right"
										,"October"=>"right"
										,"November"=>"right"
										,"December"=>"right"
										)
								,array("account_no" => "s"
									    ,"account_name" => "s"
									    ,"January"=>$this->num_format
										,"February"=>$this->num_format
										,"March"=>$this->num_format
										// ,"1q"=>$this->num_format
										,"April"=>$this->num_format
										,"May"=>$this->num_format
										,"June"=>$this->num_format
										// ,"2q"=>$this->num_format
										,"July"=>$this->num_format
										,"August"=>$this->num_format
										,"September"=>$this->num_format
										// ,"3q"=>$this->num_format
										,"October"=>$this->num_format
										,"November"=>$this->num_format
										,"December"=>$this->num_format
										)
								,$row_start);
		
		$objExcel->writeDoubleTotals(array("account_no" => " "
									    ,"account_name" => "Net Income"
									   ,"January"=>"=N$income_total_start + N$expense_total_start"
									    ,"February"=>"=R$income_total_start + R$expense_total_start"
									    ,"March"=>"=V$income_total_start + V$expense_total_start"
										// ,"1q"=>$this->formatBalance($this->tot_1q)
										,"April"=>"=Z$income_total_start + Z$expense_total_start"
										,"May"=>"=AD$income_total_start + AD$expense_total_start"
										,"June"=>"=AH$income_total_start + AH$expense_total_start"
										// ,"2q"=>$this->formatBalance($this->tot_2q)
										,"July"=>"=AL$income_total_start + AL$expense_total_start"
										,"August"=>"=AP$income_total_start + AP$expense_total_start"
										,"September"=>"=AT$income_total_start + AT$expense_total_start"
										// ,"3q"=>$this->formatBalance($this->tot_3q)
										,"October"=>"=AX$income_total_start + AX$expense_total_start"
										,"November"=>"=BB$income_total_start + BB$expense_total_start"
										,"December"=>"=BF$income_total_start + BF$expense_total_start"
										)
								,array("account_no" => "right"
									    ,"account_name" => "left"
									    ,"January"=>"right"
										,"February"=>"right"
										,"March"=>"right"
										// ,"1q"=>"right"
										,"April"=>"right"
										,"May"=>"right"
										,"June"=>"right"
										// ,"2q"=>"right"
										,"July"=>"right"
										,"August"=>"right"
										,"September"=>"right"
										// ,"3q"=>"right"
										,"October"=>"right"
										,"November"=>"right"
										,"December"=>"right"
										)
								,array("account_no" => "s"
									    ,"account_name" => "s"
									    ,"January"=>$this->num_format
										,"February"=>$this->num_format
										,"March"=>$this->num_format
										// ,"1q"=>$this->num_format
										,"April"=>$this->num_format
										,"May"=>$this->num_format
										,"June"=>$this->num_format
										// ,"2q"=>$this->num_format
										,"July"=>$this->num_format
										,"August"=>$this->num_format
										,"September"=>$this->num_format
										// ,"3q"=>$this->num_format
										,"October"=>$this->num_format
										,"November"=>$this->num_format
										,"December"=>$this->num_format
										)
								,'N'
								,'BI');
		
		$row_start+=2;
		$objExcel->_setCell('D'.$row_start,'Note:',true,7);	
			
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
		$objPdf->init("landscape",.3);		
		$objPdf->writeHeaderInfo($this->report_title, date("F Y",strtotime($this->report_from))." to ".date("F Y",strtotime($this->report_to)), "F0007");
		
		$objPdf->writeSubheader($this->subheader
							   ,$this->pdf_aligment
							   ,.3);
		
		$objPdf->SetFont('Arial','B',7);
		$objPdf->Cell(23*$this->row_height,$this->row_height,"Assets:","","","L",false);
		$objPdf->Ln();
		
		$this->list = $this->getData('A');
	
		$objPdf->writeTableData($this->list
  								,array("account_no" => 2
										,"account_name" => 10
									    ,"January"=>4
									    ,"February"=>4
									    ,"March"=>4
										// ,"1q"=>4
										,"April"=>4
										,"May"=>4
										,"June"=>4
										// ,"2q"=>4
										,"July"=>4
										,"August"=>4
										,"September"=>4
										// ,"3q"=>4
										,"October"=>4
										,"November"=>4
										,"December"=>4
										)
								,array("account_no" => "R"
									    ,"account_name" => "L"
									    ,"January"=>"R"
										,"February"=>"R"
										,"March"=>"R"
										// ,"1q"=>"R"
										,"April"=>"R"
										,"May"=>"R"
										,"June"=>"R"
										// ,"2q"=>"R"
										,"July"=>"R"
										,"August"=>"R"
										,"September"=>"R"
										// ,"3q"=>"R"
										,"October"=>"R"
										,"November"=>"R"
										,"December"=>"R"
										)
								,.3
								,null
								,null
								,6);
		
		$objPdf->writeTotals(array("account_no" => " "
									    ,"account_name" => "Total Assets"
									    ,"January"=>$this->formatBalance($this->tot_jan, 'A')
									    ,"February"=>$this->formatBalance($this->tot_feb, 'A')
									    ,"March"=>$this->formatBalance($this->tot_mar, 'A')
										// ,"1q"=>$this->formatBalance($this->tot_1q)
										,"April"=>$this->formatBalance($this->tot_apr, 'A')
										,"May"=>$this->formatBalance($this->tot_may, 'A')
										,"June"=>$this->formatBalance($this->tot_jun, 'A')
										// ,"2q"=>$this->formatBalance($this->tot_2q)
										,"July"=>$this->formatBalance($this->tot_jul, 'A')
										,"August"=>$this->formatBalance($this->tot_aug, 'A')
										,"September"=>$this->formatBalance($this->tot_sep, 'A')
										// ,"3q"=>$this->formatBalance($this->tot_3q)
										,"October"=>$this->formatBalance($this->tot_oct, 'A')
										,"November"=>$this->formatBalance($this->tot_nov, 'A')
										,"December"=>$this->formatBalance($this->tot_dec, 'A')
										)
								,array("account_no" => "L"
									    ,"account_name" => "C"
									    ,"January"=>"R"
										,"February"=>"R"
										,"March"=>"R"
										// ,"1q"=>"R"
										,"April"=>"R"
										,"May"=>"R"
										,"June"=>"R"
										// ,"2q"=>"R"
										,"July"=>"R"
										,"August"=>"R"
										,"September"=>"R"
										// ,"3q"=>"R"
										,"October"=>"R"
										,"November"=>"R"
										,"December"=>"R"
										)
								,array("account_no" => 2
									    ,"account_name" => 10
									    ,"January"=>4
									    ,"February"=>4
									    ,"March"=>4
										// ,"1q"=>4
										,"April"=>4
										,"May"=>4
										,"June"=>4
										// ,"2q"=>4
										,"July"=>4
										,"August"=>4
										,"September"=>4
										// ,"3q"=>4
										,"October"=>4
										,"November"=>4
										,"December"=>4
										)		
								,.3
								,true
								,null
								,null
								,6);	
		
		$objPdf->Ln();
		$y = $objPdf->GetY();
		$objPdf->Line(.3,$y, 11.1, $y);
		$objPdf->Ln(.02);
		$y = $objPdf->GetY();
		$objPdf->Line(.3,$y, 11.1, $y);
		$objPdf->Ln(.02);
		
		$objPdf->SetFont('Arial','B',7);
		$objPdf->Cell(23*$this->row_height,$this->row_height,"Liabilities:","","","L",false);
		$objPdf->Ln();
		
		$this->list = $this->getData('L');
		
		$objPdf->writeTableData($this->list
  								,array("account_no" => 2
										,"account_name" => 10
									    ,"January"=>4
									    ,"February"=>4
									    ,"March"=>4
										// ,"1q"=>4
										,"April"=>4
										,"May"=>4
										,"June"=>4
										// ,"2q"=>4
										,"July"=>4
										,"August"=>4
										,"September"=>4
										// ,"3q"=>4
										,"October"=>4
										,"November"=>4
										,"December"=>4
										)
								,array("account_no" => "R"
									    ,"account_name" => "L"
									    ,"January"=>"R"
										,"February"=>"R"
										,"March"=>"R"
										// ,"1q"=>"R"
										,"April"=>"R"
										,"May"=>"R"
										,"June"=>"R"
										// ,"2q"=>"R"
										,"July"=>"R"
										,"August"=>"R"
										,"September"=>"R"
										// ,"3q"=>"R"
										,"October"=>"R"
										,"November"=>"R"
										,"December"=>"R"
										)
								,.3
								,null
								,null
								,6);
		
		$objPdf->writeTotals(array("account_no" => " "
									    ,"account_name" => "Total Liabilities"
									    ,"January"=>$this->formatBalance($this->ltot_jan)
									    ,"February"=>$this->formatBalance($this->ltot_feb)
									    ,"March"=>$this->formatBalance($this->ltot_mar)
										// ,"1q"=>$this->formatBalance($this->ltot_1q)
										,"April"=>$this->formatBalance($this->ltot_apr)
										,"May"=>$this->formatBalance($this->ltot_may)
										,"June"=>$this->formatBalance($this->ltot_jun)
										// ,"2q"=>$this->formatBalance($this->ltot_2q)
										,"July"=>$this->formatBalance($this->ltot_jul)
										,"August"=>$this->formatBalance($this->ltot_aug)
										,"September"=>$this->formatBalance($this->ltot_sep)
										// ,"3q"=>$this->formatBalance($this->ltot_3q)
										,"October"=>$this->formatBalance($this->ltot_oct)
										,"November"=>$this->formatBalance($this->ltot_nov)
										,"December"=>$this->formatBalance($this->ltot_dec)
										)
								,array("account_no" => "L"
									    ,"account_name" => "C"
									    ,"January"=>"R"
										,"February"=>"R"
										,"March"=>"R"
										// ,"1q"=>"R"
										,"April"=>"R"
										,"May"=>"R"
										,"June"=>"R"
										// ,"2q"=>"R"
										,"July"=>"R"
										,"August"=>"R"
										,"September"=>"R"
										// ,"3q"=>"R"
										,"October"=>"R"
										,"November"=>"R"
										,"December"=>"R"
										)
								,array("account_no" => 2
									    ,"account_name" => 10
									    ,"January"=>4
									    ,"February"=>4
									    ,"March"=>4
										// ,"1q"=>4
										,"April"=>4
										,"May"=>4
										,"June"=>4
										// ,"2q"=>4
										,"July"=>4
										,"August"=>4
										,"September"=>4
										// ,"3q"=>4
										,"October"=>4
										,"November"=>4
										,"December"=>4
										)										
								,.3
								,true
								,null
								,null
								,6);
		
		$objPdf->Ln();
		$y = $objPdf->GetY();
		$objPdf->Line(.3,$y, 11.1, $y);
		$objPdf->Ln(.02);
		$y = $objPdf->GetY();
		$objPdf->Line(.3,$y, 11.1, $y);
		$objPdf->Ln(.02);
		
		$objPdf->SetFont('Arial','B',7);
		$objPdf->Cell(23*$this->row_height,$this->row_height,"Capital:","","","L",false);
		$objPdf->Ln();
		$this->list = $this->getData('C');
		
		/* echo "<pre>";
		print_r($this->list);
		echo "</pre>";
		exit(); */
		
		$objPdf->writeTableData($this->list
  								,array("account_no" => 2
										,"account_name" => 10
									    ,"January"=>4
									    ,"February"=>4
									    ,"March"=>4
										// ,"1q"=>4
										,"April"=>4
										,"May"=>4
										,"June"=>4
										// ,"2q"=>4
										,"July"=>4
										,"August"=>4
										,"September"=>4
										// ,"3q"=>4
										,"October"=>4
										,"November"=>4
										,"December"=>4
										)
								,array("account_no" => "R"
									    ,"account_name" => "L"
									    ,"January"=>"R"
										,"February"=>"R"
										,"March"=>"R"
										// ,"1q"=>"R"
										,"April"=>"R"
										,"May"=>"R"
										,"June"=>"R"
										// ,"2q"=>"R"
										,"July"=>"R"
										,"August"=>"R"
										,"September"=>"R"
										// ,"3q"=>"R"
										,"October"=>"R"
										,"November"=>"R"
										,"December"=>"R"
										)
								,.3
								,null
								,null
								,6);
		
		$objPdf->writeTotals(array("account_no" => " "
									    ,"account_name" => "Total Capital Account"
									    ,"January"=>$this->formatBalance($this->ctot_jan)
									    ,"February"=>$this->formatBalance($this->ctot_feb)
									    ,"March"=>$this->formatBalance($this->ctot_mar)
										// ,"1q"=>$this->formatBalance($this->ctot_1q)
										,"April"=>$this->formatBalance($this->ctot_apr)
										,"May"=>$this->formatBalance($this->ctot_may)
										,"June"=>$this->formatBalance($this->ctot_jun)
										// ,"2q"=>$this->formatBalance($this->ctot_2q)
										,"July"=>$this->formatBalance($this->ctot_jul)
										,"August"=>$this->formatBalance($this->ctot_aug)
										,"September"=>$this->formatBalance($this->ctot_sep)
										// ,"3q"=>$this->formatBalance($this->ctot_3q)
										,"October"=>$this->formatBalance($this->ctot_oct)
										,"November"=>$this->formatBalance($this->ctot_nov)
										,"December"=>$this->formatBalance($this->ctot_dec)
										)
								,array("account_no" => "L"
									    ,"account_name" => "C"
									    ,"January"=>"R"
										,"February"=>"R"
										,"March"=>"R"
										// ,"1q"=>"R"
										,"April"=>"R"
										,"May"=>"R"
										,"June"=>"R"
										// ,"2q"=>"R"
										,"July"=>"R"
										,"August"=>"R"
										,"September"=>"R"
										// ,"3q"=>"R"
										,"October"=>"R"
										,"November"=>"R"
										,"December"=>"R")
								,array("account_no" => 2
									    ,"account_name" => 10
									    ,"January"=>4
									    ,"February"=>4
									    ,"March"=>4
										// ,"1q"=>4
										,"April"=>4
										,"May"=>4
										,"June"=>4
										// ,"2q"=>4
										,"July"=>4
										,"August"=>4
										,"September"=>4
										// ,"3q"=>4
										,"October"=>4
										,"November"=>4
										,"December"=>4)		
								,.3
								,true
								,null
								,null
								,6);
		$objPdf->Ln();
		$objPdf->writeTotals(array("account_no" => " "
									    ,"account_name" => "Total Liabilities and Capital Account"
									    ,"January"=>$this->formatBalance(($this->ltot_jan + $this->ctot_jan))
									    ,"February"=>$this->formatBalance(($this->ltot_feb+$this->ctot_feb))
									    ,"March"=>$this->formatBalance(($this->ltot_mar+$this->ctot_mar))
										// ,"1q"=>$this->formatBalance(($this->ltot_1q+$this->ctot_1q))
										,"April"=>$this->formatBalance(($this->ltot_apr+$this->ctot_apr))
										,"May"=>$this->formatBalance(($this->ltot_may+$this->ctot_may))
										,"June"=>$this->formatBalance(($this->ltot_jun+$this->ctot_jun))
										// ,"2q"=>$this->formatBalance(($this->ltot_2q+$this->ctot_2q))
										,"July"=>$this->formatBalance(($this->ltot_jul+$this->ctot_jul))
										,"August"=>$this->formatBalance(($this->ltot_aug+$this->ctot_aug))
										,"September"=>$this->formatBalance(($this->ltot_sep+$this->ctot_sep))
										// ,"3q"=>$this->formatBalance(($this->ltot_3q+$this->ctot_3q))
										,"October"=>$this->formatBalance(($this->ltot_oct+$this->ctot_oct))
										,"November"=>$this->formatBalance(($this->ltot_nov+$this->ctot_nov))
										,"December"=>$this->formatBalance(($this->ltot_dec+$this->ctot_dec)))
								,array("account_no" => "L"
									    ,"account_name" => "C"
									    ,"January"=>"R"
										,"February"=>"R"
										,"March"=>"R"
										// ,"1q"=>"R"
										,"April"=>"R"
										,"May"=>"R"
										,"June"=>"R"
										// ,"2q"=>"R"
										,"July"=>"R"
										,"August"=>"R"
										,"September"=>"R"
										// ,"3q"=>"R"
										,"October"=>"R"
										,"November"=>"R"
										,"December"=>"R"
										)
								,array("account_no" => 2
									    ,"account_name" => 10
									    ,"January"=>4
									    ,"February"=>4
									    ,"March"=>4
										// ,"1q"=>4
										,"April"=>4
										,"May"=>4
										,"June"=>4
										// ,"2q"=>4
										,"July"=>4
										,"August"=>4
										,"September"=>4
										// ,"3q"=>4
										,"October"=>4
										,"November"=>4
										,"December"=>4)											
								,.3
								,true
								,null
								,null
								,6);
		
		$objPdf->Ln();
		$y = $objPdf->GetY();
		$objPdf->Line(.3,$y, 11.1, $y);
		$objPdf->Ln(.02);
		$y = $objPdf->GetY();
		$objPdf->Line(.3,$y, 11.1, $y);
		$objPdf->Ln(.02);
		
		$this->getIncome();
		$objPdf->writeTableData(array(array("account_no" => "400"
									    ,"account_name" => "Gross Income"
									    ,"January"=>$this->formatBalance($this->itot_jan)
									    ,"February"=>$this->formatBalance($this->itot_feb)
									    ,"March"=>$this->formatBalance($this->itot_mar)
										// ,"1q"=>$this->formatBalance($this->itot_1q)
										,"April"=>$this->formatBalance($this->itot_apr)
										,"May"=>$this->formatBalance($this->itot_may)
										,"June"=>$this->formatBalance($this->itot_jun)
										// ,"2q"=>$this->formatBalance($this->itot_2q)
										,"July"=>$this->formatBalance($this->itot_jul)
										,"August"=>$this->formatBalance($this->itot_aug)
										,"September"=>$this->formatBalance($this->itot_sep)
										// ,"3q"=>$this->formatBalance($this->itot_3q))
										,"October"=>$this->formatBalance($this->itot_oct)
										,"November"=>$this->formatBalance($this->itot_nov)
										,"December"=>$this->formatBalance($this->itot_dec)
										))
  								,array("account_no" => 2
										,"account_name" => 10
									    ,"January"=>4
									    ,"February"=>4
									    ,"March"=>4
										// ,"1q"=>4
										,"April"=>4
										,"May"=>4
										,"June"=>4
										// ,"2q"=>4
										,"July"=>4
										,"August"=>4
										,"September"=>4
										// ,"3q"=>4
										,"October"=>4
										,"November"=>4
										,"December"=>4)
								,array("account_no" => "R"
									    ,"account_name" => "L"
									    ,"January"=>"R"
										,"February"=>"R"
										,"March"=>"R"
										// ,"1q"=>"R"
										,"April"=>"R"
										,"May"=>"R"
										,"June"=>"R"
										// ,"2q"=>"R"
										,"July"=>"R"
										,"August"=>"R"
										,"September"=>"R"
										// ,"3q"=>"R"
										,"October"=>"R"
										,"November"=>"R"
										,"December"=>"R"
									)
								,.3
								,null
								,25
								,6);
		
		$this->getExpense();
		$objPdf->writeTableData(array(array("account_no" => "500"
									    ,"account_name" => "Total Expenses"
									    ,"January"=>$this->formatBalance($this->etot_jan)
									    ,"February"=>$this->formatBalance($this->etot_feb)
									    ,"March"=>$this->formatBalance($this->etot_mar)
										// ,"1q"=>$this->formatBalance($this->etot_1q)
										,"April"=>$this->formatBalance($this->etot_apr)
										,"May"=>$this->formatBalance($this->etot_may)
										,"June"=>$this->formatBalance($this->etot_jun)
										// ,"2q"=>$this->formatBalance($this->etot_2q)
										,"July"=>$this->formatBalance($this->etot_jul)
										,"August"=>$this->formatBalance($this->etot_aug)
										,"September"=>$this->formatBalance($this->etot_sep)
										// ,"3q"=>$this->formatBalance($this->etot_3q))
										,"October"=>$this->formatBalance($this->etot_oct)
										,"November"=>$this->formatBalance($this->etot_nov)
										,"December"=>$this->formatBalance($this->etot_dec)
										))
  								,array("account_no" => 2
										,"account_name" => 10
									    ,"January"=>4
									    ,"February"=>4
									    ,"March"=>4
										// ,"1q"=>4
										,"April"=>4
										,"May"=>4
										,"June"=>4
										// ,"2q"=>4
										,"July"=>4
										,"August"=>4
										,"September"=>4
										// ,"3q"=>4
										,"October"=>4
										,"November"=>4
										,"December"=>4)
								,array("account_no" => "R"
									    ,"account_name" => "L"
									    ,"January"=>"R"
										,"February"=>"R"
										,"March"=>"R"
										// ,"1q"=>"R"
										,"April"=>"R"
										,"May"=>"R"
										,"June"=>"R"
										// ,"2q"=>"R"
										,"July"=>"R"
										,"August"=>"R"
										,"September"=>"R"
										// ,"3q"=>"R"
										,"October"=>"R"
										,"November"=>"R"
										,"December"=>"R")
								,.3
								,null
								,null
								,6);
	
		$objPdf->writeTotals(array("account_no" => " "
									    ,"account_name" => "Net Income"
									    ,"January"=>$this->formatBalance(($this->itot_jan + $this->etot_jan))
									    ,"February"=>$this->formatBalance(($this->itot_feb + $this->etot_feb))
									    ,"March"=>$this->formatBalance(($this->itot_mar + $this->etot_mar))
										// ,"1q"=>$this->formatBalance(($this->itot_1q - $this->etot_1q))
										,"April"=>$this->formatBalance(($this->itot_apr + $this->etot_apr))
										,"May"=>$this->formatBalance(($this->itot_may + $this->etot_may))
										,"June"=>$this->formatBalance(($this->itot_jun + $this->etot_jun))
										// ,"2q"=>$this->formatBalance(($this->itot_2q - $this->etot_2q))
										,"July"=>$this->formatBalance(($this->itot_jul + $this->etot_jul))
										,"August"=>$this->formatBalance(($this->itot_aug + $this->etot_aug))
										,"September"=>$this->formatBalance(($this->itot_sep + $this->etot_sep))
										// ,"3q"=>$this->formatBalance(($this->itot_3q - $this->etot_3q))
										,"October"=>$this->formatBalance(($this->itot_oct + $this->etot_oct))
										,"November"=>$this->formatBalance(($this->itot_nov + $this->etot_nov))
										,"December"=>$this->formatBalance(($this->itot_dec + $this->etot_dec))
										)
								,array("account_no" => "L"
									    ,"account_name" => "C"
									    ,"January"=>"R"
										,"February"=>"R"
										,"March"=>"R"
										// ,"1q"=>"R"
										,"April"=>"R"
										,"May"=>"R"
										,"June"=>"R"
										// ,"2q"=>"R"
										,"July"=>"R"
										,"August"=>"R"
										,"September"=>"R"
										// ,"3q"=>"R"
										,"October"=>"R"
										,"November"=>"R"
										,"December"=>"R")
								,array("account_no" => 2
									    ,"account_name" => 10
									    ,"January"=>4
									    ,"February"=>4
									    ,"March"=>4
										// ,"1q"=>4
										,"April"=>4
										,"May"=>4
										,"June"=>4
										// ,"2q"=>4
										,"July"=>4
										,"August"=>4
										,"September"=>4
										// ,"3q"=>4
										,"October"=>4
										,"November"=>4
										,"December"=>4)		
								,.3
								,true
								,null
								,null
								,6);
		
		$objPdf->Ln();
		$y = $objPdf->GetY();
		$objPdf->Line(.3,$y, 11.1, $y);
		$objPdf->Ln(.02);
		$y = $objPdf->GetY();
		$objPdf->Line(.3,$y, 11.1, $y);
		$objPdf->Ln(.02);
		$objPdf->SetFont('Arial','',7);
		$objPdf->Cell(2.5*$this->row_height,$this->row_height," ","","","L",false);
		$objPdf->Cell(23*$this->row_height,$this->row_height,"Note:","","","L",false);
		
		$objPdf->writeFooter();
		$objPdf->Output($this->report_title."_".date("YmdHis"));								
	}
	
	/**
	 * @desc Sets the data
	 */
	function setTableData() 
	{	
		$count = 0;
		$this->report_title = "Comparative Balance Sheet";
		$w = 2;
		if ($this->file_type == '1'){
			$w = 3;
		}
		$this->subheader = array("   "=>$w
								," "=>10
								,"January"=>4
								,"February"=>4
								,"March"=>4
								// ,"1Q Total"=>4
								,"April"=>4
								,"May"=>4
								,"June"=>4
								// ,"2Q Total"=>4
								,"July"=>4
								,"August"=>4
								,"September"=>4
								// ,"3Q Total"=>4);
								,"October"=>4
								,"November"=>4
								,"December"=>4);
		$this->pdf_aligment = array("   "=>"R"
								," "=>"R"
								,"January"=>"R"
								,"February"=>"R"
								,"March"=>"R"
								// ,"1Q Total"=>"R"
								,"April"=>"R"
								,"May"=>"R"
								,"June"=>"R"
								// ,"2Q Total"=>"R"
								,"July"=>"R"
								,"August"=>"R"
								,"September"=>"R"
								// ,"3Q Total"=>"R");
								,"October"=>"R"
								,"November"=>"R"
								,"December"=>"R");
		$this->excel_aligment = array("   "=>"right"
								," "=>"right"
								,"January"=>"right"
								,"February"=>"right"
								,"March"=>"right"
								// ,"1Q Total"=>"right"
								,"April"=>"right"
								,"May"=>"right"
								,"June"=>"right"
								// ,"2Q Total"=>"right"
								,"July"=>"right"
								,"August"=>"right"
								,"September"=>"right"
								// ,"3Q Total"=>"right");	
								,"October"=>"right"
								,"November"=>"right"
								,"December"=>"right");								
								
		$this->month_list = array("January"
								,"February"
								,"March"
								,"April"
								,"May"
								,"June"
								,"July"
								,"August"
								,"September"
								,"October"
								,"November"
								,"December");						
	
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
		
		$this->a_list = array();
		$this->l_list = array();
		$this->c_list = array();
		$this->i_list = array();
		$this->e_list = array();
		
		$c_account_temp = $this->readAccountList('C');
		$a_account_temp = $this->readAccountList('A');
		$l_account_temp = $this->readAccountList('L');
		$i_account_temp = $this->readAccountList('I');
		$e_account_temp = $this->readAccountList('E');
		
		$c_account = array();
		$a_account = array();
		$l_account = array();
		$i_account = array();
		$e_account = array();
		
		foreach($c_account_temp AS $data){
			$c_account[$data['account_no']] = $data;
		}
		
		foreach($a_account_temp AS $data){
			$a_account[$data['account_no']] = $data;
		}
		
		foreach($l_account_temp AS $data){
			$l_account[$data['account_no']] = $data;
		}
		
		foreach($i_account_temp AS $data){
			$i_account[$data['account_no']] = $data;
		}
		
		foreach($e_account_temp AS $data){
			$e_account[$data['account_no']] = $data;
		}
		
		/* echo "<pre>";
		print_r($l_account);
		echo "</pre>";
		exit(); */
		
		foreach($this->accperiod_list AS $period)
		{
			//asset
			$temp_arr = $this->readComparativeSA($period);
			$temp_arr2 = array();
			foreach($temp_arr AS $data){
				$temp_arr2[$data['account_no']] = $data;
			}
			if (count($temp_arr)>0){
				foreach($a_account AS $key=>$data){
					if (array_key_exists($key,$temp_arr2)){
						$this->a_list[date("F",strtotime($period))] = $temp_arr2;
					}
					else {
						$temp_arr2[$key] = array('account_no'=>$key
											,'account_name'=>$data['account_name']
											,'accounting_period'=>$period
											,'beginning_balance'=>0
											,'debits'=>0
											,'credits'=>0
											,'ending_balance'=>0
											,'account_group'=>'C');
						$this->a_list[date("F",strtotime($period))] = $temp_arr2; 
					}
				}
			}
			else $count++;
			
			//liabilities
			$temp_arr = $this->readComparativeSL($period);
			$temp_arr2 = array();
			foreach($temp_arr AS $data){
				$temp_arr2[$data['account_no']] = $data;
			}
			if (count($temp_arr)>0){
				foreach($l_account AS $key=>$data){
					if (array_key_exists($key,$temp_arr2)){
						$this->l_list[date("F",strtotime($period))] = $temp_arr2;
					}
					else {
						$temp_arr2[$key] = array('account_no'=>$key
											,'account_name'=>$data['account_name']
											,'accounting_period'=>$period
											,'beginning_balance'=>0
											,'debits'=>0
											,'credits'=>0
											,'ending_balance'=>0
											,'account_group'=>'C');
						$this->l_list[date("F",strtotime($period))] = $temp_arr2; 
					}
				}
			}
			else $count++;
			
			//capital
			$temp_arr = $this->readComparativeSC($period);
			$temp_arr2 = array();
			foreach($temp_arr AS $data){
				$temp_arr2[$data['account_no']] = $data;
			}
			if (count($temp_arr)>0){
				foreach($c_account AS $key=>$data){
					if (array_key_exists($key,$temp_arr2)){
						$this->c_list[date("F",strtotime($period))] = $temp_arr2;
					}
					else {
						$temp_arr2[$key] = array('account_no'=>$key
											,'account_name'=>$data['account_name']
											,'accounting_period'=>$period
											,'beginning_balance'=>0
											,'debits'=>0
											,'credits'=>0
											,'ending_balance'=>0
											,'account_group'=>'C');
						$this->c_list[date("F",strtotime($period))] = $temp_arr2; 
					}
				}
			}
			else $count++;
			
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
											,'beginning_balance'=>0
											,'debits'=>0
											,'credits'=>0
											,'ending_balance'=>0
											,'account_group'=>'C');
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
											,'beginning_balance'=>0
											,'debits'=>0
											,'credits'=>0
											,'ending_balance'=>0
											,'account_group'=>'C');
						$this->e_list[date("F",strtotime($period))] = $temp_arr2; 
					}
				}
			}
			else $count++;
		}
		
		if ($count==(count($this->accperiod_list)*5)) 
			return true;
		
	}
	
	/**
	 * @desc Gets the data
	 */
	function getData($type) 
	{
		$transaction_amount = 0;
		$jan_comp = 0;
		$combal = 0;
		$new_list = array();
		$list = array();
		
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
		
		if ($type=='A')
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
			
			foreach($this->a_list AS $data){
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
			
			foreach($this->a_list AS $key=>$data)
			{
				foreach($data AS $key2=>$data2)
				{
					$list[$key2] = array("account_no"=>" ".$data2["account_no"]
										,"account_name"=>$data2["account_name"]
										,"January"=>0
										,"February"=>0 
										,"March"=>0
										// ,"1q"=>""
										,"April"=>0
										,"May"=>0
										,"June"=>0
										// ,"2q"=>""
										,"July"=>0
										,"August"=>0
										,"September"=>0
										// ,"3q"=>""
										,"October"=>0
										,"November"=>0
										,"December"=>0
										);	
					
					if ($key=='January'){
						$jan_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unjan_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["January"] = $jan_comp[$key2];
					}
					
					if ($key=='February'){
						$feb_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unfeb_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["February"] = $feb_comp[$key2];
					}
					
					if ($key=='March'){
						$mar_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unmar_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["March"] = $mar_comp[$key2];
					}
					
					if ($key=='April'){
						$apr_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unapr_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["April"] = $apr_comp[$key2];
					}
					
					if ($key=='May'){
						$may_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unmay_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["May"] = $may_comp[$key2];
					}
					
					if ($key=='June'){
						$jun_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unjun_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["June"] = $jun_comp[$key2];
					}
					
					if ($key=='July'){
						$jul_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unjul_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["July"] = $jul_comp[$key2];
					}
					
					if ($key=='August'){
						$aug_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unaug_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["August"] = $aug_comp[$key2];
					}
					
					if ($key=='September'){
						$sep_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unsep_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["September"] = $sep_comp[$key2];
					} 
					
					if ($key=='October'){
						$oct_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unoct_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["October"] = $oct_comp[$key2];
					}
					
					if ($key=='November'){
						$nov_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unnov_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["November"] = $nov_comp[$key2];
					}
					
					if ($key=='December'){
						$dec_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$undec_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["December"] = $dec_comp[$key2];
					} 
				}
			}
			//orders list by account no in ascending order
			ksort($list);
			
			foreach($list AS $key=>$data){
				$new_list[$key] = array("account_no"=>" ".$data["account_no"]
									,"account_name"=>$data["account_name"]
									,"January"=>$jan_comp[$key]
									,"February"=>$feb_comp[$key]
									,"March"=>$mar_comp[$key]
									// ,"1q"=>$this->formatBalance(($unjan_comp[$key] + $unfeb_comp[$key] + $unmar_comp[$key]))
									,"April"=>$apr_comp[$key]
									,"May"=>$may_comp[$key]
									,"June"=>$jun_comp[$key]
									// ,"2q"=>$this->formatBalance(($unapr_comp[$key] + $unmay_comp[$key] + $unjun_comp[$key]))
									,"July"=>$jul_comp[$key]
									,"August"=>$aug_comp[$key]
									,"September"=>$sep_comp[$key]
									// ,"3q"=>$this->formatBalance(($unjul_comp[$key] + $unaug_comp[$key] + $unsep_comp[$key]))
									,"October"=>$oct_comp[$key]
									,"November"=>$nov_comp[$key]
									,"December"=>$dec_comp[$key]
									);	
				
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
			}
			
		}
		
		if ($type=='L')
		{
			$this->ltot_jan = 0;
			$this->ltot_feb = 0;
			$this->ltot_mar = 0;
			$this->ltot_1q = 0;
			$this->ltot_apr = 0;
			$this->ltot_may = 0;
			$this->ltot_jun = 0;
			$this->ltot_2q = 0;
			$this->ltot_jul = 0;
			$this->ltot_aug = 0;
			$this->ltot_sep = 0;
			$this->ltot_3q = 0;
			$this->ltot_oct = 0;
			$this->ltot_nov = 0;
			$this->ltot_dec = 0;
			
			foreach($this->l_list AS $data){
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
			
			foreach($this->l_list AS $key=>$data)
			{
				foreach($data AS $key2=>$data2)
				{
					$list[$key2] = array("account_no"=>" ".$data2["account_no"]
										,"account_name"=>$data2["account_name"]
										,"January"=>0
										,"February"=>0 
										,"March"=>0
										// ,"1q"=>""
										,"April"=>0
										,"May"=>0
										,"June"=>0
										// ,"2q"=>""
										,"July"=>0
										,"August"=>0
										,"September"=>0
										// ,"3q"=>""
										,"October"=>0
										,"November"=>0
										,"December"=>0
										);	
					
					if ($key=='January'){
						$jan_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unjan_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["January"] = $jan_comp[$key2];
					}
					
					if ($key=='February'){
						$feb_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unfeb_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["February"] = $feb_comp[$key2];
					}
					
					if ($key=='March'){
						$mar_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unmar_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["March"] = $mar_comp[$key2];
					}
					
					if ($key=='April'){
						$apr_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unapr_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["April"] = $apr_comp[$key2];
					}
					
					if ($key=='May'){
						$may_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unmay_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["May"] = $may_comp[$key2];
					}
					
					if ($key=='June'){
						$jun_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unjun_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["June"] = $jun_comp[$key2];
					}
					
					if ($key=='July'){
						$jul_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unjul_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["July"] = $jul_comp[$key2];
					}
					
					if ($key=='August'){
						$aug_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unaug_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["August"] = $aug_comp[$key2];
					}
					
					if ($key=='September'){
						$sep_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unsep_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["September"] = $sep_comp[$key2];
					} 
					
					if ($key=='October'){
						$oct_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unoct_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["October"] = $oct_comp[$key2];
					}
					
					if ($key=='November'){
						$nov_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unnov_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["November"] = $nov_comp[$key2];
					}
					
					if ($key=='December'){
						$dec_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$undec_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["December"] = $dec_comp[$key2];
					} 
				}
			}
			
			//orders list by account no in ascending order
			ksort($list);
			
			foreach($list AS $key=>$data){
				$new_list[$key] = array("account_no"=>" ".$data["account_no"]
									,"account_name"=>$data["account_name"]
									,"January"=>$jan_comp[$key]
									,"February"=>$feb_comp[$key]
									,"March"=>$mar_comp[$key]
									// ,"1q"=>$this->formatBalance(($unjan_comp[$key] + $unfeb_comp[$key] + $unmar_comp[$key]))
									,"April"=>$apr_comp[$key]
									,"May"=>$may_comp[$key]
									,"June"=>$jun_comp[$key]
									// ,"2q"=>$this->formatBalance(($unapr_comp[$key] + $unmay_comp[$key] + $unjun_comp[$key]))
									,"July"=>$jul_comp[$key]
									,"August"=>$aug_comp[$key]
									,"September"=>$sep_comp[$key]
									// ,"3q"=>$this->formatBalance(($unjul_comp[$key] + $unaug_comp[$key] + $unsep_comp[$key]))
									,"October"=>$oct_comp[$key]
									,"November"=>$nov_comp[$key]
									,"December"=>$dec_comp[$key]
									);	
				
				$this->ltot_jan += $unjan_comp[$key];
				$this->ltot_feb += $unfeb_comp[$key];
				$this->ltot_mar += $unmar_comp[$key];
				$this->ltot_1q += ($unjan_comp[$key] + $unfeb_comp[$key] + $unmar_comp[$key]);
				$this->ltot_apr += $unapr_comp[$key];
				$this->ltot_may += $unmay_comp[$key];
				$this->ltot_jun += $unjun_comp[$key];
				$this->ltot_2q += ($unapr_comp[$key] + $unmay_comp[$key] + $unjun_comp[$key]);
				$this->ltot_jul += $unjul_comp[$key];
				$this->ltot_aug += $unaug_comp[$key];
				$this->ltot_sep += $unsep_comp[$key];
				$this->ltot_3q += ($unjul_comp[$key] + $unaug_comp[$key] + $unsep_comp[$key]);
				$this->ltot_oct+= $unoct_comp[$key];
				$this->ltot_nov+= $unnov_comp[$key];
				$this->ltot_dec += $undec_comp[$key];
			}
		}	
		
		if ($type=='C')
		{
			$this->ctot_jan = 0;
			$this->ctot_feb = 0;
			$this->ctot_mar = 0;
			$this->ctot_1q = 0;
			$this->ctot_apr = 0;
			$this->ctot_may = 0;
			$this->ctot_jun = 0;
			$this->ctot_2q = 0;
			$this->ctot_jul = 0;
			$this->ctot_aug = 0;
			$this->ctot_sep = 0;
			$this->ctot_3q = 0;
			$this->ctot_oct = 0;
			$this->ctot_nov = 0;
			$this->ctot_dec = 0;
			
			foreach($this->c_list AS $data){
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
			
			foreach($this->c_list AS $key=>$data)
			{
				foreach($data AS $key2=>$data2)
				{
					$list[$key2] = array("account_no"=>" ".$data2["account_no"]
										,"account_name"=>$data2["account_name"]
										,"January"=>0
										,"February"=>0 
										,"March"=>0
										// ,"1q"=>""
										,"April"=>0
										,"May"=>0
										,"June"=>0
										// ,"2q"=>""
										,"July"=>0
										,"August"=>0
										,"September"=>0
										// ,"3q"=>""
										,"October"=>0
										,"November"=>0
										,"December"=>0
										);	
					
					if ($key=='January'){
						$jan_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unjan_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["January"] = $jan_comp[$key2];
					}
					
					if ($key=='February'){
						$feb_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unfeb_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["February"] = $feb_comp[$key2];
					}
					
					if ($key=='March'){
						$mar_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unmar_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["March"] = $mar_comp[$key2];
					}
					
					if ($key=='April'){
						$apr_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unapr_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["April"] = $apr_comp[$key2];
					}
					
					if ($key=='May'){
						$may_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unmay_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["May"] = $may_comp[$key2];
					}
					
					if ($key=='June'){
						$jun_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unjun_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["June"] = $jun_comp[$key2];
					}
					
					if ($key=='July'){
						$jul_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unjul_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["July"] = $jul_comp[$key2];
					}
					
					if ($key=='August'){
						$aug_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unaug_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["August"] = $aug_comp[$key2];
					}
					
					if ($key=='September'){
						$sep_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unsep_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["September"] = $sep_comp[$key2];
					} 
					
					if ($key=='October'){
						$oct_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unoct_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["October"] = $oct_comp[$key2];
					}
					
					if ($key=='November'){
						$nov_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unnov_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["November"] = $nov_comp[$key2];
					}
					
					if ($key=='December'){
						$dec_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$undec_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["December"] = $dec_comp[$key2];
					} 
				}
			}
			
			//orders list by account no in ascending order
			ksort($list);
			
			foreach($list AS $key=>$data){
				$new_list[$key] = array("account_no"=>" ".$data["account_no"]
									,"account_name"=>$data["account_name"]
									,"January"=>$jan_comp[$key]
									,"February"=>$feb_comp[$key]
									,"March"=>$mar_comp[$key]
									// ,"1q"=>$this->formatBalance(($unjan_comp[$key] + $unfeb_comp[$key] + $unmar_comp[$key]))
									,"April"=>$apr_comp[$key]
									,"May"=>$may_comp[$key]
									,"June"=>$jun_comp[$key]
									// ,"2q"=>$this->formatBalance(($unapr_comp[$key] + $unmay_comp[$key] + $unjun_comp[$key]))
									,"July"=>$jul_comp[$key]
									,"August"=>$aug_comp[$key]
									,"September"=>$sep_comp[$key]
									// ,"3q"=>$this->formatBalance(($unjul_comp[$key] + $unaug_comp[$key] + $unsep_comp[$key]))
									,"October"=>$oct_comp[$key]
									,"November"=>$nov_comp[$key]
									,"December"=>$dec_comp[$key]
									);	
				
				$this->ctot_jan += $unjan_comp[$key];
				$this->ctot_feb += $unfeb_comp[$key];
				$this->ctot_mar += $unmar_comp[$key];
				$this->ctot_1q += ($unjan_comp[$key] + $unfeb_comp[$key] + $unmar_comp[$key]);
				$this->ctot_apr += $unapr_comp[$key];
				$this->ctot_may += $unmay_comp[$key];
				$this->ctot_jun += $unjun_comp[$key];
				$this->ctot_2q += ($unapr_comp[$key] + $unmay_comp[$key] + $unjun_comp[$key]);
				$this->ctot_jul += $unjul_comp[$key];
				$this->ctot_aug += $unaug_comp[$key];
				$this->ctot_sep += $unsep_comp[$key];
				$this->ctot_3q += ($unjul_comp[$key] + $unaug_comp[$key] + $unsep_comp[$key]);
				$this->ctot_oct += $unoct_comp[$key];
				$this->ctot_nov += $unnov_comp[$key];
				$this->ctot_dec += $undec_comp[$key];
			}	
		}
		
		return $new_list;
	}
	
	
	function getIncome()
	{
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
		
		$this->itot_jan = 0;
		$this->itot_feb = 0;
		$this->itot_mar = 0;
		$this->itot_1q = 0;
		$this->itot_apr = 0;
		$this->itot_may = 0;
		$this->itot_jun = 0;
		$this->itot_2q = 0;
		$this->itot_jul = 0;
		$this->itot_aug = 0;
		$this->itot_sep = 0;
		$this->itot_3q = 0;
		$this->itot_oct = 0;
		$this->itot_nov = 0;
		$this->itot_dec = 0;
		
		$list = array();
	
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
					$list[$key2] = array("account_no"=>" ".$data2["account_no"]
										,"account_name"=>$data2["account_name"]
										,"January"=>0
										,"February"=>0 
										,"March"=>0
										// ,"1q"=>""
										,"April"=>0
										,"May"=>0
										,"June"=>0
										// ,"2q"=>""
										,"July"=>0
										,"August"=>0
										,"September"=>0
										// ,"3q"=>""
										,"October"=>0
										,"November"=>0
										,"December"=>0
										);	
					
					if ($key=='January'){
						$jan_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unjan_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["January"] = $jan_comp[$key2];
					}
					
					if ($key=='February'){
						$feb_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unfeb_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["February"] = $feb_comp[$key2];
					}
					
					if ($key=='March'){
						$mar_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unmar_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["March"] = $mar_comp[$key2];
					}
					
					if ($key=='April'){
						$apr_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unapr_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["April"] = $apr_comp[$key2];
					}
					
					if ($key=='May'){
						$may_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unmay_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["May"] = $may_comp[$key2];
					}
					
					if ($key=='June'){
						$jun_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unjun_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["June"] = $jun_comp[$key2];
					}
					
					if ($key=='July'){
						$jul_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unjul_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["July"] = $jul_comp[$key2];
					}
					
					if ($key=='August'){
						$aug_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unaug_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["August"] = $aug_comp[$key2];
					}
					
					if ($key=='September'){
						$sep_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unsep_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["September"] = $sep_comp[$key2];
					} 
					
					if ($key=='October'){
						$oct_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unoct_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["October"] = $oct_comp[$key2];
					}
					
					if ($key=='November'){
						$nov_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unnov_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["November"] = $nov_comp[$key2];
					}
					
					if ($key=='December'){
						$dec_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$undec_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["December"] = $dec_comp[$key2];
					} 
				}
			}
		
			foreach($list AS $key=>$data){
				$this->itot_jan += $unjan_comp[$key];
				$this->itot_feb += $unfeb_comp[$key];
				$this->itot_mar += $unmar_comp[$key];
				$this->itot_1q += ($unjan_comp[$key] + $unfeb_comp[$key] + $unmar_comp[$key]);
				$this->itot_apr += $unapr_comp[$key];
				$this->itot_may += $unmay_comp[$key];
				$this->itot_jun += $unjun_comp[$key];
				$this->itot_2q += ($unapr_comp[$key] + $unmay_comp[$key] + $unjun_comp[$key]);
				$this->itot_jul += $unjul_comp[$key];
				$this->itot_aug += $unaug_comp[$key];
				$this->itot_sep += $unsep_comp[$key];
				$this->itot_3q += ($unjul_comp[$key] + $unaug_comp[$key] + $unsep_comp[$key]);
				$this->itot_oct += $unoct_comp[$key];
				$this->itot_nov += $unnov_comp[$key];
				$this->itot_dec += $undec_comp[$key];
			}
	}
	
	function getExpense()
	{
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

		$list = array();
		
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
					$list[$key2] = array("account_no"=>" ".$data2["account_no"]
										,"account_name"=>$data2["account_name"]
										,"January"=>0
										,"February"=>0 
										,"March"=>0
										// ,"1q"=>""
										,"April"=>0
										,"May"=>0
										,"June"=>0
										// ,"2q"=>""
										,"July"=>0
										,"August"=>0
										,"September"=>0
										// ,"3q"=>""
										,"October"=>0
										,"November"=>0
										,"December"=>0
										);	
					
					if ($key=='January'){
						$jan_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unjan_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["January"] = $jan_comp[$key2];
					}
					
					if ($key=='February'){
						$feb_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unfeb_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["February"] = $feb_comp[$key2];
					}
					
					if ($key=='March'){
						$mar_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unmar_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["March"] = $mar_comp[$key2];
					}
					
					if ($key=='April'){
						$apr_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unapr_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["April"] = $apr_comp[$key2];
					}
					
					if ($key=='May'){
						$may_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unmay_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["May"] = $may_comp[$key2];
					}
					
					if ($key=='June'){
						$jun_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unjun_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["June"] = $jun_comp[$key2];
					}
					
					if ($key=='July'){
						$jul_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unjul_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["July"] = $jul_comp[$key2];
					}
					
					if ($key=='August'){
						$aug_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unaug_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["August"] = $aug_comp[$key2];
					}
					
					if ($key=='September'){
						$sep_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unsep_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["September"] = $sep_comp[$key2];
					} 
					
					if ($key=='October'){
						$oct_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unoct_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["October"] = $oct_comp[$key2];
					}
					
					if ($key=='November'){
						$nov_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$unnov_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["November"] = $nov_comp[$key2];
					}
					
					if ($key=='December'){
						$dec_comp[$key2] = $this->formatBalance($data2['ending_balance'], $data2['account_group']);
						$undec_comp[$key2] = ($data2['ending_balance']);
						$list[$key2]["December"] = $dec_comp[$key2];
					} 
				}
			}
		
			foreach($list AS $key=>$data){
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
			}
		
	}
	
	
	function formatBalance($balance, $group = ""){
		$comp = 0;
		
		if ($this->file_type == '1'){
			return $this->formatBalanceExcel($balance, $group);
		}
	
		if ($group == 'A' || $group == 'E'){
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
		
		return $comp;
	}
	
	function formatBalanceExcel($balance, $group = ""){
		$comp = $balance;
	
		if ($group == 'A' || $group == 'E'){
			//do nothing
		} else{
			$comp *= -1;
		}
		
		return $comp;
	}
	
}

?>