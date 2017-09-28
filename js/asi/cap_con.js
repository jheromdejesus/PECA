//Let's pretend we rendered our grid-columns with meta-data from our ORM framework.
var capconColumns =  new Ext.grid.ColumnModel( 
	[
		{id: 'transaction_code', header: 'Transaction No.', width: 100, sortable: true, dataIndex: 'capcon[transaction_no]', align:'right'}
		,{header: 'Employee ID', width: 75, sortable: true, dataIndex: 'capcon[employee_id]', align:'right'}
		,{header: 'Last Name', width: 125, sortable: true, dataIndex: 'last_name'}
		,{header: 'First Name', width: 125, sortable: true, dataIndex: 'first_name'}
		,{header: 'Transaction Date', width: 125, sortable: true, dataIndex: 'capcon[transaction_date]', align: 'center'}
		,{header: 'Transaction Type', width: 150, sortable: true, dataIndex: 'capcon[transaction_type]'}	
	]
);

var capcon_summary = new Ext.ux.grid.GridSummary();

var capconDtlColumns =  new Ext.grid.ColumnModel( 
	[
		{id:'id', hidden: true, dataIndex: 'id'}
		,{header: 'Charge Code', width: 20, sortable: true, dataIndex: 'transaction_code', summaryRenderer: function(v, params, data){
            return 'Total Charges'; }}
		,{header: 'Description', width: 20, sortable: true, dataIndex: 'transaction_description'}
		,{header: 'Amount', width: 20, sortable: true, dataIndex: 'amount', align: 'right', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000.00');}, summaryType: 'sum'}
	]
);

var capconDtlProxy = new Ext.data.HttpProxy({
	url: '/capital_transaction/readDtl'
	,listeners:{
		'beforeload':{
			scope:this
			,fn:function(dataproxy,params ){
				params.transaction_no = Ext.getCmp('capconDetail').getForm().findField('capcon[transaction_no]').getValue();
				//added by asi 365 start
				var trans_code = Ext.getCmp('capconDetail').getForm().findField('capcon[transaction_code]').getValue();
				var index = Ext.getCmp('trans_type').getStore().findExact('transaction_code',trans_code);
				var rec = Ext.getCmp('trans_type').getStore().getAt(index);
				
				//checking if record not defined
				if(rec != null){
					if(rec.get('transcode[bank_transfer]') == 'true'){
						Ext.getCmp('capconDetail').getForm().findField('capcon[bank_transfer]').enable();
					}else{
						Ext.getCmp('capconDetail').getForm().findField('capcon[bank_transfer]').setValue('false');
						Ext.getCmp('capconDetail').getForm().findField('capcon[bank_transfer]').disable();
					}
				}
				
				/*if(trans_code == 'DDEP') {
					Ext.getCmp('capconDetail').getForm().findField('capcon[bank_transfer]').setValue('false');
					Ext.getCmp('capconDetail').getForm().findField('capcon[bank_transfer]').disable();
				} */
				//added by asi 365 end
			} 
		}
	}
});

var capconDetail = function(){
	return {
		xtype:'form'
		,id:'capconDetail'
		,region:'center'
		,title: 'Details'
//		,hidden:true
		,anchor: '100%'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,reader: pecaReaders.capconReader
		,record: null
		,buttons:[{
			text: 'Print OR'
			,hidden: true
			,iconCls: 'icon_ext_print'
		    ,handler : function(btn){
				var frm = Ext.getCmp('capconDetail').getForm();
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
		    			url: '/utilities/printORCapCon' 
						,method: 'POST'
						,form: Ext.get('frmDownload')
						,params: {transaction_no: Ext.getCmp('capconDetail').getForm().findField('capcon[transaction_no]').getValue()
			        				,auth:_AUTH_KEY}
						,isUpload: true
						,success: function(response, opts) {
							var obj = Ext.decode(response.responseText);
							if(obj.success){
								App.setAlert(true, obj.msg);
							}else{
								App.setAlert(false, obj.msg);
							}
						}
						,failure: function(response, opts) {
							var obj = Ext.decode(response.responseText);
							App.setAlert(false, obj.msg);
						}
		        	});
		    	}
		    }
		},{
			text: 'Cancel'
			,iconCls: 'icon_ext_cancel'
		    ,handler : function(btn){
//				Ext.getCmp('capconDetail').hide();
//				Ext.getCmp('capconList').show();
				Ext.getCmp('transactionCardBody').layout.setActiveItem('pnlCapcon');
				Ext.getCmp('capconDetail').getForm().reset();
				Ext.getCmp('capconDetail').getForm().findField('frm_mode').setValue(FORM_MODE_LIST);
				pecaDataStores.capconStore.reload();
		    }
		},{
			text: 'Delete'
			,iconCls: 'icon_ext_del'				
			,handler: function(){
				var frm = Ext.getCmp('capconDetail').getForm();
				frm.onDelete();
		    }
		},{
			text:'Save'
			,iconCls: 'icon_ext_save'				
		    ,handler: function(){
		    	var frm = Ext.getCmp('capconDetail').getForm();
		    	if(frm.isValid()){
		    		if (frm.isModeNew()) {
		    			frm.onSave(frm);
		    		} else {
		    		   	frm.onUpdate(frm);
		            }
		    	}        	
		    }
		},{
			text: 'Preview'
			,iconCls: 'icon_ext_preview'
		    ,handler : function(btn){
				var frm = Ext.getCmp('capconDetail').getForm();
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
		    			url: '/printable_capcon' 
						,method: 'POST'
						,form: Ext.get('frmDownload')
						,params: {transaction_code: Ext.getCmp('capconDetail').getForm().findField('capcon[transaction_code]').getValue()
									,employee_id: Ext.getCmp('capconDetail').getForm().findField('capcon[employee_id]').getValue()
									,transaction_date: Ext.getCmp('capconDetail').getForm().findField('capcon[transaction_date]').getValue()
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
							if (opts.result.error_code == 19 || opts.result.error_code == 152){
								showExtInfoMsg(opts.result.msg);
							}
						}
		        	});
		    	}
		    }
		}]
		,items: [{
		    xtype: 'hidden'
		    ,name: 'frm_mode'
		    ,value: FORM_MODE_LIST
		    ,submitValue: false
		    ,listeners: {'change':{fn: function(obj,value){
            }}}
		},{
            labelWidth: 100
            ,labelAlign: 'left'
            ,layout: 'form'
            ,border: false
            ,items: [{
                xtype: 'textfield'
                ,fieldLabel: 'Transaction No'
                ,name: 'capcon[transaction_no]'
                ,readOnly: true
                ,cls: 'x-item-disabled'
                ,style: 'text-align: right'
            }]
        },{
            layout: 'column'
            ,border: false
           	,anchor:'100%'
            ,items: [{
                labelWidth: 100
                ,labelAlign: 'left'
                ,layout: 'form'
				,width: 250
                ,border: false
                ,items: [{
                    xtype: 'textfield'
                    ,fieldLabel: 'Employee'
                    ,name: 'capcon[employee_id]'
                	,anchor:'98%'
	                ,allowBlank: false
	                ,required: true
	                ,maxLength: 8
	                ,emptyText: 'ID'
	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '8'}
					,enableKeyEvents: true
					,style: 'text-align: right'
						,listeners: {
						specialkey: function(txt,evt){
							if (evt.getKey() == evt.ENTER) {
								pecaDataStores.capconEmployeeStore.load({params: {start:0,limit:MAX_PAGE_SIZE}});
								capcon_employeeListWin().show();
							}
						},
						keypress: function(txt,evt){
							if (isNaN(String.fromCharCode(evt.getCharCode())) && !evt.isNavKeyPress() && evt.getKey() != evt.BACKSPACE 
									&& evt.getKey() != evt.DELETE){
								evt.preventDefault();
							}else{
								if(evt.getKey() == 32){
									evt.preventDefault();
								}
							}
						}
					}
                }]
            },{
                labelWidth: 1
                ,labelAlign: 'left'
                ,layout: 'form'
                ,border: false
               	,width: 150
                ,items: [{
                    xtype: 'textfield'
                    ,fieldLabel: ''
                	,name: 'last_name'
                	,anchor:'98%'
	                ,maxLength: 30
	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
                	,submitValue: false
                	,emptyText: 'Last Name'
					,enableKeyEvents: true
					,style: 'text-align: left'
					,listeners: {
						specialkey: function(txt,evt){
							if (evt.getKey() == evt.ENTER) {
								pecaDataStores.capconEmployeeStore.load({params: {start:0,limit:MAX_PAGE_SIZE}});
								capcon_employeeListWin().show();
							}
						}
					}
                }]
            },{
                labelWidth: 1
                ,labelAlign: 'left'
                ,layout: 'form'
                ,border: false
           		,width: 150
                ,items: [{
                    xtype: 'textfield'
                    ,fieldLabel: ''
                	,name: 'first_name'
                	,anchor:'98%'
	                ,maxLength: 30
	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
                	,submitValue: false
                	,emptyText: 'First Name'
					,enableKeyEvents: true
					,style: 'text-align: left'
					,listeners: {
						specialkey: function(txt,evt){
							if (evt.getKey() == evt.ENTER) {
								pecaDataStores.capconEmployeeStore.load({params: {start:0,limit:MAX_PAGE_SIZE}});
								capcon_employeeListWin().show();
							}
						}
					}
                }]
            },{
                xtype: 'button'
                ,text: 'Search'
                ,width: 75
       			,iconCls: 'icon_ext_search'				
            	,handler: function(){            		
	        		pecaDataStores.capconEmployeeStore.load({params: {start:0,limit:MAX_PAGE_SIZE}});
	        		capcon_employeeListWin().show();
			    }
            }]
        },{
            layout: 'column'
            ,labelWidth: 115
            ,border: false
            ,items: [{
                labelAlign: 'left'
                ,layout: 'form'
                ,columnWidth: 0.5
                ,xtype:'fieldset'
                ,autoScroll: true
				,title: 'Capital Contribution Details'
				,bodyStyle:{'padding':'10px'}
            	,height: 250
                ,items: [new Ext.form.ComboBox({
	                fieldLabel: 'Transaction Type'
	                ,hiddenName: 'capcon[transaction_code]'
	        	    ,typeAhead: true
	        	    ,triggerAction: 'all'
	        	    ,lazyRender:true
	        	    ,store: pecaDataStores.transcodeCCStore
	        	    ,mode: 'local'
	        	    ,valueField: 'transaction_code'
	        	    ,displayField: 'transaction_description'
	        	    ,anchor: '100%'
	        	    ,forceSelection: true
	        	    ,submitValue: false
	        	    ,emptyText: 'Please Select'
	        	    ,required: true
	        	    ,allowBlank: false
					,id: 'trans_type'
    	    		,listeners:{
	        			change:{
	        				scope:this
	        				,fn:function(combo, newVal, oldVal) {
								
	        					var index = combo.getStore().findExact('transaction_code',newVal);
	        					var rec = combo.getStore().getAt(index);
	        					if(rec.get('transcode[bank_transfer]') == 'true'){
	        						Ext.getCmp('capconDetail').getForm().findField('capcon[bank_transfer]').enable();
	        					}else{
	        						Ext.getCmp('capconDetail').getForm().findField('capcon[bank_transfer]').setValue('false');
	        						Ext.getCmp('capconDetail').getForm().findField('capcon[bank_transfer]').disable();
	        					}
	        					
	        					//for closing
	        					if (newVal == 'CLSE'){
	        						Ext.getCmp('capconDetail').getForm().findField('capcon[transaction_amount]').setReadOnly(true);
	        						//retrieve employee balance here
	        						Ext.Ajax.request({
	        							url: '/capital_transaction/showCapConBalance/' + Ext.getCmp('capconDetail').getForm().findField('capcon[employee_id]').getValue()
	        							,params: {auth:_AUTH_KEY}   
	        							,success: function(response, opts) {
	        								var capcon_bal = Ext.decode(response.responseText);
	        								Ext.getCmp('capconDetail').getForm().findField('capcon[transaction_amount]').setValue(capcon_bal);
	        							}
	        							,failure: function(response, opts) {
//	        								var obj = Ext.decode(response.responseText);
//	        								showExtInfoMsg( obj.msg);
	        							}
	        						});
	        						Ext.getCmp('capconDetail').getForm().findField('capcon[transaction_amount]').focus(true,true);
	        					} else{
	        						Ext.getCmp('capconDetail').getForm().findField('capcon[transaction_amount]').setReadOnly(false);
	        					}
	        				}
	        			}
						,select: {
							fn: function(combo, record, index){
									if ((combo.getValue() == 'WDWL' || combo.getValue() == 'CLSE') && Ext.getCmp('capconDetail').getForm().findField('frm_mode').getValue() == FORM_MODE_UPDATE){
										Ext.getCmp('capconDetail').buttons[4].show();
									}
									else{
										Ext.getCmp('capconDetail').buttons[4].hide();
									}
								}
						}
                	}
	        	}),{
                	xtype:'checkbox'
	                ,boxLabel: 'Bank Transfer'                
	                ,id: 'cc_bank_transfer'
	                ,name: 'capcon[bank_transfer]'	
	                ,anchor:'95%'
	                ,submitValue: false
                },{
                	fieldLabel: 'OR No'
                    ,name: 'capcon[or_no]'
                	,xtype: 'numberfield'
                    ,anchor:'65%'
                	,readOnly: true
                    ,cls: 'x-item-disabled'
                    ,style: 'text-align: right'
					,hidden: true
                },{
                	xtype: 'textfield'
                	,fieldLabel: 'OR Date'
                	,name: 'capcon[or_date]'
                	,anchor: '65%'
            		,readOnly: true
                    ,cls: 'x-item-disabled'
                    ,style: 'text-align: right'
					,hidden: true
                },{
                	xtype: 'textfield'
    				,fieldLabel: 'Transaction Date'
    				,name: 'capcon[transaction_date]'	
    				,anchor:'65%'
    				,allowBlank: false
    				,required: true
    				,maxLength: 10
    				,disableKeyFilter: true
    				,value: _TODAY
					,readOnly: true
					,style: 'text-align: right'
    				,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
                },{
                	xtype:'moneyfield'
	            	,fieldLabel: "Amount"
	                ,name: 'capcon[transaction_amount]'
	                ,anchor:'65%'
	                ,allowBlank: false
	                ,required: true
	                ,maxLength: 16
	                ,maxValue: 9999999999.99
	                // ,minValue: 0
	                ,value: 0.00
	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '13'}
                },{
                    xtype: 'textarea'
                    ,fieldLabel: 'Remarks'
                    ,name: 'capcon[remarks]'
                	,maxLength: 50
                	,height: 35
                    ,anchor: '75%'
					,enableKeyEvents: true
                }]
            },{
            	columnWidth: .50
				,layout: 'fit'
				,xtype:'fieldset'	
				,title: 'Other Charges'
				,bodyStyle:{'padding':'10px'}	
	            ,defaultType: 'grid'
	            ,height: 250
				,items: [capconDtlList()]
            }]
        }]
	    ,isModeNew: function() {
	    	return (Ext.getCmp('capconDetail').getForm().findField('frm_mode').getValue() == FORM_MODE_NEW);
	    }
	    ,setModeNew: function() {
	    	Ext.getCmp('capconDetail').getForm().findField('frm_mode').setValue(FORM_MODE_NEW);
	    	//Ext.getCmp('capconDetail').buttons[0].setVisible(false);  //print OR button
			Ext.getCmp('capconDetail').buttons[1].setVisible(true);  //cancel button
	    	Ext.getCmp('capconDetail').buttons[2].setVisible(false);  //delete button
	    	Ext.getCmp('capconDetail').buttons[3].setVisible(true);  //save button
	    	Ext.getCmp('capconDetail').buttons[4].setVisible(false);	 //preview button
			
	    	Ext.getCmp('capconDetail').getForm().findField('capcon[transaction_no]').setVisible(false);
	    	//Ext.getCmp('capconDetail').getForm().findField('capcon[or_no]').setVisible(false);
	    	//Ext.getCmp('capconDetail').getForm().findField('capcon[or_date]').setVisible(false);
	    	
	    	Ext.getCmp('capconDetail').getForm().findField('capcon[transaction_no]').setValue('');
	    	Ext.getCmp('capconDetail').getForm().findField('capcon[transaction_amount]').setValue('0.00');
	    	Ext.getCmp('capconDetail').getForm().findField('capcon[transaction_date]').setValue(_TODAY);
	    	
	    	Ext.getCmp('capconDetail').getForm().findField('capcon[employee_id]').focus('',250);
	    	Ext.getCmp('capconDetail').getForm().findField('capcon[employee_id]').setReadOnly(false);
			Ext.getCmp('capconDetail').getForm().findField('capcon[employee_id]').removeClass('x-item-disabled');
		}
		,setModeUpdate: function() {
			Ext.getCmp('capconDetail').getForm().findField('frm_mode').setValue(FORM_MODE_UPDATE);
			//Ext.getCmp('capconDetail').buttons[0].setVisible(true);  //print OR button
			Ext.getCmp('capconDetail').buttons[1].setVisible(true);  //cancel button
	    	Ext.getCmp('capconDetail').buttons[2].setVisible(true);  //delete button
	    	Ext.getCmp('capconDetail').buttons[3].setVisible(true);  //save button
			if(Ext.getCmp('capconDetail').getForm().findField('capcon[transaction_code]').getValue()=='WDWL' || Ext.getCmp('capconDetail').getForm().findField('capcon[transaction_code]').getValue()=='CLSE'){
				Ext.getCmp('capconDetail').buttons[4].setVisible(true); //preview button
			}
	    	else {
				Ext.getCmp('capconDetail').buttons[4].setVisible(false);
			}
	    	//can't update record
			if(_PERMISSION[119]==0){
				Ext.getCmp('capconDetail').buttons[3].setDisabled(true);	
			}
			//can't delete record
			if(_PERMISSION[25]==0){
				Ext.getCmp('capconDetail').buttons[2].setDisabled(true);	
			}
	    	
	    	Ext.getCmp('capconDetail').getForm().findField('capcon[transaction_no]').setVisible(true);
			Ext.getCmp('capconDetail').getForm().findField('capcon[employee_id]').focus(false,250);
	    }
		,onSave: function(frm){
        	frm.submit({
    			url: '/capital_transaction/addHdr' 
    			,method: 'POST'
    			,params: {'capcon[bank_transfer]': Ext.getCmp('cc_bank_transfer').getValue() ? 'Y' : 'N'
            		,auth:_AUTH_KEY, 'user': _USER_ID}
    			,waitMsg: 'Creating Data...'
    			,success: function(form, action) {
        			//showExtInfoMsg(action.result.msg);
    				showExtInfoMsg( action.result.msg);
    				frm.setModeUpdate();
    				Ext.getCmp('capconDetail').getForm().findField('capcon[transaction_no]').setValue(action.result.transaction_no);
    				Ext.getCmp('capconDetail').getForm().findField('capcon[or_no]').setValue(action.result.or_no);
    				Ext.getCmp('capconDetail').getForm().findField('capcon[or_date]').setValue(action.result.or_date);
    				pecaDataStores.capconDtlStore.load({params: {
	        			transaction_no: Ext.getCmp('capconDetail').getForm().findField('capcon[transaction_no]').getValue()
	        			,start:0, limit:MAX_PAGE_SIZE}});
					Ext.getCmp('capconDetail').getForm().load({
				    	url: '/capital_transaction/show'
				    	,params: {'capcon[transaction_no]':(action.result.transaction_no)
							,auth:_AUTH_KEY}
				    	,method: 'POST'
				    	,waitMsgTarget: true
				    	,success: function(form, action) {
				    		pecaDataStores.capconDtlStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
							/*var resp = Ext.decode(action.response.responseText).data[0];
							if(resp.with_or=="Y"){
								Ext.getCmp('capconDetail').getForm().findField('capcon[or_no]').setVisible(true);
								Ext.getCmp('capconDetail').getForm().findField('capcon[or_date]').setVisible(true);	
							}*/
				    	}
					});
					
    			}
    			,failure: function(form, action) {
    				if(action.result.error_code != ''){
    					//20111118 commented by ASI466 because this(prompt) is misleading to the user 
						//showExtErrorMsg( action.result.msg);
						showExtInfoMsg( action.result.msg);
    				}else{
	    				Ext.Msg.confirm('Confirm Action',action.result.msg,function(btn) {
	    					if(btn=='yes'){
	    						form.onUpdate(form);
	    					}
	    				});
    				}
    			}	
    		});
		}
		,onUpdate: function(frm){
			frm.submit({
    			url: '/capital_transaction/updateHdr' 
    			,method: 'POST'
    			,waitMsg: 'Updating Data...'
    			,params: {'capcon[bank_transfer]': Ext.getCmp('cc_bank_transfer').getValue() ? 'Y' : 'N'
        			,auth:_AUTH_KEY, 'user': _USER_ID	
    			}
    			,success: function(form, action) {
    				showExtInfoMsg( action.result.msg);
        			frm.setModeUpdate();
        			pecaDataStores.capconDtlStore.load({params: {
	        			transaction_no: Ext.getCmp('capconDetail').getForm().findField('capcon[transaction_no]').getValue()
	        			,start:0, limit:MAX_PAGE_SIZE}});
    			
					/*if(action.result.with_or=='1'){
						if(action.result.new_or){
							Ext.getCmp('capconDetail').getForm().findField('capcon[or_no]').setValue(action.result.or_no);
							Ext.getCmp('capconDetail').getForm().findField('capcon[or_date]').setValue(action.result.or_date);
						}
						Ext.getCmp('capconDetail').getForm().findField('capcon[or_no]').setVisible(true);
						Ext.getCmp('capconDetail').getForm().findField('capcon[or_date]').setVisible(true);	
					}
					else{
						Ext.getCmp('capconDetail').getForm().findField('capcon[or_no]').setVisible(false);
						Ext.getCmp('capconDetail').getForm().findField('capcon[or_date]').setVisible(false);	
						Ext.getCmp('capconDetail').getForm().findField('capcon[or_no]').setValue('');
						Ext.getCmp('capconDetail').getForm().findField('capcon[or_date]').setValue('');	
					}*/
				}
    			,failure: function(form, action) {
				
					//20111118 commented by ASI466 because this(prompt) is misleading to the user 
    				//showExtErrorMsg( action.result.msg);
					showExtInfoMsg( action.result.msg);
    			}	
    		});
		},onDelete: function(){
			Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
				if(btn=='yes') {
					Ext.getCmp('capconDetail').getForm().submit({
						url: '/capital_transaction/deleteHdr' 
						,method: 'POST'
						,params: {auth:_AUTH_KEY, 'capcon[modified_by]': _USER_ID}
						,clientValidation: false
						,waitMsg: 'Deleting Data...'
						,success: function(form, action) {
							showExtInfoMsg( action.result.msg);
			    			Ext.getCmp('capconDetail').setModeNew();
//			    			Ext.getCmp('capconDetail').hide();
			    			Ext.getCmp('capconDetail').getForm().reset();
//							Ext.getCmp('capconList').show();
			    			Ext.getCmp('transactionCardBody').layout.setActiveItem('pnlCapcon');
			    	        //pecaDataStores.capconStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
							if (pecaDataStores.capconStore.getCount() % MAX_PAGE_SIZE == 1){
								var page = pecaDataStores.capconStore.getTotalCount() - MAX_PAGE_SIZE - 1;
								pecaDataStores.capconStore.load({params: {start:page<0?0:page, limit:MAX_PAGE_SIZE}});
							} else{
								pecaDataStores.capconStore.reload();
							}
						}
						,failure: function(form, action) {
							showExtErrorMsg( action.result.msg);
						}	
					});
				}
			});
		}
	};
};
				
					
var capconList = function(){
	return {
		xtype: 'grid'
		,id: 'capconList'
		,titlebar: false
		,store: pecaDataStores.capconStore
		,region: 'center'
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
//		,width: 860
		,height: 420
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit: true
			,scrollOffset:13
		}
		,cm: capconColumns
		
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					
					var rec = grid.getStore().getAt(row);
//					Ext.getCmp('capconList').hide();
					pecaDataStores.transcodeCCStore.load();
//					Ext.getCmp('capconDetail').show();
					Ext.getCmp('transactionCardBody').layout.setActiveItem('pnlCapconDetail');
					Ext.getCmp('capconDetail').getForm().setModeUpdate();
					Ext.getCmp('capconDetail').getForm().load({
				    	url: '/capital_transaction/show'
				    	,params: {'capcon[transaction_no]':(rec.get('capcon[transaction_no]'))
							,auth:_AUTH_KEY}
				    	,method: 'POST'
				    	,waitMsgTarget: true
				    	,success: function(form, action) {
				    		pecaDataStores.capconDtlStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
							var resp = Ext.decode(action.response.responseText).data[0];
							/*if(resp.with_or!="Y"){
								Ext.getCmp('capconDetail').getForm().findField('capcon[or_no]').setVisible(false);
								Ext.getCmp('capconDetail').getForm().findField('capcon[or_date]').setVisible(false);	
							}
							else{
								Ext.getCmp('capconDetail').getForm().findField('capcon[or_no]').setVisible(true);
								Ext.getCmp('capconDetail').getForm().findField('capcon[or_date]').setVisible(true);
							}*/
							
							if(resp.transaction_code=="WDWL" || resp.transaction_code=="CLSE"){
								Ext.getCmp('capconDetail').buttons[4].show();
							}
							else{
								Ext.getCmp('capconDetail').buttons[4].hide();
							}
				    	}
					});
				}
			}
			,'render':{
				scope:this
				,fn:function(grid){
					//search textfield and button
					if(_PERMISSION[52]==0){
						Ext.getCmp('id_transNo').setDisabled(true);
						Ext.getCmp('capconSearchID').setDisabled(true);	
					}else{
						Ext.getCmp('id_transNo').setDisabled(false);
						Ext.getCmp('capconSearchID').setDisabled(false);
						pecaDataStores.capconStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
					}
					//new button
					if(_PERMISSION[0]==0){
						Ext.getCmp('capconNewID').setDisabled(true);	
					}else{
						Ext.getCmp('capconNewID').setDisabled(false);
					}
					//delete button
					if(_PERMISSION[25]==0){
						Ext.getCmp('capconDeleteID').setDisabled(true);	
					}else{
						Ext.getCmp('capconDeleteID').setDisabled(false);
					}
				}
			}
		}
		,tbar:[{
			name : 'transNo'
			,id: 'id_transNo' 
			,fieldLabel: 'Transaction No.'	
        	,xtype: 'textfield'
        	,anchor: '25%'
        	,emptyText: 'Transaction No'
    		,maxLength: 10
            ,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
			,enableKeyEvents: true
    		,style: 'text-align: right'
			,listeners: {
				specialkey: function(txt,evt){
					if (evt.getKey() == evt.ENTER) {
						pecaDataStores.capconStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});						
					}
				},
				keypress: function(txt,evt){
					if (isNaN(String.fromCharCode(evt.getCharCode())) && !evt.isNavKeyPress() && evt.getKey() != evt.BACKSPACE 
							&& evt.getKey() != evt.DELETE){
						evt.preventDefault();
					}else{
						if(evt.getKey() == 32){
							evt.preventDefault();
						}
					}
				}
			}
		},'-'
		,{
			text:'Search'
			,id: 'capconSearchID'
			,tooltip:'Search Capital Contribution Transaction'
			,iconCls: 'icon_ext_search'
			,scope:this			
			,handler: function(){
				pecaDataStores.capconStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});	
			}
		},'-'
		,{
			text:'New'
			,id: 'capconNewID'
			,tooltip:'Add a Capital Contribution'
			,iconCls: 'icon_ext_add'
			,scope:this
			,handler:function(btn) {
//				Ext.getCmp('capconList').hide();
				Ext.getCmp('capconDetail').getForm().reset();
//				Ext.getCmp('capconDetail').show();
				Ext.getCmp('transactionCardBody').layout.setActiveItem('pnlCapconDetail');
				capconDetail().setModeNew();
				pecaDataStores.transcodeCCStore.load();
				pecaDataStores.capconDtlStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
			}
		},'-'
		,{
			text:'Delete'
			,id: 'capconDeleteID'
			,tooltip:'Delete Selected Capital Contribution'
			,iconCls: 'icon_ext_del'
			,scope:this
			,handler:function(btn) {
				var index = Ext.getCmp('capconList').getSelectionModel().getSelected();
		        if (!index) {
		        	showExtInfoMsg( "Please select capcon transaction to delete.");
		            return false;
		        }
		        Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
					if(btn=='yes') {
				        Ext.Ajax.request({
				        	url: '/capital_transaction/deleteHdr' 
							,method: 'POST'
							,params: {'capcon[transaction_no]':index.data.transaction_no
				        				,auth:_AUTH_KEY, 'capcon[modified_by]': _USER_ID}
							,waitMsg: 'Deleting Data...'
							,success: function(response, opts) {
								var obj = Ext.decode(response.responseText);
								if(obj.success){
									showExtInfoMsg( obj.msg);
									//pecaDataStores.capconStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
									if (pecaDataStores.capconStore.getCount() % MAX_PAGE_SIZE == 1){
										var page = pecaDataStores.capconStore.getTotalCount() - MAX_PAGE_SIZE - 1;
										pecaDataStores.capconStore.load({params: {start:page<0?0:page, limit:MAX_PAGE_SIZE}});
									} else{
										pecaDataStores.capconStore.reload();
									}
								}else{
									showExtErrorMsg( obj.msg);
								}
							}
							,failure: function(response, opts) {
								var obj = Ext.decode(response.responseText);
								showExtErrorMsg( obj.msg);
							}
			        	});
					}
		        });
			}
		}]
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.capconStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: 'No records found'
	    })
	};
};

