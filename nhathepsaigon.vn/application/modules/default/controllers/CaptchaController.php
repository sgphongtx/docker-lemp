<?php

/**
 * @author      :   PhonTX - 09/08/2012
 * @name        :   CaptchaController
 * @version     :   09082012
 * @copyright   :   FPT Online
 * @todo        :   Capcha controller
 */
class CaptchaController extends Zend_Controller_Action
{

    /**
     * @author   : PhongTX - 10/08/2012
     * @name : showAction
     * @copyright   : FPT Online
     * @todo    : Show Action
     */
    public function showAction()
    {
        //Disable layout
        $this->_helper->layout->disableLayout(true);
        //Set no render view
        $this->_helper->viewRenderer->setNoRender(true);
        //Del all captcha images by time
        Core_Utils::cleanFileCaptchaByTime();
        //captcha new object
        $captcha          = new Zend_Captcha_Image();
        //Set lenght string captcha
        $captcha->setWordLen('4');
        //Set time out
        $captcha->setTimeout('300');
        //Set height image captcha
        $captcha->setHeight('34');
        //Set width image captcha
        $captcha->setWidth('70');
        //Set font captcha
        $captcha->setFontSize(15);
        $captcha->setDotNoiseLevel(0);
        $captcha->setLineNoiseLevel(0);
        //Set dir folder image captcha
        $captcha->setImgDir(APPLICATION_PATH . '/../public/captcha');
        //Set file font use captcha
        $captcha->setFont(APPLICATION_PATH . '/data/fonts/tahoma.ttf');
        //set image captcha url
        $captcha->setImgUrl(SITE_URL . '/captcha');
        //set image captcha alt
        $captcha->setImgAlt('Mã xác nhận gửi bài viết đăng lên ' . $this->view->arrConfig['web_name']);
        //Generate captcha
        $captcha->generate();
        //Render image captcha
        $image            = $captcha->render();
        // difine out captcha id
        $input            = '<input type="hidden" name="captchaID" id="captchaID" value ="' . $captcha->getId() . '" >';
        //Get word captcha
        $word             = $captcha->getWord();
        //set session word captcha
        $_SESSION['word'] = $word;
        //Define array response
        $arrResponse      = array('html' => $image . $input);
        //Return data json
        echo Zend_Json::encode($arrResponse);
        exit;
    }

}