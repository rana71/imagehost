<?php namespace imagehost2\box\artifact;

use webcitron\Subframe\Response;
use backend\artifact\model\ArtifactModel;

class ArtifactOptions extends \webcitron\Subframe\Box {
    
    private $numArtifactId;
    
    public function __construct ($numArtifactId) {
        $this->numArtifactId = $numArtifactId;
    }
    
    public function launch () {
        \webcitron\Subframe\JsController::runJs();
        
        $objModel = new ArtifactModel();
        $arrArtifact = $objModel->getBaseInfo($this->numArtifactId);
        
        return Response::html($this->render(array(
            'arrArtifact' => $arrArtifact
        )));
    }
    
}