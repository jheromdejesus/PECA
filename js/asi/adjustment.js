//Let's pretend we rendered our grid-columns with meta-data from our ORM framework.
var adustmentColumns =  new Ext.grid.ColumnModel( 
	[	
	 	new Ext.grid.CheckboxSelectionModel(),
		{id: 'transaction_no', header: 'Transaction No', width: 15, sortable: true, dataIndex: 'transaction_no', align: 'right'}
		,{header: 'Transaction Type', width: 35, sortable: true, dataIndex: 'transaction_type', align: 'left'}
		,{header: 'Employee ID', width: 10, sortable: true, dataIndex: 'employee_id', align: 'left'}
		,{header: 'Employee Name', width: 30, sortable: true, dataIndex: 'employee_name', align: 'left'}
		,{header: 'Amount', width: 15, sortable: true, dataIndex: 'amount', align: 'right', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000.00');}}
		,{header: 'Transaction Group', width: 15, sortable: true, hidden:true, dataIndex: 'transaction_group'}
	]
);

var adjustmentList = function(){
	return {
		xtype: 'grid'
		,id: 'adjustmentList'
		,titlebar: false
		,store: pecaDataStores.AdjustmentStore
		,region: 'center'
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,loadMask: true
		,sm: new Ext.grid.CheckboxSelectionModel({
			listeners: {
				'selectionchange': function() {
					var hd = Ext.fly(this.grid.getView().innerHd).child('div.x-grid3-hd-checker');
					if (this.getCount() < this.grid.getStore().getCount()) {
						hd.removeClass('x-grid3-hd-checker-on');
					} else {
						if (this.grid.getStore().getCount() == 0){
							hd.removeClass('x-grid3-hd-checker-on');
						} else{
							hd.addClass('x-grid3-hd-checker-on');
						}
					}
				}
			}
		})
		,viewConfig: {
			forceFit:true
			,scrollOffset:15
		}
		,cm: adustmentColumns
		,listeners:{
			'rowclick':{
				scope:this
				,fn:function(grid, row, e) {
					var frm = Ext.getCmp('adjustment').getForm();
					var items = Ext.getCmp('adjustmentList').getSelectionModel().getSelections();
					
					if (items.length == 1){
						var rec = items[0];
						frm.findField('adjustment[transNo]').setValue(rec.get('transaction_no'));
						frm.findField('adjustment[transaction_type]').setValue(rec.get('transaction_type'));
						frm.findField('adjustment[employee_id]').setValue(rec.get('employee_id'));
						frm.findField('adjustment[employee_name]').setValue(rec.get('employee_name'));
						frm.findField('adjustment[amt]').setValue(rec.get('amount'));
						//frm.findField('adjustment[ex-amount]').setValue(rec.get('amount'));
						frm.findField('adjustment[transaction_group]').setValue(rec.get('transaction_group'));
						
						//for amount field
						if (frm.findField('adjustment[transaction_group]').getValue() =='PD' ||
								frm.findField('adjustment[transaction_group]').getValue() =='LP'){
//							alert(frm.findField('adjustment[transaction_group]').getValue());
							frm.findField('adjustment[amt]').setReadOnly(false);
							frm.findField('adjustment[amt]').removeClass('x-item-disabled');
							
							frm.findField('adjustment[amt]').focus(true,true);
						} else{
							//for comma of amount
							frm.findField('adjustment[amt]').setReadOnly(true);
							frm.findField('adjustment[amt]').addClass('x-item-disabled');
							frm.findField('adjustment[amt]').focus(true,true);
							frm.findField('adjustment[transNo]').focus(false,true);
						}
					
					} else{
						frm.findField('adjustment[transNo]').setValue('');
						frm.findField('adjustment[transaction_type]').setValue('');
						frm.findField('adjustment[employee_id]').setValue('');
						frm.findField('adjustment[employee_name]').setValue('');
						frm.findField('adjustment[amt]').setValue('');
						frm.findField('adjustment[ex-amount]').setValue('');
						frm.findField('adjustment[transaction_group]').setValue('');
						
						frm.findField('adjustment[amt]').setReadOnly(true);
					}
					
				}
			}		
		}
		,tbar:[{
			text:'Delete'
			,id: 'adjustmentDeleteID'
			,tooltip:'Delete Selected Rows'
			,iconCls: 'icon_ext_del'
			,scope:this
			,handler : function(btn){
				var frm = Ext.getCmp('adjustment').getForm();
				var items = Ext.getCmp('adjustmentList').getSelectionModel().getSelections();
				var rec = new Array();
	            Ext.each(items, function(r){	            	
	            	rec.push(r.get('transaction_no'));
	            });
	            
	            if (items.length > 0){
	            	Ext.Msg.confirm('Confirm Action','Are you sure you want to delete the selected record(s)?',function(btn) {
	    				if(btn=='yes') {
	    					frm.submit({
	    		    			url: '/adjustment/delete' 
	    		    			,method: 'POST'
	    		    			,clientValidation: false
	    		    			,params: {'adjustment[data]': Ext.encode(rec)
	    	            			,user: _USER_ID
	    		            		,auth:_AUTH_KEY}
	    		    			,waitMsg: 'Deleting Data...'
	    		    			,success: function(form, action) {
//	    		    				loadStore();
	    		    				clearDetail();
	    		    				//pecaDataStores.AdjustmentStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});	
									if (pecaDataStores.AdjustmentStore.getCount() % MAX_PAGE_SIZE == 1){
										pecaDataStores.AdjustmentStore.load({params: {start:pecaDataStores.AdjustmentStore.getTotalCount() - MAX_PAGE_SIZE - 1, limit:MAX_PAGE_SIZE}});
									} else{
										pecaDataStores.AdjustmentStore.reload();
									}		
	    		    				showExtInfoMsg( action.result.msg);
	    		    			}
	    		    			,failure: function(form, action) {
	    							showExtErrorMsg( action.result.msg);
	    		    			}	
	    		    		});
	    				}
	    			});
	            } else{
	            	showExtInfoMsg('Please select transaction(s).');
	            }
		    }
		},'-']
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.AdjustmentStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
		

	};
};

var adjustment = function(){
	return{
		xtype:'form'
		,id:'adjustment'
		,region:'center'
		,anchor: '100%'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,buttons:[{
			text: 'Save'
			,id: 'adjustmentSaveID'
			,iconCls: 'icon_ext_save'
		    ,handler : function(btn){
				var frm = Ext.getCmp('adjustment').getForm();
				var famount = frm.findField('adjustment[amt]');
				var fgroup = frm.findField('adjustment[transaction_group]');
				
    			if(famount.value == '' || famount.value == null){
    				if(!famount.readOnly){
    					famount.setValue(0.00);
    				}
    			}
				
				if (fgroup.value == 'PD' || fgroup.value == 'LP'){
					if(frm.isValid()){
						frm.submit({
			    			url: '/adjustment/update' 
			    			,params: {auth:_AUTH_KEY
									,user: _USER_ID}
			    			,method: 'POST'
			    			,waitMsg: 'Updating Data...'
			    			,success: function(form, action) {
//			    				loadStore();
			    				clearDetail();
    		    				pecaDataStores.AdjustmentStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});	    	
			    				showExtInfoMsg( action.result.msg);
			    			}
			    			,failure: function(form, action) {
		    					showExtErrorMsg( action.result.msg);
			    			}	
			    		});
					}
				} else if (fgroup.value == ''){
					showExtInfoMsg('Please select one transaction to adjust.');
				}else{
					showExtInfoMsg('Only Payroll Deduction and Loan Payment Transactions are allowed for adjustments.');
				}	
				
		    }
		}]
		,listeners:{
			'render':{
				scope:this
				,fn:function(form){
					//save button(update)
					if(_PERMISSION[117]==0){
						Ext.getCmp('adjustmentSaveID').setDisabled(true);	
					}else{
						Ext.getCmp('adjustmentSaveID').setDisabled(false);
					}
					//delete button
					if(_PERMISSION[24]==0){
						Ext.getCmp('adjustmentDeleteID').setDisabled(true);	
					}else{
						Ext.getCmp('adjustmentDeleteID').setDisabled(false);
					}
				}
			}
		}
	    ,items: [{
            labelWidth: 150,
            labelAlign: 'left',
            layout: 'form',
            border: false,
            padding: 1,
            items: [
                {
                    xtype: 'combo',
                    fieldLabel: 'Trasaction Group'
                    ,anchor: '50%'
                	,"hiddenName": "adjustment[transGrp]"
                    ,typeAhead: true
	        	    ,triggerAction: 'all'
	        	    ,lazyRender:true
	        	    ,store: pecaDataStores.tgStore
	        	    ,mode: 'local'
	        	    ,valueField: 'code'
	        	    ,displayField: 'name'
                    ,emptyText: 'Please Select'
                    ,forceSelection: true
	        	    ,submitValue: false
	        	    ,listeners:{
            			'change':{
            				scope:this
            				,fn:function(cb, val1, val2){
			                	var frm = Ext.getCmp('adjustment').getForm();
			                	var cb_trans = frm.findField('adjustment[transCode]');
			                	cb_trans.reset();
			                	pecaDataStores.transcodeAdjStore.baseParams = {auth:_AUTH_KEY, filter:val1};
			                	pecaDataStores.transcodeAdjStore.load();
            				}
            			}
            		}
                },
                {
                    xtype: 'combo',
                    fieldLabel: 'Transaction Code',
                    anchor: '50%'
                	,"hiddenName": "adjustment[transCode]"
                    ,typeAhead: true
	        	    ,triggerAction: 'all'
	        	    ,lazyRender:true
	        	    ,store: pecaDataStores.transcodeAdjStore
	        	    ,mode: 'local'
	        	    ,valueField: 'transaction_code'
	        	    ,displayField: 'transaction_description'
                    ,emptyText: 'Please Select'
                    ,forceSelection: true
	        	    ,submitValue: false
                },
                {
                    xtype: 'textfield',
                    fieldLabel: 'Employee ID',
                    anchor: '35%'
                	,name: "adjustment[empid]"
                	,id: "adjustment[empid]"
                	,maxLength: 8
                	,submitValue: false
                	,autoCreate: {tag: 'input', type: 'text', maxLength: '8'}
	    			,enableKeyEvents: true
	    			,style: 'text-align: right'
	    			,listeners: {
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
                }
            ]
        },
        {
            layout: 'column',
            border: false,
            items: [
                {
	                 layout: 'form',
	                 width: 150,
	                 border: false,
	                 padding: 1,
                    items: [
                        {
                            xtype: 'label',
                            fieldLabel: 'Amount'
                        }
                    ]
                },
                {
                    labelWidth: 50,
                    labelAlign: 'left',
                    layout: 'form',
                    width: 200,
                    padding: 1,
                    items: [
                        {
                            xtype: 'moneyfield',
                            fieldLabel: 'From'
                            ,anchor: '95%'
                            ,maxLength: 16 
                            ,name: 'adjustment[fromAmt]'
                            ,id: "adjustment[fromAmt]"
                            ,submitValue: false
                            ,maxValue: 9999999999.99
        	                ,minValue: 0
//        	                ,value: 0.00
        	                ,style: 'text-align: right'
        	                ,autoCreate: {tag: 'input', type: 'text', maxLength: '16'}
    	                	,validateOnBlur: false
    	            		,validationEvent: false
        	                ,validator: function(value1){
	                        	var frm = Ext.getCmp('adjustment').getForm();
	                        	var value1 = frm.findField('adjustment[fromAmt]').getValue();
	                    		var value2 = frm.findField('adjustment[toAmt]').getValue();
	                    		
	                    		if (value2 != null && value2 != ''){
	                    			if (value1 < value2){
	                        			return true;
	                        		} else{
	                        			return 'From amount should be lesser than To amount.';
	                        		}
	                    		}else{
	                    			return true;
	                    		}
	                    		
	                    	}
	                        ,listeners:{
	                			'blur':{
	                				scope:this
	                				,fn:function(form){
	                                	var frm = Ext.getCmp('adjustment').getForm();
		                        		frm.findField('adjustment[toAmt]').validate();
	                				}
	                			}
	                		}
                        }
                    ]
                },
                {
                    labelWidth: 40,
                    labelAlign: 'left',
                    layout: 'form',
                    width: 200,
                    padding: 1,
                    border: false,
                    items: [
                        {
                        	xtype: 'moneyfield',
                            fieldLabel: 'To'
                            ,anchor: '95%'
                            ,maxLength: 16 
                            ,name: 'adjustment[toAmt]'
                            ,id: "adjustment[toAmt]"
                            ,submitValue: false
                            ,maxValue: 9999999999.99
        	                ,minValue: 0
//        	                ,value: 0.00
        	                ,style: 'text-align: right'
        	                ,autoCreate: {tag: 'input', type: 'text', maxLength: '16'}
    	                	,validateOnBlur: false
    	            		,validationEvent: false
        	                , validator: function(value2){
	                        	var frm = Ext.getCmp('adjustment').getForm();
	                    		var value1 = frm.findField('adjustment[fromAmt]').getValue();
	                    		var value2 = frm.findField('adjustment[toAmt]').getValue();
	                    		
	                    		if (value1 != null && value1 != ''){
	                    			if (value1 < value2){
	                        			return true;
	                        		} else{
	                        			return 'To amount should be greater than From amount.';
	                        		}
	                    		}else{
	                    			return true;
	                    		}
	                    		
	                    	}
	                        ,listeners:{
	                			'blur':{
	                				scope:this
	                				,fn:function(form){
	                                	var frm = Ext.getCmp('adjustment').getForm();
		                        		frm.findField('adjustment[fromAmt]').validate();
	                				}
	                			}
	                		}
                        }
                    ]
                },
                {
                    labelAlign: 'left',
                    layout: 'form',
                    padding: 1,
                    border: false,
                    items: [
                        {
                            xtype: 'button',
                            text: 'Search'
                            ,width: 70
                        	,handler : function(btn){
                        		var frm = Ext.getCmp('adjustment').getForm();
                        		if(frm.isValid()){
                        			if (frm.findField('adjustment[fromAmt]').getValue() == 0 && frm.findField('adjustment[toAmt]').getValue() == 0){
                        				frm.findField('adjustment[fromAmt]').setValue('');
                        				frm.findField('adjustment[toAmt]').setValue('');
                        			}
//                        			loadStore();
                        			clearDetail();
	    		    				pecaDataStores.AdjustmentStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});	    	
                        		}
	            		    }
                        }
                    ]
                }
            ]
        },
        {
        	layout: 'fit'
			,bodyStyle:{'padding':'10px'}	
            ,defaultType: 'grid'
            ,height: 225
			,items: [adjustmentList()]
        },
        {
            layout: 'column',
            id: 'adjustmentdtl',
            border: false,
            bodyStyle:{'padding':'10px'},	
            items: [
                {
                    xtype: 'textfield',
                    columnWidth: .1,
                    name: 'adjustment[transNo]',
                    readOnly: true,
                    cls: 'x-item-disabled'
                },
                {
                    xtype: 'textfield',
                    columnWidth: .35,
                    name: 'adjustment[transaction_type]',
                    disabled: true
                },
                {
                    xtype: 'textfield',
                    columnWidth: .1,
                    name: 'adjustment[employee_id]',
                    disabled: true
                },
                {
                    xtype: 'textfield',
                    columnWidth: .3,
                    name: 'adjustment[employee_name]',
                    disabled: true
                },
                {
                    xtype: 'moneyfield',
                    columnWidth: .15,
                    name: 'adjustment[amt]',
                    maxLength: 16
                    ,cls: 'x-item-disabled'
                    ,autoCreate: {tag: 'input', type: 'text', maxLength: '16'},
                    readOnly: true
                    ,maxValue: 9999999999.99
	                ,minValue: 0
//	                ,value: 0.00
//	                ,listeners:{
//            			'blur':{
//            				scope:this
//            				,fn:function(txt){
//                    			var frm = Ext.getCmp('adjustment').getForm();
//                    			var txt2 = frm.findField('adjustment[amt]');
//                    			if(txt2.value == '' || txt2.value == null)
//                    				txt.setValue(frm.findField('adjustment[ex-amount]').getValue());
//            				}
//            			}
//            		}
                },
                {
                    xtype: 'moneyfield',
                    columnWidth: .15,
                    name: 'adjustment[ex-amount]',
                    maxLength: 16,
                    autoCreate: {tag: 'input', type: 'text', maxLength: '16'},
                    disabled: true,
                    hidden: true
                    ,maxValue: 9999999999.99
	                ,minValue: 0
	                ,value: 0.00
                },
                {
                    xtype: 'textfield',
                    columnWidth: .15,
                    name: 'adjustment[transaction_group]',
                    disabled: true,
                    value: '',
                    hidden: true
                }
            ]
        }
        ]
	};
};

function clearDetail(){
	var frm = Ext.getCmp('adjustment').getForm();
	frm.findField('adjustment[transNo]').setValue('');
	frm.findField('adjustment[transaction_type]').setValue('');
	frm.findField('adjustment[employee_id]').setValue('');
	frm.findField('adjustment[employee_name]').setValue('');
	frm.findField('adjustment[amt]').setValue('');
	frm.findField('adjustment[ex-amount]').setValue('');
	frm.findField('adjustment[transaction_group]').setValue('');
	frm.findField('adjustment[amt]').setReadOnly(true);
	frm.findField('adjustment[amt]').addClass('x-item-disabled');
}
