<?php

class Loansuspendedborrowersreport_model extends Asi_Model {
	var $table_name = 'm_loan';
	var $id = 'loan_no';
	var $model_data = null;
	var $date_columns = array('loan_date', 'amortization_startdate');
	
	
    function Loansuspendedborrowersreport_model()
    {
        parent::Asi_Model();
    }
  
    /**
     * @desc Retrieve list of loans without payment regardless of capital balances of employees
     * @param $acctgPeriod
     * @param $loan_date
	 * @param $loan_code
     */
	function getNPMLList($acctgPeriod='20100127', $curr_date = null,$company_code, $loan_code ='MNIL'){
		
		$sql = "SELECT DISTINCT(m_employee.company_code) AS company_code		
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
				LEFT OUTER JOIN (SELECT loan_no FROM m_loan_payment WHERE payment_date LIKE '".$acctgPeriod."%' AND status_flag IN (2,3)) AS lp 		
					ON (m_loan.loan_no = lp.loan_no) 	
				INNER JOIN m_employee 		
					ON (m_loan.employee_id = m_employee.employee_id)	
				WHERE m_loan.loan_date <= '".$curr_date."'
					AND lp.loan_no IS NULL 	
					AND m_loan.principal_balance > 0 	
					AND m_loan.loan_code ='".$loan_code."'
					AND (m_loan.status_flag > 1 AND m_loan.status_flag < 4) 
					AND m_employee.company_code = '".$company_code."'					
				ORDER BY m_employee.company_code,m_loan.loan_date ASC";
			
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
			
	}
	
	function getNPMLList2($acctgPeriod='20100127', $curr_date = null,$company_code, $loan_code ='MNIL'){
		
		$sql = "SELECT DISTINCT(m_employee.company_code) AS company_code		
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
				LEFT OUTER JOIN (SELECT loan_no FROM m_loan_payment WHERE payment_date LIKE '".$acctgPeriod."%' AND status_flag IN (2,3)) AS lp 		
					ON (m_loan.loan_no = lp.loan_no) 	
				INNER JOIN m_employee 		
					ON (m_loan.employee_id = m_employee.employee_id)	
				WHERE m_loan.loan_date < '".$curr_date."'
					AND lp.loan_no IS NULL 	
					AND m_loan.principal_balance > 0 	
					AND m_loan.loan_code ='".$loan_code."'
					AND (m_loan.status_flag > 1 AND m_loan.status_flag < 4) 
					AND m_employee.company_code = '".$company_code."'					
					AND m_loan.term = 2 #retrieve only two term miniloans
					AND ".$acctgPeriod." - SUBSTR(m_loan.amortization_startdate, 1, 6) = 0 #used to determine if first term
					
				UNION
				
				SELECT DISTINCT(m_employee.company_code) AS company_code		
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
				LEFT OUTER JOIN (SELECT m_loan_payment.loan_no
					, balance 
					FROM (m_loan_payment) 
					INNER JOIN (SELECT m_loan_payment.loan_no
							, MAX(modified_date) AS LastPay 
						FROM m_loan_payment 
						WHERE payment_date LIKE '".$acctgPeriod."%' 
						AND status_flag IN (2,3)
						GROUP BY loan_no) LP1 
					ON m_loan_payment.`loan_no`=`LP1`.`loan_no` AND m_loan_payment.modified_date=LP1.LastPay
					) lp 	
				ON (m_loan.loan_no = lp.loan_no)  	
				INNER JOIN m_employee 		
					ON (m_loan.employee_id = m_employee.employee_id)	
				WHERE m_loan.loan_date < '".$curr_date."'
					AND lp.loan_no IS NULL 	
					AND m_loan.principal_balance > 0 	
					AND m_loan.loan_code ='".$loan_code."'
					AND (m_loan.status_flag > 1 AND m_loan.status_flag < 4) 
					AND m_employee.company_code = '".$company_code."'
					AND (
							m_loan.term = 1
							OR
							m_loan.term = 2 AND ".$acctgPeriod." - SUBSTR(m_loan.amortization_startdate,1,6) > 0
							)
				ORDER BY company_code,loan_date ASC";
			
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
			
	}
	
