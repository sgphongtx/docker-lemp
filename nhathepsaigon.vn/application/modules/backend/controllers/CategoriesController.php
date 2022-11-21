<?php

/**
 * @author      :   PhongTX
 * @name        :   CategoriesController
 * @copyright   :   Fpt Online
 * @todo        :   Categories Controller
 */
class Backend_CategoriesController extends Zend_Controller_Action
{
    /**
     * @todo - Action init
     * @author PhongTX
     */
    public function init()
    {

    }

    /**
     * @todo - Action index
     * @author PhongTX
     */
    public function indexAction()
    {
        //Check permission - Get menu by menu code
        $arrMenuByMenuCode = Core_Model_Menus::getDetailMenuByMenuCode($this->view->moduleName.'_'.$this->view->controllerName.'_'.$this->view->actionName);
        if(!in_array($arrMenuByMenuCode['menu_id'],$this->view->user['permission'][PERMISSION_VIEW])){
            $this->_redirect(SITE_URL . '/backend/forbidden');
        }
        /*End check permission*/
        //Get params
        $arrParamsRequest = $this->_request->getParams();
        $arrParams = array(
            'intCategoryType' => (int)$arrParamsRequest['type'],
            'intParentId' => NULL,
            'intStatus' => '1,2',
            'intOffset' => NULL,
            'intLimit' => NULL,
        );
        //Get list category
        $arrReturn = Core_Model_Categories::getListCategory($arrParams);
        //Call recursive function
        Core_Global::recursive($arrReturn['data'], 'category_id', 0, 0, $arrResult);
        //Assign to view
        $this->view->assign(array(
                'arrData' => $arrResult,
                'intCategoryType' => (int)$arrParamsRequest['type']
            )
        );
        //Set title
        $this->view->headTitle()->append('Danh mục');
        //append script
        $this->view->headScript()->appendFile(STATIC_URL . '/library/js/jquery.validate.min.js')
            ->appendFile(STATIC_URL . '/library/js/jquery.number.min.js')
            ->appendFile(STATIC_URL . '/be/js/categories/add.js');
    }

