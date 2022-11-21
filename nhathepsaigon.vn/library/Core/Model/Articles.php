<?php
/**
 * @name        :   Core_Model_Articles
 * @author      :   PhongTX
 * @copyright   :   Fpt Online
 * Date: 24/12/14
 * Time: 08:45
 */
class Core_Model_Articles
{
    /**
     * PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function getDetailArticleById($id)
    {
        return Core_Business_Articles::getDetailArticleById($id);
    }

    /**
     * PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function insertArticle($arrParams)
    {
        return Core_Business_Articles::insertArticle($arrParams);
    }

    /**
     * PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function updateArticle($arrParams)
    {
        return Core_Business_Articles::updateArticle($arrParams);
    }

    /**
     * PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function getListArticle($arrParams)
    {
        return Core_Business_Articles::getListArticle($arrParams);
    }

    /**
     * PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function getListArticleByTagId($arrParams)
    {
        return Core_Business_Articles::getListArticleByTagId($arrParams);
    }

    /**
     * PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function getListOtherArticle($arrParams)
    {
        return Core_Business_Articles::getListOtherArticle($arrParams);
    }

    /**
     * PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function insertTopstory($arrParams)
    {
        return Core_Business_Articles::insertTopstory($arrParams);
    }

    /**
     * PhongTX
     * @return array
     */
    public static function getListTopstory($arrParams)
    {
        return Core_Business_Articles::getListTopstory($arrParams);
    }

