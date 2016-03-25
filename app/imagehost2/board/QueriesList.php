<?php namespace imagehost2\board;

use \webcitron\Subframe\Board;
use \webcitron\Subframe\Response;

class QueriesList extends Board {
  
    public function index() {
        $objLayout = new \imagehost2\layout\Standard();
        
        
        $objLayout->addBoxes('main', array(
            new \imagehost2\box\PageHeader('Najnowsze wyszukiwania'), 
            new \imagehost2\box\searcher\QueriesList()
        ));
        
        return Response::html($objLayout->render(array(
            'title' => 'Najnowsze wyszukiwania', 
            'robots' => 'noindex, follow', 
            'googlebot' => 'noindex, follow'
        )));
    }
    
    
}