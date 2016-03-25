/* global Subframe, head, JsonRpc */
Subframe.Box.admins_FullList = function () {
    "use strict";

    var $this = this;

    $this.objFormData = {};
    $this.objHtmlElement = null;

    this.init = function () {
//        $this.objModalCtr = Subframe.objModalController;
    };

    this.launch = function () {
        $this.objHtmlElement = $('div.box-admins-list');
        $this.objHtmlElement.find('a.remove').click(function () {
            var numAdminId = $(this).data('admin-id');
            var strNameSurname = $(this).closest('tr').find('td.name-surname').text();
            if (confirm('Na pewno usunąć konto administratora '+strNameSurname+'?')) {
                $(this).closest('tr').remove();
                
                JsonRpc2.post({
                    context: $this, 
                    data: {
                        numAdminId: numAdminId
                    }, 
                    method: 'admin.AdminController.removeAccount', 
                    callBack: 'removeAdminAccount'
                });
                
            }
            return false;
        });
    };
    
    this.removeAdminAccount = function (objResponse) {
        if (empty(objResponse.result.arrErrors)) {
            toastr.success('Konto administratora zostało usunięte');
        };
    };
};
