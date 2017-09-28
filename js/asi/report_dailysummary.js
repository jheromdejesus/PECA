var userWindow = function(id){
	return new Ext.Window({
		id: 'userWindow'
		,title: 'Users'
		,frame: true
		,layout: 'form'
		,width: 500
		,plain: true
		,modal: true
		,resizable: false
		,closable: false
		,constrainHeader: true
		,bodyStyle:{"padding":"5px"}
		,loadMask: true	
		,items:[ userList1(id) ]
        ,buttons:[{
 			text: 'Cancel'
 			,iconCls: 'icon_ext_cancel'
 		    ,handler : function(btn){
 				Ext.getCmp('userWindow').close();				
 		    }
 		}]
	});
};

var userList1 = function(id){
	return {
		xtype: 'grid'
		,id: 'userList1'
		,titlebar: false
		,store: pecaDataStores.pecaUserIsAdminStore
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 400
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit:true
			,scrollOffset:0
		}
		,cm: userListColumns1	
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
					if (id=='Search_1' || id=='Search_2') {
						Ext.getCmp('rpt_disbursement').getForm().findField('checked_by').setValue(rec.get('user_id'));
						Ext.getCmp('rpt_disbursement').getForm().findField('position').setValue(rec.get('group_id'));
					}
					if (id=='Search_7')
						Ext.getCmp('rpt_adjustment').getForm().findField('bookkeeper').setValue(rec.get('user_id'));
					else if (id=='Search_3' || id=='Search_4'){
						Ext.getCmp('rpt_adjustment').getForm().findField('checked_by').setValue(rec.get('user_id'));
						Ext.getCmp('rpt_adjustment').getForm().findField('position').setValue(rec.get('group_id'));
					}
					
					if (id=='Search_5' || id=='Search_6') {
						Ext.getCmp('rpt_collection').getForm().findField('checked_by').setValue(rec.get('user_id'));
						Ext.getCmp('rpt_collection').getForm().findField('position').setValue(rec.get('group_id'));
					}
					Ext.getCmp('userWindow').close.defer(1,Ext.getCmp('userWindow'));
				}
			}
		}
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.pecaUserIsAdminStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: 'No records found'
	    })
	};
};

var userListColumns1 =  new Ext.grid.ColumnModel( 
		[
			{id:'user_id', header: 'User ID', width: 20, sortable: true, dataIndex: 'user_id'}
			,{header: 'User Name', width: 20, sortable: true, dataIndex: 'user_name'}
			,{header: 'Group ID', width: 20, sortable: true, dataIndex: 'group_id'}
		]
);

