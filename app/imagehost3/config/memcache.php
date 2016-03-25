<?php
use webcitron\Subframe\StorageMemcache;
use webcitron\Subframe\Application;

StorageMemcache::addServer(Application::ENVIRONMENT_DEV, 'stats', 11211, 'imagehost_dev_%s');
StorageMemcache::addServer(Application::ENVIRONMENT_RC, 'stats', 11211, 'rc_imged_com_%s');

$strAdditionalConfigFile = dirname(__FILE__).'/memcache-live.php';
if (file_exists($strAdditionalConfigFile)) {
    include $strAdditionalConfigFile;
}