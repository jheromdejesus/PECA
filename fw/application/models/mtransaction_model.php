<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Mtransaction_model extends Asi_Model {
	
    function Mtransaction_model()
    {
        parent::Asi_Model();
        $this->table_name = 'm_transaction';
        $this->id = 'transaction_no';
        $this->date_columns = array('');
    }

    
 	function retrieveDormantAccount($date){
    	$sql = "SELECT mm.employee_id AS employee_id
				FROM m_employee mm 
				LEFT OUTER JOIN (SELECT DISTINCT employee_id
							FROM m_transaction
							WHERE transaction_date > '{$date}'
								AND transaction_code NOT IN ('DFEE','DIVD','DTAX')
								AND transaction_code NOT LIKE 'DIV%'
								AND status_flag = '3') t
					ON t.employee_id = mm.employee_id
				WHERE t.employee_id IS NULL
					AND mm.member_status = 'A'";
		
    	$result = $this->db->query($sql);
  
//    	echo $this->db->last_query();
    	return $result->result_array();		
    }
    
    function batchInsertByGroup($group, $curdate){
    	$sql = "INSERT INTO m_transaction
					( transaction_no
					, transaction_date
					, transaction_code
					, employee_id
					, transaction_amount
					, source
					, reference
					, remarks
					, status_flag
					, created_by					
					, created_date					
					, modified_by					
					, modified_date )
				SELECT transaction_no
					, tt.transaction_date
					, tt.transaction_code
					, tt.employee_id
					, tt.transaction_amount
					, tt.source
					, tt.reference
					, tt.remarks
					, '3'
					, tt.created_by					
					, tt.created_date					
					, tt.modified_by					
					, tt.modified_date
				FROM t_transaction tt
				INNER JOIN r_transaction rt
					ON rt.transaction_code = tt.transaction_code
				WHERE rt.transaction_group = '{$group}'
					AND tt.transaction_date <= '{$curdate}'
					AND tt.status_flag = '2'";
				
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
    	
    	return $this->checkError('','',$query);
    }
    
 	/**
	 * @desc Retrieves all employees with no active transactions for two years.
	 * @return array
	 */
    function retrieveDormantEmployees($current_date_minus_two_years)
    {
    	$sql = "SELECT mm.employee_id AS employee_id
					,mm.last_name AS last_name
					,mm.first_name AS first_name
					,mm.middle_name AS middle_name
				FROM m_employee mm 
				LEFT OUTER JOIN (SELECT DISTINCT employee_id
							FROM m_transaction
							WHERE transaction_date > '{$current_date_minus_two_years}'
								AND transaction_code NOT IN ('DFEE','DIVD','DTAX')
								AND transaction_code NOT LIKE 'DIV%'
								AND status_flag = '3') t
					ON t.employee_id = mm.employee_id
				WHERE t.employee_id IS NULL
					AND mm.member_status = 'A'";	
		
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
    	
        return $this->checkError($result->result_array(), $count, $query);
    }
    
 	function getTranList($filter = null, $limit = null, $offset = null, $select = null, $orderby = null){
		if($select)
			$this->db->select($select);
    	$this->db->from('m_transaction t');
    	$this->db->join('m_employee m', 'm.employee_id = t.employee_id', 'left outer');
    	$this->db->join('r_transaction r', 't.transaction_code = r.transaction_code', 'left outer');
		
    	if ($filter)
			$this->db->where($filter);
			
		if($orderby)
			$this->db->order_by($orderby);
			
		$this->db->limit($offset, $limit);
		$result = $this->db->get();
		
		$query = $this->db->last_query();
		
		//for count
		if ($filter)
			$this->db->where($filter);
		$this->db->from('t_transaction t');
    	$this->db->join('m_employee m', 'm.employee_id = t.employee_id', 'left outer');
    	$this->db->join('r_transaction r', 't.transaction_code = r.transaction_code', 'left outer');
		$count = $this->db->count_all_results();
					
		return $this->checkError($result->result_array(), $count, $query);			
	}
	
	function getHistTranList($filter = null, $limit = null, $offset = null, $select = null, $orderby = null){
		$sql = "SELECT transaction_date AS transaction_date
				, transaction_code AS transaction_code
				, transaction_amount AS transaction_amount
				, capcon_effect AS capcon_effect
				, modified_date AS modified_date
				, transaction_date_order AS transaction_date_order  
				FROM 
					(SELECT DATE_FORMAT(t.transaction_date,'%m%d%Y') AS transaction_date
						, `t`.`transaction_code` AS transaction_code
						, `t`.`transaction_amount` AS transaction_amount
						, COALESCE(`r`.`capcon_effect`, 0) AS capcon_effect 
						, t.modified_date AS modified_date
						, t.transaction_date AS transaction_date_order
					FROM (`m_transaction` t) 
					LEFT OUTER JOIN `m_employee` m 
						ON `m`.`employee_id` = `t`.`employee_id` 
					LEFT OUTER JOIN `r_transaction` r 
						ON `t`.`transaction_code` = `r`.`transaction_code` 
					WHERE `t`.`employee_id` = '".$filter."') temp 
				WHERE capcon_effect <> 0 
				ORDER BY transaction_date_order";
			
		$result = $this->db->query($sql);  
		$count = $result->num_rows();
		if( $limit || $offset)
			$sql .=	" LIMIT $offset, $limit";
		$result = $this->db->query($sql);  
		$query = $this->db->last_query();
		return $this->checkError($result->result_array(), $count, $query);			
	}
	
	function getHistTranListDesc($filter = null, $limit = null, $offset = null, $select = null, $orderby = null){
		$sql = "SELECT transaction_date AS transaction_date
				, transaction_code AS transaction_code
				, transaction_amount AS transaction_amount
				, capcon_effect AS capcon_effect
				, modified_date AS modified_date
				, transaction_date_order AS transaction_date_order  
				FROM 
					(SELECT DATE_FORMAT(t.transaction_date,'%m%d%Y') AS transaction_date
						, `t`.`transaction_code` AS transaction_code
						, `t`.`transaction_amount` AS transaction_amount
						, COALESCE(`r`.`capcon_effect`, 0) AS capcon_effect 
						, t.modified_date AS modified_date
						, t.transaction_date AS transaction_date_order
					FROM (`m_transaction` t) 
					LEFT OUTER JOIN `m_employee` m 
						ON `m`.`employee_id` = `t`.`employee_id` 
					LEFT OUTER JOIN `r_transaction` r 
						ON `t`.`transaction_code` = `r`.`transaction_code` 
					WHERE `t`.`employee_id` = '".$filter."') temp 
				WHERE capcon_effect <> 0 
				ORDER BY transaction_date_order DESC, modified_date DESC";
			
		$result = $this->db->query($sql);  
		$count = $result->num_rows();
		if( $limit || $offset)
			$sql .=	" LIMIT $offset, $limit";
		$result = $this->db->query($sql);  
		$query = $this->db->last_query();
		return $this->checkError($result->result_array(), $count, $query);			
	}
	
	function getMTransBalance($employee_id){
		$sql = "SELECT COALESCE(SUM(CASE WHEN rt.capcon_effect = -1 THEN (transaction_amount * -1) ELSE transaction_amount END),0) AS total 
				FROM m_transaction mt
				INNER JOIN r_transaction rt
				ON(rt.transaction_code = mt.transaction_code)
				WHERE employee_id = '{$employee_id}'
				AND mt.status_flag = 3
				AND rt.capcon_effect <> 0
				GROUP BY employee_id";
				
		$result = $this->db->query($sql);
		if ($result->num_rows() > 0){
		   $row = $result->row();
		   $balance = $row->total;
		} 
		else{
			$balance = 0;
		}
		
		return $balance;
	}
	
 	function getStartingDate($employee){
 		// I have to do this query because if not mysql will hang up.
		$sql = "SELECT MIN(transaction_date) AS transaction_date FROM (
			SELECT rt.transaction_code, IF(rt.transaction_code IS NULL, 99999999999999,transaction_date) transaction_date,
			IF(rt.capcon_effect = 0 OR rt.capcon_effect IS NULL, 2,capcon_effect) capcon_effect
			FROM m_transaction mt
			LEFT OUTER JOIN r_transaction rt 
				ON mt.transaction_code = rt.transaction_code 
			WHERE employee_id = '$employee') temp WHERE capcon_effect < 2";
			
		$result = $this->db->query($sql);  
		$query = $this->db->last_query();
		return $this->checkError($result->result_array(), 1, $query);			
	}
	
	function retrieveCapConTransactions($employee_id = null, $start_date = null, $end_date = null, $select = null, $orderby = null)
    {
		$sql = "SELECT tt.transaction_date
					, tt.transaction_code
					, rt.transaction_description
					, td.dividend_rate
					, CASE WHEN rt.capcon_effect = 1 THEN transaction_amount ELSE 0 END AS addition
					, CASE WHEN rt.capcon_effect = -1 THEN transaction_amount ELSE 0 END AS deduction
				FROM m_transaction tt
				LEFT OUTER JOIN t_dividend td
				ON tt.transaction_code = td.dividend_code
					AND MONTH(tt.transaction_date) = MONTH(td.accounting_period)
					AND YEAR(tt.transaction_date) = YEAR(td.accounting_period)
				INNER JOIN r_transaction rt
					ON tt.transaction_code = rt.transaction_code
				WHERE tt.transaction_date BETWEEN '$start_date' AND '$end_date'
				AND rt.capcon_effect <> 0
				AND tt.employee_id = '$employee_id'
				AND tt.status_flag = '3'
				ORDER BY tt.transaction_date ASC";
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);		
    }	
	
	function getDormantTransactionCount($date){
		$year = substr($date, 0, 4);
    	$month = substr($date, 4, 2);
		$sql = "SELECT COUNT(employee_id) as dormant
				FROM m_transaction 
				WHERE transaction_code = 'DFEE'
					AND transaction_date LIKE ('$year$month%')";
		
    	$result = $this->db->query($sql);
		$result = $result->result_array();		
  
//    	echo $this->db->last_query();
    	return $result[0]['dormant'];		
	}
	
	function retrieveByAccountingPeriod($acctngperiod){
    	$sql = "SELECT mt.employee_id
					, mt.transaction_date
					, (rt.capcon_effect * mt.transaction_amount) as amount
				FROM m_transaction mt
				INNER JOIN r_transaction rt
					ON (rt.transaction_code = mt.transaction_code)
				WHERE mt.transaction_date LIKE '{$acctngperiod}%'
				AND rt.capcon_effect <> 0
				ORDER BY mt.employee_id, mt.transaction_date, mt.modified_date
				";
		
    	$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
			
		return $this->checkError($result->result_array(), $count, $query);	
    }
}

?>
