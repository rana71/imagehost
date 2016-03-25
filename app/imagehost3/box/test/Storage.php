<?php namespace imagehost3\box\test;

use webcitron\Subframe\Response;
use backend\StorageModel;

class Storage extends \webcitron\Subframe\Box {
    
    public function launch () {
        $strResult = 'UNKNOWN';
        try {
            $objS3Model = new StorageModel('s3', array(
                'key' => 'AKIAJRIWCGLMNSOD6FOA', 
                'secret' => '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', 
                'region' => 'eu-central-1', 
                'bucket' => 'i.imged.pl',
                'version' => '2006-03-01'
            ), array(
                'scheme' => 'http'
            ));
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