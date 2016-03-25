<?php namespace imagehost2\box\tags;

use webcitron\Subframe\Response;
use backend\tag\model\TagModel;

class ArtifactTags extends \webcitron\Subframe\Box {
    
    private $numAcritfactId = 0;
    
    public function __construct ($numArtifactId) {
        $this->numArtifactId = $numArtifactId;
    }
    
    public function launch () {
        $objModel = new TagModel();
        $arrTags = $objModel->getArtifactTags($this->numArtifactId);
        return Response::html($this->render(array(
            'arrTags' => $arrTags
        )));
    }
    
}