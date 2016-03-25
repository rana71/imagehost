/* global Subframe, head, JsonRpc2, objSearch */
Subframe.Box.artifact_Details = function () {
    "use strict";

    var $this = this;

    this.init = function () {
        $this.objLoader = new Subframe.Lib.Loader();
    };
    this.elCurrentReportModal = null;
    this.strVotingCookieName = '';

    this.launch = function () {
        $this.strVotingCookieName = 'imagehost-vote-'+$('input#artifact-id').val()+'-'+$('input#vote-environment').val();
        $this.initVoting();
        $this.initArtifactReporting();
        $this.initEmbeeds();
    };
    
    this.initVoting = function () {
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
        
        $('a.report-item').click(function () {
            $this.elCurrentReportModal = $("#modal-report").clone();
            $this.elCurrentReportModal.on('hidden.bs.modal', function (e) {
                $this.elCurrentReportModal.remove();
            });
            $this.elCurrentReportModal.modal('show');
            $this.elCurrentReportModal.find('form').submit(function (e) {
                e.preventDefault();
                var numIterator = 0;
                var arrErrors = [];
                var elFieldName = $this.elCurrentReportModal.find('form input#input-name');
                var elFieldEmail = $this.elCurrentReportModal.find('form input#input-email');
                var elFieldUrl = $this.elCurrentReportModal.find('form input#input-url');
                var elFieldReason = $this.elCurrentReportModal.find('form textarea#input-reason');

                var strName = elFieldName.val();
                var strEmail = elFieldEmail.val();
                var strUrl = elFieldUrl.val();
                var strReason = elFieldReason.val();
                var numArtifactId = $('input#artifact-id').val();
                var arrErrorMessages = [];

                arrErrors = [];

                $(this).find('.has-error').removeClass('has-error');
                $(this).find('.alert').slideUp('fast', function () {
                    $(this).remove();
                });

                if (empty(strName)) {
                    arrErrors.push({elField: elFieldName, strError: 'Nie podano imienia'});
                };

                if (empty(strEmail)) {
                    arrErrors.push({elField: elFieldEmail, strError: 'Nie podano adresu e-mail'});
                };

                if (empty(strUrl)) {
                    arrErrors.push({elField: elFieldUrl, strError: 'Nie podano adresu URL obrazka'});
                };

                if (empty(strReason)) {
                    arrErrors.push({elField: elFieldReason, strError: 'Nie podano powodu zgłoszenia'});
                };

                if (!empty(arrErrors)) {
                    for (numIterator in arrErrors) {
                        if (!$(arrErrors[numIterator].elField).closest('.form-group').hasClass('has-error')) {
                            $(arrErrors[numIterator].elField).closest('.form-group').addClass('has-error');
                        };
                        arrErrorMessages.push(arrErrors[numIterator].strError);
                    };
                    $("#modal-report").find('form').prepend(
                        $('<div class="alert alert-danger">'+arrErrorMessages.join('<br />')+'</div>').css('display', 'none').slideDown('fast')
                    );
                    return false;
                } else {
                    $this.objLoader.add();
                    JsonRpc2.post({
                        context: $this, 
                        data: {
                            numArtifactId: numArtifactId, 
                            strReporterName: strName, 
                            strReporterEmail: strEmail, 
                            strUrl: strUrl, 
                            strReason: strReason
                        }, 
                        method: 'backend.artifact.ArtifactController.reportAbuse', 
                        callBack: 'reportAbuseCallback'
                    });
                };

            });
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
    
    this.initEmbeeds = function () {
        head.load('/imagehost2/js/libs/jquery.clipboard/jquery.clipboard.js', function () {
            $('a.embeed-item').click(function () {
                $("#modal-embeed").modal('show');
            });
            
            $("#modal-embeed button.go-copy").each(function () {
                var elCopyTrigger = $(this);
                
                elCopyTrigger.on('click', function (e) {
                    e.preventDefault();
                });

                elCopyTrigger.clipboard({
                    path: '/imagehost2/js/libs/jquery.clipboard/jquery.clipboard.swf', 
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
