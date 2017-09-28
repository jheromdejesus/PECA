<?php

class Information_code extends Asi_controller {

	function Information_code()
	{
		parent::Asi_controller();
		$this->load->model('information_model');
		$this->load->model('supplierinfo_model');
	}
	
	function index()
	{
	}
	
	function show()
	{
		
		if (array_key_exists('infocode',$_REQUEST)) 
		{	
			$this->information_model->populate($_REQUEST['infocode']);
			$this->information_model->setValue('status_flag', '1');
		}
		
		$data = $this->information_model->get(null,
			array('information_code','information_description')
		);
		
		echo json_encode(array(
			'success' => true,
            'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query']
        ));
	}
	
	function read()
	{			 
	    $data = $this->information_model->get_list(
			array('status_flag' => '1'),
			array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
			array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
			array('information_code','information_description'),
			'information_description ASC'
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
		log_message('debug', "[START] Controller information_code:delete");
		log_message('debug', "information_code param exist?:".array_key_exists('information_code',$_REQUEST));

		if (array_key_exists('infocode',$_REQUEST)) {
			$table_used = $this->checkIfUsed($_REQUEST['infocode']['information_code']);
			if ($table_used){
				echo "{'success':false,'msg':'Cannot delete Information \"" . $_REQUEST['infocode']['information_code'] . " - " . $_REQUEST['infocode']['information_description'] . "\" because it is being used in $table_used.'}";
			} else {
				$this->information_model->populate($_REQUEST['infocode']);
				$this->information_model->setValue('status_flag', '0');
				$result = $this->information_model->update();
				if($result['affected_rows'] <= 0){
					echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
				} else
					echo "{'success':true,'msg':'Data successfully deleted.'}";
			}
		} else
			echo "{'success':false,'msg':'Data was NOT successfully deleted.'}";
		log_message('debug', "[END] Controller information_code:delete");
	}

	function checkIfUsed($information_code){
		$data = $this->supplierinfo_model->get_list(
			array('status_flag' => '1'
					,'info_code' => $information_code)
			, '1'
			, null
			, array('supplier_id')
			, null
		);
		if(count($data['list']) > 0){
			return "Third-Party Suppliers";
		}
		return false;
	}
	
	function update()
	{	
		log_message('debug', "[START] Controller information_code:update");
		log_message('debug', "information_code param exist?:".array_key_exists('information_code',$_REQUEST));
		
		if (array_key_exists('infocode',$_REQUEST)) {
			$this->information_model->populate($_REQUEST['infocode']);
			$this->information_model->setValue('status_flag', '1');
			$result = $this->information_model->update();
			if($result['affected_rows'] <= 0){
				echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
			} else
				echo "{'success':true,'msg':'Data successfully saved.'}";
		} else
			echo "{'success':false,'msg':'Data was NOT successfully saved.'}";
			
		log_message('debug', "[END] Controller information_code:update");
	}
	
	function add()
	{
		log_message('debug', "[START] Controller information_model:add");
		log_message('debug', "information_code param exist?:".array_key_exists('information_code',$_REQUEST));
		
		if (array_key_exists('infocode',$_REQUEST)) {
			$this->information_model->populate($_REQUEST['infocode']);
			$this->information_model->setValue('status_flag', '1');
			
			$checkDuplicate = $this->information_model->checkDuplicateKeyEntry();
	
			if($checkDuplicate['error_code'] == 1){
				$result['error_code'] = 1;
				$result['error_message'] = $checkDuplicate['error_message'];
			}
			else{
				$result = $this->information_model->insert();
			} 
			
			if($result['error_code'] == 0){  
			  echo "{'success':true,'msg':'Data successfully saved.'}";
	        } else
			  echo '{"success":false,"msg":"'.$result['error_message'].'","error_code":"'.$result['error_code'].'"}';
		} else
			echo "{'success':false,'msg':'Data was NOT successfully saved.','error_code':'2'}";
			
		log_message('debug', "[END] Controller information_code:add");
	}
}

/* End of file information_model.php */
/* Location: ./PECA/application/controllers/information_model.php */