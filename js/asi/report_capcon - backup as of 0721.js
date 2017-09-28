var rpt_capcon_prooflist = function(){
	return{
		xtype:'form'
		,id:'rpt_capcon_prooflist'
		,region: 'center'
		,anchor: '100%'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,buttons:[{
			text: 'Generate'
			,iconCls: 'icon_ext_generate'
		    ,handler : function(btn){
				var frm = Ext.getCmp('rpt_capcon_prooflist').getForm();
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
		    			url: '/report_capitalcontribution' 
						,method: 'POST'
						,form: Ext.get('frmDownload')
						,params: {report_type: '1'
									,file_type: Ext.getCmp('rpt_capcon_prooflist').getForm().getValues().file_type
									,report_date: Ext.getCmp('rpt_capcon_prooflist').getForm().getValues().report_date
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
	    	,title: 'Capital Contribution Prooflist'
	    	,layout: 'form'
            //,width: 600
			,anchor: '50%'
            ,items: [{
                xtype: 'datefield'
                ,fieldLabel: 'Date'
                ,anchor: '70%'
                ,value: _TODAY
                ,name: 'report_date'
                ,required: true
                ,allowBlank: false
				,maxLength: 10
				,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
            }
            ,{
            	xtype: 'radiogroup'
            	,fieldLabel: 'Report Format'
            	,anchor: '70%'
                ,items: [
                    {boxLabel: 'PDF', name: 'file_type', inputValue: '2', checked: true}
                    ,{boxLabel: 'Excel', name: 'file_type', inputValue: '1'}
                ]
            }]
        }]
	};
};

var rpt_capcon_audit_trail = function(){
	return{
		xtype:'form'
		,id:'rpt_capcon_audit_trail'
		,anchor: '100%'
		,region: 'center'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,buttons:[{
			text: 'Generate'
			,iconCls: 'icon_ext_generate'
		    ,handler : function(btn){
				var frm = Ext.getCmp('rpt_capcon_audit_trail').getForm();
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
		    			url: '/report_capitalcontribution' 
						,method: 'POST'
						,form: Ext.get('frmDownload')
						,params: {report_type: '2'
									,file_type: Ext.getCmp('rpt_capcon_audit_trail').getForm().getValues().file_type
									,report_date: Ext.getCmp('rpt_capcon_audit_trail').getForm().getValues().report_date
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
	    	,title: 'Capital Contribution Audit Trail Report'
	    	,layout: 'form'
            //,width: 600
			,anchor: '50%'
            ,items: [{
                xtype: 'datefield'
                ,fieldLabel: 'Date'
                ,anchor: '70%'
                ,value: _TODAY
                ,name: 'report_date'
                ,required: true
                ,allowBlank: false
				,maxLength: 10
				,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
            }
            ,{
            	xtype: 'radiogroup'
            	,fieldLabel: 'Report Format'
            	,anchor: '70%'
                ,items: [
                    {boxLabel: 'PDF', name: 'file_type', inputValue: '2', checked: true}
                    ,{boxLabel: 'Excel', name: 'file_type', inputValue: '1'}
                ]
            }]
        }]
	};
};

var rpt_capcon_bmb = function(){
	return{
		xtype:'form'
		,id:'rpt_capcon_bmb'
		,anchor: '100%'
		,region: 'center'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,buttons:[{
			text: 'Generate'
			,iconCls: 'icon_ext_generate'
		    ,handler : function(btn){
				var frm = Ext.getCmp('rpt_capcon_bmb').getForm();
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
		    			url: '/report_capitalcontribution' 
						,method: 'POST'
						,form: Ext.get('frmDownload')
						,params: {report_type: '3'
									,file_type: Ext.getCmp('rpt_capcon_bmb').getForm().getValues().file_type
									,from_date: Ext.getCmp('rpt_capcon_bmb').getForm().getValues().from_date
									,to_date: Ext.getCmp('rpt_capcon_bmb').getForm().getValues().to_date
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
	    	,title: 'Capital Contribution BMB Report'
	    	,layout: 'form'
            ,width: 600
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
									,maxLength: 10
									,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
				                    //,value: _TODAY
				                    ,name: 'from_date'
				                    ,required: true
				                    ,allowBlank: false
									//,invalidText: 'This is not a valid date - it must be in the format MM/DD/YYYY'
				                    ,validator: function(value1){
				    	            	var frm = Ext.getCmp('rpt_capcon_bmb').getForm();
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
				    	                    	var frm = Ext.getCmp('rpt_capcon_bmb').getForm();
				    	                		frm.findField('from_date').validate();
				    	    				}
				    	    			}
				    	    		}  */
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
									 ,maxLength: 10
									 ,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
				                     //,value: _TODAY
				                     ,name: 'to_date'
				                     ,required: true
				                     ,allowBlank: false
									 //,invalidText: 'This is not a valid date - it must be in the format MM/DD/YYYY'
				                     ,validator: function(value2){
				    	            	var frm = Ext.getCmp('rpt_capcon_bmb').getForm();
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
				    	            /*  ,listeners:{
				    	    			'blur':{
				    	    				scope:this
				    	    				,fn:function(form){
				    	                    	var frm = Ext.getCmp('rpt_capcon_bmb').getForm();
				    	                		frm.findField('to_date').validate();
				    	    				}
				    	    			}
				    	    		}  */
				                }]
						}
				]
			}
            ,{
            	xtype: 'radiogroup'
            	,fieldLabel: 'Report Format'
            	,anchor: '70%'
                ,items: [
                    {boxLabel: 'PDF', name: 'file_type', inputValue: '2', checked: true}
                    ,{boxLabel: 'Excel', name: 'file_type', inputValue: '1'}
                ]
            }]
        }]
	};
};