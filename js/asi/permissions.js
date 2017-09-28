//Let's pretend we rendered our grid-columns with meta-data from our ORM framework.
var per_groupColumns =  new Ext.grid.ColumnModel( 
	[
		{id: 'group[group_id]', header: "Group ID", width: 100, sortable: true, dataIndex: 'group[group_id]'}
		,{header: "Group Name", width: 200, sortable: true, dataIndex: 'group[group_name]'}		
		,{header: "Permission", hidden: true,width: 200, sortable: true, dataIndex: 'group[permission]'}		
	]
);

var per_userColumns =  new Ext.grid.ColumnModel( 
		[
			{id: 'user[user_id]', header: "User ID", width: 100, sortable: true, dataIndex: 'user[user_id]'}
			,{header: "User Name", width: 200, sortable: true, dataIndex: 'user[user_name]'}
			,{header: "Permission",hidden:false, width: 200, sortable: true, dataIndex: 'user[permission]'}
		]
	);

var per_FunctionColumns =  new Ext.grid.ColumnModel( 
		[
		 	{id: 'function_idx', header: "Function Idx",hidden:true, width: 100, sortable: true, dataIndex: 'function_idx'}
			,{header: "Function Name", name: "function_name", width: 200, sortable: true, dataIndex: 'function_name'}
			,{header: "Function Value", name: "function_value",hidden:true, width: 200, sortable: true, dataIndex: 'function_value'}
		]
	);




var permissionsDetail = function(){
	return {
		xtype:'form'
		,id:'permissionsDetail'
		,region:'center'
		,title: 'Details'
		,hidden:false
		,anchor: '100%'
		,autoscroll: true
		,frame: true
		,reader: pecaReaders.permissionsReader
		,bodyStyle:{'padding':'10px'}
		,buttons:[{
			text:'Save'	
			,iconCls: 'icon_ext_save'
		    ,handler: function(){
		    	var frm = Ext.getCmp('permissionsDetail').getForm();
		    	if(frm.isValid()){
		    		frm.onUpdate(frm);
		    	}        	
		    }
		},{
			  xtype:'button'
				  ,name: 'but1'
				  ,text: '<'
				  ,handler: function(){
					var rowsSelected = Ext.getCmp('availableFxnList').getSelectionModel().getSelections();
					var rowsCount = rowsSelected.length;
					var aRecord;
					var Function = Ext.data.Record.create([{
	    	    	    name: 'function_idx'
	    	    	}, {
	    	    	    name: 'function_name'
	    	    	}]);
					
					for(var i = 0; i < rowsCount; i++){
						aRecord = rowsSelected[i];
						fxnName = aRecord.get('function_name');
						fxnIndex = aRecord.get('function_idx');
						fxnValue = aRecord.get('function_value');
						
						pecaDataStores.permittedFxnStore.add(new Function({
		    	    	    function_idx: fxnIndex
		    	    	   , function_name: fxnName
		    	    	   , function_value: fxnValue
		    	    	}));
						pecaDataStores.availableFxnStore.remove(aRecord);
					}
			    }
		 }]
		,items: [{
	        layout:'column'
	        ,items:[{
	            columnWidth:.5
	            ,layout: 'form'
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
		            xtype: 'radiogroup'
		            ,fieldLabel: ''
		            ,hideLabel: true
		            ,columns: 2
		            ,items: [
		                {boxLabel: 'Groups'
		                , name: 'rdoOption'
		                , inputValue: 'GROUPS'
		                , checked: true
		                ,  listeners: {
		                    'check': function (checkbox, checked) {
		                        if( checked ){
		                        	Ext.getCmp('per_groupList').reconfigure(pecaDataStores.groupPermStore, per_groupColumns);
		                        	pecaDataStores.groupPermStore.load();
		                        	pecaDataStores.availableFxnStore.removeAll();
		        		    		pecaDataStores.permittedFxnStore.removeAll();
		                        }
		                    }
		                }},
		                {boxLabel: 'Users'
		                , name: 'rdoOption'
		                , inputValue: 'USERS'
		                ,  listeners: {
		                    'check': function (checkbox, checked) {
		                        if( checked ){
		                        	 Ext.getCmp('per_groupList').reconfigure(pecaDataStores.userPermStore, per_userColumns);
		                        	pecaDataStores.userPermStore.load();
		                        	pecaDataStores.availableFxnStore.removeAll();
		        		    		pecaDataStores.permittedFxnStore.removeAll();
		                        }
		                    }
		                }}
		            ]
		        },{
	    			layout: 'fit'
	    			,xtype:'fieldset'	
	    			,title: ''	
	    			,hideBorders: true
	    	        ,defaultType: 'grid'
	    	        ,height: 250
	    			,items: [per_groupList()]
	    		},{
	    			layout: 'fit'
		    		,xtype:'fieldset'	
		    		,title: 'List of Available Functions'	
		    	    ,defaultType: 'grid'
		    	    ,height: 250
		    		,items: [availableFxnList()]
		    	}]
	        },{
	        	 columnWidth:.5
	             ,layout: 'form'
	             ,defaultType: 'textfield'
	             ,labelWidth: 75
	             ,defaults: {width: 300}
	             ,items: [{hidden:true}
	             , {
		    		layout: 'fit'
				    ,xtype:'fieldset'	
				    ,title: 'List of Permitted Functions'	
				    ,defaultType: 'grid'
				    ,height: 250
				    ,items: [permittedFxnList()]
				  },{
		                fieldLabel: ''
		                ,name: 'permission[user_group_id]'
		                ,id:'permission[user_group_id]'
		                ,anchor:'95%'
		                ,allowBlank: false
		                //,required: true
		                ,maxLength: 30
		                ,hidden:true
		                ,hideLabel: true
		                ,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
		         },]
	        }]
	    }]
	    ,isModeNew: function() {
	    	return (Ext.getCmp('transcodeDetail').getForm().findField('frm_mode').getValue() == FORM_MODE_NEW);
	    }
		,setModeUpdate: function() {
			Ext.getCmp('transcodeDetail').getForm().findField('frm_mode').setValue(FORM_MODE_UPDATE);
	    	Ext.getCmp('transcodeDetail').buttons[0].setVisible(true);  //save button
	    }
		,onSave: function(frm){
        	frm.submit({
    			url: '/permissions/add' 
    			,method: 'POST'
    			,params: {'transcode[with_or]': Ext.getCmp('with_or').getValue() ? 'Y' : 'N'
        			,'transcode[bank_transfer]': Ext.getCmp('bank_transfer').getValue() ? 'Y' : 'N'
            		,'transcode[capcon_req]': Ext.getCmp('capcon_req').getValue() ? 'Y' : 'N'
            		,auth:_AUTH_KEY, 'transcode[created_by]': _USER_ID}
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
			
			//get data from permitted functions grid
			var jsonData="[";
		
			pecaDataStores.permittedFxnStore.data.each(function(row) {
		    
		        jsonData += Ext.util.JSON.encode(row.data['function_idx']);
			    jsonData += ",";
			   
		    });
			jsonData = jsonData.substring(0,jsonData.length-1) + "]";
			frm.submit({
    			url: '/permissions/update' 
    			,method: 'POST'
    			,waitMsg: 'Updating Data...'
    			,params: {
					auth:_AUTH_KEY
					, 'permission[modified_by]': _USER_ID	
					, 'permission[functions]' : jsonData
    			}
    			,success: function(form, action) {
        			showExtInfoMsg(action.result.msg);
        			frm.setModeUpdate();
        			pecaDataStores.groupPermStore.load();
        			pecaDataStores.userPermStore.load();
    			}
    			,failure: function(form, action) {
    				showExtErrorMsg(action.result.msg);
    			}	
    		});
		}
	};
};

