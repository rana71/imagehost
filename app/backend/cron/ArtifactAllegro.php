<?php
namespace backend\cron;

use \backend\DbFactory;
//use backend\artifact\model\ArtifactModel;
//use backend\tag\model\TagModel;

class ArtifactAllegro
{
    
    private $objDb = null;
 
    public function fillAllegroData () {
        $numTimeStart = $this->microtime_float();
        $arrSocket = array(
            'address' => '127.0.0.1', 
            'port' => 42647, 
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
        
        $numInsertedRows = 0;
        
        $objSql = $objGlobalDb->prepare('SELECT "idSite", host, mysql_host, mysql_dbname, mysql_user, mysql_pass FROM site_config');
        $objSql->execute();
        $arrSites = $objSql->fetchAll();
        
        $this->objDb = DbFactory::getInstance();
        $this->objDb->exec("SET SCHEMA 'artifacts'");
        $this->objDb->exec('TRUNCATE TABLE artifacts.item_allegro_info');
        
        $numBlogNo = 1;
        $numBlogsCount = count($arrSites);
        
        foreach ($arrSites as $arrSite) {
            echo "do site ".$numBlogNo.' / '.$numBlogsCount.PHP_EOL;
            $strMysqlHost = $arrSite['mysql_host'];
            $strMysqlDbname = $arrSite['mysql_dbname'];
            $strMysqlUser = $arrSite['mysql_user'];
            $strMysqlPass = $arrSite['mysql_pass'];
            
            $strDsn = 'mysql:dbname=' . $strMysqlDbname . ';host=' . $strMysqlHost;
            $objSiteDb = new \PDO($strDsn, $strMysqlUser, $strMysqlPass, array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8', 
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ));
        
                    
            $strQ = <<<EOF
SELECT CONCAT(
        '(', 
        pm.meta_value, 
        ', \'', 
        COALESCE(pm2.meta_value, 0), 
        '\', ', 
        COALESCE(pm3.meta_value, 0), 
        ', 0', 
        ')'
    ) 
FROM wp_postmeta AS pm 
LEFT JOIN wp_postmeta AS pm2 ON (pm2.post_id = pm.post_id AND pm2.meta_key = 'idAuction') 
LEFT JOIN wp_postmeta AS pm3 ON (pm3.post_id = pm.post_id AND pm3.meta_key = 'seller_id') 
WHERE pm.meta_key = 'imged_pl_id' 
EOF;
            $objSql = $objSiteDb->prepare($strQ);
            $objSql->execute();
            $arrItems = $objSql->fetchAll(\PDO::FETCH_COLUMN, 0);
//            $arrItems = $objSql->fetchAll();
//            echo '<Pre>';
//            print_r($arrItems);
//            echo '</pre>';
//            break;
            if (count($arrItems) > 0) {
                $this->objDb->exec("INSERT INTO item_allegro_info (item_id, allegro_item_id_tmp, allegro_user_id, allegro_item_id) VALUES ".join(', ', $arrItems));
                $numInsertedRows += count($arrItems);
            }
            
            unset($objSiteDb);
            $numBlogNo++;
        }
        
        
        unset ($objGlobalDb);
        $numTime = $this->microtime_float() - $numTimeStart;
        echo "end at ".date('Y-m-d H:i:s').". inserted ".$numInsertedRows." rows in ".$numTime." s".PHP_EOL;
        socket_close($arrSocket['socket']);
    }
    
    private static function getGlobalDb () {
        $arrGlobalDbConfig = array(
            'host' => 'gooroo-pgsql.inten.pl', 
            'user' => 'blogi_default', 
            'password' => 'KuRwAmAc123#', 
            'dbname' => 'blogi_global_mango1'
        );


        $objGlobalDb = new \PDO(
            sprintf("pgsql:host=%s;port=5432;dbname=%s", $arrGlobalDbConfig['host'], $arrGlobalDbConfig['dbname']), 
            $arrGlobalDbConfig['user'], 
            $arrGlobalDbConfig['password'], 
            array(
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
            )
        );
        
        return $objGlobalDb;
    }
    
    private function microtime_float() {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
}