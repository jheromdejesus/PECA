<?php
/*
 * Created on Apr 22, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Onlinememberrequestheader_model extends Asi_Model {

    function Onlinememberrequestheader_model()
    {
        parent::Asi_Model();
        $this->table_name = 'o_member_request_header';
        $this->id = 'request_no';
        $this->date_columns = array('member_date','hire_date','work_date','birth_date');
    }
    
	/**
	 * @desc Retrieves online requests of members
	 * @return array
	 */
    function retrieveOnlineMemberRequests($employee_id=null,$last_name=null,$first_name=null,$from_date=null,$to_date=null, $status =1, $limit = null, $offset = null, $select = null, $orderby=null) 
  	{
    	if($select)
    		$this->db->select($select);
    	/*$this->db->from('o_member_request_header oh');
		$this->db->from('o_workflow oa');
		$this->db->join('m_employee me', 'me.employee_id=oh.employee_id', 'LEFT OUTER');
		
		$status_flag = '0';
		$filter['oh.status_flag >'] = $status_flag; 
		if($employee_id)	
			$filter['me.employee_id'] = $employee_id;
		$request_type = 'M';
		if($request_type)	
			$filter['oa.request_type'] = $request_type;
		if($last_name)	
			$filter['me.last_name'] = $last_name;
		if($first_name)	
			$filter['me.first_name'] = $first_name;
		if($to_date)	
			$filter['oh.created_date <='] = $to_date;
		if($from_date)	
			$filter['oh.created_date >='] = $from_date;
		
		
		if($filter)	
			$this->db->where($filter); 	*/
		$request_type = 'MEMB';	
		$this->db->from('o_member_request_header oh');
    	$this->db->join('m_employee me', 'me.employee_id=oh.employee_id', 'left outer');
    	$this->db->from('o_workflow oa');
		//$this->db->join('o_workflow oa', 'oa.request_type=MEMB', 'inner');
		//$this->db->join('r_user AS ru1', 'ru1.user_name=oa.approver1', 'LEFT');
    	//$this->db->join('r_user AS ru2', 'ru2.user_name=oa.approver2', 'LEFT');
    	//$this->db->join('r_user AS ru3', 'ru3.user_name=oa.approver3', 'LEFT');
    	//$this->db->join('r_user AS ru4', 'ru4.user_name=oa.approver4', 'LEFT');
    	//$this->db->join('r_user AS ru5', 'ru5.user_name=oa.approver5', 'LEFT');
		if ($request_type)
			$this->db->where('oa.request_type =\''.$request_type.'\'');
		if ($last_name)
			$this->db->where('me.last_name LIKE \''.$last_name.'%\'');
		if ($first_name)
			$this->db->where('me.first_name LIKE \''.$first_name.'%\'');
		if ($employee_id)
			$this->db->where('oh.employee_id LIKE \''.$employee_id.'%\'');
		if ($status == 0)
			$this->db->where('oh.status_flag != 0 AND oh.status_flag != 9');
		else if($status == 3)
			$this->db->where('oh.status_flag >= 3 AND oh.status_flag < 9');
		else
			$this->db->where('oh.status_flag =\''.$status.'\'');
		if ($from_date && $to_date)
			$this->db->where('oh.created_date BETWEEN \''.$from_date.'\' AND \''.$to_date.'\'');
		
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
	 * @desc Retrieves list of requests of an employee
	 * @return array
	 */
	function retrieveListOfRequests($filter=null, $limit = null, $offset = null, $select = null, $orderby=null)
	{
		if($select)
    		$this->db->select($select);
    	$this->db->from($this->table_name);
		if($filter)	
			$this->db->where($filter); 	
		if($orderby)
			$this->db->orderby($orderby);
		$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query(); 
    	
		$count = $result->num_rows();
		//$count = $this->db->count_all($this->table_name);
		return $this->checkError($result->result_array(), $count, $query);    
	}
	
	/**
	 * @desc Retrieves list of members to approve
	 * @return array
	 */
    function retrieveRequestsToApprove($limit = null, $offset = null, $select = null, $orderby = null) 
  	{
    	if($select)
    		$this->db->select($select);
    	$this->db->from($this->table_name);
    	$filter = array('status_flag' => '3');
		$this->db->where($filter);
    	if($orderby)	
    		$this->db->orderby($orderby);	
    	$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query(); 
    	
		//$count = $result->num_rows();
		$count = $this->db->count_all($this->table_name);
		return $this->checkError($result->result_array(), $count, $query);    
	}
	
}

?>
