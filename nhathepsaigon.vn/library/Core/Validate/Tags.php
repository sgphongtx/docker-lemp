<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 21/04/15
 * Time: 17:27
 */
class Core_Validate_Tags
{
    public static function checkTag($arrData = array())
    {
        $arrReturn = array(
            'error' => 1,
            'msg' => 'Có lỗi xảy ra'
        );
        if ($arrData['strTagName'] == '') {
            $arrReturn['msg'] = 'Tag name không được để trống';
        } else if ($arrData['strMetaTitle'] == '') {
            $arrReturn['msg'] = 'Meta title không được để trống';
        } else if ($arrData['strMetaDesc'] == '') {
            $arrReturn['msg'] = 'Meta description không được để trống';
        } else if ($arrData['strMetaKeyword'] == '') {
            $arrReturn['msg'] = 'Meta keyword không được để trống';
        } else {
            $arrParams = array(
                'strTagNameEn' => Core_Utils::vn_str_filter(Core_Utils::upperTolower(trim(strip_tags($arrData['strTagName']))))
            );
            $check = Core_Model_Tags::checkTag($arrParams);
            if (!empty($check)) {
                if($check['tag_id'] != $arrData['intTagId']) {
                    $arrReturn['msg'] = 'Tag name này đã tồn tại trong hệ thống';
                }else{
                    $arrReturn['error'] = 0;
                    $arrReturn['msg'] = 'Ok';
                }
            } else {
                $arrReturn['error'] = 0;
                $arrReturn['msg'] = 'Ok';
            }
        }
        return $arrReturn;
    }
}

?>