    /**
     * PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function deleteTopstoryById($arrParams)
    {
        return Core_Business_Articles::deleteTopstoryById($arrParams);
    }

    /**
     * PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function updateTopstoryById($arrParams)
    {
        return Core_Business_Articles::updateTopstoryById($arrParams);
    }

    /**
     * PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function getDetailArticleByTitle($title)
    {
        return Core_Business_Articles::getDetailArticleByTitle($title);
    }

    /**
     * PhongTX
     * @param $strLink
     * @return array
     */
    public static function crawler($strLink){
        try{
            $arrResultCrawler = array();
            if($strLink != '') {
                $client = new Zend_Http_Client($strLink);
                $response = $client->request();
                $html = $response->getBody();
                $arrLink = explode('/', $strLink);
                switch ($arrLink[2]) {
                    case 'giaitri.vnexpress.net':
                        if($arrLink[3] == 'photo'){
                            $arrResultCrawler = Core_Crawler_VnExpressPhoto::parse($html);
                        }else{
                            $arrResultCrawler = Core_Crawler_VnExpressArticle::parse($html);
                        }
                        break;
                    case 'ione.vnexpress.net':
                        if($arrLink[3] == 'photo'){
                            $arrResultCrawler = Core_Crawler_VnExpressPhoto::parse($html);
                        }else{
                            $arrResultCrawler = Core_Crawler_VnExpressArticle::parse($html);
                        }
                        break;
                    /*case 'suckhoe.vnexpress.net':
                        if($arrLink[3] == 'photo'){
                            $arrResultCrawler = Core_Crawler_VnExpressPhoto::parse($html);
                        }else{
                            $arrResultCrawler = Core_Crawler_VnExpressArticle::parse($html);
                        }
                        break;
                    case 'tapchiyduoc.com':
                        $arrResultCrawler = Core_Crawler_Tapchiyduoc::parse($html);
                        break;*/
                    case 'thoitrang.biz':
                        $arrResultCrawler = Core_Crawler_ThoiTrangBiz::parse($html);
                        break;
                    case 'sendo.vn':
                        $arrResultCrawler = Core_Crawler_NhipSongSendo::parse($html);
                        break;
                    case 'news.zing.vn':
                        $arrResultCrawler = Core_Crawler_ZingNews::parse($html);
                        break;
                    case 'pose.com.vn':
                        $arrResultCrawler = Core_Crawler_Pose::parse($html);
                        break;
                    case 'guu.vn':
                        $arrResultCrawler = Core_Crawler_Guu::parse($html);
                        break;
                    case 'dep.com.vn':
                        $arrResultCrawler = Core_Crawler_DepComVn::parse($html);
                        break;
                    case 'nuathegioi.com':
                        $arrResultCrawler = Core_Crawler_NuaTheGioiCom::parse($html);
                        break;
                    case 'dantri.com.vn':
                        $arrResultCrawler = Core_Crawler_DanTri::parse($html);
                        break;
                    case 'kienthuc.net.vn':
                        $arrResultCrawler = Core_Crawler_KienThuc::parse($html);
                        break;
                    case 'divashop.vn':
                        $arrResultCrawler = Core_Crawler_DivaShop::parse($html);
                        break;
                    case 'arganoil.com.vn':
                        $arrResultCrawler = Core_Crawler_ArganoilComVn::parse($html);
                        break;
                    case '24h.com.vn':
                        $arrResultCrawler = Core_Crawler_24hComVn::parse($html);
                        break;
                    case 'eva.vn':
                        $arrResultCrawler = Core_Crawler_EvaVn::parse($html);
                        break;
                    case 'ngoisao.net':
                        $arrResultCrawler = Core_Crawler_NgoisaoNet::parse($html);
                        break;
                    case 'tintuc.vn':
                        $arrResultCrawler = Core_Crawler_TintucVn::parse($html);
                        break;
                    case 'kenh14.vn':
                        $arrResultCrawler = Core_Crawler_Kenh14Vn::parse($html);
                        break;
                    case 'elle.vn':
                        $arrResultCrawler = Core_Crawler_ElleVn::parse($html);
                        break;
                    case 'saostar.vn':
                        $arrResultCrawler = Core_Crawler_SaoStarVn::parse($html);
                        break;
                    case 'soha.vn':
                        $arrResultCrawler = Core_Crawler_SohaVn::parse($html);
                        break;
                    case 'phununews.vn':
                        $arrResultCrawler = Core_Crawler_PhuNuNewsVn::parse($html);
                        break;
                    case 'phongcachsao.com':
                        $arrResultCrawler = Core_Crawler_PhongCachSaoCom::parse($html);
                        break;
                    case 'xinhxinh.vn':
                        $arrResultCrawler = Core_Crawler_XinhXinhVn::parse($html);
                        break;
                    case 'eva.tingiaitri24h.com':
                        $arrResultCrawler = Core_Crawler_EvaTinGiaiTri24hCom::parse($html);
                        break;
                    case 'afamily.vn':
                        $arrResultCrawler = Core_Crawler_AfamilyVn::parse($html);
                        break;
                    case 'ttvn.vn':
                        $arrResultCrawler = Core_Crawler_TtvnVn::parse($html);
                        break;
                    case 'emdep.vn':
                        $arrResultCrawler = Core_Crawler_EmDepVn::parse($html);
                        break;
                    case 'baophunuonline.net':
                        $arrResultCrawler = Core_Crawler_BaoPhuNuOnlineNet::parse($html);
                        break;
                    case 'yeulamdep.net':
                        $arrResultCrawler = Core_Crawler_YeuLamDepNet::parse($html);
                        break;
                    case 'blog.topmot.vn':
                        $arrResultCrawler = Core_Crawler_BlogTopMotVn::parse($html);
                        break;
                    case 'vmode.vn':
                        $arrResultCrawler = Core_Crawler_VmodeVn::parse($html);
                        break;
                    case 'ngoisao.vn':
                        $arrResultCrawler = Core_Crawler_NgoisaoVn::parse($html);
                        break;
                    case 'lofficiel.vn':
                        $arrResultCrawler = Core_Crawler_LofficielVn::parse($html);
                        break;
                }
                $arrResultCrawler['data']['content'] = preg_replace('/\<[\/]?(table|tbody|tr|td)([^\>]*)\>/i', '', $arrResultCrawler['data']['content']);
            }
        }catch (Exception $ex){
            Core_Global::sendLog($ex);
        }
        return $arrResultCrawler;
    }
}
?>