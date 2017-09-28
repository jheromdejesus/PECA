<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Process_loan_payment extends Asi_controller {

	function Process_loan_payment(){
		parent::Asi_controller();
		$this->load->model('Company_model');
		$this->load->model('Employee_model');
		$this->load->model('Capitalcontribution_model');
		$this->load->model('MLoan_model');
		$this->load->model('MLoanpayment_model');
		$this->load->model('TLoanpaymentdetail_model');
		$this->load->model('MLoanpaymentdetail_model');
		$this->load->model('Loancodeheader_model');
		$this->load->model('Loancodedetail_model');
		$this->load->model('Parameter_model');
		$this->load->model('Ttransaction_model');
		$this->load->model('Transactioncharges_model');
		$this->load->model('Lockmanager_model');
		$this->load->helper('url');
		$this->load->library('constants');
		//$this->load->scaffolding('t_loan');
	}
	
//	function index(){		
//		$date = $this->Parameter_model->retrieveValue('CURRDATE');
////		echo $date;
//		$_REQUEST['process_lp'] = array('company' => '403',
//										'penalty' => '1',
//										'loan' => array('CONL'=>0, 'MNIL'=>1),
//										'user_id' => 'mowkz');
////		echo floor($this->dateDiff('19960912', date("Ymd", time()))/365) . " years.";
////		$result = $this->Transactioncharges_model->get(array('transaction_code'=>'BMBC', 'charge_code'=>'BMBC'));
////		$bmbc_charge = $this->Transactioncharges_model->retrieveChargeFormula('MNIL','PENT');
//
////		$this->getCompany();
////		$this->getLoans();		
////		$this->processLoanPayment();
//
////		echo "{success:true" .
////				", data:{currdate:'{$date}', loans:['MNIL', 'CONL']}" .
////				"}";
//		$a = array('CONL'=>1, 'MNIL'=>1);
//		foreach($a as $key=>$value){
//			if ($value == 1){
//				//echo $key . '</br>';
//			}
//		}
//
//		echo "{success:true" .
//				", data:{currdate:'20100415', loans:['MNIL', 'CONL']}" .
//				"}";
//	}

 	function index(){		
		$date = $this->Parameter_model->retrieveValue('ACCPERIOD');
		$date = date('F Y',strtotime($date));
		
		echo json_encode(array(
            'data' => array(array('currdate' => $date))
        ));
	}
	
	function getCompany(){
		$data = $this->Company_model->get();
		
		echo json_encode(array(
            'data' => $data['list']
        ));
	}
	
	function getLoans(){
		$data = $this->Loancodeheader_model->get(null, array('loan_code', 'loan_description'));
		
		echo json_encode(array(
            'data' => $data['list']
        ));
	}
	
	function dateDiff($start, $end){
		$j_start = gregoriantojd(substr($start, 4, 2), substr($start, 6, 2), substr($start, 0, 4));
		$j_end = gregoriantojd(substr($end, 4, 2), substr($end, 6, 2), substr($end, 0, 4));
		
		return $j_end - $j_start;
	}
	
	function processLoanPayment(){
		//get posted data
		$plp_array = isset($_REQUEST['process_lp']) ? $_REQUEST['process_lp'] : array();
		$company = isset($plp_array['company']) ? $plp_array['company'] : '';
		$penalty = isset($plp_array['penalty']) ? 1 : 0;
		$loans_array = json_decode(isset($plp_array['data']) ? $plp_array['data']: array(), true);
		$user = isset($plp_array['user_id']) ? $plp_array['user_id']: '';	
		$this->Lockmanager_model->user = $user;	

		try{
			//acquire lock
			$permitted = $this->Lockmanager_model->acquire($this->constants->batch_lock, 10);
			$refresh = $this->constants->lock_refresh;
			$new_time = date('Ymdhis',strtotime("+{$refresh} minute"));
			if (!$permitted){
				$resp_message = "Server is busy.</br>Other user is doing batch process.";
				echo '{"success":false,"msg":"'.$resp_message.'"}';
				exit();
			}
			
			$date = $this->Parameter_model->retrieveValue('CURRDATE');
			$acctg_period = $this->Parameter_model->retrieveValue('ACCPERIOD');
			$min_bal = $this->Parameter_model->retrieveValue('CCMINBAL');
			$cryover = $this->Parameter_model->retrieveValue('LPCRRYOVR');
			
			//transaction - begin
			$this->db->trans_begin();
			$tran_no = $this->Parameter_model->retrieveValue('PECATRANNO');
			
			//sort loans by priority
			$loans_array = $this->Loancodeheader_model->retrieveLoanCodeByPriority($loans_array);
			
			foreach ($loans_array as $row){				
				$l_code = $row['loan_code'];
				log_message('debug', "processing loan {$l_code}");
				
				//get loans with no payment made yet within the accounting period
				$lp_array = $this->MLoan_model->retrieveLoanCC($acctg_period, $company, $l_code);
				log_message('debug', "SQL = {$this->db->last_query()}");
				
				//start the bloodshed
				$tran_param = array();
				$loan_param = array();
				$detail_param = array();
	//			$tran_no = $this->Parameter_model->retrieveValue('PECATRANNO');
				
				foreach ($lp_array as $row){
					$loan_no = $row['loan_no'];
					$loan_code = $row['loan_code'];
					$pay_code = $row['payment_code'];
					$employee = $row['employee_id'];
					$i_amort = $row['employee_interest_amortization'];
					$prin_bal = $row['principal_balance'];
					$prin_amort = ($prin_bal - $row['employee_principal_amort'] < $cryover) ? $prin_bal : $row['employee_principal_amort'];
					log_message('debug', "{$loan_no} and {$loan_code}");
					
					//get employee info
					$emp_info = $this->Employee_model->get(array('employee_id' => $employee), null);
					$emp_info = $emp_info['list'][0];
					//retrieve employees balance
					$capcon_bal = $this->Capitalcontribution_model->retrieveCapConBalance($employee, $acctg_period);
					log_message('debug', "capcon_bal = {$capcon_bal}");
					//get years of service
					$yos = floor($this->dateDiff($emp_info['hire_date'], $date)/365);
					
	//	    		echo $this->Loancodedetail_model->isCapConRequired($loan_code, $yos);
					
					//alot to do
					if ($capcon_bal - ($prin_amort + $i_amort) >= $min_bal){
						log_message('debug', "insert to loan payment");
						//insert to t_loan_payment
						$loan_param['loan_no'] = $loan_no;
						$loan_param['payment_date'] = $date;
						$loan_param['transaction_code'] = $pay_code;
						$loan_param['amount'] = $prin_amort;
						$loan_param['interest_amount'] = $i_amort;
						$loan_param['payor_id'] = $employee;
						$loan_param['source'] = 'S';
						$loan_param['remarks'] = '';
						$loan_param['balance'] = $prin_bal - $prin_amort;
						$loan_param['status_flag'] = $this->constants->table_status['PROCESSED'];
						$loan_param['created_by'] = $user;
						$loan_param['modified_by'] = $user;
						
						$this->MLoanpayment_model->populate($loan_param);
						$result = $this->MLoanpayment_model->insert();
						if ($result['error_code'] == '1'){
							throw new Exception($result['error_message']);
						}
						
						log_message('debug', "insert to transaction");
						//insert to t_transaction
						$tran_param['transaction_no'] = ++$tran_no;
						$tran_param['transaction_date'] = $date;
						$tran_param['transaction_code'] = $pay_code;
						$tran_param['employee_id'] = $employee;
						$tran_param['transaction_amount'] = $prin_amort + $i_amort;
						$tran_param['source'] = 'm_loan_payment';
	//		    		$tran_param['reference'] = $loan_no . '_' . $loan_code . '_' . $date . '_' . $employee;
						$tran_param['reference'] = $loan_no . ',' . $pay_code . ',' . $date . ',' . $employee;
	//		    		$tran_param['remarks'] = "CONCAT(loan_no, '_', transaction_code, '_', payment_date, '_', payor_id)";
						$tran_param['remarks'] = "loan_no,transaction_code,payment_date,payor_id";
						$tran_param['status_flag'] = $this->constants->table_status['PROCESSED'];
						$tran_param['created_by'] = $user;
						$tran_param['modified_by'] = $user;
						
						$this->Ttransaction_model->populate($tran_param);
						$result = $this->Ttransaction_model->insert();
						if ($result['error_code'] == '1'){
							throw new Exception($result['error_message']);
						}
						
						//check for capcon req here (1/3 stuff)
						$is_capcon_req = $this->Loancodedetail_model->isCapConRequired($loan_code, $yos);
						$capcon_req = 0;
						if ($is_capcon_req){
							$capcon_req = $this->MLoan_model->retrieveLoanCCBalance($employee);
						}
						
						if ($capcon_bal - ($prin_amort + $i_amort) < $capcon_req){
							//get charge amount BMBC
							$bmbc_charge = $this->Transactioncharges_model->retrieveChargeFormula('BMBC','BMBC');
							//insert to t_loan_payment_detail
							$detail_param['loan_no'] = $loan_no;
							$detail_param['payment_date'] = $date;
							$detail_param['transaction_code'] = 'BMBC';
							$detail_param['payor_id'] = $employee;			    		
							$detail_param['amount'] = $bmbc_charge;
							$detail_param['status_flag'] = $this->constants->table_status['NEW'];
							$detail_param['created_by'] = $user;
							$detail_param['modified_by'] = $user;			    		
							
							$this->TLoanpaymentdetail_model->populate($detail_param);
							$result = $this->TLoanpaymentdetail_model->insert();
							if ($result['error_code'] == '1'){
								throw new Exception($result['error_message']);
							}
						}
					}
					
					//insert pent here
					if ($penalty == 1){
						$pent_charge = $this->Transactioncharges_model->retrieveChargeFormula($loan_code,'PENT');
						$tran_param['transaction_no'] = ++$tran_no;
						$tran_param['transaction_date'] = $date;
						$tran_param['transaction_code'] = 'PENT';
						$tran_param['employee_id'] = $employee;
						$tran_param['transaction_amount'] = $pent_charge;
						$tran_param['source'] = 't_transaction';
						$tran_param['reference'] = $tran_no;
						$tran_param['remarks'] = 'transaction_no';
						$tran_param['status_flag'] = $this->constants->table_status['PROCESSED'];
						$tran_param['created_by'] = $user;
						$tran_param['modified_by'] = $user;	
						
						$this->Ttransaction_model->populate($tran_param);
						$result = $this->Ttransaction_model->insert();
						if ($result['error_code'] == '1'){
							throw new Exception($result['error_message']);
						}
						
						//for approval from ernie
						//insert one time pd
						if (($capcon_bal - $pent_charge) < $min_bal){
							$tran_param['transaction_no'] = ++$tran_no;
							$tran_param['transaction_date'] = $date;
							$tran_param['transaction_code'] = 'PDED';
							$tran_param['employee_id'] = $employee;
							$tran_param['transaction_amount'] = $pent_charge;
							$tran_param['source'] = 't_transaction';
							$tran_param['reference'] = $tran_no;
							$tran_param['remarks'] = 'transaction_no';
							$tran_param['status_flag'] = $this->constants->table_status['PROCESSED'];
							$tran_param['created_by'] = $user;
							$tran_param['modified_by'] = $user;	
							
							$this->Ttransaction_model->populate($tran_param);
							$result = $this->Ttransaction_model->insert();
							if ($result['error_code'] == '1'){
								throw new Exception($result['error_message']);
							}
						}    	
					}
					
					//if time is beyond 2 minutes, referesh lock manager
					// if ($new_time <= date('Ymdhis')){
						// $this->Lockmanager_model->setValue('key', date('Ymdhis'));
						// $this->Lockmanager_model->update(array('function_id'=>$this->constants->batch_lock));
						// $new_time = date('Ymdhis',strtotime("+{$refresh} minute"));
					// }
				}
			}
			
			//update peca tran no
			$result = $this->Parameter_model->updateValue('PECATRANNO', $tran_no, $user);
			
			//transaction - commit or rollback
			//$this->db->trans_complete();
			
			if($this->db->trans_status() === TRUE){  
				$this->db->trans_commit();
			  echo "{'success':true,'msg':'Loan payments sucessfully processed.'}";
			} else{
				$this->db->trans_rollback();
				echo "{'success':false,'msg':'Loan payments failed to process.'}";      
				//echo '{"success":false,"msg":"'.$result['error_message'].'"}';
			}
		} catch (Exception $e){
			$this->db->trans_rollback();
			$resp_message = "";
			if ($e->getMessage() != ''){
				$resp_message .= $e->getMessage() . "</br>";
			}
			$resp_message .= "Loan payments failed to process.";
			echo "{'success':false,'msg':'{$resp_message}'}";    
		}
		
		$this->Lockmanager_model->release($this->constants->batch_lock);
	}	
		
}
?>
