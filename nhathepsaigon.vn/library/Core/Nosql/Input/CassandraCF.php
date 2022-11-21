<?php
/**
 * @author      :   PhongTX
 * @name        :   Core_Nosql_Adapter_Cassandra
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to storage
 */
class Core_Nosql_Input_CassandraCF
{
    public $keyspace;
    public $column_family;
    public $is_super;
    public $read_consistency_level;
    public $write_consistency_level;
    public $column_type; // CompareWith
    public $subcolumn_type; // CompareSubcolumnsWith
    public $parse_columns;

    /*
    BytesType: Simple sort by byte value. No validation is performed.
    AsciiType: Like BytesType, but validates that the input can be parsed as US-ASCII.
    UTF8Type: A string encoded as UTF8
    LongType: A 64bit long
    LexicalUUIDType: A 128bit UUID, compared lexically (by byte value)
    TimeUUIDType: a 128bit version 1 UUID, compared by timestamp
    */
    public function __construct($keyspace, $column_family, $is_super, $column_type, $subcolumn_type, $read_consistency_level, $write_consistency_level)
    {
        // Vars cassandra
        $this->keyspace = $keyspace;
        $this->column_family = $column_family;
        $this->is_super = $is_super;
        $this->column_type = $column_type;
        $this->subcolumn_type = $subcolumn_type;
        $this->read_consistency_level = $read_consistency_level;
        $this->write_consistency_level = $write_consistency_level;

        // Toggles parsing columns
        $this->parse_columns = true;
    }
}

