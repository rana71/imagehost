<?php
namespace backend\cron;

use \backend\DbFactory;
use backend\RunOnce;
use \backend\artifact\model\ArtifactModel;
use backend\tag\model\TagModel;

use \PDO;

class ImportOffers
{
    
    private static $socket_address = '127.0.0.1';
    private static $socket_port = 43648;
    private static $socket;
    
    private static $arrConfig = array(
        'numOffersGetFromOneSite' => 200
    );
    
    private static function getGlobalDb () {
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
        
        return $objGlobalDb;
    }
    
    public static function makrPostsAsMovedInBlogs ($strConnectionName) {
        try {
            $objRunOnce = new RunOnce(41843);
            $objRunOnce->start();
            
            DbFactory::setDefaultConnection($strConnectionName);
            $objImgjetDb = DbFactory::getInstance();
            $objGlobalDb = self::getGlobalDb();

            $objSqlSelectOffers = $objImgjetDb->prepare("SELECT id, source_post_id FROM offer WHERE source_blog_id = :source_blog_id");

            $objSql = $objGlobalDb->prepare('SELECT "idSite", host, mysql_host, mysql_dbname, mysql_user, mysql_pass FROM site_config');
            $objSql->execute();
            $arrSites = $objSql->fetchAll();
            $ii=1;
            foreach ($arrSites as $arrSite) {
                $numBlogId = intval($arrSite['idSite']);
                $strBlogHost = $arrSite['host'];
                $strMysqlHost = $arrSite['mysql_host'];
                $strMysqlDbname = $arrSite['mysql_dbname'];
                $strMysqlUser = $arrSite['mysql_user'];
                $strMysqlPass = $arrSite['mysql_pass'];

                echo "site ".$strBlogHost." #".$numBlogId." (".$ii."/".count($arrSites).") ... ";

                $objSqlSelectOffers->execute($arrP = array(
                    ':source_blog_id' => $numBlogId
                ));
                
//                print_r($arrP);
                $arrOffers = $objSqlSelectOffers->fetchAll();
                echo "grabbed ".count($arrOffers)." offers from imged".PHP_EOL;
                if (!empty($arrOffers)) {
                    $strDsn = 'mysql:dbname=' . $strMysqlDbname . ';host=' . $strMysqlHost;
                    $objSiteDb = new PDO($strDsn, $strMysqlUser, $strMysqlPass, array(
                        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', 
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ));
                    foreach ($arrOffers as $arrOffer) {
                        $strQ = <<<EOF
REPLACE INTO wp_postmeta (
    post_id, meta_key, meta_value 
) VALUES (
    :post_id, 'imged_pl_id', :imged_id
)
EOF;
                        $objSthReplace = $objSiteDb->prepare($strQ);
                        $objSthReplace->execute($arrP = array(
                            ':post_id' => $arrOffer['source_post_id'], 
                            ':imged_id' => $arrOffer['id']
                        ));
                    }
                    unset($objSiteDb);
                }
                $ii++;
            }

            unset($objGlobalDb);
            unset($objImgjetDb);
            $objRunOnce->end();
        } catch (Exception $e) {
            echo $e->getMessage().PHP_EOL;
        }
    }
    
