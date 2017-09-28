var rpt_amortizationschedule = function(loan_no_field_name){
	return{
		xtype: 'panel'
		,border: false
		,layout: 'anchor'
		,bodyStyle: 'background:transparent;'
		,padding: 20
		,items:[
			{
				xtype: 'box',
				autoEl: {cn: '<div>Generate Amortization Schedule for this Loan Year</div><br/>'}
			}
			,{ 
				width: 100
				,height: 35
				,id: 'generateAmortizationScheduleButton'
				,xtype: 'button'
				,text: 'Generate'
				,iconCls: 'icon_ext_generate'
				,handler : function(btn){
					if(Ext.getCmp(loan_no_field_name).getValue() === "") {
						showExtInfoMsg("Loading data. Please wait.");
						return;
					}
					
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
						url: '/report_amortizationschedule' 
						,method: 'POST'
						,form: Ext.get('frmDownload')
						,params: {
							'loan_no' : Ext.getCmp(loan_no_field_name).getValue() 
							,auth:_AUTH_KEY 
						}
						,isUpload: true
						,success: function(response, opts) {
							var obj = Ext.decode(response.responseText);
							if(obj.success){
								App.setAlert(true, "Downloading.");								
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
		]
	};
};