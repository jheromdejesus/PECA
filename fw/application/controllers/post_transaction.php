<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Post_transaction extends Asi_controller {

	function Post_transaction(){
		parent::Asi_controller();
		$this->load->model('Parameter_model');
		$this->load->model('Ttransaction_model');
		$this->load->model('Mtransaction_model');
		$this->load->model('Glentryheader_model');
		$this->load->model('Glentrydetail_model');
		$this->load->model('Mjournalheader_model');
		$this->load->model('Tjournalheader_model');
		$this->load->model('Mjournaldetail_model');
		$this->load->model('Tjournaldetail_model');
		$this->load->model('Amlamap_model');
		$this->load->model('Tcovered_model');
		$this->load->model('Mloan_model');
		$this->load->model('Mloanpayment_model');
		$this->load->model('Capitalcontribution_model');
		$this->load->model('Transactiongroup_model');
		$this->load->model('Transactioncode_model');
		$this->load->model('Lockmanager_model');
		$this->load->model('Loancodedetail_model');
		$this->load->model('Mloanguarantor_model');
		$this->load->model('Employee_model');
		$this->load->model('Member_model');
		$this->load->helper('url');
		$this->load->library('constants');
		//$this->load->scaffolding('t_loan');
	}
	
//	function index(){		
//		$period = $this->Parameter_model->retrieveValue('ACCPERIOD');
//		$period = date('F Y',strtotime($period));
//		$date = $this->Parameter_model->retrieveValue('CURRDATE');
//		$date = date('m/d/Y',strtotime($date));
//		
//		echo "{success:true" .
//				", data:{currdate:'{$date}', acctgperiod:'{$period}'}" .
//				"}";
//		$_REQUEST['post_trans'] = array('user_id' => 'PECA'
//										, 'data' => '["LP"]');
//		//retrieve transaction count by group - for display
////		$data = $this->Ttransaction_model->retrieveTransactionCountByGroup();
//		
////		echo json_encode(array('data' => $data));
//		
//		$this->postTransactions();
//	}

 	function index(){		
		$period = $this->Parameter_model->retrieveValue('ACCPERIOD');
		$period = date('F Y',strtotime($period));
		$date = $this->Parameter_model->retrieveValue('CURRDATE');
		$date = date('m/d/Y',strtotime($date));
		
		echo json_encode(array(
            'data' => array(array('current_period' => $period, 'posting_date' => $date))
        ));
	}
	
	function load(){
		$date = $this->Parameter_model->retrieveValue('CURRDATE');
//		retrieve transaction count by group - for display
		$data = $this->Ttransaction_model->retrieveTransactionCountByGroup($date);		
		echo json_encode(array('data' => $data));
	}
	
	function incDay(){
		$curdate = $this->Parameter_model->retrieveValue('CURRDATE');
		 //echo $curdate;
		 //check if curdate is friday
		$inc = (date('D', strtotime($curdate)) == 'Fri') ? 3 : 1;
		$tomdate = date('Ymd',strtotime("+$inc day", strtotime($curdate)));
		$result = $this->Parameter_model->updateValue('CURRDATE', $tomdate, 'peca');
	    echo date('m/d/Y',strtotime($tomdate));
    }
	
	function postTransactions(){
		//get posted transactions
		$pt_array = isset($_REQUEST['post_trans']) ? $_REQUEST['post_trans'] : array();
		$user = isset($pt_array['user_id']) ? $pt_array['user_id'] : '';
		$this->Lockmanager_model->user = $user;
		##### NRB START EDIT #####
		$posting_groups = json_decode(isset($pt_array['data']) ? stripslashes($pt_array['data']): array());
		/* $posting_groups = json_decode(isset($pt_array['data']) ? $pt_array['data']: array()); */
		##### NRB END EDIT #####
		
		$error = 0;
		$resp_message = '';
		
		$jheader_param = array();
		$jdetail_param = array();
		$glheader_array = array();
		$gldetail_array = array();
		$covered_array = array();
		
		try{
			//acquire lock for 20 minutes
			$permitted = $this->Lockmanager_model->acquire($this->constants->batch_lock, 60);
			$refresh = $this->constants->lock_refresh;
			$new_time = date('Ymdhis',strtotime("+{$refresh} minute"));
			if (!$permitted){
				$resp_message = "Server is busy.</br>Other user is doing batch process.";
				echo '{"success":false,"msg":"'.$resp_message.'"}';
				exit();
			}
			
			//get parameter values
			$date = $this->Parameter_model->retrieveValue('CURRDATE');
			$acctg_period = $this->Parameter_model->retrieveValue('ACCPERIOD');
			$cover_limit = $this->Parameter_model->retrieveValue('COVERLIMIT');
			
			/*if ($this->Parameter_model->checkTableInUse()){
				$trans_group_desc = 'System';
				$trans_group = 'Error';
				throw new Exception("Other users are accessing Batch Process.</br>Please try again later.");
			}*/
			
			$journal_no = $this->Parameter_model->retrieveValue('JOURNALNO');
//			$tran_no = $this->Parameter_model->retrieveValue('PECATRANNO');

			//delete all trans from ttransaction where status flag = 0
			$result = $this->Ttransaction_model->delete(array('status_flag'=>0));
			if ($result['error_code'] == '1'){				
				throw new Exception($result['error_message']);
			}
			
			//$transgroup_array = $this->Ttransaction_model->retrieveTransactionCountByGroup();
			foreach ($posting_groups as $trans_group){
				try{
					//transaction - begin
					$this->db->trans_begin();
					
		//			$trans_group = $row['transaction_group'];
					$result = $this->Transactiongroup_model->get(array('transaction_group'=>$trans_group), 'transaction_group_description');
					$trans_group_desc = $result['list'][0]['transaction_group_description'];
		//			$trans_count = $row['transaction_count'];
					
					//retrieve transactions by groups
					$trans_array = $this->Ttransaction_model->retrieveTransactionByGroup($trans_group, $date);
					foreach ($trans_array as $row2){
						$tran_no = $row2['transaction_no'];
						$tran_code = $row2['transaction_code'];
						$tran_date = $row2['transaction_date'];
						$tran_amount = $row2['transaction_amount'];
						$employee_id = $row2['employee_id'];
						$source = $row2['source'];
						$remarks = $row2['remarks'];
						$reference = $row2['reference'];
						$gl_code = $row2['gl_code'];
						$with_or = $row2['with_or'];
						
						//get employee info
						$emp_info = $this->Employee_model->get(array('employee_id' => $employee_id), null);
						$emp_info = $emp_info['list'][0];
						//get years of service
						$yos = floor($this->dateDiff($emp_info['hire_date'], $date)/365);
						
						//retrieve gl entries for the transaction
		//				$result = $this->Glentryheader_model->get(array('gl_code'=>$gl_code),null);
		//				$glheader_array = $result['list'][0];
						$glheader_array = $this->Glentryheader_model->retrieveGLEntry($gl_code);
						if (count($glheader_array) < 1){
							throw new Exception("No GL code for transaction " . $tran_code . ".");
						}
						
						//save journal header
						$journal_no_pad = str_pad(++$journal_no, 10, '0', STR_PAD_LEFT);
						$jheader_param['journal_no'] = $journal_no_pad;
						$jheader_param['accounting_period'] = $acctg_period;
						$jheader_param['transaction_code'] = $tran_code;
						$jheader_param['transaction_date'] = $tran_date;
						$jheader_param['particulars'] = $glheader_array[0]['particulars'];
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
		    				$amount = $this->Ttransaction_model->retrieveFieldAmount($source, $field_name, $remarks, $reference, $tran_code);
		    				
		    				$jdetail_param['journal_no'] = $journal_no_pad;
		    				$jdetail_param['account_no'] = $account_no;
		    				$jdetail_param['debit_credit'] = $debit_credit;
		    				$jdetail_param['amount'] = $amount;
							$jdetail_param['status_flag'] = '3';
		    				$jdetail_param['created_by'] = $user;
							$jdetail_param['modified_by'] = $user;
							
		    				##### NRB EDIT START #####
		    				log_message('debug','#####journal_no:'.$journal_no_pad);
		    				log_message('debug','#####account_no:'.$account_no);
		    				log_message('debug','#####debit_credit:'.$debit_credit);
		    				log_message('debug','#####amount:'.$amount);
		    				log_message('debug','#####status_flag:3');
		    				log_message('debug','#####created_by:'.$user);
		    				log_message('debug','#####modified_by:'.$user);
		    				
		    				#check if mri_fip_amount
		    				$b_insert_journal = TRUE;
		    				if(trim($field_name) == 'mri_fip_amount') {
		    					
		    					$b_insert_journal = FALSE;
			    				
			    				#get mri fip provider
			    				$m_mrifip_provider = $this->Mloan_model->retrieve_mrifip_provider($reference);
			    				if(!$m_mrifip_provider) {
				    				$b_insert_journal =  FALSE;
			    				} else {
			    					#check if account number matches provider
				    				if($account_no == $m_mrifip_provider) {
					    				$b_insert_journal = TRUE;
				    				} else {
					    				$b_insert_journal = FALSE;
				    				}
			    				}
		    				}	    				
		    				
		    				if($b_insert_journal) {
			    				$this->Tjournaldetail_model->populate($jdetail_param);
			    				$result = $this->Tjournaldetail_model->insert();   
			    				if ($result['error_code'] == '1'){
			    					throw new Exception($result['error_message']);
			    				}
		    				}
/*
		    				$this->Tjournaldetail_model->populate($jdetail_param);
		    				$result = $this->Tjournaldetail_model->insert();   
		    				if ($result['error_code'] == '1'){
		    					throw new Exception($result['error_message']);
		    				}
*/
							##### NRB EDIT END #####
		    			}
		    			
		    			//update capcon based on capcon effect
		    			$tran_new_amount = $tran_amount * $row2['capcon_effect'];
		    			$result = $this->Capitalcontribution_model->updateCapConBalance($acctg_period, $employee_id, $tran_new_amount, $user);
		    			if ($result['error_code'] == '1'){
		    				throw new Exception($result['error_message']);
		    			}
		    			
						//added by Joseph 03-01-2011
						//try to check resulting capcon balance if will be below min bal
						if($trans_group=="CC" || $trans_group=="LP"){
							if($tran_code!="CLSE"  && $row2['capcon_effect'] == -1){
								//[START] Deleted by Kweeny Libutan for 8th Enhancement - Part 2 (Update to new checking for required balance during posting) 2013/10/31
								/* $ccBal = $this->Capitalcontribution_model->retrieveCapConBalance($employee_id, $acctg_period);
								$ccMinBal = $this->Parameter_model->retrieveValue('CCMINBAL');
								$reqBal = $this->Mloan_model->retrieveReqBalance($employee_id);
								if($ccMinBal>$reqBal){
									$reqBal = $ccMinBal;
								}
								
								//check if retiree
								if ($this->showCompCode($employee_id)=="920"){
									$interestAndRemTerms = $this->Mloan_model->getInterestAndRemainingTerms($employee_id);
									$reqBal = ($reqBal > $interestAndRemTerms) ? $reqBal : $interestAndRemTerms;
								} */
								//[END] Deleted by Kweeny Libutan for 8th Enhancement - Part 2 (Update to new checking for required balance during posting) 2013/10/31
								
								//[START] Added by Kweeny Libutan for 8th Enhancement - Part 2 (Update to new checking for required balance during posting) 2013/10/31
								$balance_info = $this->Mloan_model->getEmployeeBalanceInfo($employee_id, $acctg_period);
								$ccBal = $balance_info['capconBal'];
								$reqBal = $balance_info['reqBal'];
								//[END] Added by Kweeny Libutan for 8th Enhancement - Part 2 (Update to new checking for required balance during posting) 2013/10/31
								
								if($ccBal < $reqBal){
									throw new Exception("Capcon balance for employee $employee_id falls below minimum required balance.");
								}
							}
						}
						
		    			//cancelled by ernie - only be done in employee master
		    			//update employee's company
		    			//promote transfer - but no table yet
		    			
		    			//for loan payment specifications
		    			if ($trans_group == 'LP' || $source == 'm_loan_payment'){
		    				//retrieve mloanpayment and mloan details
		    				$fields = explode(",", $remarks);
		    				$values = explode(",", $reference);
		    				$conds = array();
			    			for ($i=0; $i<count($fields); $i++){
					    		$conds[$fields[$i]] = $values[$i];
					    	}
		    				$data = $this->Mloanpayment_model->get($conds);
		    				$lp_details = $data['list'][0];
		    				$data = $this->Mloan_model->get(array('loan_no'=>$lp_details['loan_no']));
		    				$loan_details = $data['list'][0];
		    				
		    				//update loan principal balance, capcon balance, close flag and interest balance
		    				$new_prinbal = $loan_details['principal_balance'] - $lp_details['amount'];
							
							//check if 1/3 capcon is required
							$is_capcon_req = $this->Loancodedetail_model->isCapConRequired($loan_details['loan_code'], $yos);
		    				$new_capconbal = round($new_prinbal/3, 2);
		    				$new_intbal = $loan_details['interest_balance'] - $lp_details['interest_amount'];
		    				
		    				$loan_details['principal_balance'] = $new_prinbal;
							//if 1/3 required then need to update capcon bal field
							//START Modified by Kweeny Libutan for 1/3 Capcon Requirement Transition 2013/12/05
							if ($is_capcon_req){
								$loan_details['capital_contribution_balance'] = $new_capconbal;
							} else {
								$loan_details['capital_contribution_balance'] = 0;
							}
							//END Modified by Kweeny Libutan for 1/3 Capcon Requirement Transition 2013/12/05
							
		    				$loan_details['interest_balance'] = $new_intbal;
		    				$loan_details['modified_by'] = $user;
		    				
		    				//update loan close flags if balance < 0
		    				//if ($new_prinbal < 1){ Change by Joseph on 3/23/2011
							if ($new_prinbal <= 0){
		    					$loan_details['close_flag'] = 1;
								//add deletion of guarantor here if loan is already paid
								//$this->Mloanguarantor_model->setValue('status_flag', '0');
								$result = $this->Mloanguarantor_model->delete(array('loan_no'=>$lp_details['loan_no']));
								if ($result['error_code'] == '1'){
									throw new Exception($result['error_message']);
								}
								
		    				} else{
		    					$loan_details['close_flag'] = 0;
		    				}
		    				
		    				$this->Mloan_model->populate($loan_details);
		    				$result = $this->Mloan_model->update();
		    				if ($result['error_code'] == '1'){
		    					throw new Exception($result['error_message']);
		    				}
		    				
		    				//update loan payment balance
		    				$lp_details['balance'] = $new_prinbal;
		    				$lp_details['modified_by'] = $user;
		    				$this->Mloanpayment_model->populate($lp_details);
		    				//need to pass condition because of multiple pk
		    				$result = $this->Mloanpayment_model->update($conds);   
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
							
							$result = $this->Ttransaction_model->updateOR($source, $remarks, $reference, ++$or_no, $or_date);
							if ($result){
								$result = $this->Parameter_model->updateValue('LASTORNO', $or_no, $user);
							}
							
						}
						//if time is beyond 2 minutes, referesh lock manager
						// if ($new_time <= date('Ymdhis')){
							// $this->Lockmanager_model->setValue('key', date('Ymdhis'));
							// $this->Lockmanager_model->update(array('function_id'=>$this->constants->batch_lock));
							// $new_time = date('Ymdhis',strtotime("+{$refresh} minute"));
						// }
		    			
					}
					
					//move to m_transaction
		    		$result = $this->Mtransaction_model->batchInsertByGroup($trans_group, $date);
		    		if ($result['error_code'] == '1'){
		    			throw new Exception($result['error_message']);
		    		}
		    		$result = $this->Ttransaction_model->deleteBatchTransaction($trans_group, $date);
		    		if ($result['error_code'] == '1'){
		    			throw new Exception($result['error_message']);
		    		}
					
					$result = $this->Parameter_model->updateValue('JOURNALNO', $journal_no, $user);
					if ($result['error_code'] == '1'){
						throw new Exception($result['error_message']);
					}
	//				$result = $this->Parameter_model->updateValue('PECATRANNO', $tran_no, $user);
	//				if ($result['error_code'] == '1'){
	//					throw new Exception($result['error_message']);
	//				}
					
	//				//transaction - commit or rollback
	//				$this->db->trans_complete();
					
					//appended messages like pd
					if($this->db->trans_status() === TRUE){
						$this->db->trans_commit();
						$resp_message .= "{$trans_group} {$trans_group_desc} successfully posted.</br>";
			        } else{
			        	$this->db->trans_rollback();
						$this->db->_trans_status = TRUE;
			        	++$error;
			        	$resp_message .= "{$trans_group} {$trans_group_desc} processing failed.</br>";
			//		  echo '{"success":false,"msg":"'.$result['error_message'].'"}' . $result['query'];
					}
					
					//refresh lock manager here every end of transaction group
					$this->Lockmanager_model->setValue('key', date('Ymdhis'));
					$this->Lockmanager_model->update(array('function_id'=>$this->constants->batch_lock));
					
				} catch(Exception $e){
					$this->db->trans_rollback();
					$this->db->_trans_status = TRUE;
					++$error;
					if ($e->getMessage() != ''){
						$resp_message .= "{$e->getMessage()}</br>";
					}
					 log_message('debug','zzzzzzz'.$this->db->last_query());
					$resp_message .= "{$trans_group} {$trans_group_desc} processing failed.</br>";
				}
			}
		} catch (Exception $e){
			++$error;
			if ($e->getMessage() != ''){
				$resp_message .= "{$e->getMessage()}</br>";
			}
		}
		
		$this->Lockmanager_model->release($this->constants->batch_lock);
		
		if ($error > 0){
			echo '{"success":false,"msg":"'.$resp_message.'"}';
		} else{
			$resp_message .= "Do you want to end the current transaction for the day?</br>";
			echo '{"success":true,"msg":"'.$resp_message.'"}';
		}
	}	

	function dateDiff($start, $end){
		$j_start = gregoriantojd(substr($start, 4, 2), substr($start, 6, 2), substr($start, 0, 4));
		$j_end = gregoriantojd(substr($end, 4, 2), substr($end, 6, 2), substr($end, 0, 4));
		
		return $j_end - $j_start;
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
}
?>
