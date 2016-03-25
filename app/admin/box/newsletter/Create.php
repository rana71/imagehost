<?php namespace admin\box\newsletter;

use webcitron\Subframe\Response;
use webcitron\Subframe\JsController;

class Create extends \webcitron\Subframe\Box {
  
    
    public function launch () {
        JsController::runJs();
        return Response::html($this->render());
    }
    
}