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
        {{if $arrOpengraph.strImage}}
            <meta property="og:image" content="{{$arrOpengraph.strImage}}" />
        {{end if-list}}
        
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <link rel="stylesheet" href="/imagehost2/css/styles.css">
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
        <meta name="msvalidate.01" content="5CB07FDC5838EA4621AEDE883BBCCDC4" />
        <meta name="tradetracker-site-verification" content="8c907d9300b8ebae3c1eb9912646e55b55fa65ca" />
        <script src='https://code.jquery.com/jquery-1.11.3.min.js'></script>
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
        <!-- Google Tag Manager -->
        <noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-KT7255"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
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
        <nav class="topbar navbar navbar-default navbar-fixed-top">
            <div class="container">
                [placeholder:topbar]
            </div>
        </nav>
        [placeholder:jumbotron]
        <div class="container">
            <div class="row">
                <div class='col-md-8 file-details'>
                    <div class='panel panel-default'>
                        <div class="panel-body">
                            <div class='preview-container'>
                                [placeholder:left-inside]
                            </div>
                            <div class='row actions-bar'>
                                <div class='col-md-5'>
                                     [placeholder:stats]
                                 </div>
                                 <div class='col-md-7 text-right'>
                                     [placeholder:artifact-options]

                                 </div>
                                 <div class='row artifact-tags'>
                                     [placeholder:tags]
                                 </div>
                                 <div class='col-md-12 share-socials text-right'>
                                     [placeholder:share-socials]
                                 </div>
                            </div>
                        </div>
                    </div>
                    [placeholder:left-bottom]
                </div>
                <div class='col-md-4'>
                    [placeholder:right]
                </div>
            </div>
        </div>
        [placeholder:bottom]
        {{this::renderUserJs()}}
    </body>
</html>