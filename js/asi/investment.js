//Let's pretend we rendered our grid-columns with meta-data from our ORM framework.
var investmentColumns =  new Ext.grid.ColumnModel( 
	[
		{id: 'investment_no', header: 'Investment No', width: 100, sortable: true, dataIndex: 'newinvestment[investment_no]',align: 'center'}
		,{header: 'Transaction Description', width: 150, sortable: true, dataIndex: 'newinvestment[transaction_description]'}
		,{header: 'Supplier Name', width: 150, sortable: true, dataIndex: 'newinvestment[supplier_name]'}
		,{header: 'Placement Date', width: 80, sortable: true, dataIndex: 'newinvestment[placement_date]',align: 'center'}
		,{header: 'Investment Amount', width: 125, sortable: true, dataIndex: 'newinvestment[investment_amount]',align: 'right',renderer: function(value, rec){ 	return Ext.util.Format.number(value,'0,000.00');}}
	]
);

var investmentDetail = function(){
	return {
		xtype:'form'
		,id:'investmentDetail'
		,region:'center'
		,title: 'Details'
		,hidden:true
		,anchor: '100%'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,reader: pecaReaders.investmentReader
		,buttons:[{
			text: 'Cancel'
			,iconCls: 'icon_ext_cancel'
		    ,handler : function(btn){
				Ext.getCmp('investmentDetail').hide();
				Ext.getCmp('investmentList').show();
				Ext.getCmp('investmentDetail').getForm().reset();
				pecaDataStores.investmentStore.reload();
		    }
		},{
			text: 'Delete'
			,iconCls: 'icon_ext_del'
			,handler: function(){
				var frm = Ext.getCmp('investmentDetail').getForm();
				frm.onDelete();
		    }
		},{
			text:'Save'
			,iconCls: 'icon_ext_save'
		    ,handler: function(){
		    	var frm = Ext.getCmp('investmentDetail').getForm();
		    	if(frm.isValid()){
			    	if (frm.isModeNew()) {
			        	frm.onSave(frm);
			    	} else {
			        	frm.onUpdate(frm);
			    	}
		    	}
		    }
		},{
			text:'Preview'
			,iconCls: 'icon_ext_preview'
		    ,handler: function(){
		    	var frm = Ext.getCmp('investmentDetail').getForm();
				var combo = frm.findField('newinvestment[supplier_id]');
				var value = combo.getValue();
				var record = combo.findRecord(combo.valueField, value);
				var txt;
				record ? txt= record.get(combo.displayField) : txt= "";
				
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
						url: "/investment/preview"
						,method: 'POST'
						,form: Ext.get('frmDownload')
						,params: {'transaction_code': Ext.getCmp('investmentDetail').getForm().findField('newinvestment[transaction_code]').getValue()
								,'investment_amount': Ext.getCmp('investmentDetail').getForm().findField('newinvestment[investment_amount]').getValue()
								,'bank': txt
								,'remarks': Ext.getCmp('investmentDetail').getForm().findField('newinvestment[remarks]').getValue()
								,auth:_AUTH_KEY}
						,isUpload: true
						,success: function(response, opts) {
							var obj = Ext.decode(response.responseText);
							if(obj.success){
								showExtInfoMsg(obj.msg);		
							}else{
								showExtErrorMsg(obj.msg);
							}
						}	
					});
				}	
		    }
		}]
		,items: [{			
			layout: 'form'
			,anchor: '100%'
			,items: [	
				{
					xtype: 'hidden'
					,name: 'frm_mode'
					,value: FORM_MODE_NEW
					,submitValue: false
					,listeners: {'change':{fn: function(obj,value){
					}}}
				}
				,{
					xtype: 'panel'
					,width: 741
					,border: true
					,padding: 10
					,items: [
						{
							layout: 'form'
							,labelWidth: 150
							,labelAlign: 'left'
							,width: 380
							,border: false
							,items: [
								{
									xtype: 'textfield'
									,fieldLabel: 'Investment No'
									,anchor: '90%'
									,name: 'newinvestment[investment_no]'
									,maxLength: 10
									,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
									,style: 'text-align: right'
									
								}
								,{
									xtype: 'combo'
									,fieldLabel: 'Investment Type'
									,anchor: '90%'
									,id: 'itype'
									,hiddenName: 'newinvestment[transaction_code]'
									,editable: false
									,typeAhead: true
									,triggerAction: 'all'
									,lazyRender:true
									,store: pecaDataStores.itypeStore
									,mode: 'local'
									,valueField: 'transaction_code'
									,displayField: 'transaction_description'									
									,forceSelection: true
									,submitValue: false
									,emptyText: 'Please Select'
									,allowBlank: false
									,required: true
								}
								,{
									xtype: 'combo'
									,fieldLabel: 'Bank'
									,anchor: '90%'
									,id: 'btype'
									,hiddenName: 'newinvestment[supplier_id]'
									,editable: false
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
									,allowBlank: false
									,required: true
								}
							]
						}
						,{
							layout: 'column'
							,width: 900
							,border: false
							,items: [
								{
									layout: 'form'
									,labelWidth: 150
									,labelAlign: 'left'
									,width: 380
									,border: false
									,items: [
										{
											xtype: 'numberfield'
											,fieldLabel: 'Placement Days'
											,anchor: '90%'
											,name: 'newinvestment[placement_days]'
											,id: 'pd'
											,allowBlank: false
											,required: true
											,maxLength: 4
											,autoCreate: {tag: 'input', type: 'text', maxlength: '4'}
											,style: 'text-align: right'
											,listeners:{
												'change':{
													fn:function() {
														Ext.getCmp('investmentDetail').findById('ia').setValue((Ext.getCmp('investmentDetail').findById('ai').getValue()) * ((Ext.getCmp('investmentDetail').findById('ir').getValue())/100) * (Ext.getCmp('investmentDetail').findById('pd').getValue()) / 360);														
														Ext.getCmp('investmentDetail').findById('md').setValue(Ext.getCmp('investmentDetail').findById('dd').getValue().add(Date.DAY, Ext.getCmp('investmentDetail').findById('pd').getValue()).format('m/j/Y'));
													}
												}
											}
										}
										,{
											xtype: 'moneyfield'
											,fieldLabel: 'Amount of Investment'
											,anchor: '90%'
											,name: 'newinvestment[investment_amount]'
											,id: 'ai'
											,allowBlank: false
											,required: true
											,maxLength: 16
											,autoCreate: {tag: 'input', type: 'text', maxlength: '16'}
											,listeners:{
												'change':{
													fn:function() {
														Ext.getCmp('investmentDetail').findById('ia').setValue((Ext.getCmp('investmentDetail').findById('ai').getValue()) * ((Ext.getCmp('investmentDetail').findById('ir').getValue())/100) * (Ext.getCmp('investmentDetail').findById('pd').getValue()) / 360);
													}
												}
											}
											
										}
										,{
											xtype: 'numberfield'
											,fieldLabel: 'Interest Rate'
											,anchor: '90%'
											,name: 'newinvestment[interest_rate]'
											,id: 'ir'
											,allowBlank: false
											,required: true
											,maxValue: 99.999
											,maxLength: 6
											,decimalPrecision: 3
											,autoCreate: {tag: 'input', type: 'text', maxlength: '6'}
											,style: 'text-align: right'
											,listeners:{
												'change':{
													fn:function() {
														Ext.getCmp('investmentDetail').findById('ia').setValue((Ext.getCmp('investmentDetail').findById('ai').getValue()) * ((Ext.getCmp('investmentDetail').findById('ir').getValue())/100) * (Ext.getCmp('investmentDetail').findById('pd').getValue()) / 360);
													}
												}
											}
										}
										,{
											xtype: 'textarea'
											,fieldLabel: 'Remarks'
											,name: 'newinvestment[remarks]'
											,maxLength: 50
											,anchor: '95%'
										}
									]
								}
								,{
									layout: 'form'
									,labelWidth: 150
									,labelAlign: 'left'
									,width: 380
									,border: false
									,items: [
										{
											xtype: 'datefield'
											,fieldLabel: 'Placement Date'
											,anchor: '90%'
											,name: 'newinvestment[placement_date]'
											,id: 'dd'
											,allowBlank: false
											,required: true
											,maxLength: 10
											,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
											,style: 'text-align: right'
											,listeners:{
												'select':{
													fn:function() {
														Ext.getCmp('investmentDetail').findById('md').setValue(Ext.getCmp('investmentDetail').findById('dd').getValue().add(Date.DAY, Ext.getCmp('investmentDetail').findById('pd').getValue()).format('m/j/Y'));														
													}
												}
											}
											,validationEvent: 'change'
										}
										,{
											xtype: 'textfield'
											,fieldLabel: 'Maturity Date'
											,anchor: '90%'
											,name: 'newinvestment[maturity_date]'
											,id: 'md'
											,readOnly: true	
											,maxLength: 10
											,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
											,style: 'text-align: right'	
											,validationEvent: 'change'
										}
										,{
											xtype: 'moneyfield'
											,fieldLabel: 'Interest Amount'
											,anchor: '90%'
											,name: 'newinvestment[interest_amount]'
											,id: 'ia'
											,readOnly: true	
											,required: true
										}
									]
								}
							]
						}
					]
				}
			]

		}]
	    ,isModeNew: function() {
	    	return (Ext.getCmp('investmentDetail').getForm().findField('frm_mode').getValue() == FORM_MODE_NEW);
	    }
	    ,setModeNew: function() {
	    	Ext.getCmp('investmentDetail').getForm().findField('frm_mode').setValue(FORM_MODE_NEW);
	    	Ext.getCmp('investmentDetail').buttons[0].setVisible(true);  //cancel button
	    	Ext.getCmp('investmentDetail').buttons[1].setVisible(false);  //delete button
	    	Ext.getCmp('investmentDetail').buttons[2].setVisible(true);  //save button
			
	    	Ext.getCmp('investmentDetail').getForm().findField('newinvestment[transaction_code]').focus('',250);
			Ext.getCmp('investmentDetail').getForm().findField('newinvestment[placement_date]').setDisabled(false);
			Ext.getCmp('investmentDetail').getForm().findField('newinvestment[maturity_date]').setReadOnly(true);
			
			Ext.getCmp('investmentDetail').getForm().findField('newinvestment[placement_date]').setValue(_TODAY);
			Ext.getCmp('investmentDetail').getForm().findField('newinvestment[investment_amount]').setValue(0);
			Ext.getCmp('investmentDetail').getForm().findField('newinvestment[interest_amount]').setValue(0);
			Ext.getCmp('investmentDetail').getForm().findField('newinvestment[interest_rate]').setValue(0.000);
			Ext.getCmp('investmentDetail').getForm().findField('newinvestment[placement_days]').setValue(0);
			Ext.getCmp('investmentDetail').findById('md').setValue(Ext.getCmp('investmentDetail').findById('dd').getValue().add(Date.DAY, Ext.getCmp('investmentDetail').findById('pd').getValue()).format('m/j/Y'));
			
			Ext.getCmp('investmentDetail').getForm().findField('newinvestment[investment_no]').setVisible(false);
			
		}
		,setModeUpdate: function() {
			Ext.getCmp('investmentDetail').getForm().findField('frm_mode').setValue(FORM_MODE_UPDATE);
			Ext.getCmp('investmentDetail').buttons[0].setVisible(true);  //cancel button
	    	Ext.getCmp('investmentDetail').buttons[1].setVisible(true);  //delete button
	    	Ext.getCmp('investmentDetail').buttons[2].setVisible(true);  //save button

	    	//can't update record
			if(_PERMISSION[127]==0){
				Ext.getCmp('investmentDetail').buttons[2].setDisabled(true);	
			}
			//can't delete record
			if(_PERMISSION[32]==0){
				Ext.getCmp('investmentDetail').buttons[1].setDisabled(true);	
			}
			
	    	Ext.getCmp('investmentDetail').getForm().findField('newinvestment[investment_no]').focus('',250);
			Ext.getCmp('investmentDetail').getForm().findField('newinvestment[investment_no]').setReadOnly(true);
			Ext.getCmp('investmentDetail').getForm().findField('newinvestment[investment_no]').removeClass('x-item-disabled');			
			Ext.getCmp('investmentDetail').getForm().findField('newinvestment[placement_date]').setDisabled(true);
			Ext.getCmp('investmentDetail').getForm().findField('newinvestment[maturity_date]').setReadOnly(true);
			Ext.getCmp('investmentDetail').getForm().findField('newinvestment[investment_no]').addClass('x-item-disabled');
			Ext.getCmp('investmentDetail').getForm().findField('newinvestment[investment_no]').setVisible(true);
	    }
		,onSave: function(frm){
			frm.submit({
    			url: '/investment/add' 
    			,method: 'POST'
    			,params: {auth:_AUTH_KEY
							, 'newinvestment[created_by]': _USER_ID}
    			,waitMsg: 'Creating new investment...'
    			,success: function(form, action) {
				showExtInfoMsg(action.result.msg);
    				frm.setModeUpdate();
					Ext.getCmp('investmentDetail').getForm().findField('newinvestment[investment_no]').setValue(action.result.investment_no);
    			}
    			,failure: function(form, action) {
    				if(action.result.error_code == 2 || action.result.error_code == 153){
					showExtErrorMsg(action.result.msg);
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
    			url: '/investment/update' 
    			,method: 'POST'
    			,waitMsg: 'Updating Data...'
    			,params: {
        			auth:_AUTH_KEY, 'newinvestment[modified_by]': _USER_ID	
    			}
    			,success: function(form, action) {
				showExtInfoMsg(action.result.msg);
        			frm.setModeUpdate();
    			}
    			,failure: function(form, action) {
				showExtErrorMsg(action.result.msg);
    			}	
    		});
		}
		,onDelete: function(){
			Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
				if(btn=='yes') {
					Ext.getCmp('investmentDetail').getForm().submit({
						url: '/investment/delete' 
						,method: 'POST'
						,params: {auth:_AUTH_KEY, 'newinvestment[modified_by]': _USER_ID}
						,waitMsg: 'Deleting Data...'
						,clientValidation: false
						,success: function(form, action) {
							showExtInfoMsg(action.result.msg);
			    			Ext.getCmp('investmentDetail').setModeNew();
			    			Ext.getCmp('investmentDetail').getForm().reset();
			    			Ext.getCmp('investmentDetail').hide();
							Ext.getCmp('investmentList').show();
							//pecaDataStores.investmentStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
							if (pecaDataStores.investmentStore.getCount() % MAX_PAGE_SIZE == 1){
								var page = pecaDataStores.investmentStore.getTotalCount() - MAX_PAGE_SIZE - 1;
								pecaDataStores.investmentStore.load({params: {start:page<0?0:page, limit:MAX_PAGE_SIZE}});
							} else{
								pecaDataStores.investmentStore.reload();
							}
						}
						,failure: function(form, action) {
							showExtErrorMsg(action.result.msg);
						}	
					});
				}
			});
		}
	};
};

