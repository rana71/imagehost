<?php namespace admin\box;

use webcitron\Subframe\Response;

class TopMenu extends \webcitron\Subframe\Box {
  
    
    public function launch () {
        return Response::html($this->render());
    }
    
}