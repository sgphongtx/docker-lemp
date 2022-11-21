<?php

/**
 * @author      :   PhongTX
 * @name        :   Core_Nosql_Adapter_Redis
 * @version     :   201310
 * @copyright   :   Fpt company
 * @todo        :   Using to storage (http://github.com/owlient/phpredis)
 */
class Core_Nosql_Adapter_Redis extends Core_Nosql_Adapter_Abstract
{

    protected $_config = array('timeout' => 0, 'persistent' => false);

    /**
     * Constructor
     *
     */
    public function __construct($options)
    {
        //Set options
        $this->setOptions($options);

        //Set connection options
        $connection = new Redis();

        //Check timeout to connect
        if ($this->_config['persistent'] == true) {
            $connect = $connection->pconnect($this->_config['host'], $this->_config['port'], $this->_config['timeout']);
        } else {
            $connect = $connection->connect($this->_config['host'], $this->_config['port'], $this->_config['timeout']);
        }


        //Check password
        if (!empty($this->_config['pwd'])) {
            if (!$connection->auth($this->_config['pwd'])) {
                throw new Core_Nosql_Exception('Input password of redis server is wrong.');
            }
        }
        if ($connect) {
            //Set storage
            $this->setStorage($connection);
        }

    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        //Close connection
        if ($this->storage) {
            $this->storage->close();
        }
    }

    /**
     * Select db
     * @param <int> $dbName
     */
    public function selectDb($dbName)
    {
        $dbName = (int)$dbName;
        $this->storage->select($dbName);
    }

}

