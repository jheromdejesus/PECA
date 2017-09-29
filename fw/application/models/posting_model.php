<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Posting_model extends Asi_Model {
	
    function Posting_model()
    {
        parent::Asi_Model();
        $this->table_name = 't_posting';
        $this->id = 'accounting_period';
        $this->date_columns = array('');
    }
    
    //override methods since no created and modified date
    function update($cond =null){
		
		if($cond != null){
			$this->db->where($cond);
		}else{
			$this->db->where($this->id, $this->model_data[$this->id]);
		}
		$this->convertDateColumns();
		
		$this->db->update($this->table_name, $this->model_data);
	
		$query = $this->db->last_query();
		
		return $this->checkError('','',$query);
	}
	
	function insert(){
		$this->convertDateColumns();
		$this->db->insert($this->table_name, $this->model_data);
		return $this->checkError();
	}
    
    function retrieveLastPosting($type){
    	$retval = null;
    	$this->db->select_max('accounting_period');
    	$query = $this->db->get_where($this->table_name, array($type=>1));
    	
    	if ($query->num_rows() > 0){
    		$result = $query->result_array();
    		$retval = $result[0]['accounting_period'];
    	}
    	    	
    	return $retval;
 
    }
    
    /* @param $type : 'capital_contribution' or 'journal'
     * @return $retval : 1 already posted, 0 not yet but acctg period already exist
     * 					-1 acctg period not yet exist 
     */   
    function isPosted($acctg_period, $type){
    	$retval = -1;
    	$result = $this->get(array('accounting_period'=>$acctg_period), $type);
    	$result = $result['list'];
    	
    	if (count($result) > 0){
    		$retval = $result[0][$type];
    	}
    	
    	return $retval;
    }
}

?>
