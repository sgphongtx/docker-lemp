<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 21/04/15
 * Time: 17:03
 */
class Core_Business_Comment
{
    /**
     * @author PhongTX
     * @param array $arrParams
     * @return array|mixed
     * @throws Exception
     */
    public static function getDetailCommentById($intId)
    {
        $arrResult = array();
        try {
            $db = Core_Global::getDb('core', 'slave');
            $stmt = $db->prepare('CALL sp_be_getDetailCommentById(:p_id);');
            $stmt->bindParam('p_id', $intId, PDO::PARAM_INT);
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
    public static function insertComment($arrParams)
    {
        ///Define $arrReturn
        $intId = 0;
        $arrParams = array_merge(array(
            'intArticleId' => NULL,
            'strName' => NULL,
            'strEmail' => NULL,
            'strContent' => NULL,
            'intStatus' => 2 //0: Xoa, 1: Duyet, 2: Cho Duyet, 3: Khong duyet
        ), $arrParams);
        try {
            // insert DB
            $db_m = Core_Global::getDB('core', 'master');
            $stmt = $db_m->prepare('CALL sp_be_insertComment(:p_article_id, :p_name, :p_email, :p_content, :p_status, @p_id)');
            //Bind param
            $stmt->bindParam('p_article_id', $arrParams['intArticleId'], PDO::PARAM_INT);
            $stmt->bindParam('p_name', $arrParams['strName'], PDO::PARAM_STR);
            $stmt->bindParam('p_email', $arrParams['strEmail'], PDO::PARAM_STR);
            $stmt->bindParam('p_content', $arrParams['strContent'], PDO::PARAM_STR);
            $stmt->bindParam('p_status', $arrParams['intStatus'], PDO::PARAM_INT);
            //exec func
            $arrReturn = $stmt->execute();
            // Close cursor
            $stmt->closeCursor();

            $stmt = $db_m->query("SELECT @p_id");
            $intId = $stmt->fetchColumn();

            unset($stmt);
        } catch (Zend_Db_Exception $e) {
            Core_Global::sendLog($e);
        }
        return $intId;
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function updateComment($arrParams)
    {
        ///Define $arrReturn
        $result = 1;
        $arrParams = array_merge(array(
            'strContent' => NULL,
            'intUserApproveId' => NULL,
            'intId' => NUll
        ), $arrParams);
        try {
            // update DB
            $db_m = Core_Global::getDB('core', 'master');
            $stmt = $db_m->prepare('CALL sp_be_updateComment(:p_content, :p_status, :p_user_approve_id, :p_id)');
            //Bind param
            $stmt->bindParam('p_content', $arrParams['strContent'], PDO::PARAM_STR);
            $stmt->bindParam('p_status', $arrParams['intStatus'], PDO::PARAM_INT);
            $stmt->bindParam('p_user_approve_id', $arrParams['intUserApproveId'], PDO::PARAM_INT);
            $stmt->bindParam('p_id', $arrParams['intId'], PDO::PARAM_INT);
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
     * @param $params
     * @return array
     * @throws Exception
     */
    public static function getListComment($arrParams)
    {
        //Define $arrReturn
        $arrReturn = $arrResult = array();
        $arrParams = array_merge(array(
            'intFromdate' => NULL,
            'intTodate' => NULL,
            'intArticleId' => NULL,
            'strStatus' => null,
            'intLimit' => 0,
            'intOffset' => 0
        ), $arrParams);
        try {
            // Get DB
            $db_s = Core_Global::getDB('core', 'slave');
            $stmt = $db_s->prepare('CALL sp_be_getListComments(:p_fromdate, :p_todate, :p_article_id, :p_status, :p_limit, :p_offset, @p_rowcount)');
            //Bind param
            $stmt->bindParam('p_fromdate', $arrParams['intFromdate'], PDO::PARAM_INT);
            $stmt->bindParam('p_todate', $arrParams['intTodate'], PDO::PARAM_INT);
            $stmt->bindParam('p_article_id', $arrParams['intArticleId'], PDO::PARAM_INT);
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
    public static function getListCommentByArticleId($id)
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
}

?>