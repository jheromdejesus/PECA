<?php

class OAttachment_model extends Asi_Model {

    function OAttachment_model()
    {
        parent::Asi_Model();
        $this->table_name = 'o_attachment';
        $this->id = 'topic_id';
        $this->date_columns = array('');
    }
    
    function getTopicAttachmentData()
	{
	    log_message('debug', "[START] Controller User:index");
	    
	    if (array_key_exists('attachment',$_REQUEST)) 
			$this->populate($_REQUEST['attachment']);
		
		$data = $this->get(null,array(
				'attachment_id'	
				,'topic_id'								
				,'path'						
				,'type'
				,'size'	
				,'status_flag'		
		));
		
		if($data['affected_rows'] > 0){
			$this->populate($data['list'][0]);
		}
		return $data;
        log_message('debug', "[END] Controller User:index");	
	}

	function getTopicAttachments()
	{
	    log_message('debug', "[START] Controller User:index");
	    
	    if (array_key_exists('bulletin',$_REQUEST)) 
			$this->populate($_REQUEST['bulletin']);
		
		$data = $this->get(null,array(
				'attachment_id'	
				,'topic_id'								
				,'path'						
				,'type'
				,'size'	
				,'status_flag'		
		));
		
		return $data;
        log_message('debug', "[END] Controller User:index");	
	}  	
	
	//used for attachments for important documents
	function getTopicAttachments2()
	{
	    log_message('debug', "[START] Controller User:index");
	    
		$this->setValue('topic_id', '0');
		
		$data = $this->get(null,array(
				'attachment_id'	
				,'topic_id'								
				,'path'						
				,'type'
				,'size'	
				,'status_flag'		
		));
		
		foreach($data['list'] as $key => $val){
			$path_parts = pathinfo($val['path']);
			$filename = $path_parts['filename'];
			$data['list'][$key]['filename'] = $filename;
		}
		
		return $data;
        log_message('debug', "[END] Controller User:index");	
	}  	
	
	function deleteTopicAttachments($path){
		$this->delete(array('path'=>$path));
	}
	
	function getMaxAttachmentID($topic_id){
		$this->db->select_max('attachment_id');
		$this->db->from($this->table_name);
		$this->db->where($topic_id);
		$result = $this->db->get(); 
    	$query = $this->db->last_query();
		return $this->checkError($result->result_array(),'', $query);
	}	
}