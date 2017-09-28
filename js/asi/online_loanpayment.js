//Let's pretend we rendered our grid-columns with meta-data from our ORM framework.
var ol_loanpaymentColumns =  new Ext.grid.ColumnModel( 
	[
		{header: 'Submission Date', width: 150, sortable: true, dataIndex: 'lp[transaction_period]',align:'center'}
		,{header: 'Employee Name', width: 200, sortable: true, dataIndex: 'lp[employee_name]'}
		,{header: 'Amount', width: 100, sortable: true, align: 'right', dataIndex: 'amount', renderer: function(value, rec){
		    	return Ext.util.Format.number(value,'0,000,000,000.00');}}
		,{header: 'Transaction Type', width: 200, sortable: true, dataIndex: 'lp[transaction_description]'}
		,{header: 'O.R. Number', width: 100, sortable: true, align: 'center', dataIndex: 'lp[or_number]'}
		,{header: 'Approver Name', width: 200, sortable: true, dataIndex: 'lp[approver_name]'}
		,{header: 'Status', width: 100, sortable: true, dataIndex: 'lp[status]'}
	]
);


var newOnlineLPColumns =  new Ext.grid.ColumnModel( 
	[
		new Ext.grid.CheckboxSelectionModel()
		,{header: 'Loan Number', width: 75, sortable: true, align: 'right', dataIndex: 'loan_no'}
		,{header: 'Loan Code', width: 50, sortable: true, dataIndex: 'loan_code'}
		,{header: 'Loan Date', width: 75, sortable: true, align: 'center', dataIndex: 'loan_date'}
		,{header: 'Principal', width: 100, sortable: true, dataIndex: 'principal', align: 'right', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}}
		,{header: 'Term', width: 40, sortable: true, align: 'right', dataIndex: 'term'}
		,{header: 'Rate', width: 40, sortable: true, align: 'right', dataIndex: 'rate', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}}
		,{header: 'Interest Amort', width: 100, sortable: true, align: 'right', dataIndex: 'interest_amortization', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}}
		,{header: 'Principal Amort', width: 100, sortable: true, align: 'right', dataIndex: 'principal_amortization', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}}
		,{header: 'Monthly Amort', width: 100, sortable: true, align: 'right', dataIndex: 'monthly_amortization', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}}
		,{header: 'Principal Balance', width: 100, sortable: true, align: 'right', dataIndex: 'principal_balance', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}}
	]
);

