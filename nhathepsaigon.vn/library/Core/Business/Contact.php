<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 21/04/15
 * Time: 17:03
 */
class Core_Business_Contact
{
    /**
     * @author PhongTX
     * @param $intId
     * @return array|mixed
     * @throws Exception
     */
    public static function getDetailContactById($intId)
    {
        $arrResult = array();
        try {
            $db = Core_Global::getDb('core', 'slave');
            $stmt = $db->prepare('CALL sp_be_getDetailContactById(:p_id);');
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
    public static function insertContact($arrParams)
    {
        ///Define $arrReturn
        $intId = 0;
        $arrParams = array_merge(array(
            'intType' => NULL,
            'strName' => NULL,
            'strEmail' => NULL,
            'strPhone' => NULL,
            'strContent' => NULL,
            'intStatus' => NUll
        ), $arrParams);
        try {
            // insert DB
            $db_m = Core_Global::getDB('core', 'master');
            $stmt = $db_m->prepare('CALL sp_be_insertContact(:p_type, :p_name, :p_email, :p_phone, :p_content, :p_status, @p_id)');
            //Bind param
            $stmt->bindParam('p_type', $arrParams['intType'], PDO::PARAM_STR);
            $stmt->bindParam('p_name', $arrParams['strName'], PDO::PARAM_STR);
            $stmt->bindParam('p_email', $arrParams['strEmail'], PDO::PARAM_STR);
            $stmt->bindParam('p_phone', $arrParams['strPhone'], PDO::PARAM_STR);
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
    public static function updateContact($arrParams)
    {
        ///Define $arrReturn
        $result = 1;
        $arrParams = array_merge(array(
            'strName' => NULL,
            'strEmail' => NULL,
            'strPhone' => NULL,
            'strContent' => NULL,
            'intStatus' => NUll,
            'intId' => NULL
        ), $arrParams);
        try {
            // update DB
            $db_m = Core_Global::getDB('core', 'master');
            $stmt = $db_m->prepare('CALL sp_be_updateContact(:p_name, :p_email, :p_phone, :p_content, :p_status, :p_id)');
            //Bind param
            $stmt->bindParam('p_name', $arrParams['strName'], PDO::PARAM_STR);
            $stmt->bindParam('p_email', $arrParams['strEmail'], PDO::PARAM_STR);
            $stmt->bindParam('p_phone', $arrParams['strPhone'], PDO::PARAM_STR);
            $stmt->bindParam('p_content', $arrParams['strContent'], PDO::PARAM_STR);
            $stmt->bindParam('p_status', $arrParams['intStatus'], PDO::PARAM_INT);
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
    public static function getListContact($arrParams)
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
            $stmt = $db_s->prepare('CALL sp_be_getListContact(:p_status, :p_limit, :p_offset, @p_rowcount)');
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