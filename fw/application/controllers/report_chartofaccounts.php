<?php

/* Location: ./CodeIgniter/application/controllers/report_chartofaccounts.php */

class Report_chartofaccounts extends Asi_Controller 
{
	var $file_type;		//1 - excel ; 2 - pdf
	var $report_title;
	var $list;	

	function Report_chartofaccounts() 
	{
		parent::Asi_Controller();
		$this->load->model('account_model');
		$this->load->helper('url');
		$this->load->library('constants');
		$this->load->library('asi_model');
		$this->load->library('asi_pdf_ext');
		$this->load->library('asi_excel_ext');
		$this->load->helper('date');	
	}
	
	function index() 
	{			
		 
		$this->file_type = $_REQUEST['file_type'];
		
		$result = $this->setTableData();	
		
		if ($result == true) {
			echo("{'success':false,'msg':'No records found.','error_code':'19'}");	
		}
		else {
			if ($this->file_type=='1') $this->printExcel();
			else $this->printPDF();
		}
	}

	/**
	 * @descTo retrieve all active chart of accounts.
	 * @return array
	 */
	function read()
	{
		$data = $this->account_model->get_list(
			array('status_flag' => '1'),
			null,
			null,
			array('account_no'
				 ,'account_name'
				 ,'account_group'),
			'account_no ASC');
			
		return $data['list'];
	}
	
	/**
	 * @desc Displays the report in Excel File
	 */
	function printExcel()
	{
		$row_start = 5;								 		//row after the header		
		$objExcel = new Asi_excel_ext("portrait");
		$objExcel->init(37,5);		
		$objExcel->writeHeaderInfo($this->report_title, "", "T00001");
		$objExcel->writeSubheader(
								array("Account Number" => 8		//no of cells in actual excel
									 ,"Account Name" => 20
									 ,"Account Group" => 9)
								,0
								,$row_start
								,12.75
								,array("Account Number" => "right"		
									 ,"Account Name" => "left"
									 ,"Account Group" => "left")
								,true
								,$row_start);				  				
		
		$this->list = $this->getData($this->transaction_list);	
		$count = count($this->list);
		$row_start++;	
		$objExcel->writeTableData(
								$this->list
								,array("account_no" => "right"		
									 ,"account_name" => "left"
									 ,"account_group" => "left")
								,array("account_no" => "s"		
									 ,"account_name" => "s"
									 ,"account_group" => "s")
								,$row_start);	
		$objExcel->writeTotals(
								array("account_no" => "Total:"		
									 ,"account_name" => $count." Account/s"
									 ,"account_group" => "")
								,array("account_no" => "left"		
									 ,"account_name" => "left"
									 ,"account_group" => "left")
								,array("account_no" => "s"		
									 ,"account_name" => "s"
									 ,"account_group" => "s")
								,'A'
								,'AK');						
		
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
		$objPdf->writeHeaderInfo($this->report_title, null, "T00001");
		$objPdf->writeSubheader(array("Account Number" => 8		//no of cells in actual excel
									 ,"Account Name" => 20
									 ,"Account Group" => 9)
								,array("Account Number" => "R"		
									 ,"Account Name" => "L"
									 ,"Account Group" => "L"));	
		
		$this->list = $this->getData($this->transaction_list);	
		$count = count($this->list);
		$objPdf->writeTableData($this->list
							   ,array("account_no" => 8		
									 ,"account_name" => 20
									 ,"account_group" => 9)
  							   ,array("account_no" => "R"		
									 ,"account_name" => "L"
									 ,"account_group" => "L"));															  	 
		$objPdf->writeTotals(array("account_no" => "Total:"		
									 ,"account_name" => $count." Account/s"
									 ,"account_group" => "")
							,array("account_no" => "L"		
									 ,"account_name" => "L"
									 ,"account_group" => "L")
							,array("account_no" => 8		
									 ,"account_name" => 20
									 ,"account_group" => 9));
									 
		$objPdf->Ln(.25);	
		$objPdf->writeFooter();
		$objPdf->Output($this->report_title."_".date("YmdHis"));
	}
	
	/**
	 * @desc Sets the data
	 */
	function setTableData() 
	{	
		$this->report_title = "Chart of Accounts Summary";
		$this->transaction_list = $this->read();
		if (count($this->transaction_list)==0)
			return true;
	}
	
	/**
	 * @desc Gets the data
	 */
	function getData($transaction_list) 
	{
		$list = array();
		foreach ($transaction_list AS $data) 
		{	
			$list[] = array(
						 "account_no"=>" ".$data["account_no"]
						,"account_name"=>$data["account_name"]
						,"account_group"=>$data["account_group"]
					  );	
		}	
		return $list;
	}
	
}

?>