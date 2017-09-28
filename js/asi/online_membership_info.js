//Let's pretend we rendered our grid-columns with meta-data from our ORM framework.
var om_columnMemInfo={
	membershipLoanInfoColumnsMemInfo:  new Ext.grid.ColumnModel([
		{id:'loan_no', header: 'Loan Number', width: 75, sortable: true, align: 'right', dataIndex: 'loan_no'}
		,{header: 'Loan Code', width: 50, sortable: true, dataIndex: 'loan_code'}
		,{header: 'Loan Date', width: 75, sortable: true, align: 'center', dataIndex: 'loan_date'}
		,{header: 'Principal', width: 100, sortable: true, dataIndex: 'principal', align: 'right', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}}
		,{header: 'Term', width: 40, sortable: true, align: 'right', dataIndex: 'term'}
		,{header: 'Rate', width: 40, sortable: true, align: 'right', dataIndex: 'rate', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}, 
			summaryRenderer: function(v, params, data){
				return "Total"; }
			}
		,{header: 'Interest Amortization', width: 100, sortable: true, align: 'right', dataIndex: 'interest_amortization', 
			renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}
			, summaryRenderer: function(v, params, data){
				record = pecaDataStores.ol_memberLoanInfoStore2.queryBy(function(record,id){ 
					return parseFloat(record.get('principal_balance'))>0;
				}); 
				var sum_monthly = 0;
				record.each(function(item,index){ 
					sum_monthly += parseFloat(item.get('interest_amortization'));
				});
				return Ext.util.Format.number(sum_monthly,'0,000,000,000.00'); }
			}
		,{header: 'Principal Amortization', width: 100, sortable: true, align: 'right', dataIndex: 'principal_amortization', 
			renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}
			, summaryRenderer: function(v, params, data){
				record = pecaDataStores.ol_memberLoanInfoStore2.queryBy(function(record,id){ 
					return parseFloat(record.get('principal_balance'))>0;
				}); 
				var sum_monthly = 0;
				record.each(function(item,index){ 
					sum_monthly += parseFloat(item.get('principal_amortization'));
				});
				return Ext.util.Format.number(sum_monthly,'0,000,000,000.00'); }
			}
		,{header: 'Principal Balance', width: 100, sortable: true, align: 'right', dataIndex: 'principal_balance', 
			renderer:function(value,rec){
				return Ext.util.Format.number(value,'0,000,000,000.00');
			}, summaryType: 'sum'}
	])
	,membershipLoanPaymentsColumnsMemInfo: new Ext.grid.ColumnModel([
		{header: 'Payment Date', width: 50, sortable: true, align: 'center', dataIndex: 'payment_date'}
		/* ##### NRB EDIT START ##### */
		,{header: 'Principal Amortization', width: 75, sortable: true, dataIndex: 'amount', align: 'right', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}}
/*
		,{header: 'Amount', width: 75, sortable: true, dataIndex: 'amount', align: 'right', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}}
*/
		/* ##### NRB EDIT END ##### */
		,{header: 'Interest', width: 75, sortable: true, align: 'right', dataIndex: 'interest', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}}
		,{header: 'Description', width: 100, sortable: true, dataIndex: 'description'}
		,{header: 'Balance', width: 75, sortable: true, align: 'right', dataIndex: 'balance', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}}
	])
	,membershipChargesColumnsMemInfo: new Ext.grid.ColumnModel([
		{header: 'Description', width: 100, sortable: true, dataIndex: 'description'}
		,{header: 'Amount', width: 100, sortable: true, align: 'right', dataIndex: 'amount', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}}
	])
	,membershipCoMakersColumnsMemInfo: new Ext.grid.ColumnModel([
		{header: 'Employee ID', width: 50, sortable: true, dataIndex: 'employee_id'}
		,{header: 'Employee Name', width: 100, sortable: true, dataIndex: 'employee_name'}
	])
	,om_guaranteedLoansColumnsMemInfo: new Ext.grid.ColumnModel([
		{id:'loan_no', header: 'Loan Number', width: 75, sortable: true, align: 'right', dataIndex: 'loan_no'}
		,{header: 'Loan Code', width: 50, sortable: true, dataIndex: 'loan_code'}
		,{header: 'Loan Date', width: 75, sortable: true, align: 'center', dataIndex: 'loan_date'}
		,{header: 'Employee Name', width: 150, sortable: true, dataIndex: 'employee_name'}
		,{header: 'Principal', width: 100, sortable: true, align: 'right', dataIndex: 'principal', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}}
		,{header: 'Interest Amortization', width: 100, sortable: true, align: 'right', dataIndex: 'interest_amortization', 
			renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}
			, summaryRenderer: function(v, params, data){
				record = pecaDataStores.ol_guaranteedLoansStore2.queryBy(function(record,id){ 
					return parseFloat(record.get('principal_balance'))>0;
				}); 
				var sum_monthly = 0;
				record.each(function(item,index){ 
					sum_monthly += parseFloat(item.get('interest_amortization'));
				});
				return Ext.util.Format.number(sum_monthly,'0,000,000,000.00'); }
			}
		,{header: 'Principal Amortization', width: 100, sortable: true, align: 'right', dataIndex: 'principal_amortization', 
			renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}
			, summaryRenderer: function(v, params, data){
				record = pecaDataStores.ol_guaranteedLoansStore2.queryBy(function(record,id){ 
					return parseFloat(record.get('principal_balance'))>0;
				}); 
				var sum_monthly = 0;
				record.each(function(item,index){ 
					sum_monthly += parseFloat(item.get('principal_amortization'));
				});
				return Ext.util.Format.number(sum_monthly,'0,000,000,000.00'); }
			}
		,{header: 'Principal Balance', width: 100, sortable: true, align: 'right', dataIndex: 'principal_balance', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}, summaryType: 'sum'}
	])
	,om_transactionHistoryColumnsMemInfo:  new Ext.grid.ColumnModel([
		{header: 'Date', width: 50, sortable: false, align: 'center', dataIndex: 'transaction_date'}
		,{header: 'Code', width: 25, sortable: false, dataIndex: 'transaction_code'}
		,{header: 'Amount', width: 75, sortable: false, dataIndex: 'transaction_amount', align: 'right'}
		,{header: 'Balance', width: 75, sortable: false, align: 'right', dataIndex: 'balance'}
	])
};
var om_storeMemInfo={
	myStore: new Ext.data.SimpleStore({fields: ['name', 'value']})
	,om_beneficiaryStorageMemInfo: new Ext.data.Store({
		url: '/membership/readBeneficiary'
	    ,reader: new Ext.data.JsonReader({
			totalProperty: 'total'	
			,root: 'data'
			},[
				{name: 'beneficiary', mapping: 'beneficiary', type: 'string'}
				,{name: 'relationship', mapping: 'relationship', type: 'string'}
				,{name: 'beneficiary_address', mapping: 'beneficiary_address'}
				,{name: 'description', mapping: 'description'}
			]
		)
	    ,baseParams: {auth:_AUTH_KEY}
	})
	,statusStoreMemInfo: new Ext.data.SimpleStore({fields: ['value', 'name'], data: [['A', 'Active'],['I', 'Inactive']]})
	,beneficiaryStoreMemInfo: new Ext.data.SimpleStore({fields: ['initial', 'name'], data: [['S', 'Spouse'],['P', 'Parent'],['C', 'Child'],['O', 'Others']]})
	,membershipLoanPaymentsStoreMemInfo: new Ext.data.Store({
		proxy: new Ext.data.HttpProxy({
				url: '/membership/readLoanPayment'
				,listeners:{
					'beforeload':{
						scope:this
						,fn:function(dataproxy,params ){
							if(Ext.getCmp('membership_loan_info_loan_no').getValue())
								params.loan_no = Ext.getCmp('membership_loan_info_loan_no').getValue();
						}
					}
				}
		})
		,reader: new Ext.data.JsonReader({
			totalProperty: 'total'	
			,root: 'data'
			},[
				{name: 'payment_date', mapping: 'payment_date', type: 'date', convert:function(value,rec){
					return formatDate(value);}}
				,{name: 'amount', mapping: 'amount', type: 'float'}
				,{name: 'interest', mapping: 'interest', type: 'float'}
				,{name: 'description', mapping: 'transaction_description', type: 'string'}
				,{name: 'balance', mapping: 'balance', type: 'float'}
			]
		)
		,baseParams: {auth:_AUTH_KEY}
	})
	,membershipChargesStoreMemInfo: new Ext.data.Store({
		proxy: new Ext.data.HttpProxy({
				url: '/membership/readLoanCharges'
				,listeners:{
					'beforeload':{
						scope:this
						,fn:function(dataproxy,params ){
							if(Ext.getCmp('membership_loan_info_loan_no').getValue())
								params.loan_no = Ext.getCmp('membership_loan_info_loan_no').getValue();
						}
					}
				}
		})
		,reader: new Ext.data.JsonReader({
			totalProperty: 'total'	
			,root: 'data'
			},[
				{name: 'description', mapping: 'transaction_description', type: 'string'}
				,{name: 'amount', mapping: 'amount', type: 'string'}
			]
		)
		,baseParams: {auth:_AUTH_KEY}
	})
	,membershipCoMakersStoreMemInfo: new Ext.data.Store({
		proxy: new Ext.data.HttpProxy({
				url: '/membership/readLoanComakers'
				,listeners:{
					'beforeload':{
						scope:this
						,fn:function(dataproxy,params ){
							if(Ext.getCmp('membership_loan_info_loan_no').getValue())
								params.loan_no = Ext.getCmp('membership_loan_info_loan_no').getValue();
						}
					}
				}
		})
		,reader: new Ext.data.JsonReader({
			totalProperty: 'total'	
			,root: 'data'
			},[
				{name: 'employee_id', mapping: 'employee_id', type: 'string'}
				,{name: 'first_name', mapping: 'first_name', type: 'string'}
				,{name: 'middle_name', mapping: 'middle_name', type: 'string'}
				,{name: 'employee_name', mapping: 'last_name', type: 'string', convert:function(value,rec){
					return value + ', ' + rec.first_name + ' ' + rec.middle_name;
				}}
			]
		)
		,baseParams: {auth:_AUTH_KEY}
	})
	,om_genderStoreMemInfo: new Ext.data.SimpleStore({fields: ['initial', 'name'], data: [['M', 'Male'],['F', 'Female']]})	
	,om_civilstatusStoreMemInfo: new Ext.data.SimpleStore({fields: ['initial', 'name'], data: [['1', 'Single'],['2', 'Married'],['3', 'Separated'],['4', 'Widowed']]})
};
var om_functionMemInfo={
	/* ##### NRB EDIT START ##### */
	membershipLoanWinMemInfo: function(b_bsp_computation){
	/* membershipLoanWinMemInfo: function(){ */
	/* ##### NRB EDIT END ##### */
	
		return new Ext.Window({
			id: 'membershipLoanWinMemInfo'
			,title: 'Loan Information'
			,frame: true
			,layout: 'form'
			,width: 750
			,plain: true
			,modal: true
			,resizable: false
			,closable: true
			,defaults: {autoScroll: true}
			,constrainHeader: true
			,bodyStyle:{"padding":"5px"}
			,loadMask: true	
			/* ##### NRB EDIT START ##### */
			,items:[ membershipLoanDetailsMemInfo(b_bsp_computation) ]
			/* ,items:[ membershipLoanDetailsMemInfo() ] */
			/* ##### NRB EDIT END ##### */
		});
	}
	,loadValuesMemInfo: function(employee_id_parameter){
	
		for(var i=0; i<3; i++){
			var foo = om_storeMemInfo.om_beneficiaryStorageMemInfo.getAt(i);
			var j=i+1;
			if(foo!=null){
				Ext.getCmp('ol_membershipInfo').getForm().findField('online_membership[beneficiary_name'+j+']').setValue(foo.get('beneficiary'));
				Ext.getCmp('ol_membershipInfo').getForm().findField('online_membership[beneficiary_relationship'+j+']').setValue(foo.get('relationship'));
				Ext.getCmp('ol_membershipInfo').getForm().findField('online_membership[beneficiary_address'+j+']').setValue(foo.get('beneficiary_address'));
			}
		}
		pecaDataStores.ol_memberLoanInfoStore2.load({params: { employee_id :_EMP_ID, start:0, limit:MAX_PAGE_SIZE}});
		pecaDataStores.ol_guaranteedLoansStore2.load({params: { employee_id : _EMP_ID,start:0, limit:MAX_PAGE_SIZE}});
		pecaDataStores.ol_transactionHistoryStore2.load({params: { employee_id :_EMP_ID,start:0, limit:MAX_PAGE_SIZE}});
		pecaDataStores.companyStoreOLM.load();
		
		if(employee_id_parameter){
			var emp_id = employee_id_parameter;
		}
		else{
			var emp_id = _EMP_ID;
		}
		
		Ext.Ajax.request({
			//jdj 08072017 -- change to same function of membership.js
			//url: '/membership/showBalanceInfo/'
			url: '/membership/showMembershipInfoInTransHist/'
			,params: {'member[employee_id]': emp_id, auth:_AUTH_KEY}  
			,success: function(response, opts) {
				var ret = Ext.decode(response.responseText);
				Ext.getCmp('membershipMemInfo[capital_contribution]').setValue(Ext.util.Format.number(Ext.num(ret.data.capconBal,0),'0,000,000,000.00'));
				Ext.getCmp('membershipMemInfo[required_balance]').setValue(Ext.util.Format.number(Ext.num(ret.data.reqBal,0),'0,000,000,000.00'));
				Ext.getCmp('membershipMemInfo[maximum_withdrawable_amount]').setValue(Ext.util.Format.number(Ext.num(ret.data.maxWdwlAmount,0),'0,000,000,000.00'));
			}
			,failure: function(response, opts) {}
		});
	}
};

