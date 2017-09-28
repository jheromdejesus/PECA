//Let's pretend we rendered our grid-columns with meta-data from our ORM framework.
var journalColumns =  new Ext.grid.ColumnModel( 
	[
     {id: 'journal_no', header: 'Journal Number', align: 'center', sortable: true, width: 100, dataIndex: 'journalHdr[journal_no]'}
     ,{header: 'Particulars', sortable: true, width: 250, dataIndex: 'journalHdr_formated'}
     ,{header: 'Transaction Date', align: 'center',sortable: true, width: 150, dataIndex: 'journalHdr[transaction_date]'}
	]
);

var summaryAccount = new Ext.ux.grid.GridSummary();

var accountColumns =  new Ext.grid.ColumnModel( 
	[
     {id: 'account_no', header: 'Account Number', hidden: true, dataIndex: 'account_no'}
     ,{header: 'Journal Number', hidden: true, dataIndex: 'journal_no'}
     ,{header: 'Account Name', sortable: true, width: 50, dataIndex: 'account_name', summaryRenderer: function(v, params, data){
            return 'TOTAL:'; }}	 
	 ,{xtype:'numbercolumn', header: 'Debit', sortable: true, align: 'right', width: 25, dataIndex: 'debit', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}, summaryType: 'sum'}
     ,{xtype:'numbercolumn', header: 'Credit', sortable: true, align: 'right', width: 25, dataIndex: 'credit', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}, summaryType: 'sum'}
     ,{header: 'Debit Credit', hidden: true,  dataIndex: 'debit_credit'}	 
	]
);

var journalDtlWriter = new Ext.data.JsonWriter({
    encode: true
    ,writeAllFields: true
    ,listfull: true
});

var journalDtlProxy = new Ext.data.HttpProxy({
	api: {
	    read    : '/journal_entry/readDtl'
	    ,create  : '/journal_entry/addDtl'
	    ,update  : ''
	    ,destroy : ''
	}
	,listeners:{
		'beforeload':{
			scope:this
			,fn:function(dataproxy,params ){
				params.journal_no = Ext.getCmp('journalDetail').getForm().findField('journalHdr[journal_no]').getValue();
			}
		}
	}
});

