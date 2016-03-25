<?php namespace backend\utils;
 
use \JBBCode\Parser;


class BBCodeParser {
    
    private static $objParser = null;
    
    
    private static function getParser () {
        if (self::$objParser === null) {
//            echo 'create arser';
            self::$objParser = new Parser();
            self::$objParser->addBBCode("b", '<strong>{param}</strong>');
            self::$objParser->addBBCode("u", '<u>{param}</u>');
            self::$objParser->addBBCode("i", '<i>{param}</i>');
            self::$objParser->addBBCode('url', '<a href="{param}" rel="nofollow" target="_blank">{param}</a>');
        }
        return self::$objParser;
    }
    
    public static function getHtml ($strString) {
        $objPurifier = new \HTMLPurifier();
        $strString = $objPurifier->purify($strString);
//        echo '----'.$strString."\n";
        $strString = strip_tags($strString);
//        echo '----'.$strString."\n";
        $strString = nl2br($strString);
        
        $objParser = self::getParser();
        $objParser->parse($strString);
        $strString = $objParser->getAsHtml();
        return $strString;
    }
    
}