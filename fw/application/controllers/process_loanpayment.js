//Let's pretend we rendered our grid-columns with meta-data from our ORM framework.
var processLPColumns =  new Ext.grid.ColumnModel( 
	[	
	 	new Ext.grid.CheckboxSelectionModel(),
	 	{id: 'loan_code', header: 'Loan Code', width: 20, sortable: true, hidden: true, dataIndex: 'loan_code'}
		,{header: 'Loan Type', width: 20, sortable: true, dataIndex: 'loan_description'}
	]
);

var processLPList = function(){
	return {
		xtype: 'grid'
		,id: 'proLPList'
		,titlebar: false
		,store:  new Ext.data.Store({
			url: '/process_loan_payment/getLoans'
			,reader: new Ext.data.JsonReader({
			    totalProperty: 'total',
			    root: 'data'
			    },
		    	[{name: 'loan_code', mapping: 'loan_code', type: 'string'}
		    	,{name: 'loan_description', mapping: 'loan_description', type: 'string',
		    		convert: function(value, rec){return rec.loan_code + ' - ' + value;}}
		    	]
			)
			,baseParams: {auth:_AUTH_KEY}  //Transaction group = Capital Contribution
		})
		,region: 'center'
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 425
		,loadMask: true
		,sm: new Ext.grid.CheckboxSelectionModel({
			listeners: {
				'selectionchange': function() {
					var hd = Ext.fly(this.grid.getView().innerHd).child('div.x-grid3-hd-checker');
					if (this.getCount() < this.grid.getStore().getCount()) {
						hd.removeClass('x-grid3-hd-checker-on');
					} else {
						hd.addClass('x-grid3-hd-checker-on');
					}
				}
			}
		})
		,viewConfig: {
			forceFit:true
			,scrollOffset:0
		}
		,cm: processLPColumns
		
		,listeners:{
			'render':{
				scope:this
				,fn:function(grid){
				Ext.getCmp('proLPList').getStore().load({params: {start:0, limit:MAX_PAGE_SIZE}});
				}
			}
		}
	};
};

var processLoanPayment = function(){
	return{
		xtype:'form'
		,id:'processLP'
		,region:'center'
		,anchor: '100%'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,reader: new Ext.data.JsonReader({
			root: 'data'
			},[
			    {name: 'process_lp[current_period]', mapping: 'currdate', type: 'string'}
			]
		)
		,listeners:{
			'render':{
				scope:this
				,fn:function(form){
					form.load({
						url: '/process_loan_payment'
						,params: {auth:_AUTH_KEY}
						,method: 'POST'
						,waitMsgTarget: true
					
					});
					if(_PERMISSION[141]==0){
						Ext.getCmp('processLP').buttons[0].setDisabled(true);	
					}
					Ext.getCmp('proLPList').getStore().load();
				}
			}
		}
		,buttons:[{
			text: 'Process'
			,iconCls: 'icon_ext_proc'
		    ,handler : function(btn){
				var frm = Ext.getCmp('processLP').getForm();
				var items = Ext.getCmp('proLPList').getSelectionModel().getSelections();
				var rec = new Array();
	            Ext.each(items, function(r){	            	
	            	rec.push(r.get('loan_code'));
	            });
	            
	            if (items.length > 0){
	            	if (frm.isValid()){
	            		frm.submit({
    		    			url: '/process_loan_payment/processLoanPayment' 
    		    			,method: 'POST'
    		    			,timeout: 300000
    		    			,params: {'process_lp[data]': Ext.encode(rec)
    	            			,'process_lp[user_id]': _USER_ID
    		            		,auth:_AUTH_KEY}
    		    			,waitMsg: 'Processing Data...'
    		    			,success: function(form, action) {
								showExtInfoMsg(action.result.msg);
								var hd = Ext.fly(Ext.getCmp('proLPList').getView().innerHd).child('div.x-grid3-hd-checker');
								hd.removeClass('x-grid3-hd-checker-on');
								Ext.getCmp('proLPList').getSelectionModel().clearSelections();
								frm.findField('process_lp[penalty]').setValue(false);
    		    			}
    		    			,failure: function(form, action) {
    							showExtErrorMsg( action.result.msg);
    		    			}	
    		    		});
	            	}
	            } else{
	            	showExtInfoMsg('Please select loan type(s).');
	            }
		    }
		}]
	    ,items: [{
            xtype: 'textfield'
        	,name: 'process_lp[current_period]'
            ,fieldLabel: 'Current Period'
            ,anchor: '30%'
            ,readOnly: true
            ,cls: 'x-item-disabled'
        },{
            xtype: 'combo',
            fieldLabel: 'Company',
            anchor: '50%'
        	,"hiddenName": "process_lp[company]"
            ,typeAhead: true
    	    ,triggerAction: 'all'
    	    ,lazyRender:true
    	    ,store: pecaDataStores.companyStoreBP
    	    ,mode: 'local'
    	    ,valueField: 'company_code'
    	    ,displayField: 'company_name'
            ,emptyText: 'Please select'
            ,forceSelection: true
    	    ,submitValue: false
    	    ,required: true
    	    ,allowBlank: false
        },{
			layout: 'fit'
			,anchor: '100%'
			,xtype:'fieldset'	
			,title: 'Select Loan Type'
			,bodyStyle:{'padding':'10px'}	
            ,defaultType: 'grid'
            ,height: 250
			,items: [processLPList()]
        },{  	
        	xtype:'checkbox'
    		,boxLabel: 'Generate Penalty'
            ,id: 'gp'
            ,name: 'process_lp[penalty]'
            ,anchor:'40%'
            ,submitValue: true	
        }]
	};
};