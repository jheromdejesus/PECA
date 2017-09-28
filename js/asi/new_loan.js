var newloanColumns =  new Ext.grid.ColumnModel( 
	[
		{id: 'loan_no', header: 'Loan No.', align: 'right', sortable: true, dataIndex: 'newloan[loan_no]'}
		,{header: 'Employee ID', sortable: true, dataIndex: 'newloan[employee_id]'}
		,{header: 'Last Name', sortable: true, dataIndex: 'last_name'}
		,{header: 'First Name', sortable: true, dataIndex: 'first_name'}
		,{header: 'Loan Date', sortable: true, dataIndex: 'newloan[loan_date]'}
		,{header: 'Loan Description', sortable: true, dataIndex: 'newloan[loan_description]'}	
	]
);

var newloanOtherChargesColumns =  new Ext.grid.ColumnModel( 
	[
		{id:'id', hidden: true, dataIndex: 'id'}
		,{header: 'Charge Code', width: 20, sortable: true, dataIndex: 'transaction_code'}
		,{header: 'Description', width: 20, sortable: true, dataIndex: 'transaction_description'}
		,{header: 'Amount', width: 20, sortable: true, dataIndex: 'amount', align: 'right', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000.00');}}
	]
);

var newloanRLColumns =  new Ext.grid.ColumnModel( 
	[
		{id: 'loan_no', header: 'Loan No.', width: 20, sortable: true, dataIndex: 'loan_no'}
		,{header: 'Balance', width: 20, sortable: true, dataIndex: 'balance', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000.00');}}
		,{header: 'Loan Date', width: 20, sortable: true, dataIndex: 'loan_date', renderer:function(value,rec){
			return formatDate(value);}}
		,{header: 'Principal', width: 20, sortable: true, dataIndex: 'principal', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000.00');}}
		,{header: 'Term', width: 20, sortable: true, dataIndex: 'term'}
		,{header: 'Interest Rate', width: 20, sortable: true, dataIndex: 'interest_rate'}	
	]
);

var newLoanCoMakerColumns =  new Ext.grid.ColumnModel( 
	[
		{id:'employee_id', header: 'Employee ID', width: 15, sortable: true, dataIndex: 'employee_id'}
		,{header: 'Last Name', width: 25, sortable: true, dataIndex: 'last_name'}
		,{header: 'First Name', width: 25, sortable: true, dataIndex: 'first_name'}
	]
);

var newloanOtherChargesProxy = new Ext.data.HttpProxy({
	api: {
	    read    : '/loan/readCharges',
	    create  : '/loan/addCharges',
	    update  : '/loan/updateCharge',
	    destroy : '/loan/deleteCharge'
	}
	,listeners:{
		'beforeload':{
			scope:this
			,fn:function(dataproxy,params ){
				params.loan_no = Ext.getCmp('newloanDetail').getForm().findField('newloan[loan_no]').getValue();
			}
		}
		,'write':{
			scope:this
			,fn:function(proxy, action, result, res, rs){
				Ext.getCmp('newloanDetail').getForm().findField('newloan[service_fee_amount]').setValue(res.raw.total_charges);
				Ext.getCmp('newloanDetail').getForm().findField('newloan[service_fee_amount]').focus(true, 250);
				Ext.getCmp('newloanOTAmount').focus(true, 250);
				newloanDetail().computeInterestRate();
			}
		}
		,'exception':{
			scope:this
			,fn:function(proxy, type, action, options, res){
				if(type == 'response'){
					pecaDataStores.newloanOtherChargesStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
					if((Ext.decode(res.responseText).success))
						Ext.getCmp('newloanDetail').getForm().findField('newloan[service_fee_amount]').setValue(Ext.decode(res.responseText).total_charges);
						Ext.getCmp('newloanDetail').getForm().findField('newloan[service_fee_amount]').focus(true, 250);
						Ext.getCmp('newloanOTAmount').focus(true, 250);
						newloanDetail().computeInterestRate();
				}
			}
		}
	}
});

var newloanOtherChargesWriter = new Ext.data.JsonWriter({
    encode: true
    ,writeAllFields: true
    ,listfull: true
});

var newloanCoMakerProxy = new Ext.data.HttpProxy({
	api: {
	    read    : '/loan/readComaker',
	    create  : '/loan/addComaker',
	    update  : '/loan/updateComaker',
	    destroy : '/loan/deleteComaker'
	}
	,listeners:{
		'beforeload':{
			scope:this
			,fn:function(dataproxy,params ){
				params.loan_no = Ext.getCmp('newloanDetail').getForm().findField('newloan[loan_no]').getValue();
			}
		}
		,'beforewrite':{
			scope:this
			,fn:function( proxy, action, rs, params  ){
				params.loan_no = Ext.getCmp('newloanDetail').getForm().findField('newloan[loan_no]').getValue();
				params.loan_code = Ext.getCmp('newloanDetail').getForm().findField('newloan[loan_code]').getValue();
				params.employee_id = Ext.getCmp('newloanDetail').getForm().findField('newloan[employee_id]').getValue();
			}
		}
		,'exception':{
			scope:this
			,fn:function(proxy, type, action, options, res){
				if(type == 'response'){				 
					if(!newloanDetail().isModeNew()){
        				pecaDataStores.newloanCoMakerStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
        			}
				}
			}
		}
		,'write':{
			scope:this
			,fn:function(proxy, action, result, res, rs){
				pecaDataStores.newloanCoMakerStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
			}
		}
	}
});

var newloanCoMakerWriter = new Ext.data.JsonWriter({
    encode: true
    ,writeAllFields: true
    ,listfull: true
});

var newloanList = function(){
	return {
		xtype: 'grid'
		,id: 'newloanList'
		,titlebar: false
		,store: pecaDataStores.newloanStore
		,region: 'center'
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 425
		,anchor: '100%'
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit:true
			,scrollOffset:0
		}
		,cm: newloanColumns
		
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
					pecaDataStores.newloanLTStore.load({params:{'employee_id':rec.get('newloan[employee_id]')}});					
					pecaDataStores.supplierStore.load();

					Ext.getCmp('newloanDetail').getForm().setModeUpdate();
					Ext.getCmp('newloanList').hide();
					Ext.getCmp('newloanDetail').show();
					
					Ext.getCmp('newloanDetail').getForm().load({
				    	url: '/loan/showLoan'
				    	,params: {'loan_no':(rec.get('newloan[loan_no]'))
									,auth:_AUTH_KEY}
				    	,method: 'POST'
				    	,waitMsgTarget: true
				    	,success: function(response, opts) {
				    		pecaDataStores.newloanOTCmboxStore.load(
				    				{params:{'transaction_code':Ext.getCmp('newloanDetail').getForm().findField('newloan[loan_code]').getValue()}});
				    		pecaDataStores.newloanOtherChargesStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
				    		pecaDataStores.newloanCoMakerStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
				    		if(Ext.getCmp('newloanDetail').getForm().findField('newloan[restructure_no]').getValue() != ''){
				    			Ext.getCmp('chkboxRestructure').setValue(true);
				    		}
							
							var resp = Ext.decode(opts.response.responseText).data[0];
							if (resp.pension_cb == 'Y'){
								Ext.getCmp('newloanDetail').getForm().findField('newloan[pension]').setDisabled(false);
							} else{
								Ext.getCmp('newloanDetail').getForm().findField('newloan[pension]').setDisabled(true);
							}
							
							/*if (resp.comaker_cb == 'Y'){
								Ext.getCmp('newloanCoMakerSearchBtn').setDisabled(true);
							} else{
								Ext.getCmp('newloanCoMakerSearchBtn').setDisabled(false);
							}*/
				    	}
					});
					var frm = Ext.getCmp('newloanDetail').getForm();
					frm.findField('last_name').setValue(rec.get('newloan[last_name]'));
					frm.findField('first_name').setValue(rec.get('newloan[first_name]'));
				}
			}
			,'render':{
				scope:this
				,fn:function(grid){
					//search textfield and button
					if(_PERMISSION[61]==0){
						Ext.getCmp('newloan_loan_no').setDisabled(true);
						Ext.getCmp('newloanSearchID').setDisabled(true);	
					}else{
						Ext.getCmp('newloan_loan_no').setDisabled(false);
						Ext.getCmp('newloanSearchID').setDisabled(false);
						pecaDataStores.newloanStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
					}
					//new button
					if(_PERMISSION[13]==0){
						Ext.getCmp('newloanNewID').setDisabled(true);	
					}else{
						Ext.getCmp('newloanNewID').setDisabled(false);
					}
					//delete button
					if(_PERMISSION[37]==0){
						Ext.getCmp('newloanDeleteID').setDisabled(true);	
					}else{
						Ext.getCmp('newloanDeleteID').setDisabled(false);
					}
				}
			}
		}
		,tbar:[{
			name : 'loan_no'
			,fieldLabel: 'Loan No.'	
        	,emptyText: 'Loan No'
			,id: 'newloan_loan_no'
        	,xtype: 'textfield'
        	,anchor: '25%'
    		,maxLength: 10
            ,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
			,enableKeyEvents: true
    		,style: 'text-align: right'
			,listeners: {
				specialkey: function(frm,evt){
					if (evt.getKey() == evt.ENTER) {
						pecaDataStores.newloanStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});						
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
		},'-',{
			text:'Search'
			,id: 'newloanSearchID'
			,tooltip:'Search Loan No'
			,iconCls: 'icon_ext_search'
			,scope:this			
			,handler: function(){
				pecaDataStores.newloanStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});	
			}
		},'-'
		,{
			text:'New'
			,id: 'newloanNewID'
			,tooltip:'Add a New Loan'
			,iconCls: 'icon_ext_add'
			,scope:this
			,handler:function(btn) {
				Ext.getCmp('newloanList').hide();
				Ext.getCmp('newloanDetail').show();
				Ext.getCmp('newloanDetail').getForm().reset();
				pecaDataStores.supplierStore.load();
				newloanDetail().setModeNew();
			}
		},'-'
		,{
			text:'Delete'
			,id: 'newloanDeleteID'
			,tooltip:'Delete Selected Loan No.'
			,iconCls: 'icon_ext_del'
			,scope:this
			,handler:function(btn) {
				var index = Ext.getCmp('newloanList').getSelectionModel().getSelected();
		        if (!index) {
		        	showExtInfoMsg( "Please select a loan to delete.");
		            return false;
		        }
		        Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
					if(btn=='yes') {
				        Ext.Ajax.request({
				        	url: '/loan/deleteLoan' 
							,method: 'POST'
							,params: {'newloan[loan_no]':index.data.loan_no
				        				,auth:_AUTH_KEY, 'newloan[modified_by]': _USER_ID}
							,waitMsg: 'Deleting Data...'
							,success: function(response, opts) {
								var obj = Ext.decode(response.responseText);
								if(obj.success){
									showExtInfoMsg( obj.msg);
									//pecaDataStores.newloanStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
									if (pecaDataStores.newloanStore.getCount() % MAX_PAGE_SIZE == 1){
										var page = pecaDataStores.newloanStore.getTotalCount() - MAX_PAGE_SIZE - 1;
										pecaDataStores.newloanStore.load({params: {start:page<0?0:page, limit:MAX_PAGE_SIZE}});
									} else{
										pecaDataStores.newloanStore.reload();
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
	        ,store: pecaDataStores.newloanStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: 'No records found'
	    })
	};
};

var newloanDetail = function(){
	return {
		xtype:'form'
		,id:'newloanDetail'
		,region:'center'
		,title: 'Details'
		,hidden:true
		,autoscroll: true
		,boxMinWidth: 840
		,anchor: '100%'
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,reader: pecaReaders.newloanReader
		,buttons:[{
			text: 'Cancel'
			,iconCls: 'icon_ext_cancel'
		    ,handler : function(btn){
				Ext.getCmp('newloanDetail').hide();
				Ext.getCmp('newloanDetail').getForm().reset();
				Ext.getCmp('newloanList').show();
				pecaDataStores.newloanStore.reload();
		    }
		},{
			text: 'Delete'
			,iconCls: 'icon_ext_del'
			,handler: function(){
				var frm = Ext.getCmp('newloanDetail').getForm();
				frm.onDelete(frm);
		    }
		},{
			text:'Save'
			,iconCls: 'icon_ext_save'
		    ,handler: function(){
				var frm = Ext.getCmp('newloanDetail').getForm();
				if( Ext.getCmp('chkboxRestructure').getValue() == false ){
					frm.findField('newloan[restructure_no]').allowBlank = true;
					frm.findField('newloan[restructure_amount]').allowBlank = true;
				}
				else{
					frm.findField('newloan[restructure_no]').allowBlank = false;
					frm.findField('newloan[restructure_amount]').allowBlank = false;
				}
				
		    	if(frm.isValid()){
		    		var frm = Ext.getCmp('newloanDetail').getForm();
		    		var index = pecaDataStores.newloanLTStore.findExact('loan_code',frm.findField('newloan[loan_code]').getValue());
					var rec = pecaDataStores.newloanLTStore.getAt(index);
					
		    		/*if( Ext.getCmp('newloan_comaker').getValue() == false){
		    			if( rec.get('guarantor') != pecaDataStores.newloanCoMakerStore.getCount()){
		    				showExtErrorMsg('Loan Type applied requires ' + rec.get('guarantor') + ' Co-Maker/s.');
		    				return;
		    			}		    				
		    		}*/
					
		    		if( Ext.getCmp('newloan_pension').getValue() == true){
		    			if( Ext.getCmp('newloanDetail').getForm().findField('newloan[pension]').getValue() == "" || 
							Ext.getCmp('newloanDetail').getForm().findField('newloan[pension]').getValue() == null){
		    				showExtErrorMsg('Loan Type requires Pension Amount.');
		    				return;
		    			}		    				
		    		}
		    		
		    		
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
				var frm = Ext.getCmp('newloanDetail').getForm();
				
				if( Ext.getCmp('chkboxRestructure').getValue() == false ){
					frm.findField('newloan[restructure_no]').allowBlank = true;
					frm.findField('newloan[restructure_amount]').allowBlank = true;
				}
				else{
					frm.findField('newloan[restructure_no]').allowBlank = false;
					frm.findField('newloan[restructure_amount]').allowBlank = false;
				}
				
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
		    			url: '/printable_loan' 
						,method: 'POST'
						,form: Ext.get('frmDownload')
						,params: {loan_no: Ext.getCmp('newloanDetail').getForm().findField('newloan[loan_no]').getValue()
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
		}]
		,items: [{
			xtype: 'fieldset'
			,anchor: '98%'
		    ,layout: 'form'
		    ,bodyStyle:{'padding-top':'10px'}	
		    ,items: [{
			    xtype: 'hidden'
				    ,name: 'frm_mode'
				    ,value: FORM_MODE_NEW
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
		                ,fieldLabel: 'Loan No'
		                ,name: 'newloan[loan_no]'
		                ,readOnly: true
		                ,cls: 'x-item-disabled'
		            }]
		        }
				,{
		    	layout: 'column'
	            ,border: false
	            ,items: [{
	                labelWidth: 100
	                ,labelAlign: 'left'
	                ,layout: 'form'
					,width: 250
	                ,border: false
	                ,items: [{
	                    xtype: 'textfield'
	                    ,fieldLabel: 'Employee'
	                    ,name: 'newloan[employee_id]'
	                	,anchor: '98%'
	            		,boxMinWidth: 100
		                ,allowBlank: false
		                ,required: true
		                ,maxLength: 8
		                ,emptyText: 'ID'
		                ,autoCreate: {tag: 'input', type: 'text', maxlength: '8'}
	        			,enableKeyEvents: true
	            		,style: 'text-align: right'
	        			,listeners: {
	        				specialkey: function(frm,evt){
	        					if (evt.getKey() == evt.ENTER) {
	        						pecaDataStores.newloanemployeeStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
	        		        		newloan_employeeListWin().show();				
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
	        				},
		        			'blur':{
		        				scope:this
		        				,fn:function(form){
		            				if( form.getValue() != '' ){
		            					Ext.getCmp('chkboxRestructure').setDisabled(false);
		            					pecaDataStores.newloanLTStore.load({params:{'employee_id':form.getValue()}});
		            				}else{
		            					Ext.getCmp('chkboxRestructure').setDisabled(true);
		            					Ext.getCmp('chkboxRestructure').setValue('false');
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
	                	,anchor: '98%'
		                ,maxLength: 30
		                ,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
	                	,submitValue: false
	                	,emptyText: 'Last Name'
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
	                	,anchor: '98%'
		                ,maxLength: 30
		                ,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
	                	,submitValue: false
	                	,emptyText: 'First Name'
	                }]
	            },{
	            	width: 75
	                ,xtype: 'button'
	                ,text: 'Search'
					,iconCls: 'icon_ext_search'
	            	,handler: function(){
		        		pecaDataStores.newloanemployeeStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
		        		newloan_employeeListWin().show();
				    }
	            }]
		    }]
		}
		,{
			xtype: 'fieldset'
		    ,layout: 'form'
			,anchor: '98%'
		    ,bodyStyle:{'padding-top':'10px'}	
		    ,items: [{
		    	layout: 'column'
	            ,border: false
	            ,items: [{
	                labelWidth: 1
	                ,labelAlign: 'left'
	                ,layout: 'form'
	                ,border: false
					,columnWidth: .15
	                //,width: 150
	                ,items: [{
	                	xtype:'checkbox'
	                	,id: 'chkboxRestructure'
	                	// jdj 07202017 change label
	                	// ,boxLabel: 'Restructure'
                    	,boxLabel: 'Previous Loan'	
                        ,anchor:'100%'
                        ,disabled: true
                        ,submitValue: false
                        ,listeners:{
                			check:{
                				scope:this
                				,fn:function(checkbox, bool) {
	                				var btn = Ext.getCmp('nl_restructureBtn');
	                				var frm = Ext.getCmp('newloanDetail').getForm();
                					if(bool){
                						btn.setVisible(true);
                						frm.findField('newloan[restructure_no]').setVisible(true);
                						frm.findField('newloan[restructure_no]').setDisabled(false);
                						frm.findField('newloan[restructure_amount]').setVisible(true);
                						frm.findField('newloan[restructure_amount]').setDisabled(false);
										frm.findField('newloan[restructure_no]').allowBlank = false;
										frm.findField('newloan[restructure_amount]').allowBlank = false;
                					}else{
                						btn.setVisible(false);
                						frm.findField('newloan[restructure_no]').setVisible(false);
                						frm.findField('newloan[restructure_no]').setDisabled(true);
										frm.findField('newloan[restructure_no]').allowBlank = true;
										frm.findField('newloan[restructure_amount]').allowBlank = true;
                						frm.findField('newloan[restructure_amount]').setVisible(false);
                						frm.findField('newloan[restructure_amount]').setDisabled(true);
                						frm.findField('newloan[restructure_no]').setValue("");
                						frm.findField('newloan[restructure_amount]').setValue(0.0);
                						var employee_id = frm.findField('newloan[employee_id]').getValue();
                						var first_name = frm.findField('first_name').getValue();
                						var last_name = frm.findField('last_name').getValue();
                						var loan_no = frm.findField('newloan[loan_no]').getValue();
                						frm.findField('newloan[employee_id]').setValue(employee_id);
                						frm.findField('first_name').setValue(first_name);
                						frm.findField('last_name').setValue(last_name);
                						frm.findField('newloan[loan_no]').setValue(loan_no);
                						newloanDetail().computeInterestRate();
                					}
                				}
                			}                			
                		}
	                }]
	            },{
	            	labelWidth: 75
					//,width: 200
					,columnWidth: .25
	                ,labelAlign: 'left'
					,layout: 'form'
	                ,border: false
	                ,items: [{
	                    xtype: 'textfield'
	                    ,fieldLabel: 'Loan No'
	                    ,name: 'newloan[restructure_no]'
	                	,anchor: '100%'
	            		//,boxMinWidth: 150
		                ,maxLength: 8
     	                ,required: true
     	                ,readOnly: true
     	                ,cls: 'x-item-disabled'
     	                ,hidden: true
     	                ,allowBlank: false
		                ,autoCreate: {tag: 'input', type: 'text', maxlength: '8'}
	                }]
	            },{
	            	//width: 75
					columnWidth: .1
					,labelWidth: 10
	                ,xtype: 'button'
	                ,id: 'nl_restructureBtn'
					,bodyStyle:{'padding-left':'50px'}
	                ,text: 'Search'
        			,iconCls: 'icon_ext_search'	
	                ,hidden: true
	            	,handler: function(){
						if(Ext.getCmp('newloanDetail').getForm().findField('newloan[loan_code]').getValue()==''){
							showExtInfoMsg( "Please select a loan type.");		
						}
						else{
							pecaDataStores.newloanRLStore.load({params: {
								employee_id: Ext.getCmp('newloanDetail').getForm().findField('newloan[employee_id]').getValue()
								,loan_code: Ext.getCmp('newloanDetail').getForm().findField('newloan[loan_code]').getValue()
								,start:0, limit:MAX_PAGE_SIZE}});
							newloan_RLListWin().show();
						}
				    }
	            },{
	            	labelWidth: 50
					,columnWidth: .5
					//,width: 200
	                ,labelAlign: 'left'
	                ,layout: 'form'
	                ,border: false
	                ,bodyStyle:{'padding-left':'50px'}
	                ,items: [{
	                    xtype: 'moneyfield'
	                    ,fieldLabel: 'Balance'
	                    ,name: 'newloan[restructure_amount]'
	                	,anchor: '100%'
	            		,boxMinWidth: 100
						,boxMaxWidth: 100
     	                ,required: true
     	                //,readOnly: true
     	                ,maxLength: 16
     	                ,hidden: true
     	                ,allowBlank: false
     	                //,cls: 'x-item-disabled'
     	                ,maxValue: 9999999999.99
     	               ,autoCreate: {tag: 'input', type: 'text', maxlength: '13'}
					   ,listeners:{
	    	        			change:{
	    	        				scope:this
	    	        				,fn:function(field, newVal, oldVal) {	                        			
	                        			newloanDetail().computeInterestRate();
	    	        				}
	    	        			}
						}	
	                }]
	            }]
		    }]
		}
		,{
			xtype: 'fieldset'
			,anchor: '98%'
		    ,layout: 'form'
		    ,bodyStyle:{'padding-top':'10px'}	
		    ,items: [{
	            border: false
	            ,items: [{
                    layout: 'column'
                    ,autoHeight: true
                    ,border: false
                    ,items: [{
                        labelWidth: 165
                        ,labelAlign: 'left'
                        ,layout: 'form'
                        ,columnWidth: 0.5
                        ,border: false
                        ,items: [
                        	new Ext.form.ComboBox({
            	                fieldLabel: 'Loan Type'
            	                ,hiddenName: 'newloan[loan_code]'
            	        	    ,typeAhead: true
            	        	    ,triggerAction: 'all'
            	        	    ,lazyRender:true
            	        	    ,store: pecaDataStores.newloanLTStore
            	        	    ,mode: 'local'
            	        	    ,valueField: 'loan_code'
            	        	    ,displayField: 'loan_description'
           	                	,anchor: '98%'
           	             		,boxMaxWidth: 250
       		            		,boxMinWidth: 150
            	        	    ,forceSelection: true
            	        	    ,submitValue: false
            	        	    ,emptyText: 'Please Select'
            	        	    ,required: true
            	        	    ,allowBlank: false
            	        	    ,listeners:{
            	        			'change':{
            	        				scope:this
            	        				,fn:function(combo, newVal, oldVal) {
            	        					var index = combo.getStore().findExact('loan_code',newVal);
            	        					var rec = combo.getStore().getAt(index);
            	        					var frm = Ext.getCmp('newloanDetail').getForm();            	        					            	        					
            	        					if(rec){
												frm.findField('newloan[interest_rate]').setValue(rec.get('emp_interest_pct'));
												frm.findField('newloan[company_interest_rate]').setValue(rec.get('comp_share_pct'));            	        					
												newloanDetail().computeInterestRate();
												
												pecaDataStores.newloanLTStore.load({params:{'employee_id': Ext.getCmp('newloanDetail').getForm().findField('newloan[employee_id]').getValue()}});
												
												pecaDataStores.newloanOTCmboxStore.load(
														{params:{'transaction_code':Ext.getCmp('newloanDetail').getForm().findField('newloan[loan_code]').getValue()}});
												
												//set restructure no. and balance to default and recompute loan proceeds
												Ext.getCmp('newloanDetail').getForm().findField('newloan[restructure_no]').setValue('');
												Ext.getCmp('newloanDetail').getForm().findField('newloan[restructure_amount]').setValue('0.00');	
												newloanDetail().computeLoanProceeds();
												
												//co maker check box

												if( rec.get('guarantor') == '0'){
													Ext.getCmp('newloan_comaker').setValue(true);
													//Ext.getCmp('newloanCoMakerSearchBtn').setDisabled(true);
												}else{
													Ext.getCmp('newloan_comaker').setValue(false);
													//Ext.getCmp('newloanCoMakerSearchBtn').setDisabled(false);
												}
												
												if( rec.get('pension') == 'true'){
													Ext.getCmp('newloan_pension').setValue(true);
													frm.findField('newloan[pension]').enable();
												}else{
													Ext.getCmp('newloan_pension').setValue(false);
													frm.findField('newloan[pension]').disable();
												}
												
												/***** NRB EDIT START *****/
												Ext.getCmp('newloanDetail').getForm().findField('newloan[mri_fip_provider]').clearValue();
												pecaDataStores.glEntryFieldNameStore.load({params:{'s_field_name':'mri_fip_amount', 's_loan_code':newVal}});
												/***** NRB EDIT START *****/
											}
            	        				}
            	        			}
                            	}
                        })
                        ,{
                        	xtype: 'textfield'
            				,fieldLabel: 'Loan Date'
            				,name: 'newloan[loan_date]'
							,readOnly: true
							,style: 'text-align: right'
            				,anchor: '98%'
          	             	,boxMaxWidth: 250
            				,boxMinWidth: 150
            				,allowBlank: false
            				,required: true
            				,maxLength: 10
            				,disableKeyFilter: true
            				,value: _TODAY
            				,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
                        	,validator: function(value2){
                            	var frm = Ext.getCmp('newloanDetail').getForm();
                        		var value1 = frm.findField('newloan[amortization_startdate]').value;
                        		value1 = Ext.util.Format.date(value1, "y/m/d");
								value2 = Ext.util.Format.date(value2, "y/m/d");
								
                        		if (value1 != null && value1 != ''){
                        			if (value1 >= value2){
                            			return true;
                            		} else{
                            			return 'Loan Date should be lesser than or equal to Amortization Start Date.';
                            		}
                        		}else{
                        			return true;
                        		}
                        		
                        	}
                        	,listeners:{
                    			'blur':{
                    				scope:this
                    				,fn:function(form){
                        				var loan_month = form.getValue();
                        				if(loan_month == ""){
                        					form.setValue(_TODAY);
                        					loan_month = form.getValue();
                        				}
                        				
                        				
                        				var amort_month = new Date(loan_month).add(Date.MONTH,1).getFirstDateOfMonth();
                                    	var frm = Ext.getCmp('newloanDetail').getForm();
		                        		frm.findField('newloan[amortization_startdate]').setValue(amort_month);
		                        		newloanDetail().computeInterestRate();
                    				}
                    			}
	                        	,'select':{
	                				scope:this
	                				,fn:function(form,date){
	                    				var loan_month = form.getValue();
	                    				var amort_month = new Date(loan_month).add(Date.MONTH,1).getFirstDateOfMonth();
	                                	var frm = Ext.getCmp('newloanDetail').getForm();
		                        		frm.findField('newloan[amortization_startdate]').setValue(amort_month);
		                        		newloanDetail().computeInterestRate();
	                				}
	                			}
                    		}
                        }
                        ,{
                        	xtype:'moneyfield'
        	            	,fieldLabel: "Principal"
        	                ,name: 'newloan[principal]'
                			,anchor: '98%'
             	            ,boxMaxWidth: 250
                 			,boxMinWidth: 150
        	                ,allowBlank: false
        	                ,required: true
        	                ,maxLength: 16
        	                ,maxValue: 9999999999.99
        	                //,minValue: 0
        	                ,value: 0.00
        	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '13'}
	                        ,listeners:{
	    	        			change:{
	    	        				scope:this
	    	        				,fn:function(field, newVal, oldVal) {	                        			
	                        			newloanDetail().computeInterestRate();
	    	        				}
	    	        			}
	                    	}
                        }
                        ,{
                        	xtype:'numberfield'
        	            	,fieldLabel: 'Term in Months'
        	                ,name: 'newloan[term]'
                    		,anchor: '60%'
                	        ,boxMaxWidth: 125
                    		,boxMinWidth: 70
        	                ,allowBlank: false
        	                ,required: true
        	                ,maxLength: 4
        	                ,minValue: 1
        	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '4'}
        	            	,style: 'text-align: right'
    	            		,listeners:{
			        			change:{
			        				scope:this
			        				,fn:function(field, newVal, oldVal) {	                        			
		                    			newloanDetail().computeInterestRate();
			        				}
			        			}
		                	}
                        }
                        ,{
                            layout: 'column'
                            ,border: false
                            ,items: [{
	                            layout: 'form'
	                            ,border: false
		                        ,columnWidth: 0.6
	                            ,items: [{
                                	xtype:'pecaNumberField'
                                	/***** NRB EDIT START *****/
                	            	,fieldLabel: "Annual Contractual Rate"
                	            	/* ,fieldLabel: "Employee Interest Rate" */
                	            	/***** NRB EDIT END *****/
                	                ,name: 'newloan[interest_rate]'
                                	,anchor: '100%'
                            	    ,boxMaxWidth: 125
                                	,boxMinWidth: 40
                	                ,maxLength: 6
                	                ,value: 0.00
                	                ,readOnly: 'true'
            	                	,cls: 'x-item-disabled'	
                	                ,allowNegative: false
                	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '6'}
                	            	,style: 'text-align: right'	
                                }]
                            }
                            /***** NRB EDIT START *****/
                            /*
,{
                                layout: 'form'
                                ,labelWidth: 10
                                ,border: false
		                        ,columnWidth: 0.4
                                ,items: [{
                                	xtype:'moneyfield'                	            
                	                ,name: 'newloan[employee_interest_total]'
                                	,anchor: '100%'
                            	    ,boxMaxWidth: 125
                                	,boxMinWidth: 100
                	                ,maxLength: 16
                	                ,maxValue: 9999999999.99
                	                ,value: 0.00
                	                ,readOnly: 'true'
            	                	,cls: 'x-item-disabled'	
                	                ,allowNegative: false
                	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '13'}
                	            	,style: 'text-align: right'
                                }]
                            }
*/
							]
							/***** NRB EDIT END *****/
                        }
                        /***** NRB EDIT START *****/
                        ,{
                        	xtype:'pecaNumberField'
                    		,fieldLabel: 'Effective Annual Interest Rate (EIR)'
        	                ,name: 'newloan[effective_annual_interest_rate]'
                            ,anchor: '60%'
                       	    ,boxMaxWidth: 125
                            ,boxMinWidth: 70
        	                ,maxLength: 6
        	                ,value: 0.00
        	                ,readOnly: 'true'
    	                	,cls: 'x-item-disabled'	
        	                ,allowNegative: false
        	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '6'}
        	            	,style: 'text-align: right'		
                        }
                        ,{
                        	xtype:'pecaNumberField'
                    		,fieldLabel: 'Effective Monthly Interest Rate (MIR)'
        	                ,name: 'newloan[effective_monthly_interest_rate]'
                            ,anchor: '60%'
                       	    ,boxMaxWidth: 125
                            ,boxMinWidth: 70
        	                ,maxLength: 6
        	                ,value: 0.00
        	                ,readOnly: 'true'
    	                	,cls: 'x-item-disabled'	
        	                ,allowNegative: false
        	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '6'}
        	            	,style: 'text-align: right'		
                        }
                        /***** NRB EDIT END *****/
                        ,{
                        	xtype:'moneyfield'
                    		,fieldLabel: 'Employee Initial Interest'
        	                ,name: 'newloan[initial_interest]'
                            ,anchor: '60%'
                       	    ,boxMaxWidth: 125
                            ,boxMinWidth: 70
        	                ,maxLength: 16
        	                ,maxValue: 9999999999.99
        	                ,value: 0.00
        	                ,readOnly: 'true'
    	                	,cls: 'x-item-disabled'	
        	                ,allowNegative: false
        	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '13'}
        	            	,style: 'text-align: right'		
                        }
                        ,{
                        	xtype:'moneyfield'
                    		,fieldLabel: 'Employee Amortized Interest'
        	                ,name: 'newloan[employee_interest_amortization]'
                            ,anchor: '60%'
                            ,boxMaxWidth: 125
                            ,boxMinWidth: 70
        	                ,maxLength: 16
        	                ,maxValue: 9999999999.99
        	                ,value: 0.00
        	                ,readOnly: 'true'
    	                	,cls: 'x-item-disabled'	
        	                ,allowNegative: false
        	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '13'}
        	            	,style: 'text-align: right'
                        }
                        ,{
                            layout: 'column'
                            ,border: false
                            ,items: [{
                                layout: 'form'
                                ,border: false
		                        ,columnWidth: 0.6
                                ,items: [{
                                	xtype:'pecaNumberField'
                                	/***** NRB EDIT START *****/
                	            	,fieldLabel: "P&G Interest Rate"
                	            	/* ,fieldLabel: "Company Interest Rate" */
                	            	/***** NRB EDIT END *****/
                	                ,name: 'newloan[company_interest_rate]'
                                    ,anchor: '100%'
                                    ,boxMaxWidth: 125
                                    ,boxMinWidth: 40
                	                ,maxLength: 6
                	                ,value: 0.00
                	                ,readOnly: 'true'
            	                	,cls: 'x-item-disabled'	
                	                ,allowNegative: false
                	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '6'}
                	            	,style: 'text-align: right'	
                                }]
                            }
                            /***** NRB EDIT START *****/
                            /*
,{
                            	layout: 'form'
                                ,labelWidth: 10
                                ,border: false
		                        ,columnWidth: 0.4
                                ,items: [{
                                	xtype:'moneyfield'                	            
                	                ,name: 'newloan[company_interest_total]'
                                    ,anchor: '100%'
                                    ,boxMaxWidth: 125
                                    ,boxMinWidth: 70
                	                ,maxLength: 16
                	                ,maxValue: 9999999999.99
                	                ,value: 0.00
                	                ,readOnly: 'true'
            	                	,cls: 'x-item-disabled'	
                	                ,allowNegative: false
                	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '13'}
                	            	,style: 'text-align: right'
                                }]
                            }
*/
							/***** NRB EDIT END *****/]
                        },
                        {
                        	xtype:'moneyfield'
                        	/***** NRB EDIT START *****/
                    		,fieldLabel: 'P&G Amortized Interest'
                    		/* ,fieldLabel: 'Company Amortized Interest' */
                    		/***** NRB EDIT END *****/
        	                ,name: 'newloan[company_interest_amort]'
                            ,anchor: '60%'
                            ,boxMaxWidth: 125
                            ,boxMinWidth: 70
        	                ,maxLength: 16
        	                ,maxValue: 9999999999.99
        	                ,value: 0.00
        	                ,readOnly: 'true'
    	                	,cls: 'x-item-disabled'	
        	                ,allowNegative: false
        	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '13'}
        	            	,style: 'text-align: right'
                        }]
                	},
                    {
                        labelWidth: 200
                        ,labelAlign: 'left'
                        ,layout: 'form'
                        ,columnWidth: 0.5
                        ,border: false
                        ,items: [{                            	
                        	xtype: 'datefield'
                    		,fieldLabel: 'Amortization Start Date'
            				,name: 'newloan[amortization_startdate]'	
                            ,anchor: '98%'
                            ,boxMaxWidth: 250
                            ,boxMinWidth: 150
            				,allowBlank: false
            				,required: true
            				,maxLength: 10
            				,disableKeyFilter: true
            				,value: (new Date().getFullYear())+(new Date().getMonth())+(new Date().getDate())
            				,disabledDates: [
            				                 '^../02','^../03','^../04','^../05','^../06','^../07','^../08','^../09','^../10'
            				                 ,'^../11','^../12','^../13','^../14','^../15','^../16','^../17','^../18','^../19','^../20'
            				                 ,'^../21','^../22','^../23','^../24','^../25','^../26','^../27','^../28','^../29','^../30','^../31'
            				                 ]
            				,disabledDatesText  : "Invalid Amortization Start Date"
							,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
	                        ,validator: function(value2){
	                        	var frm = Ext.getCmp('newloanDetail').getForm();
	                    		var value1 = frm.findField('newloan[loan_date]').value;
	                    		value1 = Ext.util.Format.date(value1, "y/m/d");
								value2 = Ext.util.Format.date(value2, "y/m/d");
	                    		if (value1 != null && value1 != ''){
	                    			if (value1 <= value2){
	                        			return true;
	                        		} else{
	                        			return 'Amortization Start Date should be greater than or equal to Loan Date.';
	                        		}
	                    		}else{
	                    			return true;
	                    		}
	                    		
	                    	}
                        }
                        ,{
                            xtype:'moneyfield'                	            
                        	,fieldLabel: 'Employee Principal Amortization'
                        	,name: 'newloan[employee_principal_amort]'
                            ,anchor: '98%'
                            ,boxMaxWidth: 250	
                            ,boxMinWidth: 150
        	                ,maxLength: 16
        	                ,maxValue: 9999999999.99
        	                ,value: 0.00
        	                ,readOnly: 'true'
    	                	,cls: 'x-item-disabled'	
        	                ,allowNegative: false
        	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '13'}
        	            	,style: 'text-align: right'	
                        }
                        ,{
                        	xtype:'moneyfield'                	            
                        	,fieldLabel: 'Loan Proceeds'
                        	,name: 'newloan[loan_proceeds]'
                            ,anchor: '98%'
                            ,boxMaxWidth: 250
                            ,boxMinWidth: 150
        	                ,maxLength: 16
        	                ,maxValue: 9999999999.99
        	                ,value: 0.00
        	                ,readOnly: 'true'
    	                	,cls: 'x-item-disabled'	
        	                ,allowNegative: false
        	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '13'}
        	            	,style: 'text-align: right'
                        }
                        ,{
                        	xtype:'moneyfield'                	            
                    		,fieldLabel: 'MRI/FIP'
                        	,name: 'newloan[mri_fip_amount]'
                            ,anchor: '98%'
                            ,boxMaxWidth: 250
                            ,boxMinWidth: 150
        	                ,maxLength: 16
        	                ,maxValue: 9999999999.99
        	                ,value: 0.00
        	                ,allowNegative: false
        	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '13'}	
	                        ,listeners:{
			        			change:{
			        				scope:this
			        				,fn:function(field, newVal, oldVal) {	                        			
		                    			newloanDetail().computeInterestRate();
			        				}
			        			}
			        			/***** NRB EDIT START *****/
			        			,'blur':{
			        				scope:this
			        				,fn:function(form) {
				        				newloanDetail().requireMRIFIPProvider();
			        				}	
			        			}
			        			/***** NRB EDIT END *****/
		                	}
                        }
                        /***** NRB EDIT START *****/
                        , new Ext.form.ComboBox({
            	                fieldLabel: 'MRI/FIP Provider'
            	                ,hiddenName: 'newloan[mri_fip_provider]'
            	        	    ,typeAhead: true
            	        	    ,triggerAction: 'all'
            	        	    ,lazyRender:true
            	        	    ,store: pecaDataStores.glEntryFieldNameStore
            	        	    ,mode: 'local'
            	        	    ,valueField: 'account_no'
            	        	    ,displayField: 'account_name'
           	                	,anchor: '98%'
           	             		,boxMaxWidth: 250
       		            		,boxMinWidth: 150
            	        	    ,forceSelection: true
            	        	    ,submitValue: false
            	        	    ,emptyText: 'Please Select'
            	        	    ,required: false
            	        	    ,allowBlank: true
            	        	    ,listeners:{
            	        			'change':{
            	        				scope:this
            	        				,fn:function(combo, newVal, oldVal) {
            	        					/** INSERT ACTION HERE **/
            	        				}
            	        			}
                            	}
                        })
                        /***** NRB EDIT END *****/                     
                        ,{
                        	xtype:'moneyfield'                	            
                    		,fieldLabel: "Broker's Fee"
                        	,name: 'newloan[broker_fee_amount]'
                            ,anchor: '98%'
                            ,boxMaxWidth: 250 
                            ,boxMinWidth: 150
        	                ,maxLength: 16
        	                ,maxValue: 9999999999.99
        	                ,value: 0.00
        	                ,allowNegative: false
        	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '13'}
	                        ,listeners:{
			        			change:{
			        				scope:this
			        				,fn:function(field, newVal, oldVal) {	                        			
		                    			newloanDetail().computeInterestRate();
			        				}
			        			}
		                	}
                        }
                        ,{
                        	xtype:'moneyfield'                	            
                    		,fieldLabel: "Gov't Fees"
                        	,name: 'newloan[government_fee_amount]'
                            ,anchor: '98%'
                            ,boxMaxWidth: 250
                            ,boxMinWidth: 150
        	                ,maxLength: 16
        	                ,maxValue: 9999999999.99
        	                ,value: 0.00
        	                ,allowNegative: false
        	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '13'}	
	                        ,listeners:{
			        			change:{
			        				scope:this
			        				,fn:function(field, newVal, oldVal) {	                        			
		                    			newloanDetail().computeInterestRate();
			        				}
			        			}
		                	}
                        }
                        ,{
                        	xtype:'moneyfield'                	            
                    		,fieldLabel: 'From CapCon'
                        	,name: 'newloan[other_fee_amount]'
                            ,anchor: '98%'
                            ,boxMaxWidth: 250
                            ,boxMinWidth: 150
        	                ,maxLength: 16
        	                ,maxValue: 9999999999.99
        	                ,value: 0.00
        	                //,allowNegative: false
        	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '13'}	
	                        ,listeners:{
			        			change:{
			        				scope:this
			        				,fn:function(field, newVal, oldVal) {	                        			
		                    			newloanDetail().computeInterestRate();
			        				}
			        			}
		                	}
                        }
                        ,{
                            xtype:'moneyfield'                	            
                        	,fieldLabel: 'Service Charge'
                        	,name: 'newloan[service_fee_amount]'
                            ,anchor: '98%'
                            ,boxMaxWidth: 250
                            ,boxMinWidth: 150
        	                ,maxLength: 16
        	                ,maxValue: 9999999999.99
        	                ,value: 0.00
        	                ,readOnly: 'true'
    	                	,cls: 'x-item-disabled'
        	                ,allowNegative: false
        	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '13'}	
	                        ,listeners:{
			        			change:{
			        				scope:this
			        				,fn:function(field, newVal, oldVal) {	                        			
		                    			newloanDetail().computeInterestRate();
			        				}
			        			}
		                	}
                        }
                        /***** NRB EDIT START *****/
                        ,{
                            xtype:'moneyfield'                	            
                        	,fieldLabel: 'Other Charges Amount'
                        	,name: 'newloan[other_charges_amount]'
                            ,anchor: '98%'
                            ,boxMaxWidth: 250
                            ,boxMinWidth: 150
        	                ,maxLength: 16
        	                ,maxValue: 9999999999.99
        	                ,value: 0.00
        	                ,readOnly: 'true'
    	                	,cls: 'x-item-disabled'
        	                ,allowNegative: false
        	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '13'}	
                        }
                        ,{
                        	xtype:'pecaNumberField'
        	            	,fieldLabel: "Other Charges Rate"
        	                ,name: 'newloan[other_charges_rate]'
                            ,anchor: '100%'
                            ,boxMaxWidth: 125
                            ,boxMinWidth: 40
        	                ,maxLength: 6
        	                ,value: 0.00
        	                ,readOnly: 'true'
    	                	,cls: 'x-item-disabled'	
        	                ,allowNegative: false
        	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '6'}
        	            	,style: 'text-align: right'	
                        }
                        /***** NRB EDIT END *****/
                        ,{
                            xtype:'checkbox'
        	                ,boxLabel: 'Bank Transfer'
        	                ,id: 'newloan_bank_transfer'
        	                ,name: 'newloan[bank_transfer]'
        	                ,anchor:'95%'
        	                ,submitValue: false	
                        }
                        /* ##### NRB EDIT START ##### */
                        ,{
		                    layout: 'column'
		                    ,border: false
		                    ,items: [{
		                        labelWidth: 70
		                        ,labelAlign: 'left'
		                        ,layout: 'form'
		                        ,columnWidth: 0.5
		                        ,border: false
		                        ,items: [new Ext.form.ComboBox({
		                        	fieldLabel: 'Appraiser'
		    		                ,hiddenName: 'newloan[appraiser_broker]'
		    		        	    ,typeAhead: true
		    		        	    ,triggerAction: 'all'
		    		        	    ,lazyRender:true
		    		        	    ,store: pecaDataStores.supplierStore
		    		        	    ,mode: 'local'
		    		        	    ,valueField: 'supplier_id'
		    		        	    ,displayField: 'supplier_name'
		    		        	    ,forceSelection: true
		    		        	    ,submitValue: false
		    		        	    ,emptyText: 'Please Select'
									,boxMaxWidth: 250
									,boxMinWidth: 150
		    		        	})]
		                    }
		                    ,{
		                        labelWidth: 70
		                        ,labelAlign: 'left'
		                        ,layout: 'form'
		                        ,columnWidth: 0.5
		                        ,border: false
		                        ,bodyStyle:{'padding-left':'10px'}
		                        ,items: [{
		                        	xtype: 'textfield'
		    	                    ,fieldLabel: 'Check No'
		    	                    ,name: 'newloan[check_no]'
		    	                	,width:'120'
		    		                ,maxLength: 12
		    		                ,autoCreate: {tag: 'input', type: 'text', maxlength: '12'}
		                        }]
		                    }]
		                }
		                /* ##### NRB EDIT END ##### */
		                ]
                    }]
                }
