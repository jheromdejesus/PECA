Ext.namespace('Ext.ux');

Ext.util.Format.usMoneyNull = function(v, cents) {
    // -- allows clearing of field value (so that $0.00 only shows if you
    // explicitly entered zero for a value).
    if (v === null || v === '') {
        return '0';
    } else if (v > 999999999999) {
        return '0';
    } else {
        // Modified version from Ext.util.Format.usMoney checks the cents
        // parameter to determine whether or not to display decimal places
        v = Math.round((v - 0) * 100) / 100;
        v = (v == Math.floor(v)) ? v + ".00" : ((v * 10 == Math.floor(v * 10)) ? v + "0" : v);
        v = String(v);
        var ps = v.split('.');
        var whole = ps[0];
        var sub = ps[1] ? '.' + ps[1] : '.00';
        var r = /(\d+)(\d{3})/;
        while (r.test(whole)) {
            whole = whole.replace(r, '$1' + ',' + '$2');
        }
        v = (cents) ? whole + sub : whole;
        if (v.charAt(0) == '-') {
//            return '-$' + v.substr(1);
            return '-' + v.substr(1);
        }
//        return "$" + v;
        return v;
    }
};


/* NumberField Implementation */
Ext.ux.moneyField = function(config) {
    //-- Add any numberfield default settings here:
    var defaultConfig = {
        allowDecimals: true,
        allowNegative: true,
        decimalPrecision: 2,
        maxValue: 9999999999.99,
        //minValue: 0,
        selectOnFocus: true,
        value: null
        ,style: 'text-align: right'
    	,validateOnBlur: false
		,validationEvent: false
    };

    Ext.ux.moneyField.superclass.constructor.call(this, Ext.apply(defaultConfig, config));

    this.on('change',this._onChange,this);
    this.on('focus',this._onFocus);
    this.on('blur',this._onBlur);
    this.on('render',this._onRender);
    this.moneyNumericValue=config.value || null;
};

