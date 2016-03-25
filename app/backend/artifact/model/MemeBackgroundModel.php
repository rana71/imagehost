<?php namespace backend\artifact\model;

use backend\DbFactory;

class MemeBackgroundModel { 
    
    private $objDb = null;
    
    public function __construct () {
        $this->objDb = DbFactory::getInstance();
        $this->objDb->exec("SET SCHEMA 'artifacts'");
    }
    
    
    private function getTsVector($strString) {
        $this->objDb->exec("SET SCHEMA 'public'");
        $strQ = <<<EOF
SELECT to_tsvector('polish', :search_phrase)
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->bindValue(':search_phrase', $strString, \PDO::PARAM_STR);
        $objSth->execute();
        $strTsVector = $objSth->fetch(\PDO::FETCH_COLUMN, 0);
        $this->objDb->exec("SET SCHEMA 'artifacts'");
        return $strTsVector;
    }
    
    public function insert ($strFilename, $strPath, $strSearchData) {
        $strQ = <<<EOF
INSERT INTO meme_background (image_filename, image_path, search_data ) 
VALUES (:image_filename, :image_path, to_tsvector('polish', :search_ts_vector)) 
RETURNING id;
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->bindValue(':image_filename', $strFilename, \PDO::PARAM_STR);
        $objSth->bindValue(':image_path', $strPath, \PDO::PARAM_STR);
        
        $strTsVector = $this->getTsVector($strSearchData);
        $objSth->bindValue(':search_ts_vector', $strTsVector, \PDO::PARAM_STR);
        $objSth->execute();
        $numBackgroundId = $objSth->fetch(\PDO::FETCH_COLUMN, 0);
        
        return $numBackgroundId;
    }
    
    public function getById ($numId) {
        $strQ = <<<EOF
SELECT  image_path , image_filename 
FROM meme_background 
WHERE id = :id
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->bindValue(':id', $numId, \PDO::PARAM_INT);
        $objSth->execute();
        $arrMemeBackground = $objSth->fetch();
        
        return $arrMemeBackground;
    }
    
    public function getMostPopular ($numLimit, $numForceFirstMemeBackgroundId = 0) {
        $numForceFirstMemeBackgroundId = intval($numForceFirstMemeBackgroundId);
        if (empty($numForceFirstMemeBackgroundId)) {
            $strQ = <<<EOF
SELECT id, 
    CONCAT(
        image_path, 
        '/', 
        image_filename
    ) AS meme_background_url  
FROM meme_background 
ORDER BY uses_count DESC 
LIMIT :limit 
EOF;
            $objSth = $this->objDb->prepare($strQ);
            $objSth->bindValue(':limit', $numLimit, \PDO::PARAM_INT);
        } else {
            $strQ = <<<EOF
SELECT id, 
    CONCAT(
        image_path, 
        '/', 
        image_filename
    ) AS meme_background_url 
FROM meme_background 
WHERE id = :default_id 
    OR TRUE 
ORDER BY (CASE WHEN id = :default_id THEN 1 ELSE 0 END) DESC, 
    uses_count DESC 
LIMIT :limit 
EOF;
            $objSth = $this->objDb->prepare($strQ);
            $objSth->bindValue(':default_id', $numForceFirstMemeBackgroundId, \PDO::PARAM_INT);
            $objSth->bindValue(':limit', $numLimit, \PDO::PARAM_INT);
        }
        $objSth->execute();
        $arrBackgrounds = $objSth->fetchAll();
        
        if (!empty($arrBackgrounds)) {
            foreach ($arrBackgrounds as & $arrBackground) {
                $arrBackground['meme_background_url'] = ArtifactModel::replaceS3Doman($arrBackground['meme_background_url']);
            }
        }
        
        return $arrBackgrounds;
    }
    
    public function getMostPopularBySearchString ($strSearchString, $numLimit) {
        $strQPattern = <<<EOF
SELECT id, 
    CONCAT(
        image_path, 
        '/', 
        image_filename
    ) AS meme_background_url 
FROM meme_background 
WHERE search_data @@ '%s' 
ORDER BY uses_count DESC 
LIMIT :limit 
EOF;
        $strQ = sprintf($strQPattern, str_replace(' ', ' | ', $strSearchString));
        $objSth = $this->objDb->prepare($strQ);
        $objSth->bindValue(':limit', $numLimit, \PDO::PARAM_INT);
        $objSth->execute();
        $arrBackgrounds = $objSth->fetchAll();
        
        if (!empty($arrBackgrounds)) {
            foreach ($arrBackgrounds as & $arrBackground) {
                $arrBackground['meme_background_url'] = ArtifactModel::replaceS3Doman($arrBackground['meme_background_url']);
            }
        }
        
        return $arrBackgrounds;
    }
    
    /*
     * 
    
    public function getImagesBySearchString ($strSearchString, $numLimit) {
        $strQPattern = <<<EOF
SELECT ieid.image_path, ieid.image_filename, ieid.mimetype  
FROM item_element_image_data AS ieid 
JOIN item_element AS ie ON ie.id = ieid.item_element_id 
JOIN item AS i ON i.id = ie.item_id 
WHERE is_on_homepage IS TRUE 
    AND ieid.mimetype = 'image/jpeg' 
    AND i.search_data @@ '%s'
LIMIT :limit;
EOF;
        $strQ = sprintf($strQPattern, str_replace(' ', ' | ', $strSearchString));
        $objSth = $this->objDb->prepare($strQ);
        $objSth->bindValue(':limit', $numLimit, \PDO::PARAM_INT);
        $objSth->execute();
        $arrRes = $objSth->fetchAll();
        
        return $arrRes;
    }
     */
}