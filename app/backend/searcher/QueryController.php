<?php
namespace backend\search;

use backend\DbFactory;
use webcitron\Subframe\Controller;

class QueryController extends Controller
{
    
    
    
    public static function markAsUsed($strQuery)
    {
        $objDb = DbFactory::getInstance();
        $strQ = "UPDATE query SET last_use = CURRENT_TIMESTAMP WHERE query = :query";
        $objSth = $objDb->prepare($strQ);
        $objSth->execute(array(
            ':query' => $strQuery,
        ));
        return self::answer(true);
    }
    
    public static function setQueryHaveResults($strSlug, $boolHaveResults) {
        $objDb = DbFactory::getInstance();
        $strQ = "UPDATE query SET have_results = :is_have WHERE slug = :slug";
        $objSth = $objDb->prepare($strQ);
        $arrParams = array(
            ':is_have' => $boolHaveResults === true ? 'TRUE' : 'FALSE', 
            ':slug' => $strSlug,
        );
        $objSth->execute($arrParams);
        return self::answer(true);
    }
    
    public static function get($strFieldSearch, $strValueSearch, $arrFieldsToGet) {
        $strQ = sprintf(
            "SELECT %s FROM query WHERE %s = :search_value", 
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

    public static function add($strQuery, $strSlug = '')
    {
        $objDb = DbFactory::getInstance();
        if (empty($strSlug)) {
            $strSlug = \backend\String::slug($strQuery);
        }
        $strQ = "INSERT INTO query (query, slug, last_use) VALUES (:query, :slug, CURRENT_TIMESTAMP)";
        $objSth = $objDb->prepare($strQ);
        $objSth->execute(array(
            ':query' => $strQuery,
            ':slug' => $strSlug
        ));
        $arrResult = array(
            'query' => $strQuery,
            'slug' => $strSlug
        );
        return self::answer($arrResult);
    }

    public static function createUniqueSlug($strQuery)
    {
//        $objDb = DbFactory::getInstance();
        $strSlug = Tools::slug($strQuery);
//        $strQ = "SELECT * FROM query WHERE slug LIKE :slug";
//        $objSth = $objDb->prepare($strQ);
//        $objSth->execute(array(
//            ':slug' => $strSlug.'%'
//        ));
//        $arrResults = $objSth->fetchAll();
//        
//        if (!empty($arrResults)) {
//            echo '<pre>';
//            echo $strQuery.'s';
//            print_r($arrResults);
//            
//            exit('creating slug ..!');
//        }
        return self::answer($strSlug);
    }
    
}