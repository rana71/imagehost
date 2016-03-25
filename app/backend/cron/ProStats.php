<?php
namespace backend\cron;

use webcitron\Subframe\StorageMemcache;
use backend\stats\model\StatsModel;


class ProStats {
   
    public function save () {
        $objStorageMemcache = new StorageMemcache('stats');
        $arrItemsShows = $objStorageMemcache->get('artifacts_pro_stats4', array());

        if (empty($arrItemsShows)) {
            return;
        }
        
        $objStorageMemcache->clear();
        
        $arrStats = array();
        foreach ($arrItemsShows as $strUserIdItemId => $arrTimestamps) {
            list($numUserId, $numArtifactId) = explode('|', $strUserIdItemId);
            foreach ($arrTimestamps as $numTimestamp) {
                $arrStats[] = array(
                    'numArtifactId' => $numArtifactId, 
                    'numUserId' => $numUserId, 
                    'numTimestamp' => $numTimestamp
                );
            }
        }
        
        if (!empty($arrStats)) {
            $objStatsModel = new StatsModel();
            $objStatsModel->saveStats($arrStats);
        }
        
    }
    
    
}