var ol_membershipInfo = function(){
	return {
		xtype:'form'
		,id:'ol_membershipInfo'
		,region:'center'
		,title: 'Details'
		,anchor: '100%'
		,frame: true
		,reader: pecaReaders.ol_membershipReader
		,items: [{			
			layout: 'form'
			,id:'ol_membershipInfo_'
			,region:'center'
			,style: 'padding-left:10px;padding-top:10px;'
			,buttons:[
			{
				text: 'Print SOA'
				,iconCls: 'icon_ext_preview'
				,handler: function(){
					Ext.getCmp('membershipInfoCardBody').layout.setActiveItem('pnlOLReportSOA');	
				}
			}]
			,items: [	
				{
					xtype: 'hidden'
					,name: 'frm_mode'
					,value: FORM_MODE_LIST
					,submitValue: false
					,listeners: {'change':{fn: function(obj,value){
					}}}
				}
				,{
					layout: 'column'
					,border: false
					,labelAlign: 'left'
					,items: [{
						layout: 'form'
						,columnWidth: .25
						//,width:250
						,border: false
						,items: [{
							xtype: 'textfield'
							,name: 'online_membership[employee_id]'
							,fieldLabel: 'Employee'
							,allowBlank: false
							,style: 'text-align: right;'
							,cls: 'x-item-disabled'
							,anchor: '100%'
							,emptyText: 'ID'
							,autoCreate: {tag: 'input', type: 'text', maxlength: '8'}
							,readOnly: true
							}]
						},{
						layout: 'form'
						,labelWidth: 1
						,columnWidth: .175
						,border: false
						,items: [{
							xtype: 'textfield'
							,name: 'online_membership[last_name]'	
							//,width: 150
							,anchor: '100%'
							,allowBlank: false
							,fieldLabel: ' '
							,labelSeparator: ' '
							,emptyText: 'Last Name'
							,readOnly: true
							,cls: 'x-item-disabled'
							,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
							,submitValue: false
							}]
						},{
						layout: 'form'
						,labelWidth: 1
						,columnWidth: .175
						,border: false
						,items: [{
							xtype: 'textfield'
							,name: 'online_membership[first_name]'	
							//,width: 150
							,allowBlank: false
							,anchor: '100%'
							,fieldLabel: ' '
							,labelSeparator: ' '
							,emptyText: 'First Name'
							,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
							,submitValue: false
							,readOnly: true
							,cls: 'x-item-disabled'
							}]
						},{
						layout: 'form'
						,labelWidth: 1
						,columnWidth: .175
						,border: false
						,items: [{
							xtype: 'textfield'
							,name: 'online_membership[middle_name]'	
							//,width: 150
							,anchor: '100%'
							,fieldLabel: ' '
							,labelSeparator: ' '
							,submitValue: false
							,readOnly: true
							,cls: 'x-item-disabled'
							,emptyText: 'Middle Name'
							,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
							,submitValue: false
							}]
						}]
				}
				,{html: '<br />'}
				,{
					layout: 'fit'
					,defaultType: 'grid'
					,items: [membershipTabMemInfo()]
				}				
			]

		},{
			layout: 'form'
			,border: false
			,bodyStyle: 'padding-left:80%'
			,items: [
				{
					html: 'Print: Generate SOA'
				}
			]
		}]
		,listeners: {
			render: function(){
				if(_IS_ADMIN==false){
					om_storeMemInfo.om_beneficiaryStorageMemInfo.load({params: {'member[employee_id]':_EMP_ID,start:0, limit:MAX_PAGE_SIZE}});
					pecaDataStores.companyStoreOLM.load();
					Ext.getCmp('ol_membershipInfo').getForm().load({
				    	url: '/membership/show'
				    	,params: {'member[employee_id]':_EMP_ID, auth:_AUTH_KEY}
				    	,method: 'POST'
				    	,waitMsgTarget: true
						,success: function(form, action) {
							om_functionMemInfo.loadValuesMemInfo(null);
						}
					});
				}
			}
		}
	};
};

