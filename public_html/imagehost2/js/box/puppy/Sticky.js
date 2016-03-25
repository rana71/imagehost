/* global Subframe, head, JsonRpc2, objSearch */
Subframe.Box.puppy_Sticky = function () {
    "use strict";

    var $this = this;
    
    this.boolIsStickyVisible = false;
    this.numMinOffsetToShow = 100;
    this.elBanner = null;
    
    this.init = function () {};
    
    this.launch = function () {
        $this.elBanner = $('.puppy.sticky');
        if (!empty($this.elBanner) && !empty($this.elBanner.html())) {
            $this.initStickyShows();
        };
    };
    
    this.initStickyShows = function () {
        var numCurrentOffset = 0;
        
        $(window).scroll(function(){
            numCurrentOffset = $(window).scrollTop();
            
            if (numCurrentOffset > $this.numMinOffsetToShow && $this.boolIsStickyVisible === false) {
                $this.boolIsStickyVisible = true;
                $this.showSticky();
            } else if (numCurrentOffset <= $this.numMinOffsetToShow && $this.boolIsStickyVisible === true) {
                $this.boolIsStickyVisible = false;
                $this.hideSticky();
            }
            
        });
    };
    
    this.showSticky = function () {
        $this.elBanner.animate({
            left: '0'
        }, 500);
    };
    
    this.hideSticky = function () {
        $this.elBanner.animate({
            left: '100%'
        }, 500);
    };
    
    
};
