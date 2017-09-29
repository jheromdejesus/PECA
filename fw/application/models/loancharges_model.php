<?php

class Loancharges_model extends Asi_Model {
	var $table_name = 't_loan_charges';
	var $id = 'loan_no';
	var $model_data = null;
	var $date_columns = array('');
	
	
    function Loancharges_model() {
        parent::Asi_Model();
    }
    
	function getServiceCharges($filter = null, $limit = null, $offset = null, $select = null, $orderby = null)
	{
		if($select)
    		$this->db->select($select);
    	$this->db->from('t_loan_charges tlc');
    	$this->db->join('r_transaction rt', 'rt.transaction_code = tlc.transaction_code', 'inner');
    	if($filter)	
    		$this->db->where($filter);
    	if($orderby)	
    		$this->db->orderby($orderby);	
    	$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
    	
		if($filter){
			$this->db->where($filter);
			$this->db->from('t_loan_charges tlc');
			$this->db->join('r_transaction rt', 'rt.transaction_code = tlc.transaction_code', 'inner');
			$this->db->last_query();
			$count = $this->db->count_all_results();
		}else{
			$count = $this->db->count_all($this->table_name);
		};
    	
		return $this->checkError($result->result_array(), $count, $query);
	}
    
}