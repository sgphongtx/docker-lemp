<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 08/04/2015
 * Time: 1:41 SA
 */
class Core_Business_Users
{
    /**
     * @author PhongTX
     * @param array $arrParams
     * @return array|mixed
     */
    public static function getUserByEmail($arrParams = array())
    {
        $arrResult = array();
        $arrParams = array_merge(array(
            "email" => null,
            "password" => null,
        ), $arrParams);
        try {
            $db = Core_Global::getDb('core', 'slave');
            $stmt = $db->prepare('CALL sp_be_getUserByEmail(:p_email, :p_password);');
            $stmt->bindParam('p_email', $arrParams['email'], PDO::PARAM_STR);
            $stmt->bindParam('p_password', $arrParams['password'], PDO::PARAM_STR);
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
     * @param array $arrParams
     * @return array|mixed
     * @throws Exception
     */
    public static function getDetailUserById($intUserId)
    {
        $arrResult = array();
        try {
            $db = Core_Global::getDb('core', 'slave');
            $stmt = $db->prepare('CALL sp_be_getDetailUserById(:p_userid);');
            $stmt->bindParam('p_userid', $intUserId, PDO::PARAM_INT);
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
     * @param $arrParams
     * @return string
     * @throws Exception
     */
    public static function checkUserByEmail($arrParams)
    {
        $arrResult = array();
        try {
            $db = Core_Global::getDb('core', 'slave');
            $stmt = $db->prepare('CALL sp_be_checkUserByEmail(:p_email);');
            $stmt->bindParam('p_email', $arrParams['strEmail'], PDO::PARAM_STR);
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
    public static function getListUsers($arrParams)
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
            $stmt = $db_s->prepare('CALL sp_be_getListUsers(:p_status, :p_limit, :p_offset, @p_rowcount)');
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
    public static function insertUser($arrParams)
    {
        ///Define $arrReturn
        $intUserId = 0;
        $arrParams = array_merge(array(
            'strEmail' => NULL,
            'intGroupId' => NULL,
            'strFullname' => NULL,
            'strPassWord' => NULL,
            'intBirthday' => NULL,
            'intGender' => NULL,
            'strPhone' => NULL,
            'intStatus' => 1,
            'intRole' => 1,
            'strAddress' => NUll
        ), $arrParams);
        try {
            // insert DB
            $db_m = Core_Global::getDB('core', 'master');
            $stmt = $db_m->prepare('CALL sp_be_insertUser(:p_email, :p_group_id, :p_fullname, :p_password, :p_birthday, :p_gender, :p_phone, :p_status, :p_address, :p_role, @p_user_id)');
            //Bind param
            $stmt->bindParam('p_email', $arrParams['strEmail'], PDO::PARAM_STR);
            $stmt->bindParam('p_group_id', $arrParams['intGroupId'], PDO::PARAM_INT);
            $stmt->bindParam('p_fullname', $arrParams['strFullname'], PDO::PARAM_STR);
            $stmt->bindParam('p_password', $arrParams['strPassWord'], PDO::PARAM_STR);
            $stmt->bindParam('p_birthday', $arrParams['intBirthday'], PDO::PARAM_INT);
            $stmt->bindParam('p_gender', $arrParams['intGender'], PDO::PARAM_INT);
            $stmt->bindParam('p_phone', $arrParams['strPhone'], PDO::PARAM_STR);
            $stmt->bindParam('p_status', $arrParams['intStatus'], PDO::PARAM_INT);
            $stmt->bindParam('p_address', $arrParams['strAddress'], PDO::PARAM_STR);
            $stmt->bindParam('p_role', $arrParams['intRole'], PDO::PARAM_INT);
            //exec func
            $arrReturn = $stmt->execute();
            // Close cursor
            $stmt->closeCursor();

            $stmt = $db_m->query("SELECT @p_user_id");
            $intUserId = $stmt->fetchColumn();

            unset($stmt);
        } catch (Zend_Db_Exception $e) {
            Core_Global::sendLog($e);
        }
        return $intUserId;
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function updateUserById($arrParams)
    {
        $result = 1;
        $arrParams = array_merge(array(
            'strEmail' => NULL,
            'intGroupId' => NULL,
            'strFullname' => NULL,
            'strPassWord' => NULL,
            'intBirthday' => NULL,
            'intGender' => NULL,
            'strPhone' => NULL,
            'intStatus' => NULL,
            'strAddress' => NUll,
            'intRole' => NUll,
            'strCodeActive' => NULL,
            'intUserId' => NULL
        ), $arrParams);
        try {
            $db_m = Core_Global::getDB('core', 'master');
            $stmt = $db_m->prepare('CALL sp_be_updateUser(:p_email, :p_group_id, :p_fullname, :p_password, :p_birthday, :p_gender, :p_phone, :p_status, :p_address, :p_role, :p_code_active, :p_user_id)');
            //Bind param
            $stmt->bindParam('p_email', $arrParams['strEmail'], PDO::PARAM_STR);
            $stmt->bindParam('p_group_id', $arrParams['intGroupId'], PDO::PARAM_INT);
            $stmt->bindParam('p_fullname', $arrParams['strFullname'], PDO::PARAM_INT);
            $stmt->bindParam('p_password', $arrParams['strPassWord'], PDO::PARAM_STR);
            $stmt->bindParam('p_birthday', $arrParams['intBirthday'], PDO::PARAM_STR);
            $stmt->bindParam('p_gender', $arrParams['intGender'], PDO::PARAM_INT);
            $stmt->bindParam('p_phone', $arrParams['strPhone'], PDO::PARAM_STR);
            $stmt->bindParam('p_status', $arrParams['intStatus'], PDO::PARAM_INT);
            $stmt->bindParam('p_address', $arrParams['strAddress'], PDO::PARAM_STR);
            $stmt->bindParam('p_role', $arrParams['intRole'], PDO::PARAM_INT);
            $stmt->bindParam('p_code_active', $arrParams['strCodeActive'], PDO::PARAM_STR);
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
        return $result;
    }
}

?>