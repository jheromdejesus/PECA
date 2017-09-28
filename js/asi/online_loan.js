//Let's pretend we rendered our grid-columns with meta-data from our ORM framework.
//Let's pretend we rendered our grid-columns with meta-data from our ORM framework.
var ol_loanColumns =  new Ext.grid.ColumnModel( 
	[
		{header: 'Submission Date', width: 80, sortable: true, dataIndex: 'online_loan[loan_date]',align:'center'}
		//Added for 6th Enhancement
		,{header: 'Time Sent', width: 80, sortable: true, dataIndex: 'online_loan[time_sent]',align:'center'}
		,{header: 'Loan Type', width: 100, sortable: true, dataIndex: 'online_loan[loan_type]'}
		,{header: 'Employee Name', width: 150, sortable: true, dataIndex: 'online_loan[employee_name]'}
		,{header: 'Amount', width: 100, sortable: true, dataIndex: 'online_loan[amount]',align:'right', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}}
		,{header: 'Approver Name', width: 150, sortable: true, dataIndex: 'online_loan[approver_name]'}
		,{header: 'Status', width: 80, sortable: true, dataIndex: 'online_loan[status]'}
		,{header: ' ', width: 10, sortable: true, dataIndex: 'online_loan[request_no]', hidden:true}
		,{header: ' ', width: 10, sortable: true, dataIndex: 'approver1', hidden:true}
		,{header: ' ', width: 10, sortable: true, dataIndex: 'approver2', hidden:true}
		,{header: ' ', width: 10, sortable: true, dataIndex: 'approver3', hidden:true}
		,{header: ' ', width: 10, sortable: true, dataIndex: 'approver4', hidden:true}
		,{header: ' ', width: 10, sortable: true, dataIndex: 'approver5', hidden:true}
	]
);

var ol_uploadFormWin = function(){
	return new Ext.Window({
		id: 'ol_uploadFormWin'
		,title: 'Upload a File'
		,fileUpload: true
		,width: 530
		,frame: true
		,autoHeight: true
		,plain: true
		,modal: true
		,resizable: false
		,closable: false
		,constrainHeader: true
		,bodyStyle:{"padding":"5px"}
		,items:[ ol_fp()]
	});
};

