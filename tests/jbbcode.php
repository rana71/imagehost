<?php
require dirname(__FILE__).'/../vendor/autoload.php';
require dirname(__FILE__).'/../app/autoload.php';

$strText = '[b]pogrubienie[/b]'.PHP_EOL;
$strText .= '[i]kursywa[/i]'.PHP_EOL;
$strText .= '[u]podkreślenie[/u]'.PHP_EOL;
$strText .= '[url]http://imged.pl[/url]'.PHP_EOL;
//$strText .= '[ol][li]lista 1[/li][li]lista 2[/li][/ol]';

//$strText = '<strong>pogrubienie</div><br />
//<i>kursywa</i><br />
//<u>podkreślenie</u><br />
//<ul><li>lista 1</li><li>lista 2</li></ul><ol><li>lista 1</li><li>lista 2</li></ol>';

//$strText = nl2br(strip_tags($strText));
echo backend\utils\BBCodeParser::getHtml($strText);

 
//print $objParser->getAsBBCode();