<?php
namespace backend;

use webcitron\Subframe\Application;

class DbFactory extends \webcitron\Subframe\Db 
{
    private static $strDefaultConnection = '';
    
    public static function setDefaultConnection ($strConnectionName = '') {
        self::$strDefaultConnection = $strConnectionName;
    }
    
    public static function getInstance ($strConnectionName = 'default') {
//        if (empty($strConnectionName)) {
//            $strConnectionName = self::recognizeConnection();
//        }
        return parent::getInstance($strConnectionName);
    }
    
//    private static function recognizeConnection () {
//        $strConnectionName = '';
//        if (!empty(self::$strDefaultConnection)) {
//            $strConnectionName = self::$strDefaultConnection;
//        } else {
//            $strCurrentEnvironment = Application::currentEnvironment();
//
//            if ($strCurrentEnvironment === Application::ENVIRONMENT_DEV || $strCurrentEnvironment === Application::ENVIRONMENT_NIGHTLY) {
//                $strConnectionName = 'dev';
//            } else if ($strCurrentEnvironment === Application::ENVIRONMENT_RC) {
//                $strConnectionName = 'rc';
//            } else {
//                $strConnectionName = 'production';
//            }
//        }
//        
//        return $strConnectionName;
//    }
    
}
