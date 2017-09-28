<?php

class Loan_payment extends Asi_controller {

	function Loan_payment(){
		parent::Asi_controller();
		$this->load->model('loanpayment_model');
		$this->load->model('loanpaymentdetail_model');
		$this->load->model('loan_model');
		$this->load->model('mloan_model');
		$this->load->model('loancodeheader_model');
		$this->load->model('loanguarantor_model');
		$this->load->model('member_model');
		$this->load->model('loancodepaymenttype_model');
		$this->load->model('transactioncharges_model');
		$this->load->model('parameter_model');
		$this->load->model('mloanguarantor_model');
		$this->load->model('transactioncode_model');
	}

	function index(){

	}

	/**
	 * @desc To retrieve saved loan payment entries from database
	 */
	function readHdr() {
		
		if(array_key_exists('loan_no', $_REQUEST) && $_REQUEST['loan_no']!= "")
			$param['tlp.loan_no LIKE'] =  $_REQUEST['loan_no']."%";
		if(array_key_exists('employee_id', $_REQUEST) && $_REQUEST['employee_id']!= ""){
			$param['tlp.payor_id LIKE'] =  $_REQUEST['employee_id']."%";
		}
		else{
			if(array_key_exists('first_name', $_REQUEST) && $_REQUEST['first_name']!= "")
				$param['me.first_name LIKE'] =  $_REQUEST['first_name']."%";
			if(array_key_exists('last_name', $_REQUEST) && $_REQUEST['last_name']!= "")
				$param['me.last_name LIKE'] =  $_REQUEST['last_name']."%";
		}
		
		$param['tlp.status_flag'] = '1';
		$data = $this->loanpayment_model->getListLoanPayment(
		$param
		,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
		,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
		,array('tlp.loan_no AS loan_no'
		,'tlp.payor_id AS  employee_id'
		,'me.last_name AS last_name'
		,'me.first_name AS first_name'
		,'tlp.payment_date AS payment_date'
		,'rlh.loan_description AS loan_description'
		,'tlp.transaction_code AS transaction_code'
		)
		,'tlp.payment_date DESC'
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
		/*$_REQUEST['lp'] = array(
			'employee_id' => '00421526'
			,'loan_no' =>	'123'
			,'payment_date' => '04/10/2010'
		);*/
		
		$data = $this->loanpayment_model->getListLoanPayment(array(
			'ml.loan_no' => $_REQUEST['lp']['loan_no']
			,'tlp.payor_id' => $_REQUEST['lp']['employee_id']
			,'tlp.payment_date' => date("Ymd", strtotime($_REQUEST['lp']['payment_date']))
			,'tlp.status_flag' =>  '1'
			,'tlp.transaction_code' => $_REQUEST['lp']['transaction_code']
			)
			,null
			,null
			,array(
				"tlp.or_no AS or_no"										
				,"DATE_FORMAT(tlp.or_date,'%m%d%Y') AS or_date"										//or_date
				,"ml.loan_no AS loan_no"													
				,"ml.employee_id AS employee_id"		//employee id
				,"me.last_name AS last_name"			//payor last name
				,"me.first_name	AS first_name"			//payor first name
				,"mee.last_name AS employee_last_name"		//employee last name
				,"mee.first_name AS employee_first_name"	//employee first name
				,"me.company_code AS company_code"
				,"ml.loan_code AS loan_code"
				,"tlp.payor_id AS payor_id"
				,"rlh.loan_description AS loan_description"
				,"ml.principal_balance AS loan_balance"
				,"tlp.interest_amount AS interest_amount"
				,"tlp.amount AS principal_amount"
				,"DATE_FORMAT(tlp.payment_date,'%m%d%Y') AS payment_date"
				,"tlp.remarks AS remarks"
				,"tlp.transaction_code AS transaction_code"
				));
		/*foreach($data['list'] as $key => $val){
					$data['list'][$key]['payment_date'] = date("mdY", strtotime($val['payment_date']));
				}*/
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
		/*$_REQUEST['lp']['loan_no'] = '7683';
		$_REQUEST['lp']['employee_id'] = '00421526';
		$_REQUEST['lp']['payment_date']	= '04/21/2010';*/

		$params = array(
			'tlp.loan_no' => $_REQUEST['lp']['loan_no']
		,'tlp.payor_id' => $_REQUEST['lp']['employee_id']
		,'tlp.payment_date' => date("Ymd", strtotime($_REQUEST['lp']['payment_date']))
		);
		$_REQUEST['limit'] = 1;
		$data = $this->loanpayment_model->getLpDtl(
		$params
		,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
		,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
		,array('tlp.loan_no AS loan_no'
		,'tlp.payor_id AS payor_id'
		,'me.last_name AS last_name'
		,'me.first_name AS first_name'
		,'tlp.transaction_code AS transaction_code'
		,'rt.transaction_description AS transaction_description'
		,'tlp.payment_date AS payment_date'
		,'tlp.amount AS principal_amount'
		,'tlp.interest_amount AS interest_amount'
		,'tlp.remarks AS remarks'
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

		$params = array(
			'tlpd.loan_no' => $_REQUEST['lp']['loan_no']
		,'tlpd.payor_id' => $_REQUEST['lp']['employee_id']
		,'tlpd.payment_date' => date("Ymd", strtotime($_REQUEST['lp']['payment_date']))
		);

		$data = $this->loanpaymentdetail_model->getLpDetail(
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
				
			log_message('debug', "[START] Controller loan_payment:deleteDtl");
			log_message('debug', "data param exist?:".array_key_exists('data',$_REQUEST));

			if (array_key_exists('lp',$_REQUEST)) {
				$this->loanpayment_model->setValue('status_flag','0');
					
				$this->db->trans_start();
				$delHdrResult = $this->loanpayment_model->update(array(
				'loan_no' => $_REQUEST['lp']['loan_no']
				,'payment_date' => date("Ymd", strtotime($_REQUEST['lp']['payment_date']))
				,'transaction_code' => $_REQUEST['lp']['transaction_code']
				,'payor_id' => $_REQUEST['lp']['payor_id']
				,'status_flag' => '1'
				));
					
				if($delHdrResult['affected_rows'] <= 0){
					echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
				} else {
					$delDtlResult = $this->loanpaymentdetail_model->delete(array(
					'loan_no' => $_REQUEST['lp']['loan_no']
					,'payment_date' => date("Ymd", strtotime($_REQUEST['lp']['payment_date']))
					,'payor_id' => $_REQUEST['lp']['payor_id']
					));

					if($delDtlResult['error_code'] == 0)
					echo "{'success':true,'msg':'Data successfully deleted.'}";
					else
					echo '{"success":false,"msg":"'.$delDtlResult['error_message'].'"}';
				}
					
				$this->db->trans_complete();
					
					
			} else
			echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
				
			log_message('debug', "[END] Controller loan_payment:deleteDtl");
	}

	/**
	 * @desc Update loan payment.
	 */
	function update(){
			$_REQUEST['user'] = "ADMIN";

			log_message('debug', "[START] Controller loan_payment:updateLoanPayment");
			log_message('debug', "lp param exist?:".array_key_exists('lp',$_REQUEST));

			if (array_key_exists('lp',$_REQUEST)) {
				if(!$this->member_model->employeeExists($_REQUEST['employee_id'])){
					echo("{'success':false,'msg':'Employee does not exist','error_code':'22'}");
				}
				else if(!$this->member_model->employeeExists($_REQUEST['lp']['payor_id'])){
					echo("{'success':false,'msg':'Payor does not exist','error_code':'42'}");
				}
				else if($this->member_model->employeeIsInactive($_REQUEST['lp']['payor_id'])){
					echo("{'success':false,'msg':'Payor is inactive','error_code':'56'}");
				}
				else if(!$this->loancodeheader_model->loanCodeExists($_REQUEST['loan_code'])){
					echo("{'success':false,'msg':'Loan code does not exist','error_code':'49'}");
				}	
				else if(!$this->loancodepaymenttype_model->loanCodePaymentTypeExists($_REQUEST['lp']['transaction_code'])){
					echo("{'success':false,'msg':'Loan code payment type does not exist','error_code':'48'}");	
				}
				else if(!$this->checkSufficientFunds()){
					echo("{'success':false,'msg':'Member does not have sufficient funds for this transaction.','error_code':'48'}");	
				}
				else{
					$this->loanpayment_model->setValue('amount',$_REQUEST['lp']['amount']);
					$this->loanpayment_model->setValue('interest_amount',$_REQUEST['lp']['interest_amount']);
					$this->loanpayment_model->setValue('remarks',$_REQUEST['lp']['remarks']);
					$this->loanpayment_model->setValue('source', 'U');
					$this->loanpayment_model->setValue('balance', $this->computeBalance($_REQUEST['lp']['loan_no'], $_REQUEST['lp']['amount']));
					$this->loanpayment_model->setValue('modified_by', $_REQUEST['user']);
					$this->loanpayment_model->setValue('status_flag', '1');
						
					$this->db->trans_start();
					$result = $this->loanpayment_model->update(array(
					'loan_no' => $_REQUEST['lp']['loan_no']
					,'payment_date' => date("Ymd", strtotime($_REQUEST['lp']['payment_date']))
					,'transaction_code' => $_REQUEST['lp']['transaction_code']
					,'payor_id' => $_REQUEST['lp']['payor_id']
					));
						
					if($result['affected_rows']>=0){
						$this->loanpaymentdetail_model->delete(array(
						'loan_no' => $_REQUEST['lp']['loan_no']
						,'payment_date' => date("Ymd", strtotime($_REQUEST['lp']['payment_date']))
						,'payor_id' => $_REQUEST['lp']['payor_id']
						));
	
						$this->addCharges();
					}
					$this->db->trans_complete();
						
					if($result['affected_rows'] >= 0 && $this->db->trans_status() === TRUE){
						echo "{'success':true,'msg':'Data successfully saved.'}";
					} else
					echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
				}
			} else
				echo "{'success':false,'msg':'Data was NOT successfully saved.'}";

			log_message('debug', "[END] Controller loan_payment:updateLoanPayment");
	}

	/**
	 * @desc Inserts new loan payment table
	 */
	function add(){
			/*$_REQUEST['user'] = "PECA";*/
			
			log_message('debug', "[START] Controller loan_payment:addLoanPayment");
			log_message('debug', "lp param exist?:".array_key_exists('lp',$_REQUEST));
			
			/*if($this->withOR($_REQUEST['lp']['transaction_code'])){
				$or_no = $this->parameter_model->incParam('LASTORNO');
				$or_date = $this->parameter_model->getParam('CURRDATE');
			}
			else{
				$or_no = "";
				$or_date = "";
			}*/
			$payment_date = date("Ymd", strtotime($_REQUEST['lp']['payment_date']));
			
			if (array_key_exists('lp',$_REQUEST)) {
				if($this->member_model->employeeExists($_REQUEST['employee_id']) == 0){
					$result['error_message'] = 'Employee does not exist';
					$result['error_code'] = 22;
				}
				else if($this->member_model->employeeExists($_REQUEST['lp']['payor_id']) == 0){
					$result['error_message'] = 'Payor does not exist';
					$result['error_code'] = 42;
				}
				else if($this->member_model->employeeIsInactive($_REQUEST['lp']['payor_id'])){
					$result['error_message'] = 'Payor is inactive';
					$result['error_code'] = 56;
				}
				else if(!$this->loancodeheader_model->loanCodeExists($_REQUEST['loan_code'])){
					$result['error_message'] = 'Loan code does not exist';
					$result['error_code'] = 49;
				}	
				else if(!$this->loancodepaymenttype_model->loanCodePaymentTypeExists($_REQUEST['lp']['transaction_code'])){
					$result['error_message'] = 'Loan code payment type does not exist';
					$result['error_code'] = 48;
				}	
				else if(!$this->checkLoanApplyer($_REQUEST['lp']['loan_no'],$_REQUEST['employee_id'])){
						$result['error_code'] = 41;
						$result['error_message'] = 'Specified employee is not the applicant of the loan.';
					}
				else if(!$this->hasValidLoanPayor()){
					$result['error_message'] = 'Invalid loan payor.';
					$result['error_code'] = 53;
				}
				else if(!$this->checkSufficientFunds()){
					$result['error_message'] = 'Member does not have sufficient funds for this transaction.';
					$result['error_code'] = 53;
				}
				else{
					unset($_REQUEST['lp']['or_no']);
					unset($_REQUEST['lp']['or_date']);
					$this->loanpayment_model->populate($_REQUEST['lp']);
					$this->loanpayment_model->setValue('balance', $this->computeBalance($_REQUEST['lp']['loan_no'], $_REQUEST['lp']['amount']));
					/*$this->loanpayment_model->setValue('or_no', $or_no );
					$this->loanpayment_model->setValue('or_date', $or_date);*/
					$this->loanpayment_model->setValue('source', 'U');
					$this->loanpayment_model->setValue('status_flag', '1');
					$this->loanpayment_model->setValue('payment_date', $payment_date);
						
					$checkDuplicate = $this->loanpayment_model->checkDuplicateKeyEntry(array(
					'loan_no' => $_REQUEST['lp']['loan_no']
					,'payment_date' => $payment_date
					,'transaction_code' => $_REQUEST['lp']['transaction_code']
					,'payor_id' => $_REQUEST['lp']['payor_id']
					));
	
					if($checkDuplicate['error_code'] == 1){
						$result['error_code'] = 2;
						$result['error_message'] = $checkDuplicate['error_message'];
					}
					else{
						$this->db->trans_start();
						$this->loanpayment_model->insert();
						$this->addCharges();
						$this->db->trans_complete();
	
						if ($this->db->trans_status() === FALSE){
							$result['error_code'] = 1;
							$query = $this->db->last_query();
							log_message('debug', "query:  ".$query);
							$result['error_message'] = "Data was NOT successfully saved.";
						}
						else{
							$result['error_code'] = 0;
						}
					}
				}
				if($result['error_code'] == 0){
					//echo "{'success':true,'msg':'Data successfully saved.','or_no':'$or_no','or_date':'$or_date'}";
					echo "{'success':true,'msg':'Data successfully saved.'}";
				} else
				echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';
			} else
			echo "{'success':false,'msg':'Data was NOT successfully saved.','error_code':'2'}";
				
			log_message('debug', "[END] Controller loan_payment:addLoanPayment");
	}
	
	function hasValidLoanPayor(){
		$loan_no = $_REQUEST['lp']['loan_no'];
		$payor_id = $_REQUEST['lp']['payor_id'];
		
		$data = $this->mloan_model->get(array('loan_no' => $loan_no), array('employee_id'));
		foreach($data['list'] as $val){
			$valid_payors[] = $val['employee_id'];
		}
		
		$data = $this->mloanguarantor_model->get(array('loan_no' => $loan_no), array('guarantor_id'));
		foreach($data['list'] as $val){
			$valid_payors[] = $val['guarantor_id'];
		}
		
		if((count($valid_payors)>0) && in_array($payor_id, $valid_payors))
			return 1;
		else
			return 0;
	
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
	 * @desc Adds new loan type charges in t_loan_payment_detail table
	 */
	function addCharges(){
		log_message('debug', "[START] Controller loan_payment:addCharges");
		log_message('debug', "lp param exist?:".array_key_exists('lp',$_REQUEST));

		$data = $this->readTransactionCharges($_REQUEST['lp']['transaction_code']);
			
		foreach($data['list'] as $key => $params){
			$formula = str_replace('lpa', $_REQUEST['lp']['amount'], $params['formula']);
			eval("\$params['amount'] = $formula;");
			unset($params['formula']);
			$params['loan_no'] = $_REQUEST['lp']['loan_no'];
			$params['payment_date'] = $_REQUEST['lp']['payment_date'];
			$params['payor_id'] = $_REQUEST['lp']['payor_id'];
			$params['created_by'] = $_REQUEST['user'];
				
			$this->loanpaymentdetail_model->populate($params);
			$this->loanpaymentdetail_model->setValue('status_flag', '1');
			$this->loanpaymentdetail_model->insert();
		}

		log_message('debug', "[END] Controller loan_payment:addCharges");
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

		/*		$_REQUEST['lp']['empId'] = '123';
		 $_REQUEST['lp']['empFname'] = 'joseph';
		 $_REQUEST['lp']['empLname'] = 'juban';
		 */
		if(isset($_REQUEST['lp']['loan_no'])&& $_REQUEST['lp']['loan_no']!="")
		$param['ml.loan_no LIKE'] = $_REQUEST['lp']['loan_no'];
		
		if(isset($_REQUEST['employee_id'])&& $_REQUEST['employee_id']!=""){
			$param['me.employee_id LIKE'] = $_REQUEST['employee_id'].'%';
		}
		else{
			if(isset($_REQUEST['first_name'])&& $_REQUEST['first_name']!="")
				$param['me.first_name LIKE'] = $_REQUEST['first_name'].'%';
			if(isset($_REQUEST['last_name'])&& $_REQUEST['last_name']!="")
				$param['me.last_name LIKE'] = $_REQUEST['last_name'].'%';
		}
		
		$param['ml.status_flag'] = '2';
		$param['me.member_status'] = 'A';
		$param['ml.close_flag'] = '0';

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
	 * @desc To to retrieve all loans of an employee
	 */
	function readEmployeeLoanList(){
		if (array_key_exists('loan_no', $_REQUEST) && $_REQUEST['loan_no']!= ''){
				$param['ml.loan_no LIKE'] = $_REQUEST['loan_no'].'%';
			}
			
		$param['ml.employee_id'] = $_REQUEST['employee_id'];
		$param['ml.status_flag'] = '2';
		$param['me.member_status'] = 'A';
		$param['ml.close_flag'] = '0';

		$data = $this->mloan_model->getLoanList(
		$param
		,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
		,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
		,array("ml.loan_no AS loan_no"
		,"ml.loan_code AS loan_code"
		,"rl.loan_description AS loan_description"
		,"rl.bsp_computation AS bsp_computation"
		,"DATE_FORMAT(ml.loan_date, '%m%d%Y') AS loan_date"
		,"ml.principal_balance AS loan_balance"
		,"ml.employee_interest_amortization AS employee_interest_amortization"
		,"ml.employee_principal_amort AS employee_principal_amortization"
		//[START] 7th Enhancement
		,"ml.interest_rate as interest_rate"
		//[END] 7th Enhancement
		,"me.employee_id AS employee_id"
		,"me.last_name AS last_name"
		,"me.first_name AS first_name"
		,"me.company_code AS company_code"
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
		if (array_key_exists('loan_no', $_REQUEST) && $_REQUEST['loan_no']!= ''){
				$param['ml.loan_no LIKE'] = $_REQUEST['loan_no'].'%';
			}	
		$param['ml.status_flag'] = '2';
		$param['me.member_status'] = 'A';
		$param['close_flag'] = '0';
		
		$data = $this->mloan_model->getLoanList(
		$param
		,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
		,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
		,array("ml.loan_no AS loan_no"
		,"ml.loan_code AS loan_code"
		,"rl.loan_description AS loan_description"
		,"rl.bsp_computation AS bsp_computation"
		,"DATE_FORMAT(ml.loan_date, '%m%d%Y') AS loan_date"
		,"ml.principal_balance AS loan_balance"
		,"ml.employee_interest_amortization AS employee_interest_amortization"
		,"ml.employee_principal_amort AS employee_principal_amortization"
		,"me.employee_id AS employee_id"
		,"me.last_name AS last_name"
		,"me.first_name AS first_name"
		,"me.company_code AS company_code"
		//[START] 7th Enhancement
		,"ml.interest_rate as interest_rate"
		//[END] 7th Enhancement
		)
		,"me.last_name, me.first_name, ml.loan_no"
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
		
		if(isset($_REQUEST['lp']['loan_no'])&& $_REQUEST['lp']['loan_no']!="")
			$param['ml.loan_no LIKE'] = $_REQUEST['lp']['loan_no'];
			
		$param['ml.status_flag'] = '2';
		$param['me.member_status'] = 'A';
		$param['ml.close_flag'] = '0';

		$data1 = $this->mloan_model->getLoanList(
		$param
		,null
		,null
		,array('ml.employee_id AS payor_id'
		,'me.first_name first_name'
		,'me.last_name AS last_name'
		)
		,null
		,'distinct'
		);
		
		
		$param = array('mlg.loan_no' => $_REQUEST['lp']['loan_no']);
		$data2 = $this->mloanguarantor_model->getGuarantor(
		$param
		,null
		,null
		,array('mlg.guarantor_id AS payor_id'
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
		//Removed by Joseph on 04-07-2011, peca wants all payment types displayed
		//$data = $this->mloan_model->get(array('loan_no'=>$_REQUEST['lp']['loan_no'])
		//			,'loan_code');
		
		//$param['lcpt.loan_code'] = $data['list'][0]['loan_code'];
		$param['lcpt.status_flag'] = '1';
		$param['rt.status_flag'] = '1';
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
		$_REQUEST['lp']['transCode'] = 'ABMC';

		$param['rtc.transaction_code'] = $_REQUEST['lp']['transCode'];
		$data = $this->transactioncharges_model->getTransChargeList(
			$param
			,null
			,null
			,array('rtc.charge_code'
				,'rt.transaction_description'							
				,'rtc.charge_formula as amount')
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
		$data = $this->mloan_model->get(array('loan_no' => $loan_no),
		array('principal_balance'));
		if(!isset($data['list'][0]['principal_balance'])|| $data['list'][0]['principal_balance']=="")
			return 0;
		else
			return ($data['list'][0]['principal_balance'] - $principal_amount);
	}
	
	function withOR($transaction_code){
		$data = $this->transactioncode_model->get(array('transaction_code'=>$transaction_code)
		,array('with_or'));
		
		if($data['count']>0){
			if($data['list'][0]['with_or']=='Y')
				return true;
		}
		return false;
	}
	
	function withORWithEcho(){
		$transaction_code = $_REQUEST['transaction_code'];
		$data = $this->transactioncode_model->get(array('transaction_code'=>$transaction_code)
		,array('with_or'));
		
		if($data['count']>0){
			if($data['list'][0]['with_or']=='Y'){
				echo "{'hasOR': true}";
			}
			else{
				echo "{'hasOR': false}";
			}
		}
		else{
			echo "{'hasOR': false}";
		}
	}
	
	function checkSufficientFunds(){
		$loan_code = $_REQUEST['loan_code'];
		$payment_code = $_REQUEST['lp']['transaction_code'];;
		$payor_id = $_REQUEST['lp']['payor_id'];
		$payment_amount = $_REQUEST['lp']['amount'];;
		$interest_amount = $_REQUEST['lp']['interest_amount'];;
		$total_amount = $payment_amount + $interest_amount;
		$acctng_period = $this->parameter_model->retrieveValue("ACCPERIOD");
		
		//check if cross charge
		if($payment_code == $this->parameter_model->retrieveValue($loan_code."CC")){
			//Modified for 8th Enhancement - Part 2 (Loan Payments_CC) Eddie Amoto 2013/10/10
			$balanceInfo = $this->mloan_model->getEmployeeBalanceInfo($payor_id, $acctng_period);
			$maxWdwlAmount = $balanceInfo['maxWdwlAmount'];
			if($maxWdwlAmount < $total_amount){
				//insufficient withdrawable amount
				return false;
			}
			else{
				return true;
			}
		}
		else{
			return true;
		}
	}

}

/* End of file loan_payment.php */
/* Location: ./CodeIgniter/application/controllers/loan_payment.php */