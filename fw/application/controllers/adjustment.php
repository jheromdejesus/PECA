<?php

class Adjustment extends Asi_controller {

	function Adjustment()
	{
		parent::Asi_controller();
		$this->load->model('parameter_model');
		$this->load->model('transactioncode_model');
		$this->load->model('ttransaction_model');
		$this->load->library('constants');
		$this->load->model('loanpayment_model');
		$this->load->model('mloanpayment_model');
		$this->load->model('mloan_model');
	}

	function index()
	{

	}
	
	/**
	 * @desc To retrieve all transaction groups
	 * @return array
	 */
	function readTranGrp(){
		$data = $this->constants->create_list(
			$this->constants->transaction_group
		);
		
		echo json_encode(array(
			'data'=> $data
		));	
	}

	/**
	 * @desc Retrieve transaction codes according to transaction group
	 * @return unknown_type
	 */
	function readByTranGroup(){ 
		$_REQUEST['adjustment']['tranGrp'] = 'CC';
	    $data = $this->transactioncode_model->get_list(
		array('transaction_group' => $_REQUEST['adjustment']['tranGrp'], 'status_flag' => '1')
		,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
		,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
		,array('transaction_code'							
			,'transaction_description'
			)						
		,'transaction_code ASC'
		);
		
		echo json_encode(array(
			'success' => true,
            'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
        ));        
	}
	
	/**
	 * @desc Returns the reference entry for a specific transaction no.
	 * @param $transNo
	 * @return string
	 */
	function getReference($transNo){
		$data = $this->ttransaction_model->get(array('transaction_no'=>$transNo)
			,array('reference'));
			
		return explode(',', $data['list'][0]['reference']); 
	}
	
	function getInterestAmount($loan_no, $payment_date, $transaction_code, $payor_id){
		$data = $this->mloanpayment_model->get(array(
			'loan_no'=> $loan_no
			,'payment_date' => $payment_date
			,'transaction_code' => $transaction_code
			,'payor_id' => $payor_id
		), array('interest_amount'));
		
		return $data['list'][0]['interest_amount'];
	}
	
	/**
	 * @desc Retrieves the transaction code of a specific transaction no.
	 */
	function getTransGroup($transNo){
		$data = $this->ttransaction_model->get(array('transaction_no' => $transNo, 'status_flag' => 2)
			, array('transaction_code'));
		$transCode = $data['list'][0]['transaction_code'];
		
		$data = $this->transactioncode_model->get(array('transaction_code' => $transCode)
			,array('transaction_group')
		);
		
		if(!isset($data['list'][0]['transaction_group']))
			return "";
		else 
			return $data['list'][0]['transaction_group'];
	}
	
	/**
	 * @desc To retrieve a specific transactions according to transaction code, employee id and range of amount
	 */
	function read(){
		$arr_filter = array();

		if ($_REQUEST['transGrp'] != ''){
			$arr_filter['r.transaction_group'] = $_REQUEST['transGrp'];
		}
		if ($_REQUEST['transCode'] != ''){
			$arr_filter['r.transaction_code'] = $_REQUEST['transCode'];
		}
		if ($_REQUEST['empid'] != ''){
			$arr_filter['m.employee_id'] = $_REQUEST['empid'];
		}
		if ($_REQUEST['fromAmt'] != ''){
			$arr_filter['t.transaction_amount >='] = $_REQUEST['fromAmt'];
		}
		if ($_REQUEST['toAmt'] != ''){
			$arr_filter['t.transaction_amount <='] = $_REQUEST['toAmt'];
		}
		
		$arr_filter['t.status_flag'] = '2';
		
		$data = $this->ttransaction_model->getTranList(
			$arr_filter
			,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
			,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
			,array('t.transaction_no AS transaction_no'
				,'r.transaction_description AS transaction_description'
				,'m.employee_id AS employee_id'
				,"CONCAT(m.last_name, ', ', m.first_name) AS employee_name"
//				,'m.first_name AS first_name'
				,'t.transaction_amount AS transaction_amount'
				,'r.transaction_group AS transaction_group'
				)
			,'t.transaction_no ASC'
		);
		
//		$count = $this->db->count_all('t_transaction', );
		//add formatting of currency here
//		$arr_trans = $data['list'];
//		for ($index = 0; $index< count($arr_trans); $index++){
//			$row = $arr_trans[$index];
//			$arr_trans[$index]['transaction_amount_ex'] = $row['transaction_amount'];
//			$arr_trans[$index]['transaction_amount'] = number_format($row['transaction_amount'], 2, '.', ',');
//		}
		
		echo json_encode(array(
			'success' => true
			,'data' => $data['list']
			,'total' => $data['count']
			,'query' => $data['query']
			));
	}
	
