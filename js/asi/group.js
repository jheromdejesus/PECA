//Let's pretend we rendered our grid-columns with meta-data from our ORM framework.
var groupColumns =  new Ext.grid.ColumnModel( 
	[
		{id: 'group_id', header: "Group ID", width: 100, sortable: true, dataIndex: 'group_id_formatted'}
		,{header: "Group Name", sortable: true, dataIndex: 'group_name_formatted'}
	]
);

var groupDetail = function(){
	return {
		xtype:'form'
		,id:'groupDetail'
		,region:'center'
		,title: 'Details'
		,hidden:true
		,autoscroll: true
		,frame: true
		,anchor: '100%'
		,bodyStyle:{'padding':'10px'}
		,reader: pecaReaders.groupReader
		,buttons:[{
			text: 'Cancel'
			,iconCls: 'icon_ext_cancel'
		    ,handler : function(btn){
				Ext.getCmp('groupDetail').getForm().reset();
				Ext.getCmp('groupDetail').hide();
				Ext.getCmp('groupList').show();
				pecaDataStores.groupStore.reload();
		    }
		},{
			text: 'Delete'
			,iconCls: 'icon_ext_del'
			,handler : function(){
				Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
					if(btn=='yes') {
						Ext.getCmp('groupDetail').getForm().submit({
							url: '/group/delete' 
							,method: 'POST'
							,params: {auth:_AUTH_KEY, 'group[modified_by]': _USER_ID}
							,clientValidation: false
							,waitMsg: 'Deleting Data...'
							,success: function(form, action) {
				    			showExtInfoMsg(action.result.msg);
				    			Ext.getCmp('groupDetail').setModeNew();
				    			Ext.getCmp('groupDetail').getForm().reset();
				    			Ext.getCmp('groupDetail').hide();
								Ext.getCmp('groupList').show();
								if(pecaDataStores.groupStore.getCount()%20 ==1){
									pecaDataStores.groupStore.load();
								} else {
									pecaDataStores.groupStore.reload();
								}
							}
							,failure: function(form, action) {
								showExtErrorMsg(action.result.msg);
							}	
						});
					}
				});
		    }
		},{
			text:'Save'
			,iconCls: 'icon_ext_save'
		    ,handler: function(){
		    	var frm = Ext.getCmp('groupDetail').getForm();
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
            ,labelWidth: 150
            ,defaults: {width: 300}
            ,items: [{
			    xtype: 'hidden'
			    ,name: 'frm_mode'
			    ,value: FORM_MODE_NEW
			    ,listeners: {'change':{fn: function(obj,value){
                }}}
			},{
                fieldLabel: 'Group ID'
                ,name: 'group[group_id]'
                ,anchor:'25%'
				,boxMinWidth: 90
				,boxMaxWidth: 100
                ,allowBlank: false
                ,required: true
                ,maxLength: 10
                ,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
            },{
                fieldLabel: 'Group Name'
                ,name: 'group[group_name]'
                ,anchor:'75%'
				,boxMinWidth: 250
				,boxMaxWidth: 250
                ,allowBlank: false
                ,required: true
                ,maxLength: 30
                ,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
            }]
		}]
	    ,isModeNew: function() {
	    	return (Ext.getCmp('groupDetail').getForm().findField('frm_mode').getValue() == FORM_MODE_NEW);
	    }
	    ,setModeNew: function() {
	    	Ext.getCmp('groupDetail').getForm().findField('frm_mode').setValue(FORM_MODE_NEW);
	    	Ext.getCmp('groupDetail').buttons[0].setVisible(true);  //cancel button
	    	Ext.getCmp('groupDetail').buttons[1].setVisible(false);  //delete button
	    	Ext.getCmp('groupDetail').buttons[2].setVisible(true);  //save button
			Ext.getCmp('groupDetail').getForm().findField('group[group_id]').setReadOnly(false);
			Ext.getCmp('groupDetail').getForm().findField('group[group_id]').removeClass('x-item-disabled');
		}
		,setModeUpdate: function() {
			Ext.getCmp('groupDetail').getForm().findField('frm_mode').setValue(FORM_MODE_UPDATE);
			Ext.getCmp('groupDetail').buttons[0].setVisible(true);  //cancel button
	    	Ext.getCmp('groupDetail').buttons[1].setVisible(true);  //delete button
	    	Ext.getCmp('groupDetail').buttons[2].setVisible(true);  //save button
			Ext.getCmp('groupDetail').getForm().findField('group[group_id]').setReadOnly(true);
			Ext.getCmp('groupDetail').getForm().findField('group[group_id]').addClass('x-item-disabled');
	    }
		,onSave: function(frm){
			frm.submit({
    			url: '/group/add' 
    			,method: 'POST'
    			,params: {auth:_AUTH_KEY, 'group[created_by]': _USER_ID}
    			,waitMsg: 'Creating Data...'
    			,success: function(form, action) {
        			showExtInfoMsg(action.result.msg);
    				frm.setModeUpdate();
    			}
    			,failure: function(form, action) {
    				if(action.result.error_code == 2){
    					showExtInfoMsg(action.result.msg);
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
    			url: '/group/update' 
    			,method: 'POST'
    			,waitMsg: 'Updating Data...'
    			,params: {
        			auth:_AUTH_KEY, 'group[modified_by]': _USER_ID	
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
	};
};

var groupList = function(){
	return {
		xtype: 'grid'
		,id: 'groupList'
		,titlebar: false
		,store: pecaDataStores.groupStore
		,region: 'center'
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 425
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit:true
			,scrollOffset: 0
		}
		,cm: groupColumns
		
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
					Ext.getCmp('groupList').hide();
					Ext.getCmp('groupDetail').show();
					Ext.getCmp('groupDetail').getForm().setModeUpdate();
					Ext.getCmp('groupDetail').getForm().load({
				    	url: '/group/show'
				    	,params: {'group[group_id]':(rec.get('group[group_id]'))
									,auth:_AUTH_KEY}
				    	,method: 'POST'
				    	,waitMsgTarget: true
					});
				}
			}
			,'render':{
				scope:this
				,fn:function(grid){
					pecaDataStores.groupStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
				}
			}
		}
		,tbar:[{
			text:'New'
			,tooltip:'Add a new Group '
			,iconCls: 'icon_ext_add'
			,scope:this
			,handler:function(btn) {
				Ext.getCmp('groupDetail').show();
				Ext.getCmp('groupList').hide();
				groupDetail().setModeNew();
			}
		},'-',
		{
			text:'Delete'
			,tooltip:'Delete Selected Group'
			,iconCls: 'icon_ext_del'
			,scope:this
			,handler:function(btn) {
				var index = Ext.getCmp('groupList').getSelectionModel().getSelected();
		        if (!index) {
		        	showExtInfoMsg("Please select a Group to delete.");
		            return false;
		        }
		        Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
					if(btn=='yes') {
				        Ext.Ajax.request({
				        	url: '/group/delete' 
							,method: 'POST'
							,params: {'group[group_id]':index.data["group[group_id]"]
				        				,auth:_AUTH_KEY, 'group[modified_by]': _USER_ID}
							,waitMsg: 'Deleting Data...'
							,success: function(response, opts) {
								var obj = Ext.decode(response.responseText);
								if(obj.success){
									showExtInfoMsg(obj.msg);
									if(pecaDataStores.groupStore.getCount()%20 ==1){
										pecaDataStores.groupStore.load();
									} else {
										pecaDataStores.groupStore.reload();
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
	        ,store: pecaDataStores.groupStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};