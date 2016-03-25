<?php namespace imagehost3\board;

use \webcitron\Subframe\Board;
use \webcitron\Subframe\Response;
use \webcitron\Subframe\CssController;
use webcitron\Subframe\Request;

class TestPage extends Board {
  
    public function __construct () {
        $objCssController = CssController::getInstance();
        $objCssController->forceCssFile('StaticContent');
    }
    
    public function index() {
        $objLayout = new \imagehost3\layout\Blank();
        
        $objLayout->addBoxes('main', array(
            new \imagehost3\box\test\Database(), 
            new \imagehost3\box\test\Storage(), 
            new \imagehost3\box\test\Mailer(), 
            new \imagehost3\box\test\NewsletterWrapper(), 
            new \imagehost3\box\test\Cache(), 
            new \imagehost3\box\test\DirectoriesPermissions()
        ));
        
         $objResponse = Response::html($objLayout->render(array(
            'title' => 'Strona testowa', 
            'robots' => 'noindex, nofollow, noarchive', 
            'googlebot' => 'noindex, nofollow, noarchive, nosnippet'
        )));
         
        return $objResponse;
    }
    
    
}