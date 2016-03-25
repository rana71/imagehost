<?php
namespace backend;

class Cache 
{
    
    private $memcache;
    private $connected = false;
    private $enable_memcache = true;
    private $key_prefix = 'imgjet-';
    private $strGlobalKey = '1';
    private $arrConfig = array(
        'host' => 'localhost', 
        'port' => 11211
    );
            
    const DEFAULT_LIFETIME = 2592000;
    
    public function __construct(){}
    
    public function disable() 
    {
        $this->enable_memcache = false;
    }
    
    public function enable() 
    {
        $this->enable_memcache = true;
    }
    
    public function getAllKeys()
    {
        $this->connect();
        $keys = $this->memcache->getAllKeys();
        return $keys;
    }
    
    public function set( $strName, $mulValue, $numLifetime = self::DEFAULT_LIFETIME, $boolGlobalCache=false )
    {
        if( !$this->enable_memcache )
        {
            return false;
        }
        $this->connect();
        
        if (!$boolGlobalCache) {
            $strName = $this->key_prefix.$this->strGlobalKey.$strName;
        }
        $arrCache = array(
            'data' => serialize($mulValue), 
            'store_time' => time(), 
            'lifetime' => $numLifetime
        );
//        echo 'store in memcache '.$strName.':<pre>';
//        print_r($arrCache);
//        echo '</pre>';
        return $this->memcache->set($strName, $arrCache, null, 0);
    }
    
    public function get($strName, $boolGlobalCache = false) {
        if (!$this->enable_memcache) {
            return false;
        }
        $mulReturn = false;
        $this->connect();
        if (!$boolGlobalCache) {
            $strName = $this->key_prefix.$this->strGlobalKey.$strName;
        }
        $arrCache = $this->memcache->get($strName);
        if ($arrCache !== false) {
            $numCacheLiveUntil = $arrCache['store_time'] + $arrCache['lifetime'];
            
            if (time() < $numCacheLiveUntil || $arrCache['lifetime'] == 0) {
                $mulReturn = unserialize($arrCache['data']);
            }
        }
        return $mulReturn;
    }
    
    private function connect()
    {
        if( !$this->connected && $this->enable_memcache )
        {
            $this->memcache = new \Memcache();
            $this->memcache->connect($this->arrConfig['host'], $this->arrConfig['port']);
            $this->connected = true;
        }
    }
    
    public function test () {
        if ($this->enable_memcache === false) {
            $boolResult = true;
        } else {
            $objMemcache = new \Memcache();
            $boolResult = $objMemcache->connect($this->arrConfig['host'], $this->arrConfig['port']);            
        }
        return $boolResult;
    }
    
    public function __destruct()
    {
        if( $this->memcache instanceof Memcache )
        {
            $this->memcache->close();
        }
    }
    
}
