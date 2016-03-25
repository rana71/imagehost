<?php namespace imagehost3\board;

use \webcitron\Subframe\Board;
use backend\newsletter\NewsletterController;
use \webcitron\Subframe\Redirect;
use \webcitron\Subframe\Url;

class Newsletter extends Board {

    public function confirmSubscription ($strConfirmationHash) {
        $objCtr = new NewsletterController();
        $boolIsOk = $objCtr->confirmSubscription($strConfirmationHash)['result'];
        $strTargetUrl = Url::route('Homepage');
        if ($boolIsOk === true) {
            $strTargetUrl .= '#subscription-ok';
        } else {
            $strTargetUrl .= '#subscription-nok';
        }
        Redirect::url($strTargetUrl);
    }

}