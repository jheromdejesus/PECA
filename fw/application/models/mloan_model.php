<?php

class Mloan_model extends Asi_Model {
	var $table_name = 'm_loan';
	var $id = 'loan_no';
	var $model_data = null;
	var $date_columns = array('');
	
	
    function Mloan_model() {
        parent::Asi_Model();
        $this->load->model('parameter_model');
        $this->load->model('capitalcontribution_model');
		$this->load->model('Member_model');
    }
	
	function getEmployeeMris($currDate){
		$year_month = substr($currDate, 0, 6);
		
		$sql = "SELECT employee_id, mri_due_amount, loan_no, mri_due_date 
				FROM m_loan
				WHERE mri_due_amount <> ''
				AND mri_due_amount > 0
				AND mri_due_date <> ''
				AND mri_due_date LIKE '{$year_month}%'
				AND close_flag = 0";
				
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
			
		return $this->checkError($result->result_array(), $count, $query);	
	}
	
	function getEmployeeFips($currDate){
		$year_month = substr($currDate, 0, 6);
		
		$sql = "SELECT employee_id, fip_due_amount, loan_no, fip_due_date 
				FROM m_loan
				WHERE fip_due_amount <> ''
				AND fip_due_amount > 0
				AND fip_due_date <> ''
				AND fip_due_date LIKE '{$year_month}%'
				AND close_flag = 0";
				
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
			
		return $this->checkError($result->result_array(), $count, $query);	
	}
	
	function getAllEmployeeMriFips($year_month){
		$sql = "SELECT ml.employee_id, CONCAT(me.first_name,' ', me.last_name) AS employee_name
			, COALESCE(SUM(CASE WHEN (mri_due_date LIKE '{$year_month}%' AND mri_due_date <> '') THEN
						mri_due_amount
					ELSE 0
					END 
			),0) AS mri_premium
			, COALESCE(SUM(CASE WHEN (fip_due_date LIKE '{$year_month}%' AND fip_due_date <> '') THEN 
						fip_due_amount
					ELSE 0
					END 
			),0) AS fip_premium
			
		FROM m_loan ml
		INNER JOIN m_employee me
			ON(me.employee_id = ml.employee_id)
		WHERE close_flag = 0
		GROUP BY employee_id
		HAVING (mri_premium + fip_premium > 0)";
				
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
			
		return $this->checkError($result->result_array(), $count, $query);	
	}
	
	/**
	 * @desc Retrieves previously suspended employees due to MRI FIP
	*/
	//modified by asi466: issue#0008401
	//modified by asi466: issue#9 google share doc
	function getPrevMriFipSuspended($lastWorkingDay){
		$sql = "SELECT me.employee_id AS employee_id
					,CONCAT(me.first_name,' ', me.last_name) AS employee_name
					,DATE_FORMAT(ts.due_date, '%m/%d/%Y') AS due_date 
					,DATE_FORMAT(DATE_ADD(ts.due_date, INTERVAL (1) MONTH), '%m/01/%Y') AS suspended
					,DATE_FORMAT(DATE_ADD(ts.due_date, INTERVAL 7 MONTH), '%m/01/%Y') AS lifting
					,DATE_FORMAT(DATE_ADD(ts.due_date, INTERVAL 7 MONTH), '%Y%m%01') AS lifting_temp
				FROM tbl_suspension ts
				INNER JOIN m_employee me
					ON (me.employee_id = ts.employee_id)
				WHERE ts.due_date < '{$lastWorkingDay}'
				AND ts.suspension_type = 'MRFS'
				AND ts.status_flag = '1'
				HAVING lifting_temp > '{$lastWorkingDay}'
				ORDER BY ts.due_date, ts.employee_id";
				
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
			
		return $this->checkError($result->result_array(), $count, $query);	
	}
    
    /**
	 * @desc Retrieves list of loans that can be restructured of the selected employee 
	 * @return array
	 */
	function getRestructuredLoans($filter = null, $limit = null, $offset = null, $select = null, $orderby = null) 
	{
    	if($select)
    		$this->db->select($select);
    	$this->db->from('m_loan tl');
    	$this->db->join('r_loan_header rl', 'rl.loan_code=tl.loan_code', 'left outer');
    	if($filter)	
    		$this->db->where($filter);
    	if($orderby)	
    		$this->db->orderby($orderby);	
    	$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
    	$count = $result->num_rows();
    
		return $this->checkError($result->result_array(), $count, $query);
    }
	
	/**
	 * @desc Retrieves list of loans that can be restructured of the selected employee 
	 * @return array
	 */
	function getRestructuredLoanInfo($filter = null, $limit = null, $offset = null, $select = null, $orderby = null) 
	{
    	if($select)
    		$this->db->select($select);
    	$this->db->from('m_loan tl');
    	$this->db->join('m_loan_guarantor tlg', 'tlg.loan_no=tl.loan_no', 'left outer');
    	$this->db->join('r_loan_header rl', 'rl.loan_code=tl.loan_code', 'left outer');
    	if($filter)	
    		$this->db->where($filter);
    	if($orderby)	
    		$this->db->orderby($orderby);	
    	$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
    	$count = $result->num_rows();
    	
		return $this->checkError($result->result_array(), $count, $query);
    }
    
    function retrieveActiveLoans($current_period){
    	$year = substr($current_period, 0, 4);
    	$month = substr($current_period, 4, 2);
    	$sql = "SELECT loan_no
					,loan_code
					,interest_rate										
					,amortization_startdate										
					,principal_balance										
					,term										
				FROM m_loan												
				WHERE amortization_startdate < '{$year}'
					AND MONTH(amortization_startdate) = {$month}
					AND close_flag = 0
					#AND principal_balance > 0";
		
    	$query = $this->db->query($sql);
//    	echo $this->db->last_query();
    	
    	return $query->result_array();
    }
    
