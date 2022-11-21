<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 17/04/15
 * Time: 11:34
 */
class Core_Model_Categories
{
    /**
     * @author PhongTX
     * @param $intTagId
     * @return mixed
     */
    public static function getDetailCategoryById($intCategoryId)
    {
        return Core_Business_Categories::getDetailCategoryById($intCategoryId);
    }

    /**
     * @author PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function getListCategory($arrParams)
    {
        return Core_Business_Categories::getListCategory($arrParams);
    }

    /**
     * @author PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function insertCategory($arrParams)
    {
        return Core_Business_Categories::insertCategory($arrParams);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function updateCategory($arrParams)
    {
        return Core_Business_Categories::updateCategory($arrParams);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function checkCategory($arrParams)
    {
        return Core_Business_Categories::checkCategory($arrParams);
    }

    /**
     * @todo get id by code
     * @param <string> $strCode
     * @return <int>
     */
    public static function getCateByCode($strCode)
    {
        return Core_Business_Categories::getCateByCode($strCode);
    }
}

?>