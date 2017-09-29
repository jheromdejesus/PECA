<?php

class Loanyeartermreport_model extends Asi_Model {
	var $table_name = 't_loan';
	var $id = 'loan_no';
	var $model_data = null;
	var $date_columns = array('loan_date');
	
	
    function Loanyeartermreport_model()
    {
        parent::Asi_Model();
    }
  
    /**
     * @desc Retrieve all loan year term list
     * @param $filter
     * @param $select
     * @param $orderby
     */
	function getLoanYearTermList($filter = null, $select = null, $orderby = null, $distinct=null, $company_code=null){
		if($distinct)
			$this->db->distinct($distinct);
		if($select)
			$this->db->select($select);
    	$this->db->from('m_loan tl');
    	$this->db->join('r_loan_header rl', 'rl.loan_code = tl.loan_code', 'LEFT OUTER');
		$this->db->join('m_employee m', 'm.employee_id = tl.employee_id', 'LEFT OUTER');
		$this->db->join('r_company rc', 'rc.company_code = m.company_code', 'LEFT OUTER');
    	$this->db->where('tl.principal_balance > 0');
		if ($filter)
			$this->db->where($filter);
		if ($company_code)
			$this->db->where('rc.company_code ='.$company_code);
		$this->db->where('tl.status_flag IN (2,3)');
		if($orderby)
			$this->db->order_by($orderby);
		$result = $this->db->get();
			
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
			
	}
	
}
?>