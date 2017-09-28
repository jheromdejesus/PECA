<?php

/* Location: ./CodeIgniter/application/controllers/investment.php */



class Investment_maturity extends Asi_Controller {
	function Investment_maturity()
	{
		parent::Asi_Controller();
		$this->load->model('minvestmentheader_model');
		$this->load->model('transactioncode_model');
		$this->load->model('parameter_model');
		$this->load->helper('url');
		$this->load->helper('date');
		$this->load->library('constants');
	}
	
	function index() {
		
	}
	
	/**
	 * @desc To retrieve investments that have not yet matured
	 * @return array
	 */
	function read()
	{
		$params = array('i.action_code' => NULL,'i.status_flag'	=> '2');	

		if(array_key_exists('investment_no', $_REQUEST) && $_REQUEST['investment_no']!=""){
			$params['i.investment_no LIKE'] = $_REQUEST['investment_no']."%";
		}	
		$data = $this->minvestmentheader_model->getInvestmentMaturityList(
			$params,
			array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
			array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
			array('i.investment_no'
					,'m.supplier_name'				
					,'i.placement_date'	
					,'i.maturity_date'				
					,'i.investment_amount')
			,'i.investment_no'
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
	 * @desc To retrieve single investment information
	 * @return array
	 */
	function show()
	{
		$_REQUEST['filter'] = array ('i.action_code' => NULL	
								   ,'i.investment_no' => $_REQUEST['newinvestment']['investment_no']
								   ,'i.status_flag' => '2'
							  );  
							  		  
		$data = $this->minvestmentheader_model->getInvestmentMaturity(
					array_key_exists('filter',$_REQUEST) ? $_REQUEST['filter'] : null,
					array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
					array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
					array('i.investment_no'
							,'i.supplier_id'
							,'m.supplier_name'	
							,'rt.transaction_description'		
							,'i.placement_date'
							,'i.investment_amount'
							,'i.interest_rate'
							,'i.maturity_date'
							,'i.maturity_code'
							,'i.or_no'
							,'i.or_date'
							,'i.interest_amount'
							,'i.interest_income'
							,'i.transaction_code')//buot-buot
				);
	
		foreach($data['list'] as $row => $value){
			$data['list'][$row]['placement_date'] = date("mdY", strtotime($value['placement_date']));
			$data['list'][$row]['maturity_date'] = date("mdY", strtotime($value['maturity_date']));
			$data['list'][$row]['or_date'] = date("mdY", (empty($value['or_date'])?time():strtotime(trim($value['or_date']))));
		}
				
		echo json_encode(array(
				'success' => true,
				'data' => $data['list'],
				'total' => $data['count'],
				'query' => $data['query']
				));
			
	}
	
	/**
	 * @desc Updates data of investment record
	 */
	function update()
	{
		log_message('debug', "[START] Controller investment_maturity:update");
		log_message('debug', "newinvestment param exist?:".array_key_exists('newinvestment',$_REQUEST));
		
		if (array_key_exists('newinvestment',$_REQUEST)) {	
			$transaction_code = $this->transactioncode_model->transactionCodeExists($_REQUEST['newinvestment']['maturity_code'], 'II');	
			if ($transaction_code==0){
				echo "{'success':false,'msg':'Maturity code does not exist.'}";
			}
			else {
				if (array_key_exists('maturity_date',$_REQUEST['newinvestment'])){
				$_REQUEST['newinvestment']['maturity_date'] = date("Ymd", strtotime($_REQUEST['newinvestment']['maturity_date']));
				} 
				if (array_key_exists('placement_date',$_REQUEST['newinvestment'])){
					$_REQUEST['newinvestment']['placement_date'] = date("Ymd", strtotime($_REQUEST['newinvestment']['placement_date']));
				} 
				if (array_key_exists('or_date',$_REQUEST['newinvestment'])){
					$_REQUEST['newinvestment']['or_date'] = date("Ymd", strtotime($_REQUEST['newinvestment']['or_date']));
				}
				if (array_key_exists('rollover_placement_date',$_REQUEST['newinvestment'])){
					$_REQUEST['newinvestment']['rollover_placement_date'] = date("Ymd", strtotime($_REQUEST['newinvestment']['rollover_placement_date']));
				}
				if (array_key_exists('rollover_maturity_date',$_REQUEST['newinvestment'])){
					$_REQUEST['newinvestment']['rollover_maturity_date'] = date("Ymd", strtotime($_REQUEST['newinvestment']['rollover_maturity_date']));
				}
				
				$current_date = date("Ymd", strtotime($this->getParam('CURRDATE')));			
				$data = $this->minvestmentheader_model->get_list(array('investment_no'=>$_REQUEST['newinvestment']['investment_no'])
					,'investment_amount');
				$investment_amount = $data['list'][0]['investment_amount'];
				
				unset($_REQUEST['newinvestment']['or_no']);
				unset($_REQUEST['newinvestment']['or_date']);
				$this->minvestmentheader_model->populate($_REQUEST['newinvestment']);
				
				if ($_REQUEST['newinvestment']['action_code']=='W'){
					$this->minvestmentheader_model->setValue('action_code', 'W');
					$this->minvestmentheader_model->setValue('principal_amount', $investment_amount);
					unset($_REQUEST['newinvestment']['rollover_placement_date']);
					$this->minvestmentheader_model->setValue('rollover_placement_days', '0.00');
					$this->minvestmentheader_model->setValue('rollover_interest_rate', '0.00');
					$this->minvestmentheader_model->setValue('rollover_interest_amount', '0.00');
					unset($_REQUEST['newinvestment']['rollover_maturity_date']);
				}
				else if ($_REQUEST['newinvestment']['action_code']=='P'){
					$this->minvestmentheader_model->setValue('action_code', 'P');
					$this->minvestmentheader_model->setValue('principal_amount', 0);			
				}
				$this->minvestmentheader_model->setValue('status_flag', '2');
				$this->minvestmentheader_model->setValue('transaction_date', $current_date);
				$this->minvestmentheader_model->setValue('accrual', 0);
				
				$result = $this->minvestmentheader_model->update(
														array('investment_no' => $_REQUEST['newinvestment']['investment_no']
															 ,'status_flag' => '2'
														));										   
				if($result['affected_rows'] <= 0){
					echo '{"success":false,"msg":"'.$result['error_message'].'"}';
				} else {
					echo "{'success':true,'msg':'Data successfully saved.'}";
				}
			}											 
		}
		else {
			echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
		}	
		
		log_message('debug', "[END] Controller investment_maturity:update");		  			  
	}
	
	/**
	 * @desc To retrieve investments that have not yet matured
	 * @return array
	 */
	function readMaturityCodeList()
	{
		$data = $this->transactioncode_model->get_list(
			array('transaction_group'=>'II', 'status_flag'=>'1'),
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

}

?>















	