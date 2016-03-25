<?php namespace imagehost3\box\artifact;

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
    private $strHeader;
    
    public static $numArtifactsPerPage = 15;
    
    
    public function __construct (ArtifactListOptionsModel $objListOptions, $strHeader = '') {
        $this->objListOptions = $objListOptions;
        $this->strHeader = $strHeader;
        
        JsController::runJs();
    }
    
    public function launch () {
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
            'strHeader' => $this->strHeader, 
            'arrList' => $arrItems['result']['arrList'], 
            'boolLoadingEnabled' => $this->objListOptions->boolLoadingEnabled, 
            'strStreamOptionsSerialized' => json_encode($this->objListOptions), 
            'arrViewLayout' => $this->objListOptions->arrViewCellsLayout
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
        $arrResult['strGridHtml'] = $objBlitz->makeGrid($arrItems['result']['arrList'], $objOptions->arrViewCellsLayout);
        $arrResult['numNewItemsCount'] = count($arrItems['result']['arrList']);
        return \webcitron\Subframe\Controller::answer($arrResult);
    }
    
}