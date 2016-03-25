<?php
namespace backend\feedback;
use webcitron\Subframe\Controller;

class FeedbackController extends Controller 
{
    
    public static function send($strEmail, $strContent, $strName = '', $strSubject = '') {
        $objMail = new \backend\SystemMail('UserContactFeedback');
        if (!empty($strEmail)) {
            $objMail->setReplyTo($strEmail, $strName);
        }
        $objMail->addRecipient('pl@imged.com', 'Administracja imgED');
//        $objMail->addRecipient('a.mackiewicz@webcitron.eu', 'Administracja imgED');
        $objMail->setVariable('date', date('d.m.Y, H:i'));
        $objMail->setVariable('email', $strEmail);
        $objMail->setVariable('content', nl2br($strContent));
        if (!empty($strSubject)) {
            $objMail->setSubject($strSubject);
        }
        if (empty($strName)) {
            $strName = '<i>brak</i>';
        }
        $objMail->setVariable('name', $strName);
        $objMail->send();
        
        
        return self::answer('Dziękujemy za zgłoszenie, odpowiemy najbszybciej jak będzie to możliwe');
    }
    
    public static function sendAdvertisement ($strEmail, $strContent, $strWww, $strName = '') {
        
        $objMail = new \backend\SystemMail('UserAdvertisementFeedback');
        if (!empty($strEmail)) {
            $objMail->setReplyTo($strEmail, $strName);
        }
        $objMail->addRecipient('pl@imged.com', 'Administracja imgED');
//        $objMail->addRecipient('a.mackiewicz@webcitron.eu', 'Administracja imgED');
        $objMail->setVariable('date', date('d.m.Y, H:i'));
        $objMail->setVariable('email', $strEmail);
        $objMail->setVariable('content', nl2br($strContent));
        $objMail->setVariable('www', $strWww);
        if (empty($strName)) {
            $strName = '<i>brak</i>';
        }
        $objMail->setVariable('name', $strName);
        $objMail->send();
        
        return self::answer('Dziękujemy za zgłoszenie, odpowiemy najbszybciej jak będzie to możliwe');
    }
    
}
