<?php
namespace backend\artifact;

use webcitron\Subframe\Model;
use backend\DbFactory;
use webcitron\Subframe\Application;
use webcitron\Subframe\Controller;

class ImageController extends Controller
{
    
    public static function getResults ($strQuery, $numTagId, $numImagesPerPage, $numOffset = 0) {
        $arrResult = array(
            'arrResults' => array(), 
            'arrPagination' => array()
        );
        $objDb = DbFactory::getInstance();
        
        $arrQueryTokens = array();
        $arrQueryTokens[] = 'FROM offer AS o';
        if (!empty($numTagId)) {
            $arrQueryTokens[] = 'JOIN offer_tag AS ot ON ot.offer_id = o.id';
        }
        $arrQueryTokens[] = 'WHERE true';
        if (!empty($strQuery)) {
            $arrQueryTokens[] = sprintf(
                "AND (search_data_pl IS NOT NULL AND search_data_pl @@ plainto_tsquery(%s))", 
                $objDb->quote($strQuery)
            );
        }
        if (!empty($numTagId)) {
            $arrQueryTokens[] = sprintf(
                "AND (ot.tag_id = %d)", 
                $numTagId
            );
        }
        
        $strQ = sprintf(
            "SELECT o.title_pl AS title, o.slug_pl AS slug, o.id, o.width, o.height %s LIMIT :limit OFFSET :offset", 
            join(' ', $arrQueryTokens)
        );
//        echo $strQ;
//        exit();
        
        $objSth = $objDb->prepare($strQ);
        $arrParams = array(
//            ':tsvector_query' => $strQuery, 
            ':limit' => $numImagesPerPage, 
            ':offset' => $numOffset
        );
        $objSth->execute($arrParams);
        $arrResult['arrResults'] = $objSth->fetchAll();
        
        if (!empty($arrResult['arrResults'])) {
            
            foreach ($arrResult['arrResults'] as & $arrRes) {
                $numPhotoDirectory = $arrRes['id'] % 5000;
                $arrRes['href'] = \webcitron\Subframe\Url::route('Details', array(
                    'slug' => $arrRes['slug'], 
                    'id' => $arrRes['id']
                ));
                $arrRes['image_src'] = \webcitron\Subframe\Url::route('ImageDirect', array(
                    'directory' => $numPhotoDirectory, 
                    'slug' => $arrRes['slug'], 
                    'id' => $arrRes['id']
                ));
                
            }
        }
        
        $strQ2 = sprintf(
            "SELECT count(1) AS total %s", 
            join(' ', $arrQueryTokens)
        );
        $objSth2 = $objDb->prepare($strQ2);
        $objSth2->execute(array(
//            ':tsvector_query' => $strQuery 
        ));
        $arrPagination = $objSth2->fetch();
        $arrResult['arrPagination']['numTotalElements'] = $arrPagination['total'];
        
        return self::answer($arrResult);
    }
    
    public static function getActiveCount () {
        $objCache = new \backend\Cache();
        if(Application::environment() !== 'PRODUCTION') {
            $objCache->disable();
        }
        $numOffersCount = $objCache->get('site-stats-offers-count-pl');
        if ($numOffersCount === false) {
            $objDb = DbFactory::getInstance();
            $strQ = "SELECT COUNT(1) FROM offer WHERE search_data_pl IS NOT NULL";
            $objSth = $objDb->prepare($strQ);
            $objSth->execute();
            $numOffersCount = $objSth->fetch(\PDO::FETCH_COLUMN, 0);
            $objCache->set('site-stats-offers-count-pl', $numOffersCount, (60*60));
        }
        
        return self::answer($numOffersCount);
    }
    
    public static function getMostViewed($numLimit)
    {
        $objCache = new \backend\Cache();
        if(Application::environment() !== 'PRODUCTION') {
            $objCache->disable();
        }
        $arrOffers = $objCache->get('best-viewed-offers');
        if ($arrOffers === false) {
            $objDb = DbFactory::getInstance();
            $strQ = "SELECT (shows_count+shows_count_increaser) AS shows_fake, "
                        . "id, "
                        . "slug_pl AS slug, "
                        . "title_pl AS title, "
                        . "(id % 5000) AS photo_directory, "
                        . "width, "
                        . "height "
                    . "FROM offer "
                    . "ORDER BY shows_fake DESC "
                    . "LIMIT 20";
            $objSth = $objDb->prepare($strQ);
            $objSth->execute();
            $arrOffers = $objSth->fetchAll();
            $objCache->set('best-viewed-offers', $arrOffers, (60*15));
        }
        
        return self::answer($arrOffers);
    }
    
    public static function getImageUrl($numId) {
        $objDb = DbFactory::getInstance();
        $objSth = $objDb->prepare("SELECT image_source FROM offer WHERE id = :id ");
        $objSth->execute(array(':id' => $numId));
        $arrResult = $objSth->fetch();
        
        return self::answer($arrResult['image_source']);
    }
    
    
    public static function getVisibleCount() {
        $objCache = new \backend\Cache();
        if(Application::environment() !== 'PRODUCTION') {
            $objCache->disable();
        }
        $numOffersCount = $objCache->get('site-stats-offers-count-pl');
        if ($numOffersCount === false) {
            $objDb = DbFactory::getInstance();
            $strQ = "SELECT COUNT(1) FROM offer WHERE search_data_pl IS NOT NULL";
            $objSth = $objDb->prepare($strQ);
            $objSth->execute();
            $numOffersCount = $objSth->fetch(\PDO::FETCH_COLUMN, 0);
            $objCache->set('site-stats-offers-count-pl', $numOffersCount, (60*60));
        }
        
        return self::answer($numOffersCount);
    }
    
    
    public static function increaseShowsCount ($numId) {
        $objDb = DbFactory::getInstance();
        $objSth = $objDb->prepare("UPDATE offer SET shows_count = (shows_count+1) WHERE id = :id ");
        $objSth->execute(array(':id' => $numId));

        return self::answer(true);
    }
    
    public static function fillImageInfo($numId, $arrData) {
        $objDb = DbFactory::getInstance();
        $arrSets = array();
        foreach ($arrData as $strName => $strValue) {
            $arrSets[] = sprintf('%s = %s', 
                $strName, 
                $objDb->quote($strValue)
            );
        }
        $objDb = DbFactory::getInstance();
        $objSth = $objDb->prepare(sprintf(
            "UPDATE offer SET %s WHERE id = :id ", 
            join(', ', $arrSets)
        ));
        $objSth->execute(array(
            ':id' => $numId
        ));
        return self::answer(true);
    }
    
    public static function getDetails ($numId) {
        $objDb = DbFactory::getInstance();
        $strQ = "SELECT title_pl AS title, "
                    . "slug_pl AS slug, "
                    . "description, "
                    . "(id % 5000) AS photo_directory, "
                    . "(shows_count + shows_count_increaser) AS shows_count_fake, "
                    . "round((image_weight/1024)) AS image_weight_kb, "
                    . "width, "
                    . "height "
                . "FROM offer "
                . "WHERE id = :id ";
        $objSth = $objDb->prepare($strQ);
        $objSth->execute(array(':id' => $numId));
        
        $arrResult = $objSth->fetch();
        return self::answer($arrResult);
    }
    
}