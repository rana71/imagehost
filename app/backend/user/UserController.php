<?php
namespace backend\user;

use webcitron\Subframe\Controller;
use backend\DbFactory;
use webcitron\Subframe\Application;
use backend\user\model\UserModel;
use backend\artifact\model\ArtifactModel;
use webcitron\Subframe\Redirect;
use backend\Session;
use backend\SystemMail;
use backend\newsletter\model\NewsletterModel;
use webcitron\Subframe\Url;


class UserController extends Controller
{

    public static function signInWithFacebook($strFacebookId, $strEmail = '', $strFacebookName = '')
    {

        $arrResult = array();
        $arrErrors = array();
        $objModel = new model\UserModel();

        // check i suser already connected ..
        $numAccountId = $objModel->getAccountIdByFacebookId($strFacebookId);
        if (empty($numAccountId)) {
            if (!empty($strEmail)) {
                $arrAccount = $objModel->getAccountByEmail($strEmail);
            }
            if (!empty($arrAccount)) {
                $numAccountId = $arrAccount['id'];
            } else {
                $strUsername = $objModel->genreAutoUsername($strFacebookName);
                $numLoginId = $objModel->createLoginByFacebook($strFacebookId);
                $numAccountId = $objModel->createAccount($numLoginId, $strEmail, $strUsername);
                $objModel->confirmEmailByAccountId($numAccountId);
            }
        }
        $objModel->loginAccount($numAccountId);
        $arrResult['strRedirectTo'] = \webcitron\Subframe\Url::route('User::myUploads', array('sort' => 'najnowsze'));

        return parent::answer($arrResult, $arrErrors);
    }

    public static function getListAdmin($numLimit, $strSearchQuery = '')
    {
        $objModel = new model\UserModel();
        $arrList = $objModel->getListAdmin($numLimit, $strSearchQuery);
        if (!empty($arrList)) {
            $strDomain = str_replace('admin.', '', Application::url());
            foreach ($arrList as & $arrUser) {
                $arrUser['strProfileUrl'] = $strDomain . '/u/' . $arrUser['display_name'] . '/';
            }
        }
        return parent::answer($arrList);
    }

    public static function changePassword($strCurrentPasswordHash, $strNewPasswordHash)
    {
        $arrErrors = array();
        $arrResult = array();

        $objModel = new model\UserModel();

        $objSession = \backend\Session::getInstance('imagehost_user');
        $numLoggedAccountId = $objSession->getValue('imagehost_account_id');

        if ($numLoggedAccountId > 0) {
            $objModel = new model\UserModel();
            $arrCreditentials = $objModel->getAccountCreditentials($numLoggedAccountId);
            if (\backend\Password::isVerifyPassword($arrCreditentials['password'], $strCurrentPasswordHash) === false) {
                $arrErrors[] = 'Podano nieprawidłowe aktualne hasło do konta.';
            } else {
                $boolResult = $objModel->changeInternalLoginPassword($arrCreditentials['internal_login_id'], $strNewPasswordHash);
                if ($boolResult === true) {
                    $arrResult[] = 'Gratulacje, Twoje hasło zostało zmienione';
                } else {
                    $arrErrors[] = 'Napotkano błąd podczas zmiany hasła. Prosimy spróbować ponownie.';
                }
            }
        }
        return self::answer($arrResult, $arrErrors);
    }

    public static function addDisabledUploadIp ($strIp) {
        $objModel = new model\UserModel();
        $arrResult = $objModel->addDisabledUploadIp($strIp);
        
        return self::answer($arrResult);
    }
    
    public static function getDisabledUpload ($numLimit = 3, $strSearchString = '') {
        $objModel = new model\UserModel();
        $arrResult = $objModel->getBlockedUploadIps($strSearchString, $numLimit);
        
        return self::answer($arrResult);
    }
    
    public static function removeDisabledUpload ($numBlockId) {
        $objModel = new model\UserModel();
        $arrResult = $objModel->removeBlockedUploadId($numBlockId);
        
        return self::answer($arrResult);
    }
    

