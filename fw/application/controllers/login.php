<?php
class Login extends Controller {
	function Login() {
		parent :: Controller();
		$this->load->model('User_model');
		$this->load->library(array('constants','email'));
		$this->load->helper('url');
		$this->load->model('Parameter_model');
		$this->load->model('Userpasswords_model');
	}

	function index($auto_redirect=false) {
		if($auto_redirect){
			$data['auto_redirect'] = true;
		}
		else{
			$data['auto_redirect'] = false;
		}
		$this->load->view('login', $data);
	}

	function logout() {
		$this->User_model->setValue('user_id', $_REQUEST["user_id"]);
		$this->User_model->setValue('auth', "");
		$data = $this->User_model->update();
		//$this->load->view('login');
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");  
		$this->load->view('login');
	}

	function forgot() {
		log_message('debug', "[START] Controller login:forgot");
		if (array_key_exists('user', $_REQUEST)) {
				$arr_user = "email_address = '". $_REQUEST['user']['user_id']."'
				AND (status_flag = '1' OR status_flag = '-1')";
			
			$userResult = $this->User_model->get($arr_user);
			if (count($userResult['list']) == 0) {
				echo "{success: false, msg: 'The email address you entered is incorrect.'}";
			} else {
				$new_password = $this->_genRandomString();
				$userResult['list'][0]['password'] = md5($new_password);
				$this->User_model->populate($userResult['list'][0]);
				//release lock here
				$this->User_model->setValue('login_attempt', 0);
				$this->User_model->setValue('status_flag', 1);
				$result = $this->User_model->update();
				$resultInsertUserPassword = $this->insertUserPassword($userResult['list'][0]['user_id'], $userResult['list'][0]['password']);
				if ($result['error_code'] == 0 && $result['affected_rows'] > 0 && $resultInsertUserPassword) {
					$this->plain_password = $new_password;
					if($this->sendEmail($userResult['list'][0]['email_address'],$userResult['list'][0]['user_name'])){
						echo "{success: true, msg: 'Successfully sent.'}";
					} else {
						echo "{success: true, msg: 'Sending failed.'}";
					}
				} else
						echo "{success: false, msg: 'Password generation failed.'}";
			}

			$this->User_model->populate($arr_user);

		}
	}
	
	function insertUserPassword($user_id, $password){
		$checkDuplicate = $this->Userpasswords_model->checkDuplicateKeyEntry(array(
			'user_id' => $user_id
			,'password' => $password));
				
		$this->Userpasswords_model->setValue('user_id', $user_id);
		$this->Userpasswords_model->setValue('password', $password);
		$this->Userpasswords_model->setValue('status_flag', 1);
		
		if($checkDuplicate['error_code']==1){
			$this->Userpasswords_model->setValue('modified_by', 'SYSTEM');
			$resultUpdate = $this->Userpasswords_model->update(array(
				'user_id' => $user_id
				,'password' => $password));
				
			if($resultUpdate['error_code'] == 0 && $resultUpdate['affected_rows'] > 0){
				return true;
			}
			else{
				return false;
			}
		}
		else{
			$this->Userpasswords_model->setValue('created_by', 'SYSTEM');
			$resultInsert = $this->Userpasswords_model->insert();
			if($resultInsert['error_code'] == 0){
				return true;
			}
			else{
				return false;
			}
		}
	}
	
	function forgot_auto() {
		log_message('debug', "[START] Controller login:forgot auto");
		
		//get all users
		$users_list = $this->User_model->get_list(array('status_flag'=>'1', 'is_admin'=>'2'));
		foreach ($users_list['list'] as $row){
			$_REQUEST['user']['user_id'] = $row['email_address'];
			
			if (array_key_exists('user', $_REQUEST)) {
				$arr_user = array (
					'email_address' => $_REQUEST['user']['user_id']
					, 'status_flag' => '1'
				);

				$userResult = $this->User_model->get($arr_user);
				if (count($userResult['list']) == 0) {
					echo "{success: false, msg: 'User ID does not exists.'}";
				} else {
					$new_password = $this->_genRandomString();
					$userResult['list'][0]['password'] = md5($new_password);
					$this->User_model->populate($userResult['list'][0]);
					$result = $this->User_model->update();
					if ($result['error_code'] == 0 && $result['affected_rows'] > 0) {
						$this->plain_password = $new_password;
						if($this->sendEmail($userResult['list'][0]['email_address'],$userResult['list'][0]['user_name'])){
							echo "{success: true, msg: 'Successfully sent.'}";
						} else {
							echo "{success: true, msg: 'Sending failed.'}";
						}
					} else
							echo "{success: false, msg: 'Password generation failed.'}";
				}

				$this->User_model->populate($arr_user);

			}
		}
	}
	