/*
                ,
                {
                    layout: 'column'
                    ,border: false
                    ,items: [
*/
                    /* ##### NRB EDIT START ##### */
                    /*
{
                        labelWidth: 110
                        ,labelAlign: 'left'
                        ,layout: 'form'
                        ,columnWidth: 0.36
                        ,border: false
                        ,items: [new Ext.form.ComboBox({
                        	fieldLabel: 'Insurance Broker'
    		                ,hiddenName: 'newloan[insurance_broker]'
    		        	    ,typeAhead: true
    		        	    ,triggerAction: 'all'
    		        	    ,lazyRender:true
    		        	    ,store: pecaDataStores.supplierStore
    		        	    ,mode: 'local'
    		        	    ,valueField: 'supplier_id'
    		        	    ,displayField: 'supplier_name'
    		        	    //,width: '120'
    		        	    ,forceSelection: true
    		        	    ,submitValue: false
    		        	    ,emptyText: 'Please Select'
							,boxMaxWidth: 250
							,boxMinWidth: 150
    		        	})]
                    },
*/
                    /* ##### NRB EDIT END ##### */
/*
                    {
                        labelWidth: 70
                        ,labelAlign: 'left'
                        ,layout: 'form'
*/
                        /* ##### NRB EDIT START ##### */
