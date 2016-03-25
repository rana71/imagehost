<?php namespace imagehost2\box\user;

use webcitron\Subframe\Response;
use backend\user\UserController;
use webcitron\Subframe\JsController;
use backend\user\model\UserModel;

class Account extends \webcitron\Subframe\Box {
    
    public function launch () {
        JsController::runJs();
        $objModel = new UserModel();
        $arrLoggedUser = $objModel->getLoggedUser();
        
        return Response::html($this->render(array(
            'arrLoggedUser' => $arrLoggedUser
        )));
    }
    
}