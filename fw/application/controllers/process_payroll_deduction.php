<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Process_payroll_deduction extends Asi_controller {

	function Process_payroll_deduction(){
		parent::Asi_controller();
		$this->load->model('Payrolldeduction_model');
		$this->load->model('Parameter_model');
		$this->load->model('Ttransaction_model');
		$this->load->model('Tloanpayment_model');
		$this->load->model('Mloanpayment_model');
		$this->load->model('Lockmanager_model');
		$this->load->helper('url');
		$this->load->library('constants');
		$this->load->library('asi_model');
		//$this->load->scaffolding('t_loan');
	}
	
	function index(){		
		$date = $this->Parameter_model->retrieveValue('ACCPERIOD');
		$date = date('m/d/Y',strtotime($date));

//		$_REQUEST['process_pd'] = array('savings' => '0',
//										'loans' => '0',
//										'today' => '1',
//										'first_half' => '20100131',
//										'second_half' => '20100131',
//										'user_id' => 'mowkz');
//										
//		$this->processPayrollDeduction();
		
		echo json_encode(array(
            'data' => array(array('currdate' => $date))
        ));
		
		
	}
	
	function processPayrollDeduction(){//possible use $REQUEST[]
		//get posted data
		$pd_array = $_REQUEST['process_pd'];
		$savings = isset($pd_array['savings']) ? 1 : 0;
		$loans = isset($pd_array['loans']) ? 1 : 0;
		$today = isset($pd_array['today']) ? 1 : 0;
		$first_half = isset($pd_array['first_half']) ? date('Ymd', strtotime($pd_array['first_half'])) : '';
		$second_half = isset($pd_array['second_half']) ? date('Ymd', strtotime($pd_array['second_half'])) : '';
		$user = $pd_array['user_id'];			
		$this->Lockmanager_model->user = $user;
		
		//acquire lock for 10 minutes
		$permitted = $this->Lockmanager_model->acquire($this->constants->batch_lock, 20);
		$refresh = $this->constants->lock_refresh;
		$new_time = date('Ymdhis',strtotime("+{$refresh} minute"));
		if (!$permitted){
			$resp_message = "Server is busy.</br>Other user is doing batch process.";
			echo '{"success":false,"msg":"'.$resp_message.'"}';
			exit();
		}
		
		$error = 0;
		$resp_message = '';
		$date = $this->Parameter_model->retrieveValue('CURRDATE');
		$acctg_period = $this->Parameter_model->retrieveValue('ACCPERIOD');

		if ($savings == 1){
			try{
				//transaction - begin
				$this->db->trans_begin();
				
				$tran_param = array();
				$tran_no = $this->Parameter_model->retrieveValue('PECATRANNO');
				
				$payrolldedn_array = $this->Payrolldeduction_model->retrievePayrollDeductionSavings($date);		
				//delete existing payroll deductions of the month
				$result = $this->Ttransaction_model->deleteTransactionByGroup($date, 'PD');
				if ($result['error_code'] == '1'){
					throw new Exception($result['error_message']);
				}
				
				//check if need to insert 2 transactions per payroll
				$qty = ($first_half == $second_half) ? 1 : 2;
					
				foreach ($payrolldedn_array as $row){
					$tran_param['transaction_no'] = ++$tran_no;
					$tran_param['transaction_date'] = $first_half;
					$tran_param['transaction_code'] = $row['transaction_code'];
					$tran_param['employee_id'] = $row['employee_id'];
					$tran_param['transaction_amount'] = $row['amount'] / $qty;
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
					
	//	    		echo $result['error_message'];
					
					if ($qty == 2){
						$tran_param['transaction_no'] = ++$tran_no;
						$tran_param['transaction_date'] = $second_half;
						$tran_param['reference'] = $tran_no;
						
						$this->Ttransaction_model->populate($tran_param);
						$result = $this->Ttransaction_model->insert();
						if ($result['error_code'] == '1'){
							throw new Exception($result['error_message']);
						}
					}
					
					//if time is beyond 2 minutes, referesh lock manager
					// if ($new_time <= date('Ymdhis')){
						// $this->Lockmanager_model->setValue('key', date('Ymdhis'));
						// $this->Lockmanager_model->update(array('function_id'=>$this->constants->batch_lock));
						// $new_time = date('Ymdhis',strtotime("+{$refresh} minute"));
					// }
				}
				//update peca tran no
				$result = $this->Parameter_model->updateValue('PECATRANNO', $tran_no, $user);
				if ($result['error_code'] == '1'){
					throw new Exception($result['error_message']);
				}
				
				//transaction - commit or rollback
				//$this->db->trans_complete();	
				if ($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$this->db->_trans_status = TRUE;
					++$error;
					$resp_message .= "Payroll deduction for savings failed to process.</br>";
					//$resp_message['savings'] = '{"success":false,"msg":"'.$result['error_message'].'"}';
				} else{
					$this->db->trans_commit();
					$resp_message .= "Payroll deduction for savings successfully processed.</br>";
					//$resp_message['savings'] = "{'success':true,'msg':'Payroll deduction for savings successfully processed.'}";
				}
			} catch(Exception $e){
				++$error;
				$this->db->trans_rollback();
				$this->db->_trans_status = TRUE;
				$resp_message = "";
				if ($e->getMessage() != ''){
					$resp_message .= $e->getMessage() . "</br>";
				}
				$resp_message .= "Payroll deduction for savings failed to process.</br>";
			}
			
			//update lock manager that we are still using it
			//put this out from transaction to update lock manager
			$this->Lockmanager_model->setValue('key', date('Ymdhis'));
			$this->Lockmanager_model->update(array('function_id'=>$this->constants->batch_lock));
		}
		
		if ($loans == 1){
			try{
				//transaction - begin
				$this->db->trans_begin();
				
				$tran_param = array();
				$tran_no = $this->Parameter_model->retrieveValue('PECATRANNO');
				$cryover = $this->Parameter_model->retrieveValue('LPCRRYOVR');
				
				$payrolldedn_array = $this->Payrolldeduction_model->retrievePayrollDeductionLoans($acctg_period);	
					
				//delete existing payroll deductions for loans - transaction table
				$result = $this->Ttransaction_model->deleteTransactionLoanPayment($first_half);
				if ($result['error_code'] == '1'){
					throw new Exception($result['error_message']);
				}
				//delete existing payroll deductions for loans - loan payment table
				$where = array('payment_date' => $first_half,
								'status_flag' => $this->constants->table_status['PROCESSED'],
								'source' => 'P');
				$result = $this->Mloanpayment_model->delete($where);
				if ($result['error_code'] == '1'){
					throw new Exception($result['error_message']);
				}
				foreach ($payrolldedn_array as $row){
					//save loan payment
					$amort = $row['employee_principal_amort'];
					$interest = $row['employee_interest_amortization'];
					$bal = $row['principal_balance'];
					
					//compare first the amort and bal to determine the amount of payroll ded
					$newamort = ($bal - $amort < $cryover) ? $bal : $amort;
					$newbal = ($bal - $amort < $cryover) ? 0 : $bal - $amort;
					$lp_param['loan_no'] = $row['loan_no'];
					$lp_param['payment_date'] = $first_half;
					$lp_param['transaction_code'] = $row['payment_code'];
					$lp_param['amount'] = $newamort;
					//[START] 7th Enhancement
					//$lp_param['interest_amount'] = $row['employee_interest_amortization'];
					if($row["bsp_computation"] == "Y") {
						$lp_param['interest_amount'] = round(($row["principal_balance"] * ($row["interest_rate"]/100)) / 12);
					}
					else {
						$lp_param['interest_amount'] = $row['employee_interest_amortization'];
					}
					//[END] 7th Enhancement
					$lp_param['payor_id'] = $row['employee_id'];
					$lp_param['source'] = 'P';
					$lp_param['remarks'] = '';
					$lp_param['balance'] = $newbal;
					$lp_param['status_flag'] = $this->constants->table_status['PROCESSED'];
					$lp_param['created_by'] = $user;
					$lp_param['modified_by'] = $user;
					
					$this->Mloanpayment_model->populate($lp_param);
					$result = $this->Mloanpayment_model->insert();
					if ($result['error_code'] == '1'){
						throw new Exception($result['error_message']);
					}
					
					//save transaction
					$tran_param['transaction_no'] = ++$tran_no;
					$tran_param['transaction_date'] = $first_half;
					$tran_param['transaction_code'] = $row['payment_code'];
					$tran_param['employee_id'] = $row['employee_id'];
					//[START] 7th Enhancement
					//$tran_param['transaction_amount'] = $newamort + $row['employee_interest_amortization'];
					if($row["bsp_computation"] == "Y") {
						$tran_param['transaction_amount'] = $newamort + round(($row["principal_balance"] * ($row["interest_rate"]/100)) / 12);
					}
					else {
						$tran_param['transaction_amount'] = $newamort + $row['employee_interest_amortization'];
					}					
					//[END] 7th Enhancement					
					$tran_param['source'] = 'm_loan_payment';
					$tran_param['reference'] = $row['loan_no'] . ',' . $row['payment_code'] . ',' . $first_half . ',' . $row['employee_id'];
					$tran_param['remarks'] = "loan_no,transaction_code,payment_date,payor_id";
					$tran_param['status_flag'] = $this->constants->table_status['PROCESSED'];
					$tran_param['created_by'] = $user;
					$tran_param['modified_by'] = $user;
					
					$this->Ttransaction_model->populate($tran_param);
					$result = $this->Ttransaction_model->insert();
					if ($result['error_code'] == '1'){
						throw new Exception($result['error_message']);
					}
					
					//if time is beyond 2 minutes, referesh lock manager
					// if ($new_time <= date('Ymdhis')){
						// $this->Lockmanager_model->setValue('key', date('Ymdhis'));
						// $this->Lockmanager_model->update(array('function_id'=>$this->constants->batch_lock));
						// $new_time = date('Ymdhis',strtotime("+{$refresh} minute"));
					// }
				}
				
	//			update peca tran no
				$result = $this->Parameter_model->updateValue('PECATRANNO', $tran_no, $user);
				if ($result['error_code'] == '1'){
					throw new Exception($result['error_message']);
				}
				
				//transaction - commit or rollback
				//this->db->trans_complete();			
				
				if ($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$this->db->_trans_status = TRUE;
					++$error;
					$resp_message .= "Payroll deduction for loans failed to process.</br>";
					//$resp_message['savings'] = '{"success":false,"msg":"'.$result['error_message'].'"}';
				} else{
					$this->db->trans_commit();
					$resp_message .= "Payroll deduction for loans successfully processed.</br>";
					//$resp_message['savings'] = "{'success':true,'msg':'Payroll deduction for savings successfully processed.'}";
				}
			} catch(Exception $e){
				++$error;
				$this->db->trans_rollback();
				$this->db->_trans_status = TRUE;
				$resp_message = "";
				if ($e->getMessage() != ''){
					$resp_message .= $e->getMessage() . "</br>";
				}
				$resp_message .= "Payroll deduction for loans failed to process.</br>";
			}
			
			//update lock manager that we are still using it
			//put this out from transaction to update lock manager
			$this->Lockmanager_model->setValue('key', date('Ymdhis'));
			$this->Lockmanager_model->update(array('function_id'=>$this->constants->batch_lock));
		}
		
		if ($today == 1){
			try{
				//transaction - begin
				$this->db->trans_begin();
				
				//update loan payment table
	//	    	$lp_param['payment_date'] = $date;
	//    		$lp_param['modified_by'] = $user;
	//	    	$this->Loanpayment_model->populate($lp_param);
	//	    	$result = $this->Loanpayment_model->update(array('source' => 'P', 'status_flag' => '2'));
				//update loan payment table
				$result = $this->Mloanpayment_model->updateLoanPayrollDeduction($date, $user);
				if ($result['error_code'] == '1'){
					throw new Exception($result['error_message']);
				}
				
				//refresh lock manager
				// $this->Lockmanager_model->setValue('key', date('Ymdhis'));
				// $this->Lockmanager_model->update(array('function_id'=>$this->constants->batch_lock));
				
				//update transaction table
				$result = $this->Ttransaction_model->updateTransactionPayrollDeduction($date, $user);
				if ($result['error_code'] == '1'){
					throw new Exception($result['error_message']);
				}
				//transaction - commit or rollback
				//$this->db->trans_complete();			
				if ($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$this->db->_trans_status = TRUE;
					++$error;
					$resp_message .= "Payroll deduction for today failed to process.</br>";
					//$resp_message['pdtoday'] = '{"success":false,"msg":"'.$result['error_message'].'"}';
				} else{
					$this->db->trans_commit();
					$resp_message .= "Payroll deduction for today successfully processed.</br>";
					//$resp_message['pdtoday'] = "{'success':true,'msg':'Payroll deductions transactions ready for processing.'}";
				}
			} catch(Exception $e){
				++$error;
				$this->db->trans_rollback();
				$this->db->_trans_status = TRUE;
				$resp_message = "";
				if ($e->getMessage() != ''){
					$resp_message .= $e->getMessage() . "</br>";
				}
				$resp_message .= "Payroll deduction for today failed to process.</br>";
			}
		}
		
		$this->Lockmanager_model->release($this->constants->batch_lock);
		
		if ($error > 0){
			echo '{"success":false,"msg":"'.$resp_message.'"}';
		} else{
			echo '{"success":true,"msg":"'.$resp_message.'"}';
		}
	}	
		
}
?>
