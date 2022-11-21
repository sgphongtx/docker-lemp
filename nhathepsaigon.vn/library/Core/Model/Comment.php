<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 21/04/15
 * Time: 17:03
 */
class Core_Model_Comment
{
    /**
     * @author PhongTX
     * @param array $arrParams
     * @return array|mixed
     * @throws Exception
     */
    public static function getDetailCommentById($intId)
    {
        return Core_Business_Comment::getDetailCommentById($intId);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function insertComment($arrParams)
    {
        return Core_Business_Comment::insertComment($arrParams);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function updateComment($arrParams)
    {
        return Core_Business_Comment::updateComment($arrParams);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return array
     * @throws Exception
     */
    public static function getListComment($arrParams)
    {
        return Core_Business_Comment::getListComment($arrParams);
    }

    /**
     * PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function getListCommentByArticleId($id)
    {
        return Core_Business_Comment::getListCommentByArticleId($id);
    }
}

?>