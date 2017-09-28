<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Month_end extends Asi_controller {

	function Month_end(){
		parent::Asi_controller();
		$this->load->model('Ledger_model');
		$this->load->model('Posting_model');
		$this->load->model('Parameter_model');
		$this->load->model('Capitalcontribution_model');
		$this->load->model('Mjournalheader_model');
		$this->load->model('Tjournalheader_model');
		$this->load->model('Mjournaldetail_model');
		$this->load->model('Tjournaldetail_model');
		$this->load->model('Lockmanager_model');
		$this->load->helper('url');
		$this->load->library('constants');
		//$this->load->scaffolding('t_loan');
	}
	
	function index(){		
		$date = $this->Parameter_model->retrieveValue('ACCPERIOD');
		$date = date('m/d/Y',strtotime($date));
		$capcon_posting = $this->Posting_model->retrieveLastPosting('capital_contribution');
		$capcon_posting = date('m/d/Y',strtotime($capcon_posting));
		$journal_posting = $this->Posting_model->retrieveLastPosting('journal');
		$journal_posting = date('m/d/Y',strtotime($journal_posting));
		//echo $date;
		
//		$_REQUEST['month_end'] = array( 'capcon' => '0',
//										'journal' => '0',
//										'user_id' => 'PECA');
//		echo date('Ymd',strtotime("+1 month", strtotime('20100401')));
//		echo 'zzzz' . $this->Posting_model->isPosted($date, 'journal');
//		$this->processMonthEnd();

		echo json_encode(array(
            'data' => array(array('acctgperiod' => $date
							, 'capcon_posting'=>$capcon_posting
							, 'journal_posting'=>$journal_posting))
        ));
	}
	
	function processMonthEnd(){
		//get posted data
		$mos_end_array = $_REQUEST['month_end'];
		$capcon_check = isset($mos_end_array['capcon']) ? 1 : 0;
		$journal_check = isset($mos_end_array['journal']) ? 1 : 0;
		$user = $mos_end_array['user_id'];
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
		
		$error = 0;
		$resp_message = '';
		
		$date = $this->Parameter_model->retrieveValue('CURRDATE');
		$acctg_period = $this->Parameter_model->retrieveValue('ACCPERIOD');
		
		$capcon_posted_flag = 0;
		$journal_posted_flag = 0;
		
		//check if capcon already posted for the accounting period
		$capcon_posted_flag = $this->Posting_model->isPosted($acctg_period, 'capital_contribution');		
		//month-end process for capcon
		if ($capcon_check == 1){
			try{
				//transaction - begin
				$this->db->trans_begin();
				
				//check if current accounting period and last posted difference is just 1 month
				$capcon_posting = $this->Posting_model->retrieveLastPosting('capital_contribution');
				if ($this->getMonthDiff($acctg_period, $capcon_posting) > 1){
					$new_posting = date('m/d/Y', strtotime('+1 month', strtotime($capcon_posting)));
					throw new Exception('Invalid current period for Capcon, must be ' . $new_posting . '.');
				}
				
	//			//check if capcon already posted for the accounting period
				//not yet posted
				if ($capcon_posted_flag < 1){
					//delete entries > accounting period
	//				$result = $this->Capitalcontribution_model->deleteCapCon($acctg_period);
					$result = $this->Capitalcontribution_model->delete(array('accounting_period >' => $acctg_period));
					if ($result['error_code'] == '1'){
						throw new Exception($result['error_message']);
					}
					$result = $this->Capitalcontribution_model->batchInsert($acctg_period, $user);				
					if ($result['error_code'] == '1'){
						throw new Exception($result['error_message']);
					}
					
					//insert new account for t_ledger
					$result = $this->Ledger_model->batchInsertCapcon($acctg_period, $user);	
					if ($result['error_code'] == '1'){
						throw new Exception($result['error_message']);
					}
				}
				
				//posted = 0 means acctgperiod already exist so just update
				//posted = -1 means acctgperiod does not exist so need to insert
				$posting_array = array();
				if ($capcon_posted_flag == 0){
					$posting_array['accounting_period'] = $acctg_period;
					$posting_array['capital_contribution'] = '1';
		//			$posting_array['journal'] = '0';
					$posting_array['capital_contribution_user'] = $user;
					$posting_array['capital_contribution_date'] = $date;
		//			$posting_array['journal_user'] = $user;
		//			$posting_array['journal_date'] = $date;
					$this->Posting_model->populate($posting_array);
					$result = $this->Posting_model->update();
					if ($result['error_code'] == '1'){
						throw new Exception($result['error_message']);
					}
				} else{
					if ($capcon_posted_flag == -1){
						$posting_array['accounting_period'] = $acctg_period;
						$posting_array['capital_contribution'] = '1';
						$posting_array['journal'] = '0';
						$posting_array['capital_contribution_user'] = $user;
						$posting_array['capital_contribution_date'] = $date;
						$posting_array['journal_user'] = NULL;
						$posting_array['journal_date'] = NULL;
						$this->Posting_model->populate($posting_array);
						$result = $this->Posting_model->insert();
						if ($result['error_code'] == '1'){
							throw new Exception($result['error_message']);
						}
					} else{
						//already posted
						throw new Exception("Capcon Balances for this period is already processed.");
					}					
				}	
				
				//transaction - commit or rollback
				//$this->db->trans_complete();	
				if ($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					++$error;
					$resp_message .= "Capital contribution failed to process.</br>";
					//$resp_message['savings'] = '{"success":false,"msg":"'.$result['error_message'].'"}';
				} else{
					$resp_message .= "Capital contribution successfully processed.</br>";
					$capcon_posted_flag = 1;
					$this->db->trans_commit();
					//$resp_message['savings'] = "{'success':true,'msg':'Payroll deduction for savings successfully processed.'}";
				}	
			} catch (Exception $e){
				$this->db->trans_rollback();
				++$error;
				if ($e->getMessage() != ''){
					$resp_message .= "{$e->getMessage()}</br>";
				}
				$resp_message .= "Capital contribution failed to process.</br>";
			}
		}
		
		$this->Lockmanager_model->setValue('key', date('Ymdhis'));
		$this->Lockmanager_model->update(array('function_id'=>$this->constants->batch_lock));
		//check if journal already posted for the accounting period
		$journal_posted_flag = $this->Posting_model->isPosted($acctg_period, 'journal');		
		//month-end for loan
		if ($journal_check == 1){
			try{
				//transaction - begin
				$this->db->trans_begin();
				
				//check if current accounting period and last posted difference is just 1 month
				$journal_posting = $this->Posting_model->retrieveLastPosting('journal');
				if ($this->getMonthDiff($acctg_period, $journal_posting) > 1){
					$new_posting = date('m/d/Y', strtotime('+1 month', strtotime($journal_posting)));
					throw new Exception('Invalid current period for Journal, must be ' . $new_posting . '.');
				}				
				
	//			//check if journal already posted for the accounting period
	//			$journal_posted_flag = $this->Posting_model->isPosted($acctg_period, 'journal');
				//not yet posted
				if ($journal_posted_flag < 1){
					//delete entries > accounting period
					$result = $this->Ledger_model->delete(array('accounting_period >' => $acctg_period));
					if ($result['error_code'] == '1'){
						throw new Exception($result['error_message']);
					}
					//insert new account to ledger
					$result = $this->Ledger_model->batchInsert($acctg_period, $user, 1);	
					if ($result['error_code'] == '1'){
						throw new Exception($result['error_message']);
					}
					//update values of ledger from journal entries
					$result = $this->Ledger_model->batchUpdate($acctg_period, $user);
					if ($result['error_code'] == '1'){
						throw new Exception($result['error_message']);
					}
					//insert existing accounts to ledger for next accounting period
					// $result = $this->Ledger_model->batchInsert($acctg_period, $user, 2);
					// if ($result['error_code'] == '1'){
						// throw new Exception($result['error_message']);
					// }
					//update for close debits and credits
					$result = $this->Ledger_model->batchUpdateClosing($acctg_period, $user);
					if ($result['error_code'] == '1'){
						throw new Exception($result['error_message']);
					}
					//update debit and credit for 3050
					$result = $this->Ledger_model->batchUpdate3050($acctg_period, $user);
					if ($result['error_code'] == '1'){
						throw new Exception($result['error_message']);
					}
					
					//insert existing accounts to ledger for next accounting period
					$result = $this->Ledger_model->batchInsert($acctg_period, $user, 2);
					if ($result['error_code'] == '1'){
						throw new Exception($result['error_message']);
					}
					
					// $this->Lockmanager_model->setValue('key', date('Ymdhis'));
					// $this->Lockmanager_model->update(array('function_id'=>$this->constants->batch_lock));

					//moving from tjournal header/detail to mjournal header/detail
					$result = $this->Mjournalheader_model->batchInsert($acctg_period, $user);
					if ($result['error_code'] == '1'){
						throw new Exception($result['error_message']);
					}
					$result = $this->Mjournaldetail_model->batchInsert($acctg_period, $user);
					if ($result['error_code'] == '1'){
						throw new Exception($result['error_message']);
					}
				
					$result = $this->Tjournaldetail_model->deleteJournalDetail($acctg_period);
					if ($result['error_code'] == '1'){
						throw new Exception($result['error_message']);
					}
					$result = $this->Tjournalheader_model->delete(array('accounting_period'=>$acctg_period));					
					if ($result['error_code'] == '1'){
						throw new Exception($result['error_message']);
					}
				}
				
				//posted = 0 means acctgperiod already exist so just update
				//posted = -1 means acctgperiod does not exist so need to insert
				$posting_array = array();
				if ($journal_posted_flag == 0){
					$posting_array['accounting_period'] = $acctg_period;
	//				$posting_array['capital_contribution'] = '1';
					$posting_array['journal'] = '1';
	//				$posting_array['capital_contribution_user'] = $user;
	//				$posting_array['capital_contribution_date'] = $date;
					$posting_array['journal_user'] = $user;
					$posting_array['journal_date'] = $date;
					$this->Posting_model->populate($posting_array);
					$result = $this->Posting_model->update();
					if ($result['error_code'] == '1'){
						throw new Exception($result['error_message']);
					}
				} else {
					if ($journal_posted_flag == -1){
						$posting_array['accounting_period'] = $acctg_period;
						$posting_array['capital_contribution'] = '0';
						$posting_array['journal'] = '1';
						$posting_array['capital_contribution_user'] = NULL;
						$posting_array['capital_contribution_date'] = NULL;
						$posting_array['journal_user'] = $user;
						$posting_array['journal_date'] = $date;
						$this->Posting_model->populate($posting_array);
						$result = $this->Posting_model->insert();
						if ($result['error_code'] == '1'){
							throw new Exception($result['error_message']);
						}
					} else{
						//already posted
						throw new Exception("Journal Entries for this period is already processed.");
					}						
				}	
				
				//transaction - commit or rollback
				//$this->db->trans_complete();	
				if ($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					++$error;
					$resp_message .= "Journal entries failed to process.</br>";
					//$resp_message['savings'] = '{"success":false,"msg":"'.$result['error_message'].'"}';
				} else{
					$this->db->trans_commit();
					$resp_message .= "Journal entries successfully processed.\n";
					$journal_posted_flag = 1;
					//$resp_message['savings'] = "{'success':true,'msg':'Payroll deduction for savings successfully processed.'}";
				}	
			}catch (Exception $e){
				$this->db->trans_rollback();
				++$error;
				if ($e->getMessage() != ''){
					$resp_message .= "{$e->getMessage()}</br>";
				}
				$resp_message .= "Journal entries failed to process.</br>";
			}	
		}
			
		//update peca acctgperiod if capcon and journal are already processed 
		//(still to be verified from ernie since existing system will update 
		//accounting period even if not both are monthend)
		//if ($capcon_posted_flag + $journal_posted_flag == 2){			
		if ($capcon_posted_flag + $journal_posted_flag > 0){			//change by rayan as requested by client
			$next_acctg = date('Ymd',strtotime("+1 month", strtotime($acctg_period)));
			$result = $this->Parameter_model->updateValue('ACCPERIOD', $next_acctg, $user);
		}
		
		$this->Lockmanager_model->release($this->constants->batch_lock);
		
		if ($error > 0){
		  //echo "{'success':false,'msg':'Month end failed to process.'}";        	
		  echo "{'success':false,'msg':'$resp_message'}";        	
        } else{
        	echo "{'success':true,'msg':'Month end sucessfully processed.'}";
//		  echo '{"success":false,"msg":"'.$result['error_message'].'"}';
		}
	}	
	
	function getMonthDiff($date2, $date1){
		$year_covered = (substr($date2, 0, 4) - substr($date1, 0, 4))* 12;
		$month_covered = substr($date2, 4, 2) - substr($date1, 4, 2);
		$covered = $year_covered + $month_covered;
		
		return $covered;		
	}
		
}
?>
