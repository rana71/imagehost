<?php namespace admin\box\user;

use webcitron\Subframe\Response;
use backend\user\UserController;

class FullList extends \webcitron\Subframe\Box {
  
    
    public function launch () {
        \webcitron\Subframe\JsController::runJs();
//        $arrUsers = UserController::getListWithDetails();
        return Response::html($this->render(array(
//            'arrUsers' => $arrUsers['result']
        )));
    }
    
}