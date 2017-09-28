//the namespace
Ext.namespace("Ext.ux.DataView");

/**
 * a Ext.ux.DataView Plugin which imitates the famous LightBox in ExtJS style
 * it opens a window, provides back and forward buttons for cycling through images
 *
 * it uses 2 different methods of showing the window...gecko browsers simply reloads the image src,
 * and syncs the window size so it fits
 *
 * all other browser need the destroy the window, and create a new one
 *
 * example {cfg} parameters:
 *
 * {
 * 		prevImage	// the previous image src
 * 		nextImage	// the next image src
 * 		event			// the event to bind
 * }
 *
 * @class Ext.ux.DataView.LightBox
 * @author caziel
 * @copyright 2008
 * @license LGPL
 * @namespace Ext.ux.DataView
 * @version 0.2
 * @see http://extjs.com/forum/showthread.php?p=262224
 * @example http://extjs-ux.org/repo/authors/caziel/trunk/Ext/ux/DataView/LightBox/examples/
 *
 * @param {Object} cfg
 * @extends {Ext.ux}
 */
Ext.ux.DataView.LightBox = function(cfg){

    /**
     * the window config
     *
     * @private
     * @cfg {Object}
     * @type {Object}
     */
    var cfg = cfg ||
    {};
    
    /**
     * the DataView
     *
     * @private
     * @type {Ext.DataView}
     */
    var view;
    
    /**
     * the LightBox Window
     *
     * @private
     * @type {Ext.Window}
     */
    var window;
    
    /**
     * the current image record
     *
     * @private
     * @type {Ext.data.Record}
     */
    var rec;
    
    /**
     * the current index
     *
     * @private
     * @type {int}
     */
    var index;
    
    /**
     * the init function for starting the plugin
     *
     * @methodOf  Ext.ux.DataView.LightBox
     * @param {Ext.DataView} dataView the DataView
     * @return {void}
     */
    this.init = function(dataView){
        view = dataView;
        if (cfg && cfg.event) {
            view.on(cfg.event, clickAction, this);
        }
        else {
            view.on('dblclick', clickAction, this);
        }
    };
    
    /**
     * the bounded Event Handler
     *
     * @private
     * @methodOf  Ext.ux.DataView.LightBox
     * @param {Ext.DataView} view
     * @param {int} index
     * @param {Ext.data.Node} node
     * @param {Ext.EventObject} e
     * @return {void}
     */
    function clickAction(view, ind, node, e){
        index = ind;
        rec = view.getRecord(view.getNode(index)); // get the record from the selected index
        showWindow();
    };
    
    /**
     * loads an image into the window
     *
     * @methodOf  Ext.ux.DataView.LightBox
     * @private
     * @return {void}
     */
    loadImage = function(){
        //set the title
        window.setTitle(Ext.util.Format.ellipsis(rec.data.name + " [" + (index + 1) + " of " + view.store.getCount() + "]", 35));
        
        //disable/enable previous/next buttons
        view.getNode(index - 1) ? Ext.ComponentMgr.get('prev_btn').show() : Ext.ComponentMgr.get('prev_btn').hide();
        view.getNode(index + 1) ? Ext.ComponentMgr.get('next_btn').show() : Ext.ComponentMgr.get('next_btn').hide();
        
        //change selection
        Ext.each(view.getSelectedNodes(), function(e){
            view.deselect(e);
        }, this);
        view.select(index);
        
        if (Ext.isGecko) { // geckos reloads the img directly
            window.body.dom.src = rec.data.url;
            window.setSize(window.body.dom.width, window.body.dom.height);
            window.center();
        }
        else { // all others needs a new window
            if (window) {
                window.destroy();
            }
            showWindow();
        }
        
    };
    
    /**
     * the action to show the previous image
     *
     * @methodOf  Ext.ux.DataView.LightBox
     * @private
     * @return {void}
     */
    prevAction = function(){
        index = index - 1;
        rec = view.getRecord(view.getNode(index));
        loadImage();
    };
    
    /**
     * the action to show the next image
     *
     * @methodOf  Ext.ux.DataView.LightBox
     * @private
     * @return {void}
     */
    nextAction = function(){
        index = index + 1;
        rec = view.getRecord(view.getNode(index));
        loadImage();
    };
    
    /**
     * generate the LighBox Window on the fly
     *
     * @methodOf  Ext.ux.DataView.LightBox
     * @private
     * @param {int} ind
     * @return {void}
     */
    showWindow = function(){
        var config = { // the default window config
			frame: true
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
            ,title: rec ? Ext.util.Format.ellipsis(rec.data.name + " [" + (index + 1) + " of " + view.store.getCount() + "]", 35) : '',
            bodyCfg: { // the <img/> dom object
                tag: 'img',
                cls: 'x-window-body',
                style: {
                    width: '600',
                    height: '700'
                },
                src: rec ? rec.data.url : ''
            },
            bbar: [{ // the previous button
                xtype: 'tbbutton',
                icon: cfg.prevImage,
                iconCls: 'blist',
                id: 'prev_btn',
                text: "Previous",
                hidden: view.getNode(index - 1) ? false : true,
                scope: this,
                handler: prevAction
            }, {
                xtype: 'tbfill'
            }, { // the next button
                xtype: 'tbbutton',
                id: 'next_btn',
                text: "Next",
                iconCls: 'blist',
                icon: cfg.nextImage,
                scope: this,
                hidden: view.getNode(index + 1) ? false : true,
                handler: nextAction
            }],
            listeners: {
                "render": function(){
                    this.setSize(this.body.dom.width, this.body.dom.height);
                }
            }
        };
        
        window = new Ext.Window(config); // create the window
        window.show(); // show it immidiatly
        if (Ext.isGecko) { // gecko browser only
            loadImage();
        }
    };
};
