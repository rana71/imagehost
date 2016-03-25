<?php namespace imagehost2\box;

use webcitron\Subframe\Response;
use webcitron\Subframe\Request;
use webcitron\Subframe\Redirect;
use webcitron\Subframe\JsController;
use backend\String;
use backend\searcher\model\QueryModel;
use backend\user\model\UserModel;

class Topbar extends \webcitron\Subframe\Box {
    
    public function launch () {
       
        JsController::runJs();
        $objModel = new UserModel();
        $arrLoggedUser = $objModel->getLoggedUser();
        $strSearchQuery = Request::arg('searchQuery');
        $strSearchQueryLowered = mb_strtolower($strSearchQuery, 'UTF-8');
        if (!empty($strSearchQueryLowered)) {
            $strQuerySlug = String::slug($strSearchQueryLowered);
            $objModel = new QueryModel();
            $arrQuery = $objModel->getBySlug($strQuerySlug);
            if (empty($arrQuery)) {
                $objModel->add($strSearchQueryLowered, $strQuerySlug);
            }
            Redirect::route('Listing::query', array($strQuerySlug));
        }
        
        return Response::html($this->render(array(
            'arrLoggedUser' => $arrLoggedUser
        )));
    }
    
}