<?php

namespace imagehost3\board;

use backend\newsletter\NewsletterController;
use \webcitron\Subframe\Board;
use \webcitron\Subframe\Response;
use webcitron\Subframe\Redirect;
use backend\artifact\model\ArtifactModel;
use \webcitron\Subframe\Url;
use webcitron\Subframe\Application;
use backend\artifact\model\ArtifactListOptionsModel;
use webcitron\Subframe\CssController;
use backend\user\UserController;
use backend\user\model\UserModel;
use imagehost3\box;
use backend\String;

class Details extends Board
{

    public function index($strSlug, $numId)
    {
        $objArtifactModel = new ArtifactModel();
        $arrArtifact = $objArtifactModel->getBaseInfo($numId);
        if (empty($arrArtifact)) {
            Redirect::route('Homepage');
        } else {
            if ($arrArtifact['is_removed'] == true) {
                if (strtotime($arrArtifact['removed_since_timestamp']) < strtotime(ArtifactModel::TIME_WITH_404_AFTER_REMOVE)) {
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

        $arrElements = $objArtifactModel->getElements($numId);
        $objArtifactModel->increaseShowsCount($numId);

        $strArtifactUrl = Url::route('Details', array('slug' => $arrArtifact['slug'], 'id' => $arrArtifact['id']));
        $objLayout = new \imagehost3\layout\ArtifactDetails();

        $objLayout->addBoxes('top', new \imagehost3\box\Topbar());
        
        if ($arrArtifact['is_age_restricted'] == true) {
            $objLayout->addBoxes('top', new box\puppy\Area('top-artifact'));
        } else {
            $objLayout->addBoxes('top', new box\puppy\Area('top-artifact1-adults-only'));
        }

        if ($arrArtifact['is_age_restricted'] == true) {
            $objLayout->addBoxes('sidebar', new box\puppy\Area('side-artifact'));
        } else {
            $objLayout->addBoxes('sidebar', new box\puppy\Area('side-artifact1-adults-only'));
        }
        if (UserController::isLoggedIn()) {
            $objUser = UserController::getLoggedUser();
            $strEmail = $objUser['email'];
            if(!NewsletterController::isSubscribed($strEmail)){
                $objLayout->addBoxes('sidebar', new box\newsletter\SmallForm());
            }
        } else {
            $objLayout->addBoxes('sidebar', new box\newsletter\SmallForm());
        }
//        
        $numElementNo = 1;
        foreach ($arrElements as $arrElement) {
            $strBoxName = '';
            switch ($arrElement['type']) {
                case ArtifactModel::ITEM_TYPE_IMAGE:
                    $strBoxName = '\imagehost3\box\artifact\ElementImage';
                    break;
                case ArtifactModel::ITEM_TYPE_YTVIDEO:
                    $strBoxName = '\imagehost3\box\artifact\ElementYtVideo';
                    break;
                case ArtifactModel::ITEM_TYPE_MEM:
                    $strBoxName = '\imagehost3\box\artifact\ElementMeme';
                    break;
            }
            if (!empty($strBoxName)) {
                $objLayout->addBoxes('elements', new $strBoxName($arrElement, $arrArtifact));
                if ($numElementNo === 1) {
                    $objLayout->addBoxes('elements', new box\puppy\Video());
                }
                $numElementNo++;
            }
        }

        if ($arrArtifact['is_age_restricted'] == true) {
            $objLayout->addBoxes('below-elements', new box\puppy\Area('bottom-artifact'));
        } else {
            $objLayout->addBoxes('below-elements', new box\puppy\Area('bottom-artifact1-adults-only'));
        }

        $objLayout->addBoxes('below-elements', new box\artifact\Info($arrArtifact));
        $objLayout->addBoxes('below-elements', new box\social\facebook\Comments($strArtifactUrl));
        
        
        $objListOptions = new ArtifactListOptionsModel(1);
        $objListOptions->setGlobalLimit(16);
        $objListOptions->setLimit(16);
        $objListOptions->disableLoading();
        $objListOptions->setOrder(array(
            ArtifactListOptionsModel::ORDERBY_ID_DESC
        ));
        $strWhereHomepage = ($arrArtifact['is_on_homepage'] === true) ? 'true' : 'false';
        $strWhereImported = ($arrArtifact['is_imported'] === true) ? 'true' : 'false';
        $objListOptions->addAndWhere(array('is_on_homepage' => $strWhereHomepage, 'is_imported' => $strWhereImported));
        $objListOptions->addAndWhere(array('id' => $numId), '<');
        $objListOptions->clearViewLayout();
        $objListOptions->addViewLayoutRow(array(array(6,6), array(6,6)));
        $objLayout->addBoxes('see-also', new \imagehost3\box\artifact\Stream($objListOptions));
        
        $objLayout->addBoxes('below-elements', new box\artifact\HorizontalNav($numId, $arrArtifact['is_imported'], $arrArtifact['is_on_homepage']));

        $objLayout->addBoxes('foot', new \imagehost3\box\Footer());

        if (count($arrElements) > 1 && $arrArtifact['is_age_restricted'] == false) {
            $objLayout->addBoxes('foot', new box\puppy\Sticky('sticky'));
        }
        $objLayout->addBoxes('foot', new \imagehost3\box\PuppiesDetector());
        
        $arrMetaTags = $this->createMetaTags($arrArtifact, $arrElements);
        $arrOpengraph = $this->createOpengraph($arrMetaTags['title'], $arrArtifact, $arrElements);

        $strBriefDescription = '';
        if ($arrArtifact['description'] !== $arrElements[0]['description']) {
            $strBriefDescription = $arrArtifact['description'];
        }

        if ($arrArtifact['author_account_id'] > 0) {
            $objUserModel = new UserModel();
            $arrAuthorAccount = $objUserModel->getAccount($arrArtifact['author_account_id']);
            if ($arrAuthorAccount['is_pro_stats'] === true) {
                $objArtifactModel->saveProStats($arrArtifact['id'], $arrArtifact['author_account_id']);
            }
        }

        return Response::html($objLayout->render(array(
            'title' => $arrMetaTags['title'],
            'description' => $arrMetaTags['description'],
            'robots' => $arrMetaTags['robots'],
            'googlebot' => $arrMetaTags['googlebot'],
            'arrOpengraph' => $arrOpengraph,
            'arrArtifact' => array(
                'strTitle' => $arrArtifact['title'],
                'strDescription' => $arrArtifact['description'],
                'strBriefDescription' => $strBriefDescription
            ),
            'arrElements' => $arrElements
        )));
    }

    public function commerceGallery($strSlug, $numId)
    {
        $objArtifactModel = new ArtifactModel();
        $arrArtifact = $objArtifactModel->getBaseInfo($numId);

        if (empty($arrArtifact)) {
            Redirect::route('Homepage');
        } else {
            if ($arrArtifact['is_removed'] == true) {
                if (strtotime($arrArtifact['removed_since_timestamp']) < strtotime(ArtifactModel::TIME_WITH_404_AFTER_REMOVE)) {
                    Redirect::route('Homepage');
                } else {
                    return $this->force404();
                }
            }
        }

        $arrElements = $objArtifactModel->getElements($numId);
        $objArtifactModel->increaseShowsCount($numId);

        $objLayout = new \imagehost3\layout\Standard();

        $objLayout->addBoxes('top', new \imagehost3\box\TopSmall());
        $objLayout->addBoxes('top', new \imagehost3\box\artifact\CommercialGallery($arrArtifact, $arrElements));

        $arrMetaTags = $this->createMetaTags($arrArtifact, $arrElements);
        $arrOpengraph = $this->createOpengraph($arrMetaTags['title'], $arrArtifact, $arrElements);

        return Response::html($objLayout->render(array(
            'title' => $arrMetaTags['title'],
            'description' => $arrMetaTags['description'],
            'robots' => $arrMetaTags['robots'],
            'googlebot' => $arrMetaTags['googlebot'],
            'arrOpengraph' => $arrOpengraph,
            'arrElements' => $arrElements
        )));
    }

    private function createOpengraph($strTitle, $arrArtifact, $arrElements)
    {
        $arrImages = array();
        foreach ($arrElements as $arrElement) {
            $strUrl = '';
            if (!empty($arrElement['image_url']) && !empty($arrElement['mimetype']) && $arrElement['mimetype'] !== 'image/gif') {
                if (substr($arrElement['image_url'], 0, 1) === '/') {
                    $strUrl = Application::url() . $arrElement['image_url'];
                } else {
                    $strUrl = $arrElement['image_url'];
                }
                $arrImages[] = array('strUrl' => $strUrl);
            }
        }

        if (empty($arrImages) && !empty($arrArtifact['thumb_url'])) {
            $strUrl = '';
            if (substr($arrArtifact['thumb_url'], 0, 1) === '/') {
                $strUrl = Application::url() . $arrArtifact['thumb_url'];
            } else {
                $strUrl = $arrArtifact['thumb_url'];
            }
            $arrImages[] = array('strUrl' => $strUrl);
        }

        $arrReturn = array(
            'strSiteName' => 'imgED',
            'strTitle' => $strTitle,
            'strType' => 'article',
            'arrImages' => $arrImages,
            'strUrl' => Url::route('Details', array(
                'slug' => $arrArtifact['slug'],
                'id' => $arrArtifact['id']
            )),
        );
        return $arrReturn;
    }

    private function createMetaTags($arrArtifact, $arrElements)
    {
        $arrReturn = array(
            'title' => '',
            'description' => '',
            'robots' => 'index, follow, archive',
            'googlebot' => 'index, follow, archive, snippet'
        );

        $arrReturn['description'] = '';
        
        if (count($arrElements) > 1) {
            $arrReturn['title'] = sprintf('%s - Galeria zdjęć i obrazów na imgED', String::upperCaseFirstChar($arrArtifact['title']));
            $arrReturn['description'] .= 'Zobacz galerię zdjęć i obrazów: '; 
       } else if ($arrElements[0]['type'] === 2) {
            $arrReturn['title'] = sprintf('%s - Film YouTube na imgED', String::upperCaseFirstChar($arrArtifact['title']));
            $arrReturn['description'] .= 'Zobacz film: ';
        } else if (!empty($arrElements[0]['mimetype']) && $arrElements[0]['mimetype'] === 'image/gif') {
            $arrReturn['title'] = sprintf('%s - Gif na imgED', String::upperCaseFirstChar($arrArtifact['title']));
            $arrReturn['description'] .= 'Zobacz gifa: ';
        } else {
            $arrReturn['title'] = sprintf('%s - Zdjęcie na imgED', String::upperCaseFirstChar($arrArtifact['title']));
            $arrReturn['description'] .= 'Zobacz zdjęcie: ';
        }


        $arrReturn['description'] .= String::upperCaseFirstChar($arrArtifact['title']);
        if (!empty($arrArtifact['description'])) {
            $arrReturn['description'] .= ' - ' . strip_tags(stripslashes($arrArtifact['description']));
        }
        $arrReturn['description'] .= ' - imgED';

        return $arrReturn;
    }

    private function force404()
    {
        $objCssController = CssController::getInstance();
        $objCssController->forceCssFile('Error');
        $objLayout = new \imagehost3\layout\Standard();

        $objListOptions = new ArtifactListOptionsModel(1);
        $objListOptions->setGlobalLimit(10);
        $objListOptions->setLimit(6);
        $objListOptions->disableLoading();
        $objListOptions->setOrder(array(
            ArtifactListOptionsModel::ORDERBY_ON_HOMEPAGE,
            ArtifactListOptionsModel::ORDERBY_DATE_DESC
        ));
        $objListOptions->addAndWhere(array('is_on_homepage' => 'true', 'is_imported' => 'false'));


        $objLayout->addBoxes('main', array(
            new \imagehost3\box\error\NotFound(),
            new \imagehost3\box\artifact\Stream($objListOptions)
        ));
        $objLayout->addBoxes('top', new \imagehost3\box\Topbar());
        $objLayout->addBoxes('foot', new \imagehost3\box\Footer());
        $objLayout->addBoxes('foot', new \imagehost3\box\PuppiesDetector());


        $objResponse = Response::html($objLayout->render(array(
            'title' => 'Nie odnaleziono strony - imgED',
        )));
        $objResponse->setStatus(404);
        return $objResponse;
    }

}
