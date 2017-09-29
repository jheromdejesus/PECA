<?php

class Beneficiary_model extends Asi_Model {
	var $table_name = 'm_beneficiary';
	var $id = 'member_id';
	var $model_data = null;
	var $date_columns = array();
	
	
    function Beneficiary_model(){
        parent::Asi_Model();
    }

}
?>