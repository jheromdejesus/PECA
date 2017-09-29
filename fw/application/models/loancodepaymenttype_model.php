<?php

class Loancodepaymenttype_model extends Asi_Model {
	var $table_name = 'r_loan_code_payment_type';
	var $id = 'loan_code';
	var $model_data = null;
	var $date_columns = array('');
	
	
    function Loancodepaymenttype_model()
    {
        parent::Asi_Model();
    }
    
	function getLoanPaymentType($filter = null, $limit = null, $offset = null, $select = null, $orderby = null){
		$this->db->distinct();
		if($select)
			$this->db->select($select);
    	$this->db->from('r_loan_code_payment_type  lcpt');
    	$this->db->join('r_transaction rt', 'rt.transaction_code = lcpt.transaction_code', 'left outer');
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
	
	/**
	 * @desc To check the existence of a loan code payment type
	 * @param transaction_code
	 * @return 1- exist; 0- doesn't exist
	 */
	function loanCodePaymentTypeExists($transaction_code){
		$data = $this->get(array('transaction_code' => $transaction_code));
		if($data['count'] > 0)	
			return 1;
		else
			return 0;
	}
	
	/**
	 * @desc To check the existence of the transaction_code
	 * @param transaction_code
	 * @return 1- exist; 0- doesn't exist
	 */
	function transactionCodeExists($transaction_code, $transaction_group)
	{
		$data = $this->get(array('status_flag'=>'1', 'transaction_code' => $transaction_code, 'transaction_group'=>$transaction_group), array('transaction_code'));
		if($data['count'] == 1)	
			if($data['list'][0]['transaction_code'] == $transaction_code)
				return 1;
		return 0;
	}

}
?>