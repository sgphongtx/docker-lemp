<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 21/04/15
 * Time: 17:03
 */
class Core_Business_Banners
{
    /**
     * @author PhongTX
     * @param array $arrParams
     * @return array|mixed
     * @throws Exception
     */
    public static function getDetailBannerById($arrParams)
    {
        $arrResult = array();
        $arrParams = array_merge(array(
            'intPositionId' => NULL,
            'intCategoryId' => NULL
        ), $arrParams);
        try {
            $db = Core_Global::getDb('core', 'slave');
            $stmt = $db->prepare('CALL sp_be_getDetailBannerById(:p_position_id, :p_category_id);');
            $stmt->bindParam('p_position_id', $arrParams['intPositionId'], PDO::PARAM_INT);
            $stmt->bindParam('p_category_id', $arrParams['intCategoryId'], PDO::PARAM_INT);
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
    public static function insertBanner($arrParams)
    {
        ///Define $arrReturn
        $intId = 0;
        $arrParams = array_merge(array(
            'intPositionId' => NULL,
            'intCategoryId' => NULL,
            'strName' => NULL,
            'strContent' => NULL,
            'intStatus' => NUll
        ), $arrParams);
        try {
            // insert DB
            $db_m = Core_Global::getDB('core', 'master');
            $stmt = $db_m->prepare('CALL sp_be_insertBanner(:p_position_id, :p_category_id, :p_name, :p_content, :p_status, @p_id)');
            //Bind param
            $stmt->bindParam('p_position_id', $arrParams['intPositionId'], PDO::PARAM_INT);
            $stmt->bindParam('p_category_id', $arrParams['intCategoryId'], PDO::PARAM_INT);
            $stmt->bindParam('p_name', $arrParams['strName'], PDO::PARAM_STR);
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
    public static function updateBanner($arrParams)
    {
        ///Define $arrReturn
        $result = 1;
        $arrParams = array_merge(array(
            'intPositionId' => NULL,
            'intCategoryId' => NULL,
            'strName' => NULL,
            'strContent' => NULL,
            'intStatus' => NUll
        ), $arrParams);
        try {
            // update DB
            $db_m = Core_Global::getDB('core', 'master');
            $stmt = $db_m->prepare('CALL sp_be_updateBanner(:p_name, :p_content, :p_status,:p_position_id, :p_category_id)');
            //Bind param
            $stmt->bindParam('p_name', $arrParams['strName'], PDO::PARAM_STR);
            $stmt->bindParam('p_content', $arrParams['strContent'], PDO::PARAM_STR);
            $stmt->bindParam('p_status', $arrParams['intStatus'], PDO::PARAM_INT);
            $stmt->bindParam('p_position_id', $arrParams['intPositionId'], PDO::PARAM_INT);
            $stmt->bindParam('p_category_id', $arrParams['intCategoryId'], PDO::PARAM_INT);
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
    public static function checkBanner($arrParams)
    {
        $arrResult = array();
        $arrParams = array_merge(array(
            'strName' => NULL
        ), $arrParams);
        try {
            $db = Core_Global::getDb('core', 'slave');
            $stmt = $db->prepare('CALL sp_be_checkBanner(:p_name);');
            $stmt->bindParam('p_name', $arrParams['strName'], PDO::PARAM_STR);
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
    public static function getListBanners($arrParams)
    {
        //Define $arrReturn
        $arrReturn = $arrResult = array();
        $arrParams = array_merge(array(
            'intCategoryId' => null,
            'strStatus' => null,
            'intLimit' => null,
            'intOffset' => null
        ), $arrParams);
        try {
            // Get DB
            $db_s = Core_Global::getDb('core', 'slave');
            $stmt = $db_s->prepare('CALL sp_be_getListBanners(:p_category_id, :p_status, :p_limit, :p_offset, @p_rowcount)');
            //Bind param
            $stmt->bindParam('p_category_id', $arrParams['intCategoryId'], PDO::PARAM_INT);
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
    public static function getListTextBanners($arrParams)
    {
        //Define $arrReturn
        $arrReturn = $arrResult = array();
        $arrParams = array_merge(array(
            'intCategoryId' => null,
            'strStatus' => null,
            'intLimit' => null,
            'intOffset' => null
        ), $arrParams);
        try {
            // Get DB
            $db_s = Core_Global::getDb('core', 'slave');
            $stmt = $db_s->prepare('CALL sp_be_getListTextBanners(:p_status, :p_limit, :p_offset, @p_rowcount)');
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
     * @param array $arrParams
     * @return array|mixed
     * @throws Exception
     */
    public static function getDetailTextBannerById($intId)
    {
        $arrResult = array();
        try {
            $db = Core_Global::getDb('core', 'slave');
            $stmt = $db->prepare('CALL sp_be_getDetailTextBannerById(:p_id);');
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
    public static function insertTextBanner($arrParams)
    {
        ///Define $arrReturn
        $intId = 0;
        $arrParams = array_merge(array(
            'strTitle' => NULL,
            'strThumbUrl' => NULL,
            'strShareUrl' => NULL,
            'intStatus' => NUll
        ), $arrParams);
        try {
            // insert DB
            $db_m = Core_Global::getDB('core', 'master');
            $stmt = $db_m->prepare('CALL sp_be_insertTextBanner(:p_title, :p_thumb_url, :p_share_url, :p_status, @p_id)');
            //Bind param
            $stmt->bindParam('p_title', $arrParams['strTitle'], PDO::PARAM_STR);
            $stmt->bindParam('p_thumb_url', $arrParams['strThumbUrl'], PDO::PARAM_STR);
            $stmt->bindParam('p_share_url', $arrParams['strShareUrl'], PDO::PARAM_STR);
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
    public static function updateTextBanner($arrParams)
    {
        ///Define $arrReturn
        $result = 1;
        $arrParams = array_merge(array(
            'strTitle' => NULL,
            'strThumbUrl' => NULL,
            'strShareUrl' => NULL,
            'intStatus' => NUll,
            'intId' => NULL
        ), $arrParams);
        try {
            // update DB
            $db_m = Core_Global::getDB('core', 'master');
            $stmt = $db_m->prepare('CALL sp_be_updateTextBanner(:p_title, :p_thumb_url, :p_share_url, :p_status, :p_id)');
            //Bind param
            $stmt->bindParam('p_title', $arrParams['strTitle'], PDO::PARAM_STR);
            $stmt->bindParam('p_thumb_url', $arrParams['strThumbUrl'], PDO::PARAM_STR);
            $stmt->bindParam('p_share_url', $arrParams['strShareUrl'], PDO::PARAM_STR);
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
}

?>