<?php
namespace backend\newsletter;

use webcitron\Subframe\Controller;
use backend\newsletter\model\NewsletterModel;
use backend\SystemMail;
use webcitron\Subframe\Url;
use backend\NewsletterWrapper;
use backend\artifact\model\ArtifactModel;
use webcitron\Subframe\Application;

class NewsletterController extends Controller
{
    
    private static $arrNewsletterSystemConfig = array(
        Application::ENVIRONMENT_PRODUCTION => array(
            'strApiKey' => '423517a5ad5e6a0017ccdd8979a6ed38-us1', 
            'strDefaultList' => '757830c648', 
            'strFromName' => 'imgED.pl', 
            'strFromEmail' => 'pl@imged.com'
        ), 
        Application::ENVIRONMENT_RC => array(
            'strApiKey' => '423517a5ad5e6a0017ccdd8979a6ed38-us1', 
            'strDefaultList' => '3b949a3269', 
            'strFromName' => 'imgED.pl', 
            'strFromEmail' => 'pl@imged.com'
        ), 
        Application::ENVIRONMENT_DEV => array(
            'strApiKey' => '423517a5ad5e6a0017ccdd8979a6ed38-us1', 
            'strDefaultList' => '3b949a3269', 
            'strFromName' => 'imgED.pl', 
            'strFromEmail' => 'pl@imged.com'
        )
    );
    
    private static $arrNewsletterTestEmails = array(
        'a.mackiewicz@webcitron.eu', 
        'bberlinski@gmail.com'
    );
    
    public static function getCurrentEnvironmentConfig () {
        $numCurrentEnv = Application::currentEnvironment();
        return self::$arrNewsletterSystemConfig[$numCurrentEnv];
    }
    
    public function removeMember ($strEmail) {
        $objModel = new model\NewsletterModel();
        return $objModel->removeMember($strEmail);
    }
    
    public static function subscribe($strEmail)
    {
        $arrResult = array();
        $arrErrors = array();
        $strEmail = trim(strtolower(stripslashes(strip_tags($strEmail))));
        $strHash = md5($strEmail . time());
        if (!filter_var($strEmail, FILTER_VALIDATE_EMAIL)) {
            $arrErrors[] = 'Podano nieprawidłowy adres e-mail';
        }
        if (empty($arrErrors)) {
            $objModel = new NewsletterModel();
            if (!$objModel->existsSubscriber($strEmail)) {
                $boolIsCreated = $objModel->createMember($strEmail, $strHash);
                if ($boolIsCreated) {
                    $objMail = new SystemMail('SubscriptionConfirmation');
                    $objMail->addRecipient($strEmail, 'Subscriber');
                    $objMail->setVariable('url', Url::route('Newsletter::confirmSubscription', $strHash));
                    $objMail->send();
                    $arrResult[] = 'Twój e-mail został zapisany, jednak by go aktywować musisz kliknąć w linka którego właśnie wysłaliśmy na Twoją skrzynkę e-mail';
                }else{
                    $arrErrors[] = 'Coś poszło nie tak, prosimy spróbować ponownie';
                }
            } else {
                $arrErrors[] = 'Ten adres e-mail jest już zapisany';
            }
        }
        return parent::answer($arrResult, $arrErrors);
    }

    public static function testWrapper () {
        $boolReturn = true;
        try {
            $objNewsletterSystem = new NewsletterWrapper('mailchimp', self::getCurrentEnvironmentConfig());
            $objNewsletterSystem->testConnection();
        } catch (\Exception $e) {
            $boolReturn = false;
        }
        return $boolReturn;
    }
    
    public static function confirmSubscription($strConfirmationHash)
    {
        $objModel = new NewsletterModel();
        $strEmail = $objModel->confirmSubscriptionGetEmail($strConfirmationHash);
        $boolIsOk = false;
        
        if (!empty($strEmail)) {
            $boolIsOk = true;
            $objNewsletterSystem = new NewsletterWrapper('mailchimp', self::getCurrentEnvironmentConfig());
            $objNewsletterSystem->addSubscriberToList(array(
                'strEmail' => $strEmail
            ));
        }
        return parent::answer($boolIsOk);
    }

