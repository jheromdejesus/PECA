<?php
/*
 * Created on Apr 20, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Onlineloan_model extends Asi_Model {

    function Onlineloan_model()
    {
        parent::Asi_Model();
        $this->table_name = 'o_loan';
        $this->id = 'request_no';
        $this->date_columns = array('loan_date');
    }

    /**
	 * @desc Retrieve all Online Loan Transactions
	 */
    function retrieveAllOnlineLoanTransactions($loan_code = null,$loan_date_from = null,
    			$loan_date_to = null, $last_name =null, $first_name =null,
    			$empId =null, $status =1, 
    		 	$limit = null, $offset = null, $select = null, $orderby = null)
    {
    	if($select)
			$this->db->select($select);
    	$this->db->from('o_loan ol');
    	$this->db->join('r_loan_header rl', 'rl.loan_code=ol.loan_code', 'inner');
    	$this->db->join('m_employee me', 'me.employee_id=ol.employee_id', 'inner');
    	$this->db->join('o_workflow ow', 'ow.request_type=ol.loan_code', 'inner');
		$this->db->join('r_user AS ru1', 'ru1.user_name=ow.approver1', 'LEFT');
    	$this->db->join('r_user AS ru2', 'ru2.user_name=ow.approver2', 'LEFT');
    	$this->db->join('r_user AS ru3', 'ru3.user_name=ow.approver3', 'LEFT');
    	$this->db->join('r_user AS ru4', 'ru4.user_name=ow.approver4', 'LEFT');
    	$this->db->join('r_user AS ru5', 'ru5.user_name=ow.approver5', 'LEFT');
    	$this->db->where('rl.status_flag != 0');
		if ($loan_code)
			$this->db->where('ol.loan_code =\''.$loan_code.'\'');
		if ($last_name)
			$this->db->where('me.last_name LIKE \''.$last_name.'%\'');
		if ($first_name)
			$this->db->where('me.first_name LIKE \''.$first_name.'%\'');
		if ($empId)
			$this->db->where('ol.employee_id LIKE \''.$empId.'%\'');
		if ($status == 0)
			$this->db->where('ol.status_flag != 0 AND ol.status_flag != 9');
		else if($status == 3)
			$this->db->where('ol.status_flag >= 3 AND ol.status_flag < 9');
		else
			$this->db->where('ol.status_flag =\''.$status.'\'');
		if ($loan_date_from && $loan_date_to)
			$this->db->where('ol.loan_date BETWEEN \''.$loan_date_from.'\' AND \''.$loan_date_to.'\'');
		//$this->db->where('ol.status_flag != 0');
		
		$clone_db = clone $this->db;
		$count = $clone_db->count_all_results();
		
		if($orderby)
			$this->db->order_by($orderby);
		$this->db->limit($offset, $limit);
		$result = $this->db->get();
			
		$query = $this->db->last_query();
		//$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
    }
    
    /**
	 * @desc Retrieve Employee interest rate for the specified loan type
	 */
    function retrieveEmployeeInterestRate($filter = null, $limit = null, $offset = null, $select = null, $orderby = null)
    {
    	if($select)
			$this->db->select($select);
    	$this->db->from('r_loan_header');
    	if ($filter)
			$this->db->where($filter);
		if($orderby)
			$this->db->order_by($orderby);
		$this->db->limit($offset, $limit);
		$result = $this->db->get();
			
		$query = $this->db->last_query();
		//$count = $result->num_rows();
		$count = $this->db->count_all('r_loan_header');
		return $this->checkError($result->result_array(), $count, $query);
    }
    
 	/**
	 * @desc show
	 */
    function showAllOnlineLoanTransactions($filter = null, $limit = null, $offset = null, $select = null, $orderby = null)
    {
    	if($select)
			$this->db->select($select);
    	$this->db->from('o_loan ol');
    	$this->db->join('r_loan_header rl', 'rl.loan_code=ol.loan_code', 'inner');
    	$this->db->join('m_employee me', 'me.employee_id=ol.employee_id', 'inner');
    	$this->db->join('o_workflow ow', 'ow.request_type=ol.loan_code', 'inner');
		$this->db->join('r_user AS ru1', 'ru1.user_name=ow.approver1', 'LEFT');
    	$this->db->join('r_user AS ru2', 'ru2.user_name=ow.approver2', 'LEFT');
    	$this->db->join('r_user AS ru3', 'ru3.user_name=ow.approver3', 'LEFT');
    	$this->db->join('r_user AS ru4', 'ru4.user_name=ow.approver4', 'LEFT');
    	$this->db->join('r_user AS ru5', 'ru5.user_name=ow.approver5', 'LEFT');
    	$this->db->where('rl.status_flag != 0');
		if ($filter)
			$this->db->where($filter);
		$this->db->where('ol.status_flag != 0');
		if($orderby)
			$this->db->order_by($orderby);
		$this->db->limit($offset, $limit);
		$result = $this->db->get();
			
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
    }
    
}

?>
