<?php namespace imagehost3\board;

use \webcitron\Subframe\Board;
use \webcitron\Subframe\Response;
Use webcitron\Subframe\Redirect;
use webcitron\Subframe\CssController;

class TagsList extends Board {
    
    
    public function __construct () {
        $objCssController = CssController::getInstance();
        $objCssController->forceCssFile('LabelsList');
    }
  
    public function index() {
        $objLayout = new \imagehost3\layout\Standard();
        
        $objLayout->addBoxes('main', array(
            new \imagehost3\box\tags\FullList()
        ));
        $objLayout->addBoxes('top', new \imagehost3\box\Topbar());
        $objLayout->addBoxes('foot', new \imagehost3\box\Footer());
        $objLayout->addBoxes('foot', new \imagehost3\box\PuppiesDetector());
        
        return Response::html($objLayout->render(array(
            'title' => 'Najpopularniejsze tematy', 
            'robots' => 'index, follow, archive', 
            'googlebot' => 'index, follow, archive, snippet'
        )));
    }
    
    public function r301 () {
        Redirect::route('TagsList::index');
    }
    
}