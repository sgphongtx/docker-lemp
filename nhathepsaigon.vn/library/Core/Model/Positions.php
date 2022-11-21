<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 21/04/15
 * Time: 16:53
 */
class Core_Model_Positions
{
    /**
     * @author PhongTX
     * @param $intId
     * @return mixed
     */
    public static function getDetailPositionById($intId)
    {
        return Core_Business_Positions::getDetailPositionById($intId);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function insertPosition($arrParams)
    {
        return Core_Business_Positions::insertPosition($arrParams);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function updatePosition($arrParams)
    {
        return Core_Business_Positions::updatePosition($arrParams);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function checkPosition($arrParams)
    {
        return Core_Business_Positions::checkPosition($arrParams);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function getListPositions($arrParams)
    {
        return Core_Business_Positions::getListPositions($arrParams);
    }
}

?>