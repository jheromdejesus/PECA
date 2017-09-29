<?php 

class Mcloanguarantor_model extends Asi_Model {
	var $table_name = 'mc_loan_guarantor';
	var $id = 'loan_no';
	var $model_data = null;
	var $date_columns = array('');
	
	function Mcloanguarantor_model() {
        parent::Asi_Model();
    }

}


?>