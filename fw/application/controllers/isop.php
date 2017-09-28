<?php 

class Isop extends Asi_Controller{
	
	function Isop(){
		parent::Asi_Controller();
		$this->load->model('isop_model');
		$this->load->model('parameter_model');
		$this->load->model('member_model');
		$this->load->helper('date');
	}
	
	function index(){
		
	}
	
	/**
	 * @desc To retrieve all ISOP Transactions
	 */
	function read() {	
		$current_date = date("Ymd", strtotime($this->parameter_model->getParam('CURRDATE')));
		
//		if(array_key_exists('isop', $_REQUEST)){
//			if(array_key_exists('employee_id', $_REQUEST['isop']) && $_REQUEST['isop']['employee_id']!= "")
//				$param['me.employee_id LIKE'] =  $_REQUEST['isop']['employee_id']."%";
//			if(array_key_exists('first_name', $_REQUEST['isop']) && $_REQUEST['isop']['first_name']!= "")
//				$param['me.first_name LIKE'] =  $_REQUEST['isop']['first_name']."%";
//			if(array_key_exists('last_name', $_REQUEST['isop']) && $_REQUEST['isop']['last_name']!= "")
//				$param['me.last_name LIKE'] =  $_REQUEST['isop']['last_name']."%";
//		}

		if(array_key_exists('employee_id', $_REQUEST) && $_REQUEST['employee_id']!= ""){
				$param['me.employee_id LIKE'] =  $_REQUEST['employee_id']."%";
		}
		else{
			if(array_key_exists('first_name', $_REQUEST) && $_REQUEST['first_name']!= "")
				$param['me.first_name LIKE'] =  $_REQUEST['first_name']."%";
			if(array_key_exists('last_name', $_REQUEST) && $_REQUEST['last_name']!= "")
				$param['me.last_name LIKE'] =  $_REQUEST['last_name']."%";
		}
		
		//$param['ti.end_date >'] = $current_date;
		$param['ti.status_flag'] = 1;
		$data = $this->isop_model->getListIsop(
			$param
			,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
			,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
			,array('ti.transaction_no AS transaction_no' 
				,'ti.employee_id AS employee_id'									
				,'me.last_name AS last_name'									
				,'me.first_name AS first_name'									
				,'ti.start_date AS start_date'									
				,'ti.end_date AS end_date'									
				,'ti.amount AS amount')
			,'me.last_name DESC, me.first_name DESC'	
			);
		
		foreach($data['list'] as $key => $val){
			$data['list'][$key]['start_date'] = $this->formatDateYYYYMMDDToMMDDYYYY($val['start_date']);
			$data['list'][$key]['end_date'] = $this->formatDateYYYYMMDDToMMDDYYYY($val['end_date']);
		}

		echo json_encode(array(
			'success' => true
			,'data' => $data['list']
			,'total' => $data['count']
			,'query' => $data['query']
			));
	}
	
	function formatDateYYYYMMDDToMMDDYYYY($date){
		return substr($date, 4, 2) .  substr($date, 6, 2) .  substr($date, 0, 4);
	}
	
