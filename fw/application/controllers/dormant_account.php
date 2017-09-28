<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Dormant_account extends Asi_controller {

	function Dormant_account(){
		parent::Asi_controller();
		$this->load->model('Parameter_model');
		$this->load->model('Ttransaction_model');
		$this->load->model('Mtransaction_model');
		$this->load->model('Lockmanager_model');
		$this->load->helper('url');
		$this->load->library('constants');
		//$this->load->scaffolding('t_loan');
	}
	
	function index(){		
		$date = $this->Parameter_model->retrieveValue('CURRDATE');
		$date = date('m/d/Y',strtotime($date));
		
		$dates = $this->Ttransaction_model->getDormantTransactionDate();
		$date1 = isset($dates[0]) ? date('m/d/Y',strtotime($dates[0])) : '';
		$date2 = isset($dates[1]) ? date('m/d/Y',strtotime($dates[1])) : '';
		$date3 = isset($dates[2]) ? date('m/d/Y',strtotime($dates[2])) : '';
		
		echo json_encode(array(
            'data' => array(array('currdate' => $date
								,'date1' => $date1
								,'date2' => $date2
								,'date3' => $date3))
        ));
//		$_REQUEST['dormant_account'] = array('user_id' => 'PECA');
//		$this->processDormantAccount();
		// echo json_encode(array(
            // 'data' => $dates
        // ));
	}
	
	function processDormantAccount(){
		$user = $_REQUEST['dormant_account']['user_id'];
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
			$dfee = $this->Parameter_model->retrieveValue('DORMANTFEE');
			$dyears = $this->Parameter_model->retrieveValue('DORMANTYRS');
			
			//transaction - begin
			$this->db->trans_begin();
			
			//delete existing DFEE withrawals of the month
			// $result = $this->Ttransaction_model->deleteTransactionByCode($date, 'DFEE');
			// if ($result['error_code'] == '1'){
				// throw new Exception($result['error_message']);
			// }
			
			$dormants = $this->Ttransaction_model->getDormantTransactionCount($date) +
					$this->Mtransaction_model->getDormantTransactionCount($date);
					
			if ($dormants > 0){
				throw new Exception("Dormant account for this month was already processed.");
			}
			
			//get current date - 2 years
			$date_before = (substr($date, 0, 4) - $dyears) . substr($date, 4);
			$dfee_array = $this->Mtransaction_model->retrieveDormantAccount($date_before);
			
			//insert dormant fees
			$tran_param = array();
			$tran_no = $this->Parameter_model->retrieveValue('PECATRANNO');
			
			foreach ($dfee_array as $row){
				$tran_param['transaction_no'] = ++$tran_no;
				$tran_param['transaction_date'] = $date;
				$tran_param['transaction_code'] = 'DFEE';
				$tran_param['employee_id'] = $row['employee_id'];
				$tran_param['transaction_amount'] = $dfee;
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
				echo "{'success':true,'msg':'Dormant account transactions sucessfully processed.'}";
	        } else{
	        	$this->db->trans_rollback();
	        	echo "{'success':false,'msg':'Dormant Account transactions failed to process.'}";        	
	//		  echo '{"success":false,"msg":"'.$result['error_message'].'"}' . $result['query'];
			}
		} catch (Exception $e){
			$this->db->trans_rollback();
			$resp_message = "";
			if ($e->getMessage() != ''){
				$resp_message .= "{$e->getMessage()}</br>";
			}
			$resp_message .= "Dormant Account transactions failed to process.</br>";
			echo "{'success':false,'msg':'{$resp_message}'}";
		}
		
		$this->Lockmanager_model->release($this->constants->batch_lock);
		
	}	
		
}
?>
