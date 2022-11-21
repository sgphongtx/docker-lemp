<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 21/04/15
 * Time: 16:53
 */
class Core_Model_Tags
{
    /**
     * @author PhongTX
     * @param $intTagId
     * @return mixed
     */
    public static function getDetailTagById($intTagId)
    {
        return Core_Business_Tags::getDetailTagById($intTagId);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function insertTag($arrParams)
    {
        return Core_Business_Tags::insertTag($arrParams);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function updateTag($arrParams)
    {
        return Core_Business_Tags::updateTag($arrParams);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function checkTag($arrParams)
    {
        return Core_Business_Tags::checkTag($arrParams);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function getListTags($arrParams)
    {
        return Core_Business_Tags::getListTags($arrParams);
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function tagsSearchKeyword($arrParams)
    {
        return Core_Business_Tags::tagsSearchKeyword($arrParams);
    }

    /**
     * PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function checkTagArticle($arrParams)
    {
        return Core_Business_Tags::checkTagArticle($arrParams);
    }

    /**
     * PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function insertTagArticle($arrParams)
    {
        return Core_Business_Tags::insertTagArticle($arrParams);
    }

    /**
     * PhongTX
     * @param $id
     * @return mixed
     */
    public static function getListTagByArticleId($id)
    {
        return Core_Business_Tags::getListTagByArticleId($id);
    }

    /**
     * PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function deleteTagByArticle($id)
    {
        return Core_Business_Tags::deleteTagByArticle($id);
    }

    /**
     * @author PhongTX
     * @param array $arrParams
     * @return array|mixed
     * @throws Exception
     */
    public static function getDetailTagByTagCode($strTagCode)
    {
        return Core_Business_Tags::getDetailTagByTagCode($strTagCode);
    }
}

?>