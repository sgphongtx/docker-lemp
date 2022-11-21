<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 21/04/15
 * Time: 17:03
 */
class Core_Business_Groups
{
    /**
     * @author PhongTX
     * @param array $arrParams
     * @return array|mixed
     * @throws Exception
     */
    public static function getDetailGroupById($intGroupId)
    {
        $arrResult = array();
        try {
            $db = Core_Global::getDb('core', 'slave');
            $stmt = $db->prepare('CALL sp_be_getDetailGroupById(:p_group_id);');
            $stmt->bindParam('p_group_id', $intGroupId, PDO::PARAM_INT);
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
    public static function insertGroup($arrParams)
    {
        ///Define $arrReturn
        $intGroupId = 0;
        $arrParams = array_merge(array(
            'strGroupName' => NULL,
            'strGroupDesc' => NULL,
            'intStatus' => NUll
        ), $arrParams);
        try {
            // insert DB
            $db_m = Core_Global::getDB('core', 'master');
            $stmt = $db_m->prepare('CALL sp_be_insertGroup(:p_group_name, :p_group_desc, :p_status, @p_group_id)');
            //Bind param
            $stmt->bindParam('p_group_name', $arrParams['strGroupName'], PDO::PARAM_STR);
            $stmt->bindParam('p_group_desc', $arrParams['strGroupDesc'], PDO::PARAM_STR);
            $stmt->bindParam('p_status', $arrParams['intStatus'], PDO::PARAM_INT);
            //exec func
            $arrReturn = $stmt->execute();
            // Close cursor
            $stmt->closeCursor();

            $stmt = $db_m->query("SELECT @p_group_id");
            $intGroupId = $stmt->fetchColumn();

            unset($stmt);
        } catch (Zend_Db_Exception $e) {
            Core_Global::sendLog($e);
        }
        return $intGroupId;
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function updateGroup($arrParams)
    {
        ///Define $arrReturn
        $result = 1;
        $arrParams = array_merge(array(
            'strGroupName' => NULL,
            'strGroupDesc' => NULL,
            'intStatus' => NULL,
            'intGroupId' => NUll
        ), $arrParams);
        try {
            // update DB
            $db_m = Core_Global::getDB('core', 'master');
            $stmt = $db_m->prepare('CALL sp_be_updateGroup(:p_group_name, :p_group_desc, :p_status, :p_group_id)');
            //Bind param
            $stmt->bindParam('p_group_name', $arrParams['strGroupName'], PDO::PARAM_STR);
            $stmt->bindParam('p_group_desc', $arrParams['strGroupDesc'], PDO::PARAM_STR);
            $stmt->bindParam('p_status', $arrParams['intStatus'], PDO::PARAM_INT);
            $stmt->bindParam('p_group_id', $arrParams['intGroupId'], PDO::PARAM_INT);
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
    public static function checkGroup($arrParams)
    {
        $arrResult = array();
        $arrParams = array_merge(array(
            'strGroupName' => NULL
        ), $arrParams);
        try {
            $db = Core_Global::getDb('core', 'slave');
            $stmt = $db->prepare('CALL sp_be_checkGroup(:p_group_name);');
            $stmt->bindParam('p_group_name', $arrParams['strGroupName'], PDO::PARAM_STR);
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
    public static function getListGroups($arrParams)
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
            $stmt = $db_s->prepare('CALL sp_be_getListGroups(:p_status, :p_limit, :p_offset, @p_rowcount)');
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

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function insertPermission($arrParams)
    {
        ///Define $arrReturn
        $result = array();
        $arrParams = array_merge(array(
            'intGroupId' => NULL,
            'intMenuId' => NULL,
            'intActionId' => NULL,
            'intUserId' => NULL
        ), $arrParams);
        try {
            // insert DB
            $db_m = Core_Global::getDB('core', 'master');
            $stmt = $db_m->prepare('CALL sp_be_insertPermission(:p_group_id, :p_action_id, :p_menu_id, :p_user_id)');
            //Bind param
            $stmt->bindParam('p_group_id', $arrParams['intGroupId'], PDO::PARAM_INT);
            $stmt->bindParam('p_menu_id', $arrParams['intMenuId'], PDO::PARAM_INT);
            $stmt->bindParam('p_action_id', $arrParams['intActionId'], PDO::PARAM_INT);
            $stmt->bindParam('p_user_id', $arrParams['intUserId'], PDO::PARAM_INT);
            //exec func
            $stmt->execute();
            $result = $stmt->fetch();
                // Close cursor
            $stmt->closeCursor();

            unset($stmt);
        } catch (Zend_Db_Exception $e) {
            Core_Global::sendLog($e);
        }
        return isset($result['result']) ? $result['result'] : 0;
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function delPermissionByGroupId($intGroupId)
    {
        $result = array();
        try {
            $storage = Core_Global::getDB('core', 'master');
            $stmt = $storage->prepare('CALL sp_be_deletePermissionByGroupId(:p_group_id)');
            $stmt->bindParam('p_group_id', $intGroupId, PDO::PARAM_INT);
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
     * @author PhongTX
     * @param $params
     * @return array
     * @throws Exception
     */
    public static function getPermissionByGroupId($intGroupId)
    {
        //Define $arrReturn
        $arrResult = array();
        try {
            // Get DB
            $db_s = Core_Global::getDb('core', 'slave');
            $stmt = $db_s->prepare('CALL sp_be_getPermissionByGroupId(:p_group_id)');
            //Bind param
            $stmt->bindParam('p_group_id', $intGroupId, PDO::PARAM_INT);
            $stmt->execute();
            $arrResult = $stmt->fetchAll();
            $stmt->closeCursor();
            unset($stmt);
        } catch (Zend_Db_Exception $e) {
            Core_Global::sendLog($e);
        }
        return $arrResult;
    }
}

?>