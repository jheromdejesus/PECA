<?php
class Data_Migrate extends Controller{
	
	var $db1;
	var $db2;
	
	function Data_Migrate(){
		parent::Controller();
		$this->load->model('Db_peca_mysql_model');
		$this->load->model('Db_peca_mssql_model');
		$this->load->helper('url');
		$this->load->library('constants');
	}
	
	function dislayResult(){
		echo 'displayresult';
	}
	
	function index()
	{
	    
		set_time_limit(0); //runs forever
		ini_set("memory_limit","1500M");
		
		echo 'START CREATING DUMP SQL FILE<br>';
	    
		$this->processNonBatch();
		$this->processBatch();
		$this->Db_peca_mysql_model->writeToSQLFIle(' COMMIT;');
		 
		echo 'DUMP SQL FILE SUCCESSFULLY DONE! ';
	
	}
	
	function processNonBatch()
	{
	    $dbRes1 = array();
	    
		foreach($this->constants->tableNames as $key => $value){
			
			 $this->Db_peca_mssql_model->table_name = $key;
			 $this->Db_peca_mysql_model->mssqlTBLName = $key;
			 $this->Db_peca_mysql_model->table_name = $value;
			
			 $dbRes1 = $this->Db_peca_mssql_model->getTableData();
			 
			 $this->Db_peca_mysql_model->dataToCopy = $dbRes1;
			 $this->Db_peca_mysql_model->outputToSQLFile();
			 
			 unset($dbRes1);
			 
			  echo ' File written for table: '. $value . "<br>";
			 
		}
	}
	
	function processBatch()
	{
		$dbRes2 = array();
	    $dbColRes = array();
	    $ar = array();
	    
		foreach($this->constants->BatchTableNames as $key => $value){
			
			 $this->Db_peca_mssql_model->table_name = $key;
			 $this->Db_peca_mysql_model->mssqlTBLName = $key;
			 $this->Db_peca_mysql_model->table_name = $value;
			
			 $dbColRes = $this->Db_peca_mssql_model->getLimitingColumnRows($this->constants->BatchTableLimitCol[$key]);
			
			 $count = count($dbColRes);
		
			 for($row = 0; $row < $count; $row++){
			 	
			   $ar = $dbColRes[$row];
			   $dbRes2 = $this->Db_peca_mssql_model->getBatchData(key($ar),$ar[key($ar)]);
			  
			   $this->Db_peca_mysql_model->dataToCopy = $dbRes2;
			   $this->Db_peca_mysql_model->processBatchInsert();
			   unset($dbRes2);
			 }
			 echo ' File written for table: '. $value . "<br>";
		}
	}
	
	
	
}

?>