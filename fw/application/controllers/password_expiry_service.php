<?php
Class Password_expiry_service extends Controller{
	function Password_expiry_service(){
		parent::Controller();
		$this->load->model("parameter_model");
		$this->load->model("user_model");
		$this->load->model("userpasswords_model");
		$this->load->helper('url');
	}
	
	function index(){
		$currdate = date('Ymd');
		$daysBeforeExpirationNotification = $this->parameter_model->getParam('DPEX') - $this->parameter_model->getParam('DBEN');

		$daysPasswordExpires = $this->parameter_model->getParam('DPEX');
		$data = $this->userpasswords_model->retrieveLatestPasswordChange();
		
		foreach($data['list'] as $user_data){
			$actualDayToStartNotification = date("Ymd", strtotime($user_data['modified_date']." + $daysBeforeExpirationNotification days"));
			$actualDayOfExpiry = date("Ymd", strtotime($user_data['modified_date']." + $daysPasswordExpires days"));
			//echo "actual expiry date: ".$actualDayOfExpiry.":". $currdate."<br>";
			//echo "actual Day To Start Notification: ".$actualDayToStartNotification.":". $currdate."<br>";
			
			$this->load->library('email');
			if($actualDayOfExpiry < $currdate){
				if($user_data['status_flag'] != '-1'){
					if(base_url() == "https://www.pgpsla.com/"){
						$this->sendEmailOnline($user_data['email_address'], $user_data['user_name']);
					}
					else{
						$this->sendEmailLocal($user_data['email_address'], $user_data['user_name']);
					}
					
					$this->user_model->setValue('status_flag', '-1');
					$this->user_model->setValue('user_id', $user_data['user_id']);
					$this->user_model->update();
				}
			}
			else if($actualDayToStartNotification < $currdate){
				
				$dateDiff = $this->dateDiff($currdate, $actualDayOfExpiry) + 1;
				if(base_url() == "https://www.pgpsla.com/"){
					$this->sendEmailOnline($user_data['email_address'], $user_data['user_name'], "expiring", $dateDiff);
				}
				else{
					$this->sendEmailLocal($user_data['email_address'], $user_data['user_name'], "expiring", $dateDiff);
				}
			}
		}
	}
	
	function dateDiff($start, $end){
		$j_start = gregoriantojd(substr($start, 4, 2), substr($start, 6, 2), substr($start, 0, 4));
		$j_end = gregoriantojd(substr($end, 4, 2), substr($end, 6, 2), substr($end, 0, 4));
		
		return $j_end - $j_start;
	}
	
	function sendEmailLocal($email, $user_name, $mode = "expired", $dateDiff = null) {
		$email_message = '';
		$this->email->to($email);
		$this->email->from('admin@peca.com');
		
		if($mode=="expired"){
			$this->email->subject("Your password has expired!");

			$email_message = 'Hi ' . $user_name . "! <br/><br/>";
			$email_message .= "Your password has expired and your account has been locked <br/>";
			$email_message .= "Please visit the following link and click
				on forgot password to reset your password: <a href=\"".base_url()."login\">".base_url()."login </a><br/><br/>";
			$email_message .= "Thank you.<br/>";
		}
		else{
			$this->email->subject("Your password expires in $dateDiff day/s");

			$email_message = 'Hi ' . $user_name . "! <br/><br/>";
			$email_message .= "Your password expires in $dateDiff day/s <br/>";
			$email_message .= "Please change your password to prevent to prevent any inconvenience.<br/>";
			$email_message .= "To login, visit: <a href=\"".base_url()."login\">".base_url()."login </a><br/><br/>";
			$email_message .= "Thank you.<br/>";
		}
		
		$this->email->Message($email_message);

		$this->email->send();
	}
	
	function sendEmailOnline($email, $user_name, $mode = "expired", $dateDiff = null) {
		$email_message = '';
		
		if($mode=="expired"){
			$subject = "Your password has expired!";	
			$email_message = 'Hi ' . $user_name . "! <br/><br/>";
			$email_message .= "Your password has expired and your account has been locked <br/>";
			$email_message .= "Please visit the following link and click
				on forgot password to reset your password: <a href=\"".base_url()."login\">".base_url()."login </a><br/><br/>";
			$email_message .= "Thank you.<br/>";
		}
		else{
			$subject = "Your password expires in $dateDiff day/s";

			$email_message = 'Hi ' . $user_name . "! <br/><br/>";
			$email_message .= "Your password expires in $dateDiff day/s <br/>";
			$email_message .= "Please change your password to prevent to prevent any inconvenience.<br/><br/>";
			$email_message .= "Thank you.<br/>";
		}
		
		$headers = 'MIME-Version: 1.0' . "\r\n" .
		'Content-type: text/html; charset=iso-8859-1' . "\r\n" .
		'From: admin@peca.com' . "\r\n" .
		'Reply-To: admin@peca.com' . "\r\n" .
		'X-Mailer: PHP/';
		
		mail($email,$subject,$email_message, $headers);
	}
} 
?>