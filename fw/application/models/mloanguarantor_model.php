<?php

class Mloanguarantor_model extends Asi_Model {
	var $table_name = 'm_loan_guarantor';
	var $id = 'loan_no';
	var $model_data = null;
	var $date_columns = array('');
	
	
    function Mloanguarantor_model() {
        parent::Asi_Model();
    }
	
    function getInvalidCoMakers($date30DaysAgo){
		$sql = "SELECT ml.employee_id
				FROM m_loan_guarantor mlg
				INNER JOIN m_loan ml 
					ON(mlg.loan_no = ml.loan_no)
				INNER JOIN m_employee me
					ON(me.employee_id = mlg.guarantor_id)
				WHERE me.invalid_date <> ''
				AND me.invalid_date <= '".$date30DaysAgo."'";

		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
			
		return $this->checkError($result->result_array(), $count, $query);	
	}
	
	function getInvalidCoMakers2($date30DaysAgo){
		// [START] 0008326 : Modified by ASI307 on 20110915
		// [START] 0008344 : Modified by ASI466 on 20110922
		$sql = "SELECT ml.employee_id, CONCAT(me2.last_name,', ', me2.first_name) as employee_name
				,CONCAT(me.last_name,', ', me.first_name) AS comaker_name
				,DATE_FORMAT(me.work_date, '%M %e, %Y') AS separation_date
				FROM m_loan_guarantor mlg
				INNER JOIN m_loan ml 
					ON(mlg.loan_no = ml.loan_no)
				INNER JOIN m_employee me
					ON(me.employee_id = mlg.guarantor_id)
				INNER JOIN m_employee me2
					ON(me2.employee_id = ml.employee_id)
				WHERE me.work_date <> ''
				AND me.work_date <= '".$date30DaysAgo."'
				ORDER BY me2.last_name ";
		// [End] 0008326 : Modified by ASI307 on 20110915
		// [End] 0008344 : Modified by ASI307 on 20110922
		
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
			
		return $this->checkError($result->result_array(), $count, $query);	
	}
	
	function batchInsert(){
    	$sql = "INSERT INTO m_loan_guarantor
					( loan_no
					, guarantor_id
					, amortization_amount
					, interest_amount
					, status_flag
					, created_by
					, created_date
					, modified_by
					, modified_date )
				SELECT loan_no
					, guarantor_id
					, amortization_amount
					, interest_amount
					, '2'
					, created_by
					, created_date
					, modified_by
					, modified_date
				FROM t_loan_guarantor
				WHERE status_flag = '1'
				FOR UPDATE";
    	
    	$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
    	
    	return $this->checkError('','',$query);
    }
	
	/**
	 * @desc To retrieve the list of  comakers who are already resigned with its corresponding guaranteed loans.
	 * @return array
	 */
    function getCoMakersForReview()
    {
    	$sql = "SELECT DISTINCT mlg.guarantor_id
					,mlg.loan_no
					,me.work_date
				FROM m_loan_guarantor mlg
					INNER JOIN m_loan ml
						ON (ml.loan_no=mlg.loan_no)
					INNER JOIN m_employee me
						ON (me.employee_id=mlg.guarantor_id)
				WHERE ml.principal_balance>0
					AND (me.member_status='I' OR me.company_code='920')
					AND me.guarantor='Y'
					AND (me.work_date IS NOT NULL AND me.work_date<>'')
					AND ml.status_flag='2'
					AND mlg.status_flag='2'
				ORDER BY mlg.guarantor_id";
				
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
    	
        return $this->checkError($result->result_array(), $count, $query);
    }
	
	/**
	 * @desc Retrieve guarantors for selected loan transaction
	 * @return array
	 */
	function getGuarantor($filter = null, $limit = null, $offset = null, $select = null, $orderby = null) 
	{
    	if($select)
    		$this->db->select($select);
    	$this->db->from('m_loan_guarantor mlg');
    	$this->db->join('m_employee me', 'me.employee_id=mlg.guarantor_id', 'inner');
    	if($filter)	
    		$this->db->where($filter);
    	if($orderby)	
    		$this->db->orderby($orderby);	
    	$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
    	
    	$count = $result->num_rows();
    	
		return $this->checkError($result->result_array(), $count, $query);
    }
    
	
}