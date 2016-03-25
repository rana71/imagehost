<?php namespace imagehost2\box\social;

use webcitron\Subframe\Response;

class ShareAll extends \webcitron\Subframe\Box {
    
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