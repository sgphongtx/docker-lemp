<?php

/**
 * @author      :   PhongTX
 * @name        :   UsersController
 * @copyright   :   Fpt Online
 * @todo        :   Users Controller
 */
class Backend_UsersController extends Zend_Controller_Action
{
    /**
     * @todo - Action init
     * @author PhongTX
     */
    public function init()
    {

    }

    /**
     * @todo - Action index
     * @author PhongTX
     */
    public function indexAction()
    {
        //Check permission - Get menu by menu code
        $arrMenuByMenuCode = Core_Model_Menus::getDetailMenuByMenuCode($this->view->moduleName.'_'.$this->view->controllerName.'_'.$this->view->actionName);
        if(!in_array($arrMenuByMenuCode['menu_id'],$this->view->user['permission'][PERMISSION_VIEW])){
            $this->_redirect(SITE_URL . '/backend/forbidden');
        }
        /*End check permission*/
        //Get page
        $intPage = max((int)$this->_request->getParam('page', 1), 1);
        //Get limit
        $intLimit = max((int)$this->_request->getParam('limit', DEFAULT_LIMIT), DEFAULT_LIMIT);
        //Get offset
        $offset = ($intPage - 1) * $intLimit;

        $arrParams = array(
            'strStatus' => '1,2',//1: active, 2: inactive
            'intOffset' => ($offset > 0) ? $offset : 0,
            'intLimit' => $intLimit,
        );
        $arrReturn = Core_Model_Users::getListUsers($arrParams);
        //Return page
        $arrReturn['intPage'] = $intPage;
        //Return limit
        $arrReturn['intLimit'] = $intLimit;
        //Return offset
        $arrReturn['intOffset'] = $offset;
        //Assign to view
        $this->view->assign(array(
                'arrData' => $arrReturn
            )
        );
        //Set title
        $this->view->headTitle()->append('List users');
        //append script
        $this->view->headScript()->appendFile(STATIC_URL . '/be/js/users/add.js');
    }

    /**
     * @todo - Action login
     * @author PhongTX
     */
    public function loginAction()
    {
        $this->_helper->layout->setLayout('login');
        $error = (int)$this->_request->getParam('error', 0);
        $redirect = $this->_request->getParam('redirect', null);
        $redirect = $redirect ? $redirect : SITE_URL . '/backend';
        // check user readly login
        if (isset($this->view->user['user_id']) && $this->view->user['user_id'] > 0) {
            $this->_redirect(SITE_URL . '/backend');
        }
        // set msg error
        if ($error == 1) {
            $error = 'Email hoặc mật khẩu không đúng.';
        } else if ($error == 2) {
            $error = 'Bạn không có quyền login.';
        }
        //assign
        $this->view->assign(
            array('error' => $error, 'redirect' => $redirect)
        );
        //Set title
        $this->view->headTitle()->append('Administrator');
        //append script
        $this->view->headScript()->appendFile(STATIC_URL . '/be/js/login/login.js');
    }

