<?php
namespace backend\artifact;

use backend\DbFactory;
use webcitron\Subframe\Url;
use backend\user\UserController;
use backend\YouTube;
use webcitron\Subframe\Application;
use backend\utils\BBCodeParser;

class StoryController extends ArtifactController
{
    /**
     * @deprecated 
     */
    public static function getImages ($numStoryId) {
        $strSql = <<<EOF
SELECT filename, 
    title, 
    description, 
    ordering 
FROM story_photo 
WHERE artifact_id = :artifact_id 
ORDER BY ordering ASC 
EOF;
        $objDb = DbFactory::getInstance();
        $objSth = $objDb->prepare($strSql);
        $objSth->execute(array(':artifact_id' => $numStoryId));
        $arrImages = $objSth->fetchAll();
        foreach ($arrImages as & $arrImage) {
            $arrFilenameTokens = explode('.', $arrImage['filename']);
            $arrImage['ext'] = array_pop($arrFilenameTokens);
            $arrFilenameTokens = explode('-', join('.', $arrFilenameTokens));
            array_pop($arrFilenameTokens);
            $arrImage['base_filename'] = join('-', $arrFilenameTokens);
            $arrImage['photo_directory'] = $numStoryId%5000;
        }
//        echo '<pre>';
//        print_r($arrImages);
//        exit();
        return self::answer($arrImages);
    }
    
    
    public static function getElements ($numStoryId) {
        $strSql = <<<EOF
SELECT filename, 
    title, 
    description, 
    ordering, 
    type, 
    yt_id, 
    image_path AS thumb_path, 
    image_filename AS thumb_filename 
FROM story_photo 
WHERE artifact_id = :artifact_id 
ORDER BY ordering ASC 
EOF;
        $objDb = DbFactory::getInstance();
        $objSth = $objDb->prepare($strSql);
        $objSth->execute(array(':artifact_id' => $numStoryId));
        $arrImages = $objSth->fetchAll();
        foreach ($arrImages as & $arrImage) {
            if ($arrImage['type'] === 'image') {
                if (!empty($arrImage['thumb_path']) && !empty($arrImage['thumb_filename'])) {
                    $arrImage['thumb_url'] = '';
                    if (substr($arrImage['thumb_path'], 0, 1) === '/') {
                        $arrImage['thumb_url'] .= Application::url();
                    }
                    $arrImage['thumb_url'] .= $arrImage['thumb_path'].'/'.$arrImage['thumb_filename'];
                } else {
                    $arrFilenameTokens = explode('.', $arrImage['filename']);
                    $arrImage['ext'] = array_pop($arrFilenameTokens);
                    $arrFilenameTokens = explode('-', join('.', $arrFilenameTokens));
                    array_pop($arrFilenameTokens);
                    $arrImage['base_filename'] = join('-', $arrFilenameTokens);
//                    $arrImage['photo_directory'] = $numStoryId%5000;
                    $arrImage['thumb_url'] = \webcitron\Subframe\Url::route('ImageDirect', array(
                        'directory' => $numStoryId%5000, 
                        'slug' => join('-', $arrFilenameTokens), 
                        'id' => 1, 
                        'ext' => $arrImage['extension']
                    ));
                }
            }
        }
//        echo '<pre>';
//        print_r($arrImages);
//        exit();
        return self::answer($arrImages);
    }
    
    public static function getDetailedInfo($numId)
    {
        $arrInfo = array();
        $arrElements = self::getElements($numId);
        $arrInfo['arrElements'] = $arrElements['result'];
        return self::answer($arrInfo);
    }
    
