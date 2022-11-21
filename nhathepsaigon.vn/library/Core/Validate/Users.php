<?php

/**
 * Created by PhpStorm.
 * User: phongtx
 * Date: 08/04/2015
 * Time: 12:08 SA
 */
class Core_Validate_Users
{
    public static function vaildLogin($arrParams = array())
    {
        $_message = array();
        $varEmail = new Zend_Validate_EmailAddress();
        $varPass = new Zend_Validate_StringLength(array('min' => 6));
        if (empty($arrParams['email']) || !$varEmail->isValid($arrParams['email'])) {
            $_message[] = 'Email error';
        }
        if (empty($arrParams['password']) || !$varPass->isValid($arrParams['password'])) {
            $_message[] = 'Password error';
        }
        return $_message;
    }

    public static function checkUserInsert($arrData = array())
    {
        $arrReturn = array(
            'error' => 1,
            'msg' => 'Có lỗi xảy ra'
        );
        if ($arrData['strEmail'] == '') {
            $arrReturn['msg'] = 'Email không được để trống';
        } elseif (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i", $arrData['strEmail'])) {
            echo "Email không đúng định dạng";
        } elseif ($arrData['intGroupId'] == '') {
            $arrReturn['msg'] = 'Nhóm quyền không được để trống';
        } elseif ($arrData['strPassWord'] == '') {
            $arrReturn['msg'] = 'Mật khẩu không được để trống';
        } elseif ($arrData['strConfirmPassWord'] == '') {
            $arrReturn['msg'] = 'Xác nhận mật khẩu không được để trống';
        } elseif ($arrData['strPassWord'] != $arrData['strConfirmPassWord']) {
            $arrReturn['msg'] = 'Mật khẩu và xác nhận mật khẩu không trùng nhau';
        } elseif ($arrData['strFullname'] == '') {
            $arrReturn['msg'] = 'Họ tên không được để trống';
        } elseif ($arrData['strBirthday'] == '') {
            $arrReturn['msg'] = 'Ngày sinh không được để trống';
        } elseif ($arrData['strAddress'] == '') {
            $arrReturn['msg'] = 'Địa chỉ không được để trống';
        } elseif ($arrData['strPhone'] == '') {
            $arrReturn['msg'] = 'Số điện thoại không được để trống';
        } else {
            $arrParams = array(
                'strEmail' => $arrData['strEmail']
            );
            $check = Core_Model_Users::checkUserByEmail($arrParams);
            if ($check['result'] > 0) {
                $arrReturn['msg'] = 'User này đã tồn tại trong hệ thống';
            } else {
                $arrReturn['error'] = 0;
                $arrReturn['msg'] = 'Ok';
            }
        }
        return $arrReturn;
    }

    public static function checkUserUpdate($arrData = array())
    {
        $arrReturn = array(
            'error' => 1,
            'msg' => 'Có lỗi xảy ra'
        );
        /*if ($arrData['strEmail'] == '') {
            $arrReturn['msg'] = 'Email không được để trống';
        } elseif (!preg_match("/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/", $arrData['strEmail'])) {
            echo "Email không đúng định dạng";
        } else*/
        if ($arrData['strFullname'] == '') {
            $arrReturn['msg'] = 'Full name không được để trống';
        } elseif ($arrData['strBirthday'] == '') {
            $arrReturn['msg'] = 'Birthday không được để trống';
        } elseif ($arrData['strAddress'] == '') {
            $arrReturn['msg'] = 'Address không được để trống';
        } elseif ($arrData['strPhone'] == '') {
            $arrReturn['msg'] = 'Phone không được để trống';
        } elseif ($arrData['intGender'] == '') {
            $arrReturn['msg'] = 'Giới tính không được để trống';
        } else {
            $arrReturn['error'] = 0;
            $arrReturn['msg'] = 'Ok';
        }
        return $arrReturn;
    }

    public static function checkUserRegister($arrData = array())
    {
        $arrReturn = array(
            'error' => 1,
            'msg' => 'Có lỗi xảy ra'
        );
        if ($arrData['strName'] == '') {
            $arrReturn['msg'] = 'Họ tên không được để trống';
        } elseif ($arrData['strEmail'] == '') {
            $arrReturn['msg'] = 'Email không được để trống';
        } elseif (!preg_match("/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/", $arrData['strEmail'])) {
            echo "Email không đúng định dạng";
        } elseif ($arrData['intGender'] == '') {
            $arrReturn['msg'] = 'Giới tính không được để trống';
        } elseif ($arrData['strPass'] == '') {
            $arrReturn['msg'] = 'Mật khẩu không được để trống';
        } elseif ($arrData['strConfirmPass'] == '') {
            $arrReturn['msg'] = 'Xác nhận mật khẩu không được để trống';
        } elseif ($arrData['strPass'] != $arrData['strConfirmPass']) {
            $arrReturn['msg'] = 'Mật khẩu và xác nhận mật khẩu không trùng nhau';
        } else {
            $arrParams = array(
                'strEmail' => $arrData['strEmail']
            );
            $check = Core_Model_Users::checkUserByEmail($arrParams);
            if ($check['result'] > 0) {
                $arrReturn['msg'] = 'User này đã tồn tại trong hệ thống';
            } else {
                $arrReturn['error'] = 0;
                $arrReturn['msg'] = 'Ok';
            }
        }
        return $arrReturn;
    }

    public static function checkEmailUser($arrData = array())
    {
        $arrReturn = array(
            'error' => 1,
            'msg' => 'Có lỗi xảy ra'
        );
        if ($arrData['strEmail'] == '') {
            $arrReturn['msg'] = 'Email không được để trống';
        } elseif (!preg_match("/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/", $arrData['strEmail'])) {
            echo "Email không đúng định dạng";
        } else {
            $arrParams = array(
                'strEmail' => $arrData['strEmail']
            );
            $check = Core_Model_Users::checkUserByEmail($arrParams);
            if ($check['result'] > 0) {
                $arrReturn['error'] = 0;
                $arrReturn['msg'] = 'Ok';
            } else {
                $arrReturn['msg'] = 'User này không tồn tại trong hệ thống';
            }
        }
        return $arrReturn;
    }

    public static function checkUserResetPass($arrData = array())
    {
        $arrReturn = array(
            'error' => 1,
            'msg' => 'Có lỗi xảy ra'
        );
        if ($arrData['strPass'] == '') {
            $arrReturn['msg'] = 'Mật khẩu mới không được để trống';
        } elseif ($arrData['strConfirmPass'] == '') {
            $arrReturn['msg'] = 'Xác nhận mật khẩu mới không được để trống';
        } elseif ($arrData['strPass'] != $arrData['strConfirmPass']) {
            $arrReturn['msg'] = 'Mật khẩu mới và xác nhận mật khẩu mới không trùng nhau';
        } else {
            $arrReturn['error'] = 0;
            $arrReturn['msg'] = 'Ok';
        }
        return $arrReturn;
    }
}

?>