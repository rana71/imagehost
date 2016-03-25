<?php namespace admin\board;

use \webcitron\Subframe\Board;
use \webcitron\Subframe\Response;

class Login extends Board {
  
    public function login() {
        $objLayout = new \admin\layout\OneColumn();
        $objLayout->addBoxes('main', array(new \admin\box\login\Login()));
        
        return Response::html($objLayout->render());
    }
    
    public function logout () {
        $objSession = \backend\Session::getInstance('imagehost-admin');
        $objSession->destroy();
        \webcitron\Subframe\Redirect::route('Login::login');
    }
    
    public function passwordRecovery() {
        
        $objLayout = new \admin\layout\OneColumn();
        $objLayout->addBoxes('main', array(new \admin\box\login\PasswordRecovery()));
        return Response::html($objLayout->render());
    }
    
    
}