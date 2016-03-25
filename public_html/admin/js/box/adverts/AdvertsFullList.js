/* global Subframe, head, JsonRpc, JsonRpc2 */
Subframe.Box.adverts_AdvertsFullList = function () {
    "use strict";

    var $this = this;
    
    $this.objHtmlElement = null;

    this.init = function () {};

    this.launch = function () {
        $this.objHtmlElement = $('div.box-adverts-list');
        
        $this.initStdAreasManage();
    };
    
    this.initStdAreasManage = function () {
        $this.objHtmlElement.find('.clear-area').click(function () {
            var strAreaId = $(this).data('area-id');
            if (confirm('Na pewno wyczyścić strefę '+strAreaId+'?')) {
                $(this).closest('tr').find('textarea.area-advert-code').val('');
                JsonRpc2.post({
                    context: $this, 
                    data: {
                        strAreaId: strAreaId
                    }, 
                    method: 'backend.puppy.PuppyController.clearArea', 
                    callBack: 'clearAreaCallback'
                });
                
            };
            return false;
        });
        
        $this.objHtmlElement.find('.save-advert').click(function () {
            var strAreaId = $(this).data('area-id');
            var strAdvertCode = $(this).closest('tr').find('textarea.area-advert-code').val();
            JsonRpc2.post({
                context: $this, 
                data: {
                    strAdvertCode: strAdvertCode, 
                    strAreaId: strAreaId
                }, 
                method: 'backend.puppy.PuppyController.savePuppyInArea', 
                callBack: 'saveAdvertInAreaCallback'
            });

            return false;
        });
    };
    
    this.saveAdvertInAreaCallback = function (objResponse) {
        if (empty(objResponse.result.arrErrors)) {
            var strSavedAreaId = objResponse.result.strSavedAreaId;
            toastr.success('Reklama w strefie '+strSavedAreaId +' została zapisana');
        };
    };
    
};