/*
                        ,columnWidth: 0.5
*/
                        /* ,columnWidth: 0.34 */
                        /* ##### NRB EDIT END ##### */
/*
                        ,border: false
                        ,bodyStyle:{'padding-left':'20px'}
                        ,items: [new Ext.form.ComboBox({
                        	fieldLabel: 'Appraiser'
    		                ,hiddenName: 'newloan[appraiser_broker]'
    		        	    ,typeAhead: true
    		        	    ,triggerAction: 'all'
    		        	    ,lazyRender:true
    		        	    ,store: pecaDataStores.supplierStore
    		        	    ,mode: 'local'
    		        	    ,valueField: 'supplier_id'
    		        	    ,displayField: 'supplier_name'
    		        	    //,width: '120'
    		        	    ,forceSelection: true
    		        	    ,submitValue: false
    		        	    ,emptyText: 'Please Select'
							,boxMaxWidth: 250
							,boxMinWidth: 150
    		        	})]
                    }
                    ,{
                        labelWidth: 70
                        ,labelAlign: 'left'
                        ,layout: 'form'
*/
                        /* ##### NRB EDIT START ##### */
/*
                        ,columnWidth: 0.5
*/
                        /* ,columnWidth: 0.30 */
                        /* ##### NRB EDIT END ##### */
