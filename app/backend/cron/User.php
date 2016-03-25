<?php
namespace backend\cron;

use backend\user\model\UserModel;

class User {
   
    public function sendConfirmationLink () {
        
        $objModel = new UserModel();
        $arrAccounts = $objModel->getUncofirmedUsers();
        $numSent = 0;
        foreach ($arrAccounts as $arrUser) {
            if (empty($arrUser['email_confirmation_hash'])) {
                $strActivationHash = md5($arrUser['email'] . $arrUser['display_name'] . time());
                $objModel->markEmailConfirmProcessStarted($arrUser['id'], $strActivationHash);
            } else {
                $strActivationHash = $arrUser['email_confirmation_hash'];
            }
            $objMail = new \backend\SystemMail('AccountActivation');
            $objMail->addRecipient($arrUser['email'], $arrUser['display_name']);
            $objMail->setVariable('display_name', $arrUser['display_name']);
            $objMail->setVariable('url', \webcitron\Subframe\Url::route('User::accountActivation', $strActivationHash));
            $boolOk = $objMail->send();
            if ($boolOk === true) {
                $numSent++;
            }
        }
        echo 'wyslano '.$numSent.' wiadomosci'.PHP_EOL;
    }
    
    
}