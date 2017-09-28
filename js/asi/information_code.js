//Let's pretend we rendered our grid-columns with meta-data from our ORM framework.
var infocodeColumns =  new Ext.grid.ColumnModel( 
	[
		{id: 'information_code', header: "Information Code", width: 100, sortable: true, dataIndex: 'information_code_formatted'}
		,{header: "Information Description", width: 200, sortable: true, dataIndex: 'information_description_formatted'}
	]
);

var infocodeDetail = function(){
	return {
		xtype:'form'
		,id:'infocodeDetail'
		,region:'center'
		,title: 'Details'
		,hidden:true
		,anchor: '100%'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,reader: pecaReaders.infocodeReader
		,buttons:[{
			text: 'Cancel'
			,iconCls: 'icon_ext_cancel'
		    ,handler : function(btn){
				Ext.getCmp('infocodeDetail').hide();
				Ext.getCmp('infocodeList').show();
				Ext.getCmp('infocodeDetail').getForm().reset();
				pecaDataStores.infocodeStore.reload();
		    }
		},{
			text: 'Delete'
			,iconCls: 'icon_ext_del'
			,handler: function(){
				var frm = Ext.getCmp('infocodeDetail').getForm();
				frm.onDelete();
		    }
		},{
			text:'Save'
			,iconCls: 'icon_ext_save'
		    ,handler: function(){
		    	var frm = Ext.getCmp('infocodeDetail').getForm();
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
			    ,submitValue: false
			    ,listeners: {'change':{fn: function(obj,value){
                }}}
			},{
                fieldLabel: 'Information Code'
                ,name: 'infocode[information_code]'
                ,anchor:'25%'
                ,allowBlank: false
                ,required: true
                ,maxLength: 2
                ,autoCreate: {tag: 'input', type: 'text', maxlength: '2'}
            },{
                fieldLabel: 'Information Description'
                ,name: 'infocode[information_description]'
                ,anchor:'50%'
                ,allowBlank: false
                ,required: true
                ,maxLength: 30
                ,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
            }]
		}]
	    ,isModeNew: function() {
	    	return (Ext.getCmp('infocodeDetail').getForm().findField('frm_mode').getValue() == FORM_MODE_NEW);
	    }
	    ,setModeNew: function() {
	    	Ext.getCmp('infocodeDetail').getForm().findField('frm_mode').setValue(FORM_MODE_NEW);
	    	Ext.getCmp('infocodeDetail').buttons[0].setVisible(true);  //cancel button
	    	Ext.getCmp('infocodeDetail').buttons[1].setVisible(false);  //delete button
	    	Ext.getCmp('infocodeDetail').buttons[2].setVisible(true);  //save button
	    	Ext.getCmp('infocodeDetail').getForm().findField('infocode[information_code]').focus('',250);
			Ext.getCmp('infocodeDetail').getForm().findField('infocode[information_code]').setReadOnly(false);
			Ext.getCmp('infocodeDetail').getForm().findField('infocode[information_code]').removeClass('x-item-disabled');
		}
		,setModeUpdate: function() {
			Ext.getCmp('infocodeDetail').getForm().findField('frm_mode').setValue(FORM_MODE_UPDATE);
			Ext.getCmp('infocodeDetail').buttons[0].setVisible(true);  //cancel button
	    	Ext.getCmp('infocodeDetail').buttons[1].setVisible(true);  //delete button
	    	Ext.getCmp('infocodeDetail').buttons[2].setVisible(true);  //save button
	    	
	    	//can't update record
			if(_PERMISSION[126]==0){
				Ext.getCmp('infocodeDetail').buttons[2].setDisabled(true);  //save button
			}
			//can't delete record
			if(_PERMISSION[31]==0){
				Ext.getCmp('infocodeDetail').buttons[1].setDisabled(true);  //save button
			}
			
	    	Ext.getCmp('infocodeDetail').getForm().findField('infocode[information_description]').focus('',250);
			Ext.getCmp('infocodeDetail').getForm().findField('infocode[information_code]').setReadOnly(true);
			Ext.getCmp('infocodeDetail').getForm().findField('infocode[information_code]').addClass('x-item-disabled');
	    }
		,onSave: function(frm){
			frm.submit({
    			url: '/information_code/add' 
    			,method: 'POST'
    			,params: {auth:_AUTH_KEY, 'infocode[created_by]': _USER_ID}
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
    			url: '/information_code/update' 
    			,method: 'POST'
    			,waitMsg: 'Updating Data...'
    			,params: {
        			auth:_AUTH_KEY, 'infocode[modified_by]': _USER_ID	
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
					Ext.getCmp('infocodeDetail').getForm().submit({
						url: '/information_code/delete' 
						,method: 'POST'
						,params: {auth:_AUTH_KEY, 'infocode[modified_by]': _USER_ID}
						,waitMsg: 'Deleting Data...'
						,clientValidation: false
						,success: function(form, action) {
							showExtInfoMsg(action.result.msg);
			    			Ext.getCmp('infocodeDetail').setModeNew();
			    			Ext.getCmp('infocodeDetail').getForm().reset();
			    			Ext.getCmp('infocodeDetail').hide();
							Ext.getCmp('infocodeList').show();
							// pecaDataStores.infocodeStore.load();
							if (pecaDataStores.infocodeStore.getCount() % MAX_PAGE_SIZE == 1){
								pecaDataStores.infocodeStore.load({params: {start:pecaDataStores.infocodeStore.getTotalCount() - MAX_PAGE_SIZE - 1, limit:MAX_PAGE_SIZE}});
							} else{
								pecaDataStores.infocodeStore.reload();
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

var infocodeList = function(){
	return {
		xtype: 'grid'
		,id: 'infocodeList'
		,titlebar: false
		,store: pecaDataStores.infocodeStore
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
		,cm: infocodeColumns
		
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
					Ext.getCmp('infocodeList').hide();
					Ext.getCmp('infocodeDetail').show();
					Ext.getCmp('infocodeDetail').getForm().setModeUpdate();
					Ext.getCmp('infocodeDetail').getForm().load({
				    	url: '/information_code/show'
				    	,params: {'infocode[information_code]':(rec.get('infocode[information_code]'))
									,auth:_AUTH_KEY}
				    	,method: 'POST'
				    	,waitMsgTarget: true
					});
				}
			}
			,'render':{
				scope:this
				,fn:function(grid){
					pecaDataStores.infocodeStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});					
					//new button
					if(_PERMISSION[6]==0){
						Ext.getCmp('infocodeNewID').setDisabled(true);	
					}else{
						Ext.getCmp('infocodeNewID').setDisabled(false);
					}
					//delete button
					if(_PERMISSION[31]==0){
						Ext.getCmp('infocodeDeleteID').setDisabled(true);	
					}else{
						Ext.getCmp('infocodeDeleteID').setDisabled(false);
					}
				}
			}
		}
		,tbar:[{
			text:'New'
			,id: 'infocodeNewID'
			,tooltip:'Add a infocode Code'
			,iconCls: 'icon_ext_add'
			,scope:this
			,handler:function(btn) {
				Ext.getCmp('infocodeDetail').show();
				Ext.getCmp('infocodeList').hide();
				infocodeDetail().setModeNew();
			}
		},'-'
		,{
			text:'Delete'
			,id: 'infocodeDeleteID'
			,tooltip:'Delete Selected Information Code'
			,iconCls: 'icon_ext_del'
			,scope:this
			,handler:function(btn) {
				var index = Ext.getCmp('infocodeList').getSelectionModel().getSelected();
		        if (!index) {
		        	showExtInfoMsg("Please select an Information Code to delete.");
		            return false;
		        }
		        Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
					if(btn=='yes') {
				        Ext.Ajax.request({
				        	url: '/information_code/delete' 
							,method: 'POST'
							,params: {'infocode[information_code]':index.data.information_code
									    ,'infocode[information_description]':index.data.information_description
				        				,auth:_AUTH_KEY, 'infocode[modified_by]': _USER_ID}
							,waitMsg: 'Deleting Data...'
							,success: function(response, opts) {
								var obj = Ext.decode(response.responseText);
								if(obj.success){
									showExtInfoMsg(obj.msg);
									// pecaDataStores.infocodeStore.load();
									if (pecaDataStores.infocodeStore.getCount() % MAX_PAGE_SIZE == 1){
										pecaDataStores.infocodeStore.load({params: {start:pecaDataStores.infocodeStore.getTotalCount() - MAX_PAGE_SIZE - 1, limit:MAX_PAGE_SIZE}});
									} else{
										pecaDataStores.infocodeStore.reload();
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
	        ,store: pecaDataStores.infocodeStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};