/* global Subframe, head, JsonRpc2, objSearch */
Subframe.Box.artifact_Stream = function () {
    "use strict";

    var $this = this;
    
    this.boolLoadingInProgress = false;
    this.numLoadedPages = 1;
    var elList = null;
    var elLoading = null;
//    var numShowedArtifacts = 0;
//    var numArtifactsLimit = -1;

    this.init = function () {
    };
    
    

    this.launch = function () {
        $this.adjustItemsProportion();
        $(window).on('resize', function () {
            $this.adjustItemsProportion();
        });
        $this.initStream();
    };
    
    this.initStream = function () {
        $this.elLoading = $('div.items-rows div.loading.stream-load-more');
        if (!empty($this.elLoading)) {
            $this.elList = $('div.items-rows div.items-list');
            head.load('/imagehost2/js/libs/jquery.appear.js', function () {
                $this.elLoading.bind('inview', function () {
                    $this.loadMore();
                });
            });
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
    
    this.loadMore = function () {
        $this.elLoading.unbind('inview');
        JsonRpc2.post({
            context: $this, 
            data: {
                strStreamOptionsSerialized: strStreamOptionsSerialized, 
                numPageNo: $this.numLoadedPages
            }, 
            method: 'imagehost2.box.artifact.Stream.getGrid', 
            callBack: 'getGridCallback'
        });
    };
    
    this.disableMoreStream = function () {
        $this.elLoading.remove();
    };
    
    this.getGridCallback = function (objResponse) {
        if (objResponse.result.numNewItemsCount === 0) {
            $this.disableMoreStream();
        } else {
            $this.numShowedArtifacts += objResponse.result.numNewItemsCount;
            if ($this.numArtifactsLimit > -1 && $this.numShowedArtifacts >= $this.numArtifactsLimit) {
                $this.disableMoreStream();
            };
            
            $this.elList.append(objResponse.result.strGridHtml);
            $this.adjustItemsProportion();
            
            $this.numLoadedPages++;
            $this.elLoading.bind('inview', function () {
                $this.loadMore();
            });
        };
    };
};