    public static function getBasicStats()
    {
        $arrResult = array();
        $arrErrors = array();
        $objNewsletterModel = new NewsletterModel();
        $arrNewsletterBasicStats = $objNewsletterModel->getBasicStats();
        $arrResult[] = $arrNewsletterBasicStats;
        return self::answer($arrResult, $arrErrors);
    }

    public static function isSubscribed($strEmail)
    {
        $objNewsletterModel = new NewsletterModel();
        $boolIsSubscribed = $objNewsletterModel->isSubscribed($strEmail);
        return $boolIsSubscribed;
    }
    
    public static function prepareCampaign ($arrArtifactsIds, $strMailSubject) {
        $arrResult = $arrErrors = array();
        if (empty($arrArtifactsIds)) {
            $arrErrors[] = 'Nie podano żadnego artefaktu';
        } else if ($arrArtifactsIds !== array_unique($arrArtifactsIds)) {
            $arrErrors[] = 'Podana lista artefaktów zawiera dupliaty';
        } else {
            $objArtifactModel = new ArtifactModel();
            $arrArtifacts = array();
            foreach ($arrArtifactsIds as $numArtifactId) {
                if (empty($numArtifactId)) {
                    continue;
                }
                $arrArtifact = $objArtifactModel->getBaseInfo($numArtifactId);
                if (empty($arrArtifact)) {
                    $arrErrors[] = 'Nie odnaleziono artefaktu #'.$numArtifactId;
                } else {
                    $arrArtifacts[] = $arrArtifact;
                }
            }
            if (empty($arrErrors)) {
                if (Application::currentEnvironment() === Application::ENVIRONMENT_PRODUCTION) {
                    $strCampaignName = 'ad-'.date('Y-m-d');
                } else {
                    $strCampaignName = 'RC-ad-'.date('Y-m-d');
                }
                
                $arrStats = $objArtifactModel->getStatsStatic();
                $objNewsletterModel = new NewsletterModel();
                $arrNewsletterCampaignContent = $objNewsletterModel->getNewsletterCampaignTemplate($arrArtifacts, $arrStats['items_count'], $arrStats['items_added_this_month'], array(
                    'analytics_param' => 'utm_source=newsletter&utm_medium=mail&utm_campaign='.$strCampaignName
                ));
                
                $arrNewsletterSystemConfig = self::getCurrentEnvironmentConfig();
                $objNewsletter = new NewsletterWrapper('mailchimp', $arrNewsletterSystemConfig);
                $arrCampaign = $objNewsletter->createCampaign($strCampaignName, $strMailSubject, $arrNewsletterCampaignContent, array(
                    'from' => array(
                        'email' => $arrNewsletterSystemConfig['strFromEmail'], 
                        'name' => $arrNewsletterSystemConfig['strFromName']
                    )
                ), array(
                    'list_id' => $arrNewsletterSystemConfig['strDefaultList'], 
                    'folder_id' => (Application::currentEnvironment() === Application::ENVIRONMENT_PRODUCTION) ? 49309 : 49325, 
                    'auto_footer' => false
                ));
                $objNewsletter->sendTestCampaign($arrCampaign['id'], self::$arrNewsletterTestEmails);
                $arrResult['numCampaignId'] = $arrCampaign['id'];
                $arrListDetails = $objNewsletter->getListDetails($arrNewsletterSystemConfig['strDefaultList']);
                $arrResult['numRealMembersCount'] = $arrListDetails['member_count'];
            }
            
        }
        return self::answer($arrResult, $arrErrors);
    }
    
    public static function sendCampaign ($numCampaignId) {
        $arrResult = $arrErrors = array();
        
        try {
            $objNewsletter = new NewsletterWrapper('mailchimp', self::getCurrentEnvironmentConfig());
            $arrCampaign = $objNewsletter->sendCampaign($numCampaignId);
        } catch (Exception $e) {
            $arrErrors[] = 'Wystąpił błąd, spróbuj ponowanie';
        }
        
        
        $arrResult['boolSent'] = !empty($arrCampaign['complete']) ? true : false;
        return self::answer($arrResult, $arrErrors);
    }

}