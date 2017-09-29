<?php

class Glentryheader_model extends Asi_Model {
	var $table_name = 'r_gl_entry_header';
	var $id = 'gl_code';
	var $model_data = null;
	var $date_columns = array('');
	
	
    function Glentryheader_model()
    {
        parent::Asi_Model();
    }

    function retrieveGLEntry($gl_code){
    	$sql = "SELECT rgh.gl_code
					, rgh.gl_description
					, rgh.particulars
					, rgd.account_no
					, ra.account_name
					, rgd.debit_credit
					, rgd.field_name
				FROM r_gl_entry_header rgh
				INNER JOIN r_gl_entry_detail rgd
					ON rgd.gl_code = rgh.gl_code
				INNER JOIN r_account ra
					ON ra.account_no = rgd.account_no
				WHERE rgh.gl_code = '{$gl_code}'";
		
    	$result = $this->db->query($sql);
  
//    	echo $this->db->last_query();
    	return $result->result_array();		
    }

}
?>