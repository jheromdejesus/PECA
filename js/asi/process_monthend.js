var processMonthend = function(){
	return {
		xtype:'form'
		,id:'processMonthend'
		,region:'center'
		,anchor: '100%'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		//,reader: pecaReaders.companyReader
		,buttons:[{
			text:'Process'
			,iconCls: 'icon_ext_proc'
			,handler: function(){
		    	var frm = Ext.getCmp('processMonthend').getForm();
		    	if (frm.isValid()){
		    		if (frm.hasSelected()){
		    			frm.submit({
				    		url: '/month_end/processMonthEnd' 
				    			,method: 'POST'
				    			,params: {auth:_AUTH_KEY, 'month_end[user_id]': _USER_ID}
				    			,waitMsg: 'Processing Data...'
				    			,timeout: 300000
				    			,success: function(form, action) {
				        			showExtInfoMsg(action.result.msg);
				        			frm.reset();
				        			Ext.getCmp('processMonthend').getForm().load({
										url: '/month_end'
							    		,params: {auth:_AUTH_KEY}
								    	,method: 'POST'
								    	,waitMsgTarget: true
									});
				    			}
				    			,failure: function(form, action) {
				    				showExtErrorMsg(action.result.msg);
				    			}	
			    		});
		    		} else{
		    			showExtInfoMsg('Please select at least one Month-end processing option.');
		    		}
		    	}
	    	}
		}]
		,reader: new Ext.data.JsonReader({
			root: 'data'
			},[
			    {name: 'month_end[accounting_period]', mapping: 'acctgperiod', type: 'string'}
			    ,{name: 'month_end[capcon_posting]', mapping: 'capcon_posting', type: 'string'}
			    ,{name: 'month_end[journal_posting]', mapping: 'journal_posting', type: 'string'}
			]
		)
		,listeners:{
			'render':{
				scope:this
				,fn:function(form){
					form.load({
						url: '/month_end'
						,params: {auth:_AUTH_KEY}
						,method: 'POST'
						,waitMsgTarget: true
					});
					if(_PERMISSION[48]==0){
						Ext.getCmp('processMonthend').buttons[0].setDisabled(true);	
					}
				}
			}
		}
		,items: [
		         {
	                layout: 'form',
	                items: [
	                    {
	                        labelWidth: 100,
	                        labelAlign: 'left',
	                        layout: 'form',
	                        border: false,
	                        items: [
	                            {
	                                xtype: 'textfield',
	                                fieldLabel: 'Current Period',
	                                maxLength: 10,
	                                name: 'month_end[accounting_period]'
	                                ,readOnly: true
	                                ,anchor: '30%'
	                                ,cls: 'x-item-disabled'
	                            }
	                        ]
	                    }
	                ]
	            },{
	                layout: 'form',
	                items: [
	                    {
	                        layout: 'column',
	                        border: false,
	                        items: [
	                            {
	                                labelWidth: 10,
	                                labelAlign: 'left',
	                                layout: 'form',
	                                columnWidth: 0.3,
	                                border: false,
	                                items: [
	                                    {
	                                        xtype: 'checkbox',
	                                        boxLabel: 'Post CapCon Balances'
	                                        ,id: 'cc_capcon'
                        	                ,name: 'month_end[capcon]'	
                        	                ,readOnly: true
                        	                ,anchor:'100%'
                        	                ,submitValue: true
	                                    },
	                                    {
	                                        xtype: 'checkbox',
	                                        boxLabel: 'Post Journal Entries'
	                                        ,id: 'cc_journal'
                        	                ,name: 'month_end[journal]'	
                        	                ,readOnly: true
                        	                ,anchor:'100%'
                        	                ,submitValue: true
	                                    }
	                                ]
	                            },
	                            {
	                                labelWidth: 125,
	                                labelAlign: 'left',
	                                layout: 'form',
	                                columnWidth: 0.4,
	                                border: false,
	                                items: [
	                                    {
	                                        xtype: 'textfield',
	                                        fieldLabel: 'Last Posted Period',
	                                        name: 'month_end[capcon_posting]'
	                                        ,cls: 'x-item-disabled'
	                                        ,maxLength: 10
	                                    },
	                                    {
	                                        xtype: 'textfield',
	                                        fieldLabel: 'Last Posted Period',
	                                        name: 'month_end[journal_posting]'
	                                        ,cls: 'x-item-disabled'
	                                        ,maxLength: 10
	                                    }
	                                ]
	                            }
	                        ]
	                    }
	                ]
	            }
            ]
            ,hasSelected: function() {
    	    	var form = Ext.getCmp('processMonthend').getForm();
    	    	ch1 = form.findField('month_end[capcon]');
    	    	ch2 = form.findField('month_end[journal]');
    	    	
    	    	return (ch1.getValue() || ch2.getValue());
    	    }
	};
};