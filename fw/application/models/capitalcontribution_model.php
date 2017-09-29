<?php

class Capitalcontribution_model extends Asi_Model {
	var $table_name = 't_capital_contribution';
	var $id = 'transaction_no';
	var $model_data = null;
	var $date_columns = array('');
	
	
    function Capitalcontribution_model() {
        parent::Asi_Model();
        $this->table_name = 't_capital_contribution';
        $this->id = array('employee_id', 'accounting_period');
        $this->load->model('Member_model');
        $this->load->model('Loan_model');
		$this->load->model('Parameter_model');
		$this->load->model('tblnpmlsuspension_model');
        $this->date_columns = array('');
    }
    
    
    /**
	 * @desc Retrieves the date of suspension
	 * @param employee_id
	 */
    function getSuspensionDate($employee_id = '')
    {
    	$this->Member_model->populate(array('employee_id'=>$employee_id));
		$data = $this->Member_model->get(null ,array('suspended_date'));
		$suspensionDate = $data['list'][0]['suspended_date'];
		$temp_date = date("Ym", strtotime(date("Ymd", strtotime($suspensionDate)) . " +7 month"));
		$suspensionLiftOffDate = $temp_date."01"; 
		return $suspensionLiftOffDate;
	}
	
	/**
	 * @desc Checks if employee's suspension date is more than 6 months from current date
	 * @return 0-suspended more than 6 months ago, 1 - otherwise
	 */
	function checkSuspensionDate($employee_id){
	
		//// [START] there is not enoungh checking: Modified by ASI466 on 20110922
		if (isset($employee_id)&& $employee_id!=""){		
		//// [END] : Modified by ASI466 on 20110922
		
			$this->Member_model->populate(array('employee_id'=>$employee_id));
			
			$this->Parameter_model->populate(array('parameter_id'=>'CURRDATE'));
			$data = $this->Parameter_model->get(null, 'parameter_value');
			$currDate = $data['list'][0]['parameter_value'];
			
			//// [START] 0008331 : Modified by ASI466 on 20110922
			$currDate = date("Ymd", strtotime($currDate));
			// [END] 0008331 : Modified by ASI466 on 20110922
			
			$data = $this->Member_model->get(null ,array('suspended_date'));
	
			$suspensionDate = $data['list'][0]['suspended_date'];
			if($suspensionDate == "")
				return 0;
				
			
			//// [START] 0008331 : Modified by ASI466 on 20110922
			$temp_date = date("Ym", strtotime(date("Ymd", strtotime($suspensionDate)) . " +6 month"));
			// [END] 0008331 : Modified by ASI466 on 20110922
			
			$suspensionLiftOffDate = $temp_date."01"; 
			//echo "old impl".$suspensionLiftOffDate;
			
			
			// 20111029 #0008369 
			// [START] 7642 : Added by ASI466 on 20111104
			$result = $this->tblnpmlsuspension_model->getSixMonthsSuspensionRec($employee_id,$currDate);
			$hasSuspensionRec = 0;
			if($result["count"]!=0){
				$hasSuspensionRec = 1;
			}
			// [END] 7642 : Added by ASI466 on 20111104
			
			//20110726 fix on miniloan suspension issue; check if current date is less than suspension date
			// [START]  : Modified by ASI466 on 20111109
			if(($suspensionLiftOffDate <= $currDate or $currDate < $suspensionDate) and !$hasSuspensionRec ) return 0;
			// [END]  : Modified by ASI466 on 20111109
			else return 1;
		}
		else return 1;
	}
	
 	/**
	 * @desc Checks if the employee is a retiree.  
	 * @param employee_id
	 */
	function checkRetireeEmployee($employee_id = '')
	{
		$company_code = 'company_code';
		$data = $this->Member_model->get(array('employee_id'=>$employee_id), array(
			$company_code
		));
		return $data['list'][0][$company_code];
	}
	
