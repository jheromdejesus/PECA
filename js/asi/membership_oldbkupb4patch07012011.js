//Let's pretend we rendered our grid-columns with meta-data from our ORM framework.
var summaryLoanMember = new Ext.ux.grid.GridSummary();
var summaryComakerMember = new Ext.ux.grid.GridSummary();

var memberColumns =  new Ext.grid.ColumnModel( 
	[
     {id: 'employee_id', header: 'Employee ID', width: 100, align: 'right', dataIndex: 'member[employee_id]'}
	 ,{header: 'Last Name', sortable: true, width: 200,  dataIndex: 'member[last_name]'}
     ,{header: 'First Name', sortable: true, width: 200, dataIndex: 'member[first_name]'}
	]
);

var memberEmployeeColumns =  new Ext.grid.ColumnModel( 
	[
		{id:'employeeList_id', header: 'Employee ID', width: 100, sortable: true, dataIndex: 'employee_id'}
		,{header: 'Last Name', width: 100, sortable: true, dataIndex: 'last_name'}
		,{header: 'First Name', width: 100, sortable: true, dataIndex: 'first_name'}
	]
);

var memberLoanInfoColumns =  new Ext.grid.ColumnModel( 
	[
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
				if(pecaDataStores.memberLoanInfoSumStore.getCount()>0)
					return Ext.util.Format.number(pecaDataStores.memberLoanInfoSumStore.getAt(0).get('interest_amortization'),'0,000,000,000.00');
				}
			}
		,{header: 'Principal Amortization', width: 100, sortable: true, align: 'right', dataIndex: 'principal_amortization', 
			renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}
			, summaryRenderer: function(v, params, data){
				if(pecaDataStores.memberLoanInfoSumStore.getCount()>0)
					return Ext.util.Format.number(pecaDataStores.memberLoanInfoSumStore.getAt(0).get('principal_amortization'),'0,000,000,000.00');
				}
			}
		,{header: 'Monthly Amortization', width: 100, sortable: true, align: 'right', dataIndex: 'montly_amortization', 
			renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}
			, summaryRenderer: function(v, params, data){
				if(pecaDataStores.memberLoanInfoSumStore.getCount()>0)
					return Ext.util.Format.number(pecaDataStores.memberLoanInfoSumStore.getAt(0).get('monthly_amortization'),'0,000,000,000.00');
				}
			}
		,{header: 'Principal Balance', width: 100, sortable: true, align: 'right', dataIndex: 'principal_balance', 
			renderer:function(value,rec){
				return Ext.util.Format.number(value,'0,000,000,000.00');
			}
			, summaryRenderer: function(v, params, data){
				if(pecaDataStores.memberLoanInfoSumStore.getCount()>0)
					return Ext.util.Format.number(pecaDataStores.memberLoanInfoSumStore.getAt(0).get('principal_balance'),'0,000,000,000.00');
			}
		}
	]
);

var transactionHistoryColumns =  new Ext.grid.ColumnModel( 
	[
		{header: 'Date', width: 50, sortable: false, align: 'center', dataIndex: 'transaction_date'}
		,{header: 'Code', width: 25, sortable: false, dataIndex: 'transaction_code'}
		,{header: 'Amount', width: 75, sortable: false, dataIndex: 'transaction_amount', align: 'right'}
		,{header: 'Balance', width: 75, sortable: false, align: 'right', dataIndex: 'balance'}
	]
);

var guaranteedLoansColumns =  new Ext.grid.ColumnModel( 
	[
		{id:'loan_no', header: 'Loan Number', width: 75, sortable: true, align: 'right', dataIndex: 'loan_no'}
		,{header: 'Loan Code', width: 50, sortable: true, dataIndex: 'loan_code'}
		,{header: 'Loan Date', width: 75, sortable: true, align: 'center', dataIndex: 'loan_date'}
		,{header: 'Employee Name', width: 150, sortable: true, dataIndex: 'employee_name'}
		,{header: 'Principal', width: 100, sortable: true, dataIndex: 'principal', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}}
		,{header: 'Interest Amortization', width: 100, sortable: true, align: 'right', dataIndex: 'interest_amortization', 
			renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}
			, summaryRenderer: function(v, params, data){
				if(pecaDataStores.guaranteedLoansSumStore.getCount()>0)
					return Ext.util.Format.number(pecaDataStores.guaranteedLoansSumStore.getAt(0).get('interest_amortization'),'0,000,000,000.00');
				}
			}
		,{header: 'Principal Amortization', width: 100, sortable: true, align: 'right', dataIndex: 'principal_amortization', 
			renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}
			, summaryRenderer: function(v, params, data){
				if(pecaDataStores.guaranteedLoansSumStore.getCount()>0)
					return Ext.util.Format.number(pecaDataStores.guaranteedLoansSumStore.getAt(0).get('principal_amortization'),'0,000,000,000.00'); }
			}
		,{header: 'Monthly Amortization', width: 100, sortable: true, align: 'right', dataIndex: 'monthly_amortization', 
			renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}
			, summaryRenderer: function(v, params, data){
				if(pecaDataStores.guaranteedLoansSumStore.getCount()>0)
					return Ext.util.Format.number(pecaDataStores.guaranteedLoansSumStore.getAt(0).get('monthly_amortization'),'0,000,000,000.00'); }
			}
		,{header: 'Principal Balance', width: 100, sortable: true, align: 'right', dataIndex: 'principal_balance', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}
			, summaryRenderer: function(v, params, data){
				if(pecaDataStores.guaranteedLoansSumStore.getCount()>0)
					return Ext.util.Format.number(pecaDataStores.guaranteedLoansSumStore.getAt(0).get('principal_balance'),'0,000,000,000.00'); }
		}
	]
);

var statusStore = new Ext.data.SimpleStore({
	fields: ['value', 'name']
	,data: [['A', 'Active'],['I', 'Inactive']]});


var genderStore = new Ext.data.SimpleStore({
	fields: ['initial', 'name']
	,data: [['M', 'Male'],['F', 'Female']]});	

var civilStatusStore = new Ext.data.SimpleStore({
	fields: ['initial', 'name']
	,data: [['1', 'Single'],['2', 'Married'],['3', 'Separated'],['4', 'Widowed']]});	

var beneficiaryStore = new Ext.data.SimpleStore({
	fields: ['initial', 'name']
	,data: [['S', 'Spouse'],['P', 'Parent'],['C', 'Child'],['O', 'Others']]});	
	
