<?php namespace imagehost3\box\staticContent;

use webcitron\Subframe\Response;

class Agreements extends \webcitron\Subframe\Box {
    
    public function launch () {
        return Response::html($this->render());
    }
    
}