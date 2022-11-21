<?php
/**
 * @name        :   Core_Model_Pages
 * @author      :   PhongTX
 * @copyright   :   Fpt Online
 * Date: 24/12/14
 * Time: 08:45
 */
class Core_Model_Pages
{
    /**
     * PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function getDetailPageById($id)
    {
        return Core_Business_Pages::getDetailPageById($id);
    }

    /**
     * PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function insertPage($arrParams)
    {
        return Core_Business_Pages::insertPage($arrParams);
    }

    /**
     * PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function updatePage($arrParams)
    {
        return Core_Business_Pages::updatePage($arrParams);
    }

    /**
     * PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function getListPage($arrParams)
    {
        return Core_Business_Pages::getListPage($arrParams);
    }

    /**
     * PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function getDetailPageByPageKey($key)
    {
        return Core_Business_Pages::getDetailPageByPageKey($key);
    }
}
?>