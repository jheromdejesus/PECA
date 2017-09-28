<?php

class Group extends Asi_Controller {

	function Group()
	{
		parent::Asi_Controller();
		$this->load->model('Group_model');
		$this->load->library('constants');
		
	}
	
	function index()
	{		
		/*log_message('debug', "[START] Controller Group:index");
		
		$this->read();
		
		log_message('debug', "[END] Controller User:index");	*/	
	}
	
	function show()
	{
		log_message('debug', "[START] Controller Group:show()");
		$data = $this->Group_model->getGroupData();
		
		echo json_encode(array(
			'success' => true,
            'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query'],
        ));
     	
        log_message('debug', "[END] Controller Group:show()");		
	}
	
	function read()
	{
		$this->Group_model->getGroups();
	}
	
	function add()
	{

		log_message('debug', "[START] Controller group:add");
		log_message('debug', "Group param exist?:".array_key_exists('group',$_REQUEST));
		
		if(!$this->validateData()){
			return;
		}
		
		if (array_key_exists('group',$_REQUEST)) {
			$this->Group_model->populate($_REQUEST['group']);
			
			$checkDuplicate = $this->Group_model->checkDuplicateKeyEntry();
	
			if($checkDuplicate['error_code'] == 1){
				$result['error_code'] = 1;
				$result['error_message'] = $checkDuplicate['error_message'];
			}
			else{
				$result = $this->Group_model->insert(); 
			} 
						
			if($result['error_code'] == 0) {
				echo "{success:true, msg:'".$this->constants->messages['106']."'}";
	        } else
				echo '{success:false, msg:"'.$result['error_message'].'"}';
		} else
			echo "{success:false, msg:'".$this->constants->messages['109']."'}";
			
		log_message('debug', "[END] Controller group:add");
	}
	
	function update()
	{		
	
		log_message('debug', "[START] Controller group:update");
		log_message('debug', "group param exist?:".array_key_exists('group',$_REQUEST));
		
		if (array_key_exists('group',$_REQUEST)) {
			$this->Group_model->populate($_REQUEST['group']);
			$this->Group_model->setValue('status_flag', '1');
			$result = $this->Group_model->update();
			if($result['error_code'] == 0){
				echo "{'success':true,'msg':'".$this->constants->messages['107']."'}";
			} else
				echo '{"success":false,"msg":"'.$result['error_message'].'"}';
		} else
			echo "{'success':false,'msg':'".$this->constants->messages['110']."'}";
			
		log_message('debug', "[END] Controller User:update");
	}
	
	function delete()
	{		
	
		log_message('debug', "GROUP DELETE GROUP DELETE GROUP DELETE");
		log_message('debug', "[START] Controller Group:delete");
		//log_message('debug', "group param exist?:".array_key_exists('group',$_REQUEST));
		
		if (array_key_exists('group',$_REQUEST)) {
			$this->Group_model->populate($_REQUEST['group']);
			$this->Group_model->setValue('status_flag', '0');
			$result = $this->Group_model->update();
			
			if($result['error_code'] == 0){
				echo "{'success':true,'msg':'".$this->constants->messages['108']."'}";
			} else
				echo '{"success":false,"msg":"'.$result['error_message'].'"}';
		} else
			echo "{'success':false,'msg':'".$this->constants->messages['111']."'}";
			
		log_message('debug', "[END] Controller Group:delete");
	}
	
	function initData()
	{
		//for test start
			//for test vars start
		$_REQUEST['group']['group_id'] = 'CANDY';
		$_REQUEST['group']['group_name'] = 'CANDY';
		$_REQUEST['group']['permission'] =  $this->constants->defaultPermission;
		
	}
	
	function validateData()
	{
		if (!array_key_exists('group',$_REQUEST)) {
			display_message('false',$this->constants->messages['127']);
			return false;
		}
		
		if(empty($_REQUEST['group']['group_id'])){
			display_message('false',$this->constants->messages['125']);
			return false;
		}
		
		if(empty($_REQUEST['group']['group_name'])){
			display_message('false',$this->constants->messages['126']);
			return false;
		}
		return true;
	}
	
}

/* End of file user.php */
/* Location: ./system/application/controllers/user.php */