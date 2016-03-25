<?php namespace backend\puppy;

use webcitron\Subframe\Controller;

class PuppyController extends Controller
{
    
    
    public static function savePuppyInArea ($strPuppyCode, $strAreaId) {
        
        $arrReturn = array(
            'arrErrors' => array(), 
            'arrResult' => array()
        );
        $objModel = new model\PuppyModel();
        $objModel->savePuppy($strPuppyCode, $strAreaId);

        $arrReturn['strSavedAreaId'] = $strAreaId;
        return self::answer($arrReturn);
    }
    
    public static function clearArea ($strAreaId) {
        $arrReturn = array(
            'arrErrors' => array(), 
            'arrResult' => array()
        );
        $objModel = new model\PuppyModel();
        $objModel->clearArea($strAreaId);
        $arrReturn['strClearedAreaId'] = $strAreaId;
        return self::answer($arrReturn);
    }
}