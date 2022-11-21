<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 21/04/15
 * Time: 16:53
 */
class Core_Model_Topics
{
    /**
     * @author PhongTX
     * @param $intId
     * @return mixed
     */
    public static function getDetailTopicById($intId)
    {
        return Core_Business_Topics::getDetailTopicById($intId);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function insertTopic($arrParams)
    {
        return Core_Business_Topics::insertTopic($arrParams);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function updateTopic($arrParams)
    {
        return Core_Business_Topics::updateTopic($arrParams);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function checkTopic($arrParams)
    {
        return Core_Business_Topics::checkTopic($arrParams);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function getListTopics($arrParams)
    {
        return Core_Business_Topics::getListTopics($arrParams);
    }
}

?>