	/**
	 * @desc Retrieve a single transaction.
	 * @param transaction_no
	 * @return array
	 */
	function show(){
		$_REQUEST['adjustment']['transNo'] = '12222';
		$data = $this->ttransaction_model->getTranList(
			array(
				't.transaction_no' => $_REQUEST['adjustment']['transNo']
				,'t.status_flag' => '2'
				)
			,array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null
			,array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null
			,array('t.transaction_no AS transaction_no'
				,'r.transaction_code AS transaction_code'
				,'r.transaction_description AS transaction_description'
				,'m.employee_id AS employee_id'
				,'m.last_name AS last_name'
				,'m.first_name AS first_name'
				,'t.transaction_amount AS transaction_amount'
				)
		);

		echo json_encode(array(
			'success' => true
			,'data' => $data['list']
			,'total' => $data['count']
			,'query' => $data['query']
			));
	}
	
	/**
	 * @desc Update selected transaction.
	 * @param Transaction no. and amount
	 * @return string (status message)
	 */
	function update(){
//		$_REQUEST['adjustment'] = array(
//			'transNo' => '4342'
//			,'amt' => 9999	
//		);
		
//		$_REQUEST['user'] = 'PECA';
		log_message('debug', "[START] Controller adjustment:update");
		log_message('debug', "adjustment param exist?:".array_key_exists('adjustment',$_REQUEST));
		
		if (array_key_exists('adjustment',$_REQUEST)) {
			$currDate = $this->parameter_model->getParam('CURRDATE');
			$currDate = date("Ymd", strtotime($currDate));
			$transGroup = $this->getTransGroup($_REQUEST['adjustment']['transNo']);
			$transCode = $this->getTransCode($_REQUEST['adjustment']['transNo']);
			
			if(!$this->transactioncode_model->transactionCodeExists($transCode, $transGroup))
				echo "{'success':false,'msg':'Transaction code does not exist','error_code':'47'}";	
			else{
				if($transGroup == 'LP'){
					$refArr = $this->getReference($_REQUEST['adjustment']['transNo']);
					$this->db->trans_start();
					$updateLoanPaymentResult = $this->updateLoanPayment($refArr, $_REQUEST['adjustment']['amt'], $_REQUEST['user']);
					$updateTransResult = $this->updateTrans($_REQUEST['adjustment']['transNo'], $_REQUEST['adjustment']['amt'], $currDate, $_REQUEST['user']);
					$this->db->trans_complete();
					
					if ($updateLoanPaymentResult['affected_rows'] <= 0 || $updateTransResult['affected_rows'] <= 0 || $this->db->trans_status() === FALSE){
						echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
					} 
					else{
						echo "{'success':true,'msg':'Data successfully saved.'}";
					}
				}
				else if($transGroup == 'PD'){
					$this->db->trans_start();
					$source = $this->getSource($_REQUEST['adjustment']['transNo']);
					$updateLoanPaymentResult['affected_rows'] = 1;
					if($source == "m_loan_payment"){
						$refArr = $this->getReference($_REQUEST['adjustment']['transNo']);
						$updateLoanPaymentResult = $this->updateLoanPayment($refArr, $_REQUEST['adjustment']['amt'], $_REQUEST['user']);
					}
					$updateTransResult = $this->updateTrans($_REQUEST['adjustment']['transNo'], $_REQUEST['adjustment']['amt'], $currDate, $_REQUEST['user']);
					$this->db->trans_complete();
					
					if ($updateLoanPaymentResult['affected_rows'] <= 0 || $updateTransResult['affected_rows'] <= 0 || $this->db->trans_status() === FALSE){
						echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
					} 
					else{
						echo "{'success':true,'msg':'Data successfully saved.'}";
					}
				}
				else{
					echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
				}
			}
		} else
			echo "{'success':false,'msg':'Data was NOT successfully saved.'}";

		log_message('debug', "[END] Controller adjustment:update");
	}
	
	/**
	*@desc Gets the source of the t_transaction entry
	*/
	function getSource($tran_no){
		$data = $this->ttransaction_model->get(array('transaction_no' => $tran_no)
			,array('source'));
			
		if(isset($data['list'][0]['source'])){
			return $data['list'][0]['source'];
		}
		else{
			return "";
		}
	}
	
	/**
	@desc Returns the transaction code of a specific transaction no.
	*/
	function getTransCode($tran_no){
		$data = $this->ttransaction_model->get(array('transaction_no'=>$tran_no), array('transaction_code'));
		if($data['count']>0)
			return $data['list'][0]['transaction_code'];
		else
			return "";
	}
	
