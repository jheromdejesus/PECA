<?php

class Product_model extends Model {
	var $table_name = 'tbl_product';
	var $id = 'product_id';
	var $_data = null;
	
    function Product_model()
    {
        parent::Model();
    }
    
    function _setValue($col_name, $col_value)
    {
    	$this->_data[$col_name] = $col_value;
    }
    
    function _getValue($col_name)
    {
    	return $this->_data[$col_name];
    }
    
    function _get()
    {
    	return $this->db->get($this->table_name);
    }
    
    function _insert($data)
    {
		$this->db->insert($this->table_name, $data);
    }
    
    function _update($data)
    {
    	$this->db->where($this->id,$data[$this->id]);
    	$this->db->update($this->table_name, $data);
    }
    
    function _delete($data)
    {
    	$this->db->where($this->id,$data[$this->id]);
    	$this->db->delete($this->table_name);
    }
}