<?php

class Online_loan_payment extends Asi_Controller {

	function Online_loan_payment(){
		parent::Asi_controller();
		$this->load->model('onlineloanpayment_model');
		$this->load->model('onlineloanpaymentdetail_model');
		$this->load->model('loan_model');
		$this->load->model('loancodeheader_model');
		$this->load->model('loanguarantor_model');
		$this->load->model('member_model');
		$this->load->model('loancodepaymenttype_model');
		$this->load->model('transactioncharges_model');
		$this->load->model('Parameter_model');
		$this->load->model('mloan_model');
		$this->load->model('workflow_model');
		$this->load->model('loanpayment_model');
		$this->load->model('loanpaymentdetail_model');
		$this->load->helper('url');
	}

	function index(){
		
	}

	/**
	 * @desc To retrieve saved loan payment entries from database
	 */
	function read() {
	
		if(array_key_exists('transaction_code', $_REQUEST) && $_REQUEST['transaction_code']!= "")
			$param['tlp.transaction_code'] =  $_REQUEST['transaction_code'];
		if(array_key_exists('employee_id', $_REQUEST) && $_REQUEST['employee_id']!= "")
			$param['tlp.payor_id LIKE'] =  $_REQUEST['employee_id']."%";
		if(array_key_exists('first_name', $_REQUEST) && $_REQUEST['first_name']!= "")
			$param['me.first_name LIKE'] =  $_REQUEST['first_name']."%";
		if(array_key_exists('last_name', $_REQUEST) && $_REQUEST['last_name']!= "")
			$param['me.last_name LIKE'] =  $_REQUEST['last_name']."%";
		if(array_key_exists('or_no', $_REQUEST) && $_REQUEST['or_no']!= "")
			$param['tlp.or_no LIKE'] =  $_REQUEST['or_no']."%";

		$curr_date = $this->Parameter_model->retrieveValue('CURRDATE');
		if(array_key_exists('transaction_date_from',$_REQUEST)&& $_REQUEST['transaction_date_from']!= "")
			$transaction_date_from = date("Ymd", strtotime($_REQUEST['transaction_date_from']));
		else
			$transaction_date_from = $_REQUEST['transaction_date_from']== ""?"00000000":date("Ymd", strtotime('-1 day',strtotime($curr_date)));
		if(array_key_exists('transaction_date_to',$_REQUEST)&& $_REQUEST['transaction_date_to']!= "")
			$transaction_date_to = date("Ymd", strtotime($_REQUEST['transaction_date_to']));
		else
			$transaction_date_to = $_REQUEST['transaction_date_to']== ""?"99999999":date("Ymd", strtotime('+1 day',strtotime($curr_date)));
		
		$param['ow.request_type'] =  'LPAY';
		
		$param['tlp.status_flag >='] = '1';
		$data = $this->onlineloanpayment_model->getListLoanPayment(
													$param
													,$transaction_date_from
													,$transaction_date_to
													,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
													,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
													,array_key_exists('status',$_REQUEST) ? $_REQUEST['status'] : 0
													,array('tlp.loan_no AS loan_no'
															,'tlp.request_no AS request_no'
															,'tlp.payor_id AS  employee_id'
															,'tlp.amount AS  amount'
															,'me.last_name AS last_name'
															,'me.first_name AS first_name'
															,'tlp.amount AS amount'
															,"tlp.or_no as or_number"
															,'tlp.payment_date AS payment_date'
															,'rlh.loan_description AS loan_description'
															,'rt.transaction_description AS transaction_description'
															,'tlp.transaction_code AS transaction_code'
															,'ow.approver1 AS approver1'
															,'ow.approver2 AS approver2'
															,'ow.approver3 AS approver3'
															,'ow.approver4 AS approver4'
															,'ow.approver5 AS approver5'
															,'tlp.status_flag AS status_flag'
															)
													,'tlp.modified_date DESC'//loan_no
													);

		foreach($data['list'] as $key => $val){
			$data['list'][$key]['payment_date'] = date("mdY", strtotime($val['payment_date']));
		}

		echo json_encode(array(
			'success' => true
		,'data' => $data['list']
		,'total' => $data['count']
		,'query' => $data['query']
		));
	}

