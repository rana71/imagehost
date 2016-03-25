<?php namespace imagehost3\box\newsletter;

use webcitron\Subframe\Response;
use webcitron\Subframe\JsController;
use backend\user\UserController;
use backend\newsletter\NewsletterController;

class SmallForm extends \webcitron\Subframe\Box {

    public function __construct () {
        JsController::runJs();
    }

    public function launch () {
        $strEmail = '';
        if (UserController::isLoggedIn()) {
            $objUser = UserController::getLoggedUser();
            if(!NewsletterController::isSubscribed($objUser['email'])){
                $strEmail = $objUser['email'];
            }
        }
        $arrResponse = array(
            'email' => $strEmail
        );
        return Response::html($this->render($arrResponse));
    }

}