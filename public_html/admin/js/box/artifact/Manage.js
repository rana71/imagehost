/* global Subframe, head, JsonRpc */
Subframe.Box.artifact_Manage = function () {
    "use strict";

    var $this = this;

    $this.objFormData = {};
    $this.objHtmlElement = null;
    $this.objLoader = {};

    this.init = function () {
        $this.objLoader = new Subframe.Lib.Loader();
    };

    this.launch = function () {
        $this.loadList();
        $this.initReloadList();
        $this.bindTableRowsActions();
    };
    
    this.loadList = function () {
        $this.objLoader.add();
        JsonRpc2.post({
            context: $this, 
            data: {
                numLimit: $('div.box-admins-list input[name="list-length"]').val(), 
                strSearchString: $('div.box-admins-list input.artifact-search').val(), 
                boolIsImported: $('div.box-admins-list input[name="is_imported"]').is(':checked'), 
                strOrderBy: $('div.box-admins-list select[name="sort"]').val()
            }, 
            method: 'backend.artifact.ArtifactListController.getListAdmin', 
            callBack: 'loadListCallback'
        });
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
    
    this.loadListCallback = function (objResponse) {
        if (empty(objResponse.errors)) {
            var elTableBody = $('table.artifacts-table tbody');
            var strCurrentSearch = $('div.box-admins-list input.artifact-search').val();
            elTableBody.empty();
            $(objResponse.result).each(function (mulNull, objItem) {
                var elRow = $('div.box-admins-list tr.row-template').clone();
                elRow.removeClass('row-template');
                elRow.attr('data-item-id', objItem.id);
                elRow.find('td.id').text('#'+objItem.id);
                elRow.find('td.add-date').text(objItem.add_timestamp);
                elRow.find('img.image').attr('src', objItem.thumb_url);
                elRow.find('td.title a').attr({
                    href: objItem.strUrl, 
                    alt: objItem.title 
                });
                elRow.find('td.elements-count').html(parseInt(objItem.elements_count));
                elRow.find('td.shows-count-real').html(parseInt(objItem.shows_count_real));
                elRow.find('td.title a span.text').html($this.getHilightedString(objItem.title, strCurrentSearch));
                
                if (objItem.is_on_homepage === true) {
                    elRow.find('input.on-homepage').attr('checked', 'checked');
                };
                
                if (objItem.is_age_restricted === true) {
                    elRow.find('input.adults-only').attr('checked', 'checked');
                };
                
                if (objItem.is_offer === true) {
                    elRow.find('input.offer').attr('checked', 'checked');
                };
                
                if (empty(objItem.removed_since_timestamp)) {
                    elRow.find('a.restore-artifact').hide();
                } else {
                    elRow.find('a.delete-artifact').hide();
                };
                elTableBody.append(elRow.show());
                
            });
        };
        $this.objLoader.remove();
    };
    
    this.initReloadList = function () {
        var objTimeout = {};
        var elInputs = $('div.box-admins-list input[name="list-length"], div.box-admins-list input.artifact-search');
        
        elInputs.keyup(function () {
            if (!empty(objTimeout)) {
                clearTimeout(objTimeout);
            };
            objTimeout = setTimeout(function () {
                $this.loadList();
            }, 500);
        });
        
        $('div.box-admins-list input[name="is_imported"]').change(function () {
            $this.loadList();
        });
        
        $('div.box-admins-list select[name="sort"]').change(function () {
            $this.loadList();
        });
    };
    
    this.bindTableRowsActions = function () {
        var elTableBody = $('table.artifacts-table tbody');
        elTableBody.on('change', 'input.on-homepage', function () {
            $this.objLoader.add();
            var numItemId = $(this).closest('tr[data-item-id]').attr('data-item-id');
            var numNewState = 0;
            if ($(this).is(':checked') === true) {
                numNewState = 1;
            };
            $this.setOnHomepage(numItemId, numNewState);
        });
        elTableBody.on('change', 'input.adults-only', function () {
            $this.objLoader.add();
            var numItemId = $(this).closest('tr[data-item-id]').attr('data-item-id');
            var numNewState = 0;
            if ($(this).is(':checked') === true) {
                numNewState = 1;
            };
            $this.setAdultsOnly(numItemId, numNewState);
        });
        
        elTableBody.on('change', 'input.offer', function () {
            $this.objLoader.add();
            var numItemId = $(this).closest('tr[data-item-id]').attr('data-item-id');
            var numNewState = 0;
            if ($(this).is(':checked') === true) {
                numNewState = 1;
            };
            $this.setOffer(numItemId, numNewState);
        });
        
        elTableBody.on('click', 'a.delete-artifact', function (e) {
            $this.objLoader.add();
            e.preventDefault();
            var numItemId = $(this).closest('tr[data-item-id]').attr('data-item-id');
            $this.markToDelete(numItemId);
            return false;
        });
        
        elTableBody.on('click', 'a.restore-artifact', function (e) {
            $this.objLoader.add();
            e.preventDefault();
            var numItemId = $(this).closest('tr[data-item-id]').attr('data-item-id');
            $this.restoreArtifact(numItemId);
            return false;
        });
        
        elTableBody.on('click', 'a.artifact-uploader-ip', function (e) {
            e.preventDefault();
            var numItemId = $(this).closest('tr[data-item-id]').attr('data-item-id');
            toastr.info('Rozpoczęto pobieranie IP autora artefaktu #'+numItemId);
            $this.showArtifactUploaderIp(numItemId);
            return false;
        });
    };
    
    this.markToDelete = function (numItemId) {
        JsonRpc2.post({
            context: $this, 
            data: {
                arrIdsToDelete: [numItemId]
            }, 
            method: 'backend.artifact.ArtifactController.markAsRemoved', 
            callBack: 'markToDeleteCallback'
        });
    };
    
    this.markToDeleteCallback = function (objResponse) {
        if (empty(objResponse.errors)) {
            $(objResponse.result.arrDeletedIds).each(function (mulNull, numItemId) {
                $('table.artifacts-table tbody tr[data-item-id="'+numItemId+'"]').find('a.delete-artifact').hide();
                $('table.artifacts-table tbody tr[data-item-id="'+numItemId+'"]').find('a.restore-artifact').show();
            });
            
        };
        $this.objLoader.remove();
    };
    
    this.showArtifactUploaderIp = function (numItemId) {
        JsonRpc2.post({
            context: $this, 
            data: {
                numItemId: numItemId
            }, 
            method: 'backend.artifact.ArtifactController.getUploaderIp', 
            callBack: 'showArtifactUploaderIpCallback'
        });
    };
    
    this.showArtifactUploaderIpCallback = function (objResponse) {
        if (empty(objResponse.error)) {
            var numItemId = objResponse.result.numItemId;
            var strIp = objResponse.result.strIp;
            
            toastr.success('IP autora artefaktu #'+numItemId+' zostało załadowane');
            
            if (empty(strIp) || strIp === false) {
                strIp = 'brak danych';
            };
            
            $('table.artifacts-table tbody tr[data-item-id="'+numItemId+'"]').find('a.artifact-uploader-ip').replaceWith('<div>IP autora: '+strIp+'</div>');
            
        } else {
            toastr.error('Wystąpił błąd przy wczytywaniu IP: '+objResponse.error);
        };
    };
    
    this.restoreArtifact = function (numItemId) {
        JsonRpc2.post({
            context: $this, 
            data: {
                arrIdsToUnmark: [numItemId]
            }, 
            method: 'backend.artifact.ArtifactController.unmarkAsRemoved', 
            callBack: 'unmarkToDeleteCallback'
        });
    };
    
    this.unmarkToDeleteCallback = function (objResponse) {
        if (empty(objResponse.errors)) {
            $(objResponse.result.arrUnmarkedIds).each(function (mulNull, numItemId) {
                $('table.artifacts-table tbody tr[data-item-id="'+numItemId+'"]').find('a.delete-artifact').show();
                $('table.artifacts-table tbody tr[data-item-id="'+numItemId+'"]').find('a.restore-artifact').hide();
            });
        };
        $this.objLoader.remove();
    };
    
    this.setAdultsOnly = function (numItemId, numNewState) {
        JsonRpc2.post({
            context: $this, 
            data: {
                numArtifactId: numItemId, 
                numState: numNewState
            }, 
            method: 'backend.artifact.ArtifactController.setAdultsOnly', 
            callBack: 'defaultCallback'
        });
    };
    
    this.setOffer = function (numItemId, numNewState) {
        JsonRpc2.post({
            context: $this, 
            data: {
                numArtifactId: numItemId, 
                numState: numNewState
            }, 
            method: 'backend.artifact.ArtifactController.setAsOffer', 
            callBack: 'defaultCallback'
        });
    };
    
    this.setOnHomepage = function (numItemId, numNewState) {
        JsonRpc2.post({
            context: $this, 
            data: {
                numArtifactId: numItemId, 
                numState: numNewState
            }, 
            method: 'backend.artifact.ArtifactController.setOnHomepage', 
            callBack: 'defaultCallback'
        });
    };
    
    
    this.defaultCallback = function (objResponse) {
        $this.objLoader.remove();
        if (empty(objResponse.result.error)) {
            toastr.success('Zapisano');
        } else {
            toastr.error('Wystąpił błąd przy zapisie: '+objResponse.result.error);
        };
    };
    
};
