<?php namespace imagehost2\box;

use webcitron\Subframe\Response;

class PageHeader extends \webcitron\Subframe\Box {
    
    private $strHeading = '';
    private $strText = '';
    
    public function __construct ($strHeading, $boolIncludeFacebook = false) {
        $this->strHeading = $strHeading;
        $this->boolIncludeFacebook = $boolIncludeFacebook;
    }
    
    public function launch () {
      
        return Response::html($this->render(array(
            'strHeading' => $this->strHeading, 
            'boolIncludeFacebook' => $this->boolIncludeFacebook
        )));
    }
    
}