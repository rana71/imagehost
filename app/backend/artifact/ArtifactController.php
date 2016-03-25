<?php namespace backend\artifact;

use backend\artifact\model\ArtifactModel;
use webcitron\Subframe\Controller;
use backend\DbFactory;
use webcitron\Subframe\Application;
use webcitron\Subframe\Url;
use backend\tag\model\TagModel;
use backend\user\model\UserModel;
use backend\artifact\model\ArtifactListModel;
use backend\StorageModel;
use backend\utils\BBCodeParser;
use webcitron\Subframe\Templater;
use webcitron\Subframe\Config;

class ArtifactController extends Controller { 
    
    public static function getStats () {
        $objModel = new model\ArtifactModel();
        $arrStats = $objModel->getStats();
        return parent::answer($arrStats);
    }
    
    public static function getCommercialGalleryCode ($numArtifactId) {
        $arrResult = array();
        
        $objSession = \backend\Session::getInstance('imagehost_user');
        $numLoggedAccountId = $objSession->getValue('imagehost_account_id');
        
        if (!empty($numLoggedAccountId)) {
            $objModel = new model\ArtifactModel();
            $arrArtifact = $objModel->getBaseInfo($numArtifactId);
            if ($arrArtifact['author_account_id'] === $numLoggedAccountId) {
                $arrElements = $objModel->getElements($numArtifactId);                
                $objTemplater = Templater::createSpecifiedTemplater(Config::get('templater'));
                $strTempalatePath = APP_DIR.'/imagehost3/box/artifact/view/CommercialGalleryUsageCode.blitz.tpl';
                foreach ($arrElements as & $arrElement) {
                    $arrElement['strGalleryUrl'] = Url::route('Details::commerceGallery', array($arrArtifact['slug'], $arrArtifact['id']));
                }
                $strHtml = $objTemplater->parse($strTempalatePath, array(
                    'arrElements' => $arrElements
                ));
                $arrResult['strCode'] = trim(preg_replace("/\r\n|\r|\n/", '', $strHtml));
                
            }
        }
        return parent::answer($arrResult);
    }
    
    public static function banSellerAndRemoveOffers ($numSellerId, $boolBanSeller = false, $boolRemoveOffers = false) {
        $arrResult = $arrErrors = [];
        
        if ($boolBanSeller === false && $boolRemoveOffers === false) {
            $arrErrors[] = 'Nie wybrano żadnej akcji';
        } else {
            $objModel = new model\ArtifactModel();
            if ($boolBanSeller === true) {
                $objModel->banSeller($numSellerId);
                $arrResult['boolBanned'] = true;
            }
            if ($boolRemoveOffers === true) {
                $objModel->markBlockedSellersOffersAsRemoved();
                $arrResult['boolOffersRemoved'] = true;
            }
        }
        return parent::answer($arrResult, $arrErrors);
    }
    
    public static function findSellerAndOffersCount ($numArtifactId, $numSellerId) {
        $arrResult = array();
        $arrErrors = array();
        
        $objArtifactModel = new ArtifactModel();
        if (empty($numSellerId)) {
            $numSellerId = $objArtifactModel->getAllegroSellerId($numArtifactId);
        }
        
        if (empty($numSellerId)) {
            $arrErrors[] = 'Ten artefakt nie ma przypisanego sprzedawcy Allegro';
        } else {
            $boolIsBanned = $objArtifactModel->isSellerBanned($numSellerId);
            if ($boolIsBanned === true) {
                $arrErrors[] = 'Użytkownik allegro '.$numSellerId.' jest już zbanowany';
            } else {
                $arrResult['numSellerId'] = $numSellerId;
                $arrResult['numOffersCount'] = $objArtifactModel->countAllegroSellerOffers($numSellerId);
                $arrResult['arrOffers'] = $objArtifactModel->getAllegroSellerOffers($numSellerId, 3);
            }
            
        }
        
        return parent::answer($arrResult, $arrErrors);
    }
    
