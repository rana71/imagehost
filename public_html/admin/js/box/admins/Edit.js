/* global Subframe, head, JsonRpc, toastr */
Subframe.Box.admins_Edit = function () {
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
            var numId = $(this).data('admin-id');
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
                        numId: numId, 
                        strName: $this.objFormData.strName, 
                        strSurname: $this.objFormData.strSurname, 
                        strEmail: $this.objFormData.strEmail, 
                        strUsername: $this.objFormData.strUsername, 
                        strPasswordHash: CryptoJS.MD5($this.objFormData.strPassword).toString(), 
                        strRePasswordHash: CryptoJS.MD5($this.objFormData.strRePassword).toString()
                    }, 
                    method: 'admin.AdminController.changeAccount', 
                    callBack: 'changeAccountCallback'
                });
            });
            
            return false;
        });
        
    };
    
    this.changeAccountCallback = function (objResponse) {
        if (!empty(objResponse.result.arrErrors)) {
            ErrorizeForm(objResponse.result.arrErrors, $this.objHtmlElement.find('form'));
        } else {
            toastr.success('Dane konta zostały zmienione');
        };
    };
};
