<?php namespace backend\cron;

//use \backend\GoogleSitemap2;
use \backend\GoogleSitemap3;
//use GoogleSitemap2\Image;
use backend\tag\model\TagModel;
use backend\artifact\model\ArtifactModel;
use backend\user\model\UserModel;
use webcitron\Subframe\Url;
use backend\searcher\model\QueryModel;

class SitemapXml {
    
    private $objSitemap = null;
    
    public function __construct () {
        $this->objSitemap = new GoogleSitemap3();
        $this->objSitemap->strRootDir = dirname(__FILE__).'/../../../public_html';
        $this->objSitemap->strWorkingDomain = \webcitron\Subframe\Application::url();
    }
    
    public function recreate () {
        $numTimeStart = $this->microtimeFloat();
        $this->objSitemap->prepare();

        $this->objSitemap->addUrl(Url::route('Homepage'), 0.7, GoogleSitemap3::CHANGE_DAILY);
        $this->objSitemap->addUrl(Url::route('User::login', 0.2, GoogleSitemap3::CHANGE_MONTHLY));
        $this->objSitemap->addUrl(Url::route('User::register', 0.2, GoogleSitemap3::CHANGE_MONTHLY));
        $this->objSitemap->addUrl(Url::route('User::forgotPassword', 0.2, GoogleSitemap3::CHANGE_MONTHLY));
        $this->addUsersListings(10000, 0.7, GoogleSitemap3::CHANGE_WEEKLY);
        
        $this->objSitemap->addUrl(Url::route('StaticContent::about', 0.2, GoogleSitemap3::CHANGE_MONTHLY));
        $this->objSitemap->addUrl(Url::route('StaticContent::career', 0.2, GoogleSitemap3::CHANGE_MONTHLY));
        $this->objSitemap->addUrl(Url::route('StaticContent::advertisement', 0.2, GoogleSitemap3::CHANGE_MONTHLY));
        $this->objSitemap->addUrl(Url::route('StaticContent::privacyPolicy', 0.2, GoogleSitemap3::CHANGE_MONTHLY));
        $this->objSitemap->addUrl(Url::route('StaticContent::agreements', 0.2, GoogleSitemap3::CHANGE_MONTHLY));
        $this->objSitemap->addUrl(Url::route('StaticContent::contact', 0.2, GoogleSitemap3::CHANGE_MONTHLY));
        $this->objSitemap->addUrl(Url::route('StaticContent::contactCareer', 0.2, GoogleSitemap3::CHANGE_MONTHLY));
        $this->objSitemap->addUrl(Url::route('StaticContent::AccountGuestCompare', 0.2, GoogleSitemap3::CHANGE_MONTHLY));
        
        $this->objSitemap->addUrl(Url::route('Upload::index', 0.4, GoogleSitemap3::CHANGE_MONTHLY));
        $this->objSitemap->addUrl(Url::route('Upload::memeGenerator', 0.4, GoogleSitemap3::CHANGE_MONTHLY));
        
        $this->objSitemap->addUrl(Url::route('TagsList::index'), 0.5, GoogleSitemap3::CHANGE_DAILY);
        $this->objSitemap->addUrl(Url::route('QueriesList::index', GoogleSitemap3::CHANGE_DAILY));
        $this->objSitemap->addUrl(Url::route('Listing::onHomepage', GoogleSitemap3::CHANGE_DAILY));
        $this->objSitemap->addUrl(Url::route('Listing::latestStories', GoogleSitemap3::CHANGE_DAILY));
        $this->objSitemap->addUrl(Url::route('Listing::latestImported', GoogleSitemap3::CHANGE_DAILY));
        
        $this->addTags(10000, 0.7, GoogleSitemap3::CHANGE_DAILY);
        $this->addSearchQueries(10000, 0.7, GoogleSitemap3::CHANGE_DAILY);
        $this->addArtifacts(10000, 0.8, GoogleSitemap3::CHANGE_WEEKLY);
        
        $this->objSitemap->finish();
        $numSecondsTaken = $this->microtimeFloat() - $numTimeStart;
        
        $objMail = new \backend\SystemMail('DeveloperInfo');
        $objMail->setVariable('script', 'SitemapXml::recreate');
        $objMail->setVariable('appurl', \webcitron\Subframe\Application::url());
        $objMail->setVariable('seconds', $numSecondsTaken);
        $objMail->addRecipient('a.mackiewicz@webcitron.eu');
        $objMail->addRecipient('bberlinski@gmail.com');
        $objMail->send();
    }
    
