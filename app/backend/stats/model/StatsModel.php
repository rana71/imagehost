<?php namespace backend\stats\model;

use \backend\DbFactory;

class StatsModel { 
    
    private $objDb = null;
    
    public function __construct () {
        $this->objDb = DbFactory::getInstance('stats');
    }
    
    public function saveStats ($arrStats = array()) {
        if (!empty($arrStats)) {
            $arrInserts = array();
            foreach ($arrStats as $arrStatsRow) {
                $arrInserts[] = sprintf('(%d, %d, \'%s\' at time zone \'utc\')', $arrStatsRow['numArtifactId'], $arrStatsRow['numUserId'], gmdate('Y-m-d H:i:s', $arrStatsRow['numTimestamp']));
            }
            $strQ = "INSERT INTO track (artifact_id, user_id, timestamp) VALUES ".join(', ', $arrInserts);
            $objSth = $this->objDb->prepare($strQ);
            $objSth->execute();
        }
        
    }
    
    public function getUserArtifactsStats ($numUserId) {
        $strQ = <<<EOF
SELECT s.date_key AS date_key, count(t.artifact_id) AS shows_count
FROM (
    select to_char(serie, 'YYYY-MM-DD') as date_key
    FROM generate_series(
        CURRENT_TIMESTAMP - INTERVAL '1 month', 
        CURRENT_TIMESTAMP, 
        '1 day'::interval
    ) AS serie 
) AS s 
LEFT JOIN track AS t ON (
    t.user_id = :user_id  
    AND to_char("timestamp", 'YYYY-MM-DD') = s.date_key 
)
GROUP BY s.date_key 
ORDER BY s.date_key ASC 
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->bindValue(':user_id', $numUserId, \PDO::PARAM_INT);
        $objSth->execute();
        $arrStats  = $objSth->fetchAll(\PDO::FETCH_NUM);
        
        return $arrStats;
    }
    
    public function getUserArtifactStats ($numUserId, $numArtifactId) {
        $strQ = <<<EOF
SELECT s.date_key AS date_key, count(t.artifact_id) AS shows_count
FROM (
    select to_char(serie, 'YYYY-MM-DD') as date_key
    FROM generate_series(
        CURRENT_TIMESTAMP - INTERVAL '1 month', 
        CURRENT_TIMESTAMP, 
        '1 day'::interval
    ) AS serie 
) AS s 
LEFT JOIN track AS t ON (
    t.user_id = :user_id  
    AND t.artifact_id = :artifact_id 
    AND to_char("timestamp", 'YYYY-MM-DD') = s.date_key 
)
GROUP BY s.date_key 
ORDER BY s.date_key ASC 
EOF;
//        echo $strQ.'x'.$numUserId.'x'.$numArtifactId;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->bindValue(':user_id', $numUserId, \PDO::PARAM_INT);
        $objSth->bindValue(':artifact_id', $numArtifactId, \PDO::PARAM_INT);
        $objSth->execute();
        $arrStats  = $objSth->fetchAll(\PDO::FETCH_NUM);
        
        return $arrStats;
    }
}