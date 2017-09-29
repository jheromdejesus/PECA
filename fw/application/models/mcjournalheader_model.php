<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Mcjournalheader_model extends Asi_Model {
	
    function Mcjournalheader_model()
    {
        parent::Asi_Model();
        $this->table_name = 'mc_journal_header';
        $this->id = 'journal_no';
        $this->date_columns = array('');
    }
    
    function batchInsert($acctg_period, $user, $reference){
    	$now = date("YmdHis");
    	
    	$sql = "INSERT INTO mc_journal_header
					( journal_no
					, accounting_period
					, transaction_code
					, transaction_date
					, particulars
					, reference
					, source
					, document_no
					, document_date
					, remarks
					, supplier_id
					, status_flag
					, created_by
					, created_date
					, modified_by
					, modified_date )
				SELECT journal_no
					, accounting_period
					, transaction_code
					, transaction_date
					, particulars
					, reference
					, source
					, document_no
					, document_date
					, remarks
					, supplier_id
					, '0'
					,'{$user}'					
					,'{$now}'					
					,'{$user}'				
					,'{$now}'
				FROM t_journal_header
				WHERE reference = '{$reference}'
				AND accounting_period > '{$acctg_period}'";
				
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
    	
    	return $this->checkError('','',$query);
    }
	
	function insertJournal($journal_no, $user){
		$now = date("YmdHis");
    	
    	$sql = "INSERT INTO mc_journal_header
					( journal_no
					, accounting_period
					, transaction_code
					, transaction_date
					, particulars
					, reference
					, source
					, document_no
					, document_date
					, remarks
					, supplier_id
					, status_flag
					, created_by
					, created_date
					, modified_by
					, modified_date )
				SELECT journal_no
					, accounting_period
					, transaction_code
					, transaction_date
					, particulars
					, reference
					, source
					, document_no
					, document_date
					, remarks
					, supplier_id
					, '0'
					,'{$user}'					
					,'{$now}'					
					,'{$user}'				
					,'{$now}'
				FROM t_journal_header
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
