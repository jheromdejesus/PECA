<?php
/*
 * Created on Apr 20, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Online_loan extends Asi_Controller {

	function Online_loan(){
		parent::Asi_Controller();
		$this->load->model('Onlineloan_model');
		$this->load->model('Loanguarantor_model');
		$this->load->model('Capitalcontribution_model');
		$this->load->model('Loan_model');
		$this->load->model('Mloan_model');
		$this->load->model('Loancodedetail_model');
		$this->load->model('Loancodeheader_model');
		$this->load->model('Parameter_model');
		$this->load->model('Tloan_model');
		$this->load->model('Loancharges_model');
		$this->load->model('Transactioncharges_model');
		$this->load->model('Workflow_model');
		$this->load->model('OAttachment_model');
		$this->load->model('member_model');
		$this->load->helper('url');
		$this->load->library('constants');
		$this->load->library('Asi_Model');
		##### NRB EDIT START #####
		require_once(BASEPATH.'application/my_classes/Classes/PHPExcel/Calculation/Functions.php');
		##### NRB EDIT END #####
	}
	
	function index(){		
		
	}
	
	/**
	 * @desc Retrieve all Online Loan Transactions
	 */
	function read()
	{
		$curr_date = $this->Parameter_model->retrieveValue('CURRDATE');
		if(array_key_exists('loan_date_from',$_REQUEST)&& $_REQUEST['loan_date_from']!= "")
			$loan_date_from = date("Ymd", strtotime($_REQUEST['loan_date_from']));
		else
			$loan_date_from = $_REQUEST['loan_date_from']== ""?"00000000":date("Ymd", strtotime('-1 day',strtotime($curr_date)));
		if(array_key_exists('loan_date_to',$_REQUEST)&& $_REQUEST['loan_date_to']!= "")
			$loan_date_to = date("Ymd", strtotime($_REQUEST['loan_date_to']));
		else
			$loan_date_to = $_REQUEST['loan_date_to']== ""?"99999999":date("Ymd", strtotime('+1 day',strtotime($curr_date)));
			
		$data = $this->Onlineloan_model->retrieveAllOnlineLoanTransactions(
			array_key_exists('loan_code',$_REQUEST) ? $_REQUEST['loan_code'] : null
			,$loan_date_from
			,$loan_date_to
			,array_key_exists('last_name',$_REQUEST) ? $_REQUEST['last_name'] : null
			,array_key_exists('first_name',$_REQUEST) ? $_REQUEST['first_name'] : null
			,array_key_exists('employee_id',$_REQUEST) ? $_REQUEST['employee_id'] : null
			,array_key_exists('status',$_REQUEST) ? $_REQUEST['status'] : 0
			,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
			,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
			,array('ol.employee_id'
				,'me.last_name'
				,'me.first_name'
	    		,'ol.loan_date'
				//Added for PECA 6th Enhancement Kweeny Libutan 2012/05/10
				,'ol.time_sent'
	    		,'rl.loan_description AS loan_type'
	    		,'ol.principal AS amount'
	    		,'ol.status_flag'
				,'ol.request_no'
				,'ow.approver1 AS approver1'
				,'ow.approver2 AS approver2'
				,'ow.approver3 AS approver3'
				,'ow.approver4 AS approver4'
				,'ow.approver5 AS approver5'
	    		)
		,'ol.modified_date DESC');//loan
		
		foreach($data['list'] as $row => $value){
			$data['list'][$row]['loan_date'] = date("mdY", strtotime($value['loan_date']));
			//Added for PECA 6th Enhancement Kweeny Libutan 2012/05/10
			$data['list'][$row]['time_sent'] = $value['time_sent']==''?'':date("H:i", strtotime($value['time_sent']));
		}
		
		echo json_encode(array(
			'success' => true,
            'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
        ));
	}

 	/**
	 * @desc show
	 */
 	function show()
	{
		/*$_REQUEST = array('request_no' =>	'09'
							);*/
		if(array_key_exists('request_no',$_REQUEST)) {				
			$params = 'ol.request_no =\''.$_REQUEST['request_no'].'\'';
			
			$data = $this->Onlineloan_model->showAllOnlineLoanTransactions(
				$params
				,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
				,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
				,array('ol.request_no'
					,'ol.employee_id'
					,'me.last_name'
					,'me.first_name'
		    		,'ol.loan_date'
		    		,'rl.loan_description AS loan_type'
		    		,'ol.principal AS amount'
		    		,'ol.status_flag'
					,'rl.loan_code'
					,'ol.term'
					,'ol.interest_rate'
					,'ol.member_remarks'
					,'ol.peca_remarks'
					,'ow.approver1 AS approver1'
					,'ow.approver2 AS approver2'
					,'ow.approver3 AS approver3'
					,'ow.approver4 AS approver4'
					,'ow.approver5 AS approver5'
		    		)
			,'ol.loan_date ASC');
		foreach($data['list'] as $row => $value){
			$data['list'][$row]['loan_date'] = date("mdY", strtotime($value['loan_date']));
		}
		
			//20110721 interest rate must have the same 
			//interest rate set by peca upon loan approval issue#1.1 this will result to dirty data.
			$loan_code_filter = array("loan_code" => $data['list'][0]['loan_code'],
									  "status_flag"=>"1");
			$get=$this->Loancodeheader_model->get($loan_code_filter);
			
			if($get['count']!=0){
				$data['list'][0]['interest_rate']=$get['list'][0]['emp_interest_pct'];
			}
			

			echo json_encode(array(
				'success' => true,
	            'data' => $data['list'],
				'total' => $data['count'],
				'query' => $data['query']
	        ));
		}
	}

 	/**
	 * @desc Retrieve Employee interest rate for the specified loan type
	 */
	function showEmpInterestRate()
	{		
		//$_REQUEST['loan'] = array('loan_code' => 'CAR2');
		if (array_key_exists('online_loan',$_REQUEST)){ 
			$data = $this->Onlineloan_model->retrieveEmployeeInterestRate($_REQUEST['loan']
				,null
				,null
				,array('emp_interest_pct'							
				));
		}
		
		echo json_encode(array(
			'success' => true,
            'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
        ));
	}
	
	/**
	 * @desc Deletes a Withdrawal Transaction
	 */
 	function delete()
	{				
		
		if (array_key_exists('request_no',$_REQUEST)) {
				$this->Onlineloan_model->setValue('status_flag', '0');
				$result = $this->Onlineloan_model->update(array(
											'request_no' => $_REQUEST['request_no']
											,'status_flag' => '2'
											));	
				if($result['affected_rows'] <= 0){
					echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
				} else {
					echo "{'success':true,'msg':'Data successfully deleted.'}";
				}
		} else
			echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
			
		log_message('debug', "[END] Controller online_loan:delete");
	}
	
	/**
	 * @desc Updates a Withdrawal Transaction
	 */
 	function update()
	{		
		/* $_REQUEST['wtransaction'] = array ('request_no' => '02'
										,'insurance_broker' =>'ABM'
										,'appraiser_broker' =>'BDO'
										,'check_no' =>'12345'
										,'modified_by' =>'PECA'
										,'modified_date' => '20101201123000'
									); */
		
		log_message('debug', "[START] Controller online_loan:update");
		log_message('debug', "online_loan param exist?:".array_key_exists('online_loan',$_REQUEST));
		
		if (array_key_exists('online_loan',$_REQUEST)) {
			if($this->Loancodeheader_model->loanCodeExists($_REQUEST['online_loan']['loan_code'])) {
				
				/* checking */
				$employee_id = $_REQUEST['employee_id'];
				$principal = $_REQUEST['online_loan']['principal'];
				$term = $_REQUEST['online_loan']['term'];
				$loan_code = $_REQUEST['online_loan']['loan_code'];
				$member_remarks = $_REQUEST['online_loan']['member_remarks'];
				$saveOrSendFlag = $_REQUEST['saveOrSendFlag'];
				$loan_date = date("Ymd", strtotime($_REQUEST['online_loan']['loan_date']));
				$_REQUEST['online_loan']['loan_date'] = $loan_date ;
				$data = $this->Loan_model->getRLoanHdr($loan_code);
				$_REQUEST['online_loan']['interest_rate'] = $data[0]['employee_interest_percentage'];
				
				if ($this->Loan_model->checkMaximumLoanAmount($principal, $data[0]['max_loan_amount'])>0) {
					echo("{'success':false,'msg':'Principal Amount exceeds the maximum loan amount','error_code':'13'}");	
				}
				else if ($this->Loan_model->checkMinMaxTerms($term, $data[0]['min_term'], $data[0]['max_term'])>0){
					echo("{'success':false,'msg':
						'Term should be between ".$data[0]['min_term']." and ".$data[0]['max_term']."','error_code':'14'}");	
				}
				else if ($this->Capitalcontribution_model->checkSuspensionDate($employee_id)>0){
					echo("{'success':false,'msg':'You are still on suspension','error_code':'9'}");	
				}
				else if ($this->checkRetiree($employee_id, $principal)>0) {
					echo("{'success':false,'msg':'The employee is a retiree. Thus he cannot loan more than 70% of his capital contribution.','error_code':'24'}");	
				}
				else if ($this->checkCapitalContribution($principal, $loan_code, $employee_id)>0){
					$result = $this->checkCapitalContribution($principal, $loan_code, $employee_id);
					
					if ($this->checkRetiree($employee_id, $principal)>0) {
						echo("{'success':false,'msg':'The employee is a retiree. Thus he cannot loan more than 70% of his capital contribution.','error_code':'24'}");
						return;
					}			
					if ($result==2) {
						echo("{'success':false,'msg':'Capital Contribution Balance is less than the Capital Contribution Minimum Balance','error_code':'37'}");
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
				//20110722 co-maker trapping issue#1.0
				//20110801 switched with invalid guarantor checker to match with the online loan application.
				else if ($this->checkAllowedGuarantor($loan_code, $employee_id) != 0){
					$allowedGuarantors = $this->checkAllowedGuarantor($loan_code, $employee_id);
					echo("{'success':false,'msg':'Loan Type applied requires at least $allowedGuarantors Co-Maker/s.','error_code':'8'}");	
				}
				else {
				
				/* end checking */
				
				
					$saveOrSendFlag = $_REQUEST['saveOrSendFlag'];
					$this->Onlineloan_model->populate($_REQUEST['online_loan']);
					$this->Onlineloan_model->setValue('employee_id', $employee_id);
					$this->Onlineloan_model->setValue('loan_code', $_REQUEST['online_loan']['loan_code']);
					if($saveOrSendFlag==1){
						$this->Onlineloan_model->setValue('status_flag', '1');
						//Added for PECA 6th Enhancement Kweeny Libutan 2012/05/10
						$this->Onlineloan_model->setValue('time_sent', date("His"));
						$result = $this->Onlineloan_model->update('request_no ='.$_REQUEST['request_no']);	
					}
					else if($saveOrSendFlag==2){
						$result = $this->Onlineloan_model->update('status_flag = 2 AND request_no ='.$_REQUEST['request_no']);	
					}
					if($result['affected_rows'] <= 0){
					//	echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
						if($saveOrSendFlag==1){
							echo "{'success':false,'msg':'Data was NOT successfully sent.'}";
						}
						else if($saveOrSendFlag==2){
							echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
						}
					} else
					//	echo "{'success':true,'msg':'Data successfully saved.'}";
						if($saveOrSendFlag==1){
							echo "{'success':true,'msg':'Data was successfully sent.'}";
						}
						else if($saveOrSendFlag==2){
							echo "{'success':true,'msg':'Data was successfully saved.'}";
						}
				}
			}
			else
				echo "{'success':false,'msg':'Loan code does not exist.'}";
		} else{
		//	echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
			if($saveOrSendFlag==1){
				echo "{'success':false,'msg':'Data was NOT successfully sent.'}";
			}
			else if($saveOrSendFlag==2){
				echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
			}
		}
			
		log_message('debug', "[END] Controller online_loan:update");
	}
	
	/**
	 * @desc Inserts new data to o_loan table
	 */
	function add()
	{
		/* $_REQUEST['online_loan'] = array(
										'employee_id' => '01517066'
										,'loan_code' => 'CAR2'
										,'loan_date' => '20100126120000'
										,'principal' => '500000'
										,'term' => '36'
										,'interest_rate' => '9.10'
										
										);  */
		
		log_message('debug', "[START] Controller online_loan:add");
		log_message('debug', "online_loan param exist?:".array_key_exists('online_loan',$_REQUEST));
		
		if (array_key_exists('online_loan',$_REQUEST)) 
		{
			if($this->Loancodeheader_model->loanCodeExists($_REQUEST['online_loan']['loan_code'])) {
				$request_no = $this->Parameter_model->retrieveValue('OREQ')+1;	
				
				$employee_id = $_REQUEST['employee_id'];
				$principal = $_REQUEST['online_loan']['principal'];
				$term = $_REQUEST['online_loan']['term'];
				$loan_code = $_REQUEST['online_loan']['loan_code'];
				$member_remarks = $_REQUEST['online_loan']['member_remarks'];
				$saveOrSendFlag = $_REQUEST['saveOrSendFlag'];
				$loan_date = date("Ymd", strtotime($_REQUEST['online_loan']['loan_date']));
				$_REQUEST['online_loan']['loan_date'] = $loan_date ;
				$data = $this->Loan_model->getRLoanHdr($loan_code);
				$_REQUEST['online_loan']['interest_rate'] = $data[0]['employee_interest_percentage'];

				if ($this->Loan_model->checkMaximumLoanAmount($principal, $data[0]['max_loan_amount'])>0) {
					echo("{'success':false,'msg':'Principal Amount exceeds the maximum loan amount','error_code':'13'}");	
				}
				else if ($this->Loan_model->checkMinMaxTerms($term, $data[0]['min_term'], $data[0]['max_term'])>0){
					echo("{'success':false,'msg':
						'Term should be between ".$data[0]['min_term']." and ".$data[0]['max_term']."','error_code':'14'}");	
				}
				else if ($this->Capitalcontribution_model->checkSuspensionDate($employee_id)>0){
					echo("{'success':false,'msg':'You are still on suspension','error_code':'9'}");	
				}
				else if ($this->checkRetiree($employee_id, $principal)>0) {
					echo("{'success':false,'msg':'The employee is a retiree. Thus he cannot loan more than 70% of his capital contribution.','error_code':'24'}");	
				}
				else if ($this->checkCapitalContribution($principal, $loan_code, $employee_id)>0){
					$result = $this->checkCapitalContribution($principal, $loan_code, $employee_id);
					
					if ($this->checkRetiree($employee_id, $principal)>0) {
						echo("{'success':false,'msg':'The employee is a retiree. Thus he cannot loan more than 70% of his capital contribution.','error_code':'24'}");
						return;
					}			
					if ($result==2) {
						echo("{'success':false,'msg':'Capital Contribution Balance is less than the Capital Contribution Minimum Balance','error_code':'37'}");
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
				//20110721 co-maker trapping issue#1.0
				//20110801 switched with invalid guarantor checker to match with the online loan application.
				else if ($this->checkAllowedGuarantor($loan_code, $employee_id) != 0){
					$allowedGuarantors = $this->checkAllowedGuarantor($loan_code, $employee_id);
					echo("{'success':false,'msg':'Loan Type applied requires at least $allowedGuarantors Co-Maker/s.','error_code':'8'}");	
				}
				else {
					$this->db->trans_start();
					$this->Onlineloan_model->populate($_REQUEST['online_loan']);
					$this->Onlineloan_model->setValue('status_flag', $saveOrSendFlag);
					//Added for PECA 6th Enhancement Kweeny Libutan 2012/05/10
					$this->Onlineloan_model->setValue('time_sent', $saveOrSendFlag==1?date("His"):"");
					$this->Onlineloan_model->setValue('employee_id', $employee_id);
					$this->Onlineloan_model->setValue('loan_code', $loan_code);
					$this->Onlineloan_model->setValue('loan_date', $loan_date);
					$this->Onlineloan_model->setValue('request_no', $request_no);
					
					$checkDuplicate = $this->Onlineloan_model->checkDuplicateKeyEntry();
			
					if($checkDuplicate['error_code'] == 1){
						$result['error_code'] = 1;
						$result['error_message'] = $checkDuplicate['error_message'];
					}
					else {
						$result = $this->Onlineloan_model->insert();
						$this->insertAttachments("L".$request_no);
					} 		
					$this->db->trans_complete();		
					if($result['error_code'] == 0 && $this->db->trans_status()){  
					  $this->Parameter_model->updateValue(('OREQ'), $request_no, $_REQUEST['online_loan']['created_by']);	
					  //echo "{'success':true,'msg':'Data successfully saved.'}";
						//if to save data
						if($saveOrSendFlag==2){
							echo "{'success':true,'msg':'Data successfully saved.','request_no':'".$request_no."'}";
						}
						//if to send data
						else if($saveOrSendFlag==1){
							echo "{'success':true,'msg':'Data successfully sent.','request_no':'".$request_no."'}";
						}
					} 
					else {
						if($result['error_message']!=""){
							echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';
						}
						else{
							echo '{"success":false,"msg":"Failed to insert attachments","error_code":"13"}';
						}
					}
				} //else
					//echo "{'success':false,'msg':'Data was NOT successfully saved.','error_code':'2'}";
			}else echo "{'success':false,'msg':'Loan code does not exist.','error_code':'13'}";
		}	
		log_message('debug', "[END] Controller online_loan:add");
	}
	
	function insertAttachments($topic_id){
		$filearr = json_decode(stripslashes($_REQUEST['files']),true);
		$attachment_id = 1;
		$created_by = "";
		if(array_key_exists('created_by', $_REQUEST['online_loan'])){
			$created_by = $_REQUEST['online_loan']['created_by'];
		}
		else if(array_key_exists('modified_by', $_REQUEST['online_loan'])){
			$created_by = $_REQUEST['online_loan']['modified_by'];
		}
		
		foreach($filearr as $fileinfo){
			$params = array('attachment_id' => sprintf("%06d", $attachment_id)
							,'topic_id' => $topic_id
							,'path' => $fileinfo['path']
							,'type' => $fileinfo['type']
							,'size' => $fileinfo['size']
							,'created_by' => $created_by
							); 
			$this->OAttachment_model->populate($params);				
			$this->OAttachment_model->insert();
			$attachment_id++;
		}
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
	 * @desc Retrieves the Maximum and Minimum Terms for the specied loan type
	 */
 	function showMinMaxTerms()
	{
		//$_REQUEST['loan'] = array('loan_code' => 'CAR2');
		$data_unfiltered = $this->Loan_model->retrieveMinMaxTerms($_REQUEST['loan']['loan_code']);
		$data = array('min_term' => $data_unfiltered['min_term']
						,'max_term' => $data_unfiltered['max_term']
					);
		
		return $data;
	}
	
	/**
	 * @desc Checks if the term is greater than or equal to Minimum term and less than or equal to Maximum term
	 * @return Returns 0 if the input term is within minimum and maximum terms
	 */
	function checkMinMaxTerms()
	{
		/* $_REQUEST['oloan'] = array('term' => '38'
									,'loan_code' => 'CAR2'
								); */
		$data = $this->Loan_model->retrieveMinMaxTerms($_REQUEST['oloan']['loan_code']);
		return $this->Loan_model->checkMinMaxTerms($_REQUEST['oloan']['term'], $data['min_term'], $data['max_term']);
	}
	
	/**
	 * @desc Checks the minimum number of months of service for the specified loan type
	 */
	function readLoanMonthOfService()
    {
    	//$_REQUEST['oloan'] = array('loan_code' => 'APPL');
    	echo $this->Loan_model->getLoanMonthOfService($_REQUEST['oloan']['loan_code']);
    }
    
    /**
	 * @desc Retrieves the date when the employee is hired
	 */
    function showEmpHireDate()
    {
    	//$_REQUEST['employee'] = array('employee_id' => '01679638');
    	if(array_key_exists('employee',$_REQUEST))
    		$hire_date = $this->Loan_model->getEmployeeHireDate($_REQUEST['employee']['employee_id']);
    }
    
    /**
	 * @desc Checks if the employee is a retiree.  
	 */
    function getEmployeeCompany($employee_id)
    {
    	return $this->Capitalcontribution_model->checkRetireeEmployee($employee_id);
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
		
		$company_code = $this->getEmployeeCompany($employee_id);
		
		if ($company_code=='920') {
			$capcon_pct = $this->getEmployeeCapConBalance($employee_id) * .7;
			$capcon_pct = round($capcon_pct,2);
			
			
			if (($principal + $this->showLoanBalance($employee_id)) > $capcon_pct)
				$result = 1;
			else $result = 0;
		}
		else $result = 0;
		
		return $result;
	}
	
	function showLoanBalance($employee_id) 
	{
		$data = $this->Mloan_model->get(array('employee_id'=>$employee_id, 'status_flag'=>'2', 'close_flag' => '0')
								,array('COALESCE(SUM(principal_balance),0) AS mloan_balance'));
		
		$total_loan_balance = $data['list'][0]['mloan_balance'];
		$total_loan_balance = round($total_loan_balance, 2);
		
		return $total_loan_balance;
	}
    
    /**
	 * @desc Retrieve capital contribution balance of an employee
	 */
    function getEmployeeCapConBalance($employee_id)
    {
    	return $this->Capitalcontribution_model->getEmployeeLoanBalance($employee_id);
    }
    
    /**
	 * @desc Retrieves detail of the selected loan
	 */
    function showLoanDetail()
    {
    	//$_REQUEST['oloan'] = array('loan_code' => 'MNLI');
    	if(array_key_exists('oloan',$_REQUEST))
    		$data = $this->Loancodedetail_model->getLoanDetail($_REQUEST['oloan']);
    	
    	echo json_encode(array(
			'success' => true,
            'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
        ));
    }
    
    function readLoanTypes()
    {
    	//$_REQUEST['filter'] = array('rl.guarantor' => 'N');
    	//$params = 'rh.loan_code IN(\'MNIL\', \'SPEI\', \'SPCL\', \'CONL\', \'LOYA\')';
		//[START] 7th Enhancement
		$params = 'rh.loan_code IN(\'MNIL\', \'SPEI\', \'SPCL\', \'CONL\', \'LOYA\', \'CON3\', \'LOY3\', \'MNI3\', \'SPO3\', \'SPE3\')';
	    $data = $this->Loancodeheader_model->retrieveLoanTypes2(
			$params
			,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
			,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
			,array('rh.loan_code'
	    		,'rh.loan_description'
	    		,'rh.emp_interest_pct AS employee_interest_rate')
		/*,'rh.loan_code'*/);
		echo json_encode(array(
			'success' => true,
            'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
        ));
		//[END] 7th Enhancement
    }
       
	/**
	 * @desc Checks if  employee is qualified for the applied loan
	 * @return 0-if employee can loan, 1-otherwise
	 */
	function checkYearsOfService($employee_id = '01517085', $loan_code = 'CAR2')
	{
		$result = 0;
		$data = $this->Loan_model->getRLoanDtl($loan_code); 	
		
		$from_yos = $data['from_yos']; 	
		$to_yos = $data['to_yos'];
		
		$years_of_service = $this->Loan_model->getEmpYearsOfService($employee_id);
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
	function checkCapitalContribution($principal, $loan_code, $employee_id)
	{
		$result = 0;
		$years_of_service = $this->member_model->getEmpYearsOfService($employee_id);

		$data = $this->Loancodeheader_model->retrieveLoanCodes(
			array('RH.status_flag' => '1', 'RH.loan_code' => $loan_code)
			,null
			,null
			,array("COALESCE(RL.capital_contribution,0) as capital_contribution ")
			,'RH.loan_description ASC'
			,$years_of_service
			);

		
		//$data = $this->getRLoanDtl($loan_code, $years_of_service); 
		$loan_capcon = $data['list'][0]['capital_contribution'] ;
		$capcon = $this->showCapConBalance($employee_id);	//gets the total capital contribution
		$ccminbal = $this->getParam('CCMINBAL');	
		
		if ($loan_capcon>0){

			//if capital contribution is already below 
			if($capcon < $ccminbal){
				return 2;
			}
		
			$data = $this->Mloan_model->get(array('employee_id' => $employee_id)
					,array('SUM(capital_contribution_balance) AS capconbalance')
			);
			$capcon_balance = $data['list'][0]['capconbalance']; //retrieved from m_loan table
			$required_balance = 0;
			
			$capcon_balance = $capcon_balance + $principal/3;
			
			if ($capcon>=$capcon_balance) {
				$result = 0;
			} else {
				$result = 1;
			}
			
		}
		else $result = 0;
		
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
	 * @desc To retrieve capital contribution balance of an employee
	 * @param employee_id, accounting_period
	 * @return array
	 */
	function showCapConBalance($employee_id){
		$_REQUEST['capTranHdr']['employee_id'] = $employee_id;
		if (array_key_exists('capTranHdr',$_REQUEST)){
			$acctgPeriod = date("Ymd", strtotime($this->Parameter_model->retrieveValue('ACCPERIOD')));
			
			$_REQUEST['capTranHdr']['accounting_period'] = $acctgPeriod;
			$this->Capitalcontribution_model->populate($_REQUEST['capTranHdr']);
		}
		
		$data = $this->Capitalcontribution_model->get(null 
			,array('ending_balance AS capcon_balance'
			));
		return isset($data['list'][0]['capcon_balance'])?$data['list'][0]['capcon_balance']:0;	
	}
	
	/**
	 * @desc disapprove change request
	 */
	function disapproveOnlineLoan()
	{
		/*$_REQUEST['data'] = array('request_no' => '39');*/
		
		log_message('debug', "[START] Controller online_loan:disapprove");
		log_message('debug', "online_loan param exist?:".array_key_exists('online_loan',$_REQUEST));
		
		if ((array_key_exists('status',$_REQUEST))&&($_REQUEST['status']>2)&&($_REQUEST['status']<9)) {
			$this->Onlineloan_model->setValue('peca_remarks', $_REQUEST['online_loan']['peca_remarks']);
			$this->Onlineloan_model->setValue('status_flag', '10');
			$result = $this->Onlineloan_model->update(array('request_no' => $_REQUEST['request_no']));	
			if($result['affected_rows'] <= 0){
				echo "{'success':false,'msg':'Request was NOT successfully disapproved.'}";
			} else
				echo "{'success':true,'msg':'Request successfully disapproved.'}";
		} else
			echo "{'success':false,'msg':'Request was NOT successfully disapproved.'}";
			
		log_message('debug', "[END] Controller online_loan:disapprove");
	}
	
	/**
	 * @desc Approve change request
	 */
	function approveOnlineLoan()
	{
		/*$_REQUEST['online_loan'] = array('employee_id' => '01517066'
									,'loan_code' =>'MNIL'
									,'created_by' => 'APPLE');
		$_REQUEST['status'] ='4';
		$_REQUEST['request_no'] ='214';*/
		log_message('debug', "[START] Controller online_loan:approve");
		log_message('debug', "online_loan param exist?:".array_key_exists('online_loan',$_REQUEST));
		
		if ((array_key_exists('online_loan',$_REQUEST))&&($_REQUEST['status']>2)&&($_REQUEST['status']<9)) {
			$status = $this->Workflow_model->checkNextApprover($_REQUEST['online_loan']['loan_code'], $_REQUEST['status']);
			if($status != 0)
			{
				$this->Onlineloan_model->setValue('peca_remarks', $_REQUEST['online_loan']['peca_remarks']);
				$this->Onlineloan_model->setValue('status_flag', $status);
				
				//20110721 issue# 1.1 updates the online interest rate of the user online loan application, this is to eliminate dirty data.
				$this->Onlineloan_model->setValue('interest_rate', $_REQUEST['online_loan']['interest_rate']);

				
				$result = $this->Onlineloan_model->update(array('request_no' => $_REQUEST['request_no']));
				
				if($result['affected_rows'] <= 0){
					echo "{'success':false,'msg':'Request was NOT successfully approved.'}";
				} else {
					if($status == 9)
					{
						$filter = 'ol.request_no =\''.$_REQUEST['request_no'].'\'';
						$data = $this->Onlineloan_model->showAllOnlineLoanTransactions(
							$filter
							,null
							,null
							,array('ol.request_no'
								,'ol.employee_id'
								,'ol.loan_date'
								,'ol.principal'
								,'ol.loan_code'
								,'ol.term'
								,'ol.interest_rate'
								)
							,'ol.loan_date ASC');
						$params = array(
									'loan_date' =>$data['list'][0]['loan_date']
									,'employee_id' =>$data['list'][0]['employee_id']
									,'principal' =>$data['list'][0]['principal']
									,'loan_code' => $data['list'][0]['loan_code']
									,'term' =>$data['list'][0]['term']
									,'created_by' =>$_REQUEST['online_loan']['created_by']
									,'interest_rate' =>$data['list'][0]['interest_rate']
									);
						$error_code = $this->addAfterApproval($params, $_REQUEST['request_no']);
						if($error_code == 1){
							echo '{"success":false,"msg":"Data was NOT successfully saved.","error_code":"'.$error_code.'"}';
						}else
							echo "{'success':true,'msg':'Request successfully approved.'}";
					}else
						echo "{'success':true,'msg':'Request successfully approved.'}";
				}
			} else
				echo "{'success':false,'msg':'Request was NOT successfully approved.'}";
		} else
			echo "{'success':false,'msg':'Request was NOT successfully approved.'}";
			
		log_message('debug', "[END] Controller online_loan:approve");
	}
	
	function addAfterApproval($ol_loan, $request_no)
	{
		$loan_no = $this->Parameter_model->retrieveValue('LASTLOANNO')+1;
		$employee_interest_total = $this->computeInterestRateAmount($ol_loan['interest_rate'], $ol_loan['principal'], $ol_loan['term']);
		$data = $this->Loan_model->getRLoanHdr($ol_loan['loan_code']);
		
		$loan_proceeds = $ol_loan['principal'];
		
		if ($data[0]['interest_earned']=="Y") {
			$initial_interest = $this->computeInitialInterestAmount($data[0]['employee_interest_percentage'], $ol_loan['principal']);
			$loan_proceeds = $loan_proceeds - $initial_interest;
		}
		else{ 
			$initial_interest = 0;
		}
		
		if ($data[0]['unearned_interest']=="Y"){ 
			$employee_interest_amortization = 0;
			$loan_proceeds = $loan_proceeds - $employee_interest_total;
		}
		else 
			$employee_interest_amortization = $this->computeAmortizedInterest($ol_loan['interest_rate'], $ol_loan['principal'], $ol_loan['term']);
		$company_interest_rate = $data[0]['company_share_percentage'];
		if ($company_interest_rate==NULL) {
			$company_interest_rate = 0;
			$company_interest_rate_total = 0;
			$company_interest_amort = 0;
		}
		else {
			$company_interest_rate_total = $this->computeCompanyInterestRateAmount($company_interest_rate, $ol_loan['principal']);	
			$company_interest_amort = $this->computeCompanyAmortizedInterest($company_interest_rate, $ol_loan['principal']);
		}
		$employee_principal_amort = $this->computePrincipalAmortization($ol_loan['principal'], $ol_loan['term']);
		$service_fee_amount = $this->computeServiceFee($ol_loan['loan_code'], $ol_loan['principal']);
		$loan_proceeds = $loan_proceeds - $service_fee_amount;
		//Edited by ASI 365 start
		//$amortization_startdate  = date("Ym", strtotime($ol_loan['loan_date']." + 1 month"))."01";
		$data = $this->parameter_model->get(array('parameter_id'=>'CURRDATE'),'parameter_value');
		$amortization_startdate  = date("Ym", strtotime($data['list'][0]['parameter_value']." + 1 month"))."01";
		//Edited by ASI 365 end
		
		$this->Tloan_model->populate($ol_loan);
		$this->Tloan_model->setValue('status_flag', '1');
		$this->Tloan_model->setValue('loan_no', $loan_no);
		$this->Tloan_model->setValue('employee_interest_total', $employee_interest_total);
		$this->Tloan_model->setValue('employee_interest_amortization', round($employee_interest_amortization));
		$this->Tloan_model->setValue('amortization_startdate', $amortization_startdate);
		$this->Tloan_model->setValue('company_interest_rate', $company_interest_rate);
		$this->Tloan_model->setValue('company_interest_total', $company_interest_rate_total);
		$this->Tloan_model->setValue('company_interest_amort', $company_interest_amort);
		$this->Tloan_model->setValue('employee_principal_amort', round($employee_principal_amort));
		$this->Tloan_model->setValue('loan_proceeds', $loan_proceeds);
		$this->Tloan_model->setValue('mri_fip_amount', 0);
		$this->Tloan_model->setValue('broker_fee_amount', 0);
		$this->Tloan_model->setValue('government_fee_amount', 0);
		$this->Tloan_model->setValue('other_fee_amount', 0);
		$this->Tloan_model->setValue('service_fee_amount', $service_fee_amount);
		$this->Tloan_model->setValue('principal_balance', $ol_loan['principal']);
		$this->Tloan_model->setValue('interest_balance', $employee_interest_total);
		$this->Tloan_model->setValue('initial_interest', $initial_interest);
		//Added by ASI 365 start
		$dateApproved = date("Ymd", strtotime($data['list'][0]['parameter_value']));
		$this->Tloan_model->setValue('loan_date', $dateApproved);
		//log_message("debug", "Avs Date Log: \n Approved date: " . $dateApproved);
		//Added by ASI 365 end
		
		##### NRB EDIT START #####
		#check if loan type is bsp
		$b_is_bsp = $this->Loancodeheader_model->is_bsp($ol_loan['loan_code']);
		
		if($b_is_bsp) {
		#recalculate other charges					
			log_message('debug','recalculate other charges');
			$f_other_charges_amount = $service_fee_amount + $initial_interest;
			$f_other_charges_rate = ($f_other_charges_amount / $ol_loan['principal']) * 100;
			$this->Tloan_model->setValue('other_charges_amount', $f_other_charges_amount);
			$this->Tloan_model->setValue('other_charges_rate', $f_other_charges_rate);
			
			#recalculate MIR/EIR
			log_message('debug','Recalculate MIR/EIR');
			$a_eir = $this->_eir($ol_loan['interest_rate'], $ol_loan['principal'], $service_fee_amount, $ol_loan['term'], $ol_loan['loan_code'], $initial_interest);
			$this->Tloan_model->setValue('effective_annual_interest_rate', $a_eir['f_eir']);
			$this->Tloan_model->setValue('effective_monthly_interest_rate', $a_eir['f_mir']);
		}
		##### NRB EDIT END #####

				
		$checkDuplicate = $this->Tloan_model->checkDuplicateKeyEntry();
		if($checkDuplicate['error_code'] == 1){
			$result['error_code'] = 1;
			$result['error_message'] = $checkDuplicate['error_message'];
		}
		else{
			$result = $this->Tloan_model->insert();
			$addChargeResult = $this->addDefaultCharges($ol_loan,$loan_no);	
			if ($result['affected_rows'] <= 0){
				
				$result['error_code'] = 1;
			}
			else if($addChargeResult==0){
				$this->Onlineloan_model->setValue('loan_no', $loan_no);
				$this->Onlineloan_model->setValue('employee_interest_total', $employee_interest_total);
				$this->Onlineloan_model->setValue('employee_interest_amortization', $employee_interest_amortization);
				$this->Onlineloan_model->setValue('employee_principal_amortization', $employee_principal_amort);
				$this->Onlineloan_model->setValue('service_fee_amount', $service_fee_amount);
				$this->Onlineloan_model->setValue('principal_balance', $ol_loan['principal']);
				$this->Onlineloan_model->setValue('interest_balance', $employee_interest_total);
				$result = $this->Onlineloan_model->update(array('request_no' => $request_no));
				$this->Parameter_model->updateValue(('LASTLOANNO'), $loan_no, $ol_loan['created_by']);	
				$result['error_code'] = 0;
			}
		}
		return $result['error_code'];
	}
	
	/**
	 * @desc Inserts default charges for the given loan type
	 * @param Array
	 */
	function addDefaultCharges($ol_loan, $loan_no)
	{
		$params = array();		
		$charge_list = $this->getCharges($ol_loan['loan_code']);		
		if (count($charge_list)==0) {
			return 0;
		}
		foreach ($charge_list AS $data) {
			$params['loan_no'] = $loan_no;										  
			$params['transaction_code'] = $data['charge_code'];
			$params['amount'] = $this->computeCharges($ol_loan['principal'], $data['charge_formula']);
			$this->Loancharges_model->populate($params);
			$this->Loancharges_model->setValue('status_flag', '1');
			$this->Loancharges_model->setValue('created_by', $ol_loan['created_by']);
			
			$result = $this->Loancharges_model->insert();	
		}
		
		if($result['error_code'] == 0){  
		  return 0;
		} 
		else
		  return 1;
		
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
		$data = $this->Transactioncharges_model->getServiceCharges(
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
	function getTopicAttachments()
	{
		$_REQUEST['bulletin']['topic_id'] = $_REQUEST['topic_id'];
		
		$data = $this->OAttachment_model->getTopicAttachments();
		
		echo json_encode(array(
			'success' => true,
            'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query'],
        ));
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
		//$days_of_month = date("t", strtotime(date("Y",$date). "-" . date("M",$date) . "-01"));
		$days_of_month = date("t", strtotime($date));
		$remaining_days_of_month = ($days_of_month-$day_today);
		
		$emp_initial_interest_amount = (($principal * ($emp_interest_pct/100)/12)/$days_of_month)*$remaining_days_of_month;	
			
		return round($emp_initial_interest_amount,2);
	}
	
	//used for trapping co-maker
	
	function checkAllowedGuarantor($loan_code, $employee_id){
		
		$years_of_service = $this->member_model->getEmpYearsOfService($employee_id);
		$data = $this->Loancodeheader_model->retrieveLoanCodes(
			array('RH.status_flag' => '1', 'RH.loan_code' => $loan_code)
			,null
			,null
			,array('RL.guarantor AS guarantor')
			,null
			,$years_of_service
			);

		if(!isset($data['list'][0]['guarantor']))	
			return 0;
		else 
			return $data['list'][0]['guarantor'];
	}
	
	##### NRB EDIT START #####
	function _eir($f_annual_contractual_rate, $f_loan_amount, $f_service_charge, $i_terms, $s_loan_code, $f_initial_interest) {
		$c_excel = new PHPExcel_Calculation_Functions();
		
		$a_values = array();
		
		#check if BSP
		$b_is_bsp = $this->Loancodeheader_model->is_bsp($s_loan_code);
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
	##### NRB EDIT END #####
	
}
?>