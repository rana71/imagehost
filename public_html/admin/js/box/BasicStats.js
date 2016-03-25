Subframe.Box.BasicStats = function () {
    "use strict";
    var $this = this;
    this.elUserBasicStats = null;
    this.elArtifactsBasicStats = null;
    this.elTagsBasicStats  = null;
    this.elNewsletterBasicStats = null;

    this.init = function () {
    };

    this.launch = function () {
        this.initBasicStats();
    };

    this.initBasicStats = function () {
        $this.elUserBasicStats = $('div.stat-user');
        $this.elArtifactsBasicStats = $('div.stat-artifact');
        $this.elTagsBasicStats = $('div.stat-tag');
        $this.elNewsletterBasicStats = $('div.stat-newsletter');

        $this.elUserBasicStats.click($this.refreshUserAdminStats);
        $this.elArtifactsBasicStats.click($this.refreshArtifactAdminStats);
        $this.elTagsBasicStats.click($this.refreshTagAdminStats);
        $this.elNewsletterBasicStats.click($this.refreshNewsletterAdminStats);
    };

    this.refreshUserAdminStats = function () {
        toastr.info('Liczenie zostało rozpoczęte, poczekaj chwilę');
        JsonRpc2.post({
            context: $this,
            data: {},
            method: 'backend.adminstats.AdminStatsController.refreshUserStats',
            callBack: 'showUserAdminStats'
        });
    };

    this.showUserAdminStats = function (objResponse) {
        if (objResponse.status === 1) {
            $this.elUserBasicStats.find('span.num.active_users').text(objResponse.result.active_users.numValue);
            $this.elUserBasicStats.find('span.num.inactive_users').text(objResponse.result.inactive_users.numValue);
            $this.elUserBasicStats.find('strong.update_date').text(objResponse.result.active_users.strLastRefreshDate);
            toastr.success('Statystyki użytkowników zostały odświeżone');
        }
        else {
            toastr.error(objResponse.error.join('<br/>'));
        }
    };

    this.refreshArtifactAdminStats = function () {
        toastr.info('Liczenie zostało rozpoczęte, poczekaj chwilę');
        JsonRpc2.post({
            context: $this,
            data: {},
            method: 'backend.adminstats.AdminStatsController.refreshArtifactStats',
            callBack: 'showArtifactAdminStats'
        });
    };

    this.showArtifactAdminStats = function (objResponse) {
        if (objResponse.status === 1) {
            $this.elArtifactsBasicStats.find('span.num.visible_artifacts').text(objResponse.result.visible_artifacts.numValue);
            $this.elArtifactsBasicStats.find('span.num.invisible_artifacts').text(objResponse.result.invisible_artifacts.numValue);
            $this.elArtifactsBasicStats.find('strong.update_date').text(objResponse.result.visible_artifacts.strLastRefreshDate);
            toastr.success('Statystyki artefaktów zostały odświeżone');
        }
        else {
            toastr.error(objResponse.error.join('<br/>'));
        }
    };

    this.refreshTagAdminStats = function () {
        toastr.info('Liczenie zostało rozpoczęte, poczekaj chwilę');
        JsonRpc2.post({
            context: $this,
            data: {},
            method: 'backend.adminstats.AdminStatsController.refreshTagStats',
            callBack: 'showTagAdminStats'
        });
    };

    this.showTagAdminStats = function (objResponse) {
        if (objResponse.status === 1) {
            $this.elTagsBasicStats.find('span.num.active_tags').text(objResponse.result.active_tags.numValue);
            $this.elTagsBasicStats.find('span.num.inactive_tags').text(objResponse.result.inactive_tags.numValue);
            $this.elTagsBasicStats.find('strong.update_date').text(objResponse.result.active_tags.strLastRefreshDate);
            toastr.success('Statystyki tagów zostały odświeżone');
        }
        else {
            toastr.error(objResponse.error.join('<br/>'));
        }
    };
    
    this.refreshNewsletterAdminStats = function () {
        toastr.info('Liczenie zostało rozpoczęte, poczekaj chwilę');
        JsonRpc2.post({
            context: $this,
            data: {},
            method: 'backend.adminstats.AdminStatsController.refreshNewsletterStats',
            callBack: 'showNewsletterAdminStats'
        });
    };

    this.showNewsletterAdminStats = function (objResponse) {
        if (objResponse.status === 1) {
            $this.elNewsletterBasicStats.find('span.num.active_newsletter_emails').text(objResponse.result.active_newsletter_emails.numValue);
            $this.elNewsletterBasicStats.find('span.num.inactive_newsletter_emails').text(objResponse.result.inactive_newsletter_emails.numValue);
            $this.elNewsletterBasicStats.find('strong.update_date').text(objResponse.result.active_newsletter_emails.strLastRefreshDate);
            toastr.success('Statystyki newslettera zostały odświeżone');
        }
        else {
            toastr.error(objResponse.error.join('<br/>'));
        }
    };

};

