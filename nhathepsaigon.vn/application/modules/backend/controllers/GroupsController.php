<?php

/**
 * @author      :   PhongTX
 * @name        :   GroupsController
 * @copyright   :   Fpt Online
 * @todo        :   Groups Controller
 */
class Backend_GroupsController extends Zend_Controller_Action
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
        //Get page
        $intPage = max((int)$this->_request->getParam('page', 1), 1);
        //Get limit
        $intLimit = max((int)$this->_request->getParam('limit', DEFAULT_LIMIT), DEFAULT_LIMIT);
        //Get offset
        $offset = ($intPage - 1) * $intLimit;

        $arrParams = array(
            'strStatus' => '1,2',//1: active, 2: inactive
            'intOffset' => ($offset > 0) ? $offset : 0,
            'intLimit' => $intLimit,
        );
        $arrReturn = Core_Model_Groups::getListGroups($arrParams);
        //Return page
        $arrReturn['intPage'] = $intPage;
        //Return limit
        $arrReturn['intLimit'] = $intLimit;
        //Return offset
        $arrReturn['intOffset'] = $offset;
        //Assign to view
        $this->view->assign(array(
                'arrData' => $arrReturn
            )
        );
        //Set title
        $this->view->headTitle()->append('Nhóm quyền');
        //append script
        $this->view->headScript()->appendFile(STATIC_URL . '/be/js/groups/add.js');
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
            $checkGroup = Core_Validate_Groups::checkGroup($arrParamsRequest);
            if ($checkGroup['error'] == 1) {
                echo Zend_Json::encode($checkGroup);
                exit;
            } else {
                //Add group
                if($arrParamsRequest['intGroupId'] == 0) {
                    $arrParams = array(
                        'strGroupName' => trim(strip_tags($arrParamsRequest['strGroupName'])),
                        'strGroupDesc' => trim(strip_tags($arrParamsRequest['strGroupDesc'])),
                        'intStatus' => 1 //status = 0: Delete, status = 1: Active, status = 2: Inactive
                    );
                    $intGroupId = Core_Model_Groups::insertGroup($arrParams);
                    if ($intGroupId > 0) {
                        if(isset($arrParamsRequest['accept']) && !empty($arrParamsRequest['accept'])){
                            foreach($arrParamsRequest['accept'] as $key => $row){
                                if(!empty($row)){
                                    foreach($row as $r){
                                        $arrParams = array(
                                            'intGroupId' => $intGroupId,
                                            'intMenuId' => $key,
                                            'intActionId' => $r,
                                            'intUserId' => $this->view->user['user_id']
                                        );
                                        Core_Model_Groups::insertPermission($arrParams);
                                    }
                                }
                            }
                        }
                        $arrReturn['error'] = 0;
                        $arrReturn['msg'] = 'Tạo nhóm quyền thành công';
                    }
                    //Update group
                }else{
                    $arrParams = array(
                        'strGroupName' => trim(strip_tags($arrParamsRequest['strGroupName'])),
                        'strGroupDesc' => trim(strip_tags($arrParamsRequest['strGroupDesc'])),
                        'intGroupId' => $arrParamsRequest['intGroupId']
                    );
                    $isUpdate = Core_Model_Groups::updateGroup($arrParams);
                    if ($isUpdate > 0) {
                        Core_Model_Groups::delPermissionByGroupId($arrParamsRequest['intGroupId']);
                        if(isset($arrParamsRequest['accept']) && !empty($arrParamsRequest['accept'])){
                            foreach($arrParamsRequest['accept'] as $key => $row){
                                if(!empty($row)){
                                    foreach($row as $r){
                                        $arrParams = array(
                                            'intGroupId' => $arrParamsRequest['intGroupId'],
                                            'intMenuId' => $key,
                                            'intActionId' => $r,
                                            'intUserId' => $this->view->user['user_id']
                                        );
                                        Core_Model_Groups::insertPermission($arrParams);
                                    }
                                }
                            }
                        }
                        $arrReturn['error'] = 0;
                        $arrReturn['msg'] = 'Cập nhật nhóm quyền thành công';
                    }
                }
                echo Zend_Json::encode($arrReturn);
                exit;
            }
        }
    }

    /**
     * @todo - Action ajaxGetDetailGroup
     * @author PhongTX
     */
    public function ajaxGetDetailGroupAction()
    {
        //disable layout + render
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->_request->isPost()) {
            //Get intGroupId
            $intGroupId = $this->_request->getParam('intGroupId', 0);
            //Define $arrGroupDetail
            $arrGroupDetail = array();
            if ($intGroupId != 0) {
                $arrGroupDetail = Core_Model_Groups::getDetailGroupById($intGroupId);
            }
            //Get list menus
            $arrMenus = Core_Model_Menus::getListMenus();
            //Call recursive function
            Core_Global::recursive($arrMenus, 'menu_id', 0, 0, $arrResult);
            //Get permission by group id
            $arrPermission = Core_Model_Groups::getPermissionByGroupId($intGroupId);
            if(!empty($arrResult)){
                foreach($arrResult as $key => $row){
                    if(!empty($arrPermission)){
                        foreach($arrPermission as $r){
                            if($row['menu_id'] == $r['menu_id']){
                                $arrResult[$key]['action'][] = $r['action_id'];
                            }
                        }
                    }
                }
            }
            // Assign to view
            $this->view->assign(array(
                'arrGroupDetail' => $arrGroupDetail,
                'arrMenus' => $arrResult
            ));
            //Render view
            $response['html'] = $this->view->render('groups/add.phtml');
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
                'strGroupName' => NULL,
                'intStatus' => $arrParamsRequest['intStatus'],
                'intGroupId' => $arrParamsRequest['intGroupId']
            );
            $isUpdate = Core_Model_Groups::updateGroup($arrParams);
            if ($isUpdate) {
                $label = $arrParamsRequest['intStatus']==1?'Active':'Inactive';
                $arrReturn['error'] = 0;
                $arrReturn['msg'] = $label.' nhóm quyền [' . $arrParamsRequest['strName'] . '] thành công';
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
                'strGroupName' => NULL,
                'intStatus' => 0, //Delete
                'intGroupId' => $arrParamsRequest['intGroupId']
            );
            $isUpdate = Core_Model_Groups::updateGroup($arrParams);
            if ($isUpdate) {
                $arrReturn['error'] = 0;
                $arrReturn['msg'] = 'Xóa nhóm quyền [' . $arrParamsRequest['strName'] . '] thành công';
            }
            //Return Json
            echo Zend_Json::encode($arrReturn);
            exit;
        }
    }

}

?>