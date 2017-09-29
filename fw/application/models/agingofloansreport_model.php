<?php

class Agingofloansreport_model extends Asi_Model {
	var $table_name = 'm_loan';
	var $id = 'loan_no';
	var $model_data = null;
	var $date_columns = array('loan_date', 'amortization_startdate');
	
	
    function Agingofloansreport_model()
    {
        parent::Asi_Model();
    }
  
    /**
     * @desc Retrieve all active loans with maximum loan term of more than 12
     * @param $loan_desc
     */
	function getAgingOfLoans($loan_desc){
		$sql = "SELECT DISTINCT(loan_description) AS loan_description			
				, loan_no AS loan_no		
				, Aging.employee_id 		
				, last_name 		
				, first_name 
				, loan_code		
				, loan_date 		
				, principal 		
				, term 		
				, interest_rate 		
				, employee_principal_amort AS Employee_Principal_Amortization		
				, company_interest_amort AS Company_Int_Amortization		
				, amortization_startdate AS Amortization_Start_Date		
				, principal_balance AS Principal_Balance		
				, interest_balance AS Int_Balance		
				, CASE WHEN LongTerm1 < 50 THEN ShortTerm1 + LongTerm1 ELSE ShortTerm1 END AS Current		
				,CASE WHEN LongTerm1 < 50 THEN 0 ELSE LongTerm1 END AS Non_Current  		
			FROM ( 			
			   SELECT 			
					CASE WHEN (employee_principal_amort  * 12 < principal_balance) THEN (employee_principal_amort* 12) ELSE principal_balance END AS ShortTerm1		
					,principal_balance - CASE WHEN employee_principal_amort  * 12 < principal_balance THEN employee_principal_amort * 12 ELSE principal_balance END AS LongTerm1		
					,m_loan.*,r_loan_header.loan_description  		
			   FROM m_loan 			
			   INNER JOIN r_loan_header			
					ON  (m_loan.loan_code = r_loan_header.loan_code)		
			   WHERE principal_balance > 0 			
					AND m_loan.status_flag  IN ('2','3') 		
					AND r_loan_header.max_term > 12 		
			) AS Aging 			
			INNER JOIN m_employee AS Member 			
				ON (Aging.employee_id = Member.employee_id)		
			WHERE loan_description = '".$loan_desc."'		
			ORDER BY loan_no, last_name, first_name ASC			
			";
				
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
    	
        return $this->checkError($result->result_array(), $count, $query);
			
	}
	
	/**
     * @desc Retrieve list of loan descriptions to be used in read
     */
	function getDesc(){
		$sql = "SELECT DISTINCT(loan_description) AS loan_description			
				FROM ( 			
			   SELECT 			
					CASE WHEN (employee_principal_amort  * 12 < principal_balance) THEN (employee_principal_amort* 12) ELSE principal_balance END AS ShortTerm1		
					,principal_balance - CASE WHEN employee_principal_amort  * 12 < principal_balance THEN employee_principal_amort * 12 ELSE principal_balance END AS LongTerm1		
					,m_loan.*,r_loan_header.loan_description  		
			   FROM m_loan 			
			   INNER JOIN r_loan_header			
					ON  (m_loan.loan_code = r_loan_header.loan_code)		
			   WHERE principal_balance > 0 			
					AND m_loan.status_flag = 2 		
					AND r_loan_header.max_term > 12 		
			) AS Aging 			
			INNER JOIN m_employee AS Member 			
				ON (Aging.employee_id = Member.employee_id)		
			ORDER BY loan_description ASC			
			";
				
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
    	
        return $this->checkError($result->result_array(), $count, $query);
			
	}
	
}
?>