    function retrieveLoanCC($period, $company_code, $loan_code){
    ##### NRB EDIT START #####
    if($loan_code=="MNIL" || $loan_code=="MNI3"){
	/* if($loan_code=="MNIL"){ */
	##### NRB EDIT END #####
		$period2 = substr($period, 0, 6);
		/* START Modified for Diminishing Balance Issues by ASI372 2013/05/06 */
		$sql = "SELECT DISTINCT tl.loan_no									
					, tl.loan_code								
					, tl.employee_id								
					, ipl.parameter_value AS payment_code							
					, tl.employee_principal_amort								
					, tl.employee_interest_amortization								
					, tl.principal_balance
					, 'true' AS is_charged
					, 'false' AS is_suspend
					, rl.bsp_computation
					, tl.interest_rate
				FROM m_loan tl									
				INNER JOIN i_parameter_list ipl									
					ON CONCAT(tl.loan_code, 'CC') = ipl.parameter_id								
				INNER JOIN m_employee mm									
					ON mm.employee_id = tl.employee_id								
				LEFT OUTER JOIN m_loan_payment tlp									
					ON tlp.loan_no = tl.loan_no 								
						AND tlp.payment_date >= '{$period}'						
						AND tlp.status_flag = '2'
				LEFT OUTER JOIN t_transaction tt 
					ON tt.reference = CONCAT(tlp.loan_no, ',', tlp.transaction_code, ',', tlp.payment_date, ',', tlp.payor_id) 
					AND tt.status_flag = '2'
				INNER JOIN r_loan_header rl
					ON (rl.loan_code = tl.loan_code)		
				WHERE(tlp.loan_no IS NULL OR tt.reference IS NOT NULL) 								
					AND tl.loan_code = '{$loan_code}'								
					AND tl.close_flag = 0							
					AND mm.company_code = '{$company_code}'								
					AND tl.loan_date < '{$period}'								
					AND tl.status_flag = '2'
					AND tl.term = 2 
					AND SUBSTR({$period},1,6) - SUBSTR(tl.amortization_startdate, 1, 6) = 0 
					
				UNION
				
				SELECT DISTINCT tl.loan_no									
					, tl.loan_code								
					, tl.employee_id								
					, ipl.parameter_value AS payment_code							
					, tl.employee_principal_amort								
					, tl.employee_interest_amortization								
					, tl.principal_balance	
					, 'true' AS is_charged
					, 'true' AS is_suspend
					, rl.bsp_computation
					, tl.interest_rate
				FROM m_loan tl									
				INNER JOIN i_parameter_list ipl									
					ON CONCAT(tl.loan_code, 'CC') = ipl.parameter_id								
				INNER JOIN m_employee mm									
					ON mm.employee_id = tl.employee_id								
				LEFT OUTER JOIN (SELECT m_loan_payment.loan_no
					, balance 
					,transaction_code
					,payment_date 
					,payor_id
					FROM (m_loan_payment) 
					INNER JOIN (SELECT m_loan_payment.loan_no
							, MAX(modified_date) AS LastPay 
						FROM m_loan_payment 
						WHERE payment_date  >= '{$period}'	
						AND status_flag IN (2,3)
						GROUP BY loan_no) LP1 
					ON m_loan_payment.loan_no=LP1.loan_no AND m_loan_payment.modified_date=LP1.LastPay
					) tlp 
					ON (tl.loan_no = tlp.loan_no)
				LEFT OUTER JOIN t_transaction tt 
					ON tt.reference = CONCAT(tlp.loan_no, ',', tlp.transaction_code, ',', tlp.payment_date, ',', tlp.payor_id) 
					AND tt.status_flag = '2'
				INNER JOIN r_loan_header rl
					ON (rl.loan_code = tl.loan_code)		
				WHERE (tlp.loan_no IS NULL OR tlp.balance > 0 OR tt.reference IS NOT NULL) 							
					AND tl.loan_code = '{$loan_code}'								
					AND tl.close_flag = 0							
					AND mm.company_code = '{$company_code}'								
					AND tl.loan_date < '{$period}'								
					AND tl.status_flag = '2'
					AND (
							tl.term = 1
							OR
							tl.term = 2 AND SUBSTR({$period},1,6) - SUBSTR(tl.amortization_startdate,1,6) > 0
							)
				UNION #All miniloans fully paid but are late
					
					SELECT ml.loan_no	
						,ml.loan_code
						,ml.employee_id	
						,ipl.parameter_value AS payment_code
						,ml.employee_principal_amort								
						,ml.employee_interest_amortization								
						,ml.principal_balance	
						, 'false' AS is_charged
						, 'true' AS is_suspend
						, rl.bsp_computation
						, ml.interest_rate
					FROM m_loan ml
					INNER JOIN i_parameter_list ipl									
						ON CONCAT(ml.loan_code, 'CC') = ipl.parameter_id
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
					INNER JOIN r_loan_header rl
						ON (rl.loan_code = ml.loan_code)			
					WHERE CONCAT(DATE_FORMAT(DATE_ADD(ml.loan_date, INTERVAL (ml.term + 1) MONTH), '%Y%m'),17) <= lp.payment_date
					AND SUBSTR(lp.payment_date,1,6) = '{$period2}'
					AND ml.close_flag = '1'
					AND ml.loan_code  = '{$loan_code}'
					AND ml.status_flag = '2'
					AND ml.loan_date >= '20100701'
					ANd me.company_code = '{$company_code}'
			";
		/* END Modified for Diminishing Balance Issues by ASI372 2013/05/06 */	
	}
	else{
		/* START Modified for Diminishing Balance Issues by ASI372 2013/05/06 */
    	$sql = "SELECT DISTINCT tl.loan_no									
					, tl.loan_code								
					, tl.employee_id								
					, ipl.parameter_value AS payment_code							
					, tl.employee_principal_amort								
					, tl.employee_interest_amortization								
					, tl.principal_balance								
					, rl.bsp_computation
					, tl.interest_rate
				FROM m_loan tl									
				INNER JOIN i_parameter_list ipl									
					ON CONCAT(tl.loan_code, 'CC') = ipl.parameter_id								
				INNER JOIN m_employee mm									
					ON mm.employee_id = tl.employee_id								
				LEFT OUTER JOIN m_loan_payment tlp									
					ON tlp.loan_no = tl.loan_no 								
						AND tlp.payment_date >= '{$period}'						
						AND tlp.status_flag = '2'
				INNER JOIN r_loan_header rl
					ON (rl.loan_code = tl.loan_code)				
				WHERE tlp.loan_no IS NULL									
					AND tl.loan_code = '{$loan_code}'								
					AND tl.close_flag = 0							
					AND mm.company_code = '{$company_code}'								
					AND tl.loan_date < '{$period}'								
					AND tl.status_flag = '2'";
		/* END Modified for Diminishing Balance Issues by ASI372 2013/05/06 */		
	}	
    	$query = $this->db->query($sql);  
    	return $query->result_array();		
    }
    
    function retrieveLoanCCBalance($employee_id){
    	$cc_bal = 0;
    	
    	$sql = "SELECT SUM(capital_contribution_balance) AS requirement 											
				FROM m_loan 											
				WHERE employee_id = '{$employee_id}'										
					AND status_flag = '2'";		
    	$query = $this->db->query($sql);      	
    	$ret_val = $query->result_array(); 	
    	$cc_bal = $ret_val[0]['requirement'];
    	
    	$sql = "SELECT SUM(capital_contribution_balance) AS requirement 											
				FROM t_loan 											
				WHERE employee_id = '{$employee_id}'										
					AND status_flag = '1'";		
    	$query = $this->db->query($sql);      	
    	$ret_val = $query->result_array(); 	
    	$cc_bal += $ret_val[0]['requirement'];
    	
    	return $cc_bal;
    }
    
