<?php

class Loanpaymentduereport_model extends Asi_Model {
	var $table_name = 'm_loan';
	var $id = 'loan_no';
	var $model_data = null;
	var $date_columns = array('loan_date', 'amortization_startdate');
	
	
    function Loanpaymentduereport_model()
    {
        parent::Asi_Model();
    }
  
    /**
     * @desc Retrieve list of loan payment overdue regardless of capital balance of employees
     * @param $acctgPeriod
     * @param $loan_date
     */
	function getLoanOverDueList($acctgPeriod='20100127', $loan_date = null, $company_code, $loan_code ='CARL'){
		
		$sql = "SELECT DISTINCT(m_employee.company_code) as company_code
					,m_loan.loan_no	
					,m_loan.loan_date	
					,m_loan.employee_id	
					,m_employee.last_name	
					,m_employee.first_name	
					,m_loan.employee_principal_amort AS principal	
					,m_loan.employee_interest_amortization AS interest
					,rtc.charge_formula AS penalty	
				FROM m_loan		
				LEFT OUTER JOIN  r_transaction_charge rtc 		
					ON (rtc.transaction_code = '".$loan_code."' AND rtc.charge_code = 'PENT')	
				LEFT OUTER JOIN (
					SELECT loan_no 
					FROM m_loan_payment 
					WHERE payment_date LIKE '".$loan_date."%'
					AND status_flag IN (2,3)
					) AS lp 		
					ON (m_loan.loan_no = lp.loan_no) 	
				LEFT OUTER JOIN m_employee 		
					ON (m_loan.employee_id = m_employee.employee_id)	
				WHERE m_loan.loan_date <= '".$loan_date."01'		
					AND lp.loan_no IS NULL 	
					AND m_loan.principal_balance > 0 	
					AND m_loan.loan_code ='".$loan_code."'  	
					AND (m_loan.status_flag IN(2,3)) 
					ANd m_employee.company_code = '".$company_code."'
				ORDER BY m_employee.company_code,m_loan.loan_date ASC";
			
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
			
	}
	//enhancement
	function getLoanOverDueList2($acctgPeriod='20100127', $loan_date = null, $company_code, $loan_code ='CARL'){
		
		$sql = "SELECT DISTINCT(m_employee.company_code) as company_code
					,m_loan.loan_no	
					,m_loan.loan_date
					,m_loan.term	
					,m_loan.employee_id	
					,m_employee.last_name	
					,m_employee.first_name	
					,m_loan.principal_balance AS principal	
					,m_loan.employee_interest_amortization AS interest
					,rtc.charge_formula AS penalty	
				FROM m_loan		
				LEFT OUTER JOIN  r_transaction_charge rtc 		
					ON (rtc.transaction_code = '".$loan_code."' AND rtc.charge_code = 'PENT')	
				LEFT OUTER JOIN (
					SELECT loan_no 
						,transaction_code
						,payment_date 
						,payor_id
					FROM m_loan_payment 
					WHERE payment_date LIKE '".$loan_date."%'
					AND status_flag IN (2,3)
					) AS lp 		
					ON (m_loan.loan_no = lp.loan_no) 
				LEFT OUTER JOIN t_transaction tt 
					ON tt.reference = CONCAT(lp.loan_no, ',', lp.transaction_code, ',', lp.payment_date, ',', lp.payor_id) 
					AND tt.status_flag = '2'
				LEFT OUTER JOIN m_employee 		
					ON (m_loan.employee_id = m_employee.employee_id)	
				WHERE m_loan.loan_date < '".$loan_date."01'		
					AND (lp.loan_no IS NULL OR tt.reference IS NOT NULL) 	
					AND m_loan.principal_balance > 0 	
					AND m_loan.loan_code ='".$loan_code."'  	
					AND (m_loan.status_flag IN(2,3)) 
					ANd m_employee.company_code = '".$company_code."'
					AND m_loan.term = 2 #retrieve only two term miniloans
					AND ".$loan_date." - SUBSTR(m_loan.amortization_startdate, 1, 6) = 0 #used to determine if first term
				
				UNION
				
				SELECT DISTINCT(m_employee.company_code) as company_code
					,m_loan.loan_no	
					,m_loan.loan_date	
					,m_loan.term
					,m_loan.employee_id	
					,m_employee.last_name	
					,m_employee.first_name	
					,m_loan.principal_balance AS principal	
					,m_loan.employee_interest_amortization AS interest
					,rtc.charge_formula AS penalty	
				FROM m_loan		
				LEFT OUTER JOIN  r_transaction_charge rtc 		
					ON (rtc.transaction_code = '".$loan_code."' AND rtc.charge_code = 'PENT')	
				LEFT OUTER JOIN (SELECT m_loan_payment.loan_no
					, balance
					,transaction_code
					,payment_date 
					,payor_id
					FROM (m_loan_payment) 
					INNER JOIN (SELECT m_loan_payment.loan_no
							, MAX(modified_date) AS LastPay 
						FROM m_loan_payment 
						WHERE payment_date LIKE '".$loan_date."%' 
						AND status_flag IN (2,3)
						GROUP BY loan_no) LP1 
					ON m_loan_payment.`loan_no`=`LP1`.`loan_no` AND m_loan_payment.modified_date=LP1.LastPay
					) lp 	
				ON (m_loan.loan_no = lp.loan_no)
				LEFT OUTER JOIN t_transaction tt 
					ON tt.reference = CONCAT(lp.loan_no, ',', lp.transaction_code, ',', lp.payment_date, ',', lp.payor_id) 
					AND tt.status_flag = '2'
				LEFT OUTER JOIN m_employee 		
					ON (m_loan.employee_id = m_employee.employee_id)	
				WHERE m_loan.loan_date < '".$loan_date."01'		
					AND (lp.loan_no IS NULL OR lp.balance > 0 OR tt.reference IS NOT NULL)	
					AND m_loan.principal_balance > 0 	
					AND m_loan.loan_code ='".$loan_code."'  	
					AND (m_loan.status_flag IN(2,3)) 
					ANd m_employee.company_code = '".$company_code."'
					AND (
							m_loan.term = 1
							OR
							m_loan.term = 2 AND ".$loan_date." - SUBSTR(m_loan.amortization_startdate,1,6) > 0
							)
				ORDER BY company_code,loan_date ASC
				";
			
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
			
	}
	
