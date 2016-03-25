/* global Subframe, head, JsonRpc2 */
Subframe.Box.user_FullList = function () {
    "use strict";

    var $this = this;
    $this.objLoader = {};

    this.init = function () {
        $this.objLoader = new Subframe.Lib.Loader();
    };

    this.launch = function () {
        $this.loadList();
        $this.initReloadList();
        $this.bindTableRowsActions();
    };
    
    this.initReloadList = function () {
        var objTimeout = {};
        var elInputs = $('div.box-admins-list input[name="list-length"], div.box-admins-list input.account-search');
        
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
                numLimit: $('div.box-admins-list input[name="list-length"]').val(), 
                strSearchString: $('div.box-admins-list input.account-search').val() 
            }, 
            method: 'backend.user.UserController.getListAdmin', 
            callBack: 'loadListCallback'
        });
    };
    
    this.loadListCallback = function (objResponse) {
        if (empty(objResponse.errors)) {
            var elTableBody = $('table.users-table tbody');
            var strCurrentSearchString = $('div.box-admins-list input.account-search').val();
            elTableBody.empty();
            $(objResponse.result).each(function (mulNull, objItem) {
                var elRow = $('table.users-table tr.row-template').clone();
                elRow.removeClass('row-template');
                elRow.attr('data-user-id', objItem.id);
                elRow.find('td.username a').attr({
                    href: objItem.strProfileUrl, 
                    alt: objItem.display_name
                });
                elRow.find('td.username span.text').html($this.getHilightedString(objItem.display_name, strCurrentSearchString));
                elRow.find('td.email').html($this.getHilightedString(objItem.email, strCurrentSearchString));
                if (empty(objItem.register_timestamp)) {
                    elRow.find('td.add_date').html('<i>brak danych</i>');
                } else {
                    elRow.find('td.add_date').text(objItem.register_timestamp);
                }
                
                if (objItem.is_email_confirmed === true) {
                    elRow.find('td.is-active strong').css('color' ,'green').text('Tak');
                    if (empty(objItem.email_confirm_timestamp)) {
                        elRow.find('td.is-active div').remove();
                    } else {
                        elRow.find('td.is-active div').text('od '+objItem.email_confirm_timestamp);
                    };
                } else {
                    elRow.find('td.is-active strong').css('color' ,'red').text('Nie');
                    if (empty(objItem.email_confirmation_sent_timestamp)) {
                        elRow.find('td.is-active div').remove();
                    } else {
                        elRow.find('td.is-active div').text('oczekuje od '+objItem.email_confirmation_sent_timestamp);
                    }
                };
                
                elRow.find('td.artifacts-count').text(objItem.artifacts_count);
                
                if (objItem.is_pro_stats === true) {
                    elRow.find('input.is_pro_stats').attr('checked', 'checked');
                };
                
                if (objItem.is_anonymous_available === true) {
                    elRow.find('input.is_anonymous_available').attr('checked', 'checked');
                };
                
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
        elTableBody.on('change', 'input.is_pro_stats', function () {
            var numUserId = $(this).closest('tr[data-user-id]').attr('data-user-id');
            var numNewState = 0;
            if ($(this).is(':checked') === true) {
                numNewState = 1;
            };
            $this.setIsProStats(numUserId, numNewState);
        });
        elTableBody.on('change', 'input.is_anonymous_available', function () {
            var numUserId = $(this).closest('tr[data-user-id]').attr('data-user-id');
            var numNewState = 0;
            if ($(this).is(':checked') === true) {
                numNewState = 1;
            };
            $this.setIsAnonymousAvailable(numUserId, numNewState);
        });
        
    };
    
    this.setIsProStats = function (numUserId, numNewState) {
        JsonRpc2.post({
            context: $this, 
            data: {
                numUserId: numUserId, 
                numState: numNewState
            }, 
            method: 'backend.user.UserController.setIsProStats', 
            callBack: 'defaultCallback'
        });
    };
    
    this.setIsAnonymousAvailable = function (numUserId, numNewState) {
        JsonRpc2.post({
            context: $this, 
            data: {
                numUserId: numUserId, 
                numState: numNewState
            }, 
            method: 'backend.user.UserController.setIsAnonymousAvailable', 
            callBack: 'defaultCallback'
        });
    };

    this.defaultCallback = function (objResponse) {
        if (empty(objResponse.error)) {
            toastr.success('Zapisano');
        } else {
            toastr.error('Wystąpił błąd przy zapisie: '+objResponse.error);
        };
    };
};
