//Let's pretend we rendered our grid-columns with meta-data from our ORM framework.
var pdColumns =  new Ext.grid.ColumnModel( 
	[
		{id: 'employee_id', header: 'Employee ID', width: 100, sortable: true, dataIndex: 'newpayroll[employee_id]',align:'right'}
		,{header: 'Last Name', width: 150, sortable: true, dataIndex: 'last_name'}
		,{header: 'First Name', width: 150, sortable: true, dataIndex: 'first_name'}
		,{id: 'start_date', header: 'Start Date', width: 80, sortable: true, dataIndex: 'newpayroll[start_date]',align:'center'}
		,{header: 'End Date', width: 80, sortable: true, dataIndex: 'newpayroll[end_date]',align: 'center'}
		,{header: 'Amount', width: 125, sortable: true, dataIndex: 'newpayroll[amount]',align: 'right',renderer: function(value, rec){ 	return Ext.util.Format.number(value,'0,000.00');}}
	]
);


var employeeColumns =  new Ext.grid.ColumnModel( 
	[
		{id:'employee_id', header: 'Employee ID', width: 100, sortable: true, dataIndex: 'employee_id',align:'right'}
		,{header: 'Last Name', width: 150, sortable: true, dataIndex: 'last_name'}
		,{header: 'First Name', width: 150, sortable: true, dataIndex: 'first_name'}
	]
);

