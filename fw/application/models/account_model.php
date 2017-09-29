<?php

class Account_model extends Asi_Model {
	var $table_name = 'r_account';
	var $id = 'account_no';
	var $model_data = null;
	var $date_columns = array('');
	
	
    function Account_model()
    {
        parent::Asi_Model();
    }
	 /**
	 * @desc Retrieves Journal Entries that are already posted in a specified period.
	 * @return array
	 */
	function getAccountSummary($filter = null, $select = null, $orderby = null, $transaction_date, $account_no, $posted=true){
		if($select)
    		$this->db->select($select);
    	$this->db->from('r_account ra');
		$this->db->join('t_ledger ml', 'ml.account_no=ra.account_no', 'INNER');
		if($posted){
			$sql = "(SELECT mjh.journal_no								
				,mjh.accounting_period		
				,mjh.particulars		
				,mjh.transaction_date		
				,mjd.account_no		
				,mjd.debit_credit		
				,mjd.amount 		
			FROM m_journal_header mjh				
			INNER JOIN m_journal_detail mjd 		
				ON (mjh.journal_no=mjd.journal_no)  
			WHERE mjh.transaction_date <= '$transaction_date' 				
			AND mjd.amount <> '0'  				
			AND mjd.account_no = '$account_no') Journal";
		}
		else{
			$sql = "(SELECT tjh.journal_no										
					,tjh.particulars						
					,tjh.transaction_date										
					,tjh.accounting_period										
					,tjd.account_no										
					,tjd.debit_credit										
					,tjd.amount 		
			FROM t_journal_header tjh				
			INNER JOIN t_journal_detail tjd		
				ON (tjh.journal_no=tjd.journal_no)   
			WHERE tjh.transaction_date <= '$transaction_date' 				
			AND tjd.amount <> '0'  				
			AND tjd.account_no = '$account_no') Journal";
		
		}
		$this->db->join($sql, 'ml.account_no=Journal.account_no AND ml.accounting_period=Journal.accounting_period', 'LEFT OUTER');
		if($filter)	
    		$this->db->where($filter);
    	if($orderby)	
    		$this->db->orderby($orderby);
			
		$result = $this->db->get(); 
    	$query = $this->db->last_query();
    	$count = $result->num_rows();
    
		return $this->checkError($result->result_array(), $count, $query);
	}
	
	/**
	 * @desc To check the existence of an account no.
	 * @param account_no
	 * @return 1- exist; 0- doesn't exist
	 */
	function accountNoExists($account_no)
	{
		$data = $this->get(array('status_flag'=>'1', 'account_no' => $account_no));
		if($data['count'] > 0)	
			return 1;
		else
			return 0;
	}

	function getAccountInfo($account_no,$rows)
	{
		$data = $this->account_model->get_list(array('account_no' => $account_no)
														 ,null
														 ,null
														 ,$rows);											 										 
		return $data['list'][0];
	}
	
}
?>