<?php

class Company extends Asi_Controller {

	function Company()
	{
		parent::Asi_Controller();
		$this->load->model('company_model');
		$this->load->model('employee_model');
	}
	
	function index()
	{	
	}
	
	function show()
	{   	
		if (array_key_exists('company',$_REQUEST))
		{	
			$this->company_model->populate($_REQUEST['company']);
			$this->company_model->setValue('status_flag','1');
		}
		$data = $this->company_model->get(null,
			array('company_code','company_name'));
		
		echo json_encode(array(
			'success' => true,
            'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
        ));
	}
	
	function read()
	{	
	    $data = $this->company_model->get_list(
		array('status_flag' => '1'),
		array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
		array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
		array('company_code', 'company_name'),
		'company_code ASC'
		);
		
		echo json_encode(array(
			'success' => true,
            'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
        ));
	}
	
	function delete()
	{		
		log_message('debug', "[START] Controller company:delete");
		log_message('debug', "company param exist?:".array_key_exists('company',$_REQUEST));
		if (array_key_exists('company',$_REQUEST)) {
			$table_used = $this->checkIfUsed($_REQUEST['company']['company_code']);
			
			if ($table_used){
				echo "{'success':false,'msg':'Cannot delete Company \"" . $_REQUEST['company']['company_code'] . " - " . $_REQUEST['company']['company_name'] . "\" because it is being used in $table_used.'}";
			} else {
				$this->company_model->populate($_REQUEST['company']);
				$this->company_model->setValue('status_flag', '0');
				$result = $this->company_model->update();
				if($result['affected_rows'] <= 0){
					echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
				} else
					echo "{'success':true,'msg':'Data successfully deleted.'}";
			}
		} else
			echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
			
			log_message('debug', "[END] Controller company:delete");
	}

	function checkIfUsed($company_code){
		$data = $this->employee_model->get_list(
			array('status_flag' => '1'
					,'company_code' => $company_code)
			, '1'
			, null
			, array('company_code')
			, null
		);
		
		if(count($data['list']) > 0){
			return "Membership";
		}
		return false;
	}
	
	
	function update()
	{	
		log_message('debug', "[START] Controller company:update");
		log_message('debug', "company param exist?:".array_key_exists('company',$_REQUEST));
		
		if (array_key_exists('company',$_REQUEST)) {
			$this->company_model->populate($_REQUEST['company']);
			$this->company_model->setValue('status_flag', '1');
			$result = $this->company_model->update();
			if($result['affected_rows'] <= 0){
				echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
			} else
				echo "{'success':true,'msg':'Data successfully saved.'}";
		} else
			echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
			
		log_message('debug', "[END] Controller company");
	}
	
	function add()
	{
		log_message('debug', "[START] Controller company:add");
		log_message('debug', "company param exist?:".array_key_exists('company',$_REQUEST));
		
		if (array_key_exists('company',$_REQUEST)) {
			$this->company_model->populate($_REQUEST['company']);
			$this->company_model->setValue('status_flag', '1');
			
			$checkDuplicate = $this->company_model->checkDuplicateKeyEntry();
	
			if($checkDuplicate['error_code'] == 1){
				$result['error_code'] = 1;
				$result['error_message'] = $checkDuplicate['error_message'];
			}
			else{
				$result = $this->company_model->insert();
			} 	
			
			if($result['error_code'] == 0){  
			  echo "{'success':true,'msg':'Data successfully saved.'}";
	        } else
			  echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';
		} else
			echo "{'success':false,'msg':'Data was NOT successfully saved.','error_code':'2'}";
			
		log_message('debug', "[END] Controller company:add");
	}
}

/* End of file company.php */
/* Location: ./PECA/application/controllers/company.php */