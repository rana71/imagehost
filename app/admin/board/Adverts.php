<?php namespace admin\board;

use \webcitron\Subframe\Board;
use \webcitron\Subframe\Response;
use backend\admin\AdminController;

class Adverts extends Board {
  
    public function index () {
        AdminController::GoAwayIfNotLogged();
        
        
        $objLayout = new \admin\layout\Sidebar();

        $objLayout->addBoxes('right', new \admin\box\adverts\AdvertsFullList());
        
        
        return Response::html($objLayout->render());
    }
    
    
}