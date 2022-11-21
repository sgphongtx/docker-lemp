<?php

/**
 * @author      :   PhongTX
 * @name        :   IndexController
 * @copyright   :   Fpt Online
 * @todo        :   Index Controller
 */
class IndexController extends Zend_Controller_Action
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
        $time = time();

        /* Lay trang gioi thieu */
        //Get detail page
        $arrPageDetail = Core_Model_Pages::getDetailPageByPageKey('gioi-thieu');
        if (empty($arrPageDetail)) {
            $arrPageDetail['name'] = 'Giới thiệu';
            $arrPageDetail['content'] = 'Không tìm thấy dữ liệu';
        }
        /* End lay trang gioi thieu */

        /*Lay tin trang home*/
        //Define arrParams
        $arrParams = array(
            'strStatus' => '1',
            'intOffset' => 0,
            'intLimit' => LIMIT_NEWS_ON_HOME,
            'intPublishTime' => $time
        );
        //Call function getListArticle
        $arrReturn = Core_Model_Articles::getListArticle($arrParams);
        /*Lay tin trang home*/

        //********************* Process Meta: title + description + keywords Facebook*************************//
        $strMetaTitle = $this->view->arrConfig['meta_title'];
        $strMetaDesc = $this->view->arrConfig['meta_description'];
        $strMetaKeyword = $this->view->arrConfig['meta_keyword'];
        $strMetaImage = THUMBNAIL_SHARE_DEFAULT;
        $strMetaUrl = SITE_URL;
        //Assign to view
        $this->view->assign(array(
            'arrData' => $arrReturn,
            'strMetaTitle' => $strMetaTitle,
            'strMetaDesc' => $strMetaDesc,
            'strMetaKeyword' => $strMetaKeyword,
            'strMetaImage' => $strMetaImage,
            'strMetaUrl' => $strMetaUrl,
            'arrPageDetail' => $arrPageDetail
        ));
        $this->view->headTitle()->append($strMetaTitle);
        $this->view->headMeta()->setName('description', $strMetaDesc);
        $this->view->headMeta()->setName('keywords', $strMetaKeyword);
    }
}
?>