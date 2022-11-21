<?php
/**
 * @author      :   PhongTX
 * @name        :   Core_Nosql_Adapter_Cassandra
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to storage
 */
class Core_Nosql_Adapter_Cassandra extends Core_Nosql_Adapter_Abstract
{
    //default max # of rows for get_range()
    const DEFAULT_ROW_LIMIT = 100;
    const DEFAULT_COLUMN_TYPE = "UTF8String";
    const DEFAULT_SUBCOLUMN_TYPE = null;
    protected $_config = array();
    /**
     * Core_Nosql_Input_CassandraCF
     * @var <Core_Nosql_Input_CassandraCF>
     */
    private $cassandraCF = null;

    /**
     * Constructor
     *
     */
    public function __construct($options)
    {
        //Set options
        $this->setOptions($options);

        //Set connection options
        $connection_options = array(
            'adapter'   =>  'thrift',
            'thrift'    =>  array(
                'host'      =>  $this->_config['host'],
                'port'      =>  $this->_config['port'],
                'callback'  =>  'CassandraClient',
                'package'   =>  'Cassandra'
            )
        );
        $connection = Core_Connection::getInstance($connection_options);

        //Set storage
        $this->setStorage($connection->getInstance());
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        //Set storage
        $this->setStorage(null);
        $this->cassandraCF = null;
    }

    /**
     * Get time
     * @return <string>
     */
    private function getTime()
    {
        $time1 = microtime();
        //needs converted to string, otherwise will omit trailing zeroes
        settype($time1, 'string');
        $time2 = explode(" ", $time1);
        $time2[0] = preg_replace('/0./', '', $time2[0], 1);
        $time3 = ($time2[1].$time2[0])/100;
        return $time3;
    }

    /**
     * Parse column name
     * @param <string> $column_name
     * @param <boolean> $is_column
     * @return <string>
     */
    private function parseColumnName($column_name, $is_column=true)
    {
        if(!$this->cassandraCF->parse_columns)
        {
            return $column_name;
        }

        if(!$column_name)
        {
            return null;
        }

        $type = $is_column ? $this->cassandraCF->column_type : $this->cassandraCF->subcolumn_type;
        if($type == "LexicalUUIDType" || $type == "TimeUUIDType")
        {
            return Core_Guuid::convert($column_name, Core_Guuid::FMT_BINARY, Core_Guuid::FMT_STRING);
        }
        else if($type == "LongType")
        {
            // FIXME: currently only supports 32 bit unsigned
            $tmp = unpack("N", $column_name);
            return $tmp[1];
        }
        return $column_name;
    }

    /**
     * Unparse column name
     * @param <string> $column_name
     * @param <boolean> $is_column
     * @return <string>
     */
    private function unparseColumnName($column_name, $is_column=true)
    {
        if(!$this->cassandraCF->parse_columns)
        {
            return $column_name;
        }

        if(!$column_name)
        {
            return null;
        }

        $type = $is_column ? $this->cassandraCF->column_type : $this->cassandraCF->subcolumn_type;
        if($type == "LexicalUUIDType" || $type == "TimeUUIDType")
        {
            return Core_Guuid::convert($column_name, Core_Guuid::FMT_STRING, Core_Guuid::FMT_BINARY);
        }
        else if($type == "LongType")
        {
            // FIXME: currently only supports 32 bit unsigned
            return pack("NN", $column_name, 0);
        }
        return $column_name;
    }

    /**
     * Convert keyslice to array
     * @param <object> $keyslices
     * @return <array>
     */
    private function keySlicesToArray($keyslices)
    {
        $ret = null;
        foreach($keyslices as $keyslice)
        {
            $key     = $keyslice->key;
            $columns = $keyslice->columns;
            $ret[$key] = $this->supercolumnsOrColumnsToArray($columns);
        }
        return $ret;
    }

