<?php namespace imagehost2\box\user;

use webcitron\Subframe\Response;
use backend\user\model\UserModel;
use webcitron\Subframe\JsController;

class Logout extends \webcitron\Subframe\Box {
    
    public function launch () {
        JsController::runJs();
        $objModel = new UserModel();
        $objModel->logout();
        return Response::html($this->render());
    }
    
}