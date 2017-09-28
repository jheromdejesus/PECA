var processPD = function(){
	return {
		xtype:'form'
		,id:'processPD'
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
		    	var frm = Ext.getCmp('processPD').getForm();
		    	if (frm.isValid()){
		    		if (frm.hasSelected()){
		    			frm.submit({
				    		url: '/process_payroll_deduction/processPayrollDeduction' 
				    			,method: 'POST'
				    			,params: {auth:_AUTH_KEY, 'process_pd[user_id]': _USER_ID}
				    			,waitMsg: 'Processing Data...'
				    			,timeout: 300000
				    			,success: function(form, action) {
				        			showExtInfoMsg(action.result.msg);
				        			frm.reset();
				        			Ext.getCmp('processPD').getForm().load({
										url: '/process_payroll_deduction'
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
		    			showExtInfoMsg('Please select at least one Payroll deduction option.');
		    		}
		    	}
	    	}
		}]
		,reader: new Ext.data.JsonReader({
			root: 'data'
			},[
			    {name: 'process_pd[current_date]', mapping: 'currdate', type: 'string'}
			]
		)
		,listeners:{
			'render':{
				scope:this
				,fn:function(form){
					form.load({
						url: '/process_payroll_deduction'
						,params: {auth:_AUTH_KEY}
						,method: 'POST'
						,waitMsgTarget: true
					
					});
					if(_PERMISSION[49]==0){
						Ext.getCmp('processPD').buttons[0].setDisabled(true);	
					}
				}
			}
		}
		,items: [
		         {
				    layout: 'form',
				    border: false,
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
				                    anchor: '30%',
				                    name: 'process_pd[current_date]'
				                    ,readOnly: true
		                            ,cls: 'x-item-disabled'
				                }
				            ]
				        },
				        {
				            xtype: 'fieldset',
				            title: 'Select the type of payroll deduction',
				            layout: 'form',
							anchor: '100%',
				            items: [
				                {
				                    layout: 'column',
				                    border: false,
				                    items: [
				                        {
				                            labelWidth: 100,
				                            labelAlign: 'left',
				                            layout: 'form',
				                            columnWidth: 0.3,
				                            border: false,
				                            items: [
				                                {
				                                    xtype: 'datefield',
				                                    fieldLabel: '1st Half',
				                                    anchor: '90%'
				                                    ,id: "first_half"
			                                    	,name: "process_pd[first_half]"
			                                    	,maxlength: 10
			                                    	,disabled: true
			                                    	,hidden: true
			                                    	,required: true
			                                    	,allowBlank: false
				                                    ,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
													,invalidText: 'First Half is not a valid date - it must be in the format MM/DD/YYYY'
				                                	//,cls: 'x-item-disabled'
				                                	,validator: function(value1){
				                                    	var frm = Ext.getCmp('processPD').getForm();
						                        		var value2 = frm.findField('process_pd[second_half]').value;
														
														if (value1 == null || value1 == '' || isNaN(Date.parse(value1))){
															return true;
														}
						                        		
						                        		if (value2 != null && value2 !== ''){
															first = new Date(value1);
						                        			second = new Date(value2);
						                        			if (first <= second){
						                        				if (first.getMonth() == second.getMonth() && first.getFullYear() == second.getFullYear()){
						                        					return true;
						                        				} else{
						                        					return 'First half and second half should belong to the same month and year.'
						                        				}
							                        			
							                        		} else{
							                        			return 'First half should be lesser than or equal to second half.';
							                        		}
						                        		}else{
						                        			return true;
						                        		}
						                        		
						                        	}
				                                    // ,listeners:{
				                            			// 'blur':{
				                            				// scope:this
				                            				// ,fn:function(form){
						                                    	// var frm = Ext.getCmp('processPD').getForm();
																// frm.findField('process_pd[first_half]').validate();
								                        		// frm.findField('process_pd[second_half]').validate();
				                            				// }
				                            			// }
				                            		// }
				                                }
				                            ]
				                        },
				                        {
				                            labelWidth: 100,
				                            labelAlign: 'left',
				                            layout: 'form',
				                            columnWidth: 0.3,
				                            border: false,
				                            items: [
				                                {
				                                    xtype: 'datefield',
				                                    fieldLabel: '2nd Half',
				                                    anchor: '90%',
			                                    	name: "process_pd[second_half]"
			                                    	,id: "second_half"
			                                    	,maxlength: 10
				                                    ,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
				                                	,disabled: true
				                                	,hidden: true
				                                	,required: true
			                                    	,allowBlank: false
													,invalidText: 'Second Half is not a valid date - it must be in the format MM/DD/YYYY'
				                                	//,cls: 'x-item-disabled'
				                                    ,validator: function(value2){
				                                    	var frm = Ext.getCmp('processPD').getForm();
						                        		var value1 = frm.findField('process_pd[first_half]').value;
														
														if (value2 == null || value2 == '' || isNaN(Date.parse(value2))){
															return true;
														}
														
						                        		if (value1 != null && value1 !== ''){
															first = new Date(value1);
						                        			second = new Date(value2);
						                        			if (first <= second){         				
						                        				if (first.getMonth() == second.getMonth() && first.getFullYear() == second.getFullYear()){
						                        					return true;
						                        				} else{
						                        					return 'First half and second half should belong to the same month and year.'
						                        				}
							                        			
							                        		} else{
							                        			return 'Second half should be greater than or equal to first half.';
							                        		}
						                        		}else{
						                        			return true;
						                        		}
						                        		
						                        	}
				                                    // ,listeners:{
				                            			// 'blur':{
				                            				// scope:this
				                            				// ,fn:function(form){
						                                    	// var frm = Ext.getCmp('processPD').getForm();
								                        		// frm.findField('process_pd[first_half]').validate();
																// frm.findField('process_pd[second_half]').validate();
				                            				// }
				                            			// }
				                            		// }
				                                }
				                            ]
				                        }
				                    ]
				                },
				                {
				                    labelWidth: 10,
				                    labelAlign: 'left',
				                    layout: 'form',
				                    border: false,
				                    items: [
				                        {
				                            xtype: 'checkbox',
				                            boxLabel: 'Payroll Deduction for Savings'
				                            ,name: 'process_pd[savings]'	
                        	                ,anchor:'100%'
                        	                ,submitValue: true
                        	                ,listeners:{
		                            			'check':{
		                            				scope:this
		                            				,fn:function(cc, bool){
				                                    	if (bool){
															Ext.getCmp('processPD').getForm().findField('process_pd[first_half]').reset();
					                                    	Ext.getCmp('processPD').getForm().findField('process_pd[second_half]').reset();
															
				                                    		Ext.getCmp('processPD').getForm().findField('process_pd[first_half]').setDisabled(false);
				                                    		Ext.getCmp('processPD').getForm().findField('process_pd[second_half]').setDisabled(false);
				                                    		
				                                    		Ext.getCmp('processPD').getForm().findField('process_pd[first_half]').setVisible(true);
				                                    		Ext.getCmp('processPD').getForm().findField('process_pd[second_half]').setVisible(true);
				                                    	}else{
				                                    		Ext.getCmp('processPD').getForm().findField('process_pd[second_half]').setDisabled(true);
				                                    		Ext.getCmp('processPD').getForm().findField('process_pd[second_half]').setVisible(false);
				                                    		
				                                    		if (Ext.getCmp('processPD').getForm().findField('process_pd[loans]').getValue()){
				                                    			Ext.getCmp('processPD').getForm().findField('process_pd[first_half]').reset();
					                                    		Ext.getCmp('processPD').getForm().findField('process_pd[second_half]').reset();
				                                    		} else{
					                                    		Ext.getCmp('processPD').getForm().findField('process_pd[first_half]').setDisabled(true);
																Ext.getCmp('processPD').getForm().findField('process_pd[first_half]').setVisible(false);
				                                    		}
				                                    	}
		                            				}
		                            			}
		                            		}
				                        },
				                        {
				                            xtype: 'checkbox',
				                            boxLabel: 'Payroll Deduction for Loans'
				                            ,name: 'process_pd[loans]'	
                        	                ,anchor:'100%'
                        	                ,submitValue: true
                        	                ,listeners:{
		                            			'check':{
		                            				scope:this
		                            				,fn:function(cc, bool){
				                                    	if (bool){
															if (!Ext.getCmp('processPD').getForm().findField('process_pd[savings]').getValue()){
																Ext.getCmp('processPD').getForm().findField('process_pd[first_half]').reset();
															}
															
				                                    		Ext.getCmp('processPD').getForm().findField('process_pd[first_half]').setDisabled(false);
				                                    		Ext.getCmp('processPD').getForm().findField('process_pd[first_half]').setVisible(true);
				                                    	}else{
															if (!Ext.getCmp('processPD').getForm().findField('process_pd[savings]').getValue()){
																Ext.getCmp('processPD').getForm().findField('process_pd[first_half]').setDisabled(true);
																Ext.getCmp('processPD').getForm().findField('process_pd[first_half]').setVisible(false);
															}
				                                    	}
		                            				}
		                            			}
		                            		}
				                        },
				                        {
				                            xtype: 'checkbox',
				                            boxLabel: 'Payroll Deduction for today'
				                            ,name: 'process_pd[today]'	
                        	                ,anchor:'100%'
                        	                ,submitValue: true
				                        }
				                    ]
				                }
				            ]
				        }
				    ]
				}
	        ]
	        
	        , hasSelected: function() {
    	    	var form = Ext.getCmp('processPD').getForm();
    	    	ch1 = form.findField('process_pd[savings]');
    	    	ch2 = form.findField('process_pd[loans]');
    	    	ch3 = form.findField('process_pd[today]');
    	    	
    	    	return (ch1.getValue() || ch2.getValue() || ch3.getValue());
    	    }
	};
};