    /**
     * Convert supercolumn or columns to array
     * @param <object> $array_of_c_or_sc
     * @return <array>
     */
    private function supercolumnsOrColumnsToArray($array_of_c_or_sc)
    {
        $ret = null;
        foreach($array_of_c_or_sc as $c_or_sc)
        {
            if($c_or_sc->column)
            {
                //// normal columns
                $name  = $this->parseColumnName($c_or_sc->column->name, true);
                $value = $c_or_sc->column->value;
                $ret[$name] = $value;
            }
            else if($c_or_sc->super_column)
            {
                //// super columns
                $name    = $this->parseColumnName($c_or_sc->super_column->name, true);
                $columns = $c_or_sc->super_column->columns;
                $ret[$name] = $this->columnsToArray($columns);
            }
        }
        return $ret;
    }

    /**
     * Convert columns to array
     * @param <object> $array_of_c
     * @return <array>
     */
    private function columnsToArray($array_of_c)
    {
        $ret = null;
        foreach($array_of_c as $c)
        {
            $name  = $this->parseColumnName($c->name, false);
            $value = $c->value;
            $ret[$name] = $value;
        }
        return $ret;
    }

    /**
     * Convert array to supercolumns or columns
     * @param <array> $array
     * @param <int> $timestamp
     * @return <object>
     */
    private function arrayToSupercolumnsOrColumns($array, $timestamp=null)
    {
        //Get timestamp
        if(empty($timestamp))
        {
            $timestamp = $this->getTime();
        }

        //Add columns
        $ret = null;
        foreach($array as $name => $value)
        {
            $c_or_sc = new cassandra_ColumnOrSuperColumn();
            if(is_array($value))
            {
                $c_or_sc->super_column = new cassandra_SuperColumn();
                $c_or_sc->super_column->name = $this->unparseColumnName($name, true);
                $c_or_sc->super_column->columns = $this->arrayToColumns($value, $timestamp);
                $c_or_sc->super_column->timestamp = $timestamp;
            }
            else
            {
                $c_or_sc = new cassandra_ColumnOrSuperColumn();
                $c_or_sc->column = new cassandra_Column();
                $c_or_sc->column->name = $this->unparseColumnName($name, true);
                $c_or_sc->column->value = $this->columnToValue($value);;
                $c_or_sc->column->timestamp = $timestamp;
            }
            $ret[] = $c_or_sc;
        }
        return $ret;
    }

    /**
     * Convert array to columns
     * @param <array> $array
     * @param <int> $timestamp
     * @return <object>
     */
    private function arrayToColumns($array, $timestamp=null)
    {
        //Get timestamp
        if(empty($timestamp))
        {
            $timestamp = $this->getTime();
        }

        //Add columns
        $ret = null;
        foreach($array as $name => $value)
        {
            $column = new cassandra_Column();
            $column->name = $this->unparseColumnName($name, false);
            $column->value = $this->columnToValue($value);
            $column->timestamp = $timestamp;
            $ret[] = $column;
        }
        return $ret;
    }

    /**
     * Convert columns to value
     * @param <object> $thing
     * @return <object>
     */
    private function columnToValue($thing)
    {
        if($thing == null)
        {
            return "";
        }
        return $thing;
    }

    /**
     * Set Core_Nosql_Adapter_Cassandra
     * @param <string> $keyspace
     * @param <string> $column_family
     * @param <boolean> $is_super
     * @param <int> $column_type
     * @param <int> $subcolumn_type
     * @param <int> $read_consistency_level
     * @param <int> $write_consistency_level
     */
    public function setCassandraCF($keyspace, $column_family, $is_super=false, $column_type=self::DEFAULT_COLUMN_TYPE, $subcolumn_type=self::DEFAULT_SUBCOLUMN_TYPE, $read_consistency_level=cassandra_ConsistencyLevel::ONE, $write_consistency_level=cassandra_ConsistencyLevel::ZERO)
    {
        $cassandraCF = new Core_Nosql_Input_CassandraCF($keyspace, $column_family, $is_super, $column_type, $subcolumn_type, $read_consistency_level, $write_consistency_level);
        $this->cassandraCF = $cassandraCF;
    }

    /**
     * Get column
     * @param <string> $key
     * @param <string> $column
     * @param <object> $super_column
     * @return <array>
     */
    public function get($key, $column, $super_column=null)
    {
        //Add column family
        $columnPath = new cassandra_ColumnPath();
        $columnPath->column_family = $this->cassandraCF->column_family;
        $columnPath->super_column = $super_column;
        $columnPath->column = $column;

        //Query columns
        $resp = $this->storage->get($this->cassandraCF->keyspace, $key, $columnPath, $this->cassandraCF->read_consistency_level);
        return $this->supercolumnsOrColumnsToArray($resp);
    }

