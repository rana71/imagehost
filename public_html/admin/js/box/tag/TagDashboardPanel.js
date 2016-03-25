/* global Subframe, head, JsonRpc2 */
Subframe.Box.tag_TagDashboardPanel = function () {
    "use strict";

    var $this = this;

    $this.objFormData = {};
    $this.objHtmlElement = null;

    this.init = function () {};

    this.launch = function () {
        $this.objHtmlElement = $('div.tags-dashboard');
        $this.initUndoRemove();
        $this.objHtmlElement.find('form.remove-tag').submit(function (e) {
            e.preventDefault();
            
            var strTagSlug = $.trim($this.objHtmlElement.find('input[name="tag-to-delete"]').val());
            
            if (strTagSlug.length === 0) {
                toastr.warning('Nie podano żadnego taga do usunięcia');
            } else {
                JsonRpc2.post({
                    context: $this, 
                    data: {
                        strTagSlug: strTagSlug
                    }, 
                    method: 'backend.tag.TagController.markToDelete', 
                    callBack: 'removeTagCallback'
                });
            };
            return false;
        });
    };
    
    this.removeTagCallback = function (objResponse) {
        if (empty(objResponse.result.arrErrors)) {
            var strSlug = objResponse.result.strSlug;
            var strDate = objResponse.result.strDate;
            var numTagId = objResponse.result.numId;
            $this.objHtmlElement.find('input[name="tag-to-delete"]').val('');
            toastr.success('Tag został usunięty');
            $this.objHtmlElement.find('table tbody').prepend('<tr><td>'+strSlug+'</td><td>'+strDate+'</td><td><button type="button" class="btn btn-default undo-remove" data-tag-id="'+numTagId+'" data-tag-slug="'+strSlug+'"><i class="glyphicon glyphicon-plus"></i></button></td></tr>');
        } else {
            toastr.error(objResponse.result.arrErrors.join('<br />'));
        };
    };
    
    this.initUndoRemove = function () {
        $this.objHtmlElement.on('click', '.undo-remove', function () {
            var strTagSlug = $(this).data('tag-slug');
            JsonRpc2.post({
                context: $this, 
                data: {
                    strTagSlug: strTagSlug
                }, 
                method: 'backend.tag.TagController.undoDelete', 
                callBack: 'undoDeleteCallback'
            });
        });
    };
    
    this.undoDeleteCallback = function (objResponse) {
        if (empty(objResponse.result.arrErrors)) {
            var strSlug = objResponse.result.strSlug;
            var numTagId = objResponse.result.numId;
            $this.objHtmlElement.find('.undo-remove[data-tag-id="'+numTagId+'"]').closest('tr').remove();
            toastr.success('Usunięcie taga '+strSlug +' zostało cofnięte');
        } else {
            toastr.error(objResponse.result.arrErrors.join('<br />'));
        };
    };
};
