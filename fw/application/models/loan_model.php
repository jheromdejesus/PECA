<?php
/*
 * Created on Mar 24, 2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Loan_model extends Asi_Model {
	
    function Loan_model()
    {
        parent::Asi_Model();
        $this->table_name = 't_loan';
        $this->id = 'loan_no';
        $this->date_columns = array('');
		$this->load->model('Loancodeheader_model');
		$this->load->model('Member_model');
		$this->load->model('Parameter_model');
		$this->load->model('Loanguarantor_model');
    }
    
    function retrieveActiveLoans($current_period){
    	$year = substr($current_period, 0, 4);
    	$month = substr($current_period, 4, 2);
    	$sql = "SELECT loan_no												
					,interest_rate										
					,amortization_startdate										
					,principal_balance										
					,term										
				FROM t_loan												
				WHERE amortization_startdate < '{$year}' 
					AND MONTH(amortization_startdate) = {$month}";
		
    	$query = $this->db->query($sql);
    	
    	return $query->result_array();
    }
	
	/**
	 * @desc To retrieve all Loan Transactions
	 * @return array
	 */
	function getLoanList($filter = null, $limit = null, $offset = null, $select = null, $orderby = null, $distinct = null) 
	{
	if($distinct)
		$this->db->distinct();
	if($select)
    		$this->db->select($select);
    	$this->db->from('t_loan tl');
    	$this->db->join('m_employee me', 'me.employee_id=tl.employee_id', 'inner');
    	$this->db->join('r_loan_header rl', 'rl.loan_code=tl.loan_code', 'inner');
    	if($filter)	
    		$this->db->where($filter);
    	if($orderby)	
    		$this->db->orderby($orderby);	
    	$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
    	$count = $result->num_rows();
    
    	return $this->checkError($result->result_array(), $count, $query);
    }
    
    /**
	 * @desc Retrieves list of loans that can be restructured of the selected employee 
	 * @return array
	 */
	function getRestructuredLoans($filter = null, $limit = null, $offset = null, $select = null, $orderby = null) 
	{
    	if($select)
    		$this->db->select($select);
    	$this->db->from('t_loan tl');
    	$this->db->join('r_loan_header rl', 'rl.loan_code=tl.loan_code', 'left outer');
    	if($filter)	
    		$this->db->where($filter);
    	if($orderby)	
    		$this->db->orderby($orderby);	
    	$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
    	$count = $result->num_rows();
    	
		return $this->checkError($result->result_array(), $count, $query);
    }
	
	/**
	 * @desc Retrieves list of loans that can be restructured of the selected employee 
	 * @return array
	 */
	function getRestructuredLoanInfo($filter = null, $limit = null, $offset = null, $select = null, $orderby = null) 
	{
    	if($select)
    		$this->db->select($select);
    	$this->db->from('t_loan tl');
    	$this->db->join('t_loan_guarantor tlg', 'tlg.loan_no=tl.loan_no', 'left outer');
    	$this->db->join('r_loan_header rl', 'rl.loan_code=tl.loan_code', 'left outer');
    	if($filter)	
    		$this->db->where($filter);
    	if($orderby)	
    		$this->db->orderby($orderby);	
    	$this->db->limit($offset, $limit);
    	
    	$result = $this->db->get(); 
    	$query = $this->db->last_query();
    	$count = $result->num_rows();
    	
		return $this->checkError($result->result_array(), $count, $query);
    }
	
	/**
	 * @desc Checks if the principal amount exceeds the Maximum Loan amount for the specified loan
	 * @return Returns 0 if principal amount does not exceed Maximum loan amount
	 * @param $principal : User input
	 */
	function checkMaximumLoanAmount($principal = 0, $max_loan_amount) 
	{	
		$result = 0; 	
		if ($principal<=$max_loan_amount) $result = 0;
		else $result = 1;
		
		return $result;
	}

	/**
	 * @desc Retrieves Information of the specified loan type
	 * @return array
	 */
	function getRLoanHdr($loan_code = '')
	{
		$_REQUEST['filter'] = array('loan_code' => $loan_code, 'status_flag' => '1');

		$data = $this->Loancodeheader_model->get_list(
		array_key_exists('filter',$_REQUEST) ? $_REQUEST['filter'] : null,
		array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
		array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
		array('loan_code AS loan_code'
		,'loan_description AS loan_description'
		,'emp_interest_pct AS employee_interest_percentage'
		,'min_term AS min_term'
		,'max_term AS max_term'
		,'interest_earned AS interest_earned'
		,'unearned_interest AS unearned_interest'
		,'max_loan_amount AS max_loan_amount'
		,'comp_share_pct AS company_share_percentage'
		,'restructure AS restructure'
		,'downpayment_pct AS downpayment_pct'
		,'min_emp_months AS min_emp_months')
		);

		return $data['list'];
	}
	
	/**
	 * @desc Retrieves the Maximum and Minimum Terms for the specied loan type
	 * @return array 
	 */
	function retrieveMinMaxTerms($loan_code)
	{
		$data = $this->getRLoanHdr($loan_code);
		return $data[0];
	}	
	
	/**
	 * @desc Checks if the term is greater than or equal to Minimum term and less than or equal to Maximum term
	 * @return Returns 0 if the input term is within minimum and maximum terms
	 * @param $term User input
	 */
	function checkMinMaxTerms($term = 1, $min_term, $max_term)
	{
		$result = 0;
		if ($term>=$min_term && $term<=$max_term) $result = 0;
		else $result = 1;
		
		return $result;
	}
	
	/**
	 * @desc Checks the minimum number of months of service for the specified loan type
	 */
	function getLoanMonthOfService($loan_code)
	{
		$data = $this->getRLoanHdr($loan_code);
		return $data[0]['min_emp_months'];
	}
	
	/**
	 * @desc Retrieves the date when the employee is hired
	 */
	function getEmployeeHireDate($employee_id ='01517085')
	{
		$data = $this->Member_model->get_list(
										array('employee_id' => $employee_id)
										,null
										,null
										,'hire_date'											
									);
		$hire_date = $data['list'][0]['hire_date'];
		return $hire_date;
	}
	
	/**
	 * @desc To get a specific parameter value
	 * @param Parameter id
	 * @return string (parameter value)
	 */
	function getParam($param_id){
		$data = $this->Parameter_model->get(array('parameter_id' => $param_id)
			,array('parameter_value')
		);
	
		return $data['list'][0]['parameter_value'];
	}	
	
	/**
	 * @desc Common function to count the difference in years from start to end date
	 * @param $start
	 * @param $end
	 */
	function dateDiff($start, $end)
	{	
		$date_diff = date("Y", strtotime($end) - strtotime($start)) - 1970;
		return $date_diff;
	}
	
	
	function getEmpYearsOfService($employee_id = '01517085')
	{
		$current_date = $this->getParam('CURRDATE');	
		
		$data = $this->Member_model->get_list(
										array('employee_id' => $employee_id)
										,null
										,null
										,'hire_date'											
									);
							
		$hire_date = $data['list'][0]['hire_date'];
		
		$years_of_service = $this->dateDiff($hire_date, $current_date);
	
		return $years_of_service;
	}
	
	/**
	 * @desc Retrieves detail of the selected loan for checking capital contribution and counting years of service
	 * @param Array
	 */
	function getRLoanDtl($loan_code )
	{
		$_REQUEST['filter'] = array('loan_code' => $loan_code);		

		$data = $this->Loancodedetail_model->getLoanDetail(
			array_key_exists('filter',$_REQUEST) ? $_REQUEST['filter'] : null,
			array_key_exists('start',$_REQUEST) ? $_REQUEST['start'] : null,
			array_key_exists('limit',$_REQUEST) ? $_REQUEST['limit'] : null,
			array('loan_code'
				 ,'capital_contribution'
				 ,'MAX(years_of_service) AS to_yos'
				 ,'MIN(years_of_service) AS from_yos'
				 ,'guarantor')
			,'loan_code'	 
		);
		
		if(!isset($data['list'][0])){
			return array(
				'loan_code' => $loan_code
				,'capital_contribution' => 0
				 ,'to_yos' => 0
				 ,'from_yos' => 0
				 ,'guarantor' => 0
			);
		}
		else{
			return $data['list'][0];
		}
	}
	
	
	/**
	 * @desc Retrieves loans of an employee with principal balance greater than 0
	 */
    function retrieveLoanInformation($filter = null, $limit = null, $offset = null, $select = null, $orderby = null)
    {
    	if($select)
			$this->db->select($select);
    	$this->db->from('m_loan');
    	if ($filter)
			$this->db->where($filter);
		if($orderby)
			$this->db->order_by($orderby);
		$this->db->limit($offset, $limit);
		$result = $this->db->get();
			
		$query = $this->db->last_query();
		//$count = $result->num_rows();
		$count = $this->db->count_all('m_loan');
		
		return $this->checkError($result->result_array(), $count, $query);
    }
	
	/**
	 * @desc Retrieves guaranteed loans of employees
	 */
    function retrieveGuarantorList($filter = null, $limit = null, $offset = null, $select = null, $orderby = null)
    {
    	if($select)
			$this->db->select($select);
    	$this->db->from('t_loan_guarantor tlg');
		$this->db->join('m_loan ml', 'ml.loan_no=tlg.loan_no', 'INNER');
		$this->db->join('m_employee me', 'me.employee_id = ml.employee_id', 'INNER');
    	if ($filter)
			$this->db->where($filter);
		if($orderby)
			$this->db->order_by($orderby);
		$this->db->limit($offset, $limit);
		$result = $this->db->get();
			
		$query = $this->db->last_query();
		$count = $result->num_rows();
		//$count = $this->db->count_all($this->table_name);
		
		return $this->checkError($result->result_array(), $count, $query);
    }
    
    /**
     * @desc Retrieve list of loan applications that are not yet processed
     * @param $filter
     * @param $limit
     * @param $offset
     * @param $select
     * @param $orderby
     */
    function retrieveLoanApplicationProofList($filter = null, $limit = null, $offset = null, $select = null, $orderby = null, $distinct=null)
    {
		if($distinct)
			$this->db->distinct($distinct);
		if($select)
			$this->db->select($select);
    	$this->db->from('t_loan TL');
    	$this->db->join('r_transaction RT', 'TL.loan_code=RT.transaction_code', 'LEFT');
    	$this->db->join('m_employee ME', 'ME.employee_id=TL.employee_id', 'INNER');
		if ($filter)
			$this->db->where($filter);
		$this->db->where('TL.status_flag = 1');
		
		if($orderby)
			$this->db->order_by($orderby);
		//$this->db->group_by('TL.loan_code');
		$this->db->limit($offset, $limit);
		$result = $this->db->get();
			
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
    }
    
	/**
     * @desc Retrieve list of loan applications that are processed
     * @param $filter
     * @param $limit
     * @param $offset
     * @param $select
     * @param $orderby
     */
    function retrieveLoanApplicationAuditTrail($filter = null, $limit = null, $offset = null, $select = null, $orderby = null, $distinct = null)
    {
    	if($distinct)
			$this->db->distinct($distinct);
		if($select)
			$this->db->select($select);
    	$this->db->from('m_loan TL');
    	$this->db->join('r_transaction RT', 'TL.loan_code=RT.transaction_code', 'LEFT');
    	$this->db->join('m_employee ME', 'ME.employee_id=TL.employee_id', 'INNER');
		if ($filter)
			$this->db->where($filter);
		$this->db->where('TL.status_flag = 2');
		
		if($orderby)
			$this->db->order_by($orderby);
		//$this->db->group_by('TL.loan_code');
		$this->db->limit($offset, $limit);
		$result = $this->db->get();
			
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
    }
    
	/**
     * @desc Retrieve list of loan payments 
     * @param $filter
     * @param $select
     */
    function retrieveLoanPaymentTransactions($filter = null, $select = null, $start_date, $end_date, $orderby=null, $employee_id = null)
    {
    	if($select)
			$this->db->select($select);
    	$this->db->from('m_loan tl');
    	$this->db->join('m_loan_payment tlp', 'tl.loan_no = tlp.loan_no', 'INNER');
		//11.15.2010 rhye added join to mtransaction so that report  
		//will retrieve payments that are posted only
		//$this->db->join("(SELECT reference FROM m_transaction tt WHERE transaction_date BETWEEN '$start_date' AND '$end_date') tt", "tt.reference = CONCAT(tlp.loan_no, ',', tlp.transaction_code, ',', tlp.payment_date, ',', tlp.payor_id)", 'inner');
		$this->db->join('t_transaction tt', "tt.reference = CONCAT(tlp.loan_no, ',', tlp.transaction_code, ',', tlp.payment_date, ',', tlp.payor_id)", 'left outer');		
		if ($filter)
			$this->db->where($filter);
		$this->db->where("tlp.payment_date BETWEEN '$start_date' AND '$end_date'");
		if($orderby)	
    		$this->db->orderby($orderby);
		$result = $this->db->get();
			
		$query = $this->db->last_query();
		log_message('debug', 'aa-aa'.$query);
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
    }
	
	/**
     * @desc Retrieve employee's guaranteed loans.
     * @param $filter
     * @param $select
     */
    function retrieveGuaranteedLoans($filter = null, $select = null)
    {
    	if($select)
			$this->db->select($select);
    	$this->db->from('m_loan tl');
    	$this->db->join('m_loan_guarantor tlg', 'tl.loan_no = tlg.loan_no', 'LEFT OUTER');
    	$this->db->join('m_employee mm', 'tl.employee_id = mm.employee_id', 'INNER');
		if ($filter)
			$this->db->where($filter);
		
		$result = $this->db->get();
			
		$query = $this->db->last_query();
		$count = $result->num_rows();
		
		return $this->checkError($result->result_array(), $count, $query);
    }
	
	function retrieveOutstandingBalanceByLoan($filter = null, $select=null, $period, $groupby=null, $orderby=null){
		if($select)
			$this->db->select($select);
		$this->db->from('m_loan tl');
		$this->db->join('m_employee mm', 'tl.employee_id = mm.employee_id', 'INNER');
		//$this->db->join('r_loan_header rl', 'rl.loan_code = tl.loan_code', 'INNER');
		$joinSql = "(SELECT tlp.loan_no															
						,tlp.payment_date								
						,MIN(tlp.balance) AS amount								
						FROM m_loan_payment tlp								
						LEFT OUTER JOIN (SELECT loan_no								
								, MAX(payment_date) AS last_payment						
								, MAX(modified_date) AS last_update						
								FROM m_loan_payment						
								WHERE payment_date<='$period'						
									AND status_flag = '2'					
								GROUP BY loan_no) tlp2 						
							ON tlp.loan_no = tlp2.loan_no 							
								AND tlp.payment_date = tlp2.last_payment 						
								AND tlp.modified_date = tlp2.last_update						
						WHERE tlp.status_flag = '2'								
							AND tlp2.loan_no IS NOT NULL							
						GROUP BY tlp.loan_no								
							,tlp.payment_date) lp ";
		$this->db->join($joinSql, 'tl.loan_no = lp.loan_no', 'LEFT OUTER');
		$this->db->join('r_company rc', 'rc.company_code = mm.company_code', 'LEFT OUTER');
		if($filter)	
    		$this->db->where($filter);
		if($groupby)
			$this->db->group_by($groupby);
		if($orderby)	
    		$this->db->orderby($orderby);
		$result = $this->db->get(); 
    	$query = $this->db->last_query();//echo $query;exit();
		$count =$this->db->count_all_results();
    	return $this->checkError($result->result_array(), $count, $query);	
		/*"SELECT mm.employee_id															
				, DATE_FORMAT(MAX(lp.payment_date),'%m/%d/%Y') AS period														
				, mm.company_code														
				, rc.company_name														
				, SUM(COALESCE(lp.amount, tl.principal_balance)) AS amount														
				, mm.last_name														
				, mm.middle_name														
				, mm.first_name														
				, rl.loan_code AS transaction_code														
			FROM t_loan tl															
			INNER JOIN m_employee mm															
				ON tl.employee_id = mm.employee_id														
			INNER JOIN r_loan rl															
				ON rl.loan_code = tl.loan_code														
			LEFT OUTER JOIN (SELECT tlp.loan_no															
				,tlp.payment_date								
				,MIN(tlp.balance) AS amount								
				FROM t_loan_payment tlp								
				LEFT OUTER JOIN (SELECT loan_no								
						, MAX(payment_date) AS last_payment						
						, MAX(modified_date) AS last_update						
						FROM t_loan_payment						
						WHERE payment_date<='$period'						
							AND status_flag = '2'					
						GROUP BY loan_no) tlp2 						
					ON tlp.loan_no = tlp2.loan_no 							
						AND tlp.payment_date = tlp2.last_payment 						
						AND tlp.modified_date = tlp2.last_update						
				WHERE tlp.status_flag = '2'								
					AND tlp2.loan_no IS NOT NULL							
				GROUP BY tlp.loan_no								
					,tlp.payment_date) lp 							
				ON tl.loan_no = lp.loan_no														
			LEFT OUTER JOIN r_company rc															
				ON rc.company_code = mm.company_code														
			WHERE tl.loan_date <= '$period'															
				AND tl.status_flag = '2'														
			GROUP BY mm.company_code														
				, rc.company_name														
				, mm.last_name														
				, mm.first_name														
				, mm.middle_name
				, tl.employee_id															
				, rl.loan_code														
			ORDER BY company_code															
				, tl.employee_id ASC"; */
	
	
	}
}

?>
