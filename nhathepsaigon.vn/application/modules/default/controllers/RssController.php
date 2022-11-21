<?php

/**
 * @author      :   PhongTX
 * @name        :   RssController
 * @copyright   :   Fpt Online
 * @todo        :   Rss Controller
 */
class RssController extends Zend_Controller_Action
{

    public function init()
    {

    }

    public function indexAction()
    {
        //Set no view & no layout
        $this->_helper->layout->disableLayout();
        //Define arrParams
        $arrParams = array(
            'strStatus' => 1,
            'intOffset' => 0,
            'intLimit' => 50,
            'intPublishTime' => time(),
            'intPublishTime24' => strtotime(date('Y-m-d', time()) . ' 00:00:00') - (7 * 86400), //12h truoc
        );
        //Call function getListArticle
        $arrReturn = Core_Model_Articles::getListArticle($arrParams);
        $arrOtherNews = array();
        if(!empty($arrReturn['data'])){
            foreach($arrReturn['data'] as $row){
                //Init params
                $arrParams = array(
                    'intCategoryId' => $row['category_id'],
                    'strStatus' => '1',
                    'intOffset' => 0,
                    'intLimit' => 3,
                    'intPublishTime' => time(),
                    'intArticleId' => $row['article_id']
                );
                //Call function getListArticle
                $arrTemp = Core_Model_Articles::getListOtherArticle($arrParams);
                $arrOtherNews[$row['article_id']] = $arrTemp['data'];
                //End get list other news
            }
        }
        //Assign to view
        $this->view->assign(array(
            'arrData' => $arrReturn['data'],
            'arrOtherNews' => $arrOtherNews
        ));
    }

    /*public function indexAction()
    {
        //Set no view & no layout
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        //set header
        header("Content-Type: application/xml; charset=utf-8");
        //Auto refesh
        $this->view->headMeta()->appendHttpEquiv('refesh', '300');
        //Create parent feed
        $feed = new Zend_Feed_Writer_Feed;
        //set generator
        $feed->setGenerator('IDC Health News');
        //set publish date
        $feed->setDateModified(time());
        //set description
        $feed->setDescription('IDC Health News RSS');
        //Define arrParams
        $arrParams = array(
            'strStatus' => 1,
            'intOffset' => 0,
            'intLimit' => 30,
            'intPublishTime' => time(),
            'intPublishTime24' => strtotime(date('Y-m-d', time()) . ' 00:00:00') - (1 * 86400), //12h truoc
        );
        //Call function getListArticle
        $arrReturn = Core_Model_Articles::getListArticle($arrParams);
        foreach ($arrReturn['data'] as $val) {
            //create list entry
            $entry = $feed->createEntry();
            $link = SITE_URL . $val['share_url'];
            if ($val['title'] != null) {
                $entry->setTitle($val['title']);
            }
            if ($link) {
                $entry->setLink($link);
            }
            if ($val['utime'] != null) {
                $entry->setDateModified($val['utime']);
            }
            if ($val['ptime'] != null) {
                $entry->setDateCreated($val['ptime']);
            }
            $img = ($val['thumb_url']) ? '<a href="' . $link . '"><img width=130 height=100 src="' . $this->view->ImageurlArticle($val, 'size2') . '" ></a></br>' : '';
            $val['lead'] != null ? $entry->setDescription($img . $val['lead']) : $entry->setDescription('No Description');
            //add entry to $feed
            $feed->addEntry($entry);
        }
        //set title for pages
        $feed->setTitle('IDC Health News RSS');
        // set image
        $feed->setImage(array(
            'title' => 'Chuyên Trang Sức Khỏe - Trang tin điện tử tổng hợp thông tin Y Dược vì sức khỏe cộng đồng',
            'link' => 'http://healthidc.net',
            'uri' => 'http://www.healthidc.net/static/fe/images/logo.png'
        ));
        //Create xml
        $out = $feed->export('rss');
        echo $out;
    }*/

}