    /**
     * @desc Retrieve list of loan payment overdue regardless of capital balance of employees
     * @param $acctgPeriod
     * @param $loan_date
     */
	function getLoanInsufficient($acctgPeriod='20100127', $loan_date = null, $company_code, $loan_code ='CARL', $ccminimum){
		
		
		
		$sql = "SELECT * FROM (SELECT DISTINCT(m_employee.company_code) AS company_code		
					,m_loan.loan_no	
					,m_loan.loan_date	
					,m_loan.employee_id	
					,m_employee.last_name	
					,m_employee.first_name	
					,m_loan.employee_principal_amort AS principal	
					,m_loan.employee_interest_amortization AS interest	
					,rtc.charge_formula AS penalty
					,ending_balance - rtc.charge_formula AS remaining
				FROM m_loan		
				LEFT OUTER JOIN  r_transaction_charge rtc 		
					ON (rtc.transaction_code = '$loan_code' AND rtc.charge_code = 'PENT')
				LEFT OUTER JOIN (SELECT loan_no FROM m_loan_payment WHERE payment_date LIKE '$acctgPeriod%') AS lp 		
					ON (m_loan.loan_no = lp.loan_no) 	
				INNER JOIN m_employee 		
					ON (m_loan.employee_id = m_employee.employee_id)
				LEFT OUTER JOIN t_capital_contribution tcc
					ON (tcc.employee_id = m_employee.employee_id AND accounting_period LIKE '$acctgPeriod%')
				WHERE m_loan.loan_date <= '$loan_date'
					AND lp.loan_no IS NULL 	
					AND m_loan.principal_balance > 0 	
					AND m_loan.loan_code ='$loan_code'
					AND (m_loan.status_flag > 1 AND m_loan.status_flag < 4) 
					AND m_employee.company_code = '$company_code') temp_table
				WHERE remaining < $ccminimum			
				ORDER BY company_code,loan_date ASC";
			
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
				//echo $query."<br />";

		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
			
	}


    /**
     * @desc Retrieve list of loan payment overdue regardless of capital balance of employees
     * @param $acctgPeriod
     * @param $loan_date
     */
	function getLoanSufficient($acctgPeriod='20100127', $loan_date = null, $company_code, $loan_code ='CARL', $ccminimum){
		
		
		
		$sql = "SELECT * FROM (SELECT DISTINCT(m_employee.company_code) AS company_code		
					,m_loan.loan_no	
					,m_loan.loan_date	
					,m_loan.employee_id	
					,m_employee.last_name	
					,m_employee.first_name	
					,m_loan.employee_principal_amort AS principal	
					,m_loan.employee_interest_amortization AS interest	
					,rtc.charge_formula AS penalty
					-- ,ending_balance - rtc.charge_formula AS remaining
				FROM m_loan		
				LEFT OUTER JOIN  r_transaction_charge rtc 		
					ON (rtc.transaction_code = '$loan_code' AND rtc.charge_code = 'PENT')
				LEFT OUTER JOIN (SELECT loan_no FROM m_loan_payment WHERE payment_date LIKE '$acctgPeriod%') AS lp 		
					ON (m_loan.loan_no = lp.loan_no) 	
				INNER JOIN m_employee 		
					ON (m_loan.employee_id = m_employee.employee_id)
				LEFT OUTER JOIN t_capital_contribution tcc
					ON (tcc.employee_id = m_employee.employee_id AND accounting_period LIKE '$acctgPeriod%')
				WHERE m_loan.loan_date <= '$loan_date'
					AND lp.loan_no IS NULL 	
					AND m_loan.principal_balance > 0 	
					AND m_loan.loan_code ='$loan_code'
					AND (m_loan.status_flag > 1 AND m_loan.status_flag < 4) 
					AND m_employee.company_code = '$company_code') temp_table
				-- WHERE remaining >= $ccminimum			
				ORDER BY company_code,loan_date ASC";
			
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
				//echo $query."<br />";

		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
			
	}
	
	/**
     * @desc Retrieve list of loan payment overdue regardless of capital balance of employees
     * @param $acctgPeriod
     * @param $loan_date
     */
	function getDesc($acctgPeriod='20100127', $loan_date = null, $loan_code ='CARL'){
		
		$sql = "SELECT DISTINCT(m_employee.company_code) as company_code
						FROM m_loan		
					LEFT OUTER JOIN  i_parameter_list ip 		
						ON (ip.parameter_id = 'LOANDUEFEE')	
					LEFT OUTER JOIN (
						SELECT loan_no 
						FROM m_loan_payment 
						WHERE payment_date LIKE '".$loan_date."%'
						AND status_flag IN (2,3)
						) AS lp 		
						ON (m_loan.loan_no = lp.loan_no) 	
					LEFT OUTER JOIN m_employee 		
						ON (m_loan.employee_id = m_employee.employee_id)	
					WHERE m_loan.loan_date <= '".$loan_date."%'		
						AND lp.loan_no IS NULL 	
						AND m_loan.principal_balance > 0 	
						AND m_loan.loan_code ='".$loan_code."'  	
						AND (m_loan.status_flag IN(2,3))
				ORDER BY m_employee.company_code, m_loan.loan_date ASC";
			
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		log_message('debug', 'xx-xx '.$query);
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
	}
	
	//enhancement
	function getDesc2($acctgPeriod='20100127', $loan_date = null, $loan_code ='CARL'){
		
		$sql = "SELECT DISTINCT(m_employee.company_code) as company_code
						FROM m_loan		
					LEFT OUTER JOIN  i_parameter_list ip 		
						ON (ip.parameter_id = 'LOANDUEFEE')	
					LEFT OUTER JOIN (
						SELECT loan_no
							,transaction_code
							,payment_date 
							,payor_id
						FROM m_loan_payment 
						WHERE payment_date LIKE '".$loan_date."%'
						AND status_flag IN (2,3)
						) AS lp 		
						ON (m_loan.loan_no = lp.loan_no)
					LEFT OUTER JOIN t_transaction tt 
							ON tt.reference = CONCAT(lp.loan_no, ',', lp.transaction_code, ',', lp.payment_date, ',', lp.payor_id) 
							AND tt.status_flag = '2'
					LEFT OUTER JOIN m_employee 		
						ON (m_loan.employee_id = m_employee.employee_id)	
					WHERE m_loan.loan_date < '".$loan_date."01'		
						AND (lp.loan_no IS NULL OR tt.reference IS NOT NULL)	
						AND m_employee.company_code IS NOT NULL
						AND m_loan.principal_balance > 0 	
						AND m_loan.loan_code ='".$loan_code."'  	
						AND (m_loan.status_flag IN(2,3))
						AND m_loan.term = 2 #retrieve only two term miniloans
						AND ".$loan_date." - SUBSTR(m_loan.amortization_startdate,1,6) = 0 #used to determine if first term
				
				UNION #All miniloans on their last month
				
					SELECT DISTINCT(m_employee.company_code) as company_code
						FROM m_loan		
					LEFT OUTER JOIN  i_parameter_list ip 		
						ON (ip.parameter_id = 'LOANDUEFEE')	
					LEFT OUTER JOIN (SELECT m_loan_payment.loan_no
						, balance
						,transaction_code
						,payment_date 
						,payor_id						
						FROM (m_loan_payment) 
						INNER JOIN (SELECT m_loan_payment.loan_no
								, MAX(modified_date) AS LastPay 
							FROM m_loan_payment 
							WHERE payment_date LIKE '".$loan_date."%' 
							AND status_flag IN (2,3)
							GROUP BY loan_no) LP1 
						ON m_loan_payment.`loan_no`=`LP1`.`loan_no` AND m_loan_payment.modified_date=LP1.LastPay
						) lp 	
						ON (m_loan.loan_no = lp.loan_no)
					LEFT OUTER JOIN t_transaction tt 
							ON tt.reference = CONCAT(lp.loan_no, ',', lp.transaction_code, ',', lp.payment_date, ',', lp.payor_id) 			
							AND tt.status_flag = '2'
					LEFT OUTER JOIN m_employee 		
						ON (m_loan.employee_id = m_employee.employee_id)	
					WHERE m_loan.loan_date < '".$loan_date."01'		
						AND (lp.loan_no IS NULL OR lp.balance > 0 OR tt.reference IS NOT NULL) 	
						AND m_employee.company_code IS NOT NULL
						AND m_loan.principal_balance > 0 	
						AND m_loan.loan_code ='".$loan_code."'  	
						AND (m_loan.status_flag IN(2,3))
						AND (
							m_loan.term = 1
							OR
							m_loan.term = 2 AND ".$loan_date." - SUBSTR(m_loan.amortization_startdate,1,6) > 0
							)
				ORDER BY company_code";
			
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		log_message('debug', 'xx-xx '.$query);
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
	}
	
	function getDescMnilDue(){
		$sql = "SELECT DISTINCT(me.company_code) as company_code
				FROM  m_loan ml
				LEFT OUTER JOIN m_employee me		
					ON (ml.employee_id = me.employee_id)
				WHERE ml.loan_code ='MNIL'
				AND ml.close_flag = '0'  	
				AND ml.status_flag IN(2,3)
				ORDER BY company_code";
		
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
	}
	
	function getMnilDueList($company_code){
		$sql = "SELECT ml.loan_no	
					,ml.loan_date
					,ml.term	
					,ml.employee_id	
					,me.last_name	
					,me.first_name	
					,ml.principal_balance AS principal	
				FROM  m_loan ml
				LEFT OUTER JOIN m_employee me		
					ON (ml.employee_id = me.employee_id)
				WHERE ml.loan_code ='MNIL'
				AND ml.close_flag = '0'  	
				AND ml.status_flag IN(2,3)
				AND me.company_code =  '{$company_code}'
				ORDER BY loan_date";
		
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
	}
	
	//enhancement
	function getDescMidMonth($lastWorkingDayLastMonth, $currLastYearMonth, $lastMonthMidFrom, $lastMonthMidTo, $loan_code ='MNIL'){
		
		$sql = "
				#Retrieve all employees who do not have partial payment and is on last term
				SELECT DISTINCT(m_employee.company_code) as company_code
						FROM m_loan		
					LEFT OUTER JOIN  i_parameter_list ip 		
						ON (ip.parameter_id = 'LOANDUEFEE')	
					LEFT OUTER JOIN (
						SELECT loan_no 
						FROM m_loan_payment 
						WHERE payment_date LIKE '".$loan_date."%'
						AND status_flag IN (2,3)
						) AS lp 		
						ON (m_loan.loan_no = lp.loan_no) 	
					LEFT OUTER JOIN m_employee 		
						ON (m_loan.employee_id = m_employee.employee_id)	
					WHERE m_loan.loan_date <= '".$loan_date."%'		
						AND lp.loan_no IS NULL 	
						AND m_loan.principal_balance > 0 	
						AND m_loan.loan_code ='".$loan_code."'  	
						AND (m_loan.status_flag IN(2,3))
						AND (
							m_loan.term = 1
							OR
							m_loan.term = 2 AND ".$loan_date." - SUBSTR(m_loan.amortization_startdate,1,6) > 0
							)
				UNION
				
				#Retrieve all employees who do not have posting of payments 
				SELECT DISTINCT(m_employee.company_code) as company_code
						FROM m_loan		
					LEFT OUTER JOIN  i_parameter_list ip 		
						ON (ip.parameter_id = 'LOANDUEFEE')	
					LEFT OUTER JOIN (
						SELECT loan_no 
						FROM m_loan_payment 
						WHERE payment_date LIKE '".$loan_date."%'
						AND status_flag IN (2,3)
						) AS lp 		
						ON (m_loan.loan_no = lp.loan_no) 
					LEFT OUTER JOIN t_transaction tt 
						ON tt.reference = CONCAT(ml.loan_no, ',', ml.transaction_code, ',', ml.payment_date, ',', ml.payor_id)  		
					LEFT OUTER JOIN m_employee 		
						ON (m_loan.employee_id = m_employee.employee_id)	
					WHERE m_loan.loan_date <= '".$loan_date."%'		
						AND lp.loan_no IS NULL 	
						AND m_loan.principal_balance > 0 	
						AND m_loan.loan_code ='".$loan_code."'  	
						AND (m_loan.status_flag IN(2,3))
				";
			
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		log_message('debug', 'xx-xx '.$query);
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
	}
	
}
?>