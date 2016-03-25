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
        $this.elContent = $('.forgot-password-contents');
        $this.initPasswordRecovering();
    };
    
    this.initPasswordRecovering = function () {
        $this.elContent.find('form').submit(function (e) {
            e.stopPropagation();
            $this.objLoader.add();
            
            $this.objFormData.strEmail = $.trim($this.elContent.find('input[name="email"]').val());
            
           if ($this.isValid($this.objFormData)) {
               
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
    
    
    this.isValid = function (objFormData) {
        
        var strErrorMessage = '';
        var arrErrors = [];
        
        if ($this.elContent.find('form .alert').length > 0) {
            $this.elContent.find('form .alert').remove();
        };
        
        if (objFormData.strEmail.length === 0) {
            arrErrors.push('Nie podano adresu e-mail');
        };
        
        if (arrErrors.length === 0) {
            return true;
        };
        
        strErrorMessage = '<div class="alert alert-danger" role="alert">' + arrErrors.join('<br />') + '</div>';
        $this.elContent.find('form').prepend(strErrorMessage);
        
        return false;
        
    };
    
};
