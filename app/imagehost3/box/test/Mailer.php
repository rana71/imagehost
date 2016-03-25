<?php namespace imagehost3\box\test;

use webcitron\Subframe\Response;
use backend\SystemMail;

class Mailer extends \webcitron\Subframe\Box {
    
    public function launch () {
        $strResult = 'UNKNOWN';
        try {
            $objSystemMail = new SystemMail();
            $boolIsOk = $objSystemMail->testSmtp();
            if ($boolIsOk === true) {
                $strResult = 'OK';
            } else {
                $strResult = 'ERROR';
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