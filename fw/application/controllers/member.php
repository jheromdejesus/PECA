<?php

/* Location: ./CodeIgniter/application/controllers/member.php */



class Member extends Asi_controller {

	function Member()
	{
		parent::Asi_controller();
		$this->load->model('member_model');
		$this->load->helper('url');
		$this->load->library('constants');
		$this->load->library('asi_model');
		$this->load->library('asi_pdf');
		$this->load->helper('date');
		
	}
	
	function index() {
	}
	
	function read()
	{
		$params = array('member_status' => 'A', 'status_flag' => '1' );
		
		//if there is employee id, discard other parameters
		if(array_key_exists('employee_id', $_REQUEST) && $_REQUEST['employee_id']!= ""){
			$params['employee_id LIKE'] =  $_REQUEST['employee_id']."%";
		}
		else{
			if(array_key_exists('first_name', $_REQUEST) && $_REQUEST['first_name']!= "")
				$params['first_name LIKE'] =  $_REQUEST['first_name']."%";
			if(array_key_exists('last_name', $_REQUEST) && $_REQUEST['last_name']!= "")
				$params['last_name LIKE'] =  $_REQUEST['last_name']."%";
			if(array_key_exists('middle_name', $_REQUEST) && $_REQUEST['middle_name']!= "")
				$params['middle_name LIKE'] =  $_REQUEST['middle_name']."%";
		}
		
		$data = $this->member_model->get_list(
		$params,
		array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
		array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
		array('employee_id '
			,'last_name'
			,'first_name'
			,'middle_name'
			,'member_date'
			,'status_flag'
			,'guarantor'
			,'beneficiaries')
		,'employee_id ASC'
		);
		
		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
			));

		return $data;
	}
	
	//same as read function, but includes inactive employees
	function readAll()
	{
		$params = array('status_flag' => '1');
		
		//if there is employee id, discard other parameters
		if(array_key_exists('employee_id', $_REQUEST) && $_REQUEST['employee_id']!= ""){
			$params['employee_id LIKE'] =  $_REQUEST['employee_id']."%";
		}
		else{
			if(array_key_exists('first_name', $_REQUEST) && $_REQUEST['first_name']!= "")
				$params['first_name LIKE'] =  $_REQUEST['first_name']."%";
			if(array_key_exists('last_name', $_REQUEST) && $_REQUEST['last_name']!= "")
				$params['last_name LIKE'] =  $_REQUEST['last_name']."%";
			if(array_key_exists('middle_name', $_REQUEST) && $_REQUEST['middle_name']!= "")
				$params['middle_name LIKE'] =  $_REQUEST['middle_name']."%";
		}
		
		$data = $this->member_model->get_list(
		$params,
		array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
		array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
		array('employee_id '
			,'last_name'
			,'first_name'
			,'middle_name'
			,'member_date'
			,'status_flag'
			,'guarantor'
			,'beneficiaries')
		,'employee_id ASC'
		);
		
		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
			));

		return $data;
	}
	
	/**
	 * @desc Reads all members who are not comakers of the specific loan, applier of the 
	 * loan and does not have the company code 910 and 920 and is an active member
	 */
	function readAllowedComakers(){
		/*$_REQUEST['employee_id'] = '000001';
		$_REQUEST['loan_no'] = '123';
		$_REQUEST['comaker_id'] = '01517144';
		$_REQUEST['first_name']= 'BAUTISTA';*/
		
		$params = "";
		//if there is employee id, discard other parameters
		if(array_key_exists('comaker_id', $_REQUEST) && $_REQUEST['comaker_id']!= ""){
			$params.= "employee_id LIKE '".$_REQUEST['comaker_id']."%' AND ";
		}
		else{
		if(array_key_exists('first_name', $_REQUEST) && $_REQUEST['first_name']!= "")
			$params.= "first_name LIKE '".$_REQUEST['first_name']."%' AND ";
		if(array_key_exists('last_name', $_REQUEST) && $_REQUEST['last_name']!= "")
			$params.= "last_name LIKE '".$_REQUEST['last_name']."%' AND ";
		}
		
		$params .= "((me.company_code NOT IN ('910','920') AND me.member_status = 'A') OR me.non_member = 'Y')
			AND me.guarantor = 'Y'
			AND tl.guarantor_id IS NULL
			AND me.employee_id != '".$_REQUEST['employee_id']."'";
		
		$data = $this->member_model->getAllowedComakers(
		$params,
		array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
		array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
		array('me.employee_id AS employee_id'
			,'me.last_name AS last_name'
			,'first_name AS first_name')
		,'employee_id ASC'
		,$_REQUEST['loan_no']
		);
		
		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
			));
	}
	
	function readAllowedComakersForMembership(){
		/*$_REQUEST['employee_id'] = '000001';
		$_REQUEST['loan_no'] = '123';
		$_REQUEST['comaker_id'] = '01517144';
		$_REQUEST['first_name']= 'BAUTISTA';*/
		
		$params = "";
		//if there is employee id, discard other parameters
		if(array_key_exists('comaker_id', $_REQUEST) && $_REQUEST['comaker_id']!= ""){
			$params.= "employee_id LIKE '".$_REQUEST['comaker_id']."%' AND ";
		}
		else{
			if(array_key_exists('first_name', $_REQUEST) && $_REQUEST['first_name']!= "")
				$params.= "first_name LIKE '".$_REQUEST['first_name']."%' AND ";
			if(array_key_exists('last_name', $_REQUEST) && $_REQUEST['last_name']!= "")
				$params.= "last_name LIKE '".$_REQUEST['last_name']."%' AND ";
		}
		
		$params .= "((me.company_code NOT IN ('910','920') AND me.member_status = 'A') OR me.non_member = 'Y')
			AND me.guarantor = 'Y'
			AND tl.guarantor_id IS NULL
			AND me.employee_id != '".$_REQUEST['employee_id']."'";
		
		$data = $this->member_model->getAllowedComakersForMembership(
		$params,
		array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
		array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
		array('me.employee_id AS employee_id'
			,'me.last_name AS last_name'
			,'first_name AS first_name')
		,'employee_id ASC'
		,$_REQUEST['loan_no']
		);
		
		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
			));
	}
	
}
?>