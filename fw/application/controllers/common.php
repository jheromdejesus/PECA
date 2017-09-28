<?php

class Common extends Asi_Controller {

	function Common()
	{
		parent::Asi_Controller();
		$this->load->library('constants');
		$this->load->model('dividend_model');
		$this->load->model('parameter_model');
	}
	
	function index()
	{	
	}
	
	function getGLEntryTG(){
		$trans_group = $this->constants->transaction_group;
		$trans_group["OT"] = "Others";
		$data = $this->constants->create_list($trans_group);
		
		echo json_encode(array(
            'data' => $data
        )); 
	}
		
	function getTG(){
		$data = $this->constants->create_list($this->constants->transaction_group);
		
		echo json_encode(array(
            'data' => $data
        )); 
	}
	
	function getCE(){
		$data = $this->constants->create_list($this->constants->capcon_effect);
		
		echo json_encode(array(
            'data' => $data
        ));
	}
	
	function getAccountGroup(){
		$data = $this->constants->create_list($this->constants->account_group);
		
		echo json_encode(array(
            'data' => $data
        ));
	}
	
	function getStatus(){
		$data = $this->constants->create_list($this->constants->member_status);
		
		echo json_encode(array(
            'data' => $data
        ));
	}
	
	function getRelationship(){
		$data = $this->constants->create_list($this->constants->member_relationship);
		
		echo json_encode(array(
            'data' => $data
        ));
	}
	function transferStatus(){
		$table = $_REQUEST["table"];
		switch($table){
			case "1";
			$sql = "UPDATE o_loan SET status_flag = '3' WHERE status_flag = '1';";
			echo $this->db->query($sql);
			break;
			case "2";
			$sql = "UPDATE o_loan_payment SET status_flag = '3' WHERE status_flag = '1';";
			echo $this->db->query($sql);
			break;
			case "3";
			$sql = "UPDATE o_member_request_header SET status_flag = '3' WHERE status_flag = '1';";
			echo $this->db->query($sql);
			break;
			case "4";
			$sql = "UPDATE o_payroll_deduction SET status_flag = '3' WHERE status_flag = '1';";
			echo $this->db->query($sql);
			break;
			case "5";
			$sql = "UPDATE o_capital_transaction_header SET status_flag = '3' WHERE status_flag = '1';";
			echo $this->db->query($sql);
			break;
		}
	}
	function transferAllStatus(){
		$this->db->trans_begin();
		$sql = "UPDATE o_loan SET status_flag = '3' WHERE status_flag = '1';";
		$this->db->query($sql);
		$sql = "UPDATE o_loan_payment SET status_flag = '3' WHERE status_flag = '1';";
		$this->db->query($sql);
		$sql = "UPDATE o_member_request_header SET status_flag = '3' WHERE status_flag = '1';";
		$this->db->query($sql);
		$sql = "UPDATE o_payroll_deduction SET status_flag = '3' WHERE status_flag = '1';";
		$this->db->query($sql);
		$sql = "UPDATE o_capital_transaction_header SET status_flag = '3' WHERE status_flag = '1';";
		$this->db->query($sql);
		$this->db->trans_complete();
		if($this->db->trans_status() === TRUE){  
			  echo "{'success':true,'msg':'Online appliactions were successfully transfered.'}";
	    } else{
			  echo "{'success':false,'msg':'Online appliactions were NOT successfully transfered.','error_code':'2'}";
		}
	}
	
	function getLastDivProcessingDate()
	{
		$divDate = "";
		$data = $this->dividend_model->getLastDivProcessingDate();
			
		if ($data['count']==0)
			$divDate = " ";
		else 
			$divDate = date("m/d/Y",strtotime($data['list'][0]['accounting_period']));
			
		$data['list'][0]['accounting_period'] = $divDate;
		
		echo json_encode(array(
            'data' => $data['list']
        ));
			
	}
	
	function getAccountingPeriod()
	{
		$data = $this->parameter_model->getParam('ACCPERIOD');
	
		$date['list'][0]['accounting_period'] = $data;	
		
		echo json_encode(array(
            'data' => $date['list']
        ));
			
	}
	
}
/* End of file common.php */
/* Location: ./PECA/application/controllers/common.php */