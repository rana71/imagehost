<?php namespace imagehost3\box\staticContent;

use webcitron\Subframe\Response;

class Career extends \webcitron\Subframe\Box {
    
    public function launch () {
        return Response::html($this->render());
    }
    
}