var membershipTabMemInfo = function(){
	return {
		xtype: 'tabpanel'
		,id: 'membershipTabMemInfo'
		,titlebar:false
		,activeTab:6
		,bodyStyle: 'background:transparent;'
		,height: 350
		,boxMinWidth: 500
		,anchor: '100%'
		,defaults: {autoScroll: true}
		,items:[
			om_memInfoTabMemInfo()
			,om_employmentInfoTabMemInfo()
			,om_loanInfoTabMemInfo()
			,om_guaranteedLoansInfoTabMemInfo()
			,om_bankInfoTabMemInfo()
			,om_personalInfoTabMemInfo()
			,om_transactionHistoryTabMemInfo()
		]
	};
};
var openMemInfoEdit = function(tabIndex){
	if(_IS_ADMIN==false){
		Ext.getCmp("mainBody").activate(8);
		Ext.getCmp("membershipTab").activate(tabIndex);
		om_store.om_beneficiaryStorage.load({params: { 'member[employee_id]':_EMP_ID,start:0, limit:MAX_PAGE_SIZE}});
		pecaDataStores.companyStoreOLM.load();
		Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOLMemInfoDetail');
		Ext.getCmp('ol_membershipDetail').getForm().reset();
		om_function.clearHighlights();
		temp=[];
		tempStorageOfFields=[];
		tempStorageOfBFields=[];
		Ext.getCmp('ol_membershipDetail').getForm().load({
			url: '/membership/show'
			,params: {'member[employee_id]':_EMP_ID, auth:_AUTH_KEY}
			,method: 'POST'
			,waitMsgTarget: true
			,success: function(form, action) {
				om_function.loadValues(null);
			}
		});
		temp = [];
		tempStorageOfFields=[];
		tempStorageOfBFields=[];
		Ext.getCmp('ol_membershipDetail').getForm().setModeNew();
	}
}
var om_memInfoTabMemInfo = function(){
	return {
		layout:'form'
		,title:'Membership'
		,id:'om_memInfoTabMemInfo'
		,bodyStyle: 'background:transparent; padding: 10px;'
		,buttons:[
			{
				text: 'Edit'
				,iconCls: 'icon_ext_edit'
				,handler: function(){
					openMemInfoEdit(0);
				}
			}]
		,items: [ {
				layout: 'column'
				,border: false
				,labelAlign: 'left'
				,items:[{
					layout: 'form'
					,border: false
					,columnWidth: .5
					,items: [
						{
						xtype: 'datefield'
						,fieldLabel: 'Membership Date'
						,name: 'online_membership[member_date]'
						,submitValue: false
						,validationEvent: 'change'
						,maxLength: 10
						,readOnly: true
						,cls: 'x-item-disabled'
						,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
						}
						]
				},{
					layout: 'form'
					,border: false
					,columnWidth: .5
					,items: []
				}
				,{
					layout: 'form'
					,border: false
					,columnWidth: .5
					,items: [{
						xtype:'checkbox'
						//,columnWidth: .3
						//,width: 100
						,boxLabel: 'Non-member'
						,name: 'online_membership[non_member]'
						,submitValue: false
						,disabled: true
						,cls: 'x-item-disabled'
					}]
				}
				]
		},{
			layout: 'form'
			,border: false
			,items: [{
				xtype: 'combo'
				,hiddenName: 'online_membership[member_status]'
				,store: om_storeMemInfo.statusStoreMemInfo
				,fieldLabel: 'Status'
				// ,width: 183
				,mode: 'local'
				,displayField: 'name'
				,valueField: 'value'
				,editable: 'false'
				,emptyText: 'Please Select'
				,forceSelection: true
				,triggerAction: 'all'
				,selectOnFocus: true
				,editable: false
				,allowBlank: false
				,required: true
				,submitValue: false
				,readOnly: true
				,cls: 'x-item-disabled'
			}]
		},{
			layout: 'form'
			,xtype: 'fieldset'
			,title: 'Beneficiaries'
			,border: true
			,bodyStyle:{'padding':'2px'}
			,anchor: '100%'
			,items:[{
				layout: 'column'
				,border: false
				,labelAlign: 'left'
				,items:[{
					layout: 'form'
					,border: false
					,labelWidth: 1
					,columnWidth: .3
					,bodyStyle:{'padding':'2px'}
					,items:[ {
						html: '<div align="center">Name</div>'
					},{
						xtype: 'textfield'
						,anchor: '100%'
						,name: 'online_membership[beneficiary_name1]'
						,submitValue: false
						,autoCreate: {tag: 'input', type: 'text', maxlength: '100'}
						,readOnly: true
						,cls: 'x-item-disabled'
					},{
						xtype: 'textfield'
						,anchor: '100%'
						,name: 'online_membership[beneficiary_name2]'
						,submitValue: false
						,autoCreate: {tag: 'input', type: 'text', maxlength: '100'}
						,readOnly: true
						,cls: 'x-item-disabled'
					},{
						xtype: 'textfield'
						,anchor: '100%'
						,name: 'online_membership[beneficiary_name3]'
						,submitValue: false
						,autoCreate: {tag: 'input', type: 'text', maxlength: '100'}
						,readOnly: true
						,cls: 'x-item-disabled'
					}]	
				},{
					layout: 'form'
					,border: false
					,labelWidth: 1
					,columnWidth: .2
					,bodyStyle:{'padding':'2px'}
					,items:[{
						html: '<div align="center">Relationship</div>'
					},{
						xtype: 'combo'
						,anchor: '100%'
						// ,width: 135
						,hiddenName: 'online_membership[beneficiary_relationship1]'
						,store: om_storeMemInfo.beneficiaryStoreMemInfo
						,mode: 'local'
						,displayField: 'name'
						,valueField: 'initial'
						,editable: 'false'
						,emptyText: 'Please Select'
						,forceSelection: true
						,triggerAction: 'all'
						,selectOnFocus: true
						,submitValue: false
						,editable: false
						,readOnly: true
						,cls: 'x-item-disabled'
					},{
						xtype: 'combo'
						,anchor: '100%'
						// ,width: 135
						,hiddenName: 'online_membership[beneficiary_relationship2]'
						,store: om_storeMemInfo.beneficiaryStoreMemInfo
						,mode: 'local'
						,displayField: 'name'
						,valueField: 'initial'
						,editable: 'false'
						,emptyText: 'Please Select'
						,forceSelection: true
						,triggerAction: 'all'
						,selectOnFocus: true
						,submitValue: false
						,editable: false
						,readOnly: true
						,cls: 'x-item-disabled'
					},{
						xtype: 'combo'
						,anchor: '100%'
						// ,width: 135
						,hiddenName: 'online_membership[beneficiary_relationship3]'
						,store: om_storeMemInfo.beneficiaryStoreMemInfo
						,mode: 'local'
						,displayField: 'name'
						,valueField: 'initial'
						,editable: 'false'
						,emptyText: 'Please Select'
						,forceSelection: true
						,triggerAction: 'all'
						,selectOnFocus: true
						,submitValue: false
						,editable: false
						,readOnly: true
						,cls: 'x-item-disabled'
					}]
				},{
					layout: 'form'
					,border: false
					,labelWidth: 1
					,columnWidth: .5
					,bodyStyle:{'padding':'2px'}
					,items:[{
						html: '<div align="center">Address</div>'
					},{
						xtype: 'textfield'
						,anchor: '100%'
						,name: 'online_membership[beneficiary_address1]'
						,submitValue: false
						,autoCreate: {tag: 'input', type: 'text', maxlength: '100'}
						,readOnly: true
						,cls: 'x-item-disabled'
					},{
						xtype: 'textfield'
						,anchor: '100%'
						,name: 'online_membership[beneficiary_address2]'
						,submitValue: false
						,autoCreate: {tag: 'input', type: 'text', maxlength: '100'}
						,readOnly: true
						,cls: 'x-item-disabled'
					},{
						xtype: 'textfield'
						,anchor: '100%'
						,name: 'online_membership[beneficiary_address3]'
						,submitValue: false
						,autoCreate: {tag: 'input', type: 'text', maxlength: '100'}
						,readOnly: true
						,cls: 'x-item-disabled'
					}]
				}]
		}]	
		}]
		
	};
};

