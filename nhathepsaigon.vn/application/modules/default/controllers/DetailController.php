<?php

/**
 * @author      :   PhongTX
 * @name        :   DetailController
 * @copyright   :   Fpt Online
 * @todo        :   Detail Controller
 */
class DetailController extends Zend_Controller_Action
{

    public function init()
    {
        
    }

    /**
     * @todo - Core home page
     * @author PhongTX
     */
    public function indexAction()
    {
        //Get article id
        $id = $this->_request->getParam('id', 0);
        if (!is_numeric($id) || $id <= 0)
        {
            $this->_redirect(SITE_URL . '/404.html');
        }
        //Get full detail article
        $articleDetail = Core_Model_Articles::getDetailArticleById($id);
        //********************* Check Detail Article *************************//
        if (empty($articleDetail))
        {
            $this->_redirect(SITE_URL . '/404.html');
        }
        //Get string link
        $strLink = $this->_request->getPathInfo();
        //Get page
        //$intPage = max(1, intval($this->_request->getParam('page', 1)));
        //check link to redirect
        if ($strLink != $articleDetail['share_url'])
        {
            $this->_redirect(SITE_URL.$articleDetail['share_url'], array('code' => 301));
        }
        //Set category id for block_cate user in block
        $this->_request->setParam('cateid', $articleDetail['category_id']);
        //Update views +1
        Core_Model_Articles::updateArticle(array('intViews' => ($articleDetail['views'] + 1) ,'intArticleId' => $articleDetail['article_id']));
        //End update views
        /*Lay list tags article*/
        $arrTagsArticle = Core_Model_Tags::getListTagByArticleId($articleDetail['article_id']);
        $time = time();
        //Get list other news
        //Init params
        $arrParams = array(
            'intCategoryId' => NULL, //$articleDetail['category_id'],
            'strStatus' => '1',
            'intOffset' => 0,
            'intLimit' => LIMIT_LIST_OTHER_NEWS,
            'intPublishTime' => $time,
            'intArticleId' => $articleDetail['article_id']
        );
        //Call function getListArticle
        $arrOtherNews = Core_Model_Articles::getListOtherArticle($arrParams);
        //End get list other news

        /*Get list comment*/
        //Define arrParams
        $arrParams = array(
            'intArticleId' => $articleDetail['article_id'],
            'strStatus' => '1',
            'intOffset' => 0,
            'intLimit' => 10000
        );
        //Call function getListComment
        $arrListComment = Core_Model_Comment::getListComment($arrParams);
        /*End get list comment*/

        $arrCateTemp = array();
        $arrCate = $this->view->arrCategories;
        //get cate id by cate code
        /*$cateDetail = Core_Model_Categories::getDetailCategoryById($articleDetail['category_id']);
        if($cateDetail['parent_id'] == 0) {
            unset($arrCate[$cateDetail['category_id']]);
        }else{
            unset($arrCate[$cateDetail['parent_id']]);
        }*/
        if(!empty($arrCate)){
            foreach($arrCate as $row){
                //Lay cate cha
                if(!empty($row)) {
                    $arrCateTemp[$row['category_id']][] = $row['category_id'];
                    if(!empty($row['child'])) {
                        foreach($row['child'] as $r) {
                            $arrCateTemp[$row['category_id']][] = $r['category_id'];
                        }
                    }
                }else{ //Lay cate con
                    $arrCateTemp[] = $row['category_id'];
                }
            }
        }
        //Get list cate news
        $arrReturnCate = array();
        if(!empty($arrCateTemp)){
            foreach($arrCateTemp as $key => $row){
                //Define arrParams
                $arrParams = array(
                    'intCategoryId' => !empty($row)?implode(',',$row):'',
                    'strStatus' => '1',
                    'intOffset' => 0,
                    'intLimit' => LIMIT_LIST_DETAIL_NEWS,
                    'intPublishTime' => $time
                );
                //Call function getListArticle
                $temp = Core_Model_Articles::getListArticle($arrParams);
                $arrReturnCate[$key] = $temp['data'];
            }
        }

        //********************* Process Meta: title + description + keywords Facebook*************************//
        $strMetaTitle = strip_tags(html_entity_decode($articleDetail['meta_title'], ENT_COMPAT, 'UTF-8'));
        $strMetaDesc = strip_tags(html_entity_decode($articleDetail['meta_desc'], ENT_COMPAT, 'UTF-8'));
        $strMetaKeyword = strip_tags(html_entity_decode($articleDetail['meta_keyword'], ENT_COMPAT, 'UTF-8'));
        $strMetaImage = !empty($articleDetail['thumb_url']) ? $this->view->ImageurlArticle($articleDetail, 'size1') : THUMBNAIL_SHARE_DEFAULT;
        $strMetaUrl = SITE_URL . $articleDetail['share_url'];

        //********************* Assign to views *************************//
        $this->view->assign(array(
            'arrArticleDetail' => $articleDetail,
            'arrTagsArticle' => $arrTagsArticle,
            'arrOtherNews' => $arrOtherNews,
            'arrListComment' => $arrListComment,
            'arrReturnCate' => $arrReturnCate,
            'strMetaTitle' => htmlspecialchars($strMetaTitle),
            'strMetaDesc' => htmlspecialchars($strMetaDesc),
            'strMetaKeyword' => $strMetaKeyword,
            'strMetaImage' => $strMetaImage,
            'strMetaUrl' => $strMetaUrl
        ));
        $this->view->headTitle()->append($strMetaTitle);
        $this->view->headMeta()->setName('description', $strMetaDesc);
        $this->view->headMeta()->setName('keywords', $strMetaKeyword);
        $this->view->headScript()->appendFile(STATIC_URL . '/fe/js/detail/index'.EXT.'.js');
    }

