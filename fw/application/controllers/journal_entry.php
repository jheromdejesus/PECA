<?php

class Journal_entry extends Asi_controller {

	function Journal_entry()
	{
		parent::Asi_controller();
		$this->load->model('journalheader_model');
		$this->load->model('journaldetail_model');
		$this->load->model('glentryheader_model');
		$this->load->model('glentrydetail_model');
		$this->load->model('account_model');
		$this->load->model('parameter_model');
		$this->load->model('supplier_model');
		$this->load->model('mcjournaldetail_model');
		$this->load->model('mcjournalheader_model');
		$this->load->library('constants');
	}

	function index(){

	}
	

	/**
	 * @desc To retrieve all header data of journal entry
	 */
	function show(){
		//$_REQUEST['journalHdr']['journal_no'] = '108'; 
		if (array_key_exists('journalHdr',$_REQUEST)){ 
			$this->journalheader_model->setValue('journal_no', $_REQUEST['journalHdr']['journal_no']);
			$this->journalheader_model->setValue('status_flag', '3');
		}
		
		$data = $this->journalheader_model->get(null,array(
				'journal_no' 									
				,'accounting_period'									
				,'transaction_code' 									
				,'transaction_date' 									
				,'particulars'  											
				,'reference'									
				,'source'											
				,'document_no' 											
				,'document_date' 											
				,'remarks'
				,'supplier_id' 											
		));
		
		foreach($data['list'] as $key => $val){
			if($val['transaction_date'] != '')
				$data['list'][$key]['transaction_date'] = date("mdY", strtotime($val['transaction_date']));
			if($val['document_date'] != '')
				$data['list'][$key]['document_date'] = date("mdY", strtotime($val['document_date']));
		}

		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
			));
	}
	
	/**
	 * @desc To retrieve list of journal entries
	 */
	function readHdr() {
//		if(isset($_REQUEST['journal']['journal_no']) && $_REQUEST['journal']['journal_no']!=""){
//			$param['journal_no LIKE'] = $_REQUEST['journal']['journal_no']."%";
//		}

		if(array_key_exists('journal_no', $_REQUEST) && $_REQUEST['journal_no']!=""){
			$param['journal_no LIKE'] = $_REQUEST['journal_no']."%";
		}

		$param['source'] = 'U';
		$param['status_flag'] = '3';

		$data = $this->journalheader_model->get_list(
			$param
			,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
			,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
			,array('journal_no'					
				,'particulars'				
				,'transaction_date')
		);
		
		foreach($data['list'] as $key => $val){
			if($val['transaction_date'] != '')
				$data['list'][$key]['transaction_date'] = date("mdY", strtotime($val['transaction_date']));
		}

		echo json_encode(array(
			'success' => true
			,'data' => $data['list']
			,'total' => $data['count']
			,'query' => $data['query']
			));
			
		
	}

	/**
	 * @desc Displays all the details of a journal entry
	 */
	function readDtl(){	
		//$_REQUEST['journal_no'] = '108';
		$params = array(
			'journal_no' => $_REQUEST['journal_no']
		);
		
		$data = $this->journaldetail_model->get_list(
			$params
			,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
			,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
			,array('journal_no', 'account_no','debit_credit','amount')
			,'account_no ASC'
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
			if(array_key_exists($value['account_no'],$accountArr))
				$data['list'][$row]['account_name'] = $value['account_no']." - ".$accountArr[$value['account_no']];
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

	/**
	 * @desc Delete a journal entry header and all its details
	 */
	function deleteHdr(){
		//$_REQUEST['journalHdr']['journal_no'] = '0000210667';
		
		log_message('debug', "[START] Controller journal_entry:deleteHdr");
		log_message('debug', "journalHdr param exist?:".array_key_exists('journalHdr',$_REQUEST));
		
		if (array_key_exists('journalHdr',$_REQUEST)) {
			$this->journalheader_model->setValue('status_flag', '0');
			
			$this->db->trans_start();
			
			if($this->mcjournalheader_model->insertJournal(
							$_REQUEST['journalHdr']['journal_no']
							,$_REQUEST['journal']['modified_by']
				)) {
				if($this->mcjournaldetail_model->insertJournal(
							$_REQUEST['journalHdr']['journal_no']
							,$_REQUEST['journal']['modified_by']
				)) {
					$delHdrResult = $this->journalheader_model->delete(array(
						'journal_no' => $_REQUEST['journalHdr']['journal_no']
					));
					
					if($delHdrResult['affected_rows'] <= 0){
						echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
					} else {
						$delDtlResult = $this->journaldetail_model->delete(array(
							'journal_no' => $_REQUEST['journalHdr']['journal_no']
						));
						
						if($delDtlResult['error_code'] == 0)
							echo "{'success':true,'msg':'Data successfully deleted.'}";
						else
							echo '{"success":false,"msg":"'.$delDtlResult['error_message'].'"}';
					}
				} else
					echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
			} else
				echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
			
			$this->db->trans_complete();
			
			
		} else
			echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
			
		log_message('debug', "[END] Controller journal_entry:deleteHdr");
	}

	/**
	 * @desc Delete a journal detail.
	 */
	function deleteDtl() {	
		/*$_REQUEST['data'] = '{
			"journal_no":"0000210667"
			,"account_no":"1019"
			,"debit_credit":"C"
			,"amount":"10000"
		}';	*/
		
		log_message('debug', "[START] Controller journal_entry:deleteDtl");
		log_message('debug', "data param exist?:".array_key_exists('data',$_REQUEST));
		
		if (array_key_exists('data',$_REQUEST)) {
			$params = array();
			$params = json_decode(stripslashes($_REQUEST['data']),true);
			$result = $this->journaldetail_model->delete(array(
				'journal_no' => $params['journal_no']
				,'account_no' => $params['account_no']
				,'debit_credit' => $params['debit_credit']
			));
			
			if($result['affected_rows'] <= 0){
				echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
			} else {
				echo "{'success':true,'msg':'Data successfully deleted.'}";
			}
		} else {
			echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
		}

		log_message('debug', "[END] Controller journal_entry:deleteDtl");
	}

	/**
	 * @desc Updates journal entry header data
	 */
	function updateHdr(){	
		log_message('debug', "[START] Controller journal_entry:updateHdr");
		log_message('debug', "journalHdr param exist?:".array_key_exists('journalHdr',$_REQUEST));
		
		if (array_key_exists('journalHdr',$_REQUEST)) {
			$this->journalheader_model->populate($_REQUEST['journalHdr']);
			$transaction_date = $_REQUEST['journalHdr']['transaction_date'];
			$accounting_period = date("Ym", strtotime($transaction_date))."01"; //accounting period must be based on transaction date
			$this->journalheader_model->setValue('accounting_period', $accounting_period);
			$this->journalheader_model->setValue('status_flag', '3');
			$this->journalheader_model->setValue('modified_by', $_REQUEST['user']);
			
			if($_REQUEST['journalHdr']['supplier_id']!="" && !$this->supplier_model->supplierIdExists($_REQUEST['journalHdr']['supplier_id'])){
				echo "{'success':false,'msg':'Supplier does not exist','error_code':'50'}";			
			}
			else if(!$this->accountNumberExists()){
				echo "{'success':false,'msg':'Account number does not exist','error_code':'51'}";	
			}
			else if($this->checkTotalAmounts()){
				echo "{'success':false,'msg':'Debit credit total amounts are not equal','error_code':'30'}";
			}
			else{		
				$result = $this->journalheader_model->update();
				if($result['affected_rows']>0){
					 $deleteResult = $this->journaldetail_model->delete(array(
							'journal_no' => $_REQUEST['journalHdr']['journal_no']
						));	
						
					if($deleteResult['error_code']!=0)
						echo "{'success':false,'msg':'Data was NOT successfully saved.','error_code':'2'}";
					else
						echo "{'success':true,'msg':'Data successfully saved.'}";
				}
				else
				  echo "{'success':false,'msg':'Data was NOT successfully saved.','error_code':'2'}";	
			}	
		} else
			echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
		
		log_message('debug', "[END] Controller journal_entry:updateHdr");
	}
	
	function checkTotalAmounts(){
		
		$creditData = $this->journaldetail_model->get(array(
			'journal_no' => $_REQUEST['journalHdr']['journal_no']
			,'debit_credit' => 'C'
		), array('SUM(amount) as credit_sum'));
		
		$credit_total = $creditData['list'][0]['credit_sum'];
		
		$debitData = $this->journaldetail_model->get(array(
			'journal_no' => $_REQUEST['journalHdr']['journal_no']
			,'debit_credit' => 'D'
		), array('SUM(amount) as debit_sum'));
		
		$debit_total = $debitData['list'][0]['debit_sum'];
		
		if ($credit_total != $debit_total) 
			return 1;
		else
			return 0;
	}	
	
	/**
	 * @desc Add new journal header
	 */
	function addHdr(){
		
		log_message('debug', "[START] Controller journal_entry:addHdr");
		log_message('debug', "journalHdr param exist?:".array_key_exists('journalHdr',$_REQUEST));
		
		if (array_key_exists('journalHdr',$_REQUEST)) {
			
			$journal_no = $this->parameter_model->getParam('JOURNALNO') + 1;
			$padded_journal_no = str_pad($journal_no, 10, "0", STR_PAD_LEFT);
			$transaction_date = $_REQUEST['journalHdr']['transaction_date'];
			$transaction_code = $_REQUEST['journalHdr']['transaction_code'];
			$accounting_period = date("Ym", strtotime($transaction_date))."01"; //accounting period must be based on transaction date
			$this->journalheader_model->populate($_REQUEST['journalHdr']);
			if($transaction_code=='MCLS'){ //save transaction as MCLS if closing else make blank
				$this->journalheader_model->setValue('transaction_code', 'MCLS');
			}
			else{
				$this->journalheader_model->setValue('transaction_code', '');
			}
			$this->journalheader_model->setValue('source', 'U');
			$this->journalheader_model->setValue('created_by', $_REQUEST['user']);
			$this->journalheader_model->setValue('journal_no', $padded_journal_no);
			/* new implementation: accounting period must be based on transaction date
			$this->journalheader_model->setValue('accounting_period', $this->parameter_model->getParam('ACCPERIOD'));
			*/
			$this->journalheader_model->setValue('accounting_period', $accounting_period);
			$this->journalheader_model->setValue('status_flag', '3');
			
			$checkDuplicate = $this->journalheader_model->checkDuplicateKeyEntry();
	
			if($_REQUEST['journalHdr']['supplier_id']!="" && !$this->supplier_model->supplierIdExists($_REQUEST['journalHdr']['supplier_id'])){
				$result['error_code'] = 50;
				$result['error_message'] = "Supplier does not exist";
			}
			else if(!$this->accountNumberExists()){
				$result['error_code'] = 51;
				$result['error_message'] = "Account number does not exist";
			}
			else{
				if($checkDuplicate['error_code'] == 1){
					$result['error_code'] = 1;
					$result['error_message'] = $checkDuplicate['error_message'];
				}
				else{	
					$result = $this->journalheader_model->insert();
					
				} 	
			}
			
			if($result['error_code'] == 0){
			  $this->parameter_model->updateValue('JOURNALNO', $journal_no, $_REQUEST['user']);
			  echo "{'success':true,'msg':'Data successfully saved.','journal_no':'$padded_journal_no'}";
	        } else
			  echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';
		} else
			echo "{'success':false,'msg':'Data was NOT successfully saved.','error_code':'2'}";
			
		log_message('debug', "[END] Controller journal_entry:addHdr");
	}
	
	/**
	@desc Checks if all account nos. given still exist
	*/
	function accountNumberExists(){
		$data = json_decode(stripslashes($_REQUEST['data']),true);
	
		foreach($data as $key => $val){	
			if(!$this->account_model->accountNoExists($val['account_no'])){
				return 0;
			}
		}

		return 1;
	}
	
	/**
	 * @desc Adds a details to a specific journal entry.
	 */
	function addDtl(){
		
		log_message('debug', "[START] Controller journal_entry:addDtl");
		log_message('debug', "data param exist?:".array_key_exists('data',$_REQUEST));

		if (array_key_exists('data',$_REQUEST)) {
		    $this->db->trans_start();

			$data = array();
			if(substr($_REQUEST['data'],0,1)=='['){
				$data = json_decode(stripslashes($_REQUEST['data']),true);
				foreach($data as $key => $val){
					unset($val['account_name']);
					$data[$key] = implode(",", $val); 
				}
				$data = array_unique($data);
				
				foreach($data as $key => $val){
					$data[$key] = explode(",", $val); 
				}
			
				foreach($data as $key => $val){	
					$params['created_by'] = $_REQUEST['user'];
					$params['account_no'] = $val[0];
					$params['journal_no'] = $val[1];
					$params['amount'] = $val[2];
					$params['debit_credit'] = $val[3];
					$this->journaldetail_model->populate($params);
					$this->journaldetail_model->setValue('status_flag', '3');
					$result = $this->journaldetail_model->insert();
				}
			}	
			else{
				$data = json_decode(stripslashes($_REQUEST['data']),true);
				$params['created_by'] = $_REQUEST['user'];
				$params['journal_no'] = $data['journal_no'];
				$params['account_no'] = $data['account_no'];
				$params['debit_credit'] = $data['debit_credit'];
				$params['amount'] = $data['amount'];
				$this->journaldetail_model->populate($params);
				$this->journaldetail_model->setValue('status_flag', '1');
				$this->journaldetail_model->insert();
			}
			
			$this->db->trans_complete();
			if($this->db->trans_status() === TRUE){  
			  echo "{'success':true,'msg':'Data successfully saved.'}";
	        } else
			  echo "{'success':false,'msg':'Data was NOT successfully saved.','error_code':'2'}";	
		} else
			echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
				
		log_message('debug', "[END] Controller journal_entry:addDtl");
	}
	
	/**
	 * @desc Update a specific journal detail
	 */
	function updateDtl(){
		
		log_message('debug', "[START] Controller journal_entry:updateDtl");
		log_message('debug', "data param exist?:".array_key_exists('data',$_REQUEST));
		
		if (array_key_exists('data',$_REQUEST)) {
			$params = array();
			$params = json_decode(stripslashes($_REQUEST['data']),true);
			$this->journaldetail_model->setValue('amount', $params['amount']);
			$this->journaldetail_model->setValue('modified_by', $_REQUEST['user']);
			$this->journaldetail_model->setValue('status_flag', '1');
			$result = $this->journaldetail_model->update(array(
				'journal_no' => $params['journal_no']
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
		
		log_message('debug', "[END] Controller journal_entry:updateDtl");
	}

}

/* End of file journal_entry.php */
/* Location: ./CodeIgniter/application/controllers/journal_entry.php */