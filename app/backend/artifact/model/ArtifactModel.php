<?php namespace backend\artifact\model;

use backend\DbFactory;
use \backend\File;
use backend\String;
use backend\YouTube;
use backend\StorageModel;
use backend\utils\BBCodeParser;
use backend\tag\model\TagModel;
use backend\artifact\MemeBackgroundController;
use backend\libs\GDText;
use webcitron\Subframe\StorageMemcache;
use webcitron\Subframe\Application;

class ArtifactModel
{

    private $objDb = null;

    const ITEM_TYPE_IMAGE = 1;
    const ITEM_TYPE_YTVIDEO = 2;
    const ITEM_TYPE_MEM = 3;
    const TIME_WITH_404_AFTER_REMOVE = '-1 month';

    public static $arrBucketsUrls = array(
        'http://s3.eu-central-1.amazonaws.com/i1.imged.pl' => 'http://i1.imged.pl', 
        'https://s3.eu-central-1.amazonaws.com/i1.imged.pl' => 'http://i1.imged.pl', 
        'http://s3.eu-central-1.amazonaws.com/i.imged.pl' => 'http://i.imged.pl', 
        'https://s3.eu-central-1.amazonaws.com/i.imged.pl' => 'http://i.imged.pl' 
    );

    
    public function __construct()
    {
        $this->objDb = DbFactory::getInstance();
//        $this->objDb->exec("SET search_path TO 'artifacts'");
        $this->objDb->exec("SET SCHEMA 'artifacts'");
    }
    
    public static function replaceS3Doman ($strUrl) {
        $strReturn = str_replace(
            array_keys(self::$arrBucketsUrls),
            array_values(self::$arrBucketsUrls), 
            $strUrl
        );
        
        return $strReturn;
    }
    
    public function getArtifaceUploaderIp ($numArtifactId) {
        $strQPattern = <<<EOF
SELECT uploaded_user_ip 
FROM artifacts.%s 
WHERE item_id = :item_id 
LIMIT 1
EOF;
        $numPartitionNumber = floor($numArtifactId/1000000)+1;
        $numPartitionName = 'item_info_p_'.$numPartitionNumber;
        $strQ = sprintf($strQPattern, $numPartitionName);
        $objSth = $this->objDb->prepare($strQ);
        $objSth->bindValue(':item_id', $numArtifactId, \PDO::PARAM_INT);
        $objSth->execute();
        $strIp = $objSth->fetch(\PDO::FETCH_COLUMN, 0);
        return $strIp;
    }
    
    public function getAllegroSellerId ($arrArtifactId) {
        $strQ = <<<EOF
SELECT COALESCE(allegro_user_id, 0) 
FROM artifacts.item_allegro_info 
WHERE item_id = :item_id
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->bindValue(':item_id', $arrArtifactId, \PDO::PARAM_INT);
        $objSth->execute();
        $numSellerId = $objSth->fetch(\PDO::FETCH_COLUMN, 0);
        return $numSellerId;
    }
    
    public function countAllegroSellerOffers ($numSellerId) {
        $strQ = <<<EOF
SELECT count(1) 
FROM artifacts.item_allegro_info 
WHERE allegro_user_id = :seller_id 
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->bindValue(':seller_id', $numSellerId, \PDO::PARAM_INT);
        $objSth->execute();
        $numOffersCount = $objSth->fetch(\PDO::FETCH_COLUMN, 0);
        return $numOffersCount;
    }
    
    public function isSellerBanned ($numSellerId) {
        $strQ = <<<EOF
SELECT count(1) 
FROM grabber.blocked_allegro_user 
WHERE allegro_user_id = :seller_id 
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->bindValue(':seller_id', $numSellerId, \PDO::PARAM_INT);
        $objSth->execute();
        $boolIsBlocked = (intval($objSth->fetch(\PDO::FETCH_COLUMN, 0)) > 0);
        return $boolIsBlocked;
    }
    
    public function banSeller ($numSellerId) {
        $strQ = "INSERT INTO grabber.blocked_allegro_user (allegro_user_id) VALUES (:seller_id)";
        $objSth = $this->objDb->prepare($strQ);
        $objSth->bindValue(':seller_id', $numSellerId, \PDO::PARAM_INT);
        $objSth->execute();
        return true;
    }
    
    public function getAllegroSellerOffers ($numSellerId, $numLimit) {
        $arrUrls = array();
        $strQ = <<<EOF
SELECT i.slug, i.id, i.title 
FROM artifacts.item_allegro_info AS iai 
LEFT JOIN artifacts.item AS i ON i.id = iai.item_id 
WHERE iai.allegro_user_id = :seller_id  
LIMIT :limit
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->bindValue(':seller_id', $numSellerId, \PDO::PARAM_INT);
        $objSth->bindValue(':limit', $numLimit, \PDO::PARAM_INT);
        $objSth->execute();
        $arrSet = $objSth->fetchAll();
        
        $strUrlBase = str_replace('admin.', '', Application::url());
        
        if (!empty($arrSet)) {
            foreach ($arrSet as $arrOffer) {
                $arrUrls[] = array(
                    'strTitle' => $arrOffer['title'], 
                    'strUrl' => sprintf('%s/%s-%d.html', $strUrlBase, $arrOffer['slug'], $arrOffer['id'])
                );
            }
        }
        return $arrUrls;
    }

    public function getArtifactsUserIds($arrArtifactsIds)
    {
        $strQ = sprintf('SELECT id, author_account_id FROM artifacts.item WHERE id IN (%s)', join(', ', $arrArtifactsIds));
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute();
        $arrItemsUsers = $objSth->fetchAll();
        return $arrItemsUsers;
    }

    public function getImageElementBase64($numItemElementId)
    {
        $strQ = <<<EOF
SELECT height, width, mimetype, image_filename, image_path,     
CONCAT (image_path, '/', image_filename) as image_url 
FROM item_element_image_data 
WHERE item_element_id = :item_element_id
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->bindValue(':item_element_id', $numItemElementId, \PDO::PARAM_INT);
        $objSth->execute();
        $arrItem = $objSth->fetch();
        
        $arrItem['image_url'] = self::replaceS3Doman($arrItem['image_url']);
        
        return $arrItem;
    }

    public function refreshStats()
    {
        $strQ = <<<EOF
UPDATE stats 
SET items_count = (SELECT count(1) FROM item WHERE is_removed = FALSE AND is_public = TRUE), 
    items_added_this_month = (SELECT count(1) FROM item WHERE is_removed = FALSE AND is_public = TRUE AND add_timestamp >= (NOW() AT TIME ZONE 'UTC' - '1 month'::interval))
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute();
    }

    public function getImagesElementsToItemIdBetween($numMinId, $numMaxId)
    {
        $strQ = <<<EOF
SELECT ie.item_id, 
    ie.description, 
    ie.title, 
    ( 
        CASE 
            WHEN ie.type = 1 THEN CONCAT(ei.image_path, '/', ei.image_filename)
            WHEN ie.type = 3 THEN CONCAT(em.image_path, '/', em.image_filename) 
        END 
    ) AS image_url 
FROM item_element AS ie 
LEFT JOIN item_element_image_data AS ei ON ei.item_element_id = ie.id 
LEFT JOIN item_element_mem_data AS em ON em.item_element_id = ie.id  
WHERE ie.item_id >= :min_id 
    AND ie.item_id <= :max_id 
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->bindValue(':min_id', $numMinId, \PDO::PARAM_INT);
        $objSth->bindValue(':max_id', $numMaxId, \PDO::PARAM_INT);
        $objSth->execute();
        $arrItems = $objSth->fetchAll();
        if (!empty($arrItems)) {
            foreach ($arrItems as & $arrArtifact) {
                $arrArtifact['image_url'] = self::replaceS3Doman($arrArtifact['image_url']);
            }
        }
        
        return $arrItems;
    }

    public function getStats()
    {
        $strQ = <<<EOF
SELECT count(1) AS total 
FROM item 
WHERE is_public IS TRUE 
    AND is_removed IS FALSE
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute();
        $arrRes = $objSth->fetchAll();

        return $arrRes;

    }

    public function getStatsStatic()
    {
        $strQ = <<<EOF
SELECT * FROM stats;
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute();
        $arrRes = $objSth->fetch();

        return $arrRes;
    }

