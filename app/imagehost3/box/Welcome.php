<?php namespace imagehost3\box;

use webcitron\Subframe\Response;

class Welcome extends \webcitron\Subframe\Box {
    
    
    public function launch () {
        return Response::html($this->render());
    }
    
}