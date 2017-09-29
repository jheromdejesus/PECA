<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Mcjournaldetail_model extends Asi_Model {
	
    function Mcjournaldetail_model()
    {
        parent::Asi_Model();
        $this->table_name = 'mc_journal_detail';
        $this->id = 'journal_no';
        $this->date_columns = array('');
    }
    
    function batchInsert($acctg_period, $user, $reference){
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
					, '0'
					,'{$user}'					
					,'{$now}'					
					,'{$user}'				
					,'{$now}'
				FROM t_journal_detail md
				INNER JOIN t_journal_header mh
					ON mh.journal_no = md.journal_no
				WHERE mh.reference = '{$reference}'
					AND mh.accounting_period > '{$acctg_period}'";
				
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
    	
    	return $this->checkError('','',$query);
    }    
    
 	function deleteJournalDetail($acctg_period, $reference){
    	$sql = "DELETE tjd
				FROM t_journal_detail tjd
				INNER JOIN t_journal_header tjh
					ON tjh.journal_no = tjd.journal_no
				WHERE tjh.reference = '{$reference}'
				AND tjh.accounting_period > '{$acctg_period}'";
				
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
    	
    	return $this->checkError('','',$query);
    }
	function insertJournal($journal_no, $user){
		$now = date("YmdHis");
    	
    	$sql = "INSERT INTO mc_journal_detail
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
					, '0'
					,'{$user}'					
					,'{$now}'					
					,'{$user}'				
					,'{$now}'
				FROM t_journal_detail md
				WHERE journal_no = '$journal_no'";
				
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$return = $this->checkError('','',$query);
    	
		if($return['error_code'] == 0){
			return 1;
		} else {
			return 0;
		}
	}    
}

?>
