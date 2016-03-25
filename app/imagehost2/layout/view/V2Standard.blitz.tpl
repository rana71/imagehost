<!DOCTYPE html>
<html lang="pl">
    <head>
        <base href='{{this::baseUrl()}}/' />
        <meta charset="UTF-8" />
        <meta name="robots" content="{{$robots}}" />
        <meta name="googlebot" content="{{$googlebot}}" />
        <title>{{title}}</title>
        <meta name="description" content="{{$description}}" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/imagehost2/css/styles.css">
        
        <link rel="stylesheet" href="/imagehost2/css/v2-top.css">
        <link rel="stylesheet" href="/imagehost2/css/v2-welcome.css">
        <link rel="stylesheet" href="/imagehost2/css/stream.css">
        <link rel="stylesheet" href="/imagehost2/css/footer.css">
        
        <!--<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">-->
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
        [placeholder:topbar]
        [placeholder:main]
        [placeholder:footer]
        
        {{this::renderUserJs()}}
        <div id='fb-root'></div>
    </body>
</html>