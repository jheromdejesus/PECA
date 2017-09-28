//Let's pretend we rendered our grid-columns with meta-data from our ORM framework.
var lpColumns =  new Ext.grid.ColumnModel( 
	[
     {id: 'loan_no', header: 'Loan No.', sortable: true, width: 100, align: 'right', dataIndex: 'lp[loan_no]'}
     ,{header: 'Employee ID', sortable: true, width: 100, align: 'right', dataIndex: 'lp[employee_id]'}
     ,{header: 'Last Name', sortable: true, width: 200, dataIndex: 'last_name' }
     ,{header: 'First Name', sortable: true, width: 200, dataIndex: 'first_name' }
     ,{header: 'Payment Date', sortable: true, width: 150, align: 'center', dataIndex: 'lp[payment_date]' }
     ,{header: 'Loan Description', sortable: true, width: 150, dataIndex: 'lp[loan_description]' }
	 ,{hidden: true, dataIndex: 'lp[transaction_code]'}
	]
);

var summaryLoanPayment = new Ext.ux.grid.GridSummary();

var loanEmployeeColumns =  new Ext.grid.ColumnModel( 
	[
		{id:'employee_id', header: 'Employee ID', width: 100, align: 'right', sortable: true, dataIndex: 'employee_id'}
		,{header: 'Last Name', width: 100, sortable: true, dataIndex: 'last_name'}
		,{header: 'First Name', width: 100, sortable: true, dataIndex: 'first_name'}
	]
);

var loanListColumns =  new Ext.grid.ColumnModel( 
	[
		{id:'loan_no', header: 'Loan No', width: 100, align: 'right', sortable: true, dataIndex: 'loan_no'}
		,{header: 'Loan Description', width: 100, sortable: true, dataIndex: 'loan_description'}
		,{header: 'Loan Date', width: 100, sortable: true, dataIndex: 'loan_date'}
	]
);

var lpDtlColumns =  new Ext.grid.ColumnModel( 
	[
		{id:'id', hidden: true, dataIndex: 'id'}
		,{header: 'Charge Code', width: 20, sortable: true, align: 'right', dataIndex: 'charge_code', summaryRenderer: function(v, params, data){
            return 'Total Charges'; }}
		,{header: 'Description', width: 20, sortable: true, dataIndex: 'transaction_description'}
		,{header: 'Amount', width: 20, sortable: true, align: 'right', dataIndex: 'amount', summaryType: 'sum', renderer: function(value, rec){
		    	return Ext.util.Format.number(value,'0,000,000,000.00');}}
	]
);

