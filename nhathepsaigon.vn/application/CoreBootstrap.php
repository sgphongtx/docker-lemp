<?php

/**
 * @author      :   PhongTX
 * @name        :   CoreBootstrap
 * @copyright   :   Fpt Online
 * @todo        :   Bootstrap Controller
 */
class CoreBootstrap extends Zend_Application_Bootstrap_BootstrapAbstract
{

    public function run()
    {
        try
        {
            $front = $this->getResource('FrontController');
            $front->throwExceptions(true);
            $front->addModuleDirectory(APPLICATION_PATH . '/modules');
            $front->setDefaultModule('default');
            $front->registerPlugin(new Core_Plugin_App());
            $default = $front->getDefaultModule();
            if (null === $front->getControllerDirectory($default))
            {
                throw new Zend_Application_Bootstrap_Exception(
                'No default controller directory registered with front controller'
                );
            }
            $front->setParam('bootstrap', $this)->setParam('noErrorHandler', true)->returnResponse(true);
            $response = $front->dispatch();

            $body = $response->getBody();
            $Time = time();
            $ETag = md5($body);
            header('Cache-Control: max-age='.TIME_CACHE);
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $Time) . ' GMT');
            header('ETag: ' . $ETag);
            //store to apc cache
            /*if (preg_match('/^CACHE_NHATHEPSAIGON_/', KEY_CACHE_PAGE))
            {
                apcu_store(KEY_CACHE_PAGE, array('time' => $Time, 'etag' => $ETag, 'content' => $body));
            }
            else
            {
                apcu_store(KEY_CACHE_PAGE, array('time' => $Time, 'etag' => $ETag, 'content' => $body), 900);
            }*/

            $response->sendResponse();
        }
        catch (Exception $ex)
        {
            header("HTTP/1.0 404 Not Found");
            exit;
        }
    }

}
