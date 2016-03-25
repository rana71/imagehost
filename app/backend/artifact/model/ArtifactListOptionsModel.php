<?php namespace backend\artifact\model;

class ArtifactListOptionsModel { 
    
    const ORDERBY_ALPHABET = 0;
    const ORDERBY_DATE_DESC = 1;
    const ORDERBY_ON_HOMEPAGE = 2;
    const ORDERBY_MOST_POPULAR = 3;
    const ORDERBY_ID_DESC = 4;
    
    private $arrSortSlugs = array(
        'alfabetyczne' => 0, 
        'najnowsze' => 1, 
        'strona-glowna' => 2, 
        'najpopularniejsze' => 3
    );
    
    public $numLimit = 30;
    public $numGlobalLimit = 0;
    public $arrOrders = array();
    public $arrWheres = array();
    private $numPageNo = 1;
    public $boolLoadingEnabled = true;
    public $arrTags = array();
    public $arrQueries = array();
    public $arrAccountIds = array();
    public $arrViewCellsLayout = array(
        array(array(6,4), array(3,4), array(3,4)), 
        array(array(4,6), array(4,3), array(4,3)), 
        array(array(2,3), array(3,3), array(5,3), array(2,3))
    );
    
    public function __construct ($numPageNo) {
        $this->numPageNo = $numPageNo;
    }
    
    public function clearViewLayout () { 
        $this->arrViewCellsLayout = array();
    }
    
    public function addViewLayoutRow ($arrRowConfig) {
        $this->arrViewCellsLayout[] = $arrRowConfig;
    }
    
    public function disableLoading () {
        $this->boolLoadingEnabled = false;
    }
    
    public function translateOrderSlugToId ($strSortSlug) {
        return $this->arrSortSlugs[$strSortSlug];
    }
    
    public function getPageNo () {
        return $this->numPageNo;
    }
    
    public function addQuery ($strQuery) {
        $this->arrQueries[] = $strQuery;
    }
    
    public function addAuthorAccount ($mulAccountIds) {
        if (!is_array($mulAccountIds)) {
            $mulAccountIds = array($mulAccountIds);
        }
        $this->arrAccountIds = $mulAccountIds;
    }
    
    public function initializeWithJson ($strDefaultValuesJson) {
        $arrOptions = json_decode($strDefaultValuesJson);
        foreach ($arrOptions as $strKey => $strValue) {
            $this->{$strKey} = $strValue;
        }
    }
    
    public function addTag ($numTagId) {
        $this->arrTags[] = $numTagId;
    }
    
    public function setLimit ($numLimit) {
        $this->numLimit = $numLimit;
    }
    
    public function setGlobalLimit ($numLimit) {
        $this->numGlobalLimit = $numLimit;
    }
    
    public function setOrder ($arrOrders) {
        $this->arrOrders = $arrOrders;
    }
    
    public function addAndWhere ($arrWhere, $strSign = '=') {
        $this->arrWheres[] = array($strSign, $arrWhere);
    }
    
    public function getNextPageSerialized () {
        $objNewOptions = clone $this;
        $objNewOptions->numPageNo++;
        return json_encode($objNewOptions);
    }
    
    public function getOrderString () {
        $strString = '';
        $arrOrders = array();
        if (!empty($this->arrQueries)) {
            foreach ($this->arrQueries as $strQuery) {
                $arrOrders[] = sprintf("ts_rank_cd(search_data, '%s') desc", str_replace(' ', ' & ', $strQuery));
            }
        }
        if (!empty($this->arrOrders)) {
            foreach ($this->arrOrders as $strOrderKey) {
                switch ($strOrderKey) {
                    case self::ORDERBY_DATE_DESC:
                        $arrOrders[] = 'add_timestamp DESC NULLS LAST';
                        break;
                    case self::ORDERBY_ON_HOMEPAGE:
                        $arrOrders[] = 'is_on_homepage DESC';
                        break;
                    
                    case self::ORDERBY_ALPHABET:
                        $arrOrders[] = 'title ASC';
                        break;
                    case self::ORDERBY_MOST_POPULAR:
                        $arrOrders[] = 'shows_count_real DESC';
                        break;
                    case self::ORDERBY_ID_DESC:
                        $arrOrders[] = 'id DESC';
                        break;
                }
            }
        }
        if (!empty($arrOrders)) {
            $strString = 'ORDER BY '.join(', ', $arrOrders);
        }
        return $strString;
    }
    
    public function getWhereString () {
        $strString = '';
        $arrWheres = array();
        if (!empty($this->arrWheres)) {
            foreach ($this->arrWheres as $arrWhere) {
                $strSign = $arrWhere[0];
                $arrFieldsValues = $arrWhere[1];
                $arrThisWhere = array();
                foreach ($arrFieldsValues as $strWhereKey => $strWhereValue) {
                    $arrThisWhere[] = sprintf('(%s %s %s)', $strWhereKey, $strSign, $strWhereValue);
                }
                $arrWheres[] = join(' AND ', $arrThisWhere);
            }
            
        }
        if (!empty($this->arrTags)) {
            $arrWheres[] = sprintf("tags @> '{%s}'::int[]", join(', ', $this->arrTags));
        }
        if (!empty($this->arrQueries)) {
            $arrQueryWheres = array();
            foreach ($this->arrQueries as $strQuery) {
                $arrQueryWheres[] = sprintf("search_data @@ '%s'", str_replace(' ', ' | ', $strQuery));
            }
            $arrWheres[] = join(' AND ', $arrQueryWheres);
        }
        if (!empty($this->arrAccountIds)) {
            if (count($this->arrAccountIds) === 1) {
                $arrWheres[] = sprintf('(author_account_id = %d)', $this->arrAccountIds[0]);
            } else {
                $arrWheres[] = sprintf('(author_account_id IN (%s))', join(', ', $this->arrAccountIds));
            }
        }
        if (!empty($arrWheres)) { 
            $strString = 'AND ('.join(' AND ', $arrWheres).')';
        }
        return $strString;
    }
    
}