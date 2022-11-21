<?php

/**
 * @author      :   PhongTX
 * @name        :   TagsController
 * @copyright   :   Fpt Online
 * @todo        :   Tags Controller
 */
class Backend_TagsController extends Zend_Controller_Action
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
        //Get id
        $intId = (int)$this->_request->getParam('keyid', 0);
        //Get id
        $strKeyword = $this->_request->getParam('keyword', null);
        $strKeyworden = Core_Utils::vn_str_filter(Core_Utils::upperTolower(trim(strip_tags($strKeyword))));
        //Get page
        $intPage = max((int)$this->_request->getParam('page', 1), 1);
        //Get limit
        $intLimit = max((int)$this->_request->getParam('limit', DEFAULT_LIMIT), DEFAULT_LIMIT);
        //Get offset
        $offset = ($intPage - 1) * $intLimit;

        $arrParams = array(
            'intId' => $intId > 0 ? $intId : NULL,
            'strKeyword' => isset($strKeyworden) && $strKeyworden != '' ? $strKeyworden : NULL,
            'strStatus' => '1,2',//1: active, 2: inactive
            'intOffset' => ($offset > 0) ? $offset : 0,
            'intLimit' => $intLimit,
        );
        $arrReturn = Core_Model_Tags::getListTags($arrParams);
        //Return page
        $arrReturn['intPage'] = $intPage;
        //Return limit
        $arrReturn['intLimit'] = $intLimit;
        //Return offset
        $arrReturn['intOffset'] = $offset;
        //Assign to view
        $this->view->assign(array(
                'arrData' => $arrReturn,
                'intId' => $intId,
                'strKeyword' => $strKeyword
            )
        );
        //Set title
        $this->view->headTitle()->append('Tags');
        //append script
        $this->view->headScript()->appendFile(STATIC_URL . '/be/js/tags/add.js');
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
            $checkTag = Core_Validate_Tags::checkTag($arrParamsRequest);
            if ($checkTag['error'] == 1) {
                echo Zend_Json::encode($checkTag);
                exit;
            } else {
                //Add tag
                if($arrParamsRequest['intTagId'] == 0) {
                    $arrParams = array(
                        'strTagName' => trim(strip_tags($arrParamsRequest['strTagName'])),
                        'strTagNameEn' => Core_Utils::vn_str_filter(Core_Utils::upperTolower(trim(strip_tags($arrParamsRequest['strTagName'])))),
                        'strTagCode' => strtolower(Core_Utils::setSeoLink(trim(strip_tags($arrParamsRequest['strTagName'])))),
                        'strMetaTitle' => trim(strip_tags($arrParamsRequest['strMetaTitle'])),
                        'strMetaDesc' => trim(strip_tags($arrParamsRequest['strMetaDesc'])),
                        'strMetaKeyword' => trim(strip_tags($arrParamsRequest['strMetaKeyword'])),
                        'intStatus' => 1 //status = 0: Delete, status = 1: Active, status = 2: Inactive
                    );
                    $intTagId = Core_Model_Tags::insertTag($arrParams);
                    if ($intTagId > 0) {
                        $arrReturn['error'] = 0;
                        $arrReturn['msg'] = 'Add tag thành công';
                    }
                //Update tag
                }else{
                    $arrParams = array(
                        'strTagName' => trim(strip_tags($arrParamsRequest['strTagName'])),
                        'strTagNameEn' => Core_Utils::vn_str_filter(Core_Utils::upperTolower(trim(strip_tags($arrParamsRequest['strTagName'])))),
                        'strTagCode' => strtolower(Core_Utils::setSeoLink(trim(strip_tags($arrParamsRequest['strTagName'])))),
                        'strMetaTitle' => trim(strip_tags($arrParamsRequest['strMetaTitle'])),
                        'strMetaDesc' => trim(strip_tags($arrParamsRequest['strMetaDesc'])),
                        'strMetaKeyword' => trim(strip_tags($arrParamsRequest['strMetaKeyword'])),
                        'intTagId' => $arrParamsRequest['intTagId']
                    );
                    $isUpdate = Core_Model_Tags::updateTag($arrParams);
                    if ($isUpdate > 0) {
                        $arrReturn['error'] = 0;
                        $arrReturn['msg'] = 'Update tag thành công';
                    }
                }
                echo Zend_Json::encode($arrReturn);
                exit;
            }
        }
    }

    /**
     * @todo - Action ajaxGetDetailTag
     * @author PhongTX
     */
    public function ajaxGetDetailTagAction()
    {
        //disable layout + render
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->_request->isPost()) {
            //Get intTagId
            $intTagId = $this->_request->getParam('intTagId', 0);
            //Define $arrTagDetail
            $arrTagDetail = array();
            if ($intTagId != 0) {
                $arrTagDetail = Core_Model_Tags::getDetailTagById($intTagId);
            }
            // Assign to view
            $this->view->assign(array(
                'arrTagDetail' => $arrTagDetail
            ));
            //Render view
            $response['html'] = $this->view->render('tags/add.phtml');
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
                'intTagId' => $arrParamsRequest['intTagId']
            );
            $isUpdate = Core_Model_Tags::updateTag($arrParams);
            if ($isUpdate) {
                $label = $arrParamsRequest['intStatus']==1?'Active':'Inactive';
                $arrReturn['error'] = 0;
                $arrReturn['msg'] = $label.' tag [' . $arrParamsRequest['strName'] . '] thành công';
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
                'intTagId' => $arrParamsRequest['intTagId']
            );
            $isUpdate = Core_Model_Tags::updateTag($arrParams);
            if ($isUpdate) {
                $arrReturn['error'] = 0;
                $arrReturn['msg'] = 'Xóa tag [' . $arrParamsRequest['strName'] . '] thành công';
            }
            //Return Json
            echo Zend_Json::encode($arrReturn);
            exit;
        }
    }

    /**
     * @author      :   PhongTX
     * @name    :   ajaxSearchTagsAction
     */
    public function ajaxSearchTagsAction()
    {
        //disable layout + render
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        //Get key
        $strKeyword = Core_Utils::vn_str_filter(Core_Utils::upperTolower(trim(strip_tags($this->_request->getParam('q', '')))));
        //$strKeyword = trim(strip_tags($this->_request->getParam('q', '')));
        $arrResult = array();
        //Define arrParams
        $arrParams = array(
            'strTagNameEn' => $strKeyword,
            'status' => 1,
            'intOffset' => 0,
            'intLimit' => 30,
        );
        $arrResult = Core_Model_Tags::tagsSearchKeyword($arrParams);
        if (isset($arrResult['data'])) {
            foreach ($arrResult['data'] as $key => $row) {
                $arrReturn[] = array('id' => $row['tag_id'], 'text' => $row['tag_name']);
            }
        }
        echo Zend_Json::encode($arrReturn);
        exit;
    }
}

?>