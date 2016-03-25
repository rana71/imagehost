<?php namespace imagehost2\box\staticContent;

use webcitron\Subframe\Response;
use backend\user\UserController;

class AdvFeedback extends \webcitron\Subframe\Box {
    
    
    public function launch () {
        \webcitron\Subframe\JsController::runJs();
        return Response::html($this->render());
    }
    
}