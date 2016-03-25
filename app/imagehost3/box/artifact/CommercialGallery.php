<?php namespace imagehost3\box\artifact;

use webcitron\Subframe\Response;
use \webcitron\Subframe\Url;
use \webcitron\Subframe\JsController;
//use backend\artifact\model\ArtifactModel;
//use webcitron\Subframe\Application;
//use webcitron\Subframe\CssController;
//use backend\puppy\model\PuppyModel;

class CommercialGallery extends \webcitron\Subframe\Box {
    
    private $arrArtifact;
    private $arrElements;
    
    public function __construct ($arrArtifact, $arrElements) {
        $this->arrArtifact = $arrArtifact;
        $this->arrElements = $arrElements;
    }
    
    public function launch () {
        JsController::runJs();
        $strThisGalleryUrl = Url::route('Details::commerceGallery', array('slug' => $this->arrArtifact['slug'], 'id' => $this->arrArtifact['id']));

        return Response::html($this->render(array(
            'strThisGalleryUrl' => $strThisGalleryUrl, 
            'arrArtifact' => $this->arrArtifact, 
            'arrElements' => $this->arrElements
        )));
        
    }
    
}