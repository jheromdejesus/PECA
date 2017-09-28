//Let's pretend we rendered our grid-columns with meta-data from our ORM framework.
var posttransColumns =  new Ext.grid.ColumnModel( 
	[	
	 	new Ext.grid.CheckboxSelectionModel(),
	 	{id: 'transaction_group', width: 20, sortable: true, hidden: true, dataIndex: 'transaction_group'}
		,{header: 'Transaction', width: 20, sortable: true, dataIndex: 'transaction_description'}
	]
);

var posttransList = function(){
	return {
		xtype: 'grid'
		,id: 'posttransList'
		,titlebar: false
		,store:  new Ext.data.Store({
			url: '/post_transaction/load'
			,reader: new Ext.data.JsonReader({
			    root: 'data'
			    },
		    	[{name: 'transaction_group', mapping: 'transaction_group', type: 'string'}
		    	,{name: 'transaction_count', mapping: 'transaction_count', type: 'string'}
		    	,{name: 'transaction_description', mapping: 'transaction_group_description', type: 'string',
		    		convert: function(value, rec){return rec.transaction_group + ' ' + value + ' (' + rec.transaction_count + ')';}}
		    	]
			)
			,baseParams: {auth:_AUTH_KEY}  //Transaction group = Capital Contribution
		})
		,region: 'center'
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 425
		,width: 500
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

var postTransaction = function(){
	return{
		xtype:'form'
		,id:'postTransaction'
		,region:'center'
		,anchor: '100%'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,reader: new Ext.data.JsonReader({
			root: 'data'
			},[
			    {name: 'post_trans[current_period]', mapping: 'current_period', type: 'string'}
			    ,{name: 'post_trans[posting_date]', mapping: 'posting_date', type: 'string'}
			]
		)
		,listeners:{
			'render':{
				scope:this
				,fn:function(form){
					form.load({
						url: '/post_transaction'
						,params: {auth:_AUTH_KEY}
						,method: 'POST'
						,waitMsgTarget: true
					
					});
					if(_PERMISSION[50]==0){
						Ext.getCmp('postTransaction').buttons[0].setDisabled(true);	
					}
				}
			}
		}
		,buttons:[{
			text: 'Process'
			,iconCls: 'icon_ext_proc'
		    ,handler : function(btn){
				var frm = Ext.getCmp('postTransaction').getForm();
				var items = Ext.getCmp('posttransList').getSelectionModel().getSelections();
				var rec = new Array();
	            Ext.each(items, function(r){	            	
	            	rec.push(r.get('transaction_group'));
	            });
	            
	            if (items.length > 0){
	            	frm.submit({
		    			url: '/post_transaction/postTransactions' 
		    			,method: 'POST'
		    			,timeout: 1200000
		    			,params: {'post_trans[data]': Ext.encode(rec)
	            			,'post_trans[user_id]': _USER_ID
		            		,auth:_AUTH_KEY}
		    			,waitMsg: 'Processing Data...'
		    			,success: function(form, action) {
		    				//showExtInfoMsg( action.result.msg);
							var dlg = Ext.MessageBox.show({
								title: 'Info Message'
								,msg: action.result.msg
								,width:500
								,buttons: Ext.MessageBox.YESNO
								,fn: function(btn){
									if (btn == 'yes'){
										Ext.Ajax.request({
											url: '/post_transaction/incDay'
											,params: {auth:_AUTH_KEY}   
											,success: function(response, opts) {
												var tomdate = response.responseText;
												Ext.getCmp('post_trans[posting_date]').setValue(tomdate);
											}
											,failure: function(response, opts) {
	//	        								var obj = Ext.decode(response.responseText);
	//	        								showExtInfoMsg( obj.msg);
											}
										});
									}	
								}
								,icon: Ext.MessageBox.INFO
							}).getDialog();
							dlg.defaultButton = 2;
							dlg.focus();
							//var hd = Ext.fly(Ext.getCmp('posttransList').getView().innerHd).child('div.x-grid3-hd-checker');
							//hd.removeClass('x-grid3-hd-checker-on');
		    				Ext.getCmp('posttransList').getStore().load({params: {start:0, limit:MAX_PAGE_SIZE}});
		    			}
		    			,failure: function(form, action) {
		    				showExtErrorMsg( action.result.msg);
		    				Ext.getCmp('posttransList').getStore().load({params: {start:0, limit:MAX_PAGE_SIZE}});
		    			}	
		    		});
	            } else{
	            	showExtInfoMsg('Please select transaction(s).');
	            }
		    }
		}]
	    ,items: [{
            xtype: 'textfield'
        	,name: 'post_trans[current_period]'
            ,fieldLabel: 'Current Period'
            ,anchor: '30%'
            ,readOnly: true
            ,cls: 'x-item-disabled'
        },{
        	xtype: 'textfield'
    		,name: 'post_trans[posting_date]'
			,id: 'post_trans[posting_date]'
            ,fieldLabel: 'Posting Date'
            ,anchor: '30%'
        	,readOnly: true
        	,cls: 'x-item-disabled'
        },{
			layout: 'fit'
			,anchor: '100%'
			,xtype:'fieldset'	
			,title: 'Select Transactions to be Posted'
			,bodyStyle:{'padding':'10px'}	
            ,defaultType: 'grid'
            ,height: 250
			,items: [posttransList()]
        }]
	};
};