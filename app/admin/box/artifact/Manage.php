<?php namespace admin\box\artifact;

use webcitron\Subframe\Response;
use webcitron\Subframe\JsController;

class Manage extends \webcitron\Subframe\Box {
  
    
    public function launch () {
        JsController::runJs();
        return Response::html($this->render());
    }
    
}