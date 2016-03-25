/* global Subframe, head, JsonRpc */
Subframe.Box.artifact_RemoveImportedAndBan = function () {
    "use strict";

    var $this = this;

    $this.objFormData = {};
    $this.objHtmlElement = null;
    $this.objLoader = {};

    this.init = function () {
        $this.objLoader = new Subframe.Lib.Loader();
    };

    this.launch = function () {
        $this.objHtmlElement = $('div#remove_ban');
        
        $this.prebindStep2();
        $this.objHtmlElement.find('button.do-remove').click(function () {
            
            var numArtifactId = $.trim($this.objHtmlElement.find('input.artifact-id').val());
            var numSellerId = $.trim($this.objHtmlElement.find('input.seller-id').val());
            
            
            
            if (!empty(numArtifactId) && !empty(numSellerId)) {
                toastr.error('Podano ID artefaktu i ID sprzedawcy');
            } else if (empty(numArtifactId) && empty(numSellerId)) {
                toastr.error('Nie podano ID artefaktu ani ID sprzedawcy');
            } else {
                $this.objLoader.add();
                
                if (!empty(numArtifactId)) {
                    numArtifactId = parseInt(numArtifactId);
                };
                if (!empty(numSellerId)) {
                    numSellerId = parseInt(numSellerId);
                };
                
                JsonRpc2.post({
                    context: $this, 
                    data: {
                        numArtifactId: numArtifactId, 
                        numSellerId: numSellerId
                    }, 
                    method: 'backend.artifact.ArtifactController.findSellerAndOffersCount', 
                    callBack: 'findSellerCallback'
                });
            };
            return false;
        });
    };
    
    this.prebindStep2 = function () {
        $this.objHtmlElement.find('div.step2 span.cancel').click(function () {
            $this.closeStep2();
        });
        $this.objHtmlElement.find('div.step2 button.confirm').click(function () {
            $this.objLoader.add();
            var numSellerId = parseInt($this.objHtmlElement.find('div.step2 strong.allegro-seller-id').text());
            var boolBanSeller = $this.objHtmlElement.find('div.step2 input[name="ban-seller"]').is(':checked');
            var boolRemoveOffers = $this.objHtmlElement.find('div.step2 input[name="remove-offers"]').is(':checked');
            
            JsonRpc2.post({
                context: $this, 
                data: {
                    numSellerId: numSellerId, 
                    boolBanSeller: boolBanSeller, 
                    boolRemoveOffers: boolRemoveOffers
                }, 
                method: 'backend.artifact.ArtifactController.banSellerAndRemoveOffers', 
                callBack: 'banSellerAndRemoveOffersCallback'
            });

        });
    };
    
    this.banSellerAndRemoveOffersCallback = function (objResponse) {
        $this.objLoader.remove();
        if (empty(objResponse.error)) {
            var strMessage = '';
            if (!empty(objResponse.result.boolBanned)) {
                strMessage += 'Użytkownik został zbanowany<br />';
            };
            if (!empty(objResponse.result.boolOffersRemoved)) {
                strMessage += 'Oferty zostały usunięte<br />';
            };
            $this.closeStep2();
            toastr.success(strMessage);
        } else {
            toastr.error(objResponse.error.join('<br />'));
        };
    };
    
    this.closeStep2 = function () {
        $this.objHtmlElement.find('div.step2').find('strong.allegro-seller-id, strong.seller-offers-count, div.example-offers ul').empty();
        $this.objHtmlElement.find('input.artifact-id, input.seller-id').removeAttr('disabled');
        $this.objHtmlElement.find('div.step2').slideUp('fast', function () {
            $this.objHtmlElement.find('div.step1').slideDown('fast');
        });
    };
    
    this.findSellerCallback = function (objResponse) {
        $this.objLoader.remove();
        if (empty(objResponse.error)) {
            $this.objHtmlElement.find('div.step1').slideUp('fast', function () {
                $this.objHtmlElement.find('input.artifact-id, input.seller-id').attr('disabled', 'disabled');
                $this.objHtmlElement.find('div.step2 strong.allegro-seller-id').text(objResponse.result.numSellerId);
                $this.objHtmlElement.find('div.step2 strong.seller-offers-count').text(objResponse.result.numOffersCount);
                $(objResponse.result.arrOffers).each(function (numIterator, objOffer) {
                    $this.objHtmlElement.find('div.step2 div.example-offers ul').append('<li><a href="'+objOffer.strUrl+'" target="_blank">'+objOffer.strTitle+'<span class="glyphicon glyphicon-new-window" style="margin-left:5px;"></span></a></li>');
                });
                $this.objHtmlElement.find('div.step2').slideDown('fast');
                
                
            });
        } else {
            toastr.error(objResponse.error.join('<br />'));
        };
    };
    
    this.removeArtifactCallback = function (objResponse) {
        if (empty(objResponse.error)) {
            $this.objHtmlElement.find('textarea').val('');
            toastr.success('Pomyślnie usunięto '+objResponse.result.numDeletedCount+' artefaktów');
        } else {
            toastr.error(objResponse.error.join('<br />'));
        };
    };
};
