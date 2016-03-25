<?php namespace backend;

class GoogleSitemap
{
    private static $MAX_LINKS = 30000;
    private static $MAX_SIZE = 20971520; // B / 20MB

    const MAP_HEADER = '<?xml version="1.0" encoding="UTF-8" ?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
    const MAP_FOOTER = '</urlset>';
    const CHANGE_ALWAYS = 'always';
    const CHANGE_HOUR = 'hourly';
    const CHANGE_DAY = 'daily';
    const CHANGE_WEEK = 'weekly';
    const CHANGE_MONTH = 'monthly';
    const CHANGE_YEAR = 'yearly';
    const CHANGE_NEVER = 'never';

    private $protocol = 'http://';
    private $domain = '';
    private $mapsDir = 'sitemaps-xml/';
    private $items = array();
    private $maps = array();
    private $numMemoryLimitBytes = -1;
    
    public function __construct () {
        $this->numMemoryLimitBytes = intval(return_bytes(ini_get('memory_limit')));
    }

    public function setDomain($fullUrl)
    {
        $this->domain = $fullUrl;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;
    }

    public function getProtocol()
    {
        return $this->protocol;
    }
    
    public function AddItem($loc, $priority, $changefreq, $urlType = '', $arrImages = array())
    {
        $item = new GoogleSitemap_Url();
        $item->loc = $loc;
        $item->changefreq = $changefreq;
        $item->priority = $priority;
        $item->type = $urlType;
        $this->items[] = $item;
        
        if (!empty($arrImages)) {
            foreach ($arrImages as $objImage) {
                $item->images[] = $objImage;
            }
        }
        
        
        
        echo '<pre>';
        print_r($item);
        exit();
        
        $numCurrentMemoryUsageBytes = memory_get_usage();
//        echo $this->numMemoryLimitBytes.'x';
        // 
        if ($this->numMemoryLimitBytes !== -1 && ($numCurrentMemoryUsageBytes > $this->numMemoryLimitBytes * 0.75)) {
            echo 'reach limit. limit is '.($this->numMemoryLimitBytes/1024/1024).'mb, current is '.($numCurrentMemoryUsageBytes/1024/1024).'mb'.PHP_EOL;
            echo 'save map';
            exit();
        }
        
//        echo 'current memory usage: '.memory_get_usage().PHP_EOL;
//        exit();
//        $boolIsMemoryEnds = $this->isMemoryEnds();
//        if ($this->)
        
        return $item;
    }
    
    private function isMemoryEnds() {
        $boolIsEnds = false;
        $numCurrentMemoryUsageBytes = memory_get_usage();
        if ($this->numMemoryLimitBytes !== -1 && ($numCurrentMemoryUsageBytes > $this->numMemoryLimitBytes * 0.75)) {
            $boolIsEnds = true;
        }
        return $boolIsEnds;
    }

    private function GenreSitemap()
    {
        $mapNo = 1;
        $linkNo = 1;
        foreach ($this->items as $item) {
            $mapId = $mapNo;
            if (!empty($item->type)) {
                $mapId = "{$item->type}-{$mapNo}";
            }
            if (!isset($this->maps[$mapId])) {
                $this->maps[$mapId] = array(
                    'items' => array(),
                    'size' => strlen(GoogleSitemap::MAP_HEADER.GoogleSitemap::MAP_FOOTER)
                );
            }
            $nodeSize = strlen($item->html());

            if (( (int)$this->maps[$mapId]['size'] + $nodeSize > GoogleSitemap::$MAX_SIZE ) || ( ($linkNo - 1) == GoogleSitemap::$MAX_LINKS )) {
                $linkNo = 1;
                $mapNo++;
            }

            $this->maps[$mapId]['items'][] = $item;
            $this->maps[$mapId]['size'] += $nodeSize;
            $linkNo++;
        }
    }

    public function Save($indexFile, $mapsDir)
    {
        if (!file_exists($mapsDir) || !is_dir($mapsDir)) {
            mkdir($mapsDir, 0777);
        }
        $this->GenreSitemap();
        $index_content = $this->GenreMapIndexContent();
        $fp = fopen($indexFile, 'w');
        fwrite($fp, $index_content);
        fclose($fp);
        foreach ($this->maps as $id => $map) {
            $map_content = $this->GenreMapContent($id);
            $fp = fopen($mapsDir.'/sitemap-'.$id.'.xml', 'w');
            fwrite($fp, $map_content);
            fclose($fp);
        }
    }

    private function GenreMapContent($id)
    {
        $output = GoogleSitemap::MAP_HEADER.PHP_EOL;
        foreach ($this->maps[$id]['items'] as $item) {
            $output .= $item->html();
        }
        $output .= GoogleSitemap::MAP_FOOTER.PHP_EOL;
        return $output;
    }

    private function GenreMapIndexContent()
    {
        $output = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
        $output .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.PHP_EOL;
        foreach ($this->maps as $id => $map) {
            $output .= "\t<sitemap>".PHP_EOL;
            $output .= "\t\t<loc>".$this->getProtocol().$this->getDomain()."/{$this->mapsDir}sitemap-{$id}.xml</loc>".PHP_EOL;
            $output .= "\t</sitemap>".PHP_EOL;
        }
        $output .= "</sitemapindex>".PHP_EOL;
        return $output;
    }

    public function SetLinksLimit($limit)
    {
        GoogleSitemap::$MAX_LINKS = $limit;
    }

    public function getItemsCount()
    {
        return count($this->items);
    }

    public function Summary()
    {
        echo 'Ilość urli: '.$this->getItemsCount().PHP_EOL;
        echo 'Ilość plików map: '.count($this->maps).PHP_EOL;
    }

}

class GoogleSitemap_Url
{
    public $loc = '';
    public $changefreq = 'monthly';
    public $priority = 0.5;
    public $type = '';
    public $images = array();

    public function addImage($loc)
    {
        $image = new GoogleSitemap_Url_Image();
        $image->loc = $loc;
        $this->images[] = $image;
        return $image;
    }

    public function html()
    {
        $html = "<url>".PHP_EOL;
        if (!empty($this->loc)) {
            $html .= "\t<loc>{$this->loc}</loc>".PHP_EOL;
        }
        if (!empty($this->changefreq)) {
            $html .= "\t<changefreq>{$this->changefreq}</changefreq>".PHP_EOL;
        }
        if (!empty($this->priority)) {
            $html .= "\t<priority>{$this->priority}</priority>".PHP_EOL;
        }
        if (!empty($this->images)) {
            foreach ($this->images as $image) {
                $html .= $image->html();
            }
        }
        $html .= "</url>".PHP_EOL;

        return $html;
    }

}

class GoogleSitemap_Url_Image
{
    public $loc = '';
    
    public function __construct ($strUrl) {
        $this->loc = $strUrl;
    }

    public function html()
    {

        if (empty($this->loc)) {
            return '';
        }
        $html = "<image:image>".PHP_EOL;
        $html .= "\t<image:loc>{$this->loc}</image:loc>".PHP_EOL;
        $html .= "</image:image>".PHP_EOL;

        return $html;
    }

}
