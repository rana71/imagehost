<?php namespace backend\newsletter\model;

use \backend\DbFactory;
use webcitron\Subframe\Templater;
use webcitron\Subframe\Config;
use webcitron\Subframe\Application;
use webcitron\Subframe\Url;
 
class NewsletterModel
{

    private $objDb = null;

    public function __construct()
    {
        $this->objDb = DbFactory::getInstance();
    }

    public function existsSubscriber($strEmail)
    {
        $strQ = <<<EOF
SELECT count(1) FROM newsletter.member
WHERE email = :email
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':email' => $strEmail
        ));
        $arrResult = $objSth->fetch(\PDO::FETCH_COLUMN, 0);
        $boolExists = true;
        if(empty($arrResult)){
            $boolExists = false;
        }
        return $boolExists;
    }
    
    public function removeMember ($strEmail) {
        $strQ = "DELETE FROM newsletter.member WHERE email = :email";
        $objSthInsert = $this->objDb->prepare($strQ);
        $objSthInsert->execute(array(
            ':email' => $strEmail
        ));
        return true;
    }

    public function createMember($strEmail, $strHash)
    {
        $boolIsCreated = false;
        $boolIsConfirmed = 0;
        $strQ = <<<EOF
INSERT  INTO newsletter.member (
    email,  hash, is_confirmed
) VALUES (
    :email, :hash, :is_confirmed
) RETURNING email;
EOF;
        $objSthInsert = $this->objDb->prepare($strQ);
        $objSthInsert->execute(array(
            ':email' => $strEmail,
            ':hash' => $strHash,
            ':is_confirmed' => $boolIsConfirmed
        ));
        $strEmailAccount = $objSthInsert->fetch(\PDO::FETCH_COLUMN, 0);
        if(!empty($strEmailAccount)){
            $boolIsCreated = true;
        }
        return $boolIsCreated;
    }

    public function confirmSubscriptionGetEmail($strConfirmationHash) {
        $strQ = <<<EOF
UPDATE newsletter.member
SET is_confirmed = TRUE, 
    hash = NULL 
WHERE hash = :hash 
    AND is_confirmed = FALSE
RETURNING email
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(':hash' => $strConfirmationHash));
        $strEmailSubscribed = $objSth->fetch(\PDO::FETCH_COLUMN, 0);
        
        return $strEmailSubscribed;
    }

    public function getBasicStats () {
        $arrStats = array();
        $strQ = "SELECT COUNT(1) FROM newsletter.member WHERE is_confirmed = true";
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute();
        $numCount = $objSth->fetch(\PDO::FETCH_COLUMN, 0);
        $arrStats['numTotalConfirmed'] = $numCount;
        $strQ = "SELECT COUNT(1) FROM newsletter.member";
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute();
        $numCount = $objSth->fetch(\PDO::FETCH_COLUMN, 0);
        $arrStats['numTotal'] = $numCount;
        return $arrStats;
    }

    public function isSubscribed($strEmail){
        $boolIsSubscribed = false;
        $strQ = <<<EOF
SELECT COUNT(1) from newsletter.member
WHERE email = :email
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(':email' => $strEmail));
        $numCount = $objSth->fetch(\PDO::FETCH_COLUMN, 0);
        if ($numCount > 0){
            $boolIsSubscribed = true;
        }
        return $boolIsSubscribed;
    }

    public function getNewsletterCampaignTemplate ($arrArtifacts, $numArtifactsCount = 0, $numAddedThisMonth = 0, $arrOptions = array()) {
        $arrResult = array('html' => '', 'text' => '');
        $strTemplaterName = Config::get('templater');
        $objTemplater = Templater::createSpecifiedTemplater($strTemplaterName);
        $strCompaignTemplate = $objTemplater->getTemplateFileContent(__DIR__.'/view/NewsletterCampaignTemplate');
        $strCampaignItemTemplate = $objTemplater->getTemplateFileContent(__DIR__.'/view/NewsletterCampaignItemTemplate');
        $strCampaignTextTemplate = $objTemplater->getTemplateFileContent(__DIR__.'/view/NewsletterCampaignTemplatePlaintext');
        
        $strArtifactsHtml = $strArtifactsText = '';
        $strUrlHp = str_replace('admin.', '', Application::url());
                
        foreach ($arrArtifacts as $arrArtifact) {
            $strArtifactUrl = sprintf('%s/%s-%d.html', $strUrlHp, $arrArtifact['slug'], $arrArtifact['id']);
            
            $strArtifactsHtml .= str_replace(array(
                '*artifact-url*', 
                '*artifact-title*', 
                '*artifact-image-url*', 
                '*analytics-params*'
            ), array(
                $strArtifactUrl, 
                $arrArtifact['title'], 
                $arrArtifact['thumb_url'], 
                !empty($arrOptions['analytics_param']) ? $arrOptions['analytics_param'] : ''
            ), $strCampaignItemTemplate);
            
            $strArtifactsText .= $arrArtifact['title']."\n".$strArtifactUrl.'?'.(!empty($arrOptions['analytics_param']) ? $arrOptions['analytics_param'] : '')."\n\n";
        }
        
        $arrResult['html'] = str_replace(array(
            '*analytics-params*', 
            '*artifacts*', 
            '*artifacts-count*', 
            '*uploaded-month*', 
            '*url-hp*'
        ), array(
            !empty($arrOptions['analytics_param']) ? $arrOptions['analytics_param'] : '', 
            $strArtifactsHtml, 
            number_format($numArtifactsCount, 0, ',', ' '), 
            number_format($numAddedThisMonth, 0, ',', ' '), 
            $strUrlHp
        ), $strCompaignTemplate);
        
        $arrResult['text'] = str_replace(array(
            '*analytics-params*', 
            '*artifacts*', 
            '*url-hp*'
        ), array(
            !empty($arrOptions['analytics_param']) ? $arrOptions['analytics_param'] : '', 
            $strArtifactsText, 
            $strUrlHp
        ), $strCampaignTextTemplate);
        
        
        return $arrResult;
    }
}
