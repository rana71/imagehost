<?php namespace imagehost2\box\artifact;

use webcitron\Subframe\Response;
use webcitron\Subframe\Url;
use webcitron\Subframe\JsController;
use backend\artifact\ArtifactController;
use backend\user\UserController;
use backend\advert\AdvertController;
use webcitron\Subframe\Application;

class Details extends \webcitron\Subframe\Box {
    
    public $arrArtifact = array();
    
    public function __construct ($arrArtifact) {
        $this->arrArtifact = $arrArtifact;
    }
    
    public function launch () {
        JsController::runJs();
        
        
        $arrArtifactAuthor = array();
        if ($this->arrArtifact['user_id'] > 0) {
            $arrArtifactAuthorResult = UserController::getById($this->arrArtifact['user_id'], array('username', 'activation_hash'));
            if (empty($arrArtifactAuthorResult['result']['activation_hash'])) {
                $arrArtifactAuthor = $arrArtifactAuthorResult['result'];
            }
        }
        
        $arrTags = ArtifactController::getTags($this->arrArtifact['id']);
        $arrTags = $arrTags['result'];
        
        $strShareWeb = sprintf('<a href="%s" title="%s"><img src="%s" alt="%s" /></a>', 
            Url::route('Details', array($this->arrArtifact['slug'], $this->arrArtifact['id'])), 
            $this->arrArtifact['title'], 
            Url::route('ImageDirect', array($this->arrArtifact['photo_directory'], $this->arrArtifact['slug'], $this->arrArtifact['id'], $this->arrArtifact['extension'])), 
            $this->arrArtifact['title']
        );
        
        $arrAdverts = array('strBottom' => '');
        if ($this->arrArtifact['adults_only'] === 0) {
            $arrAdvert = AdvertController::getByArea('bottom-artifact1-adults-only');
            $arrAdverts['strBottom'] .= $arrAdvert['result']['code'];
        }
        $advertBottom = AdvertController::getByArea('bottom-artifact');
        $arrAdverts['strBottom'] .= $advertBottom['result']['code'];
        
        if( !empty($this->arrArtifact['width']) && !empty($this->arrArtifact['height'])) {
            $this->arrArtifact['arrViewHelper']['showResolution'] = true;
        }
        if (!empty($this->arrArtifact['orginal_exif'])) {
            $this->arrArtifact['arrExifInfo'] = ArtifactController::getInfoFromExif($this->arrArtifact['orginal_exif']);
        }
        
        if ($this->arrArtifact['type'] === 'story' && !empty($this->arrArtifact['arrElements']) && $this->arrArtifact['adults_only'] != 1) {
            $this->arrArtifact['arrElements'][1]['showVideoPuppy'] = 1;
        }
        
        $strViewName = 'Image';
        if ($this->arrArtifact['type'] === 'story') {
            $strViewName = 'Gallery';
        }
        
        return Response::html($this->render(array(
            'strCurrentUrl' => Url::route('Details', array($this->arrArtifact['slug'], $this->arrArtifact['id'])), 
            'arrAdverts' => $arrAdverts, 
            'arrArtifact' => $this->arrArtifact, 
            'arrStoryElements' => @$this->arrArtifact['arrElements'], 
            'arrArtifactAuthor' => $arrArtifactAuthor, 
            'arrTags' => $arrTags, 
            'numEnvironment' => Application::currentEnvironment(), 
            'strShareWeb' => $strShareWeb
        ), $strViewName));
    }
    
}