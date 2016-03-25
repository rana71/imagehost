<?php namespace admin\board;

use \webcitron\Subframe\Board;
use \webcitron\Subframe\Response;
use backend\admin\AdminController;

class Artifact extends Board {
  
    public function onHomepage () {
        AdminController::GoAwayIfNotLogged();
        
        $objLayout = new \admin\layout\Sidebar();
        $objLayout->addBoxes('right', new \admin\box\artifact\OnHomepage());
        
        return Response::html($objLayout->render());
    }
    
    public function manage () {
        AdminController::GoAwayIfNotLogged();
        
        $objLayout = new \admin\layout\Sidebar();
        $objLayout->addBoxes('right', new \admin\box\artifact\Manage());
        
        return Response::html($objLayout->render());
        
//        
//        AdminController::GoAwayIfNotLogged();
//        $objLayout = new \admin\layout\Sidebar();
//        $objLayout->addBoxes('right', array(
//            $arrBoxes[] = new \admin\box\artifact\Manage()
//        ));
//        return Response::html($objLayout->render());
    }
    
}