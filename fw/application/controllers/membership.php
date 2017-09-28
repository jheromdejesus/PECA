<?php
##### NRB EDIT START #####
require BASEPATH.'application/my_classes/Classes/PHPExcel/Calculation/Functions.php';
##### NRB EDIT END #####
class Membership extends Asi_controller {

	var $bank_permission = null;
	var $email_permission = null;
	
	function Membership()
	{
		parent::Asi_controller();
		$this->load->model('member_model');
		$this->load->model('beneficiary_model');
		$this->load->model('mloan_model');
		$this->load->model('loanguarantor_model');
		$this->load->model('loancodeheader_model');
		$this->load->model('capitalcontribution_model');
		$this->load->model('mtransaction_model');
		$this->load->model('mloanpayment_model');
		$this->load->model('mloancharges_model');
		$this->load->model('user_model');
		$this->load->model('Group_model');
		$this->load->model('parameter_model');
		$this->load->model('Userpasswords_model');
		##### NRB EDIT START #####
		$this->load->model('loancodeheader_model');
		$this->load->model('glentrydetail_model');
		##### NRB EDIT END #####
		$this->load->library(array('constants','email'));
		set_time_limit(10000);
		$this->bank_permission = parent::_getUserPermission(22);
		$this->email_permission = parent::_getUserPermission(23);
		log_message('debug', "Avs bank permission: ".$this->bank_permission);

	}

	function index(){

	}
	
	/**
	 * @desc Add employee to database
	 */
	function add(){
		 /*  $_REQUEST['member'] = array(
			'employee_id' => '436645645'
			,'last_name' => 'SANTOS'	
			,'first_name' => 'HAHA'	
			,'email_address' => 'haha_cantoneros@yahoo.com'	
			,'hire_date' => '10/15/1989'
		);   */
		$_REQUEST['user'] = 'PECA';
		log_message('debug', "[START] Controller membership:add");
		log_message('debug', "member param exist?:".array_key_exists('member',$_REQUEST));
		
		if (array_key_exists('member',$_REQUEST)) {
			$this->plain_password = $this->_genRandomString();
			$groupResult = $this->Group_model->get(array('group_id' => 'USER'));
			$permission = array_key_exists(0,$groupResult['list'])? $groupResult['list'][0]['permission']:0;
			$this->user_model->setValue('user_id', $_REQUEST['member']['employee_id']);
			$this->user_model->setValue('user_name', $_REQUEST['member']['first_name'].' '.$_REQUEST['member']['last_name']);
			$this->user_model->setValue('password', md5($this->plain_password));
			$this->user_model->setValue('group_id', 'USER');
			$this->user_model->setValue('permission', $permission);
			$this->user_model->setValue('status_flag', '1');
			$this->user_model->setValue('created_by', 'SYSTEM');
			$email_address = isset($_REQUEST['member']['email_address']) ? $_REQUEST['member']['email_address']:'';
			$this->user_model->setValue('email_address', $email_address);
			$this->user_model->setValue('is_admin', '0');
			$checkDuplicate = $this->user_model->checkDuplicateKeyEntry();
			$checkDuplicateUserPassword = $this->Userpasswords_model->checkDuplicateKeyEntry(array(
				'user_id' => $_REQUEST['member']['employee_id'], 'password' => md5($this->plain_password)
			));
			if($checkDuplicate['error_code']==1){
				$result['error_code'] = 1;
				$result['error_message'] = $checkDuplicate['error_message'];
				echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';
			}
			else if($checkDuplicateUserPassword['error_code']==1){
				echo '{"success":false,"msg":"Duplicate password generated, please try again.","error_code":"2"}';
			}
			else{
				$result = $this->user_model->insert();
				
				$this->Userpasswords_model->setValue('user_id', $_REQUEST['member']['employee_id']);
				$this->Userpasswords_model->setValue('password', md5($this->plain_password));
				$this->Userpasswords_model->setValue('status_flag', 1);
				$this->Userpasswords_model->setValue('created_by', 'SYSTEM');
				$this->Userpasswords_model->insert();
				
				$this->member_model->populate($_REQUEST['member']);
				$this->member_model->setValue('status_flag', '1');
				$this->member_model->setValue('created_by', $_REQUEST['user']);
				
				if($this->user_info['group_id'] == 'OPERATIONS'){
					$this->member_model->setValue('member_status', 'I');
				}

				$checkDuplicate = $this->member_model->checkDuplicateKeyEntry();
		
				if($checkDuplicate['error_code'] == 1){
					$result['error_code'] = 1;
					$result['error_message'] = $checkDuplicate['error_message'];
				}
				else{
					$result = $this->member_model->insert();
				} 		
						
				if($result['error_code'] == 0){
				  if($_REQUEST['member']['member_status']=="A" && $email_address!=""){
					$email_success = $this->sendEmail($_REQUEST['member']['employee_id'], $_REQUEST['member']['email_address'], $_REQUEST['member']['first_name'].' '.$_REQUEST['member']['last_name']);
				  }
				  echo "{'success':true,'msg':'Data successfully saved.'}";
				} else
				  echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';
			} 
		} else
			echo "{'success':false,'msg':'Data was NOT successfully saved.','error_code':'2'}";
			
		log_message('debug', "[END] Controller loan_code:addHdr");
	}
	
		function sendEmail($user_id, $email, $user_name) {
		$email_message = '';
		$this->email->to($email);
	    //$this->email->cc('epama@asi-dev2.com');
		$this->email->from('admin@peca.com');
		$this->email->subject($this->constants->messages['115'] . $user_name);

		$email_message = 'Hi ' . $user_name . "! <br/><br/>";
		$email_message .= $this->constants->messages['118'] . " <br/><br/>";
		$email_message .= $this->constants->messages['114'] . $email . " <br/>";
		$email_message .= $this->constants->messages['113'] . $this->plain_password. " <br/>";
		$email_message .= "<br/>";
		$email_message .= "To login, visit: <a href=\"".base_url()."login\">".base_url()."login </a>";
		
		$this->email->Message($email_message);

		if ($this->email->send()) {
			return true;
		} else {
			return false;
		}
	}
	
