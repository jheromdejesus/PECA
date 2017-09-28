<?php

class replicationClient extends Controller {
	var $error_message = "";
	function replicationClient(){
		parent::Controller();
		set_time_limit(10000);
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
	
		$this->load->model('parameter_model');
		
		$this->load->model('capitalcontribution_model');
		
		$this->load->model('beneficiary_model');
		$this->load->model('member_model');
		$this->load->model('supplier_model');
		$this->load->model('supplierinfo_model');
		
		$this->load->model('account_model');
		$this->load->model('amlamap_model');
		$this->load->model('company_model');
		$this->load->model('glentrydetail_model');
		$this->load->model('glentryheader_model');
		$this->load->model('information_model');
		$this->load->model('loancodepaymenttype_model');
		$this->load->model('loancodedetail_model');
		$this->load->model('loancodeheader_model');
		$this->load->model('transactiongroup_model');
		$this->load->model('transactioncode_model');
		$this->load->model('transactioncharges_model');
		$this->load->model('user_model');
		$this->load->model('userpasswords_model');
		$this->load->model('usergroup_model');
		$this->load->model('mcaptrandetail_model');
		$this->load->model('mcaptranheader_model');
		$this->load->model('mcjournaldetail_model');
		$this->load->model('mcjournalheader_model');
		$this->load->model('minvestmentdetail_model');
		$this->load->model('minvestmentheader_model');
		$this->load->model('mjournaldetail_model');
		$this->load->model('mjournalheader_model');
		$this->load->model('mloan_model');
		$this->load->model('mloancharges_model');
		$this->load->model('mloanguarantor_model');
		$this->load->model('mloanpayment_model');
		$this->load->model('mloanpaymentdetail_model');
		$this->load->model('mtransaction_model');
		$this->load->library('xmlrpc');
		//$this->xmlrpc->set_debug(TRUE);
	}
	
	function index(){
		
	}
	//to do: remove encryption after retrieving password, password should have already been encrypted in database
	function replicateOnline()
	{	
		//$server_url = "192.36.253.228/replicationServer";
		$server_url = "https://peca.com.ph/replicationServer";	
		
		$this->xmlrpc->server($server_url, 80);
		$this->xmlrpc->method('read');
		//$this->xmlrpc->set_debug(TRUE);
	
		$request = array('peca', $this->getPassword('peca'));
		$this->xmlrpc->request($request);	
		
		if ( ! $this->xmlrpc->send_request()) //Online data not successfully retrieved
		{
			log_message('error',$this->xmlrpc->display_error());
			 echo "{'success':false,'msg':'Online data not successfully retrieved.'}";
		}
		else
		{	//insert all data (except r_user w/c only updates) from online to main server database
			$this->db->trans_begin();
			$data = $this->xmlrpc->display_response();
			foreach($data as $model_name => $table_rows){
				$table_rows = (array) json_decode($table_rows);
				eval("\$pkArray = \$this->".$model_name."->get_pk();");
				foreach($table_rows as $table_row){
					$table_row = (array)$table_row;
					
					$cond = array();
					foreach($pkArray as $pk){
						$cond[$pk] = $table_row[$pk];
					}
					
					eval("\$result = \$this->".$model_name."->checkDuplicateKeyEntry(\$cond);");
					if($result['error_code']==0 && $model_name!='user_model'){
						eval("\$this->".$model_name."->populate(\$table_row);");
						eval("\$this->".$model_name."->setValue('status_flag', '3');");
						eval("\$this->".$model_name."->insert();");
					}
					else if($result['error_code']==1 && ($model_name=='user_model' || $model_name=='parameter_model')){
						if($model_name=='user_model'){
							$this->user_model->populate($table_row);
							$this->user_model->update(array('user_id'=>$table_row['user_id']));
						}
						else if($model_name=='parameter_model'){
							$this->parameter_model->populate($table_row);
							$this->parameter_model->update(array('parameter_id'=>$table_row['parameter_id']));
						}
					}
				}
			}
			$this->db->trans_complete();
			
			if($this->db->trans_status() === TRUE){ //online data is successfully inserted to main server database
				 $this->xmlrpc->method('update');
				
				$request = array('peca',$this->getPassword('peca'));
				$this->xmlrpc->request($request); //tell online server to set all new online data to processed	
				
				if ( ! $this->xmlrpc->send_request())
				{
					log_message('error',$this->xmlrpc->display_error()); //new online data not successfully updated to processed			
					  echo "{'success':false,'msg':'Online data not successfully updated.'}";
				}
				else
				{
					log_message('info', 'Online data successfully replicated on main server');
					echo "{'success':true,'msg':'Online data successfully replicated on main server.'}";
				}
			}	
			else{
				$this->db->trans_rollback();
				log_message('error','Online data not successfully replicated on main server');		
				echo "{'success':false,'msg':'Online data not successfully replicated on main server.'}";
			}
		}
	}
	
