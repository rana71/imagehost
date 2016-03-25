<?php namespace imagehost3\box\test;

use webcitron\Subframe\Response;

class DirectoriesPermissions extends \webcitron\Subframe\Box {
    
    private $arrDirs = array(
        '../../../../var/tmp', 
        '../../../../public_html/sitemap.xml', 
        '../../../../public_html/sitemaps-xml', 
    );
    
    public function launch () {
        $strResult = 'OK';
        $strThisDir = dirname(__FILE__);
        foreach ($this->arrDirs as $strDir) {
            $boolIsOk = true;
            if (!file_exists($strThisDir .'/'. $strDir)) {
                $boolIsOk = mkdir($strThisDir .'/'. $strDir);
            }
            
            if ($boolIsOk === true) {
                $boolIsOk = is_writable($strThisDir .'/'. $strDir);
            }
            $strResult = ($boolIsOk === true) ? 'OK' : 'ERROR';
        }
        
        
        return Response::html($this->render(array(
            'strTestName' => __CLASS__, 
            'strResult' => $strResult
        ), 'DefaultView'));   
    }
    
}