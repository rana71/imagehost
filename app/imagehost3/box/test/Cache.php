<?php namespace imagehost3\box\test;

use webcitron\Subframe\Response;
use webcitron\Subframe\Application;

class Cache extends \webcitron\Subframe\Box {
    
    public function launch () {
        $strResult = 'UNKNOWN';
        try {
            $objCache = new \backend\Cache();
            $boolResult = $objCache->test();
            if ($boolResult === true) {
                $strResult = 'OK';
            }
        } catch (Exception $e) {
            $strResult = 'ERROR';
        }
        
        return Response::html($this->render(array(
            'strTestName' => __CLASS__, 
            'strResult' => $strResult
        ), 'DefaultView'));   
    }
    
}