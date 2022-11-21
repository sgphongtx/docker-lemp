<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 21/04/15
 * Time: 17:27
 */
class Core_Validate_Positions
{
    public static function checkPosition($arrData = array())
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
            $check = Core_Model_Positions::checkPosition($arrParams);
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
}

?>