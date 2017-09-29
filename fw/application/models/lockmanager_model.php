<?php

class Lockmanager_model extends Asi_Model {

	var $user = "";
	var $span = 5;
	
    function Lockmanager_model(){
        parent::Asi_Model();
		$this->table_name = 'i_lock_manager';
        $this->id = 'function_id';
        $this->date_columns = array('');
    }
    
    function setValue($col_name, $col_value){
		$this->model_data[$col_name] = date('Ymdhis', strtotime("+" . $this->span . " minute"));
	}
	
	//$key : function_id, $span: time in minutes to prevent deadlock
	function acquire($key = "", $span = 5){
		$this->span = $span;
		$this->lock();
		log_message('debug', "Acquire key {$key} starts here...");
		$result = $this->get("function_id = '{$key}'", "key");
		$this->populate(array('function_id'=>$key
								, 'key'=>date('Ymdhis', strtotime("+{$span} minute"))
								, 'created_by' => $this->user
								, 'modified_by' => $this->user));
		
		$data = $result['list'];
		if ($result['count'] < 1){
			//need to insert here
			log_message('debug', 'Insert key here...');
			$this->insert();			
			$this->unLock();
			return true;
		} 
		
		$last_time = $data[0]['key'];
		if ($last_time === ""){
			//"" means released, so we can use
			log_message('debug', 'Good we can use...');
			$this->update();	
			$this->unLock();
			return true;			
		}
		
		//check for key if value is already past the current time
		//$time = date('Ymdhis',strtotime("+{$span} minute", $last_time));
		//$time = date('Ymdhis',strtotime("+{$span} minute", strtotime($last_time)));
		if ($last_time <= date('Ymdhis')){
			//if time already beyond 5 minutes, then refresh
			log_message('debug', "deadlock {$last_time}, need to update...");
			$this->update();
			$this->unLock();
			return true;
		}
		
		
		$this->unLock();
		log_message('debug', 'Engine Busy...');
		return false;		
	}
	
	function release($key = ""){
		$this->lock();
		log_message('debug', "Release key {$key}...");
		$this->populate(array('function_id'=>$key, 'key'=>""));
		$this->update();
		$this->unLock();
	}

}
?>