var lpDetail = function(){
	return {
		xtype:'form'
		,id:'lpDetail'
		,region:'center'
		,title: 'Details'
		,hidden:true
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,reader: pecaReaders.lpHdrReader
		,buttons:[{
			text: 'Print OR'
			,hidden: true
			,iconCls: 'icon_ext_print'
		    ,handler : function(btn){
				var frm = Ext.getCmp('lpDetail').getForm();
		    	if(frm.isValid()){
		    		try {
		    		    Ext.destroy(Ext.get('frmDownload'));
		    		}catch(e) {}		    		
		    		if (!Ext.fly('frmDownload')) {
                        var frm = document.createElement('form');
                        frm.id = 'frmDownload';
                        frm.name = id;
                        frm.className = 'x-hidden';
                        document.body.appendChild(frm);
                    } 
		    		
		    		Ext.Ajax.request({
		    			url: '/utilities/printORLoanPayment' 
						,method: 'POST'
						,form: Ext.get('frmDownload')
						,params: {loan_no: Ext.getCmp('lpDetail').getForm().findField('lp[loan_no]').getValue()
									,payment_date: Ext.getCmp('lpDetail').getForm().findField('lp[payment_date]').getValue()
									,transaction_code: Ext.getCmp('lpDetail').getForm().findField('lp[transaction_code]').getValue()
									,payor_id: Ext.getCmp('lpDetail').getForm().findField('lp[payor_id]').getValue()
			        				,auth:_AUTH_KEY}
						,isUpload: true
						,success: function(response, opts) {
							var obj = Ext.decode(response.responseText);
							if(obj.success){
								App.setAlert(true, obj.msg);
							}else{
								App.setAlert(false, obj.msg);
							}
						}
						,failure: function(response, opts) {
							var obj = Ext.decode(response.responseText);
							App.setAlert(false, obj.msg);
						}
		        	});
		    	}
		    }
		},{
			text: 'Cancel'
			,iconCls: 'icon_ext_cancel'
		    ,handler : function(btn){
				Ext.getCmp('lpDetail').hide();
				Ext.getCmp('lpDetail').getForm().reset();
				Ext.getCmp('lpList').show();
				pecaDataStores.lpStore.reload();
		    }
		},{
			text: 'Delete'
			,iconCls: 'icon_ext_del'
			,handler: function(){
				var frm = Ext.getCmp('lpDetail').getForm();
				frm.onDelete();
		    }
		},{
			text:'Save'
			,iconCls: 'icon_ext_save'
		    ,handler: function(){
		    	var frm = Ext.getCmp('lpDetail').getForm();
		    	if(frm.isValid()){
		    		if (frm.isModeNew()) {
			        	frm.onSave(frm);
		    		} else {
		    		   	frm.onUpdate(frm);
		            }
		    	}
		    }
		}]
		,items: [{
		    xtype: 'hidden'
		    ,name: 'frm_mode'
		    ,value: FORM_MODE_NEW
		    ,submitValue: false
		    ,listeners: {'change':{fn: function(obj,value){
            	}}}
		},{
			xtype: 'panel'
			,bodyStyle:{'padding':'10px'}
			,border: true
			,items: [{
			layout: 'column'
            	,border: true
				,labelAlign: 'left'
				,items: [{
					layout: 'form'
					,width:250
					,border: false
					,items: [{
						xtype: 'textfield'
						,name: 'lp[loan_no]'
						,fieldLabel: 'Loan No.'
						,style: 'text-align: right'
		                ,allowBlank: false
						,anchor: '100%'
						,width:250
		                ,required: true
						,autoCreate: {tag: 'input', type: 'numeric', maxlength: '10'}
					,enableKeyEvents: true
		    		,style: 'text-align: right'
						,enableKeyEvents: true
			    		,style: 'text-align: right'
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
							}, 
							specialkey: function(frm,evt){
								if (evt.getKey() == evt.ENTER) {
									if(Ext.getCmp('lpDetail').getForm().findField('employee_id').getValue() == ''){
										pecaDataStores.lpLoanListStore.load({params: {
											loan_no: Ext.getCmp('lpDetail').getForm().findField('lp[loan_no]').getValue()
											,start:0, limit:MAX_PAGE_SIZE}});
											lp_LoanListWin().show();
										}
										else{
										pecaDataStores.lpLoanListWithEmployeeStore.load({params: {
											'employee_id' : Ext.getCmp('lpDetail').getForm().findField('employee_id').getValue()
											,loan_no : Ext.getCmp('lpDetail').getForm().findField('lp[loan_no]').getValue()
											,start:0, limit:MAX_PAGE_SIZE}});
											lp_LoanListWithEmployeeWin().show();
										}
								}
							}
						}
					}]
					},{
					layout: 'form'
					,labelWidth: 1
					,border: false
					,items: [{
						xtype: 'button'
						,text: 'Search'
						,id: 'lpLoanSearch'	
						,iconCls: 'icon_ext_search'
						//,anchor: '95%'
						,labelSeparator: ' '
						,fieldLabel: ' '
						//,columnWidth:.2
						,width: 75
						,handler: function(){ 
							if(Ext.getCmp('lpDetail').getForm().findField('employee_id').getValue() == ''){
							pecaDataStores.lpLoanListStore.load({params: {
								loan_no: Ext.getCmp('lpDetail').getForm().findField('lp[loan_no]').getValue()
								,start:0, limit:MAX_PAGE_SIZE}});
								lp_LoanListWin().show();
							}
							else{
							pecaDataStores.lpLoanListWithEmployeeStore.load({params: {
								'employee_id' : Ext.getCmp('lpDetail').getForm().findField('employee_id').getValue()
								,loan_no : Ext.getCmp('lpDetail').getForm().findField('lp[loan_no]').getValue()
								,start:0, limit:MAX_PAGE_SIZE}});
								lp_LoanListWithEmployeeWin().show();
							}
						}
					}]
					}]
        },{ layout: 'column'
            	,border: false
				,labelAlign: 'left'
				,items: [{
					layout: 'form'
					,width:250
					,border: false
					,items: [{
						xtype: 'textfield'
						,name: 'employee_id'
						,anchor: '100%'
						,fieldLabel: 'Employee'
		                ,allowBlank: false
		                ,required: true
						,style: 'text-align: right'
						,emptyText: 'ID'
						,autoCreate: {tag: 'input', type: 'numeric', maxlength: '8'}
						,enableKeyEvents: true
			    		,style: 'text-align: right'
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
							}, 
							specialkey: function(frm,evt){
								if (evt.getKey() == evt.ENTER) {
									pecaDataStores.employeeWithLoanStore.load({params: {
									employee_id: Ext.getCmp('lpDetail').getForm().findField('employee_id').getValue()
									,first_name: Ext.getCmp('lpDetail').getForm().findField('first_name').getValue()
									,last_name: Ext.getCmp('lpDetail').getForm().findField('last_name').getValue()
									,loan_no: Ext.getCmp('lpDetail').getForm().findField('lp[loan_no]').getValue()
									,start:0, limit:MAX_PAGE_SIZE}});
									lp_employeeListWin().show();
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
						,name: 'last_name'	
						,fieldLabel: ' '
						,anchor: '100%'
						,labelSeparator: ' '
						,columnWidth:.2
						,emptyText: 'Last Name'
						,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
					}]
					},{
					layout: 'form'
					,labelWidth: 1
					,border: false
					,width: 150
					,items: [{
						xtype: 'textfield'
						,name: 'first_name'	
						,fieldLabel: ' '
						,anchor: '100%'
						,labelSeparator: ' '
						,emptyText: 'First Name'
						,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
					}]
					},{
					layout: 'form'
					,labelWidth: 1
					,border: false
					,width: 75
					,items: [{
						xtype: 'button'
						,text: 'Search'
						,id: 'lpEmpSearch'
						,iconCls: 'icon_ext_search'
						,anchor: '100%'
						,labelSeparator: ' '
						,fieldLabel: ' '
						,handler: function(){ 
							pecaDataStores.employeeWithLoanStore.load({params: {
								employee_id: Ext.getCmp('lpDetail').getForm().findField('employee_id').getValue()
								,first_name: Ext.getCmp('lpDetail').getForm().findField('first_name').getValue()
								,last_name: Ext.getCmp('lpDetail').getForm().findField('last_name').getValue()
								,loan_no: Ext.getCmp('lpDetail').getForm().findField('lp[loan_no]').getValue()
								,start:0, limit:MAX_PAGE_SIZE}});
								lp_employeeListWin().show();
						}
					}]
					}]
        }]
		
		},{
			xtype: 'panel'
			,bodyStyle:{'padding':'10px'}
			,border: true
			,items: [{
			layout: 'column'
            	,border: true
				,labelAlign: 'left'
				,items: [{
					layout: 'form'
					,width:250
					,border: false
					,items: [{
						xtype: 'textfield'
						,name: 'company_code'
						,fieldLabel: 'Legal Entity'
						,readOnly: true
						,anchor: '100%'
						,width:250
						,autoCreate: {tag: 'input', type: 'text', maxlength: '4'}
					}]
			}]
        },{ layout: 'column'
            	,border: false
				,labelAlign: 'left'
				,items: [{
					layout: 'form'
					,width:250
					,border: false
					,items: [{
						xtype: 'textfield'
						,name: 'loan_code'
						,fieldLabel: 'Loan Type'
						,anchor: '100%'
						,width:250
						,emptyText: 'Loan Code'
						,readOnly: true
						,autoCreate: {tag: 'input', type: 'text', maxlength: '8'}
					}]
					},{
					layout: 'form'
					,labelWidth: 1
					,border: false
					,items: [{
						xtype: 'textfield'
						,name: 'loan_description'	
						,width: 150
						,fieldLabel: ' '
						//,anchor: '95%'
						,labelSeparator: ' '
						//,anchor: '95%'
						,readOnly: true
						//,columnWidth:.2
						,emptyText: 'Loan Description'
						,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
					}]
					}]
        },{
			layout: 'column'
            	,border: true
				,labelAlign: 'left'
				,items: [{
					layout: 'form'
					,width:250
					,border: false
					,items: [{
						xtype: 'moneyfield'
						,name: 'lp[balance]'
						,fieldLabel: 'Loan Balance'
						,style: 'text-align: right'
						,readOnly: true
						,width:250
						,anchor: '100%'
						,autoCreate: {tag: 'input', type: 'numeric', maxlength: '16'}
					}]
			}]
        }]
		
		},{
			xtype: 'panel'
			,bodyStyle:{'padding':'10px'}
			,layout: 'column'

			,border: true
			,items: [{
				labelAlign: 'left'
				//[START] 7th Enhancement
				,labelWidth: 120
				//[END] 7th Enhancement
				,layout: 'form'	
				,columnWidth:.5				
				,xtype: 'fieldset'
                ,autoScroll: true
				,title: 'Loan Payment Details'
				,bodyStyle:{'padding':'10px'}
				,items: [
				{
					xtype: 'combo'
					,fieldLabel: 'Payor'
					//,anchor: '80%'
					,width: 250
					,id: 'lp[pcode]'
					,allowBlank: false
					,required: true
					,hiddenName: 'lp[payor_id]'
					,typeAhead: true
					,triggerAction: 'all'
					,lazyRender:true
					,store: pecaDataStores.lpPayorStore
					,mode: 'local'
					,valueField: 'payor_id'
					,displayField: 'last_first'									
					,forceSelection: true
					,submitValue: false
					,emptyText: 'Please Select'
				},{
					xtype: 'combo'
					,fieldLabel: 'Payment Type'
					//,anchor: '80%'
					,width: 250
					,id: 'transaction_code'
					,allowBlank: false
					,required: true
					,hiddenName: 'lp[transaction_code]'
					,typeAhead: true
					,triggerAction: 'all'
					,lazyRender:true
					,store: pecaDataStores.lpTypeStore
					,mode: 'local'
					,valueField: 'payment_code'
					,displayField: 'payment_type_description'									
					,forceSelection: true
					,submitValue: false
					,emptyText: 'Please Select'
					//,name: 'lp[transaction_code]'
				},{
					xtype: 'numberfield'
					,fieldLabel: 'OR No.'
					//,anchor: '80%'
					,width: 200
					,style: 'text-align: right'
					,name: 'lp[or_no]'
				},{
					xtype: 'textfield'
					,fieldLabel: 'OR Date'
					,width: 200
					,style: 'text-align: right'
					,name: 'lp[or_date]'
				},{
					xtype: 'textfield'
					,fieldLabel: 'Payment Date'
					//,anchor: '80%'
					,width: 200
					,allowBlank: false
					,readOnly: true
					,required: true
					,value: _TODAY
					,style: 'text-align: right'
					,name: 'lp[payment_date]'
				},{
					xtype: 'moneyfield'
					,fieldLabel: 'Principal Amt'
					,width: 200
					//,anchor: '80%'
					,allowBlank: false
					,required: true
					,style: 'text-align: right'
					,name: 'lp[amount]'
					,autoCreate: {tag: 'input', type: 'text', maxlength: '16'}
				},{
					xtype: 'moneyfield'
					,fieldLabel: 'Interest Amt'
					,width: 200
					//,anchor: '80%'
					,allowBlank: false
					,required: true
					,style: 'text-align: right'
					,name: 'lp[interest_amount]'
					,autoCreate: {tag: 'input', type: 'text', maxlength: '16'}
				},
				//[START] 7th Enhancement
				{
					xtype: 'checkbox'
					,name: 'lp[advance_payment_flag]'
					,style: 'background:transparent; margin-left: 0%; text-align: right; border:0'
					,anchor: '95%'
					,submitValue: false
					,fieldLabel: 'Advance Payment'
					,onClick: function () {
						var advance_payment_flag = Ext.getCmp('lpDetail').getForm().findField('lp[advance_payment_flag]').getValue();
						if(!advance_payment_flag) {
							var loan_balance = Ext.getCmp('lpDetail').getForm().findField('lp[balance]').getValue();
							var interest_rate = Ext.getCmp('lpDetail').getForm().findField('lp[interest_rate]').getValue();
							if(loan_balance !== "" && interest_rate !== "") {
								var computed_interest = Math.round(loan_balance * (interest_rate / 100) / 12);
								Ext.getCmp('lpDetail').getForm().findField('lp[interest_amount]').setValue(computed_interest);
							}
							else {
								Ext.getCmp('lpDetail').getForm().findField('lp[interest_amount]').setValue("0.00");
							}
						} else {
							Ext.getCmp('lpDetail').getForm().findField('lp[interest_amount]').setValue("0.00");
						}
					}
				},{
					xtype: 'hidden'
					,submitValue: false
					,name: 'lp[interest_rate]'
				},
				//[END] 7th Enhancement
				{
					xtype: 'textarea'
					,fieldLabel: 'Remarks'
                	,maxLength: 50
					,anchor: '95%'
					,name: 'lp[remarks]'
				}]
			},{
				//xtype: 'label'
				columnWidth:.005
				,html: '&nbsp;'

			},{
				labelAlign: 'left'
				,layout: 'form'	
				,id: 'lpOtherCharges'
				,columnWidth:.48				
				,xtype: 'fieldset'
                ,autoScroll: true
				,title: 'Other Charges'
				,bodyStyle:{'padding':'10px'}
				,items: [lpDtlList()]
			}]
		}]
	    ,isModeNew: function() {
	    	return (Ext.getCmp('lpDetail').getForm().findField('frm_mode').getValue() == FORM_MODE_NEW);
	    }
	    ,setModeNew: function() {
	    	Ext.getCmp('lpDetail').getForm().findField('frm_mode').setValue(FORM_MODE_NEW);
			Ext.getCmp('lpDetail').buttons[0].setVisible(false);  //print OR button
	    	Ext.getCmp('lpDetail').buttons[1].setVisible(true);  //cancel button
	    	Ext.getCmp('lpDetail').buttons[2].setVisible(false);  //delete button
	    	Ext.getCmp('lpDetail').buttons[3].setVisible(true);  //save button
			Ext.getCmp('lpDetail').findById('lpLoanSearch').setVisible(true);
			Ext.getCmp('lpDetail').findById('lpEmpSearch').setVisible(true);
			Ext.getCmp('lpDetail').findById('lpEmpSearch').setVisible(true);
			Ext.getCmp('lpDetail').findById('lpOtherCharges').setVisible(false);
			Ext.getCmp('lpDetail').getForm().findField('lp[or_date]').setVisible(false);
			Ext.getCmp('lpDetail').getForm().findField('lp[or_no]').setVisible(false);
			
			//Ext.getCmp('lpDetail').getForm().findField('lp[payment_date]').reset();
			Ext.getCmp('lpDetail').getForm().findField('lp[amount]').setValue('0');
			Ext.getCmp('lpDetail').getForm().findField('lp[interest_amount]').setValue('0');
			Ext.getCmp('lpDetail').getForm().findField('lp[payment_date]').setValue(_TODAY);
			
	    	Ext.getCmp('lpDetail').getForm().findField('lp[loan_no]').focus('',250);
			Ext.getCmp('lpDetail').getForm().findField('lp[loan_no]').setReadOnly(false);
			//Ext.getCmp('lpDetail').getForm().findField('loan_description').setReadOnly(false);
			Ext.getCmp('lpDetail').getForm().findField('employee_id').setReadOnly(false);
			Ext.getCmp('lpDetail').getForm().findField('last_name').setReadOnly(false);
			Ext.getCmp('lpDetail').getForm().findField('first_name').setReadOnly(false);
			Ext.getCmp('lpDetail').getForm().findField('lp[payor_id]').setReadOnly(false);
			Ext.getCmp('lpDetail').getForm().findField('lp[transaction_code]').setReadOnly(false);
			//Ext.getCmp('lpDetail').getForm().findField('lp[payment_date]').setReadOnly(false);
			
			
			Ext.getCmp('lpDetail').getForm().findField('lp[payor_id]').reset();
			Ext.getCmp('lpDetail').getForm().findField('lp[transaction_code]').reset();
			
			Ext.getCmp('lpDetail').getForm().findField('loan_description').removeClass('x-item-disabled');
			Ext.getCmp('lpDetail').getForm().findField('lp[loan_no]').removeClass('x-item-disabled');
			Ext.getCmp('lpDetail').getForm().findField('employee_id').removeClass('x-item-disabled');
			Ext.getCmp('lpDetail').getForm().findField('last_name').removeClass('x-item-disabled');
			Ext.getCmp('lpDetail').getForm().findField('first_name').removeClass('x-item-disabled');
			Ext.getCmp('lpDetail').getForm().findField('lp[payor_id]').removeClass('x-item-disabled');
			Ext.getCmp('lpDetail').getForm().findField('lp[transaction_code]').removeClass('x-item-disabled');
			Ext.getCmp('lpDetail').getForm().findField('loan_code').removeClass('x-item-disabled');
			//Ext.getCmp('lpDetail').getForm().findField('lp[payment_date]').removeClass('x-item-disabled');
			Ext.getCmp('lpDetail').getForm().findField('company_code').removeClass('x-item-disabled');
			Ext.getCmp('lpDetail').getForm().findField('lp[balance]').removeClass('x-item-disabled');
			Ext.getCmp('lpDetail').getForm().findField('lp[balance]').setValue("0.00");
			
			//[START]7th Enhancement
			//Ext.getCmp('lpDetail').getForm().findField('lp[interest_amount]').setReadOnly(true);
			//Ext.getCmp('lpDetail').getForm().findField('lp[interest_amount]').addClass('x-item-disabled');
			//[END]7th Enhancement
			
			pecaDataStores.lpTypeStore.removeAll(true);
			pecaDataStores.lpPayorStore.removeAll(true);
		}
		,setModeUpdate: function() {
			Ext.getCmp('lpDetail').getForm().findField('frm_mode').setValue(FORM_MODE_UPDATE);
			//Ext.getCmp('lpDetail').buttons[0].setVisible(true);  //print OR button
			Ext.getCmp('lpDetail').buttons[1].setVisible(true);  //cancel button
	    	Ext.getCmp('lpDetail').buttons[2].setVisible(true);  //delete button
	    	Ext.getCmp('lpDetail').buttons[3].setVisible(true);  //save button
	    	
	    	//can't update record
			if(_PERMISSION[132]==0){
				Ext.getCmp('lpDetail').buttons[3].setDisabled(true);	
			}
			//can't delete record
			if(_PERMISSION[36]==0){
				Ext.getCmp('lpDetail').buttons[2].setDisabled(true);	
			}
	    				
	    	//Ext.getCmp('lpDetail').getForm().findField('lp[amount]').focus('',10);
			Ext.getCmp('lpDetail').getForm().findField('lp[or_date]').setVisible(false);
			Ext.getCmp('lpDetail').getForm().findField('lp[or_no]').setVisible(false);
			Ext.getCmp('lpDetail').getForm().findField('lp[loan_no]').setReadOnly(true);
			Ext.getCmp('lpDetail').getForm().findField('loan_description').setReadOnly(true);
			Ext.getCmp('lpDetail').getForm().findField('employee_id').setReadOnly(true);
			Ext.getCmp('lpDetail').getForm().findField('last_name').setReadOnly(true);
			Ext.getCmp('lpDetail').getForm().findField('first_name').setReadOnly(true);
			Ext.getCmp('lpDetail').getForm().findField('lp[payor_id]').setReadOnly(true);
			Ext.getCmp('lpDetail').getForm().findField('lp[transaction_code]').setReadOnly(true);
			Ext.getCmp('lpDetail').getForm().findField('loan_code').setReadOnly(true);
			Ext.getCmp('lpDetail').getForm().findField('lp[payment_date]').setReadOnly(true);
			Ext.getCmp('lpDetail').findById('lpLoanSearch').setVisible(false);
			Ext.getCmp('lpDetail').findById('lpEmpSearch').setVisible(false);
			Ext.getCmp('lpDetail').getForm().findField('lp[or_date]').setReadOnly(true);
			Ext.getCmp('lpDetail').getForm().findField('lp[or_no]').setReadOnly(true);
			Ext.getCmp('lpDetail').getForm().findField('lp[or_date]').addClass('x-item-disabled');
			Ext.getCmp('lpDetail').getForm().findField('lp[or_no]').addClass('x-item-disabled');
			Ext.getCmp('lpDetail').findById('lpOtherCharges').setVisible(true);

			Ext.getCmp('lpDetail').getForm().findField('loan_description').addClass('x-item-disabled');
			Ext.getCmp('lpDetail').getForm().findField('lp[loan_no]').addClass('x-item-disabled');
			Ext.getCmp('lpDetail').getForm().findField('employee_id').addClass('x-item-disabled');
			Ext.getCmp('lpDetail').getForm().findField('last_name').addClass('x-item-disabled');
			Ext.getCmp('lpDetail').getForm().findField('first_name').addClass('x-item-disabled');
			Ext.getCmp('lpDetail').getForm().findField('lp[payor_id]').addClass('x-item-disabled');
			Ext.getCmp('lpDetail').getForm().findField('lp[transaction_code]').addClass('x-item-disabled');
			Ext.getCmp('lpDetail').getForm().findField('lp[payment_date]').addClass('x-item-disabled');
			Ext.getCmp('lpDetail').getForm().findField('loan_code').addClass('x-item-disabled');
			Ext.getCmp('lpDetail').getForm().findField('company_code').addClass('x-item-disabled');
			Ext.getCmp('lpDetail').getForm().findField('lp[balance]').addClass('x-item-disabled');
			
			//[START]7th Enhancement
			//Ext.getCmp('lpDetail').getForm().findField('lp[interest_amount]').setReadOnly(true);
			//Ext.getCmp('lpDetail').getForm().findField('lp[interest_amount]').addClass('x-item-disabled');
			//[END]7th Enhancement
			
	    }
		,onSave: function(frm){
			frm.submit({
    			url: '/loan_payment/add' 
    			,method: 'POST'
    			,params: {auth:_AUTH_KEY, 'lp[created_by]': _USER_ID,user: _USER_ID	}
    			,waitMsg: 'Creating Data...'
    			,success: function(form, action) {
    				showExtInfoMsg( action.result.msg);
					Ext.getCmp('lpDetail').getForm().findField('lp[or_no]').setValue(action.result.or_no);
					Ext.getCmp('lpDetail').getForm().findField('lp[or_date]').setValue(action.result.or_date);
    				frm.setModeUpdate();
					pecaDataStores.lpChargesStore.load({params: {
						'lp[loan_no]': Ext.getCmp('lpDetail').getForm().findField('lp[loan_no]').getValue()
						,'lp[employee_id]': Ext.getCmp('lpDetail').getForm().findField('employee_id').getValue()
						,'lp[payment_date]': Ext.getCmp('lpDetail').getForm().findField('lp[payment_date]').getValue()
						,user: _USER_ID	
					,auth:_AUTH_KEY}});
					Ext.getCmp('lpDetail').getForm().load({
				    	url: '/loan_payment/showHdr'
				    	,params: {'lp[loan_no]':(Ext.getCmp('lpDetail').getForm().findField('lp[loan_no]').getValue())
									,'lp[employee_id]':(Ext.getCmp('lpDetail').getForm().findField('employee_id').getValue())
									,'lp[payment_date]':(Ext.getCmp('lpDetail').getForm().findField('lp[payment_date]').getValue())
									,'lp[transaction_code]':(Ext.getCmp('lpDetail').getForm().findField('lp[transaction_code]').getValue())
									,auth:_AUTH_KEY}
				    	,method: 'POST'
				    	,waitMsgTarget: true
					});
					/*Ext.Ajax.request({
				        	url: '/loan_payment/withORWithEcho' 
							,method: 'POST'
							,params: {'transaction_code':(Ext.getCmp('lpDetail').getForm().findField('lp[transaction_code]').getValue())
				        				,auth:_AUTH_KEY}
							,success: function(response, opts) {
								var obj = Ext.decode(response.responseText);
								if(obj.hasOR){
										Ext.getCmp('lpDetail').getForm().findField('lp[or_date]').setVisible(true);
										Ext.getCmp('lpDetail').getForm().findField('lp[or_no]').setVisible(true);
								}else{
									Ext.getCmp('lpDetail').getForm().findField('lp[or_date]').setVisible(false);
									Ext.getCmp('lpDetail').getForm().findField('lp[or_no]').setVisible(false);
								}
							}
			        	});*/
					
    			}
    			,failure: function(form, action) {
					if(action.result.error_code == '2'){
	    				Ext.Msg.confirm('Confirm Action',action.result.msg,function(btn) {
	    					if(btn=='yes'){
	    						form.onUpdate(form);
	    					}
	    				});
    				}else{
    					showExtErrorMsg( action.result.msg);
    				}
    			}	
    		});
		}
		,onUpdate: function(frm){
			frm.submit({
    			url: '/loan_payment/update' 
    			,method: 'POST'
    			,waitMsg: 'Updating Data...'
    			,params: {
        			auth:_AUTH_KEY, 'lp[modified_by]': _USER_ID	
					,user: _USER_ID	
    			}
    			,success: function(form, action) {
    				showExtInfoMsg( action.result.msg);
        			frm.setModeUpdate();
					pecaDataStores.lpChargesStore.load({params: {
						'lp[loan_no]': Ext.getCmp('lpDetail').getForm().findField('lp[loan_no]').getValue()
						,'lp[employee_id]': Ext.getCmp('lpDetail').getForm().findField('employee_id').getValue()
						,'lp[payment_date]': Ext.getCmp('lpDetail').getForm().findField('lp[payment_date]').getValue()
					,auth:_AUTH_KEY}});
				}
    			,failure: function(form, action) {
    				showExtErrorMsg( action.result.msg);
    			}	
    		});
		}
		,onDelete: function(){
			Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
				if(btn=='yes') {
					Ext.getCmp('lpDetail').getForm().submit({
						url: '/loan_payment/delete' 
						,method: 'POST'
						,params: {auth:_AUTH_KEY, 'lp[modified_by]': _USER_ID}
						,waitMsg: 'Deleting Data...'
						,success: function(form, action) {
							showExtInfoMsg( action.result.msg);
			    			Ext.getCmp('lpDetail').getForm().reset();
			    			Ext.getCmp('lpDetail').hide();
							Ext.getCmp('lpList').show();
							Ext.getCmp('lpDetail').setModeNew();
							//pecaDataStores.lpStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
							if (pecaDataStores.lpStore.getCount() % MAX_PAGE_SIZE == 1){
								var page = pecaDataStores.lpStore.getTotalCount() - MAX_PAGE_SIZE - 1;
								pecaDataStores.lpStore.load({params: {start:page<0?0:page, limit:MAX_PAGE_SIZE}});
							} else{
								pecaDataStores.lpStore.reload();
							}
						}
						,failure: function(form, action) {
							showExtErrorMsg( action.result.msg);
						}	
					});
				}
			});
		}
	};
};