	function sendEmail($email, $user_name) {
		$email_message = '';
		$this->email->to($email);
	    // $this->email->cc('epama@asi-dev2.com');
		$this->email->from('admin@peca.com');
		$this->email->subject("Forgot Password : ". $user_name);

		$email_message = 'Hi ' . $user_name . "! <br/><br/>";
		$email_message .= $this->constants->messages['118'] . " <br/><br/>";
		$email_message .= $this->constants->messages['114'] . $email . " <br/>";
		$email_message .= $this->constants->messages['113'] . $this->plain_password . " <br/>";
		$email_message .= "<br/>";
		$email_message .= "To login, visit: <a href=\"".base_url()."login\">".base_url()."login </a>";

		$this->email->Message($email_message);

		if ($this->email->send()) {
			return true;
		} else {
			return false;
		}
	}
	
	function sendEmailNotification($email, $user_name) {
		$email_message = '';
		$this->email->to($email);
	    // $this->email->cc('epama@asi-dev2.com');
		$this->email->from('admin@peca.com');
		$this->email->subject("PECA login failed notification : ". $user_name);

		$email_message = 'Hi ' . $user_name . "! <br/><br/>";
		$email_message .= "You have made 5 failed login attempts." . " <br/>";
		$email_message .= "Please click the forget password link in the login page to reset your password.<br/><br/>";
		$email_message .= "To login, visit: <a href=\"".base_url()."login\">".base_url()."login </a><br/><br/>";
		$email_message .= "Thank you.<br/>";

		$this->email->Message($email_message);

		if ($this->email->send()) {
			return true;
		} else {
			return false;
		}
	}

	function process() {
		log_message('debug', "[START] Controller login:process");

		if (array_key_exists('user', $_REQUEST)) {
			//get login attempt from parameter list
			$loginFail = $this->Parameter_model->retrieveValue('LOGINFAIL');
			
			//get number of attempts for the login user
			$no_attempt = 1;
			$this->User_model->populate(array('email_address'=>$_REQUEST['user']['user_id']));
			$result = $this->User_model->get();
			if (count($result['list']) > 0){
				$no_attempt = $result['list'][0]['login_attempt'] + 1;		
				
				//check login attempts
				if ($no_attempt > $loginFail){
					//send email notification here
					$this->sendEmailNotification($_REQUEST['user']['user_id'], $result['list'][0]['user_name']);
					echo "{success: false, msg: 'You have made 5 failed login attempts.'}";
					exit;
				}
			}
			
			$user = array();
			$user['password'] = md5(rtrim($_REQUEST['user']['password']));
			//$user['status_flag'] = '1';
			$user['email_address'] = $_REQUEST['user']['user_id'];
			$this->User_model->populate($user);

			$result = $this->User_model->get();
			
			if (count($result['list']) == 0) {
			
				//increment login attempt of user
				$this->User_model->populate(array());
				$this->User_model->setValue('login_attempt', $no_attempt);
				$this->User_model->update(array('email_address'=>$_REQUEST['user']['user_id']));
				
				echo "{success: false, msg: 'The email or password you entered is incorrect.'}";
			} else {	
				if($result['list'][0]['status_flag'] == '-1'){
					echo "{success: false, msg: 'Your account is locked, please click on \'Forgot Password\' to reset your password.'}";
					exit;
				}
				else if($result['list'][0]['status_flag'] == '0'){
					//increment login attempt of user
					$this->User_model->populate(array());
					$this->User_model->setValue('login_attempt', $no_attempt);
					$this->User_model->update(array('email_address'=>$_REQUEST['user']['user_id']));
					echo "{success: false, msg: 'The email or password you entered is incorrect.'}";
					exit;
				}
				
				$this->User_model->setValue('user_id', $result['list'][0]['user_id']);
				if ($result['error_code'] == 0 && $result['affected_rows'] > 0) {
					$auth_key = $this->User_model->generate_auth();
					log_message('debug', "auth_key = $auth_key");
					$result = $this->User_model->update();
	
					if ($result['error_code'] == 0 && $result['affected_rows'] > 0) {
						//release lock here
						$this->User_model->setValue('login_attempt', 0);
						$result = $this->User_model->update();
						
						echo "{success: true, redirect: '/home', auth_key:'$auth_key'}";
					} else
						if ($result['affected_rows'] == 0)
							echo "{success: false, msg: 'Invalid login information.'}";
						else
							echo "{success: false, msg: '" . $result['error_message'] . "'}";
				} else
					if ($result['affected_rows'] == 0)
						echo "{success: false, msg: 'Invalid login information.'}";
					else
						echo "{success: false, msg: '" . $result['error_message'] . "'}";
			}
		} else
				echo "{success: false, msg: 'Missing user paramater'}";

		log_message('debug', "[END] Controller login:process");
	}
		
	function _genRandomString() {
		$length = 10;
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		$string = '';

		for ($p = 0; $p < $length; $p++) {
			$string .= $characters[mt_rand(0, strlen($characters) - 1)];
		}

		return $string;
	}
}

/* End of file login.php */
/* Location: ./system/application/controllers/login.php */