    public static function getUploaderIp ($numArtifactId) {
        $arrResult = array();
        $objModel = new model\ArtifactModel();
        $strIp = $objModel->getArtifaceUploaderIp($numArtifactId);
        $arrResult['numItemId'] = $numArtifactId;
        $arrResult['strIp'] = $strIp;
        return parent::answer($arrResult);
    }
    
    public static function removeFromHomepage ($numArtifactId) {
        $arrResult = array();
        $objModel = new model\ArtifactModel();
        $objModel->removeFromHomepage($numArtifactId);
        $arrResult['numRemovedArtifactId'] = $numArtifactId;
        return parent::answer($arrResult);
    }
    
    public static function getImageElementBase64 ($strItemElementIdBase64) {
        $arrResult = array();
        if (!empty($strItemElementIdBase64)) {
            $numItemElementId = base64_decode($strItemElementIdBase64);
            if ($numItemElementId == intval($numItemElementId)) {
                $objModel = new model\ArtifactModel();
                $arrResult['arrImage'] = $objModel->getImageElementBase64($numItemElementId);
                if (!empty($arrResult['arrImage']['image_url'])) {
                    $strBlob = file_get_contents($arrResult['arrImage']['image_url']);
                    if (empty($arrResult['arrImage']['mimetype'])) {
                        $arrResult['arrImage']['mimetype'] = 'image/'.pathinfo($arrResult['arrImage']['image_url'], PATHINFO_EXTENSION);
                    }
                    $arrResult['arrImage']['strBase64'] = sprintf('data:%s;base64,%s', $arrResult['arrImage']['mimetype'], base64_encode($strBlob));
                }
            }
        }
        return parent::answer($arrResult);
    }
    
    public static function addToHomepage ($numArtifactId) {
        $arrErrors = array();
        $arrResult = array();
        $objModel = new model\ArtifactModel();
        $arrArtifact = $objModel->getBaseInfo($numArtifactId);
        if (empty($arrArtifact)) {
            $arrErrors[] = 'Nie odnaleziono artefaktu o takim ID';
        } else if ($arrArtifact['is_on_homepage'] === true) {
            $arrErrors[] = 'Ten artefakt jest już na stronie głównej';
        } else {
            $objModel->addToHomepage($numArtifactId);
            $arrResult['arrArtifact'] = $arrArtifact;
        }
        return parent::answer($arrResult, $arrErrors);
    }
    
    public static function reportAbuse ($numArtifactId, $strReporterName, $strReporterEmail, $strUrl, $strReason) {
        $arrResult = array();
        $objMail = new \backend\SystemMail('ReportArtifactAbuse');
        $objMail->addRecipient('bberlinski@gmail.com', 'Bartek');
//        $objMail->addRecipient('a.mackiewicz@webcitron.eu', 'Adam');
        $objMail->setVariable('id', $numArtifactId);
        $objMail->setVariable('url', $strUrl);
        $objMail->setVariable('reporter_name', $strReporterName);
        $objMail->setVariable('reporter_email', $strReporterEmail);
        $objMail->setVariable('reason', nl2br(strip_tags($strReason)));
        $objMail->send();
        
        $arrResult['strReporterName'] = $strReporterName;
        $arrResult['strReporterEmail'] = $strReporterEmail;
        
        return self::answer($arrResult);
    }
    
    public static function markAsRemoved ($arrIdsToUnmark = array()) {
        $arrResult = array();
        if (!empty($arrIdsToUnmark)) {
            $objModel = new model\ArtifactModel();
            $objModel->markAsRemoved($arrIdsToUnmark);
            
            $arrResult['numunMarkedCount'] = count($arrIdsToUnmark);
            $arrResult['arrDeletedIds'] = $arrIdsToUnmark;
        }
        
        return self::answer($arrResult);
    }
    
    public static function unmarkAsRemoved ($arrIdsToUnmark = array()) {
        $arrResult = array();
        if (!empty($arrIdsToUnmark)) {
            $objModel = new model\ArtifactModel();
            $objModel->unmarkAsRemoved($arrIdsToUnmark);
            
            $arrResult['numUnMarkedCount'] = count($arrIdsToUnmark);
            $arrResult['arrUnmarkedIds'] = $arrIdsToUnmark;
        }
        
        return self::answer($arrResult);
    }
    
