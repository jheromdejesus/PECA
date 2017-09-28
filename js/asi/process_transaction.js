//Let's pretend we rendered our grid-columns with meta-data from our ORM framework.
var protransColumns =  new Ext.grid.ColumnModel( 
	[	
	 	new Ext.grid.CheckboxSelectionModel(),
	 	{id: 'transaction_code', width: 20, sortable: true, hidden: true, dataIndex: 'transaction_code'}
		,{header: 'Transaction', width: 20, sortable: true, dataIndex: 'transaction_description'}
	]
);

var protransList = function(){
	return {
		xtype: 'grid'
		,id: 'protransList'
		,titlebar: false
		,store:  new Ext.data.Store({
			url: '/process_transaction/load'
			,reader: new Ext.data.JsonReader({
			    root: 'data'
			    },
		    	[{name: 'transaction_code', mapping: 'code', type: 'string'}
		    	,{name: 'transaction_description', mapping: 'name', type: 'string',
		    		convert: function(value, rec){return rec.code + ' ' + value;}}
		    	]
			)
			,baseParams: {auth:_AUTH_KEY}  //Transaction group = Capital Contribution
		})
		,region: 'center'
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 425
		//,width: 500
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
			,scrollOffset:0
		}
		,cm: protransColumns
		
//		,listeners:{
//			'render':{
//				scope:this
//				,fn:function(grid){
//				Ext.getCmp('protransList').getStore().load({params: {start:0, limit:MAX_PAGE_SIZE}});
//				}
//			}
//		}
	};
};

var processTransaction = function(){
	return{
		xtype:'form'
		,id:'processTransaction'
		,region:'center'
		,anchor: '100%'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,reader: new Ext.data.JsonReader({
			root: 'data'
			},[
			    {name: 'protrans[current_period]', mapping: 'current_period', type: 'string'}
			    ,{name: 'protrans[posting_date]', mapping: 'posting_date', type: 'string'}
			]
		)
		,listeners:{
			'render':{
				scope:this
				,fn:function(form){
					form.load({
						url: '/process_transaction'
						,params: {auth:_AUTH_KEY}
						,method: 'POST'
						,waitMsgTarget: true
					
					});
					if(_PERMISSION[51]==0){
						Ext.getCmp('processTransaction').buttons[0].setDisabled(true);	
					}
				}
			}
		}
		,buttons:[{
			text: 'Process'
			,iconCls: 'icon_ext_proc'
		    ,handler : function(btn){
				var frm = Ext.getCmp('processTransaction').getForm();
				var items = Ext.getCmp('protransList').getSelectionModel().getSelections();
				var rec = new Array();
	            Ext.each(items, function(r){	            	
	            	rec.push(r.get('transaction_code'));
	            });
	            
	            if ((items.length > 0) || frm.findField('protrans[bmb]').getValue() || frm.findField('protrans[npml]').getValue()){
	            	frm.submit({
		    			url: '/process_transaction/processTransactions' 
		    			,method: 'POST'
						,timeout: 300000
		    			,params: {'protrans[data]': Ext.encode(rec)
	            			,'protrans[user_id]': _USER_ID
		            		,auth:_AUTH_KEY}
		    			,waitMsg: 'Processing Data...'
		    			,success: function(form, action) {
		    				//showExtInfoMsg( action.result.msg);
							Ext.MessageBox.show({
								title: 'Info Message'
								,msg: action.result.msg
								,width:500
								,buttons: Ext.MessageBox.YESNO
								,fn: function(btn){
									if (btn == 'yes'){
										//alert('do something here');
										var frm2 = document.createElement('form');
						                frm2.id = 'frmDownload';
						                frm2.name = id;
						                frm2.className = 'x-hidden';
						                document.body.appendChild(frm2);
							            
							            Ext.Ajax.request({
							    			url: '/report_transactioncontroltotals' 
											,method: 'POST'
											,form: Ext.get('frmDownload')
											,params: {report_type: '1' //by company
														,file_type: '2' //pdf
														,report_date: frm.findField('protrans[posting_date]').getValue()
								        				,auth:_AUTH_KEY}
											,isUpload: true
											,success: function(response, opts) {
												var obj = Ext.decode(response.responseText);
												if(obj.success){
													//App.setAlert(true, obj.msg);
													showExtInfoMsg(obj.msg);
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
								}
								,icon: Ext.MessageBox.INFO
							});
							//var hd = Ext.fly(Ext.getCmp('protransList').getView().innerHd).child('div.x-grid3-hd-checker');
							//hd.removeClass('x-grid3-hd-checker-on');
							frm.findField('protrans[bmb]').setValue(false);
							frm.findField('protrans[npml]').setValue(false);
		    				Ext.getCmp('protransList').getStore().load({params: {start:0, limit:MAX_PAGE_SIZE}});
		    			}
		    			,failure: function(form, action) {
		    				showExtErrorMsg( action.result.msg);
		    				Ext.getCmp('protransList').getStore().load({params: {start:0, limit:MAX_PAGE_SIZE}});
		    			}	
		    		});
	            } else{
	            	showExtInfoMsg('Please select transaction(s).');
	            }
	            
		    }
		}]
	    ,items: [{
            xtype: 'textfield'
        	,name: 'protrans[current_period]'
            ,fieldLabel: 'Current Period'
            ,anchor: '30%'
            ,readOnly: true
            ,cls: 'x-item-disabled'
        },{
        	xtype: 'textfield'
    		,name: 'protrans[posting_date]'
            ,fieldLabel: 'Posting Date'
            ,anchor: '30%'
        	,readOnly: true
        	,cls: 'x-item-disabled'
        },{
			layout: 'fit'
			,xtype:'fieldset'
			,anchor: '100%'
			,title: 'Select Transactions to be Processed'
			,bodyStyle:{'padding':'10px'}	
            ,defaultType: 'grid'
            ,height: 250
			,items: [protransList()]
        },{ 	
        	xtype:'checkbox'
    		,boxLabel: 'Compute BMB for the Month'
            ,id: 'bmb'
            ,name: 'protrans[bmb]'
            ,anchor:'40%'
            ,submitValue: true
        },{  	
        	xtype:'checkbox'
    		,boxLabel: 'Compute NPML for the Month'
            ,id: 'npml'
            ,name: 'protrans[npml]'
            ,anchor:'40%'
            ,submitValue: true	
        }]
	};
};