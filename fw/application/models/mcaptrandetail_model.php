<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Mcaptrandetail_model extends Asi_Model {
	
    function Mcaptrandetail_model()
    {
        parent::Asi_Model();
        $this->table_name = 't_capital_transaction_detail';
        $this->id = 'transaction_no, transaction_code';
        $this->date_columns = array('');
    }
    
 	function batchInsert(){
 		$now = date("YmdHis");
    	$sql = "INSERT INTO m_capital_transaction_detail
					( transaction_no
					, transaction_code
					, amount
					, status_flag
					, created_by
					, created_date
					, modified_by
					, modified_date )
				SELECT transaction_no
					, transaction_code
					, amount
					, '2'
					, created_by
					, created_date
					, modified_by
					, modified_date 
				FROM t_capital_transaction_detail
				WHERE status_flag = '1'";
    	
    	$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
    	
    	return $this->checkError('','',$query);
    }
}

?>
