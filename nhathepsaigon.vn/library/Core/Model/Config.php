<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 21/04/15
 * Time: 16:53
 */
class Core_Model_Config
{
    /**
     * @author PhongTX
     * @param $id
     * @return array|mixed
     */
    public static function getDetailConfig($id)
    {
        return Core_Business_Config::getDetailConfig($id);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function updateConfig($arrParams)
    {
        return Core_Business_Config::updateConfig($arrParams);
    }
}

?>