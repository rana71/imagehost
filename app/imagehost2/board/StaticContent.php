<?php namespace imagehost2\board;

use \webcitron\Subframe\Board;
use \webcitron\Subframe\Response;

class StaticContent extends Board {
  
    public function agreements() {
        $objLayout = new \imagehost2\layout\Standard();
        
        $objLayout->addBoxes('main', array(
            new \imagehost2\box\PageHeader('Regulamin serwisu'), 
            new \imagehost2\box\staticContent\Agreements()
        ));
        
        return $this->getResponse($objLayout, 'Regulamin - imgED');
    }
    
    public function career () {
        $objLayout = new \imagehost2\layout\Standard();
        $objLayout->addBoxes('main', array(
            new \imagehost2\box\PageHeader('Kariera'), 
            new \imagehost2\box\staticContent\Career()
        ));
        
        return $this->getResponse($objLayout, 'Kariera - imgED');
    }
    
    public function about () {
        $objLayout = new \imagehost2\layout\Standard();
        $objLayout->addBoxes('main', array(
            new \imagehost2\box\PageHeader('O nas'), 
            new \imagehost2\box\staticContent\About()
        ));
        
        return $this->getResponse($objLayout, 'Polityka prywatności - imgED');
    }
    
    public function privacyPolicy () {
        $objLayout = new \imagehost2\layout\Standard();
        $objLayout->addBoxes('main', array(
            new \imagehost2\box\PageHeader('Polityka prywatności'), 
            new \imagehost2\box\staticContent\PrivacyPolicy(), 
            new \imagehost2\box\puppy\Area('test')
        ));
        
        return $this->getResponse($objLayout, 'Polityka prywatności - imgED');
    }
    
    public function advertisement () {
        $objLayout = new \imagehost2\layout\Standard();
        $objLayout->addBoxes('main', array(
            new \imagehost2\box\PageHeader('Reklama w imgED'), 
            new \imagehost2\box\staticContent\AdvFeedback()
        ));
        
        return $this->getResponse($objLayout, 'Reklama w serwisie - imgED');
    }
    
    public function contact() {
        $objLayout = new \imagehost2\layout\Standard();
        
        
        $objLayout->addBoxes('main', array(
            new \imagehost2\box\PageHeader('Kontakt'), 
            new \imagehost2\box\staticContent\Contact()
        ));
        
        return $this->getResponse($objLayout, 'Kontakt - imgED');
    }
    
    public function contactCareer() {
        $objLayout = new \imagehost2\layout\Standard();
        
        
        $objLayout->addBoxes('main', array(
            new \imagehost2\box\PageHeader('Kontakt - kariera'), 
            new \imagehost2\box\staticContent\Contact('Redaktor zgłasza się do pracy')
        ));
        
        return $this->getResponse($objLayout, 'Kontakt - kariera - imgED');
    }
    
    public function AccountGuestCompare () {
        $objLayout = new \imagehost2\layout\Standard();
        
        
        $objLayout->addBoxes('main', array(
            new \imagehost2\box\PageHeader('Porównanie kont'), 
            new \imagehost2\box\staticContent\AccountGuestCompare()
        ));
        
        return $this->getResponse($objLayout, 'Porównanie kont - imgED');
    }
    
    private function getResponse ($objLayout, $strTitle) {
        $objLayout->addBoxes('topbar', array(
            new \imagehost2\box\Topbar()
        ));
        
        $objResponse = Response::html($objLayout->render(array(
            'title' => $strTitle, 
            'robots' => 'noindex, nofollow, noarchive', 
            'googlebot' => 'noindex, nofollow, noarchive, nosnippet'
        )));
        return $objResponse;
    }
    
    
    
}