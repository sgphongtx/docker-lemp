<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 21/04/15
 * Time: 17:27
 */
class Core_Validate_Config
{
    public static function checkConfig($arrData = array())
    {
        $arrReturn = array(
            'error' => 1,
            'msg' => 'Có lỗi xảy ra'
        );
        if ($arrData['strEmail'] == '') {
            $arrReturn['msg'] = 'Email hệ thống không được để trống';
        } else if ($arrData['strLinkFb'] == '') {
            $arrReturn['msg'] = 'Link facebook fanpage không được để trống';
        } else if ($arrData['strLinkTwitter'] == '') {
            $arrReturn['msg'] = 'Link twitter không được để trống';
        } else if ($arrData['strLinkGPlus'] == '') {
            $arrReturn['msg'] = 'Link G+ không được để trống';
        } else if ($arrData['strLinkYoutube'] == '') {
            $arrReturn['msg'] = 'Link youtube không được để trống';
        } else if ($arrData['strTextBanner'] == '') {
            $arrReturn['msg'] = 'Tiêu đề box text banner không được để trống';
        } else if ($arrData['strColorTextBanner'] == '') {
            $arrReturn['msg'] = 'Màu box text banner không được để trống';
        } else {
            $varEmail = new Zend_Validate_EmailAddress();
            $varPass = new Zend_Validate_StringLength(array('min' => 6));
            if (empty($arrData['strEmail']) || !$varEmail->isValid($arrData['strEmail'])) {
                $arrReturn['msg'] = 'Email hệ thống không hợp lệ';
            } else {
                $arrReturn['error'] = 0;
                $arrReturn['msg'] = 'Ok';
            }
        }
        return $arrReturn;
    }
}

?>