//Let's pretend we rendered our grid-columns with meta-data from our ORM framework.
var isopColumns =  new Ext.grid.ColumnModel( 
	[
     {id: 'employee_id', header: 'Employee ID', sortable: true, align: 'right', width: 100, dataIndex: 'isop[employee_id]', bodyStyle: 'text-align: right;'}
     ,{header: 'Last Name', sortable: true, width: 200, dataIndex: 'isop[last_name]'}
     ,{header: 'First Name', sortable: true, width: 200, dataIndex: 'isop[first_name]'}
     ,{header: 'Start Date', sortable: true, width: 150, align: 'center;', dataIndex: 'isop[start_date]'}
     ,{header: 'End Date', sortable: true, width: 150, align: 'center', dataIndex: 'isop[end_date]'}
     ,{header: 'Amount', sortable: true, width: 100, align: 'right', dataIndex: 'isop[amount]', renderer: function(value, rec){
		    	return Ext.util.Format.number(value,'0,000,000,000.00');}}
     ,{header: ' ', sortable: true, width: 40, renderer: function(){
	    	return ' ';}}
	]
);

var isopEmployeeColumns =  new Ext.grid.ColumnModel( 
	[
		{id:'employeeList_id', header: 'Employee ID', width: 100, sortable: true, dataIndex: 'employee_id'}
		,{header: 'Last Name', width: 100, sortable: true, dataIndex: 'last_name'}
		,{header: 'First Name', width: 100, sortable: true, dataIndex: 'first_name'}
	]
);