var memberDetail = function(){
	return {
		xtype:'form'
		,id:'memberDetail'
		,region:'center'
		,title: 'Details'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,reader: pecaReaders.memberReader
		,buttons:[{
			text: 'Cancel'
			,iconCls: 'icon_ext_cancel'
		    ,handler : function(btn){
				Ext.getCmp('masterFilesCardBody').layout.setActiveItem('pnlMemberInfo');
				Ext.getCmp('memberDetail').getForm().reset();
				Ext.getCmp('memberDetail').getForm().findField('frm_mode').setValue(FORM_MODE_LIST);
				pecaDataStores.memberStore.reload();
		    }
		},{
			text:'Save'
			,iconCls: 'icon_ext_save'
		    ,handler: function(){
		    	var frm = Ext.getCmp('memberDetail').getForm();
				if(!Ext.getCmp('memberDetail').getForm().findField('member[hire_date]').isValid(false)
					|| !Ext.getCmp('memberDetail').getForm().findField('member[email_address]').isValid(false)){
						Ext.getCmp('memberDetail').findById("memberTab").setActiveTab(1);
				} else 
				if(!Ext.getCmp('memberDetail').getForm().findField('member[gender]').isValid(false)
					|| !Ext.getCmp('memberDetail').getForm().findField('member[civil_status]').isValid(false)){
						Ext.getCmp('memberDetail').findById("memberTab").setActiveTab(5);
				
				}

		    	if(frm.isValid()){
		    		if (frm.isModeNew()) {
			        	frm.onSave(frm);
		    		} else {
		    		   	frm.onUpdate(frm);
		            }
		    	}
				var messages = Ext.query("*[class=x-form-invalid-msg]");
				Ext.each(messages, function(message, index) {
					message.style.width ="100%";
				});
		    }
		}]
		,items: [{
		    xtype: 'hidden'
		    ,name: 'frm_mode'
		    ,value: FORM_MODE_LIST
		    ,submitValue: false
		    ,listeners: {'change':{fn: function(obj,value){
            	}}}
			},{
				items: [{
	            layout: 'column'
            	,border: false
				,labelAlign: 'left'
	            ,items: [{
					layout: 'form'
					,width:250
					,border: false
					,items: [{
						xtype: 'textfield'
						,name: 'member[employee_id]'
						,fieldLabel: 'Employee'
		                ,allowBlank: false
						,style: 'text-align: right'
						,anchor: '100%'
		                ,required: true
						,emptyText: 'ID'
						,autoCreate: {tag: 'input', type: 'text', maxlength: '8'}
						,enableKeyEvents: true
						,listeners: {
							keypress: function(txt,evt){
								if (isNaN(String.fromCharCode(evt.getCharCode())) && !evt.isNavKeyPress() && evt.getKey() != evt.BACKSPACE 
										&& evt.getKey() != evt.DELETE){
									evt.preventDefault();
								}else{
									if(evt.getKey() == 32){
										evt.preventDefault();
									}
								}
							}
						}
					}]
					},{
					layout: 'form'
					,labelWidth: 1
					,border: false
					,width: 150
					,items: [{
						xtype: 'textfield'
						,name: 'member[last_name]'	
						,allowBlank: false
						,anchor: '100%'
						,fieldLabel: ' '
						,labelSeparator: ' '
						,emptyText: 'Last Name'
						,msgTarget: 'under'
						,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
					}]
					},{
					layout: 'form'
					,labelWidth: 1
					,border: false
					,width: 150
					,items: [{
						xtype: 'textfield'
						,name: 'member[first_name]'	
						,allowBlank: false
						,anchor: '100%'
						,fieldLabel: ' '
						,labelSeparator: ' '
						,emptyText: 'First Name'
						,msgTarget: 'under'
						,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
					}]
					},{
					layout: 'form'
					,labelWidth: 1
					,border: false
					,width: 150
					,items: [{
						xtype: 'textfield'
						,anchor: '100%'
						,name: 'member[middle_name]'	
						,fieldLabel: ' '
						,labelSeparator: ' '
						,submitValue: false
						,allowBlank: false
						,emptyText: 'Middle Name'
						,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
					}]
					}]
        }]
		},{html: '<br />'},
			memberTab() ]
	    ,isModeNew: function() {
	    	return (Ext.getCmp('memberDetail').getForm().findField('frm_mode').getValue() == FORM_MODE_NEW);
	    }
	    ,setModeNew: function() {
	    	Ext.getCmp('memberDetail').getForm().findField('frm_mode').setValue(FORM_MODE_NEW);
	    	Ext.getCmp('memberDetail').buttons[0].setVisible(true);  //cancel button
	    	Ext.getCmp('memberDetail').buttons[1].setVisible(true);  //save button
	    	Ext.getCmp('memberDetail').getForm().findField('member[employee_id]').focus('',250);
			Ext.getCmp('memberDetail').getForm().findField('member[employee_id]').setReadOnly(false);
			Ext.getCmp('memberDetail').getForm().findField('member[employee_id]').removeClass('x-item-disabled');
			pecaDataStores.companyStore.load();
		}
		,setModeUpdate: function() {
			Ext.getCmp('memberDetail').getForm().findField('frm_mode').setValue(FORM_MODE_UPDATE);
			Ext.getCmp('memberDetail').buttons[0].setVisible(true);  //cancel button
	    	Ext.getCmp('memberDetail').buttons[1].setVisible(true);  //save button
	    	Ext.getCmp('memberDetail').getForm().findField('member[last_name]').focus('',250);
			Ext.getCmp('memberDetail').getForm().findField('member[employee_id]').setReadOnly(true);
			Ext.getCmp('memberDetail').getForm().findField('member[employee_id]').addClass('x-item-disabled');
			pecaDataStores.companyStore.load();
	    }
		,onSave: function(frm){
			var count=0;
			for(var i=1; i<=3; i++){
				var _name = Ext.getCmp('memberDetail').getForm().findField('member[beneficiary_name'+i+']').getValue()
				var _rel = Ext.getCmp('member[beneficiary_relationship'+i+']').getValue();
				var _add = Ext.getCmp('memberDetail').getForm().findField('member[beneficiary_address'+i+']').getValue()
				if( _name!='' || _rel!='' || _add !='' )
					count++;
			}
			frm.submit({
    			url: '/membership/add' 
    			,method: 'POST'
				,timeout: 60000
    			,params: {auth:_AUTH_KEY
						, 'member[non_member]': Ext.getCmp('memberDetail').getForm().findField('member[non_member]').getValue() ? 'Y' : 'N'
						//, 'member[guarantor]': Ext.getCmp('memberDetail').getForm().findField('member[guarantor]').getValue() ? 'Y' : 'N'
						, 'member[guarantor]': 'Y'
						, 'member[middle_name]': Ext.getCmp('memberDetail').getForm().findField('member[middle_name]').getValue()
						, 'member[beneficiaries]': count
						, 'member[created_by]': _USER_ID}
    			,waitMsg: 'Creating Data...'
    			,success: function(form, action) {
					var rec = '[';
					for(var i=1; i<=3; i++){
						var _name = Ext.getCmp('memberDetail').getForm().findField('member[beneficiary_name'+i+']').getValue()
						var _rel = Ext.getCmp('member[beneficiary_relationship'+i+']').getValue();
						var _add = Ext.getCmp('memberDetail').getForm().findField('member[beneficiary_address'+i+']').getValue()
						if( _name!='' || _rel!='' || _add !='' ){
							if(rec != '[')
								rec += ',';
							rec += '{"beneficiary":"'+_name+'","relationship":"'+_rel+'","beneficiary_address":"'+_add+'"}';
						}
					}
					rec += ']';
					Ext.Ajax.request({
						url: '/membership/addBeneficiary' 
						,method: 'POST'
						,params: {'member[employee_id]':Ext.getCmp('memberDetail').getForm().findField('member[employee_id]').getValue()
									,data: rec
									,user: _USER_ID
									,auth:_AUTH_KEY, 'member[modified_by]': _USER_ID}
					});
    				showExtInfoMsg( action.result.msg);
    				frm.setModeUpdate();
    			}
    			,failure: function(form, action) {
    				if(action.result.error_code == 1){
						Ext.Msg.confirm('Confirm Action',action.result.msg,function(btn) {
							if(btn=='yes'){
								form.onUpdate(form);
							}
						});
					}
					else
    					showExtErrorMsg( action.result.msg);
    			}	
    		});
		}
		,onUpdate: function(frm){
			var count=0;
			for(var i=1; i<=3; i++){
				var _name = Ext.getCmp('memberDetail').getForm().findField('member[beneficiary_name'+i+']').getValue()
				var _rel = Ext.getCmp('member[beneficiary_relationship'+i+']').getValue();
				var _add = Ext.getCmp('memberDetail').getForm().findField('member[beneficiary_address'+i+']').getValue()
				if( _name!='' || _rel!='' || _add !='' )
					count++;
			}
			frm.submit({
    			url: '/membership/update' 
    			,method: 'POST'
				,timeout: 60000
    			,waitMsg: 'Updating Data...'
    			,params: { auth:_AUTH_KEY
							,'member[non_member]': Ext.getCmp('memberDetail').getForm().findField('member[non_member]').getValue() ? 'Y' : 'N'
							//,'member[guarantor]': Ext.getCmp('memberDetail').getForm().findField('member[guarantor]').getValue() ? 'Y' : 'N'
							,'member[guarantor]': 'Y'
							, 'member[beneficiaries]': count
							,'member[middle_name]': Ext.getCmp('memberDetail').getForm().findField('member[middle_name]').getValue()
							,'member[modified_by]': _USER_ID}
    			,success: function(form, action) {
					Ext.Ajax.request({
						url: '/membership/deleteBeneficiaries' 
						,method: 'POST'
						,params: {'member[employee_id]':Ext.getCmp('memberDetail').getForm().findField('member[employee_id]').getValue()
									,auth:_AUTH_KEY, 'member[modified_by]': _USER_ID}
						,success: function(response, opts) {
							var rec = '[';
							for(var i=1; i<=3; i++){
								var _name = Ext.getCmp('memberDetail').getForm().findField('member[beneficiary_name'+i+']').getValue()
								var _rel = Ext.getCmp('member[beneficiary_relationship'+i+']').getValue();
								var _add = Ext.getCmp('memberDetail').getForm().findField('member[beneficiary_address'+i+']').getValue()
								if( _name!='' || _rel!='' || _add !='' ){
									if(rec != '[')
										rec += ',';
									rec += '{"beneficiary":"'+_name+'","relationship":"'+_rel+'","beneficiary_address":"'+_add+'"}';
								}
							}
							rec += ']';
							Ext.Ajax.request({
								url: '/membership/addBeneficiary' 
								,method: 'POST'
								,params: {'member[employee_id]':Ext.getCmp('memberDetail').getForm().findField('member[employee_id]').getValue()
											,data: rec
											,user: _USER_ID
											,auth:_AUTH_KEY, 'member[modified_by]': _USER_ID}
							});		
						}
					});
    				showExtInfoMsg( action.result.msg);
        			frm.setModeUpdate();
    			}
    			,failure: function(form, action) {
    				showExtErrorMsg( action.result.msg);
    			}	
    		});
		}
	};
};

