<?php namespace imagehost2\box\user;

use webcitron\Subframe\Response;
use webcitron\Subframe\JsController;

class ForgotPassword extends \webcitron\Subframe\Box {
  
    
    public function launch () {
        JsController::runJs();
        return Response::html($this->render());
    }
    
}