var lpList = function(){
	return {
		xtype: 'grid'
		,id: 'lpList'
		,titlebar: false
		,store: pecaDataStores.lpStore
		,region: 'center'
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 425
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit: true
			,scrollOffset: 0
		}
		,cm: lpColumns
		
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
					var recVal = rec.get('lp[loan_no]');
					Ext.getCmp('lpList').hide();
					Ext.getCmp('lpDetail').show();
					Ext.getCmp('lpDetail').getForm().setModeUpdate();
					pecaDataStores.lpTypeStore.load({params: {
						 'lp[loan_no]': recVal
						,auth:_AUTH_KEY}});
					pecaDataStores.lpPayorStore.load({params: {
						 'lp[loan_no]': recVal
						,auth:_AUTH_KEY}});
					pecaDataStores.lpChargesStore.load({params: {
						//'lp[transCode]' : rec.get('transaction_code')
						'lp[loan_no]': recVal
						,'lp[employee_id]' : rec.get('employee_id')
						,'lp[payment_date]' : rec.get('payment_date')
					,auth:_AUTH_KEY}});
					Ext.getCmp('lpDetail').getForm().load({
				    	url: '/loan_payment/showHdr'
				    	,params: {'lp[loan_no]':(rec.get('lp[loan_no]'))
									,'lp[transaction_code]':(rec.get('lp[transaction_code]'))
									,'lp[employee_id]':(rec.get('lp[employee_id]'))
									,'lp[payment_date]':(rec.get('lp[payment_date]'))
									,auth:_AUTH_KEY}
				    	,method: 'POST'
				    	,waitMsgTarget: true
					});
					
					/*Ext.Ajax.request({
				        	url: '/loan_payment/withORWithEcho' 
							,method: 'POST'
							,params: {'transaction_code':(rec.get('lp[transaction_code]'))
				        				,auth:_AUTH_KEY}
							,success: function(response, opts) {
								var obj = Ext.decode(response.responseText);
								if(obj.hasOR){
										Ext.getCmp('lpDetail').getForm().findField('lp[or_date]').setVisible(true);
										Ext.getCmp('lpDetail').getForm().findField('lp[or_no]').setVisible(true);
								}else{
									Ext.getCmp('lpDetail').getForm().findField('lp[or_date]').setVisible(false);
									Ext.getCmp('lpDetail').getForm().findField('lp[or_no]').setVisible(false);
								}
							}
			        	});*/

					/*Ext.getCmp('lpDetail').getForm().load({
				    	url: '/loan_payment/showDtl'
				    	,params: {'lp[[transCode]':(rec.get('transaction_code'))
									,'lp[employee_id]':(rec.get('lp[employee_id]'))
									,'lp[payment_date]':(rec.get('lp[payment_date]'))
									,auth:_AUTH_KEY}
				    	,method: 'POST'
				    	,waitMsgTarget: true
					});*/
				}
			}
			,'render':{
				scope:this
				,fn:function(grid){					
					//search textfield and button
					if(_PERMISSION[59]==0){
						Ext.getCmp('lp_loan_no').setDisabled(true);
						Ext.getCmp('lp_id').setDisabled(true);
						Ext.getCmp('lp_lastname').setDisabled(true);
						Ext.getCmp('lp_firstname').setDisabled(true);
						Ext.getCmp('lpSearchID').setDisabled(true);	
					}else{
						Ext.getCmp('lp_loan_no').setDisabled(false);
						Ext.getCmp('lp_id').setDisabled(false);
						Ext.getCmp('lp_lastname').setDisabled(false);
						Ext.getCmp('lp_firstname').setDisabled(false);
						Ext.getCmp('lpSearchID').setDisabled(false);
						pecaDataStores.lpStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
					}
					//new button
					if(_PERMISSION[11]==0){
						Ext.getCmp('lpNewID').setDisabled(true);	
					}else{
						Ext.getCmp('lpNewID').setDisabled(false);
					}
					//delete button
					if(_PERMISSION[36]==0){
						Ext.getCmp('lpDeleteID').setDisabled(true);	
					}else{
						Ext.getCmp('lpDeleteID').setDisabled(false);
					}
				}
			}
		}
		,tbar:[{
			xtype: 'textfield'
			,width: 70
			,id: 'lp_loan_no'
            ,hideLabel: true
            ,emptyText: 'Loan No'
			,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
			,enableKeyEvents: true
    		,style: 'text-align: right'
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
				}, 
				specialkey: function(frm,evt){
					if (evt.getKey() == evt.ENTER) {
						pecaDataStores.lpStore.load({params: {
							start:0
							,limit:MAX_PAGE_SIZE
							,auth:_AUTH_KEY}});						
					}
				}
			}
    	},' ',{
            xtype: 'textfield'
            ,width: 70
			,id: 'lp_id'
            ,hideLabel: true
            ,emptyText: 'Employee ID'
			,autoCreate: {tag: 'input', type: 'text', maxlength: '8'}
			,enableKeyEvents: true
			,style: 'text-align: right'
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
				},
				specialkey: function(frm,evt){
					if (evt.getKey() == evt.ENTER) {
						pecaDataStores.lpStore.load({params: {
							start:0
							,limit:MAX_PAGE_SIZE
							,auth:_AUTH_KEY}});						
					}
				}
			}
		},' ',{
            xtype: 'textfield'
            ,width: 100
			,id: 'lp_lastname'
            ,hideLabel: true
            ,emptyText: 'Last Name'
			,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
			,listeners: {
				specialkey: function(frm,evt){
					if (evt.getKey() == evt.ENTER) {
						pecaDataStores.lpStore.load({params: {
							start:0
							,limit:MAX_PAGE_SIZE
							,auth:_AUTH_KEY}});				
					}
				}
			}
    	},' ',{
            xtype: 'textfield'
            ,width: 100
			,id: 'lp_firstname'
            ,hideLabel: true
            ,emptyText: 'First Name'
			,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
			,listeners: {
				specialkey: function(frm,evt){
					if (evt.getKey() == evt.ENTER) {
						pecaDataStores.lpStore.load({params: {
							start:0
							,limit:MAX_PAGE_SIZE
							,auth:_AUTH_KEY}});
					
					}
				}
			}
        },{
			text:'Search'
			,id: 'lpSearchID'
			,iconCls: 'icon_ext_search'
			,scope:this
			,handler:function(btn) {
				pecaDataStores.lpStore.load({params: {
					start:0
					,limit:MAX_PAGE_SIZE
					,auth:_AUTH_KEY}});	
			}
		},'-'
		,{
			text:'New'
			,id: 'lpNewID'
			,tooltip:'Add a New Loan Payment'
			,iconCls: 'icon_ext_add'
			,scope:this
			,handler:function(btn) {
				Ext.getCmp('lpDetail').show();
				Ext.getCmp('lpList').hide();
				lpDetail().setModeNew();
			}
		},'-'
		,{
			text:'Delete'
			,id: 'lpDeleteID'
			,tooltip:'Delete Selected Loan Payment'
			,iconCls: 'icon_ext_del'
			,scope:this
			,handler:function(btn) {
				var index = Ext.getCmp('lpList').getSelectionModel().getSelected();
		        if (!index) {
					showExtInfoMsg( "Please select a Loan Payment to delete.");
		            return false;
		        }
		        Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
					if(btn=='yes') {
				        Ext.Ajax.request({
				        	url: '/loan_payment/delete' 
							,method: 'POST'
							,params: {'lp[payor_id]':index.data.employee_id
										,'lp[loan_no]':index.data.loan_no
										,'lp[payment_date]':index.data.payment_date
										,'lp[transaction_code]':index.data.transaction_code
										//,'lp[payor_id]':index.data.payor_code
				        				,auth:_AUTH_KEY, 'lp[modified_by]': _USER_ID}
							,waitMsg: 'Deleting Data...'
							,success: function(response, opts) {
								var obj = Ext.decode(response.responseText);
								if(obj.success){
									showExtInfoMsg( obj.msg);
									//pecaDataStores.lpStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
									if (pecaDataStores.lpStore.getCount() % MAX_PAGE_SIZE == 1){
										var page = pecaDataStores.lpStore.getTotalCount() - MAX_PAGE_SIZE - 1;
										pecaDataStores.lpStore.load({params: {start:page<0?0:page, limit:MAX_PAGE_SIZE}});
									} else{
										pecaDataStores.lpStore.reload();
									}
								}else{
									showExtErrorMsg( obj.msg);
								}
							}
							,failure: function(response, opts) {
								var obj = Ext.decode(response.responseText);
								showExtErrorMsg( obj.msg);
							}
			        	});
					}
		        });
			}
		}]
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.lpStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};

