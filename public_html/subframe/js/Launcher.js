/* global head */

var Subframe = {};
Subframe.Lib = {};
Subframe.Apps = {};
Subframe.Box = {};
Subframe.Model = {};
JsonRpc2 = {};

Subframe.Lib.Launcher = function (strApplicationName, strBaseUrl, arrCurrentBoxes) {
    "use strict";

    var $this = this;
    
    this.strApplicationName = strApplicationName;
    this.strBaseUrl = strBaseUrl;
    this.arrCurrentBoxes = arrCurrentBoxes;
    this.objApplication = {};

    this.init = function () {
        var strAppliactionBootstrapFile = '/'+$this.strApplicationName+'/js/'+$this.strApplicationName+'.js';
        head.load(strAppliactionBootstrapFile, function () {
            $this.objApplication = new Subframe.Apps[$this.strApplicationName]($this.arrCurrentBoxes);
            $this.objApplication.init();
            var arrScriptsToLoad = [];
            if (typeof $this.objApplication.arrBootstrapFiles !== undefined) {
                arrScriptsToLoad = $this.objApplication.arrBootstrapFiles;
            };
            head.load(arrScriptsToLoad, function () {
                $this.objApplication.loaded();
            });
        });
    };
};


