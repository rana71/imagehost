Subframe.Lib.BoxController = function (strApplicationName) {
    "use strict";
    var $this = this;
    this.objBoxes = {};
    
    $this.strApplicationName = strApplicationName;
    
    this.loadBox = function (strBoxFilename) {
        var strBoxName = strBoxFilename.replace('/', '_');
        var strBoxFilePath = '/'+$this.strApplicationName+'/js/box/'+strBoxFilename+'.js';
        head.load(strBoxFilePath, function (a) {
            var objBoxPrototype = Subframe.Box[strBoxName];
            
            $this.objBoxes[strBoxName] = new objBoxPrototype();
            $this.objBoxes[strBoxName].init();
            $this.objBoxes[strBoxName].launch();
        });
    };
    
    this.init = function (arrBoxes) {
        $(arrBoxes).each(function (mulNull, strBoxFilename) {
            $this.loadBox(strBoxFilename);
        });
    };
};