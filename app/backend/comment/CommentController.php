<?php namespace backend\comment;

use backend\DbFactory;
use webcitron\Subframe\Controller;

class CommentController extends Controller
{
    
    public static function getArtifactComments ($numArtifactId) {
        $objDb = DbFactory::getInstance();
        // SELECT author_nickname, add_timestamp_utc AT TIME ZONE 'Europe/Warsaw' AS add_timestamp_utc, content 
        $strSql = <<<EOF
SELECT author_nickname, add_timestamp_utc, content 
FROM comment 
WHERE artifact_id = :artifact_id 
ORDER BY add_timestamp_utc 
EOF;
        $objSth = $objDb->prepare($strSql);
        $arrParams = array(
            ':artifact_id' => $numArtifactId
        );
        $objSth->execute($arrParams);
        $arrComments = $objSth->fetchAll();
        return self::answer($arrComments);
    }
    
}