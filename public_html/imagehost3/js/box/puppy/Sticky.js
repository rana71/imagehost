/* global Subframe, head, JsonRpc2, objSearch */
Subframe.Box.puppy_Sticky = function () {
    "use strict";

    var $this = this;
    
    this.boolIsStickyVisible = false;
    this.numMinOffsetToShow = 100;
    this.strDisableStickyCookieName = 'sticky-disabled-views-count';
    this.numDisableStickyForViewsCount = 3;
    this.boolIsStickyEnabled = true;
    this.elBanner = null;
    this.strStickyName = '';
    
    this.init = function () {};
    
    this.launch = function () {
        head.load('/bower_components/jquery.cookie/jquery.cookie.js', function () {
            $this.boolIsStickyEnabled = $this.isStickyEnabled();
            if ($this.boolIsStickyEnabled === true) {
                $this.elBanner = $('.puppy.sticky');
                if (!empty($this.elBanner) && !empty($this.elBanner.html())) {
                    $this.initStickyShows();
                    $this.initStickyClose();
                };
            } else {
                $.cookie($this.strDisableStickyCookieName, $.cookie($this.strDisableStickyCookieName)-1, {
                    expires: 365, 
                    path: '/'
                });
            };
        });
    };
    
    this.isStickyEnabled = function () {
        var boolIsEnabled = true;
        
        if (!empty($.cookie($this.strDisableStickyCookieName))) {
            var numStickyDisabledViewsLeft = parseInt($.cookie($this.strDisableStickyCookieName));
            boolIsEnabled = (numStickyDisabledViewsLeft <= 0);
        };
        return boolIsEnabled;
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
    
    this.initStickyClose = function () {
        $this.elBanner.find('span.sticky-close').click(function () {
            $this.disableSticky();
            $this.hideSticky();
        });
    };
    
    this.disableSticky = function () {
        $.cookie($this.strDisableStickyCookieName, $this.numDisableStickyForViewsCount-1, {
            expires: 365, 
            path: '/'
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