    /**
     * @todo - Action add
     * @author PhongTX
     */
    public function ajaxAddAction()
    {
        //disable layout + render
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        if ($this->_request->isPost()) {
            //Get params
            $arrParamsRequest = $this->_request->getParams();
            $arrReturn = array(
                'error' => 1,
                'msg' => 'Có lỗi xảy ra',
                'intCategoryType' => $arrParamsRequest['intCategoryType']
            );
            $checkCategory = Core_Validate_Categories::checkCategory($arrParamsRequest);
            if ($checkCategory['error'] == 1) {
                echo Zend_Json::encode($checkCategory);
                exit;
            } else {
                //Add category
                if ($arrParamsRequest['intCategoryId'] == 0) {
                    // $arrParams insert category
                    $arrParams = array(
                        'intCategoryType' => $arrParamsRequest['intCategoryType'],
                        'intParentId' => $arrParamsRequest['intParentId'],
                        'strCateName' => trim(strip_tags($arrParamsRequest['strCateName'])),
                        'strCateCode' => Core_Utils::upperTolower(Core_Utils::setSeoLink(trim(strip_tags($arrParamsRequest['strCateName'])))),
                        'strCateLink' => Core_Utils::upperTolower($arrParamsRequest['strCateLink']),
                        'intDisplayOrder' => $arrParamsRequest['intDisplayOrder'],
                        'strMetaTitle' => trim(strip_tags($arrParamsRequest['strMetaTitle'])),
                        'strMetaDesc' => trim(strip_tags($arrParamsRequest['strMetaDesc'])),
                        'strMetaKeyword' => trim(strip_tags($arrParamsRequest['strMetaKeyword'])),
                        'intStatus' => $arrParamsRequest['intStatus'],
                        'intShowFolder' => $arrParamsRequest['intShowFolder']
                    );
                    // insert category
                    $intCategoryId = Core_Model_Categories::insertCategory($arrParams);
                    unset($arrParams);
                    if ($intCategoryId > 0) {
                        $arrReturn['error'] = 0;
                        $arrReturn['msg'] = 'Add category thành công';
                    }
                    //Update category
                } else {
                    $arrCategoryDetail = Core_Model_Categories::getDetailCategoryById($arrParamsRequest['intCategoryId']);
                    //Update category
                    $arrParams = array(
                        'intCategoryType' => NULL,
                        'intCategoryId' => $arrParamsRequest['intCategoryId'],
                        'intParentId' => $arrParamsRequest['intParentId'],
                        'strCateName' => trim(strip_tags($arrParamsRequest['strCateName'])),
                        'strCateCode' => Core_Utils::upperTolower(Core_Utils::setSeoLink(trim(strip_tags($arrParamsRequest['strCateName'])))),
                        'strCateLink' => Core_Utils::upperTolower($arrParamsRequest['strCateLink']),
                        'intDisplayOrder' => $arrParamsRequest['intDisplayOrder'],
                        'strMetaTitle' => trim(strip_tags($arrParamsRequest['strMetaTitle'])),
                        'strMetaDesc' => trim(strip_tags($arrParamsRequest['strMetaDesc'])),
                        'strMetaKeyword' => trim(strip_tags($arrParamsRequest['strMetaKeyword'])),
                        'intStatus' => $arrParamsRequest['intStatus'],
                        'intShowFolder' => $arrParamsRequest['intShowFolder']
                    );
                    //Update object
                    $isUpdate = Core_Model_Categories::updateCategory($arrParams);
                    if ($isUpdate > 0) {
                        $arrReturn['error'] = 0;
                        $arrReturn['msg'] = 'Update category thành công';
                    }
                }
            }
            echo Zend_Json::encode($arrReturn);
            exit;
        }
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
            $arrParams = array(
                'intCategoryType' => NULL,
                'intCategoryId' => $arrParamsRequest['intCateId'],
                'intParentId' => NULL,
                'strFullParent' => NULL,
                'strCateName' => NULL,
                'strCateCode' => NULL,
                'strCateLink' => NULL,
                'strMetaTitle' => NULL,
                'strMetaDesc' => NULL,
                'strMetaKeyword' => NULL,
                'intDisplayOrder' => NULL,
                'intStatus' => $arrParamsRequest['intStatus'],
                'intShowFolder' => NULL
            );
            $isUpdate = Core_Model_Categories::updateCategory($arrParams);
            if ($isUpdate) {
                $label = $arrParamsRequest['intStatus']==1?'Active':'Inactive';
                $arrReturn['error'] = 0;
                $arrReturn['msg'] = $label.' category [' . $arrParamsRequest['strName'] . '] thành công';
            }
            //Return Json
            echo Zend_Json::encode($arrReturn);
            exit;
        }
    }

    /**
     * @todo - Action ajaxDel
     * @author PhongTX
     */
    public function ajaxDelAction()
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
            $arrParams = array(
                'intCategoryType' => NULL,
                'intCategoryId' => $arrParamsRequest['intCateId'],
                'intParentId' => NULL,
                'strFullParent' => NULL,
                'strCateName' => NULL,
                'strCateCode' => NULL,
                'strCateLink' => NULL,
                'strMetaTitle' => NULL,
                'strMetaDesc' => NULL,
                'strMetaKeyword' => NULL,
                'intDisplayOrder' => NULL,
                'intStatus' => 0,//Del
                'intShowFolder' => NULL
            );
            $isUpdate = Core_Model_Categories::updateCategory($arrParams);
            if ($isUpdate) {
                $arrReturn['error'] = 0;
                $arrReturn['msg'] = 'Xóa category [' . $arrParamsRequest['strName'] . '] thành công';
            }
            //Return Json
            echo Zend_Json::encode($arrReturn);
            exit;
        }
    }

    /**
     * @todo - Action getDetailCategory
     * @author PhongTX
     */
    public function ajaxGetDetailCategoryAction()
    {
        //disable layout + render
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->_request->isPost()) {
            //Get intCategoryId
            $intCategoryId = $this->_request->getParam('intCategoryId', 0);
            //Get cate type
            $intCategoryType = $this->_request->getParam('intCategoryType', 0);
            //Define $arrCategoryDetail, $arrFolders
            $arrCategoryDetail = $arrFolders = $arrParentDetail = array();
            if ($intCategoryId != 0) {
                //Get cate detail
                $arrCategoryDetail = Core_Model_Categories::getDetailCategoryById($intCategoryId);
                if($arrCategoryDetail['parent_id'] > 0) {
                    //Get parent detail
                    $arrParentDetail = Core_Model_Categories::getDetailCategoryById($arrCategoryDetail['parent_id']);
                    $arrCategoryDetail['parent_name'] = $arrParentDetail['cate_name'];
                }else{
                    $arrCategoryDetail['parent_name'] = 'Chọn danh mục';
                }
            }else{
                $arrCategoryDetail['parent_name'] = 'Chọn danh mục';
            }
            //Get list category
            $arrCategories = Core_Model_Categories::getListCategory(array('intCategoryType' => $intCategoryType, 'intParentId' => NULL, 'intStatus' => 1, 'intOffset' => NULL, 'intLimit' => NULL));
            // Assign to view
            $this->view->assign(array(
                'arrCategoryDetail' => $arrCategoryDetail,
                'arrCategories' => $arrCategories,
                'intCategoryType' => $intCategoryType
            ));
            //Render view
            $response['html'] = $this->view->render('categories/add.phtml');
            //Return Json
            echo Zend_Json::encode($response);
            exit;
        }
    }
}

?>