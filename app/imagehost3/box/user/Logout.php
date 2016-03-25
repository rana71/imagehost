<?php namespace imagehost3\box\user;

use webcitron\Subframe\Response;
use backend\user\model\UserModel;
use webcitron\Subframe\JsController;

class Logout extends \webcitron\Subframe\Box {
    
    public function __construct () {
        JsController::runJs();
    }
    
    public function launch () {
        $objModel = new UserModel();
        $objModel->logout();
        return Response::html($this->render());
    }
    
}