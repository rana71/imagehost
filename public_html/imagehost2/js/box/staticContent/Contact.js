/* global Subframe, head, JsonRpc2, objSearch */
Subframe.Box.staticContent_Contact = function () {
    "use strict";

    var $this = this;
    this.objFormData = {};
    this.elContent = null;
    this.objFormData = {};
    this.elCurrentModal = null;
    this.objLoader = {};

    this.init = function () {
        $this.objLoader = new Subframe.Lib.Loader();
    };


    
    this.launch = function () {
        $this.elContent = $('.container.contact');
        
        $this.initForm();
    };
    
    this.initForm = function () {
        $this.elContent.find('form').submit(function (e) {
            e.stopPropagation();
            $this.objLoader.add();
            
            $this.objFormData.strEmail = $.trim($this.elContent.find('input[name="contact-email"]').val());
            $this.objFormData.strContent = $.trim($this.elContent.find('textarea[name="contact-content"]').val());
            $this.objFormData.strName = $.trim($this.elContent.find('input[name="contact-name"]').val());
            $this.objFormData.strSubject= $.trim($this.elContent.find('input[name="contact-subject"]').val());
            
           if ($this.isValid($this.objFormData)) {
                JsonRpc2.post({
                    context: $this,
                    data: $this.objFormData, 
                    method: 'backend.feedback.FeedbackController.send',
                    callBack: 'feedbackSentCallback'
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
        
        if (objFormData.strEmail.length === 0) {
            arrErrors.push('Nie podano adresu e-mail');
        };
        
        if (objFormData.strContent.length === 0) {
            arrErrors.push('Nie podano treści zapytania');
        } else if (objFormData.strContent.length < 10) {
            arrErrors.push('Treść musi składać się z co najmniej 10 znaków');
        };
       
        
        if (arrErrors.length === 0) {
            return true;
        };
        
        strErrorMessage = '<div class="alert alert-danger" role="alert">' + arrErrors.join('<br />') + '</div>';
        $this.elContent.find('form').prepend(strErrorMessage);
        
        return false;
        
    };
    
    this.feedbackSentCallback = function (objResponse) {
        var strMessage = '';
        
        $this.objLoader.remove();
        if(!empty(objResponse.error)) {
            strMessage = '<div class="alert alert-danger" role="alert">' + objResponse.error + '</div>';
        } else {
            $this.elContent.find('form').hide();
            strMessage = '<div class="alert alert-success" role="alert">' + objResponse.result + '</div>';
        };
        $this.elContent.find('form').before(strMessage);
    };

};
