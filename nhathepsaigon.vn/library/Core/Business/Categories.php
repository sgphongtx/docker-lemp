<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 17/04/15
 * Time: 11:20
 */
class Core_Business_Categories
{

    /**
     * @author PhongTX
     * @param array $arrParams
     * @return array|mixed
     * @throws Exception
     */
    public static function getDetailCategoryById($intCategoryId)
    {
        $arrResult = array();
        try {
            $db = Core_Global::getDb('core', 'slave');
            $stmt = $db->prepare('CALL sp_be_getDetailCategoryById(:p_categoryid);');
            $stmt->bindParam('p_categoryid', $intCategoryId, PDO::PARAM_INT);
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
     * @return array
     * @throws Exception
     */
    public static function getListCategory($arrParams)
    {
        //Define $arrReturn, $arrResult
        $arrReturn = $arrResult = $arrTemp = array();
        $arrParams = array_merge(array(
            'intCategoryType' => NULL,
            'intParentId' => NULL,
            'intStatus' => NULL,
            'intLimit' => NULL,
            'intOffset' => NULL,
            'intShowFolder' => NULL
        ), $arrParams);
        try {
            // Get DB
            $db_s = Core_Global::getDB('core', 'slave');
            $stmt = $db_s->prepare('CALL sp_be_getListCategoryByParent(:p_categorytype, :p_parent_id, :p_status, :p_limit, :p_offset, :p_show_folder, @p_rowcount)');
            //Bind param
            $stmt->bindParam('p_categorytype', $arrParams['intCategoryType'], PDO::PARAM_INT);
            $stmt->bindParam('p_parent_id', $arrParams['intParentId'], PDO::PARAM_INT);
            $stmt->bindParam('p_status', $arrParams['intStatus'], PDO::PARAM_STR);
            $stmt->bindParam('p_limit', $arrParams['intLimit'], PDO::PARAM_INT);
            $stmt->bindParam('p_offset', $arrParams['intOffset'], PDO::PARAM_INT);
            $stmt->bindParam('p_show_folder', $arrParams['intShowFolder'], PDO::PARAM_INT);
            //exec func
            $stmt->execute();
            // Fetch result
            $arrReturn = $stmt->fetchAll();
            if(!empty($arrReturn)){
                foreach($arrReturn as $row){
                    $arrTemp[$row['category_id']] = $row;
                }
            }
            // Close cursor
            $stmt->closeCursor();
            $stmt = $db_s->query("SELECT @p_rowcount");
            $totalnumRows = $stmt->fetchColumn();
            $arrResult = array('data' => $arrTemp, 'total' => $totalnumRows);
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
    public static function insertCategory($arrParams)
    {
        ///Define $arrReturn
        $intCategoryId = 0;
        $arrParams = array_merge(array(
            'intCategoryType' => NULL,
            'intParentId' => NULL,
            'strCateName' => NULL,
            'strCateCode' => NULL,
            'strCateLink' => NULL,
            'strMetaTitle' => NULL,
            'strMetaDesc' => NULL,
            'strMetaKeyword' => NULL,
            'intDisplayOrder' => NULL,
            'intStatus' => NULL,
            'intShowFolder' => NULL
        ), $arrParams);
        try {
            // insert DB
            $db_m = Core_Global::getDB('core', 'master');
            $stmt = $db_m->prepare('CALL sp_be_insertCategory(:p_category_type, :p_parent_id, :p_cate_name, :p_cate_code, :p_cate_link, :p_meta_title, :p_meta_desc, :p_keyword, :p_display_order, :p_status, :p_show_folder, @p_category_id)');
            //Bind param
            $stmt->bindParam('p_category_type', $arrParams['intCategoryType'], PDO::PARAM_INT);
            $stmt->bindParam('p_parent_id', $arrParams['intParentId'], PDO::PARAM_INT);
            $stmt->bindParam('p_cate_name', $arrParams['strCateName'], PDO::PARAM_STR);
            $stmt->bindParam('p_cate_code', $arrParams['strCateCode'], PDO::PARAM_STR);
            $stmt->bindParam('p_cate_link', $arrParams['strCateLink'], PDO::PARAM_STR);
            $stmt->bindParam('p_meta_title', $arrParams['strMetaTitle'], PDO::PARAM_STR);
            $stmt->bindParam('p_meta_desc', $arrParams['strMetaDesc'], PDO::PARAM_STR);
            $stmt->bindParam('p_keyword', $arrParams['strMetaKeyword'], PDO::PARAM_STR);
            $stmt->bindParam('p_display_order', $arrParams['intDisplayOrder'], PDO::PARAM_INT);
            $stmt->bindParam('p_status', $arrParams['intStatus'], PDO::PARAM_INT);
            $stmt->bindParam('p_show_folder', $arrParams['intShowFolder'], PDO::PARAM_INT);
            //exec func
            $arrReturn = $stmt->execute();
            // Close cursor
            $stmt->closeCursor();

            $stmt = $db_m->query("SELECT @p_category_id");
            $intCategoryId = $stmt->fetchColumn();

            unset($stmt);
        } catch (Zend_Db_Exception $e) {
            Core_Global::sendLog($e);
        }
        return $intCategoryId;
    }

    /**
     * @author PhongTX
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function updateCategory($arrParams)
    {
        ///Define $arrReturn
        $result = 1;
        $arrParams = array_merge(array(
            'intCategoryType' => NULL,
            'intCategoryId' => NULL,
            'intParentId' => NULL,
            'strCateName' => NULL,
            'strCateCode' => NULL,
            'strCateLink' => NULL,
            'strMetaTitle' => NULL,
            'strMetaDesc' => NULL,
            'strMetaKeyword' => NULL,
            'intDisplayOrder' => NULL,
            'intStatus' => NULL,
            'intShowFolder' => NULL
        ), $arrParams);
        try {
            // update DB
            $db_m = Core_Global::getDB('core', 'master');
            $stmt = $db_m->prepare('CALL sp_be_updateCategory(:p_category_type, :p_category_id, :p_parent_id, :p_cate_name, :p_cate_code, :p_cate_link, :p_meta_title, :p_meta_desc, :p_keyword, :p_display_order, :p_status, :p_show_folder)');
            //Bind param
            $stmt->bindParam('p_category_type', $arrParams['intCategoryType'], PDO::PARAM_INT);
            $stmt->bindParam('p_category_id', $arrParams['intCategoryId'], PDO::PARAM_INT);
            $stmt->bindParam('p_parent_id', $arrParams['intParentId'], PDO::PARAM_INT);
            $stmt->bindParam('p_cate_name', $arrParams['strCateName'], PDO::PARAM_STR);
            $stmt->bindParam('p_cate_code', $arrParams['strCateCode'], PDO::PARAM_STR);
            $stmt->bindParam('p_cate_link', $arrParams['strCateLink'], PDO::PARAM_STR);
            $stmt->bindParam('p_meta_title', $arrParams['strMetaTitle'], PDO::PARAM_STR);
            $stmt->bindParam('p_meta_desc', $arrParams['strMetaDesc'], PDO::PARAM_STR);
            $stmt->bindParam('p_keyword', $arrParams['strMetaKeyword'], PDO::PARAM_STR);
            $stmt->bindParam('p_display_order', $arrParams['intDisplayOrder'], PDO::PARAM_INT);
            $stmt->bindParam('p_status', $arrParams['intStatus'], PDO::PARAM_INT);
            $stmt->bindParam('p_show_folder', $arrParams['intShowFolder'], PDO::PARAM_INT);
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
    public static function checkCategory($arrParams)
    {
        $arrResult = array();
        $arrParams = array_merge(array(
            'intCategoryType' => NULL,
            'intParentId' => NULL,
            'strCateName' => NULL
        ), $arrParams);
        try {
            $db = Core_Global::getDb('core', 'slave');
            $stmt = $db->prepare('CALL sp_be_checkCategory(:p_categorytype, :p_parentid, :p_catename);');
            $stmt->bindParam('p_categorytype', $arrParams['intCategoryType'], PDO::PARAM_INT);
            $stmt->bindParam('p_parentid', $arrParams['intParentId'], PDO::PARAM_INT);
            $stmt->bindParam('p_catename', $arrParams['strCateName'], PDO::PARAM_STR);
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
     * @todo get id by code
     * @param <string> $strCode
     * @return <int>
     */
    public static function getCateByCode($strCode)
    {
        $arrResult = array();
        try {
            $db = Core_Global::getDb('core', 'slave');
            $stmt = $db->prepare('CALL sp_be_getCategoryByCatecode(:p_cate_code);');
            $stmt->bindParam('p_cate_code', $strCode, PDO::PARAM_STR);
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