    /**
     * @deprecated
     * Unused ?
     
    private static function fillListWithDetails ($arrList) {
        if (!empty($arrList)) {
            
            foreach ($arrList as & $arrRes) {
                if (empty($arrRes['mimetype'])) {
                    $arrRes['extension'] = 'jpg';
                } else {
                    $arrRes['extension'] = \backend\File::mimetypeToExtension($arrRes['mimetype']);
                }
                $numPhotoDirectory = $arrRes['id'] % 5000;
                $arrRes['href'] = \webcitron\Subframe\Url::route('Details', array(
                    'slug' => $arrRes['slug'], 
                    'id' => $arrRes['id']
                ));
                if (!empty($arrRes['thumb_path']) && !empty($arrRes['thumb_filename'])) {
                    $arrRes['thumb_url'] = '';
                    if (substr($arrRes['thumb_path'], 0, 1) === '/') {
                        $arrRes['thumb_url'] .= Application::url();
                    }
                    $arrRes['thumb_url'] .= $arrRes['thumb_path'].'/'.$arrRes['thumb_filename'];
                    $arrRes['thumb_url'] = str_replace('https://s3.eu-central-1.amazonaws.com/i.imged.pl', 'http://i.imged.pl', $arrRes['thumb_url']);
                } else {
                    $strImageId = $arrRes['id'];
                    if ($arrRes['type'] === 'story') {
                        $strImageId = 1;
                    }
                    $arrRes['thumb_url'] = \webcitron\Subframe\Url::route('ImageDirect', array(
                        'directory' => $numPhotoDirectory, 
                        'slug' => $arrRes['slug'], 
                        'id' => $strImageId, 
                        'ext' => $arrRes['extension']
                    ));
                }
            }
        }
        return self::answer($arrList);
    }
    */
    
    
    public static function setAdultsOnly ($numArtifactId, $numState) {
        $objModel = new model\ArtifactModel();
        $objModel->setAdultsOnly($numArtifactId, $numState);
        return parent::answer(true);
    }
    
