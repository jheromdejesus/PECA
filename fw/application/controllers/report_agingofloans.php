<?php
/*
 * Created on May 12, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Report_agingofloans extends Asi_Controller {

 	var $file_type;		//1 - excel ; 2 - pdf
	
	var $report_date;
	var $report_title;
	var $list;	
	var $total_principal;
	var $total_current;
 	var $total_non_current;
 	//updated by lap UAT 08042016
 	var $grand_principal;
 	var $grand_current;
 	var $grand_non_current;

	function Report_agingofloans(){
		parent::Asi_Controller();
		$this->load->model('agingofloansreport_model');
		$this->load->model('parameter_model');
		$this->load->helper('url');
		$this->load->library('constants');
		$this->load->library('asi_pdf_ext');
		$this->load->library('asi_excel_ext');
		$this->load->helper('date');	
	}
	
	function index(){	
		$this->total_principal= 0;
		$this->total_current = 0;
		$this->total_non_current = 0;
		set_time_limit(0);	
		$this->report_date = $this->parameter_model->getParam('CURRDATE');
		$result = $this->setTableData();	
		
		if ($result == true) {
			echo("{'success':false,'msg':'No records found.','error_code':'19'}");	
		}
		else {
			$this->printExcel();
		}
	}
	
 	/**
	 * @desc Retrieve all active loans with maximum loan term of more than 12
	 */
	function read()
	{
		$data = $this->agingofloansreport_model->getAgingOfLoans($this->loan_desc);
		return $data['list'];
	}
	
 	/**
	 * @desc Retrieve list of loan descriptions to be used in read
	 */
	function readDesc()
	{
		$data = $this->agingofloansreport_model->getDesc();
		return $data['list'];
	}
	
 	/**
	 * @desc Displays the report in Excel File
	 */
	function printExcel()
	{
		$row_start = 6;											 		//row after the header		
		$objExcel = new Asi_excel_ext();
		$objExcel->init(60,7);		
		$objExcel->writeHeaderInfo($this->report_title, "for the period ".date("F Y",strtotime($this->report_date)), "L0008");
		$total2 = array("loan_no" => "left"
					      ,"employee_id" => "right"
					      ,"last_name" => "left"
						  ,"first_name" => "left"
						  ,"loan_code" => "left"
						  ,"loan_date" => "center"
						  ,"principal" => "left"
						  ,"term" => "right"
						  ,"interest_rate" => "right"
						  ,"total" => "left"
						  ,"Company_Int_Amortization" => "right"
						  ,"Amortization_Start_Date" => "right"
						  ,"total_Principal_Balance" => "right"
						  ,"Int_Balance" => "right"
						  ,"total_Current" => "right"
						  ,"total_Non_Current" => "right");
		$total3 = array("loan_no" => "s"
						  ,"employee_id" => "s"
						  ,"last_name" => "s"
						  ,"first_name" => "s"
						  ,"loan_code" => "s"
						  ,"loan_date" => "s"
						  ,"principal" => "s"
						  ,"term" => "s"
						  ,"interest_rate" => "#,##0.00"
						  ,"total" => "#,##0.00"
						  ,"Company_Int_Amortization" => "#,##0.00"
						  ,"Amortization_Start_Date" => "s"
						  ,"total_Principal_Balance" => "#,##0.00"
						  ,"Int_Balance" => "#,##0.00"
						  ,"total_Current" => "#,##0.00"
						  ,"total_Non_Current" => "#,##0.00");
		$objExcel->writeSubheader(array("Loan No" => 3		//no of cells in actual excel
									,"Employee ID" => 4
									,"Last Name" => 5
									,"First Name" => 6
									,"Loan Code" => 3
									,"Loan Date" => 5
									,"Principal" => 5
									,"Term" => 2
									,"Interest Rate" => 6
									,"Employee Principal Amortization" => 6
									,"Company Interest Amortization" => 6
									,"Amortization Start Date" => 5
									,"Principal Balance" => 6
									,"Interest Balance" => 6
									,"Current" => 5
									,"Non-Current" => 5)
							  ,0
							  ,$row_start
							  ,12.75
							  ,array("Loan No" => "left"
								      ,"Employee ID" => "right"
									  ,"Last Name" => "left"
									  ,"First Name" => "left"
									  ,"Loan Code" => "left"
									  ,"Loan Date" => "center"
									  ,"Principal" => "right"
									  ,"Term" => "right"
									  ,"Interest Rate" => "right"
									  ,"Employee Principal Amortization" => "right"
									  ,"Company Interest Amortization" => "right"
									  ,"Amortization Start Date" => "center"
									  ,"Principal Balance" => "right"
									  ,"Interest Balance" => "right"
									  ,"Current" => "right"
									  ,"Non-Current" => "right")
							  );
		$row_start = $row_start+1;
		foreach ($this->agingofloanscode_list AS $data) 
		{
			$this->loan_desc = $data["loan_description"];
			$objExcel->writeSubheaderTitle($this->loan_desc, 0, $row_start = $row_start+1);
			$this->agingofloans_list = $this->read();
			$this->list = $this->getData($this->agingofloans_list);
			$count = (count($this->list))+1;					
			$objExcel->writeTableData($this->list
  									  ,array("loan_no" => "left"
										      ,"employee_id" => "right"
										      ,"last_name" => "left"
											  ,"first_name" => "left"
											  ,"loan_code" => "left"
											  ,"loan_date" => "center"
											  ,"principal" => "right"
											  ,"term" => "right"
											  ,"interest_rate" => "right"
											  ,"Employee_Principal_Amortization" => "right"
											  ,"Company_Int_Amortization" => "right"
											  ,"Amortization_Start_Date" => "center"
											  ,"Principal_Balance" => "right"
											  ,"Int_Balance" => "right"
											  ,"Current" => "right"
											  ,"Non_Current" => "right")
									  ,array("loan_no" => "s"
											  ,"employee_id" => "s"
											  ,"last_name" => "s"
											  ,"first_name" => "s"
											  ,"loan_code" => "s"
											  ,"loan_date" => "s"
											  ,"principal" => "#,##0.00"
											  ,"term" => "s"
											  ,"interest_rate" => "#,##0.00"
											  ,"Employee_Principal_Amortization" => "#,##0.00"
											  ,"Company_Int_Amortization" => "#,##0.00"
											  ,"Amortization_Start_Date" => "s"
											  ,"Principal_Balance" => "#,##0.00"
											  ,"Int_Balance" => "#,##0.00"
											  ,"Current" => "#,##0.00"
											  ,"Non_Current" => "#,##0.00")		
									  ,$row_start = $row_start+1);	
			
			$objExcel->writeTotals(array("loan_no" => ""
									  ,"employee_id" => ""
									  ,"last_name" => ""
									  ,"first_name" => ""
									  ,"loan_code" => ""
									  ,"loan_date" => ""
									  ,"principal" => ""
									  ,"term" => ""
									  ,"interest_rate" => ""
									  ,"total" => "Total"
									  ,"Company_Int_Amortization" => ""
								      ,"Amortization_Start_Date" => ""
									  ,"total_Principal_Balance" => number_format($this->total_principal,2,'.',',')
									  ,"Int_Balance" => ""
									  ,"total_Current" => number_format($this->total_current,2,'.',',')
									  ,"total_Non_Current" => number_format($this->total_non_current,2,'.',','))
									,$total2
									,$total3
									,'A' 	  					//first column name:used for border
									,'BC');	 				//end column name:used for border
			$row_start = ($row_start+$count);
			$this->grand_principal += $this->total_principal;
			$this->grand_current += $this->total_current;				 
			$this->grand_non_current += $this->total_non_current;
		}
		$row_start++;
		$objExcel->writeTableData(array(array("loan_no" => ""
									  ,"employee_id" => ""
									  ,"last_name" => ""
									  ,"first_name" => ""
									  ,"loan_code" => ""
									  ,"loan_date" => ""
									  ,"principal" => "RECAP:"
									  ,"term" => ""
									  ,"interest_rate" => ""
									  ,"total" => ""
									  ,"Company_Int_Amortization" => ""
									  ,"Amortization_Start_Date" => ""
									  ,"total_Principal_Balance" => ""
									  ,"Int_Balance" => ""
									  ,"total_Current" => ""
									  ,"total_Non_Current" => ""))
									,$total2
									,$total3
									,$row_start+=1);					//end column name:used for border
		foreach ($this->agingofloanscode_list AS $data) 
		{
			$this->loan_desc = $data["loan_description"];
			$this->agingofloans_list = $this->read();
			$this->list = $this->getData($this->agingofloans_list);
			$objExcel->writeTableData(array(array("loan_no" => ""
									  ,"employee_id" => ""
									  ,"last_name" => ""
									  ,"first_name" => ""
									  ,"loan_code" => ""
									  ,"loan_date" => ""
									  ,"principal" => ""
									  ,"term" => ""
									  ,"interest_rate" => ""
									  ,"total" => $this->loan_desc
									  ,"Company_Int_Amortization" => ""
									  ,"Amortization_Start_Date" => ""
									  ,"total_Principal_Balance" => number_format($this->total_principal,2,'.',',')
									  ,"Int_Balance" => ""
									  ,"total_Current" => number_format($this->total_current,2,'.',',')
									  ,"total_Non_Current" => number_format($this->total_non_current,2,'.',',')))
									,$total2
									,$total3
									,$row_start = $row_start+1);	
		}
		$objExcel->writeTotals(array("loan_no" => ""
									  ,"employee_id" => ""
									  ,"last_name" => ""
									  ,"first_name" => ""
									  ,"loan_code" => ""
									  ,"loan_date" => ""
									  ,"principal" => ""
									  ,"term" => ""
									  ,"interest_rate" => ""
									  ,"total" => "Grand Total"
									  ,"Company_Int_Amortization" => ""
									  ,"Amortization_Start_Date" => ""
									  ,"total_Principal_Balance" => number_format($this->grand_principal,2,'.',',')
									  ,"Int_Balance" => ""
									  ,"total_Current" => number_format($this->grand_current,2,'.',',')
									  ,"total_Non_Current" => number_format($this->grand_non_current,2,'.',','))
									,$total2
									,$total3
									,'AM' 	  					//first column name:used for border
									,'BB');	 				//end column name:used for border
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
		$this->report_title = "Aging of Loans Report";		
		$this->agingofloanscode_list = $this->readDesc();
		if (count($this->agingofloanscode_list)==0)
			return true;	
	}
	
 	/**
	 * @desc Gets the data
	 */
	function getData($agingofloans_list) 
	{
		$list = array();
		$this->total_principal =0;
		$this->total_current =0;				 
		$this->total_non_current =0;
		
		foreach($agingofloans_list AS $data)
		{
			$list[] = array("loan_no"=>" ".$data["loan_no"]
							 ,"employee_id"=>" ".$data["employee_id"]
							 ,"last_name"=>$data["last_name"]
							 ,"first_name"=>$data["first_name"]
							 ,"loan_code"=>$data['loan_code']
							 ,"loan_date"=>date("m/d/Y", strtotime($data["loan_date"]))
							 ,"principal"=>number_format($data["principal"],2,'.',',')
							 ,"term"=>" ".$data["term"]
							 ,"interest_rate"=>$data["interest_rate"]
							 ,"Employee_Principal_Amortization"=>number_format($data["Employee_Principal_Amortization"],2,'.',',')
							 ,"Company_Int_Amortization" => number_format($data["Company_Int_Amortization"],2,'.',',')
							 ,"Amortization_Start_Date" => date("m/d/Y", strtotime($data["Amortization_Start_Date"]))
							 ,"Principal_Balance"=>number_format($data["Principal_Balance"],2,'.',',')
							 ,"Int_Balance"=>number_format($data["Int_Balance"],2,'.',',') 
							 ,"Current"=>number_format($data["Current"],2,'.',',')
							 ,"Non_Current"=>number_format($data["Non_Current"],2,'.',','));		 	
							 
			$this->total_principal += $data["Principal_Balance"];
			$this->total_current += $data["Current"];				 
			$this->total_non_current += $data["Non_Current"];
		}
		
		return $list;
	}
}
?>