var investmentList = function(){
	return {
		xtype: 'grid'
		,id: 'investmentList'
		,titlebar: false
		,store: pecaDataStores.investmentStore
		,region: 'center'
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 425
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit: true
			,scrollOffset: 13
		}
		,cm: investmentColumns
		
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
					Ext.getCmp('investmentList').hide();
					Ext.getCmp('investmentDetail').show();
					Ext.getCmp('investmentDetail').getForm().setModeUpdate();
					Ext.getCmp('investmentDetail').getForm().load({
				    	url: '/investment/show'
				    	,params: {'newinvestment[investment_no]':(rec.get('newinvestment[investment_no]'))
									,auth:_AUTH_KEY}
				    	,method: 'POST'
				    	,waitMsgTarget: true
					});
				}
			}
			,'render':{
				scope:this
				,fn:function(grid){
					//search textfield and button
					if(_PERMISSION[55]==0){
						Ext.getCmp('investment_no').setDisabled(true);
						Ext.getCmp('invSearchID').setDisabled(true);	
					}else{
						Ext.getCmp('investment_no').setDisabled(false);
						Ext.getCmp('invSearchID').setDisabled(false);
						pecaDataStores.investmentStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
						pecaDataStores.itypeStore.load();
						pecaDataStores.supplierStore.load();
					}
					//new button
					if(_PERMISSION[7]==0){
						Ext.getCmp('invNewID').setDisabled(true);	
					}else{
						Ext.getCmp('invNewID').setDisabled(false);
						pecaDataStores.itypeStore.load();
						pecaDataStores.supplierStore.load();
					}
					//delete button
					if(_PERMISSION[32]==0){
						Ext.getCmp('invDeleteID').setDisabled(true);	
					}else{
						Ext.getCmp('invDeleteID').setDisabled(false);
					}
				}
			}
		}
		,tbar:[{
			xtype: 'label'
			,text: 'Investment No :'
            ,fieldLabel: ' '
            ,labelSeparator: ' '
		},' '
		,{		
			xtype: 'textfield'
			,anchor: '95%'
			,id: 'investment_no'
			,style: 'text-align: right'
			,autoCreate: {tag: 'input', type: 'numeric', maxlength: '10'}
			,enableKeyEvents: true
			,style: 'text-align: right'
			,listeners: {
				specialkey: function(txt,evt){
					if (evt.getKey() == evt.ENTER) {
						pecaDataStores.investmentStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});						
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
			,id: 'invSearchID'
			,tooltip:'Search investment'
			,iconCls: 'icon_ext_search'
			,scope:this			
			,handler: function(){
				pecaDataStores.investmentStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
			}
		},'-'
		,{
			text:'New'
			,id: 'invNewID'
			,tooltip:'Add an investment'
			,iconCls: 'icon_ext_add'
			,scope:this
			,handler:function(btn) {
				Ext.getCmp('investmentList').hide();
				Ext.getCmp('investmentDetail').show();
				Ext.getCmp('investmentDetail').getForm().reset();
				investmentDetail().setModeNew();
				
			}
		},'-'
		,{
			text:'Delete'
			,id: 'invDeleteID'
			,tooltip:'Delete investment'
			,iconCls: 'icon_ext_del'
			,scope:this
			,handler:function(btn) {
				var index = Ext.getCmp('investmentList').getSelectionModel().getSelected();
		        if (!index) {
				showExtInfoMsg("Please select an investment to delete.");
		        	return false;
		        }
		        Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
					if(btn=='yes') {
				        Ext.Ajax.request({
				        	url: '/investment/delete' 
							,method: 'POST'
							,params: {'newinvestment[investment_no]':index.data.investment_no
				        				,auth:_AUTH_KEY, 'newinvestment[modified_by]': _USER_ID}
							,waitMsg: 'Deleting Data...'
							,success: function(response, opts) {
								var obj = Ext.decode(response.responseText);
								if(obj.success){
									showExtInfoMsg(obj.msg);
									//pecaDataStores.investmentStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
									if (pecaDataStores.investmentStore.getCount() % MAX_PAGE_SIZE == 1){
										var page = pecaDataStores.investmentStore.getTotalCount() - MAX_PAGE_SIZE - 1;
										pecaDataStores.investmentStore.load({params: {start:page<0?0:page, limit:MAX_PAGE_SIZE}});
									} else{
										pecaDataStores.investmentStore.reload();
									}
								}else{
									showExtErrorMsg(obj.msg);
								}
							}
							,failure: function(response, opts) {
								var obj = Ext.decode(response.responseText);
								showExtErrorMsg(obj.msg);
							}
			        	});
					}
		        });
			}
		}]
		
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.investmentStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};

