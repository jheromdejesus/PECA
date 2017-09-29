<?php

class Tblonlinenotification extends Asi_Model {
	var $date_columns = array('');
	
    function Tblonlinenotification()
    {
        parent::Asi_Model();
        $this->table_name = 'tbl_online_notification';
        $this->id = 'entry_id';
    }
	
	function readDistinctTableRef(){
		$sql = "Select DISTINCT(table_reference) AS table_reference
				From ".$this->table_name;
		$result = $this->db->query($sql);
		$result = $this->checkError($result->result_array(),$result->num_rows(),$sql);
		
		return $result;
	}
}
/* End of file user_model.php */
/* Location: ./system/application/models/user_model.php */	