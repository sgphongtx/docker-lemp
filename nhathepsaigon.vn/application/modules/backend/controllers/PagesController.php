<?php

/**
 * @author      :   PhongTX
 * @name        :   PagesController
 * @copyright   :   Fpt Online
 * @todo        :   Pages Controller
 */
class Backend_PagesController extends Zend_Controller_Action
{
    //public $arrImgDone = array();
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
        $arrStatus = array(
            PAGES_STATUS_ACTIVE => 'Active',
            PAGES_STATUS_INACTIVE => 'Inactive'
        );
        //Get page
        $intPage = max((int)$this->_request->getParam('page', 1), 1);
        //Get limit
        $intLimit = max((int)$this->_request->getParam('limit', DEFAULT_LIMIT), DEFAULT_LIMIT);
        //Get offset
        $offset = ($intPage - 1) * $intLimit;
        //Get id
        $intId = (int)$this->_request->getParam('keyid', 0);
        //Get keyword
        $strKeyword = $this->_request->getParam('keyword', null);
        //Get pageview
        $intPageview = (int)$this->_request->getParam('pageview', 0);
        //Get user id
        $intUserId = (int)$this->_request->getParam('userid', 0);
        //Define arrParams
        $arrParams = array(
            'intId' => $intId > 0 ? $intId : NULL,
            'strKeyword' => isset($strKeyword) && $strKeyword != '' ? $strKeyword : NULL,
            'intStatus' => 1,
            'intUserId' => $intUserId == 0 ? NULL : $intUserId,
            'intOffset' => ($offset > 0) ? $offset : 0,
            'intLimit' => $intLimit,
            'intPageview' => $intPageview == 0 ? NULL : $intPageview
        );
        //Call function getListPage
        $arrReturn = Core_Model_Pages::getListPage($arrParams);
        $arrUser = $arrUserId = array();
        if (!empty($arrReturn['data'])) {
            foreach ($arrReturn['data'] as $row) {
                $userDetail = Core_Model_Users::getDetailUserById($row['user_id']);
                $arrUser[$row['id']]['user'] = $userDetail;
            }
        }
        $arrUserId = Core_Model_Users::getListUsers(array('strStatus' => 1, 'intLimit' => 500, 'intOffset' => 0));
        //Return page
        $arrReturn['intPage'] = $intPage;
        //Return limit
        $arrReturn['intLimit'] = $intLimit;

        $arrReturn['intOffset'] = $offset;

