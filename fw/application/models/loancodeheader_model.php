<?php

class Loancodeheader_model extends Asi_Model {
	var $table_name = 'r_loan_header';
	var $id = 'loan_code';
	var $model_data = null;
	var $date_columns = array('');
	
	
    function Loancodeheader_model()
    {
        parent::Asi_Model();
    }

    /**
	 * @desc Retrieve Loan types that do not require co-maker
	 * @return array
	 */
    function retrieveLoanTypes($filter = null, $limit = null, $offset = null, $select = null, $orderby = null)
    {
    	if($select)
    		$this->db->select($select);
    	$this->db->from('r_loan_header RH');
    	$this->db->join('r_loan_detail RL', 'RL.loan_code=RH.loan_code', 'inner');
    	if($filter)	
    		$this->db->where($filter);
    	if($orderby)	
    		$this->db->orderby($orderby);	
    	$this->db->limit($limit, $offset);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query(); 
    	   	
    	if($filter){
			$this->db->where($filter);
			$this->db->from('r_loan_header RH');
    		$this->db->join('r_loan_detail RL', 'RL.loan_code=RH.loan_code', 'inner');
			$count = $this->db->count_all_results();
		}else{
			$count = $this->db->count_all($this->table_name);
		}
	
		return $this->checkError($result->result_array(), $count, $query);    
	}
	
/**
	 * @desc Retrieve Loan types that do not require co-maker
	 * @return array
	 */
    function retrieveLoanCodes($filter = null
    	, $limit = null, $offset = null
    	, $select = null, $orderby = null
    	, $years_of_service)
    {
    	if($select)
		{
    		$this->db->select($select);
		}
    	$this->db->from('r_loan_header RH');
    	$this->db->join("(SELECT loan_code
    			, years_of_service
    			, pension
    			, guarantor 
				, capital_contribution
    			FROM r_loan_detail 
    			WHERE years_of_service <='".$years_of_service."' ORDER BY years_of_service DESC) RL"
    		, "(RL.loan_code=RH.loan_code ", 'left outer');
    	if($filter)	
    		$this->db->where($filter);
    	if($orderby)
    	$this->db->orderby(
    		//$orderby
    		//updated 02082016 by lap
    		is_array($orderby) ? $orderby[0]: $orderby//, is_array($orderby) ? $orderby[1]: null
    		);	
    	//updated 02082016 by lap
    	if(!is_array($orderby))
    		$this->db->groupby('RH.loan_code');
    	
    	$this->db->limit($limit,$offset);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query(); 
		$count = $result->num_rows();

		return $this->checkError($result->result_array(), $count, $query);  
	}
	
	function retrieveLoanCodeList($filter = null, $limit = null, $offset = null, $select = null, $orderby = null)
    {
    	if($select)
    		$this->db->select($select);
    	$this->db->from('r_loan_header rh');
    	$this->db->join('r_transaction t', 't.transaction_code=rh.transaction_code', 'left outer');
    	if($filter)	
    		$this->db->where($filter);
    	if($orderby)	
    		$this->db->orderby($orderby);	
    	$this->db->limit($limit, $offset);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query(); 
    	   	
    	if($filter){
			$this->db->where($filter);
			$this->db->from('r_loan_header rh');
    		$this->db->join('r_transaction t', 't.transaction_code=rh.transaction_code', 'left outer');
			$count = $this->db->count_all_results();
		}else{
			$count = $this->db->count_all($this->table_name);
		}
	
		return $this->checkError($result->result_array(), $count, $query);    
	}
	
 /**
	 * @desc Retrieve Loan types that do not require co-maker
	 * @return array
	 */
    function retrieveLoanTypes2($filter = null, $limit = null, $offset = null, $select = null, $orderby = null)
    {
    	if($select)
    		$this->db->select($select);
    	$this->db->from('r_loan_header rh');
    	if($filter)	
    		$this->db->where($filter);
    	if($orderby)	
    		$this->db->orderby($orderby);	
    	$this->db->limit($limit, $offset);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query(); 
    	if($filter){
			$this->db->where($filter);
			$this->db->from('r_loan_header rh');
    		$count = $this->db->count_all_results();
		}else{
			$count = $this->db->count_all($this->table_name);
		}
	
		return $this->checkError($result->result_array(), $count, $query);    
	}
	
	/**
	 * @desc To get information of a loan type
	 * @param loan_code
	 * @return array
	 */
	function getLoanInfo($loan_code, $rows)
	{
		$data = $this->loancodeheader_model->get_list(array('loan_code' => $loan_code)
														 ,null
														 ,null
														 ,$rows);											 										 
		return $data['list'][0];
	}
	
	/**
	 * @desc To get loan payment CC type of a loan
	 * @param loan_code
	 * @return array
	 */
	function getLoanCC($filter, $select)
	{
		if($select)
    		$this->db->select($select);
		$this->db->distinct();
    	$this->db->from('r_loan_header rlh');
    	$this->db->join('r_loan_code_payment_type rlcp', 'rlh.loan_code = rlcp.loan_code', 'left outer');
    	$this->db->join('r_transaction rtc', 'rlcp.transaction_code = rtc.transaction_code', 'left outer');
    	if($filter)	
    		$this->db->where($filter);
			
		$result = $this->db->get(); 
    	$query = $this->db->last_query(); 
		$count = $result->num_rows();	
		
		return $this->checkError($result->result_array(), $count, $query);    
	}
	
	/**
	 * @desc To check the existence of the loan code
	 * @param loan_code
	 * @return 1- exist; 0- doesn't exist
	 */
	function loanCodeExists($loan_code)
	{
		$data = $this->get(array('status_flag'=>'1', 'loan_code' => $loan_code), array('loan_code'));
		if($data['count'] == 1)	
			if($data['list'][0]['loan_code'] == $loan_code)
				return 1;
		return 0;
	}
	
	function retrieveLoanCodeByPriority($loans = array()){
		$new_loans = array();
		foreach($loans as $loan_code){
			$new_loans[] = "'" . $loan_code . "'";
		}
		$loans_array_stmt = implode(',', $new_loans);
		$sql = "SELECT loan_code" .
				" FROM r_loan_header" .
				" WHERE loan_code IN ($loans_array_stmt)" .
				" ORDER BY priority";
				
		$result = $this->db->query($sql); 
    	
    	return $result->result_array();		
		
	}
	
	##### NRB EDIT START #####
	
	function is_bsp($s_loan_code) {
		$s_bsp = 'SELECT bsp_computation FROM r_loan_header WHERE bsp_computation = \'Y\' AND loan_code LIKE \''.$s_loan_code.'\'';
		$r_bsp = $this->db->query($s_bsp);
		
		if($r_bsp->result_array()) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	##### NRB EDIT END #####
}
?>