<?php namespace imagehost3\box\artifact;

use backend\artifact\model\ArtifactModel;
use webcitron\Subframe\Response;
use backend\tag\model\TagModel;
use webcitron\Subframe\Url;
use backend\user\model\UserModel;
use \webcitron\Subframe\JsController;

class Info extends \webcitron\Subframe\Box {
    
    private $arrBaseInfo;
    
    public function __construct ($arrBaseInfo) {
        $this->arrBaseInfo = $arrBaseInfo;
    }
    
    public function launch () {
        JsController::runJs();
        
        $strPageUrl = Url::route('Details', array('slug' => $this->arrBaseInfo['slug'], 'id' => $this->arrBaseInfo['id']));
        $objModel = new ArtifactModel();
        $arrStats = $objModel->getArtifactStats($this->arrBaseInfo['id']);
        
        $objModel = new TagModel();
//        $arrTags = $objModel->getArtifactTags($this->arrBaseInfo['id']);
        $arrTags = array();
        
        $arrAuthor = array();
        if ($this->arrBaseInfo['author_account_id'] > 0) {
            $objModel = new UserModel();
            $arrAuthor = $objModel->getAccount($this->arrBaseInfo['author_account_id']);
        }
        
        return Response::html($this->render(array(
            'arrStats' => $arrStats, 
            'arrTags' => $arrTags, 
            'strPageUrl' => $strPageUrl, 
            'strAddTimestamp' => empty($this->arrBaseInfo['is_imported']) ? $this->arrBaseInfo['add_timestamp'] : null, 
            'strArtifactTitle' => $this->arrBaseInfo['title'], 
            'arrAuthor' => $arrAuthor
        )));
        
    }
    
}