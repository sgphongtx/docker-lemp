<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 21/04/15
 * Time: 16:53
 */
class Core_Model_Menus
{
    /**
     * @author PhongTX
     * @param $intMenuId
     * @return mixed
     */
    public static function getDetailMenuById($intMenuId)
    {
        return Core_Business_Menus::getDetailMenuById($intMenuId);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function insertMenu($arrParams)
    {
        return Core_Business_Menus::insertMenu($arrParams);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function updateMenu($arrParams)
    {
        return Core_Business_Menus::updateMenu($arrParams);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function checkMenu($arrParams)
    {
        return Core_Business_Menus::checkMenu($arrParams);
    }

    /**
     * @author PhongTX
     * @return array
     * @throws Exception
     */
    public static function getListMenus()
    {
        return Core_Business_Menus::getListMenus();
    }

    /**
     * @author PhongTX
     * @param $strMenuCode
     * @return mixed
     */
    public static function getDetailMenuByMenuCode($strMenuCode)
    {
        return Core_Business_Menus::getDetailMenuByMenuCode($strMenuCode);
    }
}

?>