<?php namespace imagehost3\box\user;

use webcitron\Subframe\Response;
use webcitron\Subframe\JsController;
use backend\user\model\UserModel;

class Account extends \webcitron\Subframe\Box {
    
    public function __construct () {
        JsController::runJs();
    }
    
    public function launch () {
        $objModel = new UserModel();
        $arrLoggedUser = $objModel->getLoggedUser();
        
        return Response::html($this->render(array(
            'arrLoggedUser' => $arrLoggedUser
        )));
    }
    
}