var lpDtlList = function(){
	return {
		xtype: 'grid'
		,id: 'lpDtlList'
		,titlebar: false
		,store: pecaDataStores.lpChargesStore
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 150
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit: true
			,scrollOffset: 0
		}
		,cm: lpDtlColumns
		,plugins: [summaryLoanPayment]
		,listeners: {
			'afterrender':{
				scope:this
				,fn:function(component) {
					component.getBottomToolbar().refresh.hideParent = true;
					component.getBottomToolbar().refresh.hide(); 
				}
			}
		}
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.lpChargesStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};

var lp_employeeList = function(){
	return {
		xtype: 'grid'
		,id: 'lp_employeeList'
		,titlebar: false
		,store: pecaDataStores.employeeWithLoanStore
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
		,cm: loanEmployeeColumns
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
					Ext.getCmp('lpDetail').getForm().findField('employee_id').setValue(rec.get('employee_id'));
					Ext.getCmp('lpDetail').getForm().findField('last_name').setValue(rec.get('last_name'));
					Ext.getCmp('lpDetail').getForm().findField('first_name').setValue(rec.get('first_name'));
					Ext.getCmp('lp_employeeListWin').close.defer(1,Ext.getCmp('lp_employeeListWin'));
				}
			}
		}
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.employeeWithLoanStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: 'No records found'
	    })
	};
};

