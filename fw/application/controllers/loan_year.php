<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Loan_year extends Asi_controller {

	function Loan_year(){
		parent::Asi_controller();
		$this->load->model('MLoan_model');
		$this->load->model('Parameter_model');
		$this->load->model('Loancodeheader_model');
		$this->load->model('Lockmanager_model');
		$this->load->helper('url');
		$this->load->library('constants');
		//$this->load->scaffolding('t_loan');
	}
	
	function index(){		
		$date = $this->Parameter_model->retrieveValue('ACCPERIOD');
		$date = date('F Y',strtotime($date));
		
		echo json_encode(array(
            'data' => array(array('currdate' => $date))
        ));
	}
	
	function processLoanYearTerm(){
		$date = $this->Parameter_model->retrieveValue('CURRDATE');
		$acctg_period = $this->Parameter_model->retrieveValue('ACCPERIOD');	
		$user = $_REQUEST['process_loan_year']['user_id'];
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
			
			//transaction - begin
			$this->db->trans_begin();
			
			//update loan year term
			$new_interest = 0;
			$remain_term = 0;
			$new_principal_amort = 0;
			$cur_year = substr($acctg_period, 0, 4);    	
			
			$lyt_array = $this->MLoan_model->retrieveActiveLoans($acctg_period);
			// [START] NUVEM 1ST ENHANCEMENT Pt. 1
			$promoSuffix = $this->Parameter_model->get(array('parameter_id'=>'PROMOSUFX'),
				'parameter_value');	
			$promoSuffix = $promoSuffix['list'][0]['parameter_value'];
			// [END] NUVEM 1ST ENHANCEMENT Pt. 1
			foreach ($lyt_array as $row){
				// [START] NUVEM 1ST ENHANCEMENT Pt. 2
				$promoCode = $row['loan_code'].$promoSuffix;
				$promo = $this->Parameter_model->get(array('parameter_id'=>$promoCode),
					'parameter_name,parameter_value');	

				if(count($promo['list'])>0){
					$regularLoanCode = $promo['list'][0]['parameter_name']; // the regular loan code
					$promoPeriod = $promo['list'][0]['parameter_value']; // the promo perio

					$monthDifference = $this->getMonthDifference($row['amortization_startdate'],$acctg_period);

					if($monthDifference>=$promoPeriod){
						$row['loan_code'] = $regularLoanCode;
					}else{
						continue;
					}
				}
				// [END] NUVEM 1ST ENHANCEMENT Pt. 2
				$result = $this->Loancodeheader_model->get(array('loan_code'=>$row['loan_code']), 'emp_interest_pct,unearned_interest');
				//echo $this->db->last_query();
				$new_interest_factor = $result['list'][0]['emp_interest_pct'];
				$unearned = $result['list'][0]['unearned_interest'];
				//$remain_term_principal = $row['term'] - (($cur_year - substr($row['amortization_startdate'], 0, 4)) * 12);
				//$remain_term_principal = $row['principal_balance'] / $row['employee_principal_amort'];
				//$remain_term_principal = round($remain_sterm_principal);
				//20100415 rhye added new computation for remaining term - specific for interest
				//if ($remain_term_principal >= 12){
					//$remain_term_interest = 12;
				//}
				$remain_term_interest = 12;
				
				$new_interest = ($row['principal_balance'] * $new_interest_factor / 100) / $remain_term_interest;
				//$new_interest = round($new_interest,2);
				$new_interest = round($new_interest);
				//$new_principal_amort = round($row['principal_balance'] / $remain_term_principal);
				
				$tran_param['loan_no'] = $row['loan_no'];
				$tran_param['interest_rate'] = $new_interest_factor;
				$tran_param['employee_interest_amortization'] = ($unearned == 'N') ? $new_interest : 0;
				//$tran_param['employee_principal_amort'] = $new_principal_amort;
				$tran_param['modified_by'] = $user;
				
				$this->MLoan_model->populate($tran_param);
				$result = $this->MLoan_model->update();   
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
			
			//transaction - commit or rollback
			//$this->db->trans_complete();
			
			if($this->db->trans_status() === TRUE){  
				$this->db->trans_commit();
				echo "{'success':true,'msg':'Loan year term successfully processed.'}";
			} else{
				$this->db->trans_rollback();
				echo "{'success':false,'msg':'Loan year term failed to process.'}";
			}
		} catch (Exception $e){
			$this->db->trans_rollback();
			$resp_message = "";
			if ($e->getMessage() != ''){
				$resp_message .= $e->getMessage() . "</br>";
			}
			$resp_message .= "Loan year term failed to process.";
			echo "{'success':false,'msg':'{$resp_message}'}";    
		}
		
		$this->Lockmanager_model->release($this->constants->batch_lock);
	}
	// [START] NUVEM 1ST ENHANCEMENT Pt. 3
	function getMonthDifference($date1,$date2){ //amortization_startdate , acctg_period
		$ts1 = strtotime($date1);
		$ts2 = strtotime($date2);

		$year1 = date('Y', $ts1);
		$year2 = date('Y', $ts2);

		$month1 = date('m', $ts1);
		$month2 = date('m', $ts2);

		//echo $year1.' '.$month1.'<br/>';
		//echo $year2.' '.$month2;

		return $diff = (($year2 - $year1) * 12) + ($month2 - $month1);
	}
	// [END] NUVEM 1ST ENHANCEMENT Pt. 3		
}
?>
