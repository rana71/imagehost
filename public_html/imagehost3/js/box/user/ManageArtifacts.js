/* global Subframe, head, JsonRpc2, objSearch */
Subframe.Box.user_ManageArtifacts = function () {
    "use strict";

    var $this = this;

    this.elContent = null;
    this.objCurrentModalElement = {};
    this.objLoader = {};
    this.objGoogle = {};
    
    this.init = function () {
        $this.objLoader = new Subframe.Lib.Loader();
    };

    this.launch = function () {
        $this.elContent = $('div.m-user div.user-artifacts');
        
        $this.initCommercialGallery();
        $this.initDeletingArtifact();
        $this.initStatsModals();
    };
    
    this.initCommercialGallery = function () {
        $this.elContent.find('button.allegro-gallery').click(function () {
            $this.objLoader.add();
            var numArtifactId = $(this).closest('tr').data('artifact-id');
            JsonRpc2.post({
                context: $this,
                data: {
                    numArtifactId: numArtifactId
                }, 
                method: 'backend.artifact.ArtifactController.getCommercialGalleryCode',
                callBack: 'getCommercialGalleryCodeCallback'
            });
        });
    };
    
    this.getCommercialGalleryCodeCallback = function (objResponse) {
        $this.objLoader.remove();
        var elModal = $('div.m-user div#commercial-gallery-modal');
        $this.showModal(elModal, function () {
            $this.objCurrentModalElement.find('textarea').val(objResponse.result.strCode).focus(function () {
                $(this).select();
            });
        });
    };
    
    this.initStatsModals = function () {
        head.load('//www.gstatic.com/charts/loader.js', function () {
            $this.objGoogle = google;
            $this.objGoogle.charts.load('current', {'packages':['corechart'], 'language': 'pl'});
            
            $this.elContent.find('.show-stats').click(function () {
                if ($(this).hasClass('active')) {
                    $(this).removeClass('active');
                    $(this).closest('tr').next('tr').find('.stats-container').empty();
                } else {
                    $(this).addClass('active');
                    
                    $this.objLoader.add();
                    var objJsonData = {};
                    var strMethodToCall = '';
                    switch ($(this).data('type')) {
                        case 'general':
                            strMethodToCall = 'backend.stats.StatsController.getStatsUserGeneral';
                            break;
                        case 'item':
                            strMethodToCall = 'backend.stats.StatsController.getStatsUserArtifact';
                            objJsonData.numArtifactId = $(this).data('item-id');
                            break;
                    };

                    JsonRpc2.post({
                        context: $this,
                        data: objJsonData, 
                        method: strMethodToCall,
                        callBack: 'getStatsCallback'
                    });
                };

            });
        });
    };
    
    
    this.getStatsCallback = function (objResponse) {
        var arrChartData = objResponse.result.stats;
        
        $this.objGoogle.charts.setOnLoadCallback(function () {
            var numIterator = 0;
            var arrPreparedRows = [];
            var arrDate = [];
            var objDate = {};
            var objData = new google.visualization.DataTable();
            
            objData.addColumn('date', 'Dzień');
            objData.addColumn('number', 'Odsłony');
            for (numIterator in arrChartData) {
//                arrDate = arrChartData[numIterator][0].split('-');
                objDate = new Date(arrChartData[numIterator][0]);
                arrPreparedRows.push([objDate, arrChartData[numIterator][1]]);
                
            };
            objData.addRows(arrPreparedRows);

            var chart = new $this.objGoogle.visualization.AreaChart(document.getElementById('stats-chart-'+objResponse.result.statsType));

            chart.draw(objData, {
                height: 300,
                curveType: 'function',
                legend: { position: 'bottom' }, 
                vAxis: {
                    viewWindowMode: 'explicit',
                    minValue: 0
                }, 
                hAxis: {
                    format: 'd-MM-yyyy'
                }
            });
            $this.objLoader.remove();
            
        });

    };
    
    this.showModal = function (elModalElement, fnCallback) {
        if (!empty($this.objCurrentModalElement)) {
            $this.objCurrentModalElement.modal('hide');
        };
        $this.objCurrentModalElement = elModalElement;
        $this.objCurrentModalElement.on('show.bs.modal', function () {
            $(this).find('.modal-body').css({
              width:'auto'
            });
            if (typeof fnCallback ===  'function') {
                fnCallback();
            };
        }).modal('show').on('hidden.bs.modal', function (e) {
            $this.objCurrentModalElement = {};
        });
    };
    
    this.initDeletingArtifact = function () {
        $this.elContent.find('button.remove').click(function () {
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
            $this.elContent.find('tr[data-artifact-id="'+numRemovedArtifactId+'"]').addClass('bg-danger').animate({
                opacity: 0
            }, 750, function () {
                $(this).remove();
            });
        };
    };
    
};
