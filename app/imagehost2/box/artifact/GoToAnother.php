<?php namespace imagehost2\box\artifact;

use webcitron\Subframe\Response;
use backend\artifact\ArtifactController;
use backend\advert\AdvertController;

class GoToAnother extends \webcitron\Subframe\Box {
    
    public $numCurrentId;
    public $numAdultsOnly;
    
    public function __construct ($numCurrentId, $numAdultsOnly) {
        $this->numCurrentId = $numCurrentId;
        $this->numAdultsOnly = $numAdultsOnly;
    }
    
    public function launch () {
        $arrAnothers = ArtifactController::getOlders($this->numCurrentId, 15);
        
        $arrNavigation = array();
        if (!empty($arrAnothers['result'][0])) {
            $arrNavigation['arrNext'] = $arrAnothers['result'][0];
        }
        $arrNewers = ArtifactController::getNewers($this->numCurrentId, 1);
        if (!empty($arrNewers['result'])) {
            $arrNavigation['arrPrev'] = $arrNewers['result'][0];
        }
        
        $strAdverts = '';
        if ($this->numAdultsOnly === 0) {
            $arrAdvert = AdvertController::getByArea('side-artifact1-adults-only');
            $strAdverts .= $arrAdvert['result']['code'];
        }
        
        $arrAdvert = AdvertController::getByArea('side-artifact');
        $strAdverts .= $arrAdvert['result']['code'];
        
        return Response::html($this->render(array(
            'strAdverts' => $strAdverts, 
            'arrAnothers' => $arrAnothers['result'], 
            'arrNavigation' => $arrNavigation
        )));
    }
    
}