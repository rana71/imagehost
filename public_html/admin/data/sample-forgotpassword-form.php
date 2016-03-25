<?php
error_reporting(0);
require dirname(__FILE__).'/../../../app/autoload.php';
/*
	Sample Processing of Forgot password form via ajax
	Page: extra-register.html
*/

# Response Data Array
$resp = array();

$email = $_POST["email"];

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
$objPdo = new \PDO($strDsn, $strDbUser, $strDbName);
$objPdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
$objPdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
$objPdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

$strQ = "SELECT id, username FROM admin WHERE email = :email";

$stmt = $objPdo->prepare($strQ);
$stmt->execute(array(
    ':email' => $email
));

$arrAdmin = $stmt->fetch();
if (!empty($arrAdmin)) {
    $arrPass = \backend\Password::getGeneratePassword();
    $strQ = "UPDATE admin SET password_hash = :password_hash WHERE id = :id";
    $stmt = $objPdo->prepare($strQ);
    $stmt->execute(array(
        ':password_hash' => md5($arrPass['passwordText']), 
        ':id' => $arrAdmin['id']
    ));

    $objMail = new \backend\SystemMail('AdminPasswordForgot');
    $objMail->addRecipient($email, $arrAdmin['username']);
    $objMail->setVariable('username', $arrAdmin['username']);
    $objMail->setVariable('new_password', $arrPass['passwordText']);
    $objMail->send();
}

// Fields Submitted


// This array of data is returned for demo purpose, see assets/js/neon-forgotpassword.js
$resp['submitted_data'] = $_POST;

echo json_encode($resp);