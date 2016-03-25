<?php
date_default_timezone_set('Europe/Warsaw');

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/app/autoload.php';

$objMailchimp = new Mailchimp('423517a5ad5e6a0017ccdd8979a6ed38-us1');
//
//$f = new Mailchimp_Folders($objMailchimp);
//print_r($f->getList('campaign'));
//
//exit();
$strCurrentDate = date('Y-m-d');
$strCampaignName = 'ad-'.$strCurrentDate;

/**
 * @description test test
 * *analytics-params* 
 * *artifacts*
 * *artifacts-count*
 * *uploaded-month*
 */
$strTemplateContent = <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Facebook sharing information tags -->
        <meta property="og:title" content="*|MC:SUBJECT|*">
        <title>*|MC:SUBJECT|*</title>
        
    
        <style type="text/css">
            *{
                    font-family:"Helvetiva Neue", Helvetica, Arial;
                    font-size:14px;
                    color:#676767;
            }
            body{
                    background-color:#FFFFFF;
            }
            .wrap-row{
                    padding-top:25px;
                    padding-bottom:25px;
            }
            .bg-gray{
                    background-color:#F6F6F6;
            }
            a{
                    text-decoration:none;
            }
            a.text-link:hover{
                    text-decoration:underline;
            }
            p{
                    text-align:justify;
                    line-height:20px;
                    margin-top:20px;
                    margin-bottom:20px;
            }
            h1{
                    text-align:left;
                    font-size:16px;
            }
            h2{
                    font-size:22px;
                    color:#B60000;
                    margin-top:30px;
                    margin-bottom:30px;
            }
            .stats{
                    color:#B60000;
                    font-size:12px;
            }
            .stats-bold{
                    color:#B60000;
                    font-size:35px;
                    letter-spacing:-2px;
            }
            .item{
                    background-color:#FFFFFF;
                    padding:1px;
                    border:1px solid #D6D6D6;
                    margin-bottom:30px;
            }
            .item:hover{
                    background-color:#B60000;
            }
            .item:hover .item-description-link{
                    color:#FFFFFF;
            }
            .item-description-link{
                    display:block;
                    margin:9px;
                    color:#B60000;
                    font-size:16px;
                    line-height:20px;
            }
            .button{
                    margin-top:40px;
                    margin-bottom:40px;
                    text-align:center;
            }
            .button-link{
                    font-weight:bold;
                    color:#B60000;
                    font-size:16px;
                    border:2px solid #B60000;
                    padding:10px 20px;
                    -webkit-border-radius:6px;
                    -moz-border-radius:6px;
                    border-radius:6px;
            }
            @media only screen and (min-width:600px){
                .wrapper{
                        width:600px !important;
                }

            }	
            @media only screen and (max-width:600px){
                .wrapper{
                        width:100% !important;
                }

            }	
            @media only screen and (max-width:600px){
                .column-wrapper{
                        width:100% !important;
                }
            }
        </style>
    </head>
    <body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
        <center>
            <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
                <tr>
                    <td align="center" valign="top" class="wrap-row">
                        <table border="0" cellpadding="0" cellspacing="0" width="600" class="wrapper">
                            <tr>
                                <td align="center" valign="top">
                                    <a href="http://imged.pl?*analytics-params*" title="Idź na imgED.pl" target="_blank">
                                        <img src="http://static.imged.pl/newsletter-logo.png" alt="imgED">
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#F6F6F6" align="center" valign="top" class="wrap-row bg-gray">
                        <table border="0" cellpadding="0" cellspacing="0" width="600" class="wrapper">
                            <tr>
                                <td align="center" valign="top">
                                    <h1>Cześć,</h1>
                                    
                                    <p>Zobacz <b>najpopularniejsze galerie</b>, które pojawiły się na imgED w ostatnim tygodniu!</p>
                                    
                                    <h2>Najpopularniejsze w tym tygodniu</h2>
                        

                                    *artifacts*

                                  <div class="button">
                                        <a href="http://imged.pl/top/?*analytics-params*" title="Najlepsze galerie" target="_blank" class="button-link">Zobacz więcej galerii!</a>
                                  </div>
                                                                  
                            
                                    <p>A może masz ciekawsze zdjęcia? <b>Wrzuć je za darmo</b> na imgED</p>

                                    <div class="button">
                                        <a href="http://imged.pl/upload.html?*analytics-params*" title="Zacznij dodawać" target="_blank" class="button-link">Zacznij dodawać!</a>
                                  </div>
                        
                                   <p>imgED - Prawdopodobnie <b>najpopularniejszy hosting zdjęć</b> w Polsce!</p>
                                    <p>
                                        Pozdrawiamy<br>
                                        <b>imgED</b>
                                    </p>
                                  </td>
                              </tr>
                          </table>
                    </td>
                </tr>
                <tr>
                    <td align="center" valign="top" class="wrap-row">
                        <table border="0" cellpadding="0" cellspacing="0" width="600" class="wrapper">
                            <tr>
                                <td align="center" valign="top">
                                    <table align="left" border="0" cellpadding="0" cellspacing="0" width="300" class="column-wrapper">
                                        <tr>
                                            <td align="center" valign="top" class="stats">
                                                <b class="stats-bold">*artifacts-count*</b><br>
                                                obrazów w naszej bazie
                                            </td>
                                          </tr>
                                      </table>
                                      <table align="left" border="0" cellpadding="0" cellspacing="0" width="300" class="column-wrapper">
                                        <tr>
                                            <td align="center" valign="top" class="stats">
                                                <b class="stats-bold">*uploaded-month*</b><br>
                                                dodanych w tym miesiącu
                                            </td>
                                          </tr>
                                      </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#F6F6F6" align="center" valign="top" class="wrap-row bg-gray">
                        <table border="0" cellpadding="0" cellspacing="0" width="600" class="wrapper">
                            <tr>
                                <td align="center" valign="center">
                                	<table border="0" cellpadding="0" cellspacing="0">
                                    	<tr>
                                        	<td valign="center">
                                            	Odkryj nas na 
                                                
                                            </td>
                                            <td valign="center">
                                            <a href="https://www.facebook.com/imgedPL" title="imgED.pl na Facebooku" target="_blank" class="text-link">
                                                    <img src="http://static.imged.pl/newsletter-fb.png" alt="imgED.pl na Facebooku" style="margin-left:10px;">
                                                </a>
                                                </td>
                                        </tr>
                                    </table>
                                    </td>
                                </tr>

                                <tr>
                                  <td align="center" valign="top" style="padding-top:20px; padding-bottom: 20px;">
									<a href="*|UNSUB|*">Nie chcę otrzymywać newslettera</a>.  
                                    &copy; *|CURRENT_YEAR|* <b>imgED</b>
                                  </td>
                                </tr>
                                <tr>
                                	<td align="center">*|REWARDS|*</td>
                                </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </center>
    </body>
