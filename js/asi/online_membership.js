//Let's pretend we rendered our grid-columns with meta-data from our ORM framework.
var om_column={
	ol_membershipColumns: new Ext.grid.ColumnModel([
		{header: 'Request Date', width: 80, sortable: true, dataIndex: 'online_membership[request_date]',align:'center'}
		,{header: 'Employee Name', width: 150, sortable: true, dataIndex: 'online_membership[employee_name]'}
		,{header: 'Approver Name', width: 150, sortable: true, dataIndex: 'online_membership[approver_name]'}
		,{header: 'Status', width: 80, sortable: true, dataIndex: 'online_membership[status]',align:'left'}
		,{header: ' ', width: 80, sortable: true, dataIndex: 'online_membership[employee_id]', hidden:true}
		,{header: ' ', width: 10, sortable: true, dataIndex: 'online_membership[request_no]', hidden:true}
		,{header: ' ', width: 10, sortable: true, dataIndex: 'approver1', hidden:true}
		,{header: ' ', width: 10, sortable: true, dataIndex: 'approver2', hidden:true}
		,{header: ' ', width: 10, sortable: true, dataIndex: 'approver3', hidden:true}
		,{header: ' ', width: 10, sortable: true, dataIndex: 'approver4', hidden:true}
		,{header: ' ', width: 10, sortable: true, dataIndex: 'approver5', hidden:true}
	])
	,membershipLoanInfoColumns:  new Ext.grid.ColumnModel([
		{id:'loan_no', header: 'Loan Number', width: 75, sortable: true, align: 'right', dataIndex: 'loan_no'}
		,{header: 'Loan Code', width: 50, sortable: true, dataIndex: 'loan_code'}
		,{header: 'Loan Date', width: 75, sortable: true, align: 'center', dataIndex: 'loan_date'}
		,{header: 'Principal', width: 100, sortable: true, dataIndex: 'principal', align: 'right', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}}
		,{header: 'Term', width: 40, sortable: true, align: 'right', dataIndex: 'term'}
		,{header: 'Rate', width: 40, sortable: true, align: 'right', dataIndex: 'rate', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}, 
			summaryRenderer: function(v, params, data){
				return "Total"; }
			}
		,{header: 'Interest Amortization', width: 100, sortable: true, align: 'right', dataIndex: 'interest_amortization', 
			renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}
			, summaryRenderer: function(v, params, data){
				record = pecaDataStores.ol_memberLoanInfoStore.queryBy(function(record,id){ 
					return parseFloat(record.get('principal_balance'))>0;
				}); 
				var sum_monthly = 0;
				record.each(function(item,index){ 
					sum_monthly += parseFloat(item.get('interest_amortization'));
				});
				return Ext.util.Format.number(sum_monthly,'0,000,000,000.00'); }
			}
		,{header: 'Principal Amortization', width: 100, sortable: true, align: 'right', dataIndex: 'principal_amortization', 
			renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}
			, summaryRenderer: function(v, params, data){
				record = pecaDataStores.ol_memberLoanInfoStore.queryBy(function(record,id){ 
					return parseFloat(record.get('principal_balance'))>0;
				}); 
				var sum_monthly = 0;
				record.each(function(item,index){ 
					sum_monthly += parseFloat(item.get('principal_amortization'));
				});
				return Ext.util.Format.number(sum_monthly,'0,000,000,000.00'); }
			}
		,{header: 'Principal Balance', width: 100, sortable: true, align: 'right', dataIndex: 'principal_balance', 
			renderer:function(value,rec){
				return Ext.util.Format.number(value,'0,000,000,000.00');
			}, summaryType: 'sum'}
	])
	,membershipLoanPaymentsColumns: new Ext.grid.ColumnModel([
		{header: 'Payment Date', width: 50, sortable: true, align: 'center', dataIndex: 'payment_date'}
		,{header: 'Amount', width: 75, sortable: true, dataIndex: 'amount', align: 'right', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}}
		,{header: 'Interest', width: 75, sortable: true, align: 'right', dataIndex: 'interest', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}}
		,{header: 'Description', width: 100, sortable: true, dataIndex: 'description'}
		,{header: 'Balance', width: 75, sortable: true, align: 'right', dataIndex: 'balance', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}}
	])
	,membershipChargesColumns: new Ext.grid.ColumnModel([
		{header: 'Description', width: 100, sortable: true, dataIndex: 'description'}
		,{header: 'Amount', width: 100, sortable: true, align: 'right', dataIndex: 'amount', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}}
	])
	,membershipCoMakersColumns: new Ext.grid.ColumnModel([
		{header: 'Employee ID', width: 50, sortable: true, dataIndex: 'employee_id'}
		,{header: 'Employee Name', width: 100, sortable: true, dataIndex: 'employee_name'}
	])
	,om_guaranteedLoansColumns: new Ext.grid.ColumnModel([
		{id:'loan_no', header: 'Loan Number', width: 75, sortable: true, align: 'right', dataIndex: 'loan_no'}
		,{header: 'Loan Code', width: 50, sortable: true, dataIndex: 'loan_code'}
		,{header: 'Loan Date', width: 75, sortable: true, align: 'center', dataIndex: 'loan_date'}
		,{header: 'Employee Name', width: 150, sortable: true, dataIndex: 'employee_name'}
		,{header: 'Principal', width: 100, sortable: true, align: 'right', dataIndex: 'principal', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}}
		,{header: 'Interest Amortization', width: 100, sortable: true, align: 'right', dataIndex: 'interest_amortization', 
			renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}
			, summaryRenderer: function(v, params, data){
				record = pecaDataStores.ol_guaranteedLoansStore.queryBy(function(record,id){ 
					return parseFloat(record.get('principal_balance'))>0;
				}); 
				var sum_monthly = 0;
				record.each(function(item,index){ 
					sum_monthly += parseFloat(item.get('interest_amortization'));
				});
				return Ext.util.Format.number(sum_monthly,'0,000,000,000.00'); }
			}
		,{header: 'Principal Amortization', width: 100, sortable: true, align: 'right', dataIndex: 'principal_amortization', 
			renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}
			, summaryRenderer: function(v, params, data){
				record = pecaDataStores.ol_guaranteedLoansStore.queryBy(function(record,id){ 
					return parseFloat(record.get('principal_balance'))>0;
				}); 
				var sum_monthly = 0;
				record.each(function(item,index){ 
					sum_monthly += parseFloat(item.get('principal_amortization'));
				});
				return Ext.util.Format.number(sum_monthly,'0,000,000,000.00'); }
			}
		,{header: 'Principal Balance', width: 100, sortable: true, align: 'right', dataIndex: 'principal_balance', renderer:function(value,rec){
			return Ext.util.Format.number(value,'0,000,000,000.00');}, summaryType: 'sum'}
	])
	,om_transactionHistoryColumns:  new Ext.grid.ColumnModel([
		{header: 'Date', width: 50, sortable: false, align: 'center', dataIndex: 'transaction_date'}
		,{header: 'Code', width: 25, sortable: false, dataIndex: 'transaction_code'}
		,{header: 'Amount', width: 75, sortable: false, dataIndex: 'transaction_amount', align: 'right'}
		,{header: 'Balance', width: 75, sortable: false, align: 'right', dataIndex: 'balance'}
	])
};
var om_store={
	myStore: new Ext.data.SimpleStore({fields: ['name', 'value']})
	,om_beneficiaryStorage: new Ext.data.Store({
		url: '/membership/readBeneficiary'
	    ,reader: new Ext.data.JsonReader({
			totalProperty: 'total'	
			,root: 'data'
			},[
				{name: 'beneficiary', mapping: 'beneficiary', type: 'string'}
				,{name: 'relationship', mapping: 'relationship', type: 'string'}
				,{name: 'beneficiary_address', mapping: 'beneficiary_address'}
				,{name: 'description', mapping: 'description'}
			]
		)
	    ,baseParams: {auth:_AUTH_KEY}
	})
	,statusStore: new Ext.data.SimpleStore({fields: ['value', 'name'], data: [['A', 'Active'],['I', 'Inactive']]})
	,beneficiaryStore: new Ext.data.SimpleStore({fields: ['initial', 'name'], data: [['S', 'Spouse'],['P', 'Parent'],['C', 'Child'],['O', 'Others']]})
	,membershipLoanPaymentsStore: new Ext.data.Store({
		proxy: new Ext.data.HttpProxy({
				url: '/membership/readLoanPayment'
				,listeners:{
					'beforeload':{
						scope:this
						,fn:function(dataproxy,params ){
							if(Ext.getCmp('membership_loan_info_loan_no').getValue())
								params.loan_no = Ext.getCmp('membership_loan_info_loan_no').getValue();
						}
					}
				}
		})
		,reader: new Ext.data.JsonReader({
			totalProperty: 'total'	
			,root: 'data'
			},[
				{name: 'payment_date', mapping: 'payment_date', type: 'date', convert:function(value,rec){
					return formatDate(value);}}
				,{name: 'amount', mapping: 'amount', type: 'float'}
				,{name: 'interest', mapping: 'interest', type: 'float'}
				,{name: 'description', mapping: 'transaction_description', type: 'string'}
				,{name: 'balance', mapping: 'balance', type: 'float'}
			]
		)
		,baseParams: {auth:_AUTH_KEY}
	})
	,membershipChargesStore: new Ext.data.Store({
		proxy: new Ext.data.HttpProxy({
				url: '/membership/readLoanCharges'
				,listeners:{
					'beforeload':{
						scope:this
						,fn:function(dataproxy,params ){
							if(Ext.getCmp('membership_loan_info_loan_no').getValue())
								params.loan_no = Ext.getCmp('membership_loan_info_loan_no').getValue();
						}
					}
				}
		})
		,reader: new Ext.data.JsonReader({
			totalProperty: 'total'	
			,root: 'data'
			},[
				{name: 'description', mapping: 'transaction_description', type: 'string'}
				,{name: 'amount', mapping: 'amount', type: 'string'}
			]
		)
		,baseParams: {auth:_AUTH_KEY}
	})
	,membershipCoMakersStore: new Ext.data.Store({
		proxy: new Ext.data.HttpProxy({
				url: '/membership/readLoanComakers'
				,listeners:{
					'beforeload':{
						scope:this
						,fn:function(dataproxy,params ){
							if(Ext.getCmp('membership_loan_info_loan_no').getValue())
								params.loan_no = Ext.getCmp('membership_loan_info_loan_no').getValue();
						}
					}
				}
		})
		,reader: new Ext.data.JsonReader({
			totalProperty: 'total'	
			,root: 'data'
			},[
				{name: 'employee_id', mapping: 'employee_id', type: 'string'}
				,{name: 'first_name', mapping: 'first_name', type: 'string'}
				,{name: 'middle_name', mapping: 'middle_name', type: 'string'}
				,{name: 'employee_name', mapping: 'last_name', type: 'string', convert:function(value,rec){
					return value + ', ' + rec.first_name + ' ' + rec.middle_name;
				}}
			]
		)
		,baseParams: {auth:_AUTH_KEY}
	})
	,om_genderStore: new Ext.data.SimpleStore({fields: ['initial', 'name'], data: [['M', 'Male'],['F', 'Female']]})	
	,om_civilStatusStore: new Ext.data.SimpleStore({fields: ['initial', 'name'], data: [['1', 'Single'],['2', 'Married'],['3', 'Separated'],['4', 'Widowed']]})
};
var om_function={
	membershipLoanWin: function(){
		return new Ext.Window({
			id: 'membershipLoanWin'
			,title: 'Loan Information'
			,frame: true
			,layout: 'form'
			,width: 750
			,plain: true
			,modal: true
			,resizable: false
			,closable: true
			,defaults: {autoScroll: true}
			,constrainHeader: true
			,bodyStyle:{"padding":"5px"}
			,loadMask: true	
			,items:[ membershipLoanDetails() ]
		});
	}
	,loadValues: function(employee_id_parameter){
		//loading original values
		Ext.Ajax.request({
			url: '/online_member/showOrigValues/'
			,params: {'employee_id': Ext.getCmp('ol_membershipDetail').getForm().findField('online_membership[employee_id]').getValue()
						,auth:_AUTH_KEY}  
			,success: function(response, opts) {
				 showOrigRet = Ext.decode(response.responseText).data[0];
				 companyCode = showOrigRet.company_code;
				 pecaDataStores.companyStoreOLM.addListener('load', function(){
					var companyIndex = pecaDataStores.companyStoreOLM.findExact('company_code', companyCode);
					var companyName = pecaDataStores.companyStoreOLM.getAt(companyIndex).get('company_name');
					Ext.getCmp('ol_membershipDetail').getForm().findField('om_company_code').setValue(pecaReaders.origValue+companyName);
				 });
			}
		});
		for(var i=0; i<3; i++){
			var foo = om_store.om_beneficiaryStorage.getAt(i);
			var j=i+1;
			if(foo!=null){
				//Ext.getCmp('ol_membershipDetail').getForm().findField('om_beneficiary_name'+j).setValue(pecaReaders.origValue +foo.get('beneficiary'));
				//Ext.getCmp('ol_membershipDetail').getForm().findField('om_beneficiary_relationship'+j).setValue(pecaReaders.origValue +foo.get('relationship'));
				//Ext.getCmp('ol_membershipDetail').getForm().findField('om_beneficiary_address'+j).setValue(pecaReaders.origValue +foo.get('beneficiary_address'));
				Ext.getCmp('ol_membershipDetail').getForm().findField('online_membership[beneficiary_name'+j+']').setValue(foo.get('beneficiary'));
				Ext.getCmp('ol_membershipDetail').getForm().findField('online_membership[beneficiary_relationship'+j+']').setValue(foo.get('relationship'));
				Ext.getCmp('ol_membershipDetail').getForm().findField('online_membership[beneficiary_address'+j+']').setValue(foo.get('beneficiary_address'));
			}
			//else{
				//Ext.getCmp('ol_membershipDetail').getForm().findField('om_beneficiary_name'+j).setValue(pecaReaders.origValue);
				//Ext.getCmp('ol_membershipDetail').getForm().findField('om_beneficiary_relationship'+j).setValue(pecaReaders.origValue);
				//Ext.getCmp('ol_membershipDetail').getForm().findField('om_beneficiary_address'+j).setValue(pecaReaders.origValue);
			//}
		}
		pecaDataStores.ol_memberLoanInfoStore.load({params: { employee_id :Ext.getCmp('ol_membershipDetail').getForm().findField('online_membership[employee_id]').getValue(),start:0, limit:MAX_PAGE_SIZE}});
		pecaDataStores.ol_guaranteedLoansStore.load({params: { employee_id :Ext.getCmp('ol_membershipDetail').getForm().findField('online_membership[employee_id]').getValue(),start:0, limit:MAX_PAGE_SIZE}});
		pecaDataStores.ol_transactionHistoryStore.load({params: { employee_id :Ext.getCmp('ol_membershipDetail').getForm().findField('online_membership[employee_id]').getValue(),start:0, limit:MAX_PAGE_SIZE}});
		pecaDataStores.companyStoreOLM.load();
		
		if(employee_id_parameter){
			var emp_id = employee_id_parameter;
		}
		else{
			var emp_id = _EMP_ID;
		}
		
		Ext.Ajax.request({
			url: '/membership/showBalanceInfo/'
			,params: {'member[employee_id]': emp_id, auth:_AUTH_KEY}  
			,success: function(response, opts) {
				var ret = Ext.decode(response.responseText);
				Ext.getCmp('membership[capital_contribution]').setValue(Ext.util.Format.number(Ext.num(ret.data.capconBal,0),'0,000,000,000.00'));
				Ext.getCmp('membership[required_balance]').setValue(Ext.util.Format.number(Ext.num(ret.data.reqBal,0),'0,000,000,000.00'));
				Ext.getCmp('membership[maximum_withdrawable_amount]').setValue(Ext.util.Format.number(Ext.num(ret.data.maxWdwlAmount,0),'0,000,000,000.00'));
				//jdj 08042017
				Ext.getCmp('member[capcon111]').setValue(Ext.util.Format.number(Ext.num(ret.data.capcon11,0),'0,000,000,000.00'));
			}
			,failure: function(response, opts) {}
		});
	}
	,clearHighlights: function(){
		if(tempStorageOfFields.length!=0){
			for(var i=0; i<tempStorageOfFields.length; i++){
				Ext.getCmp('ol_membershipDetail').getForm().findField(tempStorageOfFields[i]).removeClass('highlightField');
				var a=Ext.getCmp('ol_membershipDetail').getForm().findField(tempStorageOfFields[i]).getItemId();
				Ext.getCmp('ol_membershipDetail').getForm().findField(a.slice(0,9)+(new Number(a.slice(9))+1)).hide();
			}
		}
		if(tempStorageOfBFields.length!=0){
			for(var i=0; i<tempStorageOfBFields.length; i++){
				Ext.getCmp('ol_membershipDetail').getForm().findField(tempStorageOfBFields[i]).removeClass('highlightField');
			}
		}
	}
	,changeIfExists: function(array,field,value){
		var ret=-1;
		var a=Ext.getCmp('ol_membershipDetail').getForm().findField(field).getItemId();
		var b=Ext.getCmp('ol_membershipDetail').getForm().findField(a.slice(0,9)+(new Number(a.slice(9))+1)).getValue().slice(4);
		for(var i=0;i<array.length;i++){
			if(array[i][0]==field){
				array[i][1]=value;
				ret=-2;
				if(b==value) ret=i;
				
			}
		}
		return ret;
	}
	,populateData: function(field,value){
		if(om_function.changeIfExists(temp,field,value)==-1)
			temp.push([field,value]);
		else if(om_function.changeIfExists(temp,field,value)>=0)
			temp.splice(om_function.changeIfExists(temp,field,value),1);
	}
};

