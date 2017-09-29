<?php

/* Location: ./CodeIgniter/application/controllers/report_company.php */

class Report_company extends Asi_Controller 
{
	var $file_type;		//1 - excel ; 2 - pdf
	var $report_title;
	var $list;	

	function Report_company() 
	{
		parent::Asi_Controller();
		$this->load->model('company_model');
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
	 * @descTo retrieve all active companies.
	 * @return array
	 */
	function read()
	{
		$data = $this->company_model->get_list(
			array('status_flag' => '1'),
			null,
			null,
			array('company_code'
				 ,'company_name'),
			'company_code ASC');
			
		return $data['list'];
	}
	
	/**
	 * @desc Displays the report in Excel File
	 */
	function printExcel()
	{
		$row_start = 5;								 		//row after the header		
		$objExcel = new Asi_excel_ext("portrait");
		$objExcel->init(44,5);		
		$objExcel->writeHeaderInfo($this->report_title, "", "T00001");
		$objExcel->writeSubheader(
								array("Company Code" => 13		//no of cells in actual excel
									 ,"Company Name" => 24)
								,0
								,$row_start
								,12.75
								,array("Company Code" => "right"
									 ,"Company Name" => "left")
								,true
								,$row_start);				  				
		
		$this->list = $this->getData($this->transaction_list);	
		$count = count($this->list);
		$row_start++;	
		$objExcel->writeTableData(
								$this->list
								,array("company_code" => "right"		
									 ,"company_name" => "left")
								,array("company_code" => "s"		
									 ,"company_name" => "s")
								,$row_start);	
		$objExcel->writeTotals(
								array("company_code" => "Total:"		
									 ,"company_name" => $count." Companies")
								,array("company_code" => "left"		
									 ,"company_name" => "left")
								,array("company_code" => "s"		
									 ,"company_name" => "s")
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
		$objPdf->writeSubheader(array("Company Code" => 13		//no of cells in actual excel
									 ,"Company Name" => 24)
								,array("Company Code" => "R"
									 ,"Company Name" => "L"));	
		
		$this->list = $this->getData($this->transaction_list);	
		$count = count($this->list);
		$objPdf->writeTableData($this->list
							   ,array("company_code" => 13		
									 ,"company_name" => 24)
  							   ,array("company_code" => "R"		
									 ,"company_name" => "L"));															  	 
		$objPdf->writeTotals(array("company_code" => "Total:"		
								  ,"company_name" => $count. ($count == "1"?"Company":" Companies"))
							,array("company_code" => "L"		
								  ,"company_name" => "L")
							,array("company_code" => 13		
								  ,"company_name" => 24));
									 
		$objPdf->Ln(.25);	
		$objPdf->writeFooter();
		$objPdf->Output($this->report_title."_".date("YmdHis"));
	}
	
	/**
	 * @desc Sets the data
	 */
	function setTableData() 
	{	
		$this->report_title = "Company Summary";
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
						 "company_code"=>" ".$data["company_code"]
						,"company_name"=>$data["company_name"]
					  );	
		}	
		return $list;
	}
	
}

?>