    public function updateItemElementImageInfo($numItemElementId, $arrData)
    {
        $strQPattern = <<<EOF
UPDATE item_element_image_data 
SET %s 
WHERE item_element_id = :item_element_id
EOF;
        $arrToUpdateString = array();
        $arrToUpdateValues = array();
        if (!empty($arrData['width'])) {
            $arrToUpdateString[] = 'width = :width';
            $arrToUpdateValues[':width'] = intval($arrData['width']);
        }
        if (!empty($arrData['height'])) {
            $arrToUpdateString[] = 'height = :height';
            $arrToUpdateValues[':height'] = intval($arrData['height']);
        }
        if (!empty($arrData['weight'])) {
            $arrToUpdateString[] = 'weight = :weight';
            $arrToUpdateValues[':weight'] = intval($arrData['weight']);
        }
        if (!empty($arrData['mimetype'])) {
            $arrToUpdateString[] = 'mimetype = :mimetype';
            $arrToUpdateValues[':mimetype'] = $arrData['mimetype'];
        }
        if (!empty($arrData['image_path'])) {
            $arrToUpdateString[] = 'image_path = :image_path';
            $arrToUpdateValues[':image_path'] = $arrData['image_path'];
        }

        if (!empty($arrToUpdateString)) {
            $strQ = sprintf($strQPattern, join(', ', $arrToUpdateString));
            $objSth = $this->objDb->prepare($strQ);
            $arrToUpdateValues[':item_element_id'] = $numItemElementId;
            $objSth->execute($arrToUpdateValues);
        }
    }

