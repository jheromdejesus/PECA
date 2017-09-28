<?php

class Gl_entries extends Asi_controller {

	function Gl_entries()
	{
		parent::Asi_controller();
		$this->load->model('glentryheader_model');
		$this->load->model('glentrydetail_model');
		$this->load->model('account_model');
		$this->load->model('transactioncode_model');
		$this->load->model('ledger_model');
		$this->load->model('parameter_model');
		$this->load->library('constants');
	}

	function index()
	{

	}

	/**
	 * @desc Retrieve a single gl entry header.
	 */
	function showHdr()
	{
		if (array_key_exists('glHdr',$_REQUEST)){ 
			$this->glentryheader_model->populate($_REQUEST['glHdr']);
			$this->glentryheader_model->setValue('status_flag', '1');
		}
		
		$data = $this->glentryheader_model->get(null,array(
				'gl_code'
				,'gl_description'
				,'particulars'
		));

		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
			));
	}
	
	/**
	 * @desc To retrieve all active GL entry headers.
	 */
	function readHdr() {
		$_REQUEST['filter'] = array('status_flag' => '1');
		$data = $this->glentryheader_model->get_list(
			array_key_exists('filter',$_REQUEST) ? $_REQUEST['filter'] : null
			,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
			,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
			,array('gl_code'
				,'gl_description'
				,'particulars')
			,'gl_description ASC'
		);

		echo json_encode(array(
			'success' => true
			,'data' => $data['list']
			,'total' => $data['count']
			,'query' => $data['query']
			));
	}
	
	##### NRB EDIT START #####
	/**
	 * @desc To retrieve all active GL entries through Detail Field Name.
	 */
	function readDtlFieldName() {
		
		if(!isset($_REQUEST['s_loan_code']) || !isset($_REQUEST['s_field_name'])) {
			$a_data = array('list' => array()
							,'count' => ''
							,'query' => '');
		} else {
			$s_field_name = $_REQUEST['s_field_name'];
			$s_loan_code = $_REQUEST['s_loan_code'];
			$a_data = $this->glentrydetail_model->retrieveGLEntryDetailsFieldName($s_field_name, $s_loan_code);
		}
		
		echo json_encode(array(
			'success' => true
			,'data' => $a_data['list']
			,'total' => $a_data['count']
			,'query' => $a_data['query']
			));
	}
	##### NRB EDIT END #####

	function readDtl(){	
		$params = array(
			'status_flag' => '1'
			,'gl_code' => $_REQUEST['gl_code']
		);
		
		$data = $this->glentrydetail_model->get_list(
			$params
			,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
			,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
			,array('gl_code','account_no','debit_credit','field_name')
			,'gl_code DESC'
		);
		 
		$accountData = $this->account_model->get_list(
			null
			,null
			,null
			,array('account_no','account_name')
		);
		
		$accountArr = array();
		foreach($accountData['list'] as $accountRow){	
			$accountArr[$accountRow['account_no']] = $accountRow['account_name'];
		}
		
		foreach($data['list'] as $row => $value){
			$id = $value['gl_code'] . ':' . $value['account_no'] . ':' . $value['debit_credit'];
			$data['list'][$row]['id'] = $id;
			if(array_key_exists($value['account_no'],$accountArr))
				$data['list'][$row]['account_name'] = $value['account_no']. ' - ' . $accountArr[$value['account_no']];
			else  
				$data['list'][$row]['account_name'] = '';
		}
	
		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
			));
	}
	
	function readDtlMCLS(){
		$period = $this->parameter_model->getParam('ACCPERIOD');
		$period = date("Ymd", strtotime($period));
		$data = $this->ledger_model->retrieveConsolidatedSIEforGLEntry($period);
		$debit_sum = 0;
		$credit_sum = 0;
		
		$dataAccount = $this->account_model->get(array('account_no'=>'3050'), array('account_no', 'CONCAT_WS("-", account_no, account_name) AS account_name'));
		
		foreach($data['list'] as $key => $val){
			$total = $val['beginning_balance']+$val['debit']-$val['credit'];
			if($total<0){
				$data['list'][$key]['amount'] = $total * -1;
				$data['list'][$key]['debit_credit'] = 'D';
				$debit_sum = $debit_sum + (($total)*(-1));
			}
			else{
				$data['list'][$key]['amount'] = $total;
				$data['list'][$key]['debit_credit'] = 'C';
				$credit_sum = $credit_sum + $total;
			}
		}
		if($debit_sum > $credit_sum){
			$thirtyfifty_amount = $debit_sum - $credit_sum;
			$thirtyfifty_debitcredit = 'C';
			$credit_sum = $credit_sum + $thirtyfifty_amount;
		}
		else{
			$thirtyfifty_amount = $credit_sum - $debit_sum;
			$thirtyfifty_debitcredit = 'D';
			$debit_sum = $debit_sum + $thirtyfifty_amount;
		}
		
		if($dataAccount['count'] > 0){
			$data['list'][] = array(
				"account_no" => $dataAccount['list'][0]['account_no']
				,"account_name" => $dataAccount['list'][0]['account_name']
				,"debit_credit" => $thirtyfifty_debitcredit
				,"amount" => $thirtyfifty_amount
			);
		}
		
		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'] + 1 ,
			'query' => $data['query']
			));
	}
	
	/**
	 * @desc Returns all the details of a specific gl code
	 */
	function readClone($gl_code){	
		$params = array(
			'status_flag' => '1'
			,'gl_code' => $gl_code
		);
		
		$data = $this->glentrydetail_model->get_list(
			$params
			,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
			,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
			,array('gl_code','account_no','debit_credit','field_name')
			,'gl_code DESC'
		);
		 
		return $data;
	}

	/**
	 * @desc Delete a single gl entry header and all its details
	 * @param gl_code
	 * @return string (status message)
	 */
	function deleteHdr(){
		log_message('debug', "[START] Controller gl_entries:deleteHdr");
		log_message('debug', "glHdr param exist?:".array_key_exists('glHdr',$_REQUEST));
		
		if (array_key_exists('glHdr',$_REQUEST)) {
			$table_used = $this->checkIfUsed($_REQUEST['glHdr']['gl_code']);
			if ($table_used){
				echo "{'success':false,'msg':'Cannot delete GL Entry \"" . $_REQUEST['glHdr']['gl_code'] . " - " . $_REQUEST['glHdr']['gl_description'] . "\" because it is being used in $table_used.'}";
			} else {
				$this->glentryheader_model->setValue('status_flag', '0');
				$this->db->trans_start();
				$delHdrResult = $this->glentryheader_model->update(array(
					'gl_code' => $_REQUEST['glHdr']['gl_code']
					,'status_flag' => '1'
				));
				
				if($delHdrResult['affected_rows'] <= 0){
					echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
				} else {
					$this->glentrydetail_model->populate($_REQUEST['glHdr']);
					$delDtlResult = $this->glentrydetail_model->delete();
					
					if($delDtlResult['error_code'] == 0)
						echo "{'success':true,'msg':'Data successfully deleted.'}";
					else
						echo '{"success":false,"msg":"'.$delDtlResult['error_message'].'"}';
				}
			}
			
			$this->db->trans_complete();
			
			
		} else
			echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
			
		log_message('debug', "[END] Controller gl_entries:deleteHdr");
	}
	
	function checkIfUsed($gl_code){
		$data = $this->transactioncode_model->get_list(
			array('status_flag' => '1'
					,'gl_code' => $gl_code)
			, '1'
			, null
			, array('gl_code')
			, null
		);
		if(count($data['list']) > 0){
			return "Transaction Code";
		}
		return false;
	}
	

	/**
	 * @desc Delete a single gl entry detail.
	 */
	function deleteDtl() {	
		log_message('debug', "[START] Controller gl_entries:deleteDtl");
		log_message('debug', "data param exist?:".array_key_exists('data',$_REQUEST));
		
		if (array_key_exists('data',$_REQUEST)) {
			$params = array();
			$params = explode(':',json_decode(stripslashes($_REQUEST['data']),true));
			
			$result = $this->glentrydetail_model->delete(array(
				'gl_code' => $params[0]
				,'account_no' => $params[1]
				,'debit_credit' => $params[2]
			));
			
			if($result['affected_rows'] <= 0){
				echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
			} else {
				echo "{'success':true,'msg':'Data successfully deleted.'}";
			}
		} else {
			echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
		}

		log_message('debug', "[END] Controller gl_entries:deleteDtl");
	}

	/**
	 * @desc Update values of a gl entry header.
	 */
	function updateHdr(){
		log_message('debug', "[START] Controller gl_entries:updateHdr");
		log_message('debug', "glHdr param exist?:".array_key_exists('glHdr',$_REQUEST));
		
		if (array_key_exists('glHdr',$_REQUEST)) {
		$this->glentryheader_model->populate($_REQUEST['glHdr']);
		$this->glentryheader_model->setValue('status_flag', '1');
		$result = $this->glentryheader_model->update();
		
			if($result['affected_rows'] <= 0){
				echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
			} else
				echo "{'success':true,'msg':'Data successfully saved.'}";
		} else
			echo "{'success':false,'msg':'Data was NOT successfully saved.'}";

		log_message('debug', "[END] Controller gl_entries:updateHdr");
	}
	
	/**
	 * @desc Insert new data to GL entry header table.
	 */
	function addHdr(){
		log_message('debug', "[START] Controller gl_entries:addHdr");
		log_message('debug', "gl_code param exist?:".array_key_exists('glHdr',$_REQUEST));
		
		if (array_key_exists('glHdr',$_REQUEST)) {
			$this->glentryheader_model->populate($_REQUEST['glHdr']);
			$this->glentryheader_model->setValue('status_flag', '1');
			
			$checkDuplicate = $this->glentryheader_model->checkDuplicateKeyEntry();
	
			if($checkDuplicate['error_code'] == 1){
				$result['error_code'] = 1;
				$result['error_message'] = $checkDuplicate['error_message'];
			}
			else{
				$result = $this->glentryheader_model->insert();
			} 		
					
			if($result['error_code'] == 0){  
			  echo "{'success':true,'msg':'Data successfully saved.'}";
	        } else
			  echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';
		} else
			echo "{'success':false,'msg':'Data was NOT successfully saved.','error_code':'2'}";
			
		log_message('debug', "[END] Controller transaction_code:add");
	}

	/**
	 * @desc Insert new data to GL entry detail table.
	 */
	function addDtl()
	{	
		log_message('debug', "[START] Controller gl_entries:addDtl");
		log_message('debug', "data param exist?:".array_key_exists('data',$_REQUEST));

		if (array_key_exists('data',$_REQUEST)) {
			$params = array();
			$params = json_decode(stripslashes($_REQUEST['data']),true);
			$params['created_by'] = $_REQUEST['user'];
			$this->glentrydetail_model->populate($params);
			$this->glentrydetail_model->setValue('status_flag', '1');
			$result = $this->glentrydetail_model->insert();	
			
			if($result['error_code'] == 0){
			  	echo "{'success':true,'msg':'Data successfully saved.'}";
	        } else {
	        	echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';
	        }
		}else
			echo "{'success':false,'msg':'Data was NOT successfully saved.','error_code':'2'}";
		
		log_message('debug', "[END] Controller gl_entries:addDtl");
	}
	
	/**
	 * @desc Update a specific gl code detail
	 */
	function updateDtl(){
		log_message('debug', "[START] Controller gl_entries:update");
		log_message('debug', "data param exist?:".array_key_exists('data',$_REQUEST));
		
		if (array_key_exists('data',$_REQUEST)) {
			$params = array();
			$params = json_decode(stripslashes($_REQUEST['data']),true);
			unset($params['id']);
			unset($params['account_name']);
			$params['modified_by'] = $_REQUEST['user'];
			$this->glentrydetail_model->populate($params);
			$this->glentrydetail_model->setValue('status_flag', '1');
			$result = $this->glentrydetail_model->update(array(
				'gl_code' => $params['gl_code']
				,'account_no' => $params['account_no']
				,'debit_credit' => $params['debit_credit']
			));
			
			if($result['affected_rows'] <= 0){
				echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
			} else {
				echo "{'success':true,'msg':'Data successfully saved.'}";
			}
		} else {
			echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
		}	
		
		log_message('debug', "[END] Controller transcode:update");
	}

	/**
	 * @param Retrieve all form fields according to transaction group.
	 */
	function readFields()
	{
		if (array_key_exists('transaction_group',$_REQUEST)) {
			$transGroup = $_REQUEST['transaction_group'];
			if(array_key_exists($transGroup, $this->constants->transaction_group)){
				$data = $this->constants->field_group_contents($_REQUEST['transaction_group']);
		
				echo json_encode(array(
				 'data' => $data
				 ));
			}
			else {
				echo json_encode(array(
				 'data' => array()
				 ));
			} 
		}
	}
	
	/**
	 * @param Retrieve all form fields according to transaction group.
	 */
	function glReadFields()
	{
		if (array_key_exists('transaction_group',$_REQUEST)) {
			$transGroup = $_REQUEST['transaction_group'];
			if(array_key_exists($transGroup, $this->constants->transaction_group)||$transGroup=='OT'){
				if($transGroup == 'OT'){
					$data = array(array("0"));
				}
				else{
					$data = $this->constants->field_group_contents($_REQUEST['transaction_group']);
				}
				
				echo json_encode(array(
				 'data' => $data
				 ));
			}
			else {
				echo json_encode(array(
				 'data' => array()
				 ));
			} 
		}
	}
	
	/**
	 * @desc Copies a certain header's details to another header
	 */
	function addClone(){
		log_message('debug', "[START] Controller transaction_code:cloned");
		log_message('debug', "transaction_code param exist?:".array_key_exists('transcode',$_REQUEST));
		
		if (array_key_exists('glHdr',$_REQUEST)) {
			$this->glentryheader_model->populate($_REQUEST['glHdr']);
			$this->glentryheader_model->setValue('status_flag', '1');
			
			$checkDuplicate = $this->glentryheader_model->checkDuplicateKeyEntry();
	
			if($checkDuplicate['error_code'] == 1){
				$result['error_code'] = 1;
				$result['error_message'] = $checkDuplicate['error_message'];
			}
			else{
				$cloneData = $this->readClone($_REQUEST['cloneID']);
				$this->db->trans_start();	
				$this->glentryheader_model->insert();
				if($cloneData['count']!=0){
					foreach($cloneData['list'] as $row => $value){
						$this->glentrydetail_model->populate($value);
						$this->glentrydetail_model->setValue('gl_code', $_REQUEST['glHdr']['gl_code']);
						$this->glentrydetail_model->setValue('status_flag', '1');
						$this->glentrydetail_model->setValue('created_by', $_REQUEST['glHdr']['created_by']);
						$this->glentrydetail_model->insert();
					}
				}
				$this->db->trans_complete();
				if ($this->db->trans_status() === TRUE){
					$result['error_code'] = 0;
				}
				else{
					$result['error_code'] = 25;
					$result['error_message'] = "Clone was NOT successfully saved";
				}
			} 				
			if($result['error_code'] == 0){  
			  echo "{'success':true,'msg':'Data successfully saved.'}";
	        } else
			  echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';
		} else
			echo "{'success':false,'msg':'Data was NOT successfully saved.','error_code':'2'}";
			
		log_message('debug', "[END] Controller transaction_code:cloned");
	}

}

/* End of file company.php */
/* Location: ./CodeIgniter/application/controllers/company.php */