var rpt_actsummaryposted = function(){
	return{
		xtype:'form'
		,id:'rpt_actsummaryposted'
		,region: 'center'
		,anchor: '100%'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,buttons:[{
			text: 'Generate'
			,iconCls: 'icon_ext_generate'
		    ,handler : function(btn){
				var frm = Ext.getCmp('rpt_actsummaryposted').getForm();
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
		    			url: '/report_actsummary' 
						,method: 'POST'
						,form: Ext.get('frmDownload')
						,params: {'file_type': Ext.getCmp('rpt_actsummaryposted').getForm().getValues().file_type
									,'report_date': Ext.getCmp('rpt_actsummaryposted').getForm().getValues().report_date
									,'account_no': Ext.getCmp('rpt_actsummaryposted').getForm().findField('account_id').getValue()
									,'report_type': '1'
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
							var obj = Ext.decode(response.responseText);
							App.setAlert(false, obj.msg);
						}
		        	});
		    	}
		    }
		}]
	    ,items: [{
	    	xtype: 'fieldset'
	    	,title: 'Posted Account Summary Report for the Current Period'
	    	,layout: 'form'
			,labelWidth: 140
            ,anchor: '70%'
            ,items: [{
                xtype: 'datefield'
                ,fieldLabel: 'Date'
                ,anchor: '90%'
                ,value: _TODAY
                ,name: 'report_date'
                ,required: true
                ,allowBlank: false
				,maxLength: 10
				,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
            },{
				xtype: 'combo'
				,hiddenName: 'account_id'
				,fieldLabel: 'Account Number'
				,anchor: '90%'
				,editable: true
				,typeAhead: true
				,triggerAction: 'all'
				,lazyRender:true
				,store: pecaDataStores.accountStoreASR
				,mode: 'local'
				,valueField: 'account_no'
				,displayField: 'account_no_name'									
				,forceSelection: true
				,submitValue: false
				,emptyText: 'Please Select'
				,required: true
                ,allowBlank: false
				,autoCreate: {tag: 'input', type: 'text', maxlength: '50'}
			}
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

var rpt_actsummaryunposted = function(){
	return{
		xtype:'form'
		,id:'rpt_actsummaryunposted'
		,region: 'center'
		,anchor: '100%'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,buttons:[{
			text: 'Generate'
			,iconCls: 'icon_ext_generate'
		    ,handler : function(btn){
				var frm = Ext.getCmp('rpt_actsummaryunposted').getForm();
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
		    			url: '/report_actsummary' 
						,method: 'POST'
						,form: Ext.get('frmDownload')
						,params: {'file_type': Ext.getCmp('rpt_actsummaryunposted').getForm().getValues().file_type
									,'report_date': Ext.getCmp('rpt_actsummaryunposted').getForm().getValues().report_date
									,'account_no': Ext.getCmp('rpt_actsummaryunposted').getForm().findField('account_id').getValue()
									,'report_type': '2'
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
							var obj = Ext.decode(response.responseText);
							App.setAlert(false, obj.msg);
						}
		        	});
		    	}
		    }
		}]
	    ,items: [{
	    	xtype: 'fieldset'
	    	,title: 'Unposted Account Summary Report for the Current Period'
	    	,layout: 'form'
			,labelWidth: 140
            ,anchor: '70%'
            ,items: [{
                xtype: 'datefield'
                ,fieldLabel: 'Date'
                ,anchor: '90%'
                ,value: _TODAY
                ,name: 'report_date'
                ,required: true
                ,allowBlank: false
				,maxLength: 10
				,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
            },{
				xtype: 'combo'
				,hiddenName: 'account_id'
				,fieldLabel: 'Account Number'
				,editable: true
				,anchor: '90%'
				,typeAhead: true
				,triggerAction: 'all'
				,lazyRender:true
				,store: pecaDataStores.accountStoreASR
				,mode: 'local'
				,valueField: 'account_no'
				,displayField: 'account_no_name'									
				,forceSelection: true
				,submitValue: false
				,emptyText: 'Please Select'
				,required: true
                ,allowBlank: false
				,autoCreate: {tag: 'input', type: 'text', maxlength: '50'}
			},{
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