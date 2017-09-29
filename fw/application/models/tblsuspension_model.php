<?php

class Tblsuspension_model extends Asi_Model {
	var $table_name = 'tbl_suspension';
	var $id = 'entry_id';
	var $model_data = null;
	var $date_columns = array();
	
	
    function Tblsuspension_model()
    {
        parent::Asi_Model();
    }
    
}
?>