	function _genRandomString() {
		$length = 10;
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		$string = '';    

		for ($p = 0; $p < $length; $p++) {
			$string .= $characters[mt_rand(0, strlen($characters)-1)];
		}

		return $string;
	}
	/**
	 * @desc Retrieve all details of a single employee
	 */
	function show(){
		//$_REQUEST['member']['employee_id'] = '00421526';
		if (array_key_exists('member',$_REQUEST)){ 
			$this->member_model->setValue('employee_id', $_REQUEST['member']['employee_id']);
			$this->member_model->setValue('status_flag', '1');
		}
		$data = $this->member_model->get(null, array("
				employee_id 
				,last_name
				,first_name
				,middle_name
				,DATE_FORMAT(member_date,'%m%d%Y') AS member_date
				,bank_account_no
				,bank
				,TIN
				,DATE_FORMAT(hire_date,'%m%d%Y') AS hire_date
				,DATE_FORMAT(work_date,'%m%d%Y') AS work_date
				,department
				,position
				,company_code
				,email_address
				,office_no
				,mobile_no
				,home_phone
				,address_1
				,address_2
				,address_3
				,DATE_FORMAT(birth_date,'%m%d%Y') AS birth_date
				,civil_status
				,gender
				,spouse
				,non_member
				,guarantor
				,beneficiaries
				,member_status
				,bank
				,bank_account_no"
		));
		//$data['bank_info_permission'] = 'okay';
		//foreach($data as $item){
		//	log_message('debug', 'data[]: '.$item);
		//}
		
		//email permission checking
		if($this->email_permission == '0') {
			$data['list'][0]['email_info_permission'] = 'false';
			log_message('debug', 'Avs->user permission: false');
		} else if($this->email_permission == '1') {
			$data['list'][0]['email_info_permission'] = 'true';
			log_message('debug', 'Avs->user permission: true');
		}
		//bank permission checking
		
		if($this->bank_permission == '0') {
			$data['list'][0]['bank_info_permission'] = 'false';
			log_message('debug', 'Avs->user permission: false');
		} else if($this->bank_permission == '1') {
			$data['list'][0]['bank_info_permission'] = 'true';
			log_message('debug', 'Avs->user permission: true');
		}
		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
		));
	}
	/**
	 * @desc Retrieve all details of a single employee's loan
	 */
	function showLoanInfo(){

		##### NRB EDIT START #####		
		//used for computing the MIR and EIR
		//load models
        $this->load->model('loancodeheader_model');
        $this->load->model('mloan_model');
        $this->load->model('member_model');
        $this->load->model('parameter_model');
        $this->load->model('mloanpayment_model');

        //load helper
        $this->load->helper('compute_mir_eir');
        ##### NRB EDIT END #####
		
		$param['ml.loan_no'] = $_REQUEST['loan_no'];
		
		$data = $this->mloan_model->getLoanList(
		$param
		,null
		,null
			,array("ml.loan_no AS loan_no"
			,"ml.restructure_amount AS restructure_amount"
			,"ml.employee_id"
			,"ml.restructure_no AS restructure_no"
			,"ml.loan_code AS loan_code"
			,"rl.loan_description AS loan_description"
			##### NRB EDIT START #####
			,"rl.bsp_computation AS bsp_computation"
			##### NRB EDIT END #####
			,"DATE_FORMAT(ml.loan_date,'%m%d%Y') AS loan_date"
			,"ml.principal_balance AS principal_balance"
			,"ml.principal AS principal"
			,"ml.term AS term"
			,"ml.interest_rate AS rate"
			,"ml.initial_interest AS initial_interest"
			,"ml.employee_interest_total AS employee_interest_total"
			,"ml.employee_interest_amortization AS employee_interest_amortization"
			,"DATE_FORMAT(ml.amortization_startdate,'%m%d%Y') AS amortization_startdate"
			,"ml.employee_principal_amort AS employee_principal_amortization"
			,"ml.loan_proceeds AS loan_proceeds"
			,"ml.mri_fip_amount AS mri_fip_amount"
			,"ml.broker_fee_amount AS broker_fee_amount"
			,"ml.government_fee_amount AS government_fee_amount"
			,"ml.other_fee_amount AS other_fee_amount"
			,"ml.service_fee_amount AS service_fee_amount"
			,"ml.capital_contribution_balance AS capital_contribution_balance"
			,"ml.company_interest_rate AS company_interest_rate"
			,"ml.company_interest_total AS company_interest_total"
			,"ml.company_interest_amort AS company_interest_amort"
			,"ml.pension AS pension"
			,"ml.mri_due_amount AS mri_due_amount"
			,"DATE_FORMAT(ml.mri_due_date,'%m%d%Y') AS mri_due_date"
			,"ml.fip_due_amount AS fip_due_amount"
			,"DATE_FORMAT(ml.fip_due_date,'%m%d%Y') AS fip_due_date"
			##### NRB EDIT START #####
			,"ml.effective_annual_interest_rate AS effective_annual_interest_rate"
			,"ml.effective_monthly_interest_rate AS effective_monthly_interest_rate"
			,"ml.mri_fip_provider AS mri_fip_provider"
			,"ml.other_charges_amount AS other_charges_amount"
			,"ml.other_charges_rate AS other_charges_rate"
			##### NRB EDIT END #####
			)
		,null
		,null
		);

		##### NRB EDIT START #####		
		if(count($data['list'])>0) {
			$data['list'][0]['nocomaker'] = $this->coMakerCount($data['list'][0]['employee_id'],$data['list'][0]['loan_code']);
			
			#get account name for mri/fip provider
			if($data['list'][0]['mri_fip_provider']) {
				$a_provider = $this->glentrydetail_model->retrieveGLEntryAccountName($data['list'][0]['mri_fip_provider']);
				$data['list'][0]['mri_fip_provider'] = $a_provider[0]['account_name'];
			}

			//get accurate MIR and EIR
			$a_MIRandEIR = array();
			$a_MIRandEIR = getMIRandEIR( $data['list'][0]['loan_no'] );

			//save old MIR and EIR
			$data['list'][0]['old_effective_monthly_interest_rate'] = $data['list'][0]['effective_monthly_interest_rate'];
			$data['list'][0]['old_effective_annual_interest_rate'] = $data['list'][0]['effective_annual_interest_rate'];

			//apply new MIR and EIR
			$data['list'][0]["effective_monthly_interest_rate"] = number_format((float)($a_MIRandEIR['MIR']*100), 2, '.', ',');
			$data['list'][0]["effective_annual_interest_rate"] = number_format((float)($a_MIRandEIR['EIR']*100), 2, '.', ',');
			
			
			log_message('debug', "outP&GSUBSIDY: " . $data['list'][0]["principal_balance"]  * ($data['list'][0]["company_interest_rate"]/100) / 12);
			log_message('debug', "outP&GSUBSIDY: " . $data['list'][0]['bsp_computation']);
			log_message('debug', "outP&GSUBSIDY: " . $data['list'][0]['principal_balance']);
			log_message('debug', "outP&GSUBSIDY: " . $data['list'][0]['company_interest_rate']);
			//[START] Added by Vincent Sy for 8th Enhancement 2013/07/05
			if($data['list'][0]['bsp_computation'] == 'Y' && strtolower(substr($data['list'][0]['loan_code'], 0, 1)) == 'h' && $data['list'][0]["company_interest_rate"] > 0)
			{
				log_message('debug', "inP&GSUBSIDY: " . $data['list'][0]["principal_balance"]  * ($data['list'][0]["company_interest_rate"]/100) / 12);
				log_message('debug', "inP&GSUBSIDY: " . $data['list'][0]['bsp_computation']);
				log_message('debug', "inP&GSUBSIDY: " . $data['list'][0]['company_interest_rate']);
				$data['list'][0]["company_interest_amort"] = $data['list'][0]["principal_balance"]  * ($data['list'][0]["company_interest_rate"]/100) / 12;
			}
			//[END] Added by Vincent Sy for 8th Enhancement 2013/07/05
		}
/*
		if(count($data['list'])>0) 
			$data['list'][0]['nocomaker'] = $this->coMakerCount($data['list'][0]['employee_id'],$data['list'][0]['loan_code']);
*/
		##### NRB EDIT END #####

		echo json_encode(array(
			'success' => true
		,'data' => $data['list']
		,'total' => $data['count']
		,'query' => $data['query']
		));
		
	}	
	
	
	/**
	 * @desc Lists all employees
	 */
	function read(){
		//if there is employee id, discard other parameters
		if(array_key_exists('employee_id', $_REQUEST) && $_REQUEST['employee_id']!= ""){
			$param['employee_id LIKE'] =  $_REQUEST['employee_id']."%";
		}
		else{
			if(array_key_exists('first_name', $_REQUEST) && $_REQUEST['first_name']!= "")
				$param['first_name LIKE'] =  $_REQUEST['first_name']."%";
			if(array_key_exists('last_name', $_REQUEST) && $_REQUEST['last_name']!= "")
				$param['last_name LIKE'] =  $_REQUEST['last_name']."%";
		}
		
		//added 3-11-2011 so that blank employee id, first name and last name returns nothing
		//if(isset($param)){
			$param['status_flag'] = '1';
			
			$data = $this->member_model->get_list(
			$param,
			array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
			array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
			array('employee_id' 
				,'last_name'
				,'first_name'
				,'middle_name'),
			'employee_id'
			);
		//}
		//else{
		//	$data['list'] = array();
		//	$data['count'] = 0;
		//	$data['query'] = "";
		//}
		
		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
			));
	}
	/**
	 * @desc Lists all loan payments of a loan
	 */
	function readLoanPayment(){		
		
		//$_REQUEST['loan_no'] = '100';
		
		/*$param['loan_no'] = $_REQUEST['loan_no'];
		$param['ml.status_flag'] = '2';
		$param['ml.source <>'] = 'B';
		$param['tt.reference IS'] = NULL;*/
		
		
		/*$param = "loan_no = '" . $_REQUEST['loan_no'] . "'" 
			. " AND ml.status_flag = '2'"
			. " AND ml.source <> 'B'"
			. " AND tt.reference IS NULL";*/
		
		##### NRB EDIT START #####
		#get loan code
		$m_loan_code = $this->mloan_model->get_loan_property($_REQUEST['loan_no'], 'loan_code');
		
		#check if loan type bsp
		$b_isbsp = $this->loancodeheader_model->is_bsp($m_loan_code);
		
		#get principal amount 
		$m_principal = $this->mloan_model->get_loan_property($_REQUEST['loan_no'], 'loan_code');
		
		#get init
		$a_loan = $this->mloan_model->get_loan_payment_initial($_REQUEST['loan_no']);
		$s_init_balance = $a_loan['principal'];
		$s_init_interest = round(($a_loan['principal'] * ($a_loan['interest_rate'] / 100)) / 12);
		##### NRB EDIT START #####
		
		$i_start = array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null;
		$i_limit = array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null;
		
		##### NRB EDIT START #####
		if($b_isbsp) {
			if($i_start == 0) {
				$i_limit = $i_limit - 1;
			} else {
				$i_start = $i_start - 1;
			}
		}
		##### NRB EDIT END #####
		
		$data = $this->mloanpayment_model->getLoanPaymentList(
		$_REQUEST['loan_no'],
		##### NRB EDIT START #####
		$i_start,
		$i_limit,
/*
		array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
		array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
*/
		##### NRB EDIT END #####
		array("DATE_FORMAT(ml.payment_date,'%m%d%Y') AS payment_date"
			,"ml.amount"
			,"ml.interest_amount as interest"
			,"rl.transaction_description as transaction_description"
			,"ml.balance as balance"
		)
		##### NRB EDIT START #####
		/*, "ml.payment_date ASC"*/
		 , "ml.payment_date DESC"
		##### NRB EDIT END #####
		, $_REQUEST['loan_no']
		);
		
		##### NRB EDIT START #####
		if($b_isbsp) {
			if($i_start == 0) {
				#recalculate interest
				/* $a_list = $data['list'];
				for($i = 0; $i < count($a_list); $i++) {
					$s_temp_interest = round(($a_list[$i]['balance'] * ($a_loan['interest_rate'] / 100)) / 12);
					$a_list[$i]['interest'] = $s_temp_interest;
				}
				
				$data['list'] = $a_list; */
			
				$a_first = array(
						'amount' => 0
						,'balance' => $s_init_balance
						,'interest' => $s_init_interest
						,'payment_date' => ''
						,'payor_id' => ''
						,'reference' => NULL
						,'transaction_description' => ''
					);
				
				$a_original_list = $data['list'];
				//$a_original_list = array_reverse($a_original_list);
				array_push($a_original_list, $a_first);
				//[START] 7th Enhancement
				//$a_original_list = array_reverse($a_original_list);
				//[END] 7th Enhancement
	
				$data['list'] = $a_original_list;
			}
			
			$data['count'] = $data['count'] + 1;
		}
		//[START] 7th Enhancement
		/*else {
			$data['list'] = array_reverse($data['list']);
		}*/
		//[END] 7th Enhancement
		##### NRB EDIT END #####
		
		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
			));
	}
	
	/**
	 * @desc Lists all loan charges of a loan
	 */	
	function readLoanCharges(){		
		
		//$_REQUEST['loan_no'] = '100';
		$param['loan_no'] = $_REQUEST['loan_no'];
		
		$data = $this->mloancharges_model->getLoanChargesList(
		$param,
		array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
		array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
		array("rl.transaction_description AS transaction_description"
			,"ml.amount"
		));
		
		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
			));
	}
	
	/**
	 * @desc Lists all loan charges of a loan
	 */	
	function readLoanComakers(){		
		
		//$_REQUEST['loan_no'] = '3177';
		$param['loan_no'] = $_REQUEST['loan_no'];
		
		$data = $this->member_model->getLoanComakersList(
		$param,
		array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
		array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
		array("me.employee_id"
			 ,"me.last_name"
			 ,"me.first_name"
			 ,"me.middle_name"
		));
		
		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
			));
	}
	
	/**
	 * @desc Updates employee information
	 */
	function update(){
		 /*  $_REQUEST['member'] = array(
			'employee_id' => '436645645'
			,'last_name' => 'SANTOS'	
			,'first_name' => 'HoHo'	
			,'email_address' => 'hoho_cantoneros@yahoo.com'	
			,'hire_date' => '10/15/1989'
		);   */
		log_message('debug', "[START] Controller membership:update");
		log_message('debug', "member param exist?:".array_key_exists('member',$_REQUEST));
		if (array_key_exists('member',$_REQUEST)) {
			if(array_key_exists('email_address',$_REQUEST['member'])){
				$return = $this->member_model->get(array('employee_id' => $_REQUEST['member']['employee_id']), array('email_address', 'last_name', 'first_name', 'member_status', 'company_code','non_member'));
				$prevEmail = $return['list'][0]['email_address'];
				$prevFirst = $return['list'][0]['first_name'];
				$prevLast = $return['list'][0]['last_name'];
				$prevMemberStatus = $return['list'][0]['member_status'];
				$prevCompanyCode = $return['list'][0]['company_code'];
				$prevNonMember = $return['list'][0]['non_member'];
				
				if(($prevEmail!= $_REQUEST['member']['email_address'])||($prevFirst!= $_REQUEST['member']['first_name'])||($prevLast!= $_REQUEST['member']['last_name'])){
					$this->user_model->setValue('user_id', $_REQUEST['member']['employee_id']);
					$this->user_model->setValue('user_name', $_REQUEST['member']['first_name'].' '.$_REQUEST['member']['last_name']);
					$this->user_model->setValue('modified_by', 'SYSTEM');
					$this->user_model->setValue('email_address', $_REQUEST['member']['email_address']);
					$result = $this->user_model->update();
					if($result['affected_rows'] < 0){
						echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
					} else{
						
						$this->member_model->populate($_REQUEST['member']);
						
						$getInvalidDateResult = $this->getInvalidDate($_REQUEST['member']['member_status'], array_key_exists('company_code', $_REQUEST['member'])?$_REQUEST['member']['company_code']:$prevCompanyCode, $_REQUEST['member']['non_member'], $prevMemberStatus, $prevCompanyCode, $prevNonMember);
						if($getInvalidDateResult['change_value']){
							$this->member_model->setValue('invalid_date', $getInvalidDateResult['value']);
						}
						$result = $this->member_model->update();
						
						if($result['affected_rows'] <= 0){
							echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
						} else
							echo "{'success':true,'msg':'Data successfully saved.'}";
					}
				} else{
					$this->member_model->populate($_REQUEST['member']);
					
					$getInvalidDateResult = $this->getInvalidDate($_REQUEST['member']['member_status'], array_key_exists('company_code', $_REQUEST['member'])?$_REQUEST['member']['company_code']:$prevCompanyCode, $_REQUEST['member']['non_member'], $prevMemberStatus, $prevCompanyCode, $prevNonMember);
					if($getInvalidDateResult['change_value']){
						$this->member_model->setValue('invalid_date', $getInvalidDateResult['value']);
					}
					
					$result = $this->member_model->update();
					
					if($result['affected_rows'] <= 0){
						echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
					} else
						echo "{'success':true,'msg':'Data successfully saved.'}";
				}
			} else{
				$return = $this->member_model->get(array('employee_id' => $_REQUEST['member']['employee_id']), array('member_status', 'company_code', 'non_member'));
				$prevMemberStatus = $return['list'][0]['member_status'];
				$prevCompanyCode = $return['list'][0]['company_code'];
				$prevNonMember = $return['list'][0]['non_member'];
				
				$this->member_model->populate($_REQUEST['member']);
				
				$getInvalidDateResult = $this->getInvalidDate($_REQUEST['member']['member_status'], array_key_exists('company_code', $_REQUEST['member'])?$_REQUEST['member']['company_code']:$prevCompanyCode, $_REQUEST['member']['non_member'], $prevMemberStatus, $prevCompanyCode, $prevNonMember);
				if($getInvalidDateResult['change_value']){
					$this->member_model->setValue('invalid_date', $getInvalidDateResult['value']);
				}
				
				$result = $this->member_model->update();
				if($result['affected_rows'] <= 0){
					echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
				} else
					echo "{'success':true,'msg':'Data successfully saved.'}";
			}
		} else
			echo "{'success':false,'msg':'Data was NOT successfully saved.'}";

		log_message('debug', "[END] Controller loan_code:updateHdr");
	}
	
	function getInvalidDate($new_member_status, $new_company_code, $new_non_member, $old_member_status, $old_company_code, $old_non_member){
		
		//if member status is inactive set status flag for user to 0 in user table
		if($new_member_status=="I"){
			$this->user_model->populate(array()); //clear user model data first
			$this->user_model->setValue('status_flag', '0');
			$this->user_model->update(array('user_id'=>$_REQUEST['member']['employee_id']));
		}
		//if member status is active set status flag for user to 1 in user table
		else if($new_member_status=="A"){
			$this->user_model->populate(array()); //clear user model data first
			$this->user_model->setValue('status_flag', '1');
			$this->user_model->update(array('user_id'=>$_REQUEST['member']['employee_id']));
		}
		
		if(($old_member_status == 'I' || $old_company_code == '910' || $old_company_code == '920') && ($old_non_member =="" || $old_non_member == "N")){
			$old_state_is_valid = false;
		}
		else{
			$old_state_is_valid = true;
		}
		
		if(($new_member_status == 'I' || $new_company_code == '910' || $new_company_code == '920') && ($new_non_member =="" || $new_non_member == "N")){
			$new_state_is_valid = false;
		}
		else{
			$new_state_is_valid = true;
		}
		
		$value = "";
		$change_value = false;
		
		if($old_state_is_valid && $new_state_is_valid){
			 //do nothing
		}
		else if(!$old_state_is_valid && $new_state_is_valid){
			$change_value = true;
			$value = ""; //clear invalid date
		}
		else if(!$old_state_is_valid && !$new_state_is_valid){
			 //do nothing
		}
		else if($old_state_is_valid && !$new_state_is_valid){
			$change_value = true;
			$value = date('Ymd', strtotime($this->parameter_model->getParam('CURRDATE')));
		}
		
		return array('change_value'=> $change_value, 'value'=> $value);
	}
	
	function deleteBeneficiaries(){
		// $_REQUEST['member']['employee_id'] = '100';
		
		log_message('debug', "[START] Controller membership:deleteBeneficiaries");
		log_message('debug', "member param exist?:".array_key_exists('member',$_REQUEST));
		
		if (array_key_exists('member',$_REQUEST)) {
			
			$this->beneficiary_model->setValue('member_id', $_REQUEST['member']['employee_id']);
			$result = $this->beneficiary_model->delete();
			
			if($result['affected_rows'] <= 0){
				echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
			} else {
				echo "{'success':true,'msg':'Data successfully deleted.'}";
			}
		} else {
			echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
		}

		log_message('debug', "[END] Controller membership:deleteBeneficiaries");
	}
	
	/**
	 * @desc Add beneficiaries
	 */
	function addBeneficiary(){
		/*$_REQUEST['member']['employee_id'] = '123';
		$_REQUEST['data'] = '[
			{"beneficiary":"Juan dela Cruz"
			,"relationship": "P"
			,"beneficiary_address": "Makati City"	
			}
			,{"beneficiary":"Joana Magsalin"
			,"relationship": "P"
			,"beneficiary_address": "Makati City"	
			}
			,{"beneficiary":"Ella Cruz"
			,"relationship": "P"
			,"beneficiary_address": "Makati City"	
			}
		]'; 
		$_REQUEST['user'] = 'PECA';*/
		
		if(array_key_exists('data',$_REQUEST)){
			$params = json_decode(stripslashes($_REQUEST['data']),true);
			$sequence_no = 1;
			if(substr($_REQUEST['data'],0,1)=='{'){
				$params['member_id'] = $_REQUEST['member']['employee_id'];
				$params['sequence_no'] = $sequence_no;
				$params['status_flag'] = '1';
				$params['created_by'] = $_REQUEST['user'];
				$this->beneficiary_model->populate($params);
				
				//existing deletes if beneficiary name is blank
				if(trim($params['beneficiary'])!=""){
					$this->beneficiary_model->insert();
				}	
			}
			else{
				foreach($params as $value){
					$value['member_id'] = $_REQUEST['member']['employee_id'];
					$value['sequence_no'] = $sequence_no;
					$value['status_flag'] = '1';
					$value['created_by'] = $_REQUEST['user'];
					$this->beneficiary_model->populate($value);
					
					//existing deletes if beneficiary name is blank
					if(trim($value['beneficiary'])!=""){
						$this->beneficiary_model->insert();
						$sequence_no++;	
					}
				}
			}
		}
	}
	
	/**
	 * @desc To to retrieve all loans of an employee
	 */
	function readEmployeeLoanList(){
		//$_REQUEST['member']['employee_id'] = '01518520';
		
		$param['ml.employee_id'] = $_REQUEST['employee_id'];
//		$param['ml.principal_balance >'] = '0';
		$param['ml.status_flag'] = '2';	
	
		$data = $this->mloan_model->getLoanList(
		$param
		,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
		,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
			,array("ml.loan_no AS loan_no"
			,"ml.loan_code AS loan_code"
			,"rl.loan_description AS loan_description"
			##### NRB EDIT START #####
			,"rl.bsp_computation AS bsp_computation"
			##### NRB EDIT END #####
			,"DATE_FORMAT(ml.loan_date,'%m%d%Y') AS loan_date"
			,"ml.principal AS principal"
			,"ml.term AS term"
			,"ml.interest_rate AS rate"
			,"ml.employee_interest_amortization AS interest_amortization"
			,"ml.employee_principal_amort AS principal_amortization"
			,"ml.principal_balance AS principal_balance"
			,"(ml.employee_interest_amortization + ml.employee_principal_amort) AS monthly_amortization"
			)
		,'ml.loan_code, ml.loan_date DESC'
		,null
		);
		
		##### NRB EDIT START #####
		#iterate list
		for($i = 0; $i < sizeof($data['list']); $i++) {
			$a_detail = $data['list'][$i];
			#get loan type if bsp			
			if($a_detail['bsp_computation'] == 'Y') {
				#interest amortization
				$f_interest_amortization = ($a_detail['principal_balance'] * ($a_detail['rate'] / 100)) / 12;
				#principal amortization
				$f_principal_amortization = $a_detail['principal'] / $a_detail['term'];
				#monthly amortization
				$f_monthly_amortization = $f_interest_amortization + $f_principal_amortization;
				
				$a_detail['interest_amortization'] = (string) round($f_interest_amortization);
				$a_detail['principal_amortization'] = (string) round($f_principal_amortization);
				$a_detail['monthly_amortization'] = (string) round($f_monthly_amortization);
				
				$data['list'][$i] = $a_detail;
			}
			
		}
		##### NRB EDIT END #####
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
	function readEmployeeLoanListSum(){
		//$_REQUEST['member']['employee_id'] = '01518520';
		
		$param['ml.employee_id'] = $_REQUEST['employee_id'];
//		$param['ml.principal_balance >'] = '0';
		$param['ml.status_flag'] = '2';	
	
		$data = $this->mloan_model->getLoanList(
		$param
		,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
		,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
			,array("ml.loan_no AS loan_no"
			,"ml.loan_code AS loan_code"
			,"rl.loan_description AS loan_description"
			##### NRB EDIT START #####
			,"rl.bsp_computation AS bsp_computation"
			##### NRB EDIT END #####
			,"DATE_FORMAT(ml.loan_date,'%m%d%Y') AS loan_date"
			,"ml.principal AS principal"
			,"ml.term AS term"
			,"ml.interest_rate AS rate"
			,"ml.employee_interest_amortization AS interest_amortization"
			,"ml.employee_principal_amort AS principal_amortization"
			,"ml.principal_balance AS principal_balance"
			,"(ml.employee_interest_amortization + ml.employee_principal_amort) AS monthly_amortization"
			)
		,'ml.loan_code, ml.loan_date DESC'
		,null
		);
		
		##### NRB EDIT START #####
		#iterate list
		for($i = 0; $i < sizeof($data['list']); $i++) {
			$a_detail = $data['list'][$i];
			#get loan type if bsp			
			if($a_detail['bsp_computation'] == 'Y') {
				#interest amortization
				$f_interest_amortization = ($a_detail['principal_balance'] * ($a_detail['rate'] / 100)) / 12;
				#principal amortization
				$f_principal_amortization = $a_detail['principal'] / $a_detail['term'];
				#monthly amortization
				$f_monthly_amortization = $f_interest_amortization + $f_principal_amortization;
				
				$a_detail['interest_amortization'] = (string) round($f_interest_amortization);
				$a_detail['principal_amortization'] = (string) round($f_principal_amortization);
				$a_detail['monthly_amortization'] = (string) $a_detail['principal_amortization'] + $a_detail['interest_amortization'];
				
				$data['list'][$i] = $a_detail;
			}
			
		}
		##### NRB EDIT END #####
		
		echo json_encode(array(
			'success' => true
			,'data' => $data['list']
			,'total' => $data['count']
			,'query' => $data['query']
		));
	}
	
	
	/**
	 * @desc To to retrieve the sum of all loans of an employee
	 */
	/* function readEmployeeLoanListSum(){
		//$_REQUEST['employee_id'] = '01518520';
		
		$param['ml.employee_id'] = $_REQUEST['employee_id'];
		$param['ml.principal_balance >'] = '0';
		$param['ml.status_flag >'] = '0';

		$data = $this->mloan_model->getLoanList(
		$param
		,null
		,null
			,array("SUM(ml.employee_interest_amortization) AS interest_amortization"
			,"SUM(ml.employee_principal_amort) AS principal_amortization"
			,"SUM(ml.principal_balance) AS principal_balance"
			,"SUM(ml.employee_interest_amortization + ml.employee_principal_amort) AS monthly_amortization"
			)
		,null
		,null
		);
		
		echo json_encode(array(
			'success' => true
		,'data' => $data['list']
		,'total' => $data['count']
		,'query' => $data['query']
		));
	}		 */
	/**
	 * @desc Read all loans guaranteed by the employee
	 */
	function readEmployeeGuaranteedLoans(){
		//$_REQUEST['member']['employee_id'] = '00421526';
		
		$param['tlg.guarantor_id'] = $_REQUEST['employee_id'];

		$data = $this->loanguarantor_model->getMLoanGuaranteedWithName(
		$param
		,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
		,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
		,array("ml.loan_no AS loan_no"
			,"ml.loan_code AS loan_code"
			,"rl.loan_description AS loan_description"
			,"DATE_FORMAT(ml.loan_date,'%m%d%Y') AS loan_date"
			,'me.last_name as last_name'
			,'me.first_name as first_name'
			,'me.middle_name as middle_name'
			,"ml.principal AS principal"
			,"ml.employee_interest_amortization AS interest_amortization"
			,"ml.employee_principal_amort AS principal_amortization"
			,"ml.principal_balance AS principal_balance"
		)
		,'loan_code, loan_date DESC'
		);
		

		echo json_encode(array(
		'success' => true
		,'data' => $data['list']
		,'total' => $data['count']
		,'query' => $data['query']
		));
	}
	/**
	 * @desc Read sum of all loans guaranteed by the employee
	 */
	function readEmployeeGuaranteedLoansSum(){
		//$_REQUEST['member']['employee_id'] = '00421526';
		//$_REQUEST['employee_id'] = '01518520';
		$param['tlg.guarantor_id'] = $_REQUEST['employee_id'];
		$param['ml.principal_balance >'] = '0';

		$data = $this->loanguarantor_model->getMLoanGuaranteedWithName(
		$param
		,null
		,null
		,array("SUM(ml.employee_interest_amortization) AS interest_amortization"
			,"SUM(ml.employee_principal_amort) AS principal_amortization"
			,"SUM(ml.principal_balance) AS principal_balance"
			,"SUM(ml.employee_interest_amortization + ml.employee_principal_amort) AS monthly_amortization"
		)
		,null
		);
		

		echo json_encode(array(
		'success' => true
		,'data' => $data['list']
		,'total' => $data['count']
		,'query' => $data['query']
		));
	}	
	function readTransHistory(){
		//$_REQUEST['member']['employee_id'] = '01517076';
		//$_REQUEST['employee_id'] = '01518520';
		//$_REQUEST['start'] = '0';
		//$_REQUEST['limit'] = '20';
		$data_date = $this->mtransaction_model->getStartingDate($_REQUEST['employee_id']);
		
		$str_date = date("Ym", strtotime($data_date['list'][0]['transaction_date']));

		$param = "employee_id = '".$_REQUEST['employee_id']."' AND accounting_period LIKE '$str_date%'";
		
		$data = $this->capitalcontribution_model->get_list(
			$param
			,null
			,1
			,array('beginning_balance')
			,'created_by'
		);
		
		if(isset($data['list'][0]['beginning_balance'])){
			$balance = $starting = $data['list'][0]['beginning_balance'];
		}
		else{
			$balance = $starting = 0;
		}
		
		//$param = "t.employee_id = '".$_REQUEST['employee_id']."' AND r.capcon_effect IN (-1,1) ";
		$data = $this->mtransaction_model->getHistTranList(
			$_REQUEST['employee_id']
			,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
			,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
			,array(
				"DATE_FORMAT(t.transaction_date,'%m%d%Y') AS transaction_date"
				,"t.transaction_code AS transaction_code"
				,"t.transaction_amount AS transaction_amount"
				,"r.capcon_effect AS capcon_effect"
				)
			,'t.transaction_date, t.modified_date'
		);
		if( array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : 0 > 0){
			$balanceData = $this->mtransaction_model->getHistTranList(
				$_REQUEST['employee_id']
				,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : 0
				,0
				,array(
					"t.transaction_amount AS transaction_amount"
					,"r.capcon_effect AS capcon_effect"
					)
				,'t.transaction_date, t.modified_date'
			);
			
			foreach($balanceData['list'] as $key => $val){
				if($val['capcon_effect']==1){
					$balance = $balance + $val['transaction_amount'];
				}
				else{
					$balance = $balance - $val['transaction_amount'];
				}
			}
		}
		
		foreach($data['list'] as $key => $val){
			if($val['capcon_effect']==1){
				$data['list'][$key]['balance'] = $balance = $balance + $val['transaction_amount'];
			}
			else{
				$data['list'][$key]['transaction_amount'] = $val['transaction_amount'];
				$data['list'][$key]['balance'] = $balance = $balance - $val['transaction_amount'];
			}
			
		}
		
		if(count($data['list'])>0 && $_REQUEST['start'] == '0'){
		$data['list'] = array_merge(array(0 => array(
			"transaction_date" => ""
			,"transaction_code" => ""
			,"transaction_amount" => $starting
			,"capcon_effect" => ""
			,"balance" => $starting))
		, $data['list']);
		$data['count'] ++;
		}
		
		echo json_encode(array(
		'success' => true
		,'data' => $data['list']
		,'total' => $data['count']
		,'query' => $data['query']
		));
	}
	
	function readTransHistoryDesc(){
		/*$_REQUEST['employee_id'] = '01517412';
		$_REQUEST['limit'] = 20;
		$_REQUEST['start'] = 0;*/
		$balance = $this->mtransaction_model->getMTransBalance($_REQUEST['employee_id']);
		
		$data_date = $this->mtransaction_model->getStartingDate($_REQUEST['employee_id']);
		
		$str_date = date("Ym", strtotime($data_date['list'][0]['transaction_date']));

		$param = "employee_id = '".$_REQUEST['employee_id']."' AND accounting_period LIKE '$str_date%'";
		
		$data = $this->capitalcontribution_model->get_list(
			$param
			,null
			,1
			,array('beginning_balance')
			,'created_by'
		);
		
		if(isset($data['list'][0]['beginning_balance'])){
			$starting = $data['list'][0]['beginning_balance'];
		}
		else{
			$starting = 0;
		}
		
		$balance = $balance + $starting;
		$data = $this->mtransaction_model->getHistTranListDesc(
			$_REQUEST['employee_id']
			,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
			,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
			,array(
				"DATE_FORMAT(t.transaction_date,'%m%d%Y') AS transaction_date"
				,"t.transaction_code AS transaction_code"
				,"t.transaction_amount AS transaction_amount"
				,"r.capcon_effect AS capcon_effect"
				)
			,'t.transaction_date, t.modified_date'
		);
			
		$last_page = false;
		if($data['count']==0){
			$last_page = true;
		}
		else if(ceil($data['count']/20) == (($_REQUEST['start']/20) + 1)){
			$last_page = true;
		}
		
		if( array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : 0 > 0){
			$balanceData = $this->mtransaction_model->getHistTranListDesc(
				$_REQUEST['employee_id']
				,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : 0
				,0
				,array(
					"t.transaction_amount AS transaction_amount"
					,"r.capcon_effect AS capcon_effect"
					)
				,'t.transaction_date, t.modified_date'
			);
			
			foreach($balanceData['list'] as $key => $val){
				if($val['capcon_effect']==1){
					$balance = $balance - $val['transaction_amount'];
				}
				else{
					$balance = $balance + $val['transaction_amount'];
				}
			}
		}
		
		foreach($data['list'] as $key => $val){
			if($val['capcon_effect']==1){
			
				$data['list'][$key]['balance'] = $balance;
				
				$balance = $balance - $val['transaction_amount'];
			}
			else{
				$data['list'][$key]['transaction_amount'] = $val['transaction_amount'];
				$data['list'][$key]['balance'] = $balance;
				$balance = $balance + $val['transaction_amount'];
			}
		}
		
		if($last_page){
			$data['list'] = array_merge($data['list']
				,array(0 => array(
					"transaction_date" => ""
					,"transaction_code" => ""
					,"transaction_amount" => $starting
					,"capcon_effect" => ""
					,"balance" => $starting)
				)
			);
			$data['count'] ++;
		}
		
		echo json_encode(array(
		'success' => true
		,'data' => $data['list']
		,'total' => $data['count']
		,'query' => $data['query']
		));
	}
	/**
	 * @desc Shows the employee's Capital Contribution, Required Balance and Max Withdrawable Amount
	 */
	function showBalanceInfo(){
		/*$capconBal = $this->showCapConBalance();
		$reqBal = $this->showReqBalance();
		$ccMinBal = $this->parameter_model->getParam('CCMINBAL');
		
		if($ccMinBal>$reqBal){
			$reqBal = $ccMinBal;
		}
		
		$maxWdwlAmount = $capconBal - $reqBal;
		if($maxWdwlAmount<0)
			$maxWdwlAmount = 0;*/
		
		$acctg_period = date("Ymd", strtotime($this->parameter_model->getParam('ACCPERIOD')));
		$bal_info = $this->mloan_model->showBalanceInfo($_REQUEST['member']['employee_id'],$acctg_period);	
		
		echo json_encode(array(
		'success' => true
		,'data' => array(
			'capconBal' => $bal_info['capconBal']
			,'reqBal' => $bal_info['reqBal']
			,'maxWdwlAmount' => $bal_info['maxWdwlAmount']
		)));
	}
	
	/**
	 * @desc To retrieve capital contribution balance of an employee
	 */
	/*function showCapConBalance(){
		$_REQUEST['member']['employee_id'] = '01528000';
		
		$accounting_period = date("YmdHis", strtotime($this->parameter_model->getParam('ACCPERIOD')));
		$this->capitalcontribution_model->populate(array(
			'employee_id' => $_REQUEST['member']['employee_id']
			,'accounting_period' => $accounting_period
		));


		$data = $this->capitalcontribution_model->get(null
		,array('ending_balance AS capcon_balance'
		));
		
		if(!isset($data['list'][0]['capcon_balance']))
			return 0;
		else
			return $data['list'][0]['capcon_balance'];
	}*/
	
	/**
	 * @desc To retrieve the required balance of an employee
	 */
	/*function showReqBalance(){
		$_REQUEST['member']['employee_id'] = '01528000';	
		
		$this->mloan_model->populate(array(
			'employee_id' => $_REQUEST['member']['employee_id']
		));


		$data = $this->mloan_model->get(null
		,array('SUM(capital_contribution_balance) AS reqBalance'
		));
		
		if(!isset($data['list'][0]['reqBalance']))
			return 0;
		else
			return $data['list'][0]['reqBalance'];
	}*/
	
		/**
	 * @desc Retrieves beneficiaries of employees
	 */
	function readBeneficiary()
	{
		//$_REQUEST['member']['employee_id'] = '00421526';
		if(array_key_exists('member',$_REQUEST))
		{	
			$param = array('member_id' => $_REQUEST['member']['employee_id']
				,'beneficiary !=' => ''
				,'relationship !=' => ''
				,'beneficiary_address !=' => ''
			);
			$data = $this->Member_model->retrieveEmployeeBeneficiaries(
				$param
				,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
				,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
				,array('beneficiary'
					,'relationship'
					,'beneficiary_address'
					)
			,'sequence_no');
			
		$relation = $this->constants->create_list($this->constants->member_relationship);
		
		foreach($data['list'] as $key => $val){
			$data['list'][$key]['description'] = "";
			foreach($relation as $value){
				if($val['relationship']==$value['code']){
					$data['list'][$key]['description'] = $value['name'];
					break;
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
	}
	
	function coMakerCount($empId, $loanCode){
		$retVal = 'Y';
		
		$years_of_service = $this->member_model->getEmpYearsOfService($empId);	
			
		$data = $this->loancodeheader_model->retrieveLoanCodes(
		array('RH.status_flag' => '1', 'RH.loan_code'=> $loanCode)
		,null
		,null
		,array("RH.loan_code AS loan_code"							
			,"COALESCE(RL.guarantor, 0) AS guarantor")
		,null
		,$years_of_service
		);
		if(count($data['list']) == 0 || $data['list'][0]['guarantor'] > 0)
			$retVal = 'N';
		else 
			$retVal = 'Y';
		return $retVal;

	}
	
	/**
	 * @desc Retrieve Information of a single loan transaction
	 * @return array
	 */
	function showLoan()
	{   
		$param = array('loan_no' => $_REQUEST['loan_no']
					   ,'status_flag' => '2'
						);
		$data = $this->mloan_model->get(
			$param
			,array("loan_no"
				,"restructure_no"					
				,"restructure_amount"					
				,"employee_id"					
				,"loan_code"					
				,"DATE_FORMAT(loan_date,'%m%d%Y') AS loan_date"					
				,"principal"					
				,"term"					
				,"interest_rate"					
				,"initial_interest"					
				,"employee_interest_total"					
				,"employee_interest_amortization"					
				,"employee_interest_vat_rate"					
				,"employee_interest_vat_amount"					
				,"company_interest_rate"					
				,"company_interest_total"					
				,"company_interest_amort"					
				,"DATE_FORMAT(amortization_startdate,'%m%d%Y') AS amortization_startdate"					
				,"employee_principal_amort"					
				,"down_payment_percentage"					
				,"down_payment_amount"					
				,"loan_proceeds"					
				,"estate_value"					
				,"mri_fip_amount"					
				,"broker_fee_amount"					
				,"government_fee_amount"					
				,"other_fee_amount"					
				,"service_fee_amount"					
				,"pension"					
				,"bank_transfer"					
				,"principal_balance"					
				,"interest_balance"					
				,"cash_payments"					
				,"capital_contribution_balance"					
				,"insurance_broker"				
				,"appraiser_broker"					
				,"check_no")
		);
		
		echo json_encode(array(
				'success' => true,
	            'data' => $data['list'],
				'total' => $data['count'],
				'query' => $data['query']
	        ));

	}
	
	function updateInsurance(){
		if(array_key_exists('loan_no', $_REQUEST)){
			if($_REQUEST['loan_no']!=""){
				if($_REQUEST['mri_due_date']==""){
					$mri_due_date = "";
				}
				else{
					$mri_due_date = date('Ymd', strtotime($_REQUEST['mri_due_date']));
				}
				
				if($_REQUEST['fip_due_date']==""){
					$fip_due_date = "";
				}
				else{
					$fip_due_date = date('Ymd', strtotime($_REQUEST['fip_due_date']));
				}
				
				$this->mloan_model->setValue('mri_due_amount', ($_REQUEST['mri_due_amount'] == "") ? 0 : $_REQUEST['mri_due_amount']);
				$this->mloan_model->setValue('mri_due_date', $mri_due_date);
				$this->mloan_model->setValue('fip_due_amount', ($_REQUEST['fip_due_amount'] == "") ? 0 : $_REQUEST['fip_due_amount']);
				$this->mloan_model->setValue('fip_due_date', $fip_due_date);
				
				$result = $this->mloan_model->update(array('loan_no' => $_REQUEST['loan_no']));
				if($result['affected_rows']>0){
					echo '{"success":true,"msg":"Data successfully saved"}';
				}
				else{
					echo '{"success":false,"msg":"Data NOT successfully saved"}';
				}
			}
			else{
				echo '{"success":false,"msg":"Data NOT successfully saved"}';
			}
		}
		else{
			echo '{"success":false,"msg":"Data NOT successfully saved"}';
		}
	}
	
	/** [START] Added by Vincent Sy for 8th Enhancement 2013/07/09
	 * returns value for $capconBal, $nonWithdrawableCapital, and $maxWithdrawable
	 * 
	 * 
	 * 
	 */
	function showMembershipInfoInTransHist() {	
	
		$employee_id = $_REQUEST['member']['employee_id'];	
		
		$accounting_period = date("Ymd", strtotime($this->parameter_model->getParam('ACCPERIOD')));	
						
		$data = $this->mloan_model->getEmployeeBalanceInfo($employee_id, $accounting_period);								
		
		echo json_encode(array(
		'success' => true
		,'data' => array(
			'capconBal' => $data['capconBal']
			,'reqBal' => $data['reqBal']
			,'maxWdwlAmount' => $data['maxWdwlAmount']
			// jdj 08082017 -- added HC
			,'capcon11' => $data['capcon11']
			,'capcon11date' => $data['accdate']
		)));						
	}
	//[END] Added by Vincent Sy for 8th Enhancement 2013/07/09
}

	

/* End of file membership.php */
/* Location: ./CodeIgniter/application/controllers/membership.php */
