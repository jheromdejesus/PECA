var processDividend = function(){
	return {
		xtype:'form'
		,id:'processDividend'
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
		    	var frm = Ext.getCmp('processDividend').getForm();
		    	if (frm.isValid()){
		    		frm.submit({
			    		url: '/dividend/processDividend' 
			    			,method: 'POST'
			    			,params: {auth:_AUTH_KEY, 'dividend[created_by]': _USER_ID}
			    			,waitMsg: 'Processing Data...'
			    			,timeout: 300000
			    			,success: function(form, action) {
			        			showExtInfoMsg(action.result.msg);
			        			frm.reset();
			        			Ext.getCmp('processDividend').getForm().load({
									url: '/dividend'
						    		,params: {auth:_AUTH_KEY}
							    	,method: 'POST'
							    	,waitMsgTarget: true
								});
			    			}
			    			,failure: function(form, action) {
			    				showExtErrorMsg(action.result.msg);
			    			}	
		    		});
		    	}
	    	}
		}]
		,reader: new Ext.data.JsonReader({
			root: 'data'
			},[
			    {name: 'dividend[accounting_period]', mapping: 'currdate', type: 'string'}
			]
		)
		,listeners:{
			'render':{
				scope:this
				,fn:function(form){
					form.load({
						url: '/dividend'
						,params: {auth:_AUTH_KEY}
						,method: 'POST'
						,waitMsgTarget: true
					
					});
					if(_PERMISSION[44]==0){
						Ext.getCmp('processDividend').buttons[0].setDisabled(true);	
					}
				}
			}
		}
		,items: [
					{
					    "layout": "form",
					    "items": [
					        {
					            "labelWidth": 100,
					            "labelAlign": "left",
					            "layout": "form",
					            "border": false,
					            "items": [
					                {
					                    "xtype": "textfield",
					                    "fieldLabel": "Current Period",
					                    "anchor": "30%",
					                    "maxLength": 10,
					                    readOnly: true,
					                    "name": "dividend[accounting_period]"
					                    ,cls: 'x-item-disabled'
					                }
					            ]
					        },
					        {
					        	"xtype": "fieldset",
								anchor: '100%',
					            "title": "Options",
					            "layout": "form",
					            "items": [
					                {
					                    "border": false,
					                    "layout": "column",
					                    "items": [
					                        {
					                            "labelWidth": 100,
					                            "labelAlign": "left",
					                            "layout": "form",
					                            "columnWidth": 0.35,
					                            "border": false,
					                            "items": [
					                                {
					                                    "xtype": "datefield",
					                                    "fieldLabel": "Inclusive Dates",
					                                    "name": "dividend[start_date]",
					                                    "maxLength": 10,
					                                    allowBlank: false,
														invalidText: 'From date is not a valid date - it must be in the format MM/DD/YYYY',
					                                    autoCreate: {tag: 'input', type: 'text', maxlength: '10'},
					                                    required: true
					                                    ,validator: function(value1){
					                                    	var frm = Ext.getCmp('processDividend').getForm();
							                        		var value2 = frm.findField('dividend[end_date]').value;
															
															if (value1 == null || value1 == '' || isNaN(Date.parse(value1))){
																return true;
															}
															
															value1 = Ext.util.Format.date(value1, "y/m/d");
															value2 = Ext.util.Format.date(value2, "y/m/d");
							                        		
							                        		if (value2 != null && value2 != ''){
							                        			if (value1 <= value2){
								                        			return true;
								                        		} else{
								                        			return 'From date should be lesser than or equal to To date.';
								                        		}
							                        		}else{
							                        			return true;
							                        		}
							                        		
							                        	}
					                                    // ,listeners:{
					                            			// 'blur':{
					                            				// scope:this
					                            				// ,fn:function(form){
							                                    	// var frm = Ext.getCmp('processDividend').getForm();
																	// frm.findField('dividend[start_date]').validate();
									                        		// frm.findField('dividend[end_date]').validate();
					                            				// }
					                            			// }
					                            		// }
					                                }
					                            ]
					                        },{
					                            "labelWidth": 40,
					                            "labelAlign": "left",
					                            "layout": "form",
					                            "columnWidth": 0.4,
					                            "border": false,
					                            "items": [
					                                {
					                                    "xtype": "datefield",
					                                    "fieldLabel": "to",
					                                    "name": "dividend[end_date]",
				                                    	"maxLength": 10,
					                                    allowBlank: false,
														invalidText: 'To date is not a valid date - it must be in the format MM/DD/YYYY',
					                                    autoCreate: {tag: 'input', type: 'text', maxlength: '10'},
					                                    required: true	
					                                    , validator: function(value2){
					                                    	var frm = Ext.getCmp('processDividend').getForm();
							                        		var value1 = frm.findField('dividend[start_date]').value;
															
															if (value2 == null || value2 == '' || isNaN(Date.parse(value2))){
																return true;
															}
															
															value1 = Ext.util.Format.date(value1, "y/m/d");
															value2 = Ext.util.Format.date(value2, "y/m/d");
							                        		
							                        		if (value1 != null && value1 != ''){
							                        			if (value1 <= value2){
								                        			return true;
								                        		} else{
								                        			return 'To date should be greater than or equal to From date.';
								                        		}
							                        		}else{
							                        			return true;
							                        		}
							                        		
							                        	}
					                                    // ,listeners:{
					                            			// 'blur':{
					                            				// scope:this
					                            				// ,fn:function(form){
							                                    	// var frm = Ext.getCmp('processDividend').getForm();
									                        		// frm.findField('dividend[start_date]').validate();
																	// frm.findField('dividend[end_date]').validate();
					                            				// }
					                            			// }
					                            		// }
					                                }
					                            ]
					                        }
					                     ]
					                },
					                {
					                    "layout": "column",
					                    "border": false,
					                    "items": [
					                        {
					                            "labelWidth": 125,
					                            "labelAlign": "left",
					                            "layout": "form",
					                            "columnWidth": 0.4,
					                            "border": false,
					                            "items": [
					                                {
					                                    "xtype": "numberfield",
					                                    "fieldLabel": "Dividend Rate",
					                                    "name": "dividend[dividend_rate]",
					                                    "maxLength": 6
														,style: 'text-align: right'
					                                    ,autoCreate: {tag: 'input', type: 'text', maxlength: '6'}
					                                    ,allowBlank: false
					                                    ,required: true
														,minValue: 0
														,maxValue: 999.99
					                                    
					                                },
					                                {
					                                    "xtype": "numberfield",
					                                    "fieldLabel": "Withholding Tax Rate",
					                                    "name": "dividend[with_tax_rate]",
				                                    	"maxLength": 6
														,style: 'text-align: right'
					                                    ,autoCreate: {tag: 'input', type: 'text', maxlength: '6'}
														,minValue: 0
														,maxValue: 999.99
					                                }
					                            ]
					                        },
					                        {
					                            "labelWidth": 125,
					                            "labelAlign": "left",
					                            "layout": "form",
					                            "columnWidth": 0.4,
					                            "border": false,
					                            "items": [
					                                {
					                                    "xtype": "combo",
					                                    "fieldLabel": "Dividend Code",
					                                    "anchor": "100%",
					                                    "hiddenName": "dividend[dividend_code]"
					                                    ,typeAhead: true
					                	        	    ,triggerAction: 'all'
					                	        	    ,lazyRender:true
					                	        	    ,store: pecaDataStores.divStore
					                	        	    ,mode: 'local'
					                	        	    ,valueField: 'code'
					                	        	    ,displayField: 'name'
					                                    ,emptyText: 'Please Select'
					                                    ,forceSelection: true
					                	        	    ,submitValue: false
					                	        	    ,allowBlank: false
					                	        	    ,required: true
					                                },
					                                {
					                                    "xtype": "combo",
					                                    "fieldLabel": "Withholding Tax Code",
					                                    "anchor": "100%",
					                                    "hiddenName": "dividend[with_tax_code]"
				                                    	,typeAhead: true
				                    	        	    ,triggerAction: 'all'
				                    	        	    ,lazyRender:true
				                    	        	    ,store: pecaDataStores.divStore
				                    	        	    ,mode: 'local'
				                    	        	    ,valueField: 'code'
				                    	        	    ,displayField: 'name'
					                                    ,emptyText: 'Please Select'
					                                    ,forceSelection: true
					                	        	    ,submitValue: false
					                                }
					                            ]
					                        }
					                    ]
					                }
					            ]
					        }
					    ]
					}
	            ]
	};
};