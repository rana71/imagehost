<?php namespace backend;


class NewsletterWrapper implements INewsletterWrapper {
    
    private $objService;

    public function __construct($strService, $arrConfig)
    {
        try {
            switch($strService)
            {
                case 'mailchimp':
                    $this->objService = new \backend\newsletterwrapper\MailChimp($arrConfig);
                    break;
                default:
                    $this->objService = new \backend\newsletterwrapper\MailChimp($arrConfig);
            }
        } catch (Exception $e) {}
    }
    
    public function addSubscriberToList ($arrSubscriber, $strList = '') {
        $mulReturn = null;
        try {
            $mulReturn =  $this->objService->addSubscriberToList($arrSubscriber, $strList = '');
        } catch (Exception $e) {}
        
        return $mulReturn;
    }
    
    public function removeSubscriberFromList ($arrSubscriber, $strList = '') {
        $mulReturn = null;
        try {
            $mulReturn =  $this->objService->removeSubscriberFromList($arrSubscriber, $strList = '');
        } catch (Exception $e) {}
        
        return $mulReturn;
    }
    
    public function createCampaign ($strCampaignName, $strMailSubject, $arrContent, $arrCampaignOptions, $arrSpecifiedConfig = array()) {
        $mulReturn = null;
        try {
            $mulReturn =  $this->objService->createCampaign($strCampaignName, $strMailSubject, $arrContent, $arrCampaignOptions, $arrSpecifiedConfig);
        } catch (Exception $e) {}
        
        return $mulReturn;
    }
    
    
    public function sendTestCampaign ($mulCampaignId, $arrReceipments) {
        $mulReturn = null;
        try {
            $mulReturn =  $this->objService->sendTestCampaign ($mulCampaignId, $arrReceipments);
        } catch (Exception $e) {}
        
        return $mulReturn;
    }
    
    public function getListDetails ($mulCampaignId) {
        $mulReturn = null;
        try {
            $mulReturn =  $this->objService->getListDetails ($mulCampaignId);
        } catch (Exception $e) {}
        
        return $mulReturn;
    }
    
    public function sendCampaign ($mulCampaignId) {
        $mulReturn = null;
        try {
            $mulReturn =  $this->objService->sendCampaign ($mulCampaignId);
        } catch (Exception $e) {}
        
        return $mulReturn;
    }
    
    public function testConnection () {
        $mulReturn = null;
        try {
            $mulReturn =  $this->objService->testConnection ();
        } catch (Exception $e) {}
        
        return $mulReturn;
    }
}

interface INewsletterWrapper {
    
    public function addSubscriberToList ($mulList, $strEmail);
    
    public function removeSubscriberFromList ($mulList, $strEmail);
    
    public function createCampaign ($strCampaignName, $strMailSubject, $arrContent, $arrCampaignOptions, $arrSpecifiedConfig = array());
    
    public function sendTestCampaign ($mulCampaignId, $arrReceipments);
    
    public function getListDetails ($mulListId);
    
    public function sendCampaign ($mulCampaignId);
    
    public function testConnection ();
    
}