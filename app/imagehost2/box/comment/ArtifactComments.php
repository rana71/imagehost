<?php namespace imagehost2\box\comment;

use webcitron\Subframe\Response;
use webcitron\Subframe\Url;

class ArtifactComments extends \webcitron\Subframe\Box {
    
    public $numArtifactId = 0;
    public $strArtifactSlug = '';
    
    public function __construct ($numArtifactId, $strArtifactSlug) {
        $this->numArtifactId = $numArtifactId;
        $this->strArtifactSlug = $strArtifactSlug;
    }
    
    public function launch () {
        
        $strCurrentUrl = Url::route('Details', array(
            'slug' => $this->strArtifactSlug, 
            'id' => $this->numArtifactId
        ));
        
        return Response::html($this->render(array(
            'strCurrentUrl' => $strCurrentUrl,
        ), 'FacebookComments'));
    }
    
}