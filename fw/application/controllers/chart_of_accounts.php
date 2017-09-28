<?php

class Chart_of_accounts extends Asi_Controller {

	function Chart_of_accounts()
	{
		parent::Asi_Controller();
		$this->load->model('account_model');
		$this->load->model('glentrydetail_model');
		$this->load->model('mjournaldetail_model');
		$this->load->model('tjournaldetail_model');
		$this->load->model('ledger_model');
		$this->load->model('parameter_model');
		$this->load->library('constants');
	}

	function index()
	{
	}

	function show()
	{

		if (array_key_exists('coa',$_REQUEST))
		$this->account_model->populate($_REQUEST['coa']);
		$this->account_model->setValue('status_flag','1');
		$data = $this->account_model->get();

		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
			));
	}

	function read()
	{
		$data = $this->account_model->get_list(
		array('status_flag' => '1'),
		array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
		array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
		array('account_no','account_name','account_group','effectivity_date'),
		'account_no ASC');
		
		foreach($data['list'] as $row => $value){
			if($value['account_group'] != ''){
				$accntGrp_name = $this->constants->account_group[$value['account_group']];
			}else{
				$accntGrp_name = '';
			}
			$data['list'][$row]['accntGrp_name'] = $accntGrp_name;
			
			$data['list'][$row]['accnt_description'] = 	$value['account_no']." - ".$value['account_name'];			
		}

		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
			));
	}

	function delete()
	{
		log_message('debug', "[START] Controller chart_of_accounts:delete");
		log_message('debug', "chart_of_accounts param exist?:".array_key_exists('chart_of_accounts',$_REQUEST));

		if (array_key_exists('coa',$_REQUEST)) {
			//check if used by journals and gl entries
			$table_used = $this->checkIfUsed($_REQUEST['coa']['account_no']);
			if ($table_used){
				echo "{'success':false,'msg':'Cannot delete account \"" . $_REQUEST['coa']['account_no'] . " - " . $_REQUEST['coa']['account_name'] . "\" because it is being used in $table_used.'}";
			} else {
				$this->account_model->populate($_REQUEST['coa']);
				$this->account_model->setValue('status_flag', '0');
				$result = $this->account_model->update();
				if($result['affected_rows'] <= 0){
					echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
				} else
					echo "{'success':true,'msg':'Data successfully deleted.'}";
			}
		} else
			echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
			
		log_message('debug', "[END] Controller chart_of_accounts:delete");
	}

	function update()
	{
		log_message('debug', "[START] Controller chart_of_accounts:update");
		log_message('debug', "chart_of_accounts param exist?:".array_key_exists('chart_of_accounts',$_REQUEST));

		if (array_key_exists('coa',$_REQUEST)) {
			$this->account_model->populate($_REQUEST['coa']);
			$this->account_model->setValue('status_flag', '1');
			$result = $this->account_model->update();
			if($result['affected_rows'] <= 0){
				echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
			} else
				echo "{'success':true,'msg':'Data successfully saved.'}";
		} else
			echo "{'success':false,'msg':'Data was NOT successfully saved.'}";

		log_message('debug', "[END] Controller chart_of_accounts");
	}

	function checkIfUsed($account_no = null){
		$data = $this->glentrydetail_model->get_list(
			array('status_flag' => '1'
					,'account_no' => $account_no)
			, '1'
			, null
			, array('gl_code')
			, null
		);
		
		if(count($data['list']) > 0){
			return "GL Entries";
		}
		
		$data = $this->tjournaldetail_model->get_list(
			array('status_flag' => '3'
					,'account_no' => $account_no)
			, '1'
			, null
			, array('journal_no')
			, null
		);
		if(count($data['list']) > 0){
			return "Journal";
		}
		return false;
	}
	
	function add()
	{
		 log_message('debug', "[START] Controller chart_of_accounts:add");
		 log_message('debug', "chart_of_accounts param exist?:".array_key_exists('chart_of_accounts',$_REQUEST));

		 if (array_key_exists('coa',$_REQUEST)) {
			$account_no = $_REQUEST['coa']['account_no'];
			$created_by = $_REQUEST['coa']['created_by'];
			$result['error_code'] = 0;
			$result['error_message'] = "";
			$resultTLedgerInsert['error_code'] = 0;
			$resultTLedgerInsert['error_message'] = "";
			$accounting_period = $this->parameter_model->retrieveValue('ACCPERIOD');
		 	$this->account_model->populate($_REQUEST['coa']);
		 	$this->account_model->setValue('status_flag', '1');
		 	
		 	$checkDuplicate = $this->account_model->checkDuplicateKeyEntry();
	
			if($checkDuplicate['error_code'] == 1){
				$result['error_code'] = 1;
				$result['error_message'] = $checkDuplicate['error_message'];
				
			}
			else{
				$checkDuplicateTLedger = $this->ledger_model->checkDuplicateKeyEntry(array(
					'account_no' => $account_no
					,'accounting_period' => $accounting_period));
					
				if($checkDuplicateTLedger['error_code'] == 1){
					$resultTLedgerInsert['error_code'] = 2;
					$resultTLedgerInsert['error_message'] = "Duplicate T-Ledger Entry.";
					
				}
				else{
						
					$result = $this->account_model->insert();
					$this->ledger_model->populate(array(
						'account_no' => $account_no
						,'accounting_period' => $accounting_period
						,'beginning_balance' => 0
						,'debits' => 0
						,'credits' => 0
						,'ending_balance' => 0
						,'status_flag' => 1
						,'created_by' => $created_by
						,'close_debits' => 0
						,'close_credits' => 0
						,'net_income' => 0
					));
					$resultTLedgerInsert = $this->ledger_model->insert();
				}
			}
		 	
		 	if($result['error_code'] == 0 && $resultTLedgerInsert['error_code'] == 0){
		 		echo "{'success':true,'msg':'Data successfully saved.'}";
		 	} else{
				if($result['error_code'] != 0){
					echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';
				}
				else{
					echo '{"success":false,"msg":"'.$resultTLedgerInsert['error_message'].'","error_code":"'.$resultTLedgerInsert['error_code'].'"}';
				}
			}	
		} else
			echo "{'success':false,'msg':'Data was NOT successfully saved.','error_code':'2'}";

		 log_message('debug', "[END] Controller chart_of_accounts:add");
	}
}

/* End of file chart_of_accounts.php */
/* Location: ./PECA/application/controllers/chart_of_accounts.php */