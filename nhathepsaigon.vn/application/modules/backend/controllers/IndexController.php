<?php

/**
 * @author      :   PhongTX
 * @name        :   IndexController
 * @copyright   :   Fpt Online
 * @todo        :   Index Controller
 */
class Backend_IndexController extends Zend_Controller_Action
{

    public function init()
    {

    }

    /**
     * @todo - Core backend home page
     * @author PhongTX
     */
    public function indexAction()
    {
        //Check permission - Get menu by menu code
        $arrMenuByMenuCode = Core_Model_Menus::getDetailMenuByMenuCode($this->view->moduleName.'_'.$this->view->controllerName.'_'.$this->view->actionName);
        if(!in_array($arrMenuByMenuCode['menu_id'],$this->view->user['permission'][PERMISSION_VIEW])){
            $this->_redirect(SITE_URL . '/backend/forbidden');
        }
        //Get category id
        $intCategoryId = (int)$this->_request->getParam('intCategoryId', 0);
        /*End check permission*/
        $arrParams = array(
            'intCategoryType' => 1,//1: News category, 2: Product category
            'intParentId' => NULL,
            'intStatus' => '1,2',
            'intOffset' => NULL,
            'intLimit' => NULL,
        );
        //Get list category
        $arrCategories = Core_Model_Categories::getListCategory($arrParams);
        //Call recursive function
        Core_Global::recursive($arrCategories['data'], 'category_id', 0, 0, $arrResult);
        $arrParams = array(
            'intTime' => time(),
            'intCategoryId' => $intCategoryId
        );
        $arrReturn = Core_Model_Articles::getListTopstory($arrParams);
        //Assign to view
        $this->view->assign(array(
                'arrData' => $arrReturn,
                'arrCate' => $arrResult,
                'intCategoryId' => $intCategoryId
            )
        );
        //Set title
        $this->view->headTitle()->append('NEWS EDITOR');
        //append script
        $this->view->headScript()->appendFile(STATIC_URL . '/be/js/index/index.js');
    }

    /**
     * @author      :   PhongTX
     * @name    :   ajaxDelAction
     */
    public function ajaxDelAction()
    {
        //disable layout + render
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        //Get $id
        $id = $this->_request->getParam('intId', 0);
        $intCategoryId = $this->_request->getParam('intCategoryId', 0);
        //Get Name
        $strName = $this->_request->getParam('strName', '');
        $arrReturn = array('error' => 1, 'msg' => 'Xóa bài viết [' . $strName . '] khỏi danh sách topstory không thành công.');
        if ($id > 0) {
            //Update article
            $arrParams = array(
                'intId' => $id,
                'intCategoryId' => $intCategoryId
            );
            $isUpdate = Core_Model_Articles::deleteTopstoryById($arrParams);
            if ($isUpdate > 0) {
                $arrReturn['error'] = 0;
                $arrReturn['msg'] = 'Xóa bài viết [' . $strName . '] khỏi danh sách topstory thành công.';
            }
        }
        echo Zend_Json::encode($arrReturn);
        exit;
    }

    /**
     * @todo - Action ajaxGetListArticlesAction
     * @author PhongTX
     */
    public function ajaxGetListArticlesAction()
    {
        //disable layout + render
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $arrStatus = array(
            STATUS_DA_DUYET => 'Đã duyệt',
            STATUS_CHO_DUYET => 'Chờ duyệt',
            STATUS_LUU_NHAP => 'Lưu nháp'
        );
        if ($this->_request->isPost()) {
            $total = $this->_request->getParam('total',0);
            $strId = $this->_request->getParam('strId','');
            $intCategoryId = $this->_request->getParam('intCategoryId',0);
            if($strId != ''){
                $arrId = explode(',',$strId);
            }else{
                $arrId = array();
            }
            if($intCategoryId != 0) {
                $arrCateTemp[] = $intCategoryId;
                if (!empty($this->view->arrCategories[$intCategoryId]['child'])) {
                    foreach ($this->view->arrCategories[$intCategoryId]['child'] as $row1) {
                        $arrCateTemp[] = $row1['category_id'];
                    }
                }
            }
            //Define arrParams
            $arrParams = array(
                'intCategoryId' => $intCategoryId!=0?implode(',',$arrCateTemp):NULL,
                'strStatus' => 1,
                'intOffset' => 0,
                'intLimit' => 10000
            );
            $arrResult = array('data' => array(),'total' => 0);
            //Call function getListArticle
            $arrReturn = Core_Model_Articles::getListArticle($arrParams);
            if(!empty($arrReturn['data'])){
                $i = 0;
                foreach($arrReturn['data'] as $row){
                    if(!in_array($row['article_id'],$arrId)){
                        $arrResult['data'][] = $row;
                        $arrResult['total'] = $arrResult['total'] + 1;
                        $i++;
                    }
                }
            }
            // Assign to view
            $this->view->assign(array(
                'arrData' => $arrResult,
                'arrStatus' => $arrStatus,
                'total' => $total,
                'intCategoryId' => $intCategoryId
            ));
            //Render view
            $response['html'] = $this->view->render('index/get-list-articles.phtml');
            //Return Json
            echo Zend_Json::encode($response);
            exit;
        }
    }

    /**
     * @author      :   PhongTX
     * @name    :   ajaxDelAction
     */
    public function ajaxAddTopstoryAction()
    {
        //disable layout + render
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        //Request params
        $arrParamsRequest = $this->_request->getParams();
        if(isset($arrParamsRequest['articleid']) && !empty($arrParamsRequest['articleid'])){
            $i = $arrParamsRequest['intTotal'] + 1;
            foreach($arrParamsRequest['articleid'] as $row){
                Core_Model_Articles::insertTopstory(array('intArticleId' => $row, 'intCategoryId' => $arrParamsRequest['intCategoryId'], 'intDisplayOrder' => $i, 'intUserId' => $this->view->user['user_id']));
                $i++;
            }
        }
        $lab = 'topstory';
        if($arrParamsRequest['intCategoryId'] > 0){
            $lab = 'danh mục trang home';
        }
        $arrReturn['error'] = 0;
        $arrReturn['msg'] = 'Thêm bài viết vào '.$lab.' thành công';
        echo Zend_Json::encode($arrReturn);
        exit;
    }

    /**
     * @todo - Action ajaxUpdate
     * @author PhongTX
     */
    public function ajaxUpdateAction()
    {
        //disable layout + render
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->_request->isPost()) {
            $arrReturn = array(
                'error' => 1,
                'msg' => 'Có lỗi xảy ra'
            );
            //Get params
            $arrParamsRequest = $this->_request->getParams();
            if(!empty($arrParamsRequest['order'])){
                foreach($arrParamsRequest['order'] as $key => $row){
                    $arrParams = array(
                        'intArticleId' => $row,
                        'intOrder' => $key + 1
                    );
                    Core_Model_Articles::updateTopstoryById($arrParams);
                }
            }
            $arrReturn['error'] = 0;
            $arrReturn['msg'] = 'Thay đổi vị trí thành công';
            //Return Json
            echo Zend_Json::encode($arrReturn);
            exit;
        }
    }

}

?>