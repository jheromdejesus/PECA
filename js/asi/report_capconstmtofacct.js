var rpt_csoa = function(){
	return{
		xtype:'form'
		,id:'rpt_csoa'
		,region: 'center'
		,anchor: '100%'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,buttons:[{
			text: 'Generate'
			,iconCls: 'icon_ext_generate'
		    ,handler : function(btn){
				var frm = Ext.getCmp('rpt_csoa').getForm();
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
		    			url: '/report_capconstatementofacct' 
						,method: 'POST'
						,form: Ext.get('frmDownload')
						,params: {'file_type': Ext.getCmp('rpt_csoa').getForm().getValues().file_type
									,'start_date': Ext.getCmp('rpt_csoa').getForm().getValues().from_date
									,'end_date': Ext.getCmp('rpt_csoa').getForm().getValues().to_date
									,'start_trans_date': Ext.getCmp('rpt_csoa').getForm().getValues().from_trans_date? Ext.getCmp('rpt_csoa').getForm().getValues().from_trans_date:''
									,'end_trans_date': Ext.getCmp('rpt_csoa').getForm().getValues().to_trans_date? Ext.getCmp('rpt_csoa').getForm().getValues().to_trans_date:''
									,'company_code': Ext.getCmp('rpt_csoa').getForm().findField('company_code').getValue()? Ext.getCmp('rpt_csoa').getForm().findField('company_code').getValue():''
									//,'employee_id': Ext.getCmp('rpt_csoa').getForm().findField('employee_id').getValue()
									,'employee_id': Ext.getCmp('rpt_csoa').getForm().findField('employee_id').getValue()? Ext.getCmp('rpt_csoa').getForm().findField('employee_id').getValue():''
									,'report_type': Ext.getCmp('rpt_csoa').getForm().getValues().report_type
									,auth:_AUTH_KEY}
						,isUpload: true
						,success: function(response, opts) {
							var obj = Ext.decode(response.responseText);
							if(obj.success){
								App.setAlert(true, obj.msg);
							}else{
								showExtInfoMsg(obj.msg);
							}
						}
						,failure: function(response, opts) {
							if (opts.result.error_code == 19){
								showExtInfoMsg(opts.result.msg);
							}
						}
		        	});
		    	}
		    }
		}]
	    ,items: [{
	    	xtype: 'fieldset'
	    	,title: 'Statement of Account'
	    	,layout: 'form'
            ,anchor: '100%'
            ,items: [{
				layout: 'column'
				,width: 800
				,border: false
				,items: [{
						layout: 'form'
						,labelAlign: 'left'
						,border: false
						,hideBorders: false
						,labelWidth: 130
						,columnWidth: 0.35
						,items: [{
								xtype: 'datefield'
								,fieldLabel: 'Date'
								,maxLength: 10
								,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
								,style: 'text-align: right'
								,validationEvent: 'change'
								,name: 'from_date'
								,required: true
								,allowBlank: false
								//,invalidText: 'This is not a valid date - it must be in the format MM/DD/YYYY'
				                ,validator: function(value1){
				    	            	var frm = Ext.getCmp('rpt_csoa').getForm();
				    	        		var value2 = frm.findField('to_date').value;
				    	        		
										if (value1=='' || value1==null){
											return 'This field is required.';
										}
										
				    	        		if ((value1 != null && value1 != '') && (value2 != null && value2 != '')){
											first = new Date(value1);
						                    second = new Date(value2);
												
												if (first.format('m/d/Y')=="NaN/NaN/NaN"){
													return 'This is not a valid date - it must be in the format MM/DD/YYYY';
												}else {
													if (first <= second){
														return true;
													} else{
														return 'The date should be lesser than or equal to date to.';
													}
												}	
				    	        		}else{
				    	        			return true;
				    	        		}
				    	        	}
							}]
					},{
						layout: 'form'
						,labelAlign: 'left'
						,border: false
						,hideBorders: false
						,labelWidth: 10
						,columnWidth: 0.4
						,items: [{
								xtype: 'datefield'
								,maxLength: 10
								,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
								,style: 'text-align: right'
								,validationEvent: 'change'
								,name: 'to_date'
								,required: true
								,allowBlank: false
								//,invalidText: 'This is not a valid date - it must be in the format MM/DD/YYYY'
				                ,validator: function(value2){
				    	            	var frm = Ext.getCmp('rpt_csoa').getForm();
				    	        		var value1 = frm.findField('from_date').value;
				    	        		
										if (value2=='' || value2==null){
											return 'This field is required.';
										}
										
				    	        		if ((value1 != null && value1 != '') && (value2 != null && value2 != '')){
											first = new Date(value1);
						                    second = new Date(value2);
											
											if (second.format('m/d/Y')=="NaN/NaN/NaN"){
													return 'This is not a valid date - it must be in the format MM/DD/YYYY';
											}else {
												if (first <= second){
													return true;				    	            			
												} else{
													return 'The date should be greater than or equal to date from.';
												}
											}												
				    	        		}else{
				    	        			return true;
				    	        		}
				    	    		
				    	        	}
						}]
					}]
			},{
				layout: 'column'
				,anchor: '100%'
				,border: false
				,items: [{
					xtype:'radio'
					, boxLabel: ''
					, name: 'report_type'
					, id: 'report_type_1'
					, inputValue: '1'
					//, checked: true
					, listeners: {
						check: function( radio, checked){
							if(checked){
								Ext.getCmp('rpt_csoa').getForm().findField('employee_id').setDisabled(false);
								Ext.getCmp('rpt_csoa').getForm().findField('last_name').setDisabled(false);
								Ext.getCmp('rpt_csoa').getForm().findField('first_name').setDisabled(false);
								Ext.getCmp('rpt_csoa').getForm().findField('middle_name').setDisabled(false);
								Ext.getCmp('search_report_statement').setDisabled(false);
								
								Ext.getCmp('rpt_csoa').getForm().findField('company_code').setDisabled(true);								
								Ext.getCmp('rpt_csoa').getForm().findField('from_trans_date').setDisabled(true);
								Ext.getCmp('rpt_csoa').getForm().findField('to_trans_date').setDisabled(true);
								
								//manually set other radio button due to not using group
								Ext.getCmp('report_type_2').setValue(false);
								Ext.getCmp('report_type_3').setValue(false);
							}
							else {
								Ext.getCmp('rpt_csoa').getForm().findField('employee_id').setValue("");
								Ext.getCmp('rpt_csoa').getForm().findField('employee_id').clearInvalid();
								Ext.getCmp('rpt_csoa').getForm().findField('company_code').setValue("");
								Ext.getCmp('rpt_csoa').getForm().findField('company_code').clearInvalid();
								Ext.getCmp('rpt_csoa').getForm().findField('last_name').setValue("");
								Ext.getCmp('rpt_csoa').getForm().findField('first_name').setValue("");
								Ext.getCmp('rpt_csoa').getForm().findField('middle_name').setValue("");
								Ext.getCmp('rpt_csoa').getForm().findField('from_trans_date').setValue("");
								Ext.getCmp('rpt_csoa').getForm().findField('to_trans_date').setValue("");
								Ext.getCmp('rpt_csoa').getForm().findField('from_trans_date').clearInvalid();
								Ext.getCmp('rpt_csoa').getForm().findField('to_trans_date').clearInvalid(); 
							} 
						}
					}
				},{
					layout: 'form'
					,border: false
					,labelWidth: 1
					,columnWidth: 0.16
					,items: [{
						xtype:'label'
						, text: 'Employee'
						, fieldLabel: ' '
						, labelSeparator: ' '
					}]				
				},{
					layout: 'form'
					,labelWidth: 1
					,border: false
					,columnWidth: 0.18
					,items: [{
						xtype: 'textfield'
						,name: 'employee_id'	
						,anchor:'100%'
						,fieldLabel: ''
						,labelSeparator: ' '
						,emptyText: 'ID'
						,autoCreate: {tag: 'input', type: 'text', maxlength: '8'}
						,enableKeyEvents: true
						,style: 'text-align: right'
						,allowBlank: false
						,listeners: {
							specialkey: function(txt,evt){
								if (evt.getKey() == evt.ENTER) {
									pecaDataStores.capconStmtOfAcctReportEmployeeStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
										repStatement_employeeListWin().show();					
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
					}]
				}, {
					layout: 'form'
					,labelWidth: 1
					,border: false
					,columnWidth: 0.18
					,items: [{
						xtype: 'textfield'
						,name: 'last_name'	
						,anchor:'100%'
						,fieldLabel: ''
						,submitValue: false
						,labelSeparator: ' '
						,emptyText: 'Last Name'
						,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
						,enableKeyEvents: true
						,listeners: {
							specialkey: function(txt,evt){
								if (evt.getKey() == evt.ENTER) {
									pecaDataStores.capconStmtOfAcctReportEmployeeStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
									repStatement_employeeListWin().show();					
								}
							}
						}
					}]
				},{
					layout: 'form'
					,labelWidth: 1
					,border: false
					,columnWidth: 0.18
					,items: [{
						xtype: 'textfield'
						,name: 'first_name'	
						,submitValue: false
						,anchor:'100%'
						,fieldLabel: ''
						,labelSeparator: ' '
						,emptyText: 'First Name'
						,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
						,enableKeyEvents: true
						,listeners: {
							specialkey: function(txt,evt){
								if (evt.getKey() == evt.ENTER) {
									pecaDataStores.capconStmtOfAcctReportEmployeeStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
									repStatement_employeeListWin().show();					
								}
							}
						}
					}]
				},{
					layout: 'form'
					,labelWidth: 1
					,columnWidth: 0.18
					,border: false
					,items: [{
						xtype: 'textfield'
						,name: 'middle_name'	
						,submitValue: false
						,anchor: '100%'
						,fieldLabel: ''
						,labelSeparator: ' '
						,emptyText: 'Middle Name'
						,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
						,enableKeyEvents: true
						,listeners: {
							specialkey: function(txt,evt){
								if (evt.getKey() == evt.ENTER) {
									pecaDataStores.capconStmtOfAcctReportEmployeeStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
									repStatement_employeeListWin().show();					
								}
							}
						}
					}]
				},{
					//layout: 'form'
					//,labelWidth: 10
					//,border: false
					//,items: [{
						xtype: 'button'
						,text: 'Search'
						,id: 'search_report_statement'
						,iconCls: 'icon_ext_search' 
						,labelSeparator: ' '
						,fieldLabel: ''
						,handler: function(){
							pecaDataStores.capconStmtOfAcctReportEmployeeStore.load({params: {
								employee_id: Ext.getCmp('rpt_csoa').getForm().findField('employee_id').getValue()
								,first_name: Ext.getCmp('rpt_csoa').getForm().findField('first_name').getValue()
								,last_name: Ext.getCmp('rpt_csoa').getForm().findField('last_name').getValue()
								,middle_name: Ext.getCmp('rpt_csoa').getForm().findField('middle_name').getValue()
								,start:0, limit:MAX_PAGE_SIZE}});
								repStatement_employeeListWin().show();
						}
					//}]
					}		
				]
			},{
				layout: 'column'
				,border: false
				,items: [{
					xtype:'radio'
					, boxLabel: ''
					, name: 'report_type'
					, id: 'report_type_2'
					, inputValue: '2'
					, listeners: {
						check: function( radio, checked){
							if(checked){
								Ext.getCmp('rpt_csoa').getForm().findField('employee_id').setDisabled(true);
								Ext.getCmp('rpt_csoa').getForm().findField('last_name').setDisabled(true);
								Ext.getCmp('rpt_csoa').getForm().findField('first_name').setDisabled(true);
								Ext.getCmp('rpt_csoa').getForm().findField('middle_name').setDisabled(true);
								Ext.getCmp('search_report_statement').setDisabled(true);
								
								Ext.getCmp('rpt_csoa').getForm().findField('company_code').setDisabled(false);
								
								Ext.getCmp('rpt_csoa').getForm().findField('from_trans_date').setDisabled(true);
								Ext.getCmp('rpt_csoa').getForm().findField('to_trans_date').setDisabled(true);
							}
							else {
								Ext.getCmp('rpt_csoa').getForm().findField('employee_id').setValue("");
								Ext.getCmp('rpt_csoa').getForm().findField('employee_id').clearInvalid();
								Ext.getCmp('rpt_csoa').getForm().findField('company_code').setValue("");
								Ext.getCmp('rpt_csoa').getForm().findField('company_code').clearInvalid();
								Ext.getCmp('rpt_csoa').getForm().findField('last_name').setValue("");
								Ext.getCmp('rpt_csoa').getForm().findField('first_name').setValue("");
								Ext.getCmp('rpt_csoa').getForm().findField('middle_name').setValue("");
								Ext.getCmp('rpt_csoa').getForm().findField('from_trans_date').setValue("");
								Ext.getCmp('rpt_csoa').getForm().findField('to_trans_date').setValue("");
								Ext.getCmp('rpt_csoa').getForm().findField('from_trans_date').clearInvalid();
								Ext.getCmp('rpt_csoa').getForm().findField('to_trans_date').clearInvalid(); 
							} 
						}
					}
				},{
					layout: 'form'
					,border: false
					,columnWidth: 0.15
					,labelWidth: 1
					,items: [{
						xtype:'label'
						, text: 'Company'
						, fieldLabel: ' '
						, labelSeparator: ' '
					}]				
				},{
					layout: 'form'
					,border: false
					,columnWidth: 0.25
					,labelWidth: 1
					,items: [{
						xtype: 'combo'
						,hiddenName: 'company_code'
						,store: pecaDataStores.companyStoreSOA
						,mode: 'local'
						, fieldLabel: ''
						, labelSeparator: ''
						,allowBlank: false
						,displayField: 'company_name'
						,valueField: 'company_code'
						,editable: 'false'
						,emptyText: 'Please Select'
						,forceSelection: true
						,triggerAction: 'all'
						,selectOnFocus: true
						,editable: false
				}]
				}]
			},{
				layout: 'column'
				,border: false
				,items: [{
						xtype:'radio'
						, boxLabel: ''
						, name: 'report_type'
						, id: 'report_type_3'
						, inputValue: '3'
						, listeners: {
							check: function( radio, checked){
								if(checked){
									Ext.getCmp('rpt_csoa').getForm().findField('employee_id').setDisabled(true);
									Ext.getCmp('rpt_csoa').getForm().findField('last_name').setDisabled(true);
									Ext.getCmp('rpt_csoa').getForm().findField('first_name').setDisabled(true);
									Ext.getCmp('rpt_csoa').getForm().findField('middle_name').setDisabled(true);
									Ext.getCmp('search_report_statement').setDisabled(true);
									
									Ext.getCmp('rpt_csoa').getForm().findField('company_code').setDisabled(true);									
									Ext.getCmp('rpt_csoa').getForm().findField('from_trans_date').setDisabled(false);
									Ext.getCmp('rpt_csoa').getForm().findField('to_trans_date').setDisabled(false);
								}
								else {
									Ext.getCmp('rpt_csoa').getForm().findField('employee_id').setValue("");
									Ext.getCmp('rpt_csoa').getForm().findField('employee_id').clearInvalid();
									Ext.getCmp('rpt_csoa').getForm().findField('company_code').setValue("");
									Ext.getCmp('rpt_csoa').getForm().findField('company_code').clearInvalid();
									Ext.getCmp('rpt_csoa').getForm().findField('last_name').setValue("");
									Ext.getCmp('rpt_csoa').getForm().findField('first_name').setValue("");
									Ext.getCmp('rpt_csoa').getForm().findField('middle_name').setValue("");
									Ext.getCmp('rpt_csoa').getForm().findField('from_trans_date').setValue("");
									Ext.getCmp('rpt_csoa').getForm().findField('to_trans_date').setValue("");
									Ext.getCmp('rpt_csoa').getForm().findField('from_trans_date').clearInvalid();
									Ext.getCmp('rpt_csoa').getForm().findField('to_trans_date').clearInvalid();
								} 
							}
					}
					},{
						layout: 'form'
						,border: false
						,columnWidth: 0.15
						,labelWidth: 1
						,items: [{
							xtype:'label'
							, text: 'Transaction Date'
							, fieldLabel: ' '
							, labelSeparator: ' '
						}]		
					},{
					layout: 'form'
					,labelWidth: 1
					,border: false
					,columnWidth: 0.22
					,items: [{
							xtype: 'datefield'
							,maxLength: 10
							,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
							,style: 'text-align: right'
							,validationEvent: 'change'
							,allowBlank: false
							,name: 'from_trans_date'
							,allowBlank: false
							,validator: function(value1){
				    	            	var frm = Ext.getCmp('rpt_csoa').getForm();
				    	        		var value2 = frm.findField('to_trans_date').value;
				    	        		
										if (value1=='' || value1==null){
											return 'This field is required.';
										}
										
				    	        		if ((value1 != null && value1 != '') && (value2 != null && value2 != '')){
											first = new Date(value1);
						                    second = new Date(value2);
												
												if (first.format('m/d/Y')=="NaN/NaN/NaN"){
													return 'This is not a valid date - it must be in the format MM/DD/YYYY';
												}else {
													if (first <= second){
														return true;
													} else{
														return 'The date should be lesser than or equal to date to.';
													}
												}	
				    	        		}else{
				    	        			return true;
				    	        		}
				    	      }
					}]
					},{
					layout: 'form'
					,labelWidth: 10
					,border: false
					,columnWidth: 0.22
					,items: [{
							xtype: 'datefield'
							,maxLength: 10
							,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
							,style: 'text-align: right'
							,validationEvent: 'change'
							,allowBlank: false
							,name: 'to_trans_date'
							,allowBlank: false
							,validator: function(value2){
				    	            	var frm = Ext.getCmp('rpt_csoa').getForm();
				    	        		var value1 = frm.findField('from_trans_date').value;
				    	        		
										if (value2=='' || value2==null){
											return 'This field is required.';
										}
										
				    	        		if ((value1 != null && value1 != '') && (value2 != null && value2 != '')){
											first = new Date(value1);
						                    second = new Date(value2);
											
											if (second.format('m/d/Y')=="NaN/NaN/NaN"){
													return 'This is not a valid date - it must be in the format MM/DD/YYYY';
											}else {
												if (first <= second){
													return true;				    	            			
												} else{
													return 'The date should be greater than or equal to date from.';
												}
											}												
				    	        		}else{
				    	        			return true;
				    	        		}
							}
					}]
				}
			]},{
            	layout: 'form'
				,labelAlign: 'left'
				,border: false
				,hideBorders: false
				,labelWidth: 130
				,items: [{
					xtype: 'radiogroup'
					,fieldLabel: 'Report Format'
					,anchor: '45%'
					,items: [
						{boxLabel: 'PDF', name: 'file_type', inputValue: '2', checked: true}
						,{boxLabel: 'Excel', name: 'file_type', inputValue: '1'}
					]
				}]
            }
			]
        }]
	};
};

var repStatement_employeeListWin = function(){
	return new Ext.Window({
		id: 'repStatement_employeeListWin'
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
		,items:[ repStatement_employeeList() ]
        ,buttons:[{
 			text: 'Cancel'
 			,iconCls: 'icon_ext_cancel'
 		    ,handler : function(btn){
 				Ext.getCmp('repStatement_employeeListWin').close();				
 		    }
 		}]
	});
};

var repStatement_EmployeeColumns =  new Ext.grid.ColumnModel( 
	[
		{id:'employeeList_id', header: 'Employee ID', width: 100, sortable: true, dataIndex: 'employee_id'}
		,{header: 'Last Name', width: 100, sortable: true, dataIndex: 'last_name'}
		,{header: 'First Name', width: 100, sortable: true, dataIndex: 'first_name'}
	]
);

var repStatement_employeeList = function(){
	return {
		xtype: 'grid'
		,id: 'repStatement_employeeList'
		,titlebar: false
		,store: pecaDataStores.capconStmtOfAcctReportEmployeeStore
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
		,cm: repStatement_EmployeeColumns
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
					Ext.getCmp('rpt_csoa').getForm().findField('employee_id').setValue(rec.get('employee_id'));
					Ext.getCmp('rpt_csoa').getForm().findField('last_name').setValue(rec.get('last_name'));
					Ext.getCmp('rpt_csoa').getForm().findField('first_name').setValue(rec.get('first_name'));
					Ext.getCmp('rpt_csoa').getForm().findField('middle_name').setValue(rec.get('middle_name'));
					Ext.getCmp('repStatement_employeeListWin').close.defer(1,Ext.getCmp('repStatement_employeeListWin'));
				}
			}
		}
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.capconStmtOfAcctReportEmployeeStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: 'No records found'
	    })
	};
};