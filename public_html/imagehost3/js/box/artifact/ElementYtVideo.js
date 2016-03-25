/* global Subframe, head, JsonRpc2, objSearch */
Subframe.Box.artifact_ElementYtVideo = function () {
    "use strict";

    var $this = this;

    this.init = function () {};
    
    this.launch = function () {
        $this.initResizing();
        $(window).resize(function () {
            $this.initResizing();
        });
    };
    
    this.initResizing = function () {
        var numNewWidth = $('div.m-artifact ol.elements').innerWidth() - 2;
        $('div.m-artifact ol.elements iframe.yt-video').each(function (numIterator, elVideo) {
            elVideo = $(elVideo);
            if (elVideo.attr('width') !== numNewWidth) {
                $this.resizeVideo(elVideo, numNewWidth);
            };
        });
    };
    
    this.resizeVideo = function (elVideo, numNewWidth) {
        var numCurrentWidth = elVideo.attr('width');
        
        var numRatio =  numCurrentWidth / numNewWidth;
        var numNewHeight = elVideo.attr('height') / numRatio;
        
        elVideo.attr({
            width: numNewWidth, 
            height: numNewHeight
        });
    };
    
};
