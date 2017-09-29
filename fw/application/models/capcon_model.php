<?php

class Capcon_model extends Asi_Model {
	var $table_name = 't_capital_contribution';
	var $id = array('employee_id', 'accounting_period');
	var $model_data = null;
	var $date_columns = array('accounting_period');
	
	
    function Capcon_model()
    {
        parent::Asi_Model();
    }
  
	/**
     * @desc To retrieve capital contribution every month from $start_date to $end_date
     * @param $filter
     * @param $select
	 * @param $orderby
     */
	function retrieveCapcon($filter = null, $select = null, $orderby = null, $divisor=1, $ccminbal = 5000){
		$sqlquery = "SELECT m_employee.company_code AS company_code
						, rc.company_name AS company_name
						, m_employee.employee_id AS employee_id
						, m_employee.last_name
						, m_employee.first_name
						, SUBSTR(m_employee.middle_name,1,1) AS middle_name
						, t_capital_contribution.accounting_period
						, CASE WHEN t_capital_contribution.minimum_balance >= $ccminbal THEN t_capital_contribution.minimum_balance ELSE 0 END AS minimum_balance
					FROM m_employee 
					INNER JOIN t_capital_contribution ON m_employee.employee_id = t_capital_contribution.employee_id  
					INNER JOIN r_company rc ON rc.company_code = m_employee.company_code
					WHERE m_employee.member_status='A' $filter

					UNION  

					SELECT m_employee.company_code AS company_code
						, rc.company_name AS company_name
						, m_employee.employee_id AS employee_id
						, m_employee.last_name
						, m_employee.first_name
						, SUBSTR(m_employee.middle_name,1,1) AS middle_name
						, '99991231' AS accounting_period 
						, CC.AvgMinBal AS AvgBal  
					FROM m_employee 
					INNER JOIN t_capital_contribution ON m_employee.employee_id = t_capital_contribution.employee_id 
					INNER JOIN r_company rc ON rc.company_code = m_employee.company_code
					INNER JOIN (
						SELECT t_capital_contribution.employee_id
							, ROUND(SUM(CASE WHEN t_capital_contribution.minimum_balance >= $ccminbal THEN t_capital_contribution.minimum_balance ELSE 0 END)/$divisor,2) AS AvgMinBal 
						FROM t_capital_contribution 
						WHERE t_capital_contribution.employee_id IN (
							SELECT employee_id FROM m_employee WHERE member_status='A'
						) $filter
						GROUP BY t_capital_contribution.employee_id
					) CC  ON m_employee.employee_id=CC.employee_id  
					WHERE m_employee.member_status='A' $filter ";
		//echo $sqlquery;
		
		if($select)
			$this->db->select($select);
		$this->db->from('m_employee');
		$this->db->join('( '.$sqlquery.' ) L', 'L.employee_id = m_employee.employee_id', 'INNER');
		if($orderby)
			$this->db->order_by($orderby);
		$result = $this->db->get();
			
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
			
	}
	
	/**
     * @desc To retrieve employees whose capital contribution exceeds the maximum balance for the given period.
     * @param $filter
     * @param $select
	 * @param $orderby
	 * @param $distinct
     */
	function retrieveMaximumBalanceCapCon($filter = null, $select = null, $orderby = null, $distinct = null){
		if($distinct)
			$this->db->distinct($distinct);
		if($select)
			$this->db->select($select);
    	$this->db->from('t_capital_contribution tcc');
    	$this->db->join('m_employee mm', 'tcc.employee_id = mm.employee_id', 'INNER');
		$this->db->join('r_company rc', 'rc.company_code = mm.company_code', 'INNER');
		if ($filter)
			$this->db->where($filter);
		if($orderby)
			$this->db->order_by($orderby);
		$result = $this->db->get();
			
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
			
	}
	
	/**
     * @desc To retrieve employees whose capital contribution exceeds the maximum balance for the given period.
     * @param $filter
     * @param $select
	 * @param $orderby
	 */
	function retrievePayrollDeduction($filter = null, $select = null, $orderby = null){
		if($select)
			$this->db->select($select);
    	$this->db->from('t_payroll_deduction');
    	if ($filter)
			$this->db->where($filter);
		$this->db->group_by('employee_id');	
		if($orderby)
			$this->db->order_by($orderby);
	
		$result = $this->db->get();
			
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
			
	}
	
	/**
     * @desc To retrieve employees whose capital contribution exceeds the maximum balance for the given period.
     * @param $filter
     * @param $select
	 * @param $orderby
	 */
	function retrieveCapConBalance($filter = null, $select = null, $orderby = null){
		if($select)
			$this->db->select($select);
    	$this->db->from('t_capital_contribution tcc');
    	if ($filter)
			$this->db->where($filter);
		$this->db->join('m_employee mm', 'tcc.employee_id = mm.employee_id', 'INNER');
		if($orderby)
			$this->db->order_by($orderby);
		$result = $this->db->get();
			
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
	}
	
	/**
     * @desc To retrieve list of employees who did not have transactions for the given range having the amount range specified.
     * @param $filter
     * @param $select
	 * @param $orderby
	 */
	function retrieveDormantAccountsCapCon($filter = null, $select = null, $orderby = null, $start_date, $end_date){
		if($select)
			$this->db->select($select);
    	$this->db->from('t_capital_contribution tcc');
    	if ($filter)
			$this->db->where($filter);
		$this->db->join('m_employee mm', 'tcc.employee_id = mm.employee_id', 'INNER');
		$this->db->join("(SELECT employee_id					
							FROM m_transaction		
							WHERE transaction_date BETWEEN '".$start_date."' AND '".$end_date."'
								AND transaction_code NOT LIKE 'DIV%'
							) AS tt"
			, 'mm.employee_id = tt.employee_id', 'LEFT OUTER');
		if($orderby)
			$this->db->order_by($orderby);
		$result = $this->db->get();
			
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
			
	}
}
?>