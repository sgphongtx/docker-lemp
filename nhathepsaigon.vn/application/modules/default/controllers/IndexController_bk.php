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
        /* Lay tin hot news*/
        if($this->view->arrConfig['topstory_type'] == 0) {
            //Define arrParams
            $arrParams = array(
                'strStatus' => '1',
                'intOffset' => 0,
                'intLimit' => LIMIT_TOP_NEWS + LIMIT_TOP_NEWS_BOTTOM + 1,
                'intPublishTime' => $time
            );
            //Call function getListArticle
            $arrReturn = Core_Model_Articles::getListArticle($arrParams);
            if(!empty($arrReturn['data'])){
                $arrReturn = $arrReturn['data'];
            }else{
                $arrReturn = array();
            }
        }else {
            $arrReturn = Core_Model_Articles::getListTopstory($time);
        }
        /*End lay tin hot news*/
        /*Lay tin trang home*/
        $arrHomeArticle = array();
        if($this->view->arrConfig['topstory_type'] == 0) {
            if (!empty($this->view->arrCategories)) {
                foreach ($this->view->arrCategories as $row) {
                    $arrCateTemp[] = $row['category_id'];
                    if (!empty($row['child'])) {
                        foreach ($row['child'] as $row1) {
                            $arrCateTemp[] = $row1['category_id'];
                        }
                    }
                    //Define arrParams
                    $arrParams = array(
                        'intCategoryId' => !empty($arrCateTemp) ? implode(',', $arrCateTemp) : '',
                        'strStatus' => '1',
                        'intOffset' => 0,
                        'intLimit' => LIMIT_NEWS_ON_CATE_HOME,
                        'intPublishTime' => $time
                    );
                    $temp = Core_Model_Articles::getListArticle($arrParams);
                    $arrHomeArticle[$row['category_id']] = $temp['data'];
                    unset($arrCateTemp);
                }
            }
        }else{
            if (!empty($this->view->arrCategories)) {
                foreach ($this->view->arrCategories as $row) {
                    $arrParams = array(
                        'intTime' => $time,
                        'intCategoryId' => $row['category_id'],
                    );
                    $temp = Core_Model_Articles::getListTopstory($arrParams);
                    $arrHomeArticle[$row['category_id']] = $temp;
                    unset($arrCateTemp);
                }
            }
        }
        /*End lay tin trang home*/
        /*Lay tin 24h qua*/
        //Define arrParams
        $arrParams = array(
            'strStatus' => '1',
            'intOffset' => 0,
            'intLimit' => LIMIT_24h_NEWS,
            'intPublishTime' => $time,
            'intPublishTime24' => strtotime(date('Y-m-d', $time) . ' 00:00:00') - (2 * 86400), //48h truoc
        );
        //Call function getListArticle
        $arrReturn24h = Core_Model_Articles::getListArticle($arrParams);
        /*End lay tin 24h qua*/
        /*Lay list tags*/
        $arrParams = array(
            'strStatus' => '1',//1: active, 2: inactive
            'intOffset' => 0,
            'intLimit' => LIMIT_LIST_TAGS
        );
        $arrTags = Core_Model_Tags::getListTags($arrParams);
        /*End lay list tags*/
        /*Get tin doc nhieu nhat*/
        //Define arrParams
        $arrParams = array(
            'strStatus' => '1',
            'intOffset' => 0,
            'intLimit' => LIMIT_LIST_TOP_VIEWS_NEWS,
            'intPublishTime' => $time,
            'isTopViews' => 1 //Lay top views
        );
        //Call function getListArticle
        $arrTopViews = Core_Model_Articles::getListArticle($arrParams);
        /*End get tin doc nhieu nhat*/
        /*Get tin binh luan nhieu nhat*/
        //Define arrParams
        $arrParams = array(
            'strStatus' => '1',
            'intOffset' => 0,
            'intLimit' => LIMIT_LIST_TOP_COMMENT_NEWS,
            'intPublishTime' => $time,
            'isTopComments' => 1 //Lay top comment
        );
        //Call function getListArticle
        $arrTopComments = Core_Model_Articles::getListArticle($arrParams);
        /*End get tin binh luan nhieu nhat*/
        //********************* Process Meta: title + description + keywords Facebook*************************//
        $strMetaTitle = $this->view->arrConfig['meta_title'];
        $strMetaDesc = $this->view->arrConfig['meta_description'];
        $strMetaKeyword = $this->view->arrConfig['meta_keyword'];
        $strMetaImage = THUMBNAIL_SHARE_DEFAULT;
        $strMetaUrl = SITE_URL;
        //Assign to view
        $this->view->assign(array(
            'arrData' => $arrReturn,
            'arrHomeArticle' => $arrHomeArticle,
            'arrReturn24h' => $arrReturn24h,
            'arrTags' => $arrTags,
            'arrTopViews' => $arrTopViews,
            'arrTopComments' => $arrTopComments,
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