    function retrieveRestructuredOneThird($employee_id){
		$sql = "SELECT SUM(restructure_amount) AS sum_restructured_amount
				FROM t_loan
				WHERE employee_id = '{$employee_id}'
				AND status_flag=1";
				
		$query = $this->db->query($sql); 
		$ret_val = $query->result_array();

		if(isset($ret_val[0]['sum_restructured_amount'])){
			return $ret_val[0]['sum_restructured_amount'] / 3;
		}
		else{
			return 0;
		}
	}
	
 	function retrieveNMPLEmployee($cur_date){
		$year = substr($cur_date, 0, 4);
    	$month = substr($cur_date, 4, 2);
		
		##### NRB EDIT START #####
		
    	$sql = "SELECT tl.employee_id									
				FROM m_loan tl									
				LEFT OUTER JOIN m_loan_payment tlp									
					ON tlp.payment_date LIKE '{$year}{$month}%'								
						AND tlp.loan_no = tl.loan_no
						AND tlp.status_flag = '2'							
				INNER JOIN m_employee mm									
					ON mm.employee_id = tl.employee_id								
				WHERE tl.loan_date <= '{$cur_date}'									
					AND (tl.loan_code = 'MNIL' OR tl.loan_code = 'MNI3')
					#AND tl.principal_balance > 0								
					AND tl.close_flag = '0'								
					AND tlp.loan_no IS NULL	
					AND tl.status_flag = '2'
				ORDER BY tl.loan_date ASC							
				    	";
		
/*
    	$sql = "SELECT tl.employee_id									
				FROM m_loan tl									
				LEFT OUTER JOIN m_loan_payment tlp									
					ON tlp.payment_date LIKE '{$year}{$month}%'								
						AND tlp.loan_no = tl.loan_no
						AND tlp.status_flag = '2'							
				INNER JOIN m_employee mm									
					ON mm.employee_id = tl.employee_id								
				WHERE tl.loan_date <= '{$cur_date}'									
					AND tl.loan_code = 'MNIL'
					#AND tl.principal_balance > 0								
					AND tl.close_flag = '0'								
					AND tlp.loan_no IS NULL	
					AND tl.status_flag = '2'
				ORDER BY tl.loan_date ASC							
				    	";
*/
		
		##### NRB EDIT END #####
		
    	$result = $this->db->query($sql);
    	//echo $this->db->last_query();
    	return $result->result_array();
    }
    
	//enhancement
	function retrieveNMPLEmployee2($cur_date){
		$year = substr($cur_date, 0, 4);
    	$month = substr($cur_date, 4, 2);
		
		##### NRB EDIT START #####
		
    	$sql = " #2 terms, 1st month : no payment
    			SELECT tl.employee_id
				,tl.loan_no
				,'true' AS charged
				FROM m_loan tl									
				LEFT OUTER JOIN m_loan_payment tlp									
					ON tlp.payment_date LIKE '{$year}{$month}%'								
						AND tlp.loan_no = tl.loan_no
						AND tlp.status_flag = '2'	
				LEFT OUTER JOIN t_transaction tt 
					ON tt.reference = CONCAT(tlp.loan_no, ',', tlp.transaction_code, ',', tlp.payment_date, ',', tlp.payor_id) 
					AND tt.status_flag = '2'
				INNER JOIN m_employee mm									
					ON mm.employee_id = tl.employee_id								
				WHERE tl.loan_date < '{$year}{$month}01'									
					AND (tl.loan_code = 'MNIL' OR tl.loan_code = 'MNI3')					
					AND tl.close_flag = '0'								
					AND (tlp.loan_no IS NULL OR tt.reference IS NOT NULL) 
					AND tl.status_flag = '2'
					AND tl.term = 2 
					AND {$year}{$month} - SUBSTR(tl.amortization_startdate, 1, 6) = 0 
					#AND 
					
				UNION
				#1 & 2 terms (2nd month), not fully paid 
				SELECT tl.employee_id
				,tl.loan_no
				,'true' AS charged				
				FROM m_loan tl									
				LEFT OUTER JOIN (SELECT m_loan_payment.loan_no
					, balance 
					,transaction_code
					,payment_date 
					,payor_id
					FROM (m_loan_payment) 
					INNER JOIN (SELECT m_loan_payment.loan_no
							, MAX(modified_date) AS LastPay 
						FROM m_loan_payment 
						WHERE payment_date LIKE '{$year}{$month}%' 
						AND status_flag IN (2,3)
						GROUP BY loan_no) LP1 
					ON m_loan_payment.loan_no=LP1.loan_no AND m_loan_payment.modified_date=LP1.LastPay
					) lp 	
				ON (tl.loan_no = lp.loan_no)
				LEFT OUTER JOIN t_transaction tt 
					ON tt.reference = CONCAT(lp.loan_no, ',', lp.transaction_code, ',', lp.payment_date, ',', lp.payor_id) 
					AND tt.status_flag = '2'
				INNER JOIN m_employee mm									
					ON mm.employee_id = tl.employee_id								
				WHERE tl.loan_date < '{$year}{$month}01'	
					AND (lp.loan_no IS NULL OR lp.balance > 0 OR tt.reference IS NOT NULL) 
					AND (tl.loan_code = 'MNIL' OR tl.loan_code = 'MNI3')							
					AND tl.close_flag = '0'
					AND tl.status_flag = '2'
					AND (
							tl.term = 1
							OR
							(tl.term = 2 AND {$year}{$month} - SUBSTR(tl.amortization_startdate,1,6) > 0)
							)	

				UNION 
				#All miniloans fully paid but are late					
				SELECT ml.employee_id
				,ml.loan_no
				,'false' AS charged
				FROM m_loan ml	
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
				WHERE CONCAT(DATE_FORMAT(DATE_ADD(ml.loan_date, INTERVAL (ml.term + 1) MONTH), '%Y%m'),17) <= lp.payment_date
				AND SUBSTR(lp.payment_date,1,6) = '{$year}{$month}'
				AND ml.close_flag = '1'
				AND (ml.loan_code  = 'MNIL' OR ml.loan_code  = 'MNI3')
				AND ml.status_flag = '2'
				AND ml.loan_date >= '20100701'							
				";
				
    	/*
$sql = " #2 terms, 1st month : no payment
    			SELECT tl.employee_id
				,tl.loan_no
				,'true' AS charged
				FROM m_loan tl									
				LEFT OUTER JOIN m_loan_payment tlp									
					ON tlp.payment_date LIKE '{$year}{$month}%'								
						AND tlp.loan_no = tl.loan_no
						AND tlp.status_flag = '2'	
				LEFT OUTER JOIN t_transaction tt 
					ON tt.reference = CONCAT(tlp.loan_no, ',', tlp.transaction_code, ',', tlp.payment_date, ',', tlp.payor_id) 
					AND tt.status_flag = '2'
				INNER JOIN m_employee mm									
					ON mm.employee_id = tl.employee_id								
				WHERE tl.loan_date < '{$year}{$month}01'									
					AND tl.loan_code = 'MNIL'								
					AND tl.close_flag = '0'								
					AND (tlp.loan_no IS NULL OR tt.reference IS NOT NULL) 
					AND tl.status_flag = '2'
					AND tl.term = 2 
					AND {$year}{$month} - SUBSTR(tl.amortization_startdate, 1, 6) = 0 
					#AND 
					
				UNION
				#1 & 2 terms (2nd month), not fully paid 
				SELECT tl.employee_id
				,tl.loan_no
				,'true' AS charged				
				FROM m_loan tl									
				LEFT OUTER JOIN (SELECT m_loan_payment.loan_no
					, balance 
					,transaction_code
					,payment_date 
					,payor_id
					FROM (m_loan_payment) 
					INNER JOIN (SELECT m_loan_payment.loan_no
							, MAX(modified_date) AS LastPay 
						FROM m_loan_payment 
						WHERE payment_date LIKE '{$year}{$month}%' 
						AND status_flag IN (2,3)
						GROUP BY loan_no) LP1 
					ON m_loan_payment.loan_no=LP1.loan_no AND m_loan_payment.modified_date=LP1.LastPay
					) lp 	
				ON (tl.loan_no = lp.loan_no)
				LEFT OUTER JOIN t_transaction tt 
					ON tt.reference = CONCAT(lp.loan_no, ',', lp.transaction_code, ',', lp.payment_date, ',', lp.payor_id) 
					AND tt.status_flag = '2'
				INNER JOIN m_employee mm									
					ON mm.employee_id = tl.employee_id								
				WHERE tl.loan_date < '{$year}{$month}01'	
					AND (lp.loan_no IS NULL OR lp.balance > 0 OR tt.reference IS NOT NULL) 
					AND tl.loan_code = 'MNIL'								
					AND tl.close_flag = '0'
					AND tl.status_flag = '2'
					AND (
							tl.term = 1
							OR
							(tl.term = 2 AND {$year}{$month} - SUBSTR(tl.amortization_startdate,1,6) > 0)
							)	

				UNION 
				#All miniloans fully paid but are late					
				SELECT ml.employee_id
				,ml.loan_no
				,'false' AS charged
				FROM m_loan ml	
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
				WHERE CONCAT(DATE_FORMAT(DATE_ADD(ml.loan_date, INTERVAL (ml.term + 1) MONTH), '%Y%m'),17) <= lp.payment_date
				AND SUBSTR(lp.payment_date,1,6) = '{$year}{$month}'
				AND ml.close_flag = '1'
				AND ml.loan_code  = 'MNIL'
				AND ml.status_flag = '2'
				AND ml.loan_date >= '20100701'							
				";
*/
				
		##### NRB EDIT END #####
		
    	$result = $this->db->query($sql);
		log_message('debug', 'xx-xx '.$this->db->last_query());
    	//echo $this->db->last_query();
    	return $result->result_array();
    }
	
