<?php
/**
 * @author: PhongTX
 */
// Production
// error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
// ini_set('display_errors', 0);
// Development
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=utf-8');
define('ROOT_PATH', realpath(dirname(__FILE__) . '/..'));
// Define application environment
define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
// Define path to application directory
define('APPLICATION_PATH', ROOT_PATH . '/application');
// Define library path
define('LIBRARY_PATH', ROOT_PATH . '/library/');
// Define library path
define('EXT', APPLICATION_ENV == 'production' ? '.min' : '');
defined('TIME_CACHE') || define('TIME_CACHE', 120);
define('KEY_CACHE_PAGE', 'CACHE_NHATHEPSAIGON_'.md5($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']));
header('Cache-Control: max-age=' . TIME_CACHE);
//$data = apcu_fetch(KEY_CACHE_PAGE);
$data = array();
if (!empty($data)) {
    $headers = array('Since' => '', 'Match' => '');
    if (!empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
        $headers['Since'] = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
    }
    if (!empty($_SERVER['HTTP_IF_NONE_MATCH'])) {
        $headers['Match'] = $_SERVER['HTTP_IF_NONE_MATCH'];
    }
    $PageNotUpdated = $headers['Since'] != '' && (abs(strtotime($headers['Since']) - $data['time']) < 60);
    $DoIDsMatch = $headers['Match'] != '' && (strpos($headers['Match'], $data['etag']) !== false);
    if ($PageNotUpdated && $DoIDsMatch) {
        header('ETag: ' . $data['etag'], true, 304);
    } else {
        header('Cache-Control: max-age=' . TIME_CACHE);
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $data['time']) . ' GMT');
        header('ETag: ' . $data['etag']);
        echo $data['content'];
    }
    exit;
}
// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    get_include_path(),
    LIBRARY_PATH
)));
//include defined configs
require_once APPLICATION_PATH . '/configs/defined-'.APPLICATION_ENV.'.php';
//include file Autoload for Zend
require_once LIBRARY_PATH . 'Zend/Loader.php';
//include file Autoload for Zend
require_once LIBRARY_PATH . 'Zend/Loader/Autoloader.php';
//include file Autoload for Fw framwork
require_once LIBRARY_PATH . 'Core/Autoloader.php';
//getInstance Autoload
$Autoloader = Zend_Loader_Autoloader::getInstance();
$Autoloader->pushAutoloader(new Core_Autoloader(LIBRARY_PATH), array('Zend_', 'ZendX_', 'Core_'));
//set default Autoload to Fpt framework
$Autoloader->setDefaultAutoloader(array(new Core_Autoloader(), 'loadClass'));
//regis name space for giaitri
$Autoloader->registerNamespace('Core_');
try {
    //get config from application
    $config = Core_Global::getConfig('application');
    // Create application, bootstrap, and run
    $application = new Zend_Application(APPLICATION_ENV, $config);
    $application->bootstrap()->run();
} catch (Exception $exception) {
    header("HTTP/1.0 404 Not Found");
    exit;
}
?>