    public static function setAsOffer ($numArtifactId, $numState) {
        $objModel = new model\ArtifactModel();
        $objModel->setAsOffer($numArtifactId, $numState);
        return parent::answer(true);
    }
    
    
    public static function setOnHomepage ($numArtifactId, $numState) {
        $objModel = new model\ArtifactModel();
        $objModel->setOnHomepage($numArtifactId, $numState);
        return parent::answer(true);
    }
    
    
    public static function upload ($arrElements, $strTitle, $strDescription = '', $strClientIp = '') {
        
        /**
         * $arrElements[] = array(
         *  numOrdering => 1, 
         *  strTitle => 'tytul', 
         *  strDescription => 'opis', 
         *  numType => 1, // 1 - image, 2 - ytvideo, 3 - mem
         * 
         *  strImageBlob => '', 
         *  strMovieUrl => ''
         * )
         */
//        echo '<Pre>';
//        print_r($arrElements);
//        print_r($strTitle);
//        print_r($strDescription);
//        exit();
        $arrResult = array();
        $objArtifactModel = new model\ArtifactModel();
        $arrErrors = $objArtifactModel->prevalidArtifact($strTitle, $arrElements);
        if (empty($arrErrors)) {
            $objTagModel = new TagModel();
            $objUserModel = new UserModel();
            $arrAuthorAccount = $objUserModel->getLoggedUser();
            $mulAccountId = null;

            if (!empty($arrAuthorAccount)) {
                $mulAccountId = $arrAuthorAccount['id'];
            } else if (!empty($strNewAccountEmail)) {
                // create new account
            }
            
            foreach ($arrElements as & $arrElement) {
                if ($arrElement['numType'] === model\ArtifactModel::ITEM_TYPE_MEM) {
                    $arrElement = $objArtifactModel->buildMeme($arrElement);
                }
            }
            
//            
            $arrItem = $objArtifactModel->addItem($arrElements[0], $mulAccountId, false, false, $strTitle, $strDescription, 0, $strClientIp);
            $objArtifactModel->addElements($arrItem['id'], $arrElements, $arrItem['slug']);
            $strStringToTagize = $objArtifactModel->getStringToAutoTagize($strTitle, $arrElements);
            $arrTags = $objTagModel->extractTagsFromString($strStringToTagize);
            if (!empty($arrTags)) {
                $objArtifactModel->addTags($arrItem['id'], $arrTags);
            }
            
            $strArtifactUrl = Url::route('Details', array($arrItem['slug'], $arrItem['id']));
            $arrResult['strUrl'] = $strArtifactUrl;
            $arrResult['numId'] = $arrItem['id'];
        }
        
        return self::answer($arrResult, $arrErrors);
    }
    
    
    private static function add ($strImageBase64, $strTitle, $strContent, $numAdultsOnly = 1, $arrTags = array(), $numUserId = 0) {
        $arrImageTokens = explode(',', $strImageBase64);
        $strImageBlob = base64_decode(str_replace(' ', '+', $arrImageTokens[1]));
        $objFinfo = finfo_open();
        $strMimeType = finfo_buffer($objFinfo, $strImageBlob, FILEINFO_MIME_TYPE);
        $strExt = \backend\File::mimetypeToExtension($strMimeType);
        if (empty($strExt)) {
            return false;
        }
        $arrExif = array();
        if (exif_imagetype($strImageBase64) === IMAGETYPE_JPEG && function_exists('\exif_read_data')) {
            $arrExifNative = \exif_read_data($strImageBase64);
            if ($arrExifNative === false) {
                $arrExif = array();
            } else {
                $arrExif = $arrExifNative;
            }
        }
        
        $objImage = imagecreatefromstring($strImageBlob);
        $numImageWidth = imagesx($objImage);
        $numImageHeight = imagesy($objImage);
        
        $objDb = DbFactory::getInstance();
        
        $strQ = "INSERT INTO offer ( "
                    . "title_pl, "
                    . "slug_pl, "
                    . "description, "
                    . "image_weight, "
                    . "width, "
                    . "height, "
                    . "add_date, "
                    . "mimetype, "
                    . "orginal_exif, "
                    . "adults_only, "
                    . "user_id "
                . ") VALUES ("
                    . ":title_pl, "
                    . ":slug_pl, "
                    . ":description, "
                    . ":image_weight, "
                    . ":width, "
                    . ":height, "
                    . "NOW(), "
                    . ":mimetype, "
                    . ":orginal_exif, "
                    . ":adults_only, "
                    . ":user_id "
                . ") RETURNING id, (id % 5000) AS photo_directory, title_pl AS title ";
        $arrData = array();
        $arrData[':title_pl'] = $strTitle;
        $arrSlug = self::genUniqueSlug($strTitle);
        $arrData[':slug_pl'] = $arrSlug['result'];
        $arrData[':description'] = BBCodeParser::getHtml($strContent);
        $arrData[':image_weight'] = strlen($strImageBlob);
        $arrData[':width'] = $numImageWidth;
        $arrData[':height'] = $numImageHeight;
        $arrData[':mimetype'] = $strMimeType;
        $arrData[':orginal_exif'] = json_encode($arrExif);
        $arrData[':adults_only'] = $numAdultsOnly;
        $arrData[':user_id'] = intval($numUserId);
        
        $objSth = $objDb->prepare($strQ);
        $objSth->execute($arrData);
        $arrInserted = $objSth->fetch();
        
        $numId = $arrInserted['id'];
        
        if (Application::currentEnvironment() === Application::ENVIRONMENT_PRODUCTION || Application::currentEnvironment() === Application::ENVIRONMENT_RC) {
            $objS3Model = new StorageModel('s3', array(
                'key' => 'AKIAJRIWCGLMNSOD6FOA', 
                'secret' => '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', 
                'region' => 'eu-central-1', 
                'bucket' => 'i.imged.pl',
                'version' => '2006-03-01'
            ), array(
                'scheme' => 'http'
            ));
            $strImageName = sprintf('%s-%d.%s', $arrData[':slug_pl'], $numId, $strExt);
            $arrSaveResult = $objS3Model->writeFile($strImageName, $strImageBlob);
            if (!empty($arrSaveResult['ObjectURL'])) {
                $strQ = <<<EOF
UPDATE offer 
SET thumb_path = :thumb_path, 
    thumb_filename = :thumb_filename 
WHERE id = :id
EOF;
                $arrTokens = explode('/', $arrSaveResult['ObjectURL']);
                $strImageFilename = array_pop($arrTokens);
                $strImagePath = join('/', $arrTokens);
                
                $objSth = $objDb->prepare($strQ);
                $objSth->execute(array(
                    ':thumb_path' => $strImagePath, 
                    ':thumb_filename' => $strImageFilename, 
                    ':id' => $numId
                ));
            }
        }
        
        if (!empty($arrTags)) {
            self::addTags($arrInserted['id'], $arrTags);
        } else {
            self::autoTagize(array(
                $arrInserted['id'] => $arrInserted['title']
            ));
        }
        return self::answer($numId);
    }
    