	function updateLoanPayment($refArr, $amt, $user){
		$loan_no = $refArr[0];
		$transaction_code = $refArr[1];
		$payment_date = $refArr[2];
		$payor_id = $refArr[3];
		$interest_amount = $this->getInterestAmount($loan_no, $payment_date, $transaction_code, $payor_id);
		$principal_balance = $this->getPrincipalBalance($loan_no);
			
		if($interest_amount >= $amt){
			$this->mloanpayment_model->populate(array(
				'amount' => 0
				,'balance' => $principal_balance - $amt
				,'interest_amount' => $amt
				,'modified_by' => $user
			));
			
			$data = $this->mloanpayment_model->update(array(
				'payment_date' => $payment_date								
				,'loan_no'  => $loan_no							
				,'transaction_code' => $transaction_code							
				,'payor_id' => $payor_id						
				,'interest_amount >='  => $amt															
			));
			
			
		}
		else{
			$this->mloanpayment_model->populate(array(
				'amount' => $amt - $interest_amount
				,'balance' => $principal_balance - ($amt - $interest_amount)
				,'modified_by' => $user
			));
			
			$data = $this->mloanpayment_model->update(array(
				'payment_date' => $payment_date								
				,'loan_no'  => $loan_no							
				,'transaction_code' => $transaction_code							
				,'payor_id' => $payor_id						
				,'interest_amount <'  => $amt															
			));
		}
		
		return $data;
	}
	
	function getPrincipalBalance($loan_no){
		$data = $this->mloan_model->get(array('loan_no' => $loan_no)
			,array('principal_balance'));
			
		if($data['count']>0 && $data['list'][0]['principal_balance']!=""){
			return $data['list'][0]['principal_balance'];
		}
		else{
			return 0;
		}
	}
	/**
	 * @desc Update a specific transaction's amount and date
	 * @param $transNo
	 * @param $amt
	 * @param $currDate
	 * @return array
	 */
	function updateTrans($transNo, $amt, $currDate, $user){
		$this->ttransaction_model->populate(array(
			'transaction_no' => $transNo
			,'transaction_amount' => $amt
			//,'transaction_date' => $currDate
			,'modified_by' => $user
		));
		
		$data = $this->ttransaction_model->update();
		return $data;
	}
	
	/**
	 * @desc Delete a single transaction entry
	 */
	function delete(){
		log_message('debug', "[START] Controller adjustment:delete");
		log_message('debug', "adjustment param exist?:".array_key_exists('adjustment',$_REQUEST));
		
		$params = array();
		if (array_key_exists('data',$_REQUEST['adjustment'])){
			$params = json_decode($_REQUEST['adjustment']['data'],true);
			$this->db->trans_start();
			foreach($params as $value){
				$this->ttransaction_model->setValue('transaction_no', $value);
				$this->ttransaction_model->setValue('modified_by', $_REQUEST['user']);
				$this->ttransaction_model->setValue('status_flag', '0');
				$this->ttransaction_model->update();
				
				$data = $this->ttransaction_model->get(array('transaction_no' => $value)
					,array('transaction_code', 'source', 'reference', 'remarks')
				);
				
				if($data['count'] > 0){
					$remArr = explode(",", $data['list'][0]['remarks']);
					$refArr = explode(",", $data['list'][0]['reference']);
					
					/*while((list(,$rem) = each($remArr)) && (list(,$ref) = each($refArr))){
						$condArr[] = "$rem = '$ref'";
					}*/
					$ref_index = 0;
					$condArr = array();
					foreach($remArr as $remark){
						if(isset($refArr[$ref_index])){
							$condArr[] = "$remark = '$refArr[$ref_index]'";
						}
						$ref_index++;	
					}
				
					$condStr = implode(" AND ", $condArr);
				
					$getTG = $this->transactioncode_model->get(
						array('transaction_code' => $data['list'][0]['transaction_code'])
						,array('transaction_group')
					);
					
					if($getTG['count']==0 || $getTG['list'][0]['transaction_group']=='' || ($getTG['list'][0]['transaction_group']!='SC' && $data['list'][0]['source']!='m_loan_payment')){
						$this->db->query('UPDATE '.$data['list'][0]['source'].' SET status_flag=\'0\'
						 WHERE '.$condStr);
					}
					else{
						$this->db->query('DELETE FROM '.$data['list'][0]['source'].' WHERE '.$condStr);
					}
				}
			}
			$this->db->trans_complete();
			
			
			if ($this->db->trans_status() === FALSE){
				echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
			} else{
				echo "{'success':true,'msg':'Data successfully deleted.'}";
			}
		} else
			echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
			
		log_message('debug', "[END] Controller adjustment:delete");
	}
}

/* End of file adjustment.php */
/* Location: ./CodeIgniter/application/controllers/adjustment.php */