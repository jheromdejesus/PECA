<?php

class Comakerexceptionreport_model extends Asi_Model {
	var $table_name = '';
	var $id = '';
	var $model_data = null;
	var $date_columns = array('');
	
	
    function Comakerexceptionreport_model()
    {
        parent::Asi_Model();
    }
  
    /**
     * @desc Retrieve list of loan payment overdue regardless of capital balance of employees
     * @param $acctgPeriod
     */
	function getLoanNumOfGuarantorException($curr_date='20100127'){
		
		$sql = "SELECT DISTINCT exc1.loan_no AS loan_no							
					, l.loan_code AS loan_code						
					, l.employee_id AS employee_id						
					, m.last_name AS last_name
					, m.first_name AS first_name
					, l.principal_balance AS principal_balance
					, lg.guarantor_id AS guarantor_id
					, gn.last_name AS guarantor_last_name
					, gn.first_name AS guarantor_first_name
					, exc1.remark AS remark
				FROM 							
				(    SELECT l.loan_no							
					   ,CASE  							
						WHEN (((('".$curr_date."' - m.hire_date)/10000) < 10) AND (COALESCE(NoOfGuarantor,0) <> 2) AND loan_code = 'CONL') 					
							THEN  'Members with <10 YOS should have 2 comakers for Consumption Loan' 				
						WHEN (((( '".$curr_date."' - m.hire_date )/10000) BETWEEN 10 AND 14) AND (COALESCE(NoOfGuarantor,0) < 1 ) AND loan_code = 'CONL' ) 					
							THEN  'Members with 10-14 YOS should have at least 1 comaker for Consumption Loan' 				
						WHEN (((('".$curr_date."' - m.hire_date)/10000) >= 15 ) AND (COALESCE(NoOfGuarantor,0) <> 0) AND loan_code = 'CONL' ) 					
							THEN 'Members with >=15 YOS should have no Comaker for Consumption Loan' 				
						WHEN (((('".$curr_date."'- m.hire_date)/10000) <= 3 ) AND (COALESCE(NoOfGuarantor,0) <> 1 )  AND loan_code = 'SPCL' )					
							THEN 'Members with <=3 YOS should have 1 Comaker for Spot Cash Loan' 				
						WHEN (((('".$curr_date."' - m.hire_date)/10000) > 3 ) AND (COALESCE(NoOfGuarantor,0) <> 0 )  AND loan_code = 'SPCL' )					
							THEN 'Members with > 3 YOS don''t need a Comaker for Spot Cash Loan' 				
						ELSE ''					
					END  AS Remark						
					  FROM m_loan AS l 							
					  LEFT OUTER JOIN (SELECT loan_no, COUNT(*) AS NoOfGuarantor FROM m_loan_guarantor GROUP BY loan_no) AS lc 							
					ON (l.loan_no = lc.loan_nO)						
					 INNER JOIN m_employee AS m 							
					ON (l.employee_id = m.employee_id)						
				WHERE principal_balance > 0 							
						AND (l.status_flag  = 2 )					
						AND ((((('".$curr_date."'- m.hire_date)/10000) < 10) AND (COALESCE(NoOfGuarantor,0) <> 2) AND loan_code = 'CONL' ) 					
						OR (((('".$curr_date."' - m.hire_date)/10000) BETWEEN 10 AND 14) AND (COALESCE(NoOfGuarantor,0) < 1 ) AND loan_code = 'CONL' )					
						OR (((('".$curr_date."'- m.hire_date)/10000) >= 15 ) AND (COALESCE(NoOfGuarantor,0) <> 0) AND loan_code = 'CONL' )					
						OR (((('".$curr_date."'- m.hire_date)/10000) <= 3 ) AND (COALESCE(NoOfGuarantor,0) <> 1 )  AND loan_code = 'SPCL' )					
						OR (((('".$curr_date."' - m.hire_date)/10000) > 3 ) AND (COALESCE(NoOfGuarantor,0) <> 0 )  AND loan_code = 'SPCL' ))					
				) AS exc1 							
											
				INNER JOIN m_loan l 							
					ON (exc1.loan_no = l.loan_no)						
				LEFT OUTER JOIN m_loan_guarantor lg 							
					ON (exc1.loan_no = lg.loan_no)						
				LEFT OUTER JOIN m_employee gn 							
					ON (lg.guarantor_id = gn.employee_id)						
				INNER JOIN m_employee m 							
					ON (l.employee_id = m.employee_id)";
			
		return $sql;
	}
	
	/**
	 * @desc To retrieve inactive co-makers with guaranteed loans
	 */
    function getInactiveCoMakers()
    {
    	$sql = "SELECT l.loan_no AS loan_no			
						,l.loan_code AS loan_code		
						,l.employee_id AS employee_id		
						,m.last_name AS last_name		
						,m.first_name AS first_name		
						,l.principal_balance AS principal_balance
						,lg.guarantor_id AS guarantor_id		
						,gn.last_name AS guarantor_last_name		
						,gn.first_name AS guarantor_first_name		
						,'Co-Maker is no longer active' AS remark   		
				FROM m_loan l 			
				INNER JOIN m_employee m 			
					ON m.employee_id = l.employee_id			
				INNER JOIN  m_loan_guarantor lg
					ON l.loan_no = lg.loan_no			
				INNER JOIN  m_employee gn			
					ON gn.employee_id=lg.guarantor_id
				WHERE principal_balance > 0			
					AND (l.status_flag = 2)
					AND gn.member_status = 'I'";
			
		return $sql;
    }
	
	function getAll($sql1, $sql2, $sql3, $sql4)
	{
		$sql = "SELECT L.*
				FROM ( ".$sql1." 
				UNION
					".$sql2."
				UNION
					".$sql3."
				UNION
					".$sql4."
				) AS L
				ORDER BY L.loan_no, L.guarantor_last_name";
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
	}
}
?>