<?php

/**
 * @author      :   PhongTX
 * @name        :   CommentsController
 * @copyright   :   Fpt Online
 * @todo        :   Comments Controller
 */
class Backend_CommentsController extends Zend_Controller_Action
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
        $arrStatus = array(
            STATUS_DA_DUYET => 'Đã duyệt',
            STATUS_CHO_DUYET => 'Chờ duyệt',
            STATUS_LUU_NHAP => 'Lưu nháp'
        );
        //Get page
        $intPage = max((int)$this->_request->getParam('page', 1), 1);
        //Get limit
        $intLimit = max((int)$this->_request->getParam('limit', DEFAULT_LIMIT), DEFAULT_LIMIT);
        //Get offset
        $offset = ($intPage - 1) * $intLimit;
        //Get status
        $intStatus = (int)$this->_request->getParam('status', 0);
        $fromdate = $this->_request->getParam('form_date', null);
        $todate = $this->_request->getParam('to_date', null);
        $tempFromdate = $fromdate != '' ? strtotime(preg_replace("/(\d+)\/(\d+)\/(\d+)/", "\\3-\\2-\\1", $fromdate)) : null;
        $tempTodate = $todate != '' ? strtotime(preg_replace("/(\d+)\/(\d+)\/(\d+)/", "\\3-\\2-\\1", $todate)) + 86399 : null;
        //Check status
        if ($intStatus == 0) {
            $strStatus = '1,2,3';
        } else {
            $strStatus = "'" . $intStatus . "'";
        }
        //Define arrParams
        $arrParams = array(
            'intFromdate' => $tempFromdate,
            'intTodate' => $tempTodate,
            'strStatus' => $strStatus,
            'intOffset' => ($offset > 0) ? $offset : 0,
            'intLimit' => $intLimit
        );
        //Call function getListArticle
        $arrReturn = Core_Model_Comment::getListComment($arrParams);
        $arrUsser = array();
        if (!empty($arrReturn['data'])) {
            foreach ($arrReturn['data'] as $row) {
                $userApproveDetail = Core_Model_Users::getDetailUserById($row['user_approve_id']);
                $arrUsser[$row['id']]['user_approve'] = $userApproveDetail;
            }
        }
        //Return page
        $arrReturn['intPage'] = $intPage;
        //Return limit
        $arrReturn['intLimit'] = $intLimit;

        $arrReturn['intOffset'] = $offset;

        //Assign to view
        $this->view->assign(array(
            'arrData' => $arrReturn,
            'arrStatus' => $arrStatus,
            'intStatus' => $intStatus,
            'fromdate' => $fromdate,
            'todate' => $todate,
            'arrUsser' => $arrUsser
        ));
        //Set title
        $this->view->headTitle()->append('Bình luận');
        //append script
        $this->view->headScript()->appendFile(STATIC_URL . '/be/js/comments/index.js');
    }

    /**
     * @todo - Action edit
     * @Author PhongTX
     */
    public function ajaxEditAction()
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
            if ($arrParamsRequest['strContent'] == '') {
                $arrReturn['msg'] = 'Nội dung không được để trống';
                echo Zend_Json::encode($arrReturn);
                exit;
            } else {
                $arrParams = array(
                    'strContent' => $arrParamsRequest['strContent'],
                    'intId' => $arrParamsRequest['intId']
                );
                $isUpdate = Core_Model_Comment::updateComment($arrParams);
                if ($isUpdate > 0) {
                    $arrReturn['error'] = 0;
                    $arrReturn['msg'] = 'Update comment thành công';
                }
                echo Zend_Json::encode($arrReturn);
                exit;
            }
        }
    }

    /**
     * @todo - Action ajaxGetDetailComment
     * @author PhongTX
     */
    public function ajaxGetDetailCommentAction()
    {
        //disable layout + render
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        if ($this->_request->isPost()) {
            //Get $intId
            $intId = $this->_request->getParam('intId', 0);
            //Define $arrCommentDetail
            $arrCommentDetail = array();
            if ($intId != 0) {
                $arrCommentDetail = Core_Model_Comment::getDetailCommentById($intId);
            }
            // Assign to view
            $this->view->assign(array(
                'arrCommentDetail' => $arrCommentDetail
            ));
            //Render view
            $response['html'] = $this->view->render('comments/add.phtml');
            //Return Json
            echo Zend_Json::encode($response);
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
                'intStatus' => $arrParamsRequest['intStatus'],
                'intUserApproveId' => $this->view->user['user_id'],
                'intId' => $arrParamsRequest['intId']
            );
            $isUpdate = Core_Model_Comment::updateComment($arrParams);
            if ($isUpdate) {
                if($arrParamsRequest['intStatus']==1){
                    $arrCommentDetail = Core_Model_Comment::getDetailCommentById($arrParamsRequest['intId']);
                    $arrArticleDetail = Core_Model_Articles::getDetailArticleById($arrCommentDetail['article_id']);
                    // $arrParams update article
                    $arrParams = array(
                        'intArticleId' => $arrArticleDetail['article_id'],
                        'intComments' => $arrArticleDetail['comments'] + 1
                    );
                    //Update article
                    Core_Model_Articles::updateArticle($arrParams);
                }
                $label = $arrParamsRequest['intStatus']==1?'Duyệt':'Từ chối duyệt';
                $arrReturn['error'] = 0;
                $arrReturn['msg'] = $label.' comment [' . $arrParamsRequest['strName'] . '] thành công';
            }
            //Return Json
            echo Zend_Json::encode($arrReturn);
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
                'intStatus' => 0, //Delete
                'intId' => $arrParamsRequest['intId']
            );
            $isUpdate = Core_Model_Comment::updateComment($arrParams);
            if ($isUpdate) {
                $arrReturn['error'] = 0;
                $arrReturn['msg'] = 'Xóa comment [' . $arrParamsRequest['strName'] . '] thành công';
            }
            //Return Json
            echo Zend_Json::encode($arrReturn);
            exit;
        }
    }
}

?>