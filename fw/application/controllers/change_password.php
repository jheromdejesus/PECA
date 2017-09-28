<?php

class Change_password extends Asi_Controller {
	

	function Change_password()
	{
		parent::Asi_Controller();
		$this->load->model('User_model');
		$this->load->model('Userpasswords_model');
		$this->load->library('constants');
		$this->load->helper('misc');
		
	}
	
	function index()
	{			
	}
	
	function show()
	{
		if (array_key_exists('user',$_REQUEST)) {
				$data = $this->User_model->getUserData();
		}
	}
	
	function update()
	{		
		
		log_message('debug', "[START] Controller group:update");
		log_message('debug', "group param exist?:".array_key_exists('user',$_REQUEST));
		
		$this->show();
		
		if(!$this->validateData()){
			return;
		}
		
		if (array_key_exists('user',$_REQUEST)) {
			$this->encryptNewPassword();
			if($this->isInLastFivePasswords()){
				display_message('false','You cannot use your last 5 passwords.');
			}
			else{
				$this->db->trans_start();
				$this->User_model->populate($_REQUEST['user']);
				$result = $this->User_model->update();
				
				$checkDuplicate = $this->Userpasswords_model->checkDuplicateKeyEntry(array(
					'user_id' => $_REQUEST['user']['user_id']
					,'password' => $_REQUEST['user']['password']));
				
				$this->Userpasswords_model->populate($_REQUEST['user']);
				$this->Userpasswords_model->setValue('status_flag', 1);
				
				if($checkDuplicate['error_code']==1){
					$this->Userpasswords_model->setValue('modified_by', $_REQUEST['user']['user_id']);
					$this->Userpasswords_model->update(array(
						'user_id' => $_REQUEST['user']['user_id']
						,'password' => $_REQUEST['user']['password']));
				}
				else{
					$this->Userpasswords_model->setValue('created_by', $_REQUEST['user']['user_id']);
					$this->Userpasswords_model->insert();
				}
				$this->db->trans_complete();
				
				if($this->db->trans_status() === TRUE){
					display_message('true',$this->constants->messages['129']);
				} else{
					display_message('false','Data NOT successfully saved.');
				}
			}
		} else{
			 display_message('false',$this->constants->messages['130']);
		}
			
			
		log_message('debug', "[END] Controller User:update");
	}
	
	function isInLastFivePasswords(){
		$user_id = $_REQUEST['user']['user_id'];
		$password = $_REQUEST['user']['password'];
		
		$data = $this->Userpasswords_model->getLastFivePasswords($user_id);
		foreach($data['list'] as $item){
			if($item['password'] == $password){
				return true;
			}
		}
		return false;
	}
	
	function encryptNewPassword()
	{
		if (array_key_exists('user_cp',$_REQUEST)) {
			$newpass = md5($_REQUEST['user_cp']['newpass']);
			$_REQUEST['user']['password'] = $newpass;
			
		}else{
				echo "{'success':false,'msg':'Data was NOT successfully encrypted.'}";
		}
	}
	
	function compareOldPassword()
	{
		 $oldpass = md5($_REQUEST['user_cp']['oldpass']);
		
		 if(strcmp($this->User_model->getValue('password'),$oldpass) != 0){
		 	echo "{success:false,errors:[{id:'user_cp[oldpass]'}],msg:'".$this->constants->messages['128']."'}";		 		
		 	return false;
		 }
		return true;
	}
	
	
	function validateData()
	{
		if(!$this->compareOldPassword()){
			return false;
		}
	
		if (!array_key_exists('user',$_REQUEST)) {
			display_message('false',$this->constants->messages['124']);
			return false;
		}
		
		if(empty($_REQUEST['user_cp']['oldpass'])){
			echo "{success:false,errors:[{id:'user_cp[oldpass]'}],msg:'".$this->constants->messages['119']."'}";		 		
			return false;
		}
		
		if(empty($_REQUEST['user_cp']['newpass'])){
			echo "{success:false,errors:[{id:'user_cp[newpass]'}],msg:'".$this->constants->messages['120']."'}";		 		
			return false;
		}
		
		if(empty($_REQUEST['user_cp']['confirmpass'])){
			echo "{success:false,errors:[{id:'user_cp[confirmpass]'}],msg:'".$this->constants->messages['121']."'}";		 		
			return false;
		}
		return true;
	}
	
}

/* End of file user.php */
/* Location: ./system/application/controllers/user.php */