var loancodeHdrColumns =  new Ext.grid.ColumnModel( 
	[
		{id: 'loan_code', header: "Loan Code", width: 20, sortable: true, dataIndex: 'loan_code_formatted'}
		,{header: "Loan Description", width: 50, sortable: true, dataIndex: 'loan_description_formatted'}
		,{header: "Priority", width: 20, sortable: true, align: 'right', dataIndex: 'loancodeHdr[priority]'}
		,{header: "Minimum Month's of Service", width: 20, sortable: true,align: 'right', dataIndex: 'loancodeHdr[min_emp_months]'}
		,{header: "Maximum Loan Amount", width: 50, sortable: true, dataIndex: 'loancodeHdr[max_loan_amount]',align: 'right', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000.00');}}
		,{header: "Min Term", width: 20, sortable: true, align: 'right', dataIndex: 'loancodeHdr[min_term]'}
		,{header: "Max Term", width: 20, sortable: true, align: 'right', dataIndex: 'loancodeHdr[max_term]'}
		,{header: "Down payment Percent", width: 20, align: 'right', sortable: true, dataIndex: 'loancodeHdr[downpayment_pct]', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0.00');}}
		,{header: "Restructure", width: 20, sortable: true, align: 'center', dataIndex: 'loancodeHdr[restructure]', renderer:function(value,rec){
			return formatYesNo(value);}}
		,{header: "Employee Interest Percentage", width: 20, align: 'right', sortable: true, dataIndex: 'loancodeHdr[emp_interest_pct]', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0.00');}}
		,{header: "Percent of Company Share", width: 20, align: 'right', sortable: true, dataIndex: 'loancodeHdr[comp_share_pct]', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0.00');}}
		,{header: "Payroll Deduction", width: 20, sortable: true, align: 'center', dataIndex: 'loancodeHdr[payroll_deduction]', renderer:function(value,rec){
			return formatYesNo(value);}}
		,{header: "Interest Deducted in Advance", width: 20, align: 'center', sortable: true, dataIndex: 'loancodeHdr[unearned_interest]', renderer:function(value,rec){
			return formatYesNo(value);}}
		,{header: "Initial Interest", width: 20, sortable: true, align: 'center', dataIndex: 'loancodeHdr[interest_earned]', renderer:function(value,rec){
			return formatYesNo(value);}}
		,{header: "Transaction Code", width: 20, sortable: true, dataIndex: 'loancodeHdr[transcode_formatted]'}
	]
);

var loancodeDtlColumns =  new Ext.grid.ColumnModel( 
	[
	 	{id: "id", hidden:true, dataIndex: 'id'}
//	 	,{header: "Years of Service", colspan: 2}
	 	,{id: "Loan Code", hidden:true, dataIndex: 'loancodeDtl[loan_code]'}
		,{header: "Years Of Service", width: 50, sortable: true, dataIndex: 'loancodeDtl[years_of_service]'}
		,{header: "1/3 of Capital Contribution", width: 75, sortable: true, dataIndex: 'loancodeDtl[capital_contribution]', renderer:function(value,rec){
			return formatYesNo(value);}}
		,{header: "Submit Pension Plan", width: 75, sortable: true, dataIndex: 'loancodeDtl[pension]', renderer:function(value,rec){
			return formatYesNo(value);}}
		,{header: "Co-Maker", width: 25, sortable: true, dataIndex: 'loancodeDtl[guarantor]'}		
	]
);

var loancodePCColumns =  new Ext.grid.ColumnModel( 
	[
		{id: 'id', hidden: true, width: 20, sortable: true, dataIndex: 'id'}
		,{header: 'Payment Code', width: 20, sortable: true, dataIndex: 'transaction_code'}
		,{header: 'Description', width: 20, sortable: true, dataIndex: 'transaction_description'}	
	]
);

var loancodePCProxy = new Ext.data.HttpProxy({
	api: {
	    read    : '/loan_code/readPayment',
	    create  : '/loan_code/addPayment',
	    update  : '',
	    destroy : '/loan_code/deletePayment'
	}
	,listeners:{
		'beforeload':{
			scope:this
			,fn:function(dataproxy,params ){
				params.loan_code = Ext.getCmp('loancodeDetail').getForm().findField('loancodeHdr[loan_code]').getValue();
			}
		}
	}
});

var loancodeDtlProxy = new Ext.data.HttpProxy({
	api: {
	    read    : '/loan_code/readDtl',
	    create  : '/loan_code/addDtl',
	    update  : '/loan_code/updateDtl',
	    destroy : '/loan_code/deleteDtl'
	}
	,listeners:{
		'beforeload':{
			scope:this
			,fn:function(dataproxy,params ){
				params.loan_code = Ext.getCmp('loancodeDetail').getForm().findField('loancodeHdr[loan_code]').getValue();
			}
		}
	}
});

var loancodeDtlWriter = new Ext.data.JsonWriter({
    encode: true
    ,writeAllFields: true
    ,listfull: true
});


var loancodeList = function(){
	return {
		xtype: 'grid'
		,id: 'loancodeList'
		,titlebar: false
		,store: pecaDataStores.loancodeHdrStore
		,region: 'center'
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 425
		//,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit: true
			,scrollOffset: 0
		}
		,cm: loancodeHdrColumns
		
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
					Ext.getCmp('loancodeList').hide();
					pecaDataStores.loanLNStore.load();  //Loans
					pecaDataStores.loanLPStore.load();	//Loan Payment Code
					Ext.getCmp('loancodeDetail').show();
					Ext.getCmp('loancodeDetail').getForm().setModeUpdate();
					Ext.getCmp('loancodeDetail').getForm().load({
				    	url: '/loan_code/showHdr'
				    	,params: {'loancodeHdr[loan_code]':(rec.get('loancodeHdr[loan_code]'))
							,auth:_AUTH_KEY}
				    	,method: 'POST'
				    	,waitMsgTarget: true
				    	,success: function(form, action) {
				    		pecaDataStores.loancodeDtlStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
				    		pecaDataStores.loancodePCStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
				    	}
					});
				}
			}
			,'render':{
				scope:this
				,fn:function(grid){
					pecaDataStores.loancodeHdrStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
					//new button
					if(_PERMISSION[10]==0){
						Ext.getCmp('loancodeNewID').setDisabled(true);	
					}else{
						Ext.getCmp('loancodeNewID').setDisabled(false);
					}
					//delete button
					if(_PERMISSION[35]==0){
						Ext.getCmp('loancodeDeleteID').setDisabled(true);	
					}else{
						Ext.getCmp('loancodeDeleteID').setDisabled(false);
					}
				}
			}
		}
		,tbar:[{
			text:'New'
			,id: 'loancodeNewID'
			,tooltip:'Add a Loan Code'
			,iconCls: 'icon_ext_add'
			,scope:this
			,handler:function(btn) {
				Ext.getCmp('loancodeList').hide();
				pecaDataStores.loancodeDtlStore.load();
				pecaDataStores.loancodePCStore.load();
				pecaDataStores.loanLNStore.load();  //Loans
				pecaDataStores.loanLPStore.load();	//Loan Payment Code
				Ext.getCmp('loancodeDetail').show();
				loancodeDetail().setModeNew();
			}
		},'-'
		,{
			text:'Delete'
			,id: 'loancodeDeleteID'
			,tooltip:'Delete Selected Loan Code'
			,iconCls: 'icon_ext_del'
			,scope:this
			,handler:function(btn) {
				var index = Ext.getCmp('loancodeList').getSelectionModel().getSelected();
		        if (!index) {
		        	showExtInfoMsg("Please select a Loan Code to delete.");
		            return false;
		        }
		        Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
					if(btn=='yes') {
				        Ext.Ajax.request({
				        	url: '/loan_code/deleteHdr' 
							,method: 'POST'
							,params: {'loancodeHdr[loan_code]':index.data.loan_code
										,'loancodeHdr[loan_description]':index.data.loan_description
				        				,auth:_AUTH_KEY, 'loancodeHdr[modified_by]': _USER_ID}
							,waitMsg: 'Deleting Data...'
							,success: function(response, opts) {
								var obj = Ext.decode(response.responseText);
								if(obj.success){
									showExtInfoMsg(obj.msg);
									// pecaDataStores.loancodeHdrStore.load();
									if (pecaDataStores.loancodeHdrStore.getCount() % MAX_PAGE_SIZE == 1){
										pecaDataStores.loancodeHdrStore.load({params: {start:pecaDataStores.loancodeHdrStore.getTotalCount() - MAX_PAGE_SIZE - 1, limit:MAX_PAGE_SIZE}});
									} else{
										pecaDataStores.loancodeHdrStore.reload();
									}
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
		        });
			}
		}]
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.loancodeHdrStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};

var loancodeDtlForm = function(){
	return {
		xtype:'form'
		,id:'loancodeDtlForm'
		,anchor: '100%'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,reader: pecaReaders.loancodeDtlReader
		,buttons:[{
			text: 'Cancel'
			,iconCls: 'icon_ext_cancel'
		    ,handler : function(btn){
				Ext.getCmp('loancodeDtlFormWin').close();				
		    }
		},{
			text:'Save'
			,iconCls: 'icon_ext_save'
			,formBind:true	
		    ,handler: function(){
		    	var frm = Ext.getCmp('loancodeDtlForm').getForm();
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
			layout: 'form'
            ,defaultType: 'textfield'
            ,labelWidth: 115
            ,defaults: {width: 300}
            ,items: [{
			    xtype: 'hidden'
			    ,name: 'frm_mode'
			    ,value: FORM_MODE_NEW
			    ,submitValue: false
			    ,listeners: {'change':{fn: function(obj,value){
                }}}
			},{
			    xtype: 'hidden'
			    ,name: 'loancodeDtl[loan_code]'
			},{
                fieldLabel: 'Years Of Service'
                ,name: 'loancodeDtl[years_of_service]'
            	,xtype: 'numberfield'
                ,anchor:'60%'
                ,allowBlank: false
                ,required: true
                ,maxLength: 4
                ,minValue: 0
                ,autoCreate: {tag: 'input', type: 'text', maxlength: '4'}
				,style: 'text-align: right'
				,allowDecimal: true
			},{
                xtype:'checkbox'
            	,boxLabel: '1/3 of Capital Contribution'
                ,id: 'capital_contribution'
                ,name: 'loancodeDtl[capital_contribution]'	
                ,anchor:'90%'
                ,submitValue: false
            },{
                xtype:'checkbox'
            	,boxLabel: 'Submit Pension Plan'
                ,id: 'pension'
                ,name: 'loancodeDtl[pension]'	
                ,anchor:'90%'
                ,submitValue: false
            },{
                fieldLabel: 'Co-Maker'
                ,xtype: 'numberfield'
                ,name: 'loancodeDtl[guarantor]'
                ,anchor:'50%'
                ,maxLength: 1
                ,autoCreate: {tag: 'input', type: 'text', maxlength: '1'}
            	,style: 'text-align: right'
            }]
		}]
	    ,isModeNew: function() {
	    	return (Ext.getCmp('loancodeDtlForm').getForm().findField('frm_mode').getValue() == FORM_MODE_NEW);
	    }
	    ,setModeNew: function() {
	    	Ext.getCmp('loancodeDtlForm').getForm().findField('frm_mode').setValue(FORM_MODE_NEW);
	    	Ext.getCmp('loancodeDtlForm').buttons[0].setVisible(true);  //cancel button
	    	//Ext.getCmp('loancodeDtlForm').buttons[1].setVisible(false);  //delete button
	    	Ext.getCmp('loancodeDtlForm').buttons[1].setVisible(true);  //save button
			Ext.getCmp('loancodeDtlForm').getForm().findField('loancodeDtl[years_of_service]').enable();
		}
		,setModeUpdate: function() {
			Ext.getCmp('loancodeDtlForm').getForm().findField('frm_mode').setValue(FORM_MODE_UPDATE);
			Ext.getCmp('loancodeDtlForm').buttons[0].setVisible(true);  //cancel button
	    	//Ext.getCmp('loancodeDtlForm').buttons[1].setVisible(true);  //delete button
	    	Ext.getCmp('loancodeDtlForm').buttons[1].setVisible(true);  //save button
			Ext.getCmp('loancodeDtlForm').getForm().findField('loancodeDtl[years_of_service]').disable();
	    }
		,onSave: function(frm){
			var rec = new pecaDataStores.loancodeDtlStore.recordType({
				'loancodeDtl[loan_code]' : Ext.getCmp('loancodeDetail').getForm().findField('loancodeHdr[loan_code]').getValue()
				,'loancodeDtl[years_of_service]' : Ext.getCmp('loancodeDtlForm').getForm().findField('loancodeDtl[years_of_service]').getValue()
				,'loancodeDtl[capital_contribution]' : Ext.getCmp('capital_contribution').getValue() ? 'Y' : 'N'
				,'loancodeDtl[pension]' : Ext.getCmp('pension').getValue() ? 'Y' : 'N'
				,'loancodeDtl[guarantor]' : Ext.getCmp('loancodeDtlForm').getForm().findField('loancodeDtl[guarantor]').getValue()
			});
			pecaDataStores.loancodeDtlStore.insert(0, rec);
			Ext.getCmp('loancodeDtlFormWin').close();
			// pecaDataStores.loancodeDtlStore.reload();
		}
		,onUpdate: function(frm){
			Ext.getCmp('loancodeDtlForm').getForm().updateRecord(Ext.getCmp('loancodeDtlForm').record);
			Ext.getCmp('loancodeDtlFormWin').close();
			// pecaDataStores.loancodeDtlStore.reload();
		}
	};
};

var loancodeDtlFormWin = function(){
	return new Ext.Window({
		id: 'loancodeDtlFormWin'
		,title: 'Loan Code Detail Form'
		,frame: true
		,layout: 'form'
		,width:400
		,autoHeight: true
		,plain: true
		,modal: true
		,resizable: false
		,closable: false
		,constrainHeader: true
		,bodyStyle:{"padding":"5px"}
		,items:[ loancodeDtlForm() ]
	});
};

var loancodeDtlList = function(){
	return {
		xtype: 'grid'
		,id: 'loancodeDtlList'
		,titlebar: false
		,store: pecaDataStores.loancodeDtlStore
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 150
		//,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit: true
			,scrollOffset: 0
		}
		,cm: loancodeDtlColumns
		,record: null
		
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
					loancodeDtlFormWin().show();
					Ext.getCmp('loancodeDtlForm').setModeUpdate();
					Ext.getCmp('loancodeDtlForm').record = rec;
					Ext.getCmp('loancodeDtlForm').getForm().loadRecord(rec);
				}
			}
		}
		,tbar:[{
			text:'New'
			,tooltip:'Add a Loan Code Detail'
			,iconCls: 'icon_ext_add'
			,scope:this
			,handler:function(btn) {
				loancodeDtlFormWin().show();
				loancodeDtlForm().setModeNew();
			}
		},'-'
		,{
			text:'Remove'
			,tooltip:'Delete Selected Loan Code Detail'
			,iconCls: 'icon_ext_del'
			,scope:this
			,handler:function(btn) {
				var index = Ext.getCmp('loancodeDtlList').getSelectionModel().getSelected();
		        if (!index) {
		        	showExtInfoMsg("Please select a Detail to delete.");
		            return false;
		        }
				Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
					if(btn=='yes') {
				        Ext.getCmp('loancodeDtlList').store.remove(index);
					}
				});
			}
		}]
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.loancodeDtlStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};

