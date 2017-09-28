<?php

class Excel extends Asi_Controller {

	function Excel()
	{
		parent::Asi_Controller();
		$this->load->helper('date');
		$this->load->model('parameter_model');
		$this->load->model('loancodeheader_model');
		require_once(BASEPATH.'application/my_classes/Classes/PHPExcel/Calculation/Functions.php');
	}
	
	function index() {
		
	}
	
	function eir() {
		$c_excel = new PHPExcel_Calculation_Functions();
		
		$a_values = array();
		
		if(!isset($_REQUEST['s_loan_code']) 
			|| !isset($_REQUEST['f_annual_contractual_rate'])
			|| !isset($_REQUEST['f_loan_amount'])
			|| !isset($_REQUEST['f_service_charge'])
			|| !isset($_REQUEST['i_terms'])
			|| !isset($_REQUEST['s_initial_interest'])
			) {
				
			$a_return = array('f_mir' => 0,
							'f_eir' => 0);
	
			echo json_encode($a_return);
			exit();
		}
		
		#check if BSP
		if($_REQUEST['s_loan_code']) {
			$b_is_bsp = $this->loancodeheader_model->is_bsp($_REQUEST['s_loan_code']);
			if(!$b_is_bsp) {
				$a_return = array('f_mir' => 0,
								'f_eir' => 0);
		
				echo json_encode($a_return);
				exit();
			} 
		}
		
		$f_annual_contractual_rate = $_REQUEST['f_annual_contractual_rate']; #%
		$f_monthly_contractual_rate = $f_annual_contractual_rate / 12;

		$f_loan_amount = $_REQUEST['f_loan_amount'];
		/* $f_initial_interest = ($f_monthly_contractual_rate / 100) * $f_loan_amount; */
		$f_initial_interest = $_REQUEST['s_initial_interest'];
		$f_service_charge = $_REQUEST['f_service_charge'];
		$f_other_charges = $f_initial_interest + $f_service_charge;
		$f_loan_proceeds = $f_loan_amount - $f_other_charges;
		
		if($f_loan_amount <= 0) {
			$a_return = array('f_mir' => 0,
							'f_eir' => 0);
	
			echo json_encode($a_return);
			exit();
		}
		
		$i_terms = $_REQUEST['i_terms'];
		
		$f_principal = $f_loan_amount / $i_terms;
		
		if($i_terms > 12) {
			$i_periods = 12;
		} else {
			$i_periods = $i_terms;
		}
		
		$a_values[] = $f_loan_proceeds;
		
		$f_current_balance = $f_loan_amount;
		for($i = 0; $i < $i_terms; $i++) {
			$f_current_interest = $f_current_balance * ($f_monthly_contractual_rate / 100);
			$f_current_balance = $f_current_balance - $f_principal;
			$f_payment_amount = $f_principal + $f_current_interest;
			$a_values[] = $f_payment_amount * -1;
		}
		
		for($i_guess = 1; $i_guess <= 5; $i_guess++) {
			$f_guess = ($i_guess / 100);
			$f_mir_raw = $c_excel->IRR($a_values, $f_guess);
			if($f_mir_raw != '#VALUE!')
				$i_guess = 6;
		}
		
		$f_mir = round($f_mir_raw * 100, 2);
		$f_eir_raw = pow((1 + $f_mir_raw), 12) - 1;
		$f_eir = round($f_eir_raw * 100, 2);
				
		if(is_infinite($f_eir)) {
			$f_eir = 0;
		}
		
		if(is_infinite($f_mir)) {
			$f_mir = 0;
		}
		
		$a_return = array('f_mir' => $f_mir,
						'f_eir' => $f_eir);

		echo json_encode($a_return);
	}
	
}


/* End of file excel.php */
/* Location: ./system/application/controllers/excel.php */