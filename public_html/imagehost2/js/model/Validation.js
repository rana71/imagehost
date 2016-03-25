Subframe.Model.Validation = function () {
    "use strict";
    var $this = this;
    
    
    this.email = function (strInputEmail) {
        var boolResult = false;
        var regRegex = /\S+@\S+\.\S+/;
        boolResult = regRegex.test(strInputEmail);
        return boolResult;
    };
};