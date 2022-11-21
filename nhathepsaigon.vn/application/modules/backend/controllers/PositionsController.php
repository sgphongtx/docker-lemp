<?php

/**
 * @author      :   PhongTX
 * @name        :   PositionsController
 * @copyright   :   Fpt Online
 * @todo        :   Positions Controller
 */
class Backend_PositionsController extends Zend_Controller_Action
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
        $arrReturn = Core_Model_Positions::getListPositions($arrParams);
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
        $this->view->headTitle()->append('Vị trí quảng cáo');
        //append script
        $this->view->headScript()->appendFile(STATIC_URL . '/be/js/positions/add.js');
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
            $checkPosition = Core_Validate_Positions::checkPosition($arrParamsRequest);
            if ($checkPosition['error'] == 1) {
                echo Zend_Json::encode($checkPosition);
                exit;
            } else {
                //Add Position
                if($arrParamsRequest['intId'] == 0) {
                    $arrParams = array(
                        'strName' => trim(strip_tags($arrParamsRequest['strName'])),
                        'intStatus' => 1 //status = 0: Delete, status = 1: Active, status = 2: Inactive
                    );
                    $intId = Core_Model_Positions::insertPosition($arrParams);
                    if ($intId > 0) {
                        $arrReturn['error'] = 0;
                        $arrReturn['msg'] = 'Add vị trí quảng cáo thành công';
                    }
                //Update Positions
                }else{
                    $arrParams = array(
                        'strName' => trim(strip_tags($arrParamsRequest['strName'])),
                        'intId' => $arrParamsRequest['intId']
                    );
                    $isUpdate = Core_Model_Positions::updatePosition($arrParams);
                    if ($isUpdate > 0) {
                        $arrReturn['error'] = 0;
                        $arrReturn['msg'] = 'Update vị trí quảng cáo thành công';
                    }
                }
                echo Zend_Json::encode($arrReturn);
                exit;
            }
        }
    }

    /**
     * @todo - Action ajaxGetDetailPosition
     * @author PhongTX
     */
    public function ajaxGetDetailPositionAction()
    {
        //disable layout + render
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->_request->isPost()) {
            //Get intPositionId
            $intId = $this->_request->getParam('intId', 0);
            //Define $arrPositionDetail
            $arrPositionDetail = array();
            if ($intId != 0) {
                $arrPositionDetail = Core_Model_Positions::getDetailPositionById($intId);
            }
            // Assign to view
            $this->view->assign(array(
                'arrPositionDetail' => $arrPositionDetail
            ));
            //Render view
            $response['html'] = $this->view->render('positions/add.phtml');
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
                'strTagName' => NULL,
                'intStatus' => $arrParamsRequest['intStatus'],
                'intId' => $arrParamsRequest['intId']
            );
            $isUpdate = Core_Model_Positions::updatePosition($arrParams);
            if ($isUpdate) {
                $label = $arrParamsRequest['intStatus']==1?'Active':'Inactive';
                $arrReturn['error'] = 0;
                $arrReturn['msg'] = $label.' vị trí quảng cáo [' . $arrParamsRequest['strName'] . '] thành công';
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
                'strTagName' => NULL,
                'intStatus' => 0, //Delete
                'intId' => $arrParamsRequest['intId']
            );
            $isUpdate = Core_Model_Positions::updatePosition($arrParams);
            if ($isUpdate) {
                $arrReturn['error'] = 0;
                $arrReturn['msg'] = 'Xóa vị trí quảng cáo [' . $arrParamsRequest['strName'] . '] thành công';
            }
            //Return Json
            echo Zend_Json::encode($arrReturn);
            exit;
        }
    }

}

?>