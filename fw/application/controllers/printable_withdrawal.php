<?php

/* Location: ./CodeIgniter/application/controllers/printable_withdrawal.php */

class Printable_withdrawal extends Asi_Controller 
{
	function Printable_withdrawal() 
	{
		parent::Asi_Controller();
		$this->load->model('member_model');
		$this->load->model('parameter_model');
		$this->load->model('Onlinecapitaltransactionheader_model');
		$this->load->helper('file'); 
		$this->load->helper('url');
		$this->load->library('common');
	}
	
	function index() {	
		$_REQUEST['is_admin'] = '1';
		/*$_REQUEST['employee_id'] = "00421526";
		$_REQUEST['transaction_amount'] = 10000;
		$_REQUEST['transaction_code']='WDWL';
		$_REQUEST['remarks'] = "test";*/
		if($_REQUEST['transaction_code']!='DDEP'){
			 $data =  $this->getData($_REQUEST['employee_id']); 
			 if($data){
				 set_time_limit(0);
				$contact_nos = array();
				 if($data['office_no']!=""){
					$contact_nos[] = $data['office_no'];
				 }
				 if($data['mobile_no']!=""){
					$contact_nos[] = $data['mobile_no'];
				 }
				 if($data['home_phone']!=""){
					$contact_nos[] = $data['home_phone'];
				 }
				 
				 $filename = "ALL Withdrawal_".date("YmdHis");		
				 $this->load->plugin('to_pdf');  
				 //$data['date'] = date("m/d/Y", strtotime($this->parameter_model->getParam('CURRDATE')));
				 $transDate = explode('T',$_REQUEST['transaction_date']);
				 $arrangeDate = explode('-',$transDate[0]);
				 $data['date'] = $arrangeDate[1].'/'.$arrangeDate[2].'/'.$arrangeDate[0];
				 $data['is_admin'] = $_REQUEST['is_admin'];
				 $data['remarks'] = $_REQUEST['remarks'];
				 $data['base_url'] = base_url();
				 $data['contact_nos'] = implode("/", $contact_nos); 
				 $data['transaction_amount'] = number_format($_REQUEST['transaction_amount'],2,".",",");
				 
				 if(strrpos($_REQUEST['transaction_amount'], ".") != false) {
				 //[start] 0007882 edited by asi466 on 20110901
				 $amountArr = explode(".", number_format($_REQUEST['transaction_amount'],2,".",","));
				 //[end] 0007882 edited by asi466 on 20110901					
					if($amountArr[1] != "00") {
						$data['transaction_amount_in_words_cents'] = $this->common->number_to_words($amountArr[1]);
					 } else {
						$data['transaction_amount_in_words_cents'] = false;
					 }
				 } else {
					$data['transaction_amount_in_words_cents'] = false;
				 }
				 $data['transaction_amount_in_words'] = $this->common->number_to_words($_REQUEST['transaction_amount']);
				 $html = $this->load->view('forms/withdrawal', $data, true);
				 pdf_create($html, $filename);
			}
			else{
			echo "{'success':false,'msg':'Employee does not exist','error_code':'22'}";
			}
		 }
		 else
			echo "{'success':false,'msg':'No preview available for direct deposit','error_code':'52'}";
	}
	
	function getData($employee_id){
		$data = $this->member_model->get(
			array('employee_id' => $_REQUEST['employee_id'])
			,array(
				"employee_id"
				,"CONCAT_WS(' ', first_name, middle_name, last_name) as employee_name"
				,"email_address"
				,"office_no"
				,"mobile_no"
				,"home_phone"
			)
		);
		if(count($data['list'])>0){
			return $data['list'][0];
		}
		else 
			return null;
	}
	
}

?>