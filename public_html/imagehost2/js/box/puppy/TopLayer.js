/* global Subframe, head, JsonRpc2, objSearch */
Subframe.Box.puppy_TopLayer = function () {
    "use strict";

    var $this = this;
    this.elCurrentTLModal = null;
    this.strLastClosedTimestampCookieName = 'imagehost-toplayer'
    this.numPageLoadedTimestamp = 0;
    this.numShowEverySeconds = 60*60;
    
    this.init = function () {};
    
    this.launch = function () {
        
        head.load('https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js', function () {
            $this.numPageLoadedTimestamp = Math.floor(new Date().getTime() / 1000);
            var numLastClosedTimestamp = $.cookie($this.strLastClosedTimestampCookieName);
            
            if (empty(numLastClosedTimestamp) || $this.numPageLoadedTimestamp-numLastClosedTimestamp >= $this.numShowEverySeconds) {
                $this.showTopLayer();
            };
        });
    };
    
    this.showTopLayer = function () {
        $this.elCurrentTLModal = $("#modal-top-layer").clone();
        $this.elCurrentTLModal.on('hidden.bs.modal', function (e) {
            $.cookie($this.strLastClosedTimestampCookieName, $this.numPageLoadedTimestamp, { expires: 9999, path: '/' });
            $this.elCurrentTLModal.remove();
        }).on('shown.bs.modal', function (e) {
            var numTargetWidth = parseInt($this.elCurrentTLModal.find('input.toplayer-width').val());
            var numTargetHeight = parseInt($this.elCurrentTLModal.find('input.toplayer-height').val());

            if (numTargetWidth === 0) {
                numTargetWidth = $this.elCurrentTLModal.find('.modal-body >').width();
            };
            if (numTargetHeight === 0) {
                numTargetHeight = $this.elCurrentTLModal.find('.modal-body >').height();
            };

            numTargetWidth += 30;
            numTargetHeight += 30;

            $this.elCurrentTLModal.find('.modal-dialog, .modal-content, .modal-body').css({
                'min-width' : numTargetWidth+'px', 
                'min-height' : numTargetHeight+'px'
            });
        });
        $this.elCurrentTLModal.modal('show');
    };
    
};