    /**
     * Get multi keys in a column
     * @param <array> $keys
     * @param <string> $column
     * @param <object> $super_column
     * @return <array>
     */
    public function getMulti($keys, $column, $super_column=null)
    {
        //Add column family
        $columnPath = new cassandra_ColumnPath();
        $columnPath->column_family = $this->cassandraCF->column_family;
        $columnPath->super_column = $super_column;
        $columnPath->column = $column;

        //Query columns
        $resp = $this->storage->multiget($this->cassandraCF->keyspace, $keys, $columnPath, $this->cassandraCF->read_consistency_level);
        return $this->supercolumnsOrColumnsToArray($resp);
    }

    /**
     * Get slice column
     * @param <string> $key
     * @param <string> $super_column
     * @param <int> $slice_start
     * @param <int> $slice_finish
     * @param <boolean> $column_reversed
     * @param <int> $column_count
     * @return <array>
     */
    public function getSlice($key, $super_column=null, $slice_start="", $slice_finish="", $column_reversed=false, $column_count=100)
    {
        //Add column family
        $column_parent = new cassandra_ColumnParent();
        $column_parent->column_family = $this->cassandraCF->column_family;
        $column_parent->super_column = $this->unparseColumnName($super_column, false);

        //Get slice range
        $slice_range = new cassandra_SliceRange();
        $slice_range->count = $column_count;
        $slice_range->reversed = $column_reversed;
        $slice_range->start =  $slice_start?$this->unparseColumnName($slice_start,true):"";
        $slice_range->finish = $slice_finish?$this->unparseColumnName($slice_finish,true):"";
        $predicate = new cassandra_SlicePredicate();
        $predicate->slice_range = $slice_range;

        //Query slice
        $resp = $this->storage->get_slice($this->cassandraCF->keyspace, $key, $column_parent, $predicate, $this->cassandraCF->read_consistency_level);
        return $this->supercolumnsOrColumnsToArray($resp);
    }

    /**
     * Get multi slice columns
     * @param <string> $keys
     * @param <string> $slice_start
     * @param <string> $slice_finish
     * @return <array>
     */
    public function getMultiSlice($keys, $slice_start="", $slice_finish="")
    {
        //Add column family
        $column_parent = new cassandra_ColumnParent();
        $column_parent->column_family = $this->cassandraCF->column_family;
        $column_parent->super_column = null;

        //Get slice range
        $slice_range = new cassandra_SliceRange();
        $slice_range->start =  $slice_start?$this->unparseColumnName($slice_start,true):"";
        $slice_range->finish = $slice_finish?$this->unparseColumnName($slice_finish,true):"";
        $predicate = new cassandra_SlicePredicate();
        $predicate->slice_range = $slice_range;

        //Query slice
        $resp = $this->storage->multiget_slice($this->cassandraCF->keyspace, $keys, $column_parent, $predicate, $this->cassandraCF->read_consistency_level);

        //Convert data
        $ret = null;
        foreach($resp as $name => $value)
        {
            $ret[$name] = $this->supercolumnsOrColumnsToArray($value);
        }
        return $ret;
    }

    /**
     * Get count of column_parent
     * @param <string> $key
     * @param <string> $super_column
     * @return <array>
     */
    public function getCount($key, $super_column=null)
    {
        //Add column family
        $column_path = new cassandra_ColumnPath();
        $column_path->column_family = $this->cassandraCF->column_family;
        $column_path->super_column = $super_column;

        //Query slice
        $resp = $this->storage->get_count($this->cassandraCF->keyspace, $key, $column_path, $this->cassandraCF->read_consistency_level);
        return $resp;
    }

