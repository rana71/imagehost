/* global Subframe, head, JsonRpc2, objSearch */
Subframe.Box.artifact_HorizontalNav = function () {
    // purge test
    "use strict";

    var $this = this;

    this.elLeft = null;
    this.elRight = null;
    this.numLeftOffset = 0;
    this.numRightOffset = 0;
    this.numOrginalOpacity = 0;
    this.boolAnimation = false;
    this.objLoader = {};

    this.init = function () {
        $this.objLoader = new Subframe.Lib.Loader();
    };

    this.launch = function () {
//        $this.elLeft = $('a.nav-horizontal.left');
//        $this.elRight = $('a.nav-horizontal.right');
//        $this.numOrginalOpacity = $this.elRight.css('opacity');
//        $this.enchantNavigation();
    };

    this.enchantNavigation = function () {
        var strFocusTag = '';
        
        if ($this.elRight.length > 0 && $this.elLeft.length > 0) {
            $('body').on({
                'movestart': function (e) {
                    console.log('s');
                    if ((e.distX > e.distY && e.distX < -e.distY) || (e.distX < e.distY && e.distX > -e.distY)) {
                         e.preventDefault();
                    };
                }
            });
        };
        
        if ($this.elRight.length > 0) {
            $this.elRight.find('span').show();
            $this.numRightOffset = 0 - $this.elRight.find('span').outerWidth();
            $this.elRight.css('right', $this.numRightOffset);
            
            $this.elRight.on({
                mouseenter: $this.showRight, 
                mouseleave: $this.hideRight, 
                click: $this.goRight, 
                touchstart: function () {
                    $this.showRight();
                    $this.goRight();
                }
            });   
            
            $('body').on({
                'swipeleft': function () {
                    $this.showRight();
                    $this.goRight();
                }, 
                'keydown': function (e) {
                    if (e.keyCode === 39) {
                        strFocusTag = $(e.target).prop('tagName').toLowerCase();
                        if (strFocusTag !== 'input' && strFocusTag !== 'textarea') {
                            $this.showRight();
                            $this.goRight();
                        };
                    };
                }
            });
        };

        if ($this.elLeft.length > 0) {
            $this.elLeft.find('span').show();
            $this.numLeftOffset = 0 - $this.elLeft.find('span').outerWidth();
            $this.elLeft.css('left', $this.numLeftOffset);

            $this.elLeft.on({
                mouseenter: $this.showLeft, 
                mouseleave: $this.hideLeft, 
                click: $this.goLeft, 
                touchstart: function () {
                    $this.showLeft();
                    $this.goLeft();
                }
            });   
            
            $('body').on({
                'swiperight': function () {
                    $this.showLeft();
                    $this.goLeft();
                }, 
                'keydown': function (e) {
                    if (e.keyCode === 37) {
                        strFocusTag = $(e.target).prop('tagName').toLowerCase();
                        if (strFocusTag !== 'input' && strFocusTag !== 'textarea') {
                            $this.showLeft();
                            $this.goLeft();
                        };
                    };
                }
            });
        };
    };

    this.showRight = function () {
        if ($this.boolAnimation === true) {
            return false;
        };
        ga('send', 'pageview', '/virt/swipe/right/focus');
        $this.boolAnimation = true;
        $this.elRight.animate({
            right: 0,
            opacity: 1
        }, 300, function () {
            $this.boolAnimation = false;
        });
    };
    
    this.hideRight = function () {
        $this.elRight.animate({
            right: $this.numRightOffset,
            opacity: $this.numOrginalOpacity
        }, 150);
    };
    
    this.showLeft = function () {
        if ($this.boolAnimation === true) {
            return false;
        };
        ga('send', 'pageview', '/virt/swipe/left/focus');
        $this.boolAnimation = true;
        $this.elLeft.animate({
            left: 0,
            opacity: 1
        }, 300, function () {
            $this.boolAnimation = false;
        });
    };
    
    this.hideLeft = function () {
        $this.elLeft.animate({
            left: $this.numLeftOffset,
            opacity: $this.numOrginalOpacity
        }, 150);
    };
    
    this.goLeft = function () {
        ga('send', 'pageview', '/virt/swipe/left/go');
        $this.objLoader.add();
        $this.elRight.animate({
            opacity: 0
        }, 200);
        setTimeout(function () {
            window.location.replace($this.elLeft.attr('href'));
        }, 200);
    };
    
    this.goRight = function () {
        ga('send', 'pageview', '/virt/swipe/right/go');
        $this.objLoader.add();
        $this.elLeft.animate({
            opacity: 0
        }, 200);
        setTimeout(function () {
            window.location.replace($this.elRight.attr('href'));
        }, 200);
    };

};