</html>
EOF;

$strTemplateTextContent = <<<EOF
Cześć,\n
\n                            
Zobacz najpopularniejsze galerie, które pojawiły się na imgED w ostatnim tygodniu!\n
\n
Najpopularniejsze w tym tygodniu\n
*artifacts*\n
\n
Zobacz więcej najlepszych galerii: http://imged.pl/top/?*analytics-params*\n
A może masz ciekawsze zdjęcia? Wrzuć je za darmo na imgED: http://imged.pl/upload.html?*analytics-params*\n
\n
imgED - Prawdopodobnie najpopularniejszy hosting zdjęć w Polsce!\n
\n
Pozdrawiamy,\n
http://imged.pl
EOF;

$strItemTemplate = <<<EOF
      <table align="left" border="0" cellpadding="1" width="100%" cellspacing="0" class="item">
            <tr>
               <td class="item-photo">
                   <a class="item-photo-link" href="*artifact-url*?*analytics-params*" title="*artifact-title*" target="_blank">
                       <img src="*artifact-image-url*" alt="*artifact-title*" width="100%">
                   </a>
               </td>
           </tr>
           <tr>
               <td class="item-description">
                   <a href="*artifact-url*?*analytics-params*" title="*artifact-title*" target="_blank" class="item-description-link">
                       *artifact-title*
                   </a>
               </td>
           </tr>
       </table>  
