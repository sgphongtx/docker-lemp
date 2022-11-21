<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 21/04/15
 * Time: 17:03
 */
class Core_Business_Tags
{
    /**
     * @author PhongTX
     * @param array $arrParams
     * @return array|mixed
     * @throws Exception
     */
    public static function getDetailTagById($intTagId)
    {
        $arrResult = array();
        try {
            $db = Core_Global::getDb('core', 'slave');
            $stmt = $db->prepare('CALL sp_be_getDetailTagById(:p_tagid);');
            $stmt->bindParam('p_tagid', $intTagId, PDO::PARAM_INT);
            $stmt->execute();
            $arrResult = $stmt->fetch();
            $stmt->closeCursor();
            unset($stmt);
        } catch (Zend_Db_Exception $e) {
            Core_Global::sendLog($e);
        }
        return $arrResult;
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function insertTag($arrParams)
    {
        ///Define $arrReturn
        $intTagId = 0;
        $arrParams = array_merge(array(
            'strTagName' => NULL,
            'strTagNameEn' => NULL,
            'strTagCode' => NULL,
            'strMetaTitle' => NULL,
            'strMetaDesc' => NULL,
            'strMetaKeyword' => NULL,
            'intStatus' => NUll
        ), $arrParams);
        try {
            // insert DB
            $db_m = Core_Global::getDB('core', 'master');
            $stmt = $db_m->prepare('CALL sp_be_insertTag(:p_tag_name, :p_tag_name_en, :p_tag_code, :p_meta_title, :p_meta_desc, :p_keyword, :p_status, @p_tag_id)');
            //Bind param
            $stmt->bindParam('p_tag_name', $arrParams['strTagName'], PDO::PARAM_STR);
            $stmt->bindParam('p_tag_name_en', $arrParams['strTagNameEn'], PDO::PARAM_STR);
            $stmt->bindParam('p_tag_code', $arrParams['strTagCode'], PDO::PARAM_STR);
            $stmt->bindParam('p_meta_title', $arrParams['strMetaTitle'], PDO::PARAM_STR);
            $stmt->bindParam('p_meta_desc', $arrParams['strMetaDesc'], PDO::PARAM_STR);
            $stmt->bindParam('p_keyword', $arrParams['strMetaKeyword'], PDO::PARAM_STR);
            $stmt->bindParam('p_status', $arrParams['intStatus'], PDO::PARAM_INT);
            //exec func
            $arrReturn = $stmt->execute();
            // Close cursor
            $stmt->closeCursor();

            $stmt = $db_m->query("SELECT @p_tag_id");
            $intTagId = $stmt->fetchColumn();

            unset($stmt);
        } catch (Zend_Db_Exception $e) {
            Core_Global::sendLog($e);
        }
        return $intTagId;
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function updateTag($arrParams)
    {
        ///Define $arrReturn
        $result = 1;
        $arrParams = array_merge(array(
            'strTagName' => NULL,
            'intStatus' => NULL,
            'strTagNameEn' => NULL,
            'strTagCode' => NULL,
            'strMetaTitle' => NULL,
            'strMetaDesc' => NULL,
            'strMetaKeyword' => NULL,
            'intTagId' => NUll
        ), $arrParams);
        try {
            // update DB
            $db_m = Core_Global::getDB('core', 'master');
            $stmt = $db_m->prepare('CALL sp_be_updateTag(:p_tag_name, :p_tag_name_en, :p_tag_code, :p_meta_title, :p_meta_desc, :p_keyword, :p_status, :p_tag_id)');
            //Bind param
            $stmt->bindParam('p_tag_name', $arrParams['strTagName'], PDO::PARAM_STR);
            $stmt->bindParam('p_tag_name_en', $arrParams['strTagNameEn'], PDO::PARAM_STR);
            $stmt->bindParam('p_tag_code', $arrParams['strTagCode'], PDO::PARAM_STR);
            $stmt->bindParam('p_meta_title', $arrParams['strMetaTitle'], PDO::PARAM_STR);
            $stmt->bindParam('p_meta_desc', $arrParams['strMetaDesc'], PDO::PARAM_STR);
            $stmt->bindParam('p_keyword', $arrParams['strMetaKeyword'], PDO::PARAM_STR);
            $stmt->bindParam('p_status', $arrParams['intStatus'], PDO::PARAM_INT);
            $stmt->bindParam('p_tag_id', $arrParams['intTagId'], PDO::PARAM_INT);
            //exec func
            $stmt->execute();
            $result = $stmt->fetch();
            // Close cursor
            $stmt->closeCursor();
            unset($stmt);
        } catch (Zend_Db_Exception $e) {
            Core_Global::sendLog($e);
        }
        return $result;
    }

    /**
     * @author PhongTX
     * @param array $arrParams
     * @return array|mixed
     * @throws Exception
     */
    public static function checkTag($arrParams)
    {
        $arrResult = array();
        $arrParams = array_merge(array(
            'strTagNameEn' => NULL
        ), $arrParams);
        try {
            $db = Core_Global::getDb('core', 'slave');
            $stmt = $db->prepare('CALL sp_be_checkTag(:p_tag_name_en);');
            $stmt->bindParam('p_tag_name_en', $arrParams['strTagNameEn'], PDO::PARAM_STR);
            $stmt->execute();
            $arrResult = $stmt->fetch();
            $stmt->closeCursor();
            unset($stmt);
        } catch (Zend_Db_Exception $e) {
            Core_Global::sendLog($e);
        }
        return $arrResult;
    }

    /**
     * @author PhongTX
     * @param $params
     * @return array
     * @throws Exception
     */
    public static function getListTags($arrParams)
    {
        //Define $arrReturn
        $arrReturn = $arrResult = array();
        $arrParams = array_merge(array(
            'intId' => null,
            'strKeyword' => null,
            'strStatus' => null,
            'intLimit' => null,
            'intOffset' => null
        ), $arrParams);
        try {
            // Get DB
            $db_s = Core_Global::getDb('core', 'slave');
            $stmt = $db_s->prepare('CALL sp_be_getListTags(:p_tag_id, :p_tag_name_en, :p_status, :p_limit, :p_offset, @p_rowcount)');
            //Bind param
            $stmt->bindParam('p_tag_id', $arrParams['intId'], PDO::PARAM_INT);
            $stmt->bindParam('p_tag_name_en', $arrParams['strKeyword'], PDO::PARAM_STR);
            $stmt->bindParam('p_status', $arrParams['strStatus'], PDO::PARAM_STR);
            $stmt->bindParam('p_limit', $arrParams['intLimit'], PDO::PARAM_INT);
            $stmt->bindParam('p_offset', $arrParams['intOffset'], PDO::PARAM_INT);
            //exec func
            $stmt->execute();
            // Fetch result
            $arrReturn = $stmt->fetchAll();
            // Close cursor
            $stmt->closeCursor();
            $stmt = $db_s->query("SELECT @p_rowcount");
            $totalnumRows = $stmt->fetchColumn();
            $arrResult = array('data' => $arrReturn, 'total' => $totalnumRows);
            unset($stmt);
        } catch (Zend_Db_Exception $e) {
            Core_Global::sendLog($e);
        }
        return $arrResult;
    }

    /**
     * @author PhongTX
     * @param $params
     * @return array
     * @throws Exception
     */
    public static function tagsSearchKeyword($arrParams)
    {
        //Define $arrReturn
        $arrReturn = $arrResult = array();
        $arrParams = array_merge(array(
            'strTagNameEn' => null,
            'strStatus' => null,
            'intLimit' => null,
            'intOffset' => null
        ), $arrParams);
        try {
            // Get DB
            $db_s = Core_Global::getDb('core', 'slave');
            $stmt = $db_s->prepare('CALL sp_be_searchTags(:p_tag_name_en, :p_status, :p_limit, :p_offset, @p_rowcount)');
            //Bind param
            $stmt->bindParam('p_tag_name_en', $arrParams['strTagNameEn'], PDO::PARAM_STR);
            $stmt->bindParam('p_status', $arrParams['strStatus'], PDO::PARAM_STR);
            $stmt->bindParam('p_limit', $arrParams['intLimit'], PDO::PARAM_INT);
            $stmt->bindParam('p_offset', $arrParams['intOffset'], PDO::PARAM_INT);
            //exec func
            $stmt->execute();
            // Fetch result
            $arrReturn = $stmt->fetchAll();
            // Close cursor
            $stmt->closeCursor();
            $stmt = $db_s->query("SELECT @p_rowcount");
            $totalnumRows = $stmt->fetchColumn();
            $arrResult = array('data' => $arrReturn, 'total' => $totalnumRows);
            unset($stmt);
        } catch (Zend_Db_Exception $e) {
            Core_Global::sendLog($e);
        }
        return $arrResult;
    }

    /**
     * PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function checkTagArticle($arrParams)
    {
        $result = array();
        $arrParams = array_merge(array(
            'intArticleId' => 0,
            'intTagId' => 0
        ), $arrParams);
        try {
            $storage = Core_Global::getDb('core', 'slave');
            $stmt = $storage->prepare('CALL sp_be_checkTagArticle(:p_article_id,:p_tag_id)');
            $stmt->bindParam('p_article_id', $arrParams['intArticleId'], PDO::PARAM_INT);
            $stmt->bindParam('p_tag_id', $arrParams['intTagId'], PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();
            $stmt->closeCursor();
            unset($stmt);
        } catch (Zend_Db_Exception $e) {
            Core_Global::sendLog($e);
        }
        return isset($result['result']) ? $result['result'] : 0;
    }

    /**
     * PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function insertTagArticle($arrParams)
    {
        $arrParams = array_merge(array(
            'intArticleId' => 0,
            'intTagId' => 0
        ), $arrParams);
        try {
            // insert DB
            $db_m = Core_Global::getDB('core', 'master');
            $stmt = $db_m->prepare('CALL sp_be_insertTagArticle(:article_id, :tag_id)');
            //Bind param
            $stmt->bindParam('article_id', $arrParams['intArticleId'], PDO::PARAM_INT);
            $stmt->bindParam('tag_id', $arrParams['intTagId'], PDO::PARAM_INT);
            //exec func
            $arrReturn = $stmt->execute();
            // Close cursor
            $stmt->closeCursor();
            unset($stmt);
        } catch (Zend_Db_Exception $e) {
            Core_Global::sendLog($e);
        }
        return $arrReturn;
    }

    /**
     * PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function getListTagByArticleId($id)
    {
        //Define $arrReturn
        $arrReturn = array();
        try {
            // Get DB
            $db_s = Core_Global::getDB('core', 'slave');
            $stmt = $db_s->prepare('CALL sp_be_getListTagByArticleId(:p_article_id)');
            //Bind param
            $stmt->bindParam('p_article_id', $id, PDO::PARAM_INT);
            //exec func
            $stmt->execute();
            // Fetch result
            $arrReturn = $stmt->fetchAll();
            // Close cursor
            $stmt->closeCursor();
            unset($stmt);
        } catch (Zend_Db_Exception $e) {
            Core_Global::sendLog($e);
        }
        return $arrReturn;
    }

    /**
     * PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function deleteTagByArticle($id = 0)
    {
        $result = 0;
        try {
            $storage = Core_Global::getDB('core', 'master');
            $stmt = $storage->prepare('CALL sp_be_deleteTagByArticle(:p_article_id)');
            $stmt->bindParam('p_article_id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();
            $stmt->closeCursor();
            unset($stmt);
        } catch (Zend_Db_Exception $e) {
            Core_Global::sendLog($e);
        }
        return isset($result) ? $result : 0;
    }

    /**
     * @author PhongTX
     * @param array $arrParams
     * @return array|mixed
     * @throws Exception
     */
    public static function getDetailTagByTagCode($strTagCode)
    {
        $arrResult = array();
        try {
            $db = Core_Global::getDb('core', 'slave');
            $stmt = $db->prepare('CALL sp_be_getDetailTagByTagCode(:p_tag_code);');
            $stmt->bindParam('p_tag_code', $strTagCode, PDO::PARAM_STR);
            $stmt->execute();
            $arrResult = $stmt->fetch();
            $stmt->closeCursor();
            unset($stmt);
        } catch (Zend_Db_Exception $e) {
            Core_Global::sendLog($e);
        }
        return $arrResult;
    }
}

?>