<?php namespace imagehost3\box\social\facebook;

use webcitron\Subframe\Response;

class Comments extends \webcitron\Subframe\Box {
    
    private $strPageUrl = '';
    
    public function __construct ($strPageUrl) {
        $this->strPageUrl = $strPageUrl;
    }
    
    public function launch () {
        return Response::html($this->render(array(
            'strPageUrl' => $this->strPageUrl
        )));
    }
    
}