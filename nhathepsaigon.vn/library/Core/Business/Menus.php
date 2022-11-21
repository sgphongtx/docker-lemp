<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 21/04/15
 * Time: 17:03
 */
class Core_Business_Menus
{
    /**
     * @author PhongTX
     * @param array $arrParams
     * @return array|mixed
     * @throws Exception
     */
    public static function getDetailMenuById($intMenuId)
    {
        $arrResult = array();
        try {
            $db = Core_Global::getDb('core', 'slave');
            $stmt = $db->prepare('CALL sp_be_getDetailMenuById(:p_menu_id);');
            $stmt->bindParam('p_menu_id', $intMenuId, PDO::PARAM_INT);
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
    public static function insertMenu($arrParams)
    {
        ///Define $arrReturn
        $intMenuId = 0;
        $arrParams = array_merge(array(
            'intParentId' => NULL,
            'strMenuName' => NULL,
            'strMenuCode' => NULL,
            'strMenuUrl' => NULL,
            'intDisplayOrder' => NULL,
            'intStatus' => 1 //status = 0: Delete, status = 1: Active, status = 2: Inactive
        ), $arrParams);
        try {
            // insert DB
            $db_m = Core_Global::getDB('core', 'master');
            $stmt = $db_m->prepare('CALL sp_be_insertMenu(:p_menu_name, :p_menu_code, :p_parent_id, :p_url, :p_display_order, :p_status, @p_menu_id)');
            //Bind param
            $stmt->bindParam('p_menu_name', $arrParams['strMenuName'], PDO::PARAM_STR);
            $stmt->bindParam('p_menu_code', $arrParams['strMenuCode'], PDO::PARAM_STR);
            $stmt->bindParam('p_parent_id', $arrParams['intParentId'], PDO::PARAM_INT);
            $stmt->bindParam('p_url', $arrParams['strMenuUrl'], PDO::PARAM_STR);
            $stmt->bindParam('p_display_order', $arrParams['intDisplayOrder'], PDO::PARAM_INT);
            $stmt->bindParam('p_status', $arrParams['intStatus'], PDO::PARAM_INT);
            //exec func
            $arrReturn = $stmt->execute();
            // Close cursor
            $stmt->closeCursor();

            $stmt = $db_m->query("SELECT @p_menu_id");
            $intMenuId = $stmt->fetchColumn();

            unset($stmt);
        } catch (Zend_Db_Exception $e) {
            Core_Global::sendLog($e);
        }
        return $intMenuId;
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function updateMenu($arrParams)
    {
        ///Define $arrReturn
        $result = 1;
        $arrParams = array_merge(array(
            'intParentId' => NULL,
            'strMenuName' => NULL,
            'strMenuCode' => NULL,
            'strMenuUrl' => NULL,
            'intDisplayOrder' => NULL,
            'intStatus' => NULL, //status = 0: Delete, status = 1: Active, status = 2: Inactive
            'intMenuId' => NUll
        ), $arrParams);
        try {
            // update DB
            $db_m = Core_Global::getDB('core', 'master');
            $stmt = $db_m->prepare('CALL sp_be_updateMenu(:p_menu_name, :p_menu_code, :p_parent_id, :p_url, :p_display_order, :p_status, :p_menu_id)');
            //Bind param
            $stmt->bindParam('p_menu_name', $arrParams['strMenuName'], PDO::PARAM_STR);
            $stmt->bindParam('p_menu_code', $arrParams['strMenuCode'], PDO::PARAM_STR);
            $stmt->bindParam('p_parent_id', $arrParams['intParentId'], PDO::PARAM_INT);
            $stmt->bindParam('p_url', $arrParams['strMenuUrl'], PDO::PARAM_STR);
            $stmt->bindParam('p_display_order', $arrParams['intDisplayOrder'], PDO::PARAM_INT);
            $stmt->bindParam('p_status', $arrParams['intStatus'], PDO::PARAM_INT);
            $stmt->bindParam('p_menu_id', $arrParams['intMenuId'], PDO::PARAM_INT);
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
    public static function checkMenu($arrParams)
    {
        $arrResult = array();
        $arrParams = array_merge(array(
            'intMenuId' => NULL,
            'strMenuCode' => NULL
        ), $arrParams);
        try {
            $db = Core_Global::getDb('core', 'slave');
            $stmt = $db->prepare('CALL sp_be_checkMenu(:p_menu_id, :p_menu_code);');
            $stmt->bindParam('p_menu_id', $arrParams['intMenuId'], PDO::PARAM_INT);
            $stmt->bindParam('p_menu_code', $arrParams['strMenuCode'], PDO::PARAM_STR);
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
     * @return array
     * @throws Exception
     */
    public static function getListMenus()
    {
        //Define $arrReturn
        $arrReturn = array();
        try {
            // Get DB
            $db_s = Core_Global::getDb('core', 'slave');
            $stmt = $db_s->prepare('CALL sp_be_getListMenus()');
            //exec func
            $stmt->execute();
            // Fetch result
            while ($row = $stmt->fetch()) {
                $arrReturn[$row['menu_id']] = $row;
            }
            // Close cursor
            $stmt->closeCursor();
            unset($stmt);
        } catch (Zend_Db_Exception $e) {
            Core_Global::sendLog($e);
        }
        return $arrReturn;
    }

    /**
     * @author PhongTX
     * @param array $arrParams
     * @return array|mixed
     * @throws Exception
     */
    public static function getDetailMenuByMenuCode($strMenuCode)
    {
        $arrResult = array();
        try {
            $db = Core_Global::getDb('core', 'slave');
            $stmt = $db->prepare('CALL sp_be_getDetailMenuByMenuCode(:p_menu_code);');
            $stmt->bindParam('p_menu_code', $strMenuCode, PDO::PARAM_STR);
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