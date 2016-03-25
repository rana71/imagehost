<?php namespace backend\user\model;

use \backend\DbFactory;

class UserModel { 
    
    private $objDb = null;
    
    public function __construct () {
        $this->objDb = DbFactory::getInstance();
        $this->objDb->exec("SET SCHEMA 'users'");
    }
    
    public function isIpAllowedToUpload ($strIp) {
        $boolIsAllowed = true;
        $strQ = <<<EOF
SELECT count(1) AS ex 
FROM users.blocked_upload 
WHERE ip = :ip
LIMIT 1
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':ip' => $strIp
        ));
        $numIsBlocked = $objSth->fetch(\PDO::FETCH_COLUMN, 0);
        
        if ($numIsBlocked > 0) {
            $boolIsAllowed = false;
        }
        return $boolIsAllowed;
    }
    
    public function getBlockedUploadIps ($strSearchString = '', $numLimit = 3) {
        $strQPattern = <<<EOF
SELECT id, block_date, ip 
FROM users.blocked_upload 
%s
LIMIT %d
EOF;
        $strWhere = '';
        if (!empty($strSearchString)) {
            $strWhere = "WHERE ip LIKE '%".$strSearchString."%'";
        }
        $strQ = sprintf($strQPattern, $strWhere, $numLimit);
        
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute();
        
        $arrBlocks = $objSth->fetchAll();
        return $arrBlocks;
    }
    
    public function removeBlockedUploadId ($numBlockId) {
        $strQ = <<<EOF
DELETE FROM users.blocked_upload 
WHERE id = :id 
RETURNING id, ip
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':id' => $numBlockId
        ));
        
        $arrRemovedBlock = $objSth->fetch();
        
        return $arrRemovedBlock;
    }
    
    public function addDisabledUploadIp ($strIp) {
        $strQ = <<<EOF
INSERT INTO users.blocked_upload (block_date, ip) 
VALUES (CURRENT_TIMESTAMP, :ip)
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->bindValue(':ip', $strIp);
        $objSth->execute();
        
        return true;
    }
    
    public function getUncofirmedUsers () {
        $strQ = <<<EOF
SELECT id, display_name, email, email_confirmation_hash 
FROM account 
WHERE is_email_confirmed = FALSE 
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute();
        
        $arrAccounts = $objSth->fetchAll();
        return $arrAccounts;
    }
    
    public function setIsProStats ($numUserId, $numState) {
        $strQ = <<<EOF
UPDATE users.account 
SET is_pro_stats = :new_state 
WHERE id = :id
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':new_state' => $numState, 
            ':id' => $numUserId
        ));
    }
    
    public function setIsAnonymousAvailable ($numUserId, $numState) {
        $strQ = <<<EOF
UPDATE users.account 
SET is_anonymous_available = :new_state 
WHERE id = :id
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':new_state' => $numState, 
            ':id' => $numUserId
        ));
    }
    
    public function getUsers ($numOffset, $numLimit) {
        $strQ = <<<EOF
SELECT 
    display_name 
FROM account 
WHERE is_email_confirmed = TRUE 
LIMIT :limit 
OFFSET :offset
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->bindValue(':limit', $numLimit, \PDO::PARAM_INT);
        $objSth->bindValue(':offset', $numOffset, \PDO::PARAM_INT);
        $objSth->execute();
        
        $arrAccounts = $objSth->fetchAll();
        return $arrAccounts;
    }
    
    public function getBasicStats () {
        $arrStats = array();
        $strQ = "SELECT COUNT(1) FROM account WHERE is_email_confirmed = true";
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute();
        $numCount = $objSth->fetch(\PDO::FETCH_COLUMN, 0);
        $arrStats['numTotalActivated'] = $numCount;
        $strQ = "SELECT COUNT(1) FROM account WHERE is_email_confirmed = false";
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute();
        $numCount = $objSth->fetch(\PDO::FETCH_COLUMN, 0);
        $arrStats['numTotalNotActivated'] = $numCount;
        return $arrStats;
    }
    
    public function getListAdmin ($numLimit, $strSearchQuery) {
        $strQPattern = <<<EOF
SELECT a.id, a.display_name, a.email, a.register_timestamp, a.is_email_confirmed, a.email_confirm_timestamp, a.email_confirmation_sent_timestamp, COUNT(i.id) AS artifacts_count, a.is_pro_stats, a.is_anonymous_available   
FROM account AS a 
LEFT JOIN artifacts.item AS i ON (i.author_account_id = a.id AND i.is_public = TRUE AND i.is_removed = FALSE) 
%s 
GROUP BY i.author_account_id , a.id, a.display_name, a.email, a.register_timestamp, a.is_email_confirmed, a.email_confirm_timestamp, a.email_confirmation_sent_timestamp, a.is_pro_stats, a.is_anonymous_available 
ORDER BY a.is_email_confirmed DESC, a.email_confirm_timestamp DESC NULLS LAST, a.register_timestamp DESC NULLS LAST 
LIMIT :limit
EOF;
//SELECT u.user_id, u.username, u.email, u.add_date, u.activation_hash, u.activation_datetime, u.activation_sent_datetime, count(o.id) AS artifacts_count 
//FROM users AS u  
//LEFT JOIN offer AS o ON (o.user_id = u.user_id AND o.visible IS TRUE) 
//%s 
//GROUP BY o.user_id, u.username, u.user_id, u.username, u.email, u.add_date, u.activation_hash, u.activation_datetime, u.activation_sent_datetime 
//ORDER BY u.activation_datetime DESC NULLS LAST , u.add_date DESC NULLS LAST 
//LIMIT :limit
        if (!empty($strSearchQuery)) {
            $strQ = sprintf($strQPattern, "WHERE (display_name ILIKE '%".$strSearchQuery."%' OR email ILIKE '%".$strSearchQuery."%')");
        } else {
            $strQ = sprintf($strQPattern, "");
        }
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':limit' => $numLimit
        ));
        $arrList = $objSth->fetchAll();
        return $arrList;
    }
    
    public function logout () {
        \backend\Session::destroy();
    }
    
    public function changeInternalLoginPassword ($numIternalLoginId, $strPasswordHash) {
        $strQ = <<<EOF
UPDATE internal_login 
SET password = :password 
WHERE id = :internal_login_id
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $boolResult = $objSth->execute(array(
            ':password' => $strPasswordHash, 
            ':internal_login_id' => $numIternalLoginId
        ));
        
        return $boolResult;
    }
    
    public function getAccountCreditentials ($numAccountId) {
        $strQ = <<<EOF
SELECT il.username, 
    il.password, 
    il.id AS internal_login_id 
FROM account AS a 
JOIN login AS l ON l.id = a.login_id 
JOIN internal_login AS il ON il.id = l.internal_login_id 
WHERE a.id = :account_id
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':account_id' => $numAccountId
        ));
        $arrCreditentials = $objSth->fetch();
        
        return $arrCreditentials;
    }
    
    public function getLoggedUser () {
        $arrResult = array();
        $objSession = \backend\Session::getInstance('imagehost_user');
        $numAccountId = $objSession->getValue('imagehost_account_id');
        if (!empty($numAccountId)) {
            $arrResult = $this->getAccount($numAccountId);
        }
        return $arrResult;
    }
    
    public function getAccountIdByFacebookId ($strFacebookId) {
        $strQ = <<<EOF
SELECT a.id 
FROM social_account AS sa 
JOIN login AS l ON l.social_account_id = sa.id 
JOIN account AS a ON a.login_id = l.id 
WHERE sa.social_service = 1 
    AND social_user_id = :social_user_id
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':social_user_id' => $strFacebookId
        ));
        $numAccountId = $objSth->fetch(\PDO::FETCH_COLUMN, 0);
        return $numAccountId;
    }
    
    public function genreAutoUsername ($strPrefix) {
        $strPrefix = trim(strtolower($strPrefix));
        $strQ = "(SELECT username FROM internal_login WHERE username ILIKE '".$strPrefix."%')
            UNION 
            (SELECT display_name FROM account WHERE display_name ILIKE '".$strPrefix."%')";
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute();
        $arrUsernamesUsed = $objSth->fetchAll(\PDO::FETCH_COLUMN, 0);
        if (empty($arrUsernamesUsed)) {
            $strUsername = $strPrefix;
        } else {
            $numNo = 1;
            do {
                $strUsername = $strPrefix.$numNo;
                $numNo++;
            } while (in_array($strUsername, $arrUsernamesUsed));
        }
        
        return $strUsername;
    }
    
    public function connectFacebookId ($strUserId, $strFacebookId) {
                $strQ = <<<EOF
UPDATE users SET connected_facebook_id = :facebook_id WHERE user_id = :user_id
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':user_id' => $strUserId, 
            ':facebook_id' => $strFacebookId
        ));
        return true;
    }
    
    public function addUser($email, $strUsername, $password, $boolIsPasswordHashed = false)
    {   
        $numAccountId = 0;
        $numLoginId = $this->addInternalLogin($strUsername, $password, $boolIsPasswordHashed);
        if ($numLoginId !== false) {
            $numAccountId = $this->createAccount($numLoginId, $email, $strUsername);
        }
        return $numAccountId;
    }
    
    public function getAccountUser ($numAccountId) {
        $strQ = <<<EOF
SELECT email, 
    register_timestamp, 
    display_name 
FROM account 
WHERE id = :account_id
EOF;
        $objSthSelect = $this->objDb->prepare($strQ);
        
        $objSthSelect->execute(array(
            ':account_id' => $numAccountId
        ));
        $arrUser = $objSthSelect->fetch();
        
        return $arrUser;
    }
    
    public function clearPendingAccounts () {
        $strQ = <<<EOF
SELECT a.id AS account_id, 
    l.id AS login_id, 
    il.id AS internal_login_id 
FROM account AS a 
LEFT JOIN login AS l ON l.id = a.login_id 
LEFT JOIN internal_login AS il ON il.id = l.internal_login_id                  
WHERE email_confirmation_hash IS NOT NULL 
    AND email_confirmation_sent_timestamp < (NOW() AT TIME ZONE 'UTC') - INTERVAL '72 hours'
EOF;
        $objSthSelect = $this->objDb->prepare($strQ);
        
$strQ = <<<EOF
DELETE FROM account WHERE id = :account_id
EOF;
        $objSthDeleteAccount = $this->objDb->prepare($strQ);
        
$strQ = <<<EOF
DELETE FROM login WHERE id = :login_id
EOF;
        $objSthDeleteLogin = $this->objDb->prepare($strQ);
        
$strQ = <<<EOF
DELETE FROM internal_login WHERE id = :internal_login_id 
EOF;
        $objSthDeleteInternalLogin = $this->objDb->prepare($strQ);
        
        $objSthSelect->execute();
        $arrRows = $objSthSelect->fetch(); 
        
        if (!empty($arrRows)) {
            foreach ($arrRows as $arrRow) {
                $objSthDeleteAccount->execute(array(':account_id' => $arrRow['account_id']));
                $objSthDeleteLogin->execute(array(':login_id' => $arrRow['login_id']));
                $objSthDeleteInternalLogin->execute(array(':internal_login_id' => $arrRow['internal_login_id']));
            }
        }
    }
    
    public function confirmEmail($strConfirmationHash) {
        $strQ = <<<EOF
UPDATE account 
SET email_confirm_timestamp = NOW() AT TIME ZONE 'UTC', 
    email_confirmation_hash = NULL, 
    email_confirmation_sent_timestamp = NULL, 
    is_email_confirmed = TRUE 
WHERE email_confirmation_hash = :confirmation_hash 
RETURNING id
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(':confirmation_hash' => $strConfirmationHash));
        $numActivatedAccount = $objSth->fetch(\PDO::FETCH_COLUMN, 0);
        
        $boolReturn = false;
        if (!empty($numActivatedAccount)) {
            $boolReturn = true;
        }
        
        return $boolReturn;
    }
    
    public function confirmEmailByAccountId($numAccountId) {
        $strQ = <<<EOF
UPDATE account 
SET email_confirm_timestamp = NOW() AT TIME ZONE 'UTC', 
    email_confirmation_hash = NULL, 
    email_confirmation_sent_timestamp = NULL, 
    is_email_confirmed = TRUE 
WHERE id = :account_id
RETURNING id
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(':account_id' => $numAccountId));
        $numActivatedAccount = $objSth->fetch(\PDO::FETCH_COLUMN, 0);
        
        $boolReturn = false;
        if (!empty($numActivatedAccount)) {
            $boolReturn = true;
        }
        
        return $boolReturn;
    }
    
    public function markEmailConfirmProcessStarted ($numAccountId, $strConfirmationHash) {
        $strQ = <<<EOF
UPDATE account 
SET email_confirm_timestamp = NULL, 
    email_confirmation_hash = :confirmation_hash, 
    email_confirmation_sent_timestamp = NOW() AT TIME ZONE 'UTC', 
    is_email_confirmed = FALSE
WHERE id = :account_id
EOF;
        $objSthUpdate = $this->objDb->prepare($strQ);
        $objSthUpdate->execute(array(
            ':confirmation_hash' => $strConfirmationHash, 
            ':account_id' => $numAccountId
        ));
    }
    
    private function addInternalLogin ($strUsername, $strPassword, $boolIsPasswordHashed = false) {
        $strQ = <<<EOF
INSERT INTO internal_login (
    password, username 
) VALUES (
    :password, :username
) RETURNING id;
EOF;
        $objSthInsertInternal = $this->objDb->prepare($strQ);
        
        $strQ = <<<EOF
INSERT INTO login (
    internal_login_id
) VALUES (
    :internal_login_id
) RETURNING id;
EOF;
        $objSthInsertLogin = $this->objDb->prepare($strQ);
        
        if ($boolIsPasswordHashed === false) {
            $strPassword = \backend\Password::getPasswordHash($strPassword);
        }
        $objSthInsertInternal->execute(array(
            ':username' => $strUsername, 
            ':password' => $strPassword
        ));
        $numInternalLoginId = $objSthInsertInternal->fetch(\PDO::FETCH_COLUMN, 0);
        
        $objSthInsertLogin->execute(array(
            ':internal_login_id' => $numInternalLoginId
        ));
        $numLoginId = $objSthInsertLogin->fetch(\PDO::FETCH_COLUMN, 0);
        
        return $numLoginId;
        
    }
    
    public function createLoginByFacebook ($strFacebookId) {
        $strQ = <<<EOF
INSERT INTO social_account (
    social_service, social_user_id 
) VALUES (
    :social_service, :social_user_id
) RETURNING id
EOF;
        $objSthInsertAccount = $this->objDb->prepare($strQ);
        
        $strQ = <<<EOF
INSERT INTO login (
    social_account_id 
) VALUES (
    :social_account_id
) RETURNING id
EOF;
        $objSthInsertLogin = $this->objDb->prepare($strQ);
        
        $objSthInsertAccount->execute(array(
            ':social_service' => 1, 
            ':social_user_id' => $strFacebookId
        ));
        $numSocialAccountId = $objSthInsertAccount->fetch(\PDO::FETCH_COLUMN, 0);
        
        $objSthInsertLogin->execute(array(
            ':social_account_id' => $numSocialAccountId
        ));
        $numLoginId = $objSthInsertLogin->fetch(\PDO::FETCH_COLUMN, 0);
        
        return $numLoginId;
    }
     
    public function createAccount ($numLoginId, $strEmail, $strDisplayName) {
        $strEmail = strtolower($strEmail);
        $strQ = <<<EOF
INSERT  INTO account (
    email,  login_id, register_timestamp, is_email_confirmed, display_name 
) VALUES (
    :email, :login_id, NOW() AT TIME ZONE 'UTC', FALSE, :display_name
) RETURNING id;
EOF;
        $objSthInsert = $this->objDb->prepare($strQ);
        $objSthInsert->execute(array(
            ':email' => $strEmail, 
            ':login_id' => $numLoginId, 
            ':display_name' => $strDisplayName
        ));
        $numAccountId = $objSthInsert->fetch(\PDO::FETCH_COLUMN, 0);
        
        return $numAccountId;
    }
    
    public function getAccountIdByCreditentials($strUsername, $strPasswordHash)
    {
        $strQ = <<<EOF
SELECT a.id
FROM internal_login AS il 
JOIN login AS l ON l.internal_login_id = il.id 
JOIN account AS a on a.login_id = l.id 
WHERE a.is_email_confirmed = TRUE 
    AND il.username = :username 
    AND il.password = :password_hash
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':username' => $strUsername, 
            ':password_hash' => $strPasswordHash
        ));
        $numAccountId = $objSth->fetch(\PDO::FETCH_COLUMN, 0);
        
        return $numAccountId;
    }
    
    public function loginAccount ($numAccountId) {
        $objSession = \backend\Session::getInstance('imagehost_user');
        $objSession->setValue('imagehost_account_id' , $numAccountId);
        
        return true;
    }
    
    public function getAccountByDisplayname ($strDisplayName) {
        $strQ = <<<EOF
SELECT * 
FROM account 
WHERE display_name = :display_name
EOF;
        $objStmt = $this->objDb->prepare($strQ);
        $objStmt->execute(array(
            ':display_name' => $strDisplayName,
        ));
        $arrResult = $objStmt->fetch();
        return $arrResult;
    }
    
    public function getAccountByEmail($strEmail)
    {
        $strQ = <<<EOF
SELECT a.*  
FROM account AS a 
WHERE a.email = :email
EOF;
        $objStmt = $this->objDb->prepare($strQ);
        $objStmt->execute(array(
            ':email' => $strEmail,
        ));
        $arrResult = $objStmt->fetch();
        return $arrResult;
    }
    
    public function getAccount($numAccountId)
    {
        $strQ = <<<EOF
SELECT *
FROM account 
WHERE id = :account_id
EOF;
        $objStmt = $this->objDb->prepare($strQ);
        $objStmt->execute(array(
            ':account_id' => $numAccountId,
        ));
        $arrResult = $objStmt->fetch();
        return $arrResult;
    }
    
    public function getInternalLogin ($numAccountId) {
        $strQ = <<<EOF
SELECT il.username 
FROM account AS a 
JOIN login AS l ON l.id = a.login_id 
JOIN internal_login AS il ON il.id = l.internal_login_id 
WHERE a.id = :account_id
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':account_id' => $numAccountId,
        ));
        $arrResult = $objSth->fetch();
        return $arrResult;
    }
    
    public function changePassword($username, $password)
    {
        $strQ = <<<EOF
UPDATE internal_login 
SET password = :new_password_hash 
WHERE username = :username
EOF;
        
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':new_password_hash' => $password, 
            ':username' => $username
        ));
    }
    
    public function isCreditentialsAvailable ($strEmail, $strUsername, $numExceptAccountId = 0) {
        $strQ = <<<EOF
SELECT count(1) 
FROM account 
WHERE email = :email
AND id != :except_account_id
EOF;
        $objSthSelectEmail = $this->objDb->prepare($strQ);
        
$strQ = <<<EOF
SELECT count(1) 
FROM internal_login AS il 
LEFT JOIN login AS l ON l.internal_login_id = il.id 
LEFT JOIN account AS a ON a.login_id = l.id 
WHERE il.username = :username
AND a.id != :except_account_id
EOF;

        $objSthSelectUsername = $this->objDb->prepare($strQ);
        
        $strQ = <<<EOF
SELECT count(1) 
FROM account
WHERE display_name = :display_name
AND id != :except_account_id
EOF;

        $objSthSelectDisplayName = $this->objDb->prepare($strQ);
        
        $arrReturn = array(); 
        
        $objSthSelectEmail->execute(array(
            ':email' => $strEmail, 
            ':except_account_id' => $numExceptAccountId
        ));
        $numIsMailBusy = $objSthSelectEmail->fetch(\PDO::FETCH_COLUMN, 0);
        if (!empty($numIsMailBusy)) {
            $arrReturn[] = 'EMAIL_NOT_AVAILABLE';
        }
        
        $objSthSelectUsername->execute(array(
            ':username' => $strUsername, 
            ':except_account_id' => $numExceptAccountId
        ));
        $umIsUsernameBusy = $objSthSelectUsername->fetch(\PDO::FETCH_COLUMN, 0);
        if (!empty($umIsUsernameBusy)) {
            $arrReturn[] = 'USERNAME_NOT_AVAILABLE';
        } else {
            $objSthSelectDisplayName->execute(array(
                ':display_name' => $strUsername, 
                ':except_account_id' => $numExceptAccountId
            ));
            $umIsUsernameBusy = $objSthSelectDisplayName->fetch(\PDO::FETCH_COLUMN, 0);
            if (!empty($umIsUsernameBusy)) {
                $arrReturn[] = 'USERNAME_NOT_AVAILABLE';
            }
        }
        
        return $arrReturn;
    }
    
    public function changeAccountData ($numAccountId, $arrNewData) {
        $strQ = <<<EOF
UPDATE account 
SET %s 
WHERE id = :account_id
EOF;
        $arrUpdates = array();
        foreach ($arrNewData as $strKey => $strValue) {
            if (is_integer($strValue) || is_bool($strValue)) {
                $arrUpdates[] = sprintf('%s = %s', $strKey, $strValue);
            } else {
                $arrUpdates[] = sprintf('%s = %s', $strKey, $this->objDb->quote($strValue));
            }
        }
        $objSth = $this->objDb->prepare(sprintf($strQ, join(', ', $arrUpdates)));
        $boolId = $objSth->execute(array(
            ':account_id' => $numAccountId
        ));
        
        return $boolId;
    }
    
    
}
    