<?php

function autoloadStock($strClassName)
{
    if ($strClassName === 'PHPmailer') {
        require_once __DIR__.'/backend/libs/phpmailer/class.phpmailer.php';
        return;
    } else if ($strClassName === 'SMTP') {
        require_once __DIR__.'/backend/libs/phpmailer/class.smtp.php';
        return;
    } else if ($strClassName === 'Mailchimp') {
        require_once __DIR__.'/backend/libs/mailchimp/Mailchimp.php';
        return;
    }
    $strSeprator = sprintf("%s", chr('92'));
    $strClassName = ltrim($strClassName, $strSeprator);
    $strFileName  = '';
    $strNameSpace = '';
    
    if ($numLastNsPos = strripos($strClassName, $strSeprator)) {
        $strNameSpace = substr($strClassName, 0, $numLastNsPos);
        $strClassName = substr($strClassName, $numLastNsPos + 1);
        $strFileName  = str_replace($strSeprator, DIRECTORY_SEPARATOR, $strNameSpace) . DIRECTORY_SEPARATOR;
    }
    $strFileName .= str_replace('_', DIRECTORY_SEPARATOR, $strClassName) . '.php';
    $strFileNamePath = sprintf("%s%s%s", __DIR__, DIRECTORY_SEPARATOR, $strFileName);
    if (is_readable($strFileNamePath)) {
        require_once $strFileNamePath;
    } else {
        // Global model
        if (strstr($strFileName, "Model")) {
            $strFileNamePath = sprintf("model/%s", $strFileName);
            if (is_readable($strFileNamePath)) {
                require_once $strFileNamePath;
            }
        }
    }
}

spl_autoload_register('autoloadStock');
