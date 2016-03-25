(function ($) {
    
    $.fn.ImageSelect = function (objOptions) {
        var elSourceFileField = null, elWrapper = null, elCropResultField = null;
        var elUploadSpace = null, elPreviewModeWrapper = null;
        var objResultImageCanvas = {}, objSelectedImage = {}, objInputFileFileReader = {};
        var objPreviewImage = {}, objResult = {};
        var objSettings = $.extend({}, $.fn.ImageSelect.objDefaultSettings, objOptions);
    
        this.filter('input[type="file"]').each(function () {
            elSourceFileField = $(this);
            elWrapper = $('<div />').attr('class', objSettings.strWrapperCssClass);
            elSourceFileField.before(elWrapper)/*.attr('accept', 'image/*')*/.hide();
            
            objResultImageCanvas = $('<canvas>');
            
            prepareUI();
            makeUploadable();
        });
        
        function prepareUI () {
            var strName = elSourceFileField.attr('name');
            elSourceFileField.removeAttr('name');
            elCropResultField = $('<input />').attr({
                type: 'hidden', 
                name: strName
            });
            
            elSourceFileField.after(elCropResultField);
            
//            elUploadSpace = $('<div />').addClass('upload-space').append(
//                $('<div />').addClass('icon'), 
//                $('<span />').addClass('message').text(objSettings.strUploadLabel)
//            );
            elUploadSpace = $('<div />').addClass('upload-space').text(objSettings.strUploadLabel);
    
            elPreviewModeWrapper = $('<div />').css({
                margin: 'auto'
            }).addClass('preview-wrapper').hide();
    
            elWrapper.append(elUploadSpace).append(elPreviewModeWrapper);
        };
        
        function makeUploadable() {

            elSourceFileField.on('change', function () {
                inputFileSelected($(this)[0].files[0]);
            });

            elUploadSpace.on({
                click: function () {
                    elSourceFileField.click();
                },
                mouseenter: function () {
                    $(this).addClass('hovered');
                },
                mouseleave: function () {
                    $(this).removeClass('hovered');
                },
                dragenter: function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).addClass('hover-file');
                    return false;
                },
                dragleave: function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).removeClass('hover-file');
                    return false;
                },
                dragover: function (e) {
                    e.preventDefault();
                    return false;
                }, 
                drop: function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    inputFileSelected(e.originalEvent.dataTransfer.files[0]);
                    return false;
                }
            });
        };
        
        function inputFileSelected(objFile) {
            if (isSelectedImageTypeValid(objFile) === false) {
                alert('Możesz dodać tylko zdjęcia');   
            } else if (isSelectedImageSizeValid(objFile) === false) {
                alert('Maksymalny rozmiar zdjęcia możliwy do dodania to 20MB');   
            } else {
                objSelectedImage = objFile;
                preparePreviewMode();
            };
        };
        
        function isSelectedImageTypeValid(objSelectedImage) {
            if (!(/image/i).test(objSelectedImage.type)) {
                return false;
            }
            ;
            return true;
        };
        
        function isSelectedImageSizeValid(objSelectedImage) {
            
            if (objSelectedImage.size > (1024 * 1024 * 20)) {
                return false;
            }
            ;
            return true;
        };
        
        function preparePreviewMode () {
            if (empty(objSettings.elPreviewTarget)) {
                elUploadSpace.hide();
                elPreviewModeWrapper.show();
            };
            
            objInputFileFileReader = new FileReader();
            objInputFileFileReader.readAsDataURL(objSelectedImage);
            objInputFileFileReader.onload = function () {
                selectedImageUploaded();
            };
        };
        
        function selectedImageUploaded() {
            if (!empty(objSettings.elPreviewTarget)) {
                objSettings.elPreviewTarget.attr({
                    src: objInputFileFileReader.result
                }).css('max-width', '100%');
            } else {
                objPreviewImage = new Image();
                objPreviewImage = $('<img />').attr({
                    src: objInputFileFileReader.result
                }).css('max-width', '100%');
                elPreviewModeWrapper.append(objPreviewImage);
            };
            updateResultField();
        };
        
        function updateResultField () {
            objResult.base64 = objInputFileFileReader.result;
            objResult.userFilename = elSourceFileField.val().split('\\').pop();
            
            elCropResultField.val(JSON.stringify(objResult));
        };
    };
    
    $.fn.ImageSelect.objDefaultSettings = {
        strWrapperCssClass: 'image-selector-wrapper', 
        elPreviewTarget: null, 
        strUploadLabel: 'Wybierz lub upuść tutaj zdjęcie'
    };
    
}(jQuery));