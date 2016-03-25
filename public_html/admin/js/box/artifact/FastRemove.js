/* global Subframe, head, JsonRpc */
Subframe.Box.artifact_FastRemove = function () {
    "use strict";

    var $this = this;

    $this.objFormData = {};
    $this.objHtmlElement = null;

    this.init = function () {};

    this.launch = function () {
        $this.objHtmlElement = $('div#fastremove_artifact');
        $this.objHtmlElement.find('button.do-remove').click(function () {
            
            var arrUrls = $this.objHtmlElement.find('textarea').val().split("\n");
            var arrFilteredUrls = [];
            $.each(arrUrls, function (mulNull, strRow) {
                if (!empty($.trim(strRow))) {
                    arrFilteredUrls.push($.trim(strRow));
                };
            });
            
            if (arrFilteredUrls.length === 0) {
                toastr.warning('Nie podano żadnego artefaktu do usunięcia');
            } else {
                if (confirm("Na pewno usunąć wybrane "+arrFilteredUrls.length+" artefakty ?")) {
                    JsonRpc2.post({
                        context: $this, 
                        data: {
                            arrUrls: arrFilteredUrls
                        }, 
                        method: 'backend.artifact.ArtifactController.markAsRemoved', 
                        callBack: 'removeArtifactCallback'
                    });
                };
            };
            return false;
        });
    };
    
    this.removeArtifactCallback = function (objResponse) {
        if (empty(objResponse.result.arrErrors)) {
            $this.objHtmlElement.find('textarea').val('');
            toastr.success('Pomyślnie usunięto '+objResponse.result.numDeletedCount+' artefaktów');
        } else {
            toastr.error(objResponse.result.arrErrors.join('<br />'));
        };
    };
};
