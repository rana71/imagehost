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
        $('div.m-user div.sign-up inpu.sign-fb').click(function () {
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
        }
        ;
    };

    this.initSignUp = function () {
        $this.elContent = $('div.m-user div.sign-up form');
        $this.elContent.submit(function (e) {
            e.stopPropagation();
            e.preventDefault();
            $this.elContent.find('.error-info').remove();
            $this.elContent.find('.alert').remove();
            $this.elContent.find('.errored').removeClass('errored');

            $this.objLoader.add();

            $this.objFormData.strEmail = $.trim($this.elContent.find('input[name="email"]').val().toLowerCase());
            $this.objFormData.strUsername = $.trim($this.elContent.find('input[name="username"]').val().toLowerCase());
            $this.objFormData.strPassword = $.trim($this.elContent.find('input[name="password"]').val());
            $this.objFormData.strRepassword = $.trim($this.elContent.find('input[name="repassword"]').val());
            $this.objFormData.boolSubscribe = $this.elContent.find('input[name="newsletter_check"]').is(':checked');

            if ($this.isValid($this.elContent)) {
                head.load('http://crypto-js.googlecode.com/svn/tags/3.0.2/build/rollups/md5.js', function () {
                    JsonRpc2.post({
                        context: $this,
                        data: {
                            strEmail: $this.objFormData.strEmail,
                            strUsername: $this.objFormData.strUsername,
                            strPasswordHash: CryptoJS.MD5($this.objFormData.strPassword).toString(),
                            boolSubscribe: $this.objFormData.boolSubscribe
                        },
                        method: 'backend.user.UserController.signUp',
                        callBack: 'signUpCallback'
                    });
                });
            } else {
                $this.objLoader.remove();
            }
            return false;
        });
        $this.elContent.find('input[type="submit"]').removeAttr('disabled').attr('value', $this.elContent.find('input[type="submit"]').data('value'));
    };

    this.signUpCallback = function (objResponse) {
        var strMessage = '';
        $this.objLoader.remove();
        if (!empty(objResponse.error)) {
            strMessage = '<div class="alert alert-danger" role="alert">' + objResponse.error.join('<br />') + '</div>';
            $this.elContent.prepend(strMessage);
        } else {
            strMessage = '<div class="alert alert-success" role="alert">' + objResponse.result.join('<br />') + '</div>';
            $this.elContent.replaceWith(strMessage);
        }
    };

    this.isValid = function (elForm) {
        var boolIsValid = true;

        var strEmail = $.trim(elForm.find('input[name="email"]').val().toLowerCase());
        var strUsername = $.trim(elForm.find('input[name="username"]').val().toLowerCase());
        var strPassword = $.trim(elForm.find('input[name="password"]').val());
        var strRepassword = $.trim(elForm.find('input[name="repassword"]').val());

        if (strEmail.length === 0) {
            boolIsValid = false;
            elForm.find('input[name="email"]').addClass('errored');
            elForm.find('input[name="email"]').closest('div.col').append('<div class="error-info">Nie podano adresu e-mail</div>');
        } else if (strEmail.indexOf('@') === -1) {
            boolIsValid = false;
            elForm.find('input[name="email"]').addClass('errored');
            elForm.find('input[name="email"]').closest('div.col').append('<div class="error-info">Nieprawidłowy format adresu e-mail</div>');
        }

        if (strUsername.length === 0) {
            boolIsValid = false;
            elForm.find('input[name="username"]').addClass('errored');
            elForm.find('input[name="username"]').closest('div.col').append('<div class="error-info">Nie podano nazwy uzytkownika</div>');
        } else if (strUsername.length < 3) {
            boolIsValid = false;
            elForm.find('input[name="username"]').addClass('errored');
            elForm.find('input[name="username"]').closest('div.col').append('<div class="error-info">Nazwa użytkownika musi składać się z co najmniej 3 znaków</div>');
        }

        var boolPasswordError = false;

        if (strPassword.length === 0) {
            boolIsValid = false;
            boolPasswordError = true;
            elForm.find('input[name="password"]').addClass('errored');
            elForm.find('input[name="password"]').closest('div.col').append('<div class="error-info">Nie podano hasła</div>');
        } else if (strPassword.length < 5) {
            boolIsValid = false;
            boolPasswordError = true;
            elForm.find('input[name="password"]').addClass('errored');
            elForm.find('input[name="password"]').closest('div.col').append('<div class="error-info">Hasło musi składac się z co namniej 5 znaków</div>');
        }

        if (boolPasswordError === false) {
            if (strRepassword.length === 0) {
                boolIsValid = false;
                elForm.find('input[name="repassword"]').addClass('errored');
                elForm.find('input[name="repassword"]').closest('div.col').append('<div class="error-info">Nie potwierdzono hasła</div>');
            } else if (strRepassword !== strPassword) {
                boolIsValid = false;
                elForm.find('input[name="repassword"]').addClass('errored');
                elForm.find('input[name="repassword"]').closest('div.col').append('<div class="error-info">Podane hasła różnią się</div>');
            }
        }
        ;

        return boolIsValid;
    };

};
