<?php
/*
 * Created on May 5, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Report_amortunearnedinterest extends Asi_Controller {

 	var $file_type;		//1 - excel ; 2 - pdf
	
	var $report_date;
	var $report_title;
	var $list;	
	var $countEmp;
	
	var $total_principal;
	var $total_term;
	var $total_interest_amount;
	
	var $month1;
	var $month2;
	var $month3;
	var $month4;
	var $month5;
	var $month6;
	var $month7;
	var $month8;
	var $month9;
	var $month10;
	
	var $total_total;
 	
	function Report_amortunearnedinterest(){
		parent::Asi_Controller();
		$this->load->model('amortizationunearnedinterestreport_model');
		$this->load->helper('url');
		$this->load->library('constants');
		$this->load->library('asi_pdf_ext');
		$this->load->library('asi_excel_ext');
		$this->load->helper('date');	
	}
	
	function index(){		
		$this->file_type = $_REQUEST['file_type'];
		
		$this->countEmp = 0;
		
		$this->total_principal=0;
		$this->total_term=0;
		$this->total_interest_amount=0;
		
		$this->month1=0;
		$this->month2=0;
		$this->month3=0;
		$this->month4=0;
		$this->month5=0;
		$this->month6=0;
		$this->month7=0;
		$this->month8=0;
		$this->month9=0;
		$this->month10=0;
		
		$this->total_total=0;
			
		$this->report_date = $_REQUEST['report_date'];
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
	 * @desc read loans before and after the acctg period
	 */
	function read()
	{
		$_REQUEST['loan_date'] = date("Ym", strtotime($_REQUEST['report_date']));
								
		$params = 'loan_date LIKE \''.$_REQUEST['loan_date'].'%\' 
					AND COALESCE(mlp.balance,tl.principal_balance) > 0 
					AND r.unearned_interest = \'Y\'';
		$data = $this->amortizationunearnedinterestreport_model->getUnearnedAmortizationInterest(
													$params									
													,null
													,null
													,array('r.loan_description as loan_description'
															,'SUBSTR(loan_date, 1,6) AS loan_date'
															,'interest_rate'		
															,'tl.employee_id as employee_id'	
															,'m.last_name as last_name'	
															,'m.first_name as first_name'	
															,'ROUND(principal, 2) AS principal'
															,'ROUND(term,0) AS term'
															,'ROUND(employee_interest_total,2) AS interest_amount'
															,'ROUND(employee_interest_total/(CASE WHEN term<12 THEN term ELSE 12 END),2) AS unearned_interest_per_month'
															,'ROUND(employee_interest_total, 2) AS total'
														)
													,'r.loan_description, interest_rate, employee_id ASC'
													,$_REQUEST['loan_date']);
		return $data['list'];
	}
	
	
 	/**
	 * @desc Displays the report in Excel File
	 */
	function printExcel()
	{
		$row_start = 6;											 		//row after the header		
			
		$objExcel = new Asi_excel_ext();
		$objExcel->init(60,6);		
		$objExcel->writeHeaderInfo($this->report_title, "for the period of ".date("F Y",strtotime($this->report_date)), "L0007");
		
		$total2 = array("total" => "left"
				      ,"total_emp" => "center"
					  ,"total_principal" => "right"
					  ,"total_term" => "right"
					  ,"total_interest_amount" => "right"
					  ,"month1" => "right"
					  ,"month2" => "right"
					  ,"month3" => "right"
					  ,"month4" => "right"
					  ,"month5" => "right"
					  ,"month6" => "right"
					  ,"month7" => "right"
					  ,"month8" => "right"
					  ,"month9" => "right"
					  ,"month10" => "right"
					  ,"total_total" => "right"
					 );
		$total3 = array("total" => "s"
					  ,"total_emp" => "s"
					  ,"total_principal" => "#,##0.00"
					  ,"total_term" => "#,##0"
					  ,"total_interest_amount" => "#,##0.00"
					  ,"month1" => "#,##0.00"
					  ,"month2" => "#,##0.00"
					  ,"month3" => "#,##0.00"
					  ,"month4" => "#,##0.00"
					  ,"month5" => "#,##0.00"
					  ,"month6" => "#,##0.00"
					  ,"month7" => "#,##0.00"
					  ,"month8" => "#,##0.00"
					  ,"month9" => "#,##0.00"
					  ,"month10" => "#,##0.00"
					  ,"total_total" => "#,##0.00"
				);
		
		$this->amort_unearned_list = $this->read();
		
		$this->loan_description ="";
		$count = 0;	
		foreach ($this->amort_unearned_list AS $data) 
		{
			if($this->loan_description != $data['loan_description'])
			{
				if($this->loan_description != "")
				{
					$objExcel->writeTotals(array("total" => "Total"
											,"total_emp" => $this->countEmp ." Employee/s"
											,"total_principal" => $this->total_principal
											,"total_term" => ""//$this->total_term
											,"total_interest_amount" => $this->total_interest_amount
											,"month1" => $this->month1
											,"month2" => $this->month2
											,"month3" => $this->month3
											,"month4" => $this->month4
											,"month5" => $this->month5
											,"month6" => $this->month6
											,"month7" => $this->month7
											,"month8" => $this->month8
											,"month9" => $this->month9
											,"month10" => $this->month10
											,"total_total" =>$this->total_total
											)
									,$total2
									,$total3
									,'A' 	  					//first column name:used for border
									,'BK');	  				//end column name:used for border
					$row_start = ($row_start+$count);
					$this->countEmp = 0;
					
					$this->total_principal=0;
					$this->total_term=0;
					$this->total_interest_amount=0;
					
					$this->month1=0;
					$this->month2=0;
					$this->month3=0;
					$this->month4=0;
					$this->month5=0;
					$this->month6=0;
					$this->month7=0;
					$this->month8=0;
					$this->month9=0;
					$this->month10=0;
					
					$this->total_total=0;
				}
				$row_start = $row_start+1;						 
				$objExcel->writeSubheader(array("Employee ID" => 3		//no of cells in actual excel
												,"Employee Name" => 8	
												,"Principal" => 4
												,"Term" => 2
												,"Interest Amount" => 4
												,$this->getMonth($this->month_no, $data['loan_date']) => 4
												,$this->getMonth($this->month_no+1, $data['loan_date']) => 4
												,$this->getMonth($this->month_no+2, $data['loan_date']) => 4
												,$this->getMonth($this->month_no+3, $data['loan_date']) => 4
												,$this->getMonth($this->month_no+4, $data['loan_date']) => 4
												,$this->getMonth($this->month_no+5, $data['loan_date']) => 4
												,$this->getMonth($this->month_no+6, $data['loan_date']) => 4
												,$this->getMonth($this->month_no+7, $data['loan_date']) => 4
												,$this->getMonth($this->month_no+8, $data['loan_date']) => 4
												,$this->getMonth($this->month_no+9, $data['loan_date']) => 4
												,"Total" => 4
												)
										  ,0
										  ,$row_start
										  ,12.75
										  ,array("Employee ID" => "center"
										      ,"Employee Name" => "left"
											  ,"Principal" => "right"
											  ,"Term" => "right"
											  ,"Interest Amount" => "right"
											  ,$this->getMonth($this->month_no, $data['loan_date']) => "right"
											  ,$this->getMonth($this->month_no+1, $data['loan_date']) => "right"
											  ,$this->getMonth($this->month_no+2, $data['loan_date']) => "right"
											  ,$this->getMonth($this->month_no+3, $data['loan_date']) => "right"
											  ,$this->getMonth($this->month_no+4, $data['loan_date']) => "right"
											  ,$this->getMonth($this->month_no+5, $data['loan_date']) => "right"
											  ,$this->getMonth($this->month_no+6, $data['loan_date']) => "right"
											  ,$this->getMonth($this->month_no+7, $data['loan_date']) => "right"
											  ,$this->getMonth($this->month_no+8, $data['loan_date']) => "right"
											  ,$this->getMonth($this->month_no+9, $data['loan_date']) => "right"
											  ,"Total" => "right")
										  ,true
										  ,$row_start = $row_start +1 
										  );			  				
				$objExcel->writeSubheaderTitle($data['loan_description'], 0, $row_start=$row_start+1);
				$objExcel->writeSubheaderTitle('Prevailing Interest Rate        '.$data['interest_rate'], 0, $row_start=$row_start+1);
				
			}	
			$this->list = $this->getData($data);					
			$count = (count($this->list))+2;	
			//print_r($this->list);				
			$objExcel->writeTableData($this->list
  									  ,array("employee_id" => "center"
										      ,"employee_name" => "left"
											  ,"principal" => "right"
											  ,"term" => "right"
											  ,"interest_amount" => "right"
											  ,"month1" => "right"
											  ,"month2" => "right"
											  ,"month3" => "right"
											  ,"month4" => "right"
											  ,"month5" => "right"
											  ,"month6" => "right"
											  ,"month7" => "right"
											  ,"month8" => "right"
											  ,"month9" => "right"
											  ,"month10" => "right"
											  ,"total" => "right"
											 )
									  ,array("employee_id" => "s"
											  ,"employee_name" => "s"
											  ,"principal" => "#,##0.00"
											  ,"term" => "#,##0"
											  ,"interest_amount" => "#,##0.00"
											  ,"month1" => "#,##0.00"
											  ,"month2" => "#,##0.00"
											  ,"month3" => "#,##0.00"
											  ,"month4" => "#,##0.00"
											  ,"month5" => "#,##0.00"
											  ,"month6" => "#,##0.00"
											  ,"month7" => "#,##0.00"
											  ,"month8" => "#,##0.00"
											  ,"month9" => "#,##0.00"
											  ,"month10" => "#,##0.00"
											  ,"total" => "#,##0.00"
											  )	
									  ,$row_start = $row_start+1);	
		
			$this->loan_description = $data['loan_description'];
			//$row_start = $row_start+1;
		}
		
		$objExcel->writeTotals(array("total" => "Total"
											,"total_emp" => $this->countEmp ." Employee(s)"
											,"total_principal" => $this->total_principal
											,"total_term" => ""//$this->total_term
											,"total_interest_amount" => $this->total_interest_amount
											,"month1" => $this->month1
											,"month2" => $this->month2
											,"month3" => $this->month3
											,"month4" => $this->month4
											,"month5" => $this->month5
											,"month6" => $this->month6
											,"month7" => $this->month7
											,"month8" => $this->month8
											,"month9" => $this->month9
											,"month10" => $this->month10
											,"total_total" =>$this->total_total
											)
									,$total2
									,$total3
									,'A' 	  					//first column name:used for border
									,'BK');	  				//end column name:used for border
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
		$objPdf->init("landscape", .4);	
		$objPdf->writeHeaderInfo($this->report_title, "for the period of ".date("F Y",strtotime($this->report_date)), "L0007");
		$total2 = array("total" => "L"
				      ,"total_emp" => "C"
					  ,"total_principal" => "R"
					  ,"total_term" => "R"
					  ,"total_interest_amount" => "R"
					  ,"month1" => "R"
					  ,"month2" => "R"
					  ,"month3" => "R"
					  ,"month4" => "R"
					  ,"month5" => "R"
					  ,"month6" => "R"
					  ,"month7" => "R"
					  ,"month8" => "R"
					  ,"month9" => "R"
					  ,"month10" => "R"
					  ,"total_total" => "R"
					 );
		$total3 = array("total" => 4
					  ,"total_emp" => 9
					  ,"total_principal" =>4
					  ,"total_term" => 3
					  ,"total_interest_amount" => 5
					  ,"month1" => 3
					  ,"month2" => 3
					  ,"month3" => 3
					  ,"month4" => 3
					  ,"month5" => 3
					  ,"month6" => 3
					  ,"month7" => 3
					  ,"month8" => 3
					  ,"month9" => 3
					  ,"month10" =>3
					  ,"total_total" => 5
				);
		$this->amort_unearned_list = $this->read();
		$this->loan_description = "";
		
		$ctr = 0;
		foreach ($this->amort_unearned_list AS $data) 
		{
			$ctr++;
			if($this->loan_description != $data['loan_description'])
			{ 
				if($this->loan_description != "")
				{
						$objPdf->writeTotals(array("total" => "Total"
								,"total_emp" => number_format($this->countEmp,0,' ',',') ." Employee(s)"
								,"total_principal" => number_format($this->total_principal,2,'.',',')
								,"total_term" => ""//$this->total_term
								,"total_interest_amount" => number_format($this->total_interest_amount,2,'.',',')
								,"month1" => number_format($this->month1,2,'.',',')
								,"month2" => number_format($this->month2,2,'.',',')
								,"month3" => number_format($this->month3,2,'.',',')
								,"month4" => number_format($this->month4,2,'.',',')
								,"month5" => number_format($this->month5,2,'.',',')
								,"month6" => number_format($this->month6,2,'.',',')
								,"month7" => number_format($this->month7,2,'.',',')
								,"month8" => number_format($this->month8,2,'.',',')
								,"month9" => number_format($this->month9,2,'.',',')
								,"month10" => number_format($this->month10,2,'.',',')
								,"total_total" =>number_format($this->total_total,2,'.',',')
								)
										,$total2
										,$total3
										,.4);
						$objPdf->Ln(.16);
						$this->countEmp = 0;
					
						$this->total_principal=0;
						$this->total_term=0;
						$this->total_interest_amount=0;
						
						$this->month1=0;
						$this->month2=0;
						$this->month3=0;
						$this->month4=0;
						$this->month5=0;
						$this->month6=0;
						$this->month7=0;
						$this->month8=0;
						$this->month9=0;
						$this->month10=0;
						
						$this->total_total=0;
						// if ($count!=20 && $ctr > 6) { 
							// $ctr = 0;
							// $objPdf->Ln(.06);
							// $objPdf->writeFooter();
							//$objPdf->pdf_obj->AddPage();
							// $objPdf->Header();
							// $objPdf->pdf_obj->SetFont('Arial','',7);
					// }
				}
				$objPdf->Ln();
				$objPdf->writeSubheader(array("Employee ID" => 4		
												,"Employee Name" => 9	
												,"Principal" => 4
												,"Term" => 3
												,"Interest Amount" => 5
												,$this->getMonth($this->month_no, $data['loan_date']) => 3
												,$this->getMonth($this->month_no+1, $data['loan_date']) => 3
												,$this->getMonth($this->month_no+2, $data['loan_date']) => 3
												,$this->getMonth($this->month_no+3, $data['loan_date']) => 3
												,$this->getMonth($this->month_no+4, $data['loan_date']) => 3
												,$this->getMonth($this->month_no+5, $data['loan_date']) => 3
												,$this->getMonth($this->month_no+6, $data['loan_date']) => 3
												,$this->getMonth($this->month_no+7, $data['loan_date']) => 3
												,$this->getMonth($this->month_no+8, $data['loan_date']) => 3
												,$this->getMonth($this->month_no+9, $data['loan_date']) => 3
												,"Total" => 5
												)
											,array("Employee ID" => "C"
										      ,"Employee Name" => "L"
											  ,"Principal" => "R"
											  ,"Term" => "R"
											  ,"Interest Amount" => "R"
											  ,$this->getMonth($this->month_no, $data['loan_date']) => "R"
											  ,$this->getMonth($this->month_no+1, $data['loan_date']) => "R"
											  ,$this->getMonth($this->month_no+2, $data['loan_date']) => "R"
											  ,$this->getMonth($this->month_no+3, $data['loan_date']) => "R"
											  ,$this->getMonth($this->month_no+4, $data['loan_date']) => "R"
											  ,$this->getMonth($this->month_no+5, $data['loan_date']) => "R"
											  ,$this->getMonth($this->month_no+6, $data['loan_date']) => "R"
											  ,$this->getMonth($this->month_no+7, $data['loan_date']) => "R"
											  ,$this->getMonth($this->month_no+8, $data['loan_date']) => "R"
											  ,$this->getMonth($this->month_no+9, $data['loan_date']) => "R"
											  ,"Total" => "R")
											,.4
										);
				
				$objPdf->writeSubheaderTitle($data['loan_description']
												,.4);	
				$objPdf->writeSubheaderTitle('Prevailing Interest Rate        '.$data['interest_rate']
												,.4);	
			}
			$this->list = $this->getData($data);
			$count = $objPdf->writeTableData($this->list
										,array("employee_id" => 4
											  ,"employee_name" =>9
											 ,"principal" => 4
											  ,"term" => 3
											  ,"interest_amount" => 5
											  ,"month1" => 3
											  ,"month2" => 3
											  ,"month3" => 3
											  ,"month4" => 3
											  ,"month5" => 3
											  ,"month6" =>3
											  ,"month7" => 3
											  ,"month8" => 3
											  ,"month9" => 3
											  ,"month10" => 3
											  ,"total" => 5
											  )
	  								 	,array("employee_id" => "C"
										      ,"employee_name" => "L"
											  ,"principal" => "R"
											  ,"term" => "R"
											  ,"interest_amount" => "R"
											  ,"month1" => "R"
											  ,"month2" => "R"
											  ,"month3" => "R"
											  ,"month4" => "R"
											  ,"month5" => "R"
											  ,"month6" => "R"
											  ,"month7" => "R"
											  ,"month8" => "R"
											  ,"month9" => "R"
											  ,"month10" => "R"
											  ,"total" => "R")
										,.4
										,.18);	
			
			$this->loan_description = $data['loan_description'];
		}	
		
		$objPdf->writeTotals(array("total" => "Total"
								,"total_emp" => $this->countEmp ." Employee(s)"
								,"total_principal" => number_format($this->total_principal,2,'.',',')
								,"total_term" => ""//$this->total_term
								,"total_interest_amount" => number_format($this->total_interest_amount,2,'.',',')
								,"month1" => number_format($this->month1,2,'.',',')
								,"month2" => number_format($this->month2,2,'.',',')
								,"month3" => number_format($this->month3,2,'.',',')
								,"month4" => number_format($this->month4,2,'.',',')
								,"month5" => number_format($this->month5,2,'.',',')
								,"month6" => number_format($this->month6,2,'.',',')
								,"month7" => number_format($this->month7,2,'.',',')
								,"month8" => number_format($this->month8,2,'.',',')
								,"month9" => number_format($this->month9,2,'.',',')
								,"month10" => number_format($this->month10,2,'.',',')
								,"total_total" =>number_format($this->total_total,2,'.',',')
								)
							,$total2
							,$total3
							,.4);
		$objPdf->writeFooter();	
		
		$objPdf->Output($this->report_title."_".date("YmdHis"));	
	}
	
 	/**
	 * @desc Sets the data
	 */
	function setTableData() 
	{	
			$this->month_no = date("m",strtotime($this->report_date))+1;
			$this->report_title = "Amortization of Unearned Interest";		
			$this->amort_unearned_list = $this->read();
		
			if (count($this->amort_unearned_list)==0)
				return true;	
		
	}
	
 	/**
	 * @desc Gets the data
	 */
	function getData($data) 
	{
		$last_name = $data['last_name'];
		$first_name = $data['first_name'];

		$list[0] = array("employee_id"=>" ".$data["employee_id"]
						 ,"employee_name"=>$last_name.", ".$first_name
						 ,"principal"=>number_format($data["principal"],2,'.',',')
						 ,"term"=>" ".$data["term"]
						 ,"interest_amount"=>number_format($data["interest_amount"],2,'.',',')
						 ,"month1"=>number_format($data['unearned_interest_per_month'],2,'.',',')
						 );	
						 
		$value = $data['unearned_interest_per_month'];
		$tot_list[0]['month1'] = $value;
		for($ctr=2; $ctr<=$data["term"] && $data["term"]<= 10; $ctr++)
		{
			if($ctr==$data["term"]){ //meaning last month of paying
				$total_payments_minus_one_term = ($data["term"] - 1) * $value;
				$value = $data["interest_amount"] - $total_payments_minus_one_term;
			}
			
			$list[0]['month'.$ctr] = number_format($value,2,'.',',');
			$tot_list[0]['month'.$ctr] = $value;
		}
		$val = "0.00";
		for(; $ctr<= 10; $ctr++)
		{
			$list[0]['month'.$ctr]=$val;
			$tot_list[0]['month'.$ctr] = $val;
		}
		$list[0]['total'] = number_format($data["total"],2,'.',',');
		
		$this->countEmp += 1;
		$this->month1 += $tot_list[0]['month1'];
		$this->month2 += $tot_list[0]['month2'];
		$this->month3 += $tot_list[0]['month3'];
		$this->month4 += $tot_list[0]['month4'];
		$this->month5 += $tot_list[0]['month5'];
		$this->month6 += $tot_list[0]['month6'];
		$this->month7 += $tot_list[0]['month7'];
		$this->month8 += $tot_list[0]['month8'];
		$this->month9 += $tot_list[0]['month9'];
		$this->month10 += $tot_list[0]['month10'];
		
		$this->total_principal+=$data["principal"];
		$this->total_term+=$data["term"];
		$this->total_interest_amount+=$data["interest_amount"];
		$this->total_total += $data['total'];
	
		return $list;
	}
	
	function getMonth($term=7, $loan_date='201001')
	{
		$month = substr($loan_date, 4, 5);
		$mo = ($term)%12;
		$ret = "";
		switch($mo)
		{
		case 1: {$ret = 'Jan'; break;}
		case 2: {$ret = 'Feb'; break;}
		case 3: {$ret = 'Mar'; break;}
		case 4: {$ret = 'Apr'; break;}
		case 5: {$ret = 'May'; break;}
		case 6: {$ret = 'Jun'; break;}
		case 7: {$ret = 'Jul'; break;}
		case 8: {$ret = 'Aug'; break;}
		case 9: {$ret = 'Sep'; break;}
		case 10: {$ret = 'Oct'; break;}
		case 11: {$ret = 'Nov'; break;}
		case 0: {$ret = 'Dec'; break;}
		}
	
		return $ret;
	}
}
?>
