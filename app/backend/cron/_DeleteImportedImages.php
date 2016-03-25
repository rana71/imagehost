<?php
namespace backend\cron;

use \backend\DbFactory;
use \backend\File;

class DeleteImportedImages
{
   
    public static function genreBash ($strConnectionName) {
        
        DbFactory::setDefaultConnection($strConnectionName);
        $objDb = DbFactory::getInstance();
        
        $strQ = <<<EOF
SELECT slug_pl, id, mimetype, id%5000 AS photo_directory 
FROM offer 
WHERE source_blog_id IS NOT NULL 
    AND image_source IS NOT NULL 
    AND mimetype IS NOT NULL
LIMIT 100 
EOF;
        
        $objSql = $objDb->prepare($strQ);
        $objSql->execute();
        $arrOffers = $objSql->fetchAll();
        echo "#!/bin/bash".PHP_EOL;
        foreach ($arrOffers as $arrOffer) {
            $numDirectory = $arrOffer['photo_directory'];
            $strSlug = $arrOffer['slug_pl'];
            $numId = $arrOffer['id'];
            $strExtension = File::mimetypeToExtension($arrOffer['mimetype']);
            echo sprintf("/bin/rm /home/imgjet/domains/imged.pl/public_html/p/%d/%s-%d.%s;".PHP_EOL, $numDirectory, $strSlug, $numId, $strExtension);
            echo "/bin/sleep 0.1;".PHP_EOL;
        }
        echo "exit;".PHP_EOL;
        
        
        unset ($objDb);
        
    }
    
}
