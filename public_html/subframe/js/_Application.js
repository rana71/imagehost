/* global Subframe */
Subframe.Lib.Application = function(strApplicationName) {
    "use strict";
    
    var $this = this;
    
    this.strName = strApplicationName;

    this.objScriptInstances = {};
    
    this.init = function () {
        var objApplicationContext = Subframe[$this.strName];
        var strScriptName = '';
        
        for (strScriptName in objApplicationContext) {
            if (strScriptName === 'modal') {
                continue;
            };
            $this.objScriptInstances[strScriptName] = new objApplicationContext[strScriptName]();
            $this.objScriptInstances[strScriptName].init();
        };
    };
    
    this.launch = function () {
        var strScriptName = '';
        
        for (strScriptName in $this.objScriptInstances) {
            if (strScriptName === 'modal') {
                continue;
            };
            $this.objScriptInstances[strScriptName].launch();
        };
    };
    
};