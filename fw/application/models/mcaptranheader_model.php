<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Mcaptranheader_model extends Asi_Model {
	
    function Mcaptranheader_model()
    {
        parent::Asi_Model();
        $this->table_name = 'm_capital_transaction_header';
        $this->id = 'transaction_no';
        $this->date_columns = array('');
    }
    
 	function batchInsert(){
 		$now = date("YmdHis");
    	$sql = "INSERT INTO m_capital_transaction_header
					( transaction_no
					, transaction_date
					, transaction_code
					, employee_id
					, transaction_amount
					, or_no
					, or_date
					, remarks
					, bank_transfer
					, status_flag
					, created_by
					, created_date
					, modified_by
					, modified_date )
				SELECT transaction_no
					, transaction_date
					, transaction_code
					, employee_id
					, transaction_amount
					, or_no
					, or_date
					, remarks
					, bank_transfer
					, '2'
					, created_by
					, created_date
					, modified_by
					, modified_date 
				FROM t_capital_transaction_header
				WHERE status_flag = '1'";
    	
    	$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
    	
    	return $this->checkError('','',$query);
    }
	
	function getCapCon($filter = null, $limit = null, $offset = null, $select = null, $orderby = null)
    {
    	if($select)
    		$this->db->select($select);
    	$this->db->from('m_capital_transaction_header tc');
    	$this->db->join('m_capital_transaction_detail tcc', 'tcc.transaction_no=tc.transaction_no', 'left outer');
    	if($filter)	
    		$this->db->where($filter);
    	if($orderby)	
    		$this->db->order_by($orderby);	
    	$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
    	
    	$count = $this->db->count_all($this->table_name);
  		
		return $this->checkError($result->result_array(), $count, $query);
    }
    
	function getCapConCodes($filter = null, $limit = null, $offset = null, $select = null, $orderby = null)
    {
    	if($select)
    		$this->db->distinct('tc.transaction_code AS transaction_code');
    		$this->db->select($select);
    	$this->db->from('m_capital_transaction_header tc');
    	$this->db->join('r_transaction rt', 'rt.transaction_code=tc.transaction_code', 'left outer');
    	if($filter)	
    		$this->db->where($filter);
    	if($orderby)	
    		$this->db->order_by($orderby);	
    	$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
    
    	$count = $result->num_rows();
  		
		return $this->checkError($result->result_array(), $count, $query);
    }
	
	function generateOR($currdate = '', $tran_no = 0){
		$sql = "";
		
		//0 means batch OR
		if ($tran_no == 0){
			//remove SUM and group by since OR is generated per transaction , not per employee
			$sql = "SELECT mcth.or_date
						, mcth.or_no
						, mcth.employee_id
						, mm.first_name
						, mm.last_name
						, mm.middle_name
						, mcth.transaction_amount
						#, mcth.transaction_amount - COALESCE(mctd.amount,0) AS capcon
						, COALESCE(mctd.amount,0) AS charges
					FROM m_capital_transaction_header mcth
					LEFT OUTER JOIN m_capital_transaction_detail mctd
					ON (mctd.transaction_no = mcth.transaction_no) 
					INNER JOIN m_employee mm
						ON(mm.employee_id = mcth.employee_id)
					INNER JOIN r_transaction rt															
						ON rt.transaction_code = mcth.transaction_code
					WHERE mcth.or_date = '{$currdate}'
						AND mcth.or_no IS NOT NULL
						AND rt.with_or = 'Y'
						AND mcth.status_flag = '2'
					ORDER BY  mm.first_name
						, mm.middle_name
						, mm.last_name";
		} else{
			//print OR from capcon
			$sql = "SELECT mcth.or_date
						, mcth.or_no
						, mcth.employee_id
						, mm.first_name
						, mm.last_name
						, mm.middle_name
						, mcth.transaction_amount
						#, mcth.transaction_amount - COALESCE(mctd.amount,0) AS capcon
						, COALESCE(mctd.amount,0) AS charges
					FROM t_capital_transaction_header mcth
					LEFT OUTER JOIN t_capital_transaction_detail mctd
					ON (mctd.transaction_no = mcth.transaction_no) 
					INNER JOIN m_employee mm
						ON(mm.employee_id = mcth.employee_id)
					INNER JOIN r_transaction rt															
						ON rt.transaction_code = mcth.transaction_code
					WHERE mcth.or_date = '{$currdate}'
						AND mcth.or_no IS NOT NULL
						AND rt.with_or = 'Y'
						AND mcth.transaction_no = '{$tran_no}'
						AND mcth.status_flag = '1'
					ORDER BY  mm.first_name
						, mm.middle_name
						, mm.last_name";
		}
		
    	$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
    	
    	return $this->checkError($result->result_array(),$count,$query);
	}
}

?>
