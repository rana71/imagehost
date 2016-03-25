<?php namespace backend\artifact;

use webcitron\Subframe\Controller;
use backend\StorageModel;
use backend\artifact\model\MemeBackgroundModel;
//use backend\DbFactory;
//use webcitron\Subframe\Application;
//use webcitron\Subframe\Url;
//use backend\tag\model\TagModel;
//use backend\user\model\UserModel;
//use backend\StorageModel;
//use backend\utils\BBCodeParser;

class MemeBackgroundController extends Controller { 
    
     public static function add ($strFilename, $strBackgroundImageBlob, $strSearchData = null) {
         $arrResults = array();
         $objS3Model = new StorageModel('s3', array(
            'key' => 'AKIAJRIWCGLMNSOD6FOA', 
            'secret' => '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', 
            'region' => 'eu-central-1', 
            'bucket' => 'i1.imged.pl',
            'version' => '2006-03-01'
        ), array(
            'scheme' => 'http'
        ));
        $arrSaveResult = $objS3Model->writeFile($strFilename, $strBackgroundImageBlob);
        if (!empty($arrSaveResult['ObjectURL'])) {
            $arrNameTokens = explode('/', $arrSaveResult['ObjectURL']);
            $strImageFilename = array_pop($arrNameTokens);
            
            $strImagePath = join('/', $arrNameTokens);
            
            $objModel = new MemeBackgroundModel();
            $numBackgroundId = $objModel->insert($strImageFilename, $strImagePath, $strSearchData);
            
            $arrResults['numBackgroundId'] = $numBackgroundId;
        }
        
        return self::answer($arrResults);
     }
     
     
    public static function getMostPopular ($numLimit, $strSearchString = null, $numForceFirstMemeBackgroundId = 0) {
        $arrResult = array();
        $objModel = new model\MemeBackgroundModel();
        if (!empty($strSearchString)) {
            $arrResult['arrBackgroundImages'] = $objModel->getMostPopularBySearchString($strSearchString, $numLimit);
        } else {
            $arrResult['arrBackgroundImages'] = $objModel->getMostPopular($numLimit, $numForceFirstMemeBackgroundId);
        }
        
        return parent::answer($arrResult);
    }
    
    
    
}