    /**
     * Get slice range
     * @param <string> $start_key
     * @param <string> $finish_key
     * @param <int> $row_count
     * @param <string> $slice_start
     * @param <string> $slice_finish
     * @return <array>
     */
    public function getRangeSlice($start_key="", $finish_key="", $row_count=self::DEFAULT_ROW_LIMIT, $slice_start="", $slice_finish="")
    {
        //Add column family
        $column_parent = new cassandra_ColumnParent();
        $column_parent->column_family = $this->cassandraCF->column_family;
        $column_parent->super_column = null;

        //Get slice range
        $slice_range = new cassandra_SliceRange();
        $slice_range->start =  $slice_start?$this->unparseColumnName($slice_start,true):"";
        $slice_range->finish = $slice_finish?$this->unparseColumnName($slice_finish,true):"";
        $predicate = new cassandra_SlicePredicate();
        $predicate->slice_range = $slice_range;

        //Query slice
        $resp = $this->storage->get_range_slice($this->cassandraCF->keyspace, $column_parent, $predicate, $start_key, $finish_key, $row_count, $this->cassandraCF->read_consistency_level);
        return $this->keySlicesToArray($resp);
    }

    /**
     * Insert a column
     * @param <string> $key
     * @param <string> $column_name
     * @param <object> $column_value
     * @param <object> $super_column
     * @return <object>
     */
    public function insert($key, $column_name, $column_value, $super_column=null)
    {
        //Get timestamp
        $timestamp = $this->getTime();

        //Add column family
        $columnPath = new cassandra_ColumnPath();
        $columnPath->column_family = $this->cassandraCF->column_family;
        $columnPath->super_column = $super_column;
        $columnPath->column = $column_name;

        //Query insert
        $resp = $this->storage->insert($this->cassandraCF->keyspace, $key, $columnPath, $column_value, $timestamp, $this->cassandraCF->write_consistency_level);
        return $resp;
    }

    /**
     * Insert multi data
     * @param <string> $key
     * @param <array> $columns
     * @return <object>
     */
    public function insertMulti($key, $columns)
    {
        //Get timestamp
        $timestamp = $this->getTime();

        //Add columns
        $cfmap = array();
        $cfmap[$this->column_family] = $this->arrayToSupercolumnsOrColumns($columns, $timestamp);

        //Query insert
        $resp = $this->storage->batch_insert($this->cassandraCF->keyspace, $key, $cfmap, $this->cassandraCF->write_consistency_level);
        return $resp;
    }

    /**
     * remove data
     * @param <string> $key
     * @param <string> $column_name
     * @return <object>
     */
    public function remove($key, $column_name=null)
    {
        //Get timestamp
        $timestamp = $this->getTime();

        //Add columns
        $column_path = new cassandra_ColumnPath();
        $column_path->column_family = $this->column_family;
        if($this->cassandraCF->is_super)
        {
            $column_path->super_column = $this->unparseColumnName($column_name, false);
        }
        else
        {
            $column_path->column = $this->unparseColumnName($column_name, true);
        }

        //Query remove
        $resp = $this->storage->remove($this->cassandraCF->keyspace, $key, $column_path, $timestamp, $this->cassandraCF->write_consistency_level);
        return $resp;
    }

    /**
     * Get list
     * @param <string> $key
     * @param <string> $key_name
     * @param <string> $slice_start
     * @param <string> $slice_finish
     * @return <array>
     */
    public function getList($key, $key_name='key', $slice_start="", $slice_finish="")
    {
        // Must be on supercols!
        $resp = $this->getSlice($key, NULL, $slice_start, $slice_finish);
        $ret = array();
        foreach($resp as $_key => $_value)
        {
            $_value[$key_name] = $_key;
            $ret[] = $_value;
        }
        return $ret;
    }

    /**
     * Get range list
     * @param <string> $key_name
     * @param <string> $start_key
     * @param <string> $finish_key
     * @param <int> $row_count
     * @param <string> $slice_start
     * @param <string> $slice_finish
     * @return <array>
     */
    public function getRangeList($key_name='key', $start_key="", $finish_key="", $row_count=self::DEFAULT_ROW_LIMIT, $slice_start="", $slice_finish="")
    {
        $resp = $this->getRangeSlice($start_key, $finish_key, $row_count, $slice_start, $slice_finish);
        $ret = array();
        foreach($resp as $_key => $_value)
        {
            if(!empty($_value))
            {
                //// filter nulls
                $_value[$key_name] = $_key;
                $ret[] = $_value;
            }
        }
        return $ret;
    }
}

