<?php

class Member_model extends Asi_Model {
	
	var $table_name = 'm_employee';
	var $id = 'employee_id';
	var $model_data = null;
	var $date_columns = array('member_date', 'hire_date', 'work_date', 'birth_date');
	
    function Member_model() {
        parent::Asi_Model();
        $this->load->model('parameter_model');
    }
    function checkGuarantors($filter = null, $limit = null, $offset = null, $select = null, $orderby = null){
		$this->db->distinct();
		if($select)
			$this->db->select($select);
		$this->db->from('m_loan ml');
		$this->db->join('m_loan_guarantor lg', 'lg.loan_no = ml.loan_no', 'left outer');

		$this->db->join('m_employee me', 'me.employee_id = lg.guarantor_id', 'left outer');
		if($orderby)
			$this->db->order_by($orderby);
		if ($filter)
			$this->db->where($filter);
		$this->db->limit($offset, $limit);
		$result = $this->db->get();
		
		$query = $this->db->last_query();
			
		$is_valid = 0; 
		
		if($result->num_rows() > 0){
			   foreach ($result->result() as $row){
					if(in_array($row->company_code,array('910', '920'))||$row->member_status=="I"){
						if($row->non_member=='N'){
							return $is_valid = 1;
						}
					}
			   }
		}
		return  $is_valid;	
	}
	
	function readEmployee($empId=null, $empFname=null, $empLname=null, $offset=null, $limit=null){
		$this->db->select('employee_id,last_name,first_name');
    	$this->db->from($this->table_name);
 
		if($empId)
			$filter['employee_id LIKE'] = $empId.'%';
		if($empFname)
			$filter['first_name LIKE'] = $empFname.'%';
		if($empLname)
			$filter['last_name LIKE'] = $empLname.'%';
			
		$filter['member_status'] = 'A';
		$filter['status_flag'] = '1';
		$this->db->where($filter);
		$this->db->order_by('employee_id ASC');
		$this->db->limit($limit, $offset);
		$result = $this->db->get();
			
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
	}
	
	/**
	 * @desc Retrieves beneficiaries of employees
	 * @return array
	 */
	function retrieveEmployeeBeneficiaries($filter=null, $limit = null, $offset = null, $select = null, $orderby=null)
	{
		if($select)
    		$this->db->select($select);
    	$this->db->from('m_beneficiary');
		if($filter)	
			$this->db->where($filter); 	
		if($orderby)
			$this->db->orderby($orderby);
		$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query(); 
    	
		if($filter){
			$this->db->where($filter);
			$this->db->from('m_beneficiary');
			$count = $this->db->count_all_results();
		}else{
			$count = $this->db->count_all('m_beneficiary');
		}
	
		return $this->checkError($result->result_array(), $count, $query);    
	}
	
	function employeeExists($empId){
		$data = $this->get(array(
			'employee_id' => $empId
			), array('COUNT(*) as count'));
				
		 if($data['list'][0]['count'] == 0) 
		 	return 0;
		 else return 1;
	}
	
	function getEmpYearsOfService($employee_id)
	{
		$current_date = date("YmdHis", strtotime($this->parameter_model->getParam('CURRDATE')));	
		
		$data = $this->get(array(
			'employee_id' => $employee_id)
		,'hire_date'											
		);
							
		$hire_date = $data['list'][0]['hire_date'];
		
		$years_of_service = $this->dateDiff($hire_date, $current_date);
	
		return $years_of_service;
	}
	
	function dateDiff($start, $end)
	{	
		$date_diff = date("Y", strtotime($end) - strtotime($start)) - 1970;
		return $date_diff;
	}	
	
	function getAllowedComakers($filter = null, $limit = null, $offset = null, $select = null, $orderby = null,$loan_no = null){
		
		if($select)
			$this->db->select($select);
		$this->db->from("m_employee me");
		$this->db->join("t_loan_guarantor tl", "tl.guarantor_id = me.employee_id AND tl.loan_no = '$loan_no'", "left outer");
		if($orderby)
			$this->db->order_by($orderby);
		if ($filter)
		$this->db->where($filter);
		$this->db->limit($offset,$limit);

		$result = $this->db->get();
			
		$query = $this->db->last_query();
		
		if($filter){
			$this->db->where($filter);
			$this->db->from("m_employee me");
			$this->db->join("t_loan_guarantor tl", "tl.guarantor_id = me.employee_id AND tl.loan_no = '$loan_no'", "left outer");
			$count = $this->db->count_all_results();
		}else{
			$count = $this->db->count_all($this->table_name);
		}
			
		return $this->checkError($result->result_array(), $count, $query);
	}
	