var lp_loanList = function(){
	return {
		xtype: 'grid'
		,id: 'lp_loanList'
		,titlebar: false
		,store: pecaDataStores.lpLoanListStore
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
		,cm: loanListColumns
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
					var recVal = rec.get('loan_no');
					Ext.getCmp('lpDetail').getForm().findField('lp[loan_no]').setValue(recVal);
					pecaDataStores.lpPayorStore.load({params: {
						 'lp[loan_no]': recVal
						,auth:_AUTH_KEY}});
					pecaDataStores.lpTypeStore.load({params: {
						 'lp[loan_no]': recVal
						,auth:_AUTH_KEY}});
					Ext.getCmp('lpDetail').getForm().findField('lp[loan_no]').setValue(recVal);					//Ext.getCmp('lpDetail').getForm().findField('employee_id').setValue(rec.get('employee_id'));
					Ext.getCmp('lpDetail').getForm().findField('last_name').setValue(rec.get('last_name'));
					Ext.getCmp('lpDetail').getForm().findField('employee_id').setValue(rec.get('employee_id'));
					Ext.getCmp('lpDetail').getForm().findField('first_name').setValue(rec.get('first_name'));
					Ext.getCmp('lpDetail').getForm().findField('last_name').setValue(rec.get('last_name'));
					Ext.getCmp('lpDetail').getForm().findField('company_code').setValue(rec.get('company_code'));
					Ext.getCmp('lpDetail').getForm().findField('loan_code').setValue(rec.get('loan_code'));
					Ext.getCmp('lpDetail').getForm().findField('loan_description').setValue(rec.get('loan_description'));
					Ext.getCmp('lpDetail').getForm().findField('lp[balance]').setValue(rec.get('loan_balance'));
					//[START] 7th Enhancement
					//Ext.getCmp('lpDetail').getForm().findField('lp[interest_amount]').setValue(rec.get('employee_interest_amortization'));
					Ext.getCmp('lpDetail').getForm().findField('lp[interest_rate]').setValue(rec.get('interest_rate'));
					var computed_interest = 0;
					if(rec.get('bsp_computation') == "Y"){
						computed_interest = Math.round(rec.get('loan_balance') * (rec.get('interest_rate') / 100) / 12);
					}
					
					Ext.getCmp('lpDetail').getForm().findField('lp[interest_amount]').setValue(computed_interest);
					//[END] 7th Enhancement					
					Ext.getCmp('lpDetail').getForm().findField('lp[amount]').setValue(rec.get('employee_principal_amortization'));
					Ext.getCmp('lp_LoanListWin').close.defer(1,Ext.getCmp('lp_LoanListWin'));
				}
			}
		}
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.lpLoanListStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: 'No records found'
	    })
	};
};

