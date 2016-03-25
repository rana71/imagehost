<?php namespace imagehost2\board;

use \webcitron\Subframe\Board;
use \webcitron\Subframe\Response;
use \webcitron\Subframe\Redirect;
use backend\search\QueryController;
use backend\artifact\ArtifactController;
use backend\tag\model\TagModel;
use backend\artifact\model\ArtifactListOptionsModel;
use backend\searcher\model\QueryModel;
use backend\artifact\model\ArtifactListModel;
use backend\user\model\UserModel;

class Listing extends Board {
  
    public function userUploads ($strUserDisplayName) {
        $objLayout = new \imagehost2\layout\Standard();
        
        $objUserModel = new UserModel();
        $arrUserAccount = $objUserModel->getAccountByDisplayname($strUserDisplayName);
        
        $objListOptions = new ArtifactListOptionsModel(1);
        $objListOptions->setOrder(array(
            ArtifactListOptionsModel::ORDERBY_DATE_DESC
        ));
        $objListOptions->addAuthorAccount($arrUserAccount['id']);
        
        $objLayout->addBoxes('main', array(
            new \imagehost2\box\PageHeader($arrUserAccount['display_name'].' - dodane'), 
            new \imagehost2\box\artifact\Stream($objListOptions), 
//            new \imagehost2\box\puppy\TopLayer()
        ));
        
        $objLayout->addBoxes('main', new \imagehost2\box\puppy\Sticky('sticky'));
        
        $objModelList = new ArtifactListModel();
        $numResultsCount = $objModelList->getResultsCount($objListOptions);
        if ($numResultsCount > 0) {
            $strMetaTitle = sprintf('%s - %d dodanych obrazków', $arrUserAccount['display_name'], $numResultsCount);
        } else {
            $strMetaTitle = sprintf('%s - brak dodanych obrazków', $arrUserAccount['display_name']);
        }
        
        return Response::html($objLayout->render(array(
            'title' => $strMetaTitle
        )));
    }
    
    public function latestStories () {
        $objLayout = new \imagehost2\layout\Standard();
        
        $objListOptions = new ArtifactListOptionsModel(1);
        $objListOptions->setOrder(array(
            ArtifactListOptionsModel::ORDERBY_DATE_DESC
        ));
        $objListOptions->addAndWhere(array('is_imported' => 'false'));
        
        $objLayout->addBoxes('main', array(
            new \imagehost2\box\PageHeader('Ostatnio dodane galerie zdjęć'), 
            new \imagehost2\box\artifact\Stream($objListOptions), 
//            new \imagehost2\box\puppy\TopLayer()
        ));
        $objLayout->addBoxes('main', new \imagehost2\box\puppy\Sticky('sticky'));
        
        return Response::html($objLayout->render(array(
            'title' => 'Ostatnio dodane galerie zdjęć', 
            'robots' => 'noindex, follow', 
            'googlebot' => 'noindex, follow'
        )));
    }
    
    public function latestImported () {
        $objLayout = new \imagehost2\layout\Standard();
        
        $objListOptions = new ArtifactListOptionsModel(1);
        $objListOptions->setOrder(array(
            ArtifactListOptionsModel::ORDERBY_DATE_DESC
        ));
        $objListOptions->addAndWhere(array('is_imported' => 'true'));
        
        
        $objLayout->addBoxes('main', array(
            new \imagehost2\box\PageHeader('Ostatnio dodane galerie Allegro'), 
            new \imagehost2\box\artifact\Stream($objListOptions), 
//            new \imagehost2\box\puppy\TopLayer()
        ));
        $objLayout->addBoxes('main', new \imagehost2\box\puppy\Sticky('sticky'));
        
        return Response::html($objLayout->render(array(
            'title' => 'Ostatnio dodane galerie Allegro', 
            'robots' => 'noindex, follow', 
            'googlebot' => 'noindex, follow'
        )));
    }
    
    public function query($strQuerySlug) {
        $objLayout = new \imagehost2\layout\Standard();
        
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
            new \imagehost2\box\PageHeader(
                $arrQuery['title']
            ), 
            new \imagehost2\box\artifact\Stream($objListOptions)
        ));
        $objLayout->addBoxes('main', new \imagehost2\box\puppy\Sticky('sticky'));
        
        if ($numResultsCount > 0) {
            $strTitle = sprintf('%s (%d znalezionych obrazków)', $arrQuery['title'], $numResultsCount);
        } else {
            $strTitle = sprintf('%s - brak wyników', $arrQuery['title']);
        }
        return Response::html($objLayout->render(array(
            'title' => $strTitle, 
            'robots' => 'noindex, follow', 
            'googlebot' => 'noindex, follow'
        )));
    }
    
    
    
    
    public function tag($strTagSlug) {
        $objLayout = new \imagehost2\layout\Standard();
        
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
            $strTitle = sprintf('%s (%d znalezionych obrazków) - imgED', $arrTag['title'], $arrTag['elements_count']);
            $strDescription = sprintf('Zobacz Galerię: %s (%d obrazków) - imgED', $arrTag['title'], $arrTag['elements_count']);
        } else {
            $strHeader = sprintf('Brak wyników w temacie %s', $arrTag['title']);
            $strTitle = sprintf('%s (brak obrazków) - imgED', $arrTag['title']);
            $strDescription = sprintf('Galeria %s nie zawiera obrazków - imgED', $arrTag['title']);
        }
        
        $objListOptions = new ArtifactListOptionsModel(1);
        $objListOptions->setOrder(array(
            ArtifactListOptionsModel::ORDERBY_DATE_DESC
        ));
        $objListOptions->addTag($arrTag['id']);
        
        $objLayout->addBoxes('main', array(
            new \imagehost2\box\PageHeader($strHeader), 
            new \imagehost2\box\puppy\Area('top-tag'), 
            new \imagehost2\box\artifact\Stream($objListOptions), 
        ));
        $objLayout->addBoxes('main', new \imagehost2\box\puppy\Sticky('sticky'));
        
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