<?php
namespace backend\stats;

use webcitron\Subframe\Controller;
use backend\stats\model\StatsModel;
use backend\user\model\UserModel;


class StatsController extends Controller
{
    
    public static function getStatsUserGeneral () {
        $arrResult = array();
        
        $objUserModel = new UserModel();
        $arrAuthorAccount = $objUserModel->getLoggedUser();
        
        $objStatsModel = new StatsModel();
        $arrResult['stats']= $objStatsModel->getUserArtifactsStats($arrAuthorAccount['id']);
        $arrResult['statsType'] = 'general';
        
        return self::answer($arrResult);
    }
    
    public static function getStatsUserArtifact ($numArtifactId) {
        $arrResult = array();
        
        $objUserModel = new UserModel();
        $arrAuthorAccount = $objUserModel->getLoggedUser();
        
        $objStatsModel = new StatsModel();
        $arrResult['stats']= $objStatsModel->getUserArtifactStats($arrAuthorAccount['id'], $numArtifactId);
        $arrResult['statsType'] = 'artifact-'.$numArtifactId;
        
        return self::answer($arrResult);
    }
    
}