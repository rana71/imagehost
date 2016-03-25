<?php namespace admin\box\admins;

use webcitron\Subframe\Response;
use backend\admin\AdminController;
use webcitron\Subframe\JsController;

class Edit extends \webcitron\Subframe\Box {
  
    public $numAdminId = 0;
    
    public function __construct ($numAdminId) {
        $this->numAdminId = $numAdminId;
    }
    
    public function launch () {
        JsController::runJs();
        $arrAdmin = AdminController::getById($this->numAdminId);
        
        return Response::html($this->render(array(
            'arrAdmin' => $arrAdmin['result']
        )));
    }
    
}