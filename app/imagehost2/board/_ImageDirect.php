<?php namespace imagehost2\board;

use \webcitron\Subframe\Board;
use \webcitron\Subframe\Response;

class ImageDirect extends Board {
  
    public function index ($strPhotoDirectory, $strSlug, $numId, $strExt) {
        $arrBoxes = array();
        $arrBoxes[] = new \imagehost2\box\files\SaveAndShowImage($strPhotoDirectory, $strSlug, $numId);
//        
        $objLayout = new \imagehost2\layout\Nothing();
        $objLayout->addBoxes('main', $arrBoxes);
        return Response::html($objLayout->render());
    }
    
    
}