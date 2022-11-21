<?php

/**
 * @author      :   PhongTX
 * @name        :   IndexController
 * @copyright   :   Fpt Online
 * @todo        :   Index Controller
 */
class Backend_CrawlerController extends Zend_Controller_Action
{
    private $_arrLink = array();
    public function init()
    {
        $this->_arrLink = array(
            0 => 'http://vnexpress.net/rss/giai-tri.rss',
            1 => 'http://ngoisao.net/rss/thoi-trang.rss',
            2 => 'http://www.24h.com.vn/upload/rss/thoitrang.rss',
            3 => 'http://ione.vnexpress.net/rss/thoi-trang.rss',
            4 => 'http://ione.vnexpress.net/rss/thoi-trang/mot.rss',
            5 => 'http://ione.vnexpress.net/rss/thoi-trang/kstyle.rss',
            6 => 'http://ione.vnexpress.net/rss/thoi-trang/phong-cach-sao.rss',
            7 => 'http://ione.vnexpress.net/rss/lam-dep.rss',
            8 => 'http://ione.vnexpress.net/rss/lam-dep/toc.rss',
            9 => 'http://ione.vnexpress.net/rss/lam-dep/dang-dep.rss',
            10 => 'http://baophunuonline.net/feed',
            11 => 'http://eva.vn/rss/rss.php',
            12 => 'http://eva.vn/rss/rss.php/20',
            13 => 'http://eva.vn/rss/rss.php/36',
            14 => 'http://eva.vn/rss/rss.php/324',
            15 => 'http://eva.vn/rss/rss.php/186',
            16 => 'http://eva.tingiaitri24h.com/rss/thoi-trang-eva.html',
            17 => 'http://eva.tingiaitri24h.com/rss/mac-dep.html',
            18 => 'http://eva.tingiaitri24h.com/rss/tu-van.html',
            19 => 'http://eva.tingiaitri24h.com/rss/lam-dep.html',
            20 => 'http://eva.tingiaitri24h.com/rss/khoe-dep.html',
            21 => 'http://eva.tingiaitri24h.com/rss/lang-sao.html',
            22 => 'http://eva.tingiaitri24h.com/rss/soi-sao.html',
            23 => 'http://eva.tingiaitri24h.com/rss/hau-truong.html',
            24 => 'http://eva.tingiaitri24h.com/rss/thoi-trang-cong-so.html',
            25 => 'http://eva.tingiaitri24h.com/rss/bi-quyet-lam-dep.html',
            26 => 'http://eva.tingiaitri24h.com/rss/ba-bau.html',
            27 => 'http://eva.tingiaitri24h.com/rss/the-gioi-cong-so.html',
            28 => 'http://eva.tingiaitri24h.com/rss/mac-gi-.html',
            29 => 'http://eva.tingiaitri24h.com/rss/bau-dep.html',
            30 => 'http://dep.com.vn/Rss/Feed.aspx',
            31 => 'http://dep.com.vn/_RSS_/1.rss',
            32 => 'http://dep.com.vn/_RSS_/29.rss',
            33 => 'http://dep.com.vn/_RSS_/9.rss',
            34 => 'http://dep.com.vn/_RSS_/156.rss',
            35 => 'http://dep.com.vn/_RSS_/247.rss',
            36 => 'http://dep.com.vn/_RSS_/56.rss',
            /*32 => 'http://dep.com.vn/Rss/Feed.aspx',
            33 => 'http://dep.com.vn/Rss/Feed.aspx',
            34 => 'http://dep.com.vn/Rss/Feed.aspx',
            35 => 'http://dep.com.vn/Rss/Feed.aspx',
            36 => 'http://dep.com.vn/Rss/Feed.aspx',
            37 => 'http://dep.com.vn/Rss/Feed.aspx',
            38 => 'http://dep.com.vn/Rss/Feed.aspx',
            39 => 'http://dep.com.vn/Rss/Feed.aspx',
            40 => 'http://dep.com.vn/Rss/Feed.aspx',
            41 => 'http://dep.com.vn/Rss/Feed.aspx',
            42 => 'http://dep.com.vn/Rss/Feed.aspx',
            43 => 'http://dep.com.vn/Rss/Feed.aspx',
            44 => 'http://dep.com.vn/Rss/Feed.aspx',
            45 => 'http://dep.com.vn/Rss/Feed.aspx',*/
        );
    }

    /**
     * @todo - Action crawler
     * @Author PhongTX
     */
    public function linkDetailAction()
    {
        //Check permission - Get menu by menu code
        $arrMenuByMenuCode = Core_Model_Menus::getDetailMenuByMenuCode($this->view->moduleName.'_'.$this->view->controllerName.'_'.$this->view->actionName);
        if(!in_array($arrMenuByMenuCode['menu_id'],$this->view->user['permission'][PERMISSION_VIEW])){
            $this->_redirect(SITE_URL . '/backend/forbidden');
        }
        /*End check permission*/
        //Set title
        $this->view->headTitle()->append('Crawler data link detail');
        $this->view->headScript()->appendFile(STATIC_URL . '/be/js/crawler/link-detail.js');
    }