var lp_loanListWithEmployee = function(){
	return {
		xtype: 'grid'
		,id: 'lp_loanListWithEmployee'
		,titlebar: false
		,store: pecaDataStores.lpLoanListWithEmployeeStore
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
		,cm: loanListColumns
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
					var recVal = rec.get('loan_no');
					Ext.getCmp('lpDetail').getForm().findField('lp[loan_no]').setValue(recVal);
					pecaDataStores.lpPayorStore.load({params: {
						 'lp[loan_no]': recVal
						,auth:_AUTH_KEY}});
					pecaDataStores.lpTypeStore.load({params: {
						 'lp[loan_no]': recVal
						,auth:_AUTH_KEY}});
					/*Ext.getCmp('lpDetail').getForm().load({
				    	url: '/loan_payment/showHdr'
				    	,params: {'lp[loan_no]':recVal
									,auth:_AUTH_KEY}
				    	,method: 'POST'
				    	,waitMsgTarget: true
					});*/
					Ext.getCmp('lpDetail').getForm().findField('lp[loan_no]').setValue(recVal);					//Ext.getCmp('lpDetail').getForm().findField('employee_id').setValue(rec.get('employee_id'));
					Ext.getCmp('lpDetail').getForm().findField('last_name').setValue(rec.get('last_name'));
					Ext.getCmp('lpDetail').getForm().findField('employee_id').setValue(rec.get('employee_id'));
					Ext.getCmp('lpDetail').getForm().findField('first_name').setValue(rec.get('first_name'));
					Ext.getCmp('lpDetail').getForm().findField('last_name').setValue(rec.get('last_name'));
					Ext.getCmp('lpDetail').getForm().findField('company_code').setValue(rec.get('company_code'));
					Ext.getCmp('lpDetail').getForm().findField('loan_code').setValue(rec.get('loan_code'));
					Ext.getCmp('lpDetail').getForm().findField('loan_description').setValue(rec.get('loan_description'));
					Ext.getCmp('lpDetail').getForm().findField('lp[balance]').setValue(rec.get('loan_balance'));
					Ext.getCmp('lpDetail').getForm().findField('lp[amount]').setValue(rec.get('employee_principal_amortization'));
					//Ext.getCmp('lpDetail').getForm().findField('lp[interest_amount]').setValue(rec.get('employee_interest_amortization'));
					//[START] 7th Enhancement
					//Ext.getCmp('lpDetail').getForm().findField('lp[interest_amount]').setValue(rec.get('employee_interest_amortization'));
					Ext.getCmp('lpDetail').getForm().findField('lp[interest_rate]').setValue(rec.get('interest_rate'));
					var computed_interest = 0;
					if(rec.get('bsp_computation') == "Y"){
						computed_interest = Math.round(rec.get('loan_balance') * (rec.get('interest_rate') / 100) / 12);
					}
					Ext.getCmp('lpDetail').getForm().findField('lp[interest_amount]').setValue(computed_interest);
					//[END] 7th Enhancement		
					Ext.getCmp('lp_LoanListWithEmployeeWin').close.defer(1,Ext.getCmp('lp_LoanListWithEmployeeWin'));
				}
			}
		}
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.lpLoanListWithEmployeeStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: 'No records found'
	    })
	};
};


