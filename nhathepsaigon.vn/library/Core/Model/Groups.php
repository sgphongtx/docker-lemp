<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 21/04/15
 * Time: 16:53
 */
class Core_Model_Groups
{
    /**
     * @author PhongTX
     * @param $intGroupId
     * @return mixed
     */
    public static function getDetailGroupById($intGroupId)
    {
        return Core_Business_Groups::getDetailGroupById($intGroupId);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function insertGroup($arrParams)
    {
        return Core_Business_Groups::insertGroup($arrParams);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function updateGroup($arrParams)
    {
        return Core_Business_Groups::updateGroup($arrParams);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function checkGroup($arrParams)
    {
        return Core_Business_Groups::checkGroup($arrParams);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function getListGroups($arrParams)
    {
        return Core_Business_Groups::getListGroups($arrParams);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function insertPermission($arrParams)
    {
        return Core_Business_Groups::insertPermission($arrParams);
    }

    /**
     * @author PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function delPermissionByGroupId($arrParams)
    {
        return Core_Business_Groups::delPermissionByGroupId($arrParams);
    }

    /**
     * @author PhongTX
     * @param $intGroupId
     * @return array
     */
    public static function getPermissionByGroupId($intGroupId)
    {
        return Core_Business_Groups::getPermissionByGroupId($intGroupId);
    }
}

?>