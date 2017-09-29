<?php
/*
 * Created on Apr 22, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Onlinecapitaltransactionheader_model extends Asi_Model {

    function Onlinecapitaltransactionheader_model()
    {
        parent::Asi_Model();
        $this->table_name = 'o_capital_transaction_header';
        $this->id = 'request_no';
		$this->date_columns = array('transaction_date');
    }
    
  	/**
	 * @desc Searches a transaction
	 * @return array
	 */
    function searchTransaction($transaction_date = null, $status_flag = null, $limit = null, $offset = null, $select = null) 
  	{
    	if($select)
    		$this->db->select($select);
    	$this->db->from(' o_capital_transaction_header th');
		$this->db->join('r_transaction rt', 'rt.transaction_code = th.transaction_code', 'INNER');
    	$this->db->where('rt.status_flag != 0');
		if($transaction_date)
			$filter['transaction_date LIKE'] = '%'.$transaction_date.'%';
		if($status_flag)
			$filter['status_flag LIKE'] = '%'.$status_flag.'%';
		$this->db->where($filter);
    	$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query(); 
    	
		$count = $result->num_rows();
	
		return $this->checkError($result->result_array(), $count, $query);    
	}

	/**
	 * @desc Previews a transaction
	 * @return array
	 */
    function previewOnlineForm($filter = null, $limit = null, $offset = null, $select = null) 
  	{
    	if($select)
    		$this->db->select($select);
    	$this->db->from('o_capital_transaction_header tc');
		$this->db->join('m_employee me', 'me.employee_id = tc.employee_id', 'INNER');
    	
		if($filter)	
			$this->db->where($filter);
    	$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query(); 
    	
		$count = $result->num_rows();
	
		return $this->checkError($result->result_array(), $count, $query);    
	}

	/**
	 * @desc Retrieves a single online transaction
	 * @return array
	 */
    function retrieveOnlineCapTransactionHeader($filter = null, $limit = null, $offset = null, $select = null, $orderby = null) 
  	{
    	if($select)
    		$this->db->select($select);
    	$this->db->from('o_capital_transaction_header tc');
		$this->db->join('r_transaction rt', 'rt.transaction_code = tc.transaction_code', 'INNER');
		$this->db->join('m_employee me', 'me.employee_id=tc.employee_id', 'inner');
    	$this->db->join('o_workflow ow', 'ow.request_type=tc.transaction_code', 'inner');
		$this->db->join('r_user AS ru1', 'ru1.user_name=ow.approver1', 'LEFT');
    	$this->db->join('r_user AS ru2', 'ru2.user_name=ow.approver2', 'LEFT');
    	$this->db->join('r_user AS ru3', 'ru3.user_name=ow.approver3', 'LEFT');
    	$this->db->join('r_user AS ru4', 'ru4.user_name=ow.approver4', 'LEFT');
    	$this->db->join('r_user AS ru5', 'ru5.user_name=ow.approver5', 'LEFT');
    	$this->db->where('rt.status_flag != 0');
		if($filter)	
			$this->db->where($filter);
			
		$clone_db = clone $this->db;
		$count = $clone_db->count_all_results();	
			
    	if($orderby)
			$this->db->order_by($orderby);
		$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query(); 
		//$count = $result->num_rows();
		return $this->checkError($result->result_array(), $count, $query);    
	}
	
 	/**
	 * @desc Retrieves a single online transaction
	 * @return array
	 */
    function retrieveAllOnlineCapTransactions(/*$filter = null,*/$transaction_code = null,$submission_date_from = null,
    			$submission_date_to = null, $last_name =null, $first_name =null,
    			$empId =null, $status =1,$orNo =1, $limit = null, $offset = null, $select = null, $orderby = null) 
  	{
    	if($select)
    		$this->db->select($select);
    	$this->db->from('o_capital_transaction_header tc');
    	$this->db->join('m_employee me', 'me.employee_id=tc.employee_id', 'inner');
		$this->db->join('r_transaction rt', 'rt.transaction_code = tc.transaction_code', 'INNER');
    	$this->db->join('o_workflow ow', 'ow.request_type=tc.transaction_code', 'inner');
		$this->db->join('r_user AS ru1', 'ru1.user_name=ow.approver1', 'LEFT');
    	$this->db->join('r_user AS ru2', 'ru2.user_name=ow.approver2', 'LEFT');
    	$this->db->join('r_user AS ru3', 'ru3.user_name=ow.approver3', 'LEFT');
    	$this->db->join('r_user AS ru4', 'ru4.user_name=ow.approver4', 'LEFT');
    	$this->db->join('r_user AS ru5', 'ru5.user_name=ow.approver5', 'LEFT');
		
		$this->db->where('rt.status_flag != 0');
		if ($transaction_code)
			$this->db->where('tc.transaction_code =\''.$transaction_code.'\'');
		if ($last_name)
			$this->db->where('me.last_name LIKE \''.$last_name.'%\'');
		if ($first_name)
			$this->db->where('me.first_name LIKE \''.$first_name.'%\'');
		if ($empId)
			$this->db->where('tc.employee_id LIKE \''.$empId.'%\'');
		if ($orNo)
			$this->db->where('tc.or_no =\''.$orNo.'\'');
		if ($status == 0)
			$this->db->where('tc.status_flag != 0 AND tc.status_flag != 9');
		else if($status == 3)
			$this->db->where('tc.status_flag >= 3 AND tc.status_flag < 9');
		else
			$this->db->where('tc.status_flag =\''.$status.'\'');
		if ($submission_date_from && $submission_date_to)
			$this->db->where('tc.transaction_date BETWEEN \''.$submission_date_from.'\' AND \''.$submission_date_to.'\'');
		//$this->db->where('ol.status_flag != 0');
				
		$clone_db = clone $this->db;
		$count = $clone_db->count_all_results();	
			
    	if($orderby)
			$this->db->orderby($orderby);
		$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query(); 
		//$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);    
	}
	
	/**
	 * @desc Retrieves from Transaction Codes.  Shows Withrawal and Direct Deposits (Savings) only.
	 */
    function getTransactionTypes() 
  	{
    	$sql = "SELECT transaction_code, transaction_description FROM r_transaction WHERE transaction_code IN ('DDEP', 'WDWL')";
    	$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
		return $this->checkError($result->result_array(), $count, $query);    
	}
	function getTopicAttachments()
	{
		$_REQUEST['bulletin']['topic_id'] = $_REQUEST['topic_id'];
		
		$data = $this->OAttachment_model->getTopicAttachments();
		
		echo json_encode(array(
			'success' => true,
            'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query'],
        ));
	}	
}

?>
