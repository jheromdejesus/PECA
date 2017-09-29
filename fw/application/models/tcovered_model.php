<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Tcovered_model extends Asi_Model {
	
    function Tcovered_model()
    {
        parent::Asi_Model();
        $this->table_name = 't_amla_covered';
        $this->id = 'transaction_no';
        $this->date_columns = array('');
    }
    
    function saveAmla($tran_no, $tran_date, $tran_code, $tran_amount, $employee_id, $amla_code, $user){
    	$covered_array['transaction_no'] = $tran_no;
    	$covered_array['transaction_date'] = $tran_date;
    	$covered_array['transaction_code'] = $tran_code;
    	$covered_array['amla_code'] = $amla_code;
    	$covered_array['employee_id'] = $employee_id;
    	$covered_array['transaction_amount'] = $tran_amount; 
    	$covered_array['created_by'] = $user;
    	$covered_array['modified_by'] = $user;

    	//insert here
    	$this->populate($covered_array);
    	$result = $this->insert();
    	
    	return $result;
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
				FROM t_amla_covered tc																		
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
}

?>
