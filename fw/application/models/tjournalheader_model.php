<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Tjournalheader_model extends Asi_Model {
	
    function Tjournalheader_model()
    {
        parent::Asi_Model();
        $this->table_name = 't_journal_header';
        $this->id = 'journal_no';
        $this->date_columns = array('');
    }
    
    /**
	 * @desc To retrieve particulars for the given date
	 * @return array
	 */
    function retrieveParticularsForDisbursement($transaction_date, $ci_acct_no)
    {
    	$sql = "SELECT DISTINCT jh.particulars AS particulars								
				  FROM t_journal_header jh 								
						INNER JOIN t_journal_detail jd 							
							ON (jd.journal_no=jh.journal_no
								AND jd.account_no='".$ci_acct_no."'							
								AND jd.debit_credit='C'
								#AND jd.amount>0
								AND jd.status_flag='3') 						
				  WHERE jh.transaction_date='".$transaction_date."'															
						AND jh.status_flag='3'							
				  ORDER BY jh.particulars ASC";
				
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
    	
        return $this->checkError($result->result_array(), $count, $query);
    }
    
 	/**
	 * @desc To retrieve particulars for adjustment for the given date
	 * @return array
	 */
    function retrieveParticularsForAdjustment($transaction_date, $ci_acct_no, $user=null)
    {
    	if ($user==null){
			$sql = "SELECT DISTINCT jh.particulars AS particulars								
				  FROM t_journal_header jh 								
						LEFT JOIN (SELECT DISTINCT journal_no FROM t_journal_detail WHERE account_no='".$ci_acct_no."') x 						
							ON jh.journal_no=x.journal_no 
						INNER JOIN t_journal_detail td
						 	ON (td.journal_no=jh.journal_no AND td.status_flag='3')	
				  WHERE jh.transaction_date='".$transaction_date."'								
						AND x.journal_no IS NULL	
						AND jh.status_flag='3'
						#AND td.amount>0							
				  ORDER BY jh.particulars ASC";
		}
		else {
			$sql = "SELECT DISTINCT jh.particulars AS particulars								
				  FROM t_journal_header jh 								
						LEFT JOIN (SELECT DISTINCT journal_no FROM t_journal_detail WHERE account_no='".$ci_acct_no."') x 						
							ON jh.journal_no=x.journal_no 	
						INNER JOIN t_journal_detail td
						 	ON (td.journal_no=jh.journal_no AND td.status_flag='3')		
				  WHERE jh.transaction_date='".$transaction_date."'								
						AND x.journal_no IS NULL	
						AND jh.status_flag='3'	
						#AND td.amount>0		
						AND jh.modified_by='$user'	
				  ORDER BY jh.particulars ASC";
		}
					
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
    	
        return $this->checkError($result->result_array(), $count, $query);
    }
    
 	/**
	 * @desc To retrieve particulars for collection for the given date
	 * @return array
	 */
    function retrieveParticularsForCollection($transaction_date, $ci_acct_no)
    {
    	$sql = "SELECT DISTINCT jh.particulars AS particulars								
				  FROM t_journal_header jh 								
						INNER JOIN t_journal_detail jd 							
							ON (jd.journal_no=jh.journal_no
								AND jd.status_flag='3'
								#AND jd.amount>0
								AND jd.account_no='".$ci_acct_no."'							
								AND jd.debit_credit='D')
				  WHERE jh.transaction_date='".$transaction_date."'														
						AND jh.status_flag='3'	
				  ORDER BY jh.particulars ASC";
				
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
    	
        return $this->checkError($result->result_array(), $count, $query);
    }
    
 	/**
	 * @desc To retrieve Accounts which have transactions for the given date
	 * @return array
	 */
    function retrieveAccountsForDisbursement($transaction_date, $ci_acct_no='1010')
    {
		$sql = "SELECT DISTINCT tjd.account_no
					, tjd.debit_credit
				FROM t_journal_detail tjd
				INNER JOIN t_journal_header tjh
				ON (tjh.journal_no=tjd.journal_no
				AND tjh.transaction_date='$transaction_date'
				AND tjh.status_flag='3')
				WHERE #tjd.amount>0
				tjd.status_flag='3'
				AND particulars 
				IN (SELECT DISTINCT particulars 
					FROM t_journal_header 
					INNER JOIN t_journal_detail 
					ON (t_journal_detail.journal_no=t_journal_header.journal_no 
						AND t_journal_detail.account_no='$ci_acct_no'
						AND t_journal_header.transaction_date='$transaction_date'	
						AND t_journal_detail.debit_credit='C'
						AND t_journal_detail.status_flag='3')
					WHERE t_journal_header.status_flag='3')
				ORDER BY tjd.account_no ASC";		
    	
    	$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
    	
        return $this->checkError($result->result_array(), $count, $query);
    }
	
	function retrieveAccountsForDisbursementWithLimit($transaction_date, $ci_acct_no='1010', $offset, $limit)
    {
		$sql = "SELECT DISTINCT tjd.account_no
					, tjd.debit_credit
				FROM t_journal_detail tjd
				INNER JOIN t_journal_header tjh
				ON (tjh.journal_no=tjd.journal_no
				AND tjh.transaction_date='$transaction_date'
				AND tjh.status_flag='3')
				WHERE #tjd.amount>0
				tjd.status_flag='3'
				AND particulars 
				IN (SELECT DISTINCT particulars 
					FROM t_journal_header 
					INNER JOIN t_journal_detail 
					ON (t_journal_detail.journal_no=t_journal_header.journal_no 
						AND t_journal_detail.account_no='$ci_acct_no'
						AND t_journal_header.transaction_date='$transaction_date'	
						AND t_journal_detail.debit_credit='C'
						AND t_journal_detail.status_flag='3')
					WHERE t_journal_header.status_flag='3')
				ORDER BY tjd.account_no ASC
				LIMIT {$offset}, {$limit}";		
    	
    	$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
    	
        return $this->checkError($result->result_array(), $count, $query);
    }
    
 	/**
	 * @desc To retrieve Accounts which have transactions for the given date
	 * @return array
	 */
    function retrieveAccountsForAdjustment($transaction_date,$ci_acct_no,$user=null)
    {
    	if ($user==null){
			$sql = "SELECT DISTINCT tjd.account_no 
						,tjd.debit_credit
					FROM t_journal_header jh 
						LEFT JOIN (SELECT DISTINCT journal_no FROM t_journal_detail WHERE account_no='$ci_acct_no' AND status_flag='3') x
							ON jh.journal_no=x.journal_no 
						LEFT OUTER JOIN t_journal_detail tjd
							ON (tjd.journal_no=jh.journal_no AND tjd.status_flag='3')
					WHERE x.journal_no IS NULL 
						AND tjd.account_no IS NOT NULL 
						AND jh.transaction_date='$transaction_date'
						AND jh.status_flag='3'
						AND particulars
						IN (SELECT DISTINCT t_journal_header.particulars AS particulars								
							  FROM t_journal_header 								
									LEFT JOIN (SELECT DISTINCT journal_no FROM t_journal_detail WHERE account_no='$ci_acct_no' AND status_flag='3') x 						
										ON t_journal_header.journal_no=x.journal_no 						
							  WHERE t_journal_header.transaction_date='$transaction_date'								
									AND x.journal_no IS NULL
									AND t_journal_header.status_flag='3')
					ORDER BY tjd.account_no";
		}
		else {
			$sql = "SELECT DISTINCT tjd.account_no 
						,tjd.debit_credit
					FROM t_journal_header jh 
						LEFT JOIN (SELECT DISTINCT journal_no FROM t_journal_detail WHERE account_no='$ci_acct_no' AND status_flag='3') x 						
							ON jh.journal_no=x.journal_no 
						LEFT OUTER JOIN t_journal_detail tjd
							ON (tjd.journal_no=jh.journal_no AND tjd.status_flag='3')
					WHERE x.journal_no IS NULL
						AND jh.transaction_date='$transaction_date'
						AND jh.status_flag='3'
						AND particulars
						IN (SELECT DISTINCT t_journal_header.particulars AS particulars								
							  FROM t_journal_header 								
									LEFT JOIN (SELECT DISTINCT journal_no FROM t_journal_detail WHERE account_no='$ci_acct_no' AND status_flag='3') x 						
										ON t_journal_header.journal_no=x.journal_no 						
							  WHERE t_journal_header.transaction_date='$transaction_date'	
									AND t_journal_header.modified_by='$user'
									AND x.journal_no IS NULL
									AND t_journal_header.status_flag='3')
					ORDER BY tjd.account_no";
		}
		
    	$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
    	
        return $this->checkError($result->result_array(), $count, $query);
    }
    
	function retrieveAccountsForAdjustmentWithLimit($transaction_date,$ci_acct_no,$user=null, $offset, $limit)
    {
    	if ($user==null){
			$sql = "SELECT DISTINCT tjd.account_no 
						,tjd.debit_credit
					FROM t_journal_header jh 
						LEFT JOIN (SELECT DISTINCT journal_no FROM t_journal_detail WHERE account_no='$ci_acct_no' AND status_flag='3') x
							ON jh.journal_no=x.journal_no 
						LEFT OUTER JOIN t_journal_detail tjd
							ON (tjd.journal_no=jh.journal_no AND tjd.status_flag='3')
					WHERE x.journal_no IS NULL 
						AND tjd.account_no IS NOT NULL
						AND jh.transaction_date='$transaction_date'
						AND jh.status_flag='3'
						AND particulars
						IN (SELECT DISTINCT t_journal_header.particulars AS particulars								
							  FROM t_journal_header 								
									LEFT JOIN (SELECT DISTINCT journal_no FROM t_journal_detail WHERE account_no='$ci_acct_no' AND status_flag='3') x 						
										ON t_journal_header.journal_no=x.journal_no 						
							  WHERE t_journal_header.transaction_date='$transaction_date'								
									AND x.journal_no IS NULL
									AND t_journal_header.status_flag='3')
					ORDER BY tjd.account_no
					LIMIT {$offset}, {$limit}";
		}
		else {
			$sql = "SELECT DISTINCT tjd.account_no 
						,tjd.debit_credit
					FROM t_journal_header jh 
						LEFT JOIN (SELECT DISTINCT journal_no FROM t_journal_detail WHERE account_no='$ci_acct_no' AND status_flag='3') x 						
							ON jh.journal_no=x.journal_no 
						LEFT OUTER JOIN t_journal_detail tjd
							ON (tjd.journal_no=jh.journal_no AND tjd.status_flag='3')
					WHERE x.journal_no IS NULL 
						AND jh.transaction_date='$transaction_date'
						AND jh.status_flag='3'
						AND particulars
						IN (SELECT DISTINCT t_journal_header.particulars AS particulars								
							  FROM t_journal_header 								
									LEFT JOIN (SELECT DISTINCT journal_no FROM t_journal_detail WHERE account_no='$ci_acct_no' AND status_flag='3') x 						
										ON t_journal_header.journal_no=x.journal_no 						
							  WHERE t_journal_header.transaction_date='$transaction_date'	
									AND t_journal_header.modified_by='$user'
									AND x.journal_no IS NULL
									AND t_journal_header.status_flag='3')
					ORDER BY tjd.account_no
					LIMIT {$offset}, {$limit}";
		}
		
    	$result = $this->db->query($sql);    
    	$query = $this->db->last_query()."<br>";
		$count = $result->num_rows();
    	
        return $this->checkError($result->result_array(), $count, $query);
    }
	
 	/**
	 * @desc To retrieve Accounts which have transactions for the given date
	 * @return array
	 */
    function retrieveAccountsForCollection($transaction_date,$ci_acct_no='1010')
    {
    	$sql = "SELECT DISTINCT tjd.account_no
					, tjd.debit_credit
				FROM t_journal_detail tjd
				INNER JOIN t_journal_header tjh
				ON (tjh.journal_no=tjd.journal_no
				AND tjh.transaction_date='$transaction_date')
				WHERE #tjd.amount>0
				particulars 
				IN (SELECT DISTINCT particulars 
					FROM t_journal_header 
					INNER JOIN t_journal_detail 
					ON (t_journal_detail.journal_no=t_journal_header.journal_no 
					AND t_journal_detail.account_no='$ci_acct_no'
					AND t_journal_header.transaction_date='$transaction_date'	
					AND t_journal_detail.debit_credit='D'))
				ORDER BY tjd.account_no ASC";
    	
    	$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
    	
        return $this->checkError($result->result_array(), $count, $query);
    }
    
	/**
	 * @desc To retrieve Accounts which have transactions for the given date with given limits
	 * @return array
	 */
    function retrieveAccountsForCollectionWithLimit($transaction_date,$ci_acct_no='1010', $offset, $limit)
    {
    	$sql = "SELECT DISTINCT tjd.account_no
				, tjd.debit_credit
				FROM t_journal_detail tjd
				INNER JOIN t_journal_header tjh
				ON (tjh.journal_no=tjd.journal_no
				AND tjh.transaction_date='$transaction_date')
				WHERE #tjd.amount>0
				particulars 
				IN (SELECT DISTINCT particulars 
					FROM t_journal_header 
					INNER JOIN t_journal_detail 
					ON (t_journal_detail.journal_no=t_journal_header.journal_no 
					AND t_journal_detail.account_no='$ci_acct_no'
					AND t_journal_header.transaction_date='$transaction_date'	
					AND t_journal_detail.debit_credit='D'))
				ORDER BY tjd.account_no ASC
				LIMIT {$offset}, {$limit}";
    	
    	$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
    	
        return $this->checkError($result->result_array(), $count, $query);
    }
	
 	/**
	 * @desc To retrieve accounts of a single particular
	 * @return array
	 */
    function retrieveAccountsByParticularForDisbursement($transaction_date,$particulars)
    {
    	$sql = "SELECT jd.account_no AS account_no		
					,jd.debit_credit
					,SUM(ROUND(jd.amount,2)) AS amount		
				FROM t_journal_header jh 			
					INNER JOIN t_journal_detail jd 		
						ON (jh.journal_no=jd.journal_no 
						#AND jd.amount>0	
						)	
				WHERE jh.transaction_date='".$transaction_date."'			
					AND jh.particulars=".$this->db->escape($particulars)."	
					AND jh.status_flag='3'		
				GROUP BY jd.account_no, jd.debit_credit";
    	
    	$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
    	$count = $result->num_rows();
    	
        return $this->checkError($result->result_array(), $count, $query);
    }
    
 	/**
	 * @desc To retrieve accounts of a single particular
	 * @return array
	 */
    function retrieveAccountsByParticularForAdjustment($transaction_date,$particulars, $ci_acct_no)
    {
    	$sql = "SELECT tjd.account_no 
					,tjd.debit_credit
    				,SUM(ROUND(tjd.amount,2)) AS amount
				FROM t_journal_header jh 
					LEFT JOIN (SELECT DISTINCT journal_no FROM t_journal_detail WHERE account_no='".$ci_acct_no."') x
						ON jh.journal_no=x.journal_no 
					LEFT OUTER JOIN t_journal_detail tjd
						ON (tjd.journal_no=jh.journal_no)
				WHERE x.journal_no IS NULL 
					AND jh.transaction_date='".$transaction_date."'
					AND jh.status_flag='3'
					AND jh.particulars=".$this->db->escape($particulars)."
				GROUP BY tjd.account_no, tjd.debit_credit		
				ORDER BY tjd.account_no";
    	
    	$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
    	$count = $result->num_rows();
    	
        return $this->checkError($result->result_array(), $count, $query);
    }
    
 	/**
	 * @desc To retrieve accounts of a single particular
	 * @return array
	 */
    function retrieveAccountsByParticularForCollection($transaction_date,$particulars)
    {
    	$sql = "SELECT jd.account_no AS account_no	
					,jd.debit_credit
					,SUM(ROUND(jd.amount,2)) AS amount		
				FROM t_journal_header jh 			
					INNER JOIN t_journal_detail jd 		
						ON (jh.journal_no=jd.journal_no)	
				WHERE jh.transaction_date='".$transaction_date."'			
					AND jh.particulars=".$this->db->escape($particulars)."
					#AND jd.amount>0	
					AND jh.status_flag='3'		
				GROUP BY jd.account_no, jd.debit_credit";
    	
    	$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
    	$count = $result->num_rows();
    	
        return $this->checkError($result->result_array(), $count, $query);
    }
    
}

?>
