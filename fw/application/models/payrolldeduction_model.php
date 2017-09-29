<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

 class Payrolldeduction_model extends Asi_Model {

    function Payrolldeduction_model()
    {
        parent::Asi_Model();
        $this->table_name = 't_payroll_deduction';
        $this->id = array('employee_id', 'trasaction_code', 'start_date');
        $this->date_columns = array('');
    }

    function retrievePayrollDeductionSavings($current_date){
    	$sql = "SELECT tpd.employee_id
				, tpd.transaction_code
				, tpd.amount
			FROM t_payroll_deduction tpd
			INNER JOIN m_employee mm
				ON (mm.employee_id = tpd.employee_id)
			INNER JOIN r_company rc
				ON (rc.company_code = mm.company_code)
			WHERE tpd.start_date <= '{$current_date}'
				AND tpd.end_date >= '{$current_date}'
				AND mm.member_status = 'A'
				AND rc.company_name LIKE '%P&G%'
				AND tpd.amount > 0
				AND tpd.status_flag = '1'";

    	$query = $this->db->query($sql);

    	//echo $this->db->last_query();

    	return $query->result_array();
    }

    function retrievePayrollDeductionLoans($acctg_period){
    	$retval_array = array();    	
		//[START] 7th Enhancement
		/*$sql = "SELECT tl.loan_no
				, tl.employee_id
				, tl.employee_principal_amort
				, tl.employee_interest_amortization
				, tl.principal_balance
				, ip.parameter_value AS payment_code
			FROM m_loan tl
			INNER JOIN r_loan_header rl
				ON (rl.loan_code = tl.loan_code)
			INNER JOIN m_employee mm
				ON (mm.employee_id = tl.employee_id)
			INNER JOIN r_company rc
				ON (rc.company_code = mm.company_code)
			INNER JOIN i_parameter_list ip
				ON (ip.parameter_id = CONCAT(tl.loan_code, 'PD'))
			WHERE rl.payroll_deduction = 'Y'
				AND tl.close_flag = '0'
				#AND tl.principal_balance > 0
				AND tl.amortization_startdate <= '{$acctg_period}'
				AND rc.company_name LIKE '%P&G%'
				AND mm.member_status = 'A'";*/
		$sql = "SELECT tl.loan_no
				, tl.employee_id
				, tl.employee_principal_amort
				, tl.employee_interest_amortization
				, tl.principal_balance
				, tl.interest_rate
				, ip.parameter_value AS payment_code
				, rl.bsp_computation
			FROM m_loan tl
			INNER JOIN r_loan_header rl
				ON (rl.loan_code = tl.loan_code)
			INNER JOIN m_employee mm
				ON (mm.employee_id = tl.employee_id)
			INNER JOIN r_company rc
				ON (rc.company_code = mm.company_code)
			INNER JOIN i_parameter_list ip
				ON (ip.parameter_id = CONCAT(tl.loan_code, 'PD'))
			WHERE rl.payroll_deduction = 'Y'
				AND tl.close_flag = '0'
				#AND tl.principal_balance > 0
				AND tl.amortization_startdate <= '{$acctg_period}'
				AND rc.company_name LIKE '%P&G%'
				AND mm.member_status = 'A'";
		//[END] 7th Enhancement

    	$result = $this->db->query($sql);
		//echo $this->db->last_query();
    	if ($result->num_rows() > 0){
    		$retval_array = $result->result_array();
    	}
    	//echo $this->db->last_query();
    	return $retval_array;
    }

	function getPayrollDeductionList($filter = null, $limit = null, $offset = null, $select = null, $orderby = null)
	{
		if($select)
    		$this->db->select($select);
    	$this->db->from('t_payroll_deduction AS pd');
    	$this->db->join('m_employee AS me ', 'pd.employee_id = me.employee_id', 'left outer');
    	if($filter)
    		$this->db->where($filter);

		$clone_db = clone $this->db;
		$count = $clone_db->count_all_results();

    	if($orderby)
    		$this->db->order_by($orderby);
    	$this->db->limit($offset, $limit);

    	$result = $this->db->get();
    	$query = $this->db->last_query();

    	//$count = $this->db->count_all($this->table_name);

    	/*if($filter){
			$this->db->where($filter);
			$this->db->from($this->table_name);
			$count = $this->db->count_all_results();
		}else{
			$count = $this->db->count_all($this->table_name);
		}*/

		return $this->checkError($result->result_array(), $count, $query);
    }

 	function getPayrollDeduction($filter = null, $limit = null, $offset = null, $select = null, $orderby = null)
	{
		if($select)
    		$this->db->select($select);
    	$this->db->from('t_payroll_deduction AS pd');
    	$this->db->join('m_employee AS me ', 'pd.employee_id = me.employee_id', 'left outer');
    	$this->db->join('r_transaction rt', 'rt.transaction_code=pd.transaction_code', 'left outer');
    	if($filter)
    		$this->db->where($filter);
    	if($orderby)
    		$this->db->order_by($orderby);
    	$this->db->limit($offset, $limit);

    	$result = $this->db->get();
    	$query = $this->db->last_query();

    	$count = $this->db->count_all($this->table_name);

    	/*if($filter){
			$this->db->where($filter);
			$this->db->from($this->table_name);
			$count = $this->db->count_all_results();
		}else{
			$count = $this->db->count_all($this->table_name);
		}*/

		return $this->checkError($result->result_array(), $count, $query);
    }

	function getPDEmployeeList($filter = null, $limit = null, $offset = null, $select = null, $orderby = null)
	{
		if($select)
    		$this->db->select($select);
    	$this->db->from('m_employee AS me');
    	$this->db->join('t_payroll_deduction pd', 'me.employee_id = pd.employee_id', 'left outer');
    	if($filter)
    		$this->db->where($filter);
    	if($orderby)
    		$this->db->order_by($orderby);
    	$this->db->limit($offset, $limit);

    	$result = $this->db->get();
    	$query = $this->db->last_query();

    	$count = $this->db->count_all($this->table_name);

    	/*if($filter){
			$this->db->where($filter);
			$this->db->from($this->table_name);
			$count = $this->db->count_all_results();
		}else{
			$count = $this->db->count_all($this->table_name);
		}*/

		return $this->checkError($result->result_array(), $count, $query);
    }

	function getPDEntries($filter = null, $limit = null, $offset = null, $select = null, $groupby = null)
	{
		if($select)
    		$this->db->select($select);
    	$this->db->from('t_payroll_deduction');
    	if($filter)
    		$this->db->where($filter);
    	if($groupby)
    		$this->db->group_by($groupby);
    	$this->db->limit($offset, $limit);

    	$result = $this->db->get();
    	$query = $this->db->last_query();
    	$count = $this->db->count_all($this->table_name);

    	/*if($filter){
			$this->db->where($filter);
			$this->db->from($this->table_name);
			$count = $this->db->count_all_results();
		}else{
			$count = $this->db->count_all($this->table_name);
		}*/

		return $this->checkError($result->result_array(), $count, $query);
    }
	
	function getAPDProoflistEmp()
	{
		$sql = "SELECT tpd.employee_id
					#, CONCAT(me.last_name, ' ', me.first_name, ' ', me.middle_name) AS employee_name
					, me.first_name
					, me.last_name
					, me.middle_name
					, rt.transaction_description AS transaction_type
					, tpd.start_date
					, tpd.end_date
					, tpd.amount
					#, tpd.transaction_code
				FROM t_payroll_deduction tpd
				INNER JOIN m_employee me
					ON me.employee_id = tpd.employee_id
				INNER JOIN r_transaction rt
					ON rt.transaction_code = tpd.transaction_code
				WHERE tpd.transaction_type = 'A'
					AND tpd.status_flag = '1'";
					
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);					
					
	}
	
	function getAPDProoflistEmpCount()
	{
		$sql = "SELECT COUNT(DISTINCT(employee_id)) AS pd_count
					, SUM(amount) AS pd_total
				FROM t_payroll_deduction
				WHERE transaction_type='A'
					AND status_flag='1'";
					
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);					
					
	}
	
	function getDPDProoflistEmp($transaction_period)
	{
		$sql = "SELECT tpd.employee_id
					#, CONCAT(me.last_name, ' ', me.first_name, ' ', me.middle_name) AS employee_name
					, me.first_name
					, me.last_name
					, me.middle_name
					, rt.transaction_description AS transaction_type
					, tpd.start_date
					, tpd.end_date
					, tpd.amount
					#, tpd.transaction_code
				FROM t_payroll_deduction tpd
				INNER JOIN m_employee me
					ON me.employee_id = tpd.employee_id
				INNER JOIN r_transaction rt
					ON rt.transaction_code = tpd.transaction_code
				WHERE tpd.transaction_type = 'D'
					AND tpd.transaction_period = '$transaction_period'
					AND tpd.status_flag = '1'";
					
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);	
	}
	
	function getDPDProoflistEmpCount($transaction_period)
	{
		$sql = "SELECT COUNT(DISTINCT(employee_id)) AS pd_count
					, SUM(amount) AS pd_total
				FROM t_payroll_deduction
				WHERE transaction_period = '$transaction_period'
					AND transaction_type='D'
					AND status_flag='1'";
					
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);	
	}
	
	function getDistinctCompany()
	{
		$sql = "SELECT DISTINCT rc.company_code
				FROM r_company rc
				INNER JOIN m_employee me
					ON me.company_code = rc.company_code
				INNER JOIN t_transaction tt
					ON tt.employee_id = me.employee_id
				INNER JOIN r_transaction rt
					ON rt.transaction_code = tt.transaction_code
				WHERE rt.transaction_group = 'PD'
				ORDER BY company_code";
					
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);	
	}
	
	function retrieveConflict($start, $end, $employee, $tran_code, $edit_mode = false){
		$sql = "";		
		if ($edit_mode){
			$sql = "SELECT start_date
					, end_date
					, amount
				FROM t_payroll_deduction 
				WHERE ((start_date <= '$start' AND end_date >= '$start')
						OR (start_date <= '$end' AND end_date >= '$end'))
					AND employee_id = '$employee'
					AND transaction_code = '$tran_code'
					AND start_date <> '$start'
					AND status_flag = '1'";
		} else{
			$sql = "SELECT start_date
					, end_date
					, amount
				FROM t_payroll_deduction 
				WHERE ((start_date <= '$start' AND end_date >= '$start')
						OR (start_date <= '$end' AND end_date >= '$end'))
					AND employee_id = '$employee'
					AND transaction_code = '$tran_code'
					AND status_flag = '1'";
		}
				
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);	
	}
	

	function deleteConflict($start, $end, $employee, $transaction_code){
		$sql = "DELETE
				FROM t_payroll_deduction 
				WHERE ((start_date <= '$start' AND end_date >= '$start')
						OR (start_date <= '$end' AND end_date >= '$end'))
					AND transaction_code = '$transaction_code'
					AND employee_id = '$employee'
					AND status_flag = '1'";
					
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		
		return $this->checkError('','',$query);
	}
	

	function insertPD($new_start_date, $new_end_date, $new_amount, $delimited, $employee_id, $transaction_code, $transaction_period, $created_by){
		$currdate = date("YmdHis");
		$sql = "INSERT
				INTO t_payroll_deduction 
			    (start_date, end_date, amount, transaction_type, employee_id, transaction_code, transaction_period, created_by, created_date, modified_by, modified_date) 
			    VALUES ('$new_start_date', '$new_end_date', '$new_amount', '$delimited', '$employee_id', '$transaction_code', '$transaction_period', '$created_by', '$currdate', '$created_by', '$currdate')";
					
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		
		return $this->checkError('','',$query);
	}
	
		
	function getAPDProoflistEmpNewPD($start_date)
	{
		$sql = "SELECT tpd.employee_id
					#, CONCAT(me.last_name, ' ', me.first_name, ' ', me.middle_name) AS employee_name
					, me.first_name
					, me.last_name
					, me.middle_name
					, rt.transaction_description AS transaction_type
					, tpd.start_date
					, tpd.end_date
					, tpd.amount
					#, tpd.transaction_code
				FROM t_payroll_deduction tpd
				INNER JOIN m_employee me
					ON me.employee_id = tpd.employee_id
				INNER JOIN r_transaction rt
					ON rt.transaction_code = tpd.transaction_code
				WHERE tpd.start_date like '".$start_date."%'
					AND tpd.status_flag = '1'
					AND tpd.new_pd = '1'
					";
					
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);					
					
	}
	
	function getAPDProoflistEmpCountNewPD($start_date)
	{
		$sql = "SELECT COUNT(DISTINCT(employee_id)) AS pd_count
					, SUM(amount) AS pd_total
				FROM t_payroll_deduction tpd
				WHERE start_date like '".$start_date."%'
					AND status_flag = '1'
					AND new_pd = '1'
					";
					
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);					
					
	}
	
	function updateNewPDField($employee_id, $transaction_code,$resultStartDate){
	
		$sql = "Update t_payroll_deduction tpd
				Set new_pd = '1'
				WHERE start_date = '".$resultStartDate."'
					AND transaction_code = '".$transaction_code."'
					AND employee_id = '".$employee_id."'
					";
					
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		
		return $this->checkError('','',$query);
	}
	
	
	function updateNewPDFlag($employee_id, $start_date, $transaction_code){
		$currdate = date("YmdHis");
		$sql = "UPDATE t_payroll_deduction SET new_pd = 1 
				WHERE employee_id = '{$employee_id}'
					AND transaction_code = '{$transaction_code}'
					AND start_date = '{$start_date}'
				";
					
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		
		return $this->checkError('','',$query);
	}
	
}

?>
