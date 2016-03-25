/* global Subframe, head, JsonRpc2, objSearch */
Subframe.Box.artifact_Info = function () {
    // purge test
    "use strict";

    var $this = this;
    this.objCurrentModalElement = {};

    this.init = function () {
        $this.objLoader = new Subframe.Lib.Loader();
    };
    this.elCurrentReportModal = null;
    this.strVotingCookieName = '';
    
    this.arrPuppiesImages = [
        'http://static.imged.pl/allow-puppies-1-big.jpg', 
        'http://static.imged.pl/allow-puppies-2-big.jpg', 
        'http://static.imged.pl/allow-puppies-3-big.jpg'
    ];

    this.launch = function () {
        $this.strVotingCookieName = 'imagehost-vote-'+$('input#artifact-id').val()+'-'+$('input#vote-environment').val();
        
        $this.actionsAfterUpload();
        $this.initElementsOptions();
        $this.initOverlayOptions();
        $this.initArtifactReporting();
        $this.initArtifactEmbeed();
        $this.checkForPuppiesBlocked();
    };
    
    this.checkForPuppiesBlocked = function () {
        var boolIsPuppiesBlocked = isClientBlockPuppies();
        var numPuppiesThumbsIterator = 0;
        
        if (boolIsPuppiesBlocked === true) {
            $('div.m-artifact ol.elements div.thumb a.object img').each(function () {
                $(this).attr('src', $this.arrPuppiesImages[numPuppiesThumbsIterator]);
                numPuppiesThumbsIterator++;
                if (numPuppiesThumbsIterator === $this.arrPuppiesImages.length) {
                    numPuppiesThumbsIterator = 0;
                };
            });
        };
    };
    
    this.actionsAfterUpload = function () {
        var strHashSubstring = 'uploaded-';
        var strHash = window.location.hash.replace('#', '');
        if (!empty(strHash) && strHash.substr(0, strHashSubstring.length) === strHashSubstring) {
            $('div.m-artifact ol.elements div.thumb:eq(0)').addClass('show-overlay');
            
            var numArtifactId =  strHash.substr(strHashSubstring.length);
            ga('send', 'pageview', '/virt/upload-ok-'+numArtifactId);
        };
    };
    
    this.initOverlayOptions = function () {
        $('div.m-artifact div.thumb.show-overlay div.options-overlay').mouseout(function () {
            $(this).closest('div.thumb.show-overlay').removeClass('show-overlay');
        });
        
        $('div.m-artifact div.options-overlay a.l.get-code').click(function (e) {
            e.preventDefault();
            var numElementId = $(this).data('element-id');
            var strModalId = 'modal-embeed-element-'+numElementId;
            $this.showModal($("#"+strModalId));
            return false;
        });
        
        $('div.m-artifact div.options-overlay div.l.get-link').click(function () {
            $this.showModal($("#modal-embeed"));
        });
        
        $('div.m-artifact div.options-overlay a.preview').click(function (e) {
            e.preventDefault();
            var strModalId = 'modal-preview-' + $(this).data('element-id');
            $this.showModal($('#'+strModalId));
            return false;
        });
    };
    
    this.showModal = function (elModalElement) {
        if (!empty($this.objCurrentModalElement)) {
            $this.objCurrentModalElement.modal('hide');
        };
        $this.objCurrentModalElement = elModalElement;
        $this.objCurrentModalElement.on('show.bs.modal', function () {
            $(this).find('.modal-body').css({
              width:'auto', //probably not needed
//              height:'auto', //probably not needed 
//              'max-height':'100%'
        });
        }).modal('show').on('hidden.bs.modal', function (e) {
            $this.objCurrentModalElement = {};
        });
    };
    
    this.initElementsOptions = function () {
        
        
        $('div.m-artifact ol.elements div.options').click(function () {
            var elOptionsTrigger = $(this);
            var elOptionsPanel = elOptionsTrigger.next('div.options-tooltip');
            if (elOptionsPanel.is(':hidden')) {
                $(this).addClass('hightlighted');
                elOptionsPanel.show();
                elOptionsPanel.find('a.element-code').click(function (e) {
                    e.preventDefault();
                    var numElementId = $(this).data('element-id');
                    var strModalId = 'modal-embeed-element-'+numElementId;
                    $this.showModal($("#"+strModalId));
                    elOptionsTrigger.removeClass('hightlighted');
                    elOptionsPanel.hide();
                    return false;
                });
            } else if (elOptionsPanel.is(':visible')) {
                elOptionsTrigger.removeClass('hightlighted');
                elOptionsPanel.hide();
            }
        });
    };
    
    this.initVoting = function () {
        /**
         * change to local if need in future !
         */
        head.load('https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js', function () {
            if (parseInt($.cookie($this.strVotingCookieName)) === 1) {
                $('div.voting').addClass('liked');
            } else if (parseInt($.cookie($this.strVotingCookieName)) === -1) {
                $('div.voting').addClass('disliked');
            };
            
            $('div.voting .like').click(function () {
                 if (!$('div.voting').hasClass('liked')) {
                     $this.goVote(1);
                 };
             });
             $('div.voting .dislike').click(function () {
                 if (!$('div.voting').hasClass('disliked')) {
                     $this.goVote(-1);
                 };
             }); 
             $('div.voting-container div.loading-inline').fadeOut('fast');
             $('div.voting-container div.voting').fadeIn('fast');
        });
    };
    
    this.goVote = function (numModifier) {
        var numCurrentMark = parseInt($('#artifact-likes').text());
        var numNewMark = numCurrentMark;
        
        $('div.voting').removeClass('liked').removeClass('disliked');
        if (numModifier > 0) {
            $('div.voting').addClass('liked');
            numNewMark++;
            $('#artifact-likes').text(numNewMark).css('color', 'green');
        } else {
            $('div.voting').addClass('disliked');
            numNewMark--;
            $('#artifact-likes').text(numNewMark).css('color', 'red');
        };
        
        JsonRpc2.post({
            context: $this, 
            data: {
                numArtifactId: $('input#artifact-id').val(), 
                numModifier: numModifier
            }, 
            method: 'artifact.ArtifactController.goVote', 
            callBack: 'goVoteCallback'
        });
        
    };
    
    this.goVoteCallback = function (objResponse) {
        var numModifier = objResponse.result.numModifier;
        $.cookie($this.strVotingCookieName, numModifier, { expires: 9999, path: '/' });
    };
    
    this.initArtifactReporting = function () {
        
        $('div.m-artifact div.global-options span.abuse').click(function () {
            $this.elCurrentReportModal = $("#modal-report").clone();
            $this.elCurrentReportModal.on('hidden.bs.modal', function (e) {
                $this.elCurrentReportModal.remove();
            });
            $this.elCurrentReportModal.modal('show');
            
            
            $this.elCurrentReportModal.find('form input#input-url').val(window.location.toString());
            
            $this.elCurrentReportModal.find('form').submit(function (e) {
                e.preventDefault();
                
                $this.elCurrentReportModal.find('form .error-info').remove();
                $this.elCurrentReportModal.find('form .errored').removeClass('errored');
                $this.objLoader.add();
                
                var elFieldName = $this.elCurrentReportModal.find('form input#input-name');
                var elFieldEmail = $this.elCurrentReportModal.find('form input#input-email');
                var elFieldUrl = $this.elCurrentReportModal.find('form input#input-url');
                var elFieldReason = $this.elCurrentReportModal.find('form textarea#input-reason');
                
                
                var boolIsValid = true;
                
                if (elFieldName.val() === '') {
                    boolIsValid = false;
                    elFieldName.addClass('errored');
                    elFieldName.closest('div.r').append('<div class="error-info">Nie podano imienia</div>');
                }
                
                if (elFieldEmail.val() === '') {
                    boolIsValid = false;
                    elFieldEmail.addClass('errored');
                    elFieldEmail.closest('div.r').append('<div class="error-info">Nie podano adresu e-mail</div>');
                } else if (elFieldEmail.val().indexOf('@') === -1) {
                    elFieldEmail.addClass('errored');
                    elFieldEmail.closest('div.r').append('<div class="error-info">Nieprawidłowy format adresu e-mail</div>');
                };
                
                if (elFieldUrl.val() === '') {
                    boolIsValid = false;
                    elFieldUrl.addClass('errored');
                    elFieldUrl.closest('div.r').append('<div class="error-info">Nie podano zgłaszanego adresu URL</div>');
                }
                
                if (elFieldReason.val() === '') {
                    boolIsValid = false;
                    elFieldReason.addClass('errored');
                    elFieldReason.closest('div.r').append('<div class="error-info">Nie podano powodu zgłoszenia</div>');
                } else if (elFieldReason.val().length < 10) {
                    boolIsValid = false;
                    elFieldReason.addClass('errored');
                    elFieldReason.closest('div.r').append('<div class="error-info">Zgłoszenie musi zawierać co najmniej 10 znaków</div>');
                };
                
                if (boolIsValid === true) {
                    JsonRpc2.post({
                        context: $this, 
                        data: {
                            numArtifactId: $('input#artifact-id').val(), 
                            strReporterName: elFieldName.val(), 
                            strReporterEmail: elFieldEmail.val(), 
                            strUrl: elFieldUrl.val(), 
                            strReason: elFieldReason.val()
                        }, 
                        method: 'backend.artifact.ArtifactController.reportAbuse', 
                        callBack: 'reportAbuseCallback'
                    });
                } else {
                    $this.objLoader.remove();
                };
                return false;
            });
            $this.elCurrentReportModal.find('form input[type="submit"]').removeAttr('disabled').attr('value', $this.elCurrentReportModal.find('form input[type="submit"]').data('value'));
        });
    };
    
    this.reportAbuseCallback = function (objResponse) {
        if (!empty(objResponse.result)) {
            var strReporterName = objResponse.result.strReporterName;
            var strReporterEmail = objResponse.result.strReporterEmail;
            
            var strMessage = '<div class="alert alert-success" role="alert">';
            strMessage += '<strong>Dzieki '+strReporterName+'!</strong> Otrzymaliśmy Twoje zgłoszenie nadużycia. Zareagujemy na nie najszybciej jak będzie to możliwe, jeśli będziemy mieli jakieś wątpliwości - skontaktujemy się na podany przez Ciebie adres e-mail '+strReporterEmail;
            strMessage += '</div>';
            $this.elCurrentReportModal.find('.modal-footer').empty().html('<button type="submit" class="btn btn-default" data-dismiss="modal">Super! Teraz zamknij to okno</button>');
            $this.elCurrentReportModal.find('.modal-body').empty().html(strMessage);
        };
        $this.objLoader.remove();
    };
    
    this.initArtifactEmbeed = function () {
        head.load('/imagehost3/js/libs/jquery.clipboard/jquery.clipboard.js', function () {
            $('div.m-artifact div.global-options span.code').click(function () {
                $("#modal-embeed").modal('show');
            });
            
            $("#modal-embeed button.go-copy").each(function () {
                var elCopyTrigger = $(this);
                
                elCopyTrigger.on('click', function (e) {
                    e.preventDefault();
                });

                elCopyTrigger.clipboard({
                    path: '/imagehost3/js/libs/jquery.clipboard/jquery.clipboard.swf', 
                    copy: function () {
                        var strString = elCopyTrigger.closest('li').find('input[type="text"]').val();
                        var elInfo = elCopyTrigger.closest('li').find('span.embeed-info');
                        
                        elInfo.animate({
                            opacity: 1
                        }, 500, function () {
                            elInfo.animate({
                                opacity: 0
                            }, 2000);
                        });
                        return strString;
                    }
                });
            });
            
        });
    };
    
};