var journalDetail = function(){
	return {
		xtype:'form'
		,id:'journalDetail'
		,region:'center'
		,title: 'Details'
		,hidden:true
		,anchor: '200%'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,reader: pecaReaders.journalReader
		,buttons:[{
			text: 'Cancel'
			,iconCls: 'icon_ext_cancel'
		    ,handler : function(btn){
				Ext.getCmp('journalDetail').hide();
				Ext.getCmp('journalList').show();
				Ext.getCmp('journalDetail').getForm().reset();
				pecaDataStores.journalStore.reload();
		    }
		},{
			text: 'Delete'
			,iconCls: 'icon_ext_del'
			,handler: function(){
				var frm = Ext.getCmp('journalDetail').getForm();
				frm.onDelete();
		    }
		},{
			text:'Save'
			,iconCls: 'icon_ext_save'
		    ,handler: function(){
		    	var frm = Ext.getCmp('journalDetail').getForm();
		    	if(frm.isValid()){
		    		if (frm.isModeNew()) {
			        	frm.onSave(frm);
		    		} else {
		    		   	frm.onUpdate(frm);
		            }
		    	}
		    }
		}
		,{
			text: 'Preview'
			,iconCls: 'icon_ext_preview'
		    ,handler : function(){
				var frm_ = Ext.getCmp('journalDetail').getForm();
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
					
					var jsonPass = '[';
					var flag = false;
					for(var i=0; i<pecaDataStores.debitCreditStore.getCount(); i++){
						flag = true;
						var foo = pecaDataStores.debitCreditStore.getAt(i);
						if(jsonPass != '[')
							jsonPass += ',';
						jsonPass += '{ "account_no":"' + foo.get('account_no') + '","account_name":"' + foo.get('account_name') + '","amount":' + foo.get('amount') + ',"debit_credit":"' + foo.get('debit_credit') + '"}';
						
					}
					jsonPass += ']';
					
					Ext.Ajax.request({
						url: "/Report_journalEntryDisbVoucher"
						,method: 'POST'
						,form: Ext.get('frmDownload')
						,params: {data : jsonPass
									,reference: frm_.findField('journalHdr[reference]').getValue()
									,transaction_date: frm_.findField('journalHdr[transaction_date]').getValue()
									,supplier_id: frm_.findField('journalHdr[supplier_id]').getValue()	
									,remarks: frm_.findField('journalHdr[remarks]').getValue()									
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
		    xtype: 'hidden'
		    ,name: 'frm_mode'
		    ,value: FORM_MODE_NEW
		    ,submitValue: false
		    ,listeners: {'change':{fn: function(obj,value){
            	}}}
			},{
				items: [{
	            layout: 'column'
            	,border: false
				,labelAlign: 'left'
	            ,items: [{
					layout: 'form'
					,width:250
					,columnWidth: .4
					,border: false
					,items: [{
						xtype: 'textfield'
						,name: 'journalHdr[journal_no]'
						,fieldLabel: 'Journal Entry No.'
		                ,readOnly: true
		                ,cls: 'x-item-disabled'
					 },new Ext.form.ComboBox({
						hiddenName: 'journalHdr[transaction_code]'
						,fieldLabel: 'Entry Type'
						,anchor: '100%'
		                ,id: 'transcode'
						,typeAhead: true
						,triggerAction: 'all'
						,lazyRender:true
						,store: pecaDataStores.entryStore
						,mode: 'local'
						,valueField: 'gl_code'
						,displayField: 'gl_description'	
						,forceSelection: true
						,submitValue: false
						,listeners:{
						 'select':{fn:function(combo, value, row) {
						 	rec = this.getStore().getAt(row);
						 	Ext.getCmp('journalDetail').getForm().findField('journalHdr[particulars]').setValue(rec.get('particulars'));
					 		
							if(rec.get('gl_code')=='MCLS'){
								//glDtlStore
								Ext.Ajax.request({
									url: '/gl_entries/readDtlMCLS' 
									,method: 'POST'
									,params: { gl_code : 'MCLS'
												,auth:_AUTH_KEY, 'journal[modified_by]': _USER_ID
												,user: _USER_ID}
									,success: function(response, opts) {
										var obj = Ext.decode(response.responseText);
										if(obj.success){
											pecaDataStores.debitCreditStore.removeAll();
											var debitAmount, creditAmount;
											for(var i=0; i<Ext.num(obj.total, 0); i++){
												if( obj.data[i].debit_credit == 'C'){
													debitAmount = '';
													creditAmount = obj.data[i].amount;
												}
												else{
													debitAmount = obj.data[i].amount;
													creditAmount = '';
												}
															
												var rec = new pecaDataStores.debitCreditStore.recordType({
													'account_no' : obj.data[i].account_no
													,'journal_no' : 0
													,'account_name' : obj.data[i].account_name
													,'amount' : obj.data[i].amount
													,'debit' : debitAmount
													,'credit' : creditAmount
													,'debit_credit' : obj.data[i].debit_credit
												});
												pecaDataStores.debitCreditStore.insert(0, rec);
											}
										}
									}
								});
							}
							else{
								//glDtlStore
								Ext.Ajax.request({
									url: '/gl_entries/readDtl' 
									,method: 'POST'
									,params: { gl_code : rec.get('gl_code')
												,auth:_AUTH_KEY, 'journal[modified_by]': _USER_ID
												,user: _USER_ID}
									,success: function(response, opts) {
										var obj = Ext.decode(response.responseText);
										if(obj.success){
											pecaDataStores.debitCreditStore.removeAll();
											var debitAmount, creditAmount;
											for(var i=0; i<Ext.num(obj.total, 0); i++){
												if( obj.data[i].debit_credit == 'C'){
													debitAmount = '';
													creditAmount = 0;
												}
												else{
													debitAmount = 0;
													creditAmount = '';
												}	
												var rec = new pecaDataStores.debitCreditStore.recordType({
													'account_no' : obj.data[i].account_no
													,'journal_no' : 0
													,'account_name' : obj.data[i].account_name
													,'amount' : 0
													,'debit' : debitAmount
													,'credit' : creditAmount
													,'debit_credit' : obj.data[i].debit_credit
												});
												pecaDataStores.debitCreditStore.insert(0, rec);
											}
										}
									}
								});
							}
							
							}}
	                    }
					}),{
						xtype: 'textfield'
						,name: 'journalHdr[particulars]'
						,fieldLabel: 'Particulars'
		                ,allowBlank: false
						,anchor: '100%'
		                ,required: true
						,maxLength: 40
						,autoCreate: {tag: 'input', type: 'string', maxlength: '40'}
					},{
						xtype: 'textfield'
						,name: 'journalHdr[reference]'
						,fieldLabel: 'Reference'
						,anchor: '100%'
						,autoCreate: {tag: 'input', type: 'string', maxlength: '10'}
					},{
						xtype: 'datefield'
						,name: 'journalHdr[transaction_date]'
						,fieldLabel: 'Transaction Date'
						,anchor: '100%'		
						,editable: true
						,value: _TODAY
						,style: 'text-align: right'
					},{
						xtype: 'datefield'
						,name: 'journalHdr[document_date]'
						,fieldLabel: 'Document Date'
						,anchor: '100%'
						,editable: true
						,value: _TODAY
						,style: 'text-align: right'
					},{
						xtype: 'numberfield'
						,name: 'journalHdr[document_no]'
						,fieldLabel: 'Document No'
						,anchor: '100%'
						,autoCreate: {tag: 'input', type: 'numeric', maxlength: '10'}
						,style: 'text-align: right'
					},{
						xtype: 'textfield'
						,name: 'journalHdr[remarks]'
						,fieldLabel: 'Remarks'
						,anchor: '100%'
						,autoCreate: {tag: 'input', type: 'text', maxlength: '50'}
					},{
						xtype: 'combo'
						,hiddenName: 'journalHdr[supplier_id]'
						,fieldLabel: 'Supplier ID'
						,anchor: '100%'
						,id: 'supplier'
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
					}]
					},{
					columnWidth: .05
					,html: '&nbsp;'
					},{
					xtype: 'panel'
					,layout: 'form'
					,id: 'journalTable'
					,columnWidth: .55
					,border: false
					,buttons:[{
						text: 'Add'
						,iconCls: 'icon_ext_add'
						,handler : function(btn){
						
							//var frm = Ext.getCmp('jlAccounts').getForm();
							//if(!frm.isValid()){
							//	return false;
							//}
							
							var col;
							var debitAmount = Ext.getCmp('debitField').getValue();
							var creditAmount = Ext.getCmp('creditField').getValue();
							var finalAmount;
								
							if( (creditAmount == null || creditAmount == '' || creditAmount == 0)  && (debitAmount == null || debitAmount == '' || debitAmount == 0)){
								showExtErrorMsg("Please specify debit or credit amount.");
								return false;
							}
							if( (creditAmount == null || creditAmount == '' || creditAmount == 0)  && (debitAmount != null || debitAmount != '' || debitAmount != 0)){
								finalAmount = debitAmount;
								col = 'D';
							}
							else if( (debitAmount == null || debitAmount == '' || debitAmount == 0)  && (creditAmount != null || creditAmount != '' || creditAmount != 0) ){
								finalAmount = creditAmount;
								col = 'C';
							}
							else {
								showExtErrorMsg("You can only input either debit or credit amount.");
								return false;
							}
							var accountID = Ext.getCmp('journalDetail').getForm().findField('account_id').getValue();
							//var accountName = accountID + ' - ' + Ext.getCmp('journalDetail').getForm().findField('account_id').lastSelectionText;
							var accountName = Ext.getCmp('journalDetail').getForm().findField('account_id').lastSelectionText;
							var journNo = Ext.getCmp('journalDetail').getForm().findField('journalHdr[journal_no]').getValue();
							if(accountID == ''){
									showExtErrorMsg('Please select an account name.'); 
									return false;
							}	
							
							for(var i=0; i<pecaDataStores.debitCreditStore.getCount(); i++){
								var foo = pecaDataStores.debitCreditStore.getAt(i);
								if( foo.get('account_no') == accountID && foo.get('debit_credit') == col ){
									showExtErrorMsg('Duplicate Entry for the Account ' + accountName); 
									return false;
								}
							}
							
							Ext.getCmp('newJournalEntry').addClass('x-item-disabled');
							Ext.getCmp('newJournalEntry').setDisabled(true);
							
							var rec = new pecaDataStores.debitCreditStore.recordType({
									'account_no' : accountID
									,'journal_no' : journNo
									,'account_name' : accountName
									,'amount' : finalAmount
									,'debit' : debitAmount
									,'credit' : creditAmount
									,'debit_credit' : col
							});
							
							console.log(rec);

							pecaDataStores.debitCreditStore.insert(0, rec);
							Ext.getCmp('account').setValue('');
							Ext.getCmp('debitField').setValue('');
							Ext.getCmp('creditField').setValue('');

					}
					},{
						text: 'Update'
							,iconCls: 'icon_ext_edit'
							,handler : function(btn){
							
								//var frm = Ext.getCmp('jlAccounts').getForm();
								//if(!frm.isValid()){
								//	return false;
								//}
								
								//Ext.getCmp('newJournalEntry').setEnabled(false);
								var col;
								var debitAmount = Ext.getCmp('debitField').getValue();
								var creditAmount = Ext.getCmp('creditField').getValue();
								
								if (!Ext.getCmp('debitField').validate() || !Ext.getCmp('creditField').validate()) {
									return false;
								}
								
								var finalAmount;
								
								
								if( (creditAmount == null || creditAmount == '' || creditAmount == 0)  && (debitAmount == null || debitAmount == '' || debitAmount == 0)){
									showExtErrorMsg("Please specify debit or credit amount.");
									return false;
								}
								if( (creditAmount == null || creditAmount == '' || creditAmount == 0)  && (debitAmount != null || debitAmount != '' || debitAmount != 0)){
									finalAmount = debitAmount;
									col = 'D';
								}
								else if( (debitAmount == null || debitAmount == '' || debitAmount == 0)  && (creditAmount != null || creditAmount != '' || creditAmount != 0) ){
									finalAmount = creditAmount;
									col = 'C';
								}
								else {
									showExtErrorMsg("You can only input either debit or credit amount.");
									return false;
								}
								var accountID = Ext.getCmp('journalDetail').getForm().findField('account_id').getValue();
								var journNo = Ext.getCmp('journalDetail').getForm().findField('journalHdr[journal_no]').getValue();
								//var accountName = accountID + ' - ' + Ext.getCmp('journalDetail').getForm().findField('account_id').lastSelectionText;
								var accountName = Ext.getCmp('journalDetail').getForm().findField('account_id').lastSelectionText;
								if(accountID == ''){
									showExtErrorMsg('Please select an account name.'); 
									return false;
								}
								var index = Ext.getCmp('accountList').getSelectionModel().getSelected();
								
								for(var i=0; i<pecaDataStores.debitCreditStore.getCount(); i++){
									var foo = pecaDataStores.debitCreditStore.getAt(i);
									if( foo.get('account_no') == accountID && foo.get('debit_credit') == col && index.get('account_no') != accountID){
										showExtErrorMsg('Duplicate Entry for the Account ' + accountName); 
										return false;
									}
								}
								
								Ext.getCmp('journalTable').buttons[0].removeClass('x-item-disabled');
								Ext.getCmp('journalTable').buttons[0].setDisabled(false);
								Ext.getCmp('journalTable').buttons[1].addClass('x-item-disabled');
								Ext.getCmp('journalTable').buttons[1].setDisabled(true);
								
								var index = Ext.getCmp('accountList').getSelectionModel().getSelected();

								index.set('account_no', accountID);
								index.set('account_name', accountName);
								index.set('debit', debitAmount);
								index.set('credit', creditAmount);
								index.set('amount', finalAmount);
								index.set('debit_credit', col);
								
								
								Ext.getCmp('account').setValue('');
								Ext.getCmp('debitField').setValue('');
								Ext.getCmp('creditField').setValue('');
							}
						}]
					,items: [ accountList()
							,{html:'&nbsp;'
							},{
								xtype: 'panel'
								,id: 'jlAccounts'
								,layout: 'column'
								,anchor: '100%'
								,items:[{
									xtype: 'combo'
									,hiddenName: 'account_id'
									,columnWidth: .50
									,id: 'account'
									,editable: true
									,typeAhead: true
									,triggerAction: 'all'
									,lazyRender:true
									,store: pecaDataStores.accountStore
									,mode: 'local'
									,valueField: 'account_no'
									,displayField: 'account_no_name' //'account_name'									
									,forceSelection: true
									,submitValue: false
									,emptyText: 'Please Select'
									},{
									xtype: 'moneyfield'
									,name: 'journal[debitField]'
									,id: 'debitField'
									,maxLength: 16
									,autoCreate: {tag: 'input', type: 'text', maxlength: '13'}
									,maxValue: 9999999999.99
									,minValue: 0.00
									,columnWidth: .25
									},{
									xtype: 'moneyfield'
									,name: 'journal[creditField]'
									,id: 'creditField'
									,maxLength: 16
									,autoCreate: {tag: 'input', type: 'text', maxlength: '13'}
									,maxValue: 9999999999.99
									,minValue: 0.00
									,columnWidth: .25
									}
								]
							},{
								xtype: 'panel'
								,layout: 'column'
								,anchor: '100%'
								,items:[{
									html: '<div align="center">Account Name</div>'
									,columnWidth: .50
									},{
									html: '<div align="center">Debit</div>'
									,columnWidth: .25
									},{
									html: '<div align="center">Credit</div>'
									,columnWidth: .25
									}
								]
							},{html:'&nbsp;'
							}]
					}]
        }]
		}]
	    ,isModeNew: function() {
	    	return (Ext.getCmp('journalDetail').getForm().findField('frm_mode').getValue() == FORM_MODE_NEW);
	    }
	    ,setModeNew: function() {
	    	Ext.getCmp('journalDetail').getForm().findField('frm_mode').setValue(FORM_MODE_NEW);
	    	Ext.getCmp('journalDetail').buttons[0].setVisible(true);  //cancel button
	    	Ext.getCmp('journalDetail').buttons[1].setVisible(false);  //delete button
	    	Ext.getCmp('journalDetail').buttons[2].setVisible(true);  //save button
	    	Ext.getCmp('journalDetail').getForm().findField(true);  //save button
	    	Ext.getCmp('journalTable').setVisible(true);
	    	//Ext.getCmp('journalDetail').getForm().findField('journalHdr[transaction_code]').setReadOnly(false);
			Ext.getCmp('journalDetail').getForm().findField('journalHdr[transaction_code]').removeClass('x-item-disabled');
			Ext.getCmp('journalDetail').getForm().findField('journalHdr[transaction_code]').setDisabled(false);
			Ext.getCmp('journalDetail').getForm().findField('journalHdr[particulars]').setReadOnly(false);
			Ext.getCmp('journalDetail').getForm().findField('journalHdr[particulars]').removeClass('x-item-disabled');
			Ext.getCmp('journalDetail').getForm().findField('journalHdr[particulars]').setReadOnly(false);
	    	Ext.getCmp('journalDetail').getForm().findField('journalHdr[journal_no]').setVisible(false);
	    	Ext.getCmp('journalDetail').getForm().findField('journalHdr[journal_no]').setValue('');
	    	Ext.getCmp('journalDetail').getForm().findField('journalHdr[transaction_date]').setValue(_TODAY);
	    	Ext.getCmp('journalDetail').getForm().findField('journalHdr[document_date]').setValue(_TODAY);

			Ext.getCmp('journalTable').buttons[0].removeClass('x-item-disabled');
			Ext.getCmp('journalTable').buttons[0].setDisabled(false);
			Ext.getCmp('journalTable').buttons[1].addClass('x-item-disabled');
			Ext.getCmp('journalTable').buttons[1].setDisabled(true);		
			
	    	Ext.getCmp('journalRemove').setDisabled(true);
			Ext.getCmp('journalRemove').addClass('x-item-disabled');
	    	Ext.getCmp('newJournalEntry').setDisabled(true);
			Ext.getCmp('newJournalEntry').addClass('x-item-disabled');

	    	//pecaDataStores.debitCreditStore.removeAll();
			Ext.getCmp('account').setValue('');
			Ext.getCmp('debitField').setValue('');
			Ext.getCmp('creditField').setValue('');
			pecaDataStores.debitCreditStore.load();
			pecaDataStores.supplierStore.load();
			pecaDataStores.accountStore.load();
			pecaDataStores.entryStore.load();
			pecaDataStores.debitCreditStore.load();
			
	    }
		,setModeUpdate: function() {
			Ext.getCmp('journalDetail').getForm().findField('frm_mode').setValue(FORM_MODE_UPDATE);
			Ext.getCmp('journalDetail').buttons[0].setVisible(true);  //cancel button
	    	Ext.getCmp('journalDetail').buttons[1].setVisible(true);  //delete button
	    	Ext.getCmp('journalDetail').buttons[2].setVisible(true);  //save button
	    	Ext.getCmp('journalTable').setVisible(true);
	    	
	    	//can't update record
			if(_PERMISSION[130]==0){
				Ext.getCmp('journalDetail').buttons[2].setDisabled(true);	
			}
			//can't delete record
			if(_PERMISSION[34]==0){
				Ext.getCmp('journalDetail').buttons[1].setDisabled(true);	
			}
			
			Ext.getCmp('journalDetail').getForm().findField('journalHdr[transaction_code]').setValue('');
	    	//Ext.getCmp('journalDetail').getForm().findField('journalHdr[transaction_code]').setReadOnly(true);
			Ext.getCmp('journalDetail').getForm().findField('journalHdr[transaction_code]').addClass('x-item-disabled');
			Ext.getCmp('journalDetail').getForm().findField('journalHdr[transaction_code]').setDisabled(true);
			Ext.getCmp('journalDetail').getForm().findField('journalHdr[particulars]').setReadOnly(true);
			Ext.getCmp('journalDetail').getForm().findField('journalHdr[particulars]').addClass('x-item-disabled');
			Ext.getCmp('journalTable').buttons[1].addClass('x-item-disabled');
	    	Ext.getCmp('journalDetail').getForm().findField('journalHdr[journal_no]').setVisible(true);
			
			Ext.getCmp('account').setValue('');
			Ext.getCmp('debitField').setValue('');
			Ext.getCmp('creditField').setValue('');
			
			pecaDataStores.supplierStore.load();
			pecaDataStores.accountStore.load();
			pecaDataStores.debitCreditStore.autoSave = false;
			Ext.getCmp('newJournalEntry').addClass('x-item-disabled');
			Ext.getCmp('newJournalEntry').setDisabled(true);
			Ext.getCmp('journalRemove').addClass('x-item-disabled');
			Ext.getCmp('journalRemove').setDisabled(true);
			Ext.getCmp('journalTable').buttons[0].removeClass('x-item-disabled');
			Ext.getCmp('journalTable').buttons[0].setDisabled(false);
			Ext.getCmp('journalTable').buttons[1].addClass('x-item-disabled');
			Ext.getCmp('journalTable').buttons[1].setDisabled(true);
	
		}
		,onSave: function(frm){
			var debitSum = 0, creditSum = 0;
			for(var i=0; i<pecaDataStores.debitCreditStore.getCount(); i++){
				var foo = pecaDataStores.debitCreditStore.getAt(i);
				creditSum +=  Ext.num(foo.get('credit'), 0);
				debitSum +=  Ext.num(foo.get('debit'), 0);
			}
			creditSum = Ext.util.Format.number(creditSum,'0,000,000,000.00');
			debitSum = Ext.util.Format.number(debitSum,'0,000,000,000.00');
			
			if( debitSum != creditSum ){
				showExtErrorMsg('Debit and Credit Total must be equal');
				return false;
			}
			var jsonCheck = '[';
			for(var i=0; i<pecaDataStores.debitCreditStore.getCount(); i++){
				var foo = pecaDataStores.debitCreditStore.getAt(i);
				if(jsonCheck != '[')
					jsonCheck += ',';
				jsonCheck += '{ "account_no":"' + foo.get('account_no') + '","account_name":"' + foo.get('account_name') + '","amount":' + foo.get('amount') + ',"debit_credit":"' + foo.get('debit_credit') + '"}';
				
			}
			jsonCheck += ']';
			frm.submit({
    			url: '/journal_entry/addHdr' 
    			,method: 'POST'
    			,params: {auth:_AUTH_KEY, data: jsonCheck, 'journalHdr[created_by]': _USER_ID, user: _USER_ID}
    			,waitMsg: 'Creating Data...'
    			,success: function(form, action) {
    				showExtInfoMsg(action.result.msg);
    				frm.setModeUpdate();
    				Ext.getCmp('journalDetail').getForm().findField('journalHdr[journal_no]').setValue(action.result.journal_no);
					var jsonPass = '[';
					var flag = false;
					for(var i=0; i<pecaDataStores.debitCreditStore.getCount(); i++){
						flag = true;
						var foo = pecaDataStores.debitCreditStore.getAt(i);
						if(jsonPass != '[')
							jsonPass += ',';
						jsonPass += '{ "account_no":"' + foo.get('account_no') + '","journal_no":"' + action.result.journal_no + '","account_name":"' + foo.get('account_name') + '","amount":' + foo.get('amount') + ',"debit_credit":"' + foo.get('debit_credit') + '"}';
						
					}
					jsonPass += ']';
					if(flag){
						Ext.Ajax.request({
				        	url: '/journal_entry/addDtl' 
							,method: 'POST'
							,params: { data : jsonPass
										,auth:_AUTH_KEY, 'journal[modified_by]': _USER_ID
										,user: _USER_ID}
							,success: function(response, opts) {
								pecaDataStores.debitCreditStore.load();	
							}
			        	});
					}
					//pecaDataStores.debitCreditStore.load({params: {journal_no: action.result.journal_no}});
    			}
    			,failure: function(form, action) {
    					showExtErrorMsg(action.result.msg);
    			}	
    		});
		}
		,onUpdate: function(frm){
			var debitSum = 0, creditSum = 0;
			var jsonPass = '[';
			var flag = false;
			for(var i=0; i<pecaDataStores.debitCreditStore.getCount(); i++){
				flag = true;
				var foo = pecaDataStores.debitCreditStore.getAt(i);	
				creditSum +=  Ext.num(foo.get('credit'), 0);
				debitSum +=  Ext.num(foo.get('debit'), 0);
				if(jsonPass != '[')
					jsonPass += ',';
				jsonPass += '{ "account_no":"' + foo.get('account_no') + '","journal_no":"' + foo.get('journal_no') + '","account_name":"' + foo.get('account_name') + '","amount":' + foo.get('amount') + ',"debit_credit":"' + foo.get('debit_credit') + '"}';
				
			}
			jsonPass += ']';
			
			creditSum = Ext.util.Format.number(creditSum,'0,000,000,000.00');
			debitSum = Ext.util.Format.number(debitSum,'0,000,000,000.00');
			
			if( debitSum != creditSum ){
				showExtErrorMsg('Debit and Credit Total must be equal');
				return false;
			}
			frm.submit({
    			url: '/journal_entry/updateHdr' 
    			,method: 'POST'
    			,waitMsg: 'Updating Data...'
    			,params: { auth:_AUTH_KEY, data: jsonPass, 'journalHdr[modified_by]': _USER_ID, user: _USER_ID}	
				,clientValidation: false
    			,success: function(form, action) {
        			var frm = Ext.getCmp('journalDetail').getForm();
					if(flag){
						Ext.Ajax.request({
				        	url: '/journal_entry/addDtl' 
							,method: 'POST'
							,params: { data : jsonPass
										,auth:_AUTH_KEY, 'journal[modified_by]': _USER_ID
										,user: _USER_ID}
							,success: function(response, opts) {
								pecaDataStores.debitCreditStore.load();	
							}
			        	});
					}
        			showExtInfoMsg(action.result.msg);
        			frm.setModeUpdate();
        			pecaDataStores.debitCreditStore.load();	
    			}
    			,failure: function(form, action) {
    				showExtErrorMsg(action.result.msg);
    			}	
    		});
			//pecaDataStores.debitCreditStore.save();
    		//pecaDataStores.debitCreditStore.reload();
		}
		,onDelete: function(){
			Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
				if(btn=='yes') {
					Ext.getCmp('journalDetail').getForm().submit({
						url: '/journal_entry/deleteHdr' 
						,method: 'POST'
						,params: {auth:_AUTH_KEY, 'journal[modified_by]': _USER_ID}
						,waitMsg: 'Deleting Data...'
						,clientValidation: false
						,success: function(form, action) {
							showExtInfoMsg(action.result.msg);
			    			Ext.getCmp('journalDetail').getForm().reset();
			    			Ext.getCmp('journalDetail').hide();
							Ext.getCmp('journalList').show();
							Ext.getCmp('journalDetail').setModeNew();
							//pecaDataStores.journalStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
							if (pecaDataStores.journalStore.getCount() % MAX_PAGE_SIZE == 1){
								var page = pecaDataStores.journalStore.getTotalCount() - MAX_PAGE_SIZE - 1;
								pecaDataStores.journalStore.load({params: {start:page<0?0:page, limit:MAX_PAGE_SIZE}});
							} else{
								pecaDataStores.journalStore.reload();
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

var journalList = function(){
	return {
		xtype: 'grid'
		,id: 'journalList'
		,titlebar: false
		,store: pecaDataStores.journalStore
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
		,cm: journalColumns
		
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
					Ext.getCmp('journalList').hide();
					Ext.getCmp('journalDetail').show();
					Ext.getCmp('journalDetail').getForm().setModeUpdate();
					Ext.getCmp('journalDetail').getForm().load({
				    	url: '/journal_entry/show'
				    	,params: {'journalHdr[journal_no]':(rec.get('journalHdr[journal_no]'))
									,auth:_AUTH_KEY}
				    	,method: 'POST'
				    	,waitMsgTarget: true
						,success: function(response, opts) {
							pecaDataStores.debitCreditStore.load();
						}
						,failure: function(response, opts) {
								var obj = Ext.decode(response.responseText);
								showExtErrorMsg(obj.msg);
						}
					});
					
				}
			}
			,'render':{
				scope:this
				,fn:function(grid){
					//search textfield and button
					if(_PERMISSION[58]==0){
						Ext.getCmp('journal_no').setDisabled(true);
						Ext.getCmp('journalSearchID').setDisabled(true);	
					}else{
						Ext.getCmp('journal_no').setDisabled(false);
						Ext.getCmp('journalSearchID').setDisabled(false);
						pecaDataStores.journalStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
					}
					//new button
					if(_PERMISSION[9]==0){
						Ext.getCmp('journalNewID').setDisabled(true);	
					}else{
						Ext.getCmp('journalNewID').setDisabled(false);
					}
					//delete button
					if(_PERMISSION[34]==0){
						Ext.getCmp('journalDeleteID').setDisabled(true);	
					}else{
						Ext.getCmp('journalDeleteID').setDisabled(false);
					}
				}
			}
		}
		,tbar:[{
			xtype: 'label'
			,text: 'Journal Entry No :'
            ,fieldLabel: ' '
            ,labelSeparator: ' '
		},{
            xtype: 'textfield'
            ,width: 70
			,id: 'journal_no'
			,name: 'journal_no'
            ,hideLabel: true
			,autoCreate: {tag: 'input', type: 'numeric', maxlength: '10'}
			,enableKeyEvents: true
			,listeners: {
				specialkey: function(frm,evt){
					if (evt.getKey() == evt.ENTER) {
						pecaDataStores.journalStore.load({params: {
							'journal_no': Ext.getCmp('journal_no').getValue()
							,start:0
							,limit:MAX_PAGE_SIZE
							,auth:_AUTH_KEY}});						
					}
				}
			}
		},' ',{
			text:'Search'
			,id: 'journalSearchID'
			,iconCls: 'icon_ext_search'
			,scope:this
			,handler:function(btn) {
				pecaDataStores.journalStore.load({params: {
							'journal_no': Ext.getCmp('journal_no').getValue()
							,start:0
							,limit:MAX_PAGE_SIZE
							,auth:_AUTH_KEY}});
			}
		},'-'
		,{
			text:'New'
			,id: 'journalNewID'
			,tooltip:'Add a New Journal Entry'
			,iconCls: 'icon_ext_add'
			,scope:this
			,handler:function(btn) {
				Ext.getCmp('journalDetail').show();
				Ext.getCmp('journalList').hide();
				journalDetail().setModeNew();
			}
		},'-'
		,{
			text:'Delete'
			,id: 'journalDeleteID'
			,tooltip:'Delete Selected Journal Entry'
			,iconCls: 'icon_ext_del'
			,scope:this
			,handler:function(btn) {
				var index = Ext.getCmp('journalList').getSelectionModel().getSelected();
		        if (!index) {
					showExtInfoMsg("Please select a Journal Entry to delete.");
		            return false;
		        }
		        Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
		        	if(btn=='yes') {
				        Ext.Ajax.request({
				        	url: '/journal_entry/deleteHdr' 
							,method: 'POST'
							,params: {'journalHdr[journal_no]':index.data.journal_no
				        				,auth:_AUTH_KEY, 'journal[modified_by]': _USER_ID}
							,waitMsg: 'Deleting Data...'
							,success: function(response, opts) {
								var obj = Ext.decode(response.responseText);
								if(obj.success){
									showExtInfoMsg(obj.msg);
									//pecaDataStores.journalStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
									if (pecaDataStores.journalStore.getCount() % MAX_PAGE_SIZE == 1){
										var page = pecaDataStores.journalStore.getTotalCount() - MAX_PAGE_SIZE - 1;
										pecaDataStores.journalStore.load({params: {start:page<0?0:page, limit:MAX_PAGE_SIZE}});
									} else{
										pecaDataStores.journalStore.reload();
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
	        ,store: pecaDataStores.journalStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};

var accountList = function(){
	return {
		xtype: 'grid'
		,id: 'accountList'
		,titlebar: false
		,store: pecaDataStores.debitCreditStore
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 200
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,plugins: [summaryAccount]
		,viewConfig: {
			forceFit:true
			,scrollOffset:13
		}
		,cm: accountColumns
		,listeners:{
			'rowclick':{
				scope:this
				,fn:function(grid, row, e) {
					var index = Ext.getCmp('accountList').getSelectionModel().getSelected();
					Ext.getCmp('journalRemove').removeClass('x-item-disabled');
					Ext.getCmp('journalRemove').setDisabled(false);
			        if (!index) {
						showExtInfoMsg("Please select an entry to edit.");
						return false;
					}
					Ext.getCmp('newJournalEntry').removeClass('x-item-disabled');
					Ext.getCmp('newJournalEntry').setDisabled(false);
					Ext.getCmp('journalTable').buttons[0].addClass('x-item-disabled');
					Ext.getCmp('journalTable').buttons[0].setDisabled(true);
			        Ext.getCmp('journalTable').buttons[1].removeClass('x-item-disabled');
					Ext.getCmp('journalTable').buttons[1].setDisabled(false);
					Ext.getCmp('account').setValue(index.data.account_no);
					Ext.getCmp('debitField').setValue(index.data.debit);
					Ext.getCmp('creditField').setValue(index.data.credit);
					/*record = pecaDataStores.debitCreditStore.queryBy(function(record,id){ 
						return record.get('account_no')>0;
					}); 
					record.each(function(item,index){ 
						alert(item.get('account_no'));
						alert(item.get('journal_no'));
						alert(item.get('account_name'));
						alert(item.get('amount'));
					}); */
				}
			}
		}
		,tbar:[{
			text:'Remove'
			,tooltip:'Remove Selected Entry'
			,id: 'journalRemove'
			,iconCls: 'icon_ext_del'
			,scope:this
			,handler:function(btn) {
				Ext.getCmp('journalRemove').addClass('x-item-disabled');
				Ext.getCmp('journalRemove').setDisabled(true);
				var index = Ext.getCmp('accountList').getSelectionModel().getSelected();
		        if (!index) {
					showExtInfoMsg("Please select an entry to delete.");
		            return false;
		        }

				Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
					if(btn=='yes') {
						Ext.getCmp('accountList').store.remove(index);
						showExtInfoMsg("Entry Removed.");
						Ext.getCmp('account').setValue('');
						Ext.getCmp('debitField').setValue('');
						Ext.getCmp('creditField').setValue('');
						Ext.getCmp('newJournalEntry').setDisabled(false);
						Ext.getCmp('newJournalEntry').removeClass('x-item-disabled');
						Ext.getCmp('journalTable').buttons[0].removeClass('x-item-disabled');
						Ext.getCmp('journalTable').buttons[0].setDisabled(false);
						Ext.getCmp('journalTable').buttons[1].addClass('x-item-disabled');
						Ext.getCmp('journalTable').buttons[1].setDisabled(true);
					}
				}
				
				
				);
			}
			}
			,{
				text:'New'
				,id: 'newJournalEntry'
				//,disabled: true
				,tooltip:'Add a new Entry'
				,iconCls: 'icon_ext_add'
				,scope:this
				,handler:function(btn) {
					Ext.getCmp('newJournalEntry').addClass('x-item-disabled');
					Ext.getCmp('journalTable').buttons[0].removeClass('x-item-disabled');
					Ext.getCmp('journalTable').buttons[1].addClass('x-item-disabled');
					Ext.getCmp('newJournalEntry').setDisabled(true);
					Ext.getCmp('journalTable').buttons[0].setDisabled(false);
					Ext.getCmp('journalTable').buttons[1].setDisabled(true);
			        Ext.getCmp('account').setValue('');
			        Ext.getCmp('account').focus();
					Ext.getCmp('debitField').setValue('');
					Ext.getCmp('creditField').setValue('');
				}
			}
		]
		//,bbar: new Ext.PagingToolbar({
	    //   store: pecaDataStores.debitCreditStore
	    //    ,displayInfo: false
	    //})
	};
};