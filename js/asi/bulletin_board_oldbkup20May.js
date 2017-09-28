
//Let's pretend we rendered our grid-columns with meta-data from our ORM framework.
var bulletinColumns =  new Ext.grid.ColumnModel( 
	[
		{id:  'topic_id', header: "Topic ID",hidden:true, width: 100, sortable: true, dataIndex: 'bulletin[topic_id]'}
		,{header: "Subject", width: 300, sortable: true, dataIndex: 'bulletin[subject]'}
		//,{header: "Content", width: 200, height: 50, sortable: true, dataIndex: 'bulletin[content]'}
		,{header: "Published Date", width: 200, sortable: true, dataIndex: 'bulletin[published_date]'}
		,{header: "End Date", width: 200, sortable: true, hidden: true, dataIndex: 'bulletin[end_date]'}
	]
);

var checkboxSel = new Ext.grid.CheckboxSelectionModel();

var tpl = new Ext.XTemplate(
	'<tpl for=".">',
        '<div class="thumb-wrap" id="{name}">',
	    '<div class="thumb"><a href="{url}" target="_blank" ><img style="border:0px;" src="{url}" title="{name}"></a></div>',
	    '<span class="x-editable">{name}</span></div>',
    '</tpl>',
    '<div class="x-clear"></div>'
);

					
/**
 * the store
 */
var store = new Ext.data.JsonStore({
        //url: 'get-images.php',
        root: 'images',
        fields: ['name', 'url', {name:'size', type: 'float'}, {name:'lastmod', type:'date', dateFormat:'timestamp'}]
    });
//    store.load();



var panel = function(){
	return new Ext.Panel({
    //id:'images-view',
    frame:true,
	region:'center',
    width: '50%',
    items: [ stickyItems()
	, bulletinList()]
});
};

var panelImptDocs = function(){
	return new Ext.Panel({
		frame:true,
		region:'east',
		width: '50%',
		items: [importantDocs()]
	});
};
				
var fileColumns =  new Ext.grid.ColumnModel( 
		[
		 	checkboxSel
			,{id:  'uploaded_id', header: "File ID",hidden:true, width: 100, sortable: true, dataIndex: 'attachment_id'}
			,{header: "Path", width: 200, sortable: true, dataIndex: 'path'}
			,{header: "File Type", width: 200,hidden:false, sortable: true, dataIndex: 'type'}
			,{header: "File Size(KB)", width: 200,hidden:false, sortable: true, dataIndex: 'size'}
			
		]
	);

var attachedColumns =  new Ext.grid.ColumnModel( 
		[
		 	{id:  'attachment_id', header: "File ID",hidden:true, width: 100, sortable: true, dataIndex: 'attachment_id'}
			,{header: "Path", width: 200, sortable: true, dataIndex: 'path'}
			,{header: "File Type", width: 200,hidden:false, sortable: true, dataIndex: 'type'}
			,{header: "File Size(KB)", width: 200,hidden:false, sortable: true, dataIndex: 'size'}
			
		]
	);

