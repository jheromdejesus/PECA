<?php

/* Location: ./CodeIgniter/application/controllers/online_payroll_deduction.php */



class Online_Payroll_deduction extends Asi_Controller {

	function Online_Payroll_deduction()
	{
		parent::Asi_Controller();
		$this->load->model('onlinepayrolldeduction_model');
		$this->load->model('transactioncode_model');
		$this->load->model('Parameter_model');
		$this->load->model('workflow_model');
		$this->load->model('payrolldeduction_model');
		$this->load->helper('url');
		$this->load->helper('date');
		$this->load->library('constants');
	}
	
	function index() {
		
	}
	
	/**
	 * @desc To retrieve all payroll deduction transactions
	 * @return array
	 */
	function read()
	{
		$curr_date = $this->Parameter_model->retrieveValue('CURRDATE');
		if(array_key_exists('transaction_date_from',$_REQUEST)&& $_REQUEST['transaction_date_from']!= "")
			$transaction_date_from = date("Ymd", strtotime($_REQUEST['transaction_date_from']));
		else
			$transaction_date_from = $_REQUEST['transaction_date_from']== ""?"00000000":date("Ymd", strtotime('-1 day', strtotime($curr_date)));
		if(array_key_exists('transaction_date_to',$_REQUEST)&& $_REQUEST['transaction_date_to']!= "")
			$transaction_date_to = date("Ymd", strtotime('+1 day',strtotime($_REQUEST['transaction_date_to'])));
		else
			$transaction_date_to = $_REQUEST['transaction_date_to']== ""?"99999999":date("Ymd", strtotime('+1 day',strtotime($curr_date)));
		
		$params['ow.request_type'] =  'PAYR';
		
		$data = $this->onlinepayrolldeduction_model->getPayrollDeductionList(
			$params
			,$transaction_date_from
			,$transaction_date_to
			,array_key_exists('last_name',$_REQUEST) ? $_REQUEST['last_name'] : null
			,array_key_exists('first_name',$_REQUEST) ? $_REQUEST['first_name'] : null
			,array_key_exists('employee_id',$_REQUEST) ? $_REQUEST['employee_id'] : null
			,array_key_exists('status',$_REQUEST) ? $_REQUEST['status'] : 0
			,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
			,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
			,array('op.request_no'
				,'DATE_FORMAT(op.created_date,"%m%d%Y") AS transaction_period'
				,'me.last_name'
				,'me.first_name'
				,'op.amount'
				,'op.status_flag'
				,'ow.approver1 AS approver1'
				,'ow.approver2 AS approver2'
				,'ow.approver3 AS approver3'
				,'ow.approver4 AS approver4'
				,'ow.approver5 AS approver5'
				,'op.employee_id'
				,'op.start_date as start_date'
				,'op.transaction_code'
				)
			,'op.modified_date'//last_name, first_name ASC
		);
	
		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
		));
		
	}	
	
	/**
	 * @desc Retrieves a single payroll deduction entry of an employee 
	 * @return array
	 */
	function show()
	{
		//$_REQUEST['newpayroll'] = array('employee_id' => '01517066', 'transaction_code' => 'PDED', 'start_date' => '20040401');
		/* $_REQUEST['newpayroll']['employee_id']=	'01517068';
		$_REQUEST['newpayroll']['start_date']=	'20100426075951';
		$_REQUEST['newpayroll']['transaction_code']=	'PDED'; */
		$start_date = date("Ymd", strtotime($_REQUEST['newpayroll']['start_date']));
		$_REQUEST['filter'] = array(
			'pd.employee_id' => $_REQUEST['newpayroll']['employee_id']
			,'pd.transaction_code' => $_REQUEST['newpayroll']['transaction_code']
			 ,'pd.request_no' => $_REQUEST['newpayroll']['request_no']
			,'pd.start_date LIKE' => SUBSTR($start_date, 0,5).'%'
			,'pd.status_flag >=' => '1'
			,'ow.request_type' =>  'PAYR'
		);				  
		$data = $this->onlinepayrolldeduction_model->getPayrollDeduction(
			array_key_exists('filter',$_REQUEST) ? $_REQUEST['filter'] : null,
			array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
			array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
			array('pd.employee_id'
					,'me.last_name'
					,'me.first_name'				
					,'pd.start_date'				
					,'pd.end_date'				
					,'pd.amount'
					,'pd.transaction_code'
					,'pd.member_remarks'
					,'pd.peca_remarks'
					,'pd.request_no'
					,'pd.status_flag'
					,'ow.approver1 AS approver1'
					,'ow.approver2 AS approver2'
					,'ow.approver3 AS approver3'
					,'ow.approver4 AS approver4'
					,'ow.approver5 AS approver5'
					)
			);
		
		foreach($data['list'] as $row => $value){
			$data['list'][$row]['start_date'] = $this->formatDateYYYYMMDDToMMDDYYYY($value['start_date']);
			$data['list'][$row]['end_date'] = $this->formatDateYYYYMMDDToMMDDYYYY($value['end_date']);
		}
	
		echo json_encode(array(
				'success' => true,
				'data' => $data['list'],
				'total' => $data['count'],
				'query' => $data['query']
				));
			
	}	
	
	/**  
	 * @desc get list of approvers with request type PAYR
	 */
	function readApprovers()
	{
		$data = $this->workflow_model->getRequestList(
				array('request_type' => 'PAYR')
				,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
				,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
				,array('request_name'
					,'user1.user_name as approver1_name'
					,'user2.user_name as approver2_name'
					,'user3.user_name as approver3_name'
					,'user4.user_name as approver4_name'
					,'user5.user_name as approver5_name'
					)
			,'');

		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
		));
		
	}
	
	/**
	 * @desc To retrieve all active employees with or without payroll deductions transactions 
	 * and displays payroll deductions data 
	 * @return array
	 */
	function readPDEmployeeList()
	{			  
		if(array_key_exists('newpayroll', $_REQUEST)){
			if(array_key_exists('employee_id', $_REQUEST['newpayroll']) && $_REQUEST['newpayroll']['employee_id']!= "")
				$params['pd.employee_id'] =  $_REQUEST['newpayroll']['employee_id'];
			if(array_key_exists('first_name', $_REQUEST['newpayroll']) && $_REQUEST['newpayroll']['first_name']!= "")
				$params['me.first_name LIKE'] =  $_REQUEST['newpayroll']['first_name']."%";
			if(array_key_exists('last_name', $_REQUEST['newpayroll']) && $_REQUEST['newpayroll']['last_name']!= "")
				$params['me.last_name LIKE'] =  $_REQUEST['newpayroll']['last_name']."%";
		}
		
		$params = array('me.member_status' => 'A');		
		
		$data = $this->onlinepayrolldeduction_model->getPDEmployeeList(
			$params,
			array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
			array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
			array('pd.employee_id'
					,'me.last_name'
					,'me.first_name'				
					,'pd.start_date'				
					,'pd.end_date'				
					,'pd.amount')
		);
		
		echo json_encode(array(
				'success' => true,
				'data' => $data['list'],
				'total' => $data['count'],
				'query' => $data['query']
				));	
	}

	function readPDTransactionType()
	{
		$_REQUEST['filter'] = array(
							  		'transaction_group' => 'PD'
							  		,'status_flag' => '1'
							  );
		$data = $this->transactioncode_model->get_list(
			array_key_exists('filter',$_REQUEST) ? $_REQUEST['filter'] : null,
			array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
			array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
			array('transaction_code'
					,'transaction_description')
			,'transaction_description ASC'
		);
		
		echo json_encode(array(
				'success' => true,
				'data' => $data['list'],
				'total' => $data['count'],
				'query' => $data['query']
				));	
	}
	
	/**
	 * @desc Adds new payroll deduction record
	 * @return 
	 */
	function add()
	{
		//$_REQUEST['user'] = 'PECA';
		/*$_REQUEST['newpayroll'] = array('employee_id' => '01517068'
										,'last_name' => 'BELTRAN'
										,'first_name' => 'ANGELO SALVADOR'
										,'start_date' => '20050401000000'
										,'end_date' => '20060630000000'
										,'transaction_code' => 'PDED'
										,'amount' => 5000
										,'transaction_type' => 'D'
										,'transaction_period' => '20040701000000'
										,'modified_by' => 'WIE'
									);*/
		log_message('debug', "[START] Controller online_payroll_deduction:add");
		log_message('debug', "newpayroll param exist?:".array_key_exists('newpayroll',$_REQUEST));
		  
		if (array_key_exists('newpayroll',$_REQUEST)) {	
		
			$start_date = $this->formatDateMMsDDsYYYYToYYYYMMDD($_REQUEST['newpayroll']['start_date']);
			$end_date = $this->formatDateMMsDDsYYYYToYYYYMMDD($_REQUEST['newpayroll']['end_date']);
			
			$acctgPeriod = $this->parameter_model->retrieveValue('ACCPERIOD');
			//$pdEntry = $this->getEmployeePDEntries($_REQUEST['user']);
			
					
			if ($this->checkStartDate($start_date)) {
				echo("{'success':false,'msg':'Start date should be the first day of the month','error_code':'26'}");	
			}
			else if ($this->checkEndDate($end_date)) {
				echo("{'success':false,'msg':'End date should be the last day of the month','error_code':'27'}");	
			}
			else if ($start_date < $acctgPeriod) {
				echo("{'success':false,'msg':'Start date should not be prior to accounting period','error_code':'28'}");	
			}
			else if ($end_date < $acctgPeriod) {
				echo("{'success':false,'msg':'End date should not be prior to accounting period','error_code':'29'}");	
			}
			
			else {
				//$command = $this->checkOverlapRecords($pdEntry['list'], $_REQUEST['newpayroll'], 'add');	
				//if ($command==0) {
					unset($_REQUEST['newpayroll']['first_name']);			
					unset($_REQUEST['newpayroll']['last_name']);	
					
					$this->onlinepayrolldeduction_model->populate($_REQUEST['newpayroll']);
					$this->onlinepayrolldeduction_model->setValue('employee_id', $_REQUEST['user']);
					$this->onlinepayrolldeduction_model->setValue('status_flag', $_REQUEST['newpayroll']['status_flag']);
					$this->onlinepayrolldeduction_model->setValue('start_date', $start_date);
					$this->onlinepayrolldeduction_model->setValue('end_date', $end_date);
					$this->onlinepayrolldeduction_model->setValue('transaction_type', 'A');
					$this->onlinepayrolldeduction_model->setValue('transaction_period', $acctgPeriod);
					$this->onlinepayrolldeduction_model->setValue('created_by', $_REQUEST['user']);
					
					$request_no = $this->parameter_model->retrieveValue('OREQ')+1;
					$this->onlinepayrolldeduction_model->setValue('request_no', $request_no);
					
					$checkDuplicate = $this->onlinepayrolldeduction_model->checkDuplicateKeyEntry(
																		array('employee_id' => $_REQUEST['user']
																				,'transaction_code' => $_REQUEST['newpayroll']['transaction_code']
																				,'start_date' => $start_date
																				,'status_flag <>' => '0'
																				,'status_flag !=' => '10'
																				)
																		);	
					if($checkDuplicate['error_code'] == 1){
						$result['error_code'] = 1;
						$result['error_message'] = 'Start date already exists in your record.'; //$checkDuplicate['error_message'];
					}
					else $result = $this->onlinepayrolldeduction_model->insert();
					
					if($_REQUEST['newpayroll']['status_flag']==1){
						$savedOrSent = "sent";
					}
					else{
						$savedOrSent = "saved";
					}
					
					if($result['error_code'] == 0) {
						$this->parameter_model->updateValue(('OREQ'), $request_no, $_REQUEST['newpayroll']['modified_by']);
						echo "{'success':true,'msg':'Data successfully ".$savedOrSent.".','request_no':$request_no}";
					} 
					else echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';
				//}
				//else if($command==1){
					//echo("{'success':false,'msg':'There are overlapped records, would you like to adjust?','error_code':'19'}");
				//}	
			}
		}
		else {
			echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
		}
		
		log_message('debug', "[END] Controller online_payroll_deduction:add");		  			
	}
	
	/**
	 * @desc Updates payroll deduction record
	 * @return 
	 */
	function update()
	{
		/*$_REQUEST['newpayroll'] = array('employee_id' => '01517068'
										,'last_name' => 'BELTRAN'
										,'first_name' => 'ANGELO SALVADOR'
										,'start_date' => '20050401000000'
										,'end_date' => '20060630000000'
										,'transaction_code' => 'PDED'
										,'amount' => 50000
										,'transaction_type' => 'D'
										,'transaction_period' => '20040701000000'
										,'modified_by' => 'WIE'
									);*/
		//$_REQUEST['user'] = 'PECA';
		log_message('debug', "[START] Controller loan:addCharges");
		log_message('debug', "newpayroll param exist?:".array_key_exists('newpayroll',$_REQUEST));
		  
		if (array_key_exists('newpayroll',$_REQUEST)) {	
			
			$start_date = $this->formatDateMMsDDsYYYYToYYYYMMDD($_REQUEST['newpayroll']['start_date']);
			$end_date = $this->formatDateMMsDDsYYYYToYYYYMMDD($_REQUEST['newpayroll']['end_date']);
			
			$acctgPeriod = $this->parameter_model->retrieveValue('ACCPERIOD');
			//$pdEntry = $this->getEmployeePDEntries($_REQUEST['user']);
			
			if ($this->checkStartDate($start_date)) {
				echo("{'success':false,'msg':'Start date should be the first day of the month','error_code':'26'}");	
			}
			else if ($this->checkEndDate($end_date)) {
				echo("{'success':false,'msg':'End date should be the last day of the month','error_code':'27'}");	
			}
			else if ($start_date < $acctgPeriod) {
				echo("{'success':false,'msg':'Start date should not be prior to accounting period','error_code':'28'}");	
			}
			else if ($end_date < $acctgPeriod) {
				echo("{'success':false,'msg':'End date should not be prior to accounting period','error_code':'29'}");	
			}
			else {
				//$command = $this->checkOverlapRecords($pdEntry['list'], $_REQUEST['newpayroll'], 'update');	
				//if ($command==0) {
					unset($_REQUEST['newpayroll']['first_name']);			
					unset($_REQUEST['newpayroll']['last_name']);	
					
					$this->onlinepayrolldeduction_model->populate($_REQUEST['newpayroll']);
					//$this->onlinepayrolldeduction_model->setValue('status_flag', '1');
					$this->onlinepayrolldeduction_model->setValue('employee_id', $_REQUEST['user']);
					$this->onlinepayrolldeduction_model->setValue('start_date', $start_date);
					$this->onlinepayrolldeduction_model->setValue('end_date', $end_date);
					
					$this->onlinepayrolldeduction_model->setValue('modified_by', $_REQUEST['user']);
					
					$result = $this->onlinepayrolldeduction_model->update(array('employee_id' => $_REQUEST['user']
														   ,'transaction_code' => $_REQUEST['newpayroll']['transaction_code']
														   ,'request_no' => $_REQUEST['newpayroll']['request_no']
														   ,'start_date' => $start_date)
												 	 );
													 
					if($_REQUEST['newpayroll']['status_flag']==1){
						$savedOrSent = "sent";
					}
					else{
						$savedOrSent = "saved";
					}
					
					if($result['affected_rows'] <= 0) echo '{"success":false,"msg":"'.$result['error_message'].'"}';
					else echo "{'success':true,'msg':'Data successfully ".$savedOrSent.".'}";
				//}
				//else if($command==1){
					//$this->adjustPayrollDeduction($pdEntry['list'], $_REQUEST['newpayroll']);
				//}	
			}
		}
		else {
			echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
		}	
		
		log_message('debug', "[END] Controller online_payroll_deduction:update");	  			
	}
	
	/**
	 * @desc Deletes payroll deduction record
	 * @return 
	 */
	function delete()
	{			

		log_message('debug', "[START] Controller payrolldeduction:delete");
		log_message('debug', "newpayroll param exist?:".array_key_exists('newpayroll',$_REQUEST));
		
		if (array_key_exists('newpayroll',$_REQUEST)) {	
			$this->onlinepayrolldeduction_model->setValue('status_flag', '0');
			$this->onlinepayrolldeduction_model->setValue('modified_by', $_REQUEST['newpayroll']['modified_by']);
			
			$result = $this->onlinepayrolldeduction_model->update(array(
				'request_no' => $_REQUEST['newpayroll']['request_no']
				,'status_flag' => '2'
			));
			
			if($result['affected_rows'] <= 0){
				echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
			} else {
				echo "{'success':true,'msg':'Data successfully deleted.'}";
			}
		} else
			echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
			
		log_message('debug', "[END] Controller payrolldeduction:delete");
	}
	
	/**
	 * @desc Updates information on payroll deduction record
	 */
	function updatePD($payroll='',$type='A', $old_start_date = null)
	{
		$this->payrolldeduction_model->populate($payroll);
		$this->payrolldeduction_model->setValue('status_flag', '1');
		$this->payrolldeduction_model->setValue('transaction_type', $type);
		
		if($old_start_date){
			$start_date = $old_start_date;
		}
		else{
			$start_date = $payroll['start_date'];
		}
		
		$result = $this->payrolldeduction_model->update(array('employee_id' => $payroll['employee_id']
														   ,'transaction_code' => $payroll['transaction_code']
														   ,'start_date' => $start_date)
												 	 );
		return $result;							 	
	}
	
	/**
	 * @desc Inserts new record to o_payroll_deduction
	 */
	function addPD($payroll='', $type='A')
	{
		$acctgPeriod = $this->parameter_model->retrieveValue('ACCPERIOD');
		$this->payrolldeduction_model->populate($payroll);
		$this->payrolldeduction_model->setValue('status_flag', '1');
		$this->payrolldeduction_model->setValue('transaction_type', $type);
		$this->payrolldeduction_model->setValue('transaction_period', date("Ymd",strtotime($acctgPeriod)));
			
		$checkDuplicate = $this->payrolldeduction_model->checkDuplicateKeyEntry(
																array('employee_id' => $payroll['employee_id']
																		,'transaction_code' => $payroll['transaction_code']
																		,'start_date' => $payroll['start_date'])
																);		
		log_message('debug', 'PD emp idzzzzz: '.$payroll['employee_id']);
		log_message('debug', 'PD trancodezzzzz: '.$payroll['transaction_code']);
		log_message('debug', 'PD startdatezzzzz: '.$payroll['start_date']);
		log_message('debug', 'PD errorcodezzz: '.$checkDuplicate['error_code']);
		
		if($checkDuplicate['error_code'] == 1){
			log_message('debug', 'duplicatezzzzzzz');
			$result['error_code'] = 1;
			$result['error_message'] = $checkDuplicate['error_message'];
		}
		else{
			$result = $this->payrolldeduction_model->insert();
		} 			
		return $result;
	}
	
	/**
	 * @desc Deletes payroll deduction record
	 * @return 
	 */
	function deletePD($payroll = '')
	{	
		$this->payrolldeduction_model->populate($payroll);
		$this->payrolldeduction_model->setValue('status_flag', '0');
		$result = $this->payrolldeduction_model->update(array(
				'employee_id' => $payroll['employee_id']
				,'transaction_code' => $payroll['transaction_code']
				,'start_date' => $payroll['start_date']
				,'status_flag' => '1'
		));
			
		return $result;
	}
	
	/**
	 * @desc Checks overlap records
	 */
	function checkOverlapRecords($old, $new, $function)
	{
		$_REQUEST['command'] = "OK";
		$count = 0;
		$command = 0;	//0 - to add directly; 1 - OK to adjust; 2 - not OK to adjust 
		foreach ($old AS $data) 
		{
			//log_message('debug', 'old_startzzzzz: '.$data['start_date']);
			//log_message('debug', 'old_endzzzzz: '.$data['end_date']);
			//log_message('debug', 'new_startzzzzz: '.$new['start_date']);
			//log_message('debug', 'new_endzzzzz: '.$new['end_date']);
			if (($data['start_date'] < $new['start_date'] && $new['start_date'] <= $data['end_date'])
				||($data['start_date'] <= $new['end_date'] && $data['end_date'] >= $new['end_date'])
				||($new['start_date'] < $data['start_date'] && $new['end_date'] > $data['start_date'])
				) {
				$count++;
			}
		}
		
		if ($count==0) {
			$command = 0;
		}
		/*else if ($count==1 && $function='update'){
			$command = 0;
		}*/
		else {
			/*echo "{'success':false,'msg':There are '.$count.' overlap records. Would you like to adjust payroll deduction?'}";
			if ($_REQUEST['command']=="OK") $command = 1;
			else $command = 2;*/	
			
			$command = 1;
		}		
		return $command;
	}
	
	function formatDateMMsDDsYYYYToYYYYMMDD($date){
		return substr($date, 6, 4) .  substr($date, 0, 2) .  substr($date, 3, 2);
	}
	
	function formatDateYYYYMMDDToMMDDYYYY($date){
		return substr($date, 4, 2) .  substr($date, 6, 2) .  substr($date, 0, 4);
	}	
	
	function adjustPayrollDeduction($mode)
	{
		log_message('debug', 'adjustpd 0zzzzzz');
		
		$param['pd.status_flag >='] = '1';
		$param['ow.request_type'] =  'PAYR';
		$param['pd.request_no'] = $_REQUEST['newpayroll']['request_no'];
		
		$data = $this->onlinepayrolldeduction_model->getPayrollDeduction(
													$param
													,null
													,null
													,null
													,null
													,null
													,null
													,null
													,null
													,array('pd.employee_id'				
														,'pd.start_date'				
														,'pd.end_date'				
														,'pd.amount'
														,'pd.transaction_code'
														,'pd.transaction_period'
														,'pd.transaction_type'
														)
													);
		if(!isset($data['list'][0])){
			die('{"success":false,"msg":"Request no. does not exist"}');
		}
		
		$pd = array(
						'employee_id' => $data['list'][0]['employee_id']
						,'transaction_code' => $data['list'][0]['transaction_code']
						,'start_date' => $data['list'][0]['start_date']
						,'end_date' => $data['list'][0]['end_date']
						,'amount' => $data['list'][0]['amount']
						,'transaction_type' => $data['list'][0]['transaction_type']
						,'transaction_period' => $data['list'][0]['transaction_period']
						,'created_by' => $_REQUEST['newpayroll']['created_by']
					);
					
		$this->db->trans_begin();
		
		$start = $pd['start_date'];
		$end = $pd['end_date'];
			
		$amount = $pd['amount'];
		$employee_id = $pd['employee_id'];
		$transaction_code = $pd['transaction_code'];
		$transaction_period = $pd['transaction_period'];
		
		$this->db->trans_start();
		//retrieve conflicting pd delete afterwards
		$result = $this->payrolldeduction_model->retrieveConflict($start, $end, $employee_id, $transaction_code);
		$arr_conflicts = $result['list'];
		$result = $this->payrolldeduction_model->deleteConflict($start, $end, $employee_id, $transaction_code);
		$arr_01 = array();
		
		foreach ($arr_conflicts as $row){
			//add dates that are not in between the given start and end date
			if ($row['start_date'] < $start || $row['start_date'] > $end){
				$arr_01[$row['start_date']] = $row['amount'];
			} 
			
			if ($row['end_date'] < $start || $row['end_date'] > $end){
				$arr_01[$row['end_date']] = $row['amount'];
			} 
		}
		
		//append the input dates here in the array
		$arr_01[$start] = $amount;
		$arr_01[$end] = $amount;
		
		//sort dates ASC
		ksort($arr_01);
		
		//transfer from array1(sorted) to array2(conflict resolved)
		$arr_02 = array();
		$prev_key = "";
		$bol_start = true;
		foreach ($arr_01 as $key=>$value){
			$day = substr($key, 6, 2);
			if ($bol_start){
				if ($day == "01"){
					$arr_02[$key] = $value;
					$bol_start = false;
				} else{
					/*$prev_date = $this->firstDayNextMonth($prev_key);//date('Ymd',strtotime('+1 day',strtotime($prev_key)));
					$arr_02[$prev_date] = null;
					$arr_02[$key] = $value;
					$bol_start = true;*/
				}
			} else{
				if ($day == "01"){
					$prev_date = $this->lastDayLastMonth($key);//date('Ymd',strtotime('-1 day',strtotime($key)));
					$arr_02[$prev_date] = null;
					$arr_02[$key] = $value;
					$bol_start = false;
					
				} else{
					$arr_02[$key] = $value;
					$bol_start = true;
				}
			}
			
			$prev_key = $key;
		}
		
		//transfer array02 to array03 having 0,1,2 as indices
		$arr_03 = array();
		foreach ($arr_02 as $key=>$value){
			$arr_03[] = array($key=>$value);
		}
		
		//using array03, insert the corresponding entries
		for ($i=0; $i<count($arr_03); $i = $i + 2){
			$arr_current = $arr_03[$i];
			$arr_next = $arr_03[$i + 1];
			$new_start_date = key($arr_current);
			$new_end_date = key($arr_next);
			$new_amount = isset($arr_current[$new_start_date]) ? $arr_current[$new_start_date] : $arr_next[$new_end_date];
			$delimited = 'D';
			//check for delimited
			if (isset($arr_current[$new_start_date]) && $arr_next[$new_end_date]){
				$delimited = 'A';
			}			
			
			$this->payrolldeduction_model->insertPD($new_start_date, $new_end_date, $new_amount, $delimited, $employee_id, $transaction_code, $transaction_period, $_REQUEST['newpayroll']['created_by']);
		}	
		//peca 6th enhancement
		//get the start date of approved pd.
		$resultStartDate = $this->onlinepayrolldeduction_model->get(array('request_no'=>$_REQUEST['newpayroll']['request_no']));
		$this->payrolldeduction_model->updateNewPDField($employee_id, $transaction_code,$resultStartDate['list'][0]['start_date']);
		
		if($this->db->trans_status()===TRUE){
			$this->onlinepayrolldeduction_model->setValue('status_flag', 9);
			$this->onlinepayrolldeduction_model->setValue('peca_remarks', array_key_exists('peca_remarks',$_REQUEST['newpayroll']) ? $_REQUEST['newpayroll']['peca_remarks'] : null);
			$result = $this->onlinepayrolldeduction_model->update(array('request_no' => $_REQUEST['newpayroll']['request_no']));	
			$this->db->trans_commit();
			echo "{'success':true,'msg':'Request was successfully approved.'}";
		}
		
		else{
			$this->db->trans_rollback();
			echo "{'success':false,'msg':'Request was NOT successfully approved.'}";
		}
	}
	
	function firstDayNextMonth($date){
		$month = substr($date, 4,2);
		$year = substr($date, 0,4);
		
		if($month < 12){
			$month = $month + 1;
			$month = str_pad($month, 2, "0", STR_PAD_LEFT);
			return $year.$month."01";
		}
		else{
			$year = $year + 1;
			$year = str_pad($year, 2, "0", STR_PAD_LEFT);
			return $year."01"."01";
		}
	}
	
	function lastDayLastMonth($date){
		$month = substr($date, 4,2);
		$year = substr($date, 0,4);
		
		if($month > 1){
			$month = $month - 1;
			$month = str_pad($month, 2, "0", STR_PAD_LEFT);
			$days = days_in_month($month, $year);
			return $year.$month.$days;
		}
		else{
			$month = "12";
			$year = $year - 1;
			$year = str_pad($year, 2, "0", STR_PAD_LEFT);
			$days = days_in_month($month, $year);
			return $year.$month.$days;
		}
	}
	
	function adjustPayrollDeduction2($mode)
	{
		log_message('debug', 'adjustpd 0zzzzzz');
		
		$param['pd.status_flag >='] = '1';
		$param['ow.request_type'] =  'PAYR';
		$param['pd.request_no'] = $_REQUEST['newpayroll']['request_no'];
		
		$data = $this->onlinepayrolldeduction_model->getPayrollDeduction(
													$param
													,null
													,null
													,null
													,null
													,null
													,null
													,null
													,null
													,array('pd.employee_id'				
														,'pd.start_date'				
														,'pd.end_date'				
														,'pd.amount'
														,'pd.transaction_code'
														,'pd.transaction_period'
														,'pd.transaction_type'
														)
													);
		if(!isset($data['list'][0])){
			die('{"success":false,"msg":"Request no. does not exist"}');
		}
		
		$pd = array(
						'employee_id' => $data['list'][0]['employee_id']
						,'transaction_code' => $data['list'][0]['transaction_code']
						,'start_date' => $data['list'][0]['start_date']
						,'end_date' => $data['list'][0]['end_date']
						,'amount' => $data['list'][0]['amount']
						,'transaction_type' => $data['list'][0]['transaction_type']
						,'transaction_period' => $data['list'][0]['transaction_period']
						,'created_by' => $_REQUEST['newpayroll']['created_by']
					);
		
		//$_REQUEST['newpayroll']['start_date'] = $this->formatDateMMsDDsYYYYToYYYYMMDD($_REQUEST['newpayroll']['start_date']);
		//$_REQUEST['newpayroll']['end_date'] = $this->formatDateMMsDDsYYYYToYYYYMMDD($_REQUEST['newpayroll']['end_date']);
		
		$new = $pd;
		
		if($mode == 'update'){
			$pdEntry = $this->getEmployeePDEntries($new['employee_id'], true);
		}
		else{
			$pdEntry = $this->getEmployeePDEntries($new['employee_id']);
		}
		
		$old = $pdEntry['list'];
		$adjustSuccess = true;
		$completeOverlap = false;
		
		$this->db->trans_begin();
		foreach ($old AS $data) 
		{
			if(($data['start_date'] < $new['start_date']) && ($data['end_date'] > $new['start_date'])) {
				log_message('debug', 'adjustpd 1zzzzzz');
				$data_end_date = $data['end_date'];
				$data['end_date'] = $this->getPreviousDay($new['start_date']);
				$updateResult = $this->updatePD($data, "D");
				
				if($mode=='update'){
					$addResult = $this->updatePD($new);
				}
				else {
					$addResult = $this->addPD($new);
				}
					
				if($data_end_date > $new['end_date']){
					log_message('debug', 'adjustpd 1.1zzzzzz');
					$data['start_date'] = $this->getNextDay($new['end_date']);
					$data['end_date'] = $data_end_date;
					$addResult1 = $this->addPD($data, "D");
				}
				
				if($updateResult['affected_rows'] <= 0) {
					log_message('debug', 'adjustpd 1.1.1zzzzzz');
					$adjustSuccess = false;
					break;
				}
				else if($addResult['error_code'] != 0) {
					log_message('debug', 'adjustpd 1.1.2zzzzzz');
					$adjustSuccess = false;
					break;
				}
				else if(isset($addResult1['error_code'])){
					if($addResult1['error_code'] != 0){
						log_message('debug', 'adjustpd 1.1.3zzzzzz');
						$adjustSuccess = false;
						break;
					}
				}
				else {
					/*$this->onlinepayrolldeduction_model->setValue('status_flag', 9);
					$this->onlinepayrolldeduction_model->setValue('peca_remarks', array_key_exists('peca_remarks',$_REQUEST['newpayroll']) ? $_REQUEST['newpayroll']['peca_remarks'] : null);
					$result = $this->onlinepayrolldeduction_model->update(array('request_no' => $_REQUEST['newpayroll']['request_no']));	
					if($result['affected_rows']== 0){
						log_message('debug', 'adjustpd 1.1.4zzzzzz');
						$adjustSuccess = false;
					}*/
				}
			}
			else if(($data['start_date'] > $new['start_date']) && ($new['end_date'] > $data['start_date'])){
				log_message('debug', 'adjustpd 2zzzzzz');
				if ($data['end_date'] < $new['end_date']) {	
					log_message('debug', 'adjustpd 2.1zzzzzz');
					$adjustSuccess = false;
					$completeOverlap = true;
					break;																													
				}	
				else if ($data['end_date'] > $new['end_date']) {
					log_message('debug', 'adjustpd 2.2zzzzzz');
					
					if($mode=='update'){
						log_message('debug', 'adjustpd 2.2.1zzzzzz');
						$addResult = $this->updatePD($new);
					}
					else{
						log_message('debug', 'adjustpd 2.2.2zzzzzz');
						$addResult = $this->addPD($new);
					}
			
					$old_start_date = $data['start_date'];
					$data['start_date'] = $this->getNextDay($new['end_date']);
					$updateResult = $this->updatePD($data,'D', $old_start_date);	

					if($addResult['error_code'] != 0) { 
						$adjustSuccess = false;
						log_message('debug', 'adjustpd 2.2.3zzzzzz');
						break;
					}	
					else if($updateResult['affected_rows'] <= 0) {
						$adjustSuccess = false;
						log_message('debug', 'adjustpd 2.2.4zzzzzz');
						break;
					}
					else {
						/*$this->onlinepayrolldeduction_model->setValue('status_flag', 9);
						$this->onlinepayrolldeduction_model->setValue('peca_remarks', array_key_exists('peca_remarks',$_REQUEST['newpayroll']) ? $_REQUEST['newpayroll']['peca_remarks'] : null);
						$result = $this->onlinepayrolldeduction_model->update(array('request_no' => $_REQUEST['newpayroll']['request_no']));	
						if($result['affected_rows']== 0){
							log_message('debug', 'adjustpd 2.2.4.1zzzzzz');
							$adjustSuccess = false;
						}	*/
						
						log_message('debug', 'adjustpd 2.2.5zzzzzz');
					}
				}	
				else {   //$pdEntry[0]['end_date'] == $new_end_date	
					log_message('debug', 'adjustpd 2.3zzzzzz');
					if($mode=='update'){
						$addResult = $this->updatePD($new);
					}
					else{
						$addResult = $this->addPD($new);
					}
					
					$deleteResult = $this->deletePD($data);
					
					if($addResult['error_code'] != 0){ 
						log_message('debug', 'adjustpd 2.3.1zzzzzz');
						$adjustSuccess = false;
						break;
					}	
					else if($deleteResult['affected_rows'] <= 0) { 
						log_message('debug', 'adjustpd 2.3.2zzzzzz');
						$adjustSuccess = false;
						break;
					}
					else {
						/*$this->onlinepayrolldeduction_model->setValue('status_flag', 9);
						$this->onlinepayrolldeduction_model->setValue('peca_remarks', array_key_exists('peca_remarks',$_REQUEST['newpayroll']) ? $_REQUEST['newpayroll']['peca_remarks'] : null);
						$result = $this->onlinepayrolldeduction_model->update(array('request_no' => $_REQUEST['newpayroll']['request_no']));	
						if($result['affected_rows']== 0){
							log_message('debug', 'adjustpd 2.3.3zzzzzz');
							$adjustSuccess = false;
						}	*/				
					}
				}		
			}
			else if($data['start_date'] == $new['start_date']){ // $pdEntry[0]['start_date'] == $new_start_date 
				//Primary key constraint, start date is PK of t_payroll_deduction table
				log_message('debug', 'adjustpd 3zzzzzz');
				/*$checkDuplicate = $this->payrolldeduction_model->checkDuplicateKeyEntry(
																		array('employee_id' => $new['employee_id']
																			  ,'transaction_code' => $new['transaction_code']
																			  ,'start_date' => $new['start_date'])
																	);
				if($checkDuplicate['error_code'] == 1){
					$result['error_code'] = 2;
					$result['error_message'] = $checkDuplicate['error_message'];
					echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';	
					break;
				}
				else {*/
					$addResult = $this->addPD($new);
					if($addResult['error_code'] != 0){ 
						log_message('debug', 'adjustpd 3.1zzzzzz');
						$adjustSuccess = false;
						break;
					}
					else {
						/*$this->onlinepayrolldeduction_model->setValue('status_flag', 9);
						$this->onlinepayrolldeduction_model->setValue('peca_remarks', array_key_exists('peca_remarks',$_REQUEST['newpayroll']) ? $_REQUEST['newpayroll']['peca_remarks'] : null);
						$result = $this->onlinepayrolldeduction_model->update(array('request_no' => $_REQUEST['newpayroll']['request_no']));	
						if($result['affected_rows']== 0){
							log_message('debug', 'adjustpd 3.2zzzzzz');
							$adjustSuccess = false;
						}	*/
					}
				//}				
			}
		}
		
		if($adjustSuccess && $this->db->trans_status()===TRUE){
			$this->onlinepayrolldeduction_model->setValue('status_flag', 9);
			$this->onlinepayrolldeduction_model->setValue('peca_remarks', array_key_exists('peca_remarks',$_REQUEST['newpayroll']) ? $_REQUEST['newpayroll']['peca_remarks'] : null);
			$result = $this->onlinepayrolldeduction_model->update(array('request_no' => $_REQUEST['newpayroll']['request_no']));	
			if($result['affected_rows']== 0){
				log_message('debug', 'adjustpd 3.2zzzzzz');
				$this->db->trans_rollback();
				echo "{'success':false,'msg':'Request was NOT successfully approved.'}";
			}
			else{
			$this->db->trans_commit();
			echo "{'success':true,'msg':'Request was successfully approved.'}";
			}
		}
		
		else{
			$this->db->trans_rollback();
			if($completeOverlap){
				echo "{'success':false,'msg':'Cannot Save Data. Entry Completely overlapped saved entry.'}";
			}
			else{
				echo "{'success':false,'msg':'Request was NOT successfully approved.'}";
			}
		}
	}
	
	/**
	 * @desc To retrieve payroll deduction entries of an employee for specific transaction type 
	 * @return array
	 */
	function getEmployeePDEntries($employee_id, $is_mode_update=false)
	{
		$acctgPeriod = date("Ymd", strtotime($this->parameter_model->getParam('ACCPERIOD')));
		$param = array('employee_id' => $employee_id, /*'transaction_period >=' => $acctgPeriod,*/ 'status_flag'=> '1');	
		if($is_mode_update){
			$param['start_date <>'] = $_REQUEST['newpayroll']['start_date'];
		}	
		
		$data = $this->payrolldeduction_model->get_list(
			$param,
			null,
			null,
			array('employee_id'
					,'transaction_code'	
					,'start_date'				
					,'end_date'
					,'amount'
					,'created_by')
		);
		return $data;
	}	
	
	function getPreviousDay($date = '')
	{
		$unix = mysql_to_unix($date);
		$unix = $unix - 86400; //no. of secs in 1 day
		return date("Ymd",$unix ); //convert back to mysql
	}
	
	function getNextDay($date = '')
	{
		$unix = mysql_to_unix($date);
		$unix = $unix + 86400; //no. of secs in 1 day
		return date("Ymd",$unix ); //convert back to mysql
	}
	
	/**
	 * @desc Checks if the start date is not prior to the accounting period and is the first day of the month
	 * @param $start_date
	 * @param $accounting_period
	 * @return 0 - valid, 1 - otherwise
	 */
	function checkStartDate($start_date){
		if(substr($start_date, 6,2)!= '01')
			return 1;
		else return 0;	
	}
	
	/**
	 * @desc Checks if the end date is not prior to the accounting period and is the last day of the month
	 * @param $end_date
	 * @param $accounting_period
	 * @return 0 - valid, 1 - otherwise
	 */
	function checkEndDate($end_date){
		$endDateMonth = substr($end_date, 4,2);
		$endDateYear = substr($end_date, 0,4);
		$endDateDay = substr($end_date, 6,2);
		
		if($endDateDay != days_in_month($endDateMonth,$endDateYear))
			return 1;
		else return 0;	
	}
	
	/**
	 * @desc Approve change request
	 */
	function approve()
	{
		// $_REQUEST['data'] = array('request_no' => '27');
		log_message('debug', "[START] Controller online_payroll_deduction:approve");
		log_message('debug', "online_payroll_deduction param exist?:".array_key_exists('data',$_REQUEST));
		
		if ((array_key_exists('data',$_REQUEST))&&($_REQUEST['data']['status_flag']>2)&&($_REQUEST['data']['status_flag']<9)) {
			$this->onlinepayrolldeduction_model->populate($_REQUEST['data']);
			$status = $this->workflow_model->checkNextApprover('PAYR', $_REQUEST['data']['status_flag']);
			if($status != 0 ){
				if($status != 9){
					$this->onlinepayrolldeduction_model->setValue('status_flag', $status);
					$this->onlinepayrolldeduction_model->setValue('peca_remarks', array_key_exists('peca_remarks',$_REQUEST['data']) ? $_REQUEST['data']['peca_remarks'] : null);
					$result = $this->onlinepayrolldeduction_model->update(array('request_no' => $_REQUEST['data']['request_no']));	
					if($result['affected_rows'] <= 0){
						echo "{'success':false,'msg':'Request was NOT successfully approved.'}";
					}
					else{
						echo "{'success':true,'msg':'Request was successfully approved.'}";
					}
				}
				else{
					if($status == 9)
					{
						$_REQUEST['pd']['request_no'] = $_REQUEST['data']['request_no'];
						$param['pd.status_flag >='] = '1';
						$param['ow.request_type'] =  'PAYR';
						$param['pd.request_no'] = $_REQUEST['pd']['request_no'];
						$data = $this->onlinepayrolldeduction_model->getPayrollDeduction(
																	$param
																	,null
																	,null
																	,null
																	,null
																	,null
																	,null
																	,null
																	,null
																	,array('pd.employee_id'				
																		,'pd.start_date'				
																		,'pd.end_date'				
																		,'pd.amount'
																		,'pd.transaction_code'
																		,'pd.transaction_period'
																		,'pd.transaction_type'
																		)
																	);
 						/* foreach($data['list'] as $key => $val){
							$data['list'][$key]['start_date'] = date("m/d/Y", strtotime($val['start_date']));
							$data['list'][$key]['end_date'] = date("m/d/Y", strtotime($val['end_date']));
							$data['list'][$key]['transaction_period'] = date("m/d/Y", strtotime($val['transaction_period']));
						} */
						$params = array(
										'employee_id' => $data['list'][0]['employee_id']
										,'transaction_code' => $data['list'][0]['transaction_code']
										,'start_date' => $data['list'][0]['start_date']
										,'end_date' => $data['list'][0]['end_date']
										,'amount' => $data['list'][0]['amount']
										,'transaction_type' => $data['list'][0]['transaction_type']
										,'transaction_period' => $data['list'][0]['transaction_period']
										,'created_by' => $_REQUEST['user']
									);
						$error_code = $this->addAfterApproval($params, $_REQUEST['pd']['request_no']);
						if($error_code == 0){
							$this->onlinepayrolldeduction_model->setValue('status_flag', 9);
							$this->onlinepayrolldeduction_model->setValue('peca_remarks', array_key_exists('peca_remarks',$_REQUEST['newpayroll']) ? $_REQUEST['newpayroll']['peca_remarks'] : null);
							$result = $this->onlinepayrolldeduction_model->update(array('request_no' => $_REQUEST['data']['request_no']));	
							if($result['affected_rows']== 0){
								echo "{'success':false,'msg':'Request NOT successfully approved.'}";
							}
							else{
								echo "{'success':true,'msg':'Request successfully approved.'}";
							}
						}
						/*else if($error_code == 2){
							echo '{"success":false,"msg":"Duplicate entry for the fields employee ID, transaction code and start date. Update?","error_code":"'.$error_code.'"}';
							//$this->onlinepayrolldeduction_model->setValue('status_flag', $_REQUEST['data']['status_flag']);
							//$result = $this->onlinepayrolldeduction_model->update(array('request_no' => $_REQUEST['data']['request_no']));
						}*/
						else if($error_code == 3){
							echo '{"success":false,"msg":"Cannot Save Data. Entry Completely overlapped saved entry.","error_code":"'.$error_code.'"}';
						}
						else if($error_code == 19){
							echo '{"success":false,"msg":"There are overlapped records, would you like to adjust?","error_code":"'.$error_code.'"}';
						}
						else{
							echo '{"success":false,"msg":"Request was NOT successfully approved.","error_code":"'.$error_code.'"}';
						}
					}//else
						//echo "{'success':true,'msg':'Request successfully approved.'}";				
				}
			}else
				echo "{'success':false,'msg':'Request was NOT successfully approved.'}";
		} else
			echo "{'success':false,'msg':'Request was NOT successfully approved.'}";
			
		log_message('debug', "[END] Controller online_payroll_deduction:approve");
	}

	function addAfterApproval($pd, $request_no)
	{
		log_message('debug', "[START] Controller online_payroll_deduction:addAfterApproval");
		$this->payrolldeduction_model->populate($pd);
		$this->payrolldeduction_model->setValue('new_pd', '1');
		$this->payrolldeduction_model->setValue('status_flag', '1');
		$this->payrolldeduction_model->delete(array('start_date' => $pd['start_date'], 'transaction_code' => $pd['transaction_code'], 'employee_id' => $pd['employee_id'], 'status_flag' => 0));
		/*$checkDuplicate = $this->payrolldeduction_model->checkDuplicateKeyEntry(array(
					'employee_id' => $pd['employee_id']
					,'start_date' => $pd['start_date']
					,'transaction_code' => $pd['transaction_code']
					));*/
					
		//$pdEntry = $this->getEmployeePDEntries($pd['employee_id']);
		//log_message('debug', "countzzzzzzz: ".$pdEntry['count']);
		//$command = $this->checkOverlapRecords($pdEntry['list'], $pd, 'add');
		$retrieveConflictResult = $this->payrolldeduction_model->retrieveConflict($pd['start_date'], $pd['end_date'], $pd['employee_id'], $pd['transaction_code']);
		
		/*if($checkDuplicate['error_code'] == 1){
			$result['error_code'] = 2;
			$result['error_message'] = $checkDuplicate['error_message'];
		}*/
		if($this->sameStartEndDateTransactionCodeExists($pd['start_date'], $pd['end_date'], $pd['employee_id'], $pd['transaction_code'])){
			//just update amount
			$this->payrolldeduction_model->setValue('amount', $pd['amount']);
			$this->payrolldeduction_model->update(array('start_date'=> $pd['start_date'], 'end_date'=>  $pd['end_date'], 'employee_id' => $pd['employee_id']));
			$result['error_code'] = 0;
		}
		
		else if($this->completeOverlapExists($pd['start_date'], $pd['end_date'], $pd['employee_id'], $pd['transaction_code'])){
			$result['error_code'] = 3;
		}
		else if($retrieveConflictResult['count']>0){
			$result['error_code'] = 19;
			$result['error_message'] = "There are overlapped records, would you like to adjust?";
		}
		else{
			$result = $this->payrolldeduction_model->insert();
			if ($result['affected_rows'] <= 0){
				$result['error_code'] = 1;
			}
			else{
				$result['error_code'] = 0;
			}
		}	
		log_message('debug', "[END] Controller online_payroll_deduction:addAfterApproval ");
		return $result['error_code'];
	}
	
	function completeOverlapExists($start_date, $end_date, $employee_id, $transaction_code){
		$result = $this->payrolldeduction_model->get(array('start_date >'=> $start_date, 'end_date <'=>  $end_date, 'employee_id' => $employee_id, 'transaction_code' => $transaction_code, 'status_flag' => '1'));
		if($result['count'] > 0){
			return 1;
		}
		else{
			return 0;
		}
	}
	
	function sameStartEndDateTransactionCodeExists($start_date, $end_date, $employee_id, $transaction_code){
		$result = $this->payrolldeduction_model->get(array('start_date'=> $start_date, 'end_date'=>  $end_date, 'employee_id' => $employee_id, 'transaction_code' => $transaction_code));
		if($result['count'] > 0){
			return 1;
		}
		else{
			return 0;
		}
	}
	
	/**
	 * @desc disapprove change request
	 */
	function disapprove()
	{
		// $_REQUEST['data'] = array('request_no' => '27');
		
		log_message('debug', "[START] Controller online_payroll_deduction:disapprove");
		log_message('debug', "online_payroll_deduction param exist?:".array_key_exists('data',$_REQUEST));
		
		if ((array_key_exists('data',$_REQUEST))&&($_REQUEST['data']['status_flag']>2)&&($_REQUEST['data']['status_flag']<9)) {
			$this->onlinepayrolldeduction_model->populate($_REQUEST['data']);
			$this->onlinepayrolldeduction_model->setValue('status_flag', '10');
			$result = $this->onlinepayrolldeduction_model->update();	
			if($result['affected_rows'] <= 0){
				echo "{'success':false,'msg':'Request was NOT successfully disapproved.'}";
			} else
				echo "{'success':true,'msg':'Request successfully disapproved.'}";
		} else
			echo "{'success':false,'msg':'Request was NOT successfully disapproved.'}";
			
		log_message('debug', "[END] Controller online_payroll_deduction:disapprove");
	}
	
	function updateAfterAdjust(){
		$param['pd.status_flag >='] = '1';
		$param['ow.request_type'] =  'PAYR';
		$param['pd.request_no'] = $_REQUEST['newpayroll']['request_no'];
		$data = $this->onlinepayrolldeduction_model->getPayrollDeduction(
													$param
													,null
													,null
													,null
													,null
													,null
													,null
													,null
													,null
													,array('pd.employee_id'				
														,'pd.start_date'				
														,'pd.end_date'				
														,'pd.amount'
														,'pd.transaction_code'
														,'pd.transaction_period'
														,'pd.transaction_type'
														)
													);
		
		if(!isset($data['list'][0])){
			die('{"success":false,"msg":"Request no. does not exist"}');
		}
		
		$pd = array(
						'employee_id' => $data['list'][0]['employee_id']
						,'transaction_code' => $data['list'][0]['transaction_code']
						,'start_date' => $data['list'][0]['start_date']
						,'end_date' => $data['list'][0]['end_date']
						,'amount' => $data['list'][0]['amount']
						,'transaction_type' => $data['list'][0]['transaction_type']
						,'transaction_period' => $data['list'][0]['transaction_period']
						,'created_by' => $_REQUEST['newpayroll']['created_by']
					);
		
		$this->payrolldeduction_model->populate($pd);
		$this->payrolldeduction_model->setValue('status_flag', '1');
		
		//$this->payrolldeduction_model->setValue('start_date', $_REQUEST['newpayroll']['start_date']);
		//$this->payrolldeduction_model->setValue('end_date', $_REQUEST['newpayroll']['end_date']);
		
		$result = $this->payrolldeduction_model->update(array('employee_id' => $pd['employee_id']
											   ,'transaction_code' => $pd['transaction_code']
											   ,'start_date' => $pd['start_date'])
										 );
		
		if($result['affected_rows'] <= 0 && !empty($result['error_message'])){ 
			echo '{"success":false,"msg":"Request was NOT successfully approved."}';
		} else {
			$this->onlinepayrolldeduction_model->setValue('status_flag', 9);
			$this->onlinepayrolldeduction_model->setValue('peca_remarks', array_key_exists('peca_remarks',$_REQUEST['newpayroll']) ? $_REQUEST['newpayroll']['peca_remarks'] : null);
			$result = $this->onlinepayrolldeduction_model->update(array('request_no' => $_REQUEST['newpayroll']['request_no']));	
			if($result['affected_rows']== 0){
				echo "{'success':false,'msg':'Request NOT successfully approved.'}";
			}
			else{
				echo "{'success':true,'msg':'Request successfully approved.'}";
			}
		}	
	}
	
}

?>