	/**
	 * @desc To retrieve all Loan Transactions
	 * @return array
	 */
	function getLoanList($filter = null, $limit = null, $offset = null, $select = null, $orderby = null, $distinct = null) 
	{
	if($distinct)
		$this->db->distinct();
	if($select)
    		$this->db->select($select);
    	$this->db->from('m_loan ml');
    	$this->db->join('m_employee me', 'me.employee_id=ml.employee_id', 'inner');
    	$this->db->join('r_loan_header rl', 'rl.loan_code=ml.loan_code', 'inner');
    	if($filter)	
    		$this->db->where($filter);
    	if($orderby)	
    		$this->db->orderby($orderby);	
    	$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
    	
    	
		if($filter){
			if($distinct)
				$this->db->select("COUNT(DISTINCT `ml`.`employee_id`) AS `numrows` ");
			$this->db->where($filter);
			$this->db->from('m_loan ml');
	    	$this->db->join('m_employee me', 'me.employee_id=ml.employee_id', 'inner');
	    	$this->db->join('r_loan_header rl', 'rl.loan_code=ml.loan_code', 'inner');
	    	if($distinct){
	    		$data = $this->db->get();
	    		$data = $data->result_array();
	    		$count = $data[0]['numrows'];  	
	    	} else {
				$count = $this->db->count_all_results();
	    	}
		}else{
			$count = $this->db->count_all($this->table_name);
		}
    	
		$query1 = $this->db->last_query();
		log_message('debug', 'queryyyzz '.$query1);
    	return $this->checkError($result->result_array(), $count, $query.$query1);
    }
    
	function retrieveLoanCodesForTCC($filter = null, $limit = null, $offset = null, $select = null, $orderby = null){
		if($select)
    		$this->db->distinct('ml.loan_code');
    		$this->db->select($select);
    	$this->db->from('m_loan ml');
		$this->db->join('r_loan_header rl', 'rl.loan_code=ml.loan_code', 'inner');
    	if($filter)	
    		$this->db->where($filter);
    	if($orderby)	
    		$this->db->orderby($orderby);	
    	$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
    	$count = $this->db->count_all($this->table_name);
    
    	return $this->checkError($result->result_array(), $count, $query);
    }
	
	function retrieveLoansForTCC($filter = null, $limit = null, $offset = null, $select = null, $orderby = null){
		if($select)
    		$this->db->select($select);
    	$this->db->from('m_loan ml');
    	$this->db->join('m_employee mm', 'mm.employee_id=ml.employee_id', 'inner');
		$this->db->join('t_transaction tt', 'tt.reference=ml.loan_no AND source=\'m_loan\'', 'left outer');
    	if($filter)	
    		$this->db->where($filter);
    	$this->db->group_by('mm.company_code');
    	if($orderby)	
    		$this->db->orderby($orderby);	
    	$this->db->limit($offset, $limit);
    	$this->db->having('grand_total > 0'); 
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
		log_message('debug', 'retrieveLoansForTCC '.$query);
    	$count = $this->db->count_all($this->table_name);
    
    	return $this->checkError($result->result_array(), $count, $query);
    }
    
