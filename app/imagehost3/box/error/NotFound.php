<?php namespace imagehost3\box\error;

use webcitron\Subframe\Response;

class NotFound extends \webcitron\Subframe\Box {
    
    
    public function launch () {
        return Response::html($this->render());
    }
    
}