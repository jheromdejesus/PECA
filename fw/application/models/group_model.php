<?php

class Group_model extends Asi_Model {
	
	var $date_columns = array('');
	
    function Group_model()
    {
        parent::Asi_Model();
        $this->table_name = 'r_user_group';
        $this->id = 'group_id';
       
    }  
    
	function getGroups()
	{
	    log_message('debug', "[START] Controller User:index");
		
		$data = $this->get_list(
		array('status_flag' => '1'),
		array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
		array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
		array('group_id'							
			,'group_name'						
			,'permission'						
			,'status_flag'),											
			'group_id ASC'
		);
		
		
		echo json_encode(array(
			'success' => true,
            'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query'],
        ));
      
        log_message('debug', "[END] Controller User:index");	
        
	}  
	
	function getGroupData()
	{
	    log_message('debug', "[START] Model Group:getGroupData");
	    
	    if (array_key_exists('group',$_REQUEST)) 
			$this->populate($_REQUEST['group']);
		
		$data = $this->get(null,array(
				'group_id'					
				,'group_name'				
				,'permission'				
				,'status_flag'		
		));
		
		if($data['affected_rows'] > 0){
			$this->populate($data['list'][0]);
		}
		
		return $data;
		
        log_message('debug', "[END] Model Group: getGroupdata");	
        
	}  
}
/* End of file user_model.php */
/* Location: ./system/application/models/user_model.php */