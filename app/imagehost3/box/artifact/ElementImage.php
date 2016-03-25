<?php namespace imagehost3\box\artifact;

use webcitron\Subframe\Response;
use backend\artifact\model\ArtifactModel;
use webcitron\Subframe\Application;
use webcitron\Subframe\CssController;
use backend\puppy\model\PuppyModel;

class ElementImage extends \webcitron\Subframe\Box {
    
    private $arrElement;
    private $arrArtifact;
    
    public function __construct ($arrElement, $arrArtifact) {
        $this->arrElement = $arrElement;
        $this->arrArtifact = $arrArtifact;
        CssController::addStylesheets('artifact');
    }
    
    public function launch () {
        $objModel = new ArtifactModel();
        
        $numCurrentEnv = Application::currentEnvironment();
        if ($numCurrentEnv !== Application::ENVIRONMENT_PRODUCTION) {
                if ($this->arrElement['type'] === ArtifactModel::ITEM_TYPE_IMAGE && substr($this->arrElement['thumb_url'], 0, 1) === '/') {
                    $this->arrElement['thumb_url'] = 'http://imged.pl'.$this->arrElement['thumb_url'];
            }
        }
        
        $this->arrElement['weight_kb'] = 0;
        if (!empty($this->arrElement['weight'])) {
            $this->arrElement['weight_kb'] = round($this->arrElement['weight']/1024);
        }
       
        $this->arrElement['pretty_exif'] = array();
        if (!empty($this->arrElement['exif'])) {
            $this->arrElement['pretty_exif'] =  $objModel->getInfoFromExif($this->arrElement['exif']);
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