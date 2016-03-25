/* global Subframe, head, JsonRpc */
Subframe.Lib.Loader = function() {
    "use strict";
    
    var $this = this;
    this.numLoadersVisible = 0;
    this.elLoader = null;
    this.objShowingTimeout = {};
    
    this.elLoader = null;
    this.elProgressBar = null;
   
//    this.launch = function () {
        $this.elLoader = $('<div class="loader disabled"></div>');
        $this.elLoader.appendTo($('body'));
//    };
    
    this.createProgressBar = function () {
        var elProgressBar = $('<div class="progress">\
                <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%; min-width: 2em;">0%</div>\
            </div>');
        $this.elProgressBar = elProgressBar;
        $this.elLoader.append(elProgressBar);
    };
    
    this.updateProgressBar = function (numLoadedPart) {
        if (empty($this.elProgressBar)) {
            $this.createProgressBar();
        };
        var numPercents = Math.round(numLoadedPart * 100);
        $this.elProgressBar.find('div.progress-bar').attr({
            'aria-valuenow': numPercents
        }).css({
            width: numPercents+'%'
        }).text(numPercents+'%');
    };
    
    this.add = function () {
        $this.numLoadersVisible++;
        if ($this.numLoadersVisible === 1) {
            $this.objShowingTimeout = setTimeout($this.showLoader, 25);
        };
    };
    
    this.remove = function () {
        $this.numLoadersVisible--;
        if ($this.numLoadersVisible === 0) {
            clearTimeout($this.objShowingTimeout);
            $this.hideLoader();
        };
    };
    
    this.showLoader = function () {
        $this.elLoader.removeClass('disabled').addClass('enabled');
    };
    
    this.hideLoader = function () {
        $this.elLoader.removeClass('enabled').addClass('disabled');
    }
    
};