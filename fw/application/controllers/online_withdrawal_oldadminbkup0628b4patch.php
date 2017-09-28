<?php
/*
 * Created on Apr 22, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Online_withdrawal extends Asi_Controller {
	const STATUS_APPROVED = 9;
	function Online_withdrawal(){
		parent::Asi_Controller();
		$this->load->model('Onlinecapitaltransactionheader_model');
		$this->load->model('Onlinecapitaltransactiondetail_model');
		$this->load->model('Capitalcontribution_model');
		$this->load->model('Parameter_model');
		$this->load->model('Loan_model');
		$this->load->model('Workflow_model');
		$this->load->model('Capitaltransactionheader_model');
		$this->load->model('Capitaltransactiondetail_model');
		$this->load->model('Transactioncharges_model');
		$this->load->model('Mloan_model');
		$this->load->model('Tloan_model');
		$this->load->model('OAttachment_model');
		$this->load->helper('url');
		$this->load->library('constants');
		$this->load->library('Asi_Model');
		$this->load->model('Transactioncode_model');
	}
	
	function index(){		
		
	}
	
	/**
	 * @desc Retrieve all Online Capital Contribution Transactions
	 */
	function readHeader()
	{
		/* $_REQUEST['filter'] = array('tc.status_flag >' => '0'
								);	 */
		$curr_date = $this->Parameter_model->retrieveValue('CURRDATE');
		if(array_key_exists('submission_date_from',$_REQUEST)&& $_REQUEST['submission_date_from']!= "")
			$submission_date_from = date("Ymd", strtotime($_REQUEST['submission_date_from']));
		else
			$submission_date_from = $_REQUEST['submission_date_from']== ""?"00000000":date("Ymd", strtotime('-1 day',strtotime($curr_date)));
		if(array_key_exists('submission_date_to',$_REQUEST)&& $_REQUEST['submission_date_to']!= "")
			$submission_date_to = date("Ymd", strtotime($_REQUEST['submission_date_to']));
		else
			$submission_date_to = $_REQUEST['submission_date_to']== ""?"99999999":date("Ymd", strtotime('+1 day',strtotime($curr_date)));
		
	    $data = $this->Onlinecapitaltransactionheader_model->retrieveAllOnlineCapTransactions(
			//array_key_exists('filter',$_REQUEST) ? $_REQUEST['filter'] : null
			array_key_exists('transaction_code',$_REQUEST) ? $_REQUEST['transaction_code'] : null
			,$submission_date_from
			,$submission_date_to
			,array_key_exists('last_name',$_REQUEST) ? $_REQUEST['last_name'] : null
			,array_key_exists('first_name',$_REQUEST) ? $_REQUEST['first_name'] : null
			,array_key_exists('employee_id',$_REQUEST) ? $_REQUEST['employee_id'] : null
			,array_key_exists('status',$_REQUEST) ? $_REQUEST['status'] : 0
			,array_key_exists('or_no',$_REQUEST) ? $_REQUEST['or_no'] : 0
			,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
			,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
			,array('tc.request_no AS request_no'
	    		,'tc.employee_id AS employee_id'
				,'tc.transaction_date AS transaction_date'
				,'rt.transaction_description AS transaction_description'
				,'tc.status_flag AS status'
				,'me.last_name'
				,'me.first_name'
				,'tc.transaction_amount AS transaction_amount'
				,'tc.or_no'
				,'ow.approver1 AS approver1'
				,'ow.approver2 AS approver2'
				,'ow.approver3 AS approver3'
				,'ow.approver4 AS approver4'
				,'ow.approver5 AS approver5'
				)
		,'tc.modified_date DESC');//transaction
		
		foreach($data['list'] as $row => $value){
			$data['list'][$row]['transaction_date'] = date("mdY", strtotime($value['transaction_date']));
		}
		
		echo json_encode(array(
			'success' => true,
            'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
        ));
	}	
	
	/**
	 * @desc Searches a transaction
	 */
	function search()
	{		
		/* $_REQUEST['filter'] = array('transaction_date' => '20050907'
									,'status_flag' => '0'
								);	 */
				
	    $data = $this->Onlinecapitaltransactionheader_model->searchTransaction(
			$_REQUEST['filter']['transaction_date']
			,$_REQUEST['filter']['status_flag']
			,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
			,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
			,array('th.request_no'
				,'th.transaction_no'
	    		,'th.employee_id'
				,'th.transaction_date'
				,'rt.transaction_description'
				,'th.status_flag'
				));
		
		echo json_encode(array(
			'success' => true,
            'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
        ));
	}
	
	/**
	 * @desc previewOnlineForm - Previews the Transaction
	 */
	function preview()
	{
		//$_REQUEST['transaction_no'] = array('transaction_no' => '000134');
		if (array_key_exists('transaction_no',$_REQUEST)){ 
			$data = $this->Onlinecapitaltransactionheader_model->previewOnlineForm($_REQUEST['transaction_no']
				,null
				,null
				,array('me.first_name AS first_name'
						,'me.last_name AS last_name'
						,'transaction_date AS transaction_date'
						,'tc.transaction_amount AS transaction_amount'
						,'tc.employee_id AS sap_no'
						,'me.company_code AS location'
				));
		}
		
		echo json_encode(array(
			'success' => true,
            'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
        ));
	}
	
	/**
	 * @desc Retrieves a single online transaction
	 */
	function showHeader()
	{
		//$_REQUEST['request_no'] = '27';
		
		$params = 'tc.request_no =\''.$_REQUEST['request_no'].'\'';						
		$data = $this->Onlinecapitaltransactionheader_model->retrieveOnlineCapTransactionHeader(
				//array_key_exists('filter',$_REQUEST) ? $_REQUEST['filter'] : null
				$params
				,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
				,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
				,array('tc.request_no AS request_no'
						,'tc.transaction_code AS transaction_code'
						,'tc.employee_id AS employee_id'
						,'me.last_name'
						,'me.first_name'
						,'tc.transaction_date AS transaction_date'
						,'rt.transaction_description AS transaction_description'
						,'tc.member_remarks'
						,'tc.transaction_amount AS transaction_amount'
						,'tc.peca_remarks'
						,'tc.status_flag AS status'
						,'ow.approver1 AS approver1'
						,'ow.approver2 AS approver2'
						,'ow.approver3 AS approver3'
						,'ow.approver4 AS approver4'
						,'ow.approver5 AS approver5'
						)
				,'tc.transaction_date DESC');
		foreach($data['list'] as $row => $value){
			$data['list'][$row]['transaction_date'] = date("mdY", strtotime($value['transaction_date']));
		}
		
		$acctg_period = date("Ymd", strtotime($this->Parameter_model->getParam('ACCPERIOD')));
		foreach($data['list'] as $row => $value) {
			if($this->showCompCode($value['employee_id'])=="920"){
				$data['list'][$row]['maxWdwlAmount'] = $this->allowedWdwlForRetiree($value['employee_id']);
			}
			else{
				$bal_info = $this->Mloan_model->showBalanceInfo($value['employee_id'],$acctg_period);	
				$data['list'][$row]['maxWdwlAmount'] = $bal_info['maxWdwlAmount'];
			}
		}
		
		
		echo json_encode(array(
			'success' => true,
            'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
        ));
	}
	
	/**
	 * @desc Retrieves a single online transaction detail
	 */
	function showDetail()
	{
		/* $_REQUEST['filter'] = array('transaction_no' => '000134'
								);	 */
								
		$data = $this->Onlinecapitaltransactiondetail_model->retrieveOnlineCapitalTransactionDetail(
				array_key_exists('filter',$_REQUEST) ? $_REQUEST['filter'] : null
				,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
				,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
				,array('transaction_no'
						,'transaction_code'
						,'amount'
						,'created_by'
						,'created_date'
						,'modified_by'
						,'modified_date'
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
	 * @desc Deletes a request
	 */
	function deleteHeader()
	{		
									
		log_message('debug', "[START] Controller online_withdrawal:updateRequest");
		log_message('debug', "online_withdrawal param exist?:".array_key_exists('online_withdrawal',$_REQUEST));
		
		if (array_key_exists('online_withdrawal',$_REQUEST)) {
				//$this->Onlinecapitaltransactionheader_model->populate($_REQUEST['online_withdrawal']);
				$this->Onlinecapitaltransactionheader_model->setValue('status_flag', '0');
				$result = $this->Onlinecapitaltransactionheader_model->update(array(
											'request_no' => $_REQUEST['request_no']
											,'status_flag' => '2'
											));	
				if($result['affected_rows'] <= 0){
					echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
				} else {
					$this->Onlinecapitaltransactionheader_model->setValue('status_flag', '0');
					$result2 = $this->Onlinecapitaltransactionheader_model->update(array(
											'request_no' => $_REQUEST['request_no']
											,'status_flag' => '2'
											));
					
					if($result2['error_code'] == 0)
						echo "{'success':true,'msg':'Data successfully deleted.'}";
					else
						echo '{"success":false,"msg":"'.$result2['error_message'].'"}';
				}
		} else
			echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
			
		log_message('debug', "[END] Controller online_withdrawal:delete");
	}
	
	/**
	 * @desc Delete all online transaction details
	 */
	function deleteDetail()
	{		
		/* $_REQUEST['data'] = array ('transaction_no' => '000135'
									,'status_flag' => '0'
									); */
									
		log_message('debug', "[START] Controller online_withdrawal:deleteDetails");
		log_message('debug', "online_withdrawal param exist?:".array_key_exists('data',$_REQUEST));
		
		if (array_key_exists('data',$_REQUEST)) {
			$this->Onlinecapitaltransactiondetail_model->populate($_REQUEST['data']);
			$this->Onlinecapitaltransactiondetail_model->setValue('status_flag', '0');
			$result = $this->Onlinecapitaltransactiondetail_model->update('status_flag = 1');	
			if($result['affected_rows'] <= 0){
				echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
			} else
				echo "{'success':true,'msg':'Data successfully saved.'}";
		} else
			echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
			
		log_message('debug', "[END] Controller online_withdrawal:deleteDetails");
	}
	
	/**
	 * @desc sendOnlineRequest - Submits the request to the administrator for approval
	 */
	function updateRequest()
	{		
		/* $_REQUEST['data'] = array ('transaction_no' => '000134'
										,'status_flag' => '3'
										,'modified_by' =>'WIETZELL'
										,'modified_date' => '20101201123000'
									); */
									
		log_message('debug', "[START] Controller online_withdrawal:updateRequest");
		log_message('debug', "online_withdrawal param exist?:".array_key_exists('data',$_REQUEST));
		
		if (array_key_exists('data',$_REQUEST)) {
			$this->Onlinecapitaltransactionheader_model->populate($_REQUEST['data']);
			$this->Onlinecapitaltransactionheader_model->setValue('status_flag', '3');
			$result = $this->Onlinecapitaltransactionheader_model->update();	
			if($result['affected_rows'] <= 0){
				echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
			} else
				echo "{'success':true,'msg':'Data successfully saved.'}";
		} else
			echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
			
		log_message('debug', "[END] Controller online_withdrawal:updateRequest");
	}
	
	/**
	 * @desc Updates an Online Transaction
	 */
	 /*function update()
	 {
		$_REQUEST['data'] = array ('transaction_no' => '000135'	
									,'transaction_date' => '20101212120000'	
									,'transaction_code' =>'DDEP'
									,'transaction_amount' => 4000
									,'status_flag' => '1'
									,'remarks' =>'this is a remark'
									,'modified_by' => 'WIE'
									,'modified_date' => '20100204053522'
									); 
		$data2 = array ('transaction_no' => $_REQUEST['data']['transaction_no']	
									,'transaction_code' => $_REQUEST['data']['transaction_code']		
									,'amount' => $_REQUEST['data']['transaction_amount']
									,'modified_by' => $_REQUEST['data']['modified_by']
									,'modified_date' => $_REQUEST['data']['modified_date']
								); 
		
		log_message('debug', "[START] Controller online_withdrawal:update");
		log_message('debug', "online_withdrawal param exist?:".array_key_exists('data',$_REQUEST));
		
		if (array_key_exists('data',$_REQUEST)) {
			if($this->updateHeader($_REQUEST['data']))
			{
				if($this->updateDetail($data2))
					echo "{'success':true,'msg':'Data successfully saved.'}";
			}
		}
		else
			echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
			
		log_message('debug', "[END] Controller online_withdrawal:update");
	 }*/
	
	/**
	 * @desc Updates an Online Transaction
	 */
	function updateHeader()
	{		
		/*$_REQUEST['data'] = array ('transaction_no' => '000149'	
									,'transaction_date' => '20100204013522'	
									,'transaction_code' =>'WDWL'
									,'transaction_amount' => 50000000
									,'status_flag' => '1'
									,'remarks' =>'this is a remark'
									,'modified_by' => 'WIE'
									,'modified_date' => '20100204053522'
									); */
		log_message('debug', "[START] Controller online_withdrawal:updateHeader");
		log_message('debug', "online_withdrawal param exist?:".array_key_exists('online_withdrawal',$_REQUEST));
		
		$transaction_code = $_REQUEST['online_withdrawal']['transaction_code'];
		$employee_id = $_REQUEST['employee_id'];
		$transaction_amount = $_REQUEST['online_withdrawal']['transaction_amount'];
					
		$acctgPeriod = date("Ymd", strtotime($this->parameter_model->getParam('ACCPERIOD')));		
		
		
		if (array_key_exists('online_withdrawal',$_REQUEST)) {
			if($this->Transactioncode_model->transactionCodeExists($_REQUEST['online_withdrawal']['transaction_code'], 'CC')) {
				$saveOrSendFlag = $_REQUEST['saveOrSendFlag'];
				$str_return = "";
				unset($_REQUEST['online_withdrawal']['employee_id']);
				$this->Onlinecapitaltransactionheader_model->populate($_REQUEST['online_withdrawal']);
				//$cond = 'status_flag = 1 AND transaction_no = '.$_REQUEST['online_withdrawal']['transaction_no'];
				//$result = $this->Onlinecapitaltransactionheader_model->update($cond);	
				
				if($transaction_code=="DDEP" &&
					$this->depositGreaterThanTenMillion($transaction_amount, $employee_id, $acctgPeriod)){
					$str_return = "{'success':false,'msg':'Capital contribution will be greater than 10 million after this transaction.','error_code':'8'}";
				}
				else {

					if($transaction_code=="WDWL") {
						//check if employee has valid co-makers
						if($this->hasValidGuarantors($employee_id)){
							$str_return = "{'success':false,'msg':'Employee has invalid co-makers','error_code':'8'}";
						}
						else if($this->checkSuspensionDate($employee_id)){
							$str_return = "{'success':false,'msg':'You are still on suspension','error_code':'9'}";
						}
						else if($this->compareRemainingBalance($employee_id, $transaction_amount, $acctgPeriod)){
							$str_return = "{'success':false,'msg':'Employee has exceeded the withdrawal amount','error_code':'10'}";
						}
						else {
							if($this->showCompCode($employee_id)=='920' &&
							$transaction_amount > $this->allowedWdwlForRetiree($employee_id)){
								$str_return = "{'success':false,'msg':'Employee has exceeded the withdrawal amount for a retiree','error_code':'11'}";
							}
						}
					}
				} 
				if ($str_return == ""){
				
					if($saveOrSendFlag==1){
						$this->Onlinecapitaltransactionheader_model->setValue('status_flag', '1');
						$result = $this->Onlinecapitaltransactionheader_model->update('request_no ='.$_REQUEST['request_no']);	
					}
					else if($saveOrSendFlag==2){
						$result = $this->Onlinecapitaltransactionheader_model->update('status_flag = 2 AND request_no ='.$_REQUEST['request_no']);	
					}
					if($result['affected_rows'] <= 0){
						echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';
						
					} else{
						//echo "{'success':true,'msg':'Data successfully saved.'}";
						if($saveOrSendFlag==1){
							echo "{'success':true,'msg':'Data was successfully sent.'}";
						}
						else if($saveOrSendFlag==2){
							echo "{'success':true,'msg':'Data was successfully saved.'}";
						}
					}
				} else {
					echo $str_return;
				}
			} else echo "{'success':false,'msg':'Transaction code does not exist.'}";
		}else{
			//echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
			if($saveOrSendFlag==1){
				echo "{'success':false,'msg':'Data was NOT successfully sent.'}";
			}
			else if($saveOrSendFlag==2){
				echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
			}
		}
		
		log_message('debug', "[END] Controller online_withdrawal:updateHeader");
	}

	function depositGreaterThanTenMillion($transaction_amount, $employee_id, $accounting_period, $transaction_no=null){
		//Get capital contribution balance
		$data = $this->capitalcontribution_model->get(
		array('accounting_period'=>$accounting_period,'employee_id'=>$employee_id)
		,array('ending_balance') 
		);
		
		if($data['count']>0){
			$ending_balance  = $data['list'][0]['ending_balance'];
		}
		else{
			$ending_balance = 0;
		}
		
		//Sum all deposit amounts of employee
		$param = array('employee_id'=> $employee_id, 'transaction_code'=>'DDEP', 'status_flag' => '1');
		//transaction no is set (update is performed) exclude the specified transaction no
		if($transaction_no){
			$param['transaction_no !='] = $transaction_no;
		}
		
		$data = $this->Capitaltransactionheader_model->get(
			$param
			,array('SUM(transaction_amount) AS total_deposit')
		);
		
		if($data['count']>0){
			$total_deposit  = $data['list'][0]['total_deposit'];
		}
		else{
			$total_deposit = 0;
		}
		
		//Sum up ending balance, unposted deposit amounts and deposited amount
		if(($total_deposit+$ending_balance+$transaction_amount)>10000000){
			return 1;
		}
		else{
			return 0;
		}	
	}
	
	/**
	 * @desc Updates an Online Transaction
	 */
	function updateDetail()
	{		
		/*$_REQUEST['data'] = array ('transaction_no' => '000149'
									,'transaction_code' => 'WDWL'		
									,'amount' => 50000000
									,'modified_by' => 'WIE'
									,'modified_date' => '20100204053522'
								); */
		log_message('debug', "[START] Controller online_withdrawal:updateDetail");
		log_message('debug', "online_withdrawal param exist?:".array_key_exists('data',$_REQUEST));
		
		if (array_key_exists('data',$_REQUEST)) {
			$this->Onlinecapitaltransactiondetail_model->populate($_REQUEST['data']);
			$cond = 'status_flag = 1 AND transaction_no = '.$_REQUEST['data']['transaction_no'];
			$result = $this->Onlinecapitaltransactiondetail_model->update($cond);	
			if($result['affected_rows'] <= 0){
				echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';
				
			} else
				echo "{'success':true,'msg':'Data successfully saved.'}";
		}else echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
		log_message('debug', "[END] Controller online_withdrawal:updateDetail");
	}
	
	/**
	 * @desc Insert new data to o_capital_transaction_header and o_capital_transaction_detail
	 */
	/*function add()
	{
		 $_REQUEST['data'] = array('transaction_no' => '000147'
								,'transaction_date' => '20100204013522'
								,'transaction_code' => 'WDWL'
								,'employee_id' => '01517065'
								,'transaction_amount' => '50000000'
								,'status_flag' => '1'
								,'remarks' => 'a withrawal transaction'
								,'created_by' => 'PECA'
								,'created_date' => '20100204013522'
								,'modified_by' => 'PECA'
								,'modified_date' => '20100204013522'
							); 
		
		$data = array('transaction_no' => $_REQUEST['data']['transaction_no']
									,'transaction_code' => $_REQUEST['data']['transaction_code']
									,'amount' => $_REQUEST['data']['transaction_amount']
									,'created_by' => $_REQUEST['data']['created_by']
									,'created_date' => $_REQUEST['data']['created_date']
									,'modified_by' => $_REQUEST['data']['modified_by']
									,'modified_date' => $_REQUEST['data']['modified_date']
								);
		
		log_message('debug', "[START] Controller online_withdrawal:add");
		log_message('debug', "online_withdrawal param exist?:".array_key_exists('data',$_REQUEST));
		
		if (array_key_exists('data',$_REQUEST)) {
			if($this->addHeader($_REQUEST['data']))
			{
				if($this->addDetail($data))
					echo "{'success':true,'msg':'Data successfully saved.'}";
			}
		}
		else
			echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
			
		log_message('debug', "[END] Controller online_withdrawal:add");
	} */
	
	/**
	 * @desc saveOnlineTransactionHeader - Insert new data to o_capital_transaction_header
	 */
	function addHeader()
	{
		/*$_REQUEST['data'] = array('transaction_code' => 'DDEP'
									,'employee_id' => '01517066'
									,'transaction_amount' => '50000000'
									,'created_by' => 'WIE'
									//,'created_date' => '20100426110012'
								);*/
		log_message('debug', "[START] Controller online_withdrawal:addHeader");
		log_message('debug', "online_withdrawal param exist?:".array_key_exists('online_withdrawal',$_REQUEST));
		
		if (array_key_exists('online_withdrawal',$_REQUEST)) {
			if($this->Transactioncode_model->transactionCodeExists($_REQUEST['online_withdrawal']['transaction_code'], 'CC')) {
				$transaction_code = $_REQUEST['online_withdrawal']['transaction_code'];
				$employee_id = $_REQUEST['employee_id'];
				$transaction_amount = $_REQUEST['online_withdrawal']['transaction_amount'];
				$loan_date = date("Ymd", strtotime($_REQUEST['online_withdrawal']['transaction_date']));
				$_REQUEST['online_withdrawal']['transaction_date'] = $loan_date ;
				$acctgPeriod = date("Ymd", strtotime($this->getParam('ACCPERIOD')));
				$str_return = "";			
				$saveOrSendFlag = $_REQUEST['saveOrSendFlag'];
				
				if($transaction_code=="DDEP" &&
					$this->depositGreaterThanTenMillion($transaction_amount, $employee_id, $acctgPeriod)){
					$str_return = "{'success':false,'msg':'Capital contribution will be greater than 10 million after this transaction.','error_code':'8'}";
				}
				else {
					if($transaction_code == 'WDWL'){

						if($this->hasValidGuarantors($employee_id)){
							$str_return = "{'success':false,'msg':'Employee has invalid co-makers','error_code':'8'}";
						}
						else if($this->checkSuspensionDate($employee_id)){
							$str_return = "{'success':false,'msg':'You are still on suspension','error_code':'9'}";
						}
						else if($this->compareRemainingBalance($employee_id, $transaction_amount, $acctgPeriod)){
							$str_return = "{'success':false,'msg':'Employee has exceeded the withdrawal amount','error_code':'10'}";
						}
						else {
							if($this->showCompCode($employee_id)=='920' &&
							$transaction_amount > $this->allowedWdwlForRetiree($employee_id)){
								$str_return = "{'success':false,'msg':'Employee has exceeded the withdrawal amount for a retiree','error_code':'11'}";
							}
						}
					}
				}
				if ($str_return == ""){
					$this->db->trans_start();
					$request_no = $this->Parameter_model->retrieveValue('OREQ')+1;
					$this->Onlinecapitaltransactionheader_model->populate($_REQUEST['online_withdrawal']);
					$this->Onlinecapitaltransactionheader_model->setValue('employee_id', $employee_id);
					$this->Onlinecapitaltransactionheader_model->setValue('status_flag', $saveOrSendFlag);
					$this->Onlinecapitaltransactionheader_model->setValue('request_no', $request_no);
					$checkDuplicate = $this->Onlinecapitaltransactionheader_model->checkDuplicateKeyEntry();
		
					if($checkDuplicate['error_code'] == 1){
						$result['error_code'] = 1;
						$result['error_message'] = $checkDuplicate['error_message'];
					}
					else{
						$params= array(
										'transaction_code' => $transaction_code
										,'amount' => $transaction_amount
										,'request_no' => $request_no
										,'created_by' => $_REQUEST['online_withdrawal']['created_by']
									);
						$result = $this->Onlinecapitaltransactionheader_model->insert();
						$this->parameter_model->updateValue(('OREQ'), $request_no, $params['created_by']);
						$this->addDetail($params);
						$this->insertAttachments("W".$request_no);
					} 		
					$this->db->trans_complete();			
					if($result['error_code'] == 0 && $this->db->trans_status()){  
						//$str_return = "{'success':true,'msg':'Data successfully saved.'}";
						//if to save data
						if($saveOrSendFlag==2){
							$str_return = "{'success':true,'msg':'Data successfully saved.','request_no':'".$request_no."'}";
						}
						//if to send data
						else if($saveOrSendFlag==1){
							$str_return = "{'success':true,'msg':'Data successfully sent.','request_no':'".$request_no."'}";
						}
					} else {
						if($result['error_message']!=""){
							$str_return = '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';
						}
						else{
							$str_return = '{"success":false,"msg":"Cannot add details.","error_code":"8"}';
						}
					}
				}
			} else $str_return = '{"success":false,"msg":"Transaction code does not exist.","error_code":"1"}';
			echo $str_return;		
			
		}
		log_message('debug', "[END] Controller online_withdrawal:addHeader");
	}
	
	function insertAttachments($topic_id){
		$filearr = json_decode(stripslashes($_REQUEST['files']),true);
		$attachment_id = 1;
		$created_by = "";
		if(array_key_exists('created_by', $_REQUEST['online_withdrawal'])){
			$created_by = $_REQUEST['online_withdrawal']['created_by'];
		}
		else if(array_key_exists('modified_by', $_REQUEST['online_withdrawal'])){
			$created_by = $_REQUEST['online_withdrawal']['modified_by'];
		}
		
		foreach($filearr as $fileinfo){
			$params = array('attachment_id' => sprintf("%06d", $attachment_id)
							,'topic_id' => $topic_id
							,'path' => $fileinfo['path']
							,'type' => $fileinfo['type']
							,'size' => $fileinfo['size']
							,'created_by' => $created_by
							); 
			$this->OAttachment_model->populate($params);				
			$this->OAttachment_model->insert();
			$attachment_id++;
		}
	}
		
	/**
	 * @desc Checks if employee has acceptable guarantors (not a transferee, retired or inactive)
	 * @param employee_id
	 * @return 0-valid guarantors, 1-invalid guarantors
	 */
	function hasValidGuarantors($employee_id){
		$data = $this->member_model->checkGuarantors(
		array('ml.employee_id' => $employee_id, 'close_flag' => '0')
		,null
		,null
		,array('me.company_code AS company_code, me.member_status AS member_status', 'me.non_member AS non_member')
		);
			
		return $data;
	}	
	/**
	 * @desc saveOnlineTransactionDetail - Insert new data to o_capital_transaction_detail
	 */
	function addDetail($withdrawal)
	{
		/*$_REQUEST['data'] = array('transaction_no' => '000149'
									,'transaction_code' => 'WDWL' 
									,'amount' => '50000000'
									,'created_by' => 'WIE'
									,'created_date' => '20100426110023'
									,'modified_by' => ''
									,'modified_date' => '' 
								);*/
		log_message('debug', "[START] Controller online_withdrawal:addDetail");
		log_message('debug', "online_withdrawal param exist?:".$withdrawal);
		
			$this->Onlinecapitaltransactiondetail_model->populate($withdrawal);
			$this->Onlinecapitaltransactiondetail_model->setValue('status_flag', '1');
			
			$checkDuplicate = $this->Onlinecapitaltransactiondetail_model->checkDuplicateKeyEntry();
	
			if($checkDuplicate['error_code'] == 1){
				$result['error_code'] = 1;
				$result['error_message'] = $checkDuplicate['error_message'];
			}
			else{
				$result = $this->Onlinecapitaltransactiondetail_model->insert();
			} 		
			
		log_message('debug', "[END] Controller online_withdrawal:addDetail");
	}
	
	/**
	* START
	* supporting funcs for addHeader()
	*/
	
	/**
	 * @desc To get a specific parameter value
	 */
	function getParam($param_id){
		$data = $this->Parameter_model->get(array('parameter_id' => $param_id)
		,array('parameter_value')
		);
		if(!isset($data['list'][0]['parameter_value'])) 
			return "";
		else 
			return $data['list'][0]['parameter_value'];
	}
	
	/**
	 * @desc Checks if employee's suspension date is more than 6 months from current date
	 * @return 1-suspended more than 6 months ago, 0 - otherwise
	 */
	function checkSuspensionDate($employee_id = ''){
		if (isset($employee_id)){
			$this->Member_model->populate(array('employee_id'=>$employee_id));
			$currDate =  date("Ymd", strtotime($this->Parameter_model->getParam('CURRDATE')));
			$data = $this->Member_model->get(null ,array('suspended_date'));

			$suspensionDate = $data['list'][0]['suspended_date'];
			if($suspensionDate == "")
				return 0; //copied: originally return 1;

			$temp_date = date("Ym", strtotime(date("Ymd", strtotime($suspensionDate)) . " +7 month"));

			$suspensionLiftOffDate = $temp_date."01";

			if($suspensionLiftOffDate <= $currDate) return 0;
			else return 1;
		}
		else return 1;
	}
	
	/**
	 * @desc Checks if the remaining capcon balance after withdrawal is higher than the minimum capcon balance 
	 * @return 0 - Remaining balance is higher than minimum, 1 - otherwise
	 */
	function compareRemainingBalance($employee_id, $transAmt, $acctgPeriod){
		/*$ccMinBal= $this->getParam('CCMINBAL');

		$data = $this->Capitalcontribution_model->get(array(
			'employee_id'=> $employee_id
			,'accounting_period' => $acctgPeriod
			)
		,'ending_balance AS capcon_balance');
		
		$capconBal = $data['list'][0]['capcon_balance'];
		
		if(($capconBal - $transAmt) < $ccMinBal) return 1;
		else return 0;*/
		
		$bal_info = $this->Mloan_model->showBalanceInfo($employee_id,$acctgPeriod);
		settype($bal_info["maxWdwlAmount"],'float');
		settype($transAmt,'float');
		$allowedWdwl = $bal_info["maxWdwlAmount"];
		
		$allowedWdwl = round($allowedWdwl,2);
		$transAmt = round($transAmt,2);
		log_message('debug', 'transAmtzzzzz:' . $transAmt . 'allowedWdwzzzzz' . $allowedWdwl . 'bool' . ($allowedWdwl - $transAmt));
		if($allowedWdwl < $transAmt) return 1;
		else return 0;
	}
	
	/**
	 * @desc Retrieves the company code of an employee
	 */
	function showCompCode($employee_id){
		$data = $this->Member_model->get(array('employee_id'=>$employee_id), array(
			'company_code'
			));
		if(!isset($data['list'][0]['company_code']))
			return "";
		else
			return $data['list'][0]['company_code'];
	}
	
	function allowedWdwlForRetiree($employee_id = ''){
		$acctg_period = date("Ymd", strtotime($this->parameter_model->getParam('ACCPERIOD')));
		$capcon_balance = $this->Capitalcontribution_model->retrieveCapConBalance($employee_id, $acctg_period);
		$capcon_balance = round($capcon_balance, 2);
		$interestAndRemainingTerms = $this->getInterestAndRemainingTerms($employee_id);
		return ($capcon_balance - ($interestAndRemainingTerms));
	}

	function getInterestAndRemainingTerms($employee_id){
		$remaining_terms = "(principal_balance / employee_principal_amort)"; 
		$interest = "($remaining_terms * employee_interest_amortization)";
		
		/*$data1 = $this->Tloan_model->get(array('employee_id'=> $employee_id, 'status_flag'=> '1')
			,array("COALESCE(SUM(principal_balance + ($interest)),0) AS tsum"));
		*/	
		
		$data2 = $this->Mloan_model->get(array('employee_id'=>$employee_id, 'status_flag'=>'2', 'close_flag' => '0')
			,array("COALESCE(SUM(principal_balance + $interest),0) AS msum"));
	
		return (round($data2['list'][0]['msum'], 2));
	}
	
	/**
	 * @desc Returns the capital contribution balance of an employee
	 */
	function getCapConBalance($employee_id = ''){
		$accounting_period = date("Ymd", strtotime($this->getParam('ACCPERIOD')));
		$this->Capitalcontribution_model->populate(array(
			'employee_id' => $employee_id
			,'accounting_period' => $accounting_period
		));


		$data = $this->Capitalcontribution_model->get(null
		,array('ending_balance AS capcon_balance'
		));
		
		if(!isset($data['list'][0]['capcon_balance']))
			return 0;
		else
			return $data['list'][0]['capcon_balance'];
	}
	
	/**
	 * @desc To return the sum of the remaining loan balance of an employee
	 * @param employee_id
	 * @return int (loan balance)
	 */
	function getLoanBalance($employee_id){
		$this->Loan_model->populate(array(
			'employee_id' => $employee_id
			,'close_flag' => 0
		));

		$data = $this->Loan_model->get(null
		,array('COALESCE(SUM(principal_balance),0) AS loan_balance'
		));

		return $data['list'][0]['loan_balance'];
	}
	
	/**
	* END
	* supporting funcs for addHeader()
	*/
	
	/**
	 * @desc Retrieves from Transaction Codes.  Shows Withrawal and Direct Deposits (Savings) only.
	 */
	function readTransactionTypes()
	{
		$data = $this->Onlinecapitaltransactionheader_model->getTransactionTypes();
		
		echo json_encode(array(
			'success' => true,
            'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
        ));
	}
	
	/**
	 * @desc Approve change request
	 */
	function approve()
	{
		/*$_REQUEST['data'] = array('request_no' => '27'
									,'status_flag' => '8'
									,'user' => 'WIE');
		*/
		log_message('debug', "[START] Controller online_withdrawal:approve");
		log_message('debug', "online_withdrawal param exist?:".array_key_exists('online_withdrawal',$_REQUEST));
		
		if ((array_key_exists('online_withdrawal',$_REQUEST))&&($_REQUEST['online_withdrawal']['status_flag']>2)&&($_REQUEST['online_withdrawal']['status_flag']<9)) {
			if($this->Transactioncode_model->transactionCodeExists($_REQUEST['online_withdrawal']['transaction_code'], 'CC')) {
				$status = $this->Workflow_model->checkNextApprover($_REQUEST['online_withdrawal']['transaction_code'], $_REQUEST['online_withdrawal']['status_flag']);
				if($status != 0)
				{
					//$this->Onlinecapitaltransactionheader_model->populate($_REQUEST['online_withdrawal']);
					$this->Onlinecapitaltransactionheader_model->setValue('peca_remarks', $_REQUEST['online_withdrawal']['peca_remarks']);
					$this->Onlinecapitaltransactionheader_model->setValue('status_flag', $status);
					$result = $this->Onlinecapitaltransactionheader_model->update(array('request_no' => $_REQUEST['online_withdrawal']['request_no']));
					
					if($result['affected_rows'] <= 0){
						echo "{'success':false,'msg':'Request was NOT successfully approved.'}";
					} else {
						if($status == Online_withdrawal::STATUS_APPROVED)
						{
							$_REQUEST['request_no'] = $_REQUEST['online_withdrawal']['request_no'];
							$capcon = $this->Onlinecapitaltransactionheader_model->retrieveOnlineCapTransactionHeader(
										array('request_no' => $_REQUEST['online_withdrawal']['request_no'])
										,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
										,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
										,array('tc.transaction_code AS transaction_code'
												,'tc.employee_id AS employee_id'
												,'tc.transaction_date AS transaction_date'
												,'rt.transaction_description AS transaction_description'
												,'tc.transaction_amount AS transaction_amount'
												)
										,'tc.transaction_date DESC');
							$params = array(
										'transaction_date' =>$capcon['list'][0]['transaction_date']
										,'transaction_code' =>$capcon['list'][0]['transaction_code']
										,'employee_id' => $capcon['list'][0]['employee_id']
										,'transaction_amount' =>$capcon['list'][0]['transaction_amount']
										,'created_by' =>$_REQUEST['online_withdrawal']['created_by']
										,'bank_transfer' => 'N'
										);
							$error_code = $this->addAfterApproval($params, $_REQUEST['online_withdrawal']['request_no']);
							if($error_code == 1){
								echo '{"success":false,"msg":"Data was NOT successfully saved.","error_code":"'.$error_code.'"}';
							}else
								echo "{'success':true,'msg':'Request successfully approved.'}";
						}else
							echo "{'success':true,'msg':'Request successfully approved.'}";
					}
				} else
					echo "{'success':false,'msg':'Request was NOT successfully approved.'}";
			} else
					echo "{'success':false,'msg':'Transaction code does not exist.'}";
		} else
			echo "{'success':false,'msg':'Request was NOT successfully approved.'}";
			
		log_message('debug', "[END] Controller online_withdrawal:approve");
	}
	
	function addAfterApproval($capcon, $request_no)
	{
		$this->Capitaltransactionheader_model->populate($capcon);
		$trans_no = $this->Parameter_model->incParam('LASTTRANNO');
		$or_no = $this->Parameter_model->incParam('LASTORNO');
		$curr_date = date("Ymd", strtotime($this->Parameter_model->getParam('CURRDATE')));
		$this->Capitaltransactionheader_model->setValue('transaction_no', $trans_no);
		$this->Capitaltransactionheader_model->setValue('or_no', $or_no);
		$this->Capitaltransactionheader_model->setValue('or_date', $curr_date);
		$this->Capitaltransactionheader_model->setValue('status_flag', '1');
		
		$checkDuplicate = $this->Capitaltransactionheader_model->checkDuplicateKeyEntry();
	//	echo $checkDuplicate['error_message'];
		if($checkDuplicate['error_code'] == 1){
			$result['error_code'] = 1;
			$result['error_message'] = $checkDuplicate['error_message'];
		}
		else{
			$acctgPeriod = date("Ymd", strtotime($this->Parameter_model->getParam('ACCPERIOD')));
			
			if($this->checkCapconEntry($capcon['employee_id'], $acctgPeriod)){
				$this->addCapconEntry($capcon['employee_id'], $acctgPeriod, $capcon['created_by']);
			}
			$result = $this->Capitaltransactionheader_model->insert();
			$_REQUEST['capcon']['transaction_code'] = $capcon['transaction_code'];
			$this->addDtl($trans_no, $capcon['transaction_amount'], $capcon['created_by']);
				
			if ($result['affected_rows'] <= 0){
				$result['error_code'] = 1;
			}
			else{
				$this->Onlinecapitaltransactionheader_model->setValue('transaction_no', $trans_no);
				$this->Onlinecapitaltransactionheader_model->setValue('or_no', $or_no);
				$this->Onlinecapitaltransactionheader_model->setValue('or_date', $curr_date);
				$result = $this->Onlinecapitaltransactionheader_model->update(array('request_no' => $request_no));
				$this->Onlinecapitaltransactiondetail_model->setValue('transaction_no', $trans_no);
				$result = $this->Onlinecapitaltransactiondetail_model->update(array('request_no' => $request_no));
				$result['error_code'] = 0;
			}
		}
		
		return $result['error_code'];
	}
	
	/**
	 * @desc Checks if employee has an entry in the capcon table for the specified acctg period
	 * @return 0 - has capcon entry for a specified acctg period, 1 - otherwise
	 */
	function checkCapconEntry($employee_id, $acctgPeriod){
		$data = $this->Capitalcontribution_model->get(array(
			'employee_id'=>$employee_id
			,'accounting_period' => $acctgPeriod
		),array(
			'COUNT(*) AS count'
			));

			if($data['list'][0]['count'] > 0) return 0;
			else return 1;
	}

	/**
	 * @desc Inserts  employee in the capcon table for the specified acctg period
	 * @param $employee_id
	 * @param $acctgPeriod
	 */
	function addCapconEntry($employee_id, $acctgPeriod, $created_by){
		$this->Capitalcontribution_model->populate(array(
			'employee_id'=>$employee_id
		,'accounting_period' => $acctgPeriod
		,'beginning_balance' => 0
		,'ending_balance' => 0
		,'minimum_balance' => 0
		,'maximum_balance' => 0
		,'status_flag' => '1'
		,'created_by' => $created_by
		));
			
		$this->Capitalcontribution_model->insert();
	}
	
	function addDtl($trans_no, $amt, $user){
		log_message('debug', "[START] Controller capital_transaction:addDtl");
		$data = $this->readCharge();
		
		foreach($data['list'] as $key => $params){
			$formula = str_replace('cta', $amt, $params['formula']);
			eval("\$params['amount'] = $formula;");
			unset($params['formula']);
			$params['transaction_no'] = $trans_no;
			$params['created_by'] = $user;
			$this->Capitaltransactiondetail_model->populate($params);
			$this->Capitaltransactiondetail_model->setValue('status_flag', '1');
			$this->Capitaltransactiondetail_model->insert();
		}
		
		log_message('debug', "[END] Controller capital_transaction:addDtl");
	}
	
	/**
	 * @desc Retrieves transaction charge of the specified transaction type
	 */
	function readCharge(){			
		if (array_key_exists('capcon',$_REQUEST)){
			$this->Transactioncharges_model->populate($_REQUEST['capcon']);
		}
			
		$data = $this->Transactioncharges_model->getTransChargeList(
		array('rtc.transaction_code'=>$_REQUEST['capcon']['transaction_code'], 'rtc.status_flag' => '1')
		,null
		,null
		,array('rtc.charge_code AS transaction_code'
		,'rtc.charge_formula AS formula'
		));
		
		return $data;
	}
	
	/**
	 * @desc disapprove change request
	 */
	function disapprove()
	{
		/*$_REQUEST['data'] = array('request_no' => '39');*/
		
		log_message('debug', "[START] Controller online_withdrawal:disapprove");
		log_message('debug', "online_withdrawal param exist?:".array_key_exists('online_withdrawal',$_REQUEST));
		
		if ((array_key_exists('online_withdrawal',$_REQUEST))&&($_REQUEST['online_withdrawal']['status_flag']>2)&&($_REQUEST['online_withdrawal']['status_flag']<9)) {
			if($this->Transactioncode_model->transactionCodeExists($_REQUEST['online_withdrawal']['transaction_code'], 'CC')) {
				$this->Onlinecapitaltransactionheader_model->populate($_REQUEST['online_withdrawal']);
				$this->Onlinecapitaltransactionheader_model->setValue('status_flag', '10');
				$result = $this->Onlinecapitaltransactionheader_model->update();	
				if($result['affected_rows'] <= 0){
					echo "{'success':false,'msg':'Request was NOT successfully disapproved.'}";
				} else
					echo "{'success':true,'msg':'Request successfully disapproved.'}";
			} else
				echo "{'success':false,'msg':'Transaction code does not exist.'}";
		} else
			echo "{'success':false,'msg':'Request was NOT successfully disapproved.'}";
			
		log_message('debug', "[END] Controller online_withdrawal:disapprove");
	}
	
	function readWAmount()
	{
		$acctg_period = date("Ymd", strtotime($this->Parameter_model->getParam('ACCPERIOD')));
		
		$bal_info = $this->Mloan_model->showBalanceInfo($_REQUEST['employee_id'],$acctg_period);	
		$maxWdwlAmount = $bal_info['maxWdwlAmount'];
	
		$data = $this->Member_model->get(array('employee_id' => $_REQUEST['employee_id'])
											,array('last_name'
												,'first_name'
												,'employee_id')
											);
		$data['list'][0]['maxWdwlAmount'] = $maxWdwlAmount;
		echo json_encode(array(
			'success' => true,
            'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
        ));
	}
	function getTopicAttachments()
	{
		$_REQUEST['bulletin']['topic_id'] = $_REQUEST['topic_id'];
		
		$data = $this->OAttachment_model->getTopicAttachments();
		
		echo json_encode(array(
			'success' => true,
            'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query'],
        ));
	}
}
?>
