<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Tinvestmentheader_model extends Asi_Model {
	
    function Tinvestmentheader_model()
    {
        parent::Asi_Model();
        $this->table_name = 't_investment_header';
        $this->id = 'investment_no';
        $this->date_columns = array('');
    }
    
	// function retrieveNewInvestmentTransactions(){
   	// $sql = "SELECT ti.*, rt.gl_code							
				// FROM t_investment_header ti									
				// INNER JOIN r_transaction rt									
					// ON ti.transaction_code = rt.transaction_code								
				// WHERE ti.status_flag = '1'
				// FOR UPDATE";
		
   	// $result = $this->db->query($sql);
	// $query = $this->db->last_query();
	// $count = $result->num_rows();
 
   	// echo $this->db->last_query();
   	// return $this->checkError($result->result_array(), $count, $query);
   // }
    
 	function retrieveNewInvestmentTransactions(){
 		$this->db->select('i.*, rt.gl_code');
    	$this->db->from('t_investment_header AS i');
    	$this->db->join('r_transaction AS rt', 'i.transaction_code = rt.transaction_code', 'inner');
    	$this->db->where(array('i.status_flag' => '1'));
    	    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
		// echo $query;
    	$count = $this->db->count_all($this->table_name);  
  		
		return $this->checkError($result->result_array(), 0, $query);
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
			
		$clone_db = clone $this->db;
		$count = $clone_db->count_all_results();
		
    	if($orderby)	
    		$this->db->order_by($orderby);	
    	$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
    	//$count = $this->db->count_all($this->table_name);  
  		
		return $this->checkError($result->result_array(), $count, $query);
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

	$clone_db = clone $this->db;
	$count = $clone_db->count_all_results();
	
    	if($orderby)	
    		$this->db->order_by($orderby);	
    	$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
    	
    	//$count = $this->db->count_all($this->table_name);  
  		
		return $this->checkError($result->result_array(), $count, $query);
    }
    
	 /**
	 * @desc To retrieve a single investment
	 * @return array
	 */
	function getInvestmentMaturity($filter = null, $limit = null, $offset = null, $select = null, $orderby = null) 
	{
		if($select)
    		$this->db->select($select);
    	$this->db->from('t_investment_header AS i');
    	$this->db->join('r_transaction AS rt', 'i.transaction_code = rt.transaction_code', 'left outer');
    	$this->db->join('m_supplier AS m', 'i.supplier_id = m.supplier_id', 'left outer');
    	if($filter)	
    		$this->db->where($filter);
    	if($orderby)	
    		$this->db->order_by($orderby);	
    	$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
    	
    	$count = $this->db->count_all($this->table_name);  
  		
		return $this->checkError($result->result_array(), $count, $query);
    }
	
}

?>
