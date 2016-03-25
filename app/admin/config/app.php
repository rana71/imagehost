<?php
use webcitron\Subframe\Config;
use webcitron\Subframe\Application;

Config::setTemplater('blitz');
Application::addEnvironment(Application::ENVIRONMENT_PRODUCTION, 'http://admin.imged.pl');
Application::addEnvironment(Application::ENVIRONMENT_RC, 'http://admin.rc.imged.com');
Application::addEnvironment(Application::ENVIRONMENT_DEV, 'http://admin.imagehost.dev');

\webcitron\Subframe\JsController::runJs();
