/* global Subframe, head, JsonRpc */
Subframe.Box.newsletter_Create = function () {
    "use strict";

    var $this = this;
    this.objLoader = {};
    
    this.init = function () {
        $this.objLoader = new Subframe.Lib.Loader();
    };

    this.launch = function () {
        $this.objHtmlElement = $('div#create-newsletter');
        $this.objHtmlElement.find('button.go-create').click(function () {
            
            var arrIds = $this.objHtmlElement.find('textarea').val().split("\n");
            var arrFilteredIds = [];
            var strMailSubject = $.trim($this.objHtmlElement.find('input.mail-subject').val());
            var boolError = false;
            $.each(arrIds, function (mulNull, strRow) {
                if (!empty($.trim(strRow))) {
                    arrFilteredIds.push($.trim(strRow));
                };
            });
            if (empty(strMailSubject)) {
                toastr.warning('Nie podano tytułu e-maila');
                boolError = true;
            };
            if (arrFilteredIds.length === 0) {
                toastr.warning('Nie podano żadnego artefaktu do newslettera');
                boolError = true;
            };
            
            if (boolError === false) {
                $this.objLoader.add();
                JsonRpc2.post({
                    context: $this, 
                    data: {
                        arrIds: arrFilteredIds, 
                        strMailSubject: strMailSubject
                    }, 
                    method: 'backend.newsletter.NewsletterController.prepareCampaign', 
                    callBack: 'prepareCampaignCallback'
                });
            };
            return false;
        });
    };
    
    this.prepareCampaignCallback = function (objResponse) {
        if (empty(objResponse.error)) {
            if (confirm("Wiadomość testowa została wysłana. Wysłać newsletter do aktualnej listy subskrybentów ("+objResponse.result.numRealMembersCount+" adresatów)?")) {
                JsonRpc2.post({
                    context: $this, 
                    data: {
                        numCampaignId: objResponse.result.numCampaignId
                    }, 
                    method: 'backend.newsletter.NewsletterController.sendCampaign', 
                    callBack: 'sendCampaignCallback'
                });
            } else {
                $this.objLoader.remove();
            };
        } else {
            $this.objLoader.remove();
            toastr.error(objResponse.error.join('<br />'));
        };
    };
    
    this.sendCampaignCallback = function (objResponse) {
        $this.objLoader.remove();
        if (empty(objResponse.error)) {
            $this.objHtmlElement.find('input.mail-subject, textarea').val('');
            toastr.success("Newsletter został wysłany");
        } else {
            toastr.error(objResponse.error.join('<br />'));
        };
    };
};
