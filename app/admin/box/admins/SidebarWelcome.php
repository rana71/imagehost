<?php namespace admin\box\admins;

use webcitron\Subframe\Response;
use backend\admin\AdminController;

class SidebarWelcome extends \webcitron\Subframe\Box {
  
    
    public function launch () {
        $objSession = \backend\Session::getInstance('imagehost-admin');
        $numAdminId = $objSession->getValue('admin_iamgehost_auth');
        $arrAdmin = AdminController::getById($numAdminId);
        
        return Response::html($this->render(array(
            'arrAdmin' => $arrAdmin['result']
        )));
    }
    
}