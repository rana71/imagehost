<?php
namespace backend;

use webcitron\Subframe\Application;

class ErrorHandler 
{
    
    public static function rpcError($strInfo, $arrCall) {
        $objApp = Application::getInstance();
        
        $objMail = new \backend\SystemMail('rpcapiError');
        $objMail->setVariable('application', $objApp->strName);
        $objMail->setVariable('url', Application::url());
        $objMail->setVariable('pointer', $arrCall['strMethodPointer']);
        $objMail->setVariable('raw', $arrCall['strMethodRawPath']);
        $objMail->setVariable('params', '<pre>'.print_r($arrCall['arrParams'], true).'</pre>');
        $objMail->setVariable('error', $strInfo);
        $objMail->setVariable('date', date('Y-m-d H:i:s'));
        $objMail->addRecipient('a.mackiewicz@webcitron.eu');
        $objMail->send();
    }
    
}
