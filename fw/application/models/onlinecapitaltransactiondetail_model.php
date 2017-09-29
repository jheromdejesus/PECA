<?php
/*
 * Created on Apr 22, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Onlinecapitaltransactiondetail_model extends Asi_Model {

    function Onlinecapitaltransactiondetail_model()
    {
        parent::Asi_Model();
        $this->table_name = 'o_capital_transaction_detail';
        $this->id = 'request_no';
        $this->date_columns = array('transaction_date');
    }
    
	/**
	 * @desc Retrieves a single online transaction detail
	 * @return array
	 */
    function retrieveOnlineCapitalTransactionDetail($filter = null, $limit = null, $offset = null, $select = null, $orderby = null) 
  	{
    	if($select)
    		$this->db->select($select);
    	$this->db->from($this->table_name);
		
		if($filter)	
			$this->db->where($filter);
    	if($orderby)
			$this->db->orderby($orderby);
		$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query(); 
    	
		//$count = $result->num_rows();
		$count = $this->db->count_all($this->table_name);
		return $this->checkError($result->result_array(), $count, $query);    
	}
}

?>
