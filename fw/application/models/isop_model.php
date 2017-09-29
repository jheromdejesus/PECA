<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Isop_model extends Asi_Model {
	
    function Isop_model()
    {
        parent::Asi_Model();
        $this->table_name = 't_isop';
        $this->id = 'transaction_no';
        $this->date_columns = array('');
    }
    
    function retrieveIsopWithrawals($period, $current_date, $min_bal){
    	$sql = "SELECT ti.transaction_no
						, ti.employee_id
						, ti.amount													
						, 'ISOP' AS trancode													
					FROM t_isop ti													
					LEFT OUTER JOIN t_capital_contribution tcc														
						ON ti.employee_id = tcc.employee_id													
					LEFT OUTER JOIN (SELECT employee_id														
										, SUM(capital_contribution_balance) AS requirement 							
									FROM m_loan 								
									WHERE status_flag = '2'						
									GROUP BY employee_id) tl								
							ON tl.employee_id = tcc.employee_id													
					WHERE tcc.accounting_period = '{$period}'														
						AND ti.start_date <= '{$current_date}'													
						AND ti.end_date >= '{$current_date}'													
						AND tcc.ending_balance - ti.amount >= {$min_bal}													
						AND tcc.ending_balance - ti.amount >= COALESCE(tl.requirement,0)													
						AND ti.amount > 0
						AND ti.status_flag = 1";
		
		
    	$query = $this->db->query($sql);    	
    	return $query->result_array();		
    }
	function getIsop($_param = null, $select = null){
		if($select)
			$this->db->select($select);
		$this->db->from('t_isop ti');
    	$this->db->join('m_employee me', 'ti.employee_id = me.employee_id', 'left outer');
		$this->db->where($_param ? $_param : $this->model_data);
		$result = $this->db->get();
		$query = $this->db->last_query();
		return $this->checkError($result->result_array(),0,$query);
	}
	
	function getListIsop($filter = null, $limit = null, $offset = null, $select = null, $orderby = null){
    	if($select)
			$this->db->select($select);
    	$this->db->from('t_isop ti');
    	$this->db->join('m_employee me', 'ti.employee_id = me.employee_id', 'left outer');
		if ($filter)
			$this->db->where($filter);
		if($orderby)
		//$clone_db = clone $this->db; 
		$this->db->order_by($orderby);
		
		//$count = $clone_db->count_all_results();
		
		$this->db->limit($offset, $limit);
		$result = $this->db->get();
		//$query = $clone_db->last_query();
		$query = $this->db->last_query();
		
		if($filter){
			$this->db->where($filter);
			$this->db->from('t_isop ti');
    		$this->db->join('m_employee me', 'ti.employee_id = me.employee_id', 'left outer');
			$count = $this->db->count_all_results();
		}else{
			$count = $this->db->count_all($this->table_name);
		}
		
		return $this->checkError($result->result_array(), $count, $query);
    	
    }
	
	function getNewISOPCountForReport($transaction_period)
	{
		$sql = "SELECT DISTINCT employee_id
				FROM t_isop 
				WHERE transaction_period LIKE '$transaction_period%'
					AND transaction_type='A'
					AND status_flag='1'";
					
		$result = $this->db->query($sql);
			
		$query = $this->db->last_query();
		$count = $result->num_rows();
			
		return $this->checkError($result->result_array(), $count, $query);	
	}
	
	function getAllISOPCountForReport()
	{
		$sql = "SELECT DISTINCT employee_id
				FROM t_isop 
				WHERE transaction_type='D'
					AND status_flag='1'";
					
		$result = $this->db->query($sql);
			
		$query = $this->db->last_query();
		$count = $result->num_rows();
			
		return $this->checkError($result->result_array(), $count, $query);	
	}
	
	function retrieveConflict($start, $end, $employee, $transaction_no=null){
		$sql = "";		
		if ($transaction_no){
			$sql = "SELECT start_date
					, end_date
					, amount
				FROM t_isop 
				WHERE ((start_date <= '$start' AND end_date >= '$start')
						OR (start_date <= '$end' AND end_date >= '$end'))
					AND employee_id = '$employee'
					AND transaction_no <> '$transaction_no'
					AND status_flag = '1'";
		} else{
			$sql = "SELECT start_date
					, end_date
					, amount
				FROM t_isop 
				WHERE ((start_date <= '$start' AND end_date >= '$start')
						OR (start_date <= '$end' AND end_date >= '$end'))
					AND employee_id = '$employee'
					AND status_flag = '1'";
		}
				
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);	
	}
	

	function deleteConflict($start, $end, $employee){
		$sql = "DELETE
				FROM t_isop
				WHERE ((start_date <= '$start' AND end_date >= '$start')
						OR (start_date <= '$end' AND end_date >= '$end'))
					AND employee_id = '$employee'
					AND status_flag = '1'";
					
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		
		return $this->checkError('','',$query);
	}
	

	function insertIsop($transaction_no, $new_start_date, $new_end_date, $new_amount, $transaction_type, $employee_id, $transaction_period, $created_by){
		$currdate = date("YmdHis");
		$sql = "INSERT
				INTO t_isop
			    (transaction_no, start_date, end_date, amount, transaction_type,  employee_id, transaction_period, created_by, created_date, modified_by, modified_date, status_flag) 
			    VALUES ('$transaction_no', '$new_start_date', '$new_end_date', '$new_amount', '$transaction_type', '$employee_id', '$transaction_period', '$created_by', '$currdate', '$created_by', '$currdate', '1')";
					
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		
		return $this->checkError('','',$query);
	}
	
}

?>
