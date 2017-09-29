<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Loanpayment_model extends Asi_Model {
	
    function Loanpayment_model()
    {
        parent::Asi_Model();
        $this->table_name = 't_loan_payment';
        $this->id = array('loan_no', 'transaction_code', 'payment_date', 'payor_id');
        $this->date_columns = array('payment_date','or_date');
    }
    
    function getListLoanPayment($filter = null, $limit = null, $offset = null, $select = null, $orderby = null){
		if($select)
			$this->db->select($select);
    	$this->db->from('t_loan_payment tlp');
    	$this->db->join('m_loan ml', 'ml.loan_no = tlp.loan_no', 'left outer');
    	$this->db->join('r_loan_header rlh', 'rlh.loan_code = ml.loan_code', 'left outer');
    	$this->db->join('m_employee me', 'me.employee_id = tlp.payor_id', 'left outer');
    	$this->db->join('m_employee mee', 'mee.employee_id = ml.employee_id', 'left outer');
		if ($filter)
			$this->db->where($filter);
		if($orderby)
		$this->db->order_by($orderby);
		$this->db->limit($offset, $limit);
		$result = $this->db->get();
			
		$query = $this->db->last_query();
		
		log_message('debug', " delete123".$query);
		
		if($filter){
			$this->db->where($filter);
			$this->db->from('t_loan_payment tlp');
	    	$this->db->join('m_loan ml', 'ml.loan_no = tlp.loan_no', 'left outer');
	    	$this->db->join('r_loan_header rlh', 'rlh.loan_code = ml.loan_code', 'left outer');
	    	$this->db->join('m_employee me', 'me.employee_id = tlp.payor_id', 'left outer');
			$count = $this->db->count_all_results();
		}else{
			$count = $this->db->count_all($this->table_name);
		}

		return $this->checkError($result->result_array(), $count, $query);	
	}
	
 	function getLpDtl($filter = null, $limit = null, $offset = null, $select = null, $orderby = null){
		if($select)
			$this->db->select($select);
    	$this->db->from('t_loan_payment tlp');
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
    
}

?>
