<?php

class Loanpaymentreport_model extends Asi_Model {
	var $table_name = 't_loan_payment';
	var $id = 'loan_no';
	var $model_data = null;
	var $date_columns = array('payment_date');
	
	
    function Loanpaymentreport_model()
    {
        parent::Asi_Model();
    }
  
    /**
     * @desc Retrieves loan payments that are not yet processed
     * @param $filter
     * @param $limit
     * @param $offset
     * @param $select
     * @param $orderby
     */
	function retrieveLoanPaymentProofList($filter = null, $limit = null, $offset = null, $select = null, $orderby = null, $distinct=null){
		if($distinct)
			$this->db->distinct($distinct);
		if($select)
			$this->db->select($select);
    	$this->db->from('t_loan_payment TL');
    	$this->db->join('r_transaction RT', 'RT.transaction_code=TL.transaction_code', 'INNER');
		$this->db->join('m_employee ME', 'ME.employee_id=TL.payor_id', 'INNER');
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
	
	/**
	 * @desc Retrieves list of loan payments that are already processed
	 * @param $filter
	 * @param $limit
	 * @param $offset
	 * @param $select
	 * @param $orderby
	 */
	function retrieveLoanPaymentAuditTrail($filter = null, $limit = null, $offset = null, $select = null, $orderby = null,$distinct=null){
		if($distinct)
			$this->db->distinct($distinct);
		if($select)
			$this->db->select($select);
    	$this->db->from('m_loan_payment TL');
    	$this->db->join('r_transaction RT', "RT.transaction_code=TL.transaction_code AND TL.source IN ('U','S')", 'INNER');
		$this->db->join('m_employee ME', 'ME.employee_id=TL.payor_id', 'INNER');
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