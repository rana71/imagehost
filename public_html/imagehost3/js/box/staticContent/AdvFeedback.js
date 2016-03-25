/* global Subframe, head, JsonRpc2, objSearch */
Subframe.Box.staticContent_AdvFeedback = function () {
    "use strict";

    var $this = this;
//    this.objFormData = {};
    this.elContent = null;
    this.objFormData = {};
    this.objLoader = {};
    this.elForm = null;

    this.init = function () {
        $this.objLoader = new Subframe.Lib.Loader();
    };

    this.launch = function () {
        $this.elContent = $('div.m-static');
        
        $this.initForm();
    };
    
    this.initForm = function () {
        $this.elForm = $this.elContent.find('form');
        
        $this.elForm.submit(function (e) {
            e.stopPropagation();
            e.preventDefault();
            
            $this.elForm.find('.error-info').remove();
            $this.elForm.find('.errored').removeClass('errored');
            $this.objLoader.add();
            
           if ($this.isValid($this.elForm)) {
                JsonRpc2.post({
                    context: $this,
                    data: {
                        strEmail: $this.elForm.find('input[name="contact-email"]').val(), 
                        strContent: $this.elForm.find('textarea[name="contact-content"]').val(), 
                        strWww: $this.elForm.find('input[name="contact-www"]').val(), 
                        strName: $this.elForm.find('input[name="contact-name"]').val()
                    }, 
                    method: 'backend.feedback.FeedbackController.sendAdvertisement',
                    callBack: 'feedbackSentCallback'
                });

           } else {
               $this.objLoader.remove();
           };
           
           return false;
        });
        $this.elForm.find('input[type="submit"]').removeAttr('disabled').attr('value', $this.elContent.find('form').find('input[type="submit"]').data('value'));
    };


    this.isValid = function (elForm) {
        
        var boolIsValid = true;
        
        var strEmail = elForm.find('input[name="contact-email"]').val();
        var strContent = elForm.find('textarea[name="contact-content"]').val();
        
        if (strEmail.length === 0) {
            boolIsValid = false;
            elForm.find('input[name="contact-email"]').addClass('errored');
            elForm.find('input[name="contact-email"]').closest('div.r').append('<div class="error-info">Nie podano adresu e-mail</div>');
        } else if (strEmail.indexOf('@') === -1) {
            elForm.find('input[name="contact-email"]').addClass('errored');
            elForm.find('input[name="contact-email"]').closest('div.r').append('<div class="error-info">Nieprawidłowy format adresu e-mail</div>');
        }
        
        if (strContent.length === 0) {
            boolIsValid = false;
            elForm.find('textarea[name="contact-content"]').addClass('errored');
            elForm.find('textarea[name="contact-content"]').closest('div.r').append('<div class="error-info">Nie podano treści zapytania</div>');
        } else if (strContent.length < 10) {
            elForm.find('textarea[name="contact-content"]').addClass('errored');
            elForm.find('textarea[name="contact-content"]').closest('div.r').append('<div class="error-info">Treść musi składać się z co najmniej 10 znaków</div>');
        }
        
        return boolIsValid;
        
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
        $this.elContent.find('form').replaceWith(strMessage);
    };

};
