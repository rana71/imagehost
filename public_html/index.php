<?php

ini_set('log_errors','1'); 
ini_set('display_errors','0'); 

date_default_timezone_set('Europe/Warsaw');
require dirname(__FILE__).'/../vendor/autoload.php';
//require dirname(__FILE__).'/../vendor/webcitron/Subframe/vendor/autoload.php';
require dirname(__FILE__).'/../app/autoload.php';

//$strClientIp = '';
//if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
//    $strClientIp = $_SERVER['HTTP_CF_CONNECTING_IP'];
//} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
//    $strClientIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
//} else if (!empty($_SERVER['REMOTE_ADDR'])) {
//    $strClientIp = $_SERVER['REMOTE_ADDR'];
//}

//$arrBlockedIp = array(
//    '62.233.42.48', 
//    '62.233.42.195', 
//    '62.233.53.193', 
//    '62.233.42.34', 
//    '62.233.57.197', 
//    '62.233.42.232', 
//    '62.233.57.138', 
//    '62.233.42.99', 
//    '107.168.80.82', 
//    '107.168.80.217', 
//    '107.168.80.114', 
//    '107.168.80.99'
//);
//
//if (!empty($strClientIp) && (in_array($strClientIp, $arrBlockedIp) || substr($strClientIp, 0, 7) === '62.233.')) {
//    exit();
//} else {
    $objLauncher = new webcitron\Subframe\Launcher();
    $objLauncher->goBabyGo();
//}