var per_groupList = function(){
	return {
		xtype: 'grid'
		,id: 'per_groupList'
		,titlebar: false
		,store: pecaDataStores.groupPermStore
		,region: 'center'
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 450
		,width: 1100
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit:true
			,scrollOffset:0
		}
		,cm: per_groupColumns
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
					var param = '';
					
					if(Ext.getCmp('permissionsDetail').getForm().getValues()['rdoOption'] == 'USERS'){
						param = (rec.get('user[permission]'));
						Ext.get('permission[user_group_id]').set({value: rec.get('user[user_id]')});
					}else{
						param = (rec.get('group[permission]'));
						Ext.get('permission[user_group_id]').set({value: rec.get('group[group_id]')});
					}
					pecaDataStores.availableFxnStore.load({params:{'permission' : param}});
		    		pecaDataStores.permittedFxnStore.load({params:{'permission' : param}});
				}
			}
			,'render':{
				scope:this
				,fn:function(grid){
					pecaDataStores.groupPermStore.load();
					
				}
			}
		}
	};
};

var availableFxnList = function(){
	return {
		xtype: 'grid'
		,id: 'availableFxnList'
		,titlebar: false
		,store: pecaDataStores.availableFxnStore
		,region: 'center'
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 450
		,width: 1100
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit:true
			,scrollOffset:0
		}
		,cm: per_FunctionColumns
		,listeners:{
			'click':{
				scope:this
				,fn:function(evt) {
					Ext.getCmp('permittedFxnList').getSelectionModel().clearSelections();
					
				}
			}
		}
	};
};

var permittedFxnList = function(){
	return {
		xtype: 'grid'
		,id: 'permittedFxnList'
		,titlebar: false
		,store: pecaDataStores.permittedFxnStore
		,region: 'center'
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 450
		,width: 1100
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit:true
			,scrollOffset:0
		}
		,cm: per_FunctionColumns
		,listeners:{
			'click':{
				scope:this
				,fn:function(evt) {
					Ext.getCmp('availableFxnList').getSelectionModel().clearSelections();
					
				}
			}
		}
	};
};