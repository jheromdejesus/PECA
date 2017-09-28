
Ext.apply(Ext.form.VTypes, {
	password: function(val, field) {
		if (field.initialPassField) {
			var pwd = Ext.getCmp(field.initialPassField);
			return (val == pwd.getValue());
		}
		return true;
	},
	passwordText: 'The passwords entered did not matched!'
});

var changepasswordDetail = function(){
	return {
		xtype:'form'
		,id:'changepasswordDetail'
		,region:'center'
		,title: 'Details'
		,hidden:false
		,anchor: '100%'
		,autoscroll: true
		,frame: true
		,bodyStyle:{'padding':'10px'}
		,reader: pecaReaders.userReader
		,buttons:[{
			text:'Save'
			,iconCls: 'icon_ext_save'
		    ,handler: function(){
		    	var frm = Ext.getCmp('changepasswordDetail').getForm();
		    	if(frm.isValid()){
		    		frm.onUpdate(frm);
		    	}        	
		    }
		}]
		,items: [{
	        layout:'column'
	        ,items:[{
	            columnWidth:.5
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
	                fieldLabel: 'Enter Old Password'
	                ,name: 'user_cp[oldpass]'
	                ,id: 'user_cp[oldpass]'
	                ,anchor:'95%'
	                ,allowBlank: false
	                ,required: true
	                ,maxLength: 20
	                ,minLength: 4
	                ,inputType: 'password'
	                ,autoCreate: {tag: 'input', type: 'text', maxlength: '20'}
	            },{
	            	id: 'lbl'
	            	,xtype: 'box'
	            	,autoEl: {cn: 'Note: Password is case sensitive.'}
	            },new Ext.ux.PasswordMeter({
	                fieldLabel: 'Enter New Password'
	               ,name: 'user_cp[newpass]'
		           ,id:'user_cp[newpass]'
		           ,anchor:'95%'
		           ,allowBlank: false
		           ,required: true
		           ,maxLength: 20
				   ,minLength: 4
		           ,inputType: 'password'
	               ,autoCreate: {tag: 'input', type: 'text', maxlength: '20'}
	            })
	            , {
	                fieldLabel: 'Re-type New Password'
		            ,name: 'user_cp[confirmpass]'
		            ,anchor:'95%'
		            ,allowBlank: false
		            ,required: true
		            ,inputType: 'password'
		            ,maxLength: 20
					,minLength: 4
		            ,vtype: 'password', initialPassField: 'user_cp[newpass]'
		            ,autoCreate: {tag: 'input', type: 'text', maxlength: '20'}
		          }]
	        }]
	    }]
	    ,isModeNew: function() {
	    	return (Ext.getCmp('changepasswordDetail').getForm().findField('frm_mode').getValue() == FORM_MODE_NEW);
	    }
	    ,setModeNew: function() {
	    	Ext.getCmp('changepasswordDetail').getForm().findField('frm_mode').setValue(FORM_MODE_NEW);
	    	Ext.getCmp('changepasswordDetail').buttons[0].setVisible(true);  //save button
	    	//Ext.getCmp('changepasswordDetail').getForm().findField('user[user_id]').setReadOnly(false);
			//Ext.getCmp('changepasswordDetail').getForm().findField('user[user_id]').removeClass('x-item-disabled');
		}
		,setModeUpdate: function() {
			Ext.getCmp('changepasswordDetail').getForm().findField('frm_mode').setValue(FORM_MODE_UPDATE);
	    	Ext.getCmp('changepasswordDetail').buttons[0].setVisible(true);  //save button
	    	//Ext.getCmp('userDetail').getForm().findField('user[user_id]').setReadOnly(true);
			//Ext.getCmp('userDetail').getForm().findField('user[user_id]').addClass('x-item-disabled');
	    }
		,onSave: function(frm){
        	frm.submit({
    			url: '/change_password/add' 
    			,method: 'POST'
    			,params: {auth:_AUTH_KEY, 'user_cp[created_by]': _USER_ID}
    			,waitMsg: 'Creating Data...'
    			,success: function(form, action) {
        			showExtInfoMsg(action.result.msg);
    				frm.setModeUpdate();
    			}
    			,failure: function(form, action) {
    				if(action.result.error_code == 2){
    					showExtInfoMsg(action.result.msg);
    				}else{
	    				Ext.Msg.confirm('Confirm Action',action.result.msg,function(btn) {
	    					if(btn=='yes'){
	    						form.onUpdate(form);
	    					}
	    				});
    				}
    			}	
    		});
		}
		,onUpdate: function(frm){
			frm.submit({
    			url: '/change_password/update' 
    			,method: 'POST'
    			,waitMsg: 'Updating Data...'
    			,params: {auth:_AUTH_KEY, 'user_cp[modified_by]': _USER_ID	,'user[user_id]': _USER_ID	
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
		,listeners:{
			'render':{
			scope:this
			,fn:function(frm){
				//pecaDataStores.changepasswordStore.load();
			    //Ext.getCmp('changepasswordDetail').show();
				Ext.getCmp('changepasswordDetail').getForm().setModeUpdate();
				/*Ext.getCmp('changepasswordDetail').getForm().load({
			    	url: '/users/show'
				    	,params: {'user[user_id]':_USER_ID
									,auth:_AUTH_KEY}
				    	,method: 'POST'
				    	,waitMsgTarget: true
				});*/
				//Ext.getCmp('changepasswordDetail').show();
			}
		}
		}
	};
};



