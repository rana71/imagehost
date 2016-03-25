/* global Subframe, head, JsonRpc2, objSearch */
Subframe.Box.Topbar = function () {
    "use strict";

    var $this = this;
    this.elSearchForm = null;
    this.elSearchQueryInput = null;
    this.elUploadForm = null;
    this.elCurrentUploadModal = null;
    this.elUserAcccountPopover = null;
    $this.objLoader = {};
    $this.objFacebookConnectModel = {};
    $this.objValidationModel = {};
    
    this.numAddedPhotosToStory = 0;
    this.numImagesInStoryLimit = 20;
    this.elUserModal = null;
    

    this.init = function () {
        $this.objLoader = new Subframe.Lib.Loader();
    };


    this.launch = function () {
        head.load(['/imagehost2/js/model/FacebookConnect.js'], function () {
            $this.objFacebookConnectModel = new Subframe.Model.FacebookConnect();
            $this.initUserAccountPopover();
        });
        $this.initSearcher();
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
    
    this.clickSomewhereSearcher = function (e) {
        if ($(e.target).closest('li.search').length === 0) {
            console.log('trigger');
            $('div.m-top li.search').removeClass('active');
            $(window).unbind('click', $this.clickSomewhereSearcher);
        }
    };
    
    this.initSearcher = function () {
        
        
        $('div.m-top li.search form').submit(function (e) {
//            e.preventDefault();
            $this.elSearchForm = $('div.m-top li.search form');
            $this.elSearchQueryInput = $this.elSearchForm.find('input[name="q"]');
            
            var strInput = $.trim($this.elSearchQueryInput.val());
            if (strInput.length < 3) {
                var strBorderColor = $this.elSearchForm.css('border-color');
                $this.elSearchForm.css('border-color', 'red');
                setTimeout(function () {
                    $this.elSearchForm.css('border-color', strBorderColor);
                    setTimeout(function () {
                        $this.elSearchForm.css('border-color', 'red');
                        setTimeout(function () {
                            $this.elSearchForm.css('border-color', strBorderColor);
                        }, 500);
                    }, 500);
                }, 500);
                e.preventDefault();
                return false;
            }
            ;
            $this.objLoader.add();
            return true;
        });
        
        
        $('div.m-top li.search').on('click touchstart', function (e) {
            var elSearchLi = $(this);
            if (elSearchLi.hasClass('active') === false) {
                elSearchLi.addClass('active');
                elSearchLi.find('input.q').focus();
                $(window).bind('click touchstart', $this.clickSomewhereSearcher);
            }
        });
        
        $('div.m-top li.search div.icon').on('click touchstart', function () {
            if ($(this).closest('li.search').hasClass('active')) {
                $('div.m-top li.search form').trigger('submit');
            };
        });
    };
    
    this.initUserAccountPopover = function () {
        $('div.m-top button.my-imged').click(function () {
            $this.elUserModal = $('div.m-top div.templates div#user-modal').clone();
            $this.elUserModal.on('hidden.bs.modal', function (e) {
                $this.elUserModal.remove();
            });
            $this.elUserModal.find('form').submit(function (e) {
                e.stopPropagation();
                e.preventDefault();
                $this.objLoader.add();
                $this.elUserModal.find('.alert').remove();
                $this.elUserModal.find('form .error-info').remove();
                $this.elUserModal.find('form .errored').removeClass('errored');
            
                if ($this.isLoginFormValid($this.elUserModal.find('form'))) {
                    head.load('http://crypto-js.googlecode.com/svn/tags/3.0.2/build/rollups/md5.js', function () {
                        JsonRpc2.post({
                            context: $this,
                            data: {
                                strUsername: $this.elUserModal.find('form input[name="login-username"]').val(), 
                                strPassword: CryptoJS.MD5($this.elUserModal.find('form input[name="login-password"]').val()).toString()
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
            $this.elUserModal.find('input.sign-fb').click(function() {
                $this.objLoader.add();
                $this.objFacebookConnectModel.showFacebookConnect($this, 'facebookConnectCallback');
            });
            $this.elUserModal.find('form').find('input[type="submit"]').removeAttr('disabled').attr('value', $this.elUserModal.find('form').find('input[type="submit"]').data('value'));
            $this.elUserModal.modal('show');
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
    
    this.isLoginFormValid = function (elForm) {
        var boolIsValid = true;
        
        var strUsername = elForm.find('input[name="login-username"]').val();
        var strPassword = elForm.find('input[name="login-password"]').val();
        
        if (strUsername.length === 0) {
            boolIsValid = false;
            elForm.find('input[name="login-username"]').addClass('errored');
            elForm.find('input[name="login-username"]').closest('div.r').append('<div class="error-info">Nie podano nazwy uzytkownika</div>');
        } else if (strUsername.length < 3) {
            boolIsValid = false;
            elForm.find('input[name="login-username"]').addClass('errored');
            elForm.find('input[name="login-username"]').closest('div.r').append('<div class="error-info">Nazwa użytkownika musi składać się z co najmniej 5 znaków</div>');
        };
        
        
        if (strPassword.length === 0) {
            boolIsValid = false;
            elForm.find('input[name="login-password"]').addClass('errored');
            elForm.find('input[name="login-password"]').closest('div.r').append('<div class="error-info">Nie podano hasła</div>');
        } else if (strPassword.length < 5) {
            boolIsValid = false;
            elForm.find('input[name="login-password"]').addClass('errored');
            elForm.find('input[name="login-password"]').closest('div.r').append('<div class="error-info">Hasło musi składac się z co namniej 5 znaków</div>');
        };
        
        return boolIsValid;
    };
    
    this.signInCallback = function (objResponse) {
        if(!empty(objResponse.error)) {
            var strMessage = '';
            $this.objLoader.remove();
            strMessage = '<div class="alert alert-danger" role="alert">' + objResponse.error.join('<br />') + '</div>';
            $this.elUserModal.find('form').prepend(strMessage);
        } else {
            $this.sucessfullyLogged(objResponse.result.strRedirectTo);
        };
    };
    
    this.sucessfullyLogged = function (strRedirectTo) {
        setTimeout(function () {
            window.location.replace(strRedirectTo);
        }, 1000);
    };
    
};
