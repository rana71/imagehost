/**
 *	Neon Login Script
 *
 *	Developed by Arlind Nushi - www.laborator.co
 */

var adminCore = adminCore || {};

;
(function ($, window, undefined)
{
    "use strict";

    $(document).ready(function ()
    {

        // sidebar menu navigation
        public_vars.$sidebarMenu.find('a.admins-list').click(function () {
            switchMainView('adminList');
        });
        public_vars.$sidebarMenu.find('a.admins-add').click(function () {
            switchMainView('adminAdd');
        });

    });
    

})(jQuery, window);

function switchMainView(strViewName) {
    var objBox = new Subframe.Box[strViewName]();
    objBox.show();
//    JsonRpc.post({
//                        context: $this, 
//                        data: {
//                            strUsername: $this.objFormData.strUsername, 
//                            strPasswordHash: CryptoJS.MD5($this.objFormData.strPassword).toString()
//                        }, 
//                        method: 'controller/User.signIn', 
//                        callBack: 'signInCallback'
//                    });
//    context: $this, 
//    JsonRpc.post({
//        method: 'box/admin.adminList', 
//        callback : function (objResponse) {
//            console.log(objResponse);
//        }
//    });
//    console.log('s');
};
