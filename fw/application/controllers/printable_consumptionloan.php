<?php

/* Location: ./CodeIgniter/application/controllers/printable_capcon.php */

class Printable_consumptionloan extends Asi_Controller 
{
	function Printable_consumptionloan() 
	{
		parent::Asi_Controller();
		$this->load->model('member_model');
		$this->load->model('parameter_model');
		$this->load->helper('file');
		$this->load->helper('text'); 
		$this->load->helper('url');
		$this->load->library('constants'); 
		$this->load->library('common'); 
	}
	
	function index() {
		/*$_REQUEST['employee_id'] = "00421526"; 
		$_REQUEST['principal_amount'] = 2000;
		$_REQUEST['term']  = 36; */
		$_REQUEST['is_admin'] = "1";
		$data =  $this->getData($_REQUEST['employee_id']); 
		 if($data){
			 set_time_limit(0);
			 $filename = "ALL Consumption_".date("YmdHis");		
			 $this->load->plugin('to_pdf');  
			 //$data['date'] = date("m/d/Y", strtotime($this->parameter_model->getParam('CURRDATE')));
			 $data['is_admin'] = $_REQUEST['is_admin'];
			 $data['base_url'] = base_url();
			 $data['principal_amount'] = number_format($_REQUEST['principal_amount'],2,".",",");
			 $data['principal_amount_in_words'] = $this->common->number_to_words($_REQUEST['principal_amount']);
			 $data['term'] = $_REQUEST['term'];
			 $data['term_in_words'] = $this->common->number_to_words($_REQUEST['term']);
			  //added by ASI 365
			 $transDate = explode('T',$_REQUEST['loan_date']);
			 $arrangeDate = explode('-',$transDate[0]);
			 $data['date'] = $arrangeDate[1].'/'.$arrangeDate[2].'/'.$arrangeDate[0];
			 //added by ASI 365 end
			 
			 $html = $this->load->view('forms/consumption', $data, true);
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
				,"position"
				,"department"
				,"spouse"
				,"DATE_FORMAT(hire_date, '%m/%d/%Y') as hire_date"
				,"civil_status"
				,"office_no"
				,"home_phone"
				,"mobile_no"
				,"email_address"
			)
		);
		if(count($data['list'])>0){
			$civil_status = $this->constants->civil_status;
			
			if(array_key_exists($data['list'][0]['civil_status'], $civil_status))
				$data['list'][0]['civil_status'] = $civil_status[$data['list'][0]['civil_status']];
			else
				$data['list'][0]['civil_status'] ="";
			
			return $data['list'][0];
		}
		else 
			return null;
	}
	
}

?>