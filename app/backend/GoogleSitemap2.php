<?php namespace backend;

class GoogleSitemap2
{
    
    private static $MAX_MAP_LINKS = 40000;
    private static $MAX_MAP_SIZE_BYTES = 41943040; // 40mb;
    
    const MAP_XML_TAG = '<?xml version="1.0" encoding="UTF-8"?>';
    
    const CHANGE_ALWAYS = 'always';
    const CHANGE_HOURLY = 'hourly';
    const CHANGE_DAILY = 'daily';
    const CHANGE_WEEKLY = 'weekly';
    const CHANGE_MONTHLY = 'monthly';
    const CHANGE_YEARLY = 'yearly';
    const CHANGE_NEVER = 'never';
    
    private $strIndexFile = '';
    private $strMapsDirectory = '';
    private $strMapsDirectoryUri = '';
    
    private $strCurrentDomain = '';
    private $numCurrentMapNo = 1;
    
    private $arrCurrentUrls = array();
    private $strCurrentMapCode = '';
    private $arrObjectTypes = array();
    
    private $arrGeneratedMapFiles = array();
    
    public static function escape ($strInput) {
        $strOutput = htmlspecialchars($strInput, ENT_QUOTES);
        return $strOutput;
    }
    
    public function __construct ($strIndexFilePath, $strMapsDirectory, $strMapsDirectoryUri) {
        $this->strIndexFile = $strIndexFilePath;
        $this->strMapsDirectory = $strMapsDirectory;
        $this->strMapsDirectoryUri = $strMapsDirectoryUri;
        $this->clearOld();
    }

    public function setDomain ($strCurrentDomain) {
        $this->strCurrentDomain = $strCurrentDomain;
    }
        
    public function addUrl ($strUri, $numPriority = 0, $strChangeFreq = '', $strAdditions = array()) {
        $strLoc = $strUri;
        $objUrl = new \GoogleSitemap2\Url($strLoc, $numPriority, $strChangeFreq);
        if (!in_array('url', $this->arrObjectTypes)) {
            $this->arrObjectTypes[] = 'url';
        }
        if (!empty($strAdditions)) {
            foreach ($strAdditions as $objAddition) {
                $strObjectType = '';
                
                switch (get_class($objAddition)) {
                    case 'GoogleSitemap2\Image':
                        $strObjectType = 'image';
                        break;
                }
                if (!in_array($strObjectType, $this->arrObjectTypes)) {
                    $this->arrObjectTypes[] = $strObjectType;
                }
            }
        }
        $this->arrCurrentUrls[] = $objUrl;
        $this->strCurrentMapCode .= $objUrl->html($strAdditions);
        
        $boolNeedSaveMap = $this->needSaveMap();
        if ($boolNeedSaveMap) {
            $this->saveCurrentMap();
        }
    }
    
    private function clearOld () {
        fopen($this->strIndexFile, 'w');
        $objHandle = opendir($this->strMapsDirectory);
        if ($objHandle !== false) {
            while (false !== ($strFile = readdir($objHandle))) {
                if ($strFile === '.' || $strFile === '..') {
                    continue;
                }
                unlink($this->strMapsDirectory . $strFile);
            }
            closedir($objHandle);
        }
    }
    
    
    public function save () {
        $this->saveCurrentMap();
        $this->saveMapIndex();
    }
    
    private function saveCurrentMap () {
        if (count($this->arrCurrentUrls) === 0) {
            return false;
        }
        
        if (!is_dir($this->strMapsDirectory)) {
            mkdir($this->strMapsDirectory, 0777);
            chmod($this->strMapsDirectory, 0777);
        }
        
        $strMapFile = sprintf('%ssitemap-%d.xml', $this->strMapsDirectory, $this->numCurrentMapNo);
        $objFileHandler = fopen($strMapFile, 'w');
        fwrite($objFileHandler, self::MAP_XML_TAG.PHP_EOL);
        $strUrlsetString = $this->getUrlsetStringCurrentMap();
        fwrite($objFileHandler, $strUrlsetString.PHP_EOL);
        fwrite($objFileHandler, $this->strCurrentMapCode);
        fwrite($objFileHandler, '</urlset>');
        fclose($objFileHandler);
        
        $this->arrGeneratedMapFiles[] = $this->strCurrentDomain . $this->strMapsDirectoryUri .'sitemap-'.$this->numCurrentMapNo.'.xml';
        $this->numCurrentMapNo++;
        
        $this->resetCurrentMap();
    }
    
