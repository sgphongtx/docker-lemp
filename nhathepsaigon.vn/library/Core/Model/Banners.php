<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 21/04/15
 * Time: 16:53
 */
class Core_Model_Banners
{
    /**
     * @author PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function getDetailBannerById($arrParams)
    {
        return Core_Business_Banners::getDetailBannerById($arrParams);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function insertBanner($arrParams)
    {
        return Core_Business_Banners::insertBanner($arrParams);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function updateBanner($arrParams)
    {
        return Core_Business_Banners::updateBanner($arrParams);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function checkBanner($arrParams)
    {
        return Core_Business_Banners::checkBanner($arrParams);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function getListBanners($arrParams)
    {
        return Core_Business_Banners::getListBanners($arrParams);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function getListTextBanners($arrParams)
    {
        return Core_Business_Banners::getListTextBanners($arrParams);
    }

    /**
     * @author PhongTX
     * @param $intId
     * @return mixed
     * @throws Core_Exception
     */
    public static function getDetailTextBannerById($intId)
    {
        return Core_Business_Banners::getDetailTextBannerById($intId);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function insertTextBanner($arrParams)
    {
        return Core_Business_Banners::insertTextBanner($arrParams);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function updateTextBanner($arrParams)
    {
        return Core_Business_Banners::updateTextBanner($arrParams);
    }
}

?>