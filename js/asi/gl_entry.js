var glHdrColumns =  new Ext.grid.ColumnModel( 
	[
		{id: 'gl_code', header: "GL Code", width: 100, sortable: true, dataIndex: 'gl_code_formatted'}
		,{header: "GL Description", width: 200, sortable: true, dataIndex: 'gl_description_formatted'}
		,{header: "Particulars", width: 200, sortable: true, dataIndex: 'particulars_formatted'}
	]
);

var glDtlColumns =  new Ext.grid.ColumnModel( 
	[
	 	{id: "id", hidden:true, dataIndex: 'id'}
		,{header: "Account No", width: 100, sortable: true, dataIndex: 'gldtlaccount_no_formatted'}
		,{header: "Account Name", width: 100, sortable: true, dataIndex: 'gldtlaccount_name_formatted'}
		,{header: "D/C", width: 200, sortable: true, dataIndex: 'debit_credit'}
		,{header: "Field Name", width: 200, sortable: true, dataIndex: 'glDtl[field_name]'}
	]
);

var glDtlProxy = new Ext.data.HttpProxy({
	api: {
	    read    : '/gl_entries/readDtl',
	    create  : '/gl_entries/addDtl',
	    update  : '/gl_entries/updateDtl',
	    destroy : '/gl_entries/deleteDtl'
	}
	,listeners:{
		'beforeload':{
			scope:this
			,fn:function(dataproxy,params ){
				params.gl_code = Ext.getCmp('glDetail').getForm().findField('glHdr[gl_code]').getValue();
			}
		}
	}
});

var glDtlWriter = new Ext.data.JsonWriter({
    encode: true
    ,writeAllFields: true
    ,listfull: true
});

var addGLAction = new Ext.Action({
    text: 'Add'
    ,itemId: 'addGLAction'
    ,width: 75
    ,handler: function(){
		var frm = Ext.getCmp('glDtlForm').getForm();
		
		var oldVal = Ext.getCmp('glDtlForm').getForm().findField('glDtl[field_name]').getValue();
    	var fieldVal = Ext.getCmp('glDtlForm').getForm().findField('glDtl[field]').getValue() + ' ';
    	var operatorVal = Ext.getCmp('glDtlForm').getForm().findField('glDtl[operator]').getValue() + ' ';
		var transGroup = Ext.getCmp('glDtlForm').getForm().findField('glDtl[transaction_group]').getValue() + ' ';
		
		if(!Ext.getCmp('glDtlForm').getForm().findField('glDtl[field]').disabled && fieldVal != ' '){
			Ext.getCmp('glDtlForm').getForm().findField('glDtl[field_name]').setValue(oldVal + fieldVal);
			Ext.getCmp('glDtlForm').getForm().findField('glDtl[field]').disable();
			Ext.getCmp('glDtlForm').getForm().findField('glDtl[operator]').enable();
		}else if(operatorVal != ' ' && fieldVal != ' '){
			Ext.getCmp('glDtlForm').getForm().findField('glDtl[field_name]').setValue(oldVal + operatorVal);
			Ext.getCmp('glDtlForm').getForm().findField('glDtl[field]').enable();
			Ext.getCmp('glDtlForm').getForm().findField('glDtl[operator]').disable();
		}
		
		if(fieldVal != ' '){
			Ext.getCmp('glDtlForm').getForm().findField('glDtl[transaction_group]').disable();
		}
		
		if(transGroup==' '|| fieldVal== ' '){
			showExtInfoMsg("No transaction group / field type selected");
		}
    }
});

