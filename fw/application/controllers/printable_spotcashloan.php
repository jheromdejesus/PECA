<?php

/* Location: ./CodeIgniter/application/controllers/printable_capcon.php */

class Printable_spotcashloan extends Asi_Controller 
{
	function Printable_spotcashloan() 
	{
		parent::Asi_Controller();
		$this->load->model('member_model');
		$this->load->model('parameter_model');
		$this->load->helper('file');
  		$this->load->helper('url');
		$this->load->library('common');
	}
	
	function index() {	
		$_REQUEST['is_admin'] = '1';
		 $data =  $this->getData($_REQUEST['employee_id']); 
		 if($data){
			 set_time_limit(0);
			 $filename = "ALL Spot Cash Loan_".date("YmdHis");		
			 $this->load->plugin('to_pdf');  
			// $data['date'] = date("m/d/Y", strtotime($this->parameter_model->getParam('CURRDATE')));
			 $data['is_admin'] = $_REQUEST['is_admin'];
			 $data['base_url'] = base_url();
			 $data['principal_amount'] = number_format($_REQUEST['principal_amount'],2,".",",");
			 $data['principal_amount_in_words'] = $this->common->number_to_words($_REQUEST['principal_amount']);
			 $data['term'] = $_REQUEST['term'];
			 //added by ASI 365
			 $transDate = explode('T',$_REQUEST['loan_date']);
			 $arrangeDate = explode('-',$transDate[0]);
			 $data['date'] = $arrangeDate[1].'/'.$arrangeDate[2].'/'.$arrangeDate[0];
			 //added by ASI 365 end
			 $data['term_in_words'] = $this->common->number_to_words($_REQUEST['term']);
			 $html = $this->load->view('forms/spotcash', $data, true);
			 pdf_create($html, $filename);
		}
		else{
			echo "{'success':false,'msg':'Employee does not exist','error_code':'22'}";
		}
		 
	}
	
	function getData($employee_id){
		$data = $this->member_model->get(
			array('employee_id' => $_REQUEST['employee_id'])
			,array(
				"employee_id"
				,"CONCAT_WS(' ', first_name, middle_name, last_name) as employee_name"
				,"DATE_FORMAT(hire_date, '%m/%d/%Y') as hire_date"
				,"office_no"
				,"mobile_no"
				,"home_phone"
				,"email_address"
			)
		);
		if(count($data['list'])>0){
			$contact_nos = array();
			
			if($data['list'][0]['office_no']!="")
				$contact_nos[] = $data['list'][0]['office_no'];
			if($data['list'][0]['mobile_no']!="")
				$contact_nos[] = $data['list'][0]['mobile_no'];
			if($data['list'][0]['home_phone']!="")
				$contact_nos[] = $data['list'][0]['home_phone'];
			
			if(count($contact_nos)){
				$data['list'][0]['contact_nos']	= implode("/", $contact_nos);
			}
			else{
				$data['list'][0]['contact_nos'] = "";
			}
			return $data['list'][0];
		}
		else 
			return null;
	}
	
}

?>