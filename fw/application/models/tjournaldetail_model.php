<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Tjournaldetail_model extends Asi_Model {
	
    function Tjournaldetail_model()
    {
        parent::Asi_Model();
        $this->table_name = 't_journal_detail';
        $this->id = 'journal_no';
        $this->date_columns = array('');
    }
    
    function deleteJournalDetail($acctg_period){
    	$sql = "DELETE td
				FROM t_journal_header tj
				INNER JOIN t_journal_detail td
					ON td.journal_no = tj.journal_no
				WHERE tj.accounting_period = '{$acctg_period}' ";
				
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
    	
    	return $this->checkError('','',$query);
    }
	
	function deleteJournalDetailII($acctg_period, $inv_no){
    	$sql = "DELETE td
				FROM t_journal_header tj
				INNER JOIN t_journal_detail td
					ON td.journal_no = tj.journal_no
				WHERE tj.accounting_period > '{$acctg_period}'
					AND tj.reference = '{$inv_no}'";
				
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
    	
    	return $this->checkError('','',$query);
    }
    
 	function deleteJournalDetailIM($inv_no, $maturity_date){
    	$sql = "DELETE td
				FROM t_journal_header tj
				INNER JOIN t_journal_detail td
					ON td.journal_no = tj.journal_no
				WHERE tj.reference = '{$inv_no}'
					AND tj.transaction_code = 'IAIR'
					AND tj.transaction_date = '{$maturity_date}'";
				
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
    	
    	return $this->checkError('','',$query);
    }
    
	function retrieveVoucher($transaction_date, $ci_acct_no)
	{
		$sql = "SELECT tjd.account_no AS account_no
					,CASE WHEN tjd.debit_credit='D' THEN SUM(amount) ELSE 0 END AS debit
					,CASE WHEN tjd.debit_credit='C' THEN SUM(amount) ELSE 0 END AS credit
				FROM t_journal_detail tjd
				INNER JOIN t_journal_header tjh
					ON (tjh.journal_no=tjd.journal_no
					AND tjh.transaction_date='$transaction_date'
					AND tjh.status_flag='3')
				WHERE tjd.amount>0
					AND tjd.status_flag='3'
					AND particulars 
					IN (SELECT DISTINCT particulars 
						FROM t_journal_header 
						INNER JOIN t_journal_detail 
							ON (t_journal_detail.journal_no=t_journal_header.journal_no 
							AND t_journal_detail.account_no='$ci_acct_no'
							AND t_journal_detail.debit_credit='C'
							AND t_journal_detail.status_flag='3')
						AND t_journal_header.status_flag='3')    
				GROUP BY tjd.account_no, tjd.debit_credit    
				ORDER BY tjd.debit_credit DESC, tjd.account_no ASC";		
		
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
    	
        return $this->checkError($result->result_array(), $count, $query);		
			
	}
    
}

?>