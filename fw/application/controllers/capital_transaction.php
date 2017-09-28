<?php

class Capital_transaction extends Asi_controller {

	function Capital_transaction(){
		parent::Asi_controller();
		$this->load->model('capitaltransactionheader_model');
		$this->load->model('capitaltransactiondetail_model');
		$this->load->model('member_model');
		$this->load->model('mloan_model');
		$this->load->model('tloan_model');
		$this->load->model('capitalcontribution_model');
		$this->load->model('transactioncharges_model');
		$this->load->model('transactioncode_model');
		$this->load->model('parameter_model');
		$this->load->model('transaction_model');
		$this->load->model('mtransaction_model');
		$this->load->model('mcaptranheader_model');
		$this->load->model('tblnpmlsuspension_model');
	}

	function index(){

	}

	/**
	 * @desc To retrieve all Capital Contribution Transactions that are not yet posted
	 * @return array
	 */
	function read() {
		
		if(isset($_REQUEST['transNo']) && $_REQUEST['transNo']!="")
			$params['tc.transaction_no LIKE'] = $_REQUEST['transNo'].'%';
			
		$params['tc.status_flag'] = '1';
		$data = $this->capitaltransactionheader_model->getListCapconTrans(
			$params
			,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
			,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
			,array('tc.transaction_no as transaction_no'
				,'tc.employee_id AS employee_id'
				,'me.last_name AS last_name'
				,'me.first_name AS first_name'
				,'tc.transaction_date AS transaction_date'
				,'rt.transaction_code AS transaction_code'
				,'rt.transaction_description AS transaction_type'
				,'tc.transaction_no AS transaction_no'
				,'tc.or_no AS or_no'
				,'tc.or_date AS or_date'
				,'tc.remarks AS remarks'
				,'tc.transaction_amount AS transaction_amount'
				),
			'transaction_date DESC'
		);
		
		foreach($data['list'] as $key => $val){
			$data['list'][$key]['transaction_date'] = date("mdY", strtotime($val['transaction_date']));
		}
		
		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
		));
	
	}

	/**
	 * @desc Returns details of a specific employee
	 * @param employee_id
	 * @return array
	 */
	function showEmployee(){
		if(array_key_exists('employee', $_REQUEST)){
			$this->member_model->populate($_REQUEST['employee']);
		}

		$data = $this->member_model->get(null, array(
			'employee_id'				
			,'last_name'
			,'first_name'
			,'middle_name'
			));

			echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
			));
	}

	/**
	 * @desc To count the remaining loans of an employee
	 * @param employee_id
	 * @return integer (remaining loans)
	 */
	function countRemainingLoans($employee_id){
		if (isset($employee_id)){
			$this->mloan_model->populate(array('employee_id' => $employee_id));
			$this->mloan_model->setValue('close_flag','0');
		}
		$data = $this->mloan_model->get(null ,array(
			'COUNT(*) AS remaining_loans'
			));

			return $data['list'][0]['remaining_loans'];

	}

	function allowedWdwlForRetiree($employee_id){
		$acctg_period = date("Ymd", strtotime($this->parameter_model->getParam('ACCPERIOD')));
		$capcon_balance = $this->capitalcontribution_model->retrieveCapConBalance($employee_id, $acctg_period);
		$capcon_balance = round($capcon_balance, 2);
		$interestAndRemainingTerms = $this->getInterestAndRemainingTerms($employee_id);
		log_message('debug', 'capcon_balancezzzz: '. $capcon_balance);
		log_message('debug', 'interestAndRemainingTermszzzz: '. $interestAndRemainingTerms);
		if(($capcon_balance - ($interestAndRemainingTerms)) < 0){
			return 0;
		}
		else{
			return ($capcon_balance - ($interestAndRemainingTerms));
		}
	}

	function getInterestAndRemainingTerms($employee_id){
		$remaining_terms = "(principal_balance / employee_principal_amort)"; 
		$interest = "($remaining_terms * employee_interest_amortization)";
		
		/*$data1 = $this->tloan_model->get(array('employee_id'=> $employee_id, 'status_flag'=> '1')
			,array("COALESCE(SUM(principal_balance + $interest),0) AS tsum"));
		*/
		
		$data2 = $this->mloan_model->get(array('employee_id'=>$employee_id, 'status_flag'=>'2', 'close_flag' => '0')
			,array("COALESCE(SUM(principal_balance + $interest),0) AS msum"));
	
		return (round($data2['list'][0]['msum'], 2));
	}
	/**
	 * @desc To return the sum of the remaining loan balance of an employee
	 * @param employee_id
	 * @return int (loan balance)
	 */
	function getOutstandingLoan($employee_id){
		$data1 = $this->mloan_model->get(array('employee_id'=>$employee_id, 'status_flag'=>'2', 'close_flag' => '0')
								,array('COALESCE(SUM(principal_balance),0) AS mloan_balance'));
		
		$data2 = $this->tloan_model->get(array('employee_id'=>$employee_id, 'status_flag'=>'1')
								,array('COALESCE(SUM(principal_balance),0) AS tloan_balance'));
		
		$total_loan_balance = $data1['list'][0]['mloan_balance'] + $data2['list'][0]['tloan_balance'];
		$total_loan_balance = round($total_loan_balance, 2);
		
		return $total_loan_balance;
	}

	/**
	 * @desc To retrieve capital contribution balance of an employee
	 * @param employee_id, accounting_period
	 * @return array
	 */
	function showCapConBalance($employee_id = null){
		$acctg_period = date("Ymd", strtotime($this->parameter_model->getParam('ACCPERIOD')));
		echo $this->capitalcontribution_model->retrieveCapConBalance($employee_id, $acctg_period);
		/*$this->capitalcontribution_model->populate(array(
			'employee_id' => $employee_id
			,'accounting_period' => $accounting_period
		));


		$data = $this->capitalcontribution_model->get(null
		,array('ending_balance AS capcon_balance'
		));
		
		if(!isset($data['list'][0]['capcon_balance']))
			echo 0;
		else
			echo $data['list'][0]['capcon_balance'];*/
	}

	/**
	 * @desc Checks if initial deposit is higher than capital contribution minimum balance
	 * @param transaction_amount
	 * @return 1 - Initial deposit is acceptable, 0 - otherwise
	 */
	function checkInitDeposit($transaction_amount){
		$ccMinBal = $this->parameter_model->getParam('CCMINBAL');
		$mFee = $this->parameter_model->getParam('MFEE');
		if($mFee=="")
			$mFee = 0;
		
		if($transaction_amount >= ($ccMinBal+$mFee)) 
			return 0;
		else 
			return 1;
	}

	/**
	 * @desc Returns the capital contribution balance of an employee
	 */
	/*function getCapConBalance($employee_id = ''){
		$accounting_period = $acctgPeriod = date("YmdHis", strtotime($this->parameter_model->getParam('ACCPERIOD')));
		$this->capitalcontribution_model->populate(array(
			'employee_id' => $employee_id
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
	 * @desc Checks if employee has acceptable guarantors (not a transferee, retired or inactive)
	 * @param employee_id
	 * @return 0-valid guarantors, 1-invalid guarantors
	 */
	function hasValidGuarantors($employee_id){
		$data = $this->member_model->checkGuarantors(
		array('ml.employee_id' => $employee_id, 'close_flag' => '0')
		,null
		,null
		,array('me.company_code AS company_code, me.member_status AS member_status, me.non_member AS non_member')
		);
			
		return $data;
	}

	/**
	 * @desc Retrieves the company code of an employee
	 */
	function showCompCode($employee_id){
		$data = $this->member_model->get(array('employee_id'=>$employee_id), array(
			'company_code'
			));
		if(!isset($data['list'][0]['company_code']))
			return "";
		else
			return $data['list'][0]['company_code'];
	}

	/**
	 * @desc Checks if employee's suspension date is more than 6 months from current date
	 * @return 1-suspended less than 6 months, 0 - otherwise
	 */
	function checkSuspensionDate($employee_id = ''){
		if (isset($employee_id)&& $employee_id!=""){
			$this->member_model->populate(array('employee_id'=>$employee_id));
			$currDate = date("Ymd", strtotime($this->parameter_model->getParam('CURRDATE')));
			$data = $this->member_model->get(null ,array('suspended_date'));

			$suspensionDate = $data['list'][0]['suspended_date'];//20110801
			if($suspensionDate == ""){
				return 0;
			}

			$temp_date = date("Ym", strtotime(date("Ymd", strtotime($suspensionDate)) . " +6 month"));//201202

			$suspensionLiftOffDate = $temp_date."01";//20120201
			
			// 20111029 #0008369 
			// [START] 7642 : Added by ASI466 on 20111104
			$result = $this->tblnpmlsuspension_model->getSixMonthsSuspensionRec($employee_id,$currDate);
			$hasSuspensionRec = 0;
			if($result["count"]!=0){
				$hasSuspensionRec = 1;
			}			
			// [END] 7642 : Added by ASI466 on 20111104
			
			// 20111029 #0008369 if($suspensionLiftOffDate <= $currDate or  $currDate < $suspensionDate  ) return 0;
			// [START]  : Modified by ASI466 on 20111109
			if(($suspensionLiftOffDate <= $currDate or $currDate < $suspensionDate) and  !$hasSuspensionRec  ) return 0;
			// [END]  : Modified by ASI466 on 20111109
			else return 1;
		}
		else return 1;
	}

	/**
	 * @desc Checks if the remaining capcon balance after withdrawal is higher than the minimum capcon balance 
	 * @return 0 - Remaining balance is higher than minimum, 1 - otherwise
	 */
	function compareRemainingBalance($employee_id, $transAmt, $acctgPeriod){
		/*$ccMinBal= $this->parameter_model->getParam('CCMINBAL');

		$data = $this->capitalcontribution_model->get(array(
			'employee_id'=> $employee_id
			,'accounting_period' => $acctgPeriod
			)
		,'ending_balance AS capcon_balance');
			
		$capconBal = $data['list'][0]['capcon_balance'];*/
		
		//[START] 8th Enhancement MODIFIED by VINCENT SY 20130814
		$bal_info = $this->mloan_model->getEmployeeBalanceInfo($employee_id,$acctgPeriod);
		//[END] 8th Enhancement MODIFIED by VINCENT SY 20130814
		$allowedWdwl = $bal_info["maxWdwlAmount"];
		
		//to check using float
		// settype($allowedWdwl,'float');
		// settype($transAmt,'float');
		
		$allowedWdwl = round($allowedWdwl,2);
		$transAmt = round($transAmt,2);
		
		log_message('debug', 'Inside compareRemainingBalance: transAmtzzzzz ' . $transAmt . 'allowedWdwzzzzz ' . $allowedWdwl . 'bool ' . ($allowedWdwl - $transAmt));
		//if(($capconBal - $transAmt) < $ccMinBal) return 1;
		if($allowedWdwl < $transAmt) return 1;
		else return 0;
	}

	/**
	 * @desc Retrieves transaction charge of the specified transaction type
	 */
	function readCharge(){			
		if (array_key_exists('capcon',$_REQUEST)){
			$this->transactioncharges_model->populate($_REQUEST['capcon']);
		}
			
		$data = $this->transactioncharges_model->getTransChargeList(
		array('rtc.transaction_code'=>$_REQUEST['capcon']['transaction_code'], 'rtc.status_flag' => '1')
		,null
		,null
		,array('rtc.charge_code AS transaction_code'
		,'rtc.charge_formula AS formula'
		));
		
		return $data;
	}

	/**
	 * @desc Retrieves transaction types from 'CC' transaction group
	 */
	function readTranTypes(){
		$data = $this->transactioncode_model->get_list(
		array('transaction_group' => 'CC', 'status_flag'=>'1')
		,null
		,null
		,array('transaction_code'
		,'transaction_description'
		,'bank_transfer'
		)
		,'transaction_description ASC'
		);

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
		,'created_by' => $_REQUEST['user']
		));
			
		$this->capitalcontribution_model->insert();
	}

	/**
	 * @desc Checks if employee id exists
	 * @return 0 - employee id exists, 1 - otherwise
	 */
	function checkEmployee($employee_id){
		$data = $this->member_model->get(array('employee_id' => $employee_id), array('COUNT(*) AS count'));
		if($data['list'][0]['count']>0) return 0;
		else return 1;
	}

	function addHdr(){
		/*$_REQUEST['capcon'] = array(
			'employee_id' => '00421526'
			,'transaction_code' => 'BMBN'
		    ,'transaction_date' => '04/27/2010'
		    ,'transaction_amount' => '100'
			);
		
		$_REQUEST['user'] = 'peca';*/
		log_message('debug', "[START] Controller capital_transaction:addHdr");
		log_message('debug', "capcon param exist?:".array_key_exists('capcon',$_REQUEST));

		if (array_key_exists('capcon',$_REQUEST)){
				
			$transaction_code = $_REQUEST['capcon']['transaction_code'];
			$employee_id = $_REQUEST['capcon']['employee_id'];
			$transaction_amount = $_REQUEST['capcon']['transaction_amount'];
				
			$acctgPeriod = date("Ymd", strtotime($this->parameter_model->getParam('ACCPERIOD')));
			$str_return = "";
			$negativeEffectCapconTrans = $this->getNegativeEffectCapconTrans();	
				
			if($this->checkEmployee($employee_id)){
				$str_return = "{'success':false,'msg':'Employee does not exist','error_code':'22'}";
			}
			else if($this->member_model->employeeIsInactive($employee_id)){
				$str_return = "{'success':false,'msg':'Employee is inactive','error_code':'55'}";
			}
			else if(!$this->transactioncode_model->transactionCodeExists($transaction_code, 'CC')){
				$str_return = "{'success':false,'msg':'Transaction code does not exist','error_code':'47'}";
			}
			else {
				if($this->checkCapconEntry($employee_id, $acctgPeriod))
				$this->addCapconEntry($employee_id, $acctgPeriod);

				if($transaction_code=="CLSE" && //check if closing
				$this->countRemainingLoans($employee_id)>0){
					$str_return = "{'success':false,'msg':'Employee still has remaining loans','error_code':'7'}";
				}
				else if($transaction_code=="MISL" &&
				$this->checkInitDeposit($transaction_amount)){
					$str_return = "{'success':false,'msg':'Employee\'s initial deposit should be a value greater than or equal to the capital contribution minimum balance.','error_code':'12'}";
				}
				else if($transaction_code=="DDEP" &&
				$this->depositGreaterThanTenMillion($transaction_amount, $employee_id, $acctgPeriod)){
					$str_return = "{'success':false,'msg':'Employee cannot have more than 10 million.','error_code':'54'}";
				}
				else {
					if(in_array($transaction_code, $negativeEffectCapconTrans)) {
						if($transaction_code=="CLSE"){
							//closing is exempted, should allow this transaction to exceed the employee's maximum withdrawable amount and does not need to maintain the required balance.
						}
						
						//Withdrawal 
						else if($transaction_code=="WDWL"){
							if($this->hasValidGuarantors($employee_id)){
							$str_return = "{'success':false,'msg':'Employee has invalid co-makers','error_code':'8'}";
							}
							else if($this->checkSuspensionDate($employee_id)){
								$str_return = "{'success':false,'msg':'Employee is still on suspension','error_code':'9'}";
							}
							else if($this->compareRemainingBalance($employee_id, $transaction_amount, $acctgPeriod)){
								//[START] 8th Enhancement MODIFIED by VINCENT SY 20130814
								if($this->showCompCode($employee_id)=='920')
									$str_return = "{'success':false,'msg':'Employee has exceeded the withdrawal amount for a retiree','error_code':'11'}";
								else
									$str_return = "{'success':false,'msg':'Employee has exceeded the withdrawal amount','error_code':'10'}";
								//[END] 8th Enhancement MODIFIED by VINCENT SY 20130814
							}
						}
						
						//All transactions who's Capcon Effect is "Deducted From Capital Contribution" must not be allowed to exceed the maximum withdrawable amount and must maintain the required balance listed on their individual ledger. It does NOT require a valid co-maker and must NOT be suspended.
						else{
							if($this->compareRemainingBalance($employee_id, $transaction_amount, $acctgPeriod)){
								//[START] 8th Enhancement MODIFIED by VINCENT SY 20130814
								if($this->showCompCode($employee_id)=='920')
									$str_return = "{'success':false,'msg':'Employee has exceeded the withdrawal amount for a retiree','error_code':'11'}";
								else
									$str_return = "{'success':false,'msg':'Employee has exceeded the withdrawal amount','error_code':'10'}";
								//[END] 8th Enhancement MODIFIED by VINCENT SY 20130814
							}
						}
					}
				}

			}
			if ($str_return == ""){
				unset($_REQUEST['capcon']['or_no']);
				unset($_REQUEST['capcon']['or_date']);
				$this->capitaltransactionheader_model->populate($_REQUEST['capcon']);
				$trans_no = $this->parameter_model->incParam('LASTTRANNO');
				
				$this->capitaltransactionheader_model->setValue('transaction_no', $trans_no);
				
				$this->capitaltransactionheader_model->setValue('status_flag', '1');
				$this->capitaltransactionheader_model->setValue('created_by', $_REQUEST['user']);
				
				/*if($this->withOR($transaction_code)){
					$or_no = $this->parameter_model->incParam('LASTORNO');
					$curr_date = $this->parameter_model->getParam('CURRDATE');
					$this->capitaltransactionheader_model->setValue('or_no', $or_no);
					$this->capitaltransactionheader_model->setValue('or_date', $curr_date);
					$success_message = "{'success':true,'msg':'Data successfully saved.','transaction_no':'$trans_no'
						,'or_no':'$or_no'
						,'or_date':'$curr_date'}";
				}
				else{
					$success_message = "{'success':true,'msg':'Data successfully saved.','transaction_no':'$trans_no'}";
				}*/

				$checkDuplicate = $this->capitaltransactionheader_model->checkDuplicateKeyEntry();

				if($checkDuplicate['error_code'] == 1){
					$result['error_code'] = 1;
					$result['error_message'] = $checkDuplicate['error_message'];
				}
				else{
					$this->db->trans_start();
					$this->capitaltransactionheader_model->insert();
					$this->addDtl($trans_no, $transaction_amount);
					$this->db->trans_complete();
					
					if ($this->db->trans_status() === FALSE){
						$result['error_code'] = 1;
						$result['error_message'] = "Data was NOT successfully saved.";
					}
					else{
						$result['error_code'] = 0;
					}
				}

				if($result['error_code'] == 0){
					echo "{'success':true,'msg':'Data successfully saved.','transaction_no':'$trans_no'}";
				} else
				echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';
			} else {
				echo $str_return;
			}

		} else
		echo "{'success':false,'msg':'Data was NOT successfully saved.','error_code':'2'}";

		log_message('debug', "[END] Controller capital_transaction:addHdr");
	}
	
	/**
	* Get all transactions which deduct capcon balance
	*/
	function getNegativeEffectCapconTrans(){
		$transaction_codes = array();
		$result = $this->transactioncode_model->get(array('capcon_effect' => '-1'
			, 'transaction_group' => 'CC', 'status_flag' => '1'), array('transaction_code'));
		
		foreach($result['list'] as $row){
			$transaction_codes[] = $row['transaction_code'];
		}
		
		return $transaction_codes;
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
	
	function addDtl($trans_no, $amt){
		if($_REQUEST['capcon']['transaction_code'] == "NPML"){
			return ;
		}
		log_message('debug', "[START] Controller capital_transaction:addDtl");
		$data = $this->readCharge();
		
		//Get allowed no. of withdrawals
		$allowed_withdrawals = $this->parameter_model->getParam('MAXNOWDRAW');
		if($_REQUEST['capcon']['transaction_code']=="WDWL" && (($this->noOfWithdrawals($trans_no))>$allowed_withdrawals)){
			$maxwdrawfe = $this->parameter_model->getParam('WMFE');
			$data['list'][]['transaction_code'] = 'WMAX';
			$index = count($data['list'])-1;
			$data['list'][$index]['formula'] = $maxwdrawfe;
		}
		
		foreach($data['list'] as $key => $params){
			$formula = str_replace('cta', $amt, $params['formula']);
			eval("\$params['amount'] = $formula;");
			unset($params['formula']);
			$params['transaction_no'] = $trans_no;
			$params['created_by'] = $_REQUEST['user'];
			$this->capitaltransactiondetail_model->populate($params);
			$this->capitaltransactiondetail_model->setValue('status_flag', '1');
			$this->capitaltransactiondetail_model->insert();
		}

		log_message('debug', "[END] Controller capital_transaction:addDtl");
	}

	/**
	 * @desc Retrieve the information of the selected transaction
	 */
	function show(){
		if (array_key_exists('capcon',$_REQUEST)){
			$this->capitaltransactionheader_model->setValue('tc.transaction_no',$_REQUEST['capcon']['transaction_no']);
		}
		
		//Update the transaction date to currdate when loading the details of capcon withdrawal/deposit - requested by peca 20110609
		$currDate = date("Ymd", strtotime($this->parameter_model->getParam('CURRDATE')));
		$this->capitaltransactionheader_model->changeTransactionDateToCurrDate($_REQUEST['capcon']['transaction_no'], $currDate);

		$data = $this->capitaltransactionheader_model->getCapconTrans(null
		,array('tc.transaction_no AS transaction_no'
		,'tc.employee_id AS employee_id'
		,'me.last_name AS last_name'
		,'me.first_name AS first_name'
		,'tc.transaction_code AS transaction_code'
		,'rt.transaction_description AS transaction_type'
		,'rt.with_or AS with_or'
		,'tc.bank_transfer AS bank_transfer'
		,'tc.or_no AS or_no'
		,'tc.or_date AS or_date'
		,'tc.transaction_date AS transaction_date'	
		,'tc.transaction_amount AS transaction_amount'
		,'tc.remarks AS remarks'
		));
		
		foreach($data['list'] as $key => $val){
			$data['list'][$key]['transaction_date'] = date("mdY", strtotime($val['transaction_date']));
			if($data['list'][$key]['or_date'] != "")
				$data['list'][$key]['or_date'] = date("mdY", strtotime($val['or_date']));
		}

		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
		));
	}

	/**
	 * @desc Retrieves other charges of the selected transaction
	 */
	function readDtl() {
		
		$data = $this->capitaltransactiondetail_model->getListTransCharge(
		array('tcc.transaction_no' => $_REQUEST['transaction_no']
		,'tcc.status_flag' => '1'
		)
		,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
		,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
		,array('tcc.transaction_no AS transaction_no'
		,'tcc.transaction_code AS transaction_code'
		,'rt.transaction_description AS transaction_description'
		,'tcc.amount AS amount'
		),
		'transaction_no DESC'
		);
		
		foreach($data['list'] as $row => $value){
			$id = $value['transaction_no'] . ':' . $value['transaction_code'];
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
	 * @desc Updates the t_capital_transaction_header table and its details
	 */
	function updateHdr(){
		log_message('debug', "[START] Controller capital_transaction:updateHdr");
		log_message('debug', "capcon param exist?:".array_key_exists('capcon',$_REQUEST));

		if (array_key_exists('capcon',$_REQUEST)){
					
				$transaction_code = $_REQUEST['capcon']['transaction_code'];
				$employee_id = $_REQUEST['capcon']['employee_id'];
				$transaction_amount = $_REQUEST['capcon']['transaction_amount'];
					
				$acctgPeriod = date("Ymd", strtotime($this->parameter_model->getParam('ACCPERIOD')));
				$str_return = "";
				$negativeEffectCapconTrans = $this->getNegativeEffectCapconTrans();
				
				if($this->checkEmployee($employee_id)){
					$str_return = "{'success':false,'msg':'Employee does not exist','error_code':'22'}";
				}
				else if($this->member_model->employeeIsInactive($employee_id)){
					$str_return = "{'success':false,'msg':'Employee is inactive','error_code':'55'}";
				}
				else if(!$this->transactioncode_model->transactionCodeExists($transaction_code, 'CC')){
				$str_return = "{'success':false,'msg':'Transaction code does not exist','error_code':'47'}";
				}
				else {
					if($this->checkCapconEntry($employee_id, $acctgPeriod))
					$this->addCapconEntry($employee_id, $acctgPeriod);

					if($transaction_code=="CLSE" && //check if closing
					$this->countRemainingLoans($employee_id)>0){
						$str_return = "{'success':false,'msg':'Employee still has remaining loans','error_code':'7'}";
					}
					else if($transaction_code=="MISL" &&
					$this->checkInitDeposit($transaction_amount)){
						$str_return = "{'success':false,'msg':'Employee\'s initial deposit should be a value greater than or equal to the capital contribution minimum balance.','error_code':'12'}";
					}
					else if($transaction_code=="DDEP" &&
					$this->depositGreaterThanTenMillion($transaction_amount, $employee_id, $acctgPeriod, $_REQUEST['capcon']['transaction_no'])){
						$str_return = "{'success':false,'msg':'Employee cannot have more than 10 million.','error_code':'54'}";
					}
					else {

						if(in_array($transaction_code, $negativeEffectCapconTrans)) {
							if($transaction_code=="CLSE"){
								//closing is exempted, should allow this transaction to exceed the employee's maximum withdrawable amount and does not need to maintain the required balance.
							}
							
							//Withdrawal 
							else if($transaction_code=="WDWL"){
								if($this->hasValidGuarantors($employee_id)){
									$str_return = "{'success':false,'msg':'Employee has invalid co-makers','error_code':'8'}";
								}
								else if($this->checkSuspensionDate($employee_id)){
									$str_return = "{'success':false,'msg':'Employee is still on suspension','error_code':'9'}";
								}
								else if($this->compareRemainingBalance($employee_id, $transaction_amount, $acctgPeriod)){
									//[START] 8th Enhancement MODIFIED by VINCENT SY 20130814
									if($this->showCompCode($employee_id)=='920')
										$str_return = "{'success':false,'msg':'Employee has exceeded the withdrawal amount for a retiree','error_code':'11'}";
									else
										$str_return = "{'success':false,'msg':'Employee has exceeded the withdrawal amount','error_code':'10'}";
									//[END] 8th Enhancement MODIFIED by VINCENT SY 20130814
								}
							}
							
							//All transactions who's Capcon Effect is "Deducted From Capital Contribution" must not be allowed to exceed the maximum withdrawable amount and must maintain the required balance listed on their individual ledger. It does NOT require a valid co-maker and can be suspended.
							else{
								if($this->compareRemainingBalance($employee_id, $transaction_amount, $acctgPeriod)){
									//[START] 8th Enhancement MODIFIED by VINCENT SY 20130814
									if($this->showCompCode($employee_id)=='920')
										$str_return = "{'success':false,'msg':'Employee has exceeded the withdrawal amount for a retiree','error_code':'11'}";
									else
										$str_return = "{'success':false,'msg':'Employee has exceeded the withdrawal amount','error_code':'10'}";
									//[END] 8th Enhancement MODIFIED by VINCENT SY 20130814
								}
							}
						}
					}

				}
				if ($str_return == ""){
					unset($_REQUEST['capcon']['or_no']);
					unset($_REQUEST['capcon']['or_date']);
					$this->capitaltransactionheader_model->populate($_REQUEST['capcon']);
					$this->capitaltransactionheader_model->setValue('status_flag', '1');
					$this->capitaltransactionheader_model->setValue('modified_by', $_REQUEST['user']);
					/*if($this->withOR($transaction_code))
						$with_or='1';
					else
						$with_or='0';
					$success_message = "{'success':true,'msg':'Data successfully saved.','with_or': '$with_or'
					,'new_or':false}";*/
	
					$this->db->trans_start();
					/*if($this->withOR($transaction_code) && $_REQUEST['capcon']['or_no']==""){
						$or_no = $this->parameter_model->incParam('LASTORNO');
						$curr_date = $this->parameter_model->getParam('CURRDATE');
						$this->capitaltransactionheader_model->setValue('or_no', $or_no);
						$this->capitaltransactionheader_model->setValue('or_date', $curr_date);
						$success_message = "{'success':true,'msg':'Data successfully saved.','with_or': '$with_or'
						,'or_no':'$or_no','or_date':'$curr_date','new_or':true}";
					}
					else if(!$this->withOR($transaction_code)){
						$this->capitaltransactionheader_model->setValue('or_no', '');
						$this->capitaltransactionheader_model->setValue('or_date', '');
					}*/
					
					$result = $this->capitaltransactionheader_model->update(
						array('transaction_no' => $_REQUEST['capcon']['transaction_no']
						,'status_flag' => '1')
					);
					
					if($result['affected_rows']>0){
						$this->capitaltransactiondetail_model->setValue('transaction_no', $_REQUEST['capcon']['transaction_no']);
						$this->capitaltransactiondetail_model->delete();
						$this->addDtl($_REQUEST['capcon']['transaction_no'], $_REQUEST['capcon']['transaction_amount']);	
					}
					$this->db->trans_complete();
			
					if($result['affected_rows'] > 0 && $this->db->trans_status() === TRUE){
						echo "{'success':true,'msg':'Data successfully saved.'}";
					} else
					echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
				} else {
					echo $str_return;
				}

			} else
			echo "{'success':false,'msg':'Data was NOT successfully saved.'}";

			log_message('debug', "[END] Controller capital_transaction:updateHdr");
	}

	/**
	 * @desc Delete a single capital transaction header and all its details
	 * @param transaction_no
	 * @return string (status message)
	 */
	function deleteHdr(){
		log_message('debug', "[START] Controller capital_transaction:deleteHdr");
		log_message('debug', "capTranHdr param exist?:".array_key_exists('capTranHdr',$_REQUEST));
		
		//[start]20111121 modified by asi466 issue:0008377
		//get employee id first of the transaction no in t cap tran header
		$result_employee_id = $this->capitaltransactionheader_model->get(array("transaction_no"=>$_REQUEST['capcon']['transaction_no']),"employee_id");
		
		//check if the tcaptranheader has wmax transaction detail
		$WmaxTcaptrandetail =  $this->capitaltransactiondetail_model->get(array("transaction_no"=>$_REQUEST['capcon']['transaction_no'],"transaction_code"=>"WMAX"));
		
		//check other tcaptranheader if there are wmax tcaptrandetail that are to delete
		if($WmaxTcaptrandetail["count"] == 0 ){
			//get other withdrawals within the month in tcaptranheader 
			$accounting_period = date("Ym", strtotime($this->parameter_model->getParam('ACCPERIOD')));
			$resultWdwlWithWmax = $this->capitaltransactionheader_model->getWdwlWithWmax($result_employee_id["list"][0]["employee_id"],$accounting_period);
			
			if($resultWdwlWithWmax["count"] > 0){
			log_message("debug","yyyyy".$resultWdwlWithWmax["list"][0]["transaction_no"]);
				// $param = array("transaction_no", $resultWdwlWithWmax["list"][0]["transaction_no"],"transaction_code"=>"WMAX");
				$this->capitaltransactiondetail_model->setValue('status_flag', '0');
				$delDtlResult = $this->capitaltransactiondetail_model->update(array("transaction_no"=> $resultWdwlWithWmax["list"][0]["transaction_no"],"transaction_code"=>"WMAX"));
			}
		
		}
		
		//else do below if there are no wmax that are to delete
		//[end]20111121 modified by asi466 issue:0008377
		
		
		if (array_key_exists('capcon',$_REQUEST)) {
			$this->capitaltransactionheader_model->setValue('status_flag', '0');

			$this->db->trans_start();
			$delHdrResult = $this->capitaltransactionheader_model->update(array(
				'transaction_no' => $_REQUEST['capcon']['transaction_no']
//			,'status_flag' => '1'
			));

			if($delHdrResult['affected_rows'] <= 0){
				echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
			} else {
				$this->capitaltransactiondetail_model->setValue('transaction_no', $_REQUEST['capcon']['transaction_no']);
				$delDtlResult = $this->capitaltransactiondetail_model->delete();

				if($delDtlResult['error_code'] == 0)
				echo "{'success':true,'msg':'Data successfully deleted.'}";
				else
				echo '{"success":false,"msg":"'.$delDtlResult['error_message'].'"}';
			}

			$this->db->trans_complete();
		} else
		echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";

		log_message('debug', "[END] Controller gl_entries:deleteHdr");
	}

	/**
	 * @desc Delete all details of a capital transaction
	 */
	function deleteDtl(){
		log_message('debug', "[START] Controller capital_transaction:deleteDtl");
		log_message('debug', "capcon param exist?:".array_key_exists('capcon',$_REQUEST));

		if (array_key_exists('capcon',$_REQUEST)) {
			$this->capitaltransactiondetail_model->setValue('transaction_no', $_REQUEST['capcon']['transaction_no']);
			$delDtlResult = $this->capitaltransactiondetail_model->delete();

			if($delDtlResult['error_code'] == 0)
			echo "{'success':true,'msg':'Data successfully deleted.'}";
			else
			echo '{"success":false,"msg":"'.$delDtlResult['error_message'].'"}';

		} else
		echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";

		log_message('debug', "[END] Controller gl_entries:deleteDtl");
	}
	
	function depositGreaterThanTenMillion($transaction_amount, $employee_id, $accounting_period, $transaction_no=null){
		//Get capital contribution balance
		$data = $this->capitalcontribution_model->get(
		array('accounting_period'=>$accounting_period,'employee_id'=>$employee_id)
		,array('ending_balance') 
		);
		
		if($data['count']>0){
			$ending_balance  = $data['list'][0]['ending_balance'];
		}
		else{
			$ending_balance = 0;
		}
		
		//Sum all deposit amounts of employee
		$param = array('employee_id'=> $employee_id, 'transaction_code'=>'DDEP', 'status_flag' => '1');
		//transaction no is set (update is performed) exclude the specified transaction no
		if($transaction_no){
			$param['transaction_no !='] = $transaction_no;
		}
		
		$data = $this->capitaltransactionheader_model->get(
			$param
			,array('SUM(transaction_amount) AS total_deposit')
		);
		
		if($data['count']>0){
			$total_deposit  = $data['list'][0]['total_deposit'];
		}
		else{
			$total_deposit = 0;
		}
		
		//Sum up ending balance, unposted deposit amounts and deposited amount
		if(($total_deposit+$ending_balance+$transaction_amount)>10000000){
			return 1;
		}
		else{
			return 0;
		}	
	}
	
	function noOfWithdrawals($trans_no=null){
		$accounting_period = date("Ym", strtotime($this->parameter_model->getParam('ACCPERIOD')));
		$employee_id = $_REQUEST['capcon']['employee_id'];
		
		//Get all withdrawal t transactions of employee
		$param = array("transaction_code" => "WDWL", "employee_id"=> $employee_id, "transaction_date LIKE" => "$accounting_period%", "status_flag" => "1");
		//if update exclude the capcon to be updated
		//[start]20111121 modified by asi466 issue:0008377
		$param['transaction_no <='] = $trans_no;
		//[end]20111121 modified by asi466 issue:0008377
		
		$data = $this->capitaltransactionheader_model->get($param);
		
		$t_count = $data['count'];
		//Get all withdrawal m transactions of employee
		$data = $this->mcaptranheader_model->get(
		array("transaction_code" => "WDWL", "employee_id"=> $employee_id, "transaction_date LIKE" => "$accounting_period%", "status_flag" => "2")
		);
		
		$m_count = $data['count'];
		
		//add and return all withdrawals
		return ($t_count+$m_count);
	}
	
	/*function getInterestAndRemainingTerms2(){
	$data1 = $this->tloan_model->get(array('employee_id'=> $employee_id, 'status_flag'=> '1'),
			array('principal_balance', 'employee_principal_amort', 'employee_interest_amortization'));
		$data2 = $this->mloan_model->get(array('employee_id'=>$employee_id, 'status_flag'=>'2', 'close_flag' => '0')
			,array('principal_balance', 'employee_principal_amort', 'employee_interest_amortization'));
		$remaining_terms = 0;	
		$interest = 0;
		$data = array_merge($data1, $data2);
		foreach($data as $loan_details){
			//Formula:
			//Remaining Terms = Loan Balance / Principal Amortization
			//Interest = Remaining Terms * Interest Amortization
			$remaining_terms +=  $loan_details['principal_balance'] / $loan_details['employee_principal_amort'];
			$interest += $remaining_terms + $loan_details['employee_interest_amortization'];
		} 
		return array('remaining_terms' => $remaining_terms
			,'interest' => $interest
		);
	}*/
}

/* End of file capital_transaction.php */
/* Location: ./CodeIgniter/application/controllers/capital_transaction.php */
