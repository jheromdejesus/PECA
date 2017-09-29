<?php

class Db_peca_mysql_model extends Asi_Model {
	
	var $dataToCopy =  array();
	var $mssqlTBLName = '';
	
    function Db_peca_mysql_model()
    {
        parent::Asi_Model();      
        $this->table_name = '';
        $this->id = '';       
        $this->load->library('constants');
        $this->load->helper('file');
      
    }   
    
    /**
     * Function that prepares INSERT statement and call
     * write to .sql file function
     * @return unknown_type
     */
	function outputToSQLFile(){
		
		if(!isset($this->constants->tables2[$this->table_name])){
			return;
		}				
		if(count($this->dataToCopy) <= 0){
			return;
		}
	
		$sql = 'INSERT INTO '.  $this->table_name .' (';	
		$cols = $this->getColumnNames();		
		$sql .= $cols . ') VALUES ';
		$sql .= $this->getColumnValues(). ';  
		';
		$this->writeToSQLFile($sql);
		
	}
	
 	/**
     * Function that prepares INSERT statement and call
     * write to .sql file function for batch process
     * @return unknown_type
     */
//	function processBatchInsert(){
//		
//		$count = count($this->dataToCopy);
//		
//		if(!isset($this->constants->tables2[$this->table_name])){
//			return;
//		}				
//		if($count <= 0){
//			return;
//		}
//		
//		if( $count > 0){
//			for ( $row = 0; $row < $count; $row++ ){
//				
//				$ar = $this->dataToCopy[$row];
//				
//				$sql = 'INSERT INTO '.  $this->table_name .' (';	
//				$cols = $this->getColumnNames();		
//				$sql .= $cols . ') VALUES ';			
//				
//				array_walk($ar,array($this,'addDelimeter')); 
//				$sql .= "(".implode(",",$ar);
//				$sql .= ");
//				";
//				
//				$this->writeToSQLFile($sql);
//			}
//		}	
//	
//	}

	function processBatchInsert(){

		$count = count($this->dataToCopy);

		if(!isset($this->constants->tables2[$this->table_name])){
			return;
		}
		if($count <= 0){
			return;
		}

		if( $count > 0){
			$sql = 'INSERT INTO '.  $this->table_name .' (';
			$cols = $this->getColumnNames();
			$sql .= $cols . ') VALUES ';
			for ( $row = 0; $row < $count; $row++ ){

				$ar = $this->dataToCopy[$row];

				array_walk($ar,array($this,'addDelimeter'));
				$sql .= "(".implode(",",$ar);
				$sql .= "),";
			}
			$sql  = rtrim($sql,',') . ";
			";
			$this->writeToSQLFile($sql);
		}

	}
	
	/**
	 * Function that gets column names for each table
	 * from constant file
	 * @return unknown_type
	 */
	function getColumnNames(){
		return implode(",",array_keys($this->constants->tables[$this->mssqlTBLName]));
	}
	
	/**
	 * Function that adds delimeter to each column values 
	 * depending on the column data type defined in constant file
	 * @param $array
	 * @param $key
	 * @return unknown_type
	 */
	
	function addDelimeter(&$array,$key){
	
		if($this->constants->tables2[$this->table_name][$key] == 'string'){
		    $array = "'".addslashes($array)."'";		    
		}
	}
	
	/**
	 * Function that gets delimeted column values per row
	 * 
	 * @return unknown_type
	 */
	function getColumnValues(){
		
		$sql = '';
		$ar = array();
		
		$count = count($this->dataToCopy);
		
		
		if( $count > 0){
			for ( $row = 0; $row < $count; $row++ ){	
				
				$ar = $this->dataToCopy[$row];
				array_walk($ar,array($this,'addDelimeter')); 
				$sql .= "(".implode(",",$ar);
				$sql .= "),";
				
			}		
			$sql  = rtrim($sql,',');
		}	
		return $sql;	
	}	
	
	/**
	 * function that writes data to .sql file
	 * this will create a file if the file does not exists
	 * and will append data if otherwise
	 * creates temp_dir folder in codeIgniter folder
	 * @param $data
	 * @return unknown_type
	 */
	function writeToSQLFIle($data){
	
   
			if ( ! write_file($this->constants->dumpDirectoryFileName, $data,'a+'))
			{
			     echo 'Unable to write the file for table: '. $this->table_name . "<br>";
			}
			
			$data = ''; //empty string
			/*else
			{
			     echo ' File written for table: '. $this->table_name . "<br>";
			}*/
	}
  
}
?>