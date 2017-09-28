<?php

class Permissions extends Asi_Controller {
	
	var $userFunctionList = array();
	var $ar_permittedFunctions;
	var $ar_availableFunctions;
	
	
	function Permissions()
	{
		parent::Asi_Controller(false);
		$this->load->model(array('Group_model','User_model'));
		$this->load->library('constants');
		
	}
	
	function index()
	{		
		log_message('debug', "[START] Controller Group:index");
		
		$this->Group_model->getGroups();
		$this->User_model->getUsers();
		$this->getFunctions();
		
		$this->init();
		$this->show();
		log_message('debug', "[END] Controller User:index");		
	}
	
	function show()
	{
		
		if (array_key_exists('permission',$_REQUEST)) {
		
			if($_REQUEST['permission']['rdoOption'] == 'USERS'){
				$_REQUEST['user']['user_id'] = $_REQUEST['permission']['code'];
				$this->User_model->getUserData();
				$this->setPermissionFunction($this->User_model->getValue('permission'));
				
			}else{
				$_REQUEST['group']['group_id'] = $_REQUEST['permission']['code'];
				$this->Group_model->getGroupData();
				$this->setPermissionFunction($this->Group_model->getValue('permission'));
			}
					echo "{'success':true,'msg':'Data was NOT successfully RETRIEVED.'}";
			
		}else{
			echo "{'success':false,'msg':'Data was NOT successfully RETRIEVED.'}";
		}
		
	}
	
	function update()
	{
		
		if (array_key_exists('permission',$_REQUEST)) {
			$this->ar_permittedFunctions = (json_decode(stripslashes($_REQUEST['permission']['functions'])));	

			if($_REQUEST['rdoOption'] == 'USERS'){
				$this->updateUserPermission();
			}else{
				$this->updateGroupPermission();
			}
		}else
			echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
			
	}
	
	function getFunctions()
	{
		$data = $this->constants->create_list($this->constants->functions);
		
		echo json_encode(array(
            'data' => $data
        )); 
	}
	
	function setAvailableFunctions($var)
	{
	    return($var == 0);		
	}
	
	function setPermittedFunctions($var)
	{
	   return($var == 1);
	}
	
	function getAvailableFunctions()
	{
		
		if (array_key_exists('permission',$_REQUEST)) {
			$this->setPermissionFunction($_REQUEST['permission']);
		 
		 $data =  array_filter($this->userFunctionList, array('Permissions', 'setAvailableFunctions'));

		
		$this->ar_availableFunctions = $this->convertToMulti($data);
	
		 
		}else{
			echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
		}
			
		echo json_encode(array(
				'success' => true,
	            'data' => $this->ar_availableFunctions,
	        ));
		
	}
	
	function convertToMulti($data)
	{
		//$ictr = 0;
		$ar = array();
		
		if(count($data) > 0){	
					
				foreach ($data as $key => $value)
				{
					
					$ar[] = array('function_name' => $key
								  , 'function_value' => $value
								  , 'function_idx' => array_search($key,$this->constants->functions));
					//$ictr ++;
				}	
		}
		return $ar;
	}
	
	function getPermittedFunctions()
	{
		
        if (array_key_exists('permission',$_REQUEST)) {
			$this->setPermissionFunction($_REQUEST['permission']);
		 
		 $data =  array_filter($this->userFunctionList, array('Permissions', 'setPermittedFunctions'));
			
		$this->ar_permittedFunctions= $this->convertToMulti($data);
	
		 
		}else{
			echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
		}
			
		 echo json_encode(array(
				'success' => true,
	            'data' => $this->ar_permittedFunctions,
	        ));
			
		 
		 
	}
	
	function setPermissionFunction($strPermission = '')
	{
		// Split the permission string into an array
		$perm = strlen($strPermission) <= 1 ? str_split($this->constants->defaultPermission) : str_split($strPermission);
		$this->userFunctionList = array_combine($this->constants->functions, $perm );

	}
	
	function updateUserPermission()
	{		
		$strPermission = $this->getPermissionString();
		
		if (array_key_exists('permission',$_REQUEST)) {
			$_REQUEST['user']['user_id'] = $_REQUEST['permission']['user_group_id'];
			$_REQUEST['user']['permission'] = $strPermission;
			
			$this->User_model->populate($_REQUEST['user']);
			$result = $this->User_model->update();
			
			if($result['error_code'] == 0){
				display_message('true',$this->constants->messages['132']);
			} else
				display_message('false',$result['error_message']);
		} else
			display_message('false',$this->constants->messages['133']);
			
		log_message('debug', "[END] Controller User:update");
	}
	
	function updateGroupPermission()
	{	
		$strPermission = $this->getPermissionString();
		
		if (array_key_exists('permission',$_REQUEST)) {
			
			$_REQUEST['group']['group_id'] = $_REQUEST['permission']['user_group_id'];
			$_REQUEST['group']['permission'] = $strPermission;
			$this->Group_model->populate($_REQUEST['group']);
			$result = $this->Group_model->update();
			
			if($result['error_code'] == 0){
				display_message('true',$this->constants->messages['134']);
			} else
				display_message('false',$result['error_message']);
		} else
			display_message('false',$this->constants->messages['135']);
			
		log_message('debug', "[END] Controller User:update");
	}
	
	function getPermissionString()
	{
		
		$strPermission = $this->constants->defaultPermission;
		$count = count($this->ar_permittedFunctions);
		
		if($count > 0){
				foreach($this->ar_permittedFunctions as $key => $value) {
	    			
	    				$strPermission = substr_replace($strPermission, "1", $value, 1) ;
	    			
				}
			return $strPermission;
		}
		return  $this->constants->defaultPermission;
	}
}

/* End of file user.php */
/* Location: ./system/application/controllers/user.php */