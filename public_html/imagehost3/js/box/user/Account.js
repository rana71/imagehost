/* global Subframe, head, JsonRpc2, objSearch */
Subframe.Box.user_Account = function () {
    "use strict";

    var $this = this;
    this.objFormData = {};
    this.elContent = null;
    this.objFormData = {};
    this.elCurrentModal = null;

    this.init = function () {
        $this.objLoader = new Subframe.Lib.Loader();
    };


    
    this.launch = function () {
        $this.elContent = $('div.m-user div.main-content');
        
        $this.initChangePassword();
        $this.initChangeEmail();
    };
    
    this.initChangePassword = function () {
        $this.elContent.find('form.user-account-change-password').submit(function (e) {
            e.stopPropagation();
            $this.objLoader.add();
            $this.objFormData = {};
            $this.objFormData.strOldPassword = $.trim($this.elContent.find('input[name="current_password"]').val());
            $this.objFormData.strNewPassword = $.trim($this.elContent.find('input[name="new_password"]').val());
            $this.objFormData.strNewPassword2 = $.trim($this.elContent.find('input[name="new_password2"]').val());

            if ($this.isValidChangePasswordForm($this.objFormData)) {
                head.load('http://crypto-js.googlecode.com/svn/tags/3.0.2/build/rollups/md5.js', function () {
                    
                    JsonRpc2.post({
                        context: $this,
                        data: {
                            strPassword: CryptoJS.MD5($this.objFormData.strOldPassword).toString(), 
                            strNewPassword: CryptoJS.MD5($this.objFormData.strNewPassword).toString()
                        }, 
                        method: 'backend.user.UserController.changePassword',
                        callBack: 'changePasswordCallback'
                    });
                });
            } else {
                $this.objLoader.remove();
            };

            return false;
        });
    };
    
    
    this.isValidChangePasswordForm = function (objFormData) {
        var strErrorMessage = '';
        var boolPasswordError = false;
        var arrErrors = [];
        
        if ($this.elContent.find('form.user-account-change-password').find('.alert').length > 0) {
            $this.elContent.find('form.user-account-change-password').find('.alert').remove();
        };
        
        if (objFormData.strOldPassword.length === 0) {
            arrErrors.push('Nie podano aktualnego hasła');
            boolPasswordError = true;
        } else if (objFormData.strOldPassword.length < 5) {
            arrErrors.push('Hasło musi składac się z co namniej 5 znaków');
            boolPasswordError = true;
        };
        
        if (objFormData.strNewPassword.length === 0) {
            arrErrors.push('Nie podano nowego hasła');
            boolPasswordError = true;
        } else if (objFormData.strNewPassword.length < 5) {
            arrErrors.push('Hasło musi składac się z co namniej 5 znaków');
            boolPasswordError = true;
        };
        
        if (objFormData.strNewPassword2.length === 0) {
            arrErrors.push('Nie potwierdzono nowego hasła');
            boolPasswordError = true;
        };
        
        if (boolPasswordError === false && objFormData.strNewPassword !== objFormData.strNewPassword2) {
            arrErrors.push('Podane nowe hasła różnią się');
        };
        
        if (arrErrors.length === 0) {
            return true;
        };
        
        strErrorMessage = '<div class="alert alert-danger" role="alert">' + arrErrors.join('<br />') + '</div>';
        $this.elContent.find('form.user-account-change-password').prepend(strErrorMessage);
        
        return false;
    };
    
    this.changePasswordCallback = function (objResponse) {
        var strMessage = '';
        $this.objLoader.remove();
        if(!empty(objResponse.error)) {
            strMessage = '<div class="alert alert-danger" role="alert">' + objResponse.error.join('<br />') + '</div>';
            $this.elContent.find('form.user-account-change-password').prepend(strMessage);
        } else {
            strMessage = '<div class="alert alert-success" role="alert">' + objResponse.result.join('<br />') + '</div>';
            $this.elContent.find('form.user-account-change-password').prepend(strMessage);
            $this.elContent.find('form.user-account-change-password input[type="password"]').val('');
        }
    };
    
    this.initChangeEmail = function () {
        $this.elContent.find('form.user-account-change-email').submit(function (e) {
            e.stopPropagation();
            $this.elContent.find('form.user-account-change-email .alert').remove();
            $this.objLoader.add();
            $this.objFormData = {};
            $this.objFormData.strPassword = $.trim($this.elContent.find('form.user-account-change-email input[name="password"]').val());
            $this.objFormData.strNewEmail = $.trim($this.elContent.find('form.user-account-change-email input[name="new_email"]').val());
            $this.objFormData.strNewEmail2 = $.trim($this.elContent.find('form.user-account-change-email input[name="new_email2"]').val());

            if ($this.isValidChangeEmailForm($this.objFormData)) {
                head.load('http://crypto-js.googlecode.com/svn/tags/3.0.2/build/rollups/md5.js', function () {
                    JsonRpc2.post({
                        context: $this,
                        data: {
                            strPassword: CryptoJS.MD5($this.objFormData.strPassword).toString(), 
                            strNewEmail: $this.objFormData.strNewEmail
                        },
                        method: 'backend.user.UserController.changeEmail',
                        callBack: 'changeEmailCallback'
                    });
                });
            } else {
                $this.objLoader.remove();
            };

            return false;
        });
    };
    
    
    this.isValidChangeEmailForm = function (objFormData) {
        var strErrorMessage = '';
        var boolEmailError = false;
        var arrErrors = [];
        
        if ($this.elContent.find('form.user-account-change-email').find('.alert').length > 0) {
            $this.elContent.find('form.user-account-change-email').find('.alert').remove();
        };
        
        if (objFormData.strPassword.length === 0) {
            arrErrors.push('Nie podano aktualnego hasła');
        } else if (objFormData.strPassword.length < 5) {
            arrErrors.push('Hasło musi składac się z co namniej 5 znaków');
        };
        
        if (objFormData.strNewEmail.length === 0) {
            arrErrors.push('Nie podano nowego adresu e-mail');
            boolEmailError = true;
        };
        
        if (objFormData.strNewEmail2.length === 0) {
            arrErrors.push('Nie potwierdzono nowego adresu e-mail');
            boolEmailError = true;
        };
        
        if (boolEmailError === false && objFormData.strNewEmail !== objFormData.strNewEmail2) {
            arrErrors.push('Podane adresy e-mail różnią się');
        };
        
        if (arrErrors.length === 0) {
            return true;
        };
        
        strErrorMessage = '<div class="alert alert-danger" role="alert">' + arrErrors.join('<br />') + '</div>';
        $this.elContent.find('form.user-account-change-email').prepend(strErrorMessage);
        
        return false;
    };
    
    this.changeEmailCallback = function (objResponse) {
        var strMessage = '';
        $this.objLoader.remove();
        if(!empty(objResponse.error)) {
            strMessage = '<div class="alert alert-danger" role="alert">' + objResponse.error.join('<br />') + '</div>';
            $this.elContent.find('form.user-account-change-email').prepend(strMessage);
        } else {
            strMessage = '<div class="alert alert-success" role="alert">' + objResponse.result.join('<br />') + '</div>';
            $this.elContent.find('form.user-account-change-email').prepend(strMessage);
            setTimeout(function () {
                location.reload();
            }, 1000);
        }
    };
};
