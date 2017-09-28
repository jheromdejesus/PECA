<?php
/*
 * Created on Apr 22, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Online_member extends Asi_Controller {

	function Online_member(){
		parent::Asi_Controller();
		$this->load->model('Onlinememberrequestheader_model');
		$this->load->model('Onlinememberrequestdetail_model');
		$this->load->model('Member_model');
		$this->load->model('Parameter_model');
		$this->load->model('Workflow_model');
		$this->load->model('Beneficiary_model');
		$this->load->model('Loan_model');
		$this->load->helper('url');
		$this->load->library('constants');
		$this->load->model('user_model');
	}
	
	function index(){		
		
	}
	
	
	/**
	 * @desc Retrieves list of approvers for selected request -- MEMBERSHIP
	 */
	function show()
	{
		$data = $this->Workflow_model->readApproversMembership(
			array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
			,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
			,array('user1.user_name as approver1_name'
	    		,'user2.user_name as approver2_name'
	    		,'user3.user_name as approver3_name'
	    		,'user4.user_name as approver4_name'
	    		,'user5.user_name as approver5_name'
	    		)
		,null);
		
		echo json_encode(array(
			'success' => true,
            'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
        ));
	}
	
	/**
	 * @desc ReadHeader - Retrieves online requests of members
	 */
	function readHeader()
	{
		/*$_REQUEST['data'] = array('employee_id' => '01517067'
									,'last_name' => 'TABLIZO'
									,'first_name' => 'IRIS ANN'
									,'from_date' => '20090422124563'
									,'to_date' => '20110422124563'
								);	*/ 
		$curr_date = $this->Parameter_model->retrieveValue('CURRDATE');
		if(array_key_exists('request_date_from',$_REQUEST)&& $_REQUEST['request_date_from']!= "")
			$request_date_from = date("Ymd", strtotime($_REQUEST['request_date_from']));
		else
			$request_date_from = $_REQUEST['request_date_from']== ""?"00000000":date("Ymd", strtotime('-1 day',strtotime($curr_date)));
		if(array_key_exists('request_date_to',$_REQUEST)&& $_REQUEST['request_date_to']!= "")
			$request_date_to = date("Ymd", strtotime('+1 day',strtotime($_REQUEST['request_date_to'])));
		else
			$request_date_to = $_REQUEST['request_date_to']== ""?"99999999":date("Ymd", strtotime('+1 day',strtotime($curr_date)));
	    $data = $this->Onlinememberrequestheader_model->retrieveOnlineMemberRequests(
			array_key_exists('employee_id',$_REQUEST) ? $_REQUEST['employee_id'] : null
			,array_key_exists('last_name',$_REQUEST) ? $_REQUEST['last_name'] : null
			,array_key_exists('first_name',$_REQUEST) ? $_REQUEST['first_name'] : null
			//,$_REQUEST['data']['from_date']
			//,$_REQUEST['data']['to_date']
			,$request_date_from
			,$request_date_to
			,array_key_exists('status',$_REQUEST) ? $_REQUEST['status'] : 0
			,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
			,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
			,array('oh.created_date AS request_date'
				,'me.last_name'
				,'me.first_name'
				,'oa.approver1'
				,'oa.approver2'
				,'oa.approver3'
				,'oa.approver4'
				,'oa.approver5'
				,'oh.status_flag'
				,'oh.request_no'
				,'oh.employee_id'
				)
		,'oh.modified_date DESC' );//created
		foreach($data['list'] as $row => $value){
			$data['list'][$row]['request_date'] = date("mdY", strtotime($value['request_date']));
		}
		echo json_encode(array(
			'success' => true,
            'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
        ));
	}	
	
	/**
	 * @desc Retrieves beneficiaries of employees
	 */
	function readBeneficiary()
	{
		//$_REQUEST['member']['employee_id'] = '01517069';
		if(array_key_exists('member',$_REQUEST))
		{	
			$param = array('member_id' => $_REQUEST['member']['employee_id']);
			$data = $this->Member_model->retrieveEmployeeBeneficiaries(
				$param
				,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
				,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
				,array('beneficiary'
					,'relationship'
					,'beneficiary_address'
					)
			,'sequence_no');
			
		$relation = $this->constants->create_list($this->constants->member_relationship);
		
		foreach($data['list'] as $key => $val){
			$data['list'][$key]['description'] = "";
			foreach($relation as $value){
				if($val['relationship']==$value['code']){
					$data['list'][$key]['description'] = $value['name'];
					break;
				}
			}		
		}
		
		
			echo json_encode(array(
				'success' => true,
				'data' => $data['list'],
				'total' => $data['count'],
				'query' => $data['query']
			));
		}
	}
	
	/**
	 * @desc Retrieves list of requests of an employee
	 */
	function readRequest()
	{
		$_REQUEST['filter'] = array('employee_id' => $_REQUEST['employee_id'], 'request_no' => $_REQUEST['request_no']);
		//$_REQUEST['request_no']=174;
		$data = $this->Onlinememberrequestheader_model->retrieveListOfRequests(
				array_key_exists('filter',$_REQUEST) ? $_REQUEST['filter'] : null
				,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
				,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
				,array('request_no'
					,'status_flag'
					,'created_date'
					,'modified_date'
					,'member_remarks'
					,'peca_remarks'
					)
			,'request_no ASC');
		if($data['count'] != 0) {
			foreach($data['list'] as $key){
				$list = $this->Onlinememberrequestdetail_model->get_list(array('request_no' =>  $_REQUEST['request_no'])
																		,null
																		,null
																		,array('fieldname', 'value')
																		);
				$list['list']['status_flag'] = $key['status_flag'];
				$list['list']['request_no'] = $key['request_no'];
				$list['list']['member_remarks'] = $key['member_remarks'];
				$list['list']['peca_remarks'] = $key['peca_remarks'];
			}
			
			$approvers = $this->Workflow_model->readApproversMembership(
													null //start
													,null //limit
													,array('approver1'
														,'approver2'
														,'approver3'
														,'approver4'
														,'approver5'
														)
												,null);
			foreach($approvers['list'][0] as $key => $val)
				$list['list'][$key] = $val;
			echo json_encode(array(
				'success' => true,
				'data' => $list['list'],
				'total' => $list['count'],
				'query' => $list['query']
			));
		}
	}
	
	/**
	 * @desc Retrieves loans of an employee with principal balance greater than 0
	 */
	function readLoan()
	{
		$_REQUEST['filter'] = array('employee_id' => '01517338');
		$data = $this->Loan_model->retrieveLoanInformation(
				array_key_exists('filter',$_REQUEST) ? $_REQUEST['filter'] : null
				,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
				,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
				,array('loan_no'
					,'loan_code'
					,'loan_date'
					,'principal'
					,'term'
					,'interest_rate'
					,'employee_interest_amortization AS interest_amortization'
					,'employee_principal_amortization AS principal_amortization'
					,'principal_balance'
					,'(employee_interest_amortization + employee_principal_amortization) AS monthly_amortization'
					)
			,'loan_code, loan_date ASC');
		
		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
		));
	}
	
	/**
	 * @desc Retrieves guaranteed loans of employees
	 */
	function readGuarantor()
	{
		$_REQUEST['filter'] = array('ml.employee_id' => '01518726');
		$data = $this->Loan_model->retrieveGuarantorList(
				array_key_exists('filter',$_REQUEST) ? $_REQUEST['filter'] : null
				,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
				,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
				,array('ml.loan_no'
					,'ml.loan_code'
					,'ml.loan_date'
					,'me.last_name'
					,'me.first_name'
					,'me.middle_name'
					,'me.last_name + me.first_name + me.middle_name AS name'
					,'ml.principal'
					,'employee_principal_amortization'
					,'ml.employee_interest_amortization'
					,'ml.principal_balance'
					,'(employee_interest_amortization + employee_principal_amortization) AS monthly_amortization'
					)
			,null);
		
		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
		));
	}
	
	/**
	 * @desc ADMIN -- Retrieves list of members to approve
	 */
	function readReqToApprove()
	{
		$data = $this->Onlinememberrequestheader_model->retrieveRequestsToApprove(
			array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
			,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
			,array('employee_id'
	    		,'request_no'
	    		)
		,null);
		
		echo json_encode(array(
			'success' => true,
            'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
        ));
	}
	
	/**
	 * @desc saveNewChangeRequestHeader - Inserts member request header and detail
	 * @desc acts like an update
	 */
	function add()
	{
		/* $_REQUEST = array('employee_id' => '01517069'
								,'created_by' => 'WIE'
							); 					
		$_REQUEST['data'] = '[{"fieldname":"membership[address_2]","value":"Rosewood Pointe, mk"}]';
		 */
		log_message('debug', "[START] Controller online_member:add");
		log_message('debug', "online_member param exist?:".array_key_exists('data',$_REQUEST));
		$saveOrSendFlag = isset($_REQUEST['saveOrSendFlag'])?$_REQUEST['saveOrSendFlag']:"1";

		if (array_key_exists('data',$_REQUEST) && strlen($_REQUEST['data']) > 2) {
			$request_no = $this->Parameter_model->retrieveValue('OREQ')+1;	
			$saveOrSendFlag = $_REQUEST['saveOrSendFlag'];
			$dataHdr = array('employee_id' => $_REQUEST['employee_id']
								,'created_by' => $_REQUEST['created_by']
								,'member_remarks' => $_REQUEST['online_membership']['member_remarks']
							); 	
			$this->Onlinememberrequestheader_model->populate($dataHdr);
			$this->Onlinememberrequestheader_model->setValue('status_flag', $saveOrSendFlag);
			$this->Onlinememberrequestheader_model->setValue('request_no', $request_no);
			$checkDuplicate = $this->Onlinememberrequestheader_model->checkDuplicateKeyEntry();
	
			if($checkDuplicate['error_code'] == 1){
				$result['error_code'] = 2;
				$result['error_message'] = $checkDuplicate['error_message'];
			}
			else 
				$result = $this->Onlinememberrequestheader_model->insert();
				
				
			if($result['error_code'] != 0){  
				echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';
				return;
			} else 
			{
				$this->Parameter_model->updateValue(('OREQ'), $request_no, $_REQUEST['created_by']);
				if(substr($_REQUEST['data'],0,1)=='['){
					$data = json_decode(stripslashes($_REQUEST['data']),true);
					foreach($data as $key => $val){
						$data[$key] = implode("|", $val);
					}			
					$data = array_unique($data);
					foreach($data as $key => $val){
						$data[$key] = explode("|", $val); 
					}
					foreach($data as $key => $val){	
						$params['created_by'] = $_REQUEST['created_by'];
						$params['fieldname'] = $val[0];
						$params['value'] = $val[1];
						$result = $this->addDtl($params,$request_no);
						if($result['error_code'] != 0){
							echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';
							return;
						}
					}
				}	
				else
				{
					$data = json_decode(stripslashes($_REQUEST['data']),true);
					$params= array('fieldname' => $data['fieldname']
									,'value' => $data['value']
									,'created_by' => $_REQUEST['created_by']
								); 
					$result = $this->addDtl($params,$request_no);
					if($result['error_code'] != 0) {
						echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';
						return;
					}
				}
			}
			//echo "{'success':true,'msg':'Data successfully saved.'}";
			//if to save data
			if($saveOrSendFlag==2){
				echo "{'success':true,'msg':'Request successfully saved.','request_no':'".$request_no."','status':'".$saveOrSendFlag."'}";
			}
			//if to send data
			else if($saveOrSendFlag==1){
				echo "{'success':true,'msg':'Request successfully sent.','request_no':'".$request_no."','status':'".$saveOrSendFlag."'}";
			}
		}
		else{
			//echo "{'success':false,'msg':'Data was NOT successfully saved.','error_code':'2'}";
			if($saveOrSendFlag==2){
				echo "{'success':false,'msg': 'You need to have atleast 1 changed to save this request.','error_code':'1'}";
			}
			//if to send data
			else if($saveOrSendFlag==1){
				echo "{'success':false,'msg': 'You need to have atleast 1 changed to send this request.','error_code':'1'}";
			}
		}
		log_message('debug', "[END] Controller online_member:add");
	}
	
	/**
	 * @desc saveNewChangeRequestDetail - Inserts member request detail
	 */
	function addDtl($data, $request_no)
	{
		$this->Onlinememberrequestdetail_model->populate($data);
		$this->Onlinememberrequestdetail_model->setValue('status_flag', '1');
		$this->Onlinememberrequestdetail_model->setValue('request_no', $request_no);
		$checkDuplicate = $this->Onlinememberrequestdetail_model->checkDuplicateKeyEntry(
															array('request_no' => $request_no
																	,'fieldname' => $data['fieldname'])
																	);

		if($checkDuplicate['error_code'] == 1){
			$result['error_code'] = 1;
			$result['error_message'] = $checkDuplicate['error_message'];
		}
		else 
			$result = $this->Onlinememberrequestdetail_model->insert();
		return $result;
	}
	
	
	/**
	 * @desc saveNewChangeRequestHeader - Inserts member request header and detail
	 * @desc acts like an update
	 */
	function update()
	{
		/* $_REQUEST = array('employee_id' => '01517069'
								,'created_by' => 'WIE'
								,'request_no' => '122'
							); 					
		$_REQUEST['data'] = '[{"fieldname":"first_name","value":"mahahah"},{"fieldname":"last_name","value":"adadad"},{"fieldname":"middle_name","value":"mahahah"}]';
		 */
		log_message('debug', "[START] Controller online_member:add");
		log_message('debug', "online_member param exist?:".array_key_exists('data',$_REQUEST));
		
		if (array_key_exists('data',$_REQUEST)) {
			if(substr($_REQUEST['data'],0,1)=='['){
				$data = json_decode(stripslashes($_REQUEST['data']),true);
				foreach($data as $key => $val){
					$data[$key] = implode("|", $val);
				}
				$data = array_unique($data);
				foreach($data as $key => $val){
					$data[$key] = explode("|", $val); 
				}
		
				$this->Onlinememberrequestdetail_model->populate(array("request_no" => $_REQUEST['request_no']));
				$this->Onlinememberrequestdetail_model->delete();
				foreach($data as $key => $val){	
					$params['created_by'] = $_REQUEST['created_by'];
					$params['fieldname'] = $val[0];
					$params['value'] = $val[1];
					$params['status_flag'] = $_REQUEST['status'];
					$result = $this->updateDtl($params,$_REQUEST['request_no']);
					if($result['error_code'] != 0){
						echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';
						return;
					}
				}
			}	
			else
			{
				$data = json_decode(stripslashes($_REQUEST['data']),true);
				$params= array('fieldname' => $data['fieldname']
								,'value' => $data['value']
								,'created_by' => $_REQUEST['created_by']
							); 
				$params['status_flag'] = $_REQUEST['status'];
				$this->Onlinememberrequestdetail_model->populate(array("request_no" => $_REQUEST['request_no']));
				$this->Onlinememberrequestdetail_model->delete();
				$result = $this->updateDtl($params,$_REQUEST['request_no']);
				if($result['error_code'] != 0) {
					echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';
					return;
				}
			}
			$saveOrSendFlag = $_REQUEST['saveOrSendFlag'];
			$this->Onlinememberrequestheader_model->setValue('status_flag',$saveOrSendFlag );
			$this->Onlinememberrequestheader_model->setValue('member_remarks', $_REQUEST['online_membership']['member_remarks']);
			$result = $this->Onlinememberrequestheader_model->update(array('request_no'=> $_REQUEST['request_no']));
			if($result['error_code'] == 0){
				//echo "{'success':true,'msg':'Data successfully saved.'}";
				if($saveOrSendFlag==1){
				echo "{'success':true,'msg':'Request successfully sent.'}";
				}
				else if($saveOrSendFlag==2){
					echo "{'success':true,'msg':'Request successfully updated.'}";
				}
			}
			else echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';
		}
		else
			echo "{'success':false,'msg':'Request was NOT successfully updated.','error_code':'2'}";
		
		log_message('debug', "[END] Controller online_member:add");
	}
	
	/**
	 * @desc saveNewChangeRequestDetail - Inserts member request detail
	 */
	function updateDtl($data, $request_no)
	{
		$this->Onlinememberrequestdetail_model->populate($data);
		$this->Onlinememberrequestdetail_model->setValue('request_no', $request_no);
		$this->Onlinememberrequestdetail_model->setValue('fieldname', $data['fieldname']);
		
		$result = $this->Onlinememberrequestdetail_model->insert();
		return $result;
	}
	
	
	function delete()
	{
		log_message('debug', "[START] Controller online_member:delete");
		log_message('debug', "online_membership param exist?:".array_key_exists('online_membership',$_REQUEST));
		
		if (array_key_exists('online_membership',$_REQUEST)) {
			$this->Onlinememberrequestdetail_model->setValue('status_flag', '0');
			$cond = 'request_no = '.$_REQUEST['request_no'];
			$result = $this->Onlinememberrequestdetail_model->update($cond);	
			if($result['affected_rows'] <= 0){
				echo "{'success':false,'msg':'Request was NOT successfully deleted.'}";
			} else {
				$this->Onlinememberrequestheader_model->setValue('status_flag', '0');
				$result = $this->Onlinememberrequestheader_model->update($cond);	
				echo "{'success':true,'msg':'Request successfully deleted.'}";
			}
		} else
			echo "{'success':false,'msg':'Request was NOT successfully deleted.'}";
			
		log_message('debug', "[END] Controller online_member:delete");
	}
	/***
		temporary function for retrieving of original values
	***/
	function showOrigValues(){
		//$_REQUEST['member']['employee_id'] = '00421526';
		/*if (array_key_exists('member',$_REQUEST)){ 
			$this->member_model->setValue('employee_id', $_REQUEST['member']['employee_id']);
			$this->member_model->setValue('status_flag', '1');
		}*/
		$this->member_model->setValue('employee_id', $_REQUEST['employee_id']);
		$this->member_model->setValue('status_flag', '1');
		$data = $this->member_model->get(null, array("
				employee_id 
				,last_name
				,first_name
				,middle_name
				,DATE_FORMAT(member_date,'%m%d%Y') AS member_date
				,bank_account_no
				,bank
				,TIN
				,DATE_FORMAT(hire_date,'%m%d%Y') AS hire_date
				,DATE_FORMAT(work_date,'%m%d%Y') AS work_date
				,department
				,position
				,company_code
				,email_address
				,office_no
				,mobile_no
				,home_phone
				,address_1
				,address_2
				,address_3
				,DATE_FORMAT(birth_date,'%m%d%Y') AS birth_date
				,civil_status
				,gender
				,spouse
				,guarantor
				,beneficiaries
				,member_status"
		));
		
		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
			));
	}
	
	/**
	 * @desc disapprove change request
	 */
	function disapprove()
	{
		//$_REQUEST = array('request_no' => '21');
		
		log_message('debug', "[START] Controller online_membership:disapprove");
		log_message('debug', "online_membership param exist?:".array_key_exists('online_membership',$_REQUEST));
		
			//$this->Onlinememberrequestheader_model->populate($_REQUEST['online_membership']);
			$this->Onlinememberrequestheader_model->setValue('status_flag', '10');
			$this->Onlinememberrequestheader_model->setValue('peca_remarks', $_REQUEST['online_membership']['peca_remarks']);
			$result = $this->Onlinememberrequestheader_model->update(array('request_no' => $_REQUEST['request_no']));	
			if($result['affected_rows'] <= 0){
				echo "{'success':false,'msg':'Request was NOT successfully disapproved.'}";
			} else
				echo "{'success':true,'msg':'Request successfully disapproved.'}";
			
		log_message('debug', "[END] Controller online_membership:disapprove");
	}
	
	/**
	 * @desc Approve change request
	 */
	function approve(){	
		 /* $_REQUEST = array('request_no' => '88'
								,'employee_id' => '00421532'
								,'status' => '3');
		$_REQUEST['created_by'] = 'PECA';  
		$_REQUEST['online_membership']['peca_remarks'] = 'test';*/
		
		log_message('debug', "[START] Controller online_loan_payment:approve");
		log_message('debug', "online_membership param exist?:".array_key_exists('online_membership',$_REQUEST));
		
		if (($_REQUEST['status']>2)&&($_REQUEST['status']<9)) {
			$status = $this->Workflow_model->checkNextApprover('MEMB', $_REQUEST['status']);
			if($status != 0)
			{
				$this->Onlinememberrequestheader_model->setValue('status_flag', $status);
				$this->Onlinememberrequestheader_model->setValue('peca_remarks', $_REQUEST['online_membership']['peca_remarks']);
				$result = $this->Onlinememberrequestheader_model->update(array('request_no' => $_REQUEST['request_no']));	
				if($result['affected_rows'] <= 0){
					echo "{'success':false,'msg':'Request was NOT successfully approved.'}";
				} else
				{
					if($status == 9)
					{
						$params =array();
						$_REQUEST['filter'] = array('employee_id' => $_REQUEST['employee_id'], 'request_no' => $_REQUEST['request_no']);
						$data = $this->Onlinememberrequestheader_model->retrieveListOfRequests(
								array_key_exists('filter',$_REQUEST) ? $_REQUEST['filter'] : null
								,null
								,null
								,array('request_no'
									,'status_flag'
									,'created_date'
									,'modified_date'
									)
							,'request_no ASC');
						if($data['count'] != 0) {
							$list = $this->Onlinememberrequestdetail_model->get_list(array('request_no' =>  $_REQUEST['request_no'])
																						,null
																						,null
																						,array('fieldname', 'value')
																						);
							$params = $list['list'];
						}

						$error_code = $this->addAfterApproval($params, $_REQUEST['request_no'], $_REQUEST['employee_id']);
						if($error_code == 0){
							echo "{'success':true,'msg':'Request successfully approved.'}";
						}else
							echo '{"success":false,"msg":"Request was NOT successfully approved.","error_code":"'.$error_code.'"}';
					}else
						echo "{'success':true,'msg':'Request successfully approved.'}";
				}
			}
		} else
			echo "{'success':false,'msg':'Request was NOT successfully approved.'}";
			
		log_message('debug', "[END] Controller online_loan_payment:approve");
	}
	
	function addAfterApproval($membership, $request_no, $emp_id)
	{
		//format the fields
		foreach($membership as $data => $key) {
			$arr = explode('[', $key['fieldname']);
			$fieldname[substr($arr[1], 0, strlen($arr[1])-1)] = $key['value'];
		}
		foreach($fieldname as $data => $key) {
			$this->Beneficiary_model->populate(null);
			$arr = explode('beneficiary_', $data);
			//if beneficiary
			if($arr[0] == NULL) {
				unset($value);
				if(substr($arr[1], 0, strlen($arr[1])-1) == 'NAME' || substr($arr[1], 0, strlen($arr[1])-1) == 'name')
					$value['beneficiary'] = $key;
				else if(substr($arr[1], 0, strlen($arr[1])-1) == 'ADDRESS' || substr($arr[1], 0, strlen($arr[1])-1) == 'address')
					$value['beneficiary_address'] = $key;
				else
					$value[substr($arr[1], 0, strlen($arr[1])-1)] = $key;
				$sequence_no = substr($arr[1], strlen($arr[1])-1 , strlen($arr[1]));
				foreach($value as $data1 => $key1) {
					$this->Beneficiary_model->setValue($data1, $key1);
					$checkDuplicate = $this->Beneficiary_model->checkDuplicateKeyEntry(
																array('member_id' => $emp_id, 'sequence_no' => $sequence_no));
					if($checkDuplicate['error_code'] == 1){
						$result = $this->Beneficiary_model->update(array('member_id' => $emp_id, 'sequence_no' => $sequence_no));
					} else {
						$this->Beneficiary_model->setValue('member_id', $emp_id);
						$this->Beneficiary_model->setValue('sequence_no', $sequence_no);
						$this->Beneficiary_model->setValue('created_by', $_REQUEST['created_by']);
						$result = $this->Beneficiary_model->insert();
					}
				}
			} //if not beneficiary
			else {
				if($data=="non_member"){
					if($key == "true"){
						$key = "Y";
					}
					else{
						$key = "N";
					}
				}
				else if($data=="email_address"){ //update email address in r_user
					$this->user_model->populate(array()); //clear user model
					$this->user_model->setValue('email_address', $key);
					$this->user_model->setValue('user_id', $emp_id);
					$this->user_model->update();
				}
				$this->Member_model->setValue($data, $key);
			}
		}
		$return = $this->Member_model->get(array('employee_id' => $emp_id), array('email_address', 'last_name', 'first_name', 'member_status', 'company_code','non_member'));
		$prevMemberStatus = $return['list'][0]['member_status'];
		$prevCompanyCode = $return['list'][0]['company_code'];
		$prevNonMember = $return['list'][0]['non_member'];
		
		if($this->Member_model->model_data == null){
			$this->Member_model->model_data = array();
		}
		
		$getInvalidDateResult = $this->getInvalidDate($this->Member_model->model_data, $prevMemberStatus, $prevCompanyCode, $prevNonMember);
		
		if($getInvalidDateResult['change_value']){
			$this->Member_model->setValue('invalid_date', $getInvalidDateResult['value']);
		}
				
		$result = $this->Member_model->update(array('employee_id' => $emp_id));
		
		if ($result['affected_rows'] <= 0)
			$result['error_code'] = 1;
		else
			$result['error_code'] = 0;
		
		
		return $result['error_code'];
	}
	
	function getInvalidDate($new_array, $old_member_status, $old_company_code, $old_non_member){
		if(($old_member_status == 'I' || $old_company_code == '910' || $old_company_code == '920') && ($old_non_member =="" || $old_non_member == "N")){
			$old_state_is_valid = false;
		}
		else{
			$old_state_is_valid = true;
		}
		
		if(array_key_exists('member_status', $new_array)){
			$new_member_status_condition = "\$new_array['member_status'] == 'I'";
			
			$new_member_status = $new_array['member_status'];
			//if member status is inactive set status flag for user to 0 in user table
			if($new_member_status=="I"){
				$this->user_model->populate(array()); //clear user model data first
				$this->user_model->setValue('status_flag', '0');
				$this->user_model->update(array('user_id'=>$_REQUEST['employee_id']));
			}
			//if member status is active set status flag for user to 1 in user table
			else if($new_member_status=="A"){
				$this->user_model->populate(array()); //clear user model data first
				$this->user_model->setValue('status_flag', '1');
				$this->user_model->update(array('user_id'=>$_REQUEST['employee_id']));
			}
		}
		else{
		//[start] google share#8  Modified by asi466 on 20120110
			// $new_member_status_condition = "false";
			$new_member_status_condition = "\$old_member_status == 'I'";
		//[end] google share#8  Modified by asi466 on 20120110
		}
		
		if(array_key_exists('company_code', $new_array)){
			$new_company_code_condition = "\$new_array['company_code'] == '910' || \$new_array['company_code'] == '920'";
		}
		else{
		
		//[start] google share#8  Modified by asi466 on 20120110
			$new_company_code_condition = "\$old_company_code == '920' || \$old_company_code == '910'";
			//$new_company_code_condition = "false";		
		//[end] google share#8  Modified by asi466 on 20120110
		}
		
		if(array_key_exists('non_member', $new_array)){
			$new_non_member_condition = "\$new_array['non_member'] == '' || \$new_array['non_member'] == 'N'";
		}
		else{
		//[start] google share#8  Modified by asi466 on 20120110
			$new_non_member_condition = "\$old_non_member == '' || \$old_non_member == 'N'";
			//$new_non_member_condition = "false";
		//[end] google share#8  Modified by asi466 on 20120110
		}
		
		eval("
		if(($new_member_status_condition || $new_company_code_condition) && ($new_non_member_condition)){
			\$new_state_is_valid = false;
		}
		else{
			\$new_state_is_valid = true;
		}
		");
		
		$value = "";
		$change_value = false;
		
		if($old_state_is_valid && $new_state_is_valid){
			 //do nothing
		}
		else if(!$old_state_is_valid && $new_state_is_valid){
			$change_value = true;
			$value = ""; //clear invalid date
		}
		else if(!$old_state_is_valid && !$new_state_is_valid){
			 //do nothing
		}
		else if($old_state_is_valid && !$new_state_is_valid){
			$change_value = true;
			$value = date('Ymd', strtotime($this->Parameter_model->getParam('CURRDATE')));
		}
		
		return array('change_value'=> $change_value, 'value'=> $value);
	}
}
?>