Ext.extend(Ext.ux.moneyField, Ext.form.NumberField, {
    moneyNumericValue: null,

    initSelf: function() {
        // When form loads, bare number is loaded and displayed. Call this (via listener, typically)
        // to format value to money.
        if (this.value === null || this.value === '') {
            this.moneyNumericValue = null;
        } else {
            this.moneyNumericValue = this.value;
        }
        this.setRawValue(this.formatter(this.moneyNumericValue));

        // prevent field from reporting itself as "dirty" after form load (isDirty check):
        this.originalValue = this.moneyNumericValue;
    },
    
    findParentBy: function(fn) {
        for (var p = this.ownerCt; (p !== null) && !fn(p); p = p.ownerCt);
        return p;
    },

    findParentByType: function(xtype) {
        return typeof xtype == 'function' ?
            this.findParentBy(function(p) {
                return p.constructor === xtype;
            }) :
            this.findParentBy(function(p) {
                return p.constructor.xtype === xtype;
            });
    }
    
    ,initComponent: Ext.form.NumberField.prototype.initComponent.createSequence(function() {
        this.renderer = Ext.util.Format.numberRenderer("0" + this.decimalSeparator + ("0000000000".substr(0, this.decimalPrecision)));
    })

    ,setValue : function(v){
	    v = Ext.num(String(v).replace(this.decimalSeparator, "."), '');
	    this.moneyNumericValue = v;
	    Ext.form.NumberField.superclass.setValue.call(this, this.renderer(v));
	}
    
    ,getValue:function() {
        //return this.value+"".replace(/[^0-9.-]/g,"")-0; strip out any formatting characters from string.
        if (this.value === '' || this.value === null) {
            return null;
        } else if (isNaN(this.value)) {
            this.value = 0;
        } else {
            return Number(this.value);
        }
    },

    _onChange:function(field, newVal, oldVal) {
        // n will always be unformatted numeric as STRING! So "-0" to force numeric type:
        if (newVal === '') {
            this.moneyNumericValue = null;
        } else {
            this.moneyNumericValue = newVal-0;
            }
    },

    _onBeforeAction: function(form, action) {
        this.setRawValue(this.getValue());
    },

    _onRender:function(cmp) {
        this.setRawValue(this.formatter(this.moneyNumericValue));
        if (this.isFormField) {    // Is this check necessary?
            var parentForm = this.findParentByType('form');

            /*
                Note: If client-side validation is enabled, unformatted numbers get posted on save. Good!

                (The field's "isValid" method just does this for some reason, which works to our advantage.)

                BUT (there's always a "but"), we then need to apply back the money formatting so that the
                numbers aren't left displayed as plain.
            */

            // Format money after successful form save:
            parentForm.on('actioncomplete', function() {cmp.initSelf();});

            // Format back to money after failed save attempt.
            parentForm.on('actionfailed', function() {cmp.initSelf();});

            // doLayout is called on initial page load.
            parentForm.on('afterLayout', function() {cmp.initSelf();});

            parentForm.on('beforeaction', this._onBeforeAction, this);

            // Formats money after client-side validation fails...maybe...still investigating this.
            // parentForm.on('beforeaction', function() {cmp.initSelf();});

            /*
                Depends on order of any success/actioncomplete/actionfailed listeners???
                Or maybe you are checking if the form isValid yourself by listening to the
                form's beforeaction, type action.type=='submit' and then returning false to
                cancel the action.    Still looking into all this.

                More on clientValidation:
                Before the form submits (doAction 'submit' with clientValidation not set to false...
                from docs: "If undefined, pre-submission field validation is performed.") the call
                to form "isValid" will turn the moneyfield back to a plain, unformatted number,
                which will get posted.
            */

        }
    },

    formatter: function(value) {
        var showCents = (this.decimalPrecision !== 0);

        // usMoneyNull formatter *always* includes 2 decimal places at the end (".00"). If decimalPrecision is set to 0, lob off the decimals.

        if (value === 0) {
            return Ext.util.Format.usMoneyNull("0", showCents);  // returns '$0.00/$0' instead of ''.
        } else if(value == null){
        	return '';
        } else {
            return Ext.util.Format.usMoneyNull(value, showCents);
        }
    },

    _onBlur: function(field) {
        /*
            always update moneyNumericValue with the actual RawValue (which right here (onBlur) will
            *always* be numeric    (remember: when focused, it's unformatted numeric entry...just like
            numberfield does. So onBlur, grab that numeric value and save it to this.moneyNumericValue,
            then apply formatting back to RawValue for display.
        */

        if (field.getRawValue().substring(0,1) != '$') {
            if (field.getRawValue() === '') {
                this.moneyNumericValue=null;
            } else {
                this.moneyNumericValue=field.getRawValue()-0;
            }

            field.setRawValue(this.formatter(this.moneyNumericValue));

            if (this.moneyNumericValue !== this.value)    {
                /* for some reason, when zero'ing out a value (or clearing), the onChange event is not firing 
                    and this.value is not getting set to the new zero or blank value. This fixes that:
                */
                this.value = this.moneyNumericValue;
            }

        }
    },

    _onFocus: function(field) {
        if (this.moneyNumericValue === null||this.moneyNumericValue === '') {
        	if(field.getValue()==''){
                field.setRawValue('');
            }
        } else {
            // remove formatting by restoring RawValue to moneyNumericValue.
            field.setRawValue((this.moneyNumericValue-0).toFixed(this.decimalPrecision));
        }
    },

    processValue: function(value) {
        return value;
    },

    validateValue : function(value) {
    	if (this.uglyFix === undefined && value.length < 1) {
    		// This horrible fix is to avoid validation error when a 
                    // required field (i.e. allowBlank: false)  is rendered
    		// for the first time and its initial value is empty.
    		this.uglyFix = '';
    		return true;
    	}

        if (!Ext.form.NumberField.superclass.validateValue.call(this, value)) {
          return false;
        }
        if (value.length < 1) { // if it's blank and textfield didn't flag it then it's valid
           return true;
        }
        value = Number(this.value);
        if (isNaN(value)) {
          this.markInvalid(String.format(this.nanText, value));
          return false;
        }
        var num = this.parseValue(value);
        if (num < this.minValue) {
          this.markInvalid(String.format(this.minText, this.minValue));
          return false;
        }
        if (num > this.maxValue) {
          this.markInvalid(String.format(this.maxText, this.maxValue));
          return false;
        }
        return true;
    },

    initAllSiblings: function() {
        /*
        Sometimes need to call this manually after complex form loads
        */
        if (this.isFormField) {    // Is this check necessary?
            var parentForm = this.findParentByType('form');

            var moneyFields = parentForm.findByType('moneyfield');
            Ext.each(moneyFields, function(moneyfield) {moneyfield.initSelf();});
        }
    },

    clearDirty: function() {
        /* needed for a calculated display-only fields whose submitted value is ignored */
        this.originalValue = this.getRawValue();
    }

});

Ext.reg('moneyfield', Ext.ux.moneyField);

//override: to automatically display decimal place for whole numbers
var pecaNumberField = Ext.extend(Ext.form.NumberField, {
	
	initComponent: Ext.form.NumberField.prototype.initComponent.createSequence(function() {
        this.renderer = Ext.util.Format.numberRenderer("0" + this.decimalSeparator + ("0000000000".substr(0, this.decimalPrecision)));
    }),

    setValue : function(v){
	    v = Ext.num(String(v).replace(this.decimalSeparator, "."), '');
	    return Ext.form.NumberField.superclass.setValue.call(this, this.renderer(v));
	}
});
//register numberfield override
Ext.reg('pecaNumberField', pecaNumberField);