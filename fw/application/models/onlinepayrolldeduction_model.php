<?php
/*
 * Created on Apr 26, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class OnlinePayrolldeduction_model extends Asi_Model {
	
    function OnlinePayrolldeduction_model()
    {
        parent::Asi_Model();
        $this->table_name = 'o_payroll_deduction';
        $this->id = 'request_no';
        $this->date_columns = array('transaction_period');
    }
    
	function getPayrollDeductionList($params = null,$date_from = null,
    			$date_to = null, $last_name =null, $first_name =null,
    			$empId =null, $status =1
				,$limit = null, $offset = null, $select = null, $orderby = null) 
	{
		if($select)
    		$this->db->select($select);
    	$this->db->from('o_payroll_deduction op');
    	$this->db->from('o_workflow ow');
    	$this->db->join('m_employee AS me', 'me.employee_id = op.employee_id', 'LEFT');
    	$this->db->join('r_user AS ru1', 'ru1.user_name=ow.approver1', 'LEFT');
    	$this->db->join('r_user AS ru2', 'ru2.user_name=ow.approver2', 'LEFT');
    	$this->db->join('r_user AS ru3', 'ru3.user_name=ow.approver3', 'LEFT');
    	$this->db->join('r_user AS ru4', 'ru4.user_name=ow.approver4', 'LEFT');
    	$this->db->join('r_user AS ru5', 'ru5.user_name=ow.approver5', 'LEFT');
    	
    	$this->db->where($params);
    	if ($last_name)
			$this->db->where('me.last_name LIKE \''.$last_name.'%\'');
		if ($first_name)
			$this->db->where('me.first_name LIKE \''.$first_name.'%\'');
		if ($empId)
			$this->db->where('op.employee_id LIKE \''.$empId.'%\'');
		if ($status == 0)
			$this->db->where('op.status_flag != 0 AND op.status_flag != 9');
		else if($status == 3)
			$this->db->where('op.status_flag BETWEEN 3 AND 8');
		else
			$this->db->where('op.status_flag =\''.$status.'\'');
		if ($date_from && $date_to)
			$this->db->where('op.created_date BETWEEN \''.$date_from.'\' AND \''.$date_to.'\'');
			
    	if($orderby)	
    		$this->db->order_by($orderby);	
    	$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
    	
    	if($params){
    		$this->db->where($params);
    		if ($last_name)
				$this->db->where('me.last_name =\''.$last_name.'\'');
			if ($first_name)
				$this->db->where('me.first_name =\''.$first_name.'\'');
			if ($empId)
				$this->db->where('op.employee_id =\''.$empId.'\'');
			if ($status == 0)
				$this->db->where('op.status_flag != 0 AND op.status_flag != 9');
			else if($status == 3)
				$this->db->where('op.status_flag BETWEEN 3 AND 8');
			else
				$this->db->where('op.status_flag =\''.$status.'\'');
			if ($date_from && $date_to)
				$this->db->where('op.created_date BETWEEN \''.$date_from.'\' AND \''.$date_to.'\'');
			$this->db->from('o_payroll_deduction op');
	    	$this->db->from('o_workflow ow');
	    	$this->db->join('m_employee AS me', 'me.employee_id = op.employee_id', 'LEFT');
	    	$this->db->join('r_user AS ru1', 'ru1.user_name=ow.approver1', 'LEFT');
	    	$this->db->join('r_user AS ru2', 'ru2.user_name=ow.approver2', 'LEFT');
	    	$this->db->join('r_user AS ru3', 'ru3.user_name=ow.approver3', 'LEFT');
	    	$this->db->join('r_user AS ru4', 'ru4.user_name=ow.approver4', 'LEFT');
	    	$this->db->join('r_user AS ru5', 'ru5.user_name=ow.approver5', 'LEFT');
	    	$count = $this->db->count_all_results();
    	}else
    		$count = $this->db->count_all($this->table_name);
  		return $this->checkError($result->result_array(), $count, $query);
    }
    
 	function getPayrollDeduction($filter = null, $limit = null, $offset = null, $select = null, $orderby = null) 
	{
		if($select)
    		$this->db->select($select);
    	$this->db->from('o_payroll_deduction AS pd');
		$this->db->from('o_workflow ow');
		
		$this->db->join('r_user AS ru1', 'ru1.user_name=ow.approver1', 'LEFT');
    	$this->db->join('r_user AS ru2', 'ru2.user_name=ow.approver2', 'LEFT');
    	$this->db->join('r_user AS ru3', 'ru3.user_name=ow.approver3', 'LEFT');
    	$this->db->join('r_user AS ru4', 'ru4.user_name=ow.approver4', 'LEFT');
    	$this->db->join('r_user AS ru5', 'ru5.user_name=ow.approver5', 'LEFT');
		
    	$this->db->join('m_employee AS me ', 'pd.employee_id = me.employee_id', 'left outer');
    	$this->db->join('r_transaction rt', 'rt.transaction_code=pd.transaction_code', 'left outer');
    	if($filter)	
    		$this->db->where($filter);
    	if($orderby)	
    		$this->db->order_by($orderby);	
    	$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
    	//echo $query;
    	$count = $this->db->count_all($this->table_name);
  		return $this->checkError($result->result_array(), $count, $query);
    }
    
	function getPDEmployeeList($filter = null, $limit = null, $offset = null, $select = null, $orderby = null) 
	{
		if($select)
    		$this->db->select($select);
    	$this->db->from('m_employee AS me');
    	$this->db->join('o_payroll_deduction pd', 'me.employee_id = pd.employee_id', 'left outer');
    	if($filter)	
    		$this->db->where($filter);
    	if($orderby)	
    		$this->db->order_by($orderby);	
    	$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
    	
    	if($filter)	{
    		$this->db->where($filter);
    		$this->db->from('m_employee AS me');
    		$this->db->join('o_payroll_deduction pd', 'me.employee_id = pd.employee_id', 'left outer');
    		$count = $this->db->count_all_results();
    	}else
    		$count = $this->db->count_all($this->table_name);
  		return $this->checkError($result->result_array(), $count, $query);
    }
    
}

?>
