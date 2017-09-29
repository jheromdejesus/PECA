<?php
/*
 * Created on Jun 5, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Db_archive_model extends Asi_Model {
	
	var $archive_db = "";
	var $table_name = "";
	var $archive_date = "";
	
    function Db_archive_model(){
        parent::Asi_Model();     
        $this->load->library('constants');
        $this->archive_db = "peca_archive"; 
    }
    
    function archiveHeader(){
    	
    	try{
    		$this->db->trans_begin();
    		
    		$sql = "INSERT INTO " . $this->archive_db . "." . $this->table_name 
    				. "(" . $this->buildColumns() . ")"
    				. " SELECT " . $this->buildColumns() 
    				. " FROM " . $this->table_name 
    				. " " . $this->buildWhere();
					
			// log_message('debug', $sql);
    		
	    	$result = $this->db->query($sql);
			log_message('debug', $this->db->last_query());
	    	
	    	if (!$result){
	    		throw new Exception($this->db->_error_message());
	    	}
	    	
	    	$sql = "DELETE FROM " . $this->table_name . " " . $this->buildWhere();
			// log_message('debug', $sql);
			$result = $this->db->query($sql);
			
			log_message('debug', $this->db->last_query());
	    	
	    	if (!$result){
	    		throw new Exception($this->db->_error_message());
	    	}
	    	
	    	// echo "Archiving done for table " . $this->table_name . ".</br>";
	    	
	    	$this->db->trans_commit();
	    	return true;
	    	
    	} catch(Exception $e){
    		$this->db->trans_rollback();
    		// echo $e->getMessage() . '</br>' . $this->db->last_query();
    		return false;
    	}
    	
    	
    }
    
    function archiveDetail($header = "", $pk = array()){
    	
    	try{
    		$this->db->trans_begin();
    		
    		$on_stmt = "";
    		$on_stmt_arr = array();
    		
    		//build on statement
    		foreach ($pk as $value){
    			$on_stmt_arr[] = $header . "." . $value . '=' . $this->table_name . "." . $value;
    		}
    		
    		$on_stmt = implode(" AND ", $on_stmt_arr);    		
    		
    		$sql = "INSERT INTO " . $this->archive_db . "." . $this->table_name 
    				. "(" . $this->buildColumns() . ")"
    				. " SELECT " . $this->buildColumns(true) 
    				. " FROM " . $this->table_name
    				. " INNER JOIN " . $header
    				. " ON " . $on_stmt
    				. " " . $this->buildWhere($header);
					
			// log_message('debug', $sql);
    		
	    	$result = $this->db->query($sql);
			log_message('debug', $this->db->last_query());
	    	
	    	if (!$result){
	    		throw new Exception($this->db->_error_message());
	    	}
	    	
	    	$sql = "DELETE " . $this->table_name
	    			. " FROM " . $this->table_name
    				. " INNER JOIN " . $header
    				. " ON " . $on_stmt
    				. " " . $this->buildWhere($header);		
					
			// log_message('debug', $sql);
			
			$result = $this->db->query($sql);	
			log_message('debug', $this->db->last_query());			
	    	if (!$result){
	    		throw new Exception($this->db->_error_message());
	    	}
	    	
	    	// echo "Archiving done for table " . $this->table_name . ".</br>";
	    	
	    	$this->db->trans_commit();
	    	return true;
	    	
    	} catch(Exception $e){
    		$this->db->trans_rollback();
    		// echo $e->getMessage() . '</br>' . $this->db->last_query();
    		return false;
    	}
    	
    	
    }
    
    function buildColumns($join = false){		
		$query = '';
		
		if(isset($this->constants->tables2[$this->table_name])){
			foreach($this->constants->tables2[$this->table_name] as $key => $value){	
				if ($join){
					$query .= ' ' . $this->table_name . '.' . $key . ',';
				} else{
					$query .= ' ' . $key . ',';
				}		
			}
			$query = rtrim($query,',');
		}
		return $query;		
	}
	
	function buildWhere($header = ""){
		$query = '';
		
		$table = ($header == "") ? $this->table_name : $header;
		
		if(isset($this->constants->tables_archive[$table])){
			$field = $this->constants->tables_archive[$table];
		 	$query = 'WHERE ' . $table . "." . $field . " < '" . $this->archive_date . "'";
		 	
		 	if ($table == 'm_loan'){
		 		$query .= " AND close_flag = 1";
		 	}		
		}
		return $query;				
	}
	

}
?>
