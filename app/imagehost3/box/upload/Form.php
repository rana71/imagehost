<?php namespace imagehost3\box\upload;

use webcitron\Subframe\Response;
use webcitron\Subframe\JsController;
use webcitron\Subframe\CssController;
use webcitron\Subframe\Request;
use backend\user\model\UserModel;

class Form extends \webcitron\Subframe\Box {
    
    private $strDefaultItemType = '';
    private $strBoxHeader = '';
    
    public function __construct ($strDefaultItemType = '', $strBoxHeader = '') {
        $this->strDefaultItemType = $strDefaultItemType;
        $this->strBoxHeader = $strBoxHeader;
        
        CssController::addStylesheets('upload');
        JsController::runJs();
    }
    
    public function launch () {
        $objRequest = Request::getInstance();
        $arrServer = $objRequest->getServerInfo();
        $strClientIp = '';
        if (!empty($arrServer['HTTP_CF_CONNECTING_IP'])) {
            $strClientIp = $arrServer['HTTP_CF_CONNECTING_IP'];
        } else if (!empty($arrServer['HTTP_X_FORWARDED_FOR'])) {
            $strClientIp = $arrServer['HTTP_X_FORWARDED_FOR'];
        } else if (!empty($arrServer['REMOTE_ADDR'])) {
            $strClientIp = $arrServer['REMOTE_ADDR'];
        }
        
        $objUserModel = new UserModel();
        $boolIsAllowToUpload = $objUserModel->isIpAllowedToUpload($strClientIp);
        
        return Response::html($this->render(array(
            'strDefaultItemType' => $this->strDefaultItemType, 
            'strBoxHeader' => $this->strBoxHeader, 
            'strClientIp' => $strClientIp
        ), ($boolIsAllowToUpload === false ? 'Disabled' : null)));
    }
    
}