	/**
	 * @desc To retrieve the information of the selected transaction
	 */
	function show(){
		if (array_key_exists('isop',$_REQUEST)){ 
			$this->isop_model->setValue('ti.transaction_no', $_REQUEST['isop']['transaction_no']);
			$this->isop_model->setValue('ti.status_flag', '1');
		}
		
		$data = $this->isop_model->getIsop(null,array(
				'ti.transaction_no AS transaction_no'	
				,'ti.employee_id AS employee_id'									
				,'me.last_name AS last_name'									
				,'me.first_name AS first_name'									
				,'ti.start_date AS start_date'									
				,'ti.end_date AS end_date'									
				,'ti.amount AS amount'
		));
		
		foreach($data['list'] as $key => $val){
			$data['list'][$key]['start_date'] = $this->formatDateYYYYMMDDToMMDDYYYY($val['start_date']);
			$data['list'][$key]['end_date'] = $this->formatDateYYYYMMDDToMMDDYYYY($val['end_date']);
		}

		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
			));
	}
	
	/**
	 * @desc Checks if the start date is not prior to the accounting period and is the first day of the month
	 */
	function checkIfFirstDay($start_date){
		if(date("d", strtotime($start_date))!= '01')
			return 1;
		else 
			return 0;	
	}
	
	/**
	 * @desc Checks if date is last day of the month
	 */
	function checkIfLastDay($end_date){
		$endDateMonth = substr($end_date, 4,2);
		$endDateYear = substr($end_date, 0,4);
		$endDateDay = substr($end_date, 6,2);
		
		if($endDateDay != days_in_month($endDateMonth,$endDateYear))
			return 1;
		else 
			return 0;
	}
	
	function formatDateMMsDDsYYYYToYYYYMMDD($date){
		return substr($date, 6, 4) .  substr($date, 0, 2) .  substr($date, 3, 2);
	}
	
	/**
	 * @desc Inserts new ISOP transaction to t_isop table
	 */
	function add(){
		
		log_message('debug', "[START] Controller isop:add");
		log_message('debug', "isop param exist?:".array_key_exists('isop',$_REQUEST));
		
		if (array_key_exists('isop',$_REQUEST)) {
			$_REQUEST['isop']['start_date'] = $start_date = $this->formatDateMMsDDsYYYYToYYYYMMDD($_REQUEST['isop']['start_date']);
			$_REQUEST['isop']['end_date'] = $end_date = $this->formatDateMMsDDsYYYYToYYYYMMDD($_REQUEST['isop']['end_date']);
			$employee_id = $_REQUEST['isop']['employee_id']; 
			
			$accounting_period = $this->parameter_model->getParam('ACCPERIOD');
			$accounting_period = date("Ymd", strtotime($accounting_period));
			//$isopEntry = $this->getEmployeeIsopEntries($employee_id);
			$retrieveConflictResult = $this->isop_model->retrieveConflict($start_date, $end_date, $employee_id);
			
			$_REQUEST['isop']['transaction_no'] = $this->parameter_model->getParam('LASTTRANNO') + 1;
			$_REQUEST['isop']['transaction_period'] = $this->parameter_model->getParam('ACCPERIOD');
			$_REQUEST['isop']['transaction_type'] = 'A';
			
			if(!$this->member_model->employeeExists($employee_id)){
				echo("{'success':false,'msg':'Employee does not exist','error_code':'22'}");
			}
			else if($this->member_model->employeeIsInactive($employee_id)){
				echo("{'success':false,'msg':'Employee is inactive','error_code':'55'}");
			}
			else if($start_date > $end_date){
				echo("{'success':false,'msg':'Start date is greater than the end date','error_code':'31'}");
			}
			else if($this->checkIfFirstDay($start_date)){
				echo("{'success':false,'msg':'Isop start date should be the first day of the month','error_code':'35'}");	
			}
			else if($start_date < $accounting_period){
				echo("{'success':false,'msg':'Isop start date cannot be prior to the accounting period','error_code':'17'}");	
			}
			else if($this->checkIfLastDay($end_date)){
				echo("{'success':false,'msg':'Isop end date should be the last day of the month','error_code':'36'}");	
			}
			else if($end_date < $accounting_period){
				echo("{'success':false,'msg':'Isop end date cannot be prior to the accounting period','error_code':'18'}");	
			}
			else if($this->checkIfExact($employee_id, $start_date,$end_date)){
				$this->isop_model->setValue('amount', $_REQUEST['isop']['amount']);
				$this->isop_model->setValue('modified_by', $_REQUEST['isop']['created_by']);
				$this->isop_model->update(array('employee_id' => $employee_id, 'start_date' => $start_date, 'end_date' => $end_date));
				echo("{'success':true,'msg':'Data successfully saved.'}");
			}
			else if($this->checkCompleteOverlap($employee_id, $start_date,$end_date)){
				echo("{'success':false,'msg':'Cannot Save Data. Entry Completely overlapped saved entry.','error_code':'21'}");
			}
			else if($retrieveConflictResult['count']>0){
				echo("{'success':false,'msg':'Isop date conflict, would you like to adjust?','error_code':'19'}");	
			}
			else{
				unset($_REQUEST['isop']['first_name']);
				unset($_REQUEST['isop']['last_name']);
				unset($_REQUEST['isop']['old_start_date']);
				unset($_REQUEST['isop']['old_end_date']);
				$this->isop_model->populate($_REQUEST['isop']);
				$this->isop_model->setValue('status_flag', '1');
				
				$checkDuplicate = $this->isop_model->checkDuplicateKeyEntry();
		
				if($checkDuplicate['error_code'] == 1){
					$result['error_code'] = 1;
					$result['error_message'] = $checkDuplicate['error_message'];
				}
				else{
					$result = $this->isop_model->insert();
					$result1 = $this->parameter_model->updateValue('LASTTRANNO', $_REQUEST['isop']['transaction_no'], $_REQUEST['isop']['created_by']);	
				} 		
						
				if($result['error_code'] == 0){  
				  echo "{'success':true,'msg':'Data successfully saved.','transaction_no':'" . $_REQUEST['isop']['transaction_no'] . "'}";
		        } 
				else if($result['error_code'] > 0){
					echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';	
				}
				else{
					echo '{"success":false,"msg":"'.$result1['error_message'].'","error_code":"'.$result1['error_code'].'"}';	
				}
			}	  
		} else
			echo "{'success':false,'msg':'Data was NOT successfully saved.','error_code':'2'}";
			
		log_message('debug', "[END] Controller isop:add");
	}
	
	/**
	 * @desc Checks overlap records
	 */
	function checkIsopConflict($old, $start_date, $end_date, $function)
	{
		$count = 0;
		
		foreach ($old AS $data) 
		{
			if(($data['start_date'] == $start_date)
			||($data['start_date'] < $start_date &&$start_date <= $data['end_date']) 
			||($data['start_date'] <=$end_date && $data['end_date'] >=$end_date)
			){
				$count++;
			}
		}
		
		if ($count > 0) {
			return 1;
		}
		
		else {
			return 0;	
		}	
		
	}
	
	/**
	 * @desc To retrieve payroll deduction entries of an employee for specific transaction type 
	 * @return array
	 */
	function getEmployeeIsopEntries($employee_id, $tran_no=null)
	{
		$currDate = date("Ymd", strtotime($this->parameter_model->getParam('CURRDATE')));
		$param = array('employee_id' => $employee_id, 'end_date >' => $currDate, 'status_flag' => '1');	
		if($tran_no){
			$param['transaction_no !='] = $tran_no;
		}
		
		$data = $this->isop_model->get_list(
			$param,	
			null,
			null,
			array(
				'transaction_no'
				,'employee_id'
				,'start_date'				
				,'end_date'
				,'amount'
				,'created_by')
		);
		return $data;	
	}
	
	/**
	 * @desc Updates a single isop transaction
	 */
	function update(){
		$_REQUEST['isop']['created_by'] = 'peca';
		log_message('debug', "[START] Controller isop:update");
		log_message('debug', "isop param exist?:".array_key_exists('isop',$_REQUEST));
			
		if (array_key_exists('isop',$_REQUEST)) {
			$transaction_no = $_REQUEST['isop']['transaction_no'];
			$_REQUEST['isop']['start_date'] = $start_date = $this->formatDateMMsDDsYYYYToYYYYMMDD($_REQUEST['isop']['start_date']);
			$_REQUEST['isop']['end_date'] = $end_date = $this->formatDateMMsDDsYYYYToYYYYMMDD($_REQUEST['isop']['end_date']); 
			$old_start_date = date("Ymd", strtotime($_REQUEST['isop']['old_start_date']));
			$old_end_date = date("Ymd", strtotime($_REQUEST['isop']['old_end_date']));
			$employee_id = $_REQUEST['isop']['employee_id'];
			
			$accounting_period = $this->parameter_model->getParam('ACCPERIOD');
			$accounting_period = date("Ymd", strtotime($accounting_period));
			//$isopEntry = $this->getEmployeeIsopEntries($employee_id, $transaction_no);
			$retrieveConflictResult = $this->isop_model->retrieveConflict($start_date, $end_date, $employee_id, $transaction_no);
			
			if(!$this->member_model->employeeExists($employee_id)){
				echo("{'success':false,'msg':'Employee does not exist','error_code':'22'}");
			}
			else if($this->member_model->employeeIsInactive($employee_id)){
				echo("{'success':false,'msg':'Employee is inactive','error_code':'55'}");
			}
			else if ($start_date > $end_date){
				echo("{'success':false,'msg':'Start date is greater than the end date','error_code':'31'}");
			}
			else if($this->checkIfFirstDay($start_date)){
				echo("{'success':false,'msg':'Isop start date should be the first day of the month','error_code':'35'}");	
			}
			else if($start_date < $accounting_period){
				echo("{'success':false,'msg':'Isop start date cannot be prior to the accounting period','error_code':'17'}");	
			}
			else if($this->checkIfLastDay($end_date)){
				echo("{'success':false,'msg':'Isop end date should be the last day of the month','error_code':'36'}");	
			}
			else if($end_date < $accounting_period){
				echo("{'success':false,'msg':'Isop end date cannot be prior to the accounting period','error_code':'18'}");	
			}
			else if($this->checkIfExact($employee_id, $start_date,$end_date)){
				$this->isop_model->setValue('status_flag', '0');
				$this->isop_model->update(array('transaction_no' => $transaction_no));
				
				$this->isop_model->populate(array());
				$this->isop_model->setValue('amount', $_REQUEST['isop']['amount']);
				$this->isop_model->setValue('modified_by', $_REQUEST['isop']['modified_by']);
				$this->isop_model->update(array('employee_id' => $employee_id, 'start_date' => $start_date, 'end_date' => $end_date));
				echo("{'success':true,'msg':'Data successfully saved.'}");
			}
			else if($this->checkCompleteOverlap($employee_id,$start_date,$end_date)){
				echo("{'success':false,'msg':'Cannot Save Data. Entry Completely overlapped saved entry.','error_code':'21'}");
			}
			else if($retrieveConflictResult['count']>0){
				echo "{'success':false,'msg':'Isop date conflict, would you like to adjust?','error_code':'43'}";	
			}
			else{
				$_REQUEST['isop']['transaction_period'] = $this->parameter_model->getParam('ACCPERIOD');
				$_REQUEST['isop']['transaction_type'] = 'A';
				unset($_REQUEST['isop']['first_name']);
				unset($_REQUEST['isop']['last_name']);
				unset($_REQUEST['isop']['old_start_date']);
				unset($_REQUEST['isop']['old_end_date']);
				$this->isop_model->populate($_REQUEST['isop']);
				$this->isop_model->setValue('status_flag', '1');
			
				$result = $this->isop_model->update();
				
				if($result['error_code'] == 0){  
				  echo "{'success':true,'msg':'Data successfully saved.'}";
		        } else
				  echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';	
		
			}	
		} else
			echo "{'success':false,'msg':'Data was NOT successfully saved.'}";

		log_message('debug', "[END] Controller isop:update");
	}
	
	/**
	 * @desc Delete a single isop transaction.
	 * @param transaction_no
	 * @return string (status message)
	 */
	function delete(){
		log_message('debug', "[START] Controller isop:delete");
		log_message('debug', "isop param exist?:".array_key_exists('isop',$_REQUEST));
		
		if (array_key_exists('isop',$_REQUEST)) {
			$this->isop_model->setValue('transaction_no', $_REQUEST['isop']['transaction_no']);
			$this->isop_model->setValue('status_flag', '0');
			
			$result = $this->isop_model->update();
			
			if($result['affected_rows'] == 0){
				echo '{"success":false,"msg":"'.$result['error_message'].'"}';
			} else {
				echo "{'success':true,'msg':'Data successfully deleted.'}";
			}
		} else
			echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
			
		log_message('debug', "[END] Controller isop:delete");
	}
	
	/**
	 * @desc Deletes a single isop transaction, called by only by adjustIsop function
	 */
	function deleteIsop($tran_no){
		$this->isop_model->setValue('transaction_no', $tran_no);
		$result = $this->isop_model->delete();
	}
	
	/**
	 * @desc Adjusts ISOP entry when there are overlapping dates
	 */
	 
	function adjustIsop($method){
		$start = $this->formatDateMMsDDsYYYYToYYYYMMDD($_REQUEST['isop']['start_date']);
		$end = $this->formatDateMMsDDsYYYYToYYYYMMDD($_REQUEST['isop']['end_date']);
		$amount = $_REQUEST['isop']['amount'];
		$employee_id = $_REQUEST['isop']['employee_id'];
		$transaction_period = date("Ymd", strtotime($this->parameter_model->getParam('ACCPERIOD')));
		
		$this->db->trans_start();
		//retrieve conflicting pd delete afterwards
		$result = $this->isop_model->retrieveConflict($start, $end, $employee_id);
		$arr_conflicts = $result['list'];
		$result = $this->isop_model->deleteConflict($start, $end, $employee_id);
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
					$prev_date = $this->firstDayNextMonth($prev_key);//date('Ymd',strtotime('+1 day',strtotime($prev_key)));
					$arr_02[$prev_date] = null;
					$arr_02[$key] = $value;
					$bol_start = true;
				}
			} else{
				if ($day == "01"){
					$prev_date = $this->lastDayLastMonth($key);//('Ymd',strtotime('-1 day',strtotime($key)));
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
			
			$transaction_no = $this->parameter_model->incParam('LASTTRANNO');
			$this->isop_model->insertIsop($transaction_no, $new_start_date, $new_end_date, $new_amount, $delimited, $employee_id, $transaction_period, $_REQUEST['isop']['created_by']);
		}	
		if($this->db->trans_status()===TRUE){
			$this->db->trans_commit();
			echo "{'success':true,'msg':'Data successfully saved.'}";	
		}
		else{
			$this->db->trans_rollback();
			echo "{'success':false,'msg':'Data NOT successfully saved.'}";	
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
	
	function adjustIsop2($method)
	{
		/*$_REQUEST['isop'] = array(
			"employee_id" => '01517353'
			,"start_date" => '05/01/2010'
			,"end_date" => '07/31/2010'
			,"amount" => '1111'
			,"created_by" => 'peca'
		);*/
		
		$old = $this->getEmployeeIsopEntries($_REQUEST['isop']['employee_id']);
		$_REQUEST['isop']['start_date'] = date("Ymd", strtotime($_REQUEST['isop']['start_date']));
		$_REQUEST['isop']['end_date'] = date("Ymd", strtotime($_REQUEST['isop']['end_date']));
		unset($_REQUEST['isop']['old_start_date']);
		unset($_REQUEST['isop']['old_end_date']);
		$new = $_REQUEST['isop'];
		$this->db->trans_start();
		foreach ($old['list'] as $data) 
		{	
			if($data['start_date'] == $new['start_date']){
				if($new['end_date'] < $data['end_date']){
					 if($method== 'add')
					 	$this->addIsop($new,'A');
					 else
					 	$this->updateIsop($new,'A');
					 $data['start_date'] = $this->getNextDay($new['end_date']);
					 //$data['amount'] = $new['amount'];
					 $this->updateIsop($data,'D');
				}
				else if($new['end_date'] > $data['end_date']){
					//$data['amount'] = $new['amount'];
					$this->updateIsop($data,'A');
					if($method== 'add')
					 	$this->addIsop($new,'A');
					else
					 	$this->updateIsop($new,'A');
				}
				else{ //both dates are equal
					//$data['amount'] = $new['amount'];
					$this->updateIsop($data,'A');
					if($method == 'update')
						$this->deleteIsop($new['transaction_no']);
				}
			}
			
			else if($data['start_date'] < $new['start_date'] && $new['start_date'] <= $data['end_date']) {
				$data_end_date = $data['end_date'];
				$data['end_date'] = $this->getPreviousDay($new['start_date']);
				//$data['amount'] = $new['amount'];
				$this->updateIsop($data,'D');
				$data['end_date'] = $data_end_date;
				if($method== 'add')
					$this->addIsop($new,'A');
				else
				 	$this->updateIsop($new,'A');
					
				if($data['end_date'] > $new['end_date']){
					$data['start_date'] = $this->getNextDay($new['end_date']);
					$this->addIsop($data, 'D');
				}
			}
			else if($data['start_date'] <= $new['end_date'] && $data['end_date'] >= $new['end_date']){
				$new_end_date = $new['end_date'];
				$data_end_date = $data['end_date'];
				
				if($method== 'add')
					$this->addIsop($new,'A');
				 else
				 	$this->updateIsop($new,'A');
				 
				if ($data_end_date  > $new_end_date) {	
				
					$data['start_date'] = $this->getNextDay($new['end_date']);
					//$data['amount'] = $new['amount'];
					$this->updateIsop($data,'D');				
				}	
				else {   //existing end date == new end date	
					//$data['amount'] = $new['amount'];
					$this->updateIsop($data,'A');				
				}		
			}
		}
		/*if($tran_no!=""){ //new isop has blank tran_no, while update has otherwise
			$this->isop->delete(array(
				"transaction_no" => $tran_no
			));
		}*/
		$this->db->trans_complete();
		if($this->db->trans_status()===FALSE)
			echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
		else{
			echo "{'success':true,'msg':'Data successfully saved.'}";
		}		
	}
	
/**
	 * @desc Updates information on payroll deduction record
	 */
	function updateIsop($data, $trantype)
	{
		$this->isop_model->populate($data);
		$this->isop_model->setValue('status_flag', '1');
		$this->isop_model->setValue('transaction_type', $trantype);
		$this->isop_model->update();							 	
	}
	
	/**
	 * @desc Inserts new record to t_payroll_deduction
	 */
	function addIsop($data, $trantype)
	{
		$acctgPeriod = date("Ymd", strtotime($this->parameter_model->getParam('ACCPERIOD')));
		$this->isop_model->populate($data);
		$this->isop_model->setValue('transaction_no', $this->parameter_model->incParam('LASTTRANNO'));
		$this->isop_model->setValue('status_flag', '1');
		$this->isop_model->setValue('transaction_type', $trantype);
		$this->isop_model->setValue('transaction_period', $acctgPeriod);
		$this->isop_model->insert();	
	}
	
	/**
	 * @desc Checks if start and end date completely overlaps other saved start and end dates of an employee
	 */
	function checkCompleteOverlap($employee_id, $start_date,$end_date){
		
		$data = $this->isop_model->get(
			"employee_id = '$employee_id' AND start_date > '$start_date' AND end_date < '$end_date' AND status_flag = 1"
			,"COUNT(*) AS count"
		);
		
		if($data['list'][0]['count']==0)
			return 0;
		else 
			return 1;
	}
	
	function checkIfExact($employee_id, $start_date,$end_date){
		
		$data = $this->isop_model->get(
			"employee_id = '$employee_id' AND start_date = '$start_date' AND end_date = '$end_date' AND status_flag=1"
			,"COUNT(*) AS count"
		);
		
		if($data['list'][0]['count']==0)
			return 0;
		else 
			return 1;
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
	
}
?>