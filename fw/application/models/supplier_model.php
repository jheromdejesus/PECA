<?php

class Supplier_model extends Asi_Model {
	var $table_name = 'm_supplier';
	var $id = 'supplier_id';
	var $model_data = null;
	var $date_columns = array('');
	
	
    function Supplier_model()
    {
        parent::Asi_Model();
    }

	/**
	 * @desc To get information of a supplier/bank
	 * @param supplier_id
	 * @return array
	 */
	function getSupplierInfo($supplier_id, $rows)
	{
		$data = $this->supplier_model->get(array('supplier_id' => $supplier_id)
			,$rows
		);
		return $data['list'][0];
	}
	
	/**
	 * @desc To check the existence of the supplier_id
	 * @param supplier_id
	 * @return 1- exist; 0- doesn't exist
	 */
	function supplierIdExists($supplier_id)
	{
		$data = $this->get(array('status_flag'=>'1', 'supplier_id' => $supplier_id), array('supplier_id'));
		if($data['count'] == 1)	
			if($data['list'][0]['supplier_id'] == $supplier_id)
				return 1;
		return 0;
	}
	
}
?>