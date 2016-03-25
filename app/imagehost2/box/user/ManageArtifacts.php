<?php namespace imagehost2\box\user;

use webcitron\Subframe\Response;
use backend\user\UserController;
use webcitron\Subframe;
use webcitron\Subframe\Redirect;
use backend\artifact\ArtifactController;
use webcitron\Subframe\JsController;
use backend\user\model\UserModel;
use backend\artifact\model\ArtifactListModel;
use backend\artifact\model\ArtifactListOptionsModel;

class ManageArtifacts extends \webcitron\Subframe\Box {
    
    private $numPageNo = 1;
    private $strSort = '';
    private $numArtifactsPerPage = 25;
    
    public function __construct ($strSortType, $numPageNo = 1) {
        $this->numPageNo = $numPageNo;
        $this->strSort = $strSortType;
    }
    
    public function launch () {
        JsController::runJs();
        
        $objUserModel = new UserModel();
        $arrCurrentUser = $objUserModel->getLoggedUser();
        
        $objListOptions = new ArtifactListOptionsModel(1);
        $objListOptions->setOrder(array(
            $objListOptions->translateOrderSlugToId($this->strSort)
        ));
        $objListOptions->setLimit(9999);
        $objListOptions->addAuthorAccount($arrCurrentUser['id']);
        
        $objModelList = new ArtifactListModel();
        $arrArtifacts = $objModelList->getArtifacts($objListOptions);
            
        return Response::html($this->render(array(
            'strCurrentSort' => $this->strSort, 
            'arrArtifacts' => $arrArtifacts 
        )));
        
    }
    
    public function OLDlaunch () {
        JsController::runJs();
        
        $arrArtifactsView = array();
        $arrPagination = array();
        $arrPages = array();
        
        $arrLoggedUser = UserController::getLoggedUser();
        if (empty($arrLoggedUser['result'])) {
            Redirect::route('User::login');
        } 
        
        $numOrder = 0;
        if (!empty($this->strSort)) {
            switch ($this->strSort) {
                case 'najpopularniejsze':
                    $numOrder = 1;
                    break;
                case 'najbardziej-lubiane':
                    $numOrder = 2;
                    break;
                case 'alfabetyczne':
                    $numOrder = 3;
                    break;
            }
        }
        
        $arrListsParams = array(
            'numUserId' => $arrLoggedUser['result']['user_id'], 
            'numOrder' => $numOrder
        );
        
        $arrArtifacts = ArtifactController::getListDefault($this->numArtifactsPerPage, $this->numPageNo, $arrListsParams);
        
        if (!empty($arrArtifacts)) {
            $numLp = $this->numArtifactsPerPage * $this->numPageNo - $this->numArtifactsPerPage + 1;
            foreach ($arrArtifacts['result'] as $arrArtifact) {
                
                $arrArtifactRow = array();
                $arrArtifactRow['numId'] = $arrArtifact['id'];
                $arrArtifactRow['strImageSrc'] = $arrArtifact['thumb_url'];
                $arrArtifactRow['strTitle'] = stripslashes($arrArtifact['title']);
                $arrArtifactRow['strAddDate'] = $arrArtifact['add_date'];
                $arrArtifactRow['strUrl'] = $arrArtifact['href'];
                $arrArtifactRow['numShowsCountFake'] = $arrArtifact['shows_count_fake'];
                $arrArtifactRow['numLikes'] = $arrArtifact['likes'];
                $arrArtifactsView[] = $arrArtifactRow;
                $numLp++;
            }
        }
        
        $arrUserArtifactsCount = ArtifactController::getResultsCount($arrListsParams);   
        if ($arrUserArtifactsCount['result'] > $this->numArtifactsPerPage) {
            // need pagination
            $numMaxPageNo = ceil($arrUserArtifactsCount['result'] / $this->numArtifactsPerPage);
            
            if ($this->numPageNo > 1) {
                $arrPrev = array(
                    'strUrl' => Subframe\Url::route('User::artifacts', array('pagination_page' => $this->numPageNo-1))
                );
            } else {
                $arrPrev = array('numDisabled' => 1, 'strUrl' => 'javascript:void(0);');
            }
            
            if ($numMaxPageNo > $this->numPageNo) {
                $arrNext = array(
                    'strUrl' => Subframe\Url::route('User::artifacts', array('pagination_page' => $this->numPageNo+1))
                );
            } else {
                $arrNext = array('numDisabled' => 1, 'strUrl' => 'javascript:void(0);');
            }
            
            $numPagesStart = $this->numPageNo - 5;
            $numPagesEnd = $this->numPageNo + 5;
            for ($numPage = $numPagesStart; $numPage <= $numPagesEnd; $numPage++) {
                if ($numPage < 1 || $numPage > $numMaxPageNo) {
                    continue;
                }
                $arrCurrentPage = array(
                    'strUrl' => Subframe\Url::route('User::artifacts', array('pagination_page' => $numPage)), 
                    'strLabel' => $numPage
                );
                
                if ($numPage === $this->numPageNo) {
                    $arrCurrentPage['numActive'] = 1;
                }
                
                $arrPages[] = $arrCurrentPage;
            }
            
            
            $arrPagination = array(
                'numCurrentPage' => $this->numPageNo, 
                'numTotalItems' => $arrUserArtifactsCount['result'], 
                'numTotalPages' => $numMaxPageNo, 
                'arrPrev' => $arrPrev, 
                'arrNext' => $arrNext
            );
//            echo '<pre>';
//            print_r($arrPagination);
//            exit();
        }
        
        return Response::html($this->render(array(
            'strCurrentSort' => $this->strSort, 
            'arrPages' => $arrPages, 
            'arrArtifactsView' => $arrArtifactsView, 
            'arrPagination' => $arrPagination
        )));
    }
    
}