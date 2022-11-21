<?php

/**
 * @author      :   PhongTX
 * @name        :   ForbiddenController
 * @copyright   :   Fpt Online
 * @todo        :   Forbidden Controller
 */
class Backend_ForbiddenController extends Zend_Controller_Action
{

    public function init()
    {

    }

    /**
     * @todo - Core backend home page
     * @author PhongTX
     */
    public function indexAction()
    {
        //Set title
        $this->view->headTitle()->append('NEWS EDITOR');
    }

}

?>