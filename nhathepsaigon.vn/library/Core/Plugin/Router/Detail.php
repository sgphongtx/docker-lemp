<?php

/**
 * @todo router for Fashion news
 * @return Portal_Plugin_Router_Cate
 * @author LamTX
 */
class Core_Plugin_Router_Detail extends Zend_Controller_Router_Route_Regex
{

    /**
     * Matches a user submitted path with a previously defined route.
     * Assigns and returns an array of defaults on a successful match.
     * @param  string $path Path used to match against this routing map
     * @param boolean $partial
     * @return array|false  An array of assigned values or a false on a mismatch
     * @author PhongTX
     */
    public function match($path, $partial=false)
    {
        $return = parent::match($path, $partial);
        if ( $return )
        {
            if ( $return['action'] == 'tin-tuc' )
            {
                $return['action']   = 'index';
            }//end if
        }//end if
        return $return;
    }

}

?>