<?php
use webcitron\Subframe\Db;

Db::addConnection('pgsql', 'gooroo-pgsql.inten.pl', 'imgjet', array('imgjet', 'OpsIrdamteiterdoajpacOt8'), 'production');
Db::addConnection('pgsql', 'gooroo-pgsql.inten.pl', 'imgjetrc', array('imgjetrc', 'GuvauwoutyoysfadnacNelj3'), 'rc');
Db::addConnection('pgsql', 'gooroo-pgsql.inten.pl', 'imgjetrc', array('imgjetrc', 'GuvauwoutyoysfadnacNelj3'), 'dev');
