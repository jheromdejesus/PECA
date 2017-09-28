Ext.override(Ext.layout.FormLayout, {
	getTemplateArgs: function(field) {
		var noLabelSep = !field.fieldLabel || field.hideLabel;
		var labelSep = (typeof field.labelSeparator == 'undefined' ? this.labelSeparator : field.labelSeparator);
		if (field.required) labelSep += '<span style="color: rgb(255, 0, 0); padding-left: 2px;">*</span>';
			return {
			id: field.id,
			label: field.fieldLabel,
			labelStyle: field.labelStyle||this.labelStyle||'',
			elementStyle: this.elementStyle||'',
			labelSeparator: noLabelSep ? '' : labelSep,
			itemCls: (field.itemCls||this.container.itemCls||'') + (field.hideLabel ? ' x-hide-label' : ''),
			clearCls: field.clearCls || 'x-form-clear-left'
		};
	}
});

var MAX_PAGE_SIZE = 20;
var FORM_MODE_NEW = '1';
var FORM_MODE_UPDATE = '2';
var FORM_MODE_SHOW = '3';
var FORM_MODE_CLONE = '4';
var FORM_MODE_LIST = '5';

function showExtErrorMsg(_msg) {
    Ext.MessageBox.show({
        title: 'Error Message'
        ,msg: _msg
        ,width:500
        ,buttons: Ext.MessageBox.OK
        ,icon: Ext.MessageBox.ERROR
    });
}

function showExtInfoMsg(_msg) {
    Ext.MessageBox.show({
        title: 'Info Message'
        ,msg: _msg
        ,width:500
        ,buttons: Ext.MessageBox.OK
        ,icon: Ext.MessageBox.INFO
    });
}

function formatDate(value){
    return value ? value.substring(0,2)+'/'+value.substring(2,4)+'/'+value.substring(4,8) : '';
}

function formatDateYYYYMMDD(value){
    return value ? value.substring(4,6)+'/'+value.substring(6,8)+'/'+value.substring(0,4) : '';
}

function formatDateTime(value){
    return value ? 
		value.substring(0,4)+'/'+value.substring(4,6)+'/'+value.substring(6,8)+
		' ' +
		value.substring(8,10)+':'+value.substring(10,12)+':'+value.substring(12,14) : '';
}

function formatCheckbox(value){
	return (value=='Y')? 'true':'false';
}

function formatCheckboxCapcon(value){
	var par_value = parseFloat(value); 
	return (par_value==0.00)? 'false':'true';
}

function formatYesNo(value){
	return (value=='true')? 'Yes':'No';
}

function loadForm(form, url){
	Ext.getCmp(form).getForm().load({
		url: url
		,params: {auth:_AUTH_KEY}
    	,method: 'POST'
    	,waitMsgTarget: true
	});
}

function getComboYear(){
	var years =  [];
	var year_to_display = 10;
	var cur_year = new Date().getFullYear();
	var i = 0;
	
	for (i=0; i<year_to_display; i++){
		years[i] = [cur_year-i, cur_year-i];
	}
		
	return years;


}