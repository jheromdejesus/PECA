<?php

class Interestearnedreport_model extends Asi_Model {
	var $table_name = 't_loan';
	var $id = 'loan_no';
	var $model_data = null;
	var $date_columns = array('loan_date');
	
	
    function Interestearnedreport_model()
    {
        parent::Asi_Model();
    }
  
    /**
     * @desc Retrieve all interest earned
     * @param $filter
     * @param $limit
     * @param $offset
     * @param $select
     * @param $orderby
     */
	function getInterestEarned($filter = null, $limit = null, $offset = null, $select = null, $orderby = null)
	{
		if($select)
			$this->db->select($select);
    	$this->db->from('m_loan AS tl');
    	$this->db->join('r_loan_header rlh', 'tl.loan_code = rlh.loan_code', 'INNER');
		$this->db->join('m_employee m', 'tl.employee_id = m.employee_id', 'INNER');
		$this->db->join('m_loan_payment tlp', 'tl.loan_no = tlp.loan_no', 'INNER');
		
		if ($filter)
			$this->db->where($filter);
		if($orderby)
			$this->db->order_by($orderby);
		$this->db->limit($offset, $limit);
		$result = $this->db->get();
		
		$query = $this->db->last_query();
		
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
			
	}
	
}
?>