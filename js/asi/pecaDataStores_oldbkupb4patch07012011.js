var employeeColumns =  new Ext.grid.ColumnModel( 
	[
		{id:'employee_id', header: 'Employee ID', width: 100, sortable: true, dataIndex: 'employee_id'}
		,{header: 'Last Name', width: 100, sortable: true, dataIndex: 'last_name'}
		,{header: 'First Name', width: 100, sortable: true, dataIndex: 'first_name'}
	]
);


var pecaReaders ={
	
	origValue: '>   ' //original value indicator
	//Transaction Code Reader	
	,transcodeReader: new Ext.data.JsonReader({
	    totalProperty: 'total',
	    root: 'data'
	    },[{name: 'transcode[transaction_code]', mapping: 'transaction_code', type: 'string'}
	    ,{name: 'transcode_formated', mapping: 'transaction_code', type: 'string', convert:function(value,rec){
			return Ext.util.Format.htmlEncode(value);
		}}
		,{name: 'transaction_code', mapping: 'transaction_code', type: 'string'}
	    ,{name: 'description_formated', mapping: 'transaction_description', type: 'string', convert:function(value,rec){
			return Ext.util.Format.htmlEncode(value);
		}}
	    ,{name: 'transcode[transaction_description]', mapping: 'transaction_description', type: 'string'}
	    ,{name: 'transaction_description', mapping: 'transaction_description', type: 'string'}
	    ,{name: 'transcode[gl_code]', mapping: 'gl_code', type: 'string'}
	    ,{name: 'transcode[transaction_group]', mapping: 'transaction_group', type: 'string'}
	    ,{name: 'transcode[transaction_group_name]', mapping: 'tg_name', type: 'string'}
		//hidden fields
	    ,{name: 'transcode[wage_type]', mapping: 'wage_type', type: 'string'}
	    ,{name: 'wage_type_formated', mapping: 'wage_type', type: 'string', convert:function(value,rec){
			return Ext.util.Format.htmlEncode(value);
		}}
	    ,{name: 'transcode[capcon_effect]', mapping: 'capcon_effect', type: 'string'}
	    ,{name: 'capcon_effect', mapping: 'ce_name', type: 'string'}
	    ,{name: 'transcode[with_or]', mapping: 'with_or', type: 'string', convert: function(value, rec){
	    	return formatCheckbox(value);}}
	    ,{name: 'transcode[bank_transfer]', mapping: 'bank_transfer', type: 'string', convert: function(value, rec){
		    return formatCheckbox(value);}}
	    ,{name: 'transcode[amla_code]', mapping: 'amla_code', type: 'string'}
	    ,{name: 'amla_code_formated', mapping: 'amla_code', type: 'string', convert:function(value,rec){
			return Ext.util.Format.htmlEncode(value);
		}}
	    ,{name: 'transcode[capcon_req]', mapping: 'capcon_req', type: 'string', convert: function(value, rec){
	    	return formatCheckbox(value);}}
	    ,{name: 'transcode[created_date]', mapping: 'created_date', type: 'string', convert: function(value, rec){
	    	return formatDateTime(value);}
	    }
		,{name: 'transcode_description_formatted', mapping: 'transaction_description', type: 'string', convert:function(value,rec){
			return Ext.util.Format.htmlEncode(value);
		}}
    ])
	//Company Reader
	,companyReader: new Ext.data.JsonReader({
	    totalProperty: 'total'
	    ,root: 'data'
		},[
		    {name: 'company[company_code]', mapping: 'company_code', type: 'string'}
		    ,{name: 'company_code', mapping: 'company_code', type: 'string'}
		    ,{name: 'company_name', mapping: 'company_name', type: 'string'}
		    ,{name: 'company[company_name]', mapping: 'company_name', type: 'string'}
			//,{name: 'company_name', mapping: 'company_name', type: 'string'}
			,{name: 'company_code_formatted', mapping: 'company_code', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}
			,{name: 'company_name_formatted', mapping: 'company_name', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}
	    ]
	)
	//Form Image Reader
	,formImageReader: new Ext.data.JsonReader({
	    totalProperty: 'total'
	    ,root: 'data'
		},[
		    {name: 'name', mapping: 'name', type: 'string'}
		    ,{name: 'url', mapping: 'path', type: 'string'}
		    ,{name: 'lastmod', mapping: 'size', type: 'string'}
	    ]
	)
	
	//Chart of Accounts Reader
	,coaReader: new Ext.data.JsonReader({
	    totalProperty: 'total'
	    ,root: 'data'
		},[
		    {name: 'coa[account_no]', mapping: 'account_no', type: 'string'}
		    ,{name: 'coa[account_name]', mapping: 'account_name', type: 'string'}
			,{name: 'account_name_formated', mapping: 'account_name', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}
		    ,{name: 'account_no', mapping: 'account_no', type: 'string'}
		    ,{name: 'account_name', mapping: 'account_name', type: 'string'}
		    ,{name: 'account_no_name', mapping: 'account_no', type: 'string', convert: function(value, rec){
		    	return value + ' - ' + rec.account_name;}}
		    ,{name: 'coa[account_group]', mapping: 'account_group', type: 'string'}
		    ,{name: 'coa[accntGrp_name]', mapping: 'accntGrp_name', type: 'string'}
			,{name: 'accntGrp_name_formated', mapping: 'accntGrp_name', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}
		    ,{name: 'coa[effectivity_date]', mapping: 'effectivity_date', type: 'string'}
			,{name: 'account_no_formatted', mapping: 'account_no', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}
			,{name: 'account_name_formatted', mapping: 'account_name', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}
			,{name: 'accntGrp_name_formatted', mapping: 'accntGrp_name', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}
	    ]
	)
	//Chart of Accounts Reader for GL Entry
	,coaGlReader: new Ext.data.JsonReader({
	    totalProperty: 'total'
	    ,root: 'data'
		},[
		    {name: 'coa[account_no]', mapping: 'account_no', type: 'string'}
		    ,{name: 'coa[account_name]', mapping: 'account_name', type: 'string'}
		    ,{name: 'account_no', mapping: 'account_no', type: 'string'}
		    ,{name: 'account_name', mapping: 'account_name', type: 'string'}
		    ,{name: 'account_no_name', mapping: 'account_no', type: 'string', convert: function(value, rec){
		    	return value + ' - ' + rec.account_name;}}
		    ,{name: 'coa[account_group]', mapping: 'account_group', type: 'string'}
		    ,{name: 'coa[accntGrp_name]', mapping: 'accntGrp_name', type: 'string'}
		    ,{name: 'coa[effectivity_date]', mapping: 'effectivity_date', type: 'string'}
			,{name: 'account_no_name_formatted', mapping: 'account_no', type: 'string', convert: function(value, rec){
				formatted = Ext.util.Format.htmlEncode(value) + ' - ' + Ext.util.Format.htmlEncode(rec.account_name);
		    	return formatted;}}
	    ]
	)
	//Information Code Reader
	,infocodeReader: new Ext.data.JsonReader({
	    totalProperty: 'total'
	    ,root: 'data'
		},[
		    {name: 'infocode[information_code]', mapping: 'information_code', type: 'string'}
		    ,{name: 'information_code', mapping: 'information_code', type: 'string'}
		    ,{name: 'infocode[information_description]', mapping: 'information_description', type: 'string'}
			,{name: 'information_description', mapping: 'information_description', type: 'string'}
			,{name: 'information_code_formatted', mapping: 'information_code', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}
			,{name: 'information_description_formatted', mapping: 'information_description', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}
	    ]
	)
	//System Parameters Reader
	,sysparamsReader: new Ext.data.JsonReader({
		totalProperty: 'total'
		,root: 'data'
		},[
		    {name: 'sysparam[parameter_id]', mapping: 'parameter_id', type: 'string'}
		    ,{name: 'parameter_id', mapping: 'parameter_id', type: 'string'}
		    ,{name: 'sysparam[parameter_name]', mapping: 'parameter_name', type: 'string'}
		    ,{name: 'sysparam[parameter_value]', mapping: 'parameter_value', type: 'string'}
			,{name: 'parameter_id_formatted', mapping: 'parameter_id', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}
			,{name: 'parameter_name_formatted', mapping: 'parameter_name', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}
			,{name: 'parameter_value_formatted', mapping: 'parameter_value', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}
	    ]
	)
	//User  Reader
	,userReader: new Ext.data.JsonReader({
	    totalProperty: 'total'
	    ,root: 'data'
		},[
		    {name: 'user[user_id]', mapping: 'user_id', type: 'string'}
		    ,{name: 'user[user_name]', mapping: 'user_name', type: 'string'}
		    ,{name: 'user_id_formatted', mapping: 'user_id', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}
		    ,{name: 'user_name_formatted', mapping: 'user_name', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}
		    ,{name: 'user[group_id]', mapping: 'group_id', type: 'string'}
		    ,{name: 'group_id_formatted', mapping: 'group_id', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}
		    ,{name: 'user[email_address]', mapping: 'email_address', type: 'string'}
		    ,{name: 'user[password]', mapping: 'password', type: 'string'}
		    ,{name: 'user[permission]', mapping: 'permission', type: 'string'}
	    ]
	)
		//User  Reader for Permission
	,userPermReader: new Ext.data.JsonReader({
	    totalProperty: 'total'
	    ,root: 'data'
		},[
		    {name: 'user[user_id]', mapping: 'user_id', type: 'string'}
		    ,{name: 'user[user_name]', mapping: 'user_name', type: 'string'}
		    ,{name: 'user_id_formatted', mapping: 'user_id', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}
		    ,{name: 'user_name_formatted', mapping: 'user_name', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}
		    ,{name: 'user[group_id]', mapping: 'group_id', type: 'string'}
		    ,{name: 'group_id_formatted', mapping: 'group_id', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}
		    ,{name: 'user[email_address]', mapping: 'email_address', type: 'string'}
		    ,{name: 'user[password]', mapping: 'password', type: 'string'}
		    ,{name: 'user[permission]', mapping: 'permission', type: 'string'}
	    ]
	)
	,groupReader: new Ext.data.JsonReader({
	    totalProperty: 'total'
	    ,root: 'data'
		},[
		    ,{name: 'group[group_id]', mapping: 'group_id', type: 'string'}
		    ,{name: 'group[group_name]', mapping: 'group_name', type: 'string'}
		    ,{name: 'group_id_formatted', mapping: 'group_id', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}
		    ,{name: 'group_name_formatted', mapping: 'group_name', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}
		    ,{name: 'group[permission]', mapping: 'permission', type: 'string'}
		    ,{name: 'group[status_flag]', mapping: 'status_flag', type: 'string'}
	    ]
	)
	,groupPermReader: new Ext.data.JsonReader({
	    totalProperty: 'total'
	    ,root: 'data'
		},[
		    ,{name: 'group[group_id]', mapping: 'group_id', type: 'string'}
		    ,{name: 'group[group_name]', mapping: 'group_name', type: 'string'}
		    ,{name: 'group_id_formatted', mapping: 'group_id', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}
		    ,{name: 'group_name_formatted', mapping: 'group_name', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}
		    ,{name: 'group[permission]', mapping: 'permission', type: 'string'}
		    ,{name: 'group[status_flag]', mapping: 'status_flag', type: 'string'}
	    ]
	)
	//Permission Reader
	,permissionsReader: new Ext.data.JsonReader({
	    totalProperty: 'total'
	    ,root: 'data'
		},[
		    {name: 'user[user_id]', mapping: 'user_id', type: 'string'}
		    ,{name: 'user[permission]', mapping: 'permission', type: 'string'}
	    ]
	)
	//Bulletin Reader
	,bulletinReader: new Ext.data.JsonReader({
	    totalProperty: 'total'
	    ,root: 'data'
		},[
		    {name: 'bulletin[topic_id]', mapping: 'topic_id', type: 'string'}
		    ,{name: 'bulletin[end_date]', mapping: 'end_date', type: 'date', convert: function(value, rec){
		    	return formatDateYYYYMMDD(value);}}
		    ,{name: 'bulletin[published_date]', mapping: 'published_date', type: 'date', convert: function(value, rec){
		    	return formatDateYYYYMMDD(value);}}
		    ,{name: 'bulletin[subject]', mapping: 'subject', type: 'string'}
		    ,{name: 'bulletin[content]', mapping: 'content', type: 'string'}
			,{name: 'bulletin[sticky]', mapping: 'sticky', type: 'string', convert: function(value, rec){
				return formatCheckbox(value);}}
	    ]
	)
	//Transaction Charges Reader
	,transchargeReader: new Ext.data.JsonReader({
		totalProperty: 'total'
		,root: 'data'
		},[
		    {name: 'transcharge[transaction_code]', mapping: 'transaction_code', type: 'string'}
		    ,{name: 'transaction_code', mapping: 'transaction_code', type: 'string'}
		    ,{name: 'transcharge[transaction_description]', mapping: 'transaction_description', type: 'string'}
		    ,{name: 'transaction_description_formatted', mapping: 'transaction_description', type: 'string', convert: function(value, rec){
		    	return Ext.util.Format.htmlEncode(value);
		    }}
		    ,{name: 'transcharge[charge_code]', mapping: 'charge_code', type: 'string'}
		    ,{name: 'charge_code', mapping: 'charge_code', type: 'string'}
		    ,{name: 'charge_description', mapping: 'charge_description', type: 'string', convert: function(value, rec){
		    	return rec.charge_code + ' - ' + value;}}
		    ,{name: 'transcharge[charge_description]', mapping: 'charge_description', type: 'string'}
		    ,{name: 'transcharge[charge_formula]', mapping: 'charge_formula', type: 'string'}
			,{name: 'charge_code_formatted', mapping: 'charge_code', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}
			,{name: 'charge_description_formatted', mapping: 'charge_description', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}
			,{name: 'charge_formula_formatted', mapping: 'charge_formula', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}
	    ]
	)
	//GL Entries Header Reader
	,glHdrReader: new Ext.data.JsonReader({
		totalProperty: 'total'
		,successProperty: 'success'
	    ,idProperty: 'gl_code'
		,root: 'data'
		},[
		    {name: 'glHdr[gl_code]', mapping: 'gl_code', type: 'string'}
		    ,{name: 'glHdr[gl_description]', mapping: 'gl_description', type: 'string'}
		    ,{name: 'gl_code', mapping: 'gl_code', type: 'string'}
		    ,{name: 'gl_description', mapping: 'gl_description', type: 'string', convert: function(value, rec){
		    	return value + ' - ' + rec.gl_code;}}
		    ,{name: 'glHdr[particulars]', mapping: 'particulars', type: 'string'}
			,{name: 'gl_code_formatted', mapping: 'gl_code', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}
			,{name: 'gl_description_formatted', mapping: 'gl_description', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}
			,{name: 'particulars_formatted', mapping: 'particulars', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}
	    ]
	)
	//GL Entries Header Reader
	,glTransHdrReader: new Ext.data.JsonReader({
		totalProperty: 'total'
		,successProperty: 'success'
	    ,idProperty: 'gl_code'
		,root: 'data'
		},[
		    {name: 'glHdr[gl_code]', mapping: 'gl_code', type: 'string'}
		    ,{name: 'glHdr[gl_description]', mapping: 'gl_description', type: 'string'}
		    ,{name: 'gl_code', mapping: 'gl_code', type: 'string'}
		    ,{name: 'gl_description', mapping: 'gl_description', type: 'string', convert: function(value, rec){
		    	return value + ' - ' + rec.gl_code;}}
		    ,{name: 'glHdr[particulars]', mapping: 'particulars', type: 'string'}
	    ]
	)
	//GL Entries Detail Reader
	,glDtlReader: new Ext.data.JsonReader({
		totalProperty: 'total'
		,successProperty: 'success'
	    ,idProperty: 'id'	
		,root: 'data'
		},[
		   	{name: 'id', mapping: 'id', type: 'string'}
		    ,{name: 'glDtl[gl_code]', mapping: 'gl_code', type: 'string'}
		    ,{name: 'glDtl[account_no]', mapping: 'account_no', type: 'string'}
		    ,{name: 'debit_credit', mapping: 'debit_credit', type: 'string'}
		    ,{name: 'glDtl[field_name]', mapping: 'field_name', type: 'string'}
		    ,{name: 'account_name', mapping: 'account_name', type: 'string'}
			,{name: 'gldtlaccount_no_formatted', mapping: 'account_no', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}	
			,{name: 'gldtlaccount_name_formatted', mapping: 'account_name', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}		
	    ]
	)
	//Loan Code Header Reader
	,loancodeHdrReader: new Ext.data.JsonReader({
		totalProperty: 'total'
		,successProperty: 'success'	
		,root: 'data'
		},[
		    {name: 'loancodeHdr[loan_code]', mapping: 'loan_code', type: 'string'}
		    ,{name: 'loan_code', mapping: 'loan_code', type: 'string'}
		    ,{name: 'loancodeHdr[loan_description]', mapping: 'loan_description', type: 'string'}
		    ,{name: 'loan_description', mapping: 'loan_description', type: 'string', convert: function(value, rec){
		    	return rec.loan_code + ' - ' + value;}}
		    ,{name: 'loancodeHdr[priority]', mapping: 'priority', type: 'int'}
		    ,{name: 'loancodeHdr[min_emp_months]', mapping: 'min_emp_months', type: 'int'}
		    ,{name: 'loancodeHdr[max_loan_amount]', mapping: 'max_loan_amount', type: 'float'}
		    ,{name: 'loancodeHdr[min_term]', mapping: 'min_term', type: 'int'}
		    ,{name: 'loancodeHdr[max_term]', mapping: 'max_term', type: 'int'}
		    ,{name: 'loancodeHdr[downpayment_pct]', mapping: 'downpayment_pct', type: 'float', convert: function(value, rec){
		    	return Ext.util.Format.number(value,'0.00');}}
		    ,{name: 'loancodeHdr[restructure]', mapping: 'restructure', type: 'string', convert: function(value, rec){
		    	return formatCheckbox(value);}}
		    ,{name: 'loancodeHdr[emp_interest_pct]', mapping: 'emp_interest_pct', type: 'float', convert: function(value, rec){
		    	return Ext.util.Format.number(value,'0.00');}}
		    ,{name: 'loancodeHdr[comp_share_pct]', mapping: 'comp_share_pct', type: 'float', convert: function(value, rec){
		    	return Ext.util.Format.number(value,'0.00');}}
		    ,{name: 'loancodeHdr[payroll_deduction]', mapping: 'payroll_deduction', type: 'string', convert: function(value, rec){
		    	return formatCheckbox(value);}}
		    ,{name: 'loancodeHdr[unearned_interest]', mapping: 'unearned_interest', type: 'string', convert: function(value, rec){
		    	return formatCheckbox(value);}}
		    ,{name: 'loancodeHdr[interest_earned]', mapping: 'interest_earned', type: 'string', convert: function(value, rec){
		    	return formatCheckbox(value);}}
		    ,{name: 'loancodeHdr[payment_code]', mapping: 'payment_code', type: 'string'}
		    ,{name: 'payment_description', mapping: 'payment_description', type: 'string'}
		    ,{name: 'loancodeHdr[transaction_code]', mapping: 'transaction_code', type: 'string'}
		    ,{name: 'transaction_description', mapping: 'transaction_description', type: 'string'}
		    ,{name: 'loancodeHdr[take_home_pay]', mapping: 'take_home_pay', type: 'float', convert: function(value, rec){
		    	 value = (value == null || value == "" ? "0.00": value);
				 value = Ext.util.Format.number(value,'0.00');
				 return value;}}
		    ,{name: 'loancodeHdr[submit_payslip]', mapping: 'submit_payslip', type: 'string', convert: function(value, rec){
		    	return formatCheckbox(value);}}
		    ,{name: 'loancodeHdr[post_dated_checks]', mapping: 'post_dated_checks', type: 'string', convert: function(value, rec){
		    	return formatCheckbox(value);}}
		    ,{name: 'loancodeHdr[bsp_sbl]', mapping: 'bsp_sbl', type: 'string', convert: function(value, rec){
		    	return formatCheckbox(value);}}
		    ,{name: 'loancodeHdr[pension_plan_slip]', mapping: 'pension_plan_slip', type: 'string'}
		    ,{name: 'loancodeHdr[avail_after_full_payment]', mapping: 'avail_after_full_payment', type: 'string', convert: function(value, rec){
		    	return formatCheckbox(value);}}
			,{name: 'loan_code_formatted', mapping: 'loan_code', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}	
			,{name: 'loan_description_formatted', mapping: 'loan_description', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}
			,{name: 'loancodeHdr[transcode_formatted]', mapping: 'transaction_code', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}
	    ]
	)
	//GL Entries Detail Reader
	,loancodeDtlReader: new Ext.data.JsonReader({
		totalProperty: 'total'
		,successProperty: 'success'
	    ,idProperty: 'id'	
		,root: 'data'
		},[
		   	{name: 'id', mapping: 'id', type: 'string'}
		    ,{name: 'loancodeDtl[loan_code]', mapping: 'loan_code', type: 'string'}
		    ,{name: 'loancodeDtl[years_of_service]', mapping: 'years_of_service', type: 'string', convert: function(value, rec){
		    	return Ext.util.Format.number(value,'0.0');}}
		    ,{name: 'loancodeDtl[capital_contribution]', mapping: 'capital_contribution', type: 'string', convert: function(value, rec){
		    	return formatCheckboxCapcon(value);}}
		    ,{name: 'loancodeDtl[pension]', mapping: 'pension', type: 'string', convert: function(value, rec){
		    	return formatCheckbox(value);}}
		    ,{name: 'loancodeDtl[guarantor]', mapping: 'guarantor', type: 'string'}	
	    ]
	)
	//GL Entries Detail Reader
	,loancodePCReader: new Ext.data.JsonReader({
		totalProperty: 'total'
		,successProperty: 'success'
	    ,idProperty: 'id'	
		,root: 'data'
		},[
		   	{name: 'id', mapping: 'id', type: 'string'}
		    ,{name: 'loan_code', mapping: 'loan_code', type: 'string'}
		    ,{name: 'transaction_code', mapping: 'transaction_code', type: 'string'}
		    ,{name: 'transaction_description', mapping: 'transaction_description', type: 'string'}
	    ]
	)
	//Capital Contribution Reader
	,capconReader: new Ext.data.JsonReader({
	    totalProperty: 'total',
	    root: 'data'
	    },[
	       	{name: 'capcon[transaction_no]', mapping: 'transaction_no', type: 'string'}
	       	,{name: 'transaction_no', mapping: 'transaction_no', type: 'string'}
		    ,{name: 'capcon[employee_id]', mapping: 'employee_id', type: 'string'}
		    ,{name: 'last_name', mapping: 'last_name', type: 'string'}
		    ,{name: 'first_name', mapping: 'first_name', type: 'string'}
		    ,{name: 'capcon[transaction_amount]', mapping: 'transaction_amount', type: 'float', renderer:function(value,rec){
				return Ext.util.Format.number(value,'0,000.00');}}
		    ,{name: 'capcon[transaction_date]', mapping: 'transaction_date', type: 'date', convert: function(value, rec){
		    	return formatDate(value);}
		    }
		    ,{name: 'capcon[transaction_code]', mapping: 'transaction_code', type: 'string'}
		    ,{name: 'capcon[transaction_type]', mapping: 'transaction_type', type: 'string'}
		    ,{name: 'capcon[or_no]', mapping: 'or_no', type: 'string'}
		    ,{name: 'capcon[or_date]', mapping: 'or_date', type: 'string', convert: function(value, rec){
		    	return formatDate(value);}
		    }
		    ,{name: 'capcon[remarks]', mapping: 'remarks', type: 'string'}
		    ,{name: 'capcon[bank_transfer]', mapping: 'bank_transfer', type: 'string', convert: function(value, rec){
		    	return formatCheckbox(value);}}
	])
	//Employee List Reader
	,employeeReader: new Ext.data.JsonReader({
	    totalProperty: 'total'
	    ,root: 'data' 	
	    },[
		    {name: 'employee_id', mapping: 'employee_id', type: 'string'}
		    ,{name: 'last_name', mapping: 'last_name', type: 'string'}
		    ,{name: 'first_name', mapping: 'first_name', type: 'string'}
		    ,{name: 'middle_name', mapping: 'middle_name', type: 'string'}
	])
	
	//Capital Contribution Detail Reader
	,capconDtlReader: new Ext.data.JsonReader({
	    totalProperty: 'total'
    	,idProperty: 'id'	
	    ,root: 'data'
	    },[
		    {name: 'id', mapping: 'id', type: 'string'}
		    ,{name: 'transaction_no', mapping: 'transaction_no', type: 'string'}
		    ,{name: 'transaction_code', mapping: 'transaction_code', type: 'string'}
		    ,{name: 'transaction_description', mapping: 'transaction_description', type: 'string'}
		    ,{name: 'amount', mapping: 'amount', type: 'float'}
	])
	//Employee List Reader
	,newloanReader: new Ext.data.JsonReader({
	    totalProperty: 'total'	
	    ,root: 'data'
	    },[
		    {name: 'newloan[loan_no]', mapping: 'loan_no', type: 'float'}
		    ,{name: 'loan_no', mapping: 'loan_no', type: 'float'}
		    ,{name: 'newloan[employee_id]', mapping: 'employee_id', type: 'string'}
		    ,{name: 'last_name', mapping: 'last_name', type: 'string'}
		    ,{name: 'first_name', mapping: 'first_name', type: 'string'}
		    ,{name: 'newloan[loan_date]', mapping: 'loan_date', type: 'date', convert:function(value,rec){
				return formatDate(value);}}
		    ,{name: 'newloan[loan_description]', mapping: 'loan_description', type: 'string'}
		    ,{name: 'newloan[restructure_no]', mapping: 'restructure_no', type: 'string'}
		    ,{name: 'newloan[restructure_amount]', mapping: 'restructure_amount', type: 'string'}
		    ,{name: 'newloan[loan_code]', mapping: 'loan_code', type: 'string'}
		    //,{name: 'newloan[loan_date]', mapping: 'loan_date', type: 'string'}
		    ,{name: 'newloan[principal]', mapping: 'principal', type: 'string'}
		    ,{name: 'newloan[term]', mapping: 'term', type: 'string'}
		    ,{name: 'newloan[interest_rate]', mapping: 'interest_rate', type: 'string'}
		    ,{name: 'newloan[initial_interest]', mapping: 'initial_interest', type: 'string'}
		    ,{name: 'newloan[employee_interest_total]', mapping: 'employee_interest_total', type: 'string'}
		    ,{name: 'newloan[employee_interest_amortization]', mapping: 'employee_interest_amortization', type: 'string'}
		    ,{name: 'newloan[employee_interest_vat_rate]', mapping: 'employee_interest_vat_rate', type: 'string'}
		    ,{name: 'newloan[employee_interest_vat_amount]', mapping: 'employee_interest_vat_amount', type: 'string'}
		    ,{name: 'newloan[company_interest_rate]', mapping: 'company_interest_rate', type: 'string'}
		    ,{name: 'newloan[company_interest_total]', mapping: 'company_interest_total', type: 'string'}
		    ,{name: 'newloan[company_interest_amort]', mapping: 'company_interest_amort', type: 'string'}
		    ,{name: 'newloan[amortization_startdate]', mapping: 'amortization_startdate', type: 'string'}
		    ,{name: 'newloan[employee_principal_amort]', mapping: 'employee_principal_amort', type: 'string'}
		    ,{name: 'newloan[down_payment_percentage]', mapping: 'down_payment_percentage', type: 'string'}
		    ,{name: 'newloan[down_payment_amount]', mapping: 'down_payment_amount', type: 'string'}
		    ,{name: 'newloan[loan_proceeds]', mapping: 'loan_proceeds', type: 'string'}
		    ,{name: 'newloan[estate_value]', mapping: 'estate_value', type: 'string'}
		    ,{name: 'newloan[mri_fip_amount]', mapping: 'mri_fip_amount', type: 'string'}
		    ,{name: 'newloan[broker_fee_amount]', mapping: 'broker_fee_amount', type: 'string'}
		    ,{name: 'newloan[government_fee_amount]', mapping: 'government_fee_amount', type: 'string'}
		    ,{name: 'newloan[other_fee_amount]', mapping: 'other_fee_amount', type: 'string'}
		    ,{name: 'newloan[service_fee_amount]', mapping: 'service_fee_amount', type: 'string'}
		    ,{name: 'newloan[pension]', mapping: 'pension', type: 'string'}
		    ,{name: 'newloan[bank_transfer]', mapping: 'bank_transfer', type: 'string', convert: function(value, rec){
			    return formatCheckbox(value);}}
		    ,{name: 'newloan[principal_balance]', mapping: 'principal_balance', type: 'string'}
		    ,{name: 'newloan[interest_balance]', mapping: 'interest_balance', type: 'string'}
		    ,{name: 'newloan[cash_payments]', mapping: 'cash_payments', type: 'string'}
		    ,{name: 'newloan[capital_contribution_balance]', mapping: 'capital_contribution_balance', type: 'string'}
		    ,{name: 'newloan[insurance_broker]', mapping: 'insurance_broker', type: 'string'}
		    ,{name: 'newloan[appraiser_broker]', mapping: 'appraiser_broker', type: 'string'}
		    ,{name: 'newloan[check_no]', mapping: 'check_no', type: 'string'} 
			,{name: 'newloan[pension_cb]', mapping: 'pension_cb', type: 'string', convert: function(value, rec){
			    return formatCheckbox(value);}}
			,{name: 'newloan[comaker_cb]', mapping: 'comaker_cb', type: 'string', convert: function(value, rec){
			    return formatCheckbox(value);}}	
	])
	//Payroll Deduction Reader
	,pdReader: new Ext.data.JsonReader({
		totalProperty: 'total'
		,successProperty: 'success'
	    ,idProperty: 'id'	
		,root: 'data'
		},[
		    {name: 'newpayroll[employee_id]', mapping: 'employee_id', type: 'string'}
		    ,{name: 'employee_id', mapping: 'employee_id', type: 'string'}
			,{name: 'newpayroll[last_name]', mapping: 'last_name', type: 'string'}
		    ,{name: 'last_name', mapping: 'last_name', type: 'string'}
			,{name: 'newpayroll[first_name]', mapping: 'first_name', type: 'string'}
		    ,{name: 'first_name', mapping: 'first_name', type: 'string'}
		    ,{name: 'middle_name', mapping: 'middle_name', type: 'string'}
			,{name: 'start_date', mapping: 'start_date', type: 'string'}
		    ,{name: 'newpayroll[start_date]', mapping: 'start_date', type: 'date', convert:function(value,rec){
				return formatDate(value);
			}}
		    ,{name: 'newpayroll[end_date]', mapping: 'end_date', type: 'date', convert:function(value,rec){
				return formatDate(value);
			}}
			,{name: 'newpayroll[amount]', mapping: 'amount', type: 'float'}
			//,{name: 'newpayroll[transaction_code]', mapping: 'transaction_code', type: 'string'}
			,{name: 'transaction_code', mapping: 'transaction_code', type: 'string'}
			,{name: 'newpayroll[transaction_code]', mapping: 'transaction_code', type: 'string'}
	    ]
	)
	//Transaction Type Reader
	,ttypeReader: new Ext.data.JsonReader({
		totalProperty: 'total'
		,successProperty: 'success'
	    ,idProperty: 'id'
		,root: 'data'
		},[
		    {name: 'transcode[transaction_code]', mapping: 'transaction_code', type: 'string'}
			,{name: 'transaction_code', mapping: 'transaction_code', type: 'string'}
			,{name: 'transcode[transaction_description]', mapping: 'transaction_description', type: 'string'}
			,{name: 'transaction_description', mapping: 'transaction_description', type: 'string'}
			,{name: 'transcode[gl_code]', mapping: 'gl_code', type: 'string'}
			,{name: 'transcode[transaction_group]', mapping: 'transaction_group', type: 'string'}
			,{name: 'transcode[transaction_group_name]', mapping: 'tg_name', type: 'string'}
			,{name: 'newpayroll[transaction_type]', mapping: 'transaction_code', type: 'string'}
	    ]
	)
	//ISOP Reader
	,isopReader: new Ext.data.JsonReader({
		totalProperty: 'total'
		,successProperty: 'success'
		,root: 'data'
		},[	 {name: 'isop[transaction_no]', mapping: 'transaction_no', type: 'string'}
		    ,{name: 'transaction_no', mapping: 'transaction_no', type: 'string'}
		    ,{name: 'isop[employee_id]', mapping: 'employee_id', type: 'string'}
		    ,{name: 'isop[last_name]', mapping: 'last_name', type: 'string'}
		    ,{name: 'isop[first_name]', mapping: 'first_name', type: 'string'}
		    ,{name: 'isop[start_date]', mapping: 'start_date', type: 'date', convert: function(value, rec){
		    	return formatDate(value);}}
		    ,{name: 'isop[end_date]', mapping: 'end_date', type: 'date', convert: function(value, rec){
		    	return formatDate(value);}}
			,{name: 'isop[old_start_date]', mapping: 'start_date', type: 'date', convert: function(value, rec){
		    	return formatDate(value);}}
		    ,{name: 'isop[old_end_date]', mapping: 'end_date', type: 'date', convert: function(value, rec){
		    	return formatDate(value);}}
		    ,{name: 'isop[amount]', mapping: 'amount', type: 'float'/*, convert: function(value, rec){
		    	return Ext.util.Format.number(value,'0,000,000,000.00');}*/}		    
	    ]
	)

	// Loan Payment Header Reader
	,lpHdrReader: new Ext.data.JsonReader({
		totalProperty: 'total'
		,successProperty: 'success'
	    ,idProperty: 'id'	
		,root: 'data'
		},[
		    {name: 'lp[loan_no]', mapping: 'loan_no', type: 'string'}
		    ,{name: 'loan_no', mapping: 'loan_no', type: 'string'}
		    ,{name: 'employee_id', mapping: 'employee_id', type: 'string'}
		    ,{name: 'last_name', mapping: 'employee_last_name', type: 'string'}
		    ,{name: 'first_name', mapping: 'employee_first_name', type: 'string'}
			,{name: 'lp[or_no]', mapping: 'or_no', type: 'string'}
			,{name: 'lp[or_date]', mapping: 'or_date', type: 'string', convert: function(value, rec){
		    	return formatDate(value);}}
			,{name: 'p_last_name', mapping: 'last_name', type: 'string'}
		    ,{name: 'p_first_name', mapping: 'first_name', type: 'string'}
		    ,{name: 'company_code', mapping: 'company_code', type: 'string'}
		    ,{name: 'lp[transaction_code]', mapping: 'transaction_code', type: 'string'}
		    ,{name: 'transaction_code', mapping: 'transaction_code', type: 'string'}
			,{name: 'lp[loan_code]', mapping: 'loan_code', type: 'string'}
			,{name: 'loan_code', mapping: 'loan_code', type: 'string'}
		    ,{name: 'lp[payor_id]', mapping: 'payor_id', type: 'string'}
		    ,{name: 'payor_code', mapping: 'employee_id', type: 'string'}
		    ,{name: 'payment_date', mapping: 'payment_date', type: 'string'}
			,{name: 'lp[or_no]', mapping: 'or_no', type: 'string'}
			,{name: 'lp[or_date]', mapping: 'or_date', type: 'string', convert: function(value, rec){
		    	return formatDate(value);}}
		    ,{name: 'loan_description', mapping: 'loan_description', type: 'string'}
		    ,{name: 'lp[balance]', mapping: 'loan_balance', type: 'string'}
		    ,{name: 'lp[interest_amount]', mapping: 'interest_amount', type: 'string'}
		    ,{name: 'lp[amount]', mapping: 'principal_amount', type: 'string'}
		    ,{name: 'lp[payment_date]', mapping: 'payment_date', type: 'date', convert: function(value, rec){
		    	return formatDate(value);}}
		    ,{name: 'lp[remarks]', mapping: 'remarks', type: 'string'}
		    
	    ]
	)

	//Loan Payment Detail Reader
	,lpReader: new Ext.data.JsonReader({
		totalProperty: 'total'
		,successProperty: 'success'
	    ,idProperty: 'id'	
		,root: 'data'
		},[
		    {name: 'lp[loan_no]', mapping: 'loan_no', type: 'string'}
		    ,{name: 'loan_no', mapping: 'loan_no', type: 'string'}
		    ,{name: 'lp[employee_id]', mapping: 'employee_id', type: 'string'}
		    ,{name: 'employee_id', mapping: 'employee_id', type: 'string'}
			,{name: 'last_name', mapping: 'last_name', type: 'string'}
		    ,{name: 'first_name', mapping: 'first_name', type: 'string'}
		    ,{name: 'lp[loan_description]', mapping: 'loan_description', type: 'string'}
		    ,{name: 'lp[payor_id]', mapping: 'payor_id', type: 'string'}
		    ,{name: 'lp[payment_date]', mapping: 'payment_date', type: 'date', convert: function(value, rec){
		    	return formatDate(value);}}
			,{name: 'payment_date', mapping: 'payment_date', type: 'string', convert: function(value, rec){
		    	return formatDate(value);}}
		    ,{name: 'lp[interest_amount]', mapping: 'interest_amount', type: 'string'}
		    ,{name: 'lp[principal_amount]', mapping: 'principal_amount', type: 'string'}
		    ,{name: 'lp[amount]', mapping: 'principal_amount', type: 'string'}
		    ,{name: 'lp[transaction_code]', mapping: 'transaction_code', type: 'string'}
			,{name: 'transaction_code', mapping: 'transaction_code', type: 'string'}
			,{name: 'lp[or_no]', mapping: 'or_no', type: 'string'}
			,{name: 'lp[or_date]', mapping: 'or_date', type: 'string'}
			,{name: 'lp[loan_code]', mapping: 'loan_code', type: 'string'}
			,{name: 'loan_code', mapping: 'loan_code', type: 'string'}
		    ,{name: 'lp[transaction_description]', mapping: 'transaction_description', type: 'string'}
		    ,{name: 'lp[remarks]', mapping: 'remarks', type: 'string'}
		    ,{name: 'lp[payor_id]', mapping: 'payor_id', type: 'string'}
		    
		 ]
	)

	// Loan Payment Payor Reader
	,lpPayorReader: new Ext.data.JsonReader({
	    totalProperty: 'total'
	    ,root: 'data' 	
	    },[
		    {name: 'payor_id', mapping: 'payor_id', type: 'string'}
		    ,{name: 'last_name', mapping: 'last_name', type: 'string'}
		    ,{name: 'first_name', mapping: 'first_name', type: 'string'}
			,{name: 'last_first', mapping: 'last_name', type: 'string', convert: function(value, rec){
		    	return value + ', ' + rec.first_name;}}
	])
	// Loan Payment Type Reader
	,lpTypeReader: new Ext.data.JsonReader({
	    totalProperty: 'total'
	    ,root: 'data' 	
	    },[
		    {name: 'payment_code', mapping: 'payment_code', type: 'string'}
		    ,{name: 'payment_type_description', mapping: 'payment_type_description', type: 'string'}
	])
	// Loan List Reader
	,lpLoanListReader: new Ext.data.JsonReader({
	    totalProperty: 'total'
		,idProperty: 'id'
	    ,root: 'data' 	
	    },[
		    {name: 'loan_no', mapping: 'loan_no', type: 'string'}
		    ,{name: 'loan_description', mapping: 'loan_description', type: 'string'}
			,{name: 'loan_date', mapping: 'loan_date', type: 'string', convert: function(value, rec){
		    	return formatDate(value);}}
			,{name: 'employee_id', mapping: 'employee_id', type: 'string'}
			,{name: 'last_name', mapping: 'last_name', type: 'string'}
			,{name: 'first_name', mapping: 'first_name', type: 'string'}
			,{name: 'company_code', mapping: 'company_code', type: 'string'}
			,{name: 'loan_code', mapping: 'loan_code', type: 'string'}
			,{name: 'loan_description', mapping: 'loan_description', type: 'string'}
			,{name: 'loan_balance', mapping: 'loan_balance', type: 'string'}
			,{name: 'employee_principal_amortization', mapping: 'employee_principal_amortization', type: 'string'}
			,{name: 'employee_interest_amortization', mapping: 'employee_interest_amortization', type: 'string'}
	])
	//Investment Reader
	,investmentReader: new Ext.data.JsonReader({
		totalProperty: 'total'
		,successProperty: 'success'
	    ,idProperty: 'id'	
		,root: 'data'
		},[
		    {name: 'newinvestment[investment_no]', mapping: 'investment_no', type: 'string'}
			,{name: 'investment_no', mapping: 'investment_no', type: 'string'}
		    ,{name: 'newinvestment[transaction_description]', mapping: 'transaction_description', type: 'string'}
			,{name: 'newinvestment[transaction_code]', mapping: 'transaction_code', type: 'string'}
		    ,{name: 'newinvestment[supplier_name]', mapping: 'supplier_name', type: 'string'}
			,{name: 'newinvestment[supplier_id]', mapping: 'supplier_id', type: 'string'}
		    ,{name: 'newinvestment[placement_date]', mapping: 'placement_date', type: 'date', convert:function(value,rec){
				return formatDate(value);
			}}
			,{name: 'newinvestment[investment_amount]', mapping: 'investment_amount', type: 'float'}
			,{name: 'newinvestment[investment_type]', mapping: 'investment_type', type: 'string'}
			,{name: 'newinvestment[placement_days]', mapping: 'placement_days', type: 'string'}
			,{name: 'newinvestment[interest_rate]', mapping: 'interest_rate', type: 'string'}
			,{name: 'newinvestment[maturity_date]', mapping: 'maturity_date', type: 'string', convert:function(value,rec){
				return formatDate(value);
			}}
			,{name: 'newinvestment[interest_amount]', mapping: 'interest_amount', type: 'string', convert: function(value, rec){
		    	return Ext.util.Format.number(value,'0.00');}}
			,{name: 'newinvestment[remarks]', mapping: 'remarks', type: 'string'}
	    ]
	)
	//Investment Maturity Reader
	,imReader: new Ext.data.JsonReader({
		totalProperty: 'total'
		,successProperty: 'success'
	    ,idProperty: 'id'	
		,root: 'data'
		},[
		    {name: 'newinvestment[investment_no]', mapping: 'investment_no', type: 'string'}
			,{name: 'investment_no', mapping: 'investment_no', type: 'string'}
			,{name: 'transaction_description', mapping: 'transaction_description', type: 'string'}
			,{name: 'supplier_id', mapping: 'supplier_id', type: 'string'}
			,{name: 'supplier_name', mapping: 'supplier_name', type: 'string'}
		    ,{name: 'newinvestment[supplier_name]', mapping: 'supplier_name', type: 'string'}
		    ,{name: 'newinvestment[placement_date]', mapping: 'placement_date', type: 'date', convert:function(value,rec){
				return formatDate(value);
			}}
			,{name: 'newinvestment[maturity_date]', mapping: 'maturity_date', type: 'date', convert:function(value,rec){
				return formatDate(value);
			}}
			,{name: 'newinvestment[investment_amount]', mapping: 'investment_amount', type: 'float'}
			,{name: 'newinvestment[supplier_id]', mapping: 'supplier_id', type: 'string'}
			,{name: 'newinvestment[investment_type]', mapping: 'investment_type', type: 'string'}
			,{name: 'newinvestment[interest_rate]', mapping: 'interest_rate', type: 'string'}
			//,{name: 'newinvestment[or_no]', mapping: 'or_no', type: 'string'}
			,{name: 'newinvestment[or_date]', mapping: 'or_date', type: 'string'}
			,{name: 'newinvestment[interest_amount]', mapping: 'interest_amount', type: 'string'}
			,{name: 'interest_amount', mapping: 'interest_amount', type: 'string'}
			,{name: 'newinvestment[transaction_code]', mapping: 'transaction_code', type: 'string'}
			,{name: 'newinvestment[rollover_placement_days]', mapping: 'rollover_placement_days', type: 'string'}
			,{name: 'newinvestment[rollover_placement_date]', mapping: 'rollover_placement_date', type: 'string'}			
			,{name: 'newinvestment[rollover_interest_rate]', mapping: 'rollover_interest_rate', type: 'string'}
			,{name: 'newinvestment[rollover_interest_amount]', mapping: 'rollover_interest_amount', type: 'string'}
			,{name: 'newinvestment[rollover_maturity_date]', mapping: 'rollover_maturity_date', type: 'string'}	
	    ]
	)
	
	// Loan Payment Charges Detail Reader
	,lpChargesReader: new Ext.data.JsonReader({
	    totalProperty: 'total'
    	,idProperty: 'id'	
	    ,root: 'data'
	    },[
		    //{name: 'loan_no', mapping: 'loan_no', type: 'string'}
		    //{name: 'payment_date', mapping: 'payment_date', type: 'string'}
			//{name: 'payor_id', mapping: 'payor_id', type: 'string'}
		    {name: 'charge_code', mapping: 'charge_code', type: 'string'}
		    ,{name: 'transaction_description', mapping: 'transaction_description', type: 'string'}
		    ,{name: 'amount', mapping: 'amount', type: 'float'}
	])
	
	//Employee with Loans List Reader
	,employeeWithLoanReader: new Ext.data.JsonReader({
	    totalProperty: 'total'
	    ,root: 'data' 	
	    },[
		    {name: 'employee_id', mapping: 'employee_id', type: 'string'}
		    ,{name: 'last_name', mapping: 'last_name', type: 'string'}
		    ,{name: 'first_name', mapping: 'first_name', type: 'string'}
	])
	//Journal List Reader
	,journalReader: new Ext.data.JsonReader({
	    totalProperty: 'total'
	    ,root: 'data' 	
	    },[
		    {name: 'journalHdr[journal_no]', mapping: 'journal_no', type: 'string'}
		    ,{name: 'journal_no', mapping: 'journal_no', type: 'string'}
		    ,{name: 'journalHdr_formated', mapping: 'particulars', type: 'string', convert:function(value,rec){
				return Ext.util.Format.htmlEncode(value);
			}}
		    ,{name: 'journalHdr[particulars]', mapping: 'particulars', type: 'string'}
		    ,{name: 'journalHdr[transaction_code]', mapping: 'transaction_code', type: 'string'}
		    ,{name: 'journalHdr[reference]', mapping: 'reference', type: 'string'}
		    ,{name: 'journalHdr[document_no]', mapping: 'document_no', type: 'string'}
		    ,{name: 'journalHdr[document_date]', mapping: 'document_date', type: 'string', convert:function(value,rec){
				return formatDate(value);
			}}
		    ,{name: 'journalHdr[remarks]', mapping: 'remarks', type: 'string'}
		    ,{name: 'journalHdr[supplier_id]', mapping: 'supplier_id', type: 'string'}
		    ,{name: 'journalHdr[transaction_date]', mapping: 'transaction_date', type: 'date', convert:function(value,rec){
				return formatDate(value);
			}}
	])
	
	//For Account List Dropdown
	,accountReader: new Ext.data.JsonReader({
	    totalProperty: 'total'
	    ,root: 'data' 	
	    },[
		    {name: 'account_no', mapping: 'account_no', type: 'string'}
		    ,{name: 'account_name', mapping: 'account_name', type: 'string'}
			,{name: 'account_no_name', mapping: 'account_name', type: 'string', convert:function(value,rec){
				return rec.account_no+' - '+value;
			}}
	])
	
	//For Entry Type Dropdown
	,entryReader: new Ext.data.JsonReader({
	    totalProperty: 'total'
	    ,root: 'data' 	
	    },[
		    {name: 'gl_code', mapping: 'gl_code', type: 'string'}
		    ,{name: 'gl_description', mapping: 'gl_description', type: 'string'}
		    ,{name: 'particulars', mapping: 'particulars', type: 'string'}
	])
	//For Debit Credit List
	,debitCreditReader: new Ext.data.JsonReader({
	    totalProperty: 'total'	
	    ,root: 'data' 	
	    },[
		    {name: 'account_no', mapping: 'account_no', type: 'string'}
		    ,{name: 'journal_no', mapping: 'journal_no', type: 'string'}
		    ,{name: 'account_name', mapping: 'account_name', type: 'string'}
			,{name: 'amount', mapping: 'amount', type: 'float'}			
			,{name: 'debit', type: 'string', mapping: 'debit_credit', type: 'float', convert: function(value, rec){
		    	if (value == 'D'){ return rec.amount; } else {return '';} }}
			,{name: 'credit', type: 'string', mapping: 'debit_credit', type: 'float', convert: function(value, rec){
		    	if (value == 'C') {return rec.amount; } else {return '';} }}
			,{name: 'debit_credit', mapping: 'debit_credit', type: 'string'}
			
	])
	

	//Investment Type Reader
	,itypeReader: new Ext.data.JsonReader({
		totalProperty: 'total'
		,successProperty: 'success'
	    ,idProperty: 'id'
		,root: 'data'
		},[
		    {name: 'transcode[transaction_code]', mapping: 'transaction_code', type: 'string'}
			,{name: 'transaction_code', mapping: 'transaction_code', type: 'string'}
			,{name: 'transcode[transaction_description]', mapping: 'transaction_description', type: 'string'}
			,{name: 'transaction_description', mapping: 'transaction_description', type: 'string'}
			,{name: 'transcode[gl_code]', mapping: 'gl_code', type: 'string'}
			,{name: 'transcode[transaction_group]', mapping: 'transaction_group', type: 'string'}
			,{name: 'transcode[transaction_group_name]', mapping: 'tg_name', type: 'string'}
			,{name: 'newinvestment[transaction_type]', mapping: 'transaction_code', type: 'string'}
	    ]
	)
	//Bank (Supplier List) Reader
	,supplierReader: new Ext.data.JsonReader({
		totalProperty: 'total'
		,successProperty: 'success'
		,root: 'data'
		},[
		    {name: 'supplier[supplier_id]', mapping: 'supplier_id', type: 'string'}
			,{name: 'supplier_id', mapping: 'supplier_id', type: 'string'}
			,{name: 'supplier[supplier_name]', mapping: 'supplier_name', type: 'string'}
			,{name: 'supplier_name', mapping: 'supplier_name', type: 'string'}
			,{name: 'supplierHdr[supplier_id]', mapping: 'supplier_id', type: 'string'}	
			,{name: 'supplierHdr[supplier_name]', mapping: 'supplier_name', type: 'string'}	
			,{name: 'supplierHdr[TIN]', mapping: 'TIN', type: 'string'}	
			,{name: 'supplierHdr[account_number]', mapping: 'account_number', type: 'string'}	
	    ]
	)
	//New Loan - Restructure Loan Reader
	,newloanRLReader: new Ext.data.JsonReader({
		totalProperty: 'total'
		,root: 'data'
		},[
		    {name: 'loan_no', mapping: 'loan_no', type: 'string'}
			,{name: 'balance', mapping: 'balance', type: 'string'}
			,{name: 'loan_date', mapping: 'loan_date', type: 'string'}
			,{name: 'principal', mapping: 'principal', type: 'string'}		
			,{name: 'term', mapping: 'term', type: 'string'}
			,{name: 'interest_rate', mapping: 'interest_rate', type: 'string'}
	    ]
	)
	//Maturity Code Reader
	,mctypeReader: new Ext.data.JsonReader({
		totalProperty: 'total'
		,successProperty: 'success'
	    ,idProperty: 'id'
		,root: 'data'
		},[
		    {name: 'transcode[transaction_code]', mapping: 'transaction_code', type: 'string'}
			,{name: 'transaction_code', mapping: 'transaction_code', type: 'string'}
			,{name: 'transcode[transaction_description]', mapping: 'transaction_description', type: 'string'}
			,{name: 'transaction_description', mapping: 'transaction_description', type: 'string'}
			,{name: 'transcode[gl_code]', mapping: 'gl_code', type: 'string'}
			,{name: 'transcode[transaction_group]', mapping: 'transaction_group', type: 'string'}
			,{name: 'transcode[transaction_group_name]', mapping: 'tg_name', type: 'string'}
			,{name: 'newinvestment[transaction_type]', mapping: 'transaction_code', type: 'string'}
	    ]
	)
	//Capital Contribution Detail Reader
	,newloanOtherChargesReader: new Ext.data.JsonReader({
	    totalProperty: 'total'
		,idProperty: 'id'	
		,root: 'data'
		},[
		    {name: 'id', mapping: 'id', type: 'string'}
		    ,{name: 'loan_no', mapping: 'loan_no', type: 'string'}
		    ,{name: 'transaction_code', mapping: 'transaction_code', type: 'string'}
		    ,{name: 'transaction_description', mapping: 'transaction_description', type: 'string'}
		    ,{name: 'amount', mapping: 'amount', type: 'float'}
		]
	)
	//New Loan - Transaction type reader
	,newloanLTReader: new Ext.data.JsonReader({
		totalProperty: 'total'
		,successProperty: 'success'	
		,root: 'data'
		},[
		   	{name: 'loan_code', mapping: 'loan_code', type: 'string'}
		   	,{name: 'loan_description', mapping: 'loan_description', type: 'string'}
		   	,{name: 'emp_interest_pct', mapping: 'emp_interest_pct', type: 'float', convert: function(value, rec){
		    	return Ext.util.Format.number(value,'0.00');}}
		   	,{name: 'comp_share_pct', mapping: 'comp_share_pct', type: 'float', convert: function(value, rec){
		    	return Ext.util.Format.number(value,'0.00');}}
		   	,{name: 'interest_earned', mapping: 'interest_earned', type: 'string', convert: function(value, rec){
		    	return formatCheckbox(value);}}
		   	,{name: 'unearned_interest', mapping: 'unearned_interest', type: 'string', convert: function(value, rec){
		    	return formatCheckbox(value);}}
		   	,{name: 'pension', mapping: 'pension', type: 'string', convert: function(value, rec){
		    	return formatCheckbox(value);}}
		   	,{name: 'guarantor', mapping: 'guarantor', type: 'string'}
	    ]
	)
	//Workflow Reader
	,workflowReader: new Ext.data.JsonReader({
	    totalProperty: 'total'
		,idProperty: 'id'	
		,root: 'data'
		},[
		    {name: 'workflow[request_name]', mapping: 'request_name', type: 'string'}
			,{name: 'request_name', mapping: 'request_name', type: 'string'}
			,{name: 'workflow[request_type]', mapping: 'request_type', type: 'string'}
			,{name: 'request_type', mapping: 'request_type', type: 'string'}
		    ,{name: 'workflow[approver1_name]', mapping: 'approver1_name', type: 'string'}
			,{name: 'workflow[approver2_name]', mapping: 'approver2_name', type: 'string'}
			,{name: 'workflow[approver3_name]', mapping: 'approver3_name', type: 'string'}
			,{name: 'workflow[approver4_name]', mapping: 'approver4_name', type: 'string'}
			,{name: 'workflow[approver5_name]', mapping: 'approver5_name', type: 'string'}
			,{name: 'workflow[approver1]', mapping: 'approver1_id', type: 'string'}
			,{name: 'workflow[approver2]', mapping: 'approver2_id', type: 'string'}
			,{name: 'workflow[approver3]', mapping: 'approver3_id', type: 'string'}
			,{name: 'workflow[approver4]', mapping: 'approver4_id', type: 'string'}
			,{name: 'workflow[approver5]', mapping: 'approver5_id', type: 'string'}
		]
	)
	//Workflow Approver Reader
	,wApproverReader: new Ext.data.JsonReader({
	    totalProperty: 'total'
		,idProperty: 'id'	
		,root: 'data'
		},[
		    {name: 'workflow[approver_id]', mapping: 'approver_id', type: 'string'}
			,{name: 'approver_id', mapping: 'approver_id', type: 'string'}
			,{name: 'workflow[approver_name]', mapping: 'approver_name', type: 'string'}
			,{name: 'approver_name', mapping: 'approver_name', type: 'string'}
		    
		]
	)
	//Workflow Request Reader
	,wRequestReader: new Ext.data.JsonReader({
	    totalProperty: 'total'
		,idProperty: 'id'	
		,root: 'data'
		},[
		    {name: 'workflow[request_type]', mapping: 'request_type', type: 'string'}
			,{name: 'request_type', mapping: 'request_type', type: 'string'}
			,{name: 'workflow[request_name]', mapping: 'request_name', type: 'string'}
			,{name: 'request_name', mapping: 'request_name', type: 'string'}
		    
		]
	)
	//Online Loan Reader
	,ol_loanReader: new Ext.data.JsonReader({
	    totalProperty: 'total'
		,idProperty: 'id'	
		,root: 'data'
		},[
		    {name: 'online_loan[loan_date]', mapping: 'loan_date', type: 'date', convert:function(value,rec){
				return formatDate(value);}}
			,{name: 'online_loan[loan_type]', mapping: 'loan_type', type: 'string'}
			,{name: 'last_name', mapping: 'last_name', type: 'string'}
		    ,{name: 'first_name', mapping: 'first_name', type: 'string'}
			,{name: 'online_loan[employee_name]', mapping: 'last_name', type: 'string', convert: function(value, rec){
		    	return value + ', ' + rec.first_name;}}
			,{name: 'online_loan[amount]', mapping: 'amount', type: 'float'}	
			,{name: 'status', mapping: 'status_flag', type: 'string'}			
			,{name: 'online_loan[status]', mapping: 'status_flag', type: 'string', convert: function(value){
				var ret;
		    	if(value=='1')
					ret='New';
				else if(value=='2')
					ret='Saved';
				else if(value=='3' || value=='4' || value=='5' || value=='6' || value=='7' || value=='8')
					ret='For Approval';
				else if(value=='9')
					ret='Approved';
				else if(value=='10')
					ret='Rejected';
				return ret;}}
			,{name: 'status', mapping: 'status_flag', type: 'string'}
			,{name: 'approver1', mapping: 'approver1', type: 'string'}
			,{name: 'approver2', mapping: 'approver2', type: 'string'}
			,{name: 'approver3', mapping: 'approver3', type: 'string'}
			,{name: 'approver4', mapping: 'approver4', type: 'string'}
			,{name: 'approver5', mapping: 'approver5', type: 'string'}
			,{name: 'online_loan[approver_name]', mapping: 'status_flag', type: 'string', convert: function(value,rec){
				var ret;
				if(value=='1')
					ret=rec.approver1;
				else if(value=='2')
					ret=rec.approver1;
				else if(value=='3')
					ret=rec.approver1;
				else if(value=='4')
					ret=rec.approver2;
				else if(value=='5')
					ret=rec.approver3;
				else if(value=='6')
					ret=rec.approver4;
				else if(value=='7')
					ret=rec.approver5;
				return ret;}}
			,{name: 'online_loan[loan_code]', mapping: 'loan_code', type: 'string'}
			,{name: 'loan_code', mapping: 'loan_code', type: 'string'}
			,{name: 'online_loan[request_no]', mapping: 'request_no', type: 'string'}
			,{name: 'request_no', mapping: 'request_no', type: 'string'}
			,{name: 'ol_employee_id', mapping: 'employee_id', type: 'string'}
			,{name: 'online_loan[employee_id]', mapping: 'employee_id', type: 'string'}
			,{name: 'online_loan[last_name]', mapping: 'last_name', type: 'string'}
			,{name: 'online_loan[first_name]', mapping: 'first_name', type: 'string'}
			,{name: 'online_loan[principal]', mapping: 'amount', type: 'string'}
			,{name: 'online_loan[term]', mapping: 'term', type: 'string'}
			,{name: 'online_loan[interest_rate]', mapping: 'interest_rate', type: 'string'}
			,{name: 'loan_date', mapping: 'loan_date', type: 'string'}
			,{name: 'online_loan[member_remarks]', mapping: 'member_remarks', type: 'string'}
			,{name: 'online_loan[peca_remarks]', mapping: 'peca_remarks', type: 'string'}
			,{name: 'online_loan[approvers]', mapping: 'status_flag', type: 'string', convert: function(value,rec){
				/*var ret;
				if(value=='4')
					ret=rec.approver1;
				else if(value=='5')
					ret=rec.approver1+'\n'+rec.approver2;
				else if(value=='6')
					ret=rec.approver1+'\n'+rec.approver2+'\n'+rec.approver3;
				else if(value=='7')
					ret=rec.approver1+'\n'+rec.approver2+'\n'+rec.approver3+'\n'+rec.approver4;
				else if(value=='9')
					ret=rec.approver1+'\n'+rec.approver2+'\n'+rec.approver3+'\n'+rec.approver4+'\n'+rec.approver5;*/
				return rec.approver1+'\n'+rec.approver2+'\n'+rec.approver3+'\n'+rec.approver4+'\n'+rec.approver5;;}}
		]
	)
	//Loan Type Reader (Online Loan)
	,ol_loantypeReader: new Ext.data.JsonReader({
	    totalProperty: 'total'
		,idProperty: 'id'	
		,root: 'data'
		},[
			{name: 'loan_code', mapping: 'loan_code', type: 'string'}
			,{name: 'loan_description', mapping: 'loan_description', type: 'string'}	
			,{name: 'employee_interest_rate', mapping: 'employee_interest_rate', type: 'float', convert: function(value, rec){
		    	return Ext.util.Format.number(value,'0.00');}}				
		]
	)
	//Online Withdrawal Reader
	,ol_withdrawalReader: new Ext.data.JsonReader({
	    totalProperty: 'total'
		,idProperty: 'id'	
		,root: 'data'
		},[
		    {name: 'online_withdrawal[transaction_date]', mapping: 'transaction_date', type: 'date', convert:function(value,rec){
				return formatDate(value);}}
			,{name: 'online_withdrawal[transaction_description]', mapping: 'transaction_description', type: 'string'}
			,{name: 'last_name', mapping: 'last_name', type: 'string'}
		    ,{name: 'first_name', mapping: 'first_name', type: 'string'}
			,{name: 'online_withdrawal[employee_name]', mapping: 'last_name', type: 'string', convert: function(value, rec){
		    	return value + ', ' + rec.first_name;}}
			,{name: 'online_withdrawal[transaction_amount]', mapping: 'transaction_amount', type: 'float'}	
			,{name: 'online_withdrawal[or_no]', mapping: 'or_no', type: 'string', convert: function(value, rec){
		    	return ((value == null || isNaN(value))?"-":value);}}	
			,{name: 'approver1', mapping: 'approver1', type: 'string'}
			,{name: 'approver2', mapping: 'approver2', type: 'string'}
			,{name: 'approver3', mapping: 'approver3', type: 'string'}
			,{name: 'approver4', mapping: 'approver4', type: 'string'}
			,{name: 'approver5', mapping: 'approver5', type: 'string'}
			,{name: 'online_withdrawal[approver_name]', mapping: 'status', type: 'string', convert: function(value,rec){
				var ret;
				if(value=='1')
					ret=rec.approver1;
				else if(value=='2')
					ret=rec.approver1;
				else if(value=='3')
					ret=rec.approver1;
				else if(value=='4')
					ret=rec.approver2;
				else if(value=='5')
					ret=rec.approver3;
				else if(value=='6')
					ret=rec.approver4;
				else if(value=='7')
					ret=rec.approver5;
				return ret;}}
			,{name: 'status', mapping: 'status', type: 'string'}			
			,{name: 'online_withdrawal[status]', mapping: 'status', type: 'string', convert: function(value){
				var ret;
		    	if(value=='1')
					ret='New';
				else if(value=='2')
					ret='Saved';
				else if(value=='3' || value=='4' || value=='5' || value=='6' || value=='7' || value=='8')
					ret='For Approval';
				else if(value=='9')
					ret='Approved';
				else if(value=='10')
					ret='Rejected';
				return ret;}}
			
			,{name: 'online_withdrawal[employee_id]', mapping: 'employee_id', type: 'string'}
			,{name: 'online_withdrawal[last_name]', mapping: 'last_name', type: 'string'}
			,{name: 'online_withdrawal[first_name]', mapping: 'first_name', type: 'string'}
			,{name: 'online_withdrawal[transaction_code]', mapping: 'transaction_code', type: 'string'}
			,{name: 'transaction_code', mapping: 'transaction_code', type: 'string'}
			//,{name: 'online_withdrawal[transaction_date]', mapping: 'transaction_date', type: 'string'}
			,{name: 'online_withdrawal[transaction_amount]', mapping: 'transaction_amount', type: 'float'}
			,{name: 'ow_request_no', mapping: 'request_no', type: 'string'}		
			,{name: 'ow_status', mapping: 'status', type: 'string'}					
			,{name: 'online_withdrawal[request_no]', mapping: 'request_no', type: 'string'}
			,{name: 'request_no', mapping: 'request_no', type: 'string'}
			,{name: 'online_withdrawal[member_remarks]', mapping: 'member_remarks', type: 'string'}
			,{name: 'online_withdrawal[peca_remarks]', mapping: 'peca_remarks', type: 'string'}			
			,{name: 'ow_employee_id', mapping: 'employee_id', type: 'string'}			
			,{name: 'online_withdrawal[approvers]', mapping: 'status', type: 'string', convert: function(value,rec){
				/*var ret;
				if(value=='4')
					ret=rec.approver1;
				else if(value=='5')
					ret=rec.approver1+'\n'+rec.approver2;
				else if(value=='6')
					ret=rec.approver1+'\n'+rec.approver2+'\n'+rec.approver3;
				else if(value=='7')
					ret=rec.approver1+'\n'+rec.approver2+'\n'+rec.approver3+'\n'+rec.approver4;
				else if(value=='9')
					ret=rec.approver1+'\n'+rec.approver2+'\n'+rec.approver3+'\n'+rec.approver4+'\n'+rec.approver5;*/
				return rec.approver1+'\n'+rec.approver2+'\n'+rec.approver3+'\n'+rec.approver4+'\n'+rec.approver5;}}
			,{name: 'online_withdrawal[withdrawable_amount]', mapping: 'maxWdwlAmount', type: 'string'}
		    
		]
	)
	//Transaction Type Reader (Online Withdrawal)
	,ow_transactiontypeReader: new Ext.data.JsonReader({
	    totalProperty: 'total'
		,idProperty: 'id'	
		,root: 'data'
		},[
			{name: 'transaction_code', mapping: 'transaction_code', type: 'string'}
			,{name: 'transaction_description', mapping: 'transaction_description', type: 'string'}
		]
	)
	//Online Membership Reader
	,ol_membershipReader: new Ext.data.JsonReader({
	    totalProperty: 'total'
		,idProperty: 'id'	
		,root: 'data'
		},[
		    {name: 'online_membership[request_date]', mapping: 'request_date', type: 'date', convert:function(value,rec){
				return formatDate(value);}}
			,{name: 'last_name', mapping: 'last_name', type: 'string'}
		    ,{name: 'first_name', mapping: 'first_name', type: 'string'}
			,{name: 'online_membership[employee_name]', mapping: 'last_name', type: 'string', convert: function(value, rec){
		    	return value + ', ' + rec.first_name;}}
		    ,{name: 'approver1', mapping: 'approver1', type: 'string'}
			,{name: 'approver2', mapping: 'approver2', type: 'string'}
			,{name: 'approver3', mapping: 'approver3', type: 'string'}
			,{name: 'approver4', mapping: 'approver4', type: 'string'}
			,{name: 'approver5', mapping: 'approver5', type: 'string'}
			,{name: 'online_membership[approver_name]', mapping: 'status_flag', type: 'string', convert: function(value,rec){
				var ret;
				if(value=='1')
					ret=rec.approver1;
				else if(value=='2')
					ret=rec.approver1;
				else if(value=='3')
					ret=rec.approver1;
				else if(value=='4')
					ret=rec.approver2;
				else if(value=='5')
					ret=rec.approver3;
				else if(value=='6')
					ret=rec.approver4;
				else if(value=='7')
					ret=rec.approver5;
				return ret;}}
			,{name: 'status', mapping: 'status_flag', type: 'string'}			
			,{name: 'online_membership[status]', mapping: 'status_flag', type: 'string', convert: function(value){
				var ret;
		    	if(value=='1')
					ret='New';
				else if(value=='2')
					ret='Saved';
				else if(value=='3' || value=='4' || value=='5' || value=='6' || value=='7' || value=='8')
					ret='For Approval';
				else if(value=='9')
					ret='Approved';
				else if(value=='10')
					ret='Rejected';
				return ret;}}
			,{name: 'request_no', mapping: 'request_no', type: 'string'}
			,{name: 'online_membership[request_no]', mapping: 'request_no', type: 'string'}
			,{name: 'online_membership[employee_id]', mapping: 'employee_id', type: 'string'}
			,{name: 'online_membership[last_name]', mapping: 'last_name', type: 'string'}
			,{name: 'online_membership[first_name]', mapping: 'first_name', type: 'string'}
			,{name: 'online_membership[middle_name]', mapping: 'middle_name', type: 'string'}
			
			,{name: 'online_membership[member_date]', mapping: 'member_date', type: 'string'}
			,{name: 'online_membership[guarantor]', mapping: 'guarantor', type: 'string', convert:function(value,rec){
				return formatCheckbox(value);}}
			,{name: 'online_membership[non_member]', mapping: 'non_member', type: 'string', convert:function(value,rec){
				return formatCheckbox(value);}}
			,{name: 'online_membership[member_status]', mapping: 'member_status', type: 'string'}
			
			,{name: 'online_membership[TIN]', mapping: 'TIN', type: 'string'}
			,{name: 'online_membership[hire_date]', mapping: 'hire_date', type: 'string'}
			,{name: 'online_membership[work_date]', mapping: 'work_date', type: 'string'}
			,{name: 'online_membership[company_code]', mapping: 'company_code', type: 'string'}
			,{name: 'online_membership[department]', mapping: 'department', type: 'string'}
			,{name: 'online_membership[position]', mapping: 'position', type: 'string'}
			,{name: 'online_membership[office_no]', mapping: 'office_no', type: 'string'}
			,{name: 'online_membership[email_address]', mapping: 'email_address', type: 'string'}
			
			,{name: 'membership[birth_date]', mapping: 'birth_date', type: 'string'}
			,{name: 'membership[gender]', mapping: 'gender', type: 'string'}
			,{name: 'membership[civil_status]', mapping: 'civil_status', type: 'string'}
			,{name: 'membership[spouse]', mapping: 'spouse', type: 'string'}
			,{name: 'membership[address_1]', mapping: 'address_1', type: 'string'}
			,{name: 'membership[address_2]', mapping: 'address_2', type: 'string'}
			,{name: 'membership[address_3]', mapping: 'address_3', type: 'string'}
			,{name: 'membership[home_phone]', mapping: 'home_phone', type: 'string'}
			,{name: 'membership[mobile_no]', mapping: 'mobile_no', type: 'string'}
			,{name: 'membership[bank]', mapping: 'bank', type: 'string'}
			,{name: 'membership[bank_account_no]', mapping: 'bank_account_no', type: 'string'}
			
			//original values fields
			,{name: 'om_member_date', mapping: 'member_date', type: 'string', convert:function(value,rec){
				return pecaReaders.origValue + formatDate(value);}}
			,{name: 'om_guarantor', mapping: 'guarantor', type: 'string', convert:function(value,rec){
				return pecaReaders.origValue + formatCheckbox(value);}}
			,{name: 'om_non_member', mapping: 'non_member', type: 'string', convert:function(value,rec){
				return pecaReaders.origValue + formatCheckbox(value);}}
			,{name: 'om_member_status', mapping: 'member_status', type: 'string', convert:function(value,rec){
				if(value=='I'){
					return pecaReaders.origValue+"Inactive";
				}
					
				if(value=='A'){
					return pecaReaders.origValue+"Active";
				}
					
				return pecaReaders.origValue + value;		
				}}
			
			,{name: 'om_TIN', mapping: 'TIN', type: 'string', convert:function(value,rec){
				return pecaReaders.origValue + value;}}
			,{name: 'om_hire_date', mapping: 'hire_date', type: 'string', convert:function(value,rec){
				return pecaReaders.origValue +formatDate(value);}}
			,{name: 'om_work_date', mapping: 'work_date', type: 'string', convert:function(value,rec){
				return pecaReaders.origValue +formatDate(value);}}
			,{name: 'om_company_code', mapping: 'company_code', type: 'string', convert:function(value,rec){
				return pecaReaders.origValue + value;}}
			,{name: 'om_department', mapping: 'department', type: 'string', convert:function(value,rec){
				return pecaReaders.origValue + value;}}
			,{name: 'om_position', mapping: 'position', type: 'string', convert:function(value,rec){
				return pecaReaders.origValue + value;}}
			,{name: 'om_office_no', mapping: 'office_no', type: 'string', convert:function(value,rec){
				return pecaReaders.origValue + value;}}
			,{name: 'om_email_address', mapping: 'email_address', type: 'string', convert:function(value,rec){
				return pecaReaders.origValue + value;}}
			
			,{name: 'om_birth_date', mapping: 'birth_date', type: 'string', convert:function(value,rec){
				return pecaReaders.origValue + formatDate(value);}}
			,{name: 'om_gender', mapping: 'gender', type: 'string', convert:function(value,rec){
				if(value=="M"){
					return pecaReaders.origValue + "Male";
				}
				if(value=="F"){
					return pecaReaders.origValue + "Female";
				}
				return pecaReaders.origValue + value;
				}}
			,{name: 'om_civil_status', mapping: 'civil_status', type: 'string', convert:function(value,rec){
				if(value=="1"){
					return pecaReaders.origValue + "Single";
				}
				if(value=="2"){
					return pecaReaders.origValue + "Married";
				}
				if(value=="3"){
					return pecaReaders.origValue + "Separated";
				}
				if(value=="4"){
					return pecaReaders.origValue + "Widowed";
				}
				
				return pecaReaders.origValue + value;
				
				}}
			,{name: 'om_spouse', mapping: 'spouse', type: 'string', convert:function(value,rec){
				return pecaReaders.origValue + value;}}
			,{name: 'om_address_1', mapping: 'address_1', type: 'string', convert:function(value,rec){
				return pecaReaders.origValue + value;}}
			,{name: 'om_address_2', mapping: 'address_2', type: 'string', convert:function(value,rec){
				return pecaReaders.origValue + value;}}
			,{name: 'om_address_3', mapping: 'address_3', type: 'string', convert:function(value,rec){
				return pecaReaders.origValue + value;}}
			,{name: 'om_home_phone', mapping: 'home_phone', type: 'string', convert:function(value,rec){
				return pecaReaders.origValue + value;}}
			,{name: 'om_mobile_no', mapping: 'mobile_no', type: 'string', convert:function(value,rec){
				return pecaReaders.origValue + value;}}
				
			,{name: 'om_last_name', mapping: 'last_name', type: 'string', convert:function(value,rec){
				return pecaReaders.origValue + value;}}
			,{name: 'om_first_name', mapping: 'first_name', type: 'string', convert:function(value,rec){
				return pecaReaders.origValue + value;}}
			,{name: 'om_middle_name', mapping: 'middle_name', type: 'string', convert:function(value,rec){
				return pecaReaders.origValue + value;}}
			,{name: 'om_status', mapping: 'status_flag', type: 'numeric'}
			,{name: 'om_request_no', mapping: 'request_no', type: 'numeric'}
			
			//,{name: 'online_membership[approvers]', mapping: 'status_flag', type: 'string', convert: function(value,rec){
				/*var ret;
				if(value=='4')
					ret=rec.approver1;
				else if(value=='5')
					ret=rec.approver1+'\n'+rec.approver2;
				else if(value=='6')
					ret=rec.approver1+'\n'+rec.approver2+'\n'+rec.approver3;
				else if(value=='7')
					ret=rec.approver1+'\n'+rec.approver2+'\n'+rec.approver3+'\n'+rec.approver4;
				else if(value=='9')
					ret=rec.approver1+'\n'+rec.approver2+'\n'+rec.approver3+'\n'+rec.approver4+'\n'+rec.approver5;*/
				/*return rec.approver1+'\n'+rec.approver2+'\n'+rec.approver3+'\n'+rec.approver4+'\n'+rec.approver5;}}*/
		]
	)
	,memberReader: new Ext.data.JsonReader({
		totalProperty: 'total'
		,successProperty: 'success'	
		,root: 'data'
		},[
		   	{name: 'member[employee_id]', mapping: 'employee_id', type: 'string'}
			,{name: 'employee_id', mapping: 'employee_id', type: 'string'}
			,{name: 'member[last_name]', mapping: 'last_name', type: 'string'}
			,{name: 'member[first_name]', mapping: 'first_name', type: 'string'}
			,{name: 'member[middle_name]', mapping: 'middle_name', type: 'string'}
			,{name: 'member[member_date]', mapping: 'member_date', type: 'string', convert:function(value,rec){
				return formatDate(value);}}
			,{name: 'member[bank_account_no]', mapping: 'bank_account_no', type: 'string'}
			,{name: 'member[bank]', mapping: 'bank', type: 'string'}
			,{name: 'member[TIN]', mapping: 'TIN', type: 'string'}
			,{name: 'member[hire_date]', mapping: 'hire_date', type: 'string', convert:function(value,rec){
				return formatDate(value);}}
			,{name: 'member[work_date]', mapping: 'work_date', type: 'string', convert:function(value,rec){
				return formatDate(value);}}
			,{name: 'member[department]', mapping: 'department', type: 'string'}
			,{name: 'member[position]', mapping: 'position', type: 'string'}
			,{name: 'member[company_code]', mapping: 'company_code', type: 'string'}
			,{name: 'member[email_address]', mapping: 'email_address', type: 'string'}
			,{name: 'member[office_no]', mapping: 'office_no', type: 'string'}
			,{name: 'member[mobile_no]', mapping: 'mobile_no', type: 'string'}
			,{name: 'member[home_phone]', mapping: 'home_phone', type: 'string'}
			,{name: 'member[address_1]', mapping: 'address_1', type: 'string'}
			,{name: 'member[address_2]', mapping: 'address_2', type: 'string'}
			,{name: 'member[address_3]', mapping: 'address_3', type: 'string'}
			,{name: 'member[birth_date]', mapping: 'birth_date', type: 'string'}
			,{name: 'member[civil_status]', mapping: 'civil_status', type: 'string'}
			,{name: 'member[gender]', mapping: 'gender', type: 'string'}
			,{name: 'member[spouse]', mapping: 'spouse', type: 'string'}
			,{name: 'member[guarantor]', mapping: 'guarantor', type: 'string', convert:function(value,rec){
				return formatCheckbox(value);}}
			,{name: 'member[non_member]', mapping: 'non_member', type: 'string', convert:function(value,rec){
				return formatCheckbox(value);}}	
			,{name: 'member[beneficiaries]', mapping: 'beneficiaries', type: 'string'}
			,{name: 'member[member_status]', mapping: 'member_status', type: 'string'}
	    ]
	)
	,memberLoanInfoReader: new Ext.data.JsonReader({
	    totalProperty: 'total'	
		,root: 'data'
		},[
		    {name: 'loan_no', mapping: 'loan_no', type: 'string'}
		    ,{name: 'loan_code', mapping: 'loan_code', type: 'string'}
			,{name: 'loan_date', mapping: 'loan_date', type: 'date', convert:function(value,rec){
				return formatDate(value);}}
			,{name: 'loan_description', mapping: 'loan_description', type: 'float'}
			,{name: 'loan_balance', mapping: 'loan_balance', type: 'float'}
			,{name: 'principal', mapping: 'principal', type: 'float'}
			,{name: 'term', mapping: 'term', type: 'float'}
			,{name: 'rate', mapping: 'rate', type: 'float'}
			,{name: 'interest_amortization', mapping: 'interest_amortization', type: 'float'}
			,{name: 'principal_amortization', mapping: 'principal_amortization', type: 'float'}
			,{name: 'montly_amortization', mapping: 'principal_amortization', type: 'float', convert: function(value, rec){
		    	return (parseFloat(value) + parseFloat(rec.interest_amortization));}}
			,{name: 'principal_balance', mapping: 'principal_balance', type: 'float'}
			,{name: 'monthly_amortization', mapping: 'principal_amortization', type: 'float'}
		]
	)
	,memberLoanInfoSumReader: new Ext.data.JsonReader({
	    totalProperty: 'total'	
		,root: 'data'
		},[
			{name: 'interest_amortization', mapping: 'interest_amortization', type: 'string'}
			,{name: 'principal_amortization', mapping: 'principal_amortization', type: 'string'}
			,{name: 'principal_balance', mapping: 'principal_balance', type: 'string'}
			,{name: 'monthly_amortization', mapping: 'monthly_amortization', type: 'string'}
		]
	)
	,onlineLoanPaymentDetailReader: new Ext.data.JsonReader({
	    totalProperty: 'total'	
		,root: 'data'
		},[
		    {name: 'loan_no', mapping: 'loan_no', type: 'string'}
		    ,{name: 'loan_code', mapping: 'loan_code', type: 'string'}
			,{name: 'employee_id', mapping: 'employee_id', type: 'string'}
			,{name: 'loan_date', mapping: 'loan_date', type: 'date', convert:function(value,rec){
				return formatDate(value);}}
			,{name: 'principal', mapping: 'principal', type: 'float'}
			,{name: 'term', mapping: 'term', type: 'float'}
			,{name: 'rate', mapping: 'rate', type: 'float'}
			,{name: 'interest_amortization', mapping: 'interest_amortization', type: 'float'}
			,{name: 'principal_amortization', mapping: 'principal_amortization', type: 'float'}
			,{name: 'monthly_amortization', mapping: 'monthly_amortization', type: 'float'}
			,{name: 'principal_balance', mapping: 'principal_balance', type: 'float'}
			
		]
	)
	,guaranteedLoansReader: new Ext.data.JsonReader({
	    totalProperty: 'total'	
		,root: 'data'
		},[
		    {name: 'loan_no', mapping: 'loan_no', type: 'string'}
		    ,{name: 'loan_code', mapping: 'loan_code', type: 'string'}
			,{name: 'loan_date', mapping: 'loan_date', type: 'string', convert:function(value,rec){
				return formatDate(value);}}
			,{name: 'loan_description', mapping: 'loan_description', type: 'string'}
			,{name: 'middle_name', mapping: 'middle_name', type: 'string'}
			,{name: 'first_name', mapping: 'first_name', type: 'string'}
			,{name: 'employee_name', mapping: 'last_name', type: 'string', convert:function(value,rec){
				return value+', '+rec.first_name+' '+rec.middle_name;}}
			,{name: 'principal', mapping: 'principal', type: 'float'}
			,{name: 'interest_amortization', mapping: 'interest_amortization', type: 'float'}
			,{name: 'principal_amortization', mapping: 'principal_amortization', type: 'float'}
			,{name: 'monthly_amortization', mapping: 'principal_amortization', type: 'float', convert: function(value, rec){
		    	return (parseFloat(value) + parseFloat(rec.interest_amortization));}}
			,{name: 'principal_balance', mapping: 'principal_balance', type: 'float'}
		]
	)
	
	,transactionHistoryReader: new Ext.data.JsonReader({
	    totalProperty: 'total'	
		,root: 'data'
		},[
		    {name: 'transaction_date', mapping: 'transaction_date', type: 'date', convert:function(value,rec){
				return formatDate(value);}}
		    ,{name: 'transaction_code', mapping: 'transaction_code', type: 'string'}
		    ,{name: 'capcon_effect', mapping: 'capcon_effect', type: 'string'}
		    ,{name: 'transaction_amount', mapping: 'transaction_amount', type: 'float', convert:function(value,rec){
			if(parseInt(rec.capcon_effect)==-1){
				if(parseInt(value)<0)
					value = Ext.util.Format.number(value,'0,000,000,000.00');
				else {
					value = "(" + Ext.util.Format.number(value,'0,000,000,000.00') + ")";
				}
			} else {
				value = Ext.util.Format.number(value,'0,000,000,000.00');
			}
			return value;}}
		    ,{name: 'balance', mapping: 'balance', type: 'float', convert:function(value,rec){
				if(parseInt(value)<0)
					value = "(" + Ext.util.Format.number(parseFloat(value)*-1,'0,000,000,000.00') + ")";
				else {
					value = Ext.util.Format.number(value,'0,000,000,000.00');
				}
			return value;}}
			,{name: 'bal', mapping: 'balance', type: 'float'}
		]
	)
	
	,supplierInfoReader: new Ext.data.JsonReader({
	    totalProperty: 'total'	
		,root: 'data'
		},[
		    {name: 'supplier_id', mapping: 'supplier_id', type: 'string'}
		    ,{name: 'info_code', mapping: 'info_code', type: 'string'}
		    ,{name: 'information_description', mapping: 'information_description', type: 'string'}
		    ,{name: 'info_text', mapping: 'info_text', type: 'string'}
		]
	)
	
	,onlinePayrollDeductionReader: new Ext.data.JsonReader({
	    totalProperty: 'total'	
		,root: 'data'
		},[
		    {name: 'newpayroll[transaction_period]', mapping: 'transaction_period', type: 'date', convert:function(value,rec){
				return formatDate(value);}}
		    ,{name: 'newpayroll[last_name]', mapping: 'last_name', type: 'string'}
			,{name: 'last_name', mapping: 'last_name', type: 'string'}
			,{name: 'request_no', mapping: 'request_no', type: 'string'}
			,{name: 'newpayroll[request_no]', mapping: 'request_no', type: 'string'}
		    ,{name: 'newpayroll[first_name]', mapping: 'first_name', type: 'string'}
			,{name: 'newpayroll[start_date]', mapping: 'start_date', type: 'string'}
			,{name: 'start_date', mapping: 'start_date', type: 'string'}
			,{name: 'end_date', mapping: 'end_date', type: 'string'}
			,{name: 'newpayroll[end_date]', mapping: 'end_date', type: 'string'}
			,{name: 'newpayroll[transaction_code]', mapping: 'transaction_code', type: 'string'} 
			,{name: 'newpayroll[member_remarks]', mapping: 'member_remarks', type: 'string'} 
			,{name: 'newpayroll[peca_remarks]', mapping: 'peca_remarks', type: 'string'} 
			,{name: 'newpayroll[employee_id]', mapping: 'employee_id', type: 'string'}
			,{name: 'newpayroll[employee_name]', mapping: 'first_name', type: 'string', convert: function(value, rec){
		    	return rec.last_name + ', ' + value;  }}
		    ,{name: 'newpayroll[or_number]', mapping: 'or_number', type: 'string'}
			,{name: 'status', mapping: 'status_flag', type: 'string'}
			,{name: 'newpayroll[amount]', mapping: 'amount', type: 'float'}
			,{name: 'approver1', mapping: 'approver1', type: 'string'}
			,{name: 'approver2', mapping: 'approver2', type: 'string'}
			,{name: 'approver3', mapping: 'approver3', type: 'string'}
			,{name: 'approver4', mapping: 'approver4', type: 'string'}
			,{name: 'approver5', mapping: 'approver5', type: 'string'}
			,{name: 'newpayroll[approver_name]', mapping: 'status_flag', type: 'string', convert: function(value, rec){
		    	switch( value ){
					case '1':
					case '2':
					case '3':
						return rec.approver1;
					case '4':
						return rec.approver2;
					case '5':
						return rec.approver3;
					case '6':
						return rec.approver4;
					case '7':
						return rec.approver5;
					default:
						return '';
			}}}
			,{name: 'newpayroll[status]', mapping: 'status_flag', type: 'string', convert: function(value, rec){
		    	switch( value ){
					case '1':
						return 'New';
					case '2':
						return 'Saved';
					case '3':
						return 'For Approval';
					case '4':
						return 'For Approval';
					case '5':
						return 'For Approval';
					case '6':
						return 'For Approval';
					case '7':
						return 'For Approval';
					case '8':
						return 'For Approval';
					case '9':
						return 'Approved';
					case '10':
						return 'Rejected';
					default:
						return '';
			}}}
			,{name: 'newpayroll[approvers]', mapping: 'status_flag', type: 'string', convert: function(value, rec){
		    	/*switch( value ){
					case '4':
						return rec.approver1;
					case '5':
						return rec.approver1+'\n'+rec.approver2;
					case '6':
						return rec.approver1+'\n'+rec.approver2+'\n'+rec.approver3;
					case '7':
						return rec.approver1+'\n'+rec.approver2+'\n'+rec.approver3+'\n'+rec.approver4;
					case '8':
						return rec.approver1+'\n'+rec.approver2+'\n'+rec.approver3+'\n'+rec.approver4+'\n'+rec.approver5;
					case '9':*/
						return rec.approver1+'\n'+rec.approver2+'\n'+rec.approver3+'\n'+rec.approver4+'\n'+rec.approver5;
					/*default:
						return '';
			}
			*/}}
			
		]
	)
	,onlineLoanPaymentReader: new Ext.data.JsonReader({
	    totalProperty: 'total'	
		,root: 'data'
		},[
		    {name: 'lp[transaction_period]', mapping: 'payment_date', type: 'date', convert:function(value,rec){
				return formatDate(value);}}
			,{name: 'lp[loan_no]', mapping: 'loan_no', type: 'string'}	
		    ,{name: 'lp[last_name]', mapping: 'last_name', type: 'string'}
			,{name: 'last_name', mapping: 'last_name', type: 'string'}
			,{name: 'request_no', mapping: 'request_no', type: 'string'}
		    ,{name: 'lp[first_name]', mapping: 'first_name', type: 'string'}
			,{name: 'lp[loan_code]', mapping: 'loan_code', type: 'string'}
			,{name: 'lp[loan_description]', mapping: 'loan_description', type: 'string'}
			,{name: 'lp[transaction_description]', mapping: 'transaction_description', type: 'string'}
			,{name: 'lp[balance]', mapping: 'balance', type: 'string'}
			,{name: 'lp[amount]', mapping: 'amount', type: 'float'}
			,{name: 'amount', mapping: 'amount', type: 'float'}
			,{name: 'lp[interest_amount]', mapping: 'interest_amount', type: 'string'}
			,{name: 'lp[payment_date]', mapping: 'payment_date', type: 'string'}
			,{name: 'lp[transaction_code]', mapping: 'transaction_code', type: 'string'} 
			,{name: 'lp[transaction_code2]', mapping: 'transaction_code', type: 'string'} 
			,{name: 'lp[payor_id]', mapping: 'payor_id', type: 'string'}
			,{name: 'lp[employee_id]', mapping: 'employee_id', type: 'string'}
			,{name: 'lp[employee_name]', mapping: 'first_name', type: 'string', convert: function(value, rec){
		    	return rec.last_name + ', ' + value;  }}
		    ,{name: 'lp[member_remarks]', mapping: 'member_remarks', type: 'string'}
			,{name: 'lp[peca_remarks]', mapping: 'peca_remarks', type: 'string'}
			,{name: 'status_flag', mapping: 'status_flag', type: 'string'}
			,{name: 'lp[status]', mapping: 'status_flag', type: 'string', convert: function(value, rec){
		    	switch( value ){
					case '1':
						return 'New';
					case '2':
						return 'Saved';
					case '3':
						return 'For Approval';
					case '4':
						return 'For Approval';
					case '5':
						return 'For Approval';
					case '6':
						return 'For Approval';
					case '7':
						return 'For Approval';
					case '8':
						return 'For Approval';
					case '9':
						return 'Approved';
					case '10':
						return 'Rejected';
					default:
						return '';
			}}}
			,{name: 'lp[approver1]', mapping: 'approver1', type: 'string'}
			,{name: 'lp[approver2]', mapping: 'approver2', type: 'string'}
			,{name: 'lp[approver3]', mapping: 'approver3', type: 'string'}
			,{name: 'lp[approver4]', mapping: 'approver4', type: 'string'}
			,{name: 'lp[approver5]', mapping: 'approver5', type: 'string'}
			,{name: 'lp[approver_name]', mapping: 'status_flag', type: 'string', convert: function(value, rec){
		    	switch( value ){
					case '1':
					case '2':
					case '3':
						return rec.approver1;
					case '4':
						return rec.approver2;
					case '5':
						return rec.approver3;
					case '6':
						return rec.approver4;
					case '7':
						return rec.approver5;
					default:
						return '';
			}}}
			,{name: 'lp[approvers]', mapping: 'status_flag', type: 'string', convert: function(value, rec){
		    	/*switch( value ){
					case '4':
						return rec.approver1;
					case '5':
						return rec.approver1+'\n'+rec.approver2;
					case '6':
						return rec.approver1+'\n'+rec.approver2+'\n'+rec.approver3;
					case '7':
						return rec.approver1+'\n'+rec.approver2+'\n'+rec.approver3+'\n'+rec.approver4;
					case '8':
						return rec.approver1+'\n'+rec.approver2+'\n'+rec.approver3+'\n'+rec.approver4+'\n'+rec.approver5;
					case '9':*/
						return rec.approver1+'\n'+rec.approver2+'\n'+rec.approver3+'\n'+rec.approver4+'\n'+rec.approver5;
					/*default:
						return '';
			}*/
			}}
			,{name: 'lp[or_number]', mapping: 'or_number', type: 'string', convert: function(value, rec){
				if(rec.status_flag == 9)
					return value;
				else
					return '-';
			}}
		]
	)
	//member loan information
	,memberLoanInfoDetailReader: new Ext.data.JsonReader({
	    totalProperty: 'total'	
		,root: 'data'
		},[
		    {name: 'loan_no', mapping: 'loan_no', type: 'string'}
			,{name: 'restructure_amount', mapping: 'restructure_amount', type: 'string', convert: function(value, rec){
		    	return Ext.util.Format.number(value,'0,000,000,000.00');}}
			,{name: 'restructure', mapping: 'restructure_no', type: 'string', convert: function(value, rec){
				if(isNaN(value) || value == "")
					return formatCheckbox('N');
				else
					return formatCheckbox('Y');}}
			,{name: 'pension', mapping: 'pension', type: 'string', convert: function(value, rec){
				if(Ext.num(value,0)>0)
					return formatCheckbox('Y');
				else
					return formatCheckbox('N');}}
			,{name: 'noCoMaker', mapping: 'nocomaker', type: 'string', convert: function(value, rec){
					return formatCheckbox(value);}}
			,{name: 'pensionAmount', mapping: 'pension', type: 'string', convert: function(value, rec){
				if(Ext.num(value,0)>0)
					return Ext.util.Format.number(value,'0,000,000,000.00');
				else
					return '';}}		
			,{name: 'loan_code', mapping: 'loan_code', type: 'string'}
			,{name: 'loan_description', mapping: 'loan_description', type: 'string'}
			,{name: 'loan_date', mapping: 'loan_date', type: 'string', convert:function(value,rec){
				return formatDate(value);}}
			,{name: 'principal_balance', mapping: 'principal_balance', type: 'float', convert: function(value, rec){
		    	return Ext.util.Format.number(value,'0,000,000,000.00');}}
			,{name: 'principal', mapping: 'principal', type: 'float', convert: function(value, rec){
		    	return Ext.util.Format.number(value,'0,000,000,000.00');}}
			,{name: 'term', mapping: 'term', type: 'float'}
			,{name: 'employee_interest_rate', mapping: 'rate', type: 'float', convert: function(value, rec){
		    	return Ext.util.Format.number(value,'0,000,000,000.00');}}
			,{name: 'initial_interest', mapping: 'initial_interest', type: 'float', convert: function(value, rec){
		    	return Ext.util.Format.number(value,'0,000,000,000.00');}}
			,{name: 'employee_interest_total', mapping: 'employee_interest_total', type: 'float', convert: function(value, rec){
		    	return Ext.util.Format.number(value,'0,000,000,000.00');}}
			,{name: 'employee_interest_amortization', mapping: 'employee_interest_amortization', type: 'float', convert: function(value, rec){
		    	return Ext.util.Format.number(value,'0,000,000,000.00');}}
			,{name: 'amortization_startdate', mapping: 'amortization_startdate', type: 'string', convert:function(value,rec){
				return formatDate(value);}}
			,{name: 'employee_principal_amortization', mapping: 'employee_principal_amortization', type: 'float', convert: function(value, rec){
		    	return Ext.util.Format.number(value,'0,000,000,000.00');}}
			,{name: 'loan_proceeds', mapping: 'loan_proceeds', type: 'float', convert: function(value, rec){
		    	return Ext.util.Format.number(value,'0,000,000,000.00');}}
			,{name: 'mri_fip_amount', mapping: 'mri_fip_amount', type: 'string', convert: function(value, rec){
		    	return Ext.util.Format.number(value,'0,000,000,000.00');}}
			,{name: 'broker_fee_amount', mapping: 'broker_fee_amount', type: 'string', convert: function(value, rec){
		    	return Ext.util.Format.number(value,'0,000,000,000.00');}}
			,{name: 'government_fee_amount', mapping: 'government_fee_amount', type: 'string', convert: function(value, rec){
		    	return Ext.util.Format.number(value,'0,000,000,000.00');}}
			,{name: 'other_fee_amount', mapping: 'other_fee_amount', type: 'string', convert: function(value, rec){
		    	return Ext.util.Format.number(value,'0,000,000,000.00');}}
			,{name: 'service_fee_amount', mapping: 'service_fee_amount', type: 'string', convert: function(value, rec){
		    	return Ext.util.Format.number(value,'0,000,000,000.00');}}
			,{name: 'capital_contribution_balance', mapping: 'capital_contribution_balance', type: 'string', convert: function(value, rec){
		    	return Ext.util.Format.number(value,'0,000,000,000.00');}}
			,{name: 'company_interest_rate', mapping: 'company_interest_rate', type: 'string', convert: function(value, rec){
		    	return Ext.util.Format.number(value,'0,000,000,000.00');}}
			,{name: 'company_interest_total', mapping: 'company_interest_total', type: 'string', convert: function(value, rec){
		    	return Ext.util.Format.number(value,'0,000,000,000.00');}}
			,{name: 'company_interest_amort', mapping: 'company_interest_amort', type: 'string', convert: function(value, rec){
		    	return Ext.util.Format.number(value,'0,000,000,000.00');}}
			,{name: 'mri_due_amount', mapping: 'mri_due_amount', type: 'float', renderer:function(value,rec){
				return Ext.util.Format.number(value,'0,000.00');}}
			,{name: 'mri_due_date', mapping: 'mri_due_date', type: 'string', convert:function(value,rec){
				return formatDate(value);}}
			,{name: 'fip_due_amount', mapping: 'fip_due_amount', type: 'float', renderer:function(value,rec){
				return Ext.util.Format.number(value,'0,000.00');}}
			,{name: 'fip_due_date', mapping: 'fip_due_date', type: 'string', convert:function(value,rec){
				return formatDate(value);}}
		]
	)

	//User List for Daily Summary Reports
	,pecaUserReader: new Ext.data.JsonReader({
	    totalProperty: 'total'	
		,root: 'data'
		},[
		    {name: 'user_id', mapping: 'user_id', type: 'string'}
		    ,{name: 'user_name', mapping: 'user_name', type: 'string'}
		    ,{name: 'group_id', mapping: 'group_id', type: 'string'}
		]
	)
};
	
