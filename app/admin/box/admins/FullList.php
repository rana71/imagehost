<?php namespace admin\box\admins;

use webcitron\Subframe\Response;
use webcitron\Subframe\JsController;
use backend\admin\AdminController;

class FullList extends \webcitron\Subframe\Box {
  
    
    public function launch () {
     JsController::runJs();
        $arrAdmins = AdminController::all();
        return Response::html($this->render(array(
            'arrAdmins' => $arrAdmins['result']
        )));
    }
    
}