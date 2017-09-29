<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Mjournalheader_model extends Asi_Model {
	
    function Mjournalheader_model()
    {
        parent::Asi_Model();
        $this->table_name = 'm_journal_header';
        $this->id = 'journal_no';
        $this->date_columns = array('');
    }
    
    function batchInsert($acctg_period, $user){
    	$now = date("YmdHis");
    	
    	$sql = "INSERT INTO m_journal_header
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
					, '4'
					,'{$user}'					
					,'{$now}'					
					,'{$user}'				
					,'{$now}'
				FROM t_journal_header
				WHERE accounting_period = '{$acctg_period}'";
				
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
    	
    	return $this->checkError('','',$query);
    }
    
}

?>
