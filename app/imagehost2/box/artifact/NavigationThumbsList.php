<?php namespace imagehost2\box\artifact;

use webcitron\Subframe\Response;
use backend\artifact\model\ArtifactModel;
use webcitron\Subframe\Application;

class NavigationThumbsList extends \webcitron\Subframe\Box {
    
    private $numCurrentArtifactId;
    private $boolShowImported;
    private $strRelativeDate;
    
    public function __construct ($numCurrentArtifactId, $boolShowImported, $strRelativeDate = '') {
        $this->numCurrentArtifactId = $numCurrentArtifactId;
        $this->boolShowImported = $boolShowImported;
        $this->strRelativeDate = $strRelativeDate;
    }
    
    public function launch () {
        $objArtifactModel = new ArtifactModel();
        $arrItems = $objArtifactModel->getOlders($this->numCurrentArtifactId, 15, $this->boolShowImported, $this->strRelativeDate);
        
        $numCurrentEnv = Application::currentEnvironment();
        if ($numCurrentEnv !== Application::ENVIRONMENT_PRODUCTION) {
            foreach ($arrItems as & $arrItem) {
                if (substr($arrItem['thumb_url'], 0, 1) === '/') {
                    $arrItem['thumb_url'] = 'http://imged.pl'.$arrItem['thumb_url'];
                }
            }
        }
        
        return Response::html($this->render(array(
            'arrItems' => $arrItems
        )));
    }
    
}