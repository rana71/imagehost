<?php namespace imagehost2\box\artifact;

use webcitron\Subframe\Response;
use backend\artifact\model\ArtifactModel;

class Stats extends \webcitron\Subframe\Box {
    
    private $numArtifactId;
    
    public function __construct ($numArtifactId) {
        $this->numArtifactId = $numArtifactId;
    }
    
    public function launch () {
        
        $objModel = new ArtifactModel();
        $arrStats = $objModel->getArtifactStats($this->numArtifactId);
        
        return Response::html($this->render(array(
            'arrStats' => $arrStats
        )));
    }
    
}