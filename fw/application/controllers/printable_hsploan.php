<?php

/* Location: ./CodeIgniter/application/controllers/printable_capcon.php */

class Printable_hsploan extends Asi_Controller 
{
	function Printable_hsploan() 
	{
		parent::Asi_Controller();
		$this->load->model('member_model');
		$this->load->helper('file');
		$this->load->helper('url');	
		$this->load->library('common'); 
	}
	
	function index() {	
		 $data =  $this->getData($_REQUEST['employee_id']); 
		 if($data){
			 set_time_limit(0);
			 $filename = "ALLhsploan_".date("YmdHis");		
			 $this->load->plugin('to_pdf');
			 $data['is_admin'] = $_REQUEST['is_admin'];
			 $data['base_url'] = base_url();
			 $data['principal_amount'] = number_format($_REQUEST['principal_amount'],2,".",",");
			 $data['principal_amount_in_words'] = $this->common->number_to_words($_REQUEST['principal_amount']);
			 $data['term'] =$_REQUEST['term'];
			 $data['term_in_words'] = $this->common->number_to_words($_REQUEST['term']);
			 $html = $this->load->view('forms/hsp', $data, true);
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
				,"CONCAT_WS(' ', address_1, address_2, address_3) as employee_address"
				,"DATE_FORMAT(hire_date, '%m/%d/%Y') as hire_date"
				,"TIN"
				,"office_no"
				, "mobile_no"
				, "home_phone"
				,"civil_status"
				,"spouse"
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