	function getAllowedComakersForMembership($filter = null, $limit = null, $offset = null, $select = null, $orderby = null,$loan_no = null){
		
		if($select)
			$this->db->select($select);
		$this->db->from("m_employee me");
		$this->db->join("m_loan_guarantor tl", "tl.guarantor_id = me.employee_id AND tl.loan_no = '$loan_no'", "left outer");
		if($orderby)
			$this->db->order_by($orderby);
		if ($filter)
		$this->db->where($filter);
		$this->db->limit($offset,$limit);

		$result = $this->db->get();
			
		$query = $this->db->last_query();
		
		if($filter){
			$this->db->where($filter);
			$this->db->from("m_employee me");
			$this->db->join("m_loan_guarantor tl", "tl.guarantor_id = me.employee_id AND tl.loan_no = '$loan_no'", "left outer");
			$count = $this->db->count_all_results();
		}else{
			$count = $this->db->count_all($this->table_name);
		}
			
		return $this->checkError($result->result_array(), $count, $query);
	}
	
	function getLoanComakersList($filter = null, $limit = null, $offset = null, $select = null, $orderby = null){
		if($select)
			$this->db->select($select);
		$this->db->from("m_employee me");
		$this->db->join("m_loan_guarantor ml", "ml.guarantor_id = me.employee_id", "left outer");
		if($orderby)
			$this->db->order_by($orderby);
		if ($filter)
			$this->db->where($filter);
		$this->db->limit($offset,$limit);

		$result = $this->db->get();
			
		$query = $this->db->last_query();
			
		if($filter){
			$this->db->where($filter);
			$this->db->from("m_employee me");
			$this->db->join("m_loan_guarantor ml", "ml.guarantor_id = me.employee_id", "left outer");
			$count = $this->db->count_all_results();
		}else{
			$count = $this->db->count_all($this->table_name);
		}
			
		return $this->checkError($result->result_array(), $count, $query);
	}
	function get_EmployeeCapConStmtofAcctlist($filter = null, $limit = null, $offset = null, $select = null, $orderby = null,
												$start_date = null, $end_date = null, $start_trans_date = null,
												$end_trans_date = null){
		if($select)
			$this->db->select($select);
		$this->db->from("m_employee me");
		
		if( ($start_date != null) && ($end_date != null) && ($start_trans_date != null) && ($end_trans_date != null) ){
			$this->db->join("t_capital_contribution tcc", "tcc.employee_id = me.employee_id", "RIGHT OUTER");
			
		
		}
		
		
		if($orderby)
			$this->db->order_by($orderby);
		if ($filter)
			$this->db->where($filter);
		$this->db->limit($offset,$limit);

		$result = $this->db->get();
			
		$query = $this->db->last_query();
			
		if($filter){
			$this->db->where($filter);
			$this->db->from("m_employee me");
			$this->db->join("m_loan_guarantor ml", "ml.guarantor_id = me.employee_id", "left outer");
			$count = $this->db->count_all_results();
		}else{
			$count = $this->db->count_all($this->table_name);
		}
			
		return $this->checkError($result->result_array(), $count, $query);
	}
	/**
	 * @desc To get information of an employee
	 * @param employee_id
	 * @return array
	 */
	function getMemberInfo($employee_id, $rows)
	{
		$data = $this->member_model->get_list(array('employee_id' => $employee_id)
														 ,null
														 ,null
														 ,$rows);											 										 
		return $data['list'][0];
	}
	
	function getMembersInfo($rows)
	{
		$data = $this->member_model->get_list(null//array('member_status'=>'A')
											 ,null
											 ,null
											 ,$rows);											 										 
		return $data['list'];
	}
	
	function employeeIsInactive($employee_id){
		$data = $this->get(array("employee_id" => $employee_id), array("member_status"));
		if($data['list'][0]['member_status']=='I'){
			return 1;
		}
		else{
			return 0;
		}
	}
	
	function getCompanyList(){
		$sql = "SELECT me.company_code
					,rc.company_name
				FROM m_employee me
				INNER JOIN r_company rc
					ON (rc.company_code = me.company_code)
				GROUP BY company_code
				";
				
		$result = $this->db->query($sql);  
		$count = $result->num_rows();
    	return $this->checkError($result->result_array(), $count);		
	}
	
}
?>