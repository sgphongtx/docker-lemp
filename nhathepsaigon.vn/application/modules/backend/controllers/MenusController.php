<?php

/**
 * @author      :   PhongTX
 * @name        :   MenusController
 * @copyright   :   Fpt Online
 * @todo        :   Menus Controller
 */
class Backend_MenusController extends Zend_Controller_Action
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
        $arrReturn = Core_Model_Menus::getListMenus();
        //Call recursive function
        Core_Global::recursive($arrReturn, 'menu_id', 0, 0, $arrResult);
        //Assign to view
        $this->view->assign(array(
                'arrData' => $arrResult
            )
        );
        //Set title
        $this->view->headTitle()->append('Quản lý menus');
        //append script
        $this->view->headScript()->appendFile(STATIC_URL . '/be/js/menus/add.js');
    }

    /**
     * @todo - Action add
     * @Author PhongTX
     */
    public function ajaxAddAction()
    {
        //disable layout + render
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->_request->isPost()) {
            //Get params
            $arrParamsRequest = $this->_request->getParams();
            $arrReturn = array(
                'error' => 1,
                'msg' => 'Có lỗi xảy ra'
            );
            $checkMenu = Core_Validate_Menus::checkMenu($arrParamsRequest);
            if ($checkMenu['error'] == 1) {
                echo Zend_Json::encode($checkMenu);
                exit;
            } else {
                //Add menu
                if($arrParamsRequest['intMenuId'] == 0) {
                    $arrParams = array(
                        'intParentId' => $arrParamsRequest['intParentId'],
                        'strMenuName' => trim(strip_tags($arrParamsRequest['strMenuName'])),
                        'strMenuCode' => trim(strip_tags($arrParamsRequest['strMenuCode'])),
                        'strMenuUrl' => trim(strip_tags($arrParamsRequest['strMenuUrl'])),
                        'intDisplayOrder' => $arrParamsRequest['intDisplayOrder'],
                        'intStatus' => 1 //status = 0: Delete, status = 1: Active, status = 2: Inactive
                    );
                    $intMenuId = Core_Model_Menus::insertMenu($arrParams);
                    unset($arrParams);
                    if ($intMenuId > 0) {
                        $arrReturn['error'] = 0;
                        $arrReturn['msg'] = 'Add menu thành công';
                    }
                //Update menu
                }else{
                    $arrParams = array(
                        'intMenuId' => $arrParamsRequest['intMenuId'],
                        'intParentId' => $arrParamsRequest['intParentId'],
                        'strMenuName' => trim(strip_tags($arrParamsRequest['strMenuName'])),
                        'strMenuCode' => trim(strip_tags($arrParamsRequest['strMenuCode'])),
                        'strMenuUrl' => trim(strip_tags($arrParamsRequest['strMenuUrl'])),
                        'intDisplayOrder' => $arrParamsRequest['intDisplayOrder'],
                        'intStatus' => NULL //status = 0: Delete, status = 1: Active, status = 2: Inactive
                    );
                    $isUpdate = Core_Model_Menus::updateMenu($arrParams);
                    if ($isUpdate > 0) {
                        $arrReturn['error'] = 0;
                        $arrReturn['msg'] = 'Update menu thành công';
                    }
                }
                echo Zend_Json::encode($arrReturn);
                exit;
            }
        }
    }

    /**
     * @todo - Action ajaxGetDetailMenu
     * @author PhongTX
     */
    public function ajaxGetDetailMenuAction()
    {
        //disable layout + render
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->_request->isPost()) {
            //Get intMenuId
            $intMenuId = $this->_request->getParam('intMenuId', 0);
            //Define $arrMenuDetail
            $arrMenuDetail = array();
            if ($intMenuId != 0) {
                $arrMenuDetail = Core_Model_Menus::getDetailMenuById($intMenuId);
                if($arrMenuDetail['parent_id'] > 0) {
                    //Get parent detail
                    $arrParentDetail = Core_Model_Menus::getDetailMenuById($arrMenuDetail['parent_id']);
                    $arrMenuDetail['parent_name'] = $arrParentDetail['menu_name'];
                }else{
                    $arrMenuDetail['parent_name'] = 'Chọn danh mục';
                }
            }else{
                $arrMenuDetail['parent_name'] = 'Chọn danh mục';
            }
            //Get list menus
            $arrMenus = Core_Model_Menus::getListMenus();
            // Assign to view
            $this->view->assign(array(
                'arrMenuDetail' => $arrMenuDetail,
                'arrMenus' => $arrMenus
            ));
            //Render view
            $response['html'] = $this->view->render('menus/add.phtml');
            //Return Json
            echo Zend_Json::encode($response);
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
                'strMenuName' => NULL,
                'intStatus' => $arrParamsRequest['intStatus'],
                'intMenuId' => $arrParamsRequest['intMenuId']
            );
            $isUpdate = Core_Model_Menus::updateMenu($arrParams);
            if ($isUpdate) {
                $label = $arrParamsRequest['intStatus']==1?'Active':'Inactive';
                $arrReturn['error'] = 0;
                $arrReturn['msg'] = $label.' menu [' . $arrParamsRequest['strName'] . '] thành công';
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
                'strMenuName' => NULL,
                'intStatus' => 0, //Delete
                'intMenuId' => $arrParamsRequest['intMenuId']
            );
            $isUpdate = Core_Model_Menus::updateMenu($arrParams);
            if ($isUpdate) {
                $arrReturn['error'] = 0;
                $arrReturn['msg'] = 'Xóa menu [' . $arrParamsRequest['strName'] . '] thành công';
            }
            //Return Json
            echo Zend_Json::encode($arrReturn);
            exit;
        }
    }
}

?>