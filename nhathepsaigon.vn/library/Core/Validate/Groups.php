<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 21/04/15
 * Time: 17:27
 */
class Core_Validate_Groups
{
    public static function checkGroup($arrData = array())
    {
        $arrReturn = array(
            'error' => 1,
            'msg' => 'Có lỗi xảy ra'
        );
        if ($arrData['strGroupName'] == '') {
            $arrReturn['msg'] = 'Group name không được để trống';
        } else {
            $arrParams = array(
                'strGroupName' => trim(strip_tags($arrData['strGroupName']))
            );
            $check = Core_Model_Groups::checkGroup($arrParams);
            if (!empty($check)) {
                if($check['group_id'] != $arrData['intGroupId']) {
                    $arrReturn['msg'] = 'Group name này đã tồn tại trong hệ thống';
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