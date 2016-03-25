<?php namespace backend\searcher\model;

use \backend\DbFactory;

class QueryModel { 
    
    private $objDb = null;
    
    public function __construct () {
        $this->objDb = DbFactory::getInstance();
        $this->objDb->exec("SET SCHEMA 'searcher'");
    }
    
    public function markAsUsedById ($numQueryId) {
        $strQ = <<<EOF
UPDATE searcher.query SET last_use_timestamp = CURRENT_TIMESTAMP WHERE id = :id
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':id' => $numQueryId,
        ));
    }
    
    public function getNotEmptyQueries ($numOffset, $numLimit) {
        $strQ = <<<EOF
SELECT 
    slug 
FROM query 
WHERE is_have_results = TRUE 
LIMIT :limit 
OFFSET :offset
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->bindValue(':limit', $numLimit, \PDO::PARAM_INT);
        $objSth->bindValue(':offset', $numOffset, \PDO::PARAM_INT);
        $objSth->execute();
        
        $arrQueries = $objSth->fetchAll();
        return $arrQueries;
    }
    
    public function getLastUsed ($numLimit) {
        $strQ = <<<EOF
SELECT title, slug, last_use_timestamp 
FROM query 
WHERE is_have_results = TRUE 
ORDER BY last_use_timestamp DESC NULLS LAST 
LIMIT :limit;
EOF;
        
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':limit' => $numLimit
        ));
        $arrResults = $objSth->fetchAll();
        
        return $arrResults;
    }
    
    public function getBySlug ($strSlug) {
        $strQ = <<<EOF
SELECT id, title, slug, last_use_timestamp 
FROM query 
WHERE slug = :slug
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':slug' => $strSlug
        ));
        $arrQuery = $objSth->fetch();
        
        return $arrQuery;
    }
    
    public function add ($strQuery, $strSlug) {
        $strQ = <<<EOF
INSERT INTO query (
    slug, title
) VALUES (
    :slug, :title            
) RETURNING title, slug
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $arrOptions = array(
            ':slug' => $strSlug, 
            ':title' => $strQuery
        );
        $objSth->execute($arrOptions);
        $arrQuery = $objSth->fetch();
        
        return $arrQuery;
    }
    
    public function setIsHaveResults ($numId, $boolIsHave) {
        $strQ = <<<EOF
UPDATE searcher.query 
SET is_have_results = :is_have 
WHERE id = :query_id
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->bindValue(':is_have', $boolIsHave, \PDO::PARAM_BOOL);
        $objSth->bindValue(':query_id', $numId, \PDO::PARAM_INT);
        $objSth->execute();
    }
}