var capcon_employeeList = function(){
	return {
		xtype: 'grid'
		,id: 'capcon_employeeList'
		,titlebar: false
		,store: pecaDataStores.capconEmployeeStore
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 425
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit:true
			,scrollOffset:0
		}
		,cm: employeeColumns
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
					Ext.getCmp('capconDetail').getForm().findField('capcon[employee_id]').setValue(rec.get('employee_id'));
					Ext.getCmp('capconDetail').getForm().findField('last_name').setValue(rec.get('last_name'));
					Ext.getCmp('capconDetail').getForm().findField('first_name').setValue(rec.get('first_name'));
					
					var code = Ext.getCmp('capconDetail').getForm().findField('capcon[transaction_code]').getValue();
					//for closing
					if (code == 'CLSE'){
						Ext.getCmp('capconDetail').getForm().findField('capcon[transaction_amount]').setReadOnly(true);
						//retrieve employee balance here
						Ext.Ajax.request({
							url: '/capital_transaction/showCapConBalance/' + Ext.getCmp('capconDetail').getForm().findField('capcon[employee_id]').getValue()
							,params: {auth:_AUTH_KEY}   
							,success: function(response, opts) {
								var capcon_bal = Ext.decode(response.responseText);
								Ext.getCmp('capconDetail').getForm().findField('capcon[transaction_amount]').setValue(capcon_bal);
							}
							,failure: function(response, opts) {
//								var obj = Ext.decode(response.responseText);
//								showExtErrorMsg( obj.msg);
							}
						});
						Ext.getCmp('capconDetail').getForm().findField('capcon[transaction_amount]').focus(true,true);
					} else{
						Ext.getCmp('capconDetail').getForm().findField('capcon[transaction_amount]').setReadOnly(false);
					}
					
					Ext.getCmp('capcon_employeeListWin').close.defer(1,Ext.getCmp('capcon_employeeListWin'));
				}
			}
		}
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.capconEmployeeStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: 'No records found'
	    })
	};
};

var capcon_employeeListWin = function(){
	return new Ext.Window({
		id: 'capcon_employeeListWin'
		,title: 'Employee List'
		,frame: true
		,layout: 'form'
		,width: 600
		,plain: true
		,modal: true
		,resizable: false
		,closable: false
		,constrainHeader: true
		,bodyStyle:{"padding":"5px"}
		,loadMask: true	
		,items:[ capcon_employeeList() ]
        ,buttons:[{
 			text: 'Cancel'
 			,iconCls: 'icon_ext_cancel'
 		    ,handler : function(btn){
 				Ext.getCmp('capcon_employeeListWin').close();				
 		    }
 		}]
	});
};



var capconDtlList = function(){
	return {
		xtype: 'grid'
		,id: 'capconDtlList'
		,titlebar: false
		,store: pecaDataStores.capconDtlStore
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 150
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit: true
			//,scrollOffset: 0
		}
		,cm: capconDtlColumns
		,plugins: [capcon_summary]
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.capconDtlStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};