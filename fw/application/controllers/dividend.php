<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Dividend extends Asi_controller {

	function Dividend(){
		parent::Asi_controller();
		$this->load->model('Dividend_model');
		$this->load->model('Parameter_model');
		$this->load->model('Ttransaction_model');
		$this->load->model('Capitalcontribution_model');
		$this->load->model('Transactioncode_model');
		$this->load->model('Lockmanager_model');
		$this->load->helper('url');
		$this->load->library('constants');
		$this->load->library('asi_model');
		//$this->load->scaffolding('t_loan');
	}
	
	function index(){		
		$date = $this->Parameter_model->retrieveValue('CURRDATE');
		$date = date('m/d/Y',strtotime($date));
		//echo $date;
//		$_REQUEST['dividend'] = array('start_date' => '20100101',
//										'end_date' => '20100331',
//										'accounting_period' => $date,
//										'dividend_code' => 'DIVD',
//										'dividend_rate' => '2.2',
//										'with_tax_code' => 'DTAX',
//										'with_tax_rate' => '1.1',
//										'created_by' => 'mowkz',
//										'modified_by' => 'mowkz');
		echo json_encode(array(
            'data' => array(array('currdate' => $date))
        ));
		//$this->processDividend();
		
//		$div_array = $_REQUEST['dividend'];
//		$start_date = $div_array['start_date'];
//		$end_date = $div_array['end_date'];
//		$period = $div_array['accounting_period'];
//		$div_rate = $div_array['dividend_rate'] / 100;
//		$tax_rate = $div_array['with_tax_rate'] / 100;	
//		$this->Capitalcontribution_model->retrieveDividendAmount($start_date, $end_date, 5000, $div_rate);

//		$this->processDividend();
	}
	
 	function getDivCodes(){
		$data = $this->Transactioncode_model->get_list(array('transaction_group'=>'DV', 'status_flag'=>'1'),null,null,'transaction_code as code,transaction_description as name',null);
		
		echo json_encode(array(
            'data' => $data['list']
        )); 
	}
	
	function processDividend(){
		//get posted data
		$div_array = isset($_REQUEST['dividend']) ? $_REQUEST['dividend'] : array();
		$start_date = isset($div_array['start_date']) ? $div_array['start_date'] : '';
		$end_date = isset($div_array['end_date']) ? $div_array['end_date'] : '';
		$period = isset($div_array['accounting_period']) ? $div_array['accounting_period'] : '';
		
		$div_rate = isset($div_array['dividend_rate']) ? $div_array['dividend_rate']  / 100 : 0;
		$tax_rate = isset($div_array['with_tax_rate']) && $div_array['with_tax_rate'] != '' ? $div_array['with_tax_rate']  / 100 : 0;

		$div_code = isset($div_array['dividend_code']) ? $div_array['dividend_code'] : '';
		$tax_code = isset($div_array['with_tax_code']) ? $div_array['with_tax_code'] : '';
		
		$div_array['vat_code'] = '';
		$div_array['vat_rate'] = 0;
		
		//convert time here to ymd format
		$period = date('Ymd',strtotime($period));
		//temp since date fields still have 000000 values
		//$period .= "000000";
		$start_date = date('Ymd',strtotime($start_date));
		$end_date = date('Ymd',strtotime($end_date));
		
		$div_array['start_date'] = $start_date;
		$div_array['end_date'] = $end_date;
		$div_array['accounting_period'] = $period;
		$user = $div_array['created_by'];
		$this->Lockmanager_model->user = $user;
		$div_array['modified_by'] = $user;
		
		try{
			//acquire lock for 10 minutes
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
			$div_array['status_flag'] = '1';
			$min_bal = $this->Parameter_model->retrieveValue('CCMINBAL');
			
			//transaction - begin
			$this->db->trans_begin();
			
			$div_no = $this->Parameter_model->retrieveValue('LASTDIVNO');		
			//check if acctg period already exist in t_dividend
			$this->Dividend_model->populate(array('accounting_period' => $period));
			$result = $this->Dividend_model->get();
			$div_ret = $result['list'];
			
			//fix yeah: haiz
			$div_array['with_tax_rate'] = $tax_rate;
			
			if (count($div_ret) > 0){
				//update details
				$div_array['dividend_no'] = $div_ret[0]['dividend_no'];
				unset($div_array['created_by']);
				$this->Dividend_model->populate($div_array);
				$result = $this->Dividend_model->update();
				if ($result['error_code'] == '1'){
	    			throw new Exception($result['error_message']);
	    		}
			} else{
				//insert new dividend
				$div_array['dividend_no'] = str_pad(++$div_no, 10, '0', STR_PAD_LEFT);
				$this->Dividend_model->populate($div_array);
				$result = $this->Dividend_model->insert();
				if ($result['error_code'] == '1'){
	    			throw new Exception($result['error_message']);
	    		}
				
				//update div no
				$result = $this->Parameter_model->updateValue('LASTDIVNO', $div_no, $user);
				if ($result['error_code'] == '1'){
	    			throw new Exception($result['error_message']);
	    		}
			}
			
			//delete existing dividend of the month
			$result = $this->Ttransaction_model->deleteTransactionByGroup($date, 'DV');
			if ($result['error_code'] == '1'){
	    			throw new Exception($result['error_message']);
	    		}
			
			//get active members with its dividend amount
			$member_array = $this->Capitalcontribution_model->retrieveDividendAmount($start_date, $end_date, $min_bal, $div_rate, $acctg_period);
			
			//insert dividends to transaction table
			$tran_param = array();
			$tran_no = $this->Parameter_model->retrieveValue('PECATRANNO');
			
			foreach ($member_array as $row){
				$tran_param['transaction_no'] = ++$tran_no;
				$tran_param['transaction_date'] = $date;
				$tran_param['transaction_code'] = $div_code;
				$tran_param['employee_id'] = $row['employee_id'];
				$tran_param['transaction_amount'] = round($row['dividend_amounts'],2);
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
	//    		echo $result['error_message'];

				if ($tax_rate != 0 and $tax_code != ''){
				
					$tran_param['transaction_no'] = ++$tran_no;
					$tran_param['transaction_date'] = $date;
					$tran_param['transaction_code'] = $tax_code;
					$tran_param['employee_id'] = $row['employee_id'];
					$tran_param['transaction_amount'] = $row['dividend_amounts'] * $tax_rate;
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
			
			if($this->db->trans_status() === TRUE){
				$this->db->trans_commit();
				echo "{'success':true,'msg':'Dividend sucessfully processed.'}";
			} else{
				$this->db->trans_rollback();
				echo "{'success':false,'msg':'Dividend failed to process.'}";        	
	//		  echo '{"success":false,"msg":"'.$result['error_message'].'"}' . $result['query'];
			}
		} catch(Exception $e){
			$this->db->trans_rollback();
			$resp_message = "";
			if ($e->getMessage() != ''){
				$resp_message .= $e->getMessage() . "</br>";
			}
			$resp_message .= "Dividend failed to process.";
			echo "{'success':false,'msg':'{$resp_message}'}";    
			log_message('debug', $this->db->last_query());
		}
		
		$this->Lockmanager_model->release($this->constants->batch_lock);
	}	
		
}
?>
