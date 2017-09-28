<?php

class replicationClient extends Controller {
	var $error_message = "";
	var $replication_try = 0;
	var $replication_try_update_OLTrans = 0;
	
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
		//20110805 fix for unsync co-maker admin vs online
		$this->load->model('mcloanguarantor_model');
		$this->load->library('xmlrpc');
		$this->load->library('email');
		//$this->xmlrpc->set_debug(TRUE);
		//[start] 0008164 addded by asi466
		$this->load->model('dividend_model');
		//[end] 0008164 addded by asi466
		//[start] 0008327 addded by asi466 on 20110915
		$this->load->model('transaction_model');
		//[end] 0008327 addded by asi466 on 20110915
		//[start] 0008327 addded by asi466 on 20111104
		$this->load->model('tblnpmlsuspension_model');
		//[end] 0008327 addded by asi466 on 20111104
	}
	
	function index(){
		
	}
	
	
	//to do: remove encryption after retrieving password, password should have already been encrypted in database
	function replicateOnline()
	{	
		$server_url = "http://192.168.1.62/replicationServer";
		// $server_url = "https://192.36.253.55/replicationServer";	
		//$server_url = "https://pgpsla.com/replicationServer";
		
		//$server_url = "https://peca.com.ph/replicationServer";	
		 //$this->xmlrpc->set_debug(TRUE);
		$this->xmlrpc->server($server_url, 80);
	
		$table_models = array(
			'onlineattachment_model'
			,'onlinecapitaltransactiondetail_model'
			,'onlinecapitaltransactionheader_model'
			,'onlineloan_model'
			,'onlineloanpayment_model'
			,'onlineloanpaymentdetail_model'
			,'onlinememberrequestdetail_model'
			,'onlinememberrequestheader_model'
			,'onlinepayrolldeduction_model'
			,'onlinerequestapprover_model'
			,'user_model'
			,'userpasswords_model'
		);

		
		$request = array('peca', $this->getPassword('peca'));
		
		$model_pointer = 0;
		$model = $table_models[$model_pointer];
		$paging = 0;
		while($model != ''){
			$this->xmlrpc->method('readOnlineData');
			$request[2] = $model;
			
			$this->xmlrpc->request($request);
			
			if ( ! $this->xmlrpc->send_request())
			{
				$this->error_message = addslashes($this->xmlrpc->display_error());
				if($this->replication_try == 5){
						if($this->error_message == 'No data received from server.' || $this->error_message == "Did not receive a '200 OK' response from remote server." ){
							echo "{'success':false,'msg':'Online data not successfully retrieved on main server. <br> Please try again. An internet connection issue has been encountered.'}";
						}else if($this->error_message == 'Invalid username/password!'){
							echo "{'success':false,'msg':'Online data not successfully retrieved. Invalid username/password! '}";
						}else{
							echo "{'success':false,'msg':'Online data not successfully retrieved.'}";
						}
						
					log_message('error',$this->xmlrpc->display_error());
					$this->sendReplicationNotification("Online to Main Replication");
					break;
				}else{
					log_message('debug',$this->replication_try.' try:online to server');
					$this->replication_try++;
					continue;
				}
				
			}else{
				$this->replication_try = 0;//reset counter
				$this->error_message = "";
				
				$this->db->trans_begin();
				
				$onlineData = $this->xmlrpc->display_response();
				$onlineDataCount = $onlineData['count'];
				$onlineDataTotalCount = $onlineData['total_count'];
				log_message('debug','$onlineDataCount'.var_export($onlineData,true));
				unset($onlineData['count']);
				unset($onlineData['total_count']);
				
				//insert update data here
				unset($update_replicated_data);
				$update_replicated_data = $this->insertUpdateOnlineRecord($onlineData,$model);
				
				
				//only update the applications 
				if( $model != 'user_model'
				&& $model != 'userpasswords_model'
				){
					$this->xmlrpc->method('updateOnlineData');
					$update_replicated_data_string = serialize($update_replicated_data);
					$request = array('peca',$this->getPassword('peca'),$update_replicated_data_string);
					$this->xmlrpc->request($request); //tell online server to set all new online data to processed	
					while($this->replication_try_update_OLTrans < 5){	
						if ( ! $this->xmlrpc->send_request())
						{	
							$this->error_message = addslashes($this->xmlrpc->display_error());
							if($this->error_message == 'Online data not successfully updated'){
								$this->db->trans_rollback();
								break;
							}
							log_message('debug',$this->replication_try_update_OLTrans.' try: replication_try_update_OLTrans');
							$this->replication_try_update_OLTrans++;
							continue;
							
						}else{
							$this->error_message = "";
							$this->replication_try_update_OLTrans = 0;
							break;
						}
					}
				}
				
				if($this->replication_try_update_OLTrans == 5){
					log_message('error',$this->xmlrpc->display_error()); //new online data not successfully updated to processed
					$this->error_message = addslashes($this->xmlrpc->display_error());
					
					if($this->error_message == 'No data received from server.' || $this->error_message == "Did not receive a '200 OK' response from remote server." ){
						echo "{'success':false,'msg':'Online data not successfully updated. <br> Please try again. An internet connection issue has been encountered.'}";
					}else if($this->error_message == 'Invalid username/password!'){
						echo "{'success':false,'msg':'Online data not successfully retrieved. Invalid username/password! '}";
					}else if($this->error_message == 'Online data not successfully updated'){
						echo "{'success':false,'msg':'Online data not successfully replicated on main server.'}";
					}else{			
						echo "{'success':false,'msg':'Online data not successfully updated.'}";
					}
					
					$this->db->trans_rollback();
					$this->db->trans_complete();
					$this->sendReplicationNotification("Online to Main Replication");
					break;
				}
				
				$this->db->trans_complete();
				
				if($this->db->trans_status() === TRUE){
					$b_flag = FALSE;
					if($model == 'user_model'
					|| $model == 'userpasswords_model'
					){
						$paging += 1;
						if($paging < ceil($onlineDataTotalCount/50)){
							log_message('debug','paging'.$paging);
							$request[3] = $paging;
							$model = $model;
						}else{
							$b_flag = TRUE;
						}
					}else{
						if(($onlineDataTotalCount - $onlineDataCount) > 0){
							$model = $model;
						}else{
							$b_flag = TRUE;
						}
					}
					
					if($b_flag){
						log_message('debug','model_pointer:'.$model_pointer);
						if($model_pointer >= count($table_models)){
							$model = '';
							break;
						}else{
							unset($request[3]);
							$paging = 0;
							$model = $table_models[$model_pointer++];
						}
					}
					
				}else{
					$this->db->trans_rollback();
					log_message('error','Online data not successfully replicated on main server');
					$this->sendReplicationNotification("Online to Main Replication");
					echo "{'success':false,'msg':'Online data not successfully replicated on main server.'}";
					break;
				} 
			}
		}
		
		if(empty($this->error_message)){
			log_message('info', 'Online data successfully replicated on main server');
			echo "{'success':true,'msg':'Online data successfully replicated on main server.'}";
			$this->sendSuccessfulReplicationNotification("Online to Main Replication");
		}
	}
	
	function insertUpdateOnlineRecord($onlineData ,$model_name ){		
		log_message('debug','insertUpdateOnlineRecord');
		$table_rows = (array) json_decode($onlineData[$model_name]);
		eval("\$pkArray = \$this->".$model_name."->get_pk();");
		
		$update_replicated_data = array();
		$update_replicated_data['PK'] = $pkArray;
		$update_replicated_data['model'] = $model_name;
		$update_replicated_data['no_of_data_updated'] = count($table_rows);
		$pk_array_values = array();
		foreach($table_rows as $table_row){
			$table_row = (array)$table_row;
			$cond = array();
			$i = 0;
			foreach($pkArray as $pk){
				$cond[$pk] = $table_row[$pk];
				$pk_array_values[$pkArray[$i]][] = $cond[$pkArray[$i]];
				$i++;
			}
			
			$update_replicated_data['data'] = $pk_array_values;
			
			eval("\$result = \$this->".$model_name."->checkDuplicateKeyEntry(\$cond);");
			if($result['error_code'] == 0 && $model_name != 'user_model'){
				eval("\$this->".$model_name."->populate(\$table_row);");
				eval("\$this->".$model_name."->setValue('status_flag', '3');");
				eval("\$this->".$model_name."->insert();");
			}
			
			else if($result['error_code']==1 && ($model_name=='user_model' || $model_name == 'parameter_model')){
			
				if( $model_name == 'user_model' ){
					$this->user_model->populate($table_row);
					$this->user_model->update(array('user_id'=>$table_row['user_id']));
				}
				
				else if( $model_name == 'parameter_model' ){
					$this->parameter_model->populate($table_row);
					$this->parameter_model->update(array('parameter_id'=>$table_row['parameter_id']));
				}
			}
		}
		return $update_replicated_data;
	}
	
	/**
	 * @desc Copy all master, reference and online data yesterday and today to online server
	 */
	function replicateMain(){
		$server_url = "192.168.1.62/replicationServer";
		//$server_url = "https://peca.com.ph/replicationServer";
		//$server_url = "https://pgpsla.com/replicationServer";
		//$server_url = "https://peca.com.ph/replicationServer";	
		
		//$server_url = "http://localhost/replicationServer";	
		 //20110805 for log
		log_message('debug', 'start server to online replication: '); 	
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
			$this->sendSuccessfulReplicationNotification("Main to Online Replication");
		}
		else{
			if($this->error_message == 'No data received from server.' || $this->error_message == "Did not receive a '200 OK' response from remote server."){
				echo "{'success':false,'msg':'Main data not successfully replicated on online server. <br> Please try again. An internet connection issue has been encountered.'}";
			}else{
				echo "{'success':false,'msg':'Main data not successfully replicated on online server. ".$this->error_message."'}";
			}
			$this->sendReplicationNotification("Main to Online Replication");
			log_message('debug', 'Main data not successfully replicated on online server. '.$this->error_message.'.');
		}
		//20110805 for debug
		log_message('debug', 'end server to online replication: ');
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
			//20110805 fix for unsync co-maker admin vs online
			'mcloanguarantor_model',
			'mloanguarantor_model',
			'mloanpayment_model',
			'mloanpaymentdetail_model',
			'mtransaction_model',
			'parameter_model',
			'capitalcontribution_model',			
			//[start] 0008164 addded by asi466
			'dividend_model',			
			//[end] 0008164 addded by asi466
			//[start] 0008327 addded by asi466 on 20110915
			'transaction_model',	
			//[end] 0008327 addded by asi466 on 20110915
			//[start] 0008327 addded by asi466 on 20111104
			'tblnpmlsuspension_model'			
			//[end] 0008327 addded by asi466 on 20111104
			
		);
		//if Monday, retrieve data starting Friday
		if(date('N')=='1'){
			$min_date = date('Ymd', strtotime("-368 day"))."000000";	
		}
		else{
			$min_date = date('Ymd', strtotime("-365 day"))."000000";
		}
		
		$max_date = date('Ymd', strtotime("+1 day"))."000000";
		$replicateSuccess = true;
		$password = $this->getPassword('peca');
		
		foreach($models as $model){
			//20110808
			log_message('debug', 'server to online start model: '.$model);
			
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
			$replication_try = 0;
			for ($index; $index<$data['count']; $index = $index + 50){
				$offset = $index;
				$limit = 50;
				
				eval("\$data = \$this->".$model."->get_list(\$param, \$offset, \$limit);");
				
				//20111104$mainArray[$model] = $data['list'];
				$mainArray[$model] = $this->replaceNewline($data['list']);;//20111028
				$mainArrayString = serialize($mainArray);
				
				$request = array('peca', $password, $mainArrayString); //get master, reference and online from main database
				$this->xmlrpc->request($request);	
				
				if ( ! $this->xmlrpc->send_request()) //Main data not successfully replicated on online server
				{
					$replicateSuccess = false;
					log_message('debug', 'Model ' .$model. ' failed. '.addslashes($this->xmlrpc->display_error()));
					$this->error_message = addslashes($this->xmlrpc->display_error());
					
					if($replication_try == 5){
						break 2;
					}else{
						log_message('debug',$replication_try.' try:admin to server');
						$index = $index - 50;
						$replication_try++;
					}
				}else{
					if( $replication_try != 0 ){
						$replicateSuccess = TRUE;
						$replication_try = 0;
					}
				}
				
			}
			//20110805 for debug
			log_message('debug', 'server to online end model: '.$model);
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
	
		
	function sendReplicationNotification($replication_type){
		$this->sendEmail("jheromdejesus@gmail.com",$this->error_message,$replication_type);
		//$this->sendEmail("ate@asi-ees.com",$this->error_message,$replication_type);
		//$this->sendEmail("klibutan@asi-dev2.com",$this->error_message,$replication_type);
		//$this->sendEmail("Peca@alliance.com.ph",$this->error_message,$replication_type);			
		//$this->sendEmail("avalle@asi-dev5.com",$this->error_message,$replication_type);			
	}
	function sendSuccessfulReplicationNotification($replication_type){
		$this->sendEmail("jheromdejesus@gmail.com","Auto replication was successful",$replication_type);
		//$this->sendEmail("ate@asi-ees.com","Auto replication was successful",$replication_type);
		//$this->sendEmail("klibutan@asi-dev2.com","Auto replication was successful",$replication_type);
		//$this->sendEmail("avalle@asi-dev5.com","Auto replication was successful",$replication_type);
				
	}
	//20111102 auto replication
	function sendEmail($email,$message_body,$replication_type) {
		$email_message = '';
		$this->email->to($email);
	    // $this->email->cc('epama@asi-dev2.com');
		$this->email->from('admin@peca.com');
		if($message_body != "Auto replication was successful"){
			$this->email->subject("Auto Replication Error : ". date("Ydm"));
		}else{
			$this->email->subject("Auto Replication Successful : ". date("Ydm"));
		}
		$email_message = "Hi PECA Dev Team  <br/><br/>";
		if($message_body != "Auto replication was successful"){
			$email_message .= "An error occurred during automatic replication (".$replication_type.") on ".date("Y/m/d g:i a")."<br/><br/> ";
			$email_message .= "Error Message: <br>";
			$email_message .= $message_body;
		}else{
			$email_message .= $message_body." (".$replication_type.") on ".date("Y/m/d g:i a")."<br/><br/> ";
		}
		$email_message .= "<br/><br/>";
		$email_message .= "Thank you.<br/>";
		
		$this->email->Message($email_message);

		if ($this->email->send()) {
			return true;
		} else {
			return false;
		}
	}
	
	
	/**
	 * @desc Return all master, reference and online data which were modified today and yesterday
	 */
	 function replaceNewline($data = array()){
		$new_data = array();
		foreach ($data as $row){
			foreach ($row as $key => $value){
				$row[$key] = str_replace("\r\n", "\n", $value);
			}
			$new_data[] = $row;
		}
		
		return $new_data;
	}
	
}
?>