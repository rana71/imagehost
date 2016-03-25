<?php namespace imagehost3\box\puppy;

use webcitron\Subframe\Response;
use backend\puppy\PuppyController;
use webcitron\Subframe\JsController;
use webcitron\Subframe\CssController;

class TopLayer extends \webcitron\Subframe\Box {
    
    public function __construct () {
        CssController::addStylesheets('puppy');
    }
    
    public function launch () {
      
        $arrPuppy = PuppyController::getTopLayer();

        if (!empty($arrPuppy['result']['code'])) {
            JsController::runJs();
        }
        return Response::html($this->render(@array(
            'strPuppyCode' => $arrPuppy['result']['code'], 
            'numWidth' => !empty($arrPuppy['result']['width']) ? $arrPuppy['result']['width'] : 0, 
            'numHeight' => !empty($arrPuppy['result']['height']) ? $arrPuppy['result']['height'] : 0
        )));
    }
    
}