EOF;
/*
 * *analytics-params* 
 * *artifacts*
 * *artifacts-count*
 * *uploaded-month*
 */

/*
 * *artifact-url*
 * *analytics-params* 
 * *artifact-title*
 * *artifact-image-url*
 */

$arrVariables = array(
    'analytics-params' => 'utm_source=newsletter&utm_medium=mail&utm_campaign='.$strCampaignName, 
    'artifacts-count' => 13905326, 
    'uploaded-month' => 493209, 
    'artifcats' => array(
        array(
            'url' => 'http://imged.pl/50-twarzy-psa-16989634.html', 
            'title' => '50 twarzy psa', 
            'image' => 'http://i1.imged.pl/50-twarzy-psa.jpg'
        ), 
        array(
            'url' => 'http://imged.pl/zdjecia-autorstwa-lindy-mccartney-16989635.html', 
            'title' => 'Zdjęcia autorstwa Lindy McCartney', 
            'image' => 'http://i1.imged.pl/zdjecia-autorstwa-lindy-mccartney.jpg'
        ), 
        array(
            'url' => 'http://imged.pl/cala-prawda-o-nas-16989631.html', 
            'title' => 'Cała prawda o nas', 
            'image' => 'http://i1.imged.pl/cala-prawda-o-nas.jpg'
        )
    )
);

$strArtifactsHtml = '';
$strArtifactsText = '';
foreach ($arrVariables['artifcats'] as $arrArtifact) {
    $strArtifactsHtml .= str_replace(array(
        '*artifact-url*', 
        '*artifact-title*', 
        '*artifact-image-url*', 
        '*analytics-params*'
    ), array(
        $arrArtifact['url'], 
        $arrArtifact['title'], 
        $arrArtifact['image'], 
        $arrVariables['analytics-params']
    ), $strItemTemplate);
    $strArtifactsText .= $arrArtifact['title']."\n".$arrArtifact['url'].'?'.$arrVariables['analytics-params']."\n\n";
}

$strTemplateHtml = str_replace(array(
    '*analytics-params*', 
    '*artifacts*', 
    '*artifacts-count*', 
    '*uploaded-month*'
), array(
    $arrVariables['analytics-params'], 
    $strArtifactsHtml, 
    number_format($arrVariables['artifacts-count'], 0, ',', ' '), 
    number_format($arrVariables['uploaded-month'], 0, ',', ' ')
), $strTemplateContent);

$strTemplateText = str_replace(array(
    '*analytics-params*', 
    '*artifacts*'
), array(
    $arrVariables['analytics-params'], 
    $strArtifactsText, 
), $strTemplateTextContent);

$boolOk = false;
$strPostfix = '';
$numNumber = 0;
$objTemplates = new Mailchimp_Templates($objMailchimp);
do {
    $boolOk = true;
    try {
        $arrTemplate = $objTemplates->add($strCampaignName.$strPostfix, $strTemplateHtml, 1069);
    } catch (Mailchimp_Invalid_Options $e) {
        $numNumber++;
        $strPostfix = '-'.$numNumber;
        $boolOk = false;
    }
} while ($boolOk === false);
$numTemplateId = $arrTemplate['template_id'];

// campaign folder: 49309
$objCampaigns = new Mailchimp_Campaigns($objMailchimp);
$arrNewCampaign = $objCampaigns->create('regular', array(
    'list_id' => '757830c648', 
    'subject' => 'Tytuł maila', 
    'from_email' => 'pl@imged.com', 
    'from_name' => 'imgED.pl', 
    'template_id' => $numTemplateId, 
    'folder_id' => 49309, 
    'title' => $strCampaignName, 
    'auto_footer' => false, 
), array(
    'html' => $strTemplateHtml, 
    'text' => $strTemplateText
));

$numCampaignId = $arrNewCampaign['id'];
$objTestSent = $objCampaigns->sendTest($numCampaignId, array(
    'bberlinski@gmail.com', 
    'a.mackiewicz@webcitron.eu'
));
print_r($objTestSent);