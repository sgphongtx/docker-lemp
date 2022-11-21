<?php

/**
 * @author      :   PhonTX - 09/08/2012
 * @name        :   UsersController
 * @version     :   09082012
 * @copyright   :   FPT Online
 * @todo        :   Capcha controller
 */
class UsersController extends Zend_Controller_Action
{

    /**
     * @author   : PhongTX - 10/08/2012
     * @name : registerAction
     * @copyright   : FPT Online
     * @todo    : register Action
     */
    public function registerAction()
    {
        //disable layout + render
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->_request->isPost()) {
            //Get params
            $arrParamsRequest = $this->_request->getParams();
            $arrReturn = array(
                'error' => 1,
                'msg' => 'Có lỗi xảy ra'
            );
            //Check email user
            $checkUser = Core_Validate_Users::checkUserRegister($arrParamsRequest);
            if ($checkUser['error'] == 1) {
                echo Zend_Json::encode($checkUser);
                exit;
            } else {
                $arrCaptcha['id'] = trim($arrParamsRequest['captchaID']);
                $arrCaptcha['input'] = trim($arrParamsRequest['strCodeRegister']);
                // Creating a Zend_Captcha_Image instance
                $captcha = new Zend_Captcha_Image();
                if (!$captcha->isValid($arrCaptcha))
                {
                    $arrReturn = array(
                        'error' => 2,
                        'msg' => 'Mã xác nhận không chính xác.'
                    );
                    echo Zend_Json::encode($arrReturn);
                    exit;
                }
                $arrParams = array(
                    'strEmail' => trim(strip_tags($arrParamsRequest['strEmail'])),
                    'intGroupId' => GROUP_ID_FONTEND,
                    'strFullname' => trim(strip_tags($arrParamsRequest['strName'])),
                    'strPassWord' => md5(trim($arrParamsRequest['strPass'])),
                    'intBirthday' => NULL,
                    'intGender' => $arrParamsRequest['intGender'],
                    'strPhone' => NULL,
                    'intStatus' => 2,//inactive
                    'intRole' => 2,//Fontend
                    'strAddress' => NULL
                );
                $intId = Core_Model_Users::insertUser($arrParams);
                if ($intId > 0) {
                    $rand = Core_Utils::genString(8, 2);
                    $string = $rand . "|" . $intId . "|" . time() . "|" . $arrParams['strEmail'];
                    $objCrypt = new Core_Crypt_Adapter_Xor(CODE_MA_HOA_CHUOI);
                    $strCodeActive = $objCrypt->encode($string);
                    Core_Model_Users::updateUser(array('strCodeActive' => $strCodeActive, 'intUserId' => $intId)); // insert activecode to Db
                    // send mail
                    $link = SITE_URL . '/users/verify-email-register?register=' . $strCodeActive;
                    //Get detail user
                    $userDetail = Core_Model_Users::getDetailUserById($intId);
                    $isSendMail = Core_Email::send(array('e_template' => 'register', 'fullname' => $arrParams['strFullname'], 'email' => $arrParams['strEmail'], 'password' => trim($arrParamsRequest['strPass']), 'link' => $link));
                    if ($isSendMail) {
                        //Send sms
                        Core_Global::sendTelegramSms(array(
                            'token' => TOKEN_TELEGRAM_SMS,
                            'group_id' => GROUP_ID_TELEGRAM,
                            'msg' => date('H:i:s m/d/Y', time()) . ": Có user [" . $userDetail['user_id'] . " - " . $userDetail['fullname'] . "] đăng ký tài khoản trên trang ' . $this->view->arrConfig['web_name'] . '."
                        ));
                        $arrReturn['error'] = 0;
                        $arrReturn['msg'] = 'Đăng ký tài khoản thành công. Email hướng dẫn kích hoạt tài khoản đã được gửi đến email của bạn. Vui lòng kiểm tra email <strong>' . $arrParams['strEmail'] . '</strong> để kích hoạt tài khoản.';
                    }else{
                        $arrReturn['msg'] = 'Hệ thống không gửi được email kích hoạt tài khoản';
                        //Send sms
                        Core_Global::sendTelegramSms(array(
                            'token' => TOKEN_TELEGRAM_SMS,
                            'group_id' => GROUP_ID_TELEGRAM,
                            'msg' => date('H:i:s m/d/Y', time()) . ": Hệ thống không gửi được email kích hoạt tài khoản cho user [" . $userDetail['user_id'] . " - " . $userDetail['fullname'] . "]."
                        ));
                    }
                }
                echo Zend_Json::encode($arrReturn);
                exit;
            }
        }
    }

    /**
     * @author   : PhongTX - 10/08/2012
     * @name : verifyEmailRegisterAction
     * @copyright   : FPT Online
     * @todo    : verifyEmailRegister Action
     */
    public function verifyEmailRegisterAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $string = $this->_request->getParam('register', null);
        $objCrypt = new Core_Crypt_Adapter_Xor(CODE_MA_HOA_CHUOI);
        if ($string != null) {
            $string = $objCrypt->decode($string);
            $arrParams = explode('|', $string);
            // Neu chuoi dung cau truc va email dung
            $isCheckEmail = Core_Model_Users::checkUserByEmail(array('strEmail'=>$arrParams[3]));
            if (count($arrParams) == 4 && $isCheckEmail['result'] > 0) {
                $result = Core_Model_Users::getUserByEmail(array('email' => $arrParams[3], 'password' => null));
                if (isset($result) && ($objCrypt->decode($result['code_active']) == $string)) {
                    // xem chuoi het han chua? Neu hop le cho phep changepass
                    if (($arrParams[2] + 2 * 24 * 3600) > time()) {
                        // update status = 1
                        Core_Model_Users::updateUser(array('intUserId' => $result['user_id'], 'intStatus' => 1, 'strCodeActive' => Core_Utils::genString(8, 2)));
                        $isSendMail = Core_Email::send(array('e_template' => 'verify-email-register', 'fullname' => $result['fullname'], 'email' => $result['email']));
                        if ($isSendMail) {
                            //Send sms
                            Core_Global::sendTelegramSms(array(
                                'token' => TOKEN_TELEGRAM_SMS,
                                'group_id' => GROUP_ID_TELEGRAM,
                                'msg' => date('H:i:s m/d/Y',time()).":User [".$result['user_id']." - ".$result['fullname']."] kích hoạt tài khoản thành công trên trang ' . $this->view->arrConfig['web_name'] . '."
                            ));
                        }else{
                            //Send sms
                            Core_Global::sendTelegramSms(array(
                                'token' => TOKEN_TELEGRAM_SMS,
                                'group_id' => GROUP_ID_TELEGRAM,
                                'msg' => date('H:i:s m/d/Y',time()).": [".$result['user_id']." - ".$result['fullname']."] hệ thống không gửi được email kích hoạt tài khoản trên trang ' . $this->view->arrConfig['web_name'] . '."
                            ));
                        }
                        // get instance
                        $auth = Zend_Auth::getInstance();
                        //Set storage session
                        $auth->setStorage(new Zend_Auth_Storage_Session('user'));
                        // auth write
                        $auth->getStorage()->write($result);
                        $this->_redirect(SITE_URL.'/kich-hoat-tai-khoan-thanh-cong');
                    } else {
                        $this->_redirect(SITE_URL.'/kich-hoat-tai-khoan-khong-thanh-cong');
                    }
                } else {
                    $this->_redirect(SITE_URL.'/kich-hoat-tai-khoan-khong-thanh-cong');
                }
            } else {
                $this->_redirect(SITE_URL.'/kich-hoat-tai-khoan-khong-thanh-cong');
            }
            // neu chuoi khong dung cau truc hoac email sai thi tu chuyen qua trang forgotpass
        } else {
            $this->_redirect(SITE_URL.'/kich-hoat-tai-khoan-khong-thanh-cong');
        }
    }

    /**
     * @author   : PhongTX - 10/08/2012
     * @name : activeByEmailSuccessAction
     * @copyright   : FPT Online
     * @todo    : ctiveByEmailSuccess Action
     */
    public function activeByEmailSuccessAction()
    {
        // Kiem tra da login chua
        if (!isset($this->view->user)) {
            $this->_redirect(SITE_URL);
        }
        $this->view->headTitle()->append('Kích hoạt tài khoản thành công');
        $this->view->headMeta()->setName('description', 'Kích hoạt tài khoản thành công');
        $this->view->headMeta()->setName('keywords', 'Kích hoạt tài khoản thành công');
    }

    /**
     * @author   : PhongTX - 10/08/2012
     * @name : activeByEmailUnsuccessAction
     * @copyright   : FPT Online
     * @todo    : activeByEmailUnsuccess Action
     */
    public function activeByEmailUnsuccessAction()
    {
        $this->view->headTitle()->append('Kích hoạt tài khoản không thành công');
        $this->view->headMeta()->setName('description', 'Kích hoạt tài khoản không thành công');
        $this->view->headMeta()->setName('keywords', 'Kích hoạt tài khoản không thành công');
    }

    /**
     * @author   : PhongTX - 10/08/2012
     * @name : forgotPassByEmailUnsuccessAction
     * @copyright   : FPT Online
     * @todo    : forgotPassByEmailUnsuccess Action
     */
    public function forgotPassByEmailUnsuccessAction()
    {
        $this->view->headTitle()->append('Lấy lại mật khẩu không thành công');
        $this->view->headMeta()->setName('description', 'Lấy lại mật khẩu không thành công');
        $this->view->headMeta()->setName('keywords', 'Lấy lại mật khẩu không thành công');
    }

    /**
     * @author   : PhongTX - 10/08/2012
     * @name : loginAction
     * @copyright   : FPT Online
     * @todo    : login Action
     */
    public function loginAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        if ($this->_request->isPost()) {
            $arrReturn = array(
                'error' => 1,
                'msg' => 'Email hoặc mật khẩu không đúng'
            );
            $arrParams = $this->_request->getParams();
            $arrParams['email'] = $arrParams['strEmail'];
            $arrParams['password'] = $arrParams['strPass'];
            $objCrypt = new Core_Crypt_Adapter_Xor(CODE_MA_HOA_CHUOI);
            // check su ton tai cua email
            if ($arrParams['password'] != '') {
                $arrParams['password'] = md5($arrParams['password']);
                $vaild = Core_Validate_Users::vaildLogin($arrParams);
                if (count($vaild) > 0) {
                    echo json_encode($arrReturn);
                    exit;
                } else {
                    $result = Core_Model_Users::getUserByEmail($arrParams);
                    if (isset($result['status'])) {
                        if ($result['status'] != 1) {
                            $arrReturn['msg'] = 'Tài khoản của bạn chưa được kích hoạt hoặc đang bị khóa';
                            echo json_encode($arrReturn);
                            return;
                        }
                        //Get permission
                        //Get permission by group id
                        $arrMenuPermission = array();
                        $arrPermission = Core_Model_Groups::getPermissionByGroupId($result['group_id']);
                        if(!empty($arrPermission)){
                            foreach($arrPermission as $row){
                                switch($row['action_id']){
                                    case PERMISSION_VIEW:
                                        $arrMenuPermission[PERMISSION_VIEW][] = $row['menu_id'];
                                        break;
                                    case PERMISSION_ADD_EDIT:
                                        $arrMenuPermission[PERMISSION_ADD_EDIT][] = $row['menu_id'];
                                        break;
                                    case PERMISSION_DEL:
                                        $arrMenuPermission[PERMISSION_DEL][] = $row['menu_id'];
                                        break;
                                }
                            }
                        }
                        $result['permission'] = $arrMenuPermission;
                        // get instance
                        $auth = Zend_Auth::getInstance();
                        //Set storage session
                        $auth->setStorage(new Zend_Auth_Storage_Session('user'));
                        // auth write
                        $auth->getStorage()->write($result);
                        $arrReturn['error'] = 0;
                        $arrReturn['msg'] = 'Đăng nhập thành công';
                    } else {
                        echo json_encode($arrReturn);
                        exit;
                    }
                }

            } else {
                $arrReturn['msg'] = 'Mật khẩu không được để trống';
                echo json_encode($arrReturn);
                exit;
            }
            echo json_encode($arrReturn);
            exit;
        }
    }

    /**
     * @author   : PhongTX - 10/08/2012
     * @name : forgotpassAction
     * @copyright   : FPT Online
     * @todo    : forgotpass Action
     */
    public function forgotpassAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        if ($this->_request->isPost()) {
            $arrReturn = array(
                'error' => 1,
                'msg' => 'Bạn đã đăng nhập'
            );
            // Quen mat khau
            if (isset($this->view->user)) {
                $arrReturn['error'] = 0;
                echo json_encode($arrReturn);
                exit;
            }
            //Get params
            $arrParamsRequest = $this->_request->getParams();
            //Check email user
            $checkUser = Core_Validate_Users::checkEmailUser($arrParamsRequest);
            if ($checkUser['error'] == 1) {
                echo Zend_Json::encode($checkUser);
                exit;
            } else {
                $arrCaptcha['id'] = trim($arrParamsRequest['captchaID']);
                $arrCaptcha['input'] = trim($arrParamsRequest['strCodeForgotPass']);
                // Creating a Zend_Captcha_Image instance
                $captcha = new Zend_Captcha_Image();
                if (!$captcha->isValid($arrCaptcha)) {
                    $arrReturn = array(
                        'error' => 2,
                        'msg' => 'Mã xác nhận không chính xác.'
                    );
                    echo Zend_Json::encode($arrReturn);
                    exit;
                }
                // Lay thong tin user theo Email
                $result = Core_Model_Users::getUserByEmail(array('email' => $arrParamsRequest['strEmail'], 'password' => null));
                // Tao keyword cho send mail
                $rand = Core_Utils::genString(8, 2);
                $string = $rand . "|" . $result['user_id'] . "|" . time() . "|" . $result['email'];
                $objCrypt = new Core_Crypt_Adapter_Xor(CODE_MA_HOA_CHUOI);
                $strCodeActive = $objCrypt->encode($string);
                Core_Model_Users::updateUser(array('strCodeActive' => $strCodeActive, 'intUserId' => $result['user_id'])); // update activecode to Db
                // send mail
                $link = SITE_URL . '/users/verify-forgot-pass?forgotpass=' . $strCodeActive;
                $isSendMail = Core_Email::send(array('e_template' => 'forgot-pass', 'fullname' => $result['fullname'], 'email' => $result['email'], 'link' => $link));
                if ($isSendMail) {
                    //Send sms
                    Core_Global::sendTelegramSms(array(
                        'token' => TOKEN_TELEGRAM_SMS,
                        'group_id' => GROUP_ID_TELEGRAM,
                        'msg' => date('H:i:s m/d/Y', time()) . ": Có user [" . $result['user_id'] . " - " . $result['fullname'] . "] lấy lại mật khẩu tài khoản trên trang ' . $this->view->arrConfig['web_name'] . '."
                    ));
                    $arrReturn['error'] = 0;
                    $arrReturn['msg'] = 'Email hướng dẫn lấy lại mật khẩu đã được gửi đến email của bạn. Vui lòng kiểm tra email <strong>'.$result['email'].'</strong> để lấy lại mật khẩu.';
                }else{
                    $arrReturn['msg'] = 'Hệ thống không gửi được email hướng dẫn lấy lại mật khẩu tài khoản';
                    //Send sms
                    Core_Global::sendTelegramSms(array(
                        'token' => TOKEN_TELEGRAM_SMS,
                        'group_id' => GROUP_ID_TELEGRAM,
                        'msg' => date('H:i:s m/d/Y', time()) . ": Hệ thống không gửi được email hướng dẫn lấy lại mật khẩu tài khoản cho user [" . $result['user_id'] . " - " . $result['fullname'] . "]."
                    ));
                }
            }
            echo json_encode($arrReturn);
            exit;
        }
    }

    /**
     * @author   : PhongTX - 10/08/2012
     * @name : verifyForgotPassAction
     * @copyright   : FPT Online
     * @todo    : verifyForgotPass Action
     */
    public function verifyForgotPassAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $string = $this->_request->getParam('forgotpass', null);
        $objCrypt = new Core_Crypt_Adapter_Xor(CODE_MA_HOA_CHUOI);
        if ($string != null) {
            $string = $objCrypt->decode($string);
            $arrParams = explode('|', $string);
            // Neu chuoi dung cau truc va email dung
            $isCheckEmail = Core_Model_Users::checkUserByEmail(array('strEmail'=>$arrParams[3]));
            if (count($arrParams) == 4 && $isCheckEmail['result'] > 0) {
                $result = Core_Model_Users::getUserByEmail(array('email' => $arrParams[3], 'password' => null));
                // chuoi co phai cuoi cung khong??
                if ($objCrypt->decode($result['code_active']) == $string) {
                    // xem chuoi het han chua? Neu hop le cho phep changepass
                    if (($arrParams[2] + 2 * 24 * 3600) > time()) {
                        Core_Model_Users::updateUser(array('intUserId' => $result['user_id'], 'strCodeActive' => Core_Utils::genString(8, 2)));
                        //Get permission
                        //Get permission by group id
                        $arrMenuPermission = array();
                        $arrPermission = Core_Model_Groups::getPermissionByGroupId($result['group_id']);
                        if(!empty($arrPermission)){
                            foreach($arrPermission as $row){
                                switch($row['action_id']){
                                    case PERMISSION_VIEW:
                                        $arrMenuPermission[PERMISSION_VIEW][] = $row['menu_id'];
                                        break;
                                    case PERMISSION_ADD_EDIT:
                                        $arrMenuPermission[PERMISSION_ADD_EDIT][] = $row['menu_id'];
                                        break;
                                    case PERMISSION_DEL:
                                        $arrMenuPermission[PERMISSION_DEL][] = $row['menu_id'];
                                        break;
                                }
                            }
                        }
                        $result['permission'] = $arrMenuPermission;
                        // get instance
                        $auth = Zend_Auth::getInstance();
                        //Set storage session
                        $auth->setStorage(new Zend_Auth_Storage_Session('user'));
                        // auth write
                        $auth->getStorage()->write($result);
                        // chuyen trang qua trang thay doi mat khau
                        $this->_redirect(SITE_URL.'/dat-lai-mat-khau');
                    } else {
                        $this->_redirect(SITE_URL.'/lay-lai-mat-khau-khong-thanh-cong');
                    }
                } else {
                    $this->_redirect(SITE_URL.'/lay-lai-mat-khau-khong-thanh-cong');
                }
            } // neu chuoi khong dung cau truc hoac email sai thi tu chuyen qua trang forgotpass
            else {
                $this->_redirect(SITE_URL.'/lay-lai-mat-khau-khong-thanh-cong');
            }
        } else {
            $this->_redirect(SITE_URL.'/lay-lai-mat-khau-khong-thanh-cong');
        }
    }

    /**
     * @author   : PhongTX - 10/08/2012
     * @name : resetPassAction
     * @copyright   : FPT Online
     * @todo    : resetPass Action
     */
    public function resetPassAction()
    {
        // Kiem tra da login chua
        if (!isset($this->view->user)) {
            $this->_redirect(SITE_URL);
        }
        $this->view->headTitle()->append('Đặt lại mật khẩu');
        $this->view->headMeta()->setName('description', 'Đặt lại mật khẩu');
        $this->view->headMeta()->setName('keywords', 'Đặt lại mật khẩu');
        //append script
        $this->view->headScript()->appendFile(STATIC_URL . '/fe/js/users/reset-pass.js');
    }

    /**
     * @author   : PhongTX - 10/08/2012
     * @name : ajaxUpdatePassAction
     * @copyright   : FPT Online
     * @todo    : ajaxUpdatePass Action
     */
    public function ajaxUpdatePassAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        if ($this->_request->isPost()) {
            $arrReturn = array(
                'error' => 1,
                'msg' => 'Có lỗi xảy ra'
            );
            //Get params
            $arrParamsRequest = $this->_request->getParams();
            ///Check pass
            $checkUser = Core_Validate_Users::checkUserResetPass($arrParamsRequest);
            if ($checkUser['error'] == 1) {
                echo Zend_Json::encode($checkUser);
                exit;
            } else {
                // update pass
                $isUpdate = Core_Model_Users::updateUser(array('intUserId' => $this->view->user['user_id'], 'strPassWord' => md5($arrParamsRequest['strPass'])));
                if($isUpdate > 0){
                    $result = Core_Model_Users::getDetailUserById($this->view->user['user_id']);
                    //Get permission
                    //Get permission by group id
                    $arrMenuPermission = array();
                    $arrPermission = Core_Model_Groups::getPermissionByGroupId($result['group_id']);
                    if(!empty($arrPermission)){
                        foreach($arrPermission as $row){
                            switch($row['action_id']){
                                case PERMISSION_VIEW:
                                    $arrMenuPermission[PERMISSION_VIEW][] = $row['menu_id'];
                                    break;
                                case PERMISSION_ADD_EDIT:
                                    $arrMenuPermission[PERMISSION_ADD_EDIT][] = $row['menu_id'];
                                    break;
                                case PERMISSION_DEL:
                                    $arrMenuPermission[PERMISSION_DEL][] = $row['menu_id'];
                                    break;
                            }
                        }
                    }
                    $result['permission'] = $arrMenuPermission;
                    // get instance
                    $auth = Zend_Auth::getInstance();
                    //Set storage session
                    $auth->setStorage(new Zend_Auth_Storage_Session('user'));
                    // auth write
                    $auth->getStorage()->write($result);
                    $arrReturn['error'] = 0;
                    $arrReturn['msg'] = 'Đặt lại mật khẩu thành công';
                }
            }
            echo Zend_Json::encode($arrReturn);
            exit;
        }
    }

    /**
     * @author   : PhongTX - 10/08/2012
     * @name : resetPassAction
     * @copyright   : FPT Online
     * @todo    : resetPass Action
     */
    public function editAction()
    {
        // Kiem tra da login chua
        if (!isset($this->view->user)) {
            $this->_redirect(SITE_URL);
        }
        //Define $arrUserDetail
        $arrUserDetail = array();
        if ($this->view->user['user_id'] != 0) {
            $arrUserDetail = Core_Model_Users::getDetailUserById($this->view->user['user_id']);
        }
        // Assign to view
        $this->view->assign(array(
            'arrUserDetail' => $arrUserDetail
        ));
        $this->view->headTitle()->append('Chỉnh sửa tài khoản');
        $this->view->headMeta()->setName('description', 'Chỉnh sửa tài khoản');
        $this->view->headMeta()->setName('keywords', 'Chỉnh sửa tài khoản');
        //append css
        $this->view->headLink()->appendStylesheet(STATIC_URL.'/library/bootstrap-3.3.6/css/bootstrap-datepicker.min.css');
        //append script
        $this->view->headScript()->appendFile(STATIC_URL . '/library/bootstrap-3.3.6/js/bootstrap-datepicker.min.js')
            ->appendFile(STATIC_URL . '/fe/js/users/edit.js');
    }

    /**
     * @todo - Action ajaxUpdate
     * @author PhongTX
     */
    public function ajaxUpdateUserAction()
    {
        //disable layout + render
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->_request->isPost()) {
            $arrReturn = array(
                'error' => 1,
                'msg' => 'Có lỗi xảy ra'
            );
            //Get params
            $arrParamsRequest = $this->_request->getParams();
            $checkUser = Core_Validate_Users::checkUserUpdate($arrParamsRequest);
            if ($checkUser['error'] == 1) {
                echo Zend_Json::encode($checkUser);
            } else {
                $arrParams = array(
                    'strEmail' => NULL,
                    'intGroupId' => NULL,
                    'strFullname' => trim(strip_tags($arrParamsRequest['strFullname'])),
                    'strPassWord' => NULL,
                    'intBirthday' => strtotime(str_ireplace('/','-',$arrParamsRequest['strBirthday'])),
                    'intGender' => $arrParamsRequest['intGender'],
                    'strPhone' => trim(strip_tags($arrParamsRequest['strPhone'])),
                    'intStatus' => NULL,
                    'intRole' => NULL,
                    'strAddress' => trim(strip_tags($arrParamsRequest['strAddress'])),
                    'intUserId' => $this->view->user['user_id']
                );
                $isUpdate = Core_Model_Users::updateUser($arrParams);
                if ($isUpdate > 0) {
                    $arrReturn['error'] = 0;
                    $arrReturn['msg'] = 'Chỉnh sửa tài khoản thành công';
                }
                echo Zend_Json::encode($arrReturn);
            }
        }
    }

    /**
     * @author   : PhongTX - 10/08/2012
     * @name : logoutAction
     * @copyright   : FPT Online
     * @todo    : logout Action
     */
    public function logoutAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        if ($this->_request->isPost()) {
            $arrReturn = array(
                'error' => 1,
                'msg' => 'Bạn đã đăng nhập'
            );
            // Kiem tra da login chua
            if (!isset($this->view->user)) {
                $arrReturn['error'] = 0;
                $arrReturn['msg'] = 'Đã thoát khỏi hệ thống';
                echo json_encode($arrReturn);
                exit;
            }
            $auth = Zend_Auth::getInstance();
            //Set storage session user FE
            $auth->setStorage(new Zend_Auth_Storage_Session('user'));
            if ($auth->hasIdentity()) {
                $auth->clearIdentity();
            }
            Zend_Session::destroy();
            $arrReturn['error'] = 0;
            $arrReturn['msg'] = 'Đã thoát khỏi hệ thống';
            echo json_encode($arrReturn);
            exit;
        }
    }

    /**
     * @author   : PhongTX - 10/08/2012
     * @name : listArticlesAction
     * @copyright   : FPT Online
     * @todo    : listArticles Action
     */
    public function listArticlesAction()
    {
        // Kiem tra da login chua
        if (!isset($this->view->user)) {
            $this->_redirect(SITE_URL);
        }
        //Define arrParams
        $arrParams = array(
            'intFromdate' => NULL,
            'intTodate' => NULL,
            'intCategoryId' => NULL,
            'strStatus' => NULL,
            'intUserId' => $this->view->user['user_id'],
            'intOffset' => 0,
            'intLimit' => 1000
        );
        //Call function getListArticle
        $arrReturn = Core_Model_Articles::getListArticle($arrParams);
        //Assign to view
        $this->view->assign(array(
            'arrData' => $arrReturn
        ));
        $this->view->headTitle()->append('Danh sách bài viết đã gửi');
        $this->view->headMeta()->setName('description', 'Danh sách bài viết đã gửi');
        $this->view->headMeta()->setName('keywords', 'Danh sách bài viết đã gửi');
    }

}