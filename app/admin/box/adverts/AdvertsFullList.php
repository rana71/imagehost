<?php namespace admin\box\adverts;

use webcitron\Subframe\Response;
use webcitron\Subframe\JsController;
use backend\puppy\model\PuppyModel;

class AdvertsFullList extends \webcitron\Subframe\Box {
    
    public function launch () {
        JsController::runJs();
        
        $objModel = new PuppyModel();
        
        $arrAdverts = $objModel->getAllByArea();
        $arrAreas = $objModel->getAreas();
        
        if (!empty($arrAreas)) {
            foreach ($arrAreas as & $arrArea) {
                $strAreaId = $arrArea['strId'];
                if (!empty($arrAdverts[$strAreaId])) {
                    $arrArea['strAdvertCode'] = $arrAdverts[$strAreaId]['code'];
                }
            }
        }
        
        return Response::html($this->render(array(
            'arrAreas' => $arrAreas
        )));
    }
    
}