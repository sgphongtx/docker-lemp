<?php

/**
 * @author      :   PhongTX
 * @name        :   ApiController
 * @copyright   :   Fpt Online
 * @todo        :   Api Controller
 */
class ApiController extends Zend_Controller_Action
{

    public function init()
    {

    }

    /**
     * @todo - Core home page
     * @author PhongTX
     */
    public function indexAction()
    {
        //disable layout + render
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
		$secretcode = $this->_request->getParam('secretcode',null);
		if($secretcode == '16d67d0243508c07157ea35d0a0c89db'){
			//Get limit
			$intLimit = $this->_request->getParam('limit', 0);
			$time = time();

			/*Lay tin 7 ngay qua*/
			//Define arrParams
			$arrParams = array(
				'strStatus' => '1',
				'intOffset' => 0,
				'intLimit' => $intLimit,
				'intPublishTime' => $time
				//'intPublishTime24' => strtotime(date('Y-m-d', $time) . ' 00:00:00') - (7 * 86400), //48h truoc
			);
			//Call function getListArticle
			$arrReturn7d = Core_Model_Articles::getListArticle($arrParams);
			/*End lay tin 7 ngay qua*/
		}else{
			$arrReturn7d = array('data' => array(),'total' => 0);
		}
        echo Zend_Json::encode($arrReturn7d);
        exit;
    }

    /**
     * @todo - Core home page
     * @author PhongTX
     */
    public function cateAction()
    {
        //disable layout + render
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $secretcode = $this->_request->getParam('secretcode',null);
        if($secretcode == '16d67d0243508c07157ea35d0a0c89db'){
            //Get limit
            $intLimit = $this->_request->getParam('limit', 0);
            $intCategoryId = $this->_request->getParam('cate', 0);
            $time = time();

            /*Lay tin moi nhat theo cate*/
            //Define arrParams
            $arrParams = array(
                'intCategoryId' => $intCategoryId,
                'strStatus' => '1',
                'intOffset' => 0,
                'intLimit' => $intLimit,
                'intPublishTime' => $time
            );
            //Call function getListArticle
            $arrReturn7d = Core_Model_Articles::getListArticle($arrParams);
            /*Lay tin moi nhat theo cate*/
        }else{
            $arrReturn7d = array('data' => array(),'total' => 0);
        }
        echo Zend_Json::encode($arrReturn7d);
        exit;
    }

}

?>