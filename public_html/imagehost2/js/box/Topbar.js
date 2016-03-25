/* global Subframe, head, JsonRpc2, objSearch */
Subframe.Box.Topbar = function () {
    "use strict";

    var $this = this;
    this.elSearchForm = null;
    this.elSearchQueryInput = null;
    this.elUploadForm = null;
    this.elCurrentUploadModal = null;
    this.elUserAcccountPopover = null;
    $this.objLoader = {};
    $this.objFacebookConnectModel = {};
    $this.objValidationModel = {};
    
    this.numAddedPhotosToStory = 0;
    this.numImagesInStoryLimit = 20;
    

    this.init = function () {
        $this.objLoader = new Subframe.Lib.Loader();
    };


    this.launch = function () {
        head.load([
            '/imagehost2/js/model/FacebookConnect.js', 
//            '/imagehost2/js/model/Validation.js'
        ], function () {
            $this.objFacebookConnectModel = new Subframe.Model.FacebookConnect();
//            $this.objValidationModel = new Subframe.Model.Validation();
            $this.initUserAccountPopover();
        });
//        $this.initUploading();
        $this.initSearcher();
    };
    
    this.facebookConnectCallback = function (objResponse) {
        if (!empty(objResponse.error)) {
            $this.objLoader.remove();
        } else {
             setTimeout(function () {
                window.location.replace(objResponse.result.strRedirectTo);
            }, 1000);
        };
    };
    
    
    this.initSearcher = function () {
        $this.elSearchForm = $('form.top-search-form');
        $this.elSearchQueryInput = $this.elSearchForm.find('input[name="searchQuery"]');
        
        $this.elSearchForm.submit(function (e) {
            var strInput = $.trim($this.elSearchQueryInput.val());
            if (strInput.length < 3) {
                $this.elSearchQueryInput.css('background-color', '#f2dede');
                setTimeout(function () {
                    $('form.top-search-form input[name="searchQuery"]').css('background-color', '');
                    setTimeout(function () {
                        $('form.top-search-form input[name="searchQuery"]').css('background-color', '#f2dede');
                        setTimeout(function () {
                            $('form.top-search-form input[name="searchQuery"]').css('background-color', '');
                        }, 500);
                    }, 500);
                }, 500);
                e.preventDefault();
                return false;
            }
            ;
            return true;
        });
    };
    
//    this.showStoryUploadModal = function () {
////        $('.upload-story-button').click(function () {
//            $this.numAddedPhotosToStory = 0;
//            head.load([
//                getJsLibPath('ImageSelect.js')
////                'https://code.jquery.com/ui/1.11.3/jquery-ui.min.js'
//                
//            ], function () {
//                $this.elCurrentUploadModal = $('.modal-story-upload').clone();
//                $this.elCurrentSuggest = null;
//                
//                $this.elCurrentUploadModal.on('hidden.bs.modal', function (e) {
//                    $this.elCurrentUploadModal.remove();
//                });
//                $this.elCurrentUploadModal.modal('show');
//                
//                $this.elCurrentUploadModal.find('button#story-add-image').click(function (e) {
//                    e.preventDefault();
//                    $this.addImageToStory($this.elCurrentUploadModal);
//                    return false;
//                });
//                $this.elCurrentUploadModal.find('button#story-add-yt-movie').click(function (e) {
//                    e.preventDefault();
//                    $this.addMovieToStory($this.elCurrentUploadModal);
//                    return false;
//                });
//                
//                $this.elCurrentUploadModal.find('form').submit(function (e) {
//                    e.preventDefault();
//                    $this.uploadStory();
//                    return false;
//                });
//                
//                $this.addImageToStory($this.elCurrentUploadModal);
//            });
////        });
//    };
//    
//    this.reorganizeStoryElements = function () {
//        $this.elCurrentUploadModal.find('ol.images-container li:eq(0) div.story-element-remove-row').hide();
//        $this.elCurrentUploadModal.find('ol.images-container li').each(function (numIndex, elRow) {
//            $(elRow).find('span.imageno').text(numIndex+1);
//        });
//    };
//    
//    this.removeStoryElementRow = function (elImageRow) {
//        elImageRow.slideUp('fast', function () {
//            $(this).closest('li').remove();
//            $this.reorganizeStoryElements();
//        });
//    };
//    
//    this.addImageToStory = function (elContainer) {
//        $this.numAddedPhotosToStory++;
//        var numImagesLeft = $this.numImagesInStoryLimit - $this.numAddedPhotosToStory;
//        var elImageRow = elContainer.find('div.story-image-row.template').clone();
//        elImageRow.removeClass('template');
//        
//        elImageRow.find('span.imageno').text($this.numAddedPhotosToStory);
//        elImageRow.find('input[name="image[]"]').ImageSelect();
//        elImageRow.find('button.remove-story-element').click(function (e) {
//            e.preventDefault();
//            $this.removeStoryElementRow(elImageRow);
//            return false;
//        });
//        elContainer.find('ol.images-container').append($('<li></li>').append(elImageRow));
//        elImageRow.slideDown('fast');
//        elContainer.find('span#story-elements-left').text(numImagesLeft);
//        
//        if (numImagesLeft === 0) {
//            $this.imagesInStoryLimitReached(elContainer);
//        };
//        
//        $this.reorganizeStoryElements();
//    };
//    
//    this.addMovieToStory = function (elContainer) {
//        $this.numAddedPhotosToStory++;
//        var numImagesLeft = $this.numImagesInStoryLimit - $this.numAddedPhotosToStory;
//        var elImageRow = elContainer.find('div.story-yt-movie-row.template').clone();
//        elImageRow.removeClass('template');
//        
//        elImageRow.find('span.imageno').text($this.numAddedPhotosToStory);
//        elImageRow.find('input[name="image[]"]').ImageSelect();
//        elImageRow.find('button.remove-story-element').click(function (e) {
//            e.preventDefault();
//            $this.removeStoryElementRow(elImageRow);
//            return false;
//        });
//        elContainer.find('ol.images-container').append($('<li></li>').append(elImageRow));
//        elImageRow.slideDown('fast');
//        elContainer.find('span#story-elements-left').text(numImagesLeft);
//        
//        if (numImagesLeft === 0) {
//            $this.imagesInStoryLimitReached(elContainer);
//        };
//        
//        $this.reorganizeStoryElements();
//    };
//    
//    this.imagesInStoryLimitReached = function (elContainer) {
//        elContainer.find('button#story-add-image').attr('disabled', 'disabled').unbind('click').click(function (e) {
//            e.preventDefault();
//            return false;
//        });
//        elContainer.find('span#story-elements-left').closest('p').text('Do jednej galerii można dodać max '+$this.numImagesInStoryLimit+' obrazków');
//    };
//    
//    this.initUploading = function () {
//        $('.upload-button').click(function () {
//            $this.showUploadSelectModal();
//        });
//    };
//    
//    this.showUploadSelectModal = function () {
//        head.load([
//            '/imagehost2/js/libs/ImageSelect.js',
//            'https://code.jquery.com/ui/1.11.3/jquery-ui.min.js'
//        ], function () {
//            $this.elCurrentUploadModal = $('.modal-upload-select').clone();
//            $this.elCurrentUploadModal.find('.upload-modal-multi').click(function () {
//                $this.elCurrentUploadModal.on('hidden.bs.modal', function (e) {
//                    $this.elCurrentUploadModal.remove();
//                    $this.showStoryUploadModal();
//                }).modal('hide');
//            });
//            $this.elCurrentUploadModal.find('.upload-modal-single').click(function () {
//                $this.elCurrentUploadModal.on('hidden.bs.modal', function (e) {
//                    $this.elCurrentUploadModal.remove();
//                    $this.showUploadModal();
//                }).modal('hide');
//            });
//            $this.elCurrentUploadModal.on('hidden.bs.modal', function (e) {
//                $this.elCurrentUploadModal.remove();
//            });
//            $this.elCurrentUploadModal.modal('show');
//        });
//    };
//    
//    this.showUploadModal = function () {
//        head.load([
//            '/imagehost2/js/libs/ImageSelect.js'
//        ], function () {
//            $this.elCurrentUploadModal = $('.modal-upload').clone();
//            $this.elCurrentSuggest = null;
//
//            $this.elCurrentUploadModal.on('hidden.bs.modal', function (e) {
//                $this.elCurrentUploadModal.remove();
//            });
//            $this.elCurrentUploadModal.modal('show');
//            $this.elCurrentUploadModal.find('input[name="image"]').ImageSelect();
//            $this.elCurrentUploadModal.find('form').submit(function (e) {
//                e.preventDefault();
//                $this.uploadArtifact();
//                return false;
//            });
//        });
//    };
    
    this.initUserAccountPopover = function () {
        $this.elUserAcccountPopover = $("button.user-account-trigger").popover({
            trigger: 'click',
            placement: 'bottom',
            html: 'true', 
            content: function () {
                var strContent = $('.user-account-inner').html(); 
                return strContent;
            }
        }).parent().on('click', '.social-buttons .btn-facebook', function() {
            $this.objLoader.add();
            $this.objFacebookConnectModel.showFacebookConnect($this, 'facebookConnectCallback');
        });
    };
    
//    this.uploadStory =  function () {
//
//        $this.elCurrentUploadModal.find('.alert').remove();
//        var arrErrors = [];
//        var strTitle = $.trim($this.elCurrentUploadModal.find('input[name="story-title"]').val()) || '';
//        var strDescription = $.trim($this.elCurrentUploadModal.find('textarea[name="story-description"]').val()) || '';
//        var strNewAccountEmail = $.trim($this.elCurrentUploadModal.find('input[name="account_email"]').val());
//        
//        var arrElements = [];
//        
//        if (empty(strTitle)) {
//            arrErrors.push('Nie podano tytułu galerii');
//        };
//        
//        var arrRows = $this.elCurrentUploadModal.find('div.story-image-row:not(.template), div.story-yt-movie-row:not(.template)');
//        arrRows.each(function (numIterator, objRow) {
//            var elRow = $(objRow);
//            var objElement = {
//                numOrdering: numIterator+1, 
//                strTitle: elRow.find('input[name="title[]"]').val(), 
//               strDescription: elRow.find('textarea[name="content[]"]').val()
//            };
//            if (elRow.hasClass('story-image-row')) {
//                objElement.numType = 1;
//                objElement.arrImage = JSON.parse(elRow.find('input[type="hidden"][name="image[]"]').val());
//            } else {
//                objElement.numType = 2;
//                objElement.strMovieUrl = $.trim(elRow.find('input[name="movie_url[]"]').val());
//            }
//            arrElements.push(objElement);
//        });
//        if (empty(arrErrors)) {
//            if (arrElements.length === 0) {
//                arrErrors.push('Nie dodano żadnego elementu');
//            } else if (arrElements.length === 1) {
//                arrErrors.push('Galeria musi składać się z co najmniej 2 elementów');
//            };
//        };
//        
//        if (!empty(strNewAccountEmail) && $this.objValidationModel.email(strNewAccountEmail) === false) {
//            arrErrors.push('Podano nieprawidłowy adres e-mail');
//        };
//        
//        if (!empty(arrErrors)) {
//            $this.elCurrentUploadModal.find('.modal-body').append('<div class="alert alert-danger" role="alert">' + arrErrors.join('<br />') + '</div>');
//        } else {
//            
//            $this.objLoader.add();
//            JsonRpc2.post({
//                context: $this,
//                data: {
//                    arrElements: arrElements, 
//                    strTitle: strTitle,
//                    strDescription: strDescription
//                },
//                method: 'backend.artifact.ArtifactController.upload',
//                callBack: 'uploadStoryCallback', 
//                extends: {
//                    progress: 'uploadProgress'
//                }
//            });
//        };
//    };
    
//    this.isCorrectYoutubeUrl = function (strUrl) {
//        var boolReturn = false;
//        var objParser = document.createElement('a');
//        objParser.href = strUrl;
//        if (!empty(objParser.hostname)) {
//            if (objParser.hostname === 'youtube.com' || objParser.hostname === 'www.youtube.com' || objParser.hostname === 'youtu.be' || objParser.hostname === 'www.youtu.be') {
//                if (objParser.pathname === '/watch' && !empty(objParser.search)) {
//                    var arrSearchTokens = objParser.search.split('&');
//                    var arrSearchToken = [];
//                    var i = 0;
//                    for (i=0; i<arrSearchTokens.length; i++) {
//                        arrSearchToken = arrSearchTokens[i].split('=');
//                        if (arrSearchToken.length === 2 && arrSearchToken[0] === '?v') {
//                            boolReturn = true;
//                        };
//                    };
//                };
//            };
//        };
//        
//        return boolReturn;
//    };

//    this.uploadArtifact = function () {
//
//        $this.elCurrentUploadModal.find('.alert').remove();
//
//        var arrErrors = [];
//        var arrElements = [];
//        var arrElement = [];
//        var arrImage = [];
//        var strImageJson = $.trim($this.elCurrentUploadModal.find('input[name="image"]').val());;
//        var strTitle = $.trim($this.elCurrentUploadModal.find('input[name="title"]').val());
//        var strContent = $.trim($this.elCurrentUploadModal.find('textarea[name="content"]').val());
//        var strNewAccountEmail = $.trim($this.elCurrentUploadModal.find('input[name="account_email"]').val());
//        
//        if (empty(strImageJson)) {
//            arrErrors.push('Nie wybrano zdjęcia');
//        };
//
//        if (empty(strTitle)) {
//            arrErrors.push('Nie podano tytułu zdjęcia');
//        };
//        
//        if (!empty(strNewAccountEmail) && $this.objValidationModel.email(strNewAccountEmail) === false) {
//            arrErrors.push('Podano nieprawidłowy adres e-mail');
//        };
//
//        if (!empty(arrErrors)) {
//            $this.elCurrentUploadModal.find('.modal-body').append('<div class="alert alert-danger" role="alert">' + arrErrors.join('<br />') + '</div>');
//        } else {
//            $this.objLoader.add();
//            
//            arrImage = JSON.parse(strImageJson);
//            
//            arrElements.push({
//                'numOrdering': 1, 
//                'strTitle': strTitle, 
//                'strDescription': strContent, 
//                'numType': 1, // 1 - image, 2 - ytvideo
//                'arrImage': arrImage
//            });
//
//
//            
//            JsonRpc2.post({
//                context: $this,
//                data: {
//                        arrElements: arrElements, 
//                        strTitle: strTitle,
//                        strDescription: strContent
//                },
//                method: 'backend.artifact.ArtifactController.upload',
//                callBack: 'uploadCallback', 
//                extends: {
//                    progress: 'uploadProgress'
//                }
//            });
//        }
//        ;
//    };
//    
//    this.uploadProgress = function (objPartial) {
//        if (objPartial.event.lengthComputable) {
//            var numLoadedPart = objPartial.event.loaded / objPartial.event.total;
//            numLoadedPart -= 0.02; // leave a little bit of free time for php processing
//            $this.objLoader.updateProgressBar(numLoadedPart);
//        };
//    };
//
//    this.uploadCallback = function (objResponse) {
//        $this.objLoader.updateProgressBar(1);
//        if (!empty(objResponse.error)) {
//            $this.elCurrentUploadModal.find('.modal-footer').prepend('<div class="alert alert-danger" role="alert">' + objResponse.error.join('<br />') + '</div>');
//        } else if (!empty(objResponse.result)) {
//            var strArtifactUrl = objResponse.result.strUrl;
//            var strMessage = '<div class="alert alert-success" role="alert">';
//            strMessage += '<strong>Znakomicie!</strong> Twoje zdjęcie zostało poprawnie wysłane. Dostępne jest pod linkiem: <a href="' + strArtifactUrl + '">' + strArtifactUrl + '</a>';
//            strMessage += '</div>';
//            $this.elCurrentUploadModal.find('.modal-footer').empty().html('<button type="submit" class="btn btn-default" data-dismiss="modal">Super! Teraz zamknij to okno</button>');
//            $this.elCurrentUploadModal.find('.modal-body').empty().html(strMessage);
//        };
//        $this.objLoader.remove();
//    };
//    
//    
//    this.uploadStoryCallback = function (objResponse) {
//        $this.objLoader.updateProgressBar(1);
//        if (!empty(objResponse.error)) {
//            $this.elCurrentUploadModal.find('.modal-body').append('<div class="alert alert-danger" role="alert">' + objResponse.error.join('<br />') + '</div>');
//        } else if (!empty(objResponse.result)) {
//            var strArtifactUrl = objResponse.result.strUrl;
//            var strMessage = '<div class="alert alert-success" role="alert">';
//            strMessage += '<strong>Znakomicie!</strong> Twoja galeria została poprawnie wysłana. Dostępna jest pod linkiem: <a href="' + strArtifactUrl + '">' + strArtifactUrl + '</a>';
//            strMessage += '</div>';
//            $this.elCurrentUploadModal.find('.modal-footer').empty().html('<button type="submit" class="btn btn-default" data-dismiss="modal">Super! Teraz zamknij to okno</button>');
//            $this.elCurrentUploadModal.find('.modal-body').empty().html(strMessage);
//        };
//        $this.objLoader.remove();
//    };

};
