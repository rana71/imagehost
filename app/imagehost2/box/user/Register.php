<?php namespace imagehost2\box\user;

use webcitron\Subframe\Response;
use webcitron\Subframe;

class Register extends \webcitron\Subframe\Box {
  
    
    public function launch () {
        Subframe\JsController::runJs();
        return Response::html($this->render());
    }
    
}