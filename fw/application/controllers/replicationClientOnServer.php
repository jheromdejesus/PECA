<?php

class replicationClientOnServer extends Asi_Controller {
	
	function replicationClientOnServer(){
		parent::Asi_controller();
		$this->load->model('onlineattachment_model');
		$this->load->model('onlinebulletinboard_model');
		$this->load->model('onlinecapitaltransactiondetail_model');
		$this->load->model('onlinecapitaltransactionheader_model');
		$this->load->model('onlineloan_model');
		$this->load->model('onlineloanpayment_model');
		$this->load->model('onlineloanpaymentdetail_model');
		$this->load->model('onlinememberrequestdetail_model');
		$this->load->model('onlinememberrequestheader_model');
		$this->load->model('onlinepayrolldeduction_model');
		$this->load->model('onlinerequestapprover_model');
		$this->load->model('onlineworkflow_model');
		$this->load->model('ouser_model');
	}
	
	function index()
	{	
		$_REQUEST['user'] = 'peca';
		$_REQUEST['password'] = 'peca';
		$server_url = "192.36.253.167/replicationServerOnOnline";
	
		$this->load->library('xmlrpc');
		
		$this->xmlrpc->server($server_url, 80);
		$this->xmlrpc->method('get');
		//$this->xmlrpc->set_debug(TRUE);
		
		$request = array($_REQUEST['user'],md5($_REQUEST['password']));
		$this->xmlrpc->request($request);	
		
		if ( ! $this->xmlrpc->send_request())
		{
			echo "{'success':false,'msg':'".$this->xmlrpc->display_error()."'}";
		}
		else
		{
			$this->db->trans_begin();
			$success = TRUE;
			$data = $this->xmlrpc->display_response();
			foreach($data as $model_name => $table_rows){
				$table_rows = (array) json_decode($table_rows);
				foreach($table_rows as $table_row){
					$table_row = (array)$table_row;
					eval("\$this->".$model_name."->populate(\$table_row);");
					eval("\$this->".$model_name."->insert();");
					$error_no = $this->db->_error_number();
					if($error_no!=1062 && $error_no!=0){     //1062 = duplicate entry error, 0 - no error
						$success = FALSE;
						break 2;
					}
				}
			}
			
			if($success === TRUE){
				 $this->db->trans_commit();
				 $this->xmlrpc->method('update');
				
				$request = array($_REQUEST['user'],md5($_REQUEST['password']));
				$this->xmlrpc->request($request);	
				
				if ( ! $this->xmlrpc->send_request())
				{
					echo "{'success':false,'msg':'".$this->xmlrpc->display_error()."'}";
				}
				else
				{
					echo "{'success':true,'msg':'Online data successfully replicated on main server','error_code':'40'}";
				}
			}	
			else{
				$this->db->trans_rollback();
				echo "{'success':false,'msg':'Online data not successfully replicated on main server','error_code':'41'}";
			}
		}
	}
	
	function test(){
		$models = array(
				'onlineattachment_model'
				,'onlinebulletinboard_model'
				,'onlinecapitaltransactiondetail_model'
				,'onlinecapitaltransactionheader_model'
				,'onlineloan_model'
				,'onlineloanpayment_model'
				,'onlineloanpaymentdetail_model'
				,'onlinememberrequestdetail_model'
				,'onlinememberrequestheader_model'
				,'onlinepayrolldeduction_model'
				,'onlinerequestapprover_model'
				,'onlineworkflow_model'
			);
			
			$this->db->trans_start();
			foreach($models as $model_name){
				eval("\$this->".$model_name."->setValue('status_flag','2');");
				eval("\$this->".$model_name."->update(array('status_flag'=>'1'));");
				echo $this->db->last_query()."<br>";
			}
			
			$this->db->trans_complete();
			if ($this->db->trans_status() === FALSE){
				echo 'Online data not updated';
			}
			else {
				echo 'Online data updated';
			}	
		/*$oAtt = $this->onlineattachment_model->get_list();
		echo '<pre>';
		var_dump($oAtt['list']);
		echo '</pre>';
		$data = array(
			"onlineattachment_model" => array()
			,"onlinebulletinboard_model" => array()
			,"onlinecapitaltransactionheader_model" => array(
				0 => array(
					"transaction_no" => "000123"
					,"created_by"=>"PECA"
			        ,"request_no" => '123'	
				)
				,1 => array(
					"transaction_no" => "000132"
					,"created_by"=>"PECA"
			        ,"request_no" => '123'	
				)
				,2 => array(
					"transaction_no" => "000144"
					,"created_by"=>"PECA"
			        ,"request_no" => '123'	
				)
			)
			,"onlinepayrolldeduction_model" => array(
				0 => array(
					"request_no" => "77"
					,"created_by"=>"PECA"
				)
				,1 => array(
					"created_by"=>"PECA"
			        ,"request_no" => '31'	
				)
				,2 => array(
					"created_by"=>"PECA"
			        ,"request_no" => '30'	
				)
			)
		);
		$this->db->trans_begin();
		$success = TRUE;
		
		foreach($data as $model_name => $table_rows){
				foreach($table_rows as $table_row){
					eval("\$this->".$model_name."->populate(\$table_row);");
					eval("\$this->".$model_name."->insert();");
					$error_no = $this->db->_error_number();
					if($error_no!=1062 && $error_no!=0){     //1062 = duplicate entry error, 0 - no error
						$success = FALSE;
						break 2;
					}
				}
			}
		if($success === TRUE){
				 $this->db->trans_commit();
				 echo "{'success':true,'msg':'Online data successfully replicated on main server','error_code':''}";
			}	
			else{
				$this->db->trans_rollback();
				echo "{'success':false,'msg':'Online data not successfully replicated on main server','error_code':''}";
			}*/
	}
	
}
?>