var om_employmentInfoTabMemInfo = function(){
	return {
		layout:'form'
		,title:'Employment'
		,bodyStyle: 'background:transparent; padding: 10px;'
		,anchor: '95%'
		,buttons:[
			{
				text: 'Edit'
				,iconCls: 'icon_ext_edit'
				,handler: function(){
					openMemInfoEdit(1);
				}
			}]
		,items:[{
			layout: 'column'
			,border: false
			,labelAlign: 'left'
			,items: [{
				layout: 'form'
				,columnWidth: .5
				,labelWidth: 130
				,border: false
				,items:[{
					xtype: 'textfield'
					,fieldLabel: 'TIN'
					//,width: 300
					,anchor: '95%'
					,name: 'online_membership[TIN]'
					,autoCreate: {tag: 'input', type: 'text', maxlength: '20'}
					,submitValue: false
					,readOnly: true
					,cls: 'x-item-disabled'
				},{
					xtype: 'datefield'
					,fieldLabel: 'Date of Employment'
					,anchor: '95%'
					// ,width: 250
					,readOnly: true
					,name: 'online_membership[hire_date]'
					,required: true
					,allowBlank: false
					,submitValue: false
					,validationEvent: 'change'
					,maxLength: 10
					,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
					,cls: 'x-item-disabled'
				},{
					xtype: 'datefield'
					,fieldLabel: 'Separation Date'
					,anchor: '95%'
					,name: 'online_membership[work_date]'
					,submitValue: false
					,validationEvent: 'change'
					,maxLength: 10
					,readOnly: true
					,cls: 'x-item-disabled'
					,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
				},{
					xtype: 'combo'
					,anchor: '95%'
					,hiddenName: 'online_membership[company_code]'
					,store: pecaDataStores.companyStoreOLM
					,mode: 'local'
					,fieldLabel: 'Company'
					,displayField: 'company_name'
					,valueField: 'company_code'
					,editable: 'false'
					,emptyText: 'Please Select'
					,forceSelection: true
					,triggerAction: 'all'
					,selectOnFocus: true
					,editable: false
					,submitValue: false
					,readOnly: true
					,cls: 'x-item-disabled'
				}]
			},{
				layout: 'form'
				,xtype: 'fieldset'
				,columnWidth: .5
				,labelWidth: 130
				,border: false
				,items:[{
					xtype: 'textfield'
					,fieldLabel: 'Department'
					,anchor: '95%'
					,name: 'online_membership[department]'
					,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
					,submitValue: false
					,readOnly: true
					,cls: 'x-item-disabled'
				},{
					xtype: 'textfield'
					,fieldLabel: 'Position'
					,anchor: '95%'
					,name: 'online_membership[position]'
					,autoCreate: {tag: 'input', type: 'text', maxlength: '20'}
					,submitValue: false
					,readOnly: true
					,cls: 'x-item-disabled'
				},{
					xtype: 'textfield'
					,fieldLabel: 'Office Number'
					,anchor: '95%'
					,name: 'online_membership[office_no]'
					,autoCreate: {tag: 'input', type: 'text', maxlength: '15'}
					,submitValue: false
					,readOnly: true
					,cls: 'x-item-disabled'
				},{
					xtype: 'textfield'
					,fieldLabel: 'E-mail Address'
					,anchor: '95%'
					,required: true
					,allowBlank: false
					,msgTarget: 'under'
					,name: 'online_membership[email_address]'
					,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
					,submitValue: false
					,readOnly: true
					,cls: 'x-item-disabled'
				}]
			}]
		}]
	};
};