    public static function changeEmail($strPassword, $strNewEmail)
    {
        $arrResponse = array();
        $arrErrors = array();

        $objSession = \backend\Session::getInstance('imagehost_user');
        $numLoggedAccountId = $objSession->getValue('imagehost_account_id');

        if (!filter_var($strNewEmail, FILTER_VALIDATE_EMAIL)) {
            $arrErrors[] = 'Podano nieprawidłowy adres e-mail';
        } else if ($numLoggedAccountId > 0) {
            $objModel = new model\UserModel();
            $arrCreditentials = $objModel->getAccountCreditentials($numLoggedAccountId);
            if (\backend\Password::isVerifyPassword($arrCreditentials['password'], $strPassword) === false) {
                $arrErrors[] = 'Podano nieprawidłowe aktualne hasło do konta.';
            } else {
                $arrAvailability = $objModel->isCreditentialsAvailable($strNewEmail, $arrCreditentials['username'], $numLoggedAccountId);
                if (!empty($arrAvailability)) {
                    $arrErrors[] = 'Podany adres e-mail jest już zajęty';
                } else {
                    $boolResult = $objModel->changeAccountData($numLoggedAccountId, array(
                        'email' => $strNewEmail
                    ));
                    if ($boolResult === true) {
                        $arrUser = $objModel->getAccountUser($numLoggedAccountId);
                        $strActivationHash = md5($arrUser['email'] . $arrUser['display_name'] . time());
                        $objModel->markEmailConfirmProcessStarted($numLoggedAccountId, $strActivationHash);

                        $objMail = new \backend\SystemMail('ChangeEmail');
                        $objMail->addRecipient($strNewEmail, $arrUser['display_name']);
                        $objMail->setVariable('username', $arrUser['username']);
                        $objMail->setVariable('url', \webcitron\Subframe\Url::route('User::accountActivation', array(
                            'activation_hash' => $strActivationHash
                        )));
                        $objMail->send();

                        $objSession->destroy();
                        $arrResponse[] = 'Twój adres e-mail został zmieniony. Zostaniesz wylogowany ze swojego konta. Potwierdź nowy adres e-mail klikając w link aktywacyjny we wiadomości którą właśnie wysłaliśmy na Twój nowy adres e-mail.';
                    } else {
                        $arrErrors[] = 'Napotkano błąd podczas zmiany adresu e-mail. Prosimy spróbować ponownie.';
                    }
                }
            }
        }
        return self::answer($arrResponse, $arrErrors);
    }

    public static function removeLoggedUserArtifact($numArtifactId)
    {
        $arrErrors = array();
        $arrResult = array();

        $objUserModel = new UserModel();
        $arrCurrentAccount = $objUserModel->getLoggedUser();
        if (empty($arrCurrentAccount)) {
            Redirect::route('User::login');
        }

        $objArtifactModel = new ArtifactModel;
        $arrArtifactToDelete = $objArtifactModel->getBaseInfo($numArtifactId);

        if ($arrArtifactToDelete['author_account_id'] !== $arrCurrentAccount['id']) {
            $arrErrors[] = 'Authorize error';
        } else {
            $objArtifactModel->markAsRemoved($numArtifactId);
            $arrResult['numRemovedArtifactId'] = $numArtifactId;
        }

        return self::answer($arrResult, $arrErrors);
    }


