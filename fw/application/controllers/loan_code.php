<?php

class Loan_code extends Asi_controller {

	function Loan_code()
	{
		parent::Asi_controller();
		$this->load->model('loancodeheader_model');
		$this->load->model('loancodedetail_model');
		$this->load->model('transactioncode_model');
		$this->load->model('loancodepaymenttype_model');
		$this->load->model('member_model');
		$this->load->model('tloan_model');
		$this->load->model('parameter_model');
	}

	function index()
	{
		
	}

	/**
	 * @desc Retrieve a single loan code header.
	 */
	function showHdr(){
	  	log_message('debug', "[START] Controller loan_code:showHdr");
		log_message('debug', "loanCodeHdr param exist?:".array_key_exists('loanCodeHdr',$_REQUEST));

		if (array_key_exists('loancodeHdr',$_REQUEST)){
			$this->loancodeheader_model->populate($_REQUEST['loancodeHdr']);
			$this->loancodeheader_model->setValue('status_flag', '1');
		}

		$data = $this->loancodeheader_model->get(null,
			array('loan_code'							
				,'loan_description'	
				,'transaction_code'					
				,'priority'						
				,'min_emp_months'						
				,'max_loan_amount'						
				,'min_term'						
				,'max_term'
				,'restructure'						
				,'emp_interest_pct'						
				,'comp_share_pct'						
				,'downpayment_pct'						
				,'payroll_deduction'						
				,'unearned_interest'						
				,'interest_earned'									
				,'take_home_pay'						
				,'submit_payslip'						
				,'post_dated_checks'						
				,'bsp_sbl'						
				,'pension_plan_slip'						
				,'avail_after_full_payment'
				,'bsp_computation')); ##### NRB EDIT
				
				
		$transData = $this->transactioncode_model->get_list(
			null
			,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
			,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
			,array('transaction_code','transaction_description')
		);
		
		$transArr = array();
		foreach($transData['list'] as $transRow){	
			$transArr[$transRow['transaction_code']] = $transRow['transaction_description'];
		}
		
		foreach($data['list'] as $row => $value){
			if(array_key_exists($value['transaction_code'],$transArr))
				$data['list'][$row]['transaction_description'] = $transArr[$value['transaction_code']];
			else  
				$data['list'][$row]['transaction_description'] = '';
		}
				
		
		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
			));
		log_message('debug', "[END] Controller loan_code:showHdr");
	}

	/**
	 * @desc Retrieve multiple loan code details from a single loan code header.
	 */
	function readDtl(){
	    $params = array(
			'status_flag' => '1'
			,'loan_code' => $_REQUEST['loan_code']
		);
	                  	
		$data = $this->loancodedetail_model->get_list(
		$params,
		array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
		array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
		array('loan_code'
		    ,'years_of_service'
			,'capital_contribution'
			,'pension'
            ,'guarantor')
			);
			
		foreach($data['list'] as $row => $value){
			$id = $value['loan_code'] . ':' . $value['years_of_service'];
			$data['list'][$row]['id'] = $id;
		}

		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
			));
			
		log_message('debug', "[END] Controller loan_code:readDtl");
	}
	
	/**
	 * @desc Retrieve multiple loan code payments from a single loan code header.
	 */
	function readPayment(){
	    $params = array(
			'lcpt.loan_code' => $_REQUEST['loan_code']
		);
	                  	
		$data = $this->loancodepaymenttype_model->getLoanPaymentType(
		$params,
		array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
		array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
		array('lcpt.loan_code AS loan_code'
		    ,'lcpt.transaction_code AS transaction_code'
		    ,'rt.transaction_description AS transaction_description')
			);
			
		foreach($data['list'] as $row => $value){
			$id = $value['loan_code'] . ':' . $value['transaction_code'];
			$data['list'][$row]['id'] = $id;
		}

		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
			));
			
		log_message('debug', "[END] Controller loan_code:readDtl");
	}

	/**
	 * @desc  To retrieve all active loan codes.
	 */
	function readHdr(){		
		$data = $this->loancodeheader_model->retrieveLoanCodeList(
		array('rh.status_flag' => '1'),
		array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
		array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
		array("loan_code"							
			,"loan_description"
			,"rh.transaction_code"						
			,"priority"						
			,"min_emp_months"						
			,"max_loan_amount"						
			,"min_term"						
			,"max_term"						
			,"restructure"						
			,"emp_interest_pct"						
			,"comp_share_pct"						
			,"downpayment_pct"						
			,"payroll_deduction"						
			,"unearned_interest"						
			,"interest_earned"												
			,"take_home_pay"						
			,"submit_payslip"						
			,"post_dated_checks"						
			,"bsp_sbl"						
			,"pension_plan_slip"						
			,"avail_after_full_payment"
			,"COALESCE(transaction_description,'') AS transaction_description"),
		'loan_description DESC'
		);
		
		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
			));
	}
	
	/**
	 * @desc  To retrieve all active loan codes and description only.
	 */
	function readLoanCodes(){
		if($this->member_model->employeeExists($_REQUEST['employee_id'])){
			
			
			$years_of_service = $this->member_model->getEmpYearsOfService($_REQUEST['employee_id']);	
			log_message('debug', "YOS" . $this->db->last_query());
			
			$data = $this->loancodeheader_model->retrieveLoanCodes(
			array('RH.status_flag' => '1')
			,null
			,null
			,array("RH.loan_code AS loan_code"							
				,"RH.loan_description AS loan_description"
				,"RH.emp_interest_pct AS emp_interest_pct"
				,"RH.comp_share_pct AS comp_share_pct"
				,"RH.interest_earned AS interest_earned"
				,"RH.unearned_interest AS unearned_interest"
				,"COALESCE(RL.pension,'N') AS pension"
				,"COALESCE(RL.guarantor, 0) AS guarantor")
			,'RH.loan_description ASC'
			,$years_of_service
			);
			
		log_message('debug', "Loan_codes" . $this->db->last_query());
		foreach($data['list'] as $key => $val){
			if($val['pension']=='null')
				$data['list'][$key]['pension'] = 'N';
			if($val['guarantor']=='null')
				$data['list'][$key]['guarantor'] = '0';
		}
			
			echo json_encode(array(
				'success' => true,
				'data' => $data['list'],
				'total' => $data['count'],
				'query' => $data['query']
				));
		}
		else{
			echo json_encode(array(
				'success' => true,
				'data' => array(),
				'total' => '0'
				));
		}
	}

	/**
	 * @desc Insert new data to Loan Code header table.
	 */
	function addHdr(){
		log_message('debug', "[START] Controller loan_code:addHdr");
		log_message('debug', "loanCodeHdr param exist?:".array_key_exists('loanCodeHdr',$_REQUEST));
		
		if (array_key_exists('loancodeHdr',$_REQUEST)) {
			$this->loancodeheader_model->populate($_REQUEST['loancodeHdr']);
			$this->loancodeheader_model->setValue('status_flag', '1');
			
			$checkDuplicate = $this->loancodeheader_model->checkDuplicateKeyEntry();
	
			if($checkDuplicate['error_code'] == 1){
				$result['error_code'] = 1;
				$result['error_message'] = $checkDuplicate['error_message'];
			}
			else{
				$result = $this->loancodeheader_model->insert();
			} 		
					
			if($result['error_code'] == 0){  
			  echo "{'success':true,'msg':'Data successfully saved.'}";
	        } else
			  echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';
		} else
			echo "{'success':false,'msg':'Data was NOT successfully saved.','error_code':'2'}";
			
		log_message('debug', "[END] Controller loan_code:addHdr");
	}
	
	/**
	 * @desc Insert new data to Loan Code detail table.
	 */
	function addDtl(){	
		log_message('debug', "[START] Controller gl_entries:addDtl");
		log_message('debug', "data param exist?:".array_key_exists('data',$_REQUEST));

		if (array_key_exists('data',$_REQUEST)) {
			$params = array();
			$params = json_decode(stripslashes($_REQUEST['data']),true);
			$params["capital_contribution"] = $params["capital_contribution"] == 'Y'? '0.3333333333':"0";
			$params['created_by'] = $_REQUEST['user'];
			$params['years_of_service'] = round($params['years_of_service'],1);
			$this->loancodedetail_model->populate($params);
			$this->loancodedetail_model->setValue('status_flag', '1');
			$result = $this->loancodedetail_model->insert();	
			
			if($result['error_code'] == 0){  
			  echo "{'success':true,'msg':'Data successfully saved.'}";
	        } else
			  echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';
			
		}else
			echo "{'success':false,'msg':'Data was NOT successfully saved.','error_code':'2'}";
		
		log_message('debug', "[END] Controller gl_entries:addDtl");
	}
	
	/**
	 * @desc Insert new loan code payment type.
	 */
	function addPayment(){
		log_message('debug', "[START] Controller loan_code:addPayment");
		log_message('debug', "data param exist?:".array_key_exists('data',$_REQUEST));

		if (array_key_exists('data',$_REQUEST)) {
			$data = array();
			$this->db->trans_start();
			if(substr($_REQUEST['data'],0,1)=='['){
				$data = json_decode(stripslashes($_REQUEST['data']),true);
				foreach($data as $key => $val){
					$data[$key] = implode(",", $val); 
				}
				$data = array_unique($data);
				
				foreach($data as $key => $val){
					$data[$key] = explode(",", $val); 
				}
			
				foreach($data as $key => $val){	
					$params['created_by'] = $_REQUEST['user'];
					$params['loan_code'] = $val[0];
					$params['transaction_code'] = $val[1];
					$this->loancodepaymenttype_model->populate($params);
					$this->loancodedetail_model->setValue('status_flag', '1');
					$result = $this->loancodepaymenttype_model->insert();
				}
			}	
			else{
				$data = json_decode(stripslashes($_REQUEST['data']),true);
				$params['created_by'] = $_REQUEST['user'];
				$params['loan_code'] = $data['loan_code'];
				$params['transaction_code'] = $data['transaction_code'];
				$this->loancodepaymenttype_model->populate($params);
				$this->loancodedetail_model->setValue('status_flag', '1');
				$result = $this->loancodepaymenttype_model->insert();
			}
			
			$this->db->trans_complete();
			
			if($this->db->trans_status() === TRUE){  
			  echo "{'success':true,'msg':'Data successfully saved.'}";
	        } else
			  echo "{'success':false,'msg':'Data was NOT successfully saved.','error_code':'2'}";
			
		}else
			echo "{'success':false,'msg':'Data was NOT successfully saved.','error_code':'2'}";
		
		log_message('debug', "[END] Controller loan_code:addPayment");
	}
	
	/**
	 * @desc Delete a single loan code header and its details.
	 */
	function deleteHdr(){
		log_message('debug', "[START] Controller loan_code:deleteHdr");
		log_message('debug', "loanCodeHdr param exist?:".array_key_exists('loancodeHdr',$_REQUEST));
		
		if (array_key_exists('loancodeHdr',$_REQUEST)) {
			$table_used = $this->checkIfUsed($_REQUEST['loancodeHdr']['loan_code']);
			if ($table_used){
				echo "{'success':false,'msg':'Cannot delete Loan Code \"" . $_REQUEST['loancodeHdr']['loan_code'] . " - " . $_REQUEST['loancodeHdr']['loan_description'] . "\" because it is being used in $table_used.'}";
			} else {
			
				$this->loancodeheader_model->setValue('status_flag', '0');
				
				$this->db->trans_start();
				$delHdrResult = $this->loancodeheader_model->update(array(
					'loan_code' => $_REQUEST['loancodeHdr']['loan_code']
					,'status_flag' => '1'
				));
				
				if($delHdrResult['affected_rows'] <= 0){
					echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
				} else {
					$this->loancodedetail_model->populate($_REQUEST['loancodeHdr']);
					$delDtlResult = $this->loancodedetail_model->delete();
					
					$this->loancodepaymenttype_model->populate($_REQUEST['loancodeHdr']);
					$delPaymentResult = $this->loancodepaymenttype_model->delete();
					
					if($delDtlResult['error_code'] != 0)
						echo '{"success":false,"msg":"'.$delDtlResult['error_message'].'"}';	
					else if($delPaymentResult['error_code'] != 0)
						echo '{"success":false,"msg":"'.$delPaymentResult['error_message'].'"}';
					else 
						echo "{'success':true,'msg':'Data successfully deleted.'}";
				}
				
				$this->db->trans_complete();
			}
		} else
			echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
			
		log_message('debug', "[END] Controller loan_code:deleteHdr");
	}
	
	function checkIfUsed($loan_code = null){
	
		$transaction_code = '';
		$data = $this->loancodeheader_model->get_list(
			array('status_flag' => '1'
					,'loan_code' => $loan_code)
			, '1'
			, null
			, array('transaction_code')
			, null
		);
		
		if(isset($data['list'][0]['transaction_code'])){
			$transaction_code = $data['list'][0]['transaction_code'];
		} else {
			//if not found it means already deleted.
			return false;
		}
		
		$data = $this->tloan_model->get_list(
			array('status_flag' => '1'
					,'loan_code' => $transaction_code)
			, '1'
			, null
			, array('loan_code')
			, null
		);
		if(count($data['list']) > 0){
			return "Loan Transaction";
		}
		return false;
	}
	

	/**
	 * @desc: Physically a single loan code detail.
	 */
	function deleteDtl(){
		log_message('debug', "[START] Controller loan_code:deleteDtl");
		log_message('debug', "data param exist?:".array_key_exists('data',$_REQUEST));
		
		if (array_key_exists('data',$_REQUEST)) {
			$params = array();
			$params = explode(':', json_decode(stripslashes($_REQUEST['data']),true));
			
			$result = $this->loancodedetail_model->delete(array(
				'loan_code' => $params[0]
				,'years_of_service' => $params[1]
			));
			
			if($result['affected_rows'] <= 0){
				echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
			} else
				echo "{'success':true,'msg':'Data successfully deleted.'}";
		} else {
			echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
		}

		log_message('debug', "[END] Controller loan_code:deleteDtl");
	}
	
	/**
	 * @desc: Physically a single loan code payment.
	 */
	function deletePayment(){
		log_message('debug', "[START] Controller loan_code:deletePayment");
		log_message('debug', "data param exist?:".array_key_exists('data',$_REQUEST));
		
		if (array_key_exists('data',$_REQUEST)) {
			$params = array();
			$params = explode(':', json_decode(stripslashes($_REQUEST['data']),true));
			
			$result = $this->loancodepaymenttype_model->delete(array(
				'loan_code' => $params[0]
				,'transaction_code' => $params[1]
			));
			
			if($result['affected_rows'] <= 0){
				echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
			} else
				echo "{'success':true,'msg':'Data successfully deleted.'}";
		} else {
			echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
		}

		log_message('debug', "[END] Controller loan_code:deletePayment");
	}

	/**
	 * @desc Update values of a loan code header.
	 *
	 */
	function updateHdr(){
		log_message('debug', "[START] Controller loan_code:updateHdr");
		log_message('debug', "loanCodeHdr param exist?:".array_key_exists('loancodeHdr',$_REQUEST));
		
		if (array_key_exists('loancodeHdr',$_REQUEST)) {
			$this->loancodeheader_model->populate($_REQUEST['loancodeHdr']);
			
			$checkDuplicate = $this->loancodeheader_model->checkDuplicateKeyEntry();
		
			if(strpos($checkDuplicate['error_message'], "already exists, update status?")!==false){
				echo "{'success':false,'msg':'Entry has already been deleted','error_code':'46'}";
			}
			else{
				$this->loancodeheader_model->setValue('status_flag', '1');
				$result = $this->loancodeheader_model->update();
			
				if($result['affected_rows'] <= 0){
					echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
				} else
					echo "{'success':true,'msg':'Data successfully saved.'}";
			} 	
		} else
			echo "{'success':false,'msg':'Data was NOT successfully saved.'}";

		log_message('debug', "[END] Controller loan_code:updateHdr");
	}
	
	/**
	 * @desc Update a specific loan code detail
	 */
	function updateDtl(){
		log_message('debug', "[START] Controller loan_code:updateDtl");
		log_message('debug', "data param exist?:".array_key_exists('data',$_REQUEST));
		
		if (array_key_exists('data',$_REQUEST)) {
			$params = array();
			$params = json_decode(stripslashes($_REQUEST['data']),true);
			unset($params['id']);
			
			$params['capital_contribution'] = $params['capital_contribution'] == 'true' ? '0.3333333333' : '0.000000000';
			$params['pension'] = $params['pension'] == 'true' ? 'Y' : 'N';
			$params['modified_by'] = $_REQUEST['user'];
			$this->loancodedetail_model->populate($params);
			$this->loancodedetail_model->setValue('status_flag', '1');
			$result = $this->loancodedetail_model->update(array(
				'loan_code' => $params['loan_code']
				,'years_of_service' => $params['years_of_service']
			));
			
			if($result['affected_rows'] <= 0){
				echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
			} else
				echo "{'success':true,'msg':'Data successfully saved.'}";
		} else {
			echo "{'success':true,'msg':'Data was NOT successfully saved.'}";
		}	
			
		log_message('debug', "[END] Controller loan_code:updateDtl");
	}	
			
}
/* End of file loan_code.php */
/* Location: ./CodeIgniter/application/controllers/loan_code.php */