    /**
     * @todo - photoAction
     * @author PhongTX
     */
    public function photoAction()
    {
        //Get article id
        $id = $this->_request->getParam('id', 0);
        //Get page
        $intPage = max(1, intval($this->_request->getParam('page', 1)));
        echo $intPage;die;
    }

    /**
     * @todo - videoAction
     * @author PhongTX
     */
    public function videoAction()
    {
        //Get article id
        $id = $this->_request->getParam('id', 0);
        //Get page
        $intPage = max(1, intval($this->_request->getParam('page', 1)));
        echo $intPage;die;
    }

    /**
     * Print action
     */
    public function printAction()
    {
        //Disable layout
        $this->_helper->layout->disableLayout();

        //get id
        $id = $this->_request->getParam('id', 0);

        //Get page
        $intPage = max(1, intval($this->_request->getParam('page', 1)));

        if (!is_numeric($id) || $id <= 0)
        {
            $this->_redirect(SITE_URL . '/404.html');
        }

        //Get full detail article
        $articleDetail = Core_Model_Articles::getDetailArticleById($id);

        //Valid data
        if (empty($articleDetail))
        {
            $this->_redirect(SITE_URL . '/404.html');
        }

        //Check article photo
        $arrReference = array(
            'total' => 0,
            'data' => array()
        );
        //assign to view
        $this->view->assign(array(
            'articleDetail' => $articleDetail
        ));
    }

    /**
     * @todo - Action add commnet
     * @Author PhongTX
     */
    public function ajaxAddCommentAction()
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
            if ($arrParamsRequest['intArticleId'] == 0) {
                echo Zend_Json::encode($arrReturn);
                exit;
            } else {
                $arrCaptcha['id'] = trim($arrParamsRequest['captchaID']);
                $arrCaptcha['input'] = trim($arrParamsRequest['strCode']);
                // Creating a Zend_Captcha_Image instance
                $captcha = new Zend_Captcha_Image();
                if (!$captcha->isValid($arrCaptcha))
                {
                    $arrReturn = array(
                        'error' => 2,
                        'msg' => 'Mã xác nhận không chính xác.'
                    );
                    echo Zend_Json::encode($arrReturn);
                    exit;
                }
                $arrParams = array(
                    'intArticleId' => $arrParamsRequest['intArticleId'],
                    'strName' => trim(strip_tags($arrParamsRequest['strName'])),
                    'strEmail' => trim(strip_tags($arrParamsRequest['strEmail'])),
                    'strContent' => trim(strip_tags($arrParamsRequest['strContent'])),
                    'intStatus' => 2 //0: Xoa, 1: Duyet, 2: Cho Duyet, 3: Khong duyet
                );
                $intId = Core_Model_Comment::insertComment($arrParams);
                if ($intId > 0) {
                    //Get full detail article
                    $articleDetail = Core_Model_Articles::getDetailArticleById($arrParams['intArticleId']);
                    //Send sms
                    Core_Global::sendTelegramSms(array(
                        'token' => TOKEN_TELEGRAM_SMS,
                        'group_id' => GROUP_ID_TELEGRAM,
                        'msg' => date('H:i:s m/d/Y',time()).": Có user [".$arrParams['strName']." - ".$arrParams['strEmail']."] bình luận bài viết [".$arrParams['intArticleId']." - ".$articleDetail['title']."] trên trang ' .$this->view->arrConfig['web_name']. '."
                    ));
                    $arrReturn['error'] = 0;
                    $arrReturn['msg'] = 'Bạn đã gửi ý kiến thành công';
                }
                echo Zend_Json::encode($arrReturn);
                exit;
            }
        }
    }

}

?>