	/**
	 * @desc To retrieve capital contribution balance of an employee
	 * @param employee_id
	 * @return array
	 */
	function getEmployeeLoanBalance($employee_id = ''){
		$acctgPeriod = date("Ymd", strtotime($this->parameter_model->getParam('ACCPERIOD')));
		
		$data = $this->get(array('employee_id' => $employee_id, 'accounting_period' => $acctgPeriod)
			,array('COALESCE(ending_balance,0) AS capcon_balance'));

		return $data['list'][0]['capcon_balance'];	
	}
	
	function retrieveDividendAmount($start_date, $end_date, $min_bal, $div_rate, $acctg_period){
		$year_covered = (substr($end_date, 0, 4) - substr($start_date, 0, 4))* 12;
		$month_covered = substr($end_date, 4, 2) - substr($start_date, 4, 2) + 1;
		$covered = $year_covered + $month_covered;
		
//    	$sql = "SELECT me.employee_id AS employee_id 																								
//					,(SUM(CASE WHEN tcc.minimum_balance < {$min_bal} THEN 0 ELSE tcc.minimum_balance END) / {$covered}) * {$div_rate} AS dividend_amounts																						
//				FROM t_capital_contribution tcc
//				INNER JOIN m_employee me																																																
//					ON (tcc.employee_id =  me.employee_id)																						
//				WHERE tcc.accounting_period BETWEEN '{$start_date}' AND '{$end_date}'																								
//					AND me.member_status = 'A'																								
//				GROUP BY me.employee_id HAVING dividend_amounts > 0";

		//20100607 mowkz added condition to check employee's current ending balance'
		$sql = "SELECT me.employee_id AS employee_id 																								
					,(SUM(CASE WHEN tcc.minimum_balance < {$min_bal} THEN 0 ELSE tcc.minimum_balance END) / {$covered}) * {$div_rate} AS dividend_amounts																						
				FROM t_capital_contribution tcc
				INNER JOIN t_capital_contribution tcc2
					ON tcc2.employee_id = tcc.employee_id
				INNER JOIN m_employee me																																																
					ON (tcc.employee_id =  me.employee_id)																						
				WHERE tcc.accounting_period BETWEEN '{$start_date}' AND '{$end_date}'
					AND tcc2.accounting_period = '{$acctg_period}'
					AND tcc2.ending_balance >= {$min_bal}																									
					AND me.member_status = 'A'																								
				GROUP BY me.employee_id HAVING dividend_amounts > 0";
		
    	$result = $this->db->query($sql);
//    	echo $this->db->last_query();
    	return $result->result_array();
    }
    
    function retrieveCapConBalance($employee_id, $acctg_period){
		$retval = 0;
    	/*$sql = "SELECT tcc.ending_balance + 																
					COALESCE(SUM(rt.capcon_effect * tt.transaction_amount),0) AS ending_balance															
				FROM t_capital_contribution tcc																
				LEFT OUTER JOIN t_transaction tt																
					ON tcc.employee_id = tt.employee_id AND tt.status_flag = '2'															
				INNER JOIN r_transaction rt																
					ON tt.transaction_code = rt.transaction_code AND rt.capcon_effect <> 0															
				WHERE tcc.employee_id = '{$employee_id}'															
					AND tcc.accounting_period = '{$acctg_period}'";*/
		$sql = "SELECT tcc.ending_balance AS ending_balance															
				FROM t_capital_contribution tcc																
				WHERE tcc.employee_id = '{$employee_id}'															
					AND tcc.accounting_period = '{$acctg_period}'";
					
    	$result = $this->db->query($sql);    
    	//echo $this->db->last_query();
    	if ($result->num_rows() > 0){
    		$retval_array = $result->result_array();
    		$retval = $retval_array[0]['ending_balance'];
    	}  
    	
    	return $retval;
    }
	
