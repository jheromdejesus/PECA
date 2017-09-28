//Let's pretend we rendered our grid-columns with meta-data from our ORM framework.
var coaColumns =  new Ext.grid.ColumnModel( 
	[
		{id: 'account_no', header: "Account No", width: 100, sortable: true, dataIndex: 'account_no_formatted'}
		,{header: "Account Name", width: 200, sortable: true, dataIndex: 'account_name_formatted'}
		,{header: "Account Group", width: 200, sortable: true, dataIndex: 'accntGrp_name_formatted'}
	]
);

var coaDetail = function(){
	return {
		xtype:'form'
		,id:'coaDetail'
		,region:'center'
		,title: 'Details'
		,hidden:true
		,anchor: '100%'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,reader: pecaReaders.coaReader
		,buttons:[{
			text: 'Cancel'
			,iconCls: 'icon_ext_cancel'
		    ,handler : function(btn){
				Ext.getCmp('coaDetail').hide();
				Ext.getCmp('coaList').show();
				Ext.getCmp('coaDetail').getForm().reset();
				pecaDataStores.coaStore.reload();
		    }
		},{
		text: 'Delete'
			,iconCls: 'icon_ext_del'
			,handler: function(){
				var frm = Ext.getCmp('coaDetail').getForm();
				frm.onDelete();
		    }
		},{
			text:'Save'
			,iconCls: 'icon_ext_save'
		    ,handler: function(){
		    	var frm = Ext.getCmp('coaDetail').getForm();
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
            ,labelWidth: 100
            ,defaults: {width: 300}
            ,items: [{
			    xtype: 'hidden'
			    ,name: 'frm_mode'
			    ,value: FORM_MODE_NEW
			    ,submitValue: false
			    ,listeners: {'change':{fn: function(obj,value){
                }}}
			},{
                fieldLabel: 'Account No.'
                ,name: 'coa[account_no]'
                ,anchor:'25%'
                ,allowBlank: false
                ,required: true
                ,maxLength: 4
                ,autoCreate: {tag: 'input', type: 'text', maxlength: '4'}
            },{
                fieldLabel: 'Account Name'
                ,name: 'coa[account_name]'
                ,anchor:'45%'
                ,allowBlank: false
                ,required: true
                ,maxLength: 30
                ,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
            },new Ext.form.ComboBox({
                fieldLabel: 'Account Group'
                ,hiddenName: 'coa[account_group]'
        	    ,typeAhead: true
        	    ,triggerAction: 'all'
        	    ,lazyRender:true
        	    ,store: pecaDataStores.agStore
        	    ,mode: 'local'
        	    ,valueField: 'code'
        	    ,displayField: 'name'
        	    ,anchor: '75%'
        	    ,emptyText: 'Please Select'
         	    ,forceSelection: true
        	    ,submitValue: false
        	    ,allowBlank: false
        	    ,required: true
        	    ,anchor:'35%'
        	}),{
            	xtype: 'datefield'
				,fieldLabel: 'Effectivity Date'
				,name: 'coa[effectivity_date]'	
				,anchor:'35%'
				//,allowBlank: false
				//,required: true
				,maxLength: 10
				,disableKeyFilter: true
				,minValue: _TODAY
				,invalidText: 'Effectivity Date is not a valid date - it must be in the format MM/DD/YYYY'
				,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
            }]
		}]
	    ,isModeNew: function() {
	    	return (Ext.getCmp('coaDetail').getForm().findField('frm_mode').getValue() == FORM_MODE_NEW);
	    }
	    ,setModeNew: function() {
	    	Ext.getCmp('coaDetail').getForm().findField('frm_mode').setValue(FORM_MODE_NEW);
	    	Ext.getCmp('coaDetail').buttons[0].setVisible(true);  //cancel button
	    	Ext.getCmp('coaDetail').buttons[1].setVisible(false);  //delete button
	    	Ext.getCmp('coaDetail').buttons[2].setVisible(true);  //save button
	    	Ext.getCmp('coaDetail').getForm().findField('coa[account_no]').focus('',250);
	    	Ext.getCmp('coaDetail').getForm().findField('coa[account_no]').setReadOnly(false);
			Ext.getCmp('coaDetail').getForm().findField('coa[account_no]').removeClass('x-item-disabled');
		}
		,setModeUpdate: function() {
			Ext.getCmp('coaDetail').getForm().findField('frm_mode').setValue(FORM_MODE_UPDATE);
			Ext.getCmp('coaDetail').buttons[0].setVisible(true);  //cancel button
	    	Ext.getCmp('coaDetail').buttons[1].setVisible(true);  //delete button
	    	Ext.getCmp('coaDetail').buttons[2].setVisible(true);  //save button
	    	
	    	//can't update record
			if(_PERMISSION[120]==0){
				Ext.getCmp('coaDetail').buttons[2].setDisabled(true);  //save button
			}
			//can't delete record
			if(_PERMISSION[26]==0){
				Ext.getCmp('coaDetail').buttons[1].setDisabled(true);  //save button
			}
	    	
	    	Ext.getCmp('coaDetail').getForm().findField('coa[account_name]').focus('',250);
	    	Ext.getCmp('coaDetail').getForm().findField('coa[account_no]').setReadOnly(true);
			Ext.getCmp('coaDetail').getForm().findField('coa[account_no]').addClass('x-item-disabled');
	    }
		,onSave: function(frm){
			frm.submit({
    			url: '/chart_of_accounts/add' 
    			,method: 'POST'
    			,params: {auth:_AUTH_KEY, 'coa[created_by]': _USER_ID}
    			,waitMsg: 'Creating Data...'
    			,success: function(form, action) {
    				showExtInfoMsg(action.result.msg);
    				frm.setModeUpdate();
    			}
    			,failure: function(form, action) {
    				if(action.result.error_code == 2 || action.result.error_code == -1){
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
    			url: '/chart_of_accounts/update' 
    			,method: 'POST'
    			,waitMsg: 'Updating Data...'
    			,params: {
        			auth:_AUTH_KEY, 'coa[modified_by]': _USER_ID	
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
					Ext.getCmp('coaDetail').getForm().submit({
						url: '/chart_of_accounts/delete' 
						,method: 'POST'
						,params: {auth:_AUTH_KEY, 'coa[modified_by]': _USER_ID}
						,waitMsg: 'Deleting Data...'
						,clientValidation: false
						,success: function(form, action) {
							showExtInfoMsg(action.result.msg);
			    			Ext.getCmp('coaDetail').setModeNew();
			    			Ext.getCmp('coaDetail').hide();
			    			Ext.getCmp('coaDetail').getForm().reset();
							Ext.getCmp('coaList').show();
							// pecaDataStores.coaStore.load();
							if (pecaDataStores.coaStore.getCount() % MAX_PAGE_SIZE == 1){
								pecaDataStores.coaStore.load({params: {start:pecaDataStores.coaStore.getTotalCount() - MAX_PAGE_SIZE - 1, limit:MAX_PAGE_SIZE}});
							} else{
								pecaDataStores.coaStore.reload();
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

var coaList = function(){
	return {
		xtype: 'grid'
		,id: 'coaList'
		,titlebar: false
		,store: pecaDataStores.coaStore
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
		,cm: coaColumns
		
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
					Ext.getCmp('coaList').hide();
					Ext.getCmp('coaDetail').show();
					Ext.getCmp('coaDetail').getForm().setModeUpdate();
					Ext.getCmp('coaDetail').getForm().load({
				    	url: '/chart_of_accounts/show'
				    	,params: {'coa[account_no]':(rec.get('coa[account_no]'))
									,auth:_AUTH_KEY}
				    	,method: 'POST'
				    	,waitMsgTarget: true
					});
				}
			}
			,'render':{
				scope:this
				,fn:function(grid){
					pecaDataStores.agStore.load();
					pecaDataStores.coaStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
					//new button
					if(_PERMISSION[1]==0){
						Ext.getCmp('coaNewID').setDisabled(true);	
					}else{
						Ext.getCmp('coaNewID').setDisabled(false);
					}
					//delete button
					if(_PERMISSION[26]==0){
						Ext.getCmp('coaDeleteID').setDisabled(true);	
					}else{
						Ext.getCmp('coaDeleteID').setDisabled(false);
					}
				}
			}
		}
		,tbar:[{
			text:'New'
			,id: 'coaNewID'
			,tooltip:'Add an Account No.'
			,iconCls: 'icon_ext_add'
			,scope:this
			,handler:function(btn) {
				Ext.getCmp('coaDetail').show();
				Ext.getCmp('coaList').hide();
				coaDetail().setModeNew();
			}
		},'-'
		,{
			text:'Delete'
			,id: 'coaDeleteID'
			,tooltip:'Delete Selected Account No'
			,iconCls: 'icon_ext_del'
			,scope:this
			,handler:function(btn) {
				var index = Ext.getCmp('coaList').getSelectionModel().getSelected();
		        if (!index) {
		        	showExtInfoMsg("Please select an Account to delete.");
		            return false;
		        }
		        Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
					if(btn=='yes') {
				        Ext.Ajax.request({
				        	url: '/chart_of_accounts/delete' 
							,method: 'POST'
							,params: {'coa[account_no]':index.data.account_no
										,'coa[account_name]':index.data.account_name
				        				,auth:_AUTH_KEY, 'coa[modified_by]': _USER_ID}
							,waitMsg: 'Deleting Data...'
							,success: function(response, opts) {
								var obj = Ext.decode(response.responseText);
								if(obj.success){
									showExtInfoMsg(obj.msg);
									// pecaDataStores.coaStore.load();
									if (pecaDataStores.coaStore.getCount() % MAX_PAGE_SIZE == 1){
										pecaDataStores.coaStore.load({params: {start:pecaDataStores.coaStore.getTotalCount() - MAX_PAGE_SIZE - 1, limit:MAX_PAGE_SIZE}});
									} else{
										pecaDataStores.coaStore.reload();
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
	        ,store: pecaDataStores.coaStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};