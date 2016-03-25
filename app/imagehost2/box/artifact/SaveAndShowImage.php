<?php namespace imagehost2\box\artifact;

use webcitron\Subframe\Response;
use webcitron\Subframe\Application;
use backend\artifact\ImageController;
use backend\artifact\ArtifactController;

class SaveAndShowImage extends \webcitron\Subframe\Box {
    
    public $strPhotoDirectory = '';
    public $strSlug = '';
    public $numId = 0;
    
    private static $strDevImageUrl = 'http://stickerslug.s3.amazonaws.com/product-image/z-bs-0100-black-alt00.jpg';
    private static $strDevImageContentsCache = '';
    
    public function __construct($strPhotoDirectory, $strSlug, $numId) {
        $this->strPhotoDirectory = $strPhotoDirectory;
        $this->strSlug = $strSlug;
        $this->numId = $numId;
    }
    
    public function launch () {
        
        if (Application::currentEnvironment() === Application::ENVIRONMENT_DEV || Application::currentEnvironment() === Application::ENVIRONMENT_NIGHTLY) {
            if (empty(self::$strDevImageContentsCache)) {
                $strImageUrl = self::$strDevImageUrl;
                self::$strDevImageContentsCache = file_get_contents($strImageUrl);
            } 
            $strImageContents = self::$strDevImageContentsCache;
        } else {
            $strImageUrl = ImageController::getImageUrl($this->numId);
            $strImageContents = file_get_contents($strImageUrl['result']);
            if (Application::currentEnvironment() === Application::ENVIRONMENT_PRODUCTION) {
                ArtifactController::saveFile($strImageContents, $this->numId, $this->strSlug, $this->strPhotoDirectory);
            }
            
            
            $objImage = imagecreatefromstring($strImageContents);
            $numImageWidth = imagesx($objImage);
            $numImageHeight = imagesy($objImage);

            $objFinfo = finfo_open();
            $strMimeType = finfo_buffer($objFinfo, $strImageContents, FILEINFO_MIME_TYPE);
            
            ImageController::fillImageInfo($this->numId, array(
                'image_weight' => strlen($strImageContents), 
                'width' => $numImageWidth, 
                'height' => $numImageHeight, 
                'mimetype' => $strMimeType, 
                'thumb_path' => '/'.$this->strPhotoDirectory, 
                'thumb_filename' => sprintf('%s-%d.jpg', $this->strSlug, $this->numId)
            ));
        }
        
        
        
        
        
        
        return Response::image(array(
            'strContent' => $strImageContents
        ));
    }
    
}