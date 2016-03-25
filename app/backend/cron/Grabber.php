<?php
namespace backend\cron;

use \backend\DbFactory;
//use backend\artifact\model\ArtifactModel;
//use backend\tag\model\TagModel;
//use \SoapClient;
use \backend\artifact\model\ArtifactModel;
use \PDO;
use \backend\tag\model\TagModel;

class Grabber
{
    
    private $objDb = null;
    
    private $numItemsToGet = 100;
    
    private $arrAccess = array(
        'apikey' => '268aba47df', 
        'login' => 'webcitron-lodz', 
        'passhash' => 'iRDBFd8tWdLpSfBA1HSt42ZX7yvcFKwJktHEsLyCCCQ=', 
    );
    
    private $arrProxys = array(
//        array('url' => 'http://poszewki.com.pl/allegro-grabber-proxy/proxy.php', 'ip' => '91.200.186.252'), 
        array('url' => 'http://miniowki.pl/allegro-grabber-proxy/proxy.php', 'ip' => '91.200.187.202')
    );
    
    public function __construct () {
        $this->objDb = DbFactory::getInstance();
        $this->objDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
    }
    
    private function errHandle($errNo, $errStr, $errFile, $errLine) {
        
        $msg = "$errStr in $errFile on line $errLine";
        if ($errNo == E_NOTICE || $errNo == E_WARNING) {
            throw new \ErrorException($msg, $errNo);
        } else {
            echo $msg;
        }
    }
 
