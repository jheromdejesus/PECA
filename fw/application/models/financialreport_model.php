<?php
/*
 * Created on May 25, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Financialreport_model extends Asi_Model {

    function Financialreport_model()
    {
        parent::Asi_Model();
        $this->table_name = 't_journal_header';
        $this->id = 'journal_no';
        $this->date_columns = array('transaction_date');
    }
    
  	/**
	 * @desc Retrieves all FDUJ Entries
	 * @return array
	 */
    function retrieveFDUJEntries($select = null, $filter = null, $limit = null, $offset = null,  $orderby = null) 
  	{
    	if($select)
    		$this->db->select($select);
    	$this->db->from('t_journal_header tjh');
    	$this->db->join('t_journal_detail tjd','tjd.journal_no = tjh.journal_no','LEFT OUTER');
		$this->db->join('r_account ra','ra.account_no = tjd.account_no','LEFT OUTER');
		if($filter)	
    		$this->db->where($filter);
    	
		$clone_db = clone $this->db;
		$count = $clone_db->count_all_results();
		
		if($orderby)	
    		$this->db->orderby($orderby);	
    	$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query(); 
    	
		return $this->checkError($result->result_array(), $count, $query);    
	}
	
}

?>