	function getLoanSuspendedList($loan_date = null, $company_code, $loan_code ='CARL'){
		
		$sql = "SELECT DISTINCT(m_employee.company_code) as company_code
					,m_loan.loan_no	
					,m_loan.loan_date
					,m_loan.term	
					,m_loan.employee_id	
					,m_employee.last_name	
					,m_employee.first_name	
					,m_loan.principal_balance AS principal
					,rtc.charge_formula AS penalty	
				FROM m_loan		
				LEFT OUTER JOIN  r_transaction_charge rtc 		
					ON (rtc.transaction_code = 'NPML' AND rtc.charge_code = 'NPML')	
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
					,rtc.charge_formula AS penalty	
				FROM m_loan		
				LEFT OUTER JOIN  r_transaction_charge rtc 		
					ON (rtc.transaction_code = 'NPML' AND rtc.charge_code = 'NPML')	
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
							
				UNION #All miniloans fully paid but are late
					
					SELECT me.company_code
						,ml.loan_no	
						,ml.loan_date	
						,ml.term
						,ml.employee_id	
						,me.last_name	
						,me.first_name	
						,ml.principal_balance AS principal	
						,rtc.charge_formula AS penalty	
					FROM m_loan ml
					LEFT OUTER JOIN  r_transaction_charge rtc 		
						ON (rtc.transaction_code = 'NPML' AND rtc.charge_code = 'NPML')	
					LEFT OUTER JOIN (SELECT m_loan_payment.loan_no
							,payment_date 
							FROM (m_loan_payment) 
							INNER JOIN (SELECT m_loan_payment.loan_no
									, MAX(modified_date) AS LastPay 
								FROM m_loan_payment 
								WHERE status_flag IN (2,3)
								GROUP BY loan_no) LP1 
							ON m_loan_payment.`loan_no`=`LP1`.`loan_no` AND m_loan_payment.modified_date=LP1.LastPay
							) lp 	
						ON (ml.loan_no = lp.loan_no)
					INNER JOIN m_employee me
							ON (me.employee_id = ml.employee_id)
					WHERE DATE_FORMAT(DATE_ADD(ml.loan_date, INTERVAL (ml.term) MONTH), '%Y%m') < DATE_FORMAT(lp.payment_date, '%Y%m')
					AND SUBSTR(lp.payment_date,1,6) = '".$loan_date."'
					AND ml.close_flag = '1'
					AND ml.loan_code  = 'MNIL'
					AND ml.status_flag = '2'
					AND ml.loan_date >= '20100701'
					ANd me.company_code = '".$company_code."'
					
				ORDER BY company_code,loan_date ASC
				";
			
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
			
	}
	
	/**
     * @desc Retrieve list of loans without payment regardless of capital balances of employees
     * @param $acctgPeriod
     * @param $loan_date
	 * @param $loan_code
     */
	function getDesc($acctgPeriod='20100127', $curr_date = null, $loan_code ='MNIL'){
		
		$sql = "SELECT DISTINCT(m_employee.company_code) as company_code
				FROM m_loan		
				LEFT OUTER JOIN  i_parameter_list AS ip 		
					ON (ip.parameter_id = 'LOANDUEFEE')	
				LEFT OUTER JOIN (SELECT loan_no FROM m_loan_payment WHERE payment_date LIKE '".$acctgPeriod."%' AND status_flag IN (2,3)) AS lp 		
					ON (m_loan.loan_no = lp.loan_no) 	
				INNER JOIN m_employee 		
					ON (m_loan.employee_id = m_employee.employee_id)	
				WHERE m_loan.loan_date <= '".$curr_date."'
					AND lp.loan_no IS NULL 	
					AND m_loan.principal_balance > 0 	
					AND m_loan.loan_code ='".$loan_code."'
					AND (m_loan.status_flag = '2') 	
				ORDER BY m_employee.company_code,m_loan.loan_date ASC";
			
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
	}
	