var memberTab = function(){
	return {
		xtype: 'tabpanel'
		,id:'memberTab'
		,titlebar:false
		,activeTab:0
		,bodyStyle: 'background:transparent;'
		,height: 335
		,defaults: {autoScroll: true}
		,items:[
			memInfoTab()
			,employmentInfoTab()
			,loanInfoTab()
			,guaranteedLoansInfoTab()
			,bankInfoTab()
			,personalInfoTab()
			,transactionHistoryTab()
		]
	};
}


var memInfoTab = function(){
	return {
		layout:'form'
		,title:'Membership'
		,id:'memInfoTab'
		,bodyStyle: 'background:transparent; padding: 10px;'
		,items: [ {
				layout: 'column'
				,border: false
				,labelAlign: 'left'
				,items:[{
					layout: 'form'
					,border: false
					,items: [{
						xtype: 'datefield'
						,value: _TODAY
						,columnWidth: .5
						,width: 200
						,fieldLabel: 'Membership Date'
						,name: 'member[member_date]'
						,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
					}]
				}
				,{
					layout: 'form'
					,border: false
					,items: [{
						xtype:'checkbox'
						,columnWidth: .5
						,boxLabel: 'Co-Maker'
						,name: 'member[guarantor]'
						,checked: true
						,hidden: true
						,submitValue: false
					}]
				}
				,{
					layout: 'form'
					,border: false
					,items: [{
						xtype:'checkbox'
						,columnWidth: .5
						,boxLabel: 'Non-member'
						,name: 'member[non_member]'
						,submitValue: false
					}]
				}]
		},{
			layout: 'form'
			,border: false
			,items: [{
				xtype: 'combo'
				,hiddenName: 'member[member_status]'
				,store: statusStore
				,fieldLabel: 'Status'
				,width: 200
				,mode: 'local'
				,displayField: 'name'
				,valueField: 'value'
				,editable: 'false'
				,emptyText: 'Please Select'
				,forceSelection: true
				,triggerAction: 'all'
				,selectOnFocus: true
				,editable: false
				//,required: true
				//,allowBlank: false
				
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
						,name: 'member[beneficiary_name1]'
						,submitValue: false
						,autoCreate: {tag: 'input', type: 'text', maxlength: '100'}
						,validator: function(){
							return true;
						}
					},{
						xtype: 'textfield'
						,anchor: '100%'
						,name: 'member[beneficiary_name2]'
						,submitValue: false
						,autoCreate: {tag: 'input', type: 'text', maxlength: '100'}
						,validator: function(){
							return true;
						}
					},{
						xtype: 'textfield'
						,anchor: '100%'
						,name: 'member[beneficiary_name3]'
						,submitValue: false
						,autoCreate: {tag: 'input', type: 'text', maxlength: '100'}
						,validator: function(){
							return true;
						}
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
						,id: 'member[beneficiary_relationship1]'
						,store: beneficiaryStore
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
					},{
						xtype: 'combo'
						,anchor: '100%'
						,id: 'member[beneficiary_relationship2]'
						,store: beneficiaryStore
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
					},{
						xtype: 'combo'
						,anchor: '100%'
						,id: 'member[beneficiary_relationship3]'
						,store: beneficiaryStore
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
						,name: 'member[beneficiary_address1]'
						,submitValue: false
						,autoCreate: {tag: 'input', type: 'text', maxlength: '100'}
						,validator: function(){
							return true;
						}
					},{
						xtype: 'textfield'
						,anchor: '100%'
						,name: 'member[beneficiary_address2]'
						,submitValue: false
						,autoCreate: {tag: 'input', type: 'text', maxlength: '100'}
						,validator: function(){
							return true;
						}
					},{
						xtype: 'textfield'
						,anchor: '100%'
						,name: 'member[beneficiary_address3]'
						,submitValue: false
						,autoCreate: {tag: 'input', type: 'text', maxlength: '100'}
						,validator: function(){
							return true;
						}
					}]
				}]
	
		}]
			
		}]
	};
}
var employmentInfoTab = function(){
	return {
		layout:'form'
		,title:'Employment'
		,bodyStyle: 'background:transparent; padding: 10px;'
		,id: 'employmentInfoTab'
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
					,anchor: '95%'
					,name: 'member[TIN]'
					,autoCreate: {tag: 'input', type: 'text', maxlength: '20'}
					,validator: function(){
						return true;
					}
				},{
					xtype: 'datefield'
					,fieldLabel: 'Date of Employment'
					,anchor: '95%'
					,name: 'member[hire_date]'
					//,required: true
					//,allowBlank: false
					,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
				},{
					xtype: 'datefield'
					,fieldLabel: 'Separation Date'
					,anchor: '95%'
					,name: 'member[work_date]'
					,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}

				},{
					xtype: 'combo'
					,anchor: '95%'
					,hiddenName: 'member[company_code]'
					,store: pecaDataStores.companyStore
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
					,name: 'member[department]'
					,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
					,validator: function(){
						return true;
					}
				},{
					xtype: 'textfield'
					,fieldLabel: 'Position'
					,anchor: '95%'
					,name: 'member[position]'
					,autoCreate: {tag: 'input', type: 'text', maxlength: '20'}
					,validator: function(){
						return true;
					}
				},{
					xtype: 'textfield'
					,fieldLabel: 'Office No.'
					,anchor: '95%'
					,name: 'member[office_no]'
					,autoCreate: {tag: 'input', type: 'text', maxlength: '15'}
					,validator: function(){
						return true;
					}
				},{
					xtype: 'textfield'
					,fieldLabel: 'E-mail Address'
					//,required: true
					//,allowBlank: false
					,vtype: 'email'
					,msgTarget: 'under'
					,anchor: '95%'
					,name: 'member[email_address]'
					,autoCreate: {tag: 'input', type: 'text', maxlength: '100'}
				}]
			}]
		}]
	};
}

