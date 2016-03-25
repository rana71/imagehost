/* global Subframe, head, JsonRpc2, objSearch */
Subframe.Box.upload_Form = function () {
    "use strict";
    
    var $this = this;
    this.elFormRoot = $('div.upload-form');
    this.objLoader = {};
    this.numCurrentAddedItems = 0;
    this.numItemsLimit = 20;
    
    this.arrAddImageBtnLabels = ['Dodaj zdjęcie', 'Dodaj kolejne zdjęcie'];
    this.arrAddVideoBtnLabels = ['Dodaj video', 'Dodaj kolejne video'];
    this.arrAddMemBtnLabels = ['Dodaj mema', 'Dodaj kolejnego mama'];
    
    this.elLastUserMemSearchElement = null;
    
    this.init = function () {
        $this.objLoader = new Subframe.Lib.Loader();
    };
    
    this.launch = function () {
        var strDefaultItemType = $this.elFormRoot.data('default-item');
        var numDefaultMemeBackgroundId = $this.getDefaultMemeBackgroundId();;
        
        $this.objLoader.add();
        head.load([getJsLibPath('ImageSelect.js')], 
            function () {
                $this.bindEvents();
                if (!empty(strDefaultItemType)) {
                    $this.addItem(strDefaultItemType, numDefaultMemeBackgroundId);
                };
                $this.refreshItemsLeft();
                $this.adjustAddButtonsText();
            }
        );
    };
    
    this.getDefaultMemeBackgroundId = function () {
        var numBackgroundId = 0;
        var strCurrentHash = window.location.hash.replace('#', '');
        if (!empty(strCurrentHash)) {
            var arrHashTokens = strCurrentHash.split(':');
            if (!empty(arrHashTokens[1])) {
                numBackgroundId = arrHashTokens[1];
            }
        }
        
        return numBackgroundId;
        
    };
    
    this.adjustAddButtonsText = function () {
        var numAddedImages = 0;
        var numAddedVideos = 0;
        var numAddedMems = 0;
        var elBtnAddImage = $this.elFormRoot.find('.add-item.photo');
        var elBtnAddVideo = $this.elFormRoot.find('.add-item.ytvideo');
        var elBtnAddMem = $this.elFormRoot.find('.add-item.mem');
        
        $this.elFormRoot.find('.items-list .row.item').each(function (mulNull, elRow) {
            if ($(elRow).hasClass('image')) {
                numAddedImages++;
            } else if ($(elRow).hasClass('ytvideo')) {
                numAddedVideos++;
            } else if ($(elRow).hasClass('mem')) {
                numAddedMems++;
            };
        });
        
        elBtnAddImage.find('.text').text( numAddedImages === 0 ? $this.arrAddImageBtnLabels[0] : $this.arrAddImageBtnLabels[1] );
        elBtnAddVideo.find('.text').text( numAddedVideos === 0 ? $this.arrAddVideoBtnLabels[0] : $this.arrAddVideoBtnLabels[1] );
        elBtnAddMem.find('.text').text( numAddedMems === 0 ? $this.arrAddMemBtnLabels[0] : $this.arrAddMemBtnLabels[1] );
        
    };
    
    this.bindEvents = function () {
        $this.elFormRoot.find('div.add-item.photo').click(function () {
            $this.addItem('image');
        });
        $this.elFormRoot.find('div.add-item.ytvideo').click(function () {
            $this.addItem('ytvideo');
        });
        $this.elFormRoot.find('div.add-item.mem').click(function () {
            $this.addItem('mem');
        });
        $this.elFormRoot.find('div.submit-row div.save-button').click($this.submitUpload);
        $this.objLoader.remove();
    };
    
    this.refreshItemsLeft = function () {
        var numItemsLeft = $this.numItemsLimit - $this.numCurrentAddedItems;
        $this.elFormRoot.find('strong.items-left-no').text(numItemsLeft);
    };
    
    this.addItem = function (strItemType, numDefaultMemeBackgroundId) {
        if ($this.numCurrentAddedItems >= $this.numItemsLimit) {
            alert('Osiągnąłeś limit elementów możliwych do dodania do jednej galerii');
            return false;
        }
//        var elHtml = $this.elFormRoot.find('div.templates li.item.'+strItemType).clone();
        if (!$this.elFormRoot.hasClass('item-added')) {
            $this.elFormRoot.addClass('item-added');
        };
        if (strItemType === 'mem') {
            $this.objLoader.add();
            JsonRpc2.post({
                context: $this,
                method: 'backend.artifact.MemeBackgroundController.getMostPopular',
                data: {
                    numLimit: 31, 
                    strSearchString: '', 
                    numDefaultMemeBackgroundId: numDefaultMemeBackgroundId
                }, 
                callBack: 'addItemMemBackgroundGet'
            });
        } else {
            var elHtml = $this.elFormRoot.find('div.templates li.item.'+strItemType).clone();
            $this.bindItemEvents(elHtml);
            $this.elFormRoot.find('ol.items-list').append(elHtml);
            $this.orderizeItems();
            $this.numCurrentAddedItems++;
            $this.refreshItemsLeft();
            $this.adjustAddButtonsText();
        }
    };
    
    
    this.addItemMemBackgroundGet = function (objResponse) {
        var elHtml = $this.elFormRoot.find('div.templates li.item.mem').clone();
        
        var arrBackgrounds = objResponse.result.arrBackgroundImages;
        if (!empty(arrBackgrounds)) {
            var strImageBackgroundSrc = arrBackgrounds[0].image_path +'/'+ arrBackgrounds[0].image_filename;
            elHtml.find('img.mem-background').attr({
                'src': strImageBackgroundSrc, 
                'data-last-used-background-id' : arrBackgrounds[0].id
            });

            arrBackgrounds.shift();
            if (!empty(arrBackgrounds)) {
                $this.elLastUserMemSearchElement = elHtml;
                $this.memBackgroundSearchCallback({
                    result: {
                        arrBackgroundImages: arrBackgrounds
                    }
                });
            }
        }
        
        $this.bindItemEvents(elHtml);
        elHtml.find('.custom-mem-background').ImageSelect({
            elPreviewTarget: elHtml.find('img.mem-background'), 
            strUploadLabel: 'Dodaj swój obrazek jako tło mema'
        });
        var objTimeout = null;
        elHtml.find('input.search-image').keyup(function () {
            var elSearchInput = $(this);
            if (!empty(objTimeout)) {
                clearTimeout(objTimeout);
            };
            objTimeout = setTimeout(function () {
                $this.elLastUserMemSearchElement = elHtml;
                elSearchInput.attr('disabled', true);
                JsonRpc2.post({
                    context: $this, 
                    data: {
                        numLimit: 30,  
                        strSearchString: elSearchInput.val() 
                    }, 
                    method: 'backend.artifact.MemeBackgroundController.getMostPopular', 
                    callBack: 'memBackgroundSearchCallback'
                });
            }, 500);
        });
        elHtml.find('.mem-textarea.mem-title').keydown(function (e) {
            var numLimit = 2;
            if (e.keyCode === 13) {
                numLimit = 1;
            }
            if ($(this).val().split("\n").length > numLimit) {
                e.preventDefault();
                return false;
            }
        });
        elHtml.find('.mem-textarea.mem-text').keydown(function (e) {
            var numLimit = 3;
            if (e.keyCode === 13) {
                numLimit = 2;
            }
            if ($(this).val().split("\n").length > numLimit) {
                e.preventDefault();
                return false;
            }
        });
        $this.elFormRoot.find('ol.items-list').append(elHtml);
        $this.orderizeItems();
        $this.numCurrentAddedItems++;
        $this.refreshItemsLeft();
        $this.adjustAddButtonsText();
        $this.objLoader.remove();
    };
    
    this.memBackgroundSearchCallback = function (objResponse) {
        $this.elLastUserMemSearchElement.find('div.search-results').empty();
        $this.elLastUserMemSearchElement.find('input.search-image').attr('disabled', false);;
        if (objResponse.status === 0 || empty(objResponse.result.arrBackgroundImages)) {
            $this.elLastUserMemSearchElement.find('div.search-results').append('<p class="empty">Niestety, nic nie znaleźliśmy :(</p>');
        } else {
            $(objResponse.result.arrBackgroundImages).each(function (mulNull, objBackground) {
                
                var strThumbSrc = objBackground.image_path +'/'+ objBackground.image_filename;
                var elSearchItem = $('<div class="search-mem-background-result"></div>').click(function () {
                    $(this).closest('.search-results').find('div.search-mem-background-result.selected').removeClass('selected');
                    $(this).addClass('selected');
                    var selectedImageSrc = $(this).find('img').attr('src');
                    var numBackgroundId = $(this).find('img').data('meme-background-id');
                    $(this).closest('.row.item.mem').find('img.mem-background').attr({
                        'src': selectedImageSrc, 
                        'data-last-used-background-id' : numBackgroundId
                    }); 
                });
                elSearchItem.append($('<img />').attr({
                    'src': strThumbSrc, 
                    'data-meme-background-id': objBackground.id
                }));
                $this.elLastUserMemSearchElement.find('div.search-results').append(elSearchItem);
            });
        };
    };
    
    this.bindItemEvents = function (elItemHtml) {
        elItemHtml.find('[data-image-selector]').ImageSelect();
        elItemHtml.find('.remove-item').click(function () {
            if (confirm('Na pewno chcesz usunąć ten element ?')) {
                $this.removeItem(elItemHtml);
                $this.refreshItemsLeft();
                $this.adjustAddButtonsText();
            }
        });
    };
    this.removeItem = function (elItemToRemove) {
        elItemToRemove.remove();
        $this.numCurrentAddedItems--;
        $this.orderizeItems();
        if ($this.numCurrentAddedItems === 0) {
            $this.elFormRoot.removeClass('item-added');
        }
    };
    
    this.orderizeItems = function () {
        $this.elFormRoot.find('ol.items-list li.item').each(function (numIterator, elElement) {
            $(elElement).find('span.item-no').text(numIterator+1);
        });
    };
    
    this.submitUpload = function () {
        var arrItemsHtmls = $this.elFormRoot.find('ol.items-list li.item');
        var boolIsCorrect = true;
        
        $this.elFormRoot.find('.form-info').remove();
        
        $(arrItemsHtmls).each(function(mulNull, elItemHtml) {
            elItemHtml = $(elItemHtml);
            if (elItemHtml.hasClass('image')) {
                boolIsCorrect = boolIsCorrect && $this.validateItemImage(elItemHtml);
            } else if (elItemHtml.hasClass('ytvideo')) {
                boolIsCorrect = boolIsCorrect && $this.validateItemYtVideo(elItemHtml);
            }
        });
        
        if (boolIsCorrect === false) {
            $('html, body').animate({
                scrollTop: $(".upload-form .form-info.error:eq(0)").offset().top - 110
            }, 500);
        } else if ($this.numCurrentAddedItems > 1) {
            $this.showGalleryAdditionalInfoModal();
        } else {
            $this.saveArtifact();
        };
    };
    
    this.showGalleryAdditionalInfoModal = function () {
        var elGalleryModel = $('div.upload-form div.templates div.modal-gallery-info').clone();
        elGalleryModel.on('hidden.bs.modal', function (e) {
            elGalleryModel.remove();
        });
        elGalleryModel.modal('show');
        elGalleryModel.find('.save-info').click(function(){
            elGalleryModel.find('.form-info').remove();
            var boolOk = $this.validateGalleryInfo(elGalleryModel);
            if (boolOk === true) {
                var strGalleryTitle = elGalleryModel.find('input[name="title"]').val();
                var strGalleryDescription = elGalleryModel.find('textarea[name="content"]').val();
                elGalleryModel.modal('hide');
                $this.saveArtifact(strGalleryTitle, strGalleryDescription);
            }
        });
    };
    
    this.validateGalleryInfo = function (elModalHtml) {
        var boolOk = true;
        
        if (empty(elModalHtml.find('input[name="title"]').val())) {
            elModalHtml.find('input[name="title"]').after('<div class="form-info error">Podaj tytuł galerii</div>');
            boolOk = false;
        }
        
        return boolOk;
    };
    
    this.validateItemImage = function (elHtml) {
        var boolCorrect = true;
        if (empty(elHtml.find('input[name="title[]"]').val())) {
            elHtml.find('input[name="title[]"]').after('<div class="form-info error">Podaj tytuł zdjęcia</div>');
            boolCorrect = false;
        };
        
        var strImageJson = $.trim(elHtml.find('input[name="image[]"]').val());
        if (empty(strImageJson)) {
            elHtml.find('input[name="image[]"]').after('<div class="form-info error">Wybierz zdjęcie</div>');
            boolCorrect = false;
        };
        
        return boolCorrect;
    };
    
    this.validateItemYtVideo = function (elHtml) {
        var boolCorrect = true;
        if (empty(elHtml.find('input[name="title[]"]').val())) {
            elHtml.find('input[name="title[]"]').after('<div class="form-info error">Podaj tytuł filmu</div>');
            boolCorrect = false;
        };
        
        if (empty(elHtml.find('input[name="movie_url[]"]').val())) {
            elHtml.find('input[name="movie_url[]"]').after('<div class="form-info error">Podaj adres URL video</div>');
            boolCorrect = false;
        };
        
        return boolCorrect;
    };
    
    this.grabElements = function () {
        var arrElements = [];
        
        $this.elFormRoot.find('ol.items-list li.item').each(function (numIterator, objRow) {
            var elRow = $(objRow);
//            var objElement = {
//                numOrdering: numIterator+1, 
//                strTitle: elRow.find('input[name="title[]"]').val(), 
//                strDescription: elRow.find('textarea[name="content[]"]').val()
//            };
            var objElement = { numOrdering: numIterator+1 };
            if (elRow.hasClass('image')) {
                objElement.numType = 1;
                objElement.strTitle = elRow.find('input[name="title[]"]').val();
                objElement.strDescription = elRow.find('textarea[name="content[]"]').val();
                
                objElement.arrImage = JSON.parse(elRow.find('input[type="hidden"][name="image[]"]').val());
            } else if (elRow.hasClass('ytvideo')) {
                objElement.numType = 2;
                objElement.strTitle = elRow.find('input[name="title[]"]').val();
                objElement.strDescription = elRow.find('textarea[name="content[]"]').val();
                
                objElement.strMovieUrl = $.trim(elRow.find('input[name="movie_url[]"]').val());
            } else if (elRow.hasClass('mem')) {
                objElement.numType = 3;
                objElement.strTitle = elRow.find('div.mem-container .mem-textarea.mem-title').val();
                objElement.strDescription = elRow.find('div.mem-container .mem-textarea.mem-text').val();
                
                var strBackgroundSrc = elRow.find('img.mem-background').attr('src');
                if (strBackgroundSrc.substring(0, 10) === 'data:image') {
                    objElement.strBackground = strBackgroundSrc;
                } else {
                    objElement.numMemeBackgroundId = elRow.find('img.mem-background').data('last-used-background-id');
                }
                
                objElement.numWidth = elRow.find('img.mem-background').width();
            }
            arrElements.push(objElement);
        });
        
        return arrElements;
    };
    
    this.saveArtifact = function (strGalleryTitle, strGalleryDescription) {
        $this.objLoader.add();
        var arrElements = $this.grabElements();
        
        if (empty(strGalleryTitle)) {
            strGalleryTitle = arrElements[0].strTitle;
        };
        if (empty(strGalleryDescription)) {
            strGalleryDescription = arrElements[0].strDescription;
        }
        
        JsonRpc2.post({
            context: $this,
            data: {
                arrElements: arrElements, 
                strTitle: strGalleryTitle,
                strDescription: strGalleryDescription
            },
            method: 'backend.artifact.ArtifactController.upload',
            callBack: 'saveCallback', 
            extends: {
                progress: 'saveProgress'
            }
        });
    };
    
    this.saveProgress = function (objPartial) {
        if (objPartial.event.lengthComputable) {
            var numLoadedPart = objPartial.event.loaded / objPartial.event.total;
            numLoadedPart -= 0.02; // leave a little bit of free time for php processing
            $this.objLoader.updateProgressBar(numLoadedPart);
        };
    };
    
    this.saveCallback = function (objResponse) {
        $this.objLoader.updateProgressBar(1);
        $this.objLoader.remove();
        if (!empty(objResponse.error)) {
            alert(objResponse.error.join('<br />'));
        } else if (!empty(objResponse.result)) {
            var strArtifactUrl = objResponse.result.strUrl;
            
            var strAlertMsg = '<strong>Znakomicie!</strong> Twoja galeria została poprawnie wysłana. Za 3 sekundy zostaniesz do niej przekierowany';
            $this.elFormRoot.removeClass('items-added').empty();
            $this.elFormRoot.append('<div class="alert alert-success" role="alert">'+strAlertMsg+'</div>');
            
            setTimeout(function () {
                window.location.replace(strArtifactUrl); 
           }, 3000);
        };
        
    };
    
};