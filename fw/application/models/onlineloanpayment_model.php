<?php
/*
 * Created on Apr 26, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Onlineloanpayment_model extends Asi_Model {
	
    function Onlineloanpayment_model()
    {
        parent::Asi_Model();
        $this->table_name = 'o_loan_payment';
        $this->id = 'request_no';
        $this->date_columns = array('payment_date', 'or_date');
    }
    
    function getListLoanPayment($filter = null, $date_from = null,
    			$date_to = null, $limit = null, $offset = null, $status = null, $select = null, $orderby = null){
		if($select)
			$this->db->select($select);
    	$this->db->from('o_loan_payment tlp');
		$this->db->from('o_workflow ow');
    	$this->db->join('m_loan ml', 'ml.loan_no = tlp.loan_no', 'left outer');
    	$this->db->join('r_loan_header rlh', 'rlh.loan_code = ml.loan_code', 'left outer');
		$this->db->join('r_transaction rt', 'rt.transaction_code = tlp.transaction_code', 'left outer');
    	$this->db->join('m_employee me', 'me.employee_id = tlp.payor_id', 'left outer');
		$this->db->join('r_user AS ru1', 'ru1.user_name=ow.approver1', 'LEFT');
    	$this->db->join('r_user AS ru2', 'ru2.user_name=ow.approver2', 'LEFT');
    	$this->db->join('r_user AS ru3', 'ru3.user_name=ow.approver3', 'LEFT');
    	$this->db->join('r_user AS ru4', 'ru4.user_name=ow.approver4', 'LEFT');
    	$this->db->join('r_user AS ru5', 'ru5.user_name=ow.approver5', 'LEFT');
		if ($filter)
			$this->db->where($filter);
		if ($status == 0)
			$this->db->where('tlp.status_flag != 0 AND tlp.status_flag != 9');
		else if($status == 3)
			$this->db->where('tlp.status_flag BETWEEN 3 AND 8');
		else
			$this->db->where('tlp.status_flag =\''.$status.'\'');
		if ($date_from && $date_to)
			$this->db->where('tlp.payment_date BETWEEN \''.$date_from.'\' AND \''.$date_to.'\'');
		if($orderby)
		$this->db->order_by($orderby);
		$this->db->limit($offset, $limit);
		$result = $this->db->get();
		$query = $this->db->last_query();
		
		if ($filter){
			$this->db->where($filter);
			if ($status == 0)
				$this->db->where('tlp.status_flag != 0 AND tlp.status_flag != 9');
			else if($status == 3)
				$this->db->where('tlp.status_flag BETWEEN 3 AND 8');
			else
				$this->db->where('tlp.status_flag =\''.$status.'\'');
			if ($date_from && $date_to)
				$this->db->where('tlp.payment_date BETWEEN \''.$date_from.'\' AND \''.$date_to.'\'');
			$this->db->from('o_loan_payment tlp');
			$this->db->from('o_workflow ow');
	    	$this->db->join('m_loan ml', 'ml.loan_no = tlp.loan_no', 'left outer');
	    	$this->db->join('r_loan_header rlh', 'rlh.loan_code = ml.loan_code', 'left outer');
	    	$this->db->join('m_employee me', 'me.employee_id = tlp.payor_id', 'left outer');
			$this->db->join('r_user AS ru1', 'ru1.user_name=ow.approver1', 'LEFT');
	    	$this->db->join('r_user AS ru2', 'ru2.user_name=ow.approver2', 'LEFT');
	    	$this->db->join('r_user AS ru3', 'ru3.user_name=ow.approver3', 'LEFT');
	    	$this->db->join('r_user AS ru4', 'ru4.user_name=ow.approver4', 'LEFT');
	    	$this->db->join('r_user AS ru5', 'ru5.user_name=ow.approver5', 'LEFT');
	    	$count = $this->db->count_all_results();
		}else
			$count = $this->db->count_all($this->table_name);
		
		return $this->checkError($result->result_array(), $count, $query);	
	}
	
 	function getLpDtl($filter = null, $limit = null, $offset = null, $select = null, $orderby = null){
		if($select)
			$this->db->select($select);
    	$this->db->from('o_loan_payment tlp');
    	$this->db->join('m_employee me', 'me.employee_id = tlp.payor_id', 'left outer');
    	$this->db->join('r_transaction rt', 'rt.transaction_code = tlp.transaction_code', 'left outer');
		if ($filter)
			$this->db->where($filter);
		if($orderby)
		$this->db->order_by($orderby);
		$this->db->limit($offset, $limit);
		$result = $this->db->get();
			
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
			
	}
    
 	function getApprovers($filter = null, $limit = null, $offset = null, $select = null, $orderby = null) 
	{
		if($select)
    		$this->db->select($select);
    	$this->db->from('o_loan_payment op');
    	$this->db->from('o_workflow ow');
    	$this->db->join('m_employee AS me', 'me.employee_id = op.employee_id', 'LEFT');
    	$this->db->join('r_user AS ru1', 'ru1.user_name=ow.approver1', 'LEFT');
    	$this->db->join('r_user AS ru2', 'ru2.user_name=ow.approver2', 'LEFT');
    	$this->db->join('r_user AS ru3', 'ru3.user_name=ow.approver3', 'LEFT');
    	$this->db->join('r_user AS ru4', 'ru4.user_name=ow.approver4', 'LEFT');
    	$this->db->join('r_user AS ru5', 'ru5.user_name=ow.approver5', 'LEFT');
    	
    	if($filter)	
    		$this->db->where($filter);
    	if($orderby)	
    		$this->db->order_by($orderby);	
    	$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
    	
    	$count = $this->db->count_all($this->table_name);
  		return $this->checkError($result->result_array(), $count, $query);
    }
	
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
		//$this->db->where('ml.loan_no NOT IN (SELECT ol.loan_no FROM o_loan_payment ol WHERE ol.status_flag > 0)');
    	if($orderby)	
    		$this->db->orderby($orderby);	
    	$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
    	
		if($filter){
			if($distinct)
				$this->db->distinct();
			$this->db->where($filter);
			$this->db->from('m_loan ml');
	    	$this->db->join('m_employee me', 'me.employee_id=ml.employee_id', 'inner');
	    	$this->db->join('r_loan_header rl', 'rl.loan_code=ml.loan_code', 'inner');
			$count = $this->db->count_all_results();
		}else{
			$count = $this->db->count_all($this->table_name);
		}
    
    	return $this->checkError($result->result_array(), $count, $query);
    }
}

?>