var tempStorageOfFields=[];
var temp=[];
var tempStorageOfBFields=[];

var ol_membershipDetail = function(){
	return {
		xtype:'form'
		,id:'ol_membershipDetail'
		,region:'center'
		,title: 'Details'
		,anchor: '100%'
		,frame: true
		,reader: pecaReaders.ol_membershipReader
		,items: [{			
			layout: 'form'
			,id:'ol_membershipDetail_'
			,region:'center'
			,style: 'padding-left:10px;padding-top:10px;'
			,buttons:[
			{
				text: 'Print SOA'
				,iconCls: 'icon_ext_preview'
				,handler: function(){
				
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

					var frm = Ext.getCmp('ol_membershipDetail').getForm();
					Ext.Ajax.request({
		    			url: '/report_capconstatementofacct/memberSOA' 
						,method: 'POST'
						,form: Ext.get('frmDownload')
						,params: {'file_type': '2'
									,'employee_id': frm.findField('online_membership[employee_id]').getValue()
									,'report_type': '1'
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
							if (opts.result.error_code == 19){
								showExtInfoMsg(opts.result.msg);
							}
						}
		        	});

				}
			},{
				text: 'Delete'
				,iconCls: 'icon_ext_del'
				,handler: function(){
					var frm = Ext.getCmp('ol_membershipDetail').getForm();
					frm.onDelete();
				}
			},{
				text:'Save'
				,iconCls: 'icon_ext_save'
				,handler: function(){
					var frm = Ext.getCmp('ol_membershipDetail').getForm();
					if(!frm.findField('online_membership[hire_date]').isValid(false)
						|| !frm.findField('online_membership[email_address]').isValid(false)){
							Ext.getCmp('ol_membershipDetail').findById("membershipTab").setActiveTab(1);
					} else 
					if(!frm.findField('membership[gender]').isValid(false)
						|| !frm.findField('membership[civil_status]').isValid(false)){
							Ext.getCmp('ol_membershipDetail').findById("membershipTab").setActiveTab(5);
					
					}
					
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
					var frm = Ext.getCmp('ol_membershipDetail').getForm();
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
					Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOLMemInfo');
					pecaDataStores.ol_membershipStore.reload();
					Ext.getCmp('ol_membershipDetail').getForm().reset();
					om_function.clearHighlights();
					temp = [];
					tempStorageOfFields=[];
					tempStorageOfBFields=[];
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
					,labelAlign: 'left'
					,items: [{
						layout: 'form'
						,columnWidth: .25
						//,width:250
						,border: false
						,items: [{
							xtype: 'textfield'
							,name: 'online_membership[employee_id]'
							,fieldLabel: 'Employee'
							,allowBlank: false
							,style: 'text-align: right'
							,anchor: '100%'
							,required: true
							,emptyText: 'ID'
							,autoCreate: {tag: 'input', type: 'text', maxlength: '8'}
							,readOnly: true
							,cls: 'x-item-disabled'
							}]
						},{
						layout: 'form'
						,labelWidth: 1
						,columnWidth: .175
						,border: false
						,items: [{
							xtype: 'textfield'
							,name: 'online_membership[last_name]'	
							//,width: 150
							,anchor: '100%'
							,allowBlank: false
							,fieldLabel: ' '
							,labelSeparator: ' '
							,emptyText: 'Last Name'
							,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
							,submitValue: false
							,listeners:{
									'change':{
										fn:function(field,newVal,oldVal) {
											om_function.populateData(this.name,newVal);
										}
									}
								}
							},{
								xtype: 'textfield'
								,cls: 'highlightText'
								,hidden:true
								//,width: 150
								,name: 'om_last_name'
								,border: false
								,readOnly: true
								,submitValue: false
								,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
							}]
						},{
						layout: 'form'
						,labelWidth: 1
						,columnWidth: .175
						,border: false
						,items: [{
							xtype: 'textfield'
							,name: 'online_membership[first_name]'	
							//,width: 150
							,allowBlank: false
							,anchor: '100%'
							,fieldLabel: ' '
							,labelSeparator: ' '
							,emptyText: 'First Name'
							,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
							,submitValue: false
							,listeners:{
									'change':{
										fn:function(field,newVal,oldVal) {
											om_function.populateData(this.name,newVal);
										}
									}
								}
							},{
								xtype: 'textfield'
								//,width: 150
								,name: 'om_first_name'
								,cls: 'highlightText'
								,hidden:true
								,border: false
								,readOnly: true
								,submitValue: false
								,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
							}]
						},{
						layout: 'form'
						,labelWidth: 1
						,columnWidth: .175
						,border: false
						,items: [{
							xtype: 'textfield'
							,name: 'online_membership[middle_name]'	
							//,width: 150
							,anchor: '100%'
							,fieldLabel: ' '
							,labelSeparator: ' '
							,submitValue: false
							,emptyText: 'Middle Name'
							,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
							,submitValue: false
							,listeners:{
									'change':{
										fn:function(field,newVal,oldVal) {
											om_function.populateData(this.name,newVal);
										}
									}
								}
							},{
								xtype: 'textfield'
								//,width: 150
								,name: 'om_middle_name'
								,cls: 'highlightText'
								,hidden:true
								,border: false
								,readOnly: true
								,submitValue: false
								,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
							}
							,{//hidden field
								xtype: 'numberfield'
								,anchor: '95%'
								,name: 'om_request_no'
								,hidden:true
								,submitValue:false
							}
							,{//hidden field
								xtype: 'numberfield'
								,anchor: '95%'
								,name: 'om_approver_name'
								,hidden:true
								,submitValue:false
							}
							,{//hidden field
								xtype: 'numberfield'
								,anchor: '95%'
								,name: 'om_status'
								,hidden:true
								,submitValue:false
							}]
						}]
				}
				,{html: '<br />'}
				,{
					layout: 'fit'
					,defaultType: 'grid'
					,items: [membershipTab()]
				}
				,{html: '<br />'}
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
							,name: 'online_membership[member_remarks]'
							,maxLength: 50
							,autoScroll: true
							,anchor: '90%'
						}
						,{
							xtype: 'textarea'
							,fieldLabel: 'PECA Remarks'
							,height: 35
							,name: 'online_membership[peca_remarks]'
							,maxLength: 50
							,autoScroll: true
							,anchor: '90%'
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
							,fieldLabel: 'Approvers'
							,height: 70
							,name: 'online_membership[approvers]'
							,autoScroll: true
							,anchor: '90%'
							,submitValue:false
							,readOnly:true
							,cls: 'x-item-disabled'
						}
					]
				}				
			]

		},
		{
			layout: 'form'
			,border: false
			,hideBorders: false
			,id: 'ol_membership_detail'
			,buttons:[
			{text: 'Approve'
				,iconCls: 'icon_ext_approve'
				,hidden: true
				,handler: function(){
					var frm = Ext.getCmp('ol_membershipDetail').getForm();
					frm.onApprove(frm);
				}
			},{
				text: 'Disapprove'
				,iconCls: 'icon_ext_disapprove'
				,hidden: true
				,handler: function(){
					Ext.Msg.prompt('Reason', 'Please enter the reason for disapproval:', function(btn, text){
						buttons: Ext.Msg.OKCANCEL;
						if (btn == 'ok' && text.length > 0){
								var frm = Ext.getCmp('ol_membershipDetail').getForm();
								frm.findField('online_membership[peca_remarks]').setValue(text);
								frm.onDisapprove(frm);
						}
						else if(btn == 'ok' && text.length == 0){
							var element = Ext.getCmp('online_membership_disapprove');
							element.handler.call(element.scope);
						}
					},this,50,Ext.getCmp('ol_membershipDetail').getForm().findField('online_membership[peca_remarks]').getValue());

				}
			}]
		},{
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
	    	return (Ext.getCmp('ol_membershipDetail').getForm().findField('frm_mode').getValue() == FORM_MODE_NEW);
	    }
	    ,setModeNew: function() {
	    	Ext.getCmp('ol_membershipDetail').getForm().findField('frm_mode').setValue(FORM_MODE_NEW);
	    	Ext.getCmp('ol_membershipDetail_').buttons[0].setVisible(false);  //preview button
	    	Ext.getCmp('ol_membershipDetail_').buttons[1].setVisible(false);  //delete button
	    	Ext.getCmp('ol_membershipDetail_').buttons[2].setVisible(true);  //save button
			Ext.getCmp('ol_membershipDetail_').buttons[3].setVisible(true);  //send button
	    	Ext.getCmp('ol_membershipDetail_').buttons[4].setVisible(true);  //cancel button
	    	Ext.getCmp('ol_membership_detail').buttons[0].setVisible(false);  //approve button
			Ext.getCmp('ol_membership_detail').buttons[1].setVisible(false);  //disapprove button
			
			Ext.getCmp('ol_membershipDetail').getForm().findField('online_membership[approvers]').hide();
			Ext.getCmp('ol_membershipDetail').getForm().findField('online_membership[peca_remarks]').hide();
			om_clearRestrictions(Ext.getCmp('ol_membershipDetail').getForm());
		}
		,setModeUpdate: function(requestNo,stat) {
			Ext.getCmp('ol_membershipDetail').getForm().findField('frm_mode').setValue(FORM_MODE_UPDATE);
			Ext.getCmp('ol_membershipDetail_').buttons[0].setVisible(true);  //preview button
	    	Ext.getCmp('ol_membershipDetail_').buttons[4].setVisible(true);  //cancel button
			
			Ext.getCmp('ol_membershipDetail').getForm().findField('online_membership[approvers]').show();
			Ext.Ajax.request({
				url: '/online_member/readRequest' 
				,method: 'POST'
				,params: {'employee_id': Ext.getCmp('ol_membershipDetail').getForm().findField('online_membership[employee_id]').getValue()
							,'request_no': requestNo
							,auth:_AUTH_KEY} 
				,success: function(response, opts) {
					
					var ret = Ext.decode(response.responseText);
					if(ret.total!=0){
						//if(ret.data.status_flag!=9){
							for(var x=0; x<ret.total; x++){
								Ext.getCmp('ol_membershipDetail').getForm().findField(ret.data[x].fieldname).setValue(ret.data[x].value);
								Ext.getCmp('ol_membershipDetail').getForm().findField(ret.data[x].fieldname).addClass('highlightField');							
								var a=Ext.getCmp('ol_membershipDetail').getForm().findField(ret.data[x].fieldname).getItemId();
								
								var hold=ret.data[x].fieldname.slice(18,(ret.data[x].fieldname.toString().length-1));
								if(hold.slice(0,hold.length-1)=="beneficiary_name" || hold.slice(0,hold.length-1)=="beneficiary_relationship" || hold.slice(0,hold.length-1)=="beneficiary_address"){
									var num=new Number(hold.charAt(hold.length-1));
									var xxx=om_store.om_beneficiaryStorage.getAt(num-1);
								//	showExtInfoMsg(om_store.om_beneficiaryStorage.getAt(num-1).get('beneficiary'));
									if(xxx!=null && (ret.data.status_flag!=9)){
										if(hold.slice(0,hold.length-1)=="beneficiary_name"){
											Ext.getCmp('ol_membershipDetail').getForm().findField('om_'+hold).setValue(pecaReaders.origValue +xxx.get('beneficiary'));
										}
										else if(hold.slice(0,hold.length-1)=="beneficiary_relationship"){
											Ext.getCmp('ol_membershipDetail').getForm().findField('om_'+hold).setValue(pecaReaders.origValue + xxx.get('description'));
										}
										else if(hold.slice(0,hold.length-1)=="beneficiary_address"){
											Ext.getCmp('ol_membershipDetail').getForm().findField('om_'+hold).setValue(pecaReaders.origValue + xxx.get('beneficiary_address'));
										}
									}
									else{
										if(ret.data.status_flag!=9){
											if(hold.slice(0,hold.length-1)=="beneficiary_name"){
												Ext.getCmp('ol_membershipDetail').getForm().findField('om_'+hold).setValue(pecaReaders.origValue);
											}
											else if(hold.slice(0,hold.length-1)=="beneficiary_relationship"){
												Ext.getCmp('ol_membershipDetail').getForm().findField('om_'+hold).setValue(pecaReaders.origValue);
											}
											else if(hold.slice(0,hold.length-1)=="beneficiary_address"){
												Ext.getCmp('ol_membershipDetail').getForm().findField('om_'+hold).setValue(pecaReaders.origValue);
											}
										}
									}
									tempStorageOfBFields.push(ret.data[x].fieldname);
								}
								else{
									if(ret.data.status_flag!=9)
										Ext.getCmp('ol_membershipDetail').getForm().findField(a.slice(0,9)+(new Number(a.slice(9))+1)).show();
									tempStorageOfFields.push(ret.data[x].fieldname);
								}
								temp.push([ret.data[x].fieldname,ret.data[x].value]);
							}
						//}
						Ext.getCmp('ol_membershipDetail').getForm().findField('om_status').setValue(ret.data.status_flag);
						Ext.getCmp('ol_membershipDetail').getForm().findField('om_request_no').setValue(ret.data.request_no);
						Ext.getCmp('ol_membershipDetail').getForm().findField('online_membership[member_remarks]').setValue(ret.data.member_remarks);
						Ext.getCmp('ol_membershipDetail').getForm().findField('online_membership[peca_remarks]').setValue(ret.data.peca_remarks);
						var app="";
						/*if(ret.data.status_flag=='4')
							app=ret.data.approver1;
						else if(ret.data.status_flag=='5')
							app=ret.data.approver1+'\n'+ret.data.approver2;
						else if(ret.data.status_flag=='6')
							app=ret.data.approver1+'\n'+ret.data.approver2+'\n'+ret.data.approver3;
						else if(ret.data.status_flag=='7')
							app=ret.data.approver1+'\n'+ret.data.approver2+'\n'+ret.data.approver3+'\n'+ret.data.approver4;
						else if(ret.data.status_flag=='9')*/
						if(ret.data.approver1!="")
							app = ret.data.approver1;
						if(ret.data.approver2!="")
							app = app+'\n'+ret.data.approver2;
						if(ret.data.approver3!="")
							app = app+'\n'+ret.data.approver3;
						if(ret.data.approver4!="")
							app = app+'\n'+ret.data.approver4;
						if(ret.data.approver5!="")
							app = app+'\n'+ret.data.approver5;
						Ext.getCmp('ol_membershipDetail').getForm().findField('online_membership[approvers]').setValue(app);
					}
				}
				,failure: function(response, opts) {}
			});
			if( _IS_ADMIN == false ){
				Ext.getCmp('ol_membership_detail').buttons[0].setVisible(false);  //approve button
				Ext.getCmp('ol_membership_detail').buttons[1].setVisible(false);  //disapprove button
				Ext.getCmp('ol_membershipDetail_').buttons[1].setVisible(true);  //delete button
				Ext.getCmp('ol_membershipDetail_').buttons[2].setVisible(true);  //save button
				
				//if rejected show peca remarks
				if(stat==10){
					Ext.getCmp('ol_membershipDetail').getForm().findField('online_membership[peca_remarks]').setVisible(true);
					Ext.getCmp('ol_membershipDetail').getForm().findField('online_membership[peca_remarks]').setReadOnly(true);
					Ext.getCmp('ol_membershipDetail').getForm().findField('online_membership[peca_remarks]').addClass('x-item-disabled');
				}
				else{
					Ext.getCmp('ol_membershipDetail').getForm().findField('online_membership[peca_remarks]').setVisible(false);
				}
				
				if(stat=='Saved' || stat==2){
					om_clearRestrictions(Ext.getCmp('ol_membershipDetail').getForm());
					Ext.getCmp('ol_membershipDetail_').buttons[3].setVisible(true);  //send button	
					Ext.getCmp('ol_membershipDetail_').buttons[1].setVisible(true);  //delete button
					Ext.getCmp('ol_membershipDetail_').buttons[2].setVisible(true);  //save button
				}
				else{
					om_setRestrictions(Ext.getCmp('ol_membershipDetail').getForm());
					Ext.getCmp('ol_membershipDetail_').buttons[3].setVisible(false);  //send button	
					Ext.getCmp('ol_membershipDetail_').buttons[1].setVisible(false);  //delete button
					Ext.getCmp('ol_membershipDetail_').buttons[2].setVisible(false);  //save button		
					//Ext.getCmp('ol_membershipDetail').findById('membershipTab').addClass('x-item-disabled');
				}
			}
			else{
				Ext.getCmp('ol_membershipDetail_').buttons[1].setVisible(false);  //delete button
				Ext.getCmp('ol_membershipDetail_').buttons[2].setVisible(false);  //save button
				Ext.getCmp('ol_membershipDetail_').buttons[3].setVisible(false);  //send button
				
				Ext.getCmp('ol_membershipDetail').getForm().findField('online_membership[peca_remarks]').show();
				
				if(Ext.getCmp('ol_membershipDetail').getForm().findField('om_status').getValue()==10){
					Ext.getCmp('ol_membershipDetail').getForm().findField('online_membership[peca_remarks]').setReadOnly(true);
					Ext.getCmp('ol_membershipDetail').getForm().findField('online_membership[peca_remarks]').addClass('x-item-disabled');
				}
				else{
					Ext.getCmp('ol_membershipDetail').getForm().findField('online_membership[peca_remarks]').setReadOnly(false);
					Ext.getCmp('ol_membershipDetail').getForm().findField('online_membership[peca_remarks]').removeClass('x-item-disabled');
				}
				om_setRestrictions(Ext.getCmp('ol_membershipDetail').getForm());
			}
			
			
	    }
		,onSave: function(frm,value){
			//om_function.checkChanges(frm);			
			//if(om_store.myStore.getCount()!=0){
				/*var rec = '[';
				for(var i=0; i<om_store.myStore.getCount(); i++){
					var foo = om_store.myStore.getAt(i);
					if(rec != '[')
						rec += ',';
					rec+= '{"fieldname":"'+foo.get('name')+'","value":"'+foo.get('value')+'"}';
					tempStorageOfFields.push(foo.get('name'));
				}
				rec += ']';*/
				
				var rec = '[';
				for(var i=0; i<temp.length; i++){
					if(rec != '[')
						rec += ',';
					rec+= '{"fieldname":"'+temp[i][0]+'","value":"'+temp[i][1]+'"}';
				}
				rec += ']';
				
				frm.submit({
					url: '/online_member/add' 
					,method: 'POST'
					,params: {data: rec
							,'saveOrSendFlag': value
							,auth:_AUTH_KEY
							, 'employee_id': _USER_ID
							, 'created_by': _USER_ID}
					,waitMsg: 'Creating Request...'
				,success: function(form, action) {
					showExtInfoMsg(action.result.msg);		
					frm.setModeUpdate(action.result.request_no,value);
				}
				,failure: function(form, action) {
					if (action.result.error_code == 2){
						Ext.Msg.confirm('Confirm Action',action.result.msg,function(btn) {
							if(btn=='yes'){
								form.onUpdate(form);
							}
						});
						
						
					}
				else{
						showExtErrorMsg(action.result.msg);
					}
				}	
				});
				
			//}
		}
		,onUpdate: function(frm,value){
			//om_function.checkChanges(frm);			
			//if(om_store.myStore.getCount()!=0){
				/*var rec = '[';
				for(var i=0; i<om_store.myStore.getCount(); i++){
					var foo = om_store.myStore.getAt(i);
					if(rec != '[')
						rec += ',';
					rec+= '{"fieldname":"'+foo.get('name')+'","value":"'+foo.get('value')+'"}';
					tempStorageOfFields.push(foo.get('name'));
				}
				rec += ']';*/
				
				var rec = '[';
				for(var i=0; i<temp.length; i++){
					if(rec != '[')
						rec += ',';
					rec+= '{"fieldname":"'+temp[i][0]+'","value":"'+temp[i][1]+'"}';
				}
				rec += ']';
				
				frm.submit({
					url: '/online_member/update' 
					,method: 'POST'
					,params: {data: rec
							,'saveOrSendFlag': value
							,auth:_AUTH_KEY
							,'status':value
							,'request_no':Ext.getCmp('ol_membershipDetail').getForm().findField('om_request_no').getValue()
							, 'employee_id': _USER_ID
							, 'created_by': _USER_ID}
					,waitMsg: 'Updating Request...'
				,success: function(form, action) {
					showExtInfoMsg(action.result.msg);		
					frm.setModeUpdate(Ext.getCmp('ol_membershipDetail').getForm().findField('om_request_no').getValue(),value);
					
				}
				,failure: function(form, action) {
					if (action.result.error_code == 2){
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
				
			//}
			
		}
		,onDelete: function(){
			if(Ext.getCmp('ol_membershipDetail').getForm().findField('om_status').getValue()==2){
				Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
					if(btn=='yes') {
						Ext.getCmp('ol_membershipDetail').getForm().submit({
							url: '/online_member/delete' 
							,method: 'POST'
							,params: {'request_no': Ext.getCmp('ol_membershipDetail').getForm().findField('om_request_no').getValue()
									,auth:_AUTH_KEY
									,'online_membership[modified_by]': _USER_ID}
							,waitMsg: 'Deleting Request...'
							,clientValidation: false
							,success: function(form, action) {
								showExtInfoMsg(action.result.msg);
								Ext.getCmp('ol_membershipDetail').setModeNew();
								Ext.getCmp('ol_membershipDetail').getForm().reset();
								om_function.clearHighlights();
								temp=[];
								tempStorageOfFields=[];
								tempStorageOfBFields=[];
								Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOLMemInfo');
								//pecaDataStores.ol_membershipStore.reload();
								if (pecaDataStores.ol_membershipStore.getCount() % MAX_PAGE_SIZE == 1){
									var page = pecaDataStores.ol_membershipStore.getTotalCount() - MAX_PAGE_SIZE - 1;
									pecaDataStores.ol_membershipStore.load({params: {start:page<0?0:page, limit:MAX_PAGE_SIZE}});
								} else{
									pecaDataStores.ol_membershipStore.reload();
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
				url: '/online_member/approve' 
				,method: 'POST'
				,params: {'status':Ext.getCmp('ol_membershipDetail').getForm().findField('om_status').getValue()
						,'request_no':Ext.getCmp('ol_membershipDetail').getForm().findField('om_request_no').getValue()
						, 'employee_id': Ext.getCmp('ol_membershipDetail').getForm().findField('online_membership[employee_id]').getValue()
						, 'created_by': _USER_ID
						,auth:_AUTH_KEY}
				,waitMsg: 'Approving Request...'
				,success: function(frm, action) {
					showExtInfoMsg(action.result.msg);
					Ext.getCmp('ol_membershipDetail').getForm().reset();
					temp=[];
					tempStorageOfFields=[];
					tempStorageOfBFields=[];
					Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOLMemInfo');
					pecaDataStores.ol_membershipStore.reload();
				}
				,failure: function(frm, action) {
					showExtErrorMsg(action.result.msg);
				}	
			});
		}
		,onDisapprove: function(frm){
			frm.submit({
				url: '/online_member/disapprove' 
				,method: 'POST'
				,params: {'request_no':Ext.getCmp('ol_membershipDetail').getForm().findField('om_request_no').getValue()
						, 'employee_id': Ext.getCmp('ol_membershipDetail').getForm().findField('online_membership[employee_id]').getValue()
						, 'created_by': _USER_ID
						,auth:_AUTH_KEY}
				,waitMsg: 'Disapproving Request...'
				,success: function(frm, action) {
					showExtInfoMsg(action.result.msg);
					Ext.getCmp('ol_membershipDetail').getForm().reset();
					temp=[];
					tempStorageOfFields=[];
					tempStorageOfBFields=[];
					Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOLMemInfo');
					pecaDataStores.ol_membershipStore.reload();
				}
				,failure: function(frm, action) {
					showExtErrorMsg(action.result.msg);
				}	
			});
		}
	};
};

var membershipTab = function(){
	return {
		xtype: 'tabpanel'
		,id: 'membershipTab'
		,titlebar:false
		,activeTab:0
		,bodyStyle: 'background:transparent;'
		,height: 350
		,boxMinWidth: 500
		,anchor: '100%'
		,defaults: {autoScroll: true}
		,items:[
			om_memInfoTab()
			,om_employmentInfoTab()
			,om_loanInfoTab()
			,om_guaranteedLoansInfoTab()
			,om_bankInfoTab()
			,om_personalInfoTab()
			,om_transactionHistoryTab()
		]
	};
};

var om_memInfoTab = function(){
	return {
		layout:'form'
		,title:'Membership'
		,id:'om_memInfoTab'
		,bodyStyle: 'background:transparent; padding: 10px;'
		,items: [ {
				layout: 'column'
				// ,width: 600
				,border: false
				,labelAlign: 'left'
				,items:[{
					layout: 'form'
					,border: false
					,columnWidth: .5
					,items: [
						{
						xtype: 'datefield'
						//,columnWidth: .5
						// ,width: 183
						,fieldLabel: 'Membership Date'
						,name: 'online_membership[member_date]'
						,submitValue: false
						,validationEvent: 'change'
						,maxLength: 10
						,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
						,listeners:{
							'change':{
								fn:function(field,newVal,oldVal) {
									om_function.populateData(this.name,newVal==""?"":newVal.format('m/d/Y'));
								}
							}
						}
					},{
						xtype: 'textfield'
						//,columnWidth: .5
						// ,width: 183
						,name: 'om_member_date'
						,cls: 'highlightText'
						,hidden:true
						,border: false
						,readOnly: true
						,submitValue: false
						,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
					}]
				},{
					layout: 'form'
					,border: false
					,columnWidth: .5
					,items: [{
						xtype:'checkbox'
						//,columnWidth: .3
						//,width: 100
						,boxLabel: 'Co-Maker'
						,name: 'online_membership[guarantor]'
						,submitValue: false
						,hidden: true
						,listeners:{
							'change':{
								fn:function(field,newVal,oldVal) {
									om_function.populateData(this.name,newVal);
								}
							}
						}
					},{
						xtype: 'textfield'
						//,columnWidth: .3
						//,width: 100
						,name: 'om_guarantor'
						,cls: 'highlightText'
						,hidden:true
						,border: false
						,readOnly: true
						,submitValue: false
						,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
					}]
				}
				,{
					layout: 'form'
					,border: false
					,columnWidth: .5
					,items: [{
						xtype:'checkbox'
						//,columnWidth: .3
						//,width: 100
						,boxLabel: 'Non-member'
						,name: 'online_membership[non_member]'
						,submitValue: false
						,listeners:{
							'change':{
								fn:function(field,newVal,oldVal) {
									om_function.populateData(this.name,newVal);
								}
							}
						}
					},{
						xtype: 'textfield'
						//,columnWidth: .3
						//,width: 100
						,name: 'om_non_member'
						,cls: 'highlightText'
						,hidden:true
						,border: false
						,readOnly: true
						,submitValue: false
						,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
					}]
				}
				]
		},{
			layout: 'form'
			,border: false
			,items: [{
				xtype: 'combo'
				,hiddenName: 'online_membership[member_status]'
				,store: om_store.statusStore
				,fieldLabel: 'Status'
				// ,width: 183
				,mode: 'local'
				,displayField: 'name'
				,valueField: 'value'
				,editable: 'false'
				,emptyText: 'Please Select'
				,forceSelection: true
				,triggerAction: 'all'
				,selectOnFocus: true
				,editable: false
				,allowBlank: false
				,required: true
				,submitValue: false
				,listeners:{
					'change':{
						fn:function(field,newVal,oldVal) {
							om_function.populateData(this.hiddenName,newVal);
						}
					}
				}
			},{
				xtype: 'textfield'
				,columnWidth: .5
				// ,width: 200
				,name: 'om_member_status'
				,cls: 'highlightText'
				,hidden:true
				,border: false
				,readOnly: true
				,submitValue: false
				,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
			}]
		},{
			layout: 'form'
			,xtype: 'fieldset'
			,title: 'Beneficiaries'
			,border: true
			,bodyStyle:{'padding':'2px'}
			,anchor: '100%'
			,items:[{
				layout: 'column'
				,border: false
				,labelAlign: 'left'
				,items:[{
					layout: 'form'
					,border: false
					,labelWidth: 1
					,columnWidth: .3
					,bodyStyle:{'padding':'2px'}
					,items:[ {
						html: '<div align="center">Name</div>'
					},{
						xtype: 'textfield'
						,anchor: '100%'
						,name: 'online_membership[beneficiary_name1]'
						,submitValue: false
						,autoCreate: {tag: 'input', type: 'text', maxlength: '100'}
						,listeners:{
							'change':{
								fn:function(field,newVal,oldVal) {
									om_function.populateData(this.name,newVal);
								}
							}
						}
					},{
						xtype: 'textfield'
						,anchor: '100%'
						,name: 'om_beneficiary_name1'
						,cls: 'highlightText'
						//,hidden:true
						,border: false
						,readOnly: true
						,submitValue: false
						,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
					},{
						xtype: 'textfield'
						,anchor: '100%'
						,name: 'online_membership[beneficiary_name2]'
						,submitValue: false
						,autoCreate: {tag: 'input', type: 'text', maxlength: '100'}
						,listeners:{
							'change':{
								fn:function(field,newVal,oldVal) {
									om_function.populateData(this.name,newVal);
								}
							}
						}
					},{
						xtype: 'textfield'
						,anchor: '100%'
						,name: 'om_beneficiary_name2'
						,cls: 'highlightText'
						//,hidden:true
						,border: false
						,readOnly: true
						,submitValue: false
						,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
					},{
						xtype: 'textfield'
						,anchor: '100%'
						,name: 'online_membership[beneficiary_name3]'
						,submitValue: false
						,autoCreate: {tag: 'input', type: 'text', maxlength: '100'}
						,listeners:{
							'change':{
								fn:function(field,newVal,oldVal) {
									om_function.populateData(this.name,newVal);
								}
							}
						}
					},{
						xtype: 'textfield'
						,anchor: '100%'
						,name: 'om_beneficiary_name3'
						,cls: 'highlightText'
						//,hidden:true
						,border: false
						,readOnly: true
						,submitValue: false
						,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
					}]	
				},{
					layout: 'form'
					,border: false
					,labelWidth: 1
					,columnWidth: .2
					,bodyStyle:{'padding':'2px'}
					,items:[{
						html: '<div align="center">Relationship</div>'
					},{
						xtype: 'combo'
						,anchor: '100%'
						// ,width: 135
						,hiddenName: 'online_membership[beneficiary_relationship1]'
						,store: om_store.beneficiaryStore
						,mode: 'local'
						,displayField: 'name'
						,valueField: 'initial'
						,editable: 'false'
						,emptyText: 'Please Select'
						,forceSelection: true
						,triggerAction: 'all'
						,selectOnFocus: true
						,submitValue: false
						,editable: false
						,listeners:{
							'change':{
								fn:function(field,newVal,oldVal) {
									om_function.populateData(this.hiddenName,newVal);
								}
							}
						}
					},{
						xtype: 'textfield'
						,anchor: '100%'
						,name: 'om_beneficiary_relationship1'
						,cls: 'highlightText'
						//,hidden:true
						,border: false
						,readOnly: true
						,submitValue: false
						,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
					},{
						xtype: 'combo'
						,anchor: '100%'
						// ,width: 135
						,hiddenName: 'online_membership[beneficiary_relationship2]'
						,store: om_store.beneficiaryStore
						,mode: 'local'
						,displayField: 'name'
						,valueField: 'initial'
						,editable: 'false'
						,emptyText: 'Please Select'
						,forceSelection: true
						,triggerAction: 'all'
						,selectOnFocus: true
						,submitValue: false
						,editable: false
						,listeners:{
							'change':{
								fn:function(field,newVal,oldVal) {
									om_function.populateData(this.hiddenName,newVal);
								}
							}
						}
					},{
						xtype: 'textfield'
						,anchor: '100%'
						,name: 'om_beneficiary_relationship2'
						,cls: 'highlightText'
						//,hidden:true
						,border: false
						,readOnly: true
						,submitValue: false
						,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
					},{
						xtype: 'combo'
						,anchor: '100%'
						// ,width: 135
						,hiddenName: 'online_membership[beneficiary_relationship3]'
						,store: om_store.beneficiaryStore
						,mode: 'local'
						,displayField: 'name'
						,valueField: 'initial'
						,editable: 'false'
						,emptyText: 'Please Select'
						,forceSelection: true
						,triggerAction: 'all'
						,selectOnFocus: true
						,submitValue: false
						,editable: false
						,listeners:{
							'change':{
								fn:function(field,newVal,oldVal) {
									om_function.populateData(this.hiddenName,newVal);
								}
							}
						}
					},{
						xtype: 'textfield'
						,anchor: '100%'
						,name: 'om_beneficiary_relationship3'
						,cls: 'highlightText'
						//,hidden:true
						,border: false
						,readOnly: true
						,submitValue: false
						,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
					}]
				},{
					layout: 'form'
					,border: false
					,labelWidth: 1
					,columnWidth: .5
					,bodyStyle:{'padding':'2px'}
					,items:[{
						html: '<div align="center">Address</div>'
					},{
						xtype: 'textfield'
						,anchor: '100%'
						,name: 'online_membership[beneficiary_address1]'
						,submitValue: false
						,autoCreate: {tag: 'input', type: 'text', maxlength: '100'}
						,listeners:{
							'change':{
								fn:function(field,newVal,oldVal) {
									om_function.populateData(this.name,newVal);
								}
							}
						}
					},{
						xtype: 'textfield'
						,anchor: '100%'
						,name: 'om_beneficiary_address1'
						,cls: 'highlightText'
						//,hidden:true
						,border: false
						,readOnly: true
						,submitValue: false
						,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
					},{
						xtype: 'textfield'
						,anchor: '100%'
						,name: 'online_membership[beneficiary_address2]'
						,submitValue: false
						,autoCreate: {tag: 'input', type: 'text', maxlength: '100'}
						,listeners:{
							'change':{
								fn:function(field,newVal,oldVal) {
									om_function.populateData(this.name,newVal);
								}
							}
						}
					},{
						xtype: 'textfield'
						,anchor: '100%'
						,name: 'om_beneficiary_address2'
						,cls: 'highlightText'
						//,hidden:true
						,border: false
						,readOnly: true
						,submitValue: false
						,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
					},{
						xtype: 'textfield'
						,anchor: '100%'
						,name: 'online_membership[beneficiary_address3]'
						,submitValue: false
						,autoCreate: {tag: 'input', type: 'text', maxlength: '100'}
						,listeners:{
							'change':{
								fn:function(field,newVal,oldVal) {
									om_function.populateData(this.name,newVal);
								}
							}
						}
					},{
						xtype: 'textfield'
						,anchor: '100%'
						,name: 'om_beneficiary_address3'
						,cls: 'highlightText'
						//,hidden:true
						,border: false
						,readOnly: true
						,submitValue: false
						,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
					}]
				}]
	
		}]
			
		}]
		
	};
};

var om_employmentInfoTab = function(){
	return {
		layout:'form'
		,title:'Employment'
		,bodyStyle: 'background:transparent; padding: 10px;'
		,anchor: '95%'
		,items:[{
			layout: 'column'
			,border: false
			,labelAlign: 'left'
			,items: [{
				layout: 'form'
				,columnWidth: .5
				,labelWidth: 130
				,border: false
				,items:[{
					xtype: 'textfield'
					,fieldLabel: 'TIN'
					//,width: 300
					,anchor: '95%'
					,name: 'online_membership[TIN]'
					,autoCreate: {tag: 'input', type: 'text', maxlength: '20'}
					,submitValue: false
					,listeners:{
						'change':{
							fn:function(field,newVal,oldVal) {
								om_function.populateData(this.name,newVal);
							}
						}
					}
				},{
					xtype: 'textfield'
					,anchor: '95%'
					//,width: 300
					,name: 'om_TIN'
					,cls: 'highlightText'
					,hidden:true
					,border: false
					,readOnly: true
					,submitValue: false
					,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
				},{
					xtype: 'datefield'
					,fieldLabel: 'Date of Employment'
					,anchor: '95%'
					// ,width: 250
					,readOnly: true
					,name: 'online_membership[hire_date]'
					,required: true
					,allowBlank: false
					,submitValue: false
					,validationEvent: 'change'
					,maxLength: 10
					,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
					,listeners:{
						'change':{
							fn:function(field,newVal,oldVal) {
								om_function.populateData(this.name,newVal==""?"":newVal.format('m/d/Y'));
							}
						}
					}
				},{
					xtype: 'textfield'
					,anchor: '95%'
					//,width: 300
					,name: 'om_hire_date'
					,cls: 'highlightText'
					,hidden:true
					,border: false
					,readOnly: true
					,submitValue: false
					,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
				},{
					xtype: 'datefield'
					,fieldLabel: 'Separation Date'
					,anchor: '95%'
					,name: 'online_membership[work_date]'
					,submitValue: false
					,validationEvent: 'change'
					,maxLength: 10
					,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
					,listeners:{
						'change':{
							fn:function(field,newVal,oldVal) {
								om_function.populateData(this.name,newVal==""?"":newVal.format('m/d/Y'));
							}
						}
					}

				},{
					xtype: 'textfield'
					,anchor: '95%'
					//,width: 300
					,name: 'om_work_date'
					,cls: 'highlightText'
					,hidden:true
					,border: false
					,readOnly: true
					,submitValue: false
					,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
				},{
					xtype: 'combo'
					,anchor: '95%'
					,hiddenName: 'online_membership[company_code]'
					,store: pecaDataStores.companyStoreOLM
					,mode: 'local'
					,fieldLabel: 'Company'
					,displayField: 'company_name'
					,valueField: 'company_code'
					,editable: 'false'
					,emptyText: 'Please Select'
					,forceSelection: true
					,triggerAction: 'all'
					,selectOnFocus: true
					,editable: false
					,submitValue: false
					,listeners:{
						'change':{
							fn:function(field,newVal,oldVal) {
								om_function.populateData(this.hiddenName,newVal);
							}
						}
					}
				},{
					xtype: 'textfield'
					,anchor: '95%'
					//,width: 300
					,name: 'om_company_code'
					,cls: 'highlightText'
					,hidden:true
					,border: false
					,readOnly: true
					,submitValue: false
					,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
				}]
			},{
				layout: 'form'
				,xtype: 'fieldset'
				,columnWidth: .5
				,labelWidth: 130
				,border: false
				,items:[{
					xtype: 'textfield'
					,fieldLabel: 'Department'
					,anchor: '95%'
					,name: 'online_membership[department]'
					,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
					,submitValue: false
					,listeners:{
						'change':{
							fn:function(field,newVal,oldVal) {
								om_function.populateData(this.name,newVal);
							}
						}
					}
				},{
					xtype: 'textfield'
					,anchor: '95%'
					,name: 'om_department'
					,cls: 'highlightText'
					,hidden:true
					,border: false
					,readOnly: true
					,submitValue: false
					,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
				},{
					xtype: 'textfield'
					,fieldLabel: 'Position'
					,anchor: '95%'
					,name: 'online_membership[position]'
					,autoCreate: {tag: 'input', type: 'text', maxlength: '20'}
					,submitValue: false
					,listeners:{
						'change':{
							fn:function(field,newVal,oldVal) {
								om_function.populateData(this.name,newVal);
							}
						}
					}
				},{
					xtype: 'textfield'
					,anchor: '95%'
					,name: 'om_position'
					,cls: 'highlightText'
					,hidden:true
					,border: false
					,readOnly: true
					,submitValue: false
					,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
				},{
					xtype: 'textfield'
					,fieldLabel: 'Office Number'
					,anchor: '95%'
					,name: 'online_membership[office_no]'
					,autoCreate: {tag: 'input', type: 'text', maxlength: '15'}
					,submitValue: false
					,listeners:{
						'change':{
							fn:function(field,newVal,oldVal) {
								om_function.populateData(this.name,newVal);
							}
						}
					}

				},{
					xtype: 'textfield'
					,anchor: '95%'
					,name: 'om_office_no'
					,cls: 'highlightText'
					,hidden:true
					,border: false
					,readOnly: true
					,submitValue: false
					,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
				},{
					xtype: 'textfield'
					,fieldLabel: 'E-mail Address'
					,anchor: '95%'
					,required: true
					,allowBlank: false
					,vtype: 'email'
					,msgTarget: 'under'
					,name: 'online_membership[email_address]'
					,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
					,submitValue: false
					,listeners:{
						'change':{
							fn:function(field,newVal,oldVal) {
								om_function.populateData(this.name,newVal);
							}
						}
					}
				},{
					xtype: 'textfield'
					,anchor: '95%'
					,name: 'om_email_address'
					,cls: 'highlightText'
					,hidden:true
					,border: false
					,readOnly: true
					,submitValue: false
					,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
				}]
			}]
		}]
	};
};

var om_loanInfoTab = function(){
	return {
		xtype:'panel'
		,title:'Loan Information'
		,bodyStyle: 'background:transparent; padding: 10px;'
		,items: [{
        	layout: 'fit'
            ,defaultType: 'grid'
			,items: [membershipLoanInfoList()]
        }]
	};
};
var membershipLoanInfoList = function(){
	return {
		xtype: 'grid'
		,id: 'membershipLoanInfoList'
		,titlebar: false
		,store: pecaDataStores.ol_memberLoanInfoStore
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 270
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,plugins: [om_summaryLoanMember]
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
					om_function.membershipLoanWin().show();
					Ext.getCmp('membershipLoanDetails').getForm().load({
				    	url: '/membership/showLoanInfo'
				    	,params: {'loan_no':(rec.get('loan_no'))
									,auth:_AUTH_KEY}
				    	,method: 'POST'
				    	,waitMsgTarget: true
				    	,success: function(response, opts) {
							om_store.membershipLoanPaymentsStore.load({params: { loan_no :rec.get('loan_no'),start:0, limit:MAX_PAGE_SIZE}});
							om_store.membershipChargesStore.load({params: { loan_no :rec.get('loan_no'),start:0, limit:MAX_PAGE_SIZE}});
							om_store.membershipCoMakersStore.load({params: { loan_no :rec.get('loan_no'),start:0, limit:MAX_PAGE_SIZE}, callback: function(r, options, success) {
								/* if(om_store.membershipCoMakersStore.getTotalCount() == 0){
									Ext.getCmp('membership_noCoMaker').setValue(true);
								} */
							}});
						}
					});
					
				}
				
			}
		}
		,viewConfig: {
			forceFit:true
			,scrollOffset:13
		}
		,cm: om_column.membershipLoanInfoColumns
		,bbar: new Ext.PagingToolbar({
	        store: pecaDataStores.ol_memberLoanInfoStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};
var om_summaryLoanMember = new Ext.ux.grid.GridSummary();
var membershipLoanDetails = function(){
	return{
	xtype: 'form'
	,id: 'membershipLoanDetails'
	,frame: true
	,height: 480
	,anchor: '100%'
	,reader: pecaReaders.memberLoanInfoDetailReader
	,bodyStyle: 'background:transparent; padding: 10px;'
	,border: false
	,items:[{	
		layout: 'column'
		,anchor: '95%'
		,border: false
		,bodyStyle: 'background:transparent;'
		,items:[{
			layout: 'form'
			,columnWidth: 0.5
			,labelWidth: 200
			,bodyStyle: 'background:transparent;'
			,border: false
			,items:[{
					xtype: 'textfield'
					,border: false
					,submitValue: false
					,name: 'loan_no'
					,anchor: '95%'
					,id: 'membership_loan_info_loan_no'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Loan No.'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'loan_description'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Loan Type'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'loan_date'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Loan Date'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'principal'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Principal Amount'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'term'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Terms in Months'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'employee_interest_rate'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Emp. Interest Rate'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'employee_interest_total'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Emp. Interest Amount'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'initial_interest'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Emp. Initial Interest'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'employee_interest_amortization'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Emp. Amortized Interest'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'company_interest_rate'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Company Interest Rate'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'company_interest_total'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Company Interest Amount'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'company_interest_amort'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Company Amortized Interest'
				}
			]
		},{
			layout: 'form'
			,columnWidth: 0.5
			,labelWidth: 200
			,bodyStyle: 'background:transparent;'
			,border: false
			,items:[{
					xtype: 'checkbox'
					,readOnly: true
					,submitValue: false
					,name: 'restructure'
					,style: 'background:transparent; text-align: right; border:0'
					,anchor: '95%'
					,disabled: true
					,fieldLabel: 'Restructured'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'amortization_startdate'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Amortization Start Date'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'employee_principal_amortization'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Emp. Principal Amortization'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'loan_proceeds'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Loan Proceeds'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'mri_fip_amount'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'MRI/FIP'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'broker_fee_amount'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Broker\'s Fee'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'government_fee_amount'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Gov\'t Fees'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'other_fee_amount'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Other Fees'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'service_fee_amount'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Service Charge'
				},{
					xtype: 'textfield'
					,border: false
					,readOnly: true
					,submitValue: false
					,name: 'principal_balance'
					,anchor: '95%'
					,style: 'background:transparent; text-align: right; border:0'
					,fieldLabel: 'Loan Balance'
				}]
		}]
	}, membershipLoanDetailTab()]
	}
};
var membershipLoanDetailTab = function(){
	return {
		xtype: 'tabpanel'
		,titlebar:false
		,activeTab:0
		,anchor: '98%'
		,bodyStyle: 'background:transparent;'
		,height: 335
		,defaults: {autoScroll: true}
		,items:[
			membershipLoanPaymentsTab()
			,membershipChargesTab()
			,membershipCoMakersTab()
		]
	};
};
var membershipLoanPaymentsTab= function(){
	return {
		xtype:'panel'
		,title:'Loan Payments'
		,bodyStyle: 'background:transparent; padding: 5px;'
		,items: [membershipLoanPaymentsList()]
	};
};
var membershipLoanPaymentsList = function(){
	return {
		xtype: 'grid'
		,id: 'membershipLoanPaymentsList'
		,titlebar: false
		,store: om_store.membershipLoanPaymentsStore
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 270
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit:true
			,scrollOffset:13
		}
		,cm: om_column.membershipLoanPaymentsColumns
		,bbar: new Ext.PagingToolbar({
	        store: om_store.membershipLoanPaymentsStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};

var membershipChargesTab= function(){
	return {
		xtype:'panel'
		,title:'Charges'
		,bodyStyle: 'background:transparent; padding: 5px;'
		,items: [membershipChargesList()]
	};
};
var membershipChargesList = function(){
	return {
		xtype: 'grid'
		,id: 'membershipChargesList'
		,titlebar: false
		,store: om_store.membershipChargesStore
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 270
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit:true
			,scrollOffset:13
		}
		,cm: om_column.membershipChargesColumns
		,bbar: new Ext.PagingToolbar({
	        store: om_store.membershipChargesStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};

var membershipCoMakersTab= function(){
	return {
		xtype:'panel'
		,title:'Co-Makers'
		,bodyStyle: 'background:transparent; padding: 5px;'
		,items: [membershipCoMakersPanel()]
	};
};
var membershipCoMakersPanel = function(){
	return {
		border: false
		,layout: 'column'
		,bodyStyle: 'background:transparent;'
		,items:[membershipCoMakersList()
			,{
			columnWidth: 0.5
			,bodyStyle: 'background:transparent; padding: 10px;'
			,border: false
			,items:[{
				bodyStyle: 'background:transparent;'
				,border: false
				,html: '<br /><br />'
			},{
				xtype: 'checkbox'
				,boxLabel: 'No Co-Maker'
				,name: 'noCoMaker'
				,id: 'membership_noCoMaker'
				,disabled: true
			},{
				layout: 'column'
				,bodyStyle: 'background:transparent;'
				,border: false
				,items:[{
					columnWidth: 0.4
					,xtype: 'checkbox'
					,name: 'pension'
					,boxLabel: 'Pensioned'
					,disabled: true
				},{
					xtype: 'textfield'
					,style: 'text-align: right;'
					,name: 'pensionAmount'
					,readOnly: true
					,columnWidth: 0.5
				}]
			}]
		}]
	};
};
var membershipCoMakersList = function(){
	return {
		xtype: 'grid'
		,id: 'membershipCoMakersList'
		,titlebar: false
		,columnWidth: 0.5
		,store: om_store.membershipCoMakersStore
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 270
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit:true
			,scrollOffset:0
		}
		,cm: om_column.membershipCoMakersColumns
		,bbar: new Ext.PagingToolbar({
	        store: om_store.membershipCoMakersStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};

var om_guaranteedLoansInfoTab = function(){
	return {
		xtype:'panel'
		,title:'Co-Made Loans'
		,bodyStyle: 'background:transparent; padding: 10px;'
		,items: [{
        	layout: 'fit'
            ,defaultType: 'grid'
			,items: [om_guaranteedLoansList()]
        }]
	};
};

var om_bankInfoTab= function(){
	return {
		layout:'form'
		,title:'Bank Information'
		,bodyStyle: 'background:transparent; padding: 10px;'
		,items:[{
				xtype: 'textfield'
				,fieldLabel: 'Bank'
				,width: 200
				,name: 'membership[bank]'
				,disabled: true
				,autoCreate: {tag: 'input', type: 'text', maxlength: '1'}
				,listeners:{
					'change':{
						fn:function(field,newVal,oldVal) {
							om_function.populateData(this.name,newVal);
						}
					}
				}
			}
			,{
				xtype: 'textfield'
				,fieldLabel: 'Account Number'
				,width: 200
				,style: 'text-align: left'
				,disabled: true
				,name: 'membership[bank_account_no]'
				,autoCreate: {tag: 'input', type: 'text', maxlength: '15'}
				,listeners:{
					'change':{
						fn:function(field,newVal,oldVal) {
							om_function.populateData(this.name,newVal);
						}
					}
				}
			}
		]
	};
};

var om_guaranteedLoansList = function(){
	return {
		xtype: 'grid'
		,id: 'om_guaranteedLoansList'
		,titlebar: false
		,store: pecaDataStores.ol_guaranteedLoansStore
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 270
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		//,plugins: [om_summaryComakerMember]
		,viewConfig: {
			forceFit:true
			,scrollOffset:13
		}
		,cm: om_column.om_guaranteedLoansColumns
		,bbar: new Ext.PagingToolbar({
	        store: pecaDataStores.ol_guaranteedLoansStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};
//var om_summaryComakerMember = new Ext.ux.grid.GridSummary();

var om_personalInfoTab= function(){
	return {
		layout:'form'
		,title:'Personal'
		,bodyStyle: 'background:transparent; padding: 10px;'
		,items:[{
		layout: 'column'
		,border: false
		,labelAlign: 'left'
		,items: [{
			layout: 'form'
			,columnWidth: .5
			,labelWidth: 130
			,border: false
			,items:[{
				xtype: 'datefield'
				,fieldLabel: 'Date of Birth'
				,anchor: '95%'
				,name: 'membership[birth_date]'
				,submitValue: false
				,validationEvent: 'change'
				,maxLength: 10
				,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
				,listeners:{
					'change':{
						fn:function(field,newVal,oldVal) {
							om_function.populateData(this.name,newVal==""?"":newVal.format('m/d/Y'));
						}
					}
				}
			},{
				xtype: 'textfield'
				,anchor: '95%'
				,name: 'om_birth_date'
				,cls: 'highlightText'
				,hidden:true
				,border: false
				,readOnly: true
				,submitValue: false
				,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
			},{
				xtype: 'combo'
				,fieldLabel: 'Gender'
				,anchor: '95%'
				,hiddenName: 'membership[gender]'
				,store: om_store.om_genderStore
				,mode: 'local'
				,displayField: 'name'
				,valueField: 'initial'
				,editable: 'false'
				,emptyText: 'Please Select'
				,forceSelection: true
				,triggerAction: 'all'
				,selectOnFocus: true
				,editable: false
				,submitValue: false
				,required: true
				,listeners:{
					'change':{
						fn:function(field,newVal,oldVal) {
							om_function.populateData(this.hiddenName,newVal);
						}
					}
				}
			},{
				xtype: 'textfield'
				,anchor: '95%'
				,name: 'om_gender'
				,cls: 'highlightText'
				,hidden:true
				,border: false
				,readOnly: true
				,submitValue: false
				,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
			},{
				xtype: 'combo'
				,fieldLabel: 'Civil Status'
				,anchor: '95%'
				,hiddenName: 'membership[civil_status]'
				,store: om_store.om_civilStatusStore
				,mode: 'local'
				,displayField: 'name'
				,valueField: 'initial'
				,editable: 'false'
				,emptyText: 'Please Select'
				,forceSelection: true
				,triggerAction: 'all'
				,selectOnFocus: true
				,editable: false
				,submitValue: false
				,required: true
				,listeners:{
					'change':{
						fn:function(field,newVal,oldVal) {
							om_function.populateData(this.hiddenName,newVal);
						}
					}
				}

			},{
				xtype: 'textfield'
				,anchor: '95%'
				,name: 'om_civil_status'
				,cls: 'highlightText'
				,hidden:true
				,border: false
				,readOnly: true
				,submitValue: false
				,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
			},{
				xtype: 'textfield'
				,fieldLabel: 'Name of Spouse'
				,anchor: '95%'
				,name: 'membership[spouse]'
				,autoCreate: {tag: 'input', type: 'text', maxlength: '40'}
				,submitValue: false
				,listeners:{
					'change':{
						fn:function(field,newVal,oldVal) {
							om_function.populateData(this.name,newVal);
						}
					}
				}
			},{
				xtype: 'textfield'
				,anchor: '95%'
				,name: 'om_spouse'
				,cls: 'highlightText'
				,hidden:true
				,border: false
				,readOnly: true
				,submitValue: false
				,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
			}]
		},{
			layout: 'form'
			,xtype: 'fieldset'
			,columnWidth: .5
			,labelWidth: 130
			,border: false
			,items:[{
				xtype: 'textfield'
				,fieldLabel: 'Address'
				,anchor: '95%'
				,name: 'membership[address_1]'
				,autoCreate: {tag: 'input', type: 'text', maxlength: '35'}
				,submitValue: false
				,listeners:{
					'change':{
						fn:function(field,newVal,oldVal) {
							om_function.populateData(this.name,newVal);
						}
					}
				}
			},{
				xtype: 'textfield'
				,anchor: '95%'
				,name: 'om_address_1'
				,cls: 'highlightText'
				,hidden:true
				,border: false
				,readOnly: true
				,submitValue: false
				,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
			},{
				xtype: 'textfield'
				,fieldLabel: ' '
				,labelSeparator: ' '
				,anchor: '95%'
				,name: 'membership[address_2]'
				,autoCreate: {tag: 'input', type: 'text', maxlength: '35'}
				,submitValue: false
				,listeners:{
					'change':{
						fn:function(field,newVal,oldVal) {
							om_function.populateData(this.name,newVal);
						}
					}
				}
			},{
				xtype: 'textfield'
				,anchor: '95%'
				,name: 'om_address_2'
				,cls: 'highlightText'
				,hidden:true
				,border: false
				,readOnly: true
				,submitValue: false
				,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
			},{
				xtype: 'textfield'
				,fieldLabel: ' '
				,labelSeparator: ' '
				,anchor: '95%'
				,name: 'membership[address_3]'
				,autoCreate: {tag: 'input', type: 'text', maxlength: '35'}
				,submitValue: false
				,listeners:{
					'change':{
						fn:function(field,newVal,oldVal) {
							om_function.populateData(this.name,newVal);
						}
					}
				}

			},{
				xtype: 'textfield'
				,anchor: '95%'
				,name: 'om_address_3'
				,cls: 'highlightText'
				,hidden:true
				,border: false
				,readOnly: true
				,submitValue: false
				,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
			},{
				html: '<br />'
			},{
				xtype: 'textfield'
				,fieldLabel: 'Home Phone'
				,anchor: '95%'
				,name: 'membership[home_phone]'
				,autoCreate: {tag: 'input', type: 'text', maxlength: '15'}
				,submitValue: false
				,listeners:{
					'change':{
						fn:function(field,newVal,oldVal) {
							om_function.populateData(this.name,newVal);
						}
					}
				}
			},{
				xtype: 'textfield'
				,anchor: '95%'
				,name: 'om_home_phone'
				,cls: 'highlightText'
				,hidden:true
				,border: false
				,readOnly: true
				,submitValue: false
				,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
			},{
				xtype: 'textfield'
				,fieldLabel: 'Mobile Number'
				,anchor: '95%'
				,name: 'membership[mobile_no]'
				,autoCreate: {tag: 'input', type: 'text', maxlength: '15'}
				,submitValue: false
				,listeners:{
					'change':{
						fn:function(field,newVal,oldVal) {
							om_function.populateData(this.name,newVal);
						}
					}
				}
			},{
				xtype: 'textfield'
				,anchor: '95%'
				,name: 'om_mobile_no'
				,cls: 'highlightText'
				,hidden:true
				,border: false
				,readOnly: true
				,submitValue: false
				,style: 'background:transparent; text-align: left; border:0; font-style:italic;'
			}]
		}]
	}]
	};
};

var om_transactionHistoryTab = function(){
	return {
		layout:'form'
		,title:'Transaction History'
		,bodyStyle: 'background:transparent; padding: 10px;'
		,items:[{
			layout: 'column'
			,border: false
			,labelAlign: 'left'
			,items: [{
				columnWidth: .5
				,items: [om_transactionHistoryList()]
			},{
				layout: 'form'
				,columnWidth: .5
				,bodyStyle: 'padding: 50px;'
				,labelWidth: 200
				,labelAlign: 'left'
				,items: [{
					html: '<br /><br />'
				},{
					xtype: 'textfield'
					,border: false
					,value: '0.00'
					,readOnly: true
					,submitValue: false
					,width: 120
					,style: 'background:transparent; text-align: right; border:0'
					,id: 'membership[capital_contribution]'
					,fieldLabel: 'Capital Contribution'
				},{
					html: '<br />'
				},{
					xtype: 'textfield'
					,border: false
					,value: '0.00'
					,readOnly: true
					,submitValue: false
					,width: 120
					,style: 'background:transparent; text-align: right; border:0'
					,id: 'membership[required_balance]'
					//,fieldLabel: 'Required Balance'
					// jdj 08042017
					,fieldLabel: 'Non-Withdrawable Capital'
						
				},{
					html: '<br />'
				},{
					xtype: 'textfield'
					,border: false
					,value: '0.00'
					,readOnly: true
					,submitValue: false
					,width: 120
					,style: 'background:transparent; text-align: right; border:0'
					,id: 'membership[maximum_withdrawable_amount]'
					,fieldLabel: 'Maximum Withdrawable Amount'
				}]
			}]
			
		}]
	};
};
var om_transactionHistoryList = function(){
	return {
		xtype: 'grid'
		,id: 'om_transactionHistoryList'
		,titlebar: false
		,store: pecaDataStores.ol_transactionHistoryStore
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 270
		,loadMask: true
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit:true
			,scrollOffset:13
		}
		,cm: om_column.om_transactionHistoryColumns
		,bbar: new Ext.PagingToolbar({
	        store: pecaDataStores.ol_transactionHistoryStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};

var ol_membershipFilter = function(){
	return {
		xtype:'form'
		,id:'ol_membershipFilter'
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
						if(Ext.getCmp('ol_membershipFilter').findById('om_from').getValue()>Ext.getCmp('ol_membershipFilter').findById('om_to').getValue() && Ext.getCmp('ol_membershipFilter').findById('om_from').getValue()!="" && Ext.getCmp('ol_membershipFilter').findById('om_to').getValue()!="")
							showExtErrorMsg("Submission Date From cannot be greater than Submission Date To.");
						else
							pecaDataStores.ol_membershipStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
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
									,id: 'om_from'
									,maxLength: 10
									,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
									,style: 'text-align: right'
									,validationEvent: 'change'
									,emptyText: 'From'
									,listeners: {
										specialkey: function(frm,evt){
											if (evt.getKey() == evt.ENTER) {
												pecaDataStores.ol_membershipStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});					
											}
										}
										,'invalid':{
											scope:this
											,fn:function(field,msg){
												Ext.getCmp('ol_membershipFilter').doLayout();
											}
										}
										,'valid':{
											scope:this
											,fn:function(field){
												Ext.getCmp('ol_membershipFilter').doLayout();
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
									,id: 'om_to'
									,maxLength: 10
									,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
									,style: 'text-align: right'
									,validationEvent: 'change'
									,emptyText: 'To'
									,listeners: {
										specialkey: function(frm,evt){
											if (evt.getKey() == evt.ENTER) {
												pecaDataStores.ol_membershipStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});				
											}
										}
										,'invalid':{
											scope:this
											,fn:function(field,msg){
												Ext.getCmp('ol_membershipFilter').doLayout();
											}
										}
										,'valid':{
											scope:this
											,fn:function(field){
												Ext.getCmp('ol_membershipFilter').doLayout();
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
							,fieldLabel: 'Status'
							,anchor: '95%'
							,id: 'om_stat'
							,hiddenName: 'online_membership[status]'
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
										pecaDataStores.ol_membershipStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});					
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
									,name: 'om_employee_id'
									,id: 'om_employee_id'
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
												pecaDataStores.ol_membershipStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});			
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
									,id: 'om_last'
									,maxLength: 30
									,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
									,listeners: {
										specialkey: function(frm,evt){
											if (evt.getKey() == evt.ENTER) {
												pecaDataStores.ol_membershipStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});					
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
									,id: 'om_first'
									,maxLength: 30
									,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
									,listeners: {
										specialkey: function(frm,evt){
											if (evt.getKey() == evt.ENTER) {
												pecaDataStores.ol_membershipStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});					
											}
										}
									}
								}
							]
						}
					]
				}				
			]
		},ol_membershipList()]
		,listeners:{
			'render':{
				scope:this
				,fn:function(grid){
					if(_IS_ADMIN==false){
						Ext.getCmp('ol_membershipFilter').findById('om_employee_id').setValue(_EMP_ID);
						Ext.getCmp('ol_membershipFilter').findById('om_employee_id').setVisible(false);
						Ext.getCmp('ol_membershipFilter').findById('om_last').setVisible(false);
						Ext.getCmp('ol_membershipFilter').findById('om_first').setVisible(false);
					}
				//	else Ext.getCmp('ol_membershipDetail').getForm().applyToFields({readOnly:true});
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
					url: '/report_onlinemembershiplist'
					,method: 'POST'
					,form: Ext.get('frmDownload')
					,params: {
							'request_date_from': frm_.findField('om_from').getValue()
							,'request_date_to': frm_.findField('om_to').getValue()
							,'status': frm_.findField('om_stat').getValue()
							,'employee_id': frm_.findField('om_employee_id').getValue()
							,'first_name': frm_.findField('om_first').getValue()
							,'last_name': frm_.findField('om_last').getValue()
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

var ol_membershipList = function(){
	return {
		xtype: 'grid'
		,id: 'ol_membershipList'
		,titlebar: false
		,store: pecaDataStores.ol_membershipStore
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
		,cm: om_column.ol_membershipColumns
		
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					
					var rec = grid.getStore().getAt(row);
					myMask.show();
					om_store.om_beneficiaryStorage.load({params: { 'member[employee_id]':(rec.get('online_membership[employee_id]')),start:0, limit:MAX_PAGE_SIZE}});
					pecaDataStores.companyStoreOLM.load();
					Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOLMemInfoDetail');
					Ext.getCmp('ol_membershipDetail').getForm().findField('om_request_no').setValue(rec.get('online_membership[request_no]'));
					Ext.getCmp('ol_membershipDetail').getForm().findField('om_status').setValue(rec.get('online_membership[status]'));
					Ext.getCmp('ol_membershipDetail').getForm().findField('om_approver_name').setValue(rec.get('online_membership[approver_name]'));
					Ext.getCmp('ol_membershipDetail').getForm().load({
				    	url: '/membership/show'
				    	,params: {'member[employee_id]':(rec.get('online_membership[employee_id]')), auth:_AUTH_KEY}
				    	,method: 'POST'
				    	,waitMsgTarget: true
						,success: function(form, action) {
						
							var request_no = rec.get('online_membership[request_no]');
							var status = rec.get('status');
							var approver_name = rec.get('online_membership[approver_name]');
							Ext.getCmp('ol_membershipDetail').getForm().setModeUpdate(request_no,status);
							if(approver_name==_USER_ID && status >= '3' && status <= '7'){
								Ext.getCmp('ol_membership_detail').buttons[0].setVisible(true);  //approve button
								Ext.getCmp('ol_membership_detail').buttons[1].setVisible(true);  //disapprove button
							}
							else{
								Ext.getCmp('ol_membership_detail').buttons[0].setVisible(false);  //approve button
								Ext.getCmp('ol_membership_detail').buttons[1].setVisible(false);  //disapprove button
							}
							
							om_function.loadValues(rec.get('online_membership[employee_id]'));
							myMask.hide();
						}
					});
					
				}
			}
			,'render':{
				scope:this
				,fn:function(grid){
					Ext.getCmp('om_to').setValue(_TODAY);
					Ext.getCmp('om_from').setValue(new Date(new Date(Ext.getCmp('om_to').getValue())-1));
					pecaDataStores.ol_membershipStore.load({params: {start:0, limit:MAX_PAGE_SIZE}});
					if(_IS_ADMIN==true){
						Ext.getCmp('om_add_btn').setVisible(false);
						Ext.getCmp('om_btn_separator1').setVisible(false);
						Ext.getCmp('om_del_btn').setVisible(false);
						Ext.getCmp('om_btn_separator2').setVisible(false);
						//Ext.getCmp('ol_membershipDetail').getForm().applyToFields({readOnly: true});
						grid.getColumnModel().setHidden(1, false); 
					}
					else
						grid.getColumnModel().setHidden(1, true); 
				}
			}
		}
		
		,tbar:[
		{
			text:'New'
			,id: 'om_add_btn'
			,tooltip:'Add a Membership Request'
			,iconCls: 'icon_ext_add'
			,scope:this
			,handler:function(btn) {
				if(_IS_ADMIN==false){
					om_store.om_beneficiaryStorage.load({params: { 'member[employee_id]':_EMP_ID,start:0, limit:MAX_PAGE_SIZE}});
					pecaDataStores.companyStoreOLM.load();
					Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOLMemInfoDetail');
					Ext.getCmp('ol_membershipDetail').getForm().reset();
					om_function.clearHighlights();
					temp=[];
					tempStorageOfFields=[];
					tempStorageOfBFields=[];
					Ext.getCmp('ol_membershipDetail').getForm().load({
				    	url: '/membership/show'
				    	,params: {'member[employee_id]':_EMP_ID, auth:_AUTH_KEY}
				    	,method: 'POST'
				    	,waitMsgTarget: true
						,success: function(form, action) {
							om_function.loadValues(null);
						}
					});
					temp = [];
					tempStorageOfFields=[];
					tempStorageOfBFields=[];
					Ext.getCmp('ol_membershipDetail').getForm().setModeNew();
				}
				
			}
		},{
		text: '|'
		,id: 'om_btn_separator1'
		}
		,{
			text:'Delete'
			,id: 'om_del_btn'
			,tooltip:'Delete a Membership Request'
			,iconCls: 'icon_ext_del'
			,scope:this
			,handler:function(btn) {
				if(_IS_ADMIN==false){
					var index = Ext.getCmp('ol_membershipList').getSelectionModel().getSelected();
					if (!index) {
						showExtInfoMsg("Please select a Membership Request to delete.");
						return false;
					}
					if(index.data.status==2){
						Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
							if(btn=='yes') {
								Ext.Ajax.request({
									url: '/online_member/delete' 
									,method: 'POST'
									,params: {'request_no': index.data.request_no
											,auth:_AUTH_KEY
											, 'online_membership[modified_by]': _USER_ID}
									,waitMsg: 'Deleting Data...'
									,success: function(response, opts) {
										var obj = Ext.decode(response.responseText);
										if(obj.success){
											showExtInfoMsg(obj.msg);
											if (pecaDataStores.ol_membershipStore.getCount() % MAX_PAGE_SIZE == 1){
												var page = pecaDataStores.ol_membershipStore.getTotalCount() - MAX_PAGE_SIZE - 1;
												pecaDataStores.ol_membershipStore.load({params: {start:page<0?0:page, limit:MAX_PAGE_SIZE}});
											} else{
												pecaDataStores.ol_membershipStore.reload();
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
		,id: 'om_btn_separator2'
		}
		,{
			text:'Print'
			,id: 'om_print_btn'
			,tooltip:'Print'
			,iconCls: 'icon_ext_print'
			,scope:this
			,handler:function(btn) {
				var frm = Ext.getCmp('ol_membershipFilter').getForm();
				frm.onPreview(frm);
			}
		}
		]
		
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.ol_membershipStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};
var om_setRestrictions=function(frm){
	frm.findField('online_membership[last_name]').setReadOnly(true);
	frm.findField('online_membership[last_name]').addClass('x-item-disabled');
	frm.findField('online_membership[first_name]').setReadOnly(true);
	frm.findField('online_membership[first_name]').addClass('x-item-disabled');
	frm.findField('online_membership[middle_name]').setReadOnly(true);
	frm.findField('online_membership[middle_name]').addClass('x-item-disabled');
	// frm.findField('online_membership[member_date]').setReadOnly(true);
	frm.findField('online_membership[member_date]').addClass('x-item-disabled');
	frm.findField('online_membership[guarantor]').setReadOnly(true);
	frm.findField('online_membership[guarantor]').addClass('x-item-disabled');
	frm.findField('online_membership[non_member]').setReadOnly(true);
	frm.findField('online_membership[non_member]').addClass('x-item-disabled');
	// frm.findField('online_membership[member_status]').setReadOnly(true);
	frm.findField('online_membership[member_status]').addClass('x-item-disabled');
	frm.findField('online_membership[beneficiary_name1]').setReadOnly(true);
	frm.findField('online_membership[beneficiary_name1]').addClass('x-item-disabled');
	// frm.findField('online_membership[beneficiary_relationship1]').setReadOnly(true);
	frm.findField('online_membership[beneficiary_relationship1]').addClass('x-item-disabled');
	frm.findField('online_membership[beneficiary_address1]').setReadOnly(true);
	frm.findField('online_membership[beneficiary_address1]').addClass('x-item-disabled');
	frm.findField('online_membership[beneficiary_name2]').setReadOnly(true);
	frm.findField('online_membership[beneficiary_name2]').addClass('x-item-disabled');
	// frm.findField('online_membership[beneficiary_relationship2]').setReadOnly(true);
	frm.findField('online_membership[beneficiary_relationship2]').addClass('x-item-disabled');
	frm.findField('online_membership[beneficiary_address2]').setReadOnly(true);
	frm.findField('online_membership[beneficiary_address2]').addClass('x-item-disabled');
	frm.findField('online_membership[beneficiary_name3]').setReadOnly(true);
	frm.findField('online_membership[beneficiary_name3]').addClass('x-item-disabled');
	// frm.findField('online_membership[beneficiary_relationship3]').setReadOnly(true);
	frm.findField('online_membership[beneficiary_relationship3]').addClass('x-item-disabled');
	frm.findField('online_membership[beneficiary_address3]').setReadOnly(true);
	frm.findField('online_membership[beneficiary_address3]').addClass('x-item-disabled');
	frm.findField('online_membership[TIN]').setReadOnly(true);
	frm.findField('online_membership[TIN]').addClass('x-item-disabled');
	// frm.findField('online_membership[hire_date]').setReadOnly(true);
	frm.findField('online_membership[hire_date]').addClass('x-item-disabled');
	// frm.findField('online_membership[work_date]').setReadOnly(true);
	frm.findField('online_membership[work_date]').addClass('x-item-disabled');
	// frm.findField('online_membership[company_code]').setReadOnly(true);
	frm.findField('online_membership[company_code]').addClass('x-item-disabled');
	frm.findField('online_membership[department]').setReadOnly(true);
	frm.findField('online_membership[department]').addClass('x-item-disabled');
	frm.findField('online_membership[position]').setReadOnly(true);
	frm.findField('online_membership[position]').addClass('x-item-disabled');
	frm.findField('online_membership[office_no]').setReadOnly(true);
	frm.findField('online_membership[office_no]').addClass('x-item-disabled');
	frm.findField('online_membership[email_address]').setReadOnly(true);
	frm.findField('online_membership[email_address]').addClass('x-item-disabled');
	// frm.findField('membership[birth_date]').setReadOnly(true);
	frm.findField('membership[birth_date]').addClass('x-item-disabled');
	// frm.findField('membership[gender]').setReadOnly(true);
	frm.findField('membership[gender]').addClass('x-item-disabled');
	// frm.findField('membership[civil_status]').setReadOnly(true);
	frm.findField('membership[civil_status]').addClass('x-item-disabled');
	frm.findField('membership[spouse]').setReadOnly(true);
	frm.findField('membership[spouse]').addClass('x-item-disabled');
	frm.findField('membership[address_1]').setReadOnly(true);
	frm.findField('membership[address_1]').addClass('x-item-disabled');
	frm.findField('membership[address_2]').setReadOnly(true);
	frm.findField('membership[address_2]').addClass('x-item-disabled');
	frm.findField('membership[address_3]').setReadOnly(true);
	frm.findField('membership[address_3]').addClass('x-item-disabled');
	frm.findField('membership[home_phone]').setReadOnly(true);
	frm.findField('membership[home_phone]').addClass('x-item-disabled');
	frm.findField('membership[mobile_no]').setReadOnly(true);
	frm.findField('membership[mobile_no]').addClass('x-item-disabled');
	frm.findField('online_membership[member_remarks]').setReadOnly(true);
	frm.findField('online_membership[member_remarks]').addClass('x-item-disabled');
};
var om_clearRestrictions=function(frm){
	frm.findField('online_membership[last_name]').setReadOnly(false);
	frm.findField('online_membership[last_name]').removeClass('x-item-disabled');
	frm.findField('online_membership[first_name]').setReadOnly(false);
	frm.findField('online_membership[first_name]').removeClass('x-item-disabled');
	frm.findField('online_membership[middle_name]').setReadOnly(false);
	frm.findField('online_membership[middle_name]').removeClass('x-item-disabled');
	frm.findField('online_membership[member_date]').setReadOnly(false);
	frm.findField('online_membership[member_date]').removeClass('x-item-disabled');
	frm.findField('online_membership[guarantor]').setReadOnly(false);
	frm.findField('online_membership[guarantor]').removeClass('x-item-disabled');
	frm.findField('online_membership[non_member]').setReadOnly(false);
	frm.findField('online_membership[non_member]').removeClass('x-item-disabled');
	frm.findField('online_membership[member_status]').setReadOnly(false);
	frm.findField('online_membership[member_status]').removeClass('x-item-disabled');
	frm.findField('online_membership[beneficiary_name1]').setReadOnly(false);
	frm.findField('online_membership[beneficiary_name1]').removeClass('x-item-disabled');
	frm.findField('online_membership[beneficiary_relationship1]').setReadOnly(false);
	frm.findField('online_membership[beneficiary_relationship1]').removeClass('x-item-disabled');
	frm.findField('online_membership[beneficiary_address1]').setReadOnly(false);
	frm.findField('online_membership[beneficiary_address1]').removeClass('x-item-disabled');
	frm.findField('online_membership[beneficiary_name2]').setReadOnly(false);
	frm.findField('online_membership[beneficiary_name2]').removeClass('x-item-disabled');
	frm.findField('online_membership[beneficiary_relationship2]').setReadOnly(false);
	frm.findField('online_membership[beneficiary_relationship2]').removeClass('x-item-disabled');
	frm.findField('online_membership[beneficiary_address2]').setReadOnly(false);
	frm.findField('online_membership[beneficiary_address2]').removeClass('x-item-disabled');
	frm.findField('online_membership[beneficiary_name3]').setReadOnly(false);
	frm.findField('online_membership[beneficiary_name3]').removeClass('x-item-disabled');
	frm.findField('online_membership[beneficiary_relationship3]').setReadOnly(false);
	frm.findField('online_membership[beneficiary_relationship3]').removeClass('x-item-disabled');
	frm.findField('online_membership[beneficiary_address3]').setReadOnly(false);
	frm.findField('online_membership[beneficiary_address3]').removeClass('x-item-disabled');
	frm.findField('online_membership[TIN]').setReadOnly(false);
	frm.findField('online_membership[TIN]').removeClass('x-item-disabled');
	frm.findField('online_membership[hire_date]').setReadOnly(false);
	frm.findField('online_membership[hire_date]').removeClass('x-item-disabled');
	frm.findField('online_membership[work_date]').setReadOnly(false);
	frm.findField('online_membership[work_date]').removeClass('x-item-disabled');
	frm.findField('online_membership[company_code]').setReadOnly(false);
	frm.findField('online_membership[company_code]').removeClass('x-item-disabled');
	frm.findField('online_membership[department]').setReadOnly(false);
	frm.findField('online_membership[department]').removeClass('x-item-disabled');
	frm.findField('online_membership[position]').setReadOnly(false);
	frm.findField('online_membership[position]').removeClass('x-item-disabled');
	frm.findField('online_membership[office_no]').setReadOnly(false);
	frm.findField('online_membership[office_no]').removeClass('x-item-disabled');
	frm.findField('online_membership[email_address]').setReadOnly(false);
	frm.findField('online_membership[email_address]').removeClass('x-item-disabled');
	frm.findField('membership[birth_date]').setReadOnly(false);
	frm.findField('membership[birth_date]').removeClass('x-item-disabled');
	frm.findField('membership[gender]').setReadOnly(false);
	frm.findField('membership[gender]').removeClass('x-item-disabled');
	frm.findField('membership[civil_status]').setReadOnly(false);
	frm.findField('membership[civil_status]').removeClass('x-item-disabled');
	frm.findField('membership[spouse]').setReadOnly(false);
	frm.findField('membership[spouse]').removeClass('x-item-disabled');
	frm.findField('membership[address_1]').setReadOnly(false);
	frm.findField('membership[address_1]').removeClass('x-item-disabled');
	frm.findField('membership[address_2]').setReadOnly(false);
	frm.findField('membership[address_2]').removeClass('x-item-disabled');
	frm.findField('membership[address_3]').setReadOnly(false);
	frm.findField('membership[address_3]').removeClass('x-item-disabled');
	frm.findField('membership[home_phone]').setReadOnly(false);
	frm.findField('membership[home_phone]').removeClass('x-item-disabled');
	frm.findField('membership[mobile_no]').setReadOnly(false);
	frm.findField('membership[mobile_no]').removeClass('x-item-disabled');
	frm.findField('online_membership[member_remarks]').setReadOnly(false);
	frm.findField('online_membership[member_remarks]').removeClass('x-item-disabled');
};
