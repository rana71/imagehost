/* global Subframe, head, JsonRpc2, objSearch, FB */
Subframe.Box.user_Login = function () {
    "use strict";

    var $this = this;
    this.objFormData = {};
    this.elContent = null;
    this.objFormData = {};
    this.elCurrentModal = null;
    this.objLoader = {};
    this.objFacebookConnectModel = {};

    this.init = function () {
        $this.objLoader = new Subframe.Lib.Loader();
    };


    
    this.launch = function () {
        $this.elContent = $('.sign-in-contents');
        $this.initAccountActivation();
        $this.initLogin();
        head.load('/imagehost2/js/model/FacebookConnect.js', function () {
            $this.objFacebookConnectModel = new Subframe.Model.FacebookConnect();
            $this.initFacebookConnect();
        });
    };
    
    this.initFacebookConnect = function () {
        $('.sign-in-contents .btn-facebook').click(function() {
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
    
    this.sucessfullyLogged = function (strRedirectTo) {
        setTimeout(function () {
            window.location.replace(strRedirectTo);
        }, 1000);
    };
    
    this.initAccountActivation = function () {
        if (!empty(window.location.hash.replace('#', ''))) {
            if (window.location.hash.replace('#', '') === 'activation-nok') {
                $this.elCurrentReportModal = $(".modal-activation-nok").clone();
            } else if (window.location.hash.replace('#', '') === 'activation-ok') {
                $this.elCurrentReportModal = $(".modal-activation-ok").clone();
                window.location.hash = '';
            };
            $this.elCurrentReportModal.on('hidden.bs.modal', function (e) {
                $this.elCurrentReportModal.remove();
            });
            $this.elCurrentReportModal.modal('show');
        };
    };
    
    this.initLogin = function () {
        $this.elContent.find('form').submit(function (e) {
            e.stopPropagation();
            $this.objLoader.add();
            
            $this.objFormData.strUsername = $.trim($this.elContent.find('input[name="username"]').val());
            $this.objFormData.strPassword = $.trim($this.elContent.find('input[name="password"]').val());
            
           if ($this.isValid($this.objFormData)) {
                head.load('http://crypto-js.googlecode.com/svn/tags/3.0.2/build/rollups/md5.js', function () {
                    JsonRpc2.post({
                        context: $this,
                        data: {
                            strUsername: $this.objFormData.strUsername, 
                            strPasswordHash: CryptoJS.MD5($this.objFormData.strPassword).toString()
                        }, 
                        method: 'backend.user.UserController.signIn',
                        callBack: 'signInCallback'
                    });
                    
                });
           } else {
               $this.objLoader.remove();
           };
           
           return false;
        });
    };


    this.isValid = function (objFormData) {
        
        var strErrorMessage = '';
        var arrErrors = [];
        
        if ($this.elContent.find('form .alert').length > 0) {
            $this.elContent.find('form .alert').remove();
        };
        
        if (objFormData.strUsername.length === 0) {
            arrErrors.push('Nie podano nazwy uzytkownika');
        } else if (objFormData.strUsername.length < 3) {
            arrErrors.push('Nazwa użytkownika musi składać się z co najmniej 5 znaków');
        };
        
        if (objFormData.strPassword.length === 0) {
            arrErrors.push('Nie podano hasła');
        } else if (objFormData.strPassword.length < 5) {
            arrErrors.push('Hasło musi składac się z co namniej 5 znaków');
        };
        
        if (arrErrors.length === 0) {
            return true;
        };
        
        strErrorMessage = '<div class="alert alert-danger" role="alert">' + arrErrors.join('<br />') + '</div>';
        $this.elContent.find('form').prepend(strErrorMessage);
        
        return false;
        
    };
    
    this.signInCallback = function (objResponse) {
        if(!empty(objResponse.error)) {
            var strMessage = '';
            $this.objLoader.remove();
            strMessage = '<div class="alert alert-danger" role="alert">' + objResponse.error.join('<br />') + '</div>';
            $this.elContent.find('form').prepend(strMessage);
        } else {
            $this.sucessfullyLogged(objResponse.result.strRedirectTo);
        };
    };

};