	function getDesc2($acctgPeriod='20100127', $curr_date = null, $loan_code ='MNIL'){
		
		$sql = "SELECT DISTINCT(m_employee.company_code) as company_code
				FROM m_loan		
				LEFT OUTER JOIN  i_parameter_list AS ip 		
					ON (ip.parameter_id = 'LOANDUEFEE')	
				LEFT OUTER JOIN (SELECT loan_no FROM m_loan_payment WHERE payment_date LIKE '".$acctgPeriod."%' AND status_flag IN (2,3)) AS lp 		
					ON (m_loan.loan_no = lp.loan_no) 	
				INNER JOIN m_employee 		
					ON (m_loan.employee_id = m_employee.employee_id)	
				WHERE m_loan.loan_date < '".$curr_date."'
					AND lp.loan_no IS NULL 	
					AND m_loan.principal_balance > 0 	
					AND m_loan.loan_code ='".$loan_code."'
					AND (m_loan.status_flag = '2') 	
					AND m_loan.term = 2 #retrieve only two term miniloans
					AND ".$acctgPeriod." - SUBSTR(m_loan.amortization_startdate,1,6) = 0 #used to determine if first term
						
				UNION
				
				SELECT DISTINCT(m_employee.company_code) as company_code
				FROM m_loan		
				LEFT OUTER JOIN  i_parameter_list AS ip 		
					ON (ip.parameter_id = 'LOANDUEFEE')	
				LEFT OUTER JOIN (SELECT m_loan_payment.loan_no
						, balance 
						FROM (m_loan_payment) 
						INNER JOIN (SELECT m_loan_payment.loan_no
								, MAX(modified_date) AS LastPay 
							FROM m_loan_payment 
							WHERE payment_date LIKE '".$acctgPeriod."%' 
							AND status_flag IN (2,3)
							GROUP BY loan_no) LP1 
						ON m_loan_payment.`loan_no`=`LP1`.`loan_no` AND m_loan_payment.modified_date=LP1.LastPay
						) lp 	
				ON (m_loan.loan_no = lp.loan_no) 	
				INNER JOIN m_employee 		
					ON (m_loan.employee_id = m_employee.employee_id)	
				WHERE m_loan.loan_date < '".$curr_date."'
					AND lp.loan_no IS NULL 	
					AND m_loan.principal_balance > 0 	
					AND m_loan.loan_code ='".$loan_code."'
					AND (m_loan.status_flag = '2') 	
					AND (
							m_loan.term = 1
							OR
							m_loan.term = 2 AND ".$acctgPeriod." - SUBSTR(m_loan.amortization_startdate,1,6) > 0
							)
				ORDER BY company_code";
			
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
	}
	
	function getDescLoanSuspended($loan_date = null, $loan_code ='CARL'){
		
		$sql = "SELECT DISTINCT(m_employee.company_code) as company_code
						FROM m_loan		
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
							
				UNION #All miniloans fully paid but are late
					
					SELECT DISTINCT(company_code) FROM m_loan ml
					LEFT OUTER JOIN (SELECT m_loan_payment.loan_no
							,payment_date 
							FROM (m_loan_payment) 
							INNER JOIN (SELECT m_loan_payment.loan_no
									, MAX(modified_date) AS LastPay 
								FROM m_loan_payment 
								WHERE status_flag IN (2,3)
								GROUP BY loan_no) LP1 
							ON m_loan_payment.`loan_no`=`LP1`.`loan_no` AND m_loan_payment.modified_date=LP1.LastPay
							) lp 	
						ON (ml.loan_no = lp.loan_no)
					INNER JOIN m_employee me
							ON (me.employee_id = ml.employee_id)
					WHERE DATE_FORMAT(DATE_ADD(ml.loan_date, INTERVAL (ml.term) MONTH), '%Y%m') < DATE_FORMAT(lp.payment_date, '%Y%m')
					AND SUBSTR(lp.payment_date,1,6) = '".$loan_date."'
					AND ml.close_flag = '1'
					AND ml.loan_code  = 'MNIL'
					AND ml.status_flag = '2'
					AND ml.loan_date >= '20100701'
					
				ORDER BY company_code";
			
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		log_message('debug', 'xx-xx '.$query);
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
	}
	
	/**
     * @desc Retrieve list of loan payment overdue regardless of capital balance of employees
     * @param $acctgPeriod
     */
	function getCOMakersMembersLoanException()
	{
		$sql = "SELECT l.loan_no AS loan_no
					,l.loan_code AS loan_code
					,l.employee_id AS employee_id
					,m.last_name AS last_name				
					,m.first_name AS first_name	
					,l.principal_balance AS principal_balance	
					,lg.guarantor_id AS guarantor_id
					,gn.last_name AS guarantor_last_name
					,gn.first_name AS guarantor_first_name
					,exc1.remark AS remark	
				FROM 
				(      						
					  SELECT guarantor_id, m_loan.employee_id, 'A Comaker cannot guarantee more than one Spot Cash loan for the same employee' AS Remark 						
					  FROM m_loan						
					  INNER JOIN m_loan_guarantor						
					   ON (m_loan_guarantor.loan_no = m_loan.loan_no)					
					  WHERE principal_balance > 0 						
						   AND (m_loan.status_flag = 2)						
						   AND loan_code = 'SPCL' 						
					  GROUP BY guarantor_id, m_loan.employee_id						
					  HAVING COUNT(*) > 1 						
										
					  UNION 						
										
					  SELECT  guarantor_id, m_loan.employee_id, 'A Comaker cannot guarantee more than one Consumption Loan for the same employee' AS Remark 						
					  FROM m_loan						
					  INNER JOIN m_loan_guarantor						
						ON (m_loan_guarantor.loan_no = m_loan.loan_no)					
					  WHERE principal_balance > 0 						
							AND (m_loan.status_flag = 2)						
							AND loan_code= 'CONL' 						
					  GROUP BY guarantor_id, m_loan.employee_id						
					  HAVING COUNT(*) > 1 						
				) AS exc1 						
										
				INNER JOIN m_loan l 						
					ON (exc1.employee_id = l.employee_id)					
				INNER JOIN m_loan_guarantor lg 						
					ON (l.loan_no = lg.loan_no AND exc1.guarantor_id = lg.guarantor_id)					
				INNER JOIN m_employee gn 						
					ON (lg.guarantor_id = gn.employee_id)					
				INNER JOIN m_employee m 						
					ON (l.employee_id = m.employee_id)";			
		
		return $sql;
	}
	