    public function getImageElementsImageFileLocallyNotRemoved($numLimit)
    {
        $strQ = <<<EOF
SELECT 
    ieid.item_element_id, 
    ieid.image_path, 
    ieid.image_filename, 
    i.id AS item_id 
FROM item_element_image_data AS ieid 
JOIN item_element AS ie ON ie.id = ieid.item_element_id 
JOIN item AS i ON i.id = ie.item_id                
WHERE SUBSTRING(ieid.image_path FROM 1 FOR 1) = '/' 
    AND i.is_removed = false 
LIMIT :limit
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':limit' => $numLimit
        ));
        $arrResult = $objSth->fetchAll();

        return $arrResult;
    }

    public function fillSearchIndexes()
    {
        $strQ = <<<EOF
UPDATE item  
SET search_data = to_tsvector(:language, title) 
WHERE search_data IS NULL
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':language' => 'polish'
        ));
    }

    public function addItemAllegroInfo($numItemId, $numAllegroItemId, $numAllegroUserId)
    {
        $strQ = <<<EOF
INSERT INTO artifacts.item_allegro_info ( 
    item_id, 
    allegro_item_id, 
    allegro_user_id 
) VALUES (
    :item_id, 
    :allegro_item_id, 
    :allegro_user_id 
)
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':item_id' => $numItemId,
            ':allegro_item_id' => $numAllegroItemId,
            ':allegro_user_id' => $numAllegroUserId
        ));
    }

    public function getCleanArtifactsIdGreaterThan($numId, $numLimit)
    {
        $strQ = <<<EOF
SELECT 
    id, 
    title, 
    slug, 
    description, 
    thumb_filename, 
    CONCAT(
        thumb_path, 
        '/', 
        thumb_filename
    ) AS thumb_url 
FROM item 
WHERE is_public = true 
    AND is_removed = false 
    AND id > :than_id 
ORDER BY id ASC 
LIMIT :limit;
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->bindValue(':than_id', $numId, \PDO::PARAM_INT);
        $objSth->bindValue(':limit', $numLimit, \PDO::PARAM_INT);
        $objSth->execute();

        $arrArtifacts = $objSth->fetchAll();
        
        if (!empty($arrArtifacts)) {
            foreach ($arrArtifacts as & $arrArtifact) {
                $arrArtifact['thumb_url'] = self::replaceS3Doman($arrArtifact['thumb_url']);
            }
        }
        
        return $arrArtifacts;
    }

    public function getArtifacts($numOffset, $numLimit, $boolOrderByNewest = false)
    {
        $strQPattern = <<<EOF
SELECT 
    id, 
    title, 
    slug, 
    description, 
    thumb_filename, 
    CONCAT(
        thumb_path, 
        '/', 
        thumb_filename
    ) AS thumb_url 
FROM item 
WHERE is_public = true 
    AND is_removed = false 
    AND is_age_restricted = false 
%s 
LIMIT :limit 
OFFSET :offset
EOF;
        $strToReplace = '';
        if ($boolOrderByNewest === true) {
            $strToReplace = 'ORDER BY add_timestamp DESC NULLS LAST ';
        }
        $strQ = sprintf($strQPattern, $strToReplace);
        $objSth = $this->objDb->prepare($strQ);
        $objSth->bindValue(':limit', $numLimit, \PDO::PARAM_INT);
        $objSth->bindValue(':offset', $numOffset, \PDO::PARAM_INT);
        $objSth->execute();

        $arrArtifacts = $objSth->fetchAll();
        
        if (!empty($arrArtifacts)) {
            foreach ($arrArtifacts as & $arrArtifact) {
                $arrArtifact['thumb_url'] = self::replaceS3Doman($arrArtifact['thumb_url']);
            }
        }
        
        return $arrArtifacts;
    }

    public function getDataToCronAutotagize($numLimit)
    {
        $strQ = <<<EOF
SELECT s.id AS item_id, CONCAT_WS(' ', s.title, array_to_string(array_agg(ie.title), ' ')) AS titles_concated 
FROM 
(
    SELECT i.title, i.id, i.tags  
    FROM artifacts.item AS i 
    WHERE i.tags = '{}' OR i.tags IS NULL 
    LIMIT :limit
) AS s 
JOIN artifacts.item_element AS ie ON ie.item_id = s.id 
GROUP BY s.id, s.title;
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':limit' => $numLimit
        ));
        $arrData = $objSth->fetchAll();

        return $arrData;
    }

    public function addTags($numItemId, $arrTags, $arrRestrictedTags = array())
    {
        $objTagModel = new TagModel();
        if (empty($arrRestrictedTags)) {
            $arrRestrictedTags = $objTagModel->getLastRemoved();
        }
        foreach ($arrTags as $arrTagToAdd) {
            $boolIsRestricted = false;
            if (!empty($arrRestrictedTags)) {
                foreach ($arrRestrictedTags as $arrRestrictedTag) {
                    if ($arrRestrictedTag['slug'] === $arrTagToAdd['slug'] || $arrRestrictedTag['title'] === $arrTagToAdd['title']) {
                        $boolIsRestricted = true;
                    }
                }
            }

            if ($boolIsRestricted === false) {
                $arrTag = $objTagModel->getTagBySlug($arrTagToAdd['slug']);
                if (empty($arrTag)) {
                    $arrTag = $objTagModel->saveNewTag($arrTagToAdd['title'], $arrTagToAdd['slug']);
                }
                $this->assignTag($numItemId, $arrTag);
            }
        }
    }

    public function assignTag($numItemId, $arrTag)
    {
        $strQ = <<<EOF
UPDATE item SET tags = array_append(tags, :tag_id) WHERE id = :item_id
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $boolOk = $objSth->execute(array(
            ':tag_id' => $arrTag['id'],
            ':item_id' => $numItemId
        ));
        if ($boolOk === true) {
            $objTagModel = new TagModel();
            $objTagModel->changeElementsCount($arrTag['id'], 1);
        }
    }

    public function getStringToAutoTagize($strArtifactTitle, $arrElements)
    {
        $arrStringTokens = array();
        $arrStringTokens[] = $strArtifactTitle;
        foreach ($arrElements as $arrElement) {
            if (!empty($arrElement['strTitle'])) {
                $arrStringTokens[] = $arrElement['strTitle'];
            }
        }

        $strStringToTagize = join(' ', $arrStringTokens);
        return $strStringToTagize;
    }

    public function getArtifactStats($numArtifactId)
    {
        $strQPattern = <<<EOF
SELECT (shows_count_real + shows_count_increaser) AS shows_count 
FROM artifacts.%s 
WHERE id = :artifact_id
EOF;
        $strQ = sprintf($strQPattern, self::getPartitionName($numArtifactId));
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':artifact_id' => $numArtifactId
        ));
        $arrStats = $objSth->fetch();

        return $arrStats;
    }

    public function getArtifactInfo($numArtifactId)
    {
        $strQPattern = <<<EOF
SELECT i.add_timestamp, 
    a.display_name AS author_display_name, 
    (
        CASE 
            WHEN ie.type = 1 THEN row_to_json(ei.*) 
            WHEN ie.type = 2 THEN row_to_json(ey.*) 
        END 
    ) AS json_data 
    FROM artifacts.item_element AS ie 
JOIN artifacts.%s AS i ON i.id = ie.item_id 
LEFT OUTER JOIN artifacts.item_element_image_data AS ei ON ei.item_element_id = ie.id 
LEFT OUTER JOIN artifacts.item_element_ytvideo_data AS ey ON ey.item_element_id = ie.id  
LEFT OUTER JOIN users.account AS a ON a.id = i.author_account_id 
WHERE ie.item_id = :artifact_id 
    AND ie.ordering = 1
EOF;
        $strQ = sprintf($strQPattern, self::getPartitionName($numArtifactId));
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':artifact_id' => $numArtifactId
        ));
        $arrInfo = $objSth->fetch();
        if (!empty($arrInfo['json_data'])) {
            $arrInfo = array_merge($arrInfo, json_decode($arrInfo['json_data'], true));
            if (!empty($arrInfo['exif'])) {
                $arrInfo['prettyExif'] = $this->getInfoFromExif($arrInfo['exif']);
            }
        }
        return $arrInfo;
    }


    public function getInfoFromExif($arrExif)
    {
        $arrInfo = array();
        if (!empty($arrExif)) {
            $arrInfo['strCreatedBy'] = '';
            if (!empty($arrExif['Make'])) {
                $arrInfo['strCreatedBy'] .= $arrExif['Make'] . ' ';
            }
            if (!empty($arrExif['Model'])) {
                $arrInfo['strCreatedBy'] .= $arrExif['Model'];
            }
            if (!empty($arrExif['ExposureTime'])) {
                $arrInfo['strExposure'] = $arrExif['ExposureTime'];
            }
            if (!empty($arrExif['COMPUTED']['ApertureFNumber'])) {
                $arrInfo['strAperture'] = $arrExif['COMPUTED']['ApertureFNumber'];
            }
            if (!empty($arrExif['ISOSpeedRatings'])) {
                if (is_array($arrExif['ISOSpeedRatings'])) {
                    $arrInfo['strIso'] = array_shift($arrExif['ISOSpeedRatings']);
                } elseif (is_numeric($arrExif['ISOSpeedRatings'])) {
                    $arrInfo['strIso'] = $arrExif['ISOSpeedRatings'];
                }
            }
            if (!empty($arrExif['DateTimeOriginal'])) {
                $arrInfo['strCreateDateTime'] = strtotime($arrExif['DateTimeOriginal']);
            } else if (!empty($arrExif['DateTime'])) {
                $arrInfo['strCreateDateTime'] = strtotime($arrExif['DateTime']);
            }
        }

        return $arrInfo;
    }

    public function getArtifactBySlugOnlyForCron($strSlug)
    {
        $strQ = <<<EOF
SELECT slug, id 
FROM artifacts.item 
WHERE slug = :slug
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':slug' => $strSlug
        ));
        $arrArtifact = $objSth->fetch();

        return $arrArtifact;
    }

    public function getElements($mulArtifactIds)
    {
        $strQPattern = <<<EOF
SELECT e.title, 
    e.description, 
    e.type, 
    e.item_id AS item_id, 
    (
        CASE 
            WHEN e.type = 1 THEN row_to_json(i.*) 
            WHEN e.type = 2 THEN row_to_json(y.*) 
            WHEN e.type = 3 THEN row_to_json(m.*) 
        END 
    ) AS json_data 
    
FROM artifacts.item_element AS e 
LEFT OUTER JOIN artifacts.item_element_image_data AS i ON i.item_element_id = e.id 
LEFT OUTER JOIN artifacts.item_element_ytvideo_data AS y ON y.item_element_id = e.id  
LEFT OUTER JOIN artifacts.item_element_mem_data AS m ON m.item_element_id = e.id  
WHERE %s 
ORDER BY e.ordering ASC 
EOF;
        if (is_array($mulArtifactIds)) {
            $strWhere = sprintf('e.item_id IN (%s)', join(', ', $mulArtifactIds));
            $strQ = sprintf($strQPattern, $strWhere);
            $objSth = $this->objDb->prepare($strQ);
            $objSth->execute();
        } else {
            $strQ = sprintf($strQPattern, 'e.item_id = :artifact_id');
            $objSth = $this->objDb->prepare($strQ);
            $objSth->execute(array(
                ':artifact_id' => $mulArtifactIds
            ));
        }

        $arrElements = $objSth->fetchAll();
        foreach ($arrElements as & $arrElement) {
            if (empty($arrElement['json_data'])) {
                continue;
            }
            $arrElement = array_merge($arrElement, json_decode($arrElement['json_data'], true));

            unset($arrElement['json_data']);
            if ($arrElement['type'] === self::ITEM_TYPE_IMAGE || $arrElement['type'] === self::ITEM_TYPE_MEM) {
                // need to be change to ['image_url']
                $arrElement['thumb_url'] = self::replaceS3Doman($arrElement['image_path']) .'/'. $arrElement['image_filename'];
                $arrElement['image_url'] = $arrElement['thumb_url'];
            }
        }

        return $arrElements;
    }

    public function getOlders($numOlderThanId, $numLimit, $boolShowImported = false, $boolIsOnHomepage = false)
    {
        $strQ = <<<EOF
SELECT id, 
    title, 
    slug, 
    thumb_path, 
    thumb_filename, 
    CONCAT(
        thumb_path, 
        '/', 
        thumb_filename
    ) AS thumb_url, 
    thumb_width, 
    thumb_height 
FROM artifacts.item 
WHERE is_public = true 
    AND is_removed = false 
    AND is_imported = :is_imported 
    AND is_on_homepage = :is_on_homepage 
    AND id < :than_id
ORDER BY id DESC 
LIMIT :limit
EOF;
        // ORDER BY id DESC 
        
        $objSth = $this->objDb->prepare($strQ);
        $objSth->bindValue(':than_id', $numOlderThanId, \PDO::PARAM_INT);
        $objSth->bindValue(':is_imported', $boolShowImported, \PDO::PARAM_BOOL);
        $objSth->bindValue(':is_on_homepage', $boolIsOnHomepage, \PDO::PARAM_BOOL);
        $objSth->bindValue(':limit', $numLimit, \PDO::PARAM_INT);
        $objSth->execute();
        $arrList = $objSth->fetchAll();
        
        if (!empty($arrList)) {
            foreach ($arrList as & $arrArtifact) {
                $arrArtifact['thumb_url'] = self::replaceS3Doman($arrArtifact['thumb_url']);
            }
        }
        
        return $arrList;
    }

    public function getNewers($numNewerThanId, $numLimit, $boolShowImported = false, $boolIsOnHomepage = false)
    {
        $strQ = <<<EOF
SELECT id, 
    title, 
    slug, 
    thumb_path, 
    thumb_filename, 
    CONCAT(
        thumb_path, 
        '/', 
        thumb_filename
    ) AS thumb_url, 
    thumb_width, 
    thumb_height 
FROM artifacts.item 
WHERE is_public = true 
    AND is_removed = false 
    AND is_imported = :is_imported 
    AND is_on_homepage = :is_on_homepage 
    AND id > :than_id
ORDER BY id ASC 
LIMIT :limit
EOF;
        // ORDER BY id DESC 
        $objSth = $this->objDb->prepare($strQ);
        $objSth->bindValue(':than_id', $numNewerThanId, \PDO::PARAM_INT);
        $objSth->bindValue(':is_imported', $boolShowImported, \PDO::PARAM_BOOL);
        $objSth->bindValue(':is_on_homepage', $boolIsOnHomepage, \PDO::PARAM_BOOL);
        $objSth->bindValue(':limit', $numLimit, \PDO::PARAM_INT);
        $objSth->execute();
        $arrList = $objSth->fetchAll();

        if (!empty($arrList)) {
            foreach ($arrList as & $arrArtifact) {
                $arrArtifact['thumb_url'] = self::replaceS3Doman($arrArtifact['thumb_url']);
            }
        }
        
        return $arrList;
    }

    public function saveProStats($numItemId, $numUserId)
    {
        $objStorageMemcached = new StorageMemcache('stats');
        $strMemcacheKeyName = 'artifacts_pro_stats4';

        $strItemIdx = $numUserId . '|' . $numItemId;
        $arrShows = $objStorageMemcached->get($strMemcacheKeyName, array());

        if (!isset($arrShows[$strItemIdx])) {
            $arrShows[$strItemIdx] = array();
        }

        $arrShows[$strItemIdx][] = time();
        $objStorageMemcached->set($strMemcacheKeyName, $arrShows);
    }

    public function increaseShowsCount($numId)
    {
        $strQ = "UPDATE item SET shows_count_real = (shows_count_real+1) WHERE id = :item_id";
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(':item_id' => $numId));

        return true;
    }

    public function markBlockedSellersOffersAsRemoved () {
        $strQ = <<<EOF
UPDATE artifacts.item 
SET is_removed = TRUE, 
    removed_since_timestamp = CURRENT_TIMESTAMP 
WHERE is_removed = FALSE 
AND id IN (
    SELECT iai.item_id 
    FROM grabber.blocked_allegro_user AS bau 
    JOIN artifacts.item_allegro_info AS iai 
        ON iai.allegro_user_id = bau.allegro_user_id 
)     
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute();
        return true;
    }

    public function markAsRemoved($mulIdsToDelete)
    {
        $strQPattern = <<<EOF
UPDATE artifacts.item  
SET is_removed = TRUE, 
    removed_since_timestamp = (NOW() AT TIME ZONE 'UTC') 
WHERE id IN (%s)
EOF;
        $arrResult = array();

        if (!is_array($mulIdsToDelete)) {
            $mulIdsToDelete = array($mulIdsToDelete);
        }

        $strQ = sprintf($strQPattern, join(', ', $mulIdsToDelete));
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute();

        $arrResult['numDeletedCount'] = count($mulIdsToDelete);
        $arrResult['arrDeletedIds'] = $mulIdsToDelete;

        return $arrResult;
    }


    public function unmarkAsRemoved($mulIdsToDelete)
    {
        $strQPattern = <<<EOF
UPDATE artifacts.item 
SET is_removed = false, 
    removed_since_timestamp = NULL  
WHERE id IN (%s)
EOF;
        $arrResult = array();

        if (!is_array($mulIdsToDelete)) {
            $mulIdsToDelete = array($mulIdsToDelete);
        }

        $strQ = sprintf($strQPattern, join(', ', $mulIdsToDelete));
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute();

        $arrResult['numUnMarkedCount'] = count($mulIdsToDelete);
        $arrResult['arrUnmarkedIds'] = $mulIdsToDelete;

        return $arrResult;
    }


    public function getBaseInfo($numId)
    {
        $strQPattern = <<<EOF
SELECT id, title, description, slug, removed_since_timestamp, author_account_id, is_on_homepage, is_removed, is_age_restricted, is_imported, add_timestamp,    
    CONCAT(
        thumb_path, 
        '/', 
        thumb_filename
    ) AS thumb_url 
FROM artifacts.%s AS i 
                
WHERE id = :item_id
EOF;
        $strQ = sprintf($strQPattern, self::getPartitionName($numId));
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':item_id' => $numId
        ));
        $arrInfo =  $objSth->fetch();
        
        $arrInfo['thumb_url'] = self::replaceS3Doman($arrInfo['thumb_url']);
        
        if (String::isContainsReservedWords($arrInfo['title'])) {
            $arrInfo['is_age_restricted'] = true;
        }
        
        return $arrInfo;
    }

    /**
     * @deprecated 
     */
