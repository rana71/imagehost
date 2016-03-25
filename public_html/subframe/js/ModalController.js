/* global Subframe, head, JsonRpc */
Subframe.Lib.ModalController = function() {
    "use strict";
    
    var $this = this;
    $this.objActiveModal = {};
    $this.objActiveFancybox = {};
    
    this.shortInfo = function (strInfoClass, strInfoContent) {
        var elContent = $('<div><p class="alert '+strInfoClass+'">'+strInfoContent+'</p></div>')
        $this.fancyboxOpen(elContent, 300);
    };
    
    this.open = function (strModalName, objModalSettings) {
        var strApplicationName = Subframe.Application.strName;
        var arrNeededScripts = [];
        
        arrNeededScripts.push('/' + strApplicationName + '/js/modal/' + strModalName + '.js');
//        arrNeededScripts.push('/js/subframe/vendor/fancybox/jquery.fancybox.pack.js');
//        arrNeededScripts.push('/js/subframe/vendor/fancybox/jquery.fancybox.css');
        
        head.load(arrNeededScripts, function () {
            $this.objActiveModal = new Subframe[strApplicationName].modal[strModalName](objModalSettings);
            JsonRpc.post({
                context: $this,
                method: $this.objActiveModal.strBox,
                callBack: 'boxLoadedCallback'
            });
        });
    };
    
    this.boxLoadedCallback = function (objResponse) {
        $this.objActiveModal.elContent = $('<div>' + objResponse.strContent + '</div>');
        $this.fancyboxOpen($this.objActiveModal.elContent, 'auto');
        $this.objActiveModal.launch();
    };
    
    this.close = function () {
        $.fancybox.close();
    };
    
    this.fancyboxOpen = function (elContent, mulWidth) {
        $.fancybox.open({
            content: elContent, 
            autoScale: true, 
            autoDimensions: true, 
            centerOnScroll: true, 
            width: mulWidth, 
            height: 400
        });
    };
    
};