	/**
     * @desc Retrieve list of loan payment overdue regardless of capital balance of employees
     * @param $acctgPeriod
     */
	function getCoMakerNoOfLoansException($curr_date)
	{
		$sql = "SELECT exc1.loan_no AS loan_no				
						,l.loan_code AS loan_code			
						,l.employee_id AS employee_id			
						,m.last_name AS last_name			
						,m.first_name AS first_name			
						,l.principal_balance AS principal_balance			
						,lg.guarantor_id AS guarantor_id			
						,gn.last_name AS guarantor_last_name			
						,gn.first_name AS guarantor_first_name			
						,exc1.Remark AS remark		
				FROM  				
				(      				
					SELECT m_loan.loan_no			
						, exc.guarantor_id		
						, Remark 		
						FROM m_loan				
								
					   INNER JOIN m_loan_guarantor				
					ON (m_loan.loan_no = m_loan_guarantor.loan_no)			
					  INNER JOIN				
						(SELECT m_employee.hire_date,guarantor_id, 'Member with <3 YOS can only guarantee 1 Spot Cash and 1 Consumption Loan Only' AS Remark 		
						  FROM m_loan		
						   INNER JOIN m_loan_guarantor		
							ON (m_loan_guarantor.loan_no = m_loan.loan_no) 	
						   INNER JOIN m_employee		
							ON (m_employee.employee_id = m_loan_guarantor.guarantor_id)	
						   WHERE principal_balance > 0 		
							AND (m_loan.status_flag = '2')	
							AND loan_code = 'CONL'	
						   GROUP BY guarantor_id, COALESCE(('".$curr_date."' - hire_date)/10000,0) 		
						   HAVING COALESCE(('".$curr_date."' - hire_date)/10000,0)  < 3 AND COUNT(*) > 1		
						  UNION 		
								
						  SELECT m_employee.hire_date,guarantor_id, 'Member can only guarantee 1 Spot Cash and 1 Consumption Loan Only' AS Remark 		
						  FROM m_loan		
						  INNER JOIN m_loan_guarantor		
							  ON (m_loan_guarantor.loan_no = m_loan.loan_no)		
						  INNER JOIN m_employee		
							  ON (m_employee.employee_id = m_loan_guarantor.guarantor_id)		
						  WHERE principal_balance > 0 		
							  AND (m_loan.status_flag = '2')		
							  AND loan_code = 'SPCL'
						  GROUP BY guarantor_id, COALESCE(('".$curr_date."' - hire_date)/10000,0)		
						  HAVING COALESCE(('".$curr_date."' - hire_date)/10000,0) < 3 AND COUNT(*) > 1 		
						 UNION 		
								
						SELECT m_employee.hire_date,guarantor_id, 'Member with >=3 YOS can guarantee up to 4 loans only' AS Remark 		
						FROM m_loan		
						INNER JOIN m_loan_guarantor		
							  ON (m_loan_guarantor.loan_no = m_loan.loan_no)		
						INNER JOIN m_employee		
							  ON (m_employee.employee_id = m_loan_guarantor.guarantor_id)		
						WHERE principal_balance > 0 		
							  AND (m_loan.status_flag = 2)		
						GROUP BY guarantor_id, COALESCE(('".$curr_date."' - hire_date)/10000,0) 		
						HAVING COALESCE(('".$curr_date."' - hire_date)/10000,0)  >= 3 AND COUNT(*) > 4 		
				   ) AS exc 				
				   ON exc.guarantor_id = m_loan_guarantor.guarantor_id				
				) AS exc1 				
								
				INNER JOIN m_loan l 				
					ON (exc1.loan_no = l.loan_no)			
				INNER JOIN m_loan_guarantor lg 				
					ON (exc1.loan_no = lg.loan_no AND exc1.guarantor_id = lg.guarantor_id)			
				INNER JOIN m_employee gn 				
					ON (lg.guarantor_id = gn.employee_id)			
				INNER JOIN m_employee m 				
					ON (l.employee_id = m.employee_id)";		
		
		return $sql;
	}

}
?>