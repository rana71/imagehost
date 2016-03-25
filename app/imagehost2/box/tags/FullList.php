<?php namespace imagehost2\box\tags;

use webcitron\Subframe\Response;
use backend\tag\model\TagModel;

class FullList extends \webcitron\Subframe\Box {
    
    
    public function launch () {
        $objModel = new TagModel();
        $arrTags = $objModel->getBiggest(120);
        return Response::html($this->render(array(
            'arrTags' => $arrTags
        )));
    }
    
}