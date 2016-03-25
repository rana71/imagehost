<?php

namespace backend\artifact\model;

use \backend\DbFactory;
use backend\artifact\model\ArtifactListOptionsModel;

class ArtifactListModel {

    private $objDb = null;

    public function __construct() {
        $this->objDb = DbFactory::getInstance();
        $this->objDb->exec("SET SCHEMA 'artifacts'");
    }

    public function getArtifacts(ArtifactListOptionsModel $objOptions) {
        $strQPattern = <<<EOF
SELECT id, 
    title, 
    description, 
    slug, 
    thumb_path, 
    thumb_filename, 
    CONCAT(
        thumb_path, 
        '/', 
        thumb_filename
    ) AS thumb_url, 
    thumb_width, 
    thumb_height, 
    (thumb_height > thumb_width) AS is_vertical, 
    shows_count_real, 
    (shows_count_real + shows_count_increaser) AS shows_count, 
    add_timestamp 
FROM artifacts.item 
WHERE is_public = true 
    AND is_removed = false 
    %s 
%s  
LIMIT :limit 
OFFSET :offset
EOF;

        $strQ = sprintf(
                $strQPattern, $objOptions->getWhereString(), // where
                $objOptions->getOrderString() // order by 
        );
        $numOffset = $objOptions->numLimit * $objOptions->getPageNo() - $objOptions->numLimit;

        $objSth = $this->objDb->prepare($strQ);
        $objSth->bindValue(':limit', $objOptions->numLimit, \PDO::PARAM_INT);
        $objSth->bindValue(':offset', $numOffset, \PDO::PARAM_INT);
        $objSth->execute();
        $arrArtifacts = $objSth->fetchAll();

        if (!empty($arrArtifacts)) {
            foreach ($arrArtifacts as & $arrArtifact) {
                $arrArtifact['thumb_url'] = ArtifactModel::replaceS3Doman($arrArtifact['thumb_url']);
            }
        }

        return $arrArtifacts;
    }

    public function getResultsCount(ArtifactListOptionsModel $objOptions) {
        $strQPattern = <<<EOF
SELECT COUNT(1) AS ex 
FROM item 
WHERE is_public = true %s 
EOF;
        $strQ = sprintf(
                $strQPattern, $objOptions->getWhereString()
        );
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute();

        $numResults = $objSth->fetch(\PDO::FETCH_COLUMN, 0);

        return $numResults;
    }

    public function getListAdmin($numLimit, $strSearchString = '', $boolIsImported = false, $strOrderBy = 'default') {
        $strQPattern = <<<EOF
SELECT i.id, 
    i.title, 
    i.slug, 
    i.thumb_path, 
    i.thumb_filename, 
    CONCAT(
        i.thumb_path, 
        '/', 
        i.thumb_filename
    ) AS thumb_url, 
    i.add_timestamp, 
    i.is_on_homepage, 
    i.shows_count_real, 
    i.is_age_restricted, 
    i.removed_since_timestamp, 
    (
        SELECT COUNT(1) FROM item_element WHERE item_id = i.id
    ) AS elements_count, 
    (CASE WHEN iot.item_id IS NOT NULL THEN TRUE ELSE FALSE END) AS is_offer 
FROM artifacts.item AS i 
    LEFT JOIN artifacts.item_offer_type AS iot ON i.id= iot.item_id 
WHERE %s 
ORDER BY %s NULLS LAST 
LIMIT :limit
EOF;
        $arrWhere = array();
        $strOrderByInQuery = '';

        if ($boolIsImported === false) {
            $arrWhere[] = '(i.is_imported IS FALSE)';
        }

        if (!empty($strSearchString)) {
            if (is_numeric($strSearchString)) {
                $arrWhere[] = "(id = '" . $strSearchString . "')";
                $numLimit = 1;
            } else {
                $strSearchString = mb_strtolower($strSearchString, 'UTF-8');
                if ($boolIsImported === true) {
                    $arrWhere[] .= "(title ILIKE '%" . $strSearchString . "%' OR slug ILIKE '%" . $strSearchString . "%')";
                    $strOrderByInQuery = '';
                    switch ($strOrderBy) {
                        case 'popularity':
                            $strOrderByInQuery = 'i.shows_count_real DESC';
                            break;
                        default:
                            $strOrderByInQuery = 'i.add_timestamp DESC';
                            break;
                    }
                } else {
                    $strThisWhere = sprintf("i.search_data @@ '%s'", str_replace(' ', ' | ', $strSearchString));
                    $strThisWhere .= " OR title ILIKE '%" . $strSearchString . "%' OR slug ILIKE '%" . $strSearchString . "%'";
                    $arrWhere[] = '(' . $strThisWhere . ')';
                    switch ($strOrderBy) {
                        case 'popularity':
                            $strOrderByInQuery = 'i.shows_count_real DESC';
                            break;
                        case 'by_search':
                            $strOrderByInQuery = sprintf(
                                "ts_rank_cd(i.search_data, '%s') DESC ", str_replace(' ', ' & ', $strSearchString)
                            );
                            break;
                        default:
                            $strOrderByInQuery = 'i.add_timestamp DESC';
                            break;
                    }
                }
                
            }
        } else {
            switch ($strOrderBy) {
                case 'popularity':
                    $strOrderByInQuery = 'i.shows_count_real DESC';
                    break;
                default:
                    $strOrderByInQuery = 'i.add_timestamp DESC';
                    break;
            }
        }

        $strWhereString = '';
        if (empty($arrWhere)) {
            $strWhereString = 'TRUE';
        } else {
            $strWhereString = join(' AND ', $arrWhere);
        }

        $strQ = sprintf($strQPattern, $strWhereString, $strOrderByInQuery);
//        echo $strQ;

        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':limit' => $numLimit
        ));
        $arrList = $objSth->fetchAll();

        if (!empty($arrList)) {
            foreach ($arrList as & $arrArtifact) {
                $arrArtifact['thumb_url'] = ArtifactModel::replaceS3Doman($arrArtifact['thumb_url']);
            }
        }

        return $arrList;
    }

}
