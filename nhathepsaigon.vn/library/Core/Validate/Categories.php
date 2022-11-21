<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 21/04/15
 * Time: 17:27
 */
class Core_Validate_Categories
{
    public static function checkCategory($arrData = array())
    {
        $arrReturn = array(
            'error' => 1,
            'msg' => 'Có lỗi xảy ra'
        );
        if ($arrData['strCateName'] == '') {
            $arrReturn['msg'] = 'Name không được để trống';
        } else if ($arrData['strCateLink'] == '') {
            $arrReturn['msg'] = 'Link không được để trống';
        } else if ($arrData['strMetaTitle'] == '') {
            $arrReturn['msg'] = 'Meta title không được để trống';
        } else if ($arrData['strMetaDesc'] == '') {
            $arrReturn['msg'] = 'Meta description không được để trống';
        } else if ($arrData['strMetaKeyword'] == '') {
            $arrReturn['msg'] = 'Meta keyword không được để trống';
        } else {
            $arrParams = array(
                'intCategoryType' => $arrData['intCategoryType'],
                'intParentId' => $arrData['intParentId'],
                'strCateName' => trim(strip_tags($arrData['strCateName']))
            );
            $check = Core_Model_Categories::checkCategory($arrParams);
            if (!empty($check)) {
                if($check['category_id'] != $arrData['intCategoryId']) {
                    $arrReturn['msg'] = 'Name này đã tồn tại trong hệ thống';
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