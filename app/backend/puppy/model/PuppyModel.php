<?php namespace backend\puppy\model;

use \backend\DbFactory;

class PuppyModel { 
    
    private $objDb = null;
    
    public static $arrAreas = array(
        'top-hp' => 'Strona główna - TOP', 
        'top-tag' => 'Strona tagu - TOP', 
        'top-artifact1-adults-only' => 'Strona artefaktu - TOP (nie wyświetlana na stronach 18+)', 
        'top-artifact' => 'Strona artefaktu - TOP', 
        'side-artifact1-adults-only' => 'Strona artefaktu - SIDE 1 (nie wyświetlana na stronach 18+, max szer 350px)', 
        'side-artifact' => 'Strona artefaktu - SIDE 2 (max szer 350px)', 
        'bottom-artifact1-adults-only' => 'Strona artefaktu - BOTTOM 1 (nie wyświetlana na stronach 18+, max szer 730px)', 
        'bottom-artifact' => 'Strona artefaktu - BOTTOM 2 (max szer 730px)', 
        'sticky' => 'Banner sticky', 
        'test' => 'Strefa testowa (wyświetlana na dole Polityki Prywatności)', 
        'preview-modal' => 'Podgląd zdjęcia'
    );
    
    public function __construct () {
        $this->objDb = DbFactory::getInstance();
        $this->objDb->exec("SET SCHEMA 'adverts'");
    }
    
    public function getByArea ($strAreaId) {
        $stmt = $this->objDb->prepare('SELECT code FROM advert WHERE area = :area');
        $stmt->execute(array(
            ':area' => $strAreaId
        ));
        $arrAdvert = $stmt->fetch();
        return $arrAdvert;
    }
    
    public function getAllByArea() {
        $arrAdverts = array();
        $stmt = $this->objDb->prepare('SELECT * FROM advert');
        $stmt->execute();
        $arrAdvertsAll = $stmt->fetchAll();
        if (!empty($arrAdvertsAll)) {
            foreach ($arrAdvertsAll as $arrAdvertAll) {
                $strArea = $arrAdvertAll['area'];
                $arrAdverts[$strArea] = $arrAdvertAll;
            }
        }
        return $arrAdverts;
    }
    
    public function getAreas () {
        $arrAreas = array();
        foreach (self::$arrAreas as $strAreaId => $strAreaDescription) {
            $arrAreas[] = array(
                'strId' => $strAreaId, 
                'strDescription' => $strAreaDescription
            );
        }
        return $arrAreas;
    }
    
    public function savePuppy ($strAdvertCode, $strAreaId) {
        $strQ = <<<EOF
WITH upsert AS (
    UPDATE advert 
    SET code = :advert_code 
    WHERE area = :area 
    RETURNING *
) INSERT INTO advert (area, code) SELECT :area2, :advert_code2 WHERE NOT EXISTS (
    SELECT * FROM upsert
)
EOF;
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':advert_code' => $strAdvertCode, 
            ':area' => $strAreaId, 
            ':advert_code2' => $strAdvertCode, 
            ':area2' => $strAreaId
        ));
        
        return true;
    }
    
    public function clearArea ($strAreaId) {
        $stmt = $this->objDb->prepare('DELETE FROM advert WHERE area = :area');
        $stmt->execute(array(
            ':area' => $strAreaId
        ));
        
        return true;
    }
    
}