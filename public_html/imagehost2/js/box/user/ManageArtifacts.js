/* global Subframe, head, JsonRpc2, objSearch */
Subframe.Box.user_ManageArtifacts = function () {
    "use strict";

    var $this = this;

    this.init = function () {};
    this.elContent = null;

    this.launch = function () {
        $this.elContent = $('.user-artifacts-contents');
        
        $this.initUrlCopying();
        $this.initDeletingArtifact();
    };
    
    this.initDeletingArtifact = function () {
        $this.elContent.find('.remove-artifact').click(function () {
            if (confirm('Na pewno chcesz usunąć tą wrzutkę?')) {
                var numArtifactId = $(this).closest('tr').data('artifact-id');
                
                JsonRpc2.post({
                    context: $this,
                    data: {
                        numArtifactId: numArtifactId
                    }, 
                    method: 'backend.user.UserController.removeLoggedUserArtifact',
                    callBack: 'removeCallback'
                });
            };
            return false;
        });
    };
    
    this.removeCallback = function (objResponse) {
        if (empty(objResponse.error)) {
            var numRemovedArtifactId = objResponse.result.numRemovedArtifactId;
            $('.user-artifacts-contents tr[data-artifact-id="'+numRemovedArtifactId+'"]').addClass('bg-danger').animate({
                opacity: 0
            }, 750, function () {
                $(this).remove();
            });
        };
    };
    
    this.initUrlCopying = function () {
        head.load('/imagehost2/js/libs/jquery.clipboard/jquery.clipboard.js', function () {
            
            $this.elContent.find("button.go-copy").each(function () {
                var elCopyTrigger = $(this);
                
                elCopyTrigger.on('click', function (e) {
                    e.preventDefault();
                });

                elCopyTrigger.clipboard({
                    path: '/imagehost2/js/libs/jquery.clipboard/jquery.clipboard.swf', 
                    copy: function () {
                        var strString = elCopyTrigger.closest('td').find('input[type="text"]').val();
                        var elInfo = elCopyTrigger.closest('td').find('div.copy-info');
                        
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
