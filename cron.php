<?php
date_default_timezone_set('Europe/Warsaw');

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/app/autoload.php';

$strScriptFilename = array_shift($argv);
$strVirtualDomain = array_shift($argv);
$strMethodPointer = array_shift($argv);
$arrXargs = $argv;

$objLauncher = new \webcitron\Subframe\Launcher($strVirtualDomain);
$objLauncher->cron();

$objCronController = \webcitron\Subframe\CronController::getInstance();
//$objCronController->setEnvironment($strEvironmentName);

$arrRpcResponse = array();
$arrRpcResponse['result'] = $objCronController->fireMethod($strMethodPointer, $arrXargs);
//$arrRpcResponse['id'] = $strId;
//
//echo json_encode($arrRpcResponse);
