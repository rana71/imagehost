Subframe.Box.newsletter_SmallForm = function () {

    "use strict";

    var $this = this;

    this.elNewsLetterForm = null;
    $this.elCheckbox = null;
    this.elEmailInput = null;
    $this.objLoader = {};
    this.objFormData = {};

    this.init = function () {
        $this.objLoader = new Subframe.Lib.Loader();
    };

    this.launch = function () {
        $this.initNewsLetterForm();
    };

    this.initNewsLetterForm = function () {
        $this.elNewsLetterForm = $('div.m-newsletter.small-form form');
        $this.elEmailInput = $this.elNewsLetterForm.find('input[name="email"]');
        $this.elCheckbox = $this.elNewsLetterForm.find('input[name="newsletter_check"]');
        $this.elNewsLetterForm.submit(function (e) {
            e.stopPropagation();
            e.preventDefault();
            $this.clearValidationErrors();
            if ($this.isNewsLetterFormValid()) {
                $this.objLoader.add();
                $this.objFormData.strEmail = $.trim($this.elEmailInput.val()).toLowerCase();
                JsonRpc2.post({
                    context: $this,
                    data: {
                        strEmail: $this.objFormData.strEmail
                    },
                    method: 'backend.newsletter.NewsletterController.subscribe',
                    callBack: 'subscribeCallback'
                });
            }
            return false;
        });
    };

    this.subscribeCallback = function (objResponse) {
        $this.objLoader.remove();
        if (!empty(objResponse.error)) {
            $this.showBackendValidationError($this.elEmailInput, objResponse.error.join('<br />'));
        } else {
            $this.showSuccessMessage($this.elEmailInput, objResponse.result.join('<br />'));
            
        }
    };

    this.isNewsLetterFormValid = function () {
        var boolIsValid = true;
        var strEmail = $.trim($this.elEmailInput.val()).toLowerCase();
        var re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        if (strEmail.length === 0) {
            boolIsValid = false;
            $this.showValidationError($this.elEmailInput, 'Nie podano adresu e-mail');
        }
        else if (!re.test(strEmail)) {
            boolIsValid = false;
            $this.showValidationError($this.elEmailInput, 'Nieprawidłowy format adresu e-mail');
        }
        return boolIsValid;
    };

    this.showValidationError = function (elError, strMessage) {
        elError.addClass('errored');
        elError.closest('div.r').append('<div class="error-info">' + strMessage + '</div>');
    };

    this.showBackendValidationError = function (elError, strMessage) {
        elError.closest('div.r').append('<div class="alert alert-danger" role="alert">' + strMessage + '</div>');
    };

    this.showSuccessMessage = function (elSuccess, strMessage) {
        elSuccess.closest('div.r').append('<div class="alert alert-success" role="alert">' + strMessage + '</div>');
    };

    this.clearValidationErrors = function () {
        $this.elNewsLetterForm.find('.error-info').remove();
        $this.elNewsLetterForm.find('.alert').remove();
        $this.elNewsLetterForm.find('.errored').removeClass('errored');
    };

};

