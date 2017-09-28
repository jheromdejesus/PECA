var rpt_journal_prooflist = function(){
	return{
		xtype:'form'
		,id:'rpt_journal_prooflist'
		,anchor: '100%'
		,region: 'center'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,buttons:[{
			text: 'Generate'
			,iconCls: 'icon_ext_generate'
		    ,handler : function(btn){
				var frm = Ext.getCmp('rpt_journal_prooflist').getForm();
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
		    			url: '/report_journalentries' 
						,method: 'POST'
						,form: Ext.get('frmDownload')
						,params: {report_type: '1'
									,file_type: Ext.getCmp('rpt_journal_prooflist').getForm().getValues().file_type
									,from_date: Ext.getCmp('rpt_journal_prooflist').getForm().getValues().from_date
									,to_date: Ext.getCmp('rpt_journal_prooflist').getForm().getValues().to_date
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
	    	,title: 'Journal Entries Prooflist Report'
	    	,layout: 'form'
            ,anchor: '75%'
            ,items: [
              {
				layout: 'column'
					,border: false
					,items: [{
							layout: 'form'
							,labelAlign: 'left'
							,border: false
							//,anchor: '50%'
							,hideBorders: false
							,labelWidth: 100
							,width: 280
							,items: [{
				                xtype: 'datefield'
				                    ,fieldLabel: 'Date From'
				                    ,anchor: '90%'
				                    //,value: _TODAY
				                    ,name: 'from_date'
				                    ,required: true
									,maxLength: 10
				                    ,allowBlank: false
									,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
				                    ,validator: function(value1){
				    	            	var frm = Ext.getCmp('rpt_journal_prooflist').getForm();
				    	        		var value2 = frm.findField('to_date').value;
				    	        	
										if (value1=='' || value1==null){
											return 'This field is required.';
										}
										
				    	        		if ((value1 != null && value1 != '') && (value2 != null && value2 != '')){
											first = new Date(value1);
						                    second = new Date(value2);
											if (first.format('m/d/Y')=="NaN/NaN/NaN"){
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
				    	            /* ,listeners:{
				    	    			'blur':{
				    	    				scope:this
				    	    				,fn:function(form){
				    	                    	var frm = Ext.getCmp('rpt_journal_prooflist').getForm();
				    	                		frm.findField('to_date').beforeBlur();
				    	    				}
				    	    			}
				    	    		} */ 
				                }]
						}
						,{
							layout: 'form'
							,labelAlign: 'left'
							//,anchor: '50%'	
							,border: false
							,hideBorders: false
							,labelWidth: 50
							,width: 250
							,items: [{
				            	 xtype: 'datefield'
				                     ,fieldLabel: 'To'
				                     ,anchor: '80%'
				                    // ,value: _TODAY
				                     ,name: 'to_date'
				                     ,required: true
									 ,maxLength: 10
				                     ,allowBlank: false
									 ,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
				                     ,validator: function(value2){
				    	            	var frm = Ext.getCmp('rpt_journal_prooflist').getForm();
				    	        		var value1 = frm.findField('from_date').value;
				    	        		
										if (value2=='' || value2==null){
											return 'This field is required.';
										}
										
				    	        		if ((value1 != null && value1 != '') && (value2 != null && value2 != '')){
											first = new Date(value1);
						                    second = new Date(value2);
											
											if (second.format('m/d/Y')=="NaN/NaN/NaN"){
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
				    	            /* ,listeners:{
				    	    			'blur':{
				    	    				scope:this
				    	    				,fn:function(form){
				    	                    	var frm = Ext.getCmp('rpt_journal_prooflist').getForm();
				    	                		frm.findField('from_date').beforeBlur();
				    	    				}
				    	    			}
				    	    		} */ 
				                }]
						}
				]
			}
            ,{
            	xtype: 'radiogroup'
            	,fieldLabel: 'Report Format'
            	,anchor: '80%'
                ,items: [
                    {boxLabel: 'PDF', name: 'file_type', inputValue: '2', checked: true}
                    ,{boxLabel: 'Excel', name: 'file_type', inputValue: '1'}
                ]
            }]
        }]
	};
};

var rpt_journal_audit_trail = function(){
	return{
		xtype:'form'
		,id:'rpt_journal_audit_trail'
		,anchor: '100%'
		,region: 'center'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,buttons:[{
			text: 'Generate'
			,iconCls: 'icon_ext_generate'
		    ,handler : function(btn){
				var frm = Ext.getCmp('rpt_journal_audit_trail').getForm();
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
		    			url: '/report_journalentries' 
						,method: 'POST'
						,form: Ext.get('frmDownload')
						,params: {report_type: '2'
									,file_type: Ext.getCmp('rpt_journal_audit_trail').getForm().getValues().file_type
									,from_date: Ext.getCmp('rpt_journal_audit_trail').getForm().getValues().from_date
									,to_date: Ext.getCmp('rpt_journal_audit_trail').getForm().getValues().to_date
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
	    	,title: 'Journal Entries Audit Trail Report'
	    	,layout: 'form'
            ,anchor: '75%'
            ,items: [{
				layout: 'column'
					,border: false
					,items: [{
							layout: 'form'
							,labelAlign: 'left'
							,border: false
							//,anchor: '50%'
							,hideBorders: false
							,labelWidth: 100
							,width: 280
							,items: [{
				                xtype: 'datefield'
				                    ,fieldLabel: 'Date From'
				                    ,anchor: '90%'
				                    //,value: _TODAY
				                    ,name: 'from_date'
									,maxLength: 10
				                    ,required: true
				                    ,allowBlank: false
									,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
				                    ,validator: function(value1){
				    	            	var frm = Ext.getCmp('rpt_journal_audit_trail').getForm();
				    	        		var value2 = frm.findField('to_date').value;
				    	        		
										if (value1=='' || value1==null){
											return 'This field is required.';
										}
										
				    	        		if ((value1 != null && value1 != '') && (value2 != null && value2 != '')){
											first = new Date(value1);
						                    second = new Date(value2);
											if (first.format('m/d/Y')=="NaN/NaN/NaN"){
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
				    	            /* ,listeners:{
				    	    			'blur':{
				    	    				scope:this
				    	    				,fn:function(form){
				    	                    	var frm = Ext.getCmp('rpt_journal_audit_trail').getForm();
				    	                		frm.findField('to_date').validate();
				    	    				}
				    	    			}
				    	    		} */
				                }]
						}
						,{
							layout: 'form'
							,labelAlign: 'left'
							//,anchor: '50%'	
							,border: false
							,hideBorders: false
							,labelWidth: 50
							,width: 250
							,items: [{
				            	 xtype: 'datefield'
				                     ,fieldLabel: 'To'
				                     ,anchor: '80%'
				                    // ,value: _TODAY
									,maxLength: 10
				                     ,name: 'to_date'
				                     ,required: true
				                     ,allowBlank: false
									 ,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
				                     ,validator: function(value2){
				    	            	var frm = Ext.getCmp('rpt_journal_audit_trail').getForm();
				    	        		var value1 = frm.findField('from_date').value;
				    	        		
										if (value2=='' || value2==null){
											return 'This field is required.';
										}
										
				    	        		if ((value1 != null && value1 != '') && (value2 != null && value2 != '')){
											first = new Date(value1);
						                    second = new Date(value2);
											
											if (second.format('m/d/Y')=="NaN/NaN/NaN"){
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
				    	            /* ,listeners:{
				    	    			'blur':{
				    	    				scope:this
				    	    				,fn:function(form){
				    	                    	var frm = Ext.getCmp('rpt_journal_audit_trail').getForm();
				    	                		frm.findField('from_date').validate();
				    	    				}
				    	    			}
				    	    		} */
				                }]
						}
				]
			}
            ,{
            	xtype: 'radiogroup'
            	,fieldLabel: 'Report Format'
            	,anchor: '80%'
                ,items: [
                    {boxLabel: 'PDF', name: 'file_type', inputValue: '2', checked: true}
                    ,{boxLabel: 'Excel', name: 'file_type', inputValue: '1'}
                ]
            }]
        }]
	};
};

