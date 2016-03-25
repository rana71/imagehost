<?php
namespace backend\cron;

use \PDO;

class Blogs
{
    private $arrGlobalDbConfig = array(
        'type' => 'pgsql', 
        'host' => 'gooroo-pgsql.inten.pl', 
        'user' => 'blogi_default', 
        'password' => 'KuRwAmAc123#', 
        'dbname' => 'blogi_global_mango1'
    );
    
    private $objGlobalDb = null;
    
    public function countGalleriesPhotos () {
        $numGalleryPhotosTotal = 0;
        $numIterator = 1;
        
        $this->objGlobalDb = $this->getDbConnect($this->arrGlobalDbConfig);
        $arrBlogs = $this->getBlogs();
        $numBlogsCount = count($arrBlogs);
        foreach ($arrBlogs as $arrBlog) {
            echo sprintf("Blog %d/%d (%s) ... ", $numIterator, $numBlogsCount, $arrBlog['host']);
            $arrDbConfig = array(
                'type' => 'mysql', 
                'host' => 'gooroo-mysql.inten.pl', //$arrBlog['mysql_host'], 
                'dbname' => $arrBlog['mysql_dbname'], 
                'user' => $arrBlog['mysql_user'], 
                'password' => $arrBlog['mysql_pass']
            );
//            $objBlogDb = $this->getDbConnect($arrDbConfig);
            $strDsn = 'mysql:dbname=' . $arrDbConfig['dbname'] . ';host=' . $arrDbConfig['host'];
            $objBlogDb = new PDO($strDsn, $arrDbConfig['user'], $arrDbConfig['password'], array(
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', 
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ));
            
            $numGalleryPhotosBlog = $this->countGalleryPhotos($objBlogDb);
            $numGalleryPhotosTotal += $numGalleryPhotosBlog;
            
            echo sprintf("%d photos in gallery, total: %d %s", $numGalleryPhotosBlog, $numGalleryPhotosTotal, PHP_EOL);
            
            unset($objBlogDb);
            $numIterator++;
        }
        
        unset($this->objGlobalDb);
    }
    
    public function countAdditionalPhotos () {
        $numAdditionalPostsTotal = 0;
        $numIterator = 1;
        
        $this->objGlobalDb = $this->getDbConnect($this->arrGlobalDbConfig);
        $arrBlogs = $this->getBlogs();
        $numBlogsCount = count($arrBlogs);
        foreach ($arrBlogs as $arrBlog) {
            echo sprintf("Blog %d/%d (%s) ... ", $numIterator, $numBlogsCount, $arrBlog['host']);
            $arrDbConfig = array(
                'type' => 'mysql', 
                'host' => $arrBlog['mysql_host'], 
                'dbname' => $arrBlog['mysql_dbname'], 
                'user' => $arrBlog['mysql_user'], 
                'password' => $arrBlog['mysql_pass']
            );
            $objBlogDb = $this->getDbConnect($arrDbConfig);
            $numAdditionalPostsBlog = $this->countAdditionalPosts($objBlogDb);
            $numAdditionalPostsTotal += $numAdditionalPostsBlog;
            
            echo sprintf("%d additional posts here, total: %d %s", $numAdditionalPostsBlog, $numAdditionalPostsTotal, PHP_EOL);
            
            unset($objBlogDb);
            $numIterator++;
        }
        
        unset($this->objGlobalDb);
    }
    
    
    private function getDbConnect ($arrDbConfig) {
        $objDb = new PDO(
            sprintf("%s:host=%s;port=5432;dbname=%s", $arrDbConfig['type'], $arrDbConfig['host'], $arrDbConfig['dbname']), 
            $arrDbConfig['user'], 
            $arrDbConfig['password'], 
            array(
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            )
        );
        return $objDb;
    }
    
    private function getBlogs () {
        $strQ = <<<EOF
SELECT "idSite", host, mysql_host, mysql_dbname, mysql_user, mysql_pass 
FROM site_config
EOF;
        $objSth = $this->objGlobalDb->prepare($strQ);
        $objSth->execute();
        $arrBlogs = $objSth->fetchAll();
        
        return $arrBlogs;
    }
    
    private function countAdditionalPosts ($objBlogDb) {
        
    }
    
    private function countGalleryPhotos ($objBlogDb) {
        $strQ = <<<EOF
SELECT count(1) AS ex 
FROM galleryPhoto
EOF;
        $objSth = $objBlogDb->prepare($strQ);
        $objSth->execute();
        $arrResult = $objSth->fetch();
        
        return intval($arrResult['ex']);
    }
    
}