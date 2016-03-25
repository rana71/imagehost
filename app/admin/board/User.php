<?php namespace admin\board;

use \webcitron\Subframe\Board;
use \webcitron\Subframe\Response;
use backend\admin\AdminController;

class User extends Board {
  
    public function overview () {
        AdminController::GoAwayIfNotLogged();
        
        $objLayout = new \admin\layout\Sidebar();

        $objLayout->addBoxes('right', array(
            $arrBoxes[] = new \admin\box\TopMenu(), 
            $arrBoxes[] = new \admin\box\user\FullList()
        ));
        
        return Response::html($objLayout->render());
    }
    
    public function disallowUpload () {
        AdminController::GoAwayIfNotLogged();
        
        $objLayout = new \admin\layout\Sidebar();

        $objLayout->addBoxes('right', array(
            $arrBoxes[] = new \admin\box\TopMenu(), 
            $arrBoxes[] = new \admin\box\user\DisabledUpload()
        ));
        
        return Response::html($objLayout->render());
    }
    
}