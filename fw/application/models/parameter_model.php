<?php

class Parameter_model extends Asi_Model {
	
	var $table_name = 'i_parameter_list';
	var $id = 'parameter_id';
	var $model_data = null;
	var $date_columns = array('');
	
    function Parameter_model() {
        parent::Asi_Model();
        $this->table_name = 'i_parameter_list';
        $this->id = 'parameter_id';
        $this->date_columns = array('');
		$this->load->library('constants');
    }

	/**
	 * @desc To get a specific parameter value
	 */
	function getParam($param_id){
		$data = $this->get(array('parameter_id' => $param_id)
		,array('parameter_value')
		);
		if(!isset($data['list'][0]['parameter_value'])) 
			return "";
		else 
			return $data['list'][0]['parameter_value'];
	}
	
	

	function retrieveValue($id){
		$result = $this->get(array('parameter_id' => $id), 'parameter_value');   	
    	$retval = isset($result['list'][0]['parameter_value']) ? $result['list'][0]['parameter_value'] : '';
    	if ($id == 'ACCPERIOD' || $id == 'CURRDATE'){
    		$retval = date('Ymd',strtotime($retval));
    	}
    	return $retval;
    }

	// function retrieveValue($id){
		// $lock = in_array($id, $this->constants->lock_params);
		// $lock_stmt = $lock ? "FOR UPDATE" : '';
		
		// $sql = "SELECT parameter_value
				// FROM {$this->table_name}
				// WHERE parameter_id = '{$id}' 
				// {$lock_stmt}";
		// $result = $this->db->query($sql);
		// log_message('debug', $this->db->last_query());
		// $result = $result->result_array();
		//$result = $this->get(array('parameter_id' => $id), 'parameter_value');   	
    	// $retval = isset($result[0]['parameter_value']) ? $result[0]['parameter_value'] : '';
    	// if ($id == 'ACCPERIOD' || $id == 'CURRDATE'){
    		// $retval = date('Ymd',strtotime($retval));
    	// }
    	// return $retval;
    // }
    
    /**
	 * @desc To increment a specific parameter value and returns its new value, pads new value with 0s if second parameter is true
	 */
	function incParam($param_id){
		$data = $this->get(array('parameter_id' => $param_id)
		,array('parameter_value')
		);

		if(!isset($data['list'][0]['parameter_value']))
			return "";
		else{
			if($param_id == "LASTTRANNO"){
				$new_param_val = ($data['list'][0]['parameter_value'])+ 1;
				$new_param_val = str_pad($new_param_val, 10, "0", STR_PAD_LEFT);
			}
			else if($param_id == "JOURNALNO"){
				$new_param_val = ($data['list'][0]['parameter_value'])+ 1;
				$new_param_val = str_pad($new_param_val, 10, "0", STR_PAD_LEFT);
			}
			else if($param_id == "OREQ"){
				$new_param_val = ($data['list'][0]['parameter_value'])+ 1;
				$new_param_val = str_pad($new_param_val, 5, "0", STR_PAD_LEFT);
			}
			else if($param_id == "TOPICID"){
				$new_param_val = ($data['list'][0]['parameter_value'])+ 1;
				$new_param_val = str_pad($new_param_val, 10, "0", STR_PAD_LEFT);
			}
			else if($param_id == "LASTORNO"){
				$new_param_val = ($data['list'][0]['parameter_value']);
				$or_year = substr($new_param_val,0,4);
				$curr_year = date("Y", strtotime($this->getParam('CURRDATE')));
				$or_count = substr($new_param_val,4,6);
				if($or_year!= $curr_year){
					$new_param_val = $curr_year."000001";
				}
				else{
					$or_count = str_pad($or_count+1,6, "0", STR_PAD_LEFT);
					$new_param_val = $curr_year.$or_count;
				}
			}
			
			$this->setValue('parameter_value', $new_param_val);
			$data = $this->update(array(
				'parameter_id' => $param_id
			));
			return $new_param_val;
		}
	}
    
    function updateValue($id, $value, $user){
    	$param_value = "";
	    if ($id == 'ACCPERIOD' || $id == 'CURRDATE'){
    		$param_value = date('m/d/Y',strtotime($value));
    	} else{
    		$param_value = $value;
    	}
    	$this->populate(array('parameter_id' => $id
    							, 'parameter_value' => $param_value
    							, 'modified_by' => $user));
    	
    	return $this->update();
    }
 
	    
}

?>
