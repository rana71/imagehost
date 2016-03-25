<?php namespace imagehost2\box\staticContent;

use webcitron\Subframe\Response;

class AccountGuestCompare extends \webcitron\Subframe\Box {
    
    
    public function launch () {
        return Response::html($this->render());
    }
    
}