    function retrieveLoanCodesForTCT($filter = null, $limit = null, $offset = null, $select = null, $orderby = null){
		if($select)
    		$this->db->distinct('loan_code');
    		$this->db->select($select);
    	$this->db->from('m_loan');
    	if($filter)	
    		$this->db->where($filter);
    	if($orderby)	
    		$this->db->orderby($orderby);	
    	$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
    	$count = $this->db->count_all($this->table_name);
    
    	return $this->checkError($result->result_array(), $count, $query);
    }
    
	function retrieveLoansForTCT($filter = null, $limit = null, $offset = null, $select = null, $orderby = null){
		if($select)
    		$this->db->select($select);
    	$this->db->from('m_loan ml');
    	$this->db->join('m_employee mm', 'mm.employee_id=ml.employee_id', 'inner');
		if($filter)	
    		$this->db->where($filter);
    	$this->db->group_by('ml.loan_code, mm.company_code');
    	if($orderby)	
    		$this->db->orderby($orderby);	
    	$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
    	$count = $this->db->count_all($this->table_name);
    
    	return $this->checkError($result->result_array(), $count, $query);
    }
    
	function retrieveLoanCodesForTCE($filter = null, $limit = null, $offset = null, $select = null, $orderby = null){
		if($select)
    		$this->db->distinct('ml.loan_code');
    		$this->db->select($select);
    	$this->db->from('m_loan ml');
		$this->db->join('r_loan_header rl', 'rl.loan_code=ml.loan_code', 'inner');
    	if($filter)	
    		$this->db->where($filter);
    	if($orderby)	
    		$this->db->orderby($orderby);	
    	$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
    	$count = $this->db->count_all($this->table_name);
    
    	return $this->checkError($result->result_array(), $count, $query);
    }
	
