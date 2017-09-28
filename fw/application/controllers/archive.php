<?php
class Archive extends Asi_controller{
	
	var $db1;
	var $db2;
	
	function Archive(){
		parent::Asi_controller();
		$this->load->model('Db_archive_model');
		$this->load->helper('url');
		$this->load->library('constants');
		$this->load->model('Parameter_model');
	}
	
	function index(){	    
			
	}
	
	function process(){
		set_time_limit(0); //runs forever
		ini_set("memory_limit","1500M");
		
		log_message('debug', 'Archive start...');
		
		$result = 0;
	    
		$result += $this->processDetail();
		$result += $this->processHeader();
		
		//result is not 0 then error in archiving
		if ($result > 0){
			echo "{'success':false,'msg':'5 year-old data archiving failed.'}";
		} else{
			echo "{'success':true,'msg':'5 year-old data successfully archived.'}";
		}
		 
		log_message('debug', 'Archive done...');
	}
	
	function processHeader(){
		$error = 0;
		$date = $this->Parameter_model->retrieveValue('CURRDATE');
		foreach($this->constants->tables_archive as $table=>$field){
			log_message('debug', "Archive for table $table start...");
			$this->Db_archive_model->table_name = $table;
			
			$archive_date = date("Ymd", strtotime("-" . $this->constants->archive_span . " year", strtotime($date)));
			$archive_year = substr($archive_date, 0, 4);
			
			$this->Db_archive_model->archive_date = ($archive_year + 1) . '0101';
			$archive_result = $this->Db_archive_model->archiveHeader();
			
			if (!$archive_result){
				++$error;
			}			
			log_message('debug', "Archive for table $table done!");
		}
		
		return $error;
		 
	}
	
	function processDetail(){
		$error = 0;
		$date = $this->Parameter_model->retrieveValue('CURRDATE');
		foreach($this->constants->tables_archive2 as $detail=>$details){
			log_message('debug', "Archive for table $detail start...");
			$this->Db_archive_model->table_name = $detail;
			
			$archive_date = date("Ymd", strtotime("-" . $this->constants->archive_span . " year", strtotime($date)));
			$archive_year = substr($archive_date, 0, 4);
			
			$this->Db_archive_model->archive_date = ($archive_year + 1) . '0101';
			$archive_result = $this->Db_archive_model->archiveDetail($details['header'], $details['pk']);
			
			if (!$archive_result){
				++$error;
			}			
			log_message('debug', "Archive for table $detail done!");
		}
		
		return $error;
		 
	}
	
	
}

?>