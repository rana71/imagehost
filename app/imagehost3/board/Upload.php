<?php namespace imagehost3\board;

use \webcitron\Subframe\Board;
use \webcitron\Subframe\Response;
use \webcitron\Subframe\CssController;

class Upload extends Board {
    
    public function __construct () {
        $objCssController = CssController::getInstance();
        $objCssController->forceCssFile('Upload');
    }
  
    public function index() {
        $objLayout = new \imagehost3\layout\Standard();
        
//        $objLayout->addBoxes('main', new \imagehost2\box\PageHeader('Dodaj swoje zdjęcia'));
        $objLayout->addBoxes('main', new \imagehost3\box\upload\Form(null, 'Dodaj'));
        $objLayout->addBoxes('top', new \imagehost3\box\Topbar());
        $objLayout->addBoxes('foot', new \imagehost3\box\Footer());
        $objLayout->addBoxes('foot', new \imagehost3\box\PuppiesDetector());
        
        return Response::html($objLayout->render(array(
            'title' => 'Dodaj swoje zdjęcia, filmy, video lub memy - imgED', 
            'description' => 'Max wielkość pliku = 20 Mb, Max wielkość konta = No limit! Wszystko za Free bez konieczności Rejestracji! Darmowy hosting zdjęć na aukcje Allegro.pl, Ebay.pl i innych platform.', 
            'robots' => 'index, follow, archive', 
            'googlebot' => 'index, follow, archive, snippet'
        )));
    }
    
    public function memeGenerator () {
        $objLayout = new \imagehost3\layout\Standard();
        
//        $objLayout->addBoxes('main', new \imagehost2\box\PageHeader('Stwórz swojego mema'));
        $objLayout->addBoxes('main', new \imagehost3\box\upload\Form('mem', 'Stwórz swojego mema'));
        $objLayout->addBoxes('top', new \imagehost3\box\Topbar());
        $objLayout->addBoxes('foot', new \imagehost3\box\Footer());
        $objLayout->addBoxes('foot', new \imagehost3\box\PuppiesDetector());
        
        return Response::html($objLayout->render(array(
            'title' => 'Stwórz swojego mema - imgED', 
            'description' => 'Max wielkość pliku = 20 Mb, Max wielkość konta = No limit! Wszystko za Free bez konieczności Rejestracji! Darmowy hosting zdjęć na aukcje Allegro.pl, Ebay.pl i innych platform.', 
            'robots' => 'index, follow, archive', 
            'googlebot' => 'index, follow, archive, snippet'
        )));
    }
}