<?php namespace imagehost3\box\test;

use webcitron\Subframe\Response;
use backend\DbFactory;

class Database extends \webcitron\Subframe\Box {
    
    public function launch () {
        $strResult = 'UNKNOWN';
        try {
            $objDb = DbFactory::getInstance();
            $objDb->exec("SET SCHEMA 'artifacts'");
            $strResult = 'OK';
        } catch (Exception $e) {
            $strResult = 'ERROR';
        }
        
        return Response::html($this->render(array(
            'strTestName' => __CLASS__, 
            'strResult' => $strResult
        ), 'DefaultView'));   
    }
    
}