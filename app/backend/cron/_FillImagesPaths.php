<?php
namespace backend\cron;

use \backend\DbFactory;
use backend\File;

class FillImagesPaths
{
    public static function goOffers ($strConnectionName) {
        DbFactory::setDefaultConnection($strConnectionName);
        $objDb = DbFactory::getInstance();
        $numLimit = 5000;
        $numOffset = 0;
        echo 'in';
        $strQSelect = <<<EOF
SELECT id, source_blog_id, slug_pl AS slug, mimetype  
FROM offer 
LIMIT :limit 
OFFSET :offset
EOF;
        $objSqlSelect = $objDb->prepare($strQSelect);
        
        $strQUpdate = <<<EOF
UPDATE offer 
SET thumb_path = :thumb_path, 
    thumb_filename = :thumb_filename 
WHERE id = :id       
EOF;
        $objSqlUpdate = $objDb->prepare($strQUpdate);
        
        do {
            $objSqlSelect->execute(array(
                ':limit' => $numLimit, 
                ':offset' => $numOffset
            )) ;
            $arrOffers = $objSqlSelect->fetchAll();
            echo __LINE__.': '.count($arrOffers).PHP_EOL;
            if (empty($arrOffers)) {
                break;
            }
            
            foreach ($arrOffers as $arrOffer) {
                $strFilename = sprintf('%s-%d.%s', $arrOffer['slug'], $arrOffer['id'], File::mimetypeToExtension($arrOffer['mimetype']));
                $strPath = sprintf('/%s', $arrOffer['id']%5000);
                echo __LINE__.': check file '.dirname(__FILE__).'/../../../public_html/p'.$strPath.'/'.$strFilename.PHP_EOL;
                if (file_exists(dirname(__FILE__).'/../../../public_html/p'.$strPath.'/'.$strFilename)) {
                    echo __LINE__.': update'.PHP_EOL;
                    $objSqlUpdate->execute(array(
                        ':thumb_path' => $strPath, 
                        ':thumb_filename' => $strFilename,
                        ':id' => $arrOffer['id']
                    ));
                }
            }
            
            $numOffset += $numLimit;
        } while (true);
        
        unset($objDb);
    }
    
    public static function goStoryImages ($strConnectionName) {
        DbFactory::setDefaultConnection($strConnectionName);
        $objDb = DbFactory::getInstance();
        $numLimit = 5000;
        $numOffset = 0;
        
        $strQSelect = <<<EOF
SELECT so.id, so.ordering, so.artifact_id, so.mimetype, o.slug_pl AS slug 
FROM story_photo AS so 
JOIN offer AS o ON o.id = so.artifact_id 
WHERE so.type = :type_image  
LIMIT :limit 
OFFSET :offset
EOF;
        $objSqlSelect = $objDb->prepare($strQSelect);
        
        $strQUpdate = <<<EOF
UPDATE story_photo 
SET image_path = :image_path, 
    image_filename = :image_filename 
WHERE id = :id       
EOF;
        $objSqlUpdate = $objDb->prepare($strQUpdate);
        
        do {
            $objSqlSelect->execute(array(
                ':type_image' => 'image', 
                ':limit' => $numLimit, 
                ':offset' => $numOffset
            )) ;
            $arrOffers = $objSqlSelect->fetchAll();
            if (empty($arrOffers)) {
                break;
            }
            
            foreach ($arrOffers as $arrOffer) {
                $strFilename = sprintf('%s-%d.%s', $arrOffer['slug'], $arrOffer['ordering'], File::mimetypeToExtension($arrOffer['mimetype']));
                $strPath = sprintf('/%s', $arrOffer['artifact_id']%5000);
                if (file_exists(dirname(__FILE__).'/../../../public_html/p'.$strPath.'/'.$strFilename)) {
                    $objSqlUpdate->execute(array(
                        ':image_path' => $strPath, 
                        ':image_filename' => $strFilename,
                        ':id' => $arrOffer['id']
                    ));
                }
            }
            
            $numOffset += $numLimit;
        } while (true);
        
        unset($objDb);
    }
    
}
