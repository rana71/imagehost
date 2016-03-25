<?php
include dirname(__FILE__).'/app/backend/YouTube.php';

//https://youtu.be/tUbo25IrgZ8?t=8m26s
$strId = 'tUbo25IrgZ8';
$arrImage = \backend\YouTube::getBestThumbnailImageFromID($strId);
echo '<Pre>';
print_r($arrImage['strUrl']);
echo strlen($arrImage['strBlob']);
exit();
    

exit(); 