    /**
     * @todo - Action verify
     * @author PhongTX
     */
    public function verifyAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        if ($this->_request->isPost()) {
            $arrParams['email'] = trim($this->_request->getParam('email', null));
            $arrParams['password'] = trim($this->_request->getParam('password', null));
            $redirect = $this->_request->getParam('redirect', null);
            // valid data input
            $arrError = Core_Validate_Users::vaildLogin($arrParams);
            if (count($arrError) > 0) {
                $this->_redirect(SITE_URL . '/backend/users/login?error=1');
            }
            // md5 password
            $arrParams['password'] = md5($arrParams['password']);
            // check login
            $result = Core_Model_Users::getUserByEmail($arrParams);
            if (isset($result['user_id']) && $result['user_id'] > 0 && $result['status'] == 1 && $result['role'] == 1) {
                /**
                 * get permissions on resource
                 */
                $_SESSION['KCFINDER']['access'] = array(
                    'files' => array(
                        'delete' => true,
                        'upload' => true,
                        'copy' => true,
                        'move' => true,
                        'rename' => true
                    ),
                    'dirs' => array(
                        'create' => true,
                        'delete' => true,
                        'rename' => true
                    )
                );
                //Get permission
                //Get permission by group id
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
                // login success redirect
                $redirect = $redirect ? urldecode($redirect) : SITE_URL . '/backend';
                // redirect link
                $this->_redirect($redirect);
            }
        }
        $this->_redirect(SITE_URL . '/backend/users/login?error=1');
    }

    /**
     * @todo - Action logout
     * @Author PhongTX
     */
    public function logoutAction()
    {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        if (isset($this->view->user['user_id']) && $this->view->user['user_id'] > 0) {
            //KCFINDER
            $_SESSION['KCFINDER']['access'] = array(
                'files' => array(
                    'delete' => false,
                    'upload' => false,
                    'copy' => false,
                    'move' => false,
                    'rename' => false
                ),
                'dirs' => array(
                    'create' => false,
                    'delete' => false,
                    'rename' => false
                )
            );

            $auth = Zend_Auth::getInstance();
            //Set storage session user BE
            $auth->setStorage(new Zend_Auth_Storage_Session('user'));
            if ($auth->hasIdentity()) {
                $auth->clearIdentity();
            }
            Zend_Session::destroy();
        }
        $this->redirect(SITE_URL . '/backend/users/login');
    }

    /**
     * @todo - Action add
     * @Author PhongTX
     */
    public function ajaxAddAction()
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
            //Add user
            if ($arrParamsRequest['intUserId'] == 0) {
                $checkUser = Core_Validate_Users::checkUserInsert($arrParamsRequest);
                if ($checkUser['error'] == 1) {
                    echo Zend_Json::encode($checkUser);
                } else {
                    //format date
                    $date = DateTime::createFromFormat('d/m/Y', trim(strip_tags($arrParamsRequest['strBirthday'])));
                    $arrParamsRequest['strBirthday'] = $date->format('Y-m-d');
                    $arrParams = array(
                        'strEmail' => trim(strip_tags($arrParamsRequest['strEmail'])),
                        'intGroupId' => $arrParamsRequest['intGroupId'],
                        'strFullname' => trim(strip_tags($arrParamsRequest['strFullname'])),
                        'strPassWord' => md5(trim($arrParamsRequest['strPassWord'])),
                        'intBirthday' => strtotime(str_ireplace('/','-',$arrParamsRequest['strBirthday'])),
                        'intGender' => $arrParamsRequest['intGender'],
                        'strPhone' => trim(strip_tags($arrParamsRequest['strPhone'])),
                        'intStatus' => 1,//active
                        'intRole' => 1,//Backend
                        'strAddress' => trim(strip_tags($arrParamsRequest['strAddress']))
                    );
                    $intId = Core_Model_Users::insertUser($arrParams);
                    if ($intId > 0) {
                        $arrReturn['error'] = 0;
                        $arrReturn['msg'] = 'Add user thành công';
                    }
                    echo Zend_Json::encode($arrReturn);
                }
                //Update user
            } else {
                $checkUser = Core_Validate_Users::checkUserUpdate($arrParamsRequest);
                if ($checkUser['error'] == 1) {
                    echo Zend_Json::encode($checkUser);
                } else {
                    $arrParams = array(
                        'strEmail' => NULL,
                        'intGroupId' => $arrParamsRequest['intGroupId'],
                        'strFullname' => trim(strip_tags($arrParamsRequest['strFullname'])),
                        'strPassWord' => NULL,
                        'intBirthday' => strtotime(str_ireplace('/','-',$arrParamsRequest['strBirthday'])),
                        'intGender' => $arrParamsRequest['intGender'],
                        'strPhone' => trim(strip_tags($arrParamsRequest['strPhone'])),
                        'intStatus' => NULL,
                        'intRole' => NULL,
                        'strAddress' => trim(strip_tags($arrParamsRequest['strAddress'])),
                        'intUserId' => $arrParamsRequest['intUserId']
                    );
                    $isUpdate = Core_Model_Users::updateUser($arrParams);
                    if ($isUpdate > 0) {
                        $arrReturn['error'] = 0;
                        $arrReturn['msg'] = 'Update user thành công';
                    }
                    echo Zend_Json::encode($arrReturn);
                }
            }
            exit;
        }
    }

    /**
     * @todo - Action ajaxGetDetailUser
     * @author PhongTX
     */
    public function ajaxGetDetailUserAction()
    {
        //disable layout + render
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->_request->isPost()) {
            //Get intUserId
            $intUserId = $this->_request->getParam('intUserId', 0);
            //Define $arrUserDetail
            $arrUserDetail = array();
            if ($intUserId != 0) {
                $arrUserDetail = Core_Model_Users::getDetailUserById($intUserId);
            }
            //Get list group
            $arrParams = array(
                'strStatus' => '1,2',//1: active, 2: inactive
                'intOffset' => 0,
                'intLimit' => 1000,
            );
            $arrGroups = Core_Model_Groups::getListGroups($arrParams);
            // Assign to view
            $this->view->assign(array(
                'arrUserDetail' => $arrUserDetail,
                'arrGroups' => $arrGroups
            ));
            //Render view
            $response['html'] = $this->view->render('users/add.phtml');
            //Return Json
            echo Zend_Json::encode($response);
            exit;
        }
    }

    /**
     * @todo - Action ajaxDel
     * @author PhongTX
     */
    public function ajaxDelAction()
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
            $arrParams = array(
                'strEmail' => NULL,
                'strFullname' => NULL,
                'strPassWord' => NULL,
                'strBirthday' => NULL,
                'intGender' => NULL,
                'strPhone' => NULL,
                'intStatus' => 0, //Delete
                'strAddress' => NULL,
                'intUserId' => $arrParamsRequest['intUserId']
            );
            $isUpdate = Core_Model_Users::updateUser($arrParams);
            if ($isUpdate) {
                $arrReturn['error'] = 0;
                $arrReturn['msg'] = 'Xóa user [' . $arrParamsRequest['strName'] . '] thành công';
            }
            //Return Json
            echo Zend_Json::encode($arrReturn);
            exit;
        }
    }

    /**
     * @todo - Action ajaxUpdate
     * @author PhongTX
     */
    public function ajaxUpdateAction()
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
            $arrParams = array(
                'strEmail' => NULL,
                'strFullname' => NULL,
                'strPassWord' => NULL,
                'strBirthday' => NULL,
                'intGender' => NULL,
                'strPhone' => NULL,
                'intStatus' => $arrParamsRequest['intStatus'],
                'strAddress' => NULL,
                'intUserId' => $arrParamsRequest['intUserId']
            );
            $isUpdate = Core_Model_Users::updateUser($arrParams);
            if ($isUpdate) {
                $label = $arrParamsRequest['intStatus']==1?'Active':'Inactive';
                $arrReturn['error'] = 0;
                $arrReturn['msg'] = $label.' user [' . $arrParamsRequest['strName'] . '] thành công';
            }
            //Return Json
            echo Zend_Json::encode($arrReturn);
            exit;
        }
    }

    /**
     * @todo - Action ajaxReset
     * @author PhongTX
     */
    public function ajaxResetAction()
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
            if($arrParamsRequest['strPassWord'] != $arrParamsRequest['strConfirmPassWord'])
            {
                $arrReturn = array(
                    'error' => 1,
                    'msg' => 'New password và confirm new password không trùng nhau'
                );
            }else {
                $arrUserDetail = Core_Model_Users::getDetailUserById($arrParamsRequest['intUserId']);
                if(md5(trim($arrParamsRequest['strPassWord'])) == $arrUserDetail['password'])
                {
                    $arrReturn = array(
                        'error' => 1,
                        'msg' => 'New password và old password trùng nhau'
                    );
                }else {
                    $arrParams = array(
                        'strEmail' => NULL,
                        'strFullname' => NULL,
                        'strPassWord' => md5(trim($arrParamsRequest['strPassWord'])),
                        'strBirthday' => NULL,
                        'intGender' => NULL,
                        'strPhone' => NULL,
                        'intStatus' => NULL,
                        'strAddress' => NULL,
                        'intUserId' => $arrParamsRequest['intUserId']
                    );
                    $isUpdate = Core_Model_Users::updateUser($arrParams);
                    if ($isUpdate) {
                        $arrReturn['error'] = 0;
                        $arrReturn['msg'] = 'Reset password cho user [' . $arrParamsRequest['strName'] . '] thành công';
                    }
                }
            }
            //Return Json
            echo Zend_Json::encode($arrReturn);
            exit;
        }
    }

    /**
     * @author      :   PhongTX
     * @name    :   ajaxSearchTagsAction
     */
    public function ajaxSearchUserByEmailAction()
    {
        //disable layout + render
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        //Get key
        $strEmail = Core_Utils::upperTolower(trim(strip_tags($this->_request->getParam('q',''))));
        $arrReturn = array();
        //Define arrParams
        $arrParams = array(
            'email' => $strEmail
        );
        $arrResult = Core_Model_Users::getUserByEmail($arrParams);
        if (isset($arrResult) && !empty($arrResult)) {
            $arrReturn[] = array('id' => $arrResult['user_id'], 'text' => $arrResult['email']);
        }
        echo Zend_Json::encode($arrReturn);
        exit;
    }
}

?>