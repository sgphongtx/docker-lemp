<?php

/**
 * @author      :   PhongTX
 * @name        :   WidgetController
 * @copyright   :   Fpt Online
 * @todo        :   Widget Controller
 */
class WidgetController extends Zend_Controller_Action
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
        //disable layout
        $this->_helper->layout->disableLayout();

        $time = time();

        /*Lay tin 7 ngay qua*/
        //Define arrParams
        $arrParams = array(
            'strStatus' => '1',
            'intOffset' => 0,
            'intLimit' => LIMIT_WIDGET,
            'intPublishTime' => $time,
            'intPublishTime24' => strtotime(date('Y-m-d', $time) . ' 00:00:00') - (7 * 86400), //48h truoc
        );
        //Call function getListArticle
        $arrReturn7d = Core_Model_Articles::getListArticle($arrParams);
        /*End lay tin 7 ngay qua*/

        //********************* Process Meta: title + description + keywords Facebook*************************//
        $strMetaTitle = 'Widget - ' . $this->view->arrConfig['web_name'];
        $strMetaDesc = 'Widget - ' . $this->view->arrConfig['web_name'];
        $strMetaKeyword = 'Widget - ' . $this->view->arrConfig['web_name'];
        $strMetaImage = THUMBNAIL_SHARE_DEFAULT;
        $strMetaUrl = SITE_URL . '/widget/index';
        //Assign to view
        $this->view->assign(array(
            'arrReturn7d' => $arrReturn7d,
            'strMetaTitle' => $strMetaTitle,
            'strMetaDesc' => $strMetaDesc,
            'strMetaKeyword' => $strMetaKeyword,
            'strMetaImage' => $strMetaImage,
            'strMetaUrl' => $strMetaUrl
        ));
        $this->view->headTitle()->append($strMetaTitle);
        $this->view->headMeta()->setName('description', $strMetaDesc);
        $this->view->headMeta()->setName('keywords', $strMetaKeyword);
    }

}

?>