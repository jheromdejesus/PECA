<?php

/* Location: ./CodeIgniter/application/controllers/payroll_deduction.php */
class Payroll_deduction extends Asi_Controller {


	function Payroll_deduction()
	{
		parent::Asi_Controller();
		$this->load->model('payrolldeduction_model');
		$this->load->model('transactioncode_model');
		$this->load->model('parameter_model');
		$this->load->model('member_model');
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
//		if(array_key_exists('newpayroll', $_REQUEST)){
//			if(array_key_exists('employee_id', $_REQUEST['newpayroll']) && $_REQUEST['newpayroll']['employee_id']!= "")
//				$params['pd.employee_id LIKE'] =  $_REQUEST['newpayroll']['employee_id']."%";
//			if(array_key_exists('first_name', $_REQUEST['newpayroll']) && $_REQUEST['newpayroll']['first_name']!= "")
//				$params['me.first_name LIKE'] =  $_REQUEST['newpayroll']['first_name']."%";
//			if(array_key_exists('last_name', $_REQUEST['newpayroll']) && $_REQUEST['newpayroll']['last_name']!= "")
//				$params['me.last_name LIKE'] =  $_REQUEST['newpayroll']['last_name']."%";
//		}

		if(array_key_exists('employee_id', $_REQUEST) && $_REQUEST['employee_id']!= ""){
			$params['pd.employee_id LIKE'] =  $_REQUEST['employee_id']."%";
		}
		else{
			if(array_key_exists('first_name', $_REQUEST) && $_REQUEST['first_name']!= "")
				$params['me.first_name LIKE'] =  $_REQUEST['first_name']."%";
			if(array_key_exists('last_name', $_REQUEST) && $_REQUEST['last_name']!= "")
				$params['me.last_name LIKE'] =  $_REQUEST['last_name']."%";
		}
		
		$params['pd.status_flag'] =  '1';
		
		$data = $this->payrolldeduction_model->getPayrollDeductionList(
			$params,
			array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
			array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
			array('pd.employee_id'
					,'me.last_name'
					,'me.first_name'				
					,'pd.start_date'				
					,'pd.end_date'				
					,'pd.amount'
					,'pd.transaction_code')
			,'me.last_name, me.first_name ASC, pd.start_date'
		);

		foreach($data['list'] as $row => $value){
			$id = $value['employee_id'] . ':' . $value['transaction_code'] . ':' . $value['start_date'];
			$data['list'][$row]['id'] = $id;
			$data['list'][$row]['start_date'] = $this->formatDateYYYYMMDDToMMDDYYYY($data['list'][$row]['start_date']);
			$data['list'][$row]['end_date'] = $this->formatDateYYYYMMDDToMMDDYYYY($data['list'][$row]['end_date']);
		}
		
		
		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
		));
		
	}	
	
	function formatDateYYYYMMDDToMMDDYYYY($date){
		return substr($date, 4, 2) .  substr($date, 6, 2) .  substr($date, 0, 4);
	}	
	
	function formatDateMMDDYYYYToYYYYMMDD($date){
		return substr($date, 4, 4) .  substr($date, 0, 2) .  substr($date, 2, 2);
	}
	function formatDateMMsDDsYYYYToYYYYMMDD($date){
		return substr($date, 6, 4) .  substr($date, 0, 2) .  substr($date, 3, 2);
	}
	
	/**
	 * @desc Retrieves a single payroll deduction entry of an employee 
	 * @return array
	 */
	function show()
	{
		
		$start_date = $this->formatDateMMsDDsYYYYToYYYYMMDD($_REQUEST['newpayroll']['start_date']);
		$_REQUEST['filter'] = array(
			'pd.employee_id' => $_REQUEST['newpayroll']['employee_id']
			,'pd.transaction_code' => $_REQUEST['newpayroll']['transaction_code']
			,'pd.start_date' => $start_date
			,'pd.status_flag' => '1'
		);				  
		$data = $this->payrolldeduction_model->getPayrollDeduction(
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
					/*,'rt.transaction_description'*/)
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
		
		$data = $this->payrolldeduction_model->getPDEmployeeList(
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
		log_message('debug', "[START] Controller loan:addCharges");
		log_message('debug', "newpayroll param exist?:".array_key_exists('newpayroll',$_REQUEST));
		 
		if (array_key_exists('newpayroll',$_REQUEST)) {	
			$transaction_code = $this->transactioncode_model->transactionCodeExists($_REQUEST['newpayroll']['transaction_code'], 'PD');
			if ($transaction_code==0){
				echo "{'success':false,'msg':'Transaction type does not exist.','error_code':'153'}";
			}
			else {
				
				$_REQUEST['newpayroll']['start_date'] = $this->formatDateMMsDDsYYYYToYYYYMMDD($_REQUEST['newpayroll']['start_date']);
				$_REQUEST['newpayroll']['end_date'] = $this->formatDateMMsDDsYYYYToYYYYMMDD($_REQUEST['newpayroll']['end_date']);
				
				$this->payrolldeduction_model->delete(array('start_date' => $_REQUEST['newpayroll']['start_date'], 'transaction_code' => $_REQUEST['newpayroll']['transaction_code'], 'employee_id' => $_REQUEST['newpayroll']['employee_id'], 'status_flag' => 0));
				
				$acctgPeriod = date("Ymd", strtotime($this->parameter_model->getParam('ACCPERIOD')));
				//$pdEntry = $this->getEmployeePDEntries($_REQUEST['newpayroll']['employee_id']);
				$retrieveConflictResult = $this->payrolldeduction_model->retrieveConflict($_REQUEST['newpayroll']['start_date'], $_REQUEST['newpayroll']['end_date'], $_REQUEST['newpayroll']['employee_id'], $_REQUEST['newpayroll']['transaction_code']);
			
				if($this->member_model->employeeExists($_REQUEST['newpayroll']['employee_id']) == 0) {
					echo("{'success':false,'msg':'Employee does not exists.','error_code':'22'}");	
				}
				else if($this->member_model->employeeIsInactive($_REQUEST['newpayroll']['employee_id'])){
					echo("{'success':false,'msg':'Employee is inactive','error_code':'55'}");
				}
				else if ($this->checkStartDate($_REQUEST['newpayroll']['start_date'])) {
					echo("{'success':false,'msg':'Start date should be the first day of the month','error_code':'26'}");	
				}
				else if ($this->checkEndDate($_REQUEST['newpayroll']['end_date'])) {
					echo("{'success':false,'msg':'End date should be the last day of the month','error_code':'27'}");	
				}
				else if ($_REQUEST['newpayroll']['start_date'] < $acctgPeriod) {
					echo("{'success':false,'msg':'Start date should not be prior to accounting period','error_code':'28'}");	
				}
				else if ($_REQUEST['newpayroll']['end_date'] < $acctgPeriod) {
					echo("{'success':false,'msg':'End date should not be prior to accounting period','error_code':'29'}");	
				}
				else if ($_REQUEST['newpayroll']['start_date'] > $_REQUEST['newpayroll']['end_date']) {
					echo("{'success':false,'msg':'Start date should be less than end date','error_code':'30'}");	
				}
				else if($this->sameStartEndDateTransactionCodeExists($_REQUEST['newpayroll']['start_date'], $_REQUEST['newpayroll']['end_date'], $_REQUEST['newpayroll']['employee_id'], $_REQUEST['newpayroll']['transaction_code'])){
					//just update amount
					$this->payrolldeduction_model->setValue('amount', $_REQUEST['newpayroll']['amount']);
					$this->payrolldeduction_model->setValue('status_flag', 1);
					$this->payrolldeduction_model->update(array('start_date'=> $_REQUEST['newpayroll']['start_date'], 'end_date'=>  $_REQUEST['newpayroll']['end_date'], 'employee_id' => $_REQUEST['newpayroll']['employee_id']));
					$this->payrolldeduction_model->updateNewPDFlag($_REQUEST['newpayroll']['employee_id'],date('Ymd',strtotime($_REQUEST['newpayroll']['start_date'])),$_REQUEST['newpayroll']['transaction_code']);
					echo "{'success':true,'msg':'Data successfully saved.'}";
				}
				
				else if($this->completeOverlapExists($_REQUEST['newpayroll']['start_date'], $_REQUEST['newpayroll']['end_date'], $_REQUEST['newpayroll']['employee_id'], $_REQUEST['newpayroll']['transaction_code'])){
					echo("{'success':false,'msg':'Cannot Save Data. Entry Completely overlapped saved entry.'}");	
				}
				else if($retrieveConflictResult['count'] > 0){
					echo("{'success':false,'msg':'There are overlapped records, would you like to adjust?','error_code':'19'}");	
				}
				/*else if($this->checkDuplicate()){
					echo("{'success':false,'msg':'Employee Id, Transaction Code, Start Date already exists, would you like to update?','error_code':'2'}");	
				}*/
				else {
					//$command = $this->checkOverlapRecords($pdEntry['list'], $_REQUEST['newpayroll'], 'add');
					//if ($command==3){
						//echo("{'success':false,'msg':'There are overlapped records, would you like to adjust?','error_code':'19'}");	
					//}
					//else if ($command==0) {
						$this->payrolldeduction_model->populate($_REQUEST['newpayroll']);
						$this->payrolldeduction_model->setValue('new_pd', '1');
						$this->payrolldeduction_model->setValue('status_flag', '1');
						$this->payrolldeduction_model->setValue('new_pd', '1');
						$this->payrolldeduction_model->setValue('start_date', $_REQUEST['newpayroll']['start_date']);
						$this->payrolldeduction_model->setValue('end_date', $_REQUEST['newpayroll']['end_date']);
						$this->payrolldeduction_model->setValue('transaction_type', 'A');
						$this->payrolldeduction_model->setValue('transaction_period', $acctgPeriod);
						
						/*$checkDuplicate = $this->payrolldeduction_model->checkDuplicateKeyEntry(
																			array('employee_id' => $_REQUEST['newpayroll']['employee_id']
																					,'transaction_code' => $_REQUEST['newpayroll']['transaction_code']
																					,'start_date' => $_REQUEST['newpayroll']['start_date'])
																			);*/	
						/*if($checkDuplicate['error_code'] == 1){
							$result['error_code'] = 1;
							$result['error_message'] = $checkDuplicate['error_message'];
						}*/
						//else 
							$result = $this->payrolldeduction_model->insert();
						
						if($result['error_code'] == 0) echo "{'success':true,'msg':'Data successfully saved.'}";
						else echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';
					//}
					/*else if($command==1){
						$this->adjustPayrollDeduction($pdEntry['list'], $_REQUEST['newpayroll']);
					}	 */
				}
			}
		}
		else {
			echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
		}		  			
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
	
	function sameStartEndDateExists($start_date, $end_date, $employee_id){
		$result = $this->payrolldeduction_model->get(array('start_date'=> $start_date, 'end_date'=>  $end_date, 'employee_id' => $employee_id));
		if($result['count'] > 0){
			return 1;
		}
		else{
			return 0;
		}
	}
	
	function checkDuplicate(){
		$checkDuplicate = $this->payrolldeduction_model->checkDuplicateKeyEntry(
							array('employee_id' => $_REQUEST['newpayroll']['employee_id']
									,'transaction_code' => $_REQUEST['newpayroll']['transaction_code']
									,'start_date' => $_REQUEST['newpayroll']['start_date'])
							);	
							
		if($checkDuplicate['error_code'] == 1){
			return 1;
		}
		else{
			return 0;
		}
	}
	
	/**
	 * @desc Updates payroll deduction record
	 * @return 
	 */
	function update()
	{
		log_message('debug', "[START] Controller loan:addCharges");
		log_message('debug', "newpayroll param exist?:".array_key_exists('newpayroll',$_REQUEST));
		  
		if (array_key_exists('newpayroll',$_REQUEST)) {	
			$transaction_code = $this->transactioncode_model->transactionCodeExists($_REQUEST['newpayroll']['transaction_code'], 'PD');
			if ($transaction_code==0){
				echo "{'success':false,'msg':'Transaction type does not exist.'}";
			}
			else {
				$_REQUEST['newpayroll']['start_date'] = $this->formatDateMMsDDsYYYYToYYYYMMDD($_REQUEST['newpayroll']['start_date']);
				$_REQUEST['newpayroll']['end_date'] = $this->formatDateMMsDDsYYYYToYYYYMMDD($_REQUEST['newpayroll']['end_date']);
				
				$acctgPeriod = date("Ymd", strtotime($this->parameter_model->getParam('ACCPERIOD')));
				//$pdEntry = $this->getEmployeePDEntries($_REQUEST['newpayroll']['employee_id'],true);
				$retrieveConflictResult = $this->payrolldeduction_model->retrieveConflict($_REQUEST['newpayroll']['start_date'], $_REQUEST['newpayroll']['end_date'], $_REQUEST['newpayroll']['employee_id'], $_REQUEST['newpayroll']['transaction_code'], true);
				
				if($this->member_model->employeeExists($_REQUEST['newpayroll']['employee_id']) == 0) {
					echo("{'success':false,'msg':'Employee does not exists.','error_code':'22'}");	
				}
				else if($this->member_model->employeeIsInactive($_REQUEST['newpayroll']['employee_id'])){
					echo("{'success':false,'msg':'Employee is inactive','error_code':'55'}");
				}
				else if ($this->checkStartDate($_REQUEST['newpayroll']['start_date'])) {
					echo("{'success':false,'msg':'Start date should be the first day of the month','error_code':'26'}");	
				}
				else if ($this->checkEndDate($_REQUEST['newpayroll']['end_date'])) {
					echo("{'success':false,'msg':'End date should be the last day of the month','error_code':'27'}");	
				}
				else if ($_REQUEST['newpayroll']['start_date'] > $_REQUEST['newpayroll']['end_date']) {
					echo("{'success':false,'msg':'Start date should be less than end date','error_code':'30'}");	
				}
				else if($this->sameStartEndDateTransactionCodeExists($_REQUEST['newpayroll']['start_date'], $_REQUEST['newpayroll']['end_date'], $_REQUEST['newpayroll']['employee_id'], $_REQUEST['newpayroll']['transaction_code'])){
					//just update amount
					$this->payrolldeduction_model->setValue('amount', $_REQUEST['newpayroll']['amount']);
					$this->payrolldeduction_model->setValue('status_flag', 1);
					$this->payrolldeduction_model->update(array('start_date'=> $_REQUEST['newpayroll']['start_date'], 'end_date'=>  $_REQUEST['newpayroll']['end_date'], 'employee_id' => $_REQUEST['newpayroll']['employee_id']));
					echo "{'success':true,'msg':'Data successfully saved.'}";
				}
				else if($this->completeOverlapExists($_REQUEST['newpayroll']['start_date'], $_REQUEST['newpayroll']['end_date'], $_REQUEST['newpayroll']['employee_id'], $_REQUEST['newpayroll']['transaction_code'])){
					echo("{'success':false,'msg':'Cannot Save Data. Entry Completely overlapped saved entry.'}");	
				}
				else if($retrieveConflictResult['count'] > 0){
					echo("{'success':false,'msg':'There are overlapped records, would you like to adjust?','error_code':'19'}");	
				}
				else {
					//$command = $this->checkOverlapRecords($pdEntry['list'], $_REQUEST['newpayroll'], 'update');	
					//if ($command==0) {
						$this->payrolldeduction_model->populate($_REQUEST['newpayroll']);
						$this->payrolldeduction_model->setValue('status_flag', '1');
						
						$this->payrolldeduction_model->setValue('start_date', $_REQUEST['newpayroll']['start_date']);
						$this->payrolldeduction_model->setValue('end_date', $_REQUEST['newpayroll']['end_date']);
						
						$result = $this->payrolldeduction_model->update(array('employee_id' => $_REQUEST['newpayroll']['employee_id']
															   ,'transaction_code' => $_REQUEST['newpayroll']['transaction_code']
															   ,'start_date' => $_REQUEST['newpayroll']['start_date'])
														 );
						
						if($result['affected_rows'] <= 0 && !empty($result['error_message'])){ 
							echo '{"success":false,"msg":"'.$result['error_message'].'"}';
						} else {
							echo "{'success':true,'msg':'Data successfully saved.'}";
						}
					//}
					//else if ($command==3){
						//echo("{'success':false,'msg':'There are overlapped records, would you like to adjust?','error_code':'19'}");	
					//}
					/* else if($command==1){
						$this->adjustPayrollDeduction($pdEntry['list'], $_REQUEST['newpayroll']);
					}	 */
				}
			}	
		}
		else {
			echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
		}		  			
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
			$start_date = $this->formatDateMMsDDsYYYYToYYYYMMDD($_REQUEST['newpayroll']['start_date']);
			$this->payrolldeduction_model->populate($_REQUEST['newpayroll']);
			$this->payrolldeduction_model->setValue('status_flag', '0');
			
			$this->payrolldeduction_model->setValue('start_date', $start_date);
			if (array_key_exists('end_date',$_REQUEST['newpayroll'])) {	
				$end_date = $this->formatDateMMsDDsYYYYToYYYYMMDD($_REQUEST['newpayroll']['end_date']);
				$this->payrolldeduction_model->setValue('end_date', $end_date);
			}
			
			$result = $this->payrolldeduction_model->update(array(
				'employee_id' => $_REQUEST['newpayroll']['employee_id']
				,'transaction_code' => $_REQUEST['newpayroll']['transaction_code']
				,'start_date' => $start_date
				,'status_flag' => '1'
			));
			
			if($result['affected_rows'] <= 0){
				log_message('debug', 'Data was NOT successfully deleted.zzzzzzz');
				echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
			} else {
				log_message('debug', 'Data was successfully deleted.zzzzzzz');
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
		
		$updateArray = array('employee_id' => $payroll['employee_id']
						   ,'transaction_code' => $payroll['transaction_code']
						   ,'start_date' => $start_date);
		
		$result = $this->payrolldeduction_model->update($updateArray);
		return $result;							 	
	}
	
	/**
	 * @desc Inserts new record to t_payroll_deduction
	 */
	function addPD($payroll='',$type='A')
	{
		$acctgPeriod = $this->getParam('ACCPERIOD');
		$this->payrolldeduction_model->populate($payroll);
		$this->payrolldeduction_model->setValue('status_flag', '1');
		$this->payrolldeduction_model->setValue('transaction_type', $type);
		$this->payrolldeduction_model->setValue('transaction_period', date("Ymd",strtotime($acctgPeriod)));
			
		$checkDuplicate = $this->payrolldeduction_model->checkDuplicateKeyEntry(
																array('employee_id' => $payroll['employee_id']
																		,'transaction_code' => $payroll['transaction_code']
																		,'start_date' => $payroll['start_date'])
																);		
		if($checkDuplicate['error_code'] == 1){
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
		$this->count = 0;
		$command = 0;	//0 - to add directly; 1 - OK to adjust; 2 - not OK to adjust 
		foreach ($old AS $data) 
		{
			log_message('debug', 'old startdatezzzz: '. $data['start_date']);
			log_message('debug', 'old enddatezzzz: '. $data['end_date']);
			log_message('debug', 'new startdatezzzz: '. $new['start_date']);
			log_message('debug', 'new enddatezzzz: '. $new['end_date']);
		
			//log_message('debug', 'COzzzz0');
			if ($data['start_date'] == $new['start_date']){
				//log_message('debug', 'COzzzz1');
				$this->count++;
			} 	
			else if($data['start_date'] < $new['start_date'] && $new['start_date'] <= $data['end_date'])
			{
				//log_message('debug', 'COzzzz2');
				$this->count++;
			} 	
			else if($data['start_date'] <= $new['end_date'] && $data['end_date'] >= $new['end_date']){
				//log_message('debug', 'COzzzz3');
				$this->count++;
			}	
			else if($new['start_date'] < $data['start_date'] && $new['end_date'] > $data['start_date']){
				//log_message('debug', 'COzzzz4');
				$this->count++;
			}	
		}
		
		if ($this->count==0) {
			$command = 0;
		}
		/*else if ($this->count>0 && $function=='update'){
			$command = 0;
		}*/
		else {
			$command = 3;
			/* echo ("{'success':false,'msg':There are '.$count.' overlap records. Would you like to adjust payroll deduction?','154'}");
			$command = 2;
			if ($_REQUEST['command']=="YES") $command = 1;
			else $command = 2; */	
		}
	
		return $command;
	}
	
	function adjustPayrollDeduction($old, $new)
	{
		$adjustSuccess = true;
		$completeOverlap = false;
		$this->db->trans_begin();
		foreach ($old AS $data) 
		{
			if(($data['start_date'] < $new['start_date']) && ($data['end_date'] > $new['start_date'])) {
		
				$data['end_date'] = $this->getPreviousDay($new['start_date']);
				$updateResult = $this->updatePD($data);
				$addResult = $this->addPD($new);
					
				if($data['end_date'] > $new['end_date']){
					$data['start_date'] = $this->getNextDay($new['end_date']);
					$addResult1 = $this->addPD($data);
				}
				
				if($updateResult['affected_rows'] <= 0) {
					//echo '{"success":false,"msg":"'.$updateResult['error_message'].'"}';
					$adjustSuccess = false;
					break;
				}
				else if($addResult['error_code'] != 0) {
					//echo '{"success":false,"msg":"'.$addResult['error_message'].'"}';
					$adjustSuccess = false;
					break;
				}
				else if($addResult1['error_code'] != 0) {
					//echo '{"success":false,"msg":"'.$addResult1['error_message'].'"}';
					$adjustSuccess = false;
					break;
				}
				else {
					//echo "{'success':true,'msg':'Data successfully saved.'}";
				}
			}
			else if(($data['start_date'] > $new['start_date']) && ($new['end_date'] > $data['start_date'])){
			
				if ($data['end_date'] < $new['end_date']) {	
					//echo("{'success':false,'msg':'Cannot Save Data. Entry Completely overlapped saved entry.','error_code':'21'}");
					$adjustSuccess = false;
					$completeOverlap = true;
					break;																													
				}	
				else if ($data['end_date'] > $new['end_date']) {		
					$addResult = $this->addPD($new);
			
					$data['start_date'] = $this->getNextDay($new['end_date']);
					$updateResult = $this->updatePD($data);	

					if($addResult['error_code'] != 0) { 
						//echo '{"success":false,"msg":"'.$addResult['error_message'].'"}';
						$adjustSuccess = false;
						break;
					}	
					else if($updateResult['affected_rows'] <= 0) {
						//echo '{"success":false,"msg":"'.$updateResult['error_message'].'"}';
						$adjustSuccess = false;
						break;
					}
					else {
						//echo "{'success':true,'msg':'Data successfully saved.'}";		
					}
				}	
				else {   //$pdEntry[0]['end_date'] == $new_end_date	
					$addResult = $this->addPD($new);
					$deleteResult = $this->deletePD($data);
					
					if($addResult['error_code'] != 0){ 
						//echo '{"success":false,"msg":"'.$addResult['error_message'].'"}';
						$adjustSuccess = false;
						break;
					}	
					else if($deleteResult['affected_rows'] <= 0) { 
						//echo '{"success":false,"msg":"'.$deleteResult['error_message'].'"}';
						$adjustSuccess = false;
						break;
					}
					else {
						//echo "{'success':true,'msg':'Data successfully saved.'}";	
					}
				}		
			}
			else if($data['start_date'] == $new['start_date']) {
			
			// $pdEntry[0]['start_date'] == $new_start_date 
				//Primary key constraint, start date is PK of t_payroll_deduction table
				/*$checkDuplicate = $this->payrolldeduction_model->checkDuplicateKeyEntry(
																		array('employee_id' => $new['employee_id']
																			  ,'transaction_code' => $new['transaction_code']
																			  ,'start_date' => $new['start_date'])
																	);
				if($checkDuplicate['error_code'] == 1){
					$result['error_code'] = 1;
					$result['error_message'] = $checkDuplicate['error_message'];
					echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';	
					$adjustSuccess = false;
					break;
				}*/
				
				$addResult = $this->addPD($new);
				if($addResult['error_code'] != 0){ 
					$adjustSuccess = false;
					break;
				}	
			}
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
	
	
	function adjustPD($mode){
		$start = $this->formatDateMMsDDsYYYYToYYYYMMDD($_REQUEST['newpayroll']['start_date']);
		$end = $this->formatDateMMsDDsYYYYToYYYYMMDD($_REQUEST['newpayroll']['end_date']);
		$amount = $_REQUEST['newpayroll']['amount'];
		$employee_id = $_REQUEST['newpayroll']['employee_id'];
		$transaction_code = $_REQUEST['newpayroll']['transaction_code'];
		$transaction_period = date("Ymd", strtotime($this->parameter_model->getParam('ACCPERIOD')));
		
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
			
			$this->payrolldeduction_model->delete(array('start_date' => $new_start_date, 'transaction_code' => $transaction_code, 'employee_id' => $employee_id, 'status_flag' => 0));
			$this->payrolldeduction_model->insertPD($new_start_date, $new_end_date, $new_amount, $delimited, $employee_id, $transaction_code, $transaction_period, $_REQUEST['newpayroll']['created_by']);
		}	
		
		$this->payrolldeduction_model->updateNewPDFlag($_REQUEST['newpayroll']['employee_id'],date('Ymd',strtotime($_REQUEST['newpayroll']['start_date'])),$_REQUEST['newpayroll']['transaction_code']);
		
		if($this->db->trans_status()===TRUE){
			$this->db->trans_commit();
			echo "{'success':true,'msg':'Data successfully saved.'}";	
		}
		else{
			$this->db->trans_rollback();
			echo "{'success':false,'msg':'Data NOT successfully saved.'}";	
		}
	
	}
	function adjustPD2($mode)
	{
		if (array_key_exists('newpayroll',$_REQUEST)) 
		{	
			$_REQUEST['newpayroll']['start_date'] = $this->formatDateMMsDDsYYYYToYYYYMMDD($_REQUEST['newpayroll']['start_date']);
			$_REQUEST['newpayroll']['end_date'] = $this->formatDateMMsDDsYYYYToYYYYMMDD($_REQUEST['newpayroll']['end_date']);
			$new = $_REQUEST['newpayroll'];
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
				if($data['start_date'] < $new['start_date']) {
					//log_message('debug', 'zzzzzzzz1');
					$data_end_date = $data['end_date'];
					$data['end_date'] = $this->getPreviousDay($new['start_date']);
					$updateResult = $this->updatePD($data,'D');
					
					if($mode=='update'){
						$addResult = $this->updatePD($new);
					}
					else {
						$addResult = $this->addPD($new);
					}
					
					if($data_end_date > $new['end_date']){
						$data['start_date'] = $this->getNextDay($new['end_date']);
						$data['end_date'] = $data_end_date;
						$addResult1 = $this->addPD($data);
					}
					
					if($updateResult['affected_rows'] <= 0) {
						//echo '{"success":false,"msg":"'.$updateResult['error_message'].'"}';
						$adjustSuccess = false;
						break;
					}
					else if($addResult['error_code'] != 0) {
						//echo '{"success":false,"msg":"'.$addResult['error_message'].'"}';
						$adjustSuccess = false;
						break;
					}
					else {
						//echo "{'success':true,'msg':'Data successfully saved.'}";	
					}
				}
				else if($data['start_date'] > $new['start_date']){
					//log_message('debug', 'zzzzzzzz2');
					if ($data['end_date'] < $new['end_date']) {	
						//echo("{'success':false,'msg':'Cannot Save Data. Entry Completely overlapped saved entry.','error_code':'21'}");
						$adjustSuccess = false;
						$completeOverlap = true;
						break;																													
					}	
					else if ($data['end_date'] > $new['end_date']) {	
						//log_message('debug', 'zzzzzzzz2.2');
						
						if($mode=='update'){
							$addResult = $this->updatePD($new);
						}
						else{
							$addResult = $this->addPD($new);
						}
						
						$old_start_date = $data['start_date'];
						$data['start_date'] = $this->getNextDay($new['end_date']);
						$updateResult = $this->updatePD($data,'D', $old_start_date);	

						if($addResult['error_code'] != 0) { 
						//log_message('debug', 'zzzzzzzz2.2.1');	
							//echo '{"success":false,"msg":"'.$addResult['error_message'].'"}';
							$adjustSuccess = false;
							break;
						}	
						else if($updateResult['affected_rows'] <= 0) {
						//log_message('debug', 'zzzzzzzz2.2.2');	
							//echo '{"success":false,"msg":"'.$updateResult['error_message'].'"}';
							$adjustSuccess = false;
							break;
						}
						else {
							//echo "{'success':true,'msg':'Data successfully saved.'}";	
						}
					}	
					else {   //$pdEntry[0]['end_date'] == $new_end_date	
					//log_message('debug', 'zzzzzzzz2.3');	
						if($mode=='update'){
							$addResult = $this->updatePD($new);
						}
						else{
							$addResult = $this->addPD($new);
						}
						
						$deleteResult = $this->deletePD($data);
						
						if($addResult['error_code'] != 0){ 
							//echo '{"success":false,"msg":"'.$addResult['error_message'].'"}';
							$adjustSuccess = false;
							break;
						}	
						else if($deleteResult['affected_rows'] <= 0) { 
							//echo '{"success":false,"msg":"'.$deleteResult['error_message'].'"}';
							$adjustSuccess = false;
							break;
						}
						else {
							//echo "{'success':true,'msg':'Data successfully saved.'}";	
						}
					}		
				}
				else { 
					//log_message('debug', 'zzzzzzzz3');
					// $pdEntry[0]['start_date'] == $new_start_date 
					//Primary key constraint, start date is PK of t_payroll_deduction table
					/*$checkDuplicate = $this->payrolldeduction_model->checkDuplicateKeyEntry(
																			array('employee_id' => $new['employee_id']
																				  ,'transaction_code' => $new['transaction_code']
																				  ,'start_date' => $new['start_date'])
																		);
					
					if($checkDuplicate['error_code'] == 1){
						$result['error_code'] = 1;
						$result['error_message'] = $checkDuplicate['error_message'];
						echo("{'success':false,'msg':'Employee Id, Transaction Code, Start Date already exists, would you like to update?','error_code':'1'}");	
						break; 
					}
					else {*/
						$addResult = $this->addPD($new);
						if($addResult['error_code'] != 0){ 
							//echo '{"success":false,"msg":"'.$addResult['error_message'].'"}';
							$adjustSuccess = false;
							break;
						}
						//else echo "{'success':true,'msg':'Data successfully saved.'}";	
						//break;
					//}					
				}
			}
			
			if($this->db->trans_status()===TRUE && $adjustSuccess){
				$this->db->trans_commit();
				echo "{'success':true,'msg':'Data successfully saved.'}";	
			}
			else{
				$this->db->trans_rollback();
				if($completeOverlap){
					echo "{'success':false,'msg':'Cannot Save Data. Entry Completely overlapped saved entry.'}";
				}
				else{
					echo "{'success':false,'msg':'Data NOT successfully saved.'}";
				}
			}
		}
		else {
			echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
		}
	}
	
	/**
	 * @desc To retrieve payroll deduction entries of an employee for specific transaction type 
	 * @return array
	 */
	function getEmployeePDEntries($employee_id, $is_mode_update=false)
	{
		$acctgPeriod = date("Ymd", strtotime($this->parameter_model->getParam('ACCPERIOD')));
		$param = array('employee_id' => $employee_id/*, 'transaction_period >=' => $acctgPeriod*/, 'status_flag'=> '1');	
		if($is_mode_update){
			//log_message('debug', 'startdatezzzzz '.$_REQUEST['newpayroll']['start_date']);
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
	 * @desc To get a specific parameter value
	 * @param Parameter id
	 * @return string (parameter value)
	 */
	function getParam($param_id){
		$data = $this->parameter_model->get(array('parameter_id' => $param_id)
			,array('parameter_value')
		);
	
		return $data['list'][0]['parameter_value'];
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
	
}

?>