/* global Subframe, head, JsonRpc2, objSearch */
Subframe.Box.upload_Form = function () {
    "use strict";
    
    var $this = this;
    this.elFormRoot = $('div.m-upload');
    this.objLoader = {};
    this.numCurrentAddedItems = 0;
    this.numItemsLimit = 20;
    
    this.arrAddImageBtnLabels = ['Dodaj zdjęcie', 'Dodaj kolejne zdjęcie'];
    this.arrAddVideoBtnLabels = ['Dodaj video', 'Dodaj kolejne video'];
    this.arrAddMemBtnLabels = ['Dodaj mema', 'Dodaj kolejnego mama'];
    
    this.elLastUserMemSearchElement = null;
    this.elLastUsedVideoItemElement = null;
    this.strLastExtractedVideoUrl = '';
    this.boolAdvancedMode = false;
    
    this.mulBuffor = null;
    
    this.init = function () {
        $this.objLoader = new Subframe.Lib.Loader();
    };
    
    this.launch = function () {
        var strDefaultItemType = $this.elFormRoot.data('default-item');
        var numDefaultMemeBackgroundId = $this.getDefaultMemeBackgroundId();
        var strDefaultImageElementIdBase64 = $this.getDefaultImageElementIdBase64();
        if (!empty(strDefaultImageElementIdBase64)) {
            strDefaultItemType = 'image';
        };
        
        $this.objLoader.add();
        head.load([getJsLibPath('ImageSelect.js')], 
            function () {
                $this.bindEvents();
                $this.prepareAdvancedModeSwitcher();
                $this.refreshItemsLeft();
                $this.adjustAddButtonsText();
                
                if (!empty(strDefaultItemType)) {
                    if (strDefaultItemType === 'mem') {
                        ga('send', 'pageview', '/virt/add-meme/direct');
                        $this.addItem(strDefaultItemType, numDefaultMemeBackgroundId);
                    } else if (!empty(strDefaultImageElementIdBase64)) {
                        $this.objLoader.add();
                        JsonRpc2.post({
                            context: $this,
                            method: 'backend.artifact.ArtifactController.getImageElementBase64',
                            data: {
                                strElementIdBase64: strDefaultImageElementIdBase64
                            }, 
                            callBack: 'getDefaultImageBase64Callback'
                        });
                        
                    };
                };
                
            }
        );
    };
    
    this.getDefaultImageBase64Callback = function (objResult) {
        $this.objLoader.remove();
        $this.addItem('image', objResult.result.arrImage);
    };
    
    this.prepareAdvancedModeSwitcher = function () {
        $('div.m-upload input[name="advanced"]').change(function () {
            $this.boolAdvancedMode = $(this).is(':checked');
            $this.switchAdvancedModeFieldsVisibility($this.boolAdvancedMode);
        });
    };
    
    this.switchAdvancedModeFieldsVisibility = function (boolShow) {
        if (boolShow === true) {
            $('div.m-upload .advanced-mode-only').show();
        } else {
            $('div.m-upload .advanced-mode-only').hide();
        };
    };
    
    this.getDefaultMemeBackgroundId = function () {
        var numBackgroundId = 0;
        var strCurrentHash = window.location.hash.replace('#', '');
        if (!empty(strCurrentHash)) {
            var arrHashTokens = strCurrentHash.split(':');
            if (!empty(arrHashTokens[1]) && arrHashTokens[0] === 'tlo-mema') {
                numBackgroundId = arrHashTokens[1];
            }
        }
        
        return numBackgroundId;
    };
    
    this.getDefaultImageElementIdBase64 = function () {
        var strDefaultImageElementIdBase64 = '';
        var strCurrentHash = window.location.hash.replace('#', '');
        if (!empty(strCurrentHash)) {
            var arrHashTokens = strCurrentHash.split(':');
            if (!empty(arrHashTokens[1]) && arrHashTokens[0] === 'przejmij-fote') {
                strDefaultImageElementIdBase64 = arrHashTokens[1];
            }
        }
        
        return strDefaultImageElementIdBase64;
    };
    
    this.adjustAddButtonsText = function () {
        var numAddedImages = 0;
        var numAddedVideos = 0;
        var numAddedMems = 0;
        var elBtnAddImage = $this.elFormRoot.find('div.add-item-type-select div.add.photo');
        var elBtnAddVideo = $this.elFormRoot.find('div.add-item-type-select div.add.ytvideo');
        var elBtnAddMem = $this.elFormRoot.find('div.add-item-type-select div.add.mem');
        
        $this.elFormRoot.find('ol.items-list li.item').each(function (mulNull, elRow) {
            if ($(elRow).hasClass('image')) {
                numAddedImages++;
            } else if ($(elRow).hasClass('ytvideo')) {
                numAddedVideos++;
            } else if ($(elRow).hasClass('mem')) {
                numAddedMems++;
            };
        });
        
        elBtnAddImage.text( numAddedImages === 0 ? $this.arrAddImageBtnLabels[0] : $this.arrAddImageBtnLabels[1] );
        elBtnAddVideo.text( numAddedVideos === 0 ? $this.arrAddVideoBtnLabels[0] : $this.arrAddVideoBtnLabels[1] );
        elBtnAddMem.text( numAddedMems === 0 ? $this.arrAddMemBtnLabels[0] : $this.arrAddMemBtnLabels[1] );
        
    };
    
    this.bindEvents = function () {
        $this.elFormRoot.find('div.add.photo').click(function () {
            $this.addItem('image');
        });
        $this.elFormRoot.find('div.add.ytvideo').click(function () {
            $this.addItem('ytvideo');
        });
        $this.elFormRoot.find('div.add.mem').click(function () {
            $this.addItem('mem');
        });
        $this.elFormRoot.find('div.submit-row div.save').click($this.submitUpload);
        $this.objLoader.remove();
    };
    
    this.refreshItemsLeft = function () {
        var numItemsLeft = $this.numItemsLimit - $this.numCurrentAddedItems;
        $this.elFormRoot.find('p.items-left strong').text(numItemsLeft);
    };
    
    this.addItem = function (strItemType, mulDefaultItem) {

        if ($this.numCurrentAddedItems >= $this.numItemsLimit) {
            alert('Osiągnąłeś limit elementów możliwych do dodania do jednej galerii');
            return false;
        };
        
        if (!$this.elFormRoot.hasClass('item-added')) {
            $this.elFormRoot.addClass('item-added');
        };
        switch (strItemType) {
            case 'mem':
                $this.addItemMeme(mulDefaultItem, $this.scrollToLastItem);
                break;
            case 'image':
                $this.addItemImage(mulDefaultItem, $this.scrollToLastItem);
                break;
            case 'ytvideo':
                $this.addItemYtVideo($this.scrollToLastItem);
                break;
        };
    };
    
    this.scrollToLastItem = function () {
        $('body').animate({
            scrollTop: $this.elFormRoot.find('ol.items-list li.item:last-child').offset().top - 70
        }, 500);
    };
    
    this.addItemImage = function (objImage, fnCallback) {
        
        var elHtml = $this.elFormRoot.find('div.templates li.item.image').clone();
        $this.bindItemEvents(elHtml);
        elHtml.find('[data-image-selector]').ImageSelect({
            strUploadLabel: 'Wybierz lub upuść tutaj zdjęcie'
        });
        
        if (!empty(objImage))  {
            var objImageObject = {
                base64: objImage.strBase64, 
                userFilename: objImage.image_filename
            };
            elHtml.find('input[name="image[]"]').val(JSON.stringify(objImageObject));
            elHtml.find('div.upload-space').hide();
            elHtml.find('div.preview-wrapper').show();
            
            var objPreviewImage = new Image();
            objPreviewImage = $('<img />').attr({
                src: objImageObject.base64
            }).css('max-width', '100%');
            elHtml.find('div.preview-wrapper').append(objPreviewImage);
            elHtml.find('div.preview-wrapper').append();
        }
        
        $this.elFormRoot.find('ol.items-list').append(elHtml);
        $this.switchAdvancedModeFieldsVisibility($this.boolAdvancedMode);
        $this.orderizeItems();
        $this.numCurrentAddedItems++;
        $this.refreshItemsLeft();
        $this.adjustAddButtonsText();
        if (!empty(fnCallback)) {
            fnCallback();
        };
    };
    
    this.addItemMeme = function (numDefaultMemeBackgroundId, fnCallback) {
        $this.objLoader.add();
        if (!empty(fnCallback)) {
            $this.mulBuffor = fnCallback;
        };
        JsonRpc2.post({
            context: $this,
            method: 'backend.artifact.MemeBackgroundController.getMostPopular',
            data: {
                numLimit: 30, 
                strSearchString: '', 
                numDefaultMemeBackgroundId: numDefaultMemeBackgroundId
            }, 
            callBack: 'addItemMemBackgroundGet'
        });
    };
    
    this.addItemYtVideo = function (fnCallback) {
        var elHtml = $this.elFormRoot.find('div.templates li.item.ytvideo').clone();
        
        elHtml.find('input[name="movie_url[]"]').keyup(function () {
            var strUrl = $(this).val();
            if ($this.strLastExtractedVideoUrl !== strUrl) {
                $this.strLastExtractedVideoUrl = strUrl;
                $(this).closest('li.item.ytvideo').find('div.preview').empty().append('<div class="video-selector"></div>');
                if (!empty(strUrl) && $this.isCorrectYoutubeUrl(strUrl) === true) {
                    $this.elLastUsedVideoItemElement = $(this).closest('li.item.ytvideo');
                    $this.extractYoutubeVIdeoId(strUrl, 'extractYoutubeVIdeoIdCallback');

                }
            }
        });

        $this.bindItemEvents(elHtml);
        elHtml.find('[data-image-selector]').ImageSelect({
            strUploadLabel: 'Wybierz lub upuść tutaj zdjęcie'
        });
        $this.elFormRoot.find('ol.items-list').append(elHtml);
        $this.switchAdvancedModeFieldsVisibility($this.boolAdvancedMode);
        $this.orderizeItems();
        $this.numCurrentAddedItems++;
        $this.refreshItemsLeft();
        $this.adjustAddButtonsText();
        if (!empty(fnCallback)) {
            fnCallback();
        };
    };
    
    this.extractYoutubeVIdeoIdCallback = function (objData) {
        if (!empty(objData.strId)) {
            var elPreviewContainer = $this.elLastUsedVideoItemElement.find('div.preview');
            
            var numWidth = elPreviewContainer.width() - parseInt(elPreviewContainer.css('padding-left')) - parseInt(elPreviewContainer.css('padding-right'));
//            var numHeight = numWidth * 0.67;
            $this.elLastUsedVideoItemElement.find('div.preview').empty().append('<iframe width="'+numWidth+'" height="235" src="http://www.youtube.com/embed/'+objData.strId+'" frameborder="0"></iframe>');
        };
        $this.elLastUsedVideoItemElement = null;
        $this.objLoader.remove();
    };
    
    this.isCorrectYoutubeUrl = function (strUrl) {
        var boolReturn = false;
        var objParser = document.createElement('a');
        objParser.href = strUrl;
        if (!empty(objParser.hostname)) {
            if (objParser.hostname === 'youtube.com' || objParser.hostname === 'www.youtube.com' || objParser.hostname === 'youtu.be' || objParser.hostname === 'www.youtu.be') {
                if (objParser.pathname === '/watch' && !empty(objParser.search)) {
                    // /watch?v=XXX
                    var arrSearchTokens = objParser.search.split('&');
                    var arrSearchToken = [];
                    var i = 0;
                    for (i=0; i<arrSearchTokens.length; i++) {
                        arrSearchToken = arrSearchTokens[i].split('=');
                        if (arrSearchToken.length === 2 && arrSearchToken[0] === '?v') {
                            boolReturn = true;
                        };
                    };
                } else if (!empty(objParser.pathname) && objParser.pathname.substring(0, 1) === '/') {
                    // /XXX
                    var strPath = objParser.pathname.substring(1);
                    if (strPath.indexOf('/') === -1 && strPath.indexOf('&') === -1) {
                        boolReturn = true;
                    }
                };
            };
        };
        
        return boolReturn;
    };
    
    this.extractYoutubeVIdeoId = function (strUrl, strCallback) {
        $this.objLoader.add();
        JsonRpc2.post({
            context: $this, 
            data: {
                strUrl: strUrl
            }, 
            method: 'backend.YouTube.parseUrl', 
            callBack: strCallback
        });
        
    };
    
    this.adjustMemeItemHeight = function (elItem) {
        elItem.find('div.meme-generator img').load(function () {
            var numImgHeight = $(this).height();
            elItem.find('div.preview, div.item-no').css('height', numImgHeight);
            elItem.find('div.preview').css('height', numImgHeight);
            if (numImgHeight-200 < 120) {
                elItem.find('div.search-results').css('height', 120);
            } else {
                elItem.find('div.search-results').css('height', numImgHeight-200);
            };
        });
    };
    
    this.addItemMemBackgroundGet = function (objResponse) {
        var elHtml = $this.elFormRoot.find('div.templates li.item.mem').clone();
        
        var arrBackgrounds = objResponse.result.arrBackgroundImages;
        if (!empty(arrBackgrounds)) {
            var strImageBackgroundSrc = arrBackgrounds[0].meme_background_url;
            elHtml.find('div.meme-generator img').attr({
                'src': strImageBackgroundSrc, 
                'data-last-used-background-id' : arrBackgrounds[0].id
            });
            $this.adjustMemeItemHeight(elHtml);

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
            elPreviewTarget: elHtml.find('div.meme-generator img'), 
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
                    callJsonBack: 'memBackgroundSearchCallback'
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
        $this.switchAdvancedModeFieldsVisibility($this.boolAdvancedMode);
        $this.orderizeItems();
        $this.numCurrentAddedItems++;
        $this.refreshItemsLeft();
        $this.adjustAddButtonsText();
        $this.objLoader.remove();
        if (!empty($this.mulBuffor)) {
            $this.mulBuffor();
            $this.mulBuffor = null;
        }
    };
    
    this.memBackgroundSearchCallback = function (objResponse) {
        $this.elLastUserMemSearchElement.find('div.search-results').empty();
        $this.elLastUserMemSearchElement.find('input.search-image').attr('disabled', false);;
        if (objResponse.status === 0 || empty(objResponse.result.arrBackgroundImages)) {
            $this.elLastUserMemSearchElement.find('div.search-results').append('<p class="empty">Niestety, nic nie znaleźliśmy :(</p>');
        } else {
            var numCurrentSelectedBackgroundId = 0;
            if ($this.elLastUserMemSearchElement.find('div.meme-generator img').attr('src').substr(0, 7) === 'http://') {
                numCurrentSelectedBackgroundId = $this.elLastUserMemSearchElement.find('div.meme-generator img').attr('data-last-used-background-id');
            }
            $(objResponse.result.arrBackgroundImages).each(function (mulNull, objBackground) {
                
                var strThumbSrc = objBackground.meme_background_url;
                var elSearchItem = $('<div class="search-mem-background-result"></div>').click(function () {
                    $(this).closest('.search-results').find('div.search-mem-background-result.selected').removeClass('selected');
                    $(this).addClass('selected');
                    var selectedImageSrc = $(this).find('img').attr('src');
                    var numBackgroundId = $(this).find('img').data('meme-background-id');
                    $(this).closest('li.item.mem').find('div.meme-generator img').attr({
                        'src': selectedImageSrc, 
                        'data-last-used-background-id' : numBackgroundId
                    }).load(function () {
                        $this.adjustMemeItemHeight($(this).closest('li.item.mem'));
                    }); 
                });
                
                if (numCurrentSelectedBackgroundId === objBackground.id) {
                    elSearchItem.addClass('selected');
                }
                elSearchItem.append($('<img />').attr({
                    'src': strThumbSrc, 
                    'data-meme-background-id': objBackground.id
                }));
                $this.elLastUserMemSearchElement.find('div.search-results').append(elSearchItem);
            });
        };
    };
    
    this.bindItemEvents = function (elItemHtml) {
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
            $(elElement).find('div.item-no').text(numIterator+1);
        });
    };
    
    this.submitUpload = function () {
        var arrItemsHtmls = $this.elFormRoot.find('ol.items-list li.item');
        var boolIsCorrect = true;
        
        $this.elFormRoot.find('.error-info').remove();
        $this.elFormRoot.find('.errored').removeClass('errored');
        
        $(arrItemsHtmls).each(function(mulNull, elItemHtml) {
            elItemHtml = $(elItemHtml);
            if (elItemHtml.hasClass('image')) {
                boolIsCorrect = boolIsCorrect && $this.validateItemImage(elItemHtml);
            } else if (elItemHtml.hasClass('ytvideo')) {
                boolIsCorrect = boolIsCorrect && $this.validateItemYtVideo(elItemHtml);
            } else if (elItemHtml.hasClass('mem')) {
                boolIsCorrect = boolIsCorrect && $this.validateItemMeme(elItemHtml);
            }
        });
        
        if (boolIsCorrect === false) {
            var elScrollTo = $(".m-upload .errored:eq(0)");
            if (empty(elScrollTo)) {
                elScrollTo = $(".m-upload .error-info:eq(0)");
            }
            $('html, body').animate({
                scrollTop: elScrollTo.offset().top - 110
            }, 500);
        } else if ($this.numCurrentAddedItems > 1) {
            $this.showGalleryAdditionalInfoModal();
        } else {
            $this.saveArtifact();
        };
    };
    
    this.showGalleryAdditionalInfoModal = function () {
        var elGalleryModel = $('div.m-upload div.templates div#modal-gallery-info').clone();
        elGalleryModel.on('hidden.bs.modal', function (e) {
            elGalleryModel.remove();
        });
        elGalleryModel.modal('show');
        elGalleryModel.find('form').submit(function(e){
            e.preventDefault();
            elGalleryModel.find('.error-info').remove();
            elGalleryModel.find('.errored').removeClass('errored');
            var boolOk = $this.validateGalleryInfo(elGalleryModel);
            if (boolOk === true) {
                var strGalleryTitle = elGalleryModel.find('input[name="title"]').val();
                var strGalleryDescription = elGalleryModel.find('textarea[name="content"]').val();
                elGalleryModel.modal('hide');
                $this.saveArtifact(strGalleryTitle, strGalleryDescription);
            }
            return false;
        });
    };
    
    this.validateGalleryInfo = function (elModalHtml) {
        var boolOk = true;
        var elTitleField = elModalHtml.find('input[name="title"]');
        if (empty(elTitleField.val())) {
            elTitleField.addClass('errored').after('<div class="error-info">Podaj tytuł galerii</div>');
            boolOk = false;
        }
        
        return boolOk;
    };
    
    this.validateItemImage = function (elHtml) {
        var boolCorrect = true;
        var elTitleField = elHtml.find('input[name="title[]"]');
        if (empty(elTitleField.val())) {
            elTitleField.addClass('errored').after('<div class="error-info">Podaj tytuł zdjęcia</div>');
            boolCorrect = false;
        };
        
        var strImageJson = $.trim(elHtml.find('input[name="image[]"]').val());
        if (empty(strImageJson)) {
            elHtml.find('div.preview').append('<div class="error-info">Wybierz zdjęcie</div>');
            boolCorrect = false;
        };
        
        return boolCorrect;
    };
    
    this.validateItemYtVideo = function (elHtml) {
        var boolCorrect = true;
        
        var elTitleField = elHtml.find('input[name="title[]"]');
        if (empty(elTitleField.val())) {
            elTitleField.addClass('errored').after('<div class="error-info">Podaj tytuł filmu</div>');
            boolCorrect = false;
        };
        
        var elUrlField = elHtml.find('input[name="movie_url[]"]');
        if (empty(elUrlField.val())) {
            elUrlField.addClass('errored').after('<div class="error-info">Podaj adres URL video</div>');
            boolCorrect = false;
        } else if ($this.isCorrectYoutubeUrl(elUrlField.val()) === false) {
            elUrlField.addClass('errored').after('<div class="error-info">Niepoprawny adres URL z serwisu YouTube</div>');
            boolCorrect = false;
        };
        
        return boolCorrect;
    };
    
    this.validateItemMeme = function (elHtml) {
        var boolCorrect = true;
        
        var strImageJson = $.trim(elHtml.find('div.meme-generator img').attr('src'));
        if (empty(strImageJson) && empty(elHtml.find('div.meme-generator img').attr('data-last-used-background-id'))) {
            elHtml.find('div.preview').append('<div class="error-info">Nie wybrano tła mema/div>');
            boolCorrect = false;
        };
        
        var strMemeTitle = $.trim(elHtml.find('div.meme-generator textarea.mem-title').val());
        if (empty(strMemeTitle)) {
            elHtml.find('div.preview').append('<div class="error-info">Nie podano tytułu mema (górna treść)</div>');
            boolCorrect = false;
        }
        
        return boolCorrect;
    };
    
    this.grabElements = function () {
        var arrElements = [];
        
        $this.elFormRoot.find('ol.items-list li.item').each(function (numIterator, objRow) {
            var elRow = $(objRow);
            
            var objElement = { numOrdering: numIterator+1 };
            if (elRow.hasClass('image')) {
                objElement.numType = 1;
                objElement.strTitle = elRow.find('input[name="title[]"]').val();
                objElement.strDescription = elRow.find('textarea[name="content[]"]').val();
                
                objElement.arrImage = JSON.parse(elRow.find('input[type="hidden"][name="image[]"]').val());
                
                if ($this.boolAdvancedMode === true) {
                    objElement.strAuthor = elRow.find('input[name="author[]"]').val();
                }
                
            } else if (elRow.hasClass('ytvideo')) {
                objElement.numType = 2;
                objElement.strTitle = elRow.find('input[name="title[]"]').val();
                objElement.strDescription = elRow.find('textarea[name="content[]"]').val();
                
                objElement.strMovieUrl = $.trim(elRow.find('input[name="movie_url[]"]').val());
            } else if (elRow.hasClass('mem')) {
                objElement.numType = 3;
                objElement.strTitle = elRow.find('div.meme-generator .mem-title').val();
                objElement.strDescription = elRow.find('div.meme-generator .mem-text').val();
                
                var strBackgroundSrc = elRow.find('div.meme-generator img').attr('src');
                if (!empty(strBackgroundSrc) && strBackgroundSrc.substring(0, 10) === 'data:image') {
                    objElement.strBackground = strBackgroundSrc;
                } else {
                    objElement.numMemeBackgroundId = elRow.find('div.meme-generator img').attr('data-last-used-background-id');
                };
                objElement.numWidth = elRow.find('div.meme-generator img').width();
            }
            arrElements.push(objElement);
        });
        return arrElements;
    };
    
    this.saveArtifact = function (strGalleryTitle, strGalleryDescription) {
        $this.objLoader.add();
        var arrElements = $this.grabElements();
        var strClientIp = $('div.m-upload input[name="client_ip"]').val();
        
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
                strDescription: strGalleryDescription, 
                strClientIp: strClientIp
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
            var numArtifactId = objResponse.result.numId;
            
            var strAlertMsg = '<strong>Znakomicie!</strong> Twoja galeria została poprawnie wysłana. Za 3 sekundy zostaniesz do niej przekierowany';
            $this.elFormRoot.removeClass('items-added').empty();
            $this.elFormRoot.append('<div class="upload-success" role="alert">'+strAlertMsg+'</div>');
            
            $('html, body').animate({
                scrollTop: $this.elFormRoot.offset().top - 110
            }, 500);
            
            setTimeout(function () {
                window.location.replace(strArtifactUrl+'#uploaded-'+numArtifactId); 
           }, 3000);
        };
        
    };
    
};