	function retrieveCapConBalanceWithTtransaction($employee_id, $acctg_period){
		$acctg_period2=substr($acctg_period,0,6);
		$sql = "SELECT COALESCE(tcc.ending_balance, 0) + COALESCE(SUM(tt.transaction_amount * rt.capcon_effect),0) AS ending_balance
				FROM t_transaction tt
				INNER JOIN r_transaction rt
				ON(rt.transaction_code = tt.transaction_code)
				INNER JOIN t_capital_contribution tcc
				ON(tcc.employee_id = tt.employee_id)
				WHERE tt.employee_id = '{$employee_id}'
				AND tcc.accounting_period = '{$acctg_period}'
				AND tt.status_flag = 2
				AND tt.transaction_date LIKE '{$acctg_period2}%'
				";
				
    	$result = $this->db->query($sql);    
    	
		$retval_array = $result->result_array();
		$ending_balance = $retval_array[0]['ending_balance'];
    	
		if($ending_balance < 0){
			 return 0;
		}
    	else{
			return $ending_balance;
		}
    }
	
	function retrieveCapConBalanceWithTCapcon($employee_id, $acctg_period){
		$sql = "SELECT COALESCE(tcc.ending_balance, 0) + COALESCE(SUM(tc.transaction_amount * rt.capcon_effect),0) AS ending_balance
				FROM t_capital_transaction_header tc
				INNER JOIN r_transaction rt
				ON(rt.transaction_code = tc.transaction_code)
				INNER JOIN t_capital_contribution tcc
				ON(tcc.employee_id = tc.employee_id)
				WHERE tc.employee_id = '{$employee_id}'
				AND tcc.accounting_period = '{$acctg_period}'
				AND tc.status_flag = 1";
					
    	$result = $this->db->query($sql);    
    	
		$retval_array = $result->result_array();
		$ending_balance = $retval_array[0]['ending_balance'];
    	
		if($ending_balance < 0){
			 return 0;
		}
    	else{
			return $ending_balance;
		}
    }
    
//    function deleteCapCon($acctg_period){
//    	
//    	$sql = "DELETE FROM t_capital_contribution												
//				WHERE accounting_period > '{$acctg_period}'";
//		
//    	$result = $this->db->query($sql);        	
//		$query = $this->db->last_query();    	
//    	
//    	return $this->checkError('','',$query);
//    }
    
    function batchInsert($acctg_period, $user){
    	$next_acctg_period = date('Ymd',strtotime("+1 month", strtotime($acctg_period))); 
    	$now = date("YmdHis");
    	
    	$sql = "INSERT INTO t_capital_contribution								
					( employee_id							
					, accounting_period							
					, beginning_balance							
					, minimum_balance							
					, maximum_balance							
					, ending_balance 							
					, status_flag							
					, created_by							
					, created_date							
					, modified_by							
					, modified_date".
					//[START] Added by Vincent Sy for 8th Enhancement 2013/07/31
					", capital_after_transaction )".
				    //[END] Added by Vincent Sy for 8th Enhancement 2013/07/31
					"			
					SELECT mm.employee_id							
						, '{$next_acctg_period}'			
						, COALESCE(tcc.ending_balance,0)						
						, COALESCE(tcc.ending_balance,0)						
						, COALESCE(tcc.ending_balance,0)						
						, COALESCE(tcc.ending_balance,0)						
						, '4'						
						,'{$user}'						
						,'{$now}'						
						,'{$user}'						
						,'{$now}'".
					//[START] Added by Vincent Sy for 8th Enhancement 2013/07/31
					",	COALESCE(tcc.capital_after_transaction,0)".
				    //[END] Added by Vincent Sy for 8th Enhancement 2013/07/31
					"					
					FROM m_employee mm							
					INNER JOIN t_capital_contribution tcc							
						ON mm.employee_id = tcc.employee_id 						
							AND tcc.accounting_period = '{$acctg_period}'";
							
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
    	
    	return $this->checkError('','',$query);
    }
    
    function updateCapConBalance($acctg_period, $empid, $amount, $user="peca"){
		$now = date("YmdHis");
		$curr_date = $this->Parameter_model->retrieveValue("CURRDATE");
		
		if($curr_date == $acctg_period){ 
			//First day of the month. Minimum balance is the ending balance.
			$sql = "UPDATE t_capital_contribution												
					SET minimum_balance = ending_balance + {$amount} 							
						, maximum_balance = (CASE WHEN ending_balance + {$amount} > maximum_balance 											
									THEN ending_balance + {$amount} ELSE maximum_balance END)".
						//[START] Added by Vincent Sy for 8th Enhancement 2013/07/09
					   ", capital_after_transaction = (
							CASE 
								WHEN (ending_balance + {$amount})/11 > capital_after_transaction
									THEN (ending_balance + {$amount})/11
								ELSE capital_after_transaction
							END)".
						//[END] Added by Vincent Sy for 8th Enhancement 2013/07/09
					   ", ending_balance = ending_balance + {$amount}
						, modified_by = '$user'
						, modified_date = '$now'
					WHERE employee_id = '{$empid}'												
						AND accounting_period = '{$acctg_period}'";
		}
    	else{ 
			$sql = "UPDATE t_capital_contribution												
					SET minimum_balance = (CASE WHEN ending_balance + {$amount} < minimum_balance 											
									THEN ending_balance + {$amount} ELSE minimum_balance END)								
						, maximum_balance = (CASE WHEN ending_balance + {$amount} > maximum_balance 											
									THEN ending_balance + {$amount} ELSE maximum_balance END)".
						//[START] Added by Vincent Sy for 8th Enhancement 2013/07/09
					   ", capital_after_transaction = (
							CASE 
								WHEN (ending_balance + {$amount})/11 > capital_after_transaction
									THEN (ending_balance + {$amount})/11
								ELSE capital_after_transaction
							END)".
						//[END] Added by Vincent Sy for 8th Enhancement 2013/07/09
					   ", ending_balance = ending_balance + {$amount}
						, modified_by = '$user'
						, modified_date = '$now'
					WHERE employee_id = '{$empid}'												
						AND accounting_period = '{$acctg_period}'";
		}
    	
