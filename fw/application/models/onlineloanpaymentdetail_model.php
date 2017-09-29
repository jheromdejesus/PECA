<?php
/*
 * Created on Apr 26, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

class Onlineloanpaymentdetail_model extends Asi_Model {
	var $table_name = 'o_loan_payment_detail';
	var $id = 'request_no';
	var $model_data = null;
	var $date_columns = array('payment_date');
	
	
    function Onlineloanpaymentdetail_model()
    {
        parent::Asi_Model();
    }
  
	function getLpDetail($filter = null, $limit = null, $offset = null, $select = null, $orderby = null){
		if($select)
			$this->db->select($select);
    	$this->db->from('o_loan_payment_detail tlpd');
    	$this->db->join('r_transaction rt', 'rt.transaction_code = tlpd.transaction_code', 'left outer');
		if ($filter)
			$this->db->where($filter);
		if($orderby)
			$this->db->order_by($orderby);
		$this->db->limit($offset, $limit);
		$result = $this->db->get();
			
		$query = $this->db->last_query();
		
		if($filter){
			$this->db->where($filter);
			$this->db->from('o_loan_payment_detail tlpd');
    		$this->db->join('r_transaction rt', 'rt.transaction_code = tlpd.transaction_code', 'left outer');
    		$count = $this->db->count_all_results();
		}else
			$count = $this->db->count_all($this->table_name);
		
		return $this->checkError($result->result_array(), $count, $query);
			
	}
}
?>