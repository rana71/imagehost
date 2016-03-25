<?php namespace admin\board;

use \webcitron\Subframe\Board;
use \webcitron\Subframe\Response;

class Error extends Board {
  
    public function notFound() {
        
        $objLayout = new \imagehost2\layout\Standard();
        $objLayout->addBoxes('topbar', array(
            new \imagehost2\box\Topbar()
        ));
        $objLayout->addBoxes('main', array(
            new \imagehost2\box\artifact\NotFound()
        ));
        
        
        $objResponse = Response::html($objLayout->render(array(
            'title' => 'Nie odnaleziono strony - imgED', 
            
        )));
        $objResponse->setStatus(404);
        return $objResponse;
        
    }
    
}