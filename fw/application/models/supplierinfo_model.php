<?php

class Supplierinfo_model extends Asi_Model {
	var $table_name = 'm_supplier_info';
	var $id = 'supplier_id';
	var $model_data = null;
	var $date_columns = array('');
	
	
    function Supplierinfo_model()
    {
        parent::Asi_Model();
    }
    
	function getInfo($filter = null, $limit = null, $offset = null, $select = null, $orderby = null){
		if($select)
			$this->db->select($select);
    	$this->db->from('m_supplier_info si');
    	$this->db->join('r_information ic', 'ic.information_code = si.info_code', 'inner');
		if ($filter)
			$this->db->where($filter);
			
		$clone_db = clone $this->db;
		$count = $clone_db->count_all_results();
		if($orderby)
			$this->db->order_by($orderby);
		$this->db->limit($offset, $limit);
		$result = $this->db->get();
		
		$query = $this->db->last_query();

		return $this->checkError($result->result_array(), $count, $query);
			
	}

}
?>