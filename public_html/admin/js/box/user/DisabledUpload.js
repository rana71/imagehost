/* global Subframe, head, JsonRpc2 */
Subframe.Box.user_DisabledUpload = function () {
    "use strict";

    var $this = this;
    $this.objLoader = {};

    this.init = function () {
        $this.objLoader = new Subframe.Lib.Loader();
    };

    this.launch = function () {
        $this.initAdding();
        $this.loadList();
        $this.initReloadList();
        $this.bindTableRowsActions();
    };
    
    this.initAdding = function () {
        $('div.box-disabled-uplaod-list form.add-block-form').submit(function (e) {
            e.preventDefault();
            var strIp = $.trim($(this).find('input.ip-to-block').val());
            
            if (empty(strIp)) {
                toastr.error('Nie podano IP do zablokowania');
            } else {
                $this.objLoader.add();
                JsonRpc2.post({
                context: $this, 
                data: {
                    strIp: strIp
                }, 
                method: 'backend.user.UserController.addDisabledUploadIp', 
                callBack: 'addDisabledUploadCallback'
            });
            };
            
            return false;
        });
    };
    
    this.addDisabledUploadCallback = function (objResponse) {
        if (empty(objResponse.error)) {
            $('div.box-disabled-uplaod-list form.add-block-form input.ip-to-block').val('');
            toastr.success('Pomyślnie dodano blokadę uploadu');
            $this.loadList();
            $this.objLoader.remove();
        } else {
            $this.objLoader.remove();
            toastr.error('Wystąpił błąd przy zapisie: '+objResponse.error);
        };
    };
    
    this.initReloadList = function () {
        var objTimeout = {};
        var elInputs = $('div.box-disabled-uplaod-list input[name="list-length"], div.box-disabled-uplaod-list input.block-search');
        
        elInputs.keyup(function () {
            if (!empty(objTimeout)) {
                clearTimeout(objTimeout);
            };
            objTimeout = setTimeout(function () {
                $this.loadList();
            }, 500);
        });
    };
    
    this.loadList = function () {
        $this.objLoader.add();
        JsonRpc2.post({
            context: $this, 
            data: {
                numLimit: $('div.box-disabled-uplaod-list input[name="list-length"]').val(), 
                strSearchString: $('div.box-disabled-uplaod-list input.block-search').val() 
            }, 
            method: 'backend.user.UserController.getDisabledUpload', 
            callBack: 'loadListCallback'
        });
    };
    
    this.loadListCallback = function (objResponse) {
        if (empty(objResponse.errors)) {
            var elTableBody = $('table.users-table tbody');
            var strCurrentSearchString = $('div.box-disabled-uplaod-list input.block-search').val();
            elTableBody.empty();
            $(objResponse.result).each(function (mulNull, objItem) {
                var elRow = $('table.users-table tr.row-template').clone();
                elRow.removeClass('row-template');
                elRow.attr('data-block-id', objItem.id);
                elRow.find('td.block_date').text(objItem.block_date);
                elRow.find('td.ip').html($this.getHilightedString(objItem.ip, strCurrentSearchString));
                
                elTableBody.append(elRow.show());
                
            });
        };
        $this.objLoader.remove();
    };
    
    this.getHilightedString = function (strString, strSearchFor) {
        var strReturn = strString;
        var objRexExp = {};
        
        if (!empty(strSearchFor)) {
            objRexExp = new RegExp('('+strSearchFor+')','gi');
            strReturn = strString.replace(objRexExp, '<span class="search-hilighted-substring">$1</span>');
        };
        
        return strReturn;
    };
    
    this.bindTableRowsActions = function () {
        var elTableBody = $('table.users-table tbody');
        elTableBody.on('click', 'a.remove-block', function (e) {
              $this.objLoader.add();
              e.preventDefault();
              var numBlockId = $(this).closest('tr[data-block-id]').attr('data-block-id');
              $this.removeBlock(numBlockId);
              return false;
        });
        
    };
    
    this.removeBlock = function (numBlockId) {
        JsonRpc2.post({
            context: $this, 
            data: {
                numnBlockId: numBlockId
            }, 
            method: 'backend.user.UserController.removeDisabledUpload', 
            callBack: 'removeBlockCallback'
        });
    };

    this.removeBlockCallback = function (objResponse) {
        if (empty(objResponse.error)) {
            $('table.users-table tbody').find('tr[data-block-id="'+objResponse.result.id+'"]').remove();
            toastr.success('Zdjęto blokadę uploadu dla IP '+objResponse.result.ip);
            $this.objLoader.remove();
        } else {
            $this.objLoader.remove();
            toastr.error('Wystąpił błąd przy zapisie: '+objResponse.error);
        };
    };
};
