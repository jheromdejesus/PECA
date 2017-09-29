<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Mloanpaymentdetail_model extends Asi_Model {
	
    function Mloanpaymentdetail_model()
    {
        parent::Asi_Model();
        $this->table_name = 'm_loan_payment_detail';
        $this->id = array('loan_no', 'transaction_code', 'payment_date', 'payor_id');
        $this->date_columns = array('');
    }
    
 	function batchInsert(){
    	$sql = "INSERT INTO m_loan_payment_detail
					( loan_no
					, payment_date
					, transaction_code
					, payor_id
					, amount
					, status_flag
					, created_by
					, created_date
					, modified_by
					, modified_date )
				SELECT loan_no
					, payment_date
					, transaction_code
					, payor_id
					, amount
					, '2'
					, created_by
					, created_date
					, modified_by
					, modified_date 
				FROM t_loan_payment_detail
				WHERE status_flag = '1'";
    	
    	$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
    	
    	return $this->checkError('','',$query);
    }
    
}

?>
