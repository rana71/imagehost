<?php namespace backend\adminstats;

use webcitron\Subframe\Controller;



class AdminStatsController extends Controller { 
    
    public static function getStats () {
        $objModel = new model\AdminStatsModel();
        $arrStats = $objModel->getAllStats();
        return parent::answer($arrStats);
    }
    
    public static function refreshUserStats () {
        $objModel = new model\AdminStatsModel();
        $objModel->refreshUserStats();
        $arrStats = $objModel->selectStats(array('active_users', 'inactive_users'));
        
        return parent::answer($arrStats);
    }
    
    public static function refreshArtifactStats () {
        $objModel = new model\AdminStatsModel();
        $objModel->refreshArtifactStats();
        $arrStats = $objModel->selectStats(array('visible_artifacts', 'invisible_artifacts'));
        
        return parent::answer($arrStats);
    }
    
    public static function refreshTagStats () {
        $objModel = new model\AdminStatsModel();
        $objModel->refreshTagStats();
        $arrStats = $objModel->selectStats(array('active_tags', 'inactive_tags'));
        
        return parent::answer($arrStats);
    }
    
    public static function refreshNewsletterStats () {
        $objModel = new model\AdminStatsModel();
        $objModel->refreshNewsletterStats();
        $arrStats = $objModel->selectStats(array('active_newsletter_emails', 'inactive_newsletter_emails'));
        
        return parent::answer($arrStats);
    }
}
    