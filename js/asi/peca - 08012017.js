Ext.BLANK_IMAGE_URL = '/js/ext/resources/images/default/s.gif';
Ext.layout.FormLayout.prototype.trackLabels = true;
//Ext.form.Field.prototype.validateOnBlur = false;
//Ext.form.Field.prototype.validationEvent = false;
Ext.form.DateField.prototype.style = 'text-align: right';
Ext.form.DateField.prototype.invalidText = 'This is not a valid date - it must be in the format MM/DD/YYYY';
Ext.QuickTips.init();

var myMask = new Ext.LoadMask(Ext.getBody(), {msg:"Please wait..."});
//Ext.Ajax.on('beforerequest', myMask.show, myMask);
//Ext.Ajax.on('requestcomplete', myMask.hide, myMask);
//Ext.Ajax.on('requestexception', myMask.hide, myMask);

Ext.override(Ext.form.HtmlEditor, {
    // private
    defaultValue: (Ext.isOpera || Ext.isIE6) ? ' ' : ' ',

    cleanHtml: function(html) {
        var dv = this.defaultValue;
    
        html = String(html);
        
        // if (html.length > 5) {
        if (Ext.isWebKit) { // strip safari nonsense
            html = html.replace(/\sclass="(?:Apple-style-span|khtml-block-placeholder)"/gi, '');
        }
        // }
        
        if (html.charCodeAt(0) == dv.replace(/\D/g, '')) {
            html = html.substring(2);
        }
        
        return html;
    }
});


var IdleScreen = function() {
	this.lastTime = "";
	this.confirmFunction = "screen_timer.redirectLogout();";
	this.saveFunction = "";
	this.isIdle = false;
	this.time_out_mins = 100;
	this.stillAlive = function(){
		var curr_date = new Date()
		this.lastTime = curr_date.getTime();
		this.isIdle = false;
	};
	this.init = function(){
		var curr_date = new Date()
		this.lastTime = curr_date.getTime();
		this.checkIdle();
	};
	this.checkIdle = function(){
		var curr_date = new Date()
		curr_date = curr_date.getTime();
		var diff = (curr_date - this.lastTime);
		if(diff > (1000*60*this.time_out_mins)){
			this.isIdle = true;
			try{
				if(eval(this.confirmFunction) == false){
					this.redirectLogout();
				}
			}catch(e){
				this.redirectLogout();
			}

		} else {
			var timer = (1000*60*this.time_out_mins) - diff;
			setTimeout("screen_timer.checkIdle()", timer);
		}
	};
	this.redirectLogout = function(){
	
		Ext.Ajax.request({
				url: '/login/logout' 
				,method: 'POST'
				,success: function(response, opts) {
					window.location = "login/index/true";								
				}
				,failure: function(response, opts) {
					window.location = "login/index/true";
				}
				,params: {user_id: _USER_ID, auth:""}
		});
	};
};

var screen_timer = new IdleScreen();

Ext.override(Ext.form.TextField, {
    validator:function(text){
        return (text.length==0 || Ext.util.Format.trim(text).length!=0);
    }
});
Ext.override(Ext.form.ComboBox, {
	resizable: true    
});



(function(){
    var initListExtJs = Ext.form.ComboBox.prototype.initList;
    Ext.override(Ext.form.ComboBox, {
        initList: function() {
            if(!this.tpl) {
                this.tpl = '<tpl for="."><div class="x-combo-list-item">{' + this.displayField + ':htmlEncode}</div></tpl>';
            }
            initListExtJs.call(this);
        }
    });
})();  

Ext.namespace('peca');

var peca = function() {

    this.init = function() {
		screen_timer.init();
        // create the layout for the page
        this.setupLayout();
		Ext.getBody().on("contextmenu", Ext.emptyFn, null, {preventDefault: true});
        // remove the page loading indicator
        setTimeout(function(){
            if(document.getElementById('pageloading')) {
                Ext.get('pageloading').fadeOut({
                    useDisplay:true,
                    callback:function() {
                        Ext.get('loading-mask').fadeOut({remove:true});
                    }
                });
            }
        },10);
    };
    
    Ext.form.Field.prototype.msgTarget = 'under';
    Ext.EventManager.onDocumentReady(this.init,this);
		
};