var importantDocsColumn =  new Ext.grid.ColumnModel( 
		[
			{id:  'attachment_id', header: "File ID",hidden:true, width: 100, sortable: true, dataIndex: 'attachment_id'}
			,{header: "", width: 30, align:'right', dataIndex: 'type', renderer:function(value,rec){
				if(value=="image/jpeg" || value=="image/pjpeg" || value=="image/gif" || value=="image/png"
					|| value=="image/x-png" || value=="image/bmp"){ //image attachments
					return "<img src=\"/images/picture.gif\"";
				}
				else if(value=="text/html"){ //html attachments
					return "<img src=\"/images/explorer.gif\"";
				}
				else if(value=="application/msword"  //word attachments
					|| value=="application/vnd.openxmlformats-officedocument.wordprocessingml.document"){
					return "<img src=\"/images/doc.gif\"";
				}
				else if(value=="application/excel" || value=="application/vnd.ms-excel"  //excel attachments
					|| value=="application/msexcel"
					|| value=="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
					|| value=="application/download"
					|| value=="application/vnd.excel"){
					return "<img src=\"/images/excel.gif\"";
				}
				else if(value=="application/powerpoint" || value=="application/vnd.ms-powerpoint"
					|| value=="application/octet-stream"){ //powerpoint attachments
					return "<img src=\"/images/powerpoint.gif\"";
				}
				else if(value=="text/plain" || value=="text/richtext" || value=="text/rtf"){ //text attachments
					return "<img src=\"/images/text.gif\"";
				}
				else if(value=="text/xml"){ //xml attachments
					return "<img src=\"/images/xml.gif\"";
				}
				else if(value=="application/pdf" || value=="application/x-download"){ //pdf attachments
					return "<img src=\"/images/pdf.gif\"";
				}
			}}
			,{header: "Name", width: 200, sortable: true, dataIndex: 'filename'}
			,{header: "Path", width: 200, hidden: true, sortable: true, dataIndex: 'path'}
			,{header: "File Type", width: 200,hidden:true, sortable: true, dataIndex: 'type'}
			,{header: "File Size(KB)", width: 200,hidden:false, sortable: true, dataIndex: 'size'}
			
		]
	);

var uploadFormWin = function(){
	return new Ext.Window({
		id: 'uploadFormWin'
		,title: 'Upload a File'
		,fileUpload: true
		,width: 400
		,frame: true
		,autoHeight: true
		,plain: true
		,modal: true
		,resizable: false
		,closable: false
		,constrainHeader: true
		,bodyStyle:{"padding":"5px"}
		,items:[ fp()]
		,padding:4
	});
};

