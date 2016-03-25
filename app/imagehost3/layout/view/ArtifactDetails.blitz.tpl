<!DOCTYPE html>
<html lang="pl">
    <head>
        <base href='{{this::baseUrl()}}/' />
        <meta charset="UTF-8" />
        <meta name="robots" content="{{$robots}}" />
        <meta name="googlebot" content="{{$googlebot}}" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{$title}}</title>
        <meta name="description" content="{{$description}}" />
        
        
        
        {{if $arrOpengraph.strSiteName}}
            <meta property="og:site_name" content="{{$arrOpengraph.strSiteName}}" />
        {{end if-list}}
        {{if $arrOpengraph.strTitle}}
            <meta property="og:title" content="{{$arrOpengraph.strTitle}}" />
        {{end if-list}}
        {{if $arrOpengraph.strType}}
            <meta property="og:type" content="{{$arrOpengraph.strType}}" />
        {{end if-list}}
        {{if $arrOpengraph.strUrl}}
            <meta property="og:url" content="{{$arrOpengraph.strUrl}}" />
        {{end if-list}}
        
        {{BEGIN arrOpengraph.arrImages}}
            <meta property="og:image" content="{{$strUrl}}" />
        {{END}}
        
        <meta http-equiv="Content-Security-Policy" content="default-src * data: 'unsafe-inline' 'unsafe-eval'" />
        
        <meta name="msvalidate.01" content="5CB07FDC5838EA4621AEDE883BBCCDC4" />
        <meta name="tradetracker-site-verification" content="8c907d9300b8ebae3c1eb9912646e55b55fa65ca" />
        <meta name="p:domain_verify" content="347d12f9ee7fbbbff21b47c377a5a1ce"/>
        
        {{this::cssControllerRender()}}
        <script type="text/javascript">
        sas_tmstp=Math.round(Math.random()*10000000000);sas_masterflag=1;
        function SmartAdServer(sas_pageid,sas_formatid,sas_target) {
         if (sas_masterflag==1) {sas_masterflag=0;sas_master='M';} else {sas_master='S';};
         document.write('<scr'+'ipt src="http://www7.smartadserver.com/call/pubj/' + sas_pageid + '/' + sas_formatid + '/' + sas_master + '/' + sas_tmstp + '/' + escape(sas_target) + '?"></scr'+'ipt>');
        }
        </script>
        <script type="text/javascript" src="//cdn.behavioralengine.com/adexonsmart"></script>
    </head>
    <body role="document">
        <div id="fb-root"></div>
        <noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-KT7255" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-KT7255');</script>
        <!-- End Google Tag Manager -->
        <script type="text/javascript">
        setTimeout(function(){var a=document.createElement("script");
        var b=document.getElementsByTagName("script")[0];
        a.src=document.location.protocol+"//script.crazyegg.com/pages/scripts/0027/2685.js?"+Math.floor(new Date().getTime()/3600000);
        a.async=true;a.type="text/javascript";b.parentNode.insertBefore(a,b)}, 1);
        
        </script>
        [placeholder:top]
        <div class="m-artifact">
            <div class='main'>
                <h1>{{$arrArtifact.strTitle}}</h1>
                [placeholder:above-elements]
                {{IF $arrArtifact.strBriefDescription}}
                    <p class="global-desc">{{$arrArtifact.strBriefDescription}}</p>
                {{END if-list}}
                <ol class="elements">
                    [placeholder:elements]
                </ol>
                [placeholder:below-elements]
                <h6>Zobacz inne galerie:</h6>
                [placeholder:see-also]
            </div>
            <div class='sidebar'>
                [placeholder:sidebar]
            </div>
        </div>
        [placeholder:foot]
        {{this::renderUserJs()}}
    </body>
</html>