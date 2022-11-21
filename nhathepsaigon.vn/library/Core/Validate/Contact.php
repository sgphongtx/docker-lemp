<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 21/04/15
 * Time: 17:27
 */
class Core_Validate_Contact
{
    public static function checkDataSendArticle($arrData = array())
    {
        $arrReturn = array(
            'error' => 1,
            'msg' => 'Có lỗi xảy ra'
        );
        if (!isset($arrData['strTitle']) || $arrData['strTitle'] == '') {
            $arrReturn['msg'] = 'Vui lòng nhập title.';
        }elseif (!isset($arrData['strDesc']) || $arrData['strDesc'] == '') {
            $arrReturn['msg'] = 'Vui lòng nhập description.';
        }elseif ($arrData['ckeditor'] == '') {
            $arrReturn['msg'] = 'Vui lòng nhập nội dung bài viết.';
        }elseif (!isset($arrData['intCate']) || $arrData['intCate'] == 0) {
            $arrReturn['msg'] = 'Vui lòng chọn danh mục.';
        }else{
            $arrReturn['error'] = 0;
            $arrReturn['msg'] = 'Ok';
        }
        return $arrReturn;
    }

    public static function checkDataAds($arrData = array())
    {
        $arrReturn = array(
            'error' => 1,
            'msg' => 'Có lỗi xảy ra'
        );
        if (!isset($arrData['strName']) || $arrData['strName'] == '') {
            $arrReturn['msg'] = 'Vui lòng nhập họ tên.';
        }elseif (!isset($arrData['strEmail']) || $arrData['strEmail'] == '') {
            $arrReturn['msg'] = 'Vui lòng nhập email.';
        }elseif (!isset($arrData['strPhone']) || $arrData['strPhone'] == '') {
            $arrReturn['msg'] = 'Vui lòng nhập số điện thoại.';
        }elseif ($arrData['ckeditor'] == '') {
            $arrReturn['msg'] = 'Vui lòng nhập nội dung.';
        }else{
            $arrReturn['error'] = 0;
            $arrReturn['msg'] = 'Ok';
        }
        return $arrReturn;
    }
}

?>