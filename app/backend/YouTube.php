<?php namespace backend;

class YouTube
{
    public static function parseUrl ($strUrl) {
        $arrResult = array();
        $arrQueryTokens = array();
        $arrTokens = parse_url($strUrl);
        
        $strHost = '';
        if(!empty($arrTokens['host'])) {
            $strHost = str_replace('www.', '', $arrTokens['host']);
        }
        
        if (!empty($strHost) && !empty($arrTokens['path']) && ($strHost === 'youtube.com' || $strHost === 'youtu.be')) {
            if (ltrim($arrTokens['path'], '/') === 'watch') {
                // youtube.com/watch?v=Ywz9jJXOMHs
                // youtube.com/watch?v=Ywz9jJXOMHs&t=11m16s
                parse_str($arrTokens['query'], $arrQueryTokens);
                if (!empty($arrQueryTokens['v'])) {
                    $arrResult['strId'] = $arrQueryTokens['v'];
                }
                if (!empty($arrQueryTokens['t'])) {
                    $arrResult['strTimeStart'] = $arrQueryTokens['t'];
                }
            } else {
                // youtube.com/yiVj5ow0H0w
                // youtube.com/yiVj5ow0H0w?t=1m45s
                $arrResult['strId'] = ltrim($arrTokens['path'], '/');
                if (!empty($arrTokens['query'])) {
                    parse_str($arrTokens['query'], $arrQueryTokens);
                    if (!empty($arrQueryTokens['t'])) {
                        $arrResult['strTimeStart'] = $arrQueryTokens['t'];
                    }
                }
            }
        }
        return $arrResult;
    }
    
    public static function getBestThumbnailImageFromID ($strVideoId) {
        $arrReturn = array('strUrl' => '', 'strBlob' => '');
        
        $arrUrlsPatterns = array(
            'http://img.youtube.com/vi/%s/maxresdefault.jpg', 
            'http://img.youtube.com/vi/%s/sddefault.jpg', 
            'http://img.youtube.com/vi/%s/hqdefault.jpg', 
            'http://img.youtube.com/vi/%s/0.jpg', 
            'http://img.youtube.com/vi/%s/mqdefault.jpg', 
            'http://img.youtube.com/vi/%s/default.jpg'
        );
        
        foreach ($arrUrlsPatterns as $strPattern) {
            $strUrl = sprintf($strPattern, $strVideoId);
            $arrHeaders = get_headers($strUrl);    
            if (strpos($arrHeaders[0], '200') === false) {
                time_nanosleep(0, 250000000); // 1/4s.
            } else {
                $arrReturn['strUrl'] = $strUrl;
                $arrReturn['strBlob'] = file_get_contents($strUrl);
                break;
            }
        }
        
        return $arrReturn;
    }
    /**
     * @deprecated since 10.12.2015
     */
//    public static function getImageFromUrl ($strUrl) {
//        $arrYoutubeMovie = self::parseUrl($strUrl);
//        $strImageUrl = sprintf('http://img.youtube.com/vi/%s/maxresdefault.jpg', $arrYoutubeMovie['strId']);
//        
//        return $strImageUrl;
//    }
}
