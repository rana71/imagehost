<?php namespace imagehost2\box\searcher;

use webcitron\Subframe\Response;
use backend\searcher\model\QueryModel;

class QueriesList extends \webcitron\Subframe\Box {
    
    
    public function launch () {
        $objModel = new QueryModel();
        $arrQueries = $objModel->getLastUsed(120);
        return Response::html($this->render(array(
            'arrQueries' => $arrQueries
        )));
    }
    
}