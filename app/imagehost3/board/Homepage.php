<?php namespace imagehost3\board;

use \webcitron\Subframe\Board;
use \webcitron\Subframe\Response;
use backend\artifact\model\ArtifactListOptionsModel;

class Homepage extends Board {
  
    public function index() {
        $objLayout = new \imagehost3\layout\Standard();
        
        $objListOptions = new ArtifactListOptionsModel(1);
        $objListOptions->setGlobalLimit(10);
        $objListOptions->setLimit(10);
        $objListOptions->disableLoading();
        $objListOptions->setOrder(array(
            ArtifactListOptionsModel::ORDERBY_ON_HOMEPAGE, 
            ArtifactListOptionsModel::ORDERBY_DATE_DESC
        ));
        $objListOptions->addAndWhere(array('is_on_homepage' => 'true', 'is_imported' => 'false'));
        
        $objLayout->addBoxes('top', new \imagehost3\box\Topbar());
        
        $objLayout->addBoxes('main', new \imagehost3\box\Welcome());
        $objLayout->addBoxes('main', new \imagehost3\box\puppy\Area('top-hp'));
        $objLayout->addBoxes('main', new \imagehost3\box\artifact\Stream($objListOptions));
        $objLayout->addBoxes('main', new \imagehost3\box\artifact\GoToTopButton());
        $objLayout->addBoxes('main', new \imagehost3\box\puppy\Sticky('sticky'));
        $objLayout->addBoxes('foot', new \imagehost3\box\Footer());
        $objLayout->addBoxes('foot', new \imagehost3\box\PuppiesDetector());
        
        return Response::html($objLayout->render(array(
            'title' => 'imgED - Jeden obraz wart jest więcej niż tysiąc słów!', 
            'description' => 'Max wielkość pliku = 20 Mb, Max wielkość konta = No limit! Wszystko za Free bez konieczności Rejestracji! Darmowy hosting zdjęć na aukcje Allegro.pl, Ebay.pl i innych platform.', 
            'robots' => 'index, follow, archive', 
            'googlebot' => 'index, follow, archive, snippet'
        )));
    }
    
    
}