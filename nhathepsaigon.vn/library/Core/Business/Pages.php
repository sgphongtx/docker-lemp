<?php
/**
 * @name        :   Core_Model_Pages
 * @author      :   PhongTX
 * @copyright   :   Fpt Online
 * Date: 24/12/14
 * Time: 08:45
 */
class Core_Business_Pages
{
    /**
     * PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function insertPage($arrParams)
    {
        //Define $id
        $id = 0;
        $arrParams = array_merge(array(
            'strPageKey' => NULL,
            'strName' => NULL,
            'strShareUrl' => NULL,
            'strMetaTitle' => NULL,
            'strMetaDesc' => NULL,
            'strMetaKeyword' => NULL,
            'ckeditor' => NULL,
            'intStatus' => NULL,
            'intUserId' => NULL,
            'intPublishTime' => NULL
        ), $arrParams);
        try {
            // insert DB
            $db_m = Core_Global::getDB('core', 'master');
            $stmt = $db_m->prepare('CALL sp_be_insertPages(:p_page_key, :p_name, :p_share_url, :p_meta_title, :p_meta_desc, :p_meta_keyword, :p_content, :p_status, :p_user_id, :p_ptime, @p_id)');
            //Bind param
            $stmt->bindParam('p_page_key', $arrParams['strPageKey'], PDO::PARAM_STR);
            $stmt->bindParam('p_name', $arrParams['strName'], PDO::PARAM_STR);
            $stmt->bindParam('p_share_url', $arrParams['strShareUrl'], PDO::PARAM_STR);
            $stmt->bindParam('p_meta_title', $arrParams['strMetaTitle'], PDO::PARAM_STR);
            $stmt->bindParam('p_meta_desc', $arrParams['strMetaDesc'], PDO::PARAM_STR);
            $stmt->bindParam('p_meta_keyword', $arrParams['strMetaKeyword'], PDO::PARAM_STR);
            $stmt->bindParam('p_content', $arrParams['ckeditor'], PDO::PARAM_STR);
            $stmt->bindParam('p_status', $arrParams['intStatus'], PDO::PARAM_INT);
            $stmt->bindParam('p_user_id', $arrParams['intUserId'], PDO::PARAM_INT);
            $stmt->bindParam('p_ptime', $arrParams['intPublishTime'], PDO::PARAM_INT);
            //exec func
            $arrReturn = $stmt->execute();
            // Close cursor
            $stmt->closeCursor();

            $stmt = $db_m->query("SELECT @p_id");
            $id = $stmt->fetchColumn();

            unset($stmt);
        } catch (Zend_Db_Exception $e) {
            Core_Global::sendLog($e);
        }
        return $id;
    }

    /**
     * PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function updatePage($arrParams)
    {
        //Define $arrReturn
        $arrReturn = 0;
        $arrParams = array_merge(array(
            'strPageKey' => NULL,
            'strName' => NULL,
            'strShareUrl' => NULL,
            'strMetaTitle' => NULL,
            'strMetaDesc' => NULL,
            'strMetaKeyword' => NULL,
            'ckeditor' => NULL,
            'intViews' => NULL,
            'intStatus' => NULL,
            'intUserId' => NULL,
            'intPublishTime' => NULL,
            'intId' => NULL
        ), $arrParams);
        try {
            // insert DB
            $db_m = Core_Global::getDB('core', 'master');
            $stmt = $db_m->prepare('CALL sp_be_updatePages(:p_page_key, :p_name, :p_share_url, :p_meta_title, :p_meta_desc, :p_meta_keyword, :p_content, :p_views, :p_status, :p_user_id, :p_ptime,:p_id)');
            //Bind param
            $stmt->bindParam('p_page_key', $arrParams['strPageKey'], PDO::PARAM_STR);
            $stmt->bindParam('p_name', $arrParams['strName'], PDO::PARAM_STR);
            $stmt->bindParam('p_share_url', $arrParams['strShareUrl'], PDO::PARAM_STR);
            $stmt->bindParam('p_meta_title', $arrParams['strMetaTitle'], PDO::PARAM_STR);
            $stmt->bindParam('p_meta_desc', $arrParams['strMetaDesc'], PDO::PARAM_STR);
            $stmt->bindParam('p_meta_keyword', $arrParams['strMetaKeyword'], PDO::PARAM_STR);
            $stmt->bindParam('p_content', $arrParams['ckeditor'], PDO::PARAM_STR);
            $stmt->bindParam('p_views', $arrParams['intViews'], PDO::PARAM_INT);
            $stmt->bindParam('p_status', $arrParams['intStatus'], PDO::PARAM_INT);
            $stmt->bindParam('p_user_id', $arrParams['intUserId'], PDO::PARAM_INT);
            $stmt->bindParam('p_ptime', $arrParams['intPublishTime'], PDO::PARAM_INT);
            $stmt->bindParam('p_id', $arrParams['intId'], PDO::PARAM_INT);
            //exec func
            $arrReturn = $stmt->execute();
            // Close cursor
            $stmt->closeCursor();
            unset($stmt);
        } catch (Zend_Db_Exception $e) {
            Core_Global::sendLog($e);
        };
        return $arrReturn;
    }

    /**
     * PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function getDetailPageById($id)
    {
        //Define $arrReturn
        $arrReturn = array();
        try {
            // Get DB
            $db_s = Core_Global::getDB('core', 'slave');
            $stmt = $db_s->prepare('CALL sp_be_getDetailPages(:p_id)');
            //Bind param
            $stmt->bindParam('p_id', $id, PDO::PARAM_INT);
            //exec func
            $stmt->execute();
            // Fetch result
            $arrReturn = $stmt->fetch();
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
    public static function getListPage($params)
    {
        //Define $arrReturn
        $arrReturn = $arrResult = array();
        $arrParams = array_merge(array(
            'intId' => NULL,
            'strKeyword' => NULL,
            'strStatus' => null,
            'intUserId' => null,
            'intLimit' => 0,
            'intOffset' => 0,
            'intPageview' => null
        ), $params);

        try {
            // Get DB
            $db_s = Core_Global::getDB('core', 'slave');
            $stmt = $db_s->prepare('CALL sp_be_getListPages(:p_id, :p_name, :p_status, :p_user_id, :p_limit, :p_offset, :p_views, @p_rowcount)');
            //Bind param
            $stmt->bindParam('p_id', $arrParams['intId'], PDO::PARAM_INT);
            $stmt->bindParam('p_name', $arrParams['strKeyword'], PDO::PARAM_STR);
            $stmt->bindParam('p_status', $arrParams['strStatus'], PDO::PARAM_STR);
            $stmt->bindParam('p_user_id', $arrParams['intUserId'], PDO::PARAM_INT);
            $stmt->bindParam('p_limit', $arrParams['intLimit'], PDO::PARAM_INT);
            $stmt->bindParam('p_offset', $arrParams['intOffset'], PDO::PARAM_INT);
            $stmt->bindParam('p_views', $arrParams['intPageview'], PDO::PARAM_INT);
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
    public static function getDetailPageByPageKey($key)
    {
        //Define $arrReturn
        $arrReturn = array();
        try {
            // Get DB
            $db_s = Core_Global::getDB('core', 'slave');
            $stmt = $db_s->prepare('CALL sp_be_getDetailPagesByPageKey(:p_page_key)');
            //Bind param
            $stmt->bindParam('p_page_key', $key, PDO::PARAM_STR);
            //exec func
            $stmt->execute();
            // Fetch result
            $arrReturn = $stmt->fetch();
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