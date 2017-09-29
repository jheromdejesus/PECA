<?php

class Ouser_model extends Asi_Model {
	var $table_name = 'o_user';
	var $id = 'user_id';
	var $model_data = null;
	var $date_columns = array();
	
    function Ouser_model()
    {
        parent::Asi_Model();
    }    
}
/* End of file ouser_model.php */
/* Location: ./system/application/models/ouser_model.php */