    private function getUrlsetStringCurrentMap () {
        $arrXmlns = array();
        foreach ($this->arrObjectTypes as $strObjectType) {
            switch ($strObjectType) {
                case 'url':
                    $arrXmlns[] = 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
                    break;
                case 'image':
                    $arrXmlns[] = 'xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"';
                    break;
                case 'video':
                    $arrXmlns[] = 'xmlns:video="http://www.google.com/schemas/sitemap-video/1.1"';
                    break;
                case 'mobile':
                    $arrXmlns[] = 'xmlns:mobile="http://www.google.com/schemas/sitemap-mobile/1.0"';
                    break;
                default:
                    die('nieznany typ obiektu w mapie');
                    break;
            }
        }
        
        $strReturn = sprintf('<urlset %s>', join(' ', $arrXmlns));
        return $strReturn;
    }
    
    private function needSaveMap () {
        $boolNeedSave = false;
        if (count($this->arrCurrentUrls) === self::$MAX_MAP_LINKS) {
            $boolNeedSave = true;
        } else if (strlen($this->strCurrentMapCode) > self::$MAX_MAP_SIZE_BYTES * 0.9) {
            $boolNeedSave = true;
        }
        return $boolNeedSave;
    }
    
    public function resetCurrentMap () {
        $this->arrObjectTypes = array();
        $this->arrCurrentUrls = array();
        $this->strCurrentMapCode = '';
    }
    
    private function saveMapIndex () {
        $objFileHandler = fopen($this->strIndexFile, 'w');
        fwrite($objFileHandler, '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL);
        fwrite($objFileHandler, '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.PHP_EOL);
        foreach ($this->arrGeneratedMapFiles as $strMapFile) {
            fwrite($objFileHandler, '<sitemap>'.PHP_EOL);
            fwrite($objFileHandler, sprintf('<loc>%s</loc>%s', $strMapFile, PHP_EOL));
            fwrite($objFileHandler, '</sitemap>'.PHP_EOL);
        }
        fwrite($objFileHandler, $this->strCurrentMapCode);
        fwrite($objFileHandler, '</sitemapindex>');
        fclose($objFileHandler);
    }
    
}

namespace GoogleSitemap2;

class Url {
    
    private $strLoc = '';
    private $numPriority = 0;
    private $strChangeFreq = '';
    
    public function __construct ($strLoc, $numPriority, $strChangeFreq) {
        $this->strLoc = $strLoc;
        $this->numPriority = $numPriority;
        $this->strChangeFreq = $strChangeFreq;
    }
    
    public function html ($arrAdditions) {
        $strHtml = "<url>".PHP_EOL;
        if (!empty($this->strLoc)) {
            $strHtml .= sprintf("\t<loc>%s</loc>%s", \backend\GoogleSitemap2::escape($this->strLoc), PHP_EOL);
        }
        if (!empty($this->strChangeFreq)) {
            $strHtml .= "\t<changefreq>{$this->strChangeFreq}</changefreq>".PHP_EOL;
        }
        if (!empty($this->numPriority)) {
            $strHtml .= "\t<priority>{$this->numPriority}</priority>".PHP_EOL;
        }
        if (!empty($arrAdditions)) {
            foreach ($arrAdditions as $objAddition) {
                $strHtml .= $objAddition->html();
            }
        }
        $strHtml .= "</url>".PHP_EOL;
        
        return $strHtml;
    }
    
}

class Image {
    
    private $strLoc = '';
    private $strTitle = '';
    private $strCaption = '';
    
    public function __construct ($strLoc, $strTitle = '', $strCaption = '') {
        $this->strLoc = $strLoc;
        $this->strTitle = $strTitle;
        $this->strCaption = $strCaption;
    }
    
    public function html() {
        $strHtml = "\t<image:image>".PHP_EOL;
        if (!empty($this->strLoc)) {
            $strHtml .= sprintf("\t\t<image:loc>%s</image:loc>%s", \backend\GoogleSitemap2::escape($this->strLoc), PHP_EOL);
        }
        if (!empty($this->strTitle)) {
            $strHtml .= sprintf("\t\t<image:title>%s</image:title>%s", \backend\GoogleSitemap2::escape($this->strTitle), PHP_EOL);
        }
        if (!empty($this->strCaption)) {
            $strHtml .= sprintf("\t\t<image:caption>%s</image:caption>%s", \backend\GoogleSitemap2::escape($this->strCaption), PHP_EOL);
        }
        $strHtml .= "\t</image:image>".PHP_EOL;
        
        return $strHtml;
    }
    
}