var bulletinDetail = function(){
	return {
		xtype:'form'
		,id:'bulletinDetail'
		,region:'center'
		,title: 'Details'
		,hidden:false
		,anchor: '100%'
		,autoscroll:true
		,frame: true
		,bodyStyle: 'padding: 10px 10px 0 10px;'
		,reader: pecaReaders.bulletinReader
		,buttons:[{
			text: 'Preview'
				,iconCls: 'icon_ext_preview'
				,handler : function(){
					
					var bul_html = Ext.getCmp('bulletinDetail').getForm().findField('bulletin[content]').getValue();
		            var win = new Ext.Window({
						id: 'bulletin_viewer'
						,title: 'Bulletin Viewer'
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
						,html       : "<div style='background-color:white;  padding:1%;  height:98%; width:98%'  >" + bul_html + "</div>"
						,items: [{
					    	xtype: 'fieldset'
				    	    ,title: 'Attached Files'
				    	    ,layout: 'fit'	    
					        ,anchor: '98%'
							,boxMinWidth:500
							,boxMaxWidth:1000
					        ,autoHeight: true
						    ,items: [{	
								layout: 'fit'
					            ,defaultType: 'grid'
					            ,height: 100
								,items: [attachedList()]
							}]
					    }]
				        ,buttons:[{
				 			text: 'Cancel'
				 			,iconCls: 'icon_ext_cancel'
				 		    ,handler : function(btn){
				 				Ext.getCmp('bulletin_viewer').close();				
				 		    }
				 		}]
					});
		            win.show();
					
			    }
			},{
			text: 'Cancel'
			,iconCls: 'icon_ext_cancel'
		    ,handler : function(btn){
				Ext.getCmp('bulletinDetail').getForm().reset();
				Ext.getCmp('homeCardBody').layout.setActiveItem('pnlBulletinBoard');
				pecaDataStores.bulletinStore.reload();
				pecaDataStores.bulletinStickyStore.reload();
				pecaDataStores.fileStore2.reload();
				pecaDataStores.formImageStore.load();
		    }
		},{
			text: 'Delete'
			//,id: 'btnDelete'
			,iconCls: 'icon_ext_del'
			,handler : function(){
				Ext.Msg.confirm('Confirm Action','Are you sure you want to delete this record?',function(btn) {
					if(btn=='yes') {
						Ext.getCmp('bulletinDetail').getForm().submit({
							url: '/bulletin_board/delete' 
							,method: 'POST'
							,params: {auth:_AUTH_KEY, 'user[modified_by]': _USER_ID}
							,waitMsg: 'Deleting Data...'
							,success: function(form, action) {
				    			showExtInfoMsg(action.result.msg);
				    			Ext.getCmp('bulletinDetail').setModeNew();
				    			Ext.getCmp('bulletinDetail').getForm().reset();
								Ext.getCmp('homeCardBody').layout.setActiveItem('pnlBulletinBoard');
								if (pecaDataStores.bulletinStore.getCount() % MAX_PAGE_SIZE == 1){
									var page = pecaDataStores.bulletinStore.getTotalCount() - MAX_PAGE_SIZE - 1;
									pecaDataStores.bulletinStore.load({params: {start:page<0?0:page, limit:MAX_PAGE_SIZE}});
								} else{
									pecaDataStores.bulletinStore.reload();
								}
								
								if (pecaDataStores.bulletinStickyStore.getCount() % MAX_PAGE_SIZE == 1){
									var page1 = pecaDataStores.bulletinStickyStore.getTotalCount() - MAX_PAGE_SIZE - 1;
									pecaDataStores.bulletinStickyStore.load({params: {start:page1<0?0:page1, limit:MAX_PAGE_SIZE}});
								} else{
									pecaDataStores.bulletinStickyStore.reload();
								}
								
								pecaDataStores.fileStore2.reload();
							}
							,failure: function(form, action) {
								showExtErrorMsg(action.result.msg);
							}	
						});
					}
				});
		    }
		},{
			text:'Save'
			//,id: 'btnSave'
			,iconCls: 'icon_ext_save'
		    ,handler: function(){
		    	var frm = Ext.getCmp('bulletinDetail').getForm();
		    	if(frm.isValid()){
		    		if (frm.isModeNew()) {
		    			frm.onSave(frm);
		    		} else {
		    		   	frm.onUpdate(frm);
		            }
		    	}        	
		    }
		}]
		,items: [{
	        layout:'column'
			,id:'detail_form_id'
	        ,items:[{
	            columnWidth:1
	            ,layout: 'form'
	            ,defaultType: 'textfield'
	            ,labelWidth: 150
	            ,defaults: {width: 300}
	            ,items: [{
				    xtype: 'hidden'
				    ,name: 'frm_mode'
				    ,value: FORM_MODE_NEW
				    ,listeners: {'change':{fn: function(obj,value){
	                }}}
				},{
						xtype: 'hidden'
						,fieldLabel: ''
		                ,name: 'bulletin[topic_id]'
				        ,anchor: '50%'
						,boxMinWidth:200
						,boxMaxWidth:400
		                ,allowBlank: false
		                ,required: true
		                ,maxLength: 20
		                ,autoCreate: {tag: 'input', type: 'text', maxlength: '20'}
		        },{
					xtype:'datefield'
					,fieldLabel:'Published Date'
					,name: 'bulletin[published_date]'
					,boxMinWidth:200
					,boxMaxWidth:200
					,anchor: '50%'
					,maxLength: 10
					,required: true
					,altFormats:'m/d/Y'
					,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
					,allowBlank: false
					/*,validator: function(value1){
						var frm = Ext.getCmp('bulletinDetail').getForm();
						var value2 = frm.findField('bulletin[end_date]').value;
						value1 = Ext.util.Format.date(value1, "y/m/d");
						value2 = Ext.util.Format.date(value2, "y/m/d");
						if (value2 != null && value1 != ''){
							if (value2 >= value1){
								return true;
							} else{
								return 'End Date should be greater than or equal to Publish Date.';
							}
						}else{
							return true;
						}
						
					}*/
				}
				,{
		        	xtype:'datefield'
		        	,fieldLabel:'End Date'
		        	,name: 'bulletin[end_date]'
			        ,anchor: '50%'
					,boxMinWidth:150
					,boxMaxWidth:150
					,maxLength: 10
		        	,altFormats:'m/d/Y'
       				,allowBlank: true
		        	,required: false
					,hidden: true
	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '10'}
					/*,validator: function(value2){
						var frm = Ext.getCmp('bulletinDetail').getForm();
						var value1 = frm.findField('bulletin[published_date]').value;
						value1 = Ext.util.Format.date(value1, "y/m/d");
						value2 = Ext.util.Format.date(value2, "y/m/d");
						
						if (value1 != null && value1 != ''){
							if (value1 <= value2){
								return true;
							} else{
								return 'Publish Date should be lesser than or equal to End Date.';
							}
						}else{
							return true;
						}
						
					}*/
					
		        },{
	                fieldLabel: 'Subject'
	                ,name: 'bulletin[subject]'
			        ,anchor: '100%'
					,boxMinWidth:200
					,boxMaxWidth:400
	                ,allowBlank: false
	                ,required: true
	                ,maxLength: 30
	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '30'}
	            }
				,{
					xtype:'checkbox'
					,boxLabel: 'Sticky' 
					,anchor: '50%'					
					,name: 'bulletin[sticky]'
					,submitValue: false
				}
				,{
	            	xtype: 'htmleditor'
	            	,fieldLabel: 'Content'
	            	,name: 'bulletin[content]'
	            	//,required: true
	            	,labelSeparator: ''
	            	,height: 400
			        ,anchor: '100%'
					,boxMinWidth:345
					,boxMaxWidth:845
					, plugins: [  
				         new Ext.ux.form.HtmlEditor.Word(),  
				         new Ext.ux.form.HtmlEditor.Divider(),  
				         new Ext.ux.form.HtmlEditor.Table(),  
				         new Ext.ux.form.HtmlEditor.HR(),  
				         new Ext.ux.form.HtmlEditor.IndentOutdent(),  
				         new Ext.ux.form.HtmlEditor.SubSuperScript(),  
				         new Ext.ux.form.HtmlEditor.RemoveFormat()  
				     ]  
	            }]
	        }]
	    },{
	    	xtype: 'fieldset'
    	    ,title: 'Upload A File'
    	    ,layout: 'fit'	    
	        ,anchor: '100%'
			,boxMinWidth:500
			,boxMaxWidth:1000
	        ,autoHeight: true
		    ,items: [{	
				layout: 'fit'
	            ,defaultType: 'grid'
	            ,height: 250
				,items: [fileList()]
			}]
	    }]
	    ,isModeNew: function() {
	    	return (Ext.getCmp('bulletinDetail').getForm().findField('frm_mode').getValue() == FORM_MODE_NEW);
	    }
	    ,setModeNew: function() {
	    	Ext.getCmp('bulletinDetail').getForm().findField('frm_mode').setValue(FORM_MODE_NEW);
	    	Ext.getCmp('bulletinDetail').buttons[0].setVisible(true);  //cancel button
	    	Ext.getCmp('bulletinDetail').buttons[1].setVisible(true);  //delete button
	    	Ext.getCmp('bulletinDetail').buttons[2].setVisible(false);  //save button
			Ext.getCmp('bulletinDetail').buttons[3].setVisible(true);  //cancel button
	    	//Ext.getCmp('bulletinDetail').getForm().findField('bulletin[topic_id]').setReadOnly(false);
			//Ext.getCmp('bulletinDetail').getForm().findField('bulletin[topic_id]').removeClass('x-item-disabled');
	    	Ext.getCmp('bulletinDetail').getForm().findField('bulletin[content]').setValue("");
	    	Ext.getCmp('fileList').setDisabled(false);
		}
		,setModeUpdate: function() {
			Ext.getCmp('bulletinDetail').getForm().findField('frm_mode').setValue(FORM_MODE_UPDATE);
			Ext.getCmp('bulletinDetail').buttons[0].setVisible(true);  //cancel button
	    	Ext.getCmp('bulletinDetail').buttons[1].setVisible(true);  //delete button
	    	Ext.getCmp('bulletinDetail').buttons[2].setVisible(true);  //save button
			Ext.getCmp('bulletinDetail').buttons[3].setVisible(true);  //cancel button
	    	//Ext.getCmp('bulletinDetail').getForm().findField('bulletin[topic_id]').setReadOnly(true);
			//Ext.getCmp('bulletinDetail').getForm().findField('bulletin[topic_id]').addClass('x-item-disabled');
	    	Ext.getCmp('fileList').setDisabled(false);
	    }
		,onSave: function(frm){
			var rowCount = pecaDataStores.fileStore.getCount();
	    	var jsonData = "[";
	    	if(rowCount > 0){
	    		for(var i = 0; i < rowCount; i++){
	    			var rec = pecaDataStores.fileStore.getAt(i);
	    			jsonData += Ext.encode(rec.data);
					if((i+1)<rowCount){
						jsonData += ",";
					}
	    		}
	    	}
	    	jsonData += "]";
			
        	frm.submit({
    			url: '/bulletin_board/add' 
    			,method: 'POST'
    			,params: {auth:_AUTH_KEY
						, 'bulletin[sticky]': Ext.getCmp('bulletinDetail').getForm().findField('bulletin[sticky]').getValue()? 'Y': 'N'
    					, 'bulletin[created_by]': _USER_ID
    					, 'files' : jsonData
    					}
    			,waitMsg: 'Creating Data...'
    			,success: function(form, action) {
        			showExtInfoMsg(action.result.msg);
        			Ext.getCmp('bulletinDetail').getForm().findField('bulletin[topic_id]').setValue(action.result.topic_id);
    				frm.setModeUpdate();
    			}
    			,failure: function(form, action) {
    				//if(action.result.error_code == 2){
    					showExtErrorMsg(action.result.msg);
    				/*}else{
	    				Ext.Msg.confirm('Confirm Action',action.result.msg,function(btn) {
	    					if(btn=='yes'){
	    						form.onUpdate(form);
	    					}
	    				});
    				}*/
    			}	
    		});
		}
		,onUpdate: function(frm){
			var rowCount = pecaDataStores.fileStore.getCount();
	    	var jsonData = "[";
	    	if(rowCount > 0){
	    		for(var i = 0; i < rowCount; i++){
	    			var rec = pecaDataStores.fileStore.getAt(i);
	    			jsonData += Ext.encode(rec.data);
					if((i+1)<rowCount){
						jsonData += ",";
					}
	    		}
	    	}
	    	jsonData += "]";
			
			frm.submit({
    			url: '/bulletin_board/update' 
    			,method: 'POST'
    			,waitMsg: 'Updating Data...'
    			,params: {auth:_AUTH_KEY
						, 'bulletin[sticky]': Ext.getCmp('bulletinDetail').getForm().findField('bulletin[sticky]').getValue()? 'Y': 'N'
    			        , 'bulletin[modified_by]': _USER_ID
    			        , 'files' : jsonData	
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
	};
};
var stickyItems = function(){
	return {
		xtype: 'grid'
		,id: 'stickyItems'
		,titlebar: true
		,title: 'Sticky Items'
		,store: pecaDataStores.bulletinStickyStore
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,region: 'center'
		,loadMask: true
		,height: 200
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit:true
			,scrollOffset:0
		}
		,cm: bulletinColumns
		
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					Ext.getCmp('bulletinDetail').getForm().reset();
					Ext.getCmp('bulletinDetail').findById("detail_form_id").setVisible(true);	
					var rec = grid.getStore().getAt(row);
					Ext.fly(Ext.getCmp('fileList').getView().innerHd).child('div.x-grid3-hd-checker').removeClass('x-grid3-hd-checker-on'); 
					pecaDataStores.fileStore.removeAll();
					pecaDataStores.fileStore.load({params: {'bulletin[topic_id]':rec.get('bulletin[topic_id]')}});
					if(_IS_ADMIN==false){
						var bul_html = Ext.getCmp('bulletinDetail').getForm().findField('bulletin[content]').getValue();
			            var win = new Ext.Window({
							id: 'bulletin_viewer'
							,title: 'Bulletin Viewer'
							,frame: true
							,layout: 'form'
							,width: 800
							,height: 500
							,plain: true
							,modal: true
							,resizable: false
							,closable: true
							,constrainHeader: true
							,bodyStyle:{"padding":"5px"}
							,autoScroll: true
							,loadMask: true	
							,html       : "<div style='background-color:white; padding:1%;  height:98%; width:98%' >" + rec.get('bulletin[content]') + "</div>"
							,items: [{
						    	xtype: 'fieldset'
					    	    ,title: 'Attached Files'
					    	    ,layout: 'fit'	    
						        ,anchor: '98%'
								,boxMinWidth:500
								,boxMaxWidth:1000
						        ,autoHeight: true
							    ,items: [{	
									layout: 'fit'
						            ,defaultType: 'grid'
						            ,height: 100
									,items: [attachedList()]
								}]
						    }]
					        ,buttons:[{
					 			text: 'Cancel'
					 			,iconCls: 'icon_ext_cancel'
					 		    ,handler : function(btn){
					 				Ext.getCmp('bulletin_viewer').close();				
					 		    }
					 		}]
						});
			            win.show();
					
					} else {
						Ext.getCmp('bulletinDetail').getForm().setModeUpdate();
						Ext.getCmp('bulletinDetail').getForm().load({
					    	url: '/bulletin_board/show'
					    	,params: {'bulletin[topic_id]':(rec.get('bulletin[topic_id]'))
									  ,auth:_AUTH_KEY}
					    	,method: 'POST'
					    	,waitMsgTarget: true
					    	
						});
						Ext.getCmp('homeCardBody').layout.setActiveItem('pnlBulletinBoardDetail');
					}
				}
			}
			,'render':{
				scope:this
				,fn:function(grid){
					pecaDataStores.bulletinStickyStore.load({params: {is_admin:_IS_ADMIN, start:0, limit:MAX_PAGE_SIZE}});
					if(_IS_ADMIN == false){
						Ext.getCmp('bul_new_btn').setVisible(false);
						//Ext.getCmp('bul_btn_separator1').setVisible(false);
						//Ext.getCmp('bul_edit_btn').setVisible(false);
					}
				}
			}
		}
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.bulletinStickyStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};

