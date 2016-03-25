<?php namespace imagehost2\box\puppy;

use webcitron\Subframe\Response;
use backend\puppy\PuppyController;
use webcitron\Subframe\JsController;

class TopLayer extends \webcitron\Subframe\Box {
    
    public function __construct () {}
    
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