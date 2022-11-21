<?php
class Zend_View_Helper_NumberFormat  extends Zend_View_Helper_Abstract
{
    public function NumberFormat($intNumber)
    {
        if($intNumber <= 0){
            return 0;
        }
        $intNumber = number_format($intNumber, 0, ',', '.');
        return $intNumber;
    }
}

?>