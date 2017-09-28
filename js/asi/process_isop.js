var processIsop = function(){
	return {
		xtype:'form'
		,id:'processIsop'
		,region:'center'
		,anchor: '100%'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		//,reader: pecaReaders.companyReader
		,reader: new Ext.data.JsonReader({
			root: 'data'
			},[
			    {name: 'process_isop[current_date]', mapping: 'currdate', type: 'string'}
			]
		)
		,listeners:{
			'render':{
				scope:this
				,fn:function(form){
					form.load({
						url: '/process_isop'
						,params: {auth:_AUTH_KEY}
						,method: 'POST'
						,waitMsgTarget: true
					
					});
					if(_PERMISSION[65]==0){
						Ext.getCmp('processIsop').buttons[0].setDisabled(true);	
					}	
				}
			}
		}
		,buttons:[{
			text:'Process'
			,iconCls: 'icon_ext_proc'
			,handler: function(){
		    	var frm = Ext.getCmp('processIsop').getForm();
		    	frm.submit({
		    		url: '/process_isop/processIsop' 
		    			,method: 'POST'
		    			,params: {auth:_AUTH_KEY, 'process_isop[user_id]': _USER_ID}
		    			,waitMsg: 'Processing Data...'
		    			,timeout: 300000
		    			,success: function(form, action) {
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
            ,fieldLabel: 'Date'
            ,name: 'process_isop[current_date]'
            ,readOnly: true
            ,maxLength: 10
            ,anchor: '30%'
            ,cls: 'x-item-disabled'
        }]
	};
};