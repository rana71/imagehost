<?php namespace imagehost2\box\artifact;

use webcitron\Subframe\Response;

class NotFound extends \webcitron\Subframe\Box {
    
    
    public function launch () {
        return Response::html($this->render());
    }
    
}