var om_loanInfoTabMemInfo = function(){
	return {
		xtype:'panel'
		,title:'Loan Information'
		,bodyStyle: 'background:transparent; padding: 10px;'
		,items: [{
        	layout: 'fit'
            ,defaultType: 'grid'
			,items: [membershipLoanInfoListMemInfo()]
        }]
	};
};
var membershipLoanInfoListMemInfo = function(){
	return {
		xtype: 'grid'
		,id: 'membershipLoanInfoListMemInfo'
		,titlebar: false
		,store: pecaDataStores.ol_memberLoanInfoStore2
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 270
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,plugins: [om_summaryLoanMemberMemInfo]
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
					/* ##### NRB EDIT START ##### */
					if(rec.get('bsp_computation') == 'Y') {
						om_functionMemInfo.membershipLoanWinMemInfo(true).show();
					} else {
						om_functionMemInfo.membershipLoanWinMemInfo(false).show();
					}
					/* om_functionMemInfo.membershipLoanWinMemInfo().show(); */
					/* ##### NRB EDIT END ##### */
					Ext.getCmp('membershipLoanDetailsMemInfo').getForm().load({
				    	url: '/membership/showLoanInfo'
				    	,params: {'loan_no':(rec.get('loan_no'))
									,auth:_AUTH_KEY}
				    	,method: 'POST'
				    	,waitMsgTarget: true
				    	,success: function(response, opts) {
							om_storeMemInfo.membershipLoanPaymentsStoreMemInfo.load({params: { loan_no :rec.get('loan_no'),start:0, limit:MAX_PAGE_SIZE}});
							om_storeMemInfo.membershipChargesStoreMemInfo.load({params: { loan_no :rec.get('loan_no'),start:0, limit:MAX_PAGE_SIZE}});
							om_storeMemInfo.membershipCoMakersStoreMemInfo.load({params: { loan_no :rec.get('loan_no'),start:0, limit:MAX_PAGE_SIZE}, callback: function(r, options, success) {
								/* if(om_storeMemInfo.membershipCoMakersStoreMemInfo.getTotalCount() == 0){
									Ext.getCmp('membership_noCoMaker').setValue(true);
								} */
							}});
						}
					});
					
				}
				
			}
		}
		,viewConfig: {
			forceFit:true
			,scrollOffset:13
		}
		,cm: om_columnMemInfo.membershipLoanInfoColumnsMemInfo
		,bbar: new Ext.PagingToolbar({
	        store: pecaDataStores.ol_memberLoanInfoStore2
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};
var om_summaryLoanMemberMemInfo = new Ext.ux.grid.GridSummary();
/* ##### NRB EDIT START */
var membershipLoanDetailsMemInfo = function(b_bsp_computation){
	if(b_bsp_computation) {
		return{
		xtype: 'form'
		,id: 'membershipLoanDetailsMemInfo'
		,frame: true
		,height: 480
		,anchor: '100%'
		,reader: pecaReaders.memberLoanInfoDetailReader
		,bodyStyle: 'background:transparent; padding: 10px;'
		,border: false
		,items:[{	
			layout: 'column'
			,anchor: '95%'
			,border: false
			,bodyStyle: 'background:transparent;'
			,items:[{
				layout: 'form'
				,columnWidth: 0.5
				,labelWidth: 200
				,bodyStyle: 'background:transparent;'
				,border: false
				,items:[{
						xtype: 'textfield'
						,border: false
						,submitValue: false
						,name: 'loan_no'
						,anchor: '95%'
						,id: 'membership_loan_info_loan_no'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'Loan No.'
					},{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'loan_description'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'Loan Type'
					},
					//[START] 7th Enhancement
					{
						xtype: 'hidden'
						,submitValue: false					
						,name: 'loan_code'					
						,id: 'membership_loan_info_loan_code'
						
					}
					//[END] 7th Enhancement
					,{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'loan_date'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'Loan Date'
					},{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'principal'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'Principal Amount'
					},{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'term'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'Terms in Months'
					},{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'employee_interest_rate'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'Annual Contractual Rate'
					},{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'initial_interest'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'Emp. Initial Interest'
					},{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'employee_interest_amortization'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'Emp. Amortized Interest'
					},{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'company_interest_rate'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'P&G Interest Rate'
					},{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'company_interest_amort'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'P&G Amortized Interest'
					}
				]
			},{
				layout: 'form'
				,columnWidth: 0.5
				,labelWidth: 200
				,bodyStyle: 'background:transparent;'
				,border: false
				,items:[{
						xtype: 'checkbox'
						,readOnly: true
						,submitValue: false
						,name: 'restructure'
						,style: 'background:transparent; text-align: right; border:0'
						,anchor: '95%'
						,disabled: true
						,fieldLabel: 'Restructured'
					},{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'amortization_startdate'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'Amortization Start Date'
					},{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'loan_proceeds'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'Loan Proceeds'
					},{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'mri_fip_amount'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'MRI/FIP'
					},{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'broker_fee_amount'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'Broker\'s Fee'
					},{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'government_fee_amount'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'Gov\'t Fees'
					},{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'other_fee_amount'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'Other Fees'
					},{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'service_fee_amount'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'Service Charge'
					},{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'principal_balance'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'Loan Balance'
					}]
			}]
		}, membershipLoanDetailTabMemInfo(b_bsp_computation)]
		}
	} else {
		return{
		xtype: 'form'
		,id: 'membershipLoanDetailsMemInfo'
		,frame: true
		,height: 480
		,anchor: '100%'
		,reader: pecaReaders.memberLoanInfoDetailReader
		,bodyStyle: 'background:transparent; padding: 10px;'
		,border: false
		,items:[{	
			layout: 'column'
			,anchor: '95%'
			,border: false
			,bodyStyle: 'background:transparent;'
			,items:[{
				layout: 'form'
				,columnWidth: 0.5
				,labelWidth: 200
				,bodyStyle: 'background:transparent;'
				,border: false
				,items:[{
						xtype: 'textfield'
						,border: false
						,submitValue: false
						,name: 'loan_no'
						,anchor: '95%'
						,id: 'membership_loan_info_loan_no'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'Loan No.'
					},{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'loan_description'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'Loan Type'
					},
					//[START] 7th Enhancement
					{
						xtype: 'hidden'
						,submitValue: false					
						,name: 'loan_code'					
						,id: 'membership_loan_info_loan_code'
						
					}
					//[END] 7th Enhancement
					,{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'loan_date'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'Loan Date'
					},{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'principal'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'Principal Amount'
					},{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'term'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'Terms in Months'
					},{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'employee_interest_rate'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'Annual Contractual Rate'
					},{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'initial_interest'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'Emp. Initial Interest'
					},{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'employee_interest_amortization'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'Emp. Amortized Interest'
					},{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'company_interest_rate'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'P&G Interest Rate'
					},{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'company_interest_amort'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'P&G Amortized Interest'
					}
				]
			},{
				layout: 'form'
				,columnWidth: 0.5
				,labelWidth: 200
				,bodyStyle: 'background:transparent;'
				,border: false
				,items:[{
						xtype: 'checkbox'
						,readOnly: true
						,submitValue: false
						,name: 'restructure'
						,style: 'background:transparent; text-align: right; border:0'
						,anchor: '95%'
						,disabled: true
						,fieldLabel: 'Restructured'
					},{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'amortization_startdate'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'Amortization Start Date'
					},{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'loan_proceeds'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'Loan Proceeds'
					},{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'mri_fip_amount'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'MRI/FIP'
					},{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'broker_fee_amount'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'Broker\'s Fee'
					},{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'government_fee_amount'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'Gov\'t Fees'
					},{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'other_fee_amount'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'Other Fees'
					},{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'service_fee_amount'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'Service Charge'
					},{
						xtype: 'textfield'
						,border: false
						,readOnly: true
						,submitValue: false
						,name: 'principal_balance'
						,anchor: '95%'
						,style: 'background:transparent; text-align: right; border:0'
						,fieldLabel: 'Loan Balance'
					}]
			}]
		}, membershipLoanDetailTabMemInfo(b_bsp_computation)]
		}
	}
};
/*
var membershipLoanDetailsMemInfo = function(){
	return{
	xtype: 'form'
	,id: 'membershipLoanDetailsMemInfo'
	,frame: true
	,height: 480
	,anchor: '100%'
	,reader: pecaReaders.memberLoanInfoDetailReader
	,bodyStyle: 'background:transparent; padding: 10px;'
	,border: false
	,items:[{	
		layout: 'column'
		,anchor: '95%'
		,border: false
		,bodyStyle: 'background:transparent;'
		,items:[{
			layout: 'form'
			,columnWidth: 0.5
			,labelWidth: 200
			,bodyStyle: 'background:transparent;'
			,border: false
			,items:[{
					xtype: 'textfield'
					,border: false
					,submitValue: false
					,name: 'loan_no'
					,anchor: '95%'
					,id: 'membership_loan_info_loan_no'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Loan No.'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'loan_description'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Loan Type'
				},
				//[START] 7th Enhancement
				{
					xtype: 'hidden'
					,submitValue: false					
					,name: 'loan_code'					
					,id: 'membership_loan_info_loan_code'
					
				}
				//[END] 7th Enhancement
				,{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'loan_date'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Loan Date'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'principal'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Principal Amount'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'term'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Terms in Months'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'employee_interest_rate'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Emp. Interest Rate'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'employee_interest_total'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Emp. Interest Amount'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'initial_interest'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Emp. Initial Interest'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'employee_interest_amortization'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Emp. Amortized Interest'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'company_interest_rate'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Company Interest Rate'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'company_interest_total'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Company Interest Amount'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'company_interest_amort'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Company Amortized Interest'
				}
			]
		},{
			layout: 'form'
			,columnWidth: 0.5
			,labelWidth: 200
			,bodyStyle: 'background:transparent;'
			,border: false
			,items:[{
					xtype: 'checkbox'
					,readOnly: true
					,submitValue: false
					,name: 'restructure'
					,style: 'background:transparent; text-align: right; border:0'
					,anchor: '95%'
					,disabled: true
					,fieldLabel: 'Restructured'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'amortization_startdate'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Amortization Start Date'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'employee_principal_amortization'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Emp. Principal Amortization'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'loan_proceeds'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Loan Proceeds'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'mri_fip_amount'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'MRI/FIP'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'broker_fee_amount'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Broker\'s Fee'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'government_fee_amount'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Gov\'t Fees'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'other_fee_amount'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Other Fees'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'service_fee_amount'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Service Charge'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'principal_balance'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Loan Balance'
				}]
		}]
	}, membershipLoanDetailTabMemInfo()]
	}
};
*/
/* ##### NRB EDIT END */
var membershipLoanDetailTabMemInfo = function(b_bsp_computation){
	/*return {
		xtype: 'tabpanel'
		,titlebar:false
		,activeTab:0
		,anchor: '98%'
		,bodyStyle: 'background:transparent;'
		,height: 335
		,defaults: {autoScroll: true}
		,items:[
			membershipLoanPaymentsTabMemInfo()
			,membershipChargesTabMemInfo()
			,membershipCoMakersTabMemInfo()	
			//[START] 7th Enhancement
			,membershipAmortSchedMemInfo()
			//[START] 7th Enhancement
		]
	};*/
	//[START] 7th Enhancement
	if(b_bsp_computation) {
		return {
			xtype: 'tabpanel'
			,titlebar:false
			,activeTab:0
			,anchor: '98%'
			,bodyStyle: 'background:transparent;'
			,height: 335
			,defaults: {autoScroll: true}
			,items:[
				membershipLoanPaymentsTabMemInfo()
				,membershipChargesTabMemInfo()
				,membershipCoMakersTabMemInfo()	
				,membershipAmortSchedMemInfo()
			]
		};
	}
	else {
		return {
			xtype: 'tabpanel'
			,titlebar:false
			,activeTab:0
			,anchor: '98%'
			,bodyStyle: 'background:transparent;'
			,height: 335
			,defaults: {autoScroll: true}
			,items:[
				membershipLoanPaymentsTabMemInfo()
				,membershipChargesTabMemInfo()
				,membershipCoMakersTabMemInfo()	
			]
		};
	}
	//[START] 7th Enhancement
};
var membershipLoanPaymentsTabMemInfo= function(){
	return {
		xtype:'panel'
		,title:'Loan Payments'
		,bodyStyle: 'background:transparent; padding: 5px;'
		,items: [membershipLoanPaymentsListMemInfo()]
	};
};
var membershipLoanPaymentsListMemInfo = function(){
	return {
		xtype: 'grid'
		,id: 'membershipLoanPaymentsListMemInfo'
		,titlebar: false
		,store: om_storeMemInfo.membershipLoanPaymentsStoreMemInfo
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 270
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit:true
			,scrollOffset:13
		}
		,cm: om_columnMemInfo.membershipLoanPaymentsColumnsMemInfo
		,bbar: new Ext.PagingToolbar({
	        store: om_storeMemInfo.membershipLoanPaymentsStoreMemInfo
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};

var membershipChargesTabMemInfo= function(){
	return {
		xtype:'panel'
		,title:'Charges'
		,bodyStyle: 'background:transparent; padding: 5px;'
		,items: [membershipChargesListMemInfo()]
	};
};
var membershipChargesListMemInfo = function(){
	return {
		xtype: 'grid'
		,id: 'membershipChargesListMemInfo'
		,titlebar: false
		,store: om_storeMemInfo.membershipChargesStoreMemInfo
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 270
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit:true
			,scrollOffset:13
		}
		,cm: om_columnMemInfo.membershipChargesColumnsMemInfo
		,bbar: new Ext.PagingToolbar({
	        store: om_storeMemInfo.membershipChargesStoreMemInfo
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};

var membershipCoMakersTabMemInfo= function(){
	return {
		xtype:'panel'
		,title:'Co-Makers'
		,bodyStyle: 'background:transparent; padding: 5px;'
		,items: [membershipCoMakersPanelMemInfo()]
	};
};
var membershipCoMakersPanelMemInfo = function(){
	return {
		border: false
		,layout: 'column'
		,bodyStyle: 'background:transparent;'
		,items:[membershipCoMakersListMemInfo()
			,{
			columnWidth: 0.5
			,bodyStyle: 'background:transparent; padding: 10px;'
			,border: false
			,items:[{
				bodyStyle: 'background:transparent;'
				,border: false
				,html: '<br /><br />'
			},{
				xtype: 'checkbox'
				,boxLabel: 'No Co-Maker'
				,name: 'noCoMaker'
				,id: 'membership_noCoMaker'
				,disabled: true
			},{
				layout: 'column'
				,bodyStyle: 'background:transparent;'
				,border: false
				,items:[{
					columnWidth: 0.4
					,xtype: 'checkbox'
					,name: 'pension'
					,boxLabel: 'Pensioned'
					,disabled: true
				},{
					xtype: 'textfield'
					,style: 'text-align: right;'
					,name: 'pensionAmount'
					,readOnly: true
					,columnWidth: 0.5
				}]
			}]
		}]
	};
};
var membershipCoMakersListMemInfo = function(){
	return {
		xtype: 'grid'
		,id: 'membershipCoMakersListMemInfo'
		,titlebar: false
		,columnWidth: 0.5
		,store: om_storeMemInfo.membershipCoMakersStoreMemInfo
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 270
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit:true
			,scrollOffset:0
		}
		,cm: om_columnMemInfo.membershipCoMakersColumnsMemInfo
		,bbar: new Ext.PagingToolbar({
	        store: om_storeMemInfo.membershipCoMakersStoreMemInfo
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};

var om_guaranteedLoansInfoTabMemInfo = function(){
	return {
		xtype:'panel'
		,title:'Co-Made Loans'
		,bodyStyle: 'background:transparent; padding: 10px;'
		,items: [{
        	layout: 'fit'
            ,defaultType: 'grid'
			,items: [om_guaranteedLoansListMemInfo()]
        }]
	};
};

var om_bankInfoTabMemInfo= function(){
	return {
		layout:'form'
		,title:'Bank Information'
		,bodyStyle: 'background:transparent; padding: 10px;'
		,items:[{
				xtype: 'textfield'
				,fieldLabel: 'Bank'
				,width: 200
				,name: 'membership[bank]'
				,readOnly: true
				,cls: 'x-item-disabled'
				,autoCreate: {tag: 'input', type: 'text', maxlength: '1'}
			},{
				xtype: 'textfield'
				,fieldLabel: 'Account Number'
				,width: 200
				,style: 'text-align: left'
				,readOnly: true
				,cls: 'x-item-disabled'
				,name: 'membership[bank_account_no]'
				,autoCreate: {tag: 'input', type: 'text', maxlength: '15'}
		}]
	};
};


var om_guaranteedLoansListMemInfo = function(){
	return {
		xtype: 'grid'
		,id: 'om_guaranteedLoansListMemInfo'
		,titlebar: false
		,store: pecaDataStores.ol_guaranteedLoansStore2
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 270
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		//,plugins: [om_summaryComakerMember]
		,viewConfig: {
			forceFit:true
			,scrollOffset:13
		}
		,cm: om_columnMemInfo.om_guaranteedLoansColumnsMemInfo
		,bbar: new Ext.PagingToolbar({
	        store: pecaDataStores.ol_guaranteedLoansStore2
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};
//var om_summaryComakerMember = new Ext.ux.grid.GridSummary();

var om_personalInfoTabMemInfo= function(){
	return {
		layout:'form'
		,title:'Personal'
		,bodyStyle: 'background:transparent; padding: 10px;'
		,buttons:[
			{
				text: 'Edit'
				,iconCls: 'icon_ext_edit'
				,handler: function(){
					openMemInfoEdit(5);
				}
			}]
		,items:[{
		layout: 'column'
		,border: false
		,labelAlign: 'left'
		,items: [{
			layout: 'form'
			,columnWidth: .5
			,labelWidth: 130
			,border: false
			,items:[{
				xtype: 'datefield'
				,fieldLabel: 'Date of Birth'
				,anchor: '95%'
				,name: 'membership[birth_date]'
				,submitValue: false
				,validationEvent: 'change'
				,maxLength: 10
				,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
				,readOnly: true
				,cls: 'x-item-disabled'
			},{
				xtype: 'combo'
				,fieldLabel: 'Gender'
				,anchor: '95%'
				,hiddenName: 'membership[gender]'
				,store: om_storeMemInfo.om_genderStoreMemInfo
				,mode: 'local'
				,displayField: 'name'
				,valueField: 'initial'
				,editable: 'false'
				,emptyText: 'Please Select'
				,forceSelection: true
				,triggerAction: 'all'
				,selectOnFocus: true
				,editable: false
				,submitValue: false
				,required: true
				,readOnly: true
				,cls: 'x-item-disabled'
			},{
				xtype: 'combo'
				,fieldLabel: 'Civil Status'
				,anchor: '95%'
				,hiddenName: 'membership[civil_status]'
				,store: om_storeMemInfo.om_civilstatusStoreMemInfo
				,mode: 'local'
				,displayField: 'name'
				,valueField: 'initial'
				,editable: 'false'
				,emptyText: 'Please Select'
				,forceSelection: true
				,triggerAction: 'all'
				,selectOnFocus: true
				,editable: false
				,submitValue: false
				,required: true
				,readOnly: true
				,cls: 'x-item-disabled'
			},{
				xtype: 'textfield'
				,fieldLabel: 'Name of Spouse'
				,anchor: '95%'
				,name: 'membership[spouse]'
				,autoCreate: {tag: 'input', type: 'text', maxlength: '40'}
				,submitValue: false
				,readOnly: true
				,cls: 'x-item-disabled'
			}]
		},{
			layout: 'form'
			,xtype: 'fieldset'
			,columnWidth: .5
			,labelWidth: 130
			,border: false
			,items:[{
				xtype: 'textfield'
				,fieldLabel: 'Address'
				,anchor: '95%'
				,name: 'membership[address_1]'
				,autoCreate: {tag: 'input', type: 'text', maxlength: '35'}
				,submitValue: false
				,readOnly: true
				,cls: 'x-item-disabled'
			},{
				xtype: 'textfield'
				,fieldLabel: ' '
				,labelSeparator: ' '
				,anchor: '95%'
				,name: 'membership[address_2]'
				,autoCreate: {tag: 'input', type: 'text', maxlength: '35'}
				,submitValue: false
				,readOnly: true
				,cls: 'x-item-disabled'
			},{
				xtype: 'textfield'
				,fieldLabel: ' '
				,labelSeparator: ' '
				,anchor: '95%'
				,name: 'membership[address_3]'
				,autoCreate: {tag: 'input', type: 'text', maxlength: '35'}
				,submitValue: false
				,readOnly: true
				,cls: 'x-item-disabled'
			},{
				html: '<br />'
			},{
				xtype: 'textfield'
				,fieldLabel: 'Home Phone'
				,anchor: '95%'
				,name: 'membership[home_phone]'
				,autoCreate: {tag: 'input', type: 'text', maxlength: '15'}
				,submitValue: false
				,readOnly: true
				,cls: 'x-item-disabled'
			},{
				xtype: 'textfield'
				,fieldLabel: 'Mobile Number'
				,anchor: '95%'
				,name: 'membership[mobile_no]'
				,autoCreate: {tag: 'input', type: 'text', maxlength: '15'}
				,submitValue: false
				,readOnly: true
				,cls: 'x-item-disabled'
			}]
		}]
	}]
	};
};

var om_transactionHistoryTabMemInfo = function(){
	return {
		layout:'form'
		,title:'Transaction History'
		,bodyStyle: 'background:transparent; padding: 10px;'
		,items:[{
			layout: 'column'
			,border: false
			,labelAlign: 'left'
			,items: [{
				columnWidth: .5
				,items: [om_transactionHistoryListMemInfo()]
			},{
				layout: 'form'
				,columnWidth: .5
				,bodyStyle: 'padding: 50px;'
				,labelWidth: 200
				,labelAlign: 'left'
				,items: [{
					html: '<br /><br />'
				},{
					xtype: 'textfield'
					,border: false
					,value: '0.00'
					,readOnly: true
					,submitValue: false
					,width: 120
					,style: 'background:transparent; text-align: right; border:0'
					,id: 'membershipMemInfo[capital_contribution]'
					,fieldLabel: 'Capital Contribution'
				},{
					html: '<br />'
				},{
					xtype: 'textfield'
					,border: false
					,value: '0.00'
					,readOnly: true
					,submitValue: false
					,width: 120
					,style: 'background:transparent; text-align: right; border:0'
					,id: 'membershipMemInfo[required_balance]'
					//,fieldLabel: 'Required Balance'
					// jdj 08042017
					,fieldLabel: 'Non-Withdrawable Capital'	
				},{
					html: '<br />'
				},{
					xtype: 'textfield'
					,border: false
					,value: '0.00'
					,readOnly: true
					,submitValue: false
					,width: 120
					,style: 'background:transparent; text-align: right; border:0'
					,id: 'membershipMemInfo[maximum_withdrawable_amount]'
					,fieldLabel: 'Maximum Withdrawable Amount'
				}]
			}]
			
		}]
	};
};
var om_transactionHistoryListMemInfo = function(){
	return {
		xtype: 'grid'
		,id: 'om_transactionHistoryListMemInfo'
		,titlebar: false
		,store: pecaDataStores.ol_transactionHistoryStore2
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 270
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit:true
			,scrollOffset:13
		}
		,cm: om_columnMemInfo.om_transactionHistoryColumnsMemInfo
		,bbar: new Ext.PagingToolbar({
	        store: pecaDataStores.ol_transactionHistoryStore2
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};

//[START] 7th Enhancement
var membershipAmortSchedMemInfo = function(){
	return {
		xtype:'panel'
		,title:'Amortization Schedule'
		,id: 'amortization_schedule_tab_membership'
		,bodyStyle: 'background:transparent padding: 5px;'
		,items: [rpt_amortizationschedule("membership_loan_info_loan_no")]
	};
};
//[END] 7th Enhancement