var pdDetail = function(){
	return {
		xtype:'form'
		,id:'pdDetail'
		,region:'center'
		,title: 'Details'
		,hidden:true
		,anchor: '100%'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,reader: pecaReaders.pdReader
		,buttons:[{
			text: 'Cancel'
			,iconCls: 'icon_ext_cancel'
		    ,handler : function(btn){
				Ext.getCmp('pdDetail').hide();
				Ext.getCmp('pdList').show();
				Ext.getCmp('pdDetail').getForm().reset();
				pecaDataStores.pdStore.reload();
		    }
		},{
			text: 'Delete'
			,iconCls: 'icon_ext_del'
			,handler: function(){
				var frm = Ext.getCmp('pdDetail').getForm();
				frm.onDelete();
		    }
		},{
			text:'Save'
			,iconCls: 'icon_ext_save'
		    ,handler: function(){
		    	var frm = Ext.getCmp('pdDetail').getForm();
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
			,anchor: '100%'
			,items: [	
				{
					xtype: 'hidden'
					,name: 'frm_mode'
					,value: FORM_MODE_NEW
					,submitValue: false
					,listeners: {'change':{fn: function(obj,value){
					}}}
				}
				,{
					layout: 'column'
					,border: false
					,items: [				
						{
							layout: 'form'
							,labelWidth: 80
							,labelAlign: 'left'
							,border: false
							,hideBorders: false
							,width: 200
							,items: [
								{
									xtype: 'textfield'
									,fieldLabel: 'Employee'	
									,emptyText: 'ID'	
									,anchor: '95%'
									,name: 'newpayroll[employee_id]'
									,allowBlank: false
									,required: true
									,maxLength: 8
									,autoCreate: {tag: 'input', type: 'text', maxlength: '8'}
								,enableKeyEvents: true
					    		,style: 'text-align: right'
								,listeners: {
									specialkey: function(txt,evt){
										if (evt.getKey() == evt.ENTER) {
											pecaDataStores.pdEmployeeStore.load({params: {
												'employee_id': Ext.getCmp('pdDetail').getForm().findField('newpayroll[employee_id]').getValue()
												,'first_name': Ext.getCmp('pdDetail').getForm().findField('first_name').getValue()
												,'last_name': Ext.getCmp('pdDetail').getForm().findField('last_name').getValue()
												,start:0, limit:MAX_PAGE_SIZE}});
											pd_employeeListWin().show();
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
							}
							]
						}
						,{
							layout: 'form'
							,labelWidth: 1
							,labelAlign: 'left'
							,border: false
							,hideBorders: false
							,width: 200
							,items: [
								{
									xtype: 'textfield'							
									,anchor: '95%'		
									,hideLabel: true
									,emptyText: 'Last Name'
									,name: 'last_name'
									,maxLength: 30
									,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
									,enableKeyEvents: true
									,listeners: {
										specialkey: function(txt,evt){
											if (evt.getKey() == evt.ENTER) {
												pecaDataStores.pdEmployeeStore.load({params: {
													'employee_id': Ext.getCmp('pdDetail').getForm().findField('newpayroll[employee_id]').getValue()
													,'first_name': Ext.getCmp('pdDetail').getForm().findField('first_name').getValue()
													,'last_name': Ext.getCmp('pdDetail').getForm().findField('last_name').getValue()
													,start:0, limit:MAX_PAGE_SIZE}});
												pd_employeeListWin().show();
											}
										}
									}
								}
							]
						}
						,{
							layout: 'form'
							,labelWidth: 1
							,labelAlign: 'left'
							,border: false
							,width: 200
							,items: [
								{
									xtype: 'textfield'							
									,anchor: '98%'								
									,hideLabel: true
									,emptyText: 'First Name'
									,name: 'first_name'
									,maxLength: 30
									,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
									,enableKeyEvents: true
									,listeners: {
										specialkey: function(txt,evt){
											if (evt.getKey() == evt.ENTER) {
												pecaDataStores.pdEmployeeStore.load({params: {
													'employee_id': Ext.getCmp('pdDetail').getForm().findField('newpayroll[employee_id]').getValue()
													,'first_name': Ext.getCmp('pdDetail').getForm().findField('first_name').getValue()
													,'last_name': Ext.getCmp('pdDetail').getForm().findField('last_name').getValue()
													,start:0, limit:MAX_PAGE_SIZE}});
												pd_employeeListWin().show();
											}
										}
									}
								}
							]
						}
						,{
							layout: 'form'
							,border: false
							,width: 110
							,hideBorders: false
							,items: [
								{
									xtype: 'button'
									,id: 'sbutton'
									,text: 'Search'
									,iconCls: 'icon_ext_search'										
									,width: 75
									,handler: function(){
										pecaDataStores.pdEmployeeStore.load({params: {
											'employee_id': Ext.getCmp('pdDetail').getForm().findField('newpayroll[employee_id]').getValue()
											,'first_name': Ext.getCmp('pdDetail').getForm().findField('first_name').getValue()
											,'last_name': Ext.getCmp('pdDetail').getForm().findField('last_name').getValue()
											,start:0, limit:MAX_PAGE_SIZE}});
										pd_employeeListWin().show();
									}
								}
							]
						}
					]
				}
				,{
					layout: 'form'
					,height: 16
					,border: false
				}				
				,{
					layout: 'form'
					,labelWidth: 150
					,labelAlign: 'left'
					,layout: 'form'
					,width: 374
					,padding: 10
					,items: [
						{
							xtype: 'combo'
							,fieldLabel: 'Transaction Type'
							,anchor: '100%'
							,id: 'ttype'
							,hiddenName: 'newpayroll[transaction_code]'
							,editable: false
							,typeAhead: true
							,triggerAction: 'all'
							,lazyRender:true
							,store: pecaDataStores.ttypeStore
							,mode: 'local'
							,valueField: 'transaction_code'
							,displayField: 'transaction_description'									
							,forceSelection: true
							,submitValue: false
							,emptyText: 'Please Select'
							,allowBlank: false
							,required: true
						}
						,{
							xtype: 'datefield'
							,fieldLabel: 'Start Date'
							,anchor: '100%'
							,id: 'startD'
							,name: 'newpayroll[start_date]'
						
							,allowBlank: false
							,required: true
							,maxLength: 10
							,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
							,style: 'text-align: right'
							,validationEvent: 'change'
						}
						,{
							xtype: 'datefield'
							,fieldLabel: 'End Date'
							,anchor: '100%'
							,id: 'endD'
							,name: 'newpayroll[end_date]'
						
							,allowBlank: false
							,required: true
							,maxLength: 10
							,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
							,style: 'text-align: right'
							,validationEvent: 'change'
						}
						,{
							xtype: 'moneyfield'
							,fieldLabel: "Amount"
							,anchor: "100%"
							,id: 'amt'
							,name: 'newpayroll[amount]'
							,allowBlank: false
							,required: true
							,maxLength: 16
							,maxValue: 9999999999.99
							,minValue: 0
							,autoCreate: {tag: 'input', type: 'text', maxlength: '13'}
						}
					]
				}
			]

		}]
	    ,isModeNew: function() {
	    	return (Ext.getCmp('pdDetail').getForm().findField('frm_mode').getValue() == FORM_MODE_NEW);
	    }
	    ,setModeNew: function() {
	    	Ext.getCmp('pdDetail').getForm().findField('frm_mode').setValue(FORM_MODE_NEW);
	    	Ext.getCmp('pdDetail').buttons[0].setVisible(true);  //cancel button
	    	Ext.getCmp('pdDetail').buttons[1].setVisible(false);  //delete button
	    	Ext.getCmp('pdDetail').buttons[2].setVisible(true);  //save button
	    	Ext.getCmp('pdDetail').getForm().findField('newpayroll[employee_id]').focus('',250);
			Ext.getCmp('pdDetail').getForm().findField('newpayroll[employee_id]').setReadOnly(false);
			Ext.getCmp('pdDetail').getForm().findField('newpayroll[employee_id]').removeClass('x-item-disabled');
			Ext.getCmp('pdDetail').getForm().findField('last_name').setReadOnly(false);
			Ext.getCmp('pdDetail').getForm().findField('last_name').removeClass('x-item-disabled');
			Ext.getCmp('pdDetail').getForm().findField('first_name').setReadOnly(false);
			Ext.getCmp('pdDetail').getForm().findField('first_name').removeClass('x-item-disabled');			
			Ext.getCmp('pdDetail').getForm().findField('newpayroll[amount]').setValue(0);
			Ext.getCmp('pdDetail').getForm().findField('newpayroll[transaction_code]').setReadOnly(false);
			Ext.getCmp('pdDetail').getForm().findField('newpayroll[transaction_code]').removeClass('x-item-disabled');
			Ext.getCmp('pdDetail').getForm().findField('newpayroll[start_date]').setReadOnly(false);
			Ext.getCmp('pdDetail').getForm().findField('newpayroll[start_date]').removeClass('x-item-disabled');
			Ext.getCmp('pdDetail').findById('sbutton').setVisible(true);
			pecaDataStores.ttypeStore.load();
		}
		,setModeUpdate: function() {
			Ext.getCmp('pdDetail').getForm().findField('frm_mode').setValue(FORM_MODE_UPDATE);
			Ext.getCmp('pdDetail').buttons[0].setVisible(true);  //cancel button
	    	Ext.getCmp('pdDetail').buttons[1].setVisible(true);  //delete button
	    	Ext.getCmp('pdDetail').buttons[2].setVisible(true);  //save button
	    	
	    	//can't update record
			if(_PERMISSION[135]==0){
				Ext.getCmp('pdDetail').buttons[2].setDisabled(true);	
			}
			//can't delete record
			if(_PERMISSION[38]==0){
				Ext.getCmp('pdDetail').buttons[1].setDisabled(true);	
			}
			
	    	Ext.getCmp('pdDetail').getForm().findField('newpayroll[transaction_code]').focus('',250);
			Ext.getCmp('pdDetail').getForm().findField('newpayroll[employee_id]').setReadOnly(true);
			Ext.getCmp('pdDetail').getForm().findField('newpayroll[employee_id]').addClass('x-item-disabled');
			Ext.getCmp('pdDetail').getForm().findField('last_name').setReadOnly(true);
			Ext.getCmp('pdDetail').getForm().findField('last_name').addClass('x-item-disabled');
			Ext.getCmp('pdDetail').getForm().findField('first_name').setReadOnly(true);
			Ext.getCmp('pdDetail').getForm().findField('first_name').addClass('x-item-disabled');
			Ext.getCmp('pdDetail').getForm().findField('newpayroll[transaction_code]').setReadOnly(true);
			Ext.getCmp('pdDetail').getForm().findField('newpayroll[transaction_code]').addClass('x-item-disabled');
			Ext.getCmp('pdDetail').getForm().findField('newpayroll[start_date]').setReadOnly(true);
			Ext.getCmp('pdDetail').getForm().findField('newpayroll[start_date]').addClass('x-item-disabled');
			Ext.getCmp('pdDetail').findById('sbutton').setVisible(false);
	    }
		,onSave: function(frm){
			frm.submit({
    			url: '/payroll_deduction/add' 
    			,method: 'POST'
    			,params: {auth:_AUTH_KEY
							, 'newpayroll[created_by]': _USER_ID}
    			,waitMsg: 'Creating new payroll deduction...'
    			,success: function(form, action) {
					showExtInfoMsg( action.result.msg);
				frm.setModeUpdate();
				Ext.getCmp('pdDetail').getForm().load({
					url: '/payroll_deduction/show'
					,params: {'newpayroll[employee_id]':(Ext.getCmp('pdDetail').getForm().findField('newpayroll[employee_id]').getValue())
								,'newpayroll[start_date]':(Ext.getCmp('pdDetail').getForm().findField('newpayroll[start_date]').getValue())
								,'newpayroll[transaction_code]':(Ext.getCmp('pdDetail').getForm().findField('newpayroll[transaction_code]').getValue())
								,auth:_AUTH_KEY}
					,method: 'POST'
					,waitMsgTarget: true
				});
    			}
    			,failure: function(form, action) {
    				if(action.result.error_code == 19){
    					Ext.Msg.confirm('Confirm Action',action.result.msg,function(btn) {
	    					if(btn=='yes'){
	    						form.onAdjust(form, 'add');
	    					}
	    				});
    				}
					/*else if (action.result.error_code == 21 || action.result.error_code == 22 || action.result.error_code == 26 || action.result.error_code == 27 || action.result.error_code == 28|| action.result.error_code == 29|| action.result.error_code == 30 || action.result.error_code == 153){
						showExtErrorMsg(action.result.msg);
    				}*/
					else if(action.result.error_code == 2){
	    				Ext.Msg.confirm('Confirm Action',action.result.msg,function(btn) {
	    					if(btn=='yes'){
	    						form.onUpdate(form);
	    					}
	    				});
    				}
					else{
						showExtErrorMsg(action.result.msg);
					}
    			}	
    		});
		}
		,onAdjust: function(frm, param){
			frm.submit({
    			url: '/payroll_deduction/adjustPD/'+param
    			,method: 'POST'
    			,waitMsg: 'Adjusting Payroll Deduction...'
    			,params: { auth:_AUTH_KEY, 'newpayroll[modified_by]': _USER_ID, 'newpayroll[created_by]': _USER_ID	
    			}
    			,success: function(form, action) {
    				showExtInfoMsg( action.result.msg);
        			frm.setModeUpdate();
    			}
    			,failure: function(form, action) {
    				if(action.result.error_code == 19){
    					Ext.Msg.confirm('Confirm Action',action.result.msg,function(btn) {
	    					if(btn=='yes'){
	    						form.onAdjust(form,param);
	    					}
	    				});
					}
					else if (action.result.error_code == 1){
	    				Ext.Msg.confirm('Confirm Action',action.result.msg,function(btn) {
	    					if(btn=='yes'){
	    						form.onUpdate(form);
	    					}
	    				});
    				}
					else{
						showExtErrorMsg(action.result.msg);	
    				}
    			}	
    		});
		}
		,onUpdate: function(frm){
			frm.submit({
    			url: '/payroll_deduction/update' 
    			,method: 'POST'
    			,waitMsg: 'Updating Data...'
    			,params: {
        			auth:_AUTH_KEY, 'newpayroll[modified_by]': _USER_ID	
    			}
    			,success: function(form, action) {
				showExtInfoMsg(action.result.msg);
        			frm.setModeUpdate();
    			}
    			,failure: function(form, action) {
					if(action.result.error_code == 19){
							Ext.Msg.confirm('Confirm Action',action.result.msg,function(btn) {
								if(btn=='yes'){
									form.onAdjust(form,'update');
								}
							});
						}
						
					else{
						showExtErrorMsg(action.result.msg);
					}
    			}	
    		});
		}
		,onDelete: function(){
			Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
				if(btn=='yes') {
					Ext.getCmp('pdDetail').getForm().submit({
						url: '/payroll_deduction/delete' 
						,method: 'POST'
						,params: {auth:_AUTH_KEY, 'newpayroll[modified_by]': _USER_ID}
						,waitMsg: 'Deleting Data...'
						,clientValidation: false
						,success: function(form, action) {
							showExtInfoMsg(action.result.msg);
			    			Ext.getCmp('pdDetail').setModeNew();
			    			Ext.getCmp('pdDetail').getForm().reset();
			    			Ext.getCmp('pdDetail').hide();
							Ext.getCmp('pdList').show();
							//pecaDataStores.pdStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
							if (pecaDataStores.pdStore.getCount() % MAX_PAGE_SIZE == 1){
								var page = pecaDataStores.pdStore.getTotalCount() - MAX_PAGE_SIZE - 1;
								pecaDataStores.pdStore.load({params: {start:page<0?0:page, limit:MAX_PAGE_SIZE}});
							} else{
								pecaDataStores.pdStore.reload();
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

var pdList = function(){
	return {
		xtype: 'grid'
		,id: 'pdList'
		,titlebar: false
		,store: pecaDataStores.pdStore
		,region: 'center'
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 425
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit: true
			,scrollOffset: 13
		}
		,cm: pdColumns
		
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
					Ext.getCmp('pdList').hide();
					Ext.getCmp('pdDetail').show();
					Ext.getCmp('pdDetail').getForm().setModeUpdate();
					Ext.getCmp('pdDetail').getForm().load({
				    	url: '/payroll_deduction/show'
				    	,params: {'newpayroll[employee_id]':(rec.get('newpayroll[employee_id]'))
									,'newpayroll[start_date]':(rec.get('newpayroll[start_date]'))
									,'newpayroll[transaction_code]':(rec.get('newpayroll[transaction_code]'))
									,auth:_AUTH_KEY}
				    	,method: 'POST'
				    	,waitMsgTarget: true
					});
					
				}
			}
			,'render':{
				scope:this
				,fn:function(grid){
					//search textfield and button
					if(_PERMISSION[62]==0){
						Ext.getCmp('pd_employeeId').setDisabled(true);
						Ext.getCmp('pd_last').setDisabled(true);
						Ext.getCmp('pd_first').setDisabled(true);
						Ext.getCmp('pdSearchID').setDisabled(true);	
					}else{
						Ext.getCmp('pd_employeeId').setDisabled(false);
						Ext.getCmp('pd_last').setDisabled(false);
						Ext.getCmp('pd_first').setDisabled(false);
						Ext.getCmp('pdSearchID').setDisabled(false);
						pecaDataStores.pdStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
						pecaDataStores.ttypeStore.load();
					}
					//new button
					if(_PERMISSION[14]==0){
						Ext.getCmp('pdNewID').setDisabled(true);	
					}else{
						Ext.getCmp('pdNewID').setDisabled(false);
						pecaDataStores.ttypeStore.load();
					}
					//delete button
					if(_PERMISSION[38]==0){
						Ext.getCmp('pdDeleteID').setDisabled(true);	
					}else{
						Ext.getCmp('pdDeleteID').setDisabled(false);
					}
				}
			}
		}
		,tbar:[{
			xtype: 'label'
			,text: 'Employee :'
            ,fieldLabel: ' '
            ,labelSeparator: ' '
		},' '
		,{
			xtype: 'textfield'
			,id: 'pd_employeeId'
			,fieldLabel: 'Employee'	
			,emptyText: 'ID'	
			,anchor: '95%'
			,autoCreate: {tag: 'input', type: 'numeric', maxlength: '8'}
			,enableKeyEvents: true
			,style: 'text-align: right'
			,listeners: {
				specialkey: function(txt,evt){
					if (evt.getKey() == evt.ENTER) {
						pecaDataStores.pdStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});	
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
		},' '
		,{
			
			xtype: 'textfield'	
			,id: 'pd_last'							
			,anchor: '95%'						
			,hideLabel: true
			,emptyText: 'Last Name'
			,maxLength: 30
			,autoCreate: {tag: 'input', type: 'numeric', maxlength: '30'}
			,listeners: {
				specialkey: function(frm,evt){
					if (evt.getKey() == evt.ENTER) {
						pecaDataStores.pdStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});			
					}
				}
			}
				
		},' '
		,{
			xtype: 'textfield'	
			,id: 'pd_first'							
			,anchor: '98%'						
			,hideLabel: true
			,emptyText: 'First Name'
			,maxLength: 30
			,autoCreate: {tag: 'input', type: 'numeric', maxlength: '30'}
			,listeners: {
				specialkey: function(frm,evt){
					if (evt.getKey() == evt.ENTER) {
						pecaDataStores.pdStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});				
					}						
					
				}
			}
				
		},'-'
		,{
			text:'Search'
			,id: 'pdSearchID'
			,tooltip:'Search payroll deduction'
			,iconCls: 'icon_ext_search'
			,scope:this
			,handler: function(btn){
				pecaDataStores.pdStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});				
			}
		},'-'
		,{
			text:'New'
			,id: 'pdNewID'
			,tooltip:'Add a payroll deduction'
			,iconCls: 'icon_ext_add'
			,scope:this
			,handler:function(btn) {
				Ext.getCmp('pdList').hide();
				Ext.getCmp('pdDetail').show();
				Ext.getCmp('pdDetail').getForm().reset();
				pdDetail().setModeNew();
				
			}
		},'-'
		,{
			text:'Delete'
			,id: 'pdDeleteID'
			,tooltip:'Delete payroll deduction'
			,iconCls: 'icon_ext_del'
			,scope:this
			,handler:function(btn) {
				var index = Ext.getCmp('pdList').getSelectionModel().getSelected();
		        if (!index) {
				showExtInfoMsg("Please select a payroll deduction to delete.");
		        	return false;
		        }
		        Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
					if(btn=='yes') {
				        Ext.Ajax.request({
				        	url: '/payroll_deduction/delete' 
							,method: 'POST'
							,params: {'newpayroll[employee_id]': index.data.employee_id
									,'newpayroll[start_date]': formatDate(index.data.start_date)
									,'newpayroll[transaction_code]': index.data.transaction_code
									,auth:_AUTH_KEY
									, 'newpayroll[modified_by]': _USER_ID}
							,waitMsg: 'Deleting Data...'
							,success: function(response, opts) {
								var obj = Ext.decode(response.responseText);
								if(obj.success){
									showExtInfoMsg(obj.msg);
									//pecaDataStores.pdStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
									if (pecaDataStores.pdStore.getCount() % MAX_PAGE_SIZE == 1){
										var page = pecaDataStores.pdStore.getTotalCount() - MAX_PAGE_SIZE - 1;
										pecaDataStores.pdStore.load({params: {start:page<0?0:page, limit:MAX_PAGE_SIZE}});
									} else{
										pecaDataStores.pdStore.reload();
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
	        ,store: pecaDataStores.pdStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};


var pd_employeeListWin = function(){
	return new Ext.Window({
		id: 'pd_employeeListWin'
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
		,items:[ pd_employeeList() ]
        ,buttons:[{
 			text: 'Cancel'
 			,iconCls: 'icon_ext_cancel'
 		    ,handler : function(btn){
 				Ext.getCmp('pd_employeeListWin').close();				
 		    }
 		}]
	});
};

var pd_employeeList = function(){
	return {
		xtype: 'grid'
		,id: 'pd_employeeList'
		,titlebar: false
		,store: pecaDataStores.pdEmployeeStore
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 425
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit:true
			,scrollOffset:13
		}
		,cm: employeeColumns
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
					Ext.getCmp('pdDetail').getForm().findField('newpayroll[employee_id]').setValue(rec.get('employee_id'));
					Ext.getCmp('pdDetail').getForm().findField('last_name').setValue(rec.get('last_name'));
					Ext.getCmp('pdDetail').getForm().findField('first_name').setValue(rec.get('first_name'));
					Ext.getCmp('pd_employeeListWin').close.defer(1,Ext.getCmp('pd_employeeListWin'));
				}
			}
		}
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.pdEmployeeStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: 'No records found'
	    })
	};
};