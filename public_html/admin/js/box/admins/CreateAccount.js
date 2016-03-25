/* global Subframe, head, JsonRpc */
Subframe.Box.admins_CreateAccount = function () {
    "use strict";

    var $this = this;

    $this.objFormData = {};
    $this.objHtmlElement = null;

    this.init = function () {
//        $this.objModalCtr = Subframe.objModalController;
    };

    this.launch = function () {
        $this.objHtmlElement = $('div.box-admins-create-account');
        $this.objHtmlElement.find('form').submit(function () {
            UnerrorizeForm($this.objHtmlElement.find('form'));
            
            $this.objFormData.strName = $.trim($(this).find('input[name="name"]').val());
            $this.objFormData.strSurname = $.trim($(this).find('input[name="surname"]').val());
            $this.objFormData.strEmail = $.trim($(this).find('input[name="email"]').val());
            $this.objFormData.strUsername = $.trim($(this).find('input[name="username"]').val());
            $this.objFormData.strPassword = $.trim($(this).find('input[name="password"]').val());
            $this.objFormData.strRePassword = $.trim($(this).find('input[name="repassword"]').val());
            
            head.load('http://crypto-js.googlecode.com/svn/tags/3.0.2/build/rollups/md5.js', function () {
                JsonRpc2.post({
                    context: $this, 
                    data: {
                        strName: $this.objFormData.strUsername, 
                        strSurname: $this.objFormData.strUsername, 
                        strEmail: $this.objFormData.strEmail, 
                        strUsername: $this.objFormData.strUsername, 
                        strPasswordHash: CryptoJS.MD5($this.objFormData.strPassword).toString(), 
                        strRePasswordHash: CryptoJS.MD5($this.objFormData.strRePassword).toString()
                    }, 
                    method: 'admin.AdminController.createAccount', 
                    callBack: 'addAccountCallback'
                });
                
            });
            
            return false;
        });
        
    };
    
    this.addAccountCallback = function (objResponse) {
        if (!empty(objResponse.result.arrErrors)) {
            ErrorizeForm(objResponse.result.arrErrors, $this.objHtmlElement.find('form'));
        } else {
            toastr.success('Konto administratora zostało założone');
        };
    };
};
