<?php

class BackupDB extends Asi_Controller {

	function BackupDB()
	{
		parent::Asi_Controller();
		$this->load->model('parameter_model');
		$this->load->helper('file');
		$this->load->library('constants');
	}
	
	function index(){
	}
	
	/**
	 * @desc Backups the database
	 */
	function backup(){
		set_time_limit(0);
		$mysqlbin_dir = $this->constants->mysqlbin_dir;
		$currDate = date("Ymd", strtotime($this->parameter_model->getParam('CURRDATE')));
		$currTime = date("His");
		$allowedBackups = $this->parameter_model->getParam('ADBPD');
		if($allowedBackups==""){
			$allowedBackups = 0;
		}
		$dbName = $this->db->database;
		$dbUsername = $this->db->username;
		$dbPassword = $this->db->password;
		$req_path = trim($_REQUEST['file_path']);
		$path_parts = pathinfo($req_path);
		$dirPath = $path_parts['dirname'];
		$fileName = $path_parts['filename'];
		
		if(is_dir($req_path)){ //if file path only contains directory
			if(is_dir($dirPath."/".$fileName)) //check if path is this format <drive>:/<folder>
				$dirPath = $dirPath."/".$fileName;
			$fileName = $dbName;
			
		}		
		$fileName = $fileName."_".$currDate.$currTime.'.sql';
			
		if(file_exists($dirPath)&&$dirPath!="\\"&&$dirPath!="."){	
			if($allowedBackups!=0){
				$backupsToday = $this->getbackupsToday($dirPath, $currDate);
				if($backupsToday['count']>=$allowedBackups){
					$this->deleteOldest($dirPath, $backupsToday['files']);  
				}								
			}		
			
			$command = "$mysqlbin_dir"."mysqldump -u $dbUsername ";
			if($dbPassword!="")
				$command .= "-p$dbPassword "; 
			$command .= "$dbName > \"$dirPath"."/"."$fileName" . "\"";
			
			// echo "{'success':false,'msg':'$command','error_code':'39'}";
			// log_message('debug', $command);
			
			exec($command);
			log_message('debug', $command);
			if(!file_exists($dirPath."/".$fileName)){
				echo "{'success':false,'msg':'Backup file not successfully saved.','error_code':'39'}";
			}
			else{
				echo "{'success':true,'msg':'Backup file successfully saved.','error_code':'40'}";
			}
			
			$this->delete($dirPath, $currDate);
		}
		else echo "{'success':false,'msg':'Backup failed, invalid path.','error_code':'38'}";
	}
	
	/**
	 * @desc Deletes all backups which have exceeded the number of retainment days 
	 */
	function delete($dirPath, $currdate){
		$dbRetainmentDays = $this->parameter_model->getParam('DBRD');
		if($dbRetainmentDays==""){
			$dbRetainmentDays = 5;
		}
		$currDate  = $this->parameter_model->getParam('CURRDATE');
		$folders = array();
		$dirContents = scandir($dirPath);
		foreach($dirContents as $dirItem){ 
			if(preg_match("/.+_(19|20)[0-9]{2}(0[1-9]|1[012])(0[1-9]|[12][0-9]|3[01])(0[1-9]|1[0-9]|2[0123])([0-5][0-9])([0-5][0-9])[.]sql/",$dirItem)
			&& date("Ymd", strtotime(substr($dirItem,strrpos($dirItem, "_")+1,8)."+$dbRetainmentDays days"))< $currdate){ 	
				unlink($dirPath."/".$dirItem);
			}	
		}
	}
	
	function getBackupsToday($dir_path, $currdate){
		$dirContents = scandir($dir_path);
		$files = array();
		foreach($dirContents as $dirItem){
			if(preg_match("/.+_(19|20)[0-9]{2}(0[1-9]|1[012])(0[1-9]|[12][0-9]|3[01])(0[1-9]|1[0-9]|2[0123])([0-5][0-9])([0-5][0-9])[.]sql/",$dirItem)
			&& substr($dirItem,strrpos($dirItem, "_")+1,8)== $currdate)
			
			$files[] = $dirItem;
		}	
			
		return array(
			'count' => count($files)
			,'files' => $files
			); 
	}
	
	function deleteOldest($dir_path, $files){
		foreach($files as $file){
			$data[] = substr($file,strrpos($file, "_")+1,14);
		}
		
		$minIndex = array_search(min($data), $data);
		unlink($dir_path."/".$files[$minIndex]);
	}
		
}

/* End of file backupDB.php */
/* Location: ./PECA/application/controllers/backupDB.php */