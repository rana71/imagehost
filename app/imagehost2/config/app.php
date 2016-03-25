<?php
use webcitron\Subframe\Config;
use webcitron\Subframe\Application;
Config::setTemplater('blitz');

Application::addEnvironment(Application::ENVIRONMENT_PRODUCTION, 'http://old.imged.pl');
Application::addEnvironment(Application::ENVIRONMENT_RC, 'http://rc.old.imged.com');
Application::addEnvironment(Application::ENVIRONMENT_DEV, 'http://old.imagehost.dev');
