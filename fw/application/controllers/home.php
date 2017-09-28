<?php

class Home extends Asi_Controller {

	function Home()
	{
		parent::Asi_Controller();
		$this->load->helper('date');
		$this->load->model('parameter_model');
	}
	
	function index()
	{
		$dateString = "%m/%d/%y";
		
		log_message('debug', "[START] Controller Home:index");
		$data = $this->parameter_model->get(array('parameter_id'=>'CURRDATE')
				,'parameter_value');

		$data = array(
			'auth_key' => $this->auth_key,
			'user_info' => $this->user_info,
			'employee_id' => $this->employee_id,
			'today' => date("m/d/Y", strtotime($data['list'][0]['parameter_value']))
		);
		
		header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		header("Pragma: no-cache");


		$this->load->view('home', $data);
					
		log_message('debug', "[END] Controller Home:index");		
	}
}

/* End of file home.php */
/* Location: ./system/application/controllers/home.php */