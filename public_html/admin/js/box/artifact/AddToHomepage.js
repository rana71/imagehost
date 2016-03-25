/* global Subframe, head, JsonRpc */
Subframe.Box.artifact_AddToHomepage = function () {
    "use strict";

    var $this = this;

    $this.objFormData = {};
    $this.objHtmlElement = null;

    this.init = function () {
//        $this.objModalCtr = Subframe.objModalController;
    };

    this.launch = function () {
        $this.objHtmlElement = $('form#add-artifact-to-homepage');
        $this.objHtmlElement.find('button.add-to-homepage').click(function () {
            console.log('1');
            var numId = $.trim($this.objHtmlElement.find('input.artifact-id').val());
            
            
            if (empty(numId)) {
                toastr.warning('Nie podano żadnego artefaktu do dodania na stronę główną');
            } else {
                JsonRpc2.post({
                    context: $this, 
                    data: {
                        numId: numId
                    }, 
                    method: 'artifact.ArtifactController.addToHomepage', 
                    callBack: 'addToHomepageCallback'
                });
            };
            return false;
        });
    };
    
};
