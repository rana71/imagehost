/* global Subframe, head, JsonRpc2, objSearch */
Subframe.Box.user_ForgotPassword = function () {
    "use strict";

    var $this = this;
    this.elContent = null;
    this.objFormData = {};
    this.objLoader = {};
    
    this.init = function () {
        $this.objLoader = new Subframe.Lib.Loader();
    };


    
    this.launch = function () {
        $this.elContent = $('div.m-user div.main-content');
        $this.initPasswordRecovering();
    };
    
    this.initPasswordRecovering = function () {
        $this.elContent.find('form').submit(function (e) {
            e.stopPropagation();
            e.preventDefault();
            
            $this.elContent.find('.error-info').remove();
            $this.elContent.find('.alert').remove();
            $this.elContent.find('.errored').removeClass('errored');
            
            $this.objLoader.add();
            
            $this.objFormData.strEmail = $.trim($this.elContent.find('input[name="email"]').val());
            
           if ($this.isValid($this.elContent.find('form'))) {
               
               JsonRpc2.post({
                    context: $this,
                    data: {
                        strEmail: $this.objFormData.strEmail
                    }, 
                    method: 'backend.user.UserController.resetPassword',
                    callBack: 'resetPasswordCallback'
                });
               
           } else {
               $this.objLoader.remove();
           };
           
           return false;
        });
    };
    
    
    this.resetPasswordCallback = function (objResponse) {
        var strMessage = '';
        $this.objLoader.remove();
        if(!empty(objResponse.error)) {
            strMessage = '<div class="alert alert-danger" role="alert">' + objResponse.error + '</div>';
            $this.elContent.find('form').prepend(strMessage);
        } else {
            strMessage = '<div class="alert alert-success" role="alert">' + objResponse.result + '</div>';
            $this.elContent.find('form').replaceWith(strMessage);
        }
    };
    
    
    this.isValid = function (elForm) {
        var boolIsValid = true;
        
        var strEmail = $.trim(elForm.find('input[name="email"]').val().toLowerCase());
        
        if (strEmail.length === 0) {
            boolIsValid = false;
            elForm.find('input[name="email"]').addClass('errored');
            elForm.find('input[name="email"]').closest('div.col').append('<div class="error-info">Nie podano adresu e-mail</div>');
        } else if (strEmail.indexOf('@') === -1) {
            boolIsValid = false;
            elForm.find('input[name="email"]').addClass('errored');
            elForm.find('input[name="email"]').closest('div.col').append('<div class="error-info">Nieprawidłowy format adresu e-mail</div>');
        }
        
        
        return boolIsValid;
        
    };
    
};
