<?php namespace backend\newsletterwrapper;

class MailChimp implements \backend\INewsletterWrapper {
    
    private $objService;
    
    private $strDefaultList = '';
    
    public function __construct ($arrConfig) {
        $this->objService = new \Mailchimp($arrConfig['strApiKey'], array('ssl_verifypeer' => false));
        if (!empty($arrConfig['strDefaultList'])) {
            $this->strDefaultList = $arrConfig['strDefaultList'];
        }
    }
    
    public function addSubscriberToList ($arrSubscriber, $strList = '') {
        $objMailchimpLists = new \Mailchimp_Lists($this->objService);
        
        $strSelectedList = !empty($strList) ? $strList : $this->strDefaultList;
        $arrSubscribe = $objMailchimpLists->subscribe($strSelectedList, array(
            'email' => $arrSubscriber['strEmail']
        ), array(), false, false, false, false);
        return $arrSubscribe;
    }
    
    public function removeSubscriberFromList ($mulList, $strEmail) {
        
    }
    
    public function testConnection () {
        $objHelpers = new \Mailchimp_Helper($this->objService);
        $arrReturn = $objHelpers->ping();
        return $arrReturn;
    }
    
    public function createCampaign ($strCampaignName, $strMailSubject, $arrContent, $arrCampaignOptions, $arrSpecifiedConfig = array()) {
        $arrReturn = array();
        
        $objCampaigns = new \Mailchimp_Campaigns($this->objService);
        
        $arrOpts = array(
            'list_id' => $arrSpecifiedConfig['list_id'], 
            'subject' => $strMailSubject, 
            'from_email' => $arrCampaignOptions['from']['email'], 
            'from_name' => $arrCampaignOptions['from']['name'], 
            'folder_id' => $arrSpecifiedConfig['folder_id'], 
//            'title' => $strCampaignName, 
            'auto_footer' => $arrSpecifiedConfig['auto_footer'], 
            'inline_css' => true
        );
        
        $boolOk = false;
        $strPostfix = '';
        $numNumber = 0;
        do {
            $boolOk = true;
            try {
                $arrOpts['title'] = $strCampaignName.$strPostfix;
                $arrNewCampaign = $objCampaigns->create('regular', $arrOpts, array(
                    'html' => $arrContent['html'], 
                    'text' => $arrContent['text']
                ));
            } catch (Mailchimp_Invalid_Options $e) {
                $numNumber++;
                $strPostfix = '-'.$numNumber;
                $boolOk = false;
            }
        } while ($boolOk === false);
        
        $arrReturn['id'] = $arrNewCampaign['id'];
        $arrReturn['title'] = $arrNewCampaign['title'];
        $arrReturn['create_time'] = $arrNewCampaign['create_time'];
        $arrReturn['url'] = $arrNewCampaign['archive_url_long'];
        
        return $arrReturn;
    }
    
    public function sendTestCampaign ($mulCampaignId, $arrReceipments) {
        $objCampaigns = new \Mailchimp_Campaigns($this->objService);
        $arrStatus = $objCampaigns->sendTest($mulCampaignId, $arrReceipments);
        
        $boolReturn = !empty($arrStatus['complete']) ? true : false;
        return $boolReturn;
    }
    
    public function getListDetails ($mulListId) {
        $arrReturn = array(
            'id' => '', 
            'members_count' => 0
        );
        
        $objLists = new \Mailchimp_Lists($this->objService);
        $arrList = $objLists->getList(array(
            'list_id' => $mulListId
        ));
        $arrReturn['id'] = $arrList['data'][0]['id'];
        $arrReturn['member_count'] = $arrList['data'][0]['stats']['member_count'];
        
        return $arrReturn;
    }
    
    public function sendCampaign ($mulCampaignId) {
        $objCampaigns = new \Mailchimp_Campaigns($this->objService);
        $arrStatus = $objCampaigns->send($mulCampaignId);
        
        $boolReturn = !empty($arrStatus['complete']) ? true : false;
        return $boolReturn;
    }
    
}

