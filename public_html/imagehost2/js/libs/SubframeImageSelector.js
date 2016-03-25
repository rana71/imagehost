(function ($) {

    $.fn.SubframeImageSelector = function (objOptions) {
        var elSourceFileField = null, elWrapper = null, elUploadSpace = null;
        var elCroppingModeWrapper = null, elCroppingImageWrapper = null;
        var elCroppingForeground = null, elCroppingForegroundDragArea = null;
        var elCropResultField = null;
        var objSettings = {}, objSelectedImage = {};
        var objCroppingBackgroundImage = {}, objCroppingForegroundImage = {};
        var objInputFileFileReader = {}, objResultImageCanvas = {}, objResult = {};

        objSettings = $.extend({}, $.fn.SubframeImageSelector.objDefaultSettings, objOptions);

        this.filter('input[type="file"]').each(function () {
            elSourceFileField = $(this);
            elWrapper = $('<div />').attr('class', objSettings.strWrapperCssClass);
            elSourceFileField.before(elWrapper)/*.attr('accept', 'image/*')*/.hide();
            
            objResultImageCanvas = $('<canvas>');
            objResultImageCanvas.get(0).getContext('2d').canvas.width = objSettings.numSelectorWidth;
            objResultImageCanvas.get(0).getContext('2d').canvas.height = objSettings.numSelectorHeight;
            
            prepareUI();
            makeUploadable();
        });

        function prepareUI() {
            var strName = elSourceFileField.attr('name');
            elSourceFileField.removeAttr('name');
            elCropResultField = $('<input />').attr({
                type: 'hidden', 
                name: strName
            });
            
            elSourceFileField.after(elCropResultField);
            
            elCroppingForeground = $('<div />').css({
                width: objSettings.numSelectorWidth + 'px',
                height: objSettings.numSelectorHeight + 'px',
                zIndex: 2,
                position: 'relative',
                borderRadius: '666px',
                overflow: 'hidden',
            });

            elUploadSpace = $('<div />').css({
                width: objSettings.numSelectorWidth + 'px',
                height: objSettings.numSelectorHeight + 'px',
                cursor: 'pointer',
                textAlign: 'center', 
                margin: 'auto'
            }).append(
                    $('<div />').addClass('glyphicon glyphicon-user').css({
                'font-size': '60px',
                'margin-top': '38px'
            })
                    );

            elCroppingModeWrapper = $('<div />').css({
                width: objSettings.numSelectorWidth + 'px',
                height: objSettings.numSelectorHeight + 'px',
                margin: 'auto'
            }).hide();
            elCroppingImageWrapper = $('<div />').css({
                position: 'relative',
                overflow: 'hidden',
                'width': objSettings.numSelectorWidth + 'px',
                'height': objSettings.numSelectorHeight + 'px'
            });
            elCroppingModeWrapper.append(elCroppingImageWrapper);

            elWrapper.append(elUploadSpace).append(elCroppingModeWrapper);
        }
        ;

        function makeUploadable() {

            elSourceFileField.on('change', function () {
                inputFileSelected($(this)[0].files[0]);
            });

            elUploadSpace.on({
                click: function () {
                    elSourceFileField.click();
                },
                mouseenter: function () {
                    $(this).find('div.glyphicon').removeClass('glyphicon-user').addClass('glyphicon-cloud-upload');
                },
                mouseleave: function () {
                    $(this).find('div.glyphicon').removeClass('glyphicon-cloud-upload').addClass('glyphicon-user');
                },
                dragenter: function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).find('div.glyphicon').removeClass('glyphicon-user').addClass('glyphicon-cloud-upload');
                },
                dragleave: function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $(this).find('div.glyphicon').removeClass('glyphicon-cloud-upload').addClass('glyphicon-user');
                },
                drop: function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    alert('drop catch');

//                                inputFileSelected(e.originalEvent.dataTransfer.files[0]);
                }
            });




        }
        ;

        function inputFileSelected(objFile) {
            if (isSelectedImageValid(objFile)) {
                objSelectedImage = objFile;
                prepareCroppingMode();
            } else {
                alert('We accept only image files');
            }
            ;
        }
        ;

        function prepareCroppingMode() {
            elUploadSpace.hide();
            elCroppingModeWrapper.show();

            objInputFileFileReader = new FileReader();
            objInputFileFileReader.readAsDataURL(objSelectedImage);
            objInputFileFileReader.onload = function () {
                selectedImageUploaded();
            };
        }
        ;
        
        function updateResult() {
            var objCoords = {
                y: $(objCroppingForegroundImage).height() - parseInt($(objCroppingForegroundImage).css('top')) - objSettings.numSelectorHeight,  
                x: $(objCroppingForegroundImage).width() - parseInt($(objCroppingForegroundImage).css('left')) - objSettings.numSelectorWidth
            };
            
            objResultImageCanvas.get(0).getContext('2d').drawImage(
                objCroppingForegroundImage,
                objCoords.x,
                objCoords.y,
                objSettings.numSelectorWidth,
                objSettings.numSelectorHeight,
                0,
                0,
                objSettings.numSelectorWidth,
                objSettings.numSelectorHeight
            );
            objResult.base64 = objResultImageCanvas.get(0).toDataURL();
            objResult.userFilename = elSourceFileField.val().split('\\').pop();
            
            elCropResultField.val(JSON.stringify(objResult));
//            elCropResultField.after($('<img />').attr({
//                src: objResult.base64
//            }));
        };

        function selectedImageUploaded() {
            objCroppingForegroundImage = new Image();
            objCroppingBackgroundImage = new Image();

            objCroppingForegroundImage.src = objInputFileFileReader.result;
            objCroppingBackgroundImage.src = objCroppingForegroundImage.src;

            $(objCroppingBackgroundImage).css({
                position: 'absolute',
                top: '0',
                left: '0',
                marginLeft: objSettings.numSelectorWidth / 2,
                marginTop: objSettings.numSelectorHeight / 2,
                opacity: 0.5
            });

            objCroppingBackgroundImage.onload = function () {

                updateResult();

                var numAreaWidth = 2 * objCroppingForegroundImage.width - objSettings.numSelectorWidth;
                var numAreaHeight = 2 * objCroppingForegroundImage.height - objSettings.numSelectorHeight;

                elCroppingForegroundDragArea = $('<div />').css({
                    position: 'absolute',
                    width: numAreaWidth + 'px',
                    height: numAreaHeight + 'px',
                    left: (0 - (numAreaWidth / 2)) + 'px',
                    top: (0 - (numAreaHeight / 2)) + 'px',
                    marginTop: '50%',
                    marginLeft: '50%'
                });

                var numBackgroundPositionTop = 0 - objCroppingBackgroundImage.height / 2;
                var numBackgroundPositionLeft = 0 - objCroppingBackgroundImage.width / 2;

                $(objCroppingBackgroundImage).css({
                    top: numBackgroundPositionTop,
                    left: numBackgroundPositionLeft
                });


                var numForegroundImageInitPositionTop = numAreaHeight / 2 - objCroppingForegroundImage.height / 2;
                var numForegroundImageInitPositionLeft = numAreaWidth / 2 - objCroppingForegroundImage.width / 2;


                var objLastPosition = {top: numForegroundImageInitPositionTop, left: numForegroundImageInitPositionLeft};

                var objPosition = {};
                $(objCroppingForegroundImage).css({
                    cursor: 'move',
                    position: 'absolute',
                    left: numForegroundImageInitPositionLeft,
                    top: numForegroundImageInitPositionTop
                }).draggable({
                    containment: "parent",
                    drag: function () {
                        objPosition = $(this).position();
                        numBackgroundPositionTop += objPosition.top - objLastPosition.top;
                        numBackgroundPositionLeft += objPosition.left - objLastPosition.left;

                        $(objCroppingBackgroundImage).css({
                            left: numBackgroundPositionLeft,
                            top: numBackgroundPositionTop
                        });

                        objLastPosition = objPosition;
                    },
                    stop: function () {
                        objPosition = $(this).position();
                        numBackgroundPositionTop += objPosition.top - objLastPosition.top;
                        numBackgroundPositionLeft += objPosition.left - objLastPosition.left;

                        $(objCroppingBackgroundImage).css({
                            left: numBackgroundPositionLeft,
                            top: numBackgroundPositionTop
                        });

                        objLastPosition = objPosition;
                        
                        updateResult();
                    }
                });


                elCroppingImageWrapper.append(objCroppingBackgroundImage).append(
                        elCroppingForeground.append(
                                elCroppingForegroundDragArea.append(objCroppingForegroundImage)
                                )
                        );
            };
        }
        ;

        function moveCroppedImage(objEvent) {
            console.log(objEvent);
        }
        ;

        function isSelectedImageValid(objSelectedImage) {
            if (!(/image/i).test(objSelectedImage.type)) {
                return false;
            }
            ;
            return true;
        }
        ;

    };

    $.fn.SubframeImageSelector.objDefaultSettings = {
        strWrapperCssClass: 'image-selector-wrapper',
        numSelectorWidth: 150,
        numSelectorHeight: 150
    };


}(jQuery));