var loanInfoTab = function(){
	return {
		xtype:'panel'
		,title:'Loan Information'
		,bodyStyle: 'background:transparent; padding: 10px;'
		,items: [memberLoanInfoList()]
	};
}

var guaranteedLoansInfoTab = function(){
	return {
		xtype:'panel'
		,title:'Co-Made Loans'
		,bodyStyle: 'background:transparent; padding: 10px;'
		,items: [guaranteedLoansList()]
	};
}

var bankInfoTab= function(){
	return {
		layout:'form'
		,title:'Bank Information'
		,bodyStyle: 'background:transparent; padding: 10px;'
		,items:[{
				xtype: 'textfield'
				,fieldLabel: 'Bank'
				,width: 200
				,name: 'member[bank]'
				,autoCreate: {tag: 'input', type: 'text', maxlength: '1'}
				,validator: function(){
					return true;
				}
			},{
				xtype: 'textfield'
				,fieldLabel: 'Account Number'
				,width: 200
				,style: 'text-align: left'
				,name: 'member[bank_account_no]'
				,autoCreate: {tag: 'input', type: 'text', maxlength: '15'}
				,validator: function(){
					return true;
				}
		}]
	};
}

var personalInfoTab= function(){
	return {
		layout:'form'
		,title:'Personal'
		,bodyStyle: 'background:transparent; padding: 10px;'
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
				,name: 'member[birth_date]'
				,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
			},{
				xtype: 'combo'
				,fieldLabel: 'Gender'
				,anchor: '95%'
				,hiddenName: 'member[gender]'
				,store: genderStore
				,mode: 'local'
				,displayField: 'name'
				,valueField: 'initial'
				,editable: 'false'
				,emptyText: 'Please Select'
				,forceSelection: true
				,triggerAction: 'all'
				,selectOnFocus: true
				,editable: false
				//,required: true
				//,allowBlank: false
				
			},{
				xtype: 'combo'
				,fieldLabel: 'Civil Status'
				,anchor: '95%'
				,hiddenName: 'member[civil_status]'
				,store: civilStatusStore
				,mode: 'local'
				,displayField: 'name'
				,valueField: 'initial'
				,editable: 'false'
				,emptyText: 'Please Select'
				,forceSelection: true
				,triggerAction: 'all'
				,selectOnFocus: true
				,editable: false
				//,required: true
				//,allowBlank: false

			},{
				xtype: 'textfield'
				,fieldLabel: 'Name of Spouse'
				,anchor: '95%'
				,name: 'member[spouse]'
				,autoCreate: {tag: 'input', type: 'text', maxlength: '40'}
				,validator: function(){
					return true;
				}
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
				,name: 'member[address_1]'
				,autoCreate: {tag: 'input', type: 'text', maxlength: '35'}
				,validator: function(){
					return true;
				}
			},{
				xtype: 'textfield'
				,fieldLabel: ' '
				,labelSeparator: ' '
				,anchor: '95%'
				,name: 'member[address_2]'
				,autoCreate: {tag: 'input', type: 'text', maxlength: '35'}
				,validator: function(){
					return true;
				}
			},{
				xtype: 'textfield'
				,fieldLabel: ' '
				,labelSeparator: ' '
				,anchor: '95%'
				,name: 'member[address_3]'
				,autoCreate: {tag: 'input', type: 'text', maxlength: '35'}
				,validator: function(){
					return true;
				}
			},{
				html: '<br />'
			},{
				xtype: 'textfield'
				,fieldLabel: 'Home Phone'
				,anchor: '95%'
				,name: 'member[home_phone]'
				,autoCreate: {tag: 'input', type: 'text', maxlength: '15'}
				,validator: function(){
					return true;
				}
			},{
				xtype: 'textfield'
				,fieldLabel: 'Mobile Number'
				,anchor: '95%'
				,name: 'member[mobile_no]'
				,autoCreate: {tag: 'input', type: 'text', maxlength: '15'}
				,validator: function(){
					return true;
				}
			}]
		}]
	}]
	};
}

