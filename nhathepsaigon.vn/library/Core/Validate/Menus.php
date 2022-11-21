<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 21/04/15
 * Time: 17:27
 */
class Core_Validate_Menus
{
    public static function checkMenu($arrData = array())
    {
        $arrReturn = array(
            'error' => 1,
            'msg' => 'Có lỗi xảy ra'
        );
        if ($arrData['strMenuName'] == '') {
            $arrReturn['msg'] = 'Tên menu không được để trống';
        } else if ($arrData['strMenuCode'] == '') {
            $arrReturn['msg'] = 'Menu code không được để trống';
        } else if ($arrData['strMenuUrl'] == '') {
            $arrReturn['msg'] = 'Url không được để trống';
        } else if ($arrData['intDisplayOrder'] == '') {
            $arrReturn['msg'] = 'Display order không được để trống';
        } else {
            $arrParams = array(
                'intMenuId' => $arrData['intMenuId'],
                'strMenuCode' => trim(strip_tags($arrData['strMenuCode']))
            );
            $check = Core_Model_Menus::checkMenu($arrParams);
            if (!empty($check)) {
                if($check['menu_id'] != $arrData['intMenuId']) {
                    $arrReturn['msg'] = 'Menu code này đã tồn tại trong hệ thống';
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