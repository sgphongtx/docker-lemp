<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 21/04/15
 * Time: 17:27
 */
class Core_Validate_Banners
{
    public static function checkBanner($arrData = array())
    {
        $arrReturn = array(
            'error' => 1,
            'msg' => 'Có lỗi xảy ra'
        );
        if ($arrData['strName'] == '') {
            $arrReturn['msg'] = 'Tên vị trí quảng cáo không được để trống';
        } else {
            $arrParams = array(
                'strName' => trim(strip_tags($arrData['strName']))
            );
            $check = Core_Model_Banners::checkBanner($arrParams);
            if (!empty($check)) {
                if($check['id'] != $arrData['intId']) {
                    $arrReturn['msg'] = 'Tên vị trí quảng cáo này đã tồn tại trong hệ thống';
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

    public static function checkTextBanner($arrData = array())
    {
        $arrReturn = array(
            'error' => 1,
            'msg' => 'Có lỗi xảy ra'
        );
        if ($arrData['strTitle'] == '') {
            $arrReturn['msg'] = 'Tiêu đề không được để trống';
        } else if ($arrData['strShareUrl'] == '') {
            $arrReturn['msg'] = 'Link không được để trống';
        } else {
            $arrReturn['error'] = 0;
            $arrReturn['msg'] = 'Ok';
        }
        return $arrReturn;
    }
}

?>