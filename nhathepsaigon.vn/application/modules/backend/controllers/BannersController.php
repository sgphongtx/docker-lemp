<?php

/**
 * @author      :   PhongTX
 * @name        :   BannersController
 * @copyright   :   Fpt Online
 * @todo        :   Banners Controller
 */
class Backend_BannersController extends Zend_Controller_Action
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
        //Get category id
        $intCategoryId = (int)$this->_request->getParam('categoryid', 0);
        //Get page
        $intPage = max((int)$this->_request->getParam('page', 1), 1);
        //Get limit
        $intLimit = max((int)$this->_request->getParam('limit', DEFAULT_LIMIT), DEFAULT_LIMIT);
        //Get offset
        $offset = ($intPage - 1) * $intLimit;

        $arrParams = array(
            'intCategoryId' => $intCategoryId == 0 ? NULL : $intCategoryId,
            'strStatus' => '1,2',//1: active, 2: inactive
            'intOffset' => ($offset > 0) ? $offset : 0,
            'intLimit' => $intLimit,
        );
        $arrReturn = Core_Model_Banners::getListBanners($arrParams);
        $arrPosition = $arrCategory = array();
        if(!empty($arrReturn['data'])){
            foreach($arrReturn['data'] as $row){
                $arrPosition[$row['position_id']] = Core_Model_Positions::getDetailPositionById($row['position_id']);
                $arrCategory[$row['category_id']] = Core_Model_Categories::getDetailCategoryById($row['category_id']);
            }
        }
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
        //Return page
        $arrReturn['intPage'] = $intPage;
        //Return limit
        $arrReturn['intLimit'] = $intLimit;
        //Return offset
        $arrReturn['intOffset'] = $offset;
        //Assign to view
        $this->view->assign(array(
                'arrData' => $arrReturn,
                'arrPosition' => $arrPosition,
                'arrCategory' => $arrCategory,
                'intCategoryId' => $intCategoryId,
                'arrCate' => $arrResult
            )
        );
        //Set title
        $this->view->headTitle()->append('Quảng cáo');
        //Append css
        $this->view->headLink()->appendStylesheet(STATIC_URL . '/library/plugins/select2/select2-bootstrap.css')
            ->appendStylesheet(STATIC_URL . '/library/plugins/select2/select2.css');
        //append script
        $this->view->headScript()->appendFile(STATIC_URL . '/library/plugins/ckeditor-4.5.9/ckeditor.js')
            ->appendFile(STATIC_URL . '/library/plugins/select2/select2.min.js')
            ->appendFile(STATIC_URL . '/be/js/banners/add.js');
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
            //$checkBanner = Core_Validate_Banners::checkBanner($arrParamsRequest);
            $checkBanner = true;
            if ($checkBanner['error'] == 1) {
                echo Zend_Json::encode($checkBanner);
                exit;
            } else {
                //Add Banner
                if($arrParamsRequest['intPositionIdEdit'] == 0 && $arrParamsRequest['intCategoryIdEdit'] == 0) {
                    $arrParams = array(
                        'strName' => trim(strip_tags($arrParamsRequest['strName'])),
                        'intCategoryId' => $arrParamsRequest['intCategoryId'],
                        'intPositionId' => $arrParamsRequest['intPositionId'],
                        'strContent' => $arrParamsRequest['strContent'],
                        'intStatus' => 1 //status = 0: Delete, status = 1: Active, status = 2: Inactive
                    );
                    $intId = Core_Model_Banners::insertBanner($arrParams);
                    if ($intId > 0) {
                        $arrReturn['error'] = 0;
                        $arrReturn['msg'] = 'Tạo quảng cáo thành công';
                    }
                //Update Banners
                }else{
                    $arrParams = array(
                        'strName' => trim(strip_tags($arrParamsRequest['strName'])),
                        'intCategoryId' => $arrParamsRequest['intCategoryId'],
                        'intPositionId' => $arrParamsRequest['intPositionId'],
                        'strContent' => $arrParamsRequest['strContent'],
                    );
                    $isUpdate = Core_Model_Banners::updateBanner($arrParams);
                    if ($isUpdate > 0) {
                        $arrReturn['error'] = 0;
                        $arrReturn['intCategoryId'] = $arrParamsRequest['intCategoryId'];
                        $arrReturn['msg'] = 'Cập nhật quảng cáo thành công';
                    }
                }
                echo Zend_Json::encode($arrReturn);
                exit;
            }
        }
    }

    /**
     * @todo - Action ajaxGetDetailBanner
     * @author PhongTX
     */
    public function ajaxGetDetailBannerAction()
    {
        //disable layout + render
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->_request->isPost()) {
            //Get intBannerId
            $intPositionId = $this->_request->getParam('intPositionId', 0);
            $intCategoryId = $this->_request->getParam('intCategoryId', 0);
            //Define $arrBannerDetail
            $arrBannerDetail = array();
            $arrBannerDetail = Core_Model_Banners::getDetailBannerById(array('intPositionId' => $intPositionId, 'intCategoryId' => $intCategoryId));
            $arrParams = array(
                'strStatus' => 1,//1: active, 2: inactive
                'intOffset' => NULL,
                'intLimit' => NULL,
            );
            $arrPositions = Core_Model_Positions::getListPositions($arrParams);
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
            // Assign to view
            $this->view->assign(array(
                'arrBannerDetail' => $arrBannerDetail,
                'arrCategories' => $arrResult,
                'arrPositions' => $arrPositions['data'],
                'intCategoryId' => $intCategoryId
            ));
            //Render view
            $response['html'] = $this->view->render('banners/add.phtml');
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
                'intCategoryId' => $arrParamsRequest['intCategoryId'],
                'intPositionId' => $arrParamsRequest['intPositionId'],
                'intStatus' => $arrParamsRequest['intStatus']
            );
            $isUpdate = Core_Model_Banners::updateBanner($arrParams);
            if ($isUpdate) {
                $label = $arrParamsRequest['intStatus']==1?'Active':'Inactive';
                $arrReturn['error'] = 0;
                $arrReturn['msg'] = $label.' quảng cáo [' . $arrParamsRequest['strName'] . '] thành công';
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
                'intCategoryId' => $arrParamsRequest['intCategoryId'],
                'intPositionId' => $arrParamsRequest['intPositionId'],
                'intStatus' => 0 //Xoa
            );
            $isUpdate = Core_Model_Banners::updateBanner($arrParams);
            if ($isUpdate) {
                $arrReturn['error'] = 0;
                $arrReturn['msg'] = 'Xóa quảng cáo [' . $arrParamsRequest['strName'] . '] thành công';
            }
            //Return Json
            echo Zend_Json::encode($arrReturn);
            exit;
        }
    }

    /**
     * @todo - Action text
     * @author PhongTX
     */
    public function textAction()
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
        $arrReturn = Core_Model_Banners::getListTextBanners($arrParams);
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
        $this->view->headTitle()->append('Text banners');
        //Append css
        //$this->view->headLink()->appendStylesheet(STATIC_URL . '/library/plugins/select2/select2-bootstrap.css');
        //append script
        $this->view->headScript()->appendFile(STATIC_URL . '/be/js/banners/textadd.js');
    }

    /**
     * @todo - Action add
     * @Author PhongTX
     */
    public function ajaxAddTextBannerAction()
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
            $checkBanner = Core_Validate_Banners::checkTextBanner($arrParamsRequest);
            if ($checkBanner['error'] == 1) {
                echo Zend_Json::encode($checkBanner);
                exit;
            } else {
                //Add Banner
                if($arrParamsRequest['intId'] == 0) {
                    //Upload hinh dai dien
                    $max_file_size = MAX_SIZE_IMG; //Max size 5M
                    $up_source_dir = IMAGE_UPLOAD_DIR . '/thumbnails/originals/';
                    $up_thumb_dir = IMAGE_UPLOAD_DIR . '/thumbnails/thumbs/';
                    // Check the upload
                    if (is_uploaded_file($_FILES["strImage"]["tmp_name"]) && $_FILES["strImage"]["error"] == 0) {
                        //Check extension
                        $path_parts = pathinfo($_FILES["strImage"]['name']);
                        $ext = strtolower($path_parts['extension']);
                        if ($ext == 'jpg' || $ext == 'png' || $ext == 'gif' || $ext == 'jpeg') {
                            $name = $path_parts['filename'];
                            //generate seo string
                            $name = Core_Utils::createSeoStr($name);
                            //Get time
                            $time = time();
                            //make new name {oldname}-{time}.{ext}
                            $file_id = $name . '-' . $time . '.' . $ext;
                            $year = array(date('Y'), date('m'));
                            if (!is_dir($up_source_dir)) {
                                mkdir($up_source_dir, 0755, true);
                            }
                            foreach ($year as $row) {
                                $up_source_dir .= $row;
                                if (!is_dir($up_source_dir)) {
                                    mkdir($up_source_dir, 0755, true);
                                }
                                $up_source_dir .= DIRECTORY_SEPARATOR;
                            }
                            if (move_uploaded_file($_FILES["strImage"]['tmp_name'], $up_source_dir . $file_id)) {
                                $strFileUrl = date('Y') . '/' . date('m') . '/' . $file_id;
                                if (!is_dir($up_thumb_dir)) {
                                    mkdir($up_thumb_dir, 0755, true);
                                }
                                foreach ($year as $row) {
                                    $up_thumb_dir .= $row;
                                    if (!is_dir($up_thumb_dir)) {
                                        mkdir($up_thumb_dir, 0755, true);
                                    }
                                    $up_thumb_dir .= DIRECTORY_SEPARATOR;
                                }
                                $config       = Core_Global::getApplicationIni();
                                $arrImg = $config['images'];
                                //Resize image 490x294
                                Core_Utils::resize_crop_image($arrImg[$arrImg['size1']][0], $arrImg[$arrImg['size1']][1], $up_source_dir . $file_id, IMAGE_UPLOAD_DIR . '/thumbnails/thumbs/' . date('Y') . '/' . date('m') . '/' . $name . '-' . $time . '_'.$arrImg['size1'].'.' . $ext);
                                //Resize image 140x78
                                Core_Utils::resize_crop_image($arrImg[$arrImg['size3']][0], $arrImg[$arrImg['size3']][1], $up_source_dir . $file_id, IMAGE_UPLOAD_DIR . '/thumbnails/thumbs/' . date('Y') . '/' . date('m') . '/' . $name . '-' . $time . '_'.$arrImg['size3'].'.' . $ext);
                                $title = trim(strip_tags(str_replace('&nbsp;', ' ', $arrParamsRequest['strTitle'])));
                                $arrParams = array(
                                    'strTitle' => $title,
                                    'strThumbUrl' => $strFileUrl,
                                    'strShareUrl' => $arrParamsRequest['strShareUrl'],
                                    'intStatus' => 1 //status = 0: Delete, status = 1: Active, status = 2: Inactive
                                );
                                $intId = Core_Model_Banners::insertTextBanner($arrParams);
                                if ($intId > 0) {
                                    $this->_redirect(SITE_URL . '/backend/banners/text');
                                }else{
                                    $arrReturn['msg'] = 'Tạo quảng cáo không thành công.';
                                }
                            } else {
                                $arrReturn['msg'] = 'Không upload được hình đại diện.';
                            }
                        } else {
                            $arrReturn['msg'] = 'Sai định dạng file hình đại diện.';
                        }
                    } else {
                        $arrReturn['msg'] = 'Không upload được hình đại diện.';
                    }
                    //Update Banners
                }else{
                    if (isset($_FILES["strImage"]['tmp_name']) && !empty($_FILES["strImage"]['tmp_name'])) {
                        //Upload hinh dai dien
                        $max_file_size = MAX_SIZE_IMG; //Max size 5M
                        $up_source_dir = IMAGE_UPLOAD_DIR . '/thumbnails/originals/';
                        $up_thumb_dir = IMAGE_UPLOAD_DIR . '/thumbnails/thumbs/';
                        if (is_uploaded_file($_FILES["strImage"]["tmp_name"]) && $_FILES["strImage"]["error"] == 0) {
                            $path_parts = pathinfo($_FILES["strImage"]['name']);
                            $ext = strtolower($path_parts['extension']);
                            if ($ext == 'jpg' || $ext == 'png' || $ext == 'gif' || $ext == 'jpeg') {
                                $name = $path_parts['filename'];
                                //generate seo string
                                $name = Core_Utils::createSeoStr($name);
                                //Get time
                                $time = time();
                                //make new name {oldname}-{time}.{ext}
                                $file_id = $name . '-' . $time . '.' . $ext;
                                $year = array(date('Y'), date('m'));
                                if (!is_dir($up_source_dir)) {
                                    mkdir($up_source_dir, 0755, true);
                                }
                                foreach ($year as $row) {
                                    $up_source_dir .= $row;
                                    if (!is_dir($up_source_dir)) {
                                        mkdir($up_source_dir, 0755, true);
                                    }
                                    $up_source_dir .= DIRECTORY_SEPARATOR;
                                }
                                if (move_uploaded_file($_FILES["strImage"]['tmp_name'], $up_source_dir . $file_id)) {
                                    $strFileUrl = date('Y') . '/' . date('m') . '/' . $file_id;
                                    if (!is_dir($up_thumb_dir)) {
                                        mkdir($up_thumb_dir, 0755, true);
                                    }
                                    foreach ($year as $row) {
                                        $up_thumb_dir .= $row;
                                        if (!is_dir($up_thumb_dir)) {
                                            mkdir($up_thumb_dir, 0755, true);
                                        }
                                        $up_thumb_dir .= DIRECTORY_SEPARATOR;
                                    }
                                    $config       = Core_Global::getApplicationIni();
                                    $arrImg = $config['images'];
                                    //Resize image 490x294
                                    Core_Utils::resize_crop_image($arrImg[$arrImg['size1']][0], $arrImg[$arrImg['size1']][1], $up_source_dir . $file_id, IMAGE_UPLOAD_DIR . '/thumbnails/thumbs/' . date('Y') . '/' . date('m') . '/' . $name . '-' . $time . '_'.$arrImg['size1'].'.' . $ext);
                                    //Resize image 140x78
                                    Core_Utils::resize_crop_image($arrImg[$arrImg['size3']][0], $arrImg[$arrImg['size3']][1], $up_source_dir . $file_id, IMAGE_UPLOAD_DIR . '/thumbnails/thumbs/' . date('Y') . '/' . date('m') . '/' . $name . '-' . $time . '_'.$arrImg['size3'].'.' . $ext);
                                } else {
                                    $arrReturn['msg'] = 'Không upload được hình đại diện.';
                                }
                            } else {
                                $arrReturn['msg'] = 'Sai định dạng hình đại diện.';
                            }
                        } else {
                            $arrReturn['msg'] = 'Không upload được hình đại diện.';
                        }
                    }
                    $title = trim(strip_tags(str_replace('&nbsp;', ' ', $arrParamsRequest['strTitle'])));
                    $arrParams = array(
                        'strTitle' => $title,
                        'strThumbUrl' => $strFileUrl,
                        'strShareUrl' => $arrParamsRequest['strShareUrl'],
                        'intStatus' => NULL, //status = 0: Delete, status = 1: Active, status = 2: Inactive
                        'intId' => $arrParamsRequest['intId']
                    );
                    $isUpdate = Core_Model_Banners::updateTextBanner($arrParams);
                    if ($isUpdate > 0) {
                        $this->_redirect(SITE_URL . '/backend/banners/text');
                    }else{
                        $arrReturn['msg'] = 'Cập nhật quảng cáo không thành công.';
                    }
                }
                $this->_redirect(SITE_URL . '/backend/banners/text');
            }
        }
    }

    /**
     * @todo - Action ajaxGetDetailBanner
     * @author PhongTX
     */
    public function ajaxGetDetailTextBannerAction()
    {
        //disable layout + render
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->_request->isPost()) {
            //Get intBannerId
            $intId = $this->_request->getParam('intId', 0);
            //Define $arrBannerDetail
            $arrBannerDetail = array();
            $arrBannerDetail = Core_Model_Banners::getDetailTextBannerById($intId);
            // Assign to view
            $this->view->assign(array(
                'arrBannerDetail' => $arrBannerDetail,
                'intId' => $intId
            ));
            //Render view
            $response['html'] = $this->view->render('banners/textadd.phtml');
            //Return Json
            echo Zend_Json::encode($response);
            exit;
        }
    }

    /**
     * @todo - Action ajaxUpdate
     * @author PhongTX
     */
    public function ajaxUpdateTextBannerAction()
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
                'intStatus' => $arrParamsRequest['intStatus']
            );
            $isUpdate = Core_Model_Banners::updateTextBanner($arrParams);
            if ($isUpdate) {
                $label = $arrParamsRequest['intStatus']==1?'Active':'Inactive';
                $arrReturn['error'] = 0;
                $arrReturn['msg'] = $label.' bài viết [' . $arrParamsRequest['strTitle'] . '] thành công';
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
    public function ajaxDelTextBannerAction()
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
                'intStatus' => 0 //Xoa
            );
            $isUpdate = Core_Model_Banners::updateTextBanner($arrParams);
            if ($isUpdate) {
                $arrReturn['error'] = 0;
                $arrReturn['msg'] = 'Xóa bài viết [' . $arrParamsRequest['strTitle'] . '] thành công';
            }
            //Return Json
            echo Zend_Json::encode($arrReturn);
            exit;
        }
    }

}

?>