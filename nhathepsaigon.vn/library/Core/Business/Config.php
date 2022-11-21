<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 21/04/15
 * Time: 17:03
 */
class Core_Business_Config
{
    /**
     * @author PhongTX
     * @param $id
     * @return array
     * @throws Exception
     */
    public static function getDetailConfig($id)
    {
        $arrResult = array();
        try {
            $db = Core_Global::getDb('core', 'slave');
            $stmt = $db->prepare('CALL sp_be_getDetailConfig(:p_id);');
            $stmt->bindParam('p_id', $id, PDO::PARAM_INT);
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
    public static function updateConfig($arrParams)
    {
        ///Define $arrReturn
        $result = 1;
        $arrParams = array_merge(array(
            'intId' => NULL,
            'strWebLink' => NULL,
            'strWebName' => NULL,
            'strWebDescription' => NULL,
            'strMetaTitle' => NULL,
            'strMetaDescription' => NULL,
            'strMetaKeyword' => NULL,
            'strAddress' => NULL,
            'strPhone' => NULL,
            'strEmail' => NULL,
            'intLinkFb' => NULL,
            'strLinkTwitter' => NULL,
            'strLinkGPlus' => NULL,
            'strLinkYoutube' => NULL,
            'intTopstoryType' => NULL,
            'strTextBanner' => NULL,
            'strColorTextBanner' => NULL
        ), $arrParams);
        try {
            // update DB
            $db_m = Core_Global::getDB('core', 'master');
            $stmt = $db_m->prepare('CALL sp_be_updateConfig(:p_web_link, :p_web_name, :p_web_description, :p_meta_title, :p_meta_description, :p_meta_keyword, :p_address, :p_phone, :p_email, :p_link_fb, :p_link_twitter, :p_link_gplus, :p_link_youtube, :p_topstory_type, :p_text_banner, :p_color_text_banner, :p_id)');
            //Bind param
            $stmt->bindParam('p_web_link', $arrParams['strWebLink'], PDO::PARAM_STR);
            $stmt->bindParam('p_web_name', $arrParams['strWebName'], PDO::PARAM_STR);
            $stmt->bindParam('p_web_description', $arrParams['strWebDescription'], PDO::PARAM_STR);
            $stmt->bindParam('p_meta_title', $arrParams['strMetaTitle'], PDO::PARAM_STR);
            $stmt->bindParam('p_meta_description', $arrParams['strMetaDescription'], PDO::PARAM_STR);
            $stmt->bindParam('p_meta_keyword', $arrParams['strMetaKeyword'], PDO::PARAM_STR);
            $stmt->bindParam('p_address', $arrParams['strAddress'], PDO::PARAM_STR);
            $stmt->bindParam('p_phone', $arrParams['strPhone'], PDO::PARAM_STR);
            $stmt->bindParam('p_email', $arrParams['strEmail'], PDO::PARAM_STR);
            $stmt->bindParam('p_link_fb', $arrParams['intLinkFb'], PDO::PARAM_STR);
            $stmt->bindParam('p_link_twitter', $arrParams['strLinkTwitter'], PDO::PARAM_STR);
            $stmt->bindParam('p_link_gplus', $arrParams['strLinkGPlus'], PDO::PARAM_STR);
            $stmt->bindParam('p_link_youtube', $arrParams['strLinkYoutube'], PDO::PARAM_STR);
            $stmt->bindParam('p_topstory_type', $arrParams['intTopstoryType'], PDO::PARAM_INT);
            $stmt->bindParam('p_text_banner', $arrParams['strTextBanner'], PDO::PARAM_STR);
            $stmt->bindParam('p_color_text_banner', $arrParams['strColorTextBanner'], PDO::PARAM_STR);
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