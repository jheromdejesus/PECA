<?php

class Mloancharges_model extends Asi_Model {
	var $table_name = 'm_loan_charges';
	var $id = 'loan_no';
	var $model_data = null;
	var $date_columns = array('');
	
	
    function Mloancharges_model() {
        parent::Asi_Model();
    }
    
	function batchInsert(){
    	$sql = "INSERT INTO m_loan_charges
					( loan_no
					, transaction_code
					, amount
					, status_flag
					, created_by
					, created_date
					, modified_by
					, modified_date )
				SELECT loan_no
					, transaction_code
					, amount
					, '2'
					, created_by
					, created_date
					, modified_by
					, modified_date 
				FROM t_loan_charges
				WHERE status_flag = '1'
				FOR UPDATE";
    	
    	$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
    	
    	return $this->checkError('','',$query);
    }
    function getLoanChargesList($filter=null, $limit = null, $offset = null, $select = null){
		if($select)
			$this->db->select($select);
		$this->db->from('m_loan_charges ml');
		$this->db->join('r_transaction rl', 'rl.transaction_code = ml.transaction_code', 'inner');
		if ($filter)
		$this->db->where($filter);
		$this->db->limit($offset,$limit);

		$result = $this->db->get();
			
		$query = $this->db->last_query();
			
		if($filter){
			$this->db->where($filter);
			$this->db->from($this->table_name);
			$count = $this->db->count_all_results();
		}else{
			$count = $this->db->count_all($this->table_name);
		}
		return $this->checkError($result->result_array(), $count, $query);
		
	}
}