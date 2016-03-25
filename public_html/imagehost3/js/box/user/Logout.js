/* global Subframe, head, JsonRpc2, objSearch, FB */
Subframe.Box.user_Logout = function () {
    "use strict";

    var $this = this;
    
    $this.numMilisecondsDelay = 3000;
    
    this.init = function () {};

    this.launch = function () {
        setTimeout(function () {
            window.location.replace('/'); 
        }, $this.numMilisecondsDelay);
        
    };

};
