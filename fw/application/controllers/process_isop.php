<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Process_isop extends Asi_controller {

	function Process_isop(){
		parent::Asi_controller();
		$this->load->model('Isop_model');
		$this->load->model('Parameter_model');
		$this->load->model('Ttransaction_model');
		$this->load->model('Lockmanager_model');
		$this->load->helper('url');
		$this->load->library('constants');
		//$this->load->scaffolding('t_loan');
	}
	
	function index(){		
		$date = $this->Parameter_model->retrieveValue('CURRDATE');
		$date = date('m/d/Y',strtotime($date));
		
		echo json_encode(array(
            'data' => array(array('currdate' => $date))
        ));
//		$_REQUEST['process_isop'] = array('user_id' => 'PECA');
//		$this->processIsop();
	}
	
	function processIsop(){
		$user = $_REQUEST['process_isop']['user_id'];
		$this->Lockmanager_model->user = $user;
		
		try{
			//acquire lock
			$permitted = $this->Lockmanager_model->acquire($this->constants->batch_lock);
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
			
			//transaction - begin
			$this->db->trans_begin();
			
			//delete existing ISOP withrawals of the month
			$result = $this->Ttransaction_model->deleteTransactionByGroup($date, 'IS');
			
			$isop_array = $this->Isop_model->retrieveIsopWithrawals($acctg_period, $date, $min_bal);
			
			//insert ISOP withrawals
			$tran_param = array();
			$tran_no = $this->Parameter_model->retrieveValue('PECATRANNO');
	    	
	    	foreach ($isop_array as $row){
	    		$tran_param['transaction_no'] = ++$tran_no;
	    		$tran_param['transaction_date'] = $date;
	    		$tran_param['transaction_code'] = $row['trancode'];
	    		$tran_param['employee_id'] = $row['employee_id'];
	    		$tran_param['transaction_amount'] = round($row['amount'],2);
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
	    		
	    		//updating transaction type of isop
	    		unset($row['trancode']);
	    		$this->Isop_model->populate($row);
	    		$this->Isop_model->setValue('transaction_type', 'P');
	    		$this->Isop_model->setValue('modified_by', $user);
	    		$result = $this->Isop_model->update();
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
	
			$result = $this->Parameter_model->updateValue('PECATRANNO', $tran_no, $user);
			if ($result['error_code'] == '1'){
				throw new Exception($result['error_message']);
			}
			
//			//transaction - commit or rollback
//			$this->db->trans_complete();
			
			if($this->db->trans_status() === TRUE){
				$this->db->trans_commit();
				echo "{'success':true,'msg':'ISOP deductions sucessfully processed.'}";
	        } else{
	        	$this->db->trans_rollback();
	        	echo "{'success':false,'msg':'ISOP deductions failed to process.'}";        	
	//		  echo '{"success":false,"msg":"'.$result['error_message'].'"}' . $result['query'];
			}
		} catch(Exception $e){
			$this->db->trans_rollback();
			$resp_message = "";
			if ($e->getMessage() != ''){
				$resp_message .= $e->getMessage() . "</br>";
			}
			$resp_message .= "ISOP deductions failed to process.";
			echo "{'success':false,'msg':'{$resp_message}'}";    
		}
		
		$this->Lockmanager_model->release($this->constants->batch_lock);
		
	}		
}
?>
