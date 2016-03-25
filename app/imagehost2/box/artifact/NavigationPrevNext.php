<?php namespace imagehost2\box\artifact;

use webcitron\Subframe\Response;
use backend\artifact\model\ArtifactModel;

class NavigationPrevNext extends \webcitron\Subframe\Box {
    
    private $numArtifactId;
    
    public function __construct ($numArtifactId) {
        $this->numArtifactId = $numArtifactId;
    }
    
    public function launch () {
        $objArtifactModel = new ArtifactModel();
        
        $arrData = array();
        $arrPrev = $objArtifactModel->getOlders($this->numArtifactId, 1);
        if (!empty($arrPrev)) {
            $arrData['strUrlPrev'] = \webcitron\Subframe\Url::route('Details', array('slug' => $arrPrev[0]['slug'], 'id' => $arrPrev[0]['id']));
        }
        
        $arrNext = $objArtifactModel->getNewers($this->numArtifactId, 1);
        if (!empty($arrNext)) {
            $arrData['strUrlNext'] = \webcitron\Subframe\Url::route('Details', array('slug' => $arrNext[0]['slug'], 'id' => $arrNext[0]['id']));
        }
        
        return Response::html($this->render($arrData));
    }
    
}