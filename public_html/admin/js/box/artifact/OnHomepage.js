/* global Subframe, head, JsonRpc */
Subframe.Box.artifact_OnHomepage = function () {
    "use strict";

    var $this = this;

    this.init = function () {};
    this.launch = function () {
        $this.initAdding();
        $this.initRemoving();
    };

    this.initRemoving = function () {
        $('table.artifacts-on-hp-table button.remove-from-homepage').click(function () {
            var numId = $(this).closest('tr').attr('data-artifact-id');
            JsonRpc2.post({
                context: $this, 
                data: {
                    numId: numId
                }, 
                method: 'backend.artifact.ArtifactController.removeFromHomepage', 
                callBack: 'removeFromHomepageCallback'
            });
        });
    };

    this.initAdding = function () {
        var objForm = $('form#add-artifact-to-homepage');
        objForm.find('button.add-to-homepage').click(function () {
            var numId = $.trim(objForm.find('input.artifact-id').val());
            
            
            if (empty(numId)) {
                toastr.warning('Nie podano żadnego artefaktu do dodania na stronę główną');
            } else {
                JsonRpc2.post({
                    context: $this, 
                    data: {
                        numId: numId
                    }, 
                    method: 'backend.artifact.ArtifactController.addToHomepage', 
                    callBack: 'addToHomepageCallback'
                });
            };
            return false;
        });
        
    };
    
    
    this.addToHomepageCallback = function (objResponse) {
        if (empty(objResponse.error)) {
            $('form#add-artifact-to-homepage input.artifact-id').val('');
            toastr.success('Pomyślnie dodano artefakt do strony głównej');
            location.reload();
        } else {
            toastr.error(objResponse.error.join('<br />'));
        };
    };
    
    this.removeFromHomepageCallback = function (objResponse) {
        var numArtifactId = objResponse.result.numRemovedArtifactId;
        $('table.artifacts-on-hp-table tr[data-artifact-id="'+numArtifactId+'"]').remove();
    };
};
