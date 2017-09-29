<?php

class Bulletinboard_model extends Asi_Model {

    function Bulletinboard_model()
    {
        parent::Asi_Model();
        $this->table_name = 'o_bulletin_board';
        $this->id = 'topic_id';
        $this->date_columns = array('');
       
    }
    
    function getTopicData()
	{
	   
	    if (array_key_exists('bulletin',$_REQUEST)) 
			$this->populate($_REQUEST['bulletin']);
		
		$data = $this->get(null,array(
				'topic_id'							
				,'subject'						
				,'content'
				,'published_date'		
				,'end_date'		
				,'status_flag'
				,'sticky'
		));
		
		if($data['affected_rows'] > 0){
			$this->populate($data['list'][0]);
		}
		return $data;
        log_message('debug', "[END] Controller User:index");	
	}  
    
}