//Ext.form.DateField.prototype.invalidText.style = "width: 135px";
/* var getInvalidText = function() {
	return {
		Ext.override(Ext.form.DateField, {
			invalidText: '<div class="x-form-invalid-msg" id="ext-gen582" style="width: 135px; display: block;">The date should be lesser than or equal to date to.</div>'
		});
	}
}
 */
var rpt_CSOI = function(){
	return{
		xtype:'form'
		,id:'rpt_CSOI'
		,anchor: '100%'
		,region: 'center'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,buttons:[{
			text: 'Generate'
			,iconCls: 'icon_ext_generate'
		    ,handler : function(btn){
				var frm = Ext.getCmp('rpt_CSOI').getForm();
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
		    			url: '/report_comparativeSOI' 
						,method: 'POST'
						,form: Ext.get('frmDownload')
						,params: {'file_type': Ext.getCmp('rpt_CSOI').getForm().getValues().file_type
									,'from_date': Ext.getCmp('rpt_CSOI').getForm().getValues().from_date
									,'to_date': Ext.getCmp('rpt_CSOI').getForm().getValues().to_date
			        				,auth:_AUTH_KEY}
						,isUpload: true
						,success: function(response, opts) {
							var obj = Ext.decode(response.responseText);
							if(obj.success){
								App.setAlert(true, obj.msg);
							}else{
								if (obj.error_code == 19){
									showExtInfoMsg(obj.msg);
								}
								else if (obj.error_code == 152){
									showExtErrorMsg(obj.msg);
								}else{
									showExtInfoMsg(obj.msg);
								}
							}
						}
						,failure: function(response, opts) {
							if (opts.result.error_code == 19){
								showExtInfoMsg(opts.result.msg);
							}
							else if (opts.result.error_code == 152){
								showExtErrorMsg(opts.result.msg);
							}
						}
		        	});
		    	}
		    }
		}]
	    ,items: [{
	    	xtype: 'fieldset'
	    	,title: 'Comparative Income Statement'
	    	,layout: 'form'
            ,anchor: '75%'
            ,items: [
             {
				layout: 'column'
					,border: false
					//,anchor: '100%'
					,width: 800
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
				                    ,name: 'from_date'
				                    ,required: true
				                    ,allowBlank: false
									,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
				                    ,validator: function(value1){
				    	            	var frm = Ext.getCmp('rpt_CSOI').getForm();
				    	        		var value2 = frm.findField('to_date').value;
				    	        		first = new Date(value1);
						                second = new Date(value2);
										
										if (value1=='' || value1==null){
											return 'This field is required.';
										}else {
											if (first.format('m/d/Y')=="NaN/NaN/NaN"){
												return 'This is not a valid date - it must be in the format MM/DD/YYYY';
											}
											else if (first.format('d')!=1){
												return 'Date should be the first day of the month.';
											}		
										}
										
				    	        		if ((value1 != null && value1 != '') && (value2 != null && value2 != '')){
												if (first.format('m/d/Y')=="NaN/NaN/NaN"){
													return 'This is not a valid date - it must be in the format MM/DD/YYYY';
												}else {
													if (first <= second){
														return true;
													} else{
														return 'The date should be lesser than or equal to date to.';
														//return '<div class="x-form-invalid-msg" id="ext-gen582" style="width: 135px; display: block;">The date should be lesser than or equal to date to.</div>';
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
				                     //,value: _TODAY
				                     ,name: 'to_date'
				                     ,required: true
				                     ,allowBlank: false
									 ,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
				                     ,validator: function(value2){
				    	            	var frm = Ext.getCmp('rpt_CSOI').getForm();
				    	        		var value1 = frm.findField('from_date').value;
				    	        		first = new Date(value1);
						                second = new Date(value2);
									
										if (value2=='' || value2==null){
											return 'This field is required.';
										}else {
											if (second.format('m/d/Y')=="NaN/NaN/NaN"){
												return 'This is not a valid date - it must be in the format MM/DD/YYYY';
											}
											else if (second.format('d')!=1){
												return 'Date should be the first day of the month.';
											}
										}	
										
				    	        		if ((value1 != null && value1 != '') && (value2 != null && value2 != '')){	
											if (second.format('m/d/Y')=="NaN/NaN/NaN"){
												return 'This is not a valid date - it must be in the format MM/DD/YYYY';
											}else {
												if (first <= second){
													return true;				    	            			
												} else{
													return 'The date should be greater than or equal to date from.';
													//return '<div class="x-form-invalid-msg" id="ext-gen582" style="width: 135px; display: block;">The date should be greater than or equal to date from.</div>';
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


var rpt_CSOC = function(){
	return{
		xtype:'form'
		,id:'rpt_CSOC'
		,anchor: '100%'
		,region: 'center'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,buttons:[{
			text: 'Generate'
			,iconCls: 'icon_ext_generate'
		    ,handler : function(btn){
				var frm = Ext.getCmp('rpt_CSOC').getForm();
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
		    			url: '/report_comparativeSOC' 
						,method: 'POST'
						,form: Ext.get('frmDownload')
						,params: {'file_type': Ext.getCmp('rpt_CSOC').getForm().getValues().file_type
									,'from_date': Ext.getCmp('rpt_CSOC').getForm().getValues().from_date
									,'to_date': Ext.getCmp('rpt_CSOC').getForm().getValues().to_date
			        				,auth:_AUTH_KEY}
						,isUpload: true
						,success: function(response, opts) {
							var obj = Ext.decode(response.responseText);
							if(obj.success){
								App.setAlert(true, obj.msg);
							}else{
								if (obj.error_code == 19){
									showExtInfoMsg(obj.msg);
								}
								else if (obj.error_code == 152){
									showExtErrorMsg(obj.msg);
								}else{
									showExtInfoMsg(obj.msg);
								}
							}
						}
						,failure: function(response, opts) {
							if (opts.result.error_code == 19){
								showExtInfoMsg(opts.result.msg);
							}
							else if (opts.result.error_code == 152){
								showExtErrorMsg(opts.result.msg);
							}
						}
		        	});
		    	}
		    }
		}]
	    ,items: [{
	    	xtype: 'fieldset'
	    	,title: 'Comparative Balance Sheet'
	    	,layout: 'form'
            ,anchor: '75%'
            ,items: [
             {
				layout: 'column'
				,border: false
				//,anchor: '100%'
				,width: 800
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
				                    ,name: 'from_date'
				                    ,required: true
				                    ,allowBlank: false
									,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
				                    ,validator: function(value1){
				    	            	var frm = Ext.getCmp('rpt_CSOC').getForm();
				    	        		var value2 = frm.findField('to_date').value;
				    	        		first = new Date(value1);
						                second = new Date(value2);
									
										if (value1=='' || value1==null){
											return 'This field is required.';
										}else {
											if (first.format('m/d/Y')=="NaN/NaN/NaN"){
												return 'This is not a valid date - it must be in the format MM/DD/YYYY';
											}
											else if (first.format('d')!=1){
												return 'Date should be the first day of the month.';
											}		
										}

				    	        		if ((value1 != null && value1 != '') && (value2 != null && value2 != '')){
												if (first.format('m/d/Y')=="NaN/NaN/NaN"){
													return 'This is not a valid date - it must be in the format MM/DD/YYYY';
												}else { 
													if (first <= second){
														return true;
													} else{
														return 'The date should be lesser than or equal to date to.';
														//return '<style="width: 135px">The date should be lesser than or equal to date to.</style>';
														//getInvalidText();
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
				                     //,value: _TODAY
				                     ,name: 'to_date'
				                     ,required: true
				                     ,allowBlank: false
									 ,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
				                     ,validator: function(value2){
				    	            	var frm = Ext.getCmp('rpt_CSOC').getForm();
				    	        		var value1 = frm.findField('from_date').value;
				    	        		first = new Date(value1);
						                second = new Date(value2);
										
										if (value2=='' || value2==null){
											return 'This field is required.';
										}else {
											if (second.format('m/d/Y')=="NaN/NaN/NaN"){
												return 'This is not a valid date - it must be in the format MM/DD/YYYY';
											}
											else if (second.format('d')!=1){
												return 'Date should be the first day of the month.';
											}
										}
							
				    	        		if ((value1 != null && value1 != '') && (value2 != null && value2 != '')){	
											if (second.format('m/d/Y')=="NaN/NaN/NaN"){
													return 'This is not a valid date - it must be in the format MM/DD/YYYY';
											}else {
												if (first <= second){
													return true;				    	            			
												} else{
													return 'The date should be greater than or equal to date from.';
													//return '<style="width: 135px">The date should be greater than or equal to date from.</style>';
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