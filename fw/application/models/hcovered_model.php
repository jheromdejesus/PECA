<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Hcovered_model extends Asi_Model {
	
    function Hcovered_model()
    {
        parent::Asi_Model();
        $this->table_name = 'h_amla_covered';
        $this->id = 'transaction_no';
        $this->date_columns = array('');
    }
    
 	function retrieveAMLACoveredTrans($start_date, $end_date){
    	
    	$sql = "SELECT tc.transaction_date																		
					,tc.amla_code																	
					,tc.transaction_no																	
					,tc.employee_id																	
					,tc.transaction_amount																	
					,mm.last_name																	
					,mm.first_name																	
					,mm.middle_name																	
					,mm.address_1																	
					,mm.address_2																	
					,mm.address_3																	
					,mm.birth_date																	
					,mb.beneficiary																	
					,mb.beneficiary_address																	
				FROM h_amla_covered tc																		
				LEFT OUTER JOIN m_employee mm																		
					ON mm.employee_id = tc.employee_id 																	
				LEFT OUTER JOIN m_beneficiary mb												
					ON mb.member_id = mm.employee_id																	
				WHERE tc.transaction_date BETWEEN '$start_date' AND '$end_date'";
    	
		$result = $this->db->query($sql);
			
		$query = $this->db->last_query();
		$count = $result->num_rows();
	
		return $this->checkError($result->result_array(), $count, $query);
    }
    
 	function archiveAMLACoveredTrans($start_date, $end_date){
    	
    	$sql = "INSERT INTO h_amla_covered
					( transaction_no
						, transaction_date
						, employee_id
						, transaction_code
						, amla_code
						, transaction_amount
						, report_date
						, status_flag
						, created_by
						, created_date
						, modified_by
						, modified_date )
				SELECT transaction_no
						, transaction_date
						, employee_id
						, transaction_code
						, amla_code
						, transaction_amount
						, report_date
						, status_flag
						, created_by
						, created_date
						, modified_by
						, modified_date 
				FROM t_amla_covered
				WHERE transaction_date BETWEEN '$start_date' AND '$end_date'";
    	
    	$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
    	
    	return $this->checkError('','',$query);
    }
}

?>
