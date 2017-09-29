<?php

class Journaldetail_model extends Asi_Model {
	var $table_name = 't_journal_detail';
	var $id = 'journal_no';
	var $model_data = null;
	var $date_columns = array('');
	
	
    function Journaldetail_model()
    {
        parent::Asi_Model();
    }

}
?>