<?php namespace backend;

class SystemMail
{

    public $strId = '';
    public $arrRecipients = array();
    public $arrVariables = array();
    public $numSmtpDebug = 0;
    public $strSubject = '';
    public $arrReplyTo = array(
        'strEmail' => 'pl@imged.com', 
        'strName' => 'imgED'
    );
    
    private $arrConfig = array(
        'host' => '91.200.186.69', 
        'username' => 'pl@imged.com', 
        'password' => 'CQMP8Jrug0', 
        'port' => 587
    );
    
    public function __construct ($strMailId = '') {
        $this->strId = $strMailId;
    }
    
    public function setSubject ($strSubject) {
        $this->strSubject = $strSubject;
    }
    
    public function setReplyTo ($strEmail, $strName = '') {
        $this->arrReplyTo = array(
            'strEmail' => $strEmail, 
            'strName' => $strName
        );
    }
    
    public function addRecipient($strEmail, $strName = '') {
        $this->arrRecipients[] = array(
            'strEmail' => $strEmail, 
            'strName' => $strName
        );
    }
    
    public function setVariable($strVariableName, $strVariableContent) {
        $this->arrVariables[$strVariableName] = $strVariableContent;
    }
    
    public static function test ($strRecipient = 'a.mackiewicz@webcitron.eu') {
        $objMail = new SystemMail('Test');
        $objMail->addRecipient($strRecipient);
        $objMail->numSmtpDebug = 2;
        return $objMail->send();
    }
    
    public function testSmtp () {
        $boolResult = false;
        $objSmtp = new \SMTP();
        
        try {
            if ($objSmtp->connect($this->arrConfig['host'], $this->arrConfig['port'])) {
                if ($objSmtp->hello()) { 
                    if ($objSmtp->authenticate($this->arrConfig['username'], $this->arrConfig['password'])) {
                        $boolResult = true;
//                    } else {
//                        throw new \Exception('Authentication failed: ' . $objSmtp->getLastReply());
                    }
//                } else {
//                    throw new \Exception('HELO failed: '. $objSmtp->getLastReply());
                }
//            } else {
//                throw new \Exception('Connect failed');
            }
        } catch (\Exception $e) {
//            echo 'SMTP error: '. $e->getMessage(), "\n";
        }
        //Whatever happened, close the connection.
        $objSmtp->quit(true);
        
        return $boolResult;
    }
    
    public function send () {
        $arrEmail = $this->getEmail($this->strId, $this->arrVariables);
        $objPhpMailer = new \PHPmailer();
        $objPhpMailer->IsSMTP();
        $objPhpMailer->SMTPAuth = true;
        $objPhpMailer->Port = $this->arrConfig['port'];
        $objPhpMailer->SMTPDebug = $this->numSmtpDebug;
//        $objPhpMailer->SMTPSecure = 'tls';
        $objPhpMailer->Host = $this->arrConfig['host'];
	$objPhpMailer->Username = $this->arrConfig['username'];
	$objPhpMailer->Password   = $this->arrConfig['password'];
	$objPhpMailer->From = "pl@imged.com"; 
        $objPhpMailer->FromName = "imgED";
        $objPhpMailer->AddReplyTo($this->arrReplyTo['strEmail'], $this->arrReplyTo['strName']); 
        foreach( $this->arrRecipients as $arrRecipient ) {
            $objPhpMailer->AddAddress($arrRecipient['strEmail'], $arrRecipient['strName']);
        }
        if (!empty($this->strSubject)) {
            $objPhpMailer->Subject = $this->strSubject;
        } else {
            $objPhpMailer->Subject = $arrEmail['strSubject'];
        }
        $objPhpMailer->MsgHTML($arrEmail['strContent']);
        $objPhpMailer->AltBody = strip_tags(str_replace(array('<br>', '<br />'), "\n", $arrEmail['strContent']));
        $objPhpMailer->CharSet = 'UTF-8';
        $objPhpMailer->IsHTML(true);
        return @$objPhpMailer->Send();
    }
    
    private function getEmail ($strId, $arrVariables = array()) {
        $strMethodName = sprintf('getEmail%s', $strId);
        $arrEmail = $this->{$strMethodName}();
        
        $arrReplaceFrom = array();
        $arrReplaceTo = array();
        foreach ($arrVariables as $strVariable => $strValue) {
            $arrReplaceFrom[] = sprintf('{{%s}}', $strVariable);
            $arrReplaceTo[] = $strValue;
        }
        
        $arrEmail['strSubject'] = str_replace($arrReplaceFrom, $arrReplaceTo, $arrEmail['strSubject']);
        $arrEmail['strContent'] = str_replace($arrReplaceFrom, $arrReplaceTo, $arrEmail['strContent']);
        
        $arrEmail['strContent'] .= $this->footer();
        
        return $arrEmail;
    }
    
    private function getEmailTest ()  {
        $strSubject = 'Testowy email';
        $strContent = <<<EOF
Testowy email dla sprawdzenia skrzynki
EOF;
        $arrEmail = array(
            'strSubject' => $strSubject, 
            'strContent' => $strContent
        );
        return $arrEmail; 
    }
    
    private function getEmailUserContactFeedback () {
        $strSubject = 'Zapytanie kontaktowe';
        $strContent = <<<EOF
Skorzystano z formularza na stronie "kontakt"<br />
<br />
Data wysyłki: {{date}}<br />
Imię: {{name}}<br />
Adres e-mail: <a href="mailto::{{email}}">{{email}}</a><br />
Treść zapytania: {{content}}
EOF;
        $arrEmail = array(
            'strSubject' => $strSubject, 
            'strContent' => $strContent
        );
        return $arrEmail; 
    }
    
