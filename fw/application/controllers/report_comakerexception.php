<?php
/*
 * Created on May 15, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Report_comakerexception extends Asi_Controller {
	var $file_type;		//1 - excel ; 2 - pdf
	
	var $report_date;
	var $report_title;
	var $list;	
	
	function Report_comakerexception(){
		parent::Asi_Controller();
		$this->load->model('comakerexceptionreport_model');
		$this->load->model('parameter_model');
		$this->load->model('loansuspendedborrowersreport_model');
		$this->load->helper('url');
		$this->load->library('constants');
		$this->load->library('asi_pdf_ext');
		$this->load->library('asi_excel_ext');
		$this->load->helper('date');	
	}
	
	function index(){	
		 
		$this->report_date = date("Ymd");	
		$result = $this->setTableData();	
		
		if ($result == true) {
			echo("{'success':false,'msg':'No records found.','error_code':'19'}");	
		}
		else {
			$this->printExcel();
		}
	}
	
	function readAll()
	{
		$sql1 = $this->readLoanNumOfGuarantorException();
		$sql2 = $this->readCoMakerNoOfLoansException();
		$sql3 = $this->readCOMakersMembersLoanException();
		$sql4 = $this->readInactiveCoMakers(); 
		
		$data = $this->comakerexceptionreport_model->getAll($sql1, $sql2, $sql3, $sql4);
		
		/* echo $data['query'];
		exit(); */
		return $data['list'];
	}
	
 	/**
	 * @desc Retrieve loans that violated the required number of guarantors with regards to the YOS of  member who loans
	 */
	function readLoanNumOfGuarantorException()
	{
		$curr_date = date("Ymd");
		$sql = $this->comakerexceptionreport_model->getLoanNumOfGuarantorException($curr_date);
		return $sql;
	}
	
	/**
	 * @desc Retrieve guarantors who exceeds the required number of loans guaranteed with regards to the guarantors'  YOS
	 */
	function readCoMakerNoOfLoansException()
	{
		$curr_date = date("Ymd");
		$sql = $this->loansuspendedborrowersreport_model->getCoMakerNoOfLoansException($curr_date);
		return $sql;
	}
	
	/**
	 * @desc Retrieve guarantors that guaranteed more than one Consumption Loan or Spot Cash Loan to the same employee
	 */
	function readCOMakersMembersLoanException()
	{
		$sql = $this->loansuspendedborrowersreport_model->getCOMakersMembersLoanException();
		return $sql;
	}
	
	/**
	 * @desc Retrieve guarantors that guaranteed more than one Consumption Loan or Spot Cash Loan to the same employee
	 */
	function readInactiveCoMakers()
	{
		$sql = $this->comakerexceptionreport_model->getInactiveCoMakers();
		return $sql;
	}
	
 	/**
	 * @desc Displays the report in Excel File
	 */
	function printExcel()
	{
		$row_start = 7;											 		//row after the header		
			
		$objExcel = new Asi_excel_ext();
		$objExcel->init(44,8);			
		$objExcel->writeHeaderInfo($this->report_title, "for the period ".date("F Y", strtotime($this->report_date)), "L0007");
		$objExcel->writeSubheader(array("Loan No." =>3
												,"Loan Code" => 3		//no of cells in actual excel
												,"Employee ID" => 4
												,"Last Name" => 5
												,"First Name" => 6
												,"Principal Balance" => 5
												,"Co-Maker ID" => 4
												,"Co-Maker Last Name" => 5
												,"Co-Maker First Name" => 5
												,"Remarks" => 25
												)
										  ,0
										  ,$row_start
										  ,12.75
										  ,array("Loan No." => "left"
										  	  ,"Loan Code" => "left"
										      ,"Employee ID" => "right"
											  ,"Last Name" => "left"
											  ,"First Name" => "left"
										      ,"Principal Balance" => "right"
											  ,"Co-Maker ID" => "right"
											  ,"Co-Maker Last Name" => "left"
										      ,"Co-Maker First Name" => "left"
											  ,"Remarks" => "left"
											  )
										  ,true
										  ,$row_start = $row_start +1 
										  );
		$this->list = $this->getData($this->comakerexception_list);
		$count = (count($this->list))+1;
		$objExcel->writeTableData($this->list
								  ,array("loan_no"=>"left"
										  ,"loan_code" => "left"
										  ,"employee_id" => "right"
										  ,"last_name" => "left"
										  ,"first_name" => "left"
										  ,"principal_balance" => "right"
										  ,"guarantor_id" => "right"
										  ,"guarantor_last_name" => "left"
										  ,"guarantor_first_name" => "left"
										  ,"remark" => "left"
										  )
								  ,array("loan_no" => "s"
										  ,"loan_code" => "s"
										  ,"employee_id" => "s"
										  ,"last_name" => "s"
										  ,"first_name" => "s"
										  ,"principal_balance" => "#,##0.00"
										  ,"guarantor_id" => "s"
										  ,"guarantor_last_name" => "s"
										  ,"guarantor_first_name" => "s"
										  ,"remark" => "s"
										  )	
								  ,$row_start = $row_start+1);	
			
		$row_start = $row_start +$count-2;
		
	
		$objExcel->writeFooter();								
		$objWriter = new PHPExcel_Writer_Excel5($objExcel->php_excel_obj);
		$filename = str_replace(" ", "_", $this->report_title)."_".date("YmdHis").".xls";
		$objExcel->outputExcel($filename);
		$objWriter->save("php://output");
	}
	
 	/**
	 * @desc Sets the data
	 */
	function setTableData() 
	{	
		$this->report_title = "Comaker Exception Report";		
		$this->comakerexception_list = $this->readAll();
		if (count($this->comakerexception_list)==0)
			return true;	
		
	}
	
 	/**
	 * @desc Gets the data
	 */
	function getData($arr) 
	{
		$list = array();
		foreach($arr as $data)
		{
			$list[] = array("loan_no"=>" ".$data["loan_no"]
							,"loan_code"=>$data["loan_code"]
							,"employee_id"=>" ".$data['employee_id']
							,"last_name" =>$data["last_name"]
							,"first_name" => $data["first_name"]
							,"principal_balance"=>number_format($data["principal_balance"],2,'.',',')
							,"guarantor_id" => " ".$data["guarantor_id"]
							,"guarantor_last_name" => $data["guarantor_last_name"]
							,"guarantor_first_name" => $data["guarantor_first_name"]
							,"remark" =>$data["remark"]
							);		 	
		}
		return $list;
	}
	
}
?>
