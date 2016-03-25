<?php namespace admin\board;

use \webcitron\Subframe\Board;
use backend\admin\AdminController;
use \webcitron\Subframe\Response;

class Dashboard extends Board {
  
    public function index() {
        AdminController::GoAwayIfNotLogged();

        $objLayout = new \admin\layout\Sidebar();
        $objLayout->addBoxes('right', new \admin\box\Welcome());
        

        $objLayout->addBoxes('right', new \admin\box\BasicStats());
        $objLayout->addBoxes('right', new \admin\box\artifact\FastRemove());
        $objLayout->addBoxes('right', new \admin\box\artifact\RemoveImportedAndBan());
        $objLayout->addBoxes('right', new \admin\box\tag\TagDashboardPanel());
        $objLayout->addBoxes('right', new \admin\box\newsletter\Create());
        
        return Response::html($objLayout->render());
    }
    
    
}