<?php

class Loancodedetail_model extends Asi_Model {
	var $table_name = 'r_loan_detail';
	var $id = 'loan_code';
	var $model_data = null;
	var $date_columns = array('');
	
	
    function Loancodedetail_model()
    {
        parent::Asi_Model();
    }
    
	function getLoanDetail($filter = null, $limit = null, $offset = null, $select = null, $groupby = null, $orderby = null){
		$this->db->from($this->table_name);
		if($select)
		$this->db->select($select);
		if($orderby)
		$this->db->order_by($orderby);
		if($groupby)
		$this->db->group_by($groupby);
		if ($filter)
		$this->db->where($filter);
		$this->db->limit($offset, $limit);
		$result = $this->db->get();
			
		$query = $this->db->last_query();

		if($filter){
			$this->db->where($filter);
			$this->db->from($this->table_name);
			$count = $this->db->count_all_results();
		}else{
			$count = $this->db->count_all($this->table_name);
		}
			
		return $this->checkError($result->result_array(), $count, $query);
			
	}
	
	 function isCapConRequired($loan_code, $yos){
    	$ret_val = false;
		
    	$sql = "SELECT capital_contribution
				FROM r_loan_detail
				WHERE loan_code = '{$loan_code}'
					AND years_of_service <= {$yos}
				ORDER BY years_of_service DESC
				LIMIT 1";
		
    	$query = $this->db->query($sql);    
    	//echo $this->db->last_query();
    	
    	$result = $query->result_array(); 
    	if (count($result) > 0){
    		if ($result[0]['capital_contribution'] > 0){
    			$ret_val = true;
    		}
    	}	
    	    	
    	return $ret_val;
    }

}
?>