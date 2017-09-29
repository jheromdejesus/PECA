<?php
/*
 * Created on May 19, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Report_capconbalancelist extends Asi_Controller {

 	var $file_type;		//1 - excel ; 2 - pdf
	var $report_date;
	var $report_title;
	var $list;	
	
	function Report_capconbalancelist(){
		parent::Asi_Controller();
		$this->load->model('capcon_model');
		$this->load->model('parameter_model');
		$this->load->helper('url');
		$this->load->library('constants');
		$this->load->library('asi_pdf_ext');
		$this->load->library('asi_excel_ext');
		$this->load->helper('date');	
	}
	
	function index()
	{		
		/* $_REQUEST['file_type'] = '1';
		$_REQUEST['report_date'] = '06/01/2010';
		$_REQUEST['amount_from'] = 0;
		$_REQUEST['amount_to'] = 1000; */ 

		if(substr($_REQUEST['report_date'], 3, 2) != '01')
			echo("{'success':false,'msg':'Please enter a valid date. It must be the first day of the month.','error_code':'19'}");	
		/* else if($_REQUEST['amount_to'] < $_REQUEST['amount_from'] )
			echo("{'success':false,'msg':'Amount To must be greater than or equal to Amount From.','error_code':'19'}"); */	
		else {
			$this->file_type = $_REQUEST['file_type'];
			
			$this->report_date = date("F j, Y", strtotime($_REQUEST['report_date'])) . " to ".date("F j, Y", strtotime($_REQUEST['report_date']));
			$this->amount_from = $_REQUEST['amount_from'];
			$this->amount_to = $_REQUEST['amount_to'];
			
			$result = $this->setTableData();	
			
			if ($result == true) {
				echo("{'success':false,'msg':'No records found.','error_code':'19'}");	
			}
			else {
				if ($this->file_type=='1') $this->printExcel();
				else $this->printPDF();
			}
		}
	}
	
	/**
	 * @desc To retrieve average capital contribution from $start_date to $end_date
	 */
	function readCapConBalance()
	{
		//$acctgPeriod = $this->parameter_model->retrieveValue('ACCPERIOD');
		$acctgPeriod = date("Ymd",strtotime($_REQUEST['report_date']));
		$params = "tcc.accounting_period = '$acctgPeriod'
					AND tcc.ending_balance BETWEEN $this->amount_from AND $this->amount_to
					AND mm.member_status = 'A'";
		$data = $this->capcon_model->retrieveCapConBalance(
													$params
													,array('mm.employee_id AS employee_id'
														, 'mm.last_name AS last_name'
														, 'mm.first_name AS first_name'
														, 'tcc.ending_balance AS balance'
														, 'mm.company_code AS company_code'
														, 'mm.department AS department')
													,'mm.employee_id, mm.last_name, mm.first_name ASC');
		
		return $data['list'];
	}
	
	/**
	 * @desc Displays the report in Excel File
	 */
	function printExcel()
	{
		$row_start = 7;											 		//row after the header		
			
		$objExcel = new Asi_excel_ext();
		$objExcel->init(40,7);		
		$objExcel->writeHeaderInfo($this->report_title, $this->report_date, "M0001", "Amount From ".number_format($this->amount_from,2,'.',',')." to ".number_format($this->amount_to,2,'.',',') );
		$this->list = $this->getData($this->capconbal_list);
		$objExcel->writeSubheader(array("Employee ID" => 6		//no of cells in actual excel
											,"Employee Name" => 15
											,"Balance" =>8
											,"Company Code" => 12
											)
										  ,0
										  ,$row_start
										  ,12.75
										  ,array("Employee ID" => "right"
										      ,"Employee Name" => "left"
											  ,"Balance" => "right"
											  ,"Company Code" => "left"
											 )
										  ,true
										  ,$row_start
										  );				  				
		$objExcel->writeTableData($this->list
  									  ,array("employee_id" => "right"
										      ,"employee_name" => "left"
											  ,"balance" => "right"
											  ,"company_code" => "left"
											  )
									  ,array("employee_id" => "s"
											  ,"employee_name" => "s"
											  ,"balance" => "#,##0.00"
											  ,"company_code" => "s"
											  )	
									  ,$row_start = $row_start+1);
		
		$objExcel->writeCustomizedTotals(array("Total Number of Employees;" => 9
								,"$this->countEmp Employee(s)" => 11
								," " => 21)
							,array("Total Number of Employees;" => "left"
								  ,"$this->countEmp Employee(s)" => "left"
								  ," " => "right")
							,array("Total Number of Employees;" => "s"
								  ,"$this->countEmp Employee(s)" => "s"
								  ," " =>"s")
							,'A', 'AN');	
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
		$objPdf->init("portrait");	
		$objPdf->writeHeaderInfo($this->report_title, $this->report_date, "M0001", "Amount From ".number_format($this->amount_from,2,'.',',')." to ".number_format($this->amount_to,2,'.',','));
		$this->list = $this->getData($this->capconbal_list);
		$objPdf->writeSubheader(array("Employee ID" => 6		//no of cells in actual excel
									,"Employee Name" => 15
									,"Balance" =>8
									,"Company Code" => 10
									)
							,array("Employee ID" => "R"
									  ,"Employee Name" => "L"
									  ,"Balance" => "R"
									  ,"Company Code" => "L"
									 ));				
		$count = $objPdf->writeTableData($this->list
								  ,array("employee_id" => 6
										  ,"employee_name" => 15
										  ,"balance" =>8
										  ,"company_code" => 10)
  								  ,array("employee_id" => "R"
										      ,"employee_name" => "L"
											  ,"balance" => "R"
											  ,"company_code" => "L"
											  ));	
		$objPdf->writeTotals(array("total" => "Total Number of Employees:"
								,"total_emp" => $this->countEmp ." Employee(s)"
								,"balance" => ""
								,"company_code" => "")
							,array("total" => "L"
								  ,"total_emp" => "L"
								  ,"balance" => "R"
								  ,"company_code" => "R")
							,array("total" => 9
								  ,"total_emp" => 11
								  ,"balance" =>7
								  ,"company_code" => 12));
		$objPdf->Ln(.25);
					
		if ($count!=20) { 
			$objPdf->writeFooter();
		}	
		$objPdf->Output($this->report_title."_".date("YmdHis"));	
	}
	
 	/**
	 * @desc Sets the data
	 */
	function setTableData() 
	{	
		$this->report_title = "CapCon Balance List";		
		$this->capconbal_list = $this->readCapConBalance();
		if (count($this->capconbal_list)==0)
			return true;	
	}
	
 	/**
	 * @desc Gets the data
	 */
	function getData($arr) 
	{
		$list = array();
		$this->countEmp =0;
		foreach($arr as $data)
		{
			$list[] = array("employee_id"=>" ".$data['employee_id']
							 ,"employee_name"=>$data['last_name'].", ".$data['first_name']
							 ,"balance"=>number_format($data['balance'],2,'.',',')
							 ,"company_code"=> array_key_exists('company_code', $data) ? $data["company_code"]." - ".$data['department'] : " "
							 //,"company_code"=> $data["company_code"]
							);
			$this->countEmp += 1;
		}
		//echo "<pre>";print_r($list);echo "</pre>";exit();
		return $list;
	}
}
?>