	function retrieveLoansForTCE($loan_code,$employee_id,$loan_date)
	{
		$sql = "SELECT COALESCE(SUM(principal_balance),0) AS beginning_balance
				FROM m_loan ml
				WHERE ml.principal_balance > 0 
					AND ml.status_flag = '2'
					AND ml.loan_code ='$loan_code' 
					AND ml.employee_id='$employee_id'
					AND ml.loan_date <= '$loan_date'
				GROUP BY ml.employee_id
				ORDER BY ml.employee_id ASC";	
				
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);			
    }
	
	function getLoanCodesForPDAuditTrail($filter = null, $limit = null, $offset = null, $select = null, $orderby = null, $groupby = null, $distinct = null)
    {
    	if($select) {
    		if ($distinct) $this->db->distinct($distinct);
    		//else $this->db->distinct('ml.loan_code AS loan_code');
    		$this->db->select($select);
    	}
    	$this->db->from('m_loan ml');
    	$this->db->join('m_loan_payment mlp', 'mlp.loan_no=ml.loan_no', 'inner');
    	if($filter)	
    		$this->db->where($filter);
    	if($groupby)	
    		$this->db->group_by($groupby);	
    	if($orderby)	
    		$this->db->order_by($orderby);	
    	$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
    	
    	$count = $this->db->count_all($this->table_name);  
  		
		return $this->checkError($result->result_array(), $count, $query);
    }
    
	function getLoanTotalsForPDAudit($transaction_date, $company_code)
	{
		$sql = "SELECT SUM(mlp.amount) AS amort
					,SUM(mlp.interest_amount) AS interest
					,ml.loan_code
				FROM m_loan ml
				INNER JOIN m_loan_payment mlp
					ON (mlp.loan_no=ml.loan_no)
				INNER JOIN m_employee me
					ON me.employee_id = ml.employee_id
				WHERE mlp.payment_date LIKE '$transaction_date%'
					AND me.company_code = '$company_code'
					AND mlp.source='P'
					AND mlp.status_flag='2'
				GROUP BY ml.loan_code";
				
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);				
	}
	
	
	/**
	 * @desc Shows the employee's Capital Contribution, Required Balance and Max Withdrawable Amount
	 */
	function showBalanceInfo($employee_id, $acctng_period){
		$capconBal = $this->capitalcontribution_model->retrieveCapConBalance($employee_id, $acctng_period);
		
		$reqBal = $this->retrieveReqBalance($employee_id);
		log_message('debug', 'reqBal: '. $reqBal);
		$ccMinBal = $this->parameter_model->getParam('CCMINBAL');
		
		if($ccMinBal>$reqBal){
			$reqBal = $ccMinBal;
		}
		
		$maxWdwlAmount = $capconBal - $reqBal;
		
		if($maxWdwlAmount<0){
			$maxWdwlAmount = 0;
		}
		
		$retiree_wdwlamount = 0;
		//check if retiree
		if ($this->showCompCode($employee_id)=="920"){
			$retiree_wdwlamount = $this->allowedWdwlForRetiree($employee_id);
			log_message('debug', 'Inside showBalanceInfo: xxxxxxxxxxxxxxxx' . $retiree_wdwlamount);
			$maxWdwlAmount = ($maxWdwlAmount < $retiree_wdwlamount) ? $maxWdwlAmount : $retiree_wdwlamount;
		}
			
		
		log_message('debug', 'Inside showBalanceInfo: capconBal: '. $capconBal);
		log_message('debug', 'Inside showBalanceInfo: reqBal: '. $reqBal);
		log_message('debug', 'Inside showBalanceInfo: maxWdwlAmount: '. $maxWdwlAmount);
		
		return array(
			"capconBal" => $capconBal
			,"reqBal" => $reqBal
			,"maxWdwlAmount" => $maxWdwlAmount
		);
	}
	
	function showCompCode($employee_id){
		$data = $this->Member_model->get(array("employee_id"=>$employee_id), array(
			"COALESCE(company_code, '') AS company_code"
			));
		
		if($data['count']==0){
			return "";
		}
		else{
			return $data['list'][0]['company_code'];
		}
	}
	
	function allowedWdwlForRetiree($employee_id){
		$acctg_period = date("Ymd", strtotime($this->parameter_model->getParam('ACCPERIOD')));
		$capcon_balance = $this->capitalcontribution_model->retrieveCapConBalance($employee_id, $acctg_period);
		$capcon_balance = round($capcon_balance, 2);
		$interestAndRemainingTerms = $this->getInterestAndRemainingTerms($employee_id);
		log_message('debug', 'Inside allowedWdwlForRetiree: capcon_balancezzzz: '. $capcon_balance);
		log_message('debug', 'Inside allowedWdwlForRetiree: interestAndRemainingTermszzzz: '. $interestAndRemainingTerms);
		if(($capcon_balance - ($interestAndRemainingTerms)) < 0){
			return 0;
		}
		else{
			return round(($capcon_balance - ($interestAndRemainingTerms)),2);
		}
	}
	
	/** [START] Added by Vincent Sy for 8th Enhancement 2013/08/14
	* @desc Shows the employee's Capital Contribution, Required Balance and Max Withdrawable Amount
	*
	*/	
	function getEmployeeBalanceInfo($employee_id, $accounting_period) {
	
		$capconBal = $this->capitalcontribution_model->retrieveCapConBalance($employee_id, $accounting_period);								
										
		//Get CcMinBal								
		$ccMinBal = $this->parameter_model->getParam('CCMINBAL');								
										
		//Get employee's company code								
		$company_code = $this->showCompCode($employee_id);								
		log_message('debug', 'codessss ' . $company_code);		
		//for NWC								
		//Retrieve old Capital after Transaction / 11								
		$oldCapitalAfterTransaction = $this->capitalcontribution_model->getOldCapitalAfterTransaction($employee_id, $accounting_period);
		
		//Retrieve current Capital after Transaction / 11								
		$currentCapitalAfterTransaction = $capconBal / 11;								
		//Compare new and old capital after transaction, use the higher value as capitalAfterTransaction
		$capitalAfterTransaction = $oldCapitalAfterTransaction > $currentCapitalAfterTransaction ? $oldCapitalAfterTransaction : $currentCapitalAfterTransaction;	
		
		//jdj 08032017
		//retrieve the Highest Capital and its date
		$capitalAfterTransaction11 = $this->getHighestCapitalandDate($employee_id);
		if($capitalAfterTransaction11['list'][0]['eleven'])
			$accdate= $capitalAfterTransaction11['list'][0]['accdate'];
			$month = substr($accdate, 0,2);
			$yr = substr($accdate,2);
			switch($month) {
				case "01":
					$month="January";
					break;
				case "02":
					$month="February";
					break;
				case "03":
					$month="March";
					break;
				case "04":
					$month="April";
					break;
				case "05":
					$month="May";
					break;
				case "06":
					$month="June";
					break;
				case "07":
					$month="July";
					break;
				case "08":
					$month="August";
					break;
				case "09":
					$month="September";
					break;
				case "10":
					$month="October";
					break;
				case "11":
					$month="November";
					break;
				case "12":
					$month="December";
					break;
				default:
					$month="Error";
			}
			$accdate= $month ." ".$yr;
			
			
		if($capitalAfterTransaction11['list'][0]['eleven'])
			$eleven= $capitalAfterTransaction11['list'][0]['eleven'];	
		//end 08312017
		
		
		//[START] Modified by Kweeny Libutan for 8th Enhancement - Part 2 (RLR - Remaining Term) 2013/10/10
		log_message('debug', 'getEmployeeBalanceInfo_oldCapitalAfterTransaction=' . $oldCapitalAfterTransaction);
		log_message('debug', 'getEmployeeBalanceInfo_currentCapitalAfterTransaction=' . $currentCapitalAfterTransaction);
		//[END] Modified by Kweeny Libutan for 8th Enhancement - Part 2 (RLR - Remaining Term) 2013/10/10
		
		if($company_code == "920") {//member is a retiree
			//Retrieve RLR							
			$retireeLoanRequirement = $this->getRLR($employee_id);
			$retireeLoanRequirement1 = $retireeLoanRequirement + $capitalAfterTransaction;
			//compare three parameters							
			$nonWithdrawableCapital = max($retireeLoanRequirement1, $capitalAfterTransaction, $ccMinBal);
			
			//[START] Modified by Kweeny Libutan for 8th Enhancement - Part 2 (RLR - Remaining Term) 2013/10/10
			log_message('debug', 'getEmployeeBalanceInfo_retireee_retireeLoanRequirement=' . $retireeLoanRequirement1);
			log_message('debug', 'getEmployeeBalanceInfo_retireee_capitalAfterTransaction=' . $capitalAfterTransaction);
			log_message('debug', 'getEmployeeBalanceInfo_retireee_ccMinBal=' . $ccMinBal);
			//[END] Modified by Kweeny Libutan for 8th Enhancement - Part 2 (RLR - Remaining Term) 2013/10/10
		}			
		else {						
			//Get 1/3 ReqBalance							
			$oneThirdReqBal = $this->getLoanRequirement($employee_id);
			//compare three parameters							
			$nonWithdrawableCapital = max($oneThirdReqBal, $capitalAfterTransaction, $ccMinBal);
			
			//[START] Modified by Kweeny Libutan for 8th Enhancement - Part 2 (RLR - Remaining Term) 2013/10/10
			log_message('debug', 'getEmployeeBalanceInfo_nonRetiree_oneThirdReqBal=' . $oneThirdReqBal);
			log_message('debug', 'getEmployeeBalanceInfo_nonRetiree_capitalAfterTransaction=' . $capitalAfterTransaction);
			log_message('debug', 'getEmployeeBalanceInfo_nonRetiree_ccMinBal=' . $ccMinBal);
			//[END] Modified by Kweeny Libutan for 8th Enhancement - Part 2 (RLR - Remaining Term) 2013/10/10
		}
										
		$maxWithdrawable = $capconBal - $nonWithdrawableCapital;	
		
		//[START] Modified by Kweeny Libutan for 8th Enhancement - Part 2 (RLR - Remaining Term) 2013/10/10
		log_message('debug', 'getEmployeeBalanceInfo_capconBal=' . $capconBal);
		log_message('debug', 'getEmployeeBalanceInfo_nonWithdrawableCapital=' . $nonWithdrawableCapital);
		log_message('debug', 'getEmployeeBalanceInfo_maxWithdrawable=' . $maxWithdrawable);
		//[END] Modified by Kweeny Libutan for 8th Enhancement - Part 2 (RLR - Remaining Term) 2013/10/10
		
		return array(
			"capconBal" => $capconBal
			,"reqBal" => $nonWithdrawableCapital
			,"maxWdwlAmount" => $maxWithdrawable
			/* [Start] jdj enhancement 0829 added capcon11 for the highest capital and its month date 
			 * 
			 */
			,"capcon11" => $eleven
			,"accdate" => $accdate
		);
		
	}
	//[END] Added by Vincent Sy for 8th Enhancement 2013/08/14
	
	/** [START] Added by jdj for displaying date and highest capital
	 *  retrieves the accounting date and highest capital as basis prior to July 01 2013
	 */
	
	function getHighestCapitalandDate($employee_id) {
		
		/*
		$sql = "SELECT DATE_FORMAT(t.accounting_period,'%m%Y') AS accdate
		, (MAX(t.capital_after_transaction)*11) AS eleven FROM t_capital_contribution t
		WHERE t.employee_id= '{$employee_id}' 
		AND t.accounting_period>='20130701'";
		*/
		
		$sql = "SELECT DATE_FORMAT(MIN(b.`accounting_period`),'%m%Y') AS accdate,MAX(b.`maximum_balance`)AS eleven FROM 
		(SELECT MAX(capital_after_transaction) AS capital11 
		FROM t_capital_contribution WHERE employee_id='{$employee_id}') a
		INNER JOIN t_capital_contribution b ON a.capital11=b.`capital_after_transaction`
		where  employee_id='{$employee_id}'";
		
		$result = $this->db->query($sql);
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);	
	}
	
	
	/** [START] Added by Vincent Sy for 8th Enhancement 2013/07/08
	* to retrieve retiree with loan value
	*
	*/
	function getRLR($employee_id) {
		$total_prin_bal = 0;
		$interest = 0;
		$rem_term = 0;
		
		//[START] Modified by Kweeny Libutan for 8th Enhancement - Part 2 (RLR - Remaining Term) 2013/09/25
		$result_arr2 = $this->get(
			array('employee_id' => $employee_id
				,'principal_balance >' => ' 0'
				,'status_flag >' => ' 0')
			, array('SUM(principal_balance) as principal_balance'
				, 'MAX(ROUND(principal_balance/employee_principal_amort,2)) AS term'
				, 'MAX(interest_rate) as interest_rate'));
		//[END] Modified by Kweeny Libutan for 8th Enhancement - Part 2 (RLR - Remaining Term) 2013/09/25
		
		if($result_arr2['list'][0]['principal_balance'])
			$total_prin_bal = $result_arr2['list'][0]['principal_balance'];			
		
		if($result_arr2['list'][0]['interest_rate'])
			$interest = $result_arr2['list'][0]['interest_rate']/100;	
		
		if($result_arr2['list'][0]['term'])
			$rem_term = (ceil($result_arr2['list'][0]['term']/12));
		
		//[START] Modified by Kweeny Libutan for 8th Enhancement - Part 2 (RLR - Remaining Term) 2013/10/10
		log_message('debug', 'getRLR_totalOutstaningLoanBalance='. $total_prin_bal);
		log_message('debug', 'getRLR_highestInterest='. $interest);
		log_message('debug', 'getRLR_balanceTerm=' . $result_arr2['list'][0]['term']);
		log_message('debug', 'getRLR_remainingTerm='. $rem_term);
		//[END] Modified by Kweeny Libutan for 8th Enhancement - Part 2 (RLR - Remaining Term) 2013/10/10
		
		return round(($total_prin_bal * $interest) * $rem_term + $total_prin_bal, 2);
	}
	//[END] Added by Vincent Sy for 8th Enhancement 2013/07/08
	
	function getInterestAndRemainingTerms($employee_id){
	
		$total_prin_bal = 0;
		$interest = 0;
		$rem_term = 0;
		$result_arr = $this->readEmployeeLoanListSum($employee_id);
		
		if($result_arr['list'][0]['principal_balance']){		
			$total_prin_bal = $result_arr['list'][0]['principal_balance'];
		}
		
		if($total_prin_bal > 0){
			$curr_date = $this->parameter_model->retrieveValue("CURRDATE");	
			//gets the latest loan
			$sql = "SELECT loan_no, loan_date, term, interest_rate FROM m_loan 
					WHERE employee_id = '{$employee_id}'
					ORDER BY created_date DESC
					LIMIT 0,1";	
			
			
			$result = $this->db->query($sql);	
			$result_arr2 = $this->checkError($result->result_array(), 1, "");
			
			//start of remaining term computation
			$consumed_years = 0;
			$term = 0;
			
			if($result_arr2['list'][0]['loan_date']){
				//get query result
				$loan_date = $result_arr2['list'][0]['loan_date'];	
				
				//make loan date and current uniform in date
				 $loan_date = strtotime(substr($loan_date, 0, 6)."01");
				 $curr_date = strtotime(substr($curr_date, 0, 6)."01");	
				 $consumed_years=($curr_date-$loan_date)/(60 * 60 * 24 * 365);				 
			}
			
			if($result_arr2['list'][0]['term']){
				//$term = round($result_arr2['list'][0]['term'] / 12, 1);
				$term = $result_arr2['list'][0]['term'] / 12;			
			}
			
			$rem_term = round($term - $consumed_years, 1);			
			//end of remaining term computation
			
			//interest rate computation
			if($result_arr2['list'][0]['interest_rate']){
				$interest = $result_arr2['list'][0]['interest_rate']/100;			
			}
			
			return round(($total_prin_bal * $interest) * $rem_term + $total_prin_bal, 2);
		}
		else{
			return 0;
		}
	}
	
	//20110813 copied from membership.php
	function readEmployeeLoanListSum($employee_id){
	
		$param['ml.employee_id'] = $employee_id;//$_REQUEST['employee_id'];
		$param['ml.principal_balance >'] = '0';
		$param['ml.status_flag >'] = '0';

		$data = $this->getLoanList(
		$param
		,null
		,null
			,array(//"SUM(ml.employee_interest_amortization) AS interest_amortization"
			//,"SUM(ml.employee_principal_amort) AS principal_amortization"
			"SUM(ml.principal_balance) AS principal_balance"
			//,"SUM(ml.employee_interest_amortization + ml.employee_principal_amort) AS monthly_amortization"
			)
		,null
		,null
		);
		
		return $data;
	}		
	
	/**
	 * @desc Shows the employee's Capital Contribution, Required Balance and Max Withdrawable Amount 
	 * Removed for 8th Enhancement - Part 2 Eddie Amoto 2013/10/14
	 * Unused function
	 */
	 
	/*
	function showBalanceInfoWithTtransaction($employee_id, $acctng_period){
		$capconBal = $this->capitalcontribution_model->retrieveCapConBalanceWithTtransaction($employee_id, $acctng_period);
		
		$reqBal = $this->retrieveReqBalance($employee_id);
		$ccMinBal = $this->parameter_model->getParam('CCMINBAL');
		
		if($ccMinBal>$reqBal){
			$reqBal = $ccMinBal;
		}
		
		$maxWdwlAmount = $capconBal - $reqBal;
		if($maxWdwlAmount<0)
			$maxWdwlAmount = 0;
			
		return array(
			"capconBal" => $capconBal
			,"reqBal" => $reqBal
			,"maxWdwlAmount" => $maxWdwlAmount
		);
	}
	*/
	
	function showBalanceInfoWithTCapcon($employee_id, $acctng_period){
		$capconBal = $this->capitalcontribution_model->retrieveCapConBalanceWithTCapcon($employee_id, $acctng_period);
		
		$reqBal = $this->retrieveReqBalance($employee_id);
		$ccMinBal = $this->parameter_model->getParam('CCMINBAL');
		
		if($ccMinBal>$reqBal){
			$reqBal = $ccMinBal;
		}
		
		$maxWdwlAmount = $capconBal - $reqBal;
		if($maxWdwlAmount<0)
			$maxWdwlAmount = 0;
			
		return array(
			"capconBal" => $capconBal
			,"reqBal" => $reqBal
			,"maxWdwlAmount" => $maxWdwlAmount
		);
	}
	
	/**
	 * @desc To retrieve the required balance of an employee
	 */
	function retrieveReqBalance($employee_id){
		//$_REQUEST['member']['employee_id'] = '01528000';	
	
		/*commented to try new implementation
		$this->populate(array(
			'employee_id' => $employee_id
		));
		
		$data = $this->get(null
		,array('SUM(capital_contribution_balance) AS reqBalance'
		));
		
		if(!isset($data['list'][0]['reqBalance']))
			return 0;
		else
			return $data['list'][0]['reqBalance'];
		*/
		
		//new implementation
		$data = $this->retrieveLoanSumNeedingOneThird($employee_id);
		if(isset($data['list'][0]['principal_balance'])){ //check if isset
			if($data['list'][0]['principal_balance'] > 0){ //check if greater than 0
				return $data['list'][0]['principal_balance']/3;
			}
			else{
				return 0;
			}
		}
		else{
			return 0;
		}	
	}
	
	function retrieveLoanDebitCreditByEmployee($transaction_date, $employee_id)
	{
		$sql = "SELECT SUM(tt.transaction_amount) AS amount
				FROM t_transaction tt
				WHERE tt.transaction_date <= '$transaction_date'
					AND tt.employee_id = '$employee_id'	
					AND tt.status_flag='2'";
				
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);	
	}
	
	function retrieveLoanSumNeedingOneThird($employee_id)
	{
		$sql = "SELECT COALESCE(SUM(principal_balance),0) AS principal_balance
				FROM m_loan
				WHERE capital_contribution_balance <> ''
				AND capital_contribution_balance <> 0
				AND close_flag=0
				AND employee_id = '$employee_id'
				";
				
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		log_message('debug', 'inside retrieveLoanSumNeedingOneThird: ' . $this->db->last_query());
		return $this->checkError($result->result_array(), $count, $query);	
	}
	
	##### NRB EDIT START #####
	function retrieve_mrifip_provider($s_loan_no) {
		$s_provider = 'SELECT mri_fip_provider FROM m_loan WHERE loan_no = \''.$s_loan_no.'\'';
		
		$r_provider = $this->db->query($s_provider);
		if($r_provider->num_rows()) {
			$a_provider = $r_provider->result_array();
			return $a_provider[0]['mri_fip_provider'];
		} else {
			return FALSE;
		}
	}
	
	function get_loan_property($s_loan_no, $s_field) {
		$s_loan_code_sql = 'SELECT '.$s_field.' FROM m_loan WHERE loan_no = \''.$s_loan_no.'\'';
		
		$r_loan_code = $this->db->query($s_loan_code_sql);
		if($r_loan_code->num_rows()) {
			$a_loan_code = $r_loan_code->result_array();
			return $a_loan_code[0]['loan_code'];
		} else {
			return FALSE;
		}

	}
	
	function get_loan_payment_initial($s_loan_no) {
		$s_payment = 'SELECT principal, interest_rate FROM m_loan WHERE loan_no = \''.$s_loan_no.'\'';
		
		$r_payment = $this->db->query($s_payment);
		if($r_payment->num_rows()) {
			$a_payment = $r_payment->result_array();
			return $a_payment[0];
		} else {
			return FALSE;
		}

	}
	##### NRB EDIT END #####
	
	function getLoanRequirement($employee_id) 
	{
		$yos = $this->Member_model->getEmpYearsOfService($employee_id);

		/*
		 * @desc SQL query below returns duplicate entries on every loan
		 * Removed for Client concern Eddie Amoto 2014/01/21
		 * Replace SQL query and remove duplicates
		 */
		/*
		$sql = "SELECT COALESCE(SUM(IF(rld.capital_contribution>0, ROUND(ml.principal_balance/3,2),0)),0) AS loan_requirement
				FROM m_loan ml
				INNER JOIN r_loan_detail rld 
					ON rld.loan_code = ml.loan_code
				INNER JOIN (SELECT MAX(rldin.years_of_service) max_years_of_service
								,rldin.loan_code
							FROM m_loan mlin
							INNER JOIN r_loan_detail rldin
								ON rldin.loan_code = mlin.loan_code
							WHERE mlin.close_flag=0
							AND mlin.employee_id = '$employee_id'
							AND rldin.years_of_service <= $yos
							GROUP BY mlin.loan_no) max_yos
					ON max_yos.loan_code = rld.loan_code
				WHERE ml.close_flag=0
				AND ml.employee_id = '$employee_id'
				AND max_yos.max_years_of_service = rld.years_of_service";
		$query = $this->db->query($sql);      	
    	$result = $query->result_array(); 	
    	$loan_requirement = $result[0]['loan_requirement'];
		//log_message('debug', 'getLoanRequirement: ' .  $sql);
		*/
		
		
		$sql1 = "SELECT ml.loan_no, rld.capital_contribution, ml.principal_balance, rld.years_of_service, ROUND(ml.principal_balance/3,2) AS onethird_pb
				FROM m_loan ml
				INNER JOIN r_loan_detail rld 
					ON rld.loan_code = ml.loan_code
				INNER JOIN (SELECT MAX(rldin.years_of_service) max_years_of_service
								,rldin.loan_code
							FROM m_loan mlin
							INNER JOIN r_loan_detail rldin
								ON rldin.loan_code = mlin.loan_code
							WHERE mlin.close_flag=0
							AND mlin.employee_id = '$employee_id'
							AND rldin.years_of_service <= $yos
							GROUP BY mlin.loan_no) max_yos
					ON max_yos.loan_code = rld.loan_code
				WHERE ml.close_flag=0
				AND ml.employee_id = '$employee_id'
				AND max_yos.max_years_of_service = rld.years_of_service";
				
		$query = $this->db->query($sql1); 
		$result1 = $query->result_array(); 
		
		if(!empty($result1)){
			//Removing Duplicates
			$clean_result = array();
			foreach($result1 as $resultInd){
				if($resultInd['capital_contribution']>0) $clean_result[$resultInd['loan_no']] = $resultInd['onethird_pb'];
			}
			$loan_requirement = array_sum($clean_result);
		
			return $loan_requirement;
		}else return 0;
		
	}
}

?>