var importantDocs = function(){
	return {
		xtype: 'grid'
		,id: 'importantDocs'
		,titlebar: true
		,title: 'Important Documents'
		,store: pecaDataStores.fileStore2
		,region: 'center'
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,loadMask: true
		,height: 400
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit:true
			,scrollOffset:0
		}
		,cm: importantDocsColumn
		,tbar: [{
			text:'Update Forms'
			,tooltip:'Edit downloadable forms.'
			,id: 'bul_edit_btn'
			,iconCls: 'icon_ext_edit'
			,scope:this
			,handler:function(btn) {
				Ext.getCmp('bulletinDetail').findById("detail_form_id").setVisible(false);	
				Ext.getCmp('homeCardBody').layout.setActiveItem('pnlBulletinBoardDetail');
				bulletinDetail().setModeUpdate();
				pecaDataStores.fileStore.load({params: {'bulletin[topic_id]':'0'}});
				Ext.getCmp('bulletinDetail').getForm().findField('bulletin[topic_id]').setValue("0");
				Ext.getCmp('bulletinDetail').buttons[0].setVisible(false);  //preview  button
		    	Ext.getCmp('bulletinDetail').buttons[1].setVisible(true);  //cancel button
		    	Ext.getCmp('bulletinDetail').buttons[2].setVisible(false);  //delete button
		    	Ext.getCmp('bulletinDetail').buttons[3].setVisible(false);  //save button
			}
		}]
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
		            window.open(rec.get('path'), '_blank');
				}
			}
			,'render':{
				scope:this
				,fn:function(grid){
					pecaDataStores.fileStore2.load({params: {'topic_id':'0', start:0}});
					if(_IS_ADMIN == false){
						Ext.getCmp('bul_edit_btn').setVisible(false);
					}
				}
			}
		}
	};
};

