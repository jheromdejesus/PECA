<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Process_transaction extends Asi_controller {

	function Process_transaction(){
		parent::Asi_controller();
		$this->load->model('Tinvestmentheader_model');
		$this->load->model('Minvestmentheader_model');
		$this->load->model('Minvestmentdetail_model');
		$this->load->model('Tloan_model');
		$this->load->model('Mloan_model');
		$this->load->model('Mloancharges_model');
		$this->load->model('Mloanguarantor_model');
		$this->load->model('Loancharges_model');
		$this->load->model('Loanguarantor_model');
		$this->load->model('Tloanpayment_model');
		$this->load->model('Mloanpayment_model');
		$this->load->model('Tloanpaymentdetail_model');
		$this->load->model('Mloanpaymentdetail_model');
		$this->load->model('Capitalcontribution_model');
		$this->load->model('Tcaptranheader_model');
		$this->load->model('Tcaptrandetail_model');
		$this->load->model('Mcaptranheader_model');
		$this->load->model('Mcaptrandetail_model');
		$this->load->model('Tinvestmentheader_model');
		$this->load->model('Minvestmentheader_model');
		$this->load->model('Transactioncharges_model');
		$this->load->model('Parameter_model');
		$this->load->model('Ttransaction_model');
		$this->load->model('Transactiongroup_model');
		$this->load->model('Tcovered_model');
		$this->load->model('Tloanamort_model');
		$this->load->model('Loancodeheader_model');
		$this->load->model('Amlamap_model');
		$this->load->model('Tcovered_model');
		$this->load->model('Glentryheader_model');
		$this->load->model('Glentrydetail_model');
		$this->load->model('Tjournalheader_model');
		$this->load->model('Mjournaldetail_model');
		$this->load->model('Tjournaldetail_model');
		$this->load->model('Mcjournaldetail_model');
		$this->load->model('Mcjournalheader_model');
		$this->load->model('Transactioncode_model');
		$this->load->model('Lockmanager_model');
		$this->load->model('Loancodedetail_model');
		$this->load->model('Member_model');
		$this->load->helper('url');
		$this->load->library('constants');
		//$this->load->scaffolding('t_loan');
	}
	
	function index(){		
		$period = $this->Parameter_model->retrieveValue('ACCPERIOD');
		$period = date('F Y',strtotime($period));
		$date = $this->Parameter_model->retrieveValue('CURRDATE');
		$date = date('m/d/Y',strtotime($date));
		
		echo json_encode(array(
            'data' => array(array('current_period' => $period, 'posting_date' => $date))
        ));
		
//		$_REQUEST['protrans'] = array('user_id' => 'PECA'
//											, 'data' => '["II"]'
////											, 'bmb' => 'on'
////											, 'npml' => 0
//											);
//											
////		$next_acctg = date('Ymd',strtotime('+1 month',strtotime($period)));
////		$last_day = date('Ymd',strtotime('-1 day',strtotime($next_acctg)));
////		echo $last_day;
//		$this->processTransactions();
	}
	
	function load(){
		
		$period = $this->Parameter_model->retrieveValue('ACCPERIOD');
		$period = date('F Y',strtotime($period));
		$date = $this->Parameter_model->retrieveValue('CURRDATE');
		//$date = date('m/d/Y',strtotime($date));
		
		$trans = array();
		//CC
		$result = $this->Tcaptranheader_model->get_list(array('status_flag'=>'1'),null,null,'transaction_no',null);
		if ($result['count'] > 0){
			$trans['CC'] = 'Capital Contribution (' . $result['count'] . ')';
		}
		
		//LN
		$result = $this->Tloan_model->get_list(array('status_flag'=>'1'),null,null,'loan_no',null);
		if ($result['count'] > 0){
			$trans['LN'] = 'New Loan (' . $result['count'] . ')';
		}
		
		
		//LP
		$result = $this->Tloanpayment_model->get_list(array('status_flag'=>'1'),null,null,'loan_no',null);
		if ($result['count'] > 0){
			$trans['LP'] = 'Loan Payment (' . $result['count'] . ')';
		}
		
		//IN
		$result = $this->Tinvestmentheader_model->get_list(array('status_flag'=>'1'),null,null,'investment_no',null);
		if ($result['count'] > 0){
			$trans['IN'] = 'New Investment (' . $result['count'] . ')';
		}
		
		//II
		$result = $this->Minvestmentheader_model->get_list(array('transaction_date'=>$date, 'processed'=>'0', 'status_flag'=>'2')
						,null,null,'investment_no',null);
		if ($result['count'] > 0){
			$trans['II'] = 'Investment Maturity (' . $result['count'] . ')';
		}
		
		$data = $this->constants->create_list($trans);
		
		echo json_encode(array(
            'data' => $data
        )); 
	}
	
	function processTransactions(){
		//get posted transactions
		$pt_array = isset($_REQUEST['protrans']) ? $_REQUEST['protrans'] : array();
		$transactions = json_decode(isset($pt_array['data']) ? $pt_array['data']: '', true);
		$bmb = isset($pt_array['bmb']) ? 1 : 0;
		$npml = isset($pt_array['npml']) ? 1 : 0;
		$user = isset($pt_array['user_id']) ? $pt_array['user_id']: '';	
		$this->Lockmanager_model->user = $user;
		
		//acquire lock
		$permitted = $this->Lockmanager_model->acquire($this->constants->batch_lock);
		$refresh = $this->constants->lock_refresh;
		$new_time = date('Ymdhis',strtotime("+{$refresh} minute"));
		if (!$permitted){
			$resp_message = "Server is busy.</br>Other user is doing batch process.";
			echo '{"success":false,"msg":"'.$resp_message.'"}';
			exit();
		}
		
		//get values from parameters
		$date = $this->Parameter_model->retrieveValue('CURRDATE');
		$acctg_period = $this->Parameter_model->retrieveValue('ACCPERIOD');
		$cover_limit = $this->Parameter_model->retrieveValue('COVERLIMIT');
		
		$error = 0;
		$resp_message = '';		
		$tran_param = array();
				
		foreach ($transactions as $key){
		
			$trans_group = $key;
			$group = $trans_group;			
			//CC
			if ($group == 'CC'){
				try{
					//transaction - begin
					$this->db->trans_begin();
					$tran_no = $this->Parameter_model->retrieveValue('PECATRANNO');
					
					$trans_group_desc = 'Capital Contribution';				
					$result = $this->Tcaptranheader_model->get_list(array('status_flag'=>'1'),null,null,null,null);
					// $result = $this->Tcaptranheader_model->get_list_with_lock("transaction_no
																				// , transaction_date
																				// , transaction_code
																				// , employee_id
																				// , transaction_amount"
																			// ,"status_flag = 1");
					$cap_trans = $result['list'];
					
					foreach ($cap_trans as $row){
						$captran_no = $row['transaction_no'];
						$tran_code = $row['transaction_code'];
						$tran_date = $row['transaction_date'];
						$tran_amount = $row['transaction_amount'];
						$employee_id = $row['employee_id'];
						//insert to ttransaction table
						$tran_param['transaction_no'] = ++$tran_no;
			    		$tran_param['transaction_date'] = $date;
			    		$tran_param['transaction_code'] = $tran_code;
			    		$tran_param['employee_id'] = $employee_id;
			    		$tran_param['transaction_amount'] = $tran_amount;
			    		$tran_param['source'] = 'm_capital_transaction_header';
			    		$tran_param['reference'] = $row['transaction_no'];
			    		$tran_param['remarks'] = 'transaction_no';
			    		$tran_param['status_flag'] = $this->constants->table_status['PROCESSED'];
			    		$tran_param['created_by'] = $user;
			    		$tran_param['modified_by'] = $user;
			    		
			    		//if withrawal, check logic here
			    		if ($tran_code == 'WDWL'){
			    			$allowed_amount = $this->getWithrawableAmount($employee_id, $acctg_period);
							$allowed_amount = round($allowed_amount,2);
							$tran_amount = round($tran_amount,2);
							log_message('debug', 'transAmtzzzzz:' . $tran_amount . 'allowedWdwzzzzz' . $allowed_amount . 'bool' . ($allowed_amount - $tran_amount));
			    			if ($tran_amount > $allowed_amount){
			    				throw new Exception('Capcon balance after transaction is lesser than the capital contribution minimum balance.');
			    			}
							else if(($this->showCompCode($employee_id)=="920") &&
								$tran_amount > $this->allowedWdwlForRetiree($employee_id)){
								throw new Exception('Employee has exceeded the withdrawal amount for a retiree');
							}
			    		}
			    		
			    		$this->Ttransaction_model->populate($tran_param);
			    		$result = $this->Ttransaction_model->insert();
			    		if ($result['error_code'] == '1'){
			    			throw new Exception('');
			    		}
						
						//amla
		    			if ($tran_amount >= $cover_limit){
		    				$data = $this->Amlamap_model->get(array('transaction_code'=>$tran_code));
							$data = $this->Transactioncode_model->get(array('transaction_code'=>$tran_code), "amla_code");
		    				$amla_code = count($data['list']) > 0 ? $data['list'][0]['amla_code'] : '';
		    				$result = $this->Tcovered_model->saveAmla($tran_no, $tran_date, $tran_code, $tran_amount, $employee_id, $amla_code, $user);
			    			if ($result['error_code'] == '1'){
				    			throw new Exception('');
				    		}
		    			}
			    		
			    		//charges
			    		$result = $this->Tcaptrandetail_model->get_list(array('status_flag'=>'1', 'transaction_no'=>$captran_no),null,null,null,null);
						// $result = $this->Tcaptrandetail_model->get_list_with_lock("transaction_no, transaction_code, amount"
																				// ,"status_flag = 1 AND transaction_no = {$captran_no}");
			    		$cap_trans_detail = $result['list'];
			    		foreach ($cap_trans_detail as $row2){
			    			$tran_param['transaction_no'] = ++$tran_no;
			    			$tran_param['transaction_code'] = $row2['transaction_code'];
							$tran_param['transaction_amount'] = $row2['amount'];
							$tran_param['source'] = 'm_capital_transaction_detail';
				    		$tran_param['reference'] = $row2['transaction_no'] . ',' . $row2['transaction_code'];
				    		$tran_param['remarks'] = 'transaction_no,transaction_code';
				    		
				    		$this->Ttransaction_model->populate($tran_param);
				    		$result = $this->Ttransaction_model->insert();
				    		if ($result['error_code'] == '1'){
				    			throw new Exception('');
				    		}
			    		}
					}		
					//promote from t to m
	    			$result = $this->Mcaptranheader_model->batchInsert();	
					if ($result['error_code'] == '1'){
				    	throw new Exception('');
				    }
	    			$result = $this->Mcaptrandetail_model->batchInsert();
					if ($result['error_code'] == '1'){
				    	throw new Exception('');
				    }
	    			
	    			//clean - include status flag 0
	    			$result = $this->Tcaptranheader_model->delete(array('status_flag <='=>'1'));
	    			if ($result['error_code'] == '1'){
				    	throw new Exception('');
				    }
	    			$result = $this->Tcaptrandetail_model->delete(array('status_flag <='=>'1'));
	    			if ($result['error_code'] == '1'){
				    	throw new Exception('');
				    }
	    			//update peca tran no
					$result = $this->Parameter_model->updateValue('PECATRANNO', $tran_no, $user);
					if ($result['error_code'] == '1'){
						throw new Exception('');
					}
//	    			//transaction - commit or rollback
//					$this->db->trans_complete();
					//appended messages like pd
					if($this->db->trans_status() === TRUE){
						$this->db->trans_commit();
						$resp_message .= "{$trans_group} {$trans_group_desc} successfully processed.</br>";
			        } else{
			        	$this->db->trans_rollback();
						$this->db->_trans_status = TRUE;
			        	++$error;
	//		        	$resp_message .= "{$trans_group_desc} - {$trans_group} processing failed.</br>";        	
					  	$resp_message .= "{$trans_group} {$trans_group_desc} processing failed.</br>";
					}		
				} catch (Exception $e){
					 $this->db->trans_rollback();
					 $this->db->_trans_status = TRUE;
					 ++$error;
					  if ($e->getMessage() != ''){
					 	$resp_message .= "{$e->getMessage()}</br>";
					 }
					 $resp_message .= "{$trans_group} {$trans_group_desc} processing failed.</br>";
				}
				
				//if time is beyond 2 minutes, referesh lock manager
				$this->Lockmanager_model->setValue('key', date('Ymdhis'));
				$this->Lockmanager_model->update(array('function_id'=>$this->constants->batch_lock));
			}
			
			//LN
			if ($group == 'LN'){
				try{
					//transaction - begin
					$this->db->trans_begin();
					$tran_no = $this->Parameter_model->retrieveValue('PECATRANNO');
					
					$trans_group_desc = 'New Loan';				
					$result = $this->Tloan_model->get_list(array('status_flag'=>'1'),null,null,null,null);
					// $result = $this->Tloan_model->get_list_with_lock("*"
																	// , "status_flag = 1");
					$ln_trans = $result['list'];
					
					foreach ($ln_trans as $row){
						$loan_no = $row['loan_no'];
						$loan_code = $row['loan_code'];
						$tran_date = $row['loan_date'];
						$tran_amount = $row['principal'];
						$employee_id = $row['employee_id'];
						
						$result = $this->Loancodeheader_model->get(array('status_flag'=>'1', 'loan_code'=>$loan_code), "transaction_code");
						$tran_code = $result['list'][0]['transaction_code'];
						//insert to ttransaction table
						$tran_param['transaction_no'] = ++$tran_no;
			    		$tran_param['transaction_date'] = $date;
			    		$tran_param['transaction_code'] = $tran_code;
			    		$tran_param['employee_id'] = $employee_id;
			    		$tran_param['transaction_amount'] = $tran_amount;
			    		$tran_param['source'] = 'm_loan';
			    		$tran_param['reference'] = $row['loan_no'];
			    		$tran_param['remarks'] = 'loan_no';
			    		$tran_param['status_flag'] = $this->constants->table_status['PROCESSED'];
			    		$tran_param['created_by'] = $user;
			    		$tran_param['modified_by'] = $user;
						
						//get employee info
						$emp_info = $this->Member_model->get(array('employee_id' => $employee_id), null);
						$emp_info = $emp_info['list'][0];
						//get years of service
						$yos = floor($this->dateDiff($emp_info['hire_date'], $date)/365);
						//check for capcon req here (1/3 stuff)
						$is_capcon_req = $this->Loancodedetail_model->isCapConRequired($loan_code, $yos);
						
						//check with if retiree can still loan
						if($this->showCompCode($employee_id) == "920"){
							$wdwlAmtForRetiree  = $this->Capitalcontribution_model->retrieveCapConBalance($employee_id, $acctg_period) * .7;
							$wdwlAmtForRetiree = round($wdwlAmtForRetiree,2);
							log_message('debug', 'wdwlAmtForRetiree '.$wdwlAmtForRetiree);
							log_message('debug', 'showLoanBalance '.$this->showLoanBalance($employee_id));
							log_message('debug', 'principal '.$tran_amount);
							if(($tran_amount +  $this->showLoanBalance($employee_id, $loan_no)) > $wdwlAmtForRetiree){
								throw new Exception('The employee is a retiree. Thus he cannot loan more than 70% of his capital contribution.');
							}
						}
						//if 1/3 required then need to update capcon bal field
						else if ($is_capcon_req){
							$allowed_bol = $this->isAllowedToLoan($employee_id, $acctg_period);
							if (!$allowed_bol){
								log_message('debug', 'xx-xx loan no '.$loan_no);
								throw new Exception('Capcon balance after transaction is below 1/3 of loan principal amount.');
							}
						}
			    		
			    		$this->Ttransaction_model->populate($tran_param);
			    		$result = $this->Ttransaction_model->insert();
			    		if ($result['error_code'] == '1'){
			    			throw new Exception('');
			    		}
			    		
						//amla
		    			if ($tran_amount >= $cover_limit){
		    				$data = $this->Amlamap_model->get(array('transaction_code'=>$tran_code));
							$data = $this->Transactioncode_model->get(array('transaction_code'=>$tran_code), "amla_code");
		    				$amla_code = count($data['list']) > 0 ? $data['list'][0]['amla_code'] : '';
		    				$result = $this->Tcovered_model->saveAmla($tran_no, $tran_date, $tran_code, $tran_amount, $employee_id, $amla_code, $user);
		    				if ($result['error_code'] == '1'){
		    					throw new Exception('');
		    				}
		    			}
						
						//for restructure
						$restr_no = $row['restructure_no'];
						$restr_amount = $row['restructure_amount'];
						
						if ($restr_no != null && $restr_no != ''){
							$restr_code = $this->Parameter_model->retrieveValue($loan_code . 'R');
							$loan_param['loan_no'] = $restr_no;
							$loan_param['payment_date'] = $tran_date;
							$loan_param['transaction_code'] = $restr_code;
							$loan_param['amount'] = $restr_amount;
							$loan_param['interest_amount'] = 0;
							$loan_param['payor_id'] = $employee_id;
							$loan_param['source'] = 'S';
							$loan_param['remarks'] = '';
							$loan_param['balance'] = 0;
							$loan_param['status_flag'] = $this->constants->table_status['PROCESSED'];
							$loan_param['created_by'] = $user;
							$loan_param['modified_by'] = $user;
							
							$this->Mloanpayment_model->populate($loan_param);
							$result = $this->Mloanpayment_model->insert();
							if ($result['error_code'] == '1'){
								throw new Exception('');
							}
							
							//insert to ttransaction table
							$tran_param['transaction_no'] = ++$tran_no;
							$tran_param['transaction_date'] = $date;
							$tran_param['transaction_code'] = $restr_code;
							$tran_param['employee_id'] = $employee_id;
							$tran_param['transaction_amount'] = $restr_amount;
							$tran_param['source'] = 'm_loan_payment';
							$tran_param['reference'] = $restr_no . ',' . $restr_code . ',' . $tran_date . ',' . $employee_id;
							$tran_param['remarks'] = "loan_no,transaction_code,payment_date,payor_id";
							$tran_param['status_flag'] = $this->constants->table_status['PROCESSED'];
							$tran_param['created_by'] = $user;
							$tran_param['modified_by'] = $user;
							
							$this->Ttransaction_model->populate($tran_param);
							$result = $this->Ttransaction_model->insert();
							if ($result['error_code'] == '1'){
								throw new Exception('');
							}	
						}
		    			
		    			//promote from t to m
		    			$row['status_flag'] = '2';
		    			$this->Mloan_model->populate($row);
		    			$result = $this->Mloan_model->insert();
		    			if ($result['error_code'] == '1'){
		    				throw new Exception('');
		    			}
		    			
		    			$result = $this->Loancodeheader_model->get(array('loan_code'=>$tran_code),null);
		    			$rloan = $result['list'][0];
		    			//insert to m_loan_payment
		    			$loan_param['loan_no'] = $loan_no;
			    		$loan_param['payment_date'] = $tran_date;
			    		$loan_param['transaction_code'] = $rloan['payment_code'];
			    		$loan_param['amount'] = 0;
			    		$loan_param['interest_amount'] = 0;
			    		$loan_param['payor_id'] = $employee_id;
			    		$loan_param['source'] = 'B';
			    		$loan_param['remarks'] = '';
			    		$loan_param['balance'] = $tran_amount;
			    		$loan_param['status_flag'] = $this->constants->table_status['PROCESSED'];
			    		$loan_param['created_by'] = $user;
			    		$loan_param['modified_by'] = $user;
			    		
			    		$this->Mloanpayment_model->populate($loan_param);
			    		$result = $this->Mloanpayment_model->insert();
			    		if ($result['error_code'] == '1'){
			    			throw new Exception('');
			    		}
			    		
			    		$term = $row['term'];
			    		$amort_date = strtotime($row['amortization_startdate']);
			    		$loanamorts_param = array();
			    		$interest_bal = $row['employee_interest_total'];
			    		$cointerest_bal = $row['company_interest_total'];
			    		$principal_bal = $row['principal_balance'];
			    		
			    		for ($i=0; $i<$term; $i++){
			    			$interest_bal -= $row['employee_interest_amortization'];
			    			$cointerest_bal -= $row['company_interest_amort'];
			    			$principal_bal -= $row['employee_principal_amort'];
			    			
			    			$loanamorts_param['loan_no'] = $loan_no;
			    			$loanamorts_param['period'] = date('Ymd',strtotime("+{$i} month", $amort_date));
			    			$loanamorts_param['employee_principal_amort'] = $row['employee_principal_amort'];
			    			$loanamorts_param['employee_interest_amortization'] = $row['employee_interest_amortization'];
			    			$loanamorts_param['principal_balance'] = $principal_bal;
			    			$loanamorts_param['interest_balance'] = $interest_bal;
			    			$loanamorts_param['employee_company_amortization'] = $row['company_interest_amort'];
			    			$loanamorts_param['company_balance'] = $cointerest_bal;
			    			$loanamorts_param['created_by'] = $user;
			    			$loanamorts_param['modified_by'] = $user;
			    			
			    			$this->Tloanamort_model->populate($loanamorts_param);
			    			$this->Tloanamort_model->insert();
			    			if ($result['error_code'] == '1'){
			    				throw new Exception('');
			    			}
			    		}
						/*20101001 rhye added this to prevent doubling the required amount.
						This will be deleted/cleaned anyway in batch deletion below.						
						*/ 
						$this->Tloan_model->populate(array("loan_no"=> $loan_no, "status_flag"=>"0"));
						$this->Tloan_model->update();			
					}	
					
					//promote from t to m
	    			$result = $this->Mloancharges_model->batchInsert();	
	    			if ($result['error_code'] == '1'){
	    				throw new Exception('');
	    			}
	    			$result = $this->Mloanguarantor_model->batchInsert();
	    			if ($result['error_code'] == '1'){
	    				throw new Exception('');
	    			}
	    			
	    			//still have to do from models
	    			
	    			//clean
					$this->Tloan_model->populate(array());
	    			$result = $this->Tloan_model->delete(array('status_flag <='=>'1'));
	    			if ($result['error_code'] == '1'){
	    				throw new Exception('');
	    			}
	    			$result = $this->Loancharges_model->delete(array('status_flag <='=>'1'));
	    			if ($result['error_code'] == '1'){
	    				throw new Exception('');
	    			}
	    			$result = $this->Loanguarantor_model->delete(array('status_flag <='=>'1'));
	    			if ($result['error_code'] == '1'){
	    				throw new Exception('');
	    			}
	
					//update peca tran no
					$result = $this->Parameter_model->updateValue('PECATRANNO', $tran_no, $user);
					if ($result['error_code'] == '1'){
						throw new Exception('');
					}
	    			
//	    			//transaction - commit or rollback
//					$this->db->trans_complete();
					//appended messages like pd
					if($this->db->trans_status() === TRUE){
						$this->db->trans_commit();
						$resp_message .= "{$trans_group} {$trans_group_desc} successfully processed.</br>";
			        } else{
			        	$this->db->trans_rollback();
						$this->db->_trans_status = TRUE;
			        	++$error;
			        	$resp_message .= "{$trans_group} {$trans_group_desc} processing failed.</br>";   	
			//		  echo '{"success":false,"msg":"'.$result['error_message'].'"}' . $result['query'];
					}	
				} catch(Exception $e){
					$this->db->trans_rollback();
					$this->db->_trans_status = TRUE;
					++$error;
					 if ($e->getMessage() != ''){
					 	$resp_message .= "{$e->getMessage()}</br>";
					 }
					 log_message('debug', $this->db->last_query());
					 $resp_message .= "{$trans_group} {$trans_group_desc} processing failed.</br>";
				}
				
				//if time is beyond 2 minutes, referesh lock manager
				$this->Lockmanager_model->setValue('key', date('Ymdhis'));
				$this->Lockmanager_model->update(array('function_id'=>$this->constants->batch_lock));
						
			}
			
			//LP
			//note: ask ernie for the amla on loan payment if amount refered included the interest
			if ($group == 'LP'){
				try{
					//transaction - begin
					$this->db->trans_begin();
					$tran_no = $this->Parameter_model->retrieveValue('PECATRANNO');
					
					$trans_group_desc = 'Loan Payment';				
					$result = $this->Tloanpayment_model->get_list(array('status_flag'=>'1'),null,null,null,null);
					// $result = $this->Tloanpayment_model->get_list_with_lock("loan_no, transaction_code, payment_date, amount, interest_amount, payor_id"
																			// ,"status_flag = 1");
					$lp_trans = $result['list'];
					
					foreach ($lp_trans as $row){
						$loan_no = $row['loan_no'];
						$tran_code = $row['transaction_code'];
						$tran_date = $row['payment_date'];
						$tran_amount = $row['amount'] + $row['interest_amount'];
						$employee_id = $row['payor_id'];
						//insert to ttransaction table
						$tran_param['transaction_no'] = ++$tran_no;
						$tran_param['transaction_date'] = $date;
						$tran_param['transaction_code'] = $tran_code;
						$tran_param['employee_id'] = $employee_id;
						$tran_param['transaction_amount'] = $tran_amount;
						$tran_param['source'] = 'm_loan_payment';
						$tran_param['reference'] = $loan_no . ',' . $tran_code . ',' . $tran_date . ',' . $employee_id;
						$tran_param['remarks'] = "loan_no,transaction_code,payment_date,payor_id";
						$tran_param['status_flag'] = $this->constants->table_status['PROCESSED'];
						$tran_param['created_by'] = $user;
						$tran_param['modified_by'] = $user;
						
						$this->Ttransaction_model->populate($tran_param);
						$result = $this->Ttransaction_model->insert();
						if ($result['error_code'] == '1'){
							throw new Exception('');
						}	

						//amla
		    			if ($tran_amount >= $cover_limit){
		    				$data = $this->Amlamap_model->get(array('transaction_code'=>$tran_code));
							$data = $this->Transactioncode_model->get(array('transaction_code'=>$tran_code), "amla_code");
		    				$amla_code = count($data['list']) > 0 ? $data['list'][0]['amla_code'] : '';
		    				$result = $this->Tcovered_model->saveAmla($tran_no, $tran_date, $tran_code, $tran_amount, $employee_id, $amla_code, $user);
		    				if ($result['error_code'] == '1'){
		    					throw new Exception('');
		    				}
		    			}
						
						//charges
						$result = $this->Tloanpaymentdetail_model->get_list(array('status_flag'=>'1'
																				, 'loan_no'=>$loan_no
																				, 'payment_date'=>$tran_date, 'payor_id'=>$employee_id),null,null,null,null);
						//$result = $this->Tloanpaymentdetail_model->get_list_with_lock("transaction_code, amount", "status_flag = 1");
						$lp_trans_detail = $result['list'];
						foreach ($lp_trans_detail as $row2){
							$tran_param['transaction_no'] = ++$tran_no;
							$tran_param['transaction_code'] = $row2['transaction_code'];
							$tran_param['transaction_amount'] = $row2['amount'];
							$tran_param['source'] = 'm_loan_payment_detail';
							$tran_param['reference'] = $loan_no . ',' . $row2['transaction_code'] . ',' . $tran_date . ',' . $employee_id;
							$tran_param['remarks'] = "loan_no,transaction_code,payment_date,payor_id";
							
							$this->Ttransaction_model->populate($tran_param);
							$result = $this->Ttransaction_model->insert();
							if ($result['error_code'] == '1'){
								throw new Exception('');
							}
						}
					}	
					
					//promote from t to m
					$result = $this->Mloanpayment_model->batchInsert();	
					if ($result['error_code'] == '1'){
	    				throw new Exception('');
	    			}
					$result = $this->Mloanpaymentdetail_model->batchInsert();
					if ($result['error_code'] == '1'){
	    				throw new Exception('');
	    			}
					
					//clean
					$result = $this->Tloanpayment_model->delete(array('status_flag <='=>'1'));
					if ($result['error_code'] == '1'){
	    				throw new Exception('');
	    			}
					$result = $this->Tloanpaymentdetail_model->delete(array('status_flag <='=>'1'));
					
					//update peca tran no
					$result = $this->Parameter_model->updateValue('PECATRANNO', $tran_no, $user);
					if ($result['error_code'] == '1'){
	    				throw new Exception('');
	    			}
					
					//transaction - commit or rollback
					//$this->db->trans_complete();
					//appended messages like pd
					if($this->db->trans_status() === TRUE){
						$this->db->trans_commit();
						$resp_message .= "{$trans_group} {$trans_group_desc} successfully processed.</br>";
					} else{
						$this->db->trans_rollback();
						$this->db->_trans_status = TRUE;
						++$error;
						$resp_message .= "{$trans_group} {$trans_group_desc} processing failed.</br>";	
			//		  echo '{"success":false,"msg":"'.$result['error_message'].'"}' . $result['query'];
					}	
				} catch(Exception $e){
					$this->db->trans_rollback();
					$this->db->_trans_status = TRUE;
					++$error;
					 if ($e->getMessage() != ''){
					 	$resp_message .= "{$e->getMessage()}</br>";
					 }
					 $resp_message .= "{$trans_group} {$trans_group_desc} processing failed.</br>";
				}
				
				//if time is beyond 2 minutes, referesh lock manager
				$this->Lockmanager_model->setValue('key', date('Ymdhis'));
				$this->Lockmanager_model->update(array('function_id'=>$this->constants->batch_lock));
			}
			
			//IN
			if ($group == 'IN'){
			
				try{
					//transaction - begin
					$this->db->trans_begin();
					$tran_no = $this->Parameter_model->retrieveValue('PECATRANNO');
					$journal_no = $this->Parameter_model->retrieveValue('JOURNALNO');
					
					$trans_group_desc = 'New Investment';				
					$data = $this->Tinvestmentheader_model->retrieveNewInvestmentTransactions();
					$investments_array = $data['list'];
					
					foreach ($investments_array as $row){
						$journal_no = $this->saveInvestment($row, $date, $acctg_period, $journal_no, $user);
					}
					
	//				//promote from t to m
	//    			$result = $this->Minvestmentheader_model->batchInsert();
					
					//clean
					$result = $this->Tinvestmentheader_model->delete(array('status_flag'=>'1'));
					if ($result['error_code'] == '1'){
	    				throw new Exception('');
	    			}
					
					//update journal no
					$result = $this->Parameter_model->updateValue('JOURNALNO', $journal_no, $user);
					if ($result['error_code'] == '1'){
	    				throw new Exception('');
	    			}
					
					//transaction - commit or rollback
					//$this->db->trans_complete();
					//appended messages like pd
					if($this->db->trans_status() === TRUE){
						$this->db->trans_commit();
						$resp_message .= "{$trans_group} {$trans_group_desc} successfully processed.</br>";
					} else{
						$this->db->trans_rollback();
						$this->db->_trans_status = TRUE;
						++$error;
						$resp_message .= "{$trans_group} {$trans_group_desc} processing failed.</br>";  	
			//		  echo '{"success":false,"msg":"'.$result['error_message'].'"}' . $result['query'];
					}	
				} catch (Exception $e){
					$this->db->trans_rollback();
					$this->db->_trans_status = TRUE;
					++$error;
					 if ($e->getMessage() != ''){
					 	$resp_message .= "{$e->getMessage()}</br>";
					 }
					 $resp_message .= "{$trans_group} {$trans_group_desc} processing failed.</br>";
				}
				
				//if time is beyond 2 minutes, referesh lock manager
				$this->Lockmanager_model->setValue('key', date('Ymdhis'));
				$this->Lockmanager_model->update(array('function_id'=>$this->constants->batch_lock));
			}
			
			//II
			if ($group == 'II'){
				try{
					//transaction - begin
					$this->db->trans_begin();
					$journal_no = $this->Parameter_model->retrieveValue('JOURNALNO');
					
					$trans_group_desc = 'Investment Maturity';
					$data = $this->Minvestmentheader_model->retrieveMatureInvestmentTransactions($date);
					$investments_array = $data['list'];
					
					foreach ($investments_array as $row){
						//clear minvstmentheader_model here
						$this->Minvestmentheader_model->populate(array());
						$this->Minvestmentdetail_model->populate(array());
					
						$inv_no = $row['investment_no'];
						$tran_code = $row['maturity_code'];
						$maturity_date = $row['maturity_date'];
						$gl_code = $row['gl_code'];
						$action = $row['action_code'];
						$with_or = $row['with_or'];
						//delete investmentdetail > acctgperiod
						$result = $this->Minvestmentdetail_model->delete(array('investment_no'=>$inv_no, 'accounting_period >'=>$acctg_period));
						if ($result['error_code'] == '1'){
							throw new Exception('');
						}
						// $this->Minvestmentdetail_model->setValue('status_flag', '0');
						// $result = $this->Minvestmentdetail_model->update(array('investment_no'=>$inv_no, 'accounting_period >'=>$acctg_period));
						// if ($result['error_code'] == '1'){
							// throw new Exception('');
						// }
						
						$result = $this->Mcjournaldetail_model->batchInsert($acctg_period, $user, $inv_no);
						if ($result['error_code'] == '1'){
							throw new Exception('');
						}
						$result = $this->Tjournaldetail_model->deleteJournalDetailII($acctg_period, $inv_no);
						if ($result['error_code'] == '1'){
							throw new Exception('');
						}
						$result = $this->Mcjournalheader_model->batchInsert($acctg_period, $user, $inv_no);
						if ($result['error_code'] == '1'){
							throw new Exception('');
						}
						$result = $this->Tjournalheader_model->delete(array('reference'=>$inv_no, 'accounting_period >'=>$acctg_period));
						if ($result['error_code'] == '1'){
							throw new Exception('');
						}
						
						$data = $this->Minvestmentdetail_model->get(array('investment_no'=>$inv_no), 'SUM(amount) AS amount');
						$inv_total = $data['list'][0]['amount'];
						$this->Minvestmentheader_model->setValue('interest_amount', $inv_total);
						$this->Minvestmentheader_model->setValue('processed', '1');
						$result = $this->Minvestmentheader_model->update(array('investment_no'=>$inv_no));
						if ($result['error_code'] == '1'){
							throw new Exception($result['error_message']);
						}
						
						$glheader_array = $this->Glentryheader_model->retrieveGLEntry($gl_code);
						if (count($glheader_array) < 1){
							log_message('debug', 'xx-xx');
							throw new Exception("No GL code for transaction " . $tran_code . ".");
						}
						
						//save journal header
						$journal_no_pad = str_pad(++$journal_no, 10, '0', STR_PAD_LEFT);
						$jheader_param['journal_no'] = $journal_no_pad;
						$jheader_param['accounting_period'] = $acctg_period;
						$jheader_param['transaction_code'] = $tran_code;
						$jheader_param['transaction_date'] = $date;
						$jheader_param['particulars'] = $glheader_array[0]['particulars'];
						$jheader_param['reference'] = $inv_no;//based on transaction no
						$jheader_param['source'] = 'S';
						$jheader_param['status_flag'] = '3';
						$jheader_param['created_by'] = $user;
						$jheader_param['modified_by'] = $user;
						
						$this->Tjournalheader_model->populate($jheader_param);
						$result = $this->Tjournalheader_model->insert();
						if ($result['error_code'] == '1'){
							throw new Exception($result['error_message']);
						}
						
						//save journal detail
						foreach ($glheader_array as $row3){
							$gl_desc = $row3['gl_description'];
							$gl_particulars = $row3['particulars'];
							$account_no = $row3['account_no'];
							$account_name = $row3['account_name'];
							$debit_credit = $row3['debit_credit'];
							$field_name = $row3['field_name'];
							$amount = $this->Ttransaction_model->retrieveFieldAmount('m_investment_header', $field_name, 'investment_no', $inv_no, $tran_code);
							
							$jdetail_param['journal_no'] = $journal_no_pad;
							$jdetail_param['account_no'] = $account_no;
							$jdetail_param['debit_credit'] = $debit_credit;
							$jdetail_param['amount'] = $amount;
							$jdetail_param['status_flag'] = '3';
							$jdetail_param['created_by'] = $user;
							$jdetail_param['modified_by'] = $user;
							
							$this->Tjournaldetail_model->populate($jdetail_param);
							$result = $this->Tjournaldetail_model->insert();    				
							if ($result['error_code'] == '1'){
								throw new Exception($result['error_message']);
							}
						}
						
						//for rollover
						$investment_p = array();
						if ($action == 'P'){
							$invnum = $this->Parameter_model->retrieveValue('INVNO');
							log_message('debug', $invnum . ' zzzzzzzzzzzzzzzz');
							$investment_p['supplier_id'] = $row['supplier_id'];
							$investment_p['interest_amount'] = $row['rollover_interest_amount'];
							$investment_p['interest_rate'] = $row['rollover_interest_rate'];
							$investment_p['investment_amount'] = $row['investment_amount'];
							$investment_p['investment_no'] = str_pad(++$invnum, 10, '0', STR_PAD_LEFT);
							$investment_p['transaction_code'] = $row['transaction_code'];
							//get gl code of transaction code
							$data = $this->Transactioncode_model->get(array('transaction_code'=>$tran_code), 'gl_code');
							$investment_p['gl_code'] = $data['list'][0]['gl_code'];
	//	    				echo $data['list'][0]['gl_code'] . 'zzzzzzzzzz' . $tran_code;
							$investment_p['maturity_date'] = $row['rollover_maturity_date'];
							$investment_p['placement_date'] = $row['rollover_placement_date'];
							$investment_p['placement_days'] = $row['rollover_placement_days'];
							$investment_p['remarks'] = '';
							$investment_p['created_by'] = $user;
							$journal_no = $this->saveInvestment($investment_p, $date, $acctg_period, $journal_no, $user, false);
							$result = $this->Parameter_model->updateValue('INVNO', $invnum, $user);
							if ($result['error_code'] == '1'){
								throw new Exception($result['error_message']);
							}
						}
						
						//20100617 rhye added updating OR 
						//need to retrieve and update lastorno inside the loop since we don't know if updateOR is success
						// $tran_detail = Transactioncode_model->get(array('transaction_code'=>$tran_code), 'with_or');
						if ($with_or == 'Y'){
							$or_no = $this->Parameter_model->retrieveValue('LASTORNO');
							$or_date = $date;
							
							$result = $this->Ttransaction_model->updateOR('m_investment_header', 'investment_no', $inv_no, ++$or_no, $or_date);
							if ($result){
								$result = $this->Parameter_model->updateValue('LASTORNO', $or_no, $user);
							}
							
						}
						
	//	    			$air_code = $this->Parameter_model->retrieveValue($row['transaction_code'].'AIR');
	//	    			$result = $this->Tjournaldetail_model->deleteJournalDetailIM($acctg_period, $maturity_date);
	//	    			$result = $this->Tjournalheader_model->delete(array('investment_no'=>$inv_no, 'transaction_code'=>$air_code,'transaction_date >'=>$maturity_date));
						
					}		
					
					//update peca tran no
					$result = $this->Parameter_model->updateValue('JOURNALNO', $journal_no, $user);
					if ($result['error_code'] == '1'){
						throw new Exception($result['error_message']);
					}
					
					//transaction - commit or rollback
					//$this->db->trans_complete();
					//appended messages like pd
					if($this->db->trans_status() === TRUE){
						$this->db->trans_commit();
						$resp_message .= "{$trans_group} {$trans_group_desc} successfully processed.</br>";
					} else{
						$this->db->trans_rollback();
						$this->db->_trans_status = TRUE;
						++$error;
						$resp_message .= "{$trans_group} {$trans_group_desc} processing failed.</br>";
			//		  echo '{"success":false,"msg":"'.$result['error_message'].'"}' . $result['query'];
					}	
				} catch (Exception $e){
					$this->db->trans_rollback();
					$this->db->_trans_status = TRUE;
					++$error;
					 if ($e->getMessage() != ''){
					 	$resp_message .= "{$e->getMessage()}</br>";
					 }
					  log_message('debug', "zzz" . $this->db->last_query());
					 log_message('debug', "zzz" . $this->db->_error_message());
					$resp_message .= "{$trans_group} {$trans_group_desc} processing failed.</br>";
				}

				//if time is beyond 2 minutes, referesh lock manager
				$this->Lockmanager_model->setValue('key', date('Ymdhis'));
				$this->Lockmanager_model->update(array('function_id'=>$this->constants->batch_lock));				
			}
		}
		//BMB
		if ($bmb == 1){
			try{
				//transaction - begin
				$this->db->trans_begin();

				$tran_no = $this->Parameter_model->retrieveValue('PECATRANNO');
				$min_bal = $this->Parameter_model->retrieveValue('CCMINBAL');

				$trans_group_desc = 'BMB for the month';
				//get charge amount BMBC
				$bmbc_charge = $this->Transactioncharges_model->retrieveChargeFormula('BMBC','BMBC');
				$bmb_employees = $this->Capitalcontribution_model->retrieveBMBEmployee($date, $min_bal);
				
				//delete existing bmbc here in ttransaction
				// $result = $this->Ttransaction_model->deleteTransactionByCode($date, 'BMBC');
				// if ($result['error_code'] == '1'){
					// throw new Exception('');
				// }
					
				foreach ($bmb_employees as $row){
					$employee_id = $row['employee_id'];
					//insert to ttransaction table
					$tran_param['transaction_no'] = ++$tran_no;
					$tran_param['transaction_date'] = $date;
					$tran_param['transaction_code'] = 'BMBC';
					$tran_param['employee_id'] = $employee_id;
					$tran_param['transaction_amount'] = $bmbc_charge;
					$tran_param['source'] = 't_transaction';
					$tran_param['reference'] = $tran_no;
					$tran_param['remarks'] = 'transaction_no';
					$tran_param['status_flag'] = $this->constants->table_status['PROCESSED'];
					$tran_param['created_by'] = $user;
					$tran_param['modified_by'] = $user;

					$this->Ttransaction_model->populate($tran_param);
					$result = $this->Ttransaction_model->insert();
					if ($result['error_code'] == '1'){
						throw new Exception('');
					}
				}
				 
				//update peca tran no
				$result = $this->Parameter_model->updateValue('PECATRANNO', $tran_no, $user);
				if ($result['error_code'] == '1'){
					throw new Exception('');
				}
				 
				//transaction - commit or rollback
				//$this->db->trans_complete();
				//appended messages like pd
				if($this->db->trans_status() === TRUE){
					$this->db->trans_commit();
					$resp_message .= "{$trans_group_desc} successfully processed.</br>";
				} else{
					$this->db->trans_rollback();
					$this->db->_trans_status = TRUE;
					++$error;
					$resp_message .= "{$trans_group_desc} processing failed.</br>";        	
		//		  echo '{"success":false,"msg":"'.$result['error_message'].'"}' . $result['query'];
				}	
			} catch (Exception $e){
				$this->db->trans_rollback();
				$this->db->_trans_status = TRUE;
				++$error;
				 if ($e->getMessage() != ''){
					$resp_message .= "{$e->getMessage()}</br>";
				 }
				 $resp_message .= "{$trans_group_desc} processing failed.</br>";
			}
			
			
			//if time is beyond 2 minutes, referesh lock manager
			$this->Lockmanager_model->setValue('key', date('Ymdhis'));
			$this->Lockmanager_model->update(array('function_id'=>$this->constants->batch_lock));
		}
			
		//NPML
		if ($npml == 1){
			try{
				//transaction - begin
				$this->db->trans_begin();
				$tran_no = $this->Parameter_model->retrieveValue('PECATRANNO');
				$min_bal = $this->Parameter_model->retrieveValue('CCMINBAL');

				$trans_group_desc = 'NPML for the month';
				//get charge amount NPML
				$npml_charge = $this->Transactioncharges_model->retrieveChargeFormula('NPML','NPML');
				$npml_employees = $this->Mloan_model->retrieveNMPLEmployee2($date);
				
				//delete existing npml here in ttransaction
				// $result = $this->Ttransaction_model->deleteTransactionByCode($date, 'NPML');
				// if ($result['error_code'] == '1'){
					// throw new Exception('');
				// }

				foreach ($npml_employees as $row){
					$employee_id = $row['employee_id'];
					$capcon_bal = $this->Capitalcontribution_model->retrieveCapConBalance($employee_id, $acctg_period);
					//insert to ttransaction table
					$tran_param['transaction_no'] = ++$tran_no;
					$tran_param['transaction_date'] = $date;
					$tran_param['transaction_code'] = 'NPML';
					$tran_param['employee_id'] = $employee_id;
					$tran_param['transaction_amount'] = $npml_charge;
					$tran_param['source'] = 't_transaction';
					$tran_param['reference'] = $tran_no;
					$tran_param['remarks'] = 'transaction_no';
					$tran_param['status_flag'] = $this->constants->table_status['PROCESSED'];
					$tran_param['created_by'] = $user;
					$tran_param['modified_by'] = $user;

					$this->Ttransaction_model->populate($tran_param);
					$result = $this->Ttransaction_model->insert();
					if ($result['error_code'] == '1'){
						throw new Exception('');
					}

					//insert one time pd incase capcon bal < minbal
					if (($capcon_bal - $npml_charge) < $min_bal){
						$tran_param['transaction_no'] = ++$tran_no;
						$tran_param['transaction_date'] = $date;
						$tran_param['transaction_code'] = 'PDED';
						$tran_param['employee_id'] = $employee_id;
						$tran_param['transaction_amount'] = $npml_charge;
						$tran_param['source'] = 't_transaction';
						$tran_param['reference'] = $tran_no;
						$tran_param['remarks'] = 'transaction_no';
						$tran_param['status_flag'] = $this->constants->table_status['PROCESSED'];
						$tran_param['created_by'] = $user;
						$tran_param['modified_by'] = $user;
						 
						$this->Ttransaction_model->populate($tran_param);
						$result = $this->Ttransaction_model->insert();
						if ($result['error_code'] == '1'){
							throw new Exception('');
						}
					}
					
					//update suspension date for that employee (use next month first day)
					$next_acctg = date('Ymd',strtotime("+1 month", strtotime($acctg_period)));
					$this->Member_model->setValue('suspended_date', $next_acctg);
					$this->Member_model->setValue('modified_by', $user);
					$this->Member_model->update(array('employee_id'=>$employee_id));
				}
				 
				//update peca tran no
				$result = $this->Parameter_model->updateValue('PECATRANNO', $tran_no, $user);
				if ($result['error_code'] == '1'){
					throw new Exception('');
				}
				 
				//transaction - commit or rollback
				//$this->db->trans_complete();
				//appended messages like pd
				if($this->db->trans_status() === TRUE){
					$this->db->trans_commit();
					$resp_message .= "{$trans_group_desc} successfully processed.</br>";
				} else{
					$this->db->trans_rollback();
					$this->db->_trans_status = TRUE;
					++$error;
					$resp_message .= "{$trans_group_desc} processing failed.</br>";        	
		//		  echo '{"success":false,"msg":"'.$result['error_message'].'"}' . $result['query'];
				}	
			} catch (Exception $e){
				$this->db->trans_rollback();
				$this->db->_trans_status = TRUE;
				++$error;
				 if ($e->getMessage() != ''){
					$resp_message .= "{$e->getMessage()}</br>";
				 }
				 $resp_message .= "{$trans_group_desc} processing failed.</br>";
			}
		}
		
		$this->Lockmanager_model->release($this->constants->batch_lock);
		if ($error > 0){
			echo '{"success":false,"msg":"'.$resp_message.'"}';
		} else{
			$resp_message .= "Do you want to generate the control totals?</br>";
			echo '{"success":true,"msg":"'.$resp_message.'"}';
		}
	}
	
	function getWithrawableAmount($employee_id, $acctg_period){
		$min_bal = $this->Parameter_model->retrieveValue('CCMINBAL');
		//get 1/3 of loans of employee
		$capcon_req = $this->Mloan_model->retrieveLoanCCBalance($employee_id);
		//get employee's capcon balance
		$capcon_bal = $this->Capitalcontribution_model->retrieveCapConBalance($employee_id, $acctg_period);		
		$req = ($capcon_req >= $min_bal) ? $capcon_req : $min_bal;
		
		log_message('debug', '$req = ' . $capcon_req);
		log_message('debug', '$capcon_bal = ' . $capcon_bal);
		
		return $capcon_bal - $req;
	}
	
	function isAllowedToLoan($employee_id, $acctg_period){
		$min_bal = $this->Parameter_model->retrieveValue('CCMINBAL');
		//get 1/3 of all restructured amounts in t_loan
		$restructured_one_third = $this->Mloan_model->retrieveRestructuredOneThird($employee_id);
		//get 1/3 of loans of employee + 1/3 of current principal
		$capcon_req = $this->Mloan_model->retrieveLoanCCBalance($employee_id) - $restructured_one_third;
		//get employee's capcon balance
		$capcon_bal = $this->Capitalcontribution_model->retrieveCapConBalance($employee_id, $acctg_period);
		
		log_message('debug', '$capcon_req = ' . $capcon_req);
		log_message('debug', '$capcon_bal = ' . $capcon_bal);
		
		if ($capcon_bal < $min_bal){
			return false;
		}
		
		return $capcon_bal >= $capcon_req;
	}
	
	function dateDiff($start, $end){
		$j_start = gregoriantojd(substr($start, 4, 2), substr($start, 6, 2), substr($start, 0, 4));
		$j_end = gregoriantojd(substr($end, 4, 2), substr($end, 6, 2), substr($end, 0, 4));
		
		return $j_end - $j_start;
	}
	
	function saveInvestment($row, $date, $acctg_period, $journal_no, $user, $new = true){
		$tran_no = $row['investment_no'];
		$tran_code = $row['transaction_code'];
		$tran_date = $date;
		$tran_amount = $row['investment_amount'];
		$interest = $row['interest_rate'];
		$gl_code = $row['gl_code'];
		$placement_date = $row['placement_date'];
		$maturity_date = $row['maturity_date'];
		
		if ($new){
			//retrieve gl entries for the transaction		
			$glheader_array = $this->Glentryheader_model->retrieveGLEntry($gl_code);
			if (count($glheader_array) < 1){
				log_message('debug', 'zzzzzzz' . $gl_code);
				log_message('debug', 'xx-xx1');
				throw new Exception("No GL code for transaction " . $tran_code . ".");
			}
			
			//save journal header
			$journal_no_pad = str_pad(++$journal_no, 10, '0', STR_PAD_LEFT);
			$jheader_param['journal_no'] = $journal_no_pad;
			$jheader_param['accounting_period'] = $acctg_period;
			$jheader_param['transaction_code'] = $tran_code;
			$jheader_param['transaction_date'] = $tran_date;
			$jheader_param['particulars'] = $glheader_array[0]['particulars'] . ' ' . date('m/d/Y',strtotime($placement_date));
			$jheader_param['reference'] = $tran_no;//based on transaction no
			$jheader_param['source'] = 'S';
			$jheader_param['status_flag'] = '3';
			$jheader_param['created_by'] = $user;
			$jheader_param['modified_by'] = $user;
			
			$this->Tjournalheader_model->populate($jheader_param);
			$result = $this->Tjournalheader_model->insert();
			if ($result['error_code'] == '1'){
				throw new Exception($result['error_message']);
			}
			
			//save journal detail
			foreach ($glheader_array as $row3){
				$gl_desc = $row3['gl_description'];
				$gl_particulars = $row3['particulars'];
				$account_no = $row3['account_no'];
				$account_name = $row3['account_name'];
				$debit_credit = $row3['debit_credit'];
				$field_name = $row3['field_name'];
				//$amount = $this->Ttransaction_model->retrieveFieldAmount('t_investment', 'investment_amount', 'investment_no', $tran_no);
				
				$jdetail_param['journal_no'] = $journal_no_pad;
				$jdetail_param['account_no'] = $account_no;
				$jdetail_param['debit_credit'] = $debit_credit;
				$jdetail_param['amount'] = $tran_amount;
				$jdetail_param['status_flag'] = '3';
				$jdetail_param['created_by'] = $user;
				$jdetail_param['modified_by'] = $user;
				
				$this->Tjournaldetail_model->populate($jdetail_param);
				$result = $this->Tjournaldetail_model->insert();  
				if ($result['error_code'] == '1'){
					throw new Exception($result['error_message']);
				}
			}			
		}
    	
    	$air_code = $this->Parameter_model->retrieveValue($tran_code.'AIR');
    	
    	//solve: from placement date to maturity date
    	//have to do loop
    	//$year_covered = (substr($maturity_date, 0, 4) - substr($placement_date, 0, 4))* 12;
		//$month_covered = substr($maturity_date, 4, 2) - substr($placement_date, 4, 2) + 1;
		//$covered = $year_covered + $month_covered;
		
		$bplacement_date = $placement_date;
		$glheader_array = $this->Glentryheader_model->retrieveGLEntry($air_code);
		if (count($glheader_array) < 1){
			log_message('debug', 'xx-xx2');
			throw new Exception("No GL code for transaction " . $tran_code . ".");
		}
		$maturity_date = date('Ymd',strtotime($maturity_date));
		$bplacement_date = date('Ymd',strtotime($bplacement_date));
		
		$check_next_months = 0;
		
		while ($bplacement_date <= $maturity_date){
			//get last day of the month
			$month = substr($bplacement_date, 4, 2);
			$year = substr($bplacement_date, 0, 4);
			$cur_acctg = $year . $month . '01';
			$next_acctg = date('Ymd',strtotime('+1 month',strtotime($cur_acctg)));
			$last_day = date('Ymd',strtotime('-1 day',strtotime($next_acctg)));
			
			//added 20100630
			$tran_date_new = '';
			
			if ($maturity_date >= $last_day){
				$diff = substr($last_day, 6, 2) - substr($bplacement_date, 6, 2);
				$tran_date_new = $last_day;
			}else{
				$diff = substr($maturity_date, 6, 2) - substr($bplacement_date, 6, 2);
				$tran_date_new = $maturity_date;
			}
			
			//maturity is more than that month
			if ($check_next_months > 0){
				++$diff;
			}
			
			$temp_amount = ($tran_amount * $interest * $diff) / 36000;
			
			//insert investment detail
			$investmentdtl_param['investment_no'] = $tran_no;
			$investmentdtl_param['accounting_period'] = $cur_acctg;	
			$investmentdtl_param['amount'] = $temp_amount;	
			$investmentdtl_param['status_flag'] = '2';
			$investmentdtl_param['created_by'] = $user;
			$investmentdtl_param['modified_by'] = $user;	
			$this->Minvestmentdetail_model->populate($investmentdtl_param);
			$result = $this->Minvestmentdetail_model->insert();
			if ($result['error_code'] == '1'){
				throw new Exception($result['error_message']);
			}
			
			//save journal header
			$journal_no_pad = str_pad(++$journal_no, 10, '0', STR_PAD_LEFT);
			$jheader_param['journal_no'] = $journal_no_pad;
			$jheader_param['accounting_period'] = $cur_acctg;
			$jheader_param['transaction_code'] = $air_code;
			// $jheader_param['transaction_date'] = $tran_date;
			// still erroneous
			$jheader_param['transaction_date'] = $tran_date_new;
			$jheader_param['particulars'] = $glheader_array[0]['particulars'] . ' ' . date('m/d/Y',strtotime($bplacement_date));
			$jheader_param['reference'] = $tran_no;//based on transaction no
			$jheader_param['source'] = 'S';
			$jheader_param['created_by'] = $user;
			$jheader_param['modified_by'] = $user;
			
			$this->Tjournalheader_model->populate($jheader_param);
    		$result = $this->Tjournalheader_model->insert();
			if ($result['error_code'] == '1'){
				throw new Exception($result['error_message']);
			}
    		
			//save journal detail
    		foreach ($glheader_array as $row3){
    			$gl_desc = $row3['gl_description'];
    			$gl_particulars = $row3['particulars'];
    			$account_no = $row3['account_no'];
    			$account_name = $row3['account_name'];
    			$debit_credit = $row3['debit_credit'];
    			$field_name = $row3['field_name'];
    			//$amount = $this->Ttransaction_model->retrieveFieldAmount('t_investment', $field_name, $remarks, $reference);
    			//$amount = $this->Ttransaction_model->retrieveFieldAmount('t_investment', 'investment_amount', 'investment_no', $tran_no);
    			
    			$jdetail_param['journal_no'] = $journal_no_pad;
    			$jdetail_param['account_no'] = $account_no;
    			$jdetail_param['debit_credit'] = $debit_credit;
    			$jdetail_param['amount'] = $temp_amount;
    			$jdetail_param['created_by'] = $user;
				$jdetail_param['modified_by'] = $user;
    			
    			$this->Tjournaldetail_model->populate($jdetail_param);
    			$result = $this->Tjournaldetail_model->insert();   
				if ($result['error_code'] == '1'){
					throw new Exception($result['error_message']);
				}
    		}						
			
			$bplacement_date = $next_acctg;
			++$check_next_months;
		}	

		//promote from t to m
    	$row['status_flag'] = '2';
    	unset($row['gl_code']);
    	$this->Minvestmentheader_model->populate($row);
		$this->Minvestmentheader_model->setValue('processed', 0);
    	$result = $this->Minvestmentheader_model->insert();
		if ($result['error_code'] == '1'){
			throw new Exception('');
		}
    	
    	return $journal_no;
	}
	
	function showCompCode($employee_id){
		$data = $this->Member_model->get(array("employee_id"=>$employee_id), array(
			"COALESCE(company_code, '') AS company_code"
			));
	
		if($data['count']==0){
			return "";
		}
		else{
			return $data['list'][0]['company_code'];
		}
	}
	
	function allowedWdwlForRetiree($employee_id){
		$acctg_period = date("Ymd", strtotime($this->parameter_model->getParam('ACCPERIOD')));
		$capcon_balance = $this->Capitalcontribution_model->retrieveCapConBalance($employee_id, $acctg_period);
		$capcon_balance = round($capcon_balance, 2);
		$interestAndRemainingTerms = $this->getInterestAndRemainingTerms($employee_id);
		
		log_message('debug', 'capcon_balance: '. $capcon_balance);
		log_message('debug', 'interestAndRemainingTerms: '. $interestAndRemainingTerms);
		return ($capcon_balance - ($interestAndRemainingTerms));
	}

	function getInterestAndRemainingTerms($employee_id){
		$remaining_terms = "(principal_balance / employee_principal_amort)"; 
		$interest = "($remaining_terms * employee_interest_amortization)";
			
		$data = $this->Mloan_model->get(array('employee_id'=>$employee_id, 'status_flag'=>'2', 'close_flag' => '0')
			,array("COALESCE(SUM(principal_balance + $interest),0) AS msum"));
	
		return (round($data['list'][0]['msum'], 2));
	}
	
	function showLoanBalance($employee_id) 
	{
		$data = $this->Mloan_model->get(array('employee_id'=>$employee_id, 'status_flag'=>'2', 'close_flag' => '0')
								,array('COALESCE(SUM(principal_balance),0) AS mloan_balance'));
		
		$total_loan_balance = $data['list'][0]['mloan_balance'];
		$total_loan_balance = round($total_loan_balance, 2);
		
		return $total_loan_balance;
	}
}
?>
