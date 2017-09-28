//Let's pretend we rendered our grid-columns with meta-data from our ORM framework.
var imColumns =  new Ext.grid.ColumnModel( 
	[
		{id: 'investment_no', header: 'Investment No', width: 100, sortable: true, dataIndex: 'newinvestment[investment_no]',align: 'center'}
		,{header: 'Supplier Name', width: 150, sortable: true, dataIndex: 'newinvestment[supplier_name]'}
		,{header: 'Placement Date', width: 80, sortable: true, dataIndex: 'newinvestment[placement_date]',align: 'center'}
		,{header: 'Maturity Date', width: 80, sortable: true, dataIndex: 'newinvestment[maturity_date]',align: 'center'}
		,{header: 'Investment Amount', width: 125, sortable: true, dataIndex: 'newinvestment[investment_amount]',align: 'right',renderer: function(value, rec){ 	return Ext.util.Format.number(value,'0,000.00');}}
	]
);


var imDetail = function(){
	return {
		xtype:'form'
		,id:'imDetail'
		,region:'center'
		,title: 'Details'
		,hidden:true
		,anchor: '100%'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,reader: pecaReaders.imReader
		,buttons:[{
			text: 'Cancel'
			,iconCls: 'icon_ext_cancel'
		    ,handler : function(btn){
				Ext.getCmp('imDetail').hide();
				Ext.getCmp('imList').show();
				Ext.getCmp('imDetail').getForm().reset();
				pecaDataStores.imStore.reload();
		    }
		},{
			text: 'Delete'
			,handler: function(){
				var frm = Ext.getCmp('imDetail').getForm();
				frm.onDelete();
		    }
		},{
			text:'Save'
			,iconCls: 'icon_ext_save'
		    ,handler: function(){
		    	var frm = Ext.getCmp('imDetail').getForm();
		    	if(frm.isValid()){
			    	if (frm.isModeNew()) {
			        	frm.onSave(frm);
			    	} else {
			        	frm.onUpdate(frm);
			    	}
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
					layout: 'column'
					,width: 780
					,padding: 10
					,items: [
						{
							layout: 'form'
							,labelWidth: 150
							,labelAlign: 'left'
							,width: 350
							,hideBorders: true
							,border: false
							,items: [
								{
									xtype: 'textfield'
									,fieldLabel: 'Investment No'
									,anchor: '95%'
									,name: 'newinvestment[investment_no]'
									,maxLength: 10
									,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
									,style: 'text-align: right'
								}
								,{
									xtype: 'textfield'
									,fieldLabel: 'Supplier'
									,anchor: '95%'
									,emptyText: 'Supplier ID'
									,name: 'newinvestment[supplier_id]'
									,maxLength: 10
									,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
								}
								,{
									xtype: 'textfield'
									,fieldLabel: 'Investment Type'
									,anchor: '95%'
									,name: 'transaction_description'
									,id: 'td'
									,maxLength: 30
									,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
								}
								,{
									xtype: 'moneyfield'
									,fieldLabel: 'Amount of Investment'
									,anchor: '95%'
									,name: 'newinvestment[investment_amount]'
									,id: 'amti'
									,maxLength: 16
									,autoCreate: {tag: 'input', type: 'text', maxlength: '16'}
								}
								,{
									xtype: 'datefield'
									,fieldLabel: 'Maturity Date'
									,anchor: '95%'
									,name: 'newinvestment[maturity_date]'
									,maxLength: 10
									,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
									,style: 'text-align: right'
									,validationEvent: 'change'
								}
								,{
									xtype: 'combo'
									,fieldLabel: 'Action Code'
									,anchor: '95%'
									,id: 'acode'
									,hiddenName: 'newinvestment[action_code]'
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
										,data: [['P', 'Rollover Principal'], ['W', 'Withdraw All']]
									})
									,valueField: 'action_code'
									,displayField: 'displayText'
									,listeners:{
										'select':{
											fn:function() {
												if (this.getValue() =='P'){
													Ext.getCmp('imDetail').findById('rolloverForm').setVisible(true);
													Ext.getCmp('imDetail').findById('a').enable();
													Ext.getCmp('imDetail').findById('b').enable();
													Ext.getCmp('imDetail').findById('c').enable();
													Ext.getCmp('imDetail').findById('d').enable();
													Ext.getCmp('imDetail').getForm().findField('newinvestment[rollover_placement_date]').setValue(_TODAY);
													Ext.getCmp('imDetail').getForm().findField('newinvestment[rollover_placement_days]').setValue(0);
													Ext.getCmp('imDetail').getForm().findField('newinvestment[rollover_interest_rate]').setValue(0);
													Ext.getCmp('imDetail').getForm().findField('newinvestment[rollover_maturity_date]').setReadOnly(true);													
													Ext.getCmp('imDetail').getForm().findField('newinvestment[rollover_interest_amount]').setReadOnly(true);													
													Ext.getCmp('imDetail').findById('d').setValue(Ext.getCmp('imDetail').findById('a').getValue().add(Date.DAY, Ext.getCmp('imDetail').findById('b').getValue()).format('m/j/Y'));
														Ext.getCmp('imDetail').findById('iamt').setValue((Ext.getCmp('imDetail').findById('amti').getValue()) * ((Ext.getCmp('imDetail').findById('c').getValue())/100) * (Ext.getCmp('imDetail').findById('b').getValue()) / 360);
												}
												else{	
													Ext.getCmp('imDetail').findById('rolloverForm').setVisible(false);
													Ext.getCmp('imDetail').findById('a').disable(true);
													Ext.getCmp('imDetail').findById('b').disable(true);
													Ext.getCmp('imDetail').findById('c').disable(true);
													Ext.getCmp('imDetail').findById('d').disable(true);
													
												}
											}
										}
									}
									
								}
								,{
									xtype: 'combo'
									,fieldLabel: 'Maturity Code'
									,anchor: '95%'
									,id: 'mctype'
									,hiddenName: 'newinvestment[maturity_code]'
									,editable: false
									,typeAhead: true
									,triggerAction: 'all'
									,lazyRender:true
									,store: pecaDataStores.mctypeStore
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
									xtype: 'numberfield'
									,fieldLabel: 'OR Number'
									,anchor: '95%'
									,name: 'newinvestment[or_no]'
									,maxLength: 10
									,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
									,style: 'text-align: right'
									//,allowBlank: false
									,hidden: true
									//,required: true
								}
								,{
									xtype: 'datefield'
									,fieldLabel: 'OR Date'
									,anchor: '95%'
									,name: 'newinvestment[or_date]'
									,maxLength: 10
									,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
									,style: 'text-align: right'
									,allowBlank: false
									,required: true
									,validationEvent: 'change'
									,hidden: true
								}
								,{
									xtype: 'moneyfield'
									,fieldLabel: 'Interest Income'
									,anchor: '95%'
									,name: 'interest_amount'
									,maxLength: 16
									,maxValue: 9999999999.99
									,minValue: 0
									,autoCreate: {tag: 'input', type: 'text', maxlength: '13'}
									,allowBlank: false
									,required: true
								}
							]
						}
						,{
							xtype: 'panel'
							,width: 400
							,hideBorders: true
							,border: false
							,items: [
								{
									xtype: 'panel'
									,height: 25
									,hideBorders: true
									,border: false
								}
								,{
									layout: 'form'
									,labelWidth: 150
									,labelAlign: 'left'
									,hideBorders: true
									,border: false
									,items: [
										{
											xtype: 'textfield'
											,fieldLabel: 'Label'
											,anchor: '55%'
											,hideLabel: true
											,emptyText: 'Supplier Name'
											,name: 'supplier_name'
											,maxLength: 50
											,autoCreate: {tag: 'input', type: 'text', maxlength: '50'}
										}
										,{
											xtype: 'datefield'
											,fieldLabel: 'Placement Date'
											,anchor: '85%'
											,name: 'newinvestment[placement_date]'
											,maxLength: 10
											,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
											,style: 'text-align: right'
											,validationEvent: 'change'
										}
										,{
											xtype: 'numberfield'
											,fieldLabel: 'Interest Rate'
											,anchor: '85%'
											,allowDecimals: true
											,decimalPrecision: 3
											,name: 'newinvestment[interest_rate]'
											,maxLength: 6
											,maxValue: 99.999
											,autoCreate: {tag: 'input', type: 'text', maxlength: '6'}
											,style: 'text-align: right'
										}
									]
								}
								,{
									xtype: 'panel'
									,height: 15
								}
								,{
									layout: 'form'
									,id: 'rolloverForm'
									,labelWidth: 150
									,labelAlign: 'left'
									,padding: 10
									,hideBorders: false
									,bodyborder: true
									,border: true
									,items: [
										{
											xtype: 'datefield'
											,fieldLabel: 'Placement Date'
											,anchor: '95%'
											,name: 'newinvestment[rollover_placement_date]'
											,id: 'a'
											,allowBlank: false
											,required: true
											,maxLength: 10
											,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
											,style: 'text-align: right'
											,listeners:{
												'select':{
													fn:function() {
														Ext.getCmp('imDetail').findById('d').setValue(Ext.getCmp('imDetail').findById('a').getValue().add(Date.DAY, Ext.getCmp('imDetail').findById('b').getValue()).format('m/j/Y'));
													}
												}
											}
											,validationEvent: 'change'
										}
										,{
											xtype: 'numberfield'
											,fieldLabel: 'Placement Days'
											,anchor: '95%'
											,name: 'newinvestment[rollover_placement_days]'
											,id: 'b'
											,allowDecimals: false
											,allowBlank: false
											,required: true
											,maxLength: 4
											,autoCreate: {tag: 'input', type: 'text', maxlength: '4'}
											,style: 'text-align: right'
											,listeners:{
												'change':{
													fn:function() {
														var dt = (Ext.getCmp('imDetail').findById('amti').getValue()) * ((Ext.getCmp('imDetail').findById('c').getValue())/100) * (Ext.getCmp('imDetail').findById('b').getValue()) / 360;
														Ext.getCmp('imDetail').findById('iamt').setValue(dt);
														
														Ext.getCmp('imDetail').findById('d').setValue(Ext.getCmp('imDetail').findById('a').getValue().add(Date.DAY, Ext.getCmp('imDetail').findById('b').getValue()).format('m/j/Y'));
													}
												}
											}
										}
										,{
											xtype: 'numberfield'
											,fieldLabel: 'Interest Rate'
											,anchor: '95%'
											,allowDecimals: true
											,name: 'newinvestment[rollover_interest_rate]'
											,id: 'c'
											,allowBlank: false
											,required: true
											,decimalPrecision: 3
											,maxLength: 6
											,maxValue: 99.999
											,autoCreate: {tag: 'input', type: 'text', maxlength: '6'}
											,style: 'text-align: right'
											,listeners:{
												'change':{
													fn:function() {
														var dt = (Ext.getCmp('imDetail').findById('amti').getValue()) * ((Ext.getCmp('imDetail').findById('c').getValue())/100) * (Ext.getCmp('imDetail').findById('b').getValue()) / 360;
														Ext.getCmp('imDetail').findById('iamt').setValue(dt);
													}
												}
											}
										}
										,{
											xtype: 'moneyfield'
											,fieldLabel: 'Interest Amount'
											,anchor: '95%'
											,name: 'newinvestment[rollover_interest_amount]'
											,id: 'iamt'
											,maxLength: 16
											,maxValue: 9999999999.99
											,minValue: 0
											,autoCreate: {tag: 'input', type: 'text', maxlength: '13'}
										}
										,{
											xtype: 'textfield'
											,fieldLabel: 'Maturity Date'
											,anchor: '95%'
											,name: 'newinvestment[rollover_maturity_date]'
											,id: 'd'
											,allowBlank: false
											,required: true
											,maxLength: 10
											,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
											,style: 'text-align: right'
											,validationEvent: 'change'
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
	    	return (Ext.getCmp('imDetail').getForm().findField('frm_mode').getValue() == FORM_MODE_NEW);
	    }
	    ,setModeNew: function() {
	    	Ext.getCmp('imDetail').getForm().findField('frm_mode').setValue(FORM_MODE_NEW);
	    	Ext.getCmp('imDetail').buttons[0].setVisible(true);  //cancel button
	    	Ext.getCmp('imDetail').buttons[1].setVisible(false);  //delete button
	    	Ext.getCmp('imDetail').buttons[2].setVisible(true);  //save button
	    	//Ext.getCmp('imDetail').getForm().findField('newpayroll[investment_no]').focus('',250);
			Ext.getCmp('imDetail').getForm().findField('newinvestment[investment_no]').setReadOnly(false);
			Ext.getCmp('imDetail').getForm().findField('newinvestment[investment_no]').removeClass('x-item-disabled');			
			//Ext.getCmp('imDetail').findById('rolloverForm').setVisible(false);
		}
		,setModeUpdate: function() {
			Ext.getCmp('imDetail').getForm().findField('frm_mode').setValue(FORM_MODE_UPDATE);
			Ext.getCmp('imDetail').buttons[0].setVisible(true);  //cancel button
	    	Ext.getCmp('imDetail').buttons[1].setVisible(false);  //delete button
	    	Ext.getCmp('imDetail').buttons[2].setVisible(true);  //save button
	    	
	    	//can't update record
			if(_PERMISSION[128]==0){
				Ext.getCmp('imDetail').buttons[2].setDisabled(true);	
			}
					
	    	//Ext.getCmp('imDetail').getForm().findField('newpayroll[last_name]').focus('',250);
			
			Ext.getCmp('imDetail').getForm().findField('newinvestment[investment_no]').setReadOnly(true);
			Ext.getCmp('imDetail').getForm().findField('newinvestment[investment_no]').addClass('x-item-disabled');
			Ext.getCmp('imDetail').getForm().findField('newinvestment[supplier_id]').setReadOnly(true);
			Ext.getCmp('imDetail').getForm().findField('newinvestment[supplier_id]').addClass('x-item-disabled');
			Ext.getCmp('imDetail').getForm().findField('transaction_description').setReadOnly(true);
			Ext.getCmp('imDetail').getForm().findField('transaction_description').addClass('x-item-disabled');
			Ext.getCmp('imDetail').getForm().findField('newinvestment[investment_amount]').setReadOnly(true);
			Ext.getCmp('imDetail').getForm().findField('newinvestment[investment_amount]').addClass('x-item-disabled');
			Ext.getCmp('imDetail').getForm().findField('newinvestment[maturity_date]').setReadOnly(true);
			Ext.getCmp('imDetail').getForm().findField('newinvestment[maturity_date]').addClass('x-item-disabled');
			Ext.getCmp('imDetail').getForm().findField('supplier_name').setReadOnly(true);
			Ext.getCmp('imDetail').getForm().findField('supplier_name').addClass('x-item-disabled');
			Ext.getCmp('imDetail').getForm().findField('newinvestment[placement_date]').setReadOnly(true);
			Ext.getCmp('imDetail').getForm().findField('newinvestment[placement_date]').addClass('x-item-disabled');
			Ext.getCmp('imDetail').getForm().findField('newinvestment[interest_rate]').setReadOnly(true);		
			Ext.getCmp('imDetail').getForm().findField('newinvestment[interest_rate]').addClass('x-item-disabled');	
			Ext.getCmp('imDetail').findById('rolloverForm').setVisible(false);
			
			
	    }
		,onSave: function(frm){
			frm.submit({
    			url: '/investment_maturity/add' 
    			,method: 'POST'
    			,params: {auth:_AUTH_KEY
							, 'newinvestment[created_by]': _USER_ID}
    			,waitMsg: 'Creating new investment...'
    			,success: function(form, action) {
				showExtInfoMsg(action.result.msg);
    				frm.setModeUpdate();
    			}
    			,failure: function(form, action) {
    				if(action.result.error_code == 2){
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
				url: '/investment_maturity/update' 
				,method: 'POST'
				,waitMsg: 'Updating Data...'
				,params: {
					'newinvestment[interest_income]': Ext.getCmp('imDetail').getForm().findField('interest_amount').getValue()
					,auth:_AUTH_KEY, 'newinvestment[modified_by]': _USER_ID	
				}
				,success: function(form, action) {
					showExtInfoMsg(action.result.msg);
				}
				,failure: function(form, action) {
					showExtErrorMsg(action.result.msg);
				}	
			});			
		}
	};
};

var imList = function(){
	return {
		xtype: 'grid'
		,id: 'imList'
		,titlebar: false
		,store: pecaDataStores.imStore
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
		,cm: imColumns
		
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
					Ext.getCmp('imList').hide();
					Ext.getCmp('imDetail').show();
					Ext.getCmp('imDetail').getForm().setModeUpdate();
					Ext.getCmp('imDetail').getForm().load({
				    	url: '/investment_maturity/show'
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
					if(_PERMISSION[56]==0){
						Ext.getCmp('inv_maturity_no').setDisabled(true);
						Ext.getCmp('invmaturitySearchID').setDisabled(true);	
					}else{
						Ext.getCmp('inv_maturity_no').setDisabled(false);
						Ext.getCmp('invmaturitySearchID').setDisabled(false);
						pecaDataStores.imStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
						pecaDataStores.mctypeStore.load();
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
			,id: 'inv_maturity_no'
			,style: 'text-align: right'
			,autoCreate: {tag: 'input', type: 'numeric', maxlength: '10'}
			,enableKeyEvents: true
			,style: 'text-align: right'
			,listeners: {
				specialkey: function(txt,evt){
					if (evt.getKey() == evt.ENTER) {
						pecaDataStores.imStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});					
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
			,id: 'invmaturitySearchID'
			,tooltip:'Search investment'
			,iconCls: 'icon_ext_search'
			,scope:this			
			,handler: function(){
				pecaDataStores.imStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
			}
		}]
		
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.imStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};

