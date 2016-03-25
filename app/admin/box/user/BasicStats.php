<?php namespace admin\box\user;

use webcitron\Subframe\Response;
use webcitron\Subframe\JsController;
use backend\user\UserController;

class BasicStats extends \webcitron\Subframe\Box {
  
    
    public function launch () {
//        JsController::runJs();
        $arrStats = UserController::getBasicStats();
        return Response::html($this->render(array(
            'arrStats' => $arrStats['result']
        )));
    }
    
}