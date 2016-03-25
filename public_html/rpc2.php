<?php
ini_set('log_errors','1'); 
ini_set('display_errors','0'); 

date_default_timezone_set('Europe/Warsaw');
require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../app/autoload.php';

$objLauncher = new \webcitron\Subframe\Launcher();
$objLauncher->rpc();

//$strRequest = urldecode(file_get_contents("php://input"));
$strRequest = file_get_contents("php://input");
$arrRequest = json_decode($strRequest, true);

$strId = $arrRequest['id'];
$strMethod = $arrRequest['method'];
$arrParams = !empty($arrRequest['params']) ? $arrRequest['params'] : array();
$arrMethodTokens = explode('.', $strMethod);
$strRpcVersion = array_shift($arrMethodTokens);
$strApplicationName = array_shift($arrMethodTokens);
$strMethodPointer = join('.', $arrMethodTokens);

if ($strRpcVersion !== 'rpc2') {
    exit('invalid request');
}

$objRpcApiController = \webcitron\Subframe\RpcApiController::getInstance($strApplicationName);

$arrRpcResponse = array();
$arrRpcResponse['id'] = $strId;
$arrRpcResponse['result'] = $objRpcApiController->fireMethod($strMethodPointer, $arrParams, $strMethod);

echo json_encode($arrRpcResponse);
