<?php
error_reporting(0);
require dirname(__FILE__).'/../../../app/autoload.php';
/*
  Sample Processing of Forgot password form via ajax
  Page: extra-register.html
 */

# Response Data Array
$resp = array();
if ($_SERVER['HTTP_HOST'] === 'admin.imged.pl') {
    $strDbUser = 'imgjet';
    $strDbName = 'imgjet';
    $strDbPass = 'OpsIrdamteiterdoajpacOt8';
} else {
    $strDbUser = 'imgjetrc';
    $strDbName = 'imgjetrc';
    $strDbPass = 'GuvauwoutyoysfadnacNelj3';
}

$strDsn = sprintf("pgsql:host=%s;dbname=%s", 'gooroo-pgsql.inten.pl', $strDbName);
$objPdo = new \PDO($strDsn, $strDbUser, $strDbPass);
$objPdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
$objPdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
$objPdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

// Fields Submitted

$username = $_POST["username"];
$password = $_POST["password"];
$client_ip = $_POST["client_ip"];

$strQ = "SELECT count(1) from admins.blocked_ip WHERE ip = :ip";
$stmt = $objPdo->prepare($strQ);
$stmt->execute(array(
    ':ip' => $client_ip
));
$numIsBlocked = $stmt->fetch(\PDO::FETCH_COLUMN, 0);

if ($numIsBlocked > 0) {
    $login_status = 'blocked-ip';
} else {
    $strQ = "SELECT id, password, is_blocked FROM admins.account WHERE username = :username";

    $stmt = $objPdo->prepare($strQ);
    $stmt->execute(array(
        ':username' => $username
    ));

    $arrAdmin = $stmt->fetch();
    if (empty($arrAdmin)) {
        $login_status = 'invalid';
        $numInsertAccountId = 0;
        $boolInsertSuccess = false;
    } else {
        if ($arrAdmin['is_blocked'] == 1 ) {
            $resp['submitted_data'] = $_POST;
            $resp['login_status'] = 'blockced-account';

            echo json_encode($resp);
            exit();

        } else if (md5($password) !== $arrAdmin['password']) {
            $login_status = 'invalid';
            $numInsertAccountId = $arrAdmin['id'];
            $boolInsertSuccess = false;
        } else {
            $login_status = 'success';
            $numInsertAccountId = $arrAdmin['id'];
            $boolInsertSuccess = true;
        }
    }

    $strQ = "INSERT INTO admins.login (account_id, client_ip, is_success, timestamp) VALUES (:account_id, :client_ip, :is_success, CURRENT_TIMESTAMP)";
    $stmt = $objPdo->prepare($strQ);
    $stmt->bindValue(':account_id', $numInsertAccountId, \PDO::PARAM_INT);
    $stmt->bindValue(':client_ip', $client_ip, \PDO::PARAM_STR);
    $stmt->bindValue(':is_success', $boolInsertSuccess, \PDO::PARAM_BOOL);
    $stmt->execute();
    
    if ($boolInsertSuccess === false) {
        $strQ = "SELECT account_id, is_success FROM admins.login WHERE client_ip = :ip AND account_id = :account ORDER BY timestamp DESC LIMIT 5";
        
        $stmt = $objPdo->prepare($strQ);
        $stmt->execute(array(
            ':ip' => $client_ip, 
            ':account' => $numInsertAccountId
        ));
        
        
        
        $arrLastLogins = $stmt->fetchAll();
        $numUnsuccessfull = 0;
        if (!empty($arrLastLogins)) {
            foreach ($arrLastLogins as $arrLogin) {
                if (intval($arrLogin['is_success']) !== 1) {
                    $numUnsuccessfull++;
                }
            }
        }
        
        if ($numUnsuccessfull === 5) {
            if ($numInsertAccountId === 0) {
                $strQ = "INSERT INTO admins.blocked_ip (ip) VALUES (:ip)";
                $stmt = $objPdo->prepare($strQ);
                $stmt->execute(array(
                    ':ip' => $client_ip
                ));
                
                $strSubject = 'admin.imged.pl - zablokowane ip klienta';
                $strContent = 'Data: '.date('Y-m-d H:i:s').', IP: '.$client_ip;
                mail('a.mackiewicz@webcitron.eu', $strSubject, $strContent);
                mail('bberlinski@gmail.com', $strSubject, $strContent);
            } else {
                $strQ = "UPDATE admins.account SET is_blocked = TRUE WHERE id = :account_id";
                $stmt = $objPdo->prepare($strQ);
                $stmt->execute(array(
                    ':account_id' => $numInsertAccountId
                ));
                $strSubject = 'admin.imged.pl - zablokowane konto admina';
                $strContent = 'Data: '.date('Y-m-d H:i:s').', IP: '.$client_ip.', konto: '.$username;
                mail('a.mackiewicz@webcitron.eu', $strSubject, $strContent);
                mail('bberlinski@gmail.com', $strSubject, $strContent);
            }
            
            $strQ = "DELETE FROM admins.login WHERE client_ip = :ip AND account_id = :account_id";
            $stmt = $objPdo->prepare($strQ);
            $stmt->execute(array(
                ':ip' => $client_ip, 
                ':account_id' => $numInsertAccountId
            ));
            
        }
        
        
    }
}

$resp['submitted_data'] = $_POST;
$resp['login_status'] = $login_status;

if ($login_status == 'success') {
    $objSession = backend\Session::getInstance('imagehost-admin');
    $objSession->setValue('admin_iamgehost_auth', $arrAdmin['id']);
    $resp['redirect_url'] = '';
}


echo json_encode($resp);
