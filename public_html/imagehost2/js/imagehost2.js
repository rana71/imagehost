//?var imagehost2 = {};
//imagehost2.Box = {};

Subframe.Apps.imagehost2 = function (arrCurrentBoxes) {

    "use strict";
    var $this = this;
    $this.strName = 'imagehost2';
    this.objBoxes = {};
    this.arrCurrentBoxes = arrCurrentBoxes;
    this.objBoxController = {};
    this.arrBootstrapFiles = [
        '/subframe/js/Global.js',
        '/subframe/js/JsonRpc2.js',
        '/subframe/js/Loader.js',
        '/subframe/js/BoxController.js',
        'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js'
    ];
    this.objLoader = {};

    this.init = function () {
//        $this.prepareWatermarks();
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
        
        $(function () {
            window.fbAsyncInit = function () {
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
                ;
                FB.init({
                    appId: strFbAppId,
                    cookie: true,
                    xfbml: true,
                    version: 'v2.3'
                });
            };

            (function (d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id))
                    return;
                js = d.createElement(s);
                js.id = id;
                js.async = true;
                js.src = "//connect.facebook.net/pl_PL/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));

            $this.objBoxController = new Subframe.Lib.BoxController($this.strName);
            $this.objBoxController.init($this.arrCurrentBoxes);
        });
    };

    this.prepareWatermarks = function () {
        $(".watermark img").one("load", function() {
            var elImage = $(this);
            var elContainer = $(elImage).closest('.watermark');
            var numWidth = $(elImage).width();
            var numHeight = $(elImage).height();
            var elWatermark = $('<div></div>').css({
                width: numWidth, 
                height: numHeight, 
                position: 'absolute',   
                top: 0, 
                left: 0, 
                zIndex: 2, 
                backgroundImage: 'url(/imagehost2/img/imged-pl.png)'
            });
            $(elContainer).css({
                position: 'relative', 
                display: 'block', 
                margin: '0 auto', 
                width: numWidth, 
                height: numHeight
            }).prepend(elWatermark);
            $(elImage).css({
                position: 'absolute', 
                left: 0, 
                top: 0
            });
        }).each(function() {
          if(this.complete) $(this).load();
        });
    };
};