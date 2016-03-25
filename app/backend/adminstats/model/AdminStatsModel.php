<?php namespace backend\adminstats\model;

use backend\DbFactory;

class AdminStatsModel
{

    private $objDb = null;
    
    private $strUpdatePattern = "UPDATE public.admin_stats SET value = (%s), refresh_timestamp = CURRENT_TIMESTAMP WHERE type = :type";
    private $strSelectPattern = "SELECT * FROM public.admin_stats WHERE type IN (%s)";

    public function __construct()
    {
        $this->objDb = DbFactory::getInstance();
    }
    
    public function getAllStats () {
        $arrResult = array(
            'active_users' => array(), 
            'inactive_users' => array(), 
            'visible_artifacts' => array(), 
            'invisible_artifacts' => array(), 
            'active_tags' => array(), 
            'inactive_tags' => array(), 
            'active_newsletter_emails' => array(), 
            'inactive_newsletter_emails' => array(), 
        );
        $strQ = 'SELECT * FROM public.admin_stats';
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute();
        $arrSet = $objSth->fetchAll();
        if (!empty($arrSet)) {
            foreach ($arrSet as $arrRow) {
                $strIndex = $arrRow['type'];
                $arrResult[$strIndex] = array(
                    'numValue' => intval($arrRow['value']), 
                    'strLastRefreshDate' => date('d.m.Y H:i', strtotime($arrRow['refresh_timestamp']))
                );
            }
        }
        
        return $arrResult;
    }
    
    public function refreshUserStats () {
        $strQ = sprintf($this->strUpdatePattern, 
            'SELECT count(1) FROM users.account WHERE is_email_confirmed = TRUE'
        );
        $strType = 'active_users';
        $objSth = $this->objDb->prepare($strQ);
        $objSth->bindValue(':type', $strType, \PDO::PARAM_STR);
        $objSth->execute();
        
        $strQ2 = sprintf($this->strUpdatePattern, 
            'SELECT count(1) FROM users.account WHERE is_email_confirmed = FALSE'
        );
        $strType2 = 'inactive_users';
        $objSth2 = $this->objDb->prepare($strQ2);
        $objSth2->bindValue(':type', $strType2, \PDO::PARAM_STR);
        $objSth2->execute();
        
        return true;
    }
    
    public function refreshArtifactStats () {
        $strQ = sprintf($this->strUpdatePattern, 
            'SELECT count(1) FROM artifacts.item WHERE is_removed = FALSE AND is_public = TRUE'
        );
        $strType = 'visible_artifacts';
        $objSth = $this->objDb->prepare($strQ);
        $objSth->bindValue(':type', $strType, \PDO::PARAM_STR);
        $objSth->execute();
        
        $strQ2 = sprintf($this->strUpdatePattern, 
            'SELECT count(1) FROM artifacts.item WHERE is_removed = TRUE OR is_public = FALSE'
        );
        $strType2 = 'invisible_artifacts';
        $objSth2 = $this->objDb->prepare($strQ2);
        $objSth2->bindValue(':type', $strType2, \PDO::PARAM_STR);
        $objSth2->execute();
        
        return true;
    }
    
    public function refreshTagStats () {
        $strQ = sprintf($this->strUpdatePattern, 
            'SELECT count(1) FROM artifacts.tag WHERE is_removed = FALSE'
        );
        $strType = 'active_tags';
        $objSth = $this->objDb->prepare($strQ);
        $objSth->bindValue(':type', $strType, \PDO::PARAM_STR);
        $objSth->execute();
        
        $strQ2 = sprintf($this->strUpdatePattern, 
            'SELECT count(1) FROM artifacts.tag WHERE is_removed = TRUE'
        );
        $strType2 = 'inactive_tags';
        $objSth2 = $this->objDb->prepare($strQ2);
        $objSth2->bindValue(':type', $strType2, \PDO::PARAM_STR);
        $objSth2->execute();
        
        return true;
    }
    
    public function refreshNewsletterStats () {
        $strQ = sprintf($this->strUpdatePattern, 
            'SELECT count(1) FROM newsletter.member WHERE is_confirmed = TRUE'
        );
        $strType = 'active_newsletter_emails';
        $objSth = $this->objDb->prepare($strQ);
        $objSth->bindValue(':type', $strType, \PDO::PARAM_STR);
        $objSth->execute();
        
        $strQ2 = sprintf($this->strUpdatePattern, 
 'SELECT count(1) FROM newsletter.member WHERE is_confirmed = FALSE'
        );
        $strType2 = 'inactive_newsletter_emails';
        $objSth2 = $this->objDb->prepare($strQ2);
        $objSth2->bindValue(':type', $strType2, \PDO::PARAM_STR);
        $objSth2->execute();
        
        return true;
    }
    
    public function selectStats ($arrStatsTypes) {
        $arrResult = array();
        $strQ = sprintf($this->strSelectPattern, 
            "'" . join("', '", $arrStatsTypes) . "'"
        );
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute();
        $arrSet = $objSth->fetchAll();
        foreach ($arrSet as $arrRow) {
            $arrResult[$arrRow['type']] = array(
                'numValue' => intval($arrRow['value']), 
                'strLastRefreshDate' => date('d.m.Y H:i', strtotime($arrRow['refresh_timestamp']))
            );
        }
        
        return $arrResult;
    }
    

}