    /**
     * @deprecated
     
    public static function addTags ($numArtifactId, $arrTags) {
        $arrTagsThatOffer = array();
        $objDb = DbFactory::getInstance();
        
        $strGetTagQ = "SELECT id, slug FROM tag WHERE slug = :slug";
        $objGetTagSql = $objDb->prepare($strGetTagQ);
        
        $strOfferAddedToTagQ = "UPDATE tag SET elements_count = (elements_count+1) WHERE id = :id";
        $objOfferAddedToTagSql = $objDb->prepare($strOfferAddedToTagQ);

        $strInsertTagOfferQ = "INSERT INTO offer_tag (tag_id, offer_id) VALUES (:tag_id, :offer_id)";
        $objInsertTagOfferSql = $objDb->prepare($strInsertTagOfferQ);

        $strInsertTagQ = "INSERT INTO tag (name, slug) VALUES (:name, :slug)";
        $objInsertTagSql = $objDb->prepare($strInsertTagQ);
        
        $strGetRestrictedTags = "SELECT name, slug FROM tag WHERE removed = TRUE";
        $objGetRestrictedTagsSql = $objDb->prepare($strGetRestrictedTags);
        $objGetRestrictedTagsSql->execute();
        $arrRestrictedTags = $objGetRestrictedTagsSql->fetchAll();
        
        foreach ($arrTags as $strTag) {
            $strTag = mb_strtolower(trim($strTag), 'UTF-8');
            $strSlug = \backend\String::slug($strTag);
            if (strlen($strSlug) < 3 || strlen($strTag) < 3) {
                continue;
            } else if (in_array($strSlug, $arrTagsThatOffer)) {
                continue;
            } else if (!empty($arrRestrictedTags)) {
                foreach ($arrRestrictedTags as $arrRestrictedTag) {
                    if (trim($strSlug) == trim($arrRestrictedTag['slug']) || trim($strTag) == trim($arrRestrictedTag['name'])) {
                        continue;
                    }
                }
            }
            
            $objGetTagSql->execute(array(
                ':slug' => $strSlug
            ));
            $arrTag = $objGetTagSql->fetch();

            if (empty($arrTag)) {
                $objInsertTagSql->execute(array(
                    ':name' => $strTag, 
                    ':slug' => $strSlug
                ));
                $arrTag = array(
                    'name' => $strTag, 
                    'slug' => $strSlug, 
                    'id' => $objDb->lastInsertId('tag_id_seq')
                );

            }

            $objInsertTagOfferSql->execute(array(
                ':offer_id' => intval($numArtifactId), 
                ':tag_id' => intval($arrTag['id'])
            ));
            $objOfferAddedToTagSql->execute(array(
                ':id' => intval($arrTag['id'])
            ));
            $arrTagsThatOffer[] = $arrTag['slug'];
            
        }
        return self::answer(true);
    }*/
    
