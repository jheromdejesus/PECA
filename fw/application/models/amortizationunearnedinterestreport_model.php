<?php

class Amortizationunearnedinterestreport_model extends Asi_Model {
	var $table_name = 't_loan';
	var $id = 'loan_no';
	var $model_data = null;
	var $date_columns = array('loan_date');
	
	
    function Amortizationunearnedinterestreport_model()
    {
        parent::Asi_Model();
    }
  
    /**
     * @desc Retrieve all loans before accounting period for subsidy report
     * @param $acctgPeriod
     * @param $filter
     * @param $limit
     * @param $offset
     * @param $select
     * @param $orderby
     */
	function getUnearnedAmortizationInterest($filter = null, $limit = null, $offset = null, $select = null, $orderby = null, $payment_date){
		if($select)
			$this->db->select($select);
    	$this->db->from('m_loan tl');
    	$this->db->join('r_loan_header r', 'r.loan_code = tl.loan_code', 'LEFT OUTER');
		$this->db->join('m_employee m', 'm.employee_id=tl.employee_id', 'LEFT OUTER');
		$innerSelect = "(SELECT loan_no, MIN(balance) AS balance
						FROM m_loan_payment
						WHERE payment_date LIKE '$payment_date%'
						AND status_flag = 2
						GROUP BY loan_no
						)mlp";
		$this->db->join($innerSelect, 'mlp.loan_no = tl.loan_no', 'LEFT OUTER');
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