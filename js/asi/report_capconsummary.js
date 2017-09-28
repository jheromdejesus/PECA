var rpt_summary = function(){
	return{
		xtype:'form'
		,id:'rpt_summary'
		,region: 'center'
		,anchor: '100%'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,buttons:[{
			text: 'Generate'
			,iconCls: 'icon_ext_generate'
		    ,handler : function(btn){
				var frm = Ext.getCmp('rpt_summary').getForm();
		    	if(frm.isValid()){
		    		try {
		    		    Ext.destroy(Ext.get('frmDownload'));
		    		}catch(e) {}		    		
		    		if (!Ext.fly('frmDownload')) {
                        var frm = document.createElement('form');
                        frm.id = 'frmDownload';
                        frm.name = id;
                        frm.className = 'x-hidden';
                        document.body.appendChild(frm);
                    } 
		    		
		    		Ext.Ajax.request({
		    			url: '/report_summary' 
						,method: 'POST'
						,form: Ext.get('frmDownload')
						,params: {'file_type': Ext.getCmp('rpt_summary').getForm().getValues().file_type
									,'start_date': Ext.getCmp('rpt_summary').getForm().getValues().start_date
									,'end_date': Ext.getCmp('rpt_summary').getForm().getValues().end_date
									,auth:_AUTH_KEY}
						,isUpload: true
						,success: function(response, opts) {
							var obj = Ext.decode(response.responseText);
							if(obj.success){
								App.setAlert(true, obj.msg);
								
							}else{
								showExtInfoMsg(obj.msg);
							}
						}
						,failure: function(response, opts) {
							if (opts.result.error_code == 19){
								showExtInfoMsg(opts.result.msg);
							}
						}
		        	});
		    	}
		    }
		}]
	    ,items: [{
	    	xtype: 'fieldset'
	    	,title: 'Capital Contribution per Month'
	    	,layout: 'form'
            ,anchor: '70%'
            ,items: [{
				layout: 'column'
				,border: false
				,items: [{
						layout: 'form'
						,labelAlign: 'left'
						,border: false
						,hideBorders: false
						,labelWidth: 100
						,width: 280
						,items: [{
								xtype: 'datefield'
								,fieldLabel: 'Date From'
								,anchor: '95%'
								,maxLength: 10
								,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
								,style: 'text-align: right'
								,validationEvent: 'change'
								,name: 'start_date'
								,required: true
								,allowBlank: false
								,validator: function(value1){
				    	            	var frm = Ext.getCmp('rpt_summary').getForm();
				    	        		var value2 = frm.findField('end_date').value;
				    	        		
										if (value1=='' || value1==null){
											return 'This field is required.';
										}
										
				    	        		if ((value1 != null && value1 != '') && (value2 != null && value2 != '')){
											first = new Date(value1);
						                    second = new Date(value2);
												
												if (first.format('m/d/Y')!=value1){
													return 'This is not a valid date - it must be in the format MM/DD/YYYY';
												}else {
													if (first <= second){
														return true;
													} else{
														return 'The date should be lesser than or equal to date to.';
													}
												}	
				    	        		}else{
				    	        			return true;
				    	        		}
				    	        }
							}]
					}
					,{
						layout: 'form'
						,labelAlign: 'left'
						,border: false
						,hideBorders: false
						,labelWidth: 50
						,width: 230
						,items: [{
								xtype: 'datefield'
								,maxLength: 10
								,anchor: '95%'
								,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
								,style: 'text-align: right'
								,validationEvent: 'change'
								,name: 'end_date'
								,required: true
								,allowBlank: false
								,fieldLabel: 'To'
								,validator: function(value2){
				    	            	var frm = Ext.getCmp('rpt_summary').getForm();
				    	        		var value1 = frm.findField('start_date').value;
				    	        		
										if (value2=='' || value2==null){
											return 'This field is required.';
										}
										
				    	        		if ((value1 != null && value1 != '') && (value2 != null && value2 != '')){
											first = new Date(value1);
						                    second = new Date(value2);
											
											if (second.format('m/d/Y')!=value2){
													return 'This is not a valid date - it must be in the format MM/DD/YYYY';
											}else {
												if (first <= second){
													return true;				    	            			
												} else{
													return 'The date should be greater than or equal to date from.';
												}
											}												
				    	        		}else{
				    	        			return true;
				    	        		}
				    	    		
				    	        }
						}]
					}]
			}
			,{
            	xtype: 'radiogroup'
            	,fieldLabel: 'Report Format'
            	,anchor: '50%'
                ,items: [
                    {boxLabel: 'PDF', name: 'file_type', inputValue: '2', checked: true}
                    ,{boxLabel: 'Excel', name: 'file_type', inputValue: '1'}
                ]
            }
			]
        }]
	};
};