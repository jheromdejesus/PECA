<?php

class Loanpaymentdetail_model extends Asi_Model {
	var $table_name = 't_loan_payment_detail';
	var $id = 'loan_no';
	var $model_data = null;
	var $date_columns = array('payment_date');
	
	
    function Loanpaymentdetail_model()
    {
        parent::Asi_Model();
    }
  
	function getLpDetail($filter = null, $limit = null, $offset = null, $select = null, $orderby = null){
		if($select)
			$this->db->select($select);
    	$this->db->from('t_loan_payment_detail tlpd');
    	$this->db->join('r_transaction rt', 'rt.transaction_code = tlpd.transaction_code', 'left outer');
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