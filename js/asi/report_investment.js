var rpt_inv_prooflist = function(){
	return{
		xtype:'form'
		,id:'rpt_inv_prooflist'
		,anchor: '100%'
		,region: 'center'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,buttons:[{
			text: 'Generate'
			,iconCls: 'icon_ext_generate'
		    ,handler : function(btn){
				var frm = Ext.getCmp('rpt_inv_prooflist').getForm();
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
		    			url: '/report_investment' 
						,method: 'POST'
						,form: Ext.get('frmDownload')
						,params: {report_type: '1'
									,file_type: Ext.getCmp('rpt_inv_prooflist').getForm().getValues().file_type
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
	    	,title: 'Investment Prooflist Record'
	    	,layout: 'form'
            ,anchor: '50%'
            ,items: [{
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

var rpt_inv_audit_trail = function(){
	return{
		xtype:'form'
		,id:'rpt_inv_audit_trail'
		,anchor: '100%'
		,region: 'center'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,buttons:[{
			text: 'Generate'
			,iconCls: 'icon_ext_generate'
		    ,handler : function(btn){
				var frm = Ext.getCmp('rpt_inv_audit_trail').getForm();
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
		    			url: '/report_investment' 
						,method: 'POST'
						,form: Ext.get('frmDownload')
						,params: {report_type: '2'
									,file_type: Ext.getCmp('rpt_inv_audit_trail').getForm().getValues().file_type
									,report_date: Ext.getCmp('rpt_inv_audit_trail').getForm().getValues().report_date
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
		    	,title: 'Investment Audit Trail Report'
		    	,layout: 'form'
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
					,invalidText: 'Date is not a valid date - it must be in the format MM/DD/YYYY'
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

var rpt_inv_report = function(){

	/* date = new Date(_TODAY);
	end_day = date.getLastDateOfMonth().getDate();
	year = (date.getFullYear());
	month = (date.getMonth()+1);
	end_date = month +'/'+ end_day + '/' + year; */
	
	return{
		xtype:'form'
		,id:'rpt_inv_report'
		,anchor: '100%'
		,region: 'center'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,buttons:[{
			text: 'Generate'
			,iconCls: 'icon_ext_generate'
		    ,handler : function(btn){
				var frm = Ext.getCmp('rpt_inv_report').getForm();
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
		    			url: '/report_investment' 
						,method: 'POST'
						,form: Ext.get('frmDownload')
						,params: {report_type: '3'
									,file_type: Ext.getCmp('rpt_inv_report').getForm().getValues().file_type
									,report_date: Ext.getCmp('rpt_inv_report').getForm().getValues().report_date
			        				,auth:_AUTH_KEY}
						,isUpload: true
						,success: function(response, opts) {
							var obj = Ext.decode(response.responseText);
							if(obj.success){
								App.setAlert(true, obj.msg);
							}else{
								if (obj.error_code == 32){
									showExtErrorMsg(obj.msg);
								}else {
									showExtInfoMsg(obj.msg);
								}
							}
						}
						,failure: function(response, opts) {
							if (opts.result.error_code == 19){
								showExtInfoMsg(opts.result.msg);
							}else {
								showExtErrorMsg(opts.result.msg);
							}
						}
		        	});
		    	}
		    }
		}]
	    ,items: [{
	    	xtype: 'fieldset'
		    	,title: 'Investment Report'
		    	,layout: 'form'
	            ,anchor: '50%'
	            ,items: [{
					 layout: 'form'
					,labelWidth: 120
					,items: [{
						  xtype: 'datefield'
						,fieldLabel: 'Date (Month End)'
						,anchor: '75%'
						,maxLength: 10
						,value: new Date(_TODAY).getLastDateOfMonth().format('m/d/Y')
						,name: 'report_date'
						,required: true
						,allowBlank: false
						,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
						,invalidText: 'Date is not a valid date - it must be in the format MM/DD/YYYY'
					}]    
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

var rpt_inv_mat_prooflist = function(){
	return{
		xtype:'form'
		,id:'rpt_inv_mat_prooflist'
		,anchor: '100%'
		,region: 'center'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,buttons:[{
			text: 'Generate'
			,iconCls: 'icon_ext_generate'
		    ,handler : function(btn){
				var frm = Ext.getCmp('rpt_inv_mat_prooflist').getForm();
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
		    			url: '/report_investment' 
						,method: 'POST'
						,form: Ext.get('frmDownload')
						,params: {report_type: '4'
									,file_type: Ext.getCmp('rpt_inv_mat_prooflist').getForm().getValues().file_type
									,report_date: Ext.getCmp('rpt_inv_mat_prooflist').getForm().getValues().report_date
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
		    	,title: 'Investment Maturity Prooflist Report'
		    	,layout: 'form'
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
					,invalidText: 'Date is not a valid date - it must be in the format MM/DD/YYYY'
	            }
	            ,{
	            	xtype: 'radiogroup'
	            	,fieldLabel: 'Report Format'
	            	,anchor: '85%'
	                ,items: [
	                    {boxLabel: 'PDF', name: 'file_type', inputValue: '2', checked: true}
	                    ,{boxLabel: 'Excel', name: 'file_type', inputValue: '1'}
	                ]
	            }]
	        }]
	};
};
