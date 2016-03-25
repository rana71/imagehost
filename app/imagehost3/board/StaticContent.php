<?php namespace imagehost3\board;

use \webcitron\Subframe\Board;
use \webcitron\Subframe\Response;
use \webcitron\Subframe\CssController;

class StaticContent extends Board {
  
    public function __construct () {
        $objCssController = CssController::getInstance();
        $objCssController->forceCssFile('StaticContent');
    }
    
    public function agreements() {
        $objLayout = new \imagehost3\layout\Standard();
        
        $objLayout->addBoxes('main', array(
            new \imagehost3\box\staticContent\Agreements()
        ));
        $objLayout->addBoxes('top', new \imagehost3\box\Topbar());
        $objLayout->addBoxes('foot', new \imagehost3\box\Footer());
        $objLayout->addBoxes('foot', new \imagehost3\box\PuppiesDetector());
        
        return $this->getResponse($objLayout, 'Regulamin - imgED');
    }
    
    public function career () {
        $objLayout = new \imagehost3\layout\Standard();
        $objLayout->addBoxes('main', array(
            new \imagehost3\box\staticContent\Career()
        ));
        $objLayout->addBoxes('top', new \imagehost3\box\Topbar());
        $objLayout->addBoxes('foot', new \imagehost3\box\Footer());
        $objLayout->addBoxes('foot', new \imagehost3\box\PuppiesDetector());
        
        return $this->getResponse($objLayout, 'Kariera - imgED');
    }
    
    public function about () {
        $objLayout = new \imagehost3\layout\Standard();
        $objLayout->addBoxes('main', array(
            new \imagehost3\box\staticContent\About()
        ));
        $objLayout->addBoxes('top', new \imagehost3\box\Topbar());
        $objLayout->addBoxes('foot', new \imagehost3\box\Footer());
        $objLayout->addBoxes('foot', new \imagehost3\box\PuppiesDetector());
        
        return $this->getResponse($objLayout, 'Polityka prywatności - imgED');
    }
    
    public function privacyPolicy () {
        $objLayout = new \imagehost3\layout\Standard();
        $objLayout->addBoxes('main', array(
//            new \imagehost2\box\PageHeader('Polityka prywatności'), 
            new \imagehost3\box\staticContent\PrivacyPolicy(), 
            new \imagehost3\box\puppy\Area('test')
        ));
        $objLayout->addBoxes('top', new \imagehost3\box\Topbar());
        $objLayout->addBoxes('foot', new \imagehost3\box\Footer());
        $objLayout->addBoxes('foot', new \imagehost3\box\PuppiesDetector());
        
        return $this->getResponse($objLayout, 'Polityka prywatności - imgED');
    }
    
    public function advertisement () {
        $objLayout = new \imagehost3\layout\Standard();
        $objLayout->addBoxes('main', array(
            new \imagehost3\box\staticContent\AdvFeedback()
        ));
        $objLayout->addBoxes('top', new \imagehost3\box\Topbar());
        $objLayout->addBoxes('foot', new \imagehost3\box\Footer());
        $objLayout->addBoxes('foot', new \imagehost3\box\PuppiesDetector());
        
        return $this->getResponse($objLayout, 'Reklama w serwisie - imgED');
    }
    
    public function contact() {
        $objLayout = new \imagehost3\layout\Standard();
        
        
        $objLayout->addBoxes('main', array(
            new \imagehost3\box\staticContent\Contact()
        ));
        $objLayout->addBoxes('top', new \imagehost3\box\Topbar());
        $objLayout->addBoxes('foot', new \imagehost3\box\Footer());
        $objLayout->addBoxes('foot', new \imagehost3\box\PuppiesDetector());
        
        return $this->getResponse($objLayout, 'Kontakt - imgED');
    }
    
    public function contactCareer() {
        $objLayout = new \imagehost3\layout\Standard();
        
        
        $objLayout->addBoxes('main', array(
//            new \imagehost2\box\PageHeader('Kontakt - kariera'), 
            new \imagehost3\box\staticContent\Contact('Redaktor zgłasza się do pracy')
        ));
        $objLayout->addBoxes('top', new \imagehost3\box\Topbar());
        $objLayout->addBoxes('foot', new \imagehost3\box\Footer());
        $objLayout->addBoxes('foot', new \imagehost3\box\PuppiesDetector());
        
        return $this->getResponse($objLayout, 'Kontakt - kariera - imgED');
    }
    
    public function AccountGuestCompare () {
        $objLayout = new \imagehost3\layout\Standard();
        
        
        $objLayout->addBoxes('main', array(
            new \imagehost3\box\staticContent\AccountGuestCompare()
        ));
        $objLayout->addBoxes('top', new \imagehost3\box\Topbar());
        $objLayout->addBoxes('foot', new \imagehost3\box\Footer());
        $objLayout->addBoxes('foot', new \imagehost3\box\PuppiesDetector());
        
        return $this->getResponse($objLayout, 'Porównanie kont - imgED');
    }
    
    private function getResponse ($objLayout, $strTitle) {
        
        $objResponse = Response::html($objLayout->render(array(
            'title' => $strTitle, 
            'robots' => 'noindex, nofollow, noarchive', 
            'googlebot' => 'noindex, nofollow, noarchive, nosnippet'
        )));
        return $objResponse;
    }
    
    
    
}