/*
                        ,border: false
                        ,bodyStyle:{'padding-left':'20px'}
                        ,items: [{
                        	xtype: 'textfield'
    	                    ,fieldLabel: 'Check No'
    	                    ,name: 'newloan[check_no]'
    	                	,width:'120'
    		                ,maxLength: 12
    		                ,autoCreate: {tag: 'input', type: 'text', maxlength: '12'}
                        }]
                    }]
                }

*/
                /* ##### NRB EDIT END ##### */
                ]
	        }]
		}
		,{
		    layout: 'column'
		    ,border: false
		    ,anchor: '98%'
		    ,items: [newloanOtherCharges()
		             ,newloanCoMaker()
		             ]
		}]
		,isModeNew: function() {
	    	return (Ext.getCmp('newloanDetail').getForm().findField('frm_mode').getValue() == FORM_MODE_NEW);
	    }
	    ,setModeNew: function() {
	    	var frm = Ext.getCmp('newloanDetail').getForm();
	    	
	    	frm.findField('frm_mode').setValue(FORM_MODE_NEW);
	    	Ext.getCmp('newloanDetail').buttons[0].setVisible(true);  //cancel button
	    	Ext.getCmp('newloanDetail').buttons[1].setVisible(false);  //delete button
	    	Ext.getCmp('newloanDetail').buttons[2].setVisible(true);  //save button
			Ext.getCmp('newloanDetail').buttons[3].setVisible(false);  //preview button
	    	
	    	frm.findField('newloan[loan_no]').setVisible(false);

	    	frm.findField('newloan[loan_date]').setValue(_TODAY);
	    	var loan_month = frm.findField('newloan[loan_date]').getValue();
			var amort_month = new Date(loan_month).add(Date.MONTH,1).getFirstDateOfMonth();
    		frm.findField('newloan[amortization_startdate]').setValue(amort_month);
	    	
    		frm.findField('last_name').setValue('');
			frm.findField('first_name').setValue('');
    		//frm.findField('newloan[restructure_no]').setValue('');
    		frm.findField('newloan[restructure_amount]').setValue('0.00');
	    	frm.findField('newloan[principal]').setValue('0.00');
	    	frm.findField('newloan[term]').setValue('1');
	    	frm.findField('newloan[interest_rate]').setValue('0.00');
	    	/***** NRB EDIT START *****/
	    	/* frm.findField('newloan[employee_interest_total]').setValue('0.00'); */
	    	/***** NRB EDIT END *****/
	    	frm.findField('newloan[initial_interest]').setValue('0.00');
	    	frm.findField('newloan[employee_interest_amortization]').setValue('0.00');
	    	frm.findField('newloan[company_interest_rate]').setValue('0.00');
	    	/***** NRB EDIT START *****/
	    	/* frm.findField('newloan[company_interest_total]').setValue('0.00'); */
	    	/***** NRB EDIT END *****/
	    	/***** NRB EDIT START *****/
	    	frm.findField('newloan[effective_annual_interest_rate]').setValue('0.00');
	    	frm.findField('newloan[effective_monthly_interest_rate]').setValue('0.00');
	    	/***** NRB EDIT END *****/
	    	frm.findField('newloan[company_interest_amort]').setValue('0.00');
	    	frm.findField('newloan[employee_principal_amort]').setValue('0.00');
	    	frm.findField('newloan[loan_proceeds]').setValue('0.00');
	    	frm.findField('newloan[mri_fip_amount]').setValue('0.00');
	    	frm.findField('newloan[broker_fee_amount]').setValue('0.00');
	    	frm.findField('newloan[government_fee_amount]').setValue('0.00');
	    	frm.findField('newloan[other_fee_amount]').setValue('0.00');
	    	frm.findField('newloan[service_fee_amount]').setValue('0.00');
			frm.findField('newloan[pension]').setValue('');

			/***** NRB EDIT START *****/
			frm.findField('newloan[other_charges_amount]').setValue('0.00');
			frm.findField('newloan[other_charges_rate]').setValue('0.00');
			/***** NRB EDIT END *****/

	    	frm.findField('newloan[employee_id]').focus('',250);
			frm.findField('newloan[pension]').setDisabled(true);
			Ext.getCmp('chkboxRestructure').setDisabled(true);
			Ext.getCmp('newloanOtherCharges').setDisabled(true);
			//Ext.getCmp('newloanCoMakerSearchBtn').setDisabled(true);
			
			Ext.getCmp('newloan_comaker').setValue(false);
			pecaDataStores.newloanCoMakerStore.autoSave = false;
			
			pecaDataStores.newloanCoMakerStore.load();
			pecaDataStores.newloanOtherChargesStore.load();
			pecaDataStores.newloanLTStore.load({params:{'employee_id': "0"}});

			Ext.getCmp('newloanOtherCharges').findById('charge_code').setDisabled(false);
			Ext.getCmp('newloanOtherChargesAddBtn').show();
        	Ext.getCmp('newloanOtherChargesUpdateBtn').hide(); 
		}
	    ,setModeUpdate: function() {
	    	var frm = Ext.getCmp('newloanDetail').getForm();
	    	
			frm.findField('frm_mode').setValue(FORM_MODE_UPDATE);
			Ext.getCmp('newloanDetail').buttons[0].setVisible(true);  //cancel button
	    	Ext.getCmp('newloanDetail').buttons[1].setVisible(true);  //delete button
	    	Ext.getCmp('newloanDetail').buttons[2].setVisible(true);  //save button
			Ext.getCmp('newloanDetail').buttons[3].setVisible(true);  //preview button
	    	
			pecaDataStores.newloanOtherChargesStore.autoSave = false;
	    	
	    	//can't update record
			if(_PERMISSION[134]==0){
				Ext.getCmp('newloanDetail').buttons[2].setDisabled(true);	
			}
			//can't delete record
			if(_PERMISSION[37]==0){
				Ext.getCmp('newloanDetail').buttons[1].setDisabled(true);	
			}
			
			//added to clear the invalid text in amort field
			var amort_month = new Date(_TODAY).add(Date.MONTH,1).getFirstDateOfMonth();
    		frm.findField('newloan[amortization_startdate]').setValue(amort_month);
	    	
	    	frm.findField('newloan[loan_no]').setVisible(true);		
			Ext.getCmp('newloanOtherCharges').findById('newloanOTAmount').setValue('0.00');	    	
			Ext.getCmp('chkboxRestructure').setDisabled(false);
			Ext.getCmp('chkboxRestructure').focus(true,250);
			Ext.getCmp('newloanOtherCharges').setDisabled(false);
			pecaDataStores.newloanCoMakerStore.autoSave = false;
			
			Ext.getCmp('newloanOtherCharges').findById('charge_code').setDisabled(false);
			Ext.getCmp('newloanOtherChargesAddBtn').show();
        	Ext.getCmp('newloanOtherChargesUpdateBtn').hide();
			
	    }
		,computeLoanProceeds: function(){ 
			var frm = Ext.getCmp('newloanDetail').getForm();
			var principal = frm.findField('newloan[principal]').getValue();
			var emp_interest = frm.findField('newloan[interest_rate]').getValue()/100;
			var cmp_interest = frm.findField('newloan[company_interest_rate]').getValue()/100;
			var term =  frm.findField('newloan[term]').getValue();
			var mri = frm.findField('newloan[mri_fip_amount]').getValue();
			var broker = frm.findField('newloan[broker_fee_amount]').getValue();
			var govnt = frm.findField('newloan[government_fee_amount]').getValue();
			var serv_fee = frm.findField('newloan[service_fee_amount]').getValue();
			var other_fee = frm.findField('newloan[other_fee_amount]').getValue();
			
			var emp_total = emp_interest * principal;
			var emp_int_total = emp_total;
			var emp_int_amort = 0;
			var cmp_int_total = principal * cmp_interest;
			var cmp_int_amort = cmp_int_total/12;
			var emp_prin_amort = principal/term;
			
			var emp_init_int = 0;
			var loan_month = frm.findField('newloan[loan_date]').getValue();
			var total_days = new Date(loan_month).getDaysInMonth();
			var rem_days = total_days -  new Date(loan_month).getDate();	
			
			if(term<12){
				emp_int_total = (emp_total/12)* term;
				emp_int_amort = emp_int_total/term;
			}else{
				emp_int_amort = emp_total/12;
			}
			
			//Round this to the nearest whole number.
			emp_int_amort =  Math.round(emp_int_amort);
			
			//initialize loan proceeds with principal value
			var loan_proceeds = principal - frm.findField('newloan[restructure_amount]').getValue();
			if( frm.findField('newloan[loan_code]').getValue() != '' ){
				var index = pecaDataStores.newloanLTStore.findExact('loan_code',frm.findField('newloan[loan_code]').getValue());
				var rec = pecaDataStores.newloanLTStore.getAt(index);
				
				if(rec.get('interest_earned') == 'true'){
					emp_init_int = emp_total/12;
					emp_init_int = ((emp_init_int)/total_days)*rem_days;
					loan_proceeds = loan_proceeds - emp_init_int;
				}
				
				if(rec.get('unearned_interest') == 'true'){
					loan_proceeds = loan_proceeds - emp_int_total;
					emp_int_amort = 0;
				}
			}
			
			loan_proceeds = loan_proceeds - mri - broker - govnt - serv_fee + other_fee;
			
			frm.findField('newloan[loan_proceeds]').setValue(Ext.util.Format.number(loan_proceeds,'0.00'));
			frm.findField('newloan[loan_proceeds]')._onBlur(frm.findField('newloan[loan_proceeds]'));
		}
	    ,computeInterestRate: function(){
	    	var frm = Ext.getCmp('newloanDetail').getForm();
			var principal = frm.findField('newloan[principal]').getValue();
			var emp_interest = frm.findField('newloan[interest_rate]').getValue()/100;
			var cmp_interest = frm.findField('newloan[company_interest_rate]').getValue()/100;
			var term = frm.findField('newloan[term]').getValue();
			var mri = frm.findField('newloan[mri_fip_amount]').getValue();
			var broker = frm.findField('newloan[broker_fee_amount]').getValue();
			var govnt = frm.findField('newloan[government_fee_amount]').getValue();
			var serv_fee = frm.findField('newloan[service_fee_amount]').getValue();
			var other_fee = frm.findField('newloan[other_fee_amount]').getValue();
			
			/***** NRB EDIT START *****/
			/*var emp_total = Math.round(emp_interest * principal);*/
			var emp_total = emp_interest * principal; 
			/***** NRB EDIT END *****/
			var emp_int_total = emp_total;
			var emp_int_amort = 0;
			var cmp_int_total = principal * cmp_interest;
			/***** NRB EDIT START *****/
			var cmp_int_amort = Math.round(cmp_int_total/12);
			/* var cmp_int_amort = cmp_int_total/12; */
			/***** NRB EDIT END *****/
			var emp_prin_amort = principal/term;
			
			var emp_init_int = 0;
			var loan_month = frm.findField('newloan[loan_date]').getValue();
			var total_days = new Date(loan_month).getDaysInMonth();
			var rem_days = total_days -  new Date(loan_month).getDate();	
			
			if(term<12){
				emp_int_total = (emp_total/12)* term;
				emp_int_amort = emp_int_total/term;
			}else{
				emp_int_amort = emp_total/12;
			}
			
			/******************/
/*
			var tf_principal = frm.findField('newloan[principal]').getValue();
			var tf_rate = frm.findField('newloan[interest_rate]').getValue() / 100;
			var ts_month = frm.findField('newloan[loan_date]').getValue();
			var ti_days = new Date(ts_month).getDaysInMonth();
			var ti_diff_days = new Date(loan_month).getDate();
			var ti_remaining = ti_days - ti_diff_days;
			var tf_interest = ((((tf_principal * tf_rate) / 12) * ti_remaining) / ti_days);
			alert(tf_principal+"\n"+tf_rate+"\n"+ts_month+"\n"+ti_days+"\n"+ti_diff_days+"\n"+ti_remaining+"\n"+tf_interest);
*/
			/******************/
			
			//Round this to the nearest whole number.
			emp_int_amort =  Math.round(emp_int_amort);
			
			//initialize loan proceeds with principal value
			var loan_proceeds = principal - frm.findField('newloan[restructure_amount]').getValue();
			if( frm.findField('newloan[loan_code]').getValue() != '' ){
				var index = pecaDataStores.newloanLTStore.findExact('loan_code',frm.findField('newloan[loan_code]').getValue());
				var rec = pecaDataStores.newloanLTStore.getAt(index);
				
				if(rec.get('interest_earned') == 'true'){
					emp_init_int = emp_total/12;
					emp_init_int = ((emp_init_int)/total_days)*rem_days;
					loan_proceeds = loan_proceeds - Ext.util.Format.round(emp_init_int,2);
				}
				
				if(rec.get('unearned_interest') == 'true'){
					loan_proceeds = loan_proceeds - Ext.util.Format.round(emp_int_total,2);
					emp_int_amort = 0;
				}
			}
			
			loan_proceeds = loan_proceeds - mri - broker - govnt - serv_fee + other_fee;
			
			frm.findField('newloan[loan_proceeds]').setValue(Ext.util.Format.number(loan_proceeds,'0.00'));
			frm.findField('newloan[loan_proceeds]')._onBlur(frm.findField('newloan[loan_proceeds]'));
			/***** NRB EDIT START *****/
/*
			frm.findField('newloan[employee_interest_total]').setValue(Ext.util.Format.number(emp_int_total,'0.00'));
			frm.findField('newloan[employee_interest_total]')._onBlur(frm.findField('newloan[employee_interest_total]'));
*/
			/***** NRB EDIT END *****/
			frm.findField('newloan[initial_interest]').setValue(Ext.util.Format.number(Ext.util.Format.round(emp_init_int,2),'0.00'));
			frm.findField('newloan[initial_interest]')._onBlur(frm.findField('newloan[initial_interest]'));
			/***** NRB EDIT START *****/
			frm.findField('newloan[employee_interest_amortization]').setValue(Ext.util.Format.number(Ext.util.Format.round(emp_int_amort,2),'0.00'));
			/* frm.findField('newloan[employee_interest_amortization]').setValue(Ext.util.Format.number(emp_int_amort,'0.00')); */
			/***** NRB EDIT END *****/
			frm.findField('newloan[employee_interest_amortization]')._onBlur(frm.findField('newloan[employee_interest_amortization]'));
			/***** NRB EDIT START *****/
/*
			frm.findField('newloan[company_interest_total]').setValue(Ext.util.Format.number(cmp_int_total,'0.00'));
			frm.findField('newloan[company_interest_total]')._onBlur(frm.findField('newloan[company_interest_total]'));
*/
			/***** NRB EDIT END *****/
			frm.findField('newloan[company_interest_amort]').setValue(Ext.util.Format.number(cmp_int_amort,'0.00'));
			frm.findField('newloan[company_interest_amort]')._onBlur(frm.findField('newloan[company_interest_amort]'));
			
			frm.findField('newloan[employee_principal_amort]').setValue(Ext.util.Format.round(Ext.util.Format.number(emp_prin_amort,'0.00'), 0));
			frm.findField('newloan[employee_principal_amort]')._onBlur(frm.findField('newloan[employee_principal_amort]'));
			
			/***** NRB EDIT START *****/
			this.computeOtherChargesAmount();
			this.computeEIR();
			/***** NRB EDIT END *****/
	    }
	    /***** NRB EDIT START *****/
	    ,computeOtherChargesAmount: function() {
		    var o_frm = Ext.getCmp('newloanDetail').getForm();
		    var f_service_charge = o_frm.findField('newloan[service_fee_amount]').getValue();
		    var f_initial_interest = o_frm.findField('newloan[initial_interest]').getValue();
		    var f_principal = o_frm.findField('newloan[principal]').getValue();
		    /**
		    Other Charges Amount ::
		    Computed as Initial Interest + Service Charge
		    Example. 1239.89 + 5000 = 6,239.89 **/
		    var f_other_charges_amount = f_service_charge + f_initial_interest;
		    o_frm.findField('newloan[other_charges_amount]').setValue(Ext.util.Format.number(Ext.util.Format.round(f_other_charges_amount,2),'0.00'));
		    o_frm.findField('newloan[other_charges_amount]')._onBlur(o_frm.findField('newloan[other_charges_amount]'));
		    /**
		    Other Charges Rate ::
		    Computed as (Other Charges Amount / Principal) * 100
		    Example .( 6239.80 / 1,000,000.00)*100= 0.6 **/	
		    if(f_principal > 0) {
			    var f_other_charges_rate = (f_other_charges_amount / f_principal) * 100;
			    o_frm.findField('newloan[other_charges_rate]').setValue(Ext.util.Format.number(f_other_charges_rate,'0.00'));
		    }
													
	    }
	    ,computeEIR: function() {
	    	var o_frm = Ext.getCmp('newloanDetail').getForm();
	    	var f_annual_contractual_rate = o_frm.findField('newloan[interest_rate]').getValue();
	    	var f_loan_amount = o_frm.findField('newloan[principal]').getValue();
	    	var f_service_charge = o_frm.findField('newloan[service_fee_amount]').getValue();
	    	var i_terms = o_frm.findField('newloan[term]').getValue();
	    	var s_loan_code = o_frm.findField('newloan[loan_code]').getValue();
	    	var s_initial_interest = o_frm.findField('newloan[initial_interest]').getValue();
	        Ext.Ajax.request({
	        	url: '/excel/eir'
				,method: 'POST'
				,params: {'f_annual_contractual_rate': f_annual_contractual_rate
							,'f_loan_amount': f_loan_amount
							,'f_service_charge': f_service_charge
							,'i_terms': i_terms
							,'s_loan_code': s_loan_code
							,'s_initial_interest': s_initial_interest
	        				,auth:_AUTH_KEY}
				,success: function(response, opts) {
					var o_response = Ext.decode(response.responseText);
				    o_frm.findField('newloan[effective_annual_interest_rate]').setValue(Ext.util.Format.number(o_response.f_eir,'0.00'));
				    o_frm.findField('newloan[effective_monthly_interest_rate]').setValue(Ext.util.Format.number(o_response.f_mir,'0.00'));
				}
				,failure: function(response, opts) {
					
				}
        	});
	    }
	    ,requireMRIFIPProvider: function() {
		    var o_frm = Ext.getCmp('newloanDetail').getForm();
		    var f_mri = o_frm.findField('newloan[mri_fip_amount]').getValue();
		    if(f_mri < 1 || f_mri == null) {
		    	o_frm.findField('newloan[mri_fip_provider]').reset();
			    o_frm.findField('newloan[mri_fip_provider]').allowBlank = true;
			    o_frm.findField('newloan[mri_fip_provider]').required = false;
		    } else {
			    o_frm.findField('newloan[mri_fip_provider]').allowBlank = false;
			    o_frm.findField('newloan[mri_fip_provider]').required = true;
		    }
	    }
	    /***** NRB EDIT END *****/
	    ,onSave: function(frm){
	    	newloanDetail().requireMRIFIPProvider();
	    	var rowCount = pecaDataStores.newloanCoMakerStore.getCount();
	    	var comakerData = "[";
	    	if(rowCount > 0){
	    		for(var i = 0; i < rowCount; i++){
	    			var rec = pecaDataStores.newloanCoMakerStore.getAt(i);
	    			comakerData += Ext.encode(rec.data) + ",";
	    		}
	    	}
	    	comakerData += "]";
			frm.submit({
    			url: '/loan/addLoan' 
    			,method: 'POST'
    				,params: {'newloan[bank_transfer]': Ext.getCmp('newloan_bank_transfer').getValue() ? 'Y' : 'N'
    					,comaker: comakerData
						,auth:_AUTH_KEY, 'newloan[created_by]': _USER_ID	
    			}
    			,waitMsg: 'Creating Data...'
    			,success: function(form, action) {
    				showExtInfoMsg( action.result.msg);
    				frm.setModeUpdate();
    				frm.findField('newloan[loan_no]').setValue(action.result.loan_no);
    				frm.findField('newloan[service_fee_amount]').setValue(action.result.service_fee_amount);
    				//pecaDataStores.newloanCoMakerStore.save();
    				pecaDataStores.newloanCoMakerStore.reload({params: {start:0, limit:MAX_PAGE_SIZE}});
    				pecaDataStores.newloanOtherChargesStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
    				frm.findField('newloan[service_fee_amount]').setValue(action.result.service_fee_amount);
         			newloanDetail().computeInterestRate();
					Ext.getCmp('newloanDetail').getForm().load({
				    	url: '/loan/showLoan'
				    	,params: {'loan_no':(action.result.loan_no)
									,auth:_AUTH_KEY}
				    	,method: 'POST'
				    	,waitMsgTarget: true
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
	    	newloanDetail().requireMRIFIPProvider();
			var rowCount = pecaDataStores.newloanCoMakerStore.getCount();
	    	var comakerData = "[";
	    	if(rowCount > 0){
	    		for(var i = 0; i < rowCount; i++){
	    			var rec = pecaDataStores.newloanCoMakerStore.getAt(i);
	    			comakerData += Ext.encode(rec.data) + ",";
	    		}
	    	}
	    	comakerData += "]";
			
			var rowCount = pecaDataStores.newloanOtherChargesStore.getCount();
	    	var chargesData = "[";
	    	if(rowCount > 0){
	    		for(var i = 0; i < rowCount; i++){
	    			var rec = pecaDataStores.newloanOtherChargesStore.getAt(i);
	    			chargesData += Ext.encode(rec.data) 
					if((i+1)<rowCount){
						chargesData += ",";
					}
	    		}
	    	}
	    	chargesData += "]";
			
			frm.submit({
    			url: '/loan/updateLoan' 
    			,method: 'POST'
    				,params: {'newloan[bank_transfer]': Ext.getCmp('newloan_bank_transfer').getValue() ? 'Y' : 'N'
						,auth:_AUTH_KEY, 'newloan[modified_by]': _USER_ID	
						,comaker: comakerData
						,charges: chargesData
    			}
    			,waitMsg: 'Updating Data...'
    			,success: function(form, action) {
    				showExtInfoMsg( action.result.msg);
        			frm.setModeUpdate();
        			//pecaDataStores.newloanCoMakerStore.save();
    				pecaDataStores.newloanCoMakerStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
    			}
    			,failure: function(form, action) {
					//20111118 commented by ASI466 because this(prompt) is misleading to the user 
    				//showExtErrorMsg( action.result.msg);
					showExtInfoMsg( action.result.msg);
    			}	
    		});
		}
	    ,onDelete: function(frm){			
			Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
				if(btn=='yes') {
					frm.submit({
						url: '/loan/deleteLoan' 
						,method: 'POST'
						,params: {auth:_AUTH_KEY, 'newloan[modified_by]': _USER_ID}
						,waitMsg: 'Deleting Data...'
						,clientValidation: false
						,success: function(form, action) {						
							showExtInfoMsg( action.result.msg);
							Ext.getCmp('newloanDetail').hide();
							Ext.getCmp('newloanDetail').getForm().reset();
							Ext.getCmp('newloanList').show();
							//pecaDataStores.newloanStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
							if (pecaDataStores.newloanStore.getCount() % MAX_PAGE_SIZE == 1){
								var page = pecaDataStores.newloanStore.getTotalCount() - MAX_PAGE_SIZE - 1;
								pecaDataStores.newloanStore.load({params: {start:page<0?0:page, limit:MAX_PAGE_SIZE}});
							} else{
								pecaDataStores.newloanStore.reload();
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

var newloanOtherCharges = function(){
	return{
		xtype: 'fieldset'
	    ,title: 'Other Charges'
	    ,id: 'newloanOtherCharges'
	    ,disabled: true
	    ,layout: 'form'
	    ,columnWidth: 0.5
	    ,height: 300
	    ,bodyStyle:{'padding':'10px'}	
	    ,items: [new Ext.form.ComboBox({
            fieldLabel: 'Charge Code'
            ,id: 'charge_code'
    	    ,typeAhead: true
    	    ,triggerAction: 'all'
    	    ,lazyRender:true
    	    ,store: pecaDataStores.newloanOTCmboxStore
    	    ,mode: 'local'
    	    ,valueField: 'charge_code'
    	    ,displayField: 'charge_description'
    	    ,anchor: '75%'
    	    ,forceSelection: true
    	    ,submitValue: false
    	    ,emptyText: 'Please Select'
        })
	    ,{
            layout: 'column'
            ,border: false
            ,items: [{
	            labelWidth: 100
	            ,labelAlign: 'left'
	            ,layout: 'form'
	            ,border: false
	            ,items: [{
	            	xtype: 'moneyfield'
                    ,fieldLabel: 'Amount'
                    ,id: 'newloanOTAmount'
                    ,submitValue: false
                	,width: 150
 	                ,maxLength: 16
 	                ,maxValue: 9999999999.99
 	               ,autoCreate: {tag: 'input', type: 'text', maxlength: '13'}
                }]
            }
            ,{
                xtype: 'button'
            	,text: 'Add'
                ,id: 'newloanOtherChargesAddBtn'
				,iconCls: 'icon_ext_add'
                ,width: 75
                ,handler: function(){
            		if(Ext.getCmp('newloanOtherCharges').findById('charge_code').getValue() == '') {
						showExtInfoMsg( "Please select a charge to add.");
						return;
					}
            		else if((Ext.getCmp('newloanOtherCharges').findById('newloanOTAmount').getValue() == '0.00') || (Ext.getCmp('newloanOtherCharges').findById('newloanOTAmount').getValue() == '') || (Ext.getCmp('newloanOtherCharges').findById('newloanOTAmount').getValue() == null)){
						showExtInfoMsg( "Please enter charge amount.");
						return;
					}
					
					var chargeID = Ext.getCmp('newloanDetail').getForm().findField('newloan[loan_no]').getValue() +":"+Ext.getCmp('newloanOtherCharges').findById('charge_code').getValue();
					var chargeCode = Ext.getCmp('newloanOtherCharges').findById('charge_code').getValue();
					var chargeCodeIndex = pecaDataStores.newloanOTCmboxStore.findExact('charge_code',chargeCode);
					var chargeDescription = pecaDataStores.newloanOTCmboxStore.getAt(chargeCodeIndex).get('transcharge[charge_description]');
					
                	var rec = new pecaDataStores.newloanOtherChargesStore.recordType({
						'id': chargeID
        				,'loan_no' : Ext.getCmp('newloanDetail').getForm().findField('newloan[loan_no]').getValue()
        				,'transaction_code' : Ext.getCmp('newloanOtherCharges').findById('charge_code').getValue()
						,'transaction_description': chargeDescription
        				,'amount' : Ext.getCmp('newloanOtherCharges').findById('newloanOTAmount').getValue()
        			});
					
					var rowCount = pecaDataStores.newloanOtherChargesStore.getCount();
					var isDuplicateCharge = false;
					
					if(rowCount > 0){
						for(var i = 0; i < rowCount; i++){
							var tempVar = pecaDataStores.newloanOtherChargesStore.getAt(i);
							if((tempVar.get('id')) == chargeID){
								isDuplicateCharge = true;
								break;
							}
						}
					}
					
					if(isDuplicateCharge){
						showExtInfoMsg("Duplicate charge entry.");
					}
					else{
						pecaDataStores.newloanOtherChargesStore.insert(0, rec);
						Ext.getCmp('newloanOtherCharges').setChargeTotal();
					}
					//newloanDetail().computeInterestRate();
    		    }
            }
           ,{
        	   	xtype: 'button'
                ,text: 'Update'
                ,id: 'newloanOtherChargesUpdateBtn'
                ,hidden: true
                ,width: 100
                ,handler: function(){
        	   		Ext.getCmp('newloanOtherChargesAddBtn').show();
        	   		Ext.getCmp('newloanOtherChargesUpdateBtn').hide();
					if((Ext.getCmp('newloanOtherCharges').findById('newloanOTAmount').getValue() == '0.00') || (Ext.getCmp('newloanOtherCharges').findById('newloanOTAmount').getValue() == '') || (Ext.getCmp('newloanOtherCharges').findById('newloanOTAmount').getValue() == null)){
            			showExtInfoMsg( "Please enter charge amount.");
						return;
						}
						
            		var rec = Ext.getCmp('newloanOtherChargesList').getSelectionModel().getSelected();
    		        if (!rec) {
    		        	showExtInfoMsg( "Please select a charge to update.");
    		            return false;
    		        }    				
        			rec.set('amount', Ext.getCmp('newloanOtherCharges').findById('newloanOTAmount').getValue());
        			Ext.getCmp('newloanOtherCharges').findById('charge_code').setDisabled(false);
					Ext.getCmp('newloanOtherCharges').setChargeTotal();
					//newloanDetail().computeInterestRate();
    		    }
            }]
        }
        ,{	
			layout: 'fit'
            ,defaultType: 'grid'
			,items: [newloanOtherChargesList()]
		}]
		,setChargeTotal: function(){
		
			var rowCount = pecaDataStores.newloanOtherChargesStore.getCount();
			var chargeTotal = 0.00;		
			
			if(rowCount > 0){
				for(var i = 0; i < rowCount; i++){
					var tempVar = pecaDataStores.newloanOtherChargesStore.getAt(i);
					chargeTotal += tempVar.get('amount');
				}
			}
			Ext.getCmp('newloanDetail').getForm().findField('newloan[service_fee_amount]').setValue(chargeTotal);
			Ext.getCmp('newloanDetail').getForm().findField('newloan[service_fee_amount]').focus(true, 250);
			Ext.getCmp('newloanOTAmount').focus(true, 250);
			newloanDetail().computeInterestRate();		
		}
	};
};

var newloanOtherChargesList = function(){
	return {
		xtype: 'grid'
		,id: 'newloanOtherChargesList'
		,titlebar: false
		,store: pecaDataStores.newloanOtherChargesStore
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 200
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit: true
			,scrollOffset: 0
		}
		,cm: newloanOtherChargesColumns
		,tbar:[{
			text:'Remove'
			,tooltip:'Delete Selected Charge Code'
			,iconCls: 'icon_ext_del'
			,scope:this
			,handler:function(btn) {
				var index = Ext.getCmp('newloanOtherChargesList').getSelectionModel().getSelected();
		        if (!index) {
		        	showExtInfoMsg( "Please select a charge to delete.");
		            return false;
		        }
				Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
					if(btn=='yes') {
						pecaDataStores.newloanOtherChargesStore.remove(index);
						Ext.getCmp('newloanOtherChargesAddBtn').show();
		            	Ext.getCmp('newloanOtherChargesUpdateBtn').hide();
		            	Ext.getCmp('newloanOtherCharges').findById('charge_code').setDisabled(false);
						Ext.getCmp('newloanOtherCharges').setChargeTotal();
						//newloanDetail().computeInterestRate();
					}
				});
			}
		}]
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.newloanOtherChargesStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
		,listeners: {
            rowclick: function(g, index, ev) {
				Ext.getCmp('newloanOtherChargesAddBtn').hide();
            	Ext.getCmp('newloanOtherChargesUpdateBtn').show();    
                var rec = g.store.getAt(index);
                Ext.getCmp('newloanOtherCharges').findById('charge_code').setValue(rec.data.transaction_code);
                Ext.getCmp('newloanOtherCharges').findById('newloanOTAmount').setValue(rec.data.amount);
                
                Ext.getCmp('newloanOtherCharges').findById('charge_code').setDisabled(true);            
            }
        }

	};
};

var newloanCoMaker = function(){
	return{
		xtype: 'fieldset'
		,id: 'newloanCoMaker'
	    ,title: 'Co-Maker'
	    ,height: 300
	    ,layout: 'form'
	    ,columnWidth: 0.5
	    ,bodyStyle:{'padding':'10px'}	
	    ,items: [{
            layout: 'column'
            ,border: false
            ,items: [{
                labelWidth: 1
                ,labelAlign: 'left'
                ,layout: 'form'
                ,columnWidth: 0.33
                ,border: false
                ,items: [{
                	xtype:'checkbox'
	                ,boxLabel: 'No Co-Maker'
	                ,id: 'newloan_comaker'
					,name: 'newloan[comaker_cb]'
	                ,anchor:'95%'
                	,disabled: true
                }]
            }
            ,{
                labelWidth: 1
                ,labelAlign: 'left'
                ,layout: 'form'
                ,columnWidth: 0.33
                ,border: false
                ,items: [{
                	xtype:'checkbox'
	                ,boxLabel: 'Pensioned'
	                ,id: 'newloan_pension'
					,name: 'newloan[pension_cb]'
	                ,anchor:'95%'
	                ,disabled: true
					
                }]
            }
            ,{
                labelWidth: 1
                ,labelAlign: 'left'
                ,layout: 'form'
                ,columnWidth: 0.33
                ,border: false
                ,items: [{
                	xtype: 'moneyfield'
                    ,name: 'newloan[pension]'
                	,anchor:'95%'
 	                ,maxLength: 16
 	                ,disabled: true
 	                ,maxValue: 9999999999.99
 	               ,autoCreate: {tag: 'input', type: 'text', maxlength: '13'}
                }]
            }]
	    }
	    ,{
            layout: 'column'
            ,border: false
            ,items: [{
                labelWidth: 1
                ,labelAlign: 'left'
                ,layout: 'form'
                ,columnWidth: 0.30
                ,border: false
                ,items: [{
                    xtype: 'textfield'
                    ,id: 'newloanCoMakerID'	
                    ,anchor: '100%'
                    ,emptyText: 'ID'
                	,submitValue: false	
					,style: 'text-align:right'
                }]
            }
            ,{
                labelWidth: 1
                ,labelAlign: 'left'
                ,layout: 'form'
                ,columnWidth: 0.30
                ,border: false
                ,items: [{
                    xtype: 'textfield'
                	,id: 'newloanCoMakerLastName'
                    ,anchor: '100%'
                    ,emptyText: 'Last Name'
                	,submitValue: false
                }]
            }
            ,{
	            labelWidth: 1
	            ,labelAlign: 'left'
	            ,layout: 'form'
	            ,columnWidth: 0.30
	            ,border: false
	            ,items: [{
                    xtype: 'textfield'
                	,id: 'newloanCoMakerFirstName'
                    ,anchor: '100%'
                    ,emptyText: 'First Name'
                	,submitValue: false
                }]
            }
            ,{
            	width: 75
            	,id: 'newloanCoMakerSearchBtn'
                ,xtype: 'button'
                ,text: 'Search'
				,iconCls: 'icon_ext_search'
            	,handler: function(){
	        		pecaDataStores.newloanCoMakeremployeeStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
	        		newloan_EmpCoMakerListWin().show();
			    }
	        }]
        }
        ,{	
			layout: 'fit'
            ,defaultType: 'grid'
			,items: [newloan_CoMakerList()]
		}]
	};
};

var newloan_employeeList = function(){
	return {
		xtype: 'grid'
		,id: 'newloan_employeeList'
		,titlebar: false
		,store: pecaDataStores.newloanemployeeStore
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
					Ext.getCmp('newloanDetail').getForm().findField('newloan[loan_code]').clearValue();
					Ext.getCmp('newloanDetail').getForm().findField('newloan[employee_id]').setValue(rec.get('employee_id'));
					pecaDataStores.newloanLTStore.load({params:{'employee_id':rec.get('employee_id')}});
					Ext.getCmp('newloanDetail').getForm().findField('last_name').setValue(rec.get('last_name'));
					Ext.getCmp('newloanDetail').getForm().findField('first_name').setValue(rec.get('first_name'));
					Ext.getCmp('newloanDetail').getForm().findField('newloan[employee_id]').focus('',250);
					Ext.getCmp('newloan_employeeListWin').close.defer(1,Ext.getCmp('newloan_employeeListWin'));
				}
			}
		}
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.newloanemployeeStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: 'No records found'
	    })
	};
};

var newloan_employeeListWin = function(){
	return new Ext.Window({
		id: 'newloan_employeeListWin'
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
		,items:[ newloan_employeeList() ]
        ,buttons:[{
 			text: 'Cancel'
 			,iconCls: 'icon_ext_cancel'
 		    ,handler : function(btn){
 				Ext.getCmp('newloan_employeeListWin').close();				
 		    }
 		}]
	});
};

var newloan_RLList = function(){
	return {
		xtype: 'grid'
		,id: 'newloan_RLList'
		,titlebar: false
		,store: pecaDataStores.newloanRLStore
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
		,cm: newloanRLColumns
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
					var frm = Ext.getCmp('newloanDetail').getForm(); 
					frm.findField('newloan[restructure_no]').setValue(rec.get('loan_no'));
					frm.findField('newloan[restructure_amount]').setValue(rec.get('balance'));
					frm.findField('newloan[restructure_amount]')._onBlur(frm.findField('newloan[restructure_amount]'));
					
					/*var loan_month = formatDate(rec.get('loan_date'));
    				var amort_month = new Date(loan_month).add(Date.MONTH,1).getFirstDateOfMonth();
                	var frm = Ext.getCmp('newloanDetail').getForm();
            		frm.findField('newloan[amortization_startdate]').setValue(amort_month);
					frm.findField('newloan[loan_date]').setValue(rec.get('loan_date'));
					frm.findField('newloan[amortization_startdate]').validate();
					
					
					frm.findField('newloan[principal]').setValue(rec.get('principal'));
					frm.findField('newloan[principal]')._onBlur(frm.findField('newloan[principal]'));
					
					frm.findField('newloan[term]').setValue(rec.get('term'));
					frm.findField('newloan[interest_rate]').setValue(rec.get('interest_rate'));
					newloanDetail().computeInterestRate();*/
					
					newloanDetail().computeLoanProceeds();
					
					Ext.getCmp('newloan_RLListWin').close.defer(1,Ext.getCmp('newloan_RLListWin'));
				}
			}
		}
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.newloanRLStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: 'No records found'
	    })
	};
};