    	$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
    	
		log_message('debug', 'lalala '.$sql);
    	return $this->checkError('','',$query);
    }
	
	
	/**[START] Added by Vincent Sy for 8th Enhancement 2013/07/09
	 * Retrieves old capital_after_transaction for the current accounting period from t_capital_contribution 
	 *
	 */
	function getOldCapitalAfterTransaction($employee_id, $accounting_period) {
		$sql = "SELECT capital_after_transaction	
				FROM t_capital_contribution	
				WHERE employee_id = '{$employee_id}'	
				AND accounting_period = '{$accounting_period}'";
				
		$result = $this->db->query($sql);
		$row = $result->row_array();
		
		return (sizeof($row)>0) ? $row['capital_after_transaction'] : 0;
	}
	//[END] Added by Vincent Sy for 8th Enhancement 2013/07/09
    
 	function retrieveBMBEmployee($cur_date, $min_bal){
		$year = substr($cur_date, 0, 4);
    	$month = substr($cur_date, 4, 2);
		
    	$sql = "SELECT tcc.employee_id												
				FROM t_capital_contribution tcc												
				INNER JOIN m_employee mm												
					ON mm.employee_id = tcc.employee_id											
				LEFT OUTER JOIN (SELECT employee_id
								FROM m_transaction tt
								WHERE tt.transaction_code IN ('BMBC','BMBE') 
									AND {$month} = MONTH(tt.transaction_date) 
									AND {$year} = YEAR(tt.transaction_date) 
									AND tt.status_flag = '3') tt 
							ON tcc.employee_id = tt.employee_id										
				LEFT OUTER JOIN (SELECT employee_id,												
							SUM(capital_contribution_balance) AS requirement									
						FROM m_loan										
						WHERE loan_date <= '{$cur_date}'										
						GROUP BY employee_id) tl										
					ON tcc.employee_id = tl.employee_id											
				WHERE tcc.accounting_period LIKE '{$year}{$month}%'
					AND mm.member_status = 'A'											
					AND tcc.ending_balance > 0											
					AND tt.employee_id IS NULL											
					AND (tcc.ending_balance < {$min_bal} OR tcc.ending_balance < COALESCE(tl.requirement,0))											
					#AND tcc.status_flag = '4'											
				    	";
		
    	$result = $this->db->query($sql);
    	return $result->result_array();
    }
    
    /*
     *@desc Used in Report_transactioncontroltotals 
     * 
     * */
    function retrieveCapConForTCC($filter = null, $limit = null, $offset = null, $select = null, $orderby = null)
    {
    	if($select)
    		$this->db->select($select);
    	$this->db->from('t_capital_contribution mc');
    	$this->db->join('m_employee me', 'me.employee_id=mc.employee_id', 'inner');
    	if($filter)	
    		$this->db->where($filter);
    	$this->db->group_by('me.company_code');	
    	if($orderby)	
    		$this->db->orderby($orderby);	
    	$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
    	
		$count = $this->db->count_all($this->table_name);
    	return $this->checkError($result->result_array(), $count, $query);
    }
    
	function retrieveCapConForTCE($transaction_period)
    {
    	$sql = "SELECT SUM(ending_balance) as beginning_balance
				,employee_id 
				FROM t_capital_contribution 
				WHERE ending_balance>0 
				AND accounting_period='$transaction_period' 
				AND status_flag='1'
				GROUP BY employee_id 
				ORDER BY employee_id";
		
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);	
    }
    
	function retrieveCapConForTCT($filter = null, $limit = null, $offset = null, $select = null, $orderby = null)
    {
    	if($select)
    		$this->db->select($select);
    	$this->db->from('t_capital_contribution');
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
	
 	/* function retrieveMembersForCompany($filter = null, $select = null, $start_date, $end_date)
    {
		$this->db->distinct('tcc.employee_id');
    	if($select)
    		$this->db->select($select);
		$this->db->from('m_employee tcc');	
    	$this->db->from('t_capital_contribution tcc');
		$this->db->join('m_employee mm', 'tcc.employee_id = mm.employee_id', 'LEFT OUTER');
		
    	if($filter)	
    		$this->db->where($filter);
		$this->db->where("tcc.accounting_period BETWEEN '$start_date' AND '$end_date'");
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
    	
		$count = $this->db->count_all($this->table_name);
    	return $this->checkError($result->result_array(), $count, $query);
    }  */  
	
	function retrieveMembersForCompany($company_code)
    {
		$sql = "SELECT `employee_id`
					, company_code,last_name
					,first_name,middle_name
					,FLOOR(DATEDIFF(CURDATE(), hire_date)/365.25) AS member_date
				FROM (`m_employee`) 
				WHERE `company_code` = '$company_code' 
				AND `member_status` = 'A'";
				
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);		
    }
	
	/* function retrieveMembersByTransactionDate($filter = null, $select = null, $start_date, $end_date, $start_trans_date, $end_trans_date, $groupby=null)
    {
    	$this->db->distinct('tcc.employee_id');
		if($select)
    		$this->db->select($select);
			
    	$this->db->from('t_capital_contribution tcc');
		$this->db->join('m_transaction tt', 'tcc.employee_id = tt.employee_id', 'LEFT OUTER');
		
    	if($filter)	
    		$this->db->where($filter);
		$this->db->where("tcc.accounting_period BETWEEN '$start_date' AND '$end_date'");
    	$this->db->where("tt.transaction_date BETWEEN '$start_trans_date' AND '$end_trans_date'");
		if($groupby)
			$this->db->group_by($groupby);
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
		$count =$this->db->count_all_results();
    	return $this->checkError($result->result_array(), $count, $query);
    } */  

	function retrieveMembersByTransactionDate($start_date, $end_date, $start_trans_date, $end_trans_date)
    {
    	$sql = "SELECT DISTINCT `tcc`.`employee_id`
					,me.first_name
					,me.last_name
					,me.middle_name
					,me.company_code
					,FLOOR(DATEDIFF(CURDATE(), me.hire_date)/365.25) AS member_date 
				FROM (`t_capital_contribution` tcc) 
				LEFT OUTER JOIN `m_transaction` tt 
					ON `tcc`.`employee_id` = `tt`.`employee_id` 
				INNER JOIN m_employee me
					ON me.employee_id=tcc.employee_id
				WHERE `tt`.`status_flag` = '3' 
					AND `tcc`.`accounting_period` BETWEEN '$start_date' AND '$end_date' 
					AND `tt`.`transaction_date` BETWEEN '$start_trans_date' AND '$end_trans_date' 
				GROUP BY `tcc`.`employee_id`";
				
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
    }	
	
    function retrieveOutstandingBalanceCapCon($filter=null, $select=null, $groupby=null, $orderby=null) {
		if($select)
    		$this->db->select($select);
		$this->db->from('t_capital_contribution tc');
		$this->db->join('t_transaction tt', "tc.employee_id = tt.employee_id AND tt.status_flag = '2' AND MONTH(tc.accounting_period) = MONTH(tt.transaction_date) AND YEAR(tc.accounting_period) = YEAR(tt.transaction_date)", 'LEFT OUTER');
		$this->db->join('m_employee mm', 'mm.employee_id = tc.employee_id', 'INNER');
		$this->db->join('r_transaction rt', 'tt.transaction_code = rt.transaction_code', 'LEFT OUTER');
		$this->db->join('r_company rc', 'rc.company_code = mm.company_code', 'INNER');
		if($filter)	
    		$this->db->where($filter);
		if($groupby)
			$this->db->group_by($groupby);
		if($orderby)	
    		$this->db->orderby($orderby);
		$result = $this->db->get(); 
    	$query = $this->db->last_query();
		
		$count =$this->db->count_all_results();
    	return $this->checkError($result->result_array(), $count, $query);
	}
	
	function retrieveOutstandingBalanceCapConByEmp($input_date) 
	{
		$accounting_period = substr($input_date, 0, 6)."01";
		$year = substr($accounting_period,0,-4);
		$month = substr($accounting_period,4,-2);
		/* $sql = "SELECT SUM(tcc.ending_balance) + COALESCE(SUM(tt.transaction_amount*rt.capcon_effect),0) AS amount
				,me.company_code
				,me.last_name
				,me.first_name
				,me.middle_name
				,tcc.employee_id
				,'CapCon' AS transaction_code
				FROM t_capital_contribution tcc		
				INNER JOIN m_employee me
				ON (me.employee_id=tcc.employee_id)
				LEFT OUTER JOIN t_transaction tt 
				ON tcc.employee_id = tt.employee_id AND tt.status_flag = '2' 
					AND MONTH(tcc.accounting_period) = MONTH(tt.transaction_date) 
					AND YEAR(tcc.accounting_period) = YEAR(tt.transaction_date) 
				LEFT OUTER JOIN r_transaction rt 
				ON tt.transaction_code = rt.transaction_code																											
				WHERE tcc.accounting_period = '$accounting_period'
				-- AND tcc.status_flag='1'
				AND tcc.ending_balance>0
				GROUP BY tcc.employee_id
				ORDER BY me.company_code, tcc.employee_id"; */
				
		/*$sql = "SELECT tcc.ending_balance AS amount
					,me.company_code
					,me.last_name
					,me.first_name
					,me.middle_name
					,tcc.employee_id
					,'CapCon' AS transaction_code
				FROM t_capital_contribution tcc		
				INNER JOIN m_employee me
					ON (me.employee_id=tcc.employee_id)	
				INNER JOIN r_company rc
					ON (rc.company_code=me.company_code)
				WHERE tcc.accounting_period = '$accounting_period'
					AND tcc.ending_balance>0
					AND me.member_status = 'A'
				GROUP BY tcc.employee_id
				ORDER BY me.company_code, tcc.employee_id";*/

		//sql changed by joseph 2/18/2011, needs to retrieve balance for specific day and not ending balance only
		$sql = "SELECT COALESCE(tcc.beginning_balance + COALESCE(t.amount,0),0) AS amount
			,me.company_code
			,me.last_name
			,me.first_name
			,me.middle_name
			,me.employee_id
			,'CapCon' AS transaction_code
		FROM t_capital_contribution tcc
		INNER JOIN m_employee me 
			ON(me.employee_id = tcc.employee_id)
		LEFT OUTER JOIN (
			SELECT SUM(rt.capcon_effect * mt.transaction_amount) AS amount, mt.employee_id 
			FROM m_transaction mt
			INNER JOIN r_transaction rt
				ON (rt.transaction_code = mt.transaction_code)
			WHERE mt.transaction_date <= '{$input_date}'
			   AND MONTH(mt.transaction_date) = '{$month}'
			   AND YEAR(mt.transaction_date) = '{$year}'
			GROUP BY mt.employee_id
		)t ON (t.employee_id = me.employee_id)
		
		WHERE tcc.accounting_period = '{$accounting_period}'
			AND me.member_status = 'A'
		HAVING amount > 0
		ORDER BY me.company_code, me.employee_id";
				
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		log_message('debug', 'xx-xx '.$query);
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
	}
	
	function retrieveOutstandingBalanceCapConByComp($input_date) 
	{
		$accounting_period = substr($input_date, 0, 6)."01";
		$year = substr($accounting_period,0,-4);
		$month = substr($accounting_period,4,-2);
		/* $sql = "SELECT SUM(tcc.ending_balance) + COALESCE(SUM(tt.transaction_amount*rt.capcon_effect),0) AS amount
				,me.company_code
				,rc.company_name
				,'CapCon' AS transaction_code
				FROM t_capital_contribution tcc		
				INNER JOIN m_employee me
				ON (me.employee_id=tcc.employee_id)	
				INNER JOIN r_company rc
				ON (rc.company_code=me.company_code)
				LEFT OUTER JOIN t_transaction tt 
				ON tcc.employee_id = tt.employee_id AND tt.status_flag = '2' 
					AND MONTH(tcc.accounting_period) = MONTH(tt.transaction_date) 
					AND YEAR(tcc.accounting_period) = YEAR(tt.transaction_date) 
				LEFT OUTER JOIN r_transaction rt 
				ON tt.transaction_code = rt.transaction_code	
				WHERE tcc.accounting_period = '$accounting_period'
				-- AND tcc.status_flag='1'
				AND tcc.ending_balance>0
				GROUP BY me.company_code
				ORDER BY me.company_code, tcc.employee_id"; */
		/*$sql = "SELECT SUM(tcc.ending_balance) AS amount
					,me.company_code
					,rc.company_name
					,'CapCon' AS transaction_code
				FROM t_capital_contribution tcc		
				INNER JOIN m_employee me
					ON (me.employee_id=tcc.employee_id)	
				INNER JOIN r_company rc
					ON (rc.company_code=me.company_code)
				WHERE tcc.accounting_period = '$accounting_period'
					AND tcc.ending_balance>0
					AND me.member_status = 'A'
				GROUP BY me.company_code
				ORDER BY me.company_code, tcc.employee_id";*/
				
		//sql changed by joseph 2/18/2011, needs to retrieve balance for specific day and not ending balance only	
		$sql = "SELECT SUM(t.amount) AS amount
					  ,t.company_code AS company_code
					  ,t.company_name AS company_name
					  ,'CapCon' AS transaction_code
				FROM
					(SELECT COALESCE(tcc.beginning_balance + COALESCE(m.amount,0),0) AS amount
						,me.company_code
						,rc.company_name
						,'CapCon' AS transaction_code
					FROM t_capital_contribution tcc
					INNER JOIN m_employee me 
						ON(me.employee_id = tcc.employee_id)
					INNER JOIN r_company rc
						ON(rc.company_code = me.company_code)
					LEFT OUTER JOIN (
						SELECT SUM(rt.capcon_effect * mt.transaction_amount) AS amount, mt.employee_id 
						FROM m_transaction mt
						INNER JOIN r_transaction rt
							ON (rt.transaction_code = mt.transaction_code)
						WHERE mt.transaction_date <= '{$input_date}'
						   AND MONTH(mt.transaction_date) = '{$month}'
						   AND YEAR(mt.transaction_date) = '{$year}'
						GROUP BY mt.employee_id
					)m
						ON (m.employee_id = me.employee_id)
					WHERE tcc.accounting_period = '{$accounting_period}'
						AND me.member_status = 'A'
				)t
				GROUP BY t.company_code
				HAVING amount > 0";
		
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		$count = $result->num_rows();
		log_message('debug', 'xx-xx '.$query);
		return $this->checkError($result->result_array(), $count, $query);
	}
	
	function getDividendInfo($filter=null, $select=null){
		if($select)
			$this->db->select($select);
		$this->db->from('t_dividend');
		if($filter)	
    		$this->db->where($filter);
		$result = $this->db->get(); 
    	$query = $this->db->last_query();
		
		$count =$this->db->count_all_results();
    	return $this->checkError($result->result_array(), $count, $query);	
	
	}
	
	function getDeclaration($filter=null, $select=null, $groupby=null, $orderby=null, $start_date, $end_date){
		if($select)
			$this->db->select($select);
		$this->db->from('t_capital_contribution tcc');
		$this->db->join('m_employee mm', 'mm.employee_id = tcc.employee_id', 'INNER');
		$this->db->join('t_transaction tt', 'mm.employee_id = tt.employee_id', 'LEFT OUTER');
		
		if($filter)	
    		$this->db->where($filter);
		$this->db->where("tcc.accounting_period BETWEEN '$start_date' AND '$end_date'");	
		if($groupby)
			$this->db->group_by($groupby);
		if($orderby)	
    		$this->db->orderby($orderby);
		$result = $this->db->get(); 
    	$query = $this->db->last_query();
		
		$count =$this->db->count_all_results();
    	return $this->checkError($result->result_array(), $count, $query);	
	}
	
	function getBeginningBalanceBetween($filter=null, $limit = null, $offset = null, $select=null, $orderby=null, $start_date, $end_date, $emp_id){
		if($select)
			$this->db->select($select);
		$this->db->from('t_capital_contribution tcc');
		if ($filter)
			$this->db->where($filter);
		$sql = "accounting_period <= (
					SELECT MIN(transaction_date)				
					FROM m_transaction
					WHERE employee_id='$emp_id'
					AND transaction_date BETWEEN '$start_date' AND '$end_date'
					AND status_flag = '3')";
		$this->db->where($sql);
		if($orderby)
		$this->db->order_by($orderby);
		
		$this->db->limit($offset,$limit);

		$result = $this->db->get(); 
    	$query = $this->db->last_query();
		
		$count =$this->db->count_all_results();
    	return $this->checkError($result->result_array(), $count, $query);	
	}
	
	function retrieveCapcon($employee_id,$first_day)
	{
		$sql = "SELECT SUM(ending_balance) AS beginning_balance
				FROM t_capital_contribution
				WHERE employee_id = '$employee_id'
					AND accounting_period='$first_day'";
				
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);				
	}
	
	//[START] google doc #15 : modified by ASI466 on 20120201
	function retrieveBalBeforeStart($employee_id, $start_date){
		$acct_period = substr($start_date, 0, 6)."01";
		
		$sql = "SELECT COALESCE(SUM(transaction_amount*rt.capcon_effect),0) AS total_amount 
				FROM m_transaction tt
				INNER JOIN r_transaction rt
					ON tt.transaction_code = rt.transaction_code
				WHERE tt.employee_id = '$employee_id'
				AND tt.transaction_date >= '$acct_period' AND tt.transaction_date < '$start_date'
				AND rt.capcon_effect  <> 0";
				
		$result = $this->db->query($sql);
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		$result_arr = $this->checkError($result->result_array(), $count, $query);
		return $result_arr['list'][0]['total_amount'];
	}
	
	
	//[START] google doc #15 : Added by ASI466 on 20120130
	function getMinTransactionDate( $start_date, $end_date, $emp_id){
		$sql = "SELECT MIN(transaction_date)  as start_date
				 FROM m_transaction
				 WHERE employee_id= '$emp_id'
				 AND transaction_date BETWEEN '$start_date' AND '$end_date'
				 AND status_flag = '3'";
				 
		$result = $this->db->query($sql);
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		$result_arr = $this->checkError($result->result_array(), $count, $query);
		return $result_arr['list'][0]['start_date'];
	}
	//[END] google doc #15 : Added by ASI466 on 20120130
}

?>