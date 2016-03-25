<?php namespace imagehost2\board;

use \webcitron\Subframe\Board;
use \webcitron\Subframe\Response;
use webcitron\Subframe\Redirect;
use backend\artifact\model\ArtifactModel;
use backend\artifact\StoryController;
use backend\artifact\ImageController;
use \webcitron\Subframe\Url;
use webcitron\Subframe\Application;

class Details extends Board {
  
    
    public function index ($strSlug, $numId) {
        $objArtifactModel = new ArtifactModel();
        $arrArtifact = $objArtifactModel->getBaseInfo($numId);
        $arrElements = $objArtifactModel->getElements($numId);
        
        if (empty($arrArtifact)) {
            Redirect::route('Homepage');
        } else {
            if ($arrArtifact['is_removed'] == true) {
                if (strtotime($arrArtifact['removed_since_timestamp']) < strtotime('-1 month')) {
                    Redirect::route('Homepage');
                } else {
                    return $this->force404();
                }
            }
            if ($arrArtifact['slug'] !== $strSlug) {
                Redirect::route('Details', array(
                    'strSlug' => $arrArtifact['slug'], 
                    'numId' => $arrArtifact['id']
                ));
            }
        }
        $objArtifactModel->increaseShowsCount($numId);
        
        $strArtifactUrl = Url::route('Details', array('slug' => $arrArtifact['slug'], 'id' => $arrArtifact['id']));
        
        $objLayout = new \imagehost2\layout\Sidebar();
        
        $objLayout->addBoxes('jumbotron', new \imagehost2\box\PageHeader($arrArtifact['title']));
        
        if ($arrArtifact['is_age_restricted'] == true) {
            $objLayout->addBoxes('jumbotron', new \imagehost2\box\puppy\Area('top-artifact'));
        } else {
            $objLayout->addBoxes('jumbotron', new \imagehost2\box\puppy\Area('top-artifact1-adults-only'));
        }
        
//        $objLayout->addBoxes('right', new \imagehost2\box\artifact\NavigationPrevNext($numId));
        if ($arrArtifact['is_age_restricted'] == true) {
            $objLayout->addBoxes('right', new \imagehost2\box\puppy\Area('side-artifact'));
        } else {
            $objLayout->addBoxes('right', new \imagehost2\box\puppy\Area('side-artifact1-adults-only'));
        }
        
        $objLayout->addBoxes('right', new \imagehost2\box\artifact\NavigationThumbsList($numId, $arrArtifact['is_imported'], $arrArtifact['add_timestamp']));
        
        $objLayout->addBoxes('left-inside', new \imagehost2\box\artifact\Elements($numId, $arrArtifact, $arrElements));
        if ($arrArtifact['is_age_restricted'] == true) {
            $objLayout->addBoxes('left-inside', new \imagehost2\box\puppy\Area('bottom-artifact'));
        } else {
            $objLayout->addBoxes('left-inside', new \imagehost2\box\puppy\Area('bottom-artifact1-adults-only'));
        }
        
        $objLayout->addBoxes('stats', new \imagehost2\box\artifact\Stats($numId));
        $objLayout->addBoxes('tags', new \imagehost2\box\tags\ArtifactTags($numId));
        $objLayout->addBoxes('share-socials', new \imagehost2\box\social\ShareAll($strArtifactUrl));
        
        $objLayout->addBoxes('artifact-options', new \imagehost2\box\artifact\ArtifactOptions($numId));
        $objLayout->addBoxes('left-bottom', new \imagehost2\box\social\facebook\Comments($strArtifactUrl));
        $objLayout->addBoxes('left-bottom', new \imagehost2\box\artifact\Info($numId));
        
        if (count($arrElements) > 1) {
            $objLayout->addBoxes('bottom', new \imagehost2\box\puppy\Sticky('sticky'));
        }
        
        
        $arrMetaTags = $this->createMetaTags($arrArtifact, $arrElements);
        $arrOpengraph = $this->createOpengraph($arrMetaTags['title'], $arrArtifact);
        
        return Response::html($objLayout->render(array(
            'title' => $arrMetaTags['title'], 
            'description' => $arrMetaTags['description'],  
            'robots' => $arrMetaTags['robots'], 
            'googlebot' => $arrMetaTags['googlebot'], 
            'arrOpengraph' => $arrOpengraph
        )));
    }
    
