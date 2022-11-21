<?php

/**
 * @author      :   PhongTX
 * @name        :   Core_Global
 * @copyright   :   Fpt Online
 * @todo        :   Global for Application
 */
class Core_Global
{

    /**
     * store config ini
     * @var array
     */
    private static $_configs = array();

    /**
     * store nosql instance
     * @var array
     */
    private static $_mongo = array();
    /**
     * store nosql instance
     * @var array
     */
    private static $_nosql = array();

    /**
     * store cache instance
     * @var array
     */
    private static $_cache = array();

    /**
     * store db instance
     * @var array
     */
    private static $_db = array();

    /**
     * store job instance
     * @var array
     */
    private static $_jobClient = array();

    /**
     * Search of object
     * @var object
     */
    private static $_searchObject = null;

    /**
     * call fucn close all resource: db, memcache, redis
     * @author PhongTX
     */
    public static function closeResource()
    {
        self::closeMongo();
        self::closeRedis();
        self::closeCache();
        self::closeDb();
        self::closeGearmanClient();
    }

    /**
     * close redis storage
     * @author PhongTX
     */
    public static function closeRedis()
    {
        //check not emtpy
        if (!empty(self::$_nosql)) {
            //loop redis ins
            foreach (self::$_nosql as $farm => $redis) {
                //close redis
                $redis->getStorage()->close();
            }
        }
        //reset var
        self::$_nosql = array();
    }

    /**
     * close gearman storage
     * @author PhongTX
     */
    public static function closeGearmanClient()
    {
        //reset var
        self::$_jobClient = array();
    }

    public static function closeMongo()
    {

        if (!empty(self::$_mongo)) {
            //loop db ins to close
            foreach (self::$_mongo as $mFarm => $mg) {
                $mg->close();
            }
            self::$_mongo = array();
        }

    }

    /**
     * close Db
     * @author PhongTX
     */
    public static function closeDb()
    {
        //check empty
        if (!empty(self::$_db)) {
            //loop db ins to close
            foreach (self::$_db as $dbName => $db) {
                //check connected
                if ($db->isConnected()) {
                    $db->closeConnection();
                }
            }
        }
        self::$_db = array();
    }

    /**
     * Close memcache
     * @author PhongTX
     */
    public static function closeCache()
    {
        //reset param
        self::$_cache = array();
    }

    /**
     * Get DB connection
     * @param string $strDbname
     * @param string $strType master|slave
     * @param string $zone
     * @return Zend_Db_Adapter_Abstract
     * @author PhongTX
     */
    public static function getDb($strDbname, $strType = 'master', $server = 0)
    {
        //set key by $strType and $strDbname
        $dbName = $strType . $strDbname . $server;
        //check exists Db instance and Db instanceof Zend_Db_Adapter_Abstract
        if (!array_key_exists($dbName, self::$_db) || !(($dbAdapter = self::$_db["$dbName"]) instanceof Zend_Db_Adapter_Abstract)) {
            //get config database
            $arrConf = self::getApplicationIni('db');
            //check $strDbname && $strType isset config
            if (null != ($arrConf = $arrConf["$strDbname"]["$strType"])) {
                //set Zone
                array_key_exists($server, $arrConf) && ($arrConf = $arrConf[$server]);
                //construct Zend Db
                $dbAdapter = Zend_Db::factory($arrConf['adapter'], $arrConf['params']);
                //set instance to store db instance
                self::$_db["$dbName"] = $dbAdapter;
            } else {
                //Exception $strDbname and $strType
                throw new Exception("Db name $strDbname and type $strType not exists in config database.");
            }
        }
        return $dbAdapter;
    }