	/**
	 * @desc To retrieve loan information
	 */
	function showHdr(){
		/*$_REQUEST['lp'] = array('loan_no' => '1023');*/
		$param['tlp.status_flag >='] = '1';
		$param['ow.request_type'] =  'LPAY';
		$param['tlp.request_no'] = $_REQUEST['lp']['request_no'];
		$data = $this->onlineloanpayment_model->getListLoanPayment(
													$param
													,null
													,null
													,null
													,null
													,null
													,array('ml.loan_no	AS loan_no'													
															,'ml.employee_id AS employee_id'
															,'me.last_name AS last_name'
															,'me.first_name	AS first_name'
															,'me.company_code AS company_code'
															,'ml.loan_code AS loan_code'
															,'tlp.request_no AS request_no'
															,'tlp.payor_id AS payor_id'
															,'rlh.loan_description AS loan_description'
															,'ml.principal_balance AS balance'
															,'tlp.interest_amount AS interest_amount'
															,'tlp.amount AS amount'
															,'tlp.status_flag AS status_flag'
															,'tlp.payment_date AS payment_date'
															,'tlp.member_remarks AS member_remarks'
															,'tlp.peca_remarks AS peca_remarks'
															,'tlp.transaction_code AS transaction_code'
															,'ow.approver1 AS approver1'
															,'ow.approver2 AS approver2'
															,'ow.approver3 AS approver3'
															,'ow.approver4 AS approver4'
															,'ow.approver5 AS approver5'
														)
													);
		foreach($data['list'] as $key => $val){
					$data['list'][$key]['payment_date'] = date("mdY", strtotime($val['payment_date']));
				}
		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
				));
	}


	/**
	 * @desc To retrieve latest a specific loan payment
	 */
	function showDtl(){
		/*$_REQUEST['lp']['request_no'] = '33';
		$_REQUEST['lp']['loan_no'] = '1023';
		$_REQUEST['lp']['employee_id'] = '01517876';
		$_REQUEST['lp']['payment_date']	= '04/30/2010';
*/
		$params = array('tlp.request_no' => $_REQUEST['lp']['request_no']
						,'tlp.loan_no' => $_REQUEST['lp']['loan_no']
						,'tlp.payor_id' => $_REQUEST['lp']['employee_id']
						,'tlp.payment_date' => date("Ymd", strtotime($_REQUEST['lp']['payment_date']))
					);
		$_REQUEST['limit'] = 1;
		$data = $this->onlineloanpayment_model->getLpDtl(
													$params
													,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
													,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
													,array('tlp.request_no AS request_no'
															,'tlp.loan_no AS loan_no'
															,'tlp.payor_id AS payor_id'
															,'me.last_name AS last_name'
															,'me.first_name AS first_name'
															,'tlp.transaction_code AS transaction_code'
															,'rt.transaction_description AS transaction_description'
															,'tlp.payment_date AS payment_date'
															,'tlp.amount AS principal_amount'
															,'tlp.interest_amount AS interest_amount'
															,'tlp.member_remarks AS member_remarks'
															,'tlp.peca_remarks AS peca_remarks'
														)
													,'tlp.payment_date DESC'
													);

		foreach($data['list'] as $key => $val){
			$data['list'][$key]['payment_date'] = date("mdY", strtotime($val['payment_date']));
		}
			
		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
		));
	}

	/**
	 * @desc To retrieve the loan charges of a specific loan payment. Retrieved from t_loan_payment_detail
	 */
	function readCharges(){
/*		$_REQUEST['lp']['loan_no'] = '1023';
		$_REQUEST['lp']['employee_id'] = '01517876';
		$_REQUEST['lp']['payment_date']	= '04/30/2010';
*/
		$params = array(
						'tlpd.loan_no' => $_REQUEST['lp']['loan_no']
						,'tlpd.payor_id' => $_REQUEST['lp']['employee_id']
						,'tlpd.payment_date' => date("Ymd", strtotime($_REQUEST['lp']['payment_date']))
					);

		$data = $this->onlineloanpaymentdetail_model->getLpDetail(
														$params
														,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
														,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
														,array('tlpd.loan_no AS loan_no'
																,'tlpd.payment_date AS payment_date'
																,'tlpd.transaction_code AS charge_code'
																,'rt.transaction_description AS description'
																,'tlpd.payor_id AS payor_id'
																,'tlpd.amount AS amount'
															)
														,'rt.transaction_description ASC'
														);

		foreach($data['list'] as $key => $val){
			$data['list'][$key]['payment_date'] = date("mdY", strtotime($val['payment_date']));
		}

		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
		));
	}

	/**
	 * @desc Delete a single loan payment entry and all its charges
	 */
	function delete(){
			/*$_REQUEST['lp'] = array('loan_no' => '1099'
									,'amount' => '1.0000'
									,'transaction_code' => 'SPCL'
									,'payment_date' => '04/27/2010'
									,'payor_id' => '01517138'
									,'created_by' => 'WIE'
									);*/	
			log_message('debug', "[START] Controller online_loan_payment:deleteDtl");
			log_message('debug', "data param exist?:".array_key_exists('data',$_REQUEST));

			if (array_key_exists('lp',$_REQUEST)) {
				$this->onlineloanpayment_model->setValue('status_flag','0');
					
				$this->db->trans_start();
				$delHdrResult = $this->onlineloanpayment_model->update(array(
																		'request_no' => $_REQUEST['lp']['request_no']
																		,'status_flag >' => '0'
																		));
					
				if($delHdrResult['affected_rows'] <= 0){
					echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
				} else {
					$delDtlResult = $this->onlineloanpaymentdetail_model->delete(array(
																					'request_no' => $_REQUEST['lp']['request_no']
																					));

					if($delDtlResult['error_code'] == 0)
					echo "{'success':true,'msg':'Data successfully deleted.'}";
					else
					echo '{"success":false,"msg":"'.$delDtlResult['error_message'].'"}';
				}
					
				$this->db->trans_complete();
					
					
			} else
			echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
				
			log_message('debug', "[END] Controller online_loan_payment:deleteDtl");
	}

	/**
	 * @desc Update loan payment.
	 */
	function update(){

				
/*			$_REQUEST['lp'] = array('loan_no' => '1099'
									,'amount' => '100.0000'
									,'interest_amount' => '0.0000'
									,'transaction_code' => 'SPCL'
									,'payment_date' => '04/27/2010'
									,'payor_id' => '01517138'
									,'created_by' => 'WIE'
									,'request_no' => '40'
									,'member_remarks' => 'asasa'
									,'peca_remarks' => 'asasa!'
									);
*/			log_message('debug', "[START] Controller online_loan_payment:updateLoanPayment");
			log_message('debug', "lp param exist?:".array_key_exists('lp',$_REQUEST));

			if (array_key_exists('lp',$_REQUEST)) {
			
				$lpDetails = $this->getLpDetails($_REQUEST['lp']['request_no']);
				unset($_REQUEST['lp']['loan_no']);
				unset($_REQUEST['lp']['payor_id']);
				unset($_REQUEST['lp']['transaction_code']);
				
				if($lpDetails==null){
					echo '{"success":false,"msg":"Request no. does not exist","error_code":"0"}';
					return;
				}
				else if($this->exceedsWithdrawable($lpDetails['payor_id'], $_REQUEST['lp']['amount'], $withdraw)){
					echo '{"success":false,"msg":"Sorry, you cannot cross charge more than your maximum withdrawable amount of P'.number_format($withdraw,2,'.',',').'.","error_code":"0"}';
					return;
				}		
				$this->onlineloanpayment_model->populate($_REQUEST['lp']);
				$this->onlineloanpayment_model->setValue('source', 'U');
				$this->onlineloanpayment_model->setValue('balance', $this->computeBalance($lpDetails['loan_no'], $_REQUEST['lp']['amount']));
				$this->onlineloanpayment_model->setValue('modified_by', $_REQUEST['user']);
				$this->onlineloanpayment_model->setValue('status_flag', $_REQUEST['lp']['status_flag']);
					
				//$this->db->trans_start();
				$result = $this->onlineloanpayment_model->update();
					
				if($result['affected_rows']>=0){
					$dataDtl = array('loan_no' => $lpDetails['loan_no']
									,'amount' => $_REQUEST['lp']['amount']
									,'transaction_code' => $lpDetails['transaction_code']
									,'payment_date' => $this->parameter_model->getParam('CURRDATE')
									,'payor_id' => $lpDetails['payor_id']
									,'created_by' => $_REQUEST['user']
									,'request_no' => $_REQUEST['lp']['request_no']);
					$this->onlineloanpaymentdetail_model->populate($dataDtl);
					$this->onlineloanpaymentdetail_model->delete();

					$this->addCharges($dataDtl, $_REQUEST['lp']['request_no']);
				}
				//$this->db->trans_complete();
				if($_REQUEST['lp']['status_flag']==1){
				$saveOrSent = 'sent';
				}	
				else {
					$saveOrSent = 'saved';
				}	
				if($result['affected_rows'] >= 0 ){
					echo "{'success':true,'msg':'Data successfully ".$saveOrSent.".'}";
				} else
				echo "{'success':false,'msg':'Data was NOT successfully ".$saveOrSent.".'}";
			} else
			echo "{'success':false,'msg':'Data was NOT successfully saved.'}";

			log_message('debug', "[END] Controller online_loan_payment:updateLoanPayment");
	}

	/**
	 * @desc Inserts new online loan payment table
	 */
	 
