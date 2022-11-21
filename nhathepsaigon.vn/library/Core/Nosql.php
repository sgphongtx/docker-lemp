<?php

/**
 * @author      :   PhongTX
 * @name        :   Core_Cache
 * @version     :   201310
 * @copyright   :   Fpt company
 * @todo        :   Using to caching
 */
class Core_Nosql {

    public static function factory($adapter, array $config) {

        $adapterName = 'Core_Nosql_Adapter_';
        $adapterName .= ucwords(strtolower($adapter));
        /*
         * Create an instance of the adapter class.
         * Pass the config to the adapter class constructor.
         */
        $cacheAdapter = new $adapterName($config);
        /*
         * Verify that the object created is a descendent of the abstract adapter type.
         */
        if (!$cacheAdapter instanceof Core_Nosql_Adapter_Abstract) {
            throw new Core_Nosql_Exception("Adapter class '$cacheAdapter' does not extend Core_Nosql_Adapter_Abstract");
        }
        return $cacheAdapter;
    }

}

