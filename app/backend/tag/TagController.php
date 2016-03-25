<?php
namespace backend\tag;

use webcitron\Subframe\Controller;
use backend\DbFactory;
use webcitron\Subframe\Application;
use backend\tag\model\TagModel;

class TagController extends Controller
{
    
    public static function get($strFieldSearch, $strValueSearch, $arrFieldsToGet) {
        $strQ = sprintf(
            "SELECT %s FROM tag WHERE %s = :search_value", 
            join(', ', $arrFieldsToGet), 
            $strFieldSearch
        );
        $objDb = DbFactory::getInstance();
        $objSth = $objDb->prepare($strQ);
        $objSth->execute(array(
            ':search_value' => $strValueSearch
        ));
        $arrResult = $objSth->fetch();
        
        return self::answer($arrResult);
    }
    
    public static function suggest ($strQuery) {
        $arrTags = array();
        $strQuery = trim($strQuery);
        if (strlen($strQuery) >= 3) {
            $objDb = DbFactory::getInstance();
            $strQ = sprintf(
                "SELECT name FROM tag WHERE removed = FALSE AND name LIKE %s ORDER BY elements_count DESC LIMIT 10", 
                $objDb->quote($strQuery.'%')
            );
            $objSth = $objDb->prepare($strQ);
            $objSth->execute();
            $arrTags = $objSth->fetchAll(\PDO::FETCH_COLUMN, 0);
        }
        return self::answer($arrTags);
    }
    
    
    public static function undoDelete ($strTagSlug) {
        
        $arrResult = array();
        $objModel = new model\TagModel();
        $arrTag = $objModel->getTagBySlug($strTagSlug, true);
        if (!empty($arrTag)) {
            if ($arrTag['is_removed'] == false) {
                $arrResult['arrErrors'] = array(sprintf('Tag %s nie jest usnięty', $arrTag['title']));
            } else {
                $arrRemovedTag = $objModel->unmarkAsRemoved($arrTag['id']);
                $arrResult = array(
                    'strSlug' => $arrRemovedTag['slug'], 
                    'numId' => $arrRemovedTag['id']
                );
            }
        } else {
            $arrResult['arrErrors'] = array('Nie odnaleziono taga');
        }
        
        return self::answer($arrResult);
    }
    
    public static function markToDelete ($strTagSlug) {
        
        $arrResult = array();
        $objModel = new model\TagModel();
        $arrTag = $objModel->getTagBySlug($strTagSlug, true);
        if (!empty($arrTag)) {
            if ($arrTag['is_removed'] == true) {
                $arrResult['arrErrors'] = array(sprintf('Tag %s jest już usnięty', $arrTag['title']));
            } else {
                $arrRemovedTag = $objModel->markAsRemoved($arrTag['id']);
                $arrResult = array(
                    'strSlug' => $arrRemovedTag['slug'], 
                    'strDate' => $arrRemovedTag['removed_since'], 
                    'numId' => $arrRemovedTag['id']
                );
            }
        } else {
            $arrResult['arrErrors'] = array('Nie odnaleziono taga');
        }
        
        return self::answer($arrResult);
    }
    
    public static function getAssignedCount () {
        $objCache = new \backend\Cache();
        if(Application::environment() !== 'PRODUCTION') {
            $objCache->disable();
        }
        $numTagsCount = $objCache->get('site-stats-tags-count-pl');
        if ($numTagsCount === false) {
            $objDb = DbFactory::getInstance();
            $strQ = "SELECT COUNT(1) FROM tag WHERE WHERE removed = FALSE elements_count > 0";
            $objSth = $objDb->prepare($strQ);
            $objSth->execute();
            $numTagsCount = $objSth->fetch(\PDO::FETCH_COLUMN, 0);
            $objCache->set('site-stats-tags-count-pl', $numTagsCount, (60*60));
        }
        
        return self::answer($numTagsCount);
    }
    
    public static function getOfferTags ($numOfferId) {
        $strQ = "SELECT t.name, t.slug FROM offer_tag AS ot JOIN tag AS t ON t.id = ot.tag_id WHERE ot.offer_id = :offer_id";
        $objDb = DbFactory::getInstance();
        $objSth = $objDb->prepare($strQ);
        $objSth->execute(array(
            ':offer_id' => $numOfferId
        ));
        $arrTags = $objSth->fetchAll();
        return self::answer($arrTags);
    }

    public static function getBasicStats()
    {
        $arrResult = array();
        $arrErrors = array();
        $objTagModel = new TagModel();
        $arrTagsBasicStats = $objTagModel->getBasicStats();
        $arrResult[] = $arrTagsBasicStats;
        return self::answer($arrResult, $arrErrors);
    }
    
}