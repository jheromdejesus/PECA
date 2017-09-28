<?php

/* Location: ./CodeIgniter/application/controllers/investment.php */


class Investment extends Asi_Controller {
	function Investment()
	{
		parent::Asi_Controller();
		$this->load->model('tinvestmentheader_model');
		$this->load->model('transactioncode_model');
		$this->load->model('supplier_model');
		$this->load->model('parameter_model');
		$this->load->model('glentrydetail_model');
		$this->load->helper('url');
		$this->load->helper('date');
		$this->load->library('constants');
		//$this->load->library('common');
	}
	
	function index() {
		
	}
	
	/**
	 * @desc To retrieve all saved investments
	 * @return array
	 */
	function read()
	{
		$params = array('i.status_flag'	=> '1');		
//		if(array_key_exists('newinvestment', $_REQUEST)){
//			if(array_key_exists('investment_no', $_REQUEST['newinvestment']) && $_REQUEST['newinvestment']['investment_no']!=""){
//				$params['i.investment_no LIKE'] = $_REQUEST['newinvestment']['investment_no']."%";
//			}		
//		}
		if(array_key_exists('investment_no', $_REQUEST) && $_REQUEST['investment_no']!=""){
			$params['i.investment_no LIKE'] = $_REQUEST['investment_no']."%";
		}	
		$data = $this->tinvestmentheader_model->getInvestmentList(
			$params,
			array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
			array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
			array('i.investment_no'
					,'rt.transaction_description'
					,'s.supplier_name'				
					,'i.placement_date'				
					,'i.investment_amount')
			,'i.investment_no ASC'
		);

		foreach($data['list'] as $row => $value){
			$data['list'][$row]['placement_date'] = date("mdY", strtotime($value['placement_date']));
		}
		
		echo json_encode(array(
			'success' => true,
			'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
			));
		
	}	
	
	/**
	 * @desc To retrieve single investment information
	 * @return array
	 */
	function show()
	{
		$_REQUEST['filter'] = array(
									'status_flag' => '1'
								   ,'investment_no' => $_REQUEST['newinvestment']['investment_no']
							  );  
		$data = $this->tinvestmentheader_model->get_list(
					array_key_exists('filter',$_REQUEST) ? $_REQUEST['filter'] : null,
					array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
					array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
					array('investment_no'
							,'transaction_code'	
							,'supplier_id'
							,'placement_days'				
							,'investment_amount'
							,'interest_rate'
							,'interest_amount'
							,'remarks'
							,'maturity_date'
							,'placement_date')
				);
			
		foreach($data['list'] as $row => $value){
			$data['list'][$row]['placement_date'] = date("mdY", strtotime($value['placement_date']));
			$data['list'][$row]['maturity_date'] = date("mdY", strtotime($value['maturity_date']));
		}
				
		echo json_encode(array(
				'success' => true,
				'data' => $data['list'],
				'total' => $data['count'],
				'query' => $data['query']
				));
			
	}
	
	/**
	 * @desc Inserts new data to t_investment_header table
	 */
	function add()
	{
		log_message('debug', "[START] Controller investment:add");
		log_message('debug', "newinvestment param exist?:".array_key_exists('newinvestment',$_REQUEST));
		
		if (array_key_exists('newinvestment',$_REQUEST)) {	
			$bank = $this->supplier_model->supplierIdExists($_REQUEST['newinvestment']['supplier_id']);
			$transaction_code = $this->transactioncode_model->transactionCodeExists($_REQUEST['newinvestment']['transaction_code'], 'IN');
			if ($bank==0 && $transaction_code==0){
				echo "{'success':false,'msg':'Investment type and Supplier do not exist.','error_code':'153'}";
			}
			else if ($bank==0 && $transaction_code!=0){
				echo "{'success':false,'msg':'Supplier does not exist.','error_code':'153'}";
			}
			else if ($bank!=0 && $transaction_code==0){
				echo "{'success':false,'msg':'Investment type does not exist.','error_code':'153'}";
			}
			else {
				$placement_date = date("Ymd", strtotime($_REQUEST['newinvestment']['placement_date']));
				$maturity_date = date("Ymd", strtotime($_REQUEST['newinvestment']['maturity_date']));
				$invNo = $this->getParam('INVNO')+1;
				$invNo = str_pad($invNo, 10, "0", STR_PAD_LEFT);
				
				$this->tinvestmentheader_model->populate($_REQUEST['newinvestment']);
				$this->tinvestmentheader_model->setValue('status_flag', '1');
				$this->tinvestmentheader_model->setValue('investment_no', $invNo);
				$this->tinvestmentheader_model->setValue('maturity_date', $maturity_date);
				$this->tinvestmentheader_model->setValue('placement_date', $placement_date);
				
				$checkDuplicate = $this->tinvestmentheader_model->checkDuplicateKeyEntry();
				
				if($checkDuplicate['error_code'] == 1) {
					$result['error_code'] = 1;
					$result['error_message'] = $checkDuplicate['error_message'];
				}
				else {
					$result = $this->tinvestmentheader_model->insert();
					//update INVNO in i_parameter_list table
					$this->updateParam('INVNO', $invNo);	
				} 	
				if($result['error_code'] == 0){  
					echo "{'success':true,'msg':'Data successfully saved.','investment_no':'$invNo'}";
				} else
					echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';	
			}								
		}
		else {
			echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
		}	
		
		log_message('debug', "[END] Controller investment:add");
	}
	
