<?php

/**
 * @author      :   PhongTX
 * @name        :   Core_Plugin_App
 * @version     :   201108
 * @copyright   :   FPT Online
 * @todo        :   Environment plugin
 * @return      :   Core_Plugin_App
 */
class Core_Plugin_App extends Zend_Controller_Plugin_Abstract
{

    protected $_frontController;

    //Xhp mode
    //private $xhp = 0;

    /**
     * @author PhongTX
     */
    public function __construct()
    {
        $this->_frontController = Zend_Controller_Front::getInstance();
    }

    /**
     * Called before Zend_Controller_Front calls on the router
     * to evaluate the request against the registered routes.
     * @param Zend_Controller_Request_Abstract $request
     * @author PhongTX
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        //get route
        $router = $this->_frontController->getRouter();
        //get config from application.ini
        $arrConf = $this->_frontController->getParam('bootstrap')->getOptions();
        if (isset($arrConf['routes'])) {
            foreach ($arrConf['routes'] as $name => $route) {
                if (isset($route['route'])) {
                    $class = (isset($route['type'])) ? $route['type'] : 'Zend_Controller_Router_Route';
                    $defs = (isset($route['defaults'])) ? $route['defaults'] : array();
                    $map = (isset($route['map'])) ? $route['map'] : array();
                    $reverse = (isset($route['reverse'])) ? $route['reverse'] : null;
                    $route = new $class($route['route'], $defs, $map, $reverse);
                    $router->addRoute($name, $route);
                }
            }
        }
    }

    /**
     * Called after the router finishes routing the request.
     * @param Zend_Controller_Request_Abstract $request
     * @author PhongTX
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        //echo "<pre>"; print_r($request->getParams()); die;
    }

    /**
     * Called before Zend_Controller_Front enters its dispatch loop.
     * @param Zend_Controller_Request_Abstract $request
     * @author PhongTX
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        //get dispatch
        $dispatcher = $this->_frontController->getDispatcher();
        //Get module name
        $moduleName = $request->getModuleName();
        //Get controller name
        $controllerName = $request->getControllerName();
        //Get action name
        $actionName = $request->getActionName();
        //Get type
        $type = $this->_request->getParam('type',1);
        //Get status
        $status = $this->_request->getParam('status',0);
        //check can dispatchable or not
        if (!$dispatcher->isDispatchable($request)) {
            //redirect error page
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
            $redirector->gotoUrl('/404.html')->redirectAndExit();
        }
        //set module
        if ($moduleName == '') {
            $moduleName = 'default';
        }
        $layoutInstance = Zend_Layout::startMvc(
            array(
                'layout' => 'default',
                'layoutPath' => APPLICATION_PATH . '/layouts/scripts/' . $moduleName,
                'contentKey' => 'content'
            )
        );
        $view = $layoutInstance->getView();
        $view->setHelperPath(APPLICATION_PATH . '/layouts/helpers')->addScriptPath(APPLICATION_PATH . '/layouts');
        $arrParams = array(
            'intCategoryType' => 1,//1: News category, 2: Product category
            'intParentId' => NULL,
            'intStatus' => $moduleName == 'backend'?'1,2':'1',
            'intOffset' => NULL,
            'intLimit' => NULL,
            'intShowFolder' => $moduleName != 'backend'?1:NULL
        );
        //Get list category
        $arrCategories = Core_Model_Categories::getListCategory($arrParams);
        //Call recursive function
        Core_Global::recursive($arrCategories['data'], 'category_id', 0, 0, $arrResult);
        $arrCate = array();
        if(!empty($arrResult)){
            foreach($arrResult as $key => $row){
                if($row['parent_id'] == 0){
                    $arrCate[$row['category_id']] = $row;
                    foreach($arrResult as $row1){
                        if($row1['parent_id'] == $row['category_id']){
                            $arrCate[$row['category_id']]['child'][$row1['category_id']] = $row1;
                        }
                    }
                    unset($arrResult[$key]);
                }
            }
        }
        //Get detail config
        $arrConfig = Core_Model_Config::getDetailConfig(CONFIG);
        //Assign to view
        $view->assign(array(
                'moduleName' => $moduleName,
                'controllerName' => $controllerName,
                'actionName' => $actionName,
                'type' => $type,
                'status' => $status,
                'arrCategories' => $arrCate,
                'arrConfig' => $arrConfig
            )
        );
        // get auth
        Zend_Session::start();
        $auth = Zend_Auth::getInstance();
        //Set storage session user
        $auth->setStorage(new Zend_Auth_Storage_Session('user'));
        if ($auth->hasIdentity()) {
            $view->assign('user', $auth->getIdentity());
        }
        // check login
        if (!$auth->hasIdentity() && $moduleName == 'backend' && $controllerName != 'users') {
            $url = !substr_count($_SERVER['REQUEST_URI'], 'users') ? SITE_URL . '/backend/users/login?redirect=' . urlencode(SITE_URL . $_SERVER['REQUEST_URI']) : SITE_URL . '/backend';
            Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')->gotoUrl($url);
        }
    }

    /**
     * Called before an action is dispatched by the dispatcher.
     * This callback allows for proxy or filter behavior.
     * By altering the request and resetting its dispatched flag
     * via Zend_Controller_Request_Abstract::setDispatched(false),
     * the current action may be skipped and/or replaced.
     * @param Zend_Controller_Request_Abstract $request
     * @author PhongTX
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {

    }

    /**
     * Called after an action is dispatched by the dispatcher.
     * This callback allows for proxy or filter behavior.
     * By altering the request and resetting its dispatched flag
     * via Zend_Controller_Request_Abstract::setDispatched(false)),
     * a new action may be specified for dispatching.
     * @param Zend_Controller_Request_Abstract $request
     * @author PhongTX
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        $layout = Zend_Layout::getMvcInstance();
        if ($layout->isEnabled()) {
            $arrCateReturn = array();
            //Get module name
            $moduleName = $request->getModuleName();
            //Get controller name
            $controllerName = $request->getControllerName();
            //Get action name
            $actionName = $request->getActionName();
            if($moduleName == 'default' && (($controllerName == 'category' && $actionName == 'index') || ($controllerName == 'detail'  && $actionName == 'index'))){
                //get cate id
                $cateId = $request->getParam('cateid',0);
                if($cateId > 0) {
                    //Get detail cate
                    $arrDetailCate = Core_Model_Categories::getDetailCategoryById($cateId);
                    if (!empty($arrDetailCate) && $arrDetailCate['parent_id'] > 0){
                        $arrDetailParent = Core_Model_Categories::getDetailCategoryById($arrDetailCate['parent_id']);
                        if(!empty($arrDetailParent)){
                            $arrCateReturn = $arrDetailParent;
                            $arrCateReturn['child'] = $arrDetailCate;
                        }else{
                            $arrCateReturn = $arrDetailCate;
                        }
                    }else{
                        $arrCateReturn = $arrDetailCate;
                    }
                }
                //Assign to view
                $view = $layout->getView();
                //Assign to view
                $view->assign(array(
                        'arrCateReturn' => $arrCateReturn
                    )
                );
            }
            if($moduleName == 'default' && $controllerName == 'category' && $actionName == 'tag'){
                $arrTagsReturn = array();
                //get tag id
                $tagId = $request->getParam('tagid',0);
                if($tagId > 0) {
                    //Get detail tag
                    $arrDetailTag = Core_Model_Tags::getDetailTagById($tagId);
                    if (!empty($arrDetailTag)){
                        $arrTagsReturn = $arrDetailTag;
                    }
                }
                //Assign to view
                $view = $layout->getView();
                //Assign to view
                $view->assign(array(
                        'arrTagsReturn' => $arrTagsReturn
                    )
                );
            }
        }
    }

    /**
     * Called after Zend_Controller_Front exits its dispatch loop.
     * @author PhongTX
     */
    public function dispatchLoopShutdown()
    {
        /*if ($this->xhp) {
            //Get debug data xhprof
            $xhprof_data = xhprof_disable();
            $stringAppend = Fpt_Utility::getXhp($xhprof_data);
            $this->getResponse()->appendBody($stringAppend);
        }*/
    }

}
