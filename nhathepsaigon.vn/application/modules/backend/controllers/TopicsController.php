<?php

/**
 * @author      :   PhongTX
 * @name        :   TopicsController
 * @copyright   :   Fpt Online
 * @todo        :   Topics Controller
 */
class Backend_TopicsController extends Zend_Controller_Action
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
        $intTopicType = (int)$this->_request->getParam('type', 1);
        //Get page
        $intPage = max((int)$this->_request->getParam('page', 1), 1);
        //Get limit
        $intLimit = max((int)$this->_request->getParam('limit', DEFAULT_LIMIT), DEFAULT_LIMIT);
        //Get offset
        $offset = ($intPage - 1) * $intLimit;

        $arrParams = array(
            'strStatus' => '1,2',
            'intOffset' => ($offset > 0) ? $offset : 0,
            'intLimit' => $intLimit,
        );
        $arrReturn = Core_Model_Topics::getListTopics($arrParams);
        //Return page
        $arrReturn['intPage'] = $intPage;
        //Return limit
        $arrReturn['intLimit'] = $intLimit;
        //Return offset
        $arrReturn['intOffset'] = $offset;
        //Assign to view
        $this->view->assign(array(
                'arrData' => $arrReturn,
                'intTopicType' => $intTopicType
            )
        );
        //Set title
        $this->view->headTitle()->append('List topics');
        //append script
        $this->view->headScript()->appendFile(STATIC_URL . '/be/js/topics/add.js');
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
            $arrReturn = array(
                'error' => 1,
                'msg' => 'Có lỗi xảy ra'
            );
            //Get params
            $arrParamsRequest = $this->_request->getParams();
            $checkTopic = Core_Validate_Topics::checkTopic($arrParamsRequest);
            if ($checkTopic['error'] == 1) {
                echo Zend_Json::encode($checkTopic);
                exit;
            } else {
                //Add Topic
                if($arrParamsRequest['intTopicId'] == 0) {
                    $arrParams = array(
                        'strTopicName' => trim(strip_tags($arrParamsRequest['strTopicName'])),
                        'intCategoryId' => $arrParamsRequest['intCategoryId'],
                        'intAuthorId' => $this->view->user['user_id'],
                        'intStatus' => $arrParamsRequest['intStatus'] //status = 0: Delete, status = 1: Active, status = 2: Inactive
                    );
                    $intTopicId = Core_Model_Topics::insertTopic($arrParams);
                    if ($intTopicId > 0) {
                        $arrReturn['error'] = 0;
                        $arrReturn['msg'] = 'Add topic thành công';
                    }
                    //Update topic
                }else{
                    $arrParams = array(
                        'strTopicName' => trim(strip_tags($arrParamsRequest['strTopicName'])),
                        'intCategoryId' => $arrParamsRequest['intCategoryId'],
                        'intStatus' => $arrParamsRequest['intStatus'], //status = 0: Delete, status = 1: Active, status = 2: Inactive
                        'intTopicId' => $arrParamsRequest['intTopicId']
                    );
                    $intTopicId = Core_Model_Topics::updateTopic($arrParams);
                    if ($intTopicId > 0) {
                        $arrReturn['error'] = 0;
                        $arrReturn['msg'] = 'Update topic thành công';
                    }
                }
                echo Zend_Json::encode($arrReturn);
                exit;
            }
        }
    }

    /**
     * @todo - Action ajaxGetDetailTopic
     * @author PhongTX
     */
    public function ajaxGetDetailTopicAction()
    {
        //disable layout + render
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->_request->isPost()) {
            //Get intTopicId
            $intTopicId = $this->_request->getParam('intTopicId', 0);
            //Define $arrTopicDetail
            $arrTopicDetail = $arrCategoryDetail = array();
            if ($intTopicId != 0) {
                $arrTopicDetail = Core_Model_Topics::getDetailTopicById($intTopicId);
                //Get cate detail
                $arrCategoryDetail = Core_Model_Categories::getDetailCategoryById($arrTopicDetail['category_id']);
            }else{
                $arrCategoryDetail['cate_name'] = 'Select category';
            }
            //Get list category
            $arrCategories = Core_Model_Categories::getListCategory(array('intCategoryType' => 1, 'intParentId' => NULL, 'intStatus' => 1, 'intOffset' => NULL, 'intLimit' => NULL));
            // Assign to view
            $this->view->assign(array(
                'arrTopicDetail' => $arrTopicDetail,
                'arrCategories' => $arrCategories,
                'arrCategoryDetail' => $arrCategoryDetail
            ));
            //Render view
            $response['html'] = $this->view->render('topics/add.phtml');
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
                'strTopicName' => NULL,
                'intCategoryId' => NULL,
                'intStatus' => $arrParamsRequest['intStatus'],
                'intTopicId' => $arrParamsRequest['intTopicId']
            );
            $isUpdate = Core_Model_Topics::updateTopic($arrParams);
            if ($isUpdate) {
                $label = $arrParamsRequest['intStatus']==1?'Active':'Inactive';
                $arrReturn['error'] = 0;
                $arrReturn['msg'] = $label.' topic [' . $arrParamsRequest['strName'] . '] thành công';
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
                'strTopicName' => NULL,
                'intCategoryId' => NULL,
                'intStatus' => 0,//Delete
                'intTopicId' => $arrParamsRequest['intTopicId']
            );
            $isUpdate = Core_Model_Topics::updateTopic($arrParams);
            if ($isUpdate) {
                $arrReturn['error'] = 0;
                $arrReturn['msg'] = 'Xóa topic [' . $arrParamsRequest['strName'] . '] thành công';
            }
            //Return Json
            echo Zend_Json::encode($arrReturn);
            exit;
        }
    }
}

?>