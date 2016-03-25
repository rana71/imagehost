<?php
use webcitron\Subframe\Config;
use webcitron\Subframe\Application;
Config::setTemplater('blitz');

Application::addEnvironment(Application::ENVIRONMENT_PRODUCTION, 'http://imged.pl');
Application::addEnvironment(Application::ENVIRONMENT_RC, 'http://rc.imged.com');
Application::addEnvironment(Application::ENVIRONMENT_DEV, 'http://imagehost.dev');