	/**
	 * @desc Updates data of investment record
	 */
	function update()
	{
		log_message('debug', "[START] Controller investment:update");
		log_message('debug', "newinvestment param exist?:".array_key_exists('newinvestment',$_REQUEST));
	
		if (array_key_exists('newinvestment',$_REQUEST)) {	
			$bank = $this->supplier_model->supplierIdExists($_REQUEST['newinvestment']['supplier_id']);
			$transaction_code = $this->transactioncode_model->transactionCodeExists($_REQUEST['newinvestment']['transaction_code'], 'IN');
			if ($bank==0 && $transaction_code==0){
				echo "{'success':false,'msg':'Investment type and Supplier do not exist.'}";
			}
			else if ($bank==0 && $transaction_code!=0){
				echo "{'success':false,'msg':'Supplier does not exist.'}";
			}
			else if ($bank!=0 && $transaction_code==0){
				echo "{'success':false,'msg':'Investment type does not exist.'}";
			}
			else {
				if (array_key_exists('maturity_date',$_REQUEST['newinvestment'])){
					$_REQUEST['newinvestment']['maturity_date'] = date("Ymd", strtotime($_REQUEST['newinvestment']['maturity_date']));
				} 
				
				if (array_key_exists('placement_date',$_REQUEST['newinvestment'])){
					$_REQUEST['newinvestment']['placement_date'] = date("Ymd", strtotime($_REQUEST['newinvestment']['placement_date']));
				} 
				
				$this->tinvestmentheader_model->populate($_REQUEST['newinvestment']);
				$this->tinvestmentheader_model->setValue('status_flag', '1');
				$result = $this->tinvestmentheader_model->update(
													array('investment_no' => $_REQUEST['newinvestment']['investment_no']
														 ,'status_flag' => '1'
													)
											   );
				if($result['affected_rows'] <= 0 && !empty($result['error_message'])){
					echo '{"success":false,"msg":"'.$result['error_message'].'"}';
				} else {
					echo "{'success':true,'msg':'Data successfully saved.'}";
				}								   
			}								 
		}
		else {
			echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
		}	
		
		log_message('debug', "[END] Controller investment:update");
				  			  
	}
	
	/**
	 * @desc Updates status of investment to '0'
	 */
	function delete()
	{
		log_message('debug', "[START] Controller investment:delete");
		log_message('debug', "newinvestment param exist?:".array_key_exists('newinvestment',$_REQUEST));
				  
		if (array_key_exists('newinvestment',$_REQUEST)) {	
			$this->tinvestmentheader_model->setValue('status_flag', '0');
			$this->tinvestmentheader_model->setValue('modified_by', $_REQUEST['newinvestment']['modified_by']);
			$result = $this->tinvestmentheader_model->update(
													array('investment_no' => $_REQUEST['newinvestment']['investment_no']
														 ,'status_flag' => '1'
													)
											   );
											   
			if($result['affected_rows'] <= 0){
				echo '{"success":false,"msg":"'.$result['error_message'].'"}';
			} else {
				echo "{'success':true,'msg':'Data successfully deleted.'}";
			}									 
		}
		else {
			echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
		}	
		
		log_message('debug', "[END] Controller investment:delete");
	}
	
	/**
	 * @desc To retrieve list of all investment type
	 * @return array
	 */
	function readInvestmentTypeList()
	{
		$data = $this->transactioncode_model->get_list(
					array('transaction_group'=>'IN', 'status_flag'=>'1'),
					array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
					array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
					array('transaction_code'
							,'transaction_description')
				);
	
			echo json_encode(array(
				'success' => true,
				'data' => $data['list'],
				'total' => $data['count'],
				'query' => $data['query']
				));
	}
	
	/**
	 * @desc To get a specific parameter value
	 * @param Parameter id
	 * @return string (parameter value)
	 */
	function getParam($param_id){
		$data = $this->parameter_model->get(array('parameter_id' => $param_id)
			,array('parameter_value')
		);
	
		return $data['list'][0]['parameter_value'];
	}	
	
	/**
	 * @desc Update the parameter value of a specific id
	 * @param Parameter id
	 * @return string (parameter value)
	 */
	function updateParam($param_id, $value)
	{	
		$_REQUEST['parameter']['parameter_value'] = $value;
		$this->parameter_model->populate($_REQUEST['parameter']);
		$this->parameter_model->setValue('parameter_value', $_REQUEST['parameter']['parameter_value']);
		$this->parameter_model->update(
									array('parameter_id'=>$param_id)
								);	
	}

	function preview() {
		/*$_REQUEST = array('transaction_code' => 'IUBP'
						,'bank' => 'BDO'
						,'remarks' => 'Remarks'
						,'investment_amount' => 1000
						);*/
						
		$transaction_code = $_REQUEST['transaction_code'];
		$result = $this->glentrydetail_model->retrieveGLEntryDetails($transaction_code);
		$temp_arr = $result['list'];
		
		//put all debit first in array before credit.
		$sorted_arr = array();
		foreach($temp_arr as $val){
			if($val['debit_credit']== "D"){
				$sorted_arr[] = $val;
			}
		}
		
		foreach($temp_arr as $val){
			if($val['debit_credit']== "C"){
				$sorted_arr[] = $val;
			}
		}
		
		$data['list'] = $sorted_arr;
		 set_time_limit(0);
		 $filename = "InvestmentPreview_".date("YmdHis");		
		 $this->load->plugin('to_pdf'); 
		 $curr_date = $this->parameter_model->getParam("CURRDATE");
		 $data['date'] = date('F d, Y', strtotime($curr_date)); 
		 $data['pay_to'] = $_REQUEST['bank'];
		 $data['remarks'] = $_REQUEST['remarks']; 
		 $data['investment_amount'] = $_REQUEST['investment_amount']; 
		 $html = $this->load->view('forms/investmentPreview', $data, true);
		 pdf_create($html, $filename);	
	}	
	
}

?>















	