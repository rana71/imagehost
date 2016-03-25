<?php namespace imagehost2\box\artifact;

use webcitron\Subframe\Response;
use backend\artifact\model\ArtifactModel;

class Info extends \webcitron\Subframe\Box {
    
    private $numArtifactId;
    
    public function __construct ($numArtifactId) {
        $this->numArtifactId = $numArtifactId;
    }
    
    public function launch () {
        
        $objModel = new ArtifactModel();
        $arrInfo = $objModel->getArtifactInfo($this->numArtifactId);
        
        $arrInfo['weight_kb'] = 0;
        if (!empty($arrInfo['weight'])) {
            $arrInfo['weight_kb'] = round($arrInfo['weight']/1024);
        }
        
        return Response::html($this->render(array(
            'arrInfo' => $arrInfo
        )));
    }
    
}