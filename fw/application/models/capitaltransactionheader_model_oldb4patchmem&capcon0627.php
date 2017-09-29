<?php

class Capitaltransactionheader_model extends Asi_Model {
	var $table_name = 't_capital_transaction_header';
	var $id = 'transaction_no';
	var $model_data = null;
	var $date_columns = array('transaction_date', 'or_date');
	
	
    function Capitaltransactionheader_model() {
        parent::Asi_Model();
    }

    function getListCapconTrans($filter = null, $limit = null, $offset = null, $select = null, $orderby = null) {
    	if($select)
    		$this->db->select($select);
    	$this->db->from('t_capital_transaction_header tc');
    	$this->db->join('m_employee me', 'me.employee_id =  tc.employee_id', 'left outer');
    	$this->db->join('r_transaction rt', 'rt.transaction_code  = tc.transaction_code', 'left outer');
    	if($filter)	
    		$this->db->where($filter);
    	if($orderby)	
    		$this->db->orderby($orderby);	
    	$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
    	
    	if($filter){
			$this->db->where($filter);
			$this->db->from('t_capital_transaction_header tc');
	    	$this->db->join('m_employee me', 'me.employee_id =  tc.employee_id', 'left outer');
	    	$this->db->join('r_transaction rt', 'rt.transaction_code  = tc.transaction_code', 'left outer');
			$count = $this->db->count_all_results();
		}else{
			$count = $this->db->count_all($this->table_name);
		}
		
    	return $this->checkError($result->result_array(), $count, $query);
    }
    
	function getCapconTrans($_param = null, $select = null){
		if($select)
			$this->db->select($select);

		$this->db->from('t_capital_transaction_header tc');
    	$this->db->join('m_employee me', 'me.employee_id =  tc.employee_id', 'left outer');
    	$this->db->join('r_transaction rt', 'rt.transaction_code  = tc.transaction_code', 'left outer');
    
		$this->db->where($_param ? $_param : $this->model_data);
		$result = $this->db->get();  
		$query = $this->db->last_query();
		return $this->checkError($result->result_array(),0,$query);
	}

}
?>