var ol_loanDetail = function(){
	return {
		xtype:'form'
		,id:'ol_loanDetail'
		,region:'center'
		,title: 'Details'
		,anchor: '100%'
		//,bodyStyle:{'padding':'10px'}
		,frame: true
		,reader: pecaReaders.ol_loanReader
		,items: [{			
			layout: 'form'
			,id:'ol_loanDetail_'
			,region:'center'
			//,anchor: '100%'
			//,bodyStyle:{'padding':'10px'}
			,style: 'padding-left:10px;padding-top:10px;'
			,buttons:[
			{
				text: 'Preview'
				,iconCls: 'icon_ext_preview'
				,handler: function(){
					var frm = Ext.getCmp('ol_loanDetail').getForm();
					frm.onPreview(frm);
				}
			},{
				text: 'Delete'
				,iconCls: 'icon_ext_del'
				,handler: function(){
					var frm = Ext.getCmp('ol_loanDetail').getForm();
					frm.onDelete();
				}
			},{
				text:'Save'
				,iconCls: 'icon_ext_save'
				,handler: function(){
					var frm = Ext.getCmp('ol_loanDetail').getForm();
					if(frm.isValid()){
						if (frm.isModeNew()) {
							frm.onSave(frm,2);
						} else {
							frm.onUpdate(frm,2);
						}
					}
				}
			},{
				text: 'Send'
				,iconCls: 'icon_ext_send'
				,handler: function(){
					var frm = Ext.getCmp('ol_loanDetail').getForm();
					if(frm.isValid()){
						if (frm.isModeNew()) {
							frm.onSave(frm,1);
						} else {
							frm.onUpdate(frm,1);
						}
					}
				}
			}
			,{
				text: 'Cancel'
				,iconCls: 'icon_ext_cancel'
				,handler : function(btn){
					Ext.getCmp('ol_loanDetail').getForm().reset();
					Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOLLoan');
					pecaDataStores.ol_loanStore.reload();
				}
			}]
			,items: [	
				{
					xtype: 'hidden'
					,name: 'frm_mode'
					,value: FORM_MODE_LIST
					,submitValue: false
					,listeners: {'change':{fn: function(obj,value){
					}}}
				}
				,{
					layout: 'column'
					,border: false
					,items: [				
						{
							layout: 'form'
							,labelWidth: 150
							,labelAlign: 'left'
							,border: false
							,hideBorders: false
							,width: 350
							,items: [
								{
									xtype: 'textfield'
									,fieldLabel: 'Employee'	
									,emptyText: 'ID'	
									,anchor: '95%'
									,name: 'online_loan[employee_id]'
									,allowBlank: false
									,required: true
									,maxLength: 8
									,autoCreate: {tag: 'input', type: 'text', maxlength: '8'}
									,style: 'text-align: right'
									,readOnly:true
									,cls:'x-item-disabled'
								}
							]
						}
						,{
							layout: 'form'
							,labelWidth: 1
							,labelAlign: 'left'
							,border: false
							,hideBorders: false
							,width: 200
							,items: [
								{
									xtype: 'textfield'							
									,anchor: '95%'		
									,hideLabel: true
									,emptyText: 'Last Name'
									,name: 'online_loan[last_name]'
									,maxLength: 30
									,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
									,submitValue:false
									,readOnly:true
									,cls:'x-item-disabled'
								}
							]
						}
						,{
							layout: 'form'
							,labelWidth: 1
							,labelAlign: 'left'
							,border: false
							,width: 200
							,items: [
								{
									xtype: 'textfield'							
									,anchor: '98%'								
									,hideLabel: true
									,emptyText: 'First Name'
									,name: 'online_loan[first_name]'
									,maxLength: 30
									,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
									,submitValue:false
									,readOnly:true
									,cls:'x-item-disabled'
								}
							]
						}
						
					]
				}			
				,{
					layout: 'form'
					,labelWidth: 220
					,labelAlign: 'left'
					,layout: 'form'
					,width: 420
					,items: [
						{
							layout: 'form'
							,labelWidth: 150
							,labelAlign: 'left'
							,border: false
							,hideBorders: false
							,items: [
								{
									xtype: 'combo'
									,fieldLabel: 'Loan Type'
									,anchor: '95%'
									,hiddenName: 'online_loan[loan_code]'
									,editable: false
									,typeAhead: true
									,triggerAction: 'all'
									,lazyRender:true
									,store: pecaDataStores.ol_loantypeStore
									,mode: 'local'
									,valueField: 'loan_code'
									,displayField: 'loan_description'									
									,forceSelection: true
									,submitValue: false
									,emptyText: 'Please Select'
									,allowBlank: false
									,required: true
									,listeners:{
										'select':{
											scope:this
											,fn:function(combo,record, index) {
												var rec = combo.getStore().getAt(index);
												var frm = Ext.getCmp('ol_loanDetail').getForm();            	
												frm.findField('online_loan[interest_rate]').setValue(rec.get('employee_interest_rate'));
												
												var term = Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[term]').getValue();
												if(term <12){
													Ext.getCmp('ol_loanDetail').findById('interest_rate_amount').setValue((((Ext.getCmp('ol_loanDetail').findById('principal').getValue()) * ((Ext.getCmp('ol_loanDetail').findById('interest_rate').getValue())/100))/12)*term);
												}
												else{
													Ext.getCmp('ol_loanDetail').findById('interest_rate_amount').setValue((Ext.getCmp('ol_loanDetail').findById('principal').getValue()) * ((Ext.getCmp('ol_loanDetail').findById('interest_rate').getValue())/100));
												}
											}
										}
									}
									,maxLength: 30
									,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
								}
							]
							
						}
						,{
							xtype: 'datefield'
							,fieldLabel: 'Submission Date'
							,anchor: '95%'
							,id: 'loan_date'
							,name: 'online_loan[loan_date]'	
							,allowBlank: false
							,required: true
							,maxLength: 10
							,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
							,style: 'text-align: right'
							,validationEvent: 'change'
						}
						,{
							xtype: 'moneyfield'
							,fieldLabel: 'Principal'
							,anchor: '95%'
							,name: 'online_loan[principal]'
							,id: 'principal'
							,allowBlank: false
							,required: true
							,maxLength: 16
							,autoCreate: {tag: 'input', type: 'text', maxlength: '16'}
							,listeners:{
								'change':{
									fn:function() {
										var term = Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[term]').getValue();
										if(term <12){
											Ext.getCmp('ol_loanDetail').findById('interest_rate_amount').setValue((((Ext.getCmp('ol_loanDetail').findById('principal').getValue()) * ((Ext.getCmp('ol_loanDetail').findById('interest_rate').getValue())/100))/12)*term);
										}
										else{
											Ext.getCmp('ol_loanDetail').findById('interest_rate_amount').setValue((Ext.getCmp('ol_loanDetail').findById('principal').getValue()) * ((Ext.getCmp('ol_loanDetail').findById('interest_rate').getValue())/100));
										}
									}
								}
							}
							
						}
						,{
							xtype: 'numberfield'
							,fieldLabel: 'Terms in Months'
							,anchor: '95%'
							,name: 'online_loan[term]'
							,id: 'term'
							,allowBlank: false
							,required: true
							,maxLength: 4
							,autoCreate: {tag: 'input', type: 'text', maxlength: '4'}
							,style: 'text-align: right'
							,listeners:{
								'change':{
									fn:function() {
										var term = Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[term]').getValue();
										if(term <12){
											Ext.getCmp('ol_loanDetail').findById('interest_rate_amount').setValue((((Ext.getCmp('ol_loanDetail').findById('principal').getValue()) * ((Ext.getCmp('ol_loanDetail').findById('interest_rate').getValue())/100))/12)*term);
										}
										else{
											Ext.getCmp('ol_loanDetail').findById('interest_rate_amount').setValue((Ext.getCmp('ol_loanDetail').findById('principal').getValue()) * ((Ext.getCmp('ol_loanDetail').findById('interest_rate').getValue())/100));
										}
									}
								}
							}
						}
						,{//hidden field
							xtype: 'numberfield'
							,anchor: '95%'
							,name: 'request_no'
							,hidden:true
						}
						,{//hidden field
							xtype: 'numberfield'
							,anchor: '95%'
							,name: 'status'
							,hidden:true
						}
						,{
							layout: 'column'
							,border: false
							,items: [				
								{
									layout: 'form'
									,labelWidth: 150
									,labelAlign: 'left'
									,border: false
									,hideBorders: false
									,width: 220
									,items: [
										{
											xtype: 'moneyfield'
											,fieldLabel: 'Employee Interest Rate'
											,anchor: '98%'
											,name: 'online_loan[interest_rate]'
											,id: 'interest_rate'
											,maxLength: 6
											,autoCreate: {tag: 'input', type: 'text', maxlength: '6'}
											,style: 'text-align: right'
											,readOnly: true
											,cls: 'x-item-disabled'
										}
									]
								}
								,{
									layout: 'form'
									,labelWidth: 1
									,labelAlign: 'left'
									,border: false
									,hideBorders: false
									,width: 200
									,items: [
										{
											xtype: 'moneyfield'
											,anchor: '90%'
											,name: 'online_loan[interest_rate_amount]'
											,id: 'interest_rate_amount'
											,maxLength: 16
											,autoCreate: {tag: 'input', type: 'text', maxlength: '16'}
											,style: 'text-align: right'
											,submitValue:false
											,readOnly: true
											,cls: 'x-item-disabled'
										}
									]
								}
							]
						}					
					]					
				}
				,{
					layout: 'form'
					,labelWidth: 150
					,labelAlign: 'left'
					,border: false
					,hideBorders: false
					,width: 500
					,items: [
						{
							xtype: 'textarea'
							,fieldLabel: 'Member Remarks'
							,height: 35
							,name: 'online_loan[member_remarks]'
							,maxLength: 50
							,autoScroll: true
							,anchor: '90%'
						}
						,{
							xtype: 'textarea'
							,fieldLabel: 'PECA Remarks'
							,height: 35
							,name: 'online_loan[peca_remarks]'
							,maxLength: 50
							,autoScroll: true
							,anchor: '90%'
						}
					]
				}
				,{
					xtype: 'fieldset'
					,title: 'Upload A File'
					,layout: 'fit'	    
					,width: 550
					,autoHeight: true
					,items: [{	
						layout: 'fit'
						,defaultType: 'grid'
						,height: 150
						,items: [ol_fileList()]
					}]
				}
				,{
					layout: 'form'
					,labelWidth: 150
					,labelAlign: 'left'
					,border: false
					,hideBorders: false
					,width: 500
					,height:45
				}
				,{
					layout: 'form'
					,labelWidth: 150
					,labelAlign: 'left'
					,border: false
					,hideBorders: false
					,width: 500
					,items: [
						{
							xtype: 'textarea'
							,fieldLabel: 'Approvers'
							,height: 40
							,name: 'online_loan[approvers]'
							,autoScroll: true
							,anchor: '90%'
							,submitValue:false
							,readOnly:true
						}
					]
				}
			]

		},
		{
			layout: 'form'
			,border: false
			,hideBorders: false
			,id: 'ol_loan_detail'
			,buttons:[
			{text: 'Approve'
				,iconCls: 'icon_ext_approve'
				,hidden: true
				,handler: function(){
					var frm = Ext.getCmp('ol_loanDetail').getForm();
					frm.onApprove(frm);
				}
			},{
				text: 'Disapprove'
				,iconCls: 'icon_ext_disapprove'
				,hidden: true
				,handler: function(){
					Ext.Msg.prompt('Reason', 'Please enter the reason for disapproval:', function(btn, text){
						buttons: Ext.Msg.OKCANCEL
						if (btn == 'ok' && text.length > 0){
								var frm = Ext.getCmp('ol_loanDetail').getForm();
								frm.findField('online_loan[peca_remarks]').setValue(text);
								frm.onDisapprove(frm);
						}
						else if(btn == 'ok' && text.length == 0){
							var element = Ext.getCmp('online_loan_disapprove');
							element.handler.call(element.scope);
						}
					},this,50,Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[peca_remarks]').getValue());

				}
			}]
		}
		,{
			layout: 'form'
			,border: false
			,bodyStyle: 'padding-left:70%'
			,items: [
				{
					html: 'Save: Save Current Transaction'
				}
				,{
					html: 'Send: Send Current Transaction'
				}
				,{
					html: 'Cancel: Cancel Current'
				}
			]
		}
		]
	    ,isModeNew: function() {
	    	return (Ext.getCmp('ol_loanDetail').getForm().findField('frm_mode').getValue() == FORM_MODE_NEW);
	    }
	    ,setModeNew: function() {
	    	Ext.getCmp('ol_loanDetail').getForm().findField('frm_mode').setValue(FORM_MODE_NEW);
	    	Ext.getCmp('ol_loanDetail_').buttons[0].setVisible(false);  //preview button
	    	Ext.getCmp('ol_loanDetail_').buttons[1].setVisible(false);  //delete button
	    	Ext.getCmp('ol_loanDetail_').buttons[2].setVisible(true);  //save button
			Ext.getCmp('ol_loanDetail_').buttons[3].setVisible(true);  //send button
	    	Ext.getCmp('ol_loanDetail_').buttons[4].setVisible(true);  //cancel button
	    	Ext.getCmp('ol_loan_detail').buttons[0].setVisible(false);  //approve button
			Ext.getCmp('ol_loan_detail').buttons[1].setVisible(false);  //disapprove button
			Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[peca_remarks]').setVisible(false);
			Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[approvers]').setVisible(false);
			
			Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[principal]').setValue(0);
			Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[interest_rate]').setValue(0);
			Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[interest_rate_amount]').setValue(0);
			Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[term]').setValue(0);
			Ext.getCmp('ol_loanDetail').findById('loan_date').setValue(new Date().clearTime());
			//Ext.getCmp('ol_fileList').setDisabled(true);
			ol_clearRestrictions();
			if( _IS_ADMIN == false ){
				Ext.getCmp('ol_fileList').tbar.setVisible(true);
				Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[loan_date]').setReadOnly(true);
				Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[loan_date]').addClass('x-item-disabled');
			}
			else{
				Ext.getCmp('ol_fileList').tbar.setVisible(false);
			}
		}
		,setModeUpdate: function() {
			//Ext.getCmp('ol_fileList').setDisabled(false);
			Ext.getCmp('ol_loanDetail').getForm().findField('frm_mode').setValue(FORM_MODE_UPDATE);
			Ext.getCmp('ol_loanDetail_').buttons[0].setVisible(true);  //preview button
	    	Ext.getCmp('ol_loanDetail_').buttons[4].setVisible(true);  //cancel button
			
			Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[approvers]').setVisible(true);
			
			if( _IS_ADMIN == false ){
				Ext.getCmp('ol_loan_detail').buttons[0].setVisible(false);  //approve button
				Ext.getCmp('ol_loan_detail').buttons[1].setVisible(false);  //disapprove button
				//Ext.getCmp('ol_loanDetail_').buttons[2].setVisible(true);  //delete button
				//Ext.getCmp('ol_loanDetail_').buttons[3].setVisible(true);  //save button
				
				//if rejected show peca remarks
				if(Ext.getCmp('ol_loanDetail').getForm().findField('status').getValue()==10){
					Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[peca_remarks]').setVisible(true);
					Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[peca_remarks]').setReadOnly(true);
					Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[peca_remarks]').addClass('x-item-disabled');
				}
				else{
					Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[peca_remarks]').setVisible(false);
				}
				
				if(Ext.getCmp('ol_loanDetail').getForm().findField('status').getValue()==2){
					ol_clearRestrictions();
					Ext.getCmp('ol_loanDetail_').buttons[3].setVisible(true);  //send button	
					Ext.getCmp('ol_loanDetail_').buttons[1].setVisible(true);  //delete button
					Ext.getCmp('ol_loanDetail_').buttons[2].setVisible(true);  //save button	
					Ext.getCmp('ol_fileList').tbar.setVisible(true);
				}
				else{
					ol_setRestrictions();
					Ext.getCmp('ol_loanDetail_').buttons[3].setVisible(false);  //send button	
					Ext.getCmp('ol_loanDetail_').buttons[1].setVisible(false);  //delete button
					Ext.getCmp('ol_loanDetail_').buttons[2].setVisible(false);  //save button	
					Ext.getCmp('ol_fileList').tbar.setVisible(false);
				}
				
				Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[loan_date]').setReadOnly(true);
				Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[loan_date]').addClass('x-item-disabled');
			}
			else{
				Ext.getCmp('ol_loanDetail_').buttons[1].setVisible(false);  //delete button
				Ext.getCmp('ol_loanDetail_').buttons[2].setVisible(false);  //save button
				Ext.getCmp('ol_loanDetail_').buttons[3].setVisible(false);  //send button
				
				Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[peca_remarks]').setVisible(true);
				
				if(Ext.getCmp('ol_loanDetail').getForm().findField('status').getValue()==10 || Ext.getCmp('ol_loanDetail').getForm().findField('status').getValue()==9){
					Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[peca_remarks]').setReadOnly(true);
					Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[peca_remarks]').addClass('x-item-disabled');
				}
				else{
					Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[peca_remarks]').setReadOnly(false);
					Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[peca_remarks]').removeClass('x-item-disabled');
				}
				Ext.getCmp('ol_fileList').tbar.setVisible(false);
				ol_setRestrictions();
			}
			
	    }
		,onSave: function(frm,value){
			var rowCount = pecaDataStores.ol_fileStore.getCount();
	    	var jsonData = "[";
	    	if(rowCount > 0){
	    		for(var i = 0; i < rowCount; i++){
	    			var rec = pecaDataStores.ol_fileStore.getAt(i);
	    			jsonData += Ext.encode(rec.data);
					if((i+1)<rowCount){
						jsonData += ",";
					}
	    		}
	    	}
	    	jsonData += "]";
			
			frm.submit({
				url: '/online_loan/add' 
				,method: 'POST'
				,params: {'saveOrSendFlag':value
						,auth:_AUTH_KEY
						,'employee_id' : _USER_ID
						,'files': jsonData
						, 'online_loan[created_by]': _USER_ID}
				,waitMsg: 'Creating Request...'
				,success: function(form, action) {
					showExtInfoMsg(action.result.msg);					
					frm.findField('status').setValue(value);
					frm.findField('request_no').setValue(action.result.request_no);
					frm.setModeUpdate();
					Ext.getCmp('ol_loanDetail').getForm().load({
				    	url: '/online_loan/show'
				    	,params: {'request_no':action.result.request_no
									,auth:_AUTH_KEY}
				    	,method: 'POST'
				    	,waitMsgTarget: true
						,success: function(form, action) {
						}
					});					
				}
				,failure: function(form, action) {
					if (action.result.error_code == 13 || action.result.error_code == 14 || action.result.error_code == 9 || action.result.error_code == 16 || action.result.error_code == 23|| action.result.error_code == 24|| action.result.error_code == 37 || action.result.error_code == 8){
					
					//20111118 commented by ASI466 because this(prompt) is misleading to the user 
					//showExtErrorMsg(action.result.msg);
					showExtInfoMsg( action.result.msg);
					}
				else{
						Ext.Msg.confirm('Confirm Action',action.result.msg,function(btn) {
							if(btn=='yes'){
								form.onUpdate(form);
							}
						});
					}
				}	
			});
		}
		,onUpdate: function(frm,value){
			//if(Ext.getCmp('ol_loanDetail').getForm().findField('status').getValue()==2){
				frm.submit({
					url: '/online_loan/update' 
					,method: 'POST'
					,waitMsg: 'Updating Request...'
					,params: {
						'saveOrSendFlag':value
						,auth:_AUTH_KEY
						,'employee_id' : _USER_ID
						, 'online_loan[modified_by]': _USER_ID	
					}
					,success: function(form, action) {
						showExtInfoMsg(action.result.msg);
						frm.findField('status').setValue(value);
						frm.setModeUpdate();
					}
					,failure: function(form, action) {
					//20111118 commented by ASI466 because this(prompt) is misleading to the user 
					//showExtErrorMsg(action.result.msg);					
					showExtInfoMsg( action.result.msg);
					}	
				});
			//}
			//else{
				//showExtInfoMsg("Data was already saved.");
			//}
		}
		,onDelete: function(){
			if(Ext.getCmp('ol_loanDetail').getForm().findField('status').getValue()==2){
				Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
					if(btn=='yes') {
						Ext.getCmp('ol_loanDetail').getForm().submit({
							url: '/online_loan/delete' 
							,method: 'POST'
							,params: {auth:_AUTH_KEY, 'online_loan[modified_by]': _USER_ID}
							,waitMsg: 'Deleting Request...'
							,clientValidation: false
							,success: function(form, action) {
								showExtInfoMsg(action.result.msg);
								Ext.getCmp('ol_loanDetail').setModeNew();
								Ext.getCmp('ol_loanDetail').getForm().reset();
								Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOLLoan');
								//pecaDataStores.ol_loanStore.reload();
								if (pecaDataStores.ol_loanStore.getCount() % MAX_PAGE_SIZE == 1){
									var page = pecaDataStores.ol_loanStore.getTotalCount() - MAX_PAGE_SIZE - 1;
									pecaDataStores.ol_loanStore.load({params: {start:page<0?0:page, limit:MAX_PAGE_SIZE}});
								} else{
									pecaDataStores.ol_loanStore.reload();
								}
							}
							,failure: function(form, action) {
								showExtErrorMsg(action.result.msg);
							}	
						});
					}
				});
			}
			else{
				showExtInfoMsg("Only saved records can be deleted.");
			}
		}
		,onApprove: function(frm){
			frm.submit({
				url: '/online_loan/approveOnlineLoan' 
				,method: 'POST'
				,params: {
						//'status': Ext.getCmp('ol_loanDetail').getForm().findField('status').getValue()
						'online_loan[request_no]': Ext.getCmp('ol_loanDetail').getForm().findField('request_no').getValue()
						,auth:_AUTH_KEY
						, 'online_loan[created_by]': _USER_ID}
				,waitMsg: 'Approving Request...'
				,success: function(form, action) {
					showExtInfoMsg(action.result.msg);
					frm.setModeUpdate();
					Ext.getCmp('ol_loanDetail').getForm().reset();
					Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOLLoan');
					pecaDataStores.ol_loanStore.reload();
				}
				,failure: function(form, action) {
					showExtErrorMsg(action.result.msg);
				}	
			});
		}
		,onDisapprove: function(frm){
			frm.submit({
				url: '/online_loan/disapproveOnlineLoan' 
				,method: 'POST'
				,params: {
						//'status': Ext.getCmp('ol_loanDetail').getForm().findField('status').getValue()
						'online_loan[request_no]': Ext.getCmp('ol_loanDetail').getForm().findField('request_no').getValue()
						,auth:_AUTH_KEY
						, 'online_loan[created_by]': _USER_ID}
				,waitMsg: 'Disapproving Request...'
				,success: function(form, action) {
					showExtInfoMsg(action.result.msg);
					frm.setModeUpdate();
					Ext.getCmp('ol_loanDetail').getForm().reset();
					Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOLLoan');
					pecaDataStores.ol_loanStore.reload();
				}
				,failure: function(form, action) {
					showExtErrorMsg(action.result.msg);
				}	
			});
		}
		,onPreview: function(frm_){
			if(frm_.isValid()){
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
				var _url='';
				switch (frm_.findField('online_loan[loan_code]').getValue()){
					case 'CONL': _url='/printable_consumptionloan'; break;
					case 'MNIL': _url='/printable_miniloan'; break;
					case 'SPCL': _url='/printable_spotcashloan'; break;
					case 'SPEI': _url='/printable_specialloan'; break;
					case 'HSPL': _url='/printable_hsploan'; break;
					case 'LOYA': _url='/printable_loyaltyplusloan'; break;
				}
				
				if(_url!=''){	
					Ext.Ajax.request({
						url: _url
						,method: 'POST'
						,form: Ext.get('frmDownload')
						,params: {'employee_id': frm_.findField('online_loan[employee_id]').getValue()
								,'principal_amount': frm_.findField('online_loan[principal]').getValue()
								,'term': frm_.findField('online_loan[term]').getValue()
								,'loan_date': frm_.findField('online_loan[loan_date]').getValue()
								,'is_admin': _IS_ADMIN
								,auth:_AUTH_KEY
								, 'online_loan[modified_by]': _USER_ID}
						,isUpload: true
						,success: function(response, opts) {
							var obj = Ext.decode(response.responseText);
							if(obj.success){
								showExtInfoMsg(obj.msg);
								
							}else{
								showExtErrorMsg(obj.msg);
							}
						}
						,failure: function(response, opts) {
							if (opts.result.error_code == 19){
								showExtErrorMsg(opts.result.msg);
							}
							var obj = Ext.decode(response.responseText);
							showExtErrorMsg(obj.msg);
						}
					});
				}
				else{
					showExtInfoMsg("No preview available for "+frm_.findField('online_loan[loan_code]').getValue());	
				}
			}
		}
	};
};

