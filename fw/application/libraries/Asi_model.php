<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Asi_model extends Model {
	var $table_name = '';
	var $id = '';
	var $model_data = null;
	var $date_columns = null;


	function Asi_model(){
		parent::Model();
		$this->load->helper('inflector');
	}

	function convertDateColumns(){
		foreach($this->date_columns as $dateCol){
			if (array_key_exists($dateCol,$this->model_data)) {
				if($this->model_data[$dateCol]!="")
					$this->model_data[$dateCol] = date("Ymd", strtotime($this->model_data[$dateCol]));
			}
		}
	}

	function populate($data){
		$this->model_data = $data;
	}

	function setValue($col_name, $col_value){
		$this->model_data[$col_name] = $col_value;
	}

	function getValue($col_name){
		return $this->model_data[$col_name];
	}

	function get($_param = null, $select = null){
		if($select)
		$this->db->select($select);
		$result =  $this->db->get_where($this->table_name, $_param ? $_param : $this->model_data);
		$query = $this->db->last_query();
		$count = $result->num_rows();
		return $this->checkError($result->result_array(),$count,$query);
	}

	function get_list($filter = null, $limit = null, $offset = null, $select = null, $orderby = null){
		$this->db->from($this->table_name);
		if($select)
		$this->db->select($select);
		if($orderby)
		$this->db->order_by($orderby);
		if ($filter)
		$this->db->where($filter);
		$this->db->limit($offset,$limit);

		$result = $this->db->get();
		
		$query = $this->db->last_query();
		
		log_message('debug', "SQL1 = ".$query);
		log_message('debug', "numrows = ".$result->num_rows());	
		if($filter){
			$this->db->where($filter);
			$this->db->from($this->table_name);
			$count = $this->db->count_all_results();
		}else{
			$count = $this->db->count_all($this->table_name);
		}
			
		return $this->checkError($result->result_array(), $count, $query);
			
	}
	
	function get_list_with_lock($select="*", $where="1=1"){
		$sql = "SELECT {$select}
				FROM {$this->table_name}
				WHERE {$where}
				FOR UPDATE";
    	
    	$result = $this->db->query($sql);    
    	$query = $this->db->last_query();
		$count = $result->num_rows();
    	
    	return $this->checkError($result->result_array(),$count,$query);
	}

	function insert(){
		$this->model_data['created_date'] = date("YmdHis");
		$this->model_data['modified_date'] = date("YmdHis");
		$this->model_data['modified_by'] = $this->model_data['created_by'];
		$this->convertDateColumns();
		$this->db->insert($this->table_name, $this->model_data);
		log_message('debug', "bulletin" . $this->db->last_query());
		return $this->checkError();
	}

	function checkDuplicateKeyEntry($cond = null){
		$error_code = "";
		$error_message = "";
		
		$this->db->select('status_flag');
		$this->db->from($this->table_name);
		
		if($cond==null)
			$cond = array($this->id => $this->model_data[$this->id]);
		$this->db->where($cond);
		
		$result = $this->db->get();
		$query = $this->db->last_query();
		if($result->num_rows()== 0) {
			$error_code = 0;		
		} else {
			$error_code = 1;
			$row = $result->row();
			$status_flag = $row->status_flag;
			$primary_keys = humanize(implode(", ", array_keys($cond)));
			if($status_flag == 0) {
				$error_message = "$primary_keys already exists, update status?";
			}
			else $error_message = "$primary_keys already exists, edit?";
		}

		$result = array(
    		'error_code' => $error_code
    		,'error_message' => $error_message
    		,'query' => $query
		);
		
		return $result;
	}

	function update($cond =null){
		
		if($cond != null){
			$this->db->where($cond);
		}else{
			$this->db->where($this->id, $this->model_data[$this->id]);
		}
		
		$this->model_data['modified_date'] = date("YmdHis");
		$this->convertDateColumns();
		
		$this->db->update($this->table_name, $this->model_data);
	
		$query = $this->db->last_query();
		
		return $this->checkError('','',$query);
	}

	function delete($cond=null){
		
		if($cond != null){
			$this->db->where($cond);
		}else{
			$this->db->where($this->id, $this->model_data[$this->id]);
		}
		$this->db->delete($this->table_name);
		
		$query = $this->db->last_query();
		
		return $this->checkError('','',$query);
	}
	
	function get_pk(){
		$pk_array = array();
		$result = $this->db->query("SHOW INDEX FROM ".$this->table_name." WHERE Key_name='PRIMARY'");
		foreach ($result->result() as $row){
	      $pk_array[] = $row->Column_name;
	    }
	    return $pk_array;
	}

	function checkError($result = '', $count=0, $query=''){
			
		if($this->db->_error_message() == ''){
			$error_code = '0';
			//echo "{'success':true,'msg':'Data successfully saved.'}";
		} else {
			$error_code = '1';
			log_message('debug', "SQL [error] = ".$query);
			//echo '{"success":false,"msg":"'.$this->db->_error_message().'"}';
		}
			
		$result = array(
    		'error_code' => $error_code,
    		'error_message' => $this->db->_error_message(),
    		'list' => $result,
    		'count' => $count,
    		'query' => $query,
    		'affected_rows' => $this->db->affected_rows()
		);
			
		log_message('debug', "SQL = ".$result['query']);
		log_message('debug', "affected_rows = ".$result['affected_rows']);
		log_message('debug', "count = ".$result['count']);

		if ($result['error_code'] != 0) {
			log_message('debug', "error_code = ".$result['error_code']);
			log_message('debug', "error_message = ".$result['error_message']);
		}

			
		return $result;
			
	}
	
	function lock(){
		$sql = "LOCK TABLES " . $this->table_name . " WRITE";
		
		$result = $this->db->query($sql);
		$query = $this->db->last_query();
		
    	return $this->checkError('','',$query);
	}
	
	function unLock(){
		$sql = "UNLOCK TABLES";
		
		$result = $this->db->query($sql);
		$query = $this->db->last_query();
		
    	return $this->checkError('','',$query);
	}
}