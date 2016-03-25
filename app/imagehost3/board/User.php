<?php namespace imagehost3\board;

use \webcitron\Subframe\Board;
use \webcitron\Subframe\Response;
use backend\user\UserController;

class User extends Board {
    
    public function myUploads ($strSortType = '', $numPageNo = 1) {
        
        $this->redirectIfNotLogged();
        $numPageNo = intval(trim($numPageNo, '/'));
        
        $objLayout = new \imagehost3\layout\Standard();
        $objLayout->addBoxes('main', array(
//            new \imagehost2\box\PageHeader('Moje obrazki'), 
            new \imagehost3\box\user\ManageArtifacts($strSortType, $numPageNo)
        ));
        $objLayout->addBoxes('top', new \imagehost3\box\Topbar());
        $objLayout->addBoxes('foot', new \imagehost3\box\Footer());
        $objLayout->addBoxes('foot', new \imagehost3\box\PuppiesDetector());
        
        return Response::html($objLayout->render(array(
            'title' => 'Moje obrazki - imgED', 
            'robots' => 'noindex, follow, noarchive', 
            'googlebot' => 'noindex, follow, noarchive, nosnippet'
        )));
    }
    
    public function forgotPassword () {
        $this->redirectIfLogged();
     
        $objLayout = new \imagehost3\layout\Standard();
        
        $objLayout->addBoxes('main', array(
//            new \imagehost2\box\PageHeader('Odzyskiwanie dostępu do konta'), 
            new \imagehost3\box\user\ForgotPassword(), 
//            new \imagehost2\box\puppy\TopLayer()
        ));
        $objLayout->addBoxes('top', new \imagehost3\box\Topbar());
        $objLayout->addBoxes('foot', new \imagehost3\box\Footer());
        $objLayout->addBoxes('foot', new \imagehost3\box\PuppiesDetector());
        
        return Response::html($objLayout->render(array(
            'title' => 'Przypomnienie hasła - imgED', 
            'robots' => 'noindex, follow, noarchive', 
            'googlebot' => 'noindex, follow, noarchive, nosnippet'
        )));
    }
  
    public function logout () {
        $this->redirectIfNotLogged();
        
        $objLayout = new \imagehost3\layout\Standard();
        
        $objUserModel = new \backend\user\model\UserModel();
        $objUserModel->logout();
        
        $objLayout->addBoxes('main', array(
            new \imagehost3\box\user\Logout()
        ));
        $objLayout->addBoxes('top', new \imagehost3\box\Topbar());
        $objLayout->addBoxes('foot', new \imagehost3\box\Footer());
        $objLayout->addBoxes('foot', new \imagehost3\box\PuppiesDetector());
        
        return Response::html($objLayout->render(array(
            'title' => 'Zostałeś poprawnie wylogowany - imgED', 
            'robots' => 'noindex, follow, noarchive', 
            'googlebot' => 'noindex, follow, noarchive, nosnippet'
        )));
    }
    
    public function account () {
        $this->redirectIfNotLogged();
        
        $objLayout = new \imagehost3\layout\Standard();
        $objLayout->addBoxes('main', array(
//            new \imagehost2\box\PageHeader('Twoje konto'), 
            new \imagehost3\box\user\Account()
        ));
        $objLayout->addBoxes('top', new \imagehost3\box\Topbar());
        $objLayout->addBoxes('foot', new \imagehost3\box\Footer());
        $objLayout->addBoxes('foot', new \imagehost3\box\PuppiesDetector());
        
        return Response::html($objLayout->render(array(
            'title' => 'Twoje konto - imgED', 
            'robots' => 'noindex, follow, noarchive', 
            'googlebot' => 'noindex, follow, noarchive, nosnippet'
        )));
    }
    
    public function login() {
        $this->redirectIfLogged();
        
        $objLayout = new \imagehost2\layout\Standard();
        
        $objLayout->addBoxes('main', array(
            new \imagehost2\box\PageHeader('Logowanie do konta'), 
            new \imagehost2\box\user\Login(), 
//            new \imagehost2\box\puppy\TopLayer()
        ));
        $objLayout->addBoxes('top', new \imagehost3\box\Topbar());
        $objLayout->addBoxes('foot', new \imagehost3\box\Footer());
        $objLayout->addBoxes('foot', new \imagehost3\box\PuppiesDetector());
        
        return Response::html($objLayout->render(array(
            'title' => 'Zaloguj się do swojego konta - imgED', 
            'robots' => 'noindex, follow, noarchive', 
            'googlebot' => 'noindex, follow, noarchive, nosnippet'
        )));
    }
    
    public function register() {
        $this->redirectIfLogged();
        
        $objLayout = new \imagehost3\layout\Standard();
        
        $objLayout->addBoxes('main', array(
//            new \imagehost2\box\PageHeader('Zakładanie darmowego konta'), 
            new \imagehost3\box\user\Register(), 
//            new \imagehost2\box\puppy\TopLayer()
        ));
        $objLayout->addBoxes('top', new \imagehost3\box\Topbar());
        $objLayout->addBoxes('foot', new \imagehost3\box\Footer());
        $objLayout->addBoxes('foot', new \imagehost3\box\PuppiesDetector());
        
        return Response::html($objLayout->render(array(
            'title' => 'Załóż nowe konto - imgED', 
            'robots' => 'noindex, follow, noarchive', 
            'googlebot' => 'noindex, follow, noarchive, nosnippet'
        )));
    }
    
    public function accountActivation ($strActivationHash) {
        $this->redirectIfLogged();
        
        $objCtr = new UserController();
        $boolIsOk = $objCtr->confirmEmail($strActivationHash)['result'];
        
        $strTargetUrl = \webcitron\Subframe\Url::route('Homepage');
         
        if ($boolIsOk === true) {
            $strTargetUrl .= '#activation-ok';
        } else {
            $strTargetUrl .= '#activation-nok';
        }
         
        \webcitron\Subframe\Redirect::url($strTargetUrl);
    }
    
    private function redirectIfLogged () {
        $objSession = \backend\Session::getInstance('imagehost_user');
        $numLoggedId = intval($objSession->getValue('imagehost_user_id', 0));
        
        if ($numLoggedId > 0) {
            \webcitron\Subframe\Redirect::route('Homepage');
        }
    }
    
    
    private function redirectIfNotLogged () {
        $objSession = \backend\Session::getInstance('imagehost_user');
        $numLoggedId = intval($objSession->getValue('imagehost_account_id', 0));
        
        if ($numLoggedId === 0) {
            \webcitron\Subframe\Redirect::route('Homepage');
        }
    }
    
    
    private function force404 () {
        $objLayout = new \imagehost2\layout\Standard();
        $objLayout->addBoxes('topbar', array(
            new \imagehost2\box\Topbar()
        ));
        $objLayout->addBoxes('main', array(
            new \imagehost2\box\files\NotFound()
        ));
        $objResponse = Response::html($objLayout->render());
        $objResponse->setStatus(404);
        return $objResponse;
    }
    
}