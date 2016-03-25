<?php namespace imagehost3\board;

use \webcitron\Subframe\Board;
use \webcitron\Subframe\Response;
use webcitron\Subframe\CssController;

class QueriesList extends Board {
    
    public function __construct () {
        $objCssController = CssController::getInstance();
        $objCssController->forceCssFile('LabelsList');
    }
    
    public function index() {
        $objLayout = new \imagehost3\layout\Standard();
        
        $objLayout->addBoxes('main', array(
            new \imagehost3\box\searcher\QueriesList()
        ));
        $objLayout->addBoxes('top', new \imagehost3\box\Topbar());
        $objLayout->addBoxes('foot', new \imagehost3\box\Footer());
        $objLayout->addBoxes('foot', new \imagehost3\box\PuppiesDetector());
        
        return Response::html($objLayout->render(array(
            'title' => 'Najnowsze wyszukiwania', 
            'robots' => 'noindex, follow', 
            'googlebot' => 'noindex, follow'
        )));
    }
    
    
}