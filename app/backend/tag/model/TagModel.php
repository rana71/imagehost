<?php namespace backend\tag\model;

use \backend\DbFactory;
use backend\String;

class TagModel { 
    
    private $objDb = null;
    
    public function __construct () {
        $this->objDb = DbFactory::getInstance();
        $this->objDb->exec("SET SCHEMA 'artifacts'");
    }
    
    public function saveNewTag ($strTitle, $strSlug) {
        $strQ = <<<EOF
INSERT INTO tag (
    slug, title 
) VALUES (
    :slug, :title 
) RETURNING id, title, slug, is_removed, removed_since, elements_count 
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':slug' => $strSlug, 
            ':title' => $strTitle
        ));
        $arrAddedTag = $objSth->fetch();
        
        return $arrAddedTag;
    }
    
    public function changeElementsCount ($numTagId, $numElementsChange) {
        $strQ = <<<EOF
UPDATE tag SET elements_count = (elements_count + :elements_change) WHERE id = :tag_id 
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':elements_change' => $numElementsChange, 
            ':tag_id' => $numTagId
        ));
        
    }
    
    public function extractTagsFromString ($strString) {
        $arrTags = array();
        $arrAddedSlugs = array();
        $strString = mb_strtolower($strString, 'UTF-8');
        $arrStringTokens = preg_split('/[^1234567890qwertyuioplkjhgfdsazxcvbnmęółśążźćń]+/', $strString);
        if (!empty($arrStringTokens)) {
            foreach ($arrStringTokens as $strTagName) {
                $strTagSlug = String::slug($strTagName);
                
                if (strlen($strTagSlug) >=3 && strlen($strTagName) >= 3 && !in_array($strTagSlug, $arrAddedSlugs)) {
                    $arrTags[] = array(
                        'slug' => $strTagSlug, 
                        'title' => $strTagName
                    );
                    $arrAddedSlugs[] = $strTagSlug;
                }
            }
        }
        return $arrTags;
    }
    
    public  function getBasicStats () {
        $strQ = "SELECT COUNT(1) FROM tag WHERE is_removed = FALSE";
        
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute();
        $numCount = $objSth->fetch(\PDO::FETCH_COLUMN, 0);
        
        $arrStats = array(
            'numTotal' => $numCount
        );
        
        return $arrStats;
    }
    
    public function getBiggest ($numLimit) {
        $strQ = <<<EOF
SELECT title, slug, elements_count 
FROM tag 
WHERE is_removed = FALSE 
ORDER BY elements_count DESC 
LIMIT :limit
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':limit' => $numLimit
        ));
        $arrTags = $objSth->fetchAll();
        return $arrTags;
    }
    
    public function getTagBySlug ($strTagSlug, $boolGetRemoved = false) {
        $strQ = <<<EOF
SELECT id, title, slug, is_removed, removed_since, elements_count 
FROM tag 
WHERE slug = :tag_slug 
EOF;
        if ($boolGetRemoved === false) {
            $strQ .= ' AND is_removed = false';
        }
        
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':tag_slug' => $strTagSlug
        ));
        $arrTag = $objSth->fetch();
        
        return $arrTag;
    }
    
    public function getArtifactTags ($numArtifactId) {
        $strQ = <<<EOF
SELECT t.title, t.slug 
FROM item AS i 
JOIN tag AS t ON t.id = ANY(i.tags) 
WHERE i.id = :artifact_id 
    AND t.is_removed IS FALSE
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':artifact_id' => $numArtifactId
        ));
        $arrTags = $objSth->fetchAll();
        
        return $arrTags;
    }
    
    public function markAsRemoved ($numTagId) {
        $strQ = <<<EOF
UPDATE tag 
SET is_removed = true, 
    removed_since = (NOW() AT TIME ZONE 'UTC') 
WHERE id = :tag_id 
RETURNING id, slug, removed_since 
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':tag_id' => $numTagId
        ));
        $arrRemovedTag = $objSth->fetch();
        
        return $arrRemovedTag;
    }
    
    public function unmarkAsRemoved ($numTagId) {
        $strQ = <<<EOF
UPDATE tag 
SET is_removed = false, 
    removed_since = NULL 
WHERE id = :tag_id 
RETURNING id, slug 
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':tag_id' => $numTagId
        ));
        $arrUnemovedTag = $objSth->fetch();
        
        return $arrUnemovedTag;
    }
    
    
    public function getLastRemoved ($numLimit = -1) {
        $strQ = <<<EOF
SELECT id, slug, title, removed_since 
FROM tag 
WHERE is_removed = true 
ORDER BY removed_since DESC 
EOF;
        if ($numLimit === -1) {
            $objSth = $this->objDb->prepare($strQ);
            $objSth->execute();
        } else {
            $strQ .= ' LIMIT :limit';
            $objSth = $this->objDb->prepare($strQ);
            $objSth->execute(array(
                ':limit' => $numLimit
            ));
        }
        
        $arrRemovedTags = $objSth->fetchAll();
        
        return $arrRemovedTags;
    }
    
    public function getTagsWihmMinimumElementsCount ($numMinElements, $numOffset, $numLimit) {
        $strQ = <<<EOF
SELECT slug 
FROM tag 
WHERE is_removed = FALSE 
    AND elements_count >= :min_elements
LIMIT :limit 
OFFSET :offset
EOF;

        $objSth = $this->objDb->prepare($strQ);
        $objSth->bindValue(':min_elements', $numMinElements, \PDO::PARAM_INT);
        $objSth->bindValue(':limit', $numLimit, \PDO::PARAM_INT);
        $objSth->bindValue(':offset', $numOffset, \PDO::PARAM_INT);
        $objSth->execute();
        $arrTags = $objSth->fetchAll();
        
        return $arrTags;
    }
    
    public function getTags ($numOffset, $numLimit) {
        $strQ = <<<EOF
SELECT slug 
FROM tag 
WHERE is_removed = FALSE 
LIMIT :limit 
OFFSET :offset
EOF;

        $objSth = $this->objDb->prepare($strQ);
        $objSth->bindValue(':limit', $numLimit, \PDO::PARAM_INT);
        $objSth->bindValue(':offset', $numOffset, \PDO::PARAM_INT);
        $objSth->execute();
        $arrTags = $objSth->fetchAll();
        
        return $arrTags;
    }
    
}