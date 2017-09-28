<?php

/* Location: ./CodeIgniter/application/controllers/loan.php */
class Loan extends Asi_Controller {

	function Loan()
	{
		parent::Asi_Controller();
		$this->load->model('tloan_model');
		$this->load->model('mloan_model');
		$this->load->model('loancharges_model');
		$this->load->model('loanguarantor_model');
		$this->load->model('member_model');
		$this->load->model('loancodeheader_model');
		$this->load->model('loancodedetail_model');
		$this->load->model('transactioncode_model');
		$this->load->model('transactioncharges_model');
		$this->load->model('parameter_model');
		$this->load->model('capitalcontribution_model');
		$this->load->model('supplier_model');
		$this->load->model('Loan_model');
		$this->load->model('mloanguarantor_model');
		$this->load->model('tblnpmlsuspension_model');
		//20110805 fix for unsync co-maker admin and co-maker online 
		$this->load->model('mcloanguarantor_model');
		$this->load->helper('url');
		$this->load->library('constants');
		require_once(BASEPATH.'application/my_classes/Classes/PHPExcel/Calculation/Functions.php');
	}
	
	function index() {
		
	}
	
	/**
	 * @desc To retrieve all Loan Transactions that are not yet posted
	 * @return array
	 */
	function readLoan() 
	{
		$params = array('tl.status_flag' => '1');	
		if (array_key_exists('loan_no',$_REQUEST)){
			$params['loan_no like'] = $_REQUEST['loan_no'].'%';
		}
		
		$data = $this->tloan_model->getLoanList(
			$params,
			array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
			array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
			array('tl.loan_no AS loan_no'
					,'tl.employee_id AS employee_id'
					,'me.last_name AS last_name'
					,'me.first_name AS first_name'
					,'tl.loan_date AS loan_date'
					,'rl.loan_description AS loan_description')
				,'tl.loan_date DESC'
		);

		foreach($data['list'] as $key => $val){
			$data['list'][$key]['loan_date'] = date("mdY", strtotime($val['loan_date']));
		}

		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
			));
	}
	
	/**
	 * @desc Retrieves Service charges of the specified loan type
	 * @return array
	 */
	function readCharges() 
	{	
		$param = array('tlc.loan_no' => $_REQUEST['loan_no']
			,'tlc.status_flag' => '1'
			,'rt.transaction_group' => 'SC'
		);		
		$data = $this->loancharges_model->getServiceCharges(
			 $param
			,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
			,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
			,array('tlc.loan_no AS loan_no'
				  ,'tlc.transaction_code AS transaction_code'
				  ,'rt.transaction_description AS transaction_description'
				  ,'tlc.amount AS amount')
		);
		
		foreach($data['list'] as $row => $value){
			$id = $value['loan_no'] . ':' . $value['transaction_code'];
			$data['list'][$row]['id'] = $id;
		}

		echo json_encode(array(
				'success' => true,
				'data' => $data['list'],
				'total' => $data['count'],
				'total' => $data['count'],
				'query' => $data['query']
				));
	}
	
	/**
	 * @desc Retrieve Information of a single loan transaction
	 * @return array
	 */
	function showLoan()
	{   
		//Update the transaction date to currdate when loading the details of capcon withdrawal/deposit - requested by peca 20110616
		$currDate = date("Ymd", strtotime($this->parameter_model->getParam('CURRDATE')));
		$this->tloan_model->changeTransactionDateToCurrDate($_REQUEST['loan_no'], $currDate);
	
		$_REQUEST['filter'] = array(
									'tl.loan_no' => $_REQUEST['loan_no']
								   ,'tl.status_flag' => '1'
							  );
		$data = $this->Loan_model->getLoanList(
			 array_key_exists('filter',$_REQUEST) ? $_REQUEST['filter'] : null
			,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
			,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
			,array('loan_no'
					,'tl.restructure_no'					
					,'tl.restructure_amount'					
					,'tl.employee_id'					
					,'me.last_name'					
					,'me.first_name'					
					,'me.middle_name'					
					,'tl.loan_code'					
					,'tl.loan_date'					
					,'tl.principal'					
					,'tl.term'					
					,'tl.interest_rate'					
					,'tl.initial_interest'					
					,'tl.employee_interest_total'					
					,'tl.employee_interest_amortization'					
					,'tl.employee_interest_vat_rate'					
					,'tl.employee_interest_vat_amount'					
					,'tl.company_interest_rate'					
					,'tl.company_interest_total'					
					,'tl.company_interest_amort'					
					,'tl.amortization_startdate'					
					,'tl.employee_principal_amort'					
					,'tl.down_payment_percentage'					
					,'tl.down_payment_amount'					
					,'tl.loan_proceeds'					
					,'tl.estate_value'					
					,'tl.mri_fip_amount'					
					,'tl.broker_fee_amount'					
					,'tl.government_fee_amount'					
					,'tl.other_fee_amount'					
					,'tl.service_fee_amount'					
					,'tl.pension'					
					,'tl.bank_transfer'					
					,'tl.principal_balance'					
					,'tl.interest_balance'					
					,'tl.cash_payments'					
					,'tl.capital_contribution_balance'					
					,'tl.insurance_broker'				
					,'tl.appraiser_broker'					
					,'tl.check_no'
					##### NRB EDIT START #####
					,'tl.effective_annual_interest_rate'
					,'tl.effective_monthly_interest_rate'
					,'tl.mri_fip_provider'
					,'tl.other_charges_amount'
					,'tl.other_charges_rate'
					##### NRB EDIT END #####
					)
		);
		
		foreach($data['list'] as $key => $val){
			$data['list'][$key]['loan_date'] = date("mdY", strtotime($val['loan_date']));
			$data['list'][$key]['amortization_startdate'] = date("mdY", strtotime($val['amortization_startdate']));
		}
		
		if(count($data['list'])>0){
			$years_of_service = $this->member_model->getEmpYearsOfService($data['list'][0]['employee_id']);	
				
			$loanDtl = $this->loancodeheader_model->retrieveLoanCodes(
			array('RH.status_flag' => '1', 'RH.loan_code' => $data['list'][0]['loan_code'])
			,null
			,null
			,array("COALESCE(RL.pension,'N') as pension_cb", "COALESCE(RL.guarantor, 0) as comaker_cb")
			,null
			,$years_of_service
			);
			
			if(count($loanDtl['list'])>0){
				$data['list'][0]['pension_cb'] = $loanDtl['list'][0]['pension_cb'];
				if($loanDtl['list'][0]['comaker_cb']==0){
					$data['list'][0]['comaker_cb'] = 'Y';
				}
				else{
					$data['list'][0]['comaker_cb'] = 'N';
				}
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
	 * @desc Retrieve charges for selected loan transaction, retrieved from t_loan_charges table
	 * @return array
	 */
	function showCharges()
	{
		$_REQUEST['filter'] = array(
									'loan_no' => $_REQUEST['loan_no']
									,'status_flag' => '1'
							  );
		
		$data = $this->loancharges_model->get_list(
			array_key_exists('filter',$_REQUEST) ? $_REQUEST['filter'] : null
			,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
			,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
			,array('loan_no'
					,'transaction_code'
					,'amount')
		);

		foreach($data['list'] as $row => $value){
			$id = $value['loan_no'] . ':' . $value['transaction_code'];
			$data['list'][$row]['id'] = $id;
		}
		
		echo json_encode(array(
				'success' => true,
				'data' => $data['list'],
				'total' => $data['count'],
				'query' => $data['query']
				));

	}
	
	/**
	 * @desc Retrieve guarantors for selected loan transaction from t_loan_guarantor table
	 * @return array
	 */
	function readComaker() 
	{
		//$_REQUEST['loan_no'] = '';
		//$_REQUEST['user'] = 'PECA';
		$_REQUEST['filter'] = array(
			'loan_no' => $_REQUEST['loan_no']
			,'tlg.status_flag' => '1'
			);
		
		$data = $this->loanguarantor_model->getGuarantor(
		array_key_exists('filter',$_REQUEST) ? $_REQUEST['filter'] : null,
		array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
		array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
		array('tlg.loan_no AS loan_no'
				,'tlg.guarantor_id AS employee_id'
				,'me.last_name AS last_name'
				,'me.first_name AS first_name')
		);
		
		foreach($data['list'] as $row => $value){
			$id = $value['loan_no'] . ':' . $value['employee_id'];
			$data['list'][$row]['id'] = $id;
		}

		echo json_encode(array(
				'success' => true,
				'data' => $data['list'],
				'total' => $data['count'],
				'query' => $data['query']
				));

	}
	
	/**
	 * @desc Checks if employee has an entry in the capcon table for the specified acctg period
	 * @return 0 - has capcon entry for a specified acctg period, 1 - otherwise
	 */
	function checkCapconEntry($employee_id, $acctgPeriod){
		$data = $this->capitalcontribution_model->get(array(
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
	function addCapconEntry($employee_id, $acctgPeriod){
		$this->capitalcontribution_model->populate(array(
		'employee_id'=>$employee_id
		,'accounting_period' => $acctgPeriod
		,'beginning_balance' => 0
		,'ending_balance' => 0
		,'minimum_balance' => 0
		,'maximum_balance' => 0
		,'status_flag' => '1'
		,'created_by' => $_REQUEST['newloan']['created_by']
		));
			
		$this->capitalcontribution_model->insert();
	}
	
	/**
	 * @desc Retrieves list of loans of an employee that can be restructured
	 * @return array
	 */
	function showRestructuredLoans() 
	{	
		$_REQUEST['filter'] = array('tl.employee_id' => $_REQUEST['employee_id']
								,'tl.loan_code' => $_REQUEST['loan_code']
								,'rl.restructure' => 'Y'
								,'tl.principal_balance >'=>0
								,'tl.status_flag' => '2');	
	
		$data = $this->mloan_model->getRestructuredLoans(
				array_key_exists('filter',$_REQUEST) ? $_REQUEST['filter'] : null,
				array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
				array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
				array('tl.loan_no AS loan_no'								
						,'tl.principal_balance AS balance'							
						,'tl.loan_date AS loan_date'							
						,'tl.principal AS principal'							
						,'tl.term AS term'							
						,'tl.interest_rate AS interest_rate')
					 ,'tl.loan_no ASC'	
		);
		
		foreach($data['list'] as $key => $val){
			$data['list'][$key]['loan_date'] = date("mdY", strtotime($val['loan_date']));
		}
		
		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
			));

	}
	
	/**
	 * @desc Inserts new Loan Transaction
	 * @param Array
	 */
	function addLoan()
	{		
		log_message('debug', "[START] Controller loan:addLoan");
		log_message('debug', "newloan param exist?:".array_key_exists('newloan',$_REQUEST));
		
		if (array_key_exists('newloan',$_REQUEST)){
			$loan_code = $this->loancodeheader_model->loanCodeExists($_REQUEST['newloan']['loan_code']);
			
			##### NRB EDIT START #####
			if (isset($_REQUEST['newloan']['insurance_broker']) && ($_REQUEST['newloan']['insurance_broker']!='' || $_REQUEST['newloan']['insurance_broker']!=null)){
			/* if ($_REQUEST['newloan']['insurance_broker']!='' || $_REQUEST['newloan']['insurance_broker']!=null){ */
			##### NRB EDIT START #####
				$ins_broker = $this->supplier_model->supplierIdExists($_REQUEST['newloan']['insurance_broker']);
			}
			else {
				$ins_broker = 2;
			}
			
			if ($_REQUEST['newloan']['appraiser_broker']!='' || $_REQUEST['newloan']['appraiser_broker']!=null){
				$app_broker = $this->supplier_model->supplierIdExists($_REQUEST['newloan']['appraiser_broker']);
			}
			else {
				$app_broker = 2;
			}
			
			if ($loan_code==0 && $ins_broker==0 && $app_broker==0){
				echo "{'success':false,'msg':'Loan code, Appraiser, and Insurance Broker do not exist.'}";
			}
			else if ($loan_code==0 && $ins_broker==0 && $app_broker==1){
				echo "{'success':false,'msg':'Loan code and Insurance Broker do not exist.'}";
			}
			else if ($loan_code==0 && $ins_broker==1 && $app_broker==0){
				echo "{'success':false,'msg':'Loan code and Appraiser do not exist.'}";
			}
			else if ($loan_code==0 && $ins_broker==1 && $app_broker==1){
				echo "{'success':false,'msg':'Loan code does not exist.'}";
			}
			else if ($loan_code==1 && $ins_broker==0 && $app_broker==0){
				echo "{'success':false,'msg':'Appraiser and Insurance Broker do not exist.'}";
			}
			else if ($loan_code==1 && $ins_broker==0 && $app_broker==1){
				echo "{'success':false,'msg':'Insurance Broker does not exist.'}";
			}
			else if ($loan_code==1 && $ins_broker==1 && $app_broker==0){
				echo "{'success':false,'msg':'Appraiser does not exist.'}";
			}
			else {
				$loan_no = $this->getParam('LASTLOANNO')+1;	
				$employee_id = $_REQUEST['newloan']['employee_id'];
				$principal = $_REQUEST['newloan']['principal'];
				$term = $_REQUEST['newloan']['term'];
				$loan_code = $_REQUEST['newloan']['loan_code'];
				$loan_date = $_REQUEST['newloan']['loan_date']; 
				$acctgPeriod = date("Ymd", strtotime($this->parameter_model->getParam("ACCPERIOD")));
				$restructure_amount = isset($_REQUEST['newloan']['restructure_amount']) ? $_REQUEST['newloan']['restructure_amount']: 0;
				$data = $this->getRLoanHdr($loan_code);
				
				if($this->checkCapconEntry($employee_id, $acctgPeriod)){
				$this->addCapconEntry($employee_id, $acctgPeriod);
				}
				
				if ($this->member_model->employeeExists($employee_id)==0) {
					echo("{'success':false,'msg':'Employee does not exist','error_code':'22'}");
				}
				else if($this->member_model->employeeIsInactive($employee_id)){
					echo("{'success':false,'msg':'Employee is inactive','error_code':'55'}");
				}
				else if ($this->checkMaximumLoanAmount($principal, $data[0]['max_loan_amount'])>0) {
					echo("{'success':false,'msg':'Principal Amount exceeds the maximum loan amount','error_code':'13'}");	
				}
				else if ($this->checkMinMaxTerms($term, $data[0]['min_term'], $data[0]['max_term'])>0){
					echo("{'success':false,'msg':
						'Term should be between ".$data[0]['min_term']." and ".$data[0]['max_term']."','error_code':'14'}");	
				}
				else if ($this->checkSuspensionDate($employee_id)>0){
					echo("{'success':false,'msg':'You are still on suspension','error_code':'9'}");	
				}
				else if ($this->checkRetiree($employee_id, $principal)>0) {
					echo("{'success':false,'msg':'The employee is a retiree. Thus he cannot loan more than 70% of his capital contribution.','error_code':'11'}");	
				}
				else if ($this->checkCapitalContribution($principal, $loan_code, $employee_id, $restructure_amount)>0){
					$result = $this->checkCapitalContribution($principal, $loan_code, $employee_id, $restructure_amount);
					
					if ($this->checkRetiree($employee_id, $principal)>0) {
						echo("{'success':false,'msg':'The employee is a retiree. Thus he cannot loan more than 70% of his capital contribution.','error_code':'11'}");
						return;
					}			
					if ($result==2) {
						echo("{'success':false,'msg':'Capital Contribution Balance after transaction is less than the Capital Contribution Minimum Balance','error_code':'37'}");
					}
					else {
						echo("{'success':false,'msg':'Capital Contribution Balance is below 1/3 of Loan Principal Amount','error_code':'16'}");
					}				
				}
				else if ($this->checkYearsOfService($employee_id, $loan_code)>0){
					echo("{'success':false,'msg':'The employee has not rendered enough no of years of service for the applied loan','error_code':'23'}");	
				}
				else if($this->hasValidGuarantors($employee_id)){
					echo("{'success':false,'msg':'Employee has invalid co-makers','error_code':'8'}");	
				}
				/*else if ($this->checkRetiree($employee_id, $principal)>0) {
					echo("{'success':false,'msg':'The employee is a retiree. Thus he cannot loan more than 50% of his capital contribution.','error_code':'24'}");	
				}*/
				else {
					$interest_rate = $data[0]['employee_interest_percentage'];
					$employee_interest_total = $this->computeInterestRateAmount($interest_rate, $principal, $term);
					
					if ($data[0]['interest_earned']=="Y") 
						$initial_interest = $this->computeInitialInterestAmount($interest_rate,$principal);
					else 
						$initial_interest = 0;
			
					if ($data[0]['unearned_interest']=="Y") 
						$employee_interest_amortization = 0;
					else 
						$employee_interest_amortization = $this->computeAmortizedInterest($interest_rate, $principal, $term);
					
					$company_interest_rate = $data[0]['company_share_percentage'];
					if ($company_interest_rate==NULL) {
						$company_interest_rate = 0;
						$company_interest_rate_total = 0;
						$company_interest_amort = 0;
					}
					else {
						$company_interest_rate_total = $this->computeCompanyInterestRateAmount($company_interest_rate, $principal);	
						$company_interest_amort = $this->computeCompanyAmortizedInterest($company_interest_rate, $principal);
					}
					
					/***Retrieve capital contribution balance***/
					$years_of_service = $this->member_model->getEmpYearsOfService($employee_id);	
			
					$loanDtl = $this->loancodeheader_model->retrieveLoanCodes(
					array('RH.status_flag' => '1', 'RH.loan_code' => $loan_code)
					,null
					,null
					,array("RL.capital_contribution AS capital_contribution")
					,null
					,$years_of_service
					);
					
					if($loanDtl['count']>0 && $loanDtl['list'][0]['capital_contribution']!=''){
						$capital_contribution = $loanDtl['list'][0]['capital_contribution'] * $principal;
					}
					else{
						$capital_contribution = 0;
					}
					/***Retrieve capital contribution balance***/
					
					$employee_principal_amort = $this->computePrincipalAmortization($principal, $term);
					//$loan_proceeds = $this->computeLoanProceeds($principal, $interest_rate, $data[0]['unearned_interest'], $data[0]['interest_earned'], $term);
		
					$service_fee_amount = $this->computeServiceFee($loan_code, $principal);
					
					if($_REQUEST['newloan']['mri_fip_amount']==""){
						$_REQUEST['newloan']['mri_fip_amount'] = 0;
					}
					if($_REQUEST['newloan']['broker_fee_amount']==""){
						$_REQUEST['newloan']['broker_fee_amount'] = 0;
					}
					if($_REQUEST['newloan']['government_fee_amount']==""){
						$_REQUEST['newloan']['government_fee_amount'] = 0;
					}
					if($_REQUEST['newloan']['other_fee_amount']==""){
						$_REQUEST['newloan']['other_fee_amount'] = 0;
					}
					
					$this->tloan_model->populate($_REQUEST['newloan']);
					$this->tloan_model->setValue('status_flag', '1');
					$this->tloan_model->setValue('loan_no', $loan_no);
					$this->tloan_model->setValue('interest_rate', $interest_rate);
					//$this->tloan_model->setValue('initial_interest', $initial_interest);
					$this->tloan_model->setValue('employee_interest_total', $employee_interest_total);
					//$this->tloan_model->setValue('employee_interest_amortization', $employee_interest_amortization);
					$this->tloan_model->setValue('company_interest_rate', $company_interest_rate);
					$this->tloan_model->setValue('company_interest_total', $company_interest_rate_total);
					##### NRB EDIT START #####
					$this->tloan_model->setValue('company_interest_amort', round($company_interest_amort));
					/* $this->tloan_model->setValue('company_interest_amort', $company_interest_amort); */
					##### NRB EDIT END #####
					//$this->tloan_model->setValue('employee_principal_amort', $employee_principal_amort);
					//[start] modified by asi466 on google doc issue#13
					$this->tloan_model->setValue('loan_proceeds', $_REQUEST['newloan']['loan_proceeds'] - round($service_fee_amount,2));
					$this->tloan_model->setValue('service_fee_amount', round($service_fee_amount,2));
					//[end] modified by asi466 on google doc issue#13
					$this->tloan_model->setValue('principal_balance', $principal);
					$this->tloan_model->setValue('interest_balance', $employee_interest_total);
					$this->tloan_model->setValue('capital_contribution_balance', $capital_contribution);
					
					##### NRB EDIT START #####
					#recalculate other charges					
					log_message('debug','recalculate other charges');
					$f_other_charges_amount = $service_fee_amount + $_REQUEST['newloan']['initial_interest'];
					$f_other_charges_rate = ($f_other_charges_amount / $_REQUEST['newloan']['principal']) * 100;
					$this->tloan_model->setValue('other_charges_amount', $f_other_charges_amount);
					$this->tloan_model->setValue('other_charges_rate', $f_other_charges_rate);
					
					#recalculate MIR/EIR
					log_message('debug','Recalculate MIR/EIR');
					$a_eir = $this->_eir($interest_rate, $principal, $service_fee_amount, $term, $_REQUEST['newloan']['loan_code'], $_REQUEST['newloan']['initial_interest']);
					$this->tloan_model->setValue('effective_annual_interest_rate', $a_eir['f_eir']);
					$this->tloan_model->setValue('effective_monthly_interest_rate', $a_eir['f_mir']);
					##### NRB EDIT END #####
					
					$checkDuplicate = $this->tloan_model->checkDuplicateKeyEntry();
					$result['error_code'] = 0;
					if($checkDuplicate['error_code'] == 1){
						$result['error_code'] = ' ';
						$result['error_message'] = $checkDuplicate['error_message'];
						echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';
					}
					else{
						$this->db->trans_begin();
						$addLoanResult = $this->tloan_model->insert();
						//update LASTLOANNO in i_parameter_list table
						$this->updateParam('LASTLOANNO', $loan_no);
						
						$addChargeResult = $this->addDefaultCharges($_REQUEST['newloan']);
						$addComakerResult = $this->addComakerForAdd($loan_no);
					 		
					
						if ($this->db->trans_status() === TRUE && $addComakerResult['error_code']==0 && $result['error_code']==0){
							 $this->db->trans_commit();
							 echo "{'success':true,'msg':'Data successfully saved.','loan_no':'". $loan_no ."','service_fee_amount':'". $service_fee_amount ."'}";
						}
						else{
							$this->db->trans_rollback();
							 
							if($addLoanResult['error_code'] > 0) {
								echo '{"success":false,"msg":"'.$addLoanResult['error_message'].'","error_code":"'.$addLoanResult['error_code'].'"}';
							}
							else if ($addChargeResult>0) {
								echo "{'success':false,'msg':'Charge was NOT successfully saved.','error_code':'33'}";
							}
							else if($addComakerResult['error_code'] != 0) {
								echo '{"success":false,"msg":"'.$addComakerResult['error_message'].'","error_code":"'.$addComakerResult['error_code'].'"}';
							}
							else{
								echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';
							}
						}
					}	
				}
			}				
		}
		else echo "{'success':false,'msg':'Data was NOT successfully saved.','error_code':'2'}";
		
		log_message('debug', "[END] Controller loan:addLoan");
	}
	
	/**
	 * @desc Inserts default charges for the given loan type
	 * @param Array
	 */
	function addDefaultCharges($data)
	{
		$_REQUEST['newloan'] = $data;
		if (array_key_exists('newloan',$_REQUEST)) 
		{
			$params = array();		
			$charge_list = $this->getCharges($_REQUEST['newloan']['loan_code']);		
			if (count($charge_list)==0) {
				return 0;
			}
			foreach ($charge_list AS $data) {
				$params['loan_no'] = $this->getParam('LASTLOANNO');										  
				$params['transaction_code'] = $data['charge_code'];
				$params['amount'] = $this->computeCharges($_REQUEST['newloan']['principal'], $data['charge_formula']);
				$this->loancharges_model->populate($params);
				$this->loancharges_model->setValue('status_flag', '1');
				$this->loancharges_model->setValue('created_by', $_REQUEST['newloan']['created_by']);
				
				$result = $this->loancharges_model->insert();	
			}
			
			if($result['error_code'] == 0){  
			  return 0;
	        } 
	        else
			  return 1;
		}
		else
			return 1;
	}
	
	
	/**
	 * @desc Inserts new comaker
	 * @param Array
	 */
	function addComakerForAdd($loan_no)
	{	/*$_REQUEST['comaker'] = '[{"employee_id":"01517181","last_name":"CATAQUIS-TOLEDO","first_name":"MICHELLE MARIE"},{"employee_id":"01517069","last_name":"BAQUIRAN","first_name":"RAHMIR QUINN"},]';
		$_REQUEST['newloan']['loan_code']= 'CARL';
		$_REQUEST['newloan']['employee_id'] = '01517068';
		$_REQUEST['newloan']['created_by'] = 'peca';*/
		
		log_message('debug', "[START] Controller loan:addComaker");
		log_message('debug', "newloan param exist?:".array_key_exists('newloan',$_REQUEST));
		
		if($_REQUEST['comaker'][strlen($_REQUEST['comaker'])-2] == ",")
			$_REQUEST['comaker'] = substr($_REQUEST['comaker'], 0,strlen($_REQUEST['comaker'])-2)."]";
		$allowedGuarantors = $this->checkAllowedGuarantor($_REQUEST['newloan']['loan_code'], $_REQUEST['newloan']['employee_id']);
		$addedGuarantors = $this->checkAddedGuarantors($_REQUEST['comaker']);
		$params = json_decode(stripslashes($_REQUEST['comaker']),true);
		$restructure_no = isset($_REQUEST['newloan']['restructure_no']) ? $_REQUEST['newloan']['restructure_no'] : null;
		
		if(isset($_REQUEST['newloan']['modified_by'])){
			$_REQUEST['newloan']['created_by'] = $_REQUEST['newloan']['modified_by'];
		}
		
		if($addedGuarantors <= 1){
			if($addedGuarantors >= $allowedGuarantors){
				if($addedGuarantors == 0){
					$result['error_code'] = '0';
				}
				else if($addedGuarantors == 1){
					$company_code = $this->showCompCode($params[0]['employee_id'], 'company_code');				 
					$member_status = $this->showCompCode($params[0]['employee_id'], 'member_status');
					//get employee info
					$emp_info = $this->Member_model->get(array('employee_id' => $params[0]['employee_id']), null);
					$emp_info = $emp_info['list'][0];
					
					log_message('debug', 'company code: '.$company_code.', member_status: '.$member_status.', non member: '.$emp_info['non_member']);
					if($params[0]['employee_id'] == $_REQUEST['newloan']['employee_id']){
						$result['error_message'] = 'Loan applier cannot be co-maker of applied loan';
						$result['error_code'] = '15';
					}
					else if ($this->checkComaker($params[0]['employee_id'], $_REQUEST['newloan']['loan_code'], $_REQUEST['newloan']['loan_no'], $_REQUEST['newloan']['employee_id'], $restructure_no)>0){
						$result['error_message'] = 'One of the employees cannot become a co-maker of the loan';
						$result['error_code'] = '15';
					}
					else if (($company_code=='920' || $company_code=='910' || $member_status=='I') && $emp_info['non_member'] != 'Y') {
						$result['error_message'] = 'One of the employees cannot become a co-maker of the loan';
						$result['error_code'] = '15';
					}
					else {
						$this->loanguarantor_model->setValue('loan_no', $loan_no);
						$this->loanguarantor_model->setValue('guarantor_id', $params[0]['employee_id']);
						$this->loanguarantor_model->setValue('status_flag', '1');
						$this->loanguarantor_model->setValue('amortization_amount', '0');
						$this->loanguarantor_model->setValue('interest_amount', '0');
						$this->loanguarantor_model->setValue('created_by', $_REQUEST['newloan']['created_by']);
						$result = $this->loanguarantor_model->insert();	
					}
				}
			}
			else{
				$result['error_message'] = "Loan Type applied requires at least $allowedGuarantors Co-Maker/s.";
				$result['error_code'] = '47';
			}
		}
		else{
			$data = $this->stripDuplicate($_REQUEST['comaker']);
			if(count($data) < $allowedGuarantors){
				$result['error_message'] = "Loan Type applied requires at least $allowedGuarantors Co-Maker/s.";
				$result['error_code'] = '47';
			}
			else{
				foreach($data as $key => $val){
					$company_code = $this->showCompCode($val[0], 'company_code');				 
					$member_status = $this->showCompCode($val[0], 'member_status');
					
					//get employee info
					$emp_info = $this->Member_model->get(array('employee_id' => $val[0]), null);
					$emp_info = $emp_info['list'][0];
					
					if($val[0] == $_REQUEST['newloan']['employee_id']){
						$result['error_message'] = 'Loan applier cannot be co-maker of applied loan';
						$result['error_code'] = '15';
						break;	
					}
					else if ($this->checkComaker($val[0], $_REQUEST['newloan']['loan_code'], $loan_no, $_REQUEST['newloan']['employee_id'], $restructure_no)>0){
						$result['error_message'] = 'One of the employees cannot become a co-maker of the loan';
						$result['error_code'] = '15';
						break;	
					}
					else if (($company_code=='920' || $company_code=='910' || $member_status=='I') && $emp_info['non_member'] != 'Y') {
						$result['error_message'] = 'One of the employees cannot become a co-maker of the loan';
						$result['error_code'] = '15';
						break;	
					}
					else {	
						$this->loanguarantor_model->setValue('loan_no', $loan_no);
						$this->loanguarantor_model->setValue('guarantor_id', $val[0]);
						$this->loanguarantor_model->setValue('status_flag', '1');
						$this->loanguarantor_model->setValue('amortization_amount', '0');
						$this->loanguarantor_model->setValue('interest_amount', '0');
						$this->loanguarantor_model->setValue('created_by', $_REQUEST['newloan']['created_by']);
						$result = $this->loanguarantor_model->insert();
					}			
				}
			}
		}
			
		log_message('debug', "[END] Controller loan:addComaker");
		return $result;
	}
	
	function addMemberCoMaker(){
		$_REQUEST['data'] = '{"employee_id":"' . $_REQUEST['employee_id'] . '"}';
		$this->loanguarantor_model->table_name = $this->mloanguarantor_model->table_name;
		$this->addComaker('2');
		
	}
	
	/**
	 * @desc Inserts new comaker
	 * @param Array
	 */
	function addComaker($status_flag = '1')
	{
		log_message('debug', "[START] Controller loan:addComaker");
		log_message('debug', "newloan param exist?:".array_key_exists('newloan',$_REQUEST));
				  
		if (array_key_exists('data',$_REQUEST)) {
		
			$allowedGuarantors = $this->checkAllowedGuarantor($_REQUEST['loan_code'], $_REQUEST['employee_id']);
			$currentGuarantors =  $this->checkCurrentGuarantors($_REQUEST['loan_no']);
			$params = json_decode(stripslashes($_REQUEST['data']),true);
			//if($allowedGuarantors >= $currentGuarantors + 1){
				
				$company_code = $this->showCompCode($params['employee_id'], 'company_code');				 
				$member_status = $this->showCompCode($params['employee_id'], 'member_status');
				//get employee info
				$emp_info = $this->Member_model->get(array('employee_id' => $params['employee_id']), null);
				$emp_info = $emp_info['list'][0];
				$error_flag = 0;
			
				if ($this->checkComaker($params['employee_id'], $_REQUEST['loan_code'], $_REQUEST['loan_no'], $_REQUEST['employee_id'])>0){
					$result['error_message'] = 'One of the employees cannot become a co-maker of the loan';
					$result['error_code'] = '15';
					$error_flag = 1;
				}
				else if (($company_code=='920' || $company_code=='910' || $member_status=='I') && $emp_info['non_member'] != 'Y') {
					$result['error_message'] = 'One of the employees cannot become a co-maker of the loan';
					$result['error_code'] = '15';
					$error_flag = 1;	
				}
				else {
					$this->loanguarantor_model->setValue('loan_no', $_REQUEST['loan_no']);
					$this->loanguarantor_model->setValue('guarantor_id', $params['employee_id']);
					$this->loanguarantor_model->setValue('status_flag', $status_flag);
					$this->loanguarantor_model->setValue('amortization_amount', '0');
					$this->loanguarantor_model->setValue('interest_amount', '0');
					$this->loanguarantor_model->setValue('created_by', $_REQUEST['user']);
					$result = $this->loanguarantor_model->insert();	
				}

				if($result['error_code']==0 && $error_flag==0)
					echo "{'success':true,'msg':'Data successfully saved.'}";
				else
					echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';
			//}
			//else 
			//	echo "{'success':false,'msg':'You have exceeded the number of guarantors','error_code':'34'}";
		}
		else
			echo "{'success':false,'msg':'Data was NOT successfully saved.','error_code':'2'}";

		log_message('debug', "[END] Controller loan:addComaker");
	}
	
	function checkCurrentGuarantors($loan_no){
		$data = $this->loanguarantor_model->get(array(
			'loan_no' => $loan_no
		), 'COUNT(*) as count');
		
		return $data['list'][0]['count'];
	}
	
	/**
	 * @param Counts the added guarantors
	 */
	function checkAddedGuarantors($data){
		if(substr($data,0,1) == "{")
			return 1;
		else if(substr($data,0,1) == "["){
			$data = json_decode(stripslashes($data),true); 	
			return count($data);
		}
		else 
			return 0;	
	}
	
	/**
	 * @desc Returns the no. of guarantors for a specific loan code and years of service
	 */
	function checkAllowedGuarantor($loan_code, $employee_id){
		
		$years_of_service = $this->member_model->getEmpYearsOfService($employee_id);
		$data = $this->loancodeheader_model->retrieveLoanCodes(
			array('RH.status_flag' => '1', 'RH.loan_code' => $loan_code)
			//updated 02082016 by lap
			//,null
			,1
			,null
			,array('RL.guarantor AS guarantor')
			//updated 02082016 by lap
			//,null
			,array('cast(rl.guarantor as signed integer)', 'ASC')
			,$years_of_service
			);

		if(!isset($data['list'][0]['guarantor']))	
			return 0;
		else 
			return $data['list'][0]['guarantor'];
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
	 * @desc Strips duplicate arrays in a two dimensional array
	 */
	function stripDuplicate($data){
		$data = json_decode(stripslashes($data),true);
		foreach($data as $key => $val){
			$data[$key] = implode(",", $val); 
		}
		$data = array_unique($data);
		
		foreach($data as $key => $val){
			$data[$key] = explode(",", $val); 
		}
		return $data;
	}
	
	/**
	 * @desc Totals all loan charges
	 */
	function totalLoanCharges($loan_no){
		$data = $this->loancharges_model->get(array(
			'loan_no' => $loan_no
		), array('SUM(amount) AS total'));
		
		if(!isset($data['list'][0]['total']))
			return 0;
		else 
			return $data['list'][0]['total'];
	}
	
	/**
	 * @desc Adds multiple charges
	 */
	function addAllCharges(){
		log_message('debug', "[START] Controller loan:addAllCharges");
		log_message('debug', "charges array exist?:".array_key_exists('charges',$_REQUEST));

		if(array_key_exists('charges',$_REQUEST)){
			$chargesArray = json_decode(stripslashes($_REQUEST['charges']),true);
			
			foreach($chargesArray as $data){
				$params['loan_no'] = $data['loan_no'];										  
				$params['transaction_code'] = $data['transaction_code'];
				$params['amount'] = $data['amount'];
				$this->loancharges_model->populate($params);
				$this->loancharges_model->setValue('status_flag', '1');
				$this->loancharges_model->setValue('created_by', "PECA");
				$this->loancharges_model->insert();	
			}
		}

		log_message('debug', "[END] Controller loan:addCharges");
	}
	/**
	 * @desc Adds a single charge
	 * @param Array
	 */
	function addCharges(){
		/*$_REQUEST['data'] = '{
			"loan_no":"100"
			,"transaction_code": "PDED"
			,"amount":"400"
		}';
		
		$_REQUEST['user'] = 'PECA';*/
		
		log_message('debug', "[START] Controller loan:addCharges");
		log_message('debug', "newloan param exist?:".array_key_exists('newloan',$_REQUEST));

		if (array_key_exists('data',$_REQUEST)){
			$data = json_decode(stripslashes($_REQUEST['data']),true);
			$params['loan_no'] = $data['loan_no'];										  
			$params['transaction_code'] = $data['transaction_code'];
			$params['amount'] = $data['amount'];
			$this->loancharges_model->populate($params);
			$this->loancharges_model->setValue('status_flag', '1');
			$this->loancharges_model->setValue('created_by', $_REQUEST['user']);
			
			$this->db->trans_start();
			$this->loancharges_model->insert();	
			$totalLoanCharges = $this->totalLoanCharges($params['loan_no']);
			$this->tloan_model->setValue('service_fee_amount', $totalLoanCharges);
			$this->tloan_model->update(array('loan_no' => $params['loan_no']));
			$this->db->trans_complete();
		
			if($this->db->trans_status()=== TRUE){  
			  echo "{'success':true,'msg':'Data successfully saved.','total_charges':'$totalLoanCharges'}";
	        } 
	        else
			  echo "{'success':false,'msg':'Data was NOT successfully saved.','error_code':'2'}";
		}
		else
			echo "{'success':false,'msg':'Data was NOT successfully saved.','error_code':'2'}";

		log_message('debug', "[END] Controller loan:addCharges");
	}
	
	/**
	 * @desc Update Loan Transaction Information
	 * @return array
	 */
	function updateLoan()
	{
		log_message('debug', "[START] Controller loan:updateLoan");
		log_message('debug', "newloan param exist?:".array_key_exists('newloan',$_REQUEST));
		
		if (array_key_exists('newloan',$_REQUEST)){
			$loan_code = $this->loancodeheader_model->loanCodeExists($_REQUEST['newloan']['loan_code']);
			##### NRB EDIT START #####
			if (isset($_REQUEST['newloan']['insurance_broker']) && ($_REQUEST['newloan']['insurance_broker']!='' || $_REQUEST['newloan']['insurance_broker']!=null)){
			/* if ($_REQUEST['newloan']['insurance_broker']!='' || $_REQUEST['newloan']['insurance_broker']!=null){ */	
			##### NRB EDIT END #####
				$ins_broker = $this->supplier_model->supplierIdExists($_REQUEST['newloan']['insurance_broker']);
			}
			else {
				$ins_broker = 2;
			}
			
			if ($_REQUEST['newloan']['appraiser_broker']!='' || $_REQUEST['newloan']['appraiser_broker']!=null){
				$app_broker = $this->supplier_model->supplierIdExists($_REQUEST['newloan']['appraiser_broker']);
			}
			else {
				$app_broker = 2;
			}
			
			if ($loan_code==0 && $ins_broker==0 && $app_broker==0){
				echo "{'success':false,'msg':'Loan code, Appraiser, and Insurance Broker do not exist.'}";
			}
			else if ($loan_code==0 && $ins_broker==0 && $app_broker==1){
				echo "{'success':false,'msg':'Loan code and Insurance Broker do not exist.'}";
			}
			else if ($loan_code==0 && $ins_broker==1 && $app_broker==0){
				echo "{'success':false,'msg':'Loan code and Appraiser do not exist.'}";
			}
			else if ($loan_code==0 && $ins_broker==1 && $app_broker==1){
				echo "{'success':false,'msg':'Loan code does not exist.'}";
			}
			else if ($loan_code==1 && $ins_broker==0 && $app_broker==0){
				echo "{'success':false,'msg':'Appraiser and Insurance Broker do not exist.'}";
			}
			else if ($loan_code==1 && $ins_broker==0 && $app_broker==1){
				echo "{'success':false,'msg':'Insurance Broker does not exist.'}";
			}
			else if ($loan_code==1 && $ins_broker==1 && $app_broker==0){
				echo "{'success':false,'msg':'Appraiser does not exist.'}";
			}
			else {
				$employee_id = $_REQUEST['newloan']['employee_id'];
				$principal = $_REQUEST['newloan']['principal'];
				$term = $_REQUEST['newloan']['term'];
				$loan_code = $_REQUEST['newloan']['loan_code'];
				$restructure_amount = isset($_REQUEST['newloan']['restructure_amount']) ? $_REQUEST['newloan']['restructure_amount']: 0;
				$data = $this->getRLoanHdr($loan_code);
				
				if ($this->member_model->employeeExists($employee_id)==0) {
					echo("{'success':false,'msg':'Employee does not exsit','error_code':'22'}");
				}
				else if($this->member_model->employeeIsInactive($employee_id)){
					echo("{'success':false,'msg':'Employee is inactive','error_code':'55'}");
				}
				else if ($this->checkMaximumLoanAmount($principal, $data[0]['max_loan_amount'])>0) {
					echo("{'success':false,'msg':'Principal Amount exceeds the maximum loan amount','error_code':'13'}");	
				}
				else if ($this->checkMinMaxTerms($term, $data[0]['min_term'], $data[0]['max_term'])>0){
					echo("{'success':false,'msg':'Term should be between ".$data[0]['min_term']." and ".$data[0]['max_term']."','error_code':'14'}");	
				}
				else if ($this->checkSuspensionDate($employee_id)>0){
					echo("{'success':false,'msg':'You are still on suspension','error_code':'9'}");	
				}
				else if ($this->checkCapitalContribution($principal, $loan_code, $employee_id, $restructure_amount)>0){
					$result = $this->checkCapitalContribution($principal, $loan_code, $employee_id, $restructure_amount);
					if ($result==2) {
						echo("{'success':false,'msg':'Capital Contribution Balance after transaction is less than the Capital Contribution Minimum Balance','error_code':'37'}");
					}
					else {
						echo("{'success':false,'msg':'Capital Contribution Balance is below 1/3 of Loan Principal Amount','error_code':'16'}");
					}				
				}
				else if ($this->checkYearsOfService($employee_id, $loan_code)>0){
					echo("{'success':false,'msg':'The employee has not rendered enough no of years of service for the applied loan','error_code':'23'}");	
				}
				else if ($this->checkRetiree($employee_id, $principal)>0) {
					echo("{'success':false,'msg':'The employee is a retiree. Thus he cannot loan more than 70% of his capital contribution.','error_code':'11'}");	
				}
				else if($this->hasValidGuarantors($employee_id)){
					echo("{'success':false,'msg':'Employee has invalid co-makers','error_code':'8'}");	
				}
				else {
					$interest_rate = $data[0]['employee_interest_percentage'];
					$employee_interest_total = $this->computeInterestRateAmount($interest_rate, $principal, $term);
					$initial_interest = $this->computeInitialInterestAmount($interest_rate,$principal);
		
					if ($data[0]['unearned_interest']=="Y") 
						$employee_interest_amortization = 0;
					else 
						$employee_interest_amortization = $this->computeAmortizedInterest($interest_rate, $principal, $term);
					
					$company_interest_rate = $data[0]['company_share_percentage'];
					if ($company_interest_rate==NULL) {
						$company_interest_rate = 0;
						$company_interest_rate_total = 0;
						$company_interest_amort = 0;
					}
					else {
						$company_interest_rate_total = $this->computeCompanyInterestRateAmount($company_interest_rate, $principal);	
						$company_interest_amort = $this->computeCompanyAmortizedInterest($company_interest_rate, $principal);
					}
					
					$employee_principal_amort = $this->computePrincipalAmortization($principal, $term);
					$loan_proceeds = $this->computeLoanProceeds($principal, $interest_rate, $data[0]['unearned_interest'], $data[0]['interest_earned'], $term);
						
					/***Retrieve capital contribution balance***/
					$years_of_service = $this->member_model->getEmpYearsOfService($employee_id);	
			
					$loanDtl = $this->loancodeheader_model->retrieveLoanCodes(
					array('RH.status_flag' => '1', 'RH.loan_code' => $loan_code)
					,null
					,null
					,array("RL.capital_contribution AS capital_contribution")
					,null
					,$years_of_service
					);
					
					if($loanDtl['count']>0 && $loanDtl['list'][0]['capital_contribution']!=''){
						$capital_contribution = $loanDtl['list'][0]['capital_contribution'] * $principal;
					}
					else{
						$capital_contribution = 0;
					}
					/***Retrieve capital contribution balance***/
					
					if($_REQUEST['newloan']['mri_fip_amount']==""){
						$_REQUEST['newloan']['mri_fip_amount'] = 0;
					}
					if($_REQUEST['newloan']['broker_fee_amount']==""){
						$_REQUEST['newloan']['broker_fee_amount'] = 0;
					}
					if($_REQUEST['newloan']['government_fee_amount']==""){
						$_REQUEST['newloan']['government_fee_amount'] = 0;
					}
					if($_REQUEST['newloan']['other_fee_amount']==""){
						$_REQUEST['newloan']['other_fee_amount'] = 0;
					}
					
					$this->tloan_model->populate($_REQUEST['newloan']);
					$this->tloan_model->setValue('status_flag', '1');
					$this->tloan_model->setValue('interest_rate', $interest_rate);
					//$this->tloan_model->setValue('initial_interest', $initial_interest);
					$this->tloan_model->setValue('employee_interest_total', $employee_interest_total);
					//$this->tloan_model->setValue('employee_interest_amortization', $employee_interest_amortization);
					$this->tloan_model->setValue('company_interest_rate', $company_interest_rate);
					$this->tloan_model->setValue('company_interest_total', $company_interest_rate_total);
					$this->tloan_model->setValue('company_interest_amort', $company_interest_amort);
					//$this->tloan_model->setValue('employee_principal_amort', $employee_principal_amort);
					//$this->tloan_model->setValue('loan_proceeds', $loan_proceeds);
					$this->tloan_model->setValue('principal_balance', $principal);
					$this->tloan_model->setValue('interest_balance', $employee_interest_total);
					$this->tloan_model->setValue('capital_contribution_balance', $capital_contribution);
					
					if(isset($_REQUEST['newloan']['restructure_no'])){
						$this->tloan_model->setValue('restructure_amount', (empty($_REQUEST['newloan']['restructure_amount'])?"0":$_REQUEST['newloan']['restructure_amount']));
					} else {
						$this->tloan_model->setValue('restructure_amount', "0.0");
						$this->tloan_model->setValue('restructure_no', "");
					}
					$this->db->trans_begin();
					$result = $this->tloan_model->update(array('loan_no'=> $_REQUEST['newloan']['loan_no']
															  ,'status_flag'=>'1')
												 );
					
					if($result['affected_rows'] < 0) {
						echo '{"success":false,"msg":"'.$result['error_message'].'"}';
					}
					else{
						$this->loanguarantor_model->setValue("loan_no", $_REQUEST['newloan']['loan_no']);
						$this->loancharges_model->setValue("loan_no", $_REQUEST['newloan']['loan_no']);
						
						$deleteAllComakersResult = $this->loanguarantor_model->delete();
						$addComakerResult = $this->addComakerForAdd($_REQUEST['newloan']['loan_no']);
						$this->loancharges_model->delete();
						$this->addAllCharges();
						
						if($this->db->trans_status()===TRUE && $addComakerResult['error_code']==0){
							$this->db->trans_commit();
							echo "{'success':true,'msg':'Data successfully saved.'}";
						}
						else{
							$this->db->trans_rollback();
							if($addComakerResult['error_code']!=0){
								echo '{"success":false,"msg":"'.$addComakerResult['error_message'].'"}';
							}
							else{
								echo "{'success':false,'msg':'Data not successfully saved.'}";
							}
						}
						
					}
				}
			}
		}
		else echo "{'success':false,'msg':'Data was NOT successfully saved.','error_code':'2'}";
		
		log_message('debug', "[END] Controller loan:updateLoan");
	}
	
	/**
	 * @desc Updates charges
	 */
	function updateCharge(){	
		/*$_REQUEST['data'] = '{"id":"7677:SFEE","loan_no":"100","transaction_code":"PDED","transaction_description":"Service Charge Loan","amount":200}';
		$_REQUEST['user'] = 'PECA';
		*/
		
		log_message('debug', "[START] Controller loan:updateCharge");
		log_message('debug', "data param exist?:".array_key_exists('data',$_REQUEST));
		
		if (array_key_exists('data',$_REQUEST)){
			$params = json_decode(stripslashes($_REQUEST['data']),true);
			
			$this->loancharges_model->setValue('amount', $params['amount']);
			$this->loancharges_model->setValue('status_flag', '1');
			$this->loancharges_model->setValue('modified_by', $_REQUEST['user']);
			
			$this->db->trans_start();
			$this->loancharges_model->update(array(
				'loan_no' => $params['loan_no']
				,'transaction_code' => $params['transaction_code']
			));
			$totalLoanCharges = $this->totalLoanCharges($params['loan_no']);
			$this->tloan_model->setValue('service_fee_amount', $totalLoanCharges);
			$this->tloan_model->update(array('loan_no' => $params['loan_no']));
			$this->db->trans_complete();
			
			if($this->db->trans_status()=== TRUE){  
			  echo "{'success':true,'msg':'Data successfully saved.','total_charges':'$totalLoanCharges'}";
	        } 
	        else
			  echo "{'success':false,'msg':'Data was NOT successfully saved.','error_code':'2'}";
		}
		else
			echo "{'success':false,'msg':'Data was NOT successfully saved.','error_code':'2'}";
		
		log_message('debug', "[END] Controller loan:updateCharge");
	}
	
	/**
	 * @desc Updates comaker
	 */
	function updateComaker()
	{	
		log_message('debug', "[START] Controller loan:updateComaker");
		log_message('debug', "data param exist?:".array_key_exists('data',$_REQUEST));
		
		if (array_key_exists('data',$_REQUEST)) 
		{
			$params = array();
			foreach ($_REQUEST['data'] AS $data){
				$params[] = json_decode(stripslashes($data),true);
			}
			
			$this->loanguarantor_model->delete(array('loan_no' => $params[0]['loan_no']));
			
			foreach($params AS $data){
				$this->loanguarantor_model->populate($data);
				$this->loanguarantor_model->setValue('status_flag', '1');
				$this->loanguarantor_model->setValue('created_by', $_REQUEST['user']);
				$result = $this->loanguarantor_model->insert();				
			}
			if($result['error_code'] == 0){  
			  echo "{'success':true,'msg':'Data successfully saved.'}";
	        } 
	        else
			  echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';
		}
		else
			echo "{'success':false,'msg':'Data was NOT successfully saved.','error_code':'2'}";
		
		log_message('debug', "[END] Controller loan:updateComaker");
	}
	
	/**
	 * @desc Deletes a single charge
	 */
	function deleteCharge(){	
		log_message('debug', "[START] Controller loan:deleteCharges");
		log_message('debug', "data param exist?:".array_key_exists('data',$_REQUEST));
		
		if (array_key_exists('data',$_REQUEST)) {
			$params = explode(':', json_decode(stripslashes($_REQUEST['data']),true));
	
			$this->db->trans_start();
			$result = $this->loancharges_model->delete(array(
				'loan_no' => $params[0]
				,'transaction_code' => $params[1]
				,'status_flag' => '1'
			));
		
			$totalLoanCharges = $this->totalLoanCharges($params[0]);
			$this->tloan_model->setValue('service_fee_amount', $totalLoanCharges);
			$this->tloan_model->update(array('loan_no' => $params[0]));
			$this->db->trans_complete();
			
			
			if($result['affected_rows'] <=0 || $this->db->trans_status()=== FALSE){
				echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
			} else {
				echo "{'success':true,'msg':'Data successfully deleted.','total_charges':'$totalLoanCharges'}";
			}
		} else {
			echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
		}

		log_message('debug', "[END] Controller loan:deleteCharge");	
	}
	
	function deleteMemberComaker(){
		
		$_REQUEST['data'] = '"' . $_REQUEST['loan_no'] . ':' . $_REQUEST['employee_id'] . '"';
		//20110805 fix for unsync co-maker admin and co-maker online 
		$this->transferComakerToDelete();
		
		//jdj 07272017 
		//start. this will delete suspension date of borrower upon removal of invalid comaker
		
		$sql ="SELECT m.`invalid_date` FROM m_employee m 
		INNER JOIN m_loan_guarantor g ON m.`employee_id`=g.`guarantor_id` 
		INNER JOIN m_loan l 
		ON g.`loan_no`=l.`loan_no` WHERE g.`guarantor_id`={$_REQUEST['employee_id']} AND l.`loan_no`={$_REQUEST['loan_no']}";
		$query = $this->db->query($sql);
		log_message('debug', 'sql='.$sql);
		
				
		if($sql!='' || $sql !=null){
		
			$sql1 = "UPDATE m_employee AS m INNER JOIN m_loan l ON m.`employee_id`=l.`employee_id`
			INNER JOIN m_loan_guarantor g ON l.`loan_no`=g.`loan_no`
			SET m.`suspended_date`=''
			WHERE g.`guarantor_id`={$_REQUEST['employee_id']}
			AND l.`loan_no`={$_REQUEST['loan_no']}";
		
			$query1 = $this->db->query($sql1);
			log_message('debug', 'sql1='.$sql1);
			}
		// end 07272017
		$this->deleteComaker(true);
		
		
	}
	
	
	/**
	 * copy the comaker to be deleted from
	 * m_loan_guarantor to mc_loan_guarantor
	 */
	//20110805 fix for unsync co-maker admin and co-maker online 
	function transferComakerToDelete(){
		if (array_key_exists('data',$_REQUEST)) {		
			$params = array();
			$params = explode(':', json_decode(stripslashes($_REQUEST['data']),true));
			$result = NULL;
			$record = array( 'loan_no' => $params[0]
							,'guarantor_id' => $params[1]
							,'status_flag' => 2);
			$result = $this->mloanguarantor_model->get($record);
		if($result['list']){
				$this->mcloanguarantor_model->delete(array('loan_no' => $params[0], 'guarantor_id' => $params[1]));
		
				$this->mcloanguarantor_model->populate($result['list'][0]);				
				$this->mcloanguarantor_model->setValue('status_flag' , 0);
				$this->mcloanguarantor_model->insert();	
			}
		}
	}
	
	

	/**
	 * @desc Deletes a single comaker
	 * @return array
	 */
	function deleteComaker($member = false){
		log_message('debug', "[START] Controller loan:deleteComaker");
		log_message('debug', "data param exist?:".array_key_exists('data',$_REQUEST));
		
		if (array_key_exists('data',$_REQUEST)) {
			$params = array();
			$params = explode(':', json_decode(stripslashes($_REQUEST['data']),true));
			$result = NULL;
			if($member){
				$result = $this->mloanguarantor_model->delete(array(
													'loan_no' => $params[0]
												   ,'guarantor_id' => $params[1]
												   ,'status_flag' => '2')
												);
			} else {
				$result = $this->loanguarantor_model->delete(array(
													'loan_no' => $params[0]
												   ,'guarantor_id' => $params[1]
												   ,'status_flag' => '1')
												);
			}
			if($result['affected_rows'] <= 0){
				echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
			} else {
				echo "{'success':true,'msg':'Data successfully deleted.'}";
			}
		} else {
			echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
		}

		log_message('debug', "[END] Controller loan:deleteComaker");	
	}
	
	/**
	 * @desc Deletes loan transaction
	 * @return array
	 */
	function deleteLoan()
	{
		/*$_REQUEST['newloan']['loan_no'] = '100';*/
		
		log_message('debug', "[START] Controller loan:deleteLoan");
		log_message('debug', "newloan param exist?:".array_key_exists('newloan',$_REQUEST));
		
		if (array_key_exists('newloan',$_REQUEST)) {
			$this->db->trans_start();
			$this->tloan_model->setValue('status_flag', '0');
			$this->tloan_model->setValue('modified_by', $_REQUEST['newloan']['modified_by']);
			$deleteLoanResult 	= $this->tloan_model->update(array(
										'loan_no' => $_REQUEST['newloan']['loan_no']
										,'status_flag' => '1')
									);
			
			if($deleteLoanResult['affected_rows'] <= 0){
				echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
			} 
			else {
				$deleteChargeResult = $this->loancharges_model->delete(array('loan_no'=>$_REQUEST['newloan']['loan_no']));			
				$deleteGuarantorResult = $this->loanguarantor_model->delete(array('loan_no'=>$_REQUEST['newloan']['loan_no']));	
				
				if($deleteChargeResult['error_code'] != 0)
					echo '{"success":false,"msg":"'.$delChargeResult['error_message'].'"}';	
				else if($deleteGuarantorResult['error_code'] != 0)
					echo '{"success":false,"msg":"'.$deleteGuarantorResult['error_message'].'"}';
				else 
					echo "{'success':true,'msg':'Data successfully deleted.'}";
			}			
			$this->db->trans_complete();		
		} 
		else echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
			
		log_message('debug', "[END] Controller loan:deleteLoan");
	}
	
	/**
	 * @desc Retrieves Service charges of the specified loan type
	 * @return array
	 */
	function getCharges($loan_code) 
	{	
		$_REQUEST['filter'] = array('rtc.transaction_code' => $loan_code
			,'rtc.status_flag' => '1'
			,'rt.transaction_group' => 'SC'
		);		
		$data = $this->transactioncharges_model->getServiceCharges(
			 array_key_exists('filter',$_REQUEST) ? $_REQUEST['filter'] : null
			,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
			,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
			,array('rtc.transaction_code'
				  ,'rtc.charge_code'
				  ,'rtc.charge_formula')
		);
		
		foreach($data['list'] as $row => $value){
			$id = $value['transaction_code'] . ':' . $value['charge_code'];
			$data['list'][$row]['id'] = $id;
		}

		return $data['list'];
	}
	
	
	/**
	 * @desc Retrieves Information of the specified loan type
	 * @return array
	 */
	function getRLoanHdr($loan_code)
	{
		$_REQUEST['filter'] = array('loan_code' => $loan_code, 'status_flag' => '1');	
		$data = $this->loancodeheader_model->get_list(
			array_key_exists('filter',$_REQUEST) ? $_REQUEST['filter'] : null,
			array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
			array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
			array('loan_code AS loan_code'
					,'loan_description AS loan_description'
					,'emp_interest_pct AS employee_interest_percentage'
					,'min_term AS min_term'
					,'max_term AS max_term'
					,'interest_earned AS interest_earned'
					,'unearned_interest AS unearned_interest'
					,'max_loan_amount AS max_loan_amount'
					,'comp_share_pct AS company_share_percentage'
					,'restructure AS restructure'
					,'downpayment_pct AS downpayment_pct')
		);
		
		
		return $data['list'];
	}
	
	/**
	 * @desc Retrieves detail of the selected loan for checking capital contribution and counting years of service
	 * @param Array
	 */
	function getRLoanDtl($loan_code)
	{
		$_REQUEST['filter'] = array('loan_code' => $loan_code);		
		$data = $this->loancodedetail_model->getLoanDetail(
			array_key_exists('filter',$_REQUEST) ? $_REQUEST['filter'] : null,
			array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
			array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
			array('loan_code'
				 ,'capital_contribution'
				 ,'MAX(years_of_service) AS to_yos'
				 ,'MIN(years_of_service) AS from_yos'
				 ,'guarantor')
			,'loan_code'	 
		);
		if ($data['count']<=0){
			return 0;
		}
		return $data['list'][0];
	}
	
	/**
	 * @desc Computes charges for the loan
	 * 
	 */
	function computeCharges($principal, $charge_formula)
	{
		$amount = str_replace('pr',$principal,$charge_formula);
		eval("\$data = $amount;");
		return $data;
	}
	
	/**
	 * @desc Computes the service fee amount for adding new loan
	 * 
	 */
	function computeServiceFee($loan_code, $principal)
	{
		$service_fee = 0;
		$charge_list = $this->getCharges($loan_code);			
		foreach ($charge_list AS $data) {
			$amount = $this->computeCharges($principal, $data['charge_formula']);
			$service_fee = $service_fee + $amount;
		}
	
		return $service_fee;
	}
	
	/**
	 * @desc Checks if the principal amount exceeds the Maximum Loan amount for the specified loan
	 * @return Returns 0 if principal amount does not exceed Maximum loan amount
	 * @param $principal : User input
	 */
	function checkMaximumLoanAmount($principal=0, $max_loan_amount) 
	{	
		$result = 0; 	
		if ($principal<=$max_loan_amount) $result = 0;
		else $result = 1;
		
		return $result;
	}
	
	/**
	 * @desc Checks if the term is greater than or equal to Minimum term and less than or equal to Maximum term
	 * @return Returns 0 if the input term is within minimum and maximum terms
	 * @param $term User input
	 */
	function checkMinMaxTerms($term = 1, $min_term, $max_term)
	{
		$result = 0;
		if ($term>=$min_term && $term<=$max_term) $result = 0;
		else $result = 1;
		
		return $result;
	}
	
	/**
	 * @desc Computes Interest Amount by emp_interest_pct *principal. 
	 * If term is less than 12, employee interest amount= (principal * emp_interest_pct/12) * term
	 * @return decimal
	 * @param $term User input
	 * @param $principal User Input
	 */
	function computeInterestRateAmount($emp_interest_pct=0, $principal=0, $term=0) 
	{
		if ($term < 12)
			$emp_interest_rate_amount = ($principal * ($emp_interest_pct/100)/12) * $term;
		else 
			$emp_interest_rate_amount = ($emp_interest_pct/100) * $principal;
		
		return round($emp_interest_rate_amount,2);	
	}
	
	/**
	 * @desc Computed as  [(Pricipal*current interest rate/12)/number of days of the month] * remaining days of the month
	 * @return decimal
	 * @param integer $principal User Input
	 */
	function computeInitialInterestAmount($emp_interest_pct = 0, $principal = 0) 
	{
		$data = $this->parameter_model->get(array('parameter_id'=>'CURRDATE')
				,'parameter_value');
		$date = date("YmdHis", strtotime($data['list'][0]['parameter_value']));
		$day_today = date('d',strtotime($date));
		$days_of_month = date("t", strtotime($date));
		$remaining_days_of_month = ($days_of_month-$day_today);
		
		$emp_initial_interest_amount = (($principal * ($emp_interest_pct/100)/12)/$days_of_month)*$remaining_days_of_month;	
			
		return round($emp_initial_interest_amount,2);
	}
	
	/**
	 * @desc Computes Amortized Interest of employee when unearned interest condition is Y.
		Computed as <principal>*<emp_interest_pct>/12, if term is less than 12, computed as <principal>*<emp_interest_pct>/term
	 * @return decimal
	 * @param integer $principal User Input
	 * @param integer $term
	 */
	function computeAmortizedInterest($emp_interest_pct = 0,$principal = 0,$term = 0) 
	{
		if ($term<12) $emp_amortized_interest = $principal*($emp_interest_pct/100)/$term;
		else $emp_amortized_interest = ($principal*($emp_interest_pct/100)/12);
	
		return round($emp_amortized_interest,2);
	}
	
	/**
	 * @desc Computed as principal * Company Interest Rate.Ex. 800,000.00 * 0.05 = 40,000.00 
	 * Rounded to the nearest hundredth. Ex. 446.8561 should be rounded to 447.86
	 */
	function computeCompanyInterestRateAmount($comp_share_pct = 0, $principal = 0)
	{
		$comp_interest_rate_amount = $principal * ($comp_share_pct/100);
		
		return round($comp_interest_rate_amount,2);
	}
	
	/**
	 * @desc  Computed as (principal * Company Interest Rate)/12.
	 * Ex. (80,000.00 * 0.05)/12 = 3,333.33
	 * Rounded to the nearest whole number. Ex. 3,333.33 rounded to 3,333.00
	 */
	function computeCompanyAmortizedInterest($comp_share_pct = 0, $principal = 0)
	{
		$comp_amortized_interest = ($principal * ($comp_share_pct/100)/12);
		
		return round($comp_amortized_interest,2);
	}
	
	/**
	 * @desc  Computed as principal/term in Months.
	 * Ex. 800,000.00/48 = 16,667.6666
	 * Round to the nearest whole number. Ex. 16,667.6666 rounded to 16,667.00
	 */
	function computePrincipalAmortization($principal = 0, $term = 0)
	{
		$principal_amortization = $principal/$term;
		return round($principal_amortization,2); 
	}
	
	/**
	 * @desc Computes the Loan Proceeds. If initial interest=Y, loan proceeds=principal amount - initial interest.
	 * @param $principal User Input
	 * @param $emp_interest_pct
	 * @param $unearned_interest
	 * @param $interest_earned
	 */
	function computeLoanProceeds($principal = 0, $emp_interest_pct = 0, $unearned_interest='Y', $interest_earned='Y', $term = 0)
	{
		$loan_proceeds = $principal;
			
		if ($interest_earned=="Y") {
			$emp_intitial_interest_amount = $this->computeInitialInterestAmount();
			$loan_proceeds = $principal - $emp_intitial_interest_amount;
		}
		
		if ($unearned_interest=="Y"){
			$emp_interest_rate = $this->computeInterestRateAmount($emp_interest_pct, $principal, $term);
			$loan_proceeds = $loan_proceeds - $emp_interest_rate;
		}
	
		return round($loan_proceeds,2);
	}
	
	/**
	 * @desc Counts the number of loans the comaker has guaranteed to an employee.
	 * @param $guarantor_id ID of the comaker
	 * @param $employee_id ID of the new loan applicant
	 * 
	 */
	function countLoansGuaranteedToEmployee($guarantor_id, $employee_id)
	{
		$dataT = $this->loanguarantor_model->getLoanGuaranteed(
			array('tlg.guarantor_id' => $guarantor_id,'tl.employee_id' => $employee_id),
			null,
			null,
			array('tl.loan_no AS loan_no')
		);
		$dataM = $this->loanguarantor_model->getMLoanGuaranteed(
			array('tlg.guarantor_id' => $guarantor_id,'ml.employee_id' => $employee_id),
			null,
			null,
			array('ml.loan_no AS loan_no')
		);
		return $dataT['count'] + $dataM['count'];
	}
	function countLoansGuaranteedByComaker($guarantor_id)
	{
		$dataT = $this->loanguarantor_model->getLoanGuaranteed(
			array('tlg.guarantor_id' => $guarantor_id),
			null,
			null,
			array('tl.loan_no AS loan_no')
		);
		$dataM = $this->loanguarantor_model->getMLoanGuaranteed(
			array('tlg.guarantor_id' => $guarantor_id),
			null,
			null,
			array('ml.loan_no AS loan_no')
		);
		return $dataT['count'] + $dataM['count'];
	}
	
	/**
	 * @desc Checks if loan is a restructured loan and the guarantor is the comaker of the loan
	 * @return 0-if restructure, 1-otherwise
	 */
	function checkRestructuredLoan($employee_id, $loan_no, $guarantor_id) 
	{	
		log_message("debug", "restructure query start");
		$_REQUEST['filter'] = array('tl.employee_id' => $employee_id
									,'tl.loan_no' => $loan_no
									,'tlg.guarantor_id' => $guarantor_id
									,'rl.restructure' => 'Y'
									,'tl.principal_balance >'=>0
									,'tl.status_flag' => '2');	
		$data = $this->mloan_model->getRestructuredLoanInfo(
			array_key_exists('filter',$_REQUEST) ? $_REQUEST['filter'] : null,
			null,
			null,
			array('tl.loan_no AS loan_no'
					,'tl.employee_id AS employee_id'
					,'tlg.guarantor_id AS guarantor_id'								
					,'tl.principal_balance AS balance'							
					,'tl.loan_date AS loan_date'							
					,'tl.principal AS principal'							
					,'tl.term AS term'							
					,'tl.interest_rate AS interest_rate')
		);
		log_message("debug", "restructure query end");
		if ($data['count']>0) $result = 0;
		else $result = 1;
		return $result;	
	}
	
	/**
	 * @desc Checks if  an employee can become a comaker for the new loan
	 * @return 0 if employee can become a comaker for the new loan, 1-otherwise
	 * @param $guarantor_id Employee ID of the comaker
	 * @param $loan_code Loan Code of the new loan
	 * @param $loan_no Loan No of the new loan
	 * @param $employee_id Employee ID of the new Loan applicant
	 * 
	 */
	function checkComaker($guarantor_id, $loan_code, $loan_no, $employee_id, $rest_loan_no=null)
	{
		
		$result = 0;
		
		//counts the number of years the guarantor has rendered
		$years_of_service = $this->member_model->getEmpYearsOfService($guarantor_id);
		
		//returns the number of loans guaranteed by the guarantor
		//$data = $this->loanguarantor_model->get_list(array('guarantor_id'=>$guarantor_id, 'status_flag'=>'1'));
		$no_loans_guaranteed = $this->countLoansGuaranteedByComaker($guarantor_id);	
		
		//count loans guaranteed to one employee
		$loans_guaranteed_to_employee = $this->countLoansGuaranteedToEmployee($guarantor_id, $employee_id);
		
		if ($years_of_service>=2){
			if ($rest_loan_no==null){			//if $rest_loan_no is null, then count loans guaranteed
				if ($no_loans_guaranteed>=4) {
					$result = 1;
				}
				if ($loans_guaranteed_to_employee>=1) $result = 1;
			}
			else {						
				if ($this->checkRestructuredLoan($employee_id, $rest_loan_no, $guarantor_id)==0){ 
					$result = 0;
				}		
				else {
					if ($no_loans_guaranteed>=4) {
						$result = 1;
					}
					if ($loans_guaranteed_to_employee>=1) $result = 1;
				}
			}
		}
		else if($years_of_service<2 && $years_of_service>=1) {
			if ($no_loans_guaranteed>=1) $result = 1;
			else $result = 0;		
		}
		else{
			$result = 1;
		}
		return $result;
	}
	
	/**
	 * @desc Checks if employee's suspension date is more than 6 months from current date
	 * @return 0-suspended more than 6 months ago, 1 - otherwise
	 */
	function checkSuspensionDate($employee_id)
	{
	
		//// [START] there is not enoungh checking: Modified by ASI466 on 20110922
		if (isset($employee_id)&& $employee_id!=""){		
		//// [END] : Modified by ASI466 on 20110922
			$this->member_model->populate(array('employee_id'=>$employee_id));
			
			$this->parameter_model->populate(array('parameter_id'=>'CURRDATE'));
			$data = $this->parameter_model->get(null, 'parameter_value');
			$currDate = $data['list'][0]['parameter_value'];
			$currDate = date("Ymd", strtotime($currDate));
			
			$data = $this->member_model->get(null ,array('suspended_date'));
	
			$suspensionDate = $data['list'][0]['suspended_date'];
			if($suspensionDate == "")
				return 0;			
			
			$temp_date = date("Ym", strtotime(date("Ymd", strtotime($suspensionDate)) . " +6 month"));
			
			$suspensionLiftOffDate = $temp_date."01"; 
			
			
			// 20111029 #0008369 
			// [START] 7642 : Added by ASI466 on 20111104
			$result = $this->tblnpmlsuspension_model->getSixMonthsSuspensionRec($employee_id,$currDate);
			$hasSuspensionRec = 0;
			if($result["count"]!=0){
				$hasSuspensionRec = 1;
			}
			// [END] 7642 : Added by ASI466 on 20111104
			
			//20110726 fix on miniloan suspension issue; check if current date is less than suspension date
			// [START]  : Modified by ASI466 on 20111109
			if(($suspensionLiftOffDate <= $currDate or $currDate < $suspensionDate) and  !$hasSuspensionRec ) return 0;
			// [END]  : Added by ASI466 on 20111109
			else return 1;
		}
		else return 1;
	}
	
	/**
	 * @desc Checks if the employee is a retiree.  
	 * If the employee is a retiree, he cannot loan more than 50% of his capital contribution
	 * @param employee_id
	 * @return 0 - if employee can loan, 1-otherwise
	 */
	function checkRetiree($employee_id, $principal)
	{
		$result = 0;
		
		$company_code = $this->showCompCode($employee_id, 'company_code');
		if ($company_code=='920') {
			/*$capcon_pct = $this->showCapConBalance($employee_id) - (($this->showLoanBalance($employee_id) + $principal)*2);
			if ($capcon_pct>=0)
				$result = 0;
			else $result = 1;*/
			//$wdwlAmtForRetiree  = $this->showCapConBalance($employee_id) - (($this->showLoanBalance($employee_id))*2);
			$wdwlAmtForRetiree  = $this->showCapConBalance($employee_id) * .7;
			$wdwlAmtForRetiree = round($wdwlAmtForRetiree,2);
			log_message('debug', 'wdwlAmtForRetireezzzzz '.$wdwlAmtForRetiree);
			log_message('debug', 'showLoanBalancezzzzz '.$this->showLoanBalance($employee_id));
			log_message('debug', 'principalzzzzz '.$principal);
			if(($principal +  $this->showLoanBalance($employee_id)) > $wdwlAmtForRetiree){
				return 1;
			}
			else{
				return 0;
			}
				
		}
		else $result = 0;
	
		return $result;
	}
	
	function showLoanBalance($employee_id) 
	{
		$data = $this->mloan_model->get(array('employee_id'=>$employee_id, 'status_flag'=>'2', 'close_flag' => '0')
								,array('COALESCE(SUM(principal_balance),0) AS mloan_balance'));
		
		
		$total_loan_balance = $data['list'][0]['mloan_balance'];
		$total_loan_balance = round($total_loan_balance, 2);
		
		return $total_loan_balance;
	}
	
	function showCompCode($employee_id, $company_code)
	{
		$data = $this->member_model->get(array('employee_id'=>$employee_id), array(
			$company_code
		));
		if(!isset($data['list'][0][$company_code])){
			if($company_code=='company_code')
				return '910';
			else if($company_code=='member_status')
				return 'I';
			else 
				return "";
		}
		else
			return $data['list'][0][$company_code];
	}
	
	/**
	 * @desc To retrieve capital contribution balance of an employee
	 * @param employee_id, accounting_period
	 * @return array
	 */
	function showCapConBalance($employee_id){
		$acctgPeriod = date("Ymd", strtotime($this->getParam('ACCPERIOD')));
		$data = $this->capitalcontribution_model->get(array('employee_id' => $employee_id,'accounting_period' => $acctgPeriod) 
			,array('COALESCE(ending_balance,0) AS capcon_balance'));
			
		return $data['list'][0]['capcon_balance'];	
	}
	
	/**
	 * @desc Checks if  employee is qualified for the applied loan
	 * @return 0-if employee can loan, 1-otherwise
	 */
	function checkYearsOfService($employee_id, $loan_code)
	{
		$result = 0;
		$data = $this->getRLoanDtl($loan_code);
		$from_yos = $data['from_yos']; 	
		$to_yos = $data['to_yos'];
		
		$years_of_service = $this->member_model->getEmpYearsOfService($employee_id);
		if ($from_yos==$to_yos){
			if ($years_of_service>=$from_yos) $result = 0;
			else $result = 1;
		}
		else {
			if ($years_of_service>=$from_yos) $result = 0;
			else $result = 1;	
		}
		
		return $result;
	}
	
	/**
	 * @desc check if the loan applied requires the employee to have a 
	 * Capital Contribution Balance that is greater than or equal to 1/3 of his Loan principal Amount
	 * @return 0-if employee can loan, 1-otherwise
	 */
	function checkCapitalContribution($principal, $loan_code, $employee_id, $restructure_amount=0)
	{
		$result = 0;
		$restructure_amount_one_third = 0;
		if($restructure_amount!="" && $restructure_amount > 0)
		{
			$restructure_amount_one_third = $restructure_amount / 3;
		}
		
		$years_of_service = $this->member_model->getEmpYearsOfService($employee_id);
		$data = $this->loancodeheader_model->retrieveLoanCodes(
				array('RH.status_flag' => '1'
					, 'RH.loan_code' => $loan_code)
				,1
				,null
				,array("COALESCE(RL.capital_contribution,0) as capital_contribution ")
				,array("RL.years_of_service DESC")
				,$years_of_service);

		//$data = $this->getRLoanDtl($loan_code, $years_of_service); 
		$loan_capcon = $data['list'][0]['capital_contribution'] ;
		$capcon = $this->showCapConBalance($employee_id);	//gets the total capital contribution
		$ccminbal = $this->getParam('CCMINBAL');	
		
		if ($loan_capcon>0) //if capcon is required
		{	
			//if capital contribution is already below CCMINBAL
			if($capcon < $ccminbal){
				return 2;
			}

			//START Deleted by Kweeny Libutan for 1/3 Capcon Requirement Transition	2013/12/03
			/* $data = $this->mloan_model->get(array('employee_id' => $employee_id)
					,array('SUM(capital_contribution_balance) AS capconbalance')
			);
			$capcon_balance = $data['list'][0]['capconbalance']; //retrieved from m_loan table
			$required_balance = 0; */
			//END Deleted by Kweeny Libutan for 1/3 Capcon Requirement Transition	2013/12/03
			
			//Modified by Kweeny Libutan for 1/3 Capcon Requirement Transition	2013/12/03
			$capcon_balance = $this->mloan_model->getLoanRequirement($employee_id);
			$principal = round($principal/3);
			$capcon_balance = ($capcon_balance + $principal) - $restructure_amount_one_third;
			
			if ($capcon>=$capcon_balance) {
				$result = 0;
			} else {
				$result = 1;
			}
		}
		else 
		{ 
			$result = 0;
		}	
		
		return $result;
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
	 * @desc Update the parameter value of a specific id
	 * @param Parameter id
	 * @return string (parameter value)
	 */
	function updateParam($param_id, $value)
	{	
		$_REQUEST['parameter']['parameter_value'] = $value;
		$this->parameter_model->populate($_REQUEST['parameter']);
		$this->parameter_model->setValue('parameter_value', $_REQUEST['parameter']['parameter_value']);
		$this->parameter_model->update(
									array('parameter_id'=>$param_id)
								);	
	}
	
	function _eir($f_annual_contractual_rate, $f_loan_amount, $f_service_charge, $i_terms, $s_loan_code, $f_initial_interest) {
		$c_excel = new PHPExcel_Calculation_Functions();
		
		$a_values = array();
		
		#check if BSP
		$b_is_bsp = $this->loancodeheader_model->is_bsp($s_loan_code);
		if(!$b_is_bsp) {
			$a_return = array('f_mir' => 0,
							'f_eir' => 0);
	
			return $a_return;
		} 
		
		$f_monthly_contractual_rate = $f_annual_contractual_rate / 12;

		/* $f_initial_interest = ($f_monthly_contractual_rate / 100) * $f_loan_amount; */
		$f_other_charges = $f_initial_interest + $f_service_charge;
		$f_loan_proceeds = $f_loan_amount - $f_other_charges;
						
		$f_principal = $f_loan_amount / $i_terms;
		
		if($i_terms > 12) {
			$i_periods = 12;
		} else {
			$i_periods = $i_terms;
		}
		
		$a_values[] = $f_loan_proceeds;
		
		$f_current_balance = $f_loan_amount;
		for($i = 0; $i < $i_terms; $i++) {
			$f_current_interest = $f_current_balance * ($f_monthly_contractual_rate / 100);
			$f_current_balance = $f_current_balance - $f_principal;
			$f_payment_amount = $f_principal + $f_current_interest;
			$a_values[] = $f_payment_amount * -1;
		}
		
		for($i_guess = 1; $i_guess <= 5; $i_guess++) {
			$f_guess = ($i_guess / 100);
			$f_mir_raw = $c_excel->IRR($a_values, $f_guess);
			if($f_mir_raw != '#VALUE!')
				$i_guess = 6;
		}
		
		$f_mir = round($f_mir_raw * 100, 2);
		$f_eir_raw = pow((1 + $f_mir_raw), 12) - 1;
		$f_eir = round($f_eir_raw * 100, 2);
				
		if(is_infinite($f_eir)) {
			$f_eir = 0;
		}
		
		if(is_infinite($f_mir)) {
			$f_mir = 0;
		}
		
		$a_return = array('f_mir' => $f_mir,
						'f_eir' => $f_eir);

		return $a_return;
	}
	
}

?>