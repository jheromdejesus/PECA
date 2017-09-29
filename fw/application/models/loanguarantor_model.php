<?php

class Loanguarantor_model extends Asi_Model {
	var $table_name = 't_loan_guarantor';
	var $id = 'loan_no';
	var $model_data = null;
	var $date_columns = array('');
	
	
    function Loanguarantor_model() {
        parent::Asi_Model();
    }
    
    /**
	 * @desc Retrieve guarantors for selected loan transaction
	 * @return array
	 */
	function getGuarantor($filter = null, $limit = null, $offset = null, $select = null, $orderby = null) 
	{
    	if($select)
    		$this->db->select($select);
    	$this->db->from('t_loan_guarantor tlg');
    	$this->db->join('m_employee me', 'me.employee_id=tlg.guarantor_id', 'inner');
    	if($filter)	
    		$this->db->where($filter);
    	if($orderby)	
    		$this->db->orderby($orderby);	
    	$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
    	
    	//$count = $result->num_rows();
		if($filter){
			$this->db->where($filter);
			$this->db->from('t_loan_guarantor tlg');
			$this->db->join('m_employee me', 'me.employee_id=tlg.guarantor_id', 'inner');
			$count = $this->db->count_all_results();
		}else{
			$count = $this->db->count_all($this->table_name);
		}
    	
		return $this->checkError($result->result_array(), $count, $query);
    }
    
	function getMLoanGuaranteed($filter = null, $limit = null, $offset = null, $select = null, $orderby = null) 
	{
    	if($select)
    		$this->db->select($select);
    	$this->db->from('m_loan_guarantor tlg');
    	$this->db->join('m_loan ml', 'ml.loan_no=tlg.loan_no', 'inner');
    	if($filter)	
    		$this->db->where($filter);
    	if($orderby)	
    		$this->db->orderby($orderby);	
	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
    	$count = $result->num_rows();
		
    	/*if($filter){
			$this->db->where($filter);
			$this->db->from($this->table_name);
			$count = $this->db->count_all_results();
		}else{
			$count = $this->db->count_all($this->table_name);
		}*/
			
		return $this->checkError($result->result_array(), $count, $query);
    }
    
	function getMLoanGuaranteedWithName($filter = null, $limit = null, $offset = null, $select = null, $orderby = null) 
	{
    	if($select)
    		$this->db->select($select);
    	$this->db->from('m_loan_guarantor tlg');
    	$this->db->join('m_loan ml', 'ml.loan_no=tlg.loan_no', 'inner');
    	$this->db->join('r_loan_header rl', 'rl.loan_code = ml.loan_code', 'left outer');
    	$this->db->join('m_employee me', 'me.employee_id=ml.employee_id', 'left outer');
    	if($filter)	
    		$this->db->where($filter);
    	if($orderby)	
    		$this->db->orderby($orderby);	
	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
  
    	if($filter){
			$this->db->where($filter);
			$this->db->from('m_loan_guarantor tlg');
	    	$this->db->join('m_loan ml', 'ml.loan_no=tlg.loan_no', 'inner');
	    	$this->db->join('r_loan_header rl', 'rl.loan_code = ml.loan_code', 'left outer');
		$this->db->join('m_employee me', 'me.employee_id=ml.employee_id', 'left outer');
			$count = $this->db->count_all_results();
		}else{
			$count = $this->db->count_all($this->table_name);
		}
			
		return $this->checkError($result->result_array(), $count, $query);
    }
    
    function getLoanGuaranteed($filter = null, $limit = null, $offset = null, $select = null, $orderby = null) 
	{
    	if($select)
    		$this->db->select($select);
    	$this->db->from('t_loan_guarantor tlg');
    	$this->db->join('t_loan tl', 'tl.loan_no=tlg.loan_no', 'inner');
    	if($filter)	
    		$this->db->where($filter);
    	if($orderby)	
    		$this->db->orderby($orderby);	
	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
    	$count = $result->num_rows();
		
    	/*if($filter){
			$this->db->where($filter);
			$this->db->from($this->table_name);
			$count = $this->db->count_all_results();
		}else{
			$count = $this->db->count_all($this->table_name);
		}*/
			
		return $this->checkError($result->result_array(), $count, $query);
    }
    
}