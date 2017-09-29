<?php

class Glentrydetail_model extends Asi_Model {
	var $table_name = 'r_gl_entry_detail';
	var $id = 'gl_code';
	var $model_data = null;
	var $date_columns = array('');
	
	
    function Glentrydetail_model()
    {
        parent::Asi_Model();
    }
	
	function retrieveGLEntryDetails($transaction_code){
		$sql = "SELECT ged.account_no
					,CONCAT(ged.account_no, ' - ', ra.account_name) as account_name
					,ged.debit_credit
				FROM r_gl_entry_detail ged
				INNER JOIN r_account ra
					ON(ra.account_no = ged.account_no AND ra.status_flag = 1)
				WHERE ged.gl_code = '{$transaction_code}'
				AND ged.status_flag = 1";
				
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
    	
        return $this->checkError($result->result_array(), $count, $query); 
	}
	
	##### NRB EDIT START #####
	function retrieveGLEntryDetailsFieldName($s_field_name, $s_loan_code){
		#get transaction code of loan code
		$s_transcode_sql = 'SELECT transaction_code FROM r_loan_header WHERE loan_code = \''.$s_loan_code.'\'';
		$r_transcode = $this->db->query($s_transcode_sql);
		$a_transcode = $r_transcode->result_array();
		$s_transaction_code = $a_transcode[0]['transaction_code'];
		
		#get gl code of transaction code
		$s_glcode_sql = 'SELECT gl_code FROM r_transaction WHERE transaction_code = \''.$s_transaction_code.'\'';
		$r_glcode = $this->db->query($s_glcode_sql);
		$a_glcode = $r_glcode->result_array();
		$s_gl_code = $a_glcode[0]['gl_code'];
		
		$s_gl_detail_sql = "SELECT ged.account_no
								,CONCAT(ged.account_no, ' - ', ra.account_name) as account_name
							FROM r_gl_entry_detail ged
							INNER JOIN r_account ra
								ON(ra.account_no = ged.account_no AND ra.status_flag = 1)
							WHERE ged.field_name = '{$s_field_name}'
							AND ged.gl_code = '{$s_gl_code}'
							AND ged.status_flag = 1";
							

		$r_gl_detail = $this->db->query($s_gl_detail_sql);		
    	$s_query = $this->db->last_query();
		$i_count = $r_gl_detail->num_rows();
    	
        return $this->checkError($r_gl_detail->result_array(), $i_count, $s_query); 

	}
	
	function retrieveGLEntryAccountName($s_account_no){
		$s_account_name_sql = "SELECT DISTINCT(ged.account_no)
							,CONCAT(ged.account_no, ' - ', ra.account_name) as account_name
						FROM r_gl_entry_detail ged
						INNER JOIN r_account ra
							ON(ra.account_no = ged.account_no)
						WHERE ged.account_no = '".$s_account_no."'";

		$r_account_name = $this->db->query($s_account_name_sql);		
    	$s_query = $this->db->last_query();
		$i_count = $r_account_name->num_rows();
    	
    	return $r_account_name->result_array();
	}	
	##### NRB EDIT END #####
}
?>