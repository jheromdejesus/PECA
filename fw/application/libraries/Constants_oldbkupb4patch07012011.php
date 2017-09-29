<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Constants {
	
	var $transaction_group = array('CC'=>'Capital Contribution'
									,'DP'=>'Other Deposits'
									,'DV'=>'Dividends'
									,'II'=>'Investment Maturity'
									,'IN'=>'Investment'
									,'IS'=>'ISOP'
									,'LN'=>'Loan'
									,'LP'=>'Loan Payment'
									,'PD'=>'Payroll Deduction'
									,'SC'=>'Service Charges'
									,'TP'=>'Third Party Payments'
									);
									
	var $civil_status = array(
			""
			,"Single"
			,"Married"
			,"Separated"
			,"Widowed"
		);
		
	var $mysqlbin_dir = "C:\\wamp\\bin\\mysql\\mysql5.1.36\\bin\\";
									
									
	var $messages = array(
						'001' => 'Data successfully saved' //used when inserting and updating data
						,'002'=> 'Data was NOT successfully saved. Please recheck all fields'
						,'003' => 'Data successfully deleted' //used when deleting data
						,'004'=> 'Data was NOT successfully deleted'
						,'005'=> 'already exists , update status?' //used for checking for duplicate entries which have a status flag of 0
						,'006'=> 'already exists, edit?' //used for checking for duplicate entries which have a status flag of 1
						,'007'=> 'Employee still has remaining loans' //used when closing capital contribution
						,'008'=> 'Employee has invalid co-makers' //used to check when withdrawing from capcon
						,'009'=> 'Employee is still on suspension' //used to check when withdrawing from capcon
						,'010'=> 'Capcon balance after transaction is lesser than the capital contribution minimum balance' //used to check when withdrawing from capcon
						,'011'=> 'Employee has exceeded the withdrawal amount for a retiree' //used to check when withdrawing from capcon
						,'012'=> 'Employee\'s initial deposit should be a value greater than or equal to the capital contribution minimum balance.' ////used to check when making initial deposit to  capcon
						,'013'=> 'Principal Amount exceeds the maximum loan amount' ////used to check the principal amount in adding new loan
						,'014'=> 'Term shoule be between the minimum term and maximum term' ////used to check the minimum and maximum terms in adding new loan
						,'015'=> 'One of the employees cannot become a co-maker of the loan' ////used to check comakers when adding comakers for the new loan
						,'016'=> 'Capital Contribution Balance is below 1/3 of Loan Principal Amount' ////used to check 1/3 capital contribution of employee
						,'017'=> 'Isop start date cannot be prior to accounting period' //used during isop transactions
						,'018'=> 'Isop end date cannot be prior to accounting period' //used during isop transactions
						,'019'=> 'Isop date conflict, would you like to adjust?' //used during isop transactions
						,'020' => 'No records found'
						,'021' => 'Cannot Save Data. Entry Completely overlapped saved entry.'
						,'022'=> 'Employee does not exist' //checking before adding or updating capcon transactions
						,'023' => 'The employee has not rendered enough no of years of service for the applied loan.'
						,'024' => 'The employee is a retiree. Thus he cannot loan more than 50% of his capital contribution.'
						,'025'=> 'Clone was NOT successfully saved' //checking before adding or updating capcon transactions
						,'026'=> 'Start date should be the first day of the month' //checking before adding or updating payroll deduction
						,'027'=> 'End date should be the end day of the month' //checking before adding or updating payroll deduction
						,'028'=> 'Start date should not be prior to accounting period' //checking before adding or updating payroll deduction
						,'029'=> 'End date should not be prior to accounting period' //checking before adding or updating payroll deduction
						,'030'=> 'Debit credit total amounts are not equal' //checking when updating a journal entry 
						,'031'=> 'Start date is greater than the end date' //used during isop transactions
						,'032'=> 'The date must be the end of the month' //checking when generating investment report
						,'033'=> 'Charge was NOT successfully saved.' //checking when adding loan
						,'034'=> 'You have exceeded the number of guarantors.' //checking when adding loan guarantors
						,'035'=> 'Isop start date should be first day of the month' //checking isop transctions
						,'036'=> 'Isop end date should be last day of the month' //checking isop transctions
						,'037'=> 'Capital Contribution Balance after transaction is less than the Capital Contribution Minimum Balance' //used when checking addLoan
						,'038'=> 'Backup failed, invalid path' //used when backing up the database
						,'039'=> 'Backup file not successfully saved' //used when backing up the database
						,'040'=> 'Backup file successfully saved' //used when backing up the database
						,'041'=> 'Specified employee is not the applyer of the loan' //used when checking in loan payments
						,'042'=> 'Payor does not exist' // checking in loan payments
						,'043'=> 'Isop date conflict, would you like to adjust?' //used during isop transactions, same as error no 19, but with different error code
						,'044'=> 'Restore failed, file does not exist' //used during restore of DB		
						,'045'=> 'Database successfully restored' //used during restore of DB
						,'046'=> 'Entry has already been deleted' //used during update of header data
						,'047'=> 'Transaction code does not exist' //used in capital transactions when checking if transaction code exists
						,'048'=> 'Loan code payment type does not exist' //used in loan payments when checking if loan code payment type exists
						,'049'=> 'Loan code does not exist' //used in loan payments when checking if loan code exists
						,'050'=> 'Supplier does not exist' //used when checking if adding new journal entry
						,'051'=> 'Account number does not exist' //used when checking if adding new journal entry	
						,'052'=> 'No preview available for direct deposit' //used in online capital contribution
						,'053'=> 'Invalid loan payor.' //used in online capital contribution
						,'054'=> 'Employee cannot deposit more than 10 million.' //used in capital contribution deposit
						,'055'=> 'Employee is inactive.' //used in transactions
						,'056'=> 'Payor is inactive.' //used in transactions
						,'100'=>'User is successfully saved.'
						,'101'=>'User is successfully updated.'
						,'102'=>'User is successfully deleted.'
						,'103'=>'User was not successfully saved.'
						,'104'=>'User was not successfully updated.'
						,'105'=>'User was not successfully deleted.'
						,'106'=>'Group is successfully saved.'
						,'107'=>'Group is successfully updated.'
						,'108'=>'Group is successfully deleted.'
						,'109'=>'Group was not successfully saved.'
						,'110'=>'Group was not successfully updated.'
						,'111'=>'Group was not successfully deleted.'
						,'112'=>'Invalid Data. Please check inputted form details.'
						,'113'=>'Your system generated password : ' 
						,'114'=>'Your user id : ' 
						,'115'=>'New Registration for User : '
						,'116'=>'System generated password is sent to your email. Please use this password when logging in for the first time.'
						,'117'=>'System generated password was not sent to your email. Contact your Administrator. '
						,'118' =>'Please see below your registration information. '
						,'119' =>'User ID must not be empty  '
						,'120' =>'User Name must not be empty'
						,'121' =>'Group ID must not be empty'
						,'122' =>'Email Address must not be empty'
						,'123' =>'Email Address must be valid'
						,'124' =>'User data is empty'
						,'125' =>'Group ID must not be empty  '
						,'126' =>'Group Name must not be empty'
						,'127' =>'Group data is empty'
						,'128' =>'Passwords did not match'
						,'129' =>'User password is successfully updated.'
						,'130' =>'User password was not successfully updated.'
						,'131' =>'New and Confirm Passwords did not match.'
						,'132' =>'User Permission is successfully updated.'
						,'133' =>'User Permission was not successfully updated.'
						,'134' =>'Group Permission is successfully updated.'
						,'135' =>'Group Permission was not successfully updated.'
						,'136' =>'Old Password must not be empty.'
						,'137' =>'New Password must not be empty.'
						,'138' =>'Confirm Password must not be empty.'
						,'139' =>'Bulletin Board topic is successfully saved.'
						,'140' =>'Bulletin Board topic is successfully updated.'
						,'141' =>'Bulletin Board topic is successfully deleted'
						,'142' =>'Bulletin Board topic was not successfully saved.'
						,'143' =>'Bulletin Board topic was not successfully updated.'
						,'144' =>'Bulletin Board topic was not successfully deleted'
						,'145' =>'Bulletin Board data is empty.'
						,'146' =>'Bulletin Board topic attachment is successfully saved.'
						,'147' =>'Bulletin Board topic attachment is successfully updated.'
						,'148' =>'Bulletin Board topic attachment is successfully deleted.'
						,'149' =>'Bulletin Board topic attachment was not successfully saved.'
						,'150' =>'Bulletin Board topic attachment was not successfully updated.'
						,'151' =>'Bulletin Board topic attachment was not successfully deleted.'
						,'152' =>'File cant be generated.'
						);
									
	var $capcon_effect = array('-1'=>'Deducted From Capital Contribution'
								,'0'=>'No Effect to Capital Contribution'
								,'1'=>'Added From Capital Contribution'
								);
								
	var $member_status = array('A' => 'Active'
								,'I' => 'Inactive'
								);
		
	var $member_relationship = array('S' => 'Spouse'
									,'C' => 'Child'
									,'P' => 'Parent'
									,'O' => 'Others'
								);
								
	 var $defaultPermission = '000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000';
								
	var $account_group = array('A'=>'Assets'
								,'L'=>'Liabilities'
								,'C'=>'Capital'
								,'I'=>'Income'
								,'E'=>'Expenses'
								);
																					
	var $field_group = array( 
							'CC' => array( 'transaction_amount'	
										)
							,'DV' => array ('dividend rate'
											,'withholding tax rate'				
										) 
							,'II' => array('investment_amount'
										,'interest_income'
										,'principal_amount'
										,'interest_amount'
										)
							,'IN' => array('interest_amount'
										,'investment_amount'
										)
							,'IS' => array('amount'
										)
							,'LN' => array('restructure_amount'
										,'principal'
										,'term'
										,'interest_rate'
										,'initial_interest'
										,'employee_interest_total'
										,'employee_interest_amortization'
										,'employee_interest_vat_rate'
										,'employee_interest_vat_amount'
										,'employee_principal_amort'
										,'loan_proceeds'
										,'estate_value'
										,'principal_balance'
										,'interest_balance'
										,'cash_payments'
										,'down_payment_amount'
										,'mri_fip_amount'
										,'broker_fee_amount'
										,'governmet_fee_amount'
										,'other_fee_amount'
										,'service_fee_amount'
										,'pension'
										,'capital_contribution_balance'
										)
							,'LP' => array('amount'
										,'interest_amount'
										,'balance'
										)	
							,'PD' => array('amount'	
										)	
							,'SC' => array('amount'
										)	
							,'DP' => array()
							,'TP' => array()
				);
				
	function field_group_contents($field_group)
		{
			$result = array();
			
			foreach ($this->field_group[$field_group] as $field_group_element)
			{
				array_push($result, array($field_group_element));	
			}
		return $result;	
		}
	
	function create_list($data)
		{
		$result = array();
		
		foreach($data as $key => $value){
			array_push($result, array('code'=>$key,'name'=>$value));	
		}
		
		return $result;
	}
	
	var $access_rights_uri = array( '/capital_transaction/addHdr' => 0 				//0 'Add CapCon'
									,'/chart_of_accounts/add' => 1					//1 'Add Chart Account'
									,'/company/add' => 2							//2 'Add Company'
									,'Add Deposits'									//3 'Add Deposits'
									,'/gl_entries/addHdr' => 4						//4 'Add General Ledger'
									,'/group/add' => 5								//5 'Add Group'
									,'/information_code/add' => 6					//6 'Add Information Code'
									,'/investment/add' => 7							//7 'Add Investments'
									,'/isop/add' => 8								//8 'Add ISOP'
									,'/journal_entry/addHdr' => 9					//9 'Add Journal'
									,'/loan_code/addHdr' => 10						//10 'Add Loan Code'
									,'/loan_payment/add' => 11						//11 'Add Loan Payment'	
									,'/membership/add' => 12						//12 'Add Membership Information'
									,'/loan/addLoan' => 13							//13 'Add New Loans'
									,'/payroll_deduction/add' => 14					//14 'Add Payroll Deductions'
									,'/system_parameters/add' => 15					//15 'Add System Parameter'
									,'/supplier/addHdr' => 16						//16 'Add Third Party Supplier''
									,'/transaction_charges/add' => 17				//17 'Add Transaction Charges'
									,'/transaction_code/add' => 18					//18 'Add Transaction Code'
									,'/users/read' => 19
									,'Change Account Information'
									,'Change Amount'
									,'Change Bank Information'
									,'Check Guarantor'
									,'/adjustment/delete' => 24						//24 'Delete Adjustments'
									,'/capital_transaction/deleteHdr' => 25			//25 'Delete CapCon'
									,'/chart_of_accounts/delete' => 26				//26 'Delete Chart Account' 
									,'/company/delete' => 27						//27 'Delete Company'	
									,'Delete Deposits'
									,'/gl_entries/deleteHdr' => 29					//29 'Delete General Ledger'
									,'/group/delete' => 30							//30 'Delete Group'
									,'/information_code/delete' => 31				//31 'Delete Information Code'
									,'/investments/delete' => 32					//32 'Delete Investments'	
									,'/isop/delete' => 33							//33 'Delete ISOP'
									,'/journal_entry/deleteHdr' => 34				//34 'Delete Journal'
									,'/loan_code/deleteHdr' => 35					//35 'Delete Loan Code'
									,'/loan_payment/delete'	=> 36					//36 'Delete Loan Payments'
									,'/loan/deleteLoan' => 37						//37 'Delete New Loans'
									,'/payroll_deduction/delete' => 38				//38 'Delete Payroll Deductions'
									,'/system_parameters/delete' => 39				//39 'Delete System Parameter'
									,'/supplier/deleteHdr' 							//40 'Delete Third Party Supplier'
									,'/transaction_charges/delete' => 41			//41 'Delete Transaction Charges'
									,'/transaction_code/delete' => 42				//42 'Delete Transaction Code'
									,'/users/delete' => 43							//43 'Delete User'
									,'/dividend/processDividend' => 44				//44 'Dividend Proc Proceed Exec'
									,'/utilities/bankTransfer' => 45				//45 'Download Bank Transaction'
									,'/utilities/payrollDeduction' => 46				//46 'Download Payroll Deduction'
									,'Execute Journal Entry'
									,'/month_end/processMonthEnd' => 48				// 'Execute Month End Processing'
									,'/process_payroll_deduction/processPayrollDeduction' => 49	//'Execute Payroll Deductions'
									,'/post_transaction/postTransactions' => 50		//50 'Execute Post Transactions'
									,'/process_transaction/processTransactions' => 51		//51 'Execute Process Transactions'
									,'/capital_transaction/read' => 52				//52 'Find CapCon'
									,'Find Deposits'
									,'Find Guarantor Payment'
									,'/investment/read' => 55						//55 'Find Investments'
									,'/investment_maturity/read' => 56				//56 'Find Investments Maturity' 
									,'/isop/read'=> 57								//57 'Find ISOP'
									,'/journal_entry/readHdr' => 58					//58 'Find Journal'
									,'/loan_payment/readHdr' => 59					//59 'Find Loan Payment'
									,'Find Membership Info'
									,'/loan/readLoan' => 61							//61 'Find New Loans'
									,'/payroll_deduction/read' => 62				//62 'Find Payroll Deductions'
									,'Find Statement Account'
									,'/supplier/readHdr' => 63						//63 'Find Third Party Supplier'
									,'/process_isop/processIsop' => 65				//'ISOP Proceed Exec'
									,'Preview Accrued IR Placement' => 66
									,'Preview Amor Sched' => 67
									,'/report_consolidatedstatementofcondition' => 68	//'Preview Balance Sheet'
									,'/report_comparativeSOC' => 69			//'Preview Comparative SOC'
									,'/report_capitalcontribution/2' => 70			//Preview CapCon Audit Trail
									,'/report_capitalcontribution/1' => 71			//Preview CapCon Prooflist
									,'/report_summary' => 72		//'Preview CapCon Summary'
									,'Preview Cash Flow' => 73
									,'Preview Cash Position' => 74
									,'/report_dailysummary/3' => 75
									,'Preview Consolidated Income' => 76		//'Preview Consolidated Income
									,'/report_comparativeSOI' => 77			//'Preview Comparative Income'
									,'/report_transactioncontroltotals/1' => 78		//Preview Ctl Totals Company
									,'/report_transactioncontroltotals/2' => 79		//Preview Ctl Totals Transaction
									,'/report_transactioncontroltotals/3' => 80		//Preview Ctl Totals Transfer
									,'/report_deleted_transaction' => 81		//Preview Deleted Transactions Report
									,'/report_dailysummary/1' => 82
									,'/report_disbursementvoucher' => 83
									,'/report_dividend' => 84
									,'/report_futuredatedunposted' => 85
									,'Preview Guarantor Loan Pay' => 86
									,'Preview Guarantors Pay At' => 87
									,'/report_consolidatedSOI' => 88
									,'/report_interestearned' => 89
									,'/report_investment/2' => 90
									,'/report_investment/1' => 91
									,'/report_investment/3' => 92
									,'/report_isopdeduction/2' => 93
									,'/report_isopdeduction/1' => 94
									,'/report_journalentries/2' => 95
									,'/report_journalentries/1' => 96
									,'/report_loanpaymentpenalty' => 97
									,'/report_loanapplication/2' => 98
									,'/report_loanapplication/1' => 99
									,'/report_loanpayment/1' => 100
									,'/report_loanpayment/2' => 101
									,'/report_subsidye' => 102
									,'/report_loanyearterm' => 103
									,'/report_memberlist' => 104
									,'/report_payrolldeduction/2' => 105
									,'/report_payrolldeduction/1' => 106
									,'Preview Post Transactions' => 107
									,'Preview Resigned Guarantor' => 108
									,'/report_outstandingstatus' => 109
									,'/report_capconstatementofacct' => 110
									,'Preview Third Party PL' => 111
									,'/report_trialbalance' => 112
									,'/report_amortunearnedinterest' => 113
									,'Preview VAT Loans' => 114
									,'Resets Password' => 115
									,'/system_parameters/update' => 116				//116 'Save System Parameter'
									,'/adjustment/update' => 117					//117 'Save Adjustments'
									,'Save Bank Recon'
									,'/capital_transaction/updateHdr' => 119		//119 'Save CapCon'
									,'/chart_of_accounts/update' => 120				//120 'Save Chart Account'
									,'/company/update' => 121						//121 'Save Company'
									,'Save Deposits'
									,'/gl_entries/updateHdr' => 123					//123 'Save General Ledger'	
									,'/group/update' => 124							//124 'Save Group'
									,'Save Guarantor Payment'
									,'/information_code/update' => 126				//126 'Save Information Code'
									,'/investment/update' => 127					//127 'Save Investments'
									,'/investment_maturity/update' => 128			//128 'Save Investments Maturity'
									,'/isop/update' => 129							//129 'Save ISOP'
									,'/journal_entry/updateHdr' => 130				//130 'Save Journal'
									,'/loan_code/updateHdr' => 131					//131 'Save Loan Code'
									,'/loan_payment/update' => 132					//132 'Save Loan Payment'
									,'/membership/update' => 133					//133 'Save Membership Information'	
									,'loan/updateLoan' => 134						//134 'Save New Loans'
									,'/payroll_deduction/update' => 135				//135 'Save Payroll Deductions'
									,'/permissions/update' => 136 					//136 'Save Permission'
									,'/supplier/updateHdr' => 137		 			//137 'Save Third Party Supplier'
									,'/transaction_charges/update' => 138			//138 'Save Transaction Charges'
									,'/transaction_code/update' => 139				//139 'Save Transaction Code'
									,'/users/update' => 140			 				//140 'Save User'	
									,'/process_loan_payment/processLoanPayment' => 141	//'Execute Process Loan Payments'
									,'/loan_year/processLoanYearTerm' => 142		//'Execute Process Loan Year Term'
									,'/dormant_account/processDormantAccount' => 143	//'Execute Process Dormant Account'					
									);
									
	var $functions = array('Add CapCon'
									,'Add Chart Account'
									,'Add Company'
									,'Add Deposits'
									,'Add General Ledger'
									,'Add Group'
									,'Add Information Code'
									,'Add Investments'
									,'Add ISOP'
									,'Add Journal'
									,'Add Loan Code'
									,'Add Loan Payment'
									,'Add Membership Information'
									,'Add New Loans'
									,'Add Payroll Deductions'
									,'Add System Parameter'
									,'Add Third Party Supplier'
									,'Add Transaction Charges'
									,'Add Transaction Code'
									,'Add User'
									,'Change Account Information'
									,'Change Amount'
									,'Change Bank Information'
									,'Check Guarantor'
									,'Delete Adjustments'
									,'Delete CapCon'
									,'Delete Chart Account'
									,'Delete Company'
									,'Delete Deposits'
									,'Delete General Ledger'
									,'Delete Group'
									,'Delete Information Code'
									,'Delete Investments'
									,'Delete ISOP'
									,'Delete Journal'
									,'Delete Loan Code'
									,'Delete Loan Payments'
									,'Delete New Loans'
									,'Delete Payroll Deductions'
									,'Delete System Parameter'
									,'Delete Third Party Supplier'
									,'Delete Transaction Charges'
									,'Delete Transaction Code'
									,'Delete User'
									,'Dividend Proc Proceed Exec'
									,'Download Bank Transaction'
									,'Download Payroll Deduction'
									,'Execute Journal Entry'
									,'Execute Month End Processing'
									,'Execute Payroll Deductions'
									,'Execute Post Transactions'
									,'Execute Process Transactions'
									,'Find CapCon'
									,'Find Deposits'
									,'Find Guarantor Payment'
									,'Find Investments'
									,'Find Investments Maturity'
									,'Find ISOP'
									,'Find Journal'
									,'Find Loan Payment'
									,'Find Membership Info'
									,'Find New Loans'
									,'Find Payroll Deductions'
									,'Find Statement Account'
									,'Find Third Party Supplier'
									,'ISOP Proceed Exec'
									,'Preview Accrued IR Placement'
									,'Preview Amor Sched'
									,'Preview Balance Sheet'
									,'Preview Comparative SOC'
									,'Preview CapCon Audit Trail'
									,'Preview CapCon Proof List'
									,'Preview CapCon Summary'
									,'Preview Cash Flow'
									,'Preview Cash Position'
									,'Preview Collection'
									,'Preview Consolidated Income'
									,'Preview Comparative Income'
									,'Preview Ctl Totals Company'
									,'Preview Ctl Totals Employee'
									,'Preview Ctl Totals Transaction'
									,'Preview Deleted Transactions Report'
									,'Preview Disbursement'
									,'Preview Disbursement Voucher'
									,'Preview Dividend'
									,'Preview Future Dated Unposted JV Report'
									,'Preview Guarantor Loan Pay'
									,'Preview Guarantors Pay At'
									,'Preview Income Statement'
									,'Preview Interest Earned'
									,'Preview Investment Audit Trail'
									,'Preview Investment Proof List'
									,'Preview Investment Summary'
									,'Preview ISOP Audit Trail'
									,'Preview ISOP Proof List'
									,'Preview Journal Audit Trail'
									,'Preview Journal Proof List '
									,'Preview Loan Payment Due Report'
									,'Preview Loans Application'
									,'Preview Loans Application PL'
									,'Preview Loans Payments PList'
									,'Preview Loans Payment ATrail'
									,'Preview Loans Payment CoShare'
									,'Preview Loans Year Term'
									,'Preview Members Master List'
									,'Preview PAYDEC Audit Trail'
									,'Preview PAYDEC Proof List'
									,'Preview Post Transactions'
									,'Preview Resigned Guarantor'
									,'Preview Status Report'
									,'Preview Statement Account'
									,'Preview Third Party PL'
									,'Preview Trial Balance'
									,'Preview Unearned Interest'
									,'Preview VAT Loans'
									,'Resets Password'
									,'Save System Parameter'
									,'Save Adjustments'
									,'Save Bank Recon'
									,'Save CapCon'
									,'Save Chart Account'
									,'Save Company'
									,'Save Deposits'
									,'Save General Ledger'
									,'Save Group'
									,'Save Guarantor Payment'
									,'Save Information Code'
									,'Save Investments'
									,'Save Investments Maturity'
									,'Save ISOP'
									,'Save Journal'
									,'Save Loan Code'
									,'Save Loan Payment'
									,'Save Membership Information'
									,'Save New Loans'
									,'Save Payroll Deductions'
									,'Save Permission'
									,'Save Third Party Supplier'
									,'Save Transaction Charges'
									,'Save Transaction Code'
									,'Save User'
									,'Execute Process Loan Payments'
									,'Execute Process Loan Year Term'
									,'Execute Process Dormant Account'
									);
								
									





	// FOR MIGRATION OF DATA VARIABLES START
	
    var $dumpDirectoryFileName = 'temp_dir/pecav12.sql';
	/** The array of mssql and mysql table names **/
	var $tableNames =  array(
									'HCovered'=>'h_amla_covered'
									,'TCovered'=>'t_amla_covered'
									,'MBeneficiary'=>'m_beneficiary'
									,'MMember'=>'m_employee'
									,'MSupplier'=>'m_supplier'
									,'MSupplierInfo'=>'m_supplier_info'
									,'RAccount'=>'r_account'
									,'RAMLCMap'=>'r_amlc_map'
									,'RCompanyCode'=>'r_company'
									,'RGLEntryHdr'=>'r_gl_entry_header'
									,'RGLEntryDtl'=>'r_gl_entry_detail'
									,'RInfoCode'=>'r_information'
									,'RLoanSurety'=>'r_loan_detail'
									,'RLoan'=>'r_loan_header'
									,'IParamList'=>'i_parameter_list'
									,'RTranGroup'=>'r_trangroup'
									,'RTransaction'=>'r_transaction'
									,'RTranCharge'=>'r_transaction_charge'
									,'RUser'=>'r_user'
									,'RUserGroup'=>'r_user_group'
									,'TCapTranDtl'=>'t_capital_transaction_detail'
									,'TCapTran'=>'t_capital_transaction_header'
									,'MDividend'=>'t_dividend'
									,'MInvestmentDtl'=>'m_investment_detail'
									,'MInvestment'=>'m_investment_header'
									,'TInvestment'=>'t_investment_header'
									,'MISOP'=>'t_isop'
									,'TJournalDtl'=>'t_journal_detail'
									,'TJournalHdr'=>'t_journal_header'
									,'MCJournalHdr'=>'mc_journal_header'
								    ,'MCJournalDtl'=>'mc_journal_detail'
									,'MLedger'=>'t_ledger'
									,'TLoan'=>'t_loan'
									,'MLoanCharge'=>'m_loan_charges'
									,'TLoanCharge'=>'m_loan_charges'
									,'MLoanGuarantor'=>'m_loan_guarantor'
									,'TLoanGuarantor'=>'t_loan_guarantor'
									,'TLoanPayment'=>'t_loan_payment'
									,'MLoanPaymentDtl'=>'m_loan_payment_detail'
									,'TLoanPaymentDtl'=>'t_loan_payment_detail'
									,'MPayrollDedn'=>'t_payroll_deduction'
									,'MPosting'=>'t_posting'
									,'Ttransaction'=>'t_transaction'
									);
	/** The array of mssql and mysql table names [for batch process tables] **/								
	var $BatchTableNames =  array(
									'MCapCon'=>'t_capital_contribution'
									,'MCapTranDtl'=>'m_capital_transaction_detail'
									,'MCapTran'=>'m_capital_transaction_header'
									,'MJournalHdr'=>'m_journal_header'
									,'MLoan'=>'m_loan'
									,'MLoanAmort'=>'t_loan_amortization'
									,'MLoanPayment'=>'m_loan_payment'
								    ,'MJournalDtl'=>'m_journal_detail' //kani 500k records
									,'Mtransaction'=>'m_transaction'
									);
									
	/** The array of limiting column names per table [for batch process tables] **/								
   var $BatchTableLimitCol =  array('MCapCon'=>'AcctgPeriod'
									,'MCapTranDtl'=>'TranCode'
									,'MCapTran'=>'TranCode'
									,'MJournalDtl'=>'AcctNo'
									,'MCJournalDtl'=>'AcctNo'
									,'MJournalHdr'=>'AcctgPeriod'
									,'MCJournalHdr'=>'AcctgPeriod'
									,'MLoan'=>'LoanCode'
									,'MLoanAmort'=>'LoanNo'
									,'MLoanPayment'=>'PayorID'
									,'Mtransaction'=>'EmpID'
									);
									
	/** The array of mssql tables with the corresponding column names and mysql equivalence column **/							
	var $tables = array('HCovered'=>array('transaction_no'=>'TranNo'
									,'transaction_date'=>"convert(varchar, TranDate, 112)"
									,'employee_id'=>'EmpID'
									,'transaction_code'=>'TranCode'
									,'amla_code'=>'AMLCCode'
									,'transaction_amount'=>'TranAmt'
									,'report_date'=>"convert(varchar, ReportDate, 112)"
									,'status_flag'=> "'2'" //processed
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),	
						'TCovered'=>array('transaction_no'=>'TranNo'
									,'transaction_date'=>"convert(varchar, TranDate, 112)"
									,'employee_id'=>'EmpID'
									,'transaction_code'=>'TranCode'
									,'amla_code'=>'AMLCCode'
									,'transaction_amount'=>'TranAmt'
									,'report_date'=>"'0'"
									,'status_flag'=> "'1'" //saved
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),								
					    'MBeneficiary'=>array('member_id'=>'EmpID'
									,'sequence_no'=>'SeqNo'
									,'beneficiary'=>'Beneficiary'
									,'relationship'=>'Relationship'
									,'beneficiary_address'=>'BeneficiaryAddress'
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
						'MMember'=>array('employee_id'=>'EmpID'
									,'last_name'=>'LastName'
									,'first_name'=>'FirstName'
									,'middle_name'=>'MiddleName'
									,'member_date'=>"convert(varchar, MemberDate, 112)"
									,'bank_account_no'=>'BAcctNo'
									,'bank'=>'Bank'
									,'TIN'=>'TIN'
									,'hire_date'=>"convert(varchar, HireDate, 112)"
									,'work_date'=>"convert(varchar, LWorkDate, 112)"
									,'department'=>'Dept'
									,'position'=>'Position'
									,'company_code'=>'CompanyCode'
									,'email_address'=>'EmailAdd'
									,'office_no'=>'OfficeNo'
									,'mobile_no'=>'MobileNo'
									,'home_phone'=>'HomePhone'
									,'address_1'=>'Addr1'
									,'address_2'=>'Addr2'
									,'address_3'=>'Addr3'
									,'birth_date'=>"convert(varchar, BirthDate, 112)"
									,'civil_status'=>'CivilStatus'
									,'gender'=>'Gender'
									,'spouse'=>'Spouse'
									,'guarantor'=>'Guarantor'
									,'beneficiaries'=> 'ISNULL(Beneficiaries,0)'
									,'member_status'=>'Status'
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
						'MSupplier'=>array( 'supplier_id'=>'SupplierID'
									,'supplier_name'=>'SupplierName'
									,'TIN'=>'TIN'
									,'account_number'=>'AcctNo'
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),						
						'MSupplierInfo'=>array('supplier_id'=>'SupplierID'
									,'info_code'=>'InfoCode'
									,'info_text'=>'InfoText'
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
						'RAccount'=>array('account_no'=>'AcctNo'
									,'account_name'=>'AcctName'
									,'account_group'=>'AcctGroup'
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
						'RAMLCMap'=>array('transaction_code'=>'TranCode'
									,'amlc_code'=>'AMLCCode'
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
						'RCompanyCode'=>array('company_code'=>'CompanyCode'
									,'company_name'=>'CompanyName'
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					    'RGLEntryHdr'=>array('gl_code'=>'GLCode'
									,'gl_description'=>'GLDesc'
									,'particulars'=>'Particulars'
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
						'RGLEntryDtl'=>array('gl_code'=>'GLCode'
									,'account_no'=>'AcctNo'
									,'debit_credit'=>'DebitCredit'
									,'field_name'=>'FieldName'
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
						'RInfoCode'=>array('information_code'=>'InfoCode'
									,'information_description'=>'InfoDesc'
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
						'RLoanSurety'=>array('loan_code'=>'LoanCode'
									,'years_of_service'=>'ISNULL(YOS,0)'
									,'capital_contribution'=>'ISNULL(CapCon,0)'
									,'pension'=>'Pension'
									,'guarantor'=>'Guarantor'
									,'created_by'=>'0'
									,'created_date'=>'0'
									,'modified_by'=>'0'
									,'modified_date'=>'0'									
									),
					    'RLoan'=>array('loan_code'=>'LoanCode'
									,'loan_description'=>'LoanDesc'
									,'priority'=>'ISNULL(Priority,0)'
									,'min_emp_months'=>'ISNULL(MinEmpMonths,0)'
									,'max_loan_amount'=>'ISNULL(MaxLoanAmt,0)'
									,'min_term'=>'ISNULL(MinTerm,0)'
									,'max_term'=>'ISNULL(MaxTerm,0)'
									,'restructure'=>'Restructure'
									,'emp_interest_pct'=>'ISNULL(EEInterestPct,0)'
									,'comp_share_pct'=>'ISNULL(CoInterestPct,0)'
									,'downpayment_pct'=>'ISNULL(DownPaymentPct,0)'
									,'payroll_deduction'=>'PayDedn'
									,'unearned_interest'=>'UnearnedInt'
									,'interest_earned'=>'IntEarned'
									,'payment_code'=>'PaymentCode'
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					   'IParamList'=>array('parameter_id'=>'ParamID'
									,'parameter_name'=>'ParamName'
									,'parameter_value'=>'ParamVal'
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					   'RTranGroup'=>array('transaction_group'=>'TranGroup'
									,'transaction_group_description'=>'TranGroupDesc'
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					   'RTransaction'=>array('transaction_code'=>'TranCode'
									,'transaction_description'=>'TranDesc'
									,'gl_code'=>'GLCode'
									,'transaction_group'=>'TranGroup'
									,'wage_type'=>'WageType'
									,'capcon_effect'=>'ISNULL(CapConEffect,0)'
									,'with_or'=>'WithOR'
									,'bank_transfer'=>'BankTransfer'
									,'capcon_req'=>'CapConReq'
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					   'RTranCharge'=>array('transaction_code'=>'TranCode'
									,'charge_code'=>'ChargeCode'
									,'charge_formula'=>'ChargeFormula'
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					   'RUser'=>array('user_id'=>'UserID'
									,'user_name'=>'UserName'
									,'group_id'=>'GroupID'
									,'password'=>'Password'
									,'expired_date' => "convert(varchar, ExpireDate, 112)"
									,'permission'=> "'0'" //awa ni diri can
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
						'RUserGroup'=>array('group_id'=>'GroupID'
									,'group_name'=>'GroupName'
									,'permission'=>"'0'" //kani pud
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					   'MCapCon'=>array('employee_id'=>'EmpID'
									,'accounting_period'=>"convert(varchar, AcctgPeriod, 112)"
									,'beginning_balance'=>'ISNULL(BegBal,0)'
									,'ending_balance'=>'ISNULL(EndBal,0)'
									,'minimum_balance'=>'ISNULL(MinBal,0)'
									,'maximum_balance'=>'ISNULL(MaxBal,0)'
									,'status_flag'=> "'1'" //saved
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					   'MCapTranDtl'=>array('transaction_no'=>'TranNo'
									,'transaction_code'=>'TranCode'
									,'amount'=>'ISNULL(Amount,0)'
									,'status_flag'=> "'2'" //processed
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					   'TCapTranDtl'=>array('transaction_no'=>'TranNo'
									,'transaction_code'=>'TranCode'
									,'amount'=>'ISNULL(Amount,0)'
									,'status_flag'=> "'1'" //processed
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					   'MCapTran'=>array('transaction_no'=>'TranNo'
									,'transaction_date'=>"convert(varchar, TranDate, 112)"
									,'transaction_code'=>'TranCode'
									,'employee_id'=>'EmpID'
									,'transaction_amount'=>'TranAmt'
									,'or_no'=>'ORNo'
									,'status_flag'=> "'2'" //processed
									,'or_date'=>"convert(varchar, ORDate, 112)"
									,'remarks'=>'Remarks'
									,'bank_transfer'=>'BankTransfer'
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					    'TCapTran'=>array('transaction_no'=>'TranNo'
									,'transaction_date'=>"convert(varchar, TranDate, 112)"
									,'transaction_code'=>'TranCode'
									,'employee_id'=>'EmpID'
									,'transaction_amount'=>'TranAmt'
									,'or_no'=>'ORNo'
									,'status_flag'=> "'1'" //saved
									,'or_date'=>"convert(varchar, ORDate, 112)"
									,'remarks'=>'Remarks'
									,'bank_transfer'=>'BankTransfer'
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					   'MDividend'=>array('dividend_no'=>'DividendNo'
									,'accounting_period'=>"convert(varchar, AcctgPeriod, 112)"
									,'start_date'=>"convert(varchar, StartDate, 112)"
									,'end_date'=>"convert(varchar, EndDate, 112)"
									,'dividend_code'=>'DivCode'
									,'dividend_rate'=>'ISNULL(DividendRate,0)'
									,'with_tax_code'=>'WTaxCode'
									,'with_tax_rate'=>'ISNULL(WTaxRate,0)'
									,'status_flag'=> "'1'" //saved
									,'vat_code'=>'VATCode'
									,'vat_rate'=>'ISNULL(VATRate,0)'
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					   'MInvestmentDtl'=>array('investment_no'=>'InvNo'
									,'accounting_period'=>"convert(varchar, AcctgPeriod, 112)"
									,'amount'=>'Amount'
								    ,'status_flag'=> "'2'" //processed
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					  'MInvestment'=>array('investment_no'=>'InvNo'
									,'transaction_code'=>'TranCode'
									,'supplier_id'=>'SupplierID'
									,'placement_date'=>"convert(varchar, PlacementDate, 112)"
									,'maturity_date'=>"convert(varchar, MaturityDate, 112)"
									,'placement_days'=>'ISNULL(PlacementDays,0)'
									,'interest_rate'=>'ISNULL(IntRate,0)'
									,'interest_amount'=>'ISNULL(IntAmount,0)'
									,'investment_amount'=>'ISNULL(InvestmentAmount,0)'
									,'remarks'=>'Remarks'
									,'action_code'=>'ActionCode'
									,'maturity_code'=>'MaturityCode'
									,'principal_amount'=>'ISNULL(PrincipalAmount,0)'
									,'interest_income'=>'ISNULL(InterestIncome,0)'
									,'or_no'=>'ORNo'
									,'or_date'=>"convert(varchar, ORDate, 112)"
									,'transaction_date'=>"convert(varchar, TranDate, 112)"
									,'accrual'=>'ISNULL(Accrual,0)'
									,'rollover_placement_date'=>"convert(varchar, RollPlacementDate, 112)"
									,'rollover_placement_days'=>'ISNULL(RollPlacementDays,0)'
									,'rollover_maturity_date'=>"convert(varchar, RollMaturityDate, 112)"
									,'rollover_interest_rate'=>'ISNULL(RollIntRate,0)'
									,'rollover_interest_amount'=>'ISNULL(RollIntAmount,0)'
									,'processed'=>'Processed'
									,'status_flag'=> "'2'" //processed
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					 'TInvestment'=>array('investment_no'=>'InvNo'
									,'transaction_code'=>'TranCode'
									,'supplier_id'=>'SupplierID'
									,'placement_date'=>"convert(varchar, PlacementDate, 112)"
									,'maturity_date'=>"convert(varchar, MaturityDate, 112) "
									,'placement_days'=>'ISNULL(PlacementDays,0)'
									,'interest_rate'=>'ISNULL(IntRate,0)'
									,'interest_amount'=>'ISNULL(IntAmount,0)'
									,'investment_amount'=>'ISNULL(InvestmentAmount,0)'
									,'remarks'=>'Remarks'
									,'status_flag'=> "'1'" //processed
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					  'MISOP'=>array('transaction_no'=>'TranNo'
									,'employee_id'=>'EmpID'
									,'start_date'=> "convert(varchar, StartDate, 112)"
									,'end_date'=>"convert(varchar, EndDate, 112)"
									,'amount'=>'ISNULL(Amount,0)'
									,'transaction_period'=>"convert(varchar, TranPeriod, 112)"
									,'transaction_type'=>'TranType'
									,'status_flag'=> "'1'" //saved
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					 'MJournalDtl'=>array('journal_no'=>'JournalNo'
									,'account_no'=>'AcctNo'
									,'debit_credit'=>'DebitCredit'
									,'amount'=>'ISNULL(Amount,0)'
									,'status_flag'=> "'4'" //processed
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					'MCJournalDtl'=>array('journal_no'=>'JournalNo'
									,'account_no'=>'AcctNo'
									,'debit_credit'=>'DebitCredit'
									,'amount'=>'ISNULL(Amount,0)'
									,'status_flag'=> "'0'" //processed
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									),
					'TJournalDtl'=>array('journal_no'=>'JournalNo'
									,'account_no'=>'AcctNo'
									,'debit_credit'=>'DebitCredit'
									,'amount'=>'ISNULL(Amount,0)'
									,'status_flag'=> "'3'" //saved
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					'MJournalHdr'=>array('journal_no'=>'JournalNo'
									,'accounting_period'=>"convert(varchar, AcctgPeriod, 112)"
									,'transaction_code'=>'TranCode'
									,'transaction_date'=>"convert(varchar, TranDate, 112)"
									,'particulars'=>'Particulars'
									,'reference'=>'Reference'
									,'source'=>'Source'
									,'document_no'=>'DocNo'
									,'document_date'=>"convert(varchar, DocDate, 112)"
									,'remarks'=>'Remarks'
									,'supplier_id'=>'SupplierID'
									,'status_flag'=> "'4'" //processed
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					'MCJournalHdr'=>array('journal_no'=>'JournalNo'
									,'accounting_period'=>"convert(varchar, AcctgPeriod, 112)"
									,'transaction_code'=>'TranCode'
									,'transaction_date'=>"convert(varchar, TranDate, 112)"
									,'particulars'=>'Particulars'
									,'reference'=>'Reference'
									,'source'=>'Source'
									,'document_no'=>'DocNo'
									,'document_date'=>"convert(varchar, DocDate, 112)"
									,'remarks'=>'Remarks'
									,'supplier_id'=>'SupplierID'
									,'status_flag'=> "'0'" //processed
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
				    'TJournalHdr'=>array('journal_no'=>'JournalNo'
									,'accounting_period'=>"convert(varchar, AcctgPeriod, 112)"
									,'transaction_code'=>'TranCode'
									,'transaction_date'=>"convert(varchar, TranDate, 112)"
									,'particulars'=>'Particulars'
									,'reference'=>'Reference'
									,'source'=>'Source'
									,'document_no'=>'DocNo'
									,'document_date'=>"convert(varchar, DocDate, 112) + replace(convert(varchar(10),DocDate,108),':','')"
									,'remarks'=>'Remarks'
									,'supplier_id'=>'SupplierID'
									,'status_flag'=> "'3'" //saved
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					'MLedger'=>array('account_no'=>'AcctNo'
									,'accounting_period'=>"convert(varchar, AcctgPeriod, 112)"
									,'beginning_balance'=>'ISNULL(BegBal,0)'
									,'debits'=>'ISNULL(Debits,0)'
									,'credits'=>'ISNULL(Credits,0)'
									,'ending_balance'=>'ISNULL(EndBal,0)'
									,'status_flag'=> "'1'" //saved
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					'MLoan'=>array('loan_no'=>'LoanNo'
									,'restructure_no'=>'RestructureNo'
									,'restructure_amount'=>'ISNULL(RestructureAmt,0)'
									,'employee_id'=>'EmpID'
									,'loan_code'=>'LoanCode'
									,'loan_date'=>"convert(varchar, LoanDate, 112)"
									,'principal'=>'ISNULL(Principal,0)'
									,'term'=>'ISNULL(Term,0)'
									,'interest_rate'=>'ISNULL(IntRate,0)'
									,'initial_interest'=>'ISNULL(InitialInt,0)'
									,'employee_interest_total'=>'ISNULL(EEIntTotal,0)'
									,'employee_interest_amortization'=>'ISNULL(EEIntAmort,0)'
									,'employee_interest_vat_rate'=>'ISNULL(EEIntVATRate,0)'
									,'employee_interest_vat_amount'=>'ISNULL(EEintVATAmt,0)'
									,'company_interest_rate'=>'ISNULL(CoIntRate,0)'
									,'company_interest_total'=>'ISNULL(CoIntTotal,0)'
									,'company_interest_amort'=>'ISNULL(CoIntAmort,0)'
									,'amortization_startdate'=>"convert(varchar, AmortStartDate, 112)"
									,'employee_principal_amort'=>'ISNULL(EEPrinAmort,0)'
									,'loan_proceeds'=>'ISNULL(LoanProceeds,0)'
									,'estate_value'=>'ISNULL(EstateValue,0)'
									,'principal_balance'=>'ISNULL(PrinBalance,0)'
									,'interest_balance'=>'ISNULL(IntBalance,0)'
									,'cash_payments'=>'ISNULL(CashPayments,0)'
									,'down_payment_percentage'=>'ISNULL(DownPaymentPct,0)'
									,'down_payment_amount'=>'ISNULL(DownPaymentAmt,0)'
									,'mri_fip_amount'=>'ISNULL(MRIFIPAmt,0)'
									,'broker_fee_amount'=>'ISNULL(BrokerFeeAmt,0)'
									,'government_fee_amount'=>'ISNULL(GovtFeeAmt,0)'
									,'other_fee_amount'=>'ISNULL(OtherFeeAmt,0)'
									,'service_fee_amount'=>'ISNULL(ServiceFeeAmt,0)'
									,'pension'=>'ISNULL(Pension,0)'
									,'capital_contribution_balance'=>'ISNULL(CapConBalance,0)'
									,'bank_transfer'=>'BankTransfer'
									,'insurance_broker'=>'InsBroker'
									,'appraiser_broker'=>'AppBroker'
									,'check_no'=>'CheckNo'
									,'status_flag'=> "'2'" //processed
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					'TLoan'=>array('loan_no'=>'LoanNo'
									,'restructure_no'=>'RestructureNo'
									,'restructure_amount'=>'ISNULL(RestructureAmt,0)'
									,'employee_id'=>'EmpID'
									,'loan_code'=>'LoanCode'
									,'loan_date'=>"convert(varchar, LoanDate, 112)"
									,'principal'=>'ISNULL(Principal,0)'
									,'term'=>'ISNULL(Term,0)'
									,'interest_rate'=>'ISNULL(IntRate,0)'
									,'initial_interest'=>'ISNULL(InitialInt,0)'
									,'employee_interest_total'=>'ISNULL(EEIntTotal,0)'
									,'employee_interest_amortization'=>'ISNULL(EEIntAmort,0)'
									,'employee_interest_vat_rate'=>'ISNULL(EEIntVATRate,0)'
									,'employee_interest_vat_amount'=>'ISNULL(EEintVATAmt,0)'
									,'company_interest_rate'=>'ISNULL(CoIntRate,0)'
									,'company_interest_total'=>'ISNULL(CoIntTotal,0)'
									,'company_interest_amort'=>'ISNULL(CoIntAmort,0)'
									,'amortization_startdate'=>"convert(varchar, AmortStartDate, 112)"
									,'employee_principal_amort'=>'ISNULL(EEPrinAmort,0)'
									,'loan_proceeds'=>'ISNULL(LoanProceeds,0)'
									,'estate_value'=>'ISNULL(EstateValue,0)'
									,'principal_balance'=>'ISNULL(PrinBalance,0)'
									,'interest_balance'=>'ISNULL(IntBalance,0)'
									,'cash_payments'=>'ISNULL(CashPayments,0)'
									,'down_payment_percentage'=>'ISNULL(DownPaymentPct,0)'
									,'down_payment_amount'=>'ISNULL(DownPaymentAmt,0)'
									,'mri_fip_amount'=>'ISNULL(MRIFIPAmt,0)'
									,'broker_fee_amount'=>'ISNULL(BrokerFeeAmt,0)'
									,'government_fee_amount'=>'ISNULL(GovtFeeAmt,0)'
									,'other_fee_amount'=>'ISNULL(OtherFeeAmt,0)'
									,'service_fee_amount'=>'ISNULL(ServiceFeeAmt,0)'
									,'pension'=>'ISNULL(Pension,0)'
									,'capital_contribution_balance'=>'ISNULL(CapConBalance,0)'
									,'bank_transfer'=>'BankTransfer'
									,'insurance_broker'=>'InsBroker'
									,'appraiser_broker'=>'AppBroker'
									,'check_no'=>'CheckNo'
									,'status_flag'=> "'1'" //saved
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					'MLoanAmort'=>array('loan_no'=>'LoanNo'
									,'period'=>"convert(varchar, Period, 112) "
									,'employee_principal_amort'=>'ISNULL(EEPrinAmort,0)'
									,'employee_interest_amortization'=>'ISNULL(EEIntAmort,0)'
									,'employee_company_amortization'=>'ISNULL(EECoAmort,0)'
									,'principal_balance'=>'ISNULL(PrinBalance,0)'
									,'interest_balance'=>'ISNULL(IntBalance,0)'
									,'company_balance'=>'ISNULL(CoBalance,0)'
									,'status_flag'=> "'1'" //processed
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					'MLoanCharge'=>array('loan_no'=>'LoanNo'
									,'transaction_code'=>'TranCode'
									,'amount'=>'ISNULL(Amount,0)'
									,'status_flag'=> "'2'" //processed
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					'TLoanCharge'=>array('loan_no'=>'LoanNo'
									,'transaction_code'=>'TranCode'
									,'amount'=>'ISNULL(Amount,0)'
									,'status_flag'=> "'1'" //processed
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					'MLoanGuarantor'=>array('loan_no'=>'LoanNo'
									,'guarantor_id'=>'GuarantorID'
									,'amortization_amount'=>'ISNULL(AmortAmt,0)'
									,'interest_amount'=>'ISNULL(IntAmt,0)'
									,'status_flag'=> "'2'" //processed
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					'TLoanGuarantor'=>array('loan_no'=>'LoanNo'
									,'guarantor_id'=>'GuarantorID'
									,'amortization_amount'=>'0'
									,'interest_amount'=>'0'
									,'status_flag'=> "'1'" //processed
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					'MLoanPayment'=>array('loan_no'=>'LoanNo'
									,'payment_date'=>"convert(varchar, PaymentDate, 112)"
									,'transaction_code'=>'TranCode'
									,'payor_id'=>'PayorID'
									,'or_no'=>'ORNo'
									,'or_date'=>"convert(varchar, ORDate, 112)"
									,'amount'=>'ISNULL(Amount,0)'
									,'interest_amount'=>'ISNULL(IntAmount,0)'
									,'source'=>'Source'
									,'remarks'=>'Remarks'
									,'balance'=>'ISNULL(Balance,0)'
									,'status_flag'=> "'2'" //processed
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					'TLoanPayment'=>array('loan_no'=>'LoanNo'
									,'payment_date'=>"convert(varchar, PaymentDate, 112)"
									,'transaction_code'=>'TranCode'
									,'payor_id'=>'PayorID'
									,'or_no'=>'ORNo'
									,'or_date'=>"convert(varchar, ORDate, 112)"
									,'amount'=>'ISNULL(Amount,0)'
									,'interest_amount'=>'ISNULL(IntAmount,0)'
									,'source'=>'Source'
									,'remarks'=>'Remarks'
									,'balance'=>'ISNULL(Balance,0)'
									,'status_flag'=> "'1'" //saved
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					'MLoanPaymentDtl'=>array('loan_no'=>'LoanNo'
									,'payment_date'=>"convert(varchar, PaymentDate, 112)"
									,'transaction_code'=>'TranCode'
									,'payor_id'=>'PayorID'
									,'amount'=>'ISNULL(Amount,0)'
									,'status_flag'=> "'2'" //processed
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					'TLoanPaymentDtl'=>array('loan_no'=>'LoanNo'
									,'payment_date'=>"convert(varchar, PaymentDate, 112)"
									,'transaction_code'=>'TranCode'
									,'payor_id'=>'PayorID'
									,'amount'=>'ISNULL(Amount,0)'
									,'status_flag'=> "'1'" //saved
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					'MPayrollDedn'=>array('employee_id'=>'EmpID'
									,'transaction_code'=>'TranCode'
									,'start_date'=>"convert(varchar, StartDate, 112)"
									,'end_date'=>"convert(varchar, EndDate, 112)"
									,'amount'=>'Amount'
									,'transaction_type'=>'TranType'
									,'transaction_period'=>"convert(varchar, TranPeriod, 112)"
									,'status_flag'=> "'1'" //processed
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					'MPosting'=>array('accounting_period'=>"convert(varchar, AcctgPeriod, 112)"
									,'capital_contribution'=>'ISNULL(CapCon,0)'
									,'journal'=>'ISNULL(Journal,0)'
									,'capital_contribution_user'=>'CapConUser'
									,'capital_contribution_date'=>"convert(varchar, CapConDate, 112)"
									,'journal_user'=>'JournalUser'
									,'journal_date'=>"convert(varchar, JournalDate, 112) + replace(convert(varchar(10),JournalDate,108),':','')"
									),
				   'Mtransaction'=>array('transaction_no'=>'TranNo'
									,'transaction_date'=>"convert(varchar, TranDate, 112)"
									,'transaction_code'=>'TranCode'
									,'employee_id'=>'EmpID'
									,'transaction_amount'=>'ISNULL(TranAmt,0)'
									,'source'=>'Source'
									,'reference'=>'Reference'
									,'remarks'=>'Remarks'
									,'status_flag'=> "'3'" //posted
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									),
					'Ttransaction'=>array('transaction_no'=>'TranNo'
									,'transaction_date'=>"convert(varchar, TranDate, 112)"
									,'transaction_code'=>'TranCode'
									,'employee_id'=>'EmpID'
									,'transaction_amount'=>'ISNULL(TranAmt,0)'
									,'source'=>'Source'
									,'reference'=>'Reference'
									,'remarks'=>'Remarks'
									,'status_flag'=> "'2'" //processed
									,'created_by'=>'LUser'
									,'created_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"
									,'modified_by'=>'LUser'
									,'modified_date'=>"convert(varchar, LUpdate, 112) + replace(convert(varchar(10),LUpdate,108),':','')"									
									));
									
	/** The array of mysql table and columns with corresponding data type per column **/								
	var $tables2 = array('h_amla_covered'=>array('transaction_no'=>'string'
									,'transaction_date'=>'string'
									,'employee_id'=>'string'
									,'transaction_code'=>'string'
									,'amla_code'=>'string'
									,'transaction_amount'=>'integer'
									,'report_date'=>'string'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'							
									),
						't_amla_covered'=>array('transaction_no'=>'string'
									,'transaction_date'=>'string'
									,'employee_id'=>'string'
									,'transaction_code'=>'string'
									,'amla_code'=>'string'
									,'transaction_amount'=>'integer'
									,'report_date'=>'string'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'							
									),
						'm_beneficiary'=>array('member_id'=>'string'
									,'sequence_no'=>'integer'
									,'beneficiary'=>'string'
									,'relationship'=>'string'
									,'beneficiary_address'=>'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'
									),
						'm_employee'=>array('employee_id'=>'string'
									,'last_name'=>'string'
									,'first_name'=>'string'
									,'middle_name'=>'string'
									,'member_date'=>'string'
									,'bank_account_no'=>'string'
									,'bank'=>'string'
									,'TIN'=>'string'
									,'hire_date'=>'string'
									,'work_date'=>'string'
									,'department'=>'string'
									,'position'=>'string'
									,'company_code'=>'string'
									,'email_address'=>'string'
									,'office_no'=>'string'
									,'mobile_no'=>'string'
									,'home_phone'=>'string'
									,'address_1'=>'string'
									,'address_2'=>'string'
									,'address_3'=>'string'
									,'birth_date'=>'string'
									,'civil_status'=>'string'
									,'gender'=>'string'
									,'spouse'=>'string'
									,'guarantor'=>'string'
									,'beneficiaries'=>'integer'
									,'member_status'=>'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'								
									),			
						'm_supplier'=>array('supplier_id'=>'string'
									,'supplier_name'=>'string'
									,'TIN'=>'string'
									,'account_number'=>'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'
									),
						'm_supplier_info'=>array('supplier_id'=>'string'
									,'info_code'=>'string'
									,'info_text'=>'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'					
									),
						'r_account'=>array('account_no'=>'string'
									,'account_name'=>'string'	
									,'account_group'=>'string'	
									,'created_by'=>'string'	
									,'created_date'=>'string'	
									,'modified_by'=>'string'	
									,'modified_date'=>'string'							
									),
						'r_amlc_map'=>array('transaction_code'=>'string'	
									,'amlc_code'=>'string'	
									,'created_by'=>'string'	
									,'created_date'=>'string'	
									,'modified_by'=>'string'
									,'modified_date'=>'string'						
									),
						'r_company'=>array('company_code'=>'string'	
									,'company_name'=>'string'	
									,'created_by'=>'string'	
									,'created_date'=>'string'	
									,'modified_by'=>'string'	
									,'modified_date'=>'string'	
									),
						'r_gl_entry_header'=>array('gl_code'=>'string'
									,'gl_description'=>'string'
									,'particulars'=>'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'
									),
						'r_gl_entry_detail'=>array('gl_code'=>'string'
									,'account_no'=>'string'
									,'debit_credit'=>'string'
									,'field_name'=>'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'
									),
						'r_information'=>array('information_code'=>'string'
									,'information_description'=>'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'
									),
						'r_loan_detail'=>array('loan_code'=>'string'
									,'years_of_service'=>'integer'
									,'capital_contribution'=>'integer'
									,'pension'=>'string'
									,'guarantor'=>'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'
									),
						'r_loan_header'=>array('loan_code'=>'string'
									,'loan_description'=>'string'
									,'priority'=>'int'
									,'min_emp_months'=>'int'
									,'max_loan_amount'=>'int'
									,'min_term'=>'int'
									,'max_term'=>'int'
									,'restructure'=>'string'
									,'emp_interest_pct'=>'int'
									,'comp_share_pct'=>'int'
									,'downpayment_pct'=>'int'
									,'payroll_deduction'=>'string'
									,'unearned_interest'=>'string'
									,'interest_earned'=>'string'
									,'payment_code'=>'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'				
									),
						'i_parameter_list'=>array('parameter_id'=>'string'
									,'parameter_name'=>'string'
									,'parameter_value'=>'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'			
									),
						'r_trangroup'=>array('transaction_group'=>'string'	
									,'transaction_group_description'=>'string'	
									,'created_by'=>'string'	
									,'created_date'=>'string'	
									,'modified_by'=>'string'	
									,'modified_date'=>'string'							
									),
						'r_transaction'=>array('transaction_code'=>'string'
									,'transaction_description'=>'string'
									,'gl_code'=>'string'
									,'transaction_group'=>'string'
									,'wage_type'=>'string'
									,'capcon_effect'=>'int'
									,'with_or'=>'string'
									,'bank_transfer'=>'string'
									,'capcon_req'=>'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'					
									),
						'r_transaction_charge'=>array('transaction_code'=>'string'
									,'charge_code'=>'string'
									,'charge_formula'=>'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'						
									),
						'r_user'=>array('user_id'=>'string'
									,'user_name'=>'string'
									,'group_id'=>'string'
									,'password'=>'string'
									,'expired_date' => 'string'
									,'permission'=>'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'	
									),
						'r_user_group'=>array('group_id'=>'string'
									,'group_name'=>'string'
									,'permission'=>'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'					
									),
						't_capital_contribution'=>array('employee_id'=>'string'
									,'accounting_period'=>'string'
									,'beginning_balance'=>'int'
									,'ending_balance'=>'int'
									,'minimum_balance'=>'int'
									,'maximum_balance'=>'int'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=> 'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'
									),
						't_capital_transaction_detail'=>array('transaction_no'=>'string'
									,'transaction_code'=>'string'
									,'amount'=>'int'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'		
									),
						'm_capital_transaction_detail'=>array('transaction_no'=>'string'
									,'transaction_code'=>'string'
									,'amount'=>'int'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'		
									),
					    't_capital_transaction_header'=>array('transaction_no'=>'string'
									,'transaction_date'=>'string'
									,'transaction_code'=>'string'
									,'employee_id'=>'string'
									,'transaction_amount'=>'string'
									,'or_no'=>'string'
									,'or_date'=>'string'
									,'remarks'=>'string'
									,'bank_transfer'=>'string'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'		
									),
						'm_capital_transaction_header'=>array('transaction_no'=>'string'
									,'transaction_date'=>'string'
									,'transaction_code'=>'string'
									,'employee_id'=>'string'
									,'transaction_amount'=>'string'
									,'or_no'=>'string'
									,'or_date'=>'string'
									,'remarks'=>'string'
									,'bank_transfer'=>'string'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'		
									),
					  't_dividend'=>array('dividend_no'=>'string'
									,'accounting_period'=>'string'
									,'start_date'=>'string'
									,'end_date'=>'string'
									,'dividend_code'=>'string'
									,'dividend_rate'=>'int'
									,'with_tax_code'=>'string'
									,'with_tax_rate'=>'int'
									,'vat_code'=>'string'
									,'vat_rate'=>'int'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'			
									),
						't_investment_detail'=>array('investment_no'=>'string'
									,'accounting_period'=>'string'
									,'amount'=>'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'status_flag' => 'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'			
									),
						'm_investment_detail'=>array('investment_no'=>'string'
									,'accounting_period'=>'string'
									,'amount'=>'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'status_flag' => 'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'
									),
						't_investment_header'=>array('investment_no'=>'string'
									,'transaction_code'=>'string'
									,'supplier_id'=>'string'
									,'placement_date'=>'string'
									,'maturity_date'=>'string'
									,'placement_days'=>'int'
									,'interest_rate'=>'int'
									,'interest_amount'=>'int'
									,'investment_amount'=>'int'
									,'remarks'=>'string'
									,'action_code'=>'string'
									,'maturity_code'=>'string'
									,'principal_amount'=>'int'
									,'interest_income'=>'int'
									,'or_no'=>'string'
									,'or_date'=>'string'
									,'transaction_date'=>'string'
									,'accrual'=>'int'
									,'rollover_placement_date'=>'string'
									,'rollover_placement_days'=>'int'
									,'rollover_maturity_date'=>'string'
									,'rollover_interest_rate'=>'int'
									,'rollover_interest_amount'=>'int'
									,'processed'=>'string'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'
									),
						'm_investment_header'=>array('investment_no'=>'string'
									,'transaction_code'=>'string'
									,'supplier_id'=>'string'
									,'placement_date'=>'string'
									,'maturity_date'=>'string'
									,'placement_days'=>'int'
									,'interest_rate'=>'int'
									,'interest_amount'=>'int'
									,'investment_amount'=>'int'
									,'remarks'=>'string'
									,'action_code'=>'string'
									,'maturity_code'=>'string'
									,'principal_amount'=>'int'
									,'interest_income'=>'int'
									,'or_no'=>'string'
									,'or_date'=>'string'
									,'transaction_date'=>'string'
									,'accrual'=>'int'
									,'rollover_placement_date'=>'string'
									,'rollover_placement_days'=>'int'
									,'rollover_maturity_date'=>'string'
									,'rollover_interest_rate'=>'int'
									,'rollover_interest_amount'=>'int'
									,'processed'=>'string'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'
									),
						't_isop'=>array('transaction_no'=>'string'
									,'employee_id'=>'string'
									,'start_date'=>'string'
									,'end_date'=>'string'
									,'amount'=>'int'
									,'transaction_period'=>'string'
									,'transaction_type'=>'string'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'			
									),
						't_journal_detail'=>array('journal_no'=>'string'
									,'account_no'=>'string'
									,'debit_credit'=>'string'
									,'amount'=>'int'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'			
									),
						'm_journal_detail'=>array('journal_no'=>'string'
									,'account_no'=>'string'
									,'debit_credit'=>'string'
									,'amount'=>'int'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'			
									),
						'mc_journal_detail'=>array('journal_no'=>'string'
									,'account_no'=>'string'
									,'debit_credit'=>'string'
									,'amount'=>'int'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'			
									),
						't_journal_header'=>array('journal_no'=>'string'
									,'accounting_period'=>'string'
									,'transaction_code'=>'string'
									,'transaction_date'=>'string'
									,'particulars'=>'string'
									,'reference'=>'string'
									,'source'=>'string'
									,'document_no'=>'string'
									,'document_date'=>'string'
									,'remarks'=>'string'
									,'supplier_id'=>'string'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'		
									),
						'm_journal_header'=>array('journal_no'=>'string'
									,'accounting_period'=>'string'
									,'transaction_code'=>'string'
									,'transaction_date'=>'string'
									,'particulars'=>'string'
									,'reference'=>'string'
									,'source'=>'string'
									,'document_no'=>'string'
									,'document_date'=>'string'
									,'remarks'=>'string'
									,'supplier_id'=>'string'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'		
									),
					 	'mc_journal_header'=>array('journal_no'=>'string'
									,'accounting_period'=>'string'
									,'transaction_code'=>'string'
									,'transaction_date'=>'string'
									,'particulars'=>'string'
									,'reference'=>'string'
									,'source'=>'string'
									,'document_no'=>'string'
									,'document_date'=>'string'
									,'remarks'=>'string'
									,'supplier_id'=>'string'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'		
									),	
					   't_ledger'=>array('account_no'=>'string'
									,'accounting_period'=>'string'
									,'beginning_balance'=>'int'
									,'debits'=>'int'
									,'credits'=>'int'
									,'ending_balance'=>'int'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'			
									),
					  't_loan'=>array('loan_no'=>'string'
									,'restructure_no'=>'string'
									,'restructure_amount'=>'int'
									,'employee_id'=>'string'
									,'loan_code'=>'string'
									,'loan_date'=>'string'
									,'principal'=>'int'
									,'term'=>'int'
									,'interest_rate'=>'int'
									,'initial_interest'=>'int'
									,'employee_interest_total'=>'int'
									,'employee_interest_amortization'=>'int'
									,'employee_interest_vat_rate'=>'int'
									,'employee_interest_vat_amount'=>'int'
									,'company_interest_rate'=>'int'
									,'company_interest_total'=>'int'
									,'company_interest_amort'=>'int'
									,'amortization_startdate'=>'string'
									,'employee_principal_amort'=>'int'
									,'loan_proceeds'=>'int'
									,'estate_value'=>'int'
									,'principal_balance'=>'int'
									,'interest_balance'=>'int'
									,'cash_payments'=>'int'
									,'down_payment_percentage'=>'int'
									,'down_payment_amount'=>'int'
									,'mri_fip_amount'=>'int'
									,'broker_fee_amount'=>'int'
									,'government_fee_amount'=>'int'
									,'other_fee_amount'=>'int'
									,'service_fee_amount'=>'int'
									,'pension'=>'int'
									,'capital_contribution_balance'=>'int'
									,'bank_transfer'=>'string'
									,'insurance_broker'=>'string'
									,'appraiser_broker'=>'string'
									,'check_no'=>'string'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'		
									),
						'm_loan'=>array('loan_no'=>'string'
									,'restructure_no'=>'string'
									,'restructure_amount'=>'int'
									,'employee_id'=>'string'
									,'loan_code'=>'string'
									,'loan_date'=>'string'
									,'principal'=>'int'
									,'term'=>'int'
									,'interest_rate'=>'int'
									,'initial_interest'=>'int'
									,'employee_interest_total'=>'int'
									,'employee_interest_amortization'=>'int'
									,'employee_interest_vat_rate'=>'int'
									,'employee_interest_vat_amount'=>'int'
									,'company_interest_rate'=>'int'
									,'company_interest_total'=>'int'
									,'company_interest_amort'=>'int'
									,'amortization_startdate'=>'string'
									,'employee_principal_amort'=>'int'
									,'loan_proceeds'=>'int'
									,'estate_value'=>'int'
									,'principal_balance'=>'int'
									,'interest_balance'=>'int'
									,'cash_payments'=>'int'
									,'down_payment_percentage'=>'int'
									,'down_payment_amount'=>'int'
									,'mri_fip_amount'=>'int'
									,'broker_fee_amount'=>'int'
									,'government_fee_amount'=>'int'
									,'other_fee_amount'=>'int'
									,'service_fee_amount'=>'int'
									,'pension'=>'int'
									,'capital_contribution_balance'=>'int'
									,'bank_transfer'=>'string'
									,'insurance_broker'=>'string'
									,'appraiser_broker'=>'string'
									,'check_no'=>'string'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'		
									),
						
						't_loan_amortization'=>array('loan_no'=>'string'
									,'period'=>'string'
									,'employee_principal_amort'=>'int'
									,'employee_interest_amortization'=>'int'
									,'employee_company_amortization'=>'int'
									,'principal_balance'=>'int'
									,'interest_balance'=>'int'
									,'company_balance'=>'int'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'	
									),
					    't_loan_charges'=>array('loan_no'=>'string'
									,'transaction_code'=>'string'
									,'amount'=>'int'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'			
									),
						'm_loan_charges'=>array('loan_no'=>'string'
									,'transaction_code'=>'string'
									,'amount'=>'int'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'			
									),
						't_loan_guarantor'=>array('loan_no'=>'string'
									,'guarantor_id'=>'string'
									,'amortization_amount'=>'int'
									,'interest_amount'=>'int'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'				
									),
						'm_loan_guarantor'=>array('loan_no'=>'string'
									,'guarantor_id'=>'string'
									,'amortization_amount'=>'int'
									,'interest_amount'=>'int'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'				
									),
					    't_loan_payment'=>array('loan_no'=>'string'
									,'payment_date'=>'string'
									,'transaction_code'=>'string'
									,'payor_id'=>'string'
									,'or_no'=>'string'
									,'or_date'=>'string'
									,'amount'=>'int'
									,'interest_amount'=>'int'
									,'source'=>'string'
									,'remarks'=>'string'
									,'balance'=>'int'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'		
									),
						'm_loan_payment'=>array('loan_no'=>'string'
									,'payment_date'=>'string'
									,'transaction_code'=>'string'
									,'payor_id'=>'string'
									,'or_no'=>'string'
									,'or_date'=>'string'
									,'amount'=>'int'
									,'interest_amount'=>'int'
									,'source'=>'string'
									,'remarks'=>'string'
									,'balance'=>'int'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'		
									),
					    't_loan_payment_detail'=>array('loan_no'=>'string'
									,'payment_date'=>'string'
									,'transaction_code'=>'string'
									,'payor_id'=>'string'
									,'amount'=>'int'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'		
									),
						'm_loan_payment_detail'=>array('loan_no'=>'string'
									,'payment_date'=>'string'
									,'transaction_code'=>'string'
									,'payor_id'=>'string'
									,'amount'=>'int'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'		
									),
						't_payroll_deduction'=>array('employee_id'=>'string'
									,'transaction_code'=>'string'
									,'start_date'=>'string'
									,'end_date'=>'string'
									,'amount'=>'string'
									,'transaction_type'=>'string'
									,'transaction_period'=>'string'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'				
									),
						't_posting'=>array('accounting_period'=>'string'
									,'capital_contribution'=>'int'
									,'journal'=>'int'
									,'capital_contribution_user'=>'string'
									,'capital_contribution_date'=>'string'
									,'journal_user'=>'string'
									,'journal_date'=>'string'	
									),
						't_transaction'=>array('transaction_no'=>'string'
									,'transaction_date'=>'string'
									,'transaction_code'=>'string'
									,'employee_id'=>'string'
									,'transaction_amount'=>'int'
									,'status_flag' => 'string'
									,'source'=>'string'
									,'reference'=>'string'
									,'remarks'=>'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'			
									),
						'm_transaction'=>array('transaction_no'=>'string'
									,'transaction_date'=>'string'
									,'transaction_code'=>'string'
									,'employee_id'=>'string'
									,'transaction_amount'=>'int'
									,'status_flag' => 'string'
									,'source'=>'string'
									,'reference'=>'string'
									,'remarks'=>'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'			
									)
							
									);
   // FOR MIGRATION OF DATA VARIABLES END
   
   //FOR Archiving
   	var $archive_span = 5;
   	var $archives = array('h_amla_covered'=>array('transaction_no'=>'string'
									,'transaction_date'=>'string'
									,'employee_id'=>'string'
									,'transaction_code'=>'string'
									,'amla_code'=>'string'
									,'transaction_amount'=>'integer'
									,'report_date'=>'string'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'							
									),
						't_capital_contribution'=>array('employee_id'=>'string'
									,'accounting_period'=>'string'
									,'beginning_balance'=>'int'
									,'ending_balance'=>'int'
									,'minimum_balance'=>'int'
									,'maximum_balance'=>'int'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=> 'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'
									),
						'm_capital_transaction_detail'=>array('transaction_no'=>'string'
									,'transaction_code'=>'string'
									,'amount'=>'int'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'		
									),
						'm_capital_transaction_header'=>array('transaction_no'=>'string'
									,'transaction_date'=>'string'
									,'transaction_code'=>'string'
									,'employee_id'=>'string'
									,'transaction_amount'=>'string'
									,'or_no'=>'string'
									,'or_date'=>'string'
									,'remarks'=>'string'
									,'bank_transfer'=>'string'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'		
									),
					  	't_dividend'=>array('dividend_no'=>'string'
									,'accounting_period'=>'string'
									,'start_date'=>'string'
									,'end_date'=>'string'
									,'dividend_code'=>'string'
									,'dividend_rate'=>'int'
									,'with_tax_code'=>'string'
									,'with_tax_rate'=>'int'
									,'vat_code'=>'string'
									,'vat_rate'=>'int'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'			
									),
						'm_investment_detail'=>array('investment_no'=>'string'
									,'accounting_period'=>'string'
									,'amount'=>'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'status_flag' => 'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'
									),
						'm_investment_header'=>array('investment_no'=>'string'
									,'transaction_code'=>'string'
									,'supplier_id'=>'string'
									,'placement_date'=>'string'
									,'maturity_date'=>'string'
									,'placement_days'=>'int'
									,'interest_rate'=>'int'
									,'interest_amount'=>'int'
									,'investment_amount'=>'int'
									,'remarks'=>'string'
									,'action_code'=>'string'
									,'maturity_code'=>'string'
									,'principal_amount'=>'int'
									,'interest_income'=>'int'
									,'or_no'=>'string'
									,'or_date'=>'string'
									,'transaction_date'=>'string'
									,'accrual'=>'int'
									,'rollover_placement_date'=>'string'
									,'rollover_placement_days'=>'int'
									,'rollover_maturity_date'=>'string'
									,'rollover_interest_rate'=>'int'
									,'rollover_interest_amount'=>'int'
									,'processed'=>'string'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'
									),
						't_isop'=>array('transaction_no'=>'string'
									,'employee_id'=>'string'
									,'start_date'=>'string'
									,'end_date'=>'string'
									,'amount'=>'int'
									,'transaction_period'=>'string'
									,'transaction_type'=>'string'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'			
									),
						'm_journal_detail'=>array('journal_no'=>'string'
									,'account_no'=>'string'
									,'debit_credit'=>'string'
									,'amount'=>'int'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'			
									),
						'mc_journal_detail'=>array('journal_no'=>'string'
									,'account_no'=>'string'
									,'debit_credit'=>'string'
									,'amount'=>'int'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'			
									),
						'm_journal_header'=>array('journal_no'=>'string'
									,'accounting_period'=>'string'
									,'transaction_code'=>'string'
									,'transaction_date'=>'string'
									,'particulars'=>'string'
									,'reference'=>'string'
									,'source'=>'string'
									,'document_no'=>'string'
									,'document_date'=>'string'
									,'remarks'=>'string'
									,'supplier_id'=>'string'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'		
									),
					 	'mc_journal_header'=>array('journal_no'=>'string'
									,'accounting_period'=>'string'
									,'transaction_code'=>'string'
									,'transaction_date'=>'string'
									,'particulars'=>'string'
									,'reference'=>'string'
									,'source'=>'string'
									,'document_no'=>'string'
									,'document_date'=>'string'
									,'remarks'=>'string'
									,'supplier_id'=>'string'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'		
									),	
					   't_ledger'=>array('account_no'=>'string'
									,'accounting_period'=>'string'
									,'beginning_balance'=>'int'
									,'debits'=>'int'
									,'credits'=>'int'
									,'close_debits'=>'int'
									,'close_credits'=>'int'
									,'ending_balance'=>'int'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'			
									),
						'm_loan'=>array('loan_no'=>'string'
									,'restructure_no'=>'string'
									,'restructure_amount'=>'int'
									,'employee_id'=>'string'
									,'loan_code'=>'string'
									,'loan_date'=>'string'
									,'principal'=>'int'
									,'term'=>'int'
									,'interest_rate'=>'int'
									,'initial_interest'=>'int'
									,'employee_interest_total'=>'int'
									,'employee_interest_amortization'=>'int'
									,'employee_interest_vat_rate'=>'int'
									,'employee_interest_vat_amount'=>'int'
									,'company_interest_rate'=>'int'
									,'company_interest_total'=>'int'
									,'company_interest_amort'=>'int'
									,'amortization_startdate'=>'string'
									,'employee_principal_amort'=>'int'
									,'loan_proceeds'=>'int'
									,'estate_value'=>'int'
									,'principal_balance'=>'int'
									,'interest_balance'=>'int'
									,'cash_payments'=>'int'
									,'down_payment_percentage'=>'int'
									,'down_payment_amount'=>'int'
									,'mri_fip_amount'=>'int'
									,'broker_fee_amount'=>'int'
									,'government_fee_amount'=>'int'
									,'other_fee_amount'=>'int'
									,'service_fee_amount'=>'int'
									,'pension'=>'int'
									,'capital_contribution_balance'=>'int'
									,'bank_transfer'=>'string'
									,'insurance_broker'=>'string'
									,'appraiser_broker'=>'string'
									,'check_no'=>'string'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'
									,'close_flag'=>'string'				
									),
						
						't_loan_amortization'=>array('loan_no'=>'string'
									,'period'=>'string'
									,'employee_principal_amort'=>'int'
									,'employee_interest_amortization'=>'int'
									,'employee_company_amortization'=>'int'
									,'principal_balance'=>'int'
									,'interest_balance'=>'int'
									,'company_balance'=>'int'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'	
									),
						'm_loan_charges'=>array('loan_no'=>'string'
									,'transaction_code'=>'string'
									,'amount'=>'int'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'			
									),
						'm_loan_guarantor'=>array('loan_no'=>'string'
									,'guarantor_id'=>'string'
									,'amortization_amount'=>'int'
									,'interest_amount'=>'int'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'				
									),
						'm_loan_payment'=>array('loan_no'=>'string'
									,'payment_date'=>'string'
									,'transaction_code'=>'string'
									,'payor_id'=>'string'
									,'or_no'=>'string'
									,'or_date'=>'string'
									,'amount'=>'int'
									,'interest_amount'=>'int'
									,'source'=>'string'
									,'remarks'=>'string'
									,'balance'=>'int'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'		
									),
						'm_loan_payment_detail'=>array('loan_no'=>'string'
									,'payment_date'=>'string'
									,'transaction_code'=>'string'
									,'payor_id'=>'string'
									,'amount'=>'int'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'		
									),
						't_payroll_deduction'=>array('employee_id'=>'string'
									,'transaction_code'=>'string'
									,'start_date'=>'string'
									,'end_date'=>'string'
									,'amount'=>'string'
									,'transaction_type'=>'string'
									,'transaction_period'=>'string'
									,'status_flag' => 'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'				
									),
						't_posting'=>array('accounting_period'=>'string'
									,'capital_contribution'=>'int'
									,'journal'=>'int'
									,'capital_contribution_user'=>'string'
									,'capital_contribution_date'=>'string'
									,'journal_user'=>'string'
									,'journal_date'=>'string'	
									),
						'm_transaction'=>array('transaction_no'=>'string'
									,'transaction_date'=>'string'
									,'transaction_code'=>'string'
									,'employee_id'=>'string'
									,'transaction_amount'=>'int'
									,'status_flag' => 'string'
									,'source'=>'string'
									,'reference'=>'string'
									,'remarks'=>'string'
									,'created_by'=>'string'
									,'created_date'=>'string'
									,'modified_by'=>'string'
									,'modified_date'=>'string'			
									)
							
					);
   	//header/independent tables
   	var $tables_archive = array('t_capital_contribution' => 'accounting_period'
   								,'m_capital_transaction_header' => 'transaction_date'
   								,'mc_journal_header' => 'accounting_period'
   								,'t_dividend' => 'accounting_period'
   								,'m_investment_header' => 'transaction_date'
   								,'t_isop' => 'end_date'
   								,'m_journal_header' => 'accounting_period'
   								,'t_ledger' => 'accounting_period'
   								,'m_loan' => 'loan_date'
   								// ,'m_loan_payment' => 'payment_date'
   								,'t_payroll_deduction' => 'end_date'
   								,'t_posting' => 'accounting_period'
   								,'m_transaction' => 'transaction_date'
   								,'h_amla_covered' => 'transaction_date'
   								);
   	//detail tables with their corresponding header							
   	var $tables_archive2 = array( 'm_capital_transaction_detail' => array('header' => 'm_capital_transaction_header'
   															, 'pk' => array('transaction_no'))
   								, 'mc_journal_detail' => array('header' => 'mc_journal_header'
   															, 'pk' => array('journal_no'))
   								, 'm_investment_detail' => array('header' => 'm_investment_header'
   															, 'pk' => array('investment_no'))
   								, 'm_journal_detail' => array('header' => 'm_journal_header'
   															, 'pk' => array('journal_no'))
   								, 't_loan_amortization' => array('header' => 'm_loan'
   															, 'pk' => array('loan_no'))
   								, 'm_loan_charges' => array('header' => 'm_loan'
   															, 'pk' => array('loan_no'))
   								, 'm_loan_guarantor' => array('header' => 'm_loan'
   															, 'pk' => array('loan_no'))
								, 'm_loan_payment' => array('header' => 'm_loan'
   															, 'pk' => array('loan_no'))
   								, 'm_loan_payment_detail' => array('header' => 'm_loan'
   															, 'pk' => array('loan_no'))
   								);

	var $table_status = array('DELETED'=>'0'
								,'NEW'=>'1'
								,'PROCESSED'=>'2'
								,'POSTED'=>'3'
								,'MONTH_END'=>'4');
								
	var $lock_params = array('PECATRANNO', 'JOURNALNO', 'LASTDIVNO');
	var $batch_lock = "BATCH-PRO";
	var $lock_refresh = 2;
}
?>