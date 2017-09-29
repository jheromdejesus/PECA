<?php 

class Tblnpmlsuspension_model extends Asi_Model {
	var $table_name = 'tbl_npmlsuspension';
	var $id = 'entry_id';
	var $model_data = null;
	var $date_columns = array();
	
	
    function Tblnpmlsuspension_model()
    {
        parent::Asi_Model();
    }
	
	function getSixMonthsSuspensionRec($employee_id,$currDate)
	{		
		$temp_date = date('Ym', strtotime('-5 month', strtotime($currDate))); 
		$dateSixMonthsAgo = $temp_date."01";
		
		$sql = "SELECT * from tbl_npmlsuspension
				WHERE employee_id = '$employee_id'
				AND suspension_type = 'NPML'
				AND suspended_date >= '$dateSixMonthsAgo'
				AND suspended_date <= '$currDate'
				";
		$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
		return $this->checkError($result->result_array(), $count, $query);
	}
    
}
?>