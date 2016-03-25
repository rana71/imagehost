<?php
namespace backend;

use Aws\S3\S3Client;
use Aws\S3\Model\MultipartUpload\UploadBuilder;
use Aws\S3\Model\ClearBucket;

class AmazonS3StorageModel implements StorageInterface {

    private $strDefaultDir;
    private $objService;

    public function __construct($arrConfig, $arrSpecifiedConfig)
    {
        $arrS3Config = array_merge(array(
            'credentials' => array(
                'key' => $arrConfig['key'],
                'secret' => $arrConfig['secret'],
            ), 
            'region' => $arrConfig['region'], 
            'version' => $arrConfig['version']
        ), $arrSpecifiedConfig);
        
        $this->objService = S3Client::factory($arrS3Config);
        if(!empty($arrConfig['bucket'])){
            $this->setDefaultDir($arrConfig['bucket']);
        }
    }

    public function setDefaultDir($strDir)
    {
        return $this->strDefaultDir = $strDir;
    }

    public function getDefaultDir()
    {
        return $this->strDefaultDir;
    }

    public function createDir($strDir, $boolDefault = true, $strMode = 700)
    {
        if($boolDefault){
            $this->setDefaultDir($strDir);
        }
        $this->objService->createBucket(array('Bucket' => $strDir, 'ACL' => $this->determineAcl($strMode)));
        return $this->objService->waitUntil('BucketExists', array('Bucket' => $strDir));
    }

    public function listDir($strDir = null)
    {
        $iterator =  $this->objService->getIterator('ListObjects', array(
            'Bucket' => empty($strDir) ? $this->getDefaultDir() : $strDir
        ));
        $list = array();
        foreach ($iterator as $object) {
            $list[] = $object['Key'];
        }
        return $list;
    }

    public function isDir($strDir = null)
    {
        return $this->objService->doesBucketExist($strDir);
    }

    public function fileExists($strFilename, $strDir = null)
    {
        return $this->objService->doesObjectExist(empty($strDir) ? $this->getDefaultDir() : $strDir,$strFilename);
    }
    
    public function genUniqueFilename($strExtension, $numFilenameLength = 64)
    {
        do {
            $strFileName = sprintf('%s.%s', \utils\Tools::randomString($numFilenameLength), $strExtension);
        } while ($this->fileExists($strFileName));
        return $strFileName;
    }

    public function removeDir($strDir)
    {
        $clear = new ClearBucket($this->objService, $strDir);
        $clear->clear();
        $this->objService->deleteBucket(array('Bucket' => $strDir));
        return $this->objService->waitUntil('BucketNotExists', array('Bucket' => $strDir));
    }

    public function downloadFile($strFilename, $strDir = null)
    {
        return $this->objService->getObject(
            array(
                'Bucket' => empty($strDir) ? $this->getDefaultDir() : $strDir,
                'Key'    => $strFilename
            )
        );
    }

    public function uploadFile($strFilename, $strSrc, $strDir = null, $strMode = 700)
    {
        $uploader = UploadBuilder::newInstance()
            ->setClient($this->objService)
            ->setSource($strSrc)
            ->setBucket(empty($strDir) ? $this->getDefaultDir() : $strDir)
            ->setKey($strFilename)
            ->setConcurrency(5)
            ->setOption('ACL', $this->determineAcl($strMode))
            ->build();
        return $uploader->upload();
    }


    public function writeFile($strFilename, $strContent, $strDir = null, $strMode = 700)
    {
        $arrFileParams = array(
            'Bucket' => empty($strDir) ? $this->getDefaultDir() : $strDir,
            'Key'    => $strFilename,
            'Body'   => $strContent,
            'ACL'   => $this->determineAcl($strMode)
        );
        if (class_exists('\finfo')) {
            $objFileInfo = new \finfo(FILEINFO_MIME_TYPE);
            $arrFileParams['ContentType'] = $objFileInfo->buffer($strContent);
        }
        
        return $this->putObject($arrFileParams);
    }
    
    public function saveFile($strFilename, $strSrc, $strDir = null, $strMode = 700, $strMimeType = null)
    {
        $arrFileParams = array(
            'Bucket' => empty($strDir) ? $this->getDefaultDir() : $strDir,
            'Key'    => $strFilename,
            'SourceFile'   => $strSrc,
            'ACL'   => $this->determineAcl($strMode), 
        );
        if (empty ($strMimeType) && class_exists('\finfo')) {
            $objFileInfo = new \finfo(FILEINFO_MIME_TYPE);
            $strMimeType = $objFileInfo->buffer(file_get_contents($strSrc));
        }
        
        if (!empty($strMimeType)) {
            $arrFileParams['ContentType'] = $strMimeType;
        }
        
        
        return $this->putObject($arrFileParams);
    }

    private function putObject($arr)
    {
        return $this->objService->putObject($arr);
    }

    public function uploadDir($strLocalDir, $strRemoveDir = null, $strMode = 700)
    {
        return $this->objService->uploadDirectory($strLocalDir,
            empty($strRemoveDir) ? $this->getDefaultDir() : $strRemoveDir, '',
            array(
                'params'      => array('ACL' => $this->determineAcl($strMode)),
                'concurrency' => 20,
                'debug'       => false
            ));
    }

    public function downloadDir($strLocalDir, $strRemoveDir = null, $strMode = 700)
    {
        return $this->objService->downloadBucket($strLocalDir,
            empty($strRemoveDir) ? $this->getDefaultDir() : $strRemoveDir, '',
            array(
                'concurrency' => 20,
                'debug'       => false
            ));
    }

    public function getUrl($strFilename, $strDir = null)
    {
        return $this->objService->getObjectUrl(empty($strDir) ? $this->getDefaultDir() : $strDir, $strFilename);
    }

    public function removeFile($mixedFilename, $strDir = null)
    {
        $arrFilename = array();
        if(!is_array($mixedFilename)){
            $arrFilename[0]['Key'] = $mixedFilename;
        } else {
            foreach($mixedFilename as $filename){
                $arrFilename[] = array('Key'=>$filename);
            }
        }
        return $this->objService->deleteObjects(array(
            'Bucket' => empty($strDir) ? $this->getDefaultDir() : $strDir,
            'Objects' => $arrFilename,
            'Quiet' => true,
        ));
    }

    /**
     * Determine the most appropriate ACL based on a file mode.
     *
     * @param int $mode File mode
     *
     * @return string
     */
    private function determineAcl($strMode)
    {
        if ($strMode == 777) {
            return 'public-read-write';
        }
        if ($strMode >= 700 && $strMode <= 799) {
            return 'public-read';
        }
        if ($strMode >= 600 && $strMode <= 699) {
            return 'authenticated-read';
        }
        return 'private';
    }
}