    public function run () {
        set_error_handler(array(&$this, 'errHandle'));
        $arrSocket = array(
            'address' => '127.0.0.1', 
            'port' => 42756, 
            'socket' => null
        );
        try {
            if (fsockopen($arrSocket['address'], $arrSocket['port'], $errno, $errstr)) {
                exit( 'Another  is running, dying' );
            }
        } catch (\ErrorException $e) {}
        

        if (( $arrSocket['socket'] = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
            exit("socket_create() failed: reason: ".socket_strerror(socket_last_error())."\n");
        }

        if (socket_bind($arrSocket['socket'], $arrSocket['address'], $arrSocket['port']) === false) {
            exit("socket_bind() failed: reason: ".socket_strerror(socket_last_error($arrSocket['socket']))."\n");
        }

        if (socket_listen($arrSocket['socket'], 1) === false) {
            exit("socket_listen() failed: reason: ".socket_strerror(socket_last_error($arrSocket['socket']))."\n");
        }
        
        $numTimeStart = $this->microtime_float();
        $arrUsedProxy = $this->arrProxys[0];
        $numGettedGlobal = 0;
        
        $objArtifactModel = new ArtifactModel();
        $objTagModel = new TagModel();
        
        /**
Antyki i Sztuka - 26013
Bilety - 98553
Biuro i Reklama - 64477
Biżuteria i Zegarki - 19732
Delikatesy - 73973
Dla Dzieci - 11763
Dom i Ogród - 5
Erotyka - 63757
Filmy - 20585
Fotografia - 8845
Gry - 9
Instrumenty - 122640
Kolekcje - 6
Komputery - 2
Konsole i automaty - 122233
Książki i Komiksy - 7
Motoryzacja - 3
Muzyka - 1
Nieruchomości - 20782
Odzież, Obuwie, Dodatki - 1454
Przemysł - 16696
Rękodzieło - 76593
RTV i AGD - 10
Sport i Turystyka - 3919
Sprzęt estradowy, studyjny i DJ-ski - 122332
Telefony i Akcesoria - 4
Uroda - 1429
Wakacje - 55067
Zdrowie - 121882
         */
        $arrCategories = array(26013, 98553, 64477, 19732, 73973, 11763, 5, 20585, 8845, 9, 122640, 6, 2, 122233, 7, 3, 1, 20782, 1454, 16696, 76593, 10, 3919, 122332, 4, 1429, 55067, 121882);
        $numCategoriesCount = count($arrCategories);
        $numIndex = 0;
        
        do {
            $arrBlockedAllegroSellersIds = $this->getBlockedAllegroSellers();
            
            $numCategoryId = $arrCategories[$numIndex];
            echo "GO WITH CATEGORY ".$numCategoryId.PHP_EOL;
            $numMaxItemId = $this->getMaxItemIdForCategory($numCategoryId);
            
            echo "max item id for that category is ".$numMaxItemId.PHP_EOL;
            if (empty($numMaxItemId)) {
                $numMaxItemId = 0;
                $this->insertMaxItemIdForCategory($numCategoryId, $numMaxItemId);
            }
            
            $strResultJson = $this->getItems($arrUsedProxy, $numCategoryId);
            $objResult = json_decode($strResultJson);
            if (!is_object($objResult)) {
                die($strResultJson);
            }
            if (!empty($objResult->itemsList->item) && is_array($objResult->itemsList->item) && $objResult->itemsList->item[0]->itemId != $numMaxItemId) {
                $this->updateMaxItemIdForCategory($numCategoryId, $objResult->itemsList->item[0]->itemId);
                
                foreach ($objResult->itemsList->item as $objItem) {
                    echo "do item ".$objItem->itemId.PHP_EOL;
                    if ($objItem->itemId == $numMaxItemId) {
                        break;
                    }
                    if (empty($objItem->photosInfo->item)) {
                        continue;
                    }
                    
                    if (!empty($objItem->sellerInfo) && !empty($objItem->sellerInfo->userId) && in_array(intval($objItem->sellerInfo->userId), $arrBlockedAllegroSellersIds)) {
                        continue;
                    }

                    try {
                        $arrImagesUrls = [];
                        foreach ($objItem->photosInfo->item as $objImage) {
                            if ($objImage->photoIsMain == 1 && $objImage->photoSize === 'large') {
                                $arrImagesUrls[] = $objImage->photoUrl;
                                break;
                            }
                        }
                        
                        if (!empty($arrImagesUrls)) {
                            
                            if (strpos($arrImagesUrls[0], '400x300')) {
                                array_unshift($arrImagesUrls, str_replace('400x300', 'oryginal', $arrImagesUrls[0]));
                            } else if (strpos($arrImagesUrls[0], '128x96')) {
                                array_unshift($arrImagesUrls, str_replace('128x96', 'oryginal', $arrImagesUrls[0]));
                            } else if (strpos($arrImagesUrls[0], '64x48')) {
                                array_unshift($arrImagesUrls, str_replace('64x48', 'oryginal', $arrImagesUrls[0]));
                            }
                        
                            $boolItemExists = $this->isItemAlreadyImported($objItem->itemId);
                            if ($boolItemExists === false) {
                                
                                foreach ($arrImagesUrls as $strImageUrl) {
                                    $numGetPhotoTry = 1;
                                    do {
                                        try {
                                            $strImageBlob = @file_get_contents($strImageUrl);
                                        } catch (\ErrorException $e) {
                                            echo $e->getMessage().PHP_EOL;
                                            sleep(3);
                                            $numGetPhotoTry++;
                                            echo "another try (".($numGetPhotoTry).'/5)'.PHP_EOL;
                                        }
                                    } while (empty($strImageBlob) && $numGetPhotoTry <= 5);
                                    
                                    if (!empty($strImageBlob)) {
                                        break;
                                    }
                                }
                                
                                if (empty($strImageBlob)) {
                                    print_r($arrImagesUrls);
                                    echo "empty blob - ommit ";
                                    continue;
                                }
                                
                                $strItemTitle = mb_strtoupper(mb_substr($objItem->itemTitle, 0, 1, 'UTF-8'), 'UTF-8') . mb_strtolower(mb_substr($objItem->itemTitle, 1, null, 'UTF-8'), 'UTF-8');
                                $strImageBase64 = 'data:image/jpeg;base64,' . base64_encode($strImageBlob);

                                $arrElements = array();
                                $arrElements[] = array(
                                    'numType' => ArtifactModel::ITEM_TYPE_IMAGE, 
                                    'numOrdering' => 1, 
                                    'strTitle' => $strItemTitle, 
                                    'arrImage' => array(
                                        'base64' => $strImageBase64
                                    )
                                );
                                
                                $numShowsCountIncreaser = rand(1,200);
                                $arrItem = $objArtifactModel->addItem($arrElements[0], null, false, true, $strItemTitle, '', $numShowsCountIncreaser);
                                
                                if (!empty($arrItem)) {
                                    $objArtifactModel->addElements($arrItem['id'], $arrElements, $arrItem['slug']);
                                    $objArtifactModel->addItemAllegroInfo($arrItem['id'], $objItem->itemId, $objItem->sellerInfo->userId);
                                    $strStringToTagize = $objArtifactModel->getStringToAutoTagize($strItemTitle, $arrElements);
                                    $arrTags = $objTagModel->extractTagsFromString($strStringToTagize);
                                    if (!empty($arrTags)) {
                                        $objArtifactModel->addTags($arrItem['id'], $arrTags);
                                    }
                                    $numGettedGlobal++;
                                }
                                unset($strImageBlob, $strImageBase64);
                            }
                        }

                    } catch (PDOException $e) {
                        echo "can't add this offer, EXCEPTION".PHP_EOL;
                    }
                }
                
            }
            
            $numIndex++;
            if ($numIndex === $numCategoriesCount-1) {
                sleep(60);
                $numIndex = 0;
            }
            sleep(1);
        } while (true);
        
        unset ($this->objDb);
        $numTime = $this->microtime_float() - $numTimeStart;
        echo "end at ".date('Y-m-d H:i:s').". imported ".$numGettedGlobal." offers in ".$numTime." s".PHP_EOL;
        socket_close($arrSocket['socket']);
    }
    
    private function microtime_float () {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
    
    private function getMaxItemIdForCategory ($numCategoryId) {
        $strQ = "SELECT allegro_item_id FROM grabber.category_max_item_id WHERE allegro_category_id = :category_id";
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':category_id' => $numCategoryId
        ));
        $numMaxItemId = $objSth->fetch(PDO::FETCH_COLUMN, 0);
        return $numMaxItemId;
    }
    