var transactionHistoryTab = function(){
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
				,items: [transactionHistoryList()]
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
					,id: 'member[capital_contribution]'
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
					,id: 'member[required_balance]'
					,fieldLabel: 'Required Balance'
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
					,id: 'member[maximum_withdrawable_amount]'
					,fieldLabel: 'Maximum Withdrawable Amount'
				}]
			}]
			
		}]
	};
}

var beneficiaryStorage = new Ext.data.Store({
		url: '/membership/readBeneficiary'
	    ,reader: new Ext.data.JsonReader({
			totalProperty: 'total'	
			,root: 'data'
			},[
				{name: 'beneficiary', mapping: 'beneficiary', type: 'string'}
				,{name: 'relationship', mapping: 'relationship', type: 'string'}
				,{name: 'beneficiary_address', mapping: 'beneficiary_address'}
			]
		)
	    ,baseParams: {auth:_AUTH_KEY}
		,listeners: {
			load: function(){
				for(var i=0; i<beneficiaryStorage.getCount(); i++){
					var foo = beneficiaryStorage.getAt(i);
					var j=i+1;
					Ext.getCmp('memberDetail').getForm().findField('member[beneficiary_name'+j+']').setValue(foo.get('beneficiary'))
					Ext.getCmp('member[beneficiary_relationship'+j+']').setValue(foo.get('relationship'));
					Ext.getCmp('memberDetail').getForm().findField('member[beneficiary_address'+j+']').setValue(foo.get('beneficiary_address'))
				}
			}
		}
});

var mri_fip_changed = false; 

var memberLoanWin = function(){
	return new Ext.Window({
		id: 'memberLoanWin'
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
		,items:[ memberLoanDetails() ]
		,listeners:{
			beforeclose: function(panel){
				var frm = Ext.getCmp('member_LoanDetails').getForm();
				frm.findField('loan_no').focus(); //used to activate change event in mri amount, due date & fip amount, due date textfields
				if(frm.isValid() && mri_fip_changed){
					frm.submit({
						url: '/membership/updateInsurance' 
						,method: 'POST'
						,params: {auth:_AUTH_KEY
							,'loan_no': Ext.getCmp('member_LoanDetails').getForm().findField('loan_no').getValue()
							,'mri_due_amount': Ext.getCmp('member_LoanDetails').getForm().findField('mri_due_amount').getValue()
							,'mri_due_date': Ext.getCmp('member_LoanDetails').getForm().findField('mri_due_date').getValue()
							,'fip_due_amount': Ext.getCmp('member_LoanDetails').getForm().findField('fip_due_amount').getValue()
							,'fip_due_date': Ext.getCmp('member_LoanDetails').getForm().findField('fip_due_date').getValue()
						}
						,waitMsg: 'Updating Data...'
						,success: function(form, action) {
							showExtInfoMsg( action.result.msg);
						}
						,failure: function(form, action) {
							showExtErrorMsg( action.result.msg);
						}
					});
				}
			}
		}
        /* ,buttons:[{
 			text: 'Cancel'
 			,iconCls: 'icon_ext_cancel'
 		    ,handler : function(btn){
 				Ext.getCmp('memberLoanWin').close();				
 		    }
 		}] */
	});
};

var memberLoanDetailTab = function(){
	return {
		xtype: 'tabpanel'
		,titlebar:false
		,activeTab:0
		,anchor: '97%'
		,bodyStyle: 'background:transparent;'
		,height: 335
		,defaults: {autoScroll: true}
		,items:[
			memberLoanPaymentsTab()
			,memberChargesTab()
			,memberCoMakersTab()
		]
	};
}

var memberLoanPaymentsTab= function(){
	return {
		xtype:'panel'
		,title:'Loan Payments'
		,bodyStyle: 'background:transparent; padding: 5px;'
		,items: [memberLoanPaymentsList()]
	};
};

var memberCoMakersTab= function(){
	return {
		xtype:'panel'
		,title:'Co-Makers'
		,bodyStyle: 'background:transparent; padding: 5px;'
		,items: [memberCoMakersPanel()]
	};
};

var memberChargesTab= function(){
	return {
		xtype:'panel'
		,title:'Charges'
		,bodyStyle: 'background:transparent; padding: 5px;'
		,items: [memberChargesList()]
	};
};

var memberLoanDetails = function(){
	return{
	xtype: 'form'
	,id: 'member_LoanDetails'
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
			,labelWidth: 175
			,bodyStyle: 'background:transparent;'
			,border: false
			,items:[{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'loan_no'
					,anchor: '95%'
					,id: 'member_loan_info_loan_no'
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
				},{
					xtype: 'hidden'
					,submitValue: false
					,name: 'loan_code'
				},{
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
			,labelWidth: 175
			,bodyStyle: 'background:transparent;'
			,border: false
			,items:[{
					xtype: 'checkbox'
					,readOnly: true
					,submitValue: false
					,name: 'restructure'
					,style: 'background:transparent; margin-left:85%; text-align: right; border:0'
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
				}
				,{
					xtype: 'moneyfield'
					,border: false
					,submitValue: false
					,name: 'mri_due_amount'
					,anchor: '95%'
					,allowNegative: false
					,style: 'text-align: right'
					,fieldLabel: 'MRI Due Amount'
					,enableKeyEvents: true
					,maxLength: 16
	                ,maxValue: 9999999999.99
					,autoCreate: {tag: 'input', type: 'text', maxlength: '13'}
					,listeners:{
						// 'change':{
							// fn:function(field,newVal,oldVal) {
								// mri_fip_changed = true
							// }
						// }
						keypress: function(txt,evt){
							mri_fip_changed = true
						}
					}
				}
				,{
					xtype: 'datefield'
					,border: false
					,submitValue: false
					,name: 'mri_due_date'
					,anchor: '95%'
					,style: 'text-align: right'
					,fieldLabel: 'MRI Due Date'
					,maxLength: 10
					,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
					,listeners:{
						'change':{
							fn:function(field,newVal,oldVal) {
								mri_fip_changed = true
							}
						}
					}
				}
				,{
					xtype: 'moneyfield'
					,border: false
					,submitValue: false
					,name: 'fip_due_amount'
					,allowNegative: false
					,anchor: '95%'
					,style: 'text-align: right'
					,fieldLabel: 'FIP Due Amount'
					,enableKeyEvents: true
					,maxLength: 16
	                ,maxValue: 9999999999.99
					,autoCreate: {tag: 'input', type: 'text', maxlength: '13'}
					,listeners:{
						// 'change':{
							// fn:function(field,newVal,oldVal) {
								// mri_fip_changed = true
							// }
						// }
						keypress: function(txt,evt){
							mri_fip_changed = true
						}
					}
				}
				,{
					xtype: 'datefield'
					,border: false
					,submitValue: false
					,name: 'fip_due_date'
					,anchor: '95%'
					,style: 'text-align: right'
					,fieldLabel: 'FIP Due Date'
					,maxLength: 10
					,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
					,listeners:{
						'change':{
							fn:function(field,newVal,oldVal) {
								mri_fip_changed = true
							}
						}
					}
				}]
		}]
	}, memberLoanDetailTab()]
	}
};

