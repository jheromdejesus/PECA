var rpt_capconbal = function(){
	return{
		xtype:'form'
		,id:'rpt_capconbal'
		,region: 'center'
		,anchor: '100%'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,buttons:[{
			text: 'Generate'
			,iconCls: 'icon_ext_generate'
		    ,handler : function(btn){
				var frm = Ext.getCmp('rpt_capconbal').getForm();
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
		    			url: '/report_capconbalancelist' 
						,method: 'POST'
						,form: Ext.get('frmDownload')
						,params: {'file_type': Ext.getCmp('rpt_capconbal').getForm().getValues().file_type
									,'amount_from':Ext.getCmp('rpt_capconbal').getForm().findField('amount_from').getValue()
									,'amount_to': Ext.getCmp('rpt_capconbal').getForm().findField('amount_to').getValue()
									,'report_date': Ext.getCmp('rpt_capconbal').getForm().getValues().report_date
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
								showExtInfoMsg()(opts.result.msg);
							}
						}
		        	});
		    	}
		    }
		}]
	    ,items: [{
	    	xtype: 'fieldset'
	    	,title: 'CapCon Balance'
	    	,layout: 'form'
            ,anchor: '70%'
            ,items: [{
                xtype: 'datefield'
                ,fieldLabel: 'Date'
                ,anchor: '50%'
                //,value: _TODAY
                ,name: 'report_date'
                ,required: true
                ,allowBlank: false
				,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
			}
			,{
				layout: 'column'
				,border: false
				,items: [
					{
						layout: 'form'
						,labelAlign: 'left'
						,border: false
						,hideBorders: false
						,labelWidth: 100
						,width: 280
						,items: [{
								xtype: 'moneyfield'
								,fieldLabel: 'Amount From'
								,name: 'amount_from'
								,maxLength: 16
								,anchor: '95%'
								,autoCreate: {tag: 'input', type: 'text', maxlength: '16'}
								,style: 'text-align: right'
								,allowBlank: false
								,required: true
								//,minValue: 0
								,allowNegative: false
								,validator: function(value1){
				    	            	var frm = Ext.getCmp('rpt_capconbal').getForm();
				    	        		var value2 = new String(frm.findField('amount_to').getValue());
										value1 = new String(value1);
										
				    	        		value1 = value1 == null?"": value1.replace(/,/gi,"");
										value2 = value2 == null?"": value2.replace(/,/gi,"");
										
										if (value1=='' || value1==null){
											return 'This field is required.';
										}
										
				    	        		if ((value1 != null && value1 != '') && (value2 != null && value2 != '')){
											if (parseFloat(value1) <= parseFloat(value2)){
												return true;
											} else{
												return 'Amount From should be lesser than or equal to Amount To.';
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
								xtype: 'moneyfield'
								,fieldLabel: 'To'
								,name: 'amount_to'
								,maxLength: 16
								,anchor: '95%'
								,autoCreate: {tag: 'input', type: 'text', maxlength: '16'}
								,style: 'text-align: right'
								,allowBlank: false
								,required: true
								//,minValue: 0
								,allowNegative: false
								,validator: function(value2){
				    	            	var frm = Ext.getCmp('rpt_capconbal').getForm();
				    	        		var value1 = new String(frm.findField('amount_from').getValue());
										value2 = new String(value2);
				    	        		
										value1 = value1 == null?"": value1.replace(/,/gi,"");
										value2 = value2 == null?"": value2.replace(/,/gi,"");
										
										if (value2=='' || value2==null){
											return 'This field is required.';
										}
										
				    	        		if ((value1 != null && value1 != '') && (value2 != null && value2 != '')){
											if (parseFloat(value1) <= parseFloat(value2)){
												return true;
											} else{
												return 'Amount To should be greater than or equal to Amount From.';
											}
				    	        		}else{
				    	        			return true;
				    	        		}
				    	        }
						}]
					}
			]}
            ,{
            	xtype: 'radiogroup'
            	,fieldLabel: 'Report Format'
            	,anchor: '70%'
                ,items: [
                    {boxLabel: 'PDF', name: 'file_type', inputValue: '2', checked: true}
                    ,{boxLabel: 'Excel', name: 'file_type', inputValue: '1'}
                ]
            }
			]
        }]
	};
};