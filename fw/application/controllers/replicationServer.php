<?php

class replicationServer extends Controller {
	
	function replicationServer(){
		parent::Controller();
		set_time_limit(10000);
		$this->load->library('xmlrpc');
		$this->load->library('xmlrpcs');
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
		$this->load->model('onlineworkflow_model');
		$this->load->model('parameter_model');
		//20110805 fix for unsync co-maker admin vs online
		$this->load->model('mcloanguarantor_model');
		
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
		$config['functions']['read'] = array('function' => 'replicationServer.read');
		$config['functions']['update'] = array('function' => 'replicationServer.update');
		$config['functions']['readOnlineData'] = array('function' => 'replicationServer.readOnlineData');
		$config['functions']['updateOnlineData'] = array('function' => 'replicationServer.updateOnlineData');
		$config['functions']['insert'] = array('function' => 'replicationServer.insert');
		$this->xmlrpcs->initialize($config);
		$this->xmlrpcs->serve();
	}
	
	function read($request){		
		if($this->authenticate($request)){
			$oAtt = array();
			$param = array('status_flag' => '1');
			$date_tomorrow = date("Ymd", strtotime("+ 1 day"))."000000";
			//if Monday, retrieve data starting Friday
			if(date('N')=='1'){
				$date_yesterday = date("Ymd", strtotime("- 3 day"))."000000";	
			}
			else{
				$date_yesterday = date("Ymd", strtotime("- 1 day"))."000000";
			}
			$oAttParam = $param;
			$oAttParam['topic_id <>'] = '0';
			$oAtt = $this->onlineattachment_model->get_list($oAttParam);
			//$oBBoard = $this->onlinebulletinboard_model->get_list($param);
			$oCapTranDtl = $this->onlinecapitaltransactiondetail_model->get_list($param);
			$oCapTranHdr = $this->onlinecapitaltransactionheader_model->get_list($param);
			$oLoan = $this->onlineloan_model->get_list($param);
			$oLoanPayment = $this->onlineloanpayment_model->get_list($param);
			$oLoanPaymentDtl = $this->onlineloanpaymentdetail_model->get_list($param);
			$oMemReqDtl = $this->onlinememberrequestdetail_model->get_list($param);
			$oMemReqHdr = $this->onlinememberrequestheader_model->get_list($param);
			$oPayDed = $this->onlinepayrolldeduction_model->get_list($param);
			$oReqApp = $this->onlinerequestapprover_model->get_list($param);
			$rUser = $this->user_model->get_list("modified_date >= $date_yesterday AND modified_date < $date_tomorrow AND 
			group_id='USER'", null, null, array('user_id', 'password', 'modified_by', 'modified_date', 'login_attempt'));
			$rUserPasswords = $this->userpasswords_model->get_list("modified_date >= $date_yesterday AND modified_date < $date_tomorrow");
			/*$iParamList = $this->parameter_model->get_list("modified_date >= $date_yesterday AND modified_date < $date_tomorrow 
			AND parameter_id='TOPICID'");*/
			
			$oAtt = json_encode($oAtt['list']);
			//$oBBoard = json_encode($oBBoard['list']);
			$oCapTranDtl = json_encode($oCapTranDtl['list']);
			$oCapTranHdr = json_encode($oCapTranHdr['list']);
			$oLoan = json_encode($oLoan['list']);
			$oLoanPayment = json_encode($oLoanPayment['list']);
			$oLoanPaymentDtl = json_encode($oLoanPaymentDtl['list']);
			$oMemReqDtl = json_encode($oMemReqDtl['list']);
			$oMemReqHdr = json_encode($oMemReqHdr['list']);
			$oPayDed = json_encode($oPayDed['list']);
			$oReqApp = json_encode($oReqApp['list']);
			$rUser = json_encode($rUser['list']);
			$rUserPasswords = json_encode($rUserPasswords['list']);
			//$iParamList = json_encode($iParamList['list']);
			
			$response = array(
								array(
										'onlineattachment_model' => array($oAtt, 'string')
										//,'onlinebulletinboard_model'  => array($oBBoard, 'string')
										,'onlinecapitaltransactiondetail_model' => array($oCapTranDtl, 'string')
										,'onlinecapitaltransactionheader_model' => array($oCapTranHdr, 'string')
										,'onlineloan_model' => array($oLoan, 'string')
										,'onlineloanpayment_model' => array($oLoanPayment, 'string')
										,'onlineloanpaymentdetail_model'=> array($oLoanPaymentDtl, 'string')
										,'onlinememberrequestdetail_model' => array($oMemReqDtl, 'string')
										,'onlinememberrequestheader_model' => array($oMemReqHdr, 'string')
										,'onlinepayrolldeduction_model'=>array($oPayDed, 'string')
										,'onlinerequestapprover_model' => array($oReqApp, 'string')
										,'user_model' => array($rUser, 'string')
										,'userpasswords_model' => array($rUserPasswords, 'string')
										//,'parameter_model' => array($iParamList, 'string')
									),'struct'
								);
			return $this->xmlrpc->send_response($response);
		}
		else{
			return $this->xmlrpc->send_error_message('1', 'Invalid username/password!');
		}
	}
	
	/**
	 * @desc Sets all new online data(status 1) to submitted(status 2) 
	 */
	function update($request){
		if($this->authenticate($request)){
			$models = array(
				'onlinecapitaltransactiondetail_model'
				,'onlinecapitaltransactionheader_model'
				,'onlineloan_model'
				,'onlineloanpayment_model'
				,'onlineloanpaymentdetail_model'
				,'onlinememberrequestdetail_model'
				,'onlinememberrequestheader_model'
				,'onlinepayrolldeduction_model'
				,'onlineattachment_model'
				//,'onlinebulletinboard_model'
			);
			
			$this->db->trans_start();
			foreach($models as $model_name){
				eval("\$this->".$model_name."->setValue('status_flag','3');");
				eval("\$this->".$model_name."->update(array('status_flag'=>'1'));");
			}
			
			$this->db->trans_complete();
			if ($this->db->trans_status() === FALSE){
				return $this->xmlrpc->send_error_message('2', 'Online data not successfully updated');
			}
			else {
				return $this->xmlrpc->send_response(array(0));
			}	
		}
		return $this->xmlrpc->send_error_message('1', 'Invalid username/password!');
	}

	function __unserialize($sObject) {
   
	    $__ret =preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $sObject );
   	    return unserialize($__ret);
   	}
	
