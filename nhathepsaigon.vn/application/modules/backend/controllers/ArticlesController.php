<?php

/**
 * @author      :   PhongTX
 * @name        :   ArticlesController
 * @copyright   :   Fpt Online
 * @todo        :   Articles Controller
 */
class Backend_ArticlesController extends Zend_Controller_Action
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
            STATUS_DA_DUYET => 'Đã duyệt',
            STATUS_CHO_DUYET => 'Chờ duyệt',
            STATUS_LUU_NHAP => 'Lưu nháp'
        );
        //Get page
        $intPage = max((int)$this->_request->getParam('page', 1), 1);
        //Get limit
        $intLimit = max((int)$this->_request->getParam('limit', DEFAULT_LIMIT), DEFAULT_LIMIT);
        //Get offset
        $offset = ($intPage - 1) * $intLimit;
        //Get id
        $intId = (int)$this->_request->getParam('keyid', 0);
        //Get id
        $strKeyword = $this->_request->getParam('keyword', null);
        //Get category id
        $intCategoryId = (int)$this->_request->getParam('categoryid', 0);
        //Get status
        $intStatus = (int)$this->_request->getParam('status', 0);
        //Get pageview
        $intPageview = (int)$this->_request->getParam('pageview', 0);
        //Get user id
        $intUserId = (int)$this->_request->getParam('userid', 0);
        $fromdate = $this->_request->getParam('form_date', null);
        $todate = $this->_request->getParam('to_date', null);
        $tempFromdate = $fromdate != '' ? strtotime(preg_replace("/(\d+)\/(\d+)\/(\d+)/", "\\3-\\2-\\1", $fromdate)) : null;
        $tempTodate = $todate != '' ? strtotime(preg_replace("/(\d+)\/(\d+)\/(\d+)/", "\\3-\\2-\\1", $todate)) + 86399 : null;
        //Check status
        if ($intStatus == 0) {
            $strStatus = '1,2,3';
        } else {
            $strStatus = "'" . $intStatus . "'";
        }
        //Define arrParams
        $arrParams = array(
            'intId' => $intId > 0 ? $intId : NULL,
            'strKeyword' => isset($strKeyword) && $strKeyword != '' ? $strKeyword : NULL,
            'intFromdate' => $tempFromdate,
            'intTodate' => $tempTodate,
            'intCategoryId' => $intCategoryId == 0 ? NULL : $intCategoryId,
            'strStatus' => $strStatus,
            'intUserId' => $intUserId == 0 ? NULL : $intUserId,
            'intOffset' => ($offset > 0) ? $offset : 0,
            'intLimit' => $intLimit,
            'intPageview' => $intPageview == 0 ? NULL : $intPageview
        );
        //Call function getListArticle
        $arrReturn = Core_Model_Articles::getListArticle($arrParams);
        $arrUser = $arrUserId = array();
        if (!empty($arrReturn['data'])) {
            foreach ($arrReturn['data'] as $row) {
                $userDetail = Core_Model_Users::getDetailUserById($row['user_id']);
                $userApproveDetail = Core_Model_Users::getDetailUserById($row['user_approve_id']);
                $arrUser[$row['article_id']]['user'] = $userDetail;
                $arrUser[$row['article_id']]['user_approve'] = $userApproveDetail;
            }
        }
        $arrUserId = Core_Model_Users::getListUsers(array('strStatus' => 1, 'intLimit' => 500, 'intOffset' => 0));
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

        $arrReturn['intOffset'] = $offset;

        //Assign to view
        $this->view->assign(array(
            'arrData' => $arrReturn,
            'arrStatus' => $arrStatus,
            'intStatus' => $intStatus,
            'fromdate' => $fromdate,
            'todate' => $todate,
            'arrCate' => $arrResult,
            'intCategoryId' => $intCategoryId,
            'arrUser' => $arrUser,
            'intId' => $intId,
            'strKeyword' => $strKeyword,
            'intUserId' => $intUserId,
            'arrUserId' => $arrUserId['data'],
            'intPageview' => $intPageview,
            'fromdate' => $fromdate,
            'todate' => $todate
        ));
        $strTitle = 'Danh sách bài viết';
        switch($intStatus){
            case STATUS_DA_DUYET:
                $strTitle = 'Danh sách bài viết đã duyệt';
                break;
            case STATUS_CHO_DUYET:
                $strTitle = 'Danh sách bài viết chờ duyệt';
                break;
            case STATUS_LUU_NHAP:
                $strTitle = 'Danh sách bài viết lưu nháp';
                break;
        }
        //Set title
        $this->view->headTitle()->append($strTitle);
        //append script
        $this->view->headScript()->appendFile(STATIC_URL . '/be/js/articles/index.js');
        /*
         //Append css
        $this->view->headLink()->appendStylesheet(STATIC_URL . '/library/plugins/select2/select2-bootstrap.css')
            ->appendStylesheet(STATIC_URL . '/library/plugins/select2/select2.css');
        //append script
        $this->view->headScript()->appendFile(STATIC_URL . '/library/plugins/select2/select2.min.js')
            ->appendFile(STATIC_URL . '/be/js/articles/index.js');
         */
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
        $intType = $this->_request->getParam('type',1);
        $strLink = str_ireplace('www.','',$this->_request->getParam('link',''));
        $arrResultCrawler = Core_Model_Articles::crawler($strLink);
        $arrReturn = array(
            'error' => 1,
            'is_post' => 0,
            'msg' => 'Có lỗi xảy ra.',
            'data' => array(
                'strThumbUrl' => '',
                'strTitle' => isset($arrResultCrawler['data']['title'])?$this->view->escape(trim(strip_tags($arrResultCrawler['data']['title']))):'',
                'strDesc' => isset($arrResultCrawler['data']['lead'])?$this->view->escape(trim(strip_tags($arrResultCrawler['data']['lead']))):'',
                'strMetaTitle' => isset($arrResultCrawler['data']['title'])?$this->view->escape(trim(strip_tags($arrResultCrawler['data']['title']))):'',
                'strMetaDesc' => isset($arrResultCrawler['data']['lead'])?$this->view->escape(trim(strip_tags($arrResultCrawler['data']['lead']))):'',
                'strMetaKeyword' => isset($arrResultCrawler['data']['title'])?$this->view->escape(trim(strip_tags($arrResultCrawler['data']['title']))):'',
                'ckeditor' => isset($arrResultCrawler['data']['content'])?$arrResultCrawler['data']['content']:'',
                'intCate' => 0,
                'arrTags' => array(),
                'intIsComment' => 1,
                'intCommentType' => 1,
                'strDate' => date('d/m/Y H:i:s', time()),
                'listTags' => Zend_Json::encode(array()),
                'intStatus' => 1, //Cho duyet
                'intType' => $intType,
                'strLinkVideo' => NULL
            )
        );
        $arrReturnTagDetail = array();
        if ($this->_request->isPost()) {
            //Request params
            $arrParamsRequest = $this->_request->getParams();
            $checkArticles = Core_Validate_Articles::checkData($arrParamsRequest,'add');
            if ($checkArticles['error'] == 1) {
                $arrReturn = array_merge($arrReturn, $checkArticles);
            } else {
                //Upload hinh dai dien
                $max_file_size = MAX_SIZE_IMG; //Max size 5M
                $up_source_dir = IMAGE_UPLOAD_DIR . '/thumbnails/originals/';
                $up_thumb_dir = IMAGE_UPLOAD_DIR . '/thumbnails/thumbs/';
                // Check the upload
                if (is_uploaded_file($_FILES["strImage"]["tmp_name"]) && $_FILES["strImage"]["error"] == 0) {
                    //Check extension
                    $path_parts = pathinfo($_FILES["strImage"]['name']);
                    $ext = strtolower($path_parts['extension']);
                    if ($ext != 'jpg' && $ext != 'png' && $ext != 'gif' && $ext != 'jpeg') {
                        $arrReturn['msg'] = 'Sai định dạng hình đại diện.';
                    } else {
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
                            $arrOption = array(
                                'max_width' => $arrParamsRequest['intImgW'],
                                'max_height' => $arrParamsRequest['intImgH'],
                                'crop' => 1,
                                'crop_x' => abs($arrParamsRequest['intCropX']),
                                'crop_y' => abs($arrParamsRequest['intCropY']),
                                'scale' => $arrParamsRequest['intZoom']
                            );
                            //Resize image 354x212
                            Core_Utils::scaleCropImage($arrOption, $arrImg[$arrImg['size1']][0], $arrImg[$arrImg['size1']][1], $up_source_dir . $file_id, IMAGE_UPLOAD_DIR . '/thumbnails/thumbs/' . date('Y') . '/' . date('m') . '/' . $name . '-' . $time . '_'.$arrImg['size1'].'.' . $ext);
                            //Resize image 250x140
                            //Core_Utils::scaleCropImage($arrOption, $arrImg[$arrImg['size2']][0], $arrImg[$arrImg['size2']][1], $up_source_dir . $file_id, IMAGE_UPLOAD_DIR . '/thumbnails/thumbs/' . date('Y') . '/' . date('m') . '/' . $name . '-' . $time . '_'.$arrImg['size2'].'.' . $ext);
                            //Resize image 136x76
                            //Core_Utils::scaleCropImage($arrOption, $arrImg[$arrImg['size3']][0], $arrImg[$arrImg['size3']][1], $up_source_dir . $file_id, IMAGE_UPLOAD_DIR . '/thumbnails/thumbs/' . date('Y') . '/' . date('m') . '/' . $name . '-' . $time . '_'.$arrImg['size3'].'.' . $ext);
                            //Resize image 60x50
                            //Core_Utils::scaleCropImage($arrOption, $arrImg[$arrImg['size4']][0], $arrImg[$arrImg['size4']][1], $up_source_dir . $file_id, IMAGE_UPLOAD_DIR . '/thumbnails/thumbs/' . date('Y') . '/' . date('m') . '/' . $name . '-' . $time . '_'.$arrImg['size4'].'.' . $ext);

                            $title = trim(strip_tags(str_replace('&nbsp;', ' ', $arrParamsRequest['strTitle'])));
                            $arrTags = explode(',', $arrParamsRequest['strTags']);
                            $arrTags = array_filter(array_unique($arrTags));
                            /*$pregfind = array('#<img[^>]+src=(\'|")(.*)(\\1).*>#siU');
                            $arrParamsRequest['ckeditor'] = preg_replace_callback($pregfind, function ($match) {
                                return Core_Utils::getimage($match,'backend');
                            }, $arrParamsRequest['ckeditor']);*/
                            //$arrParamsRequest['ckeditor'] = Core_Filter::purify($arrParamsRequest['ckeditor']);
                            $pregfind = array('#<img[^>]+src=(\'|")(.*)(\\1).*>#siU');
                            $response['data']['content'] = preg_replace_callback($pregfind, function ($match) {
                                return Core_Utils::setImageContent($match,'http://nhathepsaigon.vn','nhathepsaigon.vn','www.nhathepsaigon.vn');
                            }, $arrParamsRequest['ckeditor']);
                            //Lazyload image
                            //$arrParamsRequest['ckeditor'] = str_ireplace('src=','data-original=',$response['data']['content']);
                            $response['data']['content'] = preg_replace('/\<[\/]?(table|tbody|tr|td)([^\>]*)\>/i', '', $response['data']['content']);
                            // $arrParams insert article
                            $arrParams = array(
                                'intCate' => $arrParamsRequest['intCate'],
                                'strThumbUrl' => $strFileUrl,
                                'strShareUrl' => NULL,
                                'strTitle' => $this->view->escape($title),
                                'strDesc' => $this->view->escape(trim(strip_tags($arrParamsRequest['strDesc']))),
                                'strMetaTitle' => $this->view->escape(trim(strip_tags($arrParamsRequest['strMetaTitle']))),
                                'strMetaDesc' => $this->view->escape(trim(strip_tags($arrParamsRequest['strMetaDesc']))),
                                'strMetaKeyword' => $this->view->escape(trim(strip_tags($arrParamsRequest['strMetaKeyword']))),
                                'ckeditor' => $response['data']['content'], //$arrParamsRequest['ckeditor'],
                                'intIsComment' => $arrParamsRequest['intIsComment'],
                                'intCommentType' => $arrParamsRequest['intCommentType'],
                                'intPublishTime' => strtotime(str_ireplace('/', '-', $arrParamsRequest['strDate'])),
                                'intStatus' => $arrParamsRequest['intStatus'],
                                'intUserId' => $this->view->user['user_id'],
                                'intUserApproveId' => $arrParamsRequest['intStatus'] == 1 ? $this->view->user['user_id'] : NULL,
                                'intType' => $intType,
                                'strLinkVideo' => isset($arrParamsRequest['strLinkVideo'])?$arrParamsRequest['strLinkVideo']:NULL
                            );
                            // insert article
                            $intArticleId = Core_Model_Articles::insertArticle($arrParams);
                            if ($intArticleId > 0) {
                                $cateDetail = Core_Model_Categories::getDetailCategoryById($arrParamsRequest['intCate']);
                                $titleSeo = strtolower(Core_Utils::setSeoLink($title));
                                $shareUrl = $cateDetail['cate_link'] . '/' . $titleSeo . '-' . $intArticleId . '.html';
                                $arrParamsArticleUpdate = array(
                                    'intCate' => NULL,
                                    'strThumbUrl' => NULL,
                                    'strShareUrl' => $shareUrl,
                                    'strTitle' => NULL,
                                    'strDesc' => NULL,
                                    'strMetaTitle' => NULL,
                                    'strMetaDesc' => NULL,
                                    'strMetaKeyword' => NULL,
                                    'ckeditor' => NULL,
                                    'intIsComment' => NULL,
                                    'intCommentType' => NULL,
                                    'intPublishTime' => NULL,
                                    'intStatus' => NULL,
                                    'intUserId' => NULL,
                                    'intUserApproveId' => NULL,
                                    'intArticleId' => $intArticleId,
                                    'intType' => NULL,
                                    'strLinkVideo' => NULL
                                );
                                Core_Model_Articles::updateArticle($arrParamsArticleUpdate);
                                if (!empty($arrTags)) {
                                    foreach ($arrTags as $row) {
                                        if ($row > 0) {
                                            //Get detail tag
                                            $arrTagDetail = Core_Model_Tags::getDetailTagById($row);
                                            $arrReturnTagDetail[$row] = $arrTagDetail['tag_name'];
                                            //Check tag
                                            $isCheck = Core_Model_Tags::checkTagArticle(array('intArticleId' => $intArticleId, 'intTagId' => $row));
                                            if ($isCheck == 0) {
                                                Core_Model_Tags::insertTagArticle(array('intArticleId' => $intArticleId, 'intTagId' => $row));
                                            }
                                        }
                                    }
                                }
                                $arrReturn['error'] = 0;
                                $arrReturn['msg'] = 'Thêm bài viết mới thành công.';
                            } else {
                                $arrReturn['msg'] = 'Thêm bài viết mới không thành công.';
                            }
                        } else {
                            $arrReturn['msg'] = 'Sai định dạng file hình đại diện.';
                        }
                    }
                } else {
                    $arrReturn['msg'] = 'Không upload được hình đại diện.';
                }
            }
            $arrReturn['is_post'] = 1;
            $arrReturn['data'] = array(
                'strThumbUrl' => $arrParamsRequest['strThumbUrl'],
                'strTitle' => $arrParamsRequest['strTitle'],
                'strDesc' => $arrParamsRequest['strDesc'],
                'strMetaTitle' => $arrParamsRequest['strMetaTitle'],
                'strMetaDesc' => $arrParamsRequest['strMetaDesc'],
                'strMetaKeyword' => $arrParamsRequest['strMetaKeyword'],
                'ckeditor' => $arrParamsRequest['ckeditor'],
                'intCate' => $arrParamsRequest['intCate'],
                'arrTags' => $arrReturnTagDetail,
                'intIsComment' => $arrParamsRequest['intIsComment'],
                'intCommentType' => $arrParamsRequest['intCommentType'],
                'strDate' => $arrParamsRequest['strDate'],
                'listTags' => Zend_Json::encode($arrTags),
                'intStatus' => $arrParamsRequest['intStatus'],
                'intType' => $intType,
                'strLinkVideo' => $arrParamsRequest['strLinkVideo']
            );
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
        //Assign to view
        $this->view->assign(array(
                'arrCategories' => $arrResult,
                'arrReturn' => $arrReturn,
                'intType' => $intType
            )
        );
        //Set title
        $this->view->headTitle()->append('Tạo bài viết');
        //Append css
        $this->view->headLink()->appendStylesheet(STATIC_URL . '/library/plugins/select2/select2-bootstrap.css')
            ->appendStylesheet(STATIC_URL . '/library/plugins/select2/select2.css');
        //append script
        $this->view->headScript()->appendFile(STATIC_URL . '/library/plugins/ckeditor-4.5.9/ckeditor.js')
            ->appendFile(STATIC_URL . '/library/plugins/select2/select2.min.js')
            ->appendFile(STATIC_URL . '/library/js/jquery.cropit.min.js')
            ->appendFile(STATIC_URL . '/be/js/articles/add.js');
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
            $this->_redirect(SITE_URL . '/backend/articles');
        } else {
            //Get detail article
            $arrArticleDetail = Core_Model_Articles::getDetailArticleById($id);
            if (empty($arrArticleDetail)) {
                $this->_redirect(SITE_URL . '/backend/articles');
            } else {
                $arrTagId = $arrTagName = array();
                $arrTagDetail = Core_Model_Tags::getListTagByArticleId($arrArticleDetail['article_id']);
                if (!empty($arrTagDetail)) {
                    foreach ($arrTagDetail as $row) {
                        if ($row['tag_name'] != '') {
                            $arrTagId[] = $row['tag_id'];
                            $arrTagName[$row['tag_id']] = $row['tag_name'];
                        }
                    }
                }
            }
            $arrReturn = array(
                'error' => 1,
                'is_post' => 0,
                'msg' => 'Có lỗi xảy ra.',
                'data' => array(
                    'intArticleId' => $arrArticleDetail['article_id'],
                    'strThumbUrl' => $arrArticleDetail['thumb_url'],
                    'strOriginalLink' => $arrArticleDetail['original_link'],
                    'strTitle' => $arrArticleDetail['title'],
                    'strDesc' => $arrArticleDetail['lead'],
                    'strMetaTitle' => $arrArticleDetail['meta_title'],
                    'strMetaDesc' => $arrArticleDetail['meta_desc'],
                    'strMetaKeyword' => $arrArticleDetail['meta_keyword'],
                    'ckeditor' => $arrArticleDetail['content'],
                    //'ckeditor' => str_ireplace('data-original=','src=',$arrArticleDetail['content']),
                    'intCate' => $arrArticleDetail['category_id'],
                    'arrTags' => $arrTagName,
                    'intIsComment' => $arrArticleDetail['is_comment'],
                    'intCommentType' => $arrArticleDetail['comment_type'],
                    'strDate' => date('d/m/Y H:i:s', $arrArticleDetail['ptime']),
                    'listTags' => Zend_Json::encode($arrTagId),
                    'intStatus' => $arrArticleDetail['status'],
                    'intType' => $arrArticleDetail['type'],
                    'strLinkVideo' => $arrArticleDetail['link_video'],
                )
            );
            if ($this->_request->isPost()) {
                //Request params
                $arrParamsRequest = $this->_request->getParams();
                $checkArticles = Core_Validate_Articles::checkData($arrParamsRequest,'edit');
                if ($checkArticles['error'] == 1) {
                    $arrReturn = array_merge($arrReturn, $checkArticles);
                } else {
                    $strFileUrl = '';
                    if (isset($_FILES["strImage"]) && !empty($_FILES["strImage"])) {
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
                                    $arrOption = array(
                                        'max_width' => $arrParamsRequest['intImgW'],
                                        'max_height' => $arrParamsRequest['intImgH'],
                                        'crop' => 1,
                                        'crop_x' => abs($arrParamsRequest['intCropX']),
                                        'crop_y' => abs($arrParamsRequest['intCropY']),
                                        'scale' => $arrParamsRequest['intZoom']
                                    );
                                    //Resize image 354x212
                                    Core_Utils::scaleCropImage($arrOption, $arrImg[$arrImg['size1']][0], $arrImg[$arrImg['size1']][1], $up_source_dir . $file_id, IMAGE_UPLOAD_DIR . '/thumbnails/thumbs/' . date('Y') . '/' . date('m') . '/' . $name . '-' . $time . '_'.$arrImg['size1'].'.' . $ext);
                                    //Resize image 250x140
                                    //Core_Utils::scaleCropImage($arrOption, $arrImg[$arrImg['size2']][0], $arrImg[$arrImg['size2']][1], $up_source_dir . $file_id, IMAGE_UPLOAD_DIR . '/thumbnails/thumbs/' . date('Y') . '/' . date('m') . '/' . $name . '-' . $time . '_'.$arrImg['size2'].'.' . $ext);
                                    //Resize image 136x76
                                    //Core_Utils::scaleCropImage($arrOption, $arrImg[$arrImg['size3']][0], $arrImg[$arrImg['size3']][1], $up_source_dir . $file_id, IMAGE_UPLOAD_DIR . '/thumbnails/thumbs/' . date('Y') . '/' . date('m') . '/' . $name . '-' . $time . '_'.$arrImg['size3'].'.' . $ext);
                                    //Resize image 60x50
                                    //Core_Utils::scaleCropImage($arrOption, $arrImg[$arrImg['size4']][0], $arrImg[$arrImg['size4']][1], $up_source_dir . $file_id, IMAGE_UPLOAD_DIR . '/thumbnails/thumbs/' . date('Y') . '/' . date('m') . '/' . $name . '-' . $time . '_'.$arrImg['size4'].'.' . $ext);
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
                    $cateDetail = Core_Model_Categories::getDetailCategoryById($arrParamsRequest['intCate']);
                    $titleSeo = strtolower(Core_Utils::setSeoLink($title));
                    $shareUrl = $cateDetail['cate_link'] . '/' . $titleSeo . '-' . $arrArticleDetail['article_id'] . '.html';
                    $arrTags = explode(',', $arrParamsRequest['strTags']);
                    $arrTags = array_filter(array_unique($arrTags));
                    /*$pregfind = array('#<img[^>]+src=(\'|")(.*)(\\1).*>#siU');
                    $arrParamsRequest['ckeditor'] = preg_replace_callback($pregfind, function ($match) {
                        return Core_Utils::getimage($match,'backend');
                    }, $arrParamsRequest['ckeditor']);*/
                    //$arrParamsRequest['ckeditor'] = Core_Filter::purify($arrParamsRequest['ckeditor']);
                    $pregfind = array('#<img[^>]+src=(\'|")(.*)(\\1).*>#siU');
                    $response['data']['content'] = preg_replace_callback($pregfind, function ($match) {
                        return Core_Utils::setImageContent($match,'http://nhathepsaigon.vn','nhathepsaigon.vn','www.nhathepsaigon.vn');
                    }, $arrParamsRequest['ckeditor']);
                    //Lazyload image
                    //$arrParamsRequest['ckeditor'] = str_ireplace('src=','data-original=',$response['data']['content']);
                    $response['data']['content'] = preg_replace('/\<[\/]?(table|tbody|tr|td)([^\>]*)\>/i', '', $response['data']['content']);
                    // $arrParams update article
                    $arrParams = array(
                        'intCate' => $arrParamsRequest['intCate'],
                        'strThumbUrl' => $strFileUrl != '' ? $strFileUrl : null,
                        'strShareUrl' => $shareUrl,
                        'strOriginalLink' => $arrArticleDetail['original_link'],
                        'strTitle' => $this->view->escape($title),
                        'strDesc' => $this->view->escape(trim(strip_tags($arrParamsRequest['strDesc']))),
                        'strMetaTitle' => $this->view->escape(trim(strip_tags($arrParamsRequest['strMetaTitle']))),
                        'strMetaDesc' => $this->view->escape(trim(strip_tags($arrParamsRequest['strMetaDesc']))),
                        'strMetaKeyword' => $this->view->escape(trim(strip_tags($arrParamsRequest['strMetaKeyword']))),
                        'ckeditor' => $response['data']['content'], //$arrParamsRequest['ckeditor'],
                        'intIsComment' => $arrParamsRequest['intIsComment'],
                        'intCommentType' => $arrParamsRequest['intCommentType'],
                        'intPublishTime' => strtotime(str_ireplace('/', '-', $arrParamsRequest['strDate'])),
                        'intStatus' => $arrParamsRequest['intStatus'],
                        'intUserId' => NULL,
                        'intUserApproveId' => ($arrParamsRequest['intStatus'] == 1 && $arrArticleDetail['status'] != 1) ? $this->view->user['user_id'] : NULL,
                        'intArticleId' => $arrArticleDetail['article_id'],
                        'intType' => NULL,
                        'strLinkVideo' => isset($arrParamsRequest['strLinkVideo'])?$arrParamsRequest['strLinkVideo']:NULL
                    );
                    //Update article
                    $isUpdate = Core_Model_Articles::updateArticle($arrParams);
                    $arrReturnTagDetail = array();
                    if ($isUpdate > 0) {
                        if (!empty($arrTags)) {
                            Core_Model_Tags::deleteTagByArticle($id);
                            foreach ($arrTags as $row) {
                                if ($row > 0) {
                                    //Get detail tag
                                    $arrTagDetail = Core_Model_Tags::getDetailTagById($row);
                                    $arrReturnTagDetail[$row] = $arrTagDetail['tag_name'];
                                    //Check tag
                                    $isCheck = Core_Model_Tags::checkTagArticle(array('intArticleId' => $arrArticleDetail['article_id'], 'intTagId' => $row));
                                    if ($isCheck == 0) {
                                        Core_Model_Tags::insertTagArticle(array('intArticleId' => $arrArticleDetail['article_id'], 'intTagId' => $row));
                                    }
                                }
                            }
                        }
                        $arrReturn['error'] = 0;
                        $arrReturn['msg'] = 'Cập nhật bài viết thành công.';
                    } else {
                        $arrReturn['msg'] = 'Cập nhật bài viết không thành công.';
                    }
                }
                $arrReturn['is_post'] = 1;
                $arrReturn['data'] = array(
                    'intArticleId' => $arrArticleDetail['article_id'],
                    'strThumbUrl' => $strFileUrl != '' ? $strFileUrl : null,
                    'strOriginalLink' => $arrArticleDetail['original_link'],
                    'strTitle' => $arrParamsRequest['strTitle'],
                    'strDesc' => $arrParamsRequest['strDesc'],
                    'strMetaTitle' => $arrParamsRequest['strMetaTitle'],
                    'strMetaDesc' => $arrParamsRequest['strMetaDesc'],
                    'strMetaKeyword' => $arrParamsRequest['strMetaKeyword'],
                    'ckeditor' => $arrParamsRequest['ckeditor'],
                    'intCate' => $arrParamsRequest['intCate'],
                    'arrTags' => $arrReturnTagDetail,
                    'intIsComment' => $arrParamsRequest['intIsComment'],
                    'intCommentType' => $arrParamsRequest['intCommentType'],
                    'strDate' => $arrParamsRequest['strDate'],
                    'listTags' => Zend_Json::encode($arrTags),
                    'intStatus' => $arrParamsRequest['intStatus'],
                    'intType' => $arrArticleDetail['type'],
                    'strLinkVideo' => $arrParamsRequest['link_video'],
                );
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
        //Assign to view
        $this->view->assign(array(
                'arrCategories' => $arrResult,
                'arrReturn' => $arrReturn,
                'intType' => $arrArticleDetail['type']
            )
        );
        //Set title
        $this->view->headTitle()->append('Sửa bài viết');
        //Append css
        $this->view->headLink()->appendStylesheet(STATIC_URL . '/library/plugins/select2/select2-bootstrap.css')
            ->appendStylesheet(STATIC_URL . '/library/plugins/select2/select2.css');
        //append script
        $this->view->headScript()->appendFile(STATIC_URL . '/library/plugins/ckeditor-4.5.9/ckeditor.js')
            ->appendFile(STATIC_URL . '/library/plugins/select2/select2.min.js')
            ->appendFile(STATIC_URL . '/library/js/jquery.cropit.min.js')
            ->appendFile(STATIC_URL . '/be/js/articles/edit.js');
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
            case 0:
                $lab = 'Xóa';
                break;
            case STATUS_DA_DUYET:
                $lab = 'Duyệt';
                break;
            case STATUS_LUU_NHAP:
                $lab = 'Từ chối duyệt';
                break;
        }
        $arrReturn = array('error' => 1, 'msg' => $lab . ' bài viết [' . $strName . '] không thành công.');
        //Check permission - Get menu by menu code
        $arrMenuByMenuCode = Core_Model_Menus::getDetailMenuByMenuCode($this->view->moduleName.'_'.$this->view->controllerName.'_'.$this->view->actionName);
        if(in_array($arrMenuByMenuCode['menu_id'],$this->view->user['permission'][PERMISSION_DEL])){
            if ($id > 0) {
                // $arrParams update article
                $arrParams = array(
                    'intStatus' => $status,
                    'intUserApproveId' => $this->view->user['user_id'],
                    'intArticleId' => $id
                );
                //Update article
                $isUpdate = Core_Model_Articles::updateArticle($arrParams);
                if ($isUpdate > 0) {
                    $arrReturn['error'] = 0;
                    $arrReturn['msg'] = $lab . ' bài viết [' . $strName . '] thành công.';
                }
            }
        }else{
            $arrReturn = array('error' => 1, 'msg' => 'Bạn không có quyền ' . $lab . ' bài viết [' . $strName . '].');
        }
        /*End check permission*/
        echo Zend_Json::encode($arrReturn);
        exit;
    }

    /**
     * @name        :   cropImage
     * @author      :   PhongTX
     * @copyright   :   Fpt Online
     */
    /* Library of crop image */
    private function cropImage($data)
    {
        $path = IMAGE_UPLOAD_DIR . '/thumbnails/originals';
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $arrOption = array(
            'image_library' => 1,
            'upload_dir' => IMAGE_UPLOAD_DIR . '/',
            'upload_url' => STATIC_URL . '/uploads/thumbnails/originals',
            'max_file_size' => 2097152, // <= 2MB
            'accept_file_types' => '/(\.|\/)(jpe?g|png)$/i'
        );
        $upload_handler = new EclickAdmin_Entity_UploadHandler($arrOption, false);
        $option = array(
            'no_cache' => 0,
            'max_width' => $data['intImgW'],
            'max_height' => $data['intImgH'],
            'crop' => 1,
            'crop_x' => abs($data['intCropX']),
            'crop_y' => abs($data['intCropY']),
            'scale' => $data['intZoom'],
            'strip' => 1,
            'file_path' => STATIC_URL . '/uploads/thumbnails/originals',
            'new_file_path' => STATIC_URL . '/uploads/thumbnails/thumbs',
        );
        return $upload_handler->create_scaled_image($data['strImgName'], 'thumbs', $option);
    }

    /**
     * @param $match
     * @return string
     */
    /*function getimage($match)
    {
        $src = trim($match[2]);
        $md5 = md5($src);
        if (!isset($this->arrImgDone[$md5])) {
            if (strpos($src, 'http')===0) {
                if (strpos($src, SITE_URL)!==0) {
                    $arrInfo = pathinfo($src);
                    $ext = in_array($arrInfo['extension'], array('jpg', 'png')) ? $arrInfo['extension'] : 'jpg';
                    $filename = $arrInfo['filename'].'_'.mt_rand(1,1000) . time() . '.' . $ext;
                    $url = STATIC_URL . '/uploads/images/' . date('Y/m') . '/' . $filename;
                    $up_source_dir = IMAGE_UPLOAD_DIR . '/images/originals/';
                    $path = IMAGE_UPLOAD_DIR . '/images/';
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
                    $up_source_dir .= '/' . $filename;
                    foreach ($year as $row) {
                        $path .= $row;
                        if (!is_dir($path)) {
                            $ok = mkdir($path, 0755, true);
                        }
                        $path .= DIRECTORY_SEPARATOR;
                    }
                    $path .= '/' . $filename;
                    try {
                        $content = file_get_contents($src);
                        file_put_contents($up_source_dir, $content);
                        $this->arrImgDone[$md5] = $up_source_dir;
                        $imgsize = getimagesize($up_source_dir);
                        $width = $imgsize[0];
                        $height = $imgsize[1];
                        if ($width > 663) {
                            $height = ceil(663 * $height / $width);
                            $width = 663;
                        }
                        Core_Utils::resize_crop_image($width, $height, $up_source_dir, $path);
                    } catch (Zend_Exception $e) {
                        return '';
                    }
                    return "<img style=\"margin-left: auto; margin-right: auto; max-width: 100%; height:auto;\" src=\"" . $url . "\"/>";
                } else {
                    return $match[0];
                }
            } else {
                return '';
            }
        } else {
            return "<img style=\"margin-left: auto; margin-right: auto; max-width: 100%; height:auto;\" src=\"" . $this->arrImgDone[$md5] . "\"/>";
        }
    }*/
}

?>
