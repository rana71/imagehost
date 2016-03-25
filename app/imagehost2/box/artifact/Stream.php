<?php namespace imagehost2\box\artifact;

use webcitron\Subframe\Response;
use webcitron\Subframe\JsController;
use webcitron\Subframe\Application;
use backend\artifact\ArtifactListController;
use backend\artifact\model\ArtifactListOptionsModel;

class Stream extends \webcitron\Subframe\Box {
    
    private $arrStreamOptions = array(
        'strSearchQuery' => '', 
        'numTagId' => 0, 
        'numUserId' => 0
    );
    private $boolShowLoader;
    
    public static $numArtifactsPerPage = 30;
    
//    public function __construct ($arrStreamOptions = array(), $boolShowLoader = true) {
//        $this->arrStreamOptions = $arrStreamOptions;
//        $this->boolShowLoader = $boolShowLoader;
//    }
    
    public function __construct (ArtifactListOptionsModel $objListOptions) {
        $this->objListOptions = $objListOptions;
    }
    
    public function launch () {
        JsController::runJs();
        
//        $numArtifactsLimit = -1;
        
        $objListCtr = new ArtifactListController();
        $arrItems = $objListCtr->getList($this->objListOptions);
        
        $numCurrentEnv = Application::currentEnvironment();
        if ($numCurrentEnv !== Application::ENVIRONMENT_PRODUCTION) {
            foreach ($arrItems['result']['arrList'] as & $arrItem) {
                if (substr($arrItem['thumb_url'], 0, 1) === '/') {
                    $arrItem['thumb_url'] = 'http://imged.pl'.$arrItem['thumb_url'];
                }
            }
        }
        
        $arrData = array(
            'arrList' => $arrItems['result']['arrList'], 
            'strStreamOptionsSerialized' => json_encode($this->objListOptions)
        );
        return Response::html($this->render($arrData));
    }
    
    
    public static function getGrid ($strOptionsSerialized, $numLoadedPages) {
        $numPageToLoad = $numLoadedPages+1;
        $objOptions = new ArtifactListOptionsModel($numPageToLoad);
        $objOptions->initializeWithJson($strOptionsSerialized);
        
        $objListCtr = new ArtifactListController();
        $arrItems = $objListCtr->getList($objOptions);
        
        $numCurrentEnv = Application::currentEnvironment();
        if ($numCurrentEnv !== Application::ENVIRONMENT_PRODUCTION) {
            foreach ($arrItems['result']['arrList'] as & $arrItem) {
                if (substr($arrItem['thumb_url'], 0, 1) === '/') {
                    $arrItem['thumb_url'] = 'http://imged.pl'.$arrItem['thumb_url'];
                }
            }
        }
        
        \webcitron\Subframe\Templater\Blitz::getInstance();
        $objBlitz = new \webcitron\Subframe\Templater\SubBlitz();
        $arrResult['strGridHtml'] = $objBlitz->makeGrid($arrItems['result']['arrList']);
        $arrResult['numNewItemsCount'] = count($arrItems['result']['arrList']);
        
        return \webcitron\Subframe\Controller::answer($arrResult);
    }
    
}