<?php namespace imagehost3\box\artifact;

use backend\artifact\model\ArtifactModel;
use webcitron\Subframe\Response;
use \webcitron\Subframe\JsController;

class HorizontalNav extends \webcitron\Subframe\Box {
    
    private $numCurrentId;
    private $boolShowImported;
    private $boolIsOnHomepage;
    
    public function __construct ($numCurrentId, $boolShowImported, $boolIsOnHomepage) {
        $this->numCurrentId = $numCurrentId;
        $this->boolShowImported = $boolShowImported;
        $this->boolIsOnHomepage = $boolIsOnHomepage;
    }
    
    public function launch () {
        JsController::runJs();
        $objArtifactModel = new ArtifactModel();
        
        $arrOlderItem = $objArtifactModel->getOlders($this->numCurrentId, 1, $this->boolShowImported, $this->boolIsOnHomepage);
        $arrNewerItem = $objArtifactModel->getNewers($this->numCurrentId, 1, $this->boolShowImported, $this->boolIsOnHomepage);
        
        return Response::html($this->render(array(
            'arrLeft' => $arrNewerItem, 
            'arrRight' => $arrOlderItem
        )));
        
    }
    
}