var newloan_RLListWin = function(){
	return new Ext.Window({
		id: 'newloan_RLListWin'
		,title: 'Loan List'
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
		,items:[ newloan_RLList() ]
        ,buttons:[{
 			text: 'Cancel'
 			,iconCls: 'icon_ext_cancel'
 		    ,handler : function(btn){
 				Ext.getCmp('newloan_RLListWin').close();				
 		    }
 		}]
	});
};

var newloan_CoMakerList = function(){
	return {
		xtype: 'grid'
		,id: 'newloan_CoMakerList'
		,titlebar: false
		,store: pecaDataStores.newloanCoMakerStore
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 200
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit:true
			,scrollOffset:0
		}
		,cm: newLoanCoMakerColumns
		,tbar:[{
			text:'Remove'
			,tooltip:'Delete Selected Co-Maker'
			,iconCls: 'icon_ext_del'
			,scope:this
			,handler:function(btn) {
				var index = Ext.getCmp('newloan_CoMakerList').getSelectionModel().getSelected();
		        if (!index) {
		        	showExtInfoMsg( "Please select a Co-Maker to delete.");
		            return false;
		        }
				Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
					if(btn=='yes') {
						pecaDataStores.newloanCoMakerStore.remove(index);
					}
				});
			}
		}]
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.newloanCoMakerStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: 'No records found'
	    })
	};
};