    public static function getById($numUserId, $arrFields)
    {
        try {
            $db = DbFactory::getInstance();
            $strQ = sprintf('SELECT %s FROM users WHERE user_id = :user_id', join(', ', $arrFields));
            $stmt = $db->prepare($strQ);
            $stmt->execute(array(
                'user_id' => $numUserId,
            ));
            $arrUser = $stmt->fetch();
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
        return self::answer($arrUser);
    }

    public static function getByUsername($strUsername)
    {

        $objModel = new model\UserModel();
        $arrUser = $objModel->getUserByUsername($strUsername);
        return self::answer($arrUser);
    }

    public static function signUp($strEmail, $strUsername, $strPasswordHash, $boolSubscribe)
    {
        $arrErrors = array();
        $arrResult = array();
        $objModel = new model\UserModel();

        $strEmail = trim(strtolower(stripslashes(strip_tags($strEmail))));
        $strUsername = trim(strtolower(stripslashes(strip_tags($strUsername))));

        if (!filter_var($strEmail, FILTER_VALIDATE_EMAIL)) {
            $arrErrors[] = 'Podano nieprawidłowy adres e-mail';
        }
        if (empty($arrErrors)) {
            $arrAvailability = $objModel->isCreditentialsAvailable($strEmail, $strUsername);
            if (empty($arrAvailability)) {
                $numAccountId = $objModel->addUser($strEmail, $strUsername, $strPasswordHash, true);
                if (!empty($numAccountId)) {
                    $arrUser = $objModel->getAccountUser($numAccountId);

                    $strActivationHash = md5($arrUser['email'] . $arrUser['display_name'] . time());
                    $objModel->markEmailConfirmProcessStarted($numAccountId, $strActivationHash);

                    $objMail = new \backend\SystemMail('AccountActivation');
                    $objMail->addRecipient($arrUser['email'], $arrUser['display_name']);
                    $objMail->setVariable('display_name', $arrUser['display_name']);
                    $objMail->setVariable('url', \webcitron\Subframe\Url::route('User::accountActivation', $strActivationHash));
                    $objMail->send();

                    if ($boolSubscribe) {
                        $objNewsletterModel = new NewsletterModel();
                        if (!$objNewsletterModel->existsSubscriber($arrUser['email'])) {
                            $strHash = md5($arrUser['email'] . time());
                            $boolIsCreated = $objNewsletterModel->createMember($arrUser['email'], $strHash);
                            if ($boolIsCreated) {

                                $objSubscriptionMail = new \backend\SystemMail('SubscriptionConfirmation');
                                $objSubscriptionMail->addRecipient($arrUser['email'], 'Subscriber');
                                $objSubscriptionMail->setVariable('url', Url::route('Newsletter::confirmSubscription', $strHash));
                                $objSubscriptionMail->send();
                            }
                        }
                    }

                    $arrResult[] = 'Twoje konto zostało założone. Wysłaliśmy link aktywujący pod Twój adres email. Odbierz pocztę i kliknij w ten link. Pamiętaj że link jest ważny tylko przez 72 godziny.';
                } else {
                    $arrErrors[] = 'Błąd podczas zakładania konta, prosimy spróbować ponownie';
                }
            } else {
                if (in_array('EMAIL_NOT_AVAILABLE', $arrAvailability)) {
                    $arrErrors[] = 'Podany adres e-mail jest już zajęty';
                }
                if (in_array('USERNAME_NOT_AVAILABLE', $arrAvailability)) {
                    $arrErrors[] = 'Podana nazwa użytkownika jest już zajęta';
                }
            }
        }
        return self::answer($arrResult, $arrErrors);
    }


    public static function signIn($strUsername, $strPasswordHash)
    {
        $arrErrors = array();
        $arrResult = array();
        $objModel = new model\UserModel();

        $numAccountId = $objModel->getAccountIdByCreditentials($strUsername, $strPasswordHash);
        if (!empty($numAccountId)) {
            $objModel->loginAccount($numAccountId);
            $arrResult['strRedirectTo'] = \webcitron\Subframe\Url::route('User::myUploads', array('najnowsze'));
        } else {
            $arrErrors[] = 'Podano nieprawidłowe dane logowania lub nie aktywowałeś konta za pomocą linka zawartego w wysłanej przez nas wiadomości e-mail';
        }

        return self::answer($arrResult, $arrErrors);
    }

    public function confirmEmail($strConfirmHash)
    {
        $objModel = new UserModel();
//        $objModel->clearPendingAccounts();

        $boolIsOk = $objModel->confirmEmail($strConfirmHash);
        return parent::answer($boolIsOk);
    }

    public static function setIsProStats($strUserId, $numState)
    {
        $objModel = new UserModel();
        $objModel->setIsProStats($strUserId, $numState);
        return parent::answer(true);
    }
    
    public static function setIsAnonymousAvailable($strUserId, $numState)
    {
        $objModel = new UserModel();
        $objModel->setIsAnonymousAvailable($strUserId, $numState);
        return parent::answer(true);
    }
    
    public static function resetPassword($strEmail)
    {
        $arrResult = array();
        $arrErrors = array();
        $objModel = new model\UserModel();

        if (!filter_var($strEmail, FILTER_VALIDATE_EMAIL)) {
            $arrErrors[] = 'Podano nieprawidłowy adres e-mail';
        } else {
            $arrAccount = $objModel->getAccountByEmail($strEmail);
            if (empty($arrAccount)) {
                $arrErrors[] = 'Konto nie istnieje';
            } else if ($arrAccount['is_email_confirmed'] == false) {
                $arrErrors[] = 'Konto nie zostało jeszcze aktywowane. Aktywuj je kliknięciem w link który wysłaliśmy na Twój adres e-mail';
            } else {

                $arrLogin = $objModel->getInternalLogin($arrAccount['id']);
                if (empty($arrLogin['username'])) {
                    $arrErrors[] = 'Konto ma możliwość logowania tylko za pomocą serwisów społecznościowych (np Facebooka)';
                } else {
                    $arrNewPassword = \backend\Password::getGeneratePassword();
                    $objModel->changePassword($arrLogin['username'], $arrNewPassword['password']);

                    $objMail = new \backend\SystemMail('ForgotPassword');
                    $objMail->addRecipient($arrAccount['email'], $arrAccount['display_name']);
                    $objMail->setVariable('username', $arrLogin['username']);
                    $objMail->setVariable('new_password', $arrNewPassword['passwordText']);
                    $objMail->send();

                    $arrResult[] = 'Wygenerowaliśmy dla Ciebie nowe hasło oraz wysłaliśmy je na Twój adres e-mail <strong>' . $arrAccount['email'] . '</strong>';
                }
            }
        }
        return self::answer($arrResult, $arrErrors);
    }

    public static function getBasicStats()
    {
        $arrResult = array();
        $arrErrors = array();
        $objUserModel = new UserModel();
        $arrUsersBasicStats = $objUserModel->getBasicStats();
        $arrResult[] = $arrUsersBasicStats;
        return self::answer($arrResult, $arrErrors);
    }

    public static function isLoggedIn()
    {
        $boolLoggedIn = false;
        $objSession = \backend\Session::getInstance('imagehost_user');
        $numAccountId = $objSession->getValue('imagehost_account_id');
        if ($numAccountId > 0){
            $boolLoggedIn = true;
        }
        return $boolLoggedIn;
    }

    public static function getLoggedUser()
    {
        $objUserModel = new UserModel();
        $objUser = $objUserModel->getLoggedUser();
        return $objUser;
    }

}