var memberList = function(){
	return {
		xtype: 'grid'
		,id: 'memberList'
		,titlebar: false
		,store: pecaDataStores.memberStore
		,region: 'center'
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 425
		,anchor: '100%'
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit: true
			,scrollOffset: 0
		}
		,cm: memberColumns
		
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
					pecaDataStores.memberLoanInfoSumStore.load({params: { employee_id :rec.get('member[employee_id]') }});
					pecaDataStores.guaranteedLoansSumStore.load({params: { employee_id :rec.get('member[employee_id]') }});
					beneficiaryStorage.load({params: { 'member[employee_id]':rec.get('member[employee_id]'),start:0, limit:MAX_PAGE_SIZE}});
					Ext.getCmp('masterFilesCardBody').layout.setActiveItem('pnlMemberInfoDetail');
					Ext.getCmp('memberDetail').getForm().setModeUpdate();
					Ext.getCmp('memberDetail').getForm().load({
				    	url: '/membership/show'
				    	,params: {'member[employee_id]':(rec.get('member[employee_id]'))
									,auth:_AUTH_KEY}
				    	,method: 'POST'
				    	,waitMsgTarget: true
						,success: function(response, opts) {
							pecaDataStores.guaranteedLoansStore.load({params: { employee_id :rec.get('member[employee_id]'),start:0, limit:MAX_PAGE_SIZE}});
							pecaDataStores.transactionHistoryStoreDesc.load({params: { employee_id :rec.get('member[employee_id]'),start:0, limit:MAX_PAGE_SIZE}});
							Ext.Ajax.request({
								url: '/membership/showBalanceInfo/'
								,params: {'member[employee_id]':(rec.get('member[employee_id]'))
											,auth:_AUTH_KEY}  
								,success: function(response, opts) {
									var ret = Ext.decode(response.responseText);
									Ext.getCmp('member[capital_contribution]').setValue(Ext.util.Format.number(Ext.num(ret.data.capconBal,0),'0,000,000,000.00'));
									Ext.getCmp('member[required_balance]').setValue(Ext.util.Format.number(Ext.num(ret.data.reqBal,0),'0,000,000,000.00'));
									Ext.getCmp('member[maximum_withdrawable_amount]').setValue(Ext.util.Format.number(Ext.num(ret.data.maxWdwlAmount,0),'0,000,000,000.00'));
								}
								,failure: function(response, opts) {}
							});
							pecaDataStores.memberLoanInfoStore.load({params: { employee_id :rec.get('member[employee_id]'),start:0, limit:MAX_PAGE_SIZE}});
						}
					});				

				}
			}
			,'render':{
				scope:this
				,fn:function(grid){
					pecaDataStores.memberStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
				}
			}
		}
		,tbar:[{
			xtype: 'label'
			,text: 'Employee :'
            ,fieldLabel: ' '
            ,labelSeparator: ' '
		},{
            xtype: 'textfield'
            ,width: 70
			,id: 'memberEmpId'
			,name: 'memberEmpId'
            ,hideLabel: true
            ,emptyText: 'ID'
			,autoCreate: {tag: 'input', type: 'text', maxlength: '8'}
			,enableKeyEvents: true
			    		,style: 'text-align: right'
			,listeners: {
				specialkey: function(frm,evt){
					if (evt.getKey() == evt.ENTER) {
						pecaDataStores.memberStore.load({params:{start:0, limit:MAX_PAGE_SIZE}});						
					}
				},
				keypress: function(txt,evt){
					if (isNaN(String.fromCharCode(evt.getCharCode())) && !evt.isNavKeyPress() && evt.getKey() != evt.BACKSPACE 
							&& evt.getKey() != evt.DELETE){
						evt.preventDefault();
					}else{
						if(evt.getKey() == 32){
							evt.preventDefault();
						}
					}
				}
			}
		},' ',{
            xtype: 'textfield'
            ,width: 100
			,id: 'memberLastname'
            ,hideLabel: true
            ,emptyText: 'Last Name'
			,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
			,listeners: {
				specialkey: function(frm,evt){
					if (evt.getKey() == evt.ENTER) {
						pecaDataStores.memberStore.load({params:{start:0, limit:MAX_PAGE_SIZE}});						
					}
				}
			}
    	},' ',{
            xtype: 'textfield'
            ,width: 100
			,id: 'memberFirstname'
            ,hideLabel: true
            ,emptyText: 'First Name'
			,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
			,listeners: {
				specialkey: function(frm,evt){
					if (evt.getKey() == evt.ENTER) {

						pecaDataStores.memberStore.load({params:{start:0, limit:MAX_PAGE_SIZE}});						
					}
				}
			}
        },' ',{
			text:'Search'
			,iconCls: 'icon_ext_search'
			,scope:this
			,handler:function(btn) {
				pecaDataStores.memberStore.load({params:{start:0, limit:MAX_PAGE_SIZE}});	
			}
		},'-'
		,{
			text:'New'
			,tooltip:'Add a New member'
			,iconCls: 'icon_ext_add'
			,scope:this
			,handler:function(btn) {

				Ext.getCmp('memberDetail').getForm().reset();
				//Ext.getCmp('employmentInfoTab').getForm().reset();
				Ext.getCmp('member[required_balance]').setValue('0.00');
				Ext.getCmp('member[capital_contribution]').setValue('0.00');
				Ext.getCmp('member[maximum_withdrawable_amount]').setValue('0.00');
				
				Ext.getCmp('memberDetail').getForm().findField('member[TIN]').reset();
				Ext.getCmp('memberDetail').getForm().findField('member[hire_date]').reset();
				Ext.getCmp('memberDetail').getForm().findField('member[work_date]').reset();
				Ext.getCmp('memberDetail').getForm().findField('member[company_code]').reset();
				Ext.getCmp('memberDetail').getForm().findField('member[department]').reset();
				Ext.getCmp('memberDetail').getForm().findField('member[position]').reset();
				Ext.getCmp('memberDetail').getForm().findField('member[office_no]').reset();
				Ext.getCmp('memberDetail').getForm().findField('member[email_address]').reset();
				Ext.getCmp('memberDetail').getForm().findField('member[member_date]').setValue(_TODAY);
				Ext.getCmp('memberDetail').getForm().findField('member[birth_date]').setValue(_TODAY);

				Ext.getCmp('masterFilesCardBody').layout.setActiveItem('pnlMemberInfoDetail');
				memberDetail().setModeNew();
				pecaDataStores.memberLoanInfoStore.load();
				pecaDataStores.guaranteedLoansStore.load();
				pecaDataStores.transactionHistoryStoreDesc.load();
				if(pecaDataStores.memberLoanInfoSumStore.getTotalCount()>0)
					pecaDataStores.memberLoanInfoSumStore.removeAll();
				if(pecaDataStores.guaranteedLoansSumStore.getTotalCount()>0)
					pecaDataStores.guaranteedLoansSumStore.removeAll();
			}
		}]
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.memberStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};

