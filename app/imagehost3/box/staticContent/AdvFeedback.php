<?php namespace imagehost3\box\staticContent;

use webcitron\Subframe\Response;
use \webcitron\Subframe\JsController;

class AdvFeedback extends \webcitron\Subframe\Box {
    
    public function __construct () {
        JsController::runJs();
    }
    
    public function launch () {
        return Response::html($this->render());
    }
    
}