//    public function importItemUploadedToS3($strTitle, $strSlug, $strS3ImageUrl)
//    {
////        echo 'import '.$strTitle.' / '.$strSlug.' / '.$strS3ImageUrl.PHP_EOL;
//        $this->objDb->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
//
//        $strQ = <<<EOF
//INSERT INTO artifacts.item (
//    id, 
//    add_timestamp, author_account_id, description, 
//    is_age_restricted, is_imported, is_on_homepage, is_public, is_removed, 
//    slug, title, search_data, 
//    thumb_filename, thumb_path, 
//    thumb_height, thumb_width 
//) VALUES (
//    nextval('artifacts.item_id_seq'), 
//    NULL, NULL, NULL, 
//    FALSE, TRUE, FALSE, TRUE, FALSE, 
//    :slug, :title, :search_ts_vector, 
//    :thumb_filename, :thumb_path, 
//    :thumb_height, :thumb_width             
//) RETURNING id, slug
//EOF;
//        $objSth = $this->objDb->prepare($strQ);
//
//        $strImageBlob = file_get_contents($strS3ImageUrl);
//        $strImageBase64 = 'data:image/jpeg;base64,' . base64_encode($strImageBlob);
//        $arrThumb = $this->saveThumbFromImage($strSlug, $strImageBase64);
//
//        if (empty($arrThumb['filename'])) {
//            return false;
//        }
//
//        $objSth->bindValue(':slug', $strSlug, \PDO::PARAM_STR);
//        $objSth->bindValue(':title', $strTitle, \PDO::PARAM_STR);
//
//        $strSearchTsVector = $this->getTsVector($strTitle);
//        $objSth->bindValue(':search_ts_vector', $strSearchTsVector, \PDO::PARAM_STR);
//
//        $objSth->bindValue(':thumb_filename', $arrThumb['filename'], \PDO::PARAM_STR);
//        $objSth->bindValue(':thumb_path', $arrThumb['path'], \PDO::PARAM_STR);
//        $objSth->bindValue(':thumb_width', $arrThumb['width'], \PDO::PARAM_INT);
//        $objSth->bindValue(':thumb_height', $arrThumb['height'], \PDO::PARAM_INT);
//
//        try {
//            $objSth->execute();
//            $arrImportedItem = $objSth->fetch();
//        } catch (PDOException $e) {
//            echo '<Pre>';
//            print_r($e);
//            echo '</pre>';
//        }
////        echo '<Pre>';
////        print_r($arrImportedItem);
////        echo '</pre>';
//        return $arrImportedItem;
//
//    }

    private function getTsVector($strString)
    {
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

    public function addItem($arrElement, $mulAccountId, $boolIsAgeRestricted, $boolIsImported, $strTitle, $strDescription = '', $numShowsCountIncreaser = 0, $strClientIp = '')
    {
        $strQId = "SELECT nextval('artifacts.item_id_seq')";
        
        $strQPattern = <<<EOF
INSERT INTO artifacts.%s (
    id, add_timestamp, author_account_id, description, 
    is_age_restricted, is_imported, is_on_homepage, is_public, is_removed, 
    slug, title, search_data, 
    thumb_filename, thumb_path, 
    thumb_height, thumb_width, 
    shows_count_real, shows_count_increaser 
) VALUES (
    :id, (NOW() AT TIME ZONE 'UTC') , :author_account_id, :description, 
    :is_age_restricted, :is_imported, FALSE, TRUE, FALSE, 
    :slug, :title, :search_ts_vector, 
    :thumb_filename, :thumb_path, 
    :thumb_height, :thumb_width, 
    0, :shows_count_increaser 
) RETURNING id, slug
EOF;
        $objSthId = $this->objDb->prepare($strQId);
        $objSthId->execute();
        $numId = $objSthId->fetch(\PDO::FETCH_COLUMN, 0);
        
        if (!empty($strClientIp)) {
            $strQ = "INSERT INTO artifacts.item_info (item_id, uploaded_user_ip) VALUES (:item_id, :uploaded_user_ip)";
            $objSthId = $this->objDb->prepare($strQ);
            $objSthId->execute(array(
                ':item_id' => $numId, 
                ':uploaded_user_ip' => $strClientIp
            ));
        }
    
        $strQ = sprintf($strQPattern, self::getPartitionName($numId));
        $objSth = $this->objDb->prepare($strQ);

        $objSth->bindValue(':id', $numId, \PDO::PARAM_INT);
        $strSlug = $this->genUniqueSlug($strTitle);
        switch ($arrElement['numType']) {
            case self::ITEM_TYPE_IMAGE:
                $arrThumb = $this->saveThumbFromImage($strSlug, $arrElement['arrImage']['base64']);
                break;
            case self::ITEM_TYPE_YTVIDEO:
                $arrThumb = $this->saveThumbFromYtVideo($strSlug, $arrElement['strMovieUrl']);
                break;
            case self::ITEM_TYPE_MEM:
                $strMimeType = File::extensionToMimeType($arrElement['strExtension']);
                $strImageBase64 = sprintf('data:%s;base64,%s', $strMimeType, base64_encode($arrElement['strImageBlob']));
                $arrThumb = $this->saveThumbFromImage($strSlug, $strImageBase64);
                break;
        }

        if ($mulAccountId === null) {
            $objSth->bindValue(':author_account_id', null, \PDO::PARAM_NULL);
        } else {
            $objSth->bindValue(':author_account_id', $mulAccountId, \PDO::PARAM_INT);
        }

        if (empty($strDescription)) {
            $objSth->bindValue(':description', null, \PDO::PARAM_NULL);
        } else {
            $strHtml = BBCodeParser::getHtml($strDescription);
            $objSth->bindValue(':description', $strHtml, \PDO::PARAM_STR);
        }

        $objSth->bindValue(':is_age_restricted', $boolIsAgeRestricted, \PDO::PARAM_BOOL);
        $objSth->bindValue(':is_imported', $boolIsImported, \PDO::PARAM_BOOL);


        $objSth->bindValue(':slug', $strSlug, \PDO::PARAM_STR);
        $objSth->bindValue(':title', $strTitle, \PDO::PARAM_STR);

        $strSearchTsVector = $this->getTsVector($strTitle);
        $objSth->bindValue(':search_ts_vector', $strSearchTsVector, \PDO::PARAM_STR);

        $objSth->bindValue(':thumb_filename', $arrThumb['filename'], \PDO::PARAM_STR);
        $objSth->bindValue(':thumb_path', $arrThumb['path'], \PDO::PARAM_STR);
        $objSth->bindValue(':thumb_width', $arrThumb['width'], \PDO::PARAM_INT);
        $objSth->bindValue(':thumb_height', $arrThumb['height'], \PDO::PARAM_INT);
        
        $objSth->bindValue(':shows_count_increaser', $numShowsCountIncreaser, \PDO::PARAM_INT);

        $objSth->execute();

        $arrItem = $objSth->fetch();
        return $arrItem;
    }

    public function buildMeme($arrElement)
    {
        /*
         objElement.strTitle = elRow.find('div.mem-container textarea.mem-title').val();
        objElement.strDescription = elRow.find('div.mem-container textarea.mem-text').val();

        objElement.strBackground = elRow.find('img.mem-background').attr('src');
         * numMemeBackgroundId 
        objElement.numWidth = elRow.find('img.mem-background').width();
        */
        if (!empty($arrElement['strBackground'])) {
            $arrImage = explode(',', $arrElement['strBackground']);
            $strBase64prefx = $arrImage[0];
            array_shift($arrImage);
            $strImageBlob = base64_decode(join(',', $arrImage));


            $objFinfo = finfo_open(FILEINFO_MIME);
            $strMimeType = finfo_buffer($objFinfo, $strImageBlob);
            finfo_close($objFinfo);
            if (strpos($strMimeType, ';') !== false) {
                $arrTokens = explode(';', $strMimeType);
                $strMimeType = trim($arrTokens[0]);
            } else {
                $strBaseMimeType = str_replace(array('data:', ';base64'), '', $strBase64prefx);
                $strMimeType = File::extensionToMimeType($strBaseMimeType);
            }
            $strFileExtension = File::mimetypeToExtension($strMimeType);


            $strFileName = sprintf('%s.%s', String::slug($arrElement['strTitle']), $strFileExtension);
            $strFilePath = __DIR__ . '/../../../../var/tmp/' . $strFileName;
            file_put_contents($strFilePath, $strImageBlob);
            //        $arrElement['strTmpFilePath'] = $strFilePath;

            $thumb = new \PHPThumb\GD($strFilePath, array(
                'resizeUp' => true
            ));
            $arrOrginalDimensions = $thumb->getCurrentDimensions();
            $numRatio = $arrOrginalDimensions['width'] / $arrElement['numWidth'];
            $numNewHeight = round($arrOrginalDimensions['height'] / $numRatio);
            $thumb->resize($arrElement['numWidth'], $numNewHeight);


            $strImageBlob = $thumb->getImageAsString();
            unlink($strFilePath);


            $strBackgroundFilename = String::random(64) . '.' . $strFileExtension;
            $arrBackgroundAddResult = MemeBackgroundController::add($strBackgroundFilename, $strImageBlob, $arrElement['strTitle']);

            $arrElement['numMemeBackgroundId'] = $arrBackgroundAddResult['result']['numBackgroundId'];
        } else {
            $objMememBackgroundModel = new MemeBackgroundModel();
            $arrMemeBackground = $objMememBackgroundModel->getById($arrElement['numMemeBackgroundId']);
            $strMemeBackgroundUrl = $arrMemeBackground['image_path'] . '/' . $arrMemeBackground['image_filename'];
            $strImageBlob = file_get_contents($strMemeBackgroundUrl);

            $arrFilenameTokens = explode('.', $arrMemeBackground['image_filename']);
            $strFileExtension = array_pop($arrFilenameTokens);

            $arrSizes = getimagesize($strMemeBackgroundUrl);
            $arrOrginalDimensions = array(
                'width' => $arrSizes[0],
                'height' => $arrSizes[1]
            );
            $numRatio = $arrOrginalDimensions['width'] / $arrElement['numWidth'];
            $numNewHeight = round($arrOrginalDimensions['height'] / $numRatio);
        }


        $arrElement['strExtension'] = $strFileExtension;


        $im = imagecreatefromstring($strImageBlob);

        $objColorWhite = new GDText\Color(255, 255, 255);
        $objColorBlack = new GDText\Color(0, 0, 0);

        $box = new GDText\Box($im);
//        $box->enableDebug();
        $box->setFontFace(APP_DIR . '/impact.ttf');
        $box->setFontColor($objColorWhite);
        $box->addTextShadow($objColorBlack, 3, 3);
        $box->addTextShadow($objColorBlack, -3, -3);
        $box->addTextShadow($objColorBlack, 3, -3);
        $box->addTextShadow($objColorBlack, -3, 3);
        $box->addTextShadow($objColorBlack, 0, 3);
        $box->addTextShadow($objColorBlack, 0, -3);
        $box->addTextShadow($objColorBlack, 3, 0);
        $box->addTextShadow($objColorBlack, -3, 0);

        $box->setFontSize(60);
        $box->setBox(20, 20, $arrElement['numWidth'] - 40, 150);
        $box->setTextAlign('center', 'top');
        $box->draw(mb_strtoupper($arrElement['strTitle'], 'UTF-8'));


        $box->setTextShadow($objColorBlack, 2, 2);
        $box->addTextShadow($objColorBlack, -2, -2);
        $box->addTextShadow($objColorBlack, 2, -2);
        $box->addTextShadow($objColorBlack, -2, 2);
        $box->addTextShadow($objColorBlack, 0, 2);
        $box->addTextShadow($objColorBlack, 0, -2);
        $box->addTextShadow($objColorBlack, 2, 0);
        $box->addTextShadow($objColorBlack, -2, 0);

        $box->setFontSize(30);
        $box->setBox(20, $numNewHeight - 20 - 105, $arrElement['numWidth'] - 40, 113);
        $box->setTextAlign('center', 'bottom');
        $box->draw(mb_strtoupper($arrElement['strDescription'], 'UTF-8'));

        ob_start();
        imagejpeg($im, null, 75);
        $arrElement['strImageBlob'] = ob_get_contents();
        ob_clean();


        return $arrElement;

    }

    public function importElement($numItemId, $strTitle, $strS3ImagePath)
    {
        $strQ = <<<EOF
INSERT INTO artifacts.item_element (
    description, item_id, ordering, title, type 
) VALUES (
    NULL, :item_id, 1, :title, :type 
) RETURNING id
EOF;
        $objSthInsertElement = $this->objDb->prepare($strQ);

        $strQ = <<<EOF
INSERT INTO artifacts.item_element_image_data (
    exif, height, image_filename, image_path, item_element_id, mimetype, weight, width, image_source_url
) VALUES (
    NULL, :height, :image_filename, :image_path, :item_element_id, 'image/jpeg', :weight, :width, :image_source_url
) RETURNING id
EOF;
        $objSthInsertElementImageData = $this->objDb->prepare($strQ);

        $objSthInsertElement->bindValue(':item_id', $numItemId, \PDO::PARAM_INT);
        $objSthInsertElement->bindValue(':title', $strTitle, \PDO::PARAM_STR);
        $objSthInsertElement->bindValue(':type', self::ITEM_TYPE_IMAGE, \PDO::PARAM_INT);
        $objSthInsertElement->execute();
        $numItemElementId = $objSthInsertElement->fetch(\PDO::FETCH_COLUMN, 0);

        $arrImageNameTokens = explode('/', $strS3ImagePath);
        $strImageFilename = array_pop($arrImageNameTokens);
        $strImagePath = join('/', $arrImageNameTokens);

        $arrImageInfos = getimagesize($strS3ImagePath);
        $numImageWidth = intval($arrImageInfos[0]);
        $numImageHeight = intval($arrImageInfos[1]);

        $arrImageHeaders = get_headers($strS3ImagePath, 1);
        $numImageWeight = $arrImageHeaders['Content-Length'];

        $objSthInsertElementImageData->bindValue(':width', $numImageWidth, \PDO::PARAM_INT);
        $objSthInsertElementImageData->bindValue(':height', $numImageHeight, \PDO::PARAM_INT);
        $objSthInsertElementImageData->bindValue(':image_filename', $strImageFilename, \PDO::PARAM_STR);
        $objSthInsertElementImageData->bindValue(':image_path', $strImagePath, \PDO::PARAM_STR);
        $objSthInsertElementImageData->bindValue(':item_element_id', $numItemElementId, \PDO::PARAM_INT);
        $objSthInsertElementImageData->bindValue(':weight', $numImageWeight, \PDO::PARAM_INT);
        $objSthInsertElementImageData->bindValue(':image_source_url', $strS3ImagePath, \PDO::PARAM_STR);
        $objSthInsertElementImageData->execute();

    }

    public function addElements($numItemId, $arrElements, $strImageNamesPrefix)
    {
        $strQ = <<<EOF
INSERT INTO artifacts.item_element (
    description, item_id, ordering, title, type 
) VALUES (
    :description, :item_id, :ordering, :title, :type 
) RETURNING id
EOF;
        $objSthInsertElement = $this->objDb->prepare($strQ);

        $strQ = <<<EOF
INSERT INTO artifacts.item_element_image_data (
    exif, height, image_filename, image_path, item_element_id, mimetype, weight, width, author 
) VALUES (
    :exif, :height, :image_filename, :image_path, :item_element_id, :mimetype, :weight, :width, :author
) RETURNING id
EOF;
        $objSthInsertElementImageData = $this->objDb->prepare($strQ);

        $strQ = <<<EOF
INSERT INTO artifacts.item_element_ytvideo_data (
    item_element_id, youtube_id
) VALUES (
    :item_element_id, :youtube_id
) RETURNING id
EOF;
        $objSthInsertElementYtVideoData = $this->objDb->prepare($strQ);

        $strQ = <<<EOF
INSERT INTO artifacts.item_element_mem_data (
    height, image_filename, image_path, item_element_id, mimetype, weight, width, text_title, text_content, meme_background_id 
) VALUES (
    :height, :image_filename, :image_path, :item_element_id, :mimetype, :weight, :width, :text_title, :text_content, :meme_background_id
) RETURNING id
EOF;
        $objSthInsertElementMemData = $this->objDb->prepare($strQ);

        $strQ = <<<EOF
UPDATE artifacts.meme_background 
SET uses_count = (uses_count + 1) 
WHERE id = :background_id
EOF;
        $objSthMemeBackgroundUseIncrease = $this->objDb->prepare($strQ);


//        echo '<pre>';
//                        print_r($arrElements);
//                        exit();
        foreach ($arrElements as $arrElement) {
            if (!empty($arrElement['strDescription'])) {
                $strHtml = BBCodeParser::getHtml($arrElement['strDescription']);
                $objSthInsertElement->bindValue(':description', $strHtml, \PDO::PARAM_STR);
            } else {
                $objSthInsertElement->bindValue(':description', null, \PDO::PARAM_NULL);
            }
            $objSthInsertElement->bindValue(':item_id', $numItemId, \PDO::PARAM_INT);
            $objSthInsertElement->bindValue(':ordering', $arrElement['numOrdering'], \PDO::PARAM_INT);
            $objSthInsertElement->bindValue(':title', $arrElement['strTitle'], \PDO::PARAM_STR);
            $objSthInsertElement->bindValue(':type', $arrElement['numType'], \PDO::PARAM_INT);
            $objSthInsertElement->execute();

            $numItemElementId = $objSthInsertElement->fetch(\PDO::FETCH_COLUMN, 0);
            $strElementTitleSlug = String::slug($arrElement['strTitle']);

            switch ($arrElement['numType']) {

                case self::ITEM_TYPE_IMAGE:
                    if (count($arrElements) > 1) {
                        $strImageName = sprintf('%s-%s-%d', $strImageNamesPrefix, $strElementTitleSlug, $arrElement['numOrdering']);
                    } else {
                        $strImageName = $strImageNamesPrefix;
                    }
                    $arrImage = $this->saveImageFromBase64($strImageName, $arrElement['arrImage']['base64']);

                    $arrExif = array();
                    if ($arrImage['mimetype'] === 'image/jpeg' && function_exists('\exif_read_data')) {

                        $strBase64ToExif = str_replace('data:image', 'data://image', $arrElement['arrImage']['base64']);
                        $arrExifNative = @\exif_read_data($strBase64ToExif);
                        if ($arrExifNative === false) {
                            $arrExif = array();
                        } else {
                            $arrExif = $arrExifNative;
                            unset ($arrExif['OECF'], $arrExif['MakerNote']);
                        }
                    }
                    $objSthInsertElementImageData->bindValue(':exif', null, \PDO::PARAM_NULL);
                    $objSthInsertElementImageData->bindValue(':width', $arrImage['width'], \PDO::PARAM_INT);
                    $objSthInsertElementImageData->bindValue(':height', $arrImage['height'], \PDO::PARAM_INT);
                    $objSthInsertElementImageData->bindValue(':image_filename', $arrImage['filename'], \PDO::PARAM_STR);
                    $objSthInsertElementImageData->bindValue(':image_path', $arrImage['path'], \PDO::PARAM_STR);
                    $objSthInsertElementImageData->bindValue(':item_element_id', $numItemElementId, \PDO::PARAM_INT);
                    $objSthInsertElementImageData->bindValue(':mimetype', $arrImage['mimetype'], \PDO::PARAM_STR);
                    $objSthInsertElementImageData->bindValue(':weight', $arrImage['weight'], \PDO::PARAM_INT);
                    if (!empty($arrElement['strAuthor'])) {
                        $objSthInsertElementImageData->bindValue(':author', $arrElement['strAuthor'], \PDO::PARAM_STR);
                    } else {
                        $objSthInsertElementImageData->bindValue(':author', null, \PDO::PARAM_NULL);
                    }
                    $objSthInsertElementImageData->execute();
                    break;

                case self::ITEM_TYPE_MEM:
                    // height, image_filename, image_path, item_element_id, mimetype, weight, width, text_title, text_content
                    #
                    if (count($arrElements) > 1) {
                        $strImageName = sprintf('%s-%s', $strImageNamesPrefix, $strElementTitleSlug);
                    } else {
                        $strImageName = $strImageNamesPrefix;
                    }
                    $arrImage = $this->saveImageFromBlob($strImageName, $arrElement['strImageBlob'], $arrElement['strExtension']);

                    $objSthInsertElementMemData->bindValue(':height', $arrImage['height'], \PDO::PARAM_INT);
                    $objSthInsertElementMemData->bindValue(':image_filename', $arrImage['filename'], \PDO::PARAM_STR);
                    $objSthInsertElementMemData->bindValue(':image_path', $arrImage['path'], \PDO::PARAM_STR);
                    $objSthInsertElementMemData->bindValue(':item_element_id', $numItemElementId, \PDO::PARAM_INT);
                    $objSthInsertElementMemData->bindValue(':mimetype', $arrImage['mimetype'], \PDO::PARAM_STR);
                    $objSthInsertElementMemData->bindValue(':weight', $arrImage['weight'], \PDO::PARAM_INT);
                    $objSthInsertElementMemData->bindValue(':width', $arrImage['width'], \PDO::PARAM_INT);
                    $objSthInsertElementMemData->bindValue(':text_title', $arrElement['strTitle'], \PDO::PARAM_STR);
                    $objSthInsertElementMemData->bindValue(':text_content', $arrElement['strDescription'], \PDO::PARAM_STR);
                    $objSthInsertElementMemData->bindValue(':meme_background_id', $arrElement['numMemeBackgroundId'], \PDO::PARAM_INT);
                    $objSthInsertElementMemData->execute();

                    $objSthMemeBackgroundUseIncrease->bindValue(':background_id', $arrElement['numMemeBackgroundId'], \PDO::PARAM_INT);
                    $objSthMemeBackgroundUseIncrease->execute();
                    break;

                case self::ITEM_TYPE_YTVIDEO:
                    $arrYoutube = YouTube::parseUrl($arrElement['strMovieUrl']);
                    $objSthInsertElementYtVideoData->bindValue(':item_element_id', $numItemElementId, \PDO::PARAM_INT);
                    $objSthInsertElementYtVideoData->bindValue(':youtube_id', $arrYoutube['strId'], \PDO::PARAM_STR);
                    $objSthInsertElementYtVideoData->execute();
                    break;
            }
        }
    }

    public function prevalidArtifact($strTitle, $arrElements)
    {
        $arrErrors = array();
        if (empty($strTitle) || String::slug($strTitle) === '') {
            $arrErrors[] = 'Nieprawidłowa nazwa galerii, podaj inną';
        } else {
            foreach ($arrElements as & $arrElement) {
                if ($arrElement['numType'] === self::ITEM_TYPE_YTVIDEO) {
                    $arrYoutube = YouTube::parseUrl($arrElement['strMovieUrl']);
                    if (empty($arrYoutube['strId'])) {
                        $arrErrors[] = sprintf('%s nie jest poprawnym adresem serwisu YouTube', $arrElement['strMovieUrl']);
                    }
                }
            }
        }
        return $arrErrors;
    }

    private function saveThumbFromS3Image($strS3Url)
    {
        $strImageBlob = file_get_contents($strS3Url);

    }

    private function saveThumbFromImage($strImagePrfferName, $strImageBase64decoded)
    {
        $arrImage = explode(',', $strImageBase64decoded);
        $strBase64prefx = $arrImage[0];
        array_shift($arrImage);
        $strImageBlob = base64_decode(join(',', $arrImage));

        $objFinfo = finfo_open(FILEINFO_MIME);
        $strMimeType = finfo_buffer($objFinfo, $strImageBlob);
        finfo_close($objFinfo);
        if (strpos($strMimeType, ';') !== false) {
            $arrTokens = explode(';', $strMimeType);
            $strMimeType = trim($arrTokens[0]);
        } else {
            $strBaseMimeType = str_replace(array('data:', ';base64'), '', $strBase64prefx);
            $strMimeType = File::extensionToMimeType($strBaseMimeType);
        }
        $arrThumb = array();
        $strFileExtension = File::mimetypeToExtension($strMimeType);
        if (!empty($strFileExtension)) {
            $arrThumb = $this->saveThumb($strImageBlob, 400, 400, $strImagePrfferName, $strFileExtension, $strMimeType);
        }
        return $arrThumb;
    }

    private function saveThumbFromMem($strImagePrfferName, $strBackgroundString)
    {
        if (substr($strBackgroundString, 0, 5) !== 'data:') {
            $arrImageInfo = pathinfo($strBackgroundString);
            $strMimeType = File::extensionToMimeType($arrImageInfo['extension']);
            $strImageBlob = file_get_contents($strBackgroundString);
            $strImageBase64decoded = 'data:' . $strMimeType . ';base64,' . base64_encode($strImageBlob);
        }
        $arrImage = explode(',', $strImageBase64decoded);
        $strBase64prefx = $arrImage[0];
        array_shift($arrImage);
        $strImageBlob = base64_decode(join(',', $arrImage));

        $objFinfo = finfo_open(FILEINFO_MIME);
        $strMimeType = finfo_buffer($objFinfo, $strImageBlob);
        finfo_close($objFinfo);
        if (strpos($strMimeType, ';') !== false) {
            $arrTokens = explode(';', $strMimeType);
            $strMimeType = trim($arrTokens[0]);
        } else {
            $strBaseMimeType = str_replace(array('data:', ';base64'), '', $strBase64prefx);
            $strMimeType = File::extensionToMimeType($strBaseMimeType);
        }
        $strFileExtension = File::mimetypeToExtension($strMimeType);

        $arrThumb = $this->saveThumb($strImageBlob, 400, 400, $strImagePrfferName, $strFileExtension, $strMimeType);

        return $arrThumb;
    }

    private function saveThumbFromYtVideo($strImagePrfferName, $strVideoUrl)
    {
        $arrYoutubeUrlParsed = YouTube::parseUrl($strVideoUrl);
        $arrYoutubeImage = YouTube::getBestThumbnailImageFromID($arrYoutubeUrlParsed['strId']);
        $arrThumbPathTokens = explode('.', $arrYoutubeImage['strUrl']);
        $strOrginalExtension = strtolower(array_pop($arrThumbPathTokens));

        $objFinfo = finfo_open(FILEINFO_MIME);
        $strMimeType = finfo_buffer($objFinfo, $arrYoutubeImage['strBlob']);
        finfo_close($objFinfo);
        if (strpos($strMimeType, ';') !== false) {
            $arrTokens = explode(';', $strMimeType);
            $strMimeType = trim($arrTokens[0]);
        } else {
            $strMimeType = File::extensionToMimeType($strOrginalExtension);
        }
        $strFileExtension = File::mimetypeToExtension($strMimeType);

        $arrThumb = $this->saveThumb($arrYoutubeImage['strBlob'], 400, 400, $strImagePrfferName, $strFileExtension, $strMimeType);

        return $arrThumb;
    }

    public function addWatermarkToImageBlob($strImageBlob, $strImageExtension)
    {
        if ($strImageExtension !== 'gif') {
            $objWatermarkImage = imagecreatefrompng(APP_DIR . '/watermark.png');
            $objImage = imagecreatefromstring($strImageBlob);
            $numSourceWidth = imagesx($objImage);
            $numSourceHeight = imagesy($objImage);
            imagecopy($objImage, $objWatermarkImage, $numSourceWidth - 126 - 15, $numSourceHeight - 26 - 10, 0, 0, 126, 26);
            ob_start();
            switch ($strImageExtension) {
                case 'jpg':
                case 'jpeg':
                    imagejpeg($objImage, null, 100);
                    break;
                case 'png':
                    imagealphablending($objImage, false);
                    imagesavealpha($objImage, true);
                    imagepng($objImage, null, 1);
                    break;
                case 'gif':
                    imagegif($objImage);
                    break;
            }
            $strWatermarkedImageBlob = ob_get_contents();
            ob_end_clean();
            imagedestroy($objImage);
            imagedestroy($objWatermarkImage);
        } else {
            $strWatermarkedImageBlob = $strImageBlob;
        }

        return $strWatermarkedImageBlob;
    }

    public function saveImageFromBlob($strImageFilename, $strImageBlob, $strImageExtension)
    {
        $arrResult = array();

        $strImageBlob = $this->addWatermarkToImageBlob($strImageBlob, $strImageExtension);

        $objS3Model = new StorageModel('s3', array(
            'key' => 'AKIAJRIWCGLMNSOD6FOA',
            'secret' => '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',
            'region' => 'eu-central-1',
            'bucket' => 'i.imged.pl',
            'version' => '2006-03-01'
        ), array(
            'scheme' => 'http'
        ));
        $strFileName = sprintf('%s.%s', $strImageFilename, $strImageExtension);
        $arrSaveResult = $objS3Model->writeFile($strFileName, $strImageBlob);

        if (!empty($arrSaveResult['ObjectURL'])) {
            $arrImageSizes = getimagesize($arrSaveResult['ObjectURL']);
            $arrNameTokens = explode('/', $arrSaveResult['ObjectURL']);
            $numImageLength = strlen($strImageBlob);
            $strMimeType = File::extensionToMimeType($strImageExtension);

            $strImageFilename = array_pop($arrNameTokens);
            $strImagePath = join('/', $arrNameTokens);
            
            $arrResult = array(
                'filename' => $strImageFilename,
                'path' => $strImagePath,
                'width' => $arrImageSizes[0],
                'height' => $arrImageSizes[1],
                'weight' => $numImageLength,
                'mimetype' => $strMimeType
            );
        }
        return $arrResult;
    }

    public function saveImageFromBase64($strImageFilename, $strImageBase64)
    {
        $arrResult = array();
        $arrImage = explode(',', $strImageBase64);
        array_shift($arrImage);
        $strImageBlob = base64_decode(join(',', $arrImage));

        $objFinfo = finfo_open(FILEINFO_MIME);
        $strMimeType = finfo_buffer($objFinfo, $strImageBlob);
        finfo_close($objFinfo);
        if (strpos($strMimeType, ';') !== false) {
            $arrTokens = explode(';', $strMimeType);
            $strMimeType = trim($arrTokens[0]);
        }
        $strFileExtension = File::mimetypeToExtension($strMimeType);

        $strImageBlob = $this->addWatermarkToImageBlob($strImageBlob, $strFileExtension);

        $objS3Model = new StorageModel('s3', array(
            'key' => 'AKIAJRIWCGLMNSOD6FOA',
            'secret' => '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',
            'region' => 'eu-central-1',
            'bucket' => 'i.imged.pl',
            'version' => '2006-03-01'
        ), array(
            'scheme' => 'http'
        ));

        $strFileName = sprintf('%s.%s', $strImageFilename, $strFileExtension);

        $arrSaveResult = $objS3Model->writeFile($strFileName, $strImageBlob);
        if (!empty($arrSaveResult['ObjectURL'])) {
            $arrImageSizes = getimagesize($arrSaveResult['ObjectURL']);
            $arrNameTokens = explode('/', $arrSaveResult['ObjectURL']);
            $numImageLength = strlen($strImageBlob);

            $strImageFilename = array_pop($arrNameTokens);
            $strImagePath = join('/', $arrNameTokens);
            
            $arrResult = array(
                'filename' => $strImageFilename,
                'path' => $strImagePath,
                'width' => $arrImageSizes[0],
                'height' => $arrImageSizes[1],
                'weight' => $numImageLength,
                'mimetype' => $strMimeType
            );
        }

        return $arrResult;
    }

    public function saveThumb($strImageBlob, $numMaxWidth, $numMaxHeight, $strPrefferFilename, $strFileExtension, $strMimeType)
    {
        $arrResult = array();

        $strFileName = sprintf('%s.%s', $strPrefferFilename, $strFileExtension);
        $strFilePath = __DIR__ . '/../../../../var/tmp/' . $strFileName;
        file_put_contents($strFilePath, $strImageBlob);

        try {
            $thumb = new \PHPThumb\GD($strFilePath);
        } catch (Exception $e) {
            return false;
        }
        $thumb->resize($numMaxWidth, $numMaxHeight);
        $thumb->save($strFilePath);
        $arrThumbSizes = $thumb->getNewDimensions();

        $objS3Model = new StorageModel('s3', array(
            'key' => 'AKIAJRIWCGLMNSOD6FOA',
            'secret' => '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',
            'region' => 'eu-central-1',
            'bucket' => 'i1.imged.pl',
            'version' => '2006-03-01'
        ), array(
            'scheme' => 'http'
        ));
        $arrSaveResult = $objS3Model->saveFile($strFileName, $strFilePath);
         
        if (!empty($arrSaveResult['ObjectURL'])) {
            $arrNameTokens = explode('/', $arrSaveResult['ObjectURL']);
            $strThumbFilename = array_pop($arrNameTokens);
            $strThumbPath = join('/', $arrNameTokens);
         
            
            $arrResult = array(
                'filename' => $strThumbFilename,
                'path' => $strThumbPath,
                'width' => $arrThumbSizes['newWidth'],
                'height' => $arrThumbSizes['newHeight']
            );
        }
        
        unlink($strFilePath);
        return $arrResult;
    }

    private function calculateThumbSizes($numOrginalWidth, $numOrginalHeight, $numMaxWidth, $numMaxHeight)
    {
        $arrThumbSizes = array();

        if ($numOrginalWidth < $numOrginalHeight) {
            $numRatio = $numOrginalWidth / $numMaxWidth;
            $arrThumbSizes['width'] = $numMaxWidth;
            $arrThumbSizes['height'] = round($numOrginalHeight / $numRatio);

        } else if ($numOrginalWidth > $numOrginalHeight) {
            $numRatio = $numOrginalHeight / $numMaxHeight;
            $arrThumbSizes['width'] = round($numOrginalWidth / $numRatio);
            $arrThumbSizes['height'] = $numMaxHeight;
        } else if ($numOrginalWidth === $numOrginalHeight) {
            $numRatio = $numOrginalWidth / $numMaxWidth;
            $arrThumbSizes['width'] = $numMaxWidth;
            $arrThumbSizes['height'] = round($numOrginalHeight / $numRatio);
        }
        return $arrThumbSizes;
    }

    private function genUniqueSlug($strTitle)
    {
        $strQ = <<<EOF
SELECT count(1) AS exists 
FROM artifacts.item 
WHERE slug = :slug
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $numAddon = 1;
        $strSlugBase = String::slug($strTitle);

        do {
            $strSlug = $strSlugBase;
            if ($numAddon > 1) {
                $strSlug .= '-' . $numAddon;
            }
            $objSth->execute(array(
                ':slug' => $strSlug
            ));

            $numCurrentlySlugs = $objSth->fetch(\PDO::FETCH_COLUMN, 0);
            if ($numCurrentlySlugs === 0) {
                break;
            }
            $numAddon++;
        } while (true);
        return $strSlug;
    }

    public function setOnHomepage($numArtifactId, $numState)
    {
        $strQPattern = <<<EOF
UPDATE artifacts.%s 
SET is_on_homepage = :new_state 
WHERE id = :id
EOF;
        $strQ = sprintf($strQPattern, self::getPartitionName($numArtifactId));
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':new_state' => $numState,
            ':id' => $numArtifactId
        ));
    }

    public function setAdultsOnly($numArtifactId, $numState)
    {
        $strQPattern = <<<EOF
UPDATE artifacts.%s 
SET is_age_restricted = :new_state 
WHERE id = :id
EOF;
        $strQ = sprintf($strQPattern, self::getPartitionName($numArtifactId));
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':new_state' => $numState,
            ':id' => $numArtifactId
        ));
    }

    public function setAsOffer($numArtifactId, $numState)
    {
        $numState = intval($numState);
        if ($numState === 1) {
            $strQ = "INSERT INTO artifacts.item_offer_type (item_id) VALUES (:item_id)";
        } else {
            $strQ = "DELETE FROM artifacts.item_offer_type  WHERE item_id = :item_id";
        }

        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':item_id' => $numArtifactId
        ));
    }

    public static function getPartitionName ($numArtifactId) {
        
//        $strPartition = sprintf(
//            'item_p_%d', 
//            floor($numArtifactId/1000000)+1
//        );
        $strPartition = 'item';
        return $strPartition;
    }

    public function addToHomepage($numId)
    {
        $strQPattern = <<<EOF
UPDATE artifacts.%s SET is_on_homepage = true WHERE id = :id 
EOF;
        $strQ = sprintf($strQPattern, self::getPartitionName($numId));
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':id' => $numId
        ));
        return true;
    }

    public function getArtifactsOnHomepageAdmin()
    {
        $strQ = <<<EOF
SELECT id, 
    slug, 
    title, 
    CONCAT(
        thumb_path, 
        '/', 
        thumb_filename
    ) AS thumb_url 
FROM artifacts.item  
WHERE is_on_homepage = true 
ORDER BY add_timestamp DESC, id DESC
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute();
        $arrList = $objSth->fetchAll();
        
        if (!empty($arrList)) {
            foreach ($arrList as & $arrArtifact) {
                $arrArtifact['thumb_url'] = self::replaceS3Doman($arrArtifact['thumb_url']);
            }
        }
        
        return $arrList;
    }

    public function removeFromHomepage($numId)
    {
        $strQPattern = <<<EOF
UPDATE artifacts.%s SET is_on_homepage = false WHERE id = :id 
EOF;
        $strQ = sprintf($strQPattern, self::getPartitionName($numId));
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':id' => $numId
        ));
        return true;
    }

    public function getRandom()
    {
        $strQ = <<<EOF
SELECT id, title, slug 
FROM artifacts.item 
WHERE is_public = TRUE 
    AND is_removed = FALSE 
    AND add_timestamp IS NOT NULL 
LIMIT 100
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute();
        $arrArtifacts = $objSth->fetchAll();
        shuffle($arrArtifacts);
        $arrReturn = array_splice($arrArtifacts, 0, 1);

        return $arrReturn;
    }

    public function getMostNew()
    {
        $strQ = <<<EOF
SELECT id, title, slug 
FROM item 
WHERE is_public = TRUE 
    AND is_removed = FALSE 
    AND add_timestamp IS NOT NULL 
ORDER BY id DESC 
LIMIT 1
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute();
        $arrArtifact = $objSth->fetch();

        return $arrArtifact;
    }

    public function getBasicStats()
    {
        $strQ = "SELECT COUNT(1) FROM item WHERE is_public IS TRUE AND is_removed IS FALSE";
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute();
        $numCount = $objSth->fetch(\PDO::FETCH_COLUMN, 0);
        $arrStats = array(
            'numTotal' => $numCount
        );
        return $arrStats;
    }

}

