//?var imagehost2 = {};
//imagehost2.Box = {};
Subframe.Apps.imagehost3 = function (arrCurrentBoxes) {

    "use strict";
    var $this = this;
    $this.strName = 'imagehost3';
    this.objBoxes = {};
    this.arrCurrentBoxes = arrCurrentBoxes;
    this.objBoxController = {};
    this.arrBootstrapFiles = [
        '/subframe/js/Global.js',
        '/subframe/js/JsonRpc2.js',
        '/subframe/js/Loader.js',
        '/subframe/js/BoxController.js',
        '/bower_components/jquery/dist/jquery.min.js',
        '/bower_components/bootstrap-sass/assets/javascripts/bootstrap.min.js', 
        '/bower_components/jquery.event.move/js/jquery.event.move.js', 
        '/bower_components/jquery.event.swipe/js/jquery.event.swipe.js'
    ];
    this.objLoader = {};

    this.init = function () {
        (function (i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function () {
                (i[r].q = i[r].q || []).push(arguments)
            }, i[r].l = 1 * new Date();
            a = s.createElement(o),
                    m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

        ga('create', 'UA-61969010-1', 'auto');
        ga('send', 'pageview');
    };

    this.loaded = function () {
        $this.objBoxController = new Subframe.Lib.BoxController($this.strName);
        $this.objBoxController.init($this.arrCurrentBoxes);
        $this.initAccountActivation();
        $this.ignoreHoverOnTouchDevices();
        
        $this.loadFacebookSdk();
        $(document).on('copy', $this.addTextToSelectedTextCopy);
    };
    
    this.ignoreHoverOnTouchDevices = function () {
        var boolIsTouched = window.ontouchstart || (navigator.MaxTouchPoints > 0) || (navigator.msMaxTouchPoints > 0);

        if (boolIsTouched) { 
            var numIterator = 0; 
            var numIterator2 = 0;
            var arrStylesheets = document.styleSheets;
            var objStyleSheet = {};
            
            try { 
                for (numIterator in arrStylesheets) {
                    var objStyleSheet = arrStylesheets[numIterator];
                    if (!objStyleSheet.rules) {
                        continue;
                    };

                    for (numIterator2 = objStyleSheet.rules.length - 1; numIterator2 >= 0; numIterator2--) {
                        if (!objStyleSheet.rules[numIterator2].selectorText) {
                            continue;
                        }

                        if (objStyleSheet.rules[numIterator2].selectorText.match(':hover')) {
                            objStyleSheet.deleteRule(numIterator2);
                        };
                    };
                };
            } catch (ex) {};
        };
    };
    
    this.addTextToSelectedTextCopy = function (e) {
        var objFn = window['getSelection'];
        if (typeof objFn !== 'function') {
            var objFn = document['getSelection'];
            if (typeof objFn !== 'function') {
                return false;
            }
        }
        var objSelection = objFn();
        var objSelectionRange = objSelection.getRangeAt(0);
        var strTextToAdd = "<br /><br />Zobacz więcej: <a href='"+window.location.href+"'>"+window.location.href+"</a> &copy; imgED";
        var objCopyHolder = $('<div style="position:absolute; left: -99999px"></div>').append('"'+objSelection+'"' + strTextToAdd);
        $('body').append(objCopyHolder);
        objSelection.selectAllChildren( objCopyHolder[0] );
        window.setTimeout(function () {
            objCopyHolder.remove();
            objSelection.removeAllRanges();
            objSelection.addRange(objSelectionRange);
        }, 10);
    };
    
    this.loadFacebookSdk = function () {
        var strFbAppId = '';
        switch (window.location.hostname) {
            case 'imged.pl':
                strFbAppId = '849363131784591';
                break;
            case 'rc.imged.com':
                strFbAppId = '849921671728737';
                break;
            default:
                strFbAppId = '849844208403150';
                break;
        }
        $.ajax({
            async: true, 
            type: "GET",
            url: '//connect.facebook.net/pl_PL/sdk.js',
            dataType: "script",
            cache: true, 
            success: function () {
                FB.init({
                    appId: strFbAppId,
                    version: 'v2.5', 
                    cookie: true,
                    xfbml: true
                });     
            }
        });
    };
    
//    this.loadGooglePlusSdk = function () {
//        window.___gcfg = {lang: 'pl'};
//        $.ajax({
//            async: true, 
//            type: "GET",
//            url: '//apis.google.com/js/platform.js',
//            dataType: "script",
//            cache: true
//        });
//    };
    
//    this.loadTwitterSdk = function () {
//        $.ajax({
//            async: true, 
//            type: "GET",
//            url: '//platform.twitter.com/widgets.js',
//            dataType: "script",
//            cache: true
//        });
//    };
    
    this.initAccountActivation = function () {
        if (!empty(window.location.hash.replace('#', ''))) {
            if (window.location.hash.replace('#', '') === 'activation-nok') {
                $this.elCurrentReportModal = $(".modal-activation-nok").clone();
            } else if (window.location.hash.replace('#', '') === 'activation-ok') {
                $this.elCurrentReportModal = $(".modal-activation-ok").clone();
                window.location.hash = '';
            }
            else if (window.location.hash.replace('#', '') === 'subscription-nok') {
                $this.elCurrentReportModal = $(".modal-subscription-nok").clone();
            } else if (window.location.hash.replace('#', '') === 'subscription-ok') {
                $this.elCurrentReportModal = $(".modal-subscription-ok").clone();
                window.location.hash = '';
            }
            ;
            if (!empty($this.elCurrentReportModal)) {
                $this.elCurrentReportModal.on('hidden.bs.modal', function (e) {
                    $this.elCurrentReportModal.remove();
                });
                $this.elCurrentReportModal.modal('show');
            };
        };
    };

};