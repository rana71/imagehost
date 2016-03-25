<?php
namespace backend\cron;

use \backend\DbFactory;
use \backend\artifact\model\ArtifactModel;
use backend\StorageModel;

class ArtifactImages
{
    
    private function markItemAsRemoved ($numItemId) {
        $objModel = new ArtifactModel();
        $objModel->markAsRemoved($numItemId);
    }
    
    public function moveImportedItemsElementsImagesToS3 ($numLimit, $strLocalImagesDirectory) {
        $arrSocketAutotagize = array(
            'address' => '127.0.0.1', 
            'port' => 43358, 
            'socket' => null
        );
        
        if (@fsockopen($arrSocketAutotagize['address'], $arrSocketAutotagize['port'])) {
            exit( 'Another  is running, dying' );
        }

        if (( $arrSocketAutotagize['socket'] = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
            exit("socket_create() failed: reason: ".socket_strerror(socket_last_error())."\n");
        }

        if (socket_bind($arrSocketAutotagize['socket'], $arrSocketAutotagize['address'], $arrSocketAutotagize['port']) === false) {
            exit("socket_bind() failed: reason: ".socket_strerror(socket_last_error($arrSocketAutotagize['socket']))."\n");
        }

        if (socket_listen($arrSocketAutotagize['socket'], 1) === false) {
            exit("socket_listen() failed: reason: ".socket_strerror(socket_last_error($arrSocketAutotagize['socket']))."\n");
        }
        
        $objModel = new ArtifactModel();
        
        $arrImageElements = $objModel->getImageElementsImageFileLocallyNotRemoved($numLimit);
        echo 'getted '.count($arrImageElements).' elements to move... '.PHP_EOL;
        if (!empty($arrImageElements)) {
            $objS3Model = new StorageModel('s3', array(
                'key' => 'AKIAJRIWCGLMNSOD6FOA', 
                'secret' => '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', 
                'region' => 'eu-central-1', 
                'bucket' => 'i.imged.pl',
                'version' => '2006-03-01'
            ), array(
                'scheme' => 'http'
            ));
            foreach ($arrImageElements as $arrImageElement) {
                $strImageFilename = $arrImageElement['image_filename'];
                $strImageSource = sprintf('%s%s/%s', $strLocalImagesDirectory, $arrImageElement['image_path'], $arrImageElement['image_filename']);
//                echo 'image path: '.$strImageSource.PHP_EOL;
                if (!file_exists($strImageSource)) {
                    echo '1. removing '.$arrImageElement['item_id'].PHP_EOL;
                    $this->markItemAsRemoved($arrImageElement['item_id']);
                    continue;
                }
                
                $numImageWeight = filesize($strImageSource);
                if (intval($numImageWeight) === 0) {
                    echo '2. removing'.$arrImageElement['item_id'].PHP_EOL;
                    $this->markItemAsRemoved($arrImageElement['item_id']);
                    continue;
                }
                
                $strMimetype = '';       
                if (class_exists('\finfo')) {
                    $objFileInfo = new \finfo(FILEINFO_MIME_TYPE);
                    $strMimetype = $objFileInfo->buffer(file_get_contents($strImageSource));
                }
                
                $arrSaveResult = $objS3Model->saveFile($strImageFilename, $strImageSource, null, 700, $strMimetype);
                if (!empty($arrSaveResult['ObjectURL'])) {
                    $arrImageSizes = getimagesize($arrSaveResult['ObjectURL']);
                    $arrNameTokens = explode('/', $arrSaveResult['ObjectURL']);
                    $strImageFilename = array_pop($arrNameTokens);
                    $strImagePath = join('/', $arrNameTokens);
                    
                    $arrDataToUpdate = array(
                        'width' => $arrImageSizes[0], 
                        'height' => $arrImageSizes[1], 
                        'image_path' => $strImagePath, 
                        'mimetype' => $strMimetype, 
                        'weight' => $numImageWeight
                    );
                    echo 'update item '.$arrImageElement['item_id'].PHP_EOL;
                    $objModel->updateItemElementImageInfo($arrImageElement['item_element_id'], $arrDataToUpdate);
                }
            }
        }
        
        
        socket_close($arrSocketAutotagize['socket']);
        $arrSocketAutotagize['socket'] = null;
    }
    
    public static function test () {
        $bucket = '*** Your Bucket Name ***';
        $keyname = '*** Your Object Key ***';
        // $filepath should be absolute path to a file on disk						
        $filepath = '*** Your File Path ***';

        // Instantiate the client.
        $s3 = \Aws\S3\S3Client::factory(array(
            'key'=> 'AKIAJRIWCGLMNSOD6FOA',
            'secret'=> 'n3e185gdxFIBk+XbinEVCQTqsKzj9VW4BaV1fIpH',
            'region'=>'eu-central-1',
//            'region'=>'us-west-1',
            'bucket'=>'	imged-thumbs', 
            'version' => '2006-03-01'
        ));
        
        $result = $s3->upload('imged-thumbs', 'test', 'HHH');
        // Upload a file.
//        $result = $s3->putObject(array(
//            'Bucket'       => 'imged-thumbs',
//            'Key'          => 'test',
//            'Body'   => 'Hello, world!', 
//            'ContentType'  => 'text/plain',
////            'ACL'          => 'public-read',
////            'StorageClass' => 'REDUCED_REDUNDANCY'
//        ));
        echo '<pre>';
        print_r($result);
        exit();
    }
    
    public static function createThumbnails ($strConnectionName) {
        
        
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
        $objDb = DbFactory::getInstance();
        
        $objModel = new ArtifactModel();
//        $arrArtifacts = $objModel->getWithoutThumbnail();
        $objModel->enablePhotoStorage();
        
        $strFileContents = @file_get_contents(dirname(__FILE__).'/logo1024.png');
//        echo strlen($strFileContents);
//        exit();
        if (strlen($strFileContents) > 0) {
            $strThumbnailFilename = 'test-logo.png';
            $objModel->addPhotoToStorage($strFileContents, $strThumbnailFilename);
        }
//        
//        foreach ($arrArtifacts as $arrArtifact) {
//            $strExtension = File::mimetypeToExtension($arrArtifact['mimetype']);
//            $strOrginalFilePath = sprintf('%s/../../../public_html/p/%d/%s-%d.%s', dirname(__FILE__), $arrArtifact['photo_directory'], $arrArtifact['slug_pl'], $arrArtifact['id'], $strExtension);
//            $strFileContents = @file_get_contents($strOrginalFilePath);
//            if (strlen($strFileContents) > 0) {
//                $strThumbnailFilename = sprintf('%s-%d_180x180.%s', $arrArtifact['slug_pl'], $arrArtifact['id'], $strExtension);
//                $objModel->addPhotoToStorage($strFileContents, $strThumbnailFilename);
//            }
//        }
//        
//        echo '<Pre>';
//        print_r($arrArtifacts);
//        echo '</pre>';
        
        unset ($objDb);
        socket_close(self::$socket);
    }
}