var ol_loanFilter = function(){
	return {
		xtype:'form'
		,id:'ol_loanFilter'
		,region:'center'
		,layout: 'border'
		,autoscroll: true
		,items: [{			
			layout: 'form'
			,frame: true
			,region: 'north'
			,autoHeight:true
			,buttons:[
				{
					text: 'Search'
					,iconCls: 'icon_ext_search'
					,handler: function(){
						if(Ext.getCmp('ol_loanFilter').findById('ol_from').getValue()>Ext.getCmp('ol_loanFilter').findById('ol_to').getValue() && Ext.getCmp('ol_loanFilter').findById('ol_from').getValue()!="" && Ext.getCmp('ol_loanFilter').findById('ol_to').getValue()!="")
							showExtErrorMsg("Submission Date From cannot be greater than Submission Date To.");
						else
							pecaDataStores.ol_loanStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
					}
				}
			]
			,items: [{
					layout: 'column'
					,border: false
					,items: [				
						{
							layout: 'form'
							,labelAlign: 'left'
							,border: false
							,hideBorders: false
							,labelWidth: 130
							,width: 330
							,items: [
								{
									xtype: 'datefield'
									,fieldLabel: 'Submission Date'
									,anchor: '95%'
									,id: 'ol_from'
									,maxLength: 10
									,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
									,style: 'text-align: right'
									,validationEvent: 'change'
									,emptyText: 'From'
									,listeners: {
										specialkey: function(frm,evt){
											if (evt.getKey() == evt.ENTER) {
												pecaDataStores.ol_loanStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});					
											}
										}
										,'invalid':{
											scope:this
											,fn:function(field,msg){
												Ext.getCmp('ol_loanFilter').doLayout();
											}
										}
										,'valid':{
											scope:this
											,fn:function(field){
												Ext.getCmp('ol_loanFilter').doLayout();
											}
										}
									}
								}
							]
						}
						,{
							layout: 'form'
							,labelAlign: 'left'
							,border: false
							,hideBorders: false
							,labelWidth: 1
							,width: 201
							,items: [
								{
									xtype: 'datefield'
									,anchor: '95%'
									,id: 'ol_to'
									,maxLength: 10
									,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
									,style: 'text-align: right'
									,validationEvent: 'change'
									,emptyText: 'To'
									,listeners: {
										specialkey: function(frm,evt){
											if (evt.getKey() == evt.ENTER) {
												pecaDataStores.ol_loanStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});					
											}
										}
										,'invalid':{
											scope:this
											,fn:function(field,msg){
												Ext.getCmp('ol_loanFilter').doLayout();
											}
										}
										,'valid':{
											scope:this
											,fn:function(field){
												Ext.getCmp('ol_loanFilter').doLayout();
											}
										}
									}
								}
							]
						}
					]
				}			
				,{
					layout: 'form'
					,labelWidth: 130
					,labelAlign: 'left'
					,width: 330
					,border: false
					,items: [
						{
							xtype: 'combo'
							,fieldLabel: 'Transaction Type'
							,anchor: '95%'
							,id: 'ol_transactionType'
							,hiddenName: 'loan_code'
							,typeAhead: true
							,triggerAction: 'all'
							,lazyRender:true
							,store: pecaDataStores.ol_loantypeStore
							,mode: 'local'
							,valueField: 'loan_code'
							,displayField: 'loan_description'									
							,forceSelection: true
							,submitValue: false
							,emptyText: 'Please Select'	
							,maxLength: 30
							,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}	
							,listeners: {
								specialkey: function(frm,evt){
									if (evt.getKey() == evt.ENTER) {
										pecaDataStores.ol_loanStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});					
									}
								}
							}
							//,value: 'CONL'
						}
						,{
							xtype: 'combo'
							,fieldLabel: 'Status'
							,anchor: '95%'
							,id: 'ol_stat'
							,hiddenName: 'online_loan[status]'
							,typeAhead: true
							,triggerAction: 'all'
							,lazyRender:true
							,mode: 'local'
							,valueField: 'status'
							,displayField: 'displayText'									
							,forceSelection: true
							,submitValue: false
							,emptyText: 'Please Select'
							,store: new Ext.data.ArrayStore({
								id: 0
								,fields: [
									'status'
									,'displayText'
								]
								,data: [
									['1', 'New']
									,['2', 'Saved']
									//,['9', 'Approved']
									,['10', 'Rejected']
									,['3', 'For Approval']
								]
							})	
							,maxLength: 30
							,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
							,listeners: {
								specialkey: function(frm,evt){
									if (evt.getKey() == evt.ENTER) {
										pecaDataStores.ol_loanStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});				
									}
								}
							}
							,value: '3'
						}					
					]
					
				}
				,{
					layout: 'column'
					,border: false
					,items: [				
						{
							layout: 'form'
							,labelAlign: 'left'
							,labelWidth: 130
							,border: false
							,width: 330
							,items: [
								{
									xtype: 'textfield'
									,fieldLabel: 'Employee'	
									,emptyText: 'ID'	
									,anchor: '95%'
									,name: 'ol_employee_id'
									,id: 'ol_employee_id'
									,maxLength: 8
									,autoCreate: {tag: 'input', type: 'text', maxlength: '8'}
									,style: 'text-align: right'
									,enableKeyEvents: true
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
										,specialkey: function(frm,evt){
											if (evt.getKey() == evt.ENTER) {
												pecaDataStores.ol_loanStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});				
											}
										}
									}
								}
							]
						}
						,{
							layout: 'form'
							,labelWidth: 1
							,labelAlign: 'left'
							,border: false
							,width: 201
							,items: [
								{
									xtype: 'textfield'							
									,anchor: '95%'		
									,hideLabel: true
									,emptyText: 'Last Name'
									,id: 'ol_last'
									,maxLength: 30
									,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
									,listeners: {
										specialkey: function(frm,evt){
											if (evt.getKey() == evt.ENTER) {
												pecaDataStores.ol_loanStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});					
											}
										}
									}
								}
							]
						}
						,{
							layout: 'form'
							,labelWidth: 1
							,labelAlign: 'left'
							,border: false
							,width: 201
							,items: [
								{
									xtype: 'textfield'							
									,anchor: '95%'								
									,hideLabel: true
									,emptyText: 'First Name'
									,id: 'ol_first'
									,maxLength: 30
									,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
									,listeners: {
										specialkey: function(frm,evt){
											if (evt.getKey() == evt.ENTER) {
												pecaDataStores.ol_loanStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});				
											}
										}
									}
								}
							]
						}						
					]
				}
				
			]
		},ol_loanList()]
		,listeners:{
			'render':{
				scope:this
				,fn:function(grid){
					if(_IS_ADMIN==false){
						Ext.getCmp('ol_loanFilter').findById('ol_employee_id').setValue(_EMP_ID);
						Ext.getCmp('ol_loanFilter').findById('ol_employee_id').setVisible(false);
						Ext.getCmp('ol_loanFilter').findById('ol_last').setVisible(false);
						Ext.getCmp('ol_loanFilter').findById('ol_first').setVisible(false);
					}
				}
			}
		}
		,onPreview: function(frm_){
			if(frm_.isValid()){
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
					url: '/report_onlineloanlist'
					,method: 'POST'
					,form: Ext.get('frmDownload')
					,params: {
							'loan_date_from': frm_.findField('ol_from').getValue()
							,'loan_date_to': frm_.findField('ol_to').getValue()
							,'loan_code': frm_.findField('ol_transactionType').getValue()
							,'status': frm_.findField('ol_stat').getValue()
							,'employee_id': frm_.findField('ol_employee_id').getValue()
							,'first_name': frm_.findField('ol_first').getValue()
							,'last_name': frm_.findField('ol_last').getValue()
							,auth:_AUTH_KEY}
					,isUpload: true
					,success: function(response, opts) {
						var obj = Ext.decode(response.responseText);
						if(obj.success){
							showExtInfoMsg(obj.msg);
							
						}else{
							showExtInfoMsg(obj.msg);
						}
					}
				});
			}
		}
	    
	};
};