    public static function autoTagize ($arrArtifacts) {
        $objDb = DbFactory::getInstance();
        
        $strGetTagQ = "SELECT id, slug FROM tag WHERE slug = :slug";
        $objGetTagSql = $objDb->prepare($strGetTagQ);
        
        $strOfferAddedToTagQ = "UPDATE tag SET elements_count = (elements_count+1) WHERE id = :id";
        $objOfferAddedToTagSql = $objDb->prepare($strOfferAddedToTagQ);

        $strInsertTagOfferQ = "INSERT INTO offer_tag (tag_id, offer_id) VALUES (:tag_id, :offer_id)";
        $objInsertTagOfferSql = $objDb->prepare($strInsertTagOfferQ);

        $strInsertTagQ = "INSERT INTO tag (name, slug) VALUES (:name, :slug)";
        $objInsertTagSql = $objDb->prepare($strInsertTagQ);
        
        $strGetRestrictedTags = "SELECT name, slug FROM tag WHERE removed = TRUE";
        $objGetRestrictedTagsSql = $objDb->prepare($strGetRestrictedTags);
        $objGetRestrictedTagsSql->execute();
        $arrRestrictedTags = $objGetRestrictedTagsSql->fetchAll();
        
        foreach ($arrArtifacts as $numArtifactId => $strTitle) {
            $arrTagsThatOffer = array();
            $strTitle = mb_strtolower($strTitle, 'UTF-8');
            $arrNameTokens = preg_split('/[^1234567890qwertyuioplkjhgfdsazxcvbnmęółśążźćń]+/', $strTitle);
            foreach ($arrNameTokens as $strNameToken) {
                $strSlug = \backend\String::slug($strNameToken);
                if (strlen($strSlug) < 3 || strlen($strNameToken) < 3) {
                    continue;
                } else if (in_array($strSlug, $arrTagsThatOffer)) {
                    continue;
                } else if (!empty($arrRestrictedTags)) {
                    foreach ($arrRestrictedTags as $arrRestrictedTag) {
                        if (trim($strSlug) == trim($arrRestrictedTag['slug']) || trim($strNameToken) == trim($arrRestrictedTag['name'])) {
                            continue;
                        }
                    }
                }
                    
                $objGetTagSql->execute(array(
                    ':slug' => $strSlug
                ));
                $arrTag = $objGetTagSql->fetch();

                if (empty($arrTag)) {
                    $objInsertTagSql->execute(array(
                        ':name' => $strNameToken, 
                        ':slug' => $strSlug
                    ));
                    $arrTag = array(
                        'name' => $strNameToken, 
                        'slug' => $strSlug, 
                        'id' => $objDb->lastInsertId('tag_id_seq')
                    );

                }

                $objInsertTagOfferSql->execute(array(
                    ':offer_id' => intval($numArtifactId), 
                    ':tag_id' => intval($arrTag['id'])
                ));
                $objOfferAddedToTagSql->execute(array(
                    ':id' => intval($arrTag['id'])
                ));
                $arrTagsThatOffer[] = $arrTag['slug'];
            }
        }
        return self::answer(true);
    }
    
    
    public static function getResultsCount($arrSearchParams) {
        $objDb = DbFactory::getInstance();
        
        $arrQueryTokens = self::getListBuildQueryTokens($arrSearchParams);
        $strQ = sprintf(
            "SELECT count(1) %s ", 
            join(' '.PHP_EOL, $arrQueryTokens['result'])
        );
        $objSth = $objDb->prepare($strQ);
        $objSth->execute();
        $numOffersCount = $objSth->fetch(\PDO::FETCH_COLUMN, 0);
        
        return self::answer($numOffersCount);
    }
    
