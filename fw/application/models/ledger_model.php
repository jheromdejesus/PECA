<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Ledger_model extends Asi_Model {
	
    function Ledger_model()
    {
        parent::Asi_Model();
        $this->table_name = 't_ledger';
        $this->id = array('accounting_period','account_no');
        $this->date_columns = array('');
    }
    
    function batchInsert($acctg_period, $user, $type){ 
    	$next_acctg_period = date('Ymd',strtotime("+1 month", strtotime($acctg_period))); 
    	$now = date("YmdHis");
    	
    	$sql = "INSERT INTO t_ledger						
					( account_no					
					, accounting_period					
					, beginning_balance					
					, debits					
					, credits					
					, ending_balance					
					, status_flag					
					, created_by					
					, created_date					
					, modified_by					
					, modified_date )";
    	
    	if ($type == 1){
    		$sql .= "SELECT ra.account_no						
					, '{$acctg_period}'					
					, 0					
					, 0					
					, 0					
					, 0					
					, '4'					
					,'{$user}'					
					,'{$now}'					
					,'{$user}'				
					,'{$now}'					
				FROM r_account ra						
				LEFT JOIN t_ledger tl						
					ON ra.account_no = tl.account_no AND tl.accounting_period = '{$acctg_period}'					
				WHERE tl.account_no IS NULL";
    	} else if ($type == 2){
    		$sql .= "SELECT tl.account_no							
						, '{$next_acctg_period}'						
						, tl.ending_balance						
						, 0						
						, 0						
						, tl.ending_balance
						, '4'					
						,'{$user}'					
						,'{$now}'					
						,'{$user}'				
						,'{$now}'					
					FROM t_ledger tl							
					WHERE tl.accounting_period = '{$acctg_period}'";
    	}
							
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
    	
    	return $this->checkError('','',$query);
    }
    
	function batchInsertCapcon($acctg_period, $user){ 
    	$next_acctg_period = date('Ymd',strtotime("+1 month", strtotime($acctg_period))); 
    	$now = date("YmdHis");
    	
    	$sql = "INSERT INTO t_ledger						
					( account_no					
					, accounting_period					
					, beginning_balance					
					, debits					
					, credits					
					, ending_balance					
					, status_flag					
					, created_by					
					, created_date					
					, modified_by					
					, modified_date) 
					
					SELECT ra.account_no						
					, '{$next_acctg_period}'					
					, 0					
					, 0					
					, 0					
					, 0					
					, '1'					
					,'{$user}'					
					,'{$now}'					
					,'{$user}'				
					,'{$now}'					
				FROM r_account ra						
				LEFT JOIN t_ledger tl						
					ON ra.account_no = tl.account_no AND tl.accounting_period = '{$next_acctg_period}'					
				WHERE tl.account_no IS NULL";
    					
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
    	
    	return $this->checkError('','',$query);
    }
	
    function batchUpdate($acctg_period, $user){
//    	$next_acctg_period = date('Ymd',strtotime("+1 month", strtotime($acctg_period))); 
    	$now = date("YmdHis");
    	
    	$sql = "UPDATE t_ledger tl
				LEFT OUTER JOIN (SELECT tjh.accounting_period											
							, tjd.account_no										
							, SUM(CASE WHEN tjd.debit_credit = 'D' THEN ROUND(tjd.amount,2) ELSE 0 END) AS debits										
							, SUM(CASE WHEN tjd.debit_credit = 'C' THEN ROUND(tjd.amount,2) ELSE 0 END) AS credits										
						FROM t_journal_header tjh											
						INNER JOIN t_journal_detail tjd											
							ON tjh.journal_no = tjd.journal_no										
						WHERE tjh.accounting_period = '{$acctg_period}'										
						GROUP BY tjh.accounting_period											
							, tjd.account_no) jdc
					ON jdc.accounting_period = tl.accounting_period AND jdc.account_no = tl.account_no
				SET										
					tl.debits = COALESCE(jdc.debits,0)									
					, tl.credits = COALESCE(jdc.credits,0)								
					, tl.ending_balance = tl.beginning_balance + COALESCE(jdc.debits,0) - COALESCE(jdc.credits,0)									
					, modified_by = '{$user}'									
					, modified_date = '{$now}'									
				WHERE tl.accounting_period = '{$acctg_period}'";
							
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
    	
    	return $this->checkError('','',$query);
    }
	
	function batchUpdateClosing($acctg_period, $user){
//    	$next_acctg_period = date('Ymd',strtotime("+1 month", strtotime($acctg_period))); 
    	$now = date("YmdHis");
    	
    	$sql = "UPDATE t_ledger tl
				LEFT OUTER JOIN (SELECT tjh.accounting_period											
							, tjd.account_no										
							, SUM(CASE WHEN tjd.debit_credit = 'D' THEN tjd.amount ELSE 0 END) AS debits										
							, SUM(CASE WHEN tjd.debit_credit = 'C' THEN tjd.amount ELSE 0 END) AS credits										
						FROM t_journal_header tjh											
						INNER JOIN t_journal_detail tjd											
							ON tjh.journal_no = tjd.journal_no										
						WHERE tjh.accounting_period = '{$acctg_period}'
							AND tjh.transaction_code = 'MCLS'
						GROUP BY tjh.accounting_period											
							, tjd.account_no) jdc
					ON jdc.accounting_period = tl.accounting_period AND jdc.account_no = tl.account_no
				SET										
					tl.close_debits = COALESCE(jdc.debits,0)									
					, tl.close_credits = COALESCE(jdc.credits,0)												
					, modified_by = '{$user}'									
					, modified_date = '{$now}'									
				WHERE tl.accounting_period = '{$acctg_period}'";
							
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
    	
    	return $this->checkError('','',$query);
    }
	
	function batchUpdate3050($acctg_period, $user){
//    	$next_acctg_period = date('Ymd',strtotime("+1 month", strtotime($acctg_period))); 
    	$now = date("YmdHis");
    	
    	$sql = "UPDATE t_ledger tl
				LEFT OUTER JOIN (SELECT l.accounting_period 
									, SUM(l.ending_balance) AS net								
						FROM t_ledger l
						INNER JOIN r_account ra
							ON ra.account_no = l.account_no
						WHERE accounting_period = '{$acctg_period}'
							AND ra.account_group IN ('I','E')
						GROUP BY l.accounting_period) tl2
					ON tl2.accounting_period = tl.accounting_period
				SET										
					tl.net_income = COALESCE(tl2.net,0)						
					#, tl.credits = tl.credits + COALESCE(tl2.credits,0)
					#, tl.ending_balance = ending_balance + (COALESCE(tl2.debits,0) - COALESCE(tl2.credits,0))
					, modified_by = '{$user}'									
					, modified_date = '{$now}'									
				WHERE tl.accounting_period = '{$acctg_period}'
					AND tl.account_no = '3050'";
							
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
    	
    	return $this->checkError('','',$query);
    }
	
	/**
	 * @desc To retrieve Assets, Liabilities and Capital based on the specified period.
	 * @return array
	 */
    function retrieveConsolidatedSOC($select = null, $filter = null, $limit = null, $offset = null,  $orderby = null) 
  	{
    	if($select)
    		$this->db->select($select);
    	$this->db->from('t_ledger tl');
    	$this->db->join('r_account ra','tl.account_no = ra.account_no','INNER');
		if($filter)	
    		$this->db->where($filter);
    	
		$clone_db = clone $this->db;
		$count = $clone_db->count_all_results();
		
		if($orderby)	
    		$this->db->orderby($orderby);	
    	$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
    	
		return $this->checkError($result->result_array(), $count, $query);    
	}
	
	/**
	 * @desc To retrieve income/expenses based on the specified period.
	 * @return array
	 */
	function retrieveConsolidatedSIE($period, $group)
	{
		$sql = "SELECT tl.account_no								
					,ra.account_name							
					,tl.accounting_period							
					,ROUND(tl.beginning_balance,2) AS beginning_balance							
					,COALESCE(jv.debit,ROUND(tl.debits,2)) AS debit							
					,COALESCE(jv.credit,ROUND(tl.credits,2)) AS credit							
					,COALESCE(tl.close_debits,0) AS close_debit							
					,COALESCE(tl.close_credits,0) AS close_credit							
					,tl.ending_balance							
				FROM t_ledger tl 								
					INNER JOIN r_account ra								
						ON ra.account_no = tl.account_no							
				LEFT OUTER JOIN (SELECT tjh.accounting_period								
					,tjd.account_no	
					,SUM((CASE debit_credit WHEN 'D' THEN ROUND(amount,2) ELSE 0 END)) AS debit	
					,SUM((CASE debit_credit WHEN 'C' THEN ROUND(amount,2) ELSE 0 END)) AS credit	
					FROM t_journal_header tjh 		
						INNER JOIN t_journal_detail tjd 		
							ON tjh.journal_no = tjd.journal_no	
					WHERE tjh.accounting_period = '$period'		
					GROUP BY tjh.accounting_period		
						, tjd.account_no) jv 	
					ON tl.accounting_period = jv.accounting_period AND tl.account_no = jv.account_no							
				WHERE tl.accounting_period = '$period'								
					AND ra.account_group = '$group'	
					AND (ROUND(tl.beginning_balance,2) + COALESCE(jv.debit,ROUND(tl.debits,2)) - COALESCE(jv.credit,ROUND(tl.credits,2))) <> 0
				ORDER BY tl.account_no ASC";
		
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);			
	}
	
	/**
	 * @desc To retrieve income/expenses based on the specified period.
	 * @return array
	 */
	function retrieveConsolidatedSIEforGLEntry($period)
	{
		//credit and debit are switched	
		$sql = "SELECT ra.account_no
					,tl.beginning_balance	
					,CONCAT_WS('-', ra.account_no , ra.account_name) AS account_name
					,COALESCE(jv.debit,tl.debits) AS debit 		 				
					,COALESCE(jv.credit,tl.credits) AS credit														
				FROM t_ledger tl 								
					INNER JOIN r_account ra								
						ON ra.account_no = tl.account_no AND ra.status_flag = 1						
				LEFT OUTER JOIN (SELECT tjh.accounting_period								
					,tjd.account_no	
					,SUM((CASE debit_credit WHEN 'D' THEN amount ELSE 0 END)) AS debit	
					,SUM((CASE debit_credit WHEN 'C' THEN amount ELSE 0 END)) AS credit	
					FROM t_journal_header tjh 		
						INNER JOIN t_journal_detail tjd 		
							ON tjh.journal_no = tjd.journal_no	
					WHERE tjh.accounting_period = '$period'		
					GROUP BY tjh.accounting_period		
						, tjd.account_no) jv 	
					ON tl.accounting_period = jv.accounting_period AND tl.account_no = jv.account_no							
				WHERE tl.accounting_period = '$period'								
					AND ra.account_group IN ('I','E')						
				ORDER BY tl.account_no ASC";
		
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);			
	}
	
	/**
	 * @desc To retrieve income/expenses based on the specified range.
	 * @return array
	 */
	function retrieveComparativeSIE($period, $group)
	{
		$sql = "SELECT tl.account_no								
					,ra.account_name							
					,tl.accounting_period							
					-- ,tl.debits + COALESCE(tj.debit,0) - COALESCE(tl.close_debits,0) 							
					-- 	- tl.credits - COALESCE(tj.credit,0) + COALESCE(tl.close_credits,0) AS comparative_balance						
					,(ROUND(tl.debits,2) + COALESCE(tj.debit,0) - COALESCE(tl.close_debits,0) - ROUND(tl.credits,2) - COALESCE(tj.credit,0) + COALESCE(tl.close_credits,0)) AS comparative_balance	
				FROM t_ledger tl 								
				INNER JOIN r_account ra								
					ON tl.account_no = ra.account_no							
				LEFT OUTER JOIN (SELECT jh.accounting_period								
										,jd.account_no		
										,SUM((CASE debit_credit WHEN 'D' THEN ROUND(amount,2) ELSE 0 END)) AS debit		
										,SUM((CASE debit_credit WHEN 'C' THEN ROUND(amount,2) ELSE 0 END)) AS credit		
									FROM t_journal_header jh 			
									INNER JOIN t_journal_detail jd 			
										ON jh.journal_no = jd.journal_no
									WHERE jh.transaction_code <> 'MCLS'
									GROUP BY jh.accounting_period, jd.account_no) tj 			
					ON tl.accounting_period = tj.accounting_period AND tl.account_no = tj.account_no							
				WHERE tl.accounting_period = '$period'														
					AND ra.account_group = '$group'	
					#AND ((tl.debits + COALESCE(tj.debit,0) - COALESCE(tl.close_debits,0) - tl.credits - COALESCE(tj.credit,0) + COALESCE(tl.close_credits,0))) <> 0	
				GROUP BY tl.account_no,								
					ra.account_name							
				ORDER BY tl.account_no ASC";
		
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		log_message('debug', 'zz-zz'.$query);
		
		return $this->checkError($result->result_array(), $count, $query);			
	}
	
	
	/**
	 * @desc To retrieve income/expenses based on the specified range.
	 * @return array
	 */
	function retrieveComparativeSOC($period, $group)
	{
	
		if ($group == 'I' || $group == 'E'){
			$sql = "SELECT  tl.account_no				
					,r_account.account_name			
					,tl.accounting_period			
					,tl.beginning_balance			
					,tl.debits			
					,tl.credits			
					,ROUND(tl.beginning_balance,2) + COALESCE(ROUND(tl.debits,2),0) - COALESCE(tl.close_debits,0) 
						- COALESCE(ROUND(tl.credits,2),0) + COALESCE(tl.close_credits,0) + COALESCE(tj.TotalAmt,0) AS ending_balance	
					,r_account.account_group
				FROM t_ledger tl  				
				INNER JOIN r_account				
					ON tl.account_no = r_account.account_no		
				LEFT OUTER JOIN (SELECT t.account_no												
									, t.accounting_period							
									,SUM(t.totalAmt) AS TotalAmt 
								FROM  (SELECT jd.account_no								
										, jh.accounting_period			
										,(CASE jd.debit_credit WHEN 'C' THEN  (ROUND(jd.Amount,2) * -1)  ELSE ROUND(jd.Amount,2) END ) AS totalAmt			
									FROM t_journal_header jh					
									LEFT OUTER JOIN t_journal_detail jd			
										ON (jh.journal_no=jd.journal_no)
									WHERE jh.transaction_code <> 'MCLS'
									#WHERE jh.transaction_date<='$period'
									) t 				
								GROUP BY t.account_no, t.accounting_period) tj
							ON tj.account_no = tl.account_no AND tj.accounting_period = tl.accounting_period
				WHERE tl.accounting_period = '$period' 			
					AND r_account.account_group = '$group'	
					#AND tl.ending_balance <> 0
				ORDER BY tl.account_no ASC";
		} else{
			$sql = "SELECT  tl.account_no				
					,r_account.account_name			
					,tl.accounting_period			
					,tl.beginning_balance			
					,tl.debits			
					,tl.credits			
					,ROUND(tl.ending_balance,2) + COALESCE(tj.TotalAmt,0) + COALESCE(ROUND(tl.net_income,2),0) AS ending_balance	
					#,tl.beginning_balance + tl.debits - tl.credits AS ending_balance	
					,r_account.account_group
				FROM t_ledger tl  				
				INNER JOIN r_account				
					ON tl.account_no = r_account.account_no		
				LEFT OUTER JOIN (SELECT t.account_no												
									, t.accounting_period							
									,SUM(t.totalAmt) AS TotalAmt 
								FROM  (SELECT jd.account_no								
										, jh.accounting_period			
										,(CASE jd.debit_credit WHEN 'C' THEN  (ROUND(jd.Amount,2) * -1)  ELSE ROUND(jd.Amount,2) END ) AS totalAmt			
									FROM t_journal_header jh					
									LEFT OUTER JOIN t_journal_detail jd			
										ON (jh.journal_no=jd.journal_no)
									WHERE jh.transaction_code <> 'MCLS'
									#WHERE jh.transaction_date<='$period'
									) t 				
								GROUP BY t.account_no, t.accounting_period) tj
							ON tj.account_no = tl.account_no AND tj.accounting_period = tl.accounting_period
				WHERE tl.accounting_period = '$period' 			
					AND r_account.account_group = '$group'	
					AND ((ROUND(tl.ending_balance,2) + COALESCE(tj.TotalAmt,0) <> 0 AND tl.account_no <> '3050') OR (tl.account_no = '3050'))
				ORDER BY tl.account_no ASC";
		}
		
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		log_message('debug', 'zz-zz'.$query);
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);			
	}
	
	function retrieveAccountList($from,$to,$group)
	{
		$sql = "SELECT  DISTINCT tl.account_no				
					,r_account.account_name
				FROM t_ledger tl  				
				INNER JOIN r_account				
					ON tl.account_no = r_account.account_no			
				WHERE tl.accounting_period BETWEEN '$from' AND '$to'			
					AND r_account.account_group = '$group'	
					AND tl.ending_balance <> 0
				ORDER BY tl.account_no ASC";
				
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);				
	}
	
	function retrieveAccountListSOI($from,$to,$group)
	{
		$sql = "SELECT  DISTINCT tl.account_no				
					,r_account.account_name
				FROM t_ledger tl  				
				INNER JOIN r_account				
					ON tl.account_no = r_account.account_no	
				WHERE tl.accounting_period BETWEEN '$from' AND '$to'			
					AND r_account.account_group = '$group'
					#AND tl.ending_balance <> 0
				ORDER BY tl.account_no ASC";
				
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);				
	}
	
}

?>