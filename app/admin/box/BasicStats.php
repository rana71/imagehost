<?php namespace admin\box;

use webcitron\Subframe\Response;
use webcitron\Subframe\JsController;
use backend\adminstats\model\AdminStatsModel;


class BasicStats extends \webcitron\Subframe\Box {

    public function launch () {
        JsController::runJs();
        $objStatsModel = new AdminStatsModel();
        $arrStats = $objStatsModel->getAllStats();
        
        return Response::html($this->render(array(
            'arrStats' => $arrStats
        )));
    }
    
}