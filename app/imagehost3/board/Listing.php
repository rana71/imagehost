<?php namespace imagehost3\board;

use \webcitron\Subframe\Board;
use \webcitron\Subframe\Response;
use \webcitron\Subframe\Redirect;
//use backend\search\QueryController;
//use backend\artifact\ArtifactController;
use backend\tag\model\TagModel;
use backend\artifact\model\ArtifactListOptionsModel;
use backend\searcher\model\QueryModel;
use backend\artifact\model\ArtifactListModel;
use backend\user\model\UserModel;
use \webcitron\Subframe\CssController;
use backend\String;

class Listing extends Board {
    
    public function __construct () {
        $objCssController = CssController::getInstance();
        $objCssController->forceCssFile('Listing');
    }
  
    public function userUploads ($strUserDisplayName) {
        $objLayout = new \imagehost3\layout\Standard();
        
        $objUserModel = new UserModel();
        $arrUserAccount = $objUserModel->getAccountByDisplayname($strUserDisplayName);
        
        $objListOptions = new ArtifactListOptionsModel(1);
        $objListOptions->setOrder(array(
            ArtifactListOptionsModel::ORDERBY_DATE_DESC
        ));
        $objListOptions->addAuthorAccount($arrUserAccount['id']);
        
        $objLayout->addBoxes('main', array(
//            new \imagehost2\box\PageHeader($arrUserAccount['display_name'].' - dodane'), 
            new \imagehost3\box\artifact\Stream($objListOptions, $arrUserAccount['display_name'].' - dodane'), 
//            new \imagehost2\box\puppy\TopLayer()
        ));
        
        $objLayout->addBoxes('main', new \imagehost2\box\puppy\Sticky('sticky'));
        
        $objModelList = new ArtifactListModel();
        $numResultsCount = $objModelList->getResultsCount($objListOptions);
        if ($numResultsCount > 0) {
            $strMetaTitle = sprintf('%s - %d dodanych obrazków', String::upperCaseFirstChar($arrUserAccount['display_name']), $numResultsCount);
        } else {
            $strMetaTitle = sprintf('%s - brak dodanych obrazków', String::upperCaseFirstChar($arrUserAccount['display_name']));
        }
        
        $objLayout->addBoxes('top', new \imagehost3\box\Topbar());
        $objLayout->addBoxes('foot', new \imagehost3\box\Footer());
        $objLayout->addBoxes('foot', new \imagehost3\box\PuppiesDetector());
        
        return Response::html($objLayout->render(array(
            'title' => $strMetaTitle
        )));
    }
    
    public function latestStories () {
        $objLayout = new \imagehost3\layout\Standard();
        
        $objListOptions = new ArtifactListOptionsModel(1);
        $objListOptions->setOrder(array(
            ArtifactListOptionsModel::ORDERBY_DATE_DESC
        ));
        $objListOptions->addAndWhere(array('is_imported' => 'false'));
        
        $objLayout->addBoxes('main', array(
            new \imagehost3\box\artifact\Stream($objListOptions, 'Ostatnio dodane galerie zdjęć'), 
        ));
        $objLayout->addBoxes('main', new \imagehost3\box\puppy\Sticky('sticky'));
        
        $objLayout->addBoxes('top', new \imagehost3\box\Topbar());
        $objLayout->addBoxes('foot', new \imagehost3\box\Footer());
        $objLayout->addBoxes('foot', new \imagehost3\box\PuppiesDetector());
        
        return Response::html($objLayout->render(array(
            'title' => 'Ostatnio dodane galerie zdjęć', 
            'robots' => 'noindex, follow', 
            'googlebot' => 'noindex, follow'
        )));
    }
    
    public function onHomepage () {
        $objLayout = new \imagehost3\layout\Standard();
        
        $objListOptions = new ArtifactListOptionsModel(1);
        $objListOptions->setOrder(array(
            ArtifactListOptionsModel::ORDERBY_ON_HOMEPAGE, 
            ArtifactListOptionsModel::ORDERBY_DATE_DESC
        ));
        $objListOptions->addAndWhere(array('is_on_homepage' => 'true', 'is_imported' => 'false'));
        
        
        $objLayout->addBoxes('main', array(
            new \imagehost3\box\puppy\Area('top-tag'), 
            new \imagehost3\box\artifact\Stream($objListOptions, 'Zajeb@#$% Galerie'), 
        ));
        $objLayout->addBoxes('main', new \imagehost3\box\puppy\Sticky('sticky'));
        
        $objLayout->addBoxes('top', new \imagehost3\box\Topbar());
        $objLayout->addBoxes('foot', new \imagehost3\box\Footer());
        $objLayout->addBoxes('foot', new \imagehost3\box\PuppiesDetector());
        
        return Response::html($objLayout->render(array(
            'title' => 'Najlepsze galerie na imgED', 
            'robots' => 'noindex, follow', 
            'googlebot' => 'noindex, follow'
        )));
    }
    
    public function latestImported () {
        $objLayout = new \imagehost3\layout\Standard();
        
        $objListOptions = new ArtifactListOptionsModel(1);
        $objListOptions->setOrder(array(
            ArtifactListOptionsModel::ORDERBY_DATE_DESC
        ));
        $objListOptions->addAndWhere(array('is_imported' => 'true'));
        
        
        $objLayout->addBoxes('main', array(
            new \imagehost3\box\artifact\Stream($objListOptions, 'Ostatnio dodane galerie Allegro'), 
        ));
        $objLayout->addBoxes('main', new \imagehost3\box\puppy\Sticky('sticky'));
        
        $objLayout->addBoxes('top', new \imagehost3\box\Topbar());
        $objLayout->addBoxes('foot', new \imagehost3\box\Footer());
        $objLayout->addBoxes('foot', new \imagehost3\box\PuppiesDetector());
        
        return Response::html($objLayout->render(array(
            'title' => 'Ostatnio dodane galerie Allegro', 
            'robots' => 'noindex, follow', 
            'googlebot' => 'noindex, follow'
        )));
    }
    
