<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 21/04/15
 * Time: 17:27
 */
class Core_Validate_Articles
{
    public static function checkData($arrData = array(),$act='add')
    {
        $arrReturn = array(
            'error' => 1,
            'is_post' => 1,
            'msg' => 'Có lỗi xảy ra'
        );
        if (!isset($arrData['strTitle']) || $arrData['strTitle'] == '') {
            $arrReturn['msg'] = 'Vui lòng nhập title.';
        }elseif (!isset($arrData['strDesc']) || $arrData['strDesc'] == '') {
            $arrReturn['msg'] = 'Vui lòng nhập description.';
        }elseif (!isset($arrData['strMetaTitle']) || $arrData['strMetaTitle'] == '') {
            $arrReturn['msg'] = 'Vui lòng nhập meta title.';
        }elseif (!isset($arrData['strMetaDesc']) || $arrData['strMetaDesc'] == '') {
            $arrReturn['msg'] = 'Vui lòng nhập meta description.';
        }elseif (!isset($arrData['strMetaKeyword']) || $arrData['strMetaKeyword'] == '') {
            $arrReturn['msg'] = 'Vui lòng nhập meta keyword.';
        }elseif ($arrData['ckeditor'] == '' && $arrData['intType'] == 1) {
            $arrReturn['msg'] = 'Vui lòng nhập nội dung bài viết.';
        }elseif ($arrData['strLinkVideo'] == '' && $arrData['intType'] == 2) {
            $arrReturn['msg'] = 'Vui lòng nhập link video.';
        }elseif (!isset($arrData['intCate']) || $arrData['intCate'] == 0) {
            $arrReturn['msg'] = 'Vui lòng chọn danh mục.';
        }elseif (!isset($arrData['strDate']) || $arrData['strDate'] == '') {
            $arrReturn['msg'] = 'Vui lòng nhập ngày đăng.';
        }else{
            if($act=='add'){
                $checkArticles = Core_Validate_Articles::checkDataByTitle(trim(strip_tags(str_replace('&nbsp;', ' ', $arrData['strTitle']))));
                if($checkArticles['error']==0) {
                    $arrReturn['error'] = 0;
                    $arrReturn['msg'] = 'Ok';
                }else{
                    $arrReturn['msg'] = $checkArticles['msg'];
                }
            }else{
                $arrReturn['error'] = 0;
                $arrReturn['msg'] = 'Ok';
            }
        }
        return $arrReturn;
    }

    public static function checkDataByTitle($title)
    {
        $arrReturn = array(
            'error' => 1,
            'msg' => 'Có lỗi xảy ra'
        );
        $arrArticleDetail = Core_Model_Articles::getDetailArticleByTitle($title);
        if (!empty($arrArticleDetail)) {
            $arrReturn['msg'] = 'Bài viết này đã tồn tại trong hệ thống';
        } else {
            $arrReturn['error'] = 0;
            $arrReturn['msg'] = 'Ok';
        }
        return $arrReturn;
    }
}

?>