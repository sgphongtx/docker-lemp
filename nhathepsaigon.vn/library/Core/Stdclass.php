<?php

class Core_Stdclass
{

    public function __call($method, $args)
    {
        return false;
    }

}

?>