    public static function upload ($strTitle, $arrElements, $strDescription = '', $strNewAccountEmail = '') {
        $arrArtifact = array();
        $arrErrors = array();
        $boolUrlsOk = 0;
        
        if (!empty($strTitle) && !empty($arrElements)) {
            $strTitle = stripslashes(strip_tags($strTitle));
            $strDescription = stripslashes(strip_tags($strDescription));
            $strNewAccountEmail = strtolower(stripslashes(strip_tags(trim($strNewAccountEmail))));
            
            foreach ($arrElements as & $arrElement) {
                if ($arrElement['type'] === 'yt-movie') {
                    $arrYoutube = YouTube::parseUrl($arrElement['url']);
                    if (empty($arrYoutube['strId'])) {
                        $arrResult = array('error' => sprintf('%s nie jest poprawnym adresem serwisu YouTube', $arrElement['url']));
                        return self::answer($arrResult);
                    }
                }
                $arrElement['title'] = stripslashes(strip_tags($arrElement['title']));
                $arrElement['description'] = nl2br(stripslashes(strip_tags($arrElement['description'])));
            }
            
            if (!empty($strNewAccountEmail)) {
                $arrPassword = \backend\Password::getGeneratePassword();
                $strPasswordHash = $arrPassword['password']['password'];
                $arrSignUpResult = UserController::signUp($strNewAccountEmail, $strNewAccountEmail, $strPasswordHash);
                if ($arrSignUpResult['status'] === 0) {
                    $arrErrors = $arrSignUpResult['error'];
                } else {
                    $objModel = new UserModel();
                    $arrAuthor = $objModel->getUserByUsername($strNewAccountEmail);
                    error_log(print_r($arrAuthor, true));
                    $numUserId = $arrAuthor['user_id'];
                }
            } else {
                $numUserId = 0;
                $arrLoggedUser = UserController::getLoggedUser();
                if (!empty($arrLoggedUser['result'])) {
                    $numUserId = $arrLoggedUser['result']['user_id'];
                }
            }
            if (empty($arrErrors)) {
                $numArtifactId = self::add($strTitle, $arrElements, $strDescription, 1, array(), $numUserId);
                if (!empty($numArtifactId)) {
                    $arrArtifactBackend = self::getBaseInfo($numArtifactId['result']);
                    $arrArtifact = $arrArtifactBackend['result'];
                    $arrArtifact['strUrl'] = Url::route('Details', array($arrArtifact['slug'], $arrArtifact['id']));
                }
            }
        }
        return self::answer($arrArtifact, $arrErrors);
    } 
    
    private static function add ($strTitle, $arrElements, $strDescription = '', $numAdultsOnly = 1, $arrTags = array(), $numUserId = 0) {
        $objDb = DbFactory::getInstance();
        
        $strQ = <<<EOF
INSERT INTO offer ( 
    title_pl, 
    slug_pl, 
    description, 
    add_date, 
    adults_only, 
    user_id, 
    type, 
    mimetype, 
    width, 
    height 
) VALUES (
    :title_pl, 
    :slug_pl, 
    :description, 
    NOW(), 
    :adults_only, 
    :user_id,  
    :type, 
    :mimetype, 
    :width, 
    :height 
) RETURNING id, (id % 5000) AS photo_directory, title_pl AS title
EOF;
        $arrImageTokens = explode(',', $arrElements[0]['image']['base64']);
        $strImageBlob = base64_decode(str_replace(' ', '+', $arrImageTokens[1]));
        $objFinfo = finfo_open();
        $strMimeType = finfo_buffer($objFinfo, $strImageBlob, FILEINFO_MIME_TYPE);
        
        $objImage = imagecreatefromstring($strImageBlob);
        $numImageWidth = imagesx($objImage);
        $numImageHeight = imagesy($objImage);
        
        $arrStorySlug = self::genUniqueSlug($strTitle);
        $strStorySlug = $arrStorySlug['result'];
        $arrData = array();
        $arrData[':title_pl'] = $strTitle;
        $arrData[':slug_pl'] = $strStorySlug;
        $arrData[':description'] = BBCodeParser::getHtml($strDescription);
        $arrData[':adults_only'] = $numAdultsOnly;
        $arrData[':user_id'] = intval($numUserId);
        $arrData[':type'] = 'story';
        $arrData[':mimetype'] = $strMimeType;
        $arrData[':width'] = $numImageWidth;
        $arrData[':height'] = $numImageHeight;
        
        $objSth = $objDb->prepare($strQ);
        $objSth->execute($arrData);
        $arrInserted = $objSth->fetch();
        
        self::addElementsToStory($arrElements, $arrInserted['id'], $strStorySlug, $arrInserted['photo_directory']);
        
        $strTitleToTagize = '';
        foreach ($arrElements as $arrElement) {
            $strTitleToTagize .= $arrElement['title'].' ';
        }
        
        if (!empty($arrTags)) {
            self::addTags($arrInserted['id'], $arrTags);
        } else {
            self::autoTagize(array(
                $arrInserted['id'] => trim($arrInserted['title'].' '.$strTitleToTagize)
            ));
        }
        
        return self::answer($arrInserted['id']);
    }
    
    private static function addElementsToStory ($arrElements, $numArtifactStoryId, $strStorySlug, $strPhotoDirectory) {
        $numOrder = 1;
        $numIsThumbnail = 1;
        foreach ($arrElements as $arrElement) {    
            if ($arrElement['type'] === 'image') {
                self::addImage($arrElement, $numOrder, $strStorySlug, $numArtifactStoryId, $strPhotoDirectory, $numIsThumbnail);
                $numIsThumbnail = 0;
            } else if ($arrElement['type'] === 'yt-movie') {
                self::addYtMovie($arrElement, $numOrder, $numArtifactStoryId);
            }
            
            $numOrder++;
        }
        return self::answer(true);
    }
    