    private function createOpengraph ($strTitle, $arrArtifact) {
        if (substr($arrArtifact['thumb_url'], 0, 1) === '/') {
            $strImage = Application::url() . $arrArtifact['thumb_url'];
        } else {
            $strImage = $arrArtifact['thumb_url'];
        }
        $arrReturn = array(
            'strSiteName' => 'imgED', 
            'strTitle' => $strTitle, 
            'strType' => 'article', 
            'strImage' => $strImage, 
            'strUrl' => Url::route('Details', array(
                'slug' => $arrArtifact['slug'], 
                'id' => $arrArtifact['id']
            )), 
        );
        return $arrReturn;
    }
    
    private function createMetaTags ($arrArtifact, $arrElements) {
        $arrReturn = array(
            'title' => '', 
            'description' => '', 
            'robots' => 'index, follow, archive', 
            'googlebot' => 'index, follow, archive, snippet'
        );
        
         if (count($arrElements) > 1) {
            $arrReturn['title'] = sprintf('%s - Galeria na imgED', $arrArtifact['title']);
        }
        else if ($arrElements[0]['type'] === 2) {
            $arrReturn['title'] = sprintf('%s - Film YouTube na imgED', $arrArtifact['title']);
        }
        else if ($arrElements[0]['mimetype'] === 'image/gif') {
            $arrReturn['title'] = sprintf('%s - Gif na imgED', $arrArtifact['title']);
        } else {
            $arrReturn['title'] = sprintf('%s - Zdjęcie na imgED', $arrArtifact['title']);
        }
        
        
        $arrReturn['description'] = $arrArtifact['title'];
        if (!empty($arrArtifact['description'])) {
            $arrReturn['description'] .= ' - '.strip_tags(stripslashes($arrArtifact['description']));
        }
        $arrReturn['description'] .= ' - imgED';
        
        return $arrReturn;
    }
    
