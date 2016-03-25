<?php namespace imagehost3\box\staticContent;

use webcitron\Subframe\Response;

class About extends \webcitron\Subframe\Box {
    
    
    public function launch () {
        return Response::html($this->render());
    }
    
}