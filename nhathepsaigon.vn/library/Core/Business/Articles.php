<?php
/**
 * @name        :   Core_Model_Articles
 * @author      :   PhongTX
 * @copyright   :   Fpt Online
 * Date: 24/12/14
 * Time: 08:45
 */
class Core_Business_Articles
{
    /**
     * PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function insertArticle($arrParams)
    {
        //Define $id
        $id = 0;
        $arrParams = array_merge(array(
            'intCate' => NULL,
            'strThumbUrl' => NULL,
            'strShareUrl' => NULL,
            'strOriginalLink' => NULL,
            'strTitle' => NULL,
            'strDesc' => NULL,
            'strMetaTitle' => NULL,
            'strMetaDesc' => NULL,
            'strMetaKeyword' => NULL,
            'ckeditor' => NULL,
            'intIsComment' => NULL,
            'intCommentType' => NULL,
            'intStatus' => NULL,
            'intUserId' => NULL,
            'intUserApproveId' => NULL,
            'intPublishTime' => NULL,
            'strLinkVideo' => NULL,
            'intType' => NULL
        ), $arrParams);
        try {
            // insert DB
            $db_m = Core_Global::getDB('core', 'master');
            $stmt = $db_m->prepare('CALL sp_be_insertArticles(:p_category_id,:p_thumb_url,:p_share_url, :p_original_link, :p_title,:p_lead,:p_meta_title,:p_meta_desc,:p_meta_keyword,:p_content,:p_is_comment,:p_comment_type,:p_status,:p_user_id,:p_user_approve_id,:p_ptime,:p_link_video,:p_type,@p_article_id)');
            //Bind param
            $stmt->bindParam('p_category_id', $arrParams['intCate'], PDO::PARAM_INT);
            $stmt->bindParam('p_thumb_url', $arrParams['strThumbUrl'], PDO::PARAM_STR);
            $stmt->bindParam('p_original_link', $arrParams['strOriginalLink'], PDO::PARAM_STR);
            $stmt->bindParam('p_share_url', $arrParams['strShareUrl'], PDO::PARAM_STR);
            $stmt->bindParam('p_title', $arrParams['strTitle'], PDO::PARAM_STR);
            $stmt->bindParam('p_lead', $arrParams['strDesc'], PDO::PARAM_STR);
            $stmt->bindParam('p_meta_title', $arrParams['strMetaTitle'], PDO::PARAM_STR);
            $stmt->bindParam('p_meta_desc', $arrParams['strMetaDesc'], PDO::PARAM_STR);
            $stmt->bindParam('p_meta_keyword', $arrParams['strMetaKeyword'], PDO::PARAM_STR);
            $stmt->bindParam('p_content', $arrParams['ckeditor'], PDO::PARAM_STR);
            $stmt->bindParam('p_is_comment', $arrParams['intIsComment'], PDO::PARAM_INT);
            $stmt->bindParam('p_comment_type', $arrParams['intCommentType'], PDO::PARAM_INT);
            $stmt->bindParam('p_status', $arrParams['intStatus'], PDO::PARAM_INT);
            $stmt->bindParam('p_user_id', $arrParams['intUserId'], PDO::PARAM_INT);
            $stmt->bindParam('p_user_approve_id', $arrParams['intUserApproveId'], PDO::PARAM_INT);
            $stmt->bindParam('p_ptime', $arrParams['intPublishTime'], PDO::PARAM_INT);
            $stmt->bindParam('p_link_video', $arrParams['strLinkVideo'], PDO::PARAM_STR);
            $stmt->bindParam('p_type', $arrParams['intType'], PDO::PARAM_INT);
            //exec func
            $arrReturn = $stmt->execute();
            // Close cursor
            $stmt->closeCursor();

            $stmt = $db_m->query("SELECT @p_article_id");
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
    public static function updateArticle($arrParams)
    {
        //Define $arrReturn
        $arrReturn = 0;
        $arrParams = array_merge(array(
            'intCate' => NULL,
            'strThumbUrl' => NULL,
            'strShareUrl' => NULL,
            'strTitle' => NULL,
            'strDesc' => NULL,
            'strMetaTitle' => NULL,
            'strMetaDesc' => NULL,
            'strMetaKeyword' => NULL,
            'ckeditor' => NULL,
            'intIsComment' => NULL,
            'intCommentType' => NULL,
            'intViews' => NULL,
            'intComments' => NULL,
            'intStatus' => NULL,
            'intUserId' => NULL,
            'intUserApproveId' => NULL,
            'intPublishTime' => NULL,
            'intArticleId' => NULL,
            'strLinkVideo' => NULL,
            'intType' => NULL
        ), $arrParams);
        try {
            // insert DB
            $db_m = Core_Global::getDB('core', 'master');
            $stmt = $db_m->prepare('CALL sp_be_updateArticles(:p_category_id,:p_thumb_url,:p_share_url,:p_title,:p_lead,:p_meta_title,:p_meta_desc,:p_meta_keyword,:p_content,:p_is_comment,:p_comment_type,:p_views,:p_comments,:p_status,:p_user_id,:p_user_approve_id,:p_ptime,:p_article_id,:p_link_video,:p_type)');
            //Bind param
            $stmt->bindParam('p_category_id', $arrParams['intCate'], PDO::PARAM_INT);
            $stmt->bindParam('p_thumb_url', $arrParams['strThumbUrl'], PDO::PARAM_STR);
            $stmt->bindParam('p_share_url', $arrParams['strShareUrl'], PDO::PARAM_STR);
            $stmt->bindParam('p_title', $arrParams['strTitle'], PDO::PARAM_STR);
            $stmt->bindParam('p_lead', $arrParams['strDesc'], PDO::PARAM_STR);
            $stmt->bindParam('p_meta_title', $arrParams['strMetaTitle'], PDO::PARAM_STR);
            $stmt->bindParam('p_meta_desc', $arrParams['strMetaDesc'], PDO::PARAM_STR);
            $stmt->bindParam('p_meta_keyword', $arrParams['strMetaKeyword'], PDO::PARAM_STR);
            $stmt->bindParam('p_content', $arrParams['ckeditor'], PDO::PARAM_STR);
            $stmt->bindParam('p_is_comment', $arrParams['intIsComment'], PDO::PARAM_INT);
            $stmt->bindParam('p_comment_type', $arrParams['intCommentType'], PDO::PARAM_INT);
            $stmt->bindParam('p_views', $arrParams['intViews'], PDO::PARAM_INT);
            $stmt->bindParam('p_comments', $arrParams['intComments'], PDO::PARAM_INT);
            $stmt->bindParam('p_status', $arrParams['intStatus'], PDO::PARAM_INT);
            $stmt->bindParam('p_user_id', $arrParams['intUserId'], PDO::PARAM_INT);
            $stmt->bindParam('p_user_approve_id', $arrParams['intUserApproveId'], PDO::PARAM_INT);
            $stmt->bindParam('p_ptime', $arrParams['intPublishTime'], PDO::PARAM_INT);
            $stmt->bindParam('p_article_id', $arrParams['intArticleId'], PDO::PARAM_INT);
            $stmt->bindParam('p_link_video', $arrParams['strLinkVideo'], PDO::PARAM_STR);
            $stmt->bindParam('p_type', $arrParams['intType'], PDO::PARAM_INT);
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
    public static function getDetailArticleById($id)
    {
        //Define $arrReturn
        $arrReturn = array();
        try {
            // Get DB
            $db_s = Core_Global::getDB('core', 'slave');
            $stmt = $db_s->prepare('CALL sp_be_getDetailArticles(:p_article_id)');
            //Bind param
            $stmt->bindParam('p_article_id', $id, PDO::PARAM_INT);
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
    public static function getListArticle($params)
    {
        //Define $arrReturn
        $arrReturn = $arrResult = array();
        $arrParams = array_merge(array(
            'intId' => NULL,
            'strKeyword' => NULL,
            'intFromdate' => NULL,
            'intTodate' => NULL,
            'intCategoryId' => NULL,
            'strStatus' => null,
            'intUserId' => null,
            'intLimit' => 0,
            'intOffset' => 0,
            'intPublishTime' => null,
            'intPublishTime24h' => null,
            'isTopViews' => null,
            'isTopComments' => null,
            'intPageview' => null
        ), $params);

        try {
            // Get DB
            $db_s = Core_Global::getDB('core', 'slave');
            $stmt = $db_s->prepare('CALL sp_be_getListArticles(:p_article_id, :p_title, :p_fromdate, :p_todate, :p_category_id, :p_status, :p_user_id, :p_limit, :p_offset, :p_ptime, :p_ptime24h, :p_top_views, :p_top_comments, :p_views, @p_rowcount)');
            //Bind param
            $stmt->bindParam('p_article_id', $arrParams['intId'], PDO::PARAM_INT);
            $stmt->bindParam('p_title', $arrParams['strKeyword'], PDO::PARAM_STR);
            $stmt->bindParam('p_fromdate', $arrParams['intFromdate'], PDO::PARAM_INT);
            $stmt->bindParam('p_todate', $arrParams['intTodate'], PDO::PARAM_INT);
            $stmt->bindParam('p_category_id', $arrParams['intCategoryId'], PDO::PARAM_STR);
            $stmt->bindParam('p_status', $arrParams['strStatus'], PDO::PARAM_STR);
            $stmt->bindParam('p_user_id', $arrParams['intUserId'], PDO::PARAM_INT);
            $stmt->bindParam('p_limit', $arrParams['intLimit'], PDO::PARAM_INT);
            $stmt->bindParam('p_offset', $arrParams['intOffset'], PDO::PARAM_INT);
            $stmt->bindParam('p_ptime', $arrParams['intPublishTime'], PDO::PARAM_INT);
            $stmt->bindParam('p_ptime24h', $arrParams['intPublishTime24h'], PDO::PARAM_INT);
            $stmt->bindParam('p_top_views', $arrParams['isTopViews'], PDO::PARAM_INT);
            $stmt->bindParam('p_top_comments', $arrParams['isTopComments'], PDO::PARAM_INT);
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
    public static function getListArticleByTagId($params)
    {
        try {
            $result = array();
            $params = array_merge(
                array(
                    'intTagId'=>null,
                    'intLimit' => 0,
                    'intOffset' => 0,
                    'intPublishTime' => NULL
                ), $params);
            $storage = Core_Global::getDb('core', 'slave');
            $stmt = $storage->prepare('CALL sp_be_getListArticlesByTagId(:p_tag_id,:p_limit,:p_offset,:p_ptime,@p_rowcount)');
            $stmt->bindParam('p_tag_id', $params['intTagId'], PDO::PARAM_INT);
            $stmt->bindParam('p_limit', $params['intLimit'], PDO::PARAM_INT);
            $stmt->bindParam('p_offset', $params['intOffset'], PDO::PARAM_INT);
            $stmt->bindParam('p_ptime', $params['intPublishTime'], PDO::PARAM_INT);
            // Execute
            $stmt->execute();
            $result['data'] = $stmt->fetchAll();
            $stmt->closeCursor();
            $stmt->closeCursor();
            $result['total'] = $storage->query("SELECT @p_rowcount")->fetchColumn();
            // Deallocate
            unset($stmt);
            return $result;

        } catch (Zend_Db_Exception $e) {
            Core_Global::sendLog($e);
        }
    }

    /**
     * PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function getListOtherArticle($params)
    {
        //Define $arrReturn
        $arrReturn = $arrResult = array();
        $arrParams = array_merge(array(
            'intCategoryId' => NULL,
            'strStatus' => null,
            'intLimit' => 0,
            'intOffset' => 0,
            'intPublishTime' => null,
            'intArticleId' => NULL
        ), $params);
        try {
            // Get DB
            $db_s = Core_Global::getDB('core', 'slave');
            $stmt = $db_s->prepare('CALL sp_be_getListOtherArticles(:p_category_id, :p_status, :p_limit, :p_offset, :p_ptime, :p_article_id, @p_rowcount)');
            //Bind param
            $stmt->bindParam('p_category_id', $arrParams['intCategoryId'], PDO::PARAM_INT);
            $stmt->bindParam('p_status', $arrParams['strStatus'], PDO::PARAM_STR);
            $stmt->bindParam('p_limit', $arrParams['intLimit'], PDO::PARAM_INT);
            $stmt->bindParam('p_offset', $arrParams['intOffset'], PDO::PARAM_INT);
            $stmt->bindParam('p_ptime', $arrParams['intPublishTime'], PDO::PARAM_INT);
            $stmt->bindParam('p_article_id', $arrParams['intArticleId'], PDO::PARAM_INT);
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
    public static function insertTopstory($arrParams)
    {
        $arrParams = array_merge(array(
            'intArticleId' => NULL,
            'intCategoryId' => NULL,
            'intDisplayOrder' => NULL,
            'intUserId' => NULL
        ), $arrParams);
        try {
            // insert DB
            $db_m = Core_Global::getDB('core', 'master');
            $stmt = $db_m->prepare('CALL sp_be_insertTopstory(:p_article_id, :p_category_id, :p_display_order, :p_user_id)');
            //Bind param
            $stmt->bindParam('p_article_id', $arrParams['intArticleId'], PDO::PARAM_INT);
            $stmt->bindParam('p_category_id', $arrParams['intCategoryId'], PDO::PARAM_INT);
            $stmt->bindParam('p_display_order', $arrParams['intDisplayOrder'], PDO::PARAM_INT);
            $stmt->bindParam('p_user_id', $arrParams['intUserId'], PDO::PARAM_INT);
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
     * @author PhongTX
     * @return array
     * @throws Exception
     */
    public static function getListTopstory($arrParams)
    {
        $arrParams = array_merge(array(
            'intTime' => NULL,
            'intCategoryId' => NULL
        ), $arrParams);

        //Define $arrReturn
        $arrReturn = array();
        try {
            // Get DB
            $db_s = Core_Global::getDb('core', 'slave');
            $stmt = $db_s->prepare('CALL sp_be_getListTopstory(:p_ptime,:p_category_id)');
            //Bind param
            $stmt->bindParam('p_ptime', $arrParams['intTime'], PDO::PARAM_INT);
            $stmt->bindParam('p_category_id', $arrParams['intCategoryId'], PDO::PARAM_INT);
            //exec func
            $stmt->execute();
            // Fetch result
            while ($row = $stmt->fetch()) {
                $arrReturn[$row['article_id']] = $row;
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
     * PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function deleteTopstoryById($arrParams)
    {
        $arrParams = array_merge(array(
            'intId' => NULL,
            'intCategoryId' => NULL
        ), $arrParams);

        $result = 0;
        try {
            $storage = Core_Global::getDB('core', 'master');
            $stmt = $storage->prepare('CALL sp_be_deleteTopstoryById(:p_article_id, :p_category_id)');
            $stmt->bindParam('p_article_id', $arrParams['intId'], PDO::PARAM_INT);
            $stmt->bindParam('p_category_id', $arrParams['intCategoryId'], PDO::PARAM_INT);
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
     * @param $params
     * @return mixed
     * @throws Core_Exception
     */
    public static function updateTopstoryById($arrParams)
    {
        ///Define $arrReturn
        $result = 1;
        $arrParams = array_merge(array(
            'intOrder' => NULL,
            'intArticleId' => NULL
        ), $arrParams);
        try {
            // update DB
            $db_m = Core_Global::getDB('core', 'master');
            $stmt = $db_m->prepare('CALL sp_be_updateTopstory(:p_display_order, :p_article_id)');
            //Bind param
            $stmt->bindParam('p_display_order', $arrParams['intOrder'], PDO::PARAM_INT);
            $stmt->bindParam('p_article_id', $arrParams['intArticleId'], PDO::PARAM_INT);
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
     * PhongTX
     * @param $arrParams
     * @return mixed
     */
    public static function getDetailArticleByTitle($title)
    {
        //Define $arrReturn
        $arrReturn = array();
        try {
            // Get DB
            $db_s = Core_Global::getDB('core', 'slave');
            $stmt = $db_s->prepare('CALL sp_be_getDetailArticlesByTitle(:p_title)');
            //Bind param
            $stmt->bindParam('p_title', $title, PDO::PARAM_STR);
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