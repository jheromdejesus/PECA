<?php

class Capitaltransactionheader_model extends Asi_Model {
	var $table_name = 't_capital_transaction_header';
	var $id = 'transaction_no';
	var $model_data = null;
	var $date_columns = array('transaction_date', 'or_date');
	
	
    function Capitaltransactionheader_model() {
        parent::Asi_Model();
    }

    function getListCapconTrans($filter = null, $limit = null, $offset = null, $select = null, $orderby = null) {
    	if($select)
    		$this->db->select($select);
    	$this->db->from('t_capital_transaction_header tc');
    	$this->db->join('m_employee me', 'me.employee_id =  tc.employee_id', 'left outer');
    	$this->db->join('r_transaction rt', 'rt.transaction_code  = tc.transaction_code', 'left outer');
    	if($filter)	
    		$this->db->where($filter);
    	if($orderby)	
    		$this->db->orderby($orderby);	
    	$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
    	
    	if($filter){
			$this->db->where($filter);
			$this->db->from('t_capital_transaction_header tc');
	    	$this->db->join('m_employee me', 'me.employee_id =  tc.employee_id', 'left outer');
	    	$this->db->join('r_transaction rt', 'rt.transaction_code  = tc.transaction_code', 'left outer');
			$count = $this->db->count_all_results();
		}else{
			$count = $this->db->count_all($this->table_name);
		}
		
    	return $this->checkError($result->result_array(), $count, $query);
    }
    
	function getCapconTrans($_param = null, $select = null){
		if($select)
			$this->db->select($select);

		$this->db->from('t_capital_transaction_header tc');
    	$this->db->join('m_employee me', 'me.employee_id =  tc.employee_id', 'left outer');
    	$this->db->join('r_transaction rt', 'rt.transaction_code  = tc.transaction_code', 'left outer');
    
		$this->db->where($_param ? $_param : $this->model_data);
		$result = $this->db->get();  
		$query = $this->db->last_query();
		return $this->checkError($result->result_array(),0,$query);
	}
	
	//Update the transaction date to currdate when loading the details of capcon withdrawal/deposit - requested by peca 20110609
	function changeTransactionDateToCurrDate($tranNo, $currDate){
		$select_query = "SELECT transaction_date FROM t_capital_transaction_header WHERE transaction_no = '$tranNo'";
		$select_result = mysql_query($select_query);
		$select_obj = mysql_fetch_object($select_result);
		if($select_obj->transaction_date != $currDate) {
			$query = "UPDATE t_capital_transaction_header SET transaction_date = '$currDate' WHERE transaction_no = '$tranNo'";
			log_message('debug', 'Query:'.$query);
			mysql_query($query);
		}
	}
	
	
	//[start]20111121 modified by asi466 issue:0008377
	//get other other wdwls with wmax of the member made within the month
	function getWdwlWithWmax($employee_id,$accounting_period){
		$sql = "SELECT td.transaction_no
				FROM t_capital_transaction_header th
					INNER JOIN t_capital_transaction_detail td
					ON td.transaction_no = th.transaction_no
				WHERE th.transaction_code = 'WDWL'
				AND td.transaction_code = 'WMAX'
				AND th.employee_id = '$employee_id'
				AND th.transaction_date LIKE '$accounting_period%'
				ORDER BY td.transaction_no ASC
				LIMIT 1
				";
				
    	$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
    	
    	return $this->checkError($result->result_array(),$count,$query);
	}
	//[end]20111121 modified by asi466 issue:0008377

}
?>
