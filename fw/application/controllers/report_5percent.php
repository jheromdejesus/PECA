<?php
/*
 * Created on May 4, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Report_subsidy_breakdown extends Asi_Controller {

 	var $file_type;		//1 - excel ; 2 - pdf
	
	var $report_date;
	var $report_title;
	var $list;	
	var $countEmp;
	
	var $total_principal_amount;
	var $total_loan_year_balance;
	var	$total_5_subsidy;
	var	$total_proportionate_interest;
	var	$total_total;
	var	$total_consol;
 	
	function Report_subsidy_breakdown(){
		parent::Asi_Controller();
		$this->load->model('subsidyreport_model2');
		$this->load->model('parameter_model');
		$this->load->helper('url');
		$this->load->library('constants');
		$this->load->library('asi_pdf_ext');
		$this->load->library('asi_excel_ext');
		$this->load->helper('date');	
	}
	
	function index(){		
		/*$_REQUEST['file_type'] = '2';
		$_REQUEST['month'] = '01';
		$_REQUEST['year'] = '2011';*/
		 
		$this->file_type = $_REQUEST['file_type'];
		
		$this->countEmp = 0;
		$this->total_principal_amount =0;
		$this->total_loan_year_balance =0;
		$this->total_5_subsidy =0;
		$this->total_proportionate_interest =0;
		$this->total_total =0;
		$this->total_consol =0;
			
		$this->report_date = $this->getMonth($_REQUEST['month'])." ".$_REQUEST['year'];
		$this->inputted_date = $_REQUEST['year'].$_REQUEST['month']."01";
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
	 * @desc Count the number of Employees
	 */
	function countEmployees()
	{
		$new =array();
		foreach($this->subsidy_list as $key=>$value)
			$new[$value['employee_id']] = $value['employee_id'];
		ksort($new);
		$empid ="";
		$countEmp =0;
		foreach($new as $key) {
			if($empid != $key)	{
				$countEmp++;
				$empid = $key;
			}
		}
		return $countEmp;
	}
	
	/**
	 * @desc read loans before and after the acctg period
	 */
	function read()
	{
		//$acctgPeriod = $this->parameter_model->retrieveValue('ACCPERIOD');
		$queryBefore = $this->readLoansBefore(/* $acctgPeriod */);
		//IBM report does not include those with loan dates >= to accounting period report was generated
		//$queryAfter = $this->readLoansAfter(/* $acctgPeriod */);
		$innerJoin = $queryBefore['query'];//.'  UNION  '.$queryAfter['query'];
		$data = $this->subsidyreport_model2->readAll(
													$innerJoin
													//[START] Modified by Kweeny Libutan for 8th Enhancement 2013/09/04
													,array('L.*'
														.',SUM(L.principal_amount) AS principal_amount_total'
														.',SUM(loan_year_balance) AS loan_year_balance_total'
														.',SUM(proportionate_interest) AS proportionate_interest_total'
														.',SUM(ROUND(((loan_year_balance *.05) / 12), 2)) AS 5_subsidy_total'));
													//[END] Modified by Kweeny Libutan for 8th Enhancement 2013/09/04
		log_message('debug', 'xx-xx :'.$data['query'].' xx-xx');
		/* echo $data['query'];
		exit();  */
		return $data['list'];
	}
	
	/**
	 * @desc Retrieve all loans after accounting period for subsidy report
	 */
	function readLoansAfter(/* $acctgPeriod */)
	{
		$acctgPeriod = $this->inputted_date;
		$params = '(m_loan.company_interest_amort > 0) 
					AND m_loan.close_flag = 0
					AND (m_loan.principal_balance >0) 
					AND (m_loan.loan_date>=\''.$acctgPeriod.'\')
					AND m_employee.member_status = \'A\'';
		$data = $this->subsidyreport_model2->getLoansAfterAcctgPeriod(
													$params
													,array(
														'm_loan.loan_no as loan_no'
														,'m_loan.loan_date as loan_date'
														, 'r_loan_header.loan_description'
														, 'm_loan.employee_id as employee_id'
														, 'm_employee.last_name as last_name' 							
														, 'm_employee.first_name as first_name' 
														, 'm_employee.member_status as member_status' 
														, 'm_loan.principal AS principal_amount'	
														, 'r_company.company_code'
														, 'r_company.company_name'
														, 'ROUND(m_Loan.Company_interest_rate*m_loan.initial_interest/m_loan.interest_rate,0) AS 5_subsidy'
														, 'COALESCE((
															SELECT SUM(amount) 
															FROM m_loan_payment 
															WHERE payment_date > (DATE_ADD(m_loan.amortization_startdate,INTERVAL (COALESCE((\''.$acctgPeriod.'\' - m_loan.amortization_startdate)/10000,0)) YEAR)) 
															AND m_loan_payment.loan_no = m_loan.loan_no),0) + principal_balance AS loan_year_balance'
														, '(CASE WHEN m_loan.amortization_startdate > \''.$acctgPeriod.'\' THEN initial_interest ELSE 0 END) AS proportionate_interest'
														, 'ROUND(m_Loan.Company_interest_rate*m_loan.initial_interest/m_loan.interest_rate,0) + (CASE WHEN m_loan.amortization_startdate > \''.$acctgPeriod.'\' THEN initial_interest ELSE 0 END)  AS total'
														
													));
		return $data;
	}
	
 	/**
	 * @desc Retrieve all loans before accounting period for subsidy report
	 *	
	 */
	function readLoansBefore(/* $acctgPeriod */)
	{
		$acctgPeriod = $this->inputted_date;
		//[START] Modified by Kweeny Libutan for 8th Enhancement 2013/09/05
		$acctgPeriodBSP = date("Ym", strtotime("$acctgPeriod -1 month"));
		//[END] Modified by Kweeny Libutan for 8th Enhancement 2013/09/05
		$params = '(m_loan.company_interest_amort > 0) 
					AND m_loan.close_flag = 0
					AND (LP.balance>0) 
					AND (m_loan.loan_date<\''.$acctgPeriod.'\'
					AND LP.loan_no IS NOT NULL 
					AND m_employee.member_status = \'A\'					
					)';
		$data = $this->subsidyreport_model2->getLoansBeforeAcctgPeriod(
													$acctgPeriod
													,$params
													,array(
														'm_loan.loan_no AS loan_no'
														,'m_loan.loan_date as loan_date'
														, 'r_loan_header.loan_description'
														, 'm_loan.employee_id AS employee_id'
														, 'm_employee.last_name as last_name' 							
														, 'm_employee.first_name as first_name' 
														, 'm_employee.member_status as member_status' 
														, 'm_loan.principal AS principal_amount'	
														, 'r_company.company_code'
														, 'r_company.company_name'
														, 'm_loan.company_interest_amort AS 5_subsidy'
														//[START] Added by Vincent Sy for 8th Enhancement 2013/08/02
														, 'COALESCE((CASE WHEN r_loan_header.bsp_computation = "Y" 
																	THEN mlp_bsp.balance
																	ELSE mlp.balance
																	END), principal_balance) AS loan_year_balance'
														//[END] Added by Vincent Sy for 8th Enhancement 2013/08/02
														, '(CASE WHEN m_loan.amortization_startdate > \''.$acctgPeriod.'\'  THEN initial_interest ELSE 0 END) AS proportionate_interest'
														, '`m_loan`.`company_interest_amort` + (CASE WHEN m_loan.amortization_startdate > \''.$acctgPeriod.'\' THEN initial_interest ELSE 0 END) AS total'		
													),
													$_REQUEST['year']
													//[START] Added by Vincent Sy for 8th Enhancement 2013/08/02
													,$acctgPeriodBSP);
													//[END] Added by Vincent Sy for 8th Enhancement 2013/08/02
		return $data;
	}
	
 	/**
	 * @desc Displays the report in Excel File
	 */
	function printExcel()
	{
		$row_start = 6;											 		//row after the header		
			
		$objExcel = new Asi_excel_ext();
		$objExcel->init(50,7);		
		$objExcel->writeHeaderInfo($this->report_title, "for the month of ".$this->report_date, "L0004");
		
		$this->list = $this->getData($this->subsidy_list);
		
		$objExcel->writeSubheader(array("Employee ID" => 4		//no of cells in actual excel
												,"Members" => 10
												//,"Principal Amount" => 4
												//,"Loan Year Balance" => 4
												//,"5% P&G Subsidy" => 4
												//,"Proportionate Interest" => 4
												//,"TOTAL" => 4
												,"Consol" => 4
												)
										  ,0
										  ,$row_start
										  ,12.75
										  ,array("Employee ID" => "center"
										      ,"Members" => "left"
											  //,"Principal Amount" => "right"
											  //,"Loan Year Balance" => "right"
											  //,"5% P&G Subsidy" => "right"
											  //,"Proportionate Interest" => "right"
											  //,"TOTAL" => "right"
											  ,"Consol" => "right"
											 )
										  ,true
										  ,$row_start = $row_start +1 
										  );				  				
		$objExcel->writeTableData($this->list
  									  ,array("employee_id" => "center"
										      ,"employee_name" => "left"
											  //,"principal_amount" => "right"
											  //,"loan_year_balance" => "right"
											  //,"5_subsidy" => "right"
											  //,"proportionate_interest" => "right"
											  //,"total" => "right"
											  ,"consol" => "right"
											 )
									  ,array("employee_id" => "s"
											  ,"employee_name" => "s"
											  //,"principal_amount" => "#,##0.00"
											  //,"loan_year_balance" => "#,##0.00"
											  //,"5_subsidy" => "#,##0.00"
											  //,"proportionate_interest" => "#,##0.00"
											  //,"total" => "#,##0.00"
											  ,"consol" => "#,##0.00"
											  )	
									  ,$row_start = $row_start+1);	
		
		
		$total2 = array("total" => "left"
					      ,"total_emp" => "left"
						  //,"total_principal_amount" => "right"
						  //,"total_loan_year_balance" => "right"
						  //,"total_5_subsidy" => "right"
						  //,"total_proportionate_interest" => "right"
						  //,"total_total" => "right"
						  ,"total_consol" => "right");
		$total3 = array("total" => "s"
						  ,"total_emp" => "#,##0"
						 // ,"total_principal_amount" => "#,##0.00"
						  //,"total_loan_year_balance" => "#,##0.00"
						  //,"total_5_subsidy" => "#,##0.00"
						 // ,"total_proportionate_interest" => "#,##0.00"
						 // ,"total_total" => "#,##0.00"
						  ,"total_consol" => "#,##0.00"
						  );

		$objExcel->writeTotals(array("total" => "Total"
											,"total_emp" => $this->countEmp ."    Employee(s)"
											//,"total_principal_amount" =>number_format($this->total_principal_amount,2,'.',',') 
											//,"total_loan_year_balance" =>number_format($this->total_loan_year_balance,2,'.',',') 
											//,"total_5_subsidy" =>number_format($this->total_5_subsidy,2,'.',',') 
											//,"total_proportionate_interest" => number_format($this->total_proportionate_interest,2,'.',',')
											//,"total_total" =>number_format($this->total_total,2,'.',',')
											,"total_consol" => number_format($this->total_consol,2,'.',',')
											)
									,$total2
									,$total3
									,'A' 	  					//first column name:used for border
									,'AK');	  				//end column name:used for border
		
		
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
		$objPdf->writeHeaderInfo($this->report_title, "for the month of ".$this->report_date, "L0004");
		
		$this->list = $this->getData($this->subsidy_list);
						
		$objPdf->writeSubheader(array("Employee ID" => 6	
									//,"Loan Anniversary" => 6
									,"Members" => 10
									//,"Principal Amount" => 6
									//,"Loan Year Balance" => 6
									//,"5% P&G Subsidy" => 6
									//,"Proportionate Interest" => 6
									//,"TOTAL" => 5
									,"Consol" => 6
									)
								,array("Employee ID" => "C"
									  //,"Loan Anniversary" => "C"
								      ,"Members" => "L"
									  //,"Principal Amount" => "R"
									 // ,"Loan Year Balance" => "R"
									  //,"5% P&G Subsidy" => "R"
									  //,"Proportionate Interest" => "R"
									  //,"TOTAL" => "R"
									  ,"Consol" => "R"
									 )
								,.9
							);				
		$count = $objPdf->writeTableData($this->list
								  ,array("employee_id" => 6
										  //,"anniv_date" => 6
										  ,"employee_name" => 10
										  //,"principal_amount" => 6
										 // ,"loan_year_balance" => 6
										  //,"5_subsidy" => 6
										  //,"proportionate_interest" => 6
										  //,"total" => 5
										  ,"consol" => 6)
  								  ,array("employee_id" => "C"
											  //,"anniv_date" => "C"
										      ,"employee_name" => "L"
											  //,"principal_amount" => "R"
											  //,"loan_year_balance" => "R"
											  //,"5_subsidy" => "R"
											  //,"proportionate_interest" => "R"
											  //,"total" => "R"
											  ,"consol" => "R"
											 )
									,0.9, 0.15, 60, 7);																  	 
		$total2 = array("total" => "L" 
						  //,"total2" => "L" 	
					      ,"total_emp" => "L"
						  //,"total_principal_amount" => "R"
						  //,"total_loan_year_balance" => "R"
						  //,"total_5_subsidy" => "R"
						  //,"total_proportionate_interest" => "R"
						  //,"total_total" => "R"
						  ,"total_consol" => "R");
		$total3 = array("total" => 6
				            //,"total2" => 6
							,"total_emp" => 10
							//,"total_principal_amount" => 6
							//,"total_loan_year_balance" => 6
							//,"total_5_subsidy" => 6
							//,"total_proportionate_interest" => 6
							//,"total_total" => 5
							,"total_consol" => 6
							);			
		$objPdf->writeTotals(array("total" => "Total"
											//,"total2" => ""
											,"total_emp" => $this->countEmp ."   Employee(s)"
											//,"total_principal_amount" =>number_format($this->total_principal_amount,2,'.',',') 
											//,"total_loan_year_balance" =>number_format($this->total_loan_year_balance,2,'.',',') 
											//,"total_5_subsidy" =>number_format($this->total_5_subsidy,2,'.',',') 
											//,"total_proportionate_interest" => number_format($this->total_proportionate_interest,2,'.',',')
											//,"total_total" =>number_format($this->total_total,2,'.',',')
											,"total_consol" => number_format($this->total_consol,2,'.',',')
											)
										,$total2
										,$total3
										,.9);
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
			//$this->report_title = "5% Subsidy Report";		
			//[START] Modified by Vincent Sy for 8th Enhancement 2013/07/03
			$this->report_title = "P&G Subsidy for Housing Loans";
			//[END] Modified by Vincent Sy for 8th Enhancement 2013/07/03
			$this->subsidy_list = $this->read();
			if (count($this->subsidy_list)==0)
				return true;	
	}
	
 	/**
	 * @desc Gets the data
	 */
	function getData($arr) 
	{
		$emp_id = '0';
		$consol = 0;
		$fconsol = 0;
		$prev_emp = '0';
		$employee_count = 1;
		$list = array();
		foreach($arr as $data)
		{
			$last_name = $data['last_name'];
			$first_name = $data['first_name'];
			
			//$consol = $this->getConsol($data['employee_id']);
			
			$list[] = array("employee_id"=>$data["employee_id"]
						 //,"anniv_date" => date("m/Y", strtotime($anniv_date))
						 ,"employee_name"=>$last_name.", ".$first_name
						 //,"principal_amount"=>number_format($data["principal_amount"],2,'.',',')
						 //,"loan_year_balance"=>number_format($data["loan_year_balance"],2,'.',',')
						 //,"5_subsidy"=>number_format($five_subsidy,2,'.',',')
						 //,"proportionate_interest"=>""
						 //,"total"=>number_format($five_subsidy,2,'.',',')
						 ,"consol"=>number_format($data["5_subsidy_total"],2,'.',','));
					 	
			$this->total_principal_amount += $data["principal_amount_total"];
			$this->total_loan_year_balance += $data["loan_year_balance_total"];
			$this->total_5_subsidy += $data["5_subsidy_total"];
			$this->total_proportionate_interest +=$data["proportionate_interest_total"];
			$this->total_total = $this->total_5_subsidy;//$data["total"];	
		}
		$this->countEmp = $this->countEmployees();
		$this->total_consol = $this->total_5_subsidy;//$this->total_principal_amount;
	
		return $list;
	}
	
	function getMonth($mo)
	{
		$mo = $mo % 12;
		$ret = "";
		switch($mo)
		{
		case 1: {$ret = 'January'; break;}
		case 2: {$ret = 'February'; break;}
		case 3: {$ret = 'March'; break;}
		case 4: {$ret = 'April'; break;}
		case 5: {$ret = 'May'; break;}
		case 6: {$ret = 'June'; break;}
		case 7: {$ret = 'July'; break;}
		case 8: {$ret = 'August'; break;}
		case 9: {$ret = 'September'; break;}
		case 10: {$ret = 'October'; break;}
		case 11: {$ret = 'November'; break;}
		case 0: {$ret = 'December'; break;}
		}
		
		return $ret;
	}
	
	function getConsol($employee_id)
	{
		$data = $this->subsidyreport_model2->retrieveConsolPrinAmount1($employee_id,$this->inputted_date);
		$consol1 = $data['list'][0]['consol'];
		
		$data2 = $this->subsidyreport_model2->retrieveConsolPrinAmount2($employee_id,$this->inputted_date);
		$consol2 = $data2['list'][0]['consol'];

		return $consol1+$consol2;
	}
}
?>