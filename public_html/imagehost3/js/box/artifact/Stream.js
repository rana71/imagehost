/* global Subframe, head, JsonRpc2, objSearch, strStreamOptionsSerialized */
Subframe.Box.artifact_Stream = function () {
    "use strict";

    var $this = this;
    
    this.boolLoadingInProgress = false;
    this.numLoadedPages = 1;
    this.elList = null;
    this.elLoading = null;
    
    this.arrPuppiesThumbs = [
        'http://static.imged.pl/allow-puppies-1-small.jpg', 
        'http://static.imged.pl/allow-puppies-2-small.jpg', 
        'http://static.imged.pl/allow-puppies-3-small.jpg'
    ];

    this.init = function () {
    };
    
    

    this.launch = function () {
        $this.elList = $('div.m-stream div.items-list');
        
        $this.adjustItemsProportion();
        $(window).on('resize', function () {
            $this.adjustItemsProportion();
        });
        $this.initStream();
        $this.checkForPuppiesBlocked();
    };
    
    this.checkForPuppiesBlocked = function () {
        var boolIsPuppiesBlocked = isClientBlockPuppies();
        if (boolIsPuppiesBlocked === true) {
            $this.replacePuppiesBlockedThumbs($this.elList);
        };
    };
    
    this.initStream = function () {
        $this.elLoading = $('div.m-stream .loading');    
        if (!empty($this.elLoading)) {
            head.load('/bower_components/protonet/jquery.inview/jquery.inview.js', function () {
                $this.elLoading.bind('inview', $this.loadingInview);
            });
        };
    };
    
    this.initStreamByButton = function () {
        $this.elLoading = $('div.m-stream .load-button');
        if (!empty($this.elLoading)) {
            $this.elList = $('div.m-stream div.items-list');
            $this.elLoading.click($this.loadingInview);
        };
    };
    
    this.loadingInview = function () {
        if (!$this.elLoading.hasClass('working')) {
            $this.elLoading.addClass('working');
            $this.loadMore('getGridCallback');
        };
    };
    
    this.adjustItemsProportion = function () {
        var arrElements = $('div.items-list .item');
        arrElements.removeClass('vertical');
        arrElements.each(function (mulNull, elElement) {
            if ($(elElement).outerHeight() + 40  > $(elElement).outerWidth()) {
                $(this).addClass('vertical');
            };
        });
    };
    
    this.loadMore = function (strCallback) {
        JsonRpc2.post({
            context: $this, 
            data: {
                strStreamOptionsSerialized: strStreamOptionsSerialized, 
                numPageNo: $this.numLoadedPages
            }, 
            method: 'imagehost3.box.artifact.Stream.getGrid', 
            callBack: strCallback
        });
    };
    
    this.disableMoreStream = function () {
        $this.elLoading.remove();
    };
    
    this.getGridCallback = function (objResponse) {
        if (objResponse.result.numNewItemsCount === 0) {
            $this.disableMoreStream();
        } else {
            var elNewList = null;
            var boolIsPuppiesBlocked = isClientBlockPuppies();
            
            $this.numShowedArtifacts += objResponse.result.numNewItemsCount;
            if ($this.numArtifactsLimit > -1 && $this.numShowedArtifacts >= $this.numArtifactsLimit) {
                $this.disableMoreStream();
            };
            elNewList = $(objResponse.result.strGridHtml);
            
            if (boolIsPuppiesBlocked === true) {
                $this.replacePuppiesBlockedThumbs(elNewList);
            };
            
            $this.elList.append(elNewList);
            $this.numLoadedPages++;
            $this.elLoading.removeClass('working');
        };
    };
    
    this.replacePuppiesBlockedThumbs = function (elList) {
        var numPuppiesThumbsIterator = 0;
        elList.find('img.thumb').each(function () {
            $(this).attr('src', $this.arrPuppiesThumbs[numPuppiesThumbsIterator]);
            numPuppiesThumbsIterator++;
            if (numPuppiesThumbsIterator === $this.arrPuppiesThumbs.length) {
                numPuppiesThumbsIterator = 0;
            };
        });
    };
};