var loancodeDtl_PCList = function(){
	return {
		xtype: 'grid'
		,id: 'loancodeDtl_PCList'
		,titlebar: false
		,store: pecaDataStores.loancodePCStore
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		//,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit: true
			,scrollOffset: 0
		}
		,cm: loancodePCColumns
		,record: null
	
		,tbar:[{
			text:'Remove'
			,tooltip:'Delete Selected Payment Code Detail'
			,iconCls: 'icon_ext_del'
			,scope:this
			,handler:function(btn) {
			
				if( pecaDataStores.loancodePCStore.getCount() == 1){
		    		showExtErrorMsg('Cannot delete. Loan code needs atleast 1 Payment Code.');
					return false;
				}
				var index = Ext.getCmp('loancodeDtl_PCList').getSelectionModel().getSelected();
		        if (!index) {
		        	showExtInfoMsg("Please select a Payment to delete.");
		            return false;
		        }
				Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
					if(btn=='yes') {
				        Ext.getCmp('loancodeDtl_PCList').store.remove(index);
						// pecaDataStores.loancodePCStore.reload();
					}
				});
			}
		}]
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.loancodePCStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};

var loancodeDetail = function(){
	return {
		xtype:'form'
		,id:'loancodeDetail'
		,region:'center'
		,title: 'Details'
		,hidden:true
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,reader: pecaReaders.loancodeHdrReader
		,buttons:[{
			text: 'Cancel'
			,iconCls: 'icon_ext_cancel'
		    ,handler : function(btn){
				Ext.getCmp('loancodeDetail').hide();
				Ext.getCmp('loancodeDetail').getForm().reset();
				Ext.getCmp('loancodeList').show();
				pecaDataStores.loancodeHdrStore.reload();
		    }
		},{
			text: 'Delete'
			,iconCls: 'icon_ext_del'
			,handler: function(){
				var frm = Ext.getCmp('loancodeDetail').getForm();
				frm.onDelete();
		    }
		},{
			text:'Save'
			,iconCls: 'icon_ext_save'
		    ,handler: function(){
		    	var frm = Ext.getCmp('loancodeDetail').getForm();
				Ext.getCmp('loancodeDetail').getForm().findField('loancodeHdr[max_loan_amount]').isValid(false);
		    	if(frm.isValid()){
		    		if( pecaDataStores.loancodePCStore.getCount() == 0){
		    			showExtErrorMsg('Please add atleast 1 Payment Code.');
		    		}else{
				    	if (frm.isModeNew()) {
			    			frm.onSave(frm);	
				    	} else {
			    			frm.onUpdate(frm);
				    	}
		    		}
		    	}
		    }
		}]
		,items: [{
	        layout:'column'
	        ,items:[{
	            columnWidth:.5
	            ,layout: 'form'
	            ,defaultType: 'textfield'
	            ,labelWidth: 165
	            ,defaults: {width: 300}
	        	,autoscroll: true
	            ,items: [{
				    xtype: 'hidden'
				    ,name: 'frm_mode'
				    ,value: FORM_MODE_NEW
				    ,submitValue: false
				    ,listeners: {'change':{fn: function(obj,value){
	                }}}
				},{
	                fieldLabel: 'Loan Code'
	                ,name: 'loancodeHdr[loan_code]'
	                ,width: 50
	                ,allowBlank: false
	                ,required: true
	                ,maxLength: 4
	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '4'}
	            },{
	                fieldLabel: 'Loan Code Description'
	                ,name: 'loancodeHdr[loan_description]'
	                ,id: 'loancodeHdr[loan_description]'
	                ,width: 200
	                ,allowBlank: false
	                ,required: true
	                ,maxLength: 30
	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
	            },{
	            	xtype:'numberfield'
	            	,fieldLabel: 'Priority'
	                ,name: 'loancodeHdr[priority]'
	                ,width: 50
	                ,allowBlank: false
	                ,required: true
	                ,maxLength: 1
	                ,allowDecimal: false
	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '1'}
	            	,style: 'text-align: right'
	            },{
	            	xtype:'numberfield'
	            	,fieldLabel: "Minimum Month's of Service"
	                ,name: 'loancodeHdr[min_emp_months]'
	                ,width: 50
	                ,allowBlank: false
	                ,required: true
	                ,maxLength: 1
	                ,allowDecimal: false
	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '1'}
	            	,style: 'text-align: right'
	            },{
	            	xtype:'moneyfield'
	            	,fieldLabel: "Maximum Loan Amount"
	                ,name: 'loancodeHdr[max_loan_amount]'
	                ,width: 200
	                ,allowBlank: false
	                ,required: true
	                ,maxLength: 16
	                ,maxValue: 9999999999.99
	                ,minValue: 1
	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '13'}
	            	,listeners: {'blur':{fn: function(obj,value){
	            		var test = Ext.getCmp('loancodeDetail').getForm().findField('loancodeHdr[max_loan_amount]').getValue();
	            		/*if(obj.getValue() == ""){
	            			obj.setValue("1.00");
	            		}*/
	                }}}
	            },{
	            	xtype:'numberfield'
	            	,fieldLabel: 'Minimum Term'
	                ,name: 'loancodeHdr[min_term]'
	                ,width: 50
	                ,allowBlank: false
	                ,required: true
	                ,maxLength: 3
	                ,minValue: 1
	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '3'}
	            	,style: 'text-align: right'
	            },{
	            	xtype:'numberfield'
	            	,fieldLabel: 'Maximum Term'
	                ,name: 'loancodeHdr[max_term]'
	                ,width: 50
	                ,allowBlank: false
	                ,required: true
	                ,maxLength: 3
	                ,minValue: 1
	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '3'}
	            	,style: 'text-align: right'
	            }
	            /* ##### NRB EDIT START ##### */
	            /*
,{
	            	xtype:'pecaNumberField'
	            	,fieldLabel: "Down Payment Percent"
	                ,name: 'loancodeHdr[downpayment_pct]'
	                ,width: 200
	                ,maxLength: 6
	                ,minValue: 0.00
	                ,maxValue: 100.00
	                ,value: 0.00
	                ,allowNegative: false
	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '6'}
	            	,style: 'text-align: right'
	            	,listeners: {'change':{fn: function(obj,value){
	            		if(value == ""){
	            			obj.setValue("0.00");
	            		}
	                }}}
	            }
*/
				/* ##### NRB EDIT END ##### */
				,{
	            	xtype:'pecaNumberField'
	            	,fieldLabel: "Take Home Pay"
	                ,name: 'loancodeHdr[take_home_pay]'
	                ,width: 200
	                ,maxLength: 6
	                ,value: '0.00'
	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '6'}
	            	,style: 'text-align: right'
	            	,listeners: {'change':{fn: function(obj,value){
	            		if(value == ""){
	            			obj.setValue("0.00");
	            		}
	                }}}
	            }
	            ,{
	                xtype:'checkbox'
	                ,boxLabel: 'Submit Payslip'                
	                ,id: 'submit_payslip'
	                ,name: 'loancodeHdr[submit_payslip]'	
	                ,anchor:'95%'
	                ,submitValue: false
	            }]
	        },{
	        	 columnWidth:.5
	             ,layout: 'form'
	             ,defaultType: 'textfield'
	             ,labelWidth: 185
	             ,defaults: {width: 300}
	             ,items: [
	             /* ##### NRB EDIT START ##### */
	             /*
{
	                xtype:'checkbox'
	                ,boxLabel: 'Post Dated Checks'                
	                ,id: 'post_dated_checks'
	                ,name: 'loancodeHdr[post_dated_checks]'	
	                ,anchor:'95%'
	                ,submitValue: false
	            }
	            ,*/				
				/* ##### NRB EDIT END ##### */
				{
	                xtype:'checkbox'
	                ,boxLabel: 'BSP Computation'                
	                ,id: 'bsp_computation'
	                ,name: 'loancodeHdr[bsp_computation]'	
	                ,anchor:'95%'
	                ,submitValue: false
	            },{
	                xtype:'checkbox'
	                ,boxLabel: "Single Borrower's Limit"                
	                ,id: 'bsp_sbl'
	                ,name: 'loancodeHdr[bsp_sbl]'	
	                ,anchor:'95%'
	                ,submitValue: false
	            }
	            /* ##### NRB EDIT START ##### */
	            /*
,{
	                xtype:'checkbox'
	                ,boxLabel: 'Avail After Full Payment'                
	                ,id: 'avail_after_full_payment'
	                ,name: 'loancodeHdr[avail_after_full_payment]'	
	                ,anchor:'95%'
	                ,submitValue: false
	            }
*/
				/* ##### NRB EDIT END ##### */
				,{
	                xtype:'checkbox'
	                ,boxLabel: 'Restructure'
	                ,id: 'restructure'
	                ,name: 'loancodeHdr[restructure]'
	                ,anchor:'95%'
	                ,submitValue: false
	            },{
	            	xtype:'pecaNumberField'
	            	,fieldLabel: 'Employee Interest Percentage'
	                ,name: 'loancodeHdr[emp_interest_pct]'
	                ,width: 50
	                ,maxLength: 6
	                ,minValue: 0.00
	                ,maxValue: 100.00
	                ,value: 0.00
	                ,allowNegative: false
	                ,required: true
	                ,allowBlank: false
	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '6'}
	            	,style: 'text-align: right'
	            	,listeners: {'change':{fn: function(obj,value){
	            		if(value == ""){
	            			obj.setValue("0.00");
	            		}
	                }}}
	            },{
	            	xtype:'pecaNumberField'
	            	,fieldLabel: 'Percent of Company Share'
	                ,name: 'loancodeHdr[comp_share_pct]'
	                ,width: 50
	                ,maxLength: 6
	                ,minValue: 0.00
	                ,maxValue: 100.00
	                ,value: 0.00
	                ,allowNegative: false
	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '6'}
	            	,style: 'text-align: right'
	            	,listeners: {'change':{fn: function(obj,value){
	            		if(value == ""){
	            			obj.setValue("0.00");
	            		}
	                }}}
	            },{
	                xtype:'checkbox'
	                ,boxLabel: 'Payroll Deduction'                
	                ,id: 'payroll_deduction'
	                ,name: 'loancodeHdr[payroll_deduction]'	
	                ,anchor:'95%'
	                ,submitValue: false
	            }
	            /* ##### NRB EDIT START ##### */
	            /*
,{
	                xtype:'checkbox'
	                ,boxLabel: 'Interest Deducted in Advance'                
	                ,id: 'unearned_interest'
	                ,name: 'loancodeHdr[unearned_interest]'	
	                ,anchor:'95%'
	                ,submitValue: false
	            }
*/
				/* ##### NRB EDIT END ##### */
				,{
	                xtype:'checkbox'
	                ,boxLabel: 'Initial Interest'                
	                ,id: 'interest_earned'
	                ,name: 'loancodeHdr[interest_earned]'	
	                ,anchor:'95%'
	                ,submitValue: false
	            },new Ext.form.ComboBox({
	                fieldLabel: 'Transaction Code'
	                ,hiddenName: 'loancodeHdr[transaction_code]'
	        	    ,typeAhead: true
	        	    ,triggerAction: 'all'
	        	    ,lazyRender:true
	        	    ,store: pecaDataStores.loanLNStore
	        	    ,mode: 'local'
	        	    ,valueField: 'transaction_code'
	        	    ,displayField: 'transcode_description_formatted'
	        	    ,anchor:'95%'
	        	    ,emptyText: 'Please Select'
	        	    ,forceSelection: true
	        	    ,submitValue: false
	        	    ,allowBlank: false
	        	    ,required: true
					,listeners:{
						scope: this,
							'select': function() {
									selected_text = Ext.getCmp('loancodeDetail').getForm().findField('loancodeHdr[transaction_code]').lastSelectionText;
									formatted = Ext.util.Format.htmlDecode(selected_text);
									Ext.getCmp('loancodeDetail').getForm().findField('loancodeHdr[transaction_code]').setValue(formatted);
								}	
            		}
	        	})]
	        }]
	    }
		,{
			layout: 'column'
			,items:[{	
				columnWidth: .50
				,layout: 'fit'
				,xtype:'fieldset'
				,id: 'loancodeDtlFieldSet'
				,title: 'Loan Code Details'
				,bodyStyle:{'padding':'10px'}	
	            ,defaultType: 'grid'
	            ,height: 300
				,items: [loancodeDtlList()]
			},{
				columnWidth: .50
				,anchor: '90%'
				,bodyStyle:{'padding':'10px'}
				,xtype:'fieldset'
				,id: 'loancodePCFieldSet'
				,title: 'Payment Code'
				,height: 300
				,items:[{
	            	xtype: 'panel'
	            	,anchor: '100%'
	            	,layout:'column'
        	        ,items:[{
        	            columnWidth:.85
        	            ,layout: 'form'
        	            ,items: [new Ext.form.ComboBox({
    	                	fieldLabel: 'Payment Code'
    		                ,name: 'payment_code'
    		        	    ,typeAhead: true
    		        	    ,triggerAction: 'all'
    		        	    ,lazyRender:true
    		        	    ,store: pecaDataStores.loanLPStore
    		        	    ,mode: 'local'
    		        	    ,valueField: 'transaction_code'
    		        	    ,displayField: 'transaction_description'
    		        	    ,width: 215
    		        	    ,emptyText: 'Please Select'
    		        	    ,forceSelection: true
    		        	    ,submitValue: false
    		        	})]
        	        },{
        	            columnWidth:.15
        	            ,xtype: 'panel'
    	                ,border: false
    	                ,height: '20%'
    	                ,width: 50
    	                ,bodyCfg: { tag:'center'}
    	                ,items: [ new Ext.Button({
    	                    text: 'Add'
    	                    ,iconCls: 'icon_ext_add'
    	                    ,width: 50
    	                    ,handler: function(){
								if(Ext.getCmp('loancodeDetail').getForm().findField('loancodeHdr[loan_code]').getValue() == ''){
									showExtErrorMsg("Please input Loan Code first.");
    	                			return;
								}
    	                		if(Ext.getCmp('loancodeDetail').getForm().findField('payment_code').getValue() == '' ){
										showExtErrorMsg("Please select a Payment Code.");
    	                			return;
								}
    	                		if(Ext.getCmp('loancodeDetail').getForm().findField('payment_code').getValue() == '' ||
    	                				Ext.getCmp('loancodeDetail').getForm().findField('loancodeHdr[loan_code]').getValue() == ''){
										showExtErrorMsg("Please select a Payment Code.");
    	                			return;
								}
	    	                	var rec = new pecaDataStores.loancodePCStore.recordType({
	    	        				'loan_code' : Ext.getCmp('loancodeDetail').getForm().findField('loancodeHdr[loan_code]').getValue()
	    	        				,'transaction_code' : Ext.getCmp('loancodeDetail').getForm().findField('payment_code').getValue()
	    	        				,'transaction_description' : Ext.getCmp('loancodeDetail').getForm().findField('payment_code').lastSelectionText
	    	        			});
	    	        			pecaDataStores.loancodePCStore.insert(0, rec);
	    	        			// if(!loancodeDetail().isModeNew() )
	    	        				// pecaDataStores.loancodePCStore.reload();
	    	        			Ext.getCmp('loancodeDetail').getForm().findField('loancodeHdr[loan_code]').setReadOnly(true);
	    	    		    }
    	                })]
        	        }]
	            },{	
					layout: 'fit'
		            ,defaultType: 'grid'
		            ,height: 225
					,items: [loancodeDtl_PCList()]
				}]
			}]
		}
		]
	    ,isModeNew: function() {
	    	return (Ext.getCmp('loancodeDetail').getForm().findField('frm_mode').getValue() == FORM_MODE_NEW);
	    }
	    ,setModeNew: function() {
	    	Ext.getCmp('loancodeDetail').getForm().findField('frm_mode').setValue(FORM_MODE_NEW);
	    	Ext.getCmp('loancodeDetail').buttons[0].setVisible(true);  //cancel button
	    	Ext.getCmp('loancodeDetail').buttons[1].setVisible(false);  //delete button
	    	Ext.getCmp('loancodeDetail').buttons[2].setVisible(true);  //save button
	    	Ext.getCmp('loancodeDetail').getForm().findField('loancodeHdr[loan_code]').focus('',250);
			Ext.getCmp('loancodeDetail').getForm().findField('loancodeHdr[loan_code]').setReadOnly(false);
			Ext.getCmp('loancodeDetail').getForm().findField('loancodeHdr[loan_code]').removeClass('x-item-disabled');
			Ext.getCmp('loancodeDetail').getForm().findField('loancodeHdr[max_loan_amount]').setValue('1.00');
			Ext.getCmp('loancodeDetail').getForm().findField('loancodeHdr[max_loan_amount]').setRawValue('1.00');
			Ext.getCmp('loancodeDtlList').getTopToolbar().setDisabled(true);
			Ext.getCmp('loancodeDtlFieldSet').setDisabled(true);
			pecaDataStores.loancodePCStore.autoSave = false;
		}
		,setModeUpdate: function() {
			Ext.getCmp('loancodeDetail').getForm().findField('frm_mode').setValue(FORM_MODE_UPDATE);
			Ext.getCmp('loancodeDetail').buttons[0].setVisible(true);  //cancel button
	    	Ext.getCmp('loancodeDetail').buttons[1].setVisible(true);  //delete button
	    	Ext.getCmp('loancodeDetail').buttons[2].setVisible(true);  //save button
	    	
	    	//can't update record
			if(_PERMISSION[131]==0){
				Ext.getCmp('loancodeDetail').buttons[2].setDisabled(true);  //save button
			}
			//can't delete record
			if(_PERMISSION[35]==0){
				Ext.getCmp('loancodeDetail').buttons[1].setDisabled(true);  //save button
			}
	    	
	    	Ext.getCmp('loancodeDetail').getForm().findField('loancodeHdr[loan_description]').focus('',250);
			Ext.getCmp('loancodeDetail').getForm().findField('loancodeHdr[loan_code]').setReadOnly(true);
			Ext.getCmp('loancodeDetail').getForm().findField('loancodeHdr[loan_code]').addClass('x-item-disabled');
			Ext.getCmp('loancodeDtlList').getTopToolbar().setDisabled(false);
			Ext.getCmp('loancodeDtlFieldSet').setDisabled(false);
			pecaDataStores.loancodePCStore.autoSave = true;
	    }
		,onSave: function(frm){
			frm.submit({
    			url: '/loan_code/addHdr' 
    			,method: 'POST'
    				,params: {
					'loancodeHdr[submit_payslip]': Ext.getCmp('submit_payslip').getValue() ? 'Y' : 'N'
					/* ##### NRB EDIT START ##### */
        			/* ,'loancodeHdr[post_dated_checks]': Ext.getCmp('post_dated_checks').getValue() ? 'Y' : 'N' */
        			,'loancodeHdr[post_dated_checks]': 'N'
        			/* ##### NRB EDIT END ##### */
        			,'loancodeHdr[bsp_sbl]': Ext.getCmp('bsp_sbl').getValue() ? 'Y' : 'N'
        			/* ##### NRB EDIT START ##### */
    				/* ,'loancodeHdr[avail_after_full_payment]': Ext.getCmp('avail_after_full_payment').getValue() ? 'Y' : 'N' */
    				,'loancodeHdr[avail_after_full_payment]': 'N'
					/* ##### NRB EDIT END ##### */
					,'loancodeHdr[restructure]': Ext.getCmp('restructure').getValue() ? 'Y' : 'N'
					,'loancodeHdr[payroll_deduction]': Ext.getCmp('payroll_deduction').getValue() ? 'Y' : 'N'
					/* ##### NRB EDIT START ##### */
					/* ,'loancodeHdr[unearned_interest]': Ext.getCmp('unearned_interest').getValue() ? 'Y' : 'N' */
					,'loancodeHdr[unearned_interest]': 'N'
					/* ##### NRB EDIT END ##### */
					,'loancodeHdr[bsp_computation]': Ext.getCmp('bsp_computation').getValue() ? 'Y' : 'N'
					,'loancodeHdr[interest_earned]': Ext.getCmp('interest_earned').getValue() ? 'Y' : 'N'
        			,auth:_AUTH_KEY, 'loancodeHdr[created_by]': _USER_ID	
    			}
    			,waitMsg: 'Creating Data...'
    			,success: function(form, action) {
    				showExtInfoMsg(action.result.msg);
    				frm.setModeUpdate();
    				pecaDataStores.loancodePCStore.save();
    				// pecaDataStores.loancodePCStore.reload();
    			}
    			,failure: function(form, action) {
    				if(action.result.error_code == 2){
    					showExtErrorMsg(action.result.msg);
    				}else{
	    				Ext.Msg.confirm('Confirm Action',action.result.msg,function(btn) {
	    					if(btn=='yes'){
	    						form.onUpdate(form);
	    					}
	    				});
    				}
    			}	
    		});
		}
		,onUpdate: function(frm){
			frm.submit({
    			url: '/loan_code/updateHdr' 
    			,method: 'POST'
    			,waitMsg: 'Updating Data...'
    			,params: {
					'loancodeHdr[submit_payslip]': Ext.getCmp('submit_payslip').getValue() ? 'Y' : 'N'
					/* ##### NRB EDIT START ##### */
        			/* ,'loancodeHdr[post_dated_checks]': Ext.getCmp('post_dated_checks').getValue() ? 'Y' : 'N' */
        			/* ##### NRB EDIT END ##### */
        			,'loancodeHdr[bsp_sbl]': Ext.getCmp('bsp_sbl').getValue() ? 'Y' : 'N'
    				/* ##### NRB EDIT START ##### */
    				/* ,'loancodeHdr[avail_after_full_payment]': Ext.getCmp('avail_after_full_payment').getValue() ? 'Y' : 'N' */
					/* ##### NRB EDIT END ##### */
					,'loancodeHdr[restructure]': Ext.getCmp('restructure').getValue() ? 'Y' : 'N'
					,'loancodeHdr[payroll_deduction]': Ext.getCmp('payroll_deduction').getValue() ? 'Y' : 'N'
					/* ##### NRB EDIT START ##### */
					/* ,'loancodeHdr[unearned_interest]': Ext.getCmp('unearned_interest').getValue() ? 'Y' : 'N' */
					/* ##### NRB EDIT END ##### */
					,'loancodeHdr[bsp_computation]': Ext.getCmp('bsp_computation').getValue() ? 'Y' : 'N'
					,'loancodeHdr[interest_earned]': Ext.getCmp('interest_earned').getValue() ? 'Y' : 'N'
        			,auth:_AUTH_KEY, 'loancodeHdr[modified_by]': _USER_ID	
    			}
    			,success: function(form, action) {
    				showExtInfoMsg(action.result.msg);
        			frm.setModeUpdate();
        			pecaDataStores.loancodePCStore.save();
    				// pecaDataStores.loancodePCStore.reload();
    			}
    			,failure: function(form, action) {
    				showExtErrorMsg(action.result.msg);
    			}	
    		});
		}
		,onDelete: function(){
			var frm = Ext.getCmp('loancodeDetail').getForm();
				Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
					if(btn=='yes') {
						Ext.getCmp('loancodeDetail').getForm().submit({
							url: '/loan_code/deleteHdr' 
							,method: 'POST'
							,params: {auth:_AUTH_KEY, 'loancodeHdr[modified_by]': _USER_ID}
							,waitMsg: 'Deleting Data...'
							,clientValidation: false
							,success: function(form, action) {						
								showExtInfoMsg(action.result.msg);
								Ext.getCmp('loancodeDetail').hide();
								Ext.getCmp('loancodeDetail').getForm().reset();
								Ext.getCmp('loancodeList').show();
								// pecaDataStores.loancodeHdrStore.load();
								if (pecaDataStores.loancodeHdrStore.getCount() % MAX_PAGE_SIZE == 1){
									pecaDataStores.loancodeHdrStore.load({params: {start:pecaDataStores.loancodeHdrStore.getTotalCount() - MAX_PAGE_SIZE - 1, limit:MAX_PAGE_SIZE}});
								} else{
									pecaDataStores.loancodeHdrStore.reload();
								}
							}
							,failure: function(form, action) {
								showExtErrorMsg(action.result.msg);
							}	
						});
					}
				});
		}
	};
};