var pecaDataStores = {
	
	//Transaction Code List
	formImageStore: new Ext.data.Store({
		//restful: true
	    url: '/bulletin_board/readforms'
	    ,reader: pecaReaders.formImageReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//Transaction Code List
	,transcodeStore: new Ext.data.Store({
		//restful: true
	    url: '/transaction_code/read'
	    ,reader: pecaReaders.transcodeReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//Transaction Code List - Charges
	,transcodeChargesStore: new Ext.data.Store({
		//restful: true
	    url: '/transaction_code/read'
	    ,reader: pecaReaders.transcodeReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//Transaction Code List - ChargesÅ@ÇQ
	,transcodeChargesStore2: new Ext.data.Store({
		//restful: true
	    url: '/transaction_code/read'
	    ,reader: pecaReaders.transcodeReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//Chart of Accounts List
	,coaStore: new Ext.data.Store({
		//restful: true
	    url: '/chart_of_accounts/read'
	    ,reader: pecaReaders.coaReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//Chart of Accounts List for GL Entry 
	,coaGlStore: new Ext.data.Store({
		//restful: true
	    url: '/chart_of_accounts/read'
	    ,reader: pecaReaders.coaGlReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//for Transaction group dropdown
	,tgStore: new Ext.data.Store({
		//restful:true
	    url: '/common/getTG'
	    ,reader: new Ext.data.JsonReader( { 
	    	root: 'data'
	    	,fields: [ {name: 'code'},{name: 'name'}]
	    })
		,baseParams: {auth:_AUTH_KEY}
	})
	//for Transaction group dropdown
	,tgGLEntryStore: new Ext.data.Store({
		//restful:true
	    url: '/common/getGLEntryTG'
	    ,reader: new Ext.data.JsonReader( { 
	    	root: 'data'
	    	,fields: [ {name: 'code'},{name: 'name'}]
	    })
		,baseParams: {auth:_AUTH_KEY}
	})
	//for CapCon Effect dropdown
	,ceStore: new Ext.data.Store({
		//restful:true
	    url: '/common/getCE'
	    ,reader: new Ext.data.JsonReader( { 
	    	root: 'data'
	    	,fields: [ {name: 'code'},{name: 'name'}]
	    })
		,baseParams: {auth:_AUTH_KEY}
	})
	//GL Entries Header
	,glHdrStore: new Ext.data.Store({
		//restful:true
	    url: '/gl_entries/readHdr'
	    ,reader: pecaReaders.glHdrReader
		,baseParams: {auth:_AUTH_KEY}
	})
	//GL Entries Header
	,glTransHdrStore: new Ext.data.Store({
		//restful:true
	    url: '/gl_entries/readHdr'
	    ,reader: pecaReaders.glTransHdrReader
		,baseParams: {auth:_AUTH_KEY}
	})
	//Company List
	,companyStore: new Ext.data.Store({
		//restful: true
		url: '/company/read'
	    ,reader: pecaReaders.companyReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//Company List SOA
	,companyStoreSOA: new Ext.data.Store({
		//restful: true
		url: '/company/read'
	    ,reader: pecaReaders.companyReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//Company List for membership
	,companyMemberStore: new Ext.data.Store({
		//restful: true
		url: '/company/read'
	    ,reader: pecaReaders.companyReader
	    ,baseParams: {auth:_AUTH_KEY}
	})	
	//Company List : for batch
	,companyStoreBP: new Ext.data.Store({
		//restful: true
		url: '/company/read'
	    ,reader: pecaReaders.companyReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//Company List : for ol_membership
	,companyStoreOLM: new Ext.data.Store({
		//restful: true
		url: '/company/read'
	    ,reader: pecaReaders.companyReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//for Account Group dropdown
	,agStore: new Ext.data.Store({
		//restful:true
	    url: '/common/getAccountGroup'
	    ,reader: new Ext.data.JsonReader( { 
	    	root: 'data'
	    	,fields: [ {name: 'code'},{name: 'name'}]
	    })
		,baseParams: {auth:_AUTH_KEY}
	})
	//Information Code List
	,infocodeStore: new Ext.data.Store({
		//restful: true
	    url: '/information_code/read'
	    ,reader: pecaReaders.infocodeReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//Information Code for combo cox
	,infocodeComboStore: new Ext.data.Store({
		//restful: true
	    url: '/information_code/read'
	    ,reader: pecaReaders.infocodeReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//System Parameters List
	,sysparamsStore: new Ext.data.Store({
		//restful: true
	    url: '/system_parameters/read'
	    ,reader: pecaReaders.sysparamsReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//User  List
	,userStore: new Ext.data.Store({
		//restful: true
	    url: '/users/read'
	    ,reader: pecaReaders.userReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//User  List for permissions
	,userPermStore: new Ext.data.Store({
		//restful: true
	    url: '/users/read'
	    ,reader: pecaReaders.userPermReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//Group  List
	,groupStore: new Ext.data.Store({
		//restful: true
	    url: '/group/read'
	    ,reader: pecaReaders.groupReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//Group  List for permissions
	,groupPermStore: new Ext.data.Store({
		//restful: true
	    url: '/group/read'
	    ,reader: pecaReaders.groupPermReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	
	//for User Group  dropdown
	,grStore: new Ext.data.Store({
		//restful:true
		 url: '/group/read'
	    ,reader: new Ext.data.JsonReader( { 
	    	root: 'data'
	    	,fields: [ {name: 'group_id'},{name: 'group_name'}]
	    })
		,baseParams: {auth:_AUTH_KEY}
	})
	
	//User Store [Change Password]  
	,changepasswordStore: new Ext.data.Store({
		//restful: true
	    url: '/change_password/show'
	    ,reader: pecaReaders.userReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	
	//for available functions
	,availableFxnStore: new Ext.data.Store({
		//restful:true
		 url: '/permissions/getAvailableFunctions'
	    ,reader: new Ext.data.JsonReader( { 
	    	root: 'data'
	    	,fields: [ {name: 'function_idx'},{name: 'function_name'},{name: 'function_value'}]
	    })
		,baseParams: {auth:_AUTH_KEY}
	})
	
	//for available functions
	,permittedFxnStore: new Ext.data.Store({
		//restful:true
		 url: '/permissions/getPermittedFunctions'
	    ,reader: new Ext.data.JsonReader( { 
	    	root: 'data'
	    	,fields: [ {name: 'function_idx'},{name: 'function_name'},{name: 'function_value'}]
	    })
		,baseParams: {auth:_AUTH_KEY}
	})
	//Bulletin List
	,bulletinStore: new Ext.data.Store({
		//restful: true
	    url: '/bulletin_board/read'
	    ,reader: pecaReaders.bulletinReader
	    ,baseParams: {is_admin:_IS_ADMIN, auth:_AUTH_KEY}
	})
	//Bulletin Sticky List
	,bulletinStickyStore: new Ext.data.Store({
		//restful: true
	    url: '/bulletin_board/readSticky'
	    ,reader: pecaReaders.bulletinReader
	    ,baseParams: {is_admin:_IS_ADMIN, auth:_AUTH_KEY}
	})
	//for available functions
	,fileStore: new Ext.data.Store({
		//restful:true
		 url: '/bulletin_board/getTopicAttachmentList'
	    ,reader: new Ext.data.JsonReader( { 
	    	root: 'data'
	    	,fields: [ {name: 'attachment_id'},{name: 'path'},{name:'type'},{name:'size'}]
	    })
		,baseParams: {auth:_AUTH_KEY}
	})
	//for important documents
	,fileStore2: new Ext.data.Store({
		//restful:true
		 url: '/bulletin_board/getTopicAttachmentList2'
	    ,reader: new Ext.data.JsonReader( { 
	    	root: 'data'
	    	,fields: [ {name: 'attachment_id'},{name:'filename'},{name: 'path'},{name:'type'},{name:'size'}]
	    })
		,baseParams: {auth:_AUTH_KEY, 'topic_id': '0'}
	})
	//for available functions
	,ol_fileStore: new Ext.data.Store({
		//restful:true
		 url: '/online_loan/getTopicAttachments'
	    ,reader: new Ext.data.JsonReader( { 
	    	root: 'data'
	    	,fields: [ {name: 'attachment_id'},{name: 'path'},{name:'type'},{name:'size'}]
	    })
		,baseParams: {auth:_AUTH_KEY}
	})
	//for available functions
	,ow_fileStore: new Ext.data.Store({
		//restful:true
		 url: '/online_withdrawal/getTopicAttachments'
	    ,reader: new Ext.data.JsonReader( { 
	    	root: 'data'
	    	,fields: [ {name: 'attachment_id'},{name: 'path'},{name:'type'},{name:'size'}]
	    })
		,baseParams: {auth:_AUTH_KEY}
	})
	//Transaction Charges List
	,transchargeStore: new Ext.data.Store({
		//restful: true
	    url: '/transaction_charges/read'
	    ,reader: pecaReaders.transchargeReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//MDAS Store
	,mdasStore: new Ext.data.ArrayStore({
		// reader configs
		idIndex: 0
		,fields: ['operator']
		,data: [
            ['x']
            ,['/']
            ,['+']
            ,['-']
        ]         
	})
	//GL Entries Detail
	,glDtlStore: new Ext.data.Store({
		//restful:true
	    //,url: '/gl_entries/readDtl'
		proxy: glDtlProxy
	    ,reader: pecaReaders.glDtlReader
	    ,writer: glDtlWriter
	    ,baseParams: {auth:_AUTH_KEY, user:_USER_ID}
		,autoSave: true
		,listeners:{
			'save':{
				scope:this
				,fn:function(store, batch, data) {
					pecaDataStores.glDtlStore.reload();
				}
			}
		}
	})
	//Transaction Fields Store
	,transfieldsStore: new Ext.data.Store({
		//restful:true
	    url: '/gl_entries/readFields'
    	,baseParams: {auth:_AUTH_KEY}
	    ,reader: new Ext.data.ArrayReader( { 
	    	root: 'data'
    	},[{name:'fields', type:'string'}])
	})
	//Transaction Fields Store
	,glTransfieldsStore: new Ext.data.Store({
		//restful:true
	    url: '/gl_entries/glReadFields'
    	,baseParams: {auth:_AUTH_KEY}
	    ,reader: new Ext.data.ArrayReader( { 
	    	root: 'data'
    	},[{name:'fields', type:'string'}])
	})
	//Loan Code Header Store
	,loancodeHdrStore: new Ext.data.Store({
		url: '/loan_code/readHdr'
	    ,reader: pecaReaders.loancodeHdrReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//Loan Code Detail Store
	,loancodeDtlStore: new Ext.data.Store({
		//restful:true
		//,url: '/gl_entries/readDtl'
		proxy: loancodeDtlProxy
	    ,reader: pecaReaders.loancodeDtlReader
	    ,writer: loancodeDtlWriter
	    ,baseParams: {auth:_AUTH_KEY, user:_USER_ID}
		,autoSave: true
		,listeners:{
			'save':{
				scope:this
				,fn:function(store, batch, data) {
					pecaDataStores.loancodeDtlStore.reload();
				}
			}
		}
	})
	//for Payment Code drop down in Loan Code 
	,loanLPStore: new Ext.data.Store({
		//restful: true
	    url: '/transaction_code/readFilter'
	    ,reader: pecaReaders.transcodeReader
	    ,baseParams: {auth:_AUTH_KEY, filter:'LP'}  //Transaction group = Loan Payments
	})
	//for Transaction Code drop down in Loan Code 
	,loanLNStore: new Ext.data.Store({
		//restful: true
	    url: '/transaction_code/readFilter'
	    ,reader: pecaReaders.transcodeReader
	    ,baseParams: {auth:_AUTH_KEY, filter:'LN'}  //Transaction group = Loans
	})

	//for Payment Code List in Loan Code 
	,loancodePCStore: new Ext.data.Store({
		//restful:true
		//,url: '/gl_entries/readDtl'
		proxy: loancodePCProxy
	    ,reader: pecaReaders.loancodePCReader
	    ,writer: loancodeDtlWriter
	    ,baseParams: {auth:_AUTH_KEY, user:_USER_ID}
		,autoSave: false
		,listeners:{
			'save':{
				scope:this
				,fn:function(store, batch, data) {
					pecaDataStores.loancodePCStore.reload();
				}
			}
		}
	})
	//Capital Contribution List
	,capconStore: new Ext.data.Store({
    	proxy: new Ext.data.HttpProxy({
    		url: '/capital_transaction/read'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
    					params.transNo = Ext.getCmp('id_transNo').getValue();
					}
				}
			}
		})		
	    ,reader: pecaReaders.capconReader
	    ,baseParams: {auth:_AUTH_KEY}
	})

	//Capcon - Employee List
	,capconEmployeeStore: new Ext.data.Store({
    	proxy: new Ext.data.HttpProxy({
    		url: '/member/read'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						params.employee_id = Ext.getCmp('capconDetail').getForm().findField('capcon[employee_id]').getValue();
						params.first_name = Ext.getCmp('capconDetail').getForm().findField('first_name').getValue();
						params.last_name = Ext.getCmp('capconDetail').getForm().findField('last_name').getValue();
					}
				}
			}
		})	
	    ,reader: pecaReaders.employeeReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//New Loan - Employee Store
	,newloanemployeeStore: new Ext.data.Store({
		proxy: new Ext.data.HttpProxy({
			url: '/member/read'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						params.employee_id = Ext.getCmp('newloanDetail').getForm().findField('newloan[employee_id]').getValue();
						params.first_name = Ext.getCmp('newloanDetail').getForm().findField('first_name').getValue();
	        			params.last_name = Ext.getCmp('newloanDetail').getForm().findField('last_name').getValue();
					}
				}
			}
		})	
	    ,reader: pecaReaders.employeeReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//New Loan - CoMaker - Employee Store
	,newloanCoMakeremployeeStore: new Ext.data.Store({
		proxy: new Ext.data.HttpProxy({
			url: '/member/readAllowedComakers'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						params.loan_no = Ext.getCmp('newloanDetail').getForm().findField('newloan[loan_no]').getValue();
						params.employee_id = Ext.getCmp('newloanDetail').getForm().findField('newloan[employee_id]').getValue();
						params.comaker_id = Ext.getCmp('newloanCoMaker').findById('newloanCoMakerID').getValue();
						params.first_name = Ext.getCmp('newloanCoMaker').findById('newloanCoMakerFirstName').getValue();
						params.last_name = Ext.getCmp('newloanCoMaker').findById('newloanCoMakerLastName').getValue();
					}
				}
			}
		})	
	    ,reader: pecaReaders.employeeReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//New Loan - CoMaker - Employee Store 2
	,newloanCoMakeremployeeStore2: new Ext.data.Store({
		proxy: new Ext.data.HttpProxy({
			url: '/member/readAllowedComakersForMembership'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						params.loan_no = Ext.getCmp('member_LoanDetails').getForm().findField('loan_no').getValue();
						params.employee_id = Ext.getCmp('memberDetail').getForm().findField('member[employee_id]').getValue();
						params.comaker_id = Ext.getCmp('newmemberCoMakerID').getValue();
						params.first_name = Ext.getCmp('newmemberCoMakerFirstName').getValue();
						params.last_name = Ext.getCmp('newmemberCoMakerLastName').getValue();
					}
				}
			}
		})	
	    ,reader: pecaReaders.employeeReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//Capital Contribution Detail List
	,capconDtlStore: new Ext.data.Store({
	    proxy: capconDtlProxy
	    ,reader: pecaReaders.capconDtlReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//New Loan List
	,newloanStore: new Ext.data.Store({
    	proxy: new Ext.data.HttpProxy({
    		url: '/loan/readLoan'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						params.loan_no = Ext.getCmp('newloan_loan_no').getValue();
					}
				}
			}
		})	
	    ,reader: pecaReaders.newloanReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//for Transaction Code drop down in Loan Code 
	,transcodeCCStore: new Ext.data.Store({
		//restful: true
		url: '/transaction_code/readFilter'
		,reader: pecaReaders.transcodeReader
		,baseParams: {auth:_AUTH_KEY, filter:'CC'}  //Transaction group = Capital Contribution
	})

	//for Dividend code dropdown
	,divStore: new Ext.data.Store({
		//restful:true
	    url: '/dividend/getDivCodes'
	    ,reader: new Ext.data.JsonReader( { 
	    	root: 'data'
	    	,fields: [ {name: 'code'},{name: 'name'}]
	    })
		,baseParams: {auth:_AUTH_KEY}
	})
	//Payroll Deduction
	,pdStore: new Ext.data.Store({
	    
		proxy: new Ext.data.HttpProxy({
			url: '/payroll_deduction/read'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						params.employee_id = Ext.getCmp('pd_employeeId').getValue();
						params.last_name = Ext.getCmp('pd_last').getValue();
						params.first_name = Ext.getCmp('pd_first').getValue();
					}
				}
			}
		})		
		,reader: pecaReaders.pdReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//for Transaction Type drop down in Payroll Deduction 
	,ttypeStore: new Ext.data.Store({
		//restful: true
	    url: '/payroll_deduction/readPDTransactionType'
	    ,reader: pecaReaders.ttypeReader
	    ,baseParams: {auth:_AUTH_KEY} 
	})
	
	//ISOP List
	,isopStore: new Ext.data.Store({
		//restful: true
	    proxy: new Ext.data.HttpProxy({
    		url: '/isop/read'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						params.employee_id = Ext.getCmp('isopEmpId').getValue();
						params.first_name = Ext.getCmp('isopFirstname').getValue();
						params.last_name = Ext.getCmp('isopLastname').getValue();
					}
				}
			}
		})	
	    ,reader: pecaReaders.isopReader
	    ,baseParams: {auth:_AUTH_KEY}
	})

	//Loan Payment Header List
	,lpHdrStore: new Ext.data.Store({
		//restful: true
	    url: '/loan_payment/showHdr'
	    ,reader: pecaReaders.lpHdrReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	
	//Loan Payment Detail List
	,lpStore: new Ext.data.Store({
		//restful: true
	    //url: '/loan_payment/readHdr'
		proxy: new Ext.data.HttpProxy({
			url: '/loan_payment/readHdr'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						params.loan_no = Ext.getCmp('lp_loan_no').getValue();
						params.employee_id = Ext.getCmp('lp_id').getValue();
						params.last_name = Ext.getCmp('lp_lastname').getValue();
						params.first_name = Ext.getCmp('lp_firstname').getValue();
					}
				}
			}
		})
	    ,reader: pecaReaders.lpReader
	    ,baseParams: {auth:_AUTH_KEY}
	})

	//Loan Payment Payor List
	,lpPayorStore: new Ext.data.Store({
		//restful: true
	    url: '/loan_payment/readLoanPayor'
	    ,reader: pecaReaders.lpPayorReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//For Loan Payment Type DropDown
	,lpTypeStore: new Ext.data.Store({
		//restful: true
	    url: '/loan_payment/readLoanPaymentType'
	    ,reader: pecaReaders.lpTypeReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//Loan List
	,lpLoanListStore: new Ext.data.Store({
		//restful: true
    	proxy: new Ext.data.HttpProxy({
		    url: '/loan_payment/readLoanList'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						params.loan_no = Ext.getCmp('lpDetail').getForm().findField('lp[loan_no]').getValue();
						params.employee_id = Ext.getCmp('lpDetail').getForm().findField('employee_id').getValue();
						params.last_name = Ext.getCmp('lpDetail').getForm().findField('last_name').getValue();
						params.first_name = Ext.getCmp('lpDetail').getForm().findField('first_name').getValue();
					}
				}
			}
		})
	    ,reader: pecaReaders.lpLoanListReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//Loan List With Employees
	,lpLoanListWithEmployeeStore: new Ext.data.Store({
    	proxy: new Ext.data.HttpProxy({
			url: '/loan_payment/readEmployeeLoanList'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						params.loan_no = Ext.getCmp('lpDetail').getForm().findField('lp[loan_no]').getValue();
						params.employee_id = Ext.getCmp('lpDetail').getForm().findField('employee_id').getValue();
						params.last_name = Ext.getCmp('lpDetail').getForm().findField('last_name').getValue();
						params.first_name = Ext.getCmp('lpDetail').getForm().findField('first_name').getValue();
					}
				}
			}
		})
	    ,reader: pecaReaders.lpLoanListReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//Loan Payment Other Charges List
	,lpChargesStore: new Ext.data.Store({
		//restful: true
	    url: '/loan_payment/readCharges'
	    ,reader: pecaReaders.lpChargesReader
	    ,baseParams: {auth:_AUTH_KEY}
	})	
	//Employee With Loan List
	,employeeWithLoanStore: new Ext.data.Store({
    	proxy: new Ext.data.HttpProxy({
		    url: '/loan_payment/readEmployeesWithLoan'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						params.loan_no = Ext.getCmp('lpDetail').getForm().findField('lp[loan_no]').getValue();
						params.employee_id = Ext.getCmp('lpDetail').getForm().findField('employee_id').getValue();
						params.last_name = Ext.getCmp('lpDetail').getForm().findField('last_name').getValue();
						params.first_name = Ext.getCmp('lpDetail').getForm().findField('first_name').getValue();
					}
				}
			}
		})
	    ,reader: pecaReaders.employeeWithLoanReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//Investment
	,investmentStore: new Ext.data.Store({ 
		proxy: new Ext.data.HttpProxy({
			url: '/investment/read'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						params.investment_no = Ext.getCmp('investment_no').getValue();
					}
				}
			}
		})
		,reader: pecaReaders.investmentReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//Investment Maturity
	,imStore: new Ext.data.Store({
		proxy: new Ext.data.HttpProxy({
			url: '/investment_maturity/read'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						params.investment_no = Ext.getCmp('inv_maturity_no').getValue();
					}
				}
			}
		})		
	    ,reader: pecaReaders.imReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//Journal Entry Store
	,journalStore: new Ext.data.Store({
		//restful: true
		proxy: new Ext.data.HttpProxy({
			url: '/journal_entry/readHdr'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						params.journal_no = Ext.getCmp('journal_no').getValue();
					}
				}
			}
		})
	    ,reader: pecaReaders.journalReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	
	//Supplier List Store
	,supplierStore: new Ext.data.Store({
		//restful: true
	    url: '/supplier/readHdr'
	    ,reader: pecaReaders.supplierReader
	    ,baseParams: {auth:_AUTH_KEY}
	})	
	//Supplier Header List Store
	,supplierHdrStore: new Ext.data.Store({
		//restful: true
		proxy: new Ext.data.HttpProxy({
			url: '/supplier/readHdr'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						params.supplier_id = Ext.getCmp('supplier_id').getValue();
					}
				}
			}
		})
	    ,reader: pecaReaders.supplierReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//Account List Store
	,accountStore: new Ext.data.Store({
		//restful: true
	    url: '/chart_of_accounts/read'
	    ,reader: pecaReaders.accountReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//Account List Store for Acct Summary Report
	,accountStoreASR: new Ext.data.Store({
		//restful: true
	    url: '/chart_of_accounts/read'
	    ,reader: pecaReaders.accountReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	
	//Entry Type Store
	,entryStore: new Ext.data.Store({
		//restful: true
	    url: '/gl_entries/readHdr'
	    ,reader: pecaReaders.entryReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//Entry Accounts Store
	,debitCreditStore: new Ext.data.Store({
		//restful: true
	    //url: '/journal_entry/readDtl'
		proxy: journalDtlProxy
	    ,reader: pecaReaders.debitCreditReader
	    ,baseParams: {auth:_AUTH_KEY}
		,writer: journalDtlWriter
		,autoSave: false
	})
	//for Investment Type drop down in Investment 
	,itypeStore: new Ext.data.Store({
		//restful: true
	    url: '/investment/readInvestmentTypeList'
	    ,reader: pecaReaders.itypeReader
	    ,baseParams: {auth:_AUTH_KEY} 
	})

	//for Transaction Code drop down in adjustments
	,transcodeAdjStore: new Ext.data.Store({
		//restful: true
		url: '/transaction_code/readFilter'
		,reader: pecaReaders.transcodeReader
//		,baseParams: {auth:_AUTH_KEY, filter:'CC'}  //Transaction group = Capital Contribution
	})

//for Transaction Code drop down in adjustments
	,AdjustmentStore:  new Ext.data.Store({
		proxy: new Ext.data.HttpProxy({
			url: '/adjustment/read'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params){
						var frm = Ext.getCmp('adjustment').getForm();
    					params.transGrp = frm.findField('adjustment[transGrp]').getValue();
    					params.transCode = frm.findField('adjustment[transCode]').getValue();
    					params.empid = frm.findField('adjustment[empid]').getValue();
    					params.fromAmt = frm.findField('adjustment[fromAmt]').getValue();
    					params.toAmt = frm.findField('adjustment[toAmt]').getValue();
					}
				}
			}
		})		
		,reader: new Ext.data.JsonReader({
		    totalProperty: 'total',
		    root: 'data'
		    },
	    	[{name: 'transaction_no', mapping: 'transaction_no', type: 'float'}
	    	,{name: 'transaction_type', mapping: 'transaction_description', type: 'string'}
	    	,{name: 'employee_id', mapping: 'employee_id', type: 'string'}
	    	,{name: 'employee_name', mapping: 'employee_name', type: 'string'}
	    	,{name: 'amount', mapping: 'transaction_amount', type: 'float'}
	    	,{name: 'transaction_group', mapping: 'transaction_group', type: 'string'}
	    	]
		)
			,baseParams: {auth:_AUTH_KEY} 
	})

	//Employee List in Payroll Deduction
	,pdEmployeeStore: new Ext.data.Store({
    	proxy: new Ext.data.HttpProxy({
    		url: '/member/read'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						params.employee_id = Ext.getCmp('pdDetail').getForm().findField('newpayroll[employee_id]').getValue();
						params.first_name = Ext.getCmp('pdDetail').getForm().findField('first_name').getValue();
						params.last_name = Ext.getCmp('pdDetail').getForm().findField('last_name').getValue();
					}
				}
			}
		})	
	    ,reader: pecaReaders.employeeReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//Employee List in Payroll Deduction
	,isopEmployeeStore: new Ext.data.Store({
    	proxy: new Ext.data.HttpProxy({
    		url: '/member/read'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						params.employee_id = Ext.getCmp('isopDetail').getForm().findField('isop[employee_id]').getValue();
						params.first_name = Ext.getCmp('isopDetail').getForm().findField('isop[first_name]').getValue();
						params.last_name = Ext.getCmp('isopDetail').getForm().findField('isop[last_name]').getValue();
					}
				}
			}
		})	
	    ,reader: pecaReaders.employeeReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//Employee List in CapCon Statement of Account Reports
	,capconStmtOfAcctReportEmployeeStore: new Ext.data.Store({
    	proxy: new Ext.data.HttpProxy({
    		url: '/member/readAll'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						params.employee_id = Ext.getCmp('rpt_csoa').getForm().findField('employee_id').getValue();
						params.first_name = Ext.getCmp('rpt_csoa').getForm().findField('first_name').getValue();
						params.last_name = Ext.getCmp('rpt_csoa').getForm().findField('last_name').getValue();
						params.middle_name = Ext.getCmp('rpt_csoa').getForm().findField('middle_name').getValue();
					}
				}
			}
		})	
	    ,reader: pecaReaders.employeeReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//New Loan - Loan Type drop down
	,newloanLTStore: new Ext.data.Store({
		url: '/loan_code/readLoanCodes'
	    ,reader: pecaReaders.newloanLTReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//New Loan - Restructure Loan List
	,newloanRLStore: new Ext.data.Store({
		proxy: new Ext.data.HttpProxy({
    		url: '/loan/showRestructuredLoans'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						params.employee_id = Ext.getCmp('newloanDetail').getForm().findField('newloan[employee_id]').getValue();
						params.loan_code = Ext.getCmp('newloanDetail').getForm().findField('newloan[loan_code]').getValue();
					}
				}
			}
		})	
		,reader: pecaReaders.newloanRLReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//New Loan - Other Charges List
	,newloanOtherChargesStore: new Ext.data.Store({
	    proxy: newloanOtherChargesProxy
	    ,writer: newloanOtherChargesWriter
	    ,reader: pecaReaders.newloanOtherChargesReader
	    ,baseParams: {auth:_AUTH_KEY, user:_USER_ID}
	})
	//New Loan - Other Charges drop down
	,newloanOTCmboxStore: new Ext.data.Store({
	    url: '/transaction_charges/readChargeCode'
	    ,reader: pecaReaders.transchargeReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//for Investment Type drop down in Investment 
	,mctypeStore: new Ext.data.Store({
		//restful: true
	    url: '/investment_maturity/readMaturityCodeList'
	    ,reader: pecaReaders.mctypeReader
	    ,baseParams: {auth:_AUTH_KEY} 
	})
	//New Loan - CoMaker List
	,newloanCoMakerStore: new Ext.data.Store({
	    proxy: newloanCoMakerProxy
	    ,writer: newloanCoMakerWriter
	    ,reader: pecaReaders.employeeReader
	    ,baseParams: {auth:_AUTH_KEY, user:_USER_ID}
		,autoSave: false
	})
	//Workflow
	,workflowStore: new Ext.data.Store({
	    url: '/workflow/read'
	    ,reader: pecaReaders.workflowReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//Approver (Workflow)
	,wApproverStore: new Ext.data.Store({
	    url: '/workflow/readApprovers'
	    ,reader: pecaReaders.wApproverReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//Approver (Workflow)
	,wRequestStore: new Ext.data.Store({
	    url: '/workflow/readRequests'
	    ,reader: pecaReaders.wRequestReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//Online Loan
	,ol_loanStore: new Ext.data.Store({	    
		proxy: new Ext.data.HttpProxy({
			url: '/online_loan/read'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						//params.employee_id = Ext.getCmp('memberEmpId').getValue();
						params.loan_date_from = Ext.getCmp('ol_loanFilter').findById('ol_from').getValue();
						params.loan_date_to = Ext.getCmp('ol_loanFilter').findById('ol_to').getValue();
						params.loan_code = Ext.getCmp('ol_loanFilter').findById('ol_transactionType').getValue();
						params.status = Ext.getCmp('ol_loanFilter').findById('ol_stat').getValue();
						params.employee_id = Ext.getCmp('ol_loanFilter').findById('ol_employee_id').getValue();
						params.last_name = Ext.getCmp('ol_loanFilter').findById('ol_last').getValue();
						params.first_name = Ext.getCmp('ol_loanFilter').findById('ol_first').getValue();
					}
				}
			}
		})
	    ,reader: pecaReaders.ol_loanReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//Loan Type (Online Loan)
	,ol_loantypeStore: new Ext.data.Store({
	    url: '/online_loan/readLoanTypes'
	    ,reader: pecaReaders.ol_loantypeReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//Online Withdrawal
	,ol_withdrawalStore: new Ext.data.Store({
		proxy: new Ext.data.HttpProxy({
			url: '/online_withdrawal/readHeader'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						params.submission_date_from = Ext.getCmp('ol_withdrawalFilter').findById('ow_from').getValue();
						params.submission_date_to = Ext.getCmp('ol_withdrawalFilter').findById('ow_to').getValue();
						params.transaction_code = Ext.getCmp('ol_withdrawalFilter').findById('ow_transactionType').getValue();
						params.status = Ext.getCmp('ol_withdrawalFilter').findById('ow_stat').getValue();
						params.employee_id = Ext.getCmp('ol_withdrawalFilter').findById('ow_employee_id').getValue();
						params.last_name = Ext.getCmp('ol_withdrawalFilter').findById('ow_last').getValue();
						params.first_name = Ext.getCmp('ol_withdrawalFilter').findById('ow_first').getValue();
						params.or_no = Ext.getCmp('ol_withdrawalFilter').findById('ow_or_no').getValue();
					}
				}
			}
		})
	    ,reader: pecaReaders.ol_withdrawalReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//Transaction Type (Online Withdrawal)
	,ow_transactiontypeStore: new Ext.data.Store({
	    url: '/online_withdrawal/readTransactionTypes'
	    ,reader: pecaReaders.ow_transactiontypeReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//Online Membership
	,ol_membershipStore: new Ext.data.Store({
		proxy: new Ext.data.HttpProxy({
			url: '/online_member/readHeader'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						params.request_date_from = Ext.getCmp('ol_membershipFilter').findById('om_from').getValue();
						params.request_date_to = Ext.getCmp('ol_membershipFilter').findById('om_to').getValue();
						params.status = Ext.getCmp('ol_membershipFilter').findById('om_stat').getValue();
						params.employee_id = Ext.getCmp('ol_membershipFilter').findById('om_employee_id').getValue();
						params.last_name = Ext.getCmp('ol_membershipFilter').findById('om_last').getValue();
						params.first_name = Ext.getCmp('ol_membershipFilter').findById('om_first').getValue();
					}
				}
			}
		})
	    ,reader: pecaReaders.ol_membershipReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//Member List
	,memberStore: new Ext.data.Store({
		//restful: true
		proxy: new Ext.data.HttpProxy({
			url: '/membership/read'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						params.employee_id = Ext.getCmp('memberEmpId').getValue();
						params.first_name = Ext.getCmp('memberFirstname').getValue();
						params.last_name = Ext.getCmp('memberLastname').getValue();
					}
				}
			}
		})
	    ,reader: pecaReaders.memberReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//member loan list
	,memberLoanInfoStore: new Ext.data.Store({
		//restful: true
		proxy: new Ext.data.HttpProxy({
			url: '/membership/readEmployeeLoanList'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						if(Ext.getCmp('memberDetail').getForm().findField('member[employee_id]').getValue())
							params.employee_id = Ext.getCmp('memberDetail').getForm().findField('member[employee_id]').getValue();
						else
							params.employee_id = '';
					}
				}
			}
		})
	    ,reader: pecaReaders.memberLoanInfoReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//online member loan list
	,ol_memberLoanInfoStore: new Ext.data.Store({
		//restful: true
		proxy: new Ext.data.HttpProxy({
			url: '/membership/readEmployeeLoanList'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						if(Ext.getCmp('ol_membershipDetail').getForm().findField('online_membership[employee_id]').getValue())
							params.employee_id = Ext.getCmp('ol_membershipDetail').getForm().findField('online_membership[employee_id]').getValue();
						else
							params.employee_id = '';
					}
				}
			}
		})
	    ,reader: pecaReaders.memberLoanInfoReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//online member loan list for member info tab
	,ol_memberLoanInfoStore2: new Ext.data.Store({
		//restful: true
		proxy: new Ext.data.HttpProxy({
			url: '/membership/readEmployeeLoanList'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						if(Ext.getCmp('ol_membershipInfo').getForm().findField('online_membership[employee_id]').getValue())
							params.employee_id = Ext.getCmp('ol_membershipInfo').getForm().findField('online_membership[employee_id]').getValue();
						else
							params.employee_id = '';
					}
				}
			}
		})
	    ,reader: pecaReaders.memberLoanInfoReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//member loan list sum
	,memberLoanInfoSumStore: new Ext.data.Store({
		//restful: true
		proxy: new Ext.data.HttpProxy({
			url: '/membership/readEmployeeLoanListSum'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						if(Ext.getCmp('memberDetail').getForm().findField('member[employee_id]').getValue())
							params.employee_id = Ext.getCmp('memberDetail').getForm().findField('member[employee_id]').getValue();
					}
				}
			}
		})
	    ,reader: pecaReaders.memberLoanInfoSumReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//member guaranteed loans list
	,guaranteedLoansStore: new Ext.data.Store({
		//restful: true
	    proxy: new Ext.data.HttpProxy({
			url: '/membership/readEmployeeGuaranteedLoans'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						if(Ext.getCmp('memberDetail').getForm().findField('member[employee_id]').getValue())
							params.employee_id = Ext.getCmp('memberDetail').getForm().findField('member[employee_id]').getValue();
						else
							params.employee_id = '';
					}
				}
			}
		})
	    ,reader: pecaReaders.guaranteedLoansReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//online member guaranteed loans list
	,ol_guaranteedLoansStore: new Ext.data.Store({
		//restful: true
	    proxy: new Ext.data.HttpProxy({
			url: '/membership/readEmployeeGuaranteedLoans'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						if(Ext.getCmp('ol_membershipDetail').getForm().findField('online_membership[employee_id]').getValue())
							params.employee_id = Ext.getCmp('ol_membershipDetail').getForm().findField('online_membership[employee_id]').getValue();
						else
							params.employee_id = '';
					}
				}
			}
		})
	    ,reader: pecaReaders.guaranteedLoansReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//online member guaranteed loans list for membership info tab
	,ol_guaranteedLoansStore2: new Ext.data.Store({
		//restful: true
	    proxy: new Ext.data.HttpProxy({
			url: '/membership/readEmployeeGuaranteedLoans'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						if(Ext.getCmp('ol_membershipInfo').getForm().findField('online_membership[employee_id]').getValue())
							params.employee_id = Ext.getCmp('ol_membershipInfo').getForm().findField('online_membership[employee_id]').getValue();
						else
							params.employee_id = '';
					}
				}
			}
		})
	    ,reader: pecaReaders.guaranteedLoansReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	//member guaranteed loans list sum
	,guaranteedLoansSumStore: new Ext.data.Store({
		//restful: true
	    proxy: new Ext.data.HttpProxy({
			url: '/membership/readEmployeeGuaranteedLoansSum'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						if(Ext.getCmp('memberDetail').getForm().findField('member[employee_id]').getValue())
							params.employee_id = Ext.getCmp('memberDetail').getForm().findField('member[employee_id]').getValue();
					}
				}
			}
		})
	    ,reader: pecaReaders.memberLoanInfoSumReader
	    ,baseParams: {auth:_AUTH_KEY}
	})	
	//member transaction history list
	,transactionHistoryStore: new Ext.data.Store({
		//restful: true
	    proxy: new Ext.data.HttpProxy({
			url: '/membership/readTransHistory'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						if(Ext.getCmp('memberDetail').getForm().findField('member[employee_id]').getValue()){
							params.employee_id = Ext.getCmp('memberDetail').getForm().findField('member[employee_id]').getValue();
						}
						else
							params.employee_id = '';
					}
				}
			}
		})
	    ,reader: pecaReaders.transactionHistoryReader
	    ,baseParams: {auth:_AUTH_KEY}
	})	
	
	//member transaction history list in descending order
	,transactionHistoryStoreDesc: new Ext.data.Store({
		//restful: true
	    proxy: new Ext.data.HttpProxy({
			url: '/membership/readTransHistoryDesc'
			,timeout: 120000 
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						if(Ext.getCmp('memberDetail').getForm().findField('member[employee_id]').getValue()){
							params.employee_id = Ext.getCmp('memberDetail').getForm().findField('member[employee_id]').getValue();
						}
						else
							params.employee_id = '';
					}
				}
			}
		})
	    ,reader: pecaReaders.transactionHistoryReader
	    ,baseParams: {auth:_AUTH_KEY}
	})	
	
	//online member transaction history list
	,ol_transactionHistoryStore: new Ext.data.Store({
		//restful: true
	    proxy: new Ext.data.HttpProxy({
			url: '/membership/readTransHistory'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						if(Ext.getCmp('ol_membershipDetail').getForm().findField('online_membership[employee_id]').getValue()){
							params.employee_id = Ext.getCmp('ol_membershipDetail').getForm().findField('online_membership[employee_id]').getValue();
						}
						else
							params.employee_id = '';
					}
				}
			}
		})
	    ,reader: pecaReaders.transactionHistoryReader
	    ,baseParams: {auth:_AUTH_KEY}
	})	
	//online member transaction history list for membership info tab
	,ol_transactionHistoryStore2: new Ext.data.Store({
		//restful: true
	    proxy: new Ext.data.HttpProxy({
			url: '/membership/readTransHistoryDesc'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						if(Ext.getCmp('ol_membershipInfo').getForm().findField('online_membership[employee_id]').getValue()){
							params.employee_id = Ext.getCmp('ol_membershipInfo').getForm().findField('online_membership[employee_id]').getValue();
						}
						else
							params.employee_id = '';
					}
				}
			}
		})
	    ,reader: pecaReaders.transactionHistoryReader
	    ,baseParams: {auth:_AUTH_KEY}
	})	
	,supplierInfoStore: new Ext.data.Store({
		//restful: true
		proxy: new Ext.data.HttpProxy({
			url: '/supplier/readDtl'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						params.supplier_id = Ext.getCmp('supplierHdr[supplier_id]').getValue();
					}
				}
			}
		})
	    ,reader: pecaReaders.supplierInfoReader
	    ,baseParams: {auth:_AUTH_KEY}
	})	
	
	,onlinePayrollDeductionStore: new Ext.data.Store({
		//restful: true
		proxy: new Ext.data.HttpProxy({
			url: '/online_payroll_deduction/read'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						//params.employee_id = Ext.getCmp('supplierHdr[supplier_id]').getValue();
						params.transaction_date_from = Ext.getCmp('ol_payroll_from').getRawValue();
						params.transaction_date_to = Ext.getCmp('ol_payroll_to').getRawValue();
						params.status = Ext.getCmp('ol_payroll_status').getValue();
						params.employee_id = _IS_ADMIN?Ext.getCmp('ol_payroll_id').getValue():_EMP_ID;
						params.last_name = Ext.getCmp('ol_payroll_lastname').getValue();
						params.first_name = Ext.getCmp('ol_payroll_firstname').getValue();
					}
				}
			}
		})
	    ,reader: pecaReaders.onlinePayrollDeductionReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	,onlineLoanPaymentStore: new Ext.data.Store({
		//restful: true
		proxy: new Ext.data.HttpProxy({
			url: '/online_loan_payment/read'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						//params.employee_id = Ext.getCmp('supplierHdr[supplier_id]').getValue();
						params.transaction_date_from = Ext.getCmp('ol_loanpayment_from').getRawValue();
						params.transaction_date_to = Ext.getCmp('ol_loanpayment_to').getRawValue();
						params.transaction_code = Ext.getCmp('ol_loanpayment_ttype').getValue();
						params.status = Ext.getCmp('ol_loanpayment_status').getValue();
						params.employee_id = _IS_ADMIN?Ext.getCmp('ol_loanpayment_id').getValue():_EMP_ID;
						params.last_name = Ext.getCmp('ol_loanpayment_lastname').getValue();
						params.first_name = Ext.getCmp('ol_loanpayment_firstname').getValue();
						params.or_no = Ext.getCmp('ol_loanpayment_or_no').getValue();
					}
				}
			}
		})
	    ,reader: pecaReaders.onlineLoanPaymentReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	,onlineLoanPaymentDetailStore: new Ext.data.Store({
			url: '/online_loan_payment/readEmployeeLoanList'
			,reader: pecaReaders.onlineLoanPaymentDetailReader
			,baseParams: {auth:_AUTH_KEY} 
		})	
	//for Transaction Code drop down in Loan Code - Report
	,rptPenaltyStore: new Ext.data.Store({
		//restful: true
	    url: '/transaction_code/readFilter'
	    ,reader: pecaReaders.transcodeReader
	    ,baseParams: {auth:_AUTH_KEY, filter:'LN'}  //Transaction group = Loans
	})
	// for Transaction Code drop down in Loan Payment Due - Report
	,rptPenaltyStore2: new Ext.data.Store({
		//restful: true
	    url: '/transaction_code/readFilterLPDueReport'
	    ,reader: pecaReaders.transcodeReader
	    ,baseParams: {auth:_AUTH_KEY, filter:'LN'}  //Transaction group = Loans
	})
	//for Transaction Code drop down in Loan Code - Report
	,rptSuspendedStore: new Ext.data.Store({
		//restful: true
	    url: '/transaction_code/readFilter'
	    ,reader: pecaReaders.transcodeReader
	    ,baseParams: {auth:_AUTH_KEY, filter:'LN'}  //Transaction group = Loans
	})
	//for Transaction Type drop down in Online Loan Payment
	,paymentTypeCC: new Ext.data.Store({
		//restful: true
	    url: '/online_loan_payment/readPaymentType'
	    ,reader: pecaReaders.ow_transactiontypeReader
	    ,baseParams: {auth:_AUTH_KEY}  //Transaction group = Loans
	})
	//User List for Daily Summary Reports
	,pecaUserStore: new Ext.data.Store({
		//restful: true
		proxy: new Ext.data.HttpProxy({
			url: '/user/read'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						//params.employee_id = Ext.getCmp('supplierHdr[supplier_id]').getValue();
					}
				}
			}
		})
	    ,reader: pecaReaders.pecaUserReader
	    ,baseParams: {auth:_AUTH_KEY}
	})
	
	//User List for Daily Summary Reports
	,pecaUserIsAdminStore: new Ext.data.Store({
		//restful: true
		proxy: new Ext.data.HttpProxy({
			url: '/user/readIsAdmin'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						//params.employee_id = Ext.getCmp('supplierHdr[supplier_id]').getValue();
					}
				}
			}
		})
	    ,reader: pecaReaders.pecaUserReader
	    ,baseParams: {auth:_AUTH_KEY}
	})

	
	
};