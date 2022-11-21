<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 21/04/15
 * Time: 17:03
 */
class Core_Business_Topics
{
    /**
     * @author PhongTX
     * @param array $arrParams
     * @return array|mixed
     * @throws Exception
     */
    public static function getDetailTopicById($intTopicId)
    {
        $arrResult = array();
        try {
            $db = Core_Global::getDb('core', 'slave');
            $stmt = $db->prepare('CALL sp_be_getDetailTopicById(:p_topicid);');
            $stmt->bindParam('p_topicid', $intTopicId, PDO::PARAM_INT);
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
    public static function insertTopic($arrParams)
    {
        ///Define $arrReturn
        $intTopicId = 0;
        $arrParams = array_merge(array(
            'strTopicName' => NULL,
            'intCategoryId' => NULL,
            'intAuthorId' => NULL,
            'intStatus' => NUll
        ), $arrParams);
        try {
            // insert DB
            $db_m = Core_Global::getDB('core', 'master');
            $stmt = $db_m->prepare('CALL sp_be_insertTopic(:p_category_id, :p_topic_name, :p_author_id, :p_status, @p_topic_id)');
            //Bind param
            $stmt->bindParam('p_category_id', $arrParams['intCategoryId'], PDO::PARAM_INT);
            $stmt->bindParam('p_topic_name', $arrParams['strTopicName'], PDO::PARAM_STR);
            $stmt->bindParam('p_author_id', $arrParams['intAuthorId'], PDO::PARAM_INT);
            $stmt->bindParam('p_status', $arrParams['intStatus'], PDO::PARAM_INT);
            //exec func
            $arrReturn = $stmt->execute();
            // Close cursor
            $stmt->closeCursor();

            $stmt = $db_m->query("SELECT @p_topic_id");
            $intTopicId = $stmt->fetchColumn();

            unset($stmt);
        } catch (Zend_Db_Exception $e) {
            Core_Global::sendLog($e);
        }
        return $intTopicId;
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function updateTopic($arrParams)
    {
        ///Define $arrReturn
        $result = 1;
        $arrParams = array_merge(array(
            'strTopicName' => NULL,
            'intCategoryId' => NULL,
            'intStatus' => NULL,
            'intTopicId' => NUll
        ), $arrParams);
        try {
            // update DB
            $db_m = Core_Global::getDB('core', 'master');
            $stmt = $db_m->prepare('CALL sp_be_updateTopic(:p_topic_name, :p_category_id, :p_status, :p_topic_id)');
            //Bind param
            $stmt->bindParam('p_topic_name', $arrParams['strTopicName'], PDO::PARAM_STR);
            $stmt->bindParam('p_category_id', $arrParams['intCategoryId'], PDO::PARAM_INT);
            $stmt->bindParam('p_status', $arrParams['intStatus'], PDO::PARAM_INT);
            $stmt->bindParam('p_topic_id', $arrParams['intTopicId'], PDO::PARAM_INT);
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
    public static function checkTopic($arrParams)
    {
        $arrResult = array();
        try {
            $db = Core_Global::getDb('core', 'slave');
            $stmt = $db->prepare('CALL sp_be_checkTopic(:p_topic_name);');
            $stmt->bindParam('p_topic_name', $arrParams['strTopicName'], PDO::PARAM_STR);
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
    public static function getListTopics($arrParams)
    {
        //Define $arrReturn
        $arrReturn = $arrResult = array();
        $arrParams = array_merge(array(
            'strStatus' => null,
            'intLimit' => null,
            'intOffset' => null
        ), $arrParams);
        try {
            // Get DB
            $db_s = Core_Global::getDb('core', 'slave');
            $stmt = $db_s->prepare('CALL sp_be_getListTopics(:p_status, :p_limit, :p_offset, @p_rowcount)');
            //Bind param
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
}

?>