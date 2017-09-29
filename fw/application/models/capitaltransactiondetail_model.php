<?php

class Capitaltransactiondetail_model extends Asi_Model {
	var $table_name = 't_capital_transaction_detail';
	var $id = 'transaction_no';
	var $model_data = null;
	var $date_columns = array('');
	
	
    function Capitaltransactiondetail_model() {
        parent::Asi_Model();
    }
    
	function getListTransCharge($filter = null, $limit = null, $offset = null, $select = null, $orderby = null){
		if($select)
			$this->db->select($select);
		$this->db->from('t_capital_transaction_detail tcc');
		$this->db->join('r_transaction rt', 'rt.transaction_code=tcc.transaction_code', 'left outer');
		if($filter)
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
