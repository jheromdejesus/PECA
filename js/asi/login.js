Ext.onReady(function(){
    Ext.QuickTips.init();
 
Ext.LinkButton = Ext.extend(Ext.BoxComponent, {
constructor: function(config) {
config = config || {};
config.xtype = 'box';
config.autoEl = { tag: 'a', html: config.text, href: '#' };
Ext.LinkButton.superclass.constructor.apply(this, arguments);
this.addEvents({
"click": true,
"mouseover": true,
"blur": true
});
this.text = config.text;
},
onRender: function() {
theLnk = this;
this.constructor.superclass.onRender.apply(this, arguments);
if (!theLnk.disabled) {
this.el.on('blur', function(e) { theLnk.fireEvent('blur'); });
this.el.on('click', function(e) { theLnk.fireEvent('click'); });
this.el.on('mouseover', function(e) { theLnk.fireEvent('mouseover'); });
}
}
});

Ext.reg('linkbutton', Ext.LinkButton);
 
    var login = new Ext.FormPanel({ 
        labelWidth:80,
        url:'/login/process', 
        frame:true, 
        title:'Please Login', 
        defaultType:'textfield',
        monitorValid:true,
		autoHeight: true,
        items:[{ 
                fieldLabel:'Email', 	
                name:'user[user_id]', 
                allowBlank:false,
				msgTarget: 'under',
                tabIndex: 1,
                listeners: {
					specialkey: function(txt,evt){
						if (evt.getKey() == evt.ENTER) {
			                login.getForm().url = '/login/process';
		                    login.getForm().submit({ 
		                        method:'POST', 
		                        waitTitle:'Connecting', 
		                        waitMsg:'Sending data...',
								timeout: 3000,
		                        
		                        success:function(form, action){
		                    		//alert(action.result.auth_key);
		                    		window.location = action.result.redirect + '?method=post&auth='+action.result.auth_key;
		                        },
		 
		                        failure:function(form, action){ 
		                        	Ext.Msg.alert('Login Failed!', action.result.msg);
		                            login.getForm().reset(); 
		                        } 
		                    }); 
						}
					}
					,valid: function(thisObj){
						Ext.getCmp('loginWin').syncSize()
					}
					,invalid: function(thisObj){
						Ext.getCmp('loginWin').syncSize()
					}
				} 
            },{ 
                fieldLabel:'Password', 
                name:'user[password]', 
                inputType:'password', 
				msgTarget: 'under',
                allowBlank:false,
                tabIndex: 2,
                listeners: {
					specialkey: function(txt,evt){
						if (evt.getKey() == evt.ENTER) {
			                login.getForm().url = '/login/process';
		                    login.getForm().submit({ 
		                        method:'POST', 
		                        waitTitle:'Connecting', 
		                        waitMsg:'Sending data...',
		                        timeout: 3000, 
								
		                        success:function(form, action){
		                    		//alert(action.result.auth_key);
		                    		window.location = action.result.redirect + '?method=post&auth='+action.result.auth_key;
		                        },
		 
		                        failure:function(form, action){ 
		                        	Ext.Msg.alert('Login Failed!', action.result.msg);
		                            login.getForm().reset(); 
		                        } 
		                    }); 
						}
					}
					,valid: function(thisObj){
						Ext.getCmp('loginWin').syncSize()
					}
					,invalid: function(thisObj){
						Ext.getCmp('loginWin').syncSize()
					}
				}  
            },
            { 
                 xtype: 'linkbutton'
                 ,text: 'Forgot Password?'
                 ,tabIndex: 4
				 //autoEl: {tag: 'a', href: '#', html: 'Forgot Password'}
				 ,listeners:{
				'click':{
				scope:this
				,fn:function(grid, row, e) {
                	var frm = login.getForm();
                	login.getForm().url = '/login/forgot';
                	frm.findField('user[password]').allowBlank = true;
                	if(frm.isValid()){
	                    login.getForm().submit({ 
	                        method:'POST', 
	                        waitTitle:'Connecting', 
	                        waitMsg:'Sending data...',
	                        
	                        success:function(form, action){
								Ext.Msg.alert('', action.result.msg);
								frm.findField('user[password]').allowBlank = false;
	                        },
	 
	                        failure:function(form, action){ 
	                        	Ext.Msg.alert('Login Failed!', action.result.msg);
	                            login.getForm().reset(); 
								frm.findField('user[password]').allowBlank = false;
	                        } 
	                    }); 
	                }
					else{
						frm.findField('user[password]').allowBlank = false;
					}
					
                }}
                }
            }],
 
        buttons:[{ 
                text:'Login',
                formBind: true,
                tabIndex: 3,	 
                // Function that fires when user clicks the button 
                handler:function(){ 
	                login.getForm().url = '/login/process';
                    login.getForm().submit({ 
                        method:'POST', 
                        waitTitle:'Connecting', 
                        waitMsg:'Sending data...',
                        timeout: 3000,
						
                        success:function(form, action){
                    		//alert(action.result.auth_key);
                    		window.location = action.result.redirect + '?method=post&auth='+action.result.auth_key;
                        },
 
                        failure:function(form, action){ 
                        	Ext.Msg.alert('Login Failed!', action.result.msg);
                            login.getForm().reset(); 
                        } 
                    }); 
                } 
            }]
    });
 
 
	// This just creates a window to wrap the login form. 
	// The login object is passed to the items collection.       
    var win = new Ext.Window({
        layout:'fit',
		id: 'loginWin',
        width:300,
		autoHeight: true,
        //height:150,
        closable: false,
        resizable: false,
        plain: true,
        border: false,
        items: [login]
	});
	win.show();
});