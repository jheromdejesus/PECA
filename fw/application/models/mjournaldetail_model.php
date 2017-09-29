<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Mjournaldetail_model extends Asi_Model {
	
    function Mjournaldetail_model()
    {
        parent::Asi_Model();
        $this->table_name = 'm_journal_detail';
        $this->id = 'journal_no';
        $this->date_columns = array('');
    }
    
    function batchInsert($acctg_period, $user){
    	$now = date("YmdHis");
    	
    	$sql = "INSERT INTO m_journal_detail
					( journal_no
					, account_no
					, debit_credit
					, amount
					, status_flag
					, created_by					
					, created_date					
					, modified_by					
					, modified_date )
				SELECT md.journal_no
					, md.account_no
					, md.debit_credit
					, md.amount
					, '4'
					,'{$user}'					
					,'{$now}'					
					,'{$user}'				
					,'{$now}'
				FROM t_journal_detail md
				INNER JOIN t_journal_header mh
					ON mh.journal_no = md.journal_no
				WHERE mh.accounting_period = '{$acctg_period}'";
				
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
    	
    	return $this->checkError('','',$query);
    }    
    
}

?>
