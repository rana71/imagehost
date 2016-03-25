Subframe.Lib.JsonRpc = function(strAppName) {

    var $this = this;
    $this.strAppName = strAppName;

    this.post = function(obj) {
//        var objContext = obj.context;
        var strMethodToCall = obj.callBack;
//        console.log(obj);
        var request = {};
        request.method = $this.strAppName + '/' + obj.method;
        request.params = obj.data || {};
        request.id = randomString();
        request.jsonrpc = "2.0";
        console.log(request);
        
        jQuery.post('/rpc-admin.php', JSON.stringify(request), function(response) {
//            
            if (response.id !== request.id) {
                throw new Exception("Security Error");
            };

            if (!empty(strMethodToCall)) {
                strMethodToCall(response.result);
//                objContext[strMethodToCall](response.result);
            };
//            if (!empty(objContext) && !empty(gv(objContext, strMethodToCall))) {
//                objContext[strMethodToCall](response.result);
//            };
//        
        }, "json");

    };
};

var JsonRpc = new Subframe.Lib.JsonRpc('admin');