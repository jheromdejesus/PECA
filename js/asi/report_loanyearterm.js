var rpt_loanyearterm = function(){
	return{
		xtype:'form'
		,id:'rpt_loanyearterm'
		,region: 'center'
		,anchor: '100%'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,buttons:[{
			text: 'Generate'
			,iconCls: 'icon_ext_generate'
		    ,handler : function(btn){
				var frm = Ext.getCmp('rpt_loanyearterm').getForm();
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
		    			url: '/report_loanyearterm' 
						,method: 'POST'
						,form: Ext.get('frmDownload')
						,params: {'file_type': Ext.getCmp('rpt_loanyearterm').getForm().getValues().file_type
									,'month': Ext.getCmp('rpt_loanyearterm').getForm().getValues().month
									,'year': Ext.getCmp('rpt_loanyearterm').getForm().getValues().year
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
	    	,title: 'List of the Loan Year Term Report'
	    	,layout: 'form'
            ,anchor: '70%'
            ,items: [
            {
				layout: 'column'
				,border: false
				,items: [{
						layout: 'form'
						,labelAlign: 'left'
						,border: false
						,hideBorders: false
						,labelWidth: 100
						,width: 297
						,items: [{
								xtype: 'combo'
								,anchor: '95%'
								,fieldLabel: 'Month'
								,id: 'rpt_loanyearterm_month'
								,hiddenName: 'month'
								,typeAhead: true
								,triggerAction: 'all'
								,lazyRender:true
								,mode: 'local'									
								,forceSelection: true
								,submitValue: false
								,emptyText: 'Please Select'
								,allowBlank: false
								,required: true
								,store: new Ext.data.ArrayStore({
									id: 0
									,fields: [
										'action_code'
										,'displayText'
									]
									,data: [
											  ['01', 'January'], ['02', 'February']
											, ['03', 'March'], ['04', 'April']
											, ['05', 'May'], ['06', 'June']
											, ['07', 'July'], ['08', 'August']
											, ['09', 'September'], ['10', 'October']
											, ['11', 'November'], ['12', 'December']
											]
								})
								,valueField: 'action_code'
								,displayField: 'displayText'
							}]
					}
					,{
						layout: 'form'
						,labelAlign: 'left'
						,border: false
						,hideBorders: false
						,labelWidth: 50
						,width: 205
						// ,items: [{
								// xtype: 'datefield'
								// ,fieldLabel: 'Year'
								// ,name: 'year'
								// ,maxLength: 10
								// ,anchor: '95%'
								// ,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
								// ,style: 'text-align: right'
								// ,allowBlank: false
								// ,format: 'Y'
								// ,required: true
								// ,invalidText: 'This is not a valid year - it must be in the format YYYY'
						// }]
						
						,items: [{
								xtype: 'combo'
								,anchor: '95%'
								,fieldLabel: 'Year'
								,hiddenName: 'year'
								,editable: false
								,typeAhead: true
								,triggerAction: 'all'
								,lazyRender:true
								,mode: 'local'									
								,forceSelection: true
								,submitValue: false
								,emptyText: 'Please Select'
								,allowBlank: false
								,required: true
								,store: new Ext.data.ArrayStore({
									id: 0
									,fields: [
										'action_code'
										,'displayText'
									]
									,data: getComboYear()
								})
								,valueField: 'action_code'
								,displayField: 'displayText'
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
            }]
        }]
	};
};