<?php

/**
 * @author      :   PhongTX
 * @name        :   CategoryController
 * @copyright   :   Fpt Online
 * @todo        :   Category Controller
 */
class CategoryController extends Zend_Controller_Action
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
        //get link info
        $strLink = $this->_request->getPathInfo();
        $strLink = rtrim($strLink, '/');
        //get cate code
        $cateCode = trim(strip_tags(strtolower($this->_request->getParam('cate_code', ''))));
        //get cate id by cate code
        $cateDetail = Core_Model_Categories::getCateByCode($cateCode);
        //check cate valid
        if (empty($cateDetail) || $cateDetail['category_id'] <= 0)
        {
            $this->_redirect(SITE_URL . '/404.html');
        }
        //redirect link to redirect
        if (strpos($strLink, $cateDetail['cate_link']) === FALSE)
        {
            $this->_redirect(SITE_URL . $cateDetail['cate_link'], array('code' => 301));
        }
        //set cate to param
        $this->_request->setParam('cateid', $cateDetail['category_id']);
        //********************* Set LIMIT top news cate *************************//
        $limitTopNewsCate = LIMIT_TOP_NEWS_CATE + LIMIT_TOP_NEWS_BOTTOM + 1;
        //Get page
        $intPage = max((int) $this->_request->getParam('page', 1), 1);
        //Lay cate cha
        if(!empty($this->view->arrCategories[$cateDetail['category_id']])) {
            $arrCateTemp[] = $cateDetail['category_id'];
            if(!empty($this->view->arrCategories[$cateDetail['category_id']]['child'])) {
                foreach($this->view->arrCategories[$cateDetail['category_id']]['child'] as $row) {
                    $arrCateTemp[] = $row['category_id'];
                }
            }
        }else{ //Lay cate con
            $arrCateTemp[] = $cateDetail['category_id'];
        }
        //Define arrParams
        $arrParams = array(
            'intCategoryId' => !empty($arrCateTemp)?implode(',',$arrCateTemp):'',
            'strStatus' => '1',
            'intOffset' => ($intPage - 1) * LIMIT_LIST_NEWS,
            'intLimit' => LIMIT_LIST_NEWS,
            'intPublishTime' => $time
        );
        //Call function getListArticle
        $arrReturn = Core_Model_Articles::getListArticle($arrParams);
        //Set top news
        $arrTopNews = array('data' => null, 'total' => 0);
        //Case: Page 1
        /*if ($intPage == 1)
        {
            // Lay tin hot news
            $arrTopNews['data'] = array_slice($arrReturn['data'], 0, $limitTopNewsCate);
            $arrTopNews['total'] = count($arrTopNews['data']);
            $arrReturn['data'] = array_slice($arrReturn['data'], $limitTopNewsCate);
            // End lay tin hot news

        }else{*/
            //Case: request page lớn hơn số page
            //redirect lại page max
            if ($arrReturn['total'] > 0)
            {
                $maxPage = ceil($arrReturn['total'] / LIMIT_LIST_NEWS);
                //check if page > max page then return to max page
                if ($intPage > $maxPage)
                    $this->_redirect($cateDetail['cate_link'] . '/page/' . $maxPage . '.html');
            }
        /*}*/
        //Define array
        $arrParamPaging = array();
        //Check
        if ($arrReturn['total'] > $arrParams['intLimit'])
        {
            $arrParamPaging = array(
                'total' => $arrReturn['total'],
                'page' => $intPage,
                'url' => $cateDetail['cate_link'] . '/page',
                'showItem' => 2,
                'perpage' => LIMIT_LIST_NEWS,
                'idPagination' => 'pagination',
                'extEnd' => '.html',
                'separate' => '/'
            );
        }
        unset($arrCateTemp);
        $arrCateTemp = array();
        $arrCate = $this->view->arrCategories;
        if($cateDetail['parent_id'] == 0) {
            unset($arrCate[$cateDetail['category_id']]);
        }else{
            unset($arrCate[$cateDetail['parent_id']]);
        }
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
                    'intLimit' => LIMIT_LIST_CATE_NEWS,
                    'intPublishTime' => $time
                );
                //Call function getListArticle
                $temp = Core_Model_Articles::getListArticle($arrParams);
                $arrReturnCate[$key] = $temp['data'];
            }
        }
        //********************* Process Meta: title + description + keywords Facebook*************************//
        $strMetaTitle = $cateDetail['meta_title'];
        $strMetaDesc = $cateDetail['meta_desc'];
        $strMetaKeyword = $cateDetail['keyword'];
        $strMetaImage = THUMBNAIL_SHARE_DEFAULT;
        $strMetaUrl = SITE_URL . $cateDetail['cate_link'];

        //Assign to view
        $this->view->assign(array(
            'cateDetail' => $cateDetail,
            'arrTopNews' => $arrTopNews,
            'arrData' => $arrReturn,
            'arrReturnCate' => $arrReturnCate,
            'arrParamPaging' => $arrParamPaging,
            'strMetaTitle' => $strMetaTitle,
            'strMetaDesc' => $strMetaDesc,
            'strMetaKeyword' => $strMetaKeyword,
            'strMetaImage' => $strMetaImage,
            'strMetaUrl' => $strMetaUrl
        ));
        $this->view->headTitle()->append($strMetaTitle);
        $this->view->headMeta()->setName('description', $strMetaDesc);
        $this->view->headMeta()->setName('keywords', $strMetaKeyword);
    }

    /**
     * Tag action
     */
    public function tagAction()
    {
        $time = time();
        //Get params
        $arrGetParams = $this->_request->getParams();
        //Get tag code
        $strTagCode = trim(strip_tags(strtolower($arrGetParams[1])));
        //Get tag id
        $intTagId = (int)$arrGetParams['id'];
        //Get tag detail
        $arrTagsDetail = Core_Model_Tags::getDetailTagById($intTagId);
        //check tag id is not exists
        if (empty($arrTagsDetail) || $intTagId < 1)
        {
            $this->_redirect(SITE_URL . '/404.html');
        }
        //get link info
        $strLink = $this->_request->getPathInfo();
        $strLink = rtrim($strLink, '/');
        //Get page
        $intPage = max((int) $this->_request->getParam('page', 1), 1);
        //redirect link to redirect
        if (strpos($strLink, '/'.$arrTagsDetail['tag_code'].'/tag-'.$arrTagsDetail['tag_id'].'-'.$intPage.'.html') === FALSE)
        {
            $this->_redirect(SITE_URL . '/'.$arrTagsDetail['tag_code'].'/tag-'.$arrTagsDetail['tag_id'].'-'.$intPage.'.html', array('code' => 301));
        }
        //set cate to param
        $this->_request->setParam('tagid', $arrTagsDetail['tag_id']);
        //********************* Set LIMIT top news cate *************************//
        $limitTopNewsCate = LIMIT_TOP_NEWS_CATE + LIMIT_TOP_NEWS_BOTTOM + 1;
        //Init params
        $arrParams = array(
            'intTagId' => $arrTagsDetail['tag_id'],
            'intOffset' => ($intPage - 1) * LIMIT_LIST_NEWS,
            'intLimit' => LIMIT_LIST_NEWS,
            'intPublishTime' => $time
        );
        //Call function getListArticle
        $arrReturn = Core_Model_Articles::getListArticleByTagId($arrParams);
        //Get first article
        $arrFirstArticle = (isset($arrReturn['data'][0]) && !empty($arrReturn['data'][0]))?$arrReturn['data'][0]:array();
        //Set top news
        $arrTopNews = array('data' => null, 'total' => 0);
        //Case: Page 1
        /*if ($intPage == 1)
        {
            // Lay tin hot news
            $arrTopNews['data'] = array_slice($arrReturn['data'], 0, $limitTopNewsCate);
            $arrTopNews['total'] = count($arrTopNews['data']);
            $arrReturn['data'] = array_slice($arrReturn['data'], $limitTopNewsCate);
            // End lay tin hot news

        }else{*/
            // Case: request page lớn hơn số page
            //redirect lại page max
            if ($arrReturn['total'] > 0)
            {
                $maxPage = ceil($arrReturn['total'] / LIMIT_LIST_NEWS);
                //check if page > max page then return to max page
                if ($intPage > $maxPage)
                    $this->_redirect(SITE_URL . '/'.$arrTagsDetail['tag_code'].'/tag-'.$arrTagsDetail['tag_id'].'-'.$maxPage.'.html', array('code' => 301));
            }
        /*}*/
        //Define array
        $arrParamPaging = array();
        //Check
        if ($arrReturn['total'] > $arrParams['intLimit'])
        {
            $arrParamPaging = array(
                'total' => $arrReturn['total'],
                'page' => $intPage,
                'url' => SITE_URL . '/'.$arrTagsDetail['tag_code'].'/tag-'.$arrTagsDetail['tag_id'],
                'showItem' => 2,
                'perpage' => LIMIT_LIST_NEWS,
                'idPagination' => 'pagination',
                'extEnd' => '.html',
                'separate' => '-'
            );
        }

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
        $strMetaTitle = $arrTagsDetail['meta_title'];
        $strMetaDesc = $arrTagsDetail['meta_desc'].(!empty($arrFirstArticle)?' - '.$arrFirstArticle['lead']:'').(!empty($arrFirstArticle)?' - '.$arrFirstArticle['title']:'');
        $strMetaKeyword = $arrTagsDetail['keyword'];
        $strMetaImage = !empty($arrFirstArticle['thumb_url']) ? $this->view->ImageurlArticle($arrFirstArticle, 'size1') : THUMBNAIL_SHARE_DEFAULT;
        $strMetaUrl = SITE_URL . '/'.$arrTagsDetail['tag_code'].'/tag-' . $arrTagsDetail['tag_id'] .'-'.$intPage.'.html';
        //Assign to view
        $this->view->assign(array(
            'arrTopNews' => $arrTopNews,
            'arrData' => $arrReturn,
            'arrReturn24h' => $arrReturn24h,
            'arrTags' => $arrTags,
            'arrParamPaging' => $arrParamPaging,
            'arrTopViews' => $arrTopViews,
            'arrTopComments' => $arrTopComments,
            'strMetaTitle' => $strMetaTitle,
            'strMetaDesc' => $strMetaDesc,
            'strMetaKeyword' => $strMetaKeyword,
            'strMetaImage' => $strMetaImage,
            'strMetaUrl' => $strMetaUrl,
            'arrTagsDetail' => $arrTagsDetail
        ));
        $this->view->headTitle()->append($strMetaTitle);
        $this->view->headMeta()->setName('description', $strMetaDesc);
        $this->view->headMeta()->setName('keywords', $strMetaKeyword);
    }

    /**
     * @todo - twodayAction
     * @author PhongTX
     */
    public function twodayAction()
    {
        $time = time();
        //get link info
        $strLink = $this->_request->getPathInfo();
        $strLink = rtrim($strLink, '/');
        //get cate code
        $cateCode = trim(strip_tags(strtolower($this->_request->getParam('cate_code', ''))));
        //redirect link to redirect
        if (strpos($strLink, '/tin-tuc/48h-qua') === FALSE)
        {
            $this->_redirect(SITE_URL . '/tin-tuc/48h-qua', array('code' => 301));
        }
        //********************* Set LIMIT top news cate *************************//
        $limitTopNewsCate = LIMIT_TOP_NEWS_CATE + LIMIT_TOP_NEWS_BOTTOM + 1;
        //Get page
        $intPage = max((int) $this->_request->getParam('page', 1), 1);

        //Define arrParams
        $arrParams = array(
            'strStatus' => '1',
            'intOffset' => ($intPage - 1) * (LIMIT_LIST_NEWS + 14),
            'intLimit' => LIMIT_LIST_NEWS + 14,
            'intPublishTime' => $time,
            'intPublishTime24' => strtotime(date('Y-m-d', $time) . ' 00:00:00') - (7 * 86400), //48h truoc
        );
        //Call function getListArticle
        $arrReturn = Core_Model_Articles::getListArticle($arrParams);
        //Set top news
        $arrTopNews = array('data' => null, 'total' => 0);
        //Case: Page 1
        /*if ($intPage == 1)
        {
            // Lay tin hot news
            $arrTopNews['data'] = array_slice($arrReturn['data'], 0, $limitTopNewsCate);
            $arrTopNews['total'] = count($arrTopNews['data']);
            $arrReturn['data'] = array_slice($arrReturn['data'], $limitTopNewsCate);
            // End lay tin hot news

        }else{*/
            // Case: request page lớn hơn số page
            //redirect lại page max
            if ($arrReturn['total'] > 0)
            {
                $maxPage = ceil($arrReturn['total'] / LIMIT_LIST_NEWS);
                //check if page > max page then return to max page
                if ($intPage > $maxPage)
                    $this->_redirect(SITE_URL . '/tin-tuc/48h-qua/page/' . $maxPage . '.html');
            }
        /*}*/
        //Define array
        $arrParamPaging = array();
        //Check
        if ($arrReturn['total'] > $arrParams['intLimit'])
        {
            $arrParamPaging = array(
                'total' => $arrReturn['total'],
                'page' => $intPage,
                'url' => SITE_URL . '/tin-tuc/48h-qua/page',
                'showItem' => 2,
                'perpage' => LIMIT_LIST_NEWS,
                'idPagination' => 'pagination',
                'extEnd' => '.html',
                'separate' => '/'
            );
        }

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
        $strMetaTitle = 'Tin nóng 48h qua';
        $strMetaDesc = 'Tin nóng 48h qua';
        $strMetaKeyword = 'Tin nóng 48h qua';
        $strMetaImage = THUMBNAIL_SHARE_DEFAULT;
        $strMetaUrl = SITE_URL . '/tin-tuc/48h-qua';
        //Assign to view
        $this->view->assign(array(
            'arrTopNews' => $arrTopNews,
            'arrData' => $arrReturn,
            'arrReturn24h' => $arrReturn24h,
            'arrTags' => $arrTags,
            'arrParamPaging' => $arrParamPaging,
            'arrTopViews' => $arrTopViews,
            'arrTopComments' => $arrTopComments,
            'strMetaTitle' => $strMetaTitle,
            'strMetaDesc' => $strMetaDesc,
            'strMetaKeyword' => $strMetaKeyword,
            'strMetaImage' => $strMetaImage,
            'strMetaUrl' => $strMetaUrl
        ));
        $this->view->headTitle()->append($strMetaTitle);
        $this->view->headMeta()->setName('description', $strMetaDesc);
        $this->view->headMeta()->setName('keywords', $strMetaKeyword);
    }

}

?>