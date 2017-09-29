<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Mloanpayment_model extends Asi_Model {
	
    function Mloanpayment_model()
    {
        parent::Asi_Model();
        $this->table_name = 'm_loan_payment';
        $this->id = array('loan_no', 'transaction_code', 'payment_date', 'payor_id');
        $this->date_columns = array('');
    }
    
    function updateLoanPayrollDeduction($cur_date, $user){
		$year = substr($cur_date, 0, 4);
    	$month = substr($cur_date, 4, 2);
    	$now = date("YmdHis");
    	$sql = "UPDATE t_transaction tt
				INNER JOIN (SELECT loan_no
						,transaction_code
						,payment_date
						,payor_id
						,CONCAT(loan_no, ',', transaction_code, ',', payment_date, ',', payor_id) AS reference
						FROM m_loan_payment												
						WHERE payment_date LIKE '{$year}{$month}%'
						AND source = 'P'
						AND status_flag = '2')	tlp2
					ON tt.reference = tlp2.reference	
				INNER JOIN m_loan_payment tlp
					ON tlp2.loan_no = tlp.loan_no
						AND tlp2.transaction_code = tlp.transaction_code
						AND tlp2.payment_date = tlp.payment_date
						AND tlp2.payor_id = tlp.payor_id
				SET tlp.payment_date = '{$cur_date}'
				, tlp.modified_by = '{$user}'
				, tlp.modified_date = '{$now}'
				WHERE tt.status_flag = '2'";					
    	$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
    	
//    	echo  $this->db->_error_message();
    	
    	return $this->checkError('','',$query);  	
    }
    
 	function batchInsert(){
    	$sql = "INSERT INTO m_loan_payment
					( loan_no
					, payment_date
					, transaction_code
					, payor_id
					, or_no
					, or_date
					, amount
					, interest_amount
					, source
					, remarks
					, balance
					, status_flag
					, created_by
					, created_date
					, modified_by
					, modified_date )
				SELECT loan_no
					, payment_date
					, transaction_code
					, payor_id
					, or_no
					, or_date
					, amount
					, interest_amount
					, source
					, remarks
					, balance
					, '2'
					, created_by
					, created_date
					, modified_by
					, modified_date 
				FROM t_loan_payment
				WHERE status_flag = '1'";
    	
    	$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
    	
    	return $this->checkError('','',$query);
    }
	
	function getLoanPaymentList($filter=null, $limit = null, $offset = null, $select = null, $orderby = null, $loan_no = null){
		/*if($select)
			$this->db->select($select);
		$this->db->from('m_loan_payment ml');
		$this->db->join('r_transaction rl', 'rl.transaction_code = ml.transaction_code', 'inner');
		$this->db->join('t_transaction tt', "tt.reference = CONCAT(ml.loan_no, ',', ml.transaction_code, ',', ml.payment_date, ',', ml.payor_id)", 'left outer');		
		//$this->db->join("(SELECT reference FROM m_transaction tt WHERE reference LIKE '$loan_no,%') tt", "tt.reference = CONCAT(ml.loan_no, ',', ml.transaction_code, ',', ml.payment_date, ',', ml.payor_id)", 'inner');		
	
		if ($filter)
			$this->db->where($filter);
	
		if($orderby)	
    		$this->db->orderby($orderby);
		$this->db->limit($offset,$limit);

		$result = $this->db->get();
			
		$query = $this->db->last_query();
		log_message('debug','xx-xx'.$query);
			
		if($filter){
			$this->db->where($filter);
			//$this->db->from($this->table_name);
			$this->db->from('m_loan_payment ml');
			$this->db->join('r_transaction rl', 'rl.transaction_code = ml.transaction_code', 'inner');
			$this->db->join('t_transaction tt', "tt.reference = CONCAT(ml.loan_no, ',', ml.transaction_code, ',', ml.payment_date, ',', ml.payor_id)", 'left outer');		
			$count = $this->db->count_all_results();
		}else{
			$count = $this->db->count_all($this->table_name);
		}*/
		//note: online query is different from admin in terms of getting the posted loans
		$sql = "SELECT DATE_FORMAT(ml.payment_date,'%m%d%Y') AS payment_date
					, ml.amount
					, ml.interest_amount AS interest
					, rl.transaction_description AS transaction_description
					, ml.balance AS balance 
					,  ml.payor_id
					,tt.reference
				FROM (m_loan_payment ml) 
				LEFT OUTER JOIN r_transaction rl 
					ON rl.transaction_code = ml.transaction_code 
				LEFT OUTER JOIN t_transaction tt 
					ON tt.reference = CONCAT(ml.loan_no, ',', ml.transaction_code, ',', ml.payment_date, ',', ml.payor_id) 
				WHERE loan_no = '$filter' 
				AND ml.status_flag = '2' 
				AND ml.source <> 'B' 
				AND tt.reference IS NULL";
		
		$sql2 = "";
		
		if($orderby!=NULL){
			$sql2 = $sql." ORDER BY ".$orderby;
			$sql2 .= " LIMIT ".$limit.", ".$offset;
		}
		else{
			$sql2 = $sql." LIMIT ".$limit.", ".$offset;
		}

		$result = $this->db->query($sql2); 	
    	$query = $this->db->last_query();
		log_message('debug','xx-xx'.$query);
		if($filter){
			$resultCount = $this->db->query($sql);
			$count = $resultCount->num_rows();	
		}
		else{
			$count = $this->db->count_all($this->table_name);
		}
		
		return $this->checkError($result->result_array(), $count, $query);
		
	}
	
	function generateOR($currdate = '', $pk = null){
		$sql = "";
		
		if (isset($pk)){
			$loan_no = $pk[0];
			$tran_code = $pk[1];
			$payor= $pk[2];
			$pay_date= $pk[3];
			$sql = "SELECT mlp.or_date
					, mlp.or_no
					, mlp.payor_id
					, mm.first_name
					, mm.last_name
					, mm.middle_name
					, mlp.interest_amount
					#, mlp.amount - COALESCE(mlpd.amount,0) AS loanpayment
					, mlp.amount
					, COALESCE(mlpd.amount,0) AS charges
				FROM t_loan_payment mlp
				LEFT OUTER JOIN t_loan_payment_detail mlpd
				ON (mlpd.loan_no = mlp.loan_no) 
				INNER JOIN m_employee mm
					ON(mm.employee_id = mlp.payor_id)
				INNER JOIN r_transaction rt															
					ON rt.transaction_code = mlp.transaction_code
				WHERE mlp.or_date = '{$currdate}'
					AND rt.with_or = 'Y'
					AND mlp.loan_no = '$loan_no'
					AND mlp.transaction_code = '$tran_code'
					AND mlp.payor_id = '$payor'
					AND mlp.payment_date = '$pay_date'
					AND mlp.status_flag = '1'
				ORDER BY  mm.first_name
					, mm.middle_name
					, mm.last_name";
		} else{
			$sql = "SELECT mlp.or_date
					, mlp.or_no
					, mlp.payor_id
					, mm.first_name
					, mm.last_name
					, mm.middle_name
					, mlp.interest_amount
					#, mlp.amount - COALESCE(mlpd.amount,0) AS loanpayment
					, mlp.amount
					, COALESCE(mlpd.amount,0) AS charges
				FROM m_loan_payment mlp
				LEFT OUTER JOIN m_loan_payment_detail mlpd
				ON (mlpd.loan_no = mlp.loan_no) 
				INNER JOIN m_employee mm
					ON(mm.employee_id = mlp.payor_id)
				INNER JOIN r_transaction rt															
					ON rt.transaction_code = mlp.transaction_code
				WHERE mlp.or_date = '{$currdate}'
					AND rt.with_or = 'Y'
					AND mlp.status_flag = '2'
				ORDER BY  mm.first_name
					, mm.middle_name
					, mm.last_name";
		}
    	
    	$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
    	
    	return $this->checkError($result->result_array(),$count,$query);
	}
    
	/*
	*	@desc Used in Payroll Deduction Audit Trail Report
	*/
    function retrievePDEmployeeGuarantee($employee_id, $transaction_date)
	{
		$sql = "SELECT COALESCE(SUM(amount+interest_amount),0) AS guarantee
				FROM m_loan_payment mlp
					INNER JOIN m_loan ml
						ON (mlp.loan_no=ml.loan_no AND ml.status_flag='2')
				WHERE mlp.payor_id='$employee_id'
					AND mlp.source='P' 
					AND mlp.payor_id<>ml.employee_id
					AND mlp.payment_date LIKE '$transaction_date%'
					AND mlp.status_flag='2'
				GROUP BY mlp.payor_id";
		
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);			
	}
	
	function retrievePDEmployeesGuarantee($transaction_date)
	{
		$sql = "SELECT mlp.payor_id as employee_id, COALESCE(SUM(amount+interest_amount),0) AS guarantee
				FROM m_loan_payment mlp
					INNER JOIN m_loan ml
						ON (mlp.loan_no=ml.loan_no AND ml.status_flag='2')
				WHERE mlp.source='P' 
					AND mlp.payor_id<>ml.employee_id
					AND mlp.payment_date LIKE '$transaction_date%'
					AND mlp.status_flag='2'
				GROUP BY mlp.payor_id";
		
		$result = $this->db->query($sql);    
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);			
	}
	
}

?>
