<?php namespace imagehost2\box\artifact;

use webcitron\Subframe\Response;
use backend\artifact\model\ArtifactModel;
use webcitron\Subframe\Application;

class Elements extends \webcitron\Subframe\Box {
    
    private $numArtifactId;
    private $arrElements;
    private $arrBaseInfo;
    
    public function __construct ($numArtifactId, $arrBaseInfo = array(), $arrElements = array()) {
        $this->numArtifactId = $numArtifactId;
        $this->arrBaseInfo = $arrBaseInfo;
        $this->arrElements = $arrElements;
    }
    
    public function launch () {
        $strMainDescription = '';
        
        $numCurrentEnv = Application::currentEnvironment();
        if ($numCurrentEnv !== Application::ENVIRONMENT_PRODUCTION) {
            foreach ($this->arrElements as & $arrElement) {
                if ($arrElement['type'] === ArtifactModel::ITEM_TYPE_IMAGE && substr($arrElement['thumb_url'], 0, 1) === '/') {
                    $arrElement['thumb_url'] = 'http://imged.pl'.$arrElement['thumb_url'];
                }
            }
        }
        
        if (count($this->arrElements) > 1) {
            $strMainDescription = $this->arrBaseInfo['description'];
        }
        return Response::html($this->render(array(
            'arrElements' => $this->arrElements, 
            'strMainDescription' => $strMainDescription
        )));
        
    }
    
}