    private function getEmailUserAdvertisementFeedback () {
        $strSubject = 'Zapytanie o reklamę na serwisie';
        $strContent = <<<EOF
Skorzystano z formularza na stronie "reklama w imgED"<br />
<br />
Data wysyłki: {{date}}<br />
Imię: {{name}}<br />
Strona internetowa: <a href="{{www}}">{{www}}</a><br />
Adres e-mail: <a href="mailto::{{email}}">{{email}}</a><br />
Treść zapytania: {{content}}
EOF;
        $arrEmail = array(
            'strSubject' => $strSubject, 
            'strContent' => $strContent
        );
        return $arrEmail; 
    }
    
    private function getEmailReportArtifactAbuse ()  {
        $strSubject = 'Zgłoszenie nadużycia !';
        $strContent = <<<EOF
Zgłoszone nadużycie<br />
<br />
ID Artefaktu: #{{id}}<br />
URL: <a href="{{url}}">{{url}}</a><br />
Zgłaszający: {{reporter_name}} <<a href="mailto:{{reporter_email}}">{{reporter_email}}</a>><br />
Wyjaśnienie: {{reason}}
EOF;
        $arrEmail = array(
            'strSubject' => $strSubject, 
            'strContent' => $strContent
        );
        return $arrEmail; 
    }
    
    private function getEmailForgotPassword () {
        $strSubject = 'Nowe hasło do konta';
        $strContent = <<<EOF
Witaj, {{username}} !<br />
<br />
Prosiłeś o zmianę hasła do swojego konta. Wygenerowaliśmy dla Ciebie nowe. Brzmi ono: <strong>{{new_password}}</strong><br />
EOF;
        $arrEmail = array(
            'strSubject' => $strSubject, 
            'strContent' => $strContent
        );
        return $arrEmail; 
    }
    
    private function getEmailRpcapiError () {
        $strSubject = 'Subframe - RPC API error';
        $strContent = <<<EOF
Application: {{application}}<br />
App URL: {{url}}<br />
Method app pointer: {{pointer}}<br />
Method raw path: {{raw}}<br />
Call params: {{params}}<br />
Error string: {{error}}<br />
Call date: {{date}}<br />
EOF;
        $arrEmail = array(
            'strSubject' => $strSubject, 
            'strContent' => $strContent
        );
        return $arrEmail; 
    }
    
    private function getEmailAdminPasswordForgot () {
        $strSubject = 'Nowe hasło administratora';
        $strContent = <<<EOF
Witaj, {{username}} !<br />
<br />
Prosiłeś o zmianę hasła do panelu administratora. Wygenerowaliśmy dla Ciebie nowe. Brzmi ono: <strong>{{new_password}}</strong>:<br />
EOF;
        $arrEmail = array(
            'strSubject' => $strSubject, 
            'strContent' => $strContent
        );
        return $arrEmail; 
    }
    
    private function getEmailChangeEmail () {
        $strSubject = 'Zmiana adresu e-mail';
        $strContent = <<<EOF
Witaj, {{username}} !<br />
<br />
Zmieniono Twój adres e-mail . Aby potwierdzić - kliknij w link aktywacyjny:<br />
<br />
<a href='{{url}}'>{{url}}</a>
EOF;
        $arrEmail = array(
            'strSubject' => $strSubject, 
            'strContent' => $strContent
        );
        return $arrEmail;
    }
    
    private function getEmailDeveloperInfo () {
        $strSubject = 'Informacje developerskie';
        $strContent = <<<EOF
Hello, 
I've already successfully done cron script <strong>{{script}}</strong> in app for url <strong>{{appurl}}</strong> :)<br />
It taken me <strong>{{seconds}}</strong> seconds of work!<br />
Next time can be better :)<br />
<br />
See you later!<br />
// Your lovely Subframe
EOF;
        $arrEmail = array(
            'strSubject' => $strSubject, 
            'strContent' => $strContent
        );
        return $arrEmail;
    }
    
    private function getEmailBlank () {
        $strSubject = 'Blank';
        $strContent = "{{content}}";
        $arrEmail = array(
            'strSubject' => $strSubject, 
            'strContent' => $strContent
        );
        return $arrEmail;
    }
    
    private function getEmailAccountActivation () {
        $strSubject = 'Potwierdzenie założenia konta';
        $strContent = <<<EOF
Witaj, {{display_name}} !<br />
<br />
Twoje konto zostało założone, musisz je tylko potwierdzić. Aby tego dokonać kliknij w link aktywacyjny znajdujący się poniżej:<br />
<br />
<a href='{{url}}'>{{url}}</a>
EOF;
        $arrEmail = array(
            'strSubject' => $strSubject, 
            'strContent' => $strContent
        );
        return $arrEmail;
    }

    private function getEmailSubscriptionConfirmation () {
        $strSubject = 'Subskrypcja newslettera';
        $strContent = <<<EOF
Witaj,<br />
<br />
Ten adres e-mail został zapisany na listę subskrybentów newslettera. Aby potwierdzić poprawność adresu kliknij w link poniżej. Jeśli nie możesz go kliknąć - skopiuj i wklej do paska adresu Twojej przeglądarki internetowej.<br />
<br />
<a href='{{url}}'>{{url}}</a>
EOF;
        $arrEmail = array(
            'strSubject' => $strSubject,
            'strContent' => $strContent
        );
        return $arrEmail;
    }
    
    
    
    
    private function footer () {
        $strFooter = <<<EOF
<br />
<br />
Pozdrawiamy<br />
imgED
EOF;
        return $strFooter;
    }
    
    
}