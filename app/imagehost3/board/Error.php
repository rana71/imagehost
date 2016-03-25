<?php namespace imagehost3\board;

use \webcitron\Subframe\Board;
use \webcitron\Subframe\Response;
use backend\artifact\model\ArtifactListOptionsModel;
use webcitron\Subframe\CssController;

class Error extends Board {
    
    public function __construct () {
        $objCssController = CssController::getInstance();
        $objCssController->forceCssFile('Error');
    }
  
    public function notFound() {
        
        $objLayout = new \imagehost3\layout\Standard();
        
        $objListOptions = new ArtifactListOptionsModel(1);
        $objListOptions->setGlobalLimit(10);
        $objListOptions->setLimit(6);
        $objListOptions->disableLoading();
        $objListOptions->setOrder(array(
            ArtifactListOptionsModel::ORDERBY_ON_HOMEPAGE, 
            ArtifactListOptionsModel::ORDERBY_DATE_DESC
        ));
        $objListOptions->addAndWhere(array('is_on_homepage' => 'true', 'is_imported' => 'false'));
        
        
        $objLayout->addBoxes('main', array(
            new \imagehost3\box\error\NotFound(), 
            new \imagehost3\box\artifact\Stream($objListOptions)
        ));
        $objLayout->addBoxes('top', new \imagehost3\box\Topbar());
        $objLayout->addBoxes('foot', new \imagehost3\box\Footer());
        $objLayout->addBoxes('foot', new \imagehost3\box\PuppiesDetector());
        
        $objResponse = Response::html($objLayout->render(array(
            'title' => 'Nie odnaleziono strony - imgED', 
            
        )));
        $objResponse->setStatus(404);
        return $objResponse;
        
    }
    
}