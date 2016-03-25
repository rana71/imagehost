<?php namespace admin\box;

use webcitron\Subframe\Response;

class SidebarMenu extends \webcitron\Subframe\Box {
  
    
    public function launch () {
        return Response::html($this->render());
    }
    
}