        //Assign to view
        $this->view->assign(array(
            'arrData' => $arrReturn,
            'arrUser' => $arrUser,
            'intId' => $intId,
            'strKeyword' => $strKeyword,
            'intUserId' => $intUserId,
            'arrUserId' => $arrUserId['data'],
            'intPageview' => $intPageview,
            'arrStatus' => $arrStatus
        ));
        $strName = 'Danh sách trang tĩnh';
        //Set title
        $this->view->headTitle()->append($strName);
        //append script
        $this->view->headScript()->appendFile(STATIC_URL . '/be/js/pages/index.js');
    }

    /**
     * @todo - Action add
     * @Author PhongTX
     */
    public function addAction()
    {
        //Check permission - Get menu by menu code
        $arrMenuByMenuCode = Core_Model_Menus::getDetailMenuByMenuCode($this->view->moduleName.'_'.$this->view->controllerName.'_'.$this->view->actionName);
        if(!in_array($arrMenuByMenuCode['menu_id'],$this->view->user['permission'][PERMISSION_ADD_EDIT])){
            $this->_redirect(SITE_URL . '/backend/forbidden');
        }
        /*End check permission*/
        $arrReturn = array(
            'error' => 1,
            'is_post' => 0,
            'msg' => 'Có lỗi xảy ra.',
            'data' => array(
                //'strPageKey' => '',
                'strName' => '',
                'strMetaTitle' => '',
                'strMetaDesc' => '',
                'strMetaKeyword' => '',
                'ckeditor' => '',
                'strDate' => date('d/m/Y H:i:s', time()),
                'intStatus' => 1 //Active
            )
        );
        if ($this->_request->isPost()) {
            //Request params
            $arrParamsRequest = $this->_request->getParams();
            $checkPages = Core_Validate_Pages::checkData($arrParamsRequest,'add');
            if ($checkPages['error'] == 1) {
                $arrReturn = array_merge($arrReturn, $checkPages);
            } else {
                $name = trim(strip_tags(str_replace('&nbsp;', ' ', $arrParamsRequest['strName'])));
                $pregfind = array('#<img[^>]+src=(\'|")(.*)(\\1).*>#siU');
                $response['data']['content'] = preg_replace_callback($pregfind, function ($match) {
                    return Core_Utils::setImageContent($match,'http://nhathepsaigon.vn','nhathepsaigon.vn','www.nhathepsaigon.vn');
                }, $arrParamsRequest['ckeditor']);
                //Lazyload image
                //$response['data']['content'] = preg_replace('/\<[\/]?(table|tbody|tr|td)([^\>]*)\>/i', '', $response['data']['content']);
                // $arrParams insert article
                $arrParams = array(
                    'strPageKey' => strtolower(Core_Utils::setSeoLink($name)), //$arrParamsRequest['strPageKey'],
                    'strName' => $this->view->escape($name),
                    'strShareUrl' => NULL,
                    'strMetaTitle' => $this->view->escape(trim(strip_tags($arrParamsRequest['strMetaTitle']))),
                    'strMetaDesc' => $this->view->escape(trim(strip_tags($arrParamsRequest['strMetaDesc']))),
                    'strMetaKeyword' => $this->view->escape(trim(strip_tags($arrParamsRequest['strMetaKeyword']))),
                    'ckeditor' => $response['data']['content'], //$arrParamsRequest['ckeditor'],
                    'intPublishTime' => strtotime(str_ireplace('/', '-', $arrParamsRequest['strDate'])),
                    'intStatus' => $arrParamsRequest['intStatus'],
                    'intUserId' => $this->view->user['user_id']
                );
                // insert page
                $intId = Core_Model_Pages::insertPage($arrParams);
                if ($intId > 0) {
                    $nameSeo = strtolower(Core_Utils::setSeoLink($name));
                    $shareUrl = '/' . $nameSeo . '.html';
                    $arrParamsPageUpdate = array(
                        'strPageKey' => NULL,
                        'strName' => NULL,
                        'strShareUrl' => $shareUrl,
                        'strMetaTitle' => NULL,
                        'strMetaDesc' => NULL,
                        'strMetaKeyword' => NULL,
                        'ckeditor' => NULL,
                        'intViews' => NULL,
                        'intStatus' => NULL,
                        'intUserId' => NULL,
                        'intPublishTime' => NULL,
                        'intId' => $intId
                    );
                    $resultUpdate = Core_Model_Pages::updatePage($arrParamsPageUpdate);
                    if ($resultUpdate) {
                        $arrReturn['msg'] = 'Thêm trang tĩnh thành công.';
                    } else {
                        $arrReturn['msg'] = 'Thêm trang tĩnh thành công nhưng không tạo được share URL.';
                    }
                    $arrReturn['error'] = 0;
                } else {
                    $arrReturn['msg'] = 'Thêm trang tĩnh không thành công.';
                }
            }
            $arrReturn['is_post'] = 1;
            $arrReturn['data'] = array(
                //'strPageKey' => $arrParamsRequest['strPageKey'],
                'strName' => $arrParamsRequest['strName'],
                'strMetaTitle' => $arrParamsRequest['strMetaTitle'],
                'strMetaDesc' => $arrParamsRequest['strMetaDesc'],
                'strMetaKeyword' => $arrParamsRequest['strMetaKeyword'],
                'ckeditor' => $arrParamsRequest['ckeditor'],
                'strDate' => $arrParamsRequest['strDate'],
                'intStatus' => $arrParamsRequest['intStatus']
            );
        }
        //Assign to view
        $this->view->assign(array(
                'arrReturn' => $arrReturn
            )
        );
        //Set title
        $this->view->headTitle()->append('Tạo trang tĩnh');
        //append script
        $this->view->headScript()->appendFile(STATIC_URL . '/library/plugins/ckeditor-4.5.9/ckeditor.js')
            ->appendFile(STATIC_URL . '/be/js/pages/add.js');
    }

    /**
     * @todo - Action edit
     * @Author PhongTX
     */
    public function editAction()
    {
        //Check permission - Get menu by menu code
        $arrMenuByMenuCode = Core_Model_Menus::getDetailMenuByMenuCode($this->view->moduleName.'_'.$this->view->controllerName.'_'.$this->view->actionName);
        if(!in_array($arrMenuByMenuCode['menu_id'],$this->view->user['permission'][PERMISSION_ADD_EDIT])){
            $this->_redirect(SITE_URL . '/backend/forbidden');
        }
        /*End check permission*/
        //Get id
        $id = $this->_request->getParam('id', 0);
        //Get request
        $arrParamsRequest = $this->_request->getParams();
        if ($id == 0) {
            $this->_redirect(SITE_URL . '/backend/pages');
        } else {
            //Get detail page
            $arrPageDetail = Core_Model_Pages::getDetailPageById($id);
            if (empty($arrPageDetail)) {
                $this->_redirect(SITE_URL . '/backend/pages');
            }
            $arrReturn = array(
                'error' => 1,
                'is_post' => 0,
                'msg' => 'Có lỗi xảy ra.',
                'data' => array(
                    'intId' => $arrPageDetail['id'],
                    'strPageKey' => $arrPageDetail['page_key'],
                    'strName' => $arrPageDetail['name'],
                    'strMetaTitle' => $arrPageDetail['meta_title'],
                    'strMetaDesc' => $arrPageDetail['meta_desc'],
                    'strMetaKeyword' => $arrPageDetail['meta_keyword'],
                    'ckeditor' => $arrPageDetail['content'],
                    //'ckeditor' => str_ireplace('data-original=','src=',$arrPageDetail['content']),
                    'strDate' => date('d/m/Y H:i:s', $arrPageDetail['ptime']),
                    'intStatus' => $arrPageDetail['status']
                )
            );
            if ($this->_request->isPost()) {
                //Request params
                $arrParamsRequest = $this->_request->getParams();
                $checkPages = Core_Validate_Pages::checkData($arrParamsRequest,'edit');
                if ($checkPages['error'] == 1) {
                    $arrReturn = array_merge($arrReturn, $checkPages);
                } else {
                    $name = trim(strip_tags(str_replace('&nbsp;', ' ', $arrParamsRequest['strName'])));
                    $nameSeo = strtolower(Core_Utils::setSeoLink($name));
                    $shareUrl = $nameSeo . '.html';
                    $pregfind = array('#<img[^>]+src=(\'|")(.*)(\\1).*>#siU');
                    $response['data']['content'] = preg_replace_callback($pregfind, function ($match) {
                        return Core_Utils::setImageContent($match,'http://nhathepsaigon.vn','nhathepsaigon.vn','www.nhathepsaigon.vn');
                    }, $arrParamsRequest['ckeditor']);
                    //Lazyload image
                    //$arrParamsRequest['ckeditor'] = str_ireplace('src=','data-original=',$response['data']['content']);
                    //$response['data']['content'] = preg_replace('/\<[\/]?(table|tbody|tr|td)([^\>]*)\>/i', '', $response['data']['content']);
                    // $arrParams update page
                    $arrParams = array(
                        'strPageKey' => $arrPageDetail['page_key'],
                        'strName' => $arrPageDetail['name'], //$this->view->escape($name),
                        'strShareUrl' => $arrPageDetail['share_url'], //$shareUrl,
                        'strMetaTitle' => $this->view->escape(trim(strip_tags($arrParamsRequest['strMetaTitle']))),
                        'strMetaDesc' => $this->view->escape(trim(strip_tags($arrParamsRequest['strMetaDesc']))),
                        'strMetaKeyword' => $this->view->escape(trim(strip_tags($arrParamsRequest['strMetaKeyword']))),
                        'ckeditor' => $response['data']['content'], //$arrParamsRequest['ckeditor'],
                        'intPublishTime' => strtotime(str_ireplace('/', '-', $arrParamsRequest['strDate'])),
                        'intStatus' => $arrParamsRequest['intStatus'],
                        'intUserId' => NULL,
                        'intId' => $arrPageDetail['id']
                    );
                    //Update page
                    $isUpdate = Core_Model_Pages::updatePage($arrParams);
                    if ($isUpdate > 0) {
                        $arrReturn['error'] = 0;
                        $arrReturn['msg'] = 'Cập nhật trang tĩnh thành công.';
                    } else {
                        $arrReturn['msg'] = 'Cập nhật trang tĩnh không thành công.';
                    }
                }
                $arrReturn['is_post'] = 1;
                $arrReturn['data'] = array(
                    'intId' => $arrPageDetail['id'],
                    'strPageKey' => $arrPageDetail['page_key'],
                    'strName' => $arrPageDetail['name'],
                    'strMetaTitle' => $arrParamsRequest['strMetaTitle'],
                    'strMetaDesc' => $arrParamsRequest['strMetaDesc'],
                    'strMetaKeyword' => $arrParamsRequest['strMetaKeyword'],
                    'ckeditor' => $arrParamsRequest['ckeditor'],
                    'strDate' => $arrParamsRequest['strDate'],
                    'intStatus' => $arrParamsRequest['intStatus']
                );
            }
        }
        //Assign to view
        $this->view->assign(array(
                'arrReturn' => $arrReturn
            )
        );
        //Set title
        $this->view->headTitle()->append('Sửa trang tĩnh');
        //append script
        $this->view->headScript()->appendFile(STATIC_URL . '/library/plugins/ckeditor-4.5.9/ckeditor.js')
            ->appendFile(STATIC_URL . '/be/js/pages/edit.js');
    }

    /**
     * @author      :   PhongTX
     * @name    :   ajaxUpdateAction
     */
    public function ajaxUpdateAction()
    {
        //disable layout + render
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $intStartTime = microtime(true);

        //Get $id
        $id = $this->_request->getParam('intId', 0);
        //Get status
        $status = $this->_request->getParam('intStatus', 0);
        //Get Name
        $strName = $this->_request->getParam('strName', '');
        $lab = '';
        switch ($status) {
            case PAGES_STATUS_DELETE:
                $lab = 'Xóa';
                break;
            case PAGES_STATUS_ACTIVE:
                $lab = 'Active';
                break;
            case PAGES_STATUS_INACTIVE:
                $lab = 'Inactive';
                break;
        }
        $arrReturn = array('error' => 1, 'msg' => $lab . ' trang tĩnh [' . $strName . '] không thành công.');
        //Check permission - Get menu by menu code
        $arrMenuByMenuCode = Core_Model_Menus::getDetailMenuByMenuCode($this->view->moduleName.'_'.$this->view->controllerName.'_'.$this->view->actionName);
        if(in_array($arrMenuByMenuCode['menu_id'],$this->view->user['permission'][PERMISSION_DEL])){
            if ($id > 0) {
                // $arrParams update article
                $arrParams = array(
                    'intStatus' => $status,
                    'intId' => $id
                );
                //Update article
                $isUpdate = Core_Model_Pages::updatePage($arrParams);
                if ($isUpdate > 0) {
                    $arrReturn['error'] = 0;
                    $arrReturn['msg'] = $lab . ' trang tĩnh [' . $strName . '] thành công.';
                }
            }
        }else{
            $arrReturn = array('error' => 1, 'msg' => 'Bạn không có quyền ' . $lab . ' trang tĩnh [' . $strName . '].');
        }
        /*End check permission*/
        echo Zend_Json::encode($arrReturn);
        exit;
    }
}

?>