	function readOnlineData($request){
		if($this->authenticate($request)){
			
			//get modelname
			$request_data = $request->output_parameters();
			$model_name = $request_data[2];
		
			$param = array('status_flag' => '1');
			$date_tomorrow = date("Ymd", strtotime("+ 1 day"))."000000";
			//if Monday, retrieve data starting Friday
			if(date('N')=='1'){
				$date_yesterday = date("Ymd", strtotime("- 3 day"))."000000";	
			}
			else{
				$date_yesterday = date("Ymd", strtotime("- 1 day"))."000000";
			}
			
			$data_to_replicate = array(); 
			$record_count = 0;
			
			if($model_name == 'user_model'){
				
				$page = isset($request_data[3])? ($request_data[3]*50): 0 ;
				$data = $this->user_model->get_list("modified_date >= $date_yesterday AND modified_date < $date_tomorrow AND 
				group_id='USER'", $page, 50, array('user_id', 'password', 'modified_by', 'modified_date', 'login_attempt'));
				$data_to_replicate[0] = json_encode($data['list']);
				$record_count = count($data['list']);
				$total_record_count = $data['count'];
			
			}else if($model_name == 'userpasswords_model'){
			
				$page = isset($request_data[3])? ($request_data[3]*50): 0 ;
				$data = $this->userpasswords_model->get_list("modified_date >= $date_yesterday AND modified_date < $date_tomorrow", $page, 50);
				$data_to_replicate[0] = json_encode($data['list']);
				$record_count = count($data['list']);
				$total_record_count = $data['count'];
				
			}else if( $model_name == 'onlineattachment_model'){
				
				$oAttParam = $param;
				$oAttParam['topic_id <>'] = '0';
				$data = $this->onlineattachment_model->get_list($oAttParam, 0, 50);
				$data_to_replicate[0] = json_encode($data['list']);
				$record_count = count($data['list']);
				$total_record_count = $data['count'];
				
			}else{
			
				eval("\$data = \$this->".$model_name."->get_list(\$param, 0, 50);");
				$data_to_replicate[0] = json_encode($data['list']);
				$record_count = count($data['list']);
				$total_record_count = $data['count'];
			}
			
			$data_to_replicate[1] = 'string';
			$response = array(
							array(
								$model_name => $data_to_replicate
								,'count' => $record_count
								,'total_count' => $total_record_count
							),'struct'
						);
			
			return $this->xmlrpc->send_response($response);
		}
		else{
			return $this->xmlrpc->send_error_message('1', 'Invalid username/password!');
		} 
	}
	
	function updateOnlineData($request){
		log_message('debug','newUpdateOL');
		if($this->authenticate($request)){
			$parameters = $request->output_parameters();
			$data_to_update = $this->__unserialize($parameters['2']);
			$this->db->trans_start();
			
			$model_name = $data_to_update['model'];
			$pkArray = $data_to_update['PK'];
			$no_of_data_to_update = $data_to_update['no_of_data_updated'];
			unset($data_to_update['PK']);
			unset($data_to_update['model']);
			unset($data_to_update['no_of_data_updated']);
			// log_message('debug','table_rows'.var_export($table_rows,true));
			log_message('debug','pkArray'.var_export($pkArray,true));
			log_message('debug','data_to_update'.var_export($data_to_update,true));
			$i = 0;
			
			$i = 0;
			while($i < $no_of_data_to_update){
				$cond = array();
				foreach($pkArray as $key => $value){
					$cond[$value] = $data_to_update['data'][$value][$i];
				}	
				eval("\$this->".$model_name."->setValue('status_flag','3');");
				eval("\$this->".$model_name."->update(\$cond);");
				$i++;
			}
			
			$this->db->trans_complete();
			if ($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				return $this->xmlrpc->send_error_message('2', 'Online data not successfully updated');
			}
			return $this->xmlrpc->send_response(array(0));
		}
		return $this->xmlrpc->send_error_message('1', 'Invalid username/password!');
	} 
	
	/**
	 * @desc Inserts main server data to online database
	 */
	function insert($request){
		$parameters = $request->output_parameters();
		$response = array(0);
		
		if($this->authenticate($request)){
			$mainArray = $this->__unserialize($parameters['2']);
			$this->db->trans_start();
			foreach($mainArray as $model_name => $table_rows){
				log_message('debug', 'start model: '.$model_name);
				//20110805 fix for unsync co-maker admin and co-maker online 
				//this deletes the non-existing 
				//co-maker in the online database that exist admin db
				if($model_name == 'mcloanguarantor_model'){
					$this->deleteComaker($table_rows);
					//continue;
				}
				eval("\$pkArray = \$this->".$model_name."->get_pk();");
				foreach($table_rows as $table_row){
					$cond = array();
					foreach($pkArray as $pk){
						$cond[$pk] = $table_row[$pk];
					}
					eval("\$result = \$this->".$model_name."->checkDuplicateKeyEntry(\$cond);");
					if($result['error_code']==0){
						eval("\$this->".$model_name."->populate(\$table_row);");
						eval("\$this->".$model_name."->insert();");
					}	
					else{
						eval("\$this->".$model_name."->populate(\$table_row);");
						eval("\$this->".$model_name."->update(\$cond);");
					}			
				}
			log_message('debug', 'finished model: '.$model_name);	
			}
			$this->db->trans_complete();
			log_message('debug', 'trans complete');	
			if ($this->db->trans_status() === FALSE){
				return $this->xmlrpc->send_error_message('2', 'Online data not successfully updated');
				log_message('debug', 'status false');
			}			
			log_message('debug', 'status true');	
			return $this->xmlrpc->send_response($response);
		}
		return $this->xmlrpc->send_error_message('1', 'Invalid username/password!');
	}
	
	//20110805 fix for unsync co-maker admin and co-maker online 
	//this deletes the non-existing 
	function deleteComaker($table_rows){
		
		foreach($table_rows as $table_row){
			$result = $this->mloanguarantor_model->delete(array(
													'loan_no' => $table_row['loan_no']
												   ,'guarantor_id' => $table_row['guarantor_id']
												   ,'status_flag' => '2')
												);
		}		
	
	}
	/**
	 * @desc Checks sent username / password in database
	 */
	function authenticate($request){
		$parameters = $request->output_parameters();
		$username = $parameters[0];
		$password = $parameters[1];
		
		$data = $this->user_model->get(
			array('user_id'=>$username, 'password'=>$password)
			,'COUNT(*) AS count'
		);
		
		if($data['list'][0]['count']>0)
			return 1;
		else 
			return 0;
	}

	
}
?>