<?php namespace admin\box\user;

use webcitron\Subframe\Response;

class DisabledUpload extends \webcitron\Subframe\Box {
  
    
    public function launch () {
        \webcitron\Subframe\JsController::runJs();
        return Response::html($this->render());
    }
    
}