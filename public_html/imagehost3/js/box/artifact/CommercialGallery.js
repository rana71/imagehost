/* global Subframe, head, JsonRpc2, objSearch */
Subframe.Box.artifact_CommercialGallery = function () {
    // purge test
    "use strict";

    var $this = this;

    this.init = function () {
        $this.objLoader = new Subframe.Lib.Loader();
    };
    

    this.launch = function () {
        $this.loadInitImage();
        $this.bindNagivation();
    };
    
    this.bindNagivation = function () {
        $('div.commercial-gallery div.thumbs a').click(function (e) {
            $this.objLoader.add();
            e.preventDefault();
            var numItemElementId = $(this).closest('li').data('item-element-id');
            $this.switchBigImage(numItemElementId);
            return false;
        });
        
        $('div.commercial-gallery div.main div.arrow').click(function () {
            var numCurrentElementId = $('div.commercial-gallery div.main div.photo img').attr('data-item-element-id');
            var numCurrentElementIndex = parseInt($('div.commercial-gallery div.thumbs li').index($('div.commercial-gallery li[data-item-element-id="'+numCurrentElementId+'"]')));
            var numElementsCount = $('div.commercial-gallery div.thumbs li').length;
            var numSwitchToElementId = 0;
            
            if ($(this).hasClass('right')) {
                if (numCurrentElementIndex+1 >= numElementsCount) {
                    numSwitchToElementId = $('div.commercial-gallery div.thumbs li:first-child').data('item-element-id');
                } else {
                    numSwitchToElementId = $('div.commercial-gallery div.thumbs li:eq('+(numCurrentElementIndex+1)+')').data('item-element-id');
                }
            } else {
                if (numCurrentElementIndex-1 < 0) {
                    numSwitchToElementId = $('div.commercial-gallery div.thumbs li:last-child').data('item-element-id');
                } else {
                    numSwitchToElementId = $('div.commercial-gallery div.thumbs li:eq('+(numCurrentElementIndex-1)+')').data('item-element-id');
                }
            };
            
            if (!empty(numSwitchToElementId) && numSwitchToElementId !== numCurrentElementId) {
                $this.switchBigImage(numSwitchToElementId);
            };
        });
    };
    
    this.loadInitImage = function () {
        var numInitElementId = 0;
        if (!empty(window.location.hash)) {
            var numItemElementId = window.location.hash.substring(1);   
            if ($('div.commercial-gallery div.thumbs li[data-item-element-id="'+numItemElementId+'"]').length > 0) {
                numInitElementId = numItemElementId;
            };
        };
        if (empty(numInitElementId)) {
            numInitElementId = $('div.commercial-gallery div.thumbs li:first-child').data('item-element-id');
        };
        $this.switchBigImage(numInitElementId);
    };
    
    this.switchBigImage = function (numItemElementId) {
        var strImageSrc = $('div.commercial-gallery div.thumbs li[data-item-element-id="'+numItemElementId+'"]').find('img').attr('src');
        var objNewImage = new Image();
        objNewImage.src = strImageSrc;
        objNewImage.onload = function () {
            if ($('div.commercial-gallery div.main div.photo img').length === 0) {
                $('div.commercial-gallery div.main div.photo').empty().append('<img />');
            };
            $('div.commercial-gallery div.main div.photo img').attr({
                'src': strImageSrc, 
                'data-item-element-id': numItemElementId
            });
            window.location.hash = numItemElementId;
            $this.objLoader.remove();
        };
    };
    
};
