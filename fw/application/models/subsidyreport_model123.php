<?php

class Subsidyreport_model2 extends Asi_Model {
	var $table_name = 'm_loan';
	var $id = 'loan_no';
	var $model_data = null;
	var $date_columns = array('payment_date');
	
	
    function Subsidyreport_model2()
    {
        parent::Asi_Model();
    }
  
    /**
     * @desc Retrieve all loans before accounting period for subsidy report
     * @param $acctgPeriod
     * @param $filter
     * @param $select
	 * @param $acctgPeriodBSP - added for 8th Enhancement
     */
	function getLoansBeforeAcctgPeriod($acctgPeriod, $filter = null, $select = null, $year=null, $acctgPeriodBSP){
		$this->db->select('m_loan_payment.loan_no, balance');
		$this->db->from('m_loan_payment');
			$innerSelect = '(SELECT m_loan_payment.loan_no, MAX(modified_date) AS LastPay 
							FROM m_loan_payment 
							WHERE payment_date<=\''.$acctgPeriod.'\' 
							AND status_flag = \'2\'
							GROUP BY loan_no) LP1';
							
			$innerSelect2 = '(SELECT mlp.loan_no,MIN(balance) AS balance
					FROM m_loan_payment mlp
					INNER JOIN m_loan ml
						ON ml.loan_no = mlp.loan_no
					LEFT OUTER JOIN t_transaction tt 
						ON tt.reference = CONCAT(mlp.loan_no, ",", mlp.transaction_code, ",", mlp.payment_date, ",", mlp.payor_id)
					WHERE ml.close_flag = 0
						AND tt.reference IS NULL
						AND YEAR(mlp.payment_date) <= "'.$year.'"
						AND MONTH(mlp.payment_date) = MONTH(ml.loan_date)
						AND mlp.payment_date < \''.$acctgPeriod.'\' 
						AND mlp.status_flag = \'2\'
					GROUP BY mlp.loan_no) mlp';
			
			//[START] Modified by Kweeny Libutan for 8th Enhancement 2013/09/05
			$leftOuterSelect = '(SELECT mlp.loan_no															
					,balance
				FROM m_loan_payment mlp													
				INNER JOIN m_loan ml													
					ON ml.loan_no = mlp.loan_no												
				LEFT OUTER JOIN t_transaction tt 													
					ON tt.reference = CONCAT(mlp.loan_no,",",mlp.transaction_code,",",mlp.payment_date,",",mlp.payor_id)												
				WHERE ml.close_flag = 0													
					AND tt.reference IS NULL												
					AND YEAR(mlp.payment_date) = "'.substr($acctgPeriodBSP,0,-2).'"												
					AND MONTH(mlp.payment_date) = "'.substr($acctgPeriodBSP,-2).'"
					AND mlp.status_flag = \'2\'												
				GROUP BY mlp.loan_no) mlp_bsp';
			//[END] Modified by Kweeny Libutan for 8th Enhancement 2013/09/05
					
			$this->db->join($innerSelect, 'm_loan_payment.loan_no=LP1.loan_no AND m_loan_payment.modified_date=LP1.LastPay', 'INNER');
    	//$this->db->where('LP1.loan_no IS NOT NULL AND status_flag = \'3\'');
		$innerResult = $this->db->get();
		$innerQuery = $this->db->last_query();
		
		if($select)
			$this->db->select($select);
    	$this->db->from($this->table_name);
		$this->db->join($innerSelect2, 'mlp.loan_no = m_loan.loan_no', 'INNER');
		//[START] Added by Vincent Sy for 8th Enhancement 2013/08/02
		$this->db->join($leftOuterSelect, 'mlp_bsp.loan_no = m_loan.loan_no', 'LEFT OUTER');
		//[END] Added by Vincent Sy for 8th Enhancement 2013/08/02
    	$this->db->join('r_loan_header', 'r_loan_header.loan_code=m_loan.loan_code', 'INNER');
		$this->db->join('m_employee', 'm_employee.employee_id=m_loan.employee_id', 'INNER');
		$this->db->join('r_company', 'r_company.company_code=m_employee.company_code', 'INNER');
		$this->db->join('('.$innerQuery.') LP', 'm_loan.loan_no=LP.loan_no', 'INNER');
		if ($filter)
			$this->db->where($filter);
		$result = $this->db->get();
		$query = $this->db->last_query();
		log_message('debug', 'yy-yy '.$query . 'yy-yy ');
		$count = $result->num_rows();
		return $this->checkError($result->result_array(), $count, $query);
	}
	
	/**
     * @desc Retrieve all loans after accounting period for subsidy report
     * @param $filter
     * @param $select
     */
	function getLoansAfterAcctgPeriod($filter = null, $select = null){
		if($select)
			$this->db->select($select);
    	$this->db->from($this->table_name);
    	$this->db->join('r_loan_header', 'r_loan_header.loan_code=m_loan.loan_code', 'INNER');
		$this->db->join('m_employee', 'm_employee.employee_id=m_loan.employee_id', 'INNER');
		$this->db->join('r_company', 'r_company.company_code=m_employee.company_code', 'INNER');
		if ($filter)
			$this->db->where($filter);
		$result = $this->db->get();
		$query = $this->db->last_query();
		$count = $result->num_rows();
		return $this->checkError($result->result_array(), $count, $query);
	}
	
	/**
	 * @desc Merge the two functions above
	 * @param $innerJoin
	 * @param $filter
	 * @param $orderby
	 */
	function readAll($innerJoin =null, $select =null, $orderby = null){
		if($select)
			$this->db->select($select);
    	$this->db->from($this->table_name);
    	$this->db->join('( '.$innerJoin.' ) L', 'L.loan_no=m_loan.loan_no ', 'INNER');
		//$this->db->order_by('m_loan.employee_id ASC');
		//$this->db->order_by('m_loan.loan_date ASC');
		//$this->db->where('company_code="753"');
		$this->db->group_by('m_loan.loan_no');
		$this->db->order_by('m_loan.employee_id');
		
		$result = $this->db->get();	
		$query = $this->db->last_query();
		log_message('debug', 'readall '.$query . 'readall');
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
	}
	
	/**
     * @desc Retrieve all loans before accounting period for subsidy report
     * @param $acctgPeriod
     * @param $filter
     * @param $select
     */
	function getLoansBeforeAcctgPeriod2($acctgPeriod, $filter = null, $select = null){
			
		$this->db->select('m_loan_payment.Loan_no, balance');
		$this->db->from('m_loan_payment');
			$innerSelect = '(SELECT m_loan_payment.loan_no, MAX(modified_date) AS LastPay 
							FROM m_loan_payment 
							WHERE payment_date<=\''.$acctgPeriod.'\' 
							GROUP BY loan_no) LP1';
			$this->db->join($innerSelect, 'm_loan_payment.loan_no=LP1.loan_no AND m_loan_payment.modified_date=LP1.LastPay', 'LEFT OUTER');
    	$this->db->where('LP1.loan_no IS NOT NULL AND status_flag = \'3\'');
		$innerResult = $this->db->get();
		$innerQuery = $this->db->last_query();
		
		if($select)
			$this->db->select($select);
    	$this->db->from($this->table_name);
    	$this->db->join('r_loan_header', 'r_loan_header.loan_code=m_loan.loan_code', 'LEFT OUTER');
		$this->db->join('m_employee', 'm_employee.employee_id=m_loan.employee_id', 'LEFT OUTER');
		$this->db->join('r_company', 'r_company.company_code=m_employee.company_code', 'LEFT OUTER');
		$this->db->join('('.$innerQuery.') LP', 'm_loan.loan_no=LP.loan_no', 'LEFT OUTER');
		if ($filter)
			$this->db->where($filter);
		$this->db->group_by('m_employee.last_name');
		$result = $this->db->get();
		
		$query = $this->db->last_query();
		
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
			
	}
	
	/**
     * @desc Retrieve all loans after accounting period for subsidy report
     * @param $filter
     * @param $select
     */
	function getLoansAfterAcctgPeriod2($filter = null, $select = null){
		if($select)
			$this->db->select($select);
    	$this->db->from($this->table_name);
    	$this->db->join('r_loan_header', 'r_loan_header.loan_code=m_loan.loan_code', 'INNER');
		$this->db->join('m_employee', 'm_employee.employee_id=m_loan.employee_id', 'INNER');
		$this->db->join('r_company', 'r_company.company_code=m_employee.company_code', 'INNER');
		if ($filter)
			$this->db->where($filter);
		$this->db->group_by('m_employee.last_name');
		$result = $this->db->get();
			
		$query = $this->db->last_query();
		log_message('debug', 'getLoansAfterAcctgPeriod2 '.$query . 'getLoansAfterAcctgPeriod2');
		$count = $result->num_rows();
		
		
		return $this->checkError($result->result_array(), $count, $query);
			
	}
	
	function retrieveConsolPrinAmount1($employee_id,$date)
	{			
		$sql = "SELECT COALESCE(SUM( `m_loan`.`company_interest_amort` + (CASE WHEN m_loan.amortization_startdate > '$date' THEN initial_interest ELSE 0 END)),0) AS consol
				FROM (`m_loan`) 
				INNER JOIN (SELECT `m_loan_payment`.`loan_no`
						, `balance` 
						FROM (`m_loan_payment`) 
						INNER JOIN (SELECT m_loan_payment.loan_no
								, MAX(modified_date) AS LastPay 
							FROM m_loan_payment 
							WHERE payment_date<='$date' 
							GROUP BY loan_no) LP1 
						ON `m_loan_payment`.`loan_no`=`LP1`.`loan_no` AND m_loan_payment.modified_date=LP1.LastPay) LP 
				ON `m_loan`.`loan_no`=`LP`.`loan_no` 
				WHERE (m_loan.company_interest_amort > 0) 
					AND (LP.balance>0) 
					AND (m_loan.loan_date<'$date' AND LP.loan_no IS NOT NULL) 
					AND m_loan.employee_id='$employee_id'";	
				
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);			
	}
	
	function retrieveConsolPrinAmount2($employee_id,$date)
	{
		$sql = "SELECT COALESCE(SUM(ROUND(m_Loan.Company_interest_rate*m_loan.initial_interest/m_loan.interest_rate,0) + (CASE WHEN m_loan.amortization_startdate > '$date' THEN initial_interest ELSE 0 END)),0) AS consol
				FROM (`m_loan`) 
				WHERE (m_loan.company_interest_amort > 0) 
					AND (m_loan.principal_balance >0) 
					AND (m_loan.loan_date>='$date')
					AND (m_loan.employee_id='$employee_id')";
				
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);			
	}
	
}
?>