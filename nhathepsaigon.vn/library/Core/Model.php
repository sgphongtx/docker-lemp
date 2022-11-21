<?php
/**
 * @todo define core model adapter
 * @param <string> $module
 * @param <array> $adapter
 * @return Core_Model
 * @author PhongTX
 */
class Core_Model
{
    protected function factory($module, $adapter)
    {
        $adapterName = 'Core_Business_' . ucfirst($module) . '_Adapter_' . ucfirst(strtolower($adapter));
        return new $adapterName();
    }
}
?>