var ol_loanList = function(){
	return {
		xtype: 'grid'
		,id: 'ol_loanList'
		,titlebar: false
		,store: pecaDataStores.ol_loanStore
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		//,height: 425
		,region: 'center'
		//,anchor: '100%'
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit: true
			,scrollOffset: 13
		}		
		,cm: ol_loanColumns
		
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
					Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOLLoanDetail');
					Ext.getCmp('ol_loanDetail').getForm().load({
				    	url: '/online_loan/show'
				    	,params: {'request_no':(rec.get('online_loan[request_no]'))
									,auth:_AUTH_KEY}
				    	,method: 'POST'
				    	,waitMsgTarget: true
						,success: function(form, action) {
							var term = Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[term]').getValue();
							if(term <12){
								Ext.getCmp('ol_loanDetail').findById('interest_rate_amount').setValue((((Ext.getCmp('ol_loanDetail').findById('principal').getValue()) * ((Ext.getCmp('ol_loanDetail').findById('interest_rate').getValue())/100))/12)*term);
							}
							else{
								Ext.getCmp('ol_loanDetail').findById('interest_rate_amount').setValue((Ext.getCmp('ol_loanDetail').findById('principal').getValue()) * ((Ext.getCmp('ol_loanDetail').findById('interest_rate').getValue())/100));
							}
							Ext.getCmp('ol_loanDetail').getForm().setModeUpdate();
							if(rec.get('online_loan[approver_name]')==_USER_ID && rec.get('status') >= '3' && rec.get('status') <= '7'){
								Ext.getCmp('ol_loan_detail').buttons[0].setVisible(true);  //approve button
								Ext.getCmp('ol_loan_detail').buttons[1].setVisible(true);  //disapprove button
							}
							else{
								Ext.getCmp('ol_loan_detail').buttons[0].setVisible(false);  //approve button
								Ext.getCmp('ol_loan_detail').buttons[1].setVisible(false);  //disapprove button
							}
							pecaDataStores.ol_fileStore.load({params: {'topic_id':"L" + rec.get('request_no')}});
						}
					});
					
				}
			}
			,'render':{
				scope:this
				,fn:function(grid){
					Ext.getCmp('ol_to').setValue(_TODAY);
					Ext.getCmp('ol_from').setValue(new Date(new Date(Ext.getCmp('ol_to').getValue())-1));
					Ext.getCmp('ol_transactionType').setValue('CONL');
					pecaDataStores.ol_loanStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
					pecaDataStores.ol_loantypeStore.load({callback: function(r, options, success){
					Ext.getCmp('ol_loanFilter').findById('ol_transactionType').setValue('CONL');}});
					if(_IS_ADMIN==true){
						Ext.getCmp('ol_add_btn').setVisible(false);
						Ext.getCmp('ol_btn_separator1').setVisible(false);
						Ext.getCmp('ol_del_btn').setVisible(false);
						Ext.getCmp('ol_btn_separator2').setVisible(false);
					}
					pecaDataStores.ol_fileStore.removeAll();
				}
			}
		}
		
		,tbar:[
		{
			text:'New'
			,id: 'ol_add_btn'
			,tooltip:'Add a Loan'
			,iconCls: 'icon_ext_add'
			,scope:this
			,handler:function(btn) {
				if(_IS_ADMIN==false){
					Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOLLoanDetail');
					ol_loanDetail().setModeNew();
					Ext.Ajax.request({
						url: '/membership/read'
						,params: {'employee_id':_EMP_ID
									,auth:_AUTH_KEY}
						,success: function(response, opts) {
							var ret = Ext.decode(response.responseText);
							Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[employee_id]').setValue(ret.data[0].employee_id);
							Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[last_name]').setValue(ret.data[0].last_name);
							Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[first_name]').setValue(ret.data[0].first_name);
						}
						,failure: function(response, opts) {}
					});
				}
				pecaDataStores.ol_fileStore.removeAll();
			}
		},{
		text: '|'
		,id: 'ol_btn_separator1'
		}
		,{
			text:'Delete'
			,id: 'ol_del_btn'
			,tooltip:'Delete a Loan'
			,iconCls: 'icon_ext_del'
			,scope:this
			,handler:function(btn) {
				if(_IS_ADMIN==false){
					var index = Ext.getCmp('ol_loanList').getSelectionModel().getSelected();
					if (!index) {
						showExtInfoMsg("Please select a Loan to delete.");
						return false;
					}
					if(index.data.status==2){
						Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
							if(btn=='yes') {
								Ext.Ajax.request({
									url: '/online_loan/delete' 
									,method: 'POST'
									,params: {'request_no': index.data.request_no
											,auth:_AUTH_KEY
											, 'online_loan[modified_by]': _USER_ID}
									,waitMsg: 'Deleting Data...'
									,success: function(response, opts) {
										var obj = Ext.decode(response.responseText);
										if(obj.success){
											showExtInfoMsg(obj.msg);
											if (pecaDataStores.ol_loanStore.getCount() % MAX_PAGE_SIZE == 1){
												var page = pecaDataStores.ol_loanStore.getTotalCount() - MAX_PAGE_SIZE - 1;
												pecaDataStores.ol_loanStore.load({params: {start:page<0?0:page, limit:MAX_PAGE_SIZE}});
											} else{
												pecaDataStores.ol_loanStore.reload();
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
					else{
						showExtInfoMsg("Only saved records can be deleted.");
					}
				}
			}
		},{
		text: '|'
		,id: 'ol_btn_separator2'
		}
		,{
			text:'Print'
			,id: 'ol_print_btn'
			,tooltip:'Print'
			,iconCls: 'icon_ext_print'
			,scope:this
			,handler:function(btn) {
				var frm = Ext.getCmp('ol_loanFilter').getForm();
				frm.onPreview(frm);
			}
		}]
		
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.ol_loanStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};
var ol_setRestrictions=function(){
	Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[loan_code]').setReadOnly(true);
	Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[loan_code]').addClass('x-item-disabled');
	Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[loan_date]').setReadOnly(true);
	Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[loan_date]').addClass('x-item-disabled');
	Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[principal]').setReadOnly(true);
	Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[principal]').addClass('x-item-disabled');
	Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[term]').setReadOnly(true);
	Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[term]').addClass('x-item-disabled');
	Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[member_remarks]').setReadOnly(true);
	Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[member_remarks]').addClass('x-item-disabled');
	Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[approvers]').addClass('x-item-disabled');	

}
var ol_clearRestrictions=function(){
	Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[loan_code]').setReadOnly(false);
	Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[loan_code]').removeClass('x-item-disabled');
	Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[loan_date]').setReadOnly(false);
	Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[loan_date]').removeClass('x-item-disabled');
	Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[principal]').setReadOnly(false);
	Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[principal]').removeClass('x-item-disabled');
	Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[term]').setReadOnly(false);
	Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[term]').removeClass('x-item-disabled');
	Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[member_remarks]').setReadOnly(false);
	Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[member_remarks]').removeClass('x-item-disabled');
	Ext.getCmp('ol_loanDetail').getForm().findField('online_loan[approvers]').removeClass('x-item-disabled');
}
var ol_fileList = function(){
	return {
		xtype: 'grid'
		,id: 'ol_fileList'
		,titlebar: true
		,store: pecaDataStores.ol_fileStore
		,region: 'center'
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 150
		,width: 500
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
			,scrollOffset:0
		}
		,cm: fileColumns
		,tbar:[{
			text: 'Add'
			,tooltip:'Add Attachements'
			,iconCls: 'icon_ext_add'
			,scope:this
			,handler: function(){
				ol_uploadFormWin().show();
			}
		}
		,{
			text: 'Remove'
			,tooltip:'Remove Selected Uploaded File'
			,iconCls: 'icon_ext_del'
			,scope:this
			,handler: function(){
				var rowsSelected = Ext.getCmp('ol_fileList').getSelectionModel().getSelections();
				var rowsCount = rowsSelected.length;
				var aRecord;
				var jsonData = "";
				
				if(rowsCount > 0){
					jsonData="[";
					
					for(var i = 0; i < rowsCount; i++){
						aRecord = rowsSelected[i];
						path = aRecord.get('path');
						 
						jsonData += Ext.util.JSON.encode(path);
						jsonData += ",";
						   
						pecaDataStores.ol_fileStore.remove(aRecord);
					}
					jsonData = jsonData.substring(0,jsonData.length-1) + "]";
					
					Ext.Ajax.request({
			            url: '/upload/delete_files' 
			    		,method: 'POST'
			    		,params: {auth:_AUTH_KEY, 'filepath' : jsonData}
			            ,waitMsg: 'Deleting files...'
			            ,success: function(response, opts){//form, action
			            	var obj = Ext.decode(response.responseText);
			            	showExtInfoMsg(obj.msg);
			            }
			            ,failure: function(response, opts) {
			            	var obj = Ext.decode(response.responseText);
							showExtInfoMsg(obj.msg);
						}	
			        });
					
				} else {
					showExtInfoMsg("Please select a File to delete.");
					return false;
				}
			}
		}]
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
		            var win = new Ext.Window({
						id: 'file_loan_image'
						,title: 'Image Viewer'
						,frame: true
						,layout: 'form'
						,width: 800
						,height: 600
						,plain: true
						,modal: true
						,resizable: false
						,closable: true
						,constrainHeader: true
						,bodyStyle:{"padding":"5px"}
						,autoScroll: true
						,loadMask: true	
						,html       : "<img src = '" + rec.get('path') + "' />"
				        ,buttons:[{
				 			text: 'Cancel'
				 			,iconCls: 'icon_ext_cancel'
				 		    ,handler : function(btn){
				 				Ext.getCmp('file_loan_image').close();				
				 		    }
				 		}]
					});
		            win.show();
				}
			}
		}
	};
};

var ol_fp =  function(){
	return {
		xtype:'form'
	    ,fileUpload: true
	    ,width: 500
	    ,id:'ol_fp'
	    ,frame: true
	    ,enctype : 'multipart/form-data'
	    ,autoHeight: true
	    ,bodyStyle: 'padding: 10px 10px 0 10px;'
	    ,labelWidth: 50
	    ,defaults: {
	        anchor: '95%',
	        allowBlank: false,
	        msgTarget: 'side'
	    },
	    items: [{
	        xtype: 'fileuploadfield',
	        id: 'file',
	        emptyText: 'Select an image',
	        fieldLabel: 'Photo',
	        name: 'file',
	        buttonText: '',
	        buttonCfg: {
	            iconCls: 'upload-icon'
	        }
	    }],
	    buttons: [{
	        text: 'Upload'
	        ,name:'Upload'
	        ,handler: function(){
	    	    var frmUp = Ext.getCmp('ol_fp').getForm();
	            if(frmUp.isValid()){
					var topic_id;
					var request_no = Ext.getCmp('ol_loanDetail').getForm().findField('request_no').getValue();
					if(request_no=="" || request_no==null){
						topic_id = "";
					}
					else{
						topic_id = "L" + request_no;
					}
	            	frmUp.submit({
	                    url: '/upload/do_upload' 
	            		,method: 'POST'
	            		,params: {auth:_AUTH_KEY
	            				,'created_by': _USER_ID
	            				,'topic_id': topic_id
	            				}
	                    ,waitMsg: 'Uploading your files...'
	                    ,success: function(form, action){//form, action
	                    	
	                		var Function = Ext.data.Record.create([{
	    	    	    	    name: 'attachment_id'
	    	    	    	}, {
	    	    	    	    name: 'path'
	    	    	    	}, {
	    	    	    	    name: 'type'
	    	    	    	}, {
	    	    	    	    name: 'size'
	    	    	    	}]);
	    						pecaDataStores.ol_fileStore.add(new Function({
	    		    	    	     path:action.result.path
	    		    	    	    ,type:action.result.type
	    		    	    	    ,size:action.result.size
	    		    	    	    ,attachment_id:action.result.attachment_id
	    		    	    	  
	    		    	    	}));
	                    }
	                    ,failure: function(form, action) {
	        					showExtInfoMsg(action.result.msg);
	        			
	        			}	
	                });
	            }
	        }
	    }
	    ,{
	        text: 'Close',
	        handler: function(){
	    	Ext.getCmp('ol_uploadFormWin').close();		
	        }
	    }]
	};
};