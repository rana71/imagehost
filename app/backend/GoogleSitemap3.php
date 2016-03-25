<?php namespace backend;

class GoogleSitemap3
{
    public $strRootDir = '';
    
    public $numUrlsThisMap = 0;
    public $numSizeThisMap = 0;
    public $numCurrentMapNo = 0;
    
    public $strWorkingDomain = '';
    
    private $objIndexHandler = null;
    private $objCurrentMapHandler = null;
    
    const CHANGE_ALWAYS = 'always';
    const CHANGE_HOURLY = 'hourly';
    const CHANGE_DAILY = 'daily';
    const CHANGE_WEEKLY = 'weekly';
    const CHANGE_MONTHLY = 'monthly';
    const CHANGE_YEARLY = 'yearly';
    const CHANGE_NEVER = 'never';
    
    private static $MAX_MAP_LINKS = 40000;
    private static $MAX_MAP_SIZE_BYTES = 41943040; // 40mb;
    
    public static function escape ($strInput) {
        $strOutput = htmlspecialchars($strInput, ENT_QUOTES);
        return $strOutput;
    }
    
    public function prepare () {
        if (!file_exists($this->strRootDir.'/sitemaps-xml')) {
            mkdir($this->strRootDir.'/sitemaps-xml');
        }
        $this->removeTmpFiles();

        $strIndexFilepath = $this->strRootDir . '/sitemap.xml.tmp';
        $this->objIndexHandler = fopen($strIndexFilepath,  'w+');
        $strIndexHeader = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
EOF;
        fwrite($this->objIndexHandler, $strIndexHeader);

        $this->initNewCurrentMap();
    }
    
    public function finish () {
        $strIndexFooter = <<<EOF
</sitemapindex>
EOF;
        fwrite($this->objIndexHandler, $strIndexFooter);

        $this->saveEndCurrentMap();
        
        // removing old real map
        $this->removePreviousMap();
        $this->moveTmpMapToReal();
    }
    
    private function moveTmpMapToReal () {
        $strTmpIndex = $this->strRootDir .'/sitemap.xml.tmp';
        $strRealIndex = $this->strRootDir .'/sitemap.xml';
        rename($strTmpIndex, $strRealIndex);
        
        $objDir = new \DirectoryIterator($this->strRootDir . '/sitemaps-xml/');
        foreach ($objDir as $objFile) {
            if ($objFile->isDot() === false && $objFile->getExtension() === 'tmp') {
                $strTmpFile = $strFilepath = $this->strRootDir . '/sitemaps-xml/' . $objDir->getFilename();
                $strRealFile = $strFilepath = $this->strRootDir . '/sitemaps-xml/' . substr($objDir->getFilename(), 0, -4);
                rename($strTmpFile, $strRealFile);
            }
        }
    }
    
    private function removePreviousMap () {
        $strIndex = $this->strRootDir . '/sitemap.xml';
        if (file_exists($strIndex)) {
            unlink($strIndex);
        }
        
        $objDir = new \DirectoryIterator($this->strRootDir . '/sitemaps-xml/');
        foreach ($objDir as $objFile) {
            if ($objFile->isDot() === false && $objFile->getExtension() === 'xml') {
                $strFilepath = $this->strRootDir . '/sitemaps-xml/' . $objDir->getFilename();
                unlink ($strFilepath);
            }
        }
    }
    
    private function removeTmpFiles () {
        $strIndex = $this->strRootDir . '/sitemap.xml.tmp';
        if (file_exists($strIndex)) {
            unlink($strIndex);
        }
        
        $objDir = new \DirectoryIterator($this->strRootDir . '/sitemaps-xml/');
        foreach ($objDir as $objFile) {
            if ($objFile->isDot() === false && $objFile->getExtension() === 'tmp') {
                $strFilepath = $this->strRootDir . '/sitemaps-xml/' . $objDir->getFilename();
                unlink ($strFilepath);
            }
        }
    }
    
    private function saveEndCurrentMap () {
        $strMapFooter = <<<EOF
</urlset>
EOF;
        fwrite($this->objCurrentMapHandler, $strMapFooter);
    }
    
    private function initNewCurrentMap () {
        $this->numCurrentMapNo++;
        $strCurrentMapFilepath = $this->strRootDir . '/sitemaps-xml/sitemap-'.$this->numCurrentMapNo.'.xml.tmp';
        $this->objCurrentMapHandler = fopen($strCurrentMapFilepath, 'w+');
        $strMapHeader = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"> 
EOF;
        fwrite ($this->objCurrentMapHandler, $strMapHeader);
        $this->numSizeThisMap = strlen($strMapHeader);
        
        $this->numUrlsThisMap = 0;
        
        // save url in index
        $strSitemapLinkPattern = <<<EOF
<sitemap>
    <loc>%s</loc>
    <lastmod>%s</lastmod>
</sitemap>
EOF;
        $strCurrentDate = date('Y-m-d');
        $strSitemapLink = sprintf($strSitemapLinkPattern, $this->strWorkingDomain.'/sitemaps-xml/sitemap-'.$this->numCurrentMapNo.'.xml', $strCurrentDate);
        fwrite($this->objIndexHandler, $strSitemapLink);
    }
    
    private function getUrlSitemapString ($strUrl, $numPriority = 0, $strChangeFreq = '', $arrImages = array()) {
        $strString = '<url>'.PHP_EOL;
        $strString .= sprintf('<loc>%s</loc>', self::escape($strUrl)) . PHP_EOL;
        
        
        if (!empty($numPriority)) {
            $strString .= sprintf('<priority>%s</priority>', self::escape($numPriority)) . PHP_EOL;
        }
        
        if (!empty($strChangeFreq)) {
            $strString .= sprintf('<changefreq>%s</changefreq>', self::escape($strChangeFreq)) . PHP_EOL;
        }
        
        if (!empty($arrImages)) {
            foreach ($arrImages as $arrImage) {
                if (empty($arrImage['image_url']) || $arrImage['image_url'] === '/') {
                    continue;
                }
                $strString .= '<image:image>' . PHP_EOL;
                $strString .= sprintf('<image:loc>%s</image:loc>', self::escape($arrImage['image_url'])) . PHP_EOL;
                $arrCaptions = array();
                
                if (!empty($arrImage['title'])) {
                    $arrCaptions[] = $arrImage['title'];
                }
                if (!empty($arrImage['description'])) {
                    $arrCaptions[] = $arrImage['description'];
                }
                
                if (!empty($arrCaptions)) {
                    $strString .= sprintf('<image:caption>%s</image:caption>', self::escape(join('. ', $arrCaptions))) . PHP_EOL;
                }
                $strString .= '</image:image>' . PHP_EOL;
            }
            
        }
        $strString .= '</url>' . PHP_EOL;
        
        return $strString;
    }
    
    
    public function addUrl ($strUrl, $numPriority = 0, $strChangeFreq = '', $arrImages = array()) {
        $strUrlString = $this->getUrlSitemapString($strUrl, $numPriority, $strChangeFreq, $arrImages);
        if ($this->numUrlsThisMap + 1 > self::$MAX_MAP_LINKS || $this->numSizeThisMap + strlen($strUrlString) >= self::$MAX_MAP_SIZE_BYTES - 50) {
            $this->saveEndCurrentMap();
            $this->initNewCurrentMap();
        }
        
        fwrite($this->objCurrentMapHandler, $strUrlString);
        $this->numUrlsThisMap++;
        
    }
    
    
}

