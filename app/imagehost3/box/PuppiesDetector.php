<?php namespace imagehost3\box;

use webcitron\Subframe\Response;
use webcitron\Subframe\JsController;

class PuppiesDetector extends \webcitron\Subframe\Box {
    
    
    public function __construct () {
        JsController::runJs();
    }
    
    public function launch () {    
        return Response::html($this->render());
    }
    
}