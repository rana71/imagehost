<?php namespace admin\box\artifact;

use webcitron\Subframe\Response;
use backend\artifact\model\ArtifactModel;
use webcitron\Subframe\JsController;
use webcitron\Subframe\Application;

class OnHomepage extends \webcitron\Subframe\Box {
  
    
    public function launch () {
        JsController::runJs();
        $objModel = new ArtifactModel();
        $arrArtifacts = $objModel->getArtifactsOnHomepageAdmin();
        if (!empty($arrArtifacts)) {
            $strDomain = str_replace('admin.', '', Application::url());
            foreach ($arrArtifacts as & $arrArtifact) {
                $arrArtifact['strUrl'] = $strDomain.'/'.$arrArtifact['slug'].'-'.$arrArtifact['id'].'.html';
            }
        }
        return Response::html($this->render(array(
            'arrArtifacts' => $arrArtifacts
        )));
    }
    
}