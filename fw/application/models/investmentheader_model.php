<?php

class Investmentmaturity_model extends Asi_Model {
	
	var $table_name = 't_investment_header';
	var $id = 'investment_no';
	var $model_data = null;
	var $date_columns = array('');
	
    function Investmentmaturity_model(){
    	
        parent::Asi_Model();
        
    }

    /**
	 * @desc To retrieve investments that have not yet matured
	 * @return array
	 */
	function getInvestmentMaturityList($filter = null, $limit = null, $offset = null, $select = null, $orderby = null) 
	{
		if($select)
    		$this->db->select($select);
    	$this->db->from('t_investment_header AS i');
    	$this->db->join('r_transaction AS rt', 'i.transaction_code = rt.transaction_code', 'left outer');
    	$this->db->join('m_supplier AS m', 'i.supplier_id = m.supplier_id', 'left outer');
    	if($filter)	
    		$this->db->where($filter);
    		$this->db->where('rt.transaction_code IS NOT NULL', null, false);
    		$this->db->where('i.supplier_id IS NOT NULL', null, false);
    	if($orderby)	
    		$this->db->order_by($orderby);	
    	$this->db->limit($offset, $limit);
    	
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
 
	function getInvestmentList($filter = null, $limit = null, $offset = null, $select = null, $orderby = null) 
	{
		if($select)
    		$this->db->select($select);
    	$this->db->from('t_investment_header AS i');
    	$this->db->join('r_transaction AS rt', 'i.transaction_code = rt.transaction_code', 'left outer');
    	$this->db->join('m_supplier AS s', 'i.supplier_id = s.supplier_id', 'left outer');
    	if($filter)	
    		$this->db->where($filter);
    	if($orderby)	
    		$this->db->order_by($orderby);	
    	$this->db->limit($offset, $limit);
    	
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

?>
