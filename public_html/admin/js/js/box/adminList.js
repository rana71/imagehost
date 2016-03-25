/* global Subframe, head, JsonRpc */
Subframe.Box.adminList = function () {
    "use strict";

    var $this = this;
    
    this.show = function () {
        JsonRpc.post({
            method: 'box/Admin/adminList', 
            context: $this, 
            callBack: 'getListCallback'
        });
    };
    
    this.getListCallback = function (objResponse) {
        public_vars.$mainContent.find('div.base-view').html(objResponse.strContent);
        public_vars.$mainContent.find('div.base-view').find('a.remove').click(function () {
            if (confirm('Na pewno chcesz usunąć tego administratora ?')) {
                var numAdminId = parseInt($(this).closest('tr').data('admin-id'));
                JsonRpc.post({
                    method: 'controller/Admin/removeAdmin', 
                    data : {
                        admin_id: numAdminId
                    }, 
                    context: $this, 
                    callBack: 'removeAdminCallback'
                });
            };
        });
    };
    
    this.removeAdminCallback = function (objResponse) {
        if (objResponse.arrResult.numStatus === 1) {
            var numAdminId = objResponse.arrResult.numAdminId;
            public_vars.$mainContent.find('div.base-view').find('tr[data-admin-id="'+numAdminId+'"]').slideUp('fast', function () {
                $(this).remove();
            });
        };
    };


};