var bulletinList = function(){
	return {
		xtype: 'grid'
		,id: 'bulletinList'
		,titlebar: false
		,store: pecaDataStores.bulletinStore
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,loadMask: true
		,region: 'center'
		,height: 200
		,sm: new Ext.grid.RowSelectionModel({singleSelect:true})
		,viewConfig: {
			forceFit:true
			,scrollOffset:0
		}
		,cm: bulletinColumns
		
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					Ext.getCmp('bulletinDetail').getForm().reset();
					Ext.getCmp('bulletinDetail').findById("detail_form_id").setVisible(true);	
					var rec = grid.getStore().getAt(row);
					Ext.fly(Ext.getCmp('fileList').getView().innerHd).child('div.x-grid3-hd-checker').removeClass('x-grid3-hd-checker-on'); 
					pecaDataStores.fileStore.removeAll();
					pecaDataStores.fileStore.load({params: {'bulletin[topic_id]':rec.get('bulletin[topic_id]')}});
					if(_IS_ADMIN==false){
						var bul_html = Ext.getCmp('bulletinDetail').getForm().findField('bulletin[content]').getValue();
			            var win = new Ext.Window({
							id: 'bulletin_viewer'
							,title: 'Bulletin Viewer'
							,frame: true
							,layout: 'form'
							,width: 800
							,height: 500
							,plain: true
							,modal: true
							,resizable: false
							,closable: true
							,constrainHeader: true
							,bodyStyle:{"padding":"5px"}
							,autoScroll: true
							,loadMask: true	
							,html       : "<div style='background-color:white; padding:1%;  height:98%; width:98%' >" + rec.get('bulletin[content]') + "</div>"
							,items: [{
						    	xtype: 'fieldset'
					    	    ,title: 'Attached Files'
					    	    ,layout: 'fit'	    
						        ,anchor: '98%'
								,boxMinWidth:500
								,boxMaxWidth:1000
						        ,autoHeight: true
							    ,items: [{	
									layout: 'fit'
						            ,defaultType: 'grid'
						            ,height: 100
									,items: [attachedList()]
								}]
						    }]
					        ,buttons:[{
					 			text: 'Cancel'
					 			,iconCls: 'icon_ext_cancel'
					 		    ,handler : function(btn){
					 				Ext.getCmp('bulletin_viewer').close();				
					 		    }
					 		}]
						});
			            win.show();
					
					} else {
						Ext.getCmp('bulletinDetail').getForm().setModeUpdate();
						Ext.getCmp('bulletinDetail').getForm().load({
					    	url: '/bulletin_board/show'
					    	,params: {'bulletin[topic_id]':(rec.get('bulletin[topic_id]'))
									  ,auth:_AUTH_KEY}
					    	,method: 'POST'
					    	,waitMsgTarget: true
					    	
						});
						Ext.getCmp('homeCardBody').layout.setActiveItem('pnlBulletinBoardDetail');
					}
				}
			}
			,'render':{
				scope:this
				,fn:function(grid){
					pecaDataStores.bulletinStore.load({params: {is_admin:_IS_ADMIN, start:0, limit:MAX_PAGE_SIZE}});
					if(_IS_ADMIN == false){
						Ext.getCmp('bul_new_btn').setVisible(false);
						//Ext.getCmp('bul_btn_separator1').setVisible(false);
						//Ext.getCmp('bul_edit_btn').setVisible(false);
					}
				}
			}
		}
		,tbar:[{
			text:'New'
			,tooltip:'Add a new Bulletin'
			,iconCls: 'icon_ext_add'
			,id: 'bul_new_btn'
			,scope:this
			,handler:function(btn) {
				Ext.getCmp('bulletinDetail').findById("detail_form_id").setVisible(true);	
				Ext.getCmp('homeCardBody').layout.setActiveItem('pnlBulletinBoardDetail');
				Ext.getCmp('bulletinDetail').getForm().reset();
				Ext.fly(Ext.getCmp('fileList').getView().innerHd).child('div.x-grid3-hd-checker').removeClass('x-grid3-hd-checker-on'); 
				bulletinDetail().setModeNew();
				pecaDataStores.fileStore.removeAll();
			}
		}]
		,bbar: new Ext.PagingToolbar({
	        pageSize: MAX_PAGE_SIZE
	        ,store: pecaDataStores.bulletinStore
	        ,displayInfo: true
	        ,displayMsg: 'Record {0} - {1} of {2}'
	        ,emptyMsg: "No records found"
	    })
	};
};

