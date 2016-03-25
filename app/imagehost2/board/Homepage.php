<?php namespace imagehost2\board;

use \webcitron\Subframe\Board;
use \webcitron\Subframe\Response;
use backend\artifact\model\ArtifactListOptionsModel;

class Homepage extends Board {
  
    public function index() {
        $objLayout = new \imagehost2\layout\Standard();
        
        $objListOptions = new ArtifactListOptionsModel(1);
        $objListOptions->setGlobalLimit(300);
        $objListOptions->setOrder(array(
            ArtifactListOptionsModel::ORDERBY_ON_HOMEPAGE, 
            ArtifactListOptionsModel::ORDERBY_DATE_DESC
        ));
        $objListOptions->addAndWhere(array('is_on_homepage' => 'true', 'is_imported' => 'false'));
        
        
        $objLayout->addBoxes('main', new \imagehost2\box\puppy\Area('top-hp'));
        $objLayout->addBoxes('main', new \imagehost2\box\artifact\Stream($objListOptions));
        $objLayout->addBoxes('main', new \imagehost2\box\puppy\Sticky('sticky'));
        
        return Response::html($objLayout->render(array(
            'title' => 'imgED - Jeden obraz wart jest więcej niż tysiąc słów!', 
            'description' => 'Max wielkość pliku = 20 Mb, Max wielkość konta = No limit! Wszystko za Free bez konieczności Rejestracji! Darmowy hosting zdjęć na aukcje Allegro.pl, Ebay.pl i innych platform.', 
            'robots' => 'index, follow, archive', 
            'googlebot' => 'index, follow, archive, snippet'
        )));
    }
    
    
}