    public function OLDindex($strSlug, $numId) {
        
        date_default_timezone_set('Europe/Warsaw');
        
        $arrArtifact = ArtifactController::getBaseInfo($numId);
        $arrArtifact = $arrArtifact['result'];
        
        
        if (empty($arrArtifact)) {
            Redirect::route('Homepage');
        } else if (!empty($arrArtifact['removed_since'])) {
            if (strtotime($arrArtifact['removed_since']) < strtotime('-1 month')) {
                Redirect::route('Homepage');
            } else {
                return $this->force404();
            }
        }
        
        if (empty($arrArtifact['thumb_url']) && !empty($arrArtifact['image_source'])) {
            $strImageContents = @file_get_contents($arrArtifact['image_source']);
            if (!empty($strImageContents)) {
                ArtifactController::saveFile($strImageContents, $numId, $arrArtifact['slug'], $arrArtifact['photo_directory']);
                $objImage = @imagecreatefromstring($strImageContents);
                if ($objImage !== false) {
                    $numImageWidth = imagesx($objImage);
                    $numImageHeight = imagesy($objImage);

                    $objFinfo = finfo_open();
                    $strMimeType = finfo_buffer($objFinfo, $strImageContents, FILEINFO_MIME_TYPE);

                    ImageController::fillImageInfo($numId, array(
                        'image_weight' => strlen($strImageContents), 
                        'width' => $numImageWidth, 
                        'height' => $numImageHeight, 
                        'mimetype' => $strMimeType, 
                        'thumb_path'=> '/'.$arrArtifact['photo_directory'], 
                        'thumb_filename' => sprintf('%s-%d.jpg', $arrArtifact['slug'], $numId)
                    ));
                }
                $arrArtifact = ArtifactController::getBaseInfo($numId);
                $arrArtifact = $arrArtifact['result'];
            }
        }
        
        if ($arrArtifact['slug'] !== $strSlug) {
            Redirect::route('Details', array(
                'strSlug' => $arrArtifact['slug'], 
                'numId' => $arrArtifact['id']
            ));
        }
        
        $strAdultsStringToCheck = '';
        if ($arrArtifact['type'] === 'story') {
            $arrDetailedInfo = StoryController::getDetailedInfo($arrArtifact['id']);
            $strAdultsStringToCheck = $arrArtifact['title'].' '.$arrArtifact['slug'].' '.$arrArtifact['description'];
            foreach ($arrDetailedInfo['result']['arrElements'] as $arrStoryElement) {
                $strAdultsStringToCheck .= ' '.$arrStoryElement['title'].' '.$arrStoryElement['description'];
            }
        } else {
            $arrDetailedInfo = ArtifactController::getDetailedInfo($arrArtifact['id']);
            $arrDetailedInfo['arrElements'] = array();
            $strAdultsStringToCheck = $arrArtifact['title'].' '.$arrArtifact['slug'].' '.$arrArtifact['description'];
        }
        $arrArtifact = array_merge($arrArtifact, $arrDetailedInfo['result']);
        ArtifactController::increaseShowsCount($arrArtifact['id']);
        
        
        $objLayout = new \imagehost2\layout\Sidebar();
        
        $objLayout->addBoxes('topbar', array(
            new \imagehost2\box\Topbar()
        ));
        $objLayout->addBoxes('left', array(
            new \imagehost2\box\files\Details($arrArtifact), 
//            new \imagehost2\box\comment\ArtifactComments($arrArtifact['id'], $arrArtifact['slug'])
        ));
        
        $objLayout->addBoxes('right', array(
//            new \imagehost2\box\puppyArea('side-artifact'), 
            new \imagehost2\box\files\GoToAnother($numId, $arrArtifact['adults_only']), 
            new \imagehost2\box\puppy\TopLayer()
        ));
        
        $arrJumbotronBoxes = array();
        $arrJumbotronBoxes[] = new \imagehost2\box\PageHeader($arrArtifact['title']);
        if ($arrArtifact['adults_only'] !== 1) {
            $arrJumbotronBoxes[] = new \imagehost2\box\puppy\Area('top-artifact1-adults-only');
        }
        $arrJumbotronBoxes[] = new \imagehost2\box\puppy\Area('top-artifact');
        
        $objLayout->addBoxes('jumbotron', $arrJumbotronBoxes);
        
        if ($arrArtifact['type'] === 'story') {
            $strTitle = sprintf('%s - Galeria na imgED', $arrArtifact['title']);
        }
        else if ($arrArtifact['extension'] === 'gif') {
            $strTitle = sprintf('%s - Gif na imgED', $arrArtifact['title']);
        } else {
            $strTitle = sprintf('%s - Zdjęcie na imgED', $arrArtifact['title']);
        }
        
        $arrOpengraph = array(
            'strSiteName' => 'imgED', 
            'strTitle' => $strTitle, 
            'strType' => 'article', 
            'strUrl' => Url::route('Details', array(
                'slug' => $arrArtifact['slug'], 
                'id' => $arrArtifact['id']
            )), 
        );
        if (!empty($arrArtifact['extension'])) {
            $strId = $arrArtifact['id'];
            if ($arrArtifact['type'] === 'story') {
                $strId = 1;
            }
            $arrOpengraph['strImage'] = Url::route('ImageDirect', array(
                'directory' => $arrArtifact['photo_directory'], 
                'slug' => $arrArtifact['slug'], 
                'id' => $strId, 
                'ext' => $arrArtifact['extension']
            ));
        }
        
        $strDescription = $arrArtifact['title'];
        if (!empty($arrArtifact['description'])) {
            $strDescription .= ' - '.strip_tags(stripslashes($arrArtifact['description']));
        }
        $strDescription .= ' - imgED';
        
        
        return Response::html($objLayout->render(array(
            'title' => $strTitle, 
            'description' => $strDescription, 
            'robots' => 'index, follow, archive', 
            'googlebot' => 'index, follow, archive, snippet', 
            'arrOpengraph' => $arrOpengraph
        )));
    }
    
    private function force404 () {
        $objLayout = new \imagehost2\layout\Standard();
        $objLayout->addBoxes('main', new \imagehost2\box\artifact\NotFound());
        
        $objResponse = Response::html($objLayout->render());
        $objResponse->setStatus(404);
        return $objResponse;
    }
    
    
}