    public function importMovedToS3 ($numLimitPosts, $numLimitBlogs) {
        $numTimeStart = $this->microtime_float();
        $arrSocket = array(
            'address' => '127.0.0.1', 
            'port' => 42765, 
            'socket' => null
        );
        
        if (@fsockopen($arrSocket['address'], $arrSocket['port'])) {
            exit( 'Another  is running, dying' );
        }

        if (( $arrSocket['socket'] = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
            exit("socket_create() failed: reason: ".socket_strerror(socket_last_error())."\n");
        }

        if (socket_bind($arrSocket['socket'], $arrSocket['address'], $arrSocket['port']) === false) {
            exit("socket_bind() failed: reason: ".socket_strerror(socket_last_error($arrSocket['socket']))."\n");
        }

        if (socket_listen($arrSocket['socket'], 1) === false) {
            exit("socket_listen() failed: reason: ".socket_strerror(socket_last_error($arrSocket['socket']))."\n");
        }
        
        $objGlobalDb = self::getGlobalDb();
        
        $objSql = $objGlobalDb->prepare('SELECT "idSite", host, mysql_host, mysql_dbname, mysql_user, mysql_pass FROM site_config LIMIT '.$numLimitBlogs);
        $objSql->execute();
        $arrSites = $objSql->fetchAll();
        $ii = 1;
        $numGettedGlobal = 0;
        
        
        foreach ($arrSites as $arrSite) {
            $strBlogHost = $arrSite['host'];
            $strMysqlHost = $arrSite['mysql_host'];
            $strMysqlDbname = $arrSite['mysql_dbname'];
            $strMysqlUser = $arrSite['mysql_user'];
            $strMysqlPass = $arrSite['mysql_pass'];
            
            $strDsn = 'mysql:dbname=' . $strMysqlDbname . ';host=' . $strMysqlHost;
            $objSiteDb = new PDO($strDsn, $strMysqlUser, $strMysqlPass, array(
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', 
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ));
            
            $numGetted = 0;
            $numOmmited = 0;
            
            $strQ = sprintf(
                "SELECT p.ID, p.post_title, p.post_name, pm.meta_value AS image_url, pm2.meta_value AS auction_id "
                    . "FROM wp_posts AS p "
                    . "JOIN wp_postmeta AS pm ON (pm.post_id = p.ID AND pm.meta_key = 'image-big-url') "
                    . "JOIN wp_postmeta AS pm2 ON (pm2.post_id = p.ID AND pm2.meta_key = 'idAuction') "
                    . "LEFT JOIN wp_postmeta AS pm3 ON (pm3.post_id = p.ID AND pm3.meta_key = 'imged_pl_id') "
                    . "WHERE p.post_status = 'publish' "
                        . "AND p.post_type = 'post' "
                        . "AND pm3.meta_value IS NULL "
                    .   " AND pm.meta_value LIKE '%s' "
                    .   " AND p.post_name NOT REGEXP '\-[[:digit:]]{1,}$'"
                    .   " AND LOWER(p.post_name) NOT LIKE '%s' "
                    .   " AND LOWER(p.post_name) NOT LIKE '%s' "
                    .   " AND LOWER(p.post_name) NOT LIKE '%s' "
                    . "LIMIT %d", 
                'https://s3.eu-central-1.amazonaws.com/%', 
                '%otomoto%', 
                '%otodom%', 
                '%muuda%', 
                $numLimitPosts 
            );
            $objSql = $objSiteDb->prepare($strQ);
            $objSql->execute();
            $arrPosts = $objSql->fetchAll();
            if (!empty($arrPosts)) {
                $objArtifactModel = new ArtifactModel();
                $objTagModel = new TagModel();
                
                foreach ($arrPosts as $arrPost) {
                    
                    if (intval($arrPost['auction_id']) === 0 || empty($arrPost['image_url'])) {
                        $numOmmited++;
                        continue;
                    }
                    
                    $strTitle = $arrPost['post_title'];
                    $strSlug = $arrPost['post_name'];
                    $strImageUrl = $arrPost['image_url'];
                    $numSourcePostId = $arrPost['ID'];
                    
                    $arrImageUrlTokens = explode('/', $strImageUrl);
                    $strImageFilename = array_pop($arrImageUrlTokens);
                    $strImagePath = join('/', $arrImageUrlTokens);
                    if (empty($strImageFilename) || empty($strImagePath)) {
                        continue;
                    }
                    
                    echo 'import '.$strSlug.' ('.$strImageUrl.') ... ';
                    $arrArtifact = $objArtifactModel->getArtifactBySlugOnlyForCron($strSlug);
                    if (!empty($arrArtifact)) {
                        echo 'NOK ... DUPLICATE WITH '.$arrArtifact['id'].PHP_EOL;
                        $strQ = "INSERT INTO wp_postmeta (post_id, meta_key, meta_value) VALUES (:post_id, 'imged_pl_id', :imged_pl)";
                        $objMetaSth = $objSiteDb->prepare($strQ);
                        $objMetaSth->execute(array(
                            ':post_id' => $numSourcePostId, 
                            ':imged_pl' => $arrArtifact['id']
                        ));
                        $numOmmited++;
                    } else {
                        $arrImportedItem = $objArtifactModel->importItemUploadedToS3($strTitle, $strSlug, $strImageUrl);  
                        
                        if (intval($arrImportedItem['id']) > 1) {
                            echo 'id: '.$arrImportedItem['id'].' OK!'.PHP_EOL;
    //                        echo 'imported imged id: '.$arrImportedItem['id'].', slug: '.$arrImportedItem['slug'].PHP_EOL;
                            $objArtifactModel->importElement($arrImportedItem['id'], $strTitle, $strImageUrl);
                            $numGetted++;
                            $arrTags = $objTagModel->extractTagsFromString($strTitle);
                            if (!empty($arrTags)) {
                                $objArtifactModel->addTags($arrImportedItem['id'], $arrTags);
                            }

                            $strQ = "INSERT INTO wp_postmeta (post_id, meta_key, meta_value) VALUES (:post_id, 'imged_pl_id', :imged_pl)";
                            $objMetaSth = $objSiteDb->prepare($strQ);
                            $objMetaSth->execute(array(
                                ':post_id' => $numSourcePostId, 
                                ':imged_pl' => $arrImportedItem['id']
                            ));
                        } else {
                            echo 'NOK ... fuck up !'.PHP_EOL;
                            print_r($arrSite);
                            print_r($arrPost);
                            echo PHP_EOL;
                        }
                    }

                }
            }

        }
        echo $numGetted.' posts imported and '.$numOmmited.' ommited'.PHP_EOL;
        $numGettedGlobal += $numGetted;
        unset($objSiteDb);
        $ii++;
        
        unset ($objGlobalDb);
        $numTime = $this->microtime_float() - $numTimeStart;
        echo "end at ".date('Y-m-d H:i:s').". imported ".$numGettedGlobal." offers in ".$numTime." s".PHP_EOL;
        socket_close($arrSocket['socket']);
//        $objRunOnce->end();
    }
    private function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
    /**
     * @deprecated 
     */
    public static function _import ($strConnectionName) {
        
        if (@fsockopen(self::$socket_address, self::$socket_port)) {
            exit( 'Another  is running, dying' );
        }

        if (( self::$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
            exit("socket_create() failed: reason: ".socket_strerror(socket_last_error())."\n");
        }

        if (socket_bind(self::$socket, self::$socket_address, self::$socket_port) === false) {
            exit("socket_bind() failed: reason: ".socket_strerror(socket_last_error(self::$socket))."\n");
        }

        if (socket_listen(self::$socket, 1) === false) {
            exit("socket_listen() failed: reason: ".socket_strerror(socket_last_error(self::$socket))."\n");
        }
        
        DbFactory::setDefaultConnection($strConnectionName);
        $objImgjetDb = DbFactory::getInstance();
        
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
        $objSql = $objGlobalDb->prepare('SELECT "idSite", host, mysql_host, mysql_dbname, mysql_user, mysql_pass FROM site_config');
        $objSql->execute();
        $arrSites = $objSql->fetchAll();
        $ii = 1;

        $getted = $ommited = 0;
        foreach ($arrSites as $arrSite) {
            $getted = 0;
            $numBlogId = intval($arrSite['idSite']);
            $strBlogHost = $arrSite['host'];
            $strMysqlHost = $arrSite['mysql_host'];
            $strMysqlDbname = $arrSite['mysql_dbname'];
            $strMysqlUser = $arrSite['mysql_user'];
            $strMysqlPass = $arrSite['mysql_pass'];

            echo "site ".$strBlogHost." (".$ii."/".count($arrSites).") ... ";

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
                self::$arrConfig['numOffersGetFromOneSite']);
//            echo $strQ."\n\n";
            $objSql = $objSiteDb->prepare($strQ);
            $objSql->execute();
            $arrPosts = $objSql->fetchAll();
            foreach ($arrPosts as $arrPost) {
//                echo '<Pre>';
//                print_r($arrPost);
//                echo '</pre>';
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

                if (empty($strImageFilename)) {
                    continue;
                }

                $strQ = "INSERT INTO offer ( "
                            . "title_pl, slug_pl, image_source, source_blog_id, source_post_id, allegro_auction_id, shows_count_increaser, need_autotagize, adults_only_suspicion "
                        . ") "
                        . "SELECT :title_pl, :slug_pl, :image_source, :source_blog_id, :source_post_id, :allegro_auction_id, :shows_count_increaser, :need_autotagize, :adults_only_suspicion "
                        . "WHERE (NOT EXISTS("
                            . "SELECT 1 FROM offer "
                            . "WHERE allegro_auction_id = :allegro_auction_id "
                                . "OR title_pl = ".$objImgjetDb->quote($strTitle)." "
                                . "OR slug_pl = ".$objImgjetDb->quote($strSlug)." "
                        . "))";
                
                $boolAdultsOnlySuspicion = \backend\String::isContainsReservedWords($strTitle.' '.$strSlug);
                
                $objSql = $objImgjetDb->prepare($strQ);
                $arrParams = array(
                    ':title_pl' => $strTitle, 
                    ':slug_pl' => $strSlug, 
                    ':image_source' => $strImageSource, 
                    ':allegro_auction_id' => $numAllegroAuctionId, 
                    ':source_blog_id' => $numSourceBlogId, 
                    ':source_post_id' => $numSourcePostId, 
                    ':shows_count_increaser' => rand(1,200), 
                    ':need_autotagize' => 1, 
                    ':adults_only_suspicion' => ($boolAdultsOnlySuspicion === true) ? 1 : 0
                );

                $objSql->execute($arrParams);
            }
            unset($objSiteDb);
            $ii++;
            
            echo "added ".$getted." offers\n";
        }

        
        
        unset ($objGlobalDb);
        socket_close(self::$socket);
        
    }
    
}
