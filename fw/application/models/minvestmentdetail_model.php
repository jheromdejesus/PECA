<?php

class Minvestmentdetail_model extends Asi_Model {
	
	var $table_name = 'm_investment_detail';
	var $id = 'investment_no';
	var $model_data = null;
	var $date_columns = array('');
	
    function Minvestmentdetail_model(){
    	
        parent::Asi_Model();
        
    }

    
}

?>
