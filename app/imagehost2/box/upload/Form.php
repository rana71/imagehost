<?php namespace imagehost2\box\upload;

use webcitron\Subframe\Response;
use webcitron\Subframe\JsController;

class Form extends \webcitron\Subframe\Box {
    
    private $strDefaultItemType = '';
    
    public function __construct ($strDefaultItemType = '') {
        $this->strDefaultItemType = $strDefaultItemType;
    }
    
    public function launch () {
        JsController::runJs();
        
        return Response::html($this->render(array(
            'strDefaultItemType' => $this->strDefaultItemType
        )));
    }
    
}