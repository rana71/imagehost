<?php namespace imagehost2\board;

use \webcitron\Subframe\Board;
use \webcitron\Subframe\Response;
Use webcitron\Subframe\Redirect;

class Upload extends Board {
  
    public function index() {
        $objLayout = new \imagehost2\layout\Standard();
        
        $objLayout->addBoxes('main', new \imagehost2\box\PageHeader('Dodaj swoje zdjęcia'));
        $objLayout->addBoxes('main', new \imagehost2\box\upload\Form());
        
        return Response::html($objLayout->render(array(
            'title' => 'Dodaj swoje zdjęcia - imgED', 
            'description' => 'Max wielkość pliku = 20 Mb, Max wielkość konta = No limit! Wszystko za Free bez konieczności Rejestracji! Darmowy hosting zdjęć na aukcje Allegro.pl, Ebay.pl i innych platform.', 
            'robots' => 'index, follow, archive', 
            'googlebot' => 'index, follow, archive, snippet'
        )));
    }
    
    public function memeGenerator () {
        $objLayout = new \imagehost2\layout\Standard();
        
        $objLayout->addBoxes('main', new \imagehost2\box\PageHeader('Stwórz swojego mema'));
        $objLayout->addBoxes('main', new \imagehost2\box\upload\Form('mem'));
        
        return Response::html($objLayout->render(array(
            'title' => 'Stwórz swojego mema - imgED', 
            'description' => 'Max wielkość pliku = 20 Mb, Max wielkość konta = No limit! Wszystko za Free bez konieczności Rejestracji! Darmowy hosting zdjęć na aukcje Allegro.pl, Ebay.pl i innych platform.', 
            'robots' => 'index, follow, archive', 
            'googlebot' => 'index, follow, archive, snippet'
        )));
    }
}