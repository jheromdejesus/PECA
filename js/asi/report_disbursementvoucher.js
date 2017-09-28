var rpt_disvoucher = function(){
	return{
		xtype:'form'
		,id:'rpt_disvoucher'
		,region: 'center'
		,anchor: '100%'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,buttons:[{
			text: 'Generate'
			,iconCls: 'icon_ext_generate'
		    ,handler : function(btn){
				var frm = Ext.getCmp('rpt_disvoucher').getForm();
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
		    			url: '/report_disbursementvoucher' 
						,method: 'POST'
						,form: Ext.get('frmDownload')
						,params: {file_type: Ext.getCmp('rpt_disvoucher').getForm().getValues().file_type
									,report_date: Ext.getCmp('rpt_disvoucher').getForm().getValues().report_date
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
	    	,title: 'Disbursement Voucher'
	    	,layout: 'form'
            ,anchor: '50%'
            ,items: [{
                xtype: 'datefield'
                ,fieldLabel: 'Date'
                ,anchor: '70%'
                ,value: _TODAY
                ,name: 'report_date'
				,maxLength: 10
                ,required: true
                ,allowBlank: false
				,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
				,invalidText: 'Date is not a valid date - it must be in the format MM/DD/YYYY'
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