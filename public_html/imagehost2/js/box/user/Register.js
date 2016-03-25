/* global Subframe, head, JsonRpc2, objSearch */
Subframe.Box.user_Register = function () {
    "use strict";

    var $this = this;
    this.objFormData = {};
    this.objLoader = {};
    this.objFacebookConnectModel = {};

    this.init = function () {
        $this.objLoader = new Subframe.Lib.Loader();
    };


    
    this.launch = function () {
        $this.initSignUp();
        head.load('/imagehost2/js/model/FacebookConnect.js', function () {
            $this.objFacebookConnectModel = new Subframe.Model.FacebookConnect();
            $this.initFacebookConnect();
        });
    };
    
    this.initFacebookConnect = function () {
        $('.sign-up-contents .btn-facebook').click(function() {
            $this.objLoader.add();
            $this.objFacebookConnectModel.showFacebookConnect($this, 'facebookConnectCallback');
        });
    };
    
    
    this.facebookConnectCallback = function (objResponse) {
        if (!empty(objResponse.error)) {
            $this.objLoader.remove();
        } else {
             setTimeout(function () {
                window.location.replace(objResponse.result.strRedirectTo);
            }, 1000);
        };
    };
    
    this.initSignUp = function () {
        $this.elContent = $('.sign-up-contents');
        $this.elContent.find('form').submit(function (e) {
            e.stopPropagation();
            $this.objLoader.add();
            
            $this.objFormData.strEmail = $.trim($this.elContent.find('input[name="email"]').val().toLowerCase());
            $this.objFormData.strUsername = $.trim($this.elContent.find('input[name="username"]').val().toLowerCase());
            $this.objFormData.strPassword = $.trim($this.elContent.find('input[name="password"]').val());
            $this.objFormData.strRepassword = $.trim($this.elContent.find('input[name="repassword"]').val());
            
           if ($this.isValid($this.objFormData)) {
                head.load('http://crypto-js.googlecode.com/svn/tags/3.0.2/build/rollups/md5.js', function () {
                    JsonRpc2.post({
                        context: $this,
                        data: {
                            strEmail: $this.objFormData.strEmail, 
                            strUsername: $this.objFormData.strUsername, 
                            strPasswordHash: CryptoJS.MD5($this.objFormData.strPassword).toString()
                        }, 
                        method: 'backend.user.UserController.signUp',
                        callBack: 'signUpCallback'
                    });
                });  
           } else {
               $this.objLoader.remove();
           };
           
           return false;
        });
    };
    
    this.signUpCallback = function (objResponse) {
        var strMessage = '';
        $this.objLoader.remove();
        if(!empty(objResponse.error)) {
            strMessage = '<div class="alert alert-danger" role="alert">' + objResponse.error.join('<br />') + '</div>';
            $this.elContent.find('form').prepend(strMessage);
        } else {
            strMessage = '<div class="alert alert-success" role="alert">' + objResponse.result.join('<br />') + '</div>';
            $this.elContent.find('form').replaceWith(strMessage);
        }
    };
    
    this.isValid = function (objFormData) {
        
        var strErrorMessage = '';
        var boolPasswordError = false;
        var arrErrors = [];
        
        if ($this.elContent.find('form .alert').length > 0) {
            $this.elContent.find('form .alert').remove();
        };
        
        if (objFormData.strEmail.length === 0) {
            arrErrors.push('Nie podano adresu e-mail');
        };
        if (objFormData.strUsername.length === 0) {
            arrErrors.push('Nie podano nazwy uzytkownika');
        } else if (objFormData.strUsername.length < 3) {
            arrErrors.push('Nazwa użytkownika musi składać się z co najmniej 5 znaków');
        };
        
        if (objFormData.strPassword.length === 0) {
            arrErrors.push('Nie podano hasła');
            boolPasswordError = true;
        } else if (objFormData.strPassword.length < 5) {
            arrErrors.push('Hasło musi składac się z co namniej 5 znaków');
            boolPasswordError = true;
        };
        
        if (objFormData.strRepassword.length === 0) {
            arrErrors.push('Nie potwierdzono hasła');
            boolPasswordError = true;
        };
        
        if (boolPasswordError === false && objFormData.strPassword !== objFormData.strRepassword) {
            arrErrors.push('Podane hasła różnią się');
        };
        
        if (arrErrors.length === 0) {
            return true;
        };
        
        strErrorMessage = '<div class="alert alert-danger" role="alert">' + arrErrors.join('<br />') + '</div>';
        $this.elContent.find('form').prepend(strErrorMessage);
        
        return false;
        
    };

};
