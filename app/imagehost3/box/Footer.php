<?php namespace imagehost3\box;

use webcitron\Subframe\Response;
use \backend\artifact\model\ArtifactModel;

class Footer extends \webcitron\Subframe\Box {
    
    public function launch () {
       
        $objModel = new ArtifactModel();
        $arrStats = $objModel->getStatsStatic();
        
        return Response::html($this->render(array(
            'arrStats' => $arrStats
        )));
    }
    
}