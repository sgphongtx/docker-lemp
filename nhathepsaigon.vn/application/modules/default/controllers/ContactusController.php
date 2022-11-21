<?php

/**
 * @author   : PhongTX
 * @name : ContactusController
 * @copyright   : FPT Online
 * @todo    : Contactus Controller
 */
class ContactusController extends Zend_Controller_Action
{
    public function init()
    {

    }

    /**
     * @author   : PhongTX
     * @name : sendArticleAction
     * @copyright   : FPT Online
     * @todo    : sendArticle Index Action
     */
    public function sendArticleAction()
    {
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
                'arrCate' => $arrResult
            )
        );
        //append title
        $this->view->headTitle()->append('Gửi bài viết đăng lên ' . $this->view->arrConfig['web_name']);
        $this->view->headMeta()->setName('description', 'Gửi bài viết đăng lên '. $this->view->arrConfig['web_name']);
        $this->view->headMeta()->setName('keywords', 'Gửi bài viết đăng lên ' . $this->view->arrConfig['web_name']);
        //append css
        $this->view->headLink()->appendStylesheet(STATIC_URL . '/library/plugins/select2-4.0.0/select2.min.css')
                                ->appendStylesheet(STATIC_URL . '/library/plugins/select2-4.0.0/select2.modify.css');
        //append script
        $this->view->headScript()->appendFile(STATIC_URL . '/fe/js/ckeditor-4.5.9/ckeditor.js')
            ->appendFile(STATIC_URL . '/library/plugins/select2-4.0.0/select2.full.min.js')
            ->appendFile(STATIC_URL . '/fe/js/contactus/send-article.js');
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
            // Kiem tra da login chua
            if (isset($this->view->user) && $this->view->user['user_id'] > 0) {
                //Get params
                $arrParamsRequest = $this->_request->getParams();
                $arrCaptcha['id'] = trim($arrParamsRequest['captchaID']);
                $arrCaptcha['input'] = trim($arrParamsRequest['strCode']);
                // Creating a Zend_Captcha_Image instance
                $captcha = new Zend_Captcha_Image();
                if (!$captcha->isValid($arrCaptcha)) {
                    $arrReturn = array(
                        'error' => 2,
                        'msg' => 'Mã xác nhận không chính xác.'
                    );
                    echo Zend_Json::encode($arrReturn);
                    exit;
                }
                $checkArticles = Core_Validate_Contact::checkDataSendArticle($arrParamsRequest);
                if ($checkArticles['error'] == 1) {
                    echo Zend_Json::encode($checkArticles);
                    exit;
                } else {
                    $title = trim(strip_tags(str_replace('&nbsp;', ' ', $arrParamsRequest['strTitle'])));
                    $pregfind = array('#<img[^>]+src=(\'|")(.*)(\\1).*>#siU');
                    $arrParamsRequest['ckeditor'] = preg_replace_callback($pregfind, function ($match) {
                        return Core_Utils::getimage($match, 'fontend');
                    }, $arrParamsRequest['ckeditor']);
                    // $arrParams insert article
                    $arrParams = array(
                        'intCate' => $arrParamsRequest['intCate'],
                        'strThumbUrl' => NULL,
                        'strShareUrl' => NULL,
                        'strTitle' => $title,
                        'strDesc' => trim(strip_tags($arrParamsRequest['strDesc'])),
                        'strMetaTitle' => trim(strip_tags($title)),
                        'strMetaDesc' => trim(strip_tags($arrParamsRequest['strDesc'])),
                        'strMetaKeyword' => trim(strip_tags($title)),
                        'ckeditor' => $arrParamsRequest['ckeditor'],
                        'intIsComment' => 1,//Cho phep binh luan
                        'intCommentType' => 0,//Binh luan tren trang
                        'intPublishTime' => NULL,
                        'intStatus' => STATUS_LUU_NHAP,
                        'intUserId' => $this->view->user['user_id'],
                        'intUserApproveId' => NULL
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
                            'intPublishTime' => time(),
                            'intStatus' => NULL,
                            'intUserId' => NULL,
                            'intUserApproveId' => NULL,
                            'intArticleId' => $intArticleId
                        );
                        Core_Model_Articles::updateArticle($arrParamsArticleUpdate);
                        //Send sms
                        Core_Global::sendTelegramSms(array(
                            'token' => TOKEN_TELEGRAM_SMS,
                            'group_id' => GROUP_ID_TELEGRAM,
                            'msg' => date('H:i:s m/d/Y', time()) . ": Có user gửi bài viết [" . $intArticleId . " - " . $title . "] đăng lên ' . $this->view->arrConfig['web_name'] . '."
                        ));
                        $arrReturn['error'] = 0;
                        $arrReturn['msg'] = 'Gửi bài viết đăng lên ' . $this->view->arrConfig['web_name'] . ' thành công.';
                    } else {
                        $arrReturn['msg'] = 'Gửi bài viết đăng lên ' . $this->view->arrConfig['web_name'] . ' không thành công.';
                    }
                }
            }else{
                $arrReturn['msg'] = 'Bạn phải đăng nhập trước khi gửi bài.';
            }
            echo Zend_Json::encode($arrReturn);
            exit;
        }
    }

    /**
     * @author   : PhongTX
     * @name : adsAction
     * @copyright   : FPT Online
     * @todo    : ads Action
     */
    public function adsAction()
    {
        $this->view->headTitle()->append('Liên hệ quảng cáo');
        $this->view->headMeta()->setName('description', 'Liên hệ quảng cáo');
        $this->view->headMeta()->setName('keywords', 'Liên hệ quảng cáo');
        //append script
        $this->view->headScript()->appendFile(STATIC_URL . '/fe/js/ckeditor-4.5.9/ckeditor.js')
            ->appendFile(STATIC_URL . '/fe/js/contactus/ads.js');
    }

    /**
     * @todo - Action add
     * @Author PhongTX
     */
    public function ajaxInsertAdsAction()
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
            $arrCaptcha['id'] = trim($arrParamsRequest['captchaID']);
            $arrCaptcha['input'] = trim($arrParamsRequest['strCode']);
            // Creating a Zend_Captcha_Image instance
            $captcha = new Zend_Captcha_Image();
            if (!$captcha->isValid($arrCaptcha)) {
                $arrReturn = array(
                    'error' => 2,
                    'msg' => 'Mã xác nhận không chính xác.'
                );
                echo Zend_Json::encode($arrReturn);
                exit;
            }
            $checkAds = Core_Validate_Contact::checkDataAds($arrParamsRequest);
            if ($checkAds['error'] == 1) {
                echo Zend_Json::encode($checkAds);
                exit;
            } else {
                $strName = trim(strip_tags(str_replace('&nbsp;', ' ', $arrParamsRequest['strName'])));
                // $arrParams insert ads
                $arrParams = array(
                    'intType' => 1,//0: Lien he toa soan, 1: lien he quang cao
                    'strName' => $strName,
                    'strEmail' => trim(strip_tags($arrParamsRequest['strEmail'])),
                    'strPhone' => trim(strip_tags($arrParamsRequest['strPhone'])),
                    'strContent' => $arrParamsRequest['ckeditor'],
                    'intStatus' => 1
                );
                // insert article
                $intId = Core_Model_Contact::insertContact($arrParams);
                if ($intId > 0) {
                    $isSendMail = Core_Email::send(array('e_template' => 'ads_contact', 'fullname' => $arrParams['strName'], 'email' => $arrParams['strEmail'], 'phone' => trim($arrParams['strPhone']), 'content' => $arrParams['strContent']));
                    if ($isSendMail) {
                        //Send sms
                        Core_Global::sendTelegramSms(array(
                            'token' => TOKEN_TELEGRAM_SMS,
                            'group_id' => GROUP_ID_TELEGRAM,
                            'msg' => date('H:i:s m/d/Y', time()) . ": Có user gửi liên hệ quảng cáo trên ' . $this->view->arrConfig['web_name'] . '."
                        ));
                        $arrReturn['error'] = 0;
                        $arrReturn['msg'] = 'Gửi liên hệ quảng cáo thành công. Cảm ơn bạn đã liên hệ quảng cáo trên trang ' . $this->view->arrConfig['web_name'] . '. Chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất.';
                    }
                } else {
                    $arrReturn['msg'] = 'Gửi liên hệ quảng cáo không thành công.';
                }
            }
            echo Zend_Json::encode($arrReturn);
            exit;
        }
    }

    /**
     * @author   : PhongTX
     * @name : introductionAction
     * @copyright   : FPT Online
     * @todo    : introduction Action
     */
    public function introductionAction()
    {
        //Get detail page
        $arrPageDetail = Core_Model_Pages::getDetailPageByPageKey('gioi-thieu');
        if (empty($arrPageDetail)) {
            $this->_redirect(SITE_URL . '/404.html');
        }
        //Assign to view
        $this->view->assign(array(
                'arrPageDetail' => $arrPageDetail
            )
        );
        //Set title
        $this->view->headTitle()->append($arrPageDetail['meta_title']);
        $this->view->headMeta()->setName('description', $arrPageDetail['meta_desc']);
        $this->view->headMeta()->setName('keywords', $arrPageDetail['meta_keyword']);
    }

    /**
     * @author   : PhongTX
     * @name : priceAction
     * @copyright   : FPT Online
     * @todo    : price Action
     */
    public function priceAction()
    {
        //Get detail page
        $arrPageDetail = Core_Model_Pages::getDetailPageByPageKey('bang-gia');
        if (empty($arrPageDetail)) {
            $this->_redirect(SITE_URL . '/404.html');
        }
        //Assign to view
        $this->view->assign(array(
                'arrPageDetail' => $arrPageDetail
            )
        );
        //Set title
        $this->view->headTitle()->append($arrPageDetail['meta_title']);
        $this->view->headMeta()->setName('description', $arrPageDetail['meta_desc']);
        $this->view->headMeta()->setName('keywords', $arrPageDetail['meta_keyword']);
    }

    /**
     * @author   : PhongTX
     * @name : galleryAction
     * @copyright   : FPT Online
     * @todo    : gallery Action
     */
    public function galleryAction()
    {
        //Get detail page
        $arrPageDetail = Core_Model_Pages::getDetailPageByPageKey('thu-vien');
        if (empty($arrPageDetail)) {
            $this->_redirect(SITE_URL . '/404.html');
        }
        //Assign to view
        $this->view->assign(array(
                'arrPageDetail' => $arrPageDetail
            )
        );
        //Set title
        $this->view->headTitle()->append($arrPageDetail['meta_title']);
        $this->view->headMeta()->setName('description', $arrPageDetail['meta_desc']);
        $this->view->headMeta()->setName('keywords', $arrPageDetail['meta_keyword']);
    }

    /**
     * @author   : PhongTX
     * @name : contactAction
     * @copyright   : FPT Online
     * @todo    : contact Action
     */
    public function contactAction()
    {
        //Get detail page
        $arrPageDetail = Core_Model_Pages::getDetailPageByPageKey('lien-he');
        if (empty($arrPageDetail)) {
            $this->_redirect(SITE_URL . '/404.html');
        }
        //Assign to view
        $this->view->assign(array(
                'arrPageDetail' => $arrPageDetail
            )
        );
        //Set title
        $this->view->headTitle()->append($arrPageDetail['meta_title']);
        $this->view->headMeta()->setName('description', $arrPageDetail['meta_desc']);
        $this->view->headMeta()->setName('keywords', $arrPageDetail['meta_keyword']);
    }

    /**
     * @author   : PhongTX
     * @name : Contactus
     * @copyright   : FPT Online
     * @todo    : Application Index Action
     */
    public function indexAction()
    {
        $this->_redirect(SITE_URL);
    }
}

?>