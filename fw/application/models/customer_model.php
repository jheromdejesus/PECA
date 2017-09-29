<?php

class Customer_model extends Model {
	var $table_name = 'tbl_customer';
	var $id = 'customer_id';
	var $model_data = null;
	var $date_columns = array('birth_date');
	
	
    function Customer_model()
    {
        parent::Model();
    }
    
    function convertDateColumns(){
    
    	foreach($this->date_columns as $dateCol){
    		$search = array("/",":"," ");
    		$replace = array("");
    		$this->model_data[$dateCol] = str_replace($search, $replace, $this->model_data[$dateCol]);
    	}
    }
    
    function populate($data)
    {
    	$this->model_data = $data; 	
    }
    
    function setValue($col_name, $col_value)
    {
    	$this->model_data[$col_name] = $col_value;
    }
    
    function getValue($col_name)
    {
    	return $this->model_data[$col_name];
    }
    
    function get($customer_id)
    {
    	$result =  $this->db->get_where($this->table_name, array($this->id => $customer_id));
    	
	    foreach ($result->result_array() as $row)
		{
	    	$this->model_data = $row;
		}    	
		return $this->model_data;
    }
    
    function get_list($filter = null, $limit = null, $offset = null)
    {
    	$this->db->from($this->table_name);
    	if ($filter)
    		$this->db->where($filter); 
    	$this->db->limit($offset, $limit);
    	$result = $this->db->get();
    	
    	if ($filter) 
    	{
    		$this->db->where($filter);
    		$this->db->from($this->table_name);
    		$count = $this->db->count_all_results();
    	} else
    	{
    		$count = $this->db->count_all($this->table_name);
    	}
    	
    	return array(
    		'count' => $count,
    		'list' => $result->result_array() 
    	); 
    	
    }
    
    function insert($data)
    {	
    	$data['created_date'] = date("YmdHis");
    	$this->populate($data);
    	$this->convertDateColumns();
    	$ret = $this->db->insert($this->table_name, $this->model_data);
		return $ret;
    }
    
    function update($data)
    {
    	$data['updated_date'] = date("YmdHis");
    	$this->populate($data);
    	$this->convertDateColumns();
    	$this->db->where($this->id, $this->model_data[$this->id]);
    	$this->db->update($this->table_name, $this->model_data);
    }
    
    function delete($data)
    {
    	$this->db->where($this->id, $data[$this->id]);
    	$this->db->delete($this->table_name);
    }
}