<?php
Class RecomputeMinimumBalance extends Controller{
	function RecomputeMinimumBalance(){
		parent::Controller();
		$this->load->model('mtransaction_model');
		$this->load->model('capitalcontribution_model');
		set_time_limit(0);
	}
	
	function index(){
		$input_accounting_period = '12/01/2010';
		
		$accounting_period = date('Ym', strtotime($input_accounting_period));
		$accounting_period_full = date('Ymd', strtotime($input_accounting_period));
		
		$prev_accounting_period = date('Ymd', strtotime("$input_accounting_period - 1 month"));
		//retrieve all m transactions
		$result = $this->mtransaction_model->retrieveByAccountingPeriod($accounting_period);
		
		//put additional array at end to detect end 
		$data = array_merge($result['list'], array(array('employee_id' => 'EOF')));
		
		$prev_employee_id = "";
		foreach($data as $row){
			if($row['employee_id'] == $prev_employee_id){
				//compute min and end bal
				if($row['transaction_date'] == $accounting_period."01"){
					$end_bal = $min_bal = $end_bal + $row['amount'];
				}
				else{
					$end_bal = $end_bal + $row['amount'];
					if($end_bal < $min_bal){
						$min_bal = $end_bal;
					}
				}
			}
			else{
				//save new min bal
				if($prev_employee_id != ""){
					//clear model first
					$this->capitalcontribution_model->populate(array());
					$this->capitalcontribution_model->setValue('minimum_balance', $min_bal);
					
					$capcon = $this->capitalcontribution_model->update(
					array('employee_id'=>$prev_employee_id
						, 'accounting_period' => $accounting_period_full)
					);
				}
				//exit
				if($row['employee_id'] == 'EOF'){
					exit;
				}
				
				//get min and end bal from last accounting period
				$capcon = $this->capitalcontribution_model->get(
					array('employee_id'=>$row['employee_id'], 'accounting_period' => $prev_accounting_period)
					,array('minimum_balance', 'ending_balance')
				);
				
				if($capcon['count']>0){
					$end_bal = $min_bal = $capcon['list'][0]['ending_balance'];
				}
				else{
					$min_bal = 0;
					$end_bal = 0;
				}
				
				//compute min and end bal
				//if first date of the month
				if($row['transaction_date'] == $accounting_period_full){
					$end_bal = $min_bal = $end_bal + $row['amount'];
				}
				else{
					$end_bal = $end_bal + $row['amount'];
					if($end_bal < $min_bal){
						$min_bal = $end_bal;
					}
				}
			}
			$prev_employee_id = $row['employee_id'];
		}
	}
}
?>