<?php namespace backend\artifact;

use backend\artifact\model\ArtifactListOptionsModel;
use webcitron\Subframe\Controller;
use webcitron\Subframe\Application;

class ArtifactListController extends Controller { 
    
    const DEFAULT_LIMIT_PER_PAGE = 30;
    
    public function __construct () {}
    
    public function getList (ArtifactListOptionsModel $objListOptions) {
        $arrResult = array();
        $objModel = new model\ArtifactListModel();
        $arrResult['arrList'] = $objModel->getArtifacts($objListOptions);
        
        return self::answer($arrResult);
    }
    
    public static function getListAdmin ($numLimit, $strSearchString = '', $boolIsImported = false, $strOrderBy = 'default') {
        $objModel = new model\ArtifactListModel();
        $arrList = $objModel->getListAdmin($numLimit, $strSearchString, $boolIsImported, $strOrderBy);
        if (!empty($arrList)) {
            $strDomain = str_replace('admin.', '', Application::url());
            foreach ($arrList as & $arrArtifact) {
                $arrArtifact['strUrl'] = $strDomain.'/'.$arrArtifact['slug'].'-'.$arrArtifact['id'].'.html';
            }
        }
        return parent::answer($arrList);
    }
    
    
    
}