var memberLoanInfoList = function(){
	return {
		xtype: 'grid'
		,id: 'memberLoanInfoList'
		,titlebar: false
		,store: pecaDataStores.memberLoanInfoStore
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 270
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,plugins: [summaryLoanMember]
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
					mri_fip_changed = false;
					memberLoanWin().show();	
					Ext.getCmp('member_LoanDetails').getForm().load({
				    	url: '/membership/showLoanInfo'
				    	,params: {'loan_no':(rec.get('loan_no'))
									,auth:_AUTH_KEY}
				    	,method: 'POST'
				    	,waitMsgTarget: true
				    	,success: function(response, opts) {
							memberLoanPaymentsStore.load({params: { loan_no :rec.get('loan_no'),start:0, limit:MAX_PAGE_SIZE}});
							memberChargesStore.load({params: {loan_no :rec.get('loan_no'),start:0, limit:MAX_PAGE_SIZE}});
							memberCoMakersStore.load({params: {loan_no :rec.get('loan_no'),start:0, limit:MAX_PAGE_SIZE}, callback: function(r, options, success) {
								/* if(memberCoMakersStore.getTotalCount() == 0){
									Ext.getCmp('member_noCoMaker').setValue(true);
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
		,cm: memberLoanInfoColumns
		,bbar: new Ext.PagingToolbar({
	        store: pecaDataStores.memberLoanInfoStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};

var guaranteedLoansList = function(){
	return {
		xtype: 'grid'
		,id: 'guaranteedLoansList'
		,titlebar: false
		,store: pecaDataStores.guaranteedLoansStore
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 270
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,plugins: [summaryComakerMember]
		,viewConfig: {
			forceFit:true
			,scrollOffset:13
		}
		,cm: guaranteedLoansColumns
		,bbar: new Ext.PagingToolbar({
	        store: pecaDataStores.guaranteedLoansStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};
var memberLoanPaymentsColumns = new Ext.grid.ColumnModel( 
[
	{header: 'Payment Date', width: 50, sortable: true, align: 'center', dataIndex: 'payment_date'}
	,{header: 'Amount', width: 75, sortable: true, dataIndex: 'amount', align: 'right', renderer:function(value,rec){
		return Ext.util.Format.number(value,'0,000,000,000.00');}}
	,{header: 'Interest', width: 75, sortable: true, align: 'right', dataIndex: 'interest', renderer:function(value,rec){
		return Ext.util.Format.number(value,'0,000,000,000.00');}}
	,{header: 'Description', width: 100, sortable: true, dataIndex: 'description'}
	,{header: 'Balance', width: 75, sortable: true, align: 'right', dataIndex: 'balance', renderer:function(value,rec){
		return Ext.util.Format.number(value,'0,000,000,000.00');}}
]
);
var memberLoanPaymentsStore = new Ext.data.Store({
	//restful: true
	proxy: new Ext.data.HttpProxy({
			url: '/membership/readLoanPayment'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						if(Ext.getCmp('member_loan_info_loan_no').getValue())
							params.loan_no = Ext.getCmp('member_loan_info_loan_no').getValue();
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
});

var memberLoanPaymentsList = function(){
	return {
		xtype: 'grid'
		,id: 'memberLoanPaymentsList'
		,titlebar: false
		,store: memberLoanPaymentsStore
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 270
		,width: 664
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit:true
			,scrollOffset:13
		}
		,cm: memberLoanPaymentsColumns
		,bbar: new Ext.PagingToolbar({
	        store: memberLoanPaymentsStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};

var memberCoMakersPanel = function(){
	return {
		border: false
		,layout: 'column'
		,bodyStyle: 'background:transparent;'
		,items:[{
                labelWidth: 1
                ,labelAlign: 'left'
                ,layout: 'form'
                ,columnWidth: 0.30
                ,border: false
                ,items: [{
                    xtype: 'textfield'
                    ,id: 'newmemberCoMakerID'	
                    ,anchor: '100%'
                    ,emptyText: 'ID'
                	,submitValue: false	
					,style: 'text-align:right'
					,autoCreate: {tag: 'input', type: 'text', maxlength: '8'}
                }]
            }
            ,{
                labelWidth: 1
                ,labelAlign: 'left'
                ,layout: 'form'
                ,columnWidth: 0.30
                ,border: false
                ,items: [{
                    xtype: 'textfield'
                	,id: 'newmemberCoMakerLastName'
                    ,anchor: '100%'
                    ,emptyText: 'Last Name'
                	,submitValue: false
					,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
                }]
            }
            ,{
	            labelWidth: 1
	            ,labelAlign: 'left'
	            ,layout: 'form'
	            ,columnWidth: 0.30
	            ,border: false
	            ,items: [{
                    xtype: 'textfield'
                	,id: 'newmemberCoMakerFirstName'
                    ,anchor: '100%'
                    ,emptyText: 'First Name'
                	,submitValue: false
					,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
                }]
            }
            ,{
            	width: 75
            	,id: 'newmemberCoMakerSearchBtn'
                ,xtype: 'button'
                ,text: 'Search'
				,iconCls: 'icon_ext_search'
            	,handler: function(){
	        		pecaDataStores.newloanCoMakeremployeeStore2.load({params: {start:0, limit:MAX_PAGE_SIZE}});
	        		newmember_EmpCoMakerListWin().show();
			    }
        },		
		memberCoMakersList()
			,{
			//layout: 'form'
			columnWidth: 0.4
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
				,id: 'member_noCoMaker'
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

var newmember_EmpCoMakerListWin = function(){
	return new Ext.Window({
		id: 'newmember_EmpCoMakerListWin'
		,title: 'Employee List'
		,frame: true
		,layout: 'form'
		,width: 600
		,plain: true
		,modal: true
		,resizable: false
		,closable: false
		,constrainHeader: true
		,bodyStyle:{"padding":"5px"}
		,loadMask: true	
		,items:[ newmember_EmpCoMakerList() ]
        ,buttons:[{
 			text: 'Cancel'
 			,iconCls: 'icon_ext_cancel'
 		    ,handler : function(btn){
 				Ext.getCmp('newmember_EmpCoMakerListWin').close();				
 		    }
 		}]
	});
};

var newmember_EmpCoMakerList = function(){
	return {
		xtype: 'grid'
		,id: 'newmember_EmpCoMakerList'
		,titlebar: false
		,store: pecaDataStores.newloanCoMakeremployeeStore2
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 425
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit:true
			,scrollOffset:0
		}
		,cm: employeeColumns
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
					if( rec.get('employee_id') == Ext.getCmp('memberDetail').getForm().findField('member[employee_id]').getValue()){
						showExtErrorMsg( "Employee can't be a co-maker of the applied loan.");
					}else{						
	        			var data = new memberCoMakersStore.recordType({
	        				'employee_id' : rec.get('employee_id')
	        				,'employee_name' : rec.get('last_name') + "," + rec.get('first_name') + " " + Ext.util.Format.substr(rec.get('middle_name'), 1, 1) + "."
	        			});
						Ext.Ajax.request({
							url: '/loan/addMemberCoMaker' 
							,method: 'POST'
							,form: Ext.get('frmDownload')
							,params: {loan_no: Ext.getCmp('member_LoanDetails').getForm().findField('member_loan_info_loan_no').getValue()
										,employee_id: rec.get('employee_id')
										,loan_code: Ext.getCmp('member_LoanDetails').getForm().findField('loan_code').getValue()
										,user: _USER_ID
										,auth:_AUTH_KEY}
							,isUpload: true
							,success: function(response, opts) {
								var obj = Ext.decode(response.responseText);
								if(obj.success){
									showExtInfoMsg(obj.msg);
									memberCoMakersStore.load();
								}else{
									showExtErrorMsg(obj.msg);
								}
							}
							,failure: function(response, opts) {
								var obj = Ext.decode(response.responseText);
								showExtErrorMsg(obj.msg);
							}
						});
					}
					Ext.getCmp('newmember_EmpCoMakerListWin').close.defer(1,Ext.getCmp('newmember_EmpCoMakerListWin'));
				}
			}
		}
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.newloanCoMakeremployeeStore2
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: 'No records found'
	    })
	};
};

var memberCoMakersColumns = new Ext.grid.ColumnModel( 
[
	{header: 'Employee ID', width: 50, sortable: true, dataIndex: 'employee_id'}
	,{header: 'Employee Name', width: 100, sortable: true, dataIndex: 'employee_name'}
]
);
var memberCoMakersStore = new Ext.data.Store({
	//restful: true
	proxy: new Ext.data.HttpProxy({
			url: '/membership/readLoanComakers'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						if(Ext.getCmp('member_loan_info_loan_no').getValue())
							params.loan_no = Ext.getCmp('member_loan_info_loan_no').getValue();
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
			,{name: 'last_name', mapping: 'last_name', type: 'string'}
			,{name: 'employee_name', mapping: 'last_name', type: 'string', convert:function(value,rec){
				return value + ', ' + rec.first_name + ' ' + rec.middle_name;
			}}
		]
	)
	,baseParams: {auth:_AUTH_KEY}
});
var memberCoMakersList = function(){
	return {
		xtype: 'grid'
		,id: 'memberCoMakersList'
		,titlebar: false
		,columnWidth: 0.6
		,store: memberCoMakersStore
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
		,cm: memberCoMakersColumns
		,tbar:[{
			text:'Remove'
			,tooltip:'Delete Selected Co-Maker'
			,iconCls: 'icon_ext_del'
			,scope:this
			,handler:function(btn) {
				var index = Ext.getCmp('memberCoMakersList').getSelectionModel().getSelected();
		        if (!index) {
		        	showExtInfoMsg( "Please select a Co-Maker to delete.");
		            return false;
		        }
				Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
					if(btn=='yes') {
						Ext.Ajax.request({
							url: '/loan/deleteMemberComaker' 
							,method: 'POST'
							,form: Ext.get('frmDownload')
							,params: {employee_id: index.get('employee_id')
										,loan_no: Ext.getCmp('member_LoanDetails').getForm().findField('loan_no').getValue()
										,auth:_AUTH_KEY}
							,isUpload: true
							,success: function(response, opts) {
								var obj = Ext.decode(response.responseText);
								if(obj.success){
									showExtInfoMsg(obj.msg);
								}else{
									showExtErrorMsg(obj.msg);
								}
							}
							,failure: function(response, opts) {
								var obj = Ext.decode(response.responseText);
								showExtErrorMsg(obj.msg);
							}
						});					
						memberCoMakersStore.load();
					}
				});
			}
		}]
		,bbar: new Ext.PagingToolbar({
	        store: memberCoMakersStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};

var memberChargesColumns = new Ext.grid.ColumnModel( 
[
	{header: 'Description', width: 100, sortable: true, dataIndex: 'description'}
	,{header: 'Amount', width: 100, sortable: true, align: 'right', dataIndex: 'amount', renderer:function(value,rec){
		return Ext.util.Format.number(value,'0,000,000,000.00');}}
]
);
var memberChargesStore = new Ext.data.Store({
	//restful: true
	proxy: new Ext.data.HttpProxy({
			url: '/membership/readLoanCharges'
			,listeners:{
				'beforeload':{
					scope:this
					,fn:function(dataproxy,params ){
						if(Ext.getCmp('member_loan_info_loan_no').getValue())
							params.loan_no = Ext.getCmp('member_loan_info_loan_no').getValue();
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
});
var memberChargesList = function(){
	return {
		xtype: 'grid'
		,id: 'memberChargesList'
		,titlebar: false
		,store: memberChargesStore
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
		,cm: memberChargesColumns
		,bbar: new Ext.PagingToolbar({
	        store: memberChargesStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};

var transactionHistoryList = function(){
	return {
		xtype: 'grid'
		,id: 'transactionHistoryList'
		,titlebar: false
		,anchor: '100%'
		,store: pecaDataStores.transactionHistoryStoreDesc
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 270
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit:true
			,scrollOffset:18
		}
		,cm: transactionHistoryColumns
		,bbar: new Ext.PagingToolbar({
	        store: pecaDataStores.transactionHistoryStoreDesc
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};

