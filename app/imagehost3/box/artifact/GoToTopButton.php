<?php namespace imagehost3\box\artifact;

use webcitron\Subframe\Response;

class GoToTopButton extends \webcitron\Subframe\Box {
    
    public function launch () {
        return Response::html($this->render());
    }
    
}