var lp_employeeListWin = function(){
	return new Ext.Window({
		id: 'lp_employeeListWin'
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
		,items:[ lp_employeeList() ]
        ,buttons:[{
 			text: 'Cancel'
 			,iconCls: 'icon_ext_cancel'
 		    ,handler : function(btn){
 				Ext.getCmp('lp_employeeListWin').close();				
 		    }
 		}]
	});
};

var lp_LoanListWin = function(){
	return new Ext.Window({
		id: 'lp_LoanListWin'
		,title: 'Loan List'
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
		,items:[ lp_loanList() ]
        ,buttons:[{
 			text: 'Cancel'
 			,iconCls: 'icon_ext_cancel'
 		    ,handler : function(btn){
 				Ext.getCmp('lp_LoanListWin').close();				
 		    }
 		}]
	});
};

var lp_LoanListWithEmployeeWin = function(){
	return new Ext.Window({
		id: 'lp_LoanListWithEmployeeWin'
		,title: 'Loan List'
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
		,items:[ lp_loanListWithEmployee() ]
        ,buttons:[{
 			text: 'Cancel'
 			,iconCls: 'icon_ext_cancel'
 		    ,handler : function(btn){
 				Ext.getCmp('lp_LoanListWithEmployeeWin').close();				
 		    }
 		}]
	});
}