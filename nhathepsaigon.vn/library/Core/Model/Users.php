<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 08/04/2015
 * Time: 12:16 SA
 */
class Core_Model_Users
{
    /**
     * @author PhongTX
     * @param $arrParams
     * @return array|mixed
     */
    public static function getUserByEmail($arrParams)
    {
        return Core_Business_Users::getUserByEmail($arrParams);
    }

    /**
     * @author PhongTX
     * @param $intUserId
     * @return mixed
     */
    public static function getDetailUserById($intUserId)
    {
        return Core_Business_Users::getDetailUserById($intUserId);
    }

    /**
     * @author PhongTX
     * @param $arrParams
     * @return string
     */
    public static function checkUserByEmail($arrParams)
    {
        return Core_Business_Users::checkUserByEmail($arrParams);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function getListUsers($arrParams)
    {
        return Core_Business_Users::getListUsers($arrParams);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function insertUser($arrParams)
    {
        return Core_Business_Users::insertUser($arrParams);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function updateUser($arrParams)
    {
        return Core_Business_Users::updateUserById($arrParams);
    }

}

?>