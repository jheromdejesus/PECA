<?php

class Im_notification extends Controller {

	function Im_notification()
	{
		parent::Controller();
		$this->load->model('minvestmentheader_model');
		$this->load->model('parameter_model');
		$this->load->model('onlinebulletinboard_model');
	}

	function index()
	{
	}

	/**
	 * @desc Posts in the bulletin board the investments which have matured on the current date
	 */
	function notify(){
		$currDate = date("Ymd", strtotime($this->parameter_model->getParam('CURRDATE')));
		
		$data = $this->minvestmentheader_model->get_list(
			array('maturity_date LIKE' => $currDate.'%')
			,null
			,null
			,array('investment_no')
			,'investment_no'
		);
		
		$investment_nos = array();
		foreach($data['list'] as $val){
			$investment_nos[] = $val['investment_no'];
		}
		
		if(count($investment_nos)){
			$topic_id = $this->parameter_model->incParam('TOPICID');
			$content = "Matured Investments <br> <ul>";
			foreach($investment_nos as $no){
				$content .= "<li>".$no."</li>";
			}
			$content .= "</ul>";
				
			$o_bboard = array(
				'topic_id' => $topic_id
				,'subject' => 'Matured Investments'
				,'content' => $content
				,'published_date' => $currDate
				,'end_date' => $currDate
				,'status_flag' => '1'
				,'is_admin' => '1'
				,'created_by' => 'SYSTEM'	
			);
			
			
			$this->onlinebulletinboard_model->populate($o_bboard);
			$result = $this->onlinebulletinboard_model->checkDuplicateKeyEntry();
			
			if($result['error_code']==0){
				$insert_result = $this->onlinebulletinboard_model->insert();
				if($insert_result['error_code']==0){
					echo "{'success':true,'msg':'Matured investments successfully posted to bulletin board.'}";	
				}
				else{
					echo "{'success':false,'msg':'Matured investments NOT successfully posted to bulletin board1.'}";
				}
			}
			else{
				echo "{'success':false,'msg':'Matured investments NOT successfully posted to bulletin board2.'}";
			}
		}
		else{
			echo "{'success':true,'msg':'No matured investments today.'}";
		}
	}
}

/* End of file im_notification.php */
/* Location: ./PECA/application/controllers/im_notification.php */