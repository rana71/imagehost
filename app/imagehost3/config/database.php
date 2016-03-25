<?php
use webcitron\Subframe\Db;
use webcitron\Subframe\Application;

Db::addConnection('default', Application::ENVIRONMENT_DEV, 'pgsql', 'gooroo-pgsql.inten.pl', 'imgjetrc', array('imgjetrc', 'GuvauwoutyoysfadnacNelj3'));
Db::addConnection('default', Application::ENVIRONMENT_RC, 'pgsql', 'gooroo-pgsql.inten.pl', 'imgjetrc', array('imgjetrc', 'GuvauwoutyoysfadnacNelj3'));

Db::addConnection('stats', Application::ENVIRONMENT_DEV, 'pgsql', 'gooroo-pgsql.inten.pl', 'rc_track_imged_pl', array('rc_track_imged_pl', 'Gs63h*2('));
Db::addConnection('stats', Application::ENVIRONMENT_RC, 'pgsql', 'gooroo-pgsql.inten.pl', 'rc_track_imged_pl', array('rc_track_imged_pl', 'Gs63h*2('));

$strAdditionalConfigFile = dirname(__FILE__).'/database-live.php';
if (file_exists($strAdditionalConfigFile)) {
    include $strAdditionalConfigFile;
}