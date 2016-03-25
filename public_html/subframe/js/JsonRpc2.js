/* global Subframe */
Subframe.Lib.JsonRpc2 = function() {

    var $this = this;

    this.post = function(obj) {
        var objContext = obj.context;
        var strMethodToCall = obj.callBack;
        var objExtends = obj.extends || {};
        
        var request = {};
        request.method = 'rpc2.'+objLauncher.strApplicationName + '.' + obj.method;
        request.params = $this.escapeParamsData(obj.data) || {};
        request.id = randomString();
        request.jsonrpc = "2.0";
        
        var objAjax = {
            type: 'post', 
            url: '../../rpc2.php', 
            data: JSON.stringify(request), 
            dataType: 'json', 
            complete: function(response) {
                var objResponse = response.responseJSON;
                if (objResponse.id !== request.id) {
                    alert('security error');
                };
                if (!empty(objContext) && !empty(gv(objContext, strMethodToCall))) {
                    objContext[strMethodToCall](objResponse.result);
                };
            }
        };
        
        if (!empty(objExtends.progress)) {
            objAjax.xhr = function () {
                var objXhr = new window.XMLHttpRequest();
                if (!empty(objXhr)) {
                    objXhr.upload.addEventListener("progress", function(evt) {
                        var objPartial = {
                            event: evt
                        };
                        objContext[objExtends.progress](objPartial);
                   }, false);
               };

               return objXhr;
            };
        };
        
        $.ajax(objAjax);
    };
    
    this.escapeParamsData = function (objData) {
//        var strKey = '';
//        for (strKey in objData) {
//            objData[strKey] = encodeURIComponent(objData[strKey]);
//        };
        return objData;
    };
};

JsonRpc2 = new Subframe.Lib.JsonRpc2();
