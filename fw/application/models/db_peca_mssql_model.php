<?php

class Db_peca_mssql_model extends Asi_Model {
	
	
    function Db_peca_mssql_model()
    {
        parent::Asi_Model();      
        $this->peca = $this->load->database('peca', TRUE);
        $this->table_name = '';
        $this->load->library('constants');
        $this->load->helper('file');
    }
    
	function get_database_group() {
		return 'peca';
	}
	
	function getTableData()
	{
		$query = '';
		$data = array();
		
		$query = $this->buildQuery();
		
		$data = $this->getQueryResults($query);
		
		return $data;
		
	}
	
	/**
	 * function that gets query return results
	 * @param $query
	 * @return unknown_type
	 */
	function getQueryResults($query)
	{
		
		$data = array();
		
		log_message('debug',$query);
		
		$objCon = $this->peca->query($query);
//		echo $this->peca->last_query();

		if($objCon->num_rows() > 0)
		{			
			while ($row = $objCon->_fetch_assoc())
			{
				$data[] = $row;
			}		  
		}
		$objCon->free_result();
		return $data;
		
	}
	
	/**
	 * Function that gets unique column rows
	 * in tables. Used in batch processing
	 * @param $columnName	ex. employee_id 
	 * @return unknown_type
	 */
	function getLimitingColumnRows($columnName){
		$query = '';
		$data = array();
		
		$query = 'SELECT DISTINCT '. $columnName . ' FROM '. $this->table_name . ' ORDER BY '.$columnName . ' ASC';
		
		$data = $this->getQueryResults($query);
		
		return $data;
	}
	
	/**
	 * Function that queries database
	 * for batch processes 
	 * @param $columnName	the limiting column name (ex. employee_id)
	 * @param $columnVal	the limiting column value (ex.'USER0001')
	 * @return unknown_type
	 */
	function getBatchData($columnName, $columnVal){
		$query = '';
		$data = array();
		
		$query = $this->buildQuery();
		
		$query .= ' WHERE ' .$columnName." = '$columnVal'";
		
		$data = $this->getQueryResults($query);
		
		return $data;
	}
	
	/**
	 * Function that builds select statement complete
	 * with column aliases set from constant file
	 * @return unknown_type
	 */
	function buildQuery(){
		
		$query = '';
		
		if(isset($this->constants->tables[$this->table_name])){
		 	$query = 'SELECT ';
		
			foreach($this->constants->tables[$this->table_name] as $key => $value){
				
				$query .= ' '. $value .' AS ' . $key . ',';
			}
			$query = rtrim($query,',');
		
			$query .= ' FROM ' . $this->table_name;
		}
		return $query;		
	}

}
?>