var ol_loanpaymentDetail = function(){
	return {
	xtype:'form'
	,region:'center'
	,id:'ol_loanpaymentDetail'
	,title: 'Details'
	,anchor: '100%'
	,frame: true
	,reader: pecaReaders.onlineLoanPaymentReader
	,items: [{
		layout:'form'
		,id:'ol_loanpaymentDetail_'
		,region:'center'
		//,hidden:true
		,bodyStyle:{'padding':'10px'}
		,buttons:[{
					text: 'Approve'
					,iconCls: 'icon_ext_approve'
					,hidden: true
					,id: 'online_loanpayment_approve'
					,handler: function(){
						var frm = Ext.getCmp('ol_loanpaymentDetail').getForm();
						frm.onApprove(frm);
					}
				},{
					text: 'Disapprove'
					,iconCls: 'icon_ext_disapprove'
					,hidden: true
					,id: 'online_loanpayment_disapprove'
					,handler: function(){
						Ext.Msg.prompt('Reason', 'Please enter the reason for disapproval:', function(btn, text){
							buttons: Ext.Msg.OKCANCEL
							if (btn == 'ok' && text.length > 0){
								Ext.getCmp('ol_loanpaymentDetail').getForm().findField('lp[peca_remarks]').setValue(text);
								var frm = Ext.getCmp('ol_loanpaymentDetail').getForm();
								frm.onDisapprove(frm);
							}
							else if(btn == 'ok' && text.length == 0){
								var element = Ext.getCmp('online_loanpayment_disapprove');
								element.handler.call(element.scope);
							}
						}
						,this
						,true
						,Ext.getCmp('ol_loanpaymentDetail').getForm().findField('lp[peca_remarks]').getValue());
					}
				},{
			text: 'Delete'
			,iconCls: 'icon_ext_del'
			,handler: function(){
				var frm = Ext.getCmp('ol_loanpaymentDetail').getForm();
				frm.onDelete();
		    }
		},{
			text:'Save'
			,iconCls: 'icon_ext_save'
		    ,handler: function(){
				var frm = Ext.getCmp('ol_loanpaymentDetail').getForm();
				if (frm.isModeNew()) {
					frm.onSave(frm, 2);
				} else {
					if(frm.isValid())
					frm.onUpdate(frm, 2);
				}
		    }
		},{
			text: 'Send'
			,iconCls: 'icon_ext_send'
			,handler: function(){
				var frm = Ext.getCmp('ol_loanpaymentDetail').getForm();
				if (frm.isModeNew()) {
					frm.onSave(frm, 1);
				} else {
					if(frm.isValid())
					frm.onUpdate(frm, 1);
				}
		    }
		}
		,{
			text: 'Cancel'
			,iconCls: 'icon_ext_cancel'
		    ,handler : function(btn){
				Ext.getCmp('ol_loanpaymentDetail').getForm().reset();
				//Ext.getCmp('ol_loanpaymentDetail').hide();
				//Ext.getCmp('ol_loanpaymentFilter').show();
				Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOnlineLoanPayment');
				Ext.getCmp('ol_loanpaymentDetail').getForm().findField('frm_mode').setValue(FORM_MODE_LIST);
				pecaDataStores.onlineLoanPaymentStore.reload();
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
		    xtype: 'textfield'
		    ,name: 'request_no'
		    ,submitValue: false
		    ,hidden: true
			},{
		    xtype: 'textfield'
		    ,name: 'lp[payor_id]'
		    ,hidden: true
			},{
		    xtype: 'textfield'
		    ,name: 'lp[transaction_code]'
		    ,hidden: true
			},{
		    xtype: 'textfield'
		    ,name: 'status_flag'
			,submitValue: false
		    ,hidden: true
			},newOnlineLPList(), updateOnlinePanel()
			]}
			,{
				layout: 'form'
				,height: 50
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
	    	return (Ext.getCmp('ol_loanpaymentDetail').getForm().findField('frm_mode').getValue() == FORM_MODE_NEW);
	    }
	    ,setModeNew: function() {
	    	Ext.getCmp('ol_loanpaymentDetail').getForm().findField('frm_mode').setValue(FORM_MODE_NEW);
	    	Ext.getCmp('ol_loanpaymentDetail_').buttons[5].setVisible(true);  //cancel button
			Ext.getCmp('ol_loanpaymentDetail_').buttons[2].setVisible(false);  //delete button
			Ext.getCmp('ol_loanpaymentDetail_').buttons[3].setVisible(true);  //save button
			Ext.getCmp('ol_loanpaymentDetail_').buttons[4].setVisible(true);  //send button
			Ext.getCmp('ol_loanpaymentDetail_').buttons[0].setVisible(false);  //approve button
			Ext.getCmp('ol_loanpaymentDetail_').buttons[1].setVisible(false);  //disapprove button
			
			Ext.getCmp('newOnlineLPList').setVisible(true);
			Ext.getCmp('updateOnlinePanel').setVisible(false);
			
		}
		,setModeUpdate: function() {
			Ext.getCmp('ol_loanpaymentDetail').getForm().findField('frm_mode').setValue(FORM_MODE_UPDATE);
			Ext.getCmp('ol_loanpaymentDetail_').buttons[5].setVisible(true);  //cancel button
			Ext.getCmp('ol_loanpaymentDetail_').buttons[2].setVisible(false);  //delete button
			Ext.getCmp('ol_loanpaymentDetail_').buttons[3].setVisible(false);  //save button
			Ext.getCmp('ol_loanpaymentDetail_').buttons[4].setVisible(false);  //send button
			Ext.getCmp('newOnlineLPList').setVisible(false);
			Ext.getCmp('updateOnlinePanel').setVisible(true);
			//Ext.getCmp('ol_loanpaymentDetail').getForm().findField('lp[payment_code]').setValue('Cross Charge');
			Ext.getCmp('ol_loanpaymentDetail_').buttons[0].setVisible(false);  //approve button
			Ext.getCmp('ol_loanpaymentDetail_').buttons[1].setVisible(false);  //disapprove button
			Ext.getCmp('ol_loanpaymentDetail').getForm().findField('lp[interest_amount]').setDisabled(true);
			Ext.getCmp('ol_loanpaymentDetail').getForm().findField('lp[member_remarks]').setDisabled(true);
			Ext.getCmp('ol_loanpaymentDetail').getForm().findField('lp[amount]').setDisabled(true);
			Ext.getCmp('ol_loanpaymentDetail').getForm().findField('lp[approvers]').setVisible(true);
			Ext.getCmp('online_loanpayment_approve').setVisible(false);
			Ext.getCmp('online_loanpayment_disapprove').setVisible(false);
	    }
		,onSave: function(frm, flag){
			var items = Ext.getCmp('newOnlineLPList').getSelectionModel().getSelections();
			if(items.length == 0){
				showExtInfoMsg('Please select loan item/s to pay');
				return false;
			}
			var rec = new Array();
			var total = 0;
			var _json = '[';
			Ext.each(items, function(r){
				if(_json!='[')
					_json+=',';
				_json+= '{"loan_no":"'+r.get('loan_no')+'","transaction_code":"'+r.get('loan_code')+'","payor_id":"'+r.get('employee_id')+'","amount":"'+r.get('monthly_amortization')+'","interest_amount":"'+r.get('interest_amortization')+'"}';
				total += Ext.num(r.get('monthly_amortization'), 0);
			});
			_json+= ']';
			Ext.Ajax.request({
				url: '/online_loan_payment/add' 
				,method: 'POST'
				,params: {auth:_AUTH_KEY, 'total': total, 'data': _json, 'user': _USER_ID, 'lp[created_by]': _USER_ID, 'lp[status_flag]': flag}
				,waitMsg: 'Creating Data...'
				,success: function(response, opts) {
					var obj = Ext.decode(response.responseText);
					if(obj.success){
						showExtInfoMsg(obj.msg);
						pecaDataStores.onlineLoanPaymentDetailStore.load({params: {employee_id: _EMP_ID}});
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
		,onUpdate: function(frm, flag){
			var _reqnum = Ext.getCmp('ol_loanpaymentDetail').getForm().findField('request_no').getValue();

			frm.submit({
    			url: '/online_loan_payment/update' 
    			,method: 'POST'
    			,waitMsg: 'Updating Data...'
    			,params: { auth:_AUTH_KEY, 'lp[modified_by]': _USER_ID, 'lp[request_no]': _reqnum, 'lp[status_flag]': flag,	'user': _USER_ID
    			}
    			,success: function(form, action) {
    				showExtInfoMsg( action.result.msg);
        			frm.setModeUpdate();
					if(!_IS_ADMIN && flag==2){
						Ext.getCmp('ol_loanpaymentDetail').getForm().findField('lp[member_remarks]').setDisabled(false);
						Ext.getCmp('ol_loanpaymentDetail').getForm().findField('lp[amount]').setDisabled(false);
						Ext.getCmp('ol_loanpaymentDetail').getForm().findField('lp[interest_amount]').setDisabled(false);
						Ext.getCmp('ol_loanpaymentDetail_').buttons[2].setVisible(true);  //delete button
						Ext.getCmp('ol_loanpaymentDetail_').buttons[3].setVisible(true);  //save button
						Ext.getCmp('ol_loanpaymentDetail_').buttons[4].setVisible(true);  //send button
					}
    			}
    			,failure: function(form, action) {
    				showExtErrorMsg( action.result.msg);
    			}	
    		});
		}
		,onApprove: function(frm){
			var _requestNum = Ext.getCmp('ol_loanpaymentDetail').getForm().findField('request_no').getValue();
			var _status = Ext.getCmp('ol_loanpaymentDetail').getForm().findField('status_flag').getValue();
			var _remarks = Ext.getCmp('ol_loanpaymentDetail').getForm().findField('lp[peca_remarks]').getValue();
			if( Ext.num(_status,0) < 3 || Ext.num(_status,0) > 8 ){
				showExtErrorMsg("Cannot be approved.");
				return false;
			}
/* 			 Ext.Ajax.request({
				url: '/online_loan_payment/approve' 
				,method: 'POST'
				,waitMsg: 'Approving...'
				,params: {auth:_AUTH_KEY, 'user': _USER_ID, 'data[modified_by]': _USER_ID,'data[request_no]' :_requestNum, 'data[status_flag]': _status, 'data[peca_remarks]': _remarks}
				,success: function(response, opts) {
					var obj = Ext.decode(response.responseText);
					if(obj.success){
						showExtInfoMsg(obj.msg);
						Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOnlineLoanPayment');
						Ext.getCmp('ol_loanpaymentDetail').setModeNew();
						pecaDataStores.onlineLoanPaymentStore.reload();
					}else{
						showExtErrorMsg(obj.msg);
					}
				}
				,failure: function(response, opts) {
					var obj = Ext.decode(response.responseText);
					showExtErrorMsg(obj.msg);
				}	
			}); */
			frm.submit({
				url: '/online_loan_payment/approve' 
				,method: 'POST'
				,params: {auth:_AUTH_KEY, 'user': _USER_ID, 'data[modified_by]': _USER_ID,'data[request_no]' :_requestNum, 'data[status_flag]': _status, 'data[peca_remarks]': _remarks}
				,waitMsg: 'Approving request...'
				,success: function(frm, action) {
					showExtInfoMsg(action.result.msg);
					Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOnlineLoanPayment');
					Ext.getCmp('ol_loanpaymentDetail').getForm().findField('frm_mode').setValue(FORM_MODE_LIST);
					Ext.getCmp('ol_loanpaymentDetail').getForm().reset();
					pecaDataStores.onlineLoanPaymentStore.reload();
				}
				,failure: function(frm, action) {
					if(action.result.error_code == '2'){
	    				Ext.Msg.confirm('Confirm Action',action.result.msg,function(btn) {
	    					if(btn=='yes'){
	    						frm.onUpdateOnDuplicate(frm);
	    					}
	    				});
    				}else{
    					showExtErrorMsg( action.result.msg);
    				}
				}	
			});
		}
		,onUpdateOnDuplicate: function(frm){
			var _requestNum = Ext.getCmp('ol_loanpaymentDetail').getForm().findField('request_no').getValue();
			var _remarks = Ext.getCmp('ol_loanpaymentDetail').getForm().findField('lp[peca_remarks]').getValue();
			
			frm.submit({
				url: '/online_loan_payment/updateOnDuplicate' 
				,method: 'POST'
				,params: {auth:_AUTH_KEY, 'user': _USER_ID,'request_no' :_requestNum, 'remarks': _remarks}
				,waitMsg: 'Updating loan payment...'
				,success: function(frm, action) {
					showExtInfoMsg(action.result.msg);
					Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOnlineLoanPayment');
					Ext.getCmp('ol_loanpaymentDetail').getForm().findField('frm_mode').setValue(FORM_MODE_LIST);
					Ext.getCmp('ol_loanpaymentDetail').getForm().reset();
					pecaDataStores.onlineLoanPaymentStore.reload();
				}
				,failure: function(frm, action) {
    				showExtErrorMsg( action.result.msg);
				}	
			});
		}
		,onDisapprove: function(frm){
			var _requestNum = Ext.getCmp('ol_loanpaymentDetail').getForm().findField('request_no').getValue();
			var _status = Ext.getCmp('ol_loanpaymentDetail').getForm().findField('status_flag').getValue();
			var _remarks = Ext.getCmp('ol_loanpaymentDetail').getForm().findField('lp[peca_remarks]').getValue();
			if( Ext.num(_status,0) < 3 || Ext.num(_status,0) > 8 ){
				showExtErrorMsg("Cannot be disapproved.");
				return false;
			}
			/*  Ext.Ajax.request({
				url: '/online_loan_payment/disapprove' 
				,method: 'POST'
				,waitMsg: 'Disapproving...'
				,params: {auth:_AUTH_KEY, 'user': _USER_ID, 'data[modified_by]': _USER_ID,'data[request_no]' :_requestNum, 'data[status_flag]': _status, 'data[peca_remarks]': _remarks}
				,success: function(response, opts) {
					var obj = Ext.decode(response.responseText);
					if(obj.success){
						showExtInfoMsg(obj.msg);
						Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOnlineLoanPayment');
						Ext.getCmp('ol_loanpaymentDetail').setModeNew();
						pecaDataStores.onlineLoanPaymentStore.reload();
					}else{
						showExtErrorMsg(obj.msg);
					}
				}
				,failure: function(response, opts) {
					var obj = Ext.decode(response.responseText);
					showExtErrorMsg(obj.msg);
				}
			}); */
			frm.submit({
				url: '/online_loan_payment/disapprove' 
				,method: 'POST'
				,params: {auth:_AUTH_KEY, 'user': _USER_ID, 'data[modified_by]': _USER_ID,'data[request_no]' :_requestNum, 'data[status_flag]': _status, 'data[peca_remarks]': _remarks}
				,waitMsg: 'Disapproving request...'
				,success: function(frm, action) {
					showExtInfoMsg(action.result.msg);
					Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOnlineLoanPayment');	
					Ext.getCmp('ol_loanpaymentDetail').getForm().findField('frm_mode').setValue(FORM_MODE_LIST);
					Ext.getCmp('ol_loanpaymentDetail').getForm().reset();
					pecaDataStores.onlineLoanPaymentStore.reload();
				}
				,failure: function(frm, action) {
					if(action.result)
						showExtErrorMsg(action.result.msg);
				}	
			});
		}
		,onDelete: function(){
			Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
				if(btn=='yes') {
					var _reqnum = Ext.getCmp('ol_loanpaymentDetail').getForm().findField('request_no').getValue();
					Ext.getCmp('ol_loanpaymentDetail').getForm().submit({
						url: '/online_loan_payment/delete' 
						,method: 'POST'
						,params: {auth:_AUTH_KEY, 'lp[modified_by]': _USER_ID, 'lp[request_no]': _reqnum}
						,waitMsg: 'Deleting Data...'
						,clientValidation: false
						,success: function(form, action) {
							showExtInfoMsg( action.result.msg);
			    			Ext.getCmp('ol_loanpaymentDetail').getForm().reset();
							Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOnlineLoanPayment');
							Ext.getCmp('ol_loanpaymentDetail').getForm().findField('frm_mode').setValue(FORM_MODE_LIST);
							Ext.getCmp('ol_loanpaymentDetail').setModeNew();
							//pecaDataStores.onlineLoanPaymentStore.reload();
							if (pecaDataStores.onlineLoanPaymentStore.getCount() % MAX_PAGE_SIZE == 1){
								var page = pecaDataStores.onlineLoanPaymentStore.getTotalCount() - MAX_PAGE_SIZE - 1;
								pecaDataStores.onlineLoanPaymentStore.load({params: {start:page<0?0:page, limit:MAX_PAGE_SIZE}});
							} else{
								pecaDataStores.onlineLoanPaymentStore.reload();
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

var ol_loanpaymentFilter = function(){
	return {
		xtype:'form'
		,id:'ol_loanpaymentFilter'
		,region:'center'
		//,anchor: '100%'
		,layout: 'border'
		//,flex: 1
		,defaults:{margins:'0 0 0 0'}
		,hidden: false
		,bodyStyle: 'background:transparent;'
		,autoscroll: true
		,items: [{			
			layout: 'form'
			,id: 'ol_loanpaymentFilterForm'
			,region:'north'
			,height: 175
			,frame: true
			,buttons:[
				{
					text: 'Search'
					,iconCls: 'icon_ext_search'
					,handler: function(){
						var _from = new Date(Ext.getCmp('ol_loanpayment_from').getValue());
						var _to = new Date(Ext.getCmp('ol_loanpayment_to').getValue());
						if( _to<_from && _from && _to){
							showExtErrorMsg("Submission Date From cannot be greater than Submission Date To.");
							return false;
						}
						pecaDataStores.onlineLoanPaymentStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
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
									,format : "m/d/Y"
									,anchor: '95%'
									,id: 'ol_loanpayment_from'
									,style: 'text-align: right'
									,emptyText: 'From'
									,maxLength: 10
									,validationEvent: 'change'
									,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
									,listeners: {
										specialkey: function(frm,evt){
											if (evt.getKey() == evt.ENTER) {
												var _from = new Date(Ext.getCmp('ol_loanpayment_from').getValue());
												var _to = new Date(Ext.getCmp('ol_loanpayment_to').getValue());
												if( _to<_from && _from && _to){
													showExtErrorMsg("Submission Date From cannot be greater than Submission Date To.");
													return false;
												}
												pecaDataStores.onlineLoanPaymentStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});					
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
									,format : "m/d/Y"
									,id: 'ol_loanpayment_to'
									,style: 'text-align: right'
									,emptyText: 'To'
									,maxLength: 10
									,validationEvent: 'change'
									,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
									,listeners: {
										specialkey: function(frm,evt){
											if (evt.getKey() == evt.ENTER) {
												var _from = new Date(Ext.getCmp('ol_loanpayment_from').getValue());
												var _to = new Date(Ext.getCmp('ol_loanpayment_to').getValue());
												if( _to<_from && _from && _to){
													showExtErrorMsg("Submission Date From cannot be greater than Submission Date To.");
													return false;
												}
												pecaDataStores.onlineLoanPaymentStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});					
											}
										}
									}
								}
							]
						}
					]
				},{
					layout: 'form'
					,labelWidth: 130
					,labelAlign: 'left'
					,layout: 'form'
					,width: 330
					,border: false
					,items: [{
							xtype: 'combo'
							,fieldLabel: 'Transaction Type'
							,anchor: '95%'
							,id: 'ol_loanpayment_ttype'
							,editable: true
							,typeAhead: true
							,triggerAction: 'all'
							,lazyRender:true
							,mode: 'local'
							,valueField: 'transaction_code'
							,displayField: 'transaction_description'									
							,forceSelection: true
							,submitValue: false
							,emptyText: 'Please Select'
							,store: pecaDataStores.paymentTypeCC		
							,enableKeyEvents: true
							,maxLength: 30
							,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
							,listeners: {
								specialkey: function(frm,evt){
									if (evt.getKey() == evt.ENTER) {
										var _from = new Date(Ext.getCmp('ol_loanpayment_from').getValue());
										var _to = new Date(Ext.getCmp('ol_loanpayment_to').getValue());
										if( _to<_from && _from && _to){
											showExtErrorMsg("Submission Date From cannot be greater than Submission Date To.");
											return false;
										}
										pecaDataStores.onlineLoanPaymentStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});					
									}
								}
							}
						}					
					]
					
				}		
				,{	
					layout: 'column'
					,border: false	
					,items:[{
						layout: 'form'
						,labelWidth: 130
						,labelAlign: 'left'
						,layout: 'form'
						,width: 330
						,border: false
						,items: [{
								xtype: 'combo'
								,fieldLabel: 'Status'
								,anchor: '95%'
								,id: 'ol_loanpayment_status'
								,editable: true
								,typeAhead: true
								,triggerAction: 'all'
								,lazyRender:true
								,value: '3'
								,mode: 'local'
								,valueField: 'status'
								,displayField: 'displayText'									
								,forceSelection: true
								,submitValue: false
								,emptyText: 'Please Select'
								,maxLength: 30
								,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
								,listeners: {
									specialkey: function(frm,evt){
										if (evt.getKey() == evt.ENTER) {
											var _from = new Date(Ext.getCmp('ol_loanpayment_from').getValue());
										var _to = new Date(Ext.getCmp('ol_loanpayment_to').getValue());
										if( _to<_from && _from && _to){
											showExtErrorMsg("Submission Date From cannot be greater than Submission Date To.");
											return false;
										}
										pecaDataStores.onlineLoanPaymentStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});					
										}
									}
								}
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
						}]
						}]
				},{
					layout: 'column'
					,border: false
					,id: 'online_loanpayment_search'
					,hidden: true
					,items: [				
						{
							layout: 'form'
							,labelWidth: 130
							,border: false
							,hideBorders: false
							,width: 330
							,items: [
								{
									xtype: 'textfield'
									,fieldLabel: 'Employee'	
									,emptyText: 'ID'	
									,anchor: '95%'
									,id: 'ol_loanpayment_id'
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
												var _from = new Date(Ext.getCmp('ol_loanpayment_from').getValue());
												var _to = new Date(Ext.getCmp('ol_loanpayment_to').getValue());
												if( _to<_from && _from && _to){
													showExtErrorMsg("Submission Date From cannot be greater than Submission Date To.");
													return false;
												}
												pecaDataStores.onlineLoanPaymentStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});					
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
							,hideBorders: false
							,width: 201
							,items: [
								{
									xtype: 'textfield'							
									,anchor: '95%'		
									,hideLabel: true
									,emptyText: 'Last Name'
									,id: 'ol_loanpayment_lastname'
									,maxLength: 30
									,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
									,enableKeyEvents: true
									,listeners: {
										specialkey: function(frm,evt){
											if (evt.getKey() == evt.ENTER) {
												var _from = new Date(Ext.getCmp('ol_loanpayment_from').getValue());
												var _to = new Date(Ext.getCmp('ol_loanpayment_to').getValue());
												if( _to<_from && _from && _to){
													showExtErrorMsg("Submission Date From cannot be greater than Submission Date To.");
													return false;
												}
												pecaDataStores.onlineLoanPaymentStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});					
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
									,id: 'ol_loanpayment_firstname'
									,maxLength: 30
									,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
									,enableKeyEvents: true
									,listeners: {
										specialkey: function(frm,evt){
											if (evt.getKey() == evt.ENTER) {
												var _from = new Date(Ext.getCmp('ol_loanpayment_from').getValue());
												var _to = new Date(Ext.getCmp('ol_loanpayment_to').getValue());
												if( _to<_from && _from && _to){
													showExtErrorMsg("Submission Date From cannot be greater than Submission Date To.");
													return false;
												}
												pecaDataStores.onlineLoanPaymentStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});					
											}
										}
									}
								}
							]
						}/* ,{
							xtype: 'button'
							//,id: 'sbutton'
							,text: 'Search'
							,iconCls: 'icon_ext_search' 
							,width: 100
							,handler: function(){
								pecaDataStores.onlineLoanPaymentStore.load({params: {
									'transaction_date_from': Ext.getCmp('ol_loanpayment_from').getRawValue()
									,'transaction_date_to': Ext.getCmp('ol_loanpayment_to').getRawValue()
									,'status': Ext.getCmp('ol_loanpayment_status').getValue()
									,'employee_id': Ext.getCmp('ol_loanpayment_id').getValue()
									,'last_name': Ext.getCmp('ol_loanpayment_lastname').getValue()
									,'first_name': Ext.getCmp('ol_loanpayment_firstname').getValue()
									,start:0, limit:MAX_PAGE_SIZE}});
							}
					}
					,{
							xtype: 'button'
							//,id: 'sbutton'
							,text: 'Preview'
							,iconCls: 'icon_ext_preview' 
							,width: 100
							,handler: function(){
								var frm = Ext.getCmp('ol_loanpaymentFilter').getForm();
								frm.onPreview(frm);
							}
					} */		
						
					]
				},{
					layout: 'form'
					,labelWidth: 130
					,labelAlign: 'left'
					,layout: 'form'
					,width: 330
					,border: false
					,items: [
						{
							xtype: 'numberfield'
							,fieldLabel: 'OR Number'
							,id: 'ol_loanpayment_or_no'
							,anchor: '95%'
							,maxLength: 10
							,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
							,style: 'text-align: right'
							,listeners: {
								specialkey: function(frm,evt){
									if (evt.getKey() == evt.ENTER) {
										var _from = new Date(Ext.getCmp('ol_loanpayment_from').getValue());
										var _to = new Date(Ext.getCmp('ol_loanpayment_to').getValue());
										if( _to<_from && _from && _to){
											showExtErrorMsg("Submission Date From cannot be greater than Submission Date To.");
											return false;
										}
										pecaDataStores.onlineLoanPaymentStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});					
									}
								}
							}
						}
					]
				}
			]

		}
		,ol_loanpaymentList()]
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
					url: '/report_onlineloanpaymentlist'
					,method: 'POST'
					,form: Ext.get('frmDownload')
					,params: {
							'transaction_date_from': Ext.getCmp('ol_loanpayment_from').getRawValue()
							,'transaction_date_to': Ext.getCmp('ol_loanpayment_to').getRawValue()
							,'status': Ext.getCmp('ol_loanpayment_status').getValue()
							,'employee_id': _IS_ADMIN?Ext.getCmp('ol_loanpayment_id').getValue():_EMP_ID
							,'last_name': Ext.getCmp('ol_loanpayment_lastname').getValue()
							,'first_name': Ext.getCmp('ol_loanpayment_firstname').getValue()
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

var ol_loanpaymentList = function(){
	return {
		xtype: 'grid'
		,id: 'ol_loanpaymentList'
		,titlebar: false
		,store: pecaDataStores.onlineLoanPaymentStore
		,region: 'center'
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,defaults:{margins:'0 0 0 0'}
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit: true
			,scrollOffset: 0
		}		
		,cm: ol_loanpaymentColumns
		
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					Ext.getCmp('updateOnlinePanel').setVisible(true);
					var rec = grid.getStore().getAt(row);
					var recVal = rec.get('lp[loan_no]');
					pecaDataStores.lpTypeStore.load({params: {
						 'lp[loan_no]': recVal
						,auth:_AUTH_KEY}
						,callback: function(r, options, success) {
							Ext.getCmp('ol_loanpaymentDetail').getForm().setModeUpdate();
							
							if( _IS_ADMIN && rec.get('lp[approver_name]')==_USER_ID && Ext.num(rec.get('status_flag'),0) >= 3 && Ext.num(rec.get('status_flag'),0) <= 7){
								Ext.getCmp('online_loanpayment_approve').setVisible(true);
								Ext.getCmp('online_loanpayment_disapprove').setVisible(true);
								Ext.getCmp('ol_loanpaymentDetail').getForm().findField('lp[peca_remarks]').setVisible(true);
								Ext.getCmp('ol_loanpaymentDetail').getForm().findField('lp[peca_remarks]').setDisabled(false);
								Ext.getCmp('ol_loanpaymentDetail').getForm().findField('lp[approvers]').setVisible(true);
							}
							else if(_IS_ADMIN && Ext.num(rec.get('status_flag'),0) > 2){
								Ext.getCmp('ol_loanpaymentDetail').getForm().findField('lp[peca_remarks]').setVisible(true);
								Ext.getCmp('ol_loanpaymentDetail').getForm().findField('lp[peca_remarks]').setDisabled(true);
								if(Ext.num(rec.get('status_flag'),0) != 10)
									Ext.getCmp('ol_loanpaymentDetail').getForm().findField('lp[approvers]').setVisible(true);
							}
							if(!_IS_ADMIN && Ext.num(rec.get('status_flag'),0)==2){
								Ext.getCmp('ol_loanpaymentDetail').getForm().findField('lp[member_remarks]').setDisabled(false);
								Ext.getCmp('ol_loanpaymentDetail').getForm().findField('lp[amount]').setDisabled(false);
								Ext.getCmp('ol_loanpaymentDetail').getForm().findField('lp[interest_amount]').setDisabled(false);
								Ext.getCmp('ol_loanpaymentDetail_').buttons[2].setVisible(true);  //delete button
								Ext.getCmp('ol_loanpaymentDetail_').buttons[3].setVisible(true);  //save button
								Ext.getCmp('ol_loanpaymentDetail_').buttons[4].setVisible(true);  //send button
							}
							
							//if rejected show peca remarks
							if(!_IS_ADMIN && Ext.num(rec.get('status_flag'),0)==10){
								Ext.getCmp('ol_loanpaymentDetail').getForm().findField('lp[peca_remarks]').setVisible(true);
								Ext.getCmp('ol_loanpaymentDetail').getForm().findField('lp[peca_remarks]').setDisabled(true);
							}
							else if(!_IS_ADMIN && Ext.num(rec.get('status_flag'),0)!=10){
								Ext.getCmp('ol_loanpaymentDetail').getForm().findField('lp[peca_remarks]').setVisible(false);
							}
			
							Ext.getCmp('ol_loanpaymentDetail').getForm().load({
								url: '/online_loan_payment/showHdr'
								,params: {	'lp[request_no]': rec.get('request_no')
											,auth:_AUTH_KEY}
								,method: 'POST'
								,waitMsgTarget: true
								,success: function(form, action) {}
								});
							Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOnlineLoanPaymentDetail');
						}
					});
				}
			}
			,'render':{
				scope:this
				,fn:function(grid){
					Ext.getCmp('ol_loanpayment_to').setValue(_TODAY);
					Ext.getCmp('ol_loanpayment_from').setValue(new Date(new Date(Ext.getCmp('ol_loanpayment_to').getValue())-1));
					if(_IS_ADMIN){
						Ext.getCmp('olp_add_btn').setVisible(false);
						Ext.getCmp('olp_btn_separator1').setVisible(false);
						Ext.getCmp('olp_del_btn').setVisible(false);
						Ext.getCmp('olp_btn_separator2').setVisible(false);
					}
					else{
						Ext.getCmp('ol_loanpaymentFilterForm').setHeight(150);
					}
					pecaDataStores.onlineLoanPaymentStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
					pecaDataStores.paymentTypeCC.load();
					
				}
			}
		}
		
		,tbar:[
		{
			text:'New'
			,id: 'olp_add_btn'
			,tooltip:'Add Loan Payment'
			,iconCls: 'icon_ext_add'
			,scope:this
			,handler:function(btn) {
				pecaDataStores.onlineLoanPaymentDetailStore.load({params: {employee_id: _EMP_ID}});
				
				Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOnlineLoanPaymentDetail');
				Ext.getCmp('ol_loanpaymentDetail').getForm().reset();
				ol_loanpaymentDetail().setModeNew();
				
			}
		},
		{
		text: '|'
		,id: 'olp_btn_separator1'
		}
		,{
			text:'Delete'
			,id: 'olp_del_btn'
			,tooltip:'Delete Loan Payment'
			,iconCls: 'icon_ext_del'
			,scope:this
			,handler:function(btn) {
				var index = Ext.getCmp('ol_loanpaymentList').getSelectionModel().getSelected();
		        if (!index) {
				showExtInfoMsg("Please select a loan payment to delete.");
		        	return false;
		        }
				if(index.data.status_flag==2){
					Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
						if(btn=='yes') {
							Ext.Ajax.request({
								url: '/online_loan_payment/delete' 
								,method: 'POST'
								,params: {'lp[request_no]': index.data.request_no
										, auth:_AUTH_KEY
										, 'lp[modified_by]': _USER_ID}
								,waitMsg: 'Deleting Data...'
								,success: function(response, opts) {
									var obj = Ext.decode(response.responseText);
									if(obj.success){
										showExtInfoMsg(obj.msg);
										if (pecaDataStores.onlineLoanPaymentStore.getCount() % MAX_PAGE_SIZE == 1){
											var page = pecaDataStores.onlineLoanPaymentStore.getTotalCount() - MAX_PAGE_SIZE - 1;
											pecaDataStores.onlineLoanPaymentStore.load({params: {start:page<0?0:page, limit:MAX_PAGE_SIZE}});
										} else{
											pecaDataStores.onlineLoanPaymentStore.reload();
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
		},
		{
		text: '|'
		,id: 'olp_btn_separator2'
		}
		,{
			text:'Print'
			,id: 'olp_print_btn'
			,tooltip:'Print'
			,iconCls: 'icon_ext_print'
			,scope:this
			,handler:function(btn) {
				var frm = Ext.getCmp('ol_loanpaymentFilter').getForm();
				frm.onPreview(frm);
			}
		}
		]
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.onlineLoanPaymentStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};

var updateOnlinePanel = function(){
	return{
		xtype: 'panel'
		,id: 'updateOnlinePanel'
		,border: false
		,hidden: true
		,items: [{
			xtype: 'panel'
			,bodyStyle:{'padding':'10px'}
			,border: true
			,items: [{
			layout: 'column'
            	,border: true
				,labelAlign: 'left'
				,items: [{
					layout: 'form'
					,width:250
					,border: false
					,items: [{
						xtype: 'textfield'
						,name: 'lp[loan_no]'
						,fieldLabel: 'Loan No.'
						,style: 'text-align: right'
						,readOnly: true
						,cls: 'x-item-disabled'
		                ,allowBlank: false
						,anchor: '100%'
						,width:250
		                ,required: true
						,autoCreate: {tag: 'input', type: 'numeric', maxlength: '10'}
					}]
					}]
        },{ layout: 'column'
            	,border: false
				,labelAlign: 'left'
				,items: [{
					layout: 'form'
					,width:250
					,border: false
					,items: [{
						xtype: 'textfield'
						,name: 'lp[employee_id]'
						,fieldLabel: 'Employee'
		                ,allowBlank: false
						,anchor: '100%'
						,width:250
		                ,required: true
						,disabled: true
						,style: 'text-align: right'
						,emptyText: 'ID'
						,autoCreate: {tag: 'input', type: 'numeric', maxlength: '8'}
					}]
					},{
					layout: 'form'
					,labelWidth: 1
					,border: false
					,items: [{
						xtype: 'textfield'
						,name: 'lp[last_name]'	
						,width: 150
						,fieldLabel: ' '
						,disabled: true
						//,anchor: '95%'
						,labelSeparator: ' '
						//,anchor: '95%'
						//,columnWidth:.2
						,emptyText: 'Last Name'
						,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
					}]
					},{
					layout: 'form'
					,labelWidth: 1
					,border: false
					,items: [{
						xtype: 'textfield'
						,name: 'lp[first_name]'	
						,width: 150
						,fieldLabel: ' '
						//,anchor: '95%'
						,labelSeparator: ' '
						,disabled: true
						//,columnWidth:.2
						,emptyText: 'First Name'
						,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
					}]
					}]
        }]
		
		},{
			xtype: 'panel'
			,bodyStyle:{'padding':'10px'}
			,border: true
			,items: [{ layout: 'column'
            	,border: false
				,labelAlign: 'left'
				,items: [{
					layout: 'form'
					,width:250
					,border: false
					,items: [{
						xtype: 'textfield'
						,name: 'lp[loan_code]'
						,disabled: true
						,fieldLabel: 'Loan Type'
						,anchor: '100%'
						,width:250
						,emptyText: 'Loan Code'
						,readOnly: true
						,autoCreate: {tag: 'input', type: 'text', maxlength: '8'}
					}]
					},{
					layout: 'form'
					,labelWidth: 1
					,border: false
					,items: [{
						xtype: 'textfield'
						,disabled: true
						,name: 'lp[loan_description]'	
						,width: 150
						,fieldLabel: ' '
						,labelSeparator: ' '
						,readOnly: true
						,emptyText: 'Loan Description'
						,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
					}]
					}]
        },{
			layout: 'column'
            	,border: true
				,labelAlign: 'left'
				,items: [{
					layout: 'form'
					,width:250
					,border: false
					,items: [{
						xtype: 'moneyfield'
						,disabled: true
						,name: 'lp[balance]'
						,fieldLabel: 'Loan Balance'
						,style: 'text-align: right'
						,readOnly: true
						,width:250
						,anchor: '100%'
						,autoCreate: {tag: 'input', type: 'numeric', maxlength: '16'}
					}]
			}]
        }]
		
		},{
			xtype: 'panel'
			,bodyStyle:{'padding':'10px'}
			,layout: 'column'
			,border: true
			,items: [{
				labelAlign: 'left'
				,layout: 'form'	
				,columnWidth: .95				
				,xtype: 'fieldset'
                ,autoScroll: true
				,title: 'Loan Payment Details'
				,bodyStyle:{'padding':'10px'}
				,items: [{
					xtype: 'combo'
					,fieldLabel: 'Payment Type'
					,width: 250
					,disabled: true
					,allowBlank: false
					,required: true
					,hiddenName: 'lp[transaction_code2]'
					,typeAhead: true
					,triggerAction: 'all'
					,lazyRender:true
					,store: pecaDataStores.lpTypeStore
					,mode: 'local'
					,valueField: 'payment_code'
					,displayField: 'payment_type_description'									
					,forceSelection: true
					,submitValue: false
					,emptyText: 'Please Select'
				},{
					xtype: 'datefield'
					,fieldLabel: 'Payment Date'
					,disabled: true
					,width: 200
					,allowBlank: false
					,required: true
					,value: _TODAY
					,style: 'text-align: right'
					,name: 'lp[payment_date]'
				},{
					xtype: 'moneyfield'
					,fieldLabel: 'Principal Amt'
					,width: 200
					,allowBlank: false
					,required: true
					,style: 'text-align: right'
					,name: 'lp[amount]'
					,maxLength: 16
					,autoCreate: {tag: 'input', type: 'text', maxlength: '16'}
				},{
					xtype: 'moneyfield'
					,fieldLabel: 'Interest Amt'
					,disabled: true
					,width: 200
					,allowBlank: false
					,required: true
					,style: 'text-align: right'
					,name: 'lp[interest_amount]'
					,maxLength: 16
					,autoCreate: {tag: 'input', type: 'text', maxlength: '16'}
				},{
					xtype: 'textarea'
					,fieldLabel: 'Member Remarks'
                	,maxLength: 50
					,width: 300
					,name: 'lp[member_remarks]'
				},{
					xtype: 'textarea'
					,fieldLabel: 'Peca Remarks'
                	,maxLength: 50
					,width: 300
					,name: 'lp[peca_remarks]'
				},{
					xtype: 'textarea'
					,fieldLabel: 'Approvers'
					,disabled: true
                	,maxLength: 50
					,width: 300
					,name: 'lp[approvers]'
				}]
			}]
		}]
	};
};

var newOnlineLPList = function(){
	return {
		xtype: 'grid'
		,id: 'newOnlineLPList'
		,titlebar: false
		,store:  pecaDataStores.onlineLoanPaymentDetailStore
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 300
		,loadMask: true
		,sm:  new Ext.grid.CheckboxSelectionModel({
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
			,scrollOffset:13
		}
		,cm: newOnlineLPColumns
	};
}; 

