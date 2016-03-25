<?php namespace admin\box\admins;

use webcitron\Subframe\Response;
use \webcitron\Subframe\JsController;


class CreateAccount extends \webcitron\Subframe\Box {
  
    
    public function launch () {
        JsController::runJs();
        return Response::html($this->render());
    }
    
}