    /**
     * Get Cache Instance
     * @param string $strFarmName
     * @param int $strShading
     * @param string $zone
     * @return Core_Cache_Adapter_Abstract
     * @author PhongTX
     */
    public static function getCache($strFarmName = 'detail', $strShading = NULL, $zone = ZONE_ENV)
    {
        $strInstance = $strFarmName . $zone;
        //check shadding
        if ($strShading != NULL) {
            //get config cache form Application Ini
            $arrConf = self::getApplicationIni('cache');
            //set Zone
            $arrConf = $arrConf[$zone];
            //check $strFarmName exists
            if (!array_key_exists($strFarmName, $arrConf)) {
                //Exception $strFarmName not in config cache
                throw new Exception("Farm $strFarmName not exists in config cache.");
            }
            //get adapter cache
            $adapter = $arrConf["$strFarmName"]['adapter'];
            //get servers cache
            $arrConf = $arrConf["$strFarmName"]['servers'];
            //get Id shadding
            $intShadingId = crc32($strShading) % count($arrConf);
            //gan Farm with shadding
            $strFarmName .= $intShadingId;
            //check instance strFarmName is Core_Cache_Adapter_Abstract
            if (!array_key_exists($strInstance, self::$_cache) || !(($cacheAdapter = self::$_cache["$strInstance"]) instanceof Core_Cache_Adapter_Abstract)) {
                //check $strFarmName exists
                if (!array_key_exists($intShadingId, $arrConf)) {
                    //Exception $strFarmName not in config cache
                    throw new Exception("Shading Id $intShadingId not exists in config cache.");
                }
                //get servers cache
                $arrConf = $arrConf["$intShadingId"];
                //load instance Core_Cache
                $cacheAdapter = Core_Cache::factory($adapter, $arrConf);
                //set instance
                self::$_cache["$strInstance"] = $cacheAdapter;
            }
        } else {
            //check instance strFarmName is Core_Cache_Adapter_Abstract
            if (!array_key_exists($strInstance, self::$_cache) || !(($cacheAdapter = self::$_cache["$strInstance"]) instanceof Core_Cache_Adapter_Abstract)) {
                //get config cache form Application Ini
                $arrConf = self::getApplicationIni('cache');
                //set Zone
                $arrConf = $arrConf[$zone];
                //check $strFarmName exists
                if (!array_key_exists($strFarmName, $arrConf)) {
                    //Exception $strFarmName not in config cache
                    throw new Exception("Farm $strFarmName not exists in config cache.");
                }
                //get adapter cache
                $adapter = $arrConf["$strFarmName"]['adapter'];
                //get servers cache
                $arrConf = $arrConf["$strFarmName"]['servers'];
                //load instance Core_Cache
                $cacheAdapter = Core_Cache::factory($adapter, $arrConf);
                //set instance
                self::$_cache["$strInstance"] = $cacheAdapter;
            }
        }
        return $cacheAdapter;
    }

    public static function getMongo($strFarmName, $dbName = null)
    {
        $arrConf = array();
        //check instance
        if (!array_key_exists($strFarmName, self::$_mongo) || !(self::$_mongo["$strFarmName"]['_connect'] instanceof MongoClient)) {

            $arrConf = self::getConfig('application');
            //get config mongo
            $arrConf = isset($arrConf['mongo'][$strFarmName]) ? $arrConf['mongo'][$strFarmName] : array();
            if (!isset($arrConf['db']) || !isset($arrConf['server']) || empty($arrConf['server'])) {
                self::sendLog(new Exception('Not config mongo'));
                return false;
            }
            $arrServer = array();
            foreach ($arrConf['server'] as $server) {
                if ($server['host'] != '' && $server['port'] != '') {
                    $arrServer[] = $server['host'] . ':' . $server['port'];
                }
            }
            if (empty($arrServer)) {
                self::sendLog(new Exception('Not server mongo'));
                return false;
            }
            $options = array(
                'readPreference' => 'primaryPreferred',
            );
            try {
                self::$_mongo["$strFarmName"]['_connect'] = new MongoClient("mongodb://" . implode(',', $arrServer), $options);
            } catch (Exception $ex) {
                self::sendLog(new Exception('Not connect mongo'));
                return false;
            }
        }
        $dbName = $dbName != '' ? trim($dbName) : 'null';
        if (!isset(self::$_mongo["$strFarmName"][$dbName])) {
            if (empty($arrConf)) {
                $arrConf = self::getConfig('application');
                $arrConf = $arrConf['mongo'][$strFarmName];
            }
            $db = (string)(is_array($arrConf['db'])) ? $arrConf['db'][$dbName] : $arrConf['db'];
            self::$_mongo["$strFarmName"][$dbName] = self::$_mongo["$strFarmName"]['_connect']->selectDB($db);
        }
        //return instance
        return self::$_mongo["$strFarmName"][$dbName];
    }

