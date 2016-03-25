/* global Subframe, head, JsonRpc */
Subframe.Box.adminAdd = function () {
    "use strict";

    var $this = this;
    
    this.show = function () {
        JsonRpc.post({
            method: 'box/Admin/adminAdd', 
            context: $this, 
            callBack: 'getViewCallback'
        });
    };
    
    this.getViewCallback = function (objResponse) {
        public_vars.$mainContent.find('div.base-view').html(objResponse.strContent);
        
        public_vars.$mainContent.find('div.base-view form.admin-add').submit(function () {
            var objData = {};
            objData.name = $(this).find('[name="name"]').val();
            objData.surname = $(this).find('[name="surname"]').val();
            objData.email = $(this).find('[name="email"]').val();
            objData.username = $(this).find('[name="username"]').val(); 
            objData.password = $(this).find('[name="password"]').val();
            JsonRpc.post({
                method: 'controller/Admin/addAccount', 
                objData: objData, 
                context: $this, 
                callBack: 'addCallback'
            });
            return false;
        });
    };
    
    this.addCallback = function (objResponse) {
        console.log(objResponse);
    };
    

};
