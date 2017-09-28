<?php
Class Onlinetransaction_notification_service extends Controller{
	
	function Onlinetransaction_notification_service(){
		parent::Controller();
        $this->load->model('Tblonlinenotification');
        $this->load->helper('common_helper');
	}
	
	function index(){
		
	}
	
	function sendEmailNotification(){
		
		$result = $this->Tblonlinenotification->readDistinctTableRef();
		
		if($result['error_code'] == 0 ){
			foreach($result['list'] as $table_ref){
				$list = $this->Tblonlinenotification->get_list(
												array('status' => 0
													  ,'table_reference' => $table_ref['table_reference'])
												,null
												,null
												,array(
													'employee_id'	
													,'request_no'	
													)
												);
				if($list['error_code'] == 0){
					onlinePrintoutEmailNotification($list['list'],$table_ref['table_reference'],'batch');
				}else{
					log_message('debug','DB error1');
				}
			}
		}else{
			log_message('debug','DB error2');
		}	
	}
} 
?>