    public static function addYtMovie ($arrElement, $numOrder, $numArtifactStoryId) {
        $arrYoutube = YouTube::parseUrl($arrElement['url']);
        if (empty($arrYoutube['strId'])) {
            return false;
        }
        $strQ = <<<EOF
INSERT INTO story_photo ( 
    artifact_id, 
    description, 
    yt_id, 
    yt_timestart,  
    ordering, 
    title, 
    type 
) VALUES (
    :artifact_id, 
    :description, 
    :yt_id, 
    :yt_timestart, 
    :ordering, 
    :title, 
    :type 
)
EOF;
        $objDb = DbFactory::getInstance();
        $objSth = $objDb->prepare($strQ);
        
        $arrData = array();
        $arrData[':artifact_id'] = $numArtifactStoryId;
        $arrData[':description'] = BBCodeParser::getHtml($arrElement['description']);
        $arrData[':yt_id'] = $arrYoutube['strId'];
        $arrData[':yt_timestart'] = null;
        if (!empty($arrYoutube['strTimeStart'])) {
            $arrData[':yt_timestart'] = $arrYoutube['strTimeStart'];
        }
        $arrData[':ordering'] = $numOrder;
        $arrData[':title'] = $arrElement['title'];
        $strType = 'movie';
        $arrData[':type'] = $strType;
        
        $objSth->execute($arrData);
        
        return true;
    }
    
    public static function addImage ($arrElement, $numOrder, $strStorySlug, $numArtifactStoryId, $strPhotoDirectory, $numIsThumbnail = 0) {
        $strQ = <<<EOF
INSERT INTO story_photo ( 
    artifact_id, 
    description, 
    filename, 
    width, 
    height, 
    image_weight, 
    mimetype, 
    ordering, 
    orginal_exif, 
    title, 
    type, 
    image_path, 
    image_filename 
) VALUES (
    :artifact_id, 
    :description, 
    :filename, 
    :width, 
    :height, 
    :image_weight, 
    :mimetype, 
    :ordering, 
    :orginal_exif, 
    :title, 
    :type, 
    :image_path, 
    :image_filename 
)
EOF;
        $objDb = DbFactory::getInstance();
        $objSth = $objDb->prepare($strQ);
        
        $arrImageTokens = explode(',', $arrElement['image']['base64']);
        $strImageBlob = base64_decode(str_replace(' ', '+', $arrImageTokens[1]));
        $objFinfo = finfo_open();
        $strMimeType = finfo_buffer($objFinfo, $strImageBlob, FILEINFO_MIME_TYPE);
        $strExt = \backend\File::mimetypeToExtension($strMimeType);
        if (empty($strExt)) {
            return false;
        }
        $arrExif = array();
        if (exif_imagetype($arrElement['image']['base64']) === IMAGETYPE_JPEG && function_exists('\exif_read_data')) {
            $arrExifNative = \exif_read_data($arrElement['image']['base64']);
            if ($arrExifNative === false) {
                $arrExif = array();
            } else {
                $arrExif = $arrExifNative;
            }
        }


        $objImage = imagecreatefromstring($strImageBlob);
        $numImageWidth = imagesx($objImage);
        $numImageHeight = imagesy($objImage);

        $strFilename = $strImageName = sprintf('%s-%d.%s', $strStorySlug, $numOrder, $strExt);

        $arrData = array();
        $arrData[':artifact_id'] = $numArtifactStoryId;
        $arrData[':description'] = BBCodeParser::getHtml($arrElement['description']);
        $arrData[':filename'] = $strFilename;
        $arrData[':width'] = $numImageWidth;
        $arrData[':height'] = $numImageHeight;
        $arrData[':image_weight'] = strlen($strImageBlob);
        $arrData[':mimetype'] = $strMimeType;
        $arrData[':orginal_exif'] = json_encode($arrExif);
        $arrData[':ordering'] = $numOrder;
        $arrData[':title'] = $arrElement['title'];
        $strType = 'image';
        $arrData[':type'] = $strType;
        $arrData[':image_path'] = '/'.$strPhotoDirectory;
        $arrData[':image_filename'] = $strFilename;

        $objSth->execute($arrData);

        if (Application::currentEnvironment() !== Application::ENVIRONMENT_DEV) {
            $strDirectory = sprintf('%s/../public_html/p/%d', APP_DIR, $strPhotoDirectory);
            @mkdir($strDirectory);
            chmod($strDirectory, 0777);
            file_put_contents($strDirectory.'/'.$strFilename, $strImageBlob);
            
            if ($numIsThumbnail === 1) {
                $strQ = <<<EOF
UPDATE offer 
SET thumb_path = :thumb_path, 
    thumb_filename = :thumb_filename 
WHERE id = :id
EOF;
                $objSth = $objDb->prepare($strQ);
                $objSth->execute(array(
                    ':thumb_path' => '/'.$strPhotoDirectory, 
                    ':thumb_filename' => $strFilename, 
                    ':id' => $numArtifactStoryId
                ));
            }
        }
        
        return true;
    }
}