    private static function getListBuildQueryTokens ($arrSearchParams, $numLimit = 30) {
        $objDb = DbFactory::getInstance();
        
        if (!empty($arrSearchParams['numUserId'])) {
            $numUserId = $arrSearchParams['numUserId'];
        }
        if (!empty($arrSearchParams['numTagId'])) {
            $numTagId = $arrSearchParams['numTagId'];
        }
        if (!empty($arrSearchParams['strSearchQuery'])) {
            $strSearchQuery = $arrSearchParams['strSearchQuery'];
        }
        if (isset($arrSearchParams['numOnHomepage'])) {
            $numOnHomepage = $arrSearchParams['numOnHomepage'];
        }
        if (isset($arrSearchParams['numOnlyFromUsers'])) {
            $numOnlyFromUsers = $arrSearchParams['numOnlyFromUsers'];
        }
        if (isset($arrSearchParams['strType'])) {
            $strType = $arrSearchParams['strType'];
        }
        if (isset($arrSearchParams['strUploadType'])) {
            $strUploadType = $arrSearchParams['strUploadType'];
        }
        if (!empty($arrSearchParams['numOrder'])) {
            $numOrder = $arrSearchParams['numOrder'];
        }
        
        $arrQueryTokens = array();
        $arrQueryTokens[] = "FROM offer AS o"; 
        if (!empty($numTagId)) {
            $arrQueryTokens[] = "JOIN offer_tag AS ot ON ot.offer_id = o.id"; 
        }
        $arrQueryTokens[] = "WHERE o.visible IS TRUE"; 
        
        if (!empty($numOrder)) {
            if ($numOrder === self::LIST_ORDER_RAND) {
//                $strQueryPart = <<<EOF
//AND (o.id < (
//    SELECT trunc(random() * (num_end-num_start) + num_start) FROM (SELECT min(id) AS num_start, max(id) AS num_end FROM offer) AS s1
//) OR o.id > (
//    SELECT trunc(random() * (num_end-num_start) + num_start) FROM (SELECT min(id) AS num_start, max(id) AS num_end FROM offer) AS s1
//))
//EOF;
$strQueryPart = <<<EOF
AND (o.id > (
    SELECT trunc((random() * (num_end-num_start) + num_start) / 2) FROM (SELECT min(id) AS num_start, max(id) AS num_end FROM offer) AS s1
))
EOF;
// LIMIT $numLimit ?
                $arrQueryTokens[] = $strQueryPart;
            }
        }
        
        if (!empty($strType)) {
            $arrQueryTokens[] = sprintf("AND (o.type = %s)", $objDb->quote($strType)); 
        }
        
        if (isset($strUploadType)) {
            switch ($strUploadType) {
                case 'auto':
                    $arrQueryTokens[] = "AND (o.uploaded_by_ui = 0)";
                    break;
                case 'manual':
                    $arrQueryTokens[] = "AND (o.source_post_id IS NULL)";
                    break;
            }
        }
        
        if (!empty($numUserId)) {
            $arrQueryTokens[] = sprintf("AND (o.user_id = %d)", $numUserId); 
        }
        if (!empty($numTagId)) {
            $arrQueryTokens[] = sprintf("AND (ot.tag_id = %d)", $numTagId); 
        }
        if (isset($numOnHomepage)) {
            $arrQueryTokens[] = sprintf("AND (o.on_homepage = %d)", $numOnHomepage); 
        }
        if (isset($numOnlyFromUsers) && $numOnlyFromUsers === 1) {
            $arrQueryTokens[] = "AND (o.source_blog_id IS NULL AND o.source_post_id IS NULL)"; 
        }
        if (!empty($strSearchQuery)) {
            $arrQueryTokens[] = sprintf(
                "AND ( o.search_data_pl IS NOT NULL "
                    . "AND o.search_data_pl @@ plainto_tsquery(%s, %s) )", 
                $objDb->quote('polish'), 
                $objDb->quote($strSearchQuery)
            );
        }
        
        return self::answer($arrQueryTokens);
    }
    
    public static function goVote ($numArtifactId, $numModifier) {
        $arrResult = array();
        $objDb = DbFactory::getInstance();
        if ($numModifier > 0) {
            $numSafeModifier = '+ 1';
            $arrResult['numModifier'] = 1;
        } else {
            $numSafeModifier = '- 1';
            $arrResult['numModifier'] = -1;
        }
        $strQ = sprintf(
            "UPDATE offer SET likes = (likes%s) WHERE id = :artifact_id", 
            $numSafeModifier
        );
        $objSth = $objDb->prepare($strQ);
        $objSth->execute(array(':artifact_id' => $numArtifactId));
        
        return self::answer($arrResult);
    }
    
