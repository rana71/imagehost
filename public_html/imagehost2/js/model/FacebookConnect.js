Subframe.Model.FacebookConnect = function () {
    "use strict";
    var $this = this;
    
    
    this.showFacebookConnect = function (objCallbackContext, strCallbackMethod) {
        FB.login(function (objResponse) {
            if (objResponse.status === 'connected') {
                FB.api('/me', function (objUserData) {
                    var strFacebookId = objUserData.id;
                    var strFacebookEmail = objUserData.email; 
                    var strFacebookName = objUserData.first_name;
                    JsonRpc2.post({
                        context: objCallbackContext,
                        data: {
                            strFacebookId: strFacebookId, 
                            strFacebookEmail: strFacebookEmail, 
                            strFacebookName: strFacebookName
                        }, 
                        method: 'backend.user.UserController.signInWithFacebook',
                        callBack: strCallbackMethod
                    });
                });
            } else {
                objCallbackContext[strCallbackMethod]({error: true});
            };
        }, {scope: 'public_profile,email'});
    };
};