    /**
     * @todo - Core backend home page
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
        $intKey = $this->_request->getParam('key',0);
        $feed = Zend_Feed_Reader::import($this->_arrLink[$intKey]);
        foreach ($feed as $entry) {
            $data[] = array(
                'title' => $entry->getTitle(),
                'description' => $entry->getDescription(),
                'link' => $entry->getLink()
            );
        }
        //Assign to view
        $this->view->assign(array(
                'arrData' => $data,
                'arrLink' => $this->_arrLink,
                'intKey' => $intKey
            )
        );
        //Set title
        $this->view->headTitle()->append('List data crawler');
        $this->view->headScript()->appendFile(STATIC_URL . '/be/js/crawler/index.js');
    }

    /**
     * @todo - Core backend home page
     * @author PhongTX
     */
    public function importAction()
    {
        //Set title
        $this->view->headTitle()->append('Import data crawler RSS');
        //append script
        $this->view->headScript()->appendFile(STATIC_URL . '/be/js/crawler/import.js');
    }

    public function ajaxImportAction()
    {
        //disable layout + render
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        if ($this->_request->isPost()) {
            $arrReturn = array(
                'error' => 1,
                'msg' => 'Có lỗi xảy ra'
            );
            ini_set('max_execution_time', 1800); //1800 seconds = 30 minutes
            foreach ($this->_arrLink as $key => $link) {
                $strSource = '';
                if($key == 'giaitri_vnexpress' || $key == 'ione_thoitrang' || $key == 'ione_mot' || $key == 'ione_kstyle' || $key == 'ione_phongcachsao' || $key == 'ione_lamdep' || $key == 'ione_toc' || $key == 'ione_dangdep'){
                    $strSource = '<p style="text-align: right;">Nguồn VnExpress.net</p>';
                }
                switch ($key) {
                    case 'thoitrang24h':
                        $strSource = '<p style="text-align: right;">Nguồn 24h.com.vn</p>';
                        break;
                }
                $feed = Zend_Feed_Reader::import($link);
                foreach ($feed as $entry) {
                    $strLink = str_ireplace('www.', '', $entry->getLink());
                    $arrLink = explode('/',$strLink);
                    $bool = false;
                    if($key=='giaitri_vnexpress'){
                        if ($arrLink[3] != 'video' && (in_array('gioi-sao',$arrLink)||in_array('trong-nuoc',$arrLink)||in_array('quoc-te',$arrLink)||in_array('thoi-trang',$arrLink)||in_array('lang-mot',$arrLink)||in_array('bo-suu-tap',$arrLink)||in_array('sao-dep-sao-xau',$arrLink)||in_array('lam-dep',$arrLink)||in_array('kinh-nghiem',$arrLink))) {
                            $bool = true;
                        }
                    }else{
                        $bool = true;
                    }
                    if ($bool) {
                        $arrResultCrawler = Core_Model_Articles::crawler($strLink);
                        $data = array(
                            'title' => trim(strip_tags(str_replace('&nbsp;', ' ', $entry->getTitle()))),
                            'description' => $arrResultCrawler['data']['lead'],
                            'content' => $arrResultCrawler['data']['content']
                        );
                        $checkArticles = Core_Validate_Articles::checkDataByTitle($data['title']);
                        if ($checkArticles['error'] == 0) {
                            $pregfind = array('#<img[^>]+src=(\'|")(.*)(\\1).*>#siU');
                            $data['content'] = preg_replace_callback($pregfind, function ($match) {
                                return Core_Utils::getimage($match, 'backend');
                            }, $data['content']);
                            // $arrParams insert article
                            $arrParams = array(
                                'intCate' => NULL,
                                'strThumbUrl' => NULL,
                                'strShareUrl' => NULL,
                                'strOriginalLink' => $strLink,
                                'strTitle' => $data['title'],
                                'strDesc' => trim(strip_tags($data['description'])),
                                'strMetaTitle' => trim(strip_tags($data['title'])),
                                'strMetaDesc' => trim(strip_tags($data['description'])),
                                'strMetaKeyword' => trim(strip_tags($data['title'])),
                                'ckeditor' => $data['content'] . $strSource,
                                'intIsComment' => 1,
                                'intCommentType' => 0,
                                'intPublishTime' => time(),
                                'intStatus' => 3,//Bai luu nhap
                                'intUserId' => $this->view->user['user_id'],
                                'intUserApproveId' => NULL
                            );
                            // insert article
                            Core_Model_Articles::insertArticle($arrParams);
                        }
                    }
                }
            }
            $arrReturn['error'] = 0;
            $arrReturn['msg'] = 'Import data crawler RSS thành công';
            echo Zend_Json::encode($arrReturn);
            exit;
        }
    }

}

?>