    /**
     * Get DB nosql connection - random port & retry connect
     * @param string $strFarmName
     * @return object
     * @author PhongTX
     */
    public static function getRedis($strFarmName = 'eclick')
    {
        try {
            //set instance
            $strInstance = $strFarmName;

            //set slot db
            $slotRedis = 0;

            //check instance
            if (!array_key_exists($strInstance, self::$_nosql) || !(self::$_nosql["$strInstance"] instanceof Core_Nosql_Adapter_Abstract)) {
                //get config redis
                $arrConf = self::getApplicationIni('nosql');
                //count server
                $arrServers = $arrConf["$strFarmName"]['servers'];
                $intServerNum = count($arrServers);
                //factory apapter
                $adapter = $arrConf["$strFarmName"]['adapter'];
                //Set server id
                $intId = array_rand($arrServers);

                while ($intServerNum > 0) {
                    /** @var $objRedis Core_Nosql_Adapter_Redis */
                    //factory redis
                    $objRedis = Core_Nosql::factory($adapter, $arrServers[$intId]);

                    //check redis connect
                    if ($objRedis->getStorage()) {
                        $objRedis->selectDb($slotRedis);
                        //set instance
                        self::$_nosql["$strInstance"] = $objRedis;
                        //break while
                        break;
                    } else {
                        $intServerNum--;
                        unset($arrServers[$intId]);
                        $intId = array_rand($arrServers);
                    }
                    //end if
                }
                //end while

                //check instance
                if (!array_key_exists($strInstance, self::$_nosql) || !(self::$_nosql["$strInstance"] instanceof Core_Nosql_Adapter_Abstract)) {
                    return new Core_Stdclass();
                }
                //end if
            }
            //end if
            //return instance
            return self::$_nosql["$strInstance"]->getStorage();
        } catch (Exception $ex) {
            return new Core_Stdclass();
        }
        return new Core_Stdclass();
    }

    /**
     * Get application application configuration
     * @param string $config_key
     * @return object
     * @author PhongTX
     */
    public static function getApplicationIni($config_key = null)
    {
        $config = self::getConfig('application');
        //check section
        if ($config_key !== NULL) {
            //check key in config
            if (array_key_exists($config_key, $config)) {
                //return data config
                return $config["$config_key"];
            }
            return null;
        }
        //return data
        return $config;
    }

    /**
     * get config to file ini
     * @param string $filename
     * @return array
     * @author PhongTX
     */
    public static function getConfig($filename)
    {
        //check instance by filename
        if (!isset(self::$_configs[$filename])) {
            $file = APPLICATION_PATH . '/' . 'configs' . '/' . $filename . '-' . APPLICATION_ENV . '.ini';

            //check extension apc
            if (extension_loaded('apc')) {
                //get cache apc and config by array
                if ((($config = apc_fetch($file)) === false) || !is_array($config)) {
                    //load file config by Zend_Config_Ini
                    $config = new Zend_Config_Ini($file);
                    //conver to array
                    $config = $config->toArray();
                    //set to cache apc
                    apc_store($file, $config, 0);
                }
            } else {
                //load file config by Zend_Config_Ini
                $config = new Zend_Config_Ini($file);
                //conver to array
                $config = $config->toArray();
            }
            //set config to instance
            self::$_configs[$filename] = $config;
        }
        //retrun config
        return self::$_configs[$filename];
    }

    /**
     * write log file to send mail
     * @param Exception $ex
     * @param int $level
     * @return null
     * @author PhongTX
     */
    public static function sendLog($ex)
    {
        if (APPLICATION_ENV == 'production') {
            echo '<pre>';print_r($ex);die;
        } else {
            $arrTrace = (array)$ex->getTrace();
            $arrTrace = isset($arrTrace[2]) ? $arrTrace[2] : $arrTrace;
            $logger = new Zend_Log(new Zend_Log_Writer_Stream(ROOT_PATH . '/logs/' . date('Ymd') . '_db.log'));
            $logger->info($ex->getMessage() . "\n" . json_encode($arrTrace));
            self::closeResource();
            throw new Exception($ex->getMessage(), 1);
        }
        //end if
    }

