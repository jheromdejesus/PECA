<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Tcaptrandetail_model extends Asi_Model {
	
    function Tcaptrandetail_model()
    {
        parent::Asi_Model();
        $this->table_name = 't_capital_transaction_detail';
        $this->id = 'transaction_no, transaction_code';
        $this->date_columns = array('');
    }
    
 	function batchInsert($user){
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
					,'{$user}'					
					,'{$now}'					
					,'{$user}'				
					,'{$now}'
				FROM t_capital_transaction_detail
				WHERE status_flag = '1'";
    	
    	$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
    	
    	return $this->checkError('','',$query);
    }
}

?>
