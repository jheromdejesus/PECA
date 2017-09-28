//Let's pretend we rendered our grid-columns with meta-data from our ORM framework.
var companyColumns =  new Ext.grid.ColumnModel( 
	[
		{id: 'company_code', header: "Company Code", width: 100, sortable: true, dataIndex: 'company_code_formatted'}
		,{header: "Company Name", width: 200, sortable: true, dataIndex: 'company_name_formatted'}
	]
);

var companyDetail = function(){
	return {
		xtype:'form'
		,id:'companyDetail'
		,region:'center'
		,title: 'Details'
		,hidden:true
		,anchor: '100%'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,reader: pecaReaders.companyReader
		,buttons:[{
			text: 'Cancel'
			,iconCls: 'icon_ext_cancel'
		    ,handler : function(btn){
				Ext.getCmp('companyDetail').hide();
				Ext.getCmp('companyList').show();
				Ext.getCmp('companyDetail').getForm().reset();
				pecaDataStores.companyStore.reload();
		    }
		},{
			text: 'Delete'
			,iconCls: 'icon_ext_del'
			,handler: function(){
				var frm = Ext.getCmp('companyDetail').getForm();
				frm.onDelete();
		    }
		},{
			text:'Save'
			,iconCls: 'icon_ext_save'
		    ,handler: function(){
		    	var frm = Ext.getCmp('companyDetail').getForm();
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
            ,labelWidth: 125
            ,defaults: {width: 300}
            ,items: [{
			    xtype: 'hidden'
			    ,name: 'frm_mode'
			    ,value: FORM_MODE_NEW
			    ,submitValue: false
			    ,listeners: {'change':{fn: function(obj,value){
                }}}
			},{
                fieldLabel: 'Company Code'
                ,name: 'company[company_code]'
                ,anchor:'25%'
                ,allowBlank: false
                ,required: true
                ,maxLength: 4
                ,autoCreate: {tag: 'input', type: 'text', maxlength: '4'}
            },{
                fieldLabel: 'Company Name'
                ,name: 'company[company_name]'
                ,anchor:'50%'
                ,allowBlank: false
                ,required: true
                ,maxLength: 30
                ,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
            }]
		}]
	    ,isModeNew: function() {
	    	return (Ext.getCmp('companyDetail').getForm().findField('frm_mode').getValue() == FORM_MODE_NEW);
	    }
	    ,setModeNew: function() {
	    	Ext.getCmp('companyDetail').getForm().findField('frm_mode').setValue(FORM_MODE_NEW);
	    	Ext.getCmp('companyDetail').buttons[0].setVisible(true);  //cancel button
	    	Ext.getCmp('companyDetail').buttons[1].setVisible(false);  //delete button
	    	Ext.getCmp('companyDetail').buttons[2].setVisible(true);  //save button
	    	Ext.getCmp('companyDetail').getForm().findField('company[company_code]').focus('',250);
			Ext.getCmp('companyDetail').getForm().findField('company[company_code]').setReadOnly(false);
			Ext.getCmp('companyDetail').getForm().findField('company[company_code]').removeClass('x-item-disabled');
		}
		,setModeUpdate: function() {
			Ext.getCmp('companyDetail').getForm().findField('frm_mode').setValue(FORM_MODE_UPDATE);
			Ext.getCmp('companyDetail').buttons[0].setVisible(true);  //cancel button
	    	Ext.getCmp('companyDetail').buttons[1].setVisible(true);  //delete button
	    	Ext.getCmp('companyDetail').buttons[2].setVisible(true);  //save button
	    	
	    	//can't update record
			if(_PERMISSION[121]==0){
				Ext.getCmp('companyDetail').buttons[2].setDisabled(true);  //save button
			}
			//can't delete record
			if(_PERMISSION[27]==0){
				Ext.getCmp('companyDetail').buttons[1].setDisabled(true);  //save button
			}
	    	
	    	Ext.getCmp('companyDetail').getForm().findField('company[company_name]').focus('',250);
			Ext.getCmp('companyDetail').getForm().findField('company[company_code]').setReadOnly(true);
			Ext.getCmp('companyDetail').getForm().findField('company[company_code]').addClass('x-item-disabled');
	    }
		,onSave: function(frm){
			frm.submit({
    			url: '/company/add' 
    			,method: 'POST'
    			,params: {auth:_AUTH_KEY, 'company[created_by]': _USER_ID}
    			,waitMsg: 'Creating Data...'
    			,success: function(form, action) {
    				showExtInfoMsg(action.result.msg);
    				frm.setModeUpdate();
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
    			url: '/company/update' 
    			,method: 'POST'
    			,waitMsg: 'Updating Data...'
    			,params: {
        			auth:_AUTH_KEY, 'company[modified_by]': _USER_ID	
    			}
    			,success: function(form, action) {
    				showExtInfoMsg(action.result.msg);
        			frm.setModeUpdate();
    			}
    			,failure: function(form, action) {
    				showExtErrorMsg(action.result.msg);
    			}	
    		});
		}
		,onDelete: function(){
			Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
				if(btn=='yes') {
					Ext.getCmp('companyDetail').getForm().submit({
						url: '/company/delete' 
						,method: 'POST'
						,params: {auth:_AUTH_KEY, 'company[modified_by]': _USER_ID}
						,waitMsg: 'Deleting Data...'
						,clientValidation: false
						,success: function(form, action) {
							showExtInfoMsg(action.result.msg);
			    			Ext.getCmp('companyDetail').setModeNew();
			    			Ext.getCmp('companyDetail').getForm().reset();
			    			Ext.getCmp('companyDetail').hide();
							Ext.getCmp('companyList').show();
							// pecaDataStores.companyStore.load();
							if (pecaDataStores.companyStore.getCount() % MAX_PAGE_SIZE == 1){
								pecaDataStores.companyStore.load({params: {start:pecaDataStores.companyStore.getTotalCount() - MAX_PAGE_SIZE - 1, limit:MAX_PAGE_SIZE}});
							} else{
								pecaDataStores.companyStore.reload();
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

var companyList = function(){
	return {
		xtype: 'grid'
		,id: 'companyList'
		,titlebar: false
		,store: pecaDataStores.companyStore
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
		,cm: companyColumns
		
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
					Ext.getCmp('companyList').hide();
					Ext.getCmp('companyDetail').show();
					Ext.getCmp('companyDetail').getForm().setModeUpdate();
					Ext.getCmp('companyDetail').getForm().load({
				    	url: '/company/show'
				    	,params: {'company[company_code]':(rec.get('company[company_code]'))
									,auth:_AUTH_KEY}
				    	,method: 'POST'
				    	,waitMsgTarget: true
					});
				}
			}
			,'render':{
				scope:this
				,fn:function(grid){
					pecaDataStores.companyStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
					//new button
					if(_PERMISSION[2]==0){
						Ext.getCmp('companyNewID').setDisabled(true);	
					}else{
						Ext.getCmp('companyNewID').setDisabled(false);
					}
					//delete button
					if(_PERMISSION[27]==0){
						Ext.getCmp('companyDeleteID').setDisabled(true);	
					}else{
						Ext.getCmp('companyDeleteID').setDisabled(false);
					}
				}
			}
		}
		,tbar:[{
			text:'New'
			,id: 'companyNewID'
			,tooltip:'Add a Company Code'
			,iconCls: 'icon_ext_add'
			,scope:this
			,handler:function(btn) {
				Ext.getCmp('companyDetail').show();
				Ext.getCmp('companyList').hide();
				companyDetail().setModeNew();
			}
		},'-'
		,{
			text:'Delete'
			,id: 'companyDeleteID'
			,tooltip:'Delete Selected Company Code'
			,iconCls: 'icon_ext_del'
			,scope:this
			,handler:function(btn) {
				var index = Ext.getCmp('companyList').getSelectionModel().getSelected();
		        if (!index) {
		        	showExtInfoMsg("Please select a Company to delete.");
		            return false;
		        }
		        Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
					if(btn=='yes') {
				        Ext.Ajax.request({
				        	url: '/company/delete' 
							,method: 'POST'
							,params: {'company[company_code]':index.data.company_code
										,'company[company_name]':index.data.company_name
				        				,auth:_AUTH_KEY, 'company[modified_by]': _USER_ID}
							,waitMsg: 'Deleting Data...'
							,success: function(response, opts) {
								var obj = Ext.decode(response.responseText);
								if(obj.success){
									showExtInfoMsg(obj.msg);
									// pecaDataStores.companyStore.load();
									if (pecaDataStores.companyStore.getCount() % MAX_PAGE_SIZE == 1){
										pecaDataStores.companyStore.load({params: {start:pecaDataStores.companyStore.getTotalCount() - MAX_PAGE_SIZE - 1, limit:MAX_PAGE_SIZE}});
									} else{
										pecaDataStores.companyStore.reload();
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
	        ,store: pecaDataStores.companyStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};