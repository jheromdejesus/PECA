<?php
/*
 * Created on Apr 23, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Online_membership extends Asi_Controller {

	function Online_membership(){
		parent::Asi_Controller();
		$this->load->model('Onlinememberrequestheader_model');
		$this->load->model('Onlinememberrequestdetail_model');
		$this->load->helper('url');
		$this->load->library('constants');
	}
	
	function index(){		
		
	}
	
	/**
	 * @desc Approve change request
	 */
	function approve()
	{
		$_REQUEST['data'] = array('request_no' => '21');
		
		log_message('debug', "[START] Controller online_membership:approve");
		log_message('debug', "online_membership param exist?:".array_key_exists('data',$_REQUEST));
		
		if (array_key_exists('data',$_REQUEST)) {
			$this->Onlinememberrequestheader_model->populate($_REQUEST['data']);
			$this->Onlinememberrequestheader_model->setValue('status_flag', '9');
			$result = $this->Onlinememberrequestheader_model->update();	
			if($result['affected_rows'] <= 0){
				echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
			} else
				echo "{'success':true,'msg':'Data successfully saved.'}";
		} else
			echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
			
		log_message('debug', "[END] Controller online_membership:approve");
	}
	
	/**
	 * @desc disapprove change request
	 */
	function disapprove()
	{
		$_REQUEST['data'] = array('request_no' => '21');
		
		log_message('debug', "[START] Controller online_membership:disapprove");
		log_message('debug', "online_membership param exist?:".array_key_exists('data',$_REQUEST));
		
		if (array_key_exists('data',$_REQUEST)) {
			$this->Onlinememberrequestheader_model->populate($_REQUEST['data']);
			$this->Onlinememberrequestheader_model->setValue('status_flag', '10');
			$result = $this->Onlinememberrequestheader_model->update();	
			if($result['affected_rows'] <= 0){
				echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
			} else
				echo "{'success':true,'msg':'Data successfully saved.'}";
		} else
			echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
			
		log_message('debug', "[END] Controller online_membership:disapprove");
	}
	
}
?>
