<?php namespace imagehost3\box\user;

use webcitron\Subframe\Response;
use webcitron\Subframe\JsController;

class ForgotPassword extends \webcitron\Subframe\Box {
  
    public function __construct () {
        JsController::runJs();
    }
    
    public function launch () {
        return Response::html($this->render());
    }
    
}