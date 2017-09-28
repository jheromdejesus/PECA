var processOnline = function(){
	return {
		xtype:'form'
		,id:'processOnline'
		,region:'center'
		,anchor: '100%'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		//,reader: pecaReaders.companyReader
		,listeners:{
			'render':{
				scope:this
				,fn:function(form){
					form.load({
						url: '/loan_year'
						,params: {auth:_AUTH_KEY}
						,method: 'POST'
						,waitMsgTarget: true
					
					});
				}
			}
		}
		,buttons:[{
			text:'Process'
			,iconCls: 'icon_ext_proc'
			,handler: function(){
		    	var frm = Ext.getCmp('processOnline').getForm();
		    	frm.submit({
		    		url: '/common/transferAllStatus' 
		    			,method: 'POST'
		    			,params: {auth:_AUTH_KEY, 'processOnline[user_id]': _USER_ID}
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
	};
};