	/**
	 * @desc Copy all master, reference and online data yesterday and today to online server
	 */
	function replicateMain(){
		//$server_url = "192.36.253.228/replicationServer";
		$server_url = "https://peca.com.ph/replicationServer";
			
		$this->xmlrpc->server($server_url, 80);
		$this->xmlrpc->method('insert');
		
		//$this->xmlrpc->set_debug(TRUE,3);		
		
		/*$request = array('peca', $this->getPassword('peca'),$this->getMainData()); //get master, reference and online from main database
		$this->xmlrpc->request($request);	
		
		if ( ! $this->xmlrpc->send_request()) //Main data not successfully replicated on online server
		{
			log_message('error',$this->xmlrpc->display_error());
			echo "{'success':false,'msg':'Main data not successfully replicated on online server.'}";
		}
		else
		{
			log_message('info', 'Main data successfully replicated on online server');
			echo "{'success':true,'msg':'Main data successfully replicated on online server.'}";
		}*/
		
		if($this->getMainData()){
			echo "{'success':true,'msg':'Main data successfully replicated on online server.'}";
			log_message('debug', 'Main data successfully replicated on online server.');
		}
		else{
			echo "{'success':false,'msg':'Main data not successfully replicated on online server. ".$this->error_message.".'}";
			log_message('debug', 'Main data not successfully replicated on online server. '.$this->error_message.'.');
		}
	}
	
	/**
	 * @desc Return all master, reference and online data which were modified today and yesterday
	 */
	function getMainData(){
		$models = array(
			'onlineattachment_model',
			'onlinebulletinboard_model',
			'onlinecapitaltransactiondetail_model',
			'onlinecapitaltransactionheader_model', 
			'onlineloan_model', 
			'onlineloanpayment_model', 
			'onlineloanpaymentdetail_model', 
			'onlinememberrequestdetail_model',
			'onlinememberrequestheader_model',
			'onlinepayrolldeduction_model', 
			'onlinerequestapprover_model',
			'onlineworkflow_model', 
			'beneficiary_model',  
			'member_model',  
			'supplier_model',  
			'supplierinfo_model',   
			'account_model', 
			//'amlamap_model', 
			'company_model', 
			'glentrydetail_model',  
			'glentryheader_model',  
			'information_model', 
			'loancodepaymenttype_model',
			'loancodedetail_model',
			'loancodeheader_model',
			'transactiongroup_model',
			'transactioncode_model',
			'transactioncharges_model',
			'user_model',
			'userpasswords_model',
			'usergroup_model',
			'mcaptrandetail_model',
			'mcaptranheader_model',
			'mcjournaldetail_model',
			'mcjournalheader_model',
			'minvestmentdetail_model',
			'minvestmentheader_model',
			'mjournaldetail_model',
			'mjournalheader_model',
			'mloan_model',
			'mloancharges_model',
			'mloanguarantor_model',
			'mloanpayment_model',
			'mloanpaymentdetail_model',
			'mtransaction_model',
			'parameter_model',
			'capitalcontribution_model'
		);
		//if Monday, retrieve data starting Friday
		if(date('N')=='1'){
			$min_date = date('Ymd', strtotime("-3 day"))."000000";	
		}
		else{
			$min_date = date('Ymd', strtotime("-23 day"))."000000";
		}
		
		$max_date = date('Ymd', strtotime("+1 day"))."000000";
		$replicateSuccess = true;
		$password = $this->getPassword('peca');
		
		foreach($models as $model){
			$data = array();
			$mainArray = array();
			$param = array('modified_date >='=>$min_date, 'modified_date <'=>$max_date);
			
			if($model == 'parameter_model'){
				$param['parameter_id !='] = 'OREQ';
			}
			else if($model == 'user_model'){
				$param['is_admin'] = '0';
			}
			eval("\$data = \$this->".$model."->get_list(\$param);");
			//$mainArray[$model] = $data['list'];
			
			/*experiment*/
			$index = 0;
			for ($index; $index<$data['count']; $index = $index + 100){
				$offset = $index;
				$limit = 100;
				
				eval("\$data = \$this->".$model."->get_list(\$param, \$offset, \$limit);");
				$mainArray[$model] = $data['list'];
				$mainArrayString = serialize($mainArray);
				
				$request = array('peca', $password, $mainArrayString); //get master, reference and online from main database
				$this->xmlrpc->request($request);	
				
				if ( ! $this->xmlrpc->send_request()) //Main data not successfully replicated on online server
				{
					$replicateSuccess = false;
					log_message('debug', 'Model ' .$model. ' failed. '.addslashes($this->xmlrpc->display_error()));
					$this->error_message = addslashes($this->xmlrpc->display_error());
					break 2;
				}			
			}
			/*experiment*/
		}
		return $replicateSuccess;
		//return serialize($mainArray);
	}
	
	/**
	 * @desc Retrieves password of a specific user id
	 */
	function getPassword($user_id){
		$data = $this->user_model->get(array('user_id' => $user_id), 'password');
		if(isset($data['list'][0]['password']))
			return 	$data['list'][0]['password'];
		else
			return "";
	}
	
}
?>