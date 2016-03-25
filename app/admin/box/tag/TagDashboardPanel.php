<?php namespace admin\box\tag;

use webcitron\Subframe\Response;
use backend\tag\TagController;
use webcitron\Subframe\JsController;
use backend\tag\model\TagModel;

class TagDashboardPanel extends \webcitron\Subframe\Box {
  
    
    public function launch () {
        JsController::runJs();
        $objModel = new TagModel();
        $arrLastRemoved = $objModel->getLastRemoved(10);
        
        return Response::html($this->render(array(
            'arrLastRemoved' => $arrLastRemoved
        )));
    }
    
}