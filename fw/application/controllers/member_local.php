<?php

/* Location: ./CodeIgniter/application/controllers/member.php */



class Member extends Controller {

	function Member()
	{
		parent::Controller();
		$this->load->model('Member_model');
		$this->load->helper('url');
		$this->load->library('constants');
		$this->load->library('asi_model');
		$this->load->library('asi_pdf');
		$this->load->helper('date');
		
	}
	
	function index() {

		$data = $this->retrieveEmployeeInformation();
		echo "<pre>";
		print_r(array(
			'success' => true,
            'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
        ));
		echo "</pre>";
		
	}
	
	function retrieveEmployeeInformation($employee_id) {

		$data = $this->Member_model->retrieveEmployeeInformation($employee_id);		
		return $data;
	
	}
	
	
	
}
	