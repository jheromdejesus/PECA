<?php

class Company_model extends Asi_Model {
	var $table_name = 'r_company';
	var $id = 'company_code';
	var $model_data = null;
	var $date_columns = array('');
	
	
    function Company_model()
    {
        parent::Asi_Model();
    }

}
?>