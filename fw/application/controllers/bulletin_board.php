<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Bulletin_board extends Asi_Controller {

	
	function Bulletin_board()
	{
		parent::Asi_Controller();
		$this->load->model(array('Bulletinboard_model','OAttachment_model'));
		$this->load->library('constants');
		$this->load->model('parameter_model');
		
	}
	
	function stripslashes_array(&$array, $iterations=0) {
	    if ($iterations < 3) {
	        foreach ($array as $key => $value) {
	            if (is_array($value)) {
	                stripslashes_array($array[$key], $iterations + 1);
	            } else {
	                $array[$key] = stripslashes($array[$key]);
	            }
	        }
	    }
	}
	
	function index()
	{}
	
	function show()
	{
		
		$data = $this->Bulletinboard_model->getTopicData();
		
		echo json_encode(array(
			'success' => true,
            'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query'],
        ));
	}
	
	function read()
	{
		log_message('debug', "[START] Controller User:index");
		$now = $this->parameter_model->getParam('CURRDATE');
		$now = date('Ymd', strtotime($now));
		
		if($_REQUEST['is_admin'] == 'true'){
			$_REQUEST['filter'] = 'status_flag IN (1,3) AND sticky = \'N\'';
		} else {
			$_REQUEST['filter'] = 'is_admin = 0 AND status_flag IN (1,3) AND sticky = \'N\' AND published_date <= '.$now;
		}
		
		$data = $this->Bulletinboard_model->get_list(
		array_key_exists('filter',$_REQUEST) ? $_REQUEST['filter'] : null,
		array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
		array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
		array('topic_id'							
			,'subject'						
			,'content'
			,'published_date'		
			,'end_date'		
			,'status_flag'),											
			'published_date DESC'
		);
	
		echo json_encode(array(
			'success' => true,
            'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query'],
        ));
      
        log_message('debug', "[END] Controller User:index");
	}
	
	function readSticky()
	{
		log_message('debug', "[START] Controller bulletin_board:readSticky");
		$now = $this->parameter_model->getParam('CURRDATE');
		$now = date('Ymd', strtotime($now));
		
		if($_REQUEST['is_admin'] == 'true'){
			$_REQUEST['filter'] = 'status_flag = 1 AND sticky = \'Y\'';
		} else {
			$_REQUEST['filter'] = 'is_admin = 0 AND status_flag = 1 AND sticky = \'Y\' AND published_date <= '.$now;
		}
		
		$data = $this->Bulletinboard_model->get_list(
		array_key_exists('filter',$_REQUEST) ? $_REQUEST['filter'] : null,
		array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
		array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
		array('topic_id'							
			,'subject'						
			,'content'
			,'published_date'		
			,'end_date'		
			,'status_flag'),											
			'published_date DESC'
		);
	
		echo json_encode(array(
			'success' => true,
            'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query'],
        ));
      
        log_message('debug', "[END] Controller bulletin_board:readSticky");
	}
	
	//used for attachments for important documents
	function getTopicAttachmentList()
	{
		$data = $this->OAttachment_model->getTopicAttachments();
		echo json_encode(array(
			'success' => true,
            'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query'],
        ));
	}
	
	function getTopicAttachmentList2()
	{
		$data = $this->OAttachment_model->getTopicAttachments2();
		echo json_encode(array(
			'success' => true,
            'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query'],
        ));
	}

	function add()
	{
		if (array_key_exists('bulletin',$_REQUEST)) {
			 
			//format date
			$date = strtotime( $_REQUEST['bulletin']['published_date'] );
		    $formatDate = date("Ymd", $date );
		    $_REQUEST['bulletin']['published_date'] = $formatDate;
		    //$_REQUEST['bulletin']['end_date'] = date("Ymd", strtotime($_REQUEST['bulletin']['end_date'] ));
			
			$this->db->trans_start();
			$topic_id = $this->parameter_model->incParam('TOPICID');
			$_REQUEST['bulletin']['topic_id'] = $topic_id;
			
			//$_REQUEST['bulletin']['content'] = utf8_encode($_REQUEST['bulletin']['content']);
			$this->Bulletinboard_model->populate($_REQUEST['bulletin']);
			
			$checkDuplicate = $this->Bulletinboard_model->checkDuplicateKeyEntry();
	
			if($checkDuplicate['error_code'] == 1){
				$result['error_code'] = 1;
				$result['error_message'] = "Duplicate bulletin board entry";
			}
			else{
				$result = $this->Bulletinboard_model->insert(); 
				$this->insertAttachments($topic_id);
			} 
			
			$this->db->trans_complete();				
			if($result['error_code'] == 0) {
				//$this->OAttachment_model->setValue('created_by',$this->Bulletinboard_model->getValue('created_by'));		
				//$this->addTopicAttachment();
				//display_message('true',$this->constants->messages['139']);
				echo "{'success':'true','msg':'".$this->constants->messages['139']."','topic_id':'".$_REQUEST['bulletin']['topic_id']."'}";
				
	        } else{
				display_message('false',"Bulletin Board topic NOT successfully saved.");
			}
		} else
			display_message('false',$this->constants->messages['142']);
			
		log_message('debug', "[END] Controller User:add");
	}
	
	function insertAttachments($topic_id){
		$filearr = json_decode(stripslashes($_REQUEST['files']),true);
		$attachment_id = 1;
		$created_by = "";
		if(array_key_exists('created_by', $_REQUEST['bulletin'])){
			$created_by = $_REQUEST['bulletin']['created_by'];
		}
		else if(array_key_exists('modified_by', $_REQUEST['bulletin'])){
			$created_by = $_REQUEST['bulletin']['modified_by'];
		}
		
		foreach($filearr as $fileinfo){
			$params = array('attachment_id' => sprintf("%06d", $attachment_id)
							,'topic_id' => $topic_id
							,'path' => $fileinfo['path']
							,'type' => $fileinfo['type']
							,'size' => $fileinfo['size']
							,'created_by' => $created_by
							); 
			$this->OAttachment_model->populate($params);				
			$this->OAttachment_model->insert();
			$attachment_id++;
		}
	}
	
	
	function update(){	
		if (array_key_exists('bulletin',$_REQUEST)) {
			//format date
		    $_REQUEST['bulletin']['published_date'] = date("Ymd", strtotime($_REQUEST['bulletin']['published_date'] ));
		    //$_REQUEST['bulletin']['end_date'] = date("Ymd", strtotime($_REQUEST['bulletin']['end_date'] ));
		    
			$this->db->trans_start();
			$this->Bulletinboard_model->populate($_REQUEST['bulletin']);
			$result = $this->Bulletinboard_model->update();
			$this->OAttachment_model->delete(array('topic_id'=>$_REQUEST['bulletin']['topic_id']));
			$this->insertAttachments($_REQUEST['bulletin']['topic_id']);
			$this->db->trans_complete();
				
			if($result['error_code'] == 0 && $this->db->trans_status()){
				//$this->OAttachment_model->setValue('created_by',$this->Bulletinboard_model->getValue('modified_by'));			
				//$this->addTopicAttachment();
				display_message('true',$this->constants->messages['140']);
			} else{
				display_message('false',"Bulletin Board topic NOT successfully saved.");
			}
		} else
			display_message('false',$this->constants->messages['143']);
			
		log_message('debug', "[END] Controller User:update");
	}
	
	function delete()
	{	
		log_message('debug', "[START] Controller User:delete");
		log_message('debug', "user param exist?:".array_key_exists('bulletin',$_REQUEST));
		
		if (array_key_exists('bulletin',$_REQUEST)) {
			$this->Bulletinboard_model->setValue('topic_id', $_REQUEST['bulletin']['topic_id']);
			$this->Bulletinboard_model->setValue('status_flag', '0');
			
			$this->db->trans_start();
			$result = $this->Bulletinboard_model->update();
			$attachments= $this->OAttachment_model->get(array('topic_id'=>$_REQUEST['bulletin']['topic_id']), 
				array('path'));
			$this->OAttachment_model->delete(array('topic_id'=> $_REQUEST['bulletin']['topic_id']));
			$this->db->trans_complete();
			
			if($result['error_code'] == 0 && $this->db->trans_status()){
				//delete attached files
				foreach($attachments['list'] as $attachment){
					$path_parts = pathinfo($attachment['path']);
					$fileName = $path_parts['basename'];
					$full_path = $_SERVER['DOCUMENT_ROOT']."/temp_dir/".$fileName;
					if (file_exists($full_path)) {
						@unlink($full_path);
					}	
				}
				display_message('true',$this->constants->messages['141']);
			} else
				display_message('false',$result['error_message']);
		} else
			display_message('false',$this->constants->messages['144']);
			
		log_message('debug', "[END] Controller User:delete");
	}
	
	function initData()
	{
		//for test start
		$_REQUEST['bulletin']['topic_id'] = '000003';
		//for test end
	}
	
	function initNewData()
	{
		//for test start
		$_REQUEST['bulletin']['topic_id'] = '000003';
		$_REQUEST['bulletin']['subject'] = 'second POST';
		$_REQUEST['bulletin']['content'] = 'You are so gay and you dont even like boys';
		$_REQUEST['bulletin']['published_date'] = '20100401000000';
		$_REQUEST['bulletin']['created_by'] = 'PECA';
		$_REQUEST['bulletin']['modified_by'] = 'PECA';
		
        //for test end
	}	
	function initAttachmentData()
	{
		$_REQUEST['attachment'][0]['topic_id'] = '000003';
		$_REQUEST['attachment'][0]['attachment_id'] = '000001';
		$_REQUEST['attachment'][0]['path'] = 'FIRST POST';
		$_REQUEST['attachment'][0]['type'] = 'image';
		$_REQUEST['attachment'][0]['size'] = '20100401000000';
		$_REQUEST['attachment'][0]['created_by'] = 'PECA';
		$_REQUEST['attachment'][0]['modified_by'] = 'PECA';
		
		$_REQUEST['attachment'][1]['topic_id'] = '000003';
		$_REQUEST['attachment'][1]['attachment_id'] = '000002';
		$_REQUEST['attachment'][1]['path'] = 'FIRST POST';
		$_REQUEST['attachment'][1]['type'] = 'image gif';
		$_REQUEST['attachment'][1]['size'] = '20100401000000';
		$_REQUEST['attachment'][1]['created_by'] = 'PECA';
		$_REQUEST['attachment'][1]['modified_by'] = 'PECA';
		
	}	
	function validateData()
	{
		/*if (!array_key_exists('user',$_REQUEST)) {
			display_message('false',$this->constants->messages['124']);
			return false;
		}
		
		if(empty($_REQUEST['user']['user_id'])){
			display_message('false',$this->constants->messages['119']);
			return false;
		}
		
		if(empty($_REQUEST['user']['user_name'])){
			display_message('false',$this->constants->messages['120']);
			return false;
		}
		
		if(empty($_REQUEST['user']['group_id'])){
			display_message('false',$this->constants->messages['121']);
			return false;
		}
		
		if(empty($_REQUEST['user']['email_address'])){
			display_message('false',$this->constants->messages['122']);
			return false;
		}
		
		if(!validate_email($_REQUEST['user']['email_address'])){
			display_message('false',$this->constants->messages['123']);
			return false;
		}*/
		
		return true;
	}
	function readforms(){
		$this->OAttachment_model->setValue('status_flag', '1');
		$this->OAttachment_model->setValue('topic_id', '0');
		$data = $this->OAttachment_model->getTopicAttachments();
		$arr_data = array();
		foreach($data['list'] as $data_single){
			$data_single["name"] = basename($data_single["path"]);
			$arr_data[] = $data_single;
		}
		echo json_encode(array(
			'success' => true,
            'data' => $arr_data,
			'total' => $data['count'],
			'query' => $data['query'],
        ));
	}
	function getTopicAttachments()
	{
		if (array_key_exists('bulletin',$_REQUEST)) {
			$this->OAttachment_model->populate($_REQUEST['bulletin']);
		}
		
		$data = $this->OAttachment_model->getTopicAttachments();
		
		echo json_encode(array(
			'success' => true,
            'data' => $data['list'],
			'total' => $data['count'],
			'query' => $data['query'],
        ));
	}
	
	function addTopicAttachment()
	{
		if (!array_key_exists('files',$_REQUEST)) {
			return;
		}
		$arFiles = (json_decode(stripslashes($_REQUEST['files'])));	
		
	    $iCtr = 1;
	    //physical delete of all attachments
		$params = $_REQUEST['bulletin'];
		$this->deleteAttachment($params['topic_id']);
	    
		if(empty($arFiles)) return;
		
		foreach ($arFiles as $key =>$value ){
			$this->addAttachment(array('attachment_id' => sprintf("%06d", $iCtr)
										   ,'topic_id' =>$this->Bulletinboard_model->getValue('topic_id')
										   ,'path' =>$value->path
										   ,'type' => $value->type
										   ,'size' => $value->size
										   ,'created_by' =>$this->Bulletinboard_model->getValue('modified_by')));
			$iCtr++;
		}
	}
	
	function addAttachment($arAttachFiles)
	{
	        
		if (count($arAttachFiles) > 0) {
			
			
			$this->OAttachment_model->populate($arAttachFiles);
			/*$checkDuplicate = $this->OAttachment_model->checkDuplicateKeyEntry(array
						('topic_id' => $this->Bulletinboard_model->getValue('topic_id')
						,'attachment_id' => $this->OAttachment_model->getValue('attachment_id')));
	
			if($checkDuplicate['error_code'] == 1){
				$result['error_code'] = 1;
				$result['error_message'] = $checkDuplicate['error_message'];
			}
			else{*/
				$result = $this->OAttachment_model->insert(); 
			//} 
							
			/*if($result['error_code'] == 0) {
				//display_message('true',$this->constants->messages['146']);
	        } else
				//display_message('false',$result['error_message']);*/
		} 
		log_message('debug', "[END] Controller User:add");
	}
	
	function updateAttachment()
	{	
		
		log_message('debug', "[START] Controller User:update");
		log_message('debug', "user param exist?:".array_key_exists('user',$_REQUEST));
		
		if (array_key_exists('attachment',$_REQUEST)) {
			$this->OAttachment_model->populate($_REQUEST['attachment']);
			$result = $this->OAttachment_model->update();
			
			if($result['error_code'] == 0){
				display_message('true',$this->constants->messages['140']);
			} else
				display_message('false',$result['error_message']);
		} else
			display_message('false',$this->constants->messages['143']);
			
		log_message('debug', "[END] Controller User:update");
	}
	
	function deleteAttachment($topic_id)
	{		
		
		log_message('debug', "[START] Controller User:delete");
		log_message('debug', "user param exist?:".array_key_exists('bulletin',$_REQUEST));
		
			//$this->OAttachment_model->populate($_REQUEST['attachment']);
			$this->OAttachment_model->setValue('topic_id', $topic_id);
			//$result = $this->OAttachment_model->update($_REQUEST['attachment']);
			$result = $this->OAttachment_model->delete();
			
			if($result['error_code'] == 0){
			    //display_message('true',$this->constants->messages['141']);
			    return true;
			} else {
				//display_message('false',$result['error_message']);
				return false;
			}
			echo $result['query'];
		log_message('debug', "[END] Controller User:delete");
	}
}

/* End of file user.php */
/* Location: ./system/application/controllers/user.php */