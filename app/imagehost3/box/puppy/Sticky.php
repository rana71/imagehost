<?php namespace imagehost3\box\puppy;

use webcitron\Subframe\Response;
use backend\puppy\model\PuppyModel;
use webcitron\Subframe\JsController;
use webcitron\Subframe\CssController;

class Sticky extends \webcitron\Subframe\Box {
    
    private $strAreaId = '';
    
    public function __construct ($strAreaId) {
        $this->strAreaId = $strAreaId;
        CssController::addStylesheets('puppy');
    }
    
    public function launch () {
        JsController::runJs();
        $objModel = new PuppyModel();
        $arrPuppy = $objModel->getByArea($this->strAreaId);
    
        return Response::html($this->render(@array(
            'arrPuppy' => $arrPuppy
        )));
    }
    
}