<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
	
	if ( ! function_exists('onlinePrintoutEmailNotification')) {
		function onlinePrintoutEmailNotification($list,$model,$triggered_by = 'user') {
			$ci =& get_instance();
			$ci->load->library('email');
			$ci->load->model($model);
			$ci->load->model("User_model");
			$ci->load->model("Tblonlinenotification");
			
			foreach($list as $list_data){
				$req = $ci->$model->get(array('request_no' => $list_data['request_no']));
				if($req['list'][0]['email_sent'] != 1){
					$user_data = $ci->User_model->get(array('user_id' => $list_data['employee_id'] ));
					
					$ci->email->from('admin@peca.com');
					$ci->email->to($user_data['list'][0]['email_address']);
					$ci->email->subject('Email Confirmation of Document Received.');
					$msg = $ci->load->view('onlinePrintoutEmailNotification','',true);
					
					$ci->email->message($msg);
					$result = $ci->email->send();
					
					$success = FALSE;
					if ($result) {
						$success = TRUE;
					}
					
					if($success == TRUE){
						$ci->$model->setValue('email_sent', 1);
						$ci->$model->update(array('request_no' => $list_data['request_no']));
						
						if($triggered_by == 'batch'){
							$cond = array(
								'table_reference' => $model
								,'request_no' => $list_data['request_no']
							);
							$this->Tblonlinenotification->delete($cond);
						}
												
					}else{
						$ci->Tblonlinenotification->setValue('employee_id',$list_data['employee_id']);
						$ci->Tblonlinenotification->setValue('email_address',$user_data['list'][0]['email_address']);
						$ci->Tblonlinenotification->setValue('table_reference',$model);
						$ci->Tblonlinenotification->setValue('request_no',$list_data['request_no']);
						$ci->Tblonlinenotification->setValue('status',0);
						$cond = array(
								'table_reference' => $model
								,'request_no' => $list_data['request_no']
								);
						$arr_result = $ci->Tblonlinenotification->checkDuplicateKeyEntry($cond);
						
						if($arr_result['error_code'] == 0){
							$ci->Tblonlinenotification->insert();
						}
					}
				}
			}
		}
	}

?>