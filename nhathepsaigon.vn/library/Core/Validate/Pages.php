<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 21/04/15
 * Time: 17:27
 */
class Core_Validate_Pages
{
    public static function checkData($arrData = array(), $act = 'add')
    {
        $arrReturn = array(
            'error' => 1,
            'is_post' => 1,
            'msg' => 'Có lỗi xảy ra'
        );
        /*if (!isset($arrData['strPageKey']) || $arrData['strPageKey'] == '') {
            $arrReturn['msg'] = 'Vui lòng nhập page key.';
        } else*/
        if ($act == 'add' && (!isset($arrData['strName']) || $arrData['strName'] == '')) {
            $arrReturn['msg'] = 'Vui lòng nhập name.';
        } elseif (!isset($arrData['strMetaTitle']) || $arrData['strMetaTitle'] == '') {
            $arrReturn['msg'] = 'Vui lòng nhập meta title.';
        } elseif (!isset($arrData['strMetaDesc']) || $arrData['strMetaDesc'] == '') {
            $arrReturn['msg'] = 'Vui lòng nhập meta description.';
        } elseif (!isset($arrData['strMetaKeyword']) || $arrData['strMetaKeyword'] == '') {
            $arrReturn['msg'] = 'Vui lòng nhập meta keyword.';
        } elseif ($arrData['ckeditor'] == '' && $arrData['intType'] == 1) {
            $arrReturn['msg'] = 'Vui lòng nhập nội dung bài viết.';
        } elseif (!isset($arrData['strDate']) || $arrData['strDate'] == '') {
            $arrReturn['msg'] = 'Vui lòng nhập ngày đăng.';
        } else {
            if ($act == 'add') {
                $checkPages = Core_Validate_Pages::checkDataByPageKey(trim(strip_tags(str_replace('&nbsp;', ' ', $arrData['strPageKey']))));
                if ($checkPages['error'] == 0) {
                    $arrReturn['error'] = 0;
                    $arrReturn['msg'] = 'Ok';
                } else {
                    $arrReturn['msg'] = $checkPages['msg'];
                }
            } else {
                $arrReturn['error'] = 0;
                $arrReturn['msg'] = 'Ok';
            }
        }
        return $arrReturn;
    }

    public static function checkDataByPageKey($key)
    {
        $arrReturn = array(
            'error' => 1,
            'msg' => 'Có lỗi xảy ra'
        );
        $arrPageDetail = Core_Model_Pages::getDetailPageByPageKey($key);
        if (!empty($arrPageDetail)) {
            $arrReturn['msg'] = 'Trang tĩnh này đã tồn tại trong hệ thống';
        } else {
            $arrReturn['error'] = 0;
            $arrReturn['msg'] = 'Ok';
        }
        return $arrReturn;
    }
}

?>