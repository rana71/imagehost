<?php namespace admin\box\login;

use webcitron\Subframe\Response;

class Login extends \webcitron\Subframe\Box {
    
    
    public function launch () {
        return Response::html($this->render(array()));
    }
    
}