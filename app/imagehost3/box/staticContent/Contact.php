<?php namespace imagehost3\box\staticContent;

use webcitron\Subframe\Response;
use webcitron\Subframe\JsController;

class Contact extends \webcitron\Subframe\Box {
    
    private $strSubject = '';
    
    public function __construct ($strSubject = '') {
        $this->strSubject = $strSubject;
        
        JsController::runJs();
    }
    
    public function launch () {
        return Response::html($this->render(array(
            'strSubject' => $this->strSubject
        )));
    }
    
}