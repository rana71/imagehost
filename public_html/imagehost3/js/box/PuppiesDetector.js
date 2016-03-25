/* global Subframe, head, JsonRpc2, objSearch */
Subframe.Box.PuppiesDetector = function () {
    "use strict";

    var $this = this;
    
    $this.strAntiPuppiesLastClosedTimestamp = 'imged-antipuppies-detector-closed';
    $this.numPageLoadedTimestamp = 0;
    $this.numShowEverySeconds = 60*10;
    
    this.init = function () {};


    this.launch = function () {
        head.load('https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js', function () {
            $this.numPageLoadedTimestamp = Math.floor(new Date().getTime() / 1000);
            var boolIsPuppiesBlocked = isClientBlockPuppies();
            var boolIsDetectorDisables = $this.isDetectorDisabled();

            if (boolIsPuppiesBlocked === true && boolIsDetectorDisables !== true) {
                $this.initDetector();
                $this.initClose();
            };
        });
    };
    
    this.isDetectorDisabled = function () {
        var boolIsDisabled = true;
        var numLastClosedTimestamp = $.cookie($this.strAntiPuppiesLastClosedTimestamp);;
        
        if (empty(numLastClosedTimestamp) || $this.numPageLoadedTimestamp-numLastClosedTimestamp >= $this.numShowEverySeconds) {
            boolIsDisabled = false;
        };
            
        return boolIsDisabled;
    };
    
    this.initDetector = function () {
        var objDetector = $('div.m-puppies-detector');
        objDetector.append('<strong class="head">Używasz programu blokującego reklamy!</strong>');
        objDetector.append('<p>Serwis imgED jest serwisem darmowym, utrzymującym swoją działalność tylko z reklam. Aby w pełni korzystać z serwisu - wyłącz oprogramowanie blujące reklamy (np. AdBlock). Poniżej zobaczysz jak to zrobić</p>');
        objDetector.append('<img src="http://static.imged.pl/how-to-allow-puppies.gif" alt="Jak odblokować reklamy ?" />');
        objDetector.append('<p><strong>Pamiętaj!</strong> Nic nie tracisz a pozwolisz nam się rozwijać <strong>Dziękujemy!</strong></p>');
        objDetector.append('<div class="close-link"><span>nie chcę wspierać serwisu [X]</span></div>');
        $this.showDetector();
    };
    
    this.initClose = function () {
        $('div.m-puppies-detector div.close-link span').click(function () {
            $this.disableDetector();
            $this.removeDetector();
        });
    };
    
    this.disableDetector = function () {
        $.cookie($this.strAntiPuppiesLastClosedTimestamp, $this.numPageLoadedTimestamp, { expires: 9999, path: '/' });
    };
    
    this.showDetector = function () {
        $('div.m-puppies-detector').fadeIn('fast');
    };
    
    this.removeDetector = function () {
        $('div.m-puppies-detector').fadeOut('fast', function () {
            $(this).remove();
        });
    };
    
    
};
