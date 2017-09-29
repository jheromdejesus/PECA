<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Tcaptranheader_model extends Asi_Model {
	
    function Tcaptranheader_model()
    {
        parent::Asi_Model();
        $this->table_name = 't_capital_transaction_header';
        $this->id = 'transaction_no';
        $this->date_columns = array('');
    }
    
 	function batchInsert(){
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
					, status_flag
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
    
    function getCapconPrintableInfo($transaction_date,$accounting_period, $employee_id, $transaction_code)
    {
    	$sql = "SELECT TC.transaction_no AS transaction_no				
					,TC.transaction_date AS transaction_date			
					,TC.transaction_amount AS transaction_amount			
					,TC.employee_id AS employee_id
					,tc.remarks			
					,MC.ending_balance AS capital_contribution			
				FROM t_capital_transaction_header TC 				
					INNER JOIN t_capital_contribution MC			
						ON (MC.employee_id=TC.employee_id)		
				WHERE TC.transaction_date='".$transaction_date."'				
					AND MC.accounting_period LIKE '".$accounting_period."%'			
					AND TC.employee_id='".$employee_id."'				
					AND TC.transaction_code = '".$transaction_code."'			
					AND TC.status_flag=1			
				ORDER BY tc.transaction_no ASC";

    	$result = $this->db->query($sql);    
    
        return $this->checkError($result->result_array());
    }
	
	function getCapCon($filter = null, $limit = null, $offset = null, $select = null, $orderby = null)
    {
    	if($select)
    		$this->db->select($select);
    	$this->db->from('t_capital_transaction_header tc');
    	$this->db->join('t_capital_transaction_detail tcc', 'tcc.transaction_no=tc.transaction_no', 'left outer');
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
    	$this->db->from('t_capital_transaction_header tc');
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
	
	function getWithdrawalBankTransfer()
	{
		$sql = "SELECT bank_transfer
				,COALESCE(SUM(transaction_amount),0) AS amount
				FROM t_capital_transaction_header
				WHERE transaction_code IN ('WDWL','CLSE')
				AND status_flag='1'
				GROUP BY bank_transfer";
		$result = $this->db->query($sql);    
    
        return $this->checkError($result->result_array());		
	}
    
}

?>