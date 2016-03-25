<!DOCTYPE html>
<html lang="pl">
    <head>
        <base href='{{this::baseUrl()}}/' />
        <meta charset="UTF-8" />
        <meta name="robots" content="{{$robots}}" />
        <meta name="googlebot" content="{{$googlebot}}" />
        <title>{{title}}</title>
        <meta name="description" content="{{$description}}" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        {{this::cssControllerRender()}}
        <meta http-equiv="Content-Security-Policy" content="default-src * data: 'unsafe-inline' 'unsafe-eval'" />
        <meta name="msvalidate.01" content="5CB07FDC5838EA4621AEDE883BBCCDC4" />
        <meta name="tradetracker-site-verification" content="8c907d9300b8ebae3c1eb9912646e55b55fa65ca" />
        <meta name="p:domain_verify" content="347d12f9ee7fbbbff21b47c377a5a1ce"/>
        
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
        
        [placeholder:top]
        [placeholder:main]
        [placeholder:foot]
       
        
        <div class="modal fade modal-activation-nok" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Aktywacja konta nieudana</h4>
                    </div>
                    <div class="modal-body">
                        Twoje konto nie mogło być aktywowane.<br />Prawdopodobnie link aktywacyjny wygasł. Pamiętaj, masz tylko 72 godziny na potwierdzenie swojego adresu e-mail. Prosimy o ponowną rejestrację konta.
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade modal-activation-ok" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Aktywacja konta</h4>
                    </div>
                    <div class="modal-body">
                        Twoje konto zostało aktywowane, możesz się na nie zalogować
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade modal-subscription-nok" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel2">Aktywacja newslettera</h4>
                    </div>
                    <div class="modal-body">
                        Coś poszło nie tak :(<br />
                        Jeśli problem będzie się powtarzał skontaktuj się z nami za pomocą formularza kontaktowego!
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade modal-subscription-ok" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel1">Aktywacja newslettera</h4>
                    </div>
                    <div class="modal-body">
                        Twój adres e-mail został poprawnie aktywowany.<br />
                        Od tej chwili co jakiś czas (bez obaw - nie za często) będziesz otrzymywał od nas wiadomości e-mail z najciekawszymi zdjęciami.
                    </div>
                </div>
            </div>
        </div>
        
        {{this::renderUserJs()}}
        
        
    </body>
</html>