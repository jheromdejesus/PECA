var processLoanYear = function(){
	return {
		xtype:'form'
		,id:'processLoanYear'
		,region:'center'
		,anchor: '100%'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		//,reader: pecaReaders.companyReader
		,reader: new Ext.data.JsonReader({
			root: 'data'
			},[
			    {name: 'process_loan_year[current_date]', mapping: 'currdate', type: 'string'}
			]
		)
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
					if(_PERMISSION[142]==0){
						Ext.getCmp('processLoanYear').buttons[0].setDisabled(true);	
					}
				}
			}
		}
		,buttons:[{
			text:'Process'
			,iconCls: 'icon_ext_proc'
			,handler: function(){
		    	var frm = Ext.getCmp('processLoanYear').getForm();
		    	frm.submit({
		    		url: '/loan_year/processLoanYearTerm' 
		    			,method: 'POST'
		    			,params: {auth:_AUTH_KEY, 'process_loan_year[user_id]': _USER_ID}
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
            ,fieldLabel: 'Current Period'
            ,name: 'process_loan_year[current_date]'
            ,readOnly: true
            ,anchor: '30%'
            ,cls: 'x-item-disabled'
		}]
	};
};