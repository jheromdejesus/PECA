//Let's pretend we rendered our grid-columns with meta-data from our ORM framework.
var ol_withdrawalColumns =  new Ext.grid.ColumnModel( 
	[
		{header: 'Submission Date', width: 150, sortable: true, dataIndex: 'online_withdrawal[transaction_date]',align:'center'}
		,{header: 'Transaction Type', width: 150, sortable: true, dataIndex: 'online_withdrawal[transaction_description]'}
		,{header: 'Employee Name', width: 150, sortable: true, dataIndex: 'online_withdrawal[employee_name]'}
		,{header: 'Amount', width: 100, sortable: true, dataIndex: 'online_withdrawal[transaction_amount]',align:'right', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}}
		,{header: 'O.R. Number', width: 80, sortable: true, dataIndex: 'online_withdrawal[or_no]',align:'center'}
		,{header: 'Approver Name', width:150, sortable: true, dataIndex: 'online_withdrawal[approver_name]'}
		,{header: 'Status', width: 80, sortable: true, dataIndex: 'online_withdrawal[status]'}
		,{header: ' ', width: 10, sortable: true, dataIndex: 'online_withdrawal[request_no]', hidden:true}
		,{header: ' ', width: 10, sortable: true, dataIndex: 'approver1', hidden:true}
		,{header: ' ', width: 10, sortable: true, dataIndex: 'approver2', hidden:true}
		,{header: ' ', width: 10, sortable: true, dataIndex: 'approver3', hidden:true}
		,{header: ' ', width: 10, sortable: true, dataIndex: 'approver4', hidden:true}
		,{header: ' ', width: 10, sortable: true, dataIndex: 'approver5', hidden:true}
	]
);

var ow_uploadFormWin = function(){
	return new Ext.Window({
		id: 'ow_uploadFormWin'
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
		,items:[ ow_fp()]
	});
};