    private function addUsersListings ($numPartLimit, $numPrior, $numChangeFreq) {
        $objUserModel = new UserModel();
        $numOffset = 0;
        do {
            
            $arrUsers = $objUserModel->getUsers($numOffset, $numPartLimit);
            if (!empty($arrUsers)) {
                foreach ($arrUsers as $arrUser) {
                    $strUrl = Url::route('Listing::userUploads', array('username' => $arrUser['display_name']));
                    $this->objSitemap->addUrl($strUrl, $numPrior, $numChangeFreq);
                }
            }
            if (count($arrUsers) < $numPartLimit) {
                break;
            }
            $numOffset += $numPartLimit;
        } while (true);
        
        unset ($arrUsers, $objUserModel);
    }
    
    private function addTags ($numPartLimit, $numPrior, $numChangeFreq) {
        $objTagModel = new TagModel();
        $numOffset = 0;
        do {
            
            $arrTags = $objTagModel->getTagsWihmMinimumElementsCount(3, $numOffset, $numPartLimit);
            if (!empty($arrTags)) {
                foreach ($arrTags as $arrTag) {
                    $strUrl = Url::route('Listing::tag', array('tag' => $arrTag['slug']));
                    $this->objSitemap->addUrl($strUrl, $numPrior, $numChangeFreq);
                }
            }

            if (count($arrTags) < $numPartLimit) {
                break;
            }
            $numOffset += $numPartLimit;
        } while (true);
    }
    
    private function addSearchQueries ($numPartLimit, $numPrior, $numChangeFreq) {
        $objQueryModel = new QueryModel();
        $numOffset = 0;
        do {
            
            $arrQueries = $objQueryModel->getNotEmptyQueries($numOffset, $numPartLimit);
            if (!empty($arrQueries)) {
                foreach ($arrQueries as $arrQuery) {
                    $strUrl = Url::route('Listing::query', array('query' => $arrQuery['slug']));
                    $this->objSitemap->addUrl($strUrl, $numPrior, $numChangeFreq);
                }
            }

            if (count($arrQueries) < $numPartLimit) {
                break;
            }
            $numOffset += $numPartLimit;
        } while (true);
    }
    
    private function microtimeFloat () {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
    
    private function addArtifacts ($numPartLimit, $numPrior, $numChangeFreq) {
        $objArtifactModel = new ArtifactModel();
        $numMaxId = 0;
        $numLoopNo = 0;
        do {
            $numLoopNo++;
//            echo 'do artifacts lopp '.$numLoopNo.', id > '.$numMaxId.PHP_EOL;
            $arrArtifacts = $objArtifactModel->getCleanArtifactsIdGreaterThan($numMaxId, $numPartLimit);
            $numFirstId = $arrArtifacts[0]['id'];
            $numLastId = $arrArtifacts[count($arrArtifacts)-1]['id'];
            $arrItemsImages = array();
            $arrImagesMessed = $objArtifactModel->getImagesElementsToItemIdBetween($numFirstId, $numLastId);

            foreach ($arrImagesMessed as $arrImageMess) {
                $arrItemsImages[$arrImageMess['item_id']][] = $arrImageMess;
            }
            unset ($arrImagesMessed);
            
            if (!empty($arrArtifacts)) {
//                echo '6.'.$numLoopNo.' START: '.((memory_get_usage() - $this->numMemoryStart) / (1024*1024)).PHP_EOL;
                foreach ($arrArtifacts as $arrArtifact) {
                    $strUrl = Url::route('Details', array('slug' => $arrArtifact['slug'], 'id' => $arrArtifact['id']));
                    if (isset($arrItemsImages[$arrArtifact['id']])) {
                        $this->objSitemap->addUrl($strUrl, $numPrior, $numChangeFreq, $arrItemsImages[$arrArtifact['id']]);
                    } else {
                        $this->objSitemap->addUrl($strUrl, $numPrior, $numChangeFreq);
                    }
                }
                $numMaxId = $arrArtifact['id'];
//                echo '6.'.$numLoopNo.' END: '.((memory_get_usage() - $this->numMemoryStart) / (1024*1024)).PHP_EOL;
            }

            if (count($arrArtifacts) < $numPartLimit) {
                break;
            }
        } while (true);
    }
    
    
    
}