var newloan_EmpCoMakerList = function(){
	return {
		xtype: 'grid'
		,id: 'newloan_EmpCoMakerList'
		,titlebar: false
		,store: pecaDataStores.newloanCoMakeremployeeStore
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
					if( rec.get('employee_id') == Ext.getCmp('newloanDetail').getForm().findField('newloan[employee_id]').getValue()){
						showExtErrorMsg( "Employee can't be a co-maker of the applied loan.");
					}else{						
	        			var data = new pecaDataStores.newloanCoMakerStore.recordType({
	        				'employee_id' : rec.get('employee_id')
	        				,'last_name' : rec.get('last_name')
	        				,'first_name' : rec.get('first_name')
	        			});
	        			pecaDataStores.newloanCoMakerStore.insert(0, data);
					}
					Ext.getCmp('newloan_EmpCoMakerListWin').close.defer(1,Ext.getCmp('newloan_EmpCoMakerListWin'));
				}
			}
		}
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.newloanCoMakeremployeeStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: 'No records found'
	    })
	};
};

var newloan_EmpCoMakerListWin = function(){
	return new Ext.Window({
		id: 'newloan_EmpCoMakerListWin'
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
		,items:[ newloan_EmpCoMakerList() ]
        ,buttons:[{
 			text: 'Cancel'
 			,iconCls: 'icon_ext_cancel'
 		    ,handler : function(btn){
 				Ext.getCmp('newloan_EmpCoMakerListWin').close();				
 		    }
 		}]
	});
};