var ol_withdrawalDetail = function(){
	return {
		xtype:'form'
		,id:'ol_withdrawalDetail'
		,region:'center'
		,title: 'Details'
		,anchor: '100%'
		,frame: true
		,reader: pecaReaders.ol_withdrawalReader
		,items: [{			
			layout: 'form'
			,id:'ol_withdrawalDetail_'
			,region:'center'
			,style: 'padding-left:10px;padding-top:10px;'
			,buttons:[
			{
				text: 'Preview'
				,iconCls: 'icon_ext_preview'
				,handler: function(){
					var frm = Ext.getCmp('ol_withdrawalDetail').getForm();
					frm.onPreview(frm);
				}
			},{
				text: 'Delete'
				,iconCls: 'icon_ext_del'
				,handler: function(){
					var frm = Ext.getCmp('ol_withdrawalDetail').getForm();
					frm.onDelete();
				}
			},{
				text:'Save'
				,iconCls: 'icon_ext_save'
				,handler: function(){
					var frm = Ext.getCmp('ol_withdrawalDetail').getForm();
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
					var frm = Ext.getCmp('ol_withdrawalDetail').getForm();
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
					//Ext.getCmp('ol_withdrawalDetail').setModeNew();
					Ext.getCmp('ol_withdrawalDetail').getForm().reset();
					Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOLWithdrawal');
					pecaDataStores.ol_withdrawalStore.reload();
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
									,name: 'online_withdrawal[employee_id]'
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
									,name: 'online_withdrawal[last_name]'
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
									,name: 'online_withdrawal[first_name]'
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
					,labelWidth: 150
					,labelAlign: 'left'
					,border: false
					,hideBorders: false
					,width: 350
					,items: [
						{
							xtype: 'combo'
							,fieldLabel: 'Transaction Type'
							,anchor: '95%'
							,hiddenName: 'online_withdrawal[transaction_code]'
							,editable: false
							,typeAhead: true
							,triggerAction: 'all'
							,lazyRender:true
							,store: pecaDataStores.ow_transactiontypeStore
							,mode: 'local'
							,valueField: 'transaction_code'
							,displayField: 'transaction_description'									
							,forceSelection: true
							,submitValue: false
							,emptyText: 'Please Select'
							,allowBlank: false
							,required: true
							,listeners: {
								select: {
									scope: this
									, fn: function(combo, record, index){
										if ((combo.getValue() == 'WDWL') && Ext.getCmp('ol_withdrawalDetail').getForm().findField('frm_mode').getValue() == FORM_MODE_UPDATE){
											Ext.getCmp('ol_withdrawalDetail_').buttons[0].setVisible(true);  //preview button
										}
										else{
											Ext.getCmp('ol_withdrawalDetail_').buttons[0].setVisible(false);  //preview button
										}
									}
								}
							}
						}
						,{
							xtype: 'datefield'
							,fieldLabel: 'Submission Date'
							,anchor: '95%'
							,id: 'subD'
							,name: 'online_withdrawal[transaction_date]'						
							,allowBlank: false
							,required: true
							,maxLength: 10
							,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
							,style: 'text-align: right'
							,validationEvent: 'change'
						}
						,{
							xtype: 'moneyfield'
							,fieldLabel: 'Amount'
							,anchor: '95%'
							,name: 'online_withdrawal[transaction_amount]'
							,id: 'ow_amount'
							,allowBlank: false
							,required: true
							,maxLength: 16
							,autoCreate: {tag: 'input', type: 'text', maxlength: '16'}
							
							
						}
						,{
							xtype: 'moneyfield'
							,fieldLabel: 'Withdrawable Amount'
							,anchor: '95%'
							,name: 'online_withdrawal[withdrawable_amount]'
						//	,id: 'ow_wa'
						//	,maxLength: 16
						//	,autoCreate: {tag: 'input', type: 'text', maxlength: '16'}
							,submitValue:false
							,readOnly:true
							,cls:'x-item-disabled'
							,maxValue: 9999999999999.99
						}
						,{//hidden field
							xtype: 'numberfield'
							,anchor: '95%'
							,name: 'ow_request_no'
							,hidden:true
							,submitValue:false
						}
						,{//hidden field
							xtype: 'numberfield'
							,anchor: '95%'
							,name: 'ow_status'
							,hidden:true
							,submitValue:false
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
							,name: 'online_withdrawal[member_remarks]'
							,maxLength: 50
							,autoScroll: true
							,anchor: '90%'
						}
						,{
							xtype: 'textarea'
							,fieldLabel: 'PECA Remarks'
							,height: 35
							,name: 'online_withdrawal[peca_remarks]'
							,maxLength: 50
							,autoScroll: true
							,anchor: '90%'
						}
					]
				},
				{
					xtype: 'fieldset'
					,title: 'Upload A File'
					,layout: 'fit'	    
					,width: 550
					,autoHeight: true
					,items: [{	
						layout: 'fit'
						,defaultType: 'grid'
						,height: 150
						,items: [ow_fileList()]
					}]
				},
				{
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
							,name: 'online_withdrawal[approvers]'
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
			,id: 'ol_withdrawal_detail'
			,buttons:[
			{text: 'Approve'
				,iconCls: 'icon_ext_approve'
				,hidden: true
				,handler: function(){
					var frm = Ext.getCmp('ol_withdrawalDetail').getForm();
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
								var frm = Ext.getCmp('ol_withdrawalDetail').getForm();
								frm.findField('online_withdrawal[peca_remarks]').setValue(text);
								frm.onDisapprove(frm);
						}
						else if(btn == 'ok' && text.length == 0){
							var element = Ext.getCmp('online_withdrawal_disapprove');
							element.handler.call(element.scope);
						}
					},this,50,Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[peca_remarks]').getValue());

				}
			}]
		},
		{
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
		}]
	    ,isModeNew: function() {
	    	return (Ext.getCmp('ol_withdrawalDetail').getForm().findField('frm_mode').getValue() == FORM_MODE_NEW);
	    }
	    ,setModeNew: function() {
	    	Ext.getCmp('ol_withdrawalDetail').getForm().findField('frm_mode').setValue(FORM_MODE_NEW);
	    	Ext.getCmp('ol_withdrawalDetail_').buttons[0].setVisible(false);  //preview button
	    	Ext.getCmp('ol_withdrawalDetail_').buttons[1].setVisible(false);  //delete button
	    	Ext.getCmp('ol_withdrawalDetail_').buttons[2].setVisible(true);  //save button
			Ext.getCmp('ol_withdrawalDetail_').buttons[3].setVisible(true);  //send button
	    	Ext.getCmp('ol_withdrawalDetail_').buttons[4].setVisible(true);  //cancel button
	    	Ext.getCmp('ol_withdrawal_detail').buttons[0].setVisible(false);  //approve button
			Ext.getCmp('ol_withdrawal_detail').buttons[1].setVisible(false);  //disapprove button
			Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[peca_remarks]').setVisible(false);
			Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[approvers]').setVisible(false);
			
			Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[transaction_amount]').setValue(0);
			//Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[withdrawable_amount]').setValue(0);
			Ext.getCmp('ol_withdrawalDetail').findById('subD').setValue(new Date().clearTime());
			//Ext.getCmp('ow_fileList').setDisabled(true);
			clearRestrictions();
			if( _IS_ADMIN == false ){
				Ext.getCmp('ow_fileList').tbar.setVisible(true);
			}
			else{
				Ext.getCmp('ow_fileList').tbar.setVisible(false);
			}
		}
		,setModeUpdate: function() {
			//Ext.getCmp('ow_fileList').setDisabled(false);
			Ext.getCmp('ol_withdrawalDetail').getForm().findField('frm_mode').setValue(FORM_MODE_UPDATE);
			if(Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[transaction_code]').getValue()=="WDWL"){
				Ext.getCmp('ol_withdrawalDetail_').buttons[0].setVisible(true);  //preview button
			}
			else{
				Ext.getCmp('ol_withdrawalDetail_').buttons[0].setVisible(false);  //preview button
			}	
	    	Ext.getCmp('ol_withdrawalDetail_').buttons[4].setVisible(true);  //cancel button
			
			Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[approvers]').setVisible(true);
			
			if( _IS_ADMIN == false ){
				Ext.getCmp('ol_withdrawal_detail').buttons[0].setVisible(false);  //approve button
				Ext.getCmp('ol_withdrawal_detail').buttons[1].setVisible(false);  //disapprove button
				//Ext.getCmp('ol_withdrawalDetail_').buttons[2].setVisible(true);  //delete button
				//Ext.getCmp('ol_withdrawalDetail_').buttons[3].setVisible(true);  //save button
				
				//if rejected show peca remarks
				if(Ext.getCmp('ol_withdrawalDetail').getForm().findField('ow_status').getValue()==10){
					Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[peca_remarks]').setVisible(true);
					Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[peca_remarks]').setReadOnly(true);
					Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[peca_remarks]').addClass('x-item-disabled');
				}
				else{
					Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[peca_remarks]').setVisible(false);
				}
				
				if(Ext.getCmp('ol_withdrawalDetail').getForm().findField('ow_status').getValue()==2){
					clearRestrictions();
					Ext.getCmp('ol_withdrawalDetail_').buttons[3].setVisible(true);  //send button	
					Ext.getCmp('ol_withdrawalDetail_').buttons[1].setVisible(true);  //delete button
					Ext.getCmp('ol_withdrawalDetail_').buttons[2].setVisible(true);  //save button
					Ext.getCmp('ow_fileList').tbar.setVisible(true);
				}
				else{
					setRestrictions();
					Ext.getCmp('ol_withdrawalDetail_').buttons[3].setVisible(false);  //send button	
					Ext.getCmp('ol_withdrawalDetail_').buttons[1].setVisible(false);  //delete button
					Ext.getCmp('ol_withdrawalDetail_').buttons[2].setVisible(false);  //save button					
					Ext.getCmp('ow_fileList').tbar.setVisible(false);
				}
			}
			else{
				Ext.getCmp('ol_withdrawalDetail_').buttons[1].setVisible(false);  //delete button
				Ext.getCmp('ol_withdrawalDetail_').buttons[2].setVisible(false);  //save button
				Ext.getCmp('ol_withdrawalDetail_').buttons[3].setVisible(false);  //send button
				
				Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[peca_remarks]').setVisible(true);
				
				if(Ext.getCmp('ol_withdrawalDetail').getForm().findField('ow_status').getValue()==10 || Ext.getCmp('ol_withdrawalDetail').getForm().findField('ow_status').getValue()==9){
					Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[peca_remarks]').setReadOnly(true);
					Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[peca_remarks]').addClass('x-item-disabled');
				}
				else{
					Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[peca_remarks]').setReadOnly(false);
					Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[peca_remarks]').removeClass('x-item-disabled');
				}
				Ext.getCmp('ow_fileList').tbar.setVisible(false);
				setRestrictions();
			}
			
	    }
		,onSave: function(frm,value){
			var rowCount = pecaDataStores.ow_fileStore.getCount();
	    	var jsonData = "[";
	    	if(rowCount > 0){
	    		for(var i = 0; i < rowCount; i++){
	    			var rec = pecaDataStores.ow_fileStore.getAt(i);
	    			jsonData += Ext.encode(rec.data);
					if((i+1)<rowCount){
						jsonData += ",";
					}
	    		}
	    	}
	    	jsonData += "]";
			
			frm.submit({
				url: '/online_withdrawal/addHeader' 
				,method: 'POST'
				,params: {'saveOrSendFlag':value
						,auth:_AUTH_KEY
						,'files': jsonData
						,'employee_id': _USER_ID
						, 'online_withdrawal[created_by]': _USER_ID}
				,waitMsg: 'Creating Request...'
				,success: function(form, action) {
					showExtInfoMsg(action.result.msg);
					frm.findField('ow_status').setValue(value);
					frm.findField('ow_request_no').setValue(action.result.request_no);
					frm.setModeUpdate();
					Ext.getCmp('ol_withdrawalDetail').getForm().load({
				    	url: '/online_withdrawal/showHeader'
				    	,params: {'request_no':(action.result.request_no)
									,auth:_AUTH_KEY}
				    	,method: 'POST'
				    	,waitMsgTarget: true
						,success: function(form, action) {
						}
					});
					
				}
				,failure: function(form, action) {
					if (action.result.error_code == 8 
						|| action.result.error_code == 9 
						|| action.result.error_code == 10 
						|| action.result.error_code == 11){
						showExtErrorMsg(action.result.msg);
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
			if(Ext.getCmp('ol_withdrawalDetail').getForm().findField('ow_status').getValue()==2){
				frm.submit({
					url: '/online_withdrawal/updateHeader' 
					,method: 'POST'
					,waitMsg: 'Updating Request...'
					,params: {
						'saveOrSendFlag':value
						,'status':Ext.getCmp('ol_withdrawalDetail').getForm().findField('ow_status').getValue()
						,'request_no':Ext.getCmp('ol_withdrawalDetail').getForm().findField('ow_request_no').getValue()
						,auth:_AUTH_KEY
						,'employee_id': _USER_ID
						, 'online_withdrawal[modified_by]': _USER_ID	
					}
					,success: function(form, action) {
						showExtInfoMsg(action.result.msg);
						frm.findField('ow_status').setValue(value);
						frm.setModeUpdate();
					}
					,failure: function(form, action) {
					showExtErrorMsg(action.result.msg);
					}	
				});
			}
			else{
				showExtInfoMsg("Data was already saved.");
			}
		}
		,onDelete: function(){
			if(Ext.getCmp('ol_withdrawalDetail').getForm().findField('ow_status').getValue()==2){
				Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
					if(btn=='yes') {
						Ext.getCmp('ol_withdrawalDetail').getForm().submit({
							url: '/online_withdrawal/deleteHeader' 
							,method: 'POST'
							,params: {'status': Ext.getCmp('ol_withdrawalDetail').getForm().findField('ow_status').getValue()
									,'request_no': Ext.getCmp('ol_withdrawalDetail').getForm().findField('ow_request_no').getValue()
									,auth:_AUTH_KEY
									,'online_withdrawal[modified_by]': _USER_ID}
							,waitMsg: 'Deleting Request...'
							,clientValidation: false
							,success: function(form, action) {
								showExtInfoMsg(action.result.msg);
								Ext.getCmp('ol_withdrawalDetail').setModeNew();
								Ext.getCmp('ol_withdrawalDetail').getForm().reset();
								Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOLWithdrawal');
								//pecaDataStores.ol_withdrawalStore.reload();
								if (pecaDataStores.ol_withdrawalStore.getCount() % MAX_PAGE_SIZE == 1){
									var page = pecaDataStores.ol_withdrawalStore.getTotalCount() - MAX_PAGE_SIZE - 1;
									pecaDataStores.ol_withdrawalStore.load({params: {start:page<0?0:page, limit:MAX_PAGE_SIZE}});
								} else{
									pecaDataStores.ol_withdrawalStore.reload();
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
				url: '/online_withdrawal/approve' 
				,method: 'POST'
				,params: {
						'online_withdrawal[status_flag]': Ext.getCmp('ol_withdrawalDetail').getForm().findField('ow_status').getValue()
						,'online_withdrawal[request_no]': Ext.getCmp('ol_withdrawalDetail').getForm().findField('ow_request_no').getValue()
						,'online_withdrawal[peca_remarks]': Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[peca_remarks]').getValue()
						,auth:_AUTH_KEY
						, 'online_withdrawal[created_by]': _USER_ID}
				,waitMsg: 'Approving Request...'
				,success: function(frm, action) {
					showExtInfoMsg(action.result.msg);
					frm.setModeUpdate();
					Ext.getCmp('ol_withdrawalDetail').getForm().reset();
					Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOLWithdrawal');
					pecaDataStores.ol_withdrawalStore.reload();
				}
				,failure: function(frm, action) {
					showExtErrorMsg(action.result.msg);
				}	
			});
		}
		,onDisapprove: function(frm){
			frm.submit({
				url: '/online_withdrawal/disapprove' 
				,method: 'POST'
				,params: {
						'online_withdrawal[status_flag]': Ext.getCmp('ol_withdrawalDetail').getForm().findField('ow_status').getValue()
						,'online_withdrawal[request_no]': Ext.getCmp('ol_withdrawalDetail').getForm().findField('ow_request_no').getValue()
						,'online_withdrawal[peca_remarks]': Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[peca_remarks]').getValue()
						,auth:_AUTH_KEY
						, 'online_withdrawal[created_by]': _USER_ID}
				,waitMsg: 'Disapproving Request...'
				,success: function(frm, action) {
					showExtInfoMsg(action.result.msg);
					frm.setModeUpdate();
					Ext.getCmp('ol_withdrawalDetail').getForm().reset();
					Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOLWithdrawal');
					pecaDataStores.ol_withdrawalStore.reload();
				}
				,failure: function(frm, action) {
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
				Ext.Ajax.request({
					url: '/printable_withdrawal'
					,method: 'POST'
					,form: Ext.get('frmDownload')
					,params: {'employee_id': frm_.findField('online_withdrawal[employee_id]').getValue()
							,'transaction_code': frm_.findField('online_withdrawal[transaction_code]').getValue()
							,'transaction_amount': frm_.findField('online_withdrawal[transaction_amount]').getValue()
							,'remarks': frm_.findField('online_withdrawal[member_remarks]').getValue()
							,'is_admin': _IS_ADMIN
							,auth:_AUTH_KEY
							, 'online_withdrawal[modified_by]': _USER_ID}
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
	};
};

var ol_withdrawalFilter = function(){
	return {
		xtype:'form'
		,id:'ol_withdrawalFilter'
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
						if(Ext.getCmp('ol_withdrawalFilter').findById('ow_from').getValue()>Ext.getCmp('ol_withdrawalFilter').findById('ow_to').getValue() && Ext.getCmp('ol_withdrawalFilter').findById('ow_from').getValue()!="" && Ext.getCmp('ol_withdrawalFilter').findById('ow_to').getValue()!="")
							showExtErrorMsg("Submission Date From cannot be greater than Submission Date To.");
						else
							pecaDataStores.ol_withdrawalStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
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
									,id: 'ow_from'
									,maxLength: 10
									,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
									,style: 'text-align: right'
									,validationEvent: 'change'
									,emptyText: 'From'
									,listeners: {
										specialkey: function(frm,evt){
											if (evt.getKey() == evt.ENTER) {
												pecaDataStores.ol_withdrawalStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});					
											}
										}
										,'invalid':{
											scope:this
											,fn:function(field,msg){
												Ext.getCmp('ol_withdrawalFilter').doLayout();
											}
										}
										,'valid':{
											scope:this
											,fn:function(field){
												Ext.getCmp('ol_withdrawalFilter').doLayout();
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
									,id: 'ow_to'
									,maxLength: 10
									,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
									,style: 'text-align: right'
									,validationEvent: 'change'
									,emptyText: 'To'
									,listeners: {
										specialkey: function(frm,evt){
											if (evt.getKey() == evt.ENTER) {
												pecaDataStores.ol_withdrawalStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});				
											}
										}
										,'invalid':{
											scope:this
											,fn:function(field,msg){
												Ext.getCmp('ol_withdrawalFilter').doLayout();
											}
										}
										,'valid':{
											scope:this
											,fn:function(field){
												Ext.getCmp('ol_withdrawalFilter').doLayout();
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
							,id: 'ow_transactionType'
							,hiddenName: 'transaction_code'
							,typeAhead: true
							,triggerAction: 'all'
							,lazyRender:true
							,store: pecaDataStores.ow_transactiontypeStore
							,mode: 'local'
							,valueField: 'transaction_code'
							,displayField: 'transaction_description'									
							,forceSelection: true
							,submitValue: false
							,emptyText: 'Please Select'	
							,listeners: {
								specialkey: function(frm,evt){
									if (evt.getKey() == evt.ENTER) {
										pecaDataStores.ol_withdrawalStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});			
									}
								}
							}
						}
						,{
							xtype: 'combo'
							,fieldLabel: 'Status'
							,anchor: '95%'
							,id: 'ow_stat'
							,hiddenName: 'online_withdrawal[status]'
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
							,listeners: {
								specialkey: function(frm,evt){
									if (evt.getKey() == evt.ENTER) {
										pecaDataStores.ol_withdrawalStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});		
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
									,name: 'ow_employee_id'
									,id: 'ow_employee_id'
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
												pecaDataStores.ol_withdrawalStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});					
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
									,id: 'ow_last'
									,maxLength: 30
									,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
									,listeners: {
										specialkey: function(frm,evt){
											if (evt.getKey() == evt.ENTER) {
												pecaDataStores.ol_withdrawalStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});			
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
									,id: 'ow_first'
									,maxLength: 30
									,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
									,listeners: {
										specialkey: function(frm,evt){
											if (evt.getKey() == evt.ENTER) {
												pecaDataStores.ol_withdrawalStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});					
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
					,border: false
					,width:330
					,items: [
						{
							xtype: 'numberfield'
							,fieldLabel: 'OR Number'
							,anchor: '95%'
							,id: 'ow_or_no'
							,maxLength: 10
							,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
							,style: 'text-align: right'
							,listeners: {
								specialkey: function(frm,evt){
									if (evt.getKey() == evt.ENTER) {
										pecaDataStores.ol_withdrawalStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});				
									}
								}
							}
						}
					]
				}
				
			]

		},ol_withdrawalList()]
		,listeners:{
			'render':{
				scope:this
				,fn:function(grid){
					if(_IS_ADMIN==false){
						Ext.getCmp('ol_withdrawalFilter').findById('ow_employee_id').setValue(_EMP_ID);
						Ext.getCmp('ol_withdrawalFilter').findById('ow_employee_id').setVisible(false);
						Ext.getCmp('ol_withdrawalFilter').findById('ow_last').setVisible(false);
						Ext.getCmp('ol_withdrawalFilter').findById('ow_first').setVisible(false);
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
					url: '/report_onlinewithdrawallist'
					,method: 'POST'
					,form: Ext.get('frmDownload')
					,params: {
							'submission_date_from': frm_.findField('ow_from').getValue()
							,'submission_date_to': frm_.findField('ow_to').getValue()
							,'transaction_code': frm_.findField('ow_transactionType').getValue()
							,'status': frm_.findField('ow_stat').getValue()
							,'employee_id': frm_.findField('ow_employee_id').getValue()
							,'first_name': frm_.findField('ow_first').getValue()
							,'last_name': frm_.findField('ow_last').getValue()
							,'or_no': frm_.findField('ow_or_no').getValue()
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


var ol_withdrawalList = function(){
	return {
		xtype: 'grid'
		,id: 'ol_withdrawalList'
		,titlebar: false
		,store: pecaDataStores.ol_withdrawalStore
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
		,cm: ol_withdrawalColumns
		
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
					Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOLWithdrawalDetail');
					Ext.getCmp('ol_withdrawalDetail').getForm().load({
				    	url: '/online_withdrawal/showHeader'
				    	,params: {'request_no':(rec.get('online_withdrawal[request_no]'))
									,auth:_AUTH_KEY}
				    	,method: 'POST'
				    	,waitMsgTarget: true
						,success: function(form, action) {
							var resp = Ext.decode(action.response.responseText).data[0];
							
							Ext.getCmp('ol_withdrawalDetail').getForm().setModeUpdate();
							if(rec.get('online_withdrawal[approver_name]')==_USER_ID && rec.get('status') >= '3' && rec.get('status') <= '7'){
								Ext.getCmp('ol_withdrawal_detail').buttons[0].setVisible(true);  //approve button
								Ext.getCmp('ol_withdrawal_detail').buttons[1].setVisible(true);  //disapprove button
							}
							else{
								Ext.getCmp('ol_withdrawal_detail').buttons[0].setVisible(false);  //approve button
								Ext.getCmp('ol_withdrawal_detail').buttons[1].setVisible(false);  //disapprove button
							}
							
							if(resp.transaction_code=='WDWL'){
								Ext.getCmp('ol_withdrawalDetail_').buttons[0].setVisible(true);  //preview button
							}
							else{
								Ext.getCmp('ol_withdrawalDetail_').buttons[0].setVisible(false);  //preview button
							}
							
							pecaDataStores.ow_fileStore.load({params: {'topic_id':"W" + rec.get('request_no')}});
						}
					});
					
					
				}
			}
			,'render':{
				scope:this
				,fn:function(grid){
					Ext.getCmp('ow_to').setValue(_TODAY);
					Ext.getCmp('ow_from').setValue(new Date(new Date(Ext.getCmp('ow_to').getValue())-1));
					pecaDataStores.ol_withdrawalStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
					pecaDataStores.ow_transactiontypeStore.load();
					if(_IS_ADMIN==true){
						Ext.getCmp('ow_add_btn').setVisible(false);
						Ext.getCmp('ow_btn_separator1').setVisible(false);
						Ext.getCmp('ow_del_btn').setVisible(false);
						Ext.getCmp('ow_btn_separator2').setVisible(false);
					}
					pecaDataStores.ow_fileStore.removeAll();
				}
			}
		}
		
		,tbar:[
		{
			text:'New'
			,id:'ow_add_btn'
			,tooltip:'Add a Capital Contribution'
			,iconCls: 'icon_ext_add'
			,scope:this
			,handler:function(btn) {
				if(_IS_ADMIN==false){
					Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOLWithdrawalDetail');
					ol_withdrawalDetail().setModeNew();
					Ext.Ajax.request({
						url: '/online_withdrawal/readWAmount'
						,params: {'employee_id':_EMP_ID
									,auth:_AUTH_KEY}
						,success: function(response, opts) {
							var ret = Ext.decode(response.responseText);
							Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[withdrawable_amount]').setValue(ret.data[0].maxWdwlAmount);
							Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[employee_id]').setValue(ret.data[0].employee_id);
							Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[last_name]').setValue(ret.data[0].last_name);
							Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[first_name]').setValue(ret.data[0].first_name);
						}
						,failure: function(response, opts) {}
					});
				}
				pecaDataStores.ow_fileStore.removeAll();
			}
		},{
		text: '|'
		,id: 'ow_btn_separator1'
		}
		,{
			text:'Delete'
			,id:'ow_del_btn'
			,tooltip:'Delete a Capital Contribution'
			,iconCls: 'icon_ext_del'
			,scope:this
			,handler:function(btn) {
				if(_IS_ADMIN==false){
					var index = Ext.getCmp('ol_withdrawalList').getSelectionModel().getSelected();
					if (!index) {
						showExtInfoMsg("Please select a Capital Contribution to delete.");
						return false;
					}
					if(index.data.status==2){
						Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
							if(btn=='yes') {
								Ext.Ajax.request({
									url: '/online_withdrawal/deleteHeader' 
									,method: 'POST'
									,params: {'request_no': index.data.request_no
											,auth:_AUTH_KEY
											, 'online_withdrawal[modified_by]': _USER_ID}
									,waitMsg: 'Deleting Data...'
									,success: function(response, opts) {
										var obj = Ext.decode(response.responseText);
										if(obj.success){
											showExtInfoMsg(obj.msg);
											//pecaDataStores.ol_withdrawalStore.reload();
											if (pecaDataStores.ol_withdrawalStore.getCount() % MAX_PAGE_SIZE == 1){
												var page = pecaDataStores.ol_withdrawalStore.getTotalCount() - MAX_PAGE_SIZE - 1;
												pecaDataStores.ol_withdrawalStore.load({params: {start:page<0?0:page, limit:MAX_PAGE_SIZE}});
											} else{
												pecaDataStores.ol_withdrawalStore.reload();
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
		}
		,{
		text: '|'
		,id: 'ow_btn_separator2'
		}
		,{
			text:'Print'
			,id:'ow_print_btn'
			,tooltip:'Print'
			,iconCls: 'icon_ext_print'
			,scope:this
			,handler:function(btn) {
				var frm = Ext.getCmp('ol_withdrawalFilter').getForm();
				frm.onPreview(frm);	
			}
		}
		]
		
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.ol_withdrawalStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};
var setRestrictions=function(){
	Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[transaction_code]').setReadOnly(true);
	Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[transaction_code]').addClass('x-item-disabled');
	Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[transaction_date]').setReadOnly(true);
	Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[transaction_date]').addClass('x-item-disabled');
	Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[transaction_amount]').setReadOnly(true);
	Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[transaction_amount]').addClass('x-item-disabled');
	Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[member_remarks]').setReadOnly(true);
	Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[member_remarks]').addClass('x-item-disabled');
	Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[approvers]').addClass('x-item-disabled');
}
var clearRestrictions=function(){
	Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[transaction_code]').setReadOnly(false);
	Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[transaction_code]').removeClass('x-item-disabled');
	Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[transaction_date]').setReadOnly(false);
	Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[transaction_date]').removeClass('x-item-disabled');
	Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[transaction_amount]').setReadOnly(false);
	Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[transaction_amount]').removeClass('x-item-disabled');
	Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[member_remarks]').setReadOnly(false);
	Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[member_remarks]').removeClass('x-item-disabled');
	Ext.getCmp('ol_withdrawalDetail').getForm().findField('online_withdrawal[approvers]').removeClass('x-item-disabled');
}

var ow_fileList = function(){
	return {
		xtype: 'grid'
		,id: 'ow_fileList'
		,titlebar: true
		,store: pecaDataStores.ow_fileStore
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
			,id: 'capcon_add_but'
			,scope:this
			,handler: function(){
				ow_uploadFormWin().show();
			}
		}
		,{
			text: 'Remove'
			,tooltip:'Remove Selected Uploaded File'
			,iconCls: 'icon_ext_del'
			,scope:this
			,handler: function(){
				var rowsSelected = Ext.getCmp('ow_fileList').getSelectionModel().getSelections();
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
						   
						pecaDataStores.ow_fileStore.remove(aRecord);
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
						id: 'file_capcon_image'
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
				 				Ext.getCmp('file_capcon_image').close();				
				 		    }
				 		}]
					});
		            win.show();
				}
			}
		}
	};
};

var ow_fp =  function(){
	return {
		xtype:'form'
	    ,fileUpload: true
	    ,width: 500
	    ,id:'ow_fp'
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
	    	    var frmUp = Ext.getCmp('ow_fp').getForm();
	            if(frmUp.isValid()){
					var topic_id;
					var request_no = Ext.getCmp('ol_withdrawalDetail').getForm().findField('ow_request_no').getValue();
					if(request_no=="" || request_no==null){
						topic_id = "";
					}
					else{
						topic_id = "W" + request_no;
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
	    						pecaDataStores.ow_fileStore.add(new Function({
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
	    	Ext.getCmp('ow_uploadFormWin').close();		
	        }
	    }]
	};
};