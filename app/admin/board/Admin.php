<?php namespace admin\board;

use \webcitron\Subframe\Board;
use \webcitron\Subframe\Response;
use backend\admin\AdminController;

class Admin extends Board {
  
    public function overview () {
        AdminController::GoAwayIfNotLogged();
        
        
        $objLayout = new \admin\layout\Sidebar();

        $objLayout->addBoxes('right', array(
            $arrBoxes[] = new \admin\box\admins\FullList()
        ));
        
        
        return Response::html($objLayout->render());
    }
    
    public function add () {
        AdminController::GoAwayIfNotLogged();
        
        
        $objLayout = new \admin\layout\Sidebar();
        
        $objLayout->addBoxes('right', array(
            $arrBoxes[] = new \admin\box\TopMenu(), 
            $arrBoxes[] = new \admin\box\admins\CreateAccount()
        ));
        
        
        return Response::html($objLayout->render());
    }
    
    public function edit ($numAdminId) {
        AdminController::GoAwayIfNotLogged();
        
        
        $objLayout = new \admin\layout\Sidebar();
        

        $objLayout->addBoxes('right', array(
            $arrBoxes[] = new \admin\box\admins\Edit($numAdminId)
        ));
        
        
        return Response::html($objLayout->render());
    }
    
}