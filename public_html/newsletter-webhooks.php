<?php
$log = fopen('../webhook.log', 'w+');
ini_set('log_errors', 0); 
ini_set('display_errors', 1); 

date_default_timezone_set('Europe/Warsaw');
require dirname(__FILE__).'/../vendor/autoload.php';
require dirname(__FILE__).'/../app/autoload.php';

$get = $_GET;
$post = $_POST;
$server = $_SERVER;
fwrite($log, print_r($post, true));

fwrite($log, 'A');
$objLauncher = new \webcitron\Subframe\Launcher('http://imged.pl');
fwrite($log, 'B');
$objLauncher->cron();
fwrite($log, 'C');


if ($get['fgadsfasdfasd'] === 'asdfa89y24Y51H1954UY4893UJ12PJ') {
    fwrite($log, 'D');
    if (!empty($post['data']['action'])) {
        $strAction = strtolower(trim($post['data']['action']));
    } else {
        fwrite($log, ' X1 ');
        exit();
    }
    
    if (!empty($post['data']['email'])) {
        $strEmail = $post['data']['email'];
    } else {
        fwrite($log, ' X2 ');
        exit();
    }
    
    fwrite($log, $strAction);
    fwrite($log, $strEmail);
    
    switch ($strAction) {
        case 'unsub':
        case 'delete':
            fwrite($log, 'D');
            try {
                fwrite($log, 'E');
                $objNewsletter = new \backend\newsletter\NewsletterController();
                fwrite($log, 'F'.$strEmail);
                $objNewsletter->removeMember($strEmail);
                fwrite($log, 'G');
            } catch (Exception $e) {
                echo '<Pre>';
                print_r($e);
                exit();
            }
            break;
    }
}

$strContent = '<pre>';
$strContent .= '$_POST: '.print_r($post, true);
$strContent .= '$_GET: '.print_r($get, true);
$strContent .= '$_SERVER: '.print_r($server, true);
$strContent .= '</pre>';

mail('a.mackiewicz@webcitron.eu', 'newsletter webhook', $strContent);
fclose($log);