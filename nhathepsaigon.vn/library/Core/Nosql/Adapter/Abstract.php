<?php

/**
 * @author      :   PhongTX
 * @name        :   Core_Nosql_Adapter_Abstract
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to storage
 */
abstract class Core_Nosql_Adapter_Abstract {

    /**
     * Connection instance
     * @var Core_Connection
     */
    public $storage = null;

    /**
     * Set Connection
     * @param Core_Connection $logger
     */
    protected function setStorage($storage) {
        $this->storage = $storage;
    }

    /**
     * Get options child
     * @return <array>
     */
    protected function getOptions() {
        return $this->_config;
    }

    /**
     * Set options child
     * @param <array> $options
     * @return <array>
     */
    protected function setOptions($options) {
        $this->_checkRequiredOptions($options);
        return $this->_config = array_merge($this->_config, $options);
    }

    /**
     * Get storage instance
     * @return <redis>
     */
    public function getStorage() {
        return $this->storage;
    }

    /**
     * Check for config options that are mandatory.
     * Throw exceptions if any are missing.
     *
     * @param array $config
     * @throws Core_Nosql_Exception
     */
    protected function _checkRequiredOptions(array $config) {
        // we need at least a dbname
        if (!array_key_exists('host', $config)) {
            /**
             * @see Core_Nosql_Exception
             */
            throw new Core_Nosql_Exception("Configuration array must have a key for 'host' for connect server nosql");
        }

        if (!array_key_exists('port', $config)) {
            /**
             * @see Core_Nosql_Exception
             */
            throw new Zend_Db_Adapter_Exception("Configuration array must have a key for 'port' for connect server nosql");
        }
    }

}

