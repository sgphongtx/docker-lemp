<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 21/04/15
 * Time: 16:53
 */
class Core_Model_Contact
{
    /**
     * @author PhongTX
     * @param $intTagId
     * @return mixed
     */
    public static function getDetailContactById($intTagId)
    {
        return Core_Business_Contact::getDetailContactById($intTagId);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function insertContact($arrParams)
    {
        return Core_Business_Contact::insertContact($arrParams);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function updateContact($arrParams)
    {
        return Core_Business_Contact::updateContact($arrParams);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function getListContact($arrParams)
    {
        return Core_Business_Contact::getListContact($arrParams);
    }
}

?>