function add()
	{
		//$_REQUEST['user'] ="BANANA";

		//$_REQUEST['data'] = '[{"loan_no":"10","transaction_code":"MNIL","payor_id":"01518520","amount":"5000.0000","interest_amount":"0.0000"},{"loan_no":"1000","transaction_code":"MNIL","payor_id":"01518520","amount":"10000.0000","interest_amount":"0.0000"},{"loan_no":"1001","transaction_code":"MNIL","payor_id":"01518520","amount":"5000.0000","interest_amount":"0.0000"},{"loan_no":"1002","transaction_code":"SPEL","payor_id":"01518520","amount":"5000.0000","interest_amount":"0.0000"},{"loan_no":"1003","transaction_code":"SPEL","payor_id":"01518520","amount":"6667.0000","interest_amount":"0.0000"}]';
		log_message('debug', "[START] Controller online_loan_payment:addLoanPayment");
		log_message('debug', "data param exist?:".array_key_exists('data',$_REQUEST));
		if (array_key_exists('data',$_REQUEST)) {
			//$this->db->trans_start();
			if($this->exceedsWithdrawable($_REQUEST['user'], $_REQUEST['total'], $withdraw)){
				echo '{"success":false,"msg":"Sorry, you cannot cross charge more than your maximum withdrawable amount of P'.number_format($withdraw,2,'.',',').'.","error_code":"0"}';
				return;
			}			
			$data =array();
			if(substr($_REQUEST['data'],0,1)=='['){
				$data = json_decode(stripslashes($_REQUEST['data']),true);
				 /* echo "<pre>";
				print_r($data);
				echo "</pre>"; */

				foreach($data as $key => $val){
					$data[$key] = implode(",", $val);
				}
				
				$data = array_unique($data);
				
				foreach($data as $key => $val){
					$data[$key] = explode(",", $val); 
				}
		
				foreach($data as $key => $val){	
					$params['created_by'] = $_REQUEST['user'];
					$params['loan_no'] = $val[0];
					$params['transaction_code'] = $this->getPaymentType($val[1]);
					$params['payor_id'] = $val[2];
					$params['amount'] = $val[3];
					$params['interest_amount'] = $val[4];
					$params['payment_date'] = $this->parameter_model->getParam('CURRDATE');
					$this->onlineloanpayment_model->populate($params);
					$this->onlineloanpayment_model->setValue('balance', $this->computeBalance($params['loan_no'], $params['amount']));
					$this->onlineloanpayment_model->setValue('status_flag', '1');
					$this->onlineloanpayment_model->setValue('or_no', $this->parameter_model->incParam('LASTORNO'));
					$this->onlineloanpayment_model->setValue('or_date', $this->parameter_model->getParam('CURRDATE'));
					$this->onlineloanpayment_model->setValue('source', 'U');
					$this->onlineloanpayment_model->setValue('status_flag', $_REQUEST['lp']['status_flag']);
					//$this->onlineloanpayment_model->setValue('interest_amount', '0.0000');
					$request_no = $this->parameter_model->retrieveValue('OREQ')+1;
					$this->onlineloanpayment_model->setValue('request_no', $request_no);
								
					/* $_REQUEST['lp'] = array('transaction_code' => $params['transaction_code']
								,'amount' => $params['amount']
								,'loan_no' => $params['loan_no']
								,'payment_date' => $this->parameter_model->getParam('CURRDATE')
								,'payor_id' => $params['payor_id']
								,'created_by' => $params['created_by']
								,'request_no' => $request_no
					); */
					$result = $this->onlineloanpayment_model->insert();
					if($result['error_code'] != 0){
						echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';
						return;
					}
					$this->addCharges($params, $request_no);
					$this->parameter_model->updateValue(('OREQ'), $request_no, $params['created_by']);
				}
			}	
			else
			{
				$data = json_decode(stripslashes($_REQUEST['data']),true);
				$params['created_by'] = $_REQUEST['user'];
				$params['loan_no'] = $data['loan_no'];
				
				$params['transaction_code'] = $this->getPaymentType($data['transaction_code']);
				$params['payor_id'] = $data['payor_id'];
				$params['amount'] = $data['amount'];
				$params['interest_amount'] = $data['interest_amount'];
				$params['payment_date'] = $this->parameter_model->getParam('CURRDATE');
				$this->onlineloanpayment_model->populate($params);
				$this->onlineloanpayment_model->setValue('balance', $this->computeBalance($params['loan_no'], $params['amount']));
				$this->onlineloanpayment_model->setValue('status_flag', '1');
				$this->onlineloanpayment_model->setValue('or_no', $this->parameter_model->incParam('LASTORNO'));
				$this->onlineloanpayment_model->setValue('or_date', $this->parameter_model->getParam('CURRDATE'));
				$this->onlineloanpayment_model->setValue('source', 'U');
				$this->onlineloanpayment_model->setValue('status_flag', $_REQUEST['lp']['status_flag']);
				//$this->onlineloanpayment_model->setValue('interest_amount', '0.0000');

				$request_no = $this->parameter_model->retrieveValue('OREQ')+1;
				$this->onlineloanpayment_model->setValue('request_no', $request_no);
				
				$result = $this->onlineloanpayment_model->insert();
				if($result['error_code'] != 0){
						echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';
						return;
				}
				/* $_REQUEST['lp'] = array('transaction_code' => $params['transaction_code']
								,'amount' => $params['amount']
								,'loan_no' => $params['loan_no']
								,'payment_date' => $this->parameter_model->getParam('CURRDATE')
								,'payor_id' => $params['payor_id']
								,'created_by' => $params['created_by']
								,'request_no' => $request_no
							); */
				$this->addCharges($params, $request_no);
				
				$this->parameter_model->updateValue(('OREQ'), $request_no, $params['created_by']);
			}
			if($_REQUEST['lp']['status_flag']==1){
				$saveOrSent = 'sent';
			}	
			else {
				$saveOrSent = 'saved';
			}	
			echo "{'success':true,'msg':'Data successfully ".$saveOrSent.".'}";
			//$this->db->trans_complete();		
			/* if($this->db->trans_status() === TRUE) {
			  echo "{'success':true,'msg':'Data successfully saved.'}";
			} else
			echo "{'success':false,'msg':'Data was NOT successfully saved.','error_code':'2'}";	 */
		} else
			echo "{'success':false,'msg':'Data was NOT successfully saved.'}";

		log_message('debug', "[END] Controller online_loan_payment:addLoanPayment");
	}
	/**
	 * @desc Checks if user exceeds the maximum withdrawable amount
	 */
	
	function exceedsWithdrawable($employee_id, $amount, &$withdraw){
		$accounting_period = date("Ymd", strtotime($this->parameter_model->getParam('ACCPERIOD')));
		$data = $this->mloan_model->showBalanceInfo($employee_id, $accounting_period);
		if($amount>$data['maxWdwlAmount']){
			$withdraw = $data['maxWdwlAmount'];
			return true;
		}
		else{
			return false;
		}
	
	}
	
	/**
	 * @desc Gets the corresponding CC payment of a certain loan code
	 */
	
	function getPaymentType($loan_code){
		$data = $this->loancodeheader_model->getLoanCC(
							array( 'rtc.capcon_effect' => '-1'
									,'rlh.transaction_code' => $loan_code
									,'rtc.status_flag <>' => '0'
									,'rlh.status_flag <>' => '0'
									)
							,array('rtc.transaction_code'));
		if(@$data['list'][0])
			return $data['list'][0]['transaction_code'];
		else
			return $loan_code;
	}
	
	/**
	 * @desc Reads all CC payment
	 */
	
	function readPaymentType(){
		$data = $this->loancodeheader_model->getLoanCC(
							array( 'rtc.capcon_effect' => '-1'
									,'rtc.status_flag <>' => '0'
									,'rlh.status_flag <>' => '0'
									)
							,array('rtc.transaction_code'
									,'rtc.transaction_description'));
		
		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
		));
	}
	
	
	/**
	 * @desc Adds new loan type charges in t_online_loan_payment_detail table
	 */
	function addCharges($lp, $request_no){
		log_message('debug', "[START] Controller online_loan_payment:addCharges");
		log_message('debug', "lp param exist?:".array_key_exists('lp',$_REQUEST));
		/*$_REQUEST['lp'] = array('transaction_code' => 'MNIL'
								,'amount' => '500.00'
								,'loan_no' => '1056'
								,'payment_date' => '04/29/2010'
								,'payor_id' => '01517371'
								,'created_by' => 'WIE'
								,'request_no' => '31'
							);*/
		
		$data = $this->readTransactionCharges($lp['transaction_code']);
		foreach($data['list'] as $key => $params){
			$formula = str_replace('pr', $lp['amount'], $params['formula']);
			eval("\$params['amount'] = $formula;");
			unset($params['formula']);
			$params['loan_no'] = $lp['loan_no'];
			$params['payment_date'] = $lp['payment_date'];
			$params['transaction_code'] = $lp['transaction_code'];
			$params['payor_id'] = $lp['payor_id'];
			//$params['created_by'] = $_REQUEST['user'];
			$params['created_by'] = $lp['created_by'];
			$this->onlineloanpaymentdetail_model->populate($params);
			$this->onlineloanpaymentdetail_model->setValue('status_flag', '1');
			$this->onlineloanpaymentdetail_model->setValue('request_no', $request_no);
			$result = $this->onlineloanpaymentdetail_model->insert();
			
		}
		
		log_message('debug', "[END] Controller online_loan_payment:addCharges");
	}

	/**
	 * @desc Retrieves transaction charge of the specified transaction type
	 */
	function readTransactionCharges($transaction_code){
		
		$data = $this->transactioncharges_model->getTransChargeList(
													array('rtc.transaction_code'=> $transaction_code, 'rtc.status_flag' => '1')
															,null
															,null
															,array('rtc.charge_code AS transaction_code'
															,'rtc.charge_formula AS formula'
														));

		return $data;
	}

	/**
	 * @desc To retrieve all active employees with loans
	 */
	function readEmployeesWithLoan(){

		 /*$_REQUEST['lp']['empId'] = '01517876';
		 $_REQUEST['lp']['empFname'] = 'ARMINDA';
		 $_REQUEST['lp']['empLname'] = 'BASA';*/
		
		if(isset($_REQUEST['lp']['loan_no'])&& $_REQUEST['lp']['loan_no']!="")
			$param['me.loan_no LIKE'] = $_REQUEST['lp']['loan_no'];
		if(isset($_REQUEST['employee_id'])&& $_REQUEST['employee_id']!="")
			$param['me.employee_id LIKE'] = $_REQUEST['employee_id'];
		if(isset($_REQUEST['first_name'])&& $_REQUEST['first_name']!="")
			$param['me.first_name LIKE'] = $_REQUEST['first_name'].'%';
		if(isset($_REQUEST['last_name'])&& $_REQUEST['last_name']!="")
			$param['me.last_name LIKE'] = $_REQUEST['last_name'];
			
		$param['ml.status_flag'] = '1'; //originally 3
		$param['me.member_status'] = 'A';
		//$param['ml.close_flag'] = '0';

		$data = $this->mloan_model->getLoanList(
											$param
											,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
											,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
											,array('ml.employee_id AS employee_id'
											,'me.last_name AS last_name'
											,'me.first_name first_name'
										)
										,null
										,'distinct'
										);

		echo json_encode(array(
			'success' => true
			,'data' => $data['list']
			,'total' => $data['count']
			,'query' => $data['query']
			));
	}


	/**
	 * @desc To retrieve all loans of an employee
	 */
	function readEmployeeLoanList(){
		 // $_REQUEST['employee_id'] = '01517876'; 
		$param['ml.employee_id'] = $_REQUEST['employee_id'];
		$param['ml.status_flag'] = '2'; //originally 3
		$param['me.member_status'] = 'A';
		$param['ml.close_flag'] = '0';
		$param['ml.principal_balance >'] = '0';
		//$param['ml.loan_no  NOT IN'] = '1001//'SELECT ol.loan_no FROM o_loan_payment ol WHERE ol.status_flag > 0';
		$data = $this->onlineloanpayment_model->getLoanList(
										$param
										,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
										,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
										,array('ml.loan_no AS loan_no'
												,'ml.loan_code AS loan_code'
												,"DATE_FORMAT(ml.loan_date,'%m%d%Y') AS loan_date"
												,'ml.principal AS principal'
												,'ml.term AS term'
												,'ml.interest_rate AS rate'
												,'ml.employee_interest_amortization AS interest_amortization'
												,'ml.employee_principal_amort AS principal_amortization'
												,'ml.principal_balance AS principal_balance'
												,'(ml.employee_interest_amortization + ml.employee_principal_amort) AS monthly_amortization'
												,'me.employee_id AS employee_id'
												//,'me.last_name AS last_name'
												//,'me.first_name AS first_name'
												//,'me.company_code AS company_code'
											)
											,null
											,'distinct'
											);
		

		echo json_encode(array(
			'success' => true
		,'data' => $data['list']
		,'total' => $data['count']
		,'query' => $data['query']
		));
	}

	/**
	 * @desc To retrieve all loans of all active employees
	 */
	function readLoanList(){
		
		$param = array('ml.status_flag' => '1', 'me.member_status' => 'A', 'ml.principal_balance >' => '0' /*, 'close_flag' => '0'*/);
		$data = $this->mloan_model->getLoanList(
								$param
								,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
								,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
								,array('ml.loan_no AS loan_no'
										,'ml.loan_code AS loan_code'
										,'rl.loan_description AS loan_description'
										,'ml.loan_date AS loan_date'
										,'ml.principal AS principal'
										,'ml.principal_balance AS loan_balance'
										,'ml.interest_rate AS rate'
										,'ml.term AS term'
										,'ml.employee_interest_amortization AS employee_interest_amortization'
										,'ml.employee_principal_amortization AS employee_principal_amortization'
										,'me.employee_id AS employee_id'
										,'me.last_name AS last_name'
										,'me.first_name AS first_name'
										,'me.company_code AS company_code'
									)
								,'me.last_name, me.first_name, ml.loan_no'
								);

		echo json_encode(array(
			'success' => true
		,'data' => $data['list']
		,'total' => $data['count']
		,'query' => $data['query']
		));
	}

	/**
	 * @desc To retrieve all guarantors of a loan
	 */
	function readLoanPayor(){
		/*$_REQUEST['lp']['transCode'] = 'CONL';
		$_REQUEST['lp']['loan_no'] = '1023';*/
		if(isset($_REQUEST['lp']['loan_no'])&& $_REQUEST['lp']['loan_no']!="")
			$param['ml.loan_no LIKE'] = $_REQUEST['lp']['loan_no'];
			
		$param['ml.status_flag'] = '3';
		$param['me.member_status'] = 'A';
		//$param['ml.close_flag'] = '0';

		$data1 = $this->mloan_model->getLoanList(
										$param
										,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
										,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
										,array('ml.employee_id AS payor_id'
											,'me.first_name first_name'
											,'me.last_name AS last_name'
										)
										,null
										,'distinct'
										);
		
		
		$_REQUEST['filter'] = array('tlg.loan_no' => $_REQUEST['lp']['loan_no']);
		$data2 = $this->loanguarantor_model->getGuarantor(
												array_key_exists('filter',$_REQUEST) ? $_REQUEST['filter'] : null
												,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
												,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
												,array('tlg.guarantor_id AS payor_id'
													,'me.last_name AS last_name'
													,'me.first_name AS first_name'
												)
												,'me.last_name, me.first_name ASC'
												);

		$data['list'] = array_merge($data1['list'], $data2['list']);
		$data['count'] = count($data1['list']) + count($data2['list']);
		$data['query'] = $data1['query'] . " ; ". $data2['query'];
		
		echo json_encode(array(
			'success' => true
		,'data' => $data['list']
		,'total' => $data['count']
		,'query' => $data['query']
		));
	}

	/**
	 * @desc To retrieve payment type of loan
	 */
	function readLoanPaymentType(){
		/*$_REQUEST['lp'] = array('loan_no' => '1056');*/
		$data = $this->mloan_model->get(array('loan_no'=>$_REQUEST['lp']['loan_no'])
					,'loan_code');
		
		$param['lcpt.loan_code'] = $data['list'][0]['loan_code'];
		
		$data = $this->loancodepaymenttype_model->getLoanPaymentType(
														$param
														,null
														,null
														,array('lcpt.transaction_code AS payment_code
																,rt.transaction_description AS payment_type_description'					
																)
														,'payment_type_description ASC'
														);

				echo json_encode(array(
			'success' => true
				,'data' => $data['list']
				,'total' => $data['count']
				,'query' => $data['query']
				));
	}

	/**
	 * @desc To retrieve loan payment type charges
	 */
	function readLoanPaymentTypeCharges(){
		/*$_REQUEST['lp']['transCode'] = 'PENT';*/

		$param['rtc.charge_code'] = $_REQUEST['lp']['transCode'];
		$data = $this->transactioncharges_model->getTransChargeList(
													$param
													,null
													,null
													,array('rtc.charge_code'
															,'rt.transaction_description'							
															,'rtc.charge_formula as amount'					
															)
													,'rt.transaction_description ASC'
													);

				echo json_encode(array(
			'success' => true
				,'data' => $data['list']
				,'total' => $data['count']
				,'query' => $data['query']
				));
	}

	/**
	 * @desc Computes the new balance of a loan (loan balance - principal amount)
	 */
	function computeBalance($loan_no, $principal_amount){
		$data = $this->loan_model->get(array('loan_no' => $loan_no),
		array('principal_balance'));
		if(!isset($data['list'][0]['principal_balance'])|| $data['list'][0]['principal_balance']=="")
			return 0;
		else
			return ($data['list'][0]['principal_balance'] - $principal_amount);
	}
	
	/**  
	 * @desc get list of approvers with request type LPAY
	 */
	function readApprovers()
	{
		$data = $this->workflow_model->getRequestList(
				array('request_type' => 'LPAY')
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
	 * @desc Approve change request
	 */
	function approve()
	{
		/* $_REQUEST['data'] = array('request_no' => '86'
								,'status_flag' => '5');
		$_REQUEST['user'] = 'WIE'; */
		
		log_message('debug', "[START] Controller online_loan_payment:approve");
		log_message('debug', "data param exist?:".array_key_exists('data',$_REQUEST));
		
		if ((array_key_exists('data',$_REQUEST))&&($_REQUEST['data']['status_flag']>2)&&($_REQUEST['data']['status_flag']<9)) {
			$status = $this->workflow_model->checkNextApprover('LPAY', $_REQUEST['data']['status_flag']);
			if($status != 0 && $status != 9)
			{
				$this->onlineloanpayment_model->setValue('status_flag', $status);
				$this->onlineloanpayment_model->setValue('peca_remarks', array_key_exists('peca_remarks',$_REQUEST['data']) ? $_REQUEST['data']['peca_remarks'] : null);
				$result = $this->onlineloanpayment_model->update(array('request_no' => $_REQUEST['data']['request_no']));	
				if($result['affected_rows'] <= 0){
					echo "{'success':false,'msg':'Request was NOT successfully approved.'}";
				} else
				{
					echo "{'success':true,'msg':'Request was successfully approved.'}";
				}
			}
			else if($status == 9)
			{
				$_REQUEST['lp']['request_no'] = $_REQUEST['data']['request_no'];
				$param['tlp.status_flag >='] = '1';
				$param['ow.request_type'] =  'LPAY';
				$param['tlp.request_no'] = $_REQUEST['lp']['request_no'];
				$data = $this->onlineloanpayment_model->getListLoanPayment(
															$param
															,null
															,null
															,null
															,null
															,null
															,array('ml.loan_no	AS loan_no'													
																	,'ml.employee_id AS employee_id'
																	,'tlp.payor_id AS payor_id'
																	,'tlp.interest_amount AS interest_amount'
																	,'tlp.amount AS amount'
																	,'tlp.payment_date AS payment_date'
																	,'tlp.member_remarks AS member_remarks'
																	,'tlp.peca_remarks AS peca_remarks'
																	,'tlp.transaction_code AS transaction_code'
																)
															);
				foreach($data['list'] as $key => $val)
					$data['list'][$key]['payment_date'] = date("m/d/Y", strtotime($val['payment_date'])); 
				$params = array(
								'loan_no' => $data['list'][0]['loan_no']
								,'payment_date' => date('Ymd', strtotime($data['list'][0]['payment_date']))
								,'transaction_code' => $data['list'][0]['transaction_code']
								,'payor_id' => $data['list'][0]['payor_id']
								,'amount' => $data['list'][0]['amount']
								,'interest_amount' => $data['list'][0]['interest_amount']
								,'remarks' => $data['list'][0]['peca_remarks']
								,'created_by' => $_REQUEST['user']
							);
				$error_code = $this->addAfterApproval($params, $_REQUEST['lp']['request_no'], $data['list'][0]['employee_id']);
				if($error_code == 0){
					echo "{'success':true,'msg':'Request successfully approved.'}";
				}
				else if($error_code==41){
					echo '{"success":false,"msg":"Specified employee is not the applyer of the loan","error_code":"'.$error_code.'"}';
				}
				else if ($error_code==2){
					echo '{"success":false,"msg":"Duplicate entry for the fields loan number, payment date, payment type and payor. Update?","error_code":"'.$error_code.'"}';
				}
				else{
					echo '{"success":false,"msg":"Request was NOT successfully approved.","error_code":"'.$error_code.'"}';
				}
			}
			else{
				echo "{'success':false,'msg':'Request NOT successfully approved.'}";
			}
		} else
			echo "{'success':false,'msg':'Request was NOT successfully approved.'}";
			
		log_message('debug', "[END] Controller online_loan_payment:approve");
	}
	
	function addAfterApproval($lp, $request_no, $emp_id)
	{
		$this->loanpayment_model->populate($lp);
		$or_no = $this->parameter_model->incParam('LASTORNO');
		$or_date = $this->parameter_model->retrieveValue('CURRDATE');
		$this->loanpayment_model->setValue('balance', $this->computeBalance($lp['loan_no'], $lp['amount']));
		$this->loanpayment_model->setValue('or_no', $or_no );
		$this->loanpayment_model->setValue('or_date', $or_date);
		$this->loanpayment_model->setValue('source', 'U');
		$this->loanpayment_model->setValue('status_flag', '1');
					
		$checkDuplicate = $this->loanpayment_model->checkDuplicateKeyEntry(array(
					'loan_no' => $lp['loan_no']
					,'payment_date' => $lp['payment_date']
					,'transaction_code' => $lp['transaction_code']
					,'payor_id' => $lp['payor_id']
					));
		
		if(!$this->checkLoanApplyer($lp['loan_no'],$emp_id)){
						$result['error_code'] = 41;
						$result['error_message'] = 'Specified employee is not the applyer of the loan';
		}
		else if($checkDuplicate['error_code'] == 1){
			$result['error_code'] = 2;
			$result['error_message'] = $checkDuplicate['error_message'];
		}
		else{
			$result = $this->loanpayment_model->insert();
			$params = array(
						'loan_no' => $lp['loan_no']
						,'payment_date' => $lp['payment_date']
						,'transaction_code' => $lp['transaction_code']
						,'payor_id' => $lp['payor_id']
						,'created_by' => $lp['created_by']
						,'amount' => $lp['amount']
					);
			$this->addChargesLP($params);
			
			if ($result['affected_rows'] <= 0){
				$result['error_code'] = 1;
			}
			else{
				$this->onlineloanpayment_model->setValue('status_flag', '9');
				$this->onlineloanpayment_model->setValue('or_no', $or_no);
				$this->onlineloanpayment_model->setValue('or_date', $or_date);
				$result = $this->onlineloanpayment_model->update(array('request_no' => $request_no));
				$result['error_code'] = 0;
			}
		}
		
		return $result['error_code'];
	}
	
	function updateOnDuplicate(){
		$online_lp = $this->onlineloanpayment_model->get(array('request_no'=>$_REQUEST['request_no'])
			,array('loan_no'
				,'payment_date'
				,'transaction_code'
				,'payor_id'
				,'amount'
				,'interest_amount'
				,'balance'
			));
		
		if($online_lp['count']==0){
			echo'{"success":false,"msg":"Request no. does not exist"}';
		}
		else{
			$this->loanpayment_model->populate($online_lp['list'][0]);
			$this->loanpayment_model->setValue('source', 'U');
			$this->loanpayment_model->setValue('balance', $this->computeBalance($online_lp['list'][0]['loan_no'], $online_lp['list'][0]['amount']));
			$this->loanpayment_model->setValue('modified_by', $_REQUEST['user']);
			$result = $this->loanpayment_model->update(array(
				'loan_no' => $online_lp['list'][0]['loan_no']
				,'payment_date' => $online_lp['list'][0]['payment_date']
				,'transaction_code' => $online_lp['list'][0]['transaction_code']
				,'payor_id' => $online_lp['list'][0]['payor_id']
			));
			
			if($result['affected_rows']>0){
				$this->onlineloanpayment_model->setValue('status_flag', '9');
				$this->onlineloanpayment_model->update(array('request_no' => $_REQUEST['request_no']));
				$this->onlineloanpayment_model->setValue('peca_remarks', $_REQUEST['remarks']);
				echo'{"success":true,"msg":"Loan payment successfully updated"}';
			}
			else{
				echo'{"success":false,"msg":"Update failed."}';
			}
		}	
	}
	
	/**
	 * @desc Adds new loan type charges in t_loan_payment_detail table
	 */
	function addChargesLP($lp){
		log_message('debug', "[START] Controller loan_payment:addCharges");
		log_message('debug', "lp param exist?:".array_key_exists('lp',$_REQUEST));

		$data = $this->readTransactionCharges($lp['transaction_code']);
		
		foreach($data['list'] as $key => $params){
			$formula = str_replace('lpa', $lp['amount'], $params['formula']);
			$formula = str_replace('pr', $lp['amount'], $formula);
			eval("\$params['amount'] = $formula;");
			unset($params['formula']);
			$params['loan_no'] = $lp['loan_no'];
			$params['payment_date'] = $lp['payment_date'];
			$params['payor_id'] = $lp['payor_id'];
			$params['created_by'] = $lp['created_by'];
			$this->loanpaymentdetail_model->populate($params);
			$this->loanpaymentdetail_model->setValue('status_flag', '1');
			$this->loanpaymentdetail_model->insert();
		}

		log_message('debug', "[END] Controller loan_payment:addCharges");
	}
	
	/**
	 * @desc Checks if the loan and its applyer match
	 */
	function checkLoanApplyer($loan_no, $employee_id){
		$data = $this->mloan_model->get(array(
			'loan_no' => $loan_no
			,'employee_id' => $employee_id
		), 'COUNT(*) AS count');
		
		if($data['list'][0]['count']==0)
			return 0;
		else
			return 1;
	}
	
	/**
	 * @desc disapprove change request
	 */
	function disapprove()
	{
		/*$_REQUEST['data'] = array('request_no' => '39');*/
		
		log_message('debug', "[START] Controller online_loan_payment:disapprove");
		log_message('debug', "data param exist?:".array_key_exists('data',$_REQUEST));
		
		if ((array_key_exists('data',$_REQUEST))&&($_REQUEST['data']['status_flag']>2)&&($_REQUEST['data']['status_flag']<9)) {
			$this->onlineloanpayment_model->populate($_REQUEST['data']);
			$this->onlineloanpayment_model->setValue('status_flag', '10');
			$this->onlineloanpayment_model->setValue('peca_remarks', array_key_exists('peca_remarks',$_REQUEST['data']) ? $_REQUEST['data']['peca_remarks'] : null);
			$result = $this->onlineloanpayment_model->update();	
			if($result['affected_rows'] <= 0){
				echo "{'success':false,'msg':'Request was NOT successfully disapproved.'}";
			} else
				echo "{'success':true,'msg':'Request successfully disapproved.'}";
		} else
			echo "{'success':false,'msg':'Request was NOT successfully disapproved.'}";
			
		log_message('debug', "[END] Controller online_loan_payment:disapprove");
	}
	
	function getLpDetails($request_no){
		$data = $this->onlineloanpayment_model->get(array('request_no'=>$request_no)
		,null, null, array('payor_id', 'loan_no', 'transaction_code'));
		
		if($data['count']>0){
			return $data['list'][0];
		}
		else{
			return null;
		}
	}
}

/* End of file online_loan_payment.php */
/* Location: ./CodeIgniter/application/controllers/online_loan_payment.php */