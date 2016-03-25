<?php namespace imagehost3\box\user;

use webcitron\Subframe\Response;
use webcitron\Subframe\JsController;
use webcitron\Subframe\CssController;

class Register extends \webcitron\Subframe\Box {
  
    public function __construct () {
        CssController::addStylesheets('user');
        JsController::runJs();
    }
    
    public function launch () {
        return Response::html($this->render());
    }
    
}