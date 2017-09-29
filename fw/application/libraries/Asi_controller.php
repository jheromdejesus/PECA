<?php

class Asi_Controller extends Controller {
	var $auth_key = null;
	var $user_info = null;
	var $employee_id = null;
	function Asi_Controller($auto_auth = true)
	{
		parent::Controller();	
		//ini set
		set_time_limit(10000);
		date_default_timezone_set('Asia/Manila');
		
		$this->load->model('User_model');
		$this->load->model('member_model');
		$this->load->helper('url');
		$this->load->library('constants');
		if ($auto_auth) {
			$this->auth_key = isset($_REQUEST['auth'])?$_REQUEST['auth']:"";
			$this->user_info = $this->_authenticate();
			$this->employee_id = $this->_getEmployeeID($this->user_info['email_address']);
			$this->_checkAccessRights($this->user_info['permission']);
			$this->_checkAccessRightsReports($this->user_info['permission']);
		}
		
		$this->fixOffset();
		
		date_default_timezone_set('Asia/Manila');
	}
	
	function fixOffset(){
		if(isset($_REQUEST["start"])
				&& is_numeric($_REQUEST["start"] ) 
				&& intval($_REQUEST["start"]) < 0){
			$_REQUEST["start"] = '0';
		}
	
	}
	
	
	function _authenticate()
	{
		log_message('debug', "[START] Controller ASI Controller:_authenticate1");
		
		if (array_key_exists('auth',$_REQUEST)) {
			if ($_REQUEST['auth'] === ""){
				log_message('debug', "[END] Controller ASI Controller:_authenticate2");
				redirect('login');
			}
			
			$result = $this->User_model->check_auth($_REQUEST['auth']);
				
			if($result['error_code'] == 0 && $result['affected_rows'] > 0){
				log_message('debug', "[END] Controller ASI Controller:_authenticate3");
				return $result['list'][0];
			} else {
				log_message('debug', "[END] Controller ASI Controller:_authenticate4");
				redirect('login/index/true');
			}
		} else {
			log_message('debug', "[END] Controller ASI Controller:_authenticate5");
			redirect('login');
		}
	}
	
	function _getEmployeeID($email_ad)
	{
		log_message('debug', "[START] Controller ASI Controller:_getEmployeeID");
		$this->load->helper('email');
		if (valid_email($email_ad)){
			$result = $this->member_model->get(
				array('email_address' => $email_ad)
    			,array('employee_id')
    		);	
			if($result['error_code'] == 0 && $result['affected_rows'] > 0){
				log_message('debug', "[END] Controller ASI Controller:_getEmployeeID".$result['list'][0]['employee_id']);
				return $result['list'][0]['employee_id'];
			} else {
				log_message('debug', "[END] Controller ASI Controller:_getEmployeeID");
				return '';
			}
		} else {
			return '';
			log_message('debug', "[END] Controller ASI Controller:_getEmployeeID");
		}
	}
	
	function _checkAccessRights($permission){
		log_message('debug', "[END] Controller ASI Controller:URI".$this->uri->uri_string());
		if(array_key_exists($this->uri->uri_string(), $this->constants->access_rights_uri)){		
			$uri_index = $this->constants->access_rights_uri[$this->uri->uri_string()];
			if( strlen($permission) >= ($uri_index+1) ){
				$permission_index = $permission[$uri_index];
				log_message('debug', "[END] Controller ASI Controller: $permission_index");
				if($permission_index == '0'){
					exit("{'success':false,'msg':'No Access Rights.','error_code':'-1'}");
				}
			}	
		}
	}
	
	function _checkAccessRightsReports($permission){
		//reports with audit trail/prooflist will enter here
		if (isset($_REQUEST['report_type']) && isset($_REQUEST['file_type'])){
			$report_type = '/' . $_REQUEST['report_type'];
			// if ($_REQUEST['report_type'] == 1){
				// $report_type = '/prooflist';
			// } else if ($_REQUEST['report_type'] == 2){
				// $report_type = '/auditrail';
			// } else{
				// $report_type = '/';
			// }
			
			$new_uri = $this->uri->uri_string() . $report_type;
			
			log_message('debug', "[END] Controller ASI Controller:URI".$new_uri);
			
			if(array_key_exists($new_uri, $this->constants->access_rights_uri)){		
				$uri_index = $this->constants->access_rights_uri[$new_uri];
				if( strlen($permission) >= ($uri_index+1) ){
					$permission_index = $permission[$uri_index];
					if($permission_index == '0'){
						exit("{'success':false,'msg':'No Access Rights.','error_code':'-1'}");
					}
				}	
			}
		
		}
		
	}
	
	function _getUserPermission($index = null){
		$permissionStr = $this->user_info['permission'];
		if($index != null){
			$permission = $permissionStr[$index];
			return $permission;
		}
		log_message('debug', "Avs permission index: $index - >".$permission);
		return $this->user_info['permission'];
	}
}

/* End of file home.php */
/* Location: ./system/application/controllers/home.php */