    public function query($strQuerySlug) {
        $objLayout = new \imagehost3\layout\Standard();
        
        $objModel = new QueryModel();
        $arrQuery = $objModel->getBySlug($strQuerySlug);
        if (empty($arrQuery)) {
            $strQueryTitle = str_replace('-', ' ', $strQuerySlug);
            $arrQuery = $objModel->add($strQueryTitle, $strQuerySlug);
        }
        
        $objListOptions = new ArtifactListOptionsModel(1);
        $objListOptions->setOrder(array(
            ArtifactListOptionsModel::ORDERBY_DATE_DESC
        ));
        $objListOptions->addQuery($arrQuery['title']);
        
        $objListModel = new ArtifactListModel();
        $numResultsCount = $objListModel->getResultsCount($objListOptions);
        
        $boolHaveResults = ($numResultsCount > 0); 
        $objModel->setIsHaveResults($arrQuery['id'], $boolHaveResults);
        if ($boolHaveResults === true) {
            $objModel->markAsUsedById($arrQuery['id']);
        }
        
        $objLayout->addBoxes('main', array(
            new \imagehost3\box\artifact\Stream($objListOptions, $arrQuery['title'])
        ));
        $objLayout->addBoxes('main', new \imagehost3\box\puppy\Sticky('sticky'));
        
        $strDescription = '';
        
        if ($numResultsCount > 0) {
            $strTitle = sprintf('%s (%d znalezionych zdjęć i obrazów) - imgED', String::upperCaseFirstChar($arrQuery['title']), $numResultsCount);
            $strDescription = sprintf('Zobacz Galerię: %s (%d znalezionych zdjęć i obrazów) - imgED', String::upperCaseFirstChar($arrQuery['title']), $numResultsCount);
        } else {
            $strTitle = sprintf('%s - brak zdjęć i obrazów - imgED', String::upperCaseFirstChar($arrQuery['title']));
        }
        
        $objLayout->addBoxes('top', new \imagehost3\box\Topbar());
        $objLayout->addBoxes('foot', new \imagehost3\box\Footer());
        $objLayout->addBoxes('foot', new \imagehost3\box\PuppiesDetector());
        
        return Response::html($objLayout->render(array(
            'title' => $strTitle, 
            'description' => $strDescription, 
            'robots' => 'noindex, follow', 
            'googlebot' => 'noindex, follow'
        )));
    }
    
    
    
    
    public function tag($strTagSlug) {
        $objLayout = new \imagehost3\layout\Standard();
        
        $objTagModel = new TagModel();
        $arrTag = $objTagModel->getTagBySlug($strTagSlug);
        
        if (empty($arrTag)) {
            return $this->force404();
        } else if (!empty($arrTag['removed_since'])) {
            if (strtotime($arrTag['removed_since']) < strtotime('-1 month')) {
                Redirect::route('Homepage');
            } else {
                return $this->force404();
            }
        }
        
        if ($arrTag['elements_count'] > 0) {
            $strHeader = sprintf('Galeria %d zdjęć w temacie: %s', $arrTag['elements_count'], $arrTag['title']);
            $strTitle = sprintf('%s (%d znalezionych zdjęć i obrazów) - imgED', String::upperCaseFirstChar($arrTag['title']), $arrTag['elements_count']);
            $strDescription = sprintf('Zobacz Galerię: %s (%d zdjęć i obrazów) - imgED', String::upperCaseFirstChar($arrTag['title']), $arrTag['elements_count']);
        } else {
            $strHeader = sprintf('Brak wyników w temacie %s', String::upperCaseFirstChar($arrTag['title']));
            $strTitle = sprintf('%s (brak zdjęć i obrazów) - imgED', String::upperCaseFirstChar($arrTag['title']));
            $strDescription = sprintf('Galeria %s nie zawiera zdjęć ani obrazów - imgED', String::upperCaseFirstChar($arrTag['title']));
        }
        
        $objListOptions = new ArtifactListOptionsModel(1);
        $objListOptions->setOrder(array(
            ArtifactListOptionsModel::ORDERBY_DATE_DESC
        ));
        $objListOptions->addTag($arrTag['id']);
        
        $objLayout->addBoxes('main', array(
//            new \imagehost2\box\PageHeader($strHeader), 
            new \imagehost3\box\puppy\Area('top-tag'), 
            new \imagehost3\box\artifact\Stream($objListOptions, $strHeader), 
        ));
        $objLayout->addBoxes('main', new \imagehost2\box\puppy\Sticky('sticky'));
        
        $objLayout->addBoxes('top', new \imagehost3\box\Topbar());
        $objLayout->addBoxes('foot', new \imagehost3\box\Footer());
        $objLayout->addBoxes('foot', new \imagehost3\box\PuppiesDetector());
        
        return Response::html($objLayout->render(array(
            'title' => $strTitle, 
            'description' => $strDescription, 
            'robots' => 'index, follow, archive', 
            'googlebot' => 'index, follow, archive, snippet'
        )));
    }
    
    
    private function force404 () {
        $objLayout = new \imagehost2\layout\Standard();
        $objLayout->addBoxes('main', array(
            new \imagehost2\box\artifact\NotFound()
        ));
        $objResponse = Response::html($objLayout->render());
        $objResponse->setStatus(404);
        return $objResponse;
    }
    
    public function tag301 ($strTagSlug) {
        Redirect::route('Listing::tag', array(  
            'tag' => $strTagSlug
        ));
    }
    
}