peca.prototype = {
	
	setupLayout : function() {
	
		var viewport = new Ext.Viewport({
	        id:'mainUI'
	        ,layout:'border'
	        ,items:[
				this.mainHeader() 
				,this.mainBody()
			]
		});
		
	}
	
	,mainHeader : function() {
		return {
			xtype:'panel'
	        ,region:'north'
	        ,titlebar:'false'
	        ,height: 60
            ,margins: '0 0 0 0'
	        ,html: headerTemplate
			,bodyStyle:{'font-family':'arial,verdana,sans-serif'}
		};
	},
	
	mainBody : function() {
		return {
			xtype:'tabpanel'
			,id: 'mainBody'
			,region:'center'
			,titlebar:false
			,activeTab:0
			,items:[this.homeTab()
				   ,this.transactionTab()
				   ,this.masterFilesTab()
			       ,this.reportsTab()
			       ,this.batchProcessTab()
				   ,this.utilitiesTab()
			       ,this.referenceTab()
			       ,this.securityTab()
				   ,this.onlineTab()
				   ,this.membershipInfoTab()
			       ]
			,listeners:{
				'tabchange':{
					scope:this
					,fn:function(tab, panel){
						/* var myMask = new Ext.LoadMask(Ext.getBody(), {msg:"Please wait..."});
						if (panel.title == 'Reports'){
							Ext.Ajax.on('beforerequest', myMask.hide, myMask);
							Ext.Ajax.on('requestcomplete', myMask.hide, myMask);
							Ext.Ajax.on('requestexception', myMask.hide, myMask);	
						}
						else {
							Ext.Ajax.on('beforerequest', myMask.show, myMask);
							Ext.Ajax.on('requestcomplete', myMask.hide, myMask);
							Ext.Ajax.on('requestexception', myMask.hide, myMask); */
							if (panel.title == 'Batch Process'){
								Ext.getCmp('protransList').getStore().load({params: {start:0, limit:MAX_PAGE_SIZE}});
							}
						//}
						
					}
				}
			}
		};
	}
	,securityTab : function() {
		return {
			xtype:'panel'
			,title:'Security'
			,iconCls:'securitytab'
			,layout:'border'
			,items:[
				this.securityNav()
				,this.securityCardBody()
			]
		};
	}
	,utilitiesTab : function() {
		return {
			xtype:'panel'
			,title:'Utilities'
			,iconCls:'utilitiestab'
			,layout:'border'
			,items:[
				this.utilitiesNav()
				,this.utilitiesCardBody()
			]
		};
	}
	,homeTab : function() {
		return {
			xtype:'panel'
			,title:'Home'
			,iconCls:'hometab'
			,layout:'border'
			,items:[
				this.homeCardBody()
			]
		};
	}
	,transactionTab : function() {
		return {
			xtype:'panel'
			,title:'Transaction'
			,iconCls:'transactiontab'
			,layout:'border'
			,items:[
				this.transactionNav()
				,this.transactionCardBody()
			]
		};
	}
	,securityNav : function(){
		return {
			xtype: 'treepanel'
			,title: 'Security'
			,id: 'securityNav'
			,titlebar:false
			,region:'west'
			,width:235
			,rootVisible:false
			,collapsible:true
			,lines:false
			,root: {
		        text: '',
		        children:[
					{text:'User',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlUsers'}
					,{text:'Group',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlGroup'}
					,{text:'Permission',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlPermissions'}
					,{text:'Change Password',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlChangePassword'}
					//,{text:'Bulletin',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlBulletinBoard'}
				]
			}
			,listeners:{
				'click':{
					scope:this
					,fn:function(node,e) {
						Ext.getCmp('securityCardBody').layout.setActiveItem(node.attributes.activatepanel);
						switch(node.attributes.activatepanel) {
							case "pnlUsers" :
								if (Ext.getCmp('userDetail').getForm().findField('frm_mode').getValue() != FORM_MODE_LIST){
									Ext.getCmp('securityCardBody').layout.setActiveItem('pnlUserDetail');
								}
								break;
							case "pnlGroup" :
								//Ext.getCmp('securityCardBody').layout.setActiveItem('pnlGroup');
								break;
							case "pnlPermissions" :
								//Ext.getCmp('securityCardBody').layout.setActiveItem('pnlPermissions');
								break;
							case "pnlChangePassword" :
								//Ext.getCmp('securityCardBody').layout.setActiveItem('pnlChangePassword');
								break;
							//case "pnlBulletinBoard" :
								//Ext.getCmp('securityCardBody').layout.setActiveItem('pnlBulletinBoard');
							//	break;
								
						}
					}
				}
			}
		};
	}
	
	,utilitiesNav : function(){
		return {
			xtype: 'treepanel'
			,title: 'Utilities'
			,collapsible:true	
			,id: 'utilitiesNav'
			,region: 'west'
			,width: 235
			,rootVisible:false
			,lines:false
		    ,root: {
		        text: '',
		        children:[
					{text:'Download Bank Transfer Files',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlDLBankTransfer'}
					,{text:'Download Payroll Deductions',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlDLPayrollDeductions'}
					,{text:'Download AMLA Covered Transactions',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlDLAmla'}
					,{text:'Database Archiving',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlDBArchiving'}
					,{text:'Batch OR Printing',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlBatchORPrinting'}
					,{text:'Back Up Database',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlBackupDB'}
					,{text:'Restore Database',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlRestoreDB'}
					,{text:'Online and Server Replication',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlReplicate'}
				]
			}
			,listeners:{
				"click":{
					scope:this
					,fn:function(node,e) {
						Ext.getCmp('utilitiesCardBody').layout.setActiveItem(node.attributes.activatepanel);
						switch(node.attributes.activatepanel) {
							case "pnlDLBankTransfer":
								loadForm('utilBankTransfer','/utilities');
								break;
							case "pnlDLPayrollDeductions":
								loadForm('utilPayrollDeduction','/utilities');
								break;	
							case "pnlDLAmla":
								loadForm('utilAmlaCovered','/utilities');
								break;
							case "pnlDBArchiving":
								break;
							case "pnlBatchORPrinting":
								break;
							case "pnlBackupDB":
								break;
							case "pnlRestoreDB":
								break;
							case "pnlReplicate":
								break;
						}						
					}
				}	
			}
		};
	}
	
	,transactionNav : function(){
		return {
			xtype: 'treepanel'
			,title: 'Transaction'
			,collapsible:true	
			,id: 'transactionNav'
			,region: 'west'
			,width: 200
			,rootVisible:false
			,lines:false
		    ,root: {
		        text: '',
		        children:[
					{text:'Capital Contribution',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlCapcon'}
					,{text:'ISOP',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlISOP'}
					,{text:'Loan',iconCls:'menuItemIcon',expanded:true
						,children:[
      						{text:'New Loans',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlLoan'},
    						{text:'Loan Payments',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlLoanPayments'}
    					]}
					,{text:'Payroll Deductions',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlPayrollDeductions'}
					,{text:'Adjustments',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlAdjustments'}
					,{text:'Investments',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlInvestments'}
					,{text:'Investment Maturity',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlInvMaturity'}
					,{text:'Journal Entries',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlJournalEnt'}
				]
			}
			,listeners:{
				"click":{
					scope:this
					,fn:function(node,e) {
						Ext.getCmp('transactionCardBody').layout.setActiveItem(node.attributes.activatepanel);
						switch(node.attributes.activatepanel) {
							case "pnlCapcon":
								if (Ext.getCmp('capconDetail').getForm().findField('frm_mode').getValue() != FORM_MODE_LIST){
									Ext.getCmp('transactionCardBody').layout.setActiveItem('pnlCapconDetail');
								}
								break;
							case "pnlISOP":
								break;
							case "pnlLoan":
								break;
							case "pnlLoanPayments":
								break;
							case "pnlPayrollDeductions":
								break;
							case "pnlAdjustments":
								pecaDataStores.tgStore.load();
								break;
							case "pnlInvestments":
								break;
							case "pnlInvMaturity":
								break;
							case "pnlJournalEnt":
								break;
						}						
					}
				}	
			}
		};
	}
	,
	securityCardBody: function(){
		return {
			xtype:'panel'
			,id: 'securityCardBody'
			,region:'center'
			,titlebar:false
			,activeItem:0
			,enableTabScroll: true
			,layout: 'card'
			,autoScroll: true	
			,layoutConfig: {
				  deferredRender:true
				  ,layoutOnCardChange:true
				}
			,items:[
				{xtype:'panel',id:'pnlUsers',title:'Users',width:1100
					,layout: 'border'
					,items: [userList()]
				},
				{xtype:'panel',id:'pnlUserDetail',title:'Users',width:1100
					,layout: 'border'
					,items: [userDetail()]
				},
				{xtype:'panel',id:'pnlGroup',title:'Group',width:1100
					,layout: 'border'
					,items: [groupList(), groupDetail()]
				},
				{xtype:'panel',id:'pnlPermissions',title:'Permissions',width:1100
					,layout: 'border'
					,items: [permissionsDetail()]
				},
				{xtype:'panel',id:'pnlChangePassword',title:'Change Password',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border'
					,items: [changepasswordDetail()]
				}
			]
		};
	}
	, homeCardBody: function(){
		return {
			xtype:'panel'
			,id: 'homeCardBody'
			,region:'center'
			,titlebar:false
			,activeItem:0
			,enableTabScroll: true
			,layout: 'card'
			,autoScroll: true	
			,layoutConfig: {
				  deferredRender: false
				  ,layoutOnCardChange:true
				}
			,items:[
				{xtype:'panel',id:'pnlBulletinBoard',title:'Bulletin',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [panel(), panelImptDocs()]
				}
				,{xtype:'panel',id:'pnlBulletinBoardDetail',title:'Bulletin',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [bulletinDetail()]
				}
			]
		};
	}
	
	,utilitiesCardBody: function(){
		return {
			xtype:'panel'
			,id: 'utilitiesCardBody'
			,region:'center'
			,titlebar:false
			,activeItem:0
			,layout: 'card'
			,autoScroll: true	
			,layoutConfig: {
				  deferredRender: true
				  ,layoutOnCardChange: true
				}
			,items:[
				{xtype:'panel',id:'pnlDLBankTransfer',title:'Download Bank Transfer Files',width:1100			
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [utilBankTransfer()]
				}
				,{xtype:'panel',id:'pnlDLPayrollDeductions',title:'Download Payroll Deductions',width:1100			
					,layout: 'border', defaults: {autoScroll: true}
					,items: [utilPayrollDeduction()]
				}
				,{xtype:'panel',id:'pnlDLAmla',title:'Download AMLA Covered Transactions',width:1100			
					,layout: 'border', defaults: {autoScroll: true}
					,items: [utilAmlaCovered()]
				}
				,{xtype:'panel',id:'pnlDBArchiving',title:'Database Archiving',width:1100			
					,layout: 'border', defaults: {autoScroll: true}
					,items: [utilArchive()]
				}
				,{xtype:'panel',id:'pnlBatchORPrinting',title:'Batch OR Printing',width:1100			
					,layout: 'border', defaults: {autoScroll: true}
					,items: [utilBatchOR()]
				}
				,{xtype:'panel',id:'pnlBackupDB',title:'Back Up Database',width:1100			
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [utilBackupDB()]
				}
				,{xtype:'panel',id:'pnlRestoreDB',title:'Restore Database',width:1100			
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [utilRestoreDB()]
				}
				,{xtype:'panel',id:'pnlReplicate',title:'Online and Server Replication',width:1100			
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [utilReplicate()]
				}
			]
		};
	}
	
	,transactionCardBody: function(){
		return {
			xtype:'panel'
			,id: 'transactionCardBody'
			,region:'center'
			,titlebar:false
			,activeItem:0
			,layout: 'card'
			,autoScroll: true	
			,layoutConfig: {
				  deferredRender: true
				}
			,items:[
				{xtype:'panel',id:'pnlCapcon',title:'Capital Contribution',width:1100			
//					,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [capconList()]
				}
				,{xtype:'panel',id:'pnlCapconDetail',title:'Capital Contribution',width:1100			
//					,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [capconDetail()]
				}
				,{xtype:'panel',id:'pnlISOP',title:'ISOP'
//					,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border'
					,items: [isopList(), isopDetail()]
				}
				,{xtype:'panel',id:'pnlLoan',title:'Loan',width:1100			
//					,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [newloanList(), newloanDetail()]
				}
				,{xtype:'panel',id:'pnlLoanPayments',title:'Loan Payments',width:1100
//					,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', autoscroll: true, defaults: {autoScroll: true} 
					,items: [ lpList(),lpDetail()]
				}
				,{xtype:'panel',id:'pnlPayrollDeductions',title:'Payroll Deductions',width:1100
//					,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border'
					,items: [pdList(),pdDetail()]
				}
				
				,{xtype:'panel',id:'pnlAdjustments',title:'Adjustments',width:1100
//					,html:'<div class="placeholder">Under construction</div>'
						,layout: 'border', autoscroll: true, defaults: {autoScroll: true} 
						,items: [adjustment()]
				}
				,{xtype:'panel',id:'pnlInvestments',title:'Investments'
				//,html:'<div class="placeholder">Under construction</div>'
					,width:1100
					,layout: 'border'
					,items: [investmentList(),investmentDetail()]
				}
				,{xtype:'panel',id:'pnlInvMaturity',title:'Investment Maturity'
				//,html:'<div class="placeholder">Under construction</div>'
					,width:1100
					,layout: 'border'
					,items: [imList(),imDetail()]
				}
				,{xtype:'panel',id:'pnlJournalEnt',title:'Journal Entries'
					//,html:'<div class="placeholder">Under construction</div>'
					,width:1100
					,layout: 'border'
					,items: [journalList(),journalDetail()]
				}
			]
		};
	}
	
	,referenceTab : function() {
		return {
			xtype:'panel'
			,title:'Reference'
			,iconCls:'referencetab'
			,layout:'border'
			,items:[
				this.referenceNav()
				,this.referenceCardBody()
			]
		};
	}
	
	,referenceNav : function(){
		return {
			xtype: 'treepanel'
			,title: 'Reference'
			,collapsible:true	
			,id: 'referenceNav'
			,region: 'west'
			,width: 200
			,rootVisible:false
			,lines:false
		    ,root: {
		        text: '',
		        children:[
					{text:'Transaction Codes',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlTranscode'}
					,{text:'Chart of Accounts',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlCOA'}
					,{text:'Company',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlCompany'}
					,{text:'GL Entries',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlGL'}
					,{text:'Information Code',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlInfoCode'}
					,{text:'Loan Code',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlLoanCode'}
					,{text:'Transaction Charges',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlTransCharge'}
					,{text:'System Parameters',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlSysParam'}
					,{text:'Approval Workflow',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlAppWF'}
					
				]
			}
			,listeners:{
				"click":{
					scope:this
					,fn:function(node,e) {
						Ext.getCmp('referenceCardBody').layout.setActiveItem(node.attributes.activatepanel);
						switch(node.attributes.activatepanel) {
							case "pnlTranscode":
								if (Ext.getCmp('transcodeDetail').getForm().findField('frm_mode').getValue() != FORM_MODE_LIST){
									Ext.getCmp('referenceCardBody').layout.setActiveItem('pnlTranscodeDetail');
								}
								break;
							case "pnlCOA":
								break;	
							case "pnlCompany":
								break;
							case "pnlGL":
								break;
							case "pnlInfoCode":
								break;
							case "pnlLoanCode":
								break;
							case "pnlTransCharge":
								pecaDataStores.transcodeChargesStore.load();
								pecaDataStores.transcodeChargesStore2.load();
								break;
							case "pnlSysParam":
								break;
							case "pnlAppWF":
								break;
						}						
					}
				}	
			}
		};
	}
	
	,referenceCardBody: function(){
		return {
			xtype:'panel'
			,id: 'referenceCardBody'
			,region:'center'
			,titlebar:false
			,activeItem:0
			,layout: 'card'
			,autoScroll: true	
			,layoutConfig: {
				  deferredRender: true
				  ,layoutOnCardChange: true
				}
			,items:[
				{xtype:'panel',id:'pnlTranscode',title:'Transaction Code',width:1100			
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border'
					,items: [transcodeList()]
				}
				,{xtype:'panel',id:'pnlTranscodeDetail',title:'Transaction Code',width:1100			
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border'
					,items: [transcodeDetail()]
				}
				,{xtype:'panel',id:'pnlCOA',title:'Chart of Accounts',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border'
					,items: [coaList(), coaDetail()]
				}
				,{xtype:'panel',id:'pnlCompany',title:'Company',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border'
					,items: [companyList(), companyDetail()]
				}
				,{xtype:'panel',id:'pnlGL',title:'GL Entries',width:1100,autoScroll: true
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border'
					,items: [glList(), glDetail()]
				}
				,{xtype:'panel',id:'pnlInfoCode',title:'Information Code',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', autoScroll: true
					,items: [infocodeList(), infocodeDetail()]
				}
				,{xtype:'panel',id:'pnlLoanCode',title:'Loan Code',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', autoscroll: true, defaults: {autoScroll: true} 
					,items: [loancodeList(), loancodeDetail()]
				}
				,{xtype:'panel',id:'pnlTransCharge',title:'Transaction Charges',width: 1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border'
					,items: [transchargeList(), transchargeDetail()]
				}
				,{xtype:'panel',id:'pnlSysParam',title:'System Parameters',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border'
					,items: [sysparamsList(), sysparamsDetail()]	
				}
				,{xtype:'panel',id:'pnlAppWF',title:'Approval Workflow'
					,width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border'
					,items: [workflowList(),workflowDetail()]
				}
			]
		};
	}
	
	,batchProcessTab : function() {
		return {
			xtype:'panel'
			,title:'Batch Process'
			,iconCls:'batchtab'
			,layout:'border'
			,items:[
				this.batchProcessNav()
				,this.batchProcessBody()
			]
		};
	}
	
	,batchProcessNav : function(){
		return {
			xtype: 'treepanel'
			,title: 'Batch Process'
			,collapsible:true	
			,id: 'batchProcessNav'
			,region: 'west'
			,width: 200
			,rootVisible:false
			,lines:false
		    ,root: {
		        text: '',
		        children:[
					{text:'Process Transactions',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlProcessTran'}
					,{text:'Process ISOP Deductions',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlIsop'}
					,{text:'Process Payroll Deductions',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlPD'}
					,{text:'Process Loan Payments',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlLoanPayment'}
					,{text:'Process Loan Year Term',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlLoanYear'}
					,{text:'Dividend Processing',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlDiv'}
					,{text:'Dormant Account Processing',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlDormant'}
					,{text:'Post Transactions',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlPostTran'}
					,{text:'Month End Processing',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlMonthEnd'}
					
				]
			}
			,listeners:{
				"click":{
					scope:this
					,fn:function(node,e) {
						Ext.getCmp('batchProcessCardBody').layout.setActiveItem(node.attributes.activatepanel);
						switch(node.attributes.activatepanel) {
						case "pnlProcessTran":
							loadForm('processTransaction','/process_transaction');
							Ext.getCmp('protransList').getStore().load({params: {start:0, limit:MAX_PAGE_SIZE}});
							break;
							case "pnlIsop":
//								setFieldValue('/isop', Ext.getCmp('processIsop').getForm(), 'process_isop[current_date]');
								loadForm('processIsop','/process_isop');
								break;	
							case "pnlPD":
								loadForm('processPD','/process_payroll_deduction');
								break;
							case "pnlLoanPayment":
								loadForm('processLP','/process_loan_payment');
								pecaDataStores.companyStoreBP.load();
								break;
							case "pnlLoanYear":
								loadForm('processLoanYear','/loan_year');
								break;
							case "pnlDiv":
								loadForm('processDividend','/dividend');
								pecaDataStores.divStore.load();
								break;
							case "pnlDormant":
//								setFieldValue('/dormant_account', Ext.getCmp('processDormant').getForm(), 'dormant_account[current_date]');
								loadForm('processDormant','/dormant_account');
								break;
							case "pnlPostTran":
								loadForm('postTransaction','/post_transaction');
								Ext.getCmp('posttransList').getStore().load({params: {start:0, limit:MAX_PAGE_SIZE}});
								break;
							case "pnlMonthEnd":
								loadForm('processMonthend','/month_end');
								break;
						}						
					}
				}	
			}
		};
	}
	
	,batchProcessBody: function(){
		return {
			xtype:'panel'
			,id: 'batchProcessCardBody'
			,region:'center'
			,titlebar:false
			,activeItem:0
			,layout: 'card'
			,autoScroll: true	
			,layoutConfig: {
				  deferredRender: false
				}
			,items:[
				{xtype:'panel',id:'pnlProcessTran',title:'Process Transactions'	
//					,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border'
					,items: [processTransaction()]
				},
				{xtype:'panel',id:'pnlIsop',title:'Process ISOP Deductions'
//					,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border'
					,items: [processIsop()]
				},
				{xtype:'panel',id:'pnlPD',title:'Process Payroll Deductions'
//					,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border'
					,items: [processPD()]
				},
				{xtype:'panel',id:'pnlLoanPayment',title:'Process Loan Payments'
//					,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border'
					,items: [processLoanPayment()]
				},
				{xtype:'panel',id:'pnlLoanYear',title:'Process Loan Year Term'
//					,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border'
					,items: [processLoanYear()]
				},
				{xtype:'panel',id:'pnlDiv',title:'Dividend Processing'
//					,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border'
					,items: [processDividend()]
				},
				{xtype:'panel',id:'pnlDormant',title:'Dormant Account Processing'
//					,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border'
					,items: [processDormant()]
				},
				{xtype:'panel',id:'pnlPostTran',title:'Post Transactions'
//					,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border'
					,items: [postTransaction()]
				},
				{xtype:'panel',id:'pnlMonthEnd',title:'Month End Processing'
					,layout: 'border'
					,items: [processMonthend()]}
			]
		};
	}
	,reportsTab : function() {
		return {
			xtype:'panel'
			,title:'Reports'
			,iconCls:'reportstab'
			,layout:'border'
			,items:[
				this.reportsNav()
				,this.reportsCardBody()
			]
		};
	}
	
	,reportsNav : function(){
		return {
			xtype: 'treepanel'
			,title: 'Reports'
			,collapsible:true	
			,id: 'reportsNav'
			,region: 'west'
			,autoScroll: true
			,width: 200
			,rootVisible:false
			,lines:false
		    ,root: {
		        text:'',
		        children:[
					{text:'Transactions',iconCls:'menuItemIcon',expanded:false
						,children:[
						       {text:'Capital Contribution',iconCls:'menuItemIcon',expanded:false
									,children:[
			      						{text:'Prooflist',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlcapconProofList'}
			      						,{text:'Audit Trail',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlcapconAuditTrail'}
			      						,{text:'BMB Audit Trail',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlcapconBMBAuditTrail'}
			    					]},
			    				{text:'ISOP Deductions',iconCls:'menuItemIcon',expanded:false
			        				,children:[
			              				{text:'Prooflist',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlisopProofList'}
			              				,{text:'Audit Trail',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlisopAuditTrail'}
			            			]},
			            		{text:'Payroll Deductions',iconCls:'menuItemIcon',expanded:false
			            			,children:[
			                  			{text:'Prooflist',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlpayrollProofList'}
			                  			,{text:'Audit Trail',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlpayrollAuditTrail'}
			                		]},
			                	{text:'Investments',iconCls:'menuItemIcon',expanded:false
			                		,children:[
			                      		{text:'Prooflist',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlinvProofList'}
			                      		,{text:'Audit Trail',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlinvAuditTrail'}
			                      		,{text:'Report',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlinvReport'}
			                      		,{text:'Maturity Prooflist',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlinvMatProoflist'}
			                    	]},
			                    {text:'Journal Entries',iconCls:'menuItemIcon',expanded:false
			                    	,children:[
			                        	{text:'Prooflist',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnljournalProofList'}
			                          	,{text:'Audit Trail',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnljournalAuditTrail'}
			                        ]},
			                    {text:'Transaction Totals',iconCls:'menuItemIcon',expanded:false
			                        ,children:[
			                          	{text:'By Company',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlTCC'}
			                           	,{text:'By Employee',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlTCE'}
			                           	,{text:'By Transaction',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlTCT'}
			                        ]},
			                    {text:'Daily Summary of Disbursements',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlsumDisbursement'},
			                    {text:'Daily Summary of Adjustments',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlsumAdjustment'},
			                    {text:'Daily Summary of Collections',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlsumCollection'},
			                    {text:'Disbursement Voucher',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnldisVoucher'},
			                    {text:'Dormant Account',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnldormantAccount'}
			                    
						]},
					{text:'Master Files',iconCls:'menuItemIcon',expanded:false
						,children:[
							{text:'List of Comakers for Review',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlguarantorlist'}
							,{text:'Members Master List',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlmemberlist'}	 
						]	
					},
					{text:'Reference',iconCls:'menuItemIcon',expanded:false
						,children:[
							{text:'Chart of Accounts',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlaccount'}
							,{text:'Company Summary',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlcompany'}
							,{text:'GL Entry Summary',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlglentry'}	
							,{text:'Information Code Summary',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlinformation'}	
							,{text:'Loan Code Summary',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlloancode'}	
							,{text:'System Parameters Summary',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlparameter'}	
							,{text:'Transaction Charges Summary',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnltrancharge'}
							,{text:'Transaction Codes Reference',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnltrancode'}
						]	
					},
					{text:'Loans',iconCls:'menuItemIcon',expanded:false
						,children:[
							{text:'Loan Payments',iconCls:'menuItemIcon',expanded:false
			                        ,children:[
			                          	{text:'Prooflist',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlLPP'}
			                           	,{text:'Audit Trail',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlLPA'}
			                        ]}
							,{text:'Loan Applications List',iconCls:'menuItemIcon',expanded:false
			                        ,children:[
			                          	{text:'Prooflist',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlLAP'}
			                           	,{text:'Audit Trail',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlLAA'}
			                        ]}
							//[START] Modified by Vincent Sy for 8th Enhancement 2013/07/05
							,{text:'P&G Subsidy for Housing Loans',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlsubsidy'}
							//[END] Modified by Vincent Sy for 8th Enhancement 2013/07/05
							,{text:'Loans Year Term Prooflist',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlloanyearterm'}
							,{text:'Amortization of Unearned Interest',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlamortunearned'}	
							,{text:'List Of Interest Earned',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlinterestearned'}	
							,{text:'Loan Payment Due Report',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnldue'}	
							,{text:'Mini Loan Due Report',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlmnildue'}	
							,{text:'Mini Loan Penalty Report (PENT)',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlpenalty'}	
							,{text:'Mini Loan Suspension and Penalty Report (NPML)',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlsuspendedborrowers'}	
							,{text:'MRI-FIP Suspended List',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlmrifipsuspended'}	
							,{text:'Invalid Co-maker Suspended List',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlinvalidcomakers'}	
							,{text:'Comaker Exception Report',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlguarantorexception'}
							,{text:'Aging of Loans',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlaging'}
							//[START] 6th Enhancement
							,{text:'Mini Loan Penalty Audit Report (PENT)',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlmnilpenaltyaudit'}
							,{text:'Non Payment Mini Loan Penalty Audit Trail',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlnpmlaudit'}
							//[END] 6th Enhancement
						]	
					},
					{text:'Capital Contribution',iconCls:'menuItemIcon',expanded:false
						,children:[
							{text:'Statement of Accounts',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlSOA'}
							,{text:'Outstanding Status Report',iconCls:'menuItemIcon',expanded:false
			                        ,children:[
			                          	{text:'Employee',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlOSREmployee'}
			                           	,{text:'Company',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlOSRCompany'}
			                        ]}
							,{text:'Dividend Report',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlDividend'}
							,{text:'Summary',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlSummary'}
							,{text:'Maximum Balance',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlMaximumBalance'}	
							,{text:'Capcon Balance List',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlCapconBalanceList'}	
							,{text:'Dormant Account List',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlDormantAccountList'}
						]	
					}
					,{text:'Financial Reports',iconCls:'menuItemIcon',expanded:true
						,children:[
							{text:'Comparative Balance Sheet',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlcomparativebalsheetrpt'}
							,{text:'Comparative Income Statement',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlcomparativeincomeStatementrpt'}
							,{text:'Consolidated Statement of Condition',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlconsolidatedbalsheetrpt'}
							,{text:'Consolidated Statement of Income and Expenses',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlconsolidatedincomeStatementrpt'}
							,{text:'Trial Balance',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnltrialrpt'}
							,{text:'Account Summary',iconCls:'menuItemIcon',expanded:false
			                        ,children:[
			                          	{text:'Posted',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlacctpostedrpt'}
			                           	,{text:'Unposted',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlacctunpostedrpt'}
			                        ]}
							,{text:'Deleted Transactions',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlDeletedTransactions'}	
							,{text:'Future Dated Journal Entries',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlFutureDatedJournal'}	
						]	
					}
				]
			}
			,listeners:{
				"click":{
					scope:this
					,fn:function(node,e) {
						Ext.getCmp('reportsCardBody').layout.setActiveItem(node.attributes.activatepanel);
						switch(node.attributes.activatepanel) {
							case "pnlcapconProofList":
								break;
							case "pnlcapconAuditTrail":
								break;
							case "pnlcapconBMBAuditTrail":
								break;
							case "pnlisopProofList":
								break;	
							case "pnlisopAuditTrail":
								break;
							case "pnlpayrollProofList":
								break;	
							case "pnlpayrollAuditTrail":
								break;
							case "pnlinvProofList":
								break;
							case "pnlinvAuditTrail":
								break;	
							case "pnlinvReport":
								break;
							case "pnlinvMatProoflist":
								break;
							case "pnljournalProofList":
								break;	
							case "pnljournalAuditTrail":
								break;	
							case "pnlTCC":
								break;	
							case "pnlTCE":
								break;	
							case "pnlTCT":
								break;	
							case "pnlsumDisbursement":
								break;	
							case "pnlsumAdjustment":
								break;	
							case "pnlsumCollection":
								break;	
							case "pnldisVoucher":
								break;	
							case "pnldormantAccount":
								break;	
							case "pnlmemberlist":
								break;	
							case "pnlguarantorlist":
								break;	
							case "pnlaccount":
								break;	
							case "pnlcompany":
								break;	
							case "pnlglentry":
								break;	
							case "pnlinformation":
								break;	
							case "pnlloancode":
								break;	
							case "pnlparameter":
								break;	
							case "pnltrancharge":
								break;
							case "pnltrancode":
								break;
							case "pnlLPP":
								break;	
							case "pnlLPA":
								break;	
							case "pnlLAP":
								break;	
							case "pnlLAA":
								break;	
							case "pnlsubsidy":
								break;	
							case "pnlloanyearterm":
								break;	
							case "pnlamortunearned":
								break;	
							case "pnlinterestearned":
								break;	
							case "pnlpenalty":
								break;
							case "pnlsuspendedborrowers":
								break;
							case "pnlmrifipsuspended":
								break;
							case "pnlinvalidcomakers":
								break;
							case "pnlguarantorexception":
								break;	
							case "pnlaging":
								break;
							//[START] 6th Enhancement
							case "pnlmnilpenaltyaudit":
								break;	
							case "pnlnpmlaudit":
								break; 		
							//[END] 6th Enhancement
							case "pnlSOA":
								pecaDataStores.companyStoreSOA.load();
								Ext.getCmp('rpt_csoa').getForm().findField('employee_id').setDisabled(false);
								Ext.getCmp('rpt_csoa').getForm().findField('last_name').setDisabled(false);
								Ext.getCmp('rpt_csoa').getForm().findField('first_name').setDisabled(false);
								Ext.getCmp('rpt_csoa').getForm().findField('middle_name').setDisabled(false);
								Ext.getCmp('search_report_statement').setDisabled(false);
								
								Ext.getCmp('rpt_csoa').getForm().findField('company_code').setDisabled(true);
								
								Ext.getCmp('rpt_csoa').getForm().findField('from_trans_date').setDisabled(true);
								Ext.getCmp('rpt_csoa').getForm().findField('to_trans_date').setDisabled(true);
								
								Ext.getCmp('rpt_csoa').getForm().findField('report_type').setValue(true);
								
								break;	
							case "pnlOSREmployee":
								break;	
							case "pnlOSRCompany":
								break;	
							case "pnlDividend":
								Ext.Ajax.request({
									url: 'common/getLastDivProcessingDate' 
									,method: 'POST'
									,params: {auth:_AUTH_KEY}
									,success: function(response, opts) {
											var resp = Ext.decode(response.responseText).data[0];
											Ext.getCmp('rpt_dividend').getForm().findField('report_date').setValue(resp.accounting_period);
									}
								});
								break;	
							case "pnlSummary":
								break;	
							case "pnlMaximumBalance":
								break;
							case "pnlCapconBalanceList":
								Ext.Ajax.request({
									url: 'common/getAccountingPeriod' 
									,method: 'POST'
									,params: {auth:_AUTH_KEY}
									,success: function(response, opts) {
											var resp = Ext.decode(response.responseText).data[0];
											Ext.getCmp('rpt_capconbal').getForm().findField('report_date').setValue(resp.accounting_period);
									}
								});
								break;
							case "pnlDormantAccountList":
								break;	
							case "pnlconsolidatedbalsheetrpt":
								break;	
							case "pnlconsolidatedincomeStatementrpt":
								break;
							case "pnlcomparativebalsheetrpt":
								break;	
							case "pnlcomparativeincomeStatementrpt":
								break;	
							case "pnltrialrpt":
								break;	
							case "pnlacctpostedrpt":
								pecaDataStores.accountStoreASR.load();
								break;	
							case "pnlacctunpostedrpt":
								pecaDataStores.accountStoreASR.load();
								break;
							case "pnlDeletedTransactions":
								break;
							case "pnlFutureDatedJournal":
								break;
							case "pnldue":
								break;	
							case "mnilpnldue":
								break;
						}						
					}
				}				
			}
		};
	}
	
	,reportsCardBody: function(){
		return {
			xtype:'panel'
			,id: 'reportsCardBody'
			,region:'center'
			,titlebar:false
			,activeItem:0
			,layout: 'card'
			,autoScroll: true	
			,layoutConfig: {
				  deferredRender: true
				}
			,items:[
			    //-----transaction
				{xtype:'panel',id:'pnlcapconProofList',title:'Capital Contribution Prooflist',width:1100			
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_capcon_prooflist()]
				}
				,{xtype:'panel',id:'pnlcapconAuditTrail',title:'Capital Contribution Audit Trail Report',width:1100
					//,html:'<div class="placeholder">Under construction</div>'}
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_capcon_audit_trail()]
				}
				,{xtype:'panel',id:'pnlcapconBMBAuditTrail',title:'Capital Contribution BMB Report',width:1100
					//,html:'<div class="placeholder">Under construction</div>'}
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_capcon_bmb()]
				}
				,{xtype:'panel',id:'pnlisopProofList',title:'ISOP Deduction Prooflist Record',width:1100
					//,html:'<div class="placeholder">Under construction</div>'}
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_isop_prooflist()]
				}
				,{xtype:'panel',id:'pnlisopAuditTrail',title:'ISOP Deduction Audit Trail Report',width:1100
					//,html:'<div class="placeholder">Under construction</div>'}
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_isop_audit_trail()]
				}
				,{xtype:'panel',id:'pnlpayrollProofList',title:'Payroll Deduction Prooflist Report',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_pd_prooflist()]
				}
				,{xtype:'panel',id:'pnlpayrollAuditTrail',title:'Payroll Deduction Audit Trail Report',width:1100
					//,html:'<div class="placeholder">Under construction</div>'}
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_pd_audit_trail()]
				}
				,{xtype:'panel',id:'pnlinvProofList',title:'Investment Prooflist Record',width:1100
					//,html:'<div class="placeholder">Under construction</div>'}
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_inv_prooflist()]
				}
				,{xtype:'panel',id:'pnlinvAuditTrail',title:'Investment Audit Trail Report',width:1100
					//,html:'<div class="placeholder">Under construction</div>'}
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_inv_audit_trail()]
				}
				,{xtype:'panel',id:'pnlinvReport',title:'Investment Report',width:1100
					//,html:'<div class="placeholder">Under construction</div>'}
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_inv_report()]
				}
				,{xtype:'panel',id:'pnlinvMatProoflist',title:'Investment Maturity Prooflist Report',width:1100
					//,html:'<div class="placeholder">Under construction</div>'}
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_inv_mat_prooflist()]
				}
				,{xtype:'panel',id:'pnljournalProofList',title:'Journal Entries Prooflist Report',width:1100
					//,html:'<div class="placeholder">Under construction</div>'}
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_journal_prooflist()]
				}
				,{xtype:'panel',id:'pnljournalAuditTrail',title:'Journal Entries Audit Trail Report',width:1100
					//,html:'<div class="placeholder">Under construction</div>'}
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_journal_audit_trail()]
				}
				,{xtype:'panel',id:'pnlTCC',title:'Transactions Control Total by Company Report',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_TCC()]
				}
				,{xtype:'panel',id:'pnlTCE',title:'Transactions Control Total by Employee Report',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_TCE()]
				}
				,{xtype:'panel',id:'pnlTCT',title:'Transactions Control Total by Transaction Report',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_TCT()]
				}
				,{xtype:'panel',id:'pnlsumDisbursement',title:'Daily Summary of Disbursement Report',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_disbursement()]
				}
				,{xtype:'panel',id:'pnlsumAdjustment',title:'Daily Summary of Adjustment Report',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_adjustment()]
				}
				,{xtype:'panel',id:'pnlsumCollection',title:'Daily Summary of Collections Report',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_collection()]
				}
				,{xtype:'panel',id:'pnldisVoucher',title:'Disbursement Voucher',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_disvoucher()]
				}
				,{xtype:'panel',id:'pnldormantAccount',title:'Dormant Account Report',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_dormantaccount()]
				}
				//-----master
				,{xtype:'panel',id:'pnlmemberlist',title:'Members Masterlist Report',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_memberlist()]
				}
				,{xtype:'panel',id:'pnlguarantorlist',title:'List of Guarantors for Review',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_guarantorlist()]
				}
				//-----reference
				,{xtype:'panel',id:'pnlaccount',title:'Chart Of Accounts',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_chartofaccounts()]
				}
				,{xtype:'panel',id:'pnlcompany',title:'Company Summary',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_company()]
				}
				,{xtype:'panel',id:'pnlglentry',title:'GL Entry Summary',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_glentry()]
				}
				,{xtype:'panel',id:'pnlinformation',title:'Information Code Summary',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_information()]
				}
				,{xtype:'panel',id:'pnlloancode',title:'Loan Code Summary',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_loancode()]
				}
				,{xtype:'panel',id:'pnlparameter',title:'System Parameters Summary',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_param()]
				}
				,{xtype:'panel',id:'pnltrancharge',title:'Transaction Charges Summary',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_transactioncharges()]
				}
				,{xtype:'panel',id:'pnltrancode',title:'Transaction Codes Reference',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_transactioncodes()]
				}
				//------loans
				,{xtype:'panel',id:'pnlLPP',title:'Loan Payments Prooflist Report',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_lpp()]
				}
				,{xtype:'panel',id:'pnlLPA',title:'Loan Payments Audit Trail Report',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_lpa()]
				}
				,{xtype:'panel',id:'pnlLAP',title:'Loans Applications Prooflist',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_lap()]
				}
				,{xtype:'panel',id:'pnlLAA',title:'Loans Applications Audit Trail',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_laa()]
				}
				//[START] Modified by Vincent Sy for 8th Enhancement 2013/07/03
				,{xtype:'panel',id:'pnlsubsidy',title:'P&G Subsidy for Housing Loans',width:1100
				//[END] Modified by Vincent Sy for 8th Enhancement 2013/07/03
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_subsidy()]
				}
				,{xtype:'panel',id:'pnlloanyearterm',title:'Loans Year Term Prooflist',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_loanyearterm()]
				}
				,{xtype:'panel',id:'pnlamortunearned',title:'Amortization of Unearned Interest',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_amortunearnedinterest()]
				}
				,{xtype:'panel',id:'pnlinterestearned',title:'List of Interest Earned',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_interestearned()]
				}
				,{xtype:'panel',id:'pnlpenalty',title:'Mini Loan Penalty Report (PENT)',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_penalty()]
				}
				,{xtype:'panel',id:'pnldue',title:'Loan Payment Due Report',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_duereport()]
				}
				,{xtype:'panel',id:'pnlmnildue',title:'Mini Loan Due Report',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_mnildue()]
				}
				,{xtype:'panel',id:'pnlsuspendedborrowers',title:'Mini Loan Suspension and Penalty Report (NPML)',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_suspended()]
				}
				,{xtype:'panel',id:'pnlmrifipsuspended',title:'MRI-FIP Suspended List',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_mrifip_suspended()]
				}
				,{xtype:'panel',id:'pnlinvalidcomakers',title:'Invalid Co-maker Suspended List',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_suspended_comakers()]
				}
				,{xtype:'panel',id:'pnlguarantorexception',title:'Comaker Exception Report',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_comakerexception()]
				}
				,{xtype:'panel',id:'pnlaging',title:'Aging of Loans',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_aging()]
				}
				//[START] 6th Enhancement
				,{xtype:'panel',id:'pnlmnilpenaltyaudit',title:'Mini Loan Penalty Audit Report (PENT)',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_mnilpenalty_audit()]
				}
				,{xtype:'panel',id:'pnlnpmlaudit',title:'Non Payment Mini Loan Penalty Audit Trail',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_npml_audit()]
				}
				//[END] 6th Enhancement
				//------capital contribution
				,{xtype:'panel',id:'pnlSOA',title:'Statement of Accounts',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_csoa()]
				}
				,{xtype:'panel',id:'pnlOSREmployee',title:'Outstanding Status Report Employee',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_outstanding_employee()]
				}
				,{xtype:'panel',id:'pnlOSRCompany',title:'Outstanding Status Report Company',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_outstanding_company()]
				}
				,{xtype:'panel',id:'pnlDividend',title:'Dividend Report',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_dividend()]
				}
				,{xtype:'panel',id:'pnlSummary',title:'Capital Contribution per Month',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_summary()]
				}
				,{xtype:'panel',id:'pnlMaximumBalance',title:'Maximum Balance',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_maxbal()]
				}
				,{xtype:'panel',id:'pnlCapconBalanceList',title:'CapCon Balance List',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_capconbal()]
				}
				,{xtype:'panel',id:'pnlDormantAccountList',title:'Dormant Account List',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_dormant()]
				}
				//------financial reports
				,{xtype:'panel',id:'pnlconsolidatedbalsheetrpt',title:'Consolidated Statement of Condition',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_SOC()]
				}
				,{xtype:'panel',id:'pnlconsolidatedincomeStatementrpt',title:'Consolidated Statement of Income and Expenses',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_SOI()]
				}
				,{xtype:'panel',id:'pnlcomparativebalsheetrpt',title:'Comparative Balance Sheet',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_CSOC()]
					//,items: [rpt_CSOC1()]
				}
				,{xtype:'panel',id:'pnlcomparativeincomeStatementrpt',title:'Comparative Income Statement',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_CSOI()]
				}
				,{xtype:'panel',id:'pnltrialrpt',title:'Trial Balance Report for the Current Period',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_trialbalance()]
				}
				,{xtype:'panel',id:'pnlacctpostedrpt',title:'Posted Account Summary Report for the Current Period',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_actsummaryposted()]
				}
				,{xtype:'panel',id:'pnlacctunpostedrpt',title:'Unposted Account Summary Report for the Current Period',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_actsummaryunposted()]
				}
				,{xtype:'panel',id:'pnlDeletedTransactions',title:'Deleted Transactions Report',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_deletedtransaction()]
				}
				,{xtype:'panel',id:'pnlFutureDatedJournal',title:'Future Dated Unposted Journal Entries Report',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_fduj()]
				}
			]
		};
	}
	,masterFilesTab : function() {
		return {
			xtype:'panel'
			,title:'Master Files'
			,iconCls:'mastertab'
			,layout:'border'
			,items:[
				this.masterFilesNav()
				,this.masterFilesCardBody()
			]
		};
	}
	

	,onlineTab : function() {
		return {
			xtype:'panel'
			,title:'Online Transactions'
			,iconCls:'onlinetab'
			,layout:'border'
			,items:[
				this.onlineNav()
				,this.onlineCardBody()
			]
		};
	}
	,membershipInfoTab : function() {
		return {
			xtype:'panel'
			,title:'Membership Information'
			,iconCls:'onlinetab'
			,layout:'border'
			,items:[
				this.membershipInfoCardBody()
			]
		};
	}
	,onlineNav : function(){
		return {
			xtype: 'treepanel'
			,title: 'Online Transactions'
			,collapsible:true	
			,id: 'onlineNav'
			,region: 'west'
			,width: 200
			,rootVisible:false
			,lines:false
		    ,root: {
		        text: '',
		        children:[
					{text:'Membership Information',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlOLMemInfo'}
					,{text:'Apply Loan',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlOLLoan'}
					,{text:'Pay Loan',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlOnlineLoanPayment'}
					,{text:'Capital Contribution',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlOLWithdrawal'}
					,{text:'Payroll Deductions',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlOnlinePayrollDeduction'}
					//,{text:'Process Online',leaf:true,iconCls:'menuItemIcon',activatepanel:'processOnline'}
				]
			}
			,listeners:{
				"click":{
					scope:this
					,fn:function(node,e) {
						Ext.getCmp('onlineCardBody').layout.setActiveItem(node.attributes.activatepanel);
						switch(node.attributes.activatepanel) {
							case "pnlOLMemInfo":
								if (Ext.getCmp('ol_membershipDetail').getForm().findField('frm_mode').getValue() != FORM_MODE_LIST){
									Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOLMemInfoDetail');
								}
								break;
							case "pnlOLLoan":
								if (Ext.getCmp('ol_loanDetail').getForm().findField('frm_mode').getValue() != FORM_MODE_LIST){
									Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOLLoanDetail');
									//Ext.getCmp('ol_loanDetail').getForm().reset();
								}
								break;	
							case "pnlOLWithdrawal":
								if (Ext.getCmp('ol_withdrawalDetail').getForm().findField('frm_mode').getValue() != FORM_MODE_LIST){
									Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOLWithdrawalDetail');
									//Ext.getCmp('ol_withdrawalDetail').getForm().reset();
								}
								break;
							case "pnlOnlinePayrollDeduction":
								if (Ext.getCmp('ol_payrollDetail').getForm().findField('frm_mode').getValue() != FORM_MODE_LIST){
									Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOnlinePayrollDeductionDetail');
								}
								if(_IS_ADMIN){
									Ext.getCmp('online_payroll_deduction_search').setVisible(true);
									//Ext.getCmp('onlinePDbutton').setVisible(false);
								}
								break;
							case "pnlOnlineLoanPayment":
								if (Ext.getCmp('ol_loanpaymentDetail').getForm().findField('frm_mode').getValue() != FORM_MODE_LIST){
									Ext.getCmp('onlineCardBody').layout.setActiveItem('pnlOnlineLoanPaymentDetail');
								}
								if(_IS_ADMIN){
									Ext.getCmp('online_loanpayment_search').setVisible(true);
									//Ext.getCmp('onlineLPbutton').setVisible(false);
								}
									
								break;
							case "processOnline":
								if(_IS_ADMIN){
									Ext.getCmp('onlineCardBody').layout.setActiveItem('processOnline');
								} else {
									showExtErrorMsg("No Access Rights.");
								}
									
								break;
							
						}						
					}
				}	
			}
		};
	}
	
	,onlineCardBody: function(){
		return {
			xtype:'panel'
			,id: 'onlineCardBody'
			,region:'center'
			,titlebar:false
			,activeItem:0
			,enableTabScroll: true
			,layout: 'card'
			,autoScroll: true	
			,layoutConfig: {
				  deferredRender: false
				}
			,items:[
				{xtype:'panel',id:'pnlOLMemInfo',title:'Membership'			
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [ol_membershipFilter()]
				}
				,{xtype:'panel',id:'pnlOLMemInfoDetail',title:'Membership'		
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [ol_membershipDetail()]
				}
				
				,{xtype:'panel',id:'pnlOnlinePayrollDeduction',title:'Payroll Deductions'
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [ol_payrollFilter()]
				}
				,{xtype:'panel',id:'pnlOnlinePayrollDeductionDetail',title:'Payroll Deductions'
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [ol_payrollDetail()]
				}
				,{xtype:'panel',id:'pnlOnlineLoanPayment',title:'Pay Loan'
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border'/* {type:'vbox', align:'stretch'} */, defaults: {autoScroll: true}
					,items: [ol_loanpaymentFilter()/* ,ol_loanpaymentList() */]
				}
				,{xtype:'panel',id:'pnlOnlineLoanPaymentDetail',title:'Pay Loan'
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [ol_loanpaymentDetail()]
				}
				,{xtype:'panel',id:'pnlOLLoan',title:'Apply Loan'	
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [ol_loanFilter()]
				}
				,{xtype:'panel',id:'pnlOLLoanDetail',title:'Apply Loan'		
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'fit', defaults: {autoScroll: true}
					,items: [ol_loanDetail()]
				}
				,{xtype:'panel',id:'pnlOLWithdrawal',title:'Capital Contribution'		
					// ,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [ol_withdrawalFilter()]
				}
				,{xtype:'panel',id:'pnlOLWithdrawalDetail',title:'Capital Contribution'		
					// ,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [ol_withdrawalDetail()]
				}
				,{xtype:'panel',id:'processOnline',title:'Process Online'		
					// ,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [processOnline()]
				}
			]
		};
	}
	,membershipInfoCardBody: function(){
		return {
			xtype:'panel'
			,id: 'membershipInfoCardBody'
			,region:'center'
			,titlebar:false
			,activeItem:0
			,enableTabScroll: true
			,layout: 'card'
			,autoScroll: true	
			,layoutConfig: {
				  deferredRender: false
				}
			,items:[
				{xtype:'panel',id:'pnlOLMemInfoDisplay',title:'Membership Information'		
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [ol_membershipInfo()]
				}
				,{xtype:'panel',id:'pnlOLReportSOA',title:'Statement of Account',width:1100
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [rpt_OLSOA()]
				}
			]
		};
	}
	,masterFilesNav : function(){
		return {
			xtype: 'treepanel'
			,title: 'Master Files'
			,collapsible:true	
			,id: 'masterFilesNav'
			,region: 'west'
			,width: 200
			,rootVisible:false
			,lines:false
		    ,root: {
		        text: '',
		        children:[
					{text:'Membership Information',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlMemberInfo'}
					,{text:'Third-Party Supplier',leaf:true,iconCls:'menuItemIcon',activatepanel:'pnlSupplier'}
				]
			}
			,listeners:{
				"click":{
					scope:this
					,fn:function(node,e) {
						Ext.getCmp('masterFilesCardBody').layout.setActiveItem(node.attributes.activatepanel);
						switch(node.attributes.activatepanel) {
							case "pnlMemberInfo":
								if (Ext.getCmp('memberDetail').getForm().findField('frm_mode').getValue() != FORM_MODE_LIST){
									Ext.getCmp('masterFilesCardBody').layout.setActiveItem('pnlMemberInfoDetail');
								}
								break;
							case "pnlSupplier":
								if (Ext.getCmp('supplierDetail').getForm().findField('frm_mode').getValue() != FORM_MODE_LIST){
									Ext.getCmp('masterFilesCardBody').layout.setActiveItem('pnlSupplierDetail');
								}
								break;
							default:
								break;
						}						
					}
				}	
			}
		};
	}
	
	,masterFilesCardBody: function(){
		return {
			xtype:'panel'
			,id: 'masterFilesCardBody'
			,region:'center'
			,titlebar:false
			,activeItem:0
			,enableTabScroll: true
			,layout: 'card'
			,autoScroll: true	
			,layoutConfig: {
				  deferredRender: true
				}
			,items:[
				{xtype:'panel',id:'pnlMemberInfo',title:'Membership Information'			
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [memberList()]
				}
				,{xtype:'panel',id:'pnlMemberInfoDetail',title:'Membership Information'			
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [memberDetail()]
				}
				,{xtype:'panel',id:'pnlSupplier',title:'Third-Party Supplier'
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [supplierList()]
				}
				,{xtype:'panel',id:'pnlSupplierDetail',title:'Third-Party Supplier'
					//,html:'<div class="placeholder">Under construction</div>'
					,layout: 'border', defaults: {autoScroll: true}
					,items: [supplierDetail()]
				}
			]
		};
	}
	
};

var page = new peca();