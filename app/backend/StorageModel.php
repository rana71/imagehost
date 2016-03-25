<?php

//classic pattern Strategy

namespace backend;

use backend\AmazonS3StorageModel;

class StorageModel implements StorageInterface {

    private $objService;
    
    
    public function __construct($strService, $arrConfig, $arrSpecifiedConfig = array())
    {
        switch($strService)
        {
            case 's3':
                $this->objService = new AmazonS3StorageModel($arrConfig, $arrSpecifiedConfig);
                break;
            default:
                $this->objService = new AmazonS3StorageModel($arrConfig, $arrSpecifiedConfig);
        }
    }

    public function setDefaultDir($strDir)
    {
        return $this->objService->setDefaultDir($strDir);
    }

    public function getDefaultDir()
    {
        return $this->objService->getDefaultDir();
    }

    public function createDir($strDir, $boolDefault = true, $strMode = 700)
    {
        return $this->objService->createDir($strDir, $boolDefault, $strMode = 700);
    }

    public function listDir($strDir = null)
    {
        return $this->objService->listDir($strDir);
    }

    public function removeDir($strDir)
    {
        return $this->objService->removeDir($strDir);
    }

    public function fileExists($strFilename, $strDir = null){
        return $this->objService->fileExists($strFilename, $strDir);
    }

    public function isDir($strDir)
    {
        return $this->objService->isDir($strDir);
    }

    public function downloadFile($strFilename)
    {
        return $this->objService->getFile($strFilename);
    }

    public function uploadFile($strFilename, $strSrc, $strDir = null, $strMode = null)
    {
        return $this->objService->uploadFile($strFilename, $strSrc, $strDir = null, $strMode = 700);
    }

    public function writeFile($strFilename, $strContent, $strDir = null, $strMode = 700)
    {
        return $this->objService->writeFile($strFilename, $strContent, $strDir = null, $strMode = 700);
    }
    
    public function writeImage($strFilename, $strContent, $strDir = null, $strMode = 700)
    {
        return $this->objService->writeImage($strFilename, $strContent, $strDir = null, $strMode = 700);
    }

    public function saveFile($strFilename, $strSrc, $strDir = null, $strMode = 700)
    {
        return $this->objService->saveFile($strFilename, $strSrc, $strDir = null, $strMode = 700);
    }

    public function uploadDir($strLocalDir, $strRemoveDir = null, $strMode = 700)
    {
        return $this->objService->uploadDir($strLocalDir, $strRemoveDir = null, $strMode = 700);
    }

    public function downloadDir($strLocalDir, $strRemoveDir = null, $strMode = 700)
    {
        return $this->objService->downloadDir($strLocalDir, $strRemoveDir = null, $strMode = 700);
    }

    public function getUrl($strFilename, $strDir = null)
    {
        return $this->objService->getUrl($strFilename, $strDir = null);
    }

    public function removeFile($mixedFilename, $strDir = null)
    {
        return $this->objService->removeFile($mixedFilename, $strDir = null);
    }
    
    public function genUniqueFilename($strExtension, $numFilenameLength = 64)
    {
        return $this->objService->genUniqueFilename($strExtension, $numFilenameLength);
    }


}


interface StorageInterface {

    public function setDefaultDir($strDir);

    public function getDefaultDir();

    public function createDir($strDir, $boolDefault = true, $strMode = 700);

    public function listDir($strDir = null);

    public function removeDir($strDir);

    public function isDir($strDir);

    public function fileExists($strFilename, $strDir = null);

    public function downloadFile($strFilename);

    public function uploadFile($strFilename, $strSrc, $strDir = null, $strMode = 700);

    public function writeFile($strFilename, $strContent, $strDir = null, $strMode = 700);

    public function saveFile($strFilename, $strSrc, $strDir = null, $strMode = 700);

    public function uploadDir($strLocalDir, $strRemoveDir = null, $strMode = 700);

    public function downloadDir($strLocalDir, $strRemoveDir = null, $strMode = 700);

    public function getUrl($strFilename, $strDir = null);

    public function removeFile($mixedFilename, $strDir = null);
}