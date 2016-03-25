<?php

$socket_address = '127.0.0.1';
$socket_port = 43648;

if (@fsockopen($socket_address, $socket_port)) {
    exit( 'Another  is running, dying' );
}

if (( $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
    exit("socket_create() failed: reason: ".socket_strerror(socket_last_error())."\n");
}

if (socket_bind($socket, $socket_address, $socket_port) === false) {
    exit("socket_bind() failed: reason: ".socket_strerror(socket_last_error($socket))."\n");
}

if (socket_listen($socket, 1) === false) {
    exit("socket_listen() failed: reason: ".socket_strerror(socket_last_error($socket))."\n");
}


date_default_timezone_set('Europe/Warsaw');
require dirname(__FILE__).'/../../autoload.php';
require dirname(__FILE__).'/_databases.php';

function microtime_float () {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}


$strEnvironment = $argv[1];
$arrOffersDbConfig = getDb($strEnvironment);

$arrConfig = array(
    'numOffersGetFromOneSite' => 500
);

$objImgjetDb = new PDO(
    sprintf("pgsql:host=%s;port=5432;dbname=%s", $arrOffersDbConfig['host'], $arrOffersDbConfig['dbname']), 
    $arrOffersDbConfig['user'], 
    $arrOffersDbConfig['password'], 
    array(
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    )
);

$arrGlobalDbConfig = array(
    'host' => 'gooroo-pgsql.inten.pl', 
    'user' => 'blogi_default', 
    'password' => 'KuRwAmAc123#', 
    'dbname' => 'blogi_global_mango1'
);


$objGlobalDb = new PDO(
    sprintf("pgsql:host=%s;port=5432;dbname=%s", $arrGlobalDbConfig['host'], $arrGlobalDbConfig['dbname']), 
    $arrGlobalDbConfig['user'], 
    $arrGlobalDbConfig['password'], 
    array(
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    )
);

$arrAddedOffersFromBlog = array();
$strQ = "SELECT \"idSite\", host, mysql_host, mysql_dbname, mysql_user, mysql_pass FROM site_config";
$objSql = $objGlobalDb->prepare($strQ);
$objSql->execute();
$arrSites = $objSql->fetchAll();
$ii = 1;

$getted = $ommited = 0;
foreach ($arrSites as $arrSite) {
    $numBlogId = intval($arrSite['idSite']);
    $strBlogHost = $arrSite['host'];
    $strMysqlHost = $arrSite['mysql_host'];
    $strMysqlDbname = $arrSite['mysql_dbname'];
    $strMysqlUser = $arrSite['mysql_user'];
    $strMysqlPass = $arrSite['mysql_pass'];
    
    echo "\n\n\n !!!!!!!!!!!! do site ".$strBlogHost." (".$ii."/".count($arrSites).") !!!!!!!!!!!! \n\n\n";
    
    $arrOffersToInsert = array();
    
    
    $strDsn = 'mysql:dbname=' . $strMysqlDbname . ';host=' . $strMysqlHost;
    $objSiteDb = new PDO($strDsn, $strMysqlUser, $strMysqlPass, array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', 
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ));
    
    if (!isset($arrAddedOffersFromBlog[$numBlogId])) {
        $strQ = "SELECT source_post_id FROM offer WHERE source_blog_id = :source_blog_id";
        $objSql = $objImgjetDb->prepare($strQ);
        $objSql->execute(array(
            ':source_blog_id' => $numBlogId
        ));
        $arrAddedOffersFromBlog[$numBlogId] = $objSql->fetchAll(PDO::FETCH_COLUMN, 0);
    }
    
    $arrOffers = array();
    $strBlogExceptions = '';
    if (!empty($arrAddedOffersFromBlog[$numBlogId])) {
        $strBlogExceptions = 'AND ID NOT IN ('.join(', ', $arrAddedOffersFromBlog[$numBlogId]).')';
    }
    $strQ = sprintf(
        "SELECT * "
            . "FROM wp_posts "
            . "WHERE post_status = 'publish' AND post_type = 'post' %s "
            . "ORDER BY post_date DESC "
            . "LIMIT %d", 
        $strBlogExceptions, 
        $arrConfig['numOffersGetFromOneSite']);
//    echo $strQ."\n\n";
    $objSql = $objSiteDb->prepare($strQ);
    $objSql->execute();
    $arrPosts = $objSql->fetchAll();
    foreach ($arrPosts as $arrPost) {
        $arrOffers[$arrPost['ID']] = $arrPost;
    }
    $strQ = sprintf(
        "SELECT post_id, meta_key, meta_value "
            . "FROM wp_postmeta "
            . "WHERE meta_key IN ('photo_scalled', 'photo_orginal', 'photo_big', 'photos_dir', 'idAuction') "
                . "AND post_id IN (%s)", 
        join(', ', array_keys($arrOffers))
    );
    $objSql = $objSiteDb->prepare($strQ);
    $objSql->execute();
    $arrImages = $objSql->fetchAll();
    
    $arrPostsImages = array();
    foreach ($arrImages as $arrImage) {
        $numPostId = intval($arrImage['post_id']);
        
        if ($arrImage['meta_key'] === 'photos_dir' || $arrImage['meta_key'] === 'idAuction') {
            $arrOffers[$numPostId][$arrImage['meta_key']] = $arrImage['meta_value'];
        } else {
            if (!isset($arrPostsImages[$numPostId])) {
                $arrPostsImages[$numPostId] = array();
            }
            $arrPostsImages[$numPostId][$arrImage['meta_key']] = $arrImage['meta_value'];
        }
    }
    foreach ($arrOffers as $arrOffer) {
        if (empty($arrOffer['idAuction'])) {
            continue;
        }
        $strTitle = $arrOffer['post_title'];
        $strSlug = $arrOffer['post_name'];
        $numSourceBlogId = $numBlogId;
        $numSourcePostId = $arrOffer['ID'];
        $numAllegroAuctionId = $arrOffer['idAuction'];
        if (!empty($arrOffer['photos_dir'])) {
            $strPhotosDir = $arrOffer['photos_dir'];
        } else {
            $strPhotosDir = 'photos';
        }
        
        
        $strImageFilename = '';
        if (!empty($arrPostsImages[$numSourcePostId]['photo_big'])) {
            $strImageFilename = $arrPostsImages[$numSourcePostId]['photo_big'];
        } else if (!empty($arrPostsImages[$numSourcePostId]['photo_scalled'])) {
            $strImageFilename = $arrPostsImages[$numSourcePostId]['photo_scalled'];
        } else if (!empty($arrPostsImages[$numSourcePostId]['photo_orginal'])) {
            $strImageFilename = $arrPostsImages[$numSourcePostId]['photo_orginal'];
        }
        $strImageSource = 'http://'.$strBlogHost.'/'.$strPhotosDir.'/'.$strImageFilename;
        
        
        $strQ = "INSERT INTO offer ( "
                    . "title_pl, slug_pl, image_source, source_blog_id, source_post_id, allegro_auction_id, shows_count_increaser "
                . ") "
                . "SELECT :title_pl, :slug_pl, :image_source, :source_blog_id, :source_post_id, :allegro_auction_id, :shows_count_increaser "
                . "WHERE (NOT EXISTS("
                    . "SELECT 1 FROM offer "
                    . "WHERE allegro_auction_id = :allegro_auction_id "
                        . "OR title_pl = ".$objImgjetDb->quote($strTitle)." "
                        . "OR slug_pl = ".$objImgjetDb->quote($strSlug)." "
                . "))";
//        echo $strQ."\n";
        $objSql = $objImgjetDb->prepare($strQ);
        $arrParams = array(
            ':title_pl' => $strTitle, 
            ':slug_pl' => $strSlug, 
            ':image_source' => $strImageSource, 
            ':allegro_auction_id' => $numAllegroAuctionId, 
            ':source_blog_id' => $numSourceBlogId, 
            ':source_post_id' => $numSourcePostId, 
            ':shows_count_increaser' => rand(1,200)
        );
        
//        echo '<pre>';
//        print_r($arrParams);
//        echo "</pre>\n";

        $objSql->execute($arrParams);
    
    }
    unset($objSiteDb);
    $ii++;
}

socket_close($socket);
unset($objImgjetDb, $objBlogsGlobalDb);
exit();