var glList = function(){
	return {
		xtype: 'grid'
		,id: 'glList'
		,titlebar: false
		,store: pecaDataStores.glHdrStore
		,region: 'center'
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 425
		//,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit: true
			,scrollOffset: 0
		}
		,cm: glHdrColumns
		
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
					Ext.getCmp('glList').hide();
					Ext.getCmp('glDetail').show();
					Ext.getCmp('glDetail').getForm().setModeUpdate();
					Ext.getCmp('glDetail').getForm().load({
				    	url: '/gl_entries/showHdr'
				    	,params: {'glHdr[gl_code]':(rec.get('glHdr[gl_code]'))
							,auth:_AUTH_KEY}
				    	,method: 'POST'
				    	,waitMsgTarget: true
				    	,success: function(form, action) {
				    		pecaDataStores.glDtlStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
				    	}
					});
				}
			}
			,'render':{
				scope:this
				,fn:function(grid){
					pecaDataStores.glHdrStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
					pecaDataStores.coaGlStore.load();
					//new button
					if(_PERMISSION[4]==0){
						Ext.getCmp('glNewID').setDisabled(true);	
					}else{
						Ext.getCmp('glNewID').setDisabled(false);
					}
					//delete button
					if(_PERMISSION[29]==0){
						Ext.getCmp('glDeleteID').setDisabled(true);	
					}else{
						Ext.getCmp('glDeleteID').setDisabled(false);
					}
				}
			}
		}
		,tbar:[{
			text:'New'
			,id: 'glNewID'
			,tooltip:'Add a GL Entry'
			,iconCls: 'icon_ext_add'
			,scope:this
			,handler:function(btn) {
				Ext.getCmp('glList').hide();
				pecaDataStores.glDtlStore.load();
				glDetail().setModeNew();
				Ext.getCmp('glDetail').show();
			}
		},'-'
		,{
			text:'Delete'
			,id: 'glDeleteID'
			,tooltip:'Delete Selected GL Code'
			,iconCls: 'icon_ext_del'
			,scope:this
			,handler:function(btn) {
				var index = Ext.getCmp('glList').getSelectionModel().getSelected();
		        if (!index) {
		        	showExtInfoMsg("Please select a GL entry to delete.");
		            return false;
		        }
		        Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
					if(btn=='yes') {
				        Ext.Ajax.request({
				        	url: '/gl_entries/deleteHdr' 
							,method: 'POST'
							,params: {'glHdr[gl_code]':index.data.gl_code
										,'glHdr[gl_description]':index.data.gl_description
				        				,auth:_AUTH_KEY, 'glHdr[modified_by]': _USER_ID}
							,waitMsg: 'Deleting Data...'
							,success: function(response, opts) {
								var obj = Ext.decode(response.responseText);
								if(obj.success){
									showExtInfoMsg(obj.msg);
									// pecaDataStores.glHdrStore.load();
									if (pecaDataStores.glHdrStore.getCount() % MAX_PAGE_SIZE == 1){
										pecaDataStores.glHdrStore.load({params: {start:pecaDataStores.glHdrStore.getTotalCount() - MAX_PAGE_SIZE - 1, limit:MAX_PAGE_SIZE}});
									} else{
										pecaDataStores.glHdrStore.reload();
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
		},'-'
		,{
			text:'Clone'
			,tooltip:'Clone Selected GL Code'
			,iconCls: 'icon_ext_del'
			,scope:this
			,handler:function(btn) {
				var index = Ext.getCmp('glList').getSelectionModel().getSelected();
		        if (!index) {
		        	showExtInfoMsg("Please select an Account to clone.");
		            return false;
		        }
				Ext.getCmp('glList').hide();
				Ext.getCmp('glDetail').getForm().findField('glHdr[gl_code]').setValue(index.data.gl_code);
				pecaDataStores.glDtlStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
				glDetail().setModeClone();
				Ext.getCmp('glDetail').getForm().findField('glHdr[gl_code]').allowBlank = true;
				Ext.getCmp('glDetail').getForm().findField('glHdr[gl_code]').setValue('');
				Ext.getCmp('glDetail').show();
			}
		}]
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.glHdrStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};

var glDtlForm = function(){
	return {
		xtype:'form'
		,id:'glDtlForm'
		,anchor: '100%'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,reader: pecaReaders.glDtlReader
		,buttons:[{
			text: 'Cancel'
			,iconCls: 'icon_ext_cancel'
		    ,handler : function(btn){
				Ext.getCmp('glDtlFormWin').close();				
		    }
		},{
			text:'Save'
			,iconCls: 'icon_ext_save'
			,formBind:true	
		    ,handler: function(){
		    	if(Ext.getCmp('glDtlForm').getForm().findField('glDtl[field_name]').getValue() == ''){
		    		showExtInfoMsg('Please select Transaction Group Field and/or Operator for the Field Name.');		    		
		    	}else{
					var frm = Ext.getCmp('glDtlForm').getForm();
			    	if(frm.isValid()){
				    	if (frm.isModeNew()) {
				        	frm.onSave(frm);
				    	} else {
				        	frm.onUpdate(frm);
				    	}
			    	}
		    	}
		    }
		}]
		,items: [{
			layout: 'form'
            ,defaultType: 'textfield'
            ,labelWidth: 125
            ,defaults: {width: 300}
            ,items: [{
			    xtype: 'hidden'
			    ,name: 'frm_mode'
			    ,value: FORM_MODE_NEW
			    ,submitValue: false
			    ,listeners: {'change':{fn: function(obj,value){
                }}}
			},{
			    xtype: 'hidden'
			    ,name: 'glDtl[gl_code]'
			},{
				xtype: 'fieldset'
	            ,autoHeight: true
	            ,anchor: '100%'
	            ,defaultType: 'radio' // each item will be a radio button
	            ,items: [new Ext.form.ComboBox({
	                fieldLabel: 'Account No'
                    ,hiddenName: 'glDtl[account_no]'
            	    ,typeAhead: true
            	    ,triggerAction: 'all'
            	    ,lazyRender:true
            	    ,store: pecaDataStores.coaGlStore
            	    ,mode: 'local'
            	    ,valueField: 'account_no'
            	    ,displayField: 'account_no_name'
            	    ,anchor: '75%'
            	    ,emptyText: 'Please Select'
             	    ,forceSelection: true
             	    ,required: true
             	    ,submitValue: false
             	    ,allowBlank: false
					,listeners:{
						scope: this,
							'select': function() {
									selected_text = Ext.getCmp('glDtlForm').getForm().findField('glDtl[account_no]').lastSelectionText;
									formatted = Ext.util.Format.htmlDecode(selected_text);
									Ext.getCmp('glDtlForm').getForm().findField('glDtl[account_no]').setValue(formatted);
								}	
            		}	
            	}),{
	            	xtype: 'radiogroup',

	                items: [
	                    {id: 'debit_credit_D', boxLabel: 'Debit', name: 'debit_credit', inputValue: 'D', checked: true}
	                    ,{id: 'debit_credit_C', boxLabel: 'Credit', name: 'debit_credit', inputValue: 'C'}
	                ]
	            }]
			},{
    			layout: 'form'
				,xtype:'fieldset'	
				,title: 'Field Name'	
	            ,defaultType: 'textfield'
	            ,labelWidth: 125
	            ,defaults: {width: 300}
				,anchor: '100%'
	            ,items: [{
	            	xtype: 'panel'
	            	,anchor: '100%'
	            	,layout:'column'
	        	        ,items:[{
	        	            columnWidth:.75
	        	            ,layout: 'form'
	        	            ,items: [{
	        	            	xtype: 'textfield'
	        	                ,fieldLabel: 'Field Name'
        		                ,name: 'glDtl[field_name]'
        		                ,anchor:'100%'
        		                ,readOnly: true
        		                ,maxLength: 50
        		                ,autoCreate: {tag: 'input', type: 'text', maxlength: '50'}
	        	            	,cls: 'x-item-disabled'
        		            }]
	        	        },{
	        	            columnWidth:.25
	        	            ,xtype: 'panel'
        	                ,border: false
        	                ,height: '20%'
        	                ,anchor: '100%'
        	                ,bodyCfg: { tag:'center'}
        	                ,items: [ new Ext.Button({
        	                    text: 'Clear'
        	                    ,width: 75
        	                    //todo: Add clear handler
        	                    ,handler: function(){
        	                		Ext.getCmp('glDtlForm').getForm().findField('glDtl[field_name]').setValue('');
        	                		Ext.getCmp('glDtlForm').getForm().findField('glDtl[transaction_group]').setValue('');
        	                		Ext.getCmp('glDtlForm').getForm().findField('glDtl[transaction_group]').enable();
        	                		Ext.getCmp('glDtlForm').getForm().findField('glDtl[field]').disable();
        	                		Ext.getCmp('glDtlForm').getForm().findField('glDtl[field]').setValue('');
        	                		Ext.getCmp('glDtlForm').getForm().findField('glDtl[operator]').disable();
        	                		Ext.getCmp('glDtlForm').getForm().findField('glDtl[operator]').setValue('');
        	                		addGLAction.setDisabled(true);
		    	    		    }
        	                })]
	        	        }]
	            },new Ext.form.ComboBox({
	                fieldLabel: 'Transaction Group'
                    ,hiddenName: 'glDtl[transaction_group]'
            	    ,typeAhead: true
            	    ,triggerAction: 'all'
            	    ,lazyRender:true
            	    ,store: pecaDataStores.tgGLEntryStore
            	    ,mode: 'local'
            	    ,valueField: 'code'
            	    ,displayField: 'name'
            	    ,anchor: '75%'
        	    	,emptyText: 'Please Select'
             	    ,forceSelection: true
             	    ,submitValue: false
             	   	,listeners:{
						'change':{
            				scope:this
            				,fn:function(e,newValue,oldValue){
				            	Ext.getCmp('glDtlForm').getForm().findField('glDtl[field]').enable();
				            	Ext.getCmp('glDtlForm').getForm().findField('glDtl[field]').setValue('');
								pecaDataStores.glTransfieldsStore.load({params: {'transaction_group': newValue}});	            				
            				}
            			}
            		}
            	}),new Ext.form.ComboBox({
                    fieldLabel: 'Field Type'
                    ,hiddenName: 'glDtl[field]'
            	    ,typeAhead: true
            	    ,triggerAction: 'all'
            	    ,lazyRender:true
            	    ,store: pecaDataStores.glTransfieldsStore
            	    ,mode: 'local'
            	    ,valueField: 'fields'
            	    ,displayField: 'fields'
            	    ,anchor: '75%'
        	    	,emptyText: 'Please Select'
        	    	,forceSelection: true
	        	    ,submitValue: false
	        	    ,disabled: true
	        	    ,listeners:{
            			'change':{
            				scope:this
            				,fn:function(e,newValue,oldValue){
            					addGLAction.setDisabled(false);
            				}
            			}
            		}
            	}),new Ext.form.ComboBox({
                    fieldLabel: 'Operator'
                    ,hiddenName: 'glDtl[operator]'
            	    ,typeAhead: true
            	    ,triggerAction: 'all'
            	    ,lazyRender:true
            	    ,store: pecaDataStores.mdasStore
            	    ,mode: 'local'
            	    ,valueField: 'operator'
            	    ,displayField: 'operator'
            	    ,anchor: '50%'
        	    	,emptyText: 'Please Select'
             	    ,forceSelection: true
             	    ,disabled: true
            	})
            	,{
	            	xtype: 'panel'
	                ,border: false
	                ,height: '20%'
	                ,anchor: '100%'
	                ,bodyCfg: { tag:'center'}
	                ,items: [ new Ext.Button(addGLAction)]
	            }]
			}]
		}]
	    ,isModeNew: function() {
	    	return (Ext.getCmp('glDtlForm').getForm().findField('frm_mode').getValue() == FORM_MODE_NEW);
	    }
	    ,setModeNew: function() {
	    	Ext.getCmp('glDtlForm').getForm().findField('frm_mode').setValue(FORM_MODE_NEW);
	    	Ext.getCmp('glDtlForm').buttons[0].setVisible(true);  //cancel button
	    	//Ext.getCmp('glDtlForm').buttons[1].setVisible(false);  //delete button
	    	Ext.getCmp('glDtlForm').buttons[1].setVisible(true);  //save button
	    	//Ext.getCmp('glDtlForm').getForm().findField('glDtl[account_no]').focus('',500);
			Ext.getCmp('glDtlForm').getForm().findField('glDtl[account_no]').enable();
			Ext.getCmp('glDtlForm').getForm().findField('glDtl[transaction_group]').enable();
			Ext.getCmp('debit_credit_C').setDisabled(false);
			Ext.getCmp('debit_credit_D').setDisabled(false);
			addGLAction.setDisabled(true);
		}
		,setModeUpdate: function() {
			Ext.getCmp('glDtlForm').getForm().findField('frm_mode').setValue(FORM_MODE_UPDATE);
			Ext.getCmp('glDtlForm').buttons[0].setVisible(true);  //cancel button
	    	//Ext.getCmp('glDtlForm').buttons[1].setVisible(true);  //delete button
	    	Ext.getCmp('glDtlForm').buttons[1].setVisible(true);  //save button
			Ext.getCmp('glDtlForm').getForm().findField('glDtl[account_no]').disable();
			Ext.getCmp('glDtlForm').getForm().findField('glDtl[transaction_group]').disable();
			Ext.getCmp('debit_credit_C').setDisabled(true);
			Ext.getCmp('debit_credit_D').setDisabled(true);
			addGLAction.setDisabled(true);;
			//Ext.getCmp('addGLAction').focus('',500);
	    }
		,onSave: function(frm){
			var debit_credit = 'D';
			if( Ext.getCmp('debit_credit_C').checked == true )
				debit_credit = 'C';
			
			var rec = new pecaDataStores.glDtlStore.recordType({
				'glDtl[gl_code]' : Ext.getCmp('glDetail').getForm().findField('glHdr[gl_code]').getValue()
				,'debit_credit' : debit_credit
				,'glDtl[account_no]' : Ext.getCmp('glDtlForm').getForm().findField('glDtl[account_no]').getValue()
				,'glDtl[field_name]' : Ext.getCmp('glDtlForm').getForm().findField('glDtl[field_name]').getValue()
			});
			pecaDataStores.glDtlStore.insert(0, rec);
			Ext.getCmp('glDtlFormWin').close();
			// pecaDataStores.glDtlStore.reload();
		}
		,onUpdate: function(frm){
			Ext.getCmp('glDtlForm').getForm().updateRecord(Ext.getCmp('glDtlForm').record);
			Ext.getCmp('glDtlFormWin').close();
			// pecaDataStores.glDtlStore.reload();
		}
	};
};

var glDtlFormWin = function(){
	return new Ext.Window({
		id: 'glDtlFormWin'
		,title: 'General Ledger Detail Form'
		,frame: true
		,layout: 'form'
		,width:600
		,autoHeight: true
		,plain: true
		,modal: true
		,resizable: false
		,closable: false
		,constrainHeader: true
		,bodyStyle:{"padding":"5px"}
		,items:[ glDtlForm() ]
	});
};

var glDtlList = function(){
	return {
		xtype: 'grid'
		,id: 'glDtlList'
		,titlebar: false
		,store: pecaDataStores.glDtlStore
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 150
		//,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit: true
			,scrollOffset: 0
		}
		,cm: glDtlColumns
		,record: null
		
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
					pecaDataStores.tgGLEntryStore.load();
					pecaDataStores.coaGlStore.load();
					glDtlFormWin().show();					
					Ext.getCmp('glDtlForm').record = rec;
					Ext.getCmp('glDtlForm').getForm().loadRecord(rec);
					Ext.getCmp('glDtlForm').setModeUpdate();
					//set debit_credit radio button
					Ext.getCmp('debit_credit_D').setValue(false);
					Ext.getCmp('debit_credit_C').setValue(false);
					var rdBtn = rec.data.debit_credit;
					if(rdBtn == ''){
						rdBtn = 'debit_credit_D';
					}else{
						rdBtn = 'debit_credit_' + rdBtn;
					}
					Ext.getCmp(rdBtn).setValue(true);
				}
			}
		}
		,tbar:[{
			text:'New'
			,tooltip:'Add a GL Detail'
			,iconCls: 'icon_ext_add'
			,scope:this
			,handler:function(btn) {
				glDtlFormWin().show();
				glDtlForm().setModeNew();
				pecaDataStores.tgGLEntryStore.load();
				pecaDataStores.coaGlStore.load();
			}
		},'-'
		,{
			text:'Remove'
			,tooltip:'Delete Selected GL Detail'
			,iconCls: 'icon_ext_del'
			,scope:this
			,handler:function(btn) {
				var index = Ext.getCmp('glDtlList').getSelectionModel().getSelected();
		        if (!index) {
		        	showExtInfoMsg("Please select an Account to delete.");
		            return false;
		        }
				Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
					if(btn=='yes') {
				        Ext.getCmp('glDtlList').store.remove(index);
						// pecaDataStores.glDtlStore.reload();
					}
				});
			}
		}]
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.glDtlStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};

var glDetail = function(){
	return {
		xtype:'form'
		,id:'glDetail'
		,region:'center'
		,title: 'Details'
		,hidden:true
		,anchor: '95%'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,reader: pecaReaders.glHdrReader
		,buttons:[{
			text: 'Cancel'
			,iconCls: 'icon_ext_cancel'
		    ,handler : function(btn){
				Ext.getCmp('glDetail').hide();
				Ext.getCmp('glDetail').getForm().reset();
				Ext.getCmp('glList').show();
				pecaDataStores.glHdrStore.reload();
		    }
		},{
			text: 'Delete'
			,iconCls: 'icon_ext_del'
			,handler: function(){
				var frm = Ext.getCmp('glDetail').getForm();
				frm.onDelete();
		    }
		},{
			text:'Save'
			,iconCls: 'icon_ext_save'
		    ,handler: function(){
				Ext.getCmp('glDetail').getForm().findField('glHdr[gl_code]').allowBlank = false;
		    	var frm = Ext.getCmp('glDetail').getForm();
		    	if(frm.isValid()){
			    	if (frm.isModeNew()) {
			        	frm.onSave(frm);
			    	} else if (frm.getFormMode() == FORM_MODE_UPDATE) {
			        	frm.onUpdate(frm);
			    	} else if (frm.getFormMode() == FORM_MODE_CLONE){
			    		frm.onClone(frm);
			    	}
		    	}
		    }
		}]
		,items: [{
			layout: 'form'
			,xtype:'fieldset'	
			,title: 'General Ledger Header'	
            ,defaultType: 'textfield'
			,anchor: '100%'
            ,labelWidth: 100
            ,defaults: {width: 300}
            ,items: [{
			    xtype: 'hidden'
			    ,name: 'frm_mode'
			    ,value: FORM_MODE_NEW
			    ,submitValue: false
			    ,listeners: {'change':{fn: function(obj,value){
                }}}
			},{
                fieldLabel: 'GL Code'
                ,name: 'glHdr[gl_code]'
                ,anchor:'20%'
                ,allowBlank: false
                ,required: true
                ,maxLength: 4
                ,autoCreate: {tag: 'input', type: 'text', maxlength: '4'}
            },{
                fieldLabel: 'GL Description'
                ,name: 'glHdr[gl_description]'
                ,anchor:'40%'
                ,allowBlank: false
                ,required: true
                ,maxLength: 30
                ,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
            },{
                fieldLabel: 'Particulars'
                ,name: 'glHdr[particulars]'
                ,anchor:'50%'
                ,allowBlank: false
                ,required: true
                ,maxLength: 50
                ,autoCreate: {tag: 'input', type: 'text', maxlength: '50'}
            }]
		}
		,{
			layout: 'fit'
			,xtype:'fieldset'	
			,title: 'General Ledger Details'	
            ,defaultType: 'grid'
			,anchor: '100%'
            ,height: 225
			,items: [glDtlList()]
		}]
	    ,isModeNew: function() {
	    	return (Ext.getCmp('glDetail').getForm().findField('frm_mode').getValue() == FORM_MODE_NEW);
	    }
		,getFormMode: function(){
			return Ext.getCmp('glDetail').getForm().findField('frm_mode').getValue();
		}
		,setModeClone: function(){
			this.setModeNew();
			Ext.getCmp('glDetail').getForm().findField('frm_mode').setValue(FORM_MODE_CLONE);
		}
	    ,setModeNew: function() {
	    	Ext.getCmp('glDetail').getForm().findField('frm_mode').setValue(FORM_MODE_NEW);
	    	Ext.getCmp('glDetail').buttons[0].setVisible(true);  //cancel button
	    	Ext.getCmp('glDetail').buttons[1].setVisible(false);  //delete button
	    	Ext.getCmp('glDetail').buttons[2].setVisible(true);  //save button
	    	Ext.getCmp('glDetail').getForm().findField('glHdr[gl_code]').focus('',250);
			Ext.getCmp('glDetail').getForm().findField('glHdr[gl_code]').setReadOnly(false);
			Ext.getCmp('glDetail').getForm().findField('glHdr[gl_code]').removeClass('x-item-disabled');
			Ext.getCmp('glDtlList').getTopToolbar().setDisabled(true);
			Ext.getCmp('glDtlList').setDisabled(true);
		}
		,setModeUpdate: function() {
			Ext.getCmp('glDetail').getForm().findField('frm_mode').setValue(FORM_MODE_UPDATE);
			Ext.getCmp('glDetail').buttons[0].setVisible(true);  //cancel button
	    	Ext.getCmp('glDetail').buttons[1].setVisible(true);  //delete button
	    	Ext.getCmp('glDetail').buttons[2].setVisible(true);  //save button
	    	
	    	//can't update record
			if(_PERMISSION[123]==0){
				Ext.getCmp('glDetail').buttons[2].setDisabled(true);  //save button
			}
			//can't delete record
			if(_PERMISSION[29]==0){
				Ext.getCmp('glDetail').buttons[1].setDisabled(true);  //save button
			}
	    	
	    	Ext.getCmp('glDetail').getForm().findField('glHdr[gl_description]').focus('',250);
			Ext.getCmp('glDetail').getForm().findField('glHdr[gl_code]').setReadOnly(true);
			Ext.getCmp('glDetail').getForm().findField('glHdr[gl_code]').addClass('x-item-disabled');
			Ext.getCmp('glDtlList').getTopToolbar().setDisabled(false);
			Ext.getCmp('glDtlList').setDisabled(false);
	    }
		,onSave: function(frm){
			frm.submit({
    			url: '/gl_entries/addHdr' 
    			,method: 'POST'
    			,params: {auth:_AUTH_KEY, 'glHdr[created_by]': _USER_ID}
    			,waitMsg: 'Creating Data...'
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
	    						pecaDataStores.glDtlStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
	    					}
	    				});
    				}
    			}	
    		});
		}
		,onUpdate: function(frm){
			frm.submit({
    			url: '/gl_entries/updateHdr' 
    			,method: 'POST'
    			,waitMsg: 'Updating Data...'
    			,params: {
        			auth:_AUTH_KEY, 'glHdr[modified_by]': _USER_ID	
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
					
					Ext.getCmp('glDetail').getForm().submit({
						url: '/gl_entries/deleteHdr' 
						,method: 'POST'
						,params: {auth:_AUTH_KEY, 'glHdr[modified_by]': _USER_ID}
						,waitMsg: 'Deleting Data...'
						,clientValidation: false
						,success: function(form, action) {						
							showExtInfoMsg(action.result.msg);
							Ext.getCmp('glDetail').hide();
							Ext.getCmp('glDetail').getForm().reset();
							Ext.getCmp('glList').show();
							// pecaDataStores.glHdrStore.load();
							if (pecaDataStores.glHdrStore.getCount() % MAX_PAGE_SIZE == 1){
								pecaDataStores.glHdrStore.load({params: {start:pecaDataStores.glHdrStore.getTotalCount() - MAX_PAGE_SIZE - 1, limit:MAX_PAGE_SIZE}});
							} else{
								pecaDataStores.glHdrStore.reload();
							}							
						}
						,failure: function(form, action) {
							showExtErrorMsg(action.result.msg);
						}	
					});
				}
			});
		}
		,onClone: function(frm){
			var index = Ext.getCmp('glList').getSelectionModel().getSelected();
			frm.submit({
    			url: '/gl_entries/addClone' 
    			,method: 'POST'
    			,params: {cloneID: index.data.gl_code, auth:_AUTH_KEY, 'glHdr[created_by]': _USER_ID}
    			,waitMsg: 'Creating Data...'
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
	    						pecaDataStores.glDtlStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
	    					}
	    				});
    				}
    			}	
    		});
		}
	};
};