<?php

class replicationServerOnOnline extends Controller {
	
	function replicationServerOnOnline(){
		parent::Controller();
		$this->load->library('xmlrpc');
		$this->load->library('xmlrpcs');
		$this->load->model('onlineattachment_model');
		$this->load->model('onlinebulletinboard_model');
		$this->load->model('onlinecapitaltransactiondetail_model');
		$this->load->model('onlinecapitaltransactionheader_model');
		$this->load->model('onlineloan_model');
		$this->load->model('onlineloanpayment_model');
		$this->load->model('onlineloanpaymentdetail_model');
		$this->load->model('onlinememberrequestdetail_model');
		$this->load->model('onlinememberrequestheader_model');
		$this->load->model('onlinepayrolldeduction_model');
		$this->load->model('onlinerequestapprover_model');
		$this->load->model('onlineworkflow_model');
		$this->load->model('ouser_model');
	}

	function index(){
		$config['functions']['get'] = array('function' => 'replicationServerOnOnline.read');
		$config['functions']['update'] = array('function' => 'replicationServerOnOnline.update');
		$this->xmlrpcs->initialize($config);
		$this->xmlrpcs->serve();
	}
	
	
	function read($request){
		
		if($this->authenticate($request)){
			$oAtt = array();
			$param = array('status_flag' => '1');
			$oAtt = $this->onlineattachment_model->get_list($param);
			$oBBoard = $this->onlinebulletinboard_model->get_list($param);
			$oCapTranDtl = $this->onlinecapitaltransactiondetail_model->get_list($param);
			$oCapTranHdr = $this->onlinecapitaltransactionheader_model->get_list($param);
			$oLoan = $this->onlineloan_model->get_list($param);
			$oLoanPayment = $this->onlineloanpayment_model->get_list($param);
			$oLoanPaymentDtl = $this->onlineloanpaymentdetail_model->get_list($param);
			$oMemReqDtl = $this->onlinememberrequestdetail_model->get_list($param);
			$oMemReqHdr = $this->onlinememberrequestheader_model->get_list($param);
			$oPayDed = $this->onlinepayrolldeduction_model->get_list($param);
			$oReqApp = $this->onlinerequestapprover_model->get_list($param);
			$oWorkflow = $this->onlineworkflow_model->get_list($param);
			
			$oAtt = json_encode($oAtt['list']);
			$oBBoard = json_encode($oBBoard['list']);
			$oCapTranDtl = json_encode($oCapTranDtl['list']);
			$oCapTranHdr = json_encode($oCapTranHdr['list']);
			$oLoan = json_encode($oLoan['list']);
			$oLoanPayment = json_encode($oLoanPayment['list']);
			$oLoanPaymentDtl = json_encode($oLoanPaymentDtl['list']);
			$oMemReqDtl = json_encode($oMemReqDtl['list']);
			$oMemReqHdr = json_encode($oMemReqHdr['list']);
			$oPayDed = json_encode($oPayDed['list']);
			$oReqApp = json_encode($oReqApp['list']);
			$oWorkflow = json_encode($oWorkflow['list']);
			
			$response = array(
								array(
										'onlineattachment_model' => array($oAtt, 'string')
										,'onlinebulletinboard_model'  => array($oBBoard, 'string')
										,'onlinecapitaltransactiondetail_model' => array($oCapTranDtl, 'string')
										,'onlinecapitaltransactionheader_model' => array($oCapTranHdr, 'string')
										,'onlineloan_model' => array($oLoan, 'string')
										,'onlineloanpayment_model' => array($oLoanPayment, 'string')
										,'onlineloanpaymentdetail_model'=> array($oLoanPaymentDtl, 'string')
										,'onlinememberrequestdetail_model' => array($oMemReqDtl, 'string')
										,'onlinememberrequestheader_model' => array($oMemReqHdr, 'string')
										,'onlinepayrolldeduction_model'=>array($oPayDed, 'string')
										,'onlinerequestapprover_model' => array($oReqApp, 'string')
										,'onlineworkflow_model' => array($oWorkflow, 'string')
									),'struct'
								);
			/*$test = array(array('clean house', 'call mom', 'water plants'), 'array');
			$response = array (
                   array(
                         'first_name' => $oAtt['list'],
                         'last_name' => array('Doe', 'string'),
                         'member_id' => array(123435, 'int'),
                         'todo_list' => $test
                        ),
                 'struct'
                 ); 	*/	
			return $this->xmlrpc->send_response($response);
		}
		else{
			return $this->xmlrpc->send_error_message('', 'Invalid username/password!');
		}
	}
	
	/**
	 * @desc Sets all new online data(status 1) to submitted(status 2) 
	 */
	function update($request){
		if($this->authenticate($request)){
			$models = array(
				'onlineattachment_model'
				,'onlinebulletinboard_model'
				,'onlinecapitaltransactiondetail_model'
				,'onlinecapitaltransactionheader_model'
				,'onlineloan_model'
				,'onlineloanpayment_model'
				,'onlineloanpaymentdetail_model'
				,'onlinememberrequestdetail_model'
				,'onlinememberrequestheader_model'
				,'onlinepayrolldeduction_model'
				,'onlinerequestapprover_model'
				,'onlineworkflow_model'
			);
			
			$this->db->trans_start();
			foreach($models as $model_name){
				eval("\$this->".$model_name."->setValue('status_flag','2');");
				eval("\$this->".$model_name."->update(array('status_flag'=>'1'));");
			}
			
			$this->db->trans_complete();
			if ($this->db->trans_status() === FALSE){
				return $this->xmlrpc->send_error_message('', 'Online data not successfully updated');
			}
			else {
				return $this->xmlrpc->send_response(array());
			}	
		}
		return $this->xmlrpc->send_error_message('', 'Invalid username/password!');
	}
	
	/**
	 * @desc Checks sent username / password in database
	 */
	function authenticate($request){
		$parameters = $request->output_parameters();
		$username = $parameters[0];
		$password = $parameters[1];
		
		$data = $this->ouser_model->get(
			array('user_id'=>$username, 'password'=>$password)
			,'COUNT(*) AS count'
		);
		
		if($data['list'][0]['count']>0)
			return 1;
		else 
			return 0;
	}
}
?>