<?php namespace imagehost3\box\artifact;

use webcitron\Subframe\Response;
use backend\artifact\model\ArtifactModel;
use webcitron\Subframe\Application;
use webcitron\Subframe\CssController;
use backend\puppy\model\PuppyModel;

class ElementMeme extends \webcitron\Subframe\Box {
    
    private $arrElement;
    private $arrArtifact;
    
    public function __construct ($arrElement, $arrArtifact) {
        $this->arrElement = $arrElement;
        $this->arrArtifact = $arrArtifact;
        CssController::addStylesheets('artifact');
    }
    
    public function launch () {
        
        $numCurrentEnv = Application::currentEnvironment();
        if ($numCurrentEnv !== Application::ENVIRONMENT_PRODUCTION) {
            if ($this->arrElement['type'] === ArtifactModel::ITEM_TYPE_IMAGE && substr($this->arrElement['thumb_url'], 0, 1) === '/') {
                $this->arrElement['thumb_url'] = 'http://imged.pl'.$this->arrElement['thumb_url'];
            }
        }
        
        $objPuppyModel = new PuppyModel();
        $arrPreviewPuppy = $objPuppyModel->getByArea('preview-modal');
        
        return Response::html($this->render(array(
            'arrElement' => $this->arrElement, 
            'arrArtifact' => $this->arrArtifact, 
            'arrPreviewPuppy' => $arrPreviewPuppy
        )));
        
    }
    
}