    private function insertMaxItemIdForCategory ($numCategoryId, $numMaxItemId) {
        $strQ = "INSERT INTO grabber.category_max_item_id (allegro_category_id, allegro_item_id) VALUES (:category_id, :max_item_id)";
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':category_id' => $numCategoryId, 
            ':max_item_id' => $numMaxItemId
        ));
    }
    
    private function updateMaxItemIdForCategory ($numCategoryId, $numMaxItemId) {
        $strQ = "UPDATE grabber.category_max_item_id SET allegro_item_id = :item_id WHERE allegro_category_id = :category_id";
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':category_id' => $numCategoryId, 
            ':item_id' => $numMaxItemId
        ));
    }
                
    private function getItems ($arrUsedProxy, $numCategoryId) {
        $objCurl = curl_init();
        curl_setopt($objCurl, CURLOPT_URL, $arrUsedProxy['url']);
        curl_setopt($objCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($objCurl, CURLOPT_POST, 7);
        curl_setopt($objCurl, CURLOPT_POSTFIELDS, sprintf('proxyip=%s&category=%d&apikey=%s&login=%s&passhash=%s&itemscount=%d', 
            $arrUsedProxy['ip'], 
            $numCategoryId, 
            $this->arrAccess['apikey'], 
            $this->arrAccess['login'], 
            $this->arrAccess['passhash'], 
            $this->numItemsToGet
        ));
        $strResultJson = curl_exec($objCurl);
        curl_close($objCurl);
        unset($objCurl);
        
        return $strResultJson;
    }
    
    private function isItemAlreadyImported ($numAllegroId) {
        $strQ = "SELECT count(1) AS ex FROM artifacts.item_allegro_info WHERE allegro_item_id = :allegro_item_id LIMIT 1";
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute(array(
            ':allegro_item_id' => $numAllegroId
        ));
        $boolIsExists = (intval($objSth->fetch(PDO::FETCH_COLUMN, 0)) > 0);
        return $boolIsExists;
    }
    
    
    private function getBlockedAllegroSellers () {
        $strQ = "SELECT allegro_user_id FROM grabber.blocked_allegro_user";
        $objSth = $this->objDb->prepare($strQ);
        $objSth->execute();
        $arrIds = $objSth->fetchAll(PDO::FETCH_COLUMN, 0);
        
        return $arrIds;
    }
}