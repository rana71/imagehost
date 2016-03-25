<?php namespace imagehost3\box;

use webcitron\Subframe\Response;

class TopSmall extends \webcitron\Subframe\Box {
    
    
    public function launch () {
       
        return Response::html($this->render());
    }
    
}