var isopDetail = function(){
	return {
		xtype:'form'
		,id:'isopDetail'
		,region:'center'
		,title: 'Details'
		,hidden:true
		,autoscroll: true
		,anchor: '100%'
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,reader: pecaReaders.isopReader
		,buttons:[{
			text: 'Cancel'
			,iconCls: 'icon_ext_cancel'
		    ,handler : function(btn){
				Ext.getCmp('isopDetail').hide();
				Ext.getCmp('isopList').show();
				Ext.getCmp('isopDetail').getForm().reset();
				pecaDataStores.isopStore.reload();
		    }
		},{
			text: 'Delete'
			,iconCls: 'icon_ext_del'
			,handler: function(){
				var frm = Ext.getCmp('isopDetail').getForm();
				frm.onDelete();
		    }
		},{
			text:'Save'
			,iconCls: 'icon_ext_save'
		    ,handler: function(){
		    	var frm = Ext.getCmp('isopDetail').getForm();
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
			    xtype: 'hidden'
				,name: 'isop[transaction_no]'
			},{
			    xtype: 'hidden'
				,name: 'isop[old_start_date]'
			},{
			    xtype: 'hidden'
				,name: 'isop[old_end_date]'
			},{
				items: [{
	            layout: 'column'
            	,border: false
				,labelAlign: 'left'
	            ,items: [{
					layout: 'form'
					,width:200
					,border: false
					,items: [{
						xtype: 'textfield'
						,name: 'isop[employee_id]'
						,fieldLabel: 'Employee'
		                ,allowBlank: false
						,style: 'text-align: right'
						,anchor: '98%'
		                ,required: true
						,emptyText: 'ID'
						,autoCreate: {tag: 'input', type: 'text', maxlength: '8'}
			    		,enableKeyEvents: true
			    		,style: 'text-align: right'
			    			,listeners: {
							specialkey: function(txt,evt){
								if (evt.getKey() == evt.ENTER) {
									pecaDataStores.isopEmployeeStore.load({params: {
										employee_id: Ext.getCmp('isopDetail').getForm().findField('isop[employee_id]').getValue()
										,first_name: Ext.getCmp('isopDetail').getForm().findField('isop[first_name]').getValue()
										,last_name: Ext.getCmp('isopDetail').getForm().findField('isop[last_name]').getValue()
										,start:0, limit:MAX_PAGE_SIZE}});
										isop_employeeListWin().show();					
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
					},{
					layout: 'form'
					,labelWidth: 1
					,border: false
					,width: 180
					,items: [{
						xtype: 'textfield'
						,name: 'isop[last_name]'	
						,fieldLabel: ''
						,submitValue: false
						,anchor: '98%'
						,labelSeparator: ''
						//,columnWidth:.2
						,emptyText: 'Last Name'
						,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
					}]
					},{
					layout: 'form'
					,labelWidth: 1
					,border: false
					,width: 180
					,items: [{
						xtype: 'textfield'
						,name: 'isop[first_name]'	
						,submitValue: false
						,fieldLabel: ''
						,labelSeparator: ''
						,anchor: '98%'
						//,columnWidth:.2
						,emptyText: 'First Name'
						,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
					}]
					},{
					layout: 'form'
					,labelWidth: 1
					,border: false
					,items: [{
						xtype: 'button'
						,text: 'Search'
						,id: 'Search'	
						,width:75
						,labelSeparator: ''
						,fieldLabel: ''
						,iconCls: 'icon_ext_search'	
						//,columnWidth:.2
						,handler: function(){
							pecaDataStores.isopEmployeeStore.load({params: {
								employee_id: Ext.getCmp('isopDetail').getForm().findField('isop[employee_id]').getValue()
								,first_name: Ext.getCmp('isopDetail').getForm().findField('isop[first_name]').getValue()
								,last_name: Ext.getCmp('isopDetail').getForm().findField('isop[last_name]').getValue()
								,start:0, limit:MAX_PAGE_SIZE}});
								isop_employeeListWin().show();
						}
					}]
					}]
        },{
			labelWidth: 100
			,labelAlign: 'left'
			,layout: 'form'
			,border: false
			,items: [{
				xtype: 'datefield'
				,name: 'isop[start_date]'
				,allowBlank: false
		        ,required: true
				,style: 'text-align: right'
				,fieldLabel: 'Start Date'
				,width: 200
				,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
			},{
				xtype: 'datefield'
				,name: 'isop[end_date]'
				,allowBlank: false
				,required: true
				,style: 'text-align: right'
				,fieldLabel: 'End Date'
				,width: 200
				,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
			},{
				xtype: 'moneyfield'
				,name: 'isop[amount]'
				,allowBlank: false
				,required: true
				,fieldLabel: 'Amount'
				,style: 'text-align: right'
				,width: 200
				,maxLength: 16
				,autoCreate: {tag: 'input', type: 'text', maxlength: '16'}
	    	}]
		}]
		}]
	    ,isModeNew: function() {
	    	return (Ext.getCmp('isopDetail').getForm().findField('frm_mode').getValue() == FORM_MODE_NEW);
	    }
	    ,setModeNew: function() {
	    	Ext.getCmp('isopDetail').getForm().findField('frm_mode').setValue(FORM_MODE_NEW);
	    	Ext.getCmp('isopDetail').buttons[0].setVisible(true);  //cancel button
	    	Ext.getCmp('isopDetail').buttons[1].setVisible(false);  //delete button
	    	Ext.getCmp('isopDetail').buttons[2].setVisible(true);  //save button
	    	//Ext.getCmp('isopDetail').getForm().findField('isop[employee_id]').focus('',250);
			Ext.getCmp('isopDetail').getForm().findField('isop[employee_id]').setReadOnly(false);
			Ext.getCmp('isopDetail').getForm().findField('isop[employee_id]').removeClass('x-item-disabled');
			Ext.getCmp('isopDetail').getForm().findField('isop[last_name]').setReadOnly(false);
			Ext.getCmp('isopDetail').getForm().findField('isop[last_name]').removeClass('x-item-disabled');
			Ext.getCmp('isopDetail').getForm().findField('isop[first_name]').setReadOnly(false);
			Ext.getCmp('isopDetail').getForm().findField('isop[first_name]').removeClass('x-item-disabled');
			Ext.getCmp('isopDetail').getForm().findField('isop[amount]').setValue('0');
			Ext.getCmp('Search').setVisible(true);  //search button
		}
		,setModeUpdate: function() {
			Ext.getCmp('isopDetail').getForm().findField('frm_mode').setValue(FORM_MODE_UPDATE);
			Ext.getCmp('isopDetail').buttons[0].setVisible(true);  //cancel button
	    	Ext.getCmp('isopDetail').buttons[1].setVisible(true);  //delete button
	    	Ext.getCmp('isopDetail').buttons[2].setVisible(true);  //save button
	    	
	    	//can't update record
			if(_PERMISSION[129]==0){
				Ext.getCmp('isopDetail').buttons[2].setDisabled(true);	
			}
			//can't delete record
			if(_PERMISSION[33]==0){
				Ext.getCmp('isopDetail').buttons[1].setDisabled(true);	
			}
			
	    	Ext.getCmp('isopDetail').getForm().findField('isop[start_date]').focus('',250);
			Ext.getCmp('isopDetail').getForm().findField('isop[employee_id]').setReadOnly(true);
			Ext.getCmp('isopDetail').getForm().findField('isop[employee_id]').addClass('x-item-disabled');
			Ext.getCmp('isopDetail').getForm().findField('isop[last_name]').setReadOnly(true);
			Ext.getCmp('isopDetail').getForm().findField('isop[last_name]').addClass('x-item-disabled');
			Ext.getCmp('isopDetail').getForm().findField('isop[first_name]').setReadOnly(true);
			Ext.getCmp('isopDetail').getForm().findField('isop[first_name]').addClass('x-item-disabled');
			Ext.getCmp('Search').setVisible(false);  //search button
	    }
		,onSave: function(frm){
			frm.submit({
    			url: '/isop/add' 
    			,method: 'POST'
    			,params: {auth:_AUTH_KEY, 'isop[created_by]': _USER_ID}
    			,waitMsg: 'Creating Data...'
    			,success: function(form, action) {
    				showExtInfoMsg( action.result.msg);
    				frm.setModeUpdate();
					Ext.getCmp('isopDetail').getForm().load({
				    	url: '/isop/show'
				    	,params: {'isop[transaction_no]':(action.result.transaction_no)
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
					else if(action.result.error_code == 43){
    					Ext.Msg.confirm('Confirm Action',action.result.msg,function(btn) {
	    					if(btn=='yes'){
	    						form.onAdjust(form, 'update');
	    					}
	    				});
    				}else{
						showExtErrorMsg(action.result.msg);	
    				}
    			}	
    		});
		}
		,onUpdate: function(frm){
			frm.submit({
    			url: '/isop/update' 
    			,method: 'POST'
    			,waitMsg: 'Updating Data...'
    			,params: { auth:_AUTH_KEY, 'isop[modified_by]': _USER_ID	
    			}
    			,success: function(form, action) {
    				showExtInfoMsg( action.result.msg);
        			frm.setModeUpdate();
    			}
    			,failure: function(form, action) {
    				if(action.result.error_code == 19){
    					Ext.Msg.confirm('Confirm Action',action.result.msg,function(btn) {
	    					if(btn=='yes'){
	    						form.onAdjust(form, 'add');
	    					}
	    				});
    				}
					else if(action.result.error_code == 43){
    					Ext.Msg.confirm('Confirm Action',action.result.msg,function(btn) {
	    					if(btn=='yes'){
	    						form.onAdjust(form, 'update');
	    					}
	    				});
    				}else{
						showExtErrorMsg(action.result.msg);	
    				}
    			}	
    		});
		}
		,onAdjust: function(frm, param){
			frm.submit({
    			url: '/isop/adjustIsop/'+param 
    			,method: 'POST'
    			,waitMsg: 'Updating Data...'
    			,params: { auth:_AUTH_KEY, 'isop[modified_by]': _USER_ID, 'isop[created_by]': _USER_ID	
    			}
    			,success: function(form, action) {
    				showExtInfoMsg( action.result.msg);
        			frm.setModeUpdate();
    			}
    			,failure: function(form, action) {
    				if(action.result.error_code == 19){
    					Ext.Msg.confirm('Confirm Action',action.result.msg,function(btn) {
	    					if(btn=='yes'){
	    						form.onAdjust(form);
	    					}
	    				});
    				}else{
						showExtErrorMsg(action.result.msg);	
    				}
    			}	
    		});
		}
		,onDelete: function(){
			Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
				if(btn=='yes') {
					Ext.getCmp('isopDetail').getForm().submit({
						url: '/isop/delete' 
						,method: 'POST'
						,params: {auth:_AUTH_KEY, 'isop[modified_by]': _USER_ID}
						,waitMsg: 'Deleting Data...'
						,clientValidation: false
						,success: function(form, action) {
							showExtInfoMsg( action.result.msg);
			    			Ext.getCmp('isopDetail').getForm().reset();
			    			Ext.getCmp('isopDetail').hide();
							Ext.getCmp('isopList').show();
							Ext.getCmp('isopDetail').setModeNew();
							//pecaDataStores.isopStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
							if (pecaDataStores.isopStore.getCount() % MAX_PAGE_SIZE == 1){
								var page = pecaDataStores.isopStore.getTotalCount() - MAX_PAGE_SIZE - 1;
								pecaDataStores.isopStore.load({params: {start:page<0?0:page, limit:MAX_PAGE_SIZE}});
							} else{
								pecaDataStores.isopStore.reload();
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

var isopList = function(){
	return {
		xtype: 'grid'
		,id: 'isopList'
		,titlebar: false
		,store: pecaDataStores.isopStore
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
		,layoutConfig: {
		  deferredRender: true
		  ,layoutOnCardChange: true
		}
		,cm: isopColumns
		
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
					Ext.getCmp('isopList').hide();
					Ext.getCmp('isopDetail').show();
					Ext.getCmp('isopDetail').getForm().setModeUpdate();
					Ext.getCmp('isopDetail').getForm().load({
				    	url: '/isop/show'
				    	,params: {'isop[transaction_no]':(rec.get('isop[transaction_no]'))
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
					if(_PERMISSION[57]==0){
						Ext.getCmp('isopEmpId').setDisabled(true);
						Ext.getCmp('isopLastname').setDisabled(true);
						Ext.getCmp('isopFirstname').setDisabled(true);
						Ext.getCmp('isopSearchID').setDisabled(true);	
					}else{
						Ext.getCmp('isopEmpId').setDisabled(false);
						Ext.getCmp('isopLastname').setDisabled(false);
						Ext.getCmp('isopFirstname').setDisabled(false);
						Ext.getCmp('isopSearchID').setDisabled(false);
						pecaDataStores.isopStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
					}
					//new button
					if(_PERMISSION[8]==0){
						Ext.getCmp('isopNewID').setDisabled(true);	
					}else{
						Ext.getCmp('isopNewID').setDisabled(false);
					}
					//delete button
					if(_PERMISSION[33]==0){
						Ext.getCmp('isopDeleteID').setDisabled(true);	
					}else{
						Ext.getCmp('isopDeleteID').setDisabled(false);
					}
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
			,id: 'isopEmpId'
			,name: 'isopEmpId'
            ,hideLabel: true
            ,emptyText: 'ID'
			,autoCreate: {tag: 'input', type: 'text', maxlength: '8'}
			,style: 'text-align: right'
			,enableKeyEvents: true
			,listeners: {
				specialkey: function(frm,evt){
					if (evt.getKey() == evt.ENTER) {
						pecaDataStores.isopStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});				
					}
				}
				,keypress: function(txt,evt){
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
			,id: 'isopLastname'
            ,hideLabel: true
            ,emptyText: 'Last Name'
			,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
			,listeners: {
				specialkey: function(frm,evt){
					if (evt.getKey() == evt.ENTER) {
						/*pecaDataStores.isopStore.load({params: {
							'isop[employee_id]': Ext.getCmp('EmpId').getValue()
							,'isop[last_name]': Ext.getCmp('lastname').getValue()
							,'isop[first_name]': Ext.getCmp('firstname').getValue()
							,start:0
							,limit:MAX_PAGE_SIZE
							,auth:_AUTH_KEY}})*/
						pecaDataStores.isopStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});				
					}
				}
			}
    	},' ',{
            xtype: 'textfield'
            ,width: 100
			,id: 'isopFirstname'
            ,hideLabel: true
            ,emptyText: 'First Name'
			,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
			,listeners: {
				specialkey: function(frm,evt){
					if (evt.getKey() == evt.ENTER) {
						/*pecaDataStores.isopStore.load({params: {
							'isop[employee_id]': Ext.getCmp('EmpId').getValue()
							,'isop[last_name]': Ext.getCmp('lastname').getValue()
							,'isop[first_name]': Ext.getCmp('firstname').getValue()
							,start:0
							,limit:MAX_PAGE_SIZE
							,auth:_AUTH_KEY}})*/
						pecaDataStores.isopStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});					
					}
				}
			}
        },' ',{
			text:'Search'
			,id: 'isopSearchID'
			,iconCls: 'icon_ext_search'
			,scope:this
			,handler:function(btn) {
				/*pecaDataStores.isopStore.load({params: {
					'isop[employee_id]': Ext.getCmp('EmpId').getValue()
					,'isop[last_name]': Ext.getCmp('lastname').getValue()
					,'isop[first_name]': Ext.getCmp('firstname').getValue()
					,start:0
					,limit:MAX_PAGE_SIZE
					,auth:_AUTH_KEY}})*/
				pecaDataStores.isopStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
			}
		},'-'
		,{
			text:'New'
			,id: 'isopNewID'	
			,tooltip:'Add a New ISOP'
			,iconCls: 'icon_ext_add'
			,scope:this
			,handler:function(btn) {
				Ext.getCmp('isopDetail').show();
				Ext.getCmp('isopList').hide();
				isopDetail().setModeNew();
			}
		},'-'
		,{
			text:'Delete'
			,id: 'isopDeleteID'	
			,tooltip:'Delete Selected ISOP'
			,iconCls: 'icon_ext_del'
			,scope:this
			,handler:function(btn) {
				var index = Ext.getCmp('isopList').getSelectionModel().getSelected();
		        if (!index) {
		        	showExtInfoMsg( "Please select an ISOP to delete.");
		            return false;
		        }
		        Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
					if(btn=='yes') {
				        Ext.Ajax.request({
				        	url: '/isop/delete' 
							,method: 'POST'
							,params: {'isop[transaction_no]':index.data.transaction_no
				        				,auth:_AUTH_KEY, 'isop[modified_by]': _USER_ID}
							,waitMsg: 'Deleting Data...'
							,success: function(response, opts) {
								var obj = Ext.decode(response.responseText);
								if(obj.success){
									showExtInfoMsg( obj.msg);
									//pecaDataStores.isopStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
									if (pecaDataStores.isopStore.getCount() % MAX_PAGE_SIZE == 1){
										var page = pecaDataStores.isopStore.getTotalCount() - MAX_PAGE_SIZE - 1;
										pecaDataStores.isopStore.load({params: {start:page<0?0:page, limit:MAX_PAGE_SIZE}});
									} else{
										pecaDataStores.isopStore.reload();
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
	        ,store: pecaDataStores.isopStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};

var isop_employeeListWin = function(){
	return new Ext.Window({
		id: 'isop_employeeListWin'
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
		,items:[ isop_employeeList() ]
        ,buttons:[{
 			text: 'Cancel'
 			,iconCls: 'icon_ext_cancel'
 		    ,handler : function(btn){
 				Ext.getCmp('isop_employeeListWin').close();				
 		    }
 		}]
	});
};

var isop_employeeList = function(){
	return {
		xtype: 'grid'
		,id: 'isop_employeeList'
		,titlebar: false
		,store: pecaDataStores.isopEmployeeStore
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
		,cm: isopEmployeeColumns
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
					Ext.getCmp('isopDetail').getForm().findField('isop[employee_id]').setValue(rec.get('employee_id'));
					Ext.getCmp('isopDetail').getForm().findField('isop[last_name]').setValue(rec.get('last_name'));
					Ext.getCmp('isopDetail').getForm().findField('isop[first_name]').setValue(rec.get('first_name'));
					Ext.getCmp('isop_employeeListWin').close.defer(1,Ext.getCmp('isop_employeeListWin'));
				}
			}
		}
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.isopEmployeeStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: 'No records found'
	    })
	};
};