    /**
     * Get job client
     * @param string $strFarmName
     * @param string $zoneName
     * @return Core_Job_Client
     * @author PhongTX
     */
    public static function addTaskGearman(array $arrParam, $func, $strFarmName = 'backend', $task = 'doBackgroundTask')
    {

        try {
            $arrConf = self::getApplicationIni('job');
            if (!empty($arrConf[$strFarmName]) && isset($arrConf[$strFarmName]['function'][$func])) {
                $arrConf = $arrConf[$strFarmName];
                $strFunc = $arrConf['function'][$func];
                $objClient = Core_Job_Client::factory($arrConf['adapter'], $arrConf);
                if (method_exists($objClient, $task)) {
                    call_user_func_array(array($objClient, $task), array($strFunc, $arrParam));
                }
            }
        } catch (Exception $ex) {
            //log error
            Core_Global::sendLog($ex);
        }
    }

    /**
     * Get search instance
     * @author PhongTX
     * @return <Core_Search>
     */
    public static function getSearch()
    {
        //Get search instance
        if (self::$_searchObject == null) {
            $configs = self::getApplicationIni('search');
            //Get search instance
            self::$_searchObject = Core_Search::factory('solr', $configs['object']['solr']);
        }

        //Return caching
        return self::$_searchObject;
    }

    /**
     * VN To ASCII
     * @author PhongTX
     * @param <string> $str
     * @param <boolean> $toLower
     * @return <string> $str
     */
    public static function vnToAscii($str, $toLower = false)
    {
        //Check to lower
        if ($toLower)
        {
            $str = mb_strtolower($str, 'UTF-8');
        }
        else
        {
            $str = preg_replace('/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/', 'A', $str);
            $str = preg_replace('/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/', 'E', $str);
            $str = preg_replace('/(Ì|Í|Ị|Ỉ|Ĩ)/', 'I', $str);
            $str = preg_replace('/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/', 'O', $str);
            $str = preg_replace('/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/', 'U', $str);
            $str = preg_replace('/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/', 'Y', $str);
            $str = preg_replace('/(Đ)/', 'D', $str);
        }//end if
        //Replace
        $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
        $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
        $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
        $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
        $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
        $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
        $str = preg_replace('/(đ)/', 'd', $str);

        //Return
        return trim($str);
    }

    /**
     * Get client ip
     * @return <string>
     */
    public static function get_client_ip()
    {
        $ipAddress = '';
        if ($_SERVER['HTTP_CLIENT_IP'])
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        elseif ($_SERVER['HTTP_X_FORWARDED_FOR'])
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        elseif ($_SERVER['HTTP_X_FORWARDED'])
            $ipAddress = $_SERVER['HTTP_X_FORWARDED'];
        elseif ($_SERVER['HTTP_FORWARDED_FOR'])
            $ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
        elseif ($_SERVER['HTTP_FORWARDED'])
            $ipAddress = $_SERVER['HTTP_FORWARDED'];
        elseif ($_SERVER['REMOTE_ADDR'])
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipAddress = 'UNKNOWN';

        //return
        return $ipAddress;
    }

    /**
     * @Todo: De quy
     * @author: PhongTX
     * @param $arrData
     * @param int $parent
     * @param int $level
     * @param $result
     */
    public static function recursive($arrData, $type = 'category_id', $parent = 0, $level = 0, &$arrResult) {
        if (count($arrData) > 0) {
            foreach ($arrData as $key => $val) {
                if ($parent == $val['parent_id']) {
                    $val['level'] = $level;
                    $arrResult[$key] = $val;
                    switch($type){
                        case 'category_id':
                            $_parent = $val['category_id'];
                            break;
                        case 'menu_id':
                            $_parent = $val['menu_id'];
                            break;
                    }
                    unset($arrData[$key]);
                    Core_Global::recursive($arrData, $type, $_parent, $level + 1, $arrResult);
                }
            }
        }
    }

    /**
     * @param $arrParams
     * @author PhongTX
     */
    public static function sendTelegramSms($arrParams)
    {
        $url = "https://api.telegram.org/" . $arrParams['token'] . "/sendMessage?chat_id=" . $arrParams['group_id'];
        $url = $url . "&text=" . urlencode($arrParams['msg']);
        $ch = curl_init();
        $optArray = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true
        );
        curl_setopt_array($ch, $optArray);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

}