    public function getBaseInfo($numId) {
        $objModel = new model\ArtifactModel();
        $arrInfo = $objModel->getBaseInfo($numId);
        
        return parent::answer($arrInfo);
    }
    /*
    public static function OLDgetBaseInfo ($numId) {
        $objDb = DbFactory::getInstance();
        $strQ = <<<EOF
SELECT id, 
    title_pl AS title, 
    slug_pl AS slug, 
    description, 
    (id % 5000) AS photo_directory, 
    (shows_count + shows_count_increaser) AS shows_count_fake, 
    round((image_weight/1024)) AS image_weight_kb, 
    width, 
    height, 
    mimetype, 
    removed_since, 
    likes, 
    image_source, 
    user_id, 
    add_date, 
    type, 
    on_homepage, 
    (adults_only+adults_only_suspicion) AS adults_only, 
    thumb_path, 
    thumb_filename, 
    is_removed  
FROM offer 
WHERE id = :id
EOF;
        $objSth = $objDb->prepare($strQ);
        $objSth->execute(array(':id' => $numId));
        $arrResult = $objSth->fetch();
        if (!empty($arrResult)) {
            $arrResult['title'] = stripslashes($arrResult['title']);
            $arrResult['description'] = stripslashes($arrResult['description']);
            $arrResult['extension'] = \backend\File::mimetypeToExtension($arrResult['mimetype']);
            
            if (!empty($arrResult['thumb_path']) && !empty($arrResult['thumb_filename'])) {
                $arrResult['thumb_url'] = '';
                if (substr($arrResult['thumb_path'], 0, 1) === '/') {
                    $arrResult['thumb_url'] .= Application::url();
                }
                $arrResult['thumb_url'] .= $arrResult['thumb_path'].'/'.$arrResult['thumb_filename'];
                $arrS3UrlToReplace = array(
                    'https://s3.eu-central-1.amazonaws.com/i.imged.pl', 
                    'http://s3.eu-central-1.amazonaws.com/i.imged.pl'
                );
                $arrResult['thumb_url'] = str_replace($arrS3UrlToReplace, 'http://i.imged.pl', $arrResult['thumb_url']);
            } else {
                $strImageId = $arrResult['id'];
                if ($arrResult['type'] === 'story') {
                    $strImageId = 1;
                }
                $arrResult['thumb_url'] = \webcitron\Subframe\Url::route('ImageDirect', array(
                    'directory' => $arrResult['photo_directory'], 
                    'slug' => $arrResult['slug'], 
                    'id' => $strImageId, 
                    'ext' => $arrResult['extension']
                ));
            }
        }
        
        return self::answer($arrResult);
    }
    */
    public static function getDetailedInfo ($numId) {
        $objDb = DbFactory::getInstance();
        $strQ = "SELECT orginal_exif "
                . "FROM offer "
                . "WHERE id = :id ";
        $objSth = $objDb->prepare($strQ);
        $objSth->execute(array(':id' => $numId));
        $arrResult = $objSth->fetch();
        
        return self::answer($arrResult);
    }
    
    
    public static function getTags ($numOfferId) {
        $strQ = "SELECT t.name, t.slug FROM offer_tag AS ot JOIN tag AS t ON t.id = ot.tag_id AND t.removed = FALSE WHERE ot.offer_id = :offer_id";
        $objDb = DbFactory::getInstance();
        $objSth = $objDb->prepare($strQ);
        $objSth->execute(array(
            ':offer_id' => $numOfferId
        ));
        $arrTags = $objSth->fetchAll();
        return self::answer($arrTags);
    }

    public static function getBasicStats(){
        $arrResult = array();
        $arrErrors = array();
        $objArtifactListModel = new ArtifactModel();
        $arrArtifactsBasicStats = $objArtifactListModel->getBasicStats();
        $arrResult[] = $arrArtifactsBasicStats;
        return self::answer($arrResult, $arrErrors);
    }

}
    