<?php namespace imagehost2\box\puppy;

use webcitron\Subframe\Response;
use backend\puppy\model\PuppyModel;

class Area extends \webcitron\Subframe\Box {
    
    private $strAreaId = '';
    
    public function __construct ($strAreaId) {
        $this->strAreaId = $strAreaId;
    }
    
    public function launch () {
        $objModel = new PuppyModel();
        $arrPuppy = $objModel->getByArea($this->strAreaId);
    
        return Response::html($this->render(@array(
            'strAreaId' => $this->strAreaId, 
            'strPuppyCode' => $arrPuppy['code']
        )));
    }
    
}