var fileList = function(){
	return {
		xtype: 'grid'
		,id: 'fileList'
		,titlebar: true
		,store: pecaDataStores.fileStore
		,region: 'center'
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 250
		,anchor: '100%'
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
				uploadFormWin().show();
			}
		}
		,{
			text: 'Remove'
			,tooltip:'Remove Selected Uploaded File'
			,iconCls: 'icon_ext_del'
			,scope:this
			,handler: function(){
				var rowsSelected = Ext.getCmp('fileList').getSelectionModel().getSelections();
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
						   
						pecaDataStores.fileStore.remove(aRecord);
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
					showExtInfoMsg("Please select a file to delete.");
		        	return false;
				}
			}
		}]
	};
};


var attachedList = function(){
	return {
		xtype: 'grid'
		,id: 'attachedList'
		,titlebar: true
		,store: pecaDataStores.fileStore
		,region: 'center'
		,enableColumnHide: false
		,enableColumnMove: false
		,enableHdMenu: false
		,height: 250
		,anchor: '98%'
		,viewConfig: {
			forceFit:true
			,scrollOffset:0
		}
		,cm: attachedColumns
		,listeners:{
			'rowdblclick':{
				scope:this
				,fn:function(grid, row, e) {
					var rec = grid.getStore().getAt(row);
		            window.open(rec.get('path'), '_blank');
				}
			}
		}
	};
};

