<?php namespace imagehost3\box\puppy;

use webcitron\Subframe\Response;
use webcitron\Subframe\CssController;

class Video extends \webcitron\Subframe\Box {
    
    public function __construct () {
        CssController::addStylesheets('puppy');
    }
    
    public function launch () {
        return Response::html($this->render());
    }
    
}