var rpt_disbursement = function(){
	return{
		xtype:'form'
		,id:'rpt_disbursement'
		,anchor: '100%'
		,region: 'center'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,buttons:[{
			text: 'Generate'
			,iconCls: 'icon_ext_generate'
		    ,handler : function(btn){
				var frm = Ext.getCmp('rpt_disbursement').getForm();
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
		    			url: '/report_dailysummary' 
						,method: 'POST'
						,form: Ext.get('frmDownload')
						,params: {report_type: '1'
									,file_type: Ext.getCmp('rpt_disbursement').getForm().getValues().file_type
									,report_date: Ext.getCmp('rpt_disbursement').getForm().getValues().report_date
									,prepared_by: Ext.getCmp('rpt_disbursement').getForm().getValues().prepared_by
									,checked_by: Ext.getCmp('rpt_disbursement').getForm().getValues().checked_by
									,position: Ext.getCmp('rpt_disbursement').getForm().getValues().position
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
	    	,title: 'Daily Summary of Disbursement Report'
	    	,layout: 'form'
            ,anchor: '50%'
            ,items: [
				{
				    xtype: 'datefield'
				    ,fieldLabel: 'Date'
				    ,anchor: '67%'
				    ,value: _TODAY
				    ,name: 'report_date'
				    ,required: true
				    ,allowBlank: false
					,maxLength: 10
					,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
				}
				,{
	                xtype: 'textfield'
	                ,fieldLabel: 'Prepared By'
	                ,anchor: '67%'
	                ,value: _USER_ID
	                ,name: 'prepared_by'
	                ,required: true
	                ,allowBlank: false
					,maxLength: 30
					,readOnly: true
					,cls: 'x-item-disabled'
	             }
				,{
				    layout: 'column'
					,border: false
					,labelAlign: 'left'
				    ,items: [{
						layout: 'form'
						,width:250
						,border: false
						,items: [{
							xtype: 'textfield'
							,name: 'checked_by'
							,fieldLabel: 'Checked By'
							,readOnly: true
							//,value: ''
							,maxLength: 30
				            ,allowBlank: false
							,style: 'text-align: left'
							,anchor: '100%'
				            ,required: true
							,autoCreate: {tag: 'input', type: 'text', maxlength: '8'}
				    		,enableKeyEvents: true
				    		,style: 'text-align: left'
				    		/* ,listeners: {
								specialkey: function(txt,evt){
									if (evt.getKey() == evt.ENTER) {
										pecaDataStores.pecaUserIsAdminStore.load({params:{start:0, limit:MAX_PAGE_SIZE}});
										userWindow().show();					
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
							} */
						
						}]
						},{
						layout: 'form'
						,labelWidth: 1
						,border: false
						,items: [{
							xtype: 'button'
							,text: 'Search'
							,id: 'Search_1'	
							,labelSeparator: ' '
							,fieldLabel: ' '
							//,columnWidth:.2
							,width: 100
							,handler: function(){
								pecaDataStores.pecaUserIsAdminStore.load({params:{start:0, limit:MAX_PAGE_SIZE}});
								userWindow(this.id).show();
							}
						}]
					}]
				}
				,{
				    layout: 'column'
					,border: false
					,labelAlign: 'left'
				    ,items: [{
						layout: 'form'
						,width:250
						,border: false
						,items: [{
							xtype: 'textfield'
							,name: 'position'
							,fieldLabel: 'Position'
							,readOnly: true
							//,value: ''
							,maxLength: 30
				            ,allowBlank: false
							,style: 'text-align: left'
							,anchor: '100%'
				            ,required: true
							,autoCreate: {tag: 'input', type: 'text', maxlength: '8'}
				    		,enableKeyEvents: true
				    		,style: 'text-align: left'
				    		/* ,listeners: {
								specialkey: function(txt,evt){
									if (evt.getKey() == evt.ENTER) {
										pecaDataStores.pecaUserIsAdminStore.load({params:{start:0, limit:MAX_PAGE_SIZE}});
										userWindow().show();					
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
							} */
						
						}]
						}]
				}
				,{
	            	xtype: 'radiogroup'
	            	,fieldLabel: 'Report Format'
	            	,anchor: '80%'
	                ,items: [
	                    {boxLabel: 'PDF', name: 'file_type', inputValue: '2', checked: true}
	                    ,{boxLabel: 'Excel', name: 'file_type', inputValue: '1'}
	                ]
	            }
              ]
        }]
	};
};

var rpt_adjustment = function(){
	return{
		xtype:'form'
		,id:'rpt_adjustment'
		,anchor: '100%'
		,region: 'center'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,buttons:[{
			text: 'Generate'
			,iconCls: 'icon_ext_generate'
		    ,handler : function(btn){
				var frm = Ext.getCmp('rpt_adjustment').getForm();
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
		    			url: '/report_dailysummary' 
						,method: 'POST'
						,form: Ext.get('frmDownload')
						,params: {report_type: '2'
									,file_type: Ext.getCmp('rpt_adjustment').getForm().getValues().file_type
									,report_date: Ext.getCmp('rpt_adjustment').getForm().getValues().report_date
									,prepared_by: Ext.getCmp('rpt_adjustment').getForm().getValues().prepared_by
									,checked_by: Ext.getCmp('rpt_adjustment').getForm().getValues().checked_by
									,position: Ext.getCmp('rpt_adjustment').getForm().getValues().position
									,bookkeeper: Ext.getCmp('rpt_adjustment').getForm().getValues().bookkeeper
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
	    	,title: 'Daily Summary of Adjustment Report'
	    	,layout: 'form'
            ,anchor: '50%'
            ,items: [
				{
				    xtype: 'datefield'
				    ,fieldLabel: 'Date'
				    ,anchor: '67%'
				    ,value: _TODAY
				    ,name: 'report_date'
				    ,required: true
				    ,allowBlank: false
					,maxLength: 10
					,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
				}
				,{
	                xtype: 'textfield'
	                ,fieldLabel: 'Prepared By'
	                ,anchor: '67%'
	                ,value: _USER_ID
	                ,name: 'prepared_by'
	                ,required: true
	                ,allowBlank: false
					,maxLength: 30
					,readOnly: true
					,cls: 'x-item-disabled'
	             }
				,{
				    layout: 'column'
					,border: false
					,labelAlign: 'left'
				    ,items: [{
						layout: 'form'
						,width:250
						,border: false
						,items: [{
							xtype: 'textfield'
							,name: 'checked_by'
							,fieldLabel: 'Checked By'
							,readOnly: true
							//,value: ''
							,maxLength: 30
				            ,allowBlank: false
							,style: 'text-align: left'
							,anchor: '100%'
				            ,required: true
							,autoCreate: {tag: 'input', type: 'text', maxlength: '8'}
				    		,enableKeyEvents: true
				    		,style: 'text-align: left'
				    		/* ,listeners: {
								specialkey: function(txt,evt){
									if (evt.getKey() == evt.ENTER) {
										pecaDataStores.pecaUserIsAdminStore.load({params:{start:0, limit:MAX_PAGE_SIZE}});
										userWindow().show();					
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
							} */
						
						}]
						},{
						layout: 'form'
						,labelWidth: 1
						,border: false
						,items: [{
							xtype: 'button'
							,text: 'Search'
							,id: 'Search_3'	
							,labelSeparator: ' '
							,fieldLabel: ' '
							//,columnWidth:.2
							,width: 100
							,handler: function(){
								pecaDataStores.pecaUserIsAdminStore.load({params:{start:0, limit:MAX_PAGE_SIZE}});
								userWindow(this.id).show();
							}
						}]
						}]
				}
				,{
				    layout: 'column'
					,border: false
					,labelAlign: 'left'
				    ,items: [{
						layout: 'form'
						,width:250
						,border: false
						,items: [{
							xtype: 'textfield'
							,name: 'position'
							,fieldLabel: 'Position'
							,readOnly: true
							,maxLength: 30
				            ,allowBlank: false
							,style: 'text-align: left'
							,anchor: '100%'
				            ,required: true
							,autoCreate: {tag: 'input', type: 'text', maxlength: '8'}
				    		,enableKeyEvents: true
				    		,style: 'text-align: left'
				    		/* ,listeners: {
								specialkey: function(txt,evt){
									if (evt.getKey() == evt.ENTER) {
										pecaDataStores.pecaUserIsAdminStore.load({params:{start:0, limit:MAX_PAGE_SIZE}});
										userWindow().show();					
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
							} */
						
						}]
						}]
				}
				,{
				    layout: 'column'
					,border: false
					,labelAlign: 'left'
				    ,items: [{
						layout: 'form'
						,width:250
						,border: false
						,items: [{
							xtype: 'textfield'
							,name: 'bookkeeper'
							,fieldLabel: 'Bookkeeper'
							,emptyText: 'Type ALL or the User ID'
							,maxLength: 10
				            ,allowBlank: false
							,style: 'text-align: left'
							,anchor: '100%'
				            ,required: true
				    		,enableKeyEvents: true
				    		,style: 'text-align: left'
				    		/* ,listeners: {
								specialkey: function(txt,evt){
									if (evt.getKey() == evt.ENTER) {
										pecaDataStores.pecaUserIsAdminStore.load({params:{start:0, limit:MAX_PAGE_SIZE}});
										userWindow().show();					
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
							} */
						
						}]
						},{
						layout: 'form'
						,labelWidth: 1
						,border: false
						,items: [{
							xtype: 'button'
							,text: 'Search'
							,id: 'Search_7'	
							,labelSeparator: ' '
							,fieldLabel: ' '
							//,columnWidth:.2
							,width: 100
							,handler: function(){
								pecaDataStores.pecaUserIsAdminStore.load({params:{start:0, limit:MAX_PAGE_SIZE}});
								userWindow(this.id).show();
							}
						}]
					}]
				}
				,{
	            	xtype: 'radiogroup'
	            	,fieldLabel: 'Report Format'
	            	,anchor: '80%'
	                ,items: [
	                    {boxLabel: 'PDF', name: 'file_type', inputValue: '2', checked: true}
	                    ,{boxLabel: 'Excel', name: 'file_type', inputValue: '1'}
	                ]
	            }
              ]
        }]
	};
};

var rpt_collection = function(){
	return{
		xtype:'form'
		,id:'rpt_collection'
		,anchor: '100%'
		,region: 'center'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,buttons:[{
			text: 'Generate'
			,iconCls: 'icon_ext_generate'
		    ,handler : function(btn){
				var frm = Ext.getCmp('rpt_collection').getForm();
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
		    			url: '/report_dailysummary' 
						,method: 'POST'
						,form: Ext.get('frmDownload')
						,params: {report_type: '3'
									,file_type: Ext.getCmp('rpt_collection').getForm().getValues().file_type
									,report_date: Ext.getCmp('rpt_collection').getForm().getValues().report_date
									,prepared_by: Ext.getCmp('rpt_collection').getForm().getValues().prepared_by
									,checked_by: Ext.getCmp('rpt_collection').getForm().getValues().checked_by
									,position: Ext.getCmp('rpt_collection').getForm().getValues().position
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
	    	,title: 'Daily Summary of Collections Report'
	    	,layout: 'form'
            ,anchor: '50%'
            ,items: [
				{
				    xtype: 'datefield'
				    ,fieldLabel: 'Date'
				    ,anchor: '67%'
				    ,value: _TODAY
				    ,name: 'report_date'
				    ,required: true
				    ,allowBlank: false
					,maxLength: 10
					,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
				}
				,{
	                xtype: 'textfield'
	                ,fieldLabel: 'Prepared By'
	                ,anchor: '67%'
	                ,value: _USER_ID
	                ,name: 'prepared_by'
	                ,required: true
	                ,allowBlank: false
					,maxLength: 30
					,readOnly: true
					,cls: 'x-item-disabled'
	             }
				,{
				    layout: 'column'
					,border: false
					,labelAlign: 'left'
				    ,items: [{
						layout: 'form'
						,width:250
						,border: false
						,items: [{
							xtype: 'textfield'
							,name: 'checked_by'
							,fieldLabel: 'Checked By'
							,readOnly: true
							//,value: ''
							,maxLength: 30
				            ,allowBlank: false
							,style: 'text-align: left'
							,anchor: '100%'
				            ,required: true
							,autoCreate: {tag: 'input', type: 'text', maxlength: '8'}
				    		,enableKeyEvents: true
				    		,style: 'text-align: left'
				    		/* ,listeners: {
								specialkey: function(txt,evt){
									if (evt.getKey() == evt.ENTER) {
										pecaDataStores.pecaUserIsAdminStore.load({params:{start:0, limit:MAX_PAGE_SIZE}});
										userWindow().show();					
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
							} */
						
						}]
						},{
						layout: 'form'
						,labelWidth: 1
						,border: false
						,items: [{
							xtype: 'button'
							,text: 'Search'
							,id: 'Search_5'	
							,labelSeparator: ' '
							,fieldLabel: ' '
							//,columnWidth:.2
							,width: 100
							,handler: function(){
								pecaDataStores.pecaUserIsAdminStore.load({params:{start:0, limit:MAX_PAGE_SIZE}});
								userWindow(this.id).show();
							}
						}]
						}]
				}
				,{
				    layout: 'column'
					,border: false
					,labelAlign: 'left'
				    ,items: [{
						layout: 'form'
						,width:250
						,border: false
						,items: [{
							xtype: 'textfield'
							,name: 'position'
							,fieldLabel: 'Position'
							,readOnly: true
							//,value: ''
							,maxLength: 30
				            ,allowBlank: false
							,style: 'text-align: left'
							,anchor: '100%'
				            ,required: true
							,autoCreate: {tag: 'input', type: 'text', maxlength: '8'}
				    		,enableKeyEvents: true
				    		,style: 'text-align: left'
				    		/* ,listeners: {
								specialkey: function(txt,evt){
									if (evt.getKey() == evt.ENTER) {
										pecaDataStores.pecaUserIsAdminStore.load({params:{start:0, limit:MAX_PAGE_SIZE}});
										userWindow().show();					
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
							} */
						
						}]
						}]
				}
				,{
	            	xtype: 'radiogroup'
	            	,fieldLabel: 'Report Format'
	            	,anchor: '80%'
	                ,items: [
	                    {boxLabel: 'PDF', name: 'file_type', inputValue: '2', checked: true}
	                    ,{boxLabel: 'Excel', name: 'file_type', inputValue: '1'}
	                ]
	            }
              ]
        }]
	};
};