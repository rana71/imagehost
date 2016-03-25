<?php namespace imagehost3\box;

use webcitron\Subframe\Response;
use webcitron\Subframe\Request;
use webcitron\Subframe\Redirect;
use webcitron\Subframe\JsController;
use backend\String;
use backend\searcher\model\QueryModel;
use backend\user\model\UserModel;
use backend\artifact\model\ArtifactModel;

class Topbar extends \webcitron\Subframe\Box {
    
    public function __construct () {
        JsController::runJs();
    }
    
    public function launch () {
        $objModel = new UserModel();
        $arrLoggedUser = $objModel->getLoggedUser();
        $strSearchQuery = Request::arg('q');
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
        
//        $objModel = new ArtifactModel();
//        $arrRandomArtifacts = $objModel->getRandom();
        
        return Response::html($this->render(array(
//            'arrRandomArtifact' => $arrRandomArtifacts[0], 
            'arrLoggedUser' => $arrLoggedUser
        )));
    }
    
}