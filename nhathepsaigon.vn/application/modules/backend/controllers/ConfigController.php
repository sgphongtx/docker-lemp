<?php

/**
 * @author      :   PhongTX
 * @name        :   ConfigController
 * @copyright   :   Fpt Online
 * @todo        :   Config Controller
 */
class Backend_ConfigController extends Zend_Controller_Action
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
        if(!in_array($arrMenuByMenuCode['menu_id'],$this->view->user['permission'][PERMISSION_ADD_EDIT])){
            $this->_redirect(SITE_URL . '/backend/forbidden');
        }
        //Get detail config
        $arrConfig = Core_Model_Config::getDetailConfig(CONFIG);
        // Assign to view
        $this->view->assign(array(
            'arrConfig' => $arrConfig
        ));
        //Set title
        $this->view->headTitle()->append('Cấu hình chung');
        //Append css
        $this->view->headLink()->appendStylesheet(STATIC_URL . '/library/plugins/bootstrap-colorpicker-plus-master/css/bootstrap-colorpicker.min.css')
            ->appendStylesheet(STATIC_URL . '/library/plugins/bootstrap-colorpicker-plus-master/css/bootstrap-colorpicker-plus.min.css');
        //append script
        $this->view->headScript()->appendFile(STATIC_URL . '/library/plugins/bootstrap-colorpicker-plus-master/js/bootstrap-colorpicker.min.js')
                                ->appendFile(STATIC_URL . '/library/plugins/bootstrap-colorpicker-plus-master/js/bootstrap-colorpicker-plus.min.js')
                                ->appendFile(STATIC_URL . '/be/js/config/index.js');
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
                'intId' => $arrParamsRequest['intId'],
                'strWebLink' => $arrParamsRequest['strWebLink'],
                'strWebName' => $arrParamsRequest['strWebName'],
                'strWebDescription' => $arrParamsRequest['strWebDescription'],
                'strMetaTitle' => $arrParamsRequest['strMetaTitle'],
                'strMetaDescription' => $arrParamsRequest['strMetaDescription'],
                'strMetaKeyword' => $arrParamsRequest['strMetaKeyword'],
                'strAddress' => $arrParamsRequest['strAddress'],
                'strPhone' => $arrParamsRequest['strPhone'],
                'strEmail' => $arrParamsRequest['strEmail'],
                'intLinkFb' => $arrParamsRequest['strLinkFb'],
                'strLinkTwitter' => $arrParamsRequest['strLinkTwitter'],
                'strLinkGPlus' => $arrParamsRequest['strLinkGPlus'],
                'strLinkYoutube' => $arrParamsRequest['strLinkYoutube'],
                'intTopstoryType' => $arrParamsRequest['intTopstoryType'],
                'strTextBanner' => $arrParamsRequest['strTextBanner'],
                'strColorTextBanner' => $arrParamsRequest['strColorTextBanner']
            );
            $check = Core_Validate_Config::checkConfig($arrParamsRequest);
            if ($check['error'] == 1) {
                echo Zend_Json::encode($check);
                exit;
            } else {
                $isUpdate = Core_Model_Config::updateConfig($arrParams);
                if ($isUpdate) {
                    $arrReturn['error'] = 0;
                    $arrReturn['msg'] = 'Update config thành công';
                }
            }
            //Return Json
            echo Zend_Json::encode($arrReturn);
            exit;
        }
    }
}

?>