var fp =  function(){
	return {
		xtype:'form'
	    ,fileUpload: true
	    ,anchor: '100%'
	    ,id:'fp'
	    ,frame: true
	    ,enctype : 'multipart/form-data'
	    ,bodyStyle: 'padding: 10px 10px 0 10px;'
	    ,labelWidth: 50
		,allowBlank: false
		,required: true
	    ,items: [{
	        xtype: 'fileuploadfield',
	        id: 'file',
	        emptyText: 'Select a File',
	        fieldLabel: 'File',
	        name: 'file',
	        buttonText: '',
			anchor: '100%',
	        buttonCfg: {
	            iconCls: 'upload-icon'
	        }
	    }],
	    buttons: [{
	        text: 'Upload'
	        ,name:'Upload'
	        ,handler: function(){
	    	    var frmUp = Ext.getCmp('fp').getForm();
				//if(pecaDataStores.fileStore.totalLength >= 5){
				//	showExtInfoMsg("You can only upload 5 images.");
				//	return false;
				//}
	            if(frmUp.isValid()){
	            	frmUp.submit({
	                    url: '/upload/do_upload' 
	            		,method: 'POST'
	            		,params: {auth:_AUTH_KEY
	            				,'created_by': _USER_ID
	            				,'topic_id': Ext.getCmp('bulletinDetail').getForm().findField('bulletin[topic_id]').getValue()
	            				}
	                    ,waitMsg: 'Uploading your files...'
	                    ,success: function(form, action){//form, action
	                    	
	                		var Function = Ext.data.Record.create([{
	    	    	    	    name: 'uploaded_id'
	    	    	    	}, {
	    	    	    	    name: 'path'
	    	    	    	}, {
	    	    	    	    name: 'type'
	    	    	    	}, {
	    	    	    	    name: 'size'
	    	    	    	}]);
	    						pecaDataStores.fileStore.add(new Function({
	    		    	    	     path:action.result.path
	    		    	    	    ,type:action.result.type
	    		    	    	    ,size:action.result.size
	    		    	    	    ,uploaded_id:action.result.uploaded_id
	    		    	    	  
	    		    	    	}));
	    		    	    showExtInfoMsg(action.result.msg);
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
	    	Ext.getCmp('uploadFormWin').close();		
	        }
	    }]
	};
};

