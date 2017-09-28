var processDormant = function(){
	return {
		xtype:'form'
		,id:'processDormant'
		,region:'center'
		,anchor: '100%'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		//,reader: pecaReaders.companyReader
		,reader: new Ext.data.JsonReader({
			root: 'data'
			},[
			    {name: 'dormant_account[current_date]', mapping: 'currdate', type: 'string'}
				, {name: 'dormant_account[date1]', mapping: 'date1', type: 'string'}
				, {name: 'dormant_account[date2]', mapping: 'date2', type: 'string'}
				, {name: 'dormant_account[date3]', mapping: 'date3', type: 'string'}
			]
		)
		,listeners:{
			'render':{
				scope:this
				,fn:function(form){
					form.load({
						url: '/dormant_account'
						,params: {auth:_AUTH_KEY}
						,method: 'POST'
						,waitMsgTarget: true
					
					});
					if(_PERMISSION[143]==0){
						Ext.getCmp('processDormant').buttons[0].setDisabled(true);	
					}
				}
			}
		}
		,buttons:[{
			text:'Process'
			,iconCls: 'icon_ext_proc'
			,handler: function(){
		    	var frm = Ext.getCmp('processDormant').getForm();
		    	frm.submit({
		    		url: '/dormant_account/processDormantAccount' 
		    			,method: 'POST'
		    			,params: {auth:_AUTH_KEY, 'dormant_account[user_id]': _USER_ID}
		    			,waitMsg: 'Processing Data...'
		    			,timeout: 300000
		    			,success: function(form, action) {
							// loadForm('processDormant','/dormant_account');
							frm.load({
								url: '/dormant_account'
								,params: {auth:_AUTH_KEY}
								,method: 'POST'
								,waitMsgTarget: true
							
							});
		        			showExtInfoMsg(action.result.msg);
		    			}
		    			,failure: function(form, action) {
		    				showExtErrorMsg(action.result.msg);
		    			}	
	    		});
	    	}
		}]
		,items: [{
			xtype: 'textfield'
            ,fieldLabel: 'Current Period'
            ,name: 'dormant_account[current_date]'
            ,readOnly: true
            ,maxLength: 10
            ,anchor: '30%'
            ,cls: 'x-item-disabled'
		},{
			xtype: 'textfield'
            ,fieldLabel: 'Last 3 runs'
            ,name: 'dormant_account[date1]'
            ,readOnly: true
            ,maxLength: 10
            ,anchor: '30%'
            ,cls: 'x-item-disabled'
		},{
			xtype: 'textfield'
            // ,fieldLabel: 'Current Period'
            ,name: 'dormant_account[date2]'
            ,readOnly: true
            ,maxLength: 10
            ,anchor: '30%'
            ,cls: 'x-item-disabled'
		},{
			xtype: 'textfield'
            // ,fieldLabel: 'Current Period'
            ,name: 'dormant_account[date3]'
            ,readOnly: true
            ,maxLength: 10
            ,anchor: '30%'
            ,cls: 'x-item-disabled'
		}]
	};
};