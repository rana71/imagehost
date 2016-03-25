<?php namespace imagehost3\box\artifact;

use webcitron\Subframe\Response;
use webcitron\Subframe\CssController;
use webcitron\Subframe\JsController;

class ElementYtVideo extends \webcitron\Subframe\Box {
    
    private $arrElement;
    private $arrArtifact;
    
    public function __construct ($arrElement, $arrArtifact) {
        $this->arrElement = $arrElement;
        $this->arrArtifact = $arrArtifact;
        CssController::addStylesheets('artifact');
    }
    
    public function launch () {
        JsController::runJs();
        return Response::html($this->render(array(
            'arrElement' => $this->arrElement, 
            'arrArtifact' => $this->arrArtifact
        )));
        
    }
    
}