<?php

class Minvestmentheader_model extends Asi_Model {
	
	var $table_name = 'm_investment_header';
	var $id = 'investment_no';
	var $model_data = null;
	var $date_columns = array('');
	
    function Minvestmentheader_model(){
    	
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
    	$this->db->from('m_investment_header AS i');
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
    	$this->db->from('m_investment_header AS i');
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
    

    function getInvestmentHdrForReport($transaction_date, $supplier_id)
    {
    	$sql = "SELECT investment_no
				  ,transaction_code
				  ,placement_days
				  ,placement_date
				  ,maturity_date
				  ,interest_rate
				  ,interest_amount
				  ,investment_amount
				  ,remarks
				  ,supplier_id
				  ,principal_amount
				FROM m_investment_header 
				WHERE supplier_id='$supplier_id'
					AND (action_code IS NULL OR action_code='' OR maturity_date > '$transaction_date') 
					AND placement_date <= '$transaction_date'
					AND (or_date IS NULL OR or_date='' OR or_date > '$transaction_date') 
					AND status_flag='2'
				ORDER BY investment_no ASC";
  		
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);	
    }
    
	function getSuppliersForReport($transaction_date)
    {
    	$sql = "SELECT DISTINCT(supplier_id) 
				FROM m_investment_header 
				WHERE (action_code IS NULL OR action_code='' OR maturity_date > '$transaction_date') 
					AND placement_date <= '$transaction_date'
					AND (or_date IS NULL OR or_date='' OR or_date > '$transaction_date') 
					AND status_flag='2'
				ORDER BY supplier_id ASC";
  		
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);		
    }

	function retrieveMaturityProoflist($or_date)
	{
		$sql = "SELECT investment_no
				  ,placement_date
				  ,maturity_date
				  ,principal_amount
				  ,interest_rate
				  ,interest_amount
				  ,interest_income
				  ,or_no
				  ,or_date
				  ,remarks
				  ,supplier_id
				FROM m_investment_header 
				WHERE (action_code IS NOT NULL OR action_code!='')
					AND or_date='$or_date' 
				ORDER BY investment_no ASC";
  		
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);	
	}
	
	function retrieveMatureInvestmentTransactions($currdate){
 		$this->db->select('i.*, rt.gl_code, rt.with_or');
    	$this->db->from('m_investment_header AS i');
    	$this->db->join('r_transaction AS rt', 'i.maturity_code = rt.transaction_code', 'inner');
    	$this->db->where(array('i.status_flag' => '2', 'processed' => '0', 'transaction_date' => $currdate));		
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
		//echo $query;
    	$count = $this->db->count_all($this->table_name);  
  		
		return $this->checkError($result->result_array(), $count, $query);
    }
	
	// function retrieveMatureInvestmentTransactions($currdate){
   	// $sql = "SELECT ti.*, rt.gl_code							
				// FROM m_investment_header ti									
				// INNER JOIN r_transaction rt									
					// ON ti.maturity_code = rt.transaction_code								
				// WHERE ti.status_flag = '2'
					// AND processed = '0'
					// AND transaction_date = '{$currdate}'
				// FOR UPDATE";
		
   	// $result = $this->db->query($sql);
	// $query = $this->db->last_query();
	// $count = $result->num_rows();
 
   	//echo $this->db->last_query();
   	// return $this->checkError($result->result_array(), $count, $query);
   // }
   
   function generateOR($currdate = ''){
		
		$sql = "SELECT mh.or_date
					, mh.or_no
					, mh.supplier_id
					, ms.supplier_name as first_name
					, '' as last_name #nikas
					, mh.investment_amount
					, mh.interest_amount
					, mh.action_code
				FROM m_investment_header mh
				INNER JOIN m_supplier ms
					ON(ms.supplier_id = mh.supplier_id)
				INNER JOIN r_transaction rt															
					ON rt.transaction_code = mh.maturity_code
				WHERE mh.or_date = '$currdate'
					AND rt.with_or = 'Y'
					AND mh.status_flag = '2'
				ORDER BY ms.supplier_name";
    	
    	$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
    	
    	return $this->checkError($result->result_array(),$count,$query);
	}
    
}

?>
