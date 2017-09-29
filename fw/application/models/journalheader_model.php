<?php

class Journalheader_model extends Asi_Model {
	var $table_name = 't_journal_header';
	var $id = 'journal_no';
	var $model_data = null;
	var $date_columns = array('accounting_period', 'document_date', 'transaction_date');
	
	
    function Journalheader_model()
    {
        parent::Asi_Model();
    }
	
	 /**
	 * @desc Retrieves list of deleted transactions in a motnh
	 * @return array
	 */
	function getDeletedTransactions($filter = null, $select = null, $orderby = null) 
	{
    	if($select)
    		$this->db->select($select);
    	$this->db->from('mc_journal_header mjh');
    	$this->db->join('mc_journal_detail mjd', 'mjd .journal_no = mjh.journal_no', 'left outer');
		$this->db->join('r_account ra', 'ra.account_no = mjd.account_no', 'left outer');
    	if($filter)	
    		$this->db->where($filter);
    	if($orderby)	
    		$this->db->orderby($orderby);	
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
    	$count = $result->num_rows();
    
		return $this->checkError($result->result_array(), $count, $query);
    }

}
?>