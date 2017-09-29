<?php

class Information_model extends Asi_Model {
	var $table_name = 'r_information';
	var $id = 'information_code';
	var $model_data = null;
	var $date_columns = array('');
	
	
    function Information_model()
    {
        parent::Asi_Model();
    }

	/*function getGL(){
    	$result =  $result = $this->db->get('gl_entry');
   
		return $this->checkError($result->result_array());        */
}
?>