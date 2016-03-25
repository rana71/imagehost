<?php
namespace backend;

class File 
{
    
    public static function mimetypeToExtension($strMimeType) {
        $strExt = '';
        switch ($strMimeType) {
            case 'image/png':
                $strExt = 'png';
                break;
            case 'image/gif':
                $strExt = 'gif';
                break;
            case 'image/jpg':
            case 'image/jpeg':
            case 'image/pjpeg':
                $strExt = 'jpg';
                break;  
        }
        return $strExt;
    }
    
    public static function extensionToMimeType ($strExtension) {
        $strMimeType = '';
        switch ($strExtension) {
            case 'png': $strMimeType = 'image/png'; break;
            case 'gif': $strMimeType = 'image/gif'; break;
            case 'jpg': $strMimeType = 'image/jpg'; break;
            case 'jpeg': $strMimeType = 'image